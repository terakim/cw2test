<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class MFD_Dividing_Header_Field extends MFD_Field{

    function is_savable(){
        return apply_filters( 'mfd_field_is_savable', false, $this );
    }

    public function output( $element ) {

        msm_get_template( 'form-field/dividing-header.php', array(
            'element' => $element
        ) );

    }

}