<?php

class MSPS_Wallet_Item {
	public $user_id = 0;
	protected $id;
	public $label;
	public $lang;
	protected $enabled = true;
	public function __construct( $user_id, $wallet_id, $wallet_label, $lang ) {
		$this->user_id = $user_id;
		$this->id      = $wallet_id;
		$this->label   = $wallet_label;
		$this->lang    = $lang;

		if ( ! empty( $lang ) ) {
			$this->id .= '_' . $lang;
		}
	}

	public function get_enabled() {
		return $this->enabled;
	}

	public function set_enabled( $enabled ) {
		$this->enabled = $enabled;
	}

	public function get_id() {
		return $this->id;
	}

	public function get_name() {
		return $this->label;
	}

	public function get_point( $except_order_id = 0 ) {
		global $wpdb;

		$balance_table = MSPS_POINT_BALANCE_TABLE;

		$point = $wpdb->get_var( "SELECT sum(earn) - sum(deduct) FROM {$balance_table} WHERE user_id = {$this->user_id} AND wallet_id = '{$this->id}' AND extinction = 0" );

		if( MSPS_HPOS::enabled() ) {
			$point -= $wpdb->get_var( "
					SELECT sum( point_meta.meta_value )
					FROM {$wpdb->prefix}wc_orders orders
					LEFT JOIN {$wpdb->prefix}wc_orders_meta point_meta ON orders.id = point_meta.order_id and point_meta.meta_key = '_{$this->id}'
					WHERE
					     orders.status = 'wc-pending'
					     AND point_meta.meta_value > 0
					     AND orders.customer_id = {$this->user_id}
					     AND orders.id != {$except_order_id}" );
		}else{
			$point -= $wpdb->get_var( "
					SELECT sum( point_meta.meta_value )
					FROM $wpdb->posts orders
					LEFT JOIN $wpdb->postmeta point_meta ON orders.ID = point_meta.post_id and point_meta.meta_key = '_{$this->id}'
					LEFT JOIN $wpdb->postmeta customer_meta ON orders.ID = customer_meta.post_id and customer_meta.meta_key = '_customer_user'
					WHERE
					     orders.post_status = 'wc-pending'
					     AND point_meta.meta_value > 0
					     AND customer_meta.meta_value = {$this->user_id}
					     AND orders.ID != $except_order_id" );
		}

		return ! empty( $point ) ? $point : 0;
	}
	public function get_earn_point_from( $date_from ) {
		global $wpdb;

		$balance_table = MSPS_POINT_BALANCE_TABLE;

		$point = $wpdb->get_var( "SELECT sum(earn) FROM {$balance_table} WHERE user_id = {$this->user_id} AND wallet_id = '{$this->id}' AND extinction = 0 AND date >= '{$date_from}'" );

		return ! empty( $point ) ? $point : 0;
	}
	public function earn_point( $amount ) {
		return $this->set_point( $amount, 'earn' );
	}
	public function deduct_point( $amount ) {
		return $this->set_point( $amount, 'deduction' );
	}

	public function set_point( $amount, $mode = 'set' ) {
		global $wpdb;

		if ( ! is_null( $amount ) ) {
			switch ( $mode ) {
				case 'earn' :
					$wpdb->insert(
						MSPS_POINT_BALANCE_TABLE,
						array(
							'date'      => current_time( 'mysql' ),
							'user_id'   => $this->user_id,
							'wallet_id' => $this->id,
							'earn'      => $amount
						),
						array(
							'%s',
							'%d',
							'%s',
							'%f'
						)
					);
					break;
				case 'deduction' :
					$wpdb->insert(
						MSPS_POINT_BALANCE_TABLE,
						array(
							'date'      => current_time( 'mysql' ),
							'user_id'   => $this->user_id,
							'wallet_id' => $this->id,
							'deduct'    => $amount
						),
						array(
							'%s',
							'%d',
							'%s',
							'%f'
						)
					);
					break;
				default :
					$wpdb->delete(
						MSPS_POINT_BALANCE_TABLE,
						array(
							'user_id' => $this->user_id,
							'wallet_id' => $this->id,
						),
						array(
							'%d',
							'%s',
						)
					);

					$wpdb->insert(
						MSPS_POINT_BALANCE_TABLE,
						array(
							'date'      => current_time( 'mysql' ),
							'user_id'   => $this->user_id,
							'wallet_id' => $this->id,
							'earn'      => $amount
						),
						array(
							'%s',
							'%d',
							'%s',
							'%f'
						)
					);
					break;
			}

			update_user_meta( $this->user_id, '_mshop_last_date', current_time( 'mysql' ) );

			do_action( 'msps_set_point', $mode, $this->id, $this->user_id, $amount );
		}

		return $this->get_point();
	}
}
