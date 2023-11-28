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

if ( ! class_exists( 'MSADDR_Myaccount' ) ) {

	class MSADDR_Myaccount {
		public static function add_address_book_menu( $items ) {
			return array_merge(
				$items,
				array(
					'address-book' => __( '배송지', 'mshop-address-ex' )
				)
			);
		}
		public static function output_address_book( $params = array() ) {
			if ( ! is_array( $params ) ) {
				$params = array();
			}

			$address_type = get_option( 'pafw_dc_address_book_type', 'billing' );

			MSADDR_DIY_Checkout::enqueue_script( true );
			wp_enqueue_style( 'diy-address-book', plugins_url( '/assets/css/diy-address-book.css', MSADDR_PLUGIN_FILE ), array(), MSADDR_VERSION );
			wp_localize_script( 'ms-address-search', '_msaddr_search', array(
				'form_field_display_style' => apply_filters( 'msaddr_form_field_display_style', 'flex' )
			) );

			wc_get_template( '/myaccount/style.php', array( 'params' => array( 'theme' => 'default' ) ), '', MSADDR()->template_path() );
			wc_get_template( '/myaccount/address-book.php', array( 'params' => array_merge( $params, array( 'tab_style' => 'radio', 'list_style' => 'block' ) ), 'address_type' => $address_type ), '', MSADDR()->template_path() );
			wc_get_template( 'mshop-address-search.php', array(), '', MSADDR()->template_path() );
		}
		public static function customer_save_billing_address( $user_id, $load_address ) {
			if ( msaddr_process_billing() ) {

				msaddr_update_user_address( $user_id, 'billing', $_POST );

				$fields = array_merge(
					MSADDR_Checkout_Fields::get_custom_fields( 'billing' )
				);

				self::update_custom_fields( $user_id, $fields );

				if ( $_POST['billing_country'] == 'KR' ) {
					update_user_meta( $user_id, 'first_name', $_POST['billing_first_name'] );
					update_user_meta( $user_id, 'last_name', '' );
					update_user_meta( $user_id, 'nickname', $_POST['billing_first_name'] );

					update_user_meta( $user_id, 'billing_first_name', $_POST['billing_first_name_kr'] );
					update_user_meta( $user_id, 'billing_last_name', '' );
					update_user_meta( $user_id, 'billing_email', $_POST['billing_email_kr'] );
					update_user_meta( $user_id, 'billing_phone', $_POST['billing_phone_kr'] );

					update_user_meta( $user_id, 'billing_first_name_kr', $_POST['billing_first_name_kr'] );
					update_user_meta( $user_id, 'billing_email_kr', $_POST['billing_email_kr'] );
					update_user_meta( $user_id, 'billing_phone_kr', $_POST['billing_phone_kr'] );
				} else {
					update_user_meta( $user_id, 'first_name', $_POST['billing_first_name'] );
					update_user_meta( $user_id, 'last_name', $_POST['billing_last_name'] );
					update_user_meta( $user_id, 'nickname', $_POST['billing_first_name'] );

					update_user_meta( $user_id, 'billing_first_name', $_POST['billing_first_name'] );
					update_user_meta( $user_id, 'billing_last_name', $_POST['billing_last_name'] );
					update_user_meta( $user_id, 'billing_email', $_POST['billing_email'] );
					update_user_meta( $user_id, 'billing_phone', $_POST['billing_phone'] );

					update_user_meta( $user_id, 'billing_first_name_kr', $_POST['billing_first_name'] );
					update_user_meta( $user_id, 'billing_email_kr', $_POST['billing_email'] );
					update_user_meta( $user_id, 'billing_phone_kr', $_POST['billing_phone'] );
					update_user_meta( $user_id, 'billing_postcode', $_POST['billing_postcode'] );

					update_user_meta( $user_id, 'mshop_billing_address-postnum', $_POST['billing_postcode'] );
					update_user_meta( $user_id, 'mshop_billing_address-addr1', $_POST['billing_address_1'] );
					update_user_meta( $user_id, 'mshop_billing_address-addr2', $_POST['billing_address_2'] );

					update_user_meta( $user_id, 'billing_address_1', $_POST['billing_address_1'] );
					update_user_meta( $user_id, 'billing_address_2', $_POST['billing_address_2'] );
				}
			}
		}
		public static function customer_save_shipping_address( $user_id, $load_address ) {
			if ( msaddr_process_shipping() || self::is_edit_address() ) {
				msaddr_update_user_address( $user_id, 'shipping', $_POST );

				$fields = array_merge(
					MSADDR_Checkout_Fields::get_custom_fields( 'shipping' )
				);

				self::update_custom_fields( $user_id, $fields );

				if ( $_POST['shipping_country'] == 'KR' ) {
					//한국인 경우 처리
					update_user_meta( $user_id, 'shipping_first_name', $_POST['shipping_first_name_kr'] );
					update_user_meta( $user_id, 'shipping_last_name', '' );
					update_user_meta( $user_id, 'shipping_email', $_POST['shipping_email'] );
					update_user_meta( $user_id, 'shipping_phone', $_POST['shipping_phone'] );

					update_user_meta( $user_id, 'shipping_first_name_kr', $_POST['shipping_first_name_kr'] );

				} else {
					//한국이 아닌 경우 처리
					update_user_meta( $user_id, 'shipping_first_name', $_POST['shipping_first_name'] );
					update_user_meta( $user_id, 'shipping_last_name', $_POST['shipping_last_name'] );
					update_user_meta( $user_id, 'shipping_email', $_POST['shipping_email'] );
					update_user_meta( $user_id, 'shipping_phone', $_POST['shipping_phone'] );
					update_user_meta( $user_id, 'shipping_postcode', $_POST['shipping_postcode'] );

					update_user_meta( $user_id, 'mshop_shipping_address-postnum', $_POST['shipping_postcode'] );
					update_user_meta( $user_id, 'mshop_shipping_address-addr1', $_POST['shipping_address_1'] );
					update_user_meta( $user_id, 'mshop_shipping_address-addr2', $_POST['shipping_address_2'] );

					update_user_meta( $user_id, 'shipping_address_1', $_POST['shipping_address_1'] );
					update_user_meta( $user_id, 'shipping_address_2', $_POST['shipping_address_2'] );

					update_user_meta( $user_id, 'shipping_first_name_kr', $_POST['shipping_first_name'] );
				}
			}
		}

		public static function update_custom_fields( $user_id, $fields ) {
			if ( ! msaddr_checkout_field_is_enabled() || empty( $fields ) ) {
				return;
			}

			$reserved = apply_filters( 'msaddr_reserved_fields', array(
				'mshop_billing_address',
				'mshop_shipping_address'
			) );

			foreach ( $fields as $key => $field ) {
				if ( ! in_array( $key, $reserved ) ) {
					if ( 'mshop_address' == $field['type'] ) {
						$postnum = $_POST[ $key . '-postnum' ];
						$addr1   = $_POST[ $key . '-addr1' ];
						$addr2   = $_POST[ $key . '-addr2' ];

						update_user_meta( $user_id, $key, sprintf( '(%s) %s %s', $postnum, $addr1, $addr2 ) );
						update_user_meta( $user_id, $key . '-postnum', $postnum );
						update_user_meta( $user_id, $key . '-addr1', $addr1 );
						update_user_meta( $user_id, $key . '-addr2', $addr2 );
					} else {
						update_user_meta( $user_id, $key, $_POST[ $key ] );
					}
				}
			}
		}
		public static function is_edit_address() {
			global $wp;

			return is_page( wc_get_page_id( 'myaccount' ) ) && isset( $wp->query_vars['edit-address'] );
		}
		public static function customer_save_address( $user_id, $load_address ) {
			$func = 'customer_save_' . $load_address . '_address';
			self::$func( $user_id, $load_address );
		}
		public static function hide_last_name_field() {
			global $wp;

			if ( msaddr_enabled() && is_page( wc_get_page_id( 'myaccount' ) ) && isset( $wp->query_vars['edit-account'] ) ) {
				?>
                <script type='text/javascript'>
                    jQuery('form input#account_last_name').closest('p').css('display', 'none');
                    jQuery('form input#account_last_name').val('-');
                </script>
				<?php
			}
		}
		public static function save_account_details( $user_id ) {
			if ( msaddr_enabled() ) {
				$user = get_user_by( 'id', $user_id );

				if ( $user->user_lastname == 'MSHOP_USER' || $user->user_lastname == '-' ) {
					update_user_meta( $user_id, 'last_name', '' );
				}
			}
		}
		public static function edit_address_field_value( $value, $key, $load_address ) {
			if ( msaddr_enabled() && $key == 'billing_email_kr' && empty( $value ) ) {
				$user  = wp_get_current_user();
				$value = $user->user_email;
			}

			return $value;
		}
		static function woocommerce_found_customer_details( $customer_data, $user_id, $type_to_load ) {

			if ( msaddr_enabled() ) {
				if ( $customer_data[ $type_to_load . '_country' ] == 'KR' ) {
					$customer_data[ 'mshop_' . $type_to_load . '_address_postnum' ] = get_user_meta( $user_id, 'mshop_' . $type_to_load . '_address-postnum', true );
					$customer_data[ 'mshop_' . $type_to_load . '_address_addr1' ]   = get_user_meta( $user_id, 'mshop_' . $type_to_load . '_address-addr1', true );
					$customer_data[ 'mshop_' . $type_to_load . '_address_addr2' ]   = get_user_meta( $user_id, 'mshop_' . $type_to_load . '_address-addr2', true );
				}
			}

			return $customer_data;
		}
		static function woocommerce_ajax_get_customer_details( $data, $customer, $user_id ) {

			if ( msaddr_enabled() ) {
				$data['billing']['first_name_kr']         = get_user_meta( $user_id, 'billing_first_name_kr', true );
				$data['billing']['last_name_kr']          = get_user_meta( $user_id, 'billing_last_name_kr', true );
				$data['billing']['email_kr']              = get_user_meta( $user_id, 'billing_email_kr', true );
				$data['billing']['phone_kr']              = get_user_meta( $user_id, 'billing_phone_kr', true );
				$data['billing']['mshop_address-postnum'] = get_user_meta( $user_id, 'mshop_billing_address-postnum', true );
				$data['billing']['mshop_address-addr1']   = get_user_meta( $user_id, 'mshop_billing_address-addr1', true );
				$data['billing']['mshop_address-addr2']   = get_user_meta( $user_id, 'mshop_billing_address-addr2', true );

				$data['shipping']['first_name_kr']         = get_user_meta( $user_id, 'shipping_first_name_kr', true );
				$data['shipping']['last_name_kr']          = get_user_meta( $user_id, 'shipping_last_name_kr', true );
				$data['shipping']['email_kr']              = get_user_meta( $user_id, 'shipping_email', true );
				$data['shipping']['phone_kr']              = get_user_meta( $user_id, 'shipping_phone', true );
				$data['shipping']['mshop_address-postnum'] = get_user_meta( $user_id, 'mshop_shipping_address-postnum', true );
				$data['shipping']['mshop_address-addr1']   = get_user_meta( $user_id, 'mshop_shipping_address-addr1', true );
				$data['shipping']['mshop_address-addr2']   = get_user_meta( $user_id, 'mshop_shipping_address-addr2', true );
			}

			return $data;
		}

		static function maybe_populate_subscription_addresses( $address ) {
			if ( isset( $_GET['subscription'] ) ) {
				$subscription = wcs_get_subscription( absint( $_GET['subscription'] ) );

				$field_keys = array(
					'shipping_first_name_kr',
					'shipping_last_name_kr',
					'shipping_email',
					'billing_first_name_kr',
					'billing_last_name_kr',
					'billing_email_kr',
					'billing_phone_kr',
				);

				foreach ( $field_keys as $key ) {
					if ( isset( $address[ $key ] ) ) {
						$address[ $key ]['value'] = $subscription->get_meta( '_' . $key );
					}
				}
			}

			return $address;
		}
	}
}