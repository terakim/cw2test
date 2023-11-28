<?php

if ( ! class_exists( 'MSPS_Point_Wallet' ) ) {

	include_once( 'abstracts/abstract-msps-wallet-item.php' );

	class MSPS_Point_Wallet {
		protected $user_id;

		protected $lang = '';

		protected $wallet_items = null;

		public function __construct( $user_id, $lang = '' ) {
			$this->user_id = $user_id;
			$this->lang    = $lang;
		}

		public function get_wallet_class_names() {
			$wallet_items = array(
				'MSPS_Wallet_Item_Free_Point'
			);

			return apply_filters( 'msps_wallet_items', $wallet_items );
		}
		public function load_wallet_items( $item_types = array(), $valid_only = true ) {
			$this->wallet_items = array();

			foreach ( $this->get_wallet_class_names() as $wallet_class_name ) {

				if ( class_exists( $wallet_class_name ) ) {
					$wallet_item = new $wallet_class_name( $this->user_id, $this->lang );

					if ( empty( $item_types ) || in_array( $wallet_item->get_id(), $item_types ) ) {
						$this->wallet_items[ $wallet_item->get_id() ] = $wallet_item;
					}
				}
			}

			return apply_filters( 'load_wallet_items', $this->wallet_items, $item_types, $valid_only, $this );
		}
		public function get_user_id() {
			return $this->user_id;
		}
		public function get_language_code() {
			return $this->lang;
		}
		public function get_wallet_item( $id ) {
			$wallet_items = $this->load_wallet_items();

			return isset( $wallet_items[ $id ] ) ? $wallet_items[ $id ] : false;
		}
		public function get_point( $item_types = array() ) {
			$point        = 0;
			$wallet_items = $this->load_wallet_items( $item_types );

			foreach ( $wallet_items as $wallet_item ) {
				$point += floatval( $wallet_item->get_point() );
			}

			return $point;
		}
		public function get_earn_point_from( $date_from, $item_types = array() ) {
			$point        = 0;
			$wallet_items = $this->load_wallet_items( $item_types );

			foreach ( $wallet_items as $wallet_item ) {
				$point += floatval( $wallet_item->get_earn_point_from( $date_from ) );
			}

			return $point;
		}
		public function earn( $amount, $item_type = 'free_point' ) {
			$item = $this->get_wallet_item( $item_type );

			if ( $item ) {
				return $item->earn_point( $amount );
			}
		}

		public function get_deduction_info( $amount, $item_types = array() ) {
			$deduction_info = array();
			$remain         = $amount;
			$wallet_items   = $this->load_wallet_items( $item_types );

			foreach ( $wallet_items as $wallet_item ) {
				$point = $wallet_item->get_point();

				if ( $point > 0 ) {
					if ( $point >= $remain ) {
						$deduction_info[ $wallet_item->get_id() ] = $remain;

						$remain = 0;
						break;
					} else {
						$deduction_info[ $wallet_item->get_id() ] = $point;

						$remain -= $point;
					}
				}
			}

			if ( $remain > 0 ) {
				if ( isset( $deduction_info[ msps_get_wallet_id( 'free_point', null, $this->lang ) ] ) ) {
					$deduction_info[ msps_get_wallet_id( 'free_point', null, $this->lang ) ] += $remain;
				} else {
					$deduction_info[ msps_get_wallet_id( 'free_point', null, $this->lang ) ] = $remain;
				}
			}

			return apply_filters( 'msps_deduction_info', $deduction_info );
		}

		public function deduct( $deduction_info ) {
			$remain_point = 0;
			$wallet_items = $this->load_wallet_items();

			foreach ( $deduction_info as $item_id => $amount ) {
				if ( isset( $wallet_items[ $item_id ] ) ) {
					$wallet_items[ $item_id ]->deduct_point( $amount );
					$remain_point += $wallet_items[ $item_id ]->get_point();
				}
			}

			return $remain_point;
		}

		public function redeposit( $deduction_info ) {
			$wallet_items = $this->load_wallet_items();

			foreach ( $deduction_info as $item_id => $amount ) {
				if ( isset( $wallet_items[ $item_id ] ) ) {
					$wallet_items[ $item_id ]->earn_point( $amount );
				}
			}

			return $this->get_point();
		}

		public function set( $amount, $item_type = 'free_point' ) {
			$item = $this->get_wallet_item( $item_type );

			if ( $item ) {
				return $item->set_point( $amount );
			}
		}

	}
}