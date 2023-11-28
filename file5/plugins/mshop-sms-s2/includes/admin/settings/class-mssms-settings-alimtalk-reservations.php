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

if ( ! class_exists( 'MSSMS_Settings_Alimtalk_Reservations' ) ) :

	class MSSMS_Settings_Alimtalk_Reservations {
		public static $number_per_page = 20;
		public static $navigation_size = 5;

		public static function get_setting_fields() {
			return array(
				'type'         => 'ListPage',
				'title'        => __( '예약발송 목록', 'mshop-sms-s2' ),
				'id'           => 'mssms_reservations',
				'searchConfig' => array(
					'action'               => MSSMS()->slug() . '-get_alimtalk_reservation_list',
					'pageSize'             => self::$number_per_page,
					'deleteItem'           => true,
					'btnDeleteLabel'       => __( '예약발송 취소', 'mshop-sms-s2' ),
					'deleteConfirmMessage' => __( '예약발송을 취소하시겠습니까?', 'mshop-sms-s2' ),
					'deleteItemAction'     => MSSMS()->slug() . '-cancel_alimtalk_reservation'
				),
				'elements'     => array(
					array(
						'type'              => 'MShopListTableFilter',
						'id'                => 'mssms_reservations_filter',
						'hideSectionHeader' => true,
						'elements'          => array(
							array(
								"id"        => "status",
								"title"     => __( "메시지 상태", 'mshop-sms-s2' ),
								"className" => "",
								"type"      => "Select",
								"default"   => "all",
								'options'   => array(
									'all'       => '전체',
									'COMPLETED' => '성공',
									'FAILED'    => '실패',
									'CANCEL'    => '취소',
								)
							),
							array(
								"id"          => "plus_id",
								"title"       => "카카오톡 채널 아이디",
								"className"   => "",
								"type"        => "Select",
								"default"     => "",
								"placeholder" => __( "카카오톡 채널 아이디를 선택하세요.", 'mshop-sms-s2' ),
								"options"     => array_combine( MSSMS_Manager::get_plus_ids(), MSSMS_Manager::get_plus_ids() ),
							),
							array(
								"id"        => "receiver",
								"title"     => __( "수신번호", 'mshop-sms-s2' ),
								"className" => "fluid",
								"type"      => "Text",
								"eventType" => "blur",
								"default"   => "",
							)
						)
					),
					array(
						'type' => 'MShopListTableNavigator',
					),
					array(
						'type'     => 'MShopListTable',
						'id'       => 'mssms_reservations_target',
						'default'  => array(),
						"repeater" => true,
						'elements' => array(
							'type'        => 'SortableTable',
							'className'   => 'sortable',
							'noResultMsg' => __( '검색 결과가 없습니다.', 'mshop-sms-s2' ),
							"repeater"    => true,
							"elements"    => array(
								array(
									"id"            => "no",
									"title"         => __( "", 'mshop-sms-s2' ),
									"className"     => "center aligned one wide column",
									"cellClassName" => "center aligned",
									"type"          => "Label",
								),
								array(
									"id"            => "recipientNo",
									"title"         => __( "수신", 'mshop-sms-s2' ),
									"className"     => "center aligned two wide column",
									"cellClassName" => "center aligned",
									"type"          => "Label",
								),
								array(
									"id"            => "templateCode",
									"title"         => __( '템플릿 코드', 'mshop-sms-s2' ),
									"className"     => "center aligned",
									"cellClassName" => "",
									"type"          => "Label"
								),
								array(
									"id"        => "requestDate",
									"title"     => __( "예약일자", 'mshop-sms-s2' ),
									"className" => "center aligned two wide column",
									"type"      => "Label",
								),
								array(
									"id"        => "createDate",
									"title"     => __( "요청일자", 'mshop-sms-s2' ),
									"className" => "center aligned two wide column",
									"type"      => "Label",
								),
								array(
									"id"        => "receiveDate",
									"title"     => __( "수신일자", 'mshop-sms-s2' ),
									"className" => "center aligned two wide column",
									"type"      => "Label",
								),
								array(
									"id"        => "messageStatus",
									"title"     => __( "요청상태", 'mshop-sms-s2' ),
									"className" => "center aligned one wide column",
									"type"      => "Label",
								)
							)
						)
					),
					array(
						'type' => 'MShopListTableNavigator',
					),
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
			include_once MSSMS()->plugin_path() . '/includes/admin/setting-manager/mssms-helper.php';
			$settings = self::get_setting_fields();

			self::enqueue_scripts();

			wp_localize_script( 'mshop-setting-manager', 'mshop_setting_manager', array(
				'element'  => 'mshop-setting-wrapper',
				'ajaxurl'  => admin_url( 'admin-ajax.php' ),
				'action'   => 'mssms_reservations_settings',
				'settings' => $settings,
				'values'   => MSSMS_Helper::get_settings( $settings ),
				'locale'   => get_locale(),
			) );

			?>
            <style>
                .mshop-setting-section .ui.table.sortable td {
                    height: 50px;
                }
            </style>
            <script>
                jQuery(document).ready(function() {
                    jQuery(this).trigger('mshop-setting-manager', ['mshop-setting-wrapper', '100', <?php echo json_encode( MSSMS_Helper::get_settings( $settings ) ); ?>  ]);
                });
            </script>

            <div id="mshop-setting-wrapper"></div>

			<?php
		}
	}

endif;
