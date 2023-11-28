<?php

/*
=====================================================================================
                엠샵 프리미엄 포인트 / Copyright 2014-2015 by CodeM(c)
=====================================================================================

  [ 우커머스 버전 지원 안내 ]

   워드프레스 버전 : WordPress 4.3.1 이상

   우커머스 버전 : WooCommerce 3.0 이상


  [ 코드엠 플러그인 라이센스 규정 ]

   (주)코드엠에서 개발된 워드프레스  플러그인을 사용하시는 분들에게는 다음 사항에 대한 동의가 있는 것으로 간주합니다.

   1. 코드엠에서 개발한 워드프레스 우커머스용 엠샵 프리미엄 포인트 플러그인의 저작권은 (주)코드엠에게 있습니다.
   
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

if ( ! class_exists( 'MSPS_Settings_Volatile' ) ) {

	class MSPS_Settings_Volatile {

		static function update_settings() {
			include_once MSPS()->plugin_path() . '/includes/admin/setting-manager/mshop-setting-helper.php';
			$_REQUEST = array_merge( $_REQUEST, json_decode( stripslashes( $_REQUEST['values'] ), true ) );

			MSSHelper::update_settings( self::get_setting_fields() );

			MSPS_Volatile_Wallet::maybe_update_scheduled_actions();

			wp_send_json_success();
		}

		static function get_setting_fields() {
			return array(
				'type'     => 'Page',
				'title'    => __( '포인트 지갑 ( Wallet ) 설정', 'mshop-point-ex' ),
				'class'    => '',
				'elements' => array(
					array(
						'type'     => 'Section',
						'title'    => __( '포인트 소멸  설정', 'mshop-point-ex' ),
						'elements' => array(
							array(
								'id'        => 'msps_use_volatile_extinction',
								'title'     => __( '유효기간 만료 시 자동 소멸', 'mshop-point-ex' ),
								'className' => '',
								'type'      => 'Toggle',
								'default'   => 'no',
								'desc'      => __( '<div class="desc2">기간제한 포인트 월렛의 유효기간이 경과하면 자동으로 소멸처리가 진행되고 포인트 로그에 소멸 로그를 기록합니다.</div>', 'mshop-point-ex' )
							),
						)
					),
					array(
						'type'     => 'Section',
						"title"    => "포인트 월렛 ( Wallet ) 목록",
						"class"    => 'ten wide column',
						'elements' => array(
							array(
								"id"        => "msps_volatile_wallets",
								"title"     => __( "포인트 월렛 ( Wallet )", 'mshop-point-ex' ),
								"className" => "",
								"type"      => "SortableTable",
								"editable"  => "true",
								"sortable"  => "true",
								"repeater"  => true,
								"template"  => array(
									'enabled' => 'yes',
									'id'      => '',
									'name'    => '',
								),
								"elements"  => array(
									array(
										'id'            => 'enabled',
										'title'         => __( '활성화', 'mshop-point-ex' ),
										'cellClassName' => '',
										'className'     => 'center aligned one wide column',
										'type'          => 'Toggle',
										'default'       => 'yes',
									),
									array(
										"id"            => "id",
										"title"         => __( "아이디", 'mshop-point-ex' ),
										'cellClassName' => '',
										"className"     => "center aligned six wide column fluid",
										"type"          => "Text"
									),
									array(
										"id"            => "name",
										"title"         => __( "이름", 'mshop-point-ex' ),
										'cellClassName' => '',
										"className"     => "center aligned six wide column fluid",
										"type"          => "Text"
									),
									array(
										"id"            => "valid_until",
										"title"         => __( "유효기간", 'mshop-point-ex' ),
										'cellClassName' => 'fluid',
										"className"     => "center aligned one wide column fluid",
										"type"          => "Date"
									),
									array(
										'id'             => 'reset',
										'title'          => '초기화',
										'label'          => '초기화',
										'cellClassName'  => '',
										"className"      => "center aligned one wide column fluid",
										'type'           => 'Button',
										'default'        => '',
										'actionType'     => 'ajax',
										'confirmMessage' => __( "[주의] 기간한정 포인트 초기화 후에는 다시 복원이 불가능합니다.\n선택하신 기간한정 포인트를 초기화 하시겠습니까?", 'mshop-point-ex' ),
										'ajaxurl'        => admin_url( 'admin-ajax.php' ),
										'action'         => msps_ajax_command( 'reset_volatile_wallet' )
									),
								)
							)
						)
					)
				)
			);
		}


		static function enqueue_scripts() {
			wp_enqueue_style( 'mshop-setting-manager', MSPS()->plugin_url() . '/includes/admin/setting-manager/css/setting-manager.min.css' );
			wp_enqueue_script( 'mshop-setting-manager', MSPS()->plugin_url() . '/includes/admin/setting-manager/js/setting-manager.min.js', array( 'jquery', 'jquery-ui-core', 'underscore' ) );
		}
		public static function output() {
			include_once MSPS()->plugin_path() . '/includes/admin/setting-manager/mshop-setting-helper.php';
			$settings = self::get_setting_fields();

			self::enqueue_scripts();

			wp_localize_script( 'mshop-setting-manager', 'mshop_setting_manager', array(
				'element'  => 'mshop-setting-wrapper',
				'ajaxurl'  => admin_url( 'admin-ajax.php' ),
				'action'   => msps_ajax_command( 'update_volatile_settings' ),
				'settings' => $settings
			) );

			?>
            <script>
                jQuery( document ).ready( function () {
                    jQuery( this ).trigger( 'mshop-setting-manager', ['mshop-setting-wrapper', '100', <?php echo json_encode( MSSHelper::get_settings( $settings ) ); ?>, null, null] );
                } );
            </script>

            <div id="mshop-setting-wrapper"></div>
			<?php
		}
	}
}
