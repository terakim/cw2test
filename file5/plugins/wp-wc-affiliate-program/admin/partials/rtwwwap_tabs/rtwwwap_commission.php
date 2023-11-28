<?php
	settings_fields( 'rtwwwap_commission_settings' );
	$rtwwwap_commission_settings = get_option( 'rtwwwap_commission_settings_opt' );

?>


<table class="rtwwwap-table form-table">
	<tbody>
		<tr>
			<th>
				<?php esc_html_e( 'Commission Based ', 'rtwwwap-wp-wc-affiliate-program' ); ?>
			</th>
			<td class="tr2">
				<?php
					$rtwwwap_comm_base = isset( $rtwwwap_commission_settings[ 'comm_base' ] ) ? $rtwwwap_commission_settings[ 'comm_base' ] : '1';
					
				?>
				
				<p>
					<span class="rtwwwap-custom-radio">
						<input id="radio-8" type="radio" class="rtwwwap_show_hide_prod_comm" name="rtwwwap_commission_settings_opt[comm_base]" value="1" <?php checked( $rtwwwap_comm_base, 1 ); ?> /><?php esc_html_e( 'Products', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						<label for="radio-8"></label>
					</span>
				</p>
				<p>
					<span class="rtwwwap-custom-radio">
					  	<input id="radio-9" type="radio" class="rtwwwap_show_hide_prod_comm" name="rtwwwap_commission_settings_opt[comm_base]" value="2" <?php checked( $rtwwwap_comm_base, 2 ); ?> />
					  	<?php printf( '%s ( %s <a href=%s target="_blank">%s</a> )', esc_html__( 'Users', 'rtwwwap-wp-wc-affiliate-program' ), esc_html__( 'To set commission for users goto', 'rtwwwap-wp-wc-affiliate-program' ), esc_url( admin_url( 'admin.php?page=rtwwwap&rtwwwap_tab=rtwwwap_levels' ) ), esc_html__( 'Levels', 'rtwwwap-wp-wc-affiliate-program' ) ); ?>
					  	<label for="radio-9"></label>
				    </span>
				</p>
			</td>
		</tr>
		
		<tr class="rtwwwap_prod_comm <?php if( $rtwwwap_comm_base == 2 ){ echo 'rtwwwap_prod_comm_hide'; } ?>">
			<th>
				<?php esc_html_e( 'Commission for All Products', 'rtwwwap-wp-wc-affiliate-program' ); ?>
			</th>
			<td class="tr2">
				<table>
					<thead>
						<th>
							<?php esc_html_e( 'Commission Type', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
						<th>
							<?php esc_html_e( 'Commission amount', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
					</thead>
					<tbody class="rtwwwap_tbody_all">
						<tr>
							<td>
								<?php
									$rtwwwap_all_comm_selected = isset( $rtwwwap_commission_settings[ 'all_commission_type' ] ) ? $rtwwwap_commission_settings[ 'all_commission_type' ] : 'percentage';
								?>
								<select class="rtwwwap_select2_all" id="" name="rtwwwap_commission_settings_opt[all_commission_type]" >
									<option value="percentage" <?php selected( $rtwwwap_all_comm_selected, 'percentage' ) ?> >
										<?php esc_html_e( 'Percentage', 'rtwwwap-wp-wc-affiliate-program' ); ?>
									</option>
									<option value="fixed" <?php selected( $rtwwwap_all_comm_selected, 'fixed' ) ?> >
										<?php esc_html_e( 'Fixed', 'rtwwwap-wp-wc-affiliate-program' ); ?>
									</option>
								</select>
							</td>
							<td>
								<input type="number" min="0" step="0.1" name="rtwwwap_commission_settings_opt[all_commission]" value="<?php echo isset( $rtwwwap_commission_settings[ 'all_commission' ] ) ? esc_attr( $rtwwwap_commission_settings[ 'all_commission' ] ) : esc_attr( '0' ); ?>" />
								<div class="descr"><?php esc_html_e( 'Enter Commission (By default 0)', 'rtwwwap-wp-wc-affiliate-program' );?></div>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr class="rtwwwap_prod_comm <?php if( $rtwwwap_comm_base == 2 ){ echo 'rtwwwap_prod_comm_hide'; } ?>">
			<th><?php esc_html_e( 'Commission per Product', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			<td class="tr2">
				<p>
					<span class="rtwwwap-custom-radio">
						<input id="radio-1" type="radio" name="rtwwwap_commission_settings_opt[per_prod_mode]" value="1" <?php isset( $rtwwwap_commission_settings[ 'per_prod_mode' ] ) ? checked( $rtwwwap_commission_settings[ 'per_prod_mode' ], 1 ) : ''; ?> /><?php esc_html_e( 'Percentage', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						<label for="radio-1"></label>
					</span>
				</p>
				<p>
					<span class="rtwwwap-custom-radio">
					  <input id="radio-2" type="radio" name="rtwwwap_commission_settings_opt[per_prod_mode]" value="2" <?php isset( $rtwwwap_commission_settings[ 'per_prod_mode' ] ) ? checked( $rtwwwap_commission_settings[ 'per_prod_mode' ], 2 ) : ''; ?> /><?php esc_html_e( 'Fixed Price', 'rtwwwap-wp-wc-affiliate-program' ); ?>
					  <label for="radio-2"></label>
				    </span>
				</p>
				<p>
					<span class="rtwwwap-custom-radio">
					  <input id="radio-3" type="radio" name="rtwwwap_commission_settings_opt[per_prod_mode]" value="3" <?php isset( $rtwwwap_commission_settings[ 'per_prod_mode' ] ) ? checked( $rtwwwap_commission_settings[ 'per_prod_mode' ], 3 ) : ''; ?> /><?php esc_html_e( 'Percentage + Fixed Price', 'rtwwwap-wp-wc-affiliate-program' ); ?>
					   <label for="radio-3"></label>
					</span>
				</p>
				<div class="descr"><?php esc_html_e( 'You need to add commission from product page', 'rtwwwap-wp-wc-affiliate-program' ); ?></div>
			</td>
		</tr>
		<tr class="rtwwwap_prod_comm <?php if( $rtwwwap_comm_base == 2 ){ echo 'rtwwwap_prod_comm_hide'; } ?>">
			<th><?php esc_html_e( 'Commission per Category', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			<td class="tr2">
				<table>
					<thead>
						<th>
							<?php esc_html_e( 'Categories', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
						<th>
							<?php esc_html_e( 'Percentage', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
						<th>
							<?php esc_html_e( 'Fixed Price', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
						<th>
							<?php esc_html_e( 'Add/Remove row', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
					</thead>
					<tbody class="rtwwwap_tbody">
					
						<?php
						if(RTWWWAP_IS_WOO == 1) {
							$rtwwwap_all_categories = 	get_categories( array(
														            'hide_empty' => 0,
														            'taxonomy'   => 'product_cat'
																));
						}
						if(RTWWWAP_IS_Easy == 1) {
							$rtwwwap_all_categories = 	get_categories( array(
														            'hide_empty' => 0,
														            'taxonomy'   => 'download_category'
																));
						}
						?>
						<!-- hidden row start-->
						<tr class="rtwwwap_add_new_row_hidden" style="display: none;">
							<td>
								<select class="rtwwwap_select2_hidden" multiple="multiple" id="" name="rtwwwap_commission_settings_opt[per_cat][]" data-placeholder="<?php echo esc_attr( 'Select categories', 'rtwwwap-wp-wc-affiliate-program' ); ?>">
									<?php
									if( !empty( $rtwwwap_all_categories ) ){
										foreach ( $rtwwwap_all_categories as $rtwwwap_key => $rtwwwap_category ) {
									?>
										<option value="<?php echo esc_attr( $rtwwwap_category->cat_ID ); ?>" >
											<?php echo esc_html( $rtwwwap_category->cat_name ); ?>
										</option>
									<?php }
									}
									else{ ?>
										<option value="" >
											<?php esc_html_e( 'No Category', 'rtwwwap-wp-wc-affiliate-program' ); ?>
										</option>
									<?php
									} ?>
								</select>
							</td>
							<td>
								<input class="rtwwwap_cat_percentage_commission" type="number" min="0" step="0.1" max="100" name="rtwwwap_commission_settings_opt[per_cat][cat_percentage_commission]" value="0" />
							</td>
							<td>
								<input class="rtwwwap_cat_fixed_commission" type="number" step="0.1" min="0" name="rtwwwap_commission_settings_opt[per_cat][cat_fixed_commission]" value="0" />
							</td>
							<td>
								<span class="dashicons dashicons-plus-alt rtwwwap_add_new_row"></span>
								<span class="dashicons dashicons-dismiss rtwwwap_remove_row"></span>
							</td>
						</tr>
						<!-- hidden row end-->
						<?php
						if( !empty( $rtwwwap_commission_settings ) && isset( $rtwwwap_commission_settings[ 'per_cat' ] ) && !empty( $rtwwwap_commission_settings[ 'per_cat' ] ) ){
							foreach( $rtwwwap_commission_settings[ 'per_cat' ] as $rtwwwap_key => $rtwwwap_value ){ ?>
						<tr>
							<td>
								<select class="rtwwwap_select2" multiple="multiple" id="" name="rtwwwap_commission_settings_opt[per_cat_<?php echo esc_attr( $rtwwwap_key ); ?>][]" data-placeholder="<?php echo esc_attr( 'Select categories', 'rtwwwap-wp-wc-affiliate-program' ); ?>" >
									<?php
									$rtwwwap_selected = '';
									foreach ( $rtwwwap_all_categories as $rtwwwap_key1 => $rtwwwap_category1 )
									{
										if( in_array( $rtwwwap_category1->cat_ID, $rtwwwap_value[ 'ids' ] ) ){
											$rtwwwap_selected = 'yes';
										}
										else{
											$rtwwwap_selected = 'no';
									}
									?>
										<option value="<?php echo esc_attr( $rtwwwap_category1->cat_ID ); ?>" <?php selected( $rtwwwap_selected, 'yes' ); ?> >
											<?php echo esc_html( $rtwwwap_category1->cat_name ); ?>
										</option>
									<?php
									}
									?>
								</select>
							</td>
							<td>
								<input class="rtwwwap_cat_percentage_commission" type="number" min="0" max="100" step="0.1" name="rtwwwap_commission_settings_opt[per_cat_<?php echo esc_attr( $rtwwwap_key ); ?>][cat_percentage_commission]" value="<?php echo isset( $rtwwwap_value[ 'cat_percentage_commission' ] ) ? esc_attr( $rtwwwap_value[ 'cat_percentage_commission' ] ) : esc_attr( '0' ); ?>" />
							</td>
							<td>
								<input class="rtwwwap_cat_fixed_commission" type="number" min="0" step="0.1" name="rtwwwap_commission_settings_opt[per_cat_<?php echo esc_attr( $rtwwwap_key ); ?>][cat_fixed_commission]" value="<?php echo isset( $rtwwwap_value[ 'cat_fixed_commission' ] ) ? esc_attr( $rtwwwap_value[ 'cat_fixed_commission' ] ) : esc_attr( '0' ); ?>" />
							</td>
							<td>
								<span class="dashicons dashicons-plus-alt rtwwwap_add_new_row"></span>
								<span class="dashicons dashicons-dismiss rtwwwap_remove_row"></span>
							</td>
						</tr>
						<?php }
						}
						else{ ?>
						<tr>
							<td>
								<select class="rtwwwap_select2" multiple="multiple" id="" name="rtwwwap_commission_settings_opt[per_cat_0][]" data-placeholder="<?php echo esc_attr( 'Select categories', 'rtwwwap-wp-wc-affiliate-program' ); ?>">
									<?php
									if( !empty( $rtwwwap_all_categories ) ){
										foreach ( $rtwwwap_all_categories as $rtwwwap_key => $rtwwwap_category ) {
									?>
										<option value="<?php echo esc_attr( $rtwwwap_category->cat_ID ); ?>" >
											<?php echo esc_html( $rtwwwap_category->cat_name ); ?>
										</option>
									<?php }
									}
									else{ ?>
										<option value="" >
											<?php esc_html_e( 'No Category', 'rtwwwap-wp-wc-affiliate-program' ); ?>
										</option>
									<?php
									} ?>
								</select>
							</td>
							<td>
								<input class="rtwwwap_cat_percentage_commission" type="number" min="0" max="100" step="0.1" name="rtwwwap_commission_settings_opt[per_cat_0][cat_percentage_commission]" value="0" />
							</td>
							<td>
								<input class="rtwwwap_cat_fixed_commission" type="number" min="0" step="0.1" name="rtwwwap_commission_settings_opt[per_cat_0][cat_fixed_commission]" value="0" />
							</td>
							<td>
								<span class="dashicons dashicons-plus-alt rtwwwap_add_new_row"></span>
								<span class="dashicons dashicons-dismiss rtwwwap_remove_row"></span>
							</td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
			</td>
		</tr>

		<tr class="rtwwwap_prod_comm <?php if( $rtwwwap_comm_base == 2 ){ echo 'rtwwwap_prod_comm_hide'; } ?>">
			<th><?php esc_html_e( 'Special offers for affiliate', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			<td class="tr2">
				<table>
					<thead>
						<th>
							<?php esc_html_e( 'Categories', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
						<th>
							<?php esc_html_e( 'Percentage', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
						<th>
							<?php esc_html_e( 'Fixed Price', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
						<th>
							<?php esc_html_e( 'Date Range', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
					</thead>
					<tbody class="rtwwwap_tbody">
					
						<?php
						if(RTWWWAP_IS_WOO == 1) {
							$rtwwwap_all_categories = 	get_categories( array(
														            'hide_empty' => 0,
														            'taxonomy'   => 'product_cat'
																));
						}
						if(RTWWWAP_IS_Easy == 1) {
							$rtwwwap_all_categories = 	get_categories( array(
														            'hide_empty' => 0,
														            'taxonomy'   => 'download_category'
																));
						}

						$rtwwwap_special_commission = isset($rtwwwap_commission_settings[ 'cat_opt_special' ])? $rtwwwap_commission_settings[ 'cat_opt_special' ]: array();

						?>
						<!-- hidden row start-->
						<tr class="rtwwwap_add_new_row_hidden">
							<td>
							<select class="rtwwwap_select2" multiple="multiple"  name="rtwwwap_commission_settings_opt[cat_opt_special][]" data-placeholder="<?php echo esc_attr( 'Select categories', 'rtwwwap-wp-wc-affiliate-program' ); ?>" >
								<?php
								$rtwwwap_selected = '';
								foreach ( $rtwwwap_all_categories as $rtwwwap_key1 => $rtwwwap_category1 )
								{
									
									if( in_array( $rtwwwap_category1->cat_ID, $rtwwwap_special_commission ) ){
										$rtwwwap_selected = 'yes';
									}
									else{
										$rtwwwap_selected = 'no';
									}
								?>
									<option value="<?php echo esc_attr( $rtwwwap_category1->cat_ID ); ?>" <?php selected( $rtwwwap_selected, 'yes' ); ?> >
										<?php echo esc_html( $rtwwwap_category1->cat_name ); ?>
									</option>
								<?php
								}
								?>
							</select>
							</td>
							<td>
								<input class="rtwwwap_cat_percentage_commission" type="number" min="0" step="0.1" max="100" name="rtwwwap_commission_settings_opt[percent]" value=<?php echo isset( $rtwwwap_commission_settings[ 'percent' ] ) ? esc_attr( $rtwwwap_commission_settings[ 'percent' ] ) : esc_attr( '0' ); ?> />
							</td>
							<td>
								<input class="rtwwwap_cat_fixed_commission" type="number" min="0" step="0.1" name="rtwwwap_commission_settings_opt[fixed]" value=<?php echo isset( $rtwwwap_commission_settings[ 'fixed' ] ) ? esc_attr( $rtwwwap_commission_settings[ 'fixed' ] ) : esc_attr( '0' ); ?> />
							</td>

							<td>
								<input class="rtwwwap_cat_percentage_commission" type="date" name="rtwwwap_commission_settings_opt[start_date]" value=<?php echo isset( $rtwwwap_commission_settings[ 'start_date' ] ) ? esc_attr( $rtwwwap_commission_settings[ 'start_date' ] ) : esc_attr( '0' ); ?> />
								<div class="descr">
									<?php esc_html_e( '(Start date)', 'rtwwwap-wp-wc-affiliate-program' );?>
								</div>
								
							</td>
							<td>
								<input class="rtwwwap_cat_percentage_commission" type="date" name="rtwwwap_commission_settings_opt[end_date]" value=<?php echo isset( $rtwwwap_commission_settings[ 'end_date' ] ) ? esc_attr( $rtwwwap_commission_settings[ 'end_date' ] ) : esc_attr( '0' ); ?> />
								<div class="descr">
									<?php esc_html_e( '(End date)', 'rtwwwap-wp-wc-affiliate-program' );?>
								</div>
							</td>

						</tr>
						<!-- hidden row end-->
						
					</tbody>
				</table>
			</td>
		</tr>

		<tr>
			<th>
				<?php esc_html_e( 'Maximum commission to an Affiliate in a month', 'rtwwwap-wp-wc-affiliate-program' ); ?>
			</th>
			<td class="tr2">
				<input type="number" min="0" step="0.1" name="rtwwwap_commission_settings_opt[max_commission]" value="<?php echo isset( $rtwwwap_commission_settings[ 'max_commission' ] ) ? esc_attr( $rtwwwap_commission_settings[ 'max_commission' ] ) : esc_attr( '0' ); ?>" />
				<div class="descr">
					<?php esc_html_e( 'Enter Max. Commission (By default 0, that means unlimited commission)', 'rtwwwap-wp-wc-affiliate-program' );?>
				</div>
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e( 'Withdrawal Fees', 'rtwwwap-wp-wc-affiliate-program' ); ?>
			</th>
			<td class="tr2">
				<input type="number" min="0" step="0.01" name="rtwwwap_commission_settings_opt[withdraw_commission]" value="<?php echo isset( $rtwwwap_commission_settings[ 'withdraw_commission' ] ) ? esc_attr( $rtwwwap_commission_settings[ 'withdraw_commission' ] ) : esc_attr( '0' ); ?>" />
				<div class="descr">
					<?php esc_html_e( 'Enter Fees to be deducted while payouts (By default 0)', 'rtwwwap-wp-wc-affiliate-program' ); ?>
				</div>
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e( 'Unlimited / Lifetime Commission', 'rtwwwap-wp-wc-affiliate-program' ); ?>
			</th>
			<td class="tr2">
				<?php
					$rtwwwap_unlimit_comm = isset( $rtwwwap_commission_settings[ 'unlimit_comm' ] ) ? $rtwwwap_commission_settings[ 'unlimit_comm' ] : '0';
				?>
				<p>
					<span class="rtwwwap-custom-radio">
						<input id="radio-10" type="radio" class="rtwwwap_override_show_hide" name="rtwwwap_commission_settings_opt[unlimit_comm]" value="1" <?php checked( $rtwwwap_unlimit_comm, 1 ); ?> /><?php esc_html_e( 'Yes', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						<label for="radio-10"></label>
					</span>
				</p>
				<p>
					<span class="rtwwwap-custom-radio">
					  	<input id="radio-11" type="radio" class="rtwwwap_override_show_hide" name="rtwwwap_commission_settings_opt[unlimit_comm]" value="0" <?php checked( $rtwwwap_unlimit_comm, 0 ); ?> /><?php esc_html_e( 'No', 'rtwwwap-wp-wc-affiliate-program' ); ?>
					  	<label for="radio-11"></label>
				    </span>
				</p>
				<div class="descr">
					<?php esc_html_e( 'When Unlimited commission is set then commission will be generated every time a referee made a purchase. No matter if a cookie is set or not.', 'rtwwwap-wp-wc-affiliate-program' ); ?>
				</div>
			</td>
		</tr>
		<tr class="rtwwwap_override <?php if( $rtwwwap_unlimit_comm == 0 ){ echo 'rtwwwap_override_hide'; } ?>">
			<th>
				<?php esc_html_e( 'Override Referrer in Unlimited Commission', 'rtwwwap-wp-wc-affiliate-program' ); ?>
			</th>
			<td class="tr2">
				<?php
					$rtwwwap_override_unlimit_comm = isset( $rtwwwap_commission_settings[ 'override_unlimit_comm' ] ) ? $rtwwwap_commission_settings[ 'override_unlimit_comm' ] : '0';
				?>
				<p>
					<span class="rtwwwap-custom-radio">
						<input id="radio-12" type="radio" class="" name="rtwwwap_commission_settings_opt[override_unlimit_comm]" value="1" <?php checked( $rtwwwap_override_unlimit_comm, 1 ); ?> /><?php esc_html_e( 'Yes', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						<label for="radio-12"></label>
					</span>
				</p>
				<p>
					<span class="rtwwwap-custom-radio">
					  	<input id="radio-13" type="radio" class="" name="rtwwwap_commission_settings_opt[override_unlimit_comm]" value="0" <?php checked( $rtwwwap_override_unlimit_comm, 0 ); ?> /><?php esc_html_e( 'No', 'rtwwwap-wp-wc-affiliate-program' ); ?>
					  	<label for="radio-13"></label>
				    </span>
				</p>
				<div class="descr">
					<?php esc_html_e( "When not selected then first referrer will get commission every time a purchase is done by referee. No matter who's referral link is being opened.", 'rtwwwap-wp-wc-affiliate-program' ); ?>
				</div>
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e( 'Commission for only the URL opened?', 'rtwwwap-wp-wc-affiliate-program' ); ?>
			</th>
			<td class="tr2">
				<p>
					<span class="rtwwwap-custom-radio">
					   	<input id="radio-4" type="radio" class="rtwwwap_only_open_url" name="rtwwwap_commission_settings_opt[only_open_url]" value="1" <?php isset( $rtwwwap_commission_settings[ 'only_open_url' ] ) ? checked( $rtwwwap_commission_settings[ 'only_open_url' ], 1 ) : ''; ?> />
					   	<?php esc_html_e( 'Yes', 'rtwwwap-wp-wc-affiliate-program' ); ?>
					   	<label for="radio-4"></label>
					</span>
					<span class="rtwwwap-custom-radio">
						<?php
							$rtwwwap_open_url_checked = 0;
							if( isset( $rtwwwap_commission_settings[ 'only_open_url' ] ) && $rtwwwap_commission_settings[ 'only_open_url' ] == 0 ){
								$rtwwwap_open_url_checked = 1;
							}
							elseif( !isset( $rtwwwap_commission_settings[ 'only_open_url' ] ) ){
								$rtwwwap_open_url_checked = 1;
							}
						?>
					   	<input id="radio-5" type="radio" class="rtwwwap_only_open_url" name="rtwwwap_commission_settings_opt[only_open_url]" value="0" <?php  checked( $rtwwwap_open_url_checked, 1 ); ?> />
					   	<?php esc_html_e( 'No', 'rtwwwap-wp-wc-affiliate-program' ); ?>
					    <label for="radio-5"></label>
				  </span>
				</p>
				<div class="descr">
					<?php esc_html_e( 'It will only work with Referral Links and when Unlimited/Lifetime is not set. That means if you are using Referral Code than this functionality will not work.', 'rtwwwap-wp-wc-affiliate-program' ); ?>
				</div>
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e( 'Enable Two Way Commission', 'rtwwwap-wp-wc-affiliate-program' ); ?>
			</th>
			<td class="tr2">
				<?php
					$rtwwwap_two_way_comm = isset( $rtwwwap_commission_settings[ 'two_way_comm' ] ) ? $rtwwwap_commission_settings[ 'two_way_comm' ] : '0';
				?>
				<p>
					<span class="rtwwwap-custom-radio">
						<input id="radio-two-way-1" type="radio" class="" name="rtwwwap_commission_settings_opt[two_way_comm]" value="1" <?php checked( $rtwwwap_two_way_comm, 1 ); ?> /><?php esc_html_e( 'Yes', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						<label for="radio-two-way-1"></label>
					</span>
				</p>
				<p>
					<span class="rtwwwap-custom-radio">
					  	<input id="radio-two-way-2" type="radio" class="" name="rtwwwap_commission_settings_opt[two_way_comm]" value="0" <?php checked( $rtwwwap_two_way_comm, 0 ); ?> /><?php esc_html_e( 'No', 'rtwwwap-wp-wc-affiliate-program' ); ?>
					  	<label for="radio-two-way-2"></label>
				    </span>
				</p>
				<div class="descr">
					<?php esc_html_e( "After Enabling this, you can give commission to users which is referred by affiliate. Commission for user can set from product edit page. Only Product Wise Commission is Availiable. ", 'rtwwwap-wp-wc-affiliate-program' ); ?>
				</div>
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e( 'Activate Generation of Coupons', 'rtwwwap-wp-wc-affiliate-program' ); ?>
			</th>
			<td class="tr2">
				<p>
					<span class="rtwwwap-custom-radio">
					   	<input id="radio-6" type="radio" class="rtwwwap_coupons" name="rtwwwap_commission_settings_opt[coupons]" value="1" <?php isset( $rtwwwap_commission_settings[ 'coupons' ] ) ? checked( $rtwwwap_commission_settings[ 'coupons' ], 1 ) : ''; ?> />
					   	<?php esc_html_e( 'Yes', 'rtwwwap-wp-wc-affiliate-program' ); ?>
					   	<label for="radio-6"></label>
					</span>
					<span class="rtwwwap-custom-radio">
						<?php
							$rtwwwap_coupon_checked = 0;
							if( isset( $rtwwwap_commission_settings[ 'coupons' ] ) && $rtwwwap_commission_settings[ 'coupons' ] == 0 ){
								$rtwwwap_coupon_checked = 1;
							}
							elseif( !isset( $rtwwwap_commission_settings[ 'coupons' ] ) ){
								$rtwwwap_coupon_checked = 1;
							}
						?>
					  	<input id="radio-7" type="radio" class="rtwwwap_coupons" name="rtwwwap_commission_settings_opt[coupons]" value="0" <?php checked( $rtwwwap_coupon_checked, 1 ); ?> />
					   	<?php esc_html_e( 'No', 'rtwwwap-wp-wc-affiliate-program' ); ?>
					   	<label for="radio-7"></label>
					</span>
				</p>
				<div class="descr">
					<?php esc_html_e( "After Activating this , you can generate Coupons of amount that you have in your wallet .This option is currently available for WooCommerce only ", 'rtwwwap-wp-wc-affiliate-program' ); ?>
				</div>
			</td>
		</tr>
		<tr id="rtwwwap_min_amount" class="<?php echo isset( $rtwwwap_commission_settings[ 'coupons' ] ) && $rtwwwap_commission_settings[ 'coupons' ] == 1 ? esc_attr( '' ) : esc_attr( 'rtwwwap_hidden' ); ?>" >
			<th>
				<?php esc_html_e( 'Minimum amount for Coupon generation', 'rtwwwap-wp-wc-affiliate-program' ); ?>
			</th>
			<td class="tr2">
				<p>
					<input type="number" name="rtwwwap_commission_settings_opt[min_amount_for_coupon]" min="1" step="0.1" value="<?php echo isset( $rtwwwap_commission_settings[ 'min_amount_for_coupon' ] ) ? esc_attr( $rtwwwap_commission_settings[ 'min_amount_for_coupon' ] ) : esc_attr( 1 ); ?>" />
				</p>
				<p><?php esc_html_e( 'Enter Amount', 'rtwwwap-wp-wc-affiliate-program' ); ?></p>
			</td>
		</tr>
		<tr >
			<th>
				<?php esc_html_e( 'Minimum amount affiliate need to Withdrawal their money', 'rtwwwap-wp-wc-affiliate-program' ); ?>
			</th>
			<td class="tr2">
				<p>
					<input type="number" name="rtwwwap_commission_settings_opt[minimum_ammount_for_affiliate]" min="1" step="0.1" value="<?php echo isset( $rtwwwap_commission_settings[ 'minimum_ammount_for_affiliate' ] ) ? esc_attr( $rtwwwap_commission_settings[ 'minimum_ammount_for_affiliate' ] ) : esc_attr( 1 ); ?>" />
				</p>
				<p><?php esc_html_e( 'Enter Amount', 'rtwwwap-wp-wc-affiliate-program' ); ?></p>
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e( 'Affiliate Referral automation', 'rtwwwap-wp-wc-affiliate-program' ); ?>
			</th>
			<td class="tr2">
				<?php
					$rtwwwap_affiliate_automation = isset( $rtwwwap_commission_settings[ 'affiliate_automation' ] ) ? $rtwwwap_commission_settings[ 'affiliate_automation' ] : '0';
				?>
				<p><select class="rtwwwap_select2_page" id="" name="rtwwwap_commission_settings_opt[affiliate_automation]">
								<option value="" <?php selected( $rtwwwap_affiliate_automation, 0 ); ?>>
									<?php esc_html_e( "Select option", 'rtwwwap-wp-wc-affiliate-program' ); ?>
								</option>
								<option value="completed" <?php selected( $rtwwwap_affiliate_automation,"completed" ); ?>>
									<?php esc_html_e( "completed	", 'rtwwwap-wp-wc-affiliate-program' ); ?>
								</option>
								<option value="processing" <?php selected( $rtwwwap_affiliate_automation, "processing" ); ?>>
									<?php esc_html_e( "Processing", 'rtwwwap-wp-wc-affiliate-program' ); ?>
								</option>	
							</select></p>
				<div class="descr">
					<?php esc_html_e( 'Select option to automate your referral in which status afilliate referral will automatically approve', 'rtwwwap-wp-wc-affiliate-program' ); ?>
				</div>
			</td>
		</tr>
	</tbody>
</table>
