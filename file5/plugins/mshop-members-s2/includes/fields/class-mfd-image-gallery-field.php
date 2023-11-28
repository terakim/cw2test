<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class MFD_Image_Gallery_Field extends MFD_Field {

	public function output( $element, $post, $form ) {

		wp_enqueue_media();

		$value = mfd_get_post_value( $element['name'], $post, $form );
		$type = mfd_get( $element, 'type', 'thumbnail' );

		msm_get_template( 'form-field/media-gallery-' . $type . '.php', array( 'element' => $element, 'value' => $value, 'post' => $post ) );
	}

}