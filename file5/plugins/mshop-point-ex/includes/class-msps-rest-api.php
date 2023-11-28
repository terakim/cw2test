<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MSPS_Rest_Api' ) ) {
	class MSPS_Rest_Api {
		static function init() {
			add_action( 'rest_api_init', array( __CLASS__, 'register_point_field' ) );
			add_action( 'woocommerce_rest_pre_insert_shop_order_object', array( __CLASS__, 'maybe_redeposit_used_point' ), 10, 3 );
			add_action( 'woocommerce_rest_pre_insert_shop_order_object', array( __CLASS__, 'maybe_apply_point' ), 20, 3 );

			add_action( 'woocommerce_rest_insert_customer', array( __CLASS__, 'maybe_update_customer_point' ), 20, 3 );
		}

		static function register_point_field() {
			register_rest_field(
				'customer',
				'mshop_point',
				array(
					'get_callback' => array( __CLASS__, 'set_user_point_data' ),
					'schema'       => null,
				)
			);
		}

		static function set_user_point_data( $prepared, $field_name, $request, $object_type ) {
			$user         = new MSPS_User( $request['id'] );
			$wallet_items = $user->wallet->load_wallet_items();

			$wallets = array();

			foreach ( $wallet_items as $wallet_item ) {
				$wallets[] = array(
					'id'    => $wallet_item->get_id(),
					'name'  => $wallet_item->get_name(),
					'point' => $wallet_item->get_point()
				);
			}

			return array(
				'point'   => $user->get_point(),
				'wallets' => $wallets
			);
		}
		static function maybe_redeposit_used_point( $order, $request, $creating ) {
			$params = $request->get_json_params();
			$point  = msps_get( $params, 'mshop_point', 0 );

			if ( $point > 0 && $order->get_customer_id() > 0 && $order->has_status( array( 'pending', 'failed' ) ) ) {

				if ( MSPS_Order::is_order_with_point( $order, $order->get_customer_id() ) ) {
					$current_language = apply_filters( 'msps_get_current_language', mshop_wpml_get_current_language() );
					$user             = new MSPS_User( $order->get_customer_id() );

					$deduction_info = MSPS_Order::get_deduction_info( $order );
					$used_point     = MSPS_Order::get_used_point( $order );

					if ( floatval( $used_point ) > 0 && MSPS_Order::update_order( $order, $used_point ) ) {
						$current_balance = $user->get_point();

						$remain_point = $user->wallet->redeposit( $deduction_info );
						foreach ( $deduction_info as $item_id => $amount ) {
							$current_balance += $amount;
							MSPS_Log::add_log( $order->get_user_id(), msps_get_wallet_id( $item_id, null, $current_language ), 'earn', 'purchase', $amount, $current_balance, 'completed', $order->get_id(), '', msps_get_wallet_name( $user, $item_id ) );
						}
						foreach ( $order->get_fees() as $item_id => $fee ) {
							if ( in_array( $fee->get_name(), array( __( '포인트 할인', 'mshop-point-ex' ), __( '포인트 할인 (비과세)', 'mshop-point-ex' ), __( '포인트 할인 (과세)', 'mshop-point-ex' ) ) ) ) {
								$order->remove_item( $item_id );
							}
						}
						$order->calculate_totals();
						$order->save();

						$message = sprintf( __( '주문(<a href="%1$s">#%2$s</a>) 결제시 포인트 사용금액 변경으로 사용된 %3$s포인트가 재적립 되었습니다.', 'mshop-point-ex' ), get_edit_post_link( $order->get_id() ), $order->get_id(), number_format( floatval( $used_point ), wc_get_price_decimals() ) );
						$order->add_order_note( __( '[포인트 알림] ', 'mshop-point-ex' ) . $message );

						MSPS_Order::update_used_point( $order, $order->get_customer_id(), 0 );
					}
				}
			}

			return $order;
		}
		static function maybe_apply_point( $order, $request, $creating ) {
			if ( ! is_a( $order, 'WC_Order' ) ) {
				return $order;
			}

			if ( $order->get_user_id() > 0 ) {
				$params = $request->get_json_params();
				$point  = msps_get( $params, 'mshop_point', 0 );

				if ( msps_get( $params, 'mshop_point', 0 ) > 0 ) {
					$item = new WC_Order_Item_Fee();
					$item->set_props( array(
						'name'      => __( '포인트 할인', 'mshop-point-ex' ),
						'tax_class' => 0,
						'total'     => - 1 * $point,
						'total_tax' => 0,
						'taxes'     => array(),
						'order_id'  => $order->get_id(),
					) );
					$order->save();
					$order->add_item( $item );

					$order->calculate_totals();
					$order->save();

					$current_language = apply_filters( 'msps_get_current_language', mshop_wpml_get_current_language() );

					$user       = new MSPS_User( $order->get_user_id() );
					$prev_point = $user->get_point();

					if ( $point > 0 && $user->get_point() < $point ) {
						return new WP_Error( 'P1000', __( '보유하신 포인트가 부족합니다.', 'mshop-point-ex' ) );
					}

					if ( $point != 0 && 'yes' != $order->get_meta( '_mshop_point_purchase_processed' ) ) {
						$deduction_info = $user->wallet->get_deduction_info( $point );
						MSPS_Order::update_used_point( $order, $order->get_user_id(), $deduction_info );
						$user->wallet->deduct( $deduction_info );

						$remain_point = $user->get_point();
						$current_balance = $prev_point;
						foreach ( $deduction_info as $item_id => $amount ) {
							$current_balance -= $amount;
							MSPS_Log::add_log( $order->get_user_id(), msps_get_wallet_id( $item_id, null, $current_language ), 'deduct', 'purchase', - 1 * $amount, $current_balance, 'completed', $order->get_id(), '', msps_get_wallet_name( $user, $item_id ) );
						}

						$message = sprintf( __( '주문(<a href="%1$s">#%2$s</a>) 결제시 포인트 사용으로 %3$s포인트가 차감되었습니다.<br>보유포인트가 %4$s포인트에서 %5$s포인트로 변경되었습니다.', 'mshop-point-ex' ), get_edit_post_link( $order->get_id() ), $order->get_id(), number_format( floatval( $point ), wc_get_price_decimals() ), number_format( $prev_point, wc_get_price_decimals() ), number_format( $remain_point, wc_get_price_decimals() ) );
						$order->add_order_note( __( '[포인트 알림] ', 'mshop-point-ex' ) . $message );
					}
				}

				MSPS_Order::woocommerce_checkout_order_processed( $order, null );
			}

			return $order;
		}
		static function maybe_update_customer_point( $customer, $request, $creating ) {
			if ( ! is_a( $customer, 'WP_User' ) ) {
				return $customer;
			}

			$params = $request->is_json_content_type() ? $request->get_json_params() : $request->get_body_params();

			$point_params = msps_get( $params, 'mshop_point' );

			if ( ! empty( $point_params ) ) {
				$action    = $point_params['action'];
				$wallet_id = $point_params['wallet_id'];
				$amount    = floatval( $point_params['amount'] );
				$language  = msps_get( $point_params, 'language' );

				$point_user = new MSPS_User( $customer );

				if ( 'earn' == $action ) {
					$remain_point = $point_user->earn_point( $amount, $wallet_id );
					MSPS_Log::add_log( $customer->ID, msps_get_wallet_id( $wallet_id, null, $language ), 'earn', 'admin', $amount, $remain_point, 'completed', 0, sprintf( __( '관리자에 의해 %s 포인트가 적립되었습니다.', 'mshop-point-ex' ), number_format( $amount ) ), msps_get_wallet_name( $point_user, $wallet_id ) );
				} else if ( 'deduct' == $action ) {
					$remain_point = $point_user->deduct_point( $amount, $wallet_id );
					MSPS_Log::add_log( $customer->ID, msps_get_wallet_id( $wallet_id, null, $language ), 'deduct', 'admin', $amount, $remain_point, 'completed', 0, sprintf( __( '관리자에 의해 %s 포인트가 차감되었습니다.', 'mshop-point-ex' ), number_format( $amount ) ), msps_get_wallet_name( $point_user, $wallet_id ) );
				} else if ( 'set' == $action ) {
					$point_user->set_point( $amount, $wallet_id );
					MSPS_Log::add_log( $customer->ID, msps_get_wallet_id( $wallet_id, null, $language ), 'earn', 'admin', $amount, $amount, 'completed', 0, sprintf( __( '관리자에 의해 %s 포인트로 설정되었습니다.', 'mshop-point-ex' ), number_format( $amount ) ), msps_get_wallet_name( $point_user, $wallet_id ) );
				} else {
					throw new WC_REST_Exception( '2001', sprintf( __( '[%s] 지원하지 않는 액션입니다. 지원되는 액션은 earn, deduct, set 임니다.', 'mshop-point-ex' ), $action ) );
				}
			}

			return $customer;
		}
	}


	MSPS_Rest_Api::init();
}
