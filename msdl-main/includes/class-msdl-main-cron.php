<?php
class MSDL_Main_Cron {
    public function init() {
        // Egyedi időzítők hozzáadása
        add_filter( 'cron_schedules', [ $this, 'add_cron_schedules' ] );
        
        // Fő (Master) feladat: Sorba állítja a webhelyeket
        add_action( 'msdl_main_master_sync', [ $this, 'queue_child_syncs' ] );
        
        // Egyedi feladat: Egyetlen webhely szinkronizálása
        add_action( 'msdl_main_single_site_sync', [ $this, 'process_single_site' ], 10, 1 );

        // Ha a React felületen elmentjük a központi időzítést, automatikusan beállítjuk a Cron-t
        add_action( 'update_option_msdl_global_sync_interval', [ $this, 'update_master_schedule' ], 10, 2 );
    }

    public function add_cron_schedules( $schedules ) {
        $schedules['msdl_15min'] = [ 'interval' => 900, 'display' => '15 percenként' ];
        $schedules['msdl_30min'] = [ 'interval' => 1800, 'display' => '30 percenként' ];
        return $schedules;
    }

    public function update_master_schedule( $old_value, $new_value ) {
        wp_clear_scheduled_hook( 'msdl_main_master_sync' );
        if ( ! empty( $new_value ) ) {
            wp_schedule_event( time(), $new_value, 'msdl_main_master_sync' );
        }
    }

    // Ez fut le pl. 30 percenként
    public function queue_child_syncs() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'msdl_sites';
        
        // Csak az aktív, mappával rendelkező oldalakat kérjük le
        $sites = $wpdb->get_results( "SELECT * FROM $table_name WHERE is_active = 1 AND sync_mode = 'central' AND folder_path != ''" );
        
        $delay = 0;
        foreach ( $sites as $site ) {
            // EGYMÁS UTÁN (Queue): 10 másodpercet adunk minden webhelynek
            wp_schedule_single_event( time() + $delay, 'msdl_main_single_site_sync', [ $site ] );
            $delay += 10;
        }
    }

    // Ez fut le oldalanként, 10 másodperces csúsztatásokkal a háttérben
    public function process_single_site( $site ) {
        $internal_key = get_option('msdl_internal_api_key');
        if ( empty( $internal_key ) ) return;

        $domain = $site->domain;
        $protocol = (strpos($domain, '.local') !== false || strpos($domain, '.test') !== false) ? 'http://' : 'https://';
        $url = rtrim($protocol . $domain, '/');
        
        // Rászólunk a Child oldalra, hogy azonnal indítsa el a Delta motorját
        $endpoint = '/wp-json/msdl-child/v1/sync-now';

        $response = wp_remote_post( $url . $endpoint, [
            'headers'   => [ 'X-MSDL-API-Key' => $internal_key ],
            'timeout'   => 45,
            'sslverify' => false
        ]);

        if ( ! is_wp_error( $response ) ) {
            $body = wp_remote_retrieve_body( $response );
            $decoded = json_decode( $body, true );
            
            // Ha a Child sikeresen lefutott, beírjuk a Main adatbázisba az időpontot!
            if ( isset($decoded['success']) && $decoded['success'] === true ) {
                global $wpdb;
                $table_name = $wpdb->prefix . 'msdl_sites';
                $wpdb->update( 
                    $table_name, 
                    [ 'last_sync' => current_time('mysql') ], 
                    [ 'id' => $site->id ] 
                );
            }
        }
    }
}