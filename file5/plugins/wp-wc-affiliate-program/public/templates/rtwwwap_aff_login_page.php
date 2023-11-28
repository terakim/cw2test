<?php 

	if ( ! defined( 'ABSPATH' ) ) {
	    exit; // Exit if accessed directly.
	}

	if( !is_user_logged_in() ){
		global $facebook_login_url;
		global $rtwwwap_google_login_url;
		global $rtwwwap_linkedin_url;

		$rtwwwap_extra_features 	= get_option( 'rtwwwap_extra_features_opt' );
		$rtwwwap_signup_bonus_type 	= isset( $rtwwwap_extra_features[ 'signup_bonus_type' ] ) ? $rtwwwap_extra_features[ 'signup_bonus_type' ] : 0;

		$rtwwwap_reg_temp_features = get_option( 'rtwwwap_reg_temp_opt' );
		$rtwwwap_selected_template = isset( $rtwwwap_reg_temp_features[ 'register_page' ] ) ? $rtwwwap_reg_temp_features[ 'register_page' ] : 1;
		$rtwwwap_use_default_color_checked = isset( $rtwwwap_reg_temp_features[ 'temp_colors' ] ) ? $rtwwwap_reg_temp_features[ 'temp_colors' ] : 1;
		$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );
		$rtwwwap_register_msg = isset($rtwwwap_extra_features['succes_register_msg']) && $rtwwwap_extra_features['succes_register_msg'] != "" ? $rtwwwap_extra_features['succes_register_msg'] : "Successfully Register";
		if( $rtwwwap_use_default_color_checked ){
			unset( $rtwwwap_reg_temp_features[ 'mainbg_color' ] );
			unset( $rtwwwap_reg_temp_features[ 'bg_color' ] );
			unset( $rtwwwap_reg_temp_features[ 'head_color' ] );
			unset( $rtwwwap_reg_temp_features[ 'button_color' ] );
		}
		$rtwwwap_affiliate_page_id = get_option('rtwwwap_affiliate_page_id');
		$rtwwwap_redirect_url = get_permalink($rtwwwap_affiliate_page_id);
		
		if( $rtwwwap_selected_template == 1 ){
			$rtwwwap_html = '';
			
			// if(isset($_GET['login_errors']) && !empty($_GET['login_errors']))
			// {	
				$rtwwwap_html .= "<div id='login_error'></div>\n";
			// }
			if(isset($_GET['register']) && $_GET['register'] == true)
			{	
				
				$rtwwwap_html .= '<div id="success">' .$rtwwwap_register_msg . "</div>\n";
			}
			$rtwwwap_bg_color 		= isset( $rtwwwap_reg_temp_features[ 'bg_color' ] ) ? $rtwwwap_reg_temp_features[ 'bg_color' ] : '#EEEEEE';
			$rtwwwap_button_color 	= isset( $rtwwwap_reg_temp_features[ 'button_color' ] ) ? $rtwwwap_reg_temp_features[ 'button_color' ] : '#219595';
			$rtwwwap_form_custom_css= isset( $rtwwwap_reg_temp_features[ 'css' ] ) ? $rtwwwap_reg_temp_features[ 'css' ] : '';
			$rtwwwap_form_title 	= isset( $rtwwwap_extra_features[ 'login_title' ] ) ? $rtwwwap_extra_features[ 'login_title' ] : '';

			$rtwwwap_html .= 	'<style>';
			$rtwwwap_html .= 	'#login_error
								{
									display:none;
									max-width: 550px;
									margin-bottom: 20px;
									border-left: 4px solid #00a0d2;
									border-left-color: #dc3232;
									margin: 0 auto;
									padding: 12px;
									margin-bottom: 20px;
									background-color: #fff;
									box-shadow: 0 4px 38px 0 rgba(22,21,55,.06), 0 0 21px 0 rgba(22,21,55,.03);}';
			$rtwwwap_html .= 	'#success
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
			$rtwwwap_html .= 			'<div id="rtwwwap-register-form">';
			$rtwwwap_html .= 				'<div class="rtwwwap-title">';

			$rtwwwap_html .= 					'<h2>';

			$rtwwwap_html .= '<div class="rtwwwap_main_wallet_wrapper1">
        								<div class="rtwwwap_wallet_model">
        									<div class="rtwwwap_wallet_model_dialog rtwwwap_otp_verify_modal">
        										<div class="rtwwwap_wallet_model_content">
												<div class="rtwwwap_wallet_model_header">
													<h3>'.esc_html__( "Reset your password here", "rtwwwap-wp-wc-affiliate-program" ).'</h3>
													<div class="rtwwwap_close_model_icon">
													<i class="fas fa-times"></i>
												</div>
												</div>
        											<div class="rtwwwap_wallet_model_body">
        												<div class="rtwwwap_amount_text1">
        													<input type="text" class="rtwwwap_email_field" value="" placeholder="enter your email" required>
        													 <button class="rtwwwap_send_otp_btn">'.esc_html__( "Send OTP", "rtwwwap-wp-wc-affiliate-program" ).'</button>
        												</div>
                                                       
                                                        <div class= "rtwwwap_new_pass_field">
                                                            <div class="rtwwwap_amount_text">
            													<label>
            													'.esc_html__( "Please fill the OTP send on your E-mail id", "rtwwwap-wp-wc-affiliate-program" ).'
            													</label>
            													<input type="number" name ="rtwwwap_fill_otp" class="rtwwwap_fill_otp" value="" placeholder="enter the OTP" required>
        												    </div>
        												    <div class="rtwwwap_amount_text">
            													<label>
            													'.esc_html__( "Create New Password", "rtwwwap-wp-wc-affiliate-program" ).'
            													</label>
            													<input type="password" name ="rtwwwap_password" class="rtwwwap_password" value="" placeholder="enter new password" required>
        												    </div>
        												    <div class="rtwwwap_amount_text">
            													<label>
            													'.esc_html__( "Confirm your password", "rtwwwap-wp-wc-affiliate-program" ).'
            													</label>
            													<input type="password" name ="rtwwwap_confirm_password" class="rtwwwap_confirm_password" value="" placeholder="confirm your password" required>
        												    </div>
                                                        </div>
        											</div>
        											<div class="rtwwwap_wallet_model_footer rtwwwap_forget_psw">
        												<button class="rtwwwap_cancel_btn_with">'.esc_html__( "cancel", "rtwwwap-wp-wc-affiliate-program" ).'</button>
														<button class="rtwwwap_save_btn1" id="rtwwwap_save_change_psw" data-wallet_amount=""  data-payment_method="">'.esc_html__( "Save", "rtwwwap-wp-wc-affiliate-program" ).'</button>
        											</div>
        										</div>
        									</div>
        								</div>
        						</div>';



			if( $rtwwwap_form_title != '' ){
				$rtwwwap_html .= 					esc_html( $rtwwwap_form_title );
			}
			else{
				$rtwwwap_html .= 					esc_html__( "Login your Account", "rtwwwap-wp-wc-affiliate-program" );
			}
			$rtwwwap_html .= 					'</h2>
										
			';

			$rtwwwap_html .= 				'</div>';
			$rtwwwap_html .= 					'<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-user"></i></span><input type="text" name="" placeholder="'.esc_attr__( "Username or Email Address", "rtwwwap-wp-wc-affiliate-program" ).'" id="" class="rtwwwap_user_name_email" required></div>';

			$rtwwwap_html .= 					'<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-key"></i></span><input type="password" name="" placeholder="'.esc_attr__( "Password", "rtwwwap-wp-wc-affiliate-program" ).'" id="" class="rtwwwap_user_password" required></div>';

			// $rtwwwap_html .= 					'<div><input type="button" id="" class="rtwwwap_login_button" value="'.esc_attr__( "Login", "rtwwwap-wp-wc-affiliate-program" ).'" id="rtwwwap-Login" /></div>';

			$rtwwwap_html .= 			'<div><input type="button" class="rtwwwap_login_button" value="'.esc_attr__( "Login", "rtwwwap-wp-wc-affiliate-program" ).'" id="rtwwwap-Login"><input type="button" class="rtwwwap_login_button1" value="'.esc_attr__( "forget password", "rtwwwap-wp-wc-affiliate-program" ).'" id="rtwwwap-Login1"></div>';
	
			$rtwwwap_html .= 					'<div class="rtwwwap_social_login_container" >';
			$rtwwwap_html .=					$facebook_login_url;
			$rtwwwap_html .=					$rtwwwap_google_login_url;
			$rtwwwap_html .=  					$rtwwwap_linkedin_url;
			$rtwwwap_html .= 					'</div>';
			
			// $rtwwwap_html .= 				'</form>';
			$rtwwwap_html .= '</p>';
			$rtwwwap_html .= 		'</div>';	
			
		}
		elseif( $rtwwwap_selected_template == 2 ){
		    
			$rtwwwap_html = '';
			$rtwwwap_html .= "<div id='login_error'></div>\n";
			if(isset($_GET['register']) && $_GET['register'] == true)
			{	
				
				$rtwwwap_html .= '<div id="success">' .$rtwwwap_register_msg . "</div>\n";
			}
			$rtwwwap_head_color 	= isset( $rtwwwap_reg_temp_features[ 'head_color' ] ) ? $rtwwwap_reg_temp_features[ 'head_color' ] : '#232055';
			$rtwwwap_button_color 	= isset( $rtwwwap_reg_temp_features[ 'button_color' ] ) ? $rtwwwap_reg_temp_features[ 'button_color' ] : '#232055';
			$rtwwwap_form_custom_css= isset( $rtwwwap_extra_features[ 'css' ] ) ? $rtwwwap_extra_features[ 'css' ] : '';
			$rtwwwap_form_title 	= isset( $rtwwwap_extra_features[ 'login_title' ] ) ? $rtwwwap_extra_features[ 'login_title' ] : '';

			$rtwwwap_html .= 	'<style>';
			$rtwwwap_html .= 	'#login_error
								{
									display:none;
									max-width: 550px;
									margin-bottom: 20px;
									border-left: 4px solid #00a0d2;
									border-left-color: #dc3232;
									margin: 0 auto;
									padding: 12px;
									margin-bottom: 20px;
									background-color: #fff;
									box-shadow: 0 4px 38px 0 rgba(22,21,55,.06), 0 0 21px 0 rgba(22,21,55,.03);}';
			$rtwwwap_html .= 	'#success
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

			$rtwwwap_html .= 	'<div class="rtwwwap-form-wrapper rtwwwap-login-form-wrapper">';
        	$rtwwwap_html .= 	'<div class="rtwwwap-login-inner-form-wrapper">';
        	$rtwwwap_html .= 	'<div class="rtwwwap-login-form-top-side-wrapper">';
        	   			$rtwwwap_html .= 			'<h2>';
			if( $rtwwwap_form_title != '' ){
				$rtwwwap_html .= 			esc_html( $rtwwwap_form_title );
			}
			else{
				$rtwwwap_html .= 			esc_html__( "Login Form", "rtwwwap-wp-wc-affiliate-program" );
			}
			$rtwwwap_html .= 			'</h2>';
        	$rtwwwap_html .= '</div>';
        	$rtwwwap_html .= 	'<div class="rtwwwap-login-form-bottom-side-wrapper">';
        	
        
			$rtwwwap_html .= '<div class="rtwwwap_main_wallet_wrapper1">
        								<div class="rtwwwap_wallet_model">
        									<div class="rtwwwap_wallet_model_dialog rtwwwap_otp_verify_modal">
        										<div class="rtwwwap_wallet_model_content">
												<div class="rtwwwap_wallet_model_header">
													<h3>'.esc_html__( "Reset your password here", "rtwwwap-wp-wc-affiliate-program" ).'</h3>
													<div class="rtwwwap_close_model_icon">
													<i class="fas fa-times"></i>
												</div>
        											<div class="rtwwwap_wallet_model_body">
        												<div class="rtwwwap_amount_text1">
        												
        													<input type="text" class="rtwwwap_email_field" value="" placeholder="enter your email" required>
        													 <button class="rtwwwap_send_otp_btn">'.esc_html__( "Send OTP", "rtwwwap-wp-wc-affiliate-program" ).'</button>
        												</div>
                                                       
                                                        <div class= "rtwwwap_new_pass_field">
                                                            <div class="rtwwwap_amount_text">
            													<label>
            													'.esc_html__( "Please fill the OTP send on your E-mail id", "rtwwwap-wp-wc-affiliate-program" ).'
            													</label>
            													<input type="number" name ="rtwwwap_fill_otp" class="rtwwwap_fill_otp" value="" placeholder="enter the OTP" required>
        												    </div>
        												    <div class="rtwwwap_amount_text">
            													<label>
            													'.esc_html__( "Create New Password", "rtwwwap-wp-wc-affiliate-program" ).'
            													</label>
            													<input type="password" name ="rtwwwap_password" class="rtwwwap_password" value="" placeholder="enter new password" required>
        												    </div>
        												    <div class="rtwwwap_amount_text">
            													<label>
            													'.esc_html__( "Confirm your password", "rtwwwap-wp-wc-affiliate-program" ).'
            													</label>
            													<input type="password" name ="rtwwwap_confirm_password" class="rtwwwap_confirm_password" value="" placeholder="confirm your password" required>
        												    </div>
                                                        </div>
        											</div>
        											<div class="rtwwwap_wallet_model_footer rtwwwap_forget_psw">
        												
        												<button class="rtwwwap_cancel_btn_with">'.esc_html__( "cancel", "rtwwwap-wp-wc-affiliate-program" ).'</button>
														<button class="rtwwwap_save_btn1" id="rtwwwap_save_change_psw" data-wallet_amount=""  data-payment_method="">'.esc_html__( "Save", "rtwwwap-wp-wc-affiliate-program" ).'</button>
        											</div>
        										</div>
        									</div>
        								</div>
        						</div>';
        						
        						
     
			$rtwwwap_html .= 			'<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-user"></i></span><input type="text" name="" placeholder="'.esc_attr__( "Username or Email Address", "rtwwwap-wp-wc-affiliate-program" ).'" class="rtwwwap_user_name_email" required /></div>';
			$rtwwwap_html .= 			'<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-envelope"></i></span><input type="password" name="" placeholder="'.esc_attr__( "Password", "rtwwwap-wp-wc-affiliate-program" ).'"  class="rtwwwap_user_password" required ></div>';
			$rtwwwap_html .= 			'<div><input type="button" class="rtwwwap_login_button" value="'.esc_attr__( "Login", "rtwwwap-wp-wc-affiliate-program" ).'" id="rtwwwap-Login"><input type="button" class="rtwwwap_login_button1" value="'.esc_attr__( "forget password", "rtwwwap-wp-wc-affiliate-program" ).'" id="rtwwwap-Login1"></div></div>';
			$rtwwwap_html .= 	'</div>';
			$rtwwwap_html .= 	'</div>';
		}
		elseif( $rtwwwap_selected_template == 3 ){
			$rtwwwap_html = '';
			$rtwwwap_html .= "<div id='login_error'></div>\n";
			if(isset($_GET['register']) && $_GET['register'] == true)
			{	
			
				$rtwwwap_html .= '<div id="success">' .$rtwwwap_register_msg . "</div>\n";
			}
			$rtwwwap_button_color 	= isset( $rtwwwap_reg_temp_features[ 'button_color' ] ) ? $rtwwwap_reg_temp_features[ 'button_color' ] : '#0150C9';
			$rtwwwap_form_custom_css= isset( $rtwwwap_reg_temp_features[ 'css' ] ) ? $rtwwwap_reg_temp_features[ 'css' ] : '';
			$rtwwwap_form_title 	= isset( $rtwwwap_extra_features[ 'login_title' ] ) ? $rtwwwap_extra_features[ 'login_title' ] : '';

			$rtwwwap_html .= 	'<style>';
			$rtwwwap_html .= 		'.rtwwwap-form-wrapper-2 form input[type="submit"]{';
			$rtwwwap_html .= 			'background-color:'.$rtwwwap_button_color.';';
			$rtwwwap_html .= 		'}';
			$rtwwwap_html .= 	'#login_error
								{
									display:none;
									max-width: 550px;
									margin-bottom: 20px;
									border-left: 4px solid #00a0d2;
									border-left-color: #dc3232;
									margin: 0 auto;
									padding: 12px;
									margin-bottom: 20px;
									background-color: #fff;
									box-shadow: 0 4px 38px 0 rgba(22,21,55,.06), 0 0 21px 0 rgba(22,21,55,.03);}';
			$rtwwwap_html .= 	'#success
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
									if( $rtwwwap_form_custom_css != '' ){
				$rtwwwap_html .= 	$rtwwwap_form_custom_css;
			}
			$rtwwwap_html .= 	'</style>';
		
			$rtwwwap_html .= 	'<div class="rtwwwap-form-wrapper-2">';
			$rtwwwap_html .= 		'<div class="rtwwwap-form-inner">';
			$rtwwwap_html .= 			'<div class="rtwwwap-form-image" style="background-image: url('.RTWWWAP_URL."assets/images/rtw-form-banner.jpg".');">';



			$rtwwwap_html .= '<div class="rtwwwap_main_wallet_wrapper1">
        								<div class="rtwwwap_wallet_model">
        									<div class="rtwwwap_wallet_model_dialog rtwwwap_otp_verify_modal">
        										<div class="rtwwwap_wallet_model_content">
												<div class="rtwwwap_wallet_model_header">
													<h3>'.esc_html__( "Reset your password here", "rtwwwap-wp-wc-affiliate-program" ).'</h3>
													<div class="rtwwwap_close_model_icon">
													<i class="fas fa-times"></i>
												</div>
        											<div class="rtwwwap_wallet_model_body">
        												<div class="rtwwwap_amount_text1">
        												
        													<input type="text" class="rtwwwap_email_field" value="" placeholder="enter your email" required>
        													 <button class="rtwwwap_send_otp_btn">'.esc_html__( "Send OTP", "rtwwwap-wp-wc-affiliate-program" ).'</button>
        												</div>
                                                       
                                                        <div class= "rtwwwap_new_pass_field">
                                                            <div class="rtwwwap_amount_text">
            													<label>
            													'.esc_html__( "Please fill the OTP send on your E-mail id", "rtwwwap-wp-wc-affiliate-program" ).'
            													</label>
            													<input type="number" name ="rtwwwap_fill_otp" class="rtwwwap_fill_otp" value="" placeholder="enter the OTP" required>
        												    </div>
        												    <div class="rtwwwap_amount_text">
            													<label>
            													'.esc_html__( "Create New Password", "rtwwwap-wp-wc-affiliate-program" ).'
            													</label>
            													<input type="password" name ="rtwwwap_password" class="rtwwwap_password" value="" placeholder="enter new password" required>
        												    </div>
        												    <div class="rtwwwap_amount_text">
            													<label>
            													'.esc_html__( "Confirm your password", "rtwwwap-wp-wc-affiliate-program" ).'
            													</label>
            													<input type="password" name ="rtwwwap_confirm_password" class="rtwwwap_confirm_password" value="" placeholder="confirm your password" required>
        												    </div>
                                                        </div>
        											</div>
        											<div class="rtwwwap_wallet_model_footer rtwwwap_forget_psw">
        												
        												<button class="rtwwwap_cancel_btn_with">'.esc_html__( "cancel", "rtwwwap-wp-wc-affiliate-program" ).'</button>
														<button class="rtwwwap_save_btn1" id="rtwwwap_save_change_psw" data-wallet_amount=""  data-payment_method="">'.esc_html__( "Save", "rtwwwap-wp-wc-affiliate-program" ).'</button>
        											</div>
        										</div>
        									</div>
        								</div>
        						</div>';


			
			$rtwwwap_html .= 				'<h2>';
			if( $rtwwwap_form_title != '' ){
				$rtwwwap_html .= 				esc_html( $rtwwwap_form_title );
			}
			else{
				$rtwwwap_html .= 				esc_html__( "Login", "rtwwwap-wp-wc-affiliate-program" );
			}
			$rtwwwap_html .= 				'</h2>';

			$rtwwwap_html .= 			'</div>';
			$rtwwwap_html .= 			'<div class="rtwwwap-form-content">';
			$rtwwwap_html .= 					'<label>'.esc_html__( "Username or Email Address", "rtwwwap-wp-wc-affiliate-program" ).'</label>';
		    $rtwwwap_html .= 					'<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-user"></i></span><input type="text" name="log" placeholder="'.esc_attr__( "Username or Email Address", "rtwwwap-wp-wc-affiliate-program" ).'" class="rtwwwap_user_name_email" required ></div>';
		  	$rtwwwap_html .= 					'<label>'.esc_html__( "Password", "rtwwwap-wp-wc-affiliate-program" ).'</label>';
		    $rtwwwap_html .= 					'<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-envelope"></i></span><input type="password"  class="rtwwwap_user_password" name="pwd" placeholder="'.esc_attr__( "Password", "rtwwwap-wp-wc-affiliate-program" ).'" required ></div>
			';

			// $rtwwwap_html .= 					'<div><input type="button" class="rtwwwap_login_button"  value="'.esc_attr__( "Login", "rtwwwap-wp-wc-affiliate-program" ).'" id="rtwwwap-Login"></div>';


			$rtwwwap_html .= 			'<div><input type="button" class="rtwwwap_login_button" value="'.esc_attr__( "Login", "rtwwwap-wp-wc-affiliate-program" ).'" id="rtwwwap-Login"><input type="button" class="rtwwwap_login_button1" value="'.esc_attr__( "forget password", "rtwwwap-wp-wc-affiliate-program" ).'" id="rtwwwap-Login1"></div>';


			$rtwwwap_html .= 			'</div>';
			$rtwwwap_html .= 		'</div>';
			$rtwwwap_html .= 	'</div>';
		}
		elseif( $rtwwwap_selected_template == 4 ){
			
			$rtwwwap_html = '';
			$rtwwwap_html .= "<div id='login_error'></div>\n";
			if(isset($_GET['register']) && $_GET['register'] == true)
			{	
				
				$rtwwwap_html .= '<div id="success">' .$rtwwwap_register_msg . "</div>\n";
			}

			$rtwwwap_mainbg_color 	= isset( $rtwwwap_reg_temp_features[ 'mainbg_color' ] ) ? $rtwwwap_reg_temp_features[ 'mainbg_color' ] : '#E85A26';
			$rtwwwap_bg_color 		= isset( $rtwwwap_reg_temp_features[ 'bg_color' ] ) ? $rtwwwap_reg_temp_features[ 'bg_color' ] : '#DADAF2';
			$rtwwwap_button_color 	= isset( $rtwwwap_reg_temp_features[ 'button_color' ] ) ? $rtwwwap_reg_temp_features[ 'button_color' ] : '#E85A26';
			$rtwwwap_form_custom_css= isset( $rtwwwap_reg_temp_features[ 'css' ] ) ? $rtwwwap_reg_temp_features[ 'css' ] : '';
			$rtwwwap_form_title 	= isset( $rtwwwap_extra_features[ 'login_title' ] ) ? $rtwwwap_extra_features[ 'login_title' ] : '';

			$rtwwwap_html .= 	'<style>';
			$rtwwwap_html .= 	'#login_error
								{
									display:none;
									max-width: 550px;
									margin-bottom: 20px;
									border-left: 4px solid #00a0d2;
									border-left-color: #dc3232;
									margin: 0 auto;
									padding: 12px;
									margin-bottom: 20px;
									background-color: #fff;
									box-shadow: 0 4px 38px 0 rgba(22,21,55,.06), 0 0 21px 0 rgba(22,21,55,.03);}';
		    $rtwwwap_html .= 	'#success
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
			$rtwwwap_html .= 					'<h2>';

			$rtwwwap_html .= '<div class="rtwwwap_main_wallet_wrapper1">
			<div class="rtwwwap_wallet_model">
				<div class="rtwwwap_wallet_model_dialog rtwwwap_otp_verify_modal">
					<div class="rtwwwap_wallet_model_content">
					<div class="rtwwwap_wallet_model_header">
						<h3>'.esc_html__( "Reset your password here", "rtwwwap-wp-wc-affiliate-program" ).'</h3>
													<div class="rtwwwap_close_model_icon">
													<i class="fas fa-times"></i>
					</div>
						<div class="rtwwwap_wallet_model_body">
							<div class="rtwwwap_amount_text1">
							
								<input type="text" class="rtwwwap_email_field" value="" placeholder="enter your email" required>
								 <button class="rtwwwap_send_otp_btn">'.esc_html__( "Send OTP", "rtwwwap-wp-wc-affiliate-program" ).'</button>
							</div>
						   
							<div class= "rtwwwap_new_pass_field">
								<div class="rtwwwap_amount_text">
									<label>
									'.esc_html__( "Please fill the OTP send on your E-mail id", "rtwwwap-wp-wc-affiliate-program" ).'
									</label>
									<input type="number" name ="rtwwwap_fill_otp" class="rtwwwap_fill_otp" value="" placeholder="enter the OTP" required>
								</div>
								<div class="rtwwwap_amount_text">
									<label>
									'.esc_html__( "Create New Password", "rtwwwap-wp-wc-affiliate-program" ).'
									</label>
									<input type="password" name ="rtwwwap_password" class="rtwwwap_password" value="" placeholder="enter new password" required>
								</div>
								<div class="rtwwwap_amount_text">
									<label>
									'.esc_html__( "Confirm your password", "rtwwwap-wp-wc-affiliate-program" ).'
									</label>
									<input type="password" name ="rtwwwap_confirm_password" class="rtwwwap_confirm_password" value="" placeholder="confirm your password" required>
								</div>
							</div>
						</div>
						<div class="rtwwwap_wallet_model_footer rtwwwap_forget_psw">
							<button class="rtwwwap_cancel_btn_with">'.esc_html__( "cancel", "rtwwwap-wp-wc-affiliate-program" ).'</button>
							<button class="rtwwwap_save_btn1" id="rtwwwap_save_change_psw" data-wallet_amount=""  data-payment_method="">'.esc_html__( "Save", "rtwwwap-wp-wc-affiliate-program" ).'</button>
						</div>
					</div>
				</div>
			</div>
			</div>';


			if( $rtwwwap_form_title != '' ){
				$rtwwwap_html .= 					esc_html( $rtwwwap_form_title );
			}
			else{
				$rtwwwap_html .= 					esc_html__( "Login", "rtwwwap-wp-wc-affiliate-program" );
			}
			$rtwwwap_html .= 					'</h2>	';

			$rtwwwap_html .= 					'<label>'.esc_html__( "Username or Email Address", "rtwwwap-wp-wc-affiliate-program" ).'</label>';
		    $rtwwwap_html .= 					'<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-user"></i></span><input type="text" name="log" placeholder="'.esc_attr__( "Username or Email Address", "rtwwwap-wp-wc-affiliate-program" ).'" class="rtwwwap_user_name_email" required ></div>';
		  	$rtwwwap_html .= 					'<label>'.esc_html__( "Password", "rtwwwap-wp-wc-affiliate-program" ).'</label>';
		    $rtwwwap_html .= 					'<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-envelope"></i></span><input type="password" name="pwd"  class="rtwwwap_user_password" placeholder="'.esc_attr__( "Password", "rtwwwap-wp-wc-affiliate-program" ).'" required></div>';

			// $rtwwwap_html .= 					'<div><input type="button" class="rtwwwap_login_button" value="'.esc_attr__( "Login", "rtwwwap-wp-wc-affiliate-program" ).'" id="rtwwwap-Login"></div>';


			$rtwwwap_html .= 			'<div><input type="button" class="rtwwwap_login_button" value="'.esc_attr__( "Login", "rtwwwap-wp-wc-affiliate-program" ).'" id="rtwwwap-Login"><input type="button" class="rtwwwap_login_button1" value="'.esc_attr__( "forget password", "rtwwwap-wp-wc-affiliate-program" ).'" id="rtwwwap-Login1"></div>';


			$rtwwwap_html .= 			'</div>';
			$rtwwwap_html .= 		'</div>';
			$rtwwwap_html .= 	'</div>';
		}
		return $rtwwwap_html;
	}
	else{
		$rtwwwap_html = do_shortcode( '[rtwwwap_affiliate_page]' );
		return $rtwwwap_html;
	}

?>
