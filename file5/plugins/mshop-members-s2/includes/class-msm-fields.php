<?php

/*
=====================================================================================
                ﻿엠샵 멤버스 / Copyright 2015 by CodeM(c)
=====================================================================================

  [ 우커머스 버전 지원 안내 ]

   워드프레스 버전 : WordPress 4.3

   우커머스 버전 : WooCommerce 2.4


  [ 코드엠 플러그인 라이센스 규정 ]

   (주)코드엠에서 개발된 워드프레스  플러그인을 사용하시는 분들에게는 다음 사항에 대한 동의가 있는 것으로 간주합니다.

   1. 코드엠에서 개발한 워드프레스 우커머스용 엠샵 멤버스 플러그인의 저작권은 (주)코드엠에게 있습니다.
   
   2. 플러그인은 사용권을 구매하는 것이며, 프로그램 저작권에 대한 구매가 아닙니다.

   3. 플러그인을 구입하여 다수의 사이트에 복사하여 사용할 수 없으며, 1개의 라이센스는 1개의 사이트에만 사용할 수 있습니다. 
      이를 위반 시 지적 재산권에 대한 손해 배상 의무를 갖습니다.

   4. 플러그인은 구입 후 1년간 업데이트를 지원합니다.

   5. 플러그인은 워드프레스, 테마, 플러그인과의 호환성에 대한 책임이 없습니다.

   6. 플러그인 설치 후 버전에 관련한 운용 및 관리의 책임은 사이트 당사자에게 있습니다.

   7. 다운로드한 플러그인은 환불되지 않습니다.

=====================================================================================
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MSM_Fields' ) ) {

	class MSM_Fields {

		static $_fields = null;

		static $_options = array ();

		static function init() {
			add_filter( 'mboard_fields', array ( __CLASS__, 'mboard_fields' ) );
			foreach ( self::load_fields() as $field ) {
				if ( ! empty( $field['slug'] ) ) {
					add_action( 'mboard_output_' . $field['slug'] . '_field', array ( __CLASS__, 'mboard_output_field' ), 10, 3 );
					add_action( 'mboard_output_' . $field['slug'], array ( __CLASS__, 'mboard_output' ), 10, 3 );
				}
			}
			add_filter( 'msb_fields_get_value', array ( __CLASS__, 'msb_fields_get_value' ), 10, 4 );
		}

		static function msb_fields_get_value( $value, $msb_field, $post, $is_sticky ) {
			foreach ( self::load_fields() as $field ) {
				if ( $field['slug'] == $msb_field['type'] ) {
					return self::get_label( $field['slug'], $value );
				}
			}

			return $value;
		}

		static function mboard_output_field( $field, $post, $reply ) {
			$slug = $field['slug'];
			?>
            <div class="form-row msb-field field-<?php echo $slug; ?>">
                <label for="<?php echo $slug; ?>"><?php echo $field['name']; ?></label>
                <select id="<?php echo $slug; ?>" name="<?php echo $slug; ?>">
					<?php
					$current_cat = $post->$slug;

					$options = self::get_options( $field['type'] );
					foreach ( $options as $slug => $name ) {
						printf( '<option value="%s" %s>%s</option>', $slug, $slug == $current_cat ? 'selected' : '', $name );
					}
					?>
                </select>
            </div>
			<?php
		}

		static function mboard_output( $field, $post, $reply ) {
			$slug  = $field['slug'];
			$value = self::get_label( $field['type'], $post->$slug );
			?>
            <div class="form-row msb-field field-<?php echo $slug; ?>">
                <label for="<?php echo $slug; ?>"><?php echo $field['name']; ?></label>
                <p id="<?php echo $slug; ?>"><?php echo $value ?></p>
            </div>
			<?php
		}

		static function load_fields() {
			if ( is_null( self::$_fields ) ) {
				self::$_fields = get_option( 'msm_select_fields', array () );
			}

			return self::$_fields;
		}

		public static function get_fields() {
			$fields = array ();

			foreach ( self::load_fields() as $field ) {
				$fields[ $field['slug'] ] = $field['name'];
			}

			return apply_filters( 'msm_get_fields', $fields );
		}
		public static function get_options( $field_name ) {
			if ( empty( self::$_options[ $field_name ] ) ) {
				$options = array ();
				$fields  = self::load_fields();

				$field = array_filter( $fields, function ( $field ) use ( $field_name ) {
					return $field['slug'] == $field_name;
				} );

				if ( ! empty( $field ) ) {
					$field = current( $field );

					foreach ( $field['values'] as $value ) {
						$options[ $value['slug'] ] = $value['name'];
					}
				}


				self::$_options[ $field_name ] = $options;
			}

			return self::$_options[ $field_name ];
		}
		public static function get_label( $field_name, $values ) {
			$labels  = array ();
			$options = self::get_options( $field_name );
			$values  = explode( ',', $values );

			foreach ( $values as $value ) {
				$labels[] = $options[ $value ];
			}

			return implode( ',', $labels );
		}

		public static function mboard_fields( $fields ) {
			return array_merge( $fields, self::get_fields() );
		}

	}

	MSM_Fields::init();
}