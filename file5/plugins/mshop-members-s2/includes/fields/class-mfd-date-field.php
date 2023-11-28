<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class MFD_Date_Field extends MFD_Field{

    public function output( $element, $post, $form ) {

	    $value = mfd_get_post_value( $element['name'], $post, $form );

	    msm_get_template( 'form-field/date.php', array(
			    'element' => $element,
			    'value'   => $value
	    ) );

    }

}