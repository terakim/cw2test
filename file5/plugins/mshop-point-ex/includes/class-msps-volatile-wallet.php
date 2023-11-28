<?php

if ( ! class_exists( 'MSPS_Volatile_Wallet' ) ) {

	class MSPS_Volatile_Wallet {
		protected static $action = 'msps_extinct_volatile_wallet';

		protected static $volatile_wallets = null;
		protected static $logger = null;

		static function init() {
			include_once( 'wallet/class-msps-wallet-item-volatile-point.php' );

			add_filter( 'load_wallet_items', array( __CLASS__, 'load_volatile_wallets' ), 10, 4 );
			add_filter( 'msps_get_user_point_info', array( __CLASS__, 'get_user_point_info' ), 10, 2 );

			add_filter( 'msps_manage_point_settings', array( __CLASS__, 'volatile_wallet_settings' ) );
			add_filter( 'msps_batch_action_settings', array( __CLASS__, 'get_batch_action_settings' ) );

			add_action( 'msps_extinct_volatile_wallet', array( __CLASS__, 'extinct_volatile_wallet' ), 10, 2 );
		}
		static function enabled() {
			return 'yes' == get_option( 'msps_use_volatile_extinction', 'no' );
		}
		static function is_running( $wallet_id ) {
			return ! empty( get_transient( self::$action . '_' . $wallet_id ) );
		}

		static function add_log( $msg ) {
			if ( is_null( self::$logger ) ) {
				self::$logger = new WC_Logger();
			}

			self::$logger->add( 'msps-volatile-point-extinction', $msg );
		}
		static function get_volatile_wallets() {
			if ( is_null( self::$volatile_wallets ) ) {
				$volatile_wallets = get_option( 'msps_volatile_wallets', array() );

				self::$volatile_wallets = array_combine( array_column( $volatile_wallets, 'id' ), $volatile_wallets );
			}

			return self::$volatile_wallets;
		}
		static function is_volatile_wallet( $wallet_id ) {
			return in_array( $wallet_id, array_keys( self::get_volatile_wallets() ) );
		}
		static function get_volatile_wallet( $wallet_id ) {
			$volatile_wallets = self::get_volatile_wallets();

			return msps_get( $volatile_wallets, $wallet_id );
		}

		static function load_volatile_wallets( $wallet_items, $item_types, $valid_only, $wallet ) {
			$wallets = array();

			$volatile_wallets = self::get_volatile_wallets();

			if ( ! empty( $volatile_wallets ) && is_Array( $volatile_wallets ) ) {
				foreach ( $volatile_wallets as $volatile_wallet ) {
					if ( empty( $item_types ) || in_array( $volatile_wallet['id'], $item_types ) ) {
						if ( defined( 'MSPS_EXTINCT_VOLATILE_WALLET' ) || ! $valid_only || msps_volatile_wallet_is_valid( $volatile_wallet ) ) {
							$_wallet = new MSPS_Wallet_Item_Volatile_Point( $wallet->get_user_id(), $wallet->get_language_code() );
							$_wallet->set_id( $volatile_wallet['id'] );
							$_wallet->set_wallet_name( msps_get_volatile_wallet_name( $volatile_wallet ) );

							$wallets[ $volatile_wallet['id'] ] = $_wallet;
						}
					}
				}
			}

			return empty( $wallets ) ? $wallet_items : array_merge( $wallets, $wallet_items );
		}

		static function volatile_wallet_settings( $settings ) {
			$wallet_settings = array();

			foreach ( self::get_volatile_wallets() as $volatile_wallet ) {
				if ( msps_volatile_wallet_is_valid( $volatile_wallet ) ) {
					$wallet_settings[] = array(
						"id"           => $volatile_wallet['id'],
						"title"        => msps_get_volatile_wallet_name( $volatile_wallet ),
						"className"    => "two wide column",
						"type"         => "MShopPointAdjuster",
						'custom_event' => 'mshop_manage_point',
						'action'       => msps_ajax_command( 'adjust_volatile_point' ),
						'wallet_id'    => $volatile_wallet['id']
					);
				}
			}

			return array_merge(
				array_slice( $settings, 0, count( $settings ) - 1 ),
				$wallet_settings,
				array_slice( $settings, count( $settings ) - 1, 1 ) );
		}

		static function get_user_point_info( $info, $user ) {
			$msps_user = new MSPS_User( $user->ID );

			foreach ( self::get_volatile_wallets() as $volatile_wallet ) {
				if ( msps_volatile_wallet_is_valid( $volatile_wallet ) ) {
					$point = $msps_user->get_point( array( $volatile_wallet['id'] ) );

					$info[ $volatile_wallet['id'] ] = array(
						'point'      => $point,
						'point_desc' => number_format( $point, wc_get_price_decimals() ),
						'id'         => $user->ID,
						'name'       => $user->data->display_name
					);
				}
			}

			return $info;
		}

		static function get_batch_action_settings( $settings ) {
			foreach ( self::get_volatile_wallets() as $volatile_wallet ) {
				if ( msps_volatile_wallet_is_valid( $volatile_wallet ) ) {
					$settings[] = array(
						"id"           => "batch_adjust_" . $volatile_wallet['id'],
						"title"        => sprintf( __( "%s 일괄처리", 'mshop-point-ex' ), msps_get_volatile_wallet_name( $volatile_wallet ) ),
						"className"    => "two wide column",
						"type"         => "MShopPointBatchAdjuster",
						"filter"       => "mshop_manage_point_filter",
						'custom_event' => 'mshop_manage_point',
						'wallet_id'    => $volatile_wallet['id'],
						'action'       => msps_ajax_command( 'batch_adjust_volatile_point' )
					);
				}
			}

			return $settings;
		}
		static function register_scheduled_action( $wallet, $immediately = false ) {
			if ( $immediately ) {
				as_schedule_single_action(
					time(),
					self::$action,
					array(
						'wallet_id'   => $wallet['id'],
						'valid_until' => $wallet['valid_until']
					)
				);
			} else {
				if ( self::enabled() ) {
					self::deregister_scheduled_action( $wallet );

					$extinct_date = date( 'Y-m-d 00:00:00', strtotime( $wallet['valid_until'] . ' +1 day' ) );

					as_schedule_single_action(
						strtotime( $extinct_date ) - intval( get_option( 'gmt_offset', 0 ) ) * HOUR_IN_SECONDS,
						self::$action,
						array(
							'wallet_id'   => $wallet['id'],
							'valid_until' => $wallet['valid_until']
						)
					);
				}

				delete_transient( self::$action . '_' . $wallet['id'] );
			};
		}
		static function deregister_scheduled_action( $wallet ) {
			as_unschedule_all_actions( self::$action, array( 'wallet_id' => $wallet['id'] ) );
		}
		static function deregister_all_scheduled_actions() {
			as_unschedule_all_actions( self::$action );
		}
		static function maybe_update_scheduled_actions() {
			self::deregister_all_scheduled_actions();

			if ( self::enabled() ) {
				$volatile_wallets = self::get_volatile_wallets();

				foreach ( $volatile_wallets as $volatile_wallet ) {
					if ( msps_volatile_wallet_is_valid( $volatile_wallet ) && ! empty( $volatile_wallet['valid_until'] ) ) {
						self::register_scheduled_action( $volatile_wallet );
					}
				}
			}
		}
		public static function get_users( $wallet_id ) {
			global $wpdb;

			$count_per_loop = 1000;

			$table = MSPS_POINT_BALANCE_TABLE;
			$limit = "LIMIT {$count_per_loop}";

			$wheres   = array();
			$wheres[] = "wallet_id like '{$wallet_id}%'";

			$where = ' WHERE ' . implode( ' AND ', $wheres );

			$query = " SELECT SQL_CALC_FOUND_ROWS user_id, wallet_id, sum(earn - deduct) term_balance
				FROM {$table}
				{$where}
				GROUP BY user_id, wallet_id
 				HAVING term_balance > 0
				ORDER BY user_id
				{$limit}";

			return array(
				'users'       => $wpdb->get_results( $query, ARRAY_A ),
				'total_count' => $wpdb->get_var( "SELECT FOUND_ROWS();" ),
			);
		}
		public static function extinction_user_point( $user_data ) {
			$user = new MSPS_User( $user_data['user_id'] );
			$user_point = $user->get_point( array( $user_data['wallet_id'] ) );

			$user->deduct_point( $user_point, $user_data['wallet_id'] );

			self::add_log( sprintf( '사용자 ID : %d, Wallet ID : %s, 보유포인트 : %s, 소멸포인트 : %s', $user_data['user_id'], $user_data['wallet_id'], number_format( $user_point, wc_get_price_decimals() ), number_format( $user_data['term_balance'], wc_get_price_decimals() ) ) );

			$note = sprintf( __( '포인트 사용기한이 경과하여 사용하지 않은 포인트(%s)가 소멸되었습니다.', 'mshop-point-ex' ), number_format( $user_data['term_balance'], wc_get_price_decimals() ) );

			self::add_log( $note );

			MSPS_Log::add_log( $user_data['user_id'], $user_data['wallet_id'], 'deduct', 'auto', - 1 * $user_point, 0, 'completed', 0, $note, msps_get_wallet_name( $user, $user_data['wallet_id'] ) );
		}
		static function extinct_volatile_wallet( $wallet_id, $valid_until ) {
			if ( self::enabled() && self::is_volatile_wallet( $wallet_id ) && ! self::is_running( $wallet_id ) ) {
				define( 'MSPS_EXTINCT_VOLATILE_WALLET', true );

				$wallet = self::get_volatile_wallet( $wallet_id );

				if ( $wallet['valid_until'] != $valid_until ) {
					self::add_log( sprintf( "[오류] 포인트 월렛의 유효기간이 변경되어 포인트 소멸처리가 중지됩니다.  %s ( %s, %s ), %s", $wallet['name'], $wallet['id'], $wallet['valid_until'], $valid_until ) );
				} else {

					self::add_log( sprintf( "포인트 소멸 처리 시작 : %s ( %s )", $wallet['name'], $wallet['id'] ) );

					set_transient( self::$action . '_' . $wallet['id'], 'yes', DAY_IN_SECONDS );

					$result = self::get_users( $wallet_id );

					self::add_log( sprintf( '포인트 소멸 대상 사용자 수 : %d / %d', count( $result['users'] ), $result['total_count'] ) );

					if ( ! empty( $result['users'] ) ) {
						foreach ( $result['users'] as $user_data ) {
							self::extinction_user_point( $user_data );
						}
					}

					if ( count( $result['users'] ) < $result['total_count'] ) {
						self::register_scheduled_action( $wallet, true );
					}

					delete_transient( self::$action . '_' . $wallet['id'] );

					self::add_log( '포인트 소멸 처리 종료.' );
				}
			}
		}
	}

	MSPS_Volatile_Wallet::init();
}