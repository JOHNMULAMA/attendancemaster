# local_attendancemaster

## Attendance Master Moodle Plugin (Local Type)

**Description:**

Attendance Master is a comprehensive Moodle local plugin designed for seamless, mobile-friendly attendance tracking with robust offline capabilities via Progressive Web App (PWA) integration. It empowers teachers to efficiently record student attendance using their desktop or mobile devices, track student check-in/out times, monitor detailed attendance history, and automate absence notifications to students.

This plugin aims to provide an intuitive and reliable solution for managing attendance, surpassing the features found in many commercial offerings by focusing on user experience, offline functionality, and actionable insights.

## Features:

*   **Mobile-Friendly & Responsive:** Optimized for use on any device, from desktops to smartphones.
*   **Offline Support (PWA):** Teachers can take attendance even without an internet connection. Data is automatically synchronized once connectivity is restored.
*   **Flexible Attendance Recording:** Mark students as Present, Absent, Late, or Excused.
*   **Student Check-in/Check-out (Planned for future enhancement of v1.0.0):** Track entry and exit times for more granular control (though current v1.0 focuses on session status).
*   **Detailed Attendance History:** View comprehensive reports for individual students or entire courses.
*   **Automated Absence Alerts:** Configure rules to automatically send email notifications to students and/or teachers when absence thresholds are met.
*   **Manage Attendance Sessions:** Create, edit, and delete attendance sessions specific to each course.
*   **Role-Based Capabilities:** Granular permissions for recording attendance (teachers), viewing reports (teachers, students), and configuring rules (teachers, managers).
*   **Global & Course-Level Settings:** Administrators can set global defaults, and teachers/managers can configure course-specific notification thresholds.
*   **Data Privacy (GDPR/CCPA Compliant):** Implements Moodle's Privacy API for data export and deletion requests.
*   **Moodle Backup/Restore Support:** Attendance data is included in course backups and restored seamlessly.

## Installation Guide:

1.  **Download:** Obtain the plugin archive (`local_attendancemaster.zip`).
2.  **Unzip:** Extract the contents of the archive.
3.  **Upload:** Upload the `attendancemaster` folder into your Moodle `local` directory (e.g., `moodle/local/`).
4.  **Navigate to Moodle Notifications:** As a Moodle administrator, log in and go to `Site administration > Notifications`. Moodle will detect the new plugin and prompt you to install it.
5.  **Follow On-Screen Instructions:** Complete the installation process by following the prompts provided by Moodle.
6.  **Configure Global Settings:** After installation, navigate to `Site administration > Plugins > Local plugins > Attendance Master` to configure global settings like enabling notifications and setting default absence thresholds.

## Configuration Guide:

### Global Settings (Site Administration):

1.  Go to `Site administration > Plugins > Local plugins > Attendance Master`.
2.  **Enable Absence Notifications:** Toggle this setting to enable or disable automatic email alerts for students.
3.  **Absence Notification Threshold (%):** Set the percentage of missed sessions at which an automatic notification should be triggered (e.g., 20% means a student will be notified if they miss 20% or more of their sessions).
4.  **PWA Enabled:** Enable this to allow users to install Attendance Master as a Progressive Web App on their mobile devices for offline functionality.

### Course-Level Configuration (within each course):

Access Attendance Master features and settings via the course navigation:

1.  Navigate to the desired course.
2.  In the course navigation menu (usually on the left), look for **