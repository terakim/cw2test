<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MSM_Exporter {
	static function add_field_options( $rule ) {
		return array_merge( $rule, array(
			array(
				"id"            => "fields",
				"title"         => __( "멤버스 필드", 'mshop-members-s2' ),
				'showIf'        => array( 'type' => 'Select' ),
				"className"     => "center aligned four wide column fluid",
				"cellClassName" => "fluid",
				"type"          => "Select",
				'default'       => '',
				'placeholder'   => __( '필드를 선택해주세요', 'mshop-members-s2' ),
				'options'       => MSM_Fields::get_fields()
			),
			array(
				"id"            => "multiple",
				"title"         => __( "Multiple", 'mshop-members-s2' ),
				'showIf'        => array( 'type' => 'Select' ),
				"className"     => "center aligned one wide column fluid",
				"cellClassName" => "center aligned",
				"type"          => "Toggle",
				'default'       => 'no',
			)
		) );
	}
	static function add_product_field_types( $field_types ) {
		return array_merge( $field_types, array(
			"Select" => __( 'Select', 'mshop-members-s2' )
		) );
	}
	static function get_product_field_options( $data, $field ) {
		if ( 'Select' == $field['type'] ) {
			$data['placeholder'] = __( '선택해주세요,', 'mshop-members-s2' );
			$data['options']     = array();
			$load_fields         = MSM_Fields::load_fields();
			$idx                 = array_search( ! empty( $field['fields'] ) ? $field['fields'] : key( $load_fields ), array_column( $load_fields, 'slug' ) );
			$options             = $load_fields[ $idx ];

			foreach ( $options['values'] as $option ) {
				$data['options'] = array_merge( $data['options'],
					array(
						$option['slug'] => $option['name']
					)
				);
			}

			if ( 'yes' == $field['multiple'] ) {
				$data = array_merge( $data, array(
					'multiple' => true
				) );
			}
		}

		return $data;
	}

}