<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class MFD_Agreement_Field extends MFD_Field {

	function is_savable(){
		return apply_filters( 'mfd_field_is_savable', false, $this );
	}

	public function output( $element, $post, $form ) {

		if( is_array( $element['agreement_type'] ) ) {
			$type = array_keys( $element['agreement_type'] )[0];
		}else{
			$type = $element['agreement_type'];
		}

		$agreements = MSM_Manager::get_terms_and_conditions( $type );

		foreach ( $agreements as $agreement ) {

			msm_get_template( 'form-field/agreement-' . mfd_get( $element, 'display_type', 'standard' ) . '.php', array(
				'element'   => $element,
				'agreement' => $agreement
			) );
		}
	}

}