<?php
	$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );
	$rtwwwap_decimal_place = isset($rtwwwap_extra_features['decimal_places']) && !empty($rtwwwap_extra_features['decimal_places']) ? $rtwwwap_extra_features['decimal_places'] : 2;
	$rtwwwap_decimal_separator = isset($rtwwwap_extra_features['decimal_separator']) && !empty($rtwwwap_extra_features['decimal_separator'])? $rtwwwap_extra_features['decimal_separator'] : '.';
	$rtwwwap_thousand_separator = isset($rtwwwap_extra_features['thousand__separator']) && !empty($rtwwwap_extra_features['thousand__separator']) ? $rtwwwap_extra_features['thousand__separator'] : ',';

if( RTWWWAP_IS_WOO == 1 ){
	$rtwwwap_currency_sym = esc_html( get_woocommerce_currency_symbol() );
}
else{
	require_once( RTWWWAP_DIR.'includes/rtwaffiliatehelper.php' );

	$rtwwwap_currency		= isset( $rtwwwap_extra_features[ 'currency' ] ) ? $rtwwwap_extra_features[ 'currency' ] : 'USD';
	$rtwwwap_curr_obj 		= new RtwAffiliateHelper();
	$rtwwwap_currency_sym 	= $rtwwwap_curr_obj->rtwwwap_curr_symbol( $rtwwwap_currency );

}

	// overview
if( !isset( $_GET[ 'rtwwwap_tab' ] ) || ( isset( $_GET[ 'rtwwwap_tab' ] ) && $_GET[ 'rtwwwap_tab' ] == 'overview' ) ){
	global $wpdb;

	$rtwwwap_user_id 			= get_current_user_id();
	$rtwwwap_total_referrals 	= $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(`id`) as total_referrals FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `aff_id`=%d", $rtwwwap_user_id ) );
	$rtwwwap_pending_comm 		= $wpdb->get_var( $wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `aff_id`=%d AND `status`=%d AND `capped`!=%d", $rtwwwap_user_id, 0, 1 ) );

	$rtwwwap_pending_comm = isset($rtwwwap_pending_comm) && !empty($rtwwwap_pending_comm)? $rtwwwap_pending_comm : 0;

	$rtwwwap_approved_comm 		= $wpdb->get_var( $wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `aff_id`=%d AND `status`=%d", $rtwwwap_user_id, 1 ) );

	$rtwwwap_approved_comm = isset($rtwwwap_approved_comm) && !empty($rtwwwap_approved_comm)? $rtwwwap_approved_comm : 0;

	$rtwwwap_total_comm 		= $wpdb->get_var( $wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `aff_id`=%d AND `status`=%d", $rtwwwap_user_id, 2 ) );

	$rtwwwap_total_comm = isset($rtwwwap_total_comm) && !empty($rtwwwap_total_comm)? $rtwwwap_total_comm : 0;

	$rtwwwap_total_comm 		= $rtwwwap_total_comm+$rtwwwap_approved_comm;
	$rtwwwap_wallet 			= get_user_meta( $rtwwwap_user_id, 'rtw_user_wallet', true );

	$rtwwwap_wallet   			= isset($rtwwwap_wallet) && $rtwwwap_wallet != ""  ? $rtwwwap_wallet : 0;
 

	$rtwwwap_html1 = '';
	$rtwwwap_html1 .= 	'<div id="rtwwwap_overview">';
	$rtwwwap_html1 .= 		'<div class="rtwwwap-overview-box" id="rtwwwap_total_referral">';
	$rtwwwap_html1 .= 			sprintf( '<span>%u</span> %s', ( $rtwwwap_total_referrals ) ? $rtwwwap_total_referrals : '0', esc_html__( 'Total Referrals', 'rtwwwap-wp-wc-affiliate-program' ) );
	$rtwwwap_html1 .= 		'</div>';
	$rtwwwap_html1 .= 		'<div class="rtwwwap-overview-box" id="rtwwwap_total_commission">';
	$rtwwwap_html1 .= 			sprintf( '<span> '.$rtwwwap_currency_sym.number_format( $rtwwwap_total_comm,$rtwwwap_decimal_place,$rtwwwap_decimal_separator, $rtwwwap_thousand_separator) .'</span> %s', esc_html__( 'Total Commission', 'rtwwwap-wp-wc-affiliate-program' ) );
	$rtwwwap_html1 .= 		'</div>';

	$rtwwwap_html1 .= 		'<div class="rtwwwap-overview-box" id="rtwwwap_wallet">';
	$rtwwwap_html1 .= 			sprintf( '<span>'.$rtwwwap_currency_sym.number_format($rtwwwap_wallet,$rtwwwap_decimal_place,$rtwwwap_decimal_separator, $rtwwwap_thousand_separator).'</span> %s', esc_html__( 'Wallet', 'rtwwwap-wp-wc-affiliate-program' ) );
	$rtwwwap_html1 .= 		'</div>';

	$rtwwwap_html1 .= 		'<div class="rtwwwap-overview-box" id="rtwwwap_approved_commission">';
	$rtwwwap_html1 .= 			sprintf( '<span>'.$rtwwwap_currency_sym.number_format( $rtwwwap_approved_comm,$rtwwwap_decimal_place,$rtwwwap_decimal_separator, $rtwwwap_thousand_separator).'</span> %s', esc_html__( 'Approved Commission', 'rtwwwap-wp-wc-affiliate-program' ) );
	$rtwwwap_html1 .= 		'</div>';

	$rtwwwap_html1 .= 		'<div class="rtwwwap-overview-box" id="rtwwwap_pending_commission">';
	$rtwwwap_html1 .= 			sprintf( '<span>'.$rtwwwap_currency_sym.number_format( $rtwwwap_pending_comm,$rtwwwap_decimal_place,$rtwwwap_decimal_separator, $rtwwwap_thousand_separator).'</span> %s', esc_html__( 'Pending Commission', 'rtwwwap-wp-wc-affiliate-program' ) );
	$rtwwwap_html1 .= 		'</div>';
	
	$rtwwwap_custom_html = "";
	$rtwwwap_html1 .= apply_filters('rtwwwap_add_box_overview_tab',$rtwwwap_custom_html);	
	$rtwwwap_html1 .= 	'</div>';

		//referrals
	if( $rtwwwap_total_referrals ){
		$rtwwwap_date_format = get_option( 'date_format' );
		$rtwwwap_time_format = get_option( 'time_format' );
		$rtwwwap_user_all_referrals = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `aff_id` = %d ORDER BY `date` DESC", $rtwwwap_user_id ), ARRAY_A );

		$rtwwwap_html1 .= 	'<div  id="rtwwwap_create_coupon_container">';
		$rtwwwap_html1 .= 		'<p class="rtwwwap_create_coupon_text">';
		$rtwwwap_html1 .= 			sprintf( '%s', esc_html__( 'Referrals', 'rtwwwap-wp-wc-affiliate-program' ) );
		$rtwwwap_html1 .= 		'</p>';
		$rtwwwap_html1 .= 	'</div>';

		$rtwwwap_html1 .= 	'<table id="rtwwwap_referrals_table">';
		$rtwwwap_html1 .= 		'<thead>';
		$rtwwwap_html1 .= 			'<tr>';
		$rtwwwap_html1 .= 				'<th>';
		$rtwwwap_html1 .= 					sprintf( '%s', esc_html__( 'Type', 'rtwwwap-wp-wc-affiliate-program' ) );
		$rtwwwap_html1 .= 				'</th>';
		$rtwwwap_html1 .= 				'<th>';
		$rtwwwap_html1 .= 					sprintf( '%s (%s)', esc_html__( 'Amount', 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_currency_sym );
		$rtwwwap_html1 .= 				'</th>';
		$rtwwwap_html1 .= 				'<th>';
		$rtwwwap_html1 .= 					sprintf( '%s', esc_html__( 'Date', 'rtwwwap-wp-wc-affiliate-program' ) );
		$rtwwwap_html1 .= 				'</th>';
		$rtwwwap_html1 .= 				'<th >';
		$rtwwwap_html1 .= 					sprintf( '%s', esc_html__( 'Status', 'rtwwwap-wp-wc-affiliate-program' ) );
		$rtwwwap_html1 .= 				'</th >';
		$rtwwwap_html1 .= 			'</tr>';
		$rtwwwap_html1 .= 		'</thead>';
		$rtwwwap_html1 .= 		'<tbody>';
		foreach( $rtwwwap_user_all_referrals as $rtwwwap_user_ref_key => $rtwwwap_user_ref_value ){
				
			$rtwwwap_html1 .= 		'<tr>';
			$rtwwwap_html1 .= 			'<td>';
			if( $rtwwwap_user_ref_value[ 'type' ] == 1 ){
				$rtwwwap_html1 .=				sprintf( '%s', esc_html__( 'Signup Bonus', 'rtwwwap-wp-wc-affiliate-program' ) );
			}
			elseif( $rtwwwap_user_ref_value[ 'type' ] == 2 ){
				$rtwwwap_html1 .=				sprintf( '%s', esc_html__( 'Performance Bonus', 'rtwwwap-wp-wc-affiliate-program' ) );
			}
			elseif( $rtwwwap_user_ref_value[ 'type' ] == 0 ){
				$rtwwwap_html1 .=				sprintf( '%s', esc_html__( 'Referral Bonus', 'rtwwwap-wp-wc-affiliate-program' ) );
			}
			elseif( $rtwwwap_user_ref_value[ 'type' ] == 4 ){
				$rtwwwap_html1 .=				sprintf( '%s', esc_html__( 'MLM Bonus', 'rtwwwap-wp-wc-affiliate-program' ) );
			}
			elseif( $rtwwwap_user_ref_value[ 'type' ] == 5 ){
				$rtwwwap_html1 .=				sprintf( '%s', esc_html__( 'Sharing Bonus', 'rtwwwap-wp-wc-affiliate-program' ) );
			}
			elseif( $rtwwwap_user_ref_value[ 'type' ] == 6 ){
				$rtwwwap_html1 .=				sprintf( '%s', esc_html__( 'Manual Referral', 'rtwwwap-wp-wc-affiliate-program' ) );
			}
			elseif( $rtwwwap_user_ref_value[ 'type' ] == 15 ){
				$rtwwwap_html1 .=				sprintf( '%s', esc_html__( 'Rank Bonus', 'rtwwwap-wp-wc-affiliate-program' ) );
			}
			$rtwwwap_html1 .= 			'</td>';
			$rtwwwap_html1 .= 			'<td>';

			if( RTWWWAP_IS_WOO == 1 ){
				$rtwwwap_html1 .= 			esc_html( get_woocommerce_currency_symbol( $rtwwwap_user_ref_value[ 'currency' ] ).number_format( $rtwwwap_user_ref_value[ 'amount' ],$rtwwwap_decimal_place,$rtwwwap_decimal_separator, $rtwwwap_thousand_separator ) );
			}
			else{
				$rtwwwap_html1 .= 			esc_html( $rtwwwap_curr_obj->rtwwwap_curr_symbol( $rtwwwap_user_ref_value[ 'currency' ] ).number_format( $rtwwwap_user_ref_value[ 'amount' ],$rtwwwap_decimal_place,$rtwwwap_decimal_separator, $rtwwwap_thousand_separator ) );
			}
			$rtwwwap_html1 .= 			'</td>';
			$rtwwwap_html1 .= 			'<td>';

			$rtwwwap_date_time_format = $rtwwwap_date_format.' '.$rtwwwap_time_format;
			$rtwwwap_local_date = get_date_from_gmt( date( 'Y-m-d H:i:s', strtotime( $rtwwwap_user_ref_value[ 'date' ] ) ), $rtwwwap_date_time_format );

			$rtwwwap_html1 .= 				esc_html( $rtwwwap_local_date );
			$rtwwwap_html1 .= 			'</td >';
			$rtwwwap_html1 .= 			'<td >';
			$rtwwwap_html1 .= 					'<div >';
			if( $rtwwwap_user_ref_value[ 'capped' ] == '0' ){
				if( in_array( $rtwwwap_user_ref_value[ 'status' ], array( '0', '1' ) ) ){
					if( $rtwwwap_user_ref_value[ 'status' ] == 0 ){
						$rtwwwap_html1 .=					sprintf( '%s', esc_html__( 'Pending', 'rtwwwap-wp-wc-affiliate-program' ) );
					}
					elseif( $rtwwwap_user_ref_value[ 'status' ] == 1 ){
						$rtwwwap_html1 .=					sprintf( '%s', esc_html__( 'Approved', 'rtwwwap-wp-wc-affiliate-program' ) );
					}
				}
				elseif( $rtwwwap_user_ref_value[ 'status' ] == 2 ){
					$rtwwwap_html1 .=					sprintf( '%s', esc_html__( 'Paid', 'rtwwwap-wp-wc-affiliate-program' ) );
				}
				elseif( $rtwwwap_user_ref_value[ 'status' ] == 3 ){
					$rtwwwap_html1 .=					sprintf( '%s', esc_html__( 'Rejected', 'rtwwwap-wp-wc-affiliate-program' ) );
					if($rtwwwap_user_ref_value[ 'message' ] != ''){

						$rtwwwap_html1 .= '<p><a class="rtwwwap_view_reject" href="javascript:void(0);">'.esc_html__( 'View Reason', 'rtwwwap-wp-wc-affiliate-program' ).'</a> </p>';
														$rtwwwap_html1 .= '
														<div class="rtwwwap_reason_modal_">
															<div class="rtwwwap_modal_dialog">
																<div class="rtwwwap_modal_header">
																	<h3>'.esc_html__( 'Reason Entered by Admin', 'rtwwwap-wp-wc-affiliate-program' ).'</h3>
																	<div class="rtwwwap_modal_close">
																		<i class="fas fa-times"></i>
																	</div>
																</div>
																<div class="rtwwap_modal_body">
																	'.$rtwwwap_user_ref_value[ 'message' ].'
																</div>
																<div>
															</div>
														</div>';
						

					};								
				}
			}
			
			else{
				$rtwwwap_html1 .=					sprintf( '%s', esc_html__( 'Capped', 'rtwwwap-wp-wc-affiliate-program' ) );
			}
			$rtwwwap_html1 .=        		'</div>';
			$rtwwwap_html1 .= 			'</td>';
			$rtwwwap_html1 .= 		'</tr>';
			
		}
		
		$rtwwwap_html1 .= 		'</tbody>';
		$rtwwwap_html1 .= 	'</table>';
	}

		//coupons
	$rtwwwap_commission_settings = get_option( 'rtwwwap_commission_settings_opt' );
	$rtwwwap_is_coupon_activated = isset( $rtwwwap_commission_settings[ 'coupons' ] ) ? $rtwwwap_commission_settings[ 'coupons' ] : 0;

	if( $rtwwwap_is_coupon_activated && RTWWWAP_IS_WOO == 1 ){
		$rtwwwap_min_amount_for_coupon = isset( $rtwwwap_commission_settings[ 'min_amount_for_coupon' ] ) ? $rtwwwap_commission_settings[ 'min_amount_for_coupon' ] : 0;
		$rtwwwap_html1 .= 	'<div  id="rtwwwap_create_coupon_container">';
		$rtwwwap_html1 .= 		'<p class="rtwwwap_create_coupon_text">';
		$rtwwwap_html1 .= 			sprintf( '%s', esc_html__( 'Create Coupon', 'rtwwwap-wp-wc-affiliate-program' ) );
		$rtwwwap_html1 .= 		'</p>';

		if( $rtwwwap_wallet >= $rtwwwap_min_amount_for_coupon ){
			$rtwwwap_html1 .= 		'<p>';
			$rtwwwap_html1 .= 			'<input class="input-text" id="rtwwwap_coupon_amount" type="number" min="'.esc_attr( $rtwwwap_min_amount_for_coupon ).'" max="'.esc_attr( $rtwwwap_wallet ).'" value="'.esc_attr( $rtwwwap_min_amount_for_coupon ).'" />';
			$rtwwwap_html1 .= 			'<button class="button" type="submit" id="rtwwwap_create_coupon" >'.sprintf( '%s', esc_html__( 'Create Coupon', 'rtwwwap-wp-wc-affiliate-program' ) ).'</button>';
			$rtwwwap_html1 .= 		'</p>';
		}
		else{
			$rtwwwap_html1 .= 		'<p>';
			$rtwwwap_html1 .= 			esc_html__( 'You can create ', 'rtwwwap-wp-wc-affiliate-program' );
			$rtwwwap_html1 .= 			'<span class="rtwwwap-font_bold">';
			$rtwwwap_html1 .= 				esc_html__( 'Coupons', 'rtwwwap-wp-wc-affiliate-program' );
			$rtwwwap_html1 .= 			'</span>';
			$rtwwwap_html1 .= 			esc_html__( ' once your Wallet amount is greater than ', 'rtwwwap-wp-wc-affiliate-program' );
			$rtwwwap_html1 .= 			'<span class="rtwwwap-font_bold">';
			$rtwwwap_html1 .= 				$rtwwwap_currency_sym . $rtwwwap_min_amount_for_coupon;
			$rtwwwap_html1 .= 			'</span>';
			$rtwwwap_html1 .= 		'</p>';
		}
	
	}

	$rtwwwap_coupons = get_user_meta( $rtwwwap_user_id, 'rtwwwap_coupons', true );
	if( $rtwwwap_coupons && RTWWWAP_IS_WOO == 1){
		$rtwwwap_html1 .= 	'<table id="rtwwwap_coupons_table">';
		$rtwwwap_html1 .= 		'<thead>';
		$rtwwwap_html1 .= 			'<tr>';
		$rtwwwap_html1 .= 				'<th>';
		$rtwwwap_html1 .= 					sprintf( '%s', esc_html__( 'Coupon', 'rtwwwap-wp-wc-affiliate-program' ) );
		$rtwwwap_html1 .= 				'</th>';
		$rtwwwap_html1 .= 				'<th>';
		$rtwwwap_html1 .= 					sprintf( '%s (%s)', esc_html__( 'Amount', 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_currency_sym );
		$rtwwwap_html1 .= 				'</th>';
		$rtwwwap_html1 .= 			'</tr>';
		$rtwwwap_html1 .= 		'</thead>';
		$rtwwwap_html1 .= 		'<tbody>';
		$rtwwwap_valid_coupon = true;
		foreach( $rtwwwap_coupons as $rtwwwap_key => $rtwwwap_coupon_id ){
			if( get_post_status( $rtwwwap_coupon_id ) == 'publish' ){
				$rtwwwap_valid_coupon = false;
				$rtwwwap_coupon = esc_html( get_the_title( $rtwwwap_coupon_id ) );
				$rtwwwap_amount = esc_html( get_post_meta( $rtwwwap_coupon_id, 'coupon_amount', true ) );
				$rtwwwap_html1 .= 		'<tr>';
				$rtwwwap_html1 .= 			'<td>';
				$rtwwwap_html1 .= 				sprintf( '%s', $rtwwwap_coupon );
				$rtwwwap_html1 .= 			'</td>';
				$rtwwwap_html1 .= 			'<td>';
				$rtwwwap_html1 .= 				sprintf( '%u', $rtwwwap_amount );
				$rtwwwap_html1 .= 			'</td>';
				$rtwwwap_html1 .= 		'</tr>';
			}
		}
		if( $rtwwwap_valid_coupon ){
			$rtwwwap_html1 .= 			'<tr>';
			$rtwwwap_html1 .= 				'<td colspan="2">';
			$rtwwwap_html1 .= 					sprintf( '%s', esc_html__( 'No Coupons', 'rtwwwap-wp-wc-affiliate-program' ) );
			$rtwwwap_html1 .= 				'</td>';
			$rtwwwap_html1 .= 			'</tr>';
		}
		$rtwwwap_html1 .= 		'</tbody>';
		$rtwwwap_html1 .= 	'</table>';
	}

	return $rtwwwap_html1;
}

	// commissions
if( isset( $_GET[ 'rtwwwap_tab' ] ) && $_GET[ 'rtwwwap_tab' ] == 'commissions' ){
	
	$rtwwwap_commissions = get_option( 'rtwwwap_commission_settings_opt' );
	$rtwwwap_html1 = '';
	
	$rtwwwap_html1 .= 	'<table id="rtwwwap_commission">';
	
	if(RTWWWAP_IS_WOO == 1 )
	{
		$rtwwwap_post_type = 'product';
	}
	if(RTWWWAP_IS_Easy == 1 )
	{
		$rtwwwap_post_type = 'download';
	}
	if( $rtwwwap_commissions && !empty( $rtwwwap_commissions ) ){
		$rtwwwap_commission_settings = get_option( 'rtwwwap_commission_settings_opt' );
		$rtwwwap_comm_base 	= isset( $rtwwwap_commission_settings[ 'comm_base' ] ) ? $rtwwwap_commission_settings[ 'comm_base' ] : '1';

		if( $rtwwwap_comm_base == 1 ){
			foreach( $rtwwwap_commissions as $rtwwwap_key => $rtwwwap_value ){
				if( $rtwwwap_key == 'all_commission' ){
					$rtwwwap_html1 .= 	'<div class="rtwwwap_commissionws_wrapper">';
					$rtwwwap_html1 .= 		esc_html__( 'Commission on all Products', 'rtwwwap-wp-wc-affiliate-program' );
					$rtwwwap_html1 .= 		'<span>';
					
					if( $rtwwwap_commission_settings[ 'all_commission_type' ] == 'percentage' )
					{
						$rtwwwap_html1 .= 		sprintf( '%u%s', esc_html( $rtwwwap_value ), '%' );
					}
					else{
						$rtwwwap_html1 .= 		sprintf( '%s%u', $rtwwwap_currency_sym, esc_html( $rtwwwap_value ) );
					}
					$rtwwwap_html1 .= 		'</span>';
					$rtwwwap_html1 .= 	'</div>';

				}
				elseif( $rtwwwap_key == 'per_prod_mode' ){
					if( $rtwwwap_value == 1 ){
						$rtwwwap_args = array(
							'post_type'  => $rtwwwap_post_type,
							'meta_query' => array(
								array(
									'key' => 'rtwwwap_percentage_commission_box',
									'value'   => '',
									'compare' => '!='
								)
							),
							'fields' 		=> 'ids',
							'numberposts' 	=> -1
						);
					}
					elseif( $rtwwwap_value == 2 ){
						$rtwwwap_args = array(
							'post_type'  => $rtwwwap_post_type,
							'meta_query' => array(
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
					else{
						$rtwwwap_args = array(
							'post_type'  => $rtwwwap_post_type,
							'meta_query' => array(
								'relation' => 'OR',
								array(
									'key' => 'rtwwwap_percentage_commission_box',
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
					$rtwwwap_products 	 = get_posts( $rtwwwap_args );

					$rtwwwap_html1 .= 	'<thead>';
					$rtwwwap_html1 .= 		'<tr>';
					$rtwwwap_html1 .= 			'<th colspan="3">';
					$rtwwwap_html1 .= 		 		esc_html__( 'Per Product Commission', 'rtwwwap-wp-wc-affiliate-program' );
					$rtwwwap_html1 .= 			'</th>';
					$rtwwwap_html1 .= 		'</tr>';
					$rtwwwap_html1 .= 	'</thead>';

					$rtwwwap_html1 .= 	'<tbody>';
					$rtwwwap_html1 .= 		'<tr>';
					$rtwwwap_html1 .= 		'<td><b>';
					$rtwwwap_html1 .= 		 		esc_html__( 'Product Name', 'rtwwwap-wp-wc-affiliate-program' );
					$rtwwwap_html1 .= 			'</b></td>';
					if( $rtwwwap_value == 1 || $rtwwwap_value == 3 ){
						$rtwwwap_html1 .= 	'<td><b>';
						$rtwwwap_html1 .= 		 	esc_html__( 'Percentage commission (%)', 'rtwwwap-wp-wc-affiliate-program' );
						$rtwwwap_html1 .= 		'</b></td>';
					}
					if( $rtwwwap_value == 2 || $rtwwwap_value == 3 ){
						$rtwwwap_html1 .= 	'<td><b>';
						$rtwwwap_html1 .= 		 	esc_html__( "Fixed commission ($rtwwwap_currency_sym)", 'rtwwwap-wp-wc-affiliate-program' );
						$rtwwwap_html1 .= 		'</b></td>';
					}
					$rtwwwap_html1 .= 		'</tr>';

					if( !empty( $rtwwwap_products ) ){
						foreach( $rtwwwap_products as $rtwwwap_key1 => $rtwwwap_value1 ){
							$rtwwwap_perc_comm 	= get_post_meta( $rtwwwap_value1, 'rtwwwap_percentage_commission_box', true );
							$rtwwwap_fix_comm 	= get_post_meta( $rtwwwap_value1, 'rtwwwap_fixed_commission_box', true );
							$rtwwwap_prod_name 	= get_the_title( $rtwwwap_value1 );

							$rtwwwap_html1 .= 	'<tr>';
							$rtwwwap_html1 .= 		'<td>';
							$rtwwwap_html1 .= 			esc_html( $rtwwwap_prod_name );
							$rtwwwap_html1 .= 		'</td>';
							$rtwwwap_html1 .= 		'<td>';
							if( $rtwwwap_value == 1 || $rtwwwap_value == 3 ){
								$rtwwwap_html1 .= 			esc_html( $rtwwwap_perc_comm );
							}
							$rtwwwap_html1 .= 		'</td>';
							$rtwwwap_html1 .= 		'<td>';
							if( $rtwwwap_value == 2 || $rtwwwap_value == 3 ){
								$rtwwwap_html1 .= 			esc_html( $rtwwwap_fix_comm );
							}
							$rtwwwap_html1 .= 		'</td>';
							$rtwwwap_html1 .= 	'</tr>';
						}
					}
					else{
						$rtwwwap_html1 .= 	'<tr>';
						$rtwwwap_html1 .= 		'<td colspan="3" class="rtwwwap_no_comm">'.esc_html__( 'Specific Product commission not set. Check Category Commissions' ).'</td>';
						$rtwwwap_html1 .= 	'</tr>';
					}

					$rtwwwap_html1 .= 	'</tbody>';
				}
				elseif( $rtwwwap_key == 'per_cat' ){
					$rtwwwap_html1 .= 	'<thead>';
					$rtwwwap_html1 .= 		'<tr>';
					$rtwwwap_html1 .= 			'<th colspan="3">';
					$rtwwwap_html1 .= 		 		esc_html__( 'Per Category Commission' ,'rtwwwap-wp-wc-affiliate-program' );
					$rtwwwap_html1 .= 			'</th>';
					$rtwwwap_html1 .= 		'</tr>';
					$rtwwwap_html1 .= 	'</thead>';

					$rtwwwap_html1 .= 	'<tbody>';
					$rtwwwap_html1 .= 		'<tr>';
					$rtwwwap_html1 .= 			'<td><b>';
					$rtwwwap_html1 .= 		 		esc_html__( 'Category Name', 'rtwwwap-wp-wc-affiliate-program' );
					$rtwwwap_html1 .= 			'</b></td>';
					$rtwwwap_html1 .= 			'<td><b>';
					$rtwwwap_html1 .= 		 		esc_html__( 'Percentage commission (%)', 'rtwwwap-wp-wc-affiliate-program' );
					$rtwwwap_html1 .= 			'</b></td>';
					$rtwwwap_html1 .= 			'<td><b>';
					$rtwwwap_html1 .= 		 	 sprintf( '%s (%s)', esc_html__( "Fixed commission", 'rtwwwap-wp-wc-affiliate-program' ), esc_html( $rtwwwap_currency_sym ) );
					$rtwwwap_html1 .= 			'</b></td>';
					$rtwwwap_html1 .= 		'</tr>';

					$rtwwwap_cat_count = 0;
					
					foreach( $rtwwwap_value as $rtwwwap_key1 => $rtwwwap_value1 ){
						$rtwwwap_cat_name = '';
						foreach( $rtwwwap_value1[ 'ids' ] as $rtwwwap_key2 => $rtwwwap_value2 ){
							if( $rtwwwap_key2 > 0 ){
								$rtwwwap_cat_name .= ', ';
							}
							if(RTWWWAP_IS_WOO == 1 )
							{
							$rtwwwp_product_category_taxonomy = 'product_cat';
							}
							if(RTWWWAP_IS_Easy == 1 )
							{
							$rtwwwp_product_category_taxonomy = 'download_category';
							}
							
							$rtwwwap_term 		= get_term_by( 'id', $rtwwwap_value2, $rtwwwp_product_category_taxonomy );
							$rtwwwap_cat_name .= $rtwwwap_term->name;
						}
						$rtwwwap_perc_comm 	= $rtwwwap_value1[ 'cat_percentage_commission' ];
						$rtwwwap_fix_comm 	= $rtwwwap_value1[ 'cat_fixed_commission' ];

						if( $rtwwwap_cat_name != '' ){
							$rtwwwap_cat_count = 1;
							$rtwwwap_html1 .= 	'<tr>';
							$rtwwwap_html1 .= 		'<td>';
							$rtwwwap_html1 .= 			esc_html( $rtwwwap_cat_name );
							$rtwwwap_html1 .= 		'</td>';
							$rtwwwap_html1 .= 		'<td>';
							$rtwwwap_html1 .= 			esc_html( $rtwwwap_perc_comm );
							$rtwwwap_html1 .= 		'</td>';
							$rtwwwap_html1 .= 		'<td>';
							$rtwwwap_html1 .= 			esc_html( $rtwwwap_fix_comm );
							$rtwwwap_html1 .= 		'</td>';
							$rtwwwap_html1 .= 	'</tr>';
						}
						if( !$rtwwwap_cat_count ){
							$rtwwwap_html1 .= 	'<tr>';
							$rtwwwap_html1 .= 		'<td colspan="3" class="rtwwwap_no_comm">'.esc_html__( 'Specific Category commission not set.', 'rtwwwap-wp-wc-affiliate-program' ).'</td>';
							$rtwwwap_html1 .= 	'</tr>';
						}
					}

					$rtwwwap_html1 .= 	'</tbody>';
				}
			}
		}

		// 	else{
		// 		$rtwwwap_levels_settings = get_option( 'rtwwwap_levels_settings_opt' );
		// 		$rtwwwap_user_level 	= get_user_meta( $rtwwwap_user_id, 'rtwwwap_affiliate_level', true );
		// 		$rtwwwap_user_level 	= ( $rtwwwap_user_level ) ? $rtwwwap_user_level : 0;
		// 		if( !empty( $rtwwwap_levels_settings ) )
		// 		{
		// 			$rtwwwap_html1 .= 	'<thead class="rtwwwap_level_comm">';
		// 			$rtwwwap_html1 .= 		'<tr>';
		// 			$rtwwwap_html1 .= 			'<th>';
		// 			$rtwwwap_html1 .= 		 		esc_html__( 'Level Name' );
		// 			$rtwwwap_html1 .= 			'</th>';
		// 			$rtwwwap_html1 .= 			'<th>';
		// 			$rtwwwap_html1 .= 		 		esc_html__( 'Level commission' );
		// 			$rtwwwap_html1 .= 			'</th>';
		// 			$rtwwwap_html1 .= 		'</tr>';
		// 			$rtwwwap_html1 .= 	'</thead>';

		// 			$rtwwwap_html1 .= 	'<tbody>';
		// 			foreach( $rtwwwap_levels_settings as $rtwwwap_levels_key => $rtwwwap_levels_val )
		// 			{
		// 				if($rtwwwap_user_level == $rtwwwap_levels_key)
		// 				{
		// 				$rtwwwap_html1 .= 		'<tr>';
		// 				$rtwwwap_html1 .= 			'<td>';
		// 				$rtwwwap_html1 .= 		 		esc_html($rtwwwap_levels_val['level_name']);
		// 				$rtwwwap_html1 .= 			'</td>';

		// 				$rtwwwap_html1 .= 			'<td>';
		// 				if( $rtwwwap_levels_val['level_commission_type'] == '0' )
		// 				{
		// 					$rtwwwap_html1 .= sprintf( '%s%s', esc_html( $rtwwwap_levels_val['level_comm_amount']  ), '%' );
		// 				}
		// 				elseif( $rtwwwap_levels_val['level_commission_type'] == '1' )
		// 				{
		// 					$rtwwwap_html1 .= sprintf( '%s%01.'.$rtwwwap_decimal_place, $rtwwwap_currency_sym, esc_html($rtwwwap_levels_val['level_comm_amount']) );
		// 				}
		// 				$rtwwwap_html1 .= 			'</td>';
		// 				$rtwwwap_html1 .= 		'</tr>';
		// 				}
		// 			}
		// 			$rtwwwap_html1 .= 	'</tbody>';
		// 		}
		// 	}
		// }

		else{
			$rtwwwap_levels_settings = get_option( 'rtwwwap_levels_settings_opt' );
			if( !empty( $rtwwwap_levels_settings ) )
			{
				$rtwwwap_html1 .= 	'<thead class="rtwwwap_level_comm">';
				$rtwwwap_html1 .= 		'<tr>';
				$rtwwwap_html1 .= 			'<th>';
				$rtwwwap_html1 .= 		 		esc_html__( 'Level No.' );
				$rtwwwap_html1 .= 			'</th>';
				$rtwwwap_html1 .= 			'<th>';
				$rtwwwap_html1 .= 		 		esc_html__( 'Level Name' );
				$rtwwwap_html1 .= 			'</th>';
				$rtwwwap_html1 .= 			'<th>';
				$rtwwwap_html1 .= 		 		esc_html__( 'Level commission' );
				$rtwwwap_html1 .= 			'</th>';
				$rtwwwap_html1 .= 			'<th>';
				$rtwwwap_html1 .= 		 		esc_html__( 'To Reach' );
				$rtwwwap_html1 .= 			'</th>';
				$rtwwwap_html1 .= 		'</tr>';
				$rtwwwap_html1 .= 	'</thead>';

				$rtwwwap_html1 .= 	'<tbody>';
				foreach( $rtwwwap_levels_settings as $rtwwwap_levels_key => $rtwwwap_levels_val )
				{
					$rtwwwap_html1 .= 		'<tr>';
					$rtwwwap_html1 .= 			'<td>';
					$rtwwwap_html1 .= 		 		esc_html( $rtwwwap_levels_key );
					$rtwwwap_html1 .= 			'</td>';

					$rtwwwap_html1 .= 			'<td>';
					$rtwwwap_html1 .= 		 		esc_html( $rtwwwap_levels_val[ 'level_name' ] );
					$rtwwwap_html1 .= 			'</td>';

					$rtwwwap_html1 .= 			'<td>';
					if( $rtwwwap_levels_val[ 'level_commission_type' ] == '0' )
					{
						$rtwwwap_html1 .= sprintf( '%s%s', esc_html( $rtwwwap_levels_val[ 'level_comm_amount' ] ), '%' );
					}
					elseif( $rtwwwap_levels_val[ 'level_commission_type' ] == '1' )
					{
						$rtwwwap_html1 .= sprintf( '%s%s', $rtwwwap_currency_sym, esc_html( $rtwwwap_levels_val[ 'level_comm_amount' ] ) );
					}
					$rtwwwap_html1 .= 			'</td>';

					$rtwwwap_html1 .= 			'<td>';
					if( $rtwwwap_levels_val[ 'level_criteria_type' ] == 0 )
					{
						$rtwwwap_html1 .= sprintf( '%s', esc_html__( 'Become Affiliate' ) );
					}
					elseif( $rtwwwap_levels_val[ 'level_criteria_type' ] == 1 )
					{
						$rtwwwap_html1 .= sprintf( '%s %s', esc_html__( 'No. of referrals' ), esc_html__( $rtwwwap_levels_val[ 'level_criteria_val' ] ) );
					}
					elseif( $rtwwwap_levels_val[ 'level_criteria_type' ] == 2 )
					{
						$rtwwwap_html1 .= sprintf( '%s %s%01.'.$rtwwwap_decimal_place, esc_html__( 'Total sale amount' ), $rtwwwap_currency_sym, esc_html__( $rtwwwap_levels_val[ 'level_criteria_val' ] ) );
						;
					}
					$rtwwwap_html1 .= 			'</td>';
					$rtwwwap_html1 .= 		'</tr>';
				}
				$rtwwwap_html1 .= 	'</tbody>';
			}
		}
	}
	else{
		$rtwwwap_html1 .= 	'<div class="rtwwwap_commissionws_wrapper">';
		$rtwwwap_html1 .= 		esc_html__( 'No Commission is set on any Product', 'rtwwwap-wp-wc-affiliate-program' );
		$rtwwwap_html1 .= 		'<span>';
		$rtwwwap_html1 .= 	'</div>';
	}
	$rtwwwap_html1 .= 	'</table>';

	$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );
	$rtwwwap_social_share 	= isset( $rtwwwap_extra_features[ 'social_share' ] ) ? $rtwwwap_extra_features[ 'social_share' ] : 0;

	$rtwwwap_signup_bonus 	= isset( $rtwwwap_extra_features[ 'signup_bonus' ] ) ? $rtwwwap_extra_features[ 'signup_bonus' ] : 0;

	if( $rtwwwap_social_share === 'on' || $rtwwwap_signup_bonus ){
		$rtwwwap_html1 .= 	'<table id="rtwwwap_commission">';
		$rtwwwap_html1 .= 		'<thead class="rtwwwap_level_comm">';
		$rtwwwap_html1 .= 			'<tr>';
		$rtwwwap_html1 .= 				'<th>';
		$rtwwwap_html1 .= 					esc_html__( 'Special Bonus Type', 'rtwwwap-wp-wc-affiliate-program' );
		$rtwwwap_html1 .= 				'</th>';
		$rtwwwap_html1 .= 				'<th>';
		$rtwwwap_html1 .= 					esc_html__( 'Bonus Amount', 'rtwwwap-wp-wc-affiliate-program' );
		$rtwwwap_html1 .= 				'</th>';
		$rtwwwap_html1 .= 			'</tr>';
		$rtwwwap_html1 .= 		'</thead>';
		$rtwwwap_html1 .= 		'<tbody>';
	}

	if( $rtwwwap_signup_bonus ){
		$rtwwwap_html1 .= 			'<tr>';
		$rtwwwap_html1 .= 				'<td>';
		$rtwwwap_html1 .= 					esc_html__( 'Signup Bonus', 'rtwwwap-wp-wc-affiliate-program' );
		$rtwwwap_html1 .= 				'</td>';
		$rtwwwap_html1 .= 				'<td>';
		$rtwwwap_html1 .= 					sprintf( '%s%01.2f', $rtwwwap_currency_sym, $rtwwwap_signup_bonus );
		$rtwwwap_html1 .= 				'</td>';
		$rtwwwap_html1 .= 			'</tr>';
	}

	if( $rtwwwap_social_share === 'on' ){
		$rtwwwap_sharing_bonus = isset( $rtwwwap_extra_features[ 'sharing_bonus' ] ) ? $rtwwwap_extra_features[ 'sharing_bonus' ] : 0;

		$rtwwwap_bonus_time_limit_selected = isset( $rtwwwap_extra_features[ 'sharing_bonus_time_limit' ] ) ? $rtwwwap_extra_features[ 'sharing_bonus_time_limit' ] : 0;

		$rtwwwap_sharing_bonus_amount_limit = isset( $rtwwwap_extra_features[ 'sharing_bonus_amount_limit' ] ) ? $rtwwwap_extra_features[ 'sharing_bonus_amount_limit' ] : 0;

		$rtwwwap_html1 .= 			'<tr>';
		$rtwwwap_html1 .= 				'<td>';
		$rtwwwap_html1 .= 					esc_html__( 'Sharing Bonus', 'rtwwwap-wp-wc-affiliate-program' );
		$rtwwwap_html1 .= 				'</td>';
		$rtwwwap_html1 .= 				'<td>';
		if( $rtwwwap_bonus_time_limit_selected == 0 ){
			$rtwwwap_html1 .= 				sprintf( '%s%01.2f %s %s.', $rtwwwap_currency_sym, $rtwwwap_sharing_bonus, esc_html__( 'per Shared product', 'rtwwwap-wp-wc-affiliate-program' ), esc_html__( 'with no Maximum Limit', 'rtwwwap-wp-wc-affiliate-program' ) );
		}
		else{
			$rtwwwap_html1 .= 				sprintf( '%s%01.2f %s. %s %s%01.2f', $rtwwwap_currency_sym, $rtwwwap_sharing_bonus, esc_html__( 'per Shared product', 'rtwwwap-wp-wc-affiliate-program' ), esc_html__( 'Maximum', 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_currency_sym, $rtwwwap_sharing_bonus_amount_limit );

			if( $rtwwwap_bonus_time_limit_selected == 1 ){
				$rtwwwap_html1 .=			sprintf( ' %s.', esc_html__( 'in a Day', 'rtwwwap-wp-wc-affiliate-program' ) );
			}
			if( $rtwwwap_bonus_time_limit_selected == 2 ){
				$rtwwwap_html1 .=			sprintf( ' %s.', esc_html__( 'in a Week', 'rtwwwap-wp-wc-affiliate-program' ) );
			}
			if( $rtwwwap_bonus_time_limit_selected == 3 ){
				$rtwwwap_html1 .= 			sprintf( ' %s.', esc_html__( 'in a month', 'rtwwwap-wp-wc-affiliate-program' ) );
			}
		}
		$rtwwwap_html1 .= 				'</td>';
		$rtwwwap_html1 .= 			'</tr>';
	}

	if( $rtwwwap_social_share === 'on' ){
		$rtwwwap_html1 .= 		'</tbody>';
		$rtwwwap_html1 .= 	'</table>';
	}

	return $rtwwwap_html1;
}

	// tools
if( isset( $_GET[ 'rtwwwap_tab' ] ) && $_GET[ 'rtwwwap_tab' ] == 'affiliate_tools' ){
	if(RTWWWAP_IS_WOO == 1)
	{
	$rtwwwap_all_categories = get_categories( array(
		'hide_empty' 	=> 0,
		'taxonomy'   	=> 'product_cat'
	));
	}
	// display download categories
	if(RTWWWAP_IS_Easy == 1)
	{
		$rtwwwap_all_categories = get_categories( array(
		'hide_empty' 	=> 0,
		'taxonomy'   	=> 'download_category'
	));
	}
	

	$rtwwwap_user_name = wp_get_current_user();
	$rtwwwap_user_name = $rtwwwap_user_name->data->user_login;
	$rtwwwap_user_name_new = str_replace(" ","_",$rtwwwap_user_name);

	// update code start here 

	$rtwwwap_curr_aff_id = get_current_user_id();
	$rtwwwap_aff_custom_code = get_user_meta( $rtwwwap_curr_aff_id, 'rtwwwap_referee_custom_str', true );
	if(!$rtwwwap_aff_custom_code){

		$randomString = $this->rtwwwap_generate_custom_code(6);

		update_user_meta( $rtwwwap_curr_aff_id, 'rtwwwap_referee_custom_str', $randomString );

		$rtwwwap_aff_custom_code = get_user_meta( $rtwwwap_curr_aff_id, 'rtwwwap_referee_custom_str', true );
	}

	$rtwwwap_all_users = get_users(array(
		'meta_key'     => 'rtwwwap_referee_custom_str',
		'meta_value'   => $rtwwwap_aff_custom_code,
	));

	// ends here

	$rtwwwap_extra_features_opt 	= get_option( 'rtwwwap_extra_features_opt' );
	$rtwwwap_social_share_setting 	= isset( $rtwwwap_extra_features_opt[ 'social_share' ] ) ? $rtwwwap_extra_features_opt[ 'social_share' ] : 0;
	$rtwwwap_qr_code_setting 		= isset( $rtwwwap_extra_features_opt[ 'qr_code' ] ) ? $rtwwwap_extra_features_opt[ 'qr_code' ] : 0;
	$rtwwwap_affiliate_slug 		= isset( $rtwwwap_extra_features_opt[ 'affiliate_slug' ] ) ? $rtwwwap_extra_features_opt[ 'affiliate_slug' ] : esc_html__( 'rtwwwap_aff', 'rtwwwap-wp-wc-affiliate-program' ) ;

	$rtwwwap_html1 = '';
	$rtwwwap_html1 .=	'<div id="rtwwwap_affiliates">';
	$rtwwwap_html1 .=	  	'<h3>'.esc_html__( 'Generate links', 'rtwwwap-wp-wc-affiliate-program' ).'</h3>';
	$rtwwwap_html1 .=	  	'<div id="rtwwwap_aff_links">';
	$rtwwwap_html1 .=	    	'<input type="text" id="rtwwwap_aff_link_input" placeholder="'.esc_attr__( 'Enter any product\'s URL from this website', 'rtwwwap-wp-wc-affiliate-program' ).'" value="'.esc_attr( home_url() ).'"/>';
	$rtwwwap_html1 .=	    	'<p id="rtwwwap_generated_link"></p>';
	$rtwwwap_html1 .=	    	'<input type="button" id="rtwwwap_generate_button" data-rtwwwap_aff_id="'.esc_attr( get_current_user_id() ).'" data-rtwwwap_aff_slug="'.$rtwwwap_affiliate_slug.'" data-rtwwwap_aff_name="'.esc_attr( $rtwwwap_aff_custom_code ).'" value="'.esc_attr__( 'Generate link', 'rtwwwap-wp-wc-affiliate-program' ).'" />';
	$rtwwwap_html1 .=	  	'<div class="rtwwwap_span_copied">';
	$rtwwwap_html1 .=	    	'<input type="button" id="rtwwwap_copy_to_clip" value="'.esc_attr__( 'Copy link', 'rtwwwap-wp-wc-affiliate-program' ).'" />';
	$rtwwwap_html1 .=	    	'<span id="rtwwwap_copy_tooltip_link">'.esc_html__( 'Copied', 'rtwwwap-wp-wc-affiliate-program' ).'</span>';
	$rtwwwap_html1 .=	  	'</div>';

	if( $rtwwwap_qr_code_setting ){
		$rtwwwap_html1 .=	    '<input type="button" id="rtwwwap_generate_qr" value="'.esc_attr__( 'Create QR Code', 'rtwwwap-wp-wc-affiliate-program' ).'" />';
	}
	$rtwwwap_html1 .=	  	'</div>';

	$rtwwwap_html1 .=	  	'<div class="rtwwwap_share_qr">';
		//social share
	if( $rtwwwap_social_share_setting === 'on' ){
		$rtwwwap_twitter_img_url 	= esc_url( RTWWWAP_URL.'/assets/images/twitter-share.png' );
		$rtwwwap_facebook_img_url 	= esc_url( RTWWWAP_URL.'/assets/images/facebook-share.png' );
		$rtwwwap_mail_img_url 		= esc_url( RTWWWAP_URL.'/assets/images/mail-share.png' );
		$rtwwwap_whatsapp_img_url 	= esc_url( RTWWWAP_URL.'/assets/images/whatsapp-share.png' );
		$rtwwwap_html1 .=	  	'<div class="rtwwwap_social_share">';
		$rtwwwap_html1 .=	  		'<div class="rtwwwap_btn">';
		$rtwwwap_html1 .=	  			'<a class="twitter-share-button rtwwwap_twitter" href="javascript:void(0);">';
		$rtwwwap_html1 .=	  				'<img src="'.$rtwwwap_twitter_img_url.'">';
		$rtwwwap_html1 .=	  				esc_html__( 'Tweet', 'rtwwwap-wp-wc-affiliate-program' );
		$rtwwwap_html1 .=	  			'</a>';
		$rtwwwap_html1 .=	  		'</div>';
		$rtwwwap_html1 .=	  		'<a class="rtwwwap_fb_share" href="javascript:void(0);">';
		$rtwwwap_html1 .=	  			'<img src="'.$rtwwwap_facebook_img_url.'">';
		$rtwwwap_html1 .=	  			esc_html__( 'Facebook', 'rtwwwap-wp-wc-affiliate-program' );
		$rtwwwap_html1 .=	  		'</a>';
		$rtwwwap_html1 .=	  		'<a class="rtwwwap_mail_button" href="mailto:enteryour@addresshere.com?subject=Click on this link &body=Check%20this%20out:%20" rel="nofollow">';
		$rtwwwap_html1 .=	  			'<img src ="'.$rtwwwap_mail_img_url.'">';
		$rtwwwap_html1 .=	  			esc_html__( 'Mail', 'rtwwwap-wp-wc-affiliate-program' );
		$rtwwwap_html1 .=	  		'</a>';
		$rtwwwap_html1 .=	  		'<a class="rtwwwap_whatsapp_share" href="javascript:void(0);" data-action="share/whatsapp/share">';
		$rtwwwap_html1 .=	  			'<img src="'.$rtwwwap_whatsapp_img_url.'">';
		$rtwwwap_html1 .=	  			esc_html__( 'Whatsapp', 'rtwwwap-wp-wc-affiliate-program' );
		$rtwwwap_html1 .=	  		'</a>';
		$rtwwwap_html1 .=	  	'</div>';
	}

		//qrcode
	if( $rtwwwap_qr_code_setting ){
		$rtwwwap_html1 .=	'<div id="rtwwwap_qrcode_main"><a id="rtwwwap_qrcode"></a><a id="rtwwwap_download_qr" download><span class="rtwwwap_download_qr">'.esc_html__( 'Download QR', 'rtwwwap-wp-wc-affiliate-program' ).'</span></a></div>';
	}

	$rtwwwap_html1 .=	  	'</div>';
	$rtwwwap_html1 .=	  	'<h3>'.esc_html__( 'Create banners', 'rtwwwap-wp-wc-affiliate-program' ).'</h3>';
	$rtwwwap_html1 .=	  	'<div id="rtwwwap_banner_links">';
	$rtwwwap_html1 .=	  		'<input type="text" id="rtwwwap_banner_prod_search" placeholder="'.esc_attr__( 'Search Product', 'rtwwwap-wp-wc-affiliate-program' ).'" />';
	$rtwwwap_html1 .=	   		'<select class="rtwwwap_select_cat" id="" name="rtwwwap_select_cat">';
	if( !empty( $rtwwwap_all_categories ) ){
		
		foreach ( $rtwwwap_all_categories as $rtwwwap_key => $rtwwwap_category ) {
			
			if($rtwwwap_category->cat_name == 'uncategorized')
			{
			$rtwwwap_html1 .=		'<option value="'.esc_attr( $rtwwwap_category->cat_ID ).'" selected>';
			$rtwwwap_html1 .=			esc_html( $rtwwwap_category->cat_name );
			$rtwwwap_html1 .= 		'</option>';
			}
			else{
			$rtwwwap_html1 .=		'<option value="'.esc_attr( $rtwwwap_category->cat_ID ).'" >';
			$rtwwwap_html1 .=			esc_html( $rtwwwap_category->cat_name );
			$rtwwwap_html1 .= 		'</option>';
			}
			
		}
	}
	else{
		$rtwwwap_html1 .=		'<option value="" >';
		$rtwwwap_html1 .=			esc_html__( 'No Category', 'rtwwwap-wp-wc-affiliate-program' );
		$rtwwwap_html1 .= 		'</option>';
	}
	$rtwwwap_html1 .=	  		'</select>';
	$rtwwwap_html1 .=	  		'<div>';
	
	$rtwwwap_html1 .=	    		'<input type="button" id="rtwwwap_search_button" value="'.esc_attr__( 'Search', 'rtwwwap-wp-wc-affiliate-program' ).'" />';
	$rtwwwap_html1 .=	  		'</div>';
	$rtwwwap_html1 .=	  	'</div>';
	$rtwwwap_html1 .=	  	'<div id="rtwwwap_search_main_container">';
	$rtwwwap_html1 .=		'</div>';
	$rtwwwap_html1 .=	'</div>';

	return $rtwwwap_html1;
}

	// downloads
if( isset( $_GET[ 'rtwwwap_tab' ] ) && $_GET[ 'rtwwwap_tab' ] == 'download' ){
	if(RTWWWAP_IS_WOO == 1)
	{
	$rtwwwap_all_categories = get_categories( array(
		'hide_empty' 	=> 0,
		'taxonomy'   	=> 'product_cat'
	));
	}
	// display download categories
	if(RTWWWAP_IS_Easy == 1)
	{
		$rtwwwap_all_categories = get_categories( array(
		'hide_empty' 	=> 0,
		'taxonomy'   	=> 'download_category'
	));
	}
	$rtwwwap_html1 = '';
	$rtwwwap_html1 .= 	'<div>';
	$rtwwwap_html1 .=	   	'<select class="rtwwwap_select_cat" id="" name="rtwwwap_select_cat" data-action="" data-exclude="">';
	if( !empty( $rtwwwap_all_categories ) ){
		$rtwwwap_html1 .=	'<option value="" >';
		$rtwwwap_html1 .=		esc_html__( 'Select Category', 'rtwwwap-wp-wc-affiliate-program' );
		$rtwwwap_html1 .= 	'</option>';
		foreach ( $rtwwwap_all_categories as $rtwwwap_key => $rtwwwap_category ) {
			$rtwwwap_html1 .=	'<option value="'.esc_attr( $rtwwwap_category->cat_ID ).'" >';
			$rtwwwap_html1 .=		esc_html( $rtwwwap_category->cat_name );
			$rtwwwap_html1 .= 	'</option>';
		}
	}
	else{
		$rtwwwap_html1 .=	'<option value="" >';
		$rtwwwap_html1 .=		esc_html__( 'No Category', 'rtwwwap-wp-wc-affiliate-program' );
		$rtwwwap_html1 .= 	'</option>';
	}
	$rtwwwap_html1 .=	  	'</select>';
	$rtwwwap_html1 .=	    '<input type="button" id="rtwwwap_generate_csv" value="'.esc_attr__( 'Generate CSV', 'rtwwwap-wp-wc-affiliate-program' ).'" />';
	$rtwwwap_html1 .= 	'</div>';

	return $rtwwwap_html1;
}

	// payout
if( isset( $_GET[ 'rtwwwap_tab' ] ) && $_GET[ 'rtwwwap_tab' ] == 'payout' ){
	$rtwwwap_user_id = get_current_user_id();
	global $wpdb;
	$rtwwwap_affiliate_wallet_transaction = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM '.$wpdb->prefix.'rtwwwap_wallet_transaction WHERE `aff_id` = %d ',$rtwwwap_user_id), ARRAY_A );

	if ( isset( $_POST ) && !empty( $_POST ) ) {
		if ( isset( $_POST[ 'rtwwwap_payout_save' ] ) ) {
			$rtwwwap_referral_mail 	= ( isset( $_POST[ 'rtwwwap_referral_mail' ] ) ) ? sanitize_post( $_POST[ 'rtwwwap_referral_mail' ] ) : '';
			$rtwwwap_payment_method = ( isset( $_POST[ 'rtwwwap_payment_method' ] ) ) ? sanitize_post( $_POST[ 'rtwwwap_payment_method' ] ) : 'rtwwwap_payment_direct';
			$rtwwwap_paypal_email 	= ( isset( $_POST[ 'rtwwwap_paypal_email' ] ) ) ? sanitize_post( $_POST[ 'rtwwwap_paypal_email' ] ) : '';
			$rtwwwap_stripe_email 	= ( isset( $_POST[ 'rtwwwap_stripe_email' ] ) ) ? sanitize_post( $_POST[ 'rtwwwap_stripe_email' ] ) : '';
			$rtwwwap_direct_details = ( isset( $_POST[ 'rtwwwap_direct' ] ) ) ? sanitize_post( $_POST[ 'rtwwwap_direct' ] ) : '';
			$rtwwwap_paystack_bank_account 	= ( isset( $_POST[ 'rtwwwap_paystack_bank_account' ] ) ) ? sanitize_post( $_POST[ 'rtwwwap_paystack_bank_account' ] ) : '';
			$rtwwwap_paystack_bank_code = ( isset( $_POST[ 'rtwwwap_paystack_bank_code' ] ) ) ? sanitize_post( $_POST[ 'rtwwwap_paystack_bank_code' ] ) : '';

			update_user_meta( $rtwwwap_user_id, 'rtwwwap_referral_mail', $rtwwwap_referral_mail );
			update_user_meta( $rtwwwap_user_id, 'rtwwwap_payment_method', $rtwwwap_payment_method );
			update_user_meta( $rtwwwap_user_id, 'rtwwwap_paypal_email', $rtwwwap_paypal_email );
			update_user_meta( $rtwwwap_user_id, 'rtwwwap_stripe_email', $rtwwwap_stripe_email );
			update_user_meta( $rtwwwap_user_id, 'rtwwwap_direct', $rtwwwap_direct_details );
			update_user_meta( $rtwwwap_user_id, 'rtwwwap_paystack_bank_code', $rtwwwap_paystack_bank_code );
			update_user_meta( $rtwwwap_user_id, 'rtwwwap_paystack_bank_account', $rtwwwap_paystack_bank_account );


		}
	}

	$rtwwwap_referral_mail 	= get_user_meta( $rtwwwap_user_id, 'rtwwwap_referral_mail', true );
	$rtwwwap_payment_method = get_user_meta( $rtwwwap_user_id, 'rtwwwap_payment_method', true );
	$rtwwwap_paypal_email 	= get_user_meta( $rtwwwap_user_id, 'rtwwwap_paypal_email', true );
	$rtwwwap_stripe_email 	= get_user_meta( $rtwwwap_user_id, 'rtwwwap_stripe_email', true );
	$rtwwwap_direct_details = get_user_meta( $rtwwwap_user_id, 'rtwwwap_direct', true );

	$rtwwwap_paystack_bank_code = get_user_meta( $rtwwwap_user_id, 'rtwwwap_paystack_bank_code', true );
	$rtwwwap_paystack_bank_account = get_user_meta( $rtwwwap_user_id, 'rtwwwap_paystack_bank_account', true );


	$rtwwwap_commission_settings = get_option( 'rtwwwap_commission_settings_opt' );
	$rtwwwap_comm_base = isset( $rtwwwap_commission_settings[ 'comm_base' ] ) ? $rtwwwap_commission_settings[ 'comm_base' ] : '1';
	$rtwwwap_wallet 			= get_user_meta( $rtwwwap_user_id, 'rtw_user_wallet', true );
	$rtwwwap_wallet   			= isset($rtwwwap_wallet) ? $rtwwwap_wallet : 0;
	$rtwwwap_level_name = '';
	if( $rtwwwap_comm_base == 2 )
	{
		$rtwwwap_levels_settings 	= get_option( 'rtwwwap_levels_settings_opt' );
		if( !empty( $rtwwwap_levels_settings ) )
		{
			$rtwwwap_user_level 	= get_user_meta( $rtwwwap_user_id, 'rtwwwap_affiliate_level', true );
			$rtwwwap_user_level 	= ( $rtwwwap_user_level ) ? $rtwwwap_user_level : 0;
			$rtwwwap_level_name 	= $rtwwwap_levels_settings[ $rtwwwap_user_level ][ 'level_name' ];
			$rtwwwap_level_comm 	= $rtwwwap_levels_settings[ $rtwwwap_user_level ][ 'level_comm_amount' ];
			$rtwwwap_level_comm_type = $rtwwwap_levels_settings[ $rtwwwap_user_level ][ 'level_commission_type' ];

			if( $rtwwwap_level_comm_type == 0 ){
				$rtwwwap_level_comm_type = '%';
			}
			elseif( $rtwwwap_level_comm_type == 1 ){
				$rtwwwap_level_comm_type = $rtwwwap_currency_sym;
			}
		}
	}


	$rtwwwap_html1 = '';
	$rtwwwap_html1 .= '<div class="rtwwwap_main_wallet_wrapper">';
	$rtwwwap_html1 .= 	'<div class="rtwwwap_payment_wallet_wrapper"><div class="rtwwwap_request_text_wrapper">';
	$rtwwwap_html1 .=       '<i class="fas fa-wallet"></i><span class="rtwwwap_request_text">'.esc_html__( "Request to Withdrawal", "rtwwwap-wp-wc-affiliate-program" ).'</span>';
	$rtwwwap_html1 .= 	'</div>
								<div class="rtwwwap_wallet_model">
									<div class="rtwwwap_wallet_model_dialog">
										<div class="rtwwwap_wallet_model_content">
											<div class="rtwwwap_wallet_model_header">
												<h3>'.esc_html__( "Withdrawal Your Money", "rtwwwap-wp-wc-affiliate-program" ).'</h3>
												<div class="rtwwwap_close_model_icon">
													<i class="fas fa-times"></i>
												</div>
											</div>
											<div class="rtwwwap_wallet_model_body">
												<div class="rtwwwap_amount_text">
													<label>
													'.esc_html__( "Available Balance:", "rtwwwap-wp-wc-affiliate-program" ).'
													</label>
													<p>
													'.esc_attr(	$rtwwwap_currency_sym.$rtwwwap_wallet).'
													</p>
												
												</div>
												<div class="rtwwwap_amount_text">
													<label class="rtwwwap_inpt_label">'.esc_html__( "Input Amount:", "rtwwwap-wp-wc-affiliate-program" ).'</label>
													<input class="rtwwwap_with_amount" type="Number">
													
												</div>

											</div>
											<div class="rtwwwap_wallet_model_footer">
												<button class="rtwwwap_save_btn" id="rtwwwap_request_widh" data-wallet_amount="'.$rtwwwap_wallet.'"  data-payment_method="'.$rtwwwap_payment_method.'">'.esc_html__( "Request", "rtwwwap-wp-wc-affiliate-program" ).'</button>
												<button class="rtwwwap_cancel_btn_with">'.esc_html__( "cancel", "rtwwwap-wp-wc-affiliate-program" ).'</button>
											</div>
										</div>
									</div>
								</div>
						</div>';
	$rtwwwap_html1 .= 	'<div class="rtwwwap_wallet_transaction_wrapper"><div class="rtwwwap_transaction_text_wrapper">';
	$rtwwwap_html1 .=       '<i class="fas fa-wallet"></i><span class="rtwwwap_request_text">'.esc_html__( "Withdrawal Transaction table", "rtwwwap-wp-wc-affiliate-program" ).'</span>';
	$rtwwwap_html1 .= 	'</div>
								<div class="rtwwwap_wallet_model_transaction">
									<div class="rtwwwap_wallet_model_dialog">
										<div class="rtwwwap_wallet_model_content">
											<div class="rtwwwap_wallet_model_header">
												<h3>'.esc_html__( "Withdrawal Transaction Table ", "rtwwwap-wp-wc-affiliate-program" ).'</h3>
												<div class="rtwwwap_close_model_icon ">
													<i class="fas fa-times"></i>
												</div>
											</div>
											<div class="rtwwwap_wallet_model_body">';

											$rtwwwap_html1 .= 	'<table id="rtwwwap_referrals_table">';
											$rtwwwap_html1 .= 		'<thead>';
											$rtwwwap_html1 .= 			'<tr>';
											$rtwwwap_html1 .= 				'<th>';
											$rtwwwap_html1 .= 					sprintf( '%s (%s)', esc_html__( 'Amount', 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_currency_sym );
											$rtwwwap_html1 .= 				'</th>';
											$rtwwwap_html1 .= 				'<th>';
											$rtwwwap_html1 .= 					sprintf( '%s', esc_html__( 'Date', 'rtwwwap-wp-wc-affiliate-program' ) );
											$rtwwwap_html1 .= 				'</th>';
											$rtwwwap_html1 .= 				'<th >';
											$rtwwwap_html1 .= 					sprintf( '%s', esc_html__( 'Status', 'rtwwwap-wp-wc-affiliate-program' ) );
											$rtwwwap_html1 .= 				'</th >';
											$rtwwwap_html1 .= 			'</tr>';
											$rtwwwap_html1 .= 		'</thead>';
											$rtwwwap_html1 .= 		'<tbody>';
											if($rtwwwap_affiliate_wallet_transaction)
											{
												foreach( $rtwwwap_affiliate_wallet_transaction as $rtwwwap_key => $rtwwwap_value)
												{
													$rtwwwap_html1 .= '<tr>';
													$rtwwwap_html1 .= '<td>'.$rtwwwap_value['amount'].'</td>';
													$rtwwwap_html1 .= '<td>'.$rtwwwap_value['request_date'].'</td>';
													$rtwwwap_html1 .= '<td>'.$rtwwwap_value['pay_status'].'</td>';
													$rtwwwap_html1 .= '</tr>';
												}
											}
											$rtwwwap_html1 .= 		'</tbody>';
											$rtwwwap_html1 .= 	'</table>';											
	$rtwwwap_html1 .= 						'</div>
										</div>
									</div>
								</div>
						</div>';
	$rtwwwap_html1 .='</div>';

	$rtwwwap_html1 .= 	'<form method="post">';
	$rtwwwap_html1 .= 	'<div id="rtwwwap_mail_optIn">';
	$rtwwwap_html1 .= 		'<h3>'.esc_html__( "Payout", "rtwwwap-wp-wc-affiliate-program" ).'</h3>';
	if( $rtwwwap_comm_base == 2 && $rtwwwap_level_name != '' ){
		$rtwwwap_html1 .= 		'<div id="rtwwwap_email_setting">';
		$rtwwwap_html1 .= 			'<span class="rtwwwap_setting_span">'.esc_html__( "Your Affiliate Level : ", "rtwwwap-wp-wc-affiliate-program" ).'</span>';
		$rtwwwap_html1 .= 			'<span>'.esc_html( $rtwwwap_level_name ).'</span>';
		$rtwwwap_html1 .= 		'</div>';

		$rtwwwap_html1 .= 		'<div id="rtwwwap_email_setting">';
		$rtwwwap_html1 .= 			'<span class="rtwwwap_setting_span">'.esc_html__( "Your Level Commission : ", "rtwwwap-wp-wc-affiliate-program" ).'</span>';
		$rtwwwap_html1 .= 			'<span>'.sprintf( '%s%s', esc_html( $rtwwwap_level_comm ), esc_html( $rtwwwap_level_comm_type ) ).'</span>';
		$rtwwwap_html1 .= 		'</div>';
	}

	$rtwwwap_html1 .= 		'<div id="rtwwwap_email_setting">';
	$rtwwwap_html1 .= 			'<span class="rtwwwap_setting_span">'.esc_html__( "Activate Referral Emails : ", "rtwwwap-wp-wc-affiliate-program" ).'</span>';

	if( isset( $rtwwwap_referral_mail ) && $rtwwwap_referral_mail == 'on' ){
		$rtwwwap_html1 .= 		'<input type="checkbox" name="rtwwwap_referral_mail" class="rtwwwap_referral_mail" id="rtwwwap_option" checked="checked" />';
	}
	else{
		$rtwwwap_html1 .= 		'<input type="checkbox" name="rtwwwap_referral_mail" class="rtwwwap_referral_mail" id="rtwwwap_option" />';
	}
	$rtwwwap_html1 .= 		'</div>';

	$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );
	$rtwwwap_admin_paypal 	= isset( $rtwwwap_extra_features[ 'activate_paypal' ] ) ? $rtwwwap_extra_features[ 'activate_paypal' ] : 0;
	$rtwwwap_admin_stripe 	= isset( $rtwwwap_extra_features[ 'activate_stripe' ] ) ? $rtwwwap_extra_features[ 'activate_stripe' ] : 0;

	$rtwwwap_admin_paystack 	= isset( $rtwwwap_extra_features[ 'activate_paystack' ] ) ? $rtwwwap_extra_features[ 'activate_paystack' ] : 0;

	$rtwwwap_referral_code_active = isset( $rtwwwap_extra_features[ 'signup_bonus_type' ] ) ? $rtwwwap_extra_features[ 'signup_bonus_type' ] : 0;


	if( $rtwwwap_referral_code_active ){
		// $rtwwwap_user_name 		= wp_get_current_user();
		// $rtwwwap_user_name 		= $rtwwwap_user_name->data->user_login;
		// $rtwwwap_referral_code 	= $rtwwwap_user_name.'_'.$rtwwwap_user_id;

		$rtwwwap_referral_code = get_user_meta( $rtwwwap_user_id, 'rtwwwap_referee_custom_str', true );

		$rtwwwap_html1 .= 		'<div id="rtwwwap_email_setting">';
		$rtwwwap_html1 .= 			'<span class="rtwwwap_setting_span">'.esc_html__( "Your Referral Code : ", "rtwwwap-wp-wc-affiliate-program" ).'</span>';
		$rtwwwap_html1 .= 			'<span>';
		$rtwwwap_html1 .= 				$rtwwwap_referral_code;
		$rtwwwap_html1 .= 			'</span>';
		$rtwwwap_html1 .= 		'</div>';
	}
	$rtwwwap_post_id = get_user_meta($rtwwwap_user_id,'rtwwwap_coupon_assign',true );
	if($rtwwwap_post_id)
	{
		$rtwwwap_affiliate_id =  get_post_meta( $rtwwwap_post_id, 'rtwwwap_coupon_aff_id', true );
		if($rtwwwap_affiliate_id == $rtwwwap_user_id)
		{
			$rtwwwap_post = get_post( $rtwwwap_post_id );
			$rtwwwap_coupon_title = isset( $rtwwwap_post->post_title ) ? $rtwwwap_post->post_title : '';
		}
		else{
			$rtwwwap_coupon_title = esc_html__( "Not Generated by the Admin", "rtwwwap-wp-wc-affiliate-program" );	
		}
	}
	else{
		$rtwwwap_coupon_title = esc_html__( "Not Generated by the Admin", "rtwwwap-wp-wc-affiliate-program" );;
	}
	$rtwwwap_html1 .= 		'<div id="rtwwwap_coupon_code">';
	$rtwwwap_html1 .= 			'<span class="rtwwwap_setting_span">'.esc_html__( "coupon code : ", "rtwwwap-wp-wc-affiliate-program" ).'</span>';
	$rtwwwap_html1 .= 			'<span>';
	$rtwwwap_html1 .= 			$rtwwwap_coupon_title;
	$rtwwwap_html1 .= 			'</span>';
	$rtwwwap_html1 .= 		'</div>';


	$rtwwwap_html1 .= 		'<div class="rtwwwap_payment_type">';
	$rtwwwap_html1 .= 			'<span class="rtwwwap_setting_span">'.esc_html__( "Select payment method : ", "rtwwwap-wp-wc-affiliate-program" ).'</span>';
	$rtwwwap_html1 .= 			'<select class="rtwwwap_payment_method" name="rtwwwap_payment_method">';
	$rtwwwap_html1 .=				'<option value="rtwwwap_payment_not">';
	$rtwwwap_html1 .=					esc_html__( 'Select payment Method', 'rtwwwap-wp-wc-affiliate-program' );
	$rtwwwap_html1 .= 				'</option>';
	$rtwwwap_html1 .=				'<option value="rtwwwap_payment_direct" '.selected( $rtwwwap_payment_method, 'rtwwwap_payment_direct', false ).'>';
	$rtwwwap_html1 .=					esc_html__( 'Direct Bank', 'rtwwwap-wp-wc-affiliate-program' );
	$rtwwwap_html1 .= 				'</option>';

	if( $rtwwwap_admin_paypal ){
		$rtwwwap_html1 .=			'<option value="rtwwwap_payment_paypal" '.selected( $rtwwwap_payment_method, 'rtwwwap_payment_paypal', false ).'>';
		$rtwwwap_html1 .=				esc_html__( 'Paypal', 'rtwwwap-wp-wc-affiliate-program' );
		$rtwwwap_html1 .= 			'</option>';
	}

	if( $rtwwwap_admin_stripe ){
		$rtwwwap_html1 .=			'<option value="rtwwwap_payment_stripe" '.selected( $rtwwwap_payment_method, 'rtwwwap_payment_stripe', false ).'>';
		$rtwwwap_html1 .=				esc_html__( 'Stripe', 'rtwwwap-wp-wc-affiliate-program' );
		$rtwwwap_html1 .= 			'</option>';
	}
	if( $rtwwwap_admin_paystack ){
		$rtwwwap_html1 .=			'<option value="rtwwwap_payment_paystack" '.selected( $rtwwwap_payment_method, 'rtwwwap_payment_paystack', false ).'>';
		$rtwwwap_html1 .=				esc_html__( 'Paystack', 'rtwwwap-wp-wc-affiliate-program' );
		$rtwwwap_html1 .= 			'</option>';
	}
	

	$rtwwwap_html1 .=	  		'</select>';
	$rtwwwap_html1 .= 		'</div>';

	if( $rtwwwap_payment_method == 'rtwwwap_payment_direct' ){
		$rtwwwap_html1 .= 	'<div class="rtwwwap_direct">';
	}
	else{
		$rtwwwap_html1 .= 	'<div class="rtwwwap_direct rtwwwap_payment_hidden">';
	}
	$rtwwwap_html1 .= 		'<span class="rtwwwap_setting_span">'.esc_html__( "Bank Details : ", "rtwwwap-wp-wc-affiliate-program" ).'</span>';
	$rtwwwap_html1 .= 		'<textarea rows="3" name="rtwwwap_direct" class="rtwwwap_direct" placeholder="'.esc_html__( "Enter your Bank A/c details here", "rtwwwap-wp-wc-affiliate-program" ).'" >';
	$rtwwwap_html1 .= 			$rtwwwap_direct_details;
	$rtwwwap_html1 .= 		'</textarea>';
	$rtwwwap_html1 .= 	'</div>';

	if( $rtwwwap_admin_paypal && $rtwwwap_payment_method == 'rtwwwap_payment_paypal' ){
		$rtwwwap_html1 .= 	'<div class="rtwwwap_paypal">';
	}
	else{
		$rtwwwap_html1 .= 	'<div class="rtwwwap_paypal rtwwwap_payment_hidden">';
	}
	$rtwwwap_html1 .= 		'<span class="rtwwwap_setting_span">'.esc_html__( "Paypal : ", "rtwwwap-wp-wc-affiliate-program" ).'</span>';
	$rtwwwap_html1 .= 		'<input type="email" name="rtwwwap_paypal_email" class="rtwwwap_paypal_email" placeholder="'.esc_attr__( "Enter your Paypal email here", "rtwwwap-wp-wc-affiliate-program" ).'" value="'.esc_attr( $rtwwwap_paypal_email ).'" />';
	$rtwwwap_html1 .= 	'</div>';

	if( $rtwwwap_admin_stripe && $rtwwwap_payment_method == 'rtwwwap_payment_stripe' ){
		$rtwwwap_html1 .= 	'<div class="rtwwwap_stripe">';
	}
	else{
		$rtwwwap_html1 .= 	'<div class="rtwwwap_stripe rtwwwap_payment_hidden">';
	}
	$rtwwwap_html1 .= 		'<span class="rtwwwap_setting_span">'.esc_html__( "Stripe : ", "rtwwwap-wp-wc-affiliate-program" ).'</span>';
	$rtwwwap_html1 .= 		'<input type="email" name="rtwwwap_stripe_email" class="rtwwwap_stripe_email" placeholder="'.esc_attr__( "Enter your Stripe email here", "rtwwwap-wp-wc-affiliate-program" ).'" value="'.esc_attr( $rtwwwap_stripe_email ).'" />';
	$rtwwwap_html1 .= 	'</div>';

	if( $rtwwwap_admin_paystack && $rtwwwap_payment_method == 'rtwwwap_payment_paystack' ){
		$rtwwwap_html1 .= 	'<div class="rtwwwap_paystack">';
	}
	else{
		$rtwwwap_html1 .= 	'<div class="rtwwwap_paystack rtwwwap_payment_hidden">';
	}
	$rtwwwap_html1 .= 		' <div class="rtwwwap_bank_details"><span class="rtwwwap_setting_span">'.esc_html__( "Bank Account Number : ", "rtwwwap-wp-wc-affiliate-program" ).'</span>';
	$rtwwwap_html1 .= 		'<input type="text" name="rtwwwap_paystack_bank_account" class="rtwwwap_paystack_bank_account" placeholder="'.esc_attr__( "Enter account number", "rtwwwap-wp-wc-affiliate-program" ).'" value="'.esc_attr( $rtwwwap_paystack_bank_account ).'" /> </div>';
	$rtwwwap_html1 .= 		'<div class="rtwwwap_bank_details"><span class="rtwwwap_setting_span">'.esc_html__( "Bank code : ", "rtwwwap-wp-wc-affiliate-program" ).'</span>';
	$rtwwwap_html1 .= 		'<input type="text" name="rtwwwap_paystack_bank_code" class="rtwwwap_paystack_bank_code" placeholder="'.esc_attr__( "Enter bank code", "rtwwwap-wp-wc-affiliate-program" ).'" value="'.esc_attr( $rtwwwap_paystack_bank_code ).'" /></div>';
	$rtwwwap_html1 .= 	'</div>';

	$rtwwwap_html1 .= 		'<button type="submit" name="rtwwwap_payout_save" id="rtwwwap_payout_save">'.esc_html__( "Save Details", "rtwwwap-wp-wc-affiliate-program" ).'</button>';

	$rtwwwap_html1 .= 	'</div>';
	$rtwwwap_html1 .= 	'</form>';

	return $rtwwwap_html1;
}
/**
 *
 * Custom fields of register form.
 *
 */
if(!function_exists('rtwwwap_custom_form_fields_data'))
{
	function rtwwwap_custom_form_fields_data($rtwwwap_userdata){
		$rtwwwap_reg_temp_features = get_option( 'rtwwwap_reg_temp_opt' );
		$rtwwwap_reg_custom_fields = isset($rtwwwap_reg_temp_features['custom-input']) ? $rtwwwap_reg_temp_features['custom-input'] : array();

		$rtwwwap_html = '';
		if(is_array($rtwwwap_reg_custom_fields) && !empty($rtwwwap_reg_custom_fields)){
			foreach ($rtwwwap_reg_custom_fields as $custom_fields) {
				$rtwwwap_reg_user_custom_field = isset($rtwwwap_userdata[$custom_fields['custom-input-id']][0]) ? $rtwwwap_userdata[$custom_fields['custom-input-id']][0] : '';
				if(isset($custom_fields['custom-input-type'])){
					if(($custom_fields['custom-input-type'] == 'text' || $custom_fields['custom-input-type'] == 'number')){
						$rtwwwap_html .= 					'<label>'.esc_html__( $custom_fields['custom-input-label'], "rtwwwap-wp-wc-affiliate-program" ).'</label>';
						$rtwwwap_html .= 	'<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="far fa-envelope"></i></span><input type="'.$custom_fields['custom-input-type'].'" name="'.$custom_fields['custom-input-id'].'"  id="'.$custom_fields['custom-input-id'].'" class="'.$custom_fields['custom-input-class'].'" placeholder="'.esc_attr__( $custom_fields['custom-input-label'], "rtwwwap-wp-wc-affiliate-program" ).'"" value="'.$rtwwwap_reg_user_custom_field.'" /></div>';
					}elseif ($custom_fields['custom-input-type'] == 'textarea') {
						$rtwwwap_html .= 					'<label>'.esc_html__( $custom_fields['custom-input-label'], "rtwwwap-wp-wc-affiliate-program" ).'</label>';
						$rtwwwap_html .=	'<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="far fa-envelope"></i></span><textarea name="'.$custom_fields['custom-input-id'].'"  id="'.$custom_fields['custom-input-id'].'" class="'.$custom_fields['custom-input-class'].'" placeholder="'.esc_attr__( $custom_fields['custom-input-label'], "rtwwwap-wp-wc-affiliate-program" ).'">'.$rtwwwap_reg_user_custom_field.'</textarea></div>';
					}elseif ($custom_fields['custom-input-type'] == 'checkbox') {
						$rtwwwap_html .= 					'<label>'.esc_html__( $custom_fields['custom-input-label'], "rtwwwap-wp-wc-affiliate-program" ).'</label>';
						$rtwwwap_checkbox_options = explode('|',$custom_fields['custom-input-options']);
						if(is_array($rtwwwap_checkbox_options) && !empty($rtwwwap_checkbox_options)){
							$rtwwwap_html .= '<div class="rtwwwap-custom-checkbox">';
							foreach ($rtwwwap_checkbox_options as $value) {
								$rtwwwap_value_checked          =         isset($rtwwwap_userdata[$value][0]) ? $rtwwwap_userdata[$value][0] : ''; 
								$rtwwwap_html .= 	'<label><input type="'.$custom_fields['custom-input-type'].'" name="'.$value.'"  id="'.$custom_fields['custom-input-id'].'" class="'.$custom_fields['custom-input-class'].'" placeholder="'.esc_attr__( $custom_fields['custom-input-label'], "rtwwwap-wp-wc-affiliate-program" ).'"" value="'.esc_attr__(trim($value),"rtwwwap-wp-wc-affiliate-program").'" '.checked($rtwwwap_value_checked,$value,false). ' />'.esc_html__($value,"rtwwwap-wp-wc-affiliate-program").'</label>';

							}
							$rtwwwap_html .= '</div>';
						}
					}elseif ($custom_fields['custom-input-type'] == 'radio') {
						$rtwwwap_html .= 					'<label>'.esc_html__( $custom_fields['custom-input-label'], "rtwwwap-wp-wc-affiliate-program" ).'</label>';
						$rtwwwap_checkbox_options = explode('|',$custom_fields['custom-input-options']);
						if(is_array($rtwwwap_checkbox_options) && !empty($rtwwwap_checkbox_options)){
							$rtwwwap_html .= '<div class="rtwwwap-custom-radio">';
							foreach ($rtwwwap_checkbox_options as $value) {
								$rtwwwap_html .= 	'<label for="'.$custom_fields['custom-input-id'].'"><input type="'.$custom_fields['custom-input-type'].'" name="'.$custom_fields['custom-input-id'].'"  id="'.$custom_fields['custom-input-id'].'" class="'.$custom_fields['custom-input-class'].'" placeholder="'.esc_attr__( $custom_fields['custom-input-label'], "rtwwwap-wp-wc-affiliate-program" ).'"" value="'.esc_attr__(trim($value),"rtwwwap-wp-wc-affiliate-program").'" '.checked(isset($rtwwwap_userdata[$custom_fields['custom-input-id']][0]) ? $rtwwwap_userdata[$custom_fields['custom-input-id']][0] : ''  ,$value,false). ' />'.esc_html__($value,"rtwwwap-wp-wc-affiliate-program").'</label>';

							}
							$rtwwwap_html .= '</div>';
						}
					}
					elseif ($custom_fields['custom-input-type'] == 'select') {
						$rtwwwap_html .= 					'<label>'.esc_html__( $custom_fields['custom-input-label'], "rtwwwap-wp-wc-affiliate-program" ).'</label>';
						$rtwwwap_checkbox_options = explode('|',$custom_fields['custom-input-options']);
						if(is_array($rtwwwap_checkbox_options) && !empty($rtwwwap_checkbox_options)){
							$rtwwwap_html .= 	'<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="far fa-envelope"></i></span><select name="'.$custom_fields['custom-input-id'].'"  id="'.$custom_fields['custom-input-id'].'" class="'.$custom_fields['custom-input-class'].'" >';
							foreach ($rtwwwap_checkbox_options as $options_value) {
								$rtwwwap_html .= 	'<option '.selected(isset($rtwwwap_userdata[$custom_fields['custom-input-id']][0]) ? $rtwwwap_userdata[$custom_fields['custom-input-id']][0] : '',$options_value,false). ' value="'.esc_attr__(trim($options_value ),"rtwwwap-wp-wc-affiliate-program").'" >'.esc_html__(trim($options_value),"rtwwwap-wp-wc-affiliate-program").'</option>';
							}
							$rtwwwap_html .= 	'</select></div>';
						}
					}
				}
			}
		}
		return $rtwwwap_html;
	}
}

//profile
if( isset( $_GET[ 'rtwwwap_tab' ] ) && $_GET[ 'rtwwwap_tab' ] == 'profile' ){
	$rtwwwap_user_id = get_current_user_id();
	if ( isset( $_POST ) && !empty( $_POST ) ) {
	if(isset($_POST['rtwwwap_profile_save']) && !empty($_POST['rtwwwap_profile_save'])){
		foreach ($_POST as $meta_key => $meta_value) {
			update_user_meta($rtwwwap_user_id,$meta_key,$meta_value);
		}
	}
}

	$rtwwwap_userdata = get_user_meta($rtwwwap_user_id);
	$rtwwwap_user = get_userdata($rtwwwap_user_id);
	$rtwwwap_html1 = '';
	$rtwwwap_html1 = 	'<form method="post">';
	$rtwwwap_html1 .= 	'<div id="rtwwwap_mail_optIn">';
	$rtwwwap_html1 .= 		'<h3>'.esc_html__( "Profile", "rtwwwap-wp-wc-affiliate-program" ).'</h3>';
	$rtwwwap_html1 .= 					'<label>'.esc_html__( "Username", "rtwwwap-wp-wc-affiliate-program" ).'</label>';
	$rtwwwap_html1 .= 			'<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="far fa-user"></i></span><input type="text" name="user_login" placeholder="'.esc_attr__( "Username", "rtwwwap-wp-wc-affiliate-program" ).'" value="'.$rtwwwap_userdata['nickname'][0].'" disabled></div>';

	$rtwwwap_html1 .= 					'<label>'.esc_html__( "Email", "rtwwwap-wp-wc-affiliate-program" ).'</label>';
	$rtwwwap_html1 .= 			'<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="far fa-user"></i></span><input type="email" name="user_email" placeholder="'.esc_attr__( "Email", "rtwwwap-wp-wc-affiliate-program" ).'" value="'.$rtwwwap_user->user_email.'" disabled></div>';

	$rtwwwap_html1 .= 					'<label>'.esc_html__( "First Name", "rtwwwap-wp-wc-affiliate-program" ).'</label>';
	$rtwwwap_html1 .= 			'<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="far fa-user"></i></span><input type="text" name="first_name" placeholder="'.esc_attr__( "First Name", "rtwwwap-wp-wc-affiliate-program" ).'" value="'.$rtwwwap_userdata['first_name'][0].'"></div>';

	$rtwwwap_html1 .= 					'<label>'.esc_html__( "Last Name", "rtwwwap-wp-wc-affiliate-program" ).'</label>';
	$rtwwwap_html1 .= 			'<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="far fa-user"></i></span><input type="text" name="last_name" placeholder="'.esc_attr__( "Last Name", "rtwwwap-wp-wc-affiliate-program" ).'" value="'.$rtwwwap_userdata['last_name'][0].'"></div>';

	
	$rtwwwap_html1 .= 				rtwwwap_custom_form_fields_data($rtwwwap_userdata);

	// change password code starts

	$rtwwwap_html .= '<div class="rtwwwap_main_wallet_wrapper_change_psw">
        								<div class="rtwwwap_change_password_model">
        									<div class="rtwwwap_change_psw_model_dialog">
        										<div class="rtwwwap_wallet_model_content">

												<div class="rtwwwap_wallet_model_header">
														<h3>'.esc_html__( 'Change your password here', 'rtwwwap-wp-wc-affiliate-program' ).'</h3>
														<div class="rtwwwap_modal_close">
															<i class="fas fa-times"></i>
														</div>
												</div>
        											<div class="rtwwwap_wallet_model_body">

                                                        <div class= "rtwwwap_change_pass_field">
                                                            <div class="rtwwwap_amount_text">
            													<label>
            													'.esc_html__( "Old Password", "rtwwwap-wp-wc-affiliate-program" ).'
            													</label>
            													<input type="password" name ="rtwwwap_old_password" class="rtwwwap_old_password" value="" placeholder="enter the old password" required>
        												    </div>
        												    <div class="rtwwwap_amount_text">
            													<label>
            													'.esc_html__( "New Password", "rtwwwap-wp-wc-affiliate-program" ).'
            													</label>
            													<input type="password" name ="rtwwwap_password" class="rtwwwap_password" value="" placeholder="enter new password" required>
        												    </div>
        												    <div class="rtwwwap_amount_text">
            													<label>
            													'.esc_html__( "Confirm password", "rtwwwap-wp-wc-affiliate-program" ).'
            													</label>
            													<input type="password" name ="rtwwwap_confirm_password" class="rtwwwap_confirm_password" value="" placeholder="confirm your password" required>
        												    </div>
                                                        </div>
        											</div>
        											<div class="rtwwwap_wallet_model_footer">
        												<button class="rtwwwap_save_password" id="rtwwwap_save_psw" data-wallet_amount=""  data-payment_method="">'.esc_html__( "Save", "rtwwwap-wp-wc-affiliate-program" ).'</button>
        												<button class="rtwwwap_cancel_btn_with">'.esc_html__( "cancel", "rtwwwap-wp-wc-affiliate-program" ).'</button>
        											</div>
        										</div>
        									</div>
        								</div>
        					</div>';

	// ends 

	$rtwwwap_html1 .= 		'</div">';
	$rtwwwap_html1 .= 		'<div><input type="submit" class="rtwwwap_profile_save" value="'.esc_attr__( "Update Details", "rtwwwap-wp-wc-affiliate-program" ).'" id="rtwwwap_profile_save" name="rtwwwap_profile_save">
	<input type="button" class="rtwwwap_profile_change_psw" value="'.esc_html__( "Change password", "rtwwwap-wp-wc-affiliate-program" ).'" id="rtwwwap_change_psw" name="rtwwwap_profile_save"></div>';
	$rtwwwap_html1 .= 	'</form">';
	return $rtwwwap_html1;
}

// custom banner
if( isset( $_GET[ 'rtwwwap_tab' ] ) && $_GET[ 'rtwwwap_tab' ] == 'custom_banner' ){
	$rtwwwap_custom_banner = get_option( 'rtwwwap_custom_banner_opt' );
	if(	$rtwwwap_custom_banner != '' )
	{
		$rtwwwap_count = 1;
		$rtwwwap_html1 = '';
		$rtwwwap_html1 .= 	'<div class="rtwwwap_custom_banner_container">';
		foreach($rtwwwap_custom_banner as $key => $value)
		{
			
			$rtwwwap_image_src = wp_get_attachment_url($value['image_id']);		
			$rtwwwap_image_width = $value['image_width']/2;
			$rtwwwap_image_height = (int)$value['image_height'];
			if( $rtwwwap_image_height > 350)
			{	
				$rtwwwap_image_height = $rtwwwap_image_height/2 ; 
			}
		
	
			$rtwwwap_html1 .= 	'<div class ="rtwwwap_custom_banner_product" style=" width:'.$rtwwwap_image_width.'px;height:auto;">';
			$rtwwwap_html1 .=        '<div class = "rtwwwap_banner_no">'.esc_html("Banner No.").esc_attr__($rtwwwap_count).'</div>';
			$rtwwwap_html1 .= 				'<div class ="rtwwwap_custom_banner_product_image" style="height:'.$rtwwwap_image_height.'px;">';
			$rtwwwap_html1 .=					'<img class="rtwwwap_banner_image"  src="'.$rtwwwap_image_src.'" >';
			$rtwwwap_html1 .=				 '</div>';
			$rtwwwap_html1 .=				'<div>';
			$rtwwwap_html1 .=				'<span class="rtwwwap_image_size_detail">Image Size : '.$value['image_width'].'</span>';
			$rtwwwap_html1 .=				'<span class="rtwwwap_image_size_detail"> '.esc_html__( " x ", "rtwwwap-wp-wc-affiliate-program" ).$value['image_height'].'</span>';
			$rtwwwap_html1 .=				'</div>';
			$rtwwwap_html1 .=				 '<label class="rtwwwap_copy_info" >'.esc_html__( " Copy and paste the code into your Website", "rtwwwap-wp-wc-affiliate-program" ).'</label>';	
			$rtwwwap_html1 .=				 '<div class="rtwwwap_banner_copy_text" >'.esc_html__( "Copied", "rtwwwap-wp-wc-affiliate-program" ).'</div>';
			$rtwwwap_html1 .= 			'<button  data-image_id ="'.$rtwwwap_image_src.'" data-target_link ="'.$value['target_link'].'" name="rtwwwap_custom_banner_copy_html" class="rtwwwap_custom_banner_copy_html" data-image_width ="'.$value['image_width'].'" data-image_height ="'.$value['image_height'].'">'.esc_html__( "COPY HTML", "rtwwwap-wp-wc-affiliate-program" ).'</button>';
			$rtwwwap_html1 .= 	'</div>'; 

			$rtwwwap_count  = $rtwwwap_count + 1;	 
		
		}
		$rtwwwap_html1 .= 	'</div>';
		return $rtwwwap_html1;
	}
}

	// mlm
$rtwwwap_mlm = get_option( 'rtwwwap_mlm_opt' );
if( isset( $rtwwwap_mlm[ 'activate' ] ) && $rtwwwap_mlm[ 'activate' ] == 1 )
{
	if( isset( $_GET[ 'rtwwwap_tab' ] ) && $_GET[ 'rtwwwap_tab' ] == 'mlm' )
	{
		global $wpdb;
		$rtwwwap_mlm_type = '';
		$rtwwwap_mlm_commission = $wpdb->get_var($wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `aff_id` = '%d' AND `type` = %d ", $rtwwwap_user_id,4 )) ;
	
						
		$rtwwwap_mlm_childs = isset( $rtwwwap_mlm[ 'child' ] ) ? $rtwwwap_mlm[ 'child' ] : '1';
		if( isset( $rtwwwap_mlm[ 'mlm_type' ] ) )
		{
			if( $rtwwwap_mlm[ 'mlm_type' ] == 0 )
			{
				$rtwwwap_mlm_type = esc_html__( "Binary", "rtwwwap-wp-wc-affiliate-program" );
			}
			else if( $rtwwwap_mlm[ 'mlm_type' ] == 1 )
			{
				$rtwwwap_mlm_type = esc_html__( "Forced Matrix", "rtwwwap-wp-wc-affiliate-program" );
			}
			else if( $rtwwwap_mlm[ 'mlm_type' ] == 2 )
			{
				$rtwwwap_mlm_type = esc_html__( "Unilevel", "rtwwwap-wp-wc-affiliate-program" );
			}
			else{
				$rtwwwap_mlm_type = esc_html__( "Unlimited", "rtwwwap-wp-wc-affiliate-program" );
				$rtwwwap_mlm_childs = 1;
			}
		}
		$rtwwwap_html1 = '';
		$rtwwwap_html1 .= 	'<table id="rtwwwap_commission">';
		$rtwwwap_html1 .= 		'<div class="rtwwwap_commissionws_wrapper">';
		$rtwwwap_html1 .= 			esc_html__( 'MLM Plan -', 'rtwwwap-wp-wc-affiliate-program' );
		$rtwwwap_html1 .= 			'<span>'.esc_html( $rtwwwap_mlm_type ).'</span>';
		$rtwwwap_html1 .= 		'</div>';

		$rtwwwap_html1 .= 		'<div class="rtwwwap_commissionws_wrapper">';
		$rtwwwap_html1 .= 			esc_html__( 'Number of Childs to start earning commission -', 'rtwwwap-wp-wc-affiliate-program' );
		$rtwwwap_html1 .= 			'<span>'.esc_html( $rtwwwap_mlm_childs ).'</span>';
		$rtwwwap_html1 .= 		'</div>';

		if($rtwwwap_mlm_commission != "")
		{
			$rtwwwap_html1 .= 		'<div class="rtwwwap_commissionws_wrapper">';
			$rtwwwap_html1 .= 			esc_html__( 'Total MLM commission earned -', 'rtwwwap-wp-wc-affiliate-program' );
			$rtwwwap_html1 .= 			'<span>'.esc_html( $rtwwwap_mlm_commission.$rtwwwap_currency_sym ).'</span>';
			$rtwwwap_html1 .= 		'</div>';
		}
		$rtwwwap_html1 .= 		'<thead>';
		$rtwwwap_html1 .= 			'<tr>';
		$rtwwwap_html1 .= 				'<th colspan="3">';
		$rtwwwap_html1 .= 		 			esc_html__( 'MLM Levels' , 'rtwwwap-wp-wc-affiliate-program');
		$rtwwwap_html1 .= 				'</th>';
		$rtwwwap_html1 .= 			'</tr>';
		$rtwwwap_html1 .= 		'</thead>';

		$rtwwwap_html1 .= 		'<tbody>';
		$rtwwwap_html1 .= 			'<tr>';
		$rtwwwap_html1 .= 				'<td><b>';
		$rtwwwap_html1 .= 		 			esc_html__( 'Level', 'rtwwwap-wp-wc-affiliate-program' );
		$rtwwwap_html1 .= 				'</b></td>';

		$rtwwwap_html1 .= 				'<td><b>';
		$rtwwwap_html1 .= 		 			esc_html__( 'Commission Type', 'rtwwwap-wp-wc-affiliate-program' );
		$rtwwwap_html1 .= 				'</b></td>';

		$rtwwwap_html1 .= 				'<td><b>';
		$rtwwwap_html1 .= 		 			esc_html__( "Commission Amount", 'rtwwwap-wp-wc-affiliate-program' );
		$rtwwwap_html1 .= 				'</b></td>';
		$rtwwwap_html1 .= 			'</tr>';

		if( !empty( $rtwwwap_mlm[ 'mlm_levels' ] ) )
		{
			foreach( $rtwwwap_mlm[ 'mlm_levels' ] as $rtwwwap_mlm_key => $rtwwwap_mlm_value )
			{
				$rtwwwap_selected_level = '';
				if( isset( $rtwwwap_mlm[ 'mlm_levels' ][ $rtwwwap_mlm_key ][ 'mlm_level_comm_type' ] ) )
				{
					if( $rtwwwap_mlm[ 'mlm_levels' ][ $rtwwwap_mlm_key ][ 'mlm_level_comm_type' ] == 0 )
					{
						$rtwwwap_selected_level = esc_html__( "Percentage", 'rtwwwap-wp-wc-affiliate-program' );
					}
					if( $rtwwwap_mlm[ 'mlm_levels' ][ $rtwwwap_mlm_key ][ 'mlm_level_comm_type' ] == 1 )
					{
						$rtwwwap_selected_level = esc_html__( "Fixed", 'rtwwwap-wp-wc-affiliate-program' );
					}
				}
				$rtwwwap_comm_amount = ( isset( $rtwwwap_mlm[ 'mlm_levels' ][ $rtwwwap_mlm_key ][ 'mlm_level_comm_amount' ] ) ) ? $rtwwwap_mlm[ 'mlm_levels' ][ $rtwwwap_mlm_key ][ 'mlm_level_comm_amount' ] : '0';

				$rtwwwap_html1 .= 			'<tr>';
				$rtwwwap_html1 .= 				'<td>';
				$rtwwwap_html1 .= 					esc_html( $rtwwwap_mlm_key );
				$rtwwwap_html1 .= 				'</td>';
				$rtwwwap_html1 .= 				'<td>';
				$rtwwwap_html1 .= 					esc_html( $rtwwwap_selected_level );
				$rtwwwap_html1 .= 				'</td>';
				$rtwwwap_html1 .= 				'<td>';
				$rtwwwap_html1 .= 					esc_attr( $rtwwwap_comm_amount );
				$rtwwwap_html1 .= 				'</td>';
				$rtwwwap_html1 .= 			'</tr>';
			}
		}
		$rtwwwap_html1 .= 		'</tbody>';
		$rtwwwap_html1 .= 	'</table>';

		$rtwwwap_html1 .= 	'<div>';
		$rtwwwap_html1 .= 		'<span id="rtwwwap_show_mlm_chain" data-user_id="'.$rtwwwap_user_id.'">';
		$rtwwwap_html1 .= 			esc_html__( "Show MLM chain", 'rtwwwap-wp-wc-affiliate-program' );
		$rtwwwap_html1 .= 		'</span>';
		$rtwwwap_html1 .= 		'<input type="checkbox" id="rtwwwap_show_active_only" disabled="disabled" />';
		$rtwwwap_html1 .= 		'<label for="rtwwwap_checkbox_active_mlm">'.esc_html__( "Show In-Active members also", "rtwwwap-wp-wc-affiliate-program" ).'</label>';
		$rtwwwap_html1 .= 		'<p class="rtwwwap_mlm_chain_not">';
		$rtwwwap_html1 .= 			esc_html__( "MLM chain is not proper, activate/ deactivate the members to make the chain according to your MLM plan. Once done reload the page to see the updated MLM chain.", 'rtwwwap-wp-wc-affiliate-program' );
		$rtwwwap_html1 .=	    '</p>';
		$rtwwwap_html1 .= 	'</div>';
		$rtwwwap_html1 .= 	'<div id="rtwwwap_mlm_chain_struct"></div>';
		$rtwwwap_html1 .= 	'<div id="rtwwwap_mlm_show"></div>';

		return $rtwwwap_html1;
	}
}