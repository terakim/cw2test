<?php
	
?>
<div class ="rtwwdpdl_pro_text_overlay">
<div class="rtwalwm_addons">
	<div class="rtwalwm_register_templates">
		<a href="#" >
			<div class="rtwalwm_reg_title">
				<?php esc_html_e( 'Register/Login Templates', 'rtwalwm-wp-wc-affiliate-program' ); ?>
			</div>
			<div class="rtwalwm_reg_shortcodes">
				<span>
					<label><?php esc_html_e( 'Login Shortcode', 'rtwalwm-wp-wc-affiliate-program' ); ?></label>
				</span>
				<span>
					<label><?php esc_html_e( 'Register Shrotcode', 'rtwalwm-wp-wc-affiliate-program' ); ?></label>
				
				</span>
			</div>
		</a>
	</div>
	<div class="rtwalwm_sms">
		<?php
			function rtwalwm_sms_status()
			{
				$rtwalwm_sms_status = true;
				if( function_exists('is_multisite') && is_multisite() )
				{
					include_once(ABSPATH. 'wp-admin/includes/plugin.php');
					if( !is_plugin_active('rtwsmsap-social-media-share-affiliate-program/rtwsmsap-social-media-share-affiliate-program.php') )
					{
						$rtwalwm_sms_status = false;
					}
				}
				else
				{
					if( !in_array('rtwsmsap-social-media-share-affiliate-program/rtwsmsap-social-media-share-affiliate-program.php', apply_filters('active_plugins', get_option('active_plugins') ) ) )
					{
						$rtwalwm_sms_status = false;
					}
				}
				return $rtwalwm_sms_status;
			}

			$rtwalwm_sms_status_check = rtwalwm_sms_status();

			if( $rtwalwm_sms_status_check ){
		?>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=rtwalwm&rtwalwm_tab=rtwalwm_sms' ) );?>">
				<?php esc_html_e( 'Social Media Share', 'rtwalwm-wp-wc-affiliate-program' ); ?>
			</a>
		<?php 
			}else{
		?>
			<a class="rtwalwm-unlock-sms" href="<?php echo esc_url( 'https://redefiningtheweb.com/product/social-media-share-addon-for-affiliate-program/1548' );?>" target="_blank">
				<?php esc_html_e( 'Social Media Share', 'rtwalwm-wp-wc-affiliate-program' ); ?>
			</a>
		<?php 
			}
		?>
	</div>
</div>
</div>
