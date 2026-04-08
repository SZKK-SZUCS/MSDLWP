<?php
class MSDL_Child_Admin {
    public function init() {
        add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        add_action( 'rest_api_init', [ $this, 'register_rest_endpoints' ] );
    }

    public function add_admin_menu() {
        add_menu_page( 'Dokumentumtár', 'Dokumentumtár', 'manage_options', 'msdl-child', [ $this, 'display_filemanager_page' ], 'dashicons-media-document', 25 );
        add_submenu_page( 'msdl-child', 'Fájlkezelő', 'Fájlkezelő', 'manage_options', 'msdl-child', [ $this, 'display_filemanager_page' ] );
        add_submenu_page( 'msdl-child', 'Szinkronizáció', 'Szinkronizáció', 'manage_options', 'msdl-sync', [ $this, 'display_sync_page' ] );

        $current_user = wp_get_current_user();
        if ( in_array( 'administrator', (array) $current_user->roles ) ) {
            add_submenu_page( 'msdl-child', 'Beállítások', 'Beállítások', 'manage_options', 'msdl-settings', [ $this, 'display_settings_page' ] );
        }
    }

    public function display_filemanager_page() { echo '<div id="msdl-admin-filemanager"></div>'; }
    public function display_sync_page() { echo '<div id="msdl-admin-sync"></div>'; }
    public function display_settings_page() { echo '<div id="msdl-admin-settings"></div>'; }

    public function enqueue_scripts( $hook ) {
        $page = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : '';
        if ( ! in_array( $page, [ 'msdl-child', 'msdl-sync', 'msdl-settings' ], true ) ) return;

        $asset_file = MSDL_CHILD_DIR . 'build/index.asset.php';
        if ( ! file_exists( $asset_file ) ) return;
        
        $asset = require $asset_file;
        wp_enqueue_script( 'msdl-child-js', MSDL_CHILD_URL . 'build/index.js', $asset['dependencies'], $asset['version'], true );
        wp_enqueue_style( 'wp-components' );
    }

    public function register_rest_endpoints() {
        // 1. Natív beállítások regisztrálása
        $settings = [ 'msdl_main_server_url', 'msdl_internal_api_key' ];
        foreach ( $settings as $setting ) {
            register_setting( 'msdl_options', $setting, [
                'type' => 'string', 'show_in_rest' => true, 'default' => ''
            ] );
        }

        // 2. Egyedi végpont a kapcsolat azonnali teszteléséhez
        register_rest_route( 'msdl-child/v1', '/test-connection', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [ $this, 'test_connection' ],
            'permission_callback' => function() { return current_user_can( 'administrator' ); }
        ]);
    }

    public function test_connection() {
        // Meghívjuk a mi Graph API osztályunkat, ami elindul a Main felé
        $api = new MSDL_Child_Graph_API();
        $result = $api->fetch_token_from_main();

        if ( is_wp_error( $result ) ) {
            return rest_ensure_response([
                'success' => false,
                'message' => $result->get_error_message()
            ]);
        }

        return rest_ensure_response([
            'success' => true,
            'message' => 'Sikeresen kapcsolódva a központhoz és mappa azonosítva!'
        ]);
    }
}