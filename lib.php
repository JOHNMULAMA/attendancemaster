<?php
/**
 * Core functions for local_attendancemaster plugin.
 *
 * @package    local_attendancemaster
 * @copyright  2024 John Mulama <johnmulama001@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     John Mulama - Senior Software Engineer (johnmulama001@gmail.com)
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/filelib.php');

/**
 * Extends the global navigation to include Attendance Master links.
 * @param global_navigation $navigation The global navigation object.
 * @param moodle_page $page The current page object.
 */
function local_attendancemaster_extend_navigation(global_navigation $navigation, moodle_page $page) {
    global $CFG, $OUTPUT, $USER, $DB;

    if (isloggedin() && !isguest()) {
        $managesettingsurl = new moodle_url('/admin/settings.php', ['section' => 'local_attendancemaster']);
        $course_context = context_course::instance(SITEID);

        // Add a top-level node for Attendance Master under Site Administration for managers.
        if (has_capability('local/attendancemaster:manageglobalsettings', context_system::instance())) {
             $node = $navigation->add(
                get_string('pluginname', 'local_attendancemaster'),
                new moodle_url('/local/attendancemaster/index.php'),
                navigation_node::TYPE_SETTING,
                null, // No key for now
                'attendancemaster_system_entry',
                new context_system()
            );
            $node->add(
                get_string('managesettings', 'local_attendancemaster'),
                $managesettingsurl,
                navigation_node::TYPE_SETTING,
                null, 'attendancemaster_settings_node'
            );
        }

        // Add course-specific links for teachers/managers within each course
        $courses = enrol_get_users_courses($USER->id, true);
        foreach ($courses as $course) {
            $context = context_course::instance($course->id);
            if ($course->id == SITEID) continue; // Skip site home

            // Check if user has capabilities in this course.
            if (has_capability('local/attendancemaster:recordattendance', $context) || 
                has_capability('local/attendancemaster:viewreports', $context) ||
                has_capability('local/attendancemaster:configurerules', $context) ||
                has_capability('local/attendancemaster:managesessions', $context)) {

                $course_node = $navigation->find($course->id, navigation_node::TYPE_COURSE, true);
                if ($course_node) {
                    $course_attendancemaster_node = $course_node->add(
                        get_string('pluginname', 'local_attendancemaster'),
                        new moodle_url('/local/attendancemaster/index.php', ['courseid' => $course->id]),
                        navigation_node::TYPE_COURSE_MODULE,
                        null, 'local_attendancemaster_course_menu_' . $course->id,
                        $context
                    );

                    if (has_capability('local/attendancemaster:recordattendance', $context)) {
                        $course_attendancemaster_node->add(
                            get_string('recordattendance', 'local_attendancemaster'),
                            new moodle_url('/local/attendancemaster/record.php', ['courseid' => $course->id]),
                            navigation_node::TYPE_COURSE_MODULE,
                            null, 'attendancemaster_record_' . $course->id
                        );
                    }
                    if (has_capability('local/attendancemaster:viewreports', $context)) {
                        $course_attendancemaster_node->add(
                            get_string('viewreports', 'local_attendancemaster'),
                            new moodle_url('/local/attendancemaster/report.php', ['courseid' => $course->id]),
                            navigation_node::TYPE_COURSE_MODULE,
                            null, 'attendancemaster_report_' . $course->id
                        );
                    }
                    if (has_capability('local/attendancemaster:managesessions', $context)) {
                        $course_attendancemaster_node->add(
                            get_string('sessionsmanagement', 'local_attendancemaster'),
                            new moodle_url('/local/attendancemaster/sessions.php', ['courseid' => $course->id]),
                            navigation_node::TYPE_COURSE_MODULE,
                            null, 'attendancemaster_sessions_' . $course->id
                        );
                    }
                    if (has_capability('local/attendancemaster:configurerules', $context)) {
                        $course_attendancemaster_node->add(
                            get_string('notificationrules', 'local_attendancemaster'),
                            new moodle_url('/local/attendancemaster/notifications.php', ['courseid' => $course->id]),
                            navigation_node::TYPE_COURSE_MODULE,
                            null, 'attendancemaster_notifications_' . $course->id
                        );
                    }
                }
            }
        }
    }
}

/**
 * Extends the settings navigation to include Attendance Master links for global settings.
 * @param settings_navigation $settingsnav The settings navigation object.
 * @param context_system $context The system context.
 */
function local_attendancemaster_extend_settings_navigation(settings_navigation $settingsnav, context_system $context) {
    global $CFG, $PAGE;

     if (has_capability('local/attendancemaster:manageglobalsettings', $context)) {
        $settingsnav->add(
            get_string('pluginname', 'local_attendancemaster'),
            new moodle_url('/admin/settings.php', array('section' => 'local_attendancemaster')),
            navigation_node::TYPE_SETTING,
            null, 'local_attendancemaster_settings',
            $context
        );
    }
}

/**
 * Helper function to send absence notifications.
 * @param int $courseid The course ID.
 * @param int $userid The user ID of the absent student.
 * @throws moodle_exception
 * @return bool True if notification sent, false otherwise.
 */
function local_attendancemaster_send_absence_notification($courseid, $userid) {
    global $DB, $CFG, $USER;

    $result = false;
    $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
    $student = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);

    // Check if notifications are enabled globally.
    $notificationsenabled = get_config('local_attendancemaster', 'enablenotifications');
    if (!$notificationsenabled) {
        // Notifications are disabled globally.
        return false;
    }

    // Retrieve absence threshold (course-specific first, then global).
    // For a local plugin, course-specific settings are a bit trickier without an activity module.
    // We'll use a simplified approach assuming a global setting for now, or course-specific overrides
    // could be stored in a custom course field or a dedicated table. For this example,
    // we'll assume a global setting.
    $absencethreshold = (int)get_config('local_attendancemaster', 'absencethreshold');
    if ($absencethreshold <= 0) {
        // Invalid or zero threshold, no notifications.
        return false;
    }

    // Calculate current absence percentage for the student in this course.
    $total_sessions_sql = "SELECT COUNT(id) FROM {local_attendancemaster_sessions} WHERE courseid = :courseid";
    $total_sessions = $DB->count_records_sql($total_sessions_sql, ['courseid' => $courseid]);

    $absent_sessions_sql = "SELECT COUNT(ar.id) FROM {local_attendancemaster_records} ar JOIN {local_attendancemaster_sessions} s ON ar.sessionid = s.id WHERE ar.userid = :userid AND s.courseid = :courseid AND ar.status = :status_absent";
    $absent_count = $DB->count_records_sql($absent_sessions_sql, ['userid' => $userid, 'courseid' => $courseid, 'status_absent' => 0]);

    if ($total_sessions == 0) {
        // No sessions, so no absences to calculate.
        return false;
    }

    $absence_percentage = ($absent_count / $total_sessions) * 100;

    if ($absence_percentage >= $absencethreshold) {
        // Check if a notification for this has been sent recently to avoid spam.
        // Or if it's already acknowledged.
        $notification_exists = $DB->record_exists_select(
            'local_attendancemaster_notifications',
            'courseid = :courseid AND userid = :userid AND triggerdate > :fivedaysago AND isacknowledged = 0',
            ['courseid' => $courseid, 'userid' => $userid, 'fivedaysago' => time() - (5 * DAY)]
        );

        if (!$notification_exists) {
            // Prepare notification data.
            $data = new icontextual_content_text(
                get_string('emailbody', 'local_attendancemaster', (
                    object)['studentname' => fullname($student), 'coursename' => $course->fullname, 'sessiondate' => userdate(time())])
            );

            // Attempt to send email.
            $email_subject = get_string('emailsubject', 'local_attendancemaster');
            $email_body = $data->export_for_template(get_string_manager());

            // Using Moodle's email API
            $sent = email_to_user($student,
                                  get_string('pluginname', 'local_attendancemaster'), // From user
                                  $email_subject,
                                  $email_body, // Plain text body
                                  $email_body); // HTML body (can be same or more complex)

            if ($sent) {
                // Log notification in database.
                $notificationrecord = new stdClass();
                $notificationrecord->courseid = $courseid;
                $notificationrecord->userid = $userid;
                $notificationrecord->triggerdate = time();
                $notificationrecord->reason = 'Absence Threshold Reached ('. round($absence_percentage, 2) . '%)';
                $notificationrecord->isacknowledged = 0;
                $notificationrecord->timesent = time();
                $notificationrecord->timemodified = time();
                $notificationrecord->timecreated = time();
                $DB->insert_record('local_attendancemaster_notifications', $notificationrecord);
                $result = true;
            }
        }
    }

    return $result;
}

/**
 * Returns an array of supported features for backup/restore.
 * @param string $feature The feature to check.
 * @return mixed True if supported, false otherwise, or null if not applicable.
 */
function local_attendancemaster_supports($feature) {
    switch ($feature) {
        case FEATURE_BACKUP_MOODLE2:
            return true; // We support Moodle 2 backup/restore
        case FEATURE_ADVANCED_GRADING:
        case FEATURE_COMPLETION_TRACKING:
        case FEATURE_GRADE_HAS_GRADE:
        case FEATURE_GRADE_OUTCOMES:
        case FEATURE_MOD_ARCHETYPE:
        case FEATURE_MOD_INTRO:
        case FEATURE_MOD_CM_ARRAY:
        case FEATURE_GROUPINGS:
        case FEATURE_GROUPS:
        case FEATURE_IP_CHECK:
        case FEATURE_NOSTEALTH:
        case FEATURE_OUTCOMES:
        case FEATURE_PLAGIARISM:
        case FEATURE_PREVENT_MOD_DELETE:
        case FEATURE_SHOW_DESCRIPTION:
            return true; // As a local plugin, most of these features are generally 'true' or handled by contexts.
        case FEATURE_GRADE_ITEMANDSOURCE:
        case FEATURE_GRADE_HAS_OUTCOMES:
        case FEATURE_GRADE_LETTERS:
        case FEATURE_FORCED_LANGUAGE:
        default:
            return null; // Not specifically applicable or handled by parent.
    }
}

/**
 * Gets attendance records for a given course and session.
 * @param int $sessionid The ID of the attendance session.
 * @return array An array of attendance records.
 */
function local_attendancemaster_get_session_records(int $sessionid) : array {
    global $DB;
    $records = $DB->get_records(
        'local_attendancemaster_records',
        ['sessionid' => $sessionid],
        'timeslot ASC'
    );
    if (!$records) {
        return [];
    }
    return $records;
}

/**
 * Get all attendance sessions for a specific course.
 * @param int $courseid The ID of the course.
 * @return array An array of session objects.
 */
function local_attendancemaster_get_course_sessions(int $courseid) : array {
    global $DB;
    $sessions = $DB->get_records(
        'local_attendancemaster_sessions',
        ['courseid' => $courseid],
        'sessiondate DESC, timemodified DESC'
    );
    if (!$sessions) {
        return [];
    }
    return $sessions;
}


/**
 * Deletes an attendance session and all related records.
 * @param int $sessionid The ID of the session to delete.
 * @return bool True on success, false on failure.
 */
function local_attendancemaster_delete_session(int $sessionid) : bool {
    global $DB;
    // Start a transaction to ensure atomicity.
    $transaction = $DB->start_delegated_transaction();
    try {
        // Delete all attendance records associated with the session.
        $DB->delete_records('local_attendancemaster_records', ['sessionid' => $sessionid]);

        // Delete the session itself.
        $DB->delete_records('local_attendancemaster_sessions', ['id' => $sessionid]);

        $transaction->allow_commit();
        return true;
    } catch (Exception $e) {
        $transaction->rollback($e);
        return false;
    }
}

/**
 * Marks attendance for a specific user in a session.
 * @param int $sessionid The session ID.
 * @param int $userid The user ID.
 * @param int $status The attendance status (0: Absent, 1: Present, 2: Late, 3: Excused).
 * @param string $notes Optional notes.
 * @return int|false The ID of the new/updated record on success, false on failure.
 */
function local_attendancemaster_mark_attendance(int $sessionid, int $userid, int $status, string $notes = '') {
    global $DB, $USER;

    $attendancerecord = $DB->get_record('local_attendancemaster_records', ['sessionid' => $sessionid, 'userid' => $userid]);

    $newrecord = new stdClass();
    $newrecord->sessionid = $sessionid;
    $newrecord->userid = $userid;
    $newrecord->status = $status;
    $newrecord->timeslot = time();
    $newrecord->notes = $notes;
    $newrecord->timemodified = time();

    if ($attendancerecord) {
        // Update existing record
        $newrecord->id = $attendancerecord->id;
        if ($DB->update_record('local_attendancemaster_records', $newrecord)) {
            return $newrecord->id;
        } else {
            return false;
        }
    } else {
        // Insert new record
        $newrecord->timecreated = time();
        $id = $DB->insert_record('local_attendancemaster_records', $newrecord, true);
        return $id;
    }
}

/**
 * Retrieves all students enrolled in a course.
 * @param int $courseid The course ID.
 * @return array Array of user objects.
 */
function local_attendancemaster_get_course_students(int $courseid) : array {
    global $DB;
    $context = context_course::instance($courseid);
    // Get all users with student role in this course.
    $students = get_enrolled_users($context, 'moodle/course:view', 'u.id, u.firstname, u.lastname, u.email');
    if (!$students) {
        return [];
    }
    return $students;
}

/**
 * Retrieves a specific attendance record.
 * @param int $recordid The ID of the attendance record.
 * @return object|false The attendance record object on success, false if not found.
 */
function local_attendancemaster_get_attendance_record(int $recordid) {
    global $DB;
    return $DB->get_record('local_attendancemaster_records', ['id' => $recordid]);
}

/**
 * Deletes a specific attendance record.
 * @param int $recordid The ID of the attendance record to delete.
 * @return bool True on success, false on failure.
 */
function local_attendancemaster_delete_attendance_record(int $recordid) : bool {
    global $DB;
    return $DB->delete_records('local_attendancemaster_records', ['id' => $recordid]);
}

/**
 * Acknowledges an absence notification.
 * @param int $notificationid The ID of the notification to acknowledge.
 * @return bool True on success, false on failure.
 */
function local_attendancemaster_acknowledge_notification(int $notificationid) : bool {
    global $DB;
    $notification = $DB->get_record('local_attendancemaster_notifications', ['id' => $notificationid]);
    if (!$notification) {
        return false;
    }
    $notification->isacknowledged = 1;
    $notification->timemodified = time();
    return $DB->update_record('local_attendancemaster_notifications', $notification);
}

/**
 * Retrieves all unacknowledged notifications for a course or user.
 * @param int $courseid Optional course ID.
 * @param int $userid Optional user ID.
 * @return array An array of notification objects.
 */
function local_attendancemaster_get_unacknowledged_notifications(int $courseid = 0, int $userid = 0) : array {
    global $DB;
    $conditions = ['isacknowledged' => 0];
    if ($courseid) {
        $conditions['courseid'] = $courseid;
    }
    if ($userid) {
        $conditions['userid'] = $userid;
    }
    return $DB->get_records('local_attendancemaster_notifications', $conditions, 'triggerdate DESC');
}

/**
 * Serves PWA manifest for offline capabilities.
 * @param moodle_page $page The current page object.
 */
function local_attendancemaster_serve_pwa_manifest(moodle_page $page) {
    global $CFG;

    // Only serve if PWA is enabled globally.
    if (!get_config('local_attendancemaster', 'pwaenabled')) {
        return;
    }

    // Set headers for manifest file.
    $page->set_url(new moodle_url('/local/attendancemaster/manifest.json'));
    $page->set_pagetype('local_attendancemaster_manifest');
    $page->set_context(context_system::instance());

    header('Content-Type: application/manifest+json');
    echo json_encode([
        "name" => get_string('pluginname', 'local_attendancemaster'),
        "short_name" => "Att. Master",
        "description" => get_string('pluginname_desc', 'local_attendancemaster'),
        "start_url" => (new moodle_url('/local/attendancemaster/index.php'))->out(false, ['offline' => 1]),
        "display" => "standalone",
        "background_color" => "#ffffff",
        "theme_color" => "#007bff",
        "icons" => [
            [
                "src" => (new moodle_url('/local/attendancemaster/pix/icon.svg'))->out(true),
                "sizes" => "any",
                "type" => "image/svg+xml"
            ],
            // Add more icon sizes if needed for broader compatibility
            // For example, 192x192, 512x512 PNGs.
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    die();
}

/**
 * Adds an attendance session to the database.
 * @param int $courseid The course ID.
 * @param string $sessionname The name of the session.
 * @param int $sessiondate The Unix timestamp of the session date.
 * @param string $description Optional description.
 * @return int The ID of the new session on success, false on failure.
 */
function local_attendancemaster_add_session(int $courseid, string $sessionname, int $sessiondate, string $description = '') {
    global $DB;

    $session = new stdClass();
    $session->courseid = $courseid;
    $session->sessionname = $sessionname;
    $session->sessiondate = $sessiondate;
    $session->description = $description;
    $session->timecreated = time();
    $session->timemodified = time();

    return $DB->insert_record('local_attendancemaster_sessions', $session, true);
}

/**
 * Updates an existing attendance session.
 * @param int $sessionid The ID of the session to update.
 * @param string $sessionname The new name of the session.
 * @param int $sessiondate The new Unix timestamp of the session date.
 * @param string $description New description.
 * @return bool True on success, false on failure.
 */
function local_attendancemaster_update_session(int $sessionid, string $sessionname, int $sessiondate, string $description = '') : bool {
    global $DB;

    $session = $DB->get_record('local_attendancemaster_sessions', ['id' => $sessionid], '*', MUST_EXIST);
    $session->sessionname = $sessionname;
    $session->sessiondate = $sessiondate;
    $session->description = $description;
    $session->timemodified = time();

    return $DB->update_record('local_attendancemaster_sessions', $session);
}

