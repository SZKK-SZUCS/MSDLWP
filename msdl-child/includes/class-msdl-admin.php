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
        $settings = [ 'msdl_main_server_url', 'msdl_internal_api_key' ];
        foreach ( $settings as $setting ) {
            register_setting( 'msdl_options', $setting, [ 'type' => 'string', 'show_in_rest' => true, 'default' => '' ] );
        }

        register_rest_route( 'msdl-child/v1', '/test-connection', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [ $this, 'test_connection' ],
            'permission_callback' => [ $this, 'check_api_or_admin_auth' ]
        ]);

        register_rest_route( 'msdl-child/v1', '/sync-now', [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [ $this, 'trigger_sync' ],
            'permission_callback' => [ $this, 'check_api_or_admin_auth' ]
        ]);

        register_rest_route( 'msdl-child/v1', '/get-nodes', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [ $this, 'get_nodes' ],
            'permission_callback' => function() { return current_user_can( 'read' ); }
        ]);

        register_rest_route( 'msdl-child/v1', '/get-roles', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [ $this, 'get_wp_roles' ],
            'permission_callback' => function() { return current_user_can( 'manage_options' ); }
        ]);

        register_rest_route( 'msdl-child/v1', '/update-visibility', [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [ $this, 'update_visibility' ],
            'permission_callback' => function() { return current_user_can( 'manage_options' ); }
        ]);
    }

    public function check_api_or_admin_auth( WP_REST_Request $request ) {
        if ( current_user_can( 'administrator' ) ) return true;
        
        $provided_key = $request->get_header( 'X-MSDL-API-Key' );
        $stored_key = get_option( 'msdl_internal_api_key' );
        
        if ( ! empty( $stored_key ) && $provided_key === $stored_key ) {
            return true; // A Main szerver kopogtatott a jó jelszóval!
        }
        
        return new WP_Error( 'forbidden', 'Nincs jogosultságod a végpont eléréséhez.', ['status' => 403] );
    }

    public function test_connection() {
        $api = new MSDL_Child_Graph_API();
        $result = $api->fetch_token_from_main();
        if ( is_wp_error( $result ) ) return rest_ensure_response(['success' => false, 'message' => $result->get_error_message()]);
        return rest_ensure_response(['success' => true, 'message' => 'Sikeresen kapcsolódva a központhoz és mappa azonosítva!']);
    }

    public function trigger_sync() {
        if ( ! class_exists('MSDL_Child_Sync') ) return rest_ensure_response(['success' => false, 'message' => 'Szinkronizációs osztály nem található!']);
        $sync = new MSDL_Child_Sync();
        $result = $sync->run_manual_sync();
        if ( is_wp_error( $result ) ) return rest_ensure_response(['success' => false, 'message' => $result->get_error_message()]);
        return rest_ensure_response(['success' => true, 'message' => "Szinkronizáció sikeres! Feldolgozott elemek a mappában: {$result['processed']} db."]);
    }

    public function get_nodes( WP_REST_Request $request ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'msdl_nodes';
        $parent_id = sanitize_text_field( $request->get_param( 'parent_id' ) );

        if ( empty( $parent_id ) ) {
            $query = "SELECT * FROM $table_name WHERE parent_graph_id IS NULL OR parent_graph_id = '' ORDER BY type DESC, name ASC";
        } else {
            $query = $wpdb->prepare( "SELECT * FROM $table_name WHERE parent_graph_id = %s ORDER BY type DESC, name ASC", $parent_id );
        }

        $results = $wpdb->get_results( $query );
        return rest_ensure_response( $results );
    }

    // --- JOGOSULTSÁGKEZELŐ METÓDUSOK ---

    public function get_wp_roles() {
        global $wp_roles;
        if ( ! isset( $wp_roles ) ) $wp_roles = new WP_Roles();
        // Visszaadunk egy asszociatív tömböt (pl. ['administrator' => 'Adminisztrátor', 'editor' => 'Szerkesztő'])
        return rest_ensure_response( $wp_roles->get_names() );
    }

    public function update_visibility( WP_REST_Request $request ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'msdl_nodes';
        $params = $request->get_json_params();

        $node_id = intval( $params['id'] );
        $roles = sanitize_text_field( $params['roles'] ); // pl. "public", "loggedin", vagy JSON
        $apply_to_children = isset( $params['apply_to_children'] ) ? rest_sanitize_boolean( $params['apply_to_children'] ) : false;

        if ( ! $node_id ) return new WP_Error( 'missing_id', 'Hiányzó azonosító', ['status'=>400] );

        // Fő elem frissítése
        $wpdb->update( $table_name, [ 'visibility_roles' => $roles ], [ 'id' => $node_id ] );

        // Rekurzív öröklődés (Case 1), ha ez egy mappa és bepipálták
        if ( $apply_to_children ) {
            $node = $wpdb->get_row( $wpdb->prepare( "SELECT graph_id FROM $table_name WHERE id = %d", $node_id ) );
            if ( $node ) {
                $this->update_descendants_visibility( $node->graph_id, $roles, $table_name );
            }
        }

        return rest_ensure_response( ['success' => true] );
    }

    private function update_descendants_visibility( $parent_graph_id, $roles, $table_name ) {
        global $wpdb;
        $children = $wpdb->get_results( $wpdb->prepare( "SELECT id, graph_id, type FROM $table_name WHERE parent_graph_id = %s", $parent_graph_id ) );
        
        foreach ( $children as $child ) {
            $wpdb->update( $table_name, [ 'visibility_roles' => $roles ], [ 'id' => $child->id ] );
            
            // Ha ez az almappa is tartalmaz további mappákat, megyünk tovább lefelé (Rekurzió)
            if ( $child->type === 'folder' ) {
                $this->update_descendants_visibility( $child->graph_id, $roles, $table_name );
            }
        }
    }
}