<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class MSIV_Ajax{
    static $slug;

    public static function init() {
        if ( ! WP_DEBUG || ( WP_DEBUG && ! WP_DEBUG_DISPLAY ) ) {
            @ini_set( 'display_errors', 0 );
        }
        $GLOBALS['wpdb']->hide_errors();

        self::$slug = MSIV()->slug();
        self::add_ajax_events();
    }
    public static function add_ajax_events() {

        $ajax_events = array(
        );

        if( is_admin() ){
            $ajax_events = array_merge( $ajax_events, array(
                'save_settings'          => false
            ) );
        }

        foreach ( $ajax_events as $ajax_event => $nopriv ) {
            add_action( 'wp_ajax_' . msiv_ajax_command( $ajax_event ), array( __CLASS__, $ajax_event ) );

            if ( $nopriv ) {
                add_action( 'wp_ajax_nopriv_' . msiv_ajax_command( $ajax_event ), array( __CLASS__, $ajax_event ) );
            }
        }
    }

    static function save_settings() {
        $shipping_method = new MSIV_Shipping_Korea_Zone();

        $result = $shipping_method->save_setting();

        if( $result ){
            wp_send_json_success( $result );
        }else{
            wp_send_json_error( $result );
        }
    }

}

MSIV_Ajax::init();
