<?php

/*
Plugin Name: 워드프레스 결제 심플페이 - 우커머스 결제 플러그인
Plugin URI: 
Description: 코드엠샵에서 개발, 운영되는 우커머스 전용 결제 통합 시스템 입니다.
Version: 3.3.5
Author: CodeMShop
Author URI: www.codemshop.com
License: GPLv2 or later
*/


//소스에 URL로 직접 접근 방지
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once ABSPATH . 'wp-admin/includes/plugin.php';

if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	return;
}

if ( ! class_exists( 'PGALL_For_WooCommerce' ) ) {
	class PGALL_For_WooCommerce {

		private static $_instance = null;
		protected $slug;
		protected $version = '3.3.5';
		protected $plugin_url;
		protected $plugin_path;
		public function __construct() {
			$this->slug = 'pgall-for-woocommerce';

			if ( ! defined( 'PAFW_PLUGIN_FILE' ) ) {
				define( 'PAFW_PLUGIN_FILE', __FILE__ );
			}

			if ( ! defined( 'PAFW_VERSION' ) ) {
				define( 'PAFW_VERSION', $this->version );
			}

			require_once( 'includes/pafw-hpos.php' );
			require_once( 'includes/class-pafw-autoloader.php' );
			require_once( 'includes/class-pafw-endpoint.php' );
			require_once( 'includes/class-pafw-install.php' );
			require_once( 'includes/admin/settings/abstract-pafw-settings.php' );
			require_once( 'includes/class-pafw-post-types.php' );
			require_once( 'includes/pafw-functions.php' );
			require_once( 'includes/pafw-template-functions.php' );
			require_once( 'includes/pafw-template-hooks.php' );
			require_once( 'includes/class-pafw-tax.php' );
			require_once( 'includes/class-pafw-emails.php' );
			require_once( 'includes/class-pafw-renewal-failed-notification.php' );

			add_action( 'woocommerce_init', array( $this, 'woocommerce_init' ) );
			add_action( 'before_woocommerce_init', array( $this, 'declare_woocommerce_compatibility' ) );
			add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );

			register_activation_hook( __FILE__, array( 'PAFW_Endpoint', 'install' ) );

			add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 4 );
			add_filter( "plugin_action_links", array( $this, 'plugin_action_links' ), 10, 4 );
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

		public function woocommerce_init() {
			add_action( 'wp_head', array( $this, 'echo_ajaxurl' ) );

			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
			add_filter( 'wc_order_statuses', array( $this, 'add_order_statuses' ), 10, 1 );
			add_filter( 'woocommerce_payment_gateways', array( $this, 'woocommerce_payment_gateways' ), 1 );
			add_filter( 'the_title', array( $this, 'order_received_title' ), 10, 2 );

			$this->includes();
		}
		public function declare_woocommerce_compatibility() {
			if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
			}
		}

		function get_supported_gateways() {
			$supported_gateways = array();

			$gateways = apply_filters( 'pafw_supported_gateway_ids', array(
				'inicis',
				'kakaopay',
				'kcp',
				'payco',
				'nicepay',
				'tosspayments',
				'lguplus',
				'npay',
				'settlebank',
				'settlevbank',
				'settlepg',
			) );

			foreach ( $gateways as $gateway ) {
				if ( 'yes' == get_option( 'pafw-gw-' . $gateway, 'no' ) ) {
					$supported_gateways[] = $gateway;
				}
			}

			return apply_filters( 'pafw_get_supported_gateways', $supported_gateways );
		}
		function get_enabled_payment_gateways() {
			$gateways = array();

			foreach ( $this->get_supported_gateways() as $gateway_id ) {
				if ( 'yes' == get_option( 'pafw-gw-' . $gateway_id, 'no' ) ) {
					$gateways[] = $gateway_id;
				}
			}

			return $gateways;
		}

		function is_wc_setting_page() {
			$is_setting_page = false;

			if ( ! is_ajax() && is_admin() && function_exists( 'get_current_screen' ) ) {
				$wc_screen_id      = sanitize_title( __( 'WooCommerce', 'woocommerce' ) );
				$setting_screen_id = $wc_screen_id . '_page_wc-settings';
				$current_screen    = get_current_screen();

				$is_setting_page = ( $current_screen && $setting_screen_id == $current_screen->id || ( isset( $_GET['page'] ) && 'mshop_payment' == $_GET['page'] ) );
			}

			return $is_setting_page;
		}
		function woocommerce_payment_gateways( $methods ) {
			if ( apply_filters( 'pafw_load_payment_gateways', true ) ) {
				if ( PAFW_Cash_Receipt::is_enabled() ) {
					require_once PAFW()->plugin_path() . '/includes/gateways/bacs/class-wc-gateway-pafw-bacs.php';

					$methods   = array_diff( $methods, array( 'WC_Gateway_BACS' ) );
					$methods[] = 'WC_Gateway_PAFW_BACS';
				}

				foreach ( $this->get_supported_gateways() as $gateway_id ) {
					$class_name = 'WC_Gateway_PAFW_' . ucwords( str_replace( '-', '_', $gateway_id ) );

					if ( 'yes' == get_option( 'pafw-gw-' . $gateway_id, 'no' ) ) {

						foreach ( array_keys( $class_name::get_supported_payment_methods() ) as $type ) {
							$methods[] = 'WC_Gateway_' . ucwords( $type, '_' );
						}
					}

					if ( is_admin() ) {
						$methods[] = $class_name;
					}
				}
			}

			return $methods;
		}
		function includes() {
			require_once( 'includes/class-pafw-cancel-unpaid-order.php' );
			require_once( 'includes/class-pafw-simple-pay.php' );

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

		public function admin_includes() {
			include_once( 'includes/admin/class-pafw-admin.php' );
			include_once( 'includes/admin/class-pafw-admin-notice.php' );
			include_once( 'includes/admin/class-pafw-admin-users.php' );
			include_once( 'includes/admin/class-pafw-admin-dashboard.php' );

			wp_enqueue_style( 'pafw-admin', PAFW()->plugin_url() . '/assets/css/admin.css', array(), PAFW_VERSION );

			PAFW_Admin_Notice::init( $this->slug(), PAFW_VERSION );
		}

		public function ajax_includes() {
			include_once( 'includes/class-pafw-ajax.php' );
		}

		public function frontend_includes() {
			require_once( 'includes/class-pafw-shortcodes.php' );
		}
		public function wp_enqueue_scripts( $force = false ) {
			if ( $force || ( is_checkout() && ! is_order_received_page() && ( ! function_exists( 'is_pafw_dc_checkout_page' ) || ! is_pafw_dc_checkout_page() ) ) ) {
				$dependencies = array( 'jquery', 'underscore', 'pafw-card' );

				$supported_payment_methods = array();
				$gateway_payment_methods   = array();

				foreach ( $this->get_supported_gateways() as $gateway_id ) {
					$pafw_class_name = 'WC_Gateway_PAFW_' . ucfirst( $gateway_id );
					$class_name      = 'WC_Gateway_' . ucfirst( $gateway_id );

					if ( 'yes' == get_option( 'pafw-gw-' . $gateway_id, 'no' ) ) {
						$dependencies[] = $class_name::enqueue_frontend_script();

						$supported_payment_methods = array_merge( $supported_payment_methods, array_keys( $pafw_class_name::get_supported_payment_methods() ) );

						$gateway_payment_methods[ $gateway_id ] = array_keys( $pafw_class_name::get_supported_payment_methods() );
					}
				}

				$dependencies = array_filter( $dependencies );

				wp_enqueue_style( 'pafw', PAFW()->plugin_url() . '/assets/css/payment.css', array(), PAFW_VERSION );
				wp_enqueue_script( 'pafw-card', PAFW()->plugin_url() . '/assets/js/card-input.js', array( 'jquery' ), PAFW_VERSION, 'yes' == get_option( 'pafw-script-footer', 'no' ) );
				wp_enqueue_script( 'pafw', PAFW()->plugin_url() . '/assets/js/wc-payment.js', $dependencies, PAFW_VERSION, 'yes' == get_option( 'pafw-script-footer', 'no' ) );

				wp_localize_script( 'pafw', '_pafw', apply_filters( 'pafw_payment_script_params', array(
					'ajax_url'                  => pafw_get_ajax_url(),
					'gateway_domain'            => PAFW_Payment_Gateway::gateway_domain(),
					'wc_checkout_url'           => WC_AJAX::get_endpoint( 'checkout' ),
					'checkout_form_selector'    => is_checkout_pay_page() ? get_option( 'pafw-order-pay-form-selector', 'form#order_review' ) : get_option( 'pafw-checkout-form-selector', 'form.checkout' ),
					'supported_payment_methods' => $supported_payment_methods,
					'slug'                      => $this->slug(),
					'gateway'                   => apply_filters( 'pafw_supported_payment_methods', $gateway_payment_methods ),
					'is_mobile'                 => wp_is_mobile(),
					'is_checkout_pay_page'      => is_checkout_pay_page(),
					'order_id'                  => isset( $_REQUEST['key'] ) ? wc_get_order_id_by_order_key( wc_clean( $_REQUEST['key'] ) ) : '',
					'order_key'                 => isset( $_REQUEST['key'] ) ? wc_clean( $_REQUEST['key'] ) : '',
					'_wpnonce'                  => wp_create_nonce( 'pgall-for-woocommerce' ),
					'simple_pay'                => $force ? 'yes' : 'no',
					'i18n'                      => array(
						'popup_block_message' => __( '팝업이 차단되어 있습니다. 팝업설정을 변경하신 후 다시 시도해주세요.', 'pgall-for-woocommerce' )
					)
				) ) );
			}
		}
		public function order_received_title( $title, $id = null ) {
			if ( is_order_received_page() && get_the_ID() === $id ) {
				global $wp;

				$order_id  = apply_filters( 'woocommerce_thankyou_order_id', absint( $wp->query_vars['order-received'] ) );
				$order_key = apply_filters( 'woocommerce_thankyou_order_key', empty( $_GET['key'] ) ? '' : wc_clean( $_GET['key'] ) );

				if ( ! empty( $order_id ) ) {
					$order = new WC_Order( $order_id );
					if ( $order->get_status() == 'failed' ) {
						$title = __( '결제 실패로, 결제를 다시한번 진행 해 주시기 바랍니다.', 'pgall-for-woocommerce' );
					} else {
						$title = __( '정상적인 결제완료로 주문이 접수되었습니다.', 'pgall-for-woocommerce' );
					}
				} else {
					$title = __( '정상적인 결제완료로 주문이 접수되었습니다.', 'pgall-for-woocommerce' );
				}
			}

			return $title;
		}
		function add_order_statuses( $order_statuses ) {
			$order_statuses = array_merge( $order_statuses, array(
				'wc-shipping'       => _x( '배송중', 'Order status', 'pgall-for-woocommerce' ),
				'wc-shipped'        => _x( '배송완료', 'Order status', 'pgall-for-woocommerce' ),
				'wc-cancel-request' => _x( '주문취소요청', 'Order status', 'pgall-for-woocommerce' ),
			) );

			if ( 'yes' == get_option( 'pafw-gw-support-exchange' ) ) {
				$order_statuses = array_merge( $order_statuses, array(
					'wc-exchange-request' => _x( '교환신청', 'Order status', 'pgall-for-woocommerce' ),
					'wc-accept-exchange'  => _x( '교환접수', 'Order status', 'pgall-for-woocommerce' ),
				) );
			}

			if ( 'yes' == get_option( 'pafw-gw-support-return' ) ) {
				$order_statuses = array_merge( $order_statuses, array(
					'wc-return-request' => _x( '반품신청', 'Order status', 'pgall-for-woocommerce' ),
					'wc-accept-return'  => _x( '반품접수', 'Order status', 'pgall-for-woocommerce' ),
				) );
			}

			return $order_statuses;
		}

		function echo_ajaxurl() {
			?>
            <script type="text/javascript">
				<?php

				if ( function_exists( 'icl_object_id' ) ) {
					$ajax_url = admin_url( 'admin-ajax.php?lang=' . ICL_LANGUAGE_CODE, pafw_check_ssl() ? 'https' : 'http' );
				} else {
					$ajax_url = admin_url( 'admin-ajax.php', pafw_check_ssl() ? 'https' : 'http' );
				}
				?>

                var pafw_ajaxurl = '<?php echo $ajax_url; ?>';
            </script>
			<?php
		}


		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'pgall-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . "/languages/" );
		}

		public function slug() {
			return $this->slug;
		}

		function admin_notices() {
			if ( 0 == count( $this->get_enabled_payment_gateways() ) ) {
				?>
                <div class="notice notice-success is-dismissible">
                    <p><?php _e( '심플페이 플러그인을 이용해주셔서 감사합니다. <a href="' . admin_url( 'admin.php?page=pafw_setting' ) . '">설정 페이지</a>에서 결제대행사를 활성화 한 후 이용해주세요..', 'pgall-for-woocommerce' ); ?></p>
                </div>
				<?php
			}
		}
		public function plugin_action_links( $actions, $plugin_file, $plugin_data, $context ) {
			if ( $this->slug == pafw_get( $plugin_data, 'slug' ) ) {
				$actions['settings'] = '<a href="' . admin_url( '/admin.php?page=pafw_setting' ) . '">설정</a>';
				$actions['manual']   = '<a target="_blank" href="https://manual.codemshop.com/pgall-for-woocommerce/">매뉴얼</a>';
			}

			return $actions;
		}
		public function plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {
			if ( $this->slug == pafw_get( $plugin_data, 'slug' ) ) {

				$plugin_meta[] = '<a target="_blank" href="https://wordpress.org/plugins/pgall-for-woocommerce/#reviews">별점응원하기</a>';
				$plugin_meta[] = '<a target="_blank" href="https://wordpress.org/plugins/search/codemshop/">함께 사용하면 좋은 플러그인</a>';
				$plugin_meta[] = '<a target="_blank" href="https://www.codemshop.com/product-category/outside/">쇼핑몰 플러그인</a>';
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
	function PAFW() {
		return PGALL_For_WooCommerce::instance();
	}

	return PAFW();

}
