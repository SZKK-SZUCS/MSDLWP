<?php
/**
 * Plugin Name: MSDL - Dokumentumtár Kliens (Child)
 * Description: Microsoft Graph API dokumentumtár kliens, amely a Main plugintól kapja a hitelesítést.
 * Version: 0.9.4
 * Author: MFÜI - Szurofka Márton
 */

if ( ! defined( 'WPINC' ) ) die;

define( 'MSDL_CHILD_DIR', plugin_dir_path( __FILE__ ) );
define( 'MSDL_CHILD_URL', plugin_dir_url( __FILE__ ) );

require_once MSDL_CHILD_DIR . 'includes/class-msdl-activator.php';
require_once MSDL_CHILD_DIR . 'includes/class-msdl-admin.php';
require_once MSDL_CHILD_DIR . 'includes/class-msdl-graph-api.php';
require_once MSDL_CHILD_DIR . 'includes/class-msdl-sync.php';
require_once MSDL_CHILD_DIR . 'includes/class-msdl-download.php';
require_once MSDL_CHILD_DIR . 'includes/class-msdl-elementor.php';

// --- Automatikus Frissítés (PUC) Integráció ---
require_once MSDL_CHILD_DIR . 'includes/plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$msdl_child_update_checker = PucFactory::buildUpdateChecker(
    'https://github.com/SZKK-SZUCS/MSDLWP', // A GitHub repód pontos URL-je
    __FILE__,
    'msdl-child' // Fontos: a slug egyezzen a GitHub Action által generált .zip nevével!
);

// Kényszerítjük, hogy a csatolt msdl-child.zip fájlt töltse le a nyers forráskód helyett
$msdl_child_update_checker->getVcsApi()->enableReleaseAssets('/msdl-child\.zip$/i');
// ----------------------------------------------


// Aktiválási logika
function activate_msdl_child() {
    MSDL_Activator::activate();
}
register_activation_hook( __FILE__, 'activate_msdl_child' );

// Deaktiválási logika (Takarítás)
function deactivate_msdl_child() {
    // Töröljük a beállított automata szinkronizációs időzítőt, ha a plugint kikapcsolják
    wp_clear_scheduled_hook( 'msdl_scheduled_sync' );
}
register_deactivation_hook( __FILE__, 'deactivate_msdl_child' );

// Fő indító logika
function run_msdl_child() {
    $admin = new MSDL_Child_Admin();
    $admin->init();
    
    // A szinkronizációs motornak ezt az API példányt fogjuk átadni később
    $graph_api = new MSDL_Child_Graph_API();

    $download = new MSDL_Child_Download();
    $download->init();

    $elementor = new MSDL_Child_Elementor();
    $elementor->init();
}
run_msdl_child();