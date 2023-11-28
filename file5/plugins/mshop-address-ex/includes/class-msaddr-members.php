<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class MSADDR_Members {

	public static function admin_enqueue_scripts() {
		wp_enqueue_script( 'msm_address_widget', MSADDR()->plugin_url() . '/assets/js/members/address-widget.js' );
	}

	public static function msm_widget_reserved( $widgets ) {
		$widgets = array_merge( $widgets,
			array (
				array (
					'type'     => 'Address',
					'property' => array (
						'id'         => 'billing',
						'title'      => __( '주소를 입력하세요.', 'mshop-address-ex' ),
						'buttonText' => __( '주소검색', 'mshop-address-ex' ),
						'required'   => 'yes'
					),
					'icon'     => 'icon-address',
					'title'    => __( '주소검색', 'mshop-address-ex' ),
				)
			)
		);

		return $widgets;
	}

	public static function address_field_rules( $rules, $element ) {
		$property = $element['property'];

		if ( 'Address' === $element['type'] && ! empty( $property['required'] ) && 'yes' === $property['required'] ) {
			$rules[ $property['id'] ] = array (
				'rules' => array (
					array (
						'type'   => 'empty',
						'prompt' => __( '주소를 입력해주세요.', 'mshop-address-ex' )
					)
				)
			);

			if ( 'yes' == get_option( 'msaddr_required_field_address2', 'no' ) ) {
				$rules[ $property['id'] . '_address_2' ] = array (
					'rules' => array (
						array (
							'type'   => 'empty',
							'prompt' => __( '상세주소를 입력해주세요.', 'mshop-address-ex' )
						)
					)
				);
			}
		}

		return $rules;
	}

}

