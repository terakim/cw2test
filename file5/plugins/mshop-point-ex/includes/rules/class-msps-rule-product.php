<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class MSPS_Rule_Product extends MSPS_Rule {
	protected $object_ids = null;

	public function __construct( $product ) {
		$this->rule_type        = 'product';
		$this->rule_description = '';
		parent::__construct( $product );
	}

	public function is_match( $product ) {
		$product_id = apply_filters( 'wpml_object_id', msps_get_product_id( $product ), 'product', true, mshop_wpml_get_default_language() );

		$product = wc_get_product( $product_id );
		if ( 'yes' == $this->exclude_sales && $product && $product->is_on_sale() ) {
			return false;
		}

		return array_key_exists( $product_id, $this->object );
	}
}