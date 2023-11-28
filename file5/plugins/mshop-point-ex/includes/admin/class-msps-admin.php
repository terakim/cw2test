<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'MSPS_Admin' ) ) :

	class MSPS_Admin {
		private static $saved_meta_boxes = false;

		function __construct() {
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );

			add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ), 10, 2 );

			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );

			$this->includes();
		}

		function includes() {
			include_once dirname( __FILE__ ) . '/settings/class-msps-settings-point.php';
			include_once dirname( __FILE__ ) . '/class-msps-admin-notices.php';
		}

		function admin_menu() {
			add_menu_page( __( '엠샵 포인트', 'mshop-point-ex' ), __( '엠샵 포인트', 'mshop-point-ex' ), 'manage_woocommerce', 'mshop_point_setting', '', MSPS()->plugin_url() . '/assets/images/mshop-icon.png', '20.231123' );
			add_submenu_page( 'mshop_point_setting', __( '기본설정', 'mshop-point-ex' ), __( '기본설정', 'mshop-point-ex' ), 'manage_woocommerce', 'mshop_point_setting', 'MSPS_Settings_Point_Role::output' );
			if ( MSPS_Manager::enabled() ) {
				add_submenu_page( 'mshop_point_setting', __( '정책설정', 'mshop-point-ex' ), __( '정책설정', 'mshop-point-ex' ), 'manage_woocommerce', 'mshop_point_policy_setting', array( 'MSPS_Settings_Point', 'output' ) );
				add_submenu_page( 'mshop_point_setting', __( '포인트 소멸 설정', 'mshop-point-ex' ), __( '포인트 소멸 설정', 'mshop-point-ex' ), 'manage_woocommerce', 'mshop_point_extinction', array( 'MSPS_Settings_Extinction', 'output' ) );
				add_submenu_page( 'mshop_point_setting', __( '기간제한 포인트', 'mshop-point-ex' ), __( '기간제한 포인트', 'mshop-point-ex' ), 'manage_woocommerce', 'msps_volatile_settings', array( 'MSPS_Settings_Volatile', 'output' ) );
				add_submenu_page( 'mshop_point_setting', __( '포인트 로그', 'mshop-point-ex' ), __( '포인트 로그', 'mshop-point-ex' ), 'manage_woocommerce', 'mshop_point_logs', array( $this, 'mshop_point_logs_page' ) );
				add_submenu_page( 'mshop_point_setting', __( '포인트 관리', 'mshop-point-ex' ), __( '포인트 관리', 'mshop-point-ex' ), 'manage_woocommerce', 'mshop_point_user_point', array( $this, 'mshop_point_user_point_page' ) );
				add_submenu_page( 'mshop_point_setting', __( '포인트 일괄 등록', 'mshop-point-ex' ), __( '포인트 일괄 등록', 'mshop-point-ex' ), 'manage_woocommerce', 'msex_upload_point', array( __CLASS__, 'upload_point' ) );
				add_submenu_page( 'mshop_point_setting', __( '매뉴얼', 'mshop-point-ex' ), __( '매뉴얼', 'mshop-point-ex' ), 'manage_woocommerce', 'mshop_point_manual', '' );
			}
		}

		function mshop_point_logs_page() {
			$settings = new MSPS_Settings_Point_Logs();
			$settings->output();
		}

		function mshop_point_user_point_page() {
			$settings = new MSPS_Settings_Manage_Point();
			$settings->output();
		}

		static function upload_point() {
			include( 'views/upload-point.php' );
		}
		public static function add_meta_boxes( $post_type, $post ) {
			$order = MSPS_HPOS::get_order( $post );

			if ( is_a( $order, 'WC_Order' ) ) {
				add_meta_box(
					'msps-point',
					__( '포인트 관리', 'mshop-point-ex' ),
					array( 'MSPS_Admin_Meta_Box_Order', 'output' ),
					MSPS_HPOS::get_shop_order_screen(),
					'side',
					'high'
				);
			}
		}

		static function admin_enqueue_scripts() {
			wp_enqueue_script( 'msps-admin-menu', MSPS()->plugin_url() . '/assets/js/admin/admin-menu.js', array( 'jquery' ), MSPS_VERSION );
			wp_localize_script( 'msps-admin-menu', '_msps_admin_menu', array(
				'manual_url' => 'https://manual.codemshop.com/docs/point/'
			) );
		}
	}

	return new MSPS_Admin();

endif;
