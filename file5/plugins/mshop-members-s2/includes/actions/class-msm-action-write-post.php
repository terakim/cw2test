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

if ( ! class_exists( 'MSM_Action_Write_Post' ) ) {

	class MSM_Action_Write_Post {

		static function get_upload_dir() {
			$upload_dir   = wp_upload_dir();
			$user_dirname = $upload_dir['basedir'] . '/samdi/' . get_current_user_id() . '/';
			if ( ! file_exists( $user_dirname ) ) {
				wp_mkdir_p( $user_dirname );
			}

			return $user_dirname;
		}

		static function move_upload_files() {
			$files = array();

			if ( isset( $_FILES ) ) {
				foreach ( $_FILES as $key => $file ) {
					$file_count = count( $file['name'] );

					for ( $i = 0; $i < $file_count; $i ++ ) {
						$destination = self::get_upload_dir() . basename( urlencode( $file['name'][ $i ] ) );

						if ( move_uploaded_file( $file['tmp_name'][ $i ], $destination ) ) {
							$files[] = array(
								'field_key' => $key,
								'filename'  => $destination
							);
						} else {
							throw new Exception( __( '파일 업로드중 오류가 발생했습니다.', 'mshop-members-s2' ) );
						}
					}
				}
			}

			return $files;
		}

		static function insert_attachment( $post_id, &$files ) {

			foreach ( $files as &$file ) {

				// Check the type of file. We'll use this as the 'post_mime_type'.
				$filetype = wp_check_filetype( basename( $file['filename'] ), null );

				// Get the path to the upload directory.
				$wp_upload_dir = wp_upload_dir();

				// Prepare an array of post data for the attachment.
				$attachment = array(
					'guid'           => $wp_upload_dir['url'] . '/samdi/' . get_current_user_id() . '/' . basename( $file['filename'] ),
					'post_mime_type' => $filetype['type'],
					'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file['filename'] ) ),
					'post_content'   => '',
					'post_status'    => 'inherit'
				);

				// Insert the attachment.
				$attach_id = wp_insert_attachment( $attachment, $file['filename'], $post_id );

				// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
				require_once( ABSPATH . 'wp-admin/includes/image.php' );

				// Generate the metadata for the attachment, and update the database record.
				$attach_data = wp_generate_attachment_metadata( $attach_id, $file['filename'] );
				wp_update_attachment_metadata( $attach_id, $attach_data );
				update_post_meta( $attach_id, '_mfd_field_key', $file['field_key'] );

				$file['attachment_id'] = $attach_id;
			}

		}
		public static function do_action( $params, $form ) {

			$post_type       = get_post_meta( $form->id, '_write_post_action_post_type', true );
			$post_status     = get_post_meta( $form->id, '_write_post_action_post_status', true );
			$post_categories = get_post_meta( $form->id, '_write_post_action_post_category', true );

			if ( empty( $post_type ) || ! is_array( $post_type ) ) {
				throw new Exception( __( '포스트 타입이 지정되지 않았습니다.', 'mshop-members-s2' ) );
			}

			if ( empty( $post_status ) || ! is_array( $post_status ) ) {
				throw new Exception( __( '포스트 상태가 지정되지 않았습니다.', 'mshop-members-s2' ) );
			}

			$post_type   = key( $post_type );
			$post_status = key( $post_status );

			do_action( 'msm-before-write-post-action-' . $post_type, $params, $form );

			$params = apply_filters( 'msm-write-post-action-params-' . $post_type, $params, $form );

			$args = array(
				'post_title'   => urldecode( $params['post_title'] ),
				'post_content' => urldecode( $params['post_content'] ),
				'post_type'    => $post_type,
				'post_status'  => $post_status
			);

			$args = apply_filters( 'msm_write_post_data', $args, $params, $form );

			if ( ! empty( $params['_msm_postid'] ) ) {
				$args['ID'] = $params['_msm_postid'];
				unset( $params['_msm_postid'] );
				$post_id = wp_update_post( $args );
			} else {
				$post_id = wp_insert_post( $args );
			}

			if ( is_wp_error( $post_id ) ) {
				throw new Exception( __( '포스트 등록중 오류가 발생했습니다.' ), 'mshop-members-s2' );
			}

			if ( ! empty( $post_categories ) ) {
				wp_set_post_categories( $post_id, array_keys( $post_categories ) );
			}

			MSM_Manager::add_post_processing_data( $form, $params );

			MSM_Meta::update_post_meta( $post_id, MSM_Manager::get_post_processing_data(), '_msm_form', array(
				'except_fields' => array(
					'post_*'
				)
			) );

			do_action( 'msm-after-write-post-action-' . $post_type, $params, $form, $post_id );

			do_action( 'msm-after-write-post-action', $params, $form, $post_id, $post_type );
		}
	}
}

