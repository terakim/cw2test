<?php
defined( 'ABSPATH' ) || exit;
class MSEX_Admin_Notices {
	private static $notices = array ();
	private static $core_notices = array (
		'update' => 'update_notice',
	);
	public static function init() {
		self::$notices = get_option( 'msex_admin_notices', array () );

		add_action( 'wp_loaded', array ( __CLASS__, 'hide_notices' ) );
		add_action( 'shutdown', array ( __CLASS__, 'store_notices' ) );

		if ( current_user_can( 'manage_woocommerce' ) ) {
			add_action( 'admin_print_styles', array ( __CLASS__, 'add_notices' ) );
			add_action( 'activate_gutenberg/gutenberg.php', array ( __CLASS__, 'add_wootenberg_feature_plugin_notice_on_gutenberg_activate' ) );
		}
	}
	public static function store_notices() {
		update_option( 'msex_admin_notices', self::get_notices() );
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
		delete_option( 'msex_admin_notice_' . $name );
	}
	public static function has_notice( $name ) {
		return in_array( $name, self::get_notices(), true );
	}
	public static function hide_notices() {
		if ( isset( $_GET['msex-hide-notice'] ) && isset( $_GET['_msex_notice_nonce'] ) ) { // WPCS: input var ok, CSRF ok.
			if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_msex_notice_nonce'] ) ), 'msex_hide_notices_nonce' ) ) { // WPCS: input var ok, CSRF ok.
				wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'mshop-exporter' ) );
			}

			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				wp_die( esc_html__( 'You don&#8217;t have permission to do this.', 'mshop-exporter' ) );
			}

			$hide_notice = sanitize_text_field( wp_unslash( $_GET['msex-hide-notice'] ) ); // WPCS: input var ok, CSRF ok.

			self::remove_notice( $hide_notice );

			update_user_meta( get_current_user_id(), 'dismissed_' . $hide_notice . '_notice', true );

			do_action( 'msex_hide_' . $hide_notice . '_notice' );
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

		wp_enqueue_style( 'msex-admin', plugins_url( '/assets/css/admin.css', MSEX_PLUGIN_FILE ), array (), MSEX_VERSION );

		foreach ( $notices as $notice ) {
			if ( ! empty( self::$core_notices[ $notice ] ) && apply_filters( 'msex_show_admin_notice', true, $notice ) ) {
				add_action( 'admin_notices', array ( __CLASS__, self::$core_notices[ $notice ] ) );
			} else {
				add_action( 'admin_notices', array ( __CLASS__, 'output_custom_notices' ) );
			}
		}
	}
	public static function add_custom_notice( $name, $notice_html ) {
		self::add_notice( $name );
		update_option( 'msex_admin_notice_' . $name, wp_kses_post( $notice_html ) );
	}
	public static function output_custom_notices() {
		$notices = self::get_notices();

		if ( ! empty( $notices ) ) {
			foreach ( $notices as $notice ) {
				if ( empty( self::$core_notices[ $notice ] ) ) {
					$notice_html = get_option( 'msex_admin_notice_' . $notice );

					if ( $notice_html ) {
						include dirname( __FILE__ ) . '/views/html-notice-custom.php';
					}
				}
			}
		}
	}
	public static function update_notice() {
		if ( MSEX_Install::needs_db_update() ) {
			$timestamp = as_next_scheduled_action( 'msex_run_update_callback', null, 'msex-db-updates' );

			if ( $timestamp || ! empty( $_GET['do_update_mshop_point'] ) ) { // WPCS: input var ok, CSRF ok.
				include dirname( __FILE__ ) . '/views/html-notice-updating.php';
			} else {
				include dirname( __FILE__ ) . '/views/html-notice-update.php';
			}
		} else {
			MSEX_Install::update_db_version();
			include dirname( __FILE__ ) . '/views/html-notice-updated.php';
		}
	}

}

MSEX_Admin_Notices::init();
