<?php
/**
 * Plugin Name:       엠샵 멤버스
 * Plugin URI:        
 * Description:       쇼핑몰에서 필요한 회원가입 및 관리 운영에 필요한 기능을 지원하며, 다양한 템플릿 제작을 위한 폼 디자이너를 제공 합니다.
 * Version:           6.3.1
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            CodeMShop
 * Author URI:        https://www.codemshop.com/
 * License:           Commercial License
 * Text Domain:       mshop-members-s2
 * Domain Path:       /languages
 */
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

if ( ! class_exists( 'MShop_Members' ) ) {

	class MShop_Members {

		protected $slug;

		protected static $_instance = null;
		protected $plugin_file = 'mshop-members-s2/mshop-members-s2.php';
		public $version = '6.3.1';
		public $plugin_url;
		public $plugin_path;


		private $_body_classes = array();
		public function __construct() {
            global $wpdb;

			define( 'MSHOP_MEMBERS_VERSION', $this->version );

			$this->slug = 'mshop-members-s2';

			define( 'MSM_PLUGIN_FILE', __FILE__ );
			define( 'MSM_VERSION', $this->version );
			define( 'MSM_DB_VERSION', '1.0.0' );
            define( 'MSM_AJAX_PREFIX', 'msm' );

            if ( ! defined( 'MSM_GET_USERS_TABLE' ) ) {
                define( 'MSM_GET_USERS_TABLE', $wpdb->prefix . 'users' );
            }

			add_action( 'init', array( $this, 'init' ), 0 );
			add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ), 999 );
			add_action( 'wp_footer', array( $this, 'footer' ), 9999 );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

			require_once( 'includes/class-msm-autoloader.php' );
			require_once( 'includes/class-msm-post-types.php' );
			require_once( 'includes/class-msm-endpoint.php' );
			require_once( 'includes/class-msm-install.php' );

			if ( 'yes' == get_option( 'mshop_members_enabled' ) && 'yes' == get_option( 'mshop_members_access_stibee' ) ) {
				require_once( 'includes/class-msm-stibee.php' );
			}
			if ( 'yes' == get_option( 'mshop_members_enabled' ) && 'yes' == get_option( 'mshop_members_access_mailchimp' ) ) {
				require_once( 'includes/class-msm-mailchimp.php' );
			}
            require_once( 'includes/class-msm-user-agreement-change.php' );
            require_once( 'includes/class-msm-user-agreement-info.php' );
			require_once( 'includes/class-msm-social-login.php' );
			require_once( 'includes/class-msm-phone-certification.php' );
			require_once( 'includes/msm-functions.php' );
			require_once( 'includes/msm-update-functions.php' );
			require_once( 'includes/mfd-functions.php' );

			include_once( 'includes/class-msm-access-control.php' );

			add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 4 );
			add_filter( "plugin_action_links", array( $this, 'plugin_action_links' ), 10, 4 );
			$this->init_update();
		}

		public function init_update() {
			require 'includes/admin/update/LicenseManager.php';

			$this->license_manager = new MSM_LicenseManager( $this->slug, __DIR__, __FILE__ );
		}

		public function slug() {
			return $this->slug;
		}

		public function plugin_url() {
			if ( $this->plugin_url ) {
				return $this->plugin_url;
			}

			return $this->plugin_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
		}


		public function plugin_path() {
			if ( empty( $this->plugin_path ) ) {
				$this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
			}

			return $this->plugin_path;
		}

		public function template_path() {
			return apply_filters( 'msm_template_path', 'mshop-members/' );
		}

		public function init() {
			if ( ! is_user_logged_in() ) {
				$state = msm_get( $_COOKIE, 'wp_msm_state' );

				if ( empty( $state ) ) {
					$state  = strtoupper( bin2hex( openssl_random_pseudo_bytes( 4 ) ) );
					$expire = time() + apply_filters( 'msm_cookie_lifetime', intval( 60 * 5 ) );
					setcookie( 'wp_msm_state', $state, $expire, '/', COOKIE_DOMAIN );
					$_COOKIE['wp_msm_state'] = $state;
				}
			}

			require_once( 'includes/class-msm-cron.php' );
			require_once( 'includes/class-msm-shortcodes.php' );
			require_once( 'includes/class-msm-fields.php' );
			require_once( 'includes/class-msm-profile.php' );
			require_once( 'includes/class-msm-emails.php' );
			require_once( 'includes/class-msm-security.php' );
			require_once( 'includes/class-msm-post-post-email.php' );
			require_once( 'includes/class-msm-post-post-types.php' );
			require_once( 'includes/class-msm-myaccount.php' );

			$this->includes();
		}

		function includes() {

			if ( is_admin() ) {
				$this->admin_includes();
			}

			if ( defined( 'DOING_AJAX' ) ) {
				$this->ajax_includes();
			}

			if ( ! is_admin() || defined( 'DOING_AJAX' ) ) {
				$this->frontend_includes();
			}
		}

		function admin_scripts() {
			$screen    = get_current_screen();
			$screen_id = $screen ? $screen->id : '';

			if ( in_array( $screen_id, array( 'edit-mshop_role_request' ) ) ) {
				wp_register_style( 'msm_admin_styles', MSM()->plugin_url() . '/assets/css/admin.css' );
				wp_enqueue_style( 'msm_admin_styles' );
			}
		}

		public function admin_includes() {
			MSM_Admin_Post_types::init();
			MSM_Meta_Box_Members_Form::init();
			MSM_Meta_Box_Attachment_Info::init();
			MSM_Post_Actions::init();
			include_once( 'includes/admin/class-msm-admin.php' );

			wp_enqueue_style( 'msm-admin', $this->plugin_url() . '/assets/css/admin.css' );
		}

		public function ajax_includes() {
			include_once( 'includes/class-msm-ajax.php' );
		}

		public function frontend_includes() {
		}

		public function frontend_scripts() {
		}

		public function footer() {
		}

		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'mshop-members-s2', false, 'mshop-members-s2/languages' );
		}
		public function plugin_action_links( $actions, $plugin_file, $plugin_data, $context ) {
			if ( $this->plugin_file == $plugin_file ) {
				$actions['settings'] = '<a href="' . admin_url( '/edit.php?post_type=mshop_members_form&page=mshop_members_setting' ) . '">설정</a>';
				$actions['manual']   = '<a target="_blank" href="https://manual.codemshop.com/docs/members-s2/">매뉴얼</a>';
			}

			return $actions;
		}
		public function plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {
			if ( $this->plugin_file == $plugin_file ) {
				$plugin_meta[] = '<a target="_blank" href="https://www.codemshop.com/product-category/outside/">함께 사용하면 유용한 플러그인</a>';
                $plugin_meta[] = '<a target="_blank" href="https://www.codemshop.com/manual/docs/members-s2/faq/">FAQ</a>';
				$plugin_meta[] = '<a target="_blank" href="https://www.codemshop.com/support/">기술지원</a>';
			}

			return $plugin_meta;
		}

		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}

	function MSM() {
		return MShop_Members::instance();
	}


	return MSM();
}