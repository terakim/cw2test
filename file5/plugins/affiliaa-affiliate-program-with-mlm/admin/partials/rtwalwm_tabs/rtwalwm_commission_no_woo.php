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
					$rtwalwm_comm_base = isset( $rtwalwm_commission_settings[ 'comm_base' ] ) ? $rtwalwm_commission_settings[ 'comm_base' ] : '2';
				?>
				<p>
					<span class="rtwalwm-custom-radio">
					  	<input id="radio-9" type="radio" class="rtwalwm_show_hide_prod_comm" name="rtwwwap_commission_settings_opt[comm_base]" value="2" <?php checked( $rtwalwm_comm_base, 2 ); ?> />
					  	<?php printf( '%s ( %s <a href=%s target="_blank">%s</a> )', esc_html__( 'Users', 'rtwalwm-wp-wc-affiliate-program' ), esc_html__( 'To set commission for users goto', 'rtwalwm-wp-wc-affiliate-program' ), esc_url( admin_url( 'admin.php?page=rtwalwm&rtwalwm_tab=rtwalwm_levels' ) ), esc_html__( 'Levels', 'rtwalwm-wp-wc-affiliate-program' ) ); ?>
					  	<label for="radio-9"></label>
				    </span>
				</p>
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e( 'Maximum commission to an Affiliate in a month', 'rtwalwm-wp-wc-affiliate-program' ); ?>
			</th>
			<td class="tr2">
				<input type="number" min="0" name="rtwwwap_commission_settings_opt[max_commission]" value="" />
				<div class="descr">
					<?php esc_html_e( 'Enter Max. Commission (By default 0, that means unlimited commission)', 'rtwalwm-wp-wc-affiliate-program' );?>
				</div>
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e( 'Withdrawal Fees', 'rtwalwm-wp-wc-affiliate-program' ); ?>
			</th>
			<td class="tr2">
				<input type="number" min="0" step="0.01" name="rtwwwap_commission_settings_opt[withdraw_commission]" value="" />
				<div class="descr">
					<?php esc_html_e( 'Enter Fees to be deducted while payouts (By default 0)', 'rtwalwm-wp-wc-affiliate-program' ); ?>
				</div>
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e( 'Unlimited / Lifetime Commission', 'rtwalwm-wp-wc-affiliate-program' ); ?>
			</th>
			<td class="tr2">
				
				<p>
					<span class="rtwalwm-custom-radio">
						<input id="radio-10" type="radio" class="rtwalwm_override_show_hide" name="" value="1"  /><?php esc_html_e( 'Yes', 'rtwalwm-wp-wc-affiliate-program' ); ?>
						<label for="radio-10"></label>
					</span>
				</p>
				<p>
					<span class="rtwalwm-custom-radio">
					  	<input id="radio-11" type="radio" class="rtwalwm_override_show_hide" name="" value=""  /><?php esc_html_e( 'No', 'rtwalwm-wp-wc-affiliate-program' ); ?>
					  	<label for="radio-11"></label>
				    </span>
				</p>
				<div class="descr">
					<?php esc_html_e( 'When Unlimited commission is set then commission will be generated every time a referee made a purchase. No matter if a cookie is set or not.', 'rtwalwm-wp-wc-affiliate-program' ); ?>
				</div>
			</td>
		</tr>
		<tr class="rtwalwm_override rtwalwm_override_hide">
			<th>
				<?php esc_html_e( 'Override Referrer in Unlimited Commission', 'rtwalwm-wp-wc-affiliate-program' ); ?>
			</th>
			<td class="tr2">
			
				<p>
					<span class="rtwalwm-custom-radio">
						<input id="radio-12" type="radio" class="" name="" value=""  /><?php esc_html_e( 'Yes', 'rtwalwm-wp-wc-affiliate-program' ); ?>
						<label for="radio-12"></label>
					</span>
				</p>
				<p>
					<span class="rtwalwm-custom-radio">
					  	<input id="radio-13" type="radio" class="" name="rtwwwap_commission_settings_opt[override_unlimit_comm]" value=""  /><?php esc_html_e( 'No', 'rtwalwm-wp-wc-affiliate-program' ); ?>
					  	<label for="radio-13"></label>
				    </span>
				</p>
				<div class="descr">
					<?php esc_html_e( "When not selected then first referrer will get commission every time a purchase is done by referee. No matter who's referral link is being opened.", 'rtwalwm-wp-wc-affiliate-program' ); ?>
				</div>
			</td>
		</tr>
	</tbody>	
</table>