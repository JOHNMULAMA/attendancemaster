<?php
/**
 * Admin settings for local_attendancemaster plugin.
 *
 * @package    local_attendancemaster
 * @copyright  2024 John Mulama <johnmulama001@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     John Mulama - Senior Software Engineer (johnmulama001@gmail.com)
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    // Global settings header
    $settings->add(new admin_setting_heading(
        'local_attendancemaster_global_heading',
        get_string('globalsettings', 'local_attendancemaster'),
        get_string('settingsdescription', 'local_attendancemaster')
    ));

    // Enable / Disable global absence notifications
    $settings->add(new admin_setting_configcheckbox(
        'local_attendancemaster/enablenotifications',
        get_string('enablenotifications', 'local_attendancemaster'),
        get_string('enablenotificationsdesc', 'local_attendancemaster'),
        0, // Default value: disabled
        true // Forced type: boolean
    ));

    // Global absence threshold percentage
    $settings->add(new admin_setting_configtext(
        'local_attendancemaster/absencethreshold',
        get_string('absencethreshold', 'local_attendancemaster'),
        get_string('absthresholddesc', 'local_attendancemaster'),
        20, // Default: 20% absence threshold
        PARAM_INT,
        50 // Size of input box
    ));

    // PWA Enable Checkbox
    $settings->add(new admin_setting_configcheckbox(
        'local_attendancemaster/pwaenabled',
        get_string('pwaenabled', 'local_attendancemaster'),
        get_string('pwaenabled_desc', 'local_attendancemaster'),
        0, // Default value: disabled
        true
    ));

    // Course-level settings header - This will not directly expose settings in site admin, but hints that course-level settings exist.
    $settings->add(new admin_setting_heading(
        'local_attendancemaster_course_heading',
        get_string('courselevelsettings', 'local_attendancemaster'),
        get_string('courselevelsettings', 'local_attendancemaster') . ' ' . get_string('settingsdescription', 'local_attendancemaster')
    ));

    // This part is for site-wide defaults. Actual course-level settings should be managed via course modules or activity settings for advanced plugins.
    // As this is a local plugin without its own activity, course-level settings are often handled by: 
    // 1. Using course custom fields. 
    // 2. Creating a dedicated page within the local plugin for course admins (e.g., 'local/attendancemaster/course_settings.php').
    // For this example, we'll assume global settings apply, and any course-specific overrides would be handled programmatically or through a separate UI.
    // 
    // To truly have 'course-level settings' visible within each course's administration, you would typically integrate with the course settings page
    // or create specialized pages accessible from within the course context. For a `local` plugin, a dedicated page within the plugin itself 
    // (e.g., local/attendancemaster/view.php?courseid=X&action=settings) is a common approach.

}
