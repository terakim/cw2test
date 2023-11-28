<?php

class AffiliateWP_MLM_WooCommerce extends AffiliateWP_MLM_Base {

	/**
	 * The order object
	 *
	 * @access  public
	 * @since   1.0
	*/
	public $order;

	/**
	 * Get things started
	 *
	 * @access public
	 * @since  1.0
	*/
	public function init() {

		$this->context = 'woocommerce';
		
		/* Check for WooCommerce */
		$integrations = affiliate_wp()->settings->get( 'affwp_mlm_integrations' );
				
		if ( ! isset( $integrations['woocommerce'] ) ) return; // MLM integration for WooCommerce is disabled 

		// Per-Product MLM Settings
		add_filter( 'woocommerce_product_data_tabs', array( $this, 'product_tab' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'product_settings' ), 100 );
		add_action( 'save_post', array( $this, 'save_meta' ) );		
		
		add_action( 'woocommerce_order_status_completed', array( $this, 'mark_referrals_complete' ), 5 );
		add_action( 'woocommerce_order_status_processing', array( $this, 'mark_referrals_complete' ), 5 );

		// Handle order updates/cancellations
		add_action( 'woocommerce_order_status_completed_to_refunded', array( $this, 'revoke_referrals_on_refund' ), 10 );
		add_action( 'woocommerce_order_status_on-hold_to_refunded', array( $this, 'revoke_referrals_on_refund' ), 10 );
		add_action( 'woocommerce_order_status_processing_to_refunded', array( $this, 'revoke_referrals_on_refund' ), 10 );
		add_action( 'woocommerce_order_status_processing_to_cancelled', array( $this, 'revoke_referrals' ), 10 );
		add_action( 'woocommerce_order_status_completed_to_cancelled', array( $this, 'revoke_referrals' ), 10 );
		add_action( 'woocommerce_order_status_pending_to_cancelled', array( $this, 'revoke_referrals' ), 10 );
		add_action( 'woocommerce_order_status_processing_to_failed', array( $this, 'revoke_referrals' ), 10 );
		add_action( 'woocommerce_order_status_completed_to_failed', array( $this, 'revoke_referrals' ), 10 );
		add_action( 'woocommerce_order_status_pending_to_failed', array( $this, 'revoke_referrals' ), 10 );
		add_action( 'woocommerce_order_status_on-hold_to_cancelled', array( $this, 'revoke_referrals' ), 10 );
		add_action( 'wc-on-hold_to_trash', array( $this, 'revoke_referrals' ), 10 );
		add_action( 'wc-processing_to_trash', array( $this, 'revoke_referrals' ), 10 );
		add_action( 'wc-completed_to_trash', array( $this, 'revoke_referrals' ), 10 );

		// Process referral
		add_action( 'affwp_post_insert_referral', array( $this, 'process_referral' ), 10, 2 );

		// Team Amount Sold Bonus Check
		add_action( 'affwp_pb_bonus_check_on_paid_referral', array( $this, 'check_for_team_amount_sold_bonus' ), 10, 1 );
		add_action( 'affwp_pb_bonus_check_on_unpaid_referral', array( $this, 'check_for_team_amount_sold_bonus' ), 10, 1 );

		// Team Amount Sold Rank Check
		add_action( 'affwp_pb_bonus_check_on_paid_referral', array( $this, 'check_for_team_amount_sold_rank' ), 10, 1 );
		add_action( 'affwp_pb_bonus_check_on_unpaid_referral', array( $this, 'check_for_team_amount_sold_rank' ), 10, 1 );		
		
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
	 * Creates the referral for parent affiliate
	 *
	 * @since 1.0
	 */
	public function create_parent_referral( $parent_affiliate_id, $referral_id, $data, $level_count = 0, $affiliate_id ) {

		$direct_affiliate = affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id );

		// Process cart and get amount
		$amount = $this->process_cart( $parent_affiliate_id, $data, $level_count );

		$data['affiliate_id'] = $parent_affiliate_id;
		$data['description']  = $this->get_referral_description( $level_count, $direct_affiliate );
		$data['amount']       = $amount;
		$data['custom']       = 'indirect'; // Add referral type as custom referral data
		$data['context']      = 'woocommerce';

		// unset( $data['date'] );
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

			$this->order->add_order_note( sprintf( __( 'Indirect Referral #%d for %s recorded for %s', 'affiliatewp-multi-level-marketing' ), $referral_id, $amount, $name ) );

			do_action( 'affwp_mlm_indirect_referral_created', $referral_id, $data );

		}

	}

	/**
	 * Process cart
	 *
	 * @since 1.0
	 */
	public function process_cart( $parent_affiliate_id, $data, $level_count = 0  ) {

		$wc_2_2_plus = true === version_compare( WC()->version, '2.2.0', '>=' ) ? true : false;
		
		$order_id      = $data['reference'];
		$order 		   = $wc_2_2_plus ? wc_get_order( $order_id ) : new WC_Order( $order_id );
		$this->order   = apply_filters( 'affwp_get_woocommerce_order', $order );		
		$cart_shipping = $this->order->get_total_shipping();
		$items         = $this->order->get_items();

		// Calculate the referral amount based on product prices
		$amount = 0.00;
		
		foreach( $items as $product ) {

			if ( get_post_meta( $product['product_id'], '_affwp_' . $this->context . '_referrals_disabled', true ) ) {
				continue; // Referrals are disabled on this product
			}

			// The order discount has to be divided across the items
			$product_total = $product['line_total'];
			$shipping      = 0;

			if ( $cart_shipping > 0 && ! affiliate_wp()->settings->get( 'exclude_shipping' ) ) {

				$shipping       = $cart_shipping / count( $items );
				$product_total += $shipping;

			}

			if ( ! affiliate_wp()->settings->get( 'exclude_tax' ) ) {

				$product_total += $product['line_tax'];

			}

			if ( $product_total <= 0 ) {
				continue;
			}

			$amount += $this->calculate_referral_amount( $parent_affiliate_id, $product_total, $order_id, $product['product_id'], $level_count );

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
	public function mark_referrals_complete( $order_id = 0 ) {

		if ( empty( $order_id ) ) return false;
		
		$this->order = apply_filters( 'affwp_get_woocommerce_order', new WC_Order( $order_id ) );
		
		if ( true === version_compare( WC()->version, '3.0.0', '>=' ) ) {
			$payment_method = $this->order->get_payment_method();
		} else {
			$payment_method = get_post_meta( $order_id, '_payment_method', true );
		}
		
		// If the WC status is 'wc-processing' and a COD order, leave as 'pending'.
		if ( 'wc-processing' == $this->order->get_status() && 'cod' === $payment_method ) {
			return;
		}
		
		$reference = $order_id;
		$referrals = affwp_mlm_get_referrals_for_order( $reference, $this->context );
		
		if ( empty( $referrals ) ) return false;

		foreach ( $referrals as $referral ) {
		
			$this->complete_referral( $referral, $reference );
			
		}

	}

	/**
	 * Revoke all referrals for the given Order ID
	 *
	 * @since 1.1.5
	 */
	public function revoke_referrals( $order_id = 0 ) {
	
		if ( empty( $order_id ) ) return;
		
		if ( is_a( $order_id, 'WP_Post' ) ) $order_id = $order_id->ID;
		
		if ( 'shop_order' != get_post_type( $order_id ) ) return;

		$referrals = affwp_mlm_get_referrals_for_order( $order_id, $this->context );

		if ( empty( $referrals ) ) return;

		foreach ( $referrals as $referral ) {

			$this->reject_referral( $referral, true );

		}

	}	
	
	/**
	 * Revoke referrals when an order is refunded
	 *
	 * @since 1.0
	 */
	public function revoke_referrals_on_refund( $order_id = 0 ) {
	
		if ( empty( $order_id ) ) return;
		
		if ( is_a( $order_id, 'WP_Post' ) ) $order_id = $order_id->ID;
		
		if ( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) return;
		
		if ( 'shop_order' != get_post_type( $order_id ) ) return;

		$referrals = affwp_mlm_get_referrals_for_order( $order_id, $this->context );

		if ( empty( $referrals ) ) return;

		foreach ( $referrals as $referral ) {

			$this->reject_referral( $referral );

		}

	}

	/**
	 * Retrieve the WooCommerce referral description
	 *
	 * @since   1.0
	*/
	public function get_referral_description( $level_count, $direct_affiliate ) {

		$items       = $this->order->get_items();
		$description = array();
		$item_names = array();

		foreach ( $items as $key => $item ) {

			if ( get_post_meta( $item['product_id'], '_affwp_' . $this->context . '_referrals_disabled', true ) ) {
				continue; // Referrals are disabled on this product
			}

			$item_names[] = $item['name'];

		}
		
		$description[] = $direct_affiliate . ' | Level '. $level_count . ' | ' . implode( ', ', $item_names );
		
		return implode( ', ', $description );

	}

	/**
	 * Get the Per-Level Rates for a given product
	 *
	 * @access public
	 * @since 1.1.3
	 * @return array
	 */
	public function get_int_product_level_rates( $product_id = 0 ) {
		$rates = get_post_meta( $product_id, '_affwp_mlm_' . $this->context . '_product_rates', true );
		$rates = is_array( $rates ) ? array_values( $rates ) : array();
		return apply_filters( 'affwp_mlm_wc_product_level_rates', $rates );
	}
	
	/**
	 * Get the Per-Level Rate Type for a given product
	 *
	 * @access public
	 * @since 1.1.4
	 * @return array
	 */
	public function get_int_product_level_rate_type( $product_id = 0 ) {
		$rate_type = get_post_meta( $product_id, '_affwp_mlm_' . $this->context . '_product_rate_type', true );
		return apply_filters( 'affwp_mlm_wc_product_level_rate_type', $rate_type );
	}	

	/**
	 * Get the Per-Level Rates Table
	 *
	 * @access public
	 * @since 1.1.3
	 * @return void
	 */	
	public function product_level_rates_table( $product_id = 0 ) {

		$rates = $this->get_int_product_level_rates( $product_id );
		$count = count( $rates );
									
?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('.affwp_mlm_remove_product_rate').on('click', function(e) {
				e.preventDefault();
				$(this).parent().parent().remove();
			});

			$('#affwp_mlm_new_product_rate').on('click', function(e) {

				e.preventDefault();

				var row = $('#affiliatewp-mlm-product-rates tbody tr:last');

				clone = row.clone();

				var count = $('#affiliatewp-mlm-product-rates tbody tr').length;

				clone.find( 'td input' ).val( '' );
				clone.find( 'input' ).each(function() {
					var name = $( this ).attr( 'name' );

					name = name.replace( /\[(\d+)\]/, '[' + parseInt( count ) + ']');

					$( this ).attr( 'name', name ).attr( 'id', name );
				});

				clone.insertAfter( row );

			});
		});
		</script>
		<style type="text/css">
		#affiliatewp-mlm-product-rates th { padding-left: 10px; }
		.affwp_mlm_remove_product_rate { margin: 8px 0 0 0; cursor: pointer; width: 10px; height: 10px; display: inline-block; text-indent: -9999px; overflow: hidden; }
		.affwp_mlm_remove_product_rate:active, .affwp_mlm_remove_product_rate:hover { background-position: -10px 0!important }
		</style>
		<form id="affiliatewp-mlm-product-rates-form">
			<p>Per-Level Product Rates</p>
			<table id="affiliatewp-mlm-product-rates" class="form-table wp-list-table widefat fixed posts">
				<thead>
					<tr>
						<th style="width: 20%; text-align: center;"><?php _e( 'Level', 'affiliatewp-multi-level-marketing' ); ?></th>
						<th style="width: 60%; text-align: center;"><?php _e( 'Commission Rate', 'affiliatewp-multi-level-marketing' ); ?></th>
						<th style="width: 20%;"><?php _e( 'Delete', 'affiliatewp-multi-level-marketing' ); ?></th>
					</tr>
				</thead>
				<tbody>
                	<?php if( $rates ) :
							$level_count = 0; 
							
							foreach( $rates as $key => $rate ) : 
								$level_count++;
							?>
							<tr>
								<td style="font-size: 18px; text-align: center;">
									<?php 
									
										if( ! empty( $level_count ) ) {
											echo $level_count;
										} else{
											echo '0';
										}
									
									?>
								</td>
								<td>
									<input name="_affwp_mlm_woocommerce_product_rates[<?php echo $key; ?>][rate]" type="text" value="<?php echo esc_attr( $rate['rate'] ); ?>" style="width: 100%;" />
								</td>
								<td>
									<a href="#" class="affwp_mlm_remove_product_rate" style="background: url(<?php echo admin_url('/images/xit.gif'); ?>) no-repeat;">&times;</a>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr>
							<td colspan="3" style="text-align: center;"><?php _e( 'No level rates created yet', 'affiliatewp-multi-level-marketing' ); ?></td>
						</tr>
					<?php endif; ?>
                    <?php if( empty( $rates ) ) : ?>
                        <tr>
                            <td style="font-size: 18px; text-align: center;">
                                        <?php 
                                            if( ! empty( $level_count ) ) {
                                                echo $level_count;
                                            } else{
                                                echo '0';
                                            }
                                        ?>
                            </td>
                            <td>
                                <input name="_affwp_mlm_woocommerce_product_rates[<?php echo $count; ?>][rate]" type="text" value=""/>
                            </td>
                            <td>
                                <a href="#" class="affwp_mlm_remove_product_rate" style="background: url(<?php echo admin_url('/images/xit.gif'); ?>) no-repeat;">&times;</a>
                            </td>
                        </tr>
                    <?php endif; ?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="3">
							<button id="affwp_mlm_new_product_rate" name="affwp_mlm_new_product_rate" class="button" style="width: 100%; height: 110%;"><?php _e( 'Add New Rate', 'affiliatewp-multi-level-marketing' ); ?></button>
						</th>
					</tr>
				</tfoot>
			</table>
            <p style="margin-top: 10px;"><?php _e( 'Add rates from low to high', 'affiliatewp-multi-level-marketing' ); ?></p>
		</form>
<?php
	}	
	
	/**
	 * Register the product settings tab
	 *
	 * @access  public
	 * @since   1.1.3
	*/
	public function product_tab( $tabs ) {
		$tabs['affiliatewp_mlm'] = array(
			'label'  => __( 'AffiliateWP MLM', 'affiliatewp-multi-level-marketing' ),
			'target' => 'affwp_product_mlm_settings',
			'class'  => array( ),
		);
		return $tabs;
	}	
	
	/**
	 * Adds per-product mlm settings input fields
	 *
	 * @access  public
	 * @since   1.1.3
	*/
	public function product_settings() {
		global $post;
?>
		<div id="affwp_product_mlm_settings" class="panel woocommerce_options_panel">

			<div class="options_group">
				<p><?php _e( 'Setup mlm settings for this product', 'affiliatewp-multi-level-marketing' ); ?></p>
<?php	
				woocommerce_wp_select( array(
					'id'          => '_affwp_mlm_woocommerce_product_rate_type',
					'label'       => __( 'Indirect Product Rate Type', 'affiliate-wp' ),
					'options'     => array_merge( array( '' => __( 'Site Default', 'affiliate-wp' ) ),affwp_get_affiliate_rate_types() ),
					'desc_tip'    => true,
					'description' => __( 'Earnings can be based on either a percentage or a flat rate amount.', 'affiliatewp-multi-level-marketing' ),
				) );		
			
				$product_id = $post->ID;
				$this->product_level_rates_table( $product_id );
			
			/*
				woocommerce_wp_checkbox( array(
					'id'          => '_affwp_mlm_woocommerce_indirect_referrals_disabled',
					'label'       => __( 'Disable Indirect Referrals', 'affiliatewp-multi-level-marketing' ),
					'description' => __( 'This will prevent orders of this product from generating Indirect Referral commissions for affiliates.', 'affiliatewp-multi-level-marketing' ),
					'cbvalue'     => 1
				) );

			*/
				wp_nonce_field( 'affwp_mlm_woo_product_nonce', 'affwp_mlm_woo_product_nonce' );
?>
			</div>
		</div>
<?php
	}
	
	/**
	 * Saves per-product mlm settings input fields
	 *
	 * @access  public
	 * @since   1.1.3
	*/
	public function save_meta( $post_id = 0 ) {
		
		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		
		// Don't save revisions and autosaves
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return $post_id;
		}
		
		if ( empty( $_POST['affwp_mlm_woo_product_nonce'] ) || ! wp_verify_nonce( $_POST['affwp_mlm_woo_product_nonce'], 'affwp_mlm_woo_product_nonce' ) ) {
			return $post_id;
		}
		
		$post = get_post( $post_id );
		
		if ( ! $post ) {
			return $post_id;
		}
		
		// Check post type is product
		if ( 'product' != $post->post_type ) {
			return $post_id;
		}
		
		// Check user permission
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		// Save Indirect Product Rate Field
		if ( ! empty( $_POST['_affwp_mlm_' . $this->context . '_product_rate_type'] ) ) {
			$rates = $_POST['_affwp_mlm_' . $this->context . '_product_rate_type'];
			update_post_meta( $post_id, '_affwp_mlm_' . $this->context . '_product_rate_type', $rates );
		} else {
			delete_post_meta( $post_id, '_affwp_mlm_' . $this->context . '_product_rate_type' );
		}		
		
		// Save Per-Level Rates Fields
		if ( ! empty( $_POST['_affwp_mlm_' . $this->context . '_product_rates'] ) ) {
			$rates = $_POST['_affwp_mlm_' . $this->context . '_product_rates'];
			update_post_meta( $post_id, '_affwp_mlm_' . $this->context . '_product_rates', $rates );
		} else {
			delete_post_meta( $post_id, '_affwp_mlm_' . $this->context . '_product_rates' );
		}
		
		/* Save Disable Indirect Referrals Field
		if ( ! empty( $_POST['_affwp_mlm_' . $this->context . '_indirect_referrals_disabled'] ) ) {
			$disabled = $_POST['_affwp_mlm_' . $this->context . '_indirect_referrals_disabled'];
			update_post_meta( $post_id, '_affwp_mlm_' . $this->context . '_indirect_referrals_disabled', $disabled );
		} else {
			delete_post_meta( $post_id, '_affwp_mlm_' . $this->context . '_indirect_referrals_disabled' );
		}		
		*/
		
	}

	/**
	 * Check for bonuses based on team amount sold
	 * 
	 * @since 1.1.6
	 */
	public function check_for_team_amount_sold_bonus( $referral_id ) {

		// Run if AffiliateWP Performance Bonuses is Active
		if ( ! class_exists( 'AffiliateWP_Performance_Bonuses' ) ) return;		
		
		$referral = affwp_get_referral( $referral_id );

		// Stop if this is a bonus referral
		if ( $referral->custom == 'performance_bonus' ) return;

		$affiliate_id = affwp_mlm_get_parent_affiliate( $referral->affiliate_id );
		$amount_sold = $this->get_team_amount_sold( $affiliate_id );

		// Exclude bonuses that have already been earned	
		$bonuses = apply_filters( 'affwp_pb_get_active_bonuses', get_active_bonuses(), $affiliate_id );

		// Stop if all bonuses have been earned already
		if ( empty( $bonuses ) ) return;

		foreach( $bonuses as $key => $bonus ) {

			// Check for team referral bonus
			if ( $bonus['type'] == 'team_amount_sold' ) {

				// Check if the affiliate has met the requirements
				if ( $amount_sold >= $bonus['requirement'] ) {

					$bonus_earned = affwp_pb_get_bonus_log( $affiliate_id, $bonus['pre_bonus'] );

					// Check for prerequisite bonus
					if ( ! empty( $bonus['pre_bonus'] ) && empty( $bonus_earned ) ) continue;

					// Create the bonus
					affwp_pb_create_bonus_referral( $affiliate_id, $bonus['id'], $bonus['title'], $bonus['amount'] );

				}
			}
		}
	}	

	/**
	 * Check for ranks based on team amount sold
	 * 
	 * @since 1.1.6
	 */
	public function check_for_team_amount_sold_rank( $referral_id ) {

		// Run if AffiliateWP Ranks is Active
		if ( ! class_exists( 'AffiliateWP_Ranks' ) ) return;
		
		$referral = affwp_get_referral( $referral_id );
		
		// Stop if this is a bonus referral
		if ( $referral->custom == 'performance_bonus' ) return;
		
		$ranks = get_ranks();
		
		// Make sure there are ranks setup 
		if ( empty( $ranks ) ) return;
	
		$affiliate_id = affwp_mlm_get_parent_affiliate( $referral->affiliate_id );		
		$amount_sold = $this->get_team_amount_sold( $affiliate_id );
		
		foreach( $ranks as $key => $rank ) {
			
			// Skip this rank if it's the affiliate's current rank
			if ( affwp_ranks_has_rank( $affiliate_id, $rank['id'] ) ) continue;
			
			// Check for dollar amount sold based rank
			if ( $rank['type'] == 'team_amount_sold' ) {

				// Check if the affiliate has met the requirements
				if ( $amount_sold >= $rank['requirement'] ) {

					$rank_id = $rank['id'];
					
					// Set the affiliate's rank
					affwp_ranks_set_affiliate_rank( $affiliate_id, $rank_id );
				
				}
			}
		}		
	}
	


}
new AffiliateWP_MLM_WooCommerce;