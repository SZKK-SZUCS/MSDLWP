<?php
class MSDL_Main_Cron {
    public function init() {
        add_filter( 'cron_schedules', [ $this, 'add_cron_schedules' ] );
        
        add_action( 'msdl_main_master_sync', [ $this, 'queue_child_syncs' ] );
        add_action( 'msdl_main_single_site_sync', [ $this, 'process_single_site' ], 10, 1 );
        add_action( 'update_option_msdl_global_sync_interval', [ $this, 'update_master_schedule' ], 10, 2 );

        if ( ! wp_next_scheduled( 'msdl_main_master_sync' ) ) {
            $interval = get_option( 'msdl_global_sync_interval', 'hourly' );
            if ( ! empty( $interval ) && $interval !== 'disabled' ) {
                $this->schedule_next_master_sync( $interval );
            }
        }
    }

    public function add_cron_schedules( $schedules ) {
        $schedules['msdl_5min'] = [ 'interval' => 300, 'display' => '5 percenként' ];
        $schedules['msdl_15min'] = [ 'interval' => 900, 'display' => '15 percenként' ];
        $schedules['msdl_30min'] = [ 'interval' => 1800, 'display' => '30 percenként' ];
        $schedules['msdl_thricedaily'] = [ 'interval' => 28800, 'display' => 'Naponta háromszor (8, 12, 16)' ];
        return $schedules;
    }

    public function update_master_schedule( $old_value, $new_value ) {
        wp_clear_scheduled_hook( 'msdl_main_master_sync' );
        if ( ! empty( $new_value ) && $new_value !== 'disabled' ) {
            $this->schedule_next_master_sync( $new_value );
        }
    }

    // ÚJ LOGIKA: Pontos időpont számítása WP időzóna alapján (08:00, 12:00 és 16:00)
    private function schedule_next_master_sync( $interval ) {
        $now = current_time( 'timestamp' ); // Helyi WP idő
        $next_run = $now;

        if ( $interval === 'daily' ) {
            $today_8 = strtotime( date( 'Y-m-d 08:00:00', $now ) );
            $next_run = ( $now >= $today_8 ) ? $today_8 + DAY_IN_SECONDS : $today_8;
        } elseif ( $interval === 'msdl_thricedaily' || $interval === 'twicedaily' ) {
            // A kérésednek megfelelően a 'twicedaily'-t is átállíthatjuk, 
            // vagy használhatod az új 'msdl_thricedaily' kulcsot.
            $today_8  = strtotime( date( 'Y-m-d 08:00:00', $now ) );
            $today_12 = strtotime( date( 'Y-m-d 12:00:00', $now ) );
            $today_16 = strtotime( date( 'Y-m-d 16:00:00', $now ) );
            
            if ( $now < $today_8 ) {
                $next_run = $today_8;
            } elseif ( $now < $today_12 ) {
                $next_run = $today_12;
            } elseif ( $now < $today_16 ) {
                $next_run = $today_16;
            } else {
                $next_run = $today_8 + DAY_IN_SECONDS;
            }
        } else {
            $schedules = wp_get_schedules();
            $seconds = isset($schedules[$interval]) ? $schedules[$interval]['interval'] : 3600;
            $next_run = $now + $seconds;
        }

        $utc_next_run = get_gmt_from_date( date( 'Y-m-d H:i:s', $next_run ), 'U' );
        wp_schedule_single_event( $utc_next_run, 'msdl_main_master_sync' );
    }

    public function queue_child_syncs() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'msdl_sites';
        
        $sites = $wpdb->get_results( "SELECT * FROM $table_name WHERE is_active = 1 AND sync_mode = 'central' AND folder_path != ''" );
        
        $delay = 0;
        foreach ( $sites as $site ) {
            wp_schedule_single_event( time() + $delay, 'msdl_main_single_site_sync', [ $site ] );
            $delay += 10;
        }

        $interval = get_option( 'msdl_global_sync_interval', 'hourly' );
        if ( ! empty( $interval ) && $interval !== 'disabled' ) {
            $this->schedule_next_master_sync( $interval );
        }
    }

    public function process_single_site( $site ) {
        $internal_key = get_option('msdl_internal_api_key');
        if ( empty( $internal_key ) ) return;

        $domain = $site->domain;
        $protocol = (strpos($domain, '.local') !== false || strpos($domain, '.test') !== false) ? 'http://' : 'https://';
        $url = rtrim($protocol . $domain, '/');
        
        $endpoint = '/wp-json/msdl-child/v1/sync-now';

        $response = wp_remote_post( $url . $endpoint, [
            'headers'   => [ 'X-MSDL-API-Key' => $internal_key ],
            'timeout'   => 45,
            'sslverify' => false
        ]);

        if ( ! is_wp_error( $response ) ) {
            $body = wp_remote_retrieve_body( $response );
            $decoded = json_decode( $body, true );
            
            if ( isset($decoded['success']) && $decoded['success'] === true ) {
                global $wpdb;
                $table_name = $wpdb->prefix . 'msdl_sites';
                $wpdb->update( 
                    $table_name, 
                    [ 'last_sync' => wp_date('Y-m-d H:i:s') ], 
                    [ 'id' => $site->id ] 
                );
            }
        }
    }
}