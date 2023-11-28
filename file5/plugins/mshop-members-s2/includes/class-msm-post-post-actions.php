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

if ( ! class_exists( 'MSM_Post_Post_Actions' ) ) :

	class MSM_Post_Post_Actions {

		static $post_id = 0;

		protected static function get_categories() {
			$categories = array ();

			$terms = get_categories( array ( 'taxonomy' => 'msm_post_cat', 'hide_empty' => false ) );

			foreach ( $terms as $term ) {
				$categories[ $term->slug ] = $term->name;
			}

			return $categories;
		}

		public static function post_actions( $actions ) {
			$actions['email']                   = __( '이메일 발송', 'mshop-members-s2' );
			$actions['msm_post_category']       = __( '엠샵 포스트 - 카테고리 설정', 'mshop-members-s2' );
			$actions['msm_post_update_content'] = __( '엠샵 포스트 - 컨텐츠 설정', 'mshop-members-s2' );

			return $actions;
		}

		public static function post_actions_settings( $settings ) {
			$settings['email'] = array (
				'type'              => 'Section',
				"hideSectionHeader" => true,
				'elements'          => array (
					array (
						"id"          => "email_subject",
						"title"       => "이메일 제목",
						"className"   => "four wide column fluid",
						"placeHolder" => "이메일 제목을 입력하세요.",
						"type"        => "Text",
					),
					array (
						"id"          => "email_recipient",
						"title"       => "수신자 주소",
						"className"   => "four wide column fluid",
						"placeHolder" => "수신자 주소를 입력하세요.",
						"type"        => "Text",
					),
				)
			);

			$settings['msm_post_category'] = array (
				'type'              => 'Section',
				"hideSectionHeader" => true,
				'elements'          => array (
					array (
						"id"          => "category",
						"title"       => "캬테고리 설정",
						'className'   => 'search fluid',
						"placeHolder" => "포스트 카테고리를 선택하세요.",
						"type"        => "Select",
						"multiple"    => true,
						"options"     => self::get_categories()
					),
				)
			);

			$settings['msm_post_update_content'] = array (
				'type'              => 'Section',
				"hideSectionHeader" => true,
				'elements'          => array (
					array (
						'id'          => 'post_title',
						'title'       => '포스트 제목',
						'className'   => 'search fluid',
						"placeHolder" => "포스트 제목을 입력하세요.",
						"type"        => "Text",
						"desc2"       => __( '<div class="desc2">포스트 제목의 포맷을 입력하세요. {폼 위젯 아이디}를 입력하시면 사용자가 입력한 값으로 치환됩니다.</div>', 'mshop-members-s2' ),
					),
					array (
						"id"        => "post_content",
						'title'     => '목룍 표시 필드',
						"className" => "",
						"sortable"  => 'true',
						"editable"  => 'true',
						"repeater"  => 'true',
						"type"      => "SortableTable",
						"template"  => array (
							'id' => '',
						),
						"elements"  => array (
							array (
								"className" => "fourteen wide column fluid",
								'id'        => "id",
								"title"     => __( "아이디", 'mshop-members-s2' ),
								'default'   => '',
								"type"      => "Text",
							)
						)
					)
				)
			);

			return $settings;
		}

		public static function send_email( $response, $form, $action, $params ) {
			do_action( 'msm_post_post_email', $params, $action, self::$post_id );
		}

		public static function save_post_id( $params, $form, $post_id, $post_type = '' ) {
			self::$post_id = $post_id;
		}
		public static function set_post_category( $response, $form, $action, $params ) {
			wp_set_object_terms( self::$post_id, explode( ',', msm_get( $action, 'category' ) ), 'msm_post_cat' );

			return $response;
		}
		public static function get_post_content( $response, $form, $action, $params ) {
			$post_content = msm_get( $params, 'post_content' );

			if ( empty( $post_content ) && ! empty( msm_get( $action, 'post_content' ) ) ) {
				$ids = wp_list_pluck( $action['post_content'], 'id' );

				$metas = MSM_Meta::get_post_meta( self::$post_id, '_msm_form' );

				$matched_metas = array ();
				foreach ( $metas as $meta ) {
					if ( in_array( $meta['name'], $ids ) && ! empty( $meta['title'] ) ) {
						$matched_metas[ $meta['name'] ] = $meta;
					}
				}


				$post_content = '<table class="application_info">';
				foreach ( $ids as $id ) {
					if ( ! empty( $matched_metas[ $id ] ) ) {
						$meta         = $matched_metas[ $id ];
						$post_content .= '<tr>';
						$post_content .= '<td class="meta_key">' . $meta['title'] . '</td>';
						$post_content .= '<td class="meta_value">' . str_replace( "\n", "<br>", ! empty( $meta['label'] ) ? $meta['label'] : $meta['value'] ) . '</td>';
						$post_content .= '</tr>';
					}
				}

				$post_content .= '</table>';
			}

			return $post_content;
		}
		public static function update_content( $response, $form, $action, $params ) {
			$post_title = msm_get( $action, 'post_title' );

			if ( ! empty( $post_title ) ) {

				$metas = MSM_Meta::get_post_meta( self::$post_id, '_msm_form' );
				$metas = array_combine( wp_list_pluck( $metas, 'name' ), $metas );

				foreach ( $params as $key => $value ) {
					if( ! is_string( $value ) ) {
						continue;
					}

					$value = ! empty( $metas[ $key ]['label'] ) ? $metas[ $key ]['label'] : $value;
					$post_title = str_replace( '{' . $key . '}', $value, $post_title );
				}

				wp_update_post( array (
					'ID'           => self::$post_id,
					'post_title'   => $post_title,
					'post_content' => self::get_post_content( $response, $form, $action, $params )
				) );
			}

			return $response;
		}

	}

endif;

