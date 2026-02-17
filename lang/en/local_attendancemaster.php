<?php
/**
 * Language strings for local_attendancemaster plugin.
 *
 * @package    local_attendancemaster
 * @copyright  2024 John Mulama <johnmulama001@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     John Mulama - Senior Software Engineer (johnmulama001@gmail.com)
 */

$string['pluginname'] = 'Attendance Master';
$string['attendancemaster:addinstance'] = 'Add a new Attendance Master block';
$string['attendancemaster:manage'] = 'Manage Attendance Master settings';
$string['attendancemaster:recordattendance'] = 'Record attendance';
$string['attendancemaster:viewreports'] = 'View attendance reports';
$string['attendancemaster:configurerules'] = 'Configure notification rules';
$string['attendancemaster:doanything'] = 'Do everything in Attendance Master';
$string['pluginname_desc'] = 'A mobile-friendly attendance tracking plugin with offline support. Teachers can take attendance, track student check-in/out, monitor history, and send automatic alerts.';
$string['settingsdescription'] = 'Configure global and course-level settings for Attendance Master.';
$string['modulename'] = 'Attendance Master';
$string['modulenameplural'] = 'Attendance Modules';

$string['recordattendance'] = 'Record Attendance';
$string['viewreports'] = 'View Reports';
$string['managesettings'] = 'Manage Settings';
$string['notificationrules'] = 'Notification Rules';
$string['studentsessions'] = 'Student Sessions';

$string['emailsubject'] = 'Attendance Alert: Absent from Session';
$string['emailbody'] = 'Dear {$a->studentname},

This is an automated notification to inform you that you were marked absent from the following session:

Course: {$a->coursename}
Session Date: {$a->sessiondate}

Please contact your teacher if you believe this is an error.

Thank you,
Attendance Master Team';

$string['developer'] = 'Developed by John Mulama - Senior Software Engineer';
$string['developer_email'] = 'johnmulama001@gmail.com';

$string['attendancedetails'] = 'Attendance Details';
$string['attendancereports'] = 'Attendance Reports';
$string['attendancesettings'] = 'Attendance Settings';
$string['coursereport'] = 'Course Attendance Report';
$string['studentreport'] = 'Student Attendance Report';
$string['overallstatus'] = 'Overall Status';
$string['lastupdate'] = 'Last Update';
$string['mobilefriendly'] = 'Mobile Friendly';
$string['offlinesupport'] = 'Offline Support (via PWA)';
$string['checkin'] = 'Check-in';
$string['checkout'] = 'Check-out';
$string['markpresent'] = 'Mark Present';
$string['markabsent'] = 'Mark Absent';
$string['marklate'] = 'Mark Late';
$string['markexcused'] = 'Mark Excused';
$string['selectstudent'] = 'Select Student';
$string['selectsession'] = 'Select Session';
$string['recordsaved'] = 'Attendance record saved successfully.';
$string['errorrecordsaving'] = 'Error saving attendance record.';
$string['sessionnotfound'] = 'Session not found.';
$string['studentnotfound'] = 'Student not found.';
$string['nopercentageset'] = 'No percentage set for threshold.';
$string['absencethreshold'] = 'Absence Notification Threshold (%)';
$string['absthresholddesc'] = 'Percentage of missed sessions to trigger an absence notification.';
$string['enablenotifications'] = 'Enable Absence Notifications';
$string['enablenotificationsdesc'] = 'Enable or disable automatic email notifications for student absences.';
$string['courselevelsettings'] = 'Course-level Settings';
$string['globalsettings'] = 'Global Attendance Master Settings';
$string['sessiondate'] = 'Session Date';
$string['sessionname'] = 'Session Name';
$string['creationtime'] = 'Creation Time';
$string['updatetime'] = 'Update Time';
$string['status'] = 'Status';
$string['notes'] = 'Notes';
$string['action'] = 'Action';
$string['exportcsv'] = 'Export to CSV';
$string['attendancerecorddeleted'] = 'Attendance record deleted.';
$string['confirmdelete'] = 'Are you sure you want to delete this attendance record?';
$string['sessionid'] = 'Session ID';
$string['userid'] = 'User ID';
$string['sessiontoken'] = 'Session Token';
$string['lastaccess'] = 'Last Access';
$string['ipaddress'] = 'IP Address';
$string['useragent'] = 'User Agent';
$string['notificationid'] = 'Notification ID';
$string['triggerdate'] = 'Trigger Date';
$string['isacknowledged'] = 'Is Acknowledged';
$string['acknowledge'] = 'Acknowledge';
$string['notificationacknowledged'] = 'Notification acknowledged.';
$string['erroracknowledging'] = 'Error acknowledging notification.';
$string['addsession'] = 'Add New Session';
$string['editsession'] = 'Edit Session';
$string['deletesession'] = 'Delete Session';
$string['sessionadded'] = 'Session added successfully.';
$string['sessionupdated'] = 'Session updated successfully.';
$string['sessiondeleted'] = 'Session deleted successfully.';
$string['confirmdeletesession'] = 'Are you sure you want to delete this session? This will also delete all associated attendance records.';
$string['sessiondetails'] = 'Session Details';
$string['attendancesuccess'] = 'Attendance marked successfully.';
$string['attendanceerror'] = 'Error marking attendance.';
$string['totalabsences'] = 'Total Absences';
$string['percentageabsent'] = 'Percentage Absent';
$string['totalpresent'] = 'Total Present';
$string['totalenrolled'] = 'Total Enrolled';
$string['attendancerecord'] = 'Attendance Record';
$string['attendanceoverview'] = 'Attendance Overview';
$string['courseattendance'] = 'Course Attendance';
$string['studentattendance'] = 'Student Attendance';
$string['attendancerules'] = 'Attendance Rules';
$string['notificationsent'] = 'Notification sent successfully.';
$string['errornotifsend'] = 'Error sending notification.';
$string['processtab'] = 'Process Attendance';
$string['reportstab'] = 'Reports';
$string['sessionstab'] = 'Manage Sessions';
$string['notificationstab'] = 'Notifications';
$string['teacherrole'] = 'Teacher';
$string['managerole'] = 'Manager';
$string['adminrole'] = 'Administrator';
$string['settingsaved'] = 'Settings saved.';
$string['errorsavingsettings'] = 'Error saving settings.';
$string['coursereports'] = 'Course Reports';
$string['globalreports'] = 'Global Reports';
$string['studentreports'] = 'Student Reports';
$string['sessionsmanagement'] = 'Manage Attendance Sessions';
$string['notificationmanagement'] = 'Manage Notifications';
$string['mobileapp'] = 'Mobile App Integration';
$string['integrationsettings'] = 'Integration Settings';
$string['pwaenabled'] = 'PWA Enabled';
$string['pwaenabled_desc'] = 'Enable Progressive Web App features for offline mobile use.';
$string['syncdata'] = 'Synchronize Data';
$string['syncdatadesc'] = 'Manually synchronize offline data with the server.';
$string['lastsynced'] = 'Last Synced';
$string['syncnow'] = 'Sync Now';
$string['synccomplete'] = 'Synchronization complete.';
$string['syncerror'] = 'Synchronization failed.';
$string['downloadapp'] = 'Download PWA';
$string['downloadapp_desc'] = 'Install the Attendance Master Progressive Web App to your device for offline use.';




