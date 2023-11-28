<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class MFD_Labeled_Input_Field extends MFD_Field{

    public function output( $element, $post, $form ) {

        $value = mfd_get_post_value( $element['name'], $post, $form );

	    msm_get_template( 'form-field/labeled-input.php', array(
		    'element' => $element,
		    'value'   => $value
	    ) );

    }

}