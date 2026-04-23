<?php
class MSDL_Child_Sync {
    private $api;

    public function __construct() {
        $this->api = new MSDL_Child_Graph_API();
    }

    public function run_manual_sync() {
        set_time_limit( 300 );
        $token = $this->api->fetch_token_from_main();
        if ( is_wp_error( $token ) ) return $token;

        $folder_id = $this->api->root_folder_id;
        $drive_id = $this->api->drive_id;
        
        $delta_link_key = 'msdl_delta_link_' . md5( $drive_id . '_' . $folder_id );
        $delta_link = get_option( $delta_link_key );

        if ( empty( $delta_link ) ) {
            $endpoint = empty( $folder_id ) ? "/drives/{$drive_id}/root/delta" : "/drives/{$drive_id}/root:/{$folder_id}:/delta";
        } else {
            $endpoint = $delta_link;
        }

        $all_items = [];
        $has_more = true;
        
        while ( $has_more ) {
            $response = $this->api->make_request( $endpoint );
            if ( is_wp_error( $response ) ) {
                if ( $response->get_error_code() === 'delta_expired' || $response->get_error_code() === 'resyncRequired' ) {
                    delete_option( $delta_link_key );
                    return $this->run_manual_sync();
                }
                return $response;
            }

            if ( isset( $response['value'] ) ) {
                $all_items = array_merge( $all_items, $response['value'] );
            }

            if ( isset( $response['@odata.nextLink'] ) ) {
                $endpoint = $response['@odata.nextLink'];
            } elseif ( isset( $response['@odata.deltaLink'] ) ) {
                update_option( $delta_link_key, $response['@odata.deltaLink'] );
                $has_more = false;
            } else {
                $has_more = false;
            }
        }

        $processed = $this->process_items( $all_items );
        update_option( 'msdl_last_sync_timestamp', current_time( 'timestamp' ) );
        
        return [ 'success' => true, 'processed' => $processed ];
    }

    private function process_items( $items ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'msdl_nodes';
        $processed_count = 0;

        $parent_cache = [];

        foreach ( $items as $item ) {
            if ( ! isset( $item['id'] ) || ! isset( $item['name'] ) ) continue;
            
            $graph_id = sanitize_text_field( $item['id'] );
            
            if ( isset( $item['deleted'] ) ) {
                $wpdb->delete( $table_name, [ 'graph_id' => $graph_id ] );
                $processed_count++;
                continue;
            }

            $name = sanitize_text_field( $item['name'] );
            $type = isset( $item['folder'] ) ? 'folder' : 'file';
            $size = isset( $item['size'] ) ? intval( $item['size'] ) : 0;
            $parent_graph_id = isset( $item['parentReference']['id'] ) ? sanitize_text_field( $item['parentReference']['id'] ) : null;
            $last_modified = isset( $item['lastModifiedDateTime'] ) ? gmdate( 'Y-m-d H:i:s', strtotime( $item['lastModifiedDateTime'] ) ) : current_time( 'mysql' );
            $download_url = isset( $item['@microsoft.graph.downloadUrl'] ) ? esc_url_raw( $item['@microsoft.graph.downloadUrl'] ) : '';
            $web_url = isset( $item['webUrl'] ) ? esc_url_raw( $item['webUrl'] ) : '';

            $existing = $wpdb->get_row( $wpdb->prepare( "SELECT id FROM $table_name WHERE graph_id = %s", $graph_id ) );

            if ( $existing ) {
                $wpdb->update( 
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

                // Automatikusan levágja a kiterjesztést az új fájlok címénél
                $auto_title = ($type === 'file') ? pathinfo($name, PATHINFO_FILENAME) : $name;

                $wpdb->insert( 
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
            }
            $processed_count++;
        }

        return $processed_count;
    }
}