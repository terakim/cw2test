<?php

/**
 * Plugin Name:       엠샵 주소 체크아웃
 * Plugin URI:        https://www.codemshop.com/shop/zipcode/
 * Description:       대한민국 우편번호, 주소, 이름을 지원하며, 배송지 관리 및 결제 페이지의 필드를 제어하는 에디터 기능을 제공 합니다.
 * Version:           7.3.1
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            CodeMShop
 * Author URI:        https://www.codemshop.com/
 * License:           Commercial License
 * Text Domain:       mshop-address
 * Domain Path:       /languages
 */
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

if ( ! class_exists( 'MShop_Address' ) ) {

	class MShop_Address {

		protected static $_instance = null;

		protected $slug = 'mshop-address-ex';
		public $version = '7.3.1';
		public $plugin_url;
		public $plugin_path;
		public function __construct() {
			define( 'MSADDR_PLUGIN_FILE', __FILE__ );
			define( 'MSADDR_VERSION', $this->version );
			define( 'MSADDR_AJAX_PREFIX', 'msaddr' );

			add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 4 );
			add_filter( "plugin_action_links", array( $this, 'plugin_action_links' ), 10, 4 );

			$this->init_update();
			add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );

			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

			if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
				include_once 'includes/msaddr-functions.php';
				include_once 'includes/msaddr-hpos.php';
				include_once 'includes/class-msaddr-autoloader.php';
				include_once 'includes/class-msaddr-file.php';
				include_once 'includes/class-msaddr-endpoint.php';

				// Hooks
				add_action( 'init', array( $this, 'init' ), 0 );
				add_action( 'before_woocommerce_init', array( $this, 'declare_woocommerce_compatibility' ) );
				add_action( 'wp_footer', array( $this, 'footer' ) );
				add_action( 'admin_footer', array( $this, 'footer' ) );

				// Loaded action
				do_action( 'mshop_address_loaded' );
			}

		}

		function init_update() {
			require 'includes/admin/update/LicenseManager.php';

			$this->license_manager = new MSADDR_LicenseManager( $this->slug, __DIR__, __FILE__ );
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

		public function slug() {
			return $this->slug;
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

		public function ajax_includes() {
			require_once( 'includes/class-msaddr-ajax.php' );
		}

		public function admin_includes() {
			require_once( 'includes/admin/class-msaddr-admin.php' );
			require_once( 'includes/admin/class-msaddr-admin-profile.php' );
			require_once( 'includes/admin/class-msaddr-admin-post-types.php' );
		}

		public function enqueue_script() {
			if ( msaddr_need_scripts() && ( ! function_exists( 'is_pafw_dc_checkout_page' ) || ! is_pafw_dc_checkout_page() ) ) {
				if ( ! is_view_order_page() && ! is_order_received_page() ) {
					wp_enqueue_script( 'ms-address', $this->plugin_url() . '/assets/js/mshop-address.js', array( 'jquery' ), MSADDR_VERSION );
					wp_print_scripts( 'ms-address' );
				}
				wp_enqueue_script( 'ms-address-search', $this->plugin_url() . '/assets/js/mshop-address-search.js', array( 'jquery', 'underscore' ), MSADDR_VERSION );

				$default_country   = '';
				$allowed_countries = WC()->countries->get_allowed_countries();
				if ( 1 == count( $allowed_countries ) ) {
					$default_country = current( array_keys( $allowed_countries ) );
				}

				wp_localize_script( 'ms-address-search', '_msaddr_search', array(
					'search_url'           => base64_decode( 'aHR0cHM6Ly9hZGRyZXNzLXMyLmNvZGVtc2hvcC5jb20vczI=' ),
					'confirm_key'          => base64_decode( 'VTAxVFgwRlZWRWd5TURFNE1EUXhNREUzTURRd01ERXdOemd3T1RrPQ==' ),
					'count_per_page'       => 10,
					'nav_size'             => wp_is_mobile() ? 5 : 10,
					'is_edit_address'      => is_wc_endpoint_url( 'edit-address' ),
					'use_address_book'     => MSADDR_Address_Book::is_enabled(),
					'primary_address_type' => get_option( 'msaddr_primary_address_type', 'road' ),
					'show_other_address'   => get_option( 'msaddr_show_other_address', 'no' ),
					'tel_numeric'          => get_option( 'msaddr_tel_numeric', 'no' ),
					'default_country'      => $default_country,
				) );

				//Flatsome Theme Exception
				if ( wp_script_is( 'flatsome-magnific-popup', 'registered' ) ) {
					wp_dequeue_script( 'flatsome-magnific-popup' );
				}

				wp_enqueue_script( 'jquery-magnific-popup-address', $this->plugin_url() . '/assets/js/jquery.magnific-popup.min.js', array(), MSADDR_VERSION );

				if ( is_admin() ) {
					wp_enqueue_script( 'msaddr-admin', $this->plugin_url() . '/assets/js/admin/write-panel.js', array(), MSADDR_VERSION );
				}

				wp_enqueue_style( 'mshop-address', $this->plugin_url() . '/assets/css/mshop-address.css' );

				echo '<style type="text/css">' . get_option( 'mshop_address_custom_css' ) . '</style>';
			}
		}

		function frontend_scripts() {
			if ( get_option( 'mshop_address_use_footer_script', 'no' ) == 'no' ) {
				$this->enqueue_script();
			} else {
				add_action( 'wp_footer', array( $this, 'enqueue_script' ) );
			}
		}

		function admin_scripts() {
			$this->enqueue_script();
		}

		public function init() {
			$this->includes();

			add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		}

		public function footer() {
			if ( msaddr_need_scripts() ) {
				ob_start();

				load_template( $this->plugin_path() . '/templates/mshop-address-search.php' );

				echo ob_get_clean();
			}
		}
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'mshop-address-ex', false, dirname( plugin_basename( __FILE__ ) ) . "/languages/" );
		}
		public function plugin_action_links( $actions, $plugin_file, $plugin_data, $context ) {
			$plugin_path = str_replace( '-ex.php', '.php', 'mshop-address-ex/mshop-address-ex.php' );
			if ( $plugin_path == $plugin_file ) {
				$actions['settings'] = '<a href="' . admin_url( '/admin.php?page=msaddr_setting' ) . '">설정</a>';
				$actions['manual']   = '<a target="_blank" href="https://manual.codemshop.com/docs/mshop-address-s2/">매뉴얼</a>';
			}

			return $actions;
		}
		public function plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {
			$plugin_path = str_replace( '-ex.php', ".php", "mshop-address-ex/mshop-address-ex.php" );
			if ( $plugin_path == $plugin_file ) {
				$plugin_meta[] = '<a target="_blank" href="https://www.codemshop.com/product-category/outside/">함께 사용하면 유용한 플러그인</a>';
				$plugin_meta[] = '<a target="_blank" href="https://manual.codemshop.com/docs/mshop-address-s2/faq/">FAQ</a>';
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


	function MSADDR() {
		return MShop_Address::instance();
	}


	return MSADDR();

}
