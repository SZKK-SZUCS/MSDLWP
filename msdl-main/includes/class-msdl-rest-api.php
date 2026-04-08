<?php
class MSDL_Main_REST_API {
    private $graph_api;

    public function __construct( $graph_api ) {
        $this->graph_api = $graph_api;
    }

    public function init() {
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }

    public function register_routes() {
        // Publikus végpont a Child pluginoknak (Belső API kulccsal védett)
        register_rest_route( 'msdl-main/v1', '/get-token', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [ $this, 'serve_token' ],
            'permission_callback' => [ $this, 'check_api_key' ]
        ]);

        // WP Admin végpontok a React UI-hoz (Admin bejelentkezéssel védett)
        register_rest_route( 'msdl-main/v1', '/sites', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [ $this, 'get_sites' ],
                'permission_callback' => [ $this, 'check_admin_permissions' ]
            ],
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [ $this, 'save_site' ],
                'permission_callback' => [ $this, 'check_admin_permissions' ]
            ]
        ]);

        register_rest_route( 'msdl-main/v1', '/sites/(?P<id>\d+)', [
            'methods'             => WP_REST_Server::DELETABLE,
            'callback'            => [ $this, 'delete_site' ],
            'permission_callback' => [ $this, 'check_admin_permissions' ]
        ]);

        register_rest_route( 'msdl-main/v1', '/search-sites', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [ $this, 'search_sites' ],
            'permission_callback' => [ $this, 'check_admin_permissions' ]
        ]);

        register_rest_route( 'msdl-main/v1', '/get-drives', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [ $this, 'get_drives' ],
            'permission_callback' => [ $this, 'check_admin_permissions' ]
        ]);

        register_rest_route( 'msdl-main/v1', '/search-folders', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [ $this, 'search_folders' ],
            'permission_callback' => [ $this, 'check_admin_permissions' ]
        ]);
    }

    public function check_api_key( WP_REST_Request $request ) {
        $provided = $request->get_header( 'X-MSDL-API-Key' );
        $stored = get_option( 'msdl_internal_api_key' );
        if ( empty($stored) || $provided !== $stored ) {
            return new WP_Error( 'forbidden', 'Érvénytelen belső API kulcs.', ['status' => 403] );
        }
        return true;
    }

    public function check_admin_permissions() {
        return current_user_can( 'manage_options' );
    }

    public function serve_token( WP_REST_Request $request ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'msdl_sites';

        // 1. Megnézzük, ki kopogtat
        $child_domain = $request->get_header( 'X-MSDL-Child-Domain' );
        if ( empty( $child_domain ) ) {
            return new WP_Error( 'missing_domain', 'Hiányzik a kliens domain azonosítója.', ['status' => 400] );
        }

        // Domain tisztítása biztos ami biztos
        $parsed = parse_url( $child_domain, PHP_URL_HOST );
        $clean_domain = $parsed ? $parsed : $child_domain;

        // 2. Kikeresjük a domaint a mi adatbázisunkból
        $site_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE domain = %s", $clean_domain ) );

        // 3. AUTO-DISCOVERY LOGIKA: Ha nincs még ilyen oldal, automatikusan rögzítjük
        if ( ! $site_data ) {
            $inserted = $wpdb->insert( $table_name, [
                'domain'          => sanitize_text_field( $clean_domain ),
                'folder_path'     => '', // Üres mappa = Függőben lévő jóváhagyás
                'custom_site_id'  => '',
                'custom_drive_id' => ''
            ]);

            // HA SIKERTELEN A MENTÉS, DOBJUK VISSZA A MYSQL HIBÁT
            if ( ! $inserted ) {
                return new WP_Error( 'db_insert_error', 'Központi DB Hiba: ' . $wpdb->last_error, ['status' => 500] );
            }

            return new WP_Error( 'pending_approval', 'A webhely (' . esc_html($clean_domain) . ') automatikusan regisztrálva lett a központban, de még nincs hozzárendelve gyökérmappa. Kérlek, állítsd be a Main admin felületén!', ['status' => 403] );
        }

        // 4. Ha az oldal be van regisztrálva, de még nem adtál neki mappát
        if ( empty( $site_data->folder_path ) ) {
            return new WP_Error( 'missing_folder', 'A webhely már szerepel a listában, de még nincs jóváhagyva/gyökérmappája beállítva a központban.', ['status' => 403] );
        }

        // 5. Lekérjük a tokent a Microsofttól
        $token = $this->graph_api->get_access_token();
        if ( is_wp_error( $token ) ) return $token;

        // 6. Összeállítjuk a választ
        $response_data = [
            'access_token'   => $token,
            'site_id'        => !empty($site_data->custom_site_id) ? $site_data->custom_site_id : get_option( 'msdl_site_id' ),
            'drive_id'       => !empty($site_data->custom_drive_id) ? $site_data->custom_drive_id : get_option( 'msdl_drive_id' ),
            'root_folder_id' => $site_data->folder_path
        ];

        return rest_ensure_response( $response_data );
    }

    public function get_sites() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'msdl_sites';
        $results = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY id DESC" );
        return rest_ensure_response( $results );
    }

    public function save_site( WP_REST_Request $request ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'msdl_sites';
        $params = $request->get_json_params();

        $domain = sanitize_text_field( $params['domain'] );
        $folder_path = sanitize_text_field( $params['folder_path'] );
        $custom_site_id = sanitize_text_field( $params['custom_site_id'] ?? '' );
        $custom_drive_id = sanitize_text_field( $params['custom_drive_id'] ?? '' );

        if ( empty( $domain ) || empty( $folder_path ) ) {
            return new WP_Error( 'missing_fields', 'A Domain és a Gyökér Mappa kötelező.', ['status' => 400] );
        }

        // Domain tisztítása (pl. https:// levágása)
        $parsed = parse_url( $domain, PHP_URL_HOST );
        $clean_domain = $parsed ? $parsed : $domain;

        if ( isset( $params['id'] ) && intval( $params['id'] ) > 0 ) {
            $wpdb->update( $table_name, [
                'domain' => $clean_domain,
                'folder_path' => $folder_path,
                'custom_site_id' => $custom_site_id,
                'custom_drive_id' => $custom_drive_id
            ], [ 'id' => intval( $params['id'] ) ] );
        } else {
            $wpdb->insert( $table_name, [
                'domain' => $clean_domain,
                'folder_path' => $folder_path,
                'custom_site_id' => $custom_site_id,
                'custom_drive_id' => $custom_drive_id
            ]);
        }

        return rest_ensure_response( ['status' => 'success'] );
    }

    public function delete_site( WP_REST_Request $request ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'msdl_sites';
        $id = intval( $request->get_param( 'id' ) );

        if ( $id > 0 ) {
            $wpdb->delete( $table_name, [ 'id' => $id ] );
        }
        
        return rest_ensure_response( ['status' => 'deleted'] );
    }

    public function search_sites( WP_REST_Request $request ) {
        $query = sanitize_text_field( $request->get_param( 'q' ) );
        if ( empty( $query ) ) return rest_ensure_response( [] );
        
        // Keresés a SharePoint webhelyek között
        $endpoint = "/sites?search=" . rawurlencode( $query ) . "&\$select=id,name,webUrl";
        $response = $this->graph_api->make_request( $endpoint );
        
        if ( is_wp_error( $response ) ) return $response;
        return rest_ensure_response( $response['value'] ?? [] );
    }

    public function get_drives( WP_REST_Request $request ) {
        $site_id = sanitize_text_field( $request->get_param( 'site_id' ) );
        if ( empty( $site_id ) ) return new WP_Error( 'missing_id', 'Site ID hiányzik', ['status'=>400] );
        
        // Adott Site-hoz tartozó Dokumentumtárak lekérdezése
        $endpoint = "/sites/{$site_id}/drives?\$select=id,name,webUrl";
        $response = $this->graph_api->make_request( $endpoint );
        
        if ( is_wp_error( $response ) ) return $response;
        return rest_ensure_response( $response['value'] ?? [] );
    }

    public function search_folders( WP_REST_Request $request ) {
        $query = sanitize_text_field( $request->get_param( 'q' ) );
        $custom_drive_id = sanitize_text_field( $request->get_param( 'drive_id' ) );
        
        // Ha jött egyedi Drive ID, azt használjuk, ha nem, a központit
        $drive_id = !empty($custom_drive_id) ? $custom_drive_id : get_option( 'msdl_drive_id' );
        
        if ( empty( $drive_id ) ) {
            return new WP_Error( 'missing_drive', 'Nincs beállítva a Drive ID. Előbb állítsd be a Központi Drive ID-t, vagy mentsd el a weblap egyedi azonosítóit!', ['status'=>400] );
        }
        
        // Ha nincs keresési szó, csak kilistázzuk a gyökeret. Ha van, akkor keresünk a teljes Drive-on.
        if ( empty( $query ) ) {
            $endpoint = "/drives/{$drive_id}/root/children?\$filter=folder ne null&\$select=id,name,parentReference,folder";
        } else {
            $endpoint = "/drives/{$drive_id}/root/search(q='" . rawurlencode($query) . "')?\$select=id,name,parentReference,folder";
        }
        
        $response = $this->graph_api->make_request( $endpoint );
        
        if ( is_wp_error( $response ) ) return $response;
        
        // Biztos ami biztos, csak a mappákat küldjük vissza a Reactnak
        $folders = [];
        if ( isset($response['value']) ) {
            foreach ( $response['value'] as $item ) {
                if ( isset($item['folder']) ) {
                    $folders[] = $item;
                }
            }
        }
        return rest_ensure_response( $folders );
    }
}