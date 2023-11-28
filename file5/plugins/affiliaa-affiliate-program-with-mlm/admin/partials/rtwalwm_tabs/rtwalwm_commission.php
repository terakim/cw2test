<?php
	settings_fields( 'rtwwwap_commission_settings' );
	$rtwalwm_commission_settings = get_option( 'rtwwwap_commission_settings_opt' );
?>

<table class="rtwalwm-table form-table">
	<tbody>
		<tr>
			<th>
				<?php esc_html_e( 'Commission Based on', 'rtwalwm-wp-wc-affiliate-program' ); ?>
			</th>
			<td class="tr2">
				<?php
					$rtwalwm_comm_base = isset( $rtwalwm_commission_settings[ 'comm_base' ] ) ? $rtwalwm_commission_settings[ 'comm_base' ] : '1';
					
				?>
				<p>
					<span class="rtwalwm-custom-radio">
						<input id="radio-8" type="radio" class="rtwalwm_show_hide_prod_comm" name="rtwwwap_commission_settings_opt[comm_base]" value="1" <?php checked( $rtwalwm_comm_base, 1 ); ?> /><?php esc_html_e( 'Products', 'rtwalwm-wp-wc-affiliate-program' ); ?>
						<label for="radio-8"></label>
					</span>
				</p>
				<p>
					<span class="rtwalwm-custom-radio">
						  <input id="radio-9" type="radio" class="rtwalwm_show_hide_prod_comm" name="rtwwwap_commission_settings_opt[comm_base]" value="0" <?php checked( $rtwalwm_comm_base, 0 ); ?> disabled />
						  <label for="radio-9"></label>
					  	<?php printf( '%s ( %s <a href=%s target="_blank">%s</a> )', esc_html__( 'Users', 'rtwalwm-wp-wc-affiliate-program' ), esc_html__( 'To set commission for users goto', 'rtwalwm-wp-wc-affiliate-program' ), esc_url( admin_url( 'admin.php?page=rtwalwm&rtwalwm_tab=rtwalwm_levels' ) ), esc_html__( 'Levels', 'rtwalwm-wp-wc-affiliate-program' ) ); ?>
					</span>
					<label class="rtwwdpdl_pro_text"> 	<?php esc_html_e( 'USERS option is Available in PRO version', 'rtwalwm-wp-wc-affiliate-program' ); ?>	
					<a target="_blank" href=<?php echo esc_url("https://codecanyon.net/item/wordpress-woocommerce-affiliate-program/23580333")?> ><?php esc_html_e( 'Get it now', 'rtwalwm-wp-wc-affiliate-program' ); ?></a></label>
				</p>
			</td>
		</tr>
		
		<tr class="rtwalwm_prod_comm ">
			<th>
				<?php esc_html_e( 'Commission for All Products', 'rtwalwm-wp-wc-affiliate-program' ); ?>
			</th>
			<td class="tr2">
				<table>
					<thead>
						<th>
							<?php esc_html_e( 'Commission Type', 'rtwalwm-wp-wc-affiliate-program' ); ?>
						</th>
						<th>
							<?php esc_html_e( 'Commission amount', 'rtwalwm-wp-wc-affiliate-program' ); ?>
						</th>
					</thead>
					<tbody class="rtwalwm_tbody_all">
						<tr>
							<td>
								<?php
									$rtwalwm_all_comm_selected = isset( $rtwalwm_commission_settings[ 'all_commission_type' ] ) ? $rtwalwm_commission_settings[ 'all_commission_type' ] : 'percentage';
								?>
								<select class="rtwalwm_select2_all" id="" name="rtwwwap_commission_settings_opt[all_commission_type]" >
									<option value="percentage" <?php selected( $rtwalwm_all_comm_selected, 'percentage' ) ?> >
										<?php esc_html_e( 'Percentage', 'rtwalwm-wp-wc-affiliate-program' ); ?>
									</option>
									<option value="fixed" <?php selected( $rtwalwm_all_comm_selected, 'fixed' ) ?> >
										<?php esc_html_e( 'Fixed', 'rtwalwm-wp-wc-affiliate-program' ); ?>
									</option>
								</select>
							</td>
							<td>
								<input type="number" min="0" name="rtwwwap_commission_settings_opt[all_commission]" value="<?php echo esc_attr(isset(  $rtwalwm_commission_settings[ 'all_commission' ] ) ?$rtwalwm_commission_settings[ 'all_commission' ]  :  '0' ); ?>" />
								<div class="descr"><?php esc_html_e( 'Enter Commission (By default 0)', 'rtwalwm-wp-wc-affiliate-program' );?></div>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr class="rtwalwm_prod_comm ">
			<th><?php esc_html_e( 'Commission per Product', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			<td class="tr2">
				<p>
					<span class="rtwalwm-custom-radio">
					  <input id="radio-2" type="radio" name="rtwwwap_commission_settings_opt[per_prod_mode]" value="2" <?php isset( $rtwalwm_commission_settings[ 'per_prod_mode' ] ) ? checked( $rtwalwm_commission_settings[ 'per_prod_mode' ], 2 ) : ''; ?> /><?php esc_html_e( 'Fixed Price', 'rtwalwm-wp-wc-affiliate-program' ); ?>
					  <label for="radio-2"></label>
				    </span>
				</p>
			
				<p>
					<span class="rtwwwap-custom-radio">
						<input id="radio-1" type="radio" name="rtwwwap_commission_settings_opt[per_prod_mode]" value="1" <?php isset( $rtwalwm_commission_settings[ 'per_prod_mode' ] ) ? checked( $rtwalwm_commission_settings[ 'per_prod_mode' ], 1 ) : ''; ?> /><?php esc_html_e( 'Percentage', 'rtwalwm-wp-wc-affiliate-program' ); ?>
						<label for="radio-1"></label>
					</span>
				</p>
				<div class="descr"><?php esc_html_e( 'You need to add commission from product page', 'rtwalwm-wp-wc-affiliate-program' ); ?></div>
				<p>
					<span class="rtwalwm-custom-radio">
					  <input id="radio-3" type="radio" name="rtwwwap_commission_settings_opt[per_prod_mode]" value="0" <?php esc_attr(isset( $rtwalwm_commission_settings[ 'per_prod_mode' ] ) ? 0 : 0) ; ?> disabled /><?php esc_html_e( 'Percentage + Fixed Price', 'rtwalwm-wp-wc-affiliate-program' ); ?>
					   <label for="radio-3"></label>
					

				</p>
				<span class="rtwwdpdl_pro_text"> 	<?php esc_html_e( 'PERCENTAGE & PERCENTAGE+FIXED option is Available in PRO version', 'rtwalwm-wp-wc-affiliate-program' ); ?>	
					<a target="_blank" href=<?php echo esc_url("https://codecanyon.net/item/wordpress-woocommerce-affiliate-program/23580333")?>><?php esc_html_e( 'Get it now', 'rtwalwm-wp-wc-affiliate-program' ); ?></a></span>
			</td>
		</tr>
		
		<tr class="rtwalwm_prod_comm ">
			<td colspan="2">

				<span class="rtwwdpdl_pro_text"> 	<?php esc_html_e( 'All Features Listed Below are Available in PRO Version', 'rtwalwm-wp-wc-affiliate-program' ); ?>	
				<a target="_blank" href=<?php echo esc_url("https://codecanyon.net/item/wordpress-woocommerce-affiliate-program/23580333")?>><?php esc_html_e( 'Get it now', 'rtwalwm-wp-wc-affiliate-program' ); ?></a></span>
			</td>
			<td> </td>
		</tr>

		<tr class="rtwalwm_prod_comm ">
			<th>
			<span id="rtwalwm_th_heading"><?php esc_html_e( 'Commission per Category', 'rtwalwm-wp-wc-affiliate-program' ); ?>
					<span id = "rtwalwm_pro_img"><img src="<?php echo esc_url( RTWALWM_URL.'assets/images/PRO.png' ); ?>" alt=""></span>
			</span>
			


		</th>
		
			<td class="tr2">
				<table>
					<thead>
						<th>
							<?php esc_html_e( 'Categories', 'rtwalwm-wp-wc-affiliate-program' ); ?>
						</th>
						<th>
							<?php esc_html_e( 'Percentage', 'rtwalwm-wp-wc-affiliate-program' ); ?>
						</th>
						<th>
							<?php esc_html_e( 'Fixed Price', 'rtwalwm-wp-wc-affiliate-program' ); ?>
						</th>
						<th>
							<?php esc_html_e( 'Add/Remove row', 'rtwalwm-wp-wc-affiliate-program' ); ?>
						</th>
					</thead>
					<tbody class="rtwalwm_tbody">
					
						<?php
						if(RTWALWM_IS_WOO == 1) {
							$rtwalwm_all_categories = 	get_categories( array(
														            'hide_empty' => 0,
														            'taxonomy'   => 'product_cat'
																));
						}
						if(RTWALWM_IS_Easy == 1) {
							$rtwalwm_all_categories = 	get_categories( array(
														            'hide_empty' => 0,
														            'taxonomy'   => 'download_category'
																));
						}
						?>
						<!-- hidden row start-->
						<tr>
							<td>
								<select class="rtwalwm_select2" multiple="multiple" id="" data-placeholder="<?php echo esc_html__( 'Select categories', 'rtwalwm-wp-wc-affiliate-program' ); ?>">
									
										<option value="" disabled>
											<?php esc_html_e( 'No Category', 'rtwalwm-wp-wc-affiliate-program' ); ?>
										</option>
									
								</select>
							</td>
							<td>
								<input class="rtwalwm_cat_percentage_commission" type="number" min="0" max="100" disabled/>
							</td>
							<td>
								<input class="rtwalwm_cat_fixed_commission" type="number" min="0" disabled/>
							</td>
							<td>
								<span class="dashicons dashicons-plus-alt rtwalwm_add_new_row" disabled></span>
								<span class="dashicons dashicons-dismiss rtwalwm_remove_row" disabled></span>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<th>
			<span id="rtwalwm_th_heading">	<?php esc_html_e( 'Maximum commission to an Affiliate in a month', 'rtwalwm-wp-wc-affiliate-program' ); ?>
				<span id = "rtwalwm_pro_img"><img src="<?php echo esc_url( RTWALWM_URL.'assets/images/PRO.png' ); ?>" alt=""></span>
			</span>
				
			</th>
			<td class="tr2">
				<input type="number" min="0"  disabled/>
				<div class="descr">
					<?php esc_html_e( 'Enter Max. Commission (By default 0, that means unlimited commission)', 'rtwalwm-wp-wc-affiliate-program' );?>
				</div>
			</td>
		</tr>
		<tr>
			<th>
			<span id="rtwalwm_th_heading"><?php esc_html_e( 'Withdrawal Fees', 'rtwalwm-wp-wc-affiliate-program' ); ?>
				<span id = "rtwalwm_pro_img"><img src="<?php echo esc_url( RTWALWM_URL.'assets/images/PRO.png' ); ?>" alt=""></span>
			</span>
				
			</th>
			<td class="tr2">
				<input type="number" min="0" step="0.01" disabled />
				<div class="descr">
					<?php esc_html_e( 'Enter Fees to be deducted while payouts (By default 0)', 'rtwalwm-wp-wc-affiliate-program' ); ?>
				</div>
			</td>
		</tr>
		<tr>
			<th>
			<span id="rtwalwm_th_heading"><?php esc_html_e( 'Unlimited / Lifetime Commission', 'rtwalwm-wp-wc-affiliate-program' ); ?>
				<span id = "rtwalwm_pro_img"><img src="<?php echo esc_url( RTWALWM_URL.'assets/images/PRO.png' ); ?>" alt=""></span>
			</span>
				
			</th>
			<td class="tr2">
				<p>
					<span class="rtwalwm-custom-radio">
						<input id="radio-10" type="radio" class="rtwalwm_override_show_hide"  disabled/><?php esc_html_e( 'Yes', 'rtwalwm-wp-wc-affiliate-program' ); ?>
						<label for="radio-10"></label>
					</span>
				</p>
				<p>
					<span class="rtwalwm-custom-radio">
					  	<input id="radio-11" type="radio" class="rtwalwm_override_show_hide" disabled /><?php esc_html_e( 'No', 'rtwalwm-wp-wc-affiliate-program' ); ?>
					  	<label for="radio-11"></label>
				    </span>
				</p>
				<div class="descr">
					<?php esc_html_e( 'When Unlimited commission is set then commission will be generated every time a referee made a purchase. No matter if a cookie is set or not.', 'rtwalwm-wp-wc-affiliate-program' ); ?>
				</div>
			</td>
		</tr>
		<tr class="rtwalwm_override">
			<th>
			<span id="rtwalwm_th_heading"><?php esc_html_e( 'Override Referrer in Unlimited Commission', 'rtwalwm-wp-wc-affiliate-program' ); ?>
				<span id = "rtwalwm_pro_img"><img src="<?php echo esc_url( RTWALWM_URL.'assets/images/PRO.png' ); ?>" alt=""></span>
						
			</span>
			</th>
			<td class="tr2">
				<p>
					<span class="rtwalwm-custom-radio">
						<input id="radio-12" type="radio" class="" disabled /><?php esc_html_e( 'Yes', 'rtwalwm-wp-wc-affiliate-program' ); ?>
						<label for="radio-12"></label>
					</span>
				</p>
				<p>
					<span class="rtwalwm-custom-radio">
					  	<input id="radio-13" type="radio" class="" disabled /><?php esc_html_e( 'No', 'rtwalwm-wp-wc-affiliate-program' ); ?>
					  	<label for="radio-13"></label>
				    </span>
				</p>
				<div class="descr">
					<?php esc_html_e( "When not selected then first referrer will get commission every time a purchase is done by referee. No matter who's referral link is being opened.", 'rtwalwm-wp-wc-affiliate-program' ); ?>
				</div>
			</td>
		</tr>
		<tr>
			<th>
			<span id="rtwalwm_th_heading"><?php esc_html_e( 'Commission for only the URL opened?', 'rtwalwm-wp-wc-affiliate-program' ); ?>
				<span id = "rtwalwm_pro_img"><img src="<?php echo esc_url( RTWALWM_URL.'assets/images/PRO.png' ); ?>" alt=""></span>
			</span>
			</th>
			<td class="tr2">
				<p>
					<span class="rtwalwm-custom-radio">
					   	<input id="radio-4" type="radio" class="rtwalwm_only_open_url" disabled />
					   	<?php esc_html_e( 'Yes', 'rtwalwm-wp-wc-affiliate-program' ); ?>
					   	<label for="radio-4"></label>
					</span>
					<span class="rtwalwm-custom-radio">
					   	<input id="radio-5" type="radio" class="rtwalwm_only_open_url" disabled/>
					   	<?php esc_html_e( 'No', 'rtwalwm-wp-wc-affiliate-program' ); ?>
					    <label for="radio-5"></label>
				  </span>
				</p>
				<div class="descr">
					<?php esc_html_e( 'It will only work with Referral Links and when Unlimited/Lifetime is not set. That means if you are using Referral Code than this functionality will not work.', 'rtwalwm-wp-wc-affiliate-program' ); ?>
				</div>
			</td>
		</tr>
		<tr>
			<th>
			<span id="rtwalwm_th_heading"><?php esc_html_e( 'Enable Two Way Commission', 'rtwalwm-wp-wc-affiliate-program' ); ?>
				<span id = "rtwalwm_pro_img"><img src="<?php echo esc_url( RTWALWM_URL.'assets/images/PRO.png' ); ?>" alt=""></span>
			</span>
			</th>
			<td class="tr2">
		
				<p>
					<span class="rtwalwm-custom-radio">
						<input id="radio-two-way-1" type="radio" class="" disabled/><?php esc_html_e( 'Yes', 'rtwalwm-wp-wc-affiliate-program' ); ?>
						<label for="radio-two-way-1"></label>
					</span>
				</p>
				<p>
					<span class="rtwalwm-custom-radio">
					  	<input id="radio-two-way-2" type="radio" class="" disabled /><?php esc_html_e( 'No', 'rtwalwm-wp-wc-affiliate-program' ); ?>
					  	<label for="radio-two-way-2"></label>
				    </span>
				</p>
				<div class="descr">
					<?php esc_html_e( "After Enabling this, you can give commission to users which is referred by affiliate. Commission for user can set from product edit page. Only Product Wise Commission is Availiable. ", 'rtwalwm-wp-wc-affiliate-program' ); ?>
				</div>
			</td>
		</tr>
		<tr>
			<th>
			<span id="rtwalwm_th_heading"><?php esc_html_e( 'Activate Generation of Coupons', 'rtwalwm-wp-wc-affiliate-program' ); ?>
				<span id = "rtwalwm_pro_img"><img src="<?php echo esc_url( RTWALWM_URL.'assets/images/PRO.png' ); ?>" alt=""></span>
			</span>
			</th>
			<td class="tr2">
				<p>
					<span class="rtwalwm-custom-radio">
					   	<input id="radio-6" type="radio" class="rtwalwm_coupons" disabled />
					   	<?php esc_html_e( 'Yes', 'rtwalwm-wp-wc-affiliate-program' ); ?>
					   	<label for="radio-6"></label>
					</span>
					<span class="rtwalwm-custom-radio">
						
					  	<input id="radio-7" type="radio" class="rtwalwm_coupons" disabled />
					   	<?php esc_html_e( 'No', 'rtwalwm-wp-wc-affiliate-program' ); ?>
					   	<label for="radio-7"></label>
					</span>
				</p>
				<div class="descr">
					<?php esc_html_e( "After Activating this , you can generate Coupons of amount that you have in your wallet .This option is currently available for WooCommerce only ", 'rtwalwm-wp-wc-affiliate-program' ); ?>
				</div>
			</td>
		</tr>
		<tr id="rtwalwm_min_amount" >
			<th>
			<span id="rtwalwm_th_heading"><?php esc_html_e( 'Minimum amount for Coupon generation', 'rtwalwm-wp-wc-affiliate-program' ); ?>
				<span id = "rtwalwm_pro_img"><img src="<?php echo esc_url( RTWALWM_URL.'assets/images/PRO.png' ); ?>" alt=""></span>
						
			</span>
			</th>
			<td class="tr2">
				<p>
					<input type="number" min="1" disabled/>
				</p>
				<p><?php esc_html_e( 'Enter Amount', 'rtwalwm-wp-wc-affiliate-program' ); ?></p>
			</td>
		</tr>
		<tr >
			<th>

			<span id="rtwalwm_th_heading"><?php esc_html_e( 'Minimum amount affiliate need to Withdrawal their money', 'rtwalwm-wp-wc-affiliate-program' ); ?>
				<span id = "rtwalwm_pro_img"><img src="<?php echo esc_url( RTWALWM_URL.'assets/images/PRO.png' ); ?>" alt=""></span>
						
			</span>
			</th>
	
			<td class="tr2">
				<p>
					<input type="number"  min="1" disabled />
				</p>
				<p><?php esc_html_e( 'Enter Amount', 'rtwalwm-wp-wc-affiliate-program' ); ?></p>
			</td>
		</tr>
	</tbody>
</table>
