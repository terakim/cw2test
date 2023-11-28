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
	exit; // Exit if accessed directly
}

class MSM_Meta_Box_Members_Form {

	private static $saved_meta_boxes = false;

	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ), 999 );
		add_action( 'save_post', array( __CLASS__, 'save_meta_boxes' ), 1, 2 );
		add_action( 'admin_footer', array( __CLASS__, 'output_widget_box' ), 1, 2 );
	}

	static function is_members_page() {
		global $pagenow;

		if ( 'post.php' != $pagenow && 'post-new.php' != $pagenow ) {
			return false;
		}

		if ( 'post.php' == $pagenow && 'mshop_members_form' != get_post_type( $_GET['post'] ) ) {
			return false;
		}

		if ( 'post-new.php' == $pagenow && 'mshop_members_form' != $_GET['post_type'] ) {
			return false;
		}

		return true;
	}

	public static function output_widget_box() {

		if ( ! self::is_members_page() ) {
			return;
		}

		?>
        <link rel="stylesheet" href="<?php echo MSM()->plugin_url() . '/assets/font-awesome/css/font-awesome.min.css'; ?>">
        <link rel="stylesheet" href="<?php echo MSM()->plugin_url() . '/assets/widget-box/css/widget-box.css'; ?>">
        <div class="msm-widget-box-container">
            <div id="msm-widget-panel">
                <div id="msm-widget-box"></div>
            </div>
        </div>


		<?php

		require_once MSM()->plugin_path() . '/includes/admin/setting-manager/mshop-setting-helper.php';

		$settings = self::get_setting_widget_box();

		self::enqueue_scripts();

		?>
        <script>
            jQuery( document ).ready( function () {
                jQuery( this ).trigger( 'mshop-form-designer', ['msm-widget-box', '800', <?php echo json_encode( self::get_settings() ); ?>, null, <?php echo json_encode( $settings ); ?>  ] );
            } );
        </script>
		<?php
	}
	public static function add_meta_boxes() {
		global $post;

		add_meta_box( 'mshop-members-form-designer-edit', __( '폼 관리자', 'mshop-members-s2' ), array( __CLASS__, 'output_form_designer' ), 'mshop_members_form', 'normal', 'core' );
		remove_meta_box( 'commentsdiv', 'mshop_members_form', 'normal' );
		remove_meta_box( 'commentstatusdiv', 'mshop_members_form', 'normal' );
	}
	public static function save_meta_boxes( $post_id, $post ) {
		if ( empty( $post_id ) || empty( $post ) || self::$saved_meta_boxes ) {
			return;
		}

		// Dont' save meta boxes for revisions or autosaves
		if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}

		// Check the post being saved == the $post_id to prevent triggering this call for other save_post events
		if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
			return;
		}

		// Check user has permission to edit
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		self::$saved_meta_boxes = true;

		if ( isset( $_REQUEST['msm-pre-conditions'] ) ) {
			$values = json_decode( stripslashes( $_REQUEST['msm-pre-conditions'] ), true );
			update_post_meta( $post_id, 'msm_pre_conditions', $_REQUEST['msm-pre-conditions'] );
			update_post_meta( $post_id, '_msm_pre_conditions', msm_get( $values, '_msm_pre_conditions' ) );
		}

		if ( isset( $_REQUEST['msm-submit-actions'] ) ) {
			$values = json_decode( stripslashes( $_REQUEST['msm-submit-actions'] ), true );
			update_post_meta( $post_id, 'msm_submit_actions', $_REQUEST['msm-submit-actions'] );
			update_post_meta( $post_id, '_submit_action', msm_get( $values, '_submit_action', 'msm_action_none' ) );
			update_post_meta( $post_id, '_custom_action', msm_get( $values, '_custom_action' ) );
			update_post_meta( $post_id, '_write_post_action_post_type', msm_get( $values, '_write_post_action_post_type' ) );
			update_post_meta( $post_id, '_write_post_action_post_status', msm_get( $values, '_write_post_action_post_status' ) );
			update_post_meta( $post_id, '_write_post_action_post_category', msm_get( $values, '_write_post_action_post_category' ) );
			update_post_meta( $post_id, '_application_role', msm_get( $values, '_application_role' ) );
			update_post_meta( $post_id, '_approve_method', msm_get( $values, '_approve_method', 'no' ) );
			update_post_meta( $post_id, '_msm_submit_actions', msm_get( $values, '_msm_submit_actions' ) );
		}

		if ( isset( $_REQUEST['msm-custom-style'] ) ) {
			$values = json_decode( stripslashes( $_REQUEST['msm-custom-style'] ), true );
			update_post_meta( $post_id, 'msm_custom_style', $_REQUEST['msm-custom-style'] );
			update_post_meta( $post_id, '_custom_css', msm_get( $values, '_custom_css' ) );
			update_post_meta( $post_id, '_custom_style', msm_get( $values, '_custom_style' ) );
		}
	}

	public static function output_form_designer( $post ) {
		require_once MSM()->plugin_path() . '/includes/admin/setting-manager/mshop-setting-helper.php';

		$setting_designer     = self::get_setting_fields_designer();
		$setting_actions      = self::get_setting_pre_conditions();
		$setting_actions2     = self::get_setting_submit_actions();
		$setting_custom_style = self::get_setting_custom_style();


		self::enqueue_scripts();
		wp_print_scripts( 'semantic-ui' );
		wp_print_styles( 'semantic-ui' );

		wp_localize_script( 'mshop-form-designer', 'mshop_setting_manager', array(
			'element'    => 'mshop-setting-wrapper',
			'siteurl'    => site_url(),
			'ajaxurl'    => admin_url( 'admin-ajax.php' ),
			'plugin_url' => MSM()->plugin_url(),
			'action'     => 'mshop_form_designer_update_settings',
			'slug'       => MSM_AJAX_PREFIX,
			'settings'   => $setting_designer
		) );

		?>
        <script>
            jQuery( document ).ready( function ( $ ) {
                $( '#msm-designer-panel .menu .item' ).tab();
                $( this ).trigger( 'mshop-form-designer', ['mshop-form-designer', '100', <?php echo json_encode( self::get_settings() ); ?>] );
                $( this ).trigger( 'mshop-setting-manager', ['mshop-setting-wrapper', '200', <?php echo json_encode( MSM_Setting_Helper::get_settings( $setting_actions, $post->ID ) ); ?>, null, <?php echo json_encode( $setting_actions ); ?>] );
                $( this ).trigger( 'mshop-setting-manager', ['mshop-setting-wrapper2', '300', <?php echo json_encode( MSM_Setting_Helper::get_settings( $setting_actions2, $post->ID ) ); ?>, null, <?php echo json_encode( $setting_actions2 ); ?>] );
                $( this ).trigger( 'mshop-setting-manager', ['mshop-setting-custom-style', '400', <?php echo json_encode( MSM_Setting_Helper::get_settings( $setting_custom_style, $post->ID ) ); ?>, null, <?php echo json_encode( $setting_custom_style ); ?>] );
            } );
        </script>
        <style>
            #mshop-members-form-designer-test .inside,
            #mshop-members-form-designer-edit .inside {
                margin: 0;
                padding: 5px;
                background-color: #f5f5f5;
            }

            #mshop-members-form-designer-edit .inside .ui.tabular.menu {
                padding-top: 0px;
            }

            #msm-designer-panel .ui.tab {
                display: none !important;
            }

            #msm-designer-panel .ui.tab.active {
                display: block !important;
            }
        </style>

        <div id="msm-designer-panel">
            <div class="ui top attached tabular menu">
                <a class="active item" data-tab="form-designer">폼 디자이너</a>
                <a class="item" data-tab="form-action">접근차단 설정</a>
                <a class="item" data-tab="form-action2">액션 설정</a>
                <a class="item" data-tab="form-custom-style">스타일 설정</a>
            </div>
            <div class="ui bottom attached active tab" data-tab="form-designer">
                <textarea class="wp-editor-area" style="display: none;" name="content" id="content"></textarea>

                <div id="mshop-form-designer"></div>
            </div>
            <div class="ui bottom attached tab" data-tab="form-action">
                <input type="text" name="msm-pre-conditions" style="display: none;"
                       value="<?php echo esc_html( get_post_meta( $post->ID, 'msm_pre_conditions', true ) ); ?>">

                <div class="mshop-setting-wrapper" id="mshop-setting-wrapper" style="padding: 0 !important;"></div>
            </div>
            <div class="ui bottom attached tab" data-tab="form-action2">
                <input type="text" name="msm-submit-actions" style="display: none;"
                       value="<?php echo esc_html( get_post_meta( $post->ID, 'msm_submit_actions', true ) ); ?>">

                <div class="mshop-setting-wrapper" id="mshop-setting-wrapper2" style="padding: 0 !important;"></div>
            </div>
            <div class="ui bottom attached tab" data-tab="form-custom-style">
                <input type="text" name="msm-custom-style" style="display: none;"
                       value="<?php echo esc_html( get_post_meta( $post->ID, 'msm_custom_style', true ) ); ?>">

                <div class="mshop-setting-wrapper" id="mshop-setting-custom-style" style="padding: 0 !important;"></div>
            </div>
        </div>

		<?php
	}

	static function get_settings() {
		$settings = array();

		$form     = new MSM_Form( get_the_ID() );
		$settings = array(
			'post_id'      => $form->id,
			'form_id'      => $form->form_id,
			'form_name'    => $form->form_name,
			'form_type'    => $form->form_type,
			'form_data'    => $form->form_data,
			'redirect_url' => $form->redirect_url,
		);

		return $settings;
	}

	static function get_setting_fields_designer() {
		return array(
			'type'     => 'Page',
			'class'    => 'active',
			'elements' => array(
				array(
					"id"       => "form_data",
					"type"     => "FormDesigner",
					"repeater" => true,
					"elements" => array(
						'form' => array(
							'id'                => 'form_data',
							'type'              => 'Section',
							"hideSectionHeader" => true,
							'elements'          => array()
						)
					)
				),
			)
		);
	}

	static function get_setting_pre_conditions() {
		return array(
			'type'        => 'Page',
			'title'       => '접근 차단 설정',
			'dom-element' => 'msm-pre-conditions',
			'class'       => 'active',
			'elements'    => array(
				array(
					"id"           => "_msm_pre_conditions",
					"type"         => "SortableList",
					"title"        => "접근 차단 규칙",
					"listItemType" => "MShopRule",
					"repeater"     => true,
					"template"     => array(
						'rule_type'  => 'role',
						'conditions' => array(),
					),
					"default"      => array(),
					"elements"     => array(
						'left' => array(
							'type'              => 'Section',
							'class'             => 'hidden',
							"hideSectionHeader" => true,
							'elements'          => array(
								array(
									"id"      => "rule_type",
									"showIf"  => array( 'hidden' => 'hidden' ),
									"type"    => "Select",
									'default' => 'role',
									'options' => array(
										'role'     => '사용자 역할',
										'usermeta' => '사용자 정보'
									),
								),
							)
						),
						'role' => array(
							'type'              => 'Section',
							'class'             => 'sixteen wide column',
							"hideSectionHeader" => true,
							'elements'          => array(
								array(
									"id"          => "role",
									"title"       => "적용대상",
									"placeholder" => "규칙을 적용할 사용자의 역할을 선택하세요.",
									"className"   => "fluid",
									"type"        => "Select",
									'default'     => '',
									'multiple'    => true,
									'options'     => apply_filters( 'msm_get_roles', array() ),
								),
								array(
									"id"        => "conditions",
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
									"id"        => "redirect",
									"title"     => "Redirect URL",
									"className" => "fluid",
									"type"      => "Text",
								)
							)
						)
					)
				)
			)
		);
	}

	static function get_setting_auto_next_conditions() {
		return array(
			'type'        => 'Page',
			'title'       => '페이지 넘김 설정',
			'dom-element' => 'msm-auto-next-conditions',
			'class'       => 'active',
			'elements'    => array(
				array(
					"id"        => "_msm_auto_next_conditions",
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
							"id"        => "field",
							"title"     => __( "필드명", 'mshop-members-s2' ),
							"className" => " five wide column fluid",
							"type"      => "Text",
							'default'   => ''
						),
						array(
							"id"        => "condition",
							"className" => " five wide column fluid",
							"title"     => __( "조건", 'mshop-members-s2' ),
							"type"      => "Select",
							'default'   => 'yes',
							'options'   => apply_filters( 'msm_auto_next_conditions', array(
								''          => '선택하세요',
								'empty'     => '값이 없으면 (Empty)',
								'not_empty' => '값이 있으면 (Not Empty)',
								'equal'     => '값이 같으면 (Equal)',
								'not_equal' => '값이 다르면 (Not Equal)'
							) ),
						),
						array(
							"id"        => "value",
							"title"     => __( "값", 'mshop-members-s2' ),
							"className" => " six wide column fluid",
							"type"      => "Text",
							'default'   => ''
						),
					)
				),
			)
		);
	}

	static function get_setting_custom_style() {
		return array(
			'type'        => 'Page',
			'title'       => '스타일 설정',
			'dom-element' => 'msm-custom-style',
			'class'       => 'active',
			'elements'    => array(
				array(
					'type'              => 'Section',
					'hideSectionHeader' => true,
					'elements'          => array(
						array(
							"id"      => "_custom_css",
							"type"    => "TextArea",
							"title"   => "CSS",
							"rows"    => "5",
							"default" => ""
						),
						array(
							"id"      => "_custom_style",
							"type"    => "TextArea",
							"title"   => "Style",
							"rows"    => "5",
							"default" => ""
						),
					)
				)
			)
		);
	}

	static function get_setting_submit_actions() {
		return array(
			'type'        => 'Page',
			'class'       => '',
			'dom-element' => 'msm-submit-actions',
			'title'       => '액션설정',
			'elements'    => array(
				array(
					'type'              => 'Section',
					'hideSectionHeader' => true,
					'elements'          => array(
						array(
							"id"          => "_submit_action",
							"title"       => __( "액션", 'mshop-members-s2' ),
							"placeHolder" => __( "액션 타입을 선택하세요.", 'mshop-members-s2' ),
							"className"   => "fluid",
							"type"        => "Select",
							'default'     => 'msm_action_none',
							'options'     => apply_filters( 'msm_submit_action', array(
								'msm_action_none'               => __( '없음', 'mshop-members-s2' ),
								'msm_action_login'              => __( '로그인', 'mshop-members-s2' ),
								'msm_action_register'           => __( '회원가입', 'mshop-members-s2' ),
								'msm_action_agreement'          => __( '약관동의', 'mshop-members-s2' ),
								'msm_action_ubsubscribe'        => __( '회원탈퇴', 'mshop-members-s2' ),
								'msm_action_find_login'         => __( '아이디/이메일 찾기', 'mshop-members-s2' ),
								'msm_action_lost_passwords'     => __( '비밀번호 찾기', 'mshop-members-s2' ),
								'msm_action_temporary_password' => __( '임시 비밀번호 발급', 'mshop-members-s2' ),
								'msm_action_write_post'         => __( '포스트 등록', 'mshop-members-s2' ),
								'msm_action_do_action'          => __( '커스텀 액션', 'mshop-members-s2' )
							) ),
						),
						array(
							"id"          => "_custom_action",
							"showIf"      => array( '_submit_action' => 'msm_action_do_action' ),
							"title"       => "커스텀 액션",
							'className'   => 'fluid',
							"placeholder" => "액션명을 입력하세요.",
							"type"        => "Text"
						),
						array(
							"id"           => "_write_post_action_post_type",
							"showIf"       => array( '_submit_action' => 'msm_action_write_post' ),
							"title"        => "포스트 타입",
							'className'    => 'search fluid',
							"placeHolder"  => "포스트 타입을 선택하세요.",
							"type"         => "SearchSelect",
							'search'       => true,
							"disableClear" => true,
							"action"       => 'action=' . msm_ajax_command( 'search_post_type&keyword=' )
						),
						array(
							"id"           => "_write_post_action_post_category",
							"showIf"       => array( '_submit_action' => 'msm_action_write_post' ),
							"title"        => "포스트 카테고리",
							'className'    => 'search fluid',
							"placeHolder"  => "포스트 카테고리를 선택하세요.",
							"type"         => "SearchSelect",
							'search'       => true,
							'multiple'     => true,
							"disableClear" => true,
							"action"       => 'action=' . msm_ajax_command( 'search_post_category&keyword=' )
						),
						array(
							"id"           => "_write_post_action_post_status",
							"showIf"       => array( '_submit_action' => 'msm_action_write_post' ),
							"title"        => "포스트 상태",
							'className'    => 'search fluid',
							"placeHolder"  => "포스트 상태를 선택하세요.",
							"type"         => "SearchSelect",
							'search'       => true,
							"disableClear" => true,
							"action"       => 'action=' . msm_ajax_command( 'search_post_status&keyword=' )
						),
					)
				),
				array(
					"id"           => "_msm_submit_actions",
					"title"        => "Post Actions",
					"type"         => "SortableList",
					"listItemType" => "MShopMembersSubmitAction",
					"repeater"     => true,
					"template"     => array(
						'action_type'   => 'redirect',
						'redirect_type' => 'url',
					),
					"default"      => array(),
					"elements"     => array_merge( array(
						'left' => array(
							'type'              => 'Section',
							"hideSectionHeader" => true,
							'elements'          => array(
								array(
									"id"          => "action_type",
									"title"       => __( "타입", 'mshop-members-s2' ),
									"placeHolder" => __( "액션 타입을 선택하세요.", 'mshop-members-s2' ),
									"className"   => "fluid",
									"type"        => "Select",
									'default'     => 'redirect',
									'options'     => apply_filters( 'msm_post_actions', array(
										'redirect'      => __( '리다이렉트', 'mshop-members-s2' ),
										'meta'          => __( '사용자 메타 업데이트', 'mshop-members-s2' ),
										'session'       => __( '세션저장', 'mshop-members-s2' ),
										'trigger'       => __( '이벤트 트리거', 'mshop-members-s2' ),
										'set_user_role' => __( '사용자 역할 설정', 'mshop-members-s2' ),
										'role'          => __( '역할설정', 'mshop-members-s2' ),
									) ),
								)
							)
						)
					),
						self::get_msm_post_actions_settings()
					)
				)
			)
		);
	}

	static function get_msm_post_actions_settings() {
		return apply_filters( 'msm_post_actions_settings', array(
			'redirect'      => array(
				'type'              => 'Section',
				"hideSectionHeader" => true,
				'elements'          => array(
					array(
						"id"          => "redirect_type",
						"title"       => __( "타입", 'mshop-members-s2' ),
						"placeholder" => __( "리다이렉트 타입을 선택하세요.", 'mshop-members-s2' ),
						"className"   => "fluid",
						"type"        => "Select",
						'options'     => array(
							'url'     => __( 'URL', 'mshop-members-s2' ),
							'message' => __( 'Message', 'mshop-members-s2' )
						),
					),
					array(
						"id"          => "redirect_url",
						"showIf"      => array( 'redirect_type' => 'url' ),
						"title"       => "URL",
						'className'   => 'fluid',
						"placeholder" => "이동할 URL 주소를 입력하세요. 빈칸을 입력하면 페이지를 새로고침 합니다.",
						"type"        => "Text"
					),
					array(
						"id"          => "redirect_message",
						"title"       => "Message",
						'className'   => 'fluid',
						"placeholder" => "사용자에게 보여줄 메시지를 입력하세요.",
						"type"        => "Text"
					),
					array(
						"id"        => "roles",
						"title"     => "역할별 설정",
						"showIf"    => array( "redirect_type" => "url" ),
						"className" => "",
						"editable"  => 'true',
						"type"      => "SortableTable",
						"template"  => array(
							'role' => '',
						),
						"elements"  => array(
							array(
								"id"          => "role",
								"title"       => __( "사용자 역할", 'mshop-members-s2' ),
								"className"   => " six wide column fluid",
								"type"        => "Select",
								"placeholder" => "사용자 역할",
								"options"     => apply_filters( 'msm_get_roles', array() )
							),
							array(
								"id"          => "redirect_url",
								"title"       => "URL",
								'className'   => ' nine wide column fluid',
								"type"        => "Text",
								"placeholder" => "이동할 URL 주소를 입력하세요. 빈칸을 입력하면 페이지를 새로고침 합니다.",
							)
						)
					)
				)
			),
			'trigger'       => array(
				'type'              => 'Section',
				"hideSectionHeader" => true,
				'elements'          => array(
					array(
						"id"          => "trigger",
						"title"       => "이벤트",
						'className'   => 'fluid',
						"placeholder" => "이벤트명을 입력하세요.",
						"type"        => "Text"
					)
				)
			),
			'meta'          => array(
				"id"        => "meta",
				"className" => "",
				"editable"  => 'true',
				"type"      => "SortableTable",
				"template"  => array(
					'field'      => '',
					'value'      => '',
					'meta_key'   => '',
					'meta_value' => '',
				),
				"elements"  => array(
					array(
						"id"          => "field",
						"title"       => __( "Field", 'mshop-members-s2' ),
						"className"   => " three wide column fluid",
						"type"        => "Text",
						"placeholder" => ""
					),
					array(
						"id"          => "value",
						"title"       => __( "Value", 'mshop-members-s2' ),
						"className"   => " four wide column fluid",
						"type"        => "Text",
						"placeholder" => ""
					),
					array(
						"id"          => "meta_key",
						"title"       => __( "Meta Key", 'mshop-members-s2' ),
						"className"   => " four wide column fluid",
						"type"        => "Text",
						"placeholder" => ""
					),
					array(
						"id"          => "meta_value",
						"title"       => __( "Meta Value", 'mshop-members-s2' ),
						"className"   => " four wide column fluid",
						"type"        => "Text",
						"placeholder" => ""
					)
				)
			),
			'session'       => array(
				"id"        => "session",
				"className" => "",
				"editable"  => 'true',
				"type"      => "SortableTable",
				"template"  => array(
					'field'         => '',
					'value'         => '',
					'session_key'   => '',
					'session_value' => '',
				),
				"elements"  => array(
					array(
						"id"          => "field",
						"title"       => __( "Field", 'mshop-members-s2' ),
						"className"   => " three wide column fluid",
						"type"        => "Text",
						"placeholder" => ""
					),
					array(
						"id"          => "value",
						"title"       => __( "Value", 'mshop-members-s2' ),
						"className"   => " four wide column fluid",
						"type"        => "Text",
						"placeholder" => ""
					),
					array(
						"id"          => "session_key",
						"title"       => __( "Session Key", 'mshop-members-s2' ),
						"className"   => " four wide column fluid",
						"type"        => "Text",
						"placeholder" => ""
					),
					array(
						"id"          => "session_value",
						"title"       => __( "Session Value", 'mshop-members-s2' ),
						"className"   => " four wide column fluid",
						"type"        => "Text",
						"placeholder" => ""
					)
				)
			),
			'set_user_role' => array(
				'type'              => 'Section',
				"hideSectionHeader" => true,
				'elements'          => array(
					array(
						"id"          => "role",
						"title"       => "사용자 역할",
						"placeholder" => "사용자 역할을 선택하세요.",
						"className"   => "",
						"type"        => "Select",
						"options"     => apply_filters( 'msm_get_roles', array() )
					)
				)
			),
			'role'          => array(
				'type'              => 'Section',
				"hideSectionHeader" => true,
				'elements'          => array(
					array(
						"id"          => "role",
						"title"       => "요청역할",
						"placeHolder" => "사용자 역할을 선택하세요.",
						"className"   => "",
						"type"        => "Select",
						"options"     => apply_filters( 'msm_get_roles', array() )
					),
					array(
						"id"          => "approve_method",
						"title"       => "승인 방식",
						"className"   => "",
						"placeHolder" => "역할 설정방식을 선택하세요.",
						"type"        => "Select",
						"default"     => "manual",
						"options"     => array(
							'auto'   => '자동승인',
							'manual' => '수동승인 (관리자 승인)'
						)
					)
				)
			)
		) );
	}

	static function get_widget_reserved() {
		return apply_filters( 'msm_widget_reserved', array(
			array(
				'type'     => 'Input',
				'property' => array(
					'name'        => 'login',
					'title'       => '아이디',
					'placeHolder' => '아이디를 입력하세요.',
					'type'        => 'text',
					'required'    => 'yes',
					'requiredMsg' => '아이디를 입력하세요.',
					'width'       => 'sixteen wide'
				),
				'icon'     => 'icon-user-id',
				'title'    => '아이디',
				'reserved' => array( 'name' )
			),
			array(
				'type'     => 'Input',
				'property' => array(
					'name'        => 'user_login',
					'title'       => '이메일',
					'placeHolder' => '이메일을 입력하세요.',
					'type'        => 'text',
					'required'    => 'yes',
					'requiredMsg' => '이메일을 입력하세요.',
					'width'       => 'sixteen wide'
				),
				'icon'     => 'icon-email',
				'title'    => '이메일',
				'reserved' => array( 'name' )
			),
			array(
				'type'     => 'Input',
				'property' => array(
					'name'        => 'password',
					'title'       => '비밀번호',
					'placeHolder' => '비밀번호를 입력하세요.',
					'type'        => 'password',
					'required'    => 'yes',
					'width'       => 'sixteen wide'
				),
				'icon'     => 'icon-password',
				'title'    => '비밀번호',
			),
			array(
				'type'     => 'Input',
				'property' => array(
					'name'        => 'confirm_password',
					'title'       => '비밀번호 확인',
					'placeHolder' => '비밀번호 확인을 입력하세요.',
					'type'        => 'password',
					'required'    => 'yes',
					'width'       => 'sixteen wide'
				),
				'icon'     => 'icon-confirm-password',
				'title'    => '비밀번호 확인',
			),
			array(
				'type'     => 'Input',
				'property' => array(
					'name'        => 'first_name',
					'title'       => '이름',
					'placeHolder' => '이름을 입력하세요.',
					'type'        => 'text',
					'required'    => 'yes',
					'width'       => 'sixteen wide'
				),
				'icon'     => 'icon-name',
				'title'    => '이름',
			),
			array(
				'type'     => 'Input',
				'property' => array(
					'name'        => 'billing_phone',
					'title'       => '전화번호',
					'placeHolder' => '전화번호를 입력하세요.',
					'type'        => 'text',
					'required'    => 'yes',
					'width'       => 'sixteen wide'
				),
				'icon'     => 'icon-phone',
				'title'    => '전화번호',
			),
			array(
				'type'     => 'ImageGallery',
				'property' => array(
					'name'     => 'media_gallery',
					'title'    => '미디어갤러리',
					'required' => 'yes',
					'width'    => 'sixteen wide'
				),
				'icon'     => 'icon-media-gallery',
				'title'    => '미디어갤러리',
			),
			array(
				'type'     => 'Birthday',
				'property' => array(
					'name'     => 'birthday',
					'title'    => '생년월일',
					'required' => 'yes',
					'width'    => 'sixteen wide'
				),
				'icon'     => 'icon-birthday',
				'title'    => '생년월일',
			),
			array(
				'type'     => 'Phone',
				'property' => array(
					'name'     => 'phone_number',
					'title'    => '휴대폰번호',
					'required' => 'yes',
					'width'    => 'sixteen wide'
				),
				'icon'     => 'icon-phone-number',
				'title'    => '휴대폰번호',
			),
			array(
				'type'     => 'Recaptcha',
				'property' => array(
					'name'     => 'recaptcha',
					'title'    => 'reCAPTCHA',
					'required' => 'yes',
					'width'    => 'sixteen wide'
				),
				'icon'     => 'icon-recaptcha',
				'title'    => 'reCAPTCHA',
			),
			array(
				'type'     => 'Payment',
				'property' => array(
					'width' => 'sixteen wide'
				),
				'icon'     => 'icon-payment-method',
				'title'    => '결제수단',
			),
			array(
				'type'     => 'Product',
				'property' => array(
					'type'  => 'custom',
					'width' => 'sixteen wide'
				),
				'icon'     => 'icon-payment-product',
				'title'    => '결제상품',
			),
			array(
				'type'     => 'Toggle',
				'property' => array(
					'name'      => 'mssms_agreement',
					'title'     => '문자 알림 수신 동의',
					'label'     => '광고성 문자 알림 수신에 동의합니다.',
					'checkType' => 'custom',
					'required'  => 'no',
					'default'   => '',
					'value'     => 'on',
					'width'     => 'sixteen wide'
				),
				'icon'     => 'icon-msms-agreement',
				'title'    => '문자 알림 수신 동의',
			),
			array(
				'type'     => 'Toggle',
				'property' => array(
					'name'      => 'email_agreement',
					'title'     => '이메일 알림 수신 동의',
					'label'     => '광고성 이메일 알림 수신에 동의합니다.',
					'checkType' => 'custom',
					'required'  => 'no',
					'default'   => '',
					'value'     => 'on',
					'width'     => 'sixteen wide'
				),
				'icon'     => 'icon-email-agreement',
				'title'    => '이메일 알림 수신 동의',
			),
			array(
				'type'     => 'RegionSelector',
				'property' => array(
					'name'     => 'region_selector',
					'title'    => '지역 선택',
					'required' => 'no',
					'width'    => 'sixteen wide'
				),
				'icon'     => 'icon-region',
				'title'    => '지역 선택',
			)
		) );
	}

	static function get_widget_custom() {
		return apply_filters( 'msm_widget_custom', array(
			array(
				'type'     => 'CustomAction',
				'property' => array(
					'class'  => '',
					'title'  => '커스텀 액션',
					'action' => ''
				),
				'icon'     => 'icon-custom-action',
				'title'    => '커스텀 액션',
			),
			array(
				'type'     => 'Shortcode',
				'property' => array(
					'class'  => '',
					'title'  => '숏코드',
					'action' => ''
				),
				'icon'     => 'icon-custom-action',
				'title'    => '숏코드',
			),
			array(
				'type'     => 'CustomTemplate',
				'property' => array(
					'class' => ''
				),
				'icon'     => 'icon-custom-template',
				'title'    => '커스텀 템플릿',
			),
			array(
				'type'     => 'CustomPage',
				'property' => array(
					'class' => ''
				),
				'icon'     => 'icon-custom-page',
				'title'    => '페이지',
			)
		) );
	}

	static function get_setting_widget_box() {
		return array(
			'type'     => 'Tab',
			'id'       => 'widget-box',
			'elements' => array(
				array(
					"id"      => "component_panel",
					"title"   => '예약필드',
					"class"   => "active",
					"type"    => "ComponentPanel",
					"widgets" => self::get_widget_reserved()
				),
				array(
					"id"      => "component_panel2",
					"title"   => '기본필드',
					"type"    => "ComponentPanel",
					"widgets" => array(
						array(
							'type'     => 'Header',
							'property' => array(
								'title' => '헤더',
								'class' => '',
								'width' => 'sixteen wide'
							),
							'icon'     => 'icon-header',
							'title'    => 'Header',
						),
						array(
							'type'     => 'Section',
							'property' => array(
								'title' => '섹션',
								'class' => '',
								'width' => 'sixteen wide'
							),
							'icon'     => 'icon-section',
							'title'    => 'Section',
						),
						array(
							'type'     => 'Input',
							'property' => array(
								'name'        => 'inputfield',
								'title'       => '입력필드',
								'placeHolder' => '안내문구를 입력하세요',
								'type'        => 'text',
								'width'       => 'sixteen wide'
							),
							'icon'     => 'icon-input',
							'title'    => 'Input',
						),
						array(
							'type'     => 'LabeledInput',
							'property' => array(
								'name'        => 'labeledinputfield',
								'title'       => '레이블 입력필드',
								'placeHolder' => '안내문구를 입력하세요',
								'type'        => 'text',
								'position'    => 'right',
								'label'       => 'Kg',
								'width'       => 'sixteen wide'
							),
							'icon'     => 'icon-labeledinput',
							'title'    => 'Labeled Input',
						),
						array(
							'type'     => 'Select',
							'property' => array(
								'name'      => 'selectfield',
								'title'     => '셀렉트박스',
								'data_type' => 'custom',
								'width'     => 'sixteen wide'
							),
							'icon'     => 'icon-select',
							'title'    => 'Select',
						),
						array(
							'type'     => 'Date',
							'property' => array(
								'name'  => 'datefield',
								'title' => 'Date Picker',
								'width' => 'sixteen wide'
							),
							'icon'     => 'icon-datepicker',
							'title'    => 'Date Picker',
						),
						array(
							'type'     => 'TextArea',
							'property' => array(
								'name'        => 'textarea',
								'title'       => 'TextArea',
								'placeHolder' => '안내문구를 입력하세요',
								'rows'        => 2,
								'required'    => 'no',
								'width'       => 'sixteen wide'
							),
							'icon'     => 'icon-textarea',
							'title'    => 'TextArea',
						),
						array(
							'type'     => 'Editor',
							'property' => array(
								'name'        => 'editor',
								'title'       => 'Editor',
								'placeHolder' => '안내문구를 입력하세요',
								'rows'        => 2,
								'required'    => 'no',
								'width'       => 'sixteen wide'
							),
							'icon'     => 'icon-editor',
							'title'    => 'Editor',
						),
						array(
							'type'     => 'Toggle',
							'property' => array(
								'name'      => 'toggle_field',
								'title'     => '토글',
								'label'     => '설명을 입력하세요',
								'checkType' => 'toggle',
								'required'  => 'no',
								'width'     => 'sixteen wide'
							),
							'icon'     => 'icon-toggle',
							'title'    => 'Toggle',
						),
						array(
							'type'     => 'Toggle',
							'property' => array(
								'name'      => 'checkbox_field',
								'title'     => '체크박스',
								'label'     => '설명을 입력하세요',
								'checkType' => 'checkbox',
								'required'  => 'no',
								'width'     => 'sixteen wide'
							),
							'icon'     => 'icon-checkbox',
							'title'    => 'CheckBox',
						),
						array(
							'type'     => 'Toggle',
							'property' => array(
								'name'      => 'slider_field',
								'title'     => '슬라이더',
								'label'     => '설명을 입력하세요',
								'checkType' => 'slider',
								'required'  => 'no',
								'width'     => 'sixteen wide'
							),
							'icon'     => 'icon-slider',
							'title'    => 'Slider',
						),
						array(
							'type'     => 'Button',
							'property' => array(
								'name'  => 'button',
								'title' => '버튼',
								'type'  => 'submit',
								'align' => 'center',
								'icon'  => 'sign in',
								'class' => 'small blue',
								'width' => 'sixteen wide'
							),
							'icon'     => 'icon-button',
							'title'    => 'Button',
						),
						array(
							'type'     => 'HyperLink',
							'property' => array(
								'title' => '링크',
								'url'   => '',
								'align' => 'center',
								'class' => '',
								'width' => 'sixteen wide'
							),
							'icon'     => 'icon-hyperlink',
							'title'    => 'HyperLink',
						),
						array(
							'type'     => 'Html',
							'property' => array(
								'html'  => '',
								'class' => '',
								'width' => 'sixteen wide'
							),
							'icon'     => 'icon-html',
							'title'    => 'HTML',
						),
						array(
							'type'     => 'ImagePicker',
							'property' => array(
								'name'     => 'image_picker',
								'title'    => 'Image Picker',
								'required' => 'no',
								'width'    => 'sixteen wide'
							),
							'icon'     => 'icon-imagepicker',
							'title'    => 'Image Picker',
						),
						array(
							'type'     => 'Label',
							'property' => array(
								'title'   => '레이블',
								'content' => '문구를 입력하세요.',
								'width'   => 'sixteen wide'
							),
							'icon'     => 'icon-labeled',
							'title'    => 'Label',
						)
					)
				),
				array(
					"id"      => "component_panel3",
					"title"   => '레이아웃',
					"type"    => "ComponentPanel",
					"widgets" => array(
						array(
							'type'     => 'StepContainer',
							'property' => array(
								'count' => 'two'
							),
							'icon'     => 'icon-step-container',
							'title'    => 'STEP Container',
						),
						array(
							'type'     => 'StepItem',
							'property' => array(
								'title' => '제목',
								'desc'  => '설명을 입력하세요.'
							),
							'icon'     => 'icon-step-item',
							'title'    => 'STEP Item',
						),
						array(
							'type'     => 'FormField',
							'property' => array(
								'id'    => 'formfield',
								'title' => '폼필드',
								'class' => 'two fields'
							),
							'icon'     => 'icon-fieldgroup',
							'title'    => 'Field Group',
						),
					)
				),
				array(
					"id"      => "component_panel4",
					"title"   => '약관',
					"type"    => "ComponentPanel",
					"widgets" => array(
						array(
							'type'     => 'Toggle',
							'property' => array(
								'name'      => 'agree_all',
								'class'     => 'agree_all',
								'title'     => '',
								'label'     => '전체동의',
								'required'  => 'no',
								'checkType' => ' ',
								'width'     => 'sixteen wide'
							),
							'icon'     => 'icon-agreement-all',
							'title'    => '전체동의',
						),
						array(
							'type'     => 'Agreement',
							'property' => array(
								'class'        => '',
								'display_type' => 'standard',
								'width'        => 'sixteen wide'
							),
							'icon'     => 'icon-agreement-normal',
							'title'    => '이용약관 (표준)',
						),
						array(
							'type'     => 'Agreement',
							'property' => array(
								'class'        => '',
								'display_type' => 'accordion',
								'width'        => 'sixteen wide'
							),
							'icon'     => 'icon-agreement-accordion',
							'title'    => '이용약관 (아코디언)',
						),
						array(
							'type'     => 'Agreement',
							'property' => array(
								'class'        => '',
								'display_type' => 'page',
								'width'        => 'sixteen wide'
							),
							'icon'     => 'icon-agreement-page',
							'title'    => '이용약관 (페이지)',
						),
					)
				),
				array(
					"id"      => "social_panel",
					"title"   => '소셜로그인',
					"type"    => "ComponentPanel",
					"widgets" => array(
						array(
							'type'     => 'Social',
							'property' => array(
								'channel' => 'Facebook',
								'type'    => 'icon_text_2',
								'width'   => 'four wide',
								'align'   => 'center',
								'height'  => 40
							),
							'title'    => '페이스북',
						),
						array(
							'type'     => 'Social',
							'property' => array(
								'channel' => 'Google',
								'type'    => 'icon_text_2',
								'width'   => 'four wide',
								'align'   => 'center',
								'height'  => 40
							),
							'title'    => '구글',
						),
						array(
							'type'     => 'Social',
							'property' => array(
								'channel' => 'Kakao',
								'type'    => 'icon_text_2',
								'width'   => 'four wide',
								'align'   => 'center',
								'height'  => 40
							),
							'title'    => '카카오톡',
						),
						array(
							'type'     => 'Social',
							'property' => array(
								'channel' => 'Line',
								'type'    => 'icon_text_2',
								'width'   => 'four wide',
								'align'   => 'center',
								'height'  => 40
							),
							'title'    => '라인',
						),
						array(
							'type'     => 'Social',
							'property' => array(
								'channel' => 'Naver',
								'type'    => 'icon_text_2',
								'width'   => 'four wide',
								'align'   => 'center',
								'height'  => 40
							),
							'title'    => '네이버',
						),
						array(
							'type'     => 'Social',
							'property' => array(
								'channel' => 'Apple',
								'type'    => 'icon_text_2',
								'width'   => 'four wide',
								'align'   => 'center',
								'height'  => 40
							),
							'title'    => '애플',
						),
					)
				),
				array(
					"id"      => "custom_hook",
					"title"   => '커스텀',
					"type"    => "ComponentPanel",
					"widgets" => self::get_widget_custom()
				),
			)
		);
	}


	static function enqueue_scripts() {
		do_action( 'msm_form_designer_enqueue_scripts' );

		wp_enqueue_style( 'mshop-form-designer', MSM()->plugin_url() . '/includes/admin/form-designer/css/form-designer.min.css', array(), MSM_VERSION );
		wp_enqueue_script( 'mshop-form-designer', MSM()->plugin_url() . '/includes/admin/form-designer/js/form-designer.min.js', array(
			'jquery',
			'jquery-ui-core',
			'post',
			'postbox'
		), MSM_VERSION );
		wp_localize_script( 'mshop-form-designer', '_msfd', array(
			'siteurl'        => site_url(),
			'ajaxurl'        => admin_url( 'admin-ajax.php' ),
			'slug'           => MSM_AJAX_PREFIX,
			'base_image_url' => MSM()->plugin_url() . '/assets/images/'
		) );

		wp_enqueue_style( 'mshop-setting-manager', MSM()->plugin_url() . '/includes/admin/setting-manager/css/setting-manager.min.css', array(), MSM_VERSION );
		wp_enqueue_script( 'mshop-setting-manager', MSM()->plugin_url() . '/includes/admin/setting-manager/js/setting-manager.min.js', array( 'jquery', 'jquery-ui-core', 'underscore', 'post', 'postbox' ), MSM_VERSION );
	}
}
