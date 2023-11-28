<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://www.redefiningtheweb.com
 * @since      1.0.0
 *
 * @package    Rtwwwap_Wp_Wc_Affiliate_Program
 * @subpackage Rtwwwap_Wp_Wc_Affiliate_Program/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php
	$rtwwwap_dashboard_active 	= '';
	$rtwwwap_affiliate_active 	= '';
	$rtwwwap_traffic_active = '';
	$rtwwwap_Email_active = '';
	$rtwwwap_commission_active 	= '';
	$rtwwwap_referrals_active 	= '';
	$rtwwwap_payouts_active 	= '';
	$rtwwwap_extra_active 		= '';
	$rtwwwap_levels_active 		= '';
	$rtwwwap_mlm_active 		= '';
	$rtwwwap_custom_banner_active		= '';
	$rtwwwap_addons_active 		= '';
	$rtwwwap_help_active 		= '';


	if( isset( $_GET[ 'rtwwwap_tab' ] ) )
	{
		if( $_GET[ 'rtwwwap_tab' ] == "rtwwwap_dashboard" )
		{
			$rtwwwap_dashboard_active = "nav-tab-active";
		}
		elseif( $_GET[ 'rtwwwap_tab' ] == "rtwwwap_traffic" )
		{
			$rtwwwap_traffic_active = "nav-tab-active";
			update_option( 'rtwwwap_traffic_noti', 0 );
		}
		elseif( $_GET[ 'rtwwwap_tab' ] == "rtwwwap_customize_email" )
		{
			$rtwwwap_Email_active = "nav-tab-active";
		}
		elseif( $_GET[ 'rtwwwap_tab' ] == "rtwwwap_affiliates" )
		{
			$rtwwwap_affiliate_active = "nav-tab-active";
		}
		elseif( $_GET[ 'rtwwwap_tab' ] == "rtwwwap_commission" )
		{
			$rtwwwap_commission_active = "nav-tab-active";
		}
		elseif( $_GET[ 'rtwwwap_tab' ] == "rtwwwap_referrals" )
		{
			$rtwwwap_referrals_active = "nav-tab-active";
			update_option( 'rtwwwap_referral_noti', 0 );
		}
		elseif( $_GET[ 'rtwwwap_tab' ] == "rtwwwap_payouts" )
		{
			$rtwwwap_payouts_active = "nav-tab-active";
		}
		elseif( $_GET[ 'rtwwwap_tab' ] == "rtwwwap_extra" )
		{
			$rtwwwap_extra_active = "nav-tab-active";
		}
		elseif( $_GET[ 'rtwwwap_tab' ] == "rtwwwap_levels" || $_GET[ 'rtwwwap_tab' ] == "rtwwwap_levels_add_edit" )
		{
			$rtwwwap_levels_active = "nav-tab-active";
		}
		elseif( $_GET[ 'rtwwwap_tab' ] == "rtwwwap_mlm" )
		{
			$rtwwwap_mlm_active = "nav-tab-active";
		}
		elseif( $_GET[ 'rtwwwap_tab' ] == "rtwwwap_addons" || $_GET[ 'rtwwwap_tab' ] == "rtwwwap_sms" || $_GET[ 'rtwwwap_tab' ] == "rtwwwap_register_template" || $_GET[ 'rtwwwap_tab' ] == "rtwwwap_login_template" )
		{
			$rtwwwap_addons_active = "nav-tab-active";
		}
		elseif( $_GET[ 'rtwwwap_tab' ] == "rtwwwap_custom_banner" )
		{
			$rtwwwap_custom_banner_active = "nav-tab-active";
		}
		elseif( $_GET[ 'rtwwwap_tab' ] == "rtwwwap_help" )
		{
			$rtwwwap_help_active = "nav-tab-active";
		}

	}
	else
	{
		$rtwwwap_dashboard_active = "nav-tab-active";
	}
	settings_errors();

	$rtwwwap_referral_noti = get_option( 'rtwwwap_referral_noti' );
	$rtwwwap_traffic_noti = get_option( 'rtwwwap_traffic_noti' );

?>

<div class="wrap rtwwwap">
	<div class="rtwwwap_loader_wrapper">
		<div class="rtwwwap_loader_image"><img src="<?php echo esc_url( RTWWWAP_URL.'assets/images/wpspin-2x.gif' ); ?>"></div>
	</div>
	<h2 class="rtwwwap-main-heading"><span><?php esc_html_e( 'WordPress & WooCommerce Affiliate Program', 'rtwwwap-wp-wc-affiliate-program' ); ?></span><a href="<?php echo esc_url( admin_url( 'admin.php?page=rtwwwap&rtwwwap_tab=rtwwwap_dashboard&rtwwwap_action=delete_purchase_code' ) );?>" class="rtwwwap-button"><?php esc_html_e( 'Remove Purchase Code', 'rtwwwap-wp-wc-affiliate-program' ); ?></a></h2>
	<nav class="rtwwwap-navigation-wrapper nav-tab-wrapper">
		<a class="nav-tab <?php echo esc_attr( $rtwwwap_dashboard_active ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=rtwwwap&rtwwwap_tab=rtwwwap_dashboard' ) );?>">
			<div class="rtwwwap_tab_icon"><img src="<?php echo esc_url( RTWWWAP_URL.'assets/images/dashboard.png' ); ?>" alt=""></div>
			<?php esc_html_e( 'Dashboard Overview', 'rtwwwap-wp-wc-affiliate-program' ); ?>
		</a>
		<a class="nav-tab <?php echo esc_attr( $rtwwwap_traffic_active );?>" href="<?php echo esc_url( admin_url( 'admin.php?page=rtwwwap&rtwwwap_tab=rtwwwap_traffic' ) );?>">
			<div class="rtwwwap_tab_icon"><img src="<?php echo esc_url( RTWWWAP_URL.'assets/images/multi_user_traffic.png' ); ?>" alt=""></div>
			<?php esc_html_e( 'Traffic', 'rtwwwap-wp-wc-affiliate-program' );
			if( $rtwwwap_traffic_noti ){
				?>
						<span class="rtwwwap_notify_number"><?php echo esc_html( $rtwwwap_traffic_noti ); ?></span>
				<?php
					}
					?>
		</a>

		<!-- Customize email tab starts -->
		<a class="nav-tab <?php echo esc_attr( $rtwwwap_Email_active ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=rtwwwap&rtwwwap_tab=rtwwwap_customize_email' ) );?>">
			<div class="rtwwwap_tab_icon"><img src="<?php echo esc_url( RTWWWAP_URL.'assets/images/email52.png' ); ?>" alt=""></div>
			<?php esc_html_e( 'Customize Email', 'rtwwwap-wp-wc-affiliate-program' ); ?>
		</a>

		<!-- end -->

		<a class="nav-tab <?php echo esc_attr( $rtwwwap_affiliate_active );?>" href="<?php echo esc_url( admin_url( 'admin.php?page=rtwwwap&rtwwwap_tab=rtwwwap_affiliates' ) );?>">
			<div class="rtwwwap_tab_icon"><img src="<?php echo esc_url( RTWWWAP_URL.'assets/images/affiliate_menu.png' ); ?>" alt=""></div>
			<?php esc_html_e( 'Affiliates', 'rtwwwap-wp-wc-affiliate-program' ); ?>
		</a>
		<a class="nav-tab <?php echo esc_attr( $rtwwwap_commission_active );?>" href="<?php echo esc_url( admin_url( 'admin.php?page=rtwwwap&rtwwwap_tab=rtwwwap_commission' ) );?>">
			<div class="rtwwwap_tab_icon"><img src="<?php echo esc_url( RTWWWAP_URL.'assets/images/commission_setting.png' ); ?>" alt=""></div>
			<?php esc_html_e( 'Commission Setting', 'rtwwwap-wp-wc-affiliate-program' ); ?>
		</a>
		<a class="nav-tab <?php echo esc_attr( $rtwwwap_levels_active );?>" href="<?php echo esc_url( admin_url( 'admin.php?page=rtwwwap&rtwwwap_tab=rtwwwap_levels' ) );?>">
			<div class="rtwwwap_tab_icon"><img src="<?php echo esc_url( RTWWWAP_URL.'assets/images/levels.png' ); ?>" alt=""></div>
			<?php esc_html_e( 'Levels', 'rtwwwap-wp-wc-affiliate-program' ); ?>
		</a>
		<a class="nav-tab <?php echo esc_attr( $rtwwwap_referrals_active );?>" href="<?php echo esc_url( admin_url( 'admin.php?page=rtwwwap&rtwwwap_tab=rtwwwap_referrals' ) );?>">
			<div class="rtwwwap_tab_icon"><img src="<?php echo esc_url( RTWWWAP_URL.'assets/images/referral_menu_icon.png' ); ?>" alt=""></div>
			<div class="rtwwwap_notify_wrap">
				<?php
					esc_html_e( 'Referrals', 'rtwwwap-wp-wc-affiliate-program' );
					if( $rtwwwap_referral_noti ){
				?>
						<span class="rtwwwap_notify_number"><?php echo esc_html( $rtwwwap_referral_noti ); ?></span>
				<?php
					}
				?>
			</div>
		</a>
		<a class="nav-tab <?php echo esc_attr( $rtwwwap_payouts_active );?>" href="<?php echo esc_url( admin_url( 'admin.php?page=rtwwwap&rtwwwap_tab=rtwwwap_payouts' ) );?>">
			<div class="rtwwwap_tab_icon"><img src="<?php echo esc_url( RTWWWAP_URL.'assets/images/payout.png' ); ?>" alt=""></div>
			<?php esc_html_e( 'Payouts', 'rtwwwap-wp-wc-affiliate-program' ); ?>
		</a>
		<a class="nav-tab <?php echo esc_attr( $rtwwwap_extra_active );?>" href="<?php echo esc_url( admin_url( 'admin.php?page=rtwwwap&rtwwwap_tab=rtwwwap_extra' ) );?>">
			<div class="rtwwwap_tab_icon"><img src="<?php echo esc_url( RTWWWAP_URL.'assets/images/extra_feature.png' ); ?>" alt=""></div>
			<?php esc_html_e( 'Extra Features', 'rtwwwap-wp-wc-affiliate-program' ); ?>
		</a>
		<a class="nav-tab <?php echo esc_attr( $rtwwwap_mlm_active );?>" href="<?php echo esc_url( admin_url( 'admin.php?page=rtwwwap&rtwwwap_tab=rtwwwap_mlm' ) );?>">
			<div class="rtwwwap_tab_icon"><img src="<?php echo esc_url( RTWWWAP_URL.'assets/images/mlm.png' ); ?>" alt=""></div>
			<?php esc_html_e( 'MLM', 'rtwwwap-wp-wc-affiliate-program' ); ?>
		</a>
		<a class="nav-tab <?php echo esc_attr( $rtwwwap_custom_banner_active ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=rtwwwap&rtwwwap_tab=rtwwwap_custom_banner' ) );?>">
			<div class="rtwwwap_tab_icon"><img src="<?php echo esc_url( RTWWWAP_URL.'assets/images/custom_banner.png' ); ?>" alt=""></div>
			<?php esc_html_e( 'Custom Banner', 'rtwwwap-wp-wc-affiliate-program' ); ?>
		</a>
		<a class="nav-tab <?php echo esc_attr( $rtwwwap_addons_active );?>" href="<?php echo esc_url( admin_url( 'admin.php?page=rtwwwap&rtwwwap_tab=rtwwwap_addons' ) );?>">
			<div class="rtwwwap_tab_icon"><img src="<?php echo esc_url( RTWWWAP_URL.'assets/images/add_ons.png' ); ?>" alt=""></div>
			<?php esc_html_e( 'Add-ons', 'rtwwwap-wp-wc-affiliate-program' ); ?>
		</a>
		<a class="nav-tab <?php echo esc_attr( $rtwwwap_help_active );?>" href="<?php echo esc_url( admin_url( 'admin.php?page=rtwwwap&rtwwwap_tab=rtwwwap_help' ) );?>">
			<div class="rtwwwap_tab_icon"><img src="<?php echo esc_url( RTWWWAP_URL.'assets/images/help.png' ); ?>" alt=""></div>
			<?php esc_html_e( 'Help', 'rtwwwap-wp-wc-affiliate-program' ); ?>
		</a>
	</nav>
	<?php
		if( isset( $_GET[ 'rtwwwap_tab' ] ) )
		{
			if( $_GET[ 'rtwwwap_tab' ] == "rtwwwap_dashboard" ){
				include_once( RTWWWAP_DIR . '/admin/partials/rtwwwap_tabs/rtwwwap_dashboard.php' );
			}
			elseif( $_GET[ 'rtwwwap_tab' ] == "rtwwwap_traffic" ){
				include_once( RTWWWAP_DIR . '/admin/partials/rtwwwap_tabs/rtwwwap_affiliates_traffic.php' );
			}

			elseif( $_GET[ 'rtwwwap_tab' ] == "rtwwwap_customize_email" ){
				include_once( RTWWWAP_DIR . '/admin/partials/rtwwwap_tabs/rtwwwap_customize_email.php' );
			}

			elseif( $_GET[ 'rtwwwap_tab' ] == "rtwwwap_referrals" ){
				include_once( RTWWWAP_DIR . '/admin/partials/rtwwwap_tabs/rtwwwap_referrals.php' );
			}
			elseif( $_GET[ 'rtwwwap_tab' ] == "rtwwwap_payouts" ){
				include_once( RTWWWAP_DIR . '/admin/partials/rtwwwap_tabs/rtwwwap_payouts.php' );
			}
			elseif( $_GET[ 'rtwwwap_tab' ] == "rtwwwap_affiliates" )
			{
				include_once( RTWWWAP_DIR . '/admin/partials/rtwwwap_tabs/rtwwwap_affiliates.php' );
			}
			elseif( $_GET[ 'rtwwwap_tab' ] == "rtwwwap_levels" )
			{
				include_once( RTWWWAP_DIR . '/admin/partials/rtwwwap_tabs/rtwwwap_levels.php' );
			}
			elseif( $_GET[ 'rtwwwap_tab' ] == "rtwwwap_addons" )
			{
				include_once( RTWWWAP_DIR . '/admin/partials/rtwwwap_tabs/rtwwwap_addons.php' );
			}
			elseif( $_GET[ 'rtwwwap_tab' ] == "rtwwwap_sms" )
			{
				do_action( 'rtwwwap_license_tab_include' );
			}
			elseif( $_GET[ 'rtwwwap_tab' ] == "rtwwwap_custom_banner" )
			{
				include_once( RTWWWAP_DIR . '/admin/partials/rtwwwap_tabs/rtwwwap_custom_banner.php' );
			}
			elseif( $_GET[ 'rtwwwap_tab' ] == "rtwwwap_help" )
			{
				include_once( RTWWWAP_DIR . '/admin/partials/rtwwwap_tabs/rtwwwap_help.php' );
			}
			elseif( $_GET[ 'rtwwwap_tab' ] == "rtwwwap_commission" || $_GET[ 'rtwwwap_tab' ] == "rtwwwap_extra" || $_GET[ 'rtwwwap_tab' ] == "rtwwwap_levels_add_edit" || $_GET[ 'rtwwwap_tab' ] == "rtwwwap_mlm" || $_GET[ 'rtwwwap_tab' ] == "rtwwwap_register_template"  || $_GET[ 'rtwwwap_tab' ] == "rtwwwap_login_template")
			{
	?>
			<div class="main-wrapper">
				<form enctype="multipart/form-data" action="options.php" method="post">
	<?php
				if( $_GET[ 'rtwwwap_tab' ] == "rtwwwap_commission" ){
					if( RTWWWAP_IS_WOO == 1 ){
						include_once( RTWWWAP_DIR . '/admin/partials/rtwwwap_tabs/rtwwwap_commission.php' );
					}
					elseif(RTWWWAP_IS_Easy == 1)
					{
						include_once( RTWWWAP_DIR . '/admin/partials/rtwwwap_tabs/rtwwwap_commission.php' );
					}
					else{
						include_once( RTWWWAP_DIR . '/admin/partials/rtwwwap_tabs/rtwwwap_commission_no_woo.php' );
					}
				}
				elseif( $_GET[ 'rtwwwap_tab' ] == "rtwwwap_extra" ){
					include_once( RTWWWAP_DIR . '/admin/partials/rtwwwap_tabs/rtwwwap_extra_features.php' );
				}
				
				elseif( $_GET[ 'rtwwwap_tab' ] == "rtwwwap_levels_add_edit" ){
					include_once( RTWWWAP_DIR . '/admin/partials/rtwwwap_tabs/rtwwwap_levels_add_edit.php' );
				}
				elseif( $_GET[ 'rtwwwap_tab' ] == "rtwwwap_mlm" ){
					include_once( RTWWWAP_DIR . '/admin/partials/rtwwwap_tabs/rtwwwap_mlm.php' );
				}
				elseif( $_GET[ 'rtwwwap_tab' ] == "rtwwwap_register_template" ){
					include_once( RTWWWAP_DIR . '/admin/partials/rtwwwap_addons/rtwwwap_register_template.php' );
				}
				elseif( $_GET[ 'rtwwwap_tab' ] == "rtwwwap_login_template" ){
					include_once( RTWWWAP_DIR . '/admin/partials/rtwwwap_addons/rtwwwap_login_template.php' );
				}
	?>
				<p class="submit">
					<input type="submit" value="<?php esc_attr_e( 'Save changes', 'rtwwwap-wp-wc-affiliate-program' ); ?>" class="rtwwwap-button" name="submit" />
				</p>
				</form>
				<?php include_once( RTWWWAP_DIR . '/admin/partials/rtwwwap_tabs/rtwwwap_footer.php' ); ?>
			</div>
	<?php
			}
		}
	
		else{
			include_once( RTWWWAP_DIR . '/admin/partials/rtwwwap_tabs/rtwwwap_dashboard.php' );
		}
	?>
</div>
