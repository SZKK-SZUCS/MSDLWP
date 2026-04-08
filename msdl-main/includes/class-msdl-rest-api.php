<?php
class MSDL_Main_REST_API {
    private $graph_api;

    public function __construct( $graph_api ) {
        $this->graph_api = $graph_api;
    }

    public function init() {
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }

    public function register_routes() {
        register_rest_route( 'msdl-main/v1', '/get-token', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [ $this, 'serve_token' ],
            'permission_callback' => [ $this, 'check_api_key' ]
        ]);
    }

    public function check_api_key( WP_REST_Request $request ) {
        $provided = $request->get_header( 'X-MSDL-API-Key' );
        $stored = get_option( 'msdl_internal_api_key' );
        if ( empty($stored) || $provided !== $stored ) {
            return new WP_Error( 'forbidden', 'Érvénytelen belső API kulcs.', ['status' => 403] );
        }
        return true;
    }

    public function serve_token() {
        $token = $this->graph_api->get_access_token();
        if ( is_wp_error( $token ) ) return $token;

        return rest_ensure_response([
            'access_token' => $token,
            'site_id'      => get_option( 'msdl_site_id' ),
            'drive_id'     => get_option( 'msdl_drive_id' )
        ]);
    }
}