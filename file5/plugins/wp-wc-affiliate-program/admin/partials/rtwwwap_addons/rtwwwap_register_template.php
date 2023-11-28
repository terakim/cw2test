<?php
settings_fields( 'rtwwwap_reg_temp');
$rtwwwap_reg_temp_features = get_option( 'rtwwwap_reg_temp_opt' );
  
$rtwwwap_reg_temp_features['login_title'] = isset($rtwwwap_reg_temp_features['login_title'])? $rtwwwap_reg_temp_features['login_title'] : ''; 
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
			<td></td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e( 'Form Title', 'rtwwwap-wp-wc-affiliate-program' ); ?>
			</th>
			<td class="tr2">
				<input type="text" class="rtwwwap_admin_input_text" id="rtwwwap_form_title" name="rtwwwap_reg_temp_opt[title]" value="<?php echo isset( $rtwwwap_reg_temp_features[ 'title' ] ) ? $rtwwwap_reg_temp_features[ 'title' ] : ''; ?>" placeholder="<?php esc_html_e( 'Enter Form Title', 'rtwwwap-wp-wc-affiliate-program' ); ?>" />
			</td>
			<td></td>
		</tr>
		<tr class="rtwwwap_add_remove_btn">
			<th>
				<?php esc_html_e( 'Custom Form Fields', 'rtwwwap-wp-wc-affiliate-program' ); ?>
			</th>
			<td class="rtwwwap-input_type-fields">
				<div class="rtwwwap-input_type-wrapper">
					<?php 	$rtwwwap_reg_custom_fields = isset($rtwwwap_reg_temp_features['custom-input']) ? $rtwwwap_reg_temp_features['custom-input'] : array(); 
					$final_count = 0;
					if(is_array($rtwwwap_reg_custom_fields) && !empty($rtwwwap_reg_custom_fields)){
						foreach ($rtwwwap_reg_custom_fields as $count => $custom_fields) {

							if($count == 0){
								$counter = '';
							}else{
								$counter = $count;
								$final_count = $count;
							} ?>
							<div class="rtwwwap-input_type-inner-wrapper<?php echo esc_attr($counter,  'rtwwwap-wp-wc-affiliate-program'); ?> rtwwwap-input_type-inner">
								<input type="hidden" class="rtwwwap_current_clone_count" value="<?php echo esc_attr($count,  'rtwwwap-wp-wc-affiliate-program'); ?>">
								<span class="rtwwwap-custom-label-span">
									<label for="rtwwwap-custom-label-class"><?php esc_html_e( 'Label', 'rtwwwap-wp-wc-affiliate-program' ); ?></label>
									<input type="text" name="rtwwwap_reg_temp_opt[custom-input][<?php echo esc_attr($count,  'rtwwwap-wp-wc-affiliate-program'); ?>][custom-input-label]" class="rtwwwap-custom-label" value="<?php echo esc_attr($custom_fields['custom-input-label'],  'rtwwwap-wp-wc-affiliate-program'); ?>" >
								</span>
								<span class="rtwwwap-custom-span">
									<label for="rtwwwap-custom-input_type"><?php esc_html_e( 'Input Type', 'rtwwwap-wp-wc-affiliate-program' ); ?></label>
									<select data-current_count="<?php echo esc_attr($count,  'rtwwwap-wp-wc-affiliate-program'); ?>" name="rtwwwap_reg_temp_opt[custom-input][<?php echo esc_attr($count,  'rtwwwap-wp-wc-affiliate-program'); ?>][custom-input-type]" class="rtwwwap-custom-input_type">
										<option><?php esc_html_e( 'Select Input Type', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>
										<option <?php selected( $custom_fields['custom-input-type'], 'text' ); ?> value="text"><?php esc_html_e( 'Text', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>
										<option <?php selected( $custom_fields['custom-input-type'], 'number' ); ?> value="number"><?php esc_html_e( 'Number', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>
										<option <?php selected( $custom_fields['custom-input-type'], 'checkbox' ); ?> value="checkbox"><?php esc_html_e( 'CheckBox', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>
										<option <?php selected( $custom_fields['custom-input-type'], 'radio' ); ?> value="radio"><?php esc_html_e( 'Radio', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>
										<option <?php selected( $custom_fields['custom-input-type'], 'textarea' ); ?> value="textarea"><?php esc_html_e( 'TextArea', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>
										<option <?php selected( $custom_fields['custom-input-type'], 'select' ); ?> value="select"><?php esc_html_e( 'Select', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>
									</select>
								</span>

								<span class="rtwwwap-custom-type-span">
									<label for="rtwwwap-custom-type-class"><?php esc_html_e( 'Class', 'rtwwwap-wp-wc-affiliate-program' ); ?></label>
									<input type="text" name="rtwwwap_reg_temp_opt[custom-input][<?php echo esc_attr($count,  'rtwwwap-wp-wc-affiliate-program'); ?>][custom-input-class]" class="rtwwwap-custom-type-class" value="<?php echo  esc_attr(empty($custom_fields['custom-input-class'])? $custom_fields['custom-input-label']:$custom_fields['custom-input-class'],  'rtwwwap-wp-wc-affiliate-program'); ?>">
								</span>
								
								<span class="rtwwwap-custom-type-span">
									<label for="rtwwwap-custom-type-id"><?php esc_html_e( 'Id', 'rtwwwap-wp-wc-affiliate-program' ); ?></label>
									<input type="text" name="rtwwwap_reg_temp_opt[custom-input][<?php echo  esc_attr($count,  'rtwwwap-wp-wc-affiliate-program'); ?>][custom-input-id]" class="rtwwwap-custom-type-id" value="<?php echo  esc_attr(empty($custom_fields['custom-input-label'])?$custom_fields['custom-input-label']: $custom_fields['custom-input-id'],'rtwwwap-wp-wc-affiliate-program') ?>">
								</span>
								
								<span class="rtwwwap-custom-type-span">
									<label for="rtwwwap-custom-type-placeholder"><?php esc_html_e( 'Placeholder', 'rtwwwap-wp-wc-affiliate-program' ); ?></label>
									<input type="text" class="rtwwwap-custom-type-placeholder" name="rtwwwap_reg_temp_opt[custom-input][<?php echo  esc_attr($count,  'rtwwwap-wp-wc-affiliate-program'); ?>][custom-input-placeholder]" value="<?php echo esc_attr($custom_fields['custom-input-placeholder'], 'rtwwwap-wp-wc-affiliate-program'); ?>">
								</span>
								
								<span class="rtwwwap-input_type-span rtwwwap-after-clone" id = "<?php echo $count?>">
									<label for="rtwwwap-input_type-required"><?php esc_html_e( 'Required', 'rtwwwap-wp-wc-affiliate-program' ); ?></label>
									<select name="rtwwwap_reg_temp_opt[custom-input][<?php echo esc_attr($count,'rtwwwap-wp-wc-affiliate-program'); ?>][custom-input-isrequired]" class="rtwwwap-input_type-required">
										<option <?php selected( $custom_fields['custom-input-isrequired'], 'no' ); ?> value="no"><?php esc_html_e( 'No', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>
										<option <?php selected( $custom_fields['custom-input-isrequired'], 'yes' ); ?> value="yes"><?php esc_html_e( 'Yes', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>
									</select>
								</span>
								<?php
								if(count($rtwwwap_reg_custom_fields) != 1)
								{
								?>
									<span class="rtwwwap-form-custom-field-span1">
										<button type="button" class="rtwwwap-form-delete-custom-field"><?php esc_html_e( 'Remove', 'rtwwwap-wp-wc-affiliate-program' ); ?></button>
									</span>
								<?php
								}
								?>

								
								<?php if(isset($custom_fields['custom-input-options'])){ ?>
									<span class="rtwwwap-custom-input-options-span">
					        			<label for="rtwwwap-custom-label-class"><?php esc_html_e( 'Options', 'rtwwwap-wp-wc-affiliate-program' ); ?></label>
					        			<input type="text" name="rtwwwap_reg_temp_opt[custom-input][<?php echo esc_attr($count,'rtwwwap-wp-wc-affiliate-program'); ?>][custom-input-options]" class="rtwwwap-custom-options" value="<?php echo esc_attr($custom_fields['custom-input-options'], 'rtwwwap-wp-wc-affiliate-program'); ?>">
					        		</span>
								<?php } ?>
							</div>
						<?php }
					}else{ ?>
						<div class="rtwwwap-input_type-inner-wrapper rtwwwap-input_type-inner">
							<input type="hidden" class="rtwwwap_current_clone_count" value="0">
							<span class="rtwwwap-custom-label-span">
								<label for="rtwwwap-custom-label-class"><?php esc_html_e( 'Label', 'rtwwwap-wp-wc-affiliate-program' ); ?></label>
								<input type="text" name="rtwwwap_reg_temp_opt[custom-input][0][custom-input-label]" class="rtwwwap-custom-label">
							</span>
							<span class="rtwwwap-custom-span">
								<label for="rtwwwap-custom-input_type"><?php esc_html_e( 'Input Type', 'rtwwwap-wp-wc-affiliate-program' ); ?></label>
								<select data-current_count="0" name="rtwwwap_reg_temp_opt[custom-input][0][custom-input-type]" class="rtwwwap-custom-input_type">
									<option><?php esc_html_e( 'Select Input Type', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>
									<option value="text"><?php esc_html_e( 'Text', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>
									<option value="number"><?php esc_html_e( 'Number', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>
									<option value="checkbox"><?php esc_html_e( 'CheckBox', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>
									<option value="radio"><?php esc_html_e( 'Radio', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>
									<option value="textarea"><?php esc_html_e( 'TextArea', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>
									<option value="select"><?php esc_html_e( 'Select', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>
								</select>
							</span>

							<span class="rtwwwap-custom-type-span">
								<label for="rtwwwap-custom-type-class"><?php esc_html_e( 'Class', 'rtwwwap-wp-wc-affiliate-program' ); ?></label>
								<input type="text" name="rtwwwap_reg_temp_opt[custom-input][0][custom-input-class]" class="rtwwwap-custom-type-class">
							</span>

							<span class="rtwwwap-custom-type-span">
								<label for="rtwwwap-custom-type-id"><?php esc_html_e( 'Id', 'rtwwwap-wp-wc-affiliate-program' ); ?></label>
								<input type="text" name="rtwwwap_reg_temp_opt[custom-input][0][custom-input-id]" class="rtwwwap-custom-type-id">
							</span>

							<span class="rtwwwap-custom-type-span">
								<label for="rtwwwap-custom-type-placeholder"><?php esc_html_e( 'Placeholder', 'rtwwwap-wp-wc-affiliate-program' ); ?></label>
								<input type="text" class="rtwwwap-custom-type-placeholder" name="rtwwwap_reg_temp_opt[custom-input][0][custom-input-placeholder]">
							</span>

							<span class="rtwwwap-input_type-span rtwwwap-after-clone">
								<label for="rtwwwap-input_type-required"><?php esc_html_e( 'Required', 'rtwwwap-wp-wc-affiliate-program' ); ?></label>
								<select name="rtwwwap_reg_temp_opt[custom-input][0][custom-input-isrequired]" class="rtwwwap-input_type-required">
									<option value="no"><?php esc_html_e( 'No', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>
									<option value="yes"><?php esc_html_e( 'Yes', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>
								</select>
							</span>
						</div>
						
					<?php } ?>

				</div>
				
				<?php $rtwwwap_reg_custom_fields = isset($rtwwwap_reg_temp_features['custom-input']) ? $rtwwwap_reg_temp_features['custom-input'] : array(); 
				?>
				<span class="rtwwwap-form-custom-field-span">
					<input type="button" data-clone_id="0" class="rtwwwap-form-custom-field-clone" value="<?php esc_html_e( 'Add More', 'rtwwwap-wp-wc-affiliate-program' ); ?>">
					<input type="hidden" class="rtwwwap_clone_counter" value="<?php echo esc_attr($final_count, 'rtwwwap-wp-wc-affiliate-program'); ?>">
					
				</span>

				
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
			<td></td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e( 'Main Background Color', 'rtwwwap-wp-wc-affiliate-program' ); ?>
			</th>
			<td class="tr2">
				<input type="text" id="rtwwwap_mainbgPicker" data-type="mainbg_color" class="rtwwwap_text_color_field" name="rtwwwap_reg_temp_opt[mainbg_color]" value="<?php echo isset( $rtwwwap_reg_temp_features[ 'mainbg_color' ] ) ? $rtwwwap_reg_temp_features[ 'mainbg_color' ] : ''; ?>" />
				<p class="rtwwwap_mainbg_color"><?php echo isset( $rtwwwap_reg_temp_features[ 'mainbg_color' ] ) ? $rtwwwap_reg_temp_features[ 'mainbg_color' ] : ''; ?></p>
			</td>
			<td></td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e( 'Form Background Color', 'rtwwwap-wp-wc-affiliate-program' ); ?>
			</th>
			<td class="tr2">
				<input type="text" id="rtwwwap_bgPicker" data-type="bg_color" class="rtwwwap_text_color_field" name="rtwwwap_reg_temp_opt[bg_color]" value="<?php echo isset( $rtwwwap_reg_temp_features[ 'bg_color' ] ) ? $rtwwwap_reg_temp_features[ 'bg_color' ] : ''; ?>" />
				<p class="rtwwwap_bg_color"><?php echo isset( $rtwwwap_reg_temp_features[ 'bg_color' ] ) ? $rtwwwap_reg_temp_features[ 'bg_color' ] : ''; ?></p>
			</td>
			<td></td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e( 'Form Header Color', 'rtwwwap-wp-wc-affiliate-program' ); ?>
			</th>
			<td class="tr2">
				<input type="text" id="rtwwwap_headPicker" data-type="head_color" class="rtwwwap_text_color_field" name="rtwwwap_reg_temp_opt[head_color]" value="<?php echo isset( $rtwwwap_reg_temp_features[ 'head_color' ] ) ? $rtwwwap_reg_temp_features[ 'head_color' ] : ''; ?>" />
				<p class="rtwwwap_head_color"><?php echo isset( $rtwwwap_reg_temp_features[ 'head_color' ] ) ? $rtwwwap_reg_temp_features[ 'head_color' ] : ''; ?></p>
			</td>
			<td></td>
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
			<td></td>
		</tr>
	</tbody>
</table>