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

if ( ! class_exists( 'MSADDR_Checkout_Fields' ) ) {

	class MSADDR_Checkout_Fields {
		public static function set_address_field_value( $value ) {
			$filter = current_filter();
			$key    = str_replace( 'woocommerce_process_checkout_field_', '', $filter );

			if ( empty( $_REQUEST[ $key . '-postnum' ] ) || empty( $_REQUEST[ $key . '-addr1' ] ) ) {
				return $value;
			}

			if ( 'yes' == get_option( 'msaddr_required_field_address2', 'no' ) && empty( $_REQUEST[ $key . '-addr2' ] ) ) {
				return $value;
			}

			return sprintf( "(%s) %s", $_REQUEST[ $key . '-postnum' ], $_REQUEST[ $key . '-addr1' ] );
		}
		public static function set_address_field_value_for_edit_address( $value ) {
			$filter = current_filter();
			$key    = str_replace( 'woocommerce_process_myaccount_field_', '', $filter );

			if ( empty( $_REQUEST[ $key . '-postnum' ] ) || empty( $_REQUEST[ $key . '-addr1' ] ) ) {
				return $value;
			}

			if ( 'yes' == get_option( 'msaddr_required_field_address2', 'no' ) && empty( $_REQUEST[ $key . '-addr2' ] ) ) {
				return $value;
			}

			return sprintf( "(%s) %s", $_REQUEST[ $key . '-postnum' ], $_REQUEST[ $key . '-addr1' ] );
		}
		public static function load_address_fields( $fieldset, $address_fields, $country = '' ) {
			if ( msaddr_checkout_field_is_enabled( $fieldset ) ) {
				$fields = get_option( 'msaddr_' . $fieldset . '_fields' );

				if ( ! empty( $fields ) ) {
					$address_fields = array();

					$priority = 10;

					foreach ( $fields as $field ) {
						if ( apply_filters( 'msaddr_no_needs_shipping', ! is_order_received_page() && ! is_account_page() && is_checkout() && WC()->cart && ! WC()->cart->needs_shipping() ) ) {
							$shipping_fields = array(
								'billing_postcode',
								'billing_address_1',
								'billing_address_2',
								'billing_city',
								'billing_state',
								'shipping_postcode',
								'shipping_address_1',
								'shipping_address_2',
								'shipping_city',
								'shipping_state'
							);

							if ( 'mshop_address' == $field['type'] || in_array( $field['id'], $shipping_fields ) ) {
								continue;
							}
						}

						if ( apply_filters( 'msaddr_field_is_enabled', $field['enable'] == 'yes', $field, $fieldset ) ) {
							$required = ! empty( $field['required'] ) && 'yes' == $field['required'];
							if ( $required && ( ( 'KR' == $country && 'mshop-disable-kr' == $field['display'] ) || ( 'KR' != $country && 'mshop-enable-kr' == $field['display'] ) ) ) {
								$required = false;
							}

							if ( in_array( $field['id'], array( 'mshop_billing_address', 'mshop_shipping_address' ) ) ) {
								$required = false;
							} else if ( 'mshop_address' == $field['type'] ) {
								add_filter( 'woocommerce_process_checkout_field_' . $field['id'], array( __CLASS__, 'set_address_field_value' ) );
								add_filter( 'woocommerce_process_myaccount_field_' . $field['id'], array( __CLASS__, 'set_address_field_value_for_edit_address' ) );
							}

							$options = msaddr_get( $field, 'select_options', array() );

							if ( ! empty( $options ) ) {
								$options = array_combine( array_column( $options, 'key' ), array_column( $options, 'value' ) );
							}

							$address_fields[ $field['id'] ] = array(
								'label'       => __( msaddr_get( $field, 'label' ), 'woocommerce' ),
								'placeholder' => ! empty( $field['placeholder'] ) ? __( $field['placeholder'], 'woocommerce' ) : '',
								'type'        => $field['type'],
								'required'    => $required,
								'priority'    => $priority,
								'options'     => $options,
								'class'       => array_merge( array(
									! empty( $field['position'] ) ? $field['position'] : '',
									! empty( $field['display'] ) ? $field['display'] : '',
								), ! empty( $field['class'] ) ? explode( ',', $field['class'] ) : array() )
							);
						} else {
							if ( 'billing_country' == $field['id'] || 'shipping_country' == $field['id'] ) {
								$address_fields[ $field['id'] ] = array(
									'label'       => __( $field['label'], 'woocommerce' ),
									'placeholder' => ! empty( $field['placeholder'] ) ? __( $field['placeholder'], 'woocommerce' ) : '',
									'type'        => $field['type'],
									'required'    => false,
									'priority'    => $priority,
									'class'       => array_merge( array(
										'msaddr-hidden',
										! empty( $field['position'] ) ? $field['position'] : '',
										! empty( $field['display'] ) ? $field['display'] : '',
									), ! empty( $field['class'] ) ? explode( ',', $field['class'] ) : array() )
								);
							}
						}

						$priority += 10;
					}
				}
			}

			return $address_fields;
		}
		public static function billing_fields( $fields, $country ) {
			return self::load_address_fields( 'billing', $fields, $country );
		}
		public static function shipping_fields( $fields, $country ) {
			return self::load_address_fields( 'shipping', $fields, $country );
		}
		public static function checkout_fields( $fields ) {
			$fields['order'] = self::load_address_fields( 'order', $fields['order'] );

			return $fields;
		}

		public static function get_custom_fields( $fieldset ) {
			$custom_fields = array();

			if ( msaddr_checkout_field_is_enabled( $fieldset ) ) {
				if ( 'order' == $fieldset ) {
					if ( is_callable( array( WC()->checkout(), 'get_checkout_fields' ) ) ) {
						$fields = WC()->checkout()->get_checkout_fields( $fieldset );
					} else {
						$fields = WC()->checkout()->checkout_fields[ $fieldset ];
					}
					$custom_fields = array_diff_key( $fields, array( 'order_comments' => '' ) );
				} else {
					$fields = WC()->countries->get_address_fields( '', $fieldset . '_' );

					remove_filter( 'msaddr_' . $fieldset . '_fields', array( 'MSADDR_Checkout_Fields', $fieldset . '_fields' ), 999 );
					$wc_fields = WC()->countries->get_address_fields( '', $fieldset . '_' );
					add_filter( 'msaddr_' . $fieldset . '_fields', array( 'MSADDR_Checkout_Fields', $fieldset . '_fields' ), 999, 2 );

					$custom_fields = array_diff_key( $fields, $wc_fields );
				}
			}

			return $custom_fields;
		}
		public static function woocommerce_order_details_after_customer_details( $order ) {
			if ( ! msaddr_checkout_field_is_enabled() ) {
				return;
			}

			$reserved = apply_filters( 'msaddr_reserved_fields', array(
				'mshop_billing_address',
				'mshop_shipping_address'
			) );

			$fields = $order->get_meta( '_msaddr_custom_fields' );

			if ( ! empty( $fields ) ) {
				wc_get_template( 'checkout/customer_details_custom_fields.php', array(
					'order'    => $order,
					'fields'   => $fields,
					'reserved' => $reserved
				), '', MSADDR()->template_path() );

			}

		}
		public static function checkout_update_order_meta( $order_id, $posted ) {
			if ( ! msaddr_checkout_field_is_enabled() ) {
				return;
			}

			$order = wc_get_order( $order_id );

			$reserved = apply_filters( 'msaddr_reserved_fields', array(
				'mshop_billing_address',
				'mshop_shipping_address'
			) );

			$fields = array_merge(
				self::get_custom_fields( 'billing' ),
				self::get_custom_fields( 'shipping' ),
				self::get_custom_fields( 'order' )
			);

			$order_field_keys = array_keys( self::get_custom_fields( 'order' ) );

			if ( ! empty( $fields ) ) {
				$order->update_meta_data( '_msaddr_custom_fields', $fields );

				foreach ( $fields as $key => $field ) {
					if ( ! in_array( $key, $reserved ) ) {
						if ( 'mshop_address' == $field['type'] ) {
							$postnum = $_POST[ $key . '-postnum' ];
							$addr1   = $_POST[ $key . '-addr1' ];
							$addr2   = $_POST[ $key . '-addr2' ];

							$order->update_meta_data( '_' . $key, sprintf( '(%s) %s %s', $postnum, $addr1, $addr2 ) );
							$order->update_meta_data( '_' . $key . '-postnum', $postnum );
							$order->update_meta_data( '_' . $key . '-addr1', $addr1 );
							$order->update_meta_data( '_' . $key . '-addr2', $addr2 );

							update_user_meta( $order->get_customer_id(), $key, sprintf( '(%s) %s %s', $postnum, $addr1, $addr2 ) );
							update_user_meta( $order->get_customer_id(), $key . '-postnum', $postnum );
							update_user_meta( $order->get_customer_id(), $key . '-addr1', $addr1 );
							update_user_meta( $order->get_customer_id(), $key . '-addr2', $addr2 );
						} else {
							$order->update_meta_data( '_' . $key, $posted[ $key ] );
							if ( $order->get_customer_id() ) {
								if ( ! in_array( $key, $order_field_keys ) || 'yes' == get_option( 'msaddr_save_order_fields', 'yes' ) ) {
									update_user_meta( $order->get_customer_id(), $key, $posted[ $key ] );
								} else {
									delete_user_meta( $order->get_customer_id(), $key );
								}
							}
						}
					}
				}

				if ( is_callable( array( $order, 'save_meta_data' ) ) ) {
					$order->save_meta_data();
				}
			}
		}

	}
}
