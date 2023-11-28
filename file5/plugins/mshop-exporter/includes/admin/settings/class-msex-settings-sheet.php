<?php

/*
=====================================================================================
                엠샵 업다운로드 / Copyright 2016 by CodeM(c)
=====================================================================================

  [ 우커머스 버전 지원 안내 ]

   워드프레스 버전 : WordPress 4.6 이상

   우커머스 버전 : WooCommerce 2.6 이상


  [ 코드엠 플러그인 라이센스 규정 ]

   (주)코드엠에서 개발된 워드프레스  플러그인을 사용하시는 분들에게는 다음 사항에 대한 동의가 있는 것으로 간주합니다.

   1. 코드엠에서 개발한 워드프레스 우커머스용 엠샵 업다운로드 플러그인의 저작권은 (주)코드엠에게 있습니다.
   
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

if ( ! class_exists( 'MSEX_Settings_Sheet' ) ) :

	class MSEX_Settings_Sheet {


		static function update_settings() {
			require_once MSEX()->plugin_path() . '/includes/admin/setting-manager/mshop-setting-helper.php';
			$_REQUEST = array_merge( $_REQUEST, json_decode( stripslashes( $_REQUEST['values'] ), true ) );

			MSSHelper::update_settings( self::get_setting_fields() );

			wp_send_json_success();
		}

		static function get_default_sheet_fields() {
			return array(
				array(
					'type'                => 'order_id',
					'name'                => '주문번호',
					'order_meta_key'      => '',
					'order_item_meta_key' => ''
				),
				array(
					'type'                => 'dlv_company_code',
					'name'                => '택배사코드',
					'order_meta_key'      => '_msex_dlv_company_code',
					'order_item_meta_key' => '_msex_dlv_company_code'
				),
				array(
					'type'                => 'dlv_company_name',
					'name'                => '택배사명',
					'order_meta_key'      => '_msex_dlv_company_name',
					'order_item_meta_key' => '_msex_dlv_company_name'
				),
				array(
					'type'                => 'sheet_no',
					'name'                => '송장번호',
					'order_meta_key'      => '_msex_sheet_no',
					'order_item_meta_key' => '_msex_sheet_no'
				),
			);
		}

		static function get_default_dlv_company() {
			return array(
				array(
					'dlv_code' => 'CJGLS',
					'dlv_name' => '대한통운',
					'dlv_url'  => 'https://www.cjlogistics.com/ko/tool/parcel/tracking',
				),
				array(
					'dlv_code' => 'EPOST',
					'dlv_name' => '우체국',
					'dlv_url'  => 'https://service.epost.go.kr/trace.RetrieveDomRigiTraceList.comm?sid1={msex_sheet_no}'
				),
				array(
					'dlv_code' => 'HANJIN',
					'dlv_name' => '한진택배',
					'dlv_url'  => 'http://www.hanjinexpress.hanjin.net/customer/hddcw18.tracking?w_num={msex_sheet_no}'
				),
				array(
					'dlv_code' => 'LOGEN',
					'dlv_name' => '로젠택배',
					'dlv_url'  => 'https://www.ilogen.com/web/personal/trace/{msex_sheet_no}'
				),
				array(
					'dlv_code' => 'LOTTE',
					'dlv_name' => '롯데택배',
					'dlv_url'  => 'https://www.lotteglogis.com/home/reservation/tracking/index'
				),
			);
		}

		static function get_setting_fields() {
			return array(
				'type'     => 'Page',
				'class'    => 'active',
				'title'    => __( '기본 설정', 'mshop-exporter' ),
				'elements' => array(
					array(
						'type'     => 'Section',
						'title'    => __( '활성화', 'mshop-exporter' ),
						'elements' => array(
							array(
								"id"        => "msex_sheet_settings_enabled",
								"title"     => __( "송장 업로드 활성화", 'mshop-exporter' ),
								"className" => "",
								"type"      => "Toggle",
								"default"   => "yes",
								"desc"      => __( "송장 업로드 기능을 사용합니다.", 'mshop-exporter' )
							),
						),
					),
					array(
						'type'     => 'Section',
						'showIf'   => array( 'msex_sheet_settings_enabled' => 'yes' ),
						'title'    => __( '기본 설정', 'mshop-exporter' ),
						'elements' => array(
							array(
								'id'        => 'msex_working_directory',
								'title'     => __( '작업 디렉토리', 'mshop-exporter' ),
								'className' => 'fluid',
								'type'      => 'Text',
								'default'   => str_replace( untrailingslashit( ABSPATH ), '', MSEX()->plugin_path() . '/temp/' ),
							),
						)
					),
					array(
						'type'     => 'Section',
						'showIf'   => array( 'msex_sheet_settings_enabled' => 'yes' ),
						'title'    => __( '동작 설정', 'mshop-exporter' ),
						'elements' => array(
							array(
								'id'        => 'msex_order_status_after_shipping',
								'title'     => __( '배송처리 후 변경될 주문상태', 'mshop-exporter' ),
								'className' => '',
								'type'      => 'Select',
								'default'   => 'wc-shipping',
								'options'   => wc_get_order_statuses(),
								'tooltip'   => array(
									'title' => array(
										'content' => __( '모든 주문 아이템에 송장정보가 등록되면 주문 상태를 지정된 상태로 변경합니다.', 'mshop-exporter' ),
									)
								)
							),
							array(
								"id"        => "msex_processing_count",
								"title"     => __( "동시 처리 개수", 'mshop-exporter' ),
								"className" => "",
								"type"      => "Text",
								'default'   => '10',
								"desc2"     => __( "<div class='desc2'>송장등록시 한번에 처리할 주문의 개수를 지정합니다. 이 숫자가 너무 큰 경우 504 Gateway Timeout 오류가 발생할 수 있습니다.</div>", 'mshop-exporter' )
							),
							array(
								"id"        => "msex_convert_encoding",
								"title"     => __( "파일 인코딩 자동변환", 'mshop-exporter' ),
								"className" => "",
								"type"      => "Toggle",
								'default'   => 'no',
								"desc"      => __( "<div class='desc2'>업로드 / 다운로드 파일의 인코딩을 UTF-8 / EUC-KR 로 자동 변환합니다.</div>", 'mshop-exporter' )
							),
						)
					),
					array(
						'type'     => 'Section',
						'showIf'   => array( 'msex_sheet_settings_enabled' => 'yes' ),
						'title'    => __( '택배사설정', 'mshop-exporter' ),
						'elements' => array(
							array(
								"id"        => "msex_dlv_company",
								"className" => "",
								"type"      => "SortableTable",
								"editable"  => true,
								"sortable"  => true,
								"default"   => self::get_default_dlv_company(),
								"template"  => array(
									"dlv_code" => '',
									"dlv_name" => '',
									"dlv_url"  => ''
								),
								"elements"  => array(
									array(
										"id"          => "dlv_code",
										"title"       => __( "택배사코드", 'mshop-exporter' ),
										"className"   => " three wide column fluid",
										"type"        => "Text",
										"placeholder" => ""
									),
									array(
										"id"          => "dlv_name",
										"title"       => __( "택배사명", 'mshop-exporter' ),
										"className"   => " three wide column fluid",
										"type"        => "Text",
										"placeholder" => ""
									),
									array(
										"id"          => "dlv_url",
										"title"       => __( "송장 조회 URL", 'mshop-exporter' ),
										"className"   => " ten wide column fluid",
										"type"        => "Text",
										"placeholder" => ""
									),
								)
							)
						)
					),

				)
			);
		}

		static function enqueue_scripts() {
			wp_enqueue_style( 'mshop-setting-manager', MSEX()->plugin_url() . '/includes/admin/setting-manager/css/setting-manager.min.css' );
			wp_enqueue_script( 'mshop-setting-manager', MSEX()->plugin_url() . '/includes/admin/setting-manager/js/setting-manager.min.js', array( 'jquery', 'jquery-ui-core', 'underscore' ) );
		}
		public static function output() {
			require_once MSEX()->plugin_path() . '/includes/admin/setting-manager/mshop-setting-helper.php';
			$settings = self::get_setting_fields();

			self::enqueue_scripts();

			$license_info = null;

			$license_info = json_decode( get_option( 'msl_license_' . MSEX()->slug(), json_encode( array(
				'slug'   => MSEX()->slug(),
				'domain' => preg_replace( '#^https?://#', '', home_url() )
			) ) ), true );

			$license_info = apply_filters( 'mshop_get_license', $license_info, MSEX()->slug() );
			wp_localize_script( 'mshop-setting-manager', 'mshop_setting_manager', array(
				'element'     => 'mshop-setting-wrapper',
				'ajaxurl'     => admin_url( 'admin-ajax.php' ),
				'action'      => msex_ajax_command( 'update_settings' ),
				'settings'    => $settings,
				'slug'        => MSEX()->slug(),
				'domain'      => preg_replace( '#^https?://#', '', site_url() ),
				'licenseInfo' => json_encode( $license_info )
			) );

			?>
            <script>
                jQuery( document ).ready( function () {
                    jQuery( this ).trigger( 'mshop-setting-manager', ['mshop-setting-wrapper', '100', <?php echo json_encode( MSSHelper::get_settings( $settings ) ); ?>, <?php echo json_encode( $license_info ); ?>, null] );
                } );
            </script>

            <div id="mshop-setting-wrapper"></div>
			<?php
		}
	}
endif;



