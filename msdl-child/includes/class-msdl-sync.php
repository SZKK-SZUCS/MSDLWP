<?php
class MSDL_Child_Sync {
    private $graph_api;

    public function __construct() {
        $this->graph_api = new MSDL_Child_Graph_API();
    }

    public function run_manual_sync() {
        $token_data = $this->graph_api->fetch_token_from_main();
        if ( is_wp_error( $token_data ) ) return $token_data;

        $drive_id = $this->graph_api->drive_id;
        $folder_path = trim( $this->graph_api->root_folder_id, '/' );

        if ( empty( $drive_id ) ) {
            return new WP_Error( 'missing_drive', 'Nincs Drive ID beállítva a központban ehhez a webhelyhez.' );
        }

        $folder_item_id = $this->get_item_id_by_path( $drive_id, $folder_path );
        if ( is_wp_error( $folder_item_id ) ) return $folder_item_id;

        $processed_count = 0;
        $delta_link = get_option( 'msdl_delta_link_' . $folder_item_id );
        
        if ( empty( $delta_link ) ) {
            // ELSŐ SZINKRONIZÁCIÓ: Bekérjük a listItem kiegészítést a Cím és Leírás adatokhoz!
            $endpoint = "/drives/{$drive_id}/items/{$folder_item_id}/delta?\$expand=listItem";
        } else {
            // A delta link alapból tartalmazni fogja az expand paramétert, ha az eredeti kérésben benne volt.
            $endpoint = str_replace( 'https://graph.microsoft.com/v1.0', '', $delta_link );
        }

        $has_more = true;
        
        while ( $has_more ) {
            $response = $this->graph_api->make_request( $endpoint );
            if ( is_wp_error( $response ) ) {
                delete_option( 'msdl_delta_link_' . $folder_item_id );
                return new WP_Error( 'sync_error', 'A szinkronizáció megszakadt. A token lejárt, vagy módosult a struktúra. Kérlek indítsd újra.' );
            }

            $items = isset( $response['value'] ) ? $response['value'] : [];
            $processed_count += $this->process_delta_items( $items, $folder_item_id );

            if ( isset( $response['@odata.nextLink'] ) ) {
                $endpoint = str_replace( 'https://graph.microsoft.com/v1.0', '', $response['@odata.nextLink'] );
            } else {
                $has_more = false;
                if ( isset( $response['@odata.deltaLink'] ) ) {
                    update_option( 'msdl_delta_link_' . $folder_item_id, $response['@odata.deltaLink'] );
                }
            }
        }

        return [ 'success' => true, 'processed' => $processed_count ];
    }

    private function get_item_id_by_path( $drive_id, $path ) {
        if ( empty( $path ) ) return 'root'; 
        
        $endpoint = "/drives/{$drive_id}/root:/" . rawurlencode( $path ) . "?\$select=id";
        $response = $this->graph_api->make_request( $endpoint );
        
        if ( is_wp_error( $response ) ) return new WP_Error( 'path_not_found', 'A megadott gyökérmappa nem található a SharePointban.' );
        return $response['id'];
    }

    private function process_delta_items( $items, $root_folder_id ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'msdl_nodes';
        $count = 0;

        foreach ( $items as $item ) {
            if ( $item['id'] === $root_folder_id ) continue;

            if ( isset( $item['deleted'] ) ) {
                $wpdb->delete( $table_name, [ 'graph_id' => $item['id'] ] );
                $count++;
                continue;
            }

            $is_folder = isset( $item['folder'] );
            $type = $is_folder ? 'folder' : 'file';
            $mime_type = $is_folder ? null : ( $item['file']['mimeType'] ?? '' );
            
            $parent_graph_id = isset($item['parentReference']) ? $item['parentReference']['id'] : null;
            if ( $parent_graph_id === $root_folder_id ) {
                $parent_graph_id = null;
            }

            // EXTRA ADATOK: Cím és Leírás kinyerése a listItem-ből!
            $custom_title = '';
            $custom_description = '';
            
            if ( isset( $item['listItem'] ) && isset( $item['listItem']['fields'] ) ) {
                $fields = $item['listItem']['fields'];
                // A SharePoint mezőnevek egyediek is lehetnek, de általában 'Title' és 'Description' néven futnak.
                $custom_title = isset( $fields['Title'] ) ? sanitize_text_field( $fields['Title'] ) : '';
                $custom_description = isset( $fields['Description'] ) ? wp_kses_post( $fields['Description'] ) : '';
            }

            $existing = $wpdb->get_row( $wpdb->prepare( "SELECT id FROM $table_name WHERE graph_id = %s", $item['id'] ) );

            $data = [
                'graph_id'           => $item['id'],
                'parent_graph_id'    => $parent_graph_id,
                'type'               => $type,
                'name'               => sanitize_text_field( $item['name'] ),
                'mime_type'          => sanitize_text_field( $mime_type ),
                'size'               => intval( $item['size'] ?? 0 ),
                'last_modified'      => wp_date( 'Y-m-d H:i:s', strtotime( $item['lastModifiedDateTime'] ) ),
                'custom_title'       => $custom_title,
                'custom_description' => $custom_description,
            ];

            if ( $existing ) {
                $wpdb->update( $table_name, $data, [ 'id' => $existing->id ] );
            } else {
                $wpdb->insert( $table_name, $data );
            }
            $count++;
        }
        return $count;
    }
}