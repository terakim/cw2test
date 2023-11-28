<?php

defined( 'ABSPATH' ) || exit;

function msps_update_302_user_point() {
	global $wpdb;

	if ( ! defined( 'MSPS_SUB_DOMAIN' ) && MSPS_Install::needs_db_update() ) {
		$balance_table = MSPS_POINT_BALANCE_TABLE;

		$languages = mshop_wpml_get_active_languages();

		if ( empty( $languages ) ) {
			$languages['none'] = '';
		}

		foreach ( $languages as $key => $data ) {
			$meta_key  = 'none' == $key ? '_mshop_point' : '_mshop_point_' . $key;
			$wallet_id = 'none' == $key ? 'free_point' : 'free_point_' . $key;

			$wpdb->query( "INSERT INTO {$balance_table} ( date, user_id, wallet_id, earn, deduct )
					SELECT NOW(), user_id, '{$wallet_id}', sum(meta_value), 0
					FROM {$wpdb->usermeta}
					WHERE meta_key = '{$meta_key}'
					GROUP BY user_id" );
		}
	}
}

function msps_update_302_point_log() {
	global $wpdb;

	if ( MSPS_Install::needs_db_update() ) {
		$log_table = MSPS_POINT_LOG_TABLE;

		$languages = mshop_wpml_get_active_languages();

		if ( empty( $languages ) ) {
			$languages['none'] = '';
		}
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}mshop_point_history'" ) == $wpdb->prefix . 'mshop_point_history' ) {
			foreach ( $languages as $key => $data ) {
				$wallet_id = 'none' == $key ? 'free_point' : 'free_point_' . $key;

				$wpdb->query( "INSERT INTO {$log_table} ( user_id, wallet_id, date, type, action, amount, status, message )
					SELECT userid, '{$wallet_id}', date, IF( point > 0, 'earn', 'deduct'), 'order', point, 'completed', message
					FROM {$wpdb->prefix}mshop_point_history
					WHERE
					    is_admin = 0;" );
			}
		}
	}
}

function msps_update_302_db_version() {
	MSPS_Install::update_db_version( '3.0.2' );
}

function msps_update_304_alter_table() {
	if ( MSPS_Install::needs_db_update() ) {
		global $wpdb;

		$balance_table = MSPS_POINT_BALANCE_TABLE;

		$wpdb->query( "ALTER TABLE {$balance_table} MODIFY COLUMN earn DECIMAL(20,2) DEFAULT 0, MODIFY COLUMN deduct DECIMAL(20,2) DEFAULT 0;" );
		$wpdb->query( "UPDATE {$balance_table} SET earn = 0 WHERE earn IS NULL;" );
		$wpdb->query( "UPDATE {$balance_table} SET deduct = 0 WHERE deduct IS NULL;" );
	}
}

function msps_update_304_db_version() {
	MSPS_Install::update_db_version( '3.0.4' );
}


function msps_update_400_alter_table() {
	if ( MSPS_Install::needs_db_update() ) {
		global $wpdb;

		$balance_table = MSPS_POINT_BALANCE_TABLE;

		$wpdb->query( "ALTER TABLE {$balance_table} ADD COLUMN extinction tinyint(1) DEFAULT '0', ADD COLUMN archive tinyint(1) DEFAULT '0';" );
		$wpdb->query( "ALTER TABLE {$balance_table} DROP INDEX user_balance;" );
		$wpdb->query( "ALTER TABLE {$balance_table} ADD INDEX user_balance (`user_id`,`wallet_id`, `extinction` );" );
		$wpdb->query( "UPDATE {$balance_table} SET extinction = 0 WHERE extinction IS NULL;" );
		$wpdb->query( "UPDATE {$balance_table} SET archive = 0 WHERE archive IS NULL;" );
	}
}

function msps_update_400_db_version() {
	MSPS_Install::update_db_version( '4.0.0' );
}

function msps_update_410_alter_table() {
	global $wpdb;

	$log_table = MSPS_POINT_LOG_TABLE;

	$wpdb->query( "ALTER TABLE {$log_table} ADD COLUMN wallet_name varchar (100) DEFAULT '';" );
}

function msps_update_410_db_version() {
	MSPS_Install::update_db_version( '4.1.0' );
}