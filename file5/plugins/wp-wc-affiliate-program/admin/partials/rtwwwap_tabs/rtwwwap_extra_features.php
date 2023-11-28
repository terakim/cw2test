<?php

	settings_fields( 'rtwwwap_extra_features');

	$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );
	
	$rtwwwap_login_page_title = isset($rtwwwap_extra_features['login_shortcode_page']) ? $rtwwwap_extra_features['login_shortcode_page'] : '';

	require_once( RTWWWAP_DIR.'includes/rtwaffiliatehelper.php' );

	if( RTWWWAP_IS_WOO != 1 ){
		$rtwwwap_curr_obj 		= new RtwAffiliateHelper();
		$rtwwwap_all_currencies = $rtwwwap_curr_obj->RtwWwapCurrencies();
	}
	$rtwwwap_section_show = isset($_GET['rtwwwap_section']) ? $_GET['rtwwwap_section'] : 'general';

	?>
	<div class="rtwwwap-extra-features-main-wrapper">

	<div class="rtwwwap-extra-features-wrap">
		<ul>
			<li class="<?php echo ($rtwwwap_section_show == 'general') ? 'active': '' ?>" id="rtwwwap_common_class" data-target="rtwwwap_extra_general"> <a href="javascript:void(0);"><?php esc_html_e( 'General', 'rtwwwap-wp-wc-affiliate-program' ); ?></a> </li>
			<li class="<?php echo ($rtwwwap_section_show == 'label') ? 'active': '' ?>" data-target="rtwwwap_extra_label" id="rtwwwap_common_class"> <a href="javascript:void(0);"><?php esc_html_e( 'Labels', 'rtwwwap-wp-wc-affiliate-program' ); ?></a> </li>
			<li class="<?php echo ($rtwwwap_section_show == 'bonus') ? 'active': '' ?>" data-target="rtwwwap_extra_bonus" id="rtwwwap_common_class"> <a href="javascript:void(0);"><?php esc_html_e( 'Bonus', 'rtwwwap-wp-wc-affiliate-program' ); ?></a> </li>
			<li class="<?php echo ($rtwwwap_section_show == 'payment') ? 'active': '' ?>" data-target="rtwwwap_extra_payment" id="rtwwwap_common_class"> <a href="javascript:void(0);"><?php esc_html_e( 'Payment', 'rtwwwap-wp-wc-affiliate-program' ); ?></a> </li>
			<li class="<?php echo ($rtwwwap_section_show == 'notification') ? 'active': '' ?>" data-target="rtwwwap_extra_notification" id="rtwwwap_common_class"> <a href="javascript:void(0);"><?php esc_html_e( 'Notification', 'rtwwwap-wp-wc-affiliate-program' );?></a></li>
			<li class="<?php echo ($rtwwwap_section_show == 'rank') ? 'active': '' ?>" data-target="rtwwwap_extra_rank" class="rtwwwap_class_for_email <?php echo ($rtwwwap_section_show == 'rank') ? 'active': '' ?>" id="rtwwwap_common_class"> <a href="javascript:void(0);"><?php  esc_html_e( 'Rank', 'rtwwwap-wp-wc-affiliate-program' ); ?></a></li>
			<?php 
			$rtwwwap_exrta_tab = '' ;
			$rtwwwa_html = apply_filters('rtwwwap_social_login_tab',$rtwwwap_exrta_tab);
			?>
		
		
		</ul>
	</div>
	<div class="rtwwwap-extra-table-wrapper">
			<table class="rtwwwap-table form-table <?php echo ($rtwwwap_section_show == 'general') ? 'rtwwwap-show': 'rtwwwap-hide-table' ?>" id="rtwwwap_extra_general">
				<tbody>
					<tr>
						<th><?php esc_html_e( 'Select Affiliate Page', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
						<td class="tr2">
							<?php
								$rtwwwap_affiliate_page_selected = get_option( 'rtwwwap_affiliate_page_id' );
								$rtwwwap_page_args = array(
														'post_type'    => 'page',
														'post_status'  => 'publish'
													);
								$rtwwwap_all_pages = get_pages( $rtwwwap_page_args );
							?>
							<p><select class="rtwwwap_select2_page" id="" name="rtwwwap_extra_features_opt[page]" >
								<option value="">
									<?php esc_html_e( "Select a Page", 'rtwwwap-wp-wc-affiliate-program' ); ?>
								</option>
								<?php
									foreach( $rtwwwap_all_pages as $rtwwwap_page_key => $rtwwwap_page_value )
									{
								?>
										<option value="<?php echo esc_attr($rtwwwap_page_value->ID); ?>" <?php selected( $rtwwwap_affiliate_page_selected, $rtwwwap_page_value->ID ) ?> >
											<?php echo esc_html( $rtwwwap_page_value->post_title ); ?>
										</option>
								<?php
									}
								?>
							</select>
							
							
							</p>
							<br>
							<div class="descr"><?php printf( '%s - %s', esc_html_e( 'Use the following shortcode on the selected page', 'rtwwwap-wp-wc-affiliate-program' ), '[rtwwwap_affiliate_page]' ); ?></div>
						</td>
					</tr>

					<tr>
						<th><?php esc_html_e( 'Affiliate Login page', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
						<td class="tr2">
							<?php
								$rtwwwap_login_page_selected = get_option( 'rtwwwap_login_page_id' );
								$rtwwwap_page_args = array(
														'post_type'    => 'page',
														'post_status'  => 'publish'
													);
								$rtwwwap_all_pages = get_pages( $rtwwwap_page_args );
							?>
							<p><select class="rtwwwap_select2_page" id="" name="rtwwwap_extra_features_opt[login_page_id]" >
								<option value="">
									<?php esc_html_e( "Select a Page", 'rtwwwap-wp-wc-affiliate-program' ); ?>
								</option>
								<?php
									foreach( $rtwwwap_all_pages as $rtwwwap_login_page_key => $rtwwwap_login_page_value )
									{
								?>
										<option value="<?php echo esc_attr($rtwwwap_login_page_value->ID); ?>" <?php selected( $rtwwwap_login_page_selected, $rtwwwap_login_page_value->ID ) ?> >
											<?php echo esc_html( $rtwwwap_login_page_value->post_title ); ?>
										</option>
								<?php
									}
								?>
							</select></p>
							<br>
							<div class="descr"><?php printf( '%s - %s', esc_html_e( 'Select page if your are using Login shortcode seprately', 'rtwwwap-wp-wc-affiliate-program', 'rtwwwap-wp-wc-affiliate-program' ), '[rtwwwap_aff_login_page]' ); ?></div>
						</td>
					</tr>

					<tr>
						<th><?php esc_html_e( 'Affiliate Signup page ', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
						<td class="tr2">
							<?php
								$rtwwwap_register_page_selected = get_option( 'rtwwwap_register_page_id' );
								$rtwwwap_page_args = array(
														'post_type'    => 'page',
														'post_status'  => 'publish'
													);
								$rtwwwap_all_pages = get_pages( $rtwwwap_page_args );
							?>
							<p><select class="rtwwwap_select2_page" id="" name="rtwwwap_extra_features_opt[register_page_id]" >
								<option value="">
									<?php esc_html_e( "Select a Page", 'rtwwwap-wp-wc-affiliate-program' ); ?>
								</option>
								<?php
									foreach( $rtwwwap_all_pages as $rtwwwap_reg_page_key => $rtwwwap_reg_page_value )
									{
								?>
										<option value="<?php echo esc_attr($rtwwwap_reg_page_value->ID); ?>" <?php selected( $rtwwwap_register_page_selected, $rtwwwap_reg_page_value->ID ) ?> >
											<?php echo esc_html( $rtwwwap_reg_page_value->post_title ); ?>
										</option>
								<?php
									}
								?>
							</select></p>
							<br>
							<div class="descr"><?php printf( '%s - %s', esc_html_e( 'Select page if your are using Register shortcode seprately', 'rtwwwap-wp-wc-affiliate-program', 'rtwwwap-wp-wc-affiliate-program' ), '[rtwwwap_aff_reg_page]' ); ?></div>
						</td>
					</tr>

					<tr>
						<th><?php esc_html_e( 'Affiliate Page Template ', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
						<td class="tr2">
							<p><select class="rtwwwap_select2_page" id="" name="rtwwwap_extra_features_opt[affiliate_page_template]">
							<?php
						
								$rtwwwap_select_affiliate_page_temp = isset( $rtwwwap_extra_features[ 'affiliate_page_template' ] ) ? $rtwwwap_extra_features[ 'affiliate_page_template' ]  : 1;
							?>
								<option value="1" <?php selected( $rtwwwap_select_affiliate_page_temp, 1 ); ?>>
									<?php esc_html_e( "Template 1", 'rtwwwap-wp-wc-affiliate-program' ); ?>
								</option>
								<option value="2" <?php selected( $rtwwwap_select_affiliate_page_temp, 2 ); ?>>
									<?php esc_html_e( "Template 2", 'rtwwwap-wp-wc-affiliate-program' ); ?>
								</option>
								<option value="3" <?php selected( $rtwwwap_select_affiliate_page_temp, 3 ); ?>>
									<?php esc_html_e( "Template 3", 'rtwwwap-wp-wc-affiliate-program' ); ?>
								</option>
							</select></p>
							<br>
							<div class="descr"><?php printf( '%s', esc_html_e( 'Select Template for Affiliate Page ','rtwwwap-wp-wc-affiliate-program' )); ?></div>
						</td>
					</tr>

					<?php
								if( RTWWWAP_IS_WOO == 1 ){
									?>
										<tr>
											<th><?php esc_html_e( 'Show under My Account', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
											<td class="tr2">
												<?php
													$rtwwwap_show_in_woo_checked = isset( $rtwwwap_extra_features[ 'show_in_woo' ] ) ? $rtwwwap_extra_features[ 'show_in_woo' ] : 1;
												?>
												<p>
													<span class="rtwwwap-custom-radio">
														<input id="radio-13" type="radio" name="rtwwwap_extra_features_opt[show_in_woo]" value="1"<?php checked( $rtwwwap_show_in_woo_checked, 1 ); ?> /><?php esc_html_e( 'Yes', 'rtwwwap-wp-wc-affiliate-program' ); ?>
														<label for="radio-13"></label>
													</span>
												</p>
												<p>
													<span class="rtwwwap-custom-radio">
														<input id="radio-14" type="radio" name="rtwwwap_extra_features_opt[show_in_woo]" value="0" <?php checked( $rtwwwap_show_in_woo_checked, 0 ); ?> /><?php esc_html_e( 'No', 'rtwwwap-wp-wc-affiliate-program' ); ?>
														<label for="radio-14"></label>
													</span>
												</p>
												<div class="descr"><?php esc_html_e( 'Activate to show under WooCommerce->My Account', 'rtwwwap-wp-wc-affiliate-program' );?></div>
											</td>
										</tr>
									<?php
									}
								else if( RTWWWAP_IS_WOO != 1 )
								{
					?>	
						<tr>
							<th><?php esc_html_e( 'Select Currency', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
							<td class="tr2">
								<?php
									$rtwwwap_affiliate_curr_selected = isset( $rtwwwap_extra_features[ 'currency' ] ) ? $rtwwwap_extra_features[ 'currency' ] : 'USD';
								?>
								<p><select class="rtwwwap_select2_curr" id="" name="rtwwwap_extra_features_opt[currency]" >
									<?php
										foreach( $rtwwwap_all_currencies as $rtwwwap_curr_key => $rtwwwap_curr_value )
										{
									?>
											<option value="<?php echo esc_attr( $rtwwwap_curr_key , 'rtwwwap-wp-wc-affiliate-program'); ?>" <?php selected( $rtwwwap_affiliate_curr_selected, $rtwwwap_curr_key ) ?> >
												<?php echo sprintf( '%s (%s)', esc_html( $rtwwwap_curr_value[ 'rtwwwap_curr_name' ] ), esc_html( $rtwwwap_curr_value[ 'rtwwwap_curr_symbol' ] ) ); ?>
											</option>
									<?php
										}
									?>
								</select></p>
								<br>
								<div class="descr"><?php printf( '%s', esc_html_e( 'By Default USD will be used', 'rtwwwap-wp-wc-affiliate-program' ) ); ?></div>
							</td>
						</tr>
					<?php
						}
					?>
					<?php 
					
						$html1 = "";
						$rtwwwa_html = apply_filters("rtwwwap_extra_setting_tab", $html1);
						echo $rtwwwa_html;
					?>
					<tr>
						<th>
						<?php esc_html_e( 'Hide Login Form', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
						<td class="tr2">
						
						<input id="rtwwwap_active_login_shortcode" type="checkbox" name="rtwwwap_extra_features_opt[rtwwwap_active_login_shortcode]" <?php if( isset( $rtwwwap_extra_features[ 'rtwwwap_active_login_shortcode' ] ) ){ checked( $rtwwwap_extra_features[ 'rtwwwap_active_login_shortcode' ], 'on' ); } ?> />
								
				
						<div class="descr"><?php esc_html_e( 'Select this if you want to hide Login form on Affiliate Page', 'rtwwwap-wp-wc-affiliate-program' ); ?></div>
						</td>
					</tr>
					<tr>
						<th>
						<?php esc_html_e( 'Hide Registration Form', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
						<td class="tr2">
						
						<input id="rtwwwap_active_registration_shortcode" type="checkbox" name="rtwwwap_extra_features_opt[rtwwwap_active_registration_shortcode]" <?php if( isset( $rtwwwap_extra_features[ 'rtwwwap_active_registration_shortcode' ] ) ){ checked( $rtwwwap_extra_features[ 'rtwwwap_active_registration_shortcode' ], 'on' ); } ?> />
								
				
						<div class="descr"><?php esc_html_e( 'Select this if you want to hide Registration form on Affiliate Page', 'rtwwwap-wp-wc-affiliate-program' ); ?></div>
						</td>
					</tr>
					<tr>
						<th>
						<?php esc_html_e( 'Upto Decimal Places', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
						<td class="tr2">
						<input type="text" class="rtwwwap_admin_input_text" id="rtwwwap_decimal_places" name="rtwwwap_extra_features_opt[decimal_places]" value="<?php echo isset( $rtwwwap_extra_features[ 'decimal_places' ] ) ? $rtwwwap_extra_features[ 'decimal_places' ] : 2; ?>" placeholder="<?php esc_html_e( 'Enter Upto decimal places', 'rtwwwap-wp-wc-affiliate-program' ); ?>" />
						<input type="hidden" name="rtwwwap_prev_decimal_place" value="<?php echo isset( $rtwwwap_extra_features[ 'decimal_places' ] ) ? $rtwwwap_extra_features[ 'decimal_places' ] : 2; ?>"
						<br>
						<div class="descr"><?php esc_html_e( 'This decimal places will used for calculating commission and showing numbers', 'rtwwwap-wp-wc-affiliate-program' ); ?></div>
						</td>
					</tr>
					<tr>
						<th>
						<?php esc_html_e( 'Decimal Separator', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
						<td class="tr2">
						<input type="text" class="rtwwwap_admin_input_text" id="rtwwwap_decimal_separator" name="rtwwwap_extra_features_opt[decimal_separator]" value="<?php echo isset( $rtwwwap_extra_features[ 'decimal_separator' ] ) ? $rtwwwap_extra_features[ 'decimal_separator' ] : "."; ?>" placeholder="<?php esc_html_e( 'Enter decimal separator', 'rtwwwap-wp-wc-affiliate-program' ); ?>" />
						<br>
						<div class="descr"><?php esc_html_e( 'Enter decimal Separator', 'rtwwwap-wp-wc-affiliate-program' ); ?></div>
						</td>
					</tr>
					<tr>
						<th>
						<?php esc_html_e( 'Thousand Separator', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
						<td class="tr2">
						<input type="text" class="rtwwwap_admin_input_text" id="rtwwwap_thousand_separator" name="rtwwwap_extra_features_opt[thousand__separator]" value="<?php echo isset( $rtwwwap_extra_features[ 'thousand__separator' ] ) ? $rtwwwap_extra_features[ 'thousand__separator' ] : ","; ?>" placeholder="<?php esc_html_e( 'Enter thousand separator', 'rtwwwap-wp-wc-affiliate-program' ); ?>" />
						<br>
						<div class="descr"><?php esc_html_e( 'Enter Thousand Separator', 'rtwwwap-wp-wc-affiliate-program' ); ?></div>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Affiliate Verification', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
						<td class="tr2">
							<p>
								<span class="rtwwwap-custom-radio">
									<input id="radio-1" type="radio" name="rtwwwap_extra_features_opt[aff_verify]" value="1" <?php isset( $rtwwwap_extra_features[ 'aff_verify' ] ) ? checked( $rtwwwap_extra_features[ 'aff_verify' ], '1' ) : ''; ?> /><?php esc_html_e( 'On', 'rtwwwap-wp-wc-affiliate-program' ); ?>
									<label for="radio-1"></label>
								</span>
							</p>
							<p>
								<span class="rtwwwap-custom-radio">
									<?php
										$rtwwwap_aff_verify_checked = 0;
										if( isset( $rtwwwap_extra_features[ 'aff_verify' ] ) && $rtwwwap_extra_features[ 'aff_verify' ] == 0 ){
											$rtwwwap_aff_verify_checked = 1;
										}
										elseif( !isset( $rtwwwap_extra_features[ 'aff_verify' ] ) ){
											$rtwwwap_aff_verify_checked = 1;
										}
									?>
									<input id="radio-2" type="radio" name="rtwwwap_extra_features_opt[aff_verify]" value="0" <?php checked( $rtwwwap_aff_verify_checked, 1 ); ?> /><?php esc_html_e( 'Off', 'rtwwwap-wp-wc-affiliate-program' ); ?>
									<label for="radio-2"></label>
								</span>
							</p>
							<div class="descr"><?php esc_html_e( 'Activate this feature if you want to check whether this user can be affiliate or not', 'rtwwwap-wp-wc-affiliate-program' );?></div>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Slug in Affiliate Link', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
						<td class="tr2">
							<input type="text" min="10" name="rtwwwap_extra_features_opt[affiliate_slug]" value="<?php echo isset( $rtwwwap_extra_features[ 'affiliate_slug' ] ) ? esc_attr( $rtwwwap_extra_features[ 'affiliate_slug' ] ) : esc_html_e( 'rtwwwap_aff', 'rtwwwap-wp-wc-affiliate-program' ); ?>" />
							<div class="descr"><?php esc_html_e( 'Replace rtwwwap_aff slug from Affiliate link', 'rtwwwap-wp-wc-affiliate-program' );?></div>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Mail to Admin', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
						<td class="tr2">
							<p>
								<span class="rtwwwap-custom-radio">
									<input id="radio-11" type="radio" name="rtwwwap_extra_features_opt[mail_to_admin]" value="1" <?php isset( $rtwwwap_extra_features[ 'mail_to_admin' ] ) ? checked( $rtwwwap_extra_features[ 'mail_to_admin' ], '1' ) : ''; ?> /><?php esc_html_e( 'On', 'rtwwwap-wp-wc-affiliate-program' ); ?>
									<label for="radio-11"></label>
								</span>
							</p>
							<p>
								<span class="rtwwwap-custom-radio">
									<?php
										$rtwwwap_mail_to_admin_checked = 0;
										if( isset( $rtwwwap_extra_features[ 'mail_to_admin' ] ) && $rtwwwap_extra_features[ 'mail_to_admin' ] == 0 ){
											$rtwwwap_mail_to_admin_checked = 1;
										}
										elseif( !isset( $rtwwwap_extra_features[ 'mail_to_admin' ] ) ){
											$rtwwwap_mail_to_admin_checked = 1;
										}
									?>
									<input id="radio-12" type="radio" name="rtwwwap_extra_features_opt[mail_to_admin]" value="0" <?php checked( $rtwwwap_mail_to_admin_checked, 1 ); ?> /><?php esc_html_e( 'Off', 'rtwwwap-wp-wc-affiliate-program' ); ?>
									<label for="radio-12"></label>
								</span>
							</p>
							<div class="descr"><?php esc_html_e( 'Activate this feature if you want to get mails when a commission is generated', 'rtwwwap-wp-wc-affiliate-program' );?></div>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Cookie Expiration', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
						<td class="tr2">
							<input type="number" min="0" name="rtwwwap_extra_features_opt[cookie_time]" value="<?php echo isset( $rtwwwap_extra_features[ 'cookie_time' ] ) ? esc_attr( $rtwwwap_extra_features[ 'cookie_time' ] ) : esc_attr( 0 ); ?>" />
							<div class="descr"><?php esc_html_e( 'Enter Days after which referral cookie will expire ( Note: 0 days means cookie will expire when the browser will be closed )', 'rtwwwap-wp-wc-affiliate-program' );?></div>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'QR code for referral links', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
						<td class="tr2">
							<p>
								<span class="rtwwwap-custom-radio">
									<input id="radio-3" type="radio" name="rtwwwap_extra_features_opt[qr_code]" value="1" <?php isset( $rtwwwap_extra_features[ 'qr_code' ] ) ? checked( $rtwwwap_extra_features[ 'qr_code' ], 1 ) : ''; ?> /><?php esc_html_e( 'On', 'rtwwwap-wp-wc-affiliate-program' ); ?>
									<label for="radio-3"></label>
								</span>
							</p>
							<p>
								<span class="rtwwwap-custom-radio">
									<?php
										$rtwwwap_qr_code_checked = 0;
										if( isset( $rtwwwap_extra_features[ 'qr_code' ] ) && $rtwwwap_extra_features[ 'qr_code' ] == 0 ){
											$rtwwwap_qr_code_checked = 1;
										}
										elseif( !isset( $rtwwwap_extra_features[ 'qr_code' ] ) ){
											$rtwwwap_qr_code_checked = 1;
										}
									?>
									<input id="radio-4" type="radio" name="rtwwwap_extra_features_opt[qr_code]" value="0" <?php checked( $rtwwwap_qr_code_checked, 1 ); ?> /><?php esc_html_e( 'Off', 'rtwwwap-wp-wc-affiliate-program' ); ?>
									<label for="radio-4"></label>
								</span>
							</p>
						</td>
					</tr>
					<tr>
						<th>
							<?php esc_html_e( 'Custom css', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
						<td class="tr2">
							<textarea rows="4" class="rtwwwap_textarea_css" name="rtwwwap_extra_features_opt[css]" ><?php echo isset( $rtwwwap_extra_features[ 'css' ] ) ? $rtwwwap_extra_features[ 'css' ] : ''; ?></textarea>
							<div class="descr"><?php esc_html_e( 'Write custom css for frontend', 'rtwwwap-wp-wc-affiliate-program' );?></div>
						</td>
					</tr>
				</tbody>
			</table>

			<!-- Label Table -->
			<table class="rtwwwap-table form-table <?php echo ($rtwwwap_section_show == 'label') ? 'rtwwwap-show': 'rtwwwap-hide-table' ?>" id="rtwwwap_extra_label">
				<tbody>
					<tr>
						<th>
							<?php esc_html_e( 'Title for button "Become an Affiliate"', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
						<td class="tr2">
							<input type="text" class="rtwwwap_admin_input_text" id="rtwwwap_become_title" name="rtwwwap_extra_features_opt[become_title]" value="<?php echo isset( $rtwwwap_extra_features[ 'become_title' ] ) ? $rtwwwap_extra_features[ 'become_title' ] : ''; ?>" placeholder="<?php esc_html_e( 'Enter Become an Affiliate button Title', 'rtwwwap-wp-wc-affiliate-program' ); ?>" />
						</td>
					</tr>
					<tr>
						<th>
							<?php esc_html_e( 'Title for Section "Benefits for being an Affiliate"', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
						<td class="tr2">
							<input type="text" class="rtwwwap_admin_input_text" id="rtwwwap_benefits_title" name="rtwwwap_extra_features_opt[benefits_title]" value="<?php echo isset( $rtwwwap_extra_features[ 'benefits_title' ] ) ? $rtwwwap_extra_features[ 'benefits_title' ] : ''; ?>" placeholder="<?php esc_html_e( 'Enter Benefits Section Title', 'rtwwwap-wp-wc-affiliate-program' ); ?>" />
						</td>
					</tr>
					<tr>
						<th>
							<?php esc_html_e( 'Title for "Successful Registered user"', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
						<td class="tr2">
							<input type="text" class="rtwwwap_admin_input_text" id="rtwwwap_benefits_title" name="rtwwwap_extra_features_opt[succes_register_msg]" value="<?php echo isset( $rtwwwap_extra_features[ 'succes_register_msg' ] ) ? $rtwwwap_extra_features[ 'succes_register_msg' ] : ''; ?>" placeholder="<?php esc_html_e( 'Enter successful register message', 'rtwwwap-wp-wc-affiliate-program' ); ?>" />
						</td>
					</tr>
					<tr class="rtwwwap_benefits">
						<th>
							<?php esc_html_e( 'Benefits for being an Affiliate', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
						<td class="tr2">
							<?php
								$rtwwwap_default_benefits = sprintf( "<ul><li>%s</li><li>%s</li><li>%s</li></ul>", esc_html__( 'Earn extra money just by marketing our products with our affiliate tools', 'rtwwwap-wp-wc-affiliate-program' ), esc_html__( 'Earn wallet amount to buy products on our site', 'rtwwwap-wp-wc-affiliate-program' ), esc_html__( 'Signup Bonus when someone signup from your shared link', 'rtwwwap-wp-wc-affiliate-program' ) );

								// for frontend wp_editor content
								$rtwwwap_extra_features_wp_editor = isset( $rtwwwap_extra_features[ 'aff_benefits' ] ) ? $rtwwwap_extra_features[ 'aff_benefits' ] : $rtwwwap_default_benefits;

								$rtwwwap_extra_features_wp_editor = html_entity_decode( $rtwwwap_extra_features_wp_editor );
								$rtwwwap_extra_features_wp_editor = stripslashes( $rtwwwap_extra_features_wp_editor );
								$rtwwwap_extra_features_editor_id 	= 'rtwmlbonusfrontendeditor';
								$rtwwwap_extra_features_settings 	=  array(
															'wpautop' 		=> false,
														'media_buttons' => false,
														'textarea_name' => 'rtwwwap_extra_features_opt[aff_benefits]',
														'textarea_rows' => 7
												);
								wp_editor( $rtwwwap_extra_features_wp_editor, $rtwwwap_extra_features_editor_id, $rtwwwap_extra_features_settings );
							?>
							<div class="descr"><?php esc_html_e( 'These benefits will be shown to the users, so that they will become an Affiliate', 'rtwwwap-wp-wc-affiliate-program' );?></div>
						</td>
					</tr>
					<tr>
						<th>
							<?php esc_html_e( 'Label for Tab "Overview"', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
						<td class="tr2">
							<input type="text" class="rtwwwap_admin_input_text" name="rtwwwap_extra_features_opt[affiliate_dash_overview]" value="<?php echo isset( $rtwwwap_extra_features[ 'affiliate_dash_overview' ] ) ? $rtwwwap_extra_features[ 'affiliate_dash_overview' ] : ''; ?>" placeholder="<?php esc_html_e( 'Enter Overview Label', 'rtwwwap-wp-wc-affiliate-program' ); ?>" />
							<div class="descr"><?php esc_html_e( 'Enter title for Tab Overview in Affilate Dashboard Panel', 'rtwwwap-wp-wc-affiliate-program' );?></div>
						</td>
					</tr>
					<tr>
						<th>
							<?php esc_html_e( 'Label for Tab "Commissions"', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
						<td class="tr2">
							<input type="text" class="rtwwwap_admin_input_text" name="rtwwwap_extra_features_opt[affiliate_dash_commission]" value="<?php echo isset( $rtwwwap_extra_features[ 'affiliate_dash_commission' ] ) ? $rtwwwap_extra_features[ 'affiliate_dash_commission' ] : ''; ?>" placeholder="<?php esc_html_e( 'Enter Commission Label', 'rtwwwap-wp-wc-affiliate-program' ); ?>" />
							<div class="descr"><?php esc_html_e( 'Enter title for Tab Commission in Affilate Dashboard Panel', 'rtwwwap-wp-wc-affiliate-program' );?></div>
						</td>
					</tr>
					<tr>
						<th>
							<?php esc_html_e( 'Label for Tab "Affiliate Tools"', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
						<td class="tr2">
							<input type="text" class="rtwwwap_admin_input_text" name="rtwwwap_extra_features_opt[affiliate_dash_tools]" value="<?php echo isset( $rtwwwap_extra_features[ 'affiliate_dash_tools' ] ) ? $rtwwwap_extra_features[ 'affiliate_dash_tools' ] : ''; ?>" placeholder="<?php esc_html_e( 'Enter Affilate Tools Label', 'rtwwwap-wp-wc-affiliate-program' ); ?>" />
							<div class="descr"><?php esc_html_e( 'Enter title for Tab Affiliate Tools in Affilate Dashboard Panel', 'rtwwwap-wp-wc-affiliate-program' );?></div>
						</td>
					</tr>
					<tr>
						<th>
							<?php esc_html_e( 'Label for Tab "Download"', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
						<td class="tr2">
							<input type="text" class="rtwwwap_admin_input_text" name="rtwwwap_extra_features_opt[affiliate_dash_download]" value="<?php echo isset( $rtwwwap_extra_features[ 'affiliate_dash_download' ] ) ? $rtwwwap_extra_features[ 'affiliate_dash_download' ] : ''; ?>" placeholder="<?php esc_html_e( 'Enter Download Label', 'rtwwwap-wp-wc-affiliate-program' ); ?>" />
							<div class="descr"><?php esc_html_e( 'Enter title for Tab Download in Affilate Dashboard Panel', 'rtwwwap-wp-wc-affiliate-program' );?></div>
						</td>
					</tr>
					<tr>
						<th>
							<?php esc_html_e( 'Label for Tab "Payout"', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
						<td class="tr2">
							<input type="text" class="rtwwwap_admin_input_text" name="rtwwwap_extra_features_opt[affiliate_dash_payout]" value="<?php echo isset( $rtwwwap_extra_features[ 'affiliate_dash_payout' ] ) ? $rtwwwap_extra_features[ 'affiliate_dash_payout' ] : ''; ?>" placeholder="<?php esc_html_e( 'Enter Payout Label', 'rtwwwap-wp-wc-affiliate-program' ); ?>" />
							<div class="descr"><?php esc_html_e( 'Enter title for Tab Payout in Affilate Dashboard Panel', 'rtwwwap-wp-wc-affiliate-program' );?></div>
						</td>
					</tr>
					<tr>
						<th>
							<?php esc_html_e( 'Label for Tab "Profile"', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
						<td class="tr2">
							<input type="text" class="rtwwwap_admin_input_text" name="rtwwwap_extra_features_opt[affiliate_dash_profile]" value="<?php echo isset( $rtwwwap_extra_features[ 'affiliate_dash_profile' ] ) ? $rtwwwap_extra_features[ 'affiliate_dash_profile' ] : ''; ?>" placeholder="<?php esc_html_e( 'Enter Profile Label', 'rtwwwap-wp-wc-affiliate-program' ); ?>" />
							<div class="descr"><?php esc_html_e( 'Enter title for Tab Profile in Affilate Dashboard Panel', 'rtwwwap-wp-wc-affiliate-program' );?></div>
						</td>
					</tr>
					<tr>
						<th>
							<?php esc_html_e( 'Label for Tab "Custom Banner"', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
						<td class="tr2">
							<input type="text" class="rtwwwap_admin_input_text" name="rtwwwap_extra_features_opt[affiliate_dash_custom_banner]" value="<?php echo isset( $rtwwwap_extra_features['affiliate_dash_custom_banner'] ) ? $rtwwwap_extra_features['affiliate_dash_custom_banner'] : ''; ?>" placeholder="<?php esc_html_e( 'Enter Custom Banner Label', 'rtwwwap-wp-wc-affiliate-program' ); ?>" />
							<div class="descr"><?php esc_html_e( 'Enter title for Tab Custom Banner in Affilate Dashboard Panel', 'rtwwwap-wp-wc-affiliate-program' );?></div>
						</td>
					</tr>
					<tr>
						<th>
							<?php esc_html_e( 'Label for Tab "MLM"', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
						<td class="tr2">
							<input type="text" class="rtwwwap_admin_input_text" name="rtwwwap_extra_features_opt[affiliate_dash_MLM]" value="<?php echo isset( $rtwwwap_extra_features['affiliate_dash_MLM'] ) ? $rtwwwap_extra_features['affiliate_dash_MLM'] : ''; ?>" placeholder="<?php esc_html_e( 'Enter MLM Label', 'rtwwwap-wp-wc-affiliate-program' ); ?>" />
							<div class="descr"><?php esc_html_e( 'Enter title for Tab MLM in Affilate Dashboard Panel', 'rtwwwap-wp-wc-affiliate-program' );?></div>
						</td>
					</tr>
					
				</tbody>
			</table>

			<!-- Bonus Table -->
			<table class="rtwwwap-table form-table <?php echo ($rtwwwap_section_show == 'bonus') ? 'rtwwwap-show': 'rtwwwap-hide-table' ?>" id="rtwwwap_extra_bonus">
				<tbody>
					<tr>
						<th><?php esc_html_e( 'Signup Bonus Type', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
						<td class="tr2">
							<p>
								<span class="rtwwwap-custom-radio">
									<input id="radio-9" type="radio" name="rtwwwap_extra_features_opt[signup_bonus_type]" value="1" <?php isset( $rtwwwap_extra_features[ 'signup_bonus_type' ] ) ? checked( $rtwwwap_extra_features[ 'signup_bonus_type' ], 1 ) : ''; ?> /><?php esc_html_e( 'Referral Code', 'rtwwwap-wp-wc-affiliate-program' ); ?>
									<label for="radio-9"></label>
								</span>
							</p>

							<p>
								<span class="rtwwwap-custom-radio">
									<?php
										$rtwwwap_signup_bonus_type_checked = 0;
										if( isset( $rtwwwap_extra_features[ 'signup_bonus_type' ] ) && $rtwwwap_extra_features[ 'signup_bonus_type' ] == 0 ){
											$rtwwwap_signup_bonus_type_checked = 1;
										}
										elseif( !isset( $rtwwwap_extra_features[ 'signup_bonus_type' ] ) ){
											$rtwwwap_signup_bonus_type_checked = 1;
										}
									?>
									<input id="radio-10" type="radio" name="rtwwwap_extra_features_opt[signup_bonus_type]" value="0" <?php checked( $rtwwwap_signup_bonus_type_checked, 1 ); ?> /><?php esc_html_e( 'Cookie ( Default )', 'rtwwwap-wp-wc-affiliate-program' ); ?>
									<label for="radio-10"></label>
								</span>
							</p>
							<div class="descr"><?php esc_html_e( '[ Note : When "REFFERAL CODE" is selected as Signup Bonus Type then Referral Link will not work for Singup Bonus, Social share bonus.', 'rtwwwap-wp-wc-affiliate-program' );?></div>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Active Membership Plan', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
							<td class="tr2">
								<?php
									$rtwwwap_show_in_woo_checked = isset( $rtwwwap_extra_features[ 'rtwwwap_active_membership' ] ) ? $rtwwwap_extra_features[ 'rtwwwap_active_membership' ] : 0;
								?>
								<p>
									<span class="rtwwwap-custom-radio">
										<input id="radio-25" type="radio" class="rtwwwap_membership" name="rtwwwap_extra_features_opt[rtwwwap_active_membership]" value="1"<?php checked( $rtwwwap_show_in_woo_checked, 1 ); ?> /><?php esc_html_e( 'Yes', 'rtwwwap-wp-wc-affiliate-program' ); ?>
														<label for="radio-25"></label>
									</span>
								</p>
								<p>
									<span class="rtwwwap-custom-radio">
										<input id="radio-26" type="radio" class="rtwwwap_membership" name="rtwwwap_extra_features_opt[rtwwwap_active_membership]" value="0" <?php checked( $rtwwwap_show_in_woo_checked, 0 ); ?> /><?php esc_html_e( 'No', 'rtwwwap-wp-wc-affiliate-program' ); ?>
										<label for="radio-26"></label>
									</span>
								</p>
										<div class="descr"><?php esc_html_e( 'By activating this option user have to pay some amount to be an Affiliate', 'rtwwwap-wp-wc-affiliate-program' );?></div>
							</td>
					</tr>

					<tr id="rtwwwap_membership_amount" class="<?php echo isset( $rtwwwap_extra_features[ 'rtwwwap_active_membership' ] ) && $rtwwwap_extra_features[ 'rtwwwap_active_membership' ] == 1 ? esc_attr( '' ) : esc_attr( 'rtwwwap_hidden' ); ?>" >
							<th>
								<?php esc_html_e( 'Enter Membership Amount ', 'rtwwwap-wp-wc-affiliate-program' ); ?>
							</th>
							<td class="tr2">
								<p>
									<input type="number" name="rtwwwap_extra_features_opt[membership_amount]" min="1" value="<?php echo isset( $rtwwwap_extra_features[ 'membership_amount' ] ) ? esc_attr( $rtwwwap_extra_features[ 'membership_amount' ] ) : esc_attr( 1 ); ?>" />
								</p>
								<p><?php esc_html_e( 'Enter Amount that Affiliate have to pay while becomeing an affiliate', 'rtwwwap-wp-wc-affiliate-program' ); ?></p>
							</td>
					</tr>
					<tr>
						<th>
							<?php esc_html_e( 'Signup Bonus', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
						<td class="tr2">
							<input type="number" min="0" name="rtwwwap_extra_features_opt[signup_bonus]" value="<?php echo isset( $rtwwwap_extra_features[ 'signup_bonus' ] ) ? esc_attr( $rtwwwap_extra_features[ 'signup_bonus' ] ) : esc_attr( '0' ); ?>" />
							<div class="descr"><?php esc_html_e( 'Enter Amount to be given for referral signup (By default 0)', 'rtwwwap-wp-wc-affiliate-program' );?></div>
						</td>
					</tr>

					<tr>
						<th>
							<?php esc_html_e( 'Pay Per Click', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
						<td class="tr2">
							<input type="number" min="0" name="rtwwwap_extra_features_opt[pay_per_click]" value="<?php echo isset( $rtwwwap_extra_features[ 'pay_per_click' ] ) ? esc_attr( $rtwwwap_extra_features[ 'pay_per_click' ] ) : esc_attr( '0' ); ?>" />
							<div class="descr"><?php esc_html_e( 'Enter Amount to be given for clicking on link only for first time (By default 0)', 'rtwwwap-wp-wc-affiliate-program' );?></div>
						</td>
					</tr>

					<?php
						if( RTWWWAP_IS_WOO == 1){
					?>
						<tr>
							<th>
								<?php esc_html_e( 'Performance Bonus', 'rtwwwap-wp-wc-affiliate-program' ); ?>
							</th>
							<td class="tr2">
								<table>
									<thead>
										<th>
											<?php esc_html_e( 'Total Sale amount to unlock achievement', 'rtwwwap-wp-wc-affiliate-program' ); ?>
										</th>
										<th>
											<?php esc_html_e( 'Incentive', 'rtwwwap-wp-wc-affiliate-program' ); ?>
										</th>
										<th>
											<?php esc_html_e( 'Add/Remove row', 'rtwwwap-wp-wc-affiliate-program' ); ?>
										</th>
									</thead>
									<tbody class="rtwwwap_tbody rtwwwap_perf_table">
										<!-- hidden row start-->
										<tr class="rtwwwap_add_new_row_hidden" style="display: none;">
											<td>
												<input class="rtwwwap_sale_amount" type="number" min="1" name="rtwwwap_extra_features_opt[performance_bonus][0][sale_amount]" value="1" />
											</td>
											<td>
												<input class="rtwwwap_incentive" type="number" min="0" name="rtwwwap_extra_features_opt[performance_bonus][0][incentive]" value="0" />
											</td>
											<td>
												<span class="dashicons dashicons-plus-alt rtwwwap_add_new_row_perf"></span>
												<span class="dashicons dashicons-dismiss rtwwwap_remove_row_perf"></span>
											</td>
										</tr>
										<!-- hidden row end-->
										<?php
										if( !empty( $rtwwwap_extra_features ) && isset( $rtwwwap_extra_features[ 'performance_bonus' ] ) && !empty( $rtwwwap_extra_features[ 'performance_bonus' ] ) && is_array( $rtwwwap_extra_features[ 'performance_bonus' ] ) ){
											$rtwwwap_count = 0;
											foreach( $rtwwwap_extra_features[ 'performance_bonus' ] as $rtwwwap_key => $rtwwwap_value ){
												$rtwwwap_count++;
										?>
										<tr>
											<td>
												<input class="rtwwwap_sale_amount" type="number" min="1" name="rtwwwap_extra_features_opt[performance_bonus][<?php echo esc_attr( $rtwwwap_count ); ?>][sale_amount]" value="<?php echo !empty($rtwwwap_key) ? esc_attr( $rtwwwap_key ) : 1; ?>" />
											</td>
											<td>
												<input class="rtwwwap_incentive" type="number" min="0" name="rtwwwap_extra_features_opt[performance_bonus][<?php echo esc_attr( $rtwwwap_count ); ?>][incentive]" value="<?php echo esc_attr( $rtwwwap_value ); ?>" />
											</td>
											<td>
												<span class="dashicons dashicons-plus-alt rtwwwap_add_new_row_perf"></span>
												<span class="dashicons dashicons-dismiss rtwwwap_remove_row_perf"></span>
											</td>
										</tr>
										<?php }
										}
										else{ ?>
										<tr>
											<td>
												<input class="rtwwwap_sale_amount" type="number" min="1" name="rtwwwap_extra_features_opt[performance_bonus][1][sale_amount]" value="1" />
											</td>
											<td>
												<input class="rtwwwap_incentive" type="number" min="0" name="rtwwwap_extra_features_opt[performance_bonus][1][incentive]" value="0" />
											</td>
											<td>
												<span class="dashicons dashicons-plus-alt rtwwwap_add_new_row_perf"></span>
												<span class="dashicons dashicons-dismiss rtwwwap_remove_row_perf"></span>
											</td>
										</tr>
									<?php } ?>
									</tbody>
								</table>
							</td>
						</tr>
					<?php
						}
					?>
					<?php
						if( RTWWWAP_IS_Easy == 1){
					?>
						<tr>
							<th>
								<?php esc_html_e( 'Performance Bonus', 'rtwwwap-wp-wc-affiliate-program' ); ?>
							</th>
							<td class="tr2">
								<table>
									<thead>
										<th>
											<?php esc_html_e( 'Total Sale amount to unlock achievement', 'rtwwwap-wp-wc-affiliate-program' ); ?>
										</th>
										<th>
											<?php esc_html_e( 'Incentive', 'rtwwwap-wp-wc-affiliate-program' ); ?>
										</th>
										<th>
											<?php esc_html_e( 'Add/Remove row', 'rtwwwap-wp-wc-affiliate-program' ); ?>
										</th>
									</thead>
									<tbody class="rtwwwap_tbody rtwwwap_perf_table">
										<!-- hidden row start-->
										<tr class="rtwwwap_add_new_row_hidden" style="display: none;">
											<td>
												<input class="rtwwwap_sale_amount" type="number" min="1" name="rtwwwap_extra_features_opt[performance_bonus][0][sale_amount]" value="1" />
											</td>
											<td>
												<input class="rtwwwap_incentive" type="number" min="0" name="rtwwwap_extra_features_opt[performance_bonus][0][incentive]" value="0" />
											</td>
											<td>
												<span class="dashicons dashicons-plus-alt rtwwwap_add_new_row_perf"></span>
												<span class="dashicons dashicons-dismiss rtwwwap_remove_row_perf"></span>
											</td>
										</tr>
										<!-- hidden row end-->
										<?php
										if( !empty( $rtwwwap_extra_features ) && isset( $rtwwwap_extra_features[ 'performance_bonus' ] ) && !empty( $rtwwwap_extra_features[ 'performance_bonus' ] ) && is_array( $rtwwwap_extra_features[ 'performance_bonus' ] ) ){
											$rtwwwap_count = 0;
											foreach( $rtwwwap_extra_features[ 'performance_bonus' ] as $rtwwwap_key => $rtwwwap_value ){
												$rtwwwap_count++;
										?>
										<tr>
											<td>
												<input class="rtwwwap_sale_amount" type="number" min="1" name="rtwwwap_extra_features_opt[performance_bonus][<?php echo esc_attr( $rtwwwap_count ); ?>][sale_amount]" value="<?php echo !empty($rtwwwap_key) ? esc_attr( $rtwwwap_key ) : 1; ?>" />
											</td>
											<td>
												<input class="rtwwwap_incentive" type="number" min="0" name="rtwwwap_extra_features_opt[performance_bonus][<?php echo esc_attr( $rtwwwap_count ); ?>][incentive]" value="<?php echo esc_attr( $rtwwwap_value ); ?>" />
											</td>
											<td>
												<span class="dashicons dashicons-plus-alt rtwwwap_add_new_row_perf"></span>
												<span class="dashicons dashicons-dismiss rtwwwap_remove_row_perf"></span>
											</td>
										</tr>
										<?php }
										}
										else{ ?>
										<tr>
											<td>
												<input class="rtwwwap_sale_amount" type="number" min="1" name="rtwwwap_extra_features_opt[performance_bonus][1][sale_amount]" value="1" />
											</td>
											<td>
												<input class="rtwwwap_incentive" type="number" min="0" name="rtwwwap_extra_features_opt[performance_bonus][1][incentive]" value="0" />
											</td>
											<td>
												<span class="dashicons dashicons-plus-alt rtwwwap_add_new_row_perf"></span>
												<span class="dashicons dashicons-dismiss rtwwwap_remove_row_perf"></span>
											</td>
										</tr>
									<?php } ?>
									</tbody>
								</table>
							</td>
						</tr>
					<?php
						}
					?>

					<tr>
						<th><?php esc_html_e( 'Social Media Share Buttons', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
						<td class="tr2">
							<p>
								<span class="rtwwwap-checkbox">
									<input id="rtwwwap_checkbox_social_share" type="checkbox" name="rtwwwap_extra_features_opt[social_share]" <?php if( isset( $rtwwwap_extra_features[ 'social_share' ] ) ){ checked( $rtwwwap_extra_features[ 'social_share' ], 'on' ); } ?> />
									<label for="rtwwwap_checkbox_social_share"></label>
								</span>
							</p>
						</td>
					</tr>
					<?php
						if( RTWWWAP_IS_WOO == 1  ){
							do_action( 'rtwwwap_social_share_settings' );
						}
						if( RTWWWAP_IS_Easy == 1  ){
							do_action( 'rtwwwap_social_share_settings' );
						}
					?>
					<tr class="rtwwwap_social_share_bonus">
						<th>
							<?php esc_html_e( 'Sharing Bonus', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
						<td class="tr2">
							<input type="number" min="0" step="0.01" name="rtwwwap_extra_features_opt[sharing_bonus]" value="<?php echo isset( $rtwwwap_extra_features[ 'sharing_bonus' ] ) ? esc_attr( $rtwwwap_extra_features[ 'sharing_bonus' ] ) : esc_attr( '0' ); ?>" />
							<div class="descr"><?php esc_html_e( 'Enter Amount to be given for Sharing a product on social media (By default 0)', 'rtwwwap-wp-wc-affiliate-program' );?></div>
						</td>
					</tr>
					<tr class="rtwwwap_social_share_bonus_limit">
						<th>
							<?php esc_html_e( 'Sharing Bonus Limit', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
						<td class="tr2">
							<table>
								<thead>
									<th>
										<?php esc_html_e( 'Time Limit', 'rtwwwap-wp-wc-affiliate-program' ); ?>
									</th>
									<th>
										<?php esc_html_e( 'Amount Limit', 'rtwwwap-wp-wc-affiliate-program' ); ?>
									</th>
								</thead>
								<tbody class="rtwwwap_tbody">
									<tr>
										<td>
											<?php
												$rtwwwap_bonus_time_limit_selected = isset( $rtwwwap_extra_features[ 'sharing_bonus_time_limit' ] ) ? $rtwwwap_extra_features[ 'sharing_bonus_time_limit' ] : 0;
											?>
											<select class="rtwwwap_select2_sharing_bonus_time_limit" id="" name="rtwwwap_extra_features_opt[sharing_bonus_time_limit]" >
												<option value="0" <?php selected( $rtwwwap_bonus_time_limit_selected, '0' ) ?> >
													<?php esc_html_e( 'No Limit', 'rtwwwap-wp-wc-affiliate-program' ); ?>
												</option>
												<option value="1" <?php selected( $rtwwwap_bonus_time_limit_selected, '1' ) ?> >
													<?php esc_html_e( 'Daily Limit', 'rtwwwap-wp-wc-affiliate-program' ); ?>
												</option>
												<option value="2" <?php selected( $rtwwwap_bonus_time_limit_selected, '2' ) ?> >
													<?php esc_html_e( 'Weekly Limit', 'rtwwwap-wp-wc-affiliate-program' ); ?>
												</option>
												<option value="3" <?php selected( $rtwwwap_bonus_time_limit_selected, '3' ) ?> >
													<?php esc_html_e( 'Monthly Limit', 'rtwwwap-wp-wc-affiliate-program' ); ?>
												</option>
											</select>
										</td>
										<td>
											<input type="number" <?php disabled( $rtwwwap_bonus_time_limit_selected, '0' ) ?> class="sharing_bonus_amount_limit" min="0" step="0.01" name="rtwwwap_extra_features_opt[sharing_bonus_amount_limit]" value="<?php echo isset( $rtwwwap_extra_features[ 'sharing_bonus_amount_limit' ] ) ? esc_attr( $rtwwwap_extra_features[ 'sharing_bonus_amount_limit' ] ) : esc_attr( '0' ); ?>" />
										</td>
									</tr>
								</tbody>
							</table>
							<div class="descr"><?php esc_html_e( 'Enter Time and Amount after which Affiliate will not get the share bonus', 'rtwwwap-wp-wc-affiliate-program' );?></div>
						</td>
					</tr>
				</tbody>
			</table>
			<!-- paument Table -->
			<table class="rtwwwap-table form-table <?php echo ($rtwwwap_section_show == 'payment') ? 'rtwwwap-show': 'rtwwwap-hide-table' ?>" id="rtwwwap_extra_payment">
				<tbody>
					<tr>
						<th><?php esc_html_e( 'Activate Paypal', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
						<td class="tr2">
							<p>
								<span class="rtwwwap-checkbox">
									<input id="rtwwwap_checkbox_paypal" type="checkbox" name="rtwwwap_extra_features_opt[activate_paypal]" <?php if( isset( $rtwwwap_extra_features[ 'activate_paypal' ] ) ){ checked( $rtwwwap_extra_features[ 'activate_paypal' ], 'on' ); } ?> />
									<label for="rtwwwap_checkbox_paypal"></label>
								</span>
							</p>
							<div class="rtwwwap-payment-wrapper">
								<span class="rtwwwap-custom-radio">
									<input class="rtwwwap_paypal_live_radio" id="radio-5" type="radio" name="rtwwwap_extra_features_opt[paypal_type]" value="live" <?php isset( $rtwwwap_extra_features[ 'paypal_type' ] ) ? checked( $rtwwwap_extra_features[ 'paypal_type' ], 'live' ) : ''; ?> /><?php esc_html_e( 'Paypal Live', 'rtwwwap-wp-wc-affiliate-program' ); ?>
									<label for="radio-5"></label>
								</span>
								<div class="">
									<input id="rtwwwap_paypal_live_id" type="text" name="rtwwwap_extra_features_opt[paypal_live_client_id]" value="<?php echo isset( $rtwwwap_extra_features[ 'paypal_live_client_id' ] ) ? esc_attr( $rtwwwap_extra_features[ 'paypal_live_client_id' ] ) : esc_attr( '' ); ?>" placeholder="<?php esc_attr_e( 'Client ID', 'rtwwwap-wp-wc-affiliate-program' ) ?>" />
								</div>
								<div class="">
									<input id="rtwwwap_paypal_live_secret" type="text" name="rtwwwap_extra_features_opt[paypal_live_client_secret]" value="<?php echo isset( $rtwwwap_extra_features[ 'paypal_live_client_secret' ] ) ? esc_attr( $rtwwwap_extra_features[ 'paypal_live_client_secret' ] ) : esc_attr( '' ); ?>" placeholder="<?php esc_attr_e( 'Client Secret', 'rtwwwap-wp-wc-affiliate-program' ) ?>" />
								</div>
							</div>
							<div class="rtwwwap-payment-wrapper">
								<span class="rtwwwap-custom-radio">
									<input class="rtwwwap_paypal_sandbox_radio" id="radio-6" type="radio" name="rtwwwap_extra_features_opt[paypal_type]" value="sandbox" <?php isset( $rtwwwap_extra_features[ 'paypal_type' ] ) ? checked( $rtwwwap_extra_features[ 'paypal_type' ], 'sandbox' ) : ''; ?> /><?php esc_html_e( 'Paypal Sandbox', 'rtwwwap-wp-wc-affiliate-program' ); ?>
									<label for="radio-6"></label>
								</span>
								<span class="">
									<input id="rtwwwap_paypal_sandbox_id" type="text" name="rtwwwap_extra_features_opt[paypal_sandbox_client_id]" value="<?php echo isset( $rtwwwap_extra_features[ 'paypal_sandbox_client_id' ] ) ? esc_attr( $rtwwwap_extra_features[ 'paypal_sandbox_client_id' ] ) : esc_attr( '' ); ?>" placeholder="<?php esc_attr_e( 'Client ID', 'rtwwwap-wp-wc-affiliate-program' ) ?>" />
								</span>
								<span class="">
									<input id="rtwwwap_paypal_sandbox_secret" type="text" name="rtwwwap_extra_features_opt[paypal_sandbox_client_secret]" value="<?php echo isset( $rtwwwap_extra_features[ 'paypal_sandbox_client_secret' ] ) ? esc_attr( $rtwwwap_extra_features[ 'paypal_sandbox_client_secret' ] ) : esc_attr( '' ); ?>" placeholder="<?php esc_attr_e( 'Client Secret', 'rtwwwap-wp-wc-affiliate-program' ) ?>" />
								</span>
								
							</div>

						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Activate Stripe', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
						<td class="tr2">
							<p>
								<span class="rtwwwap-checkbox">
									<input id="rtwwwap_checkbox_stripe" type="checkbox" name="rtwwwap_extra_features_opt[activate_stripe]" <?php if( isset( $rtwwwap_extra_features[ 'activate_stripe' ] ) ){ checked( $rtwwwap_extra_features[ 'activate_stripe' ], 'on' ); } ?> />
									<label for="rtwwwap_checkbox_stripe"></label>
								</span>
							</p>
							<div class="rtwwwap-payment-wrapper">
								<span class="rtwwwap-custom-radio">
									<input class="rtwwwap_stripe_live_radio" id="radio-7" type="radio" name="rtwwwap_extra_features_opt[stripe_type]" value="live" <?php isset( $rtwwwap_extra_features[ 'stripe_type' ] ) ? checked( $rtwwwap_extra_features[ 'stripe_type' ], 'live' ) : ''; ?> /><?php esc_html_e( 'Stripe Live', 'rtwwwap-wp-wc-affiliate-program' ); ?>
									<label for="radio-7"></label>
								</span>
								<div class="">
									<input id="rtwwwap_stripe_live_id" type="text" name="rtwwwap_extra_features_opt[stripe_live_publishable_key]" value="<?php echo isset( $rtwwwap_extra_features[ 'stripe_live_publishable_key' ] ) ? esc_attr( $rtwwwap_extra_features[ 'stripe_live_publishable_key' ] ) : esc_attr( '' ); ?>" placeholder="<?php esc_attr_e( 'Publishable Key', 'rtwwwap-wp-wc-affiliate-program' ) ?>" />
								</div>
								<div class="">
									<input id="rtwwwap_stripe_live_secret" type="text" name="rtwwwap_extra_features_opt[stripe_live_secret_key]" value="<?php echo isset( $rtwwwap_extra_features[ 'stripe_live_secret_key' ] ) ? esc_attr( $rtwwwap_extra_features[ 'stripe_live_secret_key' ] ) : esc_attr( '' ); ?>" placeholder="<?php esc_attr_e( 'Secret Key', 'rtwwwap-wp-wc-affiliate-program' ) ?>" />
								</div>
							</div>
							<div class="rtwwwap-payment-wrapper">
								<span class="rtwwwap-custom-radio">
									<input class="rtwwwap_stripe_sandbox_radio" id="radio-8" type="radio" name="rtwwwap_extra_features_opt[stripe_type]" value="sandbox" <?php isset( $rtwwwap_extra_features[ 'stripe_type' ] ) ? checked( $rtwwwap_extra_features[ 'stripe_type' ], 'sandbox' ) : ''; ?> /><?php esc_html_e( 'Stripe Sandbox', 'rtwwwap-wp-wc-affiliate-program' ); ?>
									<label for="radio-8"></label>
								</span>
								<span class="">
									<input id="rtwwwap_stripe_sandbox_id" type="text" name="rtwwwap_extra_features_opt[stripe_sandbox_publishable_key]" value="<?php echo isset( $rtwwwap_extra_features[ 'stripe_sandbox_publishable_key' ] ) ? esc_attr( $rtwwwap_extra_features[ 'stripe_sandbox_publishable_key' ] ) : esc_attr( '' ); ?>" placeholder="<?php esc_attr_e( 'Publishable Key', 'rtwwwap-wp-wc-affiliate-program' ) ?>" />
								</span>
								<span class="">
									<input id="rtwwwap_stripe_sandbox_secret" type="text" name="rtwwwap_extra_features_opt[stripe_sandbox_secret_key]" value="<?php echo isset( $rtwwwap_extra_features[ 'stripe_sandbox_secret_key' ] ) ? esc_attr( $rtwwwap_extra_features[ 'stripe_sandbox_secret_key' ] ) : esc_attr( '' ); ?>" placeholder="<?php esc_attr_e( 'Secret Key', 'rtwwwap-wp-wc-affiliate-program' ) ?>" />
								</span>
							</div>
						</td>
					</tr>
					<th><?php esc_html_e( 'Activate Paystack', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
						<td class="tr2">
							<p>
								<span class="rtwwwap-checkbox">
									<input id="rtwwwap_checkbox_paystack" type="checkbox" name="rtwwwap_extra_features_opt[activate_paystack]" <?php if( isset( $rtwwwap_extra_features[ 'activate_paystack' ] ) ){ checked( $rtwwwap_extra_features[ 'activate_paystack' ], 'on' ); } ?> />
									<label for="rtwwwap_checkbox_paystack"></label>
								</span>
							</p>

							<div class="rtwwwap-payment-wrapper">
								
								<span class="rtwwwap-custom-radio">
									<input class="rtwwwap_paystack_live_radio" id="paystack_live" type="radio" name="rtwwwap_extra_features_opt[paystack_type]" value="live" <?php isset( $rtwwwap_extra_features[ 'paystack_type' ] ) ? checked( $rtwwwap_extra_features[ 'paystack_type' ], 'live' ) : ''; ?> /><?php esc_html_e( 'Paystack Live', 'rtwwwap-wp-wc-affiliate-program' ); ?>
									<label for="paystack_live"></label>
								</span>

								<div class="">
									<input id="rtwwwap_paystack_secret_key" type="text" name="rtwwwap_extra_features_opt[rtwwwap_paystack_secret_key]" value="<?php echo isset( $rtwwwap_extra_features[ 'rtwwwap_paystack_secret_key' ] ) ? esc_attr( $rtwwwap_extra_features[ 'rtwwwap_paystack_secret_key' ] ) : esc_attr( '' ); ?>" placeholder="<?php esc_attr_e( 'Secret Key', 'rtwwwap-wp-wc-affiliate-program' ) ?>" />
								</div>
								<div class="">
									<input id="rtwwwap_paystack_public_key" type="text" name="rtwwwap_extra_features_opt[rtwwwap_paystack_public_key]" value="<?php echo isset( $rtwwwap_extra_features[ 'rtwwwap_paystack_public_key' ] ) ? esc_attr( $rtwwwap_extra_features[ 'rtwwwap_paystack_public_key' ] ) : esc_attr( '' ); ?>" placeholder="<?php esc_attr_e( 'public Key', 'rtwwwap-wp-wc-affiliate-program' ) ?>" />
								</div>
							</div>

							<div class="rtwwwap-payment-wrapper">
								
								<span class="rtwwwap-custom-radio">
									<input class="rtwwwap_paystack_sandbox_radio" id="paystack_sandbox" type="radio" name="rtwwwap_extra_features_opt[paystack_type]" value="sandbox" <?php isset( $rtwwwap_extra_features[ 'paystack_type' ] ) ? checked( $rtwwwap_extra_features[ 'paystack_type' ], 'sandbox' ) : ''; ?> /><?php esc_html_e( 'Paystack Sandbox', 'rtwwwap-wp-wc-affiliate-program' ); ?>
									<label for="paystack_sandbox"></label>
								</span>

								<div class="">
									<input id="rtwwwap_paystack_sandbox_secret_key" type="text" name="rtwwwap_extra_features_opt[rtwwwap_paystack_sandbox_secret_key]" value="<?php echo isset( $rtwwwap_extra_features[ 'rtwwwap_paystack_sandbox_secret_key' ] ) ? esc_attr( $rtwwwap_extra_features[ 'rtwwwap_paystack_sandbox_secret_key' ] ) : esc_attr( '' ); ?>" placeholder="<?php esc_attr_e( 'Secret Key', 'rtwwwap-wp-wc-affiliate-program' ) ?>" />
								</div>
								<div class="">
									<input id="rtwwwap_paystack_sandbox_public_key" type="text" name="rtwwwap_extra_features_opt[rtwwwap_paystack_sandbox_public_key]" value="<?php echo isset( $rtwwwap_extra_features[ 'rtwwwap_paystack_sandbox_public_key' ] ) ? esc_attr( $rtwwwap_extra_features[ 'rtwwwap_paystack_sandbox_public_key' ] ) : esc_attr( '' ); ?>" placeholder="<?php esc_attr_e( 'public Key', 'rtwwwap-wp-wc-affiliate-program' ) ?>" />
								</div>
							</div>
						</td>
					</tr>

					<tr>
						<th><?php esc_html_e( 'Receive Payment From Affiliates', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
						<td class="tr2">
							<p>
							<div>
							<span><?php esc_html_e( 'Enter client Email Id', 'rtwwwap-wp-wc-affiliate-program' ); ?></span>

							<span class="">
									<input id="rtwwwap_paypal_email" type="text" name="rtwwwap_extra_features_opt[paypal_sandbox_client_eamil]" value="<?php echo isset( $rtwwwap_extra_features[ 'paypal_sandbox_client_eamil' ] ) ? esc_attr( $rtwwwap_extra_features[ 'paypal_sandbox_client_eamil' ] ) : esc_attr( '' ); ?>" placeholder="<?php esc_attr_e( 'Client Email', 'rtwwwap-wp-wc-affiliate-program' ) ?>" />
								</span>
							</div>

							</p>
						</td>
					</tr>	
				</tbody>
			</table>
			<table class="rtwwwap-table form-table <?php echo ($rtwwwap_section_show == 'notification') ? 'rtwwwap-show': 'rtwwwap-hide-table' ?>" id="rtwwwap_extra_notification">
				<tbody>
					<tr>
						<td><input type="button" class="rtwwwap-button rtwwwap_add_notification" value="<?php esc_html_e( 'Add Notification', 'rtwwwap-wp-wc-affiliate-program' ); ?>">	</td>
					</tr>
					<tr>
						<td class="tr2">
							<table class="rtwwwap_notification_table">
								<thead>
									<th>
										<?php esc_html_e( 'Title', 'rtwwwap-wp-wc-affiliate-program' ); ?>
									</th>
									<th>
										<?php esc_html_e( 'View / Edit', 'rtwwwap-wp-wc-affiliate-program' ); ?>
									</th>
									<th>
										<?php esc_html_e( 'Remove ', 'rtwwwap-wp-wc-affiliate-program' ); ?>
									</th>
								</thead>
								<tbody class="rtwwwap_noti_main">
								<?php
								$rtwwwap_noti_option = get_option("rtwwwap_noti_arr");
								if($rtwwwap_noti_option)
								{
									foreach ($rtwwwap_noti_option as $key => $value) 
									{
									?>
										<tr>
											<td><?php echo  esc_attr($value['title'])?></td>
											<td>
												<span><i class="fa fa-eye rtwwwap_view_edit_icon" data-key="<?php echo $key?>" aria-hidden="true" data-noti_title="<?php echo $value['title'] ?>" data-noti_content="<?php echo $value['content'] ?>"></i></span></td>
											<td><i class="far fa-trash-alt rtwwwap_delete rtwwwap_view_delete_icon" data-key="<?php echo $key?>"></i></td>
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

		<div class="rtwwwap_rank_requirement_model">
			<div class="rtwwwap_rank_model_dialog">
				<div class="rtwwwap_rank_model_content">
					
					<div class="rtwwwap_rank_model_header">
						<h3>Choose the rank whatever you want to give an affiliate</h3>
						<div class="rtwwwap_close_model_icon">
							<i class="fas fa-times"></i>
						</div>
					</div>					

					<div class="rtwwwap_rank_model_body">
						<div class="rtwwwap_requirement_wrapper">
							<div class="rtwwwap_rank_content1">
								<div class="rtwwwap_rank_name">
									<label class="rtwwwap_rank_label">Name of Rank</label>
									<input type="text" name="rtwwwap_rank_name_field" value="" class ="rtwwwap_rank_name_field rtwwwap_rank_field">
								</div>
								
							</div>
							<div class="rtwwwap_rank_content2">
								<div class="rtwwwap_rank_priority">
									<label class="rtwwwap_rank_label">Rank priority</label>
									<input type="number" name="rtwwwap_priority_field" value="" class ="rtwwwap_priority_field rtwwwap_rank_field">
								</div>
								<div class="rtwwwap_rank_commission">
									<label class="rtwwwap_rank_label">Rank commission</label>
									<input type="number" name="rtwwwap_commission_field" value="" class ="rtwwwap_commission_field rtwwwap_rank_field">
								</div>
							</div>
							<div class="rtwwwap_rank_description">
								<label class="rtwwwap_rank_label">Description of Rank</label>
								<textarea name="rtwwwap_rank_desc_field" id="" cols="24" rows="3" class ="rtwwwap_rank_desc_field"></textarea>
							</div>
							<div class="rtwwwap_rank_reqmnt">
								<label class="rtwwwap_rank_label">Rank requirement</label>
								<ul>
								<div class="rtwwwap_general_class">
										<select class= "rtwwwap_requirement_option11" name ="rtwwwap_requirement_option">
											
											<option value="1">Signup as an affiliate</option>
											<option value="2">Personally sponser affiliate</option>
											<option value="3">Total affiliate in an organisation</option>
											<option value="4">Reach a Rank</option>
										</select>
										
										<input type='text' name='rtwwwap_personally_sponser' value='' class ='rtwwwap_personally_sponser'><input type='text' name='rtwwwap_total_sponser_in_orgn' value='' class ='rtwwwap_total_sponser_in_orgn'> 

										<select class='rtwwwap_reach_a_rank'>
											<option>Select</option>
											<?php 
												$result = get_option('rtwwwap_rank_details'); 
											
												if(!empty($result)){
													foreach($result as $key=>$val){
														$rtwwwap_rank_name = isset($val['rank_name'])? $val['rank_name']: "";
													?>
														<option value="<?php echo $rtwwwap_rank_name ?>"><?php echo $rtwwwap_rank_name ?></option>
													<?php
													}
												}
										
											?>
										</select>

										<input type='number' name='rtwwwap_reach' value='' class ='rtwwwap_reach' placeholder="Enter number of Aff">
										<input type="button" value="Remove" class="rtwwwap-button" id="rtwwwap_remove_requirements">
									</div>	
								</ul>
								<div class="rtwwwap_new_rank_reqmnt">
									<input type="button" value="Add new requirement" class="rtwwwap-button" id="rtwwwap_add_new_requirements">	
								</div>
							</div>
						</div>	
						</div>	
						<div class="rtwwwap_rank_footer">
								<input type="button" value="<?php esc_html_e( 'Save', 'rtwwwap-wp-wc-affiliate-program' ); ?>" class="rtwwwap-button" id="rtwwwap_save_rank_requirements">
							</div>	
				</div>	
			</div>
		</div>

			<table class="rtwwwap-table form-table <?php echo ($rtwwwap_section_show == 'rank') ? 'rtwwwap-show': 'rtwwwap-hide-table' ?>" id="rtwwwap_extra_rank">
				<tbody>
					<tr>
						<td class="rtwwwap_new_rank_text">
							<input type="button" value="<?php esc_html_e( 'Add New Rank', 'rtwwwap-wp-wc-affiliate-program' ); ?>" class="rtwwwap-button" id="rtwwwap_rank_requirements">
							
						</td>
					</tr>
					<tr>
					<td class="tr2">
						<table class="rtwwwap_notification_table">
							<thead class="rtwwwap_set_diff_width_th">
								<th>
									<?php  esc_html_e( 'SN', 'rtwwwap-wp-wc-affiliate-program' ); ?>
								</th>
								<th>
									<?php  esc_html_e( 'Rank Name', 'rtwwwap-wp-wc-affiliate-program' ); ?>
								</th>
								<th>
								<?php  esc_html_e( 'Rank Priority', 'rtwwwap-wp-wc-affiliate-program' ); ?>
								</th>
								
								<th>
									<?php  esc_html_e( 'Rank Commission', 'rtwwwap-wp-wc-affiliate-program' ); ?>
								</th>
								<th>
								<?php  esc_html_e( 'Actions ', 'rtwwwap-wp-wc-affiliate-program' ); ?>
								</th>
								<th>
									<?php  esc_html_e( 'Date ', 'rtwwwap-wp-wc-affiliate-program' ); ?>
								</th>
							</thead>

							<tbody class="rtwwwap_noti_main">
								<?php 

									$result = get_option('rtwwwap_rank_details');
			
									$count =1;
									if($result && is_array($result)){
										foreach($result as $key => $value){
											
											$rtwwwap_rank_name = isset($value['rank_name'])? $value['rank_name']: "";
											$rtwwwap_rank_priority = isset($value['rank_priority'])? $value['rank_priority']: "";
											$rtwwwap_rank_commission = isset($value['rank_commission'])? $value['rank_commission']: "";
											$date = isset($value['date'])? $value['date']: "";

										?>
											<tr>
												<td>
													<?php  esc_html_e( $count , 'rtwwwap-wp-wc-affiliate-program' ); ?>
												</td>
												<td>
													<?php  esc_html_e( $rtwwwap_rank_name, 'rtwwwap-wp-wc-affiliate-program' ); ?>
												</td>
												<td>
													<?php  esc_html_e( $rtwwwap_rank_priority, 'rtwwwap-wp-wc-affiliate-program' ); ?>
												</td>
												<td>
													<?php  esc_html_e( $rtwwwap_rank_commission, 'rtwwwap-wp-wc-affiliate-program' ); ?>
												</td>
												<td id ="<?php echo "edit_rank_$count" ?>" class="rtwwwap_action">
													<button type = "button" data-id ="<?php echo $key; ?>"  id ="rtwwwap_edit_reqmnt" class = "rtwwwap_edit_reqmnt"><?php  esc_html_e( "edit" , 'rtwwwap-wp-wc-affiliate-program' ); ?></button>
													<button type = "button" data-id ="<?php echo $key; ?>"  id ="rtwwwap_delete_reqmnt" class = "rtwwwap_delete_reqmnt"><?php  esc_html_e( "delete" , 'rtwwwap-wp-wc-affiliate-program' ); ?></button>
												</td>
												<td>
													<?php  esc_html_e( $date, 'rtwwwap-wp-wc-affiliate-program' ); ?>
												</td>
											</tr>
										<?php
										$count++;
										}
									}
									
								?>
							</tbody>
							
						</table>
					</td>
					</tr>
				</tbody>
			</table>

			<?php 
			
			$rtwwwap_exrta_tab = '' ;
			$rtwwwa_html = apply_filters('rtwwwap_social_login_settings',$rtwwwap_exrta_tab);
			?>
		</div>
	</div>


	<div class="rtwwwap-notification-wrapper">
		<div class="rtwwwap-popup-content">
			  <h3 class="rtwwwap-popup-heading"><?php esc_html_e( 'Notification Details', 'rtwwwap-wp-wc-affiliate-program' ); ?></h3>
				<div class="rtwwwap-popup-row">
					<div>
						<label class="rtwwwap_notification_title" for="rtwwwap_notification_title_inpt"><?php esc_html_e( 'enter Notification title', 'rtwwwap-wp-wc-affiliate-program' ); ?></label>
						<input type="text" class="rtwwwap_notification_title_inpt" id="rtwwwap_notification_title_inpt">
						<label class="rtwwwap_notification_title"><?php esc_html_e( 'enter Notification message', 'rtwwwap-wp-wc-affiliate-program' ); ?></label>
					 	<textarea rows="4" cols="65" maxlength="1000" class="rtwwwap_notification_textarea" placeholder='<?php esc_html_e("Enter your reason here within 100 words...", "rtwwwap-wp-wc-affiliate-program" )?>' ></textarea>
					</div>
				</div>
				<div class="rtwwwap-popup-footer">
					<input type="button" value="<?php esc_html_e( 'Save', 'rtwwwap-wp-wc-affiliate-program' ); ?>" class="rtwwwap-button" id="rtwwwap_save_notification">
					<input type="reset" name="" value="<?php esc_html_e( 'Cancel', 'rtwwwap-wp-wc-affiliate-program' ); ?>" class="rtwwwap-button rtwwwap-button-reset" id="rtwwwap_cancle_add_notification">
				</div>
		</div>
	</div>