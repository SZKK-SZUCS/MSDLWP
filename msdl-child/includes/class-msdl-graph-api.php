<?php
class MSDL_Child_Graph_API {

    private $main_server_url;
    private $internal_api_key;
    private $access_token = null;
    
    // Ezeket a Main szervertől kapjuk meg a tokennel együtt
    public $site_id = null;
    public $drive_id = null;

    public function __construct() {
        // A perjeleket levágjuk a végéről a biztonság kedvéért
        $this->main_server_url  = rtrim( get_option( 'msdl_main_server_url' ), '/' );
        $this->internal_api_key = get_option( 'msdl_internal_api_key' );
    }

    /**
     * Lekéri a Graph API Tokent és az ID-kat a Main WordPress Plugintól.
     */
    public function fetch_token_from_main() {
        if ( $this->access_token ) return $this->access_token;

        if ( empty( $this->main_server_url ) || empty( $this->internal_api_key ) ) {
            return new WP_Error( 'missing_config', 'Hiányzik a Main szerver URL vagy az API kulcs a beállításokból.' );
        }

        $endpoint = $this->main_server_url . '/wp-json/msdl-main/v1/get-token';
        
        $response = wp_remote_get( $endpoint, [
            'headers' => [ 'X-MSDL-API-Key' => $this->internal_api_key ],
            'timeout' => 15
        ] );

        if ( is_wp_error( $response ) ) return $response;

        $response_code = wp_remote_retrieve_response_code( $response );
        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( $response_code === 200 && isset( $body['access_token'] ) ) {
            $this->access_token = $body['access_token'];
            $this->site_id      = $body['site_id'] ?? null;
            $this->drive_id     = $body['drive_id'] ?? null;
            return $this->access_token;
        }

        return new WP_Error( 'main_api_error', 'Hiba a Main szerverhez való csatlakozáskor.', $body );
    }

    /**
     * MS Graph API hívás (Közvetlenül a Microsoft felé).
     */
    public function make_request( $endpoint, $method = 'GET' ) {
        $token = $this->fetch_token_from_main();
        if ( is_wp_error( $token ) ) return $token;

        $url = "https://graph.microsoft.com/v1.0" . $endpoint;
        $response = wp_remote_request( $url, [
            'method'  => $method,
            'timeout' => 30,
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ]
        ] );

        if ( is_wp_error( $response ) ) return $response;

        $response_code = wp_remote_retrieve_response_code( $response );
        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( $response_code >= 200 && $response_code < 300 ) {
            return $body;
        }

        return new WP_Error( 'graph_error', 'Graph API hiba', $body );
    }

    /**
     * Feloldja a mappa Graph ID-ját a neve alapján.
     */
    public function get_folder_id_by_path( $folder_path ) {
        // Inicializáljuk a tokent, hogy a drive_id is betöltődjön a Main-ről
        $token = $this->fetch_token_from_main();
        if ( is_wp_error( $token ) ) return $token;

        if ( empty( $this->drive_id ) ) {
            return new WP_Error( 'missing_drive_id', 'A Main szerver nem küldött Drive ID-t.' );
        }

        $encoded_path = rawurlencode( trim( $folder_path ) );
        $endpoint = "/drives/{$this->drive_id}/root:/{$encoded_path}";
        
        $response = $this->make_request( $endpoint );

        if ( is_wp_error( $response ) ) return $response;
        if ( isset( $response['id'] ) ) return $response['id'];

        return new WP_Error( 'folder_not_found', 'A mappa nem található a SharePointban.' );
    }
}