<?php
/*
=====================================================================================
                엠샵 업다운로드 / Copyright 2016 by CodeM(c)
=====================================================================================

  [ 우커머스 버전 지원 안내 ]

   워드프레스 버전 : WordPress 4.6 이상

   우커머스 버전 : WooCommerce 2.6 이상


  [ 코드엠 플러그인 라이센스 규정 ]

   (주)코드엠에서 개발된 워드프레스  플러그인을 사용하시는 분들에게는 다음 사항에 대한 동의가 있는 것으로 간주합니다.

   1. 코드엠에서 개발한 워드프레스 우커머스용 엠샵 업다운로드 플러그인의 저작권은 (주)코드엠에게 있습니다.
   
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
function msex_ajax_command( $command ) {
    return MSEX_AJAX_PREFIX . '_' . $command;
}
function msex_get_exporter( $typenow, $template_id ) {
	if ( 'product' == $typenow ) {
		return new MSEX_Export_Product( $template_id );
	} else if ( 'user' == $typenow ) {
		return new MSEX_Export_User( $template_id );
	} else if ( in_array( $typenow, wc_get_order_types() ) ) {
		return new MSEX_Export_Order( $template_id );
	}

	return null;
}

function msex_get_product_types() {
	return array_merge( wc_get_product_types(), array( 'variation' => __( 'Variation', 'mshop-exporter' ) ) );
}

function msex_maybe_convert_to_utf8( $str ) {
	$bom = pack('H*','EFBBBF');
	$str = preg_replace("/^$bom/", '', $str);

	if ( is_string( $str ) && 'EUC-KR' == mb_detect_encoding( $str, array( 'UTF-8', 'EUC-KR' ) ) ) {
		$str = mb_convert_encoding( $str, 'UTF-8', 'EUC-KR' );
	}

	return trim( $str );
}

function msex_maybe_convert_to_euckr( $str ) {
	$bom = pack('H*','EFBBBF');
	$str = preg_replace("/^$bom/", '', $str);

	if ( is_string( $str ) && 'UTF-8' == mb_detect_encoding( $str, array( 'EUC-KR', 'UTF-8' ) ) ) {
		$str = mb_convert_encoding( $str, 'EUC-KR', 'UTF-8' );
	}

	return trim( $str );
}
function msex_get( $array_object, $key, $default = '' ) {
	return ! empty( $array_object[ $key ] ) ? $array_object[ $key ] : $default;
}
function msex_get_object_property( $object, $property ) {
	$method = 'get_' . $property;

    if( ! is_object( $object ) ) {
        return '';
    }

	return is_callable( array( $object, $method ) ) ? $object->$method() : $object->$property;
}
function msex_update_meta_data( $object, $key, $value ) {
	if ( is_callable( array( $object, 'update_meta_data' ) ) ) {
		$object->update_meta_data( $key, $value );
		$object->save();
	} else {
		if ( $object instanceof WC_Abstract_Order ) {
			update_post_meta( msex_get_object_property( $object, 'id' ), $key, $value );
		} else if ( $object instanceof WC_Order_Item ) {
			wc_update_order_item_meta( msex_get_object_property( $object, 'id' ), $key, $value );
		} else if ( is_numeric( $object ) ) {
			wc_update_order_item_meta( $object, $key, $value );
		}
	}
}
function msex_get_meta( $object, $meta_key, $single = true, $context = 'view' ) {
	if ( empty( $object ) || empty( $meta_key ) ) {
		return '';
	}

	if ( is_callable( array( $object, 'get_meta' ) ) ) {
        $method = 'get_' . $meta_key;

       if( is_callable( array( $object, $method ))) {
           return $object->$method();
       }else{
           return $object->get_meta( $meta_key, $single, $context );
       }
	} else {
		if ( $object instanceof WC_Abstract_Order ) {
			return get_post_meta( msex_get_object_property( $object, 'id' ), $meta_key, $single );
		} else if ( is_numeric( $object ) ) {
			return wc_get_order_item_meta( $object, $meta_key, $single );
		} else if ( $object instanceof WC_Order_Item ) {
			return wc_get_order_item_meta( msex_get_object_property( $object, 'id' ), $meta_key, $single );
		}
	}
}
function msex_get_meta_data( $order_info, $prefix ) {
	$metas     = array();
	$meta_keys = array();

	foreach ( $order_info as $key => $value ) {
		if ( 0 === strpos( $key, $prefix ) && ! empty( $value ) ) {
			$meta_keys[] = $key;
		}
	}

	foreach ( $meta_keys as $meta_key ) {
		$store_key           = str_replace( $prefix, '', $meta_key );
		$metas[ $store_key ] = $order_info[ $meta_key ];
	}

	return $metas;
}
function msex_set_object_property( $object, $property, $value ) {
	$method = 'set_' . $property;

	if ( is_callable( $object, $method ) ) {
		$object->$method( $value );
	} else {
		$object->$property = $value;
	}
}

function msex_load_dlv_company_info() {
	static $dlv_company_info = null;

	if ( is_null( $dlv_company_info ) ) {
		$dlv_company_info = array();
		$dlv_company      = get_option( 'msex_dlv_company', MSEX_Settings_Sheet::get_default_dlv_company() );

		foreach ( $dlv_company as $company ) {
			$dlv_company_info[ $company['dlv_code'] ] = $company;
		}
	}

	return $dlv_company_info;
}

function msex_get_dlv_company_info( $dlv_code ) {
	$dlv_company_info = msex_load_dlv_company_info();

	return msex_get( $dlv_company_info, $dlv_code, null );
}
function msex_get_coupon_codes( $order ) {
	if ( version_compare( WC_VERSION, '3.7.0', '>=' ) ) {
		return $order->get_coupon_codes();
	} else {
		return $order->get_used_coupons();
	}
}

add_filter( 'woocommerce_hidden_order_itemmeta', 'MSEX_Upload_Sheets::woocommerce_hidden_order_itemmeta', 10 );
add_filter( 'woocommerce_attribute_label', 'MSEX_Upload_Sheets::woocommerce_attribute_label', 10, 2 );
add_filter( 'woocommerce_order_items_meta_display', 'MSEX_Upload_Sheets::woocommerce_order_items_meta_display', 10, 2 );
add_filter( 'woocommerce_display_item_meta', 'MSEX_Upload_Sheets::woocommerce_display_item_meta', 10, 3 );
if ( 'yes' == get_option( 'msex_sheet_settings_enabled', 'yes' ) ) {
	add_action( 'add_meta_boxes', 'MSEX_Meta_Box_Order::add_meta_boxes' );
}
function msex_sheet_no( $ship_num, $order_id ) {
	$order = wc_get_order( $order_id );

	if ( $order ) {
		$sheet_no = msex_get_meta( $order, '_msex_sheet_no' );
		if ( ! empty( $sheet_no ) ) {
			$ship_num = preg_replace( '~\D~', '', $sheet_no );
		} else {
			$order_items = $order->get_items();
			$order_item  = current( $order_items );

			if ( $order_item && ! empty( $order_item->get_meta( '_msex_dlv_code' ) ) ) {
				$ship_num = preg_replace( '~\D~', '', $order_item->get_meta( '_msex_dlv_code' ) );
			}
		}
	}

	return $ship_num;
}

add_filter( 'mshop_sms_ship_number', 'msex_sheet_no', 10, 2 );
add_filter( 'mssms_ship_number', 'msex_sheet_no', 10, 2 );
function msex_dlv_name( $dlv_name, $order_id ) {
	$order = wc_get_order( $order_id );

	if ( $order ) {
		$msex_dlv_name = msex_get_meta( $order, '_msex_dlv_name' );
		if ( ! empty( $msex_dlv_name ) ) {
			$dlv_name = $msex_dlv_name;
		} else {
			$order_items = $order->get_items();
			$order_item  = current( $order_items );

			if ( $order_item && ! empty( $order_item->get_meta( '_msex_dlv_name' ) ) ) {
				$dlv_name = $order_item->get_meta( '_msex_dlv_name' );
			}
		}
	}

	return $dlv_name;
}

add_filter( 'mshop_sms_shipping_company_name', 'msex_dlv_name', 10, 2 );
add_filter( 'mssms_shipping_company_name', 'msex_dlv_name', 10, 2 );
function msex_get_customer_field( $order, $field ) {
	static $fields = array(
        'billing_postcode'            => array(
            '_mshop_billing_address-postnum',
            'get_billing_postcode',
        ),
		'shipping_postcode'            => array(
			'_mshop_shipping_address-postnum',
			'get_shipping_postcode',
		),
        'billing_address1'            => array(
            '_mshop_billing_address-addr1',
            'get_billing_address_1',
        ),
        'billing_address2'            => array(
            '_mshop_billing_address-addr2',
            'get_billing_address_2',
        ),
		'shipping_address1'            => array(
			'_mshop_shipping_address-addr1',
			'get_shipping_address_1',
		),
		'shipping_address2'            => array(
			'_mshop_shipping_address-addr2',
			'get_shipping_address_2',
		),
		'city'                => array(
			'get_shipping_city',
			'get_billing_city'
		),
		'state'               => array(
			'get_shipping_state',
			'get_billing_state'
		),
		'country'             => array(
			'get_shipping_country',
			'get_billing_country'
		),
		'billing_phone'       => array(
			'get_billing_phone',
			'_billing_phone_kr'
		),
		'shipping_phone'      => array(
			'get_shipping_phone',
			'_shipping_phone',
			'get_billing_phone',
		),
		'billing_first_name'  => array(
			'_billing_first_name_kr',
			'get_billing_first_name',
		),
		'billing_last_name'   => array(
			'get_billing_last_name',
		),
		'shipping_first_name' => array(
			'_shipping_first_name_kr',
			'get_shipping_first_name',
		),
		'shipping_last_name'  => array(
			'get_shipping_last_name',
		),
	);

	foreach ( $fields[ $field ] as $meta_key ) {
		if ( is_callable( array( $order, $meta_key ) ) ) {
			$meta_value = $order->$meta_key();
		} else {
			$meta_value = msex_get_meta( $order, $meta_key );
		}

		if ( ! empty( $meta_value ) ) {
			return $meta_value;
		}
	}

	return '';
}
function msex_get_customer_info( $order ) {
	if ( 'naverpay' !== $order->get_payment_method() && 'KR' == $order->get_shipping_country() ) {
		return array(
			'billing_name'       => msex_get_customer_field( $order, 'billing_first_name' ),
            'billing_postcode'  => str_replace( '-', '', msex_get_customer_field( $order, 'billing_postcode' ) ),
            'billing_address_1' => msex_get_customer_field( $order, 'billing_address1' ),
            'billing_address_2' => msex_get_customer_field( $order, 'billing_address2' ),
			'billing_phone'      => str_replace( '-', '', msex_get_customer_field( $order, 'billing_phone' ) ),
			'shipping_name'      => msex_get_customer_field( $order, 'shipping_first_name' ),
			'shipping_postcode'  => str_replace( '-', '', msex_get_customer_field( $order, 'shipping_postcode' ) ),
			'shipping_address_1' => msex_get_customer_field( $order, 'shipping_address1' ),
			'shipping_address_2' => msex_get_customer_field( $order, 'shipping_address2' ),
			'shipping_phone'     => str_replace( '-', '', msex_get_customer_field( $order, 'shipping_phone' ) ),
		);
	} else {
		$country = msex_get_object_property( $order, 'shipping_country' );

		$states = WC()->countries->get_states( $country );

		$state = msex_get_object_property( $order, 'shipping_state' );
		if ( ! empty( $states ) && ! empty( $states[ $state ] ) ) {
			$state = $states[ $state ];
		}

		$address_1 = array_filter( array(
			$state,
			msex_get_object_property( $order, 'shipping_city' ),
			msex_get_object_property( $order, 'shipping_address_1' ),
		) );

		return array(
			'billing_name'       => sprintf( '%1$s %2$s', msex_get_object_property( $order, 'billing_first_name' ), msex_get_object_property( $order, 'billing_last_name' ) ),
			'billing_phone'      => str_replace( '-', '', msex_get_object_property( $order, 'billing_phone' ) ),
			'shipping_name'      => sprintf( '%1$s %2$s', msex_get_object_property( $order, 'shipping_first_name' ), msex_get_object_property( $order, 'shipping_last_name' ) ),
			'shipping_postcode'  => str_replace( '-', '', msex_get_object_property( $order, 'shipping_postcode' ) ),
			'shipping_address_1' => implode( ' ', $address_1 ),
			'shipping_address_2' => msex_get_object_property( $order, 'shipping_address_2' ),
			'shipping_phone'     => str_replace( '-', '', msex_get_customer_field( $order, 'shipping_phone' ) ),
		);
	}
}

function msex_remove_emoji( $clean_text ) {
	//step #1
	$clean_text = preg_replace( '/([0-9#][\x{20E3}])|[\x{00ae}\x{00a9}\x{203C}\x{2047}\x{2048}\x{2049}\x{3030}\x{303D}\x{2139}\x{2122}\x{3297}\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u', '', $clean_text );

	//step #2
	// Match Emoticons
	$regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
	$clean_text     = preg_replace( $regexEmoticons, '', $clean_text );

	// Match Miscellaneous Symbols and Pictographs
	$regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
	$clean_text   = preg_replace( $regexSymbols, '', $clean_text );

	// Match Transport And Map Symbols
	$regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
	$clean_text     = preg_replace( $regexTransport, '', $clean_text );

	// Match Miscellaneous Symbols
	$regexMisc  = '/[\x{2600}-\x{26FF}]/u';
	$clean_text = preg_replace( $regexMisc, '', $clean_text );

	// Match Dingbats
	$regexDingbats = '/[\x{2700}-\x{27BF}]/u';
	$clean_text    = preg_replace( $regexDingbats, '', $clean_text );

	return $clean_text;
}

function msex_get_track_url( $dlv_code, $sheet_no ) {
	$track_url = '';

	$dlv_company = msex_get_dlv_company_info( $dlv_code );

	if ( ! empty( $dlv_company ) ) {
		$track_url = str_replace( '{msex_sheet_no}', $sheet_no, $dlv_company['dlv_url'] );
	}

	return $track_url;
}