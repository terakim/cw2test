<?php

defined( 'ABSPATH' ) || exit;
class MSPS_Extinction_Notification {

	protected static $logger = null;

	protected static $balance_table;

	protected static $minimum_point = 0;

	protected static $action = 'msps_point_extinction_notification';

	public static function init() {
		self::$balance_table = MSPS_POINT_BALANCE_TABLE;

		self::$minimum_point = get_option( 'msps_extinction_notification_minimum_point', 1000 );
	}

	static function add_log( $msg ) {
		if ( is_null( self::$logger ) ) {
			self::$logger = new WC_Logger();
		}

		self::$logger->add( 'msps-point-extinction', $msg );
	}
	static function enabled() {
		return MSPS_Extinction::enabled() && class_exists( 'MSSMS_Manager' ) && 'yes' == get_option( 'msps_use_extinction_notification', 'no' ) && in_array( MSPS_Extinction::get_running_period(), array( 'year', 'monthly' ) );
	}
	static function is_running() {
		return ! empty( get_transient( self::$action ) );
	}

	static function clear() {
		self::deregister_scheduled_action();
		delete_transient( self::$action );
	}
	static function maybe_register_scheduled_action( $extinction_date, $page = 0, $immediately = false ) {
		if ( ! self::is_running() ) {
			self::register_scheduled_action( $extinction_date, $page, $immediately );
		}
	}
	static function maybe_deregister_scheduled_action() {
		if ( ! self::is_running() && class_exists( 'MSSMS_Manager' ) ) {
			self::deregister_scheduled_action();
		}
	}
	static function register_scheduled_action( $extinction_date, $page = 0, $immediately = false ) {
		if ( $immediately ) {
			as_schedule_single_action(
				time() + MINUTE_IN_SECONDS,
				self::$action,
				array( 'extinction_date' => $extinction_date, 'page' => $page )
			);
		} else {
			if ( ! self::is_running() ) {
				$next_schedule = MSPS_Extinction::get_next_schedule();

				if ( self::enabled() && ! empty( $next_schedule ) ) {
					$next_schedule = date( 'Y-m-d', strtotime( $next_schedule ) );

					$next_schedule = date( 'Y-m-d H:i:s', strtotime( $next_schedule . ' 09:00:00 - ' . get_option( 'msps_extinction_notification_term', '15' ) . ' day' ) );

					$next_time = strtotime( $next_schedule ) - intval( get_option( 'gmt_offset', 0 ) ) * HOUR_IN_SECONDS;

					if ( $next_time > time() + HOUR_IN_SECONDS ) {
						as_schedule_single_action(
							$next_time,
							self::$action,
							array( 'extinction_date' => $extinction_date, 'page' => 0 )
						);
					}

				}
			}

			delete_transient( self::$action );
		};
	}

	static function deregister_scheduled_action() {
		as_unschedule_all_actions( self::$action );
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
	public static function get_users( $extinction_date, $page ) {
		global $wpdb;

		$count_per_loop = 100;

		$table = self::$balance_table;

		if ( 0 == $page ) {
			$limit = "LIMIT {$count_per_loop}";
		} else {
			$start = $count_per_loop * $page;

			$limit = "LIMIT {$start}, {$count_per_loop}";
		}

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
	public static function send_extinction_notification( $user_data, $extinction_date ) {
		$user = new MSPS_User( $user_data['user_id'] );

		$used_point = MSPS_Extinction::get_used_point( $user_data['user_id'], $user_data['wallet_id'], $extinction_date );
		$user_point = $user->get_point( array( $user_data['wallet_id'] ) );

		$extinction_point = $user_data['term_balance'] - $used_point;

		if ( $extinction_point >= self::$minimum_point ) {
			self::add_log( sprintf( '[포인트 소멸 알림 전송] 사용자 ID : %d, Wallet ID : %s, 보유포인트 : %s, 소멸 예정 포인트 : %s', $user_data['user_id'], $user_data['wallet_id'], number_format( $user_point, wc_get_price_decimals() ), number_format( $extinction_point, wc_get_price_decimals() ) ) );
			self::send_alimtalk( $user_data, $extinction_point, $extinction_date );
		} else {
			self::add_log( sprintf( '[포인트 소멸 알림 - 소멸 예정 포인트 부족 ] 사용자 ID : %d, Wallet ID : %s, 보유포인트 : %s, 소멸 예정 포인트 : %s', $user_data['user_id'], $user_data['wallet_id'], number_format( $user_point, wc_get_price_decimals() ), number_format( $extinction_point, wc_get_price_decimals() ) ) );
		}
	}

	protected static function send_alimtalk( $user_data, $extinction_point, $extinction_date ) {
		try {
			if ( 'yes' == get_option( 'msps_use_extinction_notification_sms', 'no' ) || 'yes' == get_option( 'msps_use_extinction_notification_alimtalk', 'no' ) ) {
				$user = get_userdata( $user_data['user_id'] );

				$template_code = get_option( 'msps_extinction_notification_alimtalk_template_code' );
				if ( empty( $template_code ) ) {
					throw new Exception( __( '알림톡 템플릿이 지정되지 않았습니다.', 'mshop-point-ex' ) );
				}

				$recipient = get_user_meta( $user_data['user_id'], 'billing_phone', true );
				if ( empty( $recipient ) ) {
					throw new Exception( __( '고객의 전화번호가 없습니다.', 'mshop-point-ex' ) );
				}

				$customer_name = get_user_meta( $user_data['user_id'], 'billing_last_name', true ) . get_user_meta( $user_data['user_id'], 'billing_first_name', true );
				if ( empty( $customer_name ) ) {
					$customer_name = $user->display_name;
				}

				$template_params = array(
					'고객명'     => $customer_name,
					'소멸예정포인트' => number_format( $extinction_point, wc_get_price_decimals() ),
					'소멸예정일'   => date( 'Y-m-d', strtotime( $extinction_date ) )
				);

				if ( 'yes' == get_option( 'msps_use_extinction_notification_alimtalk', 'no' ) ) {
					$resend_method = 'alimtalk' == get_option( 'msps_extinction_notification_alimtalk_resend_method', 'none' );

					do_action( 'mssms_send_alimtalk', $template_code, array( $recipient ), $template_params, $resend_method );
				}

				if ( 'yes' == get_option( 'msps_use_extinction_notification_sms', 'no' ) ) {
					$message = get_option( 'msps_extinction_notification_sms_content' );

					foreach ( $template_params as $key => $value ) {
						$message = str_replace( '{' . $key . '}', $value, $message );
					}

					do_action( 'mshop_send_sms', $recipient, null, $message, get_option( "blogname", __( "코드엠샵", "mshop-point-ex" ) ) );
				}
			}
		} catch ( Exception $e ) {
			self::add_log( sprintf( '[알림톡 발송 실패] %s', $e->getMessage() ) );
		}
	}
	public static function run( $extinction_date, $page ) {
		if ( self::enabled() ) {
			self::add_log( '포인트 소멸 알림 처리 시작' );

			set_transient( self::$action, 'yes', DAY_IN_SECONDS );

			$result = self::get_users( $extinction_date, $page );

			self::add_log( sprintf( '포인트 소멸 기준일 : %s', $result['extinction_date'] ) );
			self::add_log( sprintf( '포인트 소멸 대상 사용자 수 : %d / %d', count( $result['users'] ), $result['total_count'] ) );

			if ( ! empty( $result['users'] ) ) {
				foreach ( $result['users'] as $user_data ) {
					self::send_extinction_notification( $user_data, $result['extinction_date'] );
				}
			}

			self::register_scheduled_action( $extinction_date, $page + 1, ! empty( $result['users'] ) );

			self::add_log( '포인트 소멸 알림 처리 종료.' );
		}
	}
}

MSPS_Extinction_Notification::init();
