<?php

/*
=====================================================================================
                ﻿엠샵 주소 체크아웃 / Copyright 2015 by CodeM(c)
=====================================================================================

  [ 우커머스 버전 지원 안내 ]

   워드프레스 버전 : WordPress 4.3

   우커머스 버전 : WooCommerce 2.4


  [ 코드엠 플러그인 라이센스 규정 ]

   (주)코드엠에서 개발된 워드프레스  플러그인을 사용하시는 분들에게는 다음 사항에 대한 동의가 있는 것으로 간주합니다.

   1. 코드엠에서 개발한 워드프레스 우커머스용 ﻿엠샵 주소 체크아웃 플러그인의 저작권은 (주)코드엠에게 있습니다.
   
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

if ( ! class_exists( 'MSADDR_Checkout' ) ) {

	class MSADDR_Checkout {
		public static function checkout_process() {

			if ( is_callable( array( WC()->checkout(), 'get_checkout_fields' ) ) ) {
				$billing_field  = WC()->checkout()->get_checkout_fields( 'billing' );
				$shipping_field = WC()->checkout()->get_checkout_fields( 'shipping' );
			} else {
				$billing_field  = WC()->checkout()->checkout_fields['billing'];
				$shipping_field = WC()->checkout()->checkout_fields['shipping'];
			}

			$billing_country               = empty( $_POST['billing_country'] ) ? '' : $_POST['billing_country'];
			$mshop_billing_address_addr1   = empty( $_POST['mshop_billing_address-addr1'] ) ? '' : $_POST['mshop_billing_address-addr1'];
			$mshop_billing_address_addr2   = empty( $_POST['mshop_billing_address-addr2'] ) ? '' : $_POST['mshop_billing_address-addr2'];
			$mshop_billing_address_postnum = empty( $_POST['mshop_billing_address-postnum'] ) ? '' : $_POST['mshop_billing_address-postnum'];

			$shipping_country               = empty( $_POST['shipping_country'] ) ? '' : $_POST['shipping_country'];
			$mshop_shipping_address_addr1   = empty( $_POST['mshop_shipping_address-addr1'] ) ? '' : $_POST['mshop_shipping_address-addr1'];
			$mshop_shipping_address_addr2   = empty( $_POST['mshop_shipping_address-addr2'] ) ? '' : $_POST['mshop_shipping_address-addr2'];
			$mshop_shipping_address_postnum = empty( $_POST['mshop_shipping_address-postnum'] ) ? '' : $_POST['mshop_shipping_address-postnum'];

			if ( $billing_country == 'KR' && empty( $mshop_billing_address_addr1 ) && isset( $billing_field['mshop_billing_address'] ) ) {
				$msg = __( '<strong>청구지 기본주소</strong> ', 'mshop-address-ex' );
				wc_add_notice( sprintf( __( '%s is a required field.', 'woocommerce' ), $msg ), 'error' );
			}

			if ( 'yes' == get_option( 'msaddr_required_field_address2', 'no' ) && $billing_country == 'KR' && empty( $mshop_billing_address_addr2 ) && isset( $billing_field['mshop_billing_address'] ) ) {
				$msg = __( '<strong>청구지 상세주소</strong> ', 'mshop-address-ex' );
				wc_add_notice( sprintf( __( '%s is a required field.', 'woocommerce' ), $msg ), 'error' );
			}

			if ( $shipping_country == 'KR' && isset( $_POST['ship_to_different_address'] ) && empty( $mshop_shipping_address_addr1 ) && isset( $shipping_field['mshop_shipping_address'] ) ) {
				$msg = __( '<strong>배송지 기본주소</strong> ', 'mshop-address-ex' );
				wc_add_notice( sprintf( __( '%s is a required field.', 'woocommerce' ), $msg ), 'error' );
			}

			if ( 'yes' == get_option( 'msaddr_required_field_address2', 'no' ) && isset( $_POST['ship_to_different_address'] ) && $shipping_country == 'KR' && empty( $mshop_shipping_address_addr2 ) && isset( $shipping_field['mshop_shipping_address'] ) ) {
				$msg = __( '<strong>배송지 상세주소</strong> ', 'mshop-address-ex' );
				wc_add_notice( sprintf( __( '%s is a required field.', 'woocommerce' ), $msg ), 'error' );
			}

			if ( $billing_country == 'KR' && empty( $mshop_billing_address_postnum ) && isset( $billing_field['mshop_billing_address'] ) ) {
				$msg = __( '<strong>청구지 우편번호</strong>', 'mshop-address-ex' );
				wc_add_notice( sprintf( __( '%s is a required field.', 'woocommerce' ), $msg ), 'error' );
			}

			if ( $shipping_country == 'KR' && isset( $_POST['ship_to_different_address'] ) && empty( $mshop_shipping_address_postnum ) && isset( $shipping_field['mshop_shipping_address'] ) ) {
				$msg = __( '<strong>배송지 우편번호</strong>', 'mshop-address-ex' );
				wc_add_notice( sprintf( __( '%s is a required field.', 'woocommerce' ), $msg ), 'error' );
			}

			if ( is_user_logged_in() && msaddr_process_billing() ) {
				msaddr_update_user_address( get_current_user_id(), 'billing', $_POST );
			}

			if ( is_user_logged_in() && msaddr_process_shipping() ) {
				msaddr_update_user_address( get_current_user_id(), 'shipping', $_POST );
			}

		}
		public static function checkout_update_user_meta( $customer_id, $posted ) {
			if ( msaddr_process_billing() ) {
				msaddr_update_user_address( $customer_id, 'billing', $_POST );
			}

			if ( msaddr_process_shipping() ) {
				msaddr_update_user_address( $customer_id, 'shipping', $_POST );
			} else if ( msaddr_process_billing() && ( ! msaddr_shipping_enabled() || empty( $_POST['ship_to_different_address'] ) || false == $_POST['ship_to_different_address'] ) ) {
				$postcode = $_POST['mshop_billing_address-postnum'];
				$address1 = $_POST['mshop_billing_address-addr1'];
				$address2 = $_POST['mshop_billing_address-addr2'];

				update_user_meta( $customer_id, 'mshop_shipping_address-postnum', $postcode );
				update_user_meta( $customer_id, 'mshop_shipping_address-addr1', $address1 );
				update_user_meta( $customer_id, 'mshop_shipping_address-addr2', $address2 );

				update_user_meta( $customer_id, 'shipping_postcode', $postcode );
				update_user_meta( $customer_id, 'shipping_address_1', $address1 );
				update_user_meta( $customer_id, 'shipping_address_2', $address2 );
			}
		}

		public static function checkout_update_order_meta( $order_id, $posted, $order = null ) {
			if ( ! apply_filters( 'msaddr_checkout_update_order_meta', true, $order_id, $posted ) ) {
				return;
			}

			if ( is_null( $order ) ) {
				if ( is_a( $posted, 'WC_Order' ) ) {
					$order = $posted;
				} else {
					$order = wc_get_order( $order_id );
				}
			}

			if ( 'naverpay' == $order->get_payment_method() ) {
				return;
			}

			$billing_fields = array(
				'mshop_billing_address-postnum',
				'mshop_billing_address-addr1',
				'mshop_billing_address-addr2',
			);

			$shipping_fields = array(
				'mshop_billing_address-postnum' => 'mshop_shipping_address-postnum',
				'mshop_billing_address-addr1'   => 'mshop_shipping_address-addr1',
				'mshop_billing_address-addr2'   => 'mshop_shipping_address-addr2',
				'billing_email_kr'              => 'shipping_email',
				'billing_phone_kr'              => 'shipping_phone'
			);

			if ( msaddr_process_billing() ) {
				foreach ( $billing_fields as $key ) {
					msaddr_update_checkout_field_value( $order, $key, $_POST[ $key ] );
				}

				msaddr_update_checkout_field_value( $order, 'billing_postcode', $_POST['mshop_billing_address-postnum'] );
				msaddr_update_checkout_field_value( $order, 'billing_address_1', $_POST['mshop_billing_address-addr1'] );
				msaddr_update_checkout_field_value( $order, 'billing_address_2', $_POST['mshop_billing_address-addr2'] );
			}
			if ( msaddr_process_shipping() ) {
				foreach ( $shipping_fields as $key => $value ) {
					msaddr_update_checkout_field_value( $order, $value, $_POST[ $value ] );
				}

				msaddr_update_checkout_field_value( $order, 'shipping_postcode', $_POST['mshop_shipping_address-postnum'] );
				msaddr_update_checkout_field_value( $order, 'shipping_address_1', $_POST['mshop_shipping_address-addr1'] );
				msaddr_update_checkout_field_value( $order, 'shipping_address_2', $_POST['mshop_shipping_address-addr2'] );

			} else if ( msaddr_process_billing() && ( ! msaddr_shipping_enabled() || empty( $_POST['ship_to_different_address'] ) || false == $_POST['ship_to_different_address'] ) ) {
				msaddr_update_checkout_field_value( $order, 'mshop_shipping_address-postnum', $_POST['mshop_billing_address-postnum'] );
				msaddr_update_checkout_field_value( $order, 'mshop_shipping_address-addr1', $_POST['mshop_billing_address-addr1'] );
				msaddr_update_checkout_field_value( $order, 'mshop_shipping_address-addr2', $_POST['mshop_billing_address-addr2'] );
				msaddr_update_checkout_field_value( $order, 'shipping_email', $_POST['billing_email_kr'] );
				msaddr_update_checkout_field_value( $order, 'shipping_phone', $_POST['billing_phone_kr'] );
				msaddr_update_checkout_field_value( $order, 'shipping_country', $_POST['billing_country'] );

				msaddr_update_checkout_field_value( $order, 'shipping_postcode', $_POST['mshop_billing_address-postnum'] );
				msaddr_update_checkout_field_value( $order, 'shipping_address_1', $_POST['mshop_billing_address-addr1'] );
				msaddr_update_checkout_field_value( $order, 'shipping_address_2', $_POST['mshop_billing_address-addr2'] );
			}

			if ( is_callable( array( $order, 'save' ) ) ) {
				$order->save();
			}
		}

		public static function update_subscription_address( $order ) {
			$fields = array(
				'_mshop_billing_address-postnum',
				'_mshop_billing_address-addr1',
				'_mshop_billing_address-addr2',
				'_billing_first_name_kr',
				'_billing_email_kr',
				'_billing_phone_kr',
				'_mshop_shipping_address-postnum',
				'_mshop_shipping_address-addr1',
				'_mshop_shipping_address-addr2',
				'_shipping_first_name_kr',
				'_shipping_email',
			);

			$switch_order_data = wcs_get_objects_property( $order, 'subscription_switch_data' );

			if ( empty( $switch_order_data ) || ! is_array( $switch_order_data ) ) {
				return;
			}

			foreach ( $switch_order_data as $subscription_id => $switch_data ) {
				$subscription = wcs_get_subscription( $subscription_id );

				if ( ! $subscription instanceof WC_Subscription ) {
					continue;
				}

				foreach ( $fields as $field ) {
					$subscription->update_meta_data( $field, $order->get_meta( $field ) );
				}

				$subscription->save_meta_data();
			}
		}
		public static function maybe_adjust_checkout_post_data( $data ) {
			if ( msaddr_process_billing() ) {
				$data['mshop_billing_address-postnum'] = $_POST['mshop_billing_address-postnum'];
				$data['mshop_billing_address-addr1']   = $_POST['mshop_billing_address-addr1'];
				$data['mshop_billing_address-addr2']   = $_POST['mshop_billing_address-addr2'];
				$data['billing_postcode']              = $_POST['mshop_billing_address-postnum'];
				$data['billing_address_1']             = $_POST['mshop_billing_address-addr1'];
				$data['billing_address_2']             = $_POST['mshop_billing_address-addr2'];
			}

			if ( msaddr_process_shipping() ) {
				$data['mshop_shipping_address-postnum'] = $_POST['mshop_shipping_address-postnum'];
				$data['mshop_shipping_address-addr1']   = $_POST['mshop_shipping_address-addr1'];
				$data['mshop_shipping_address-addr2']   = $_POST['mshop_shipping_address-addr2'];
				$data['shipping_postcode']              = $_POST['mshop_shipping_address-postnum'];
				$data['shipping_address_1']             = $_POST['mshop_shipping_address-addr1'];
				$data['shipping_address_2']             = $_POST['mshop_shipping_address-addr2'];
			}

			return $data;
		}
		public static function maybe_update_subscription_address_data( $customer_id, $checkout_data ) {
			if ( function_exists( 'wcs_cart_contains_renewal' ) ) {
				$cart_renewal_item = wcs_cart_contains_renewal();

				if ( false !== $cart_renewal_item && is_array( $cart_renewal_item ) ) {
					$subscription = wcs_get_subscription( $cart_renewal_item['subscription_renewal']['subscription_id'] );

					if ( $subscription ) {
						self::checkout_update_order_meta( $subscription->get_id(), $checkout_data );
					}
				}
			}
		}
	}
}