<?php
	global $wpdb;
	$rtwalwm_all_referrals 	= $wpdb->get_results( $wpdb->prepare( "SELECT `aff_id`, COUNT(`order_id`) as `no_of_referrals`, SUM( `amount` ) as `amount`, `currency`, MAX(`date`) as `date`, `status` FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `status` = %d AND `capped` != %d AND `type` != %d GROUP BY `aff_id` ORDER BY `aff_id` DESC", 1, 1, 3 ), ARRAY_A );
?>

<p class="rtwalwm_add_new_affiliate">
	<input type="button" value="<?php esc_attr_e( 'Pay marked PayPal Affiliates', 'rtwalwm-wp-wc-affiliate-program' ); ?>" class="rtwalwm-button rtwalwm_all_paypal_affiliate" name="" />

	<input type="button" value="<?php esc_attr_e( 'Pay marked Stripe Affiliates', 'rtwalwm-wp-wc-affiliate-program' ); ?>" class="rtwalwm-button rtwalwm_all_stripe_affiliate" name="" />
</p>
<div class ="rtwwdpdl_pro_text_overlay">

<div class="main-wrapper">
	<div id="dialogForm">
	<div class="rtwalwm-data-table-wrapper">
		<table class="rtwalwm_payout_table rtwalwm_data_table stripe" class="display dtr-inline" cellspacing="0">
		  	<thead>
			  	<tr>
			    	<th>
			    		<span class="rtwalwm-checkbox">
			    			<input id="rtwalwm_checkbox-th" class="rtwalwm_pay_check_all" type="checkbox" name=""/>
			    			<label for="rtwalwm_checkbox-th"></label>
			    		</span>
			    	</th>
			    	<th><?php esc_html_e( 'User ID', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Affiliate', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'No. of Referrals', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Amount', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Last Referral', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Status', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			  	</tr>
		  	</thead>
		  	<tbody>
		  		<?php
			  		$rtwalwm_date_format = get_option( 'date_format' );
					$rtwalwm_time_format = get_option( 'time_format' );

		  			foreach( $rtwalwm_all_referrals as $rtwalwm_key => $rtwalwm_value ){
		  				$rtwalwm_aff_info 		= get_userdata( $rtwalwm_value[ 'aff_id' ] );
						$rtwalwm_aff_name 		= ( $rtwalwm_aff_info ) ? $rtwalwm_aff_info->user_login : '';
						$rtwalwm_payment_type 	= get_user_meta( $rtwalwm_value[ 'aff_id' ], 'rtwalwm_payment_method', true );
						$rtwalwm_payment_details = '';
						if( $rtwalwm_payment_type == 'rtwalwm_payment_direct' ){
							$rtwalwm_payment_details = get_user_meta( $rtwalwm_value[ 'aff_id' ], 'rtwalwm_direct', true );
						}
		  		?>
					  	<tr data-bank_details="<?php echo esc_attr($rtwalwm_payment_details); ?>">
					    	<td>
					    		<?php
					    			if( $rtwalwm_payment_type == 'rtwalwm_payment_paypal' )
					    			{
					    		?>
							    		<span class="rtwalwm-checkbox">
							    			<input id="rtwalwm_checkbox_<?php echo esc_attr( $rtwalwm_key ); ?>" type="checkbox" name="" data-rtwalwm_pay_method="paypal"/>
							    			<label for="rtwalwm_checkbox_<?php echo esc_attr( $rtwalwm_key ); ?>"></label>
							    		</span>
						    	<?php
						    		}
						    		elseif( $rtwalwm_payment_type == 'rtwalwm_payment_stripe' )
						    		{
						    	?>
							    		<span class="rtwalwm-checkbox">
							    			<input id="rtwalwm_checkbox_<?php echo esc_attr( $rtwalwm_key ); ?>" type="checkbox" name="" data-rtwalwm_pay_method="stripe"/>
							    			<label for="rtwalwm_checkbox_<?php echo esc_attr( $rtwalwm_key ); ?>"></label>
							    		</span>
						    	<?php
						    		}
						    	?>
					    	</td>
					    	<td class="rtwalwm_aff_id" data-aff_id="<?php echo esc_attr( $rtwalwm_value[ 'aff_id' ] ); ?>">
					    		<a href="<?php echo esc_url( get_edit_user_link( $rtwalwm_value[ 'aff_id' ] ) ); ?>"><?php echo esc_html( $rtwalwm_value[ 'aff_id' ] ); ?></a>
					    	</td>
					    	<td>
					    		<a href="<?php echo esc_url( get_edit_user_link( $rtwalwm_value[ 'aff_id' ] ) ); ?>"><?php echo esc_html( $rtwalwm_aff_name ); ?></a>
					    	</td>
					    	<td>
					    		<?php echo esc_html( $rtwalwm_value[ 'no_of_referrals' ] ); ?>
					    	</td>
					    	<?php
					    		$rtwalwm_user_wallet = get_user_meta( $rtwalwm_value[ 'aff_id' ], 'rtw_user_wallet', true );
									if(empty($rtwalwm_user_wallet))
									{
										$rtwalwm_user_wallet = 0;
									}
					    	?>
					    	<td class="rtwalwm_amount" data-amount="<?php echo esc_attr( $rtwalwm_user_wallet ); ?>" data-currency="<?php echo esc_attr( $rtwalwm_value[ 'currency' ] ); ?>">
					    		<?php
					    			if( RTWALWM_IS_WOO == 1 ){
										$rtwalwm_currency_sym = get_woocommerce_currency_symbol( $rtwalwm_value[ 'currency' ] );
										
									}
									else{
									
										$rtwalwm_currency_sym 	= esc_html__( '&#36;', 'rtwalwm-wp-wc-affiliate-program' );
									}
					    			echo esc_html( $rtwalwm_currency_sym.$rtwalwm_user_wallet ); ?>
					    	</td>
					    	<td>
					    		<?php
									$rtwalwm_date_time_format = $rtwalwm_date_format.' '.$rtwalwm_time_format;
									$rtwalwm_local_date = get_date_from_gmt( date( 'Y-m-d H:i:s', strtotime( $rtwalwm_value[ 'date' ] ) ), $rtwalwm_date_time_format );
								?>
					    		<?php echo esc_html( $rtwalwm_local_date ); ?>
					    	</td>
					    	<td>
					    		<span class="rtwalwm_pay_reject">
					    			<?php
					    				$rtwalwm_extra_features = get_option( 'rtwwwap_extra_features_opt' );
										$rtwalwm_admin_paypal 	= isset( $rtwalwm_extra_features[ 'activate_paypal' ] ) ? $rtwalwm_extra_features[ 'activate_paypal' ] : 0;
										$rtwalwm_admin_stripe 	= isset( $rtwalwm_extra_features[ 'activate_stripe' ] ) ? $rtwalwm_extra_features[ 'activate_stripe' ] : 0;

					    				$rtwalwm_payment_class 	= '';
					    				$rtwalwm_payment_name 	= '';

					    				if( $rtwalwm_admin_paypal && $rtwalwm_payment_type && $rtwalwm_payment_type == 'rtwalwm_payment_paypal' ){
					    					$rtwalwm_payment_class 	= 'rtwalwm_payment_paypal';
					    					$rtwalwm_payment_name 	= esc_html__( 'Paypal', 'rtwalwm-wp-wc-affiliate-program' );
					    				}
					    				elseif( $rtwalwm_admin_stripe && $rtwalwm_payment_type && $rtwalwm_payment_type == 'rtwalwm_payment_stripe' ){
					    					$rtwalwm_payment_class 	= 'rtwalwm_payment_stripe';
					    					$rtwalwm_payment_name 	= esc_html__( 'Stripe', 'rtwalwm-wp-wc-affiliate-program' );
					    				}
					    				elseif( $rtwalwm_payment_type && $rtwalwm_payment_type == 'rtwalwm_payment_direct' ){
					    					$rtwalwm_payment_class 	= 'rtwalwm_payment_direct';
					    					$rtwalwm_payment_name 	= esc_html__( 'Bank Details', 'rtwalwm-wp-wc-affiliate-program' );
					    				}
					    			?>
					    			<?php
					    				if( $rtwalwm_payment_class != '' && $rtwalwm_payment_name != '' ){
					    			?>
							    			<span class="rtwalwm_payment_type <?php echo esc_attr( $rtwalwm_payment_class ); ?>"  >
							    				<?php echo esc_html( $rtwalwm_payment_name ); ?>
							    			</span>
							    			<span class="rtwalwm_paid" >
							    				<?php esc_html_e( 'Pay', 'rtwalwm-wp-wc-affiliate-program' ); ?>
							    			</span>
						    		<?php
						    			}
						    			else{
						    		?>
						    				<span class="rtwalwm_no_payment" data-rtwalwm_pay_type="<?php echo esc_attr( $rtwalwm_payment_class ); ?>">
							    				<?php esc_html_e( 'No payment method defined', 'rtwalwm-wp-wc-affiliate-program' ); ?>
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
			    	<th><?php esc_html_e( 'User ID', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Affiliate', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'No. of Referrals', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Amount', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Last Referral', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Status', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			  	</tr>
		  	</tfoot>
		</table>
	</div>
</div>
</div>
    <?php include_once( RTWALWM_DIR . '/admin/partials/rtwalwm_tabs/rtwalwm_footer.php' ); ?>
</div>
