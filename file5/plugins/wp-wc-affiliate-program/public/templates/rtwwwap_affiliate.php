<?php
	if ( ! defined( 'ABSPATH' ) ) {
	    exit; // Exit if accessed directly.
	}

	$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );
	$rtwwwap_affilaite_template = isset($rtwwwap_extra_features[ 'affiliate_page_template' ]) ? $rtwwwap_extra_features[ 'affiliate_page_template' ] : 1 ;
	$rtwwwap_custom_banner = get_option( 'rtwwwap_custom_banner_opt' );
	$rtwwwap_login_form =  isset($rtwwwap_extra_features['rtwwwap_active_login_shortcode']) ? $rtwwwap_extra_features['rtwwwap_active_login_shortcode'] : '';
	$rtwwwap_registration_form =  isset($rtwwwap_extra_features['rtwwwap_active_registration_shortcode']) ? $rtwwwap_extra_features['rtwwwap_active_registration_shortcode'] : '';



	$rtwwwap_html 			= '';
	$rtwwwap_is_affiliate 	= false;
	$image_url =  RTWWWAP_URL."assets/images/avatar.png";
	$rtwwwap_user_id 		= get_current_user_id();

	if(is_user_logged_in()){
		$image_url = get_avatar_url($rtwwwap_user_id);
	}
	else{
		$image_url =  RTWWWAP_URL."assets/images/avatar.png";
	}
	
	// $image_url = get_avatar_url($rtwwwap_user_id);


	if(!is_user_logged_in())
	{
		// if(isset($_GET['action']) && !empty($_GET['action']) )
		// {
		// 	if( (($_GET['action'] == 'rp') || ($_GET['action'] == 'resetpass')) && !empty($_GET['action']))
		// 		{
		// 			$rtwwwap_html .= "<div id='rtwwwap_aff_not_login'>";
		// 			$rtwwwap_html .= 	"<div id='rtwwwap_reset_password_page'>";
		// 			$rtwwwap_html .=		do_shortcode('[rtwwwap_aff_reset_password]');
		// 			$rtwwwap_html .=	"</div>";
		// 			$rtwwwap_html .= "</div>";
		// 		}
		// }
		// else 
		// {

			if($rtwwwap_login_form != 'on' && $rtwwwap_registration_form != 'on')
			{
					$rtwwwap_html .= 	"<div id='rtwwwap_aff_page_login'>";
					$rtwwwap_html .=		do_shortcode('[rtwwwap_aff_login_page]');
					$rtwwwap_html .=	"</div>";				
					$rtwwwap_html .= 	"<div id='rtwwwap_aff_page_reg'>";
					$rtwwwap_html .=		do_shortcode('[rtwwwap_aff_reg_page]');
					$rtwwwap_html .=	"</div>";
			}
			elseif ($rtwwwap_login_form == 'on' && $rtwwwap_registration_form != 'on')
			{
				$rtwwwap_html .= 	"<div id='rtwwwap_aff_page_reg_1'>";
				$rtwwwap_html .=		do_shortcode('[rtwwwap_aff_reg_page]');
				$rtwwwap_html .=	"</div>";
			}
			elseif ($rtwwwap_login_form != 'on' && $rtwwwap_registration_form == 'on')
			{
				$rtwwwap_html .= 	"<div id='rtwwwap_aff_page_login_1'>";
					$rtwwwap_html .=		do_shortcode('[rtwwwap_aff_login_page]');
					$rtwwwap_html .=	"</div>";	
			}
			$rtwwwap_html .= "</div>";
		// }
	}


	
	$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );

	$rtwwwap_css = isset( $rtwwwap_extra_features[ 'css' ] ) ? $rtwwwap_extra_features[ 'css' ] : '';
	if( $rtwwwap_css ){
		$rtwwwap_html .= '<style>';
		$rtwwwap_html .= 	$rtwwwap_css;
		$rtwwwap_html .= '</style>';
	}
	if(is_user_logged_in()){
			
			$rtwwwap_login_page_id = get_option('rtwwwap_login_page_id');
			$rtwwwap_affiliate_page_id = get_option('rtwwwap_affiliate_page_id');

			if($rtwwwap_login_page_id)
			{
				$redirect_url = get_permalink($rtwwwap_login_page_id);
			}
			else{
				$redirect_url = get_permalink($rtwwwap_login_page_id);
			}
		
			if( $rtwwwap_user_id ){
				$rtwwwap_ask_aff_approval 	= isset( $rtwwwap_extra_features[ 'aff_verify' ] ) ? $rtwwwap_extra_features[ 'aff_verify' ] : 0;
				$rtwwwap_is_aff_approved 	= ( $rtwwwap_ask_aff_approval ) ? get_user_meta( $rtwwwap_user_id, 'rtwwwap_aff_approved', true ) : 1;
				$rtwwwap_is_affiliate 		= get_user_meta( $rtwwwap_user_id, 'rtwwwap_affiliate', true );
			}

			$rtwwwap_html .= 	'<div id="rtwwwap_main_container">';
			if( $rtwwwap_is_affiliate && !$rtwwwap_is_aff_approved ){
				$rtwwwap_html .= 	'<div id="rtwwwap_not_approved">';
				$rtwwwap_html .=	 esc_html__( 'Not approved yet', 'rtwwwap-wp-wc-affiliate-program' );
				$rtwwwap_html .=	'</div>';
			}
			elseif( !$rtwwwap_is_affiliate ){

				
				$rtwwwap_become_button 		= isset( $rtwwwap_extra_features[ 'become_title' ] ) ? $rtwwwap_extra_features[ 'become_title' ] : esc_html__( 'Become an Affiliate', 'rtwwwap-wp-wc-affiliate-program' );
				
				if($rtwwwap_extra_features[ 'become_title' ] != '' )
				{
					$rtwwwap_become_text 		= $rtwwwap_become_button;
					
				}
				else
				{
					$rtwwwap_become_text  =  esc_html__( 'Become an Affiliate', 'rtwwwap-wp-wc-affiliate-program' );
				}
				$rtwwwap_default_benefits 	= sprintf( "<ul><li>%s</li><li>%s</li><li>%s</li></ul>", esc_html__( 'Earn extra money just by marketing our products with our affiliate tools', 'rtwwwap-wp-wc-affiliate-program' ), esc_html__( 'Earn wallet amount to buy products on our site', 'rtwwwap-wp-wc-affiliate-program' ), esc_html__( 'Signup Bonus when someone signup from your shared link', 'rtwwwap-wp-wc-affiliate-program' ) );

				$rtwwwap_benefits 			= isset( $rtwwwap_extra_features[ 'aff_benefits' ] ) ? $rtwwwap_extra_features[ 'aff_benefits' ] : $rtwwwap_default_benefits;

				$rtwwwap_html .= 	'<div id="rtwwwap_not_affiliate">';
				$rtwwwap_html .=		'<div id="rtwwwap_become_affiliate">';
				
				$rtwwwap_html .=		'<input id="rtwwwap_affiliate_activate" type="button" name="" value="'.esc_attr( $rtwwwap_become_text ).'" data-rtwwwap_num="'.$rtwwwap_user_id.'" />';

				$rtwwwap_benefits_title = isset( $rtwwwap_extra_features[ 'benefits_title' ] ) ? $rtwwwap_extra_features[ 'benefits_title' ] : esc_html__( 'Benefits of becoming our Affiliate', 'rtwwwap-wp-wc-affiliate-program' );

				$rtwwwap_html .=		'</div>';
				$rtwwwap_html .=		'<br>';
				$rtwwwap_html .=		'<hr>';
				$rtwwwap_html .=		'<h3>';
				$rtwwwap_html .=			$rtwwwap_benefits_title;
				$rtwwwap_html .=		'</h3>';
				$rtwwwap_html .=		'<div id="rtwwwap_benefits">'.$rtwwwap_benefits.'</div>';
				$rtwwwap_html .=	'</div>';
			}
			else{
				if(	$rtwwwap_affilaite_template == 1 && $rtwwwap_affilaite_template != '')
				{
					$rtwwwap_html1 = include( RTWWWAP_DIR.'public/templates/rtwwwap_affiliate_body.php' );
					$rtwwwap_mlm = get_option( 'rtwwwap_mlm_opt' );

					$rtwwwap_overview_active 			= '';
					$rtwwwap_report_active 				= '';
					$rtwwwap_referral_active			= '';
					$rtwwwap_commissions_active 		= '';
					$rtwwwap_affiliate_tools_active 	= '';
					$rtwwwap_download_active 			= '';
					$rtwwwap_payout_active 				= '';
					$rtwwwap_profile_active 			= '';
					$rtwwwap_mlm_active 				= '';
					$rtwwwap_custom_banner_active 		= '';
					$rtwwap_user_info = 'Admin';


					if( isset( $_GET[ 'rtwwwap_tab' ] ) )
					{
						if( $_GET[ 'rtwwwap_tab' ] == "overview" )
						{
							$rtwwwap_overview_active = "current-menu-item";
						}
					
						elseif( $_GET[ 'rtwwwap_tab' ] == "commissions" )
						{
							$rtwwwap_commissions_active = "current-menu-item";
						}
						elseif( $_GET[ 'rtwwwap_tab' ] == "affiliate_tools" )
						{
							$rtwwwap_affiliate_tools_active = "current-menu-item";
						}
						elseif( $_GET[ 'rtwwwap_tab' ] == "download" )
						{
							$rtwwwap_download_active = "current-menu-item";
						}
						elseif( $_GET[ 'rtwwwap_tab' ] == "payout" )
						{
							$rtwwwap_payout_active = "current-menu-item";
						}elseif( $_GET[ 'rtwwwap_tab' ] == "profile" )
						{
							$rtwwwap_profile_active = "current-menu-item";
						}elseif( $_GET[ 'rtwwwap_tab' ] == "mlm" )
						{
							$rtwwwap_mlm_active = "current-menu-item";
						}elseif( $_GET[ 'rtwwwap_tab' ] == "custom_banner" )
						{
							$rtwwwap_custom_banner_active = "current-menu-item";
						}elseif( $_GET[ 'rtwwwap_tab' ] == "user_info" )
						{
							$rtwwap_user_info = "current-menu-item";
						}
					}
					else
					{
						$rtwwwap_overview_active = "current-menu-item";
					}
					$rtwwwap_page_link = get_page_link();
					$rtwwwap_overview_url		= add_query_arg( 'rtwwwap_tab', esc_html("overview"), $rtwwwap_page_link);

					// custom code check 

					$rtwwwap_curn_user_id = get_current_user_id();
					global $wpdb;
					$rtwwwap_mlm = get_option( 'rtwwwap_mlm_opt' );
					$rtwwwap_mlm_depth = isset( $rtwwwap_mlm[ 'depth' ] ) ? $rtwwwap_mlm[ 'depth' ] : 0;
					$rtwwwap_total_aff_in_org = $this->rtwwwap_loop_each_parent_without_html($rtwwwap_curn_user_id,0,$rtwwwap_mlm_depth,1, $rtwwwap_active=0, $rtwwwap_mlm_child=0 );
					
					$rtwwwap_current_childs = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( `id` ) FROM ".$wpdb->prefix."rtwwwap_mlm WHERE `parent_id` = %d",  $rtwwwap_curn_user_id ) );

					$rank_check1 = false;
					$rank_check2 = false;
					$rank_check3 = false;
					$rank_check4 = false;

					$rtwwwap_rank_detail = get_user_meta($rtwwwap_curn_user_id , 'rank_detail' , true );

					$rtwwwap_rank_priority = isset($rtwwwap_rank_detail[2])? $rtwwwap_rank_detail[2]: 0;

					$rtwwwap_priority = array();
					$rank_requirement_fields = array();
					$rank_requirement_fields = get_option('rtwwwap_rank_details');

					$rtwwwap_total_aff_rank = $this->rtwwwap_loop_each_parent_all_aff_rank($rtwwwap_curn_user_id,$arr=array(),$rtwwwap_mlm_depth,1, $rtwwwap_active=0, $rtwwwap_mlm_child=0 );

					$null_arr = array();
					if(!empty($rtwwwap_total_aff_rank)){
						foreach($rtwwwap_total_aff_rank as $key=> $val)
						{
							array_push($null_arr, isset($val['curr_rank'][0])?$val['curr_rank'][0]:"");
						}
						$rtwwap_aff_reach_rnk = array_count_values($null_arr);
					}
					

					$rank_id =0;
					if(!empty($rank_requirement_fields))
					{
						foreach($rank_requirement_fields as $option_name => $option_val ){

							$rank_details = array();

							array_push($rank_details,$option_val['rank_name'], $option_val['rank_desc'], $option_val['rank_priority'], $option_val['rank_commission']);

							foreach($option_val['rank_requirement'] as $key => $value){

								if(isset($value['optionField'])){

									if(count($option_val['rank_requirement']) == 1 ){
									
										if( $value['optionField'] ==1 ){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check4 = true;
										}
										if(isset($value['personalAff']) && $value['optionField'] ==2 && $value['personalAff']<= $rtwwwap_current_childs ){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check1 = true;
										}
										if(isset($value['totalAff']) && $value['optionField'] ==3 && $value['totalAff']<= $rtwwwap_total_aff_in_org ){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check2 = true;
										}
										if($value['optionField'] ==4){
											foreach($rtwwap_aff_reach_rnk as $rank_key => $rank_val){
												
												if(isset($value['rankName']) && isset($value['reachRankAff']) && $value['reachRankAff'] == $rank_val && $value['rankName'] == $rank_key){
													array_push($rtwwwap_priority,$option_val['rank_priority']);
													$rank_check3 = true;
												}
											}
										}
				
										if($rank_check2 == true || $rank_check1 == true || $rank_check3 == true || $rank_check4 == true ){
											
											$highest_rank = max($rtwwwap_priority);
											if($highest_rank > $rtwwwap_rank_priority){
												$rank_id = $option_val['rank_priority'];
												update_user_meta($rtwwwap_curn_user_id,'rank_detail', $rank_details);
											}
										}
										else{
											update_user_meta($rtwwwap_curn_user_id,'rank_detail', null);
										}
									}

									if(count($option_val['rank_requirement']) == 2 ){

										if(isset($value['optionField']) && $value['optionField'] == 1){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check1 = true;
										}

										if(isset($value['personalAff']) && $value['optionField'] ==2 && $value['personalAff']<= $rtwwwap_current_childs ){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check2 = true;
										}
										if(isset($value['totalAff']) && $value['optionField'] ==3 && $value['totalAff']<= $rtwwwap_total_aff_in_org ){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check3 = true;
										}
										if($value['optionField'] ==4){
											foreach($rtwwap_aff_reach_rnk as $rank_key => $rank_val){
												
												if(isset($value['rankName']) && isset($value['reachRankAff']) && $value['reachRankAff'] == $rank_val && $value['rankName'] == $rank_key){
													array_push($rtwwwap_priority,$option_val['rank_priority']);
													$rank_check4 = true;
												}
											}
										}
				
										if(($rank_check1 == true && $rank_check2 == true) || ($rank_check1 == true && $rank_check3 == true) || ($rank_check1 == true && $rank_check4 == true) || ($rank_check2 == true && $rank_check3 == true)|| ($rank_check2 == true && $rank_check4 == true)|| ($rank_check3 == true && $rank_check4 == true)){
											$highest_rank = max($rtwwwap_priority);
											if($highest_rank > $rtwwwap_rank_priority){
												$rank_id = $option_val['rank_priority'];
												update_user_meta($rtwwwap_curn_user_id,'rank_detail', $rank_details);
											}
										}
									
									}
									if(count($option_val['rank_requirement']) == 3 ){

										if(isset($value['optionField']) && $value['optionField'] == 1){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check1 = true;
										}

										if(isset($value['personalAff']) && $value['optionField'] ==2 && $value['personalAff']<= $rtwwwap_current_childs ){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check2 = true;
										}
				
										if(isset($value['totalAff']) && $value['optionField'] ==3 && $value['totalAff']<= $rtwwwap_total_aff_in_org ){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check3 = true;
										}
										if($value['optionField'] ==4){
											foreach($rtwwap_aff_reach_rnk as $rank_key => $rank_val){
												if(isset($value['rankName']) && $value['rankName'] == $rank_val && $value['rankName'] == $rank_key){
													array_push($rtwwwap_priority,$option_val['rank_priority']);
													$rank_check4 = true;
												}
											}
										}
				
										if(($rank_check1 == true && $rank_check2 == true && $rank_check3 == true) || ($rank_check2 == true && $rank_check3 == true && $rank_check4 == true) || ($rank_check1 == true && $rank_check2 == true && $rank_check4 == true) || ($rank_check1 == true && $rank_check3 == true && $rank_check4 == true)){
											$highest_rank = max($rtwwwap_priority);
											if($highest_rank > $rtwwwap_rank_priority){
												$rank_id = $option_val['rank_priority'];
												update_user_meta($rtwwwap_curn_user_id,'rank_detail', $rank_details);
											}
										
										}
									
									}
									if(count($option_val['rank_requirement']) == 4 ){

										if(isset($value['optionField']) && $value['optionField'] == 1){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check1 = true;
										}

										if(isset($value['personalAff']) && $value['optionField'] ==2 && $value['personalAff']<= $rtwwwap_current_childs ){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check2 = true;
										}
				
										if(isset($value['totalAff']) && $value['optionField'] ==3 && $value['totalAff']<= $rtwwwap_total_aff_in_org ){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check3 = true;
										}
										if($value['optionField'] ==4){
											foreach($rtwwap_aff_reach_rnk as $rank_key => $rank_val){
												if(isset($value['rankName']) && $value['rankName'] == $rank_val && $value['rankName'] == $rank_key){
													array_push($rtwwwap_priority,$option_val['rank_priority']);
													$rank_check4 = true;
												}
											}
										}
				
										if($rank_check1 == true && $rank_check2 == true && $rank_check3 == true && $rank_check4 == true){
											$highest_rank = max($rtwwwap_priority);
											if($highest_rank > $rtwwwap_rank_priority){
												$rank_id = $option_val['rank_priority'];
												update_user_meta($rtwwwap_curn_user_id,'rank_detail', $rank_details);
											}
											
										}
									}
								}
								
								
							}
							$rank_check1 = false;
							$rank_check2 = false;
							$rank_check3 = false;
							$rank_check4 = false;
						}
					}
					$rtwwwap_rank_details1 = get_user_meta($rtwwwap_curn_user_id , 'rank_detail' , true );
					$rtwwwap_rank_commision =  isset($rtwwwap_rank_details1[3])? $rtwwwap_rank_details1[3]: "";
					$rtwwwap_currency = get_woocommerce_currency();

					if(($rank_id > $rtwwwap_rank_priority) && $rtwwwap_rank_details1 ){
						$rtwwwap_updated = $wpdb->insert(
							$wpdb->prefix.'rtwwwap_referrals',
							array(
							'aff_id' => $rtwwwap_curn_user_id,
							'type' => 15,
							'order_id' => "",
							'date' => date( 'Y-m-d H:i:s' ),
							'status' => 0,
							'amount' => $rtwwwap_rank_commision,
							'capped' => "",
							'currency' => $rtwwwap_currency,
							'product_details' => "",
							)
						);
					}

					$rank_name = isset($rtwwwap_rank_details1[0])? $rtwwwap_rank_details1[0]: "";

					$rtwwwap_commissions_url	= add_query_arg( 'rtwwwap_tab', esc_html("commissions"), $rtwwwap_page_link );
					$rtwwwap_affiliate_tools_url= add_query_arg( 'rtwwwap_tab', esc_html("affiliate_tools"), $rtwwwap_page_link );
					$rtwwwap_download_url		= add_query_arg( 'rtwwwap_tab', esc_html("download"), $rtwwwap_page_link );
					$rtwwwap_payout_url			= add_query_arg( 'rtwwwap_tab', esc_html("payout"), $rtwwwap_page_link );
					$rtwwwap_profile_url		= add_query_arg( 'rtwwwap_tab', esc_html("profile"), $rtwwwap_page_link );
					$rtwwwap_mlm_url			= add_query_arg( 'rtwwwap_tab', esc_html("mlm"), $rtwwwap_page_link );
					$rtwwwap_custom_banner_url		= add_query_arg( 'rtwwwap_tab', esc_html("custom_banner"), $rtwwwap_page_link );
					$rtwwwap_overview_label = isset($rtwwwap_extra_features['affiliate_dash_overview']) && !empty($rtwwwap_extra_features['affiliate_dash_overview']) ? $rtwwwap_extra_features['affiliate_dash_overview'] : 'Overview';
					$rtwwwap_commission_label = isset($rtwwwap_extra_features['affiliate_dash_commission']) && !empty($rtwwwap_extra_features['affiliate_dash_commission']) ? $rtwwwap_extra_features['affiliate_dash_commission'] : 'Commissions';
					$rtwwwap_tools_label = isset($rtwwwap_extra_features['affiliate_dash_tools']) && !empty($rtwwwap_extra_features['affiliate_dash_tools']) ? $rtwwwap_extra_features['affiliate_dash_tools'] : 'Affilate Tools';
					$rtwwwap_download_label = isset($rtwwwap_extra_features['affiliate_dash_download']) && !empty($rtwwwap_extra_features['affiliate_dash_download']) ? $rtwwwap_extra_features['affiliate_dash_download'] : 'Download';
					$rtwwwap_payout_label = isset($rtwwwap_extra_features['affiliate_dash_payout']) && !empty($rtwwwap_extra_features['affiliate_dash_payout']) ? $rtwwwap_extra_features['affiliate_dash_payout'] : 'Payout';
					$rtwwwap_profile_label = isset($rtwwwap_extra_features['affiliate_dash_profile']) && !empty($rtwwwap_extra_features['affiliate_dash_profile']) ? $rtwwwap_extra_features['affiliate_dash_profile'] : 'Profile';
					$rtwwwap_custom_banner_label = isset($rtwwwap_extra_features['affiliate_dash_custom_banner']) && !empty($rtwwwap_extra_features['affiliate_dash_custom_banner']) ? $rtwwwap_extra_features['affiliate_dash_custom_banner'] : 'Custom Banner';
					$rtwwwap_mlm_label = isset($rtwwwap_extra_features['affiliate_dash_MLM']) && !empty($rtwwwap_extra_features['affiliate_dash_MLM']) ? $rtwwwap_extra_features['affiliate_dash_MLM'] : 'MLM';

					$rtwwwap_html .=	'<div id="rtwwwap_is_affiliate">';

					$current_user = wp_get_current_user();

					// $rtwwwap_html .= '<div class="rtwwwap_user_profile">
					// 				<img src="'.$image_url.'" class="rtwwwap_logged_user_img">
					// 				<h4 class="rtwwwap_username rtwwwap_dropdown-toggle">'.esc_html($current_user->user_nicename,'rtwwwap-wp-wc-affiliate-program').'<i class="fa fa-angle-down"></i></h4>';
									
					// // $rtwwwap_html .=  '</div>';
					// $rtwwwap_html .=  		 '<a class="rtwwwap_logout_button_temp1" href='.wp_logout_url($redirect_url).'>
					// 							<i class="fas fa-sign-out-alt"></i>
					// 							<span class="rtwwwap_logout_text">Logout</span></a>';
					if($rtwwwap_curn_user_id != 1 && $rank_name){
						$rtwwwap_html .=	'<div class="rtwwwap_rank_name">'.esc_html("Affiliate Rank: $rank_name",'rtwwwap-wp-wc-affiliate-program').'</div>';
					}

					$rtwwwap_html .=		'<div id="rtwwwap_affiliate_menu">';
					$rtwwwap_html .=			'<nav class="rtwwwap_main_navigation">';
					$rtwwwap_html .=			'<ul class="rtwwwap_menu">';
					$rtwwwap_html .=				'<li class="'.$rtwwwap_overview_active.'">';
					$rtwwwap_html .=					'<a class="rtwwwap_nav_tab" href="'.esc_url( $rtwwwap_overview_url ).'">';
					$rtwwwap_html .=						esc_html__( $rtwwwap_overview_label, 'rtwwwap-wp-wc-affiliate-program' );
					$rtwwwap_html .=					'</a>';
					$rtwwwap_html .=				'</li>';
				
		
					$rtwwwap_html .=				'<li class="'.$rtwwwap_commissions_active.'">';
					$rtwwwap_html .=					'<a class="rtwwwap_nav_tab" href="'.esc_url( $rtwwwap_commissions_url ).'">';
					$rtwwwap_html .=						esc_html__( $rtwwwap_commission_label, 'rtwwwap-wp-wc-affiliate-program' );
					$rtwwwap_html .=					'</a>';
					$rtwwwap_html .=				'</li>';
					$rtwwwap_html .=				'<li class="'.$rtwwwap_affiliate_tools_active.'">';
					$rtwwwap_html .=					'<a class="rtwwwap_nav_tab" href="'.esc_url( $rtwwwap_affiliate_tools_url ).'">';
					$rtwwwap_html .=						esc_html__( $rtwwwap_tools_label, 'rtwwwap-wp-wc-affiliate-program' );
					$rtwwwap_html .=					'</a>';
					$rtwwwap_html .=				'</li>';
					$rtwwwap_html .=				'<li class="'.$rtwwwap_download_active.'">';
					$rtwwwap_html .=					'<a class="rtwwwap_nav_tab" href="'.esc_url( $rtwwwap_download_url ).'">';
					$rtwwwap_html .=						esc_html__( $rtwwwap_download_label, 'rtwwwap-wp-wc-affiliate-program' );
					$rtwwwap_html .=					'</a>';
					$rtwwwap_html .=				'</li>';
					$rtwwwap_html .=				'<li class="'.$rtwwwap_payout_active.'">';
					$rtwwwap_html .=					'<a class="rtwwwap_nav_tab" href="'.esc_url( $rtwwwap_payout_url ).'">';
					$rtwwwap_html .=						esc_html__( $rtwwwap_payout_label, 'rtwwwap-wp-wc-affiliate-program' );
					$rtwwwap_html .=					'</a>';
					$rtwwwap_html .=				'</li>';
					$rtwwwap_html .=				'<li class="'.$rtwwwap_profile_active.'">';
					$rtwwwap_html .=					'<a class="rtwwwap_nav_tab" href="'.esc_url( $rtwwwap_profile_url ).'">';
					$rtwwwap_html .=						esc_html__( $rtwwwap_profile_label, 'rtwwwap-wp-wc-affiliate-program' );	
					$rtwwwap_html .=					'</a>';
					$rtwwwap_html .=				'</li>';
					if(isset($rtwwwap_custom_banner ) && !empty($rtwwwap_custom_banner) )
					{
					$rtwwwap_html .=				'<li class="'.$rtwwwap_custom_banner_active.'">';
					$rtwwwap_html .=					'<a class="rtwwwap_nav_tab" href="'.esc_url( $rtwwwap_custom_banner_url ).'">';
					$rtwwwap_html .=						esc_html__( $rtwwwap_custom_banner_label, 'rtwwwap-wp-wc-affiliate-program' );	
					$rtwwwap_html .=					'</a>';
					$rtwwwap_html .=				'</li>';
					}
					if( isset( $rtwwwap_mlm[ 'activate' ] ) && $rtwwwap_mlm[ 'activate' ] == 1 ){
						$rtwwwap_html .=				'<li class="'.$rtwwwap_mlm_active.'">';
						$rtwwwap_html .=					'<a class="rtwwwap_nav_tab" href="'.esc_url( $rtwwwap_mlm_url ).'">';
						$rtwwwap_html .=						esc_html__( $rtwwwap_mlm_label , 'rtwwwap-wp-wc-affiliate-program' );
						$rtwwwap_html .=					'</a>';
						$rtwwwap_html .=				'</li>';
					}
					// if( isset( $rtwwap_user_info ) && !empty( $rtwwap_user_info )){
					$rtwwwap_html .= '<li class="rtwwwap_profile_list" ><i class="fas fa-user"></i><span class="rtwmer_profile_tab">Profile</span>
					<ul><span>';
					$rtwwwap_html .= esc_html($current_user->user_nicename,'rtwwwap-wp-wc-affiliate-program');
					$rtwwwap_html .= '</span><a class="rtwwwap_logout_button_temp1" href='.wp_logout_url($redirect_url).'>
					<i class="fas fa-sign-out-alt"></i>
					<span class="rtwwwap_logout_text">Logout</span></a></ul>
					</li>';
					// }
					$rtwwwap_html .=			'</ul>';
					$rtwwwap_html .=			'</nav>';
					$rtwwwap_html .=		'</div>';

					$rtwwwap_html .=		'<div id="rtwwwap_affiliate_body">';
					$rtwwwap_html .=			$rtwwwap_html1;
					$rtwwwap_html .=		'</div>';
					$rtwwwap_html .=	'</div>';

				}
				if(	$rtwwwap_affilaite_template == 2 && $rtwwwap_affilaite_template != ' ')
				{
					$rtwwwap_html1 = include( RTWWWAP_DIR.'public/templates/rtwwwap_affiliate_body_temp_2.php' );

					$rtwwwap_mlm = get_option( 'rtwwwap_mlm_opt' );

					$rtwwwap_overview_active 			= '';
					$rtwwwap_commissions_active 		= '';
					$rtwwwap_affiliate_tools_active 	= '';
					$rtwwwap_download_active 			= '';
					$rtwwwap_payout_active 				= '';
					$rtwwwap_profile_active 			= '';
					$rtwwwap_mlm_active 				= '';
					$rtwwwap_custom_banner_active 		= '';
					$rtwwwap_tab_heading = '';

					if( isset( $_GET[ 'rtwwwap_tab' ] ) )
					{
						if( $_GET[ 'rtwwwap_tab' ] == "overview" )
						{
							$rtwwwap_overview_active = "current-menu-item";
							$rtwwwap_tab_heading = "Overview";
						}
						elseif( $_GET[ 'rtwwwap_tab' ] == "commissions" )
						{
							$rtwwwap_commissions_active = "current-menu-item";
							$rtwwwap_tab_heading = "Commission";
						}
						elseif( $_GET[ 'rtwwwap_tab' ] == "affiliate_tools" )
						{
							$rtwwwap_affiliate_tools_active = "current-menu-item";
							$rtwwwap_tab_heading = "Affiliate_tool";
						}
						elseif( $_GET[ 'rtwwwap_tab' ] == "download" )
						{
							$rtwwwap_download_active = "current-menu-item";
							$rtwwwap_tab_heading = "Download";
						}
						elseif( $_GET[ 'rtwwwap_tab' ] == "payout" )
						{
							$rtwwwap_payout_active = "current-menu-item";
							$rtwwwap_tab_heading = "Payout";
						}elseif( $_GET[ 'rtwwwap_tab' ] == "profile" )
						{
							$rtwwwap_profile_active = "current-menu-item";
							$rtwwwap_tab_heading = "Profile";
						}elseif( $_GET[ 'rtwwwap_tab' ] == "mlm" )
						{
							$rtwwwap_mlm_active = "current-menu-item";
							$rtwwwap_tab_heading = "MLM";
						}elseif( $_GET[ 'rtwwwap_tab' ] == "custom_banner" )
						{
							$rtwwwap_custom_banner_active = "current-menu-item";
							$rtwwwap_tab_heading = "Custom banner";
						}
					}
					else
					{
						$rtwwwap_overview_active = "current-menu-item";
						$rtwwwap_tab_heading ="Overview";
					}
					$rtwwwap_page_link = get_page_link();
					$rtwwwap_overview_url		= add_query_arg( 'rtwwwap_tab', esc_html("overview"), $rtwwwap_page_link);
					$rtwwwap_commissions_url	= add_query_arg( 'rtwwwap_tab', esc_html("commissions"), $rtwwwap_page_link );
					$rtwwwap_affiliate_tools_url= add_query_arg( 'rtwwwap_tab', esc_html("affiliate_tools"), $rtwwwap_page_link );
					$rtwwwap_download_url		= add_query_arg( 'rtwwwap_tab', esc_html("download"), $rtwwwap_page_link );
					$rtwwwap_payout_url			= add_query_arg( 'rtwwwap_tab', esc_html("payout"), $rtwwwap_page_link );
					$rtwwwap_profile_url		= add_query_arg( 'rtwwwap_tab', esc_html("profile"), $rtwwwap_page_link );
					$rtwwwap_mlm_url			= add_query_arg( 'rtwwwap_tab', esc_html("mlm"), $rtwwwap_page_link );
					$rtwwwap_custom_banner_url		= add_query_arg( 'rtwwwap_tab', esc_html("custom_banner"), $rtwwwap_page_link );
					$rtwwwap_overview_label = isset($rtwwwap_extra_features['affiliate_dash_overview']) && !empty($rtwwwap_extra_features['affiliate_dash_overview']) ? $rtwwwap_extra_features['affiliate_dash_overview'] : 'Overview';
					$rtwwwap_commission_label = isset($rtwwwap_extra_features['affiliate_dash_commission']) && !empty($rtwwwap_extra_features['affiliate_dash_commission']) ? $rtwwwap_extra_features['affiliate_dash_commission'] : 'Commissions';
					$rtwwwap_tools_label = isset($rtwwwap_extra_features['affiliate_dash_tools']) && !empty($rtwwwap_extra_features['affiliate_dash_tools']) ? $rtwwwap_extra_features['affiliate_dash_tools'] : 'Affilate Tools';
					$rtwwwap_download_label = isset($rtwwwap_extra_features['affiliate_dash_download']) && !empty($rtwwwap_extra_features['affiliate_dash_download']) ? $rtwwwap_extra_features['affiliate_dash_download'] : 'Download';
					$rtwwwap_payout_label = isset($rtwwwap_extra_features['affiliate_dash_payout']) && !empty($rtwwwap_extra_features['affiliate_dash_payout']) ? $rtwwwap_extra_features['affiliate_dash_payout'] : 'Payout';
					$rtwwwap_profile_label = isset($rtwwwap_extra_features['affiliate_dash_profile']) && !empty($rtwwwap_extra_features['affiliate_dash_profile']) ? $rtwwwap_extra_features['affiliate_dash_profile'] : 'Profile';
					$rtwwwap_custom_banner_label = isset($rtwwwap_extra_features['affiliate_dash_custom_banner']) && !empty($rtwwwap_extra_features['affiliate_dash_custom_banner']) ? $rtwwwap_extra_features['affiliate_dash_custom_banner'] : 'Custom Banner';
					$rtwwwap_mlm_label = isset($rtwwwap_extra_features['affiliate_dash_MLM']) && !empty($rtwwwap_extra_features['affiliate_dash_MLM']) ? $rtwwwap_extra_features['affiliate_dash_MLM'] : 'MLM';

					$rtwwwap_html .=	'<div id="rtwwwap_is_affiliate">';
					$rtwwwap_html1 .= 	'<div id="rtwwwap_overview1">';
					$rtwwwap_html1 .=   '<i class="fa fa-bars"></i>';
					$rtwwwap_html1 .= 	'</div>';
					$rtwwwap_html .=		'<div class="rtwwwap_dflex"><div id="rtwwwap_affiliate_menu">';
					$rtwwwap_html .=			'<nav class="rtwwwap_main_navigation">';
					$rtwwwap_html .=			'<ul class="rtwwwap_menu">';
					$rtwwwap_html .=				'<li class="'.$rtwwwap_overview_active.'">';
					$rtwwwap_html .=					'<a class="rtwwwap_nav_tab" href="'.esc_url( $rtwwwap_overview_url ).'">';
					$rtwwwap_html .=						esc_html__( $rtwwwap_overview_label, 'rtwwwap-wp-wc-affiliate-program' );
					$rtwwwap_html .=					'</a>';
					$rtwwwap_html .=				'</li>';
					$rtwwwap_html .=				'<li class="'.$rtwwwap_commissions_active.'">';
					$rtwwwap_html .=					'<a class="rtwwwap_nav_tab" href="'.esc_url( $rtwwwap_commissions_url ).'">';
					$rtwwwap_html .=						esc_html__( $rtwwwap_commission_label, 'rtwwwap-wp-wc-affiliate-program' );
					$rtwwwap_html .=					'</a>';
					$rtwwwap_html .=				'</li>';
					$rtwwwap_html .=				'<li class="'.$rtwwwap_affiliate_tools_active.'">';
					$rtwwwap_html .=					'<a class="rtwwwap_nav_tab" href="'.esc_url( $rtwwwap_affiliate_tools_url ).'">';
					$rtwwwap_html .=						esc_html__( $rtwwwap_tools_label, 'rtwwwap-wp-wc-affiliate-program' );
					$rtwwwap_html .=					'</a>';
					$rtwwwap_html .=				'</li>';
					$rtwwwap_html .=				'<li class="'.$rtwwwap_download_active.'">';
					$rtwwwap_html .=					'<a class="rtwwwap_nav_tab" href="'.esc_url( $rtwwwap_download_url ).'">';
					$rtwwwap_html .=						esc_html__( $rtwwwap_download_label, 'rtwwwap-wp-wc-affiliate-program' );
					$rtwwwap_html .=					'</a>';
					$rtwwwap_html .=				'</li>';
					$rtwwwap_html .=				'<li class="'.$rtwwwap_payout_active.'">';
					$rtwwwap_html .=					'<a class="rtwwwap_nav_tab" href="'.esc_url( $rtwwwap_payout_url ).'">';
					$rtwwwap_html .=						esc_html__( $rtwwwap_payout_label, 'rtwwwap-wp-wc-affiliate-program' );
					$rtwwwap_html .=					'</a>';
					$rtwwwap_html .=				'</li>';
					$rtwwwap_html .=				'<li class="'.$rtwwwap_profile_active.'">';
					$rtwwwap_html .=					'<a class="rtwwwap_nav_tab" href="'.esc_url( $rtwwwap_profile_url ).'">';
					$rtwwwap_html .=						esc_html__( $rtwwwap_profile_label, 'rtwwwap-wp-wc-affiliate-program' );	
					$rtwwwap_html .=					'</a>';
					$rtwwwap_html .=				'</li>';
					if( isset($rtwwwap_custom_banner ) && !empty($rtwwwap_custom_banner) )
					{
					$rtwwwap_html .=				'<li class="'.$rtwwwap_custom_banner_active.'">';
					$rtwwwap_html .=					'<a class="rtwwwap_nav_tab" href="'.esc_url( $rtwwwap_custom_banner_url ).'">';
					$rtwwwap_html .=						esc_html__( $rtwwwap_custom_banner_label, 'rtwwwap-wp-wc-affiliate-program' );	
					$rtwwwap_html .=					'</a>';
					$rtwwwap_html .=				'</li>';
					}
					if( isset( $rtwwwap_mlm[ 'activate' ] ) && $rtwwwap_mlm[ 'activate' ] == 1 ){
						$rtwwwap_html .=				'<li class="'.$rtwwwap_mlm_active.'">';
						$rtwwwap_html .=					'<a class="rtwwwap_nav_tab" href="'.esc_url( $rtwwwap_mlm_url ).'">';
						$rtwwwap_html .=						esc_html__($rtwwwap_mlm_label, 'rtwwwap-wp-wc-affiliate-program' );
						$rtwwwap_html .=					'</a>';
						$rtwwwap_html .=				'</li>';
					}
					$rtwwwap_html .=			'</ul>';
					$rtwwwap_html .=			'</nav>';
					$rtwwwap_html .=		'</div>';
					$rtwwwap_html .=		'<div id="rtwwwap_affiliate_body">';


					// custom code check 

					$rtwwwap_curn_user_id = get_current_user_id();
					global $wpdb;
					$rtwwwap_mlm = get_option( 'rtwwwap_mlm_opt' );
					$rtwwwap_mlm_depth = isset( $rtwwwap_mlm[ 'depth' ] ) ? $rtwwwap_mlm[ 'depth' ] : 0;
					$rtwwwap_total_aff_in_org = $this->rtwwwap_loop_each_parent_without_html($rtwwwap_curn_user_id,0,$rtwwwap_mlm_depth,1, $rtwwwap_active=0, $rtwwwap_mlm_child=0 );
					
					$rtwwwap_current_childs = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( `id` ) FROM ".$wpdb->prefix."rtwwwap_mlm WHERE `parent_id` = %d",  $rtwwwap_curn_user_id ) );

					$rank_check1 = false;
					$rank_check2 = false;
					$rank_check3 = false;
					$rank_check4 = false;

					$rtwwwap_rank_detail = get_user_meta($rtwwwap_curn_user_id , 'rank_detail' , true );


					$rtwwwap_rank_priority = isset($rtwwwap_rank_detail[2])? $rtwwwap_rank_detail[2]: 0;

					$rtwwwap_priority = array();

					$rank_requirement_fields = get_option('rtwwwap_rank_details');

					$rtwwwap_total_aff_rank = $this->rtwwwap_loop_each_parent_all_aff_rank($rtwwwap_curn_user_id,$arr=array(),$rtwwwap_mlm_depth,1, $rtwwwap_active=0, $rtwwwap_mlm_child=0 );

					$null_arr = array();
					if(!empty($rtwwwap_total_aff_rank)){
						foreach($rtwwwap_total_aff_rank as $key=> $val)
						{
							array_push($null_arr, isset($val['curr_rank'][0])?$val['curr_rank'][0]:"");
						}
						$rtwwap_aff_reach_rnk = array_count_values($null_arr);
					}

					$rank_id =0;
					if(!empty($rank_requirement_fields))
					{
						foreach($rank_requirement_fields as $option_name => $option_val ){

							$rank_details = array();

							array_push($rank_details,$option_val['rank_name'], $option_val['rank_desc'], $option_val['rank_priority'], $option_val['rank_commission']);

							foreach($option_val['rank_requirement'] as $key => $value){

								if(isset($value['optionField'])){

									if(count($option_val['rank_requirement']) == 1 ){

										if( $value['optionField'] ==1 ){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check4 = true;
										}
										
										if(isset($value['personalAff']) && $value['optionField'] ==2 && $value['personalAff']<= $rtwwwap_current_childs ){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check1 = true;
										}
										if(isset($value['totalAff']) && $value['optionField'] ==3 && $value['totalAff']<= $rtwwwap_total_aff_in_org ){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check2 = true;
										}
										if($value['optionField'] ==4){
											foreach($rtwwap_aff_reach_rnk as $rank_key => $rank_val){
												
												if(isset($value['rankName']) && isset($value['reachRankAff']) && $value['reachRankAff'] == $rank_val && $value['rankName'] == $rank_key){
													array_push($rtwwwap_priority,$option_val['rank_priority']);
													$rank_check3 = true;
												}
											}
										}
				
										if($rank_check2 == true || $rank_check1 == true || $rank_check3 == true || $rank_check4 == true){
											$highest_rank = max($rtwwwap_priority);
											if($highest_rank > $rtwwwap_rank_priority){
												$rank_id = $option_val['rank_priority'];
												update_user_meta($rtwwwap_curn_user_id,'rank_detail', $rank_details);
											}
										}
										else{
											update_user_meta($rtwwwap_curn_user_id,'rank_detail', null);
										}
									}

									if(count($option_val['rank_requirement']) == 2 ){
											if(isset($value['optionField']) && $value['optionField'] == 1){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check1 = true;
										}

										if(isset($value['personalAff']) && $value['optionField'] ==2 && $value['personalAff']<= $rtwwwap_current_childs ){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check2 = true;
										}
										if(isset($value['totalAff']) && $value['optionField'] ==3 && $value['totalAff']<= $rtwwwap_total_aff_in_org ){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check3 = true;
										}
										if($value['optionField'] ==4){
											foreach($rtwwap_aff_reach_rnk as $rank_key => $rank_val){
												
												if(isset($value['rankName']) && isset($value['reachRankAff']) && $value['reachRankAff'] == $rank_val && $value['rankName'] == $rank_key){
													array_push($rtwwwap_priority,$option_val['rank_priority']);
													$rank_check4 = true;
												}
											}
										}
				
										if(($rank_check1 == true && $rank_check2 == true) || ($rank_check1 == true && $rank_check3 == true) || ($rank_check1 == true && $rank_check4 == true) || ($rank_check2 == true && $rank_check3 == true)|| ($rank_check2 == true && $rank_check4 == true)|| ($rank_check3 == true && $rank_check4 == true)){
											$highest_rank = max($rtwwwap_priority);
											if($highest_rank > $rtwwwap_rank_priority){
												$rank_id = $option_val['rank_priority'];
												update_user_meta($rtwwwap_curn_user_id,'rank_detail', $rank_details);
											}
										}
										
									}
									if(count($option_val['rank_requirement']) == 3 ){
										
										if(isset($value['optionField']) && $value['optionField'] == 1){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check1 = true;
										}

										if(isset($value['personalAff']) && $value['optionField'] ==2 && $value['personalAff']<= $rtwwwap_current_childs ){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check2 = true;
										}
				
										if(isset($value['totalAff']) && $value['optionField'] ==3 && $value['totalAff']<= $rtwwwap_total_aff_in_org ){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check3 = true;
										}
										if($value['optionField'] ==4){
											foreach($rtwwap_aff_reach_rnk as $rank_key => $rank_val){
												if(isset($value['rankName']) && $value['rankName'] == $rank_val && $value['rankName'] == $rank_key){
													array_push($rtwwwap_priority,$option_val['rank_priority']);
													$rank_check4 = true;
												}
											}
										}
				
										if(($rank_check1 == true && $rank_check2 == true && $rank_check3 == true) || ($rank_check2 == true && $rank_check3 == true && $rank_check4 == true) || ($rank_check1 == true && $rank_check2 == true && $rank_check4 == true) || ($rank_check1 == true && $rank_check3 == true && $rank_check4 == true)){
											$highest_rank = max($rtwwwap_priority);
											if($highest_rank > $rtwwwap_rank_priority){
												$rank_id = $option_val['rank_priority'];
												update_user_meta($rtwwwap_curn_user_id,'rank_detail', $rank_details);
											}
										}
		
									}
									if(count($option_val['rank_requirement']) == 4 ){

										if(isset($value['optionField']) && $value['optionField'] == 1){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check1 = true;
										}

										if(isset($value['personalAff']) && $value['optionField'] ==2 && $value['personalAff']<= $rtwwwap_current_childs ){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check2 = true;
										}
				
										if(isset($value['totalAff']) && $value['optionField'] ==3 && $value['totalAff']<= $rtwwwap_total_aff_in_org ){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check3 = true;
										}
										if($value['optionField'] ==4){
											foreach($rtwwap_aff_reach_rnk as $rank_key => $rank_val){
												if(isset($value['rankName']) && $value['rankName'] == $rank_val && $value['rankName'] == $rank_key){
													array_push($rtwwwap_priority,$option_val['rank_priority']);
													$rank_check4 = true;
												}
											}
										}
				
										if($rank_check1 == true && $rank_check2 == true && $rank_check3 == true && $rank_check4 == true){
											$highest_rank = max($rtwwwap_priority);
											if($highest_rank > $rtwwwap_rank_priority){
												$rank_id = $option_val['rank_priority'];
												update_user_meta($rtwwwap_curn_user_id,'rank_detail', $rank_details);
											}
										}
									
				
									}
								}
								
							}
							$rank_check1 = false;
							$rank_check2 = false;
							$rank_check3 = false;
							$rank_check4 = false;
						}
					}
					$rtwwwap_rank_details1 = get_user_meta($rtwwwap_curn_user_id , 'rank_detail' , true );

					$rtwwwap_rank_commision =  isset($rtwwwap_rank_details1[3])? $rtwwwap_rank_details1[3]: "";
					$rtwwwap_currency = get_woocommerce_currency();

					if( ($rank_id > $rtwwwap_rank_priority) && $rtwwwap_rank_details1 ){
						$rtwwwap_updated = $wpdb->insert(
							$wpdb->prefix.'rtwwwap_referrals',
							array(
							'aff_id' => $rtwwwap_curn_user_id,
							'type' => 15,
							'order_id' => "",
							'date' => date( 'Y-m-d H:i:s' ),
							'status' => 0,
							'amount' => $rtwwwap_rank_commision,
							'capped' => "",
							'currency' => $rtwwwap_currency,
							'product_details' => "",
							)
						);
					}

					$rank_name = isset($rtwwwap_rank_details1[0])? $rtwwwap_rank_details1[0]: "";
					
					$current_user = wp_get_current_user();
				
						$rtwwwap_html .=     '<div class="rtwwwap_page_title_area">
								<div class="rtwwwap_row">
									<div class="rtwwwap_left_side_content rtwwwap_col-6">
										<div class="rtwwwap_menu_item_name"><h4>'.$rtwwwap_tab_heading.'</h4>
										</div>
									</div>
								<div class="rtwwwap_right_side_content rtwwwap_col-6">
								<div class="rtwwwap_user_profile">
									<img src="'.$image_url.'" class="rtwwwap_logged_user_img">
									<div class="rtwwwap_user_rank_wrapper">
									<h4 class="rtwwwap_username rtwwwap_dropdown-toggle">'.esc_html($current_user->user_nicename,'rtwwwap-wp-wc-affiliate-program').'<i class="fa fa-angle-down"></i></h4>';
									if($rtwwwap_curn_user_id != 1 && $rank_name){
										$rtwwwap_html .=  '<div>
										<span class="rtwwwap_affiliate_rank">'.esc_html($rank_name,'rtwwwap-wp-wc-affiliate-program').'</span>
									</div>';
									}
								$rtwwwap_html .=  '</div>
							
								<div class="rtwwwap_dropdown_content">
								<div class="rtwwwap_logout">';
								if(!empty($rtwwwap_noti_option) )
								{
											$rtwwwap_html .= 	 '<span class="rtwwwap_toggle_notification">
																		<i class="fas fa-bell"></i>';
											if($rtwwwap_final_count_show > 0)
											{							
												$rtwwwap_html .= 	 '<span class="rtwwwap_message_count">'.esc_attr($rtwwwap_final_count_show).'</span>';
											}
											$rtwwwap_html .= 	 '</span>';
								}
					$rtwwwap_html .=  		 '<a class="rtwwwap_logout_button" href='.wp_logout_url($redirect_url).'>
												<i class="fas fa-sign-out-alt"></i>
												<span class="rtwwwap_logout_text">logout</span></a>';
					$rtwwwap_html .=     '</div></div></div></div></div></div>';
					$rtwwwap_html .= 	'<div class="rtwwwwap_template_content">';
					$rtwwwap_html .=			$rtwwwap_html1;
					$rtwwwap_html .=		'</div></div>';
					$rtwwwap_html .=	'</div>';
					$rtwwwap_html .=	'</div>';

				}
				
				if(	$rtwwwap_affilaite_template == 3 && $rtwwwap_affilaite_template != ' ')
				{
				
					$rtwwwap_html1 = include( RTWWWAP_DIR.'public/templates/rtwwwap_affiliate_body_temp_3.php' ); 	
					$rtwwwap_mlm = get_option( 'rtwwwap_mlm_opt' );

					$rtwwwap_overview_label = isset($rtwwwap_extra_features['affiliate_dash_overview']) && !empty($rtwwwap_extra_features['affiliate_dash_overview']) ? $rtwwwap_extra_features['affiliate_dash_overview'] : 'Overview';
					$rtwwwap_commission_label = isset($rtwwwap_extra_features['affiliate_dash_commission']) && !empty($rtwwwap_extra_features['affiliate_dash_commission']) ? $rtwwwap_extra_features['affiliate_dash_commission'] : 'Commissions';
					$rtwwwap_tools_label = isset($rtwwwap_extra_features['affiliate_dash_tools']) && !empty($rtwwwap_extra_features['affiliate_dash_tools']) ? $rtwwwap_extra_features['affiliate_dash_tools'] : 'Affilate Tools';
					$rtwwwap_download_label = isset($rtwwwap_extra_features['affiliate_dash_download']) && !empty($rtwwwap_extra_features['affiliate_dash_download']) ? $rtwwwap_extra_features['affiliate_dash_download'] : 'Download';
					$rtwwwap_payout_label = isset($rtwwwap_extra_features['affiliate_dash_payout']) && !empty($rtwwwap_extra_features['affiliate_dash_payout']) ? $rtwwwap_extra_features['affiliate_dash_payout'] : 'Payout';
					$rtwwwap_profile_label = isset($rtwwwap_extra_features['affiliate_dash_profile']) && !empty($rtwwwap_extra_features['affiliate_dash_profile']) ? $rtwwwap_extra_features['affiliate_dash_profile'] : 'Profile';
					$rtwwwap_custom_banner_label = isset($rtwwwap_extra_features['affiliate_dash_custom_banner']) && !empty($rtwwwap_extra_features['affiliate_dash_custom_banner']) ? $rtwwwap_extra_features['affiliate_dash_custom_banner'] : 'Custom Banner';
					$rtwwwap_mlm_label = isset($rtwwwap_extra_features['affiliate_dash_MLM']) && !empty($rtwwwap_extra_features['affiliate_dash_MLM']) ? $rtwwwap_extra_features['affiliate_dash_MLM'] : 'MLM';


					// custom code check 
					global $wpdb;
					$rtwwwap_curn_user_id = get_current_user_id();
					$current_user = wp_get_current_user();

					$rtwwwap_mlm = get_option( 'rtwwwap_mlm_opt' );
					$rtwwwap_mlm_depth = isset( $rtwwwap_mlm[ 'depth' ] ) ? $rtwwwap_mlm[ 'depth' ] : 0;
					$rtwwwap_total_aff_in_org = $this->rtwwwap_loop_each_parent_without_html($rtwwwap_curn_user_id,0,$rtwwwap_mlm_depth,1, $rtwwwap_active=0, $rtwwwap_mlm_child=0 );
					
					$rtwwwap_current_childs = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( `id` ) FROM ".$wpdb->prefix."rtwwwap_mlm WHERE `parent_id` = %d",  $rtwwwap_curn_user_id ) );

					$rank_check1 = false;
					$rank_check2 = false;
					$rank_check3 = false;
					$rank_check4 = false;

					$rtwwwap_rank_detail = get_user_meta($rtwwwap_curn_user_id , 'rank_detail' , true );

					$rtwwwap_rank_priority = isset($rtwwwap_rank_detail[2])? $rtwwwap_rank_detail[2]: 0;

					$rtwwwap_priority = array();

					$rank_requirement_fields = get_option('rtwwwap_rank_details');

					$rtwwwap_total_aff_rank = $this->rtwwwap_loop_each_parent_all_aff_rank($rtwwwap_curn_user_id,$arr=array(),$rtwwwap_mlm_depth,1, $rtwwwap_active=0, $rtwwwap_mlm_child=0 );

					$null_arr = array();
					if(!empty($rtwwwap_total_aff_rank)){
						foreach($rtwwwap_total_aff_rank as $key=> $val)
						{
							array_push($null_arr, isset($val['curr_rank'][0])?$val['curr_rank'][0]:"");
						}
						$rtwwap_aff_reach_rnk = array_count_values($null_arr);
					}
					

					$rank_id =0;
					if(!empty($rank_requirement_fields))
					{
						foreach($rank_requirement_fields as $option_name => $option_val ){

							$rank_details = array();

							array_push($rank_details,$option_val['rank_name'], $option_val['rank_desc'], $option_val['rank_priority'], $option_val['rank_commission']);

							foreach($option_val['rank_requirement'] as $key => $value){

								if(isset($value['optionField'])){

									if(count($option_val['rank_requirement']) == 1 ){
										if( $value['optionField'] ==1 ){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check4 = true;
										}
										if(isset($value['personalAff']) && $value['optionField'] ==2 && $value['personalAff']<= $rtwwwap_current_childs ){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check1 = true;
										}
										if(isset($value['totalAff']) && $value['optionField'] ==3 && $value['totalAff']<= $rtwwwap_total_aff_in_org ){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check2 = true;
										}
										if($value['optionField'] ==4){
											foreach($rtwwap_aff_reach_rnk as $rank_key => $rank_val){
												
												if(isset($value['rankName']) && isset($value['reachRankAff']) && $value['reachRankAff'] == $rank_val && $value['rankName'] == $rank_key){
													array_push($rtwwwap_priority,$option_val['rank_priority']);
													$rank_check3 = true;
												}
											}
										}
				
										if($rank_check2 == true || $rank_check1 == true || $rank_check3 == true || $rank_check4 == true){
											$highest_rank = max($rtwwwap_priority);
											if($highest_rank > $rtwwwap_rank_priority){
												$rank_id = $option_val['rank_priority'];
												update_user_meta($rtwwwap_curn_user_id,'rank_detail', $rank_details);
											}
										}
										else{
											update_user_meta($rtwwwap_curn_user_id,'rank_detail', null);
										}
									}

									if(count($option_val['rank_requirement']) == 2 ){

										if(isset($value['optionField']) && $value['optionField'] == 1){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check1 = true;
										}

										if(isset($value['personalAff']) && $value['optionField'] ==2 && $value['personalAff']<= $rtwwwap_current_childs ){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check2 = true;
										}
										if(isset($value['totalAff']) && $value['optionField'] ==3 && $value['totalAff']<= $rtwwwap_total_aff_in_org ){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check3 = true;
										}
										if($value['optionField'] ==4){
											foreach($rtwwap_aff_reach_rnk as $rank_key => $rank_val){
												
												if(isset($value['rankName']) && isset($value['reachRankAff']) && $value['reachRankAff'] == $rank_val && $value['rankName'] == $rank_key){
													array_push($rtwwwap_priority,$option_val['rank_priority']);
													$rank_check4 = true;
												}
											}
										}
				
										if(($rank_check1 == true && $rank_check2 == true) || ($rank_check1 == true && $rank_check3 == true) || ($rank_check1 == true && $rank_check4 == true) || ($rank_check2 == true && $rank_check3 == true)|| ($rank_check2 == true && $rank_check4 == true)|| ($rank_check3 == true && $rank_check4 == true)){
											$highest_rank = max($rtwwwap_priority);
											if($highest_rank > $rtwwwap_rank_priority){
												$rank_id = $option_val['rank_priority'];
												update_user_meta($rtwwwap_curn_user_id,'rank_detail', $rank_details);
											}
										}
								
									}
									if(count($option_val['rank_requirement']) == 3 ){

										if(isset($value['optionField']) && $value['optionField'] == 1){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check1 = true;
										}

										if(isset($value['personalAff']) && $value['optionField'] ==2 && $value['personalAff']<= $rtwwwap_current_childs ){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check2 = true;
										}
				
										if(isset($value['totalAff']) && $value['optionField'] ==3 && $value['totalAff']<= $rtwwwap_total_aff_in_org ){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check3 = true;
										}
										if($value['optionField'] ==4){
											foreach($rtwwap_aff_reach_rnk as $rank_key => $rank_val){
												if(isset($value['rankName']) && $value['rankName'] == $rank_val && $value['rankName'] == $rank_key){
													array_push($rtwwwap_priority,$option_val['rank_priority']);
													$rank_check4 = true;
												}
											}
										}
				
										if(($rank_check1 == true && $rank_check2 == true && $rank_check3 == true) || ($rank_check2 == true && $rank_check3 == true && $rank_check4 == true) || ($rank_check1 == true && $rank_check2 == true && $rank_check4 == true) || ($rank_check1 == true && $rank_check3 == true && $rank_check4 == true)){
											$highest_rank = max($rtwwwap_priority);
											if($highest_rank > $rtwwwap_rank_priority){
												$rank_id = $option_val['rank_priority'];
												update_user_meta($rtwwwap_curn_user_id,'rank_detail', $rank_details);
											}
										}								
				
									}
									if(count($option_val['rank_requirement']) == 4 ){

										if(isset($value['optionField']) && $value['optionField'] == 1){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check1 = true;
										}

										if(isset($value['personalAff']) && $value['optionField'] ==2 && $value['personalAff']<= $rtwwwap_current_childs ){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check2 = true;
										}
				
										if(isset($value['totalAff']) && $value['optionField'] ==3 && $value['totalAff']<= $rtwwwap_total_aff_in_org ){
											array_push($rtwwwap_priority, $option_val['rank_priority']);
											$rank_check3 = true;
										}
										if($value['optionField'] ==4){
											foreach($rtwwap_aff_reach_rnk as $rank_key => $rank_val){
												if(isset($value['rankName']) && $value['rankName'] == $rank_val && $value['rankName'] == $rank_key){
													array_push($rtwwwap_priority,$option_val['rank_priority']);
													$rank_check4 = true;
												}
											}
										}
				
										if($rank_check1 == true && $rank_check2 == true && $rank_check3 == true && $rank_check4 == true){
											$highest_rank = max($rtwwwap_priority);
											if($highest_rank > $rtwwwap_rank_priority){
												$rank_id = $option_val['rank_priority'];
												update_user_meta($rtwwwap_curn_user_id,'rank_detail', $rank_details);
											}
										}
										
				
									}
								}
								
							}
							$rank_check1 = false;
							$rank_check2 = false;
							$rank_check3 = false;
							$rank_check4 = false;
						}
					}

					$rtwwwap_rank_details1 = get_user_meta($rtwwwap_curn_user_id , 'rank_detail' , true );
					$rank_name = isset($rtwwwap_rank_details1[0])? $rtwwwap_rank_details1[0]: "";

					$rtwwwap_html .=   '
					<div class="rtwwwap-body">
							<div class="rtwwwwap_header">
								<div class="rtwwwap-header">
									<div class="rtwwwap-toggle">
										<i class="fas fa-bars"></i>
									</div>
									<label class="switch">
										<input type="checkbox" checked>
										<span class="slider round"></span>
									</label>
								</div>
							</div>
					
							<aside class="mdc-drawer rtwwwap_sidebar_wrapper">
								<div class="rtwwwap-close">
									<i class="fas fa-times"></i>
								</div>
								<div class="mdc-drawer__header rtwwwap_logo">
									<h4 class="mdc-drawer__title rtwwwap-title">'.esc_html( ' Hello '.$current_user->user_nicename,'rtwwwap-wp-wc-affiliate-program').'</h4>
									<a  href="#"><img src="'.esc_url( get_avatar_url(get_current_user_id()) ).'"  alt="image"></a>';

									if($rtwwwap_curn_user_id != 1 && $rank_name){
										$rtwwwap_html .= '<div class="rtwwwap_rank_name">'.esc_html( $rank_name,'rtwwwap-wp-wc-affiliate-program').'</div>';
									}

							$rtwwwap_html .='</div>
								<div class="mdc-drawer__content " id="rtwwwap_drawer_content">
								<nav class="mdc-list rtwwwap-navbar">
								<a class="mdc-list-item rtwwwap-navbar-active rtwwwap_tab" data-tab="rtwwwap-overview-wrapper" href="#rtwwwap-overview-wrapper" aria-current="page" >
										<span class="mdc-list-item__ripple"></span>
										<i class="material-icons mdc-list-item__graphic" aria-hidden="true">visibility</i>
										<span class="mdc-list-item__text">'.$rtwwwap_overview_label.'</span>
									</a>
									<a class="mdc-list-item rtwwwap-open-dropdown " >
										<span class="mdc-list-item__ripple"></span>
										<i class="material-icons mdc-list-item__graphic" aria-hidden="true">send</i>
										<span class="mdc-list-item__text">'.$rtwwwap_commission_label.'</span>
										<i class="fas fa-chevron-down rtwwwap_arrow_down"></i>
										<i class="fas fa-chevron-up rtwwwap-arrow-hide rtwwwap_arrow_up"></i>
									</a>
									<ul class="mdc-list rtwwwap-banner-submenu" id="rtwwwap_commission_tab" >
										<li class="" > <a class="mdc-list-item rtwwwap_tab"  href="#rtwwwap_commission_table" data-tab="rtwwwap_commission_table">
												<span class="mdc-list-item__ripple"></span>
												<i class="material-icons mdc-list-item__graphic" aria-hidden="true">view_carousel</i>
												<span class="mdc-list-item__text">'.esc_html__('Commission Table', 'rtwwwap-wp-wc-affiliate-program' ).'</span>
											</a>
										</li>
										<li class=""  > <a class="mdc-list-item rtwwwap_tab" data-tab="rtwwwap_referral_table" href="#rtwwwap_referral_table">
												<span class="mdc-list-item__ripple"></span>
												<i class="material-icons mdc-list-item__graphic" aria-hidden="true">view_carousel</i>
												<span class="mdc-list-item__text"> '.esc_html__('Refferal Table', 'rtwwwap-wp-wc-affiliate-program' ).'</span>
											</a>
										</li>
									</ul>
									<a class="mdc-list-item rtwwwap-open-dropdown "  >
										<span class="mdc-list-item__ripple"></span>
										<i class="material-icons mdc-list-item__graphic" aria-hidden="true">build</i>
										<span class="mdc-list-item__text">'.$rtwwwap_tools_label.'</span>
										<i class="fas fa-chevron-down rtwwwap_arrow_down"></i>
										<i class="fas fa-chevron-up rtwwwap-arrow-hide rtwwwap_arrow_up"></i>
									</a>
									<ul class="mdc-list rtwwwap-banner-submenu" id="rtwwwap_affiliate_tool_tab">
										<li class="" > ';
										$rtwwwap_commission_settings = get_option( 'rtwwwap_commission_settings_opt' );
										$rtwwwap_is_coupon_activated = isset( $rtwwwap_commission_settings[ 'coupons' ] ) ? $rtwwwap_commission_settings[ 'coupons' ] : 0;
											if($rtwwwap_is_coupon_activated)
											{
												$rtwwwap_html .=   '	<a class="mdc-list-item rtwwwap_tab" data-tab="rtwwwap_coupon" href="#rtwwwap_coupon">
												<span class="mdc-list-item__ripple"></span>
												<i class="material-icons mdc-list-item__graphic" aria-hidden="true">view_carousel</i>
												<span class="mdc-list-item__text">'.esc_html__('Coupon Generate', 'rtwwwap-wp-wc-affiliate-program' ).'</span>
											</a>';}
											$rtwwwap_html .=   '				</li>
										<li class="" > <a class="mdc-list-item rtwwwap_tab" href="#rtwwwap_generate_link" data-tab="rtwwwap_generate_link">
												<span class="mdc-list-item__ripple"></span>
												<i class="material-icons mdc-list-item__graphic" aria-hidden="true">view_carousel</i>
												<span class="mdc-list-item__text">'.esc_html__('Generate Link', 'rtwwwap-wp-wc-affiliate-program' ).' </span>
											</a>
										</li>
									</ul>
									<a class="mdc-list-item rtwwwap_tab" data-tab="rtwwwap_report_section" href="#rtwwwap_report_section">
										<span class="mdc-list-item__ripple"></span>
										<i class="material-icons mdc-list-item__graphic" aria-hidden="true">drafts</i>
										<span class="mdc-list-item__text">'.esc_html__('Report', 'rtwwwap-wp-wc-affiliate-program' ).'</span>
									</a>
									<a class="mdc-list-item rtwwwap_tab" data-tab="rtwwwap_download_tab"  href="#rtwwwap_download_tab" >
										<span class="mdc-list-item__ripple"></span>
										<i class="material-icons mdc-list-item__graphic" aria-hidden="true">cloud_download</i>
										<span class="mdc-list-item__text">'.$rtwwwap_download_label.'</span>
									</a>
									<a class="mdc-list-item rtwwwap_tab"  data-tab="rtwwwap_payout_tab" href="#rtwwwap_payout_tab">
										<span class="mdc-list-item__ripple"></span>
										<i class="material-icons mdc-list-item__graphic" aria-hidden="true">account_balance_wallet</i>
										<span class="mdc-list-item__text">'.$rtwwwap_payout_label.'</span>
									</a>
									<a class="mdc-list-item rtwwwap_tab" data-tab="rtwwwap_profile_tab"  href="#rtwwwap_profile_tab">
										<span class="mdc-list-item__ripple"></span>
										<i class="material-icons mdc-list-item__graphic" aria-hidden="true">face</i>
										<span class="mdc-list-item__text">'.$rtwwwap_profile_label.'</span>
									</a>';
								
									$rtwwwap_html .= '	<a class="mdc-list-item rtwwwap-open-dropdown " >
										<span class="mdc-list-item__ripple"></span>
										<i class="material-icons mdc-list-item__graphic" aria-hidden="true">view_carousel</i>
										<span class="mdc-list-item__text">'.esc_html__('Banner', 'rtwwwap-wp-wc-affiliate-program' ).'</span>
										<i class="fas fa-chevron-down rtwwwap_arrow_down"></i>
										<i class="fas fa-chevron-up rtwwwap-arrow-hide rtwwwap_arrow_up"></i>
									</a>
									<ul class="mdc-list rtwwwap-banner-submenu">
									';
									if(isset($rtwwwap_custom_banner ) && !empty($rtwwwap_custom_banner))
									{
									$rtwwwap_html .= '
										<li class="" > <a class="mdc-list-item rtwwwap_tab"  href="#rtwwwap_custom_banner_tab" data-tab="rtwwwap_custom_banner_tab">
												<span class="mdc-list-item__ripple"></span>
												<i class="material-icons mdc-list-item__graphic" aria-hidden="true">view_carousel</i>
												<span class="mdc-list-item__text">'.$rtwwwap_custom_banner_label.'r</span>
												</a>
										</li>';
									}
								
									$rtwwwap_html .= '		<li class="" > <a class="mdc-list-item rtwwwap_tab" href="#rtwwwap_create_banner_tab" data-tab="rtwwwap_create_banner_tab">
											<span class="mdc-list-item__ripple"></span>
											<i class="material-icons mdc-list-item__graphic" aria-hidden="true">view_carousel</i>
											<span class="mdc-list-item__text">'.esc_html__('Create Banner', 'rtwwwap-wp-wc-affiliate-program' ).'</span>
											</a>
										</li>
										
									</ul>';
					if( isset( $rtwwwap_mlm[ 'activate' ] ) && $rtwwwap_mlm[ 'activate' ] == 1 ){
									
						$rtwwwap_html .= '<a class="mdc-list-item rtwwwap_tab" data-tab="rtwwwap_mlm_tab" href="#rtwwwap_mlm_tab">
										<span class="mdc-list-item__ripple"></span>
										<i class="material-icons mdc-list-item__graphic" aria-hidden="true">drafts</i>
										<span class="mdc-list-item__text">'.$rtwwwap_mlm_label.'</span>
									</a>';
					}
								
					$rtwwwap_html .= '		</nav>
							</div>
						</aside>';


					$rtwwwap_html .=		'<div id="rtwwwap_affiliate_body">';
					
					$rtwwwap_rank_commision =  isset($rtwwwap_rank_details1[3])? $rtwwwap_rank_details1[3]: "";
					$rtwwwap_currency = get_woocommerce_currency();

					if( ($rank_id > $rtwwwap_rank_priority) && $rtwwwap_rank_details1 ){
						$rtwwwap_updated = $wpdb->insert(
							$wpdb->prefix.'rtwwwap_referrals',
							array(
							'aff_id' => $rtwwwap_curn_user_id,
							'type' => 15,
							'order_id' => "",
							'date' => date( 'Y-m-d H:i:s' ),
							'status' => 0,
							'amount' => $rtwwwap_rank_commision,
							'capped' => "",
							'currency' => $rtwwwap_currency,
							'product_details' => "",
							)
						);
					}


					$rtwwwap_html .=			$rtwwwap_html1;

					$rtwwwap_html .=		'</div>';

					$rtwwwap_html .= ' </div>';
				}
			}
			$rtwwwap_html .= 	'</div>';
	}
		return $rtwwwap_html;
