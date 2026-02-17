<?php
/**
 * Privacy provider for local_attendancemaster plugin.
 *
 * @package    local_attendancemaster
 * @copyright  2024 John Mulama <johnmulama001@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     John Mulama - Senior Software Engineer (johnmulama001@gmail.com)
 */

namespace local_attendancemaster\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\user_preference_provider;
use core_privacy\local\request\writer;
use context_course;
use stdClass;

/**
 * Privacy provider for Attendance Master plugin.
 *
 * Handles data requests and deletion for user data stored by the plugin.
 */
class provider implements 
    \core_privacy\local\request\plugin\provider, 
    \core_privacy\local\request\plugin\user_preference_provider
{

    /**
     * Returns the list of contexts that this plugin publishes data in.
     *
     * @param int $userid The ID of the user we are interested in.
     * @return \core_privacy\local\request\contextlist
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        global $DB;
        $contextlist = new contextlist();

        // Get all courses where the user has attendance records.
        $sql = "SELECT DISTINCT s.courseid
                FROM {local_attendancemaster_records} ar
                JOIN {local_attendancemaster_sessions} s ON s.id = ar.sessionid
                WHERE ar.userid = :userid";
        $courseids = $DB->get_fieldset_sql($sql, ['userid' => $userid]);

        foreach ($courseids as $courseid) {
            $contextlist->add(context_course::instance($courseid));
        }

        // Notifications are course specific but also linked to user.
        $sql = "SELECT DISTINCT courseid FROM {local_attendancemaster_notifications} WHERE userid = :userid";
        $notification_courseids = $DB->get_fieldset_sql($sql, ['userid' => $userid]);

        foreach ($notification_courseids as $courseid) {
            $contextlist->add(context_course::instance($courseid));
        }

        return $contextlist;
    }

    /**
     * Export all user data for the specified user and context.
     *
     * @param writer $writer The writer to write the data to.
     * @param int $userid The user to export data for.
     * @param stdClass $context The context we are exporting. It will be the same as one of the
     * returned by get_contexts_for_userid().
     */
    public static function export_userdata(writer $writer, int $userid, stdClass $context) {
        global $DB;

        if ($context->contextlevel === CONTEXT_COURSE) {
            $courseid = $context->instanceid;

            // Export attendance records for the user in this course.
            $records = $DB->get_records_sql(
                "SELECT ar.*, s.sessionname, s.sessiondate 
                 FROM {local_attendancemaster_records} ar 
                 JOIN {local_attendancemaster_sessions} s ON ar.sessionid = s.id 
                 WHERE ar.userid = :userid AND s.courseid = :courseid",
                ['userid' => $userid, 'courseid' => $courseid]
            );
            if (!empty($records)) {
                $writer->export_data(['local_attendancemaster', 'attendance_records'], $records);
            }

            // Export notifications for the user in this course.
            $notifications = $DB->get_records(
                'local_attendancemaster_notifications',
                ['userid' => $userid, 'courseid' => $courseid]
            );
            if (!empty($notifications)) {
                $writer->export_data(['local_attendancemaster', 'notifications'], $notifications);
            }
        }
    }

    /**
     * Delete all user data for the specified user and context.
     *
     * @param stdClass $context The context we are deleting. It will be the same as one of the
     * returned by get_contexts_for_userid().
     * @param int $userid The user to delete data for.
     */
    public static function delete_userdata(stdClass $context, int $userid) {
        global $DB;

        if ($context->contextlevel === CONTEXT_COURSE) {
            $courseid = $context->instanceid;

            // Delete attendance records for the user in this course.
            $sql_records = "DELETE ar FROM {local_attendancemaster_records} ar
                            JOIN {local_attendancemaster_sessions} s ON s.id = ar.sessionid
                            WHERE ar.userid = :userid AND s.courseid = :courseid";
            $DB->execute($sql_records, ['userid' => $userid, 'courseid' => $courseid]);

            // Delete notifications for the user in this course.
            $DB->delete_records('local_attendancemaster_notifications', ['userid' => $userid, 'courseid' => $courseid]);
        }
    }

    /**
     * This is the static function that provides the list of identification purposes provided by the plugin
     * which do not need any agreement from the user, they are by default approved for processing.
     *
     * @return array
     */
    public static function get_default_user_metadata_purpose_data(): array {
        // This plugin doesn't store any data that needs special identification purpose without agreement.
        return [];
    }

    /**
     * Get a list of users who have approved to have their data processed
     * by the system or a plugin.
     *
     * @param int $contextid The context to check in.
     * @return approved_userlist
     */
    public static function get_approved_users_in_context(int $contextid): approved_userlist {
        global $DB;

        $approvedusers = new approved_userlist();
        $context = context::instance_by_id($contextid, MUST_EXIST);

        if ($context->contextlevel === CONTEXT_COURSE) {
            $courseid = $context->instanceid;
            // All users who have an attendance record in this course are implicitly 'approved' to have 
            // their attendance data processed because they are enrolled and records are taken.
            // This implies data is collected as part of normal course participation.
            $users = $DB->get_fieldset_sql(
                "SELECT DISTINCT ar.userid 
                 FROM {local_attendancemaster_records} ar 
                 JOIN {local_attendancemaster_sessions} s ON ar.sessionid = s.id 
                 WHERE s.courseid = :courseid",
                ['courseid' => $courseid]
            );
            foreach ($users as $userid) {
                $approvedusers->add_user($userid, ['local_attendancemaster' => get_string('attendancerecord', 'local_attendancemaster')]);
            }

            // All users who have received a notification are also part of this.
             $users = $DB->get_fieldset_sql(
                "SELECT DISTINCT userid 
                 FROM {local_attendancemaster_notifications} 
                 WHERE courseid = :courseid",
                ['courseid' => $courseid]
            );
            foreach ($users as $userid) {
                $approvedusers->add_user($userid, ['local_attendancemaster' => get_string('notificationid', 'local_attendancemaster')]);
            }
        }

        return $approvedusers;
    }

    /**
     * Does the plugin keep any data that is not associated with any context?
     *
     * @return bool
     */
    public static function has_other_data(): bool {
        // Our plugin's data (sessions, records, notifications) are always linked to a course and/or user,
        // so they are associated with a context.
        return false;
    }

    /**
     * Delete all data that has no associated context.
     *
     * @throws 
ull_context_exception If context has to be created but it cannot be.
     */
    public static function delete_other_data() {
        // No uncontextualized data to delete.
    }

    /**
     * Export data that has no associated context.
     *
     * @param writer $writer The writer to write the data to.
     * @param int $userid The user to export data for.
     */
    public static function export_other_data(writer $writer, int $userid) {
        // No uncontextualized data to export.
    }
}
