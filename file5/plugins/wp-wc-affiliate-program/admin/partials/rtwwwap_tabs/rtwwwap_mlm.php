<?php
	settings_fields( 'rtwwwap_mlm');
	$rtwwwap_mlm = get_option( 'rtwwwap_mlm_opt' );
    $rtwwwap_mlm_com_base = isset($rtwwwap_mlm['mlm_commission_base'])? $rtwwwap_mlm['mlm_commission_base'] : 1;

	// echo '<pre>';
	// print_r($rtwwwap_mlm);
	// echo '</pre>';
	// die("zsvd");
    $rtwwwap_html1 = "";
    $rtwwwap_html = apply_filters('rtwwwap_mlm_custom_chain',$rtwwwap_html1);
	echo  $rtwwwap_html;
?>

<table class="rtwwwap-table form-table">
	<tbody>
		<tr>
			<th><?php esc_html_e( 'Activate MLM', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			<td class="tr2">
				<?php
					$rtwwwap_mlm_activate_checked = 0;
					if( isset( $rtwwwap_mlm[ 'activate' ] ) && $rtwwwap_mlm[ 'activate' ] == 1 ){
						$rtwwwap_mlm_activate_checked = 1;
					}
				?>
				<p>
					<span class="rtwwwap-custom-radio">
						<input id="radio-1" type="radio" name="rtwwwap_mlm_opt[activate]" value="1" <?php checked( $rtwwwap_mlm_activate_checked, 1 ); ?> /><?php esc_html_e( 'On', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						<label for="radio-1"></label>
					</span>
				</p>
				<p>
					<span class="rtwwwap-custom-radio">
						<input id="radio-2" type="radio" name="rtwwwap_mlm_opt[activate]" value="0" <?php checked( $rtwwwap_mlm_activate_checked, 0 ); ?> /><?php esc_html_e( 'Off', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						<label for="radio-2"></label>
					</span>
				</p>
			</td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'MLM Type', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			<td class="tr2">
				<?php
					$rtwwwap_mlm_type_selected = isset( $rtwwwap_mlm[ 'mlm_type' ] ) ? $rtwwwap_mlm[ 'mlm_type' ] : 0;
				?>
				<p>
					<select class="rtwwwap_select2_mlm" id="" name="rtwwwap_mlm_opt[mlm_type]" >
						<option value="0" <?php selected( $rtwwwap_mlm_type_selected, '0' ) ?> >
							<?php esc_html_e( 'Binary', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</option>
						<option value="1" <?php selected( $rtwwwap_mlm_type_selected, '1' ) ?> >
							<?php esc_html_e( 'Forced Matrix', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</option>
						<option value="2" <?php selected( $rtwwwap_mlm_type_selected, '2' ) ?> >
							<?php esc_html_e( 'Unilevel', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</option>
						<option value="3" <?php selected( $rtwwwap_mlm_type_selected, '3' ) ?> >
							<?php esc_html_e( 'Unlimited', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</option>
					</select>
				</p>
				<br>
				<div class="descr"><?php esc_html_e( 'NOTE : If you change MLM Plan then you need to activate/deactivate members to make the chains according to your plan selected', 'rtwwwap-wp-wc-affiliate-program' );?></div>
			</td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'MLM commission Based', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			<td class="tr2">
				<p>
					<span class="rtwalwm-custom-radio">
						<input id="radio-24" type="radio" name="rtwwwap_mlm_opt[mlm_commission_base]" value="0" <?php checked( $rtwwwap_mlm_com_base, 0 ); ?>/> <?php esc_html_e( 'Based on product price', 'rtwalwm-wp-wc-affiliate-program' ); ?>
						<label for="radio-24"></label>
					</span>
				</p>
				<p>
					<span class="rtwalwm-custom-radio">
						<input id="radio-25" type="radio" name="rtwwwap_mlm_opt[mlm_commission_base]" value="1" <?php checked( $rtwwwap_mlm_com_base, 1 ); ?>/><?php esc_html_e( ' Based on the amount earned by the affiliate as commission ', 'rtwalwm-wp-wc-affiliate-program' ); ?>
						<label for="radio-25"></label>
					</span>
				</p>
			</td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Depth', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			<td class="tr2">
				<input class="rtwwwap_mlm_depth" data-rtwwwap_depth="<?php echo isset( $rtwwwap_mlm[ 'depth' ] ) ? esc_attr( $rtwwwap_mlm[ 'depth' ] ) : esc_attr( 1 ); ?>" type="number" min="1" name="rtwwwap_mlm_opt[depth]" value="<?php echo isset( $rtwwwap_mlm[ 'depth' ] ) ? esc_attr( $rtwwwap_mlm[ 'depth' ] ) : esc_attr( 1 ); ?>" />
				<div class="descr"><?php esc_html_e( 'How many levels does this MLM can have? ( Note: By Default 1 )', 'rtwwwap-wp-wc-affiliate-program' );?></div>
			</td>
		</tr>
		<tr class="<?php if( $rtwwwap_mlm_type_selected == 2 ){ echo 'rtwwwap_mlm_child_hidden'; } ?>" >
			<th><?php esc_html_e( 'Child', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			<td class="tr2">
				<?php
				
						$rtwwwap_max_child = 0;
						if( $rtwwwap_mlm_type_selected == 0 ){
							$rtwwwap_max_child = 2;
						}
					
				?>
				<input type="number" id="rtwwwap_mlm_child" min="1" name="rtwwwap_mlm_opt[child]" value="<?php echo isset( $rtwwwap_mlm[ 'child' ] ) ? esc_attr( $rtwwwap_mlm[ 'child' ] ) : esc_attr( 1 ); ?>" <?php if( $rtwwwap_max_child ){ echo "max=$rtwwwap_max_child"; } disabled( $rtwwwap_mlm_type_selected, 2 ); ?> />
				<div class="descr"><?php esc_html_e( 'How many childs a User can have?', 'rtwwwap-wp-wc-affiliate-program' );?></div>
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e( 'Default Commission for a Level', 'rtwwwap-wp-wc-affiliate-program' ); ?>
			</th>
			<td class="tr2">
				<table>
					<thead>
						<th>
							<?php esc_html_e( 'Type', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
						<th>
							<?php esc_html_e( 'Amount', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
					</thead>
					<tbody>
						<tr class="rtwwwap_mlm_default_comm">
							<td>
								<?php
									$rtwwwap_mlm_default_comm_selected = isset( $rtwwwap_mlm[ 'mlm_default_comm' ] ) ? $rtwwwap_mlm[ 'mlm_default_comm' ] : 0;
								?>
								<select class="rtwwwap_select2_mlm_default_comm" id="" name="rtwwwap_mlm_opt[mlm_default_comm]" >
									<option value="0" <?php selected( $rtwwwap_mlm_default_comm_selected, 0 ); ?> >
										<?php esc_html_e( 'Percentage', 'rtwwwap-wp-wc-affiliate-program' ); ?>
									</option>
									<option value="1" <?php selected( $rtwwwap_mlm_default_comm_selected, 1 ); ?> >
										<?php esc_html_e( 'Fixed', 'rtwwwap-wp-wc-affiliate-program' ); ?>
									</option>
								</select>
							</td>
							<td>
								<input class="rtwwwap_mlm_default_comm_amount" type="number" min="1" step="0.01" name="rtwwwap_mlm_opt[mlm_default_comm_amount]" value="<?php echo isset( $rtwwwap_mlm[ 'mlm_default_comm_amount' ] ) ? $rtwwwap_mlm[ 'mlm_default_comm_amount' ] : esc_attr( 1 ) ?>" />
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e( 'MLM Levels', 'rtwwwap-wp-wc-affiliate-program' ); ?>
			</th>
			<td class="tr2">
				<table>
					<thead>
						<th>
							<?php esc_html_e( 'Level', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
						<th>
							<?php esc_html_e( 'Commission Type', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
						<th>
							<?php esc_html_e( 'Commission Amount', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
					</thead>
					<tbody class="rtwwwap_tbody">
						<!-- hidden row start-->
						<tr class="rtwwwap_add_new_row_hidden rtwwwap_mlm_level_comm" style="display: none;">
							<td name="rtwwwap_mlm_opt[mlm_levels][mlm_level_id]">
								<?php echo esc_html( '0' ); ?>
							</td>
							<td>
								<select class="rtwwwap_select2_mlm_level_comm_type_hidden" id="" name="rtwwwap_mlm_opt[mlm_levels][mlm_level_comm_type]" >
									<option value="0">
										<?php esc_html_e( 'Percentage', 'rtwwwap-wp-wc-affiliate-program' ); ?>
									</option>
									<option value="1">
										<?php esc_html_e( 'Fixed', 'rtwwwap-wp-wc-affiliate-program' ); ?>
									</option>
								</select>
							</td>
							<td>
								<input class="rtwwwap_mlm_level_comm_amount" type="number" min="0" step="0.01" name="rtwwwap_mlm_opt[mlm_levels][mlm_level_comm_amount]" value="<?php echo esc_attr( 0 ); ?>" />
							</td>
						</tr>
						<!-- hidden row end-->
						<?php 
							if( !empty( $rtwwwap_mlm[ 'mlm_levels' ] ) )
							{
								foreach( $rtwwwap_mlm[ 'mlm_levels' ] as $rtwwwap_mlm_key => $rtwwwap_mlm_value )
								{
						?>
									<tr class="rtwwwap_mlm_level_comm">
										<td name="rtwwwap_mlm_opt[mlm_levels][ <?php echo esc_attr( $rtwwwap_mlm_key ); ?> ][mlm_level_id]">
											<?php echo esc_html( $rtwwwap_mlm_key ); ?>
										</td>
										<td>
											<?php
												$rtwwwap_selected_level = ( isset( $rtwwwap_mlm[ 'mlm_levels' ][ $rtwwwap_mlm_key ][ 'mlm_level_comm_type' ] ) ) ? esc_attr( $rtwwwap_mlm[ 'mlm_levels' ][ $rtwwwap_mlm_key ][ 'mlm_level_comm_type' ] ) : esc_attr( '0' );
											?>
											<select class="rtwwwap_select2_mlm_level_comm_type" id="" name="rtwwwap_mlm_opt[mlm_levels][ <?php echo esc_attr( $rtwwwap_mlm_key ); ?> ][mlm_level_comm_type]" >
												<option value="0" <?php selected( $rtwwwap_selected_level, 0, true ); ?> >
													<?php esc_html_e( 'Percentage', 'rtwwwap-wp-wc-affiliate-program' ); ?>
												</option>
												<option value="1" <?php selected( $rtwwwap_selected_level, 1, true ); ?> >
													<?php esc_html_e( 'Fixed', 'rtwwwap-wp-wc-affiliate-program' ); ?>
												</option>
											</select>
										</td>
										<td>
											<?php
												$rtwwwap_comm_amount = ( isset( $rtwwwap_mlm[ 'mlm_levels' ][ $rtwwwap_mlm_key ][ 'mlm_level_comm_amount' ] ) ) ? $rtwwwap_mlm[ 'mlm_levels' ][ $rtwwwap_mlm_key ][ 'mlm_level_comm_amount' ] : '0';
											?>
											<input class="rtwwwap_mlm_level_comm_amount" type="number" min="0" step="0.01" name="rtwwwap_mlm_opt[mlm_levels][ <?php echo esc_attr( $rtwwwap_mlm_key ); ?> ][mlm_level_comm_amount]" value="<?php echo esc_attr( $rtwwwap_comm_amount ); ?>" />
										</td>
									</tr>
						<?php
								}
							}
							else
							{
						?>
								<tr class="rtwwwap_mlm_default_comm">
									<td name="rtwwwap_mlm_opt[mlm_levels][1][mlm_level_id]" value="<?php echo esc_attr( '1' ); ?>">
										<?php echo esc_html( '1' ); ?>
									</td>
									<td>
										<select class="rtwwwap_select2_mlm_level_comm_type" id="" name="rtwwwap_mlm_opt[mlm_levels][1][mlm_level_comm_type]" >
											<option value="0">
												<?php esc_html_e( 'Percentage', 'rtwwwap-wp-wc-affiliate-program' ); ?>
											</option>
											<option value="1">
												<?php esc_html_e( 'Fixed', 'rtwwwap-wp-wc-affiliate-program' ); ?>
											</option>
										</select>
									</td>
									<td>
										<input class="rtwwwap_mlm_level_comm_amount" type="number" min="0" step="0.01" name="rtwwwap_mlm_opt[mlm_levels][1][mlm_level_comm_amount]" value="<?php echo esc_attr( 0 ); ?>" />
									</td>
								</tr>
						<?php 
							}
						?>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'User can change status of members in his chain?', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			<td class="tr2">
				<?php
					$rtwwwap_mlm_user_status_checked = 0;
					if( isset( $rtwwwap_mlm[ 'user_status' ] ) && $rtwwwap_mlm[ 'user_status' ] == 1 ){
						$rtwwwap_mlm_user_status_checked = 1;
					}
				?>
				<p>
					<span class="rtwwwap-custom-radio">
						<input id="radio-3" type="radio" name="rtwwwap_mlm_opt[user_status]" value="1" <?php checked( $rtwwwap_mlm_user_status_checked, 1 ); ?> /><?php esc_html_e( 'Yes', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						<label for="radio-3"></label>
					</span>
				</p>
				<p>
					<span class="rtwwwap-custom-radio">
						<input id="radio-4" type="radio" name="rtwwwap_mlm_opt[user_status]" value="0" <?php checked( $rtwwwap_mlm_user_status_checked, 0 ); ?> /><?php esc_html_e( 'No', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						<label for="radio-4"></label>
					</span>
				</p>
			</td>
		</tr>
		
			<?php
				if(in_array('addon_for_MLM_qualification/affiliate_mlm_addon.php', apply_filters('active_plugins', get_option('active_plugins') ) ) ){
					do_action('rtwwwap_add_mlm_content');
				}
				
			?>
			
	</tbody>	
</table>