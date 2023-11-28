<?php
	if ( ! defined( 'ABSPATH' ) ) {
	    exit; // Exit if accessed directly.
	}

	$rtwalwm_html 			= '';
	$rtwalwm_is_affiliate 	= false;
	$rtwalwm_user_id 		= get_current_user_id();
	

	$rtwalwm_extra_features = get_option( 'rtwwwap_extra_features_opt' );
	$rtwalwm_affilaite_template = isset($rtwalwm_extra_features[ 'affiliate_page_template' ]) ? $rtwalwm_extra_features[ 'affiliate_page_template' ] : 1 ;
	$rtwalwm_custom_banner = get_option( 'rtwwwap_custom_banner_opt' );

	if(!is_user_logged_in())
	{
			$rtwalwm_html .= "<div id='rtwalwm_aff_not_login'>";
			$rtwalwm_html .= 	"<div id='rtwalwm_aff_page_login'>";
			$rtwalwm_html .=		do_shortcode('[rtwwwap_aff_login_page]');
			$rtwalwm_html .=	"</div>";
			// $rtwalwm_html .= 	"<div id='rtwwwap_aff_page_reg'>";
			// $rtwalwm_html .=		do_shortcode('[rtwwwap_aff_reg_page]');
			// $rtwalwm_html .=	"</div>";
			$rtwalwm_html .= "</div>";
	}


	$rtwalwm_extra_features = get_option( 'rtwwwap_extra_features_opt' );

	$rtwalwm_css = isset( $rtwalwm_extra_features[ 'css' ] ) ? $rtwalwm_extra_features[ 'css' ] : '';
	if( $rtwalwm_css ){
		$rtwalwm_html .= '<style>';
		$rtwalwm_html .= 	$rtwalwm_css;
		$rtwalwm_html .= '</style>';
	}
	if(is_user_logged_in()){

			if( $rtwalwm_user_id ){
				$rtwalwm_ask_aff_approval 	= isset( $rtwalwm_extra_features[ 'aff_verify' ] ) ? $rtwalwm_extra_features[ 'aff_verify' ] : 0;
				$rtwalwm_is_aff_approved 	= ( $rtwalwm_ask_aff_approval ) ? get_user_meta( $rtwalwm_user_id, 'rtwwwap_aff_approved', true ) : 1;
				$rtwalwm_is_affiliate 		= get_user_meta( $rtwalwm_user_id, 'rtwwwap_affiliate', true );
			}

			$rtwalwm_html .= 	'<div id="rtwalwm_main_container">';
			if( $rtwalwm_is_affiliate && !$rtwalwm_is_aff_approved ){
				$rtwalwm_html .= 	'<div id="rtwalwm_not_approved">';
				$rtwalwm_html .=	 esc_html__( 'Not approved yet', 'rtwalwm-wp-wc-affiliate-program' );
				$rtwalwm_html .=	'</div>';
			}
			elseif( !$rtwalwm_is_affiliate ){

				$rtwalwm_become_button 		= isset( $rtwalwm_extra_features[ 'become_title' ] ) ? esc_html__( $rtwalwm_extra_features[ 'become_title' ]) : esc_html__( 'Become an Affiliate', 'rtwalwm-wp-wc-affiliate-program' );
				
				if($rtwalwm_extra_features[ 'become_title' ] != '' )
				{
					$rtwalwm_become_text 		= $rtwalwm_become_button;
					
				}
				else
				{
					$rtwalwm_become_text  =  esc_html__( 'Become an Affiliate', 'rtwalwm-wp-wc-affiliate-program' );
				}
				$rtwalwm_default_benefits 	= sprintf( "<ul><li>%s</li><li>%s</li><li>%s</li></ul>", esc_html__( 'Earn extra money just by marketing our products with our affiliate tools', 'rtwalwm-wp-wc-affiliate-program' ), esc_html__( 'Earn wallet amount to buy products on our site', 'rtwalwm-wp-wc-affiliate-program' ), esc_html__( 'Signup Bonus when someone signup from your shared link', 'rtwalwm-wp-wc-affiliate-program' ) );

				$rtwalwm_benefits 			= isset( $rtwalwm_extra_features[ 'aff_benefits' ] ) ? esc_html__( $rtwalwm_extra_features[ 'aff_benefits' ]) : $rtwalwm_default_benefits;

				$rtwalwm_html .= 	'<div id="rtwalwm_not_affiliate">';
				$rtwalwm_html .=		'<div id="rtwalwm_become_affiliate">';
				
				$rtwalwm_html .=		'<input id="rtwalwm_affiliate_activate" type="button" name="" value="'.esc_attr( $rtwalwm_become_text ).'" data-rtwalwm_num="'.esc_attr($rtwalwm_user_id).'" />';

				$rtwalwm_benefits_title = isset( $rtwalwm_extra_features[ 'benefits_title' ] ) ? esc_html__($rtwalwm_extra_features[ 'benefits_title' ]) : esc_html__( 'Benefits of becoming our Affiliate', 'rtwalwm-wp-wc-affiliate-program' );

				$rtwalwm_html .=		'</div>';
				$rtwalwm_html .=		'<br>';
				$rtwalwm_html .=		'<hr>';
				$rtwalwm_html .=		'<h3>';
				$rtwalwm_html .=			$rtwalwm_benefits_title;
				$rtwalwm_html .=		'</h3>';
				$rtwalwm_html .=		'<div id="rtwalwm_benefits">'.$rtwalwm_benefits.'</div>';
				$rtwalwm_html .=	'</div>';
			}
			else{
				$rtwalwm_html1 = include( RTWALWM_DIR.'public/templates/rtwalwm_affiliate_body.php' );
				$rtwalwm_mlm = get_option( 'rtwwwap_mlm_opt' );

				$rtwalwm_overview_active 			= '';
				$rtwalwm_commissions_active 		= '';
				$rtwalwm_affiliate_tools_active 	= '';
				$rtwalwm_download_active 			= '';
				$rtwalwm_payout_active 				= '';
				$rtwalwm_profile_active 			= '';
				$rtwalwm_mlm_active 				= '';
				$rtwalwm_custom_banner_active 		= '';  


				if( isset( $_GET[ 'rtwalwm_tab' ] ) )
				{
					if( $_GET[ 'rtwalwm_tab' ] == "overview" )
					{
						$rtwalwm_overview_active = "current-menu-item";
					}
					elseif( $_GET[ 'rtwalwm_tab' ] == "commissions" )
					{
						$rtwalwm_commissions_active = "current-menu-item";
					}
					elseif( $_GET[ 'rtwalwm_tab' ] == "affiliate_tools" )
					{
						$rtwalwm_affiliate_tools_active = "current-menu-item";
					}
					elseif( $_GET[ 'rtwalwm_tab' ] == "download" )
					{
						$rtwalwm_download_active = "current-menu-item";
					}
					elseif( $_GET[ 'rtwalwm_tab' ] == "payout" )
					{
						$rtwalwm_payout_active = "current-menu-item";
					}
					elseif( $_GET[ 'rtwalwm_tab' ] == "profile" )
					{
						$rtwalwm_profile_active = "current-menu-item";	
					}
					elseif( $_GET[ 'rtwalwm_tab' ] == "custom_banner" )
					{
						$rtwalwm_custom_banner_active = "current-menu-item";
					}
					elseif( $_GET[ 'rtwalwm_tab' ] == "mlm" )
					{
						$rtwalwm_mlm_active = "current-menu-item";
					}
				}
				else
				{
					$rtwalwm_overview_active = "current-menu-item";
				}

				$rtwalwm_overview_url		= get_page_link().'?rtwalwm_tab=overview';
				$rtwalwm_commissions_url	= get_page_link().'?rtwalwm_tab=commissions';
				$rtwalwm_affiliate_tools_url= get_page_link().'?rtwalwm_tab=affiliate_tools';
				$rtwalwm_download_url		= get_page_link().'?rtwalwm_tab=download';
				$rtwalwm_payout_url			= get_page_link().'?rtwalwm_tab=payout';
				$rtwalwm_profile_url		= get_page_link().'?rtwalwm_tab=profile'; 
				$rtwalwm_custom_banner_url	= get_page_link().'?rtwalwm_tab=custom_banner';
				$rtwalwm_custom_banner_url	= get_page_link().'?rtwalwm_tab=mlm';



				$rtwalwm_overview_label = isset($rtwalwm_extra_features['affiliate_dash_overview']) && !empty($rtwalwm_extra_features['affiliate_dash_overview']) ? $rtwalwm_extra_features['affiliate_dash_overview'] : 'Overview';
				$rtwalwm_commission_label = isset($rtwalwm_extra_features['affiliate_dash_commission']) && !empty($rtwalwm_extra_features['affiliate_dash_commission']) ? $rtwalwm_extra_features['affiliate_dash_commission'] : 'Commissions';
				$rtwalwm_tools_label = isset($rtwalwm_extra_features['affiliate_dash_tools']) && !empty($rtwalwm_extra_features['affiliate_dash_tools']) ? $rtwalwm_extra_features['affiliate_dash_tools'] : 'Affilate Tools';
				$rtwalwm_download_label = isset($rtwalwm_extra_features['affiliate_dash_download']) && !empty($rtwalwm_extra_features['affiliate_dash_download']) ? $rtwalwm_extra_features['affiliate_dash_download'] : 'Download';
				$rtwalwm_payout_label = isset($rtwalwm_extra_features['affiliate_dash_payout']) && !empty($rtwalwm_extra_features['affiliate_dash_payout']) ? $rtwalwm_extra_features['affiliate_dash_payout'] : 'Payout';
				$rtwalwm_profile_label = isset($rtwalwm_extra_features['affiliate_dash_profile']) && !empty($rtwalwm_extra_features['affiliate_dash_profile']) ? $rtwalwm_extra_features['affiliate_dash_profile'] : 'Profile';
				$rtwalwm_mlm_url			= add_query_arg( 'rtwalwm_tab', esc_html("mlm") );
				
				$rtwalwm_custom_banner_label = isset($rtwalwm_extra_features['affiliate_dash_custom_banner']) && !empty($rtwalwm_extra_features['affiliate_dash_custom_banner']) ? $rtwalwm_extra_features['affiliate_dash_custom_banner'] : 'Custom Banner';
				$rtwalwm_html .=	'<div id="rtwalwm_is_affiliate">';
				$rtwalwm_html .=		'<div id="rtwalwm_affiliate_menu">';
				$rtwalwm_html .=			'<nav class="rtwalwm_main_navigation">';
				$rtwalwm_html .=			'<ul class="rtwalwm_menu">';
				$rtwalwm_html .=				'<li class="'.$rtwalwm_overview_active.'">';
				$rtwalwm_html .=					'<a class="rtwalwm_nav_tab" href="'.esc_url( $rtwalwm_overview_url ).'">';
				$rtwalwm_html .=						esc_html__( $rtwalwm_overview_label, 'rtwalwm-wp-wc-affiliate-program' );
				$rtwalwm_html .=					'</a>';
				$rtwalwm_html .=				'</li>';
				$rtwalwm_html .=				'<li class="'.$rtwalwm_commissions_active.'">';
				$rtwalwm_html .=					'<a class="rtwalwm_nav_tab" href="'.esc_url( $rtwalwm_commissions_url ).'">';
				$rtwalwm_html .=						esc_html__( $rtwalwm_commission_label, 'rtwalwm-wp-wc-affiliate-program' );
				$rtwalwm_html .=					'</a>';
				$rtwalwm_html .=				'</li>';
				$rtwalwm_html .=				'<li class="'.$rtwalwm_affiliate_tools_active.'">';
				$rtwalwm_html .=					'<a class="rtwalwm_nav_tab" href="'.esc_url( $rtwalwm_affiliate_tools_url ).'">';
				$rtwalwm_html .=						esc_html__( $rtwalwm_tools_label, 'rtwalwm-wp-wc-affiliate-program' );
				$rtwalwm_html .=					'</a>';
				$rtwalwm_html .=				'</li>';
				// $rtwalwm_html .=				'<li class="'.$rtwalwm_download_active.'">';
				// $rtwalwm_html .=					'<a class="rtwalwm_nav_tab" href="'.esc_url( $rtwalwm_download_url ).'">';
				// $rtwalwm_html .=						esc_html__( $rtwalwm_download_label, 'rtwalwm-wp-wc-affiliate-program' );
				// $rtwalwm_html .=					'</a>';
				// $rtwalwm_html .=				'</li>';
				// $rtwalwm_html .=				'<li class="'.$rtwalwm_payout_active.'">';
				// $rtwalwm_html .=					'<a class="rtwalwm_nav_tab" href="'.esc_url( $rtwalwm_payout_url ).'">';
				// $rtwalwm_html .=						esc_html__( $rtwalwm_payout_label, 'rtwalwm-wp-wc-affiliate-program' );
				// $rtwalwm_html .=					'</a>';
				// $rtwalwm_html .=				'</li>';
				$rtwalwm_html .=				'<li class="'.$rtwalwm_profile_active.'">';
				$rtwalwm_html .=					'<a class="rtwalwm_nav_tab" href="'.esc_url( $rtwalwm_profile_url ).'">';
				$rtwalwm_html .=						esc_html__( $rtwalwm_profile_label, 'rtwalwm-wp-wc-affiliate-program' );	
				$rtwalwm_html .=					'</a>';
				$rtwalwm_html .=				'</li>';
				if(!empty($rtwalwm_custom_banner) )
				{
				$rtwalwm_html .=				'<li class="'.$rtwalwm_custom_banner_active.'">';
				$rtwalwm_html .=					'<a class="rtwalwm_nav_tab" href="'.esc_url( $rtwalwm_custom_banner_url ).'">';
				$rtwalwm_html .=						esc_html__( $rtwalwm_custom_banner_label, 'rtwalwm-wp-wc-affiliate-program' );	
				$rtwalwm_html .=					'</a>';
				$rtwalwm_html .=				'</li>';
				}
				$rtwalwm_html .=			'</ul>';
				$rtwalwm_html .=			'</nav>';
				$rtwalwm_html .=		'</div>';
				$rtwalwm_html .=		'<div id="rtwalwm_affiliate_body">';
				$rtwalwm_html .=			$rtwalwm_html1;
				$rtwalwm_html .=		'</div>';
				$rtwalwm_html .=	'</div>';
			}
			$rtwalwm_html .= 	'</div>';
	}
		return $rtwalwm_html;

