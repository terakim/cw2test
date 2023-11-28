<?php

defined( 'ABSPATH' ) || exit;
class MSPS_Extinction {
	protected static $logger = null;
	protected static $balance_table;
	protected static $action = 'msps_point_extinction';

	public static function init() {
		self::$balance_table = MSPS_POINT_BALANCE_TABLE;
	}

	static function add_log( $msg ) {
		if ( is_null( self::$logger ) ) {
			self::$logger = new WC_Logger();
		}

		self::$logger->add( 'msps-point-extinction', $msg );
	}

	static function enabled() {
		return 'yes' == get_option( 'msps_use_extinction', 'no' );
	}

	static function get_running_period() {
		return get_option( 'msps_extinction_running_period', 'monthly' );
	}
	static function is_running() {
		return ! empty( get_transient( self::$action ) );
	}

	static function clear() {
		self::deregister_scheduled_action();
		delete_transient( self::$action );
	}
	static function maybe_register_scheduled_action() {
		if ( ! self::is_running() ) {
			self::register_scheduled_action();
		}
	}
	static function maybe_deregister_scheduled_action() {
		if ( ! self::is_running() ) {
			self::deregister_scheduled_action();
		}
	}
	static function register_scheduled_action( $immediately = false ) {
		if ( $immediately ) {
			as_schedule_single_action(
				time() + MINUTE_IN_SECONDS,
				self::$action
			);
		} else {
			if ( self::enabled() ) {
				self::deregister_scheduled_action();

				if ( 'monthly' == self::get_running_period() ) {
					// 매월
					$day = get_option( 'msps_extinction_running_day', '1' );

					if ( date( 'd', strtotime( current_time( 'mysql' ) ) ) >= $day ) {
						if ( $day >= 28 ) {
							$next_date = date( 'Y-m-t 00:00:00', strtotime( current_time( 'mysql' ) . ' +1 month' ) );
						} else {
							$next_date = date( sprintf( 'Y-m-%02d 00:00:00', get_option( 'msps_extinction_running_day', '1' ) ), strtotime( current_time( 'mysql' ) . ' +1 month' ) );
						}
					} else {
						if ( $day >= 28 ) {
							$next_date = date( 'Y-m-t 00:00:00' );
						} else {
							$next_date = date( sprintf( 'Y-m-%02d 00:00:00', get_option( 'msps_extinction_running_day', '1' ) ) );
						}
					}
				} else if ( 'year' == self::get_running_period() ) {
					// 매년
					$day       = get_option( 'msps_extinction_running_date', '01-01' );
					$day_array = explode( '-', $day );
					if ( date( 'm' ) > $day_array[0] ) {
						$next_date = date( sprintf( 'Y-%02d-%02d 00:00:00', $day_array[0], $day_array[1] ), strtotime( current_time( 'mysql' ) . '+1 year' ) );
					} elseif ( date( 'm' ) == $day_array[0] && date( 'd' ) >= $day_array[1] ) {
						$next_date = date( sprintf( 'Y-%02d-%02d 00:00:00', $day_array[0], $day_array[1] ), strtotime( current_time( 'mysql' ) . '+1 year' ) );
					} else {
						$next_date = date( sprintf( 'Y-%02d-%02d 00:00:00', $day_array[0], $day_array[1] ) );
					}
				} else {
					$next_date = date( 'Y-m-d 00:00:00', strtotime( current_time( 'mysql' ) . ' +1 day' ) );
				}

				$next_time = strtotime( $next_date ) - intval( get_option( 'gmt_offset', 0 ) ) * HOUR_IN_SECONDS;

				if ( $next_time > time() ) {
					as_schedule_single_action(
						$next_time,
						self::$action
					);
				}

				$extinction_date = date( 'Y-m-d 00:00:00', strtotime( $next_date . ' - ' . get_option( 'msps_extinction_term', 365 ) . ' days' ) );

				MSPS_Extinction_Notification::maybe_register_scheduled_action( $extinction_date );
			}

			delete_transient( self::$action );
		};
	}
	static function deregister_scheduled_action() {
		as_unschedule_all_actions( self::$action );

		MSPS_Extinction_Notification::maybe_deregister_scheduled_action();
	}

	static function get_next_schedule() {
		if ( self::enabled() && class_exists( 'ActionScheduler_Store' ) ) {
			$actions = as_get_scheduled_actions( array( 'hook' => self::$action, 'status' => ActionScheduler_Store::STATUS_PENDING ) );
			$action  = current( $actions );

			if ( $action ) {
				$schedule = $action->get_schedule();

				$date = $schedule->get_date();

				if ( is_callable( array( $schedule, 'get_date' ) ) ) {
					return $schedule->get_date()->modify( '+' . get_option( 'gmt_offset', 0 ) . ' hours' )->format( 'Y-m-d H:i' );
				} else if ( is_callable( array( $schedule, 'next' ) ) ) {
					return $schedule->next()->modify( '+' . get_option( 'gmt_offset', 0 ) . ' hours' )->format( 'Y-m-d H:i' );
				}
			}
		}

		return '';
	}
	public static function get_users() {
		global $wpdb;

		$count_per_loop = 1000;

		$table = self::$balance_table;
		$limit = "LIMIT {$count_per_loop}";

		$extinction_date = date( 'Y-m-d 00:00:00', strtotime( '- ' . get_option( 'msps_extinction_term', 365 ) . ' days' ) );

		$wheres   = array();
		$wheres[] = sprintf( "date < '%s'", $extinction_date );
		$wheres[] = 'extinction = 0';

		$wallet_ids   = get_option( 'msps_extinction_wallet_ids' );
		$wallet_ids   = array_filter( explode( ',', $wallet_ids ) );
		$wallet_where = array();
		foreach ( $wallet_ids as $wallet_id ) {
			$wallet_where[] = sprintf( "wallet_id like '%s%%'", $wallet_id );
		}
		$wheres[] = sprintf( "(%s)", implode( " OR ", $wallet_where ) );

		$where = ' WHERE ' . implode( ' AND ', $wheres );

		$query = " SELECT SQL_CALC_FOUND_ROWS user_id, wallet_id, sum(earn - deduct) term_balance
				FROM {$table}
				{$where}
				GROUP BY user_id, wallet_id
				ORDER BY user_id
				{$limit}";

		return array(
			'users'           => $wpdb->get_results( $query, ARRAY_A ),
			'extinction_date' => $extinction_date,
			'total_count'     => $wpdb->get_var( "SELECT FOUND_ROWS();" ),
		);
	}

	public static function get_used_point( $user_id, $wallet_id, $extinction_date ) {
		global $wpdb;

		$table = self::$balance_table;

		$query = " SELECT sum(deduct) used_point FROM {$table} WHERE user_id = {$user_id} AND wallet_id = '{$wallet_id}' AND date >= '{$extinction_date}'";

		$used_point = $wpdb->get_var( $query );

		return $used_point ? $used_point : 0;
	}

	public static function cleanup_balance_log( $user_id, $wallet_id, $extinction_date = null ) {
		global $wpdb;

		$table = self::$balance_table;

		if ( $extinction_date ) {
			$wpdb->query( "UPDATE {$table} SET extinction = 1 WHERE user_id = {$user_id} AND wallet_id = '{$wallet_id}' AND extinction = 0 AND date < '{$extinction_date}'" );
		} else {
			$wpdb->query( "UPDATE {$table} SET extinction = 1 WHERE user_id = {$user_id} AND wallet_id = '{$wallet_id}' AND extinction = 0;" );
		}
	}

	public static function compress_balance_log( $user_id, $wallet_id, $extinction_date, $earn, $deduct ) {
		global $wpdb;

		$table = self::$balance_table;

		if ( $earn > 0 || $deduct > 0 ) {
			$wpdb->query( "INSERT INTO {$table} ( date, user_id, wallet_id, earn, deduct, archive ) VALUES ( '{$extinction_date}', {$user_id}, '{$wallet_id}', {$earn}, {$deduct}, 1 )" );
		}

		self::cleanup_balance_log( $user_id, $wallet_id, $extinction_date );
	}
	public static function extinction_user_point( $user_data, $extinction_date ) {
		global $wpdb;

		$user = new MSPS_User( $user_data['user_id'] );
		$user_point = $user->get_point( array( $user_data['wallet_id'] ) );

		$wpdb->query( 'START TRANSACTION' );

		$used_point = self::get_used_point( $user_data['user_id'], $user_data['wallet_id'], $extinction_date );

		$extinction_point = $user_data['term_balance'] - $used_point;
		if ( $extinction_point < 0 ) {
			$extinction_point = 0;
		}
		self::add_log( sprintf( '사용자 ID : %d, Wallet ID : %s, 보유포인트 : %s, 소멸포인트 : %s', $user_data['user_id'], $user_data['wallet_id'], number_format( $user_point, 2 ), number_format( $extinction_point, 2 ) ) );

		self::compress_balance_log( $user_data['user_id'], $user_data['wallet_id'], $extinction_date, $user_data['term_balance'], $extinction_point );

		if ( $extinction_point > 0 ) {
			$note = sprintf( __( '포인트 사용기한이 경과하여 사용하지 않은 포인트(%s)가 소멸되었습니다.', 'mshop-point-ex' ), number_format( $extinction_point, 2 ) );

			self::add_log( $note );

			MSPS_Log::add_log( $user_data['user_id'], $user_data['wallet_id'], 'deduct', 'auto', - 1 * $extinction_point, $user_point - $extinction_point, 'completed', 0, $note );
		}

		$wpdb->query( 'COMMIT' );
	}
	public static function run() {
		if ( self::enabled() ) {
			self::add_log( '포인트 소멸 처리 시작' );

			set_transient( self::$action, 'yes', DAY_IN_SECONDS );

			$result = self::get_users();

			self::add_log( sprintf( '포인트 소멸 기준일 : %s', $result['extinction_date'] ) );
			self::add_log( sprintf( '포인트 소멸 대상 사용자 수 : %d / %d', count( $result['users'] ), $result['total_count'] ) );

			if ( ! empty( $result['users'] ) ) {
				foreach ( $result['users'] as $user_data ) {
					self::extinction_user_point( $user_data, $result['extinction_date'] );
				}
			}

			self::register_scheduled_action( count( $result['users'] ) < $result['total_count'] );

			self::add_log( '포인트 소멸 처리 종료.' );
		}
	}
}

MSPS_Extinction::init();
