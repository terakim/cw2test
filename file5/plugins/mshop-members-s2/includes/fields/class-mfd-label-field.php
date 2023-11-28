<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class MFD_Label_Field extends MFD_Field{

    function is_savable(){
        return apply_filters( 'mfd_field_is_savable', false, $this );
    }

    public function output( $element, $post, $form ) {
        msm_get_template( 'form-field/label.php', array(
            'element' => $element
        ) );
    }

}