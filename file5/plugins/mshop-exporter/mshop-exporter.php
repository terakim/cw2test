<?php
/**
 * Plugin Name:       엠샵 업다운로드
 * Plugin URI:        
 * Description:       빠른 간편상품 관리를 비롯한  운송장번호 일괄 등록 및 주문, 상품, 사용자 정보의 다운로드 기능을 제공 합니다.
 * Version:           2.4.4
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            CodeMShop
 * Author URI:        https://www.codemshop.com/
 * License:           Commercial License
 * Text Domain:       mshop-exporter
 * Domain Path:       /languages
 */

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

if ( ! class_exists( 'MSHOP_Exporter' ) ) {

	class MSHOP_Exporter {

		protected static $_instance = null;
		protected $plugin_file = 'mshop-exporter/mshop-exporter.php';


		protected $license_manager = null;
		protected $slug = 'mshop-exporter';
		public $version = '2.4.4';
		public $plugin_url;
		public $plugin_path;
		public $template_url;
		public function __construct() {
			$this->slug = 'mshop-exporter';

			define( 'MSEX_PLUGIN_FILE', __FILE__ );
			define( 'MSEX_VERSION', $this->version );
            define( 'MSEX_AJAX_PREFIX', 'msex' );

			require_once( 'includes/class-msex-autoloader.php' );
			require_once( 'includes/msex-functions.php' );
			require_once( 'includes/class-msex-post-types.php' );
			require_once( 'includes/class-msex-install.php' );
			require_once( 'includes/msex-update-functions.php' );

			require_once ABSPATH . 'wp-admin/includes/plugin.php';

			if ( is_plugin_active( 'wc-frontend-manager/wc_frontend_manager.php' ) ) {
				require_once( 'includes/vendors/wcfm/class-msex-wcfm.php' );
			}

			if ( is_plugin_active( 'dokan-lite/dokan.php' ) ) {
				require_once( 'includes/vendors/dokan/class-msex-dokan.php' );
			}

			add_action( 'init', array( $this, 'init' ), 0 );

			add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 4 );
			add_filter( "plugin_action_links", array( $this, 'plugin_action_links' ), 10, 4 );

			$this->init_update();
		}

		public function init_update() {
			require 'includes/admin/update/LicenseManager.php';
			$this->license_manager = new MSEX_LicenseManager( $this->slug, __DIR__, __FILE__ );
		}
		public function slug() {
			return $this->slug;
		}
		public function plugin_url() {
			if ( $this->plugin_url ) {
				return $this->plugin_url;
			}

			return $this->plugin_url = str_replace( 'cdn.', '', untrailingslashit( plugins_url( '/', __FILE__ ) ) );
		}
		public function plugin_path() {
			if ( $this->plugin_path ) {
				return $this->plugin_path;
			}

			return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
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

		public function template_path() {
			return $this->plugin_path() . '/templates/';
		}

		public function admin_includes() {
			include_once( 'includes/admin/class-msex-admin.php' );
		}

		public function ajax_includes() {
			include_once( 'includes/class-msex-ajax.php' );
		}

		public function frontend_includes() {
		}

		public function init() {
			$this->includes();
		}
		public function plugin_action_links( $actions, $plugin_file, $plugin_data, $context ) {
			if ( $this->plugin_file == $plugin_file ) {
				$actions['settings'] = '<a href="' . admin_url( '/admin.php?page=msex_sheet_settings' ) . '">설정</a>';
				$actions['manual']   = '<a target="_blank" href="https://manual.codemshop.com/docs/mshop-import-export/">매뉴얼</a>';
			}

			return $actions;
		}
		public function plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {
			if ( $this->plugin_file == $plugin_file ) {
				$plugin_meta[] = '<a target="_blank" href="https://www.codemshop.com/product-category/outside/">함께 사용하면 유용한 플러그인</a>';
                $plugin_meta[] = '<a target="_blank" href="https://manual.codemshop.com/docs/mshop-import-export/faq/">FAQ</a>';
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

	function MSEX() {
		return MSHOP_Exporter::instance();
	}

	return MSEX();

}