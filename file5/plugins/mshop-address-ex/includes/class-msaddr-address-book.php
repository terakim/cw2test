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

if ( ! class_exists( 'MSADDR_Address_Book' ) ) {

	class MSADDR_Address_Book {
		static $_is_enabled = null;
		static $_shipping_fields = null;
		static $_billing_fields = null;
		static $_possible_shipping_adress_edit_status = null;
		public static function is_enabled() {
			if ( is_null( self::$_is_enabled ) ) {
				self::$_is_enabled = 'yes' == get_option( 'mshop_address_use_shipping_adress_book', 'no' );
			}

			return self::$_is_enabled;
		}
		public static function get_possible_shipping_adress_edit_status() {
			if ( is_null( self::$_possible_shipping_adress_edit_status ) ) {
				self::$_possible_shipping_adress_edit_status = array();

				if ( self::is_enabled() ) {
					self::$_possible_shipping_adress_edit_status = explode( ',', get_option( 'mshop_address_possible_shipping_adress_edit_status', 'on-hold,order-received' ) );
				}

			}

			return self::$_possible_shipping_adress_edit_status;
		}
		public static function can_edit_shipping_address( $order ) {
			$possible_statuses = self::get_possible_shipping_adress_edit_status();

			return in_array( $order->get_status(), $possible_statuses );
		}
		public static function woocommerce_locate_template( $template, $template_name, $template_path ) {
			if ( self::is_enabled() ) {
				if ( strpos( $template_name, "checkout/form-shipping" ) === 0 ) {
					return apply_filters( 'msaddr_locate_template', MSADDR()->plugin_path() . '/templates/checkout/form-msaddr-shipping.php', $template_name );
				}
			}

			return $template;
		}

		public static function get_shipping_fields() {
			if ( is_null( self::$_shipping_fields ) ) {
				$fields = WC()->countries->get_address_fields( '', 'shipping_' );

				self::$_shipping_fields = array();

				foreach ( $fields as $key => $field ) {
					if ( 'mshop_address' == msaddr_get( $field, 'type' ) ) {
						self::$_shipping_fields[] = $key . '-postnum';
						self::$_shipping_fields[] = $key . '-addr1';
						self::$_shipping_fields[] = $key . '-addr2';
					} else {
						self::$_shipping_fields[] = $key;
					}
				}
			}

			return self::$_shipping_fields;
		}

		public static function get_billing_fields() {
			if ( is_null( self::$_billing_fields ) ) {
				$fields = WC()->countries->get_address_fields();

				self::$_billing_fields = array();

				foreach ( $fields as $key => $field ) {
					if ( 'mshop_address' == msaddr_get( $field, 'type' ) ) {
						self::$_billing_fields[] = $key . '-postnum';
						self::$_billing_fields[] = $key . '-addr1';
						self::$_billing_fields[] = $key . '-addr2';
					} else {
						self::$_billing_fields[] = $key;
					}
				}
			}

			return self::$_billing_fields;
		}

		public static function woocommerce_checkout_order_processed( $order_id, $posted ) {
			if ( self::is_enabled() && apply_filters( 'msaddr_process_append_address_book', true, $order_id ) ) {
				$order          = wc_get_order( $order_id );
				$shipping_infos = array();

				foreach ( self::get_shipping_fields() as $field ) {
					$shipping_infos[ $field ] = msaddr_get_checkout_field_value( $order, $field );
				}

				$key = md5( json_encode( $shipping_infos ) );

				$options = get_user_meta( $order->get_customer_id(), '_msaddr_shipping_history', true );

				if ( empty( $options ) ) {
					$options = array();
				}

				if ( ! isset( $options[ $key ] ) ) {
					$options = array_merge(
						array( $key => $shipping_infos ),
						array_slice( $options, 0, ( get_option( 'mshop_address_shipping_adress_book_count', '3' ) - 1 ), true )
					);

					update_user_meta( $order->get_customer_id(), '_msaddr_shipping_history', $options );
				}
			}
		}

		public static function localisation_address_formats( $formats ) {
			if ( apply_filters( 'msaddr_is_address_book', false ) ) {
				$formats['KR'] = "<div class='shipping-info'><p class='name'>{name}</p><p class='phone'><span class='phone'>{phone}</span><span class='email'>{email}</span></p><p class='address'>({postcode}) {address_1}{address_2}{city}{state}{country}</p></div>";
			} else if ( ! is_admin() ) {
				$formats['KR'] = "{name}\n{company}\n{address_1}\n{address_2}\n{city}\n{state}\n{postcode}\n{country}\n{shipping_phone}\n{shipping_email}";
			}

			return $formats;
		}

		public static function formatted_address_replacements( $replacements, $args ) {
			$phone = ! empty( $args['shipping_phone'] ) ? $args['shipping_phone'] : '';
			$email = ! empty( $args['shipping_email'] ) ? $args['shipping_email'] : '';

			if ( empty ( $phone ) ) {
				$phone = ! empty( $args['billing_phone'] ) ? $args['billing_phone'] : '';
			}

			if ( empty ( $email ) ) {
				$email = ! empty( $args['billing_email'] ) ? $args['billing_email'] : '';
			}

			$replacements['{shipping_phone}'] = $phone;
			$replacements['{phone}']          = $phone;
			$replacements['{shipping_email}'] = $email;
			$replacements['{email}']          = $email;

			return $replacements;
		}

		public static function get_formatted_address( $shipping ) {
			$address = apply_filters( 'msaddr_shipping_address_book_formatted_address', array(
				'first_name'     => $shipping['shipping_first_name'],
				'last_name'      => $shipping['shipping_last_name'],
				'company'        => $shipping['shipping_company'],
				'address_1'      => $shipping['shipping_address_1'],
				'address_2'      => $shipping['shipping_address_2'],
				'city'           => $shipping['shipping_city'],
				'state'          => $shipping['shipping_state'],
				'postcode'       => $shipping['shipping_postcode'],
				'country'        => $shipping['shipping_country'],
				'shipping_phone' => $shipping['shipping_phone'],
				'shipping_email' => $shipping['shipping_email'],
			), $shipping );

			add_filter( 'msaddr_is_address_book', '__return_true' );
			$formatted_address = WC()->countries->get_formatted_address( $address );
			remove_filter( 'msaddr_is_address_book', '__return_true' );

			return $formatted_address;
		}

		public static function get_formatted_billing_address( $billing ) {
			$address = apply_filters( 'msaddr_billing_address_book_formatted_address', array(
				'first_name'    => $billing['billing_first_name'],
				'last_name'     => $billing['billing_last_name'],
				'company'       => $billing['billing_company'],
				'address_1'     => $billing['billing_address_1'],
				'address_2'     => $billing['billing_address_2'],
				'city'          => $billing['billing_city'],
				'state'         => $billing['billing_state'],
				'postcode'      => $billing['billing_postcode'],
				'country'       => $billing['billing_country'],
				'billing_phone' => $billing['billing_phone'],
				'billing_email' => $billing['billing_email'],
			), $billing );

			add_filter( 'msaddr_is_address_book', '__return_true' );
			$formatted_address = WC()->countries->get_formatted_address( $address );
			remove_filter( 'msaddr_is_address_book', '__return_true' );

			return $formatted_address;
		}

		static function woocommerce_is_checkout( $is_checkout ) {
			return true;
		}

		public static function restore_field_enable_property( $enable, $field, $fieldset ) {
			return 'yes' == $field['enable'];
		}
		public static function edit_address_popup( $edit_address, $order ) {
			add_filter( 'woocommerce_is_checkout', __CLASS__ . '::woocommerce_is_checkout' );
			add_filter( 'msaddr_field_is_enabled', array( __CLASS__, 'restore_field_enable_property' ), 999, 3 );

			$country_field = 'get_' . $edit_address . '_country';

			$checkout_fields['shipping'] = WC()->countries->get_address_fields( $order->$country_field(), $edit_address . '_' );

			$checkout_fields['order'] = array(
				'order_comments' => array(
					'type'        => 'textarea',
					'class'       => array( 'notes' ),
					'label'       => __( 'Order Notes', 'mshop-address-ex' ),
					'placeholder' => _x( 'Notes about your order, e.g. special notes for delivery.', 'placeholder', 'mshop-address-ex' )
				)
			);

			$checkout_fields = apply_filters( 'woocommerce_checkout_fields', $checkout_fields );

			wp_enqueue_script( 'msaddr_edit_address', MSADDR()->plugin_url() . '/assets/js/mshop-edit-address.js', array( 'jquery', 'jquery-magnific-popup-address' ), MSADDR()->version );
			wp_localize_script( 'msaddr_edit_address', '_msaddr_edit_address', array(
				'ajaxurl'     => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
				'action'      => msaddr_ajax_command( 'update_address' ),
				'_ajax_nonce' => wp_create_nonce( 'update_address' )
			) );

			wc_get_template( 'mshop-address/edit-address-popup.php', array(
				'edit_address'    => $edit_address,
				'order'           => $order,
				'checkout_fields' => $checkout_fields
			), '', MSADDR()->template_path() );

			remove_filter( 'msaddr_field_is_enabled', array( __CLASS__, 'restore_field_enable_property' ), 999 );
			remove_filter( 'woocommerce_is_checkout', __CLASS__ . '::woocommerce_is_checkout' );

            WC()->countries->address_formats = array();
		}
		public static function woocommerce_order_details_after_customer_details( $order ) {
			if ( ! self::is_enabled() || ! self::can_edit_shipping_address( $order ) || $order->get_customer_id() <= 0 ) {
				return;
			}

			?>
            <tr>
                <th></th>
                <td><?php do_action( 'mshop_address_edit_address_popup', 'shipping', $order ); ?></td>
            </tr>
			<?php
		}
        public static function get_custom_values( $loading_address, $address_data ) {
	        $custom_values = array();

	        $custom_fields = MSADDR_Checkout_Fields::get_custom_fields( $loading_address );

	        if ( ! empty( $custom_fields ) ) {
		        foreach ( $custom_fields as $field_key => $field_value ) {
			        $custom_values[] = pafw_get( $address_data, $field_key );
		        }
	        }

            return  array_filter( $custom_values );
        }
	}

}