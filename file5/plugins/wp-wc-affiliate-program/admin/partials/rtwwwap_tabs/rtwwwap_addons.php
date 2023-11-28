<?php
	
?>

<div class="rtwwwap_addons">
	<div class="rtwwwap_register_templates">
		
			<div class="rtwwwap_reg_title">
				<?php esc_html_e( 'Register/Login Templates', 'rtwwwap-wp-wc-affiliate-program' ); ?>
			
			<div class="rtwwwap_reg_shortcodes">
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=rtwwwap&rtwwwap_tab=rtwwwap_login_template' ) );?>">
				<span>
					<label><?php esc_html_e( 'Login Shortcode', 'rtwwwap-wp-wc-affiliate-program' ); ?></label><?php echo esc_html( '[rtwwwap_aff_login_page]'); ?>
				</span>
			</a>
		     	<a href="<?php echo esc_url( admin_url( 'admin.php?page=rtwwwap&rtwwwap_tab=rtwwwap_register_template' ) );?>">
			
				<span>
					<label><?php esc_html_e( 'Register Shrotcode', 'rtwwwap-wp-wc-affiliate-program' ); ?></label>
					<?php echo esc_html( '[rtwwwap_aff_reg_page]'); ?>
				</span>
			</a>
			</div>
			</div>
	</div>
	<div class="rtwwwap_sms">
		<?php
			function rtwwwap_sms_status()
			{
				$rtwwwap_sms_status = true;
				if( function_exists('is_multisite') && is_multisite() )
				{
					include_once(ABSPATH. 'wp-admin/includes/plugin.php');
					if( !is_plugin_active('rtwsmsap-social-media-share-affiliate-program/rtwsmsap-social-media-share-affiliate-program.php') )
					{
						$rtwwwap_sms_status = false;
					}
				}
				else
				{
					if( !in_array('rtwsmsap-social-media-share-affiliate-program/rtwsmsap-social-media-share-affiliate-program.php', apply_filters('active_plugins', get_option('active_plugins') ) ) )
					{
						$rtwwwap_sms_status = false;
					}
				}
				return $rtwwwap_sms_status;
			}

			$rtwwwap_sms_status_check = rtwwwap_sms_status();

			if( $rtwwwap_sms_status_check ){
		?>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=rtwwwap&rtwwwap_tab=rtwwwap_sms' ) );?>">
				<?php esc_html_e( 'Social Media Share', 'rtwwwap-wp-wc-affiliate-program' ); ?>
			</a>
		<?php 
			}else{
		?>
			<a class="rtwwwap-unlock-sms" href="<?php echo esc_url( 'https://redefiningtheweb.com/product/social-media-share-addon-for-affiliate-program/1548' );?>" target="_blank">
				<?php esc_html_e( 'Social Media Share', 'rtwwwap-wp-wc-affiliate-program' ); ?>
			</a>
		<?php 
			}
		?>
	</div>
</div>

<?php
$rtwwwap_html1 = "";
$rtwwwap_html = apply_filters('rtwwwap_upload_file',$rtwwwap_html1);
echo  $rtwwwap_html;