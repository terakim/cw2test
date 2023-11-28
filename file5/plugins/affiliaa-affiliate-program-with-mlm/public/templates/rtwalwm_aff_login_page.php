<?php 
	if ( ! defined( 'ABSPATH' ) ) {
	    exit; // Exit if accessed directly.
	}

	if( !is_user_logged_in() ){
	
		$rtwalwm_affiliate_page_id = get_option('rtwwwap_affiliate_page_id');
		$rtwalwm_redirect_url = get_permalink($rtwalwm_affiliate_page_id);
		
	
			$rtwalwm_html = '';
			
			if(isset($_GET['login_errors']) && !empty($_GET['login_errors']))
			{	
				$rtwalwm_html .= '<div id="login_error">' . apply_filters( 'login_errors', $_GET['login_errors'] ) . "</div>\n";
			}
		
			$rtwalwm_html .= 	'<style>';
			$rtwalwm_html .= 	'#login_error
								{
									max-width: 550px;
									margin-bottom: 20px;
									border-left: 4px solid #00a0d2;
									border-left-color: #dc3232;
									margin: 0 auto;
									padding: 12px;
									margin-bottom: 20px;
									background-color: #fff;
									box-shadow: 0 4px 38px 0 rgba(22,21,55,.06), 0 0 21px 0 rgba(22,21,55,.03);}';
			$rtwalwm_html .= 	'#success
									{
										max-width: 550px;
										margin-bottom: 20px;
										border-left: 4px solid #46B450;
										border-left-color: #46B450;
										margin: 0 auto;
										padding: 12px;
										margin-bottom: 20px;
										background-color: #fff;
										box-shadow: 0 4px 38px 0 rgba(22,21,55,.06), 0 0 21px 0 rgba(22,21,55,.03);}';						
			$rtwalwm_html .= 		'#rtwalwm-register-form{';
			$rtwalwm_html .= 			'border-color:#EEEEEE;';
			$rtwalwm_html .= 		'}';
			$rtwalwm_html .= 		'#rtwalwm-register-form form input[type="submit"]{';
			$rtwalwm_html .= 			'background-color:#219595;';
			$rtwalwm_html .= 		'}';
			$rtwalwm_html .= 	'</style>';
			$rtwalwm_html .= 			'<div id="rtwalwm-register-form">';
			$rtwalwm_html .= 				'<div class="rtwalwm-title">';

			$rtwalwm_html .= 					'<h2>';
		
			
			$rtwalwm_html .= 					esc_html__( "Login your Account", "rtwalwm-wp-wc-affiliate-program" );
		
			$rtwalwm_html .= 					'</h2>';

			$rtwalwm_html .= 				'</div>';
			$rtwalwm_html .= 				'<form action="'.esc_url( site_url("wp-login.php", "login_post") ).'" method="post">';
			$rtwalwm_html .= 					'<div class="rtwalwm-text"><span class="rtwalwm-text-icon"><i class="fas fa-user"></i></span><input type="text" name="log" placeholder="'.esc_attr__( "Username or Email Address", "rtwalwm-wp-wc-affiliate-program" ).'" id="log" class="input" required /></div>';

			$rtwalwm_html .= 					'<div class="rtwalwm-text"><span class="rtwalwm-text-icon"><i class="fas fa-envelope"></i></span><input type="password" name="pwd" placeholder="'.esc_attr__( "Password", "rtwalwm-wp-wc-affiliate-program" ).'" id="pwd" class="input" required /></div>';
			$rtwalwm_html .= 					'<div><input type="submit" id="one" value="'.esc_attr__( "Login", "rtwalwm-wp-wc-affiliate-program" ).'" id="rtwalwm-Login" /></div>';
			$rtwalwm_html .=                	'<input type="hidden" value="'.esc_attr(remove_query_arg(array("login_errors","failed"),$rtwalwm_redirect_url)).'" name="redirect_to">';
			$rtwalwm_html .=                	'<input type="hidden" name="user-cookie" value="1" />';
			$rtwalwm_html .= 				'</form>';
			$rtwalwm_html .= '</p>';
			$rtwalwm_html .= 		'</div>';
			
		
		return $rtwalwm_html;
	}
	else{
		$rtwalwm_html = do_shortcode( '[rtwwwap_affiliate_page]' );
		return $rtwalwm_html;
	}
	





?>
