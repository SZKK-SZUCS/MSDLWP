<?php
class MSDL_Main_REST_API {
    private $graph_api;

    public function __construct( $graph_api ) {
        $this->graph_api = $graph_api;
    }

    public function init() { add_action( 'rest_api_init', [ $this, 'register_routes' ] ); }

    public function register_routes() {
        register_rest_route( 'msdl-main/v1', '/get-token', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [ $this, 'serve_token' ],
            'permission_callback' => [ $this, 'check_api_key' ] ]);
        
        // WP Admin CRUD
        register_rest_route( 'msdl-main/v1', '/sites', [
            [ 'methods' => WP_REST_Server::READABLE,
            'callback' => [ $this, 'get_sites' ],
            'permission_callback' => [ $this, 'check_admin_permissions' ] ],
            [ 'methods' => WP_REST_Server::CREATABLE,
            'callback' => [ $this, 'save_site' ],
            'permission_callback' => [ $this, 'check_admin_permissions' ] ]
        ]);
        register_rest_route( 'msdl-main/v1', '/sites/(?P<id>\d+)', [
            'methods' => WP_REST_Server::DELETABLE,
            'callback' => [ $this, 'delete_site' ],
            'permission_callback' => [ $this, 'check_admin_permissions' ] ]);
        
        // Graph API Keresők
        register_rest_route( 'msdl-main/v1', '/search-sites', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [ $this, 'search_sites' ],
            'permission_callback' => [ $this, 'check_admin_permissions' ] ]);
        register_rest_route( 'msdl-main/v1', '/get-drives', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [ $this, 'get_drives' ],
            'permission_callback' => [ $this, 'check_admin_permissions' ] ]);
        register_rest_route( 'msdl-main/v1', '/search-folders', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [ $this, 'search_folders' ],
            'permission_callback' => [ $this, 'check_admin_permissions' ] ]);

        register_rest_route( 'msdl-main/v1', '/remote-command', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [ $this, 'remote_command' ],
            'permission_callback' => [ $this, 'check_admin_permissions' ]
        ]);

        register_rest_route( 'msdl-main/v1', '/get-sp-url', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [ $this, 'get_sp_url' ],
            'permission_callback' => [ $this, 'check_admin_permissions' ]
        ]);

        register_rest_route( 'msdl-main/v1', '/report-sync', [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [ $this, 'report_sync' ],
            'permission_callback' => [ $this, 'check_api_key' ]
        ]);

        register_rest_route( 'msdl-main/v1', '/get-next-sync', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [ $this, 'get_next_sync_time' ],
            'permission_callback' => [ $this, 'check_admin_permissions' ]
        ]);

        register_setting( 'msdl_options', 'msdl_global_sync_interval', [
            'type' => 'string',
            'show_in_rest' => true,
            'default' => 'hourly' ] );
    }

    public function check_api_key( WP_REST_Request $request ) {
        $provided = $request->get_header( 'X-MSDL-API-Key' );
        $stored = get_option( 'msdl_internal_api_key' );
        if ( empty($stored) || $provided !== $stored ) return new WP_Error( 'forbidden', 'Érvénytelen belső API kulcs.', ['status' => 403] );
        return true;
    }
    public function check_admin_permissions() { return current_user_can( 'manage_options' ); }

    public function serve_token( WP_REST_Request $request ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'msdl_sites';
        $child_domain = $request->get_header( 'X-MSDL-Child-Domain' );
        if ( empty( $child_domain ) ) return new WP_Error( 'missing_domain', 'Hiányzik a kliens domain azonosítója.', ['status' => 400] );

        $parsed = parse_url( $child_domain, PHP_URL_HOST );
        $clean_domain = $parsed ? $parsed : $child_domain;
        $site_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE domain = %s", $clean_domain ) );

        if ( ! $site_data ) {
            $inserted = $wpdb->insert( $table_name, [ 'domain' => sanitize_text_field( $clean_domain ), 'folder_path' => '', 'custom_site_id' => '', 'custom_drive_id' => '', 'is_active' => 1 ] );
            if ( ! $inserted ) return new WP_Error( 'db_insert_error', 'Központi DB Hiba: ' . $wpdb->last_error, ['status' => 500] );
            return new WP_Error( 'pending_approval', 'A webhely automatikusan regisztrálva lett, de még nincs mappa hozzárendelve.', ['status' => 403] );
        }

        $provided_mode = $request->get_header( 'X-MSDL-Sync-Mode' );
            if ( ! empty( $provided_mode ) && $site_data->sync_mode !== $provided_mode ) {
                $wpdb->update( $table_name, [ 'sync_mode' => sanitize_text_field($provided_mode) ],
                [ 'id' => $site_data->id ] );
            }

        // ÚJ: Felfüggesztés ellenőrzése
        if ( isset($site_data->is_active) && $site_data->is_active == 0 ) {
            return new WP_Error( 'site_suspended', 'A webhely kapcsolata karbantartás vagy tiltás miatt ideiglenesen fel van függesztve a központban.', ['status' => 403] );
        }
        if ( empty( $site_data->folder_path ) ) return new WP_Error( 'missing_folder', 'A webhely már szerepel a listában, de még nincs jóváhagyva/gyökérmappája beállítva.', ['status' => 403] );

        $token = $this->graph_api->get_access_token();
        if ( is_wp_error( $token ) ) return $token;

        return rest_ensure_response([
            'access_token'   => $token,
            'site_id'        => !empty($site_data->custom_site_id) ? $site_data->custom_site_id : get_option( 'msdl_site_id' ),
            'drive_id'       => !empty($site_data->custom_drive_id) ? $site_data->custom_drive_id : get_option( 'msdl_drive_id' ),
            'root_folder_id' => $site_data->folder_path
        ]);
    }

    public function get_sites() { global $wpdb; $table_name = $wpdb->prefix . 'msdl_sites'; return rest_ensure_response( $wpdb->get_results( "SELECT * FROM $table_name ORDER BY id DESC" ) ); }
    public function save_site( WP_REST_Request $request ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'msdl_sites';
        $params = $request->get_json_params();
        
        $data = [
            'domain' => sanitize_text_field( $params['domain'] ),
            'folder_path' => sanitize_text_field( $params['folder_path'] ),
            'custom_site_id' => sanitize_text_field( $params['custom_site_id'] ?? '' ),
            'custom_drive_id' => sanitize_text_field( $params['custom_drive_id'] ?? '' ),
            'is_active' => isset($params['is_active']) ? intval($params['is_active']) : 1
        ];

        if ( isset( $params['id'] ) && intval( $params['id'] ) > 0 ) $wpdb->update( $table_name, $data, [ 'id' => intval( $params['id'] ) ] );
        else $wpdb->insert( $table_name, $data );

        return rest_ensure_response( ['status' => 'success'] );
    }
    public function delete_site( WP_REST_Request $request ) { global $wpdb; $table_name = $wpdb->prefix . 'msdl_sites'; $wpdb->delete( $table_name, [ 'id' => intval( $request->get_param( 'id' ) ) ] ); return rest_ensure_response( ['status' => 'deleted'] ); }

    // --- Kereső Metódusok (Maradnak ahogy voltak) ---
    public function search_sites( WP_REST_Request $request ) { $query = sanitize_text_field( $request->get_param( 'q' ) ); if ( empty( $query ) ) return rest_ensure_response( [] ); $response = $this->graph_api->make_request( "/sites?search=" . rawurlencode( $query ) . "&\$select=id,name,webUrl" ); return is_wp_error( $response ) ? $response : rest_ensure_response( $response['value'] ?? [] ); }
    public function get_drives( WP_REST_Request $request ) { $site_id = sanitize_text_field( $request->get_param( 'site_id' ) ); if ( empty( $site_id ) ) return new WP_Error( 'missing_id', 'Site ID hiányzik', ['status'=>400] ); $response = $this->graph_api->make_request( "/sites/{$site_id}/drives?\$select=id,name,webUrl" ); return is_wp_error( $response ) ? $response : rest_ensure_response( $response['value'] ?? [] ); }
    public function search_folders( WP_REST_Request $request ) { $query = sanitize_text_field( $request->get_param( 'q' ) ); $drive_id = !empty($request->get_param('drive_id')) ? sanitize_text_field($request->get_param('drive_id')) : get_option('msdl_drive_id'); if (empty($drive_id)) return new WP_Error( 'missing_drive', 'Nincs Drive ID', ['status'=>400] ); $endpoint = empty($query) ? "/drives/{$drive_id}/root/children?\$filter=folder ne null&\$select=id,name,parentReference,folder" : "/drives/{$drive_id}/root/search(q='" . rawurlencode($query) . "')?\$select=id,name,parentReference,folder"; $response = $this->graph_api->make_request( $endpoint ); if ( is_wp_error( $response ) ) return $response; $folders = []; if ( isset($response['value']) ) foreach ( $response['value'] as $item ) if ( isset($item['folder']) ) $folders[] = $item; return rest_ensure_response( $folders ); }

    // Távoli Parancsok Végrehajtója
    public function remote_command( WP_REST_Request $request ) {
        $params = $request->get_json_params();
        $domain = $params['domain'] ?? '';
        $command = $params['command'] ?? ''; // 'ping' vagy 'sync'
        
        $internal_key = get_option('msdl_internal_api_key');
        if (empty($domain) || empty($command)) return new WP_Error('bad_request', 'Hiányzó paraméterek');

        $protocol = '';
        if (strpos($domain, 'http') !== 0) {
            $protocol = (strpos($domain, '.local') !== false || strpos($domain, '.test') !== false) ? 'http://' : 'https://';
        }
        
        $url = rtrim($protocol . $domain, '/');
        $endpoint = ($command === 'ping') ? '/wp-json/msdl-child/v1/test-connection' : '/wp-json/msdl-child/v1/sync-now';

        $args = [
            'headers'   => [ 'X-MSDL-API-Key' => $internal_key ],
            'timeout'   => 45,
            'sslverify' => false
        ];

        if ( $command === 'ping' ) {
            $response = wp_remote_get( $url . $endpoint, $args );
        } else {
            $response = wp_remote_post( $url . $endpoint, $args );
        }

        if ( is_wp_error( $response ) ) return new WP_Error('remote_error', 'Szerver hiba: ' . $response->get_error_message(), ['status' => 500]);
        
        $body = wp_remote_retrieve_body( $response );
        $decoded = json_decode( $body, true );
        
        if ( json_last_error() !== JSON_ERROR_NONE ) return new WP_Error('invalid_response', 'A kliens érvénytelen választ adott.', ['raw' => $body]);

        // ÚJ: Ha a szinkronizáció sikeres volt, frissítsük az utolsó szinkronizáció idejét a DB-ben!
        if ( $command === 'sync' && isset($decoded['success']) && $decoded['success'] === true ) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'msdl_sites';
            $clean_domain = preg_replace('#^https?://#', '', rtrim($domain, '/'));
            $wpdb->update( 
                $table_name, 
                [ 'last_sync' => wp_date('Y-m-d H:i:s') ], 
                [ 'domain' => $clean_domain ] 
            );
        }

        return rest_ensure_response( $decoded );
    }

    // ÚJ: Dinamikus URL generátor a Graph API-val
    public function get_sp_url( WP_REST_Request $request ) {
        $type = sanitize_text_field( $request->get_param( 'type' ) );
        $site_id = intval( $request->get_param( 'site_id' ) );

        // 1. Eset: Központi SharePoint
        if ( $type === 'central' ) {
            $drive_id = get_option( 'msdl_drive_id' );
            if ( empty( $drive_id ) ) return new WP_Error('no_drive', 'Nincs központi Dokumentumtár beállítva.', ['status'=>400]);
            
            $response = $this->graph_api->make_request( "/drives/{$drive_id}/root?\$select=webUrl" );
            if ( is_wp_error( $response ) ) return $response;
            
            return rest_ensure_response( ['url' => $response['webUrl']] );
        }

        // 2. Eset: Konkrét webhely mappája
        if ( $type === 'folder' && $site_id > 0 ) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'msdl_sites';
            $site = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $site_id ) );
            
            if ( ! $site ) return new WP_Error('not_found', 'Webhely nem található az adatbázisban.', ['status'=>404]);
            
            $drive_id = !empty($site->custom_drive_id) ? $site->custom_drive_id : get_option('msdl_drive_id');
            $folder_path = trim($site->folder_path, '/');
            
            if ( empty($drive_id) ) return new WP_Error('no_drive', 'Nincs Dokumentumtár beállítva ehhez az oldalhoz.', ['status'=>400]);
            
            // Ha a gyökeret kérik, vagy egy konkrét mappát
            $endpoint = empty($folder_path) 
                ? "/drives/{$drive_id}/root?\$select=webUrl" 
                : "/drives/{$drive_id}/root:/" . rawurlencode($folder_path) . "?\$select=webUrl";
            
            $response = $this->graph_api->make_request( $endpoint );
            if ( is_wp_error( $response ) ) return $response;
            
            return rest_ensure_response( ['url' => $response['webUrl']] );
        }

        return new WP_Error('invalid_request', 'Érvénytelen kérés.', ['status'=>400]);
    }

    public function get_next_sync_time() {
    $next = wp_next_scheduled( 'msdl_main_master_sync' );
    return rest_ensure_response( [ 'next_sync' => $next ? $next : 0 ] );
}
}