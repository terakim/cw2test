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

if ( ! class_exists( 'MSM_Settings_certification_interlock' ) ) :

	class MSM_Settings_certification_interlock {

		public static function update_settings() {
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
					self::get_setting_email_authentication(),
					self::get_setting_phone_certification(),
					self::get_access_stibee(),
					self::get_access_mailchimp(),
				)
			);
		}

		public static function get_setting_email_authentication() {
			return array(
				'type'     => 'Page',
				'title'    => '이메일 인증',
				'class'    => 'active',
				'elements' => array(
					array(
						'type'     => 'Section',
						'title'    => '인증 설정',
						'elements' => array(
							array(
								"id"      => "msm_required",
								"title"   => "필수 인증",
								"type"    => "Toggle",
								"default" => "no",
								"desc"    => "이메일 미인증 사용자는 이용이 제한됩니다."
							),
							array(
								"title"     => "접근 허용 경로 설정",
								"id"        => "msm_exception_list",
								"showIf"    => array( 'msm_required' => 'yes' ),
								"className" => "",
								"sortable"  => 'true',
								"editable"  => 'true',
								"repeater"  => 'true',
								"type"      => "SortableTable",
								"elements"  => array(
									array(
										"className" => "one wide column fluid",
										'id'        => "enabled",
										"title"     => __( "활성화", 'mshop-members-s2' ),
										'default'   => '',
										"type"      => "Toggle",
									),
									array(
										"className"   => "thirteen wide column fluid",
										"id"          => "url",
										"title"       => __( "허용 경로", 'mshop-members-s2' ),
										"type"        => "Text",
										"placeholder" => __( "허용 경로를 입력하세요.", 'mshop-members-s2' ),
									)
								)
							),
							array(
								"id"      => "msm_social_except",
								"showIf"  => array( 'msm_required' => 'yes' ),
								"title"   => "소셜 로그인 예외 처리",
								"type"    => "Toggle",
								"default" => "no",
								"desc"    => "활성화 시, 소셜 로그인 사용자는 이메일 인증을 이용하지 않습니다."
							),
						)
					),
					array(
						'type'     => 'Section',
						"showIf"   => array( 'msm_required' => 'yes' ),
						'title'    => '이메일 인증시 회원등급 변경',
						'elements' => array(
							array(
								"id"      => "msm_change_role",
								"title"   => "사용",
								"type"    => "Toggle",
								"default" => "no",
								"desc"    => "이메일 인증 된 사용자의 회원 등급을 변경합니다."
							),
							array(
								"id"          => "msm_target_role",
								"showIf"      => array( "msm_change_role" => "yes" ),
								"title"       => "회원등급",
								"placeholder" => "회원등급을 선택하세요.",
								"className"   => "",
								"type"        => "Select",
								'default'     => '',
								'options'     => apply_filters( 'msm_get_roles', array() ),
							)
						)
					),
					array(
						'type'     => 'Section',
						"showIf"   => array( 'msm_required' => 'yes' ),
						'title'    => '템플릿 색상',
						'elements' => array(
							array(
								'id'          => 'msm_theme_color',
								'title'       => '테마 선택',
								'className'   => '',
								'type'        => 'Select',
								'default'     => "red",
								'placeholder' => "테마 선택",
								"options"     => array(
									"black"  => "검정",
									"blue"   => "파랑",
									"green"  => "초록",
									"orange" => "주황",
									"yellow" => "노랑",
									"red"    => "빨강"
								)
							)
						)
					),
					array(
						'type'     => 'Section',
						"showIf"   => array( 'msm_required' => 'yes' ),
						'title'    => '인증 완료 설정',
						'elements' => array(
							array(
								'id'          => "msm_finish_url",
								"className"   => "four wide column fluid",
								"title"       => "인증완료 후 이동할 URL",
								"type"        => "Text",
								'placeholder' => "인증완료 후 이동시킬 URL을 입력하세요.",
							),
							array(
								'id'          => "msm_finish_text",
								"className"   => "four wide column fluid",
								"title"       => "인증완료 후 표시할 버튼문구",
								"type"        => "Text",
								'placeholder' => "인증완료 후 표시할 버튼문구를 입력하세요.",
							)
						)
					)
				)
			);
		}

		public static function get_setting_phone_certification() {
			if ( class_exists( 'MSSMS_Manager' ) ) {
				ob_start();
				include( 'html/sms-message-guide.php' );
				$guide = ob_get_clean();

				ob_start();
				include( 'html/temporary-password-message-guide.php' );
				$temporary_password_guide = ob_get_clean();

				$pages = get_pages();
				$pages = array_combine( array_column( $pages, 'ID' ), array_column( $pages, 'post_title' ) );

				return array(
					'type'     => 'Page',
					'title'    => '휴대폰 인증',
					'elements' => array(
						array(
							'type'     => 'Section',
							'title'    => '휴대폰 인증',
							'elements' => array(
								array(
									"id"        => "mssms_use_phone_certification",
									"title"     => "사용",
									"className" => "",
									"type"      => "Toggle",
									"default"   => "no",
									"desc"      => __( "문자 또는 알림톡을 이용한 휴대폰 인증 기능을 사용합니다.", "mshop-members-s2" )
								),
							)
						),
						array(
							'type'     => 'Section',
							'title'    => '휴대폰 인증 설정',
							'showIf'   => array( 'mssms_use_phone_certification' => 'yes' ),
							'elements' => array(
								array(
									"id"        => "mssms_phone_certification_method",
									"title"     => "인증 수단",
									"className" => "",
									"type"      => "Select",
									'default'   => 'alimtalk',
									'options'   => array(
										'sms'      => '문자 (LMS)',
										'alimtalk' => '알림톡'
									),
								),
								array(
									"id"        => "mssms_phone_certification_restrict_duplicate",
									"title"     => "중복 가입 제한",
									"className" => "",
									"type"      => "Toggle",
									"default"   => "no",
									"desc"      => __( "이미 가입된 사용자의 휴대폰 번호인 경우 인증을 제한합니다.", "mshop-members-s2" )
								),
								array(
									"id"        => "mssms_phone_certification_sms_template",
									"title"     => __( "인증문자 템플릿", 'mshop-members-s2' ),
									'showIf'    => array( 'mssms_phone_certification_method' => 'sms' ),
									"className" => "center aligned fluid",
									"type"      => "TextArea",
									"default"   => __( "[{쇼핑몰명}] 고객님이 요청하신 인증번호는 [{인증번호}] 입니다. (타인 노출 금지)", "mshop-members-s2" ),
									"rows"      => 3,
									"desc2"     => $guide
								),
								array(
									"id"          => "mssms_phone_certification_alimtalk_template",
									'showIf'      => array( 'mssms_phone_certification_method' => 'alimtalk' ),
									"title"       => "알림톡 템플릿",
									"placeholder" => "휴대폰인증을 위한 템플릿을 선택해주세요.",
									"className"   => "",
									"type"        => "Select",
									'options'     => MSSMS_Manager::get_alimtalk_templates()
								)
							)
						),
						array(
							'type'     => 'Section',
							'title'    => '비회원 인증 설정',
							'showIf'   => array( 'mssms_use_phone_certification' => 'yes' ),
							'elements' => array(
								array(
									"id"        => "mssms_use_phone_certification_for_guest",
									"title"     => "비회원 결제시 휴대폰 인증",
									"className" => "",
									"type"      => "Toggle",
									"default"   => "no",
									"desc"      => __( "<div class='desc2'>비회원 결제 시 약관동의 화면에서 휴대폰 인증을 진행합니다. 인증된 휴대폰 번호는 체크아웃 화면의 휴대폰 필드에 자동으로 설정됩니다.</div>", "mshop-members-s2" )
								)
							)
						),
						array(
							'type'     => 'Section',
							'title'    => '사용 제한 설정',
							'showIf'   => array( 'mssms_use_phone_certification' => 'yes' ),
							'elements' => array(
								array(
									"id"        => "mssms_phone_certification_required",
									"title"     => "필수인증",
									"className" => "",
									"type"      => "Toggle",
									"default"   => "no",
									"desc"      => __( "<div class='desc2'>휴대폰 미인증 사용자는 쇼핑몰 이용이 제한됩니다. 이미 가입된 회원도 반드시 휴대폰 인증을 진행해야 합니다.</div>", "mshop-members-s2" )
								),
								array(
									"id"      => "msm_phone_certification_social_except",
									"showIf"  => array( 'mssms_phone_certification_required' => 'yes' ),
									"title"   => "소셜 로그인 예외 처리",
									"type"    => "Toggle",
									"default" => "no",
									"desc"    => __( "<div class='desc2'>소셜 로그인 사용자는 휴대폰 인증을 하지 않습니다.</div>", "mshop-members-s2" )
								),
								array(
									"id"        => "mssms_phone_certification_only_checkout",
									"showIf"    => array( 'mssms_phone_certification_required' => 'yes' ),
									"title"     => "결제 시 인증",
									"className" => "",
									"type"      => "Toggle",
									"default"   => "no",
									"desc"      => __( "<div class='desc2'>고객이 결제를 진행할때 휴대폰 인증 여부를 체크합니다. 결제 페이지를 제외한 다른 페이지는 이용이 가능합니다.</div>", "mshop-members-s2" )
								),
								array(
									"id"          => "mssms_phone_certification_page_id",
									'showIf'      => array( 'mssms_phone_certification_required' => 'yes' ),
									"title"       => "휴대폰 인증 페이지",
									"placeholder" => "휴대폰 인증 페이지를 선택하세요.",
									"className"   => "",
									"type"        => "Select",
									'options'     => $pages
								)
							)
						),
						array(
							'type'     => 'Section',
							'title'    => '임시 비밀번호 발급 설정',
							'showIf'   => array( 'mssms_use_phone_certification' => 'yes' ),
							'elements' => array(
								array(
									"id"        => "msm_use_issue_temporary_password",
									"title"     => "임시 비밀번호 발급 기능",
									"className" => "",
									"type"      => "Toggle",
									"default"   => "no",
									"desc"      => __( "<div class='desc2'>고객은 임시 비밀번호를 휴대폰으로 받을 수 있습니다.</div>", "mshop-members-s2" )
								),
								array(
									"id"        => "msm_issue_temporary_password_method",
									"title"     => "발송 수단",
									'showIf'    => array( 'msm_use_issue_temporary_password' => 'yes' ),
									"className" => "",
									"type"      => "Select",
									'default'   => 'alimtalk',
									'options'   => array(
										'sms'      => '문자 (LMS)',
										'alimtalk' => '알림톡'
									),
								),
								array(
									"id"        => "msm_issue_temporary_password_sms_template",
									"title"     => __( "인증문자 템플릿", 'mshop-members-s2' ),
									'showIf'    => array( array( 'msm_use_issue_temporary_password' => 'yes' ), array( 'msm_issue_temporary_password_method' => 'sms' ) ),
									"className" => "center aligned fluid",
									"type"      => "TextArea",
									"default"   => __( "[{쇼핑몰명}] 고객님의 임시 비밀번호는 [{임시비밀번호}] 입니다.", "mshop-members-s2" ),
									"rows"      => 3,
									"desc2"     => $temporary_password_guide
								),
								array(
									"id"          => "msm_issue_temporary_password_alimtalk_template",
									'showIf'      => array( array( 'msm_use_issue_temporary_password' => 'yes' ), array( 'msm_issue_temporary_password_method' => 'alimtalk' ) ),
									"title"       => "알림톡 템플릿",
									"placeholder" => "임시 비밀번호 발급을 위한 템플릿을 선택해주세요.",
									"className"   => "",
									"type"        => "Select",
									'options'     => MSSMS_Manager::get_alimtalk_templates()
								)
							)
						)
					)
				);
			} else {
				return array(
					'type'     => 'Page',
					'title'    => '휴대폰 인증',
					'elements' => array(
						array(
							'type'     => 'Section',
							'title'    => '휴대폰 인증 기능 사용 안내',
							'elements' => array(
								array(
									'id'       => 'mssms_requirement_guide',
									'type'     => 'Label',
									'readonly' => 'yes',
									'default'  => '',
									'desc2'    => __( '<div class="desc2">휴대폰 인증 기능을 이용하시려면 "<a target="_blank" href="https://www.codemshop.com/shop/sms_out/">엠샵 문자 알림톡 자동발송 플러그인</a>"이 설치되어 있어야 합니다.</div>', 'mshop-members-s2' ),
								)
							)
						),
					)
				);
			}
		}

		public static function get_access_stibee() {
			return array(
				'type'     => 'Page',
				'title'    => __( "스티비 연동", 'mshop-members-s2' ),
				'elements' => array(
					array(
						'type'     => 'Section',
						'title'    => __( "스티비 연동", 'mshop-members-s2' ),
						'elements' => array(
							array(
								'id'        => 'mshop_members_access_stibee',
								'title'     => __( "활성화", 'mshop-members-s2' ),
								'className' => '',
								'type'      => 'Toggle',
								'default'   => 'no',
								'desc'      => '스티비 연동 기능을 사용합니다.'
							),
							array(
								'id'          => 'mshop_members_stibee_api',
								'showIf'      => array( 'mshop_members_access_stibee' => 'yes' ),
								'title'       => __( "스티비 API", 'mshop-members-s2' ),
								'className'   => 'fluid',
								'type'        => 'Text',
								"placeholder" => __( "스티비 API 키 값을 입력 해주세요. 스티비 -> 계정 및 결제 -> API키 에서 확인하실 수 있습니다.", 'mshop-members-s2' ),
								'default'     => '',
								'desc2'       => __( '<div class="desc2">스티비 API 키를 입력합니다.</div>', 'mshop-members-s2' ),
							),
							array(
								'id'          => 'mshop_members_stibee_lists',
								'showIf'      => array( 'mshop_members_access_stibee' => 'yes' ),
								'title'       => __( "스티비 주소록", 'mshop-members-s2' ),
								'className'   => 'fluid',
								'type'        => 'Text',
								"placeholder" => __( "스티비 주소록의 아이디를 입력 해주세요. 스티비 -> 주소록 -> 목록 페이지의 URL 에서 확인하실 수 있습니다.", 'mshop-members-s2' ),
								'default'     => '',
								'desc2'       => __( '<div class="desc2">스티비 주소록의 아이디를 입력합니다.(예 : https://www.stibee.com/lists/12345/subscribers/S/all -> 12345)</div>', 'mshop-members-s2' ),
							),
							array(
								'id'          => 'mshop_members_stibee_groupid',
								'showIf'      => array( 'mshop_members_access_stibee' => 'yes' ),
								'title'       => __( "스티비 그룹 아이디 (선택사항)", 'mshop-members-s2' ),
								'className'   => 'fluid',
								'type'        => 'Text',
								"placeholder" => __( "스티비 주소록의 그룹 아이디를 입력 해주세요. 스티비 -> 주소록 -> 목록 -> 그룹 페이지의 URL 에서 확인하실 수 있습니다.", 'mshop-members-s2' ),
								'default'     => '',
								'desc2'       => __( '<div class="desc2">스티비 그룹의 아이디를 입력합니다.(예 : https://www.stibee.com/lists/12345/subscribers/S/00012 -> 00012)</div>', 'mshop-members-s2' ),
							),
						)
					)
				)
			);
		}

		public static function get_access_mailchimp() {
			return array(
				'type'     => 'Page',
				'title'    => __( "메일침프 연동", 'mshop-members-s2' ),
				'elements' => array(
					array(
						'type'     => 'Section',
						'title'    => __( "메일침프 연동", 'mshop-members-s2' ),
						'elements' => array(
							array(
								'id'        => 'mshop_members_access_mailchimp',
								'title'     => __( "활성화", 'mshop-members-s2' ),
								'className' => '',
								'type'      => 'Toggle',
								'default'   => 'no',
								'desc'      => __( '메일침프 연동 기능을 사용합니다.', 'mshop-members-s2' ),
							),
							array(
								'id'          => 'mshop_members_mailchimp_api',
								'showIf'      => array( 'mshop_members_access_mailchimp' => 'yes' ),
								'title'       => __( "메일침프 API", 'mshop-members-s2' ),
								'className'   => 'fluid',
								'type'        => 'Text',
								"placeholder" => __( "메일침프 API 키 값을 입력 해주세요.(위치는 매뉴얼을 참고해주세요.)", 'mshop-members-s2' ),
								'default'     => '',
								'desc2'       => __( '<div class="desc2">메일침프 API 키를 입력합니다. |<a target=\'_blank\' href=\'' . "https://manual.codemshop.com/docs/members-s2/stibee/mailchimp/#msm-field-1" . '\'> API 발급 매뉴얼</a></div>', 'mshop-members-s2' ),
							),
							array(
								'id'          => 'mshop_members_mailchimp_prefix',
								'showIf'      => array( 'mshop_members_access_mailchimp' => 'yes' ),
								'title'       => __( "메일침프 프리픽스", 'mshop-members-s2' ),
								'className'   => 'fluid',
								'type'        => 'Text',
								"placeholder" => __( "메일침프 서버 프리픽스를 입력해주세요.", 'mshop-members-s2' ),
								'default'     => '',
								'desc2'       => __( '<div class="desc2">메일침프 서버 프리픽스를 입력합니다.(예 : https://us14.admin.mailchimp.com/ -> us14)</div>', 'mshop-members-s2' ),
							),
							array(
								'id'          => 'mshop_members_mailchimp_list_id',
								'showIf'      => array( 'mshop_members_access_mailchimp' => 'yes' ),
								'title'       => __( "메일침프 리스트 ID", 'mshop-members-s2' ),
								'className'   => 'fluid',
								'type'        => 'Text',
								"placeholder" => __( "메일침프 Audience 아이디를 입력해주세요.(위치는 매뉴얼을 참고해주세요.)", 'mshop-members-s2' ),
								'default'     => '',
								'desc2'       => __( '<div class="desc2">메일침프 리스트 ID를 입력합니다. |<a target=\'_blank\' href=\'' . "https://manual.codemshop.com/docs/members-s2/stibee/mailchimp/#msm-field-2" . '\'> 리스트 ID 매뉴얼</a></div>', 'mshop-members-s2' ),
							),
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
                jQuery(document).ready(function() {
                    jQuery(this).trigger('mshop-setting-manager', ['mshop-setting-wrapper', '100', <?php echo json_encode( MSM_Setting_Helper::get_settings( $settings ) ); ?>, null, null]);
                });
            </script>

            <div id="mshop-setting-wrapper"></div>
			<?php
		}
	}

endif;


