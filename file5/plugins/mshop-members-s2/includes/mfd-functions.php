<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
function mfd_output( $element, $post, $form, $is_form_fields = false ) {
	$element_name = preg_split( "/(?<=[a-z])(?![a-z])/", $element['type'], - 1, PREG_SPLIT_NO_EMPTY );
	$class        = 'MFD_' . implode( '_', $element_name ) . '_Field';

	if ( class_exists( $class, true ) ) {
		$object = new $class( $element );
		$object->output( $element['property'], $post, $form );
	}
}
function mfd_get_post_value( $name, $post, $form ) {
	$value = '';
	$name  = trim( $name );

	if ( ! empty( $name ) ) {
		if ( $post instanceof WP_Post ) {
			if ( 0 === strpos( $name, 'post_' ) ) {
				$value = $post->$name;
			} else {
				$value = get_post_meta( $post->ID, $name, true );
			}
		} else if ( $post instanceof WP_User ) {
			if ( in_array( $name, array( 'user_nicename', 'display_name', 'user_login', 'user_email' ) ) ) {
				$value = $post->$name;
			} else {
				$value = get_user_meta( $post->ID, $name, true );
			}
		}
	}

	return apply_filters( 'mfd_get_post_value', $value, $name, $post, $form );
}
function mfd_get_field_object( $field ) {
	$field_name = preg_split( "/(?<=[a-z])(?![a-z])/", $field['type'], - 1, PREG_SPLIT_NO_EMPTY );
	$class      = 'MFD_' . implode( '_', $field_name ) . '_Field';

	if ( class_exists( $class, true ) ) {
		return new $class( $field );
	}

	return null;
}
function mfd_get_field( $field, $all ) {
	$fields = array();
	$object = mfd_get_field_object( $field );

	if ( ! is_null( $object ) ) {
		return $object->get_field( $all );
	}

	return $fields;
}

function mfd_get_form_fields( $form_data, $all = false ) {
	$fields = array();

	foreach ( $form_data as $field ) {
		$fields = array_merge( $fields, mfd_get_field( $field, $all ) );
	}

	return array_filter( $fields );
}


function mfd_get_meta_name( $name, $form ) {
	return '_msm_' . $form->id . '_' . $name;
}

function mfd_get_form( $post_id ) {
	return new MSM_Form( $post_id );
}
function mfd_get( $object, $key, $default = '', $format = null ) {
	$value = $default;

	$key = trim( $key );

	if ( ! empty( $object ) && ! empty( $object[ $key ] ) ) {
		$value = $object[ $key ];

		if ( ! is_null( $format ) ) {
			$value = sprintf( $format, $value );
		}
	}

	return $value;
}

function mfd_make_class( $classes ) {
	$results = array();

	foreach ( $classes as $class ) {
		$class   = explode( ' ', trim( $class ) );
		$results = array_merge( $results, array_filter( $class ) );
	}

	return implode( ' ', array_unique( $results ) );
}

function mfd_get_style( $style ) {
	if ( $style ) {
		$style = str_replace( "\n", "", $style );
	}

	return $style;
}
function mfd_get_conditional_class( $element ) {
	$show_if = array();

	if ( ! empty( $element['showIf'] ) ) {
		foreach ( $element['showIf'] as $condition ) {
			$values = explode( ',', $condition['value'] );

			foreach ( $values as $value ) {
				$value = trim( $value );

				if ( 0 === strpos( $value, "!" ) ) {
					$show_if[] = 'hide-if';
					$show_if[] = 'hide-if-' . $condition['id'];
					$show_if[] = 'hide-if-' . $condition['id'] . '-' . substr( $value, 1 );
				} else {
					$show_if[] = 'show-if';
					$show_if[] = 'show-if-' . $condition['id'];
					$show_if[] = 'show-if-' . $condition['id'] . '-' . $value;
				}
			}
		}

		$show_if = array_unique( $show_if );
	}

	return $show_if;
}
function mfd_get_conditional_style( $show_if ) {
	if ( ! empty( $show_if ) ) {
		return 'display: none';
	}
}
function mfd_output_title( $element ) {
	if ( ! empty( $element['title'] ) ) {
		if ( empty( $element['tooltip'] ) ) {
			?>
            <label for="<?php echo mfd_get( $element, 'name' ); ?>"><?php echo $element['title']; ?></label>
			<?php
		} else {
			?>
            <label for="<?php echo mfd_get( $element, 'name' ); ?>" class="msm-tooltip"><?php echo $element['title']; ?><i class="fa fa-question-circle " data-variation="mini"></i></label>
            <div class="ui icon popup">
                <div class="content"><?php echo $element['tooltip']; ?></div>
            </div>
			<?php
		}
	}
}
