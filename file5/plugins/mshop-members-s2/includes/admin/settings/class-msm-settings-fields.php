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

if ( ! defined( 'ABSPATH' ) ){
	exit;
}

if ( ! class_exists( 'MSM_Settings_Fields' ) ) :

class MSM_Settings_Fields {

	public static function update_settings() {
		require_once MSM()->plugin_path() . '/includes/admin/setting-manager/mshop-setting-helper.php';

		$_REQUEST = array_merge( $_REQUEST, json_decode( stripslashes( $_REQUEST['values'] ), true ) );

		MSM_Setting_Helper::update_settings( self::get_setting_fields() );

		wp_send_json_success();
	}

	static function get_setting_fields() {
		return array(
			'type'     => 'Page',
			'title'    => '멤버스 필드',
			'class'    => 'active',
			'elements' => array(
				array(
					"id"           => "msm_select_fields",
					"type"         => "SortableList",
					"title"        => "필드 목록",
					"listItemType" => "MShopRule",
					"repeater"     => true,
					"template"     => array(
						'rule_type' => 'property',
						'values'    => array()
					),
					"keyFields"    => array(
						'slug' => array(
							'type'  => 'text',
							'label' => '슬러그'
						),
						'name' => array(
							'type'  => 'text',
							'label' => '필드명'
						),
					),
					"default"      => array(),
					"elements"     => array(
						'left'     => array(
							'type'              => 'Section',
							"hideSectionHeader" => true,
							"class"             => 'six wide column',
							'elements'          => array(
								array(
									"id"        => "rule_type",
									"title"     => "규칙종류",
									"showIf"    => array( 'hidden' => 'hidden' ),
									"className" => "fluid",
									"type"      => "Select",
									'default'   => 'property',
									'options'   => array(
										'property' => '필드명'
									),
								),
								array(
									"id"        => "slug",
									"title"     => "슬러그 (slug)",
									"className" => "fluid",
									"type"      => "Text",
								),
								array(
									"id"        => "name",
									"title"     => "필드명",
									"className" => "fluid",
									"type"      => "Text",
								)
							)
						),
						'property' => array(
							'type'     => 'Section',
							"title"    => "필드 목록",
							"class"    => 'ten wide column',
							'elements' => array(
								array(
									"id"        => "values",
									"className" => "",
									"editable"  => 'true',
									"sortable"  => 'true',
									"type"      => "SortableTable",
									"template"  => array(
										'slug' => '',
										'name' => '',
										'url'  => ''
									),
									"elements"  => array(
										array(
											"id"          => "slug",
											"title"       => __( "키", 'mshop-members-s2' ),
											"className"   => " four wide column fluid",
											"type"        => "Text",
											"placeholder" => ""
										),
										array(
											"id"          => "name",
											"className"   => " four wide column fluid",
											"title"       => __( "레이블", 'mshop-members-s2' ),
											"type"        => "Text",
											"placeholder" => ""
										),
										array(
											"id"          => "url",
											"title"       => __( "썸네일 이미지 URL", 'mshop-members-s2' ),
											"className"   => " seven wide column fluid",
											"type"        => "Text",
											"placeholder" => ""
										)
									)
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
		wp_enqueue_script( 'mshop-setting-manager', MSM()->plugin_url() . '/includes/admin/setting-manager/js/setting-manager.min.js', array ( 'jquery', 'jquery-ui-core', 'underscore' ), MSM_VERSION );
	}
	public static function output() {
		require_once MSM()->plugin_path() . '/includes/admin/setting-manager/mshop-setting-helper.php';

		$settings = self::get_setting_fields();

		self::enqueue_scripts();

		wp_localize_script( 'mshop-setting-manager', 'mshop_setting_manager', array(
			'element'  => 'mshop-setting-wrapper',
			'ajaxurl'  => admin_url( 'admin-ajax.php' ),
			'action'   => msm_ajax_command( 'update_field_settings' ),
			'settings' => $settings
		) );

		?>
		<script>
			jQuery(document).ready(function () {
				jQuery(this).trigger('mshop-setting-manager', ['mshop-setting-wrapper', '100', <?php echo json_encode( MSM_Setting_Helper::get_settings( $settings ) ); ?>, null, null]);
			});
		</script>

		<div id="mshop-setting-wrapper"></div>
		<?php
	}
}

endif;


