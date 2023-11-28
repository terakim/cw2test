<?php
	global $wpdb;
	$rtwwwap_new_url = admin_url( 'user-new.php' );
	if( RTWWWAP_IS_WOO == 1 ){
		$rtwwwap_currency_sym = esc_html( get_woocommerce_currency_symbol() );
	}
	else{
		require_once( RTWWWAP_DIR.'includes/rtwaffiliatehelper.php' );
	
		$rtwwwap_currency		= isset( $rtwwwap_extra_features[ 'currency' ] ) ? $rtwwwap_extra_features[ 'currency' ] : 'USD';
		$rtwwwap_curr_obj 		= new RtwAffiliateHelper();
		$rtwwwap_currency_sym 	= $rtwwwap_curr_obj->rtwwwap_curr_symbol( $rtwwwap_currency );
	
	}


	$rtwwwap_args = array(
						'meta_key' 		=> 'rtwwwap_affiliate',
						'meta_value' 	=> '1',
						'orderby' 		=> 'id',
						'order' 		=> 'desc'
					);
    
	$rtwwwap_users = get_users( $rtwwwap_args );

	$rtwwwap_withdrawal_all_request = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."rtwwwap_wallet_transaction ORDER BY `id` DESC", ARRAY_A);

	$rtwwwap_levels_settings 		= get_option( 'rtwwwap_levels_settings_opt' );
	$rtwwwap_commission_settings 	= get_option( 'rtwwwap_commission_settings_opt' );
	$rtwwwap_comm_base 				= isset( $rtwwwap_commission_settings[ 'comm_base' ] ) ? $rtwwwap_commission_settings[ 'comm_base' ] : '1';

?>

<p class="rtwwwap_add_new_affiliate">
	<a href="<?php echo esc_url( $rtwwwap_new_url ); ?>" target="_blank">
		<input type="button" value="<?php esc_attr_e( 'Add New Affiliate', 'rtwwwap-wp-wc-affiliate-program' ); ?>" class="rtwwwap-button" name="rtwwwap_add_new_affiliate" />
	</a>
	<?php 
		$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );
		if( isset($rtwwwap_extra_features[ 'aff_verify' ]) && $rtwwwap_extra_features[ 'aff_verify' ] == 1 ){
	?>
		<input type="button" value="<?php esc_attr_e( 'Approve all marked Affiliates', 'rtwwwap-wp-wc-affiliate-program' ); ?>" class="rtwwwap-button rtwwwap_approve_all_affiliate" name="rtwwwap_approve_all_affiliate" />
	<?php } ?>
</p>

<div class="main-wrapper">
	<div class="rtwwwap-data-table-wrapper">
		<table class="rtwwwap_affiliates_table rtwwwap_data_table stripe" class="display dtr-inline" cellspacing="0">
		  	<thead>
			  	<tr>
			    	<th>
			    		<span class="rtwwwap-checkbox">
			    			<input class="rtwwwap_affiliate_check_all" id="rtwwwap_checkbox-th" type="checkbox" name=""/>
			    			<label for="rtwwwap_checkbox-th"></label>
			    		</span>
			    	</th>
			    	<th><?php esc_html_e( 'ID', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Username', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Name', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<?php if( $rtwwwap_comm_base == 2 && !empty( $rtwwwap_levels_settings ) ){ ?>
			    		<th><?php esc_html_e( 'Level', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<?php } ?>
			    	<th><?php esc_html_e( 'Email', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Phone', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>

			    	<th><?php esc_html_e( 'Parent Id', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Parent Name', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'MLM Earning', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>

					<th><?php esc_html_e( 'Paid Amount', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
					<th><?php esc_html_e( 'Unpaid Amount', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
					<th><?php esc_html_e( 'WP Role', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>

			    	<th><?php esc_html_e( 'Actions', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			  	</tr>
		  	</thead>
		  	<tbody>
		  		<?php
				  if(!empty($rtwwwap_users))
				  {
		  			foreach( $rtwwwap_users as $rtwwwap_key => $rtwwwap_user ){
						 
		  		?>
					  	<tr data-referral_id="<?php echo esc_attr( $rtwwwap_user->ID ); ?>">
					    	<td>
					    		<?php 
									  $rtwwwap_aff_is_approved = get_user_meta( $rtwwwap_user->ID, 'rtwwwap_aff_approved', true );
									
						  			if( isset( $rtwwwap_extra_features[ 'aff_verify' ] ) && $rtwwwap_extra_features[ 'aff_verify' ] == 1 && !$rtwwwap_aff_is_approved ){
						  		?>
							    		<span class="rtwwwap-checkbox">
							    			<input id="rtwwwap_checkbox_<?php echo esc_attr( $rtwwwap_key ); ?>" type="checkbox" name=""/>
							    			<label for="rtwwwap_checkbox_<?php echo esc_attr( $rtwwwap_key ); ?>"></label>
							    		</span>
						    	<?php } ?>
					    	</td>
					    	<td>
					    		<?php echo esc_html( $rtwwwap_user->ID ); ?>
					    	</td>

					    	<td>
					    		<?php echo esc_html( $rtwwwap_user->data->user_login ); ?>
					    	</td>

					    	<td>
					    		<?php echo esc_html( $rtwwwap_user->data->display_name ); ?>
					    	</td>
							
							<?php 
								if( $rtwwwap_comm_base == 2 ){ 
									if( !empty( $rtwwwap_levels_settings ) ){
							?>
							    	<td>
							    		<?php
							    			$rtwwwap_user_level = get_user_meta( $rtwwwap_user->ID, 'rtwwwap_affiliate_level', true );
							    			if( $rtwwwap_user_level ){
							    				echo esc_html( $rtwwwap_levels_settings[ $rtwwwap_user_level ][ 'level_name' ] );
							    			}
							    			else{
							    				echo esc_html( $rtwwwap_levels_settings[0][ 'level_name' ] );
							    			}
							    		?>
							    	</td>
					    	<?php 
					    			} 
					    		}
					    	?>

					    	<td>
					    		<?php echo esc_html( $rtwwwap_user->data->user_email ); ?>
					    	</td>
							<td>
							<?php 
								$rtwwwap_affiliate_phone = get_user_meta($rtwwwap_user->ID,'billing_phone',true);
								if($rtwwwap_affiliate_phone)
								{
									echo esc_html($rtwwwap_affiliate_phone, 'rtwwwap-wp-wc-affiliate-program' );
								}
								else
								{
									echo esc_html('Not mentioned', 'rtwwwap-wp-wc-affiliate-program' );
								}
							?>
							</td>
							<td>
								<?php
									global $wpdb;
							;
									$rtwwwap_parent_id = $wpdb->get_var($wpdb->prepare( "SELECT `parent_id` FROM ".$wpdb->prefix."rtwwwap_mlm WHERE `aff_id` = '%d' ", $rtwwwap_user->ID )) ;

									echo esc_attr($rtwwwap_parent_id);

 								?>
					    	</td>
							<td>
							<?php 
								
								$rtwwwap_parent_id = $wpdb->get_var($wpdb->prepare( "SELECT `parent_id` FROM ".$wpdb->prefix."rtwwwap_mlm WHERE `aff_id` = '%d' ", $rtwwwap_user->ID )) ;
								
								
								$rtwwwap_parent_name = $wpdb->get_var($wpdb->prepare( "SELECT `user_login` FROM ".$wpdb->prefix."users WHERE `ID` = '%d' ", $rtwwwap_parent_id  )) ;	

								echo ($rtwwwap_parent_name);

							?>
					    	</td>

							<td>
							<?php 
								
								$rtwwwap_mlm_commission = $wpdb->get_var($wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `aff_id` = '%d' AND `type` = %d ", $rtwwwap_user->ID,4 )) ;
	
								if($rtwwwap_mlm_commission != "")
									echo ($rtwwwap_mlm_commission.$rtwwwap_currency_sym);

							?>
					    	</td>

							<td>
							<?php 
								$rtwwwap_paid_amount = (float)'';
								foreach($rtwwwap_withdrawal_all_request  as $rtwwwap_key => $rtwwwap_val)
								{	
									if($rtwwwap_val['pay_status'] == 'paid' && $rtwwwap_val['aff_id'] == $rtwwwap_user->ID )
									{
										$rtwwwap_paid_amount += (float)$rtwwwap_val['amount'];
									}
								}
								echo $rtwwwap_currency_sym.$rtwwwap_paid_amount;
							?>
					    	</td>

							<td>
							<?php 
								$rtwwwap_unpaid_amount = (float)'';
								foreach($rtwwwap_withdrawal_all_request  as $rtwwwap_key => $rtwwwap_val)
								{
									if($rtwwwap_val['pay_status'] == 'pending' && $rtwwwap_val['aff_id'] == $rtwwwap_user->ID )
									{
										//echo $val['amount'].'('.$val['pay_status'].')';
										$rtwwwap_unpaid_amount += (float)$rtwwwap_val['amount'];
									}
								}
								echo $rtwwwap_currency_sym.$rtwwwap_unpaid_amount;
							?>
					    	</td>

							 <td>
							<?php 
								
								echo $rtwwwap_user->roles['0'];

							?>
					    	</td>
						
					    	<td>
					    		<a class="rtwwwap-edit-link" href="<?php echo esc_url( get_edit_user_link( $rtwwwap_user->ID ) ); ?>" target="_blank">
					    			<span class="dashicons dashicons-edit"></span>
					    		</a>
					    		<?php 
						  			$rtwwwap_aff_is_approved = get_user_meta( $rtwwwap_user->ID, 'rtwwwap_aff_approved', true );
						  			if( isset($rtwwwap_extra_features[ 'aff_verify' ]) && $rtwwwap_extra_features[ 'aff_verify' ] == 1 ){
						  		?>
							    		<a class="rtwwwap-add-link" href="javascript:void(0);">
							    			<span class="dashicons dashicons-yes <?php echo ( $rtwwwap_aff_is_approved ) ? esc_attr( 'rtwwwap_aff_approved' ) : esc_attr( 'rtwwwap_aff_approve' ); ?>"></span>
							    		</a>
						    	<?php } ?>
					    	</td>
					  	</tr>
				<?php }
				  }
				  else 
				  {
					  ?>
						<tr>
							<td colspan="11">
							<?php esc_html_e( 'No Data Found', 'rtwwwap-wp-wc-affiliate-program' ); ?>
							</td>
						</tr>
					  <?php
				  }
				?>
			</tbody>
			<tfoot>
			  	<tr>
			    	<th></th>
			    	<th><?php esc_html_e( 'ID', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Username', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Name', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<?php if( $rtwwwap_comm_base == 2 && !empty( $rtwwwap_levels_settings ) ){ ?>
			    		<th><?php esc_html_e( 'Level', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<?php } ?>
			    	<th><?php esc_html_e( 'Email', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Phone', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
					<th><?php esc_html_e( 'Parent Id', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Parent Name', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'MLM Earning', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>

					<th><?php esc_html_e( 'Paid Amount', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Unpaid Amount', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
					 <th><?php esc_html_e( 'WP Role', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>

			    	<th><?php esc_html_e( 'Actions', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			  	</tr>
		  	</tfoot>
		</table>
    </div>
    <?php include_once( RTWWWAP_DIR . '/admin/partials/rtwwwap_tabs/rtwwwap_footer.php' ); ?>
</div>