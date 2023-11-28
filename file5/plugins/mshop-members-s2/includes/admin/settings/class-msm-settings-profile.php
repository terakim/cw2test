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

if ( ! class_exists( 'MSM_Settings_Profile' ) ) :

	class MSM_Settings_Profile {
		static function update_settings() {
			include_once MSM()->plugin_path() . '/includes/admin/setting-manager/mshop-setting-helper.php';

			$_REQUEST = array_merge( $_REQUEST, json_decode( stripslashes( $_REQUEST['values'] ), true ) );

			MSM_Setting_Helper::update_settings( self::get_setting_fields() );

			wp_send_json_success();
		}

		static function get_pages() {
			$pages = array();

			$args = array(
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'orderby'        => 'ID',
				'order'          => 'ASC',
				'posts_per_page' => - 1
			);

			$query = new WP_Query( $args );

			foreach ( $query->posts as $post ) {
				$pages[ $post->ID ] = $post->post_title;
			}

			return $pages;
		}

		static function get_setting_fields() {
			return array(
				'type'     => 'Page',
				'title'    => '프로필 설정',
				'class'    => '',
				'elements' => array(
					array(
						'type'     => 'Section',
						'title'    => '프로필 설정',
						'elements' => array(
							array(
								"id"      => "msm_profile_hide_edit_account",
								"title"   => "계정상세 페이지 숨김",
								"type"    => "Toggle",
								"default" => "yes",
								"desc"    => "우커머스의 기본 계정상세 탭을 숨깁니다."
							),
							array(
								"id"      => "msm_profile_image_review",
								"title"   => "프로필 이미지",
								"type"    => "Toggle",
								"default" => "no",
								"desc"    => "회원은 프로필 이미지를 직접 설정할 수 있습니다. <a href='https://manual.codemshop.com/docs/members-s2/profile/image' target='_blank'>프로필 업로드 설정 가이드 <i class='file alternate outline icon'></i></a>"
							),
							array(
								"id"        => "msm_profile_page_setting",
								"title"     => "프로필 페이지",
								"className" => "",
								"editable"  => 'true',
								"sortable"  => "true",
								"repeater"  => true,
								"type"      => "SortableTable",
								"template"  => array(
									"user_roles"   => "",
									"social_login" => "include",
									"edit_form_id" => ""
								),
								"elements"  => array(
									array(
										"id"          => "user_roles",
										"title"       => __( "회원 등급", 'mshop-members-s2' ),
										"className"   => "five wide column fluid",
										"type"        => "Select",
										"multiple"    => "true",
										"placeholder" => "회원 등급을 선택하세요.",
										'options'     => apply_filters( 'msm_get_roles', array() ),
									),
									array(
										"id"          => "social_login",
										"title"       => __( "소셜로그인", 'mshop-members-s2' ),
										"className"   => "two wide column fluid",
										"type"        => "Select",
										"placeholder" => "소셜로그인",
										'options'     => array(
											'include' => __( '포함', 'mshop-members-s2' ),
											'exclude' => __( '제외', 'mshop-members-s2' ),
										),
									),
									array(
										"id"          => "edit_form_id",
										"title"       => __( "프로필 편집 폼", 'mshop-members-s2' ),
										"className"   => "four wide column fluid",
										"type"        => "Select",
										"placeholder" => "프로필 편집 폼을 선택하세요.",
										'options'     => msm_get_members_forms(),
									),
									array(
										"id"          => "view_form_id",
										"title"       => __( "프로필 조회 폼", 'mshop-members-s2' ),
										"className"   => "four wide column fluid",
										"type"        => "Select",
										"placeholder" => "프로필 조회 폼을 선택하세요.",
										'options'     => msm_get_members_forms(),
									)
								)
							)
						)
					)
				)
			);
		}

		static function enqueue_scripts() {
			wp_enqueue_style( 'mshop-setting-manager', MSM()->plugin_url() . '/includes/admin/setting-manager/css/setting-manager.min.css', array(), MSM_VERSION );
			wp_enqueue_script( 'mshop-setting-manager', MSM()->plugin_url() . '/includes/admin/setting-manager/js/setting-manager.min.js', array( 'jquery', 'jquery-ui-core', 'underscore' ), MSM_VERSION );
		}
		public static function output() {
			require_once( ABSPATH . 'wp-admin/includes/dashboard.php' );

			require_once MSM()->plugin_path() . '/includes/admin/setting-manager/mshop-setting-helper.php';

			$settings = self::get_setting_fields();

			self::enqueue_scripts();

			wp_localize_script( 'mshop-setting-manager', 'mshop_setting_manager', array(
				'element'  => 'mshop-setting-wrapper',
				'ajaxurl'  => admin_url( 'admin-ajax.php' ),
				'action'   => msm_ajax_command( 'update_profile_settings' ),
				'settings' => $settings,
			) );

			?>
            <script>
                jQuery( document ).ready( function () {
                    jQuery( this ).trigger( 'mshop-setting-manager', ['mshop-setting-wrapper', '100', <?php echo json_encode( MSM_Setting_Helper::get_settings( $settings ) ); ?>, null, null] );
                } );
            </script>

            <div id="mshop-setting-wrapper"></div>
			<?php
		}
	}

endif;