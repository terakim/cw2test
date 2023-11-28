<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class MFD_Image_Picker_Field extends MFD_Field {

	public function is_savable() {
		$saveable = true;

		if ( 'yes' == $this->avatar || 'yes' == $this->readonly) {
			$saveable = false;
		}

		return apply_filters( 'mfd_field_is_savable', $saveable, $this );
	}
	public function update_meta( $id, $updator, $params, $args ) {
		$updator( $id, $this->name, $this->value( $params, $args ) );

		if ( $this->has_value_label() ) {
			$updator( $id, $this->name . '_label', $this->value_label( $params, $args ) );
		}
	}

	public function output( $element, $post, $form ) {

		wp_enqueue_media();

		$value = mfd_get_post_value( $element['name'], $post, $form );

		msm_get_template( 'form-field/image-picker.php', array( 'element' => $element, 'value' => $value, 'post' => $post ) );
	}

}