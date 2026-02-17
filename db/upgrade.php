<?php
/**
 * Upgrade code for local_attendancemaster plugin.
 *
 * @package    local_attendancemaster
 * @copyright  2024 John Mulama <johnmulama001@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     John Mulama - Senior Software Engineer (johnmulama001@gmail.com)
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_local_attendancemaster_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2024111500) {
        // Initial DB schema creation is handled by install.xml. This is a placeholder for future upgrades.

        // Example: Add a new column to a table
        // if (!$dbman->column_exists('local_attendancemaster_sessions', 'newcolumn')) {
        //     $table = new xmldb_table('local_attendancemaster_sessions');
        //     $field = new xmldb_field('newcolumn', XMLDB_TYPE_TEXT, null, XMLDB_NOTNULL, null, null, null, 'description');
        //     $dbman->add_field($table, $field);
        // }

        // Example: Change a column type
        // if ($dbman->column_exists('local_attendancemaster_sessions', 'oldcolumn')) {
        //     $table = new xmldb_table('local_attendancemaster_sessions');
        //     $field = new xmldb_field('oldcolumn', XMLDB_TYPE_CHAR, 255, XMLDB_NOTNULL, null, null, '', 'description');
        //     $dbman->change_field_type($table, $field);
        // }

        // Example: Add a new table
        // if (!$dbman->table_exists('local_attendancemaster_newtable')) {
        //     $table = new xmldb_table('local_attendancemaster_newtable');
        //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        //     $dbman->create_table($table);
        // }
    }

    return true;
}
