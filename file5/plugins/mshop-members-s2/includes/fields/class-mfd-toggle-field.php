<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class MFD_Toggle_Field extends MFD_Field {

	public function has_value_label() {
		return true;
	}

	public function value_label( $params, $args = array() ) {
		if ( 'radio' == $this->property['checkType'] ) {
			return $this->property['label'];
		} else {
			return 'on' == $this->value( $params ) ? 'YES' : 'NO';
		}
	}

	public function output( $element, $post, $form ) {
		$value = mfd_get_post_value( $element['name'], $post, $form );

		if ( empty( $value ) && ! empty( $element['default'] ) ) {
			$meta_type = false;

			if ( $post instanceof WP_Post ) {
				$meta_type = 'post';
			} else if ( $post instanceof WP_User ) {
				$meta_type = 'user';
			}

			if ( $meta_type && ! metadata_exists( $meta_type, $post->ID, $element['name'] ) ) {
				$value = $element['default'];
			}
		}

		msm_get_template( 'form-field/toggle.php', array(
			'element' => $element,
			'value'   => $value
		) );

	}

}