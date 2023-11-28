<?php

defined( 'ABSPATH' ) || exit;
class MSM_Personal_Info {
	protected static $users_table;

	protected static $action = 'msm_personal_information_notification';

	public static function init() {
		self::$users_table = MSM_GET_USERS_TABLE;
	}
	static function enabled() {
		return 'yes' == get_option( 'mshop_members_personal_info_noti', 'no' );
	}
	static function is_running() {
		return ! empty( get_transient( self::$action ) );
	}

	static function clear() {
		self::deregister_scheduled_action();
		delete_transient( self::$action );
	}
	static function maybe_register_scheduled_action( $page = 0, $immediately = false ) {
		if ( ! self::is_running() ) {
			self::register_scheduled_action( $page, $immediately );
		}
	}
	static function maybe_deregister_scheduled_action() {
		if ( ! self::is_running() ) {
			self::deregister_scheduled_action();
		}
	}
	static function register_scheduled_action( $page = 0, $immediately = false ) {
		if ( $immediately ) {
			as_schedule_single_action(
				time() + MINUTE_IN_SECONDS,
				self::$action,
				array( 'page' => $page )
			);
		} else {
			if ( self::enabled() ) {
				$next_schedule = self::get_next_schedule();
				if ( empty( $next_schedule ) ) {
					$next_date = date( 'Y-m-01 H:i:s', strtotime( apply_filters( 'msm_personal_info_send_time', '09:00' ) . ' +1 month' ) );

					$next_time = strtotime( $next_date ) - intval( get_option( 'gmt_offset', 0 ) ) * HOUR_IN_SECONDS;

					if ( $next_time > time() ) {
						as_schedule_single_action(
							$next_time,
							self::$action,
							array( 'page' => 0 )
						);
					}
				}
			}

			delete_transient( self::$action );
		}
	}

	static function deregister_scheduled_action() {
		if ( function_exists( 'as_unschedule_all_actions' ) ) {
			as_unschedule_all_actions( self::$action );
		}
	}

	static function get_next_schedule() {
		if ( self::enabled() && class_exists( 'ActionScheduler_Store' ) ) {
			$actions = as_get_scheduled_actions( array( 'hook' => self::$action, 'status' => ActionScheduler_Store::STATUS_PENDING ) );
			$action  = current( $actions );

			if ( $action ) {
				$schedule = $action->get_schedule();

				if ( is_callable( array( $schedule, 'get_date' ) ) ) {
					return $schedule->get_date()->modify( '+' . get_option( 'gmt_offset', 0 ) . ' hours' )->format( 'Y-m-d H:i' );
				} else if ( is_callable( array( $schedule, 'next' ) ) ) {
					return $schedule->next()->modify( '+' . get_option( 'gmt_offset', 0 ) . ' hours' )->format( 'Y-m-d H:i' );
				}
			}
		}

		return '';
	}
	public static function get_users( $page ) {
		global $wpdb;

		$count_per_loop = 50;

		$table = self::$users_table;

		if ( 0 == $page ) {
			$limit = "LIMIT {$count_per_loop}";
		} else {
			$start = $count_per_loop * $page;

			$limit = "LIMIT {$start}, {$count_per_loop}";
		}

		$date  = date( 'Y-m-01 00:00:00' );
		$month = date( 'm' );

		$query = " SELECT SQL_CALC_FOUND_ROWS ID
				FROM {$table}
				WHERE
				user_registered < '{$date}'
				AND MONTH( user_registered ) = '{$month}'
				{$limit}";

		return array(
			'users'       => $wpdb->get_results( $query, ARRAY_A ),
			'total_count' => $wpdb->get_var( "SELECT FOUND_ROWS();" ),
		);
	}
	public static function run( $page ) {
		if ( self::enabled() ) {

			set_transient( self::$action, 'yes', DAY_IN_SECONDS );

			$result = self::get_users( $page );

			if ( ! empty( $result['users'] ) ) {
				foreach ( $result['users'] as $user ) {
					$user = get_userdata( $user['ID'] );
					MSM_Emails::send_personal_info_email( $user );
				}
			}

			self::register_scheduled_action( $page + 1, ! empty( $result['users'] ) );
		}
	}
}

MSM_Personal_Info::init();
