<?php 
	if ( ! defined( 'ABSPATH' ) ) {
	    exit; // Exit if accessed directly.
	}

	if( !is_user_logged_in() )
	{
		$rtwwwap_html = '';
		$rtwwwap_reg_temp_features = get_option( 'rtwwwap_reg_temp_opt' );
		$rtwwwap_selected_template = isset( $rtwwwap_reg_temp_features[ 'register_page' ] ) ? $rtwwwap_reg_temp_features[ 'register_page' ] : 1;
		$rtwwwap_use_default_color_checked = isset( $rtwwwap_reg_temp_features[ 'temp_colors' ] ) ? $rtwwwap_reg_temp_features[ 'temp_colors' ] : 1;

		if( $rtwwwap_use_default_color_checked ){
			unset( $rtwwwap_reg_temp_features[ 'mainbg_color' ] );
			unset( $rtwwwap_reg_temp_features[ 'bg_color' ] );
			unset( $rtwwwap_reg_temp_features[ 'head_color' ] );
			unset( $rtwwwap_reg_temp_features[ 'button_color' ] );
		}
	
				
			$action = isset( $_GET['action'] ) ? $_GET['action'] : '';
	    	$rp_key = isset( $_GET['rp_key'] ) ? $_GET['rp_key'] : '';
		    $rp_login = isset( $_GET['rp_login'] ) ? $_GET['rp_login'] : '';
            $user = check_password_reset_key( $rp_key, $rp_login );
        
			if(!$user || is_wp_error( $user ) )
			{
				$rtwwwap_html .= 	'<style>';
				$rtwwwap_html .= 	'.key_error
									{
										max-width: 350px;
										margin-bottom: 20px;
										border-left: 4px solid #00a0d2;
										border-left-color: #dc3232;
										margin: 0 auto;
										padding: 12px;
										margin-bottom: 20px;
										background-color: #fff;
										font-size: xx-large;
										box-shadow: 0 10px 38px 0 rgba(22,21,55,.06), 0 0 21px 0 rgba(22,21,55,.03);}';
				$rtwwwap_html .= 	'</style>';	
				if ( $user && $user->get_error_code() === 'expired_key' ) {
					$rtwwwap_html .= '<div class="key_error">' . esc_html__( "Key Expired", "rtwwwap-wp-wc-affiliate-program" ) . "</div>\n";
					return $rtwwwap_html;
				}
				else {
					$rtwwwap_html .= '<div class="key_error">'. esc_html__( "Invalid Key", "rtwwwap-wp-wc-affiliate-program" ) .'</div>';
					return $rtwwwap_html;
				}
				exit;
			}
			else{
				
				if( $rtwwwap_selected_template == 1 ){
				$rtwwwap_html = '';
				$rtwwwap_bg_color 		= isset( $rtwwwap_reg_temp_features[ 'bg_color' ] ) ? $rtwwwap_reg_temp_features[ 'bg_color' ] : '#EEEEEE';
				$rtwwwap_button_color 	= isset( $rtwwwap_reg_temp_features[ 'button_color' ] ) ? $rtwwwap_reg_temp_features[ 'button_color' ] : '#219595';
				$rtwwwap_form_custom_css= isset( $rtwwwap_reg_temp_features[ 'css' ] ) ? $rtwwwap_reg_temp_features[ 'css' ] : '';
				$rtwwwap_form_title 	= isset( $rtwwwap_reg_temp_features[ 'title' ] ) ? $rtwwwap_reg_temp_features[ 'title' ] : '';

					$rtwwwap_html .= 	'<style>';
					$rtwwwap_html .= 		'#rtwwwap-register-form{';
					$rtwwwap_html .= 			'border-color:'.$rtwwwap_bg_color.';';
					$rtwwwap_html .= 		'}';
					$rtwwwap_html .= 		'#rtwwwap-register-form form input[type="submit"]{';
					$rtwwwap_html .= 			'background-color:'.$rtwwwap_button_color.';';
					$rtwwwap_html .= 		'}';
					if( $rtwwwap_form_custom_css != '' ){
						$rtwwwap_html .= 	$rtwwwap_form_custom_css;
					}
					$rtwwwap_html .= 	'</style>';
					$rtwwwap_html .= 	'<div id="rtwwwap-register-form">';
					$rtwwwap_html .= 				'<div class="rtwwwap-title">';
					$rtwwwap_html .= 					'<h2>';
					$rtwwwap_html .= 					esc_html__( "Reset your Password", "rtwwwap-wp-wc-affiliate-program" );
					$rtwwwap_html .= 					'</h2>';
					$rtwwwap_html .= 				'</div>';
					$rtwwwap_html .= 	' <form name="resetpassform" id="resetpassform" action="'.esc_url(site_url( 'wp-login.php?action=resetpass' )).'" method="post" autocomplete="off">';
					$rtwwwap_html .= 	'<input type="hidden" id="user_login"  name="rp_login" value="'.esc_attr( $rp_login ).'" autocomplete="off" />';
					$rtwwwap_html .=   ' <input type="hidden" name="rp_key" value="'.esc_attr( $rp_key).'" />';
		
					$rtwwwap_html .= '<div class="wp-pwd">
											<div class="rtwwwap-text">
												<span class="rtwwwap-text-icon">
													<i class="fa fa-key"></i>
												</span> 
												<input type="password" data-reveal="1" data-pw="'.esc_attr( wp_generate_password( 16 ) ).'" name="pass1" id="pass1" class="input password-input" size="24" value="" autocomplete="off" aria-describedby="pass-strength-result"  placeholder="'.esc_attr__( "New Password", "rtwwwap-wp-wc-affiliate-program" ).'"  /> 
											</div>

											<div id="pass-strength-result" class="hide-if-no-js" aria-live="polite">'.esc_html__( "Reset your Password", "rtwwwap-wp-wc-affiliate-program" ).'</div>
									</div>';
					$rtwwwap_html .= '<p class="description indicator-hint">"'.wp_get_password_hint().'"</p>';
					
					$rtwwwap_html .= '<p class="resetpass-submit">';
					$rtwwwap_html .=	'<input type="submit" name="wp-submit" id="wp-submit"
												class="button " value="'. esc_html__( 'Reset Password' ,  "rtwwwap-wp-wc-affiliate-program").'" />';
					$rtwwwap_html .= '</p>';
					$rtwwwap_html .= '</form>';


			
					$rtwwwap_html .= 		'</div>';
					
				}
				elseif( $rtwwwap_selected_template == 2 ){
					$rtwwwap_html = '';
					$rtwwwap_head_color 	= isset( $rtwwwap_reg_temp_features[ 'head_color' ] ) ? $rtwwwap_reg_temp_features[ 'head_color' ] : '#232055';
					$rtwwwap_button_color 	= isset( $rtwwwap_reg_temp_features[ 'button_color' ] ) ? $rtwwwap_reg_temp_features[ 'button_color' ] : '#232055';
					$rtwwwap_form_custom_css= isset( $rtwwwap_reg_temp_features[ 'css' ] ) ? $rtwwwap_reg_temp_features[ 'css' ] : '';
					$rtwwwap_form_title 	= isset( $rtwwwap_reg_temp_features[ 'title' ] ) ? $rtwwwap_reg_temp_features[ 'title' ] : '';
		
					$rtwwwap_html .= 	'<style>';						
					$rtwwwap_html .= 		'.rtwwwap-form-wrapper form h2{';
					$rtwwwap_html .= 			'background-color:'.$rtwwwap_head_color.';';
					$rtwwwap_html .= 		'}';
					$rtwwwap_html .= 		'.rtwwwap-form-wrapper form input[type="submit"]{';
					$rtwwwap_html .= 			'background-color:'.$rtwwwap_button_color.';';
					$rtwwwap_html .= 		'}';
					if( $rtwwwap_form_custom_css != '' ){
						$rtwwwap_html .= 	$rtwwwap_form_custom_css;
					}
					$rtwwwap_html .= 	'</style>';
		
					$rtwwwap_html .= 	'<div class="rtwwwap-form-wrapper">';
					$rtwwwap_html .= 	' <form name="resetpassform" id="resetpassform" action="'.esc_url(site_url( 'wp-login.php?action=resetpass' )).'" method="post" autocomplete="off">';

		
					$rtwwwap_html .= 			'<h2>';
					$rtwwwap_html .= 					esc_html__( "Reset your Password", "rtwwwap-wp-wc-affiliate-program" );
					$rtwwwap_html .= 			'</h2>';
					$rtwwwap_html .= 	'<input type="hidden" id="user_login"  name="rp_login" value="'.esc_attr( $rp_login ).'" autocomplete="off" />';
					$rtwwwap_html .=   ' <input type="hidden" name="rp_key" value="'.esc_attr( $rp_key).'" />';
		
					$rtwwwap_html .= '<div class="wp-pwd">
											<div class="rtwwwap-text">
												<span class="rtwwwap-text-icon">
													<i class="fa fa-key"></i>
												</span> 
												<input type="password" data-reveal="1" data-pw="'.esc_attr( wp_generate_password( 16 ) ).'" name="pass1" id="pass1" class="input password-input" size="24" value="" autocomplete="off" aria-describedby="pass-strength-result"  placeholder="'.esc_attr__( "New Password", "rtwwwap-wp-wc-affiliate-program" ).'"  /> 
											</div>

											<div id="pass-strength-result" class="hide-if-no-js" aria-live="polite">'.esc_html__( "Reset your Password", "rtwwwap-wp-wc-affiliate-program" ).'</div>
									</div>';
					$rtwwwap_html .= '<p class="description indicator-hint">"'.wp_get_password_hint().'"</p>';
					
					$rtwwwap_html .= '<p class="resetpass-submit">';
					$rtwwwap_html .=	'<input type="submit" name="wp-submit" id="wp-submit"
												class="button " value="'. esc_html__( 'Reset Password' ,  "rtwwwap-wp-wc-affiliate-program").'" />';
					$rtwwwap_html .= '</p>';
					$rtwwwap_html .= '</form>';


					$rtwwwap_html .= 	'</div>';
				}
				elseif( $rtwwwap_selected_template == 3 ){
					$rtwwwap_html = '';
					$rtwwwap_button_color 	= isset( $rtwwwap_reg_temp_features[ 'button_color' ] ) ? $rtwwwap_reg_temp_features[ 'button_color' ] : '#0150C9';
					$rtwwwap_form_custom_css= isset( $rtwwwap_reg_temp_features[ 'css' ] ) ? $rtwwwap_reg_temp_features[ 'css' ] : '';
					$rtwwwap_form_title 	= isset( $rtwwwap_reg_temp_features[ 'title' ] ) ? $rtwwwap_reg_temp_features[ 'title' ] : '';
		
					$rtwwwap_html .= 	'<style>';
					$rtwwwap_html .= 		'.rtwwwap-form-wrapper-2 form input[type="submit"]{';
					$rtwwwap_html .= 			'background-color:'.$rtwwwap_button_color.';';
					$rtwwwap_html .= 		'}';			
					if( $rtwwwap_form_custom_css != '' ){
						$rtwwwap_html .= 	$rtwwwap_form_custom_css;
					}
					$rtwwwap_html .= 	'</style>';
				
					$rtwwwap_html .= 	'<div class="rtwwwap-form-wrapper-2">';
					$rtwwwap_html .= 		'<div class="rtwwwap-form-inner">';
					$rtwwwap_html .= 			'<div class="rtwwwap-form-image" style="background-image: url('.RTWWWAP_URL."assets/images/rtw-form-banner.jpg".');">';
					
					$rtwwwap_html .= 				'<h2>';
					$rtwwwap_html .= 					esc_html__( "Reset your Password", "rtwwwap-wp-wc-affiliate-program" );
		
					$rtwwwap_html .= 				'</h2>';
		
					$rtwwwap_html .= 			'</div>';
					$rtwwwap_html .= 			'<div class="rtwwwap-form-content">';
					$rtwwwap_html .= 	'<form name="resetpassform" id="resetpassform" action="'.esc_url(site_url( 'wp-login.php?action=resetpass' )).'" method="post" autocomplete="off">';
					$rtwwwap_html .= 	'<input type="hidden" id="user_login"  name="rp_login" value="'.esc_attr( $rp_login ).'" autocomplete="off" />';
					$rtwwwap_html .=   ' <input type="hidden" name="rp_key" value="'.esc_attr( $rp_key).'" />';
		
					$rtwwwap_html .= '<div class="wp-pwd">
											<div class="rtwwwap-text">
												<span class="rtwwwap-text-icon">
													<i class="fa fa-key"></i>
												</span> 
												<input type="password" data-reveal="1" data-pw="'.esc_attr( wp_generate_password( 16 ) ).'" name="pass1" id="pass1" class="input password-input" size="24" value="" autocomplete="off" aria-describedby="pass-strength-result"  placeholder="'.esc_attr__( "New Password", "rtwwwap-wp-wc-affiliate-program" ).'"  /> 
											</div>

											<div id="pass-strength-result" class="hide-if-no-js" aria-live="polite">'.esc_html__( "Reset your Password", "rtwwwap-wp-wc-affiliate-program" ).'</div>
									</div>';
					$rtwwwap_html .= '<p class="description indicator-hint">"'.wp_get_password_hint().'"</p>';
					
					$rtwwwap_html .= '<p class="resetpass-submit">';
					$rtwwwap_html .=	'<input type="submit" name="wp-submit" id="wp-submit"
												class="button " value="'. esc_html__( 'Reset Password' ,  "rtwwwap-wp-wc-affiliate-program").'" />';
					$rtwwwap_html .= '</p>';
					$rtwwwap_html .= '</form>';
					$rtwwwap_html .= 			'</div>';
					$rtwwwap_html .= 		'</div>';
					$rtwwwap_html .= 	'</div>';
				}
				elseif( $rtwwwap_selected_template == 4 ){
					$rtwwwap_html = '';		
					$rtwwwap_mainbg_color 	= isset( $rtwwwap_reg_temp_features[ 'mainbg_color' ] ) ? $rtwwwap_reg_temp_features[ 'mainbg_color' ] : '#E85A26';
					$rtwwwap_bg_color 		= isset( $rtwwwap_reg_temp_features[ 'bg_color' ] ) ? $rtwwwap_reg_temp_features[ 'bg_color' ] : '#DADAF2';
					$rtwwwap_button_color 	= isset( $rtwwwap_reg_temp_features[ 'button_color' ] ) ? $rtwwwap_reg_temp_features[ 'button_color' ] : '#E85A26';
					$rtwwwap_form_custom_css= isset( $rtwwwap_reg_temp_features[ 'css' ] ) ? $rtwwwap_reg_temp_features[ 'css' ] : '';
					$rtwwwap_form_title 	= isset( $rtwwwap_reg_temp_features[ 'title' ] ) ? $rtwwwap_reg_temp_features[ 'title' ] : '';
		
					$rtwwwap_html .= 	'<style>';
					$rtwwwap_html .= 		'.rtwwwap-form-wrapper-3{';
					$rtwwwap_html .= 			'background-color:'.$rtwwwap_mainbg_color.';';
					$rtwwwap_html .= 		'}';
					$rtwwwap_html .= 		'.rtwwwap-form-wrapper-3 .rtwwwap-form-content{';
					$rtwwwap_html .= 			'background-color:'.$rtwwwap_bg_color.';';
					$rtwwwap_html .= 		'}';
					$rtwwwap_html .= 		'.rtwwwap-form-wrapper-3 form input[type="submit"]{';
					$rtwwwap_html .= 			'background-color:'.$rtwwwap_button_color.';';
					$rtwwwap_html .= 		'}';
					if( $rtwwwap_form_custom_css != '' ){
						$rtwwwap_html .= 	$rtwwwap_form_custom_css;
					}
					$rtwwwap_html .= 	'</style>';
		
					$rtwwwap_html .= 	'<div class="rtwwwap-form-wrapper-3">';
					$rtwwwap_html .= 		'<div class="rtwwwap-form-inner">';
					$rtwwwap_html .= 			'<div class="rtwwwap-form-content">';
					$rtwwwap_html .= 	' <form name="resetpassform" id="resetpassform" action="'.esc_url(site_url( 'wp-login.php?action=resetpass' )).'" method="post" autocomplete="off">';

		
					$rtwwwap_html .= 					'<h2>';
					
					$rtwwwap_html .= 					esc_html__( "Reset your Password", "rtwwwap-wp-wc-affiliate-program" );
					
					$rtwwwap_html .= 					'</h2>';
		
					$rtwwwap_html .= 	'<input type="hidden" id="user_login"  name="rp_login" value="'.esc_attr( $rp_login ).'" autocomplete="off" />';
					$rtwwwap_html .=   ' <input type="hidden" name="rp_key" value="'.esc_attr( $rp_key).'" />';
		
					$rtwwwap_html .= '<div class="wp-pwd">
											<div class="rtwwwap-text">
												<span class="rtwwwap-text-icon">
													<i class="fa fa-key"></i>
												</span> 
												<input type="password" data-reveal="1" data-pw="'.esc_attr( wp_generate_password( 16 ) ).'" name="pass1" id="pass1" class="input password-input" size="24" value="" autocomplete="off" aria-describedby="pass-strength-result"  placeholder="'.esc_attr__( "New Password", "rtwwwap-wp-wc-affiliate-program" ).'"  /> 
											</div>

											<div id="pass-strength-result" class="hide-if-no-js" aria-live="polite">'.esc_html__( "Reset your Password", "rtwwwap-wp-wc-affiliate-program" ).'</div>
									</div>';
					$rtwwwap_html .= '<p class="description indicator-hint">"'.wp_get_password_hint().'"</p>';
					
					$rtwwwap_html .= '<p class="resetpass-submit">';
					$rtwwwap_html .=	'<input type="submit" name="wp-submit" id="wp-submit"
												class="button " value="'. esc_html__( 'Reset Password' ,  "rtwwwap-wp-wc-affiliate-program").'" />';
					$rtwwwap_html .= '</p>';
					$rtwwwap_html .= '</form>';
					
					$rtwwwap_html .= 			'</div>';
					$rtwwwap_html .= 		'</div>';
					$rtwwwap_html .= 	'</div>';
				}	
			return $rtwwwap_html;
		}
	}
	else{
		$rtwwwap_html = do_shortcode( '[rtwwwap_affiliate_page]' );
		return $rtwwwap_html;
	}
?>
