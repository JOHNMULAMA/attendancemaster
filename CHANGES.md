# CHANGELOG - local_attendancemaster

## v1.0.0 - 2024-11-15

### Initial Release (Stable)

This is the first stable release of the Attendance Master plugin, offering a comprehensive solution for attendance tracking in Moodle.

**New Features:**

*   **Core Attendance Management:**
    *   Create, edit, and delete attendance sessions per course.
    *   Mark student attendance (Present, Absent, Late, Excused) for each session.
    *   View attendance records for students and sessions.
*   **Reporting:**
    *   Generate course-level attendance reports.
    *   Generate student-specific attendance reports.
*   **Automated Notifications:**
    *   Configurable global and course-level absence notification thresholds.
    *   Automatic email alerts to students when absence thresholds are met.
    *   Notification management UI for teachers/managers to view and acknowledge alerts.
*   **Mobile-Friendly Design:** Responsive UI for seamless experience across devices.
*   **Progressive Web App (PWA) Support:**
    *   Allows installation of the plugin as a PWA for offline attendance marking.
    *   Automatic data synchronization when internet connectivity is re-established.
*   **Security & Privacy:**
    *   Full adherence to Moodle's security best practices (sesskey, clean_param, require_capability).
    *   Integration with Moodle's Privacy API (GDPR/CCPA compliance) for data export and deletion.
*   **Moodle Integration:**
    *   Extends Moodle navigation for easy access from site administration and course pages.
    *   Comprehensive admin settings page for global configurations.
    *   Full backup and restore support for attendance data within Moodle courses.
*   **Developer Friendly:**
    *   Clear and well-documented codebase.
    *   Robust database schema (`db/install.xml`, `db/upgrade.php`).
    *   Thorough language strings (`lang/en/`).
    *   PHPDoc comments for all classes and methods.

**Improvements:**

*   Optimized database queries for performance.
*   Enhanced UI/UX for intuitive attendance management.
*   Improved error handling and user feedback messages.

**Bug Fixes:**

*   Initial release - no known bugs, but open to community feedback.