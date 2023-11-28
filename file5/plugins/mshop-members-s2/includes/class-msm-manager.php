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

if ( ! class_exists( 'MSM_Manager' ) ) {

	class MSM_Manager {
		protected static $forms = null;

		static $post_processing_data = array();

		static $_enabled = null;

		static $_use_unsubscribe = null;

		static $_use_sleep_account = null;


		public static function enabled() {
			if ( is_null( self::$_enabled ) ) {
				self::$_enabled = 'yes' == get_option( 'mshop_members_enabled' );
			}

			return self::$_enabled;
		}

		public static function use_unsubscribe() {
			if ( is_null( self::$_use_unsubscribe ) ) {
				self::$_use_unsubscribe = 'yes' == get_option( 'mshop_members_use_unsubscribe' );
			}

			return self::enabled() && self::$_use_unsubscribe;
		}


		public static function use_sleep_account() {
			if ( is_null( self::$_use_sleep_account ) ) {
				self::$_use_sleep_account = 'yes' == get_option( 'mshop_members_use_sleep' );
			}

			return self::enabled() && self::$_use_sleep_account;
		}

		public static function enqueue_script_to_footer() {
			return 'yes' == get_option( 'mshop_members_using_footer_script' );
		}
		public static function get_forms( $posts_per_page = - 1, $offset = 0, $reload = false ) {
			if ( empty( self::$forms ) || $reload ) {
				self::$forms = array();

				// Query Point Rules Data
				$args = array(
					'post_type'      => 'mshop_members_form',
					'posts_per_page' => $posts_per_page,
					'offset'         => $offset,
					'post_status'    => 'publish',
				);

				$query = new WP_Query( $args );

				// Generate Point Rules
				foreach ( $query->posts as $post ) {
					$form = new MSM_Form( $post );

					if ( ! is_wp_error( $form ) ) {
						self::$forms[] = $form;
					}
				}
			}

			return self::$forms;
		}

		public static function get_forms_by_id( $form_ids ) {
			$forms = array();

			foreach ( $form_ids as $form_id ) {
				$form = new MSM_Form( $form_id );

				if ( ! is_wp_error( $form ) ) {
					$forms[] = $form;
				}
			}

			return $forms;
		}

		public static function get_form_by_slug( $slug ) {
			if( ! empty( $slug ) ) {
				$post = get_page_by_path( $slug, OBJECT, 'mshop_members_form' );

				if ( function_exists( 'icl_object_id' ) ) {
					$post = get_post( wpml_object_id_filter( $post->ID, 'mshop_members_form', true, ICL_LANGUAGE_CODE ) );
				}

				if ( $post ) {
					return self::get_form( $post->ID );
				}
			}

			return null;
		}

		public static function get_form( $post_id ) {
			$args = array(
				'post_type'      => 'mshop_members_form',
				'posts_per_page' => - 1,
				'post__in'       => array( $post_id ),
				'post_status'    => 'publish',
			);

			$query = new WP_Query( $args );

			if ( count( $query->posts ) > 0 ) {
				return new MSM_Form( current( $query->posts ) );
			}

			return null;
		}

		public static function update_form_meta( $post_id, $values ) {
			update_post_meta( $post_id, '_form_id', $values['form_id'] );
			update_post_meta( $post_id, '_form_name', $values['form_name'] );
			update_post_meta( $post_id, '_form_type', $values['form_type'] );
			update_post_meta( $post_id, '_redirect_url', $values['redirect_url'] );
		}

		public static function update_form( $values ) {
			if ( empty( $values['post_id'] ) ) {
				$args = array(
					'post_title'   => '#' . $values['form_id'] . ' : ' . $values['form_name'],
					'post_content' => json_encode( $values['form_data'], JSON_UNESCAPED_UNICODE ),
					'post_type'    => 'mshop_members_form',
					'post_status'  => 'publish'
				);

				$post_id = wp_insert_post( $args );
			} else {
				$post_id = wp_update_post( array(
					'ID'           => $values['post_id'],
					'post_title'   => '#' . $values['form_id'] . ' : ' . $values['form_name'],
					'post_content' => json_encode( $values['form_data'], JSON_UNESCAPED_UNICODE ),
				) );
			}

			if ( ! is_wp_error( $post_id ) ) {
				self::update_form_meta( $post_id, $values );
			}
		}
		public static function get_terms_and_conditions( $slug ) {
			$args = array(
				'post_type'      => 'mshop_agreement',
				'posts_per_page' => - 1,
				'post_status'    => 'publish',
				'tax_query'      => array(
					array(
						'taxonomy' => 'mshop_agreement_cat',
						'field'    => 'slug',
						'terms'    => $slug,
					),
				),
			);

			$query = new WP_Query( $args );

			return $query->posts;
		}

		public static function mshop_members_register_form() {
			echo '<input type="hidden" name="mshop_members_agree_terms_and_conditions" id="mshop_members_agree_terms_and_conditions"/>';
		}

		public static function get_members_form_list( $term = null ) {
			$forms = array();

			$args = array(
				'post_type'      => 'mshop_members_form',
				'posts_per_page' => - 1,
				'post_status'    => 'publish',
				'orderby'        => 'title',
				'order'          => 'ASC'
			);

			if ( ! is_null( $term ) ) {
				$args['tax_query'] = array(
					array(
						'taxonomy' => 'mshop_members_form_cat',
						'field'    => 'slug',
						'terms'    => $term
					)
				);
			}

			$query = new WP_Query( $args );

			foreach ( $query->posts as $post ) {
				$forms[ $post->ID ] = $post->post_title;
			}

			return $forms;
		}

		static function check_meta_condition( $metas ) {
			$user_id = get_current_user_id();

			foreach ( $metas as $meta ) {
				$result     = true;
				$user_value = get_user_meta( $user_id, $meta['meta_key'], true );
				$meta_value = $meta['meta_value'];

				switch ( $meta['compare'] ) {
					case 'equal' :
						$result = $user_value == $meta_value;
						break;
					case 'not equal' :
						$result = $user_value != $meta_value;
						break;
				}

				if ( ! $result ) {
					return false;
				}
			}

			return true;
		}

		public static function get_members_forms( $forms, $term ) {
			$args = array(
				'post_type'      => 'mshop_members_form',
				'posts_per_page' => - 1,
				'post_status'    => 'publish',
				'orderby'        => 'title',
				'order'          => 'ASC'
			);

			if ( ! is_null( $term ) ) {
				$args['tax_query'] = array(
					array(
						'taxonomy' => 'mshop_members_form_cat',
						'field'    => 'slug',
						'terms'    => $term
					)
				);
			}

			$query = new WP_Query( $args );

			foreach ( $query->posts as $post ) {
				$forms[ $post->ID ] = $post->post_title;
			}

			return $forms;
		}

		public static function get_post_processing_data() {
			return self::$post_processing_data;
		}

		public static function add_post_processing_data( $form, $params ) {
			self::$post_processing_data[] = array(
				'form'   => $form,
				'params' => $params
			);
		}

	}
}