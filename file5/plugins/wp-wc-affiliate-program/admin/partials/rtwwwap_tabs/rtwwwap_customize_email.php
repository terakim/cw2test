<?php 

$rtwwwap_section_show = 'signup-email';
settings_fields( 'rtwwwap_email_features');

$rtwwwap_email_features = get_option( 'rtwwwap_email_features_opt' );

$test = get_option('customize_email',true);

$signup_email = get_option('signup_email','null');
$become_an_affiliate = get_option('become_an_affiliate','null');
$withdrawal_request = get_option('withdrawal_request','null');
$generate_commission = get_option('generate_commission','null');
$generate_mlm_commission = get_option('generate_mlm_commission','null');

?>


<div class="main-wrapper">
	<div class="rtwwwap-data-table-wrapper">
		<table class="rtwwwap_affiliates_table rtwwwap_data_table stripe" class="display dtr-inline" cellspacing="0">
		  	<thead>
			  	<tr>
			    	<th><?php esc_html_e( 'Email type', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Subject', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
                    <th><?php esc_html_e( 'Activate/Deactivate', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
                    <th><?php esc_html_e( 'Action', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			  	</tr>
		  	</thead>
		  	<tbody>
                <?php
                    if( isset($test) && !empty($test)){
                    foreach( $test as $key=> $val ){
						$rtwwwap_email_content_without_html = $val['content'];
						// echo '<pre>';
						// var_dump($signup_email);
						// echo '</pre>';
                    ?>
					<tr>	 
                        <td class= "email_type"><?php echo esc_html_e( $key ); ?></td>
                        <td class='subject'><?php echo esc_html_e( $val['subject'], 'rtwwwap-wp-wc-affiliate-program'  ); ?></td>

						<?php
						if( $key == "Signup Email" && $signup_email == "true"){
						?>
                        	<td><label class="rtwwwap_switch"><input type="checkbox" class="rtwwwap_email_check" checked><span class="rtwwwap_slider round"></span></label></td>
						<?php
						}
						else if( $key == "Become an affiliate Email" && $become_an_affiliate == "true" ){
						?>
							<td><label class="rtwwwap_switch"><input type="checkbox" class="rtwwwap_email_check" checked><span class="rtwwwap_slider round"></span></label></td>
						<?php	
						}
						else if( $key == "Email on Withdral Request" && $withdrawal_request == "true" ){
							?>
							<td><label class="rtwwwap_switch"><input type="checkbox" class="rtwwwap_email_check" checked><span class="rtwwwap_slider round"></span></label></td>
						<?php
						}
						else if( $key == "Email on Generating Commission" && $generate_commission == "true" ){
						?>
							<td><label class="rtwwwap_switch"><input type="checkbox" class="rtwwwap_email_check" checked><span class="rtwwwap_slider round"></span></label></td>
						<?php
						}
						else if( $key == "Email on Generating MLM Commission" && $generate_mlm_commission == "true" ){
						?>
							<td><label class="rtwwwap_switch"><input type="checkbox" class="rtwwwap_email_check" checked><span class="rtwwwap_slider round"></span></label></td>

						<?php
						}
						else{
							?>
							<td><label class="rtwwwap_switch"><input type="checkbox" class="rtwwwap_email_check"><span class="rtwwwap_slider round"></span></label></td>
						<?php
						}
						?>
                        <td><input type='button' value='Edit Email' class='rtwwwap_customize_email' data-email_type="<?php esc_attr_e($key); ?>" /></td>	
                    </tr>
                    <?php
                        }
                    }
                ?>	
			</tbody>

			<div class="rtwwwap_email_content_modal">
			<div class="rtwwwap_email_model_dialog">
				<div class="rtwwwap_email_model_content">
					<div class="rtwwwap_email_model_header">
						<div class="rtwwwap_close_model_icon">
							<i class="fas fa-times"></i>
						</div>
					</div>				
					<div class="rtwwwap_email_wrapper"></div>		
				    </div>	
			    </div>
		    </div>


            <div class="rtwwwap_rank_requirement_model">
				<div class="rtwwwap_rank_model_dialog">
				<div class="rtwwwap_rank_model_content">
					
					<div class="rtwwwap_rank_model_header">
						<h3><?php esc_html_e( 'Customize your required Email from here', 'rtwwwap-wp-wc-affiliate-program' ); ?></h3>
						<div class="rtwwwap_close_model_icon">
							<i class="fas fa-times"></i>
						</div>
					</div>					

					<div class="rtwwwap_rank_model_body">
						<div class="rtwwwap_requirement_wrapper">	
							<div class="rtwwwap_email_content">	
                                <label class="rtwwwap_rank_label"><?php esc_html_e( 'Subject', 'rtwwwap-wp-wc-affiliate-program' ); ?></label>
								<input type="text" name="rtwwwap_priority_field" id ="rtwwwap_customize_subject" value="" class ="rtwwwap_priority_field rtwwwap_rank_field">
							</div>
							<div class="rtwwwap_rank_description">
								<label class="rtwwwap_rank_label"><?php esc_html_e( 'Email content', 'rtwwwap-wp-wc-affiliate-program' ); ?></label>
								
								<?php
								$rtwwwap_default_benefits = sprintf( "<ul><li>%s</li><li>%s</li><li>%s</li></ul>", esc_html__( 'Earn extra money just by marketing our products with our affiliate tools', 'rtwwwap-wp-wc-affiliate-program' ), esc_html__( 'You can customize the email according to your requirement', 'rtwwwap-wp-wc-affiliate-program' ), esc_html__( 'Signup Bonus when someone signup dxvgvsx xvxffb from your shared link', 'rtwwwap-wp-wc-affiliate-program' ) );

								// for frontend wp_editor content
								// $rtwwwap_extra_features_wp_editor = $rtwwwap_default_benefits;

								// $rtwwwap_extra_features_wp_editor = html_entity_decode( $rtwwwap_extra_features_wp_editor );
								// $rtwwwap_extra_features_wp_editor = stripslashes( $rtwwwap_extra_features_wp_editor );
								$rtwwwap_extra_features_editor_id 	= 'rtwwwap_customize_content';
								$rtwwwap_extra_features_settings 	=  array(
														'media_buttons' => true,
														'textarea_name' => 'rtwwwap_customize_content',
														'textarea_rows' => 7,
														'editor_class' => "wp-editor-check"
												);
								wp_editor( "",$rtwwwap_extra_features_editor_id, $rtwwwap_extra_features_settings );
							?>
							</div>
						</div>	
						</div>	
						<div class="rtwwwap_rank_footer">
							<input type="button" value="<?php esc_html_e( 'Save', 'rtwwwap-wp-wc-affiliate-program' ); ?>" class="rtwwwap-button" id="rtwwwap_save_customize_email">
						</div>	
				    </div>	
			    </div>
		    </div>

			<tfoot>
			  	<tr>
			    	<th><?php esc_html_e( 'Email type', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Subject', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
					<th><?php esc_html_e( 'Activate/Deactivate', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
                    <th><?php esc_html_e( 'Action', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			  	</tr>
		  	</tfoot>
		</table>
    </div>
    <?php include_once( RTWWWAP_DIR . '/admin/partials/rtwwwap_tabs/rtwwwap_footer.php' ); ?>
</div>

