<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MSPS_Shortcodes' ) ) :

	class MSPS_Shortcodes {
		public static function init() {
			$shortcodes = array(
				'msps_point_info' => array( __CLASS__, 'output_point_info' ),
				'msps_point_log'  => array( __CLASS__, 'output_point_logs' ),
				'msps_notice'     => array( __CLASS__, 'output_point_notice' ),
			);

			foreach ( $shortcodes as $shortcode => $function ) {
				add_shortcode( $shortcode, $function );
			}
		}

		public static function output_point_info( $attrs, $content = null ) {
			$result = '';

			$params = shortcode_atts( array(), $attrs );

			if ( is_user_logged_in() ) {
				$user   = new MSPS_User( get_current_user_id() );
				$result = get_option( 'mshop_point_system_point_info_template', __( '{name} 고객님은 {point} 포인트가 있습니다.', 'mshop-point-ex' ) );

				$result = str_replace( "{name}", $user->get_user_info( 'display_name' ), $result );
				$result = str_replace( "{point}", number_format( $user->get_point(), wc_get_price_decimals() ), $result );
                $result = str_replace( "{role}", mshop_point_get_user_role_name(), $result );
			}

			return nl2br( $result );
		}

		public static function output_point_logs( $attrs, $content = null ) {
			wp_enqueue_script( 'msps-myaccount', plugins_url( '/assets/js/frontend.js', MSPS_PLUGIN_FILE ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-tabs', 'underscore' ), MSPS_VERSION );
			wp_enqueue_style( 'msps-myaccount', plugins_url( '/assets/css/frontend.css', MSPS_PLUGIN_FILE ), array(), MSPS_VERSION );
            wp_enqueue_style( 'msps-fontawesome', plugins_url( '/assets/vendor/fontawesome/css/all.min.css', MSPS_PLUGIN_FILE ), array(), MSPS_VERSION );

			ob_start();

			msps_output_point_logs();

			return ob_get_clean();
		}

		public static function output_point_notice( $attrs, $content = null ) {
			$params = shortcode_atts( array(
				'target' => 'product',
			) , $attrs );

			ob_start();

			if( 'product' == $params['target']) {
				MSPS_Cart::woocommerce_after_add_to_cart_form();
			}else if( 'cart' == $params['target']) {
				MSPS_Cart::woocommerce_after_cart_totals();
			}else if( 'checkout' == $params['target']) {
				MSPS_Checkout::woocommerce_before_checkout_form();
			}

			return ob_get_clean();
		}
	}

	MSPS_Shortcodes::init();

endif;