<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class MFD_Custom_Action_Field extends MFD_Field{

	function is_savable(){
		return apply_filters( 'mfd_field_is_savable', false, $this );
	}
    public function output( $element, $post, $form ) {

	    if ( ! empty( $element['action'] ) ) {
		    $classes = mfd_make_class( array (
			    mfd_get( $element, 'class' ),
			    mfd_get( $element, 'width', 'sixteen wide' ),
			    'field'
		    ) );

		    echo sprintf( '<div class="%s">', $classes );
		    do_action( $element['action'], $element, $post, $form );
		    echo '</div>';
	    }
    }

}