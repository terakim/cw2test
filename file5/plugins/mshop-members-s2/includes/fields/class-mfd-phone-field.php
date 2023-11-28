<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class MFD_Phone_Field extends MFD_Field {
	public function output( $element, $post, $form ) {
		$value = mfd_get_post_value( $element['name'], $post, $form );

		msm_get_template( 'form-field/phone.php', array(
			'element' => $element,
			'form'    => $form,
			'value'   => $value
		) );
	}

	public function need_certificate() {

	}

	public function update_meta( $id, $updator, $params, $args ) {
		$updator( $id, $this->name, $this->value( $params, $args ) );

		if ( 'yes' == mfd_get( $this->property, 'certification' ) ) {
			$updator( $id, 'mshop_auth_method', 'mshop-sms' );
			$updator( $id, 'mshop_auth_phone', $this->value( $params, $args ) );
		};
	}

}