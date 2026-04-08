<?php
class MSDL_Child_Graph_API {

    private $main_server_url;
    private $internal_api_key;
    private $access_token = null;
    
    public $site_id = null;
    public $drive_id = null;
    public $root_folder_id = null; // Ezt is a Maintől fogjuk kapni

    public function __construct() {
        // Visszatértünk az adatbázisból való beolvasáshoz
        $this->main_server_url  = rtrim( get_option( 'msdl_main_server_url' ), '/' );
        $this->internal_api_key = get_option( 'msdl_internal_api_key' );
    }

    public function fetch_token_from_main() {
        if ( $this->access_token ) return $this->access_token;

        if ( empty( $this->main_server_url ) || empty( $this->internal_api_key ) ) {
            return new WP_Error( 'missing_config', 'Hiányzik a Main szerver URL vagy az API kulcs a beállításokból.' );
        }

        $endpoint = $this->main_server_url . '/wp-json/msdl-main/v1/get-token';
        
        // A child_domain átadása, hogy a Main tudja, kinek a mappáját kell visszaadni
        $child_domain = parse_url( site_url(), PHP_URL_HOST );

        $response = wp_remote_get( $endpoint, [
            'headers' => [ 
                'X-MSDL-API-Key' => $this->internal_api_key,
                'X-MSDL-Child-Domain' => $child_domain,
                'X-MSDL-Sync-Mode'    => get_option( 'msdl_sync_mode', 'central' )
            ],
            'timeout' => 15
        ] );

        if ( is_wp_error( $response ) ) return $response;

        $response_code = wp_remote_retrieve_response_code( $response );
        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( $response_code === 200 && isset( $body['access_token'] ) ) {
            $this->access_token   = $body['access_token'];
            $this->site_id        = $body['site_id'] ?? null;
            $this->drive_id       = $body['drive_id'] ?? null;
            $this->root_folder_id = $body['root_folder_id'] ?? null;
            return $this->access_token;
        }

        // Tényleges hibaüzenet kinyerése a Main szerver válaszából
        $error_msg = isset( $body['message'] ) ? $body['message'] : 'HTTP Hiba: ' . $response_code;
        return new WP_Error( 'main_api_error', $error_msg, $body );
    }

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
}