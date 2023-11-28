<?php
	global $wpdb;
	$rtwwwap_all_referrals 	= $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `type` != 3 ORDER BY `id` DESC", ARRAY_A );
	$rtwwwap_decimal = get_option("rtwwwap_extra_features_opt");
	$rtwwwap_decimal_place = isset($rtwwwap_decimal['decimal_places']) && !empty($rtwwwap_decimal['decimal_places']) ? $rtwwwap_decimal['decimal_places'] : "2" ;
	$rtwwwap_decimal_separator = isset($rtwwwap_decimal['decimal_separator']) && !empty($rtwwwap_decimal['decimal_separator']) ? $rtwwwap_decimal['decimal_separator'] : ".";
	$rtwwwap_thousand_separator = isset($rtwwwap_decimal['thousand__separator']) && !empty($rtwwwap_decimal['thousand__separator']) ? $rtwwwap_decimal['thousand__separator'] : ",";
	

?>

<p class="rtwwwap_add_new_affiliate">
	<input type="button" value="<?php esc_attr_e( 'Approve all marked Referrals', 'rtwwwap-wp-wc-affiliate-program' ); ?>" class="rtwwwap-button rtwwwap_approve_all_referrals" name="rtwwwap_approve_all_referrals" />

	<input type="button" value="<?php esc_attr_e( 'Reject all marked Referrals', 'rtwwwap-wp-wc-affiliate-program' ); ?>" class="rtwwwap-button rtwwwap_reject_all_referrals" name="rtwwwap_reject_all_referrals" />
  <button type="button" name="button" class="rtwwwap-button rtwwwap-manual-referral"><?php esc_html_e( 'Add Manual Referral', 'rtwwwap-wp-wc-affiliate-program' ); ?></button>
</p>

<div class="main-wrapper">
	<div class="rtwwwap-data-table-wrapper">
		<table class="rtwwwap_referral_table rtwwwap_data_table stripe" class="display dtr-inline" cellspacing="0">
		  	<thead>
			  	<tr>
			    	<th>
			    		<span class="rtwwwap-checkbox">
			    			<input class="rtwwwap_referrals_check_all" id="rtwwwap_checkbox_th" type="checkbox" name=""/>
			    			<label for="rtwwwap_checkbox_th"></label>
			    		</span>
			    	</th>
			    	<th><?php esc_html_e( 'User ID', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Affiliate', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
					<th><?php esc_html_e( 'Reference', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
					<?php if(RTWWWAP_IS_WOO == 1 ) 
								{ 
							?>
			        				<th><?php esc_html_e( 'Order Status', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
					<?php
								} 
							?>
					<th><?php esc_html_e( 'Amount', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Date', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Status', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Actions', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			  	</tr>
		  	</thead>
		  	<tbody>
		  		<?php
			  		$rtwwwap_date_format = get_option( 'date_format' );
					$rtwwwap_time_format = get_option( 'time_format' );

		  			foreach( $rtwwwap_all_referrals as $rtwwwap_key => $rtwwwap_value ){

		  				$rtwwwap_aff_info = get_userdata( $rtwwwap_value[ 'aff_id' ] );
						$rtwwwap_aff_name = ( $rtwwwap_aff_info ) ? $rtwwwap_aff_info->user_login : '';
		  		?>
					  	<tr data-referral_id="<?php echo esc_attr( $rtwwwap_value[ 'id' ] ); ?>" class="" >
					    	<td>
					    	<?php
					    		if( $rtwwwap_value[ 'status' ] == 0 && $rtwwwap_value[ 'capped' ] == 0 ){
					    	?>
						    		<span class="rtwwwap-checkbox">
						    			<input id="rtwwwap_checkbox_<?php echo esc_attr( $rtwwwap_key ); ?>" type="checkbox" name=""/>
						    			<label for="rtwwwap_checkbox_<?php echo esc_attr( $rtwwwap_key ); ?>"></label>
						    		</span>
						    <?php
								}
					    	?>
					    	</td>
					    	<td>
					    		<a target="_blank" href="<?php echo esc_url( get_edit_user_link( $rtwwwap_value[ 'aff_id' ] ) ); ?>"><?php echo esc_html( $rtwwwap_value[ 'aff_id' ] ); ?></a>
					    	</td>
					    	<td>
					    		<a target="_blank" href="<?php echo esc_url( get_edit_user_link( $rtwwwap_value[ 'aff_id' ] ) ); ?>"><?php echo esc_html( $rtwwwap_aff_name ); ?></a>
					    	</td>
					    	<td>
					    	<?php
					    		if( $rtwwwap_value[ 'order_id' ] == 0 ){
					    			if( $rtwwwap_value[ 'type' ] == 1 ){
					    				echo esc_html__( 'Signup Bonus', 'rtwwwap-wp-wc-affiliate-program' ).' (';
					    	?>
					    				<a target="_blank" href="<?php echo esc_url( get_edit_user_link( $rtwwwap_value[ 'signed_up_id' ] ) ); ?>"><?php echo esc_html( $rtwwwap_value[ 'signed_up_id' ] ); ?></a>
					    	<?php
					    				echo esc_html( ' )' );
					    			}

									if( $rtwwwap_value[ 'type' ] == 7 ){
					    				echo esc_html__( 'CPC bonus', 'rtwwwap-wp-wc-affiliate-program' );
					    			}
									elseif( $rtwwwap_value[ 'type' ] == 15 ){
										echo esc_html__( 'Rank bonus', 'rtwwwap-wp-wc-affiliate-program' );
									}

					    			elseif( $rtwwwap_value[ 'type' ] == 2 ){
					    				esc_html_e( 'Performance Bonus', 'rtwwwap-wp-wc-affiliate-program' );
					    			}
										elseif( $rtwwwap_value[ 'type' ] == 6 ){
					    				esc_html_e( 'Manual Referral', 'rtwwwap-wp-wc-affiliate-program' );
					    			}
								}
								elseif( RTWWWAP_IS_WOO == 1 ||  RTWWWAP_IS_Easy == 1){
									if( $rtwwwap_value[ 'type' ] == 4 ){
										echo esc_html__( 'MLM Bonus', 'rtwwwap-wp-wc-affiliate-program' ).' (';
							?>
										<a target="_blank" href="<?php echo esc_url( get_edit_post_link( $rtwwwap_value[ 'order_id' ] ) ); ?>"><?php echo esc_html( $rtwwwap_value[ 'order_id' ] ); ?></a>
							<?php
					    				echo esc_html( ' )' );
					    			}
					    			elseif( $rtwwwap_value[ 'type' ] == 5 ){
					    				echo esc_html__( 'Share Bonus', 'rtwwwap-wp-wc-affiliate-program' ).' (';
					    	?>
										<a target="_blank" href="<?php echo esc_url( get_edit_post_link( $rtwwwap_value[ 'order_id' ] ) ); ?>"><?php echo esc_html( $rtwwwap_value[ 'order_id' ] ); ?></a>
					    	<?php
					    				echo esc_html( ' )' );
					    			}
					    			else{
					    	?>
										<a target="_blank" href="<?php echo esc_url( get_edit_post_link( $rtwwwap_value[ 'order_id' ] ) ); ?>"><?php echo esc_html( $rtwwwap_value[ 'order_id' ] ); ?></a>
							<?php
									}
								}
								
							?>
							</td>
							<?php 
							if( RTWWWAP_IS_WOO == 1)
							{ 
								?>
					    	<td>
								<?php
								
								
					    			if($rtwwwap_value[ 'order_id' ] != 0 ){
										$rtwwwap_order = wc_get_order( $rtwwwap_value[ 'order_id' ] );
										if(!empty($rtwwwap_order))
										{
											$order_status  = $rtwwwap_order->get_status();
					    					echo esc_html( $order_status );
										}
									
									}
					    		?>
							</td>
								<?php 
							}
							 ?>
					    	<td>
					    		<?php
					    			if( RTWWWAP_IS_WOO == 1 ){
										$rtwwwap_currency_sym = get_woocommerce_currency_symbol( $rtwwwap_value[ 'currency' ] );
									}
									else{
										require_once( RTWWWAP_DIR.'includes/rtwaffiliatehelper.php' );

										$rtwwwap_curr_obj 		= new RtwAffiliateHelper();
										$rtwwwap_currency_sym 	= $rtwwwap_curr_obj->rtwwwap_curr_symbol( $rtwwwap_value[ 'currency' ] );
									}
									
									echo ( $rtwwwap_currency_sym.number_format( $rtwwwap_value[ 'amount' ],$rtwwwap_decimal_place,$rtwwwap_decimal_separator, $rtwwwap_thousand_separator ) );
					    		?>
					    	</td>
					    	<td>
					    		<?php
									$rtwwwap_date_time_format = $rtwwwap_date_format.' '.$rtwwwap_time_format;
									$rtwwwap_local_date = get_date_from_gmt( date( 'Y-m-d H:i:s', strtotime( $rtwwwap_value[ 'date' ] ) ), $rtwwwap_date_time_format );
								?>
					    		<?php echo esc_html( $rtwwwap_local_date ); ?>
					    	</td>
					    	<td>
					    		<?php if( $rtwwwap_value[ 'capped' ] == '0' ){ ?>
						    			<?php
						    				if( $rtwwwap_value[ 'status' ] == 0 ){
						    			?>
												<span class="rtwwwap_approve">
								    				<?php esc_html_e( 'Approve', 'rtwwwap-wp-wc-affiliate-program' ); ?>
								    			</span>
								    			<span class="rtwwwap_reject">
								    				<?php esc_html_e( 'Reject', 'rtwwwap-wp-wc-affiliate-program' ); ?>
								    			</span>
						    			<?php
					    					}
					    					elseif( $rtwwwap_value[ 'status' ] == 1 ){
						    			?>
					    						<span class="rtwwwap_approved">
												<i class="far fa-check-circle rtwwwap_approved_icon"></i>
								    				<?php esc_html_e( 'Amount added in wallet', 'rtwwwap-wp-wc-affiliate-program' ); ?>
								    			</span>
						    			<?php
						    				}
								   			elseif( $rtwwwap_value[ 'status' ] == 2 ){
								   		?>
						    					<span class="rtwwwap_paid">
								    				<?php esc_html_e( 'Paid', 'rtwwwap-wp-wc-affiliate-program' ); ?>
								    			</span>
								    	<?php
								    		}
						    				elseif( $rtwwwap_value[ 'status' ] == 3 ){
						    			?>
						    					<span class="rtwwwap_rejected">
						    						<?php esc_html_e( 'Rejected', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						    					</span>
												
								    	<?php
						    				}
						    			?>
							    <?php }else{ ?>
							    	<span class="rtwwwap_capped">
						    			<?php esc_html_e( 'Capped', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						    		</span>
						    	<?php } ?>
					    	</td>
					    	<td>
					    		<a class="rtwwwap-delete-link rtwwwap_referral_delete" href="javascript:void(0);">
					    			<span class="dashicons dashicons-trash"></span>
					    		</a>
					    	</td>
					  	</tr>
				<?php } ?>
			</tbody>
			<tfoot>
			  	<tr>
			    	<th></th>
			    	<th><?php esc_html_e( 'User ID', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Affiliate', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Reference', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
					<?php if(RTWWWAP_IS_WOO == 1 ) 
							{ 
							?>
			        	    	<th><?php esc_html_e( 'Order Status', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
					<?php
							} 
							?>
					<th><?php esc_html_e( 'Amount', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Date', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Status', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Actions', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			  	</tr>
		  	</tfoot>
		</table>
	</div>
	<?php include_once( RTWWWAP_DIR . '/admin/partials/rtwwwap_tabs/rtwwwap_footer.php' ); ?>
	<div class="rtwwwap-popup-wrapper">
		<div class="rtwwwap-popup-content">
			  <h3 class="rtwwwap-popup-heading"><?php esc_html_e( 'Add Manual Referral', 'rtwwwap-wp-wc-affiliate-program' ); ?></h3>
				<div class="rtwwwap-popup-row rtwwwap_notification_section">
				</div>
				<div class="rtwwwap-popup-row">
					 <div class="rtwwwap-popup-label">
						 <?php esc_html_e( 'Select Affiliate', 'rtwwwap-wp-wc-affiliate-program' ); ?>
					 </div>
					 <div class="rtwwwap-popup-input">
						  <select id="rtwwwap-manual-aff-id" data-error="<?php esc_attr_e( 'Please Select Affiliate', 'rtwwwap-wp-wc-affiliate-program' ); ?>">
								<option value=""><?php esc_html_e( 'Select', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>
								<?php
										$rtwwwap_args = array(
											'meta_key' 		=> 'rtwwwap_affiliate',
											'meta_value' 	=> '1',
											'orderby' 		=> 'id',
											'order' 		=> 'desc',
											'fields' => array('ID', 'display_name')
										);
										$rtwwwap_users = get_users( $rtwwwap_args );
										foreach ($rtwwwap_users as $key => $value)
										{
											?>
												<option value="<?php echo esc_attr($value->ID);?>"><?php echo esc_html($value->display_name); ?></option>
											<?php
										}
								 ?>
						  </select>
					 </div>
				</div>
				<div class="rtwwwap-popup-row">
					 <div class="rtwwwap-popup-label">
					 	 <?php esc_html_e( 'Reference', 'rtwwwap-wp-wc-affiliate-program' ); ?>
					 </div>
					 <div class="rtwwwap-popup-input">
						 <select id="rtwwwap-manual-aff-ref" data-error="<?php esc_attr_e( 'Please Select Reference', 'rtwwwap-wp-wc-affiliate-program' ); ?>">
							 <option value="6"><?php esc_html_e( 'Manual Referral', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>
						 </select>
					 </div>
				</div>
				<div class="rtwwwap-popup-row">
					 <div class="rtwwwap-popup-label">
					 	 <?php esc_html_e( 'Amount', 'rtwwwap-wp-wc-affiliate-program' ); ?>
					 </div>
					 <div class="rtwwwap-popup-input">
						  <input type="number" value="" id="rtwwwap-manual-ref-amnt" data-error="<?php esc_attr_e( 'Please Enter Amount', 'rtwwwap-wp-wc-affiliate-program' ); ?>">
					 </div>
				</div>
				<div class="rtwwwap-popup-row">
					 <div class="rtwwwap-popup-label">
					 	 <?php esc_html_e( 'Status', 'rtwwwap-wp-wc-affiliate-program' ); ?>
					 </div>
					 <div class="rtwwwap-popup-input">
						 <select id="rtwwwap-manual-aff-status" data-error="<?php esc_attr_e( 'Please Select Status', 'rtwwwap-wp-wc-affiliate-program' ); ?>">
							<option value=""><?php esc_html_e( 'Select', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>
							<option value="0"><?php esc_html_e( 'Pending', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>
							<option value="1"><?php esc_html_e( 'Approve', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>
							<option value="2"><?php esc_html_e( 'Paid', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>
						</select>
					 </div>
				</div>
				<div class="rtwwwap-popup-footer">
					<input type="button" value="<?php esc_html_e( 'Save', 'rtwwwap-wp-wc-affiliate-program' ); ?>" class="rtwwwap-button" id="rtwwwap_manual_add_ref">
					<input type="reset" name="" value="<?php esc_html_e( 'Cancel', 'rtwwwap-wp-wc-affiliate-program' ); ?>" class="rtwwwap-button rtwwwap-button-reset">
				</div>
		</div>
	</div>
	<div class="rtwwwap-reject-message-wrapper">
		<div class="rtwwwap-popup-content">
			  <h3 class="rtwwwap-popup-heading"><?php esc_html_e( 'Enter Your Reason Here', 'rtwwwap-wp-wc-affiliate-program' ); ?></h3>
				<div class="rtwwwap-popup-row">
					 <div class="rtwwwap-popup-input-reject">
					 <textarea rows="4" cols="65" maxlength="100" class="rtwwwap_reject_message_content" placeholder='<?php esc_html_e("Enter your reason here within 100 words... ", "rtwwwap-wp-wc-affiliate-program" )?>' ></textarea>
					 </div>
				</div>
				<div class="rtwwwap-popup-footer">
					<input type="button" value="<?php esc_html_e( 'Save', 'rtwwwap-wp-wc-affiliate-program' ); ?>" class="rtwwwap-button" id="rtwwwap_manual_add_message">
					<input type="reset" name="" value="<?php esc_html_e( 'Cancel', 'rtwwwap-wp-wc-affiliate-program' ); ?>" class="rtwwwap-button rtwwwap-button-reset" id="rtwwwap_cancle_add_message">
				</div>
		</div>
	</div>
</div>
