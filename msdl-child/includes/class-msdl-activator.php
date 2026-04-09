<?php
/**
 * Fired during plugin activation.
 */

class MSDL_Activator {

	public static function activate() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'msdl_nodes';
		$charset_collate = $wpdb->get_charset_collate();

		// JAVÍTOTT SÉMA: Hozzáadva a custom_title és custom_description, valamint a 'file_size' javítva 'size'-ra.
		$sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            graph_id varchar(255) NOT NULL,
            name varchar(255) NOT NULL,
            type varchar(50) NOT NULL,
            parent_graph_id varchar(255) DEFAULT NULL,
            visibility_roles text DEFAULT '',
            size bigint(20) DEFAULT 0,
            last_modified datetime DEFAULT NULL,
            mime_type varchar(255) DEFAULT '',
            custom_title varchar(255) DEFAULT '',
            custom_description text DEFAULT '',
            PRIMARY KEY  (id),
            UNIQUE KEY graph_id (graph_id)
        ) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}
}