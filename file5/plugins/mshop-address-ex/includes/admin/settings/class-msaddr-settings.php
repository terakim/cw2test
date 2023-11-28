<?php

/*
=====================================================================================
                ﻿엠샵 주소 체크아웃 / Copyright 2015 by CodeM(c)
=====================================================================================

  [ 우커머스 버전 지원 안내 ]

   워드프레스 버전 : WordPress 4.3

   우커머스 버전 : WooCommerce 2.4


  [ 코드엠 플러그인 라이센스 규정 ]

   (주)코드엠에서 개발된 워드프레스  플러그인을 사용하시는 분들에게는 다음 사항에 대한 동의가 있는 것으로 간주합니다.

   1. 코드엠에서 개발한 워드프레스 우커머스용 ﻿엠샵 주소 체크아웃 플러그인의 저작권은 (주)코드엠에게 있습니다.
   
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

if ( ! class_exists( 'MSADDR_Settings' ) ) :

	class MSADDR_Settings {
		static $order_statuses = null;

		static function update_settings() {
			include_once MSADDR()->plugin_path() . '/includes/admin/setting-manager/mshop-setting-helper.php';
			$_REQUEST = array_merge( $_REQUEST, json_decode( stripslashes( $_REQUEST['values'] ), true ) );

			MSSHelper::update_settings( self::get_setting_fields() );

			wp_send_json_success();
		}

		static function get_order_statuses() {
			if ( is_null( self::$order_statuses ) ) {
				$invalid_statuses = array(
					'completed',
					'cancelled',
					'cancel-request',
					'refunded',
					'failed'
				);

				$order_statuses = wc_get_order_statuses();


				foreach ( $order_statuses as $status => $desc ) {
					$status = 'wc-' === substr( $status, 0, 3 ) ? substr( $status, 3 ) : $status;

					if ( ! in_array( $status, $invalid_statuses ) ) {
						self::$order_statuses[ $status ] = $desc;
					}
				}

			}

			return self::$order_statuses;
		}

		static function get_setting_fields() {
			return array(
				'type'     => 'Tab',
				'id'       => 'mnp-setting-tab',
				'elements' => array(
					self::get_basic_settings(),
					self::get_address_book_settings(),
				)
			);
		}

		public static function get_basic_settings() {
			return array(
				'type'     => 'Page',
				'title'    => __( '기본 설정', 'mshop-address-ex' ),
				'class'    => 'active',
				'elements' => array(
					array(
						'type'     => 'Section',
						'title'    => '엠샵 대한민국 주소',
						'elements' => array(
							array(
								'id'        => 'mshop_address_enable',
								'title'     => '활성화',
								'className' => '',
								'type'      => 'Toggle',
								'default'   => 'yes',
								'desc'      => __( '<div class="desc2">엠샵 대한민국 주소 & 체크아웃 필드 에디터 기능을 사용합니다.</div>', 'mshop-address-ex' ),
							)
						)
					),
					array(
						'type'     => 'Section',
						'title'    => '기본설정',
						'showIf'   => array( 'mshop_address_enable' => 'yes' ),
						'elements' => array(
							array(
								'id'        => 'mshop_address_custom_css',
								'title'     => __( '사용자정의 CSS', 'mshop-address-ex' ),
								'className' => '',
								'rows'      => 5,
								'type'      => 'TextArea',
								'default'   => ''
							),
							array(
								'id'        => 'mshop_address_search_button_text',
								'title'     => __( '주소 검색 버튼 텍스트', 'mshop-address-ex' ),
								'className' => '',
								'type'      => 'Text',
								'default'   => __( '주소 검색', 'mshop-address-ex' ),
								'desc'      => __( '<div class="desc2">주소 입력 화면에서 주소 검색 버튼의 텍스트를 지정합니다.</div>', 'mshop-address-ex' ),
							),
							array(
								'id'        => 'mshop_address_label_text',
								'title'     => __( '주소 레이블 텍스트', 'mshop-address-ex' ),
								'className' => '',
								'type'      => 'Text',
								'default'   => __( '주소', 'mshop-address-ex' ),
								'desc'      => __( '<div class="desc2">주소 입력 화면에서 주소 레이블(Label)의 텍스트를 지정합니다.</div>', 'mshop-address-ex' ),
							),
							array(
								'id'      => 'mshop_address_user_can_write_address',
								'title'   => __( '사용자 입력 허용', 'mshop-address-ex' ),
								'default' => 'no',
								'type'    => 'Toggle',
								'desc'    => __( '<div class="desc2">사용자가 주소를 직접 입력 할 수 있도록 합니다.</div>', 'mshop-address-ex' ),
							),
							array(
								'id'      => 'mshop_address_use_footer_script',
								'title'   => __( '스크립트 Footer 사용', 'mshop-address-ex' ),
								'default' => 'no',
								'type'    => 'Toggle',
								'desc'    => __( '<div class="desc2">스크립트를 Footer 영역에서 읽도록 설정 할 수 있습니다. 타 플러그인과의 스크립트 충돌로 기능이 정상 동작되지 않는 경우에만 활성화를 해 주세요.</div>', 'mshop-address-ex' ),
							),
							array(
								'id'      => 'msaddr_required_field_address2',
								'title'   => __( '상세주소 필수입력', 'mshop-address-ex' ),
								'default' => 'no',
								'type'    => 'Toggle',
								'desc'    => __( '<div class="desc2">상세주소 필드를 필수입력 필드로 지정합니다.</div>', 'mshop-address-ex' ),
							),
							array(
								'id'      => 'msaddr_tel_numeric',
								'title'   => __( '전화번호 형식 고정', 'mshop-address-ex' ),
								'default' => 'no',
								'type'    => 'Toggle',
								'desc'    => __( '<div class="desc2">전화번호 필드에 숫자만 입력 가능하도록 합니다.</div>', 'mshop-address-ex' ),
							)
						)
					),
					array(
						'type'     => 'Section',
						'title'    => '주소검색 설정',
						'showIf'   => array( 'mshop_address_enable' => 'yes' ),
						'elements' => array(
							array(
								'id'      => 'msaddr_primary_address_type',
								'title'   => __( '주소 형식', 'mshop-address-ex' ),
								"type"    => "Select",
								"default" => 'road',
								'options' => array(
									'road'  => __( '도로명 주소', 'mshop-address-ex' ),
									'jibun' => __( '지번 주소', 'mshop-address-ex' ),
								),
								'desc'    => __( '<div class="desc2">사이트에서 이용할 주소 형식을 지정합니다.</div>', 'mshop-address-ex' ),
							),
							array(
								'id'      => 'msaddr_show_other_address',
								'title'   => __( '다른 형식도 함께 표시', 'mshop-address-ex' ),
								'default' => 'no',
								'type'    => 'Toggle',
								'desc'    => __( '<div class="desc2">주소 검색 결과에 다른 형식의 주소도 함께 표시합니다.</div>', 'mshop-address-ex' ),
							),
						)
					),
					array(
						'type'     => 'Section',
						'title'    => 'Place Holder 문구 설정',
						'showIf'   => array( 'mshop_address_enable' => 'yes' ),
						'elements' => array(
							array(
								'id'        => 'mshop_address_placeholder_firstname',
								'title'     => __( '이름', 'mshop-address-ex' ),
								'default'   => __( '이름', 'mshop-address-ex' ),
								'className' => 'fluid',
								'type'      => 'Text'
							),
							array(
								'id'        => 'mshop_address_placeholder_postnum',
								'title'     => __( '우편번호', 'mshop-address-ex' ),
								'default'   => __( '우편번호', 'mshop-address-ex' ),
								'className' => 'fluid',
								'type'      => 'Text'
							),
							array(
								'id'        => 'mshop_address_placeholder_addr1',
								'title'     => __( '기본주소', 'mshop-address-ex' ),
								'default'   => __( '기본주소', 'mshop-address-ex' ),
								'className' => 'fluid',
								'type'      => 'Text'
							),
							array(
								'id'        => 'mshop_address_placeholder_addr2',
								'title'     => __( '상세주소', 'mshop-address-ex' ),
								'default'   => __( '상세주소', 'mshop-address-ex' ),
								'className' => 'fluid',
								'type'      => 'Text'
							),
							array(
								'id'        => 'mshop_address_placeholder_email',
								'title'     => __( '이메일', 'mshop-address-ex' ),
								'default'   => __( '이메일', 'mshop-address-ex' ),
								'className' => 'fluid',
								'type'      => 'Text'
							),
							array(
								'id'        => 'mshop_address_placeholder_phone',
								'title'     => __( '전화번호', 'mshop-address-ex' ),
								'default'   => __( '전화번호', 'mshop-address-ex' ),
								'className' => 'fluid',
								'type'      => 'Text'
							),
						)
					),
				)
			);
		}

		public static function get_address_book_settings() {
			return array(
				'type'     => 'Page',
				'title'    => __( '배송지 설정', 'mshop-address-ex' ),
				'class'    => '',
				'showIf'   => array( 'mshop_address_enable' => 'yes' ),
				'elements' => array(
					array(
						'type'     => 'Section',
						'title'    => __( '배송지 관리 기능', 'mshop-address-ex' ),
						'showIf'   => array( 'mshop_address_enable' => 'yes' ),
						'elements' => array(
							array(
								'id'        => 'mshop_address_use_shipping_adress_book',
								'title'     => __( '활성화', 'mshop-address-ex' ),
								'className' => '',
								'type'      => 'Toggle',
								'default'   => 'no',
								'desc'      => __( '<div class="desc2">최근 배송지 목록 관리 기능을 사용합니다.</div>', 'mshop-address-ex' ),
							),
							array(
								'id'        => 'msaddr_book_can_edit_address',
								'title'     => __( '배송지 수정', 'mshop-address-ex' ),
								'showIf'    => array( 'mshop_address_use_shipping_adress_book' => 'yes' ),
								'className' => '',
								'type'      => 'Toggle',
								'default'   => 'no',
								'desc'      => __( '<div class="desc2">등록된 배송지를 수정할 수 있습니다.</div>', 'mshop-address-ex' ),
							),
							array(
								'id'        => 'msaddr_book_can_delete_address',
								'showIf'    => array( 'mshop_address_use_shipping_adress_book' => 'yes' ),
								'title'     => __( '배송지 삭제', 'mshop-address-ex' ),
								'className' => '',
								'type'      => 'Toggle',
								'default'   => 'no',
								'desc'      => __( '<div class="desc2">등록된 배송지를 삭제할 수 있습니다.</div>', 'mshop-address-ex' ),
							),
							array(
								'id'      => 'mshop_address_shipping_adress_book_count',
								'showIf'  => array( 'mshop_address_use_shipping_adress_book' => 'yes' ),
								'title'   => __( '최근 배송지 목록 개수', 'mshop-address-ex' ),
								'desc'    => __( '<div class="desc2">최근 배송지 목록을 몇개까지 유지할지를 지정합니다.</div>', 'mshop-address-ex' ),
								'default' => __( '3', 'mshop-address-ex' ),
								'type'    => 'Text'
							),
							array(
								'id'       => 'mshop_address_possible_shipping_adress_edit_status',
								'showIf'   => array( 'mshop_address_use_shipping_adress_book' => 'yes' ),
								'title'    => __( '배송지 변경 가능 주문상태', 'mshop-address-ex' ),
								'desc'     => __( '<div class="desc2">배송지를 변경할 수 있는 주문상태를 지정합니다.</div>', 'mshop-address-ex' ),
								"type"     => "Select",
								"multiple" => true,
								"default"  => 'on-hold,order-received',
								'options'  => self::get_order_statuses(),
							),
						)
					)
				)
			);
		}

		static function get_manual_link() {
			return array(
				'type'     => 'Page',
				'class'    => 'manual_link',
				'title'    => __( '매뉴얼', 'mshop-address-ex' ),
				'elements' => array()
			);
		}
		public static function output() {
			require_once MSADDR()->plugin_path() . '/includes/admin/setting-manager/mshop-setting-helper.php';
			$settings = self::get_setting_fields();

			MSADDR_Admin::enqueue_scripts();

			$license_info = json_decode( get_option( 'msl_license_' . MSADDR()->slug(), json_encode( array(
				'slug'   => MSADDR()->slug(),
				'domain' => preg_replace( '#^https?://#', '', home_url() )
			) ) ), true );

			$license_info = apply_filters( 'mshop_get_license', $license_info, MSADDR()->slug() );
			wp_localize_script( 'mshop-setting-manager', 'mshop_setting_manager', array(
				'element'     => 'mshop-setting-wrapper',
				'ajaxurl'     => admin_url( 'admin-ajax.php' ),
				'action'      => msaddr_ajax_command( 'update_settings' ),
				'settings'    => $settings,
				'slug'        => MSADDR()->slug(),
				'domain'      => preg_replace( '#^https?://#', '', site_url() ),
				'licenseInfo' => json_encode( $license_info )
			) );

			$setting_values = MSSHelper::get_settings( $settings );

			?>
            <style>
                .address-fields .ui.table {
                    font-size: 11px !important;;
                }

                .address-fields .ui.table tr td {
                    border-top: 1px solid rgba(34, 36, 38, 0.1) !important;
                }

                .address-fields .ui.table tr:first-child td {
                    border-top: none !important;
                }
            </style>
            <script>
                jQuery( document ).ready( function ( $ ) {
                    $( this ).trigger( 'mshop-setting-manager', ['mshop-setting-wrapper', '100', <?php echo json_encode( $setting_values ); ?>, <?php echo json_encode( $license_info ); ?>, null] );
                } );
            </script>

            <div id="mshop-setting-wrapper"></div>
			<?php
		}

	}

endif;