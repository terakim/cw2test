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
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MSEX_Admin' ) ) :

	class MSEX_Admin {

		private static $saved_meta_boxes = false;

		public static function init() {
			include_once( 'class-msex-admin-post-types.php' );

			add_action( 'admin_menu', array ( __CLASS__, 'admin_menu' ) );
			add_action( 'add_meta_boxes', array ( __CLASS__, 'add_meta_boxes' ) );
			add_action( 'save_post', array ( __CLASS__, 'save_meta_boxes' ), 10, 2 );
            add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );

            add_filter( 'woocommerce_product_data_tabs', array ( 'MSEX_Meta_Box_Product_Fields', 'woocommerce_product_data_tabs' ) );
            add_action( 'woocommerce_product_data_panels', array ( 'MSEX_Meta_Box_Product_Fields', 'woocommerce_product_data_panels' ) );
            foreach ( wc_get_product_types() as $type => $name ) {
                add_action( 'woocommerce_process_product_meta_' . $type, array ( 'MSEX_Meta_Box_Product_Fields', 'process_product_meta' ) );
            }
		}

		static function admin_menu() {
			add_menu_page( __( '엠샵 업다운로드', 'mshop-exporter' ), __( '엠샵 업다운로드', 'mshop-exporter' ), 'manage_options', 'msex_sheet_settings', '', MSEX()->plugin_url() . '/assets/images/mshop-icon.png', '20.876642' );
			add_submenu_page( 'msex_sheet_settings', __( '업다운로드 설정', 'mshop-exporter' ), __( '업다운로드 설정', 'mshop-exporter' ), 'manage_woocommerce', 'msex_sheet_settings', array ( 'MSEX_Settings_Sheet', 'output' ) );
			add_submenu_page( 'msex_sheet_settings', __( '간편 상품 관리', 'mshop-exporter' ), __( '간편 상품 관리', 'mshop-exporter' ), 'manage_woocommerce', 'mshop_exporter', array ( 'MSEX_Settings_Products', 'output' ) );
			add_submenu_page( 'msex_sheet_settings', __( '상품 필드 관리', 'mshop-exporter' ), __( '상품 필드 관리', 'mshop-exporter' ), 'manage_woocommerce', 'msex_product_fields', array ( 'MSEX_Settings_Product_Fields', 'output' ) );
			if('yes' == get_option('msex_sheet_settings_enabled', 'yes')){
				add_submenu_page( 'msex_sheet_settings', __( '송장 업로드', 'mshop-exporter' ), __( '송장 업로드', 'mshop-exporter' ), 'manage_woocommerce', 'msex_sheet_importer', array ( __CLASS__, 'sheet_importer' ) );
			}
			add_submenu_page( 'msex_sheet_settings', __( '주문 업로드', 'mshop-exporter' ), __( '주문 업로드', 'mshop-exporter' ), 'manage_woocommerce', 'msex_order_importer', array ( __CLASS__, 'order_importer' ) );
			add_submenu_page( 'msex_sheet_settings', __( '주문 다운로드 템플릿', 'mshop-exporter' ), __( '주문 다운로드 템플릿', 'mshop-exporter' ), 'manage_woocommerce', 'edit.php?post_type=msex_order' );
			add_submenu_page( 'msex_sheet_settings', __( '회원 다운로드 템플릿', 'mshop-exporter' ), __( '회원 다운로드 템플릿', 'mshop-exporter' ), 'manage_woocommerce', 'edit.php?post_type=msex_user' );
			add_submenu_page( 'msex_sheet_settings', __( '상품 다운로드 템플릿', 'mshop-exporter' ), __( '상품 다운로드 템플릿', 'mshop-exporter' ), 'manage_woocommerce', 'edit.php?post_type=msex_product' );

			add_submenu_page( 'edit.php?post_type=product', __( '간편 상품 관리', 'mshop-exporter' ), __( '간편 상품 관리', 'mshop-exporter' ), 'manage_woocommerce', 'msex_pm_product', array ( 'MSEX_Settings_Products', 'output' ) );

            add_submenu_page( 'msex_sheet_settings', __( '매뉴얼', 'mshop-exporter' ), __( '매뉴얼', 'mshop-exporter' ), 'manage_options', 'mshop_exporter_manual','' );
		}

		public static function add_meta_boxes() {
			add_meta_box( 'msex-fields', __( '필드 설정', 'mshop-exporter' ), array ( 'MSEX_Meta_Box_Order_Template', 'output_meta_box' ), 'msex_order', 'normal', 'default' );
			add_meta_box( 'msex-fields', __( '필드 설정', 'mshop-exporter' ), array ( 'MSEX_Meta_Box_Product_Template', 'output_meta_box' ), 'msex_product', 'normal', 'default' );
			add_meta_box( 'msex-fields', __( '필드 설정', 'mshop-exporter' ), array ( 'MSEX_Meta_Box_User_Template', 'output_meta_box' ), 'msex_user', 'normal', 'default' );
		}

		public static function save_meta_boxes( $post_id, $post ) {
			$post_id = absint( $post_id );

			// $post_id and $post are required
			if ( empty( $post_id ) || empty( $post ) || self::$saved_meta_boxes ) {
				return;
			}

			// Dont' save meta boxes for revisions or autosaves.
			if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
				return;
			}

			// Check the nonce.
			if ( empty( $_POST['msex_meta_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['msex_meta_nonce'] ), 'msex_save_data' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				return;
			}

			// Check the post being saved == the $post_id to prevent triggering this call for other save_post events.
			if ( empty( $_POST['post_ID'] ) || absint( $_POST['post_ID'] ) !== $post_id ) {
				return;
			}

			// Check user has permission to edit.
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			self::$saved_meta_boxes = true;

			// Check the post type.
			if ( 'msex_order' == $post->post_type ) {
				MSEX_Meta_Box_Order_Template::save_meta_box( $post_id, $post );
			}else if ( 'msex_product' == $post->post_type ) {
				MSEX_Meta_Box_Product_Template::save_meta_box( $post_id, $post );
			}else if ( 'msex_user' == $post->post_type ) {
				MSEX_Meta_Box_User_Template::save_meta_box( $post_id, $post );
			}
		}

		static function order_importer() {
			include( 'views/order-importer.php' );
		}

		static function sheet_importer() {
			include( 'views/sheet-importer.php' );
		}

        static function admin_enqueue_scripts() {
            wp_enqueue_style( 'msex-admin', MSEX()->plugin_url() . '/assets/css/admin.css', array(), MSEX_VERSION );
            wp_enqueue_script( 'msex-admin-menu', MSEX()->plugin_url() . '/assets/js/admin/admin-menu.js', array( 'jquery' ), MSEX_VERSION );
            wp_localize_script( 'msex-admin-menu', '_msex_admin_menu', array(
                'manual_url' => 'https://manual.codemshop.com/docs/mshop-import-export/'
            ) );
        }
	}

	MSEX_Admin::init();

endif;
