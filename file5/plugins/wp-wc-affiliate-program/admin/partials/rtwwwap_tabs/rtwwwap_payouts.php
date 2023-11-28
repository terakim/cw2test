<?php
	global $wpdb;
	$rtwwwap_all_referrals 	= $wpdb->get_results( $wpdb->prepare( "SELECT `aff_id`, COUNT(`order_id`) as `no_of_referrals`, SUM( `amount` ) as `amount`, `currency`, MAX(`date`) as `date`, `status` FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `status` = %d AND `capped` != %d AND `type` != %d GROUP BY `aff_id` ORDER BY `aff_id` DESC", 1, 1, 3 ), ARRAY_A );

	$rtwwwap_withdrawal_request = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."rtwwwap_wallet_transaction WHERE `pay_status`= 'pending' ORDER BY `id` DESC", ARRAY_A);
	$rtwwwap_withdrawal_all_request = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."rtwwwap_wallet_transaction ORDER BY `id` DESC", ARRAY_A);


	if( RTWWWAP_IS_WOO == 1 ){
		$rtwwwap_currency_sym = get_woocommerce_currency_symbol();
	}
	else{
		require_once( RTWWWAP_DIR.'includes/rtwaffiliatehelper.php' );

		$rtwwwap_currency 		= isset( $rtwwwap_extra_features[ 'currency' ] ) ? $rtwwwap_extra_features[ 'currency' ] : 'USD';
		$rtwwwap_curr_obj 		= new RtwAffiliateHelper();
		$rtwwwap_currency_sym 	= $rtwwwap_curr_obj->rtwwwap_curr_symbol( $rtwwwap_currency );
	}


	$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );
	$rtwwwap_commission_setting = get_option( 'rtwwwap_commission_settings_opt' );


	$rtwwwap_decimal_place = isset($rtwwwap_extra_features['decimal_places']) ? $rtwwwap_extra_features['decimal_places'] : "2" ;
	$rtwwwap_decimal_separator = isset($rtwwwap_extra_features['decimal_separator']) ? $rtwwwap_extra_features['decimal_separator'] : ".";
	$rtwwwap_thousand_separator = isset($rtwwwap_extra_features['thousand__separator']) ? $rtwwwap_extra_features['thousand__separator'] : ",";

?>
	<p class="rtwwwap_add_new_affiliate">
		<input type="button" value="<?php esc_attr_e( 'Pay marked PayPal Affiliates', 'rtwwwap-wp-wc-affiliate-program' ); ?>" class="rtwwwap-button rtwwwap_all_paypal_affiliate" name="rtwwwap_all_paypal_affiliate" />

		<input type="button" value="<?php esc_attr_e( 'Pay marked Stripe Affiliates', 'rtwwwap-wp-wc-affiliate-program' ); ?>" class="rtwwwap-button rtwwwap_all_stripe_affiliate" name="rtwwwap_all_stripe_affiliate" />
	</p>


<div class="main-wrapper">
	<div id="dialogForm">
	</div>
	<div class="rtwwwap_payment_tabs_wrapper">
		<div class="rtwwwap_payout_sub_div"><button class="rtwwwap_payout_sub_div_btn rtwwwap_payment_tab_active" data-tab=".rtwwwap_withdrawal_request"><?php esc_attr_e( 'Withdrawal Request', 'rtwwwap-wp-wc-affiliate-program' ); ?></button></div>
		<div class="rtwwwap_payout_sub_div"><button class="rtwwwap_payout_sub_div_btn" data-tab=".rtwwwap_full_settlement"><?php esc_attr_e( 'All Transaction', 'rtwwwap-wp-wc-affiliate-program' ); ?></button></div>
	</div>

	<?php
		if(in_array('addon_for_MLM_qualification/affiliate_mlm_addon.php', apply_filters('active_plugins', get_option('active_plugins') ) ) ){
			do_action('rtwwwap_add_payout_content');
		}
	?>
	
	<!-- withdrawal Request -->
	<div class="rtwwwap-data-table-wrapper rtwwwap_withdrawal_request rtwwwap_payment_tab_content rtwwwap_payment_tab_content_active">
		<table class="rtwwwap_payout_table rtwwwap_data_table stripe display dtr-inline" cellspacing="0">
		  	<thead>
			  	<tr>
			    	<th>
			    		<span class="rtwwwap-checkbox">
			    			<input id="rtwwwap_checkbox-th" class="rtwwwap_pay_check_all" type="checkbox" name=""/>
			    			<label for="rtwwwap_checkbox-th"></label>
			    		</span>
			    	</th>
			    	<th><?php esc_html_e( 'Affiliate ID 	', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Affiliate', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Amount', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Status', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Request Date', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Action', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			  	</tr>
		  	</thead>
		  	<tbody>
		  		<?php
			  		$rtwwwap_date_format = get_option( 'date_format' );
					$rtwwwap_time_format = get_option( 'time_format' );

		  			foreach( $rtwwwap_withdrawal_request as $rtwwwap_key => $rtwwwap_value ){

		  				$rtwwwap_aff_info 		= get_userdata( $rtwwwap_value[ 'aff_id' ] );
						$rtwwwap_aff_name 		= ( $rtwwwap_aff_info ) ? $rtwwwap_aff_info->user_login : '';
						$rtwwwap_payment_type 	= get_user_meta( $rtwwwap_value[ 'aff_id' ], 'rtwwwap_payment_method', true );
						$rtwwwap_payment_details = '';
						if( $rtwwwap_payment_type == 'rtwwwap_payment_direct' ){
							$rtwwwap_payment_details = get_user_meta( $rtwwwap_value[ 'aff_id' ], 'rtwwwap_direct', true );
						}
		  		?>
					  	<tr data-bank_details="<?php echo esc_attr($rtwwwap_payment_details); ?>" data-transaction_id =<?php echo esc_attr($rtwwwap_value[ 'id' ]);?> >
					    	<td>
					    		<?php
					    			if( $rtwwwap_payment_type == 'rtwwwap_payment_paypal' )
					    			{
					    		?>
							    		<span class="rtwwwap-checkbox">
							    			<input id="rtwwwap_checkbox_<?php echo esc_attr( $rtwwwap_key ); ?>" type="checkbox" name="" data-rtwwwap_pay_method="paypal"/>
							    			<label for="rtwwwap_checkbox_<?php echo esc_attr( $rtwwwap_key ); ?>"></label>
							    		</span>
						    	<?php
						    		}
						    		elseif( $rtwwwap_payment_type == 'rtwwwap_payment_stripe' )
						    		{
						    	?>
							    		<span class="rtwwwap-checkbox">
							    			<input id="rtwwwap_checkbox_<?php echo esc_attr( $rtwwwap_key ); ?>" type="checkbox" name="" data-rtwwwap_pay_method="stripe"/>
							    			<label for="rtwwwap_checkbox_<?php echo esc_attr( $rtwwwap_key ); ?>"></label>
							    		</span>
						    	<?php
						    		}
						    	?>
					    	</td>
					    	<td class="rtwwwap_aff_id" data-aff_id="<?php echo esc_attr( $rtwwwap_value[ 'aff_id' ] ); ?>">
					    		<a href="<?php echo esc_url( get_edit_user_link( $rtwwwap_value[ 'aff_id' ] ) ); ?>"><?php echo esc_html( $rtwwwap_value[ 'aff_id' ] ); ?></a>
					    	</td>
					    	<td>
					    		<a href="<?php echo esc_url( get_edit_user_link( $rtwwwap_value[ 'aff_id' ] ) ); ?>"><?php echo esc_html( $rtwwwap_aff_name ); ?></a>
					    	</td>
					    				    	
					    	<td class="rtwwwap_amount" data-amount="<?php echo esc_attr( $rtwwwap_value[ 'amount' ] ); ?>" data-currency=<?php echo get_woocommerce_currency();?>>
					    		<?php
					    			
					    			echo esc_html( $rtwwwap_currency_sym.number_format( $rtwwwap_value[ 'amount' ] ,$rtwwwap_decimal_place,$rtwwwap_decimal_separator, $rtwwwap_thousand_separator ) ); ?>
					    	</td>
							<td>
								<?php echo esc_attr( $rtwwwap_value[ 'pay_status' ] ); ?>
							</td>
					    	<td>
					    		<?php
									$rtwwwap_date_time_format = $rtwwwap_date_format.' '.$rtwwwap_time_format;
									$rtwwwap_local_date = get_date_from_gmt( date( 'Y-m-d H:i:s', strtotime(  $rtwwwap_value[ 'request_date' ] ) ), $rtwwwap_date_time_format );
								?>
					    		<?php echo esc_html( $rtwwwap_local_date ); ?>
					    	</td>
					    	<td>
					    		<span class="rtwwwap_pay_reject">
					    			<?php
					    			
										$rtwwwap_admin_paypal 	= isset( $rtwwwap_extra_features[ 'activate_paypal' ] ) ? $rtwwwap_extra_features[ 'activate_paypal' ] : 0;
										$rtwwwap_admin_stripe 	= isset( $rtwwwap_extra_features[ 'activate_stripe' ] ) ? $rtwwwap_extra_features[ 'activate_stripe' ] : 0;
										$rtwwwap_admin_paystack 	= isset( $rtwwwap_extra_features[ 'activate_paystack' ] ) ? $rtwwwap_extra_features[ 'activate_paystack' ] : 0;


					    				$rtwwwap_payment_class 	= '';
					    				$rtwwwap_payment_name 	= '';

					    				if( $rtwwwap_admin_paypal && $rtwwwap_payment_type && $rtwwwap_payment_type == 'rtwwwap_payment_paypal' ){
					    					$rtwwwap_payment_class 	= 'rtwwwap_payment_paypal';
					    					$rtwwwap_payment_name 	= esc_html__( 'Paypal', 'rtwwwap-wp-wc-affiliate-program' );
					    				}
					    				elseif( $rtwwwap_admin_stripe && $rtwwwap_payment_type && $rtwwwap_payment_type == 'rtwwwap_payment_stripe' ){
					    					$rtwwwap_payment_class 	= 'rtwwwap_payment_stripe';
					    					$rtwwwap_payment_name 	= esc_html__( 'Stripe', 'rtwwwap-wp-wc-affiliate-program' );
					    				}
					    				elseif( $rtwwwap_payment_type && $rtwwwap_payment_type == 'rtwwwap_payment_direct' ){
					    					$rtwwwap_payment_class 	= 'rtwwwap_payment_direct';
					    					$rtwwwap_payment_name 	= esc_html__( 'Bank Details', 'rtwwwap-wp-wc-affiliate-program' );
										}
										elseif( $rtwwwap_payment_type && $rtwwwap_payment_type == 'rtwwwap_payment_paystack' ){
					    					$rtwwwap_payment_class 	= 'rtwwwap_payment_paystack';
					    					$rtwwwap_payment_name 	= esc_html__( 'Paystack', 'rtwwwap-wp-wc-affiliate-program' );
					    				}
					    			?>
					    			<?php
					    				if( $rtwwwap_payment_class != '' && $rtwwwap_payment_name != '' ){
					    			?>
							    			<span class="rtwwwap_payment_type <?php echo esc_attr( $rtwwwap_payment_class ); ?>" >
							    				<?php echo esc_html( $rtwwwap_payment_name ); ?>
							    			</span>
							    			<span class="rtwwwap_paid <?php echo esc_attr( $rtwwwap_payment_class ); ?>">
							    				<?php esc_html_e( 'Pay', 'rtwwwap-wp-wc-affiliate-program' ); ?>
							    			</span>
						    		<?php
						    			}
						    			else{
						    		?>
						    				<span class="rtwwwap_no_payment" data-rtwwwap_pay_type="<?php echo esc_attr( $rtwwwap_payment_class ); ?>">
							    				<?php esc_html_e( 'No payment method defined', 'rtwwwap-wp-wc-affiliate-program' ); ?>
							    			</span>
						    		<?php
						    			}
						    		?>
					    		</span>
					    	</td>
					  	</tr>
				<?php } ?>
			</tbody>
			<tfoot>
			  	<tr>
			    	<th></th>
					<th><?php esc_html_e( 'Affiliate ID 	', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Affiliate', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Amount', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Status', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Request Date', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Action', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			  	</tr>
		  	</tfoot>
		</table>
    </div>

<!-- Full sattlement -->


<div class="rtwwwap-data-table-wrapper rtwwwap_full_settlement rtwwwap_payment_tab_content">
		<table class="withdrawal_all_request rtwwwap_data_table stripe display dtr-inline" cellspacing="0">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Transaction ID', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
					<th><?php esc_html_e( 'Affiliate ID 	', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Affiliate Name', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Amount', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Status', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Request Date', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			  	</tr>
			</thead>

			<tbody>
				<?php 
					foreach($rtwwwap_withdrawal_all_request as $key => $value)
					{
						$rtwwwap_aff_info 		= get_userdata( $value[ 'aff_id' ] );
						$rtwwwap_aff_name 		= ( $rtwwwap_aff_info ) ? $rtwwwap_aff_info->user_login : '';
				?>
					<tr>
						<td><?php echo esc_attr($value['id']);?></td>
						<td><?php echo esc_attr($value['aff_id']);?></td>
						<td><?php echo esc_attr($rtwwwap_aff_name);?></td>
						<td><?php echo esc_attr($value['amount']);?></td>
						<?php if($value['pay_status'] == 'pending')
						{ ?>
							<td><span class="rtwwwap_status_pending"><?php echo esc_attr($value['pay_status']);?></span></td>
						<?php 
						}?>
						<?php if($value['pay_status'] == 'paid')
						{ ?>
							<td><span class="rtwwwap_status_paid"><?php echo esc_attr($value['pay_status']);?></span></td>
						<?php 
						}?>

						<td><?php echo esc_attr(date( 'Y-m-d H:i:s', strtotime(  $value[ 'request_date' ] )));?></td>
					</tr>
				<?php 
				}?>
			</tbody>
			<tfoot>
			  	<tr>
				 	<th><?php esc_html_e( 'Transaction ID', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
					<th><?php esc_html_e( 'Affiliate ID 	', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Affiliate Name', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Amount', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Status', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Request Date', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			  	</tr>
		  	</tfoot>
		</table>
    </div>
    <?php include_once( RTWWWAP_DIR . '/admin/partials/rtwwwap_tabs/rtwwwap_footer.php' ); ?>
</div>
