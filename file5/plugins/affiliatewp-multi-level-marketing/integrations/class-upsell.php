<?php

class AffiliateWP_MLM_Upsell extends AffiliateWP_MLM_Base {

	/**
	 * The order object
	 *
	 * @access  private
	 * @since   1.1.6
	*/
	public $order;

	/**
	 * Get things started
	 *
	 * @access public
	 * @since  1.0
	*/
	public function init() {

		$this->context = 'upsell';
		
		/* Check for Paid Memberships Pro */
		$integrations = affiliate_wp()->settings->get( 'affwp_mlm_integrations' );
				
		if ( ! isset( $integrations['upsell'] ) ) return; // MLM integration for Upsell is disabled 
		
		add_action( 'upsell_order_entity_created', array( $this, 'store_order_data' ), -1 );	
			
		add_action( 'upsell_order_status_completed', array( $this, 'mark_referrals_complete' ), 10 );
				
		add_action( 'upsell_order_status_completed_to_refunded', array( $this, 'revoke_referrals_on_refund' ), 10 );
		add_action( 'upsell_order_status_completed_to_cancelled', array( $this, 'revoke_referrals' ), 10 );
		add_action( 'upsell_order_status_pending_to_cancelled', array( $this, 'revoke_referrals' ), 10 );
		add_action( 'upsell_order_status_completed_to_failed', array( $this, 'revoke_referrals' ), 10 );
		add_action( 'upsell_order_status_pending_to_failed', array( $this, 'revoke_referrals' ), 10 );
		
		// Process referral
		add_action( 'affwp_post_insert_referral', array( $this, 'process_referral' ), 10, 2 );

	}

	/**
	 * Store data for the order
	 *
	 * @since 1.1.6
	 */
	public function store_order_data( $order ) {

		$this->order = $this->resolve_order( $order );
	
	}	
	
	/**
	 * Process referral
	 *
	 * @since 1.1.6
	 */
	public function process_referral( $referral_id, $data ) {
		
		$this->prepare_indirect_referrals( $referral_id, $data );

	}

	/**
	 * Creates the referral for the parent affiliate
	 *
	 * @since 1.1.6
	 */
	public function create_parent_referral( $parent_affiliate_id, $referral_id, $data, $level_count = 0, $affiliate_id ) {

		$direct_affiliate = affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id );

		// Process order and get amount
		$amount = $this->process_order( $parent_affiliate_id, $data, $level_count );

		$data['affiliate_id'] = $parent_affiliate_id;
		$data['description']  = $this->get_referral_description( $level_count, $direct_affiliate );
		$data['amount']       = $amount;
		$data['custom']       = 'indirect'; // Add referral type as custom referral data
		$data['context']      = 'upsell';

		unset( $data['date'] );
		unset( $data['currency'] );
		unset( $data['status'] );

		if ( ! (bool) apply_filters( 'affwp_mlm_create_indirect_referral', true, $data, $parent_affiliate_id, $affiliate_id, $referral_id, $level_count ) ) {
			return false; // Allow extensions to prevent indirect referrals from being created
		}
		
		// create referral
		$referral_id = affiliate_wp()->referrals->add( apply_filters( 'affwp_mlm_insert_pending_referral', $data, $parent_affiliate_id, $affiliate_id, $referral_id, $level_count ) );

		if ( $referral_id ) {

			$amount = affwp_currency_filter( affwp_format_amount( $amount ) );
			$name   = affiliate_wp()->affiliates->get_affiliate_name( $parent_affiliate_id );
			$referral_link  = affwp_admin_link(
				'referrals', 
				esc_html( '#' . $referral_id ), 
				array(
					'action'      => 'edit_referral', 
					'referral_id' => $referral_id
				)
			);

			$note = Note::create([
				'comment_post_ID' => $this->order->id,
				'comment_content' => sprintf(
					__( 'Indirect Referral %1$s for %2$s recorded for %3$s (ID: %4$d).', 'affiliatewp-multi-level-marketing'),
					$referral_link,
					$amount,
					$name,
					$affiliate_id
				),
			]);

			do_action( 'affwp_mlm_indirect_referral_created', $referral_id, $data );

		}

	}

	/**
	 * Process the order
	 *
	 * @since 1.1.6
	 */
	public function process_order( $parent_affiliate_id, $data, $level_count = 0 ) {

		$order = $this->order; 
		
		if ( affwp_is_per_order_rate( $parent_affiliate_id ) ) {
			
			$amount = $this->calculate_referral_amount( $parent_affiliate_id, '', $order->id, 0, $level_count );
			
		} else {
			
			// Calculate the referral amount based on product prices
			$amount = 0.00;

			foreach ( $order->items() as $item ) {
				
				if ( get_post_meta( $item['id'], '_affwp_' . $this->context . '_referrals_disabled', true ) ) {
					continue; // Referrals are disabled on this product
				}

				// The order discount has to be divided across the items
				$total = $item['total'] - $item['tax_total'];

				if ( ! affiliate_wp()->settings->get( 'exclude_tax' ) ) $total += $item['tax_total'];

				if ( $total <= 0 && affwp_get_affiliate_rate_type( $affiliate_id ) !== 'flat') continue;

				$product_id = $item['id'];
				
				$amount += $this->calculate_referral_amount( $parent_affiliate_id, $total, $order->id, $product_id, $level_count );
			}
		}

		if ( 0 == $amount && affiliate_wp()->settings->get( 'ignore_zero_referrals' ) ) {
			return false; // Ignore a zero amount referral
		}

		return $amount;

	}

	/**
	 * Mark referrals as complete
	 *
	 * @since 1.0
	 */
	public function mark_referrals_complete( $order ) {
		
		if ( $order = $this->resolve_order( $order ) ) {
			
			$reference = $order->id;
			$referrals = affwp_mlm_get_referrals_for_order( $reference, $this->context );

			if ( empty( $referrals ) ) return false;

			foreach ( $referrals as $referral ) {

				$this->complete_referral( $referral, $reference );

			}			
		
		}

	}

	/**
	 * Revoke referrals when an order is cancelled or fails
	 *
	 * @since 1.0
	 */
	public function revoke_referrals( $order ) {

		if ( $order = $this->resolve_order( $order ) ) {
			
			$reference = $order->id;
			$referrals = affwp_mlm_get_referrals_for_order( $reference, $this->context );

			if ( empty( $referrals ) ) return false;

			foreach ( $referrals as $referral ) {

				$this->reject_referral( $referral, true );

			}			
			
		}

	}	
	
	/**
	 * Revoke referrals when an order is refunded
	 *
	 * @since 1.0
	 */
	public function revoke_referrals_on_refund( $order ) {

		if ( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) return;

		$reference = $order->id;
		$referrals = affwp_mlm_get_referrals_for_order( $reference, $this->context );

		if ( empty( $referrals ) ) return false;

		foreach ( $referrals as $referral ) {

			$this->reject_referral( $referral );

		}

	}
	
	/**
	 * Retrieve the Upsell referral description
	 *
	 * @since   1.1.6
	*/
	public function get_referral_description( $level_count, $direct_affiliate ) {

		$order = $this->order;

		if ( empty( $order ) ) return null;

		$items       = $order->items();
		$description = array();
		$item_names = array();

		foreach ( $items as $index => $item ) {

			if ( get_post_meta( $item['product_id'], '_affwp_' . $this->context . '_referrals_disabled', true ) ) {
				continue; // Referrals are disabled on this product
			}

			$item_names[] = $item['name'];

		}
		
		$description[] = $direct_affiliate . ' | Level '. $level_count . ' | ' . implode( ', ', $item_names );
		
		return implode( ', ', $description );

	}	
	
	/**
	 * Resolve Upsell Order
	 *
	 * @since 1.1.6
	 * @param  mixed                  $order
	 * @return \Upsell\Entities\Order $order
	 */
	public function resolve_order( $order ) {
		if ( is_numeric( $order ) || $order instanceof \WP_Post ) {
			$order = Order::find( $order );
		}

		return $order;
	}	

}
new AffiliateWP_MLM_Upsell;