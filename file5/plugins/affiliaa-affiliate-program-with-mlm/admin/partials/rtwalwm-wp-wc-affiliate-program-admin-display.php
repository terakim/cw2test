<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://www.redefiningtheweb.com
 * @since      1.0.0
 *
 * @package    Rtwalwm_Wp_Wc_Affiliate_Program
 * @subpackage Rtwalwm_Wp_Wc_Affiliate_Program/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php
	$rtwalwm_dashboard_active 	= '';
	$rtwalwm_affiliate_active 	= '';
	$rtwalwm_commission_active 	= '';
	$rtwalwm_referrals_active 	= '';
	$rtwalwm_Email_active		= '';
	$rtwalwm_payouts_active 	= '';
	$rtwalwm_extra_active 		= '';
	$rtwalwm_levels_active 		= '';
	$rtwalwm_mlm_active 		= '';
	$rtwalwm_custom_banner_active		= '';
	$rtwalwm_help_active		= '';


	$rtwalwm_addons_active 		= '';

	
	if( isset( $_GET[ 'rtwalwm_tab' ] ) )
	{
		if( $_GET[ 'rtwalwm_tab' ] == "rtwalwm_dashboard" )
		{
			$rtwalwm_dashboard_active = "nav-tab-active";
		}
		elseif( $_GET[ 'rtwalwm_tab' ] == "rtwalwm_affiliates" )
		{
			$rtwalwm_affiliate_active = "nav-tab-active";
		}
		elseif( $_GET[ 'rtwalwm_tab' ] == "rtwalwm_customize_email" )
		{
			$rtwalwm_Email_active = "nav-tab-active";
		}
		elseif( $_GET[ 'rtwalwm_tab' ] == "rtwalwm_commission" )
		{
			$rtwalwm_commission_active = "nav-tab-active";
		}
		elseif( $_GET[ 'rtwalwm_tab' ] == "rtwalwm_referrals" )
		{
			$rtwalwm_referrals_active = "nav-tab-active";
			update_option( 'rtwwwap_referral_noti', 0 );
		}
		elseif( $_GET[ 'rtwalwm_tab' ] == "rtwalwm_payouts" )
		{
			$rtwalwm_payouts_active = "nav-tab-active";
		}
		elseif( $_GET[ 'rtwalwm_tab' ] == "rtwalwm_extra" )
		{
			$rtwalwm_extra_active = "nav-tab-active";
		}
		elseif( $_GET[ 'rtwalwm_tab' ] == "rtwalwm_levels" || $_GET[ 'rtwalwm_tab' ] == "rtwalwm_levels_add_edit" )
		{
			$rtwalwm_levels_active = "nav-tab-active";
		}
		elseif( $_GET[ 'rtwalwm_tab' ] == "rtwalwm_mlm" )
		{
			$rtwalwm_mlm_active = "nav-tab-active";
		}
		elseif( $_GET[ 'rtwalwm_tab' ] == "rtwalwm_addons" || $_GET[ 'rtwalwm_tab' ] == "rtwalwm_sms" || $_GET[ 'rtwalwm_tab' ] == "rtwalwm_register_template" )
		{
			$rtwalwm_addons_active = "nav-tab-active";
		}
		elseif( $_GET[ 'rtwalwm_tab' ] == "rtwalwm_custom_banner" )
		{
			$rtwalwm_custom_banner_active = "nav-tab-active";
		}
		elseif( $_GET[ 'rtwalwm_tab' ] == "rtwalwm_help" )
		{
			$rtwalwm_help_active = "nav-tab-active";
		}
		

	}
	else
	{
		$rtwalwm_dashboard_active = "nav-tab-active";
	}
	settings_errors();

	$rtwalwm_referral_noti = get_option( 'rtwwwap_referral_noti' );
?>

<!-- <div class="rtw_popup">
        <div class="rtw_card">
            <div class="rtw_card_label">
                <label>Limited time offer</label>
            </div>
            <div class="rtw_card_body">
                <div class="rtw_close_popup">
                  <div class="rtw_close_icon"></div>
                </div>
               
            
             
				<a class="rtw_link" href="https://codecanyon.net/item/wordpress-woocommerce-affiliate-program/23580333" target="_blank"> <button class="rtwalwm_buy_now">Buy Now</button></a>
            </div>
        </div>
    </div> -->


<div class="wrap rtwalwm">
	<div class="rtwalwm_loader_wrapper">
		<div class="rtwalwm_loader_image"><img src="<?php echo esc_url( RTWALWM_URL.'assets/images/wpspin-2x.gif' ); ?>"></div>
	</div>
	<h2 class="rtwalwm-main-heading"><span><?php esc_html_e( 'Affiliaa - Affiliate Program with MLM', 'rtwalwm-wp-wc-affiliate-program' ); ?></span></h2>
	<nav class="rtwalwm-navigation-wrapper nav-tab-wrapper">
		<a class="nav-tab <?php echo esc_attr( $rtwalwm_dashboard_active ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=rtwalwm&rtwalwm_tab=rtwalwm_dashboard' ) );?>">
			<div class="rtwalwm_tab_icon"><img src="<?php echo esc_url( RTWALWM_URL.'assets/images/dashboard.png' ); ?>" alt=""></div>
			<?php esc_html_e( 'Dashboard Overview', 'rtwalwm-wp-wc-affiliate-program' ); ?>
		</a>
		<a class="nav-tab <?php echo esc_attr( $rtwalwm_affiliate_active );?>" href="<?php echo esc_url( admin_url( 'admin.php?page=rtwalwm&rtwalwm_tab=rtwalwm_affiliates' ) );?>">
			<div class="rtwalwm_tab_icon"><img src="<?php echo esc_url( RTWALWM_URL.'assets/images/affiliate_menu.png' ); ?>" alt=""></div>
			<?php esc_html_e( 'Affiliates', 'rtwalwm-wp-wc-affiliate-program' ); ?>
		</a>

		<!-- Customize email tab starts -->
		<a class="nav-tab <?php echo esc_attr( $rtwalwm_Email_active ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=rtwalwm&rtwalwm_tab=rtwalwm_customize_email' ) );?>">
			<div class="rtwalwm_tab_icon"><img src="<?php echo esc_url( RTWALWM_URL.'assets/images/email52.png' ); ?>" alt=""></div>
			<?php esc_html_e( 'Customize Email', 'rtwalwm-wp-wc-affiliate-program' ); ?>
		</a>

		<!-- end -->

		<a class="nav-tab <?php echo esc_attr( $rtwalwm_commission_active );?>" href="<?php echo esc_url( admin_url( 'admin.php?page=rtwalwm&rtwalwm_tab=rtwalwm_commission' ) );?>">
			<div class="rtwalwm_tab_icon"><img src="<?php echo esc_url( RTWALWM_URL.'assets/images/commission_setting.png' ); ?>" alt=""></div>
			<?php esc_html_e( 'Commission Setting', 'rtwalwm-wp-wc-affiliate-program' ); ?>
		</a>
		<a class="nav-tab <?php echo esc_attr( $rtwalwm_levels_active );?>" href="<?php echo esc_url( admin_url( 'admin.php?page=rtwalwm&rtwalwm_tab=rtwalwm_levels' ) );?>">
			<div class="rtwalwm_tab_icon"><img src="<?php echo esc_url( RTWALWM_URL.'assets/images/levels.png' ); ?>" alt=""></div>
			<?php esc_html_e( 'Levels', 'rtwalwm-wp-wc-affiliate-program' ); ?> 	<span id = "rtwalwm_pro_img_level"><img src="<?php echo esc_url( RTWALWM_URL.'assets/images/PRO.png' ); ?>" alt=""></span>
		</a>
		<a class="nav-tab <?php echo esc_attr( $rtwalwm_referrals_active );?>" href="<?php echo esc_url( admin_url( 'admin.php?page=rtwalwm&rtwalwm_tab=rtwalwm_referrals' ) );?>">
			<div class="rtwalwm_tab_icon"><img src="<?php echo esc_url( RTWALWM_URL.'assets/images/referral_menu_icon.png' ); ?>" alt=""></div>
			<div class="rtwalwm_notify_wrap">
				<?php
					esc_html_e( 'Referrals', 'rtwalwm-wp-wc-affiliate-program' );
					if( $rtwalwm_referral_noti ){
				?>
						<span class="rtwalwm_notify_number"><?php echo esc_html( $rtwalwm_referral_noti ); ?></span>
				<?php
					}
				?>
			</div>
		</a>
		<a class="nav-tab <?php echo esc_attr( $rtwalwm_payouts_active );?>" href="<?php echo esc_url( admin_url( 'admin.php?page=rtwalwm&rtwalwm_tab=rtwalwm_payouts' ) );?>">
			<div class="rtwalwm_tab_icon"><img src="<?php echo esc_url( RTWALWM_URL.'assets/images/payout.png' ); ?>" alt=""></div>
			<?php esc_html_e( 'Payouts', 'rtwalwm-wp-wc-affiliate-program' ); ?><span id = "rtwalwm_pro_img_level"><img src="<?php echo esc_url( RTWALWM_URL.'assets/images/PRO.png' ); ?>" alt=""></span>
		</a>
		<a class="nav-tab <?php echo esc_attr( $rtwalwm_extra_active );?>" href="<?php echo esc_url( admin_url( 'admin.php?page=rtwalwm&rtwalwm_tab=rtwalwm_extra' ) );?>">
			<div class="rtwalwm_tab_icon"><img src="<?php echo esc_url( RTWALWM_URL.'assets/images/extra_feature.png' ); ?>" alt=""></div>
			<?php esc_html_e( 'Extra Features', 'rtwalwm-wp-wc-affiliate-program' ); ?><span id = "rtwalwm_pro_img_level"><img src="<?php echo esc_url( RTWALWM_URL.'assets/images/PRO.png' ); ?>" alt=""></span>
		</a>
	
		<a class="nav-tab <?php echo esc_attr( $rtwalwm_mlm_active );?>" href="<?php echo esc_url( admin_url( 'admin.php?page=rtwalwm&rtwalwm_tab=rtwalwm_mlm' ) );?>">
			<div class="rtwalwm_tab_icon"><img src="<?php echo esc_url( RTWALWM_URL.'assets/images/mlm.png' ); ?>" alt=""></div>
			<?php esc_html_e( 'MLM', 'rtwalwm-wp-wc-affiliate-program' ); ?>
		</a>
		<a class="nav-tab <?php echo esc_attr( $rtwalwm_custom_banner_active ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=rtwalwm&rtwalwm_tab=rtwalwm_custom_banner' ) );?>">
			<div class="rtwalwm_tab_icon"><img src="<?php echo esc_url( RTWALWM_URL.'assets/images/custom_banner.png' ); ?>" alt=""></div>
			<?php esc_html_e( 'Custom Banner', 'rtwalwm-wp-wc-affiliate-program' ); ?>
		</a>
		<a class="nav-tab <?php echo esc_attr( $rtwalwm_addons_active );?>" href="<?php echo esc_url( admin_url( 'admin.php?page=rtwalwm&rtwalwm_tab=rtwalwm_addons' ) );?>">
			<div class="rtwalwm_tab_icon"><img src="<?php echo esc_url( RTWALWM_URL.'assets/images/add_ons.png' ); ?>" alt=""></div>
			<?php esc_html_e( 'Add-ons', 'rtwalwm-wp-wc-affiliate-program' ); ?><span id = "rtwalwm_pro_img_level"><img src="<?php echo esc_url( RTWALWM_URL.'assets/images/PRO.png' ); ?>" alt=""></span>
		</a>
		<a class="nav-tab <?php echo esc_attr( $rtwalwm_help_active );?>" href="<?php echo esc_url( admin_url( 'admin.php?page=rtwalwm&rtwalwm_tab=rtwalwm_help' ) );?>">
			<div class="rtwalwm_tab_icon"><img src="<?php echo esc_url( RTWALWM_URL.'assets/images/help.png' ); ?>" alt=""></div>
			<?php esc_html_e( 'Help', 'rtwalwm-wp-wc-affiliate-program' ); ?>
		</a>
	</nav>
	<?php
		if( isset( $_GET[ 'rtwalwm_tab' ] ) )
		{
			if( $_GET[ 'rtwalwm_tab' ] == "rtwalwm_dashboard" ){
				include_once( RTWALWM_DIR . '/admin/partials/rtwalwm_tabs/rtwalwm_dashboard.php' );
			}
			elseif( $_GET[ 'rtwalwm_tab' ] == "rtwalwm_referrals" ){
				include_once( RTWALWM_DIR . '/admin/partials/rtwalwm_tabs/rtwalwm_referrals.php' );
			}
			elseif( $_GET[ 'rtwalwm_tab' ] == "rtwalwm_payouts" ){
				include_once( RTWALWM_DIR . '/admin/partials/rtwalwm_tabs/rtwalwm_payouts.php' );
			}
			elseif( $_GET[ 'rtwalwm_tab' ] == "rtwalwm_customize_email" ){
				include_once( RTWALWM_DIR . '/admin/partials/rtwalwm_tabs/rtwalwm_customize_email.php' );
			}
			elseif( $_GET[ 'rtwalwm_tab' ] == "rtwalwm_affiliates" )
			{
				include_once( RTWALWM_DIR . '/admin/partials/rtwalwm_tabs/rtwalwm_affiliates.php' );
			}
			elseif( $_GET[ 'rtwalwm_tab' ] == "rtwalwm_levels" )
			{
				include_once( RTWALWM_DIR . '/admin/partials/rtwalwm_tabs/rtwalwm_levels.php' );
			}
			elseif( $_GET[ 'rtwalwm_tab' ] == "rtwalwm_addons" )
			{
				include_once( RTWALWM_DIR . '/admin/partials/rtwalwm_tabs/rtwalwm_addons.php' );
			}
			elseif( $_GET[ 'rtwalwm_tab' ] == "rtwalwm_sms" )
			{
				do_action( 'rtwalwm_license_tab_include' );
			}
			elseif( $_GET[ 'rtwalwm_tab' ] == "rtwalwm_custom_banner" )
			{
				include_once( RTWALWM_DIR . '/admin/partials/rtwalwm_tabs/rtwalwm_custom_banner.php' );
			}
			elseif( $_GET[ 'rtwalwm_tab' ] == "rtwalwm_help" )
			{
				include_once( RTWALWM_DIR . '/admin/partials/rtwalwm_tabs/rtwalwm_help.php' );
			}
			
			elseif( $_GET[ 'rtwalwm_tab' ] == "rtwalwm_commission" || $_GET[ 'rtwalwm_tab' ] == "rtwalwm_extra" || $_GET[ 'rtwalwm_tab' ] == "rtwalwm_levels_add_edit" || $_GET[ 'rtwalwm_tab' ] == "rtwalwm_mlm" || $_GET[ 'rtwalwm_tab' ] == "rtwalwm_register_template" )
			{
	?>
			<div class="main-wrapper">
				<form enctype="multipart/form-data" action="options.php" method="post">
	<?php
				if( $_GET[ 'rtwalwm_tab' ] == "rtwalwm_commission" ){
					if( RTWALWM_IS_WOO == 1 ){
						include_once( RTWALWM_DIR . '/admin/partials/rtwalwm_tabs/rtwalwm_commission.php' );
					}
					elseif(RTWALWM_IS_Easy == 1)
					{
						include_once( RTWALWM_DIR . '/admin/partials/rtwalwm_tabs/rtwalwm_commission.php' );
					}
					else{
						include_once( RTWALWM_DIR . '/admin/partials/rtwalwm_tabs/rtwalwm_commission_no_woo.php' );
					}
				}
				elseif( $_GET[ 'rtwalwm_tab' ] == "rtwalwm_extra" ){
					include_once( RTWALWM_DIR . '/admin/partials/rtwalwm_tabs/rtwalwm_extra_features.php' );
				}
				elseif( $_GET[ 'rtwalwm_tab' ] == "rtwalwm_levels_add_edit" ){
					include_once( RTWALWM_DIR . '/admin/partials/rtwalwm_tabs/rtwalwm_levels_add_edit.php' );
				}
				elseif( $_GET[ 'rtwalwm_tab' ] == "rtwalwm_mlm" ){
					include_once( RTWALWM_DIR . '/admin/partials/rtwalwm_tabs/rtwalwm_mlm.php' );
				}
				elseif( $_GET[ 'rtwalwm_tab' ] == "rtwalwm_register_template" ){
					include_once( RTWALWM_DIR . '/admin/partials/rtwalwm_addons/rtwalwm_register_template.php' );
				}
	?>
			<?php if($_GET[ 'rtwalwm_tab' ] =="rtwalwm_commission"  || $_GET[ 'rtwalwm_tab' ] =="rtwalwm_mlm" || $_GET[ 'rtwalwm_tab' ] =="rtwalwm_extra" ) {  ?>
					<p class="submit">
						<input type="submit" value="<?php esc_attr_e( 'Save changes', 'rtwalwm-wp-wc-affiliate-program' ); ?>" class="rtwalwm-button" name="submit" />
					</p>
			<?php } ?>
				</form>
				<?php include_once( RTWALWM_DIR . '/admin/partials/rtwalwm_tabs/rtwalwm_footer.php' ); ?>
			</div>
	<?php
			}
		}
		else{
			include_once( RTWALWM_DIR . '/admin/partials/rtwalwm_tabs/rtwalwm_dashboard.php' );
		}
	?>
</div>
