<?php
/**
 * Plugin Name: MSDL - Dokumentumtár Kliens (Child)
 * Description: Microsoft Graph API dokumentumtár kliens, amely a Main plugintól kapja a hitelesítést.
 * Version: 1.0.0
 */

if ( ! defined( 'WPINC' ) ) die;

define( 'MSDL_CHILD_DIR', plugin_dir_path( __FILE__ ) );
define( 'MSDL_CHILD_URL', plugin_dir_url( __FILE__ ) );

require_once MSDL_CHILD_DIR . 'includes/class-msdl-activator.php';
require_once MSDL_CHILD_DIR . 'includes/class-msdl-admin.php';
require_once MSDL_CHILD_DIR . 'includes/class-msdl-graph-api.php';

function activate_msdl_child() {
    MSDL_Activator::activate();
}
register_activation_hook( __FILE__, 'activate_msdl_child' );

function run_msdl_child() {
    $admin = new MSDL_Child_Admin();
    $admin->init();
    
    // A szinkronizációs motornak ezt az API példányt fogjuk átadni később
    $graph_api = new MSDL_Child_Graph_API();
}
run_msdl_child();