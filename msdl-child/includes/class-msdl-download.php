<?php
class MSDL_Child_Download {

    public function init() {
        add_action( 'template_redirect', [ $this, 'handle_download_request' ] );
    }

    /**
     * HMAC a node ID-jára és graph_id-jára: ha a fájl újraszinkronizáláskor új rekordot kap, a régi link érvénytelenné válik.
     */
    public static function get_share_key( $node ) {
        return substr( hash_hmac( 'sha256', 'msdl_download|' . $node->id . '|' . $node->graph_id, wp_salt( 'auth' ) ), 0, 24 );
    }

    public static function get_share_url( $node ) {
        return add_query_arg( [ 'msdl_download' => (int) $node->id, 'msdl_key' => self::get_share_key( $node ) ], home_url( '/' ) );
    }

    public function handle_download_request() {
        if ( ! isset( $_GET['msdl_download'] ) ) return;

        $node_id = intval( $_GET['msdl_download'] );
        if ( ! $node_id ) wp_die( 'Érvénytelen fájl azonosító.', 'Hiba', [ 'response' => 400 ] );

        global $wpdb;
        $table_name = $wpdb->prefix . 'msdl_nodes';
        
        $node = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d AND type = 'file'", $node_id ) );

        if ( ! $node ) wp_die( 'A fájl nem található a helyi adatbázisban.', 'Hiba', [ 'response' => 404 ] );

        // Az admin "Link másolása" gombja aláírt linket ad ki. Érvényes kulccsal a "rejtett" fájl is letölthető
        // (rejtett = nem jelenik meg a listákban, de közvetlen linkkel, pl. Forminatorból, elérhető legyen).
        // A gyökér- és szerepkör-korlátozás ettől még érvényes; kulcs nélkül a rejtett fájl továbbra is tiltott,
        // így az ID-k végigpróbálgatásával nem lehet rejtett fájlokat letölteni.
        $access_roles = $node->visibility_roles;
        $key = isset( $_GET['msdl_key'] ) ? sanitize_text_field( wp_unslash( $_GET['msdl_key'] ) ) : '';
        if ( $access_roles === 'hidden' && $key !== '' && hash_equals( self::get_share_key( $node ), $key ) ) {
            $access_roles = 'public';
        }

        if ( ! class_exists('MSDL_Child_Elementor') || ! MSDL_Child_Elementor::check_item_access( $access_roles ) ) {
            wp_die( 'Nincs jogosultságod a fájl megtekintéséhez vagy letöltéséhez.', 'Hozzáférés megtagadva', [ 'response' => 403 ] );
        }

        $api = new MSDL_Child_Graph_API();
        $token_result = $api->fetch_token_from_main();

        if ( is_wp_error( $token_result ) ) {
            wp_die( 'Hiba a központi szerverhez való kapcsolódáskor: ' . $token_result->get_error_message(), 'Rendszerhiba', [ 'response' => 500 ] );
        }

        $drive_id = $api->drive_id;
        if ( empty( $drive_id ) ) {
            wp_die( 'Nincs beállítva a központi dokumentumtár (Drive ID).', 'Rendszerhiba', [ 'response' => 500 ] );
        }

        $rules = json_decode($node->version_rules, true);
        $active_vid = isset($rules['active_version']) ? $rules['active_version'] : null;
        $now = current_time('timestamp');

        if ( isset($rules['schedules']) && is_array($rules['schedules']) ) {
            $best_time = 0;
            foreach ( $rules['schedules'] as $vid => $time ) {
                if (empty($time)) continue;
                $ts = strtotime($time);
                if ( $ts && $ts <= $now && $ts > $best_time ) {
                    $best_time = $ts;
                    $active_vid = $vid;
                }
            }
        }

        if ( $active_vid ) {
            $versions_endpoint = "/drives/{$drive_id}/items/{$node->graph_id}/versions";
            $versions_response = $api->make_request( $versions_endpoint );

            if ( !is_wp_error($versions_response) && isset($versions_response['value']) && is_array($versions_response['value']) ) {
                foreach ( $versions_response['value'] as $v ) {
                    if ( (string)$v['id'] === (string)$active_vid && !empty($v['@microsoft.graph.downloadUrl']) ) {
                        wp_redirect( $v['@microsoft.graph.downloadUrl'] );
                        exit;
                    }
                }
            }
        }

        // --- ALAPÉRTELMEZETT (VAGY BIZTONSÁGI TARTALÉK) ---
        $endpoint = "/drives/{$drive_id}/items/{$node->graph_id}";
        $response = $api->make_request( $endpoint );

        if ( is_wp_error( $response ) ) {
            wp_die( 'Hiba a fájl lekérésekor a Microsoft Graph-tól.', 'Hiba', [ 'response' => 500 ] );
        }

        if ( empty( $response['@microsoft.graph.downloadUrl'] ) ) {
            wp_die( 'A Microsoft nem adott vissza letöltési linket. Lehet, hogy a fájl sérült vagy a megosztási beállítások tiltják a letöltést.', 'Hiba', [ 'response' => 404 ] );
        }

        wp_redirect( $response['@microsoft.graph.downloadUrl'] );
        exit;
    }
}