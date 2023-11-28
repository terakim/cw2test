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

if ( ! class_exists( 'MSADDR_Fields' ) ) {

	class MSADDR_Fields {
		public static function billing_fields( $address, $country ) {
			if ( ! msaddr_enabled() ) {
				return $address;
			}

			$required = true;

			$addr_post_action    = '';
			$addr_request_action = '';
			$addr_ajax_action    = '';

			if ( isset( $_POST['action'] ) ) {
				$addr_post_action = ! empty( $_POST['action'] ) ? $_POST['action'] : '';
			}

			if ( isset( $_REQUEST['action'] ) ) {
				$addr_request_action = ! empty( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';
			}

			if ( isset( $_REQUEST['wc-ajax'] ) ) {
				$addr_ajax_action = ! empty( $_REQUEST['wc-ajax'] ) ? $_REQUEST['wc-ajax'] : '';
			}

			if ( ( $addr_post_action == 'edit_address' || $addr_request_action == 'woocommerce_checkout' || in_array( $addr_ajax_action, apply_filters( 'msaddr_checkout_ajax_action', array( 'checkout', 'wc_ppec_start_checkout' ) ) ) ) ) {
				if ( $country == "KR" ) {
					foreach ( $address as $key => $value ) {
						$address[ $key ]['required'] = false;
					}
				} else {
					$required = false;
				}
			}

			$kr_address = array();

			$kr_address['billing_first_name_kr'] = array_merge( $address['billing_first_name'], array(
				'clear'       => true,
				'placeholder' => get_option( 'mshop_address_placeholder_firstname', __( '이름', 'mshop-address-ex' ) ),
				'class'       => array( 'form-row-first', 'mshop_addr_title', 'mshop-enable-kr' ),
				'required'    => $required,
				'priority'    => '50'
			) );

			$kr_address['mshop_billing_address'] = array(
				'id'       => 'mshop_billing_address',
				'label'    => get_option( 'mshop_address_label_text', __( '주소', 'mshop-address-ex' ) ),
				'type'     => 'mshop_address',
				'title'    => __( 'Address', 'mshop-address-ex' ),
				'class'    => array( 'validate-required', 'mshop_addr_title', 'mshop-enable-kr' ),
				'priority' => '60'
			);

			$kr_address['billing_email_kr'] = array_merge( $address['billing_email'], array(
				'placeholder' => get_option( 'mshop_address_placeholder_email', __( '이메일', 'mshop-address-ex' ) ),
				'class'       => array( 'form-row-first', 'mshop_addr_title', 'mshop-enable-kr' ),
				'required'    => $required,
				'priority'    => '70'
			) );

			$kr_address['billing_phone_kr'] = array_merge( $address['billing_phone'], array(
				'clear'       => true,
				'placeholder' => get_option( 'mshop_address_placeholder_phone', __( '전화번호', 'mshop-address-ex' ) ),
				'class'       => array( 'form-row-last', 'mshop_addr_title', 'mshop-enable-kr' ),
				'required'    => $required,
				'priority'    => '80'
			) );
			$address                        = array_merge( $kr_address, $address );

			return apply_filters( 'msaddr_billing_fields', $address, $country );
		}
		public static function shipping_fields( $address, $country ) {
			if ( ! msaddr_enabled() ) {
				return $address;
			}

			$required = true;

			$addr_post_action    = '';
			$addr_request_action = '';
			$addr_ajax_action    = '';

			if ( isset( $_POST['action'] ) ) {
				$addr_post_action = ! empty( $_POST['action'] ) ? $_POST['action'] : '';
			}

			if ( isset( $_REQUEST['action'] ) ) {
				$addr_request_action = ! empty( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';
			}

			if ( isset( $_REQUEST['wc-ajax'] ) ) {
				$addr_ajax_action = ! empty( $_REQUEST['wc-ajax'] ) ? $_REQUEST['wc-ajax'] : '';
			}

			if ( ( $addr_post_action == 'edit_address' || $addr_request_action == 'woocommerce_checkout' || $addr_ajax_action == 'checkout' ) ) {
				if ( $country == "KR" ) {
					foreach ( $address as $key => $value ) {
						$address[ $key ]['required'] = false;
					}
				} else {
					$required = false;
				}
			}

			$kr_address = array();

			$kr_address['shipping_first_name_kr'] = array_merge( $address['shipping_first_name'], array(
				'clear'       => true,
				'placeholder' => get_option( 'mshop_address_placeholder_firstname', __( '이름', 'mshop-address-ex' ) ),
				'class'       => array( 'form-row-first', 'mshop_addr_title', 'mshop-enable-kr' ),
				'required'    => $required,
				'priority'    => '50'
			) );

			$kr_address['mshop_shipping_address'] = array(
				'id'       => 'mshop_shipping_address',
				'label'    => get_option( 'mshop_address_label_text', __( '주소', 'mshop-address-ex' ) ),
				'type'     => 'mshop_address',
				'title'    => __( 'Address', 'mshop-address-ex' ),
				'class'    => array( 'validate-required', 'mshop_addr_title', 'mshop-enable-kr' ),
				'priority' => '60'
			);
			$kr_address['shipping_email']         = array(
				'label'       => __( 'Email Address', 'mshop-address-ex' ),
				'type'        => 'text',
				'required'    => $required,
				'class'       => array( 'form-row-first', 'mshop_addr_title', 'mshop-enable-kr' ),
				'placeholder' => get_option( 'mshop_address_placeholder_email', '이메일' ),
				'validate'    => array( 'email' ),
				'priority'    => '70'
			);
			$kr_address['shipping_phone']         = array(
				'label'       => __( 'Phone', 'mshop-address-ex' ),
				'type'        => 'text',
				'required'    => $required,
				'class'       => array( 'form-row-last', 'mshop_addr_title', 'mshop-enable-kr' ),
				'placeholder' => get_option( 'mshop_address_placeholder_phone', '전화번호' ),
				'clear'       => true,
				'priority'    => '80'
			);
			$address                              = array_merge( $kr_address, $address );

			return apply_filters( 'msaddr_shipping_fields', $address, $country );
		}

		public static function admin_billing_fields( $fields ) {
			if ( MSADDR_HPOS::enabled() ) {
				$order = MSADDR_HPOS::get_order( absint( $_GET['id'] ) );
			} else {
				$order = MSADDR_HPOS::get_order( get_the_ID() );
			}

			$country = $order->get_billing_country();

			if ( msaddr_enabled() && ( empty( $country ) || 'KR' == $country ) ) {
				return array(
					'country'    => $fields['country'],
					'first_name' => array(
						'label' => __( 'First Name', 'mshop-address-ex' ),
						'show'  => false
					),
					'email'      => array(
						'label' => __( 'Email', 'mshop-address-ex' ),
					),
					'phone'      => array(
						'label' => __( 'Phone', 'mshop-address-ex' ),
					),
				);
			} else {
				return $fields;
			}
		}

		public static function admin_shipping_fields( $fields ) {
			if ( MSADDR_HPOS::enabled() ) {
				$order = MSADDR_HPOS::get_order( absint( $_GET['id'] ) );
			} else {
				$order = MSADDR_HPOS::get_order( get_the_ID() );
			}

			$country = $order->get_shipping_country();

			if ( msaddr_enabled() && ( empty( $country ) || 'KR' == $country ) ) {
				return array(
					'country'    => $fields['country'],
					'first_name' => array(
						'label' => __( 'First Name', 'mshop-address-ex' ),
						'show'  => false
					),
					'email'      => array(
						'label' => __( 'Email', 'mshop-address-ex' ),
					),
					'phone'      => array(
						'label' => __( 'Phone', 'mshop-address-ex' ),
					),
				);
			} else {
				return array_merge( $fields, array(
					'email' => array(
						'label' => __( 'Email', 'mshop-address-ex' ),
					),
					'phone' => array(
						'label' => __( 'Phone', 'mshop-address-ex' ),
					),
				) );
			}
		}
		public static function output_multiselect_fields( $field, $key, $args, $value ) {
			$options = '';

			if ( ! empty( $args['options'] ) ) {
				$label_id          = $args['id'];
				$sort              = $args['priority'] ? $args['priority'] : '';
				$custom_attributes = array();
				$selected_values = array_map( 'trim', explode( ',', $value ) );
				$field_container = '<p class="form-row %1$s" id="%2$s" data-priority="' . esc_attr( $sort ) . '">%3$s</p>';

				if ( $args['required'] ) {
					$args['class'][] = 'validate-required';
					$required        = '&nbsp;<abbr class="required" title="' . esc_attr__( 'required', 'woocommerce' ) . '">*</abbr>';
				} else {
					$required = '&nbsp;<span class="optional">(' . esc_html__( 'optional', 'woocommerce' ) . ')</span>';
				}

				foreach ( $args['options'] as $option_key => $option_text ) {
					if ( '' === $option_key ) {
						// If we have a blank option, select2 needs a placeholder.
						if ( empty( $args['placeholder'] ) ) {
							$args['placeholder'] = $option_text ? $option_text : __( 'Choose an option', 'woocommerce' );
						}
						$custom_attributes[] = 'data-allow_clear="true"';
					}
					$options .= '<option value="' . esc_attr( $option_key ) . '" ' . ( in_array( $option_key, $selected_values ) ? 'selected' : '' ) . '>' . esc_html( $option_text ) . '</option>';
				}

				$field .= '<select name="' . esc_attr( $key ) . '[]" id="' . esc_attr( $args['id'] ) . '" class="select msaddr-multiselect ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" ' . implode( ' ', $custom_attributes ) . ' data-placeholder="' . esc_attr( $args['placeholder'] ) . '" multiple="multiple">
							' . $options . '
						</select>';

				$field_html = '';

				if ( $args['label'] ) {
					$field_html .= '<label for="' . esc_attr( $label_id ) . '" class="' . esc_attr( implode( ' ', $args['label_class'] ) ) . '">' . wp_kses_post( $args['label'] ) . $required . '</label>';
				}

				$field_html .= '<span class="woocommerce-input-wrapper">' . $field;

				if ( $args['description'] ) {
					$field_html .= '<span class="description" id="' . esc_attr( $args['id'] ) . '-description" aria-hidden="true">' . wp_kses_post( $args['description'] ) . '</span>';
				}

				$field_html .= '</span>';

				$container_class = esc_attr( implode( ' ', $args['class'] ) );
				$container_id    = esc_attr( $args['id'] ) . '_field';
				$field           = sprintf( $field_container, $container_class, $container_id, $field_html );
			}

			return $field;
		}
		public static function output_address_fields( $field, $key, $args, $value ) {
			return self::output_user_address_fields( get_current_user_id(), $args );
		}

		public static function output_user_address_fields( $userid, $args ) {

			$allow_custom = 'yes' == get_option( 'mshop_address_user_can_write_address', 'no' );

			if ( ! isset( $args['class'] ) ) {
				$args['class'] = array();
			}

			wp_deregister_script( 'selectBox' );

			if ( ! empty( $args['order'] ) && is_a( $args['order'], 'WC_Order' ) ) {
				$order = $args['order'];
			} else if ( isset( $_GET['subscription'] ) ) {
				$order = wcs_get_subscription( absint( $_GET['subscription'] ) );
			}

			if ( ! empty( $order ) ) {
				$postnum = $order->get_meta( '_' . esc_attr( $args['id'] ) . '-postnum' );
				$addr1   = $order->get_meta( '_' . esc_attr( $args['id'] ) . '-addr1' );
				$addr2   = $order->get_meta( '_' . esc_attr( $args['id'] ) . '-addr2' );
			} else {
				$postnum = get_user_meta( $userid, esc_attr( $args['id'] ) . '-postnum', true );
				$addr1   = get_user_meta( $userid, esc_attr( $args['id'] ) . '-addr1', true );
				$addr2   = get_user_meta( $userid, esc_attr( $args['id'] ) . '-addr2', true );
			}
			$args['class'] = array_diff( $args['class'], array( 'form-row-first', 'form-row-last', 'form-row-wide' ) );
			if ( $args['required'] ) {
				$required = ' <abbr class="required" title="' . esc_attr__( 'required', 'woocommerce' ) . '">*</abbr>';
			} else {
				$required = '';
			}

			$fields = '<p class="form-row form-row-wide ' . esc_attr( implode( ' ', $args['class'] ) ) . '" id="' . esc_attr( $args["id"] ) . '-postnum_field">';
			$fields .= '<label for="' . esc_attr( $args["id"] ) . '-postnum" class="">' . $args['label'] . $required . '</label>';
			$fields .= '    <input type="text" class="input-text postnum" placeholder="' . get_option( 'mshop_address_placeholder_postnum', '우편번호' ) . '" id="' . esc_attr( $args["id"] ) . '-postnum" name="' . esc_attr( $args['id'] ) . '-postnum" value="' . $postnum . '" style="width:80px" ' . ( $allow_custom ? '' : 'readonly onfocus="this.blur();"' ) . '>';
			$fields .= '    <a href="#ms_addr_1" id="ms_addr_' . bin2hex( openssl_random_pseudo_bytes( 5 ) ) . '" type="button" class="ms_addr_1 ms-open-popup-link" data-id="' . esc_attr( $args["id"] ) . '"readonly="readonly" onfocus="this.blur();" value="' . get_option( 'mshop_address_search_button_text', '주소 검색' ) . '">' . get_option( 'mshop_address_search_button_text', '주소 검색' ) . '</a>';
			$fields .= '</p>';
			$fields .= '<p class="form-row form-row-wide ' . esc_attr( implode( ' ', $args['class'] ) ) . '" id="' . esc_attr( $args["id"] ) . '-addr1_field">';
			$fields .= '        <input type="text" class="input-text regular-text addr1" placeholder="' . get_option( 'mshop_address_placeholder_addr1', '기본주소' ) . '" id="' . esc_attr( $args["id"] ) . '-addr1" name="' . esc_attr( $args['id'] ) . '-addr1" value="' . $addr1 . '" ' . ( $allow_custom ? '' : 'readonly onfocus="this.blur();"' ) . '>';
			$fields .= '</p>';
			$fields .= '<p class="form-row form-row-wide ' . esc_attr( implode( ' ', $args['class'] ) ) . '" id="' . esc_attr( $args["id"] ) . '-addr2_field">';
			$fields .= '        <input type="text" class="input-text regular-text addr2" placeholder="' . get_option( 'mshop_address_placeholder_addr2', '상세주소' ) . '" id="' . esc_attr( $args["id"] ) . '-addr2" name="' . esc_attr( $args['id'] ) . '-addr2" value="' . $addr2 . '" ><br class="clear" />';
			$fields .= '</p>';

			return $fields;
		}
		public static function process_checkout_field( $value ) {
			$filter = current_filter();
			$key    = str_replace( 'woocommerce_process_checkout_field_', '', $filter );

			$loading = explode( '_', $key );
			$loading = current( $loading );

			if ( $_POST[ $loading . '_country' ] != 'KR' || empty( $_POST[ $key . '_kr' ] ) ) {
				return $value;
			} else {
				return $_POST[ $key . '_kr' ];
			}
		}

		public static function formatted_address_replacements( $replacements, $args ) {
			if ( $replacements['{country}'] == 'South Korea' ) {
				$replacements['{name}']      = $replacements['{first_name}'];
				$replacements['{last_name}'] = '';
				if ( ! empty( $replacements['{postcode}'] ) ) {
					$replacements['{address_1}'] = sprintf( '(%s) %s %s', $replacements['{postcode}'], $replacements['{address_1}'], $replacements['{address_2}'] );
					$replacements['{address_2}'] = '';
					$replacements['{postcode}']  = '';
					$replacements['{city}']      = '';
				} else {
					$replacements['{postcode}'] = '';
					$replacements['{city}']     = '';
				}
			}

			return $replacements;
		}
		public static function formatted_billing_address( $args, $order ) {
			if ( 'KR' == $order->get_billing_country() ) {
				$args['name'] = $args['first_name'];
				if ( ! empty( $args['postcode'] ) ) {
					$args['address_1'] = sprintf( '(%s) %s %s', $args['postcode'], $args['address_1'], $args['address_2'] );
					$args['last_name'] = '';
					$args['address_2'] = '';
					$args['postcode']  = '';
					$args['city']      = '';

					unset( $args['postcode'] );
				} else {
					$args['last_name'] = '';
					$args['postcode']  = '';
					$args['city']      = '';
				}
			}

			return $args;
		}
		public static function formatted_shipping_address( $args, $order ) {
			if ( 'KR' == $order->get_shipping_country() ) {
				$args['name'] = $args['first_name'];
				if ( ! empty( $args['postcode'] ) ) {
					$args['address_1']      = sprintf( '(%s) %s %s', $args['postcode'], $args['address_1'], $args['address_2'] );
					$args['last_name']      = '';
					$args['address_2']      = '';
					$args['postcode']       = '';
					$args['city']           = '';
					$args['shipping_email'] = $order->get_meta( '_shipping_email' );
					$args['shipping_phone'] = msaddr_get_shipping_phone( $order );
				} else {
					$args['last_name']      = '';
					$args['postcode']       = '';
					$args['city']           = '';
					$args['shipping_email'] = $order->get_meta( '_shipping_email' );
					$args['shipping_phone'] = msaddr_get_shipping_phone( $order );
				}

				if( is_a( $order, 'WC_Order' ) && is_callable( array( $order, 'get_shipping_phone')) && $order->get_shipping_phone() ) {
					unset( $args['shipping_phone'] );
				}
			}

			return $args;
		}

		public static function address_book_fields( $fields ) {

			$fields = array_merge(
				array_diff( $fields, array( 'mshop_address' ) ),
				array(
					'mshop_shipping_address-postnum',
					'mshop_shipping_address-addr1',
					'mshop_shipping_address-addr2',
				)
			);

			return $fields;

		}
	}
}