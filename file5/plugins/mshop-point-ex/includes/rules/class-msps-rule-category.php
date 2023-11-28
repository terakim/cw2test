<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class MSPS_Rule_Category extends MSPS_Rule {

	public function __construct( $product ) {
		$this->rule_type        = 'category';
		$this->rule_description = __( '카테고리', 'mshop-point-ex' );
		parent::__construct( $product );
	}
	public function is_match( $product ) {
		$p_id = msps_get_product_id( $product );

		$product_id = apply_filters( 'wpml_object_id', $p_id, 'product', true, mshop_wpml_get_default_language() );

		$product = wc_get_product( $product_id );
		if ( 'yes' == $this->exclude_sales && $product && $product->is_on_sale() ) {
			return false;
		}

		$terms      = get_the_terms( $product_id, 'product_cat' );

		if ( ! empty( $terms ) ) {
			$term_ids = array();

			foreach ( $terms as $term ) {
				$term_id  = apply_filters( 'wpml_object_id', $term->term_id, 'product_cat', true, mshop_wpml_get_default_language() );
				$term_ids = array_merge( $term_ids, array( $term_id ), get_ancestors( $term_id, 'product_cat' ) );
			}

			$term_ids = array_flip( $term_ids );
			$result   = array_intersect_key( $term_ids, $this->object );

			return ! empty( $result );
		}

		return false;
	}
}
