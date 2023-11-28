<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'MSADDR_Admin' ) ) :

	class MSADDR_Admin {

		static function init() {
			add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );
            add_action( 'admin_enqueue_scripts', array( 'MSADDR_Admin', 'admin_enqueue_scripts' ) );
        }

		static function admin_menu() {
			add_menu_page( '엠샵 주소체크아웃', '엠샵 주소체크아웃', 'manage_woocommerce', 'msaddr_setting', '', MSADDR()->plugin_url() . '/assets/images/mshop-icon.png', '21.9864920234' );
			add_submenu_page( 'msaddr_setting', '기본설정', '기본설정', 'manage_woocommerce', 'msaddr_setting', array( 'MSADDR_Settings', 'output' ) );
			add_submenu_page( 'msaddr_setting', '체크아웃 필드', '체크아웃 필드', 'manage_woocommerce', 'msaddr_field_setting', array( 'MSADDR_Settings_Checkout_Fields', 'output' ) );
            add_submenu_page( 'msaddr_setting', __( '매뉴얼', 'mshop-address-ex' ), __( '매뉴얼', 'mshop-address-ex' ), 'manage_woocommerce', 'mshop_address_manual','' );
        }

		static function enqueue_scripts() {
			wp_enqueue_style( 'mshop-setting-manager', MSADDR()->plugin_url() . '/includes/admin/setting-manager/css/setting-manager.min.css', array (), MSADDR_VERSION );
			wp_enqueue_script( 'mshop-setting-manager', MSADDR()->plugin_url() . '/includes/admin/setting-manager/js/setting-manager.min.js', array ( 'jquery', 'jquery-ui-core', 'underscore' ), MSADDR_VERSION );
		}

        static function admin_enqueue_scripts() {
            wp_enqueue_script( 'msaddr-admin-menu', MSADDR()->plugin_url() . '/assets/js/admin/admin-menu.js', array( 'jquery' ), MSADDR_VERSION );
            wp_localize_script( 'msaddr-admin-menu', '_msaddr_admin_menu', array(
                'manual_url' => 'https://www.codemshop.com/manual/docs/mshop-address-s2/'
            ) );
        }
	}

	MSADDR_Admin::init();

endif;
