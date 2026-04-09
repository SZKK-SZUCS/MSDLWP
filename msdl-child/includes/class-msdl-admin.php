<?php
class MSDL_Child_Admin {
    public function init() {
        add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        add_action( 'rest_api_init', [ $this, 'register_rest_endpoints' ] );
        
        // Egyedi időzítések hozzáadása
        add_filter( 'cron_schedules', [ $this, 'add_cron_schedules' ] );
        
        // A tényleges cron esemény bekötése a szinkronizációhoz
        add_action( 'msdl_scheduled_sync', [ $this, 'run_scheduled_sync' ] );
    }

    public function add_cron_schedules( $schedules ) {
        $schedules['msdl_1min'] = [ 'interval' => 60, 'display' => '1 percenként (CSAK TESZTRE)' ];
        $schedules['msdl_15min'] = [ 'interval' => 900, 'display' => '15 percenként' ];
        $schedules['msdl_30min'] = [ 'interval' => 1800, 'display' => '30 percenként' ];
        return $schedules;
    }

    public function run_scheduled_sync() {
        $sync = new MSDL_Child_Sync();
        $sync->run_manual_sync(); // A Delta motorunkat használja
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
        // Alap beállítások
        $settings = [ 
            'msdl_main_server_url', 
            'msdl_internal_api_key',
            'msdl_sync_mode',
            'msdl_local_sync_interval'
        ];
        foreach ( $settings as $setting ) {
            register_setting( 'msdl_options', $setting, [ 'type' => 'string', 'show_in_rest' => true, 'default' => '' ] );
        }

        // Egyedi végpontok
        register_rest_route( 'msdl-child/v1', '/test-connection', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [ $this, 'test_connection' ],
            'permission_callback' => [ $this, 'check_api_or_admin_auth' ]
        ]);

        register_rest_route( 'msdl-child/v1', '/sync-now', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [ $this, 'trigger_sync' ],
            'permission_callback' => [ $this, 'check_api_or_admin_auth' ]
        ]);

        register_rest_route( 'msdl-child/v1', '/get-nodes', [ 'methods' => WP_REST_Server::READABLE, 'callback' => [ $this, 'get_nodes' ], 'permission_callback' => function() { return current_user_can( 'read' ); } ]);
        register_rest_route( 'msdl-child/v1', '/get-roles', [ 'methods' => WP_REST_Server::READABLE, 'callback' => [ $this, 'get_wp_roles' ], 'permission_callback' => function() { return current_user_can( 'manage_options' ); } ]);
        register_rest_route( 'msdl-child/v1', '/update-visibility', [ 'methods' => WP_REST_Server::CREATABLE, 'callback' => [ $this, 'update_visibility' ], 'permission_callback' => function() { return current_user_can( 'manage_options' ); } ]);

        // ÚJ: Cron ütemezés frissítése a beállítások mentése után
        register_rest_route( 'msdl-child/v1', '/update-cron', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [ $this, 'handle_cron_update' ],
            'permission_callback' => function() { return current_user_can( 'manage_options' ); }
        ]);

        register_rest_route( 'msdl-child/v1', '/reset-sync', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [ $this, 'reset_sync' ],
            'permission_callback' => [ $this, 'check_api_or_admin_auth' ]
        ]);
    }

    public function reset_sync() {
        global $wpdb;
        // Töröljük az összes lementett delta linket a gyorsítótárból
        $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'msdl_delta_link_%'" );
        return rest_ensure_response(['success' => true, 'message' => 'Gyorsítótár törölve!']);
    }

    public function handle_cron_update() {
    $mode = get_option( 'msdl_sync_mode', 'central' );
    $local_interval = get_option( 'msdl_local_sync_interval', 'hourly' );

    wp_clear_scheduled_hook( 'msdl_scheduled_sync' );

    // Ha központi, akkor a helyi Cront töröljük, mert a Main indítja a parancsot távolról
    if ( $mode === 'central' || $mode === 'disabled' ) {
        return rest_ensure_response( ['success' => true, 'message' => 'Helyi időzítő kikapcsolva, a rendszer a központi parancsra vár.'] );
    }

    // Csak 'override' módban ütemezünk helyi Cront
    wp_schedule_event( time(), $local_interval, 'msdl_scheduled_sync' );
    return rest_ensure_response( ['success' => true, 'message' => "Helyi felülbírálás aktív: {$local_interval}"] );
    }

    public function check_api_or_admin_auth( WP_REST_Request $request ) {
        if ( current_user_can( 'administrator' ) ) return true;
        $provided_key = $request->get_header( 'X-MSDL-API-Key' );
        $stored_key = get_option( 'msdl_internal_api_key' );
        if ( ! empty( $stored_key ) && $provided_key === $stored_key ) return true;
        return new WP_Error( 'forbidden', 'Nincs jogosultságod.', ['status' => 403] );
    }

    public function test_connection() {
        $api = new MSDL_Child_Graph_API();
        $result = $api->fetch_token_from_main();
        if ( is_wp_error( $result ) ) return rest_ensure_response(['success' => false, 'message' => $result->get_error_message()]);
        return rest_ensure_response(['success' => true, 'message' => 'Kapcsolat rendben!']);
    }

    public function trigger_sync() {
        $sync = new MSDL_Child_Sync();
        $result = $sync->run_manual_sync();
        if ( is_wp_error( $result ) ) return rest_ensure_response(['success' => false, 'message' => $result->get_error_message()]);
        return rest_ensure_response(['success' => true, 'message' => "Sikeres! Feldolgozva: {$result['processed']} db."]);
    }

    public function get_nodes( WP_REST_Request $request ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'msdl_nodes';
        $parent_id = sanitize_text_field( $request->get_param( 'parent_id' ) );
        $query = empty( $parent_id ) ? "SELECT * FROM $table_name WHERE parent_graph_id IS NULL OR parent_graph_id = '' ORDER BY type DESC, name ASC" : $wpdb->prepare( "SELECT * FROM $table_name WHERE parent_graph_id = %s ORDER BY type DESC, name ASC", $parent_id );
        return rest_ensure_response( $wpdb->get_results( $query ) );
    }

    public function get_wp_roles() {
        global $wp_roles;
        if ( ! isset( $wp_roles ) ) $wp_roles = new WP_Roles();
        return rest_ensure_response( $wp_roles->get_names() );
    }

    public function update_visibility( WP_REST_Request $request ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'msdl_nodes';
        $params = $request->get_json_params();
        $node_id = intval( $params['id'] );
        $roles = sanitize_text_field( $params['roles'] );
        $apply_to_children = isset( $params['apply_to_children'] ) ? rest_sanitize_boolean( $params['apply_to_children'] ) : false;
        $wpdb->update( $table_name, [ 'visibility_roles' => $roles ], [ 'id' => $node_id ] );
        if ( $apply_to_children ) {
            $node = $wpdb->get_row( $wpdb->prepare( "SELECT graph_id FROM $table_name WHERE id = %d", $node_id ) );
            if ( $node ) $this->update_descendants_visibility( $node->graph_id, $roles, $table_name );
        }
        return rest_ensure_response( ['success' => true] );
    }

    private function update_descendants_visibility( $parent_graph_id, $roles, $table_name ) {
        global $wpdb;
        $children = $wpdb->get_results( $wpdb->prepare( "SELECT id, graph_id, type FROM $table_name WHERE parent_graph_id = %s", $parent_graph_id ) );
        foreach ( $children as $child ) {
            $wpdb->update( $table_name, [ 'visibility_roles' => $roles ], [ 'id' => $child->id ] );
            if ( $child->type === 'folder' ) $this->update_descendants_visibility( $child->graph_id, $roles, $table_name );
        }
    }
}