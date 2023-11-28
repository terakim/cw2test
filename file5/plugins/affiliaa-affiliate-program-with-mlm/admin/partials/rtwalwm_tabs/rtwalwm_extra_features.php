<?php
	settings_fields( 'rtwwwap_extra_features');
	$rtwalwm_extra_features = get_option( 'rtwwwap_extra_features_opt' );

	$rtwalwm_login_page_title = isset($rtwalwm_extra_features['login_shortcode_page']) ? $rtwalwm_extra_features['login_shortcode_page'] : '';

?>
<div class="rtwalwm-extra-features-main-wrapper">

<div class="rtwalwm-extra-features-wrap">
	<ul>
		<li class="active" data-target="rtwalwm_extra_general"> <a href="#"><?php esc_html_e( 'General', 'rtwalwm-wp-wc-affiliate-program' ); ?></a> </li>
		<li data-target="rtwalwm_extra_label"> <a href="#"><?php esc_html_e( 'Labels', 'rtwalwm-wp-wc-affiliate-program' ); ?></a> </li>
		<li data-target="rtwalwm_extra_bonus"> <a href="#"><?php esc_html_e( 'Bonus', 'rtwalwm-wp-wc-affiliate-program' ); ?></a> </li>
		<li data-target="rtwalwm_extra_payment"> <a href="#"><?php esc_html_e( 'Payment', 'rtwalwm-wp-wc-affiliate-program' ); ?></a> </li>
		<li data-target="rtwalwm_extra_notification"> 
			<a href="#"><?php esc_html_e( 'Notification', 'rtwalwm-wp-wc-affiliate-program' ); ?><span id = "rtwalwm_pro_notification_level"></span></a> 
		</li>
		<li data-target="rtwalwm_extra_rank"> <a href="#"><?php esc_html_e( 'Rank', 'rtwalwm-wp-wc-affiliate-program' ); ?></a> </li>
		

	</ul>
</div>
<div class ="rtwwdpdl_pro_text_overlay">

<div class="rtwalwm-extra-table-wrapper">
		<table class="rtwalwm-table form-table rtwalwm-show" id="rtwalwm_extra_general">
			<tbody>
				<tr>
					<th><?php esc_html_e( 'Select Affiliate Page', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
					<td class="tr2">
						<?php
							$rtwalwm_affiliate_page_selected = get_option( 'rtwwwap_affiliate_page_id' );
							$rtwalwm_page_args = array(
											        'post_type'    => 'page',
											        'post_status'  => 'publish'
												);
							$rtwalwm_all_pages = get_pages( $rtwalwm_page_args );

						?>
						<p><select class="rtwalwm_select2_page" id="" name="rtwwwap_extra_features_opt[page]" >
							
							
							<option value="">
								<?php esc_html_e( "Select a Page", 'rtwalwm-wp-wc-affiliate-program' ); ?>
							</option>
							<?php
								foreach( $rtwalwm_all_pages as $rtwalwm_page_key => $rtwalwm_page_value )
								{
							?>
									<option value="<?php echo esc_attr($rtwalwm_page_value->ID); ?>" <?php selected( $rtwalwm_affiliate_page_selected, $rtwalwm_page_value->ID ) ?> >
										<?php echo esc_html( $rtwalwm_page_value->post_title ); ?>
									</option>
							<?php
								}
							?>
						</select>
						
						
						</p>
						<br>
						<div class="descr"><?php printf( '%s - %s', esc_html_e( 'Use the following shortcode on the selected page', 'rtwalwm-wp-wc-affiliate-program' ), '[rtwwwap_affiliate_page]' ); ?></div>
					</td>
				</tr>

				<tr>
					<th><?php esc_html_e( 'Affiliate Login page', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
					<td class="tr2">
						<?php
							
							$rtwalwm_page_args = array(
											        'post_type'    => 'page',
											        'post_status'  => 'publish'
												);
							$rtwalwm_all_pages = get_pages( $rtwalwm_page_args );
						?>
						<p><select class="rtwalwm_select2_page" id="" name="" >
							<option value="">
								<?php esc_html_e( "Select a Page", 'rtwalwm-wp-wc-affiliate-program' ); ?>
							</option>
							
						</select></p>
						<br>
						<div class="descr"><?php printf( '%s - %s', esc_html_e( 'Select page if your are using Login shortcode seprately', 'rtwalwm-wp-wc-affiliate-program', 'rtwalwm-wp-wc-affiliate-program' ), '[rtwalwm_aff_login_page]' ); ?></div>
					</td>
				</tr>

				<tr>
					<th><?php esc_html_e( 'Affiliate Signup page ', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
					<td class="tr2">
						<?php
						
							$rtwalwm_page_args = array(
											        'post_type'    => 'page',
											        'post_status'  => 'publish'
												);
							$rtwalwm_all_pages = get_pages( $rtwalwm_page_args );
						?>
						<p><select class="rtwalwm_select2_page" id="" name="" >
							<option value="">
								<?php esc_html_e( "Select a Page", 'rtwalwm-wp-wc-affiliate-program' ); ?>
							</option>
							
						</select></p>
						<br>
						<div class="descr"><?php printf( '%s - %s', esc_html_e( 'Select page if your are using Register shortcode seprately', 'rtwalwm-wp-wc-affiliate-program', 'rtwalwm-wp-wc-affiliate-program' ), '[rtwalwm_aff_reg_page]' ); ?></div>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Affiliate Page Template ', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
					<td class="tr2">
						<p><select class="rtwalwm_select2_page" id="" name="rtwwwap_extra_features_opt[affiliate_page_template]">
						<?php
					
							$rtwalwm_select_affiliate_page_temp =  esc_attr(isset($rtwalwm_extra_features[ 'affiliate_page_template' ]) ?$rtwalwm_extra_features[ 'affiliate_page_template' ]  : 1);
						?>
							<option value="" selected>
								<?php esc_html_e( "Template 1", 'rtwalwm-wp-wc-affiliate-program' ); ?>
							</option>
							<option value="" disabled>
								<?php esc_html_e( "Template 2", 'rtwalwm-wp-wc-affiliate-program' ); ?>
							</option>
							<option value="" disabled>
								<?php esc_html_e( "Template 3", 'rtwalwm-wp-wc-affiliate-program' ); ?>
							</option>
						</select></p>
						<br>
						<div class="descr"><?php printf( '%s', esc_html_e( 'Select Template for Affiliate Page ','rtwalwm-wp-wc-affiliate-program' )); ?></div>
					</td>
				</tr>

			
				<?php
							if( RTWALWM_IS_WOO == 1 ){
								?>
									<tr>
										<th><?php esc_html_e( 'Show under My Account', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
										<td class="tr2">
											<?php
												$rtwalwm_show_in_woo_checked = esc_attr(isset( $rtwalwm_extra_features[ 'show_in_woo' ] ) ? $rtwalwm_extra_features[ 'show_in_woo' ] : 1);
											?>
											<p>
												<span class="rtwalwm-custom-radio">
													<input id="radio-13" type="radio" name="" value="" disabled /><?php esc_html_e( 'Yes', 'rtwalwm-wp-wc-affiliate-program' ); ?>
													<label for="radio-13"></label>
												</span>
											</p>
											<p>
												<span class="rtwalwm-custom-radio">
													<input id="radio-14" type="radio" name="" value=""  disabled/><?php esc_html_e( 'No', 'rtwalwm-wp-wc-affiliate-program' ); ?>
													<label for="radio-14"></label>
												</span>
											</p>
											<div class="descr"><?php esc_html_e( 'Activate to show under WooCommerce->My Account', 'rtwalwm-wp-wc-affiliate-program' );?></div>
										</td>
									</tr>
								<?php
								}
							else if( RTWALWM_IS_WOO != 1 )
							{
				?>	
					<tr>
						<th><?php esc_html_e( 'Select Currency', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
						<td class="tr2">
						
							<p><select class="rtwalwm_select2_curr" id="" name="" disabled>
								
							</select></p>
							<br>
							<div class="descr"><?php printf( '%s', esc_html_e( 'By Default USD will be used', 'rtwalwm-wp-wc-affiliate-program' ) ); ?></div>
						</td>
					</tr>
					
				<?php
					 }
				?>
				<tr>
					<th>
					 <?php esc_html_e( 'Upto Decimal Places', 'rtwalwm-wp-wc-affiliate-program' ); ?>
					</th>
					<td class="tr2">
					 <input type="text" class="rtwalwm_admin_input_text" id="" name="" value="" placeholder="<?php esc_html_e( 'Enter Upto decimal places', 'rtwalwm-wp-wc-affiliate-program' ); ?>" disabled/>
					 <br>
					 <div class="descr"><?php esc_html_e( 'This decimal places will used for calculating commission and showing numbers', 'rtwalwm-wp-wc-affiliate-program' ); ?></div>
					</td>
			 	</tr>
				 <tr>
						<th>
						<?php esc_html_e( 'Hide Login Form', 'rtwalwm-wp-wc-affiliate-program' ); ?>
						</th>
						<td class="tr2">
						
						<input id="rtwwwap_active_login_shortcode" type="checkbox" name="" />
								
				
						<div class="descr"><?php esc_html_e( 'Select this if you want to hide Login form on Affiliate Page', 'rtwalwm-wp-wc-affiliate-program' ); ?></div>
						</td>
				</tr>
				<tr>
						<th>
						<?php esc_html_e( 'Hide Registration Form', 'rtwalwm-wp-wc-affiliate-program' ); ?>
						</th>
						<td class="tr2">
						
						<input id="rtwwwap_active_registration_shortcode" type="checkbox" name=""  />
								
				
						<div class="descr"><?php esc_html_e( 'Select this if you want to hide Registration form on Affiliate Page', 'rtwalwm-wp-wc-affiliate-program' ); ?></div>
						</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Affiliate Verification', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
					<td class="tr2">
						<p>
							<span class="rtwalwm-custom-radio">
								<input id="radio-1" type="radio" value="" disabled/><?php esc_html_e( 'On', 'rtwalwm-wp-wc-affiliate-program' ); ?>
								<label for="radio-1"></label>
							</span>
						</p>
						<p>
							<span class="rtwalwm-custom-radio">
								
								<input id="radio-2" type="radio"  value="" disabled/><?php esc_html_e( 'Off', 'rtwalwm-wp-wc-affiliate-program' ); ?>
								<label for="radio-2"></label>
							</span>
						</p>
						<div class="descr"><?php esc_html_e( 'Activate this feature if you want to check whether this user can be affiliate or not', 'rtwalwm-wp-wc-affiliate-program' );?></div>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Mail to Admin', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
					<td class="tr2">
						<p>
							<span class="rtwalwm-custom-radio">
								<input id="radio-11" type="radio"  value="" disabled /><?php esc_html_e( 'On', 'rtwalwm-wp-wc-affiliate-program' ); ?>
								<label for="radio-11"></label>
							</span>
						</p>
						<p>
							<span class="rtwalwm-custom-radio">
								
								<input id="radio-12" type="radio" value="" disabled/><?php esc_html_e( 'Off', 'rtwalwm-wp-wc-affiliate-program' ); ?>
								<label for="radio-12"></label>
							</span>
						</p>
						<div class="descr"><?php esc_html_e( 'Activate this feature if you want to get mails when a commission is generated', 'rtwalwm-wp-wc-affiliate-program' );?></div>
					</td>
				</tr>
				<tr>
						<th><?php esc_html_e( 'Slug in Affiliate Link', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
						<td class="tr2">
							<input type="text" disabled/>
							<div class="descr"><?php esc_html_e( 'Replace rtwwwap_aff slug from Affiliate link', 'rtwalwm-wp-wc-affiliate-program' );?></div>
						</td>
					</tr>
				<tr>
					<th><?php esc_html_e( 'Cookie Expiration', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
					<td class="tr2">
						<input type="number" min="0"  value="" disabled/>
						<div class="descr"><?php esc_html_e( 'Enter Days after which referral cookie will expire ( Note: 0 days means cookie will expire when the browser will be closed )', 'rtwalwm-wp-wc-affiliate-program' );?></div>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'QR code for referral links', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
					<td class="tr2">
						<p>
							<span class="rtwalwm-custom-radio">
								<input id="radio-3" type="radio" name="" value="" disabled /><?php esc_html_e( 'On', 'rtwalwm-wp-wc-affiliate-program' ); ?>
								<label for="radio-3"></label>
							</span>
						</p>
						<p>
							<span class="rtwalwm-custom-radio">
							
								<input id="radio-4" type="radio" name="" value="" disabled/><?php esc_html_e( 'Off', 'rtwalwm-wp-wc-affiliate-program' ); ?>
								<label for="radio-4"></label>
							</span>
						</p>
					</td>
				</tr>
				<tr>
					<th>
						<?php esc_html_e( 'Custom css', 'rtwalwm-wp-wc-affiliate-program' ); ?>
					</th>
					<td class="tr2">
						<textarea rows="4" class="rtwalwm_textarea_css" name=""  disabled></textarea>
						<div class="descr"><?php esc_html_e( 'Write custom css for frontend', 'rtwalwm-wp-wc-affiliate-program' );?></div>
					</td>
				</tr>
			</tbody>
		</table>

		<!-- Label Table -->
		<table class="rtwalwm-table form-table rtwalwm-hide-table" id="rtwalwm_extra_label">
			<tbody>
			<tr>
					<th>
						<?php esc_html_e( 'Title for button "Become an Affiliate"', 'rtwalwm-wp-wc-affiliate-program' ); ?>
					</th>
					<td class="tr2">
						<input type="text" class="rtwalwm_admin_input_text" id="rtwwwap_become_title" name="rtwwwap_extra_features_opt[become_title]" value="<?php echo isset( $rtwalwm_extra_features[ 'become_title' ] ) ? $rtwalwm_extra_features[ 'become_title' ] : ''; ?>"  placeholder="<?php esc_html_e( 'Enter Become an Affiliate button Title', 'rtwalwm-wp-wc-affiliate-program' ); ?>"  />
					</td>
				</tr>	<div class="rtwalwm-popup-wrapper">
		
			  <h3 class="rtwalwm-popup-heading"><?php esc_html_e( 'Add Manual Referral', 'rtwalwm-wp-wc-affiliate-program' ); ?></h3>
				
	
				<tr>
					<th>
						<?php esc_html_e( 'Title for Section "Benefits for being an Affiliate"', 'rtwalwm-wp-wc-affiliate-program' ); ?>
					</th>
					<td class="tr2">
						<input type="text" class="rtwalwm_admin_input_text" id="rtwwwap_benefits_title" name="rtwwwap_extra_features_opt[benefits_title]" value="<?php echo isset( $rtwalwm_extra_features[ 'benefits_title' ] ) ? $rtwalwm_extra_features[ 'benefits_title' ] : ''; ?>" placeholder="<?php esc_html_e( 'Enter Benefits Section Title', 'rtwalwm-wp-wc-affiliate-program' ); ?>" />
					</td>
				</tr>
				<tr class="rtwalwm_benefits">
					<th>
						<?php esc_html_e( 'Benefits for being an Affiliate', 'rtwalwm-wp-wc-affiliate-program' ); ?>
					</th>
					<td class="tr2">
						<?php
							$rtwalwm_default_benefits = sprintf( "<ul><li>%s</li><li>%s</li><li>%s</li></ul>", esc_html__( 'Earn extra money just by marketing our products with our affiliate tools', 'rtwalwm-wp-wc-affiliate-program' ), esc_html__( 'Earn wallet amount to buy products on our site', 'rtwalwm-wp-wc-affiliate-program' ), esc_html__( 'Signup Bonus when someone signup from your shared link', 'rtwalwm-wp-wc-affiliate-program' ));

							// for frontend wp_editor content
							$rtwalwm_extra_features_wp_editor = isset( $rtwalwm_extra_features[ 'aff_benefits' ] ) ? $rtwalwm_extra_features[ 'aff_benefits' ] : $rtwalwm_default_benefits;

							$rtwalwm_extra_features_wp_editor = html_entity_decode( $rtwalwm_extra_features_wp_editor );
							$rtwalwm_extra_features_wp_editor = stripslashes( $rtwalwm_extra_features_wp_editor );
							$rtwalwm_extra_features_editor_id 	= 'rtwmlbonusfrontendeditor';
							$rtwalwm_extra_features_settings 	=  array(
														'wpautop' 		=> false,
													'media_buttons' => false,
													'textarea_name' => 'rtwwwap_extra_features_opt[aff_benefits]',
													'textarea_rows' => 7
												
											);
							wp_editor( $rtwalwm_extra_features_wp_editor, $rtwalwm_extra_features_editor_id, $rtwalwm_extra_features_settings );
						?>
						<div class="descr"><?php esc_html_e( 'These benefits will be shown to the users, so that they will become an Affiliate', 'rtwalwm-wp-wc-affiliate-program' );?></div>
					</td>
				</tr>
				<tr>
					<th>
						<?php esc_html_e( 'Label for Tab "Overview"', 'rtwalwm-wp-wc-affiliate-program' ); ?>
					</th>
					<td class="tr2">
						<input type="text" class="rtwalwm_admin_input_text" name="" value="" placeholder="<?php esc_html_e( 'Enter Overview Label', 'rtwalwm-wp-wc-affiliate-program' ); ?>" disabled/>
						<div class="descr"><?php esc_html_e( 'Enter title for Tab Overview in Affilate Dashboard Panel', 'rtwalwm-wp-wc-affiliate-program' );?></div>
					</td>
				</tr>
				<tr>
					<th>
						<?php esc_html_e( 'Label for Tab "Commissions"', 'rtwalwm-wp-wc-affiliate-program' ); ?>
					</th>
					<td class="tr2">
						<input type="text" class="rtwalwm_admin_input_text" name="" value="" placeholder="<?php esc_html_e( 'Enter Commission Label', 'rtwalwm-wp-wc-affiliate-program' ); ?>" disabled/>
						<div class="descr"><?php esc_html_e( 'Enter title for Tab Commission in Affilate Dashboard Panel', 'rtwalwm-wp-wc-affiliate-program' );?></div>
					</td>
				</tr>
				<tr>
					<th>
						<?php esc_html_e( 'Label for Tab "Affiliate Tools"', 'rtwalwm-wp-wc-affiliate-program' ); ?>
					</th>
					<td class="tr2">
						<input type="text" class="rtwalwm_admin_input_text" name="" value="" placeholder="<?php esc_html_e( 'Enter Affilate Tools Label', 'rtwalwm-wp-wc-affiliate-program' ); ?>"disabled />
						<div class="descr"><?php esc_html_e( 'Enter title for Tab Affiliate Tools in Affilate Dashboard Panel', 'rtwalwm-wp-wc-affiliate-program' );?></div>
					</td>
				</tr>
				<tr>
					<th>
						<?php esc_html_e( 'Label for Tab "Download"', 'rtwalwm-wp-wc-affiliate-program' ); ?>
					</th>
					<td class="tr2">
						<input type="text" class="rtwalwm_admin_input_text" name="" value="" placeholder="<?php esc_html_e( 'Enter Download Label', 'rtwalwm-wp-wc-affiliate-program' ); ?>" disabled/>
						<div class="descr"><?php esc_html_e( 'Enter title for Tab Download in Affilate Dashboard Panel', 'rtwalwm-wp-wc-affiliate-program' );?></div>
					</td>
				</tr>
				<tr>
					<th>
						<?php esc_html_e( 'Label for Tab "Payout"', 'rtwalwm-wp-wc-affiliate-program' ); ?>
					</th>
					<td class="tr2">
						<input type="text" class="rtwalwm_admin_input_text" name="" value="" placeholder="<?php esc_html_e( 'Enter Payout Label', 'rtwalwm-wp-wc-affiliate-program' ); ?>" disabled />
						<div class="descr"><?php esc_html_e( 'Enter title for Tab Payout in Affilate Dashboard Panel', 'rtwalwm-wp-wc-affiliate-program' );?></div>
					</td>
				</tr>
				<tr>
					<th>
						<?php esc_html_e( 'Label for Tab "Profile"', 'rtwalwm-wp-wc-affiliate-program' ); ?>
					</th>
					<td class="tr2">
						<input type="text" class="rtwalwm_admin_input_text" name="" placeholder="<?php esc_html_e( 'Enter Profile Label', 'rtwalwm-wp-wc-affiliate-program' ); ?>" disabled/>
						<div class="descr"><?php esc_html_e( 'Enter title for Tab Profile in Affilate Dashboard Panel', 'rtwalwm-wp-wc-affiliate-program' );?></div>
					</td>
				</tr>
			</tbody>
		</table>

		<!-- Bonus Table -->
		<table class="rtwalwm-table form-table rtwalwm-hide-table" id="rtwalwm_extra_bonus">
			<tbody>
				<tr>
					<th><?php esc_html_e( 'Signup Bonus Type', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
					<td class="tr2">
						<p>
							<span class="rtwalwm-custom-radio">
								<input id="radio-9" type="radio" name="" value="" disabled /><?php esc_html_e( 'Referral Code', 'rtwalwm-wp-wc-affiliate-program' ); ?>
								<label for="radio-9"></label>
							</span>
						</p>
						<p>
							<span class="rtwalwm-custom-radio">

								<input id="radio-10" type="radio" name="" value="" disabled /><?php esc_html_e( 'Cookie ( Default )', 'rtwalwm-wp-wc-affiliate-program' ); ?>
								<label for="radio-10"></label>
							</span>
						</p>
						<div class="descr"><?php esc_html_e( '[ Note : When "REFFERAL CODE" is selected as Signup Bonus Type then "REFFERAL LINK(s)" will not be used for Commission Generation, Signup Bonus and adding member to MLM chain ]', 'rtwalwm-wp-wc-affiliate-program' );?></div>
					</td>
				</tr>
				<tr>
					<th>
						<?php esc_html_e( 'Signup Bonus', 'rtwalwm-wp-wc-affiliate-program' ); ?>
					</th>
					<td class="tr2">
						<input type="number" min="0" name="" value="" disabled />
						<div class="descr"><?php esc_html_e( 'Enter Amount to be given for referral signup (By default 0)', 'rtwalwm-wp-wc-affiliate-program' );?></div>
					</td>
				</tr>
				
					<tr>
						<th>
							<?php esc_html_e( 'Performance Bonus', 'rtwalwm-wp-wc-affiliate-program' ); ?>
						</th>
						<td class="tr2">
							<table>
								<thead>
									<th>
										<?php esc_html_e( 'Total Sale amount to unlock achievement', 'rtwalwm-wp-wc-affiliate-program' ); ?>
									</th>
									<th>
										<?php esc_html_e( 'Incentive', 'rtwalwm-wp-wc-affiliate-program' ); ?>
									</th>
									<th>
										<?php esc_html_e( 'Add/Remove row', 'rtwalwm-wp-wc-affiliate-program' ); ?>
									</th>	<div class="rtwalwm-popup-wrapper">
	
								</thead>
								<tbody class="rtwalwm_tbody rtwalwm_perf_table">
									
								
									<tr>
										<td>
											<input class="rtwalwm_sale_amount" type="number" min="1" name="" value="" disabled  />
										</td>
										<td>
											<input class="rtwalwm_incentive" type="number" min="0" name="" value="" disabled/>
										</td>
										<td>
											<span class="dashicons dashicons-plus-alt rtwalwm_add_new_row_perf" disabled></span>
											<span class="dashicons dashicons-dismiss rtwalwm_remove_row_perf" disabled></span>
										</td>
									</tr>
							
								</tbody>
							</table>
						</td>
					</tr>
				


				<tr>
					<th><?php esc_html_e( 'Social Media Share Buttons', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
					<td class="tr2">
						<p>
							<span class="rtwalwm-checkbox">
				    			<input id="rtwalwm_checkbox_social_share" type="checkbox" name="" disabled />
				    			<label for="rtwalwm_checkbox_social_share"></label>
				    		</span>
				    	</p>
					</td>
				</tr>
				
				<tr class="rtwalwm_social_share_bonus">
					<th>
						<?php esc_html_e( 'Sharing Bonus', 'rtwalwm-wp-wc-affiliate-program' ); ?>
					</th>
					<td class="tr2">
						<input type="number" min="0" step="0.01" name="" value="" disabled/>
						<div class="descr"><?php esc_html_e( 'Enter Amount to be given for Sharing a product on social media (By default 0)', 'rtwalwm-wp-wc-affiliate-program' );?></div>
					</td>
				</tr>
				<tr class="rtwalwm_social_share_bonus_limit">
					<th>
						<?php esc_html_e( 'Sharing Bonus Limit', 'rtwalwm-wp-wc-affiliate-program' ); ?>
					</th>
					<td class="tr2">
						<table>
							<thead>
								<th>
									<?php esc_html_e( 'Time Limit', 'rtwalwm-wp-wc-affiliate-program' ); ?>
								</th>
								<th>
									<?php esc_html_e( 'Amount Limit', 'rtwalwm-wp-wc-affiliate-program' ); ?>
								</th>
							</thead>
							<tbody class="rtwalwm_tbody">
								<tr>
									<td>
									
										<select class="rtwalwm_select2_sharing_bonus_time_limit" id="" name="" >
											<option value="" disabled>
												<?php esc_html_e( 'No Limit', 'rtwalwm-wp-wc-affiliate-program' ); ?>
											</option>
											<option value="" disabled>
												<?php esc_html_e( 'Daily Limit', 'rtwalwm-wp-wc-affiliate-program' ); ?>
											</option>
											<option value="" disabled>
												<?php esc_html_e( 'Weekly Limit', 'rtwalwm-wp-wc-affiliate-program' ); ?>
											</option>
											<option value="" disabled>
												<?php esc_html_e( 'Monthly Limit', 'rtwalwm-wp-wc-affiliate-program' ); ?>
											</option>
										</select>
									</td>
									<td>
										<input type="number" class="sharing_bonus_amount_limit" min="0" step="0.01" name="" value="" disabled/>
									</td>
								</tr>
							</tbody>
						</table>
						<div class="descr"><?php esc_html_e( 'Enter Time and Amount after which Affiliate will not get the share bonus', 'rtwalwm-wp-wc-affiliate-program' );?></div>
					</td>
				</tr>
			</tbody>
		</table>
		<!-- Bonus Table -->
		<table class="rtwalwm-table form-table rtwalwm-hide-table" id="rtwalwm_extra_payment">
			<tbody>
				<tr>
					<th><?php esc_html_e( 'Activate Paypal', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
					<td class="tr2">
						<p>
							<span class="rtwalwm-checkbox">
				    			<input id="rtwalwm_checkbox_paypal" type="checkbox" name="" disabled />
				    			<label for="rtwalwm_checkbox_paypal"></label>
				    		</span>
				    	</p>
				    	<div class="rtwalwm-payment-wrapper">
							<span class="rtwalwm-custom-radio">
								<input class="rtwalwm_paypal_live_radio" id="radio-5" type="radio" name="" value="" disabled><?php esc_html_e( 'Paypal Live', 'rtwalwm-wp-wc-affiliate-program' ); ?>
								<label for="radio-5"></label>
							</span>
							<div class="">
								<input id="rtwalwm_paypal_live_id" type="text" name="" value="" placeholder="<?php esc_attr_e( 'Client ID', 'rtwalwm-wp-wc-affiliate-program' ) ?>" disabled/>
							</div>
							<div class="">
								<input id="rtwalwm_paypal_live_secret" type="text" name="" value="" placeholder="<?php esc_attr_e( 'Client Secret', 'rtwalwm-wp-wc-affiliate-program' ) ?>" disabled/>
							</div>
				    	</div>
				    	<div class="rtwalwm-payment-wrapper">
							<span class="rtwalwm-custom-radio">
								<input class="rtwalwm_paypal_sandbox_radio" id="radio-6" type="radio" name="" value="" disabled /><?php esc_html_e( 'Paypal Sandbox', 'rtwalwm-wp-wc-affiliate-program' ); ?>
								<label for="radio-6"></label>
							</span>
							<span class="">
								<input id="rtwalwm_paypal_sandbox_id" type="text" name="" value="" placeholder="<?php esc_attr_e( 'Client ID', 'rtwalwm-wp-wc-affiliate-program' ) ?>" disabled/>
							</span>
							<span class="">
								<input id="rtwalwm_paypal_sandbox_secret" type="text" name="" value="" placeholder="<?php esc_attr_e( 'Client Secret', 'rtwalwm-wp-wc-affiliate-program' ) ?>" disabled/>
							</span>
				    	</div>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Activate Stripe', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
					<td class="tr2">
						<p>
							<span class="rtwalwm-checkbox">
				    			<input id="rtwalwm_checkbox_stripe" type="checkbox" name="" disabled />
				    			<label for="rtwalwm_checkbox_stripe"></label>
				    		</span>
				    	</p>
				    	<div class="rtwalwm-payment-wrapper">
							<span class="rtwalwm-custom-radio">
								<input class="rtwalwm_stripe_live_radio" id="radio-7" type="radio" name="" value=""  disabled/><?php esc_html_e( 'Stripe Live', 'rtwalwm-wp-wc-affiliate-program' ); ?>
								<label for="radio-7"></label>
							</span>
							<div class="">
								<input id="rtwalwm_stripe_live_id" type="text" name="" value="" placeholder="<?php esc_attr_e( 'Publishable Key', 'rtwalwm-wp-wc-affiliate-program' ) ?>" disabled />
							</div>
							<div class="">
								<input id="rtwalwm_stripe_live_secret" type="text" name="" value="" placeholder="<?php esc_attr_e( 'Secret Key', 'rtwalwm-wp-wc-affiliate-program' ) ?>" disabled/>
							</div>
				    	</div>
				    	<div class="rtwalwm-payment-wrapper">
							<span class="rtwalwm-custom-radio">
								<input class="rtwalwm_stripe_sandbox_radio" id="radio-8" type="radio" name="" value="" disabled/><?php esc_html_e( 'Stripe Sandbox', 'rtwalwm-wp-wc-affiliate-program' ); ?>
								<label for="radio-8"></label>
							</span>
							<span class="">
								<input id="rtwalwm_stripe_sandbox_id" type="text" name="" value="" placeholder="<?php esc_attr_e( 'Publishable Key', 'rtwalwm-wp-wc-affiliate-program' ) ?>" disabled/>
							</span>
							<span class="">
								<input id="rtwalwm_stripe_sandbox_secret" type="text" name="" value="" placeholder="<?php esc_attr_e( 'Secret Key', 'rtwalwm-wp-wc-affiliate-program' ) ?>" disabled/>
							</span>
				    	</div>
					</td>
				</tr>

				<tr>
					<th><?php esc_html_e( 'Activate Paystack', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
					<td class="tr2">
						
				    	<div class="rtwalwm-payment-wrapper">
							
							<span class="">
								<input id="rtwalwm_stripe_sandbox_id" type="text" name="" value="" placeholder="<?php esc_attr_e( 'Public Key', 'rtwalwm-wp-wc-affiliate-program' ) ?>" disabled/>
							</span>
							<span class="">
								<input id="rtwalwm_stripe_sandbox_secret" type="text" name="" value="" placeholder="<?php esc_attr_e( 'Secret Key', 'rtwalwm-wp-wc-affiliate-program' ) ?>" disabled/>
							</span>
				    	</div>
					</td>
				</tr>

			</tbody>
		</table>
		<!-- Notification table -->
		<table class="rtwalwm-table form-table rtwalwm-hide-table" id="rtwalwm_extra_notification">
				<tbody>
					<tr>
						<td><input type="button" class="rtwalwm-button rtwalwm_add_notification" value="<?php esc_html_e( 'Add Notification', 'rtwalwm-wp-wc-affiliate-program' ); ?>"></td>
					</tr>
					<tr>
						<td class="tr2">
							<table class="rtwalwm_notification_table">
								<thead>
									<th>
										<?php esc_html_e( 'Title', 'rtwalwm-wp-wc-affiliate-program' ); ?>
									</th>
									<th>
										<?php esc_html_e( 'View / Edit', 'rtwalwm-wp-wc-affiliate-program' ); ?>
									</th>
									<th>
										<?php esc_html_e( 'Remove ', 'rtwalwm-wp-wc-affiliate-program' ); ?>
									</th>
								</thead>
								<tbody class="rtwalwm_noti_main">
									<?php
									$rtwalwm_noti_option = get_option("rtwalwm_noti_arr");
								
									if($rtwalwm_noti_option)
									{
										foreach ($rtwalwm_noti_option as $key => $value) 
										{
										?>
											<tr>
												
												<td><?php echo  esc_attr($value['title'])?></td>
												<td>
													<span><i class="fa fa-eye rtwalwm_view_edit_icon" data-key="<?php echo $key?>" aria-hidden="true" data-noti_title="<?php echo $value['title'] ?>" data-noti_content="<?php echo $value['content'] ?>"></i></span></td>
												<td><i class="far fa-trash-alt rtwalwm_delete rtwalwm_view_delete_icon" data-key="<?php echo $key?>"></i></td>
											</tr>
										<?php 
										}
									}
									?>	
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
		</table>


		<!-- Rank table -->
	
		<table class="rtwalwm-table form-table rtwalwm-hide-table" id="rtwalwm_extra_rank">
			<tbody>
				<tr>
					<td class="rtwwwap_new_rank_text">
						<input type="button" value="<?php esc_html_e( 'Add New Rank', 'rtwalwm-wp-wc-affiliate-program' ); ?>" class="rtwwwap-button" id="rtwwwap_rank_requirements">
						
					</td>
				</tr>
				<tr>
					<td class="tr2">
						<table class="rtwwwap_notification_table">
							<thead class="rtwwwap_set_diff_width_th">
								<th>
									<?php  esc_html_e( 'SN', 'rtwalwm-wp-wc-affiliate-program' ); ?>
								</th>
								<th>
									<?php  esc_html_e( 'Rank Name', 'rtwalwm-wp-wc-affiliate-program' ); ?>
								</th>
								<th>
								<?php  esc_html_e( 'Rank Priority', 'rtwalwm-wp-wc-affiliate-program' ); ?>
								</th>
								
								<th>
									<?php  esc_html_e( 'Rank Commission', 'rtwalwm-wp-wc-affiliate-program' ); ?>
								</th>
								<th>
								<?php  esc_html_e( 'Actions ', 'rtwalwm-wp-wc-affiliate-program' ); ?>
								</th>
								<th>
									<?php  esc_html_e( 'Date ', 'rtwalwm-wp-wc-affiliate-program' ); ?>
								</th>
							</thead>

							<tbody class="rtwwwap_noti_main">
								
								<tr>
									<td>
										<?php  esc_html_e( 1 , 'rtwalwm-wp-wc-affiliate-program' ); ?>
									</td>
									<td>
										<?php  esc_html_e( 'Bronze', 'rtwalwm-wp-wc-affiliate-program' ); ?>
									</td>
									<td>
										<?php  esc_html_e( 1, 'rtwalwm-wp-wc-affiliate-program' ); ?>
									</td>
									<td>
										<?php  esc_html_e( 10, 'rtwalwm-wp-wc-affiliate-program' ); ?>
									</td>
									<td id ="edit_rank" class="rtwwwap_action">
										<button type = "button"  id ="rtwwwap_edit_reqmnt" class = "rtwwwap_edit_reqmnt"><?php  esc_html_e( "edit" , 'rtwalwm-wp-wc-affiliate-program' ); ?></button>
										<button type = "button"   id ="rtwwwap_delete_reqmnt" class = "rtwwwap_delete_reqmnt"><?php  esc_html_e( "delete" , 'rtwalwm-wp-wc-affiliate-program' ); ?></button>
									</td>
									<td>
										<?php  esc_html_e( '24-11-22', 'rtwalwm-wp-wc-affiliate-program' ); ?>
									</td>
								</tr>

								<tr>
									<td>
										<?php  esc_html_e( 2 , 'rtwalwm-wp-wc-affiliate-program' ); ?>
									</td>
									<td>
										<?php  esc_html_e( 'Silver', 'rtwalwm-wp-wc-affiliate-program' ); ?>
									</td>
									<td>
										<?php  esc_html_e( 4, 'rtwalwm-wp-wc-affiliate-program' ); ?>
									</td>
									<td>
										<?php  esc_html_e( 15, 'rtwalwm-wp-wc-affiliate-program' ); ?>
									</td>
									<td id ="edit_rank" class="rtwwwap_action">
										<button type = "button"  id ="rtwwwap_edit_reqmnt" class = "rtwwwap_edit_reqmnt"><?php  esc_html_e( "edit" , 'rtwalwm-wp-wc-affiliate-program' ); ?></button>
										<button type = "button"   id ="rtwwwap_delete_reqmnt" class = "rtwwwap_delete_reqmnt"><?php  esc_html_e( "delete" , 'rtwalwm-wp-wc-affiliate-program' ); ?></button>
									</td>
									<td>
										<?php  esc_html_e( '26-11-22', 'rtwalwm-wp-wc-affiliate-program' ); ?>
									</td>
								</tr>

								<tr>
									<td>
										<?php  esc_html_e( 3 , 'rtwalwm-wp-wc-affiliate-program' ); ?>
									</td>
									<td>
										<?php  esc_html_e( 'Gold', 'rtwalwm-wp-wc-affiliate-program' ); ?>
									</td>
									<td>
										<?php  esc_html_e( 5, 'rtwalwm-wp-wc-affiliate-program' ); ?>
									</td>
									<td>
										<?php  esc_html_e( 24, 'rtwalwm-wp-wc-affiliate-program' ); ?>
									</td>
									<td id ="edit_rank" class="rtwwwap_action">
										<button type = "button"  id ="rtwwwap_edit_reqmnt" class = "rtwwwap_edit_reqmnt"><?php  esc_html_e( "edit" , 'rtwalwm-wp-wc-affiliate-program' ); ?></button>
										<button type = "button"   id ="rtwwwap_delete_reqmnt" class = "rtwwwap_delete_reqmnt"><?php  esc_html_e( "delete" , 'rtwalwm-wp-wc-affiliate-program' ); ?></button>
									</td>
									<td>
										<?php  esc_html_e( '27-11-22', 'rtwalwm-wp-wc-affiliate-program' ); ?>
									</td>
								</tr>
									
							</tbody>
							
						</table>
					</td>
				</tr>
			</tbody>
		</table>


			<?php 			
				$rtwalwm_exrta_tab = '' ;
				$rtwwwa_html = apply_filters('rtwalwm_social_login_settings',$rtwalwm_exrta_tab)
			?>
		</div>
	</div>
</div>
<div class="rtwalwm-popup-wrapper">
		<div class="rtwalwm-popup-content">
			  <h3 class="rtwalwm-popup-heading"><?php esc_html_e( 'Notification Details', 'rtwalwm-wp-wc-affiliate-program' ); ?></h3>
				<div class="rtwalwm-popup-row">
					<div>
						<label class="rtwalwm_notification_title" for="rtwalwm_notification_title_inpt"><?php esc_html_e( 'enter Notification title', 'rtwalwm-wp-wc-affiliate-program' ); ?></label>
						<input type="text" class="rtwalwm_notification_title_inpt" id="rtwalwm_notification_title_inpt">
						<label class="rtwalwm_notification_title"><?php esc_html_e( 'enter Notification message', 'rtwalwm-wp-wc-affiliate-program' ); ?></label>
					 	<textarea rows="4" cols="65" maxlength="100" class="rtwalwm_notification_textarea" placeholder='<?php esc_html_e("Enter your reason here within 100 words...", "rtwalwm-wp-wc-affiliate-program" )?>' ></textarea>
					</div>
				</div>
				<div class="rtwalwm-popup-footer">
					<input type="button" value="<?php esc_html_e( 'Save', 'rtwalwm-wp-wc-affiliate-program' ); ?>" class="rtwalwm-button" id="rtwalwm_save_notification">
					<input type="reset" name="" value="<?php esc_html_e( 'Cancel', 'rtwalwm-wp-wc-affiliate-program' ); ?>" class="rtwalwm-button rtwalwm-button-reset" id="rtwalwm_cancle_add_notification">
				</div>
		</div>
	</div>
