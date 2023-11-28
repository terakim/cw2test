<?php

/*
=====================================================================================
                엠샵 프리미엄 포인트 / Copyright 2014-2015 by CodeM(c)
=====================================================================================

  [ 우커머스 버전 지원 안내 ]

   워드프레스 버전 : WordPress 4.3.1 이상

   우커머스 버전 : WooCommerce 3.0 이상


  [ 코드엠 플러그인 라이센스 규정 ]

   (주)코드엠에서 개발된 워드프레스  플러그인을 사용하시는 분들에게는 다음 사항에 대한 동의가 있는 것으로 간주합니다.

   1. 코드엠에서 개발한 워드프레스 우커머스용 엠샵 프리미엄 포인트 플러그인의 저작권은 (주)코드엠에게 있습니다.
   
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
	exit;
}

if ( ! class_exists( 'MSPS_Login_History' ) ) {

	class MSPS_Login_History {
		protected static $visit_point_rules = null;
		public static function init() {
			if ( self::enabled() ) {
				add_action( 'wp_login', array( __CLASS__, 'add_login_history_record' ), 10, 2 );
				add_action( 'wp_login', array( __CLASS__, 'maybe_process_visit_point' ), 20, 2 );
				add_action( 'wp_head', array( __CLASS__, 'maybe_output_earn_point_notification' ), 1 );
			}
		}
		public static function enabled() {
			return 'yes' == get_option( 'msps_use_visit_point_rule', 'no' );
		}
		public static function add_record( $args ) {
			global $wpdb;

			$wpdb->insert(
				MSPS_LOGIN_HISTORY_TABLE,
				array(
					'user_id'    => $args['user_id'],
					'user_role'  => msps_get( $args, 'user_role' ),
					'user_agent' => msps_get( $args, 'user_agent' ),
					'ip_address' => msps_get( $args, 'ip_address' ),
					'date'       => current_time( 'mysql' )
				),
				array(
					'%d',
					'%s',
					'%s',
					'%s',
					'%s',
				)
			);

			do_action( 'msps_add_login_history_record', $args );
		}
		public static function get_login_count( $args ) {
			global $wpdb;

			$table_name = MSPS_LOGIN_HISTORY_TABLE;

			$wheres = array( "user_id = {$args['user_id']}" );

			if ( ! empty( $args['date_from'] ) && ! empty( $args['date_to'] ) ) {
				$wheres[] = "date BETWEEN '{$args['date_from']}' AND '{$args['date_to']}'";
			} else if ( ! empty( $args['date'] ) ) {
				$date_from = date( 'Y-m-d 00:00:00', strtotime( $args['date'] ) );
				$date_to   = date( 'Y-m-d 23:59:59', strtotime( $args['date'] ) );
				$wheres[]  = "date BETWEEN '{$date_from}' AND '{$date_to}'";
			}

			$where = implode( ' AND ', $wheres );

			$query = "
				SELECT count(*)
				FROM {$table_name}
				WHERE 
					{$where}";

			return intval( $wpdb->get_var( $query ) );
		}
		public static function add_login_history_record( $user_login, $user ) {
			self::add_record( array(
				'user_id'    => $user->ID,
				'user_role'  => mshop_point_get_user_role( $user ),
				'user_agent' => msps_get( $_SERVER, 'HTTP_USER_AGENT' ),
				'ip_address' => msps_get( $_SERVER, 'HTTP_X_FORWARDED_FOR', $_SERVER['REMOTE_ADDR'] ),
			) );
		}
		public static function get_visit_point_rules() {
			if ( is_null( self::$visit_point_rules ) ) {
				$rules = get_option( 'msps_visit_point_rule', array() );

				self::$visit_point_rules = array_combine( array_column( $rules, 'role' ), $rules );
			}

			return self::$visit_point_rules;
		}
		public static function get_visit_point_rule( $user_role ) {
			$rules = self::get_visit_point_rules();

			return msps_get( $rules, $user_role, array() );
		}
		public static function maybe_process_visit_point( $user_login, $user ) {
			$count = self::get_login_count( array(
				'user_id' => $user->ID,
				'date'    => current_time( 'mysql' )
			) );

			$rule       = self::get_visit_point_rule( mshop_point_get_user_role( $user ) );
			$earn_point = floatval( msps_get( $rule, 'day', 0 ) );

			if ( $count <= 1 && $earn_point > 0 ) {
				$mshop_user   = new MSPS_User( $user );
				$remain_point = $mshop_user->earn_point( $earn_point );

                $message = get_option( 'mshop_point_system_notice_at_visit', '매일 방문 포인트 {point} 포인트가 적립되었습니다.' );
                $message = str_replace( '{point}', number_format_i18n( $earn_point, wc_get_price_decimals() ), $message );

				set_transient( 'msps-visit-point-' . $user->ID, $message, MINUTE_IN_SECONDS * 10 );

				MSPS_Log::add_log( $user->ID, 'free_point', 'earn', 'visit', $earn_point, $remain_point, 'completed', 0, $message );
			}
		}
		public static function maybe_output_earn_point_notification() {
			if ( ! is_user_logged_in() ) {
				return;
			}

			$message = get_transient( 'msps-visit-point-' . get_current_user_id() );

			if ( ! empty( $message ) ) {
				wc_get_template( "myaccount/visit-point-notice.php", array( 'message' => $message ), '', MSPS()->template_path() );

				delete_transient( 'msps-visit-point-' . get_current_user_id() );
			}
		}
	}

	MSPS_Login_History::init();
}

