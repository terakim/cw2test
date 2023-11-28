<?php

defined( 'ABSPATH' ) || exit;
class MSPS_Install {
	private static $db_updates = array(
		'3.0.2' => array(
			'msps_update_302_user_point',
			'msps_update_302_point_log',
			'msps_update_302_db_version',
		),
		'3.0.4' => array(
			'msps_update_304_alter_table',
			'msps_update_304_db_version',
		),
		'4.0.0' => array(
			'msps_update_400_alter_table',
			'msps_update_400_db_version',
		)
	);
	private static $db_direct_updates = array(
		'4.1.0' => array(
			'msps_update_410_alter_table',
			'msps_update_410_db_version',
		),
	);
	public static function init() {
		add_action( 'init', array( __CLASS__, 'check_version' ), 5 );
		add_action( 'msps_run_update_callback', array( __CLASS__, 'run_update_callback' ) );
		add_action( 'admin_init', array( __CLASS__, 'install_actions' ) );
	}
	public static function check_version() {
		if ( ! defined( 'IFRAME_REQUEST' ) && version_compare( get_option( 'msps_db_version' ), MSPS_DB_VERSION, '<' ) ) {
			self::install();
		}
	}
	public static function install_actions() {
		if ( ! empty( $_GET['do_update_mshop_point'] ) ) { // WPCS: input var ok, CSRF ok.
			self::update();
			MSPS_Admin_Notices::add_notice( 'update' );
		}
		if ( ! empty( $_GET['force_update_mshop_point'] ) ) { // WPCS: input var ok, CSRF ok.
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
		if ( 'yes' === get_transient( 'msps_installing' ) ) {
			return;
		}

		// If we made it till here nothing is running yet, lets set the transient now.
		set_transient( 'msps_installing', 'yes', MINUTE_IN_SECONDS * 10 );

		self::create_tables();
		self::setup_environment();
		self::maybe_update_db_version();

		delete_transient( 'msps_installing' );
	}
	private static function setup_environment() {
		MSPS_Post_types::register_post_types();
		MSPS_Post_types::register_taxonomies();
	}
	public static function needs_db_update() {
		$current_db_version = get_option( 'msps_db_version', null );
		$updates            = self::get_db_update_callbacks();

		return version_compare( $current_db_version, max( array_keys( $updates ) ), '<' );
	}
	private static function maybe_update_db_version() {
		if ( self::needs_db_update() ) {
			MSPS_Admin_Notices::add_notice( 'update' );
		} else {
			include_once( 'msps-update-functions.php' );

			foreach ( self::$db_direct_updates as $version => $db_update_functions ) {
				$current_db_version = get_option( 'msps_db_version', null );

				if ( version_compare( $current_db_version, $version, '<' ) ) {
					foreach ( $db_update_functions as $db_update_function ) {
						$db_update_function();
					}
				}
			}

			self::update_db_version();
		}
	}
	public static function get_db_update_callbacks() {
		return self::$db_updates;
	}
	public static function run_update_callback( $update_callback ) {
		include_once dirname( __FILE__ ) . '/msps-update-functions.php';

		if ( is_callable( $update_callback ) ) {
			$result = (bool) call_user_func( $update_callback );
			self::run_update_callback_end( $update_callback, $result );
		}
	}
	protected static function run_update_callback_end( $callback, $result ) {
		if ( $result ) {
			as_schedule_single_action(
				time(),
				'msps_run_update_callback',
				array(
					'update_callback' => $callback,
				),
				'msps-db-updates'
			);
		}
	}
	private static function update() {
		$current_db_version = get_option( 'msps_db_version' );
		$logger             = wc_get_logger();
		$update_queued      = false;
		$loop               = 0;

		foreach ( self::get_db_update_callbacks() as $version => $update_callbacks ) {
			if ( version_compare( $current_db_version, $version, '<' ) ) {
				foreach ( $update_callbacks as $update_callback ) {
					as_schedule_single_action(
						time() + $loop,
						'msps_run_update_callback',
						array(
							'update_callback' => $update_callback,
						),
						'msps-db-updates'
					);
					$loop ++;
				}
			}
		}

		if ( $update_queued ) {
			self::$background_updater->save()->dispatch();
		}
	}
	public static function update_db_version( $version = null ) {
		delete_option( 'msps_db_version' );
		add_option( 'msps_db_version', is_null( $version ) ? MSPS_DB_VERSION : $version );
	}
	private static function create_tables() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = '';

		$balance_table       = MSPS_POINT_BALANCE_TABLE;
		$log_table           = MSPS_POINT_LOG_TABLE;
		$login_history_table = MSPS_LOGIN_HISTORY_TABLE;

		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$log_table}'" ) != $log_table ) {
			$sql .= "DROP TABLE IF EXISTS `{$log_table}`;";
			$sql .= "CREATE TABLE `{$log_table}` (
					  `id` bigint(20) NOT NULL AUTO_INCREMENT,
					  `user_id` bigint(20) NOT NULL,
					  `wallet_id` varchar(100) NOT NULL,
					  `wallet_name` varchar(100) NOT NULL,
					  `date` datetime NOT NULL,
					  `type` varchar(20) NOT NULL,
					  `action` varchar(20) NOT NULL,
					  `amount` float NOT NULL,
					  `balance` float NOT NULL,
					  `status` varchar(20) NOT NULL,
					  `object_id` bigint(20) DEFAULT NULL,
					  `message` varchar(10000) DEFAULT NULL,
					  PRIMARY KEY (`id`),
  					  KEY `user_log` (`user_id`,`wallet_id`,`date`)
					) $charset_collate;";
		}

		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$balance_table}'" ) != $balance_table ) {
			$sql .= "DROP TABLE IF EXISTS `{$balance_table}`;";
			$sql .= "CREATE TABLE `{$balance_table}` (
					  `id` bigint(20) NOT NULL AUTO_INCREMENT,
					  `date` datetime NOT NULL,
					  `user_id` bigint(20) NOT NULL,
					  `wallet_id` varchar(100) NOT NULL,
					  `earn` decimal(20,2) NOT NULL DEFAULT 0,
  					  `deduct` decimal(20,2) NOT NULL DEFAULT 0,
  					  `extinction` tinyint(1) DEFAULT '0',
  					  `archive` tinyint(1) DEFAULT '0',
					  PRIMARY KEY (`id`),
					  KEY `user_balance` (`user_id`,`wallet_id`)
					) $charset_collate;";
		}

		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$login_history_table}'" ) != $login_history_table ) {
			$sql .= "DROP TABLE IF EXISTS `{$login_history_table}`;";
			$sql .= "CREATE TABLE `{$login_history_table}` (
						  `id` bigint(20) NOT NULL AUTO_INCREMENT,
						  `user_id` bigint(20) DEFAULT NULL,
						  `user_role` varchar(2000) DEFAULT NULL,
						  `user_agent` varchar(2000) DEFAULT NULL,
						  `ip_address` varchar(255) DEFAULT NULL,
						  `date` datetime DEFAULT NULL,
						  PRIMARY KEY (`id`),
						  KEY `user_id` (`user_id`,`date`)
					) $charset_collate;";
		}

		if ( ! empty( $sql ) ) {
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}
	}
}

MSPS_Install::init();
