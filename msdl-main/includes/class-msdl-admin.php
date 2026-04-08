<?php
class MSDL_Main_Admin {
    public function init() {
        add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        add_action( 'rest_api_init', [ $this, 'register_settings' ] );
    }

    public function add_admin_menu() {
        add_menu_page( 'MSDL Központ', 'MSDL Központ', 'manage_options', 'msdl-main', [ $this, 'display_page' ], 'dashicons-shield', 20 );
    }

    public function display_page() {
        echo '<div id="msdl-main-app"></div>';
    }

    public function enqueue_scripts( $hook ) {
        if ( 'toplevel_page_msdl-main' !== $hook ) return;
        $asset_file = MSDL_MAIN_DIR . 'build/index.asset.php';
        if ( ! file_exists( $asset_file ) ) return;
        $asset = require $asset_file;
        wp_enqueue_script( 'msdl-main-js', MSDL_MAIN_URL . 'build/index.js', $asset['dependencies'], $asset['version'], true );
        wp_enqueue_style( 'wp-components' );
    }

    public function register_settings() {
        $settings = [ 'msdl_tenant_id', 'msdl_client_id', 'msdl_client_secret', 'msdl_site_id', 'msdl_drive_id', 'msdl_internal_api_key' ];
        foreach ( $settings as $setting ) {
            register_setting( 'msdl_main_options', $setting, [ 'type' => 'string', 'show_in_rest' => true, 'default' => '' ] );
        }
    }
}