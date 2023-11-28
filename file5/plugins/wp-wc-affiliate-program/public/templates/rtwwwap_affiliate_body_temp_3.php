<?php

$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );
$rtwwwap_decimal_place = isset($rtwwwap_extra_features['decimal_places']) ? $rtwwwap_extra_features['decimal_places'] : 1;
$rtwwwap_decimal_separator = isset($rtwwwap_extra_features['decimal_separator'])? $rtwwwap_extra_features['decimal_separator'] : '.';
$rtwwwap_thousand_separator = isset($rtwwwap_extra_features['thousand__separator']) ? $rtwwwap_extra_features['thousand__separator'] : ',';

$rtwwwap_noti_option = get_option("rtwwwap_noti_arr");

if( RTWWWAP_IS_WOO == 1 ){
    $rtwwwap_currency_sym = esc_html( get_woocommerce_currency_symbol() );
}
else{
    require_once( RTWWWAP_DIR.'includes/rtwaffiliatehelper.php' );

    $rtwwwap_currency		= isset( $rtwwwap_extra_features[ 'currency' ] ) ? $rtwwwap_extra_features[ 'currency' ] : 'USD';
    $rtwwwap_curr_obj 		= new RtwAffiliateHelper();
    $rtwwwap_currency_sym 	= $rtwwwap_curr_obj->rtwwwap_curr_symbol( $rtwwwap_currency );
}
global $wpdb;

    $rtwwwap_user_id 			= get_current_user_id();
    $rtwwwap_user_name          =   wp_get_current_user();
    
//// overview tab

    $rtwwwap_total_referrals 	= $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(`id`) as total_referrals FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `aff_id`=%d", $rtwwwap_user_id ) );
    $rtwwwap_pending_comm 		= (int)$wpdb->get_var( $wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `aff_id`=%d AND `status`=%d AND `capped`!=%d", $rtwwwap_user_id, 0, 1 ) );
    $rtwwwap_approved_comm 		= (int)$wpdb->get_var( $wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `aff_id`=%d AND `status`=%d", $rtwwwap_user_id, 1 ) );

    $rtwwwap_total_comm 		= $wpdb->get_var( $wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `aff_id`=%d AND `status`=%d", $rtwwwap_user_id, 2 ) );
    
    $rtwwwap_total_comm 		= $rtwwwap_total_comm + $rtwwwap_approved_comm;
    $rtwwwap_wallet 			= get_user_meta( $rtwwwap_user_id, 'rtw_user_wallet', true );
    $rtwwwap_wallet   			= isset($rtwwwap_wallet) ? $rtwwwap_wallet : '0';
    if($rtwwwap_wallet == '')
    {
        $rtwwwap_wallet = 0; 
    }
    $rtwwwap_theme = get_user_meta( $rtwwwap_user_id, 'rtwwwap_theme', true );

    $rtwwwap_theme_message = $rtwwwap_theme;

    if($rtwwwap_theme == "dark")
    {
     $rtwwwap_theme = "checked";
    }
    else {
        $rtwwwap_theme = "";
        
         }

        
$rtwwwap_html1 = '';
$rtwwwap_html1 .=	'
        <div class="rtwwwap-right-section">
            <div class="rtwwwap-top-bar">
                <div class="rtwwwap-top-bar-inner rtwwwap_row">
                    <div class="rtwwwap_top_bar_left_side">
                        <div class="rtwwwap_row">
                            <div class="rtwwwap_sidebar_icon">
                                <i class="fas fa-bars"></i>
                            </div>
                            <div class="rtwwwap_theme_change_toggle_wrapper">
                                <label class="rtwwwap_switch">
                                    <input type="checkbox" class="rtwwwap_theme_toggle" '.$rtwwwap_theme.'>
                                    <span class="rtwwwap_slider rtwwwap_round"></span>
                                </label>';
                                if($rtwwwap_theme_message == "dark"){
                                    $rtwwwap_html1 .=' <span class="rtwwwap_theme_message">'.esc_html__('Enable Lite Mode','rtwwwap-woocommerce-membership').'</span>';
                                }
                                else{
                                $rtwwwap_html1 .= '<span class="rtwwwap_theme_message">'.esc_html__('Enable Dark Mode','rtwwwap-woocommerce-membership').'</span>';
                                }
                            $rtwwwap_html1 .=  	'</div>
                        </div>
                    </div>';
                    $rtwwwap_login_page_id = get_option('rtwwwap_login_page_id');
                    $rtwwwap_affiliate_page_id = get_option('rtwwwap_affiliate_page_id');
                    if($rtwwwap_login_page_id)
                    {
                        $redirect_url = get_permalink($rtwwwap_login_page_id);
                    }
                    else{
                        $redirect_url = get_permalink($rtwwwap_login_page_id);
                    }
                   

                    $rtwwwap_html1 .= '<div class="rtwwwap_top_bar_right_side">';
                        $rtwwwap_html1 .= '<div class="rtwwwap_row rtwwwap_justify_end">';
                    if(!empty($rtwwwap_noti_option))
                    {
                        $rtwwwap_user_noti_id = get_user_meta($rtwwwap_user_id,'rtwwwap_user_noti_id');

                        $rtwwwap_user_seen_noti = isset($rtwwwap_user_noti_id[0]) ? count($rtwwwap_user_noti_id[0]) : 0;
                        $rtwwwap_count_noti = isset($rtwwwap_noti_option) ? count($rtwwwap_noti_option) : 0;
                        $rtwwwap_final_count_show = $rtwwwap_count_noti - $rtwwwap_user_seen_noti;

                        $rtwwwap_html1 .= '<div class="rtwwwap_toggle_notification_wrapper">
                            <span class="rtwwwap_toggle_notification">
                                <i class="fas fa-bell"></i>
                                <span class="rtwwwap_message_count">'.esc_attr($rtwwwap_final_count_show).'</span>
                            </span>
                            <div class="rtwwwap_notification_dropdown_wrapper">
                                <div class="rtwwwap_notification_dropdown_list">
                                    <ul class="rtwwwap_notification_dropdown_list_wrapper">';

                            foreach( array_reverse($rtwwwap_noti_option) as $key => $value)
                            {	 
                                if(isset($rtwwwap_user_noti_id[0]) && in_array($key,$rtwwwap_user_noti_id[0]))
                                {
                             $rtwwwap_html1 .= '<li class="rtwwwap_noti_li_parent rtwwwap_notification_list_present" data-title="'.$value['title'].'" data-noti_id="'.$key.'" data-content="'.$value['content'].'">';
                                }
                                else{
                                    $rtwwwap_html1 .= '<li class="rtwwwap_noti_li_parent rtwwwap_notification_list" data-title="'.$value['title'].'" data-noti_id="'.$key.'" data-content="'.$value['content'].'">';
                                }
                           
                        $rtwwwap_html1 .= '<div class="rtwwwap_border_bottom rtwwwap_row">
                                                <div class="rtwwwap_notification_icon">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M12.0964 16.6667C12.0964 18.5077 10.6039 20 8.76297 20C6.922 20 5.42969 18.5077 5.42969 16.6667C5.42969 14.8257 6.922 13.3333 8.76297 13.3333C10.6039 13.3333 12.0964 14.8257 12.0964 16.6667Z" fill="#22215B"></path>
                                                    <path d="M14.603 9.93424C11.778 9.5308 9.59641 7.1016 9.59641 4.16673C9.59641 3.33329 9.77463 2.54258 10.0905 1.82496C9.66385 1.72501 9.22058 1.66673 8.76297 1.66673C5.54642 1.66673 2.92969 4.2833 2.92969 7.50001V9.82331C2.92969 11.4725 2.20718 13.0292 0.939636 14.1008C0.61554 14.3774 0.429688 14.7817 0.429688 15.2083C0.429688 16.0126 1.08383 16.6667 1.88797 16.6667H15.638C16.4423 16.6667 17.0964 16.0126 17.0964 15.2083C17.0964 14.7817 16.9106 14.3774 16.5781 14.0933C15.3481 13.0525 14.6347 11.5416 14.603 9.93424Z" fill="#22215B"></path>
                                                    <path d="M19.5964 4.16672C19.5964 6.4679 17.7309 8.33328 15.4297 8.33328C13.1285 8.33328 11.263 6.4679 11.263 4.16672C11.263 1.86554 13.1285 0 15.4297 0C17.7309 0 19.5964 1.86554 19.5964 4.16672Z" fill="#4CE364"></path>
                                                    </svg>
                                                </div>
                                                <div class="rtwwwap_notification_body">
                                                    <h6>'.esc_attr($value['title']).'</h6>
                                                    <small>'.esc_attr($value['time']).'</small>
                                                </div>
                                            </div>
                                        </li>';
                            }
                    $rtwwwap_html1 .= '</ul>
                                </div>
                            </div>
                            <div class="rtwwwap_notification_modal_wrapper" style="display: none;">
                          <div class="rtwwwap_notification_modal">
                            <div class="rtwwwap_modal_dialog">
                              <div class="rtwwwap-modal-header">
                                  <span class="rtwwwap_modal_title"></span>
                                  <i class="fas fa-times rtwwwap_close_cont_modal" aria-hidden="true"></i>
                              </div>
                              <div class="rtwwwwap_noti_content_body">
                                <p class="rtwwwwap_noti_content">
                                </p>
                              </div>
                            </div>
                          </div>	
                        </div>';

                        
                        $rtwwwap_html1 .=     '</div>';
                    }
                            $rtwwwap_html1 .=     '<div class="rtwwwap_logout">';
                            $rtwwwap_html1 .=  		'<a class="rtwwwap_logout_button" href='.wp_logout_url($redirect_url).'>
                                                        <i class="fas fa-sign-out-alt"></i>
                                                        <span class="rtwwwap_logout_text">logout</span></a>';
                            $rtwwwap_html1 .=     '</div>';
                            $rtwwwap_html1 .=	' <div class="rtwwwap-login-user">
                                                    <a href="'.esc_url(site_url()).'"class="rtwwwap_back_to_wordpress rtwwwap_home_icon" ><span>'.esc_html__('Go Back To Home','rtwwwap-woocommerce-membership').'</span></a>
                                                    <a href="'.esc_url(site_url()).'"class=" rtwwwap_home_icon_mobile" ><i class="fas fa-home rtwwwap_home_fa_icon"></i></a>
                                                </div>
                        </div>
                    </div>
                </div>
            </div>';
        
$rtwwwap_html1 .=	'<div class="rtwwwap_hide" id="rtwwwap-overview-wrapper">
                        <div class="mdc-layout-grid">
                            <div class="mdc-layout-grid__inner">
                                <div class="mdc-layout-grid__cell mdc-card rtwwwap-grid-cell rtwwwap-card1 mdc-elevation--z9">
                                    <div class="rtwwwap-inner-padding">
                                        <div class="rtwwap-card-text">
                                            <h6 class="rtwwwap_overview_card_head">'.esc_html__( 'Total Referrals', 'rtwwwap-wp-wc-affiliate-program' ).'</h6>
                                            <h5 class="rtwwwap_overview_card_number">'.sprintf( '<span>%u</span>', ( $rtwwwap_total_referrals ) ? $rtwwwap_total_referrals : '0').'</h5>
                                        </div>
                                        <div class="rtwwwap-card-icon">
                                        
                                        </div>
                                        <div class="rtwwwap-progress">
                                            <div class="rtwwwap-progress-bar" role="progressbar"></div>
                                        </div>
                                    </div>
                                   
                                </div>
                                <div class="mdc-layout-grid__cell mdc-card rtwwwap-grid-cell mdc-elevation--z9 rtwwwap-card2">
                                    <div class="rtwwwap-inner-padding">
                                        <div class="rtwwap-card-text">
                                            <h6 class="rtwwwap_overview_card_head">'.esc_html__( 'Total Commission', 'rtwwwap-wp-wc-affiliate-program' ).'</h6>
                                            <h5 class="rtwwwap_overview_card_number">'.sprintf( '<span> '.$rtwwwap_currency_sym.number_format( $rtwwwap_total_comm,$rtwwwap_decimal_place,$rtwwwap_decimal_separator, $rtwwwap_thousand_separator)).'</h5>


                                        </div>
                                        <div class="rtwwwap-card-icon">
                                        
                                        </div>
                                        <div class="rtwwwap-progress">
                                            <div class="rtwwwap-progress-bar" role="progressbar"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mdc-layout-grid__cell mdc-card rtwwwap-grid-cell mdc-elevation--z9 rtwwwap-card3">
                                    <div class="rtwwwap-inner-padding">
                                        <div class="rtwwap-card-text">
                                            <h6 class="rtwwwap_overview_card_head">'.esc_html__( 'Wallet', 'rtwwwap-wp-wc-affiliate-program' ).'</h6>
                                            <h5 class="rtwwwap_overview_card_number">'.sprintf( '<span>'.$rtwwwap_currency_sym.number_format($rtwwwap_wallet,$rtwwwap_decimal_place,$rtwwwap_decimal_separator, $rtwwwap_thousand_separator)).'</h5>


                                        </div>
                                        <div class="rtwwwap-card-icon">
                                            
                                        </div>
                                        <div class="rtwwwap-progress">
                                            <div class="rtwwwap-progress-bar" role="progressbar"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mdc-layout-grid__cell mdc-card rtwwwap-grid-cell mdc-elevation--z9 rtwwwap-card4">
                                    <div class="rtwwwap-inner-padding">
                                        <div class="rtwwap-card-text">
                                            <h6 class="rtwwwap_overview_card_head">'.esc_html__( 'Approved Commission', 'rtwwwap-wp-wc-affiliate-program' ).'</h6>
                                            <h5 class="rtwwwap_overview_card_number">'.sprintf( '<span>'.$rtwwwap_currency_sym.number_format( $rtwwwap_approved_comm,$rtwwwap_decimal_place,$rtwwwap_decimal_separator, $rtwwwap_thousand_separator)).'</h5>

                                        </div>
                                        <div class="rtwwwap-card-icon">
                                            
                                        </div>
                                        <div class="rtwwwap-progress">
                                            <div class="rtwwwap-progress-bar" role="progressbar"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mdc-layout-grid__cell mdc-card rtwwwap-grid-cell mdc-elevation--z9 rtwwwap-card5">
                                    <div class="rtwwwap-inner-padding">
                                        <div class="rtwwap-card-text">
                                            <h6 class="rtwwwap_overview_card_head">'.esc_html__( 'Pending Commission', 'rtwwwap-wp-wc-affiliate-program' ).'</h6>
                                            <h5 class="rtwwwap_overview_card_number">'.sprintf( '<span>'.$rtwwwap_currency_sym.number_format( $rtwwwap_pending_comm,$rtwwwap_decimal_place,$rtwwwap_decimal_separator, $rtwwwap_thousand_separator)).'</h5>
                                        </div>
                                        <div class="rtwwwap-card-icon">
                                        
                                        </div>
                                        <div class="rtwwwap-progress">
                                            <div class="rtwwwap-progress-bar" role="progressbar"></div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                            <div class="rtwwwap_line_chart_js">
                               
                                <div id="rtwwwap-chart-line">
                                    <canvas id="rtwwwap_line_chart_report"></canvas>
                                </div>
                            </div>
                            <div class="rtwwwap_dashboard_chart_js">
                                <div class="rtwwwap-chart-bar">
                                    <canvas id="rtwwwap_status"></canvas>
                                 </div>
                                 <div class="rtwwwap-chart-bar">
                                    <canvas id="dashboard_report_device"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>';

// / commission 
// ////// commission table  <div id="rtwwwap-chart-bar">
// <canvas id="rtwwwap_status"></canvas>
// </div>  
$rtwwwap_commissions = get_option( 'rtwwwap_commission_settings_opt' );

if(RTWWWAP_IS_WOO == 1 )
{
    $rtwwwap_post_type = 'product';
}
if(RTWWWAP_IS_Easy == 1 )
{
    $rtwwwap_post_type = 'download';
}
$rtwwwap_html1 .= '<div class="rtwwwap_hide"  id="rtwwwap_commission_table">';
if( $rtwwwap_commissions && !empty( $rtwwwap_commissions ) ){
            $rtwwwap_commission_settings = get_option( 'rtwwwap_commission_settings_opt' );
            $rtwwwap_comm_base 	= isset( $rtwwwap_commission_settings[ 'comm_base' ] ) ? $rtwwwap_commission_settings[ 'comm_base' ] : '1';

        if( $rtwwwap_comm_base == 1 ){

$rtwwwap_html1 .= '
                    <div class="rtwwwap-text">
                        <h4>
                            <span> <i class="fas fa-retweet mr-1" aria-hidden="true"></i>';
                            $rtwwwap_html1 .= 		esc_html__( 'Commission on all Products', 'rtwwwap-wp-wc-affiliate-program' );

                            $rtwwwap_html1 .=	"  ";
                                if( $rtwwwap_commission_settings[ 'all_commission_type' ] == 'percentage' )
                            {
                                
                                    $rtwwwap_html1 .=		sprintf( '%u%s', esc_html(  $rtwwwap_commission_settings[ 'all_commission' ]), '%' );
                            }
                            else{
                        
                                $rtwwwap_html1 .=		sprintf( '%s%u', $rtwwwap_currency_sym, esc_html( $rtwwwap_commission_settings[ 'all_commission' ] ) );
                            }
$rtwwwap_html1 .=       	   '</span>
                        </h4>
                    </div>
                    <div class="rtwwwap-text">
                        <h4>
                            <span> <i class="fas fa-retweet mr-1" aria-hidden="true"></i> '.esc_html__( 'Per Product Commission', 'rtwwwap-wp-wc-affiliate-program' ).'</span>
                         </h4>
                    </div>
                    <div class="mdc-data-table">
                        <div class="mdc-data-table__table-container">
                             <table class="mdc-data-table__table" aria-label="Dessert calories" id="rtwwwap-commission-table1">
                                <thead>
                                    <tr class="mdc-data-table__header-row">
                                        <th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col">'.esc_html__( 'Product Name', 'rtwwwap-wp-wc-affiliate-program' ).'</th>

                                        <th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col">'.esc_html__( 'Percentage commission (%)', 'rtwwwap-wp-wc-affiliate-program' ).'
                                        </th>

                                        <th class="mdc-data-table__header-cell" role="columnheader" scope="col">'.esc_html__( "Fixed commission ($rtwwwap_currency_sym)", 'rtwwwap-wp-wc-affiliate-program' ).'</th>
                                    </tr>
                                </thead>
                                <tbody class="mdc-data-table__content">';
                                 foreach( $rtwwwap_commissions as $rtwwwap_key => $rtwwwap_value ){
                                            if( $rtwwwap_key == 'per_prod_mode' ){
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

                                                if( !empty( $rtwwwap_products ) ){
                                                    foreach( $rtwwwap_products as $rtwwwap_key1 => $rtwwwap_value1 ){
                                                        $rtwwwap_perc_comm 	= get_post_meta( $rtwwwap_value1, 'rtwwwap_percentage_commission_box', true );
                                                        $rtwwwap_fix_comm 	= get_post_meta( $rtwwwap_value1, 'rtwwwap_fixed_commission_box', true );
                                                        $rtwwwap_prod_name 	= get_the_title( $rtwwwap_value1 );
    $rtwwwap_html1 .=	            '<tr class="mdc-data-table__row">
                                        <td class="mdc-data-table__cell mdc-data-table__cell--numeric">'.
                                                        $rtwwwap_prod_name.'
                                        </td>
                                        <td class="mdc-data-table__cell mdc-data-table__cell--numeric">';            
                                            if( $rtwwwap_value == 1 || $rtwwwap_value == 3 ){
                                                    $rtwwwap_html1 .= 			esc_html( $rtwwwap_perc_comm );
                                                }
$rtwwwap_html1 .=                         '</td>
                                        <td class="mdc-data-table__cell">';
                                                        if( $rtwwwap_value == 2 || $rtwwwap_value == 3 ){
                                                            $rtwwwap_html1 .= 			esc_html( $rtwwwap_fix_comm );
                                                        }
$rtwwwap_html1 .=                           '</td>
                                    </tr>';
                                                    }
                                                }
                                            }     
                                        }     


$rtwwwap_html1 .=              '</tbody>
                            </table>
                        </div>
                    </div>';
                    foreach( $rtwwwap_commissions as $rtwwwap_key => $rtwwwap_value ){
                            if( $rtwwwap_key == 'per_cat' ){

$rtwwwap_html1 .=      '<div class="rtwwwap-text">
                        <h4>
                            <span> <i class="fas fa-retweet mr-1" aria-hidden="true"></i>'.	esc_html__( 'Per Category Commission', 'rtwwwap-wp-wc-affiliate-program' ).'</span>
                        </h4>
                    </div>
                        <div class="mdc-data-table">
                            <div class="mdc-data-table__table-container">   
                                <table class="mdc-data-table__table" aria-label="Dessert calories" id="rtwwwap-commission-table">            
                                    <thead>    
                                        <tr class="mdc-data-table__header-row">
                                            
                                            <th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col">'.esc_html__( 'Category Name', 'rtwwwap-wp-wc-affiliate-program' ).'</th>

                                            <th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col">'.esc_html__( 'Percentage commission (%)', 'rtwwwap-wp-wc-affiliate-program' ).'</th>
                                            
                                            <th class="mdc-data-table__header-cell" role="columnheader" scope="col">'. sprintf( '%s (%s)', esc_html__( "Fixed commission", 'rtwwwap-wp-wc-affiliate-program' ), esc_html( $rtwwwap_currency_sym ) ).'</th>
                                        </tr>
                                    </thead>
                                    <tbody class="mdc-data-table__content">';

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

$rtwwwap_html1 .=                      '<tr class="mdc-data-table__row">
                                        
                                             <td class="mdc-data-table__cell mdc-data-table__cell--numeric">'.esc_html( $rtwwwap_cat_name ).'</td>

                                             <td class="mdc-data-table__cell mdc-data-table__cell--numeric">'.esc_html( $rtwwwap_perc_comm ).'</td>

                                             <td class="mdc-data-table__cell">'.esc_html( $rtwwwap_fix_comm ).'</td>
                                        </tr>';

                                    }
                                    if( !$rtwwwap_cat_count ){
                                        $rtwwwap_html1 .= 	'<tr>';
                                        $rtwwwap_html1 .= 		'<td colspan="3" class="rtwwwap_no_comm">'.esc_html__( 'Specific Category commission not set.', 'rtwwwap-wp-wc-affiliate-program' ).'</td>';
                                        $rtwwwap_html1 .= 	'</tr>';
                                    }
                                }
$rtwwwap_html1 .=                   '</tbody>
                                </table>
                            </div>
                        </div>';
                     }
                }    
                                
            }
            else{
                $rtwwwap_levels_settings = get_option( 'rtwwwap_levels_settings_opt' );

        $rtwwwap_html1 .= '<div class="rtwwwap-text">
                                <h4>
                                    <span> <i class="fas fa-retweet mr-1" aria-hidden="true"></i>'.	esc_html__( 'Level Based Commission' ).'</span>
                                </h4>
                            </div>
                                <div class="mdc-data-table">
                                    <div class="mdc-data-table__table-container">   
                                        <table class="mdc-data-table__table" aria-label="Dessert calories" id="rtwwwap-commission-table">';  
        if( !empty( $rtwwwap_levels_settings ) )
        {
            $rtwwwap_html1 .= 	'<thead >';
            $rtwwwap_html1 .= 		'<tr class="mdc-data-table__header-row">';
            $rtwwwap_html1 .= 			'<th class="mdc-data-table__header-cell " role="columnheader" scope="col">';
            $rtwwwap_html1 .= 		 		esc_html__( 'Level No.','rtwwwap-wp-wc-affiliate-program' );
            $rtwwwap_html1 .= 			'</th>';
            $rtwwwap_html1 .= 			'<th class="mdc-data-table__header-cell " role="columnheader" scope="col">';
            $rtwwwap_html1 .= 		 		esc_html__( 'Level Name','rtwwwap-wp-wc-affiliate-program' );
            $rtwwwap_html1 .= 			'</th>';
            $rtwwwap_html1 .= 			'<th class="mdc-data-table__header-cell  " role="columnheader" scope="col">';
            $rtwwwap_html1 .= 		 		esc_html__( 'Level commission','rtwwwap-wp-wc-affiliate-program' );
            $rtwwwap_html1 .= 			'</th>';
            $rtwwwap_html1 .= 			'<th class="mdc-data-table__header-cell " role="columnheader" scope="col">';
            $rtwwwap_html1 .= 		 		esc_html__( 'To Reach','rtwwwap-wp-wc-affiliate-program' );
            $rtwwwap_html1 .= 			'</th>';
            $rtwwwap_html1 .= 		'</tr>';
            $rtwwwap_html1 .= 	'</thead>';

            $rtwwwap_html1 .= 	'<tbody class="mdc-data-table__content">';
            foreach( $rtwwwap_levels_settings as $rtwwwap_levels_key => $rtwwwap_levels_val )
            {
                $rtwwwap_html1 .= 		'<tr class="mdc-data-table__row">';
                $rtwwwap_html1 .= 			'<td class="mdc-data-table__cell ">';
                $rtwwwap_html1 .= 		 		esc_html( $rtwwwap_levels_key );
                $rtwwwap_html1 .= 			'</td>';

                $rtwwwap_html1 .= 			'<td class="mdc-data-table__cell mdc-data-table__cell--numeric">';
                $rtwwwap_html1 .= 		 		esc_html( $rtwwwap_levels_val[ 'level_name' ] );
                $rtwwwap_html1 .= 			'</td ">';

                $rtwwwap_html1 .= 			'<td class="mdc-data-table__cell mdc-data-table__cell--numeric">';
                if( $rtwwwap_levels_val[ 'level_commission_type' ] == '0' )
                {
                    $rtwwwap_html1 .= sprintf( '%s%s', esc_html( $rtwwwap_levels_val[ 'level_comm_amount' ] ), '%' );
                }
                elseif( $rtwwwap_levels_val[ 'level_commission_type' ] == '1' )
                {
                    $rtwwwap_html1 .= $rtwwwap_currency_sym.number_format($rtwwwap_levels_val[ 'level_comm_amount' ],$rtwwwap_decimal_place,$rtwwwap_decimal_separator, $rtwwwap_thousand_separator );
                }
                $rtwwwap_html1 .= 			'</td>';

                $rtwwwap_html1 .= 			'<td class="mdc-data-table__cell mdc-data-table__cell--numeric">';
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
                    $rtwwwap_html1 .= sprintf( '%s %s', esc_html__( 'Total sale amount' ), $rtwwwap_currency_sym.number_format($rtwwwap_levels_val[ 'level_criteria_val' ],$rtwwwap_decimal_place,$rtwwwap_decimal_separator, $rtwwwap_thousand_separator ) );
                    ;
                }
                $rtwwwap_html1 .= 			'</td>';
                $rtwwwap_html1 .= 		'</tr>';
            }
            $rtwwwap_html1 .= 	'</tbody>';
        }
              $rtwwwap_html1 .= 	'</table>';
            $rtwwwap_html1 .= 	'</div>';
        $rtwwwap_html1 .= 	'</div>';
                
        }
                  
    }
    else{
        $rtwwwap_html1 .= 	'<div class="rtwwwap_commissionws_wrapper">';
        $rtwwwap_html1 .= 		esc_html__( 'No Commission is set on any Product', 'rtwwwap-wp-wc-affiliate-program' );
        $rtwwwap_html1 .= 		'<span>';
        $rtwwwap_html1 .= 	'</div>';
    }

    $rtwwwap_html1 .=	'             
    </div>
            ';  
   

// referral tabels
    $rtwwwap_date_format = get_option( 'date_format' );
    $rtwwwap_time_format = get_option( 'time_format' );
    $rtwwwap_user_all_referrals = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `aff_id` = %d ORDER BY `date` DESC", $rtwwwap_user_id ), ARRAY_A );
    $rtwwwap_html1 .=	'   
                        <div class="rtwwwap_hide"  id="rtwwwap_referral_table">   
                                <div class="rtwwwap-text">
                                    <h4>
                                        <span> <i class="fas fa-retweet mr-1" aria-hidden="true"></i> '.esc_html__( 'Referral Table Commission', 'rtwwwap-wp-wc-affiliate-program' ).'</span>
                                    </h4>
                                </div>
                               
                                    <div class="rtwwwap_table_container">
                                    
                                        <table class="mdl-data-table" aria-label="Dessert calories" id="rtwwwap-refferral-table" >
                                                
                                            <thead>
                                            
                                            <tr>
                                                
                                                <th >'.	sprintf( '%s', esc_html__( 'Type', 'rtwwwap-wp-wc-affiliate-program' ) ).'</th>
                                                <th>'.	sprintf( '%s', esc_html__( 'Amount', 'rtwwwap-wp-wc-affiliate-program' ) ).'</th>
                                                <th>'.	sprintf( '%s', esc_html__( 'Date', 'rtwwwap-wp-wc-affiliate-program' ) ).'</th>
                                                <th>'.	sprintf( '%s', esc_html__( 'Status', 'rtwwwap-wp-wc-affiliate-program' ) ).'</th>
                                              
                                            </tr>
                                            </thead>
                                            <tbody >';
                                            foreach( $rtwwwap_user_all_referrals as $rtwwwap_user_ref_key => $rtwwwap_user_ref_value ){
                                                $rtwwwap_order_id = $rtwwwap_user_ref_value[ 'order_id'];
                                                if($rtwwwap_order_id)
                                                {
                                                $rtwwwap_product_details = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `order_id`= '%d' ", $rtwwwap_order_id ) );
                                                    
                                                if(!empty( $rtwwwap_product_details))
                                                {
                                                    $rtwwwap_product_detail =  json_decode($rtwwwap_product_details[0]->product_details,true);

                                                    if(RTWWWAP_IS_WOO == 1)
                                                    {
                                                        $rtwwwap_product = isset($rtwwwap_product_detail[0])? wc_get_product($rtwwwap_product_detail[0]['product_id']): "";
                                                    }
                                                    else{
                                                        $rtwwwap_product = isset($rtwwwap_product_detail[0])?$rtwwwap_product_detail[0]['product_id']: "";
                                                    }
                                                   
                                                    $rtwwwap_product_price = isset($rtwwwap_product_detail[0])?$rtwwwap_product_detail[0]['product_price']: "";
                                                    $rtwwwap_product_commission = isset($rtwwwap_product_detail[0])?$rtwwwap_product_detail[0]['prod_commission']:"";
                                
                                                 }
                                                }
    $rtwwwap_html1 .=                      '<tr>
                                                <td>';
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
                                                
                                                
                                                $rtwwwap_html1 .=  '</td>
                                                <td>';
                                                if( RTWWWAP_IS_WOO == 1 ){
                                                    $rtwwwap_html1 .=  			esc_html( get_woocommerce_currency_symbol( $rtwwwap_user_ref_value[ 'currency' ] ).number_format( $rtwwwap_user_ref_value[ 'amount' ],$rtwwwap_decimal_place,$rtwwwap_decimal_separator, $rtwwwap_thousand_separator ) );
                                                }
                                                else{
                                                    $rtwwwap_html1 .=			esc_html( $rtwwwap_curr_obj->rtwwwap_curr_symbol( $rtwwwap_user_ref_value[ 'currency' ] ).number_format( $rtwwwap_user_ref_value[ 'amount' ],$rtwwwap_decimal_place,$rtwwwap_decimal_separator, $rtwwwap_thousand_separator ) );
                                                }
                                                
                                                $rtwwwap_html1 .='</td>
                                                <td>';
                                                
                                                
                                                $rtwwwap_date_time_format = $rtwwwap_date_format.' '.$rtwwwap_time_format;
                                                $rtwwwap_local_date = get_date_from_gmt( date( 'Y-m-d H:i:s
                                                    ', strtotime( $rtwwwap_user_ref_value[ 'date' ] ) ), $rtwwwap_date_time_format );
                                                    $rtwwwap_html1 .= 				esc_html( $rtwwwap_local_date );
                                                
                                                    $rtwwwap_html1 .='</td>
                                                <td>';
                                                
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
                                                
                                                $rtwwwap_html1 .=  		'</div></td>';
                                             

                                                $rtwwwap_html1 .= '  </tr>';
                                            }
                                    
                    $rtwwwap_html1 .=  '  </tbody>
                                        </table>
                                    </div>
                                </div>';
                            $rtwwwap_html1 .= '<div class="rtwwwap_member_modal">';
                            $rtwwwap_html1 .= 	'<div class="rtwwwap_member_modal-content">';
                            $rtwwwap_html1 .= 			'<div class="rtwwwap_member_modal-header">';
                            $rtwwwap_html1 .= 					'<span class="rtwwwap_member_close">&times;</span>';
                            $rtwwwap_html1 .=  						'<h2 ><spam class="rtwwwap_member_heading">';
                            $rtwwwap_html1 .=  						 esc_html__("O","rtwwwap-wp-wc-affiliate-program"); 
                            $rtwwwap_html1 .=   				'</spam>';
                            $rtwwwap_html1 .=  esc_html__('rder','rtwwwap-wp-wc-affiliate-program');
                            $rtwwwap_html1 .= 	'<spam class="rtwwwap_member_heading">'.esc_html__("D","rtwwwap-wp-wc-affiliate-program").'</spam>';
                            
                            $rtwwwap_html1 .= esc_html__('etail','rtwwwap-wp-wc-affiliate-program'); 
                            $rtwwwap_html1 .= '</h2>';
                                    $rtwwwap_html1 .=  '</div>';
                                    $rtwwwap_html1 .=  '<div class="rtwwwap_member_modal-body">';
                                    $rtwwwap_html1 .= '	<table class="rtwwwap-profile-detail-table">';
                                    $rtwwwap_html1 .= 	'<tbody>';
                                    $rtwwwap_html1 .= 	'<tr>';
                                    $rtwwwap_html1 .= 		'<td >'.esc_html__( 'Product Name','rtwwwap-wp-wc-affiliate-program').'</td>';
                                    $rtwwwap_html1 .= 			'<td ><span id="rtwwwap_product_name"></span></td>';
                                                        
                                    $rtwwwap_html1 .= 	'<tr>';
                                    $rtwwwap_html1 .= 	'<tr>';
                                    $rtwwwap_html1 .= 		'<td >'.esc_html__( 'Product Price','rtwwwap-wp-wc-affiliate-program').'</td>';
                                    $rtwwwap_html1 .= 			'<td ><span id="rtwwwap_product_price"></span></td>';
                                                        
                                    $rtwwwap_html1 .= 	'<tr>';
                                    $rtwwwap_html1 .= 	'<tr>';
                                    $rtwwwap_html1 .= 		'<td >'.esc_html__( 'Commission Received','rtwwwap-wp-wc-affiliate-program').'</td>';
                                    $rtwwwap_html1 .= 			'<td ><span id="rtwwwap_commission_received"></span></td>';
                                                        
                                    $rtwwwap_html1 .= 	'<tr>';
                                    $rtwwwap_html1 .= 	'<tr>';
                                    $rtwwwap_html1 .= 		'<td >'.esc_html__( 'Payment Method','rtwwwap-wp-wc-affiliate-program').'</td>';
                                    $rtwwwap_html1 .= 			'<td ><span id="rtwwwap_payment_method"></span></td>';		
                                    $rtwwwap_html1 .= 	'<tr>';
                
                                    $rtwwwap_html1 .= 	'<tr>';
                                    $rtwwwap_html1 .= 		'<td >'.esc_html__( 'Order Status','rtwwwap-wp-wc-affiliate-program').'</td>';
                                    $rtwwwap_html1 .= 			'<td ><span id="rtwwwap_order_status"></span></td>';							
                                    $rtwwwap_html1 .= 	'<tr>';
                            
                            
                                                    
                                    $rtwwwap_html1 .= 		'</tbody>';
                                    $rtwwwap_html1 .= '</table>';
                                    $rtwwwap_html1 .= '</div>';
                                    $rtwwwap_html1 .= '<div class="rtwwwap_member_modal-footer">';
                                    $rtwwwap_html1 .= ' <button  class=" rtwwwap_close_button" >'.esc_html__('CLOSE','rtwwwap-wp-wc-affiliate-program').'</button>';
                            
                                    $rtwwwap_html1 .=  '</div>';
                                    $rtwwwap_html1 .= '</div>';
                                   
        $rtwwwap_html1 .= '</div>';



///coupon

        $rtwwwap_commission_settings = get_option( 'rtwwwap_commission_settings_opt' );
        $rtwwwap_is_coupon_activated = isset( $rtwwwap_commission_settings[ 'coupons' ] ) ? $rtwwwap_commission_settings[ 'coupons' ] : 0;
        $rtwwwap_coupons = get_user_meta( $rtwwwap_user_id, 'rtwwwap_coupons', true );
if( $rtwwwap_is_coupon_activated)
{
        $rtwwwap_html1 .= '
    <div class="rtwwwap_coupon_section rtwwwap_hide" id= "rtwwwap_coupon">
        <div class="rtwwwap_coupon_wrapper_row">
                <div class="rtwwwap-request-section">
                    <h4 class="rtwwwap-coupon-header">'.esc_html__( ' Create Coupon', 'rtwwwap-wp-wc-affiliate-program' ).'</h4>
                    <div class="mdc-card mdc-elevation--z9">
                        <div class="rtwwwap-msg-card">
                            <div class="rtwwwap-coupon-image">
                                <img src="'.esc_url( RTWWWAP_URL.'/assets/images/coupon.png' ).'" class="rtwwwap-img-fluid">
                            </div>';
                            if( $rtwwwap_is_coupon_activated && RTWWWAP_IS_WOO == 1 ){
                                $rtwwwap_min_amount_for_coupon = isset( $rtwwwap_commission_settings[ 'min_amount_for_coupon' ] ) ? $rtwwwap_commission_settings[ 'min_amount_for_coupon' ] : 0;
                                $rtwwwap_html1 .= 		'
                                <div class="rtwwwap_coupon_message_cart">';
                                    if( $rtwwwap_wallet >= $rtwwwap_min_amount_for_coupon ){
                                            $rtwwwap_html1 .= 		' <label class="mdc-text-field mdc-text-field--outlined">
                                            <input type="number" name="product_name" class="mdc-text-field__input" min="'.esc_attr( $rtwwwap_min_amount_for_coupon ).'" max="'.esc_attr( $rtwwwap_wallet ).'" id = "rtwwwap_coupon_amount">
                                            <div class="mdc-notched-outline mdc-notched-outline--upgraded">
                                                <div class="mdc-notched-outline__leading"></div>
                                                <div class="mdc-notched-outline__notch">
                                                <span class="mdc-floating-label"> '.esc_html__( ' Create Coupon', 'rtwwwap-wp-wc-affiliate-program' ).'</span>
                                                </div>
                                                <div class="mdc-notched-outline__trailing"></div>
                                            </div>
                                        </label>';
                                    }
                                    else{
                                        $rtwwwap_html1 .=         '
                                    
                                    <p class="rtwwwap-coupon-text">'.esc_html__( 'You can create', 'rtwwwap-wp-wc-affiliate-program' ).' <span>'.esc_html__( 'Coupons', 'rtwwwap-wp-wc-affiliate-program' ).'</span>'.esc_html__( 'Once your Wallet amount is greater than', 'rtwwwap-wp-wc-affiliate-program' ).'                                                               <span>'.$rtwwwap_currency_sym . $rtwwwap_min_amount_for_coupon.'</span></p>';
                                    }

                                    
                                    $rtwwwap_html1 .= '      </div>
                                    <div class="rtwwwap-create-btn">
                                            <a href="#rtwwwap_coupon" data-id="1" id="rtwwwap_create_coupon" class="mdc-button mdc-button--raised mdc-theme--primary mdc-ripple-upgraded">'.esc_html__( ' Create Coupon', 'rtwwwap-wp-wc-affiliate-program' ).'</a>
                                        </div>';                               
                        
$rtwwwap_html1 .=     ' </div>';
$rtwwwap_html1 .=     '
                        </div>
                    </div>
                

                  

                ';

            }

                if( $rtwwwap_coupons && RTWWWAP_IS_WOO == 1){
                    $rtwwwap_html1 .=         '
                <div class="rtwwwap-coupon-table-wrapper" id="rtwwap_coupon_table">
            
                    <div class="rtwwwap-text">
                        <h4>
                            <span> <i class="fas fa-retweet mr-1"></i>'.esc_html__( 'Coupon Table', 'rtwwwap-wp-wc-affiliate-program' ).'</span>
                        </h4>
                    </div>
                   
                        <div class="mdc-card mdc-elevation--z9">
                            <table id="rtwwwap_coupon_table" class="mdl-data-table">
                                <thead>
                                    <tr>
                                        <th>'.esc_html__( 'Coupon', 'rtwwwap-wp-wc-affiliate-program' ).'</th>
                                        <th>'.esc_html__( 'Amount', 'rtwwwap-wp-wc-affiliate-program' ).'</th>
                                        </tr>
                                </thead>
                                <tbody>';
                                $rtwwwap_valid_coupon = true;
                                foreach( $rtwwwap_coupons as $rtwwwap_key => $rtwwwap_coupon_id ){
                                if( get_post_status( $rtwwwap_coupon_id ) == 'publish' ){
                                $rtwwwap_valid_coupon = false;
                                $rtwwwap_coupon = esc_html( get_the_title( $rtwwwap_coupon_id ) );
                                $rtwwwap_amount = esc_html( get_post_meta( $rtwwwap_coupon_id, 'coupon_amount', true ) )   ;
$rtwwwap_html1 .=         '             <tr>
                                        <td>'.sprintf( '%s', $rtwwwap_coupon ).'</td>
                                        <td>'.sprintf( '%u', $rtwwwap_amount ).'</td>
                                    </tr>';
                                if( $rtwwwap_valid_coupon ){
                                    $rtwwwap_html1 .= 			'<tr>';
                                    $rtwwwap_html1 .= 				'<td colspan="2">';
                                    $rtwwwap_html1 .= 					sprintf( '%s', esc_html__( 'No Coupons', 'rtwwwap-wp-wc-affiliate-program' ) );
                                    $rtwwwap_html1 .= 				'</td>';
                                    $rtwwwap_html1 .= 			'</tr>';
                                        }  
                                     }
                                 }

$rtwwwap_html1 .=         '      </tbody>
                            </table>
                        </div>
                </div>';
                }

$rtwwwap_html1 .=   '
        </div>
    </div>';
            }



// link generation 

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

// ends here

$rtwwwap_extra_features_opt 	= get_option( 'rtwwwap_extra_features_opt' );
$rtwwwap_social_share_setting 	= isset( $rtwwwap_extra_features_opt[ 'social_share' ] ) ? $rtwwwap_extra_features_opt[ 'social_share' ] : 0;
$rtwwwap_qr_code_setting 		= isset( $rtwwwap_extra_features_opt[ 'qr_code' ] ) ? $rtwwwap_extra_features_opt[ 'qr_code' ] : 0;
$rtwwwap_affiliate_slug 		= isset( $rtwwwap_extra_features_opt[ 'affiliate_slug' ] ) ? $rtwwwap_extra_features_opt[ 'affiliate_slug' ] : esc_html__( 'rtwwwap_aff', 'rtwwwap-wp-wc-affiliate-program' ) ;

$rtwwwap_html1 .=   '          <div class="rtwwwap-tools-section rtwwwap_hide" id="rtwwwap_generate_link">
                                    <div class="rtwwwap_generate_link_row">
                                        <div class="rtwwwwap-social-image-div">
                                            <div class="rtwwwap_video_1">
                                                <embed type="video/webm" src="https://www.youtube.com/embed/eK8BbWSfdo0" width="350" height="250">
                                            </div>
                                        </div>
                                        <div class="rtwwwap_generate_link_text_wrapper">
                                            <h4>Generate Links</h4>
                                            <div class="rtwwwap-generate-link-box">
                                                <label class="mdc-text-field mdc-text-field--outlined rtwwwap-w-100">
                                                    <input type="text" class="mdc-text-field__input" aria-labelledby="my-label-id"  id="rtwwwap_aff_link_input" placeholder="'.esc_attr__( 'Enter any product\'s URL from this website', 'rtwwwap-wp-wc-affiliate-program' ).'" value="'.esc_attr( home_url() ).'">
                                                    <span class="mdc-notched-outline">
                                                        <span class="mdc-notched-outline__leading"></span>
                                                        <span class="mdc-notched-outline__notch">
                                                        <!-- <span class="mdc-floating-label" id="my-label-id">'.esc_html__( 'Your Name', 'rtwwwap-wp-wc-affiliate-program' ).'</span> -->
                                                        </span>
                                                        <span class="mdc-notched-outline__trailing"></span>
                                                    </span>
                                                </label>
                                            </div>
                                            <div class="rtwwwap-copy-link-box">
                                            <p  id="rtwwwap_generated_link"></p>
                                            </div>
                                            <button class="mdc-button mdc-button--raised" id="rtwwwap_generate_button" data-rtwwwap_aff_id="'.esc_attr( get_current_user_id() ).'"  data-rtwwwap_aff_slug="'.$rtwwwap_affiliate_slug.'" data-rtwwwap_aff_name="'.esc_attr($rtwwwap_aff_custom_code).'">
                                                <span class="mdc-button__label">'.esc_html__( 'Generate link', 'rtwwwap-wp-wc-affiliate-program' ).'</span>
                                            </button>
                                        
                                            <div class="rtwwwap_span_copied">
                                            <button class="mdc-button mdc-button--raised"  id="rtwwwap_copy_to_clip">
                                            <span class="mdc-button__label">Copy link</span>
                                        </button>
                                            <span id="rtwwwap_copy_tooltip_link">'.esc_html__( 'Copied', 'rtwwwap-wp-wc-affiliate-program' ).'</span>
                                            </div>
                                     ';
                                     if( $rtwwwap_qr_code_setting ){
                                        $rtwwwap_html1 .=	    ' <span class="mdc-button__label" id ="rtwwwap_generate_qr">'.esc_html__( 'Create QR Code', 'rtwwwap-wp-wc-affiliate-program' ).'</span>';
                                    }
                    
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

                        $rtwwwap_html1 .=	        	'</div>';      
                        $rtwwwap_html1 .=	  	    '</div>';   
                        $rtwwwap_html1 .=	  	    '</div>';                                           

                        $rtwwwap_html1 .=       '</div>';

$rtwwwap_html1 .=	  	'</div>';
                              



//Report tab

global $wpdb;
	$rtwwwap_user_id = get_current_user_id();
    $rtwwwap_user_referrals_links = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."rtwwwap_referral_link WHERE `aff_id` = %d ORDER BY `id` DESC", $rtwwwap_user_id ), ARRAY_A );
	$rtwwwap_total_order = $wpdb->get_results( $wpdb->prepare( "SELECT COUNT(`id`) as total_order, DATE(date) as date_wise FROM `".$wpdb->prefix."rtwwwap_referrals` WHERE `aff_id`=%d GROUP BY DATE(date_wise) ORDER BY `date` DESC", $rtwwwap_user_id ),ARRAY_A );

	$rtwwwap_total_referral_commission = $wpdb->get_results( $wpdb->prepare( "SELECT SUM(amount) as commission , DATE(date) as date_wise FROM `".$wpdb->prefix."rtwwwap_referrals` WHERE `aff_id`=%d AND `type`= 0 AND `status`= %d OR `status`= %d  GROUP BY DATE(date_wise)"  , $rtwwwap_user_id, 1,2 ),ARRAY_A );

	$rtwwwap_total_manual_commission = $wpdb->get_results( $wpdb->prepare( "SELECT SUM(amount) as commission , DATE(date) as date_wise FROM `".$wpdb->prefix."rtwwwap_referrals` WHERE `aff_id`=%d AND `type`= 6 AND `status`= %d OR `status`= %d  GROUP BY DATE(date_wise)"  , $rtwwwap_user_id, 1,2 ),ARRAY_A );

	$rtwwwap_total_mlm_commission = $wpdb->get_results( $wpdb->prepare( "SELECT SUM(amount) as commission , DATE(date) as date_wise FROM `".$wpdb->prefix."rtwwwap_referrals` WHERE `aff_id`=%d AND `type`= 4 AND `status`= %d OR `status`= %d  GROUP BY DATE(date_wise)"  , $rtwwwap_user_id, 1,2 ),ARRAY_A );

	$rtwwwap_total_signup_commission = $wpdb->get_results( $wpdb->prepare( "SELECT SUM(amount) as commission , DATE(date) as date_wise FROM `".$wpdb->prefix."rtwwwap_referrals` WHERE `aff_id`=%d AND `type`= 1 AND `status`= %d OR `status`= %d  GROUP BY DATE(date_wise)"  , $rtwwwap_user_id, 1,2 ),ARRAY_A );

	$rtwwwap_total_performance_commission = $wpdb->get_results( $wpdb->prepare( "SELECT SUM(amount) as commission , DATE(date) as date_wise FROM `".$wpdb->prefix."rtwwwap_referrals` WHERE `aff_id`=%d AND `type`= 2 AND `status`= %d OR `status`= %d  GROUP BY DATE(date_wise)"  , $rtwwwap_user_id, 1,2 ),ARRAY_A );

	$final_commission_array = array();

	foreach($rtwwwap_total_referral_commission as $ref_key => $ref_value )
	{
		if(array_key_exists($ref_value['date_wise'],$final_commission_array))
		{
			$final_commission_array[$ref_value['date_wise']]['total_commission'] = $ref_value['commission'];
		}
		else{
			$final_commission_array[$ref_value['date_wise']]['total_commission'] = $ref_value['commission'];
		}
	}

	foreach($rtwwwap_total_manual_commission as $ref_key => $ref_value )
	{
		if(array_key_exists($ref_value['date_wise'],$final_commission_array))
		{
			$com = $final_commission_array[$ref_value['date_wise']]['total_commission'];
			$final_commission_array[$ref_value['date_wise']]['total_commission'] = $com + $ref_value['commission'];
		}
		else{
			$final_commission_array[$ref_value['date_wise']]['total_commission'] = $ref_value['commission'];
		}
	}

	foreach($rtwwwap_total_mlm_commission as $ref_key => $ref_value )
	{
		if(array_key_exists($ref_value['date_wise'],$final_commission_array))
		{
			$com = $final_commission_array[$ref_value['date_wise']]['total_commission'];
			$final_commission_array[$ref_value['date_wise']]['total_commission'] = $com + $ref_value['commission'];
		}
		else{
			$final_commission_array[$ref_value['date_wise']]['total_commission'] = $ref_value['commission'];
		}
	}

	foreach($rtwwwap_total_signup_commission as $ref_key => $ref_value )
	{
		if(array_key_exists($ref_value['date_wise'],$final_commission_array))
		{
			$com = $final_commission_array[$ref_value['date_wise']]['total_commission'];
			$final_commission_array[$ref_value['date_wise']]['total_commission'] = $com + $ref_value['commission'];
		}
		else{
			$final_commission_array[$ref_value['date_wise']]['total_commission'] = $ref_value['commission'];
		}
	}

	foreach($rtwwwap_total_performance_commission as $ref_key => $ref_value )
	{
		if(array_key_exists($ref_value['date_wise'],$final_commission_array))
		{
			$com = $final_commission_array[$ref_value['date_wise']]['total_commission'];
			$final_commission_array[$ref_value['date_wise']]['total_commission'] = $com + $ref_value['commission'];
		}
		else{
			$final_commission_array[$ref_value['date_wise']]['total_commission'] = $ref_value['commission'];
		}
	}

	$rtwwwap_order_ids = $wpdb->get_results( $wpdb->prepare( "SELECT `order_id` as order_wise, DATE(date) as date_wise  FROM `".$wpdb->prefix."rtwwwap_referrals` WHERE `aff_id`=%d "  , $rtwwwap_user_id ),ARRAY_A );

	$rtwwwap_order_id = array();

	foreach($rtwwwap_order_ids as $key => $value) {

		if(array_key_exists($value['date_wise'],$rtwwwap_order_id))
		{
			if($value["order_wise"] > 0)
			{
				$rtwwwap_product_details = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `order_id`= '%d' AND (`status` = 1 OR `status` = 2)  AND `type` = 0 ", $value["order_wise"] ) );

                    if($rtwwwap_product_details)
                    {
                        $rtwwwap_product_detail =  json_decode($rtwwwap_product_details[0]->product_details,true);
                        $rtwwwap_product = wc_get_product($rtwwwap_product_detail[0]['product_id']);
                        $rtwwwap_product_price = $rtwwwap_product_detail[0]['product_price'];
                        $rtwwwap_order_id[$value["date_wise"]][$value["order_wise"]] = $rtwwwap_product_price;
                    }
			
				
				}
		}
		else {
			if($value["order_wise"] > 0)
			{
			$rtwwwap_product_details = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `order_id`= '%d' AND DATE(date) = '%s' AND `aff_id` = '%d'  AND (`status` = 1 OR `status` = 2)  AND `type` = 0  ", $value["order_wise"],$value['date_wise'],$rtwwwap_user_id));	
                if($rtwwwap_product_details)
                {
                    $rtwwwap_product_detail =  json_decode($rtwwwap_product_details[0]->product_details,true);
                    $rtwwwap_product = wc_get_product($rtwwwap_product_detail[0]['product_id']);
                    $rtwwwap_product_price = $rtwwwap_product_detail[0]['product_price'];
                    $rtwwwap_order_id[$value["date_wise"]][$value["order_wise"]] =  $rtwwwap_product_price ;
                }
			}
			
		}
	
	}


	$rtwwwap_total_sales = array();

	foreach($rtwwwap_order_id as $key => $value)
	{
		$total= 0;
		foreach($value as $key1 => $value1)
		{
			$total +=  $value1;
		}
			$rtwwwap_total_sales[ $key] = $total; 
	}

	foreach($final_commission_array as $key => $value)
	{
		if(array_key_exists($key,$rtwwwap_total_sales))
		{
			$final_commission_array[$key]['total_prod_price'] = $rtwwwap_total_sales[$key];
		}
		else
		{
			$final_commission_array[$key]['total_prod_price'] = 0;
		}
	}

	foreach($rtwwwap_total_order as $key => $value)
	{
		if(array_key_exists($value['date_wise'],$final_commission_array))
		{
			$final_commission_array[$value['date_wise']]['total_order'] = $value['total_order'];
		}
	}

    $rtwwwap_html1 .=  '    
    <div class="rtwwwap_hide" id="rtwwwap_report_section">
        <div class="mdc-tab-bar" role="tablist">
             <div class="mdc-tab-scroller">
                    <div class="mdc-tab-scroller__scroll-area mdc-tab-scroller__scroll-area--scroll">
                        <div class="mdc-tab-scroller__scroll-content">
                            <button class="mdc-tab mdc-tab--active rtwwwap_car_width rtwwwap_link_table" role="tab" aria-selected="true" tabindex="0">
                                <span class="mdc-tab__content">
                                <span class="mdc-tab__icon material-icons" aria-hidden="true">'.esc_html__( 'report', 'rtwwwap-wp-wc-affiliate-program' ).'</span>
                                <span class="mdc-tab__text-label">'.esc_html__( 'Report Wise', 'rtwwwap-wp-wc-affiliate-program' ).'</span>
                                </span>
                                <span class="mdc-tab-indicator mdc-tab-indicator--active">
                                    <span class="mdc-tab-indicator__content mdc-tab-indicator__content--underline"></span>
                                </span>
                                <span class="mdc-tab__ripple mdc-ripple-upgraded"></span>
                            </button>
                            <button class="mdc-tab rtwwwap_car_width rtwwwap_date_wise" role="tab" aria-selected="false" tabindex="-1">
                                <span class="mdc-tab__content">
                                <span class="mdc-tab__icon material-icons" aria-hidden="true">'.esc_html__( 'date_range', 'rtwwwap-wp-wc-affiliate-program' ).'</span>
                                <span class="mdc-tab__text-label">'.esc_html__( 'Day Wise', 'rtwwwap-wp-wc-affiliate-program' ).'</span>
                                </span>
                                <span class="mdc-tab-indicator">
                                <span class="mdc-tab-indicator__content mdc-tab-indicator__content--underline"></span>
                                </span><span class="mdc-tab__ripple mdc-ripple-upgraded"></span>
                            </button>
                    </div>
                </div>
            </div>
        </div>
                <div class="rtwwwap_main_part_display">
                    <div class="rtwwwap_report_tab_date">
                        <div class="rtwwwap-text">
                            <h4>
                                <span> <i class="fas fa-retweet mr-1"></i> '.esc_html__( 'Report Table', 'rtwwwap-wp-wc-affiliate-program' ).'</span>
                            </h4>
                        </div>
                        <div class="mdc-card mdc-elevation--z9">
                            <table id="rtwwwap_report_date_wise" class="mdl-data-table">
                                <thead>';
                                $rtwwwap_html1 .= 			'<tr>';
                                $rtwwwap_html1 .= 				'<th>';
                                $rtwwwap_html1 .= 					sprintf( '%s', esc_html__( 'Total Order', 'rtwwwap-wp-wc-affiliate-program' ) );
                                $rtwwwap_html1 .= 				'</th>';
                                $rtwwwap_html1 .= 				'<th>';
                                $rtwwwap_html1 .= 					sprintf( '%s (%s)', esc_html__( 'Date', 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_currency_sym );
                                $rtwwwap_html1 .= 				'</th>';
                                $rtwwwap_html1 .= 				'<th>';
                                $rtwwwap_html1 .= 					sprintf( '%s', esc_html__( 'Total Coomission earned ', 'rtwwwap-wp-wc-affiliate-program' ) );
                                $rtwwwap_html1 .= 				'</th>';
                                $rtwwwap_html1 .= 				'<th>';
                                $rtwwwap_html1 .= 					sprintf( '%s', esc_html__( 'Total Sales amount ', 'rtwwwap-wp-wc-affiliate-program' ) );
                                $rtwwwap_html1 .= 				'</th>';
                                $rtwwwap_html1 .= 			'</tr>';
                                $rtwwwap_html1 .=            ' </thead>
                                <tbody>';

                                $rtwwwap_from_name 		= esc_html( get_bloginfo( 'name' ) );
                                $rtwwwap_from_email 	= esc_html( get_bloginfo( 'admin_email' ) );
                                
                            foreach($final_commission_array as $key => $value)    
                                    {
                                        
                                        $rtwwwap_html1 .= 		'<tr>';
                                        if(isset($value['total_order']))
                                        {
                                         $rtwwwap_html1 .= 			'<td>'.(int)$value['total_order'].'</td>';
                                            
                                        }else{
                                            $rtwwwap_html1 .= 			'<td>0</td>';
                                        }
                                        $rtwwwap_html1 .= 			'<td>'. $key.'</td>';
                                        $rtwwwap_html1 .= 			'<td>'. wc_price($value['total_commission']).'</td>';
                                        $rtwwwap_html1 .= 			'<td>'. wc_price($value['total_prod_price']).'</td>';
                                        $rtwwwap_html1 .= 		'</tr>';                           
                                    }
                                

                        

                    $rtwwwap_html1 .=                     ' </tbody>
                            </table>
                        
                        </div>

                    </div>

                    <div class="rtwwwap_report_tab">
                        <div class="report_chart">
                            <div id="rtwwwap_report_line_chart">
                                <canvas id="rtwwwap_report"></canvas>
                                <p class="rtwwwap_chart_heading" >'.esc_html__( 'Report Cart', 'rtwwwap-wp-wc-affiliate-program' ).'</p>
                            </div>
                            <div id="rtwwwap_report_line_chart">
                                <canvas id="rtwwwap_reports"></canvas>
                                <p class="rtwwwap_chart_heading" >'.esc_html__( 'Puchase Medium Device', 'rtwwwap-wp-wc-affiliate-program' ).'</p>
                            </div>
                        </div>
                        <div class="rtwwwap-text">
                            <h4>
                                <span> <i class="fas fa-retweet mr-1"></i>'.esc_html__( 'Report Table', 'rtwwwap-wp-wc-affiliate-program' ).'</span>
                            </h4>
                        </div>
                

                            <div class="mdc-card mdc-elevation--z9">
                                <table id="rtwwwap_report_sec_table" class="mdl-data-table">
                                    <thead>';
                                    $rtwwwap_html1 .= 			'<tr>';
                                    $rtwwwap_html1 .= 				'<th>';
                                    $rtwwwap_html1 .= 					sprintf( '%s', esc_html__( 'Link', 'rtwwwap-wp-wc-affiliate-program' ) );
                                    $rtwwwap_html1 .= 				'</th>';
                                    $rtwwwap_html1 .= 				'<th>';
                                    $rtwwwap_html1 .= 					sprintf( '%s (%s)', esc_html__( 'Number of Hits', 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_currency_sym );
                                    $rtwwwap_html1 .= 				'</th>';
                                    $rtwwwap_html1 .= 				'<th>';
                                    $rtwwwap_html1 .= 					sprintf( '%s', esc_html__( 'Purchase Done', 'rtwwwap-wp-wc-affiliate-program' ) );
                                    $rtwwwap_html1 .= 				'</th>';
                                    $rtwwwap_html1 .= 			'</tr>';
                                    $rtwwwap_html1 .=            ' </thead>
                                    <tbody>';

                
                                    
                                    
                                    foreach($rtwwwap_user_referrals_links as $key => $value)
                                    {
                                    $rtwwwap_html1 .= 		'<tr>';
                                    $rtwwwap_html1 .= 			'<td>'.$value['aff_link'].'</td>';
                                    $rtwwwap_html1 .= 			'<td>'.$value['link_open'].'</td>';
                                    $rtwwwap_html1 .= 			'<td>'.$value['link_purchase'].'</td>';
                                    $rtwwwap_html1 .= 		'</tr>';
                                    }
                                    


                        $rtwwwap_html1 .=                     ' </tbody>
                                </table>
                            </div>
                     </div>
            </div>
    </div>';





// Download Tab start 

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



$rtwwwap_html1 .= ' <div class="rtwwwap-download-wrapper mdc-layout-grid rtwwwap_hide" id="rtwwwap_download_tab">';
                           

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
$rtwwwap_html1 .=	    '        <button class="mdc-button mdc-button--raised" id="rtwwwap_generate_csv"">
                        <span class="mdc-button__label">Generate CSV</span>
                    </button>';
$rtwwwap_html1 .= 	'</div>';
                        
          
// payout tab
global $wpdb;
$rtwwwap_affiliate_wallet_transaction = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM '.$wpdb->prefix.'rtwwwap_wallet_transaction WHERE `aff_id` = %d ',$rtwwwap_user_id), ARRAY_A );

$rtwwwap_referral_mail 	= get_user_meta( $rtwwwap_user_id, 'rtwwwap_referral_mail', true );
$rtwwwap_payment_method = get_user_meta( $rtwwwap_user_id, 'rtwwwap_payment_method', true );
$rtwwwap_paypal_email 	= get_user_meta( $rtwwwap_user_id, 'rtwwwap_paypal_email', true );
$rtwwwap_stripe_email 	= get_user_meta( $rtwwwap_user_id, 'rtwwwap_stripe_email', true );
$rtwwwap_paystack_email 	= get_user_meta( $rtwwwap_user_id, 'rtwwwap_paystack_email', true );
$rtwwwap_direct_details = get_user_meta( $rtwwwap_user_id, 'rtwwwap_direct', true );
$rtwwwap_swift_code = get_user_meta( $rtwwwap_user_id, 'rtwwwap_swift_code', true );

$rtwwwap_commission_settings = get_option( 'rtwwwap_commission_settings_opt' );
$rtwwwap_comm_base = isset( $rtwwwap_commission_settings[ 'comm_base' ] ) ? $rtwwwap_commission_settings[ 'comm_base' ] : '1';

$rtwwwap_admin_paypal 	= isset( $rtwwwap_extra_features[ 'activate_paypal' ] ) ? $rtwwwap_extra_features[ 'activate_paypal' ] : 0;
$rtwwwap_admin_stripe 	= isset( $rtwwwap_extra_features[ 'activate_stripe' ] ) ? $rtwwwap_extra_features[ 'activate_stripe' ] : 0;

$rtwwwap_admin_paystack 	= isset( $rtwwwap_extra_features[ 'activate_paystack' ] ) ? $rtwwwap_extra_features[ 'activate_paystack' ] : 0;

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

$rtwwwap_user_name       =          wp_get_current_user();

$rtwwwap_referral_code_active = isset( $rtwwwap_extra_features[ 'signup_bonus_type' ] ) ? $rtwwwap_extra_features[ 'signup_bonus_type' ] : 0;
// $rtwwwap_user_name 		= $rtwwwap_user_name->data->user_login;
// $rtwwwap_referral_code 	= $rtwwwap_user_name.'_'.$rtwwwap_user_id;

$rtwwwap_referral_code = get_user_meta( $rtwwwap_user_id, 'rtwwwap_referee_custom_str', true );

$rtwwwap_html1 .=	'   
                                <!-- payout section start -->
                                <div class=" mdc-layout-grid rtwwwap-payout-card-section  rtwwwap_hide" id="rtwwwap_payout_tab">
                                    ';
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

$rtwwwap_html1 .=               '<div class="rtwwwap_payout_car_main_div">
                                    <div class="mdc-layout-grid__inner">';
                         if( $rtwwwap_comm_base == 2 && $rtwwwap_level_name != '' ){
                                 if( $rtwwwap_referral_code_active ){
$rtwwwap_html1 .=  '                    <div class="mdc-layout-grid__cell mdc-card rtwwwap-payout-grid-cell mdc-elevation--z9">
                                            <div class="rtwwwap-inner-padding">
                                                <div id="rtwwwap_refferal-code-box">
                                                    <span class="rtwwwap_referral_span">'.esc_html__( 'Your Referral Code :', 'rtwwwap-wp-wc-affiliate-program' ).'</span><span>'.$rtwwwap_referral_code.'</span>
                                                </div>
                                            </div>
                                        </div>';
            
                                                }
                
$rtwwwap_html1 .=	'                    <div class="mdc-layout-grid__cell mdc-card rtwwwap-payout-grid-cell mdc-elevation--z9">
                                            <div class="rtwwwap-inner-padding ">
                                                <div class="rtwwwap-card-progress-wrapper-row">
                                                    <div class="rtwwwap-card-number">
                                                        <span>'.esc_html__( 'Your Affiliate Level', 'rtwwwap-wp-wc-affiliate-program' ).'</span>
                                                    </div>
                                                    <div class="rtwwwap-progress">
                                                        <div class="rtwwwap-progress-bar  rtwwwap_progress1" role="progressbar"></div>
                                                    </div>
                                                </div>
                                                <div class="rtwwwap-card-text">
                                                    <p>'.esc_html__( $rtwwwap_level_name, "rtwwwap-wp-wc-affiliate-program" ).'</p>
                                                </div>
                                            </div>
                                            
                                        </div>
                                        <div class="mdc-layout-grid__cell mdc-card rtwwwap-payout-grid-cell mdc-elevation--z9">
                                            <div class="rtwwwap-inner-padding">
                                                <div class="rtwwwap-card-progress-wrapper-row">
                                                    <div class="rtwwwap-card-number">
                                                        <span>'.esc_html__( 'Your Commission Level', 'rtwwwap-wp-wc-affiliate-program' ).'</span>	
                                                    </div>
                                                    <div class="rtwwwap-progress">
                                                        <div class="rtwwwap-progress-bar rtwwwap_progress2" role="progressbar"></div>
                                                    </div>
                                                </div>
                                                <div class="rtwwwap-card-text">
                                                    <p>'.sprintf( '%s%s', esc_html( $rtwwwap_level_comm ), esc_html( $rtwwwap_level_comm_type ) ).'</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        ';
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
$rtwwwap_html1 .=	 '                  <div class="mdc-layout-grid__cell mdc-card rtwwwap-payout-grid-cell mdc-elevation--z9">
                                            <div class="rtwwwap-inner-padding">
                                                <div class="rtwwwap-referral-row">
                                                    <div class="rtwwwap-refferal-card-text rtwwwap-card-number">'.esc_html__( 'Activate Refferral Emails', 'rtwwwap-wp-wc-affiliate-program' ).'</div>
                                                    <div class="mdc-touch-target-wrapper">
                                                        <div class="mdc-checkbox mdc-checkbox--touch">';

                                                        if( isset( $rtwwwap_referral_mail ) && $rtwwwap_referral_mail == "true" ){
                                                            $rtwwwap_html1 .=	 ' <input type="checkbox"
                                                                    class="mdc-checkbox__native-control"
                                                                    id="rtwwwap_referral_email" checked="checked"/>';
                                                        }
                                                        else{
                                                            $rtwwwap_html1 .=	 ' <input type="checkbox"
                                                            class="mdc-checkbox__native-control"
                                                            id="rtwwwap_referral_email" />';
                                                        }
                                                        
$rtwwwap_html1 .=	 '                                  <div class="mdc-checkbox__background">
                                                            <svg class="mdc-checkbox__checkmark"
                                                                    viewBox="0 0 24 24">
                                                                <path class="mdc-checkbox__checkmark-path"
                                                                    fill="none"
                                                                    d="M1.73,12.91 8.1,19.28 22.79,4.59"/>
                                                            </svg>
                                                            <div class="mdc-checkbox__mixedmark"></div>
                                                            </div>
                                                            <div class="mdc-checkbox__ripple"></div>
                                                        </div>
                                                        </div>
                                                </div>
                                            </div>
                                        </div>

                                         <div class="mdc-layout-grid__cell mdc-card rtwwwap-payout-grid-cell mdc-elevation--z9">
                                            <div class="rtwwwap-inner-padding">
                                                <div class="rtwwwap-referral-row">
                                                    <div class="rtwwwap-refferal-card-text rtwwwap-card-number">'.esc_html__( 'Coupon Code', 'rtwwwap-wp-wc-affiliate-program' ).'</div>
                                                    <div class="mdc-touch-target-wrapper">
                                                        : <strong>'.esc_attr($rtwwwap_coupon_title).'</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mdc-layout-grid__cell mdc-card rtwwwap-payout-grid-cell mdc-elevation--z9">
                                            <div class="rtwwwap-inner-padding">
                                                <div class="rtwwwap-referral-row">
                                                    <div class="rtwwwap-refferal-card-text rtwwwap-card-number">'.esc_html__( 'Select payment method :', 'rtwwwap-wp-wc-affiliate-program' ).'</div>
                                                    <div class="rtwwwap_select_wrapper">
                                                        <div>';
                                                        
    $rtwwwap_html1 .=	 '                                  <div>';
                                                                
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
                                        $rtwwwap_html1 .=			esc_html__( 'Paystack', 'rtwwwap-wp-wc-affiliate-program' );
                                        $rtwwwap_html1 .= 			'</option>';
                                    }

                                    $rtwwwap_html1 .=	  		'</select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <section class="rtwwwap-_payemrnt_method_section">
                                    <form method="post">
                                        <div class="rtwwwap_payment_main_wrapper">
                                            <div class="rtwwwap-payment-method-wrapper">
                                                <div class="mdc-card mdc-elevation--z9">
                                                    <div class="mdc-tab-bar" role="tablist">
                                                        <h3 class="rtwwwap-payment-header">Select payment method</h3>
                                                        <div class="mdc-tab-scroller">
                                                            <div class="mdc-tab-scroller__scroll-area mdc-tab-scroller__scroll-area--scroll" style="margin-bottom: 0px;">
                                                                <div class="mdc-tab-scroller__scroll-content rt">
                                                                    <span class="mdc-tab mdc-tab--stacked mdc-tab--active" role="tab" aria-selected="true" tabindex="0" id="rtwwwap-btn-1">
                                                                        <span class="mdc-tab__content">
                                                                            <i class="fas fa-university mdc-tab__icon"></i>
                                                                            <span class="mdc-tab__text-label">'.esc_html__( 'Direct bank', 'rtwwwap-wp-wc-affiliate-program' ).'</span>
                                                                            <span class="mdc-tab-indicator mdc-tab-indicator--active">
                                                                                <span class="mdc-tab-indicator__content mdc-tab-indicator__content--underline"></span>
                                                                            </span>
                                                                        </span>
                                                                        <span class="mdc-tab__ripple mdc-ripple-upgraded"></span>
                                                                    </span>';
                                                                    if($rtwwwap_admin_paypal)
                                                                    {

                                    $rtwwwap_html1 .=               '<span class="mdc-tab mdc-tab--stacked" role="tab"                                               aria-selected="false" tabindex="-1" id="rtwwwap-btn-2">
                                                                        <span class="mdc-tab__content">
                                                                            <i class="fab fa-paypal mdc-tab__icon "></i>
                                                                            <span class="mdc-tab__text-label">'.esc_html__( 'Paypal', 'rtwwwap-wp-wc-affiliate-program' ).'</span>
                                                                            <span class="mdc-tab-indicator">
                                                                                <span class="mdc-tab-indicator__content mdc-tab-indicator__content--underline"></span>
                                                                            </span>
                                                                        </span>
                                                                        <span class="mdc-tab__ripple mdc-ripple-upgraded"></span>
                                                                    </span>';
                                                                    }
                                                                    if($rtwwwap_admin_stripe )
                                                                 {
                                    $rtwwwap_html1 .=               '<span class="mdc-tab mdc-tab--stacked" role="tab"                                                  aria-selected="false" tabindex="-1" id="rtwwwap-btn-3">
                                                                        <span class="mdc-tab__content">
                                                                            <i class="fab fa-stripe-s mdc-tab__icon"></i>
                                                                        <span class="mdc-tab__text-label">'.esc_html__( 'Stripe', 'rtwwwap-wp-wc-affiliate-program' ).'</span>
                                                                            <span class="mdc-tab-indicator">
                                                                                <span class="mdc-tab-indicator__content mdc-tab-indicator__content--underline"></span>
                                                                            </span>
                                                                        </span>
                                                                        <span class="mdc-tab__ripple mdc-ripple-upgraded"></span>
                                                                    </span>';
                                                                
                                                                }

                                                                if($rtwwwap_admin_paystack )
                                                                {
                                   $rtwwwap_html1 .=               '<span class="mdc-tab mdc-tab--stacked" role="tab"                                                  aria-selected="false" tabindex="-1" id="rtwwwap-btn-4">
                                                                       <span class="mdc-tab__content">
                                                                       <img height="50" width="50" src="'.esc_url( RTWWWAP_URL.'/assets/images/paystack_icon.webp' ).'">
                                                                       <span class="mdc-tab__text-label">'.esc_html__( 'Paystack', 'rtwwwap-wp-wc-affiliate-program' ).'</span>
                                                                           <span class="mdc-tab-indicator">
                                                                               <span class="mdc-tab-indicator__content mdc-tab-indicator__content--underline"></span>
                                                                           </span>
                                                                       </span>
                                                                       <span class="mdc-tab__ripple mdc-ripple-upgraded"></span>
                                                                   </span>';
                                                               
                                                               }

                $rtwwwap_html1 .=                             '</div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="rtwwwap-payment-content-box">
                                                        <div class="rtwwwap-bank-deatil rtwwwap-content-active-tab">
                                                            <h4>'.esc_html__( 'Bank Account Deatils', 'rtwwwap-wp-wc-affiliate-program' ).'</h4>
                                                            
                                                                <div class="rtwwwap-input-padding">
                                                                    <label class="mdc-text-field  mdc-text-field--textarea mdc-text-field--no-label rtwwwap-w-100">
                                                                    <textarea class=" mdc-text-field__input rtwwwap_privacy_policy_content" aria-label="Label" placeholder="'.esc_html__( 'Enter Your Message for Commission to Admin', 'rtwwwap-wp-wc-affiliate-program' ).'" id="rtwwwap_direct" data-bank_account = "'.$rtwwwap_direct_details.'" > '.	$rtwwwap_direct_details.'</textarea>
                                                                    <span class="mdc-notched-outline mdc-notched-outline--no-label">
                                                                        <span class="mdc-notched-outline__leading"></span>
                                                                        <span class="mdc-notched-outline__trailing"></span>
                                                                    </span>
                                                                    </label>
                                                                </div>';
                                                                $rtwwwap_html1 .= '<div>';
                                                               
                                                                if( in_array('addon_for_MLM_qualification/affiliate_mlm_addon.php', apply_filters('active_plugins', get_option('active_plugins') ) ) )
                                                                {
                                                                        $rtwwwap_html1 .= apply_filters('show_html_on_payout_tab',$rtwwwap_swift_code );
                                                                }

                                                                $rtwwwap_html1 .= '</div>'; 
                                                                $rtwwwap_html1 .= '</div>';                                    
                                                  
                    $rtwwwap_html1 .=                   '<div class="rtwwwap-paypal-deatil">
                                                            <div class="rtwwwap-paypal-imag">
                                                                <img src="'.esc_url( RTWWWAP_URL.'/assets/images/paypal.png' ).'">
                                                            </div>
                                                           
                                                                <div class="rtwwwap-paypal-input-padding">
                                                                    <label class="mdc-text-field mdc-text-field--outlined rtwwwap-payment-text-field">
                                                                        <input type="text" class="mdc-text-field__input" id="rtwwwap_paypal_email" value="'.$rtwwwap_paypal_email.'">
                                                                        <div class="mdc-notched-outline mdc-notched-outline--upgraded">
                                                                            <div class="mdc-notched-outline__leading"></div>
                                                                            <div class="mdc-notched-outline__notch">
                                                                            <span class="mdc-floating-label">'.esc_html__( 'Enter Your Paypal Email here', 'rtwwwap-wp-wc-affiliate-program' ).'</span>
                                                                            </div>
                                                                            <div class="mdc-notched-outline__trailing"></div>
                                                                        </div>
                                                                    </label>
                                                                </div>
                                                               
                                                          
                                                        </div>';
                                                    
                                                    
                            $rtwwwap_html1 .=          '<div class="rtwwwap-stripe-deatil">
                                                            <div class="rtwwwap-paypal-imag">
                                                                <img src="'.esc_url( RTWWWAP_URL.'/assets/images/stripe.png' ).'">
                                                            </div>
                                                            <label class="mdc-text-field mdc-text-field--outlined rtwwwap-payment-text-field">
                                                                    <input type="text" class="mdc-text-field__input" id="rtwwwap_stripe_email" value="'.$rtwwwap_stripe_email.'">
                                                                    <div class="mdc-notched-outline mdc-notched-outline--upgraded">
                                                                        <div class="mdc-notched-outline__leading"></div>
                                                                        <div class="mdc-notched-outline__notch">
                                                                        <span class="mdc-floating-label">'.esc_html__( 'Enter Your Stripe Email here', 'rtwwwap-wp-wc-affiliate-program' ).'</span>
                                                                        </div>
                                                                        <div class="mdc-notched-outline__trailing"></div>
                                                                    </div>
                                                                </label>
                                                              
                                                        </div>';

                                                    $rtwwwap_html1 .=          '<div class="rtwwwap-paystack-deatil" id ="rtwwwap-paystack-deatil">
                                                        <div class="rtwwwap-paypal-imag">
                                                            <img src="'.esc_url( RTWWWAP_URL.'assets/images/paystack.jpeg' ).'">
                                                        </div>
                                                        <label class="mdc-text-field mdc-text-field--outlined rtwwwap-payment-text-field">
                                                                <input type="text" class="mdc-text-field__input" id="rtwwwap_paystack_email" value="'.$rtwwwap_paystack_email.'">
                                                                <div class="mdc-notched-outline mdc-notched-outline--upgraded">
                                                                    <div class="mdc-notched-outline__leading"></div>
                                                                    <div class="mdc-notched-outline__notch">
                                                                    <span class="mdc-floating-label">'.esc_html__( 'Enter Your Paystack Email here', 'rtwwwap-wp-wc-affiliate-program' ).'</span>
                                                                    </div>
                                                                    <div class="mdc-notched-outline__trailing"></div>
                                                                </div>
                                                            </label>
                                                            
                                                    </div>
           
                                                        <div class="rtwwwap-payment-detail-btn">
                                                        <span 
                                                        name="rtwwwap_payout_save" id="rtwwwap_payout_save" class="mdc-button mdc-button--raised mdc-ripple-upgraded" >
                                                            <span class="mdc-button__label">'.esc_html__( 'Save Details', 'rtwwwap-wp-wc-affiliate-program' ).'</span>
                                                        </span>
                                                    </div>';
                    $rtwwwap_html1 .=               '</div>                                      
                                                </div>
                                            </div>
                                        </div>
                                     </form>
                                   </section>
                                </div>
                            ';

//custom field function 

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
                    $rtwwwap_html .=  '<div class="rtwwwap-input-padding">';
                    $rtwwwap_html .=  '<div class="mdc-text-field rtwwwap-payment-text-field mdc-ripple-upgraded">';
                    $rtwwwap_html .= 	'<input type="'.$custom_fields['custom-input-type'].'" name="'.$custom_fields['custom-input-id'].'"  id="'.$custom_fields['custom-input-id'].'" class="'.$custom_fields['custom-input-class'].' rtwwwap_custom_fields mdc-text-field__input" placeholder="'.esc_attr__( $custom_fields['custom-input-label'], "rtwwwap-wp-wc-affiliate-program" ).'"" value="'.$rtwwwap_reg_user_custom_field.'" />';
                    $rtwwwap_html .=  '<div class="mdc-line-ripple"></div>';
                    $rtwwwap_html .= 		'<label class="mdc-floating-label">'.esc_html__( $custom_fields['custom-input-label'], "rtwwwap-wp-wc-affiliate-program" ).'</label>';
                    $rtwwwap_html .= '</div>';
                    $rtwwwap_html .= '</div>';
                }elseif ($custom_fields['custom-input-type'] == 'textarea') {
            
                    $rtwwwap_html .=  ' <div class="rtwwwap-input-padding"> 
                    <label class="mdc-text-field  mdc-text-field--textarea mdc-text-field--no-label rtwwwap-w-100">Address</label>
                    <label class="mdc-text-field mdc-text-field--filled mdc-text-field--textarea mdc-text-field--no-label rtwwwap-w-100">
<span class="mdc-text-field__ripple"></span>

<textarea name="'.$custom_fields['custom-input-id'].'"  id="'.$custom_fields['custom-input-id'].'" class=" mdc-text-field__input rtwwwap_custom_fields '.$custom_fields['custom-input-class'].'" placeholder="'.esc_attr__( $custom_fields['custom-input-label'], "rtwwwap-wp-wc-affiliate-program" ).'">'.$rtwwwap_reg_user_custom_field.'</textarea>

<span class="mdc-line-ripple"></span>
</label>';
                  
                    $rtwwwap_html .= '</div>';
                }
                elseif ($custom_fields['custom-input-type'] == 'checkbox') {
                    $rtwwwap_html .=  '<div class="rtwwwap-input-padding rtwwwap-custom-checkbox">';
                    $rtwwwap_html .=  '<div class="mdc-touch-target-wrapper rtwwwap_row_check">';
                    $rtwwwap_html .=  '<div class="mdc-checkbox mdc-checkbox--touch">';
                    $rtwwwap_html .=  '<input type="'.$custom_fields['custom-input-type'].'" name="'.$value.'"  id="'.$custom_fields['custom-input-id'].'" class="mdc-checkbox__native-control rtwwwap_custom_fields '.$custom_fields['custom-input-class'].'" placeholder="'.esc_attr__( $custom_fields['custom-input-label'], "rtwwwap-wp-wc-affiliate-program" ).'"" value="'.esc_attr__(trim($value),"rtwwwap-wp-wc-affiliate-program").'" '.checked($rtwwwap_value_checked,$value,false). ' />';
                    $rtwwwap_html .=  '<div class="mdc-checkbox__background">';
                    $rtwwwap_html .=  '<svg class="mdc-checkbox__checkmark" viewBox="0 0 24 24">
                    <path class="mdc-checkbox__checkmark-path" fill="none" d="M1.73,12.91 8.1,19.28 22.79,4.59"></path>
                </svg>';
                    $rtwwwap_html .=   '</div>';
                    $rtwwwap_html .=  '<div class="mdc-checkbox__ripple"></div>';
                    $rtwwwap_html .=   '</div>';
                    $rtwwwap_html .= 	'<label>'.esc_html__( $custom_fields['custom-input-label'], "rtwwwap-wp-wc-affiliate-program" ).'</label>';
                    $rtwwwap_html .=   '</div>';
                    
                    $rtwwwap_checkbox_options = explode('|',$custom_fields['custom-input-options']);
                    if(is_array($rtwwwap_checkbox_options) && !empty($rtwwwap_checkbox_options)){
                        $rtwwwap_html .= '<div class="rtwwwap-custom-checkbox">';
                        foreach ($rtwwwap_checkbox_options as $value) {
                            $rtwwwap_value_checked          =         isset($rtwwwap_userdata[$value][0]) ? $rtwwwap_userdata[$value][0] : ''; 
                            $rtwwwap_html .= 	'<label><input type="'.$custom_fields['custom-input-type'].'" name="'.$value.'"  id="'.$custom_fields['custom-input-id'].'" class="'.$custom_fields['custom-input-class'].'  rtwwwap_custom_fields" placeholder="'.esc_attr__( $custom_fields['custom-input-label'], "rtwwwap-wp-wc-affiliate-program" ).'"" value="'.esc_attr__(trim($value),"rtwwwap-wp-wc-affiliate-program").'" '.checked($rtwwwap_value_checked,$value,false). ' />'.esc_html__($value,"rtwwwap-wp-wc-affiliate-program").'</label>';

                        }
                        $rtwwwap_html .= '</div>';
                        $rtwwwap_html .= '</div>';
                    }
                }elseif ($custom_fields['custom-input-type'] == 'radio') {
                    $rtwwwap_html .= 					'<label>'.esc_html__( $custom_fields['custom-input-label'], "rtwwwap-wp-wc-affiliate-program" ).'</label>';
                    $rtwwwap_checkbox_options = explode('|',$custom_fields['custom-input-options']);
                    if(is_array($rtwwwap_checkbox_options) && !empty($rtwwwap_checkbox_options)){
                        $rtwwwap_html .= '<div class="rtwwwap-custom-radio">';
                        foreach ($rtwwwap_checkbox_options as $value) {
                            $rtwwwap_html .= 	'<label for="'.$custom_fields['custom-input-id'].'"><input type="'.$custom_fields['custom-input-type'].'" name="'.$custom_fields['custom-input-id'].'"  id="'.$custom_fields['custom-input-id'].'" class="'.$custom_fields['custom-input-class'].' rtwwwap_custom_fields" placeholder="'.esc_attr__( $custom_fields['custom-input-label'], "rtwwwap-wp-wc-affiliate-program" ).'"" value="'.esc_attr__(trim($value),"rtwwwap-wp-wc-affiliate-program").'" '.checked(isset($rtwwwap_userdata[$custom_fields['custom-input-id']][0]) ? $rtwwwap_userdata[$custom_fields['custom-input-id']][0] : ''  ,$value,false). ' />'.esc_html__($value,"rtwwwap-wp-wc-affiliate-program").'</label>';

                        }
                        $rtwwwap_html .= '</div>';
                    }
                }
                elseif ($custom_fields['custom-input-type'] == 'select') {
                    $rtwwwap_html .= 					'<label>'.esc_html__( $custom_fields['custom-input-label'], "rtwwwap-wp-wc-affiliate-program" ).'</label>';
                    $rtwwwap_checkbox_options = explode('|',$custom_fields['custom-input-options']);
                    if(is_array($rtwwwap_checkbox_options) && !empty($rtwwwap_checkbox_options)){
                        $rtwwwap_html .= 	'<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="far fa-envelope"></i></span><select name="'.$custom_fields['custom-input-id'].'"  id="'.$custom_fields['custom-input-id'].'" class="'.$custom_fields['custom-input-class'].' rtwwwap_custom_fields" >';
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

// profile tab //

$rtwwwap_userdata = get_user_meta($rtwwwap_user_id);
$rtwwwap_user = get_userdata($rtwwwap_user_id);


$rtwwwap_reg_temp_features = get_option( 'rtwwwap_reg_temp_opt' );

// $rtwwwap_custom_fields = array();
// $rtwwap_custom_firlds_array = $rtwwwap_reg_temp_features['custom-input'];

//  foreach($rtwwap_custom_firlds_array as $custom => $value)
//  {
//     $rtwwwap_custom_fields[] =  $value['custom-input-id'] ;
//  }

//   $rtwwwap_custom_fields = json_encode($rtwwwap_custom_fields);


$rtwwwap_html1 .= '  <form method="post">
                        <section class="rtwwwap-profile-section rtwwwap_hide" id="rtwwwap_profile_tab">

                        <div class="mdc-layout-grid rtwwwap-payment-section-wrapper mdc-elevation--z9">
                            <div class="mdc-elevation--z4 rtwwwap-payment-input-box">
                                    <span class="rtwwwap-login-form-logo">
                                        <i class="fas fa-user" aria-hidden="true"></i>
                                    </span>
                                <h4 class="rtwwwap-login-form-title">Profile</h4>
                                <div class="rtwwwap-input-padding">
                                    <div class="mdc-text-field rtwwwap-payment-text-field mdc-ripple-upgraded">
                                        <input class="mdc-text-field__input  rtwwwap_custom_fields" name="user_login" id="username" value="'.$rtwwwap_userdata['nickname'][0].'" disabled ">
                                        <div class="mdc-line-ripple"></div>
                                        <label class="mdc-floating-label color_white">'.esc_html__( 'Usename', 'rtwwwap-wp-wc-affiliate-program' ).'</label>
                                    </div>
                                </div>
                                <div class="rtwwwap-input-padding">
                                    <div class="mdc-text-field rtwwwap-payment-text-field mdc-ripple-upgraded">
                                        <input class="mdc-text-field__input rtwwwap_custom_fields" name="user_email" id="unseremail" value="'.$rtwwwap_user->user_email.'" disabled>
                                        <div class="mdc-line-ripple"></div>
                                        <label class="mdc-floating-label color_white">'.esc_html__( 'Email', 'rtwwwap-wp-wc-affiliate-program' ).'</label>
                                    </div>
                                </div>	
                                <div class="rtwwwap-input-padding">
                                    <div class="mdc-text-field rtwwwap-payment-text-field mdc-ripple-upgraded">
                                        <input class="mdc-text-field__input rtwwwap_custom_fields" name="first_name" id="userfirstname" value="'.$rtwwwap_userdata['first_name'][0].'">
                                        <div class="mdc-line-ripple"></div>
                                        <label class="mdc-floating-label">'.esc_html__( 'First Name', 'rtwwwap-wp-wc-affiliate-program' ).'</label>
                                    </div>
                                </div>
                                <div class="rtwwwap-input-padding">
                                    <div class="mdc-text-field rtwwwap-payment-text-field mdc-ripple-upgraded">
                                        <input class="mdc-text-field__input rtwwwap_custom_fields" name="last_name" id="userlastname" value="'.$rtwwwap_userdata['last_name'][0].'">
                                        <div class="mdc-line-ripple"></div>
                                        <label class="mdc-floating-label">'.esc_html__( 'Last name', 'rtwwwap-wp-wc-affiliate-program' ).'</label>
                                    </div>
                                </div>';
    $rtwwwap_html1 .= 	rtwwwap_custom_form_fields_data($rtwwwap_userdata);


                        $rtwwwap_html1 .= '<div>';

                        if( in_array('addon_for_MLM_qualification/affiliate_mlm_addon.php', apply_filters('active_plugins', get_option('active_plugins') ) ) )
                        {
                            $rtwwwap_html1 .= apply_filters('show_html_on_profile_tab', $rtwwwap_user_id);
                        }

                        $rtwwwap_html1 .= '</div>';
   

$rtwwwap_html1 .=              '<div class="rtwwwap-update-btn">';


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

// $rtwwwap_html1 .= '<div>';
// do_action('show_html_on_profile_tab');
// $rtwwwap_html1 .= '</div>';
$rtwwwap_html1 .=   '<span class="mdc-button mdc-button--raised mdc-theme--primary mdc-ripple-upgraded rtwwwap_payment_submit_button rtwwwap_profile_save"  id="rtwwwap_profile_save"  name="rtwwwap_profile_save" >'.esc_html__('Update Detail',"rtwwwap-wp-wc-affiliate-program").'</span>
<span class="mdc-button mdc-button--raised mdc-theme--primary mdc-ripple-upgraded rtwwwap_profile_change_psw"  id="rtwwwap_change_psw"  name="rtwwwap_profile_save" >'.esc_html__('Change password',"rtwwwap-wp-wc-affiliate-program").'</span>
                                </div>
                            </div>
                        </div>
                 
                </section>
                </form> ';


                // <input type="button" class="rtwwwap_profile_change_psw" value="'.esc_html__( "Change password", "rtwwwap-wp-wc-affiliate-program" ).'" id="rtwwwap_change_psw" name="rtwwwap_profile_save">

/// create banner//      

$rtwwwap_custom_banner = get_option( 'rtwwwap_custom_banner_opt' );
    if(	$rtwwwap_custom_banner != '' )
    {
        $rtwwwap_count = 1;
        
        $rtwwwap_html1 .= 	'<div class="rtwwwap_custom_banner_container rtwwwap_hide" id="rtwwwap_custom_banner_tab">';
        $rtwwwap_html1 .= 	'<div class="rtwwwap_custom_banner_row">';
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

        $rtwwwap_html1 .= 	'</div>';
    }




    
///create Banner tab start ///

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



$rtwwwap_html1 .= '  <div class="rtwwwap_hide" id="rtwwwap_create_banner_tab" ><h4>Create Banners</h4>
                            <div class="rtwwwap-search-prdct-input">
                                <label class="mdc-text-field mdc-text-field--outlined rtwwwap-w-100">
                                    <input type="text" name="product_name" class="mdc-text-field__input"  id="rtwwwap_banner_prod_search">
                                    <div class="mdc-notched-outline mdc-notched-outline--upgraded">
                                        <div class="mdc-notched-outline__leading"></div>
                                        <div class="mdc-notched-outline__notch">
                                        <span class="mdc-floating-label"> '.esc_html__( 'Search products', 'rtwwwap-wp-wc-affiliate-program' ).'</span>
                                        </div>
                                        <div class="mdc-notched-outline__trailing"></div>
                                    </div>
                                </label>
                            </div>
                           
                            <div class="rtwwwap-search-wrapper">
                                <select class="rtwwwap_search_product ">';
                            
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


            $rtwwwap_html1 .= ' </select><button class="mdc-button  mdc-ripple-upgraded mdc-button--raised" id="rtwwwap-search-icon">
                                    <span class=" fas fa-search"> </span>
                                </button>
                            </div>
                            <p id="rtwwwap_banner_link">                         
                            </p>

                            <button class="mdc-button  mdc-ripple-upgraded mdc-button--raised" id="rtwwwap_copy_banner_link">
                            '.esc_html__( 'Copy Link', 'rtwwwap-wp-wc-affiliate-program' ).'
                          
                            </button>
                            <span id="rtwwwap_copy_link_tooltip">'.esc_html__( 'Copied', 'rtwwwap-wp-wc-affiliate-program' ).'</span>
                            <section class="rtwwwap-search-prdct-section">
                                <div class="rtwwwap-prdct-row">
                                </div>  
                            </section>
                        </div>
                        
                    ';


/// MLM


$rtwwwap_mlm = get_option( 'rtwwwap_mlm_opt' );
if( isset( $rtwwwap_mlm[ 'activate' ] ) && $rtwwwap_mlm[ 'activate' ] == 1 )
{
    $rtwwwap_mlm_childs = isset( $rtwwwap_mlm[ 'child' ] ) ? $rtwwwap_mlm[ 'child' ] : '1';
    global $wpdb;
        $rtwwwap_mlm_type = '';
        if( isset( $rtwwwap_mlm[ 'mlm_type' ] ) )
        {
            if( $rtwwwap_mlm[ 'mlm_type' ] == 0 )
            {
                $rtwwwap_mlm_type = esc_html__( "Binary", "rtwwwap-wp-wc-affiliate-program" );
            }
            if( $rtwwwap_mlm[ 'mlm_type' ] == 1 )
            {
                $rtwwwap_mlm_type = esc_html__( "Forced Matrix", "rtwwwap-wp-wc-affiliate-program" );
            }
            if( $rtwwwap_mlm[ 'mlm_type' ] == 2 )
            {
                $rtwwwap_mlm_type = esc_html__( "Unilevel", "rtwwwap-wp-wc-affiliate-program" );
            }
            if( $rtwwwap_mlm[ 'mlm_type' ] == 3 )
            {
                $rtwwwap_mlm_type = esc_html__( "Unlimited", "rtwwwap-wp-wc-affiliate-program" );
            }
        }
        
                    
$rtwwwap_html1 .= '
                <section class="rtwwwap-mlm-section rtwwwap_hide" id="rtwwwap_mlm_tab" >
                    <div class="rtwwwap-mlm-card-row">
                        <div class="rtwwwap-mlm-left-side">
                            <div class="rtwwwap-mlm-card1 mdc-elevation--z9">
                                <p class="rtwwwap-mlm-card-content">
                                    <span>'.esc_html__( 'MLMP Plan :', 'rtwwwap-wp-wc-affiliate-program' ).'</span>
                                    <span>'.$rtwwwap_mlm_type.'</span>
                                </p>';

                                if( isset( $rtwwwap_mlm[ 'mlm_type' ] ) )
                                {
                                    if( $rtwwwap_mlm[ 'mlm_type' ] == 0 )
                                    {
$rtwwwap_html1 .= '               <div class="rtwwwap-mlm-image">
                                    <img src="'.esc_url( RTWWWAP_URL.'/assets/images/binary.gif' ).'">
                                 </div>';
                                    }
                                    if( $rtwwwap_mlm[ 'mlm_type' ] == 1 )
                                    {
 $rtwwwap_html1 .= '               <div class="rtwwwap-mlm-image">
                                        <img src="'.esc_url( RTWWWAP_URL.'/assets/images/forced.gif' ).'">
                                     </div>';
                                    }
                                    if( $rtwwwap_mlm[ 'mlm_type' ] == 2 )
                                    {
 $rtwwwap_html1 .= '               <div class="rtwwwap-mlm-image">
                                    <img src="'.esc_url( RTWWWAP_URL.'/assets/images/unilevel.gif' ).'">
                                 </div>';
                                    }
                                    if( $rtwwwap_mlm[ 'mlm_type' ] == 3 )
                                    {
 $rtwwwap_html1 .= '               <div class="rtwwwap-mlm-image">
                                    <img src="'.esc_url( RTWWWAP_URL.'/assets/images/unlimited_mlm.jpeg' ).'">
                                 </div>';
                                    }
                                }
$rtwwwap_html1 .= '       
                            </div>
                            <div class="rtwwwap-mlm-card2 mdc-elevation--z9">
                                <p>Number of Childs to start earning commission</p>
                                <div class="rtwwwap-card-number-wrapper">
                                    <div class="rtwwwap-loader"></div>
                                    <div class="rtwwwap-number">
                                        <span>'. $rtwwwap_mlm_childs
                                        .'</span>
                                    </div>
                                
                                </div>
                            </div>
                            <div class="rtwwwap-mlm-btn">
                                <a class="mdc-button mdc-button--raised mdc-theme--primary mdc-ripple-upgraded rtwwwap_payment_submit_button" id="rtwwwap_show_mlm_chain" data-user_id="'.$rtwwwap_user_id.'">
                                    <div class="mdc-button__ripple"></div>
                                    <span class="mdc-button__label" >'.esc_html__( 'Show MLM Chain', 'rtwwwap-wp-wc-affiliate-program' ).'</span>
                                </a>
                            </div>
                                <p class="rtwwwap_mlm_chain_not">'.
                                esc_html__( "MLM chain is not proper, activate/ deactivate the members to make the chain according to your MLM plan. Once done reload the page to see the updated MLM chain.", 'rtwwwap-wp-wc-affiliate-program' ).'
                                </p>
                            <div id="rtwwwap_mlm_chain_struct"></div>
                            <div id="rtwwwap_mlm_show"></div>
                        </div>
                        <div class="rtwwwap-mlm-right-side">
                                    <div class="rtwwwap-text">
                                        <h4>
                                            <span> <i class="fas fa-retweet mr-1" aria-hidden="true"></i> '.esc_html__( 'MLM Table', 'rtwwwap-wp-wc-affiliate-program' ).'</span>
                                        </h4>
                                    </div>
                           
                                
                                <div class="rtwwwap_table_container">
                                    
                                    <table class="mdl-data-table" id="rtwwwap_mlm_table" aria-label="Dessert calories">
                                            <!-- <caption>MLM Levels</caption> -->
                                        <thead>
                                        
                                        <tr>
                                            
                                            <th>'.esc_html__( 'Level', 'rtwwwap-wp-wc-affiliate-program' ).'</th>
                                            <th>'.esc_html__( 'Commission Type', 'rtwwwap-wp-wc-affiliate-program' ).'</th>
                                            <th>'.esc_html__( 'Commission Amount', 'rtwwwap-wp-wc-affiliate-program' ).'</th>
                                        </tr>
                                        </thead>
                                        <tbody>';
                                    
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

                $rtwwwap_html1 .=  '<tr>
                                        
                                            <td>'.$rtwwwap_mlm_key.'</td>
                                            <td>'.$rtwwwap_selected_level.'</td>
                                            <td>'.$rtwwwap_comm_amount.'</td>
                                </tr>';
            }
        }
                                        '</tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>    
            </section>
            ';	
}
$rtwwwap_html1 .=	'</div>';
        return $rtwwwap_html1;
?>