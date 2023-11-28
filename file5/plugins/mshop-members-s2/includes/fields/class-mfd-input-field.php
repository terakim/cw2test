<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class MFD_Input_Field extends MFD_Field {

	public function value( $params, $args = array() ) {
		if ( 'file' == $this->type ) {
			$value = array_filter( $args['files'], function ( $file ) {
				return $file['field_key'] == $this->name;
			} );

			if ( empty( $value ) && ! empty( $params[ '_' . $this->name ] ) ) {
				$value = json_decode( $params[ '_' . $this->name ], true );
			}

			return apply_filters( 'mfd_field_value', $value, $this );
		} else {
			return apply_filters( 'mfd_field_value', urldecode( mfd_get( $params, $this->name ) ), $this );
		}
	}

	public function value_label( $params, $args = array() ) {
		if ( 'file' == $this->type ) {
			$labels = array();
			$files  = $this->value( $params, $args );

			foreach ( $files as $key => $file ) {
				$url      = sprintf( '%s/?msm_file_download=%d&key=%s&type=%s&meta_name=%s', site_url(), $args['id'], $key, $args['type'], $this->name );
				$labels[] = '<a href="' . $url . '">' . urldecode( basename( $file['filename'] ) ) . '</a>';
			}

			return apply_filters( 'mfd_field_value_label', implode( '<br>', $labels ), $this );
		} else {
			return apply_filters( 'mfd_field_value_label', $this->value( $params, $args ), $this );
		}
	}

	public function has_value_label() {
		if ( 'file' == $this->type ) {
			return true;
		} else {
			return false;
		}
	}
	public function output( $element, $post, $form ) {
		$value = mfd_get_post_value( $element['name'], $post, $form );
		if ( empty( $value ) && ! empty( mfd_get( $element, 'default' ) ) ) {
			$value = mfd_get( $element, 'default' );
		}

		$type = mfd_get( $element, 'type', 'text' );

		if ( 'password' == $type ) {
			msm_get_template( 'form-field/password.php', array(
				'element' => $element,
				'value'   => $value,
				'form'    => $form
			) );
		} else if ( 'file' == $type ) {
			msm_get_template( 'form-field/file.php', array(
				'element' => $element,
				'value'   => $value,
				'form'    => $form
			) );
		} else {
			msm_get_template( 'form-field/input.php', array(
				'element' => $element,
				'value'   => $value,
				'form'    => $form
			) );
		}
	}

}