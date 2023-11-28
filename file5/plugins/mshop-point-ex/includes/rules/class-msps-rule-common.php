<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class MSPS_Rule_Common extends MSPS_Rule {

	public function __construct( $product ) {
		$this->rule_type        = 'common';
		$this->rule_description = __( '모든', 'mshop-point-ex' );
		parent::__construct( $product );
	}
	function is_match( $product ) {
		if ( 'yes' == $this->exclude_sales && $product && $product->is_on_sale() ) {
			return false;
		}

		return true;
	}
}
