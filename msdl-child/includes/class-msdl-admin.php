<?php
class MSDL_Child_Admin {
    public function __construct() {
        add_action('admin_init', [$this, 'check_db_schema']);
    }

    // JAVÍTÁS: Automatikus adatbázis ellenőrzés (hogy a mentés ne haljon el)
    public function check_db_schema() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'msdl_nodes';
        if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name) {
            $cols = $wpdb->get_results("SHOW COLUMNS FROM $table_name");
            $col_names = array_map(function($c){return $c->Field;}, $cols);
            if(!in_array('auto_inherit', $col_names)) $wpdb->query("ALTER TABLE $table_name ADD auto_inherit TINYINT(1) DEFAULT 0");
            if(!in_array('version_rules', $col_names)) $wpdb->query("ALTER TABLE $table_name ADD version_rules TEXT");
        }
    }

    public function init() {
        add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        add_action( 'rest_api_init', [ $this, 'register_rest_endpoints' ] );
        add_action( 'wp_dashboard_setup', [ $this, 'add_dashboard_widget' ] );
        add_filter( 'cron_schedules', [ $this, 'add_cron_schedules' ] );
        add_action( 'msdl_scheduled_sync', [ $this, 'run_scheduled_sync' ] );
    }

    public function add_dashboard_widget() { wp_add_dashboard_widget( 'msdl_child_dashboard_widget', 'MSDL Dokumentumtár Áttekintés', [ $this, 'render_dashboard_widget' ] ); }

    public function render_dashboard_widget() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'msdl_nodes';
        
        $total_files = (int) $wpdb->get_var( "SELECT COUNT(id) FROM $table_name WHERE type = 'file'" );
        $unhandled_count = (int) $wpdb->get_var( "SELECT COUNT(id) FROM $table_name WHERE type = 'file' AND (visibility_roles = '' OR visibility_roles IS NULL)" );
        
        $latest_unhandled = [];
        if ( $unhandled_count > 0 ) {
            $latest_unhandled = $wpdb->get_results( "SELECT id, name, parent_graph_id FROM $table_name WHERE type = 'file' AND (visibility_roles = '' OR visibility_roles IS NULL) ORDER BY id DESC LIMIT 5" );
        }

        $last_ts = get_option( 'msdl_last_sync_timestamp' );
        $last_sync = $last_ts ? wp_date( 'Y.m.d. H:i', $last_ts ) : 'Sosem';
        $filemanager_url = admin_url('admin.php?page=msdl-child');

        echo '<style>
            .msdl-dash-wrapper { font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif; }
            .msdl-dash-card { display: flex; gap: 15px; margin-bottom: 15px; }
            .msdl-dash-stat { flex: 1; background: #fff; border: 1px solid #c3c4c7; border-radius: 4px; padding: 15px; text-align: center; box-shadow: 0 1px 1px rgba(0,0,0,0.04); }
            .msdl-dash-stat h4 { margin: 0 0 5px 0; color: #50575e; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
            .msdl-dash-stat .num { font-size: 28px; font-weight: 600; line-height: 1.2; }
            .msdl-dash-stat.alert { border-left: 4px solid #d63638; }
            .msdl-dash-stat.ok { border-left: 4px solid #00a32a; }
            .msdl-dash-stat.info { border-left: 4px solid #2271b1; }
            .msdl-unhandled-list { margin: 0 0 15px 0; padding: 0; list-style: none; border: 1px solid #c3c4c7; border-radius: 4px; background: #fff; box-shadow: 0 1px 1px rgba(0,0,0,0.04); }
            .msdl-unhandled-list li { border-bottom: 1px solid #f0f0f1; padding: 10px 12px; display: flex; justify-content: space-between; align-items: center; }
            .msdl-unhandled-list li:last-child { border-bottom: none; }
            .msdl-unhandled-list .file-name { font-weight: 500; font-size: 13px; color: #1d2327; display: flex; align-items: center; gap: 6px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
            .msdl-dash-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 12px; border-top: 1px solid #dcdcdc; }
            .msdl-spin { animation: msdl-rotation 2s infinite linear; }
            @keyframes msdl-rotation { from { transform: rotate(0deg); } to { transform: rotate(359deg); } }
        </style>';

        echo '<div class="msdl-dash-wrapper"><div class="msdl-dash-card">';
        echo '<div class="msdl-dash-stat info"><h4 title="A rendszerben lévő összes fájl">Összes Fájl</h4><div class="num" style="color:#2271b1;">' . $total_files . '</div></div>';
        if ( $unhandled_count > 0 ) echo '<div class="msdl-dash-stat alert"><h4 title="Azonnali intézkedést igénylő fájlok">Kezeletlen</h4><div class="num" style="color:#d63638;">' . $unhandled_count . '</div></div>';
        else echo '<div class="msdl-dash-stat ok"><h4>Státusz</h4><div class="num" style="color:#00a32a; font-size:20px; margin-top:8px;">Minden OK</div></div>';
        echo '</div>';

        if ( $unhandled_count > 0 ) {
            echo '<p style="margin: 0 0 8px 0; font-size: 13px; font-weight: 600; color: #1d2327;">Azonnali teendők (Legújabb fájlok):</p><ul class="msdl-unhandled-list">';
            foreach( $latest_unhandled as $file ) {
                $folder_param = !empty($file->parent_graph_id) ? '&folder=' . urlencode($file->parent_graph_id) : '';
                $deep_link = admin_url('admin.php?page=msdl-child' . $folder_param . '&open_file=' . $file->id);
                echo '<li><span class="file-name" title="' . esc_attr($file->name) . '"><span class="dashicons dashicons-media-document" style="color:#82878c; font-size:16px; width:16px; height:16px;"></span> ' . esc_html(mb_strimwidth($file->name, 0, 35, '...')) . '</span><a href="' . esc_url($deep_link) . '" class="button button-small" style="flex-shrink: 0;">Megnyitás</a></li>';
            }
            if ( $unhandled_count > 5 ) echo '<li style="background: #f6f7f7;"><span style="color:#666; font-size:12px;">És további ' . ($unhandled_count - 5) . ' fájl...</span> <a href="' . $filemanager_url . '" style="text-decoration:none; font-weight:600;">Összes megtekintése &rarr;</a></li>';
            echo '</ul>';
        }

        echo '<div class="msdl-dash-footer"><div style="font-size: 12px; color: #50575e;">Utolsó frissítés:<br><strong style="color:#1d2327;">' . $last_sync . '</strong></div><button type="button" id="msdl-quick-sync-btn" class="button button-secondary"><span class="dashicons dashicons-update" style="margin-top:3px;"></span> Szinkronizálás</button></div>';
        echo '<div id="msdl-quick-sync-msg" style="margin-top: 8px; font-weight: 600; font-size: 12px; text-align: right; display:none;"></div></div>';
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var btn = document.getElementById('msdl-quick-sync-btn'), msg = document.getElementById('msdl-quick-sync-msg');
            if (btn) {
                btn.addEventListener('click', function() {
                    btn.disabled = true; btn.innerHTML = '<span class="dashicons dashicons-update-alt msdl-spin" style="margin-top:3px;"></span> Folyamatban...'; msg.style.display = 'none';
                    fetch('<?php echo esc_url( rest_url('msdl-child/v1/sync-now') ); ?>', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>' } })
                    .then(r => r.json()).then(data => {
                        msg.style.display = 'block';
                        if (data.success) { msg.style.color = '#00a32a'; msg.innerText = 'Sikeres szinkronizáció! Oldal frissítése...'; setTimeout(() => location.reload(), 1500); } 
                        else { msg.style.color = '#d63638'; msg.innerText = 'Hiba: ' + (data.message || 'Ismeretlen hiba'); btn.disabled = false; btn.innerHTML = '<span class="dashicons dashicons-update" style="margin-top:3px;"></span> Újrapróbálás'; }
                    }).catch(error => {
                        msg.style.display = 'block'; msg.style.color = '#d63638'; msg.innerText = 'Hálózati hiba történt a szinkronizáció során.'; btn.disabled = false; btn.innerHTML = '<span class="dashicons dashicons-update" style="margin-top:3px;"></span> Újrapróbálás';
                    });
                });
            }
        });
        </script>
        <?php
    }

    public function add_cron_schedules( $schedules ) {
        $schedules['msdl_5min'] = [ 'interval' => 300, 'display' => '5 percenként' ]; $schedules['msdl_15min'] = [ 'interval' => 900, 'display' => '15 percenként' ]; $schedules['msdl_30min'] = [ 'interval' => 1800, 'display' => '30 percenként' ]; $schedules['msdl_thricedaily'] = [ 'interval' => 28800, 'display' => 'Naponta háromszor (8, 12, 16)' ];
        return $schedules;
    }

    public function run_scheduled_sync() { $sync = new MSDL_Child_Sync(); $sync->run_manual_sync(); }

    public function add_admin_menu() {
        global $wpdb;
        $unhandled = $wpdb->get_var( "SELECT COUNT(id) FROM {$wpdb->prefix}msdl_nodes WHERE type='file' AND (visibility_roles = '' OR visibility_roles IS NULL)" );
        $menu_title = 'Dokumentumtár';
        if ( $unhandled > 0 ) $menu_title .= ' <span class="update-plugins count-' . $unhandled . '"><span class="plugin-count">' . $unhandled . '</span></span>';
        add_menu_page( 'Dokumentumtár', $menu_title, 'manage_options', 'msdl-child', [ $this, 'display_filemanager_page' ], 'dashicons-media-document', 25 );
        add_submenu_page( 'msdl-child', 'Fájlkezelő', 'Fájlkezelő', 'manage_options', 'msdl-child', [ $this, 'display_filemanager_page' ] );
        add_submenu_page( 'msdl-child', 'Szinkronizáció', 'Szinkronizáció', 'manage_options', 'msdl-sync', [ $this, 'display_sync_page' ] );
        if ( in_array( 'administrator', (array) wp_get_current_user()->roles ) ) add_submenu_page( 'msdl-child', 'Beállítások', 'Beállítások', 'manage_options', 'msdl-settings', [ $this, 'display_settings_page' ] );
    }

    public function display_filemanager_page() { echo '<div id="msdl-admin-filemanager"></div>'; }
    public function display_sync_page() { echo '<div id="msdl-admin-sync"></div>'; }
    public function display_settings_page() { echo '<div id="msdl-admin-settings"></div>'; }

    public function enqueue_scripts( $hook ) {
        $page = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : '';
        if ( ! in_array( $page, [ 'msdl-child', 'msdl-sync', 'msdl-settings' ], true ) ) return;
        wp_enqueue_media(); wp_enqueue_editor();
        $asset_file = MSDL_CHILD_DIR . 'build/index.asset.php';
        if ( ! file_exists( $asset_file ) ) return;
        $asset = require $asset_file;
        wp_enqueue_script( 'msdl-child-js', MSDL_CHILD_URL . 'build/index.js', $asset['dependencies'], $asset['version'], true );
        wp_enqueue_style( 'wp-components' );
    }

    public function register_rest_endpoints() {
        $settings = [ 'msdl_main_server_url', 'msdl_internal_api_key', 'msdl_sync_mode', 'msdl_local_sync_interval', 'msdl_root_visibility', 'msdl_root_auto_inherit' ];
        foreach ( $settings as $setting ) register_setting( 'msdl_options', $setting, [ 'type' => 'string', 'show_in_rest' => true, 'default' => '' ] );

        register_rest_route( 'msdl-child/v1', '/test-connection', [ 'methods' => WP_REST_Server::READABLE, 'callback' => [ $this, 'test_connection' ], 'permission_callback' => [ $this, 'check_api_or_admin_auth' ] ]);
        register_rest_route( 'msdl-child/v1', '/sync-now', [ 'methods' => WP_REST_Server::CREATABLE, 'callback' => [ $this, 'trigger_sync' ], 'permission_callback' => [ $this, 'check_api_or_admin_auth' ] ]);
        register_rest_route( 'msdl-child/v1', '/get-nodes', [ 'methods' => WP_REST_Server::READABLE, 'callback' => [ $this, 'get_nodes' ], 'permission_callback' => function() { return current_user_can( 'read' ); } ]);
        register_rest_route( 'msdl-child/v1', '/get-roles', [ 'methods' => WP_REST_Server::READABLE, 'callback' => [ $this, 'get_wp_roles' ], 'permission_callback' => function() { return current_user_can( 'manage_options' ); } ]);
        register_rest_route( 'msdl-child/v1', '/update-visibility', [ 'methods' => WP_REST_Server::CREATABLE, 'callback' => [ $this, 'update_visibility' ], 'permission_callback' => function() { return current_user_can( 'manage_options' ); } ]);
        register_rest_route( 'msdl-child/v1', '/batch-update-visibility', [ 'methods' => WP_REST_Server::CREATABLE, 'callback' => [ $this, 'batch_update_visibility' ], 'permission_callback' => function() { return current_user_can( 'manage_options' ); } ]);
        register_rest_route( 'msdl-child/v1', '/update-cron', [ 'methods' => WP_REST_Server::CREATABLE, 'callback' => [ $this, 'handle_cron_update' ], 'permission_callback' => function() { return current_user_can( 'manage_options' ); } ]);
        register_rest_route( 'msdl-child/v1', '/reset-sync', [ 'methods' => WP_REST_Server::CREATABLE, 'callback' => [ $this, 'reset_sync' ], 'permission_callback' => [ $this, 'check_api_or_admin_auth' ] ]);
        register_rest_route( 'msdl-child/v1', '/sync-status', [ 'methods' => WP_REST_Server::READABLE, 'callback' => [ $this, 'get_sync_status' ], 'permission_callback' => function() { return current_user_can( 'manage_options' ); } ]);
        
        register_rest_route( 'msdl-child/v1', '/get-file-versions', [ 'methods' => WP_REST_Server::READABLE, 'callback' => [ $this, 'get_file_versions' ], 'permission_callback' => function() { return current_user_can( 'manage_options' ); } ]);
        register_rest_route( 'msdl-child/v1', '/public-file-versions', [ 'methods' => WP_REST_Server::READABLE, 'callback' => [ $this, 'get_public_file_versions' ], 'permission_callback' => '__return_true' ]);
        
        // ÚJ: A dinamikus letöltő végpont
        register_rest_route( 'msdl-child/v1', '/download-file', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [ $this, 'handle_file_download' ],
            'permission_callback' => '__return_true'
        ]);
    }

    private function calculate_active_version($rules) {
        if ( !$rules || !is_array($rules) ) return null;
        $active = isset($rules['active_version']) ? $rules['active_version'] : null;
        $now = current_time('timestamp');

        if ( isset($rules['schedules']) && is_array($rules['schedules']) ) {
            $valid_schedules = [];
            foreach ( $rules['schedules'] as $vid => $time ) {
                if (empty($time)) continue;
                $ts = strtotime($time);
                if ( $ts && $ts <= $now ) $valid_schedules[$ts] = $vid;
            }
            if ( !empty($valid_schedules) ) {
                ksort($valid_schedules);
                $active = end($valid_schedules);
            }
        }
        return $active;
    }

    public function handle_file_download( WP_REST_Request $request ) {
        $node_id = intval( $request->get_param( 'id' ) );
        global $wpdb;
        $table_name = $wpdb->prefix . 'msdl_nodes';
        $node = $wpdb->get_row( $wpdb->prepare("SELECT graph_id, visibility_roles, download_url, version_rules FROM $table_name WHERE id = %d AND type = 'file'", $node_id) );
        
        if ( !$node ) wp_die('Fájl nem található a rendszerben.');
        if ( class_exists('MSDL_Child_Elementor') && !MSDL_Child_Elementor::check_item_access($node->visibility_roles) ) wp_die('Nincs jogosultságod a fájl megtekintéséhez.');

        $rules = json_decode($node->version_rules, true);
        $active_vid = $this->calculate_active_version($rules);

        if ( !$active_vid ) {
            wp_redirect( $node->download_url );
            exit;
        }

        $api = new MSDL_Child_Graph_API();
        $token = $api->fetch_token_from_main();
        if ( is_wp_error( $token ) ) wp_die('Hiba a központi hitelesítés során.');

        // JAVÍTÁS: Mivel a Microsoft API egyedi lekérésnél elrejtheti a linket,
        // lekérjük a verziók listáját (ahol garantáltan szerepel), és abból olvassuk ki!
        $endpoint = "/drives/{$api->drive_id}/items/{$node->graph_id}/versions";
        $response = $api->make_request( $endpoint );
        
        if ( !is_wp_error($response) && isset($response['value']) && is_array($response['value']) ) {
            foreach ( $response['value'] as $v ) {
                if ( (string)$v['id'] === (string)$active_vid && !empty($v['@microsoft.graph.downloadUrl']) ) {
                    wp_redirect( $v['@microsoft.graph.downloadUrl'] );
                    exit;
                }
            }
        }
        
        // Biztonsági tartalék, ha a verzió lekérés nem sikerül (pl. törölték a verziót a szerveren)
        wp_redirect( $node->download_url );
        exit;
    }

    public function get_public_file_versions( WP_REST_Request $request ) {
        $node_id = intval( $request->get_param( 'id' ) );
        if ( $node_id <= 0 ) return new WP_Error( 'invalid_id', 'Érvénytelen azonosító.', ['status'=>400] );

        global $wpdb;
        $table_name = $wpdb->prefix . 'msdl_nodes';
        $node = $wpdb->get_row( $wpdb->prepare( "SELECT graph_id, visibility_roles, version_rules FROM $table_name WHERE id = %d AND type = 'file'", $node_id ) );
        
        if ( !$node ) return new WP_Error( 'not_found', 'Fájl nem található az adatbázisban.', ['status'=>404] );
        if ( class_exists('MSDL_Child_Elementor') && ! MSDL_Child_Elementor::check_item_access( $node->visibility_roles ) ) return new WP_Error( 'forbidden', 'Nincs jogosultságod ehhez a fájlhoz.', ['status' => 403] );

        $api = new MSDL_Child_Graph_API();
        $token = $api->fetch_token_from_main();
        if ( is_wp_error( $token ) ) return $token;

        $endpoint = "/drives/{$api->drive_id}/items/{$node->graph_id}/versions";
        $response = $api->make_request( $endpoint );
        if ( is_wp_error( $response ) ) return $response;
        
        $versions = isset($response['value']) ? $response['value'] : [];
        if (empty($versions)) return rest_ensure_response(['total' => 1, 'previous' => 0, 'last_modified' => '-']);
        
        $rules = json_decode($node->version_rules, true);
        $active_vid = $this->calculate_active_version($rules);

        $active_version = current($versions); // Default latest
        if ( $active_vid ) {
            foreach($versions as $v) { if ($v['id'] === $active_vid) { $active_version = $v; break; } }
        }

        return rest_ensure_response([
            'total' => $active_version['id'] ?? count($versions),
            'previous' => count($versions) > 1 ? count($versions) - 1 : 0,
            'last_modified' => isset($active_version['lastModifiedDateTime']) ? date('Y.m.d. H:i', strtotime($active_version['lastModifiedDateTime'])) : '-'
        ]);
    }

    public function get_file_versions( WP_REST_Request $request ) {
        $node_id = intval( $request->get_param( 'id' ) );
        global $wpdb;
        $table_name = $wpdb->prefix . 'msdl_nodes';
        $node = $wpdb->get_row( $wpdb->prepare( "SELECT graph_id FROM $table_name WHERE id = %d AND type = 'file'", $node_id ) );
        if ( !$node ) return new WP_Error( 'not_found', 'Fájl nem található az adatbázisban.' );

        $api = new MSDL_Child_Graph_API();
        $token = $api->fetch_token_from_main();
        if ( is_wp_error( $token ) ) return $token;

        $endpoint = "/drives/{$api->drive_id}/items/{$node->graph_id}/versions";
        $response = $api->make_request( $endpoint );
        if ( is_wp_error( $response ) ) return $response;
        return rest_ensure_response( isset($response['value']) ? $response['value'] : [] );
    }

    public function get_sync_status() {
        $last_ts = get_option( 'msdl_last_sync_timestamp' );
        $last_sync = $last_ts ? wp_date( 'Y.m.d. H:i:s', $last_ts ) : 'Még nem volt szinkronizálva';
        $mode = get_option( 'msdl_sync_mode', 'central' );
        $interval = get_option( 'msdl_local_sync_interval', 'hourly' );
        $intervals = [ 'msdl_5min' => '5 percenként', 'msdl_15min' => '15 percenként', 'msdl_30min' => '30 percenként', 'hourly' => 'Óránként', 'twicedaily' => 'Naponta kétszer', 'msdl_thricedaily' => 'Naponta háromszor (8, 12, 16)', 'daily' => 'Naponta egyszer' ];

        $next_sync = ''; $mode_display = '';

        if ( $mode === 'override' ) {
            $timestamp = wp_next_scheduled( 'msdl_scheduled_sync' );
            $next_sync = $timestamp ? wp_date( 'Y.m.d. H:i:s', $timestamp ) : 'Hiba: Nincs beütemezve!';
            $mode_display = 'Helyi felülbírálás';
            $interval_display = isset($intervals[$interval]) ? $intervals[$interval] : $interval;
        } elseif ( $mode === 'disabled' ) {
            $next_sync = 'Kikapcsolva'; $mode_display = 'Kikapcsolva'; $interval_display = '-';
        } else {
            $main_url = rtrim( get_option( 'msdl_main_server_url' ), '/' );
            $api_key = get_option( 'msdl_internal_api_key' );
            $next_sync = 'Központi ütemezés lekérése sikertelen.';
            $mode_display = 'Központi szerver (Kapcsolódási hiba)';
            $interval_display = '-';

            if ( !empty($main_url) && !empty($api_key) ) {
                $response = wp_remote_get( $main_url . '/wp-json/msdl-main/v1/get-next-sync', [ 'headers' => [ 'X-MSDL-API-Key' => $api_key ], 'timeout' => 5, 'sslverify' => false ]);
                if ( ! is_wp_error( $response ) ) {
                    $body = json_decode( wp_remote_retrieve_body( $response ), true );
                    if ( isset( $body['next_sync'] ) && $body['next_sync'] > 0 ) $next_sync = wp_date( 'Y.m.d. H:i:s', $body['next_sync'] ) . ' (Becsült)';
                    else $next_sync = 'Jelenleg nincs ütemezve';
                    if ( isset( $body['interval'] ) ) {
                        $main_interval = $body['interval'];
                        $interval_display = isset($intervals[$main_interval]) ? $intervals[$main_interval] : $main_interval;
                        $mode_display = 'Központi szerverről vezérelt';
                    }
                }
            }
        }
        return rest_ensure_response([ 'last_sync' => $last_sync, 'next_sync' => $next_sync, 'mode' => $mode_display, 'interval' => $interval_display ]);
    }

    public function reset_sync() {
        global $wpdb;
        $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'msdl_delta_link_%'" );
        return rest_ensure_response(['success' => true, 'message' => 'Gyorsítótár törölve!']);
    }

    public function handle_cron_update() {
        $mode = get_option( 'msdl_sync_mode', 'central' );
        $local_interval = get_option( 'msdl_local_sync_interval', 'hourly' );
        wp_clear_scheduled_hook( 'msdl_scheduled_sync' );
        if ( $mode === 'central' || $mode === 'disabled' ) return rest_ensure_response( ['success' => true, 'message' => 'Helyi időzítő kikapcsolva.'] );
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
        
        $query = empty( $parent_id ) 
            ? "SELECT * FROM $table_name WHERE parent_graph_id IS NULL OR parent_graph_id = '' ORDER BY type DESC, name ASC" 
            : $wpdb->prepare( "SELECT * FROM $table_name WHERE parent_graph_id = %s ORDER BY type DESC, name ASC", $parent_id );
            
        $nodes = $wpdb->get_results( $query );
        $unhandled_files = $wpdb->get_results( "SELECT parent_graph_id FROM $table_name WHERE type='file' AND (visibility_roles = '' OR visibility_roles IS NULL)" );
        $all_folders = $wpdb->get_results( "SELECT graph_id, parent_graph_id FROM $table_name WHERE type='folder'" );
        
        $folder_map = [];
        foreach($all_folders as $f) $folder_map[$f->graph_id] = $f->parent_graph_id;

        $unhandled_parents = [];
        foreach ( $unhandled_files as $file ) {
            $curr_parent = $file->parent_graph_id;
            while ( $curr_parent ) {
                $unhandled_parents[$curr_parent] = true;
                if ( isset($folder_map[$curr_parent]) ) $curr_parent = $folder_map[$curr_parent];
                else break;
            }
        }

        foreach ( $nodes as &$node ) {
            if ( $node->type === 'folder' && isset($unhandled_parents[$node->graph_id]) ) $node->has_unhandled = true;
            else $node->has_unhandled = false;
        }

        return rest_ensure_response( $nodes );
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
        $auto_inherit = isset( $params['auto_inherit'] ) ? (rest_sanitize_boolean( $params['auto_inherit'] ) ? 1 : 0) : 0;
        $custom_title = isset($params['custom_title']) ? sanitize_text_field($params['custom_title']) : '';
        $custom_description = isset($params['custom_description']) ? wp_kses_post($params['custom_description']) : '';
        $version_rules = isset($params['version_rules']) ? wp_json_encode($params['version_rules']) : null;

        $update_data = [ 
            'visibility_roles' => $roles, 
            'custom_title' => $custom_title, 
            'custom_description' => $custom_description,
            'auto_inherit' => $auto_inherit
        ];
        if ( $version_rules !== null ) {
            $update_data['version_rules'] = $version_rules;
        }

        $wpdb->update( $table_name, $update_data, [ 'id' => $node_id ] );

        if ( $apply_to_children ) {
            $node = $wpdb->get_row( $wpdb->prepare( "SELECT graph_id FROM $table_name WHERE id = %d", $node_id ) );
            if ( $node ) $this->update_descendants_visibility( $node->graph_id, $roles, $table_name );
        }
        return rest_ensure_response( ['success' => true] );
    }

    public function batch_update_visibility( WP_REST_Request $request ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'msdl_nodes';
        $params = $request->get_json_params();

        $ids = isset($params['ids']) && is_array($params['ids']) ? array_map('intval', $params['ids']) : [];
        $roles = sanitize_text_field( $params['roles'] );
        $apply_to_children = isset($params['apply_to_children']) ? rest_sanitize_boolean($params['apply_to_children']) : false;

        if ( empty($ids) ) return rest_ensure_response(['success' => false, 'message' => 'Nincs kiválasztott elem.']);

        $placeholders = implode(',', array_fill(0, count($ids), '%d'));
        $query = $wpdb->prepare( "UPDATE $table_name SET visibility_roles = %s WHERE id IN ($placeholders)", array_merge([$roles], $ids) );
        $wpdb->query( $query );

        if ( $apply_to_children ) {
            $nodes_query = $wpdb->prepare( "SELECT graph_id, type FROM $table_name WHERE id IN ($placeholders)", $ids );
            $nodes = $wpdb->get_results( $nodes_query );
            foreach ( $nodes as $node ) {
                if ( $node->type === 'folder' ) $this->update_descendants_visibility( $node->graph_id, $roles, $table_name );
            }
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