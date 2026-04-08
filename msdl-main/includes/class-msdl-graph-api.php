<?php
class MSDL_Main_Graph_API {
    private $access_token = null;

    public function get_access_token() {
        if ( $this->access_token ) return $this->access_token;

        $tenant_id = get_option( 'msdl_tenant_id' );
        $client_id = get_option( 'msdl_client_id' );
        $client_secret = get_option( 'msdl_client_secret' );

        if ( empty($tenant_id) || empty($client_id) || empty($client_secret) ) {
            return new WP_Error( 'missing_creds', 'Hiányzó MS hitelesítő adatok a központban.' );
        }

        $url = "https://login.microsoftonline.com/{$tenant_id}/oauth2/v2.0/token";
        $response = wp_remote_post( $url, [
            'body' => [ 'client_id' => $client_id, 'client_secret' => $client_secret, 'scope' => 'https://graph.microsoft.com/.default', 'grant_type' => 'client_credentials' ]
        ]);

        if ( is_wp_error( $response ) ) return $response;
        
        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        if ( isset( $body['access_token'] ) ) {
            $this->access_token = $body['access_token'];
            return $this->access_token;
        }
        return new WP_Error( 'auth_failed', 'Sikertelen hitelesítés', $body );
    }

    /**
     * ÚJ METÓDUS: Közvetlen Graph API hívások lebonyolítása a Main pluginból
     */
    public function make_request( $endpoint, $method = 'GET' ) {
        $token = $this->get_access_token();
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
        
        return new WP_Error( 'graph_error', 'Graph API hiba: ' . (isset($body['error']['message']) ? $body['error']['message'] : 'Ismeretlen hiba'), $body );
    }
}