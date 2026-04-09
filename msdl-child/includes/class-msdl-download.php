<?php
class MSDL_Child_Download {

    public function init() {
        // A template_redirect hook a legjobb hely, mert már be van töltve a WP user, 
        // de még nem kezdődött el a HTML renderelés (így tudunk headert módosítani/átirányítani).
        add_action( 'template_redirect', [ $this, 'handle_download_request' ] );
    }

    public function handle_download_request() {
        // Csak akkor lépünk közbe, ha a mi URL paraméterünk szerepel
        if ( ! isset( $_GET['msdl_download'] ) ) return;

        $node_id = intval( $_GET['msdl_download'] );
        if ( ! $node_id ) wp_die( 'Érvénytelen fájl azonosító.', 'Hiba', [ 'response' => 400 ] );

        global $wpdb;
        $table_name = $wpdb->prefix . 'msdl_nodes';
        
        // Lekérjük a fájlt az adatbázisból (csak a fájlokat engedjük letölteni, mappákat nem)
        $node = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d AND type = 'file'", $node_id ) );

        if ( ! $node ) wp_die( 'A fájl nem található a helyi adatbázisban.', 'Hiba', [ 'response' => 404 ] );

        // 1. LÉPÉS: KÖZPONTI JOGOSULTSÁG ELLENŐRZÉS (Gyökérmappa + Fájl szint)
        // Behúzzuk a frissített Elementor osztályunk metódusát!
        if ( ! class_exists('MSDL_Child_Elementor') || ! MSDL_Child_Elementor::check_item_access( $node->visibility_roles ) ) {
            wp_die( 'Nincs jogosultságod a fájl megtekintéséhez vagy letöltéséhez.', 'Hozzáférés megtagadva', [ 'response' => 403 ] );
        }

        // 2. LÉPÉS: Fájl lekérése a Microsoft Graph API-tól
        $api = new MSDL_Child_Graph_API();
        $token_result = $api->fetch_token_from_main();

        if ( is_wp_error( $token_result ) ) {
            wp_die( 'Hiba a központi szerverhez való kapcsolódáskor: ' . $token_result->get_error_message(), 'Rendszerhiba', [ 'response' => 500 ] );
        }

        $drive_id = $api->drive_id;
        if ( empty( $drive_id ) ) {
            wp_die( 'Nincs beállítva a központi dokumentumtár (Drive ID).', 'Rendszerhiba', [ 'response' => 500 ] );
        }

        // Csak azt a specifikus adatot (downloadUrl) kérjük le, amire szükségünk van
        $endpoint = "/drives/{$drive_id}/items/{$node->graph_id}";
        $response = $api->make_request( $endpoint );

        if ( is_wp_error( $response ) ) {
            wp_die( 'Hiba a fájl lekérésekor a Microsoft Graph-tól.', 'Hiba', [ 'response' => 500 ] );
        }

        if ( empty( $response['@microsoft.graph.downloadUrl'] ) ) {
            wp_die( 'A Microsoft nem adott vissza letöltési linket. Lehet, hogy a fájl sérült vagy a megosztási beállítások tiltják a letöltést.', 'Hiba', [ 'response' => 404 ] );
        }

        // 3. LÉPÉS: Átirányítás a pre-autentikált, ideiglenes URL-re
        wp_redirect( $response['@microsoft.graph.downloadUrl'] );
        exit;
    }
}