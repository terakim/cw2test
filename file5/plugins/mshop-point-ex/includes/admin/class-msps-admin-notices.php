<?php
defined( 'ABSPATH' ) || exit;
class MSPS_Admin_Notices {
	private static $notices = array ();
	private static $core_notices = array (
		'update' => 'update_notice',
	);
	public static function init() {
		self::$notices = get_option( 'msps_admin_notices', array () );

		add_action( 'wp_loaded', array ( __CLASS__, 'hide_notices' ) );
		add_action( 'shutdown', array ( __CLASS__, 'store_notices' ) );

		if ( current_user_can( 'manage_woocommerce' ) ) {
			add_action( 'admin_print_styles', array ( __CLASS__, 'add_notices' ) );
			add_action( 'admin_notices', array ( __CLASS__, 'admin_notices' ) );
		}
	}
	public static function store_notices() {
		update_option( 'msps_admin_notices', self::get_notices() );
	}
	public static function get_notices() {
		return self::$notices;
	}
	public static function remove_all_notices() {
		self::$notices = array ();
	}
	public static function add_notice( $name ) {
		self::$notices = array_unique( array_merge( self::get_notices(), array ( $name ) ) );
	}
	public static function remove_notice( $name ) {
		self::$notices = array_diff( self::get_notices(), array ( $name ) );
		delete_option( 'msps_admin_notice_' . $name );
	}
	public static function has_notice( $name ) {
		return in_array( $name, self::get_notices(), true );
	}
	public static function hide_notices() {
		if ( isset( $_GET['msps-hide-notice'] ) && isset( $_GET['_msps_notice_nonce'] ) ) { // WPCS: input var ok, CSRF ok.
			if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_msps_notice_nonce'] ) ), 'msps_hide_notices_nonce' ) ) { // WPCS: input var ok, CSRF ok.
				wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'mshop-point-ex' ) );
			}

			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				wp_die( esc_html__( 'You don&#8217;t have permission to do this.', 'mshop-point-ex' ) );
			}

			$hide_notice = sanitize_text_field( wp_unslash( $_GET['msps-hide-notice'] ) ); // WPCS: input var ok, CSRF ok.

			self::remove_notice( $hide_notice );

			update_user_meta( get_current_user_id(), 'dismissed_' . $hide_notice . '_notice', true );

			do_action( 'msps_hide_' . $hide_notice . '_notice' );
		}
	}
	public static function add_notices() {
		$notices = self::get_notices();

		if ( empty( $notices ) ) {
			return;
		}

		$screen          = get_current_screen();
		$screen_id       = $screen ? $screen->id : '';
		$show_on_screens = array (
			'dashboard',
			'plugins',
		);

		wp_enqueue_style( 'msps-admin', plugins_url( '/assets/css/admin.css', MSPS_PLUGIN_FILE ), array (), MSPS_VERSION );

		foreach ( $notices as $notice ) {
			if ( ! empty( self::$core_notices[ $notice ] ) && apply_filters( 'msps_show_admin_notice', true, $notice ) ) {
				add_action( 'admin_notices', array ( __CLASS__, self::$core_notices[ $notice ] ) );
			} else {
				add_action( 'admin_notices', array ( __CLASS__, 'output_custom_notices' ) );
			}
		}
	}
	public static function add_custom_notice( $name, $notice_html ) {
		self::add_notice( $name );
		update_option( 'msps_admin_notice_' . $name, wp_kses_post( $notice_html ) );
	}
	public static function output_custom_notices() {
		$notices = self::get_notices();

		if ( ! empty( $notices ) ) {
			foreach ( $notices as $notice ) {
				if ( empty( self::$core_notices[ $notice ] ) ) {
					$notice_html = get_option( 'msps_admin_notice_' . $notice );

					if ( $notice_html ) {
						include dirname( __FILE__ ) . '/views/html-notice-custom.php';
					}
				}
			}
		}
	}
	public static function update_notice() {
		if ( MSPS_Install::needs_db_update() ) {
			$timestamp = as_next_scheduled_action( 'msps_run_update_callback', null, 'msps-db-updates' );

			if ( $timestamp || ! empty( $_GET['do_update_mshop_point'] ) ) { // WPCS: input var ok, CSRF ok.
				include dirname( __FILE__ ) . '/views/html-notice-updating.php';
			} else {
				include dirname( __FILE__ ) . '/views/html-notice-update.php';
			}
		} else {
			MSPS_Install::update_db_version();
			include dirname( __FILE__ ) . '/views/html-notice-updated.php';
		}
	}

	public static function admin_notices() {
		global $post, $pagenow;

		$screen = get_current_screen();

		if ( 'shop_order' == $screen->id ) {
			$order = wc_get_order( $post );

			if ( $order ) {
				$status = MSPS_Order::is_earn_processed( $order );
				$point  = MSPS_Order::get_earn_point( $order );

				if ( ! $status && $point > 0 && ! in_array( $order->get_status(), array ( 'pending', 'cancelled', 'refunded' ) ) ) {
					?>
                    <div class="notice notice-info">
                        <p><?php echo sprintf( __( '주문이 완료되면 %s 포인트가 적립됩니다.', 'mshop-point-ex' ), number_format( $point, 2 ) ); ?></p>
                    </div>
					<?php
				}
			}
		}

	}
}

MSPS_Admin_Notices::init();
