<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class MSPS_Wallet_Item_Volatile_Point extends MSPS_Wallet_Item {
	public function __construct( $user_id, $lang = '' ) {
		parent::__construct( $user_id, 'volatile_point', __( '기간제한 포인트', 'mshop-point-ex' ), $lang );
	}

	public function set_id( $wallet_id ) {
		$this->id = $wallet_id;
	}

	public function set_wallet_name( $wallet_name ) {
		$this->label = $wallet_name;
	}
}