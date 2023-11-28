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

if ( ! class_exists( 'MSPS_User' ) ) {
	class MSPS_User {
		protected $user;
		protected $point = null;
		public $wallet = null;
		protected $lang = '';
		protected $wallet_suffix = '';

		public function __construct( $user_id, $lang = '' ) {
			if ( is_numeric( $user_id ) ) {
				$this->user = new WP_User( $user_id );
			} else if ( $user_id instanceof WP_User ) {
				$this->user = $user_id;
			}
			if ( empty( $lang ) ) {
				$lang = apply_filters( 'msps_get_current_language', mshop_wpml_get_current_language() );
			}

			if ( $this->user ) {
				$this->wallet = new MSPS_Point_Wallet( $this->user->ID, $lang );
			}

			if ( ! empty( $lang ) ) {
				$this->lang          = $lang;
				$this->wallet_suffix = '_' . $lang;
			}
		}

		public function get_user_info( $field ) {
			return $this->user->$field;
		}

		protected function alter_item_type( $item_type ) {
			if ( is_array( $item_type ) ) {
				foreach ( $item_type as &$type ) {
					$type = apply_filters( 'msps_user_alter_item_type', $type . $this->wallet_suffix, $type, $this );
				}
			} else {
				$item_type = apply_filters( 'msps_user_alter_item_type', $item_type . $this->wallet_suffix, $item_type, $this );
			}

			return $item_type;
		}

		public function get_wallet_id( $item_type ) {
			return $this->alter_item_type( $item_type );
		}
		public function get_point( $item_types = array(), $alter_item_type = true ) {
			if ( $alter_item_type ) {
				return $this->wallet->get_point( $this->alter_item_type( $item_types ) );
			} else {
				return $this->wallet->get_point( $item_types );
			}
		}
		public function get_earn_point_from( $date_from, $item_types = array(), $alter_item_type = true ) {
			if ( $alter_item_type ) {
				return $this->wallet->get_earn_point_from( $date_from, $this->alter_item_type( $item_types ) );
			} else {
				return $this->wallet->get_earn_point_from( $date_from, $item_types );
			}
		}
		public function earn_point( $amount, $item_type = 'free_point' ) {
			return $this->wallet->earn( $amount, $this->alter_item_type( $item_type ) );
		}
		public function deduct_point( $amount, $item_type = 'free_point' ) {
			return $this->wallet->deduct( array( $this->alter_item_type( $item_type ) => $amount ) );
		}
		public function set_point( $amount, $item_type = 'free_point' ) {
			return $this->wallet->set( $amount, $this->alter_item_type( $item_type ) );
		}

		public function reset_user_point( $language = '' ) {
			$point = $this->get_point();

			if ( $point > 0 ) {
				foreach ( $this->wallet->load_wallet_items() as $wallet_id => $wallet_item ) {
					$point = $wallet_item->get_point();

					$wallet_item->deduct_point( $point );

					$note = sprintf( __( '%s(#%d) 사용자의 회원 탈퇴로 보유중인 %s 포인트가 소멸되었습니다', 'mshop-point-ex' ), $this->user->display_name, $this->user->ID, number_format( $point, wc_get_price_decimals() ) );

					MSPS_Log::add_log( $this->user->ID, msps_get_wallet_id( $wallet_id, null, $language ), 'deduct', 'unsubscribe', - 1 * $point, 0, 'completed', 0, $note, $wallet_item->get_name() );
				}

			}
		}
	}

}