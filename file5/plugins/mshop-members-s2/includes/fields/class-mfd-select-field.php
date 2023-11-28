<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class MFD_Select_Field extends MFD_Field {

	public function has_value_label() {
		return true;
	}

	public function value_label( $params, $args = array() ) {
		$labels = array();
		$values = $this->value( $params );

		if ( ! empty( $values ) ) {

			if ( ! is_array( $values ) ) {
				$values = explode( ',', $values );
			}

			$options = $this->get_options( $this->property );

			foreach ( $values as $value ) {
				$labels[] = mfd_get( $options, $value );
			}

		}

		return apply_filters( 'mfd_field_value_label', implode( ',', $labels ), $this );
	}
	static function get_options( $element, $post = null, $form = null ) {
		$options   = array();
		$data_type = mfd_get( $element, 'data_type' );

		if ( 'taxonomy' == $data_type ) {
			$taxonomy = mfd_get( $element, 'taxonomy' );

			$keys = array_keys( $taxonomy );

			if ( ! empty( $keys ) ) {
				$terms = get_terms( $keys[0], array(
					'hide_empty' => false,
				) );

				foreach ( $terms as $term ) {
					$options[ $term->slug ] = $term->name;
				}
			}
		} else if ( 'msm_field' == $data_type ) {
			$field_name = mfd_get( $element, 'msm_field_name' );

			$options = MSM_Fields::get_options( key( $field_name ) );

		} else if ( 'wc_country' == $data_type ) {
			$options = WC()->countries->get_countries();
		} else if ( 'custom' == $data_type ) {
			$custom_fields = mfd_get( $element, 'custom_fields' );

			if ( ! empty( $custom_fields ) ) {
				foreach ( $custom_fields as $field ) {
					$options[ $field['key'] ] = $field['label'];
				}
			}

		}

		return apply_filters( 'mfd_select_field_options', $options, $element, $post, $form );
	}

	static function get_values( $element, $post, $form ) {
		$values = array();

		if ( 'yes' == mfd_get( $element, 'is_taxonomy' ) ) {
			$taxonomy = key( mfd_get( $element, 'taxonomy' ) );

			if ( $post && ! empty( $taxonomy ) ) {
				$post_terms = wp_get_object_terms( $post->ID, $taxonomy );
				$values     = array_map( function ( $term ) {
					return $term->slug;
				}, $post_terms );
			}
		} else {
			$values = explode( ',', mfd_get_post_value( $element['name'], $post, $form ) );
		}

		return $values;
	}

	public function output( $element, $post, $form ) {

		$options = self::get_options( $element, $post, $form );
		$values  = self::get_values( $element, $post, $form );

		msm_get_template( 'form-field/select.php', array(
			'element' => $element,
			'values'  => $values,
			'options' => $options
		) );
	}

}