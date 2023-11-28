<?php

class AffiliateWP_MLM_LifterLMS extends AffiliateWP_MLM_Base {

	/**
	 * The order object
	 *
	 * @access  private
	 * @since   1.1.4
	*/
	private $order;
	
	/**
	 * Is LLMS version 3.x or greater?
	 *
	 * @access  private
	 * @since   1.1.4
	*/	
	public $is_llms_3x;

	/**
	 * Get things started
	 *
	 * @access public
	 * @since  1.1.4
	*/
	public function init() {

		$this->context = 'lifterlms';
		
		/* Check for Lifter LMS */
		$integrations = affiliate_wp()->settings->get( 'affwp_mlm_integrations' );
				
		if ( ! isset( $integrations['lifterlms'] ) ) return; // MLM integration for LifterLMS is disabled 

		if ( function_exists( 'LLMS' ) ) {

			if ( version_compare( LLMS()->version, '3.0.0', '>=' ) ) { // 3.x

				// Save the order data for LLMS 3.x
				add_action( 'lifterlms_new_pending_order', array( $this, 'get_order_data' ), 10, 1 );				
				
			} else { // 2.x

				

			}		
		
		// Process referral
		add_action( 'affwp_post_insert_referral', array( $this, 'process_referral' ), 10, 2 );			
			
		}
		
		add_action( 'pmpro_updated_order', array( $this, 'mark_referrals_complete' ), 10 );
		add_action( 'admin_init', array( $this, 'revoke_referrals_on_refund_and_cancel' ), 10);
		add_action( 'pmpro_delete_order', array( $this, 'revoke_referrals_on_delete' ), 10, 2 );

	}

	/**
	 * Check if the LLMS Version is 3.x or higher
	 *
	 * @since 1.1.4
	 */
	public function is_llms_3x() {

		if ( isset( $this->is_llms_3x ) ) return $this->is_llms_3x;
		
		if ( function_exists( 'LLMS' ) ) return version_compare( LLMS()->version, '3.0.0', '>=' );
		
	}	
	
	/**
	 * Store the order data for LLMS 3.x
	 *
	 * @since 1.1.4
	 */
	public function get_order_data( $order ) {

		// Store the data for later
		$this->order = $order;
		$this->is_llms_3x = true;
		
	}	
	
	/**
	 * Process referral
	 *
	 * @since 1.1
	 */
	public function process_referral( $referral_id, $data ) {
		
		$this->prepare_indirect_referrals( $referral_id, $data );

	}

	/**
	 * Creates the referral for the parent affiliate
	 *
	 * @since 1.0
	 */
	public function create_parent_referral( $parent_affiliate_id, $referral_id, $data, $level_count = 0, $affiliate_id ) {

		$direct_affiliate = affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id );

		// Get amount
		$amount = $this->process_order( $parent_affiliate_id, $data, $level_count );
		$product = $data['description'];

		$data['affiliate_id'] = $parent_affiliate_id;
		$data['description']  = $direct_affiliate . ' | Level '. $level_count . ' | ' . $product;
		$data['amount']       = $amount;
		$data['custom']       = 'indirect'; // Add referral type as custom referral data
		$data['context']      = 'lifterlms';

		unset( $data['date'] );
		unset( $data['currency'] );
		unset( $data['status'] );

		if ( ! (bool) apply_filters( 'affwp_mlm_create_indirect_referral', true, $data, $parent_affiliate_id, $affiliate_id, $referral_id, $level_count ) ) {
			return false; // Allow extensions to prevent indirect referrals from being created
		}

		// Create referral
		$referral_id = affiliate_wp()->referrals->add( apply_filters( 'affwp_mlm_insert_pending_referral', $data, $parent_affiliate_id, $affiliate_id, $referral_id, $level_count ) );

		if ( $referral_id ) {
			
			// Add order note on LLMS 3.x
			if ( $this->is_llms_3x() ) {
				
				$note = sprintf( __( 'Indirect referral #%d created successfully.', 'affiliatewp-multi-level-marketing' ), $referral_id );

				$order->add_note( $note );				
				
			}
			
			do_action( 'affwp_mlm_indirect_referral_created', $referral_id, $data );

		}
	}

	/**
	 * Process order
	 *
	 * @since 1.0
	 */
	public function process_order( $parent_affiliate_id, $data, $level_count = 0 ) {
		
		$order_id = $data['reference'];
		
		// Get order object by order id
		$order = $this->get_order( $order_id );
		apply_filters( 'affwp_get_lifterlms_order', $order );
		
		$product_id = ( $this->is_llms_3x() ) ? $order->get( 'product_id' ) : $order->product_id;
		$reference = $order_id;
		$amount = ( $this->is_llms_3x() ) ? $order->get( 'total' ) : $order->total;
		
		if ( get_post_meta( $product_id, '_affwp_disable_referrals', true ) ) {
			return; // Referrals are disabled for this product
		}		
			
		$referral_total = $this->calculate_referral_amount( $parent_affiliate_id, $amount, $reference, $product_id, $level_count );

		if ( 0 == $referral_total && affiliate_wp()->settings->get( 'ignore_zero_referrals' ) ) {
			return false; // Ignore a zero amount referral
		}
		
		return $referral_total;
		
	}

	/**
	 * Retrieves order details for an order by ID.
	 *
	 * @access private
	 * @since  1.1.4
	 *
	 * @param int  $order_id LifterLMS Order ID.
	 * @param bool $force    Whether to force skipping the cached data.
	 * @return mixed Object of order-related data, or false if no order is found.
	 */
	private function get_order( $order_id, $force = false ) {

		// Only perform lookups once, unless forced.
		if ( ! $this->order || $force ) {

			$post = get_post( $order_id );

			if ( ! $post ) return false;

			$order = new stdClass();

			$order->id = absint( $order_id );

			// WP Post
			$order->post = $post;

			// payment
			$order->payment_type = get_post_meta( $order->id, '_llms_payment_type', true );
			$order->total = get_post_meta( $order->id, '_llms_order_total', true );

			// user related
			$order->user_id = get_post_meta( $order->id , '_llms_user_id', true );
			$order->user_data = get_userdata( $order->user_id );

			// product related
			$order->product_id = get_post_meta( $order->id , '_llms_order_product_id', true );
			$order->product_title = get_post_meta( $order->id, '_llms_order_product_title', true );

			// "cache"
			$this->order = $order;

		}

		return $this->order;

	}
	
	
	/* TO-DO: FINISH BELOW -------------------------------------------- */
	
	
	
	
	
	/**
	 * Mark referrals as complete
	 *
	 * @since 1.0
	 */
	public function mark_referrals_complete( $order ) {

		if ( 'success' !== strtolower( $order->status ) ) {
			return;
		}
		
		$reference = $order->id;
		$referrals = affwp_mlm_get_referrals_for_order( $reference, $this->context );

		if ( empty( $referrals ) ) {
			return false;
		}

		foreach ( $referrals as $referral ) {
		
			$this->complete_referral( $referral, $reference );
			
			$order = new MemberOrder( $order->id );
			
			// Prevent infinite loop
			remove_action( 'pmpro_updated_order', array( $this, 'mark_referrals_complete' ), 10 );

			$amount              = html_entity_decode( affwp_currency_filter( affwp_format_amount( $referral->amount ) ), ENT_QUOTES, 'UTF-8' );
			$name                = affiliate_wp()->affiliates->get_affiliate_name( $referral->affiliate_id );
			$note                = sprintf( __( 'Indirect Referral #%d for %s recorded for %s', 'affiliatewp-multi-level-marketing' ), $referral->referral_id, $amount, $name );
			
			if ( empty( $order->notes ) ) {
				$order->notes = $note;
			} else {
				$order->notes = $order->notes . "\n\n" . $note;
			}
			$order->saveOrder();
			
		}
	}

	/**
	 * Revoke referrals when an order is refunded
	 *
	 * @since 1.0
	 */
	public function revoke_referrals_on_refund_and_cancel() {
		/*
		 * PMP does not have hooks for when an order is refunded or voided, so we detect the form submission manually
		 */

		if( ! isset( $_REQUEST['save'] ) ) {
			return;
		}

		if( ! isset( $_REQUEST['order'] ) ) {
			return;
		}

		if( ! isset( $_REQUEST['status'] ) ) {
			return;
		}

		if( ! isset( $_REQUEST['membership_id'] ) ) {
			return;
		}

		if( 'refunded' != $_REQUEST['status'] ) {
			return;
		}

		if( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}

		$reference = absint( $_REQUEST['order'] );
		$referrals = affwp_mlm_get_referrals_for_order( $reference, $this->context );

		if ( empty( $referrals ) ) {
			return false;
		}

		foreach ( $referrals as $referral ) {

			$this->reject_referral( $referral );

		}

	}

	/**
	 * Revoke referrals when an order is deleted
	 *
	 * @since 1.0
	 */
	public function revoke_referrals_on_delete( $order_id = 0, $order ) {

		if( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}
		
		$reference = $order->id;
		$referrals = affwp_mlm_get_referrals_for_order( $reference, $this->context );

		if ( empty( $referrals ) ) {
			return false;
		}

		foreach ( $referrals as $referral ) {

			$this->reject_referral( $referral );

		}

	}

}

if ( function_exists( 'LLMS' ) ) {
	new AffiliateWP_MLM_LifterLMS;
}