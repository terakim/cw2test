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
} // Exit if accessed directly

if ( ! class_exists( 'MSM_Meta' ) ) {

	class MSM_Meta {

		static $_files = array();

		static function get_upload_dir( $args ) {
			$upload_dir   = wp_upload_dir();
			$user_dirname = $upload_dir['basedir'] . '/mshop_members/' . $args['type'] . '/' . $args['id'] . '/';
			if ( ! file_exists( $user_dirname ) ) {
				wp_mkdir_p( $user_dirname );
			}

			return $user_dirname;
		}
		static function move_upload_files( $args, $user_id, $post_processing_data ) {
			$files = array();

			if ( isset( $_FILES ) ) {
				foreach ( $_FILES as $key => $file ) {
					$file_count = count( $file['name'] );

					for ( $i = 0; $i < $file_count; $i ++ ) {
						$unique_key = $key . '#' . $i;
						$unique_id  = uniqid();

						$file_name = apply_filters( 'msm_upload_file_name', $file['name'][ $i ], $args, $key, $post_processing_data );

						if ( apply_filters( 'msm_url_encode_to_upload_filename', true ) ) {
							$file_name = urlencode( $file_name );
						}

						$destination = self::get_upload_dir( $args ) . basename( $file_name );

						if ( empty( self::$_files[ $unique_key ] ) ) {
							if ( move_uploaded_file( $file['tmp_name'][ $i ], $destination ) ) {
								$files[ $unique_id ] = array(
									'field_key' => $key,
									'filename'  => $destination
								);

								self::$_files[ $unique_key ] = $destination;
							} else {
								throw new Exception( __( '파일 업로드중 오류가 발생했습니다.', 'mshop-members-s2' ) );
							}
						} else if ( ! file_exists( $destination ) ) {
							if ( copy( self::$_files[ $unique_key ], $destination ) ) {
								$files[ $unique_id ] = array (
									'field_key' => $key,
									'filename'  => $destination
								);
							} else {
								throw new Exception( __( '파일 업로드중 오류가 발생했습니다.', 'mshop-members-s2' ) );
							}
						} else {
							$files[ $unique_id ] = array (
								'field_key' => $key,
								'filename'  => $destination
							);
						}

                        if ( wp_getimagesize( $destination ) ) {
                            $dir  = wp_upload_dir();
                            $link = sprintf( '%s/mshop_members/user/%s/%s', $dir['baseurl'], $user_id, urlencode( basename( $file_name ) ) );
                            $files[ $unique_id ] = array_merge( $files[ $unique_id ], array(
                                'image' => sprintf( '<li data-title="%s"><a href="%s" target="_blank"><img src="%s"></a></li>', urldecode( $file_name ), $link, $link )
                            ) );
                        }
					}
				}
			}

			return $files;
		}

		public static function update_user_meta( $id, $post_processing_data, $form_meta_name = '', $args = array() ) {
			$args = array_merge( $args, array(
				'type'      => 'user',
				'id'        => $id,
				'meta_name' => $form_meta_name
			) );
			self::update( 'update_user_meta', $id, $post_processing_data, $form_meta_name, $args );
		}

		public static function update_post_meta( $id, $post_processing_data, $form_meta_name = '', $args = array() ) {
			$args = array_merge( $args, array(
				'type'      => 'post',
				'id'        => $id,
				'meta_name' => $form_meta_name
			) );
			self::update( 'update_post_meta', $id, $post_processing_data, $form_meta_name, $args );
		}
		public static function filter_fields( $fields, $args ) {
			$update_fields = mfd_get( $args, 'update_fields' );
			$except_fields = mfd_get( $args, 'except_fields' );

			if ( ! empty( $args['update_fields'] ) ) {
				$fields = array_filter( $fields, function ( $field ) use ( $update_fields ) {
					$match = array_filter( $update_fields, function ( $update_field ) use ( $field ) {
						$result = preg_match( "/" . $update_field . "/", $field->name );
						if ( $result === false ) {
							$result = fnmatch( $update_field, $field->name );
						}

						return $result;
					} );

					return ! empty( $match );

				} );
			}

			if ( ! empty( $except_fields ) ) {
				$fields = array_filter( $fields, function ( $field ) use ( $except_fields ) {
					$match = array_filter( $except_fields, function ( $except_field ) use ( $field ) {
						$result = preg_match( "/" . $except_field . "/", $field->name );
						if ( $result === false ) {
							$result = fnmatch( $except_field, $field->name );
						}

						return $result;
					} );

					return empty( $match );
				} );
			}
			foreach ( $fields as &$field ) {
				if ( empty( $field->name ) && empty( $field->property['name'] ) && ! empty( $field->property['id'] ) ) {
					$field->name             = $field->property['id'];
					$field->property['name'] = $field->property['id'];
				}
			}

			$fields = array_combine( array_column( $fields, 'name' ), $fields );

			return apply_filters( 'msm_filter_fields', $fields, $args );
		}
		public static function update( $updator, $id, $post_processing_data, $form_meta_name = '', $args = array() ) {
			try {
				$files = self::move_upload_files( $args, $id, $post_processing_data );

				if ( $files instanceof WP_Error ) {
					return $files;
				}

				$args['files'] = $files;

				$forms = array();

				foreach ( $post_processing_data as $data ) {
					$form = $data['form'];

					$fields = array_filter( $form->get_fields(), function ( $field ) use ( $data ) {
						if ( ! is_a( $field, 'MFD_Toggle_Field' ) || 'radio' != $field->property['checkType'] || $field->property['value'] == $data['params'][ $field->property['name'] ] ) {
							return $field;
						}
					} );

					$fields = self::filter_fields( $fields, $args );

					if ( ! empty( $fields ) ) {
						foreach ( $fields as $field ) {
							$field->update_meta( $id, $updator, $data['params'], $args );
						}

						$forms[] = array(
							'id'   => $form->id,
							'data' => $form->form_data
						);
					}
				}

				if ( ! empty( $form_meta_name ) ) {
					$args = array(
						'forms'        => $forms,
						'form_version' => MSM_VERSION,
						'args'         => $args
					);
					$updator( $id, $form_meta_name, $args );
				}

				return true;
			} catch ( Exception $e ) {
				return false;
			}
		}

		public static function get_user_meta( $id, $form_meta_name ) {
			return self::get( 'get_user_meta', $id, $form_meta_name );
		}

		public static function get_post_meta( $id, $form_meta_name ) {
			return self::get( 'get_post_meta', $id, $form_meta_name );
		}

		public static function get( $getter, $id, $form_meta_name ) {
			$metas     = array();
			$form_info = $getter( $id, $form_meta_name, true );

			if ( ! empty( $form_info ) ) {
				foreach ( $form_info['forms'] as $form_data ) {
					$fields = self::filter_fields( mfd_get_form_fields( $form_data['data'] ), $form_info['args'] );

					foreach ( $fields as $field ) {
						if ( ! empty( $field->name ) ) {
							$value = $getter( $id, $field->name, true );

							$metas[] = array(
								'name'  => $field->name,
								'title' => $field->title,
								'value' => is_scalar( $value ) ? str_replace( "\n", "<br>", $value ) : '',
								'label' => str_replace( "\n", "<br>", $getter( $id, $field->name . '_label', true ) )
							);
						}
					}
				}
			}

			return $metas;
		}
	}
}