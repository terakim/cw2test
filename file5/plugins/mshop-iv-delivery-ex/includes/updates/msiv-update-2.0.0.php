<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$previous_settings = get_option( 'mshop_iv_delivery_fee' );

if( empty( $previous_settings ) ) {
	return;
}

$previous_settings = json_decode( $previous_settings, true );

if( empty( $previous_settings ) ) {
	return;
}

$shipping_method = new MSIV_Shipping_Korea_Zone();

$shipping_method->init_settings();

$updated_settings = array();

foreach ( $previous_settings as $setting ) {
	$updated_setting = array(
			'sido'       => $setting['sido'],
			'postalcode' => $setting['postalcode']
	);
	$sigungu      = array();
	$sigungu_code = '';
	if( ! empty( $setting['_sigungu'] ) ) {
		$sigungu_list = json_decode( $setting['_sigungu'], true );
		if ( ! empty( $sigungu_list ) ) {
			foreach ( $sigungu_list as $item ) {
				if ( $setting['sigungu'] == $item['sigungu'] ) {
					$sigungu_code = "'" . $item['sigungucode'] . "'";
				}
				$sigungu[ "'" . $item['sigungucode'] . "'" ] = $item['sigungu'];
			}
		}
	}

	$updated_setting['sigungu']  = $sigungu_code;
	$updated_setting['_sigungu'] = $sigungu;
	$bjymdl      = array();
	$bjymdl_code = array();
	if( ! empty( $setting['_umdl'] ) ) {
		$bjymdl_list = json_decode( $setting['_umdl'], true );
		$umdl        = explode( ',', $setting['umdl'] );
		if ( ! empty( $bjymdl_list ) ) {
			foreach ( $bjymdl_list as $item ) {
				if ( in_array( $item['bjdcode'], $umdl ) ) {
					$bjymdl_code[] = "'" . $item['bjdcode'] . "'";
				}
				$bjymdl[ "'" . $item['bjdcode'] . "'" ] = $item['bjymdl'];
			}
		}
	}

	$updated_setting['bjymdl']  = implode( ',', $bjymdl_code );
	$updated_setting['_bjymdl'] = $bjymdl;
	$updated_setting['fee_rules'] = array(
			array(
					'target' => 'always',
					'cost'   => $setting['fee']
			)
	);

	$updated_settings[] = $updated_setting;
}

$shipping_method->settings['msiv_shipping_rules'] = $updated_settings;

update_option( 'woocommerce_korea_zone_shipping_settings', $shipping_method->settings );