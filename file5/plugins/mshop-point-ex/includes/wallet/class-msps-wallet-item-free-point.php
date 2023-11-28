<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class MSPS_Wallet_Item_Free_Point extends MSPS_Wallet_Item {
	public function __construct( $user_id, $lang = '' ) {
		parent::__construct( $user_id, 'free_point', __( '무상 포인트', 'mshop-point-ex' ), $lang );
	}

}
