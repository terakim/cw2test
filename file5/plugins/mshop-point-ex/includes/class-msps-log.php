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

if ( ! class_exists( 'MSPS_Log' ) ) {

	class MSPS_Log {

		public static function add_new_log( $user_id, $wallet_id, $type, $action, $amount, $balance, $status, $object_id = 0, $message = '', $wallet_name = '' ) {
			global $wpdb;

			$wpdb->insert(
				MSPS_POINT_LOG_TABLE,
				array(
					'user_id'     => $user_id,
					'wallet_id'   => $wallet_id,
					'wallet_name' => $wallet_name,
					'type'        => $type,
					'action'      => $action,
					'amount'      => $amount,
					'balance'     => $balance,
					'status'      => $status,
					'object_id'   => $object_id,
					'message'     => $message,
					'date'        => current_time( 'mysql' )
				),
				array(
					'%d',
					'%s',
					'%s',
					'%s',
					'%s',
					'%f',
					'%f',
					'%s',
					'%d',
					'%s',
					'%s',
				)
			);

			do_action( 'msps_add_log', $user_id, $wallet_id, $type, $action, $amount, $balance, $status, $object_id, $message );
		}

		public static function add_log( $user_id, $wallet_id, $type, $action, $amount, $balance, $status, $object_id = 0, $message = '', $wallet_name = '' ) {
			global $wpdb;
			$result = false;

			if ( ! empty( $object_id ) ) {
				$fields = apply_filters( 'msps_update_log_fields', array(
					'amount'  => $amount,
					'balance' => $balance,
					'status'  => $status,
					'message' => $message,
					'date'    => current_time( 'mysql' )
				) );

				$field_types = apply_filters( 'msps_update_log_field_types', array(
					'%f',
					'%f',
					'%s',
					'%s',
					'%s',
				) );

				$result = $wpdb->update(
					MSPS_POINT_LOG_TABLE,
					$fields,
					array(
						'user_id'   => $user_id,
						'wallet_id' => $wallet_id,
						'type'      => $type,
						'action'    => $action,
						'object_id' => $object_id,
						'status'    => 'pending'
					),
					$field_types,
					array(
						'%d',
						'%s',
						'%s',
						'%s',
						'%d',
						'%s'
					)
				);
			}

			if ( 0 == $result ) {
				self::add_new_log( $user_id, $wallet_id, $type, $action, $amount, $balance, $status, $object_id, $message, $wallet_name );
			}
		}

		public static function get_logs( $user_id, $page, $page_size = 10, $wallet_id = 'all', $current_language = '' ) {
			global $wpdb;

			$table_name = MSPS_POINT_LOG_TABLE;

			if ( ! empty( $page ) && $page > 1 ) {
				$limit = 'LIMIT ' . ( ( $page - 1 ) * $page_size ) . ', ' . $page_size;
			} else {
				$limit = 'LIMIT ' . $page_size;
			}

			$where = '';
			if ( 'all' != $wallet_id ) {
				$where = "AND wallet_id = '" . $wallet_id . "'";
			} else if ( ! empty( $current_language ) ) {
				$where = "AND wallet_id like '%_" . $current_language . "'";
			}

			$query = "
				SELECT SQL_CALC_FOUND_ROWS *
				FROM {$table_name}
				WHERE 
					user_id = {$user_id} 
					{$where}
                ORDER BY 
                	id DESC
				{$limit}";

			return array(
				'results'     => $wpdb->get_results( $query, ARRAY_A ),
				'total_count' => $wpdb->get_var( "SELECT FOUND_ROWS();" ),
			);
		}

		public static function delete_logs( $ids ) {
			global $wpdb;

			$table_name = MSPS_POINT_LOG_TABLE;

			$query = "DELETE FROM {$table_name} WHERE id IN (" . implode( ',', $ids ) . ")";

			$wpdb->query( $query );
		}

		public static function get_admin_logs( $user_id, $term, $wallet_type, $sort_key, $sort_order, $page, $page_size = 10, $current_language = '' ) {
			global $wpdb;

			$table_name = MSPS_POINT_LOG_TABLE;

			if ( $page_size > 0 ) {
				$limit = 'LIMIT ' . $page_size;
			}

			$wheres = ! empty( $user_id ) ? array( ' user_id IN (' . $user_id . ')' ) : array();

			if ( ! empty( $term ) ) {
				$terms = explode( ',', $term );
				if ( ! empty( $terms[0] ) && ! empty( $terms[1] ) ) {
					$wheres[] = 'date between "' . $terms[0] . ' 00:00:00" AND "' . $terms[1] . ' 23:59:59" ';
				}
			}

			if ( 'all' == $wallet_type ) {
				if ( ! empty( $current_language ) ) {
					$wheres[] = 'wallet_id like "%_' . $current_language . '"';
				}
			} else {
				if ( ! empty( $current_language ) ) {
					$wheres[] = 'wallet_id like "' . $wallet_type . '_' . $current_language . '"';
				} else {
					$wheres[] = 'wallet_id = "' . $wallet_type . '"';
				}
			}

			if ( ! empty( $sort_key ) ) {
				$order_by = 'ORDER BY ' . $sort_key . ' ' . ( $sort_order == 'ascending' ? 'ASC' : 'DESC' );
			} else {
				$order_by = 'ORDER BY id DESC';
			}

			if ( $page_size > 0 && ! empty( $page ) && $page > 0 ) {
				$limit = 'LIMIT ' . ( $page * $page_size ) . ', ' . $page_size;
			}

			$where = ! empty( $wheres ) ? 'WHERE ' . implode( ' AND ', $wheres ) : '';

			$query = "
				SELECT SQL_CALC_FOUND_ROWS *
				FROM {$table_name}
				{$where}
				{$order_by}
				{$limit}";

			return array(
				'results'     => $wpdb->get_results( $query, ARRAY_A ),
				'total_count' => $wpdb->get_var( "SELECT FOUND_ROWS();" ),
			);
		}
	}
}