<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class MFD_Shortcode_Field extends MFD_Field {

	function is_savable() {
		return apply_filters( 'mfd_field_is_savable', false, $this );
	}
	public function output( $element, $post, $form ) {

		if ( ! empty( $element['content'] ) ) {
			$classes = mfd_make_class( array(
				mfd_get( $element, 'class' ),
				mfd_get( $element, 'width', 'sixteen wide' ),
				'field'
			) );

			echo sprintf( '<div class="%s">', $classes );
			echo do_shortcode( $element['content'] );
			echo '</div>';
		}
	}

}