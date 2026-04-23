<?php
class MSDL_Child_Sync {
    private $api;
    private $debug_log = []; // A logolást tároló tömb

    public function __construct() {
        $this->api = new MSDL_Child_Graph_API();
    }

    // Adatbázis sémák kényszerített ellenőrzése minden szinkron előtt
    private function force_db_schema() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'msdl_nodes';
        if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name) {
            $cols = $wpdb->get_results("SHOW COLUMNS FROM $table_name");
            $col_names = array_map(function($c){return $c->Field;}, $cols);
            
            if(!in_array('auto_inherit', $col_names)) $wpdb->query("ALTER TABLE $table_name ADD auto_inherit TINYINT(1) DEFAULT 0");
            if(!in_array('version_rules', $col_names)) $wpdb->query("ALTER TABLE $table_name ADD version_rules TEXT");
            if(!in_array('download_url', $col_names)) $wpdb->query("ALTER TABLE $table_name ADD download_url TEXT");
            if(!in_array('web_url', $col_names)) $wpdb->query("ALTER TABLE $table_name ADD web_url TEXT");
        }
        $this->debug_log[] = "[" . gmdate('Y-m-d H:i:s') . "] DB Séma ellenőrizve és frissítve.";
    }

    public function run_manual_sync( $retry_count = 0 ) {
        set_time_limit( 300 );
        
        if ( $retry_count === 0 ) {
            $this->debug_log = [];
            $this->debug_log[] = "=== SZINKRONIZÁCIÓ INDÍTÁSA ===";
            $this->force_db_schema();
        }

        $token = $this->api->fetch_token_from_main();
        if ( is_wp_error( $token ) ) {
            $this->debug_log[] = "HIBA: Token lekérési hiba a Maintől.";
            $this->save_log();
            return $token;
        }

        $folder_path = isset($this->api->root_folder_id) ? trim( $this->api->root_folder_id, '/' ) : '';
        $drive_id = $this->api->drive_id;
        $folder_id = 'root';

        $this->debug_log[] = "Drive ID: {$drive_id}, Kért Folder Path: " . ($folder_path ?: 'root');

        if ( !empty($folder_path) && $folder_path !== 'root' ) {
            $encoded_path = implode('/', array_map('rawurlencode', explode('/', $folder_path)));
            $info_endpoint = "https://graph.microsoft.com/v1.0/drives/{$drive_id}/root:/{$encoded_path}";
            
            $info_req = wp_remote_get( $info_endpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $token],
                'timeout' => 30
            ]);
            
            if ( is_wp_error( $info_req ) ) {
                $this->debug_log[] = "HIBA: A mappa ID lekérése hálózati hibára futott. (Próbálkozás: $retry_count)";
                if ( $retry_count < 2 ) { sleep(2); return $this->run_manual_sync( $retry_count + 1 ); }
                $this->save_log();
                return new WP_Error('graph_api_error', 'Nem található a megadott mappa a SharePoint-ban. Hálózat: ' . $info_req->get_error_message());
            }
            
            $info_code = wp_remote_retrieve_response_code($info_req);
            $info_body = json_decode(wp_remote_retrieve_body($info_req), true);

            if ( $info_code >= 400 || !isset($info_body['id']) ) {
                $this->debug_log[] = "HIBA: Nem található a mappa azonosító (Kód: $info_code)";
                if ( $retry_count < 2 ) { sleep(2); return $this->run_manual_sync( $retry_count + 1 ); }
                $this->save_log();
                return new WP_Error('graph_api_error', 'Hiba: Nem található a megadott mappa azonosítója. (Kód: ' . $info_code . ')');
            }
            
            $folder_id = $info_body['id'];
            $this->debug_log[] = "SIKER: Mappa ID sikeresen feloldva: {$folder_id}";
        }

        $delta_link_key = 'msdl_delta_link_' . md5( $drive_id . '_' . $folder_id );
        $delta_link = get_option( $delta_link_key );

        if ( empty( $delta_link ) ) {
            $endpoint = "https://graph.microsoft.com/v1.0/drives/{$drive_id}/items/{$folder_id}/delta";
            $this->debug_log[] = "Delta lekérdezés a NULLÁRÓL indul.";
        } else {
            $endpoint = $delta_link;
            $this->debug_log[] = "Delta lekérdezés GYORSÍTÓTÁRBÓL folytatódik.";
        }

        $all_items = [];
        $has_more = true;
        $page = 1;
        
        while ( $has_more ) {
            $request_url = strpos($endpoint, 'http') === 0 ? $endpoint : 'https://graph.microsoft.com/v1.0' . $endpoint;
            $request_url = str_replace('&amp;', '&', $request_url);
            
            $this->debug_log[] = "Lekérés indul (Oldal $page)...";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $request_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'Prefer: return=minimal',
                'Accept: application/json'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response_body = curl_exec($ch);
            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);

            if ( $curl_error ) {
                $this->debug_log[] = "cURL HIBA: $curl_error";
                delete_option( $delta_link_key );
                if ( $retry_count < 2 ) { sleep(2); return $this->run_manual_sync( $retry_count + 1 ); }
                $this->save_log();
                return new WP_Error('graph_api_error', 'Szerver kommunikációs hiba: ' . $curl_error);
            }

            $body = json_decode( $response_body, true );
            
            if ( $status_code >= 400 ) {
                $err_msg = isset($body['error']['message']) ? $body['error']['message'] : 'Ismeretlen API hiba';
                $err_code = isset($body['error']['code']) ? $body['error']['code'] : '';
                $this->debug_log[] = "API HIBA ($status_code): $err_code - $err_msg";
                
                delete_option( $delta_link_key );
                
                if ( $err_code === 'resyncRequired' || $err_code === 'invalidRequest' || $err_code === 'SyncStateNotFound' || $status_code === 400 ) {
                    if ( $retry_count < 2 ) {
                        $this->debug_log[] = "Automata újrapróbálkozás indul...";
                        sleep(2);
                        return $this->run_manual_sync( $retry_count + 1 );
                    }
                }
                $this->save_log();
                return new WP_Error('graph_api_error', $err_code . ': ' . $err_msg);
            }

            if ( isset( $body['value'] ) && is_array($body['value']) ) {
                $count = count($body['value']);
                $this->debug_log[] = "Oldal $page letöltve: $count db elem található.";
                $all_items = array_merge( $all_items, $body['value'] );
            }

            if ( isset( $body['@odata.nextLink'] ) ) {
                $endpoint = $body['@odata.nextLink']; 
                $page++;
            } elseif ( isset( $body['@odata.deltaLink'] ) ) {
                update_option( $delta_link_key, $body['@odata.deltaLink'] );
                $this->debug_log[] = "Lapozás vége. Delta token elmentve.";
                $has_more = false;
            } else {
                $has_more = false;
            }
        }

        $processed = $this->process_items( $all_items, $folder_id );
        update_option( 'msdl_last_sync_timestamp', current_time( 'timestamp' ) );
        
        $this->debug_log[] = "=== SZINKRONIZÁCIÓ BEFEJEZVE ===";
        $this->save_log();
        
        return [ 'success' => true, 'processed' => $processed ];
    }

    private function process_items( $items, $root_folder_id ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'msdl_nodes';
        $processed_count = 0;

        $parent_cache = [];
        $this->debug_log[] = "Adatbázis feldolgozás indul. Elemek száma: " . count($items);

        foreach ( $items as $item ) {
            if ( ! isset( $item['id'] ) || ! isset( $item['name'] ) ) continue;
            
            $graph_id = sanitize_text_field( $item['id'] );
            $parent_graph_id = isset( $item['parentReference']['id'] ) ? sanitize_text_field( $item['parentReference']['id'] ) : null;
            $type = isset( $item['folder'] ) ? 'folder' : 'file';
            $name = sanitize_text_field( $item['name'] );

            if ( $graph_id === $root_folder_id || (empty($parent_graph_id) && $root_folder_id === 'root' && $type === 'folder') ) {
                $this->debug_log[] = "[SKIP] Gyökérmappa kihagyva: $name";
                continue;
            }
            
            if ( isset( $item['deleted'] ) ) {
                $wpdb->delete( $table_name, [ 'graph_id' => $graph_id ] );
                $this->debug_log[] = "[DELETE] Elem törölve: $name";
                $processed_count++;
                continue;
            }

            $size = isset( $item['size'] ) ? intval( $item['size'] ) : 0;
            
            if ( $parent_graph_id === $root_folder_id || $parent_graph_id === $graph_id ) {
                $parent_graph_id = null;
            }

            $last_modified = isset( $item['lastModifiedDateTime'] ) ? gmdate( 'Y-m-d H:i:s', strtotime( $item['lastModifiedDateTime'] ) ) : current_time( 'mysql' );
            $download_url = isset( $item['@microsoft.graph.downloadUrl'] ) ? esc_url_raw( $item['@microsoft.graph.downloadUrl'] ) : '';
            $web_url = isset( $item['webUrl'] ) ? esc_url_raw( $item['webUrl'] ) : '';

            $existing = $wpdb->get_row( $wpdb->prepare( "SELECT id FROM $table_name WHERE graph_id = %s", $graph_id ) );

            if ( $existing ) {
                $res = $wpdb->update( 
                    $table_name, 
                    [
                        'name' => $name,
                        'size' => $size,
                        'parent_graph_id' => $parent_graph_id,
                        'last_modified' => $last_modified,
                        'download_url' => $download_url,
                        'web_url' => $web_url
                    ], 
                    [ 'id' => $existing->id ] 
                );
                
                if ($res === false) {
                    $this->debug_log[] = "[DB HIBA - UPDATE] $name: " . $wpdb->last_error;
                }
            } else {
                $auto_role = '';
                
                if ( !empty($parent_graph_id) ) {
                    if ( !isset($parent_cache[$parent_graph_id]) ) {
                        $parent_node = $wpdb->get_row( $wpdb->prepare( "SELECT visibility_roles, auto_inherit FROM $table_name WHERE graph_id = %s", $parent_graph_id ) );
                        if ( $parent_node ) {
                            $parent_cache[$parent_graph_id] = [
                                'inherit' => (int)$parent_node->auto_inherit === 1,
                                'role' => $parent_node->visibility_roles
                            ];
                        } else {
                            $parent_cache[$parent_graph_id] = ['inherit' => false, 'role' => ''];
                        }
                    }
                    if ( $parent_cache[$parent_graph_id]['inherit'] ) {
                        $auto_role = $parent_cache[$parent_graph_id]['role'];
                    }
                } else {
                    $root_inherit = get_option('msdl_root_auto_inherit', '0');
                    if ( $root_inherit === '1' ) {
                        $auto_role = get_option('msdl_root_visibility', 'public');
                    }
                }

                $auto_title = ($type === 'file') ? pathinfo($name, PATHINFO_FILENAME) : $name;

                $res = $wpdb->insert( 
                    $table_name, 
                    [
                        'graph_id' => $graph_id,
                        'parent_graph_id' => $parent_graph_id,
                        'name' => $name,
                        'type' => $type,
                        'size' => $size,
                        'visibility_roles' => $auto_role,
                        'last_modified' => $last_modified,
                        'download_url' => $download_url,
                        'web_url' => $web_url,
                        'custom_title' => $auto_title,
                        'custom_description' => '',
                        'auto_inherit' => 0
                    ] 
                );
                
                if ($res === false) {
                    $this->debug_log[] = "[DB HIBA - INSERT] $name: " . $wpdb->last_error;
                } else {
                    $this->debug_log[] = "[INSERT] Új elem: $name (Szülő: " . ($parent_graph_id ?: 'NULL (GYÖKÉR)') . ")";
                }
            }
            $processed_count++;
        }

        return $processed_count;
    }

    private function save_log() {
        $log_content = implode( "\n", $this->debug_log );
        file_put_contents( WP_CONTENT_DIR . '/msdl_sync_log.txt', $log_content );
    }
}