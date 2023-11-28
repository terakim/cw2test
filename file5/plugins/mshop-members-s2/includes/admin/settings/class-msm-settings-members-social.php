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

if ( ! class_exists( 'MSM_Settings_Members_Social' ) ) :

	class MSM_Settings_Members_Social {

		static function update_settings() {
			require_once MSM()->plugin_path() . '/includes/admin/setting-manager/mshop-setting-helper.php';

			$_REQUEST = array_merge( $_REQUEST, json_decode( stripslashes( $_REQUEST['values'] ), true ) );

			MSM_Setting_Helper::update_settings( self::get_setting_fields() );

			wp_send_json_success();
		}

		public static function get_setting_fields() {
			return array(
				'type'     => 'Tab',
				'id'       => 'mshop-members-setting-tab',
				'elements' => array(
					self::get_setting_main_tab(),
					self::get_setting_social_login(),
				)
			);
		}

		public static function get_setting_main_tab() {
			if ( function_exists( 'wc_get_page_permalink' ) ) {
				$redirect_url = wc_get_page_permalink( 'myaccount' );
			} else {
				$redirect_url = home_url();
			}

			return array(
				'type'     => 'Page',
				'title'    => '기본 설정',
				'class'    => 'active',
				'elements' => array(
					array(
						'type'     => 'Section',
						'title'    => '기본설정',
						'elements' => array(
							array(
								'id'        => 'msm_oauth_redirect_url',
								'title'     => __( '소셜로그인 후 이동할 페이지', 'mshop-members-s2' ),
								'className' => 'fluid',
								'type'      => 'Text',
								'default'   => $redirect_url
							),
						)
					),
					array(
						'type'     => 'Section',
						'title'    => __( '소셜 회원가입 후 추가정보 입력', 'mshop-members-s2' ),
						'elements' => array(
							array(
								'id'        => 'msm_use_bouncer',
								'title'     => __( '사용', 'mshop-members-s2' ),
								'className' => '',
								'type'      => 'Toggle',
								'default'   => 'no',
								'desc'      => '<div class="desc2">소셜로그인을 이용한 회원가입 시, 사용자의 추가 정보를 입력받습니다.</div>'
							),
							array(
								"id"           => "msm_bouncer_page",
								'title'        => __( '추가정보 입력 화면', 'mshop-members-s2' ),
								'showIf'       => array( 'msm_use_bouncer' => 'yes' ),
								"placeholder"  => __( "추가정보 입력화면을 선택하세요.", 'mshop-members-s2' ),
								"className"    => "search",
								"type"         => "SearchSelect",
								'default'      => '',
								'multiple'     => false,
								'search'       => true,
								'disableClear' => true,
								'action'       => 'action=' . msm_ajax_command( 'search_page&keyword=' ),

							)
						)
					)
				)
			);
		}

		static function get_setting_social_login() {
			$settings = array(
				'type'     => 'Page',
				'title'    => __( '소셜로그인', 'mshop-members-s2' ),
				'class'    => '',
				'elements' => apply_filters( 'msm_settings_social_login', array(
						array(
							'type'     => 'Section',
							'title'    => __( '페이스북 ( Facebook )', 'mshop-members-s2' ),
							'elements' => array(
								array(
									'id'        => 'msm_oauth_facebook_enabled',
									'title'     => '활성화',
									'className' => '',
									'type'      => 'Toggle',
									'default'   => 'no',
									'desc'      => '<div class="desc2"><a href="https://manual.codemshop.com/docs/sns/facebook-social-login/" target="_blank">페이스북 소셜로그인 설정 가이드 <i class="file alternate outline icon"></i></a></div>'
								),
								array(
									'id'        => 'msm_oauth_facebook_client_id',
									'title'     => __( '앱 ID', 'mshop-members-s2' ),
									'showIf'    => array( 'msm_oauth_facebook_enabled' => 'yes' ),
									'className' => 'fluid',
									'type'      => 'Text',
									'default'   => ''
								),
								array(
									'id'        => 'msm_oauth_facebook_client_secret',
									'title'     => __( '앱 시크릿 코드', 'mshop-members-s2' ),
									'showIf'    => array( 'msm_oauth_facebook_enabled' => 'yes' ),
									'type'      => 'Text',
									'className' => 'fluid',
									'default'   => ''
								),
								array(
									'id'       => 'msm_oauth_facebook_redirect_uri',
									'title'    => __( '리다이렉트 URI', 'mshop-members-s2' ),
									'showIf'   => array( 'msm_oauth_facebook_enabled' => 'yes' ),
									'type'     => 'Label',
									'readonly' => 'yes',
									'default'  => home_url() . '/msm_facebook'
								),
							)
						),
						array(
							'type'     => 'Section',
							'title'    => __( '구글 ( Google )', 'mshop-members-s2' ),
							'elements' => array(
								array(
									'id'        => 'msm_oauth_google_enabled',
									'title'     => '활성화',
									'className' => '',
									'type'      => 'Toggle',
									'default'   => 'no',
									'desc'      => '<div class="desc2"><a href="https://manual.codemshop.com/docs/sns/google-social-login/" target="_blank">구글 소셜로그인 설정 가이드 <i class="file alternate outline icon"></i></a></div>'
								),
								array(
									'id'        => 'msm_oauth_google_client_id',
									'title'     => __( '클라이언트 ID', 'mshop-members-s2' ),
									'showIf'    => array( 'msm_oauth_google_enabled' => 'yes' ),
									'className' => 'fluid',
									'type'      => 'Text',
									'default'   => ''
								),
								array(
									'id'        => 'msm_oauth_google_client_secret',
									'title'     => __( '클라이언트 시크릿', 'mshop-members-s2' ),
									'showIf'    => array( 'msm_oauth_google_enabled' => 'yes' ),
									'type'      => 'Text',
									'className' => 'fluid',
									'default'   => ''
								),
								array(
									'id'       => 'msm_oauth_google_redirect_uri',
									'title'    => __( '리다이렉트 URI', 'mshop-members-s2' ),
									'showIf'   => array( 'msm_oauth_google_enabled' => 'yes' ),
									'type'     => 'Label',
									'readonly' => 'yes',
									'default'  => home_url() . '/msm_google'
								),
							)
						),
						array(
							'type'     => 'Section',
							'title'    => __( '인스타그램 ( Instagram )', 'mshop-members-s2' ),
							'elements' => array(
								array(
									'id'        => 'msm_oauth_instagram_enabled',
									'title'     => '활성화',
									'className' => '',
									'type'      => 'Toggle',
									'default'   => 'no',
									'desc'      => '<div class="desc2"><a href="https://manual.codemshop.com/docs/sns/instagram-social-login/" target="_blank">인스타그램 소셜로그인 설정 가이드 <i class="file alternate outline icon"></i></a></div>'
								),
								array(
									'id'        => 'msm_oauth_instagram_client_id',
									'title'     => __( '앱 ID', 'mshop-members-s2' ),
									'showIf'    => array( 'msm_oauth_instagram_enabled' => 'yes' ),
									'className' => 'fluid',
									'type'      => 'Text',
									'default'   => ''
								),
								array(
									'id'        => 'msm_oauth_instagram_client_secret',
									'title'     => __( '앱 시크릿 코드', 'mshop-members-s2' ),
									'showIf'    => array( 'msm_oauth_instagram_enabled' => 'yes' ),
									'type'      => 'Text',
									'className' => 'fluid',
									'default'   => ''
								),
								array(
									'id'       => 'msm_oauth_instagram_redirect_uri',
									'title'    => __( '리다이렉트 URI', 'mshop-members-s2' ),
									'showIf'   => array( 'msm_oauth_instagram_enabled' => 'yes' ),
									'type'     => 'Label',
									'readonly' => 'yes',
									'default'  => home_url() . '/msm_instagram'
								),
							)
						),
						array(
							'type'     => 'Section',
							'title'    => __( '네이버 ( Naver )', 'mshop-members-s2' ),
							'elements' => array(
								array(
									'id'        => 'msm_oauth_naver_enabled',
									'title'     => '활성화',
									'className' => '',
									'type'      => 'Toggle',
									'default'   => 'no',
									'desc'      => '<div class="desc2"><a href="https://manual.codemshop.com/docs/sns/naver-social-login/" target="_blank">네이버 소셜로그인 설정 가이드 <i class="file alternate outline icon"></i></a></div>'
								),
								array(
									'id'        => 'msm_oauth_naver_client_id',
									'title'     => __( '클라이언트 ID', 'mshop-members-s2' ),
									'showIf'    => array( 'msm_oauth_naver_enabled' => 'yes' ),
									'className' => 'fluid',
									'type'      => 'Text',
									'default'   => ''
								),
								array(
									'id'        => 'msm_oauth_naver_client_secret',
									'title'     => __( '클라이언트 시크릿', 'mshop-members-s2' ),
									'showIf'    => array( 'msm_oauth_naver_enabled' => 'yes' ),
									'type'      => 'Text',
									'className' => 'fluid',
									'default'   => ''
								),
								array(
									'id'       => 'msm_oauth_naver_redirect_uri',
									'title'    => __( '리다이렉트 URI', 'mshop-members-s2' ),
									'showIf'   => array( 'msm_oauth_naver_enabled' => 'yes' ),
									'type'     => 'Label',
									'readonly' => 'yes',
									'default'  => home_url() . '/msm_naver'
								),
							)
						),
						array(
							'type'     => 'Section',
							'title'    => __( '카카오 ( Kakao )', 'mshop-members-s2' ),
							'elements' => array(
								array(
									'id'        => 'msm_oauth_kakao_enabled',
									'title'     => '활성화',
									'className' => '',
									'type'      => 'Toggle',
									'default'   => 'no',
									'desc'      => '<div class="desc2"><a href="https://manual.codemshop.com/docs/sns/kakao-social-login/" target="_blank">카카오 소셜로그인 설정 가이드 <i class="file alternate outline icon"></i></a></div>'
								),
                                array(
                                    'id'        => 'msm_oauth_kakao_logout_enabled',
                                    'title'     => '카카오 로그아웃 기능 사용',
                                    'showIf'    => array( 'msm_oauth_kakao_enabled' => 'yes' ),
                                    'className' => '',
                                    'type'      => 'Toggle',
                                    'default'   => 'no',
                                    'desc'      => '<div class="desc2"><a href="https://manual.codemshop.com/docs/sns/kakao-social-login/#msm-logout" target="_blank">카카오 소셜로그인 설정 가이드 - 로그아웃<i class="file alternate outline icon"></i></a></div>'
                                ),
								array(
									'id'        => 'msm_oauth_kakao_sync',
									'title'     => '카카오싱크 사용',
									'showIf'    => array( 'msm_oauth_kakao_enabled' => 'yes' ),
									'className' => '',
									'type'      => 'Toggle',
									'default'   => 'no',
									'desc'      => '<div class="desc2"><a href="https://manual.codemshop.com/docs/sns/kakao-social-login/kakao-sync/" target="_blank">카카오싱크 설정 가이드 <i class="file alternate outline icon"></i></a></div>'
								),
								array(
									"id"           => "msm_bouncer_page_kakao",
									'title'        => __( '추가정보 입력 화면', 'mshop-members-s2' ),
									'showIf'       => array( array( 'msm_use_bouncer' => 'yes' ), array( 'msm_oauth_kakao_sync' => 'yes' ) ),
									"placeholder"  => __( "추가정보 입력화면을 선택하세요.", 'mshop-members-s2' ),
									"className"    => "search",
									"type"         => "SearchSelect",
									'default'      => get_option( 'msm_bouncer_page' ),
									'multiple'     => false,
									'search'       => true,
									'disableClear' => true,
									'action'       => 'action=' . msm_ajax_command( 'search_page&keyword=' ),

								),
								array(
									'id'        => 'msm_oauth_kakao_client_id',
									'title'     => __( 'REST API 키', 'mshop-members-s2' ),
									'showIf'    => array( 'msm_oauth_kakao_enabled' => 'yes' ),
									'className' => 'fluid',
									'type'      => 'Text',
									'default'   => ''
								),
								array(
									'id'       => 'msm_oauth_kakao_redirect_uri',
									'title'    => __( '리다이렉트 URI', 'mshop-members-s2' ),
									'showIf'   => array( 'msm_oauth_kakao_enabled' => 'yes' ),
									'type'     => 'Label',
									'readonly' => 'yes',
									'default'  => home_url() . '/msm_kakao'
								),
								array(
									"id"       => "msm_oauth_kakao_scope",
									"title"    => __( '요청 권한', "mshop-members-s2" ),
									"type"     => "Select",
									"multiple" => "true",
									'default'  => 'profile_nickname',
									'options'  => array(
										'profile'          => '사용자 기본정보',
										'account_email'    => '이메일',
										'phone_number'     => '전화번호',
										'gender'           => '성별',
										'age_range'        => '연령대',
										'name'             => '이름',
										'profile_nickname' => '닉네임',
										'profile_image'    => '프로필 이미지',
										'shipping_address' => '배송지정보',
										'birthyear'        => '출생 연도',
										'birthday'         => '생일',
										'account_ci'       => 'CI(연계정보)'
									),
									'desc2'    => '<div class="desc2">이메일과 전화번호의 경우, 앱 설정에 해당 권한이 설정되지 않은 경우 오류가 발생하니 주의해주시기 바랍니다.</div>'
								),
							)
						),
						array(
							'type'     => 'Section',
							'title'    => __( '라인 ( Line )', 'mshop-members-s2' ),
							'elements' => array(
								array(
									'id'        => 'msm_oauth_line_enabled',
									'title'     => '활성화',
									'className' => '',
									'type'      => 'Toggle',
									'default'   => 'no',
									'desc'      => '<div class="desc2"><a href="https://manual.codemshop.com/docs/sns/line-social-login/" target="_blank">라인 소셜로그인 설정 가이드 <i class="file alternate outline icon"></i></a></div>'
								),
								array(
									'id'        => 'msm_oauth_line_client_id',
									'title'     => __( '채널 ID', 'mshop-members-s2' ),
									'showIf'    => array( 'msm_oauth_line_enabled' => 'yes' ),
									'className' => 'fluid',
									'type'      => 'Text',
									'default'   => ''
								),
								array(
									'id'        => 'msm_oauth_line_client_secret',
									'title'     => __( '채널 시크릿', 'mshop-members-s2' ),
									'showIf'    => array( 'msm_oauth_line_enabled' => 'yes' ),
									'type'      => 'Text',
									'className' => 'fluid',
									'default'   => ''
								),
								array(
									'id'       => 'msm_oauth_line_redirect_uri',
									'title'    => __( '리다이렉트 URI', 'mshop-members-s2' ),
									'showIf'   => array( 'msm_oauth_line_enabled' => 'yes' ),
									'type'     => 'Label',
									'readonly' => 'yes',
									'default'  => home_url() . '/msm_line'
								),
							)
						),
						array(
							'type'     => 'Section',
							'title'    => __( '애플 ( Apple )', 'mshop-members-s2' ),
							'elements' => array(
								array(
									'id'        => 'msm_oauth_apple_enabled',
									'title'     => '활성화',
									'className' => '',
									'type'      => 'Toggle',
									'default'   => 'no',
									'desc'      => '<div class="desc2"><a href="https://manual.codemshop.com/docs/sns/apple-social-login/" target="_blank">애플 소셜로그인 설정 가이드 <i class="file alternate outline icon"></i></a></div>'
								),
								array(
									'id'        => 'msm_oauth_apple_client_id',
									'title'     => __( '식별 ID', 'mshop-members-s2' ),
									'showIf'    => array( 'msm_oauth_apple_enabled' => 'yes' ),
									'className' => 'fluid',
									'type'      => 'Text',
									'default'   => ''
								),
								array(
									'id'        => 'msm_oauth_apple_app_id',
									'title'     => __( '앱 ID', 'mshop-members-s2' ),
									'showIf'    => array( 'msm_oauth_apple_enabled' => 'yes' ),
									'className' => 'fluid',
									'type'      => 'Text',
									'default'   => ''
								),
								array(
									'id'        => 'msm_oauth_apple_key_id',
									'title'     => __( '키 ID', 'mshop-members-s2' ),
									'showIf'    => array( 'msm_oauth_apple_enabled' => 'yes' ),
									'className' => 'fluid',
									'type'      => 'Text',
									'default'   => ''
								),
								array(
									'id'        => 'msm_oauth_apple_private_key',
									'title'     => __( '키 내용', 'mshop-members-s2' ),
									'showIf'    => array( 'msm_oauth_apple_enabled' => 'yes' ),
									'className' => 'fluid',
									'type'      => 'TextArea',
									'default'   => ''
								),
								array(
									'id'       => 'msm_oauth_apple_redirect_uri',
									'title'    => __( '리다이렉트 URI', 'mshop-members-s2' ),
									'showIf'   => array( 'msm_oauth_apple_enabled' => 'yes' ),
									'type'     => 'Label',
									'readonly' => 'yes',
									'default'  => home_url() . '/msm_apple'
								),
							)
						)
					)
				)
			);

			return $settings;
		}

		static function enqueue_scripts() {
			wp_enqueue_style( 'mshop-setting-manager', MSM()->plugin_url() . '/includes/admin/setting-manager/css/setting-manager.min.css', array(), MSM_VERSION );
			wp_enqueue_script( 'mshop-setting-manager', MSM()->plugin_url() . '/includes/admin/setting-manager/js/setting-manager.min.js', array( 'jquery', 'jquery-ui-core', 'underscore' ), MSM_VERSION );
		}
		public static function output() {
			require_once MSM()->plugin_path() . '/includes/admin/setting-manager/mshop-setting-helper.php';

			$settings = self::get_setting_fields();

			self::enqueue_scripts();

			wp_localize_script( 'mshop-setting-manager', 'mshop_setting_manager', array(
				'element'  => 'mshop-setting-wrapper',
				'ajaxurl'  => admin_url( 'admin-ajax.php' ),
				'action'   => msm_ajax_command( 'update_settings_social' ),
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