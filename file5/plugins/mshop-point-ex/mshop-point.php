<?php

/**
 * Plugin Name:       엠샵 프리미엄 포인트
 * Plugin URI:        
 * Description:       회원가입, 상품 구매, 댓글, 리뷰 작성 시 포인트 제공 및 기간제한 포인트 사용 정책 및 포인트 소멸에 따른 기능을 제공 합니다.
 * Version:           6.1.8
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            CodeMShop
 * Author URI:        https://www.codemshop.com/
 * License:           Commercial License
 * Text Domain:       mshop-point-ex
 * Domain Path:       /languages
 */
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
if ( ! class_exists( 'MShop_Point' ) ) {

	class MShop_Point {

		protected $slug;

		protected static $_instance = null;
		protected $plugin_file = 'mshop-point-ex/mshop-point-ex.php';
		public $version = '6.1.8';
		public $plugin_url;
		public $plugin_path;
		public $template_url;
		public $point_rule_factory = null;

		protected $update_checker;

		private $_body_classes = array();
		public function __construct() {
			global $wpdb;

			define( 'MSPS_VERSION', $this->version );

			if ( ! defined( 'MSPS_DB_VERSION' ) ) {
				define( 'MSPS_DB_VERSION', '4.2.0' );
			}

			if ( ! defined( 'MSPS_PLUGIN_FILE' ) ) {
				define( 'MSPS_PLUGIN_FILE', __FILE__ );
			}

			if ( ! defined( 'MSPS_AJAX_PREFIX' ) ) {
				define( 'MSPS_AJAX_PREFIX', 'msps' );
			}

			if ( ! defined( 'MSPS_POINT_BALANCE_TABLE' ) ) {
				define( 'MSPS_POINT_BALANCE_TABLE', $wpdb->prefix . 'msps_balance' );
			}

			if ( ! defined( 'MSPS_POINT_LOG_TABLE' ) ) {
				define( 'MSPS_POINT_LOG_TABLE', $wpdb->prefix . 'msps_log' );
			}

			if ( ! defined( 'MSPS_LOGIN_HISTORY_TABLE' ) ) {
				define( 'MSPS_LOGIN_HISTORY_TABLE', $wpdb->prefix . 'msps_login_history' );
			}

			$this->slug = 'mshop-point-ex';

			add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 4 );
			add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 4 );

			$this->init_update();
			add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
			register_activation_hook( __FILE__, array( 'MSPS_Endpoint', 'install' ) );

			add_action( 'init', array( $this, 'init' ), 10 );
			add_action( 'before_woocommerce_init', array( $this, 'declare_woocommerce_compatibility' ) );

			require_once( 'includes/class-msps-autoloader.php' );
			require_once( 'includes/abstracts/abstract-msps-rule.php' );
			require_once( 'includes/abstracts/abstract-msps-wallet-item.php' );
			require_once( 'includes/rules/class-msps-rule-factory.php' );
			require_once( 'includes/msps-hpos.php' );
			require_once( 'includes/msps-functions.php' );
			require_once( 'includes/msps-wpml.php' );
			require_once( 'includes/msps-template-functions.php' );
			require_once( 'includes/msps-template-hooks.php' );

			require_once( 'includes/class-msps-post-types.php' );
			require_once( 'includes/class-msps-endpoint.php' );
			require_once( 'includes/class-msps-shortcodes.php' );
			require_once( 'includes/class-msps-install.php' );
			require_once( 'includes/class-msps-rest-api.php' );
			require_once( 'includes/class-msps-login-history.php' );

			$this->point_rule_factory = new MSPS_Rule_Factory();
		}

		function init_update() {
			require 'includes/admin/update/LicenseManager.php';

			$this->license_manager = new MSPS_LicenseManager( $this->slug, __DIR__, __FILE__ );
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
			if ( $this->plugin_path ) {
				return $this->plugin_path;
			}

			return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
		}
		public function template_path() {
			return $this->plugin_path() . '/templates/';
		}

		function includes() {
			if ( is_admin() ) {
				$this->admin_includes();
			}

			if ( defined( 'DOING_AJAX' ) ) {
				$this->ajax_includes();
			}
		}

		public function admin_includes() {
			include_once( 'includes/admin/admin-users.php' );
			include_once( 'includes/admin/class-msps-admin.php' );
		}

		public function ajax_includes() {
			include_once( 'includes/class-msps-ajax.php' );
		}

		public function init() {
			include_once( 'includes/class-msps-manager.php' );
			require_once( 'includes/class-msps-volatile-wallet.php' );

			$this->init_taxonomy();
			$this->includes();

			MSPS_Myaccount::init();
		}

		public static function woocommerce_payment_gateways( $load_gateways ) {
			include_once( 'includes/gateways/mshop-point/class-mshop-gateway-point.php' );

			$load_gateways[] = 'MShop_Gateway_Point';

			return $load_gateways;
		}

		public function add_body_class( $class ) {
			$this->_body_classes[] = sanitize_html_class( strtolower( $class ) );
		}

		public function output_body_class( $classes ) {
			return $classes;
		}

		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'mshop-point-ex', false, "mshop-point-ex/languages" );
		}

		public function init_taxonomy() {
		}
		public function plugin_action_links( $actions, $plugin_file, $plugin_data, $context ) {
			$plugin_path = str_replace( '-ex.php', '.php', 'mshop-point-ex/mshop-point-ex.php' );
			if ( $plugin_path == $plugin_file ) {
				$actions['settings'] = '<a href="' . admin_url( '/admin.php?page=mshop_point_setting' ) . '">설정</a>';
				$actions['manual']   = '<a target="_blank" href="https://manual.codemshop.com/docs/point/">매뉴얼</a>';
			}

			return $actions;
		}
		public function plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {
			$plugin_path = str_replace( '-ex.php', '.php', 'mshop-point-ex/mshop-point-ex.php' );
			if ( $plugin_path == $plugin_file ) {
                $plugin_meta[] = '<a target="_blank" href="https://www.codemshop.com/product-category/outside/">함께 사용하면 유용한 플러그인</a>';
                $plugin_meta[] = '<a target="_blank" href="https://www.codemshop.com/manual/docs/point/faq/">FAQ</a>';
                $plugin_meta[] = '<a target="_blank" href="https://www.codemshop.com/support/">기술지원</a>';
			}

			return $plugin_meta;
		}
		public function declare_woocommerce_compatibility() {
			if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
			}
		}

		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}


	function MSPS() {
		return MShop_Point::instance();
	}


	return MSPS();
}