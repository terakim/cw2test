<?php

$rtwalwm_extra_features = get_option( 'rtwwwap_extra_features_opt' );
$rtwalwm_decimal_place = wc_get_price_decimals();
if( RTWALWM_IS_WOO == 1 ){
	$rtwalwm_currency_sym = esc_html( get_woocommerce_currency_symbol() );
}
else{
	$rtwalwm_currency_sym 	= esc_html__( '&#36;', 'rtwalwm-wp-wc-affiliate-program' );

}

$rtwalwm_decimal_separator = ".";
$rtwalwm_thousand_separator = ",";
	// overview
if( !isset( $_GET[ 'rtwalwm_tab' ] ) || ( isset( $_GET[ 'rtwalwm_tab' ] ) && $_GET[ 'rtwalwm_tab' ] == 'overview' ) ){
	global $wpdb;

	$rtwalwm_user_id 			= get_current_user_id();
	$rtwalwm_total_referrals 	= $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(`id`) as total_referrals FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `aff_id`=%d", $rtwalwm_user_id ) );

	$rtwalwm_pending_comm 		= $wpdb->get_var( $wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `aff_id`=%d AND `status`=%d AND `capped`!=%d", $rtwalwm_user_id, 0, 1 ) );

	$rtwalwm_pending_comm = isset($rtwalwm_pending_comm ) && !empty($rtwalwm_pending_comm )? $rtwalwm_pending_comm : 0;

	$rtwalwm_approved_comm 		= $wpdb->get_var( $wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `aff_id`=%d AND `status`=%d", $rtwalwm_user_id, 1 ) );

	$rtwalwm_approved_comm = isset($rtwalwm_approved_comm ) && !empty($rtwalwm_approved_comm )? $rtwalwm_approved_comm : 0;

	$rtwalwm_total_comm 		= $wpdb->get_var( $wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `aff_id`=%d AND `status`=%d", $rtwalwm_user_id, 2 ) );

	$rtwalwm_total_comm = isset($rtwalwm_total_comm ) && !empty($rtwalwm_total_comm )? $rtwalwm_total_comm : 0;

	$rtwalwm_total_comm 		= $rtwalwm_total_comm+$rtwalwm_approved_comm;
	$rtwalwm_wallet 			= (float)get_user_meta( $rtwalwm_user_id, 'rtw_user_wallet', true );

	$rtwalwm_html1 = '';
	$rtwalwm_html1 .= 	'<div id="rtwalwm_overview">';
	$rtwalwm_html1 .= 		'<div class="rtwalwm-overview-box" id="rtwalwm_total_referral">';
	$rtwalwm_html1 .= 			sprintf( '<span>%u</span> %s', ( $rtwalwm_total_referrals ) ? $rtwalwm_total_referrals : '0', esc_html__( 'Total Referrals', 'rtwalwm-wp-wc-affiliate-program' ) );
	$rtwalwm_html1 .= 		'</div>';
	$rtwalwm_html1 .= 		'<div class="rtwalwm-overview-box" id="rtwalwm_total_commission">';
	$rtwalwm_html1 .= 			sprintf( '<span> '.$rtwalwm_currency_sym.number_format( $rtwalwm_total_comm,$rtwalwm_decimal_place,$rtwalwm_decimal_separator, $rtwalwm_thousand_separator) .'</span> %s', esc_html__( 'Total Commission', 'rtwalwm-wp-wc-affiliate-program' ) );
	$rtwalwm_html1 .= 		'</div>';
	$rtwalwm_html1 .= 		'<div class="rtwalwm-overview-box" id="rtwalwm_wallet">';
	$rtwalwm_html1 .= 			sprintf( '<span>'.$rtwalwm_currency_sym.number_format($rtwalwm_wallet,$rtwalwm_decimal_place,$rtwalwm_decimal_separator, $rtwalwm_thousand_separator).'</span> %s', esc_html__( 'Wallet', 'rtwalwm-wp-wc-affiliate-program' ) );
	$rtwalwm_html1 .= 		'</div>';
	$rtwalwm_html1 .= 		'<div class="rtwalwm-overview-box" id="rtwalwm_approved_commission">';
	$rtwalwm_html1 .= 			sprintf( '<span>'.$rtwalwm_currency_sym.number_format( $rtwalwm_approved_comm,$rtwalwm_decimal_place,$rtwalwm_decimal_separator, $rtwalwm_thousand_separator).'</span> %s', esc_html__( 'Approved Commission', 'rtwalwm-wp-wc-affiliate-program' ) );
	$rtwalwm_html1 .= 		'</div>';
	$rtwalwm_html1 .= 		'<div class="rtwalwm-overview-box" id="rtwalwm_pending_commission">';
	$rtwalwm_html1 .= 			sprintf( '<span>'.$rtwalwm_currency_sym.number_format( $rtwalwm_pending_comm,$rtwalwm_decimal_place,$rtwalwm_decimal_separator, $rtwalwm_thousand_separator).'</span> %s', esc_html__( 'Pending Commission', 'rtwalwm-wp-wc-affiliate-program' ) );
	$rtwalwm_html1 .= 		'</div>';
	$rtwalwm_html1 .= 	'</div>';

		//request commission

	
		//request commission

		//referrals
	if( $rtwalwm_total_referrals ){
		$rtwalwm_date_format = get_option( 'date_format' );
		$rtwalwm_time_format = get_option( 'time_format' );
		$rtwalwm_user_all_referrals = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `aff_id` = %d ORDER BY `date` DESC", $rtwalwm_user_id ), ARRAY_A );

		$rtwalwm_html1 .= 	'<div  id="rtwalwm_create_coupon_container">';
		$rtwalwm_html1 .= 		'<p class="rtwalwm_create_coupon_text">';
		$rtwalwm_html1 .= 			sprintf( '%s', esc_html__( 'Referrals', 'rtwalwm-wp-wc-affiliate-program' ) );
		$rtwalwm_html1 .= 		'</p>';
		$rtwalwm_html1 .= 	'</div>';
		$rtwalwm_html1 .= 	'<table id="rtwalwm_referrals_table">';
		$rtwalwm_html1 .= 		'<thead>';
		$rtwalwm_html1 .= 			'<tr>';
		$rtwalwm_html1 .= 				'<th>';
		$rtwalwm_html1 .= 					sprintf( '%s', esc_html__( 'Type', 'rtwalwm-wp-wc-affiliate-program' ) );
		$rtwalwm_html1 .= 				'</th>';
		$rtwalwm_html1 .= 				'<th>';
		$rtwalwm_html1 .= 					sprintf( '%s (%s)', esc_html__( 'Amount', 'rtwalwm-wp-wc-affiliate-program' ), $rtwalwm_currency_sym );
		$rtwalwm_html1 .= 				'</th>';
		$rtwalwm_html1 .= 				'<th>';
		$rtwalwm_html1 .= 					sprintf( '%s', esc_html__( 'Date', 'rtwalwm-wp-wc-affiliate-program' ) );
		$rtwalwm_html1 .= 				'</th>';
		$rtwalwm_html1 .= 				'<th >';
		$rtwalwm_html1 .= 					sprintf( '%s', esc_html__( 'Status', 'rtwalwm-wp-wc-affiliate-program' ) );
		$rtwalwm_html1 .= 				'</th >';
		$rtwalwm_html1 .= 			'</tr>';
		$rtwalwm_html1 .= 		'</thead>';
		$rtwalwm_html1 .= 		'<tbody>';
	
				
			$rtwalwm_html1 .= 		'<tr>';
			$rtwalwm_html1 .= 			'<td>';
			$rtwalwm_html1 .=				sprintf( '%s', esc_html__( 'Referral Bonus', 'rtwalwm-wp-wc-affiliate-program' ) );
			$rtwalwm_html1 .= 			'</td>';
			$rtwalwm_html1 .= 			'<td>';
			$rtwalwm_html1 .=				sprintf( '%s', esc_html__( '10$', 'rtwalwm-wp-wc-affiliate-program' ) );
			$rtwalwm_html1 .= 			'</td>';
			$rtwalwm_html1 .= 			'<td>';
			$rtwalwm_html1 .=				sprintf( '%s', esc_html__( 'January 1, 2020 12:00 am', 'rtwalwm-wp-wc-affiliate-program' ) );
			$rtwalwm_html1 .= 			'</td >';
			$rtwalwm_html1 .= 			'<td >';
			$rtwalwm_html1 .= 					'<div >';
			$rtwalwm_html1 .=					sprintf( '%s', esc_html__( 'Pending', 'rtwalwm-wp-wc-affiliate-program' ) );
			$rtwalwm_html1 .=        		'</div>';
			$rtwalwm_html1 .= 			'</td>';
			$rtwalwm_html1 .= 		'</tr>';

		$rtwalwm_html1 .= 		'</tbody>';
		$rtwalwm_html1 .= 	'</table>';

	}

	return $rtwalwm_html1;
}

	// commissions
if( isset( $_GET[ 'rtwalwm_tab' ] ) && $_GET[ 'rtwalwm_tab' ] == 'commissions' ){
	
	$rtwalwm_commissions = get_option( 'rtwwwap_commission_settings_opt' );
	$rtwalwm_html1 = '';
	
	$rtwalwm_html1 .= 	'<table id="rtwalwm_commission">';
	
	if(RTWALWM_IS_WOO == 1 )
	{
		$rtwalwm_post_type = 'product';
	}
	if(RTWALWM_IS_Easy == 1 )
	{
		$rtwalwm_post_type = 'download';
	}
	if( $rtwalwm_commissions && !empty( $rtwalwm_commissions ) ){
		$rtwalwm_commission_settings = get_option( 'rtwwwap_commission_settings_opt' );
		$rtwalwm_comm_base 	= isset( $rtwalwm_commission_settings[ 'comm_base' ] ) ? $rtwalwm_commission_settings[ 'comm_base' ] : '1';
		
		foreach( $rtwalwm_commissions as $rtwalwm_key => $rtwalwm_value ){

			if( $rtwalwm_key == 'all_commission' ){
				$rtwalwm_html1 .= 	'<div class="rtwalwm_commissionws_wrapper">';
				$rtwalwm_html1 .= 		esc_html__( 'Commission on all Products', 'rtwalwm-wp-wc-affiliate-program' );
				$rtwalwm_html1 .= 		'<span>';
				$rtwalwm_html1 .= 		sprintf( '%s%u', $rtwalwm_currency_sym, esc_html( $rtwalwm_value ) );
				$rtwalwm_html1 .= 		'</span>';
				$rtwalwm_html1 .= 	'</div>';
			}
			elseif( $rtwalwm_key == 'per_prod_mode' ){
				if( $rtwalwm_value == 1 ){
					$rtwalwm_args = array(
						'post_type'  => $rtwalwm_post_type,
						'meta_query' => array(
							array(
								'key' => 'rtwalwm_percentage_commission_box',
								'value'   => '',
								'compare' => '!='
							)
						),
						'fields' 		=> 'ids',
						'numberposts' 	=> -1
					);
				}
				elseif( $rtwalwm_value == 2 ){
					$rtwalwm_args = array(
						'post_type'  => $rtwalwm_post_type,
						'meta_query' => array(
							array(
								'key' => 'rtwalwm_fixed_commission_box',
								'value'   => '',
								'compare' => '!='
							)
						),
						'fields' 		=> 'ids',
						'numberposts' 	=> -1
					);
				}
				else{
					$rtwalwm_args = array(
						'post_type'  => $rtwalwm_post_type,
						'meta_query' => array(
							'relation' => 'OR',
							array(
								'key' => 'rtwalwm_percentage_commission_box',
								'value'   => '',
								'compare' => '!='
							),
							array(
								'key' => 'rtwwwap_fixed_commission_box',
								'value'   => '',
								'compare' => '!='
							)
						),
						'fields' 		=> 'ids',
						'numberposts' 	=> -1
					);
				}
				$rtwalwm_products 	 = get_posts( $rtwalwm_args );

	
					$rtwalwm_html1 .= 	'<thead>';
					$rtwalwm_html1 .= 		'<tr>';
					$rtwalwm_html1 .= 			'<th colspan="3">';
					$rtwalwm_html1 .= 		 		esc_html__( 'Per Product Commission' );
					$rtwalwm_html1 .= 			'</th>';
					$rtwalwm_html1 .= 		'</tr>';
					$rtwalwm_html1 .= 	'</thead>';
					$rtwalwm_html1 .= 	'<tbody>';
					$rtwalwm_html1 .= 		'<tr>';
					$rtwalwm_html1 .= 		'<td><b>';
					$rtwalwm_html1 .= 		 		esc_html__( 'Product Name', 'rtwalwm-wp-wc-affiliate-program' );
					$rtwalwm_html1 .= 			'</b></td>';
				
						$rtwalwm_html1 .= 	'<td><b>';
						$rtwalwm_html1 .= 		 	esc_html__( 'Percentage commission (%)', 'rtwalwm-wp-wc-affiliate-program' );
						$rtwalwm_html1 .= 		'</b></td>';
			

						$rtwalwm_html1 .= 	'<td><b>';
						$rtwalwm_html1 .= 		 	esc_html__( "Fixed commission ($rtwalwm_currency_sym)", 'rtwalwm-wp-wc-affiliate-program' );
						$rtwalwm_html1 .= 		'</b></td>';
					
					    $rtwalwm_html1 .= 		'</tr>';
						$rtwalwm_html1 .= 	'<tr>';

						if( !empty( $rtwalwm_products ) ){
							foreach( $rtwalwm_products as $rtwalwm_key1 => $rtwalwm_value1 ){
								$rtwalwm_perc_comm 	= get_post_meta( $rtwalwm_value1, 'rtwalwm_percentage_commission_box', true );
								$rtwalwm_fix_comm 	= get_post_meta( $rtwalwm_value1, 'rtwalwm_fixed_commission_box', true );
								$rtwalwm_prod_name 	= get_the_title( $rtwalwm_value1 );
	
								$rtwalwm_html1 .= 	'<tr>';
								$rtwalwm_html1 .= 		'<td>';
								$rtwalwm_html1 .= 			esc_html( $rtwalwm_prod_name );
								$rtwalwm_html1 .= 		'</td>';
								$rtwalwm_html1 .= 		'<td>';
								if( $rtwalwm_value == 1 || $rtwalwm_value == 3 ){
									$rtwalwm_html1 .= 			esc_html( $rtwalwm_perc_comm );
								}
								$rtwalwm_html1 .= 		'</td>';
								$rtwalwm_html1 .= 		'<td>';
								if( $rtwalwm_value == 2 || $rtwalwm_value == 3 ){
									$rtwalwm_html1 .= 			esc_html( $rtwalwm_fix_comm );
								}
								$rtwalwm_html1 .= 		'</td>';
								$rtwalwm_html1 .= 	'</tr>';
							}
						}
						else{
						$rtwalwm_html1 .= 		'<td colspan="3" class="rtwalwm_no_comm">'.esc_html__( 'Specific Product commission not set. Check Category Commissions' ).'</td>';
						$rtwalwm_html1 .= 	'</tr>';
						}
						$rtwalwm_html1 .= 	'</tbody>';

			}
		}
	}
	else{
		$rtwalwm_html1 .= 	'<div class="rtwalwm_commissionws_wrapper">';
		$rtwalwm_html1 .= 		esc_html__( 'No Commission is set on any Product', 'rtwalwm-wp-wc-affiliate-program' );
		$rtwalwm_html1 .= 		'<span>';
		$rtwalwm_html1 .= 	'</div>';
	}
	$rtwalwm_html1 .= 	'</table>';
	return $rtwalwm_html1;
}

	// tools
if( isset( $_GET[ 'rtwalwm_tab' ] ) && $_GET[ 'rtwalwm_tab' ] == 'affiliate_tools' ){
	if(RTWALWM_IS_WOO == 1)
	{
	$rtwalwm_all_categories = get_categories( array(
		'hide_empty' 	=> 0,
		'taxonomy'   	=> 'product_cat'
	));
	}
	// display download categories
	if(RTWALWM_IS_Easy == 1)
	{
		$rtwalwm_all_categories = get_categories( array(
		'hide_empty' 	=> 0,
		'taxonomy'   	=> 'download_category'
	));
	}
	

	$rtwalwm_user_name = wp_get_current_user();
	$rtwalwm_user_name = $rtwalwm_user_name->data->user_login;

	$rtwalwm_extra_features_opt 	= get_option( 'rtwwwap_extra_features_opt' );
	$rtwalwm_social_share_setting 	= isset( $rtwalwm_extra_features_opt[ 'social_share' ] ) ? $rtwalwm_extra_features_opt[ 'social_share' ] : 0;
	$rtwalwm_qr_code_setting 		= isset( $rtwalwm_extra_features_opt[ 'qr_code' ] ) ? $rtwalwm_extra_features_opt[ 'qr_code' ] : 0;

	$rtwalwm_html1 = '';
	$rtwalwm_html1 .=	'<div id="rtwalwm_affiliates">';
	$rtwalwm_html1 .=	  	'<h3>'.esc_html__( 'Generate links', 'rtwalwm-wp-wc-affiliate-program' ).'</h3>';
	$rtwalwm_html1 .=	  	'<div id="rtwalwm_aff_links">';
	$rtwalwm_html1 .=	    	'<input type="text" id="rtwalwm_aff_link_input" placeholder="'.esc_attr__( 'Enter any product\'s URL from this website', 'rtwalwm-wp-wc-affiliate-program' ).'" value="'.esc_attr( home_url() ).'"/>';
	$rtwalwm_html1 .=	    	'<p id="rtwalwm_generated_link"></p>';
	$rtwalwm_html1 .=	    	'<input type="button" id="rtwalwm_generate_button" data-rtwalwm_aff_id="'.esc_attr( get_current_user_id() ).'" data-rtwalwm_aff_name="'.esc_attr( $rtwalwm_user_name ).'" value="'.esc_attr__( 'Generate link', 'rtwalwm-wp-wc-affiliate-program' ).'" />';
	$rtwalwm_html1 .=	  	'<div class="rtwalwm_span_copied">';
	$rtwalwm_html1 .=	    	'<input type="button" id="rtwalwm_copy_to_clip" value="'.esc_attr__( 'Copy link', 'rtwalwm-wp-wc-affiliate-program' ).'" />';
	$rtwalwm_html1 .=	    	'<span id="rtwalwm_copy_tooltip_link">'.esc_html__( 'Copied', 'rtwalwm-wp-wc-affiliate-program' ).'</span>';
	$rtwalwm_html1 .=	  	'</div>';

	if( $rtwalwm_qr_code_setting ){
		$rtwalwm_html1 .=	    '<input type="button" id="rtwalwm_generate_qr" value="'.esc_attr__( 'Create QR Code', 'rtwalwm-wp-wc-affiliate-program' ).'" />';
	}
	$rtwalwm_html1 .=	  	'</div>';

	$rtwalwm_html1 .=	  	'<div class="rtwalwm_share_qr">';
		//social share
	if( $rtwalwm_social_share_setting === 'on' ){
		$rtwalwm_twitter_img_url 	= esc_url( RTWALWM_URL.'/assets/images/twitter-share.png' );
		$rtwalwm_facebook_img_url 	= esc_url( RTWALWM_URL.'/assets/images/facebook-share.png' );
		$rtwalwm_mail_img_url 		= esc_url( RTWALWM_URL.'/assets/images/mail-share.png' );
		$rtwalwm_whatsapp_img_url 	= esc_url( RTWALWM_URL.'/assets/images/whatsapp-share.png' );
		$rtwalwm_html1 .=	  	'<div class="rtwalwm_social_share">';
		$rtwalwm_html1 .=	  		'<div class="rtwalwm_btn">';
		$rtwalwm_html1 .=	  			'<a class="twitter-share-button rtwalwm_twitter" href="javascript:void(0);">';
		$rtwalwm_html1 .=	  				'<img src="'.$rtwalwm_twitter_img_url.'">';
		$rtwalwm_html1 .=	  				esc_html__( 'Tweet', 'rtwalwm-wp-wc-affiliate-program' );
		$rtwalwm_html1 .=	  			'</a>';
		$rtwalwm_html1 .=	  		'</div>';
		$rtwalwm_html1 .=	  		'<a class="rtwalwm_fb_share" href="javascript:void(0); ">';
		$rtwalwm_html1 .=	  			'<img src="'.$rtwalwm_facebook_img_url.'">';
		$rtwalwm_html1 .=	  			esc_html__( 'Facebook', 'rtwalwm-wp-wc-affiliate-program' );
		$rtwalwm_html1 .=	  		'</a>';
		$rtwalwm_html1 .=	  		'<a class="rtwalwm_mail_button" href="javascript:void(0);" rel="nofollow">';
		$rtwalwm_html1 .=	  			'<img src ="'.$rtwalwm_mail_img_url.'">';
		$rtwalwm_html1 .=	  			esc_html__( 'Mail', 'rtwalwm-wp-wc-affiliate-program' );
		$rtwalwm_html1 .=	  		'</a>';
		$rtwalwm_html1 .=	  		'<a class="rtwalwm_whatsapp_share" href="javascript:void(0);">';
		$rtwalwm_html1 .=	  			'<img src="'.$rtwalwm_whatsapp_img_url.'">';
		$rtwalwm_html1 .=	  			esc_html__( 'Whatsapp', 'rtwalwm-wp-wc-affiliate-program' );
		$rtwalwm_html1 .=	  		'</a>';
		$rtwalwm_html1 .=	  	'</div>';
	}

		//qrcode
	if( $rtwalwm_qr_code_setting ){
		$rtwalwm_html1 .=	'<div id="rtwalwm_qrcode_main"><a id="rtwalwm_qrcode"></a><a id="rtwalwm_download_qr" download><span class="rtwalwm_download_qr">'.esc_html__( 'Download QR', 'rtwalwm-wp-wc-affiliate-program' ).'</span></a></div>';
	}

	$rtwalwm_html1 .=	  	'</div>';
	$rtwalwm_html1 .=	  	'<h3>'.esc_html__( 'Create banners', 'rtwalwm-wp-wc-affiliate-program' ).'</h3>';
	$rtwalwm_html1 .=	  	'<div id="rtwalwm_banner_links">';
	$rtwalwm_html1 .=	  		'<input type="text" id="rtwalwm_banner_prod_search" placeholder="'.esc_attr__( 'Search Product', 'rtwalwm-wp-wc-affiliate-program' ).'" />';
	$rtwalwm_html1 .=	   		'<select class="rtwalwm_select_cat" id="" name="rtwalwm_select_cat">';
	if( !empty( $rtwalwm_all_categories ) ){
		
		foreach ( $rtwalwm_all_categories as $rtwalwm_key => $rtwalwm_category ) {
			
			if($rtwalwm_category->cat_name == 'uncategorized')
			{
			$rtwalwm_html1 .=		'<option value="'.esc_attr( $rtwalwm_category->cat_ID ).'" selected>';
			$rtwalwm_html1 .=			esc_html( $rtwalwm_category->cat_name );
			$rtwalwm_html1 .= 		'</option>';
			}
			else{
			$rtwalwm_html1 .=		'<option value="'.esc_attr( $rtwalwm_category->cat_ID ).'" >';
			$rtwalwm_html1 .=			esc_html( $rtwalwm_category->cat_name );
			$rtwalwm_html1 .= 		'</option>';
			}
			
		}
	}
	else{
		$rtwalwm_html1 .=		'<option value="" >';
		$rtwalwm_html1 .=			esc_html__( 'No Category', 'rtwalwm-wp-wc-affiliate-program' );
		$rtwalwm_html1 .= 		'</option>';
	}
	$rtwalwm_html1 .=	  		'</select>';
	$rtwalwm_html1 .=	  		'<div>';
	$rtwalwm_html1 .=	    		'<input type="button" id="rtwalwm_search_button" value="'.esc_attr__( 'Search', 'rtwalwm-wp-wc-affiliate-program' ).'" />';
	$rtwalwm_html1 .=	  		'</div>';
	$rtwalwm_html1 .=	  	'</div>';
	$rtwalwm_html1 .=	  	'<div id="rtwalwm_search_main_container">';
	$rtwalwm_html1 .=		'</div>';
	$rtwalwm_html1 .=	'</div>';

	return $rtwalwm_html1;
}



	// profile
	if( isset( $_GET[ 'rtwalwm_tab' ] ) && $_GET[ 'rtwalwm_tab' ] == 'profile' ){
		$rtwalwm_user_id = get_current_user_id();
		if(isset($_POST['rtwalwm_profile_save'])){

			$rtwalwm_first_name = sanitize_text_field($_POST['first_name']);
			$rtwalwm_last_name = sanitize_text_field($_POST['last_name']);

			if($rtwalwm_first_name){
				update_user_meta($rtwalwm_user_id,'first_name',$rtwalwm_first_name );
			}
			if($rtwalwm_last_name){
				update_user_meta($rtwalwm_user_id,'last_name',$rtwalwm_last_name );
			}
		}
		$rtwalwm_userdata = get_user_meta($rtwalwm_user_id);
		$rtwalwm_user = get_userdata($rtwalwm_user_id);
		$rtwalwm_html1 = '';
		$rtwalwm_html1 = 	'<form action="" method="post">';
		$rtwalwm_html1 .= 	'<div id="rtwalwm_mail_optIn">';
		$rtwalwm_html1 .= 		'<h3>'.esc_html__( "Profile", "rtwalwm-wp-wc-affiliate-program" ).'</h3>';
		$rtwalwm_html1 .= 					'<label>'.esc_html__( "Username", "rtwalwm-wp-wc-affiliate-program" ).'</label>';
		$rtwalwm_html1 .= 			'<div class="rtwalwm-text"><span class="rtwalwm-text-icon"><i class="fas fa-user"></i></span><input type="text" name="user_login" placeholder="'.esc_attr__( "Username", "rtwalwm-wp-wc-affiliate-program" ).'" value="'.esc_attr($rtwalwm_userdata['nickname'][0]).'" disabled></div>';
	
		$rtwalwm_html1 .= 					'<label>'.esc_html__( "Email", "rtwalwm-wp-wc-affiliate-program" ).'</label>';
		$rtwalwm_html1 .= 			'<div class="rtwalwm-text"><span class="rtwalwm-text-icon"><i class="fas fa-envelope"></i></span><input type="email" name="user_email" placeholder="'.esc_attr__( "Email", "rtwalwm-wp-wc-affiliate-program" ).'" value="'.esc_attr($rtwalwm_user->user_email).'" disabled></div>';
	
		$rtwalwm_html1 .= 					'<label>'.esc_html__( "First Name", "rtwalwm-wp-wc-affiliate-program" ).'</label>';
		$rtwalwm_html1 .= 			'<div class="rtwalwm-text"><span class="rtwalwm-text-icon"><i class="fas fa-user"></i></span><input type="text" name="first_name" placeholder="'.esc_attr__( "First Name", "rtwalwm-wp-wc-affiliate-program" ).'" value="'.esc_attr($rtwalwm_userdata['first_name'][0]).'"></div>';
	
		$rtwalwm_html1 .= 					'<label>'.esc_html__( "Last Name", "rtwalwm-wp-wc-affiliate-program" ).'</label>';
		$rtwalwm_html1 .= 			'<div class="rtwalwm-text"><span class="rtwalwm-text-icon"><i class="fas fa-user"></i></span><input type="text" name="last_name" placeholder="'.esc_attr__( "Last Name", "rtwalwm-wp-wc-affiliate-program" ).'" value="'.esc_attr($rtwalwm_userdata['last_name'][0]).'"></div>';
		$rtwalwm_html1 .= 		'</div">';
		$rtwalwm_html1 .= 		'<div><input type="submit" class="rtwalwm_profile_save" value="'.esc_attr__( "Update Details", "rtwalwm-wp-wc-affiliate-program" ).'" id="rtwalwm_profile_save" name="rtwalwm_profile_save"></div>';
		$rtwalwm_html1 .= 	'</form>';
		return $rtwalwm_html1;
	}


// custom banner
if( isset( $_GET[ 'rtwalwm_tab' ] ) && $_GET[ 'rtwalwm_tab' ] == 'custom_banner' ){
	$rtwalwm_custom_banner = get_option( 'rtwwwap_custom_banner_opt' );
	if(	$rtwalwm_custom_banner != '' )
	{
		$rtwalwm_count = 1;
		$rtwalwm_html1 = '';
		$rtwalwm_html1 .= 	'<div class="rtwalwm_custom_banner_container">';
		foreach($rtwalwm_custom_banner as $key => $value)
		{
			
			$rtwalwm_image_src = wp_get_attachment_url($value['image_id']);		
			$rtwalwm_image_width = $value['image_width']/2;
			$rtwalwm_image_height = (int)$value['image_height'];
			if( $rtwalwm_image_height > 350)
			{	
				$rtwalwm_image_height = $rtwalwm_image_height/2 ; 
			}
		
	
			$rtwalwm_html1 .= 	'<div class ="rtwalwm_custom_banner_product" style=" width:'.$rtwalwm_image_width.'px;height:auto;">';
			$rtwalwm_html1 .=        '<div class = "rtwalwm_banner_no">'.esc_html("Banner No.").esc_attr__($rtwalwm_count).'</div>';
			$rtwalwm_html1 .= 				'<div class ="rtwalwm_custom_banner_product_image" style="height:'.$rtwalwm_image_height.'px;">';
			$rtwalwm_html1 .=					'<img class="rtwalwm_banner_image"  src="'.$rtwalwm_image_src.'" >';
			$rtwalwm_html1 .=				 '</div>';
			$rtwalwm_html1 .=				'<div>';
			$rtwalwm_html1 .=				'<span class="rtwalwm_image_size_detail">Image Size : '.$value['image_width'].'</span>';
			$rtwalwm_html1 .=				'<span class="rtwalwm_image_size_detail"> '.esc_html__( " x ", "rtwalwm-wp-wc-affiliate-program" ).$value['image_height'].'</span>';
			$rtwalwm_html1 .=				'</div>';
			$rtwalwm_html1 .=				 '<label class="rtwalwm_copy_info" >'.esc_html__( " Copy and paste the code into your Website", "rtwalwm-wp-wc-affiliate-program" ).'</label>';	
			$rtwalwm_html1 .=				 '<div class="rtwalwm_banner_copy_text" >'.esc_html__( "Copied", "rtwalwm-wp-wc-affiliate-program" ).'</div>';
			$rtwalwm_html1 .= 			'<button  data-image_id ="'.esc_attr($rtwalwm_image_src).'" data-target_link ="'.esc_attr($value['target_link']).'" name="rtwalwm_custom_banner_copy_html" class="rtwalwm_custom_banner_copy_html" data-image_width ="'.esc_attr($value['image_width']).'" data-image_height ="'.esc_attr($value['image_height']).'">'.esc_html__( "COPY HTML", "rtwalwm-wp-wc-affiliate-program" ).'</button>';
			$rtwalwm_html1 .= 	'</div>'; 

			$rtwalwm_count  = $rtwalwm_count + 1;	 
		
		}
		$rtwalwm_html1 .= 	'</div>';
		return $rtwalwm_html1;
	}
}

