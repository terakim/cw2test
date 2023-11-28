<?php
settings_fields( 'rtwwwap_reg_temp');
settings_fields( 'rtwwwap_extra_features');

$rtwwwap_reg_temp_features = get_option( 'rtwwwap_reg_temp_opt' );
$rtwwwap_login_temp_features = get_option( 'rtwwwap_extra_features_opt' );

?>
<table class="rtwwwap-table form-table">
	<tbody>
		<tr>
			<th><?php esc_html_e( 'Choose Register Template', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			<td class="tr2">
				<?php
				$rtwwwap_select_reg_temp = isset( $rtwwwap_reg_temp_features[ 'register_page' ] ) ? $rtwwwap_reg_temp_features[ 'register_page' ] : 1;
				?>
				<p><select class="rtwwwap_select2_page" id="" name="rtwwwap_reg_temp_opt[register_page]" >
					<option value="1" <?php selected( $rtwwwap_select_reg_temp, 1 ); ?> >
						<?php esc_html_e( "Template 1", 'rtwwwap-wp-wc-affiliate-program' ); ?>
					</option>
					<option value="2" <?php selected( $rtwwwap_select_reg_temp, 2 ); ?> >
						<?php esc_html_e( "Template 2", 'rtwwwap-wp-wc-affiliate-program' ); ?>
					</option>
					<option value="3" <?php selected( $rtwwwap_select_reg_temp, 3 ); ?> >
						<?php esc_html_e( "Template 3", 'rtwwwap-wp-wc-affiliate-program' ); ?>
					</option>
					<option value="4" <?php selected( $rtwwwap_select_reg_temp, 4 ); ?> >
						<?php esc_html_e( "Template 4", 'rtwwwap-wp-wc-affiliate-program' ); ?>
					</option>
				</select></p>
				<br>
				<div class="descr"><?php printf( '%s - %s. <b>( %s )</b>', esc_html__( 'Use the following shortcode on the page where you want to show the Register Form', 'rtwwwap-wp-wc-affiliate-program' ), '[rtwwwap_aff_reg_page]', esc_html__( 'Note: You need to activate Register setting from WordPress Settings->General->Membership', 'rtwwwap-wp-wc-affiliate-program' ) ); ?></div>
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e( 'Form Title', 'rtwwwap-wp-wc-affiliate-program' ); ?>
			</th>
			
			<td class="tr2">
				<input type="text" class="rtwwwap_admin_input_text" id="rtwwwap_form_title" name="rtwwwap_extra_features_opt[login_title]" value="<?php echo isset( $rtwwwap_login_temp_features[ 'login_title' ] ) ? $rtwwwap_login_temp_features[ 'login_title' ] : ''; ?>" placeholder="<?php esc_html_e( 'Enter Form Title', 'rtwwwap-wp-wc-affiliate-program' ); ?>" />
			</td>
		</tr>
	
		<tr>
			<th><?php esc_html_e( 'Use default colors for template', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			<td class="tr2">
				<?php
				$rtwwwap_use_default_color_checked = isset( $rtwwwap_reg_temp_features[ 'temp_colors' ] ) ? $rtwwwap_reg_temp_features[ 'temp_colors' ] : 1;
				?>
				<p>
					<span class="rtwwwap-custom-radio">
						<input id="radio-1" type="radio" name="rtwwwap_reg_temp_opt[temp_colors]" value="1"<?php checked( $rtwwwap_use_default_color_checked, 1 ); ?> /><?php esc_html_e( 'Yes', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						<label for="radio-1"></label>
					</span>
				</p>
				<p>
					<span class="rtwwwap-custom-radio">
						<input id="radio-2" type="radio" name="rtwwwap_reg_temp_opt[temp_colors]" value="0" <?php checked( $rtwwwap_use_default_color_checked, 0 ); ?> /><?php esc_html_e( 'No', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						<label for="radio-2"></label>
					</span>
				</p>
				<div class="descr"><?php esc_html_e( 'Activate to show Default template colors given with our plugin', 'rtwwwap-wp-wc-affiliate-program' );?></div>
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e( 'Main Background Color', 'rtwwwap-wp-wc-affiliate-program' ); ?>
			</th>
			<td class="tr2">
				<input type="text" id="rtwwwap_mainbgPicker" data-type="mainbg_color" class="rtwwwap_text_color_field" name="rtwwwap_reg_temp_opt[mainbg_color]" value="<?php echo isset( $rtwwwap_reg_temp_features[ 'mainbg_color' ] ) ? $rtwwwap_reg_temp_features[ 'mainbg_color' ] : ''; ?>" />
				<p class="rtwwwap_mainbg_color"><?php echo isset( $rtwwwap_reg_temp_features[ 'mainbg_color' ] ) ? $rtwwwap_reg_temp_features[ 'mainbg_color' ] : ''; ?></p>
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e( 'Form Background Color', 'rtwwwap-wp-wc-affiliate-program' ); ?>
			</th>
			<td class="tr2">
				<input type="text" id="rtwwwap_bgPicker" data-type="bg_color" class="rtwwwap_text_color_field" name="rtwwwap_reg_temp_opt[bg_color]" value="<?php echo isset( $rtwwwap_reg_temp_features[ 'bg_color' ] ) ? $rtwwwap_reg_temp_features[ 'bg_color' ] : ''; ?>" />
				<p class="rtwwwap_bg_color"><?php echo isset( $rtwwwap_reg_temp_features[ 'bg_color' ] ) ? $rtwwwap_reg_temp_features[ 'bg_color' ] : ''; ?></p>
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e( 'Form Header Color', 'rtwwwap-wp-wc-affiliate-program' ); ?>
			</th>
			<td class="tr2">
				<input type="text" id="rtwwwap_headPicker" data-type="head_color" class="rtwwwap_text_color_field" name="rtwwwap_reg_temp_opt[head_color]" value="<?php echo isset( $rtwwwap_reg_temp_features[ 'head_color' ] ) ? $rtwwwap_reg_temp_features[ 'head_color' ] : ''; ?>" />
				<p class="rtwwwap_head_color"><?php echo isset( $rtwwwap_reg_temp_features[ 'head_color' ] ) ? $rtwwwap_reg_temp_features[ 'head_color' ] : ''; ?></p>
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e( 'Button Color', 'rtwwwap-wp-wc-affiliate-program' ); ?>
			</th>
			<td class="tr2">
				<input type="text" id="rtwwwap_buttonPicker" data-type="text_color" class="rtwwwap_text_color_field" name="rtwwwap_reg_temp_opt[button_color]"  value="<?php echo isset( $rtwwwap_reg_temp_features[ 'button_color' ] ) ? $rtwwwap_reg_temp_features[ 'button_color' ] : ''; ?>" />
				<p class="rtwwwap_button_color"><?php echo isset( $rtwwwap_reg_temp_features[ 'button_color' ] ) ? $rtwwwap_reg_temp_features[ 'button_color' ] : ''; ?></p>
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e( 'Custom css', 'rtwwwap-wp-wc-affiliate-program' ); ?>
			</th>
			<td class="tr2">
				<textarea rows="4" class="rtwwwap_textarea_css" name="rtwwwap_reg_temp_opt[css]" ><?php echo isset( $rtwwwap_reg_temp_features[ 'css' ] ) ? $rtwwwap_reg_temp_features[ 'css' ] : ''; ?></textarea>
				<div class="descr"><?php esc_html_e( 'Write custom css for frontend', 'rtwwwap-wp-wc-affiliate-program' );?></div>
			</td>
		</tr>
	</tbody>
</table>