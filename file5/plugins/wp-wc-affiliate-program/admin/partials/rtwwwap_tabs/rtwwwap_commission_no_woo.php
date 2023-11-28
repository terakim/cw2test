<?php
	settings_fields( 'rtwwwap_commission_settings' );
	$rtwwwap_commission_settings = get_option( 'rtwwwap_commission_settings_opt' );
?>

<table class="rtwwwap-table form-table">
	<tbody>
		<tr>
			<th>
				<?php esc_html_e( 'Commission Based on', 'rtwwwap-wp-wc-affiliate-program' ); ?>
			</th>
			<td class="tr2">
				<?php
					$rtwwwap_comm_base = isset( $rtwwwap_commission_settings[ 'comm_base' ] ) ? $rtwwwap_commission_settings[ 'comm_base' ] : '2';
				?>
				<p>
					<span class="rtwwwap-custom-radio">
					  	<input id="radio-9" type="radio" class="rtwwwap_show_hide_prod_comm" name="rtwwwap_commission_settings_opt[comm_base]" value="2" <?php checked( $rtwwwap_comm_base, 2 ); ?> />
					  	<?php sprintf( '%s ( %s <a href=%s target="_blank">%s</a> )', esc_html__( 'Users', 'rtwwwap-wp-wc-affiliate-program' ), esc_html__( 'To set commission for users goto', 'rtwwwap-wp-wc-affiliate-program' ), esc_url( admin_url( 'admin.php?page=rtwwwap&rtwwwap_tab=rtwwwap_levels' ) ), esc_html__( 'Levels', 'rtwwwap-wp-wc-affiliate-program' ) ); ?>
					  	<label for="radio-9"></label>
				    </span>
				</p>
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e( 'Maximum commission to an Affiliate in a month', 'rtwwwap-wp-wc-affiliate-program' ); ?>
			</th>
			<td class="tr2">
				<input type="number" min="0" name="rtwwwap_commission_settings_opt[max_commission]" value="<?php echo isset( $rtwwwap_commission_settings[ 'max_commission' ] ) ? esc_attr( $rtwwwap_commission_settings[ 'max_commission' ] ) : esc_attr( '0' ); ?>" />
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
	</tbody>	
</table>