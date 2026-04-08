<?php
/**
 * Plugin Name: MSDL - Központi Hitelesítő (Main)
 * Description: Microsoft Graph API központi token szolgáltató és weblap menedzser a Child pluginok számára.
 * Version: 1.0.0
 */

if ( ! defined( 'WPINC' ) ) die;

define( 'MSDL_MAIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MSDL_MAIN_URL', plugin_dir_url( __FILE__ ) );

// Aktivációs hook bekötése
require_once MSDL_MAIN_DIR . 'includes/class-msdl-main-activator.php';
register_activation_hook( __FILE__, [ 'MSDL_Main_Activator', 'activate' ] );

require_once MSDL_MAIN_DIR . 'includes/class-msdl-admin.php';
require_once MSDL_MAIN_DIR . 'includes/class-msdl-graph-api.php';
require_once MSDL_MAIN_DIR . 'includes/class-msdl-rest-api.php';
require_once MSDL_MAIN_DIR . 'includes/class-msdl-main-cron.php';

function run_msdl_main() {
    MSDL_Main_Activator::activate();

    $admin = new MSDL_Main_Admin();
    $admin->init();

    $graph_api = new MSDL_Main_Graph_API();
    
    $rest_api = new MSDL_Main_REST_API( $graph_api );
    $rest_api->init();

    $cron = new MSDL_Main_Cron();
    $cron->init();
}
run_msdl_main();