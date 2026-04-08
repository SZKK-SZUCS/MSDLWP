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
        // Levágjuk a perjeleket a biztonság kedvéért
        $folder_path = trim( $this->graph_api->root_folder_id, '/' );

        if ( empty( $drive_id ) ) {
            return new WP_Error( 'missing_drive', 'Nincs Drive ID beállítva a központban ehhez a webhelyhez.' );
        }

        // 2. Összeállítjuk a megfelelő Graph API végpontot
        // Ha üres az útvonal, a gyökeret kérjük. Ha van útvonal, a kettőspontos (path-based) szintaxist használjuk.
        $endpoint = empty( $folder_path ) 
            ? "/drives/{$drive_id}/root/children" 
            : "/drives/{$drive_id}/root:/" . rawurlencode( $folder_path ) . ":/children";

        // Csak a legszükségesebb adatokat kérjük le a gyorsaság érdekében
        $endpoint .= "?\$select=id,name,folder,file,size,lastModifiedDateTime";

        $response = $this->graph_api->make_request( $endpoint );

        if ( is_wp_error( $response ) ) return $response;

        $items = isset( $response['value'] ) ? $response['value'] : [];
        
        // 3. Elemek feldolgozása és adatbázisba mentése (Egyelőre a gyökérmappa elemeit mentjük)
        return $this->process_items( $items, null );
    }

    private function process_items( $items, $parent_id ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'msdl_nodes';
        $processed = 0;

        foreach ( $items as $item ) {
            $is_folder = isset( $item['folder'] );
            $type = $is_folder ? 'folder' : 'file';
            $mime_type = $is_folder ? null : ( $item['file']['mimeType'] ?? '' );

            // Megnézzük, létezik-e már ez a fájl/mappa az adatbázisunkban
            $existing = $wpdb->get_row( $wpdb->prepare( "SELECT id FROM $table_name WHERE graph_id = %s", $item['id'] ) );

            $data = [
                'graph_id'        => $item['id'],
                'parent_graph_id' => $parent_id,
                'type'            => $type,
                'name'            => sanitize_text_field( $item['name'] ),
                'mime_type'       => sanitize_text_field( $mime_type ),
                'size'            => intval( $item['size'] ?? 0 ),
                'last_modified'   => date( 'Y-m-d H:i:s', strtotime( $item['lastModifiedDateTime'] ) ),
            ];

            if ( $existing ) {
                // Ha létezik, frissítjük (pl. ha megváltozott a mérete vagy a neve)
                $wpdb->update( $table_name, $data, [ 'id' => $existing->id ] );
            } else {
                // Ha új, beszúrjuk
                $wpdb->insert( $table_name, $data );
            }
            
            $processed++;
        }

        return [ 'success' => true, 'processed' => $processed ];
    }
}