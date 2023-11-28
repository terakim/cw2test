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

if ( ! class_exists( 'MSM_Settings_Members' ) ) :

	class MSM_Settings_Members {

		function update_settings() {
			require_once MSM()->plugin_path() . '/includes/admin/setting-manager/mshop-setting-helper.php';

			$_REQUEST = array_merge( $_REQUEST, json_decode( stripslashes( $_REQUEST['values'] ), true ) );

			MSM_Setting_Helper::update_settings( $this->get_setting_fields() );

			if ( 'yes' == $_REQUEST['mshop_members_personal_info_noti'] ) {
				MSM_Personal_Info::maybe_register_scheduled_action();
			} else {
				MSM_Personal_Info::maybe_deregister_scheduled_action();
				delete_transient( 'msm_personal_information_notification' );
			}


			if ( 'yes' == get_option( 'msm_user_agreement_info_noti_enable' ) && ! empty( get_option( 'msm_user_agreement_info_noti_method' ) ) ) {
				MSM_User_Agreement_Info::maybe_register_scheduled_action();
			} else {
				MSM_User_Agreement_Info::maybe_deregister_scheduled_action();
				delete_transient( 'msm_agreement_information_notification' );
			}

            $agreemt_notice = false;
            if ( class_exists( 'MSSMS_Manager' ) ) {
                $noti_methods   = array ( 'change', 'info' );
                foreach ( $noti_methods as $noti_method ) {
                    if ( 'yes' == get_option( 'msm_user_agreement_' . $noti_method . '_noti_enable' ) ) {
                        $send_methods = get_option( 'msm_user_agreement_' . $noti_method . '_noti_method' );
                        $send_methods = explode( ',', $send_methods );
                        $sms_methods  = array ( 'alimtalk', 'sms' );

                        foreach ( $sms_methods as $sms_method ) {
                            if ( in_array( $sms_method, $send_methods ) ) {
                                if ( empty( get_option( 'msm_user_agreement_' . $noti_method . '_noti_' . $sms_method ) ) ) {
                                    $agreemt_notice = true;
                                }
                            }
                        }

                    }
                }
            }

            if( $agreemt_notice ) {
                wp_send_json_error('수신 동의 설정에 문제가 없는지 다시 한번 확인해주세요.');
            }

			wp_send_json_success();
		}


		public function get_role_field() {
			require_once( ABSPATH . 'wp-admin/includes/user.php' );

			$roles_setting = array();

			foreach ( get_editable_roles() as $slug => $role ) {
				$roles_setting[ "msm_security_" . $slug ] = $role['name'];
			}

			$roles_setting['msm_security_guest'] = __( 'Guest', 'mshop-members-s2' );

			return $roles_setting;
		}

		public function get_setting_fields() {
			return array(
				'type'     => 'Tab',
				'id'       => 'mshop-members-setting-tab',
				'elements' => apply_filters( 'msm_setting_fields', array(
					$this->get_setting_main_tab(),
					$this->get_setting_terms_and_condition(),
					$this->get_setting_members_rule(),
					$this->get_setting_user_agreement(),
					$this->get_setting_cookie(),
					$this->get_settings_access_control(),
					$this->get_setting_tools(),
				) )
			);
		}

		public function get_setting_main_tab() {
			$subscription_settings = array();

			if ( class_exists( 'WC_Subscription' ) ) {
				$subscription_settings = array(
					array(
						'id'        => 'msm_prevent_unsubscribe_when_have_active_subscription',
						'title'     => '회원탈퇴 불가',
						'className' => '',
						'type'      => 'Toggle',
						'default'   => 'no',
						'desc'      => '활성화된 정기결제권을 보유하고 있는 경우, 회원탈퇴를 할 수 없습니다.'
					),
					array(
						'id'        => 'msm_prevent_unsubscribe_message',
						'showIf'    => array( 'msm_prevent_unsubscribe_when_have_active_subscription' => 'yes' ),
						'title'     => '회원탈퇴 불가 안내메시지',
						'className' => '',
						'type'      => 'TextArea',
						'default'   => __( '<h4>고객님은 진행중인 정기결제권을 보유하고 있습니다.<br>회원탈퇴를 하시려면, 정기결제권을 모두 취소해주셔야합니다.</h4>', 'mshop-members-s2' )
					),
				);
			}

			if ( class_exists( 'MSSMS_Manager' ) ) {
				$notification_settings = array(
					array(
						"id"        => "mshop_members_sleep_notification_sms_template",
						"title"     => __( "휴면예고 문자 템플릿", 'mshop-members-s2' ),
						'showIf'    => array( array( 'mshop_members_use_sleep' => 'yes' ), array( 'mshop_members_sleep_notification_method' => 'sms' ) ),
						"className" => "center aligned fluid",
						"type"      => "TextArea",
						"default"   => __( "[{쇼핑몰명}] 안녕하세요? {고객명} 회원님.

저희 {쇼핑몰명} 쇼핑몰에 장기간 미접속으로 인해, 휴면회원으로 전환이 될 예정임을 안내해 드립니다.

휴면회원으로 전환이 되었어도, 다시 접속을 하시면, 휴면회원 처리에서 제외되오니 이점 참고하여 주세요.

* 휴면회원으로 전환된 이후 {휴면회원삭제대기일} 일 이후에는 회원 정보가 자동으로 삭제처리 됩니다.

그동안 저희 {쇼핑몰명}을 이용 해 주셔서 감사합니다." ),
						"rows"      => 8,
					),
					array(
						"id"          => "mshop_members_sleep_notification_alimtalk_template",
						'showIf'      => array( array( 'mshop_members_use_sleep' => 'yes' ), array( 'mshop_members_sleep_notification_method' => 'alimtalk' ) ),
						"title"       => __( "휴면예고 알림톡 템플릿", "mshop-members-s2" ),
						"placeholder" => __( "휴면예고 템플릿을 선택해주세요.", "mshop-members-s2" ),
						"className"   => "",
						"type"        => "Select",
						'options'     => MSSMS_Manager::get_alimtalk_templates()
					),
				);
			} else {
				$notification_settings = array(
					array(
						"id"       => "mshop_members_sleep_notification_sms_template",
						"title"    => __( "휴면예고 문자 템플릿", 'mshop-members-s2' ),
						'showIf'   => array( array( 'mshop_members_use_sleep' => 'yes' ), array( 'mshop_members_sleep_notification_method' => 'sms' ) ),
						'type'     => 'Label',
						'readonly' => 'yes',
						'default'  => '',
						'desc2'    => __( '<div class="desc2">휴면예고 문자알림 기능을 이용하시려면 "<a target="_blank" href="https://www.codemshop.com/shop/sms_out/">엠샵 문자 알림톡 자동발송 플러그인</a>"이 설치되어 있어야 합니다.</div>', 'mshop-members-s2' ),
					),
					array(
						"id"       => "mshop_members_sleep_notification_alimtalk_template",
						'showIf'   => array( array( 'mshop_members_use_sleep' => 'yes' ), array( 'mshop_members_sleep_notification_method' => 'alimtalk' ) ),
						"title"    => __( "휴면예고 알림톡 템플릿", "mshop-members-s2" ),
						'type'     => 'Label',
						'readonly' => 'yes',
						'default'  => '',
						'desc2'    => __( '<div class="desc2">휴면예고 알림톡 기능을 이용하시려면 "<a target="_blank" href="https://www.codemshop.com/shop/sms_out/">엠샵 문자 알림톡 자동발송 플러그인</a>"이 설치되어 있어야 합니다.</div>', 'mshop-members-s2' ),
					),
				);
			}

			return array(
				'type'     => 'Page',
				'title'    => '기본 설정',
				'class'    => 'active',
				'elements' => array(
					array(
						'type'     => 'Section',
						'title'    => '엠샵 멤버스',
						'elements' => array(
							array(
								'id'        => 'mshop_members_enabled',
								'title'     => '활성화',
								'className' => '',
								'type'      => 'Toggle',
								'default'   => 'no',
								'desc'      => '엠샵 멤버스 회원가입 기능을 사용합니다.'
							)
						)
					),
					array(
						'type'     => 'Section',
						'title'    => '기본설정',
						'showIf'   => array( 'mshop_members_enabled' => 'yes' ),
						'elements' => array(
							array(
								'id'      => 'msm_user_can_edit_fields',
								'title'   => __( '회원 정보 수정', 'mshop-members-s2' ),
								'desc'    => __( '<div class="desc2">회원은 회원가입 시 입력한 정보를 내계정 페이지에서 수정할 수 있습니다.</div>', 'mshop-members-s2' ),
								'default' => 'no',
								'type'    => 'Toggle'
							),
							array(
								'id'      => 'mshop_members_using_footer_script',
								'title'   => __( '스크립트 Footer 사용', 'mshop-members-s2' ),
								'desc'    => __( '<div class="desc2">스크립트를 Footer 영역에서 읽도록 설정 할 수 있습니다. 타 플러그인과의 스크립트 충돌로 기능이 정상 동작되지 않는 경우에만 활성화를 해 주세요.</div>', 'mshop-members-s2' ),
								'default' => 'no',
								'type'    => 'Toggle'
							),
							array(
								'id'      => 'msm_form_notice_method',
								'title'   => __( '오류메시지 표시 방식', 'mshop-members-s2' ),
								'desc'    => __( '<div class="desc2">로그인 또는 회원가입시 오류메시지 표시방식을 지정합니다.</div>', 'mshop-members-s2' ),
								"type"    => "Select",
								'default' => 'message',
								'options' => array(
									'message' => __( '멤버스폼 상단 또는 하단 영역에 표시', 'mshop-members-s2' ),
									'popup'   => __( '팝업창으로 표시', 'mshop-members-s2' ),
									'all'     => __( '모두 표시', 'mshop-members-s2' ),
								)
							),
							array(
								'id'      => 'msm_display_customer_info',
								'title'   => __( '고객정보 표시', 'mshop-members-s2' ),
								'desc'    => __( '<div class="desc2">로그인 후 로그인 링크 위치에 고객정보를 표시합니다.</div>', 'mshop-members-s2' ),
								'default' => 'no',
								'type'    => 'Toggle'
							),
							array(
								'id'        => 'msm_customer_info_string',
								'showIf'    => array( 'msm_display_customer_info' => 'yes' ),
								'title'     => __( '고객정보 문구', 'mshop-members-s2' ),
								'className' => 'fluid',
								'default'   => __( '{고객명}님 반갑습니다' ),
								'type'      => 'Text'
							),
						)
					),
					array(
						'type'     => 'Section',
						'title'    => '개인정보 설정',
						'showIf'   => array( 'mshop_members_enabled' => 'yes' ),
						'elements' => array(
							array(
								'id'      => 'mshop_members_personal_info_noti',
								'title'   => __( '개인정보 이용 메일 활성화', 'mshop-members-s2' ),
								'desc'    => __( '<div class="desc2">개인정보 이용내역은 연1회 발송되는 법적의무 사항입니다.<br>매달 1일 회원가입 일자를 기준으로 대상 고객들에게 발송됩니다.<br>발송되는 메일에 대한 설정은 <a style="color:red;" target="_blank" href="/wp-admin/admin.php?page=wc-settings&tab=email&section=msm_email_personal_information">개인정보 이용내역 안내 메일 설정</a>에서 진행 해주세요.</div>', 'mshop-members-s2' ),
								'default' => 'no',
								'type'    => 'Toggle'
							),
						)
					),
					array(
						'type'     => 'Section',
						'title'    => '로그인 제한',
						'showIf'   => array( 'mshop_members_enabled' => 'yes' ),
						'elements' => array(
							array(
								'id'       => 'mshop_members_restrict_login',
								'title'    => __( '로그인 제한', 'mshop-members-s2' ),
								'desc'     => __( '<div class="desc2">지정된 등급의 사용자는 로그인할 수 없습니다.</div>', 'mshop-members-s2' ),
								"type"     => "Select",
								'default'  => '',
								'multiple' => true,
								'options'  => apply_filters( 'msm_get_roles', array() ),
							),
							array(
								'id'        => 'mshop_members_restrict_login_message',
								'title'     => __( '로그인 제한 메시지', 'mshop-members-s2' ),
								'className' => 'fluid',
								"type"      => "Text",
								'default'   => __( '등록되지 않은 이메일이거나 비밀번호가 잘못되었습니다.', 'mshop-members-s2' )
							),
							array(
								'id'          => 'mshop_members_restrict_login_redirect_url',
								'title'       => __( '회원가입시 이동할 URL', 'mshop-members-s2' ),
								'className'   => 'fluid',
								"type"        => "Text",
								'placeholder' => __( '회원가입 시 기본사용자 등급이 로그인 제한 사용자인 경우, 지정된 URL로 이동됩니다.', 'mshop-members-s2' )
							),
						)
					),
					array(
						'type'     => 'Section',
						'title'    => '회원탈퇴',
						'showIf'   => array( 'mshop_members_enabled' => 'yes' ),
						'elements' => array_filter( array_merge( array(
								array(
									'id'        => 'mshop_members_use_unsubscribe',
									'title'     => '활성화',
									'className' => '',
									'type'      => 'Toggle',
									'default'   => 'no',
									'desc'      => '회원탈퇴 기능을 사용합니다.'
								),
								array(
									'id'        => 'mshop_members_unsubscribe_button_text',
									'showIf'    => array( 'mshop_members_use_unsubscribe' => 'yes' ),
									'title'     => '버튼 문구',
									'className' => '',
									'type'      => 'Text',
									'default'   => '회원탈퇴'
								),
								array(
									"id"          => "mshop_members_unsubscribe_after_process",
									'showIf'      => array( 'mshop_members_use_unsubscribe' => 'yes' ),
									"title"       => "탈퇴 시 처리",
									"placeholder" => "규칙 종류를 지정하세요.",
									"type"        => "Select",
									'default'     => 'none',
									'options'     => array(
										'none'   => '탈퇴 시 정보 유지',
										'delete' => '탈퇴 시 정보 삭제'
									),
								),
								array(
									'id'          => 'mshop_members_unsubscribe_auto_delete_wait_day',
									'showIf'      => array(
										array( 'mshop_members_use_unsubscribe' => 'yes' ),
										array( 'mshop_members_unsubscribe_after_process' => 'none' )
									),
									'title'       => '탈퇴 회원 자동 삭제 대기일',
									'className'   => '',
									'type'        => 'LabeledInput',
									"label"       => '일 이후',
									'default'     => '0',
									'placeholder' => '0',
									"tooltip"     => array(
										"title" => array(
											"content" => "탈퇴 시 회원 정보가 유지되는 일 수를 입력시 탈퇴 회원은 해당 날짜가 초과되는 시점에 정보가 자동으로 삭제 됩니다. 사용하지 않는 경우 입력칸을 비워주세요."
										)
									)
								)
							), $subscription_settings )
						)
					),
					array(
						'type'     => 'Section',
						'title'    => '휴면회원',
						'showIf'   => array( 'mshop_members_enabled' => 'yes' ),
						'elements' => array_merge( array(
							array(
								'id'        => 'mshop_members_use_sleep',
								'title'     => '활성화',
								'className' => '',
								'type'      => 'Toggle',
								'default'   => 'no',
								'desc'      => '휴면회원 기능을 사용합니다.'
							),
							array(
								'id'        => 'mshop_members_sleep_warning_day',
								'showIf'    => array( 'mshop_members_use_sleep' => 'yes' ),
								'title'     => '휴면 예고일',
								'className' => '',
								'type'      => 'LabeledInput',
								"label"     => '일 이전',
								'default'   => '30',
								"tooltip"   => array(
									"title" => array(
										"content" => "휴면 처리일로부터 지정된 일수 이전에 휴면 예고 메일이 발송됩니다. <br>입력 값이 없거나 숫자 0을 입력하는 경우 휴면예고 이메일은 발송되지 않습니다."
									)
								)
							),
							array(
								'id'        => 'mshop_members_sleep_wait_day',
								'showIf'    => array( 'mshop_members_use_sleep' => 'yes' ),
								'title'     => '휴면 처리일',
								'className' => '',
								'type'      => 'LabeledInput',
								"label"     => '일 이후',
								'default'   => '365',
								"tooltip"   => array(
									"title" => array(
										"content" => "휴면 회원으로 전환되는 일 수 입력시, 휴면 회원 대상자는 마지막 로그인 한 후, 해당 일 수가 초과된 경우 자동으로 휴면 회원으로 전환됩니다."
									)
								)
							),
							array(
								'id'        => 'mshop_members_sleep_auto_delete_wait_day',
								'showIf'    => array( 'mshop_members_use_sleep' => 'yes' ),
								'title'     => '휴면처리 후 삭제 대기일',
								'className' => '',
								'type'      => 'LabeledInput',
								"label"     => '일 이후',
								'default'   => '365',
								"tooltip"   => array(
									"title" => array(
										"content" => "휴면회원으로 전환된 이후, 휴면회원이 삭제 대기일을 초과한 경우 자동으로 회원 정보를 삭제합니다. <br>입력 값이 없거나, 숫자 0을 입력하는 경우, 휴면 처리 전환 후 바로 회원 정보를 삭제합니다."
									)
								)
							),
							array(
								"id"        => "mshop_members_sleep_notification_method",
								'showIf'    => array( 'mshop_members_use_sleep' => 'yes' ),
								"title"     => "휴면 예고 발송 수단",
								"className" => "",
								"type"      => "Select",
								"multiple"  => "true",
								'default'   => 'email',
								'options'   => array(
									'email'    => '이메일',
									'sms'      => '문자 (LMS)',
									'alimtalk' => '알림톡'
								),
							),
							array(
								'id'        => 'mshop_members_sleep_warning_email_title',
								'showIf'    => array( array( 'mshop_members_use_sleep' => 'yes' ), array( 'mshop_members_sleep_notification_method' => 'email' ) ),
								'title'     => '휴면예고 이메일 제목',
								'className' => 'fluid',
								'type'      => 'Text',
								'default'   => '휴면 전환 예고입니다.'
							),
							array(
								'id'        => 'mshop_members_sleep_warning_email',
								'showIf'    => array( array( 'mshop_members_use_sleep' => 'yes' ), array( 'mshop_members_sleep_notification_method' => 'email' ) ),
								'title'     => '휴면예고 이메일 내용',
								'className' => '',
								'type'      => 'TextArea',
								'default'   => '안녕하세요? {고객명} 회원님.

저희 {쇼핑몰명} 쇼핑몰에 장기간 미접속으로 인해, 휴면회원으로 전환이 될 예정임을 안내해 드립니다.

휴면회원으로 전환이 되었어도, 다시 접속을 하시면, 휴면회원 처리에서 제외되오니 이점 참고하여 주세요.

* 휴면회원으로 전환된 이후 {휴면회원삭제대기일} 일 이후에는 회원 정보가 자동으로 삭제처리 됩니다.

그동안 저희 {쇼핑몰명}을 이용 해 주셔서 감사합니다.',
								"rows"      => 8,
							)
						), $notification_settings )
					),
				)
			);
		}

		public function get_setting_terms_and_condition() {
			return array(
				'type'     => 'Page',
				'title'    => '이용약관',
				'showIf'   => array( 'mshop_members_enabled' => 'yes' ),
				'elements' => array(
					array(
						'type'     => 'Section',
						'title'    => '이용약관 동의',
						'elements' => array(
							array(
								"id"        => "mshop_members_use_terms_and_conditions",
								"title"     => "사용",
								"className" => "",
								"type"      => "Toggle",
								"default"   => "no",
								"desc"      => "이용 약관 기능을 사용합니다."
							),
						)
					),
					array(
						'type'     => 'Section',
						'title'    => '회원 이용약관 동의',
						'showIf'   => array( 'mshop_members_use_terms_and_conditions' => 'yes' ),
						'elements' => array(
							array(
								"id"        => "mshop_members_require_tac_for_customer",
								"title"     => "사용",
								"className" => "",
								"type"      => "Toggle",
								"default"   => "no",
								"desc"      => "신규 회원가입 시 이용 약관을 사용합니다."
							),
							array(
								"id"          => "mshop_members_tac_form_for_customer",
								'showIf'      => array( 'mshop_members_require_tac_for_customer' => 'yes' ),
								"title"       => "회원 이용약관",
								"placeholder" => "회원용 이용약관을 선택하세요.",
								"className"   => "",
								"type"        => "Select",
								'options'     => msm_get_members_forms( 'terms_and_conditions' )
							)
						)
					),
					array(
						'type'     => 'Section',
						'title'    => '비회원 이용약관 동의',
						'showIf'   => array( 'mshop_members_use_terms_and_conditions' => 'yes' ),
						'elements' => array(
							array(
								"id"        => "mshop_members_require_tac_for_guest",
								"title"     => "비회원",
								"className" => "",
								"type"      => "Toggle",
								"default"   => "no",
								"desc"      => "비회원의 상품 구매 시 이용 약관을 사용합니다."
							),
							array(
								"id"          => "mshop_members_tac_form_for_guest",
								'showIf'      => array( 'mshop_members_require_tac_for_guest' => 'yes' ),
								"title"       => "이용약관",
								"placeholder" => "비회원용 이용약관을 선택하세요.",
								"className"   => "",
								"type"        => "Select",
								'options'     => msm_get_members_forms( array( 'login', 'register', 'terms_and_conditions' ) )
							)
						)
					)
				)
			);
		}

		public function get_setting_tools() {
			return array(
				'type'     => 'Page',
				'title'    => '도구',
				'showIf'   => array( 'mshop_members_enabled' => 'yes' ),
				'elements' => array(
					array(
						'type'     => 'Section',
						'title'    => '멤버스 도구',
						'elements' => array(
							array(
								'id'             => 'msm_install_page',
								'title'          => '기본 페이지 생성',
								'label'          => '실행',
								'iconClass'      => 'icon settings',
								'className'      => '',
								'type'           => 'Button',
								'default'        => '',
								'actionType'     => 'ajax',
								'confirmMessage' => __( '[주의] 엠샵 멤버스 기본 페이지를 생성하시겠습니까? 
기본 페이지 생성 시, 기존에 수정된 엠샵 멤버스 페이지는 모두 삭제 됩니다.', 'mshop-members-s2' ),
								'ajaxurl'        => admin_url( 'admin-ajax.php' ),
								'action'         => msm_ajax_command( 'install_pages' ),
								"desc"           => "엠샵 멤버스 기본 페이지를 생성합니다."
							),
							array(
								'id'             => 'msm_install_form',
								'title'          => '기본 템플릿 생성',
								'label'          => '실행',
								'iconClass'      => 'icon settings',
								'className'      => '',
								'type'           => 'Button',
								'default'        => '',
								'actionType'     => 'ajax',
								'confirmMessage' => __( '[주의] 엠샵 멤버스 기본 템플릿을 생성하시겠습니까? 
기본 템플릿 생성 시, 기존에 수정된 엠샵 멤버스 템플릿은 모두 삭제됩니다.', 'mshop-members-s2' ),
								'ajaxurl'        => admin_url( 'admin-ajax.php' ),
								'action'         => msm_ajax_command( 'install_forms' ),
								"desc"           => "엠샵 멤버스 기본 템플릿을 생성합니다."
							),
							array(
								'id'             => 'msm_install_agreement',
								'title'          => '이용약관 생성',
								'label'          => '실행',
								'iconClass'      => 'icon settings',
								'className'      => '',
								'type'           => 'Button',
								'default'        => '',
								'actionType'     => 'ajax',
								'confirmMessage' => __( '[주의] 엠샵 멤버스 이용약관을 생성하시겠습니까? 
이용 약관 생성 시, 기존에 수정된 이용약관은 모두 삭제됩니다.', 'mshop-members-s2' ),
								'ajaxurl'        => admin_url( 'admin-ajax.php' ),
								'action'         => msm_ajax_command( 'install_agreements' ),
								"desc"           => "엠샵 멤버스 이용약관을 생성합니다."
							),
							array(
								'id'         => 'msm_import_forms2',
								'title'      => '폼 불러오기 (Import)',
								'label'      => '실행',
								'iconClass'  => 'icon settings',
								'className'  => '',
								'type'       => 'Upload',
								'default'    => '',
								'actionType' => 'ajax',
								'ajaxurl'    => admin_url( 'admin-ajax.php' ),
								'action'     => msm_ajax_command( 'import_forms2' )
							)
						)
					)
				)
			);
		}

		public function get_setting_members_rule() {
			return array(
				'type'     => 'Page',
				'title'    => '멤버스 정책',
				'showIf'   => array( 'mshop_members_enabled' => 'yes' ),
				'class'    => '',
				'elements' => array(
					array(
						'type'     => 'Section',
						'title'    => '멤버스 정책 설정',
						'showIf'   => array( 'mshop_members_enabled' => 'yes' ),
						'elements' => array(
							array(
								"id"        => "mshop_members_use_role_application_rule",
								"title"     => "활성화",
								"className" => "",
								"type"      => "Toggle",
								"default"   => "no",
								"desc"      => "멤버스 정책 관리 기능을 사용합니다."
							)
						)
					),
					array(
						"id"           => "mshop_members_role_application_rules",
						"type"         => "SortableList",
						"title"        => "멤버스 정책 목록",
						"listItemType" => "MShopMembersRule",
						"repeater"     => true,
						'showIf'       => array( 'mshop_members_use_role_application_rule' => 'yes' ),
						"template"     => array(
							'rule_type'      => 'role',
							'rule_enabled'   => 'no',
							'mms_conditions' => array(),
						),
						"default"      => array(),
						"elements"     => array(
							'left' => array(
								'type'              => 'Section',
								"hideSectionHeader" => true,
								'elements'          => array(
									array(
										"id"        => "rule_type",
										"title"     => "규칙종류",
										"showIf"    => array( 'hidden' => 'hidden' ),
										"className" => "fluid",
										"type"      => "Select",
										'default'   => 'role',
										'options'   => array(
											'role'     => '사용자 역할',
											'usermeta' => '사용자 정보'
										),
									),
									array(
										"id"        => "rule_title",
										"title"     => "규칙이름",
										"className" => "fluid",
										"type"      => "Text",
									),
									array(
										'id'        => 'rule_enabled',
										'title'     => '활성화',
										'className' => '',
										'type'      => 'Toggle',
										'default'   => 'no',
//										'desc'      => '정책 사용'
									),
								)
							),
							'role' => array(
								'type'              => 'Section',
								"hideSectionHeader" => true,
								'elements'          => array(
									array(
										"id"          => "role",
										"title"       => "표시대상",
										"placeholder" => "규칙을 적용할 회원등급을 선택하세요.",
										"className"   => "fluid",
										"type"        => "Select",
										'default'     => '',
										'multiple'    => true,
										'options'     => apply_filters( 'msm_get_roles', array() ),
									),
									array(
										"id"        => "mms_conditions",
										"title"     => "추가 조건",
										"className" => "",
										"editable"  => 'true',
										"type"      => "SortableTable",
										"template"  => array(
											'condition' => '',
											'value'     => '',
											'operator'  => '',
										),
										"elements"  => array(
											array(
												"id"        => "condition",
												"title"     => __( "사용자 조건", 'mshop-members-s2' ),
												"className" => " eight wide column fluid",
												"type"      => "Select",
												'default'   => 'role',
												'options'   => apply_filters( 'msm_rule_conditions', array(
													'' => '조건을 선택하세요'
												) )
											),
											array(
												"id"        => "value",
												"className" => " six wide column fluid",
												"title"     => __( "값", 'mshop-members-s2' ),
												"type"      => "Select",
												'default'   => 'yes',
												'options'   => apply_filters( 'msm_rule_condition_values', array(
													''    => '선택하세요',
													'yes' => 'YES',
													'no'  => 'NO'
												) ),
											),
											array(
												"id"        => "operator",
												"className" => " two wide column fluid",
												"type"      => "Select",
												'default'   => 'role',
												'options'   => array(
													''    => '',
													'and' => 'AND',
													'or'  => 'OR'
												),
											),
										)
									),
									array(
										"id"          => "description",
										"title"       => "안내 메시지",
										"placeholder" => "정책에 대한 안내 메시지를 입력하세요.
HTML 태그를 이용하여 작성도 가능합니다.",
										"className"   => "fluid",
										"type"        => "TextArea"
									),
									array(
										"id"        => "button_text",
										"title"     => "버튼 문구",
										"className" => "fluid",
										"type"      => "Text",
									),
									array(
										"id"          => "page",
										"title"       => "이동 페이지",
										"placeholder" => "이동할 페이지를 선택하세요.",
										"className"   => "fluid search",
										"type"        => "SearchSelect",
										'default'     => '',
										'multiple'    => false,
										'search'      => true,
										'action'      => 'action=' . msm_ajax_command( 'search_page&keyword=' ),

									)
								)
							)
						)
					)
				)
			);
		}

		function get_setting_user_agreement() {
			if ( class_exists( 'MSSMS_Manager' ) ) {
				$agreement_noti_methods  = array(
					'email'    => '이메일',
					'sms'      => '문자 (LMS)',
					'alimtalk' => '알림톡',
				);
				$agreement_noti_alimtalk = MSSMS_Manager::get_alimtalk_templates();
			} else {
				$agreement_noti_methods  = array( 'email' => '이메일' );
				$agreement_noti_alimtalk = '';
			}

            ob_start();
            include( 'html/sms-agreement-guide.php' );
            $guide = ob_get_clean();

			return array(
				'type'     => 'Page',
				'title'    => __( '수신 동의 설정', 'mshop-members-s2' ),
				'showIf'   => array( 'mshop_members_enabled' => 'yes' ),
				'elements' => array(
					array(
						'type'     => 'Section',
						'title'    => '수신 동의 안내 설정',
						'elements' => array(
							array(
								'id'      => 'msm_user_agreement_use_email',
								'title'   => __( '이메일 수신 동의 사용', 'mshop-members-s2' ),
								'desc'    => __( "<div class='desc2'>사이트에서 이메일 수신 동의('email_agreement')를 사용하는 경우 활성화 합니다.</div>", 'mshop-members-s2' ),
								'default' => 'no',
								'type'    => 'Toggle'
							),
							array(
								'id'      => 'msm_user_agreement_use_mssms',
								'title'   => __( '문자 수신 동의 사용', 'mshop-members-s2' ),
								'desc'    => __( "<div class='desc2'>사이트에서 문자 수신 동의('mssms_agreement')를 사용하는 경우 활성화 합니다.</div>", 'mshop-members-s2' ),
								'default' => 'no',
								'type'    => 'Toggle'
							),
						)
					),
					array(
						'type'     => 'Section',
						'title'    => '수신 동의 변경 안내 설정',
						'elements' => array(
							array(
								'id'      => 'msm_user_agreement_change_noti_enable',
								'title'   => __( '활성화', 'mshop-members-s2' ),
								'desc'    => __( '<div class="desc2">수신 동의 변경 안내 기능을 사용합니다.</div>', 'mshop-members-s2' ),
								'default' => 'no',
								'type'    => 'Toggle'
							),
							array(
								'showIf'    => array( 'msm_user_agreement_change_noti_enable' => 'yes' ),
								'id'        => 'msm_user_agreement_change_noti_method',
								'title'     => '발송 수단',
								'className' => '',
								'type'      => 'Select',
								'multiple'  => 'true',
//                                'desc'      => __( '문자 및 알림톡 발송을 위해서는 "<a target="_blank" href="https://www.codemshop.com/shop/sms_out/">엠샵 문자 알림톡 자동발송 플러그인</a>"이 설치되어 있어야 합니다.', 'mshop-members-s2' ),
								'default'   => 'email',
								'options'   => $agreement_noti_methods
							),
							array(
								'showIf'   => array( array( 'msm_user_agreement_change_noti_enable' => 'yes' ), array( 'msm_user_agreement_change_noti_method' => 'email' ) ),
								'id'       => 'msm_user_agreement_change_noti_email',
								'title'    => __( '수신 동의 변경 안내 메일', 'mshop-members-s2' ),
								'desc2'    => __( '<div class="desc2">수신 동의 상태가 변경되었을 때 발송이 진행됩니다.<br>발송되는 메일에 대한 설정은 <a style="color:red;" target="_blank" href="/wp-admin/admin.php?page=wc-settings&tab=email&section=msm_email_user_agreement_change">수신 동의 변경 안내 메일 설정</a>에서 진행 해주세요.</div>', 'mshop-members-s2' ),
								'type'     => 'Label',
								'readonly' => 'yes',
							),
							array(
								'showIf'      => array( array( 'msm_user_agreement_change_noti_enable' => 'yes' ), array( 'msm_user_agreement_change_noti_method' => 'alimtalk' ) ),
								'id'          => 'msm_user_agreement_change_noti_alimtalk',
								'title'       => __( '수신 동의 변경 안내 알림톡 템플릿', 'mshop-members-s2' ),
								'placeholder' => '휴대폰인증을 위한 템플릿을 선택해주세요.',
								'className'   => '',
								'type'        => 'Select',
								'options'     => $agreement_noti_alimtalk
							),
							array(
								'showIf'        => array( array( 'msm_user_agreement_change_noti_enable' => 'yes' ), array( 'msm_user_agreement_change_noti_method' => 'sms' ) ),
								'id'            => 'msm_user_agreement_change_noti_sms',
								'title'         => __( '수신 동의 변경 안내 문자', 'mshop-members-s2' ),
								'className'     => 'center aligned thirteen wide column fluid',
								'cellClassName' => 'fluid',
								'type'          => 'TextArea',
								'default'       => __( '안녕하세요. {쇼핑몰명} 입니다.

{쇼핑몰명}에서 발송하는 마케팅 정보 수신 동의 상태가 다음과 같이 변경되었습니다.

변경일 : {발송일}
문자 : {문자수신동의상태}
이메일 : {이메일수신동의상태}

결제 및 배송관련, 이벤트 당첨 안내는 마케팅 수신 동의 상태와 관계없이 발송됩니다.

본 문자는 "정보통신망 이용촉진 및 정보보호 등에 관한 법률" 제50조 제7항에 따라 발송되는 안내문자 입니다.

감사합니다.', 'mshop-members-s2' ),
								'placeholder'   => '발송 문구를 입력하신 후 사용해주세요.',
								'rows'          => 13

							),
						),
					),
					array(
						'type'     => 'Section',
						'title'    => '정기적 수신 동의 확인 안내 설정',
						'elements' => array(
							array(
								'id'      => 'msm_user_agreement_info_noti_enable',
								'title'   => __( '활성화', 'mshop-members-s2' ),
								'desc'    => __( '<div class="desc2">정기적 수신 동의 확인 안내 기능을 사용합니다.</div>', 'mshop-members-s2' ),
								'default' => 'no',
								'type'    => 'Toggle'
							),
							array(
								'showIf'    => array( 'msm_user_agreement_info_noti_enable' => 'yes' ),
								'id'        => 'msm_user_agreement_info_noti_method',
								'title'     => '발송 수단',
								'className' => '',
								'type'      => 'Select',
								'multiple'  => 'true',
//                                'desc'      => __( '문자 및 알림톡 발송을 위해서는 "<a target="_blank" href="https://www.codemshop.com/shop/sms_out/">엠샵 문자 알림톡 자동발송 플러그인</a>"이 설치되어 있어야 합니다.', 'mshop-members-s2' ),
								'default'   => 'email',
								'options'   => $agreement_noti_methods
							),
							array(
								'id'          => 'msm_agreement_info_send_date',
								'showIf'      => array( 'msm_user_agreement_info_noti_enable' => 'yes' ),
								'title'       => '발송 날짜',
								'className'   => '',
								"placeholder" => '예시) 12-01',
								'type'        => 'Text',
								'default'     => '12-01',
								'desc'        => __( '<div class="desc2">수신 동의 현황 안내 메일을 발송할 날짜를 월-일 형태로 입력해주세요.</div>', 'mshop-members-s2' ),
							),
							array(
								'showIf'   => array( array( 'msm_user_agreement_info_noti_enable' => 'yes' ), array( 'msm_user_agreement_info_noti_method' => 'email' ) ),
								'id'       => 'msm_user_agreement_info_noti_email',
								'title'    => __( '정기적 수신 동의 확인 안내 메일', 'mshop-members-s2' ),
								'desc2'    => __( '<div class="desc2">매년 1회 지정된 날짜에 발송이 진행됩니다.<br>발송되는 메일에 대한 설정은 <a style="color:red;" target="_blank" href="/wp-admin/admin.php?page=wc-settings&tab=email&section=msm_email_user_agreement_information">정기적 수신 동의 확인 안내 메일 설정</a>에서 진행 해주세요.</div>', 'mshop-members-s2' ),
								'type'     => 'Label',
								'readonly' => 'yes',
							),
							array(
								'showIf'      => array( array( 'msm_user_agreement_info_noti_enable' => 'yes' ), array( 'msm_user_agreement_info_noti_method' => 'alimtalk' ) ),
								'id'          => 'msm_user_agreement_info_noti_alimtalk',
								'title'       => __( '정기적 수신 동의 확인 안내 알림톡 템플릿', 'mshop-members-s2' ),
								'placeholder' => '휴대폰인증을 위한 템플릿을 선택해주세요.',
								'className'   => '',
								'type'        => 'Select',
								'options'     => $agreement_noti_alimtalk
							),
							array(
								'showIf'        => array( array( 'msm_user_agreement_info_noti_enable' => 'yes' ), array( 'msm_user_agreement_info_noti_method' => 'sms' ) ),
								'id'            => 'msm_user_agreement_info_noti_sms',
								'title'         => __( '정기적 수신 동의 확인 안내 문자', 'mshop-members-s2' ),
								'className'     => 'center aligned thirteen wide column fluid',
								'cellClassName' => 'fluid',
								'type'          => 'TextArea',
								'default'       => __( '안녕하세요. {쇼핑몰명} 입니다.

본 문자는 "정보통신망 이용촉진 및 정보보호 등에 관한 법률" 제50조 제8항에 따라 {쇼핑몰명} 마케팅 정보 수신에 동의하신 고객님께 정기적으로 동의 현황 안내를 위해 발송되는 문자입니다.

현재 고객님의 수신 동의 상태는 다음과 같습니다.

문자 : {문자수신동의상태} - {문자수신동의날짜}
이메일 : {이메일수신동의상태} - {이메일수신동의날짜}

이전 가입자의 경우, 수신 동의 일자가 저장되어 있지 않아서 수신 동의 날짜와 실제 동의 설정하신 날짜가 다를 수 있습니다.

수신동의 상태를 유지하고자 하시는 경우 별도의 조치가 필요하지 않으며, 수신거부를 원하시는 경우 사이트 내계정 페이지 또는 고객센터를 통해 언제든지 변경하실 수 있습니다.

감사합니다.', 'mshop-members-s2' ),
								'placeholder'   => '발송 문구를 입력하신 후 사용해주세요.',
								'rows'          => 14
							),
						),
					),
                    array (
                        'type'           => 'Section',
                        'hideSaveButton' => true,
                        "showIf"         => array ( array ( 'msm_user_agreement_change_noti_enable' => 'yes', 'msm_user_agreement_info_noti_enable' => 'yes' ), array ( 'msm_user_agreement_change_noti_method' => 'sms,alimtalk', 'msm_user_agreement_info_noti_method' => 'sms,alimtalk' ) ),
                        'title'          => __( '수신 동의 대체문구', 'mshop-members-s2' ),
                        'elements'       => array (
                            array (
                                "id"        => "msm_user_agreement_replace_text",
                                "className" => "fluid",
                                "type"      => "Label",
                                "readonly"  => "yes",
                                "default"   => $guide
                            )
                        )
                    ),
				)
			);
		}

		function get_setting_cookie() {
			return array(
				'type'     => 'Page',
				'title'    => __( '쿠키 사용 동의 설정', 'mshop-members-s2' ),
				'elements' => array(
					array(
						'type'     => 'Section',
						'title'    => __( '기본 설정', 'mshop-members-s2' ),
						'elements' => array(
							array(
								'id'        => 'msmp_use_cookie_agreement',
								'title'     => __( '활성화', 'mshop-members-s2' ),
								'className' => '',
								'type'      => 'Toggle',
								'default'   => 'no',
								'desc'      => __( '쿠키 사용동의 기능 기능을 사용합니다.', 'mshop-members-s2' )
							),
							array(
								'id'        => 'msmp_cookie_agreement_message',
								'showIf'    => array( 'msmp_use_cookie_agreement' => 'yes' ),
								'title'     => '쿠키 사용동의 안내문구',
								'className' => 'fluid',
								'type'      => 'TextArea',
								'rows'      => 10,
								'default'   => __( '{사이트명}에 오신 것을 환영합니다! 웹사이트를 원활하게 표시하기 위해 쿠키를 사용합니다. {사이트명}을 계속 이용하려면 쿠키 사용에 동의해야 합니다.', 'mshop-members-s2' )
							),
						)
					)
				)
			);
		}

		function get_settings_access_control() {
			return array(
				'type'     => 'Page',
				'title'    => __( '보안 설정', 'mshop-members-s2' ),
				'elements' => array(
					array(
						'type'     => 'Section',
						'title'    => __( '기본 설정', 'mshop-members-s2' ),
						'elements' => array(
							array(
								'id'        => "msm_use_access_control",
								"className" => "one wide column",
								"title"     => __( "접근제어 기능 활성화", 'mshop-members-s2' ),
								"type"      => "Toggle",
								'default'   => 'no',
							),
							array(
								'id'        => "msm_disable_wc_login",
								"className" => "one wide column",
								'showIf'    => array( 'msm_use_access_control' => 'yes' ),
								"title"     => __( "우커머스 로그인 비활성화", 'mshop-members-s2' ),
								"type"      => "Toggle",
								'default'   => 'yes',
								'desc'      => __( '스팸회원 가입 차단을 위해 우커머스의 기본 폼 핸들러를 비활성화합니다.' )
							),
							array(
								'id'          => 'msm_security_author_display',
								'title'       => __( '댓글 작성자 숨김', 'mshop-members-s2' ),
								'className'   => '',
								'type'        => 'Select',
								'default'     => "no",
								'placeHolder' => __( "필드선택", 'mshop-members-s2' ),
								"options"     => array(
									"no"    => __( "사용안함", 'mshop-members-s2' ),
									"left"  => __( "왼쪽 숨김", 'mshop-members-s2' ),
									"right" => __( "오른쪽 숨김", 'mshop-members-s2' ),
									"email" => __( "이메일 숨김", 'mshop-members-s2' )
								)
							),
							array(
								'id'        => 'msm_security_redirect_url',
								'showIf'    => array( 'msm_use_access_control' => 'yes' ),
								'title'     => __( '이동할 페이지 URL 주소', 'mshop-members-s2' ),
								'className' => 'fluid',
								'type'      => 'Text',
								'default'   => home_url(),
							),
						)
					),
					array(
						'type'     => 'Section',
						'showIf'   => array( 'msm_use_access_control' => 'yes' ),
						'title'    => __( '사용자 등급별 URL 접근차단', 'mshop-members-s2' ),
						'elements' => array(
							array(
								"id"        => "msm_security_block_list",
								"default"   => MSM_Access_Control::get_default_block_fields(),
								"className" => "",
								"sortable"  => 'true',
								"editable"  => 'true',
								"repeater"  => 'true',
								"type"      => "SortableTable",
								"elements"  => array(

									array(
										"className" => "seven wide column fluid",
										'id'        => "path",
										"title"     => __( "경로 이름", 'mshop-members-s2' ),
										'default'   => '',
										"type"      => "Text",
									),
									array(
										"className"   => "seven wide column fluid",
										"id"          => "block_list",
										"title"       => __( "등급", 'mshop-members-s2' ),
										"type"        => "Select",
										"placeHolder" => __( "등급을 선택하세요.", 'mshop-members-s2' ),
										"multiple"    => true,
										"options"     => $this->get_role_field()
									)
								)
							)
						)
					),
					array(
						'type'     => 'Section',
						'showIf'   => array( 'msm_use_access_control' => 'yes' ),
						'title'    => __( 'URL 접근차단 예외규칙', 'mshop-members-s2' ),
						'elements' => array(
							array(
								"id"        => "msm_security_exception_list",
								"className" => "",
								"sortable"  => 'true',
								"editable"  => 'true',
								"repeater"  => 'true',
								"type"      => "SortableTable",
								"default"   => MSM_Access_Control::get_default_exception_fields(),
								"elements"  => array(
									array(
										'id'        => "path",
										"className" => "six wide column fluid",
										"title"     => __( "경로 이름", 'mshop-members-s2' ),
										'default'   => '',
										"type"      => "Text",
									),
									array(
										'id'        => "is_param",
										"className" => "one wide column",
										"title"     => __( "파라미터", 'mshop-members-s2' ),
										"type"      => "Toggle",
										'default'   => '',
									),
									array(
										'id'        => "is_path",
										"className" => "one wide column",
										"title"     => __( "경로", 'mshop-members-s2' ),
										"type"      => "Toggle",
										'default'   => '',
									),
									array(
										'id'        => "value",
										"className" => "six wide column fluid",
										"title"     => __( "값", 'mshop-members-s2' ),
										'default'   => '',
										"type"      => "Text",
									)
								)
							)
						)
					)
				)
			);
		}

		static function get_wp_social_login_install_guide() {
			return array(
				'type'     => 'Page',
				'title'    => '소셜로그인',
				'elements' => array(
					array(
						'type'           => 'Section',
						'title'          => '필수 플러그인',
						'hideSaveButton' => true,
						'elements'       => array(
							array(
								'id'       => 'wp_social_login_guide',
								'type'     => 'Label',
								'readonly' => 'yes',
								'default'  => '<p>WordPress Social Login 플러그인 설치가 필요합니다.</p><a target="_blank" href="https://wordpress.org/plugins/wordpress-social-login/">WordPress Social Login 플러그인 설치하기</a>'
							)
						)
					)
				)
			);
		}

		function enqueue_scripts() {
			wp_enqueue_style( 'mshop-setting-manager', MSM()->plugin_url() . '/includes/admin/setting-manager/css/setting-manager.min.css', array(), MSM_VERSION );
			wp_enqueue_script( 'mshop-setting-manager', MSM()->plugin_url() . '/includes/admin/setting-manager/js/setting-manager.min.js', array( 'jquery', 'jquery-ui-core', 'underscore' ), MSM_VERSION );
		}
		public function output() {
			require_once MSM()->plugin_path() . '/includes/admin/setting-manager/mshop-setting-helper.php';

			$settings = $this->get_setting_fields();

			$this->enqueue_scripts();

			$license_info = json_decode( get_option( 'msl_license_' . MSM()->slug(), json_encode( array(
				'slug'   => MSM()->slug(),
				'domain' => preg_replace( '#^https?://#', '', home_url() )
			) ) ), true );

			$license_info = apply_filters( 'mshop_get_license', $license_info, MSM()->slug() );
			wp_localize_script( 'mshop-setting-manager', 'mshop_setting_manager', array(
				'element'     => 'mshop-setting-wrapper',
				'ajaxurl'     => admin_url( 'admin-ajax.php' ),
				'action'      => msm_ajax_command( 'update_settings' ),
				'settings'    => $settings,
				'slug'        => MSM()->slug(),
				'domain'      => preg_replace( '#^https?://#', '', site_url() ),
				'licenseInfo' => json_encode( $license_info )
			) );

			if ( MSM_Personal_Info::enabled() ) {
				$next_schedule = MSM_Personal_Info::get_next_schedule();

				if ( ! empty( $next_schedule ) ) {
					?>
                    <div class="notice notice-info">
                        <p style="padding: 10px;">
                        <p><?php echo sprintf( __( '개인정보 이용 안내 메일 발송 예약작업이 등록되어 있습니다. 다음 실행 예정 시간은 %s 입니다.', 'mshop-members-s2' ), $next_schedule ); ?></p>
                        <a href="/wp-admin/admin.php?page=wc-status&tab=action-scheduler&status=pending&s=msm_personal_information_notification&action=-1&pahed=1&action2=-1"
                           target="_blank" class="button" style="margin-left: 10px;">예약작업 확인하기</a>
                    </div>
					<?php
				} else {
					?>
                    <div class="notice notice-error">
                        <p style="padding: 10px;"><?php echo __( '개인정보 이용 안내 메일 발송 기능이 활성화되어 있지만, 발송 처리를 위한 예약작업이 등록되지 않았습니다. 개인정보 설정을 확인하신 후, 저장 버튼을 클릭해주세요.', 'mshop-members-s2' ); ?></p>
                    </div>
					<?php
				}
			}

			if ( MSM_User_Agreement_Info::enabled() ) {
				$next_schedule = MSM_User_Agreement_Info::get_next_schedule();

				if ( ! empty( $next_schedule ) ) {
					?>
                    <div class="notice notice-info">
                        <p style="padding: 10px;">
                        <p><?php echo sprintf( __( '정기적 수신 동의 확인 안내 발송 예약작업이 등록되어 있습니다. 다음 실행 예정 시간은 %s 입니다.', 'mshop-members-s2' ), $next_schedule ); ?></p>
                        <a href="/wp-admin/admin.php?page=wc-status&tab=action-scheduler&status=pending&s=msm_agreement_information_notification&action=-1&pahed=1&action2=-1"
                           target="_blank" class="button" style="margin-left: 10px;">예약작업 확인하기</a>
                    </div>
					<?php
				} else {
					?>
                    <div class="notice notice-error">
                        <p style="padding: 10px;"><?php echo __( '정기적 수신 동의 확인 안내 발송 기능이 활성화되어 있지만, 발송 처리를 위한 예약작업이 등록되지 않았습니다. 수신 동의 현황을 확인하신 후, 저장 버튼을 클릭해주세요.', 'mshop-members-s2' ); ?></p>
                    </div>
					<?php
				}
			}

			if ( empty( get_option( 'msm_agreement_information_site_date' ) ) ) {
				update_option( 'msm_agreement_information_site_date', date( 'Y년 m월 d일', strtotime( current_time( 'mysql' ) ) ) );
			}

			?>
            <script>
                jQuery(document).ready(function() {
                    jQuery(this).trigger('mshop-setting-manager', ['mshop-setting-wrapper', '100', <?php echo json_encode( MSM_Setting_Helper::get_settings( $settings ) ); ?>, <?php echo json_encode( $license_info ); ?>, null]);
                });
            </script>

            <div id="mshop-setting-wrapper"></div>
			<?php
		}
	}

endif;

return new MSM_Settings_Members();


