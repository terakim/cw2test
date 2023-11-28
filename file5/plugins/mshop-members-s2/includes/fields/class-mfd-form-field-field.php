<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class MFD_Form_Field_Field extends MFD_Field {

	public function output( $element, $post, $form ) {

		msm_get_template( 'form-field/form-field.php', array(
			'element'  => $element,
			'msm_post' => $post,
			'msm_form' => $form
		) );
	}

}