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

if ( ! class_exists( 'MSEX_Settings_Products' ) ) :

	class MSEX_Settings_Products {
		static $number_per_page = 20;
		static $navigation_size = 10;

		static function get_setting_fields() {
			return array (
				'type'         => 'ListPage',
				'title'        => __( '기본설정', 'mshop-exporter' ),
				'id'           => 'msex_products',
				'searchConfig' => array (
					'action'   => msex_ajax_command( 'get_product_list' ),
					'pageSize' => self::$number_per_page,
					'navSize'  => self::$navigation_size
				),
				'elements'     => array (
					array (
						'type'           => 'MShopListTableFilter',
						'id'             => 'msex_products_filter',
						'title'          => __( '검색 필터', 'mshop-exporter' ),
						'hideSaveButton' => true,
						'elements'       => array (
							array (
								"id"          => "product",
								"title"       => __( "상품", 'mshop-exporter' ),
								"placeHolder" => __( "상품 선택", 'mshop-exporter' ),
								"className"   => "fluid search",
								'multiple'    => true,
								'search'      => true,
								'action'      => 'action=' . msex_ajax_command( 'search_product' ),
								"type"        => "SearchSelect",
								'options'     => array ()
							),
							array (
								"id"          => "product_type",
								"title"       => __( "상품 타입", 'mshop-exporter' ),
								"placeHolder" => __( "상품 타입을 선택하세요.", 'mshop-exporter' ),
								"className"   => "search",
								'multiple'    => true,
								"type"        => "Select",
								'options'     => msex_get_product_types()
							),
							array (
								"id"          => "manage_stock",
								"title"       => __( "재고 관리", 'mshop-exporter' ),
								"placeholder" => __( "재고 관리 여부를 선택하세요.", 'mshop-exporter' ),
								"className"   => "search",
								"type"        => "Select",
								"options"     => array (
									"yes" => "재고관리함",
									"no"  => "재고관리 안함",
								)
							),
							array (
								"id"          => "stock_status",
								"title"       => __( "재고 상태", 'mshop-exporter' ),
								"placeholder" => __( "재고 상태를 선택하세요.", 'mshop-exporter' ),
								"className"   => "search",
								"type"        => "Select",
								"options"     => array (
									"instock"    => "재고 있음",
									"outofstock" => "품절",
								)
							),
						)
					),
					array (
						'type'           => 'Section',
						'title'          => __( '일괄 설정', 'mshop-exporter' ),
						'hideSaveButton' => true,
						'elements'       => array (
							array (
								"id"          => "bulk_action",
								"title"       => __( "일괄 설정 액션", 'mshop-exporter' ),
								"className"   => "two wide column",
								"type"        => "Select",
								"placeholder" => "일괄 설정 액션을 선택하세요.",
								"options"     => array (
									"stock"                  => "재고설정",
									"set_regular_price"      => "정상가격 설정",
									"increase_regular_price" => "정상가격 인상",
									"decrease_regular_price" => "정상가격 인하",
									"set_sale_price"         => "할인가격 설정",
									"increase_sale_price"    => "할인가격 인상",
									"decrease_sale_price"    => "할인가격 인하",
								),
								"action"      => array (
									'id'             => 'do_bulk_action',
									'title'          => '실행',
									'label'          => '실행',
									'className'      => '',
									'type'           => 'Button',
									'default'        => '',
									'actionType'     => 'ajax',
									'confirmMessage' => __( '일괄 설정을 진행하시겠습니까? ', 'mshop-exporter' ),
									'ajaxurl'        => admin_url( 'admin-ajax.php' ),
									'action'         => msex_ajax_command( 'do_bulk_update_product' ),
									'element'        => array ( 'searchParam', 'bulk_action', 'manage_stock', 'stock_status', 'stock_quantity', 'set_price', 'adjust_amount' )
								)
							),
							array (
								"id"          => "manage_stock",
								"title"       => __( "재고관리 여부", 'mshop-exporter' ),
								"showIf"      => array ( "bulk_action" => "stock" ),
								"className"   => "two wide column",
								"type"        => "Select",
								"placeholder" => "재고관리 여부를 선택하세요.",
								"options"     => array (
									"yes" => "재고관리함",
									"no"  => "재고관리 안함"
								)
							),
							array (
								"id"          => "stock_status",
								"title"       => __( "재고상태", 'mshop-exporter' ),
								"showIf"      => array ( array ( "bulk_action" => "stock" ), array ( "manage_stock" => "yes" ) ),
								"className"   => "two wide column",
								"type"        => "Select",
								"placeholder" => "재고 상태를 선택하세요.",
								"options"     => array (
									"instock"    => "재고 있음",
									"outofstock" => "품절"
								)
							),
							array (
								"id"        => "stock_quantity",
								"title"     => __( "재고수량", 'mshop-exporter' ),
								"showIf"    => array ( array ( "bulk_action" => "stock" ), array ( "manage_stock" => "yes" ), array ( "stock_status" => "instock" ) ),
								"className" => "one wide column",
								"type"      => "Text",
							),
							array (
								"id"        => "set_price",
								"title"     => __( "상품가격", 'mshop-exporter' ),
								"showIf"    => array ( "bulk_action" => "set_regular_price,set_sale_price" ),
								"className" => "wide column",
								"type"      => "LabeledInput",
								"label"     => 'right' == get_option( 'woocommerce_currency_pos', 'left' ) ? get_woocommerce_currency_symbol() : '',
								"leftLabel" => 'left' == get_option( 'woocommerce_currency_pos', 'left' ) ? get_woocommerce_currency_symbol() : ''
							),
							array (
								"id"        => "adjust_amount",
								"title"     => __( "고정금액 또는 비율", 'mshop-exporter' ),
								"showIf"    => array ( "bulk_action" => "increase_regular_price,decrease_regular_price,increase_sale_price,decrease_sale_price" ),
								"className" => "wide column",
								"type"      => "Text"
							)
						)
					),
					array (
						'type'   => 'MShopListTableNavigator',
						'action' => array (
							'id'             => 'bulk_update',
							'label'          => '모두 업데이트',
							'iconClass'      => 'icon settings',
							'className'      => '',
							'type'           => 'Button',
							'default'        => '',
							'actionType'     => 'ajax',
							'confirmMessage' => __( '모든 상품 정보를 업데이트 하시겠습니까? ', 'mshop-exporter' ),
							'ajaxurl'        => admin_url( 'admin-ajax.php' ),
							'action'         => msex_ajax_command( 'bulk_update_products' ),
							'element'        => 'msex_products_target'
						)
					),
					array (
						'type'     => 'MShopListTable',
						'id'       => 'msex_products_target',
						"repeater" => true,
						'default'  => array (),
						'elements' => array (
							'type'        => 'SortableTable',
							'className'   => 'sortable',
							'noResultMsg' => __( '검색 결과가 없습니다.', 'mshop-exporter' ),
							"repeater"    => true,
							"elements"    => array (
								array (
									"id"            => "product_id",
									"title"         => __( "ID", 'mshop-exporter' ),
									"className"     => "center aligned one wide column",
									"cellClassName" => "center aligned",
									"type"          => "Label",
									"sortKey"       => "ID"
								),
								array (
									"id"            => "sku",
									"title"         => __( "SKU", 'mshop-exporter' ),
									"className"     => "center aligned one wide column",
									"cellClassName" => "center aligned",
									"type"          => "Label",
									"sortKey"       => "ID"
								),
								array (
									"id"            => "type",
									"title"         => __( "상품타입", 'mshop-exporter' ),
									"className"     => "center aligned one wide column",
									"cellClassName" => "center aligned",
									"type"          => "Label",
									"sortKey"       => "display_name"
								),
								array (
									"id"        => "title",
									"title"     => __( "상품명", 'mshop-exporter' ),
									"className" => "center aligned five wide column",
									"type"      => "Label",
									"sortKey"   => "display_name"
								),
								array (
									"id"        => "regular_price",
									"title"     => __( "정상가격", 'mshop-exporter' ),
									"className" => "center aligned two wide column fluid",
									"type"      => "Text",
									"sortKey"   => "display_name"
								),
								array (
									"id"        => "sale_price",
									"title"     => __( "할인가격", 'mshop-exporter' ),
									"className" => "center aligned two wide column fluid",
									"type"      => "Text",
									"sortKey"   => "display_name"
								),
								array (
									"id"        => "manage_stock",
									"title"     => __( "재고관리", 'mshop-exporter' ),
									"className" => "center aligned one wide column fluid",
									"type"      => "Toggle",
									"sortKey"   => "manage_stock"
								),
								array (
									"id"        => "stock_status",
									"title"     => __( "재고상태", 'mshop-exporter' ),
									"showIf"    => array ( "manage_stock" => "yes" ),
									"className" => "center aligned two wide column fluid",
									"type"      => "Select",
									"options"   => array (
										"instock"     => "재고 있음",
										"outofstock"  => "품절",
										"onbackorder" => "이월 주문",
									)
								),
								array (
									"id"        => "stock_quantity",
									"title"     => __( "재고수량", 'mshop-exporter' ),
									"showIf"    => array ( array ( "manage_stock" => "yes" ), array ( "stock_status" => "instock,onbackorder" ) ),
									"className" => "center aligned one wide column fluid",
									"type"      => "Text",
									"sortKey"   => "manage_stock"
								),
								array (
									'id'         => 'update_product',
									'title'      => '',
									'label'      => '업데이트',
									'className'  => 'one wide column',
									'type'       => 'Button',
									'default'    => '',
									'actionType' => 'ajax',
									'ajaxurl'    => admin_url( 'admin-ajax.php' ),
									'action'     => msex_ajax_command( 'update_product' ),
								)
							)
						)
					),
					array (
						'type' => 'MShopListTableNavigator'
					),
				)
			);
		}

		static function enqueue_scripts() {
			wp_enqueue_style( 'mshop-setting-manager', MSEX()->plugin_url() . '/includes/admin/setting-manager/css/setting-manager.min.css' );
			wp_enqueue_script( 'mshop-setting-manager', MSEX()->plugin_url() . '/includes/admin/setting-manager/js/setting-manager.min.js', array ( 'jquery', 'jquery-ui-core', 'underscore' ) );
		}
		public static function output() {
			include_once MSEX()->plugin_path() . '/includes/admin/setting-manager/mshop-setting-helper.php';
			$settings = self::get_setting_fields();

			self::enqueue_scripts();

			$license_info = null;

			$license_info = json_decode( get_option( 'msl_license_' . MSEX()->slug(), json_encode( array (
				'slug'   => MSEX()->slug(),
				'domain' => preg_replace( '#^https?://#', '', home_url() )
			) ) ), true );

			$license_info = apply_filters( 'mshop_get_license', $license_info, MSEX()->slug() );
			wp_localize_script( 'mshop-setting-manager', 'mshop_setting_manager', array (
				'element'     => 'mshop-setting-wrapper',
				'ajaxurl'     => admin_url( 'admin-ajax.php' ),
				'settings'    => $settings,
				'slug'        => MSEX()->slug(),
				'domain'      => preg_replace( '#^https?://#', '', site_url() ),
				'licenseInfo' => json_encode( $license_info )
			) );


			?>
            <style>
                .mshop-setting-section .ui.table.sortable td {
                    height: 40px;
                }
            </style>
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

