<?php
	global $wpdb;
	$rtwalwm_all_referrals 	= $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `type` != 3 ORDER BY `id` DESC", ARRAY_A );
?>

<p class="rtwalwm_add_new_affiliate">
	<input type="button" value="<?php esc_attr_e( 'Approve all marked Referrals', 'rtwalwm-wp-wc-affiliate-program' ); ?>" class="rtwalwm-button rtwalwm_approve_all_referrals" name="rtwalwm_approve_all_referrals" />

	<input type="button" value="<?php esc_attr_e( 'Reject all marked Referrals', 'rtwalwm-wp-wc-affiliate-program' ); ?>" class="rtwalwm-button rtwalwm_reject_all_referrals" name="rtwalwm_reject_all_referrals" />
	
		
			<button type="button" name="button" class="rtwalwm-button  rtwalwm-manual-referral "><?php esc_html_e( 'Add Manual Referral', 'rtwalwm-wp-wc-affiliate-program' ); ?> 
	</button>
	<span id = "rtwalwm_pro_img_level_manual_referral"><img src="<?php echo esc_url( RTWALWM_URL.'assets/images/PRO.png' ); ?>" alt=""></span>

</p>

<div class="main-wrapper">
	<div class="rtwalwm-data-table-wrapper">
		<table class="rtwalwm_referral_table rtwalwm_data_table stripe" class="display dtr-inline" cellspacing="0">
		  	<thead>
			  	<tr>
			    	<th>
			    		<span class="rtwalwm-checkbox">
			    			<input class="rtwalwm_referrals_check_all" id="rtwalwm_checkbox_th" type="checkbox" name=""/>
			    			<label for="rtwalwm_checkbox_th"></label>
			    		</span>
			    	</th>
			    	<th><?php esc_html_e( 'User ID', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Affiliate', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
					<th><?php esc_html_e( 'Reference', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
					<?php if(RTWALWM_IS_WOO == 1 ) 
								{ 
							?>
			        				<th><?php esc_html_e( 'Order Status', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
					<?php
								

						} 
							?>
					<th><?php esc_html_e( 'Amount', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Date', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Status', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Actions', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			  	</tr>
		  	</thead>
		  	<tbody>
		  		<?php
			  		$rtwalwm_date_format = get_option( 'date_format' );
					$rtwalwm_time_format = get_option( 'time_format' );

		  			foreach( $rtwalwm_all_referrals as $rtwalwm_key => $rtwalwm_value ){
		  				$rtwalwm_aff_info = get_userdata( $rtwalwm_value[ 'aff_id' ] );
						$rtwalwm_aff_name = ( $rtwalwm_aff_info ) ? $rtwalwm_aff_info->user_login : '';
		  		?>
					  	<tr data-referral_id="<?php echo esc_attr( $rtwalwm_value[ 'id' ] ); ?>" class="" >
					    	<td>
					    	<?php
					    		if( $rtwalwm_value[ 'status' ] == 0 && $rtwalwm_value[ 'capped' ] == 0 ){
					    	?>
						    		<span class="rtwalwm-checkbox">
						    			<input id="rtwalwm_checkbox_<?php echo esc_attr( $rtwalwm_key ); ?>" type="checkbox" name=""/>
						    			<label for="rtwalwm_checkbox_<?php echo esc_attr( $rtwalwm_key ); ?>"></label>
						    		</span>
						    <?php
								}
					    	?>
					    	</td>
					    	<td>
					    		<a target="_blank" href="<?php echo esc_url( get_edit_user_link( $rtwalwm_value[ 'aff_id' ] ) ); ?>"><?php echo esc_html( $rtwalwm_value[ 'aff_id' ] ); ?></a>
					    	</td>
					    	<td>
					    		<a target="_blank" href="<?php echo esc_url( get_edit_user_link( $rtwalwm_value[ 'aff_id' ] ) ); ?>"><?php echo esc_html( $rtwalwm_aff_name ); ?></a>
					    	</td>
					    	<td>
					    	<?php
					    		if( $rtwalwm_value[ 'order_id' ] == 0 ){
					    			if( $rtwalwm_value[ 'type' ] == 1 ){
					    				echo esc_html__( 'Signup Bonus', 'rtwalwm-wp-wc-affiliate-program' ).' (';
					    	?>
					    				<a target="_blank" href="<?php echo esc_url( get_edit_user_link( $rtwalwm_value[ 'signed_up_id' ] ) ); ?>"><?php echo esc_html( $rtwalwm_value[ 'signed_up_id' ] ); ?></a>
					    	<?php
					    				echo esc_html( ' )' );
					    			}
					    			elseif( $rtwalwm_value[ 'type' ] == 2 ){
					    				esc_html_e( 'Performance Bonus', 'rtwalwm-wp-wc-affiliate-program' );
					    			}
										elseif( $rtwalwm_value[ 'type' ] == 6 ){
					    				esc_html_e( 'Manual Referral', 'rtwalwm-wp-wc-affiliate-program' );
					    			}
								}
								elseif( RTWALWM_IS_WOO == 1 ||  RTWALWM_IS_Easy == 1){
									if( $rtwalwm_value[ 'type' ] == 4 ){
										echo esc_html__( 'MLM Bonus', 'rtwalwm-wp-wc-affiliate-program' ).' (';
							?>
										<a target="_blank" href="<?php echo esc_url( get_edit_post_link( $rtwalwm_value[ 'order_id' ] ) ); ?>"><?php echo esc_html( $rtwalwm_value[ 'order_id' ] ); ?></a>
							<?php
					    				echo esc_html( ' )' );
					    			}
					    			elseif( $rtwalwm_value[ 'type' ] == 5 ){
					    				echo esc_html__( 'Share Bonus', 'rtwalwm-wp-wc-affiliate-program' ).' (';
					    	?>
										<a target="_blank" href="<?php echo esc_url( get_edit_post_link( $rtwalwm_value[ 'order_id' ] ) ); ?>"><?php echo esc_html( $rtwalwm_value[ 'order_id' ] ); ?></a>
					    	<?php
					    				echo esc_html( ' )' );
					    			}
					    			else{
					    	?>
										<a target="_blank" href="<?php echo esc_url( get_edit_post_link( $rtwalwm_value[ 'order_id' ] ) ); ?>"><?php echo esc_html( $rtwalwm_value[ 'order_id' ] ); ?></a>
							<?php
									}
								}
								
							?>
							</td>
							<?php 
							if( RTWALWM_IS_WOO == 1)
							{ 
								?>
					    	<td>
								<?php
								
								
					    			if($rtwalwm_value[ 'order_id' ] != 0 ){
										$rtwalwm_order = wc_get_order( $rtwalwm_value[ 'order_id' ] );
										if(!empty($rtwalwm_order))
										{
											$order_status  = $rtwalwm_order->get_status();
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
					    			if( RTWALWM_IS_WOO == 1 ){
										$rtwalwm_currency_sym = get_woocommerce_currency_symbol( $rtwalwm_value[ 'currency' ] );
									}
									else{
										$rtwalwm_currency_sym 	= esc_html__( '&#36;', 'rtwalwm-wp-wc-affiliate-program' );
									}
					    			echo esc_html( $rtwalwm_currency_sym.$rtwalwm_value[ 'amount' ] );
					    		?>
					    	</td>
					    	<td>
					    		<?php
									$rtwalwm_date_time_format = $rtwalwm_date_format.' '.$rtwalwm_time_format;
									$rtwalwm_local_date = get_date_from_gmt( date( 'Y-m-d H:i:s', strtotime( $rtwalwm_value[ 'date' ] ) ), $rtwalwm_date_time_format );
								?>
					    		<?php echo esc_html( $rtwalwm_local_date ); ?>
					    	</td>
					    	<td>
					    		<?php if( $rtwalwm_value[ 'capped' ] == '0' ){ ?>
						    			<?php
						    				if( $rtwalwm_value[ 'status' ] == 0 ){
						    			?>
												<span class="rtwalwm_approve">
								    				<?php esc_html_e( 'Approve', 'rtwalwm-wp-wc-affiliate-program' ); ?>
								    			</span>
								    			<span class="rtwalwm_reject">
								    				<?php esc_html_e( 'Reject', 'rtwalwm-wp-wc-affiliate-program' ); ?>
								    			</span>
						    			<?php
					    					}
					    					elseif( $rtwalwm_value[ 'status' ] == 1 ){
						    			?>
					    						<span class="rtwalwm_approved">
								    				<?php esc_html_e( 'Approved', 'rtwalwm-wp-wc-affiliate-program' ); ?>
								    			</span>
						    			<?php
						    				}
								   			elseif( $rtwalwm_value[ 'status' ] == 2 ){
								   		?>
						    					<span class="rtwalwm_paid">
								    				<?php esc_html_e( 'Paid', 'rtwalwm-wp-wc-affiliate-program' ); ?>
								    			</span>
								    	<?php
								    		}
						    				elseif( $rtwalwm_value[ 'status' ] == 3 ){
						    			?>
						    					<span class="rtwalwm_rejected">
						    						<?php esc_html_e( 'Rejected', 'rtwalwm-wp-wc-affiliate-program' ); ?>
						    					</span>
												
								    	<?php
						    				}
						    			?>
							    <?php }else{ ?>
							    	<span class="rtwalwm_capped">
						    			<?php esc_html_e( 'Capped', 'rtwalwm-wp-wc-affiliate-program' ); ?>
						    		</span>
						    	<?php } ?>
					    	</td>
					    	<td>
					    		<a class="rtwalwm-delete-link rtwalwm_referral_delete" href="javascript:void(0);">
					    			<span class="dashicons dashicons-trash"></span>
					    		</a>
					    	</td>
					  	</tr>
				<?php } ?>
			</tbody>
			<tfoot>
			  	<tr>
			    	<th></th>
			    	<th><?php esc_html_e( 'User ID', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Affiliate', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Reference', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
					<?php if(RTWALWM_IS_WOO == 1 ) 
							{ 
							?>
			        	    	<th><?php esc_html_e( 'Order Status', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
					<?php
							} 
							?>
					<th><?php esc_html_e( 'Amount', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Date', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Status', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Actions', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			  	</tr>
		  	</tfoot>
		</table>
	</div>
	<?php include_once( RTWALWM_DIR . '/admin/partials/rtwalwm_tabs/rtwalwm_footer.php' ); ?>

	
</div>
