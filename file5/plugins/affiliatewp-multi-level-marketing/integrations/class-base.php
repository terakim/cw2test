<?php

class AffiliateWP_MLM_Base {

	/**
	 * The context for referrals. This refers to the integration that is being used.
	 *
	 * @access  public
	 * @since   1.0
	 */
	public $context;

	/**
	 * The ID of the referring affiliate
	 *
	 * @access  public
	 * @since   1.0
	 */
	public $affiliate_id;
	
	/**
	 * The IDs of the upline parent affiliates
	 *
	 * @access  public
	 * @since   1.1.5
	 */
	public $upline;	
	
	public function __construct() {
	
		$affiliate_id = affiliate_wp()->tracking->get_affiliate_id();
		$reference = '';
		$this->affiliate_id = absint( apply_filters( 'affwp_get_referring_affiliate_id', $affiliate_id, $reference, $this->context ) );
		$this->init();
	}

	/**
	 * Gets things started
	 *
	 * @access  public
	 * @since   1.0
	 * @return  void
	 */
	public function init() {

		add_filter( 'affwp_was_referred', array( $this, 'force_default_referral' ), 10, 2 );
		add_filter( 'affwp_get_referring_affiliate_id', array( $this, 'get_default_affiliate_id' ), 99, 3 );
		add_action( 'affwp_updated_referral', array( $this, 'updated_referral_status' ), 10, 3 );
		
	}

	/**
	 *  Force referrals on non-referred sales
	 *
	 * @access  public
	 * @since   1.1.5
	 */	
	function force_default_referral( $was_referred, $tracking ) {
	
		// Stop if a referral was detected
		if ( $was_referred ) return $was_referred;
		
		$force_default_referral = affiliate_wp()->settings->get( 'affwp_mlm_force_default_referral' );
		
		if ( $force_default_referral ) $was_referred = true;
		
		return $was_referred;
	}
	
	/**
	 * Set the referring affiliate as the customer's parent affiliate
	 *
	 * @access  public
	 * @since   1.1.5
	 */	
	public function get_default_affiliate_id( $affiliate_id, $reference, $context ) {
		
		// Apply to integrations only
		if ( $this->context !== $context ) return $affiliate_id;
		
		$force_default_referral = affiliate_wp()->settings->get( 'affwp_mlm_force_default_referral' );
		
		if ( ! $force_default_referral ) return $affiliate_id;		
		
		$referral = affiliate_wp()->referrals->get_by( 'reference', $reference, $this->context );
		
		// Stop if this is an indirect referral
		if ( $referral->custom == 'indirect' ) return $affiliate_id;
		
		$user_id = get_current_user_id();
		
		// Stop if the customer is not an affiliate
		if ( ! affwp_is_affiliate( $user_id ) ) return $affiliate_id;		
		
		// The affiliate id of the current logged in customer
		$customer_affiliate_id = affwp_get_affiliate_id( $user_id );
		$parent_affiliate_id = affwp_mlm_get_parent_affiliate( $customer_affiliate_id );
		
		// Stop if they have no parent affiliate
		if ( empty( $parent_affiliate_id ) ) return $affiliate_id;

		return $parent_affiliate_id;

	}	
	
	/**
	 * Label direct referrals via custom referral data
	 *
	 * @access  public
	 * @since   1.1
	 */
	public function prepare_direct_referral( $data ) {

		$data['custom'] = maybe_unserialize( $data['custom'] );
		
		// Prevent overwriting subscription id or existing referral type
		if ( empty( $data['custom'] ) )
			$data['custom'] = 'direct'; // Add referral type as custom referral data for direct referral
		
		return $data;

	}
	
	/**
	 * Determines if indirect referrals should be created and generates the upline.
	 *
	 * @access  public
	 * @since   1.1
	 */
	public function prepare_indirect_referrals( $referral_id, $data ) {
	
		// Check for the integration
		if ( ( $this->context !== $data['context'] ) ) return;

		$affiliate_id = $data['affiliate_id'];
		$data['custom'] = maybe_unserialize( $data['custom'] );
		$referral = affiliate_wp()->referrals->get_by( 'referral_id', $referral_id, $this->context );
		$referral_type = 'direct';

		if ( empty( $referral->custom ) ) {
			
			// Prevent overwriting subscription id
			if ( empty( $data['custom'] ) ) {
				
				// Add referral type as custom referral data for direct referral
				affiliate_wp()->referrals->update( $referral->referral_id, array( 'custom' => $referral_type ), '', 'referral' );
			
			}
		
		} elseif ( $referral->custom == 'indirect' ) {
			return; // Prevent looping through indirect referrals
		}
		
		// Save affiliate from the direct referral as the customer's direct affiliate
		$this->save_customer_direct_affiliate( $referral );
		
		$upline_max = apply_filters( 'affwp_mlm_indirect_level_max', 0, $affiliate_id );
		$upline_basis = affiliate_wp()->settings->get( 'affwp_mlm_upline_basis' );
		
		// Get the affiliate's upline
		$upline = affwp_mlm_get_upline( $affiliate_id, $upline_max, $upline_basis );
		
		if ( $upline ) {
			
			// Filter upline by the default active status (Basic compression)
			$active_upline = affwp_mlm_filter_by_status( $upline );
			
			// Filter upline to allow custom compression
			$parent_affiliates = apply_filters( 'affwp_mlm_indirect_referral_upline', $active_upline, $referral_id, $data, $affiliate_id, $this->context );
			
			$this->upline = $parent_affiliates; // Store upline data for later
			
			$level_count = 0;
			
			foreach( $parent_affiliates as $parent_affiliate_id ) {
				
				$level_count++;

				// Create the parent affiliate's referral
				$this->create_parent_referral( $parent_affiliate_id, $referral_id, $data, $level_count, $affiliate_id );
			
			}
		
		}
	
	}

	/**
	 * Save the ID of the Referring Affiliate in Customer's User Meta. (For Delayed Affiliate Connections)
	 *
	 * @access  public
	 * @since   1.1.4
	 */
	public function save_customer_direct_affiliate( $referral, $override = true ) {
		
		if ( empty( $referral ) || ! is_object( $referral ) ) return;
		
		if ( ! isset( $referral->customer_id ) ) return;
		
		$customer_id = $referral->customer_id;
		$customer = affwp_get_customer( $customer_id );
		
		if ( ! $customer ) return; // Ensure customer exists

		if ( ! isset( $customer->user_id ) ) return; // Ensure user exists in customer data
		
		$user_id = $customer->user_id;
		
		// Keep existing direct affiliate if override is false
		if ( ! $override ) {
			
			$direct_affiliate = get_user_meta( $user_id, '_affwp_mlm_direct_affiliate_id', true );
			
			if ( ! empty( $direct_affiliate ) ) return;
			
		}
		
		// Store affiliate in customer's user meta
		update_user_meta( $user_id, '_affwp_mlm_direct_affiliate_id', $referral->affiliate_id );
		
	}

	/**
	 * Completes a referral. Used when orders are marked as completed
	 *
	 * @access  public
	 * @since   1.0
	 * @param   $reference The reference column for the referral to complete per the current context
	 * @return  bool
	 */
	public function complete_referral( $referral, $reference ) {
		
		if ( empty( $reference ) ) return false;
		
		if ( ! $referral ) {
		
			$referral = affiliate_wp()->referrals->get_by( 'reference', $reference, $this->context );
		}

		if ( empty( $referral ) ) return false;

		// This referral has already been completed or paid
		if ( is_object( $referral ) && $referral->status != 'pending' && $referral->status != 'rejected' ) return false;

		if ( ! apply_filters( 'affwp_auto_complete_referral', true ) ) return false;

		if ( affwp_set_referral_status( $referral->referral_id, 'unpaid' ) ) {

			do_action( 'affwp_complete_referral', $referral->referral_id, $referral, $reference );
			
			do_action( 'affwp_mlm_complete_referral', $referral->referral_id, $referral, $reference );

			return true;
		}

		return false;

	}

	/**
	 * Rejects a referal. Used when orders are refunded, deleted, or voided
	 *
	 * @since   1.0
	 * @return  bool
	 */
	public function reject_referral( $referral, $reject_pending = false ) {

		if ( empty( $referral ) ) return false;

		// If referral has already been paid it cannot be rejected
		if ( is_object( $referral ) && 'paid' == $referral->status ) return false;
		
		// This referral is pending so it cannot be rejected
		if ( is_object( $referral ) && 'pending' === $referral->status && false === $reject_pending ) return false;	

		if ( affwp_set_referral_status( $referral->referral_id, 'rejected' ) ) {

			return true;

		}

		return false;

	}

	/**
	 * Updates the status of Indirect Referrals if the Direct Referral's status was updated.
	 *
	 * @since   1.0
	 */	
	public function updated_referral_status( $updated_referral, $referral, $updated ) {

		if ( $updated && ! empty( $updated_referral->reference ) ) {
			
			// Prevent endless loop for indirect referrals
			if ( $updated_referral->custom == 'indirect' ) return;
			
			// Ensure referral status was changed
			if ( $updated_referral->status != $referral->status ) {
				
				$complete = ( $updated_referral->status == 'unpaid' ) ? true : false;
				$reject = ( $updated_referral->status == 'rejected' ) ? true : false;

				$refs = affwp_mlm_get_referrals_for_order( $updated_referral->reference, $updated_referral->context );

				if ( empty( $refs ) ) return;

				foreach ( $refs as $ref ) {

					if ( $complete ) $this->complete_referral( $ref, $ref->reference );
					
					if ( $reject ) {
						
						$reject_pending = ( $ref->context == 'woocommerce' ) ? true : false;
						
						$this->reject_referral( $ref, $reject_pending );
						
					}

				}
				
			}

		}

	}	
	
	/**
	 * Calculate referral amount
	 *
	 * @since 1.0
	 */
	public function calculate_referral_amount( $parent_affiliate_id = 0, $base_amount = '', $reference = 0, $product_id = 0, $level_count = 0 ) {

		$rate = '';
		$type = '';

		$rate = $this->get_parent_rate( $parent_affiliate_id, $product_id, $level_count, $args = array( 'reference' => $reference ) );
		$type = $this->get_parent_rate_type( $parent_affiliate_id, $product_id, $args = array( 'reference' => $reference ) );

		if ( 'percentage' == $type ) {
			// Sanitize the rate and ensure it's in the proper format
			if ( $rate > 0 ) {
				$rate = $rate / 100;
			}
		}

		$amount = $this->calc_parent_referral_amount( $base_amount, $parent_affiliate_id, $reference, $rate, $product_id, $type, $level_count );

		return $amount;

	}

	/**
	 * Get the Rates for each Level
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function get_level_rates() {
		$rates = affiliate_wp()->settings->get( 'mlm_rates', array() );
		
		// Match the level count by offseting array values to start from 1
		array_unshift( $rates, '' );
		
		return apply_filters( 'affwp_mlm_level_rates', array_values( $rates ) );
	}

	/**
	 * Get the Per-Product Rate for each Level
	 *
	 * @access public
	 * @since 1.1.3
	 * @return array
	 */
	public function get_product_level_rates( $product_id = 0 ) {
		
		if ( $this->context == 'woocommerce' ) {
			$affwp_mlm_int = new AffiliateWP_MLM_WooCommerce;	
		} else {
			return array();
		}
		
		$rates = $affwp_mlm_int->get_int_product_level_rates( $product_id );
		$rates = is_array( $rates ) ? array_values( $rates ) : array();		
		
		// Match the level count by offseting array values to start from 1
		array_unshift( $rates, '' );
		
		return apply_filters( 'affwp_mlm_product_level_rates', $rates );
	}	
	
	/**
	 * Get the Per-Product Rate Type for Indirect Referrals
	 *
	 * @access public
	 * @since 1.1.4
	 * @return array
	 */
	public function get_product_level_rate_type( $product_id = 0 ) {
		
		if ( $this->context == 'woocommerce' ) {
			$affwp_mlm_int = new AffiliateWP_MLM_WooCommerce;	
		} else {
			return array();
		}
		
		$rate_type = $affwp_mlm_int->get_int_product_level_rate_type( $product_id );
		
		return apply_filters( 'affwp_mlm_product_level_rate_type', $rate_type );
	}		
	
	/**
	 * Get parent rate while tracking sub-affiliate
	 *
	 * @since 1.0
	 */
	public function get_parent_rate( $parent_affiliate_id = 0, $product_id = 0, $level_count = 0, $args = array() ) {

		$rate = 0; // The Default Indirect Rate
		$product_rates = $this->get_product_level_rates( $product_id );
		$rates = $this->get_level_rates();
		
		// 1. The per-affiliate setting in Affiliates -> Affiliates -> Edit
		// $affiliate_level_rate = affiliate_wp()->affiliates->get_column( 'rate', $parent_affiliate_id );
		
		// 2. The per product setting for per level rates
		$product_rate = isset( $product_rates[ $level_count ]['rate'] ) ? $product_rates[ $level_count ]['rate'] : 0;
		
		// 3. The global per level setting in Affiliates -> Settings -> MLM
		$level_rate = isset( $rates[ $level_count ]['rate'] ) ? $rates[ $level_count ]['rate'] : 0;
		
		// 4. The global setting for all levels in Affiliates -> Settings -> MLM
		$mlm_rate = affiliate_wp()->settings->get( 'affwp_mlm_referral_rate' );

		// $rate = empty( $affiliate_level_rate ) ? $level_rate : $affiliate_level_rate;
		
		if ( isset( $product_rates[$level_count] ) ) {	

			// Use default rate if no per-level product rate is set
			$rate = ! empty( $product_rate ) ? $product_rate : $rate;		
		}
		
		if ( isset( $rates[$level_count] ) ) {	

			// Use per-level rate if no per-level product rate is set
			$rate = empty( $product_rate ) && ! empty( $level_rate ) ? $level_rate : $rate;
		}		
		
		if ( isset( $mlm_rate ) ) {

			// Use the global indirect rate if no per-level product rate or per-level rate is set
			$rate = empty( $product_rate ) && empty( $level_rate ) && ! empty( $mlm_rate ) ? $mlm_rate : $rate;
		}
		
		$reference = isset( $args['reference'] ) ? $args['reference'] : '';
		$type = $this->get_parent_rate_type( $parent_affiliate_id, $product_id, $args = array( 'reference' => $reference ) );
		
		return apply_filters( 'affwp_mlm_get_affiliate_rate', (float) $rate, $product_id, $args, $this->affiliate_id, $this->context, $parent_affiliate_id, $level_count, $this->upline );
	} 

	/**
	 * Get parent rate type
	 *
	 * @since 1.0
	 */
	public function get_parent_rate_type( $parent_affiliate_id = 0, $product_id = 0, $args = array() ) {

		// 1. The per product setting for per level rates
		$product_rate_type = $this->get_product_level_rate_type( $product_id );		

		// 2. The global setting in Affiliates -> Settings -> MLM
		$mlm_rate_type = affiliate_wp()->settings->get( 'affwp_mlm_referral_rate_type' );
		
		/* Per Affiliate Level Rates
		if( empty( $affiliate_level_rate_type ) ) {
			$type = $mlm_rate_type;
		} else{
			$type = $affiliate_level_rate_type;
		}
		*/
		
		// Use global indirect rate type if no per-product rate type is set
		$type = empty( $product_rate_type ) ? $mlm_rate_type : $product_rate_type;
		
		return apply_filters( 'affwp_mlm_get_affiliate_rate_type', (string) $type, $product_id, $args, $this->affiliate_id, $this->context, $parent_affiliate_id );

	}

	/**
	 * Calculate parent referral amount
	 *
	 * @since 1.0
	 */
	public function calc_parent_referral_amount( $amount = '', $parent_affiliate_id = 0, $reference = 0, $rate = '', $product_id = 0, $type = '', $level_count = 0 ) {
		
		if ( empty( $rate ) ) {
		
			// 3. The global fallback setting in Affiliates -> Settings -> MLM	
			$rate = affiliate_wp()->settings->get( 'affwp_mlm_referral_rate' );
			
			// 3. The global fallback setting in Affiliates -> Settings -> General	
			//$rate = affiliate_wp()->settings->get( 'referral_rate', 20 );
			
		}

		if ( empty( $type ) ) {
		
			// 3. The global fallback setting in Affiliates -> Settings -> General
			$type = affiliate_wp()->settings->get( 'referral_rate_type', 'percentage' );
			
		}
	
		if ( affwp_is_per_order_rate( $parent_affiliate_id ) ) {
			
			$referral_amount = apply_filters( 'affwp_mlm_calc_per_order_referral_amount', $rate, $parent_affiliate_id, $reference, $rate, $product_id, $type, $level_count, $this->context );
			
		} else {

			$decimals = function_exists( 'affwp_get_decimal_count' ) ? affwp_get_decimal_count() : 2;
			$referral_amount = ( 'percentage' === $type ) ? round( $amount * $rate, $decimals ) : $rate;			
			
		}
		
		if ( $referral_amount < 0 ) $referral_amount = 0;
		
		return apply_filters( 'affwp_mlm_calc_referral_amount', (string) $referral_amount, $amount, $parent_affiliate_id, $reference, $rate, $product_id, $type, $level_count );

	}
	
	/**
	 * Get the dollar amount sold by the sub affiliates of a given parent affiliate
	 *
	 * @since 1.1.6
	 * @return int
	 */
	public function get_team_amount_sold( $affiliate_id = 0 ) {

		if ( empty( $affiliate_id ) ) return;
		
		$context = $this->context;
		$amount = 0.00;
		
		if ( ! function_exists( 'affiliatewp_performance_bonuses' ) ) return;
		
		$affwp_pb = affiliatewp_performance_bonuses();
		$affwp_pb_int = $affwp_pb->integrations;
		$affwp_pb_int->context = $context;	
				
		if ( ! method_exists( $affwp_pb_int, 'get_affiliate_amount_sold' ) ) return; 		
		
		$sub_affiliate_ids = affwp_mlm_get_downline_array( $affiliate_id );

		if ( $sub_affiliate_ids ) {
			foreach ( $sub_affiliate_ids as $sub_id ) {
				$amount += $affwp_pb_int->get_affiliate_amount_sold( $sub_id, $context );
			}
		}

		return $amount;
	}	
	
}