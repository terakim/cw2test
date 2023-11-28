<?php

defined( 'ABSPATH' ) || exit;
class MSEX_Install {
	private static $db_updates = array (
		'1.4.24' => array (
			'msex_update_1424_order_templates',
			'msex_update_1424_product_templates',
			'msex_update_1424_user_templates',
			'msex_update_1424_db_version',
		)
	);
	private static $background_updater;
	public static function init() {
		add_action( 'init', array ( __CLASS__, 'check_version' ), 5 );
		add_action( 'msex_run_update_callback', array ( __CLASS__, 'run_update_callback' ) );
		add_action( 'admin_init', array ( __CLASS__, 'install_actions' ) );
	}

	public static function check_version() {
		if ( ! defined( 'IFRAME_REQUEST' ) && version_compare( get_option( 'msex_db_version' ), MSEX_VERSION, '<' ) ) {
			self::install();
		}
	}
	public static function install_actions() {
		if ( ! empty( $_GET['do_update_msex'] ) ) { // WPCS: input var ok, CSRF ok.
			self::update();
			MSEX_Admin_Notices::add_notice( 'update' );
		}
		if ( ! empty( $_GET['force_update_msex'] ) ) { // WPCS: input var ok, CSRF ok.
			$blog_id = get_current_blog_id();
			do_action( 'wp_' . $blog_id . '_wc_updater_cron' );
			wp_safe_redirect( admin_url( 'admin.php?page=wc-settings' ) );
			exit;
		}
	}
	public static function install() {
		if ( ! is_blog_installed() ) {
			return;
		}

		// Check if we are not already running this routine.
		if ( 'yes' === get_transient( 'msex_installing' ) ) {
			return;
		}

		// If we made it till here nothing is running yet, lets set the transient now.
		set_transient( 'msex_installing', 'yes', MINUTE_IN_SECONDS * 10 );

		self::setup_environment();
		self::maybe_update_db_version();

		delete_transient( 'msex_installing' );
	}

	private static function setup_environment() {
		MSEX_Post_types::register_post_types();
	}

	public static function needs_db_update() {
		$current_db_version = get_option( 'msex_db_version', null );
		$updates            = self::get_db_update_callbacks();

		return version_compare( $current_db_version, max( array_keys( $updates ) ), '<' );
	}
	private static function maybe_update_db_version() {
		if ( self::needs_db_update() ) {
			MSEX_Admin_Notices::add_notice( 'update' );
		} else {
			self::update_db_version();
		}
	}
	public static function get_db_update_callbacks() {
		return self::$db_updates;
	}
	public static function run_update_callback( $update_callback ) {
		include_once dirname( __FILE__ ) . '/msex-update-functions.php';

		if ( is_callable( $update_callback ) ) {
			$result = (bool) call_user_func( $update_callback );
			self::run_update_callback_end( $update_callback, $result );
		}
	}
	protected static function run_update_callback_end( $callback, $result ) {
		if ( $result ) {
			as_schedule_single_action(
				time(),
				'msex_run_update_callback',
				array (
					'update_callback' => $callback,
				),
				'msex-db-updates'
			);
		}
	}
	private static function update() {
		$current_db_version = get_option( 'msex_db_version' );
		$logger             = wc_get_logger();
		$update_queued      = false;
		$loop               = 0;

		foreach ( self::get_db_update_callbacks() as $version => $update_callbacks ) {
			if ( version_compare( $current_db_version, $version, '<' ) ) {
				foreach ( $update_callbacks as $update_callback ) {
					as_schedule_single_action(
						time() + $loop,
						'msex_run_update_callback',
						array (
							'update_callback' => $update_callback,
						),
						'msex-db-updates'
					);
					$loop++;
				}
			}
		}

		if ( $update_queued ) {
			self::$background_updater->save()->dispatch();
		}
	}
	public static function update_db_version( $version = null ) {
		update_option( 'msex_db_version', empty( $version ) ? MSEX()->version : $version );
	}
}

MSEX_Install::init();
