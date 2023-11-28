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
	exit; // Exit if accessed directly
}

class MSADDR_Meta_Box_Order_Data {

	protected static $props = array();
	public static function add_meta_boxes( $post_type, $post ) {
		$order = MSADDR_HPOS::get_order( $post );

		if ( is_a( $order, 'WC_Order' ) ) {
			$fields = $order->get_meta( '_msaddr_custom_fields' );

			if ( ! empty( $fields ) ) {
				add_meta_box(
					'msaddr-custom-fields',
					__( '커스텀 필드', 'mshop-address-ex' ),
					array( __CLASS__, 'output_custom_metabox' ),
					MSADDR_HPOS::get_shop_order_screen(),
					'side'
				);
			}
		}
	}
	public static function output_custom_metabox( $post ) {
		$order = wc_get_order( $post->ID );

		$reserved = apply_filters( 'msaddr_reserved_fields', array(
			'mshop_billing_address',
			'mshop_shipping_address'
		) );

		$fields = $order->get_meta( '_msaddr_custom_fields' );

		if ( ! empty( $fields ) ) {
			foreach ( $fields as $key => $field ) {
				if ( ! in_array( $key, $reserved ) ) {
					$value = msaddr_get_checkout_field_value( $order, $key );

					echo sprintf( '<p><span  style="font-weight: bold;">%s</span> : %s</p>', $field['label'], apply_filters( 'msaddr_custom_field_value', $value, $key, $field, $order ) );
				}
			}
		}
	}
	public static function order_data_after_billing_address( $order ) {
		$country = $order->get_billing_country();

		if ( msaddr_enabled() && ( empty( $country ) || 'KR' == $country ) ) {
			$address = array(
				'id'    => 'mshop_billing_address',
				'label' => __( 'Address', 'mshop-address-ex' ),
				'type'  => 'mshop_address'
			);
			echo '<div class="edit_address msaddr-edit-address">';

			self::echo_form_field_mshop_address( $order, $address );

			woocommerce_wp_hidden_input( $address );
			echo '</div>';
		}
	}
	public static function order_data_after_shipping_address( $order ) {
		$country = $order->get_shipping_country();

		if ( msaddr_enabled() && ( empty( $country ) || 'KR' == $country ) ) {
			$address = array(
				'id'    => 'mshop_shipping_address',
				'label' => __( 'Address', 'mshop-address-ex' ),
				'type'  => 'mshop_address'
			);
			echo '<div class="edit_address msaddr-edit-address">';

			self::echo_form_field_mshop_address( $order, $address );

			woocommerce_wp_hidden_input( $address );
			echo '</div>';
		}
	}
	public static function echo_form_field_mshop_address( $order, $value ) {

		$allow_custom = 'yes' == get_option( 'mshop_address_user_can_write_address', 'no' ) ? true : false;

		if ( empty( $value['class'] ) ) {
			$value['class'] = array();
		}

		$postnum = $order->get_meta( '_' . esc_attr( $value['id'] ) . '-postnum' );
		$addr1   = $order->get_meta( '_' . esc_attr( $value['id'] ) . '-addr1' );
		$addr2   = $order->get_meta( '_' . esc_attr( $value['id'] ) . '-addr2' );
		$fields  = '';

		$fields .= '<p class="form-row form-row-wide ' . esc_attr( implode( ' ', $value['class'] ) ) . '" id="' . esc_attr( $value['id'] ) . '-postnum_field">';
		$fields .= '    <input type="text" class="postnum" placeholder= ' . __( "우편번호", "mshop-address-ex" ) . ' id="' . esc_attr( $value["id"] ) . '-postnum" name="' . esc_attr( $value['id'] ) . '-postnum" value="' . $postnum . '" style="width:80px" ' . ( $allow_custom ? '' : 'readonly onfocus="this.blur();"' ) . '>';
		$fields .= '    <input href="#ms_addr_1" type="button" class="ms_addr_1 ms-open-popup-link" data-id="' . esc_attr( $value["id"] ) . '" readonly="readonly" onfocus="this.blur();" value="' . __( "주소 검색", "mshop-address-ex" ) . '"></button>';
		$fields .= '</p>';
		$fields .= '<p class="form-row form-row-wide ' . esc_attr( implode( ' ', $value['class'] ) ) . '" id="' . esc_attr( $value['id'] ) . '-addr1_field">';
		$fields .= '    <input type="text" class="addr1" placeholder= ' . __( "기본주소", "mshop-address-ex" ) . ' id="' . esc_attr( $value["id"] ) . '-addr1" name="' . esc_attr( $value['id'] ) . '-addr1" value="' . $addr1 . '" style="width:100%" ' . ( $allow_custom ? '' : 'readonly onfocus="this.blur();"' ) . '>';
		$fields .= '</p>';
		$fields .= '<p class="form-row form-row-wide ' . esc_attr( implode( ' ', $value['class'] ) ) . '" id="' . esc_attr( $value['id'] ) . '-addr2_field">';
		$fields .= '    <input type="text" class="addr2" placeholder= ' . __( "상세주소", "mshop-address-ex" ) . ' id="' . esc_attr( $value["id"] ) . '-addr2" name="' . esc_attr( $value['id'] ) . '-addr2" value="' . $addr2 . '" style="width:100%" ><br class="clear" />';
		$fields .= '</p>';
		echo $fields;
	}

	public static function order_totals_after_shipping( $order_id ) {
		if ( msaddr_enabled() ) {
			ob_start();

			?>
            <script type="text/javascript">
                jQuery( document ).ready( function () {
                    rebindLoadCustomerAddress();
                } );
            </script>'
			<?php

			echo ob_get_clean();
		}
	}
	public static function update_address_field( $order, $key, $value ) {
		if ( is_callable( array( $order, 'set_' . $key ) ) ) {
			self::$props[ $key ] = $value;
		} else {
			$order->update_meta_data( '_' . $key, $value );
		}
	}
	public static function update_address( $order_id, $posted ) {

		if ( ! apply_filters( 'msaddr_checkout_update_order_meta', true, $order_id, $posted ) ) {
			return;
		}

		if ( is_a( $posted, 'WC_Order' ) ) {
			$order = $posted;
		} else {
			$order = wc_get_order( $order_id );
		}

		if ( 'naverpay' == $order->get_payment_method() ) {
			return;
		}

		self::$props = array();

		$billing_fields  = array(
			'mshop_billing_address-postnum',
			'mshop_billing_address-addr1',
			'mshop_billing_address-addr2'
		);
		$shipping_fields = array(
			'mshop_shipping_address-postnum',
			'mshop_shipping_address-addr1',
			'mshop_shipping_address-addr2',
		);

		if ( msaddr_enabled() && 'KR' == $_POST['_billing_country'] ) {
			foreach ( $billing_fields as $key ) {
				self::update_address_field( $order, $key, $_POST[ $key ] );
			}

			$billing_postcode = $_POST['mshop_billing_address-postnum'];
			$billing_address1 = $_POST['mshop_billing_address-addr1'];
			$billing_address2 = $_POST['mshop_billing_address-addr2'];

			self::update_address_field( $order, 'billing_first_name_kr', $_POST['_billing_first_name'] );
			self::update_address_field( $order, 'billing_phone', $_POST['_billing_phone'] );
			self::update_address_field( $order, 'billing_phone_kr', $_POST['_billing_phone'] );
			self::update_address_field( $order, 'billing_email', $_POST['_billing_email'] );
			self::update_address_field( $order, 'billing_email_kr', $_POST['_billing_email'] );
			self::update_address_field( $order, 'billing_postcode', $billing_postcode );
			self::update_address_field( $order, 'billing_address_1', $billing_address1 );
			self::update_address_field( $order, 'billing_address_2', $billing_address2 );
		}

		if ( msaddr_enabled() && 'KR' == $_POST['_shipping_country'] ) {
			foreach ( $shipping_fields as $key ) {
				self::update_address_field( $order, $key, $_POST[ $key ] );
			}

			$shipping_postcode = $_POST['mshop_shipping_address-postnum'];
			$shipping_address1 = $_POST['mshop_shipping_address-addr1'];
			$shipping_address2 = $_POST['mshop_shipping_address-addr2'];

			self::update_address_field( $order, 'shipping_first_name_kr', $_POST['_shipping_first_name'] );
			self::update_address_field( $order, 'shipping_phone', $_POST['_shipping_phone'] );
			self::update_address_field( $order, 'shipping_email', $_POST['_shipping_email'] );
			self::update_address_field( $order, 'shipping_postcode', $shipping_postcode );
			self::update_address_field( $order, 'shipping_address_1', $shipping_address1 );
			self::update_address_field( $order, 'shipping_address_2', $shipping_address2 );
		}

		$order->set_props( self::$props );
		$order->save();
	}

}
