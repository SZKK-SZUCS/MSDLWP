<?php
class MSDL_Child_Sync {
    private $graph_api;

    public function __construct() {
        $this->graph_api = new MSDL_Child_Graph_API();
    }

    public function run_manual_sync() {
        // 1. Kapcsolódás a Main szerverhez (Token és ID-k lekérése)
        $token_data = $this->graph_api->fetch_token_from_main();
        if ( is_wp_error( $token_data ) ) return $token_data;

        $drive_id = $this->graph_api->drive_id;
        $folder_path = trim( $this->graph_api->root_folder_id, '/' );

        if ( empty( $drive_id ) ) {
            return new WP_Error( 'missing_drive', 'Nincs Drive ID beállítva a központban ehhez a webhelyhez.' );
        }

        // 2. Gyökérmappa azonosítójának (Item ID) lekérése az útvonal alapján
        $folder_item_id = $this->get_item_id_by_path( $drive_id, $folder_path );
        if ( is_wp_error( $folder_item_id ) ) return $folder_item_id;

        // 3. Delta szinkronizáció indítása
        $processed_count = 0;
        
        // Ellenőrizzük, hogy van-e elmentett Delta Linkünk az előző szinkronizációból
        $delta_link = get_option( 'msdl_delta_link_' . $folder_item_id );
        
        if ( empty( $delta_link ) ) {
            // Első szinkronizáció
            $endpoint = "/drives/{$drive_id}/items/{$folder_item_id}/delta";
        } else {
            // Csak a változások lekérése (A delta link már tartalmazza a teljes URL-t a Microsoft felé)
            $endpoint = str_replace( 'https://graph.microsoft.com/v1.0', '', $delta_link );
        }

        // Lapozás a Microsoft eredményeiben (ha sok fájl van, több oldalt küld vissza)
        $has_more = true;
        
        while ( $has_more ) {
            $response = $this->graph_api->make_request( $endpoint );
            if ( is_wp_error( $response ) ) {
                // Ha a delta link lejárt (pl. túl régen szinkronizált), töröljük és kezdjük elölről
                delete_option( 'msdl_delta_link_' . $folder_item_id );
                return new WP_Error( 'sync_error', 'A szinkronizáció megszakadt. A token lejárt, vagy módosult a struktúra. Kérlek indítsd újra.' );
            }

            $items = isset( $response['value'] ) ? $response['value'] : [];
            $processed_count += $this->process_delta_items( $items, $folder_item_id );

            // Ha van következő oldal (@odata.nextLink), folytatjuk
            if ( isset( $response['@odata.nextLink'] ) ) {
                $endpoint = str_replace( 'https://graph.microsoft.com/v1.0', '', $response['@odata.nextLink'] );
            } else {
                $has_more = false;
                // Ha végeztünk, elmentjük a JÖVŐBELI szinkronizációhoz szükséges Delta Linket
                if ( isset( $response['@odata.deltaLink'] ) ) {
                    update_option( 'msdl_delta_link_' . $folder_item_id, $response['@odata.deltaLink'] );
                }
            }
        }

        return [ 'success' => true, 'processed' => $processed_count ];
    }

    // Segédfüggvény: Útvonalból ID-t csinál a Delta API számára
    private function get_item_id_by_path( $drive_id, $path ) {
        if ( empty( $path ) ) return 'root'; // Ha a teljes Drive-ot akarjuk
        
        $endpoint = "/drives/{$drive_id}/root:/" . rawurlencode( $path ) . "?\$select=id";
        $response = $this->graph_api->make_request( $endpoint );
        
        if ( is_wp_error( $response ) ) return new WP_Error( 'path_not_found', 'A megadott gyökérmappa nem található a SharePointban.' );
        return $response['id'];
    }

    // Elemek adatbázisba írása / törlése
    private function process_delta_items( $items, $root_folder_id ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'msdl_nodes';
        $count = 0;

        foreach ( $items as $item ) {
            // A Delta API a gyökérmappát is visszaadhatja, ezt átugorjuk
            if ( $item['id'] === $root_folder_id ) continue;

            // Ha a Microsoft szerint az elemet TÖRÖLTÉK
            if ( isset( $item['deleted'] ) ) {
                $wpdb->delete( $table_name, [ 'graph_id' => $item['id'] ] );
                $count++;
                continue;
            }

            $is_folder = isset( $item['folder'] );
            $type = $is_folder ? 'folder' : 'file';
            $mime_type = $is_folder ? null : ( $item['file']['mimeType'] ?? '' );
            
            // Szülő azonosítója (Ha a gyökérmappában van, a Graph ID-ja a szülő. Ha feljebb, akkor az almappa ID-ja)
            $parent_graph_id = isset($item['parentReference']) ? $item['parentReference']['id'] : null;
            // Ha közvetlenül a mi kijelölt gyökérmappánkban van, a DB-ben a szülő NULL lesz (hogy a főoldalon jelenjen meg)
            if ( $parent_graph_id === $root_folder_id ) {
                $parent_graph_id = null;
            }

            $existing = $wpdb->get_row( $wpdb->prepare( "SELECT id FROM $table_name WHERE graph_id = %s", $item['id'] ) );

            $data = [
                'graph_id'        => $item['id'],
                'parent_graph_id' => $parent_graph_id,
                'type'            => $type,
                'name'            => sanitize_text_field( $item['name'] ),
                'mime_type'       => sanitize_text_field( $mime_type ),
                'file_size'       => intval( $item['size'] ?? 0 ), // JAVÍTVA: 'size' helyett 'file_size'
                'last_modified'   => wp_date( 'Y-m-d H:i:s', strtotime( $item['lastModifiedDateTime'] ) ), // JAVÍTVA: wp_date használata
            ];

            if ( $existing ) {
                $wpdb->update( $table_name, $data, [ 'id' => $existing->id ] );
            } else {
                // Új elem érkezett!
                $wpdb->insert( $table_name, $data );
            }
            $count++;
        }
        return $count;
    }
}