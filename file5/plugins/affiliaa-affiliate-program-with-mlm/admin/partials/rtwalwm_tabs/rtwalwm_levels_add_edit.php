<?php
	
?>

		<table class="rtwalwm-table form-table">
			<tbody>
				<tr>
					<th><?php esc_html_e( 'Level', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
					<td class="tr2">
						<input class="rtwalwm_level_name" type="text" required="required" name="rtwalwm_levels_settings_opt[level_name]" value="" disabled/>
						<input type="hidden" name="rtwalwm_levels_settings_opt[rtwalwm_level]" value="add" />
					</td>
				</tr>
				<tr>
					<th>
						<?php esc_html_e( 'Commission', 'rtwalwm-wp-wc-affiliate-program' ); ?>
					</th>
					<td class="tr2">
						<table>
							<thead>
								<th>
									<?php esc_html_e( 'Type', 'rtwalwm-wp-wc-affiliate-program' ); ?>
								</th>
								<th>
									<?php esc_html_e( 'Amount', 'rtwalwm-wp-wc-affiliate-program' ); ?>
								</th>
							</thead>
							<tbody class="rtwalwm_tbody">
								<tr clas="rtwalwm_level_commission">
									<td>
										<select class="rtwalwm_select2_level" id="" name="rtwalwm_levels_settings_opt[level_commission_type]" >
											
											<option value="0" disabled>
												<?php esc_html_e( 'Percentage', 'rtwalwm-wp-wc-affiliate-program' ); ?>
											</option>
											<option value="1" disabled>
												<?php esc_html_e( 'Fixed', 'rtwalwm-wp-wc-affiliate-program' ); ?>
											</option>
										</select>
									</td>
									<td>
										<input class="rtwalwm_level_commission_amount" type="number" min="0" step="0.01" name="rtwalwm_levels_settings_opt[level_comm_amount]" value="0" disabled/>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<th>
						<?php esc_html_e( 'To Reach', 'rtwalwm-wp-wc-affiliate-program' ); ?>
					</th>
					<td class="tr2">
						<table>
							<thead>
								<th>
									<?php esc_html_e( 'Criteria', 'rtwalwm-wp-wc-affiliate-program' ); ?>
								</th>
								<th>
									<?php esc_html_e( 'Value', 'rtwalwm-wp-wc-affiliate-program' ); ?>
								</th>
							</thead>
							<tbody class="rtwalwm_tbody">
								<tr clas="rtwalwm_level_criteria">
									<td>
										<select class="rtwalwm_select2_level_criteria" id="" name="rtwalwm_levels_settings_opt[level_criteria_type]" >
											<optiondisabled>
												<?php esc_html_e( 'Become Affiliate', 'rtwalwm-wp-wc-affiliate-program' ); ?>
											</option>
											<option disabled>
												<?php esc_html_e( 'No. of Referrals', 'rtwalwm-wp-wc-affiliate-program' ); ?>
											</option>
											<option  disabled>
												<?php esc_html_e( 'Total sale amount', 'rtwalwm-wp-wc-affiliate-program' ); ?>
											</option>
										</select>
									</td>
									<td>
										<input class="rtwalwm_level_criteria_amount" type="number" disabled="disabled" min="0" name="rtwalwm_levels_settings_opt[level_criteria_val]" value="0" disabled/>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
