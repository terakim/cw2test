<?php
	settings_fields( 'rtwwwap_levels_settings' );
	$rtwwwap_levels_settings = get_option( 'rtwwwap_levels_settings_opt' );
	$rtwwwap_level_id = isset( $_GET[ 'edit' ] ) ? $_GET[ 'edit' ] : '';
?>

<?php
if( isset( $_GET[ 'edit' ] ) ){
?>
	<table class="rtwwwap-table form-table">
		<tbody>
			<?php 
				foreach( $rtwwwap_levels_settings as $rtwwwap_key => $rtwwwap_value ){
					if( $rtwwwap_key == $rtwwwap_level_id ){
			?>
					<tr>
						<th><?php esc_html_e( 'Level', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
						<td class="tr2">
							<input class="rtwwwap_level_name" type="text" required="required" name="rtwwwap_levels_settings_opt[level_name]" value="<?php echo esc_attr( $rtwwwap_value[ 'level_name' ] ); ?>" />
							<input type="hidden" name="rtwwwap_levels_settings_opt[rtwwwap_level]" value="edit" />
							<input type="hidden" name="rtwwwap_levels_settings_opt[level_id]" value="<?php echo esc_attr( $_GET[ 'edit' ] ); ?>" />
						</td>
					</tr>
					<tr>
						<th>
							<?php esc_html_e( 'Commission', 'rtwwwap-wp-wc-affiliate-program' ); ?>
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
								<tbody class="rtwwwap_tbody">
									<tr clas="rtwwwap_level_commission">
										<td>
											<?php
												$rtwwwap_level_comm_selected = isset( $rtwwwap_value[ 'level_commission_type' ] ) ? $rtwwwap_value[ 'level_commission_type' ] : '0';
											?>
											<select class="rtwwwap_select2_level" id="" name="rtwwwap_levels_settings_opt[level_commission_type]" >
												<option value="0" <?php selected( $rtwwwap_level_comm_selected, 'percentage' ); ?> >
													<?php esc_html_e( 'Percentage', 'rtwwwap-wp-wc-affiliate-program' ); ?>
												</option>
												<option value="1" <?php selected( $rtwwwap_level_comm_selected, 'fixed' ); ?> >
													<?php esc_html_e( 'Fixed', 'rtwwwap-wp-wc-affiliate-program' ); ?>
												</option>
											</select>
										</td>
										<td>
											<input class="rtwwwap_level_commission_amount" type="number" min="0" step="0.01" name="rtwwwap_levels_settings_opt[level_comm_amount]" value="<?php echo esc_attr( $rtwwwap_value['level_comm_amount'], 'rtwwwap-wp-wc-affiliate-program' ); ?>" />
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<th>
							<?php esc_html_e( 'To Reach', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</th>
						<td class="tr2">
							<table>
								<thead>
									<th>
										<?php esc_html_e( 'Criteria', 'rtwwwap-wp-wc-affiliate-program' ); ?>
									</th>
									<th>
										<?php esc_html_e( 'Value', 'rtwwwap-wp-wc-affiliate-program' ); ?>
									</th>
								</thead>
								<tbody class="rtwwwap_tbody">
									<tr clas="rtwwwap_level_criteria">
										<td>
											<?php
												$rtwwwap_level_criteria_selected = isset( $rtwwwap_value[ 'level_criteria_type' ] ) ? $rtwwwap_value[ 'level_criteria_type' ] : '0';
											?>
											<select class="rtwwwap_select2_level_criteria" id="" name="rtwwwap_levels_settings_opt[level_criteria_type]" >
												<option value="0" <?php selected( $rtwwwap_level_criteria_selected, '0' ); ?> >
													<?php esc_html_e( 'Become Affiliate', 'rtwwwap-wp-wc-affiliate-program' ); ?>
												</option>
												<option value="1" <?php selected( $rtwwwap_level_criteria_selected, '1' ); ?> >
													<?php esc_html_e( 'No. of Referrals', 'rtwwwap-wp-wc-affiliate-program' ); ?>
												</option>
												<option value="2" <?php selected( $rtwwwap_level_criteria_selected, '2' ); ?> >
													<?php esc_html_e( 'Total sale amount', 'rtwwwap-wp-wc-affiliate-program' ); ?>
												</option>
											</select>
										</td>
										<td>
											<input class="rtwwwap_level_criteria_amount" type="number" <?php disabled( $rtwwwap_level_criteria_selected, '0' ); ?> min="0" name="rtwwwap_levels_settings_opt[level_criteria_val]" value="<?php echo esc_attr( $rtwwwap_value['level_criteria_val'], 'rtwwwap-wp-wc-affiliate-program' ); ?>" />
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
			<?php 	} 
				}
			?>
		</tbody>
	</table>
<?php 
	}
	else
	{
?>
		<table class="rtwwwap-table form-table">
			<tbody>
				<tr>
					<th><?php esc_html_e( 'Level', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
					<td class="tr2">
						<input class="rtwwwap_level_name" type="text" required="required" name="rtwwwap_levels_settings_opt[level_name]" value="" />
						<input type="hidden" name="rtwwwap_levels_settings_opt[rtwwwap_level]" value="add" />
					</td>
				</tr>
				<tr>
					<th>
						<?php esc_html_e( 'Commission', 'rtwwwap-wp-wc-affiliate-program' ); ?>
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
							<tbody class="rtwwwap_tbody">
								<tr clas="rtwwwap_level_commission">
									<td>
										<select class="rtwwwap_select2_level" id="" name="rtwwwap_levels_settings_opt[level_commission_type]" >
											<option value="0">
												<?php esc_html_e( 'Percentage', 'rtwwwap-wp-wc-affiliate-program' ); ?>
											</option>
											<option value="1">
												<?php esc_html_e( 'Fixed', 'rtwwwap-wp-wc-affiliate-program' ); ?>
											</option>
										</select>
									</td>
									<td>
										<input class="rtwwwap_level_commission_amount" type="number" min="0" step="0.01" name="rtwwwap_levels_settings_opt[level_comm_amount]" value="0" />
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<th>
						<?php esc_html_e( 'To Reach', 'rtwwwap-wp-wc-affiliate-program' ); ?>
					</th>
					<td class="tr2">
						<table>
							<thead>
								<th>
									<?php esc_html_e( 'Criteria', 'rtwwwap-wp-wc-affiliate-program' ); ?>
								</th>
								<th>
									<?php esc_html_e( 'Value', 'rtwwwap-wp-wc-affiliate-program' ); ?>
								</th>
							</thead>
							<tbody class="rtwwwap_tbody">
								<tr clas="rtwwwap_level_criteria">
									<td>
										<select class="rtwwwap_select2_level_criteria" id="" name="rtwwwap_levels_settings_opt[level_criteria_type]" >
											<option value="0">
												<?php esc_html_e( 'Become Affiliate', 'rtwwwap-wp-wc-affiliate-program' ); ?>
											</option>
											<option value="1">
												<?php esc_html_e( 'No. of Referrals', 'rtwwwap-wp-wc-affiliate-program' ); ?>
											</option>
											<option value="2">
												<?php esc_html_e( 'Total sale amount', 'rtwwwap-wp-wc-affiliate-program' ); ?>
											</option>
										</select>
									</td>
									<td>
										<input class="rtwwwap_level_criteria_amount" type="number" disabled="disabled" min="0" name="rtwwwap_levels_settings_opt[level_criteria_val]" value="0" />
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
<?php
	}
?>