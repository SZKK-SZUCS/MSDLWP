<?php
class MSDL_Main_Activator {
    public static function activate() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'msdl_sites';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            domain varchar(255) NOT NULL,
            folder_path varchar(255) NOT NULL DEFAULT '',
            custom_site_id varchar(255) NOT NULL DEFAULT '',
            custom_drive_id varchar(255) NOT NULL DEFAULT '',
            PRIMARY KEY  (id),
            UNIQUE KEY domain (domain)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
}