<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'MSM_Admin' ) ) :

	class MSM_Admin {

		static function init() {
			add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );
			include_once( 'meta-boxes/class-msm-meta-box-agreement.php' );
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );
		}

		static function admin_menu() {
            add_submenu_page( 'edit.php?post_type=mshop_members_form', __( '멤버스 설정', 'mshop-members-s2' ), __( '멤버스 설정', 'mshop-members-s2' ), 'manage_options', 'mshop_members_setting', array( __CLASS__, 'mshop_members_setting_page' ) );
            add_submenu_page( 'edit.php?post_type=mshop_members_form', __( '인증 및 연동 설정', 'mshop-members-s2' ), __( '인증 및 연동 설정', 'mshop-members-s2' ), 'manage_options', 'mshop_certification_interlock_setting', array( 'MSM_Settings_certification_interlock', 'output' ) );
			add_submenu_page( 'edit.php?post_type=mshop_members_form', __( '프로필 설정', 'mshop-members-s2' ), __( '프로필 설정', 'mshop-members-s2' ), 'manage_options', 'msm_profile_settings', array( 'MSM_Settings_Profile', 'output' ) );
			add_submenu_page( 'edit.php?post_type=mshop_members_form', __( '소셜로그인 설정', 'mshop-members-s2' ), __( '소셜로그인 설정', 'mshop-members-s2' ), 'manage_options', 'mshop_members_setting_social', array( 'MSM_Settings_Members_Social', 'output' ) );
			add_submenu_page( 'edit.php?post_type=mshop_members_form', __( '회원등급 설정', 'mshop-members-s2' ), __( '회원등급 설정', 'mshop-members-s2' ), 'manage_options', 'mshop_members_role_setting', array( 'MSM_Settings_Members_Role', 'output' ) );
			add_submenu_page( 'edit.php?post_type=mshop_members_form', __( '멤버스 필드', 'mshop-members-s2' ), __( '멤버스 필드', 'mshop-members-s2' ), 'manage_options', 'mshop_members_fields_setting', array( 'MSM_Settings_Fields', 'output' ) );
			add_submenu_page( 'edit.php?post_type=mshop_members_form', __( '사용자 필드', 'mshop-members-s2' ), __( '사용자 필드', 'mshop-members-s2' ), 'manage_options', 'mshop_members_user_fields_setting', array( 'MSM_Settings_User_Fields', 'output' ) );

			$awaiting_count = '';
			if ( self::get_awaiting_count() > 0 ) {
				$awaiting_count = sprintf( '<span class="awaiting-mod">%d</span>', self::get_awaiting_count() );
			}
			add_submenu_page( 'edit.php?post_type=mshop_members_form', __( '권한요청 목록', 'mshop-members-s2' ) . $awaiting_count, __( '권한요청 목록', 'mshop-members-s2' ) . $awaiting_count, 'manage_options', 'edit.php?post_type=mshop_role_request' );

			add_submenu_page( 'edit.php?post_type=mshop_members_form', __( '매뉴얼', 'mshop-members-s2' ), __( '매뉴얼', 'mshop-members-s2' ), 'manage_options', 'mshop_members_manual', '' );
		}
		static function mshop_members_setting_page() {
			$setting = new MSM_Settings_Members();
			$setting->output();
		}
		static function admin_enqueue_scripts() {
			wp_enqueue_script( 'msm-admin-menu', MSM()->plugin_url() . '/assets/js/admin/admin-menu.js', array( 'jquery' ), MSM_VERSION );
			wp_localize_script( 'msm-admin-menu', '_msm_admin_menu', array(
				'manual_url' => 'https://manual.codemshop.com/docs/members-s2/'
			) );
		}
		static function get_awaiting_count() {
			global $wpdb;

			$awaiting_count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'mshop_role_request' AND post_status = 'mshop-apply'" );

			return intval( $awaiting_count );
		}
	}

	MSM_Admin::init();

endif;
