<?php

/*
=====================================================================================
                엠샵 프리미엄 포인트 / Copyright 2014-2015 by CodeM(c)
=====================================================================================

  [ 우커머스 버전 지원 안내 ]

   워드프레스 버전 : WordPress 4.3.1 이상

   우커머스 버전 : WooCommerce 3.0 이상


  [ 코드엠 플러그인 라이센스 규정 ]

   (주)코드엠에서 개발된 워드프레스  플러그인을 사용하시는 분들에게는 다음 사항에 대한 동의가 있는 것으로 간주합니다.

   1. 코드엠에서 개발한 워드프레스 우커머스용 엠샵 프리미엄 포인트 플러그인의 저작권은 (주)코드엠에게 있습니다.
   
   2. 플러그인은 사용권을 구매하는 것이며, 프로그램 저작권에 대한 구매가 아닙니다.

   3. 플러그인을 구입하여 다수의 사이트에 복사하여 사용할 수 없으며, 1개의 라이센스는 1개의 사이트에만 사용할 수 있습니다. 
      이를 위반 시 지적 재산권에 대한 손해 배상 의무를 갖습니다.

   4. 플러그인은 구입 후 1년간 업데이트를 지원합니다.

   5. 플러그인은 워드프레스, 테마, 플러그인과의 호환성에 대한 책임이 없습니다.

   6. 플러그인 설치 후 버전에 관련한 운용 및 관리의 책임은 사이트 당사자에게 있습니다.

   7. 다운로드한 플러그인은 환불되지 않습니다.

=====================================================================================
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MSPS_Myaccount' ) ) {
	class MSPS_Myaccount {
		static $process_register_point = false;
		public static function init() {
			if ( MSPS_Manager::enabled() && self::show_point_menu() ) {
				add_action( 'woocommerce_account_menu_items', array( __CLASS__, 'woocommerce_account_menu_items' ) );
				add_action( 'woocommerce_account_mshop-point_endpoint', array( __CLASS__, 'mshop_point_endpoint' ) );
			}

			add_filter( 'woocommerce_valid_order_statuses_for_cancel', array( __CLASS__, 'maybe_change_cancel_order_statuses' ), 10, 2 );
			add_filter( 'woocommerce_my_account_my_orders_actions', array( __CLASS__, 'maybe_set_cancel_action' ), 99, 2 );
		}
		public static function woocommerce_register_form() {
			wc_get_template( 'myaccount/mshop-point-form-register.php', array(), '', MSPS()->template_path() );
		}
		public static function user_register( $user_id ) {
			if ( self::$process_register_point ) {
				return;
			}

			self::$process_register_point = true;

			$amount = get_option( 'mshop_point_system_user_point_register_amount', 0 );

			if ( 'yes' == get_option( 'mshop_point_system_use_user_point_rule' ) && $amount > 0 ) {
				$current_language = apply_filters( 'msps_get_current_language', mshop_wpml_get_current_language() );
				$user             = new MSPS_User( $user_id, $current_language );
				$remain_point     = $user->earn_point( $amount, 'free_point' );

				$note = sprintf( __( '신규회원가입 포인트(%s)가 적립되었습니다.', 'mshop-point-ex' ), number_format( $amount, wc_get_price_decimals() ) );

				MSPS_Log::add_log( $user_id, msps_get_wallet_id( 'free_point', null, $current_language ), 'earn', 'register', $amount, $remain_point, 'completed', 0, $note );
			}
		}

		public static function show_point_menu() {
			$roles = get_option( 'msps_myaccount_menu_roles', '' );
			if ( ! empty( $roles ) ) {
				$roles = explode( ',', $roles );
			}

			return empty( $roles ) || in_array( mshop_point_get_user_role(), $roles );
		}

		public static function woocommerce_account_menu_items( $items ) {
			//엔드포인트 동작여부 확인하여 동작시에만 동작하도록 처리
			$logout_endpoint = get_option( 'woocommerce_logout_endpoint', 'customer-logout' );
			if ( ! empty( $logout_endpoint ) ) {
				$removed = false;
				if ( isset( $items['customer-logout'] ) ) {
					unset( $items['customer-logout'] );
					$removed = true;
				}
				$items['mshop-point'] = __( '포인트', 'mshop-point-ex' );

				if ( $removed ) {
					$items['customer-logout'] = __( 'Logout', 'woocommerce' );
				}
			} else {
				$items['mshop-point'] = __( '포인트', 'mshop-point-ex' );
			}

			return $items;
		}

		public static function mshop_point_endpoint() {
			wp_enqueue_script( 'msps-myaccount', plugins_url( '/assets/js/frontend.js', MSPS_PLUGIN_FILE ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-tabs', 'underscore' ), MSPS_VERSION );
			wp_enqueue_style( 'msps-myaccount', plugins_url( '/assets/css/frontend.css', MSPS_PLUGIN_FILE ), array(), MSPS_VERSION );
			wp_enqueue_style( 'msps-fontawesome', plugins_url( '/assets/vendor/fontawesome/css/all.min.css', MSPS_PLUGIN_FILE ), array(), MSPS_VERSION );

			wc_get_template( 'myaccount/mshop-point.php', array(), '', MSPS()->template_path() );
		}
		public static function maybe_change_cancel_order_statuses( $order_statuses, $order ) {
			if ( ! function_exists( 'PAFW' ) && $order && 'mshop-point' == $order->get_payment_method() ) {
				$order_statuses = apply_filters( 'msps_valid_order_statuses_for_cancel', $order_statuses, $order );
			}

			return $order_statuses;
		}
		public static function maybe_set_cancel_action( $actions, $order ) {
			if ( function_exists( 'PAFW' ) && $order && ( 'mshop-point' == $order->get_payment_method() || ( 0 == $order->get_total() && MSPS_Order::get_used_point( $order ) > 0 ) ) ) {
				$valid_statuses = explode( ',', get_option( 'pafw-gw-possible_refund_status_for_mypage', 'pending,on-hold' ) );

				if ( in_array( $order->get_status(), $valid_statuses ) ) {
					$cancel_endpoint    = get_permalink( wc_get_page_id( 'cart' ) );
					$myaccount_endpoint = esc_attr( wc_get_endpoint_url( 'orders', '', wc_get_page_permalink( 'myaccount' ) ) );

					$actions['cancel'] = array(
						'url'  => wp_nonce_url( add_query_arg( array(
							'pafw-cancel-order' => 'true',
							'order_key'         => $order->get_order_key(),
							'order_id'          => $order->get_id(),
							'redirect'          => $myaccount_endpoint
						), $cancel_endpoint ), 'pafw-cancel-order-' . $order->get_id() . '-' . $order->get_order_key() ),
						'name' => __( 'Cancel', 'woocommerce' )
					);
				}
			}

			return $actions;
		}
	}

}