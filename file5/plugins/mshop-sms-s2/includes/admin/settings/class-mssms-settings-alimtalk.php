<?php

/*
=====================================================================================
                ﻿엠샵 문자 알림톡 자동 발송 / Copyright 2015 by CodeM(c)
=====================================================================================

  [ 우커머스 버전 지원 안내 ]

   워드프레스 버전 : WordPress 4.3.1

   우커머스 버전 : WooCommerce 2.4.7


  [ 코드엠 플러그인 라이센스 규정 ]

   (주)코드엠에서 개발된 워드프레스  플러그인을 사용하시는 분들에게는 다음 사항에 대한 동의가 있는 것으로 간주합니다.

   1. 코드엠에서 개발한 워드프레스 우커머스용 엠샵 문자 알림톡 자동 발송 플러그인의 저작권은 (주)코드엠에게 있습니다.
   
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

if ( ! class_exists( 'MSSMS_Settings_Alimtalk' ) ) :

	class MSSMS_Settings_Alimtalk {
		static function init() {
			add_filter( 'msshelper_get_mssms_profile_list', array( __CLASS__, 'get_profile_list' ) );
		}

		protected static function get_resend_send_no_list() {
			return array_combine( MSSMS_Manager::get_send_nos(), MSSMS_Manager::get_send_nos() );
		}
		protected static function get_categories() {
			try {
				return MSSMS_API_Kakao::get_category();
			} catch ( Exception $e ) {

			}
		}

		static function get_plus_ids() {
			$profiles = get_option( 'mssms_profile_lists', array() );

			$plus_ids = array_column( $profiles, 'plus_id' );

			return array_combine( $plus_ids, $plus_ids );
		}
		static function get_profile_list() {
			try {
				$profiles = MSSMS_API_Kakao::get_profile_list();

				if ( ! empty( $profiles ) ) {
					update_option( 'mssms_profile_lists', json_decode( json_encode( $profiles ), true ) );
				} else {
					update_option( 'mssms_profile_lists', array(), true );
				}

				return $profiles;
			} catch ( Exception $e ) {
				return array();
			}
		}

		static function update_settings() {
			include_once MSSMS()->plugin_path() . '/includes/admin/setting-manager/mssms-helper.php';

			$_REQUEST = array_merge( $_REQUEST, json_decode( stripslashes( $_REQUEST['values'] ), true ) );

			MSSMS_Helper::update_settings( self::get_setting_fields() );

			delete_transient( 'mssms_point_shortage_notification' );

			wp_send_json_success();
		}

		static function get_setting_fields() {
			return array(
				'type'     => 'Tab',
				'id'       => 'mssms-setting-tab',
				'elements' => array(
					self::get_basic_setting(),
					self::get_plusfriend_setting()
				)
			);
		}

		static function get_basic_setting() {
			ob_start();
			include( 'html/common-setting-guide.php' );
			$common_setting_guide = ob_get_clean();

			return array(
				'type'     => 'Page',
				'class'    => 'active',
				'title'    => __( '기본 설정', 'mshop-sms-s2' ),
				'elements' => array(
					array(
						'type'     => 'Section',
						'title'    => __( '알림톡 서비스', 'mshop-sms-s2' ),
						'elements' => array(
							array(
								"id"        => "mssms_use_alimtalk",
								"title"     => "사용",
								"className" => "fluid",
								"type"      => "Toggle",
								"default"   => "no",
								"desc"      => __( "카카오 알림톡 서비스를 사용합니다.", 'mshop-sms-s2' )
							),
						)
					),
					array(
						'type'     => 'Section',
						'title'    => __( '공통 설정', 'mshop-sms-s2' ),
						"showIf"   => array( "mssms_use_alimtalk" => "yes" ),
						'elements' => array(
							array(
								"id"        => "mssms_common_setting_guide",
								"className" => "fluid",
								"type"      => "Label",
								"readonly"  => "yes",
								"default"   => $common_setting_guide
							),
							array(
								"id"        => "mssms_admins",
								"title"     => __( "관리자 정보", 'mshop-sms-s2' ),
								"className" => "",
								"type"      => "SortableTable",
								"repeater"  => true,
								"editable"  => true,
								"sortable"  => true,
								"default"   => array(),
								'tooltip'   => array(
									'title' => array(
										'title'   => __( '관리자 정보', 'mshop-sms-s2' ),
										'content' => __( '문자 포인트 부족시 관리자에게 이메일로 알림이 발송됩니다.', 'mshop-sms-s2' )
									)
								),
								"template"  => array(
									"enable" => "yes"
								),
								"elements"  => array(
									array(
										"id"        => "enable",
										"title"     => "사용",
										"className" => "center aligned one wide column fluid",
										"type"      => "Toggle",
										"default"   => "yes"
									),
									array(
										"id"          => "name",
										"title"       => __( "이름", 'mshop-sms-s2' ),
										"className"   => "center aligned four wide column fluid",
										"type"        => "Text",
										"placeholder" => "이름을 입력하세요."
									),
									array(
										"id"          => "email",
										"title"       => __( "이메일", 'mshop-sms-s2' ),
										"className"   => "center aligned four wide column fluid",
										"type"        => "Text",
										"placeholder" => "이메일 주소를 입력하세요."
									),
									array(
										"id"          => "phone",
										"title"       => __( "휴대폰번호", 'mshop-sms-s2' ),
										"className"   => "center aligned five wide column fluid",
										"type"        => "Text",
										"placeholder" => "문자수신이 가능한 전화번호를 입력하세요."
									)
								)
							),
							array(
								"id"        => "mssms_send_point_shortage_notification",
								"title"     => "포인트 소진 알림",
								"className" => "fluid",
								"type"      => "Toggle",
								"default"   => "yes",
								"desc"      => __( "문자 포인트 소진 시 관리자에게 알림 메일을 발송합니다.", 'mshop-sms-s2' )
							),
							array(
								"id"        => "mssms_point_shortage_threshold",
								"title"     => "포인트 소진 알림 기준값",
								"showIf"    => array( "mssms_send_point_shortage_notification" => "yes" ),
								"className" => "",
								"type"      => "LabeledInput",
								"label"     => __( "포인트", 'mshop-sms-s2' ),
								"default"   => "2000",
								"desc2"     => __( "<div class='desc2'>보유 포인트가 지정된 포인트 보다 부족하면 관리자에게 이메일로 알림이 전달됩니다.</div>", "mshop-sms-s2" ),
							),
							array(
								"id"        => "mssms_remain_point",
								"title"     => "보유 포인트",
								"className" => "fluid",
								"type"      => "Label",
								"readonly"  => "yes",
								"default"   => MSSMS_Manager::get_point()
							),
							array(
								"id"        => "mssms_send_block_time",
								"className" => "",
								"title"     => "발송 금지 시간",
								"type"      => "SortableTable",
								"repeater"  => true,
								"editable"  => true,
								"sortable"  => true,
								"default"   => array(),
								"template"  => array(
									"enable" => "yes"
								),
								"elements"  => array(
									array(
										"id"        => "enable",
										"title"     => "활성화",
										"className" => "center aligned one wide column fluid",
										"type"      => "Toggle",
										"default"   => "yes"
									),
									array(
										"id"          => "from",
										"title"       => __( "시작시간", 'mshop-sms-s2' ),
										"className"   => "center aligned three wide column fluid",
										"type"        => "Select",
										"placeholder" => "발송금지 시작 시간",
										"options"     => MSSMS_Manager::get_time_options()
									),
									array(
										"id"          => "to",
										"title"       => __( "종료시간", 'mshop-sms-s2' ),
										"className"   => "center aligned three wide column fluid",
										"type"        => "Select",
										"placeholder" => "발송금지 종료 시간",
										"options"     => MSSMS_Manager::get_time_options()
									),
									array(
										"id"        => "comment",
										"title"     => __( "관리자 메모", 'mshop-sms-s2' ),
										"className" => "center aligned seven wide column fluid",
										"type"      => "Text"
									)
								)
							),
							array(
								"id"        => "mssms_use_shipping_info",
								"title"     => "배송지 정보 사용",
								"className" => "fluid",
								"type"      => "Toggle",
								"default"   => "no",
								"desc"      => __( "고객에게 문자/알림톡 발송 시 배송지 정보에 기재된 전화번호와 이름을 사용합니다.", 'mshop-sms-s2' )
							),
						)
					)
				)
			);
		}

		static function get_plusfriend_setting() {
			ob_start();
			include( 'html/alimtalk-plus-friend-guide.php' );
			$plus_friend_guide = ob_get_clean();

			return array(
				'type'     => 'Page',
				'class'    => '',
				"showIf"   => array( "mssms_use_alimtalk" => "yes" ),
				'title'    => __( '카카오톡 채널 설정', 'mshop-sms-s2' ),
				'elements' => array(
					array(
						'type'           => 'Section',
						'title'          => __( '카카오톡 채널 목록', 'mshop-sms-s2' ),
						"hideSaveButton" => true,
						"showIf"         => array( "mssms_use_alimtalk" => "yes" ),
						'elements'       => array(
							array(
								"id"          => "mssms_profile_list",
								"className"   => "",
								"type"        => "SortableTable",
								"repeater"    => true,
								"default"     => array(),
								"template"    => array(
									"enable" => "yes"
								),
								"noResultMsg" => __( '<div style="text-align: center; font-weight: bold; color: #ff9b1b;"><span>등록된 프로필 정보가 없습니다.</span></div>', 'mshop-sms-s2' ),
								"elements"    => array(
									array(
										"id"            => "plus_id",
										"title"         => "카카오톡 채널 아이디",
										"className"     => "center aligned three wide column fluid",
										"cellClassName" => "center aligned",
										"type"          => "Label",
										"readOnly"      => true
									),
									array(
										"id"            => "status",
										"title"         => __( "상태", 'mshop-sms-s2' ),
										"className"     => "center aligned one wide column fluid",
										"cellClassName" => "center aligned",
										"type"          => "Label",
										"readOnly"      => true
									),
									array(
										"id"            => "count",
										"title"         => __( "발송건수", 'mshop-sms-s2' ),
										"className"     => "center aligned two wide column fluid",
										"cellClassName" => "center aligned",
										"type"          => "Label",
										"readOnly"      => true
									),
									array(
										"id"            => "create_date",
										"title"         => __( "등록일", 'mshop-sms-s2' ),
										"className"     => "center aligned three wide column fluid",
										"cellClassName" => "center aligned",
										"type"          => "Label",
										"readOnly"      => true
									),
									array(
										"id"        => "is_resend",
										"title"     => __( "대체발송", 'mshop-sms-s2' ),
										"className" => "center aligned one wide column fluid",
										"type"      => "Toggle"
									),
									array(
										"id"          => "resend_send_no",
										"showIf"      => array( "is_resend" => "yes" ),
										"title"       => __( "발신번호", 'mshop-sms-s2' ),
										"className"   => "center aligned three wide column fluid",
										"type"        => "Select",
										"placeholder" => __( '발신번호', 'mshop-sms-s2' ),
										"options"     => self::get_resend_send_no_list()
									),
									array(
										'id'             => 'update_resend',
										'title'          => '',
										'label'          => '적용',
										'className'      => 'one wide column yellow',
										'type'           => 'Button',
										'default'        => '',
										'actionType'     => 'ajax',
										'confirmMessage' => __( '대체발송 설정을 업데이트 하시겠습니까?', 'mshop-sms-s2' ),
										'ajaxurl'        => admin_url( 'admin-ajax.php' ),
										'action'         => MSSMS()->slug() . '-alimtalk_update_resend',
									),
								)
							),
							array(
								"id"        => "mssms_alimtalk_plus_friends_guide",
								"className" => "fluid",
								"type"      => "Label",
								"readonly"  => "yes",
								"default"   => $plus_friend_guide
							)
						)
					),
					array(
						'type'           => 'Section',
						"showIf"         => array( "mssms_use_alimtalk" => "yes" ),
						"hideSaveButton" => true,
						'title'          => __( '카카오톡 채널 추가하기', 'mshop-sms-s2' ),
						'elements'       => array(
							array(
								"id"        => "mssms_profile_authentication_request",
								"className" => "",
								"type"      => "SortableTable",
								"repeater"  => true,
                                "readonly"  => true,
								"default"   => array(
									array(
										'plus_id'            => '',
										'admin_phone_number' => ''
									)
								),
								"template"  => array(
									"enable" => "yes"
								),
								"elements"  => array(
									array(
										"id"          => "plus_id",
										"title"       => "카카오톡 채널 아이디",
										"className"   => "center aligned three wide column fluid",
										"type"        => "Text",
										"placeholder" => __( '@아이디', 'mshop-sms-s2' ),
									),
									array(
										"id"          => "admin_phone_number",
										"title"       => __( "관리자 전화번호", 'mshop-sms-s2' ),
										"className"   => "center aligned three wide column fluid",
										"type"        => "Text",
										"placeholder" => __( '000-0000-0000', 'mshop-sms-s2' ),
									),
									array(
										"id"          => "category",
										"title"       => __( "카테고리", 'mshop-sms-s2' ),
										"className"   => "center aligned three wide column search fluid",
										"type"        => "Select",
										"default"     => "",
										"placeholder" => __( '카테고리 선택', 'mshop-sms-s2' ),
										"options"     => self::get_categories()
									),
									array(
										'id'             => 'mssms_alimtalk_authentication_request',
										'title'          => '',
										'label'          => '인증요청',
										'className'      => 'one wide column yellow',
										'type'           => 'Button',
										'default'        => '',
										'actionType'     => 'ajax',
										'confirmMessage' => __( '카카오톡 채널 인증을 요청하시겠습니까?', 'mshop-sms-s2' ),
										'ajaxurl'        => admin_url( 'admin-ajax.php' ),
										'action'         => MSSMS()->slug() . '-alimtalk_authentication_request',
									),
									array(
										"id"          => "auth_number",
										"title"       => "인증번호",
										"className"   => "center aligned three wide column fluid",
										"type"        => "Text",
										"placeholder" => __( '인증번호를 입력해주세요.', 'mshop-sms-s2' ),
									),
									array(
										'id'             => 'mssms_alimtalk_add_profile',
										'title'          => '',
										'label'          => '등록하기',
										'className'      => 'one wide column yellow',
										'type'           => 'Button',
										'default'        => '',
										'actionType'     => 'ajax',
										'confirmMessage' => __( '카카오톡 채널 등록 심사를 요청하시겠습니까?', 'mshop-sms-s2' ),
										'ajaxurl'        => admin_url( 'admin-ajax.php' ),
										'action'         => MSSMS()->slug() . '-alimtalk_add_profile',
									),
								)
							)
						)
					)
				)
			);
		}

		static function enqueue_scripts() {
			wp_enqueue_script( 'underscore' );
			wp_enqueue_style( 'mshop-setting-manager', MSSMS()->plugin_url() . '/includes/admin/setting-manager/css/setting-manager.min.css' );
			wp_enqueue_script( 'mshop-setting-manager', MSSMS()->plugin_url() . '/includes/admin/setting-manager/js/setting-manager.min.js', array(
				'jquery',
				'jquery-ui-core'
			) );
		}
		public static function output() {
			require_once MSSMS()->plugin_path() . '/includes/admin/setting-manager/mssms-helper.php';

			$settings = self::get_setting_fields();

			self::enqueue_scripts();

			$license_info = json_decode( get_option( 'msl_license_' . MSSMS()->slug(), json_encode( array(
				'slug'   => MSSMS()->slug(),
				'domain' => preg_replace( '#^https?://#', '', mssms_home_url() )
			) ) ), true );

			$license_info = apply_filters( 'mshop_get_license', $license_info, MSSMS()->slug() );

			wp_localize_script( 'mshop-setting-manager', 'mshop_setting_manager', array(
				'element'     => 'mshop-setting-wrapper',
				'ajaxurl'     => admin_url( 'admin-ajax.php' ),
				'action'      => MSSMS()->slug() . '-update_alimtalk_settings',
				'settings'    => $settings,
				'slug'        => MSSMS()->slug(),
				'domain'      => preg_replace( '#^https?://#', '', mssms_home_url() ),
				'licenseInfo' => json_encode( $license_info ),
				'locale'      => get_locale(),
			) );

			?>
            <script>
                jQuery( document ).ready( function () {
                    jQuery( this ).trigger( 'mshop-setting-manager', ['mshop-setting-wrapper', '100', <?php echo json_encode( MSSMS_Helper::get_settings( $settings ) ); ?>,  <?php echo json_encode( $license_info ); ?>, null] );
                } );
            </script>

            <div id="mshop-setting-wrapper"></div>
			<?php
		}
	}

	MSSMS_Settings_Alimtalk::init();

endif;






