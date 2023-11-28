<?php

/**
 * Plugin Name:       엠샵 추가 배송비
 * Plugin URI:        
 * Description:       도서산간 및 특정 지역, 배송 방법, 구매 금액에 따른 추가 배송비를 부과하는 기능을 제공 합니다.
 * Version:           3.0.2
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            CodeMShop
 * Author URI:        https://www.codemshop.com/
 * License:           Commercial License
 * Text Domain:       mshop-iv-delivery
 * Domain Path:       /languages
 */

/*
=====================================================================================
                엠샵 추가 배송비 / Copyright 2014 by CodeM(c)
=====================================================================================

  [ 우커머스 버전 지원 안내 ]

   워드프레스 버전 : WordPress 4.2.2

   우커머스 버전 : WooCommerce 2.3.x


  [ 코드엠 플러그인 라이센스 규정 ]

   (주)코드엠에서 개발된 워드프레스  플러그인을 사용하시는 분들에게는 다음 사항에 대한 동의가 있는 것으로 간주합니다.

   1. 코드엠에서 개발한 워드프레스 우커머스용 엠샵 추가 배송비 플러그인의 저작권은 (주)코드엠에게 있습니다.
   
   2. 플러그인은 사용권을 구매하는 것이며, 프로그램 저작권에 대한 구매가 아닙니다.

   3. 플러그인을 구입하여 다수의 사이트에 복사하여 사용할 수 없으며, 1개의 라이센스는 1개의 사이트에만 사용할 수 있습니다. 
      이를 위반 시 지적 재산권에 대한 손해 배상 의무를 갖습니다.

   4. 플러그인은 구입 후 1년간 업데이트를 지원합니다.

   5. 플러그인은 워드프레스, 테마, 플러그인과의 호환성에 대한 책임이 없습니다.

   6. 플러그인 설치 후 버전에 관련한 운용 및 관리의 책임은 사이트 당사자에게 있습니다.

   7. 다운로드한 플러그인은 환불되지 않습니다.

=====================================================================================
*/

if ( ! class_exists( 'MSIV_Delivery' ) ) {


	class MSIV_Delivery {

		protected static $_instance = null;

		protected $slug;
		public $version = '3.0.2';
		public $plugin_url;
		public $plugin_path;
		public $template_url;
		protected $update_checker = null;
		public function __construct() {
			$this->slug = 'mshop-iv-delivery-ex';

			$this->define( 'MSIV_PLUGIN_FILE', __FILE__ );
            define( 'MSIV_VERSION', $this->version );
            define( 'MSIV_AJAX_PREFIX', 'msiv' );

			$this->init_update();

			$this->load_plugin_textdomain();

			add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 4 );
			add_filter( "plugin_action_links", array( $this, 'plugin_action_links' ), 10, 4 );

			$this->init_update();
			// Hooks
			add_action( 'init', array( $this, 'init' ), 0 );
			add_action( 'before_woocommerce_init', array( $this, 'declare_woocommerce_compatibility' ) );
		}

		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
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

		function init_update() {
			if ( is_null( $this->update_checker ) ) {
				require 'includes/admin/update/LicenseManager.php';
				$this->update_checker = new MSIV_LicenseManager( $this->slug, __DIR__, __FILE__ );
			}
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

			require_once( 'includes/class-msiv-cart.php' );
		}

		public function admin_includes() {
		}

		public function ajax_includes() {
		}


		public function frontend_includes() {
			include_once( 'includes/class-msiv-cart.php' );
		}

		public function init() {
			if ( ! is_admin() || defined( 'DOING_AJAX' ) ) {

			}

			$this->init_taxonomy();

			$this->includes();

            require_once( 'includes/msiv-functions.php' );
			require_once( 'includes/class-msiv-autoloader.php' );
			require_once( 'includes/class-msiv-ajax.php' );

			add_filter( 'woocommerce_shipping_methods', array( $this, 'woocommerce_shipping_methods' ) );

		}

		public function woocommerce_shipping_methods( $shipping_methods ) {
			$shipping_methods['korea_zone_shipping'] = 'MSIV_Shipping_Korea_Zone';

			return $shipping_methods;
		}
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'mshop-iv-delivery-ex', false, dirname( plugin_basename( __FILE__ ) ) . "/languages/" );
		}

		public function init_taxonomy() {

		}
		public function plugin_action_links( $actions, $plugin_file, $plugin_data, $context ) {
			$plugin_path = str_replace( '-ex.php', '.php', 'mshop-iv-delivery-ex/mshop-iv-delivery-ex.php' );
			if ( $plugin_path == $plugin_file ) {
				$actions['settings'] = '<a href="' . admin_url( '/admin.php?page=wc-settings&tab=shipping&section=korea_zone_shipping' ) . '">설정</a>';
				$actions['manual']   = '<a target="_blank" href="https://manual.codemshop.com/docs/additional-fee/">매뉴얼</a>';
			}

			return $actions;
		}
		public function plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {
			$plugin_path = str_replace( '-ex.php', ".php", "mshop-iv-delivery-ex/mshop-iv-delivery-ex.php" );
			if ( $plugin_path == $plugin_file ) {
				$plugin_meta[] = '<a target="_blank" href="https://www.codemshop.com/product-category/outside/">함께 사용하면 유용한 플러그인</a>';
				$plugin_meta[] = '<a target="_blank" href="https://www.codemshop.com/manual/docs/additional-fee/faq/">FAQ</a>';
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


	function MSIV() {
		return MSIV_Delivery::instance();
	}


	return MSIV();
}
