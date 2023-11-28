<?php 
	if ( ! defined( 'ABSPATH' ) ) {
	    exit; // Exit if accessed directly.
	}


	

	if( !is_user_logged_in() ){

		/**
 *
 * Custom fields of register form.
 *
 */
if (!function_exists('rtwwwap_custom_form_fields_template_one'))
{
function rtwwwap_custom_form_fields_template_one(){
	$rtwwwap_reg_temp_features = get_option( 'rtwwwap_reg_temp_opt' );
	$rtwwwap_reg_custom_fields = isset($rtwwwap_reg_temp_features['custom-input']) ? $rtwwwap_reg_temp_features['custom-input'] : array();
	$rtwwwap_html = '';
	if(is_array($rtwwwap_reg_custom_fields) && !empty($rtwwwap_reg_custom_fields)){
		foreach ($rtwwwap_reg_custom_fields as $custom_fields) {
			if(isset($custom_fields['custom-input-type'])){
				if(($custom_fields['custom-input-type'] == 'text' || $custom_fields['custom-input-type'] == 'number')){

					$rtwwwap_html .= 	'<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-edit"></i></span><input type="'.$custom_fields['custom-input-type'].'" name="'.$custom_fields['custom-input-id'].'"  id="'.$custom_fields['custom-input-id'].'" class="'.$custom_fields['custom-input-class'].' rtwwwap_extra_field" placeholder="'.esc_attr__( $custom_fields['custom-input-label'], "rtwwwap-wp-wc-affiliate-program" ).'"" /></div>';
				}elseif ($custom_fields['custom-input-type'] == 'textarea') {
					$rtwwwap_html .=	'<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-edit"></i></span><textarea name="'.$custom_fields['custom-input-id'].'"  id="'.$custom_fields['custom-input-id'].'" class="'.$custom_fields['custom-input-class'].' rtwwwap_extra_field" placeholder="'.esc_attr__( $custom_fields['custom-input-label'], "rtwwwap-wp-wc-affiliate-program" ).'"></textarea></div>';
				}elseif ($custom_fields['custom-input-type'] == 'checkbox') {
					$rtwwwap_html .= '<div class="rtwwwap-custom-checkbox">';
					$rtwwwap_checkbox_options = explode('|',$custom_fields['custom-input-options']);
					if(is_array($rtwwwap_checkbox_options) && !empty($rtwwwap_checkbox_options)){
						$rtwwwap_html .=  '<span>'.$custom_fields['custom-input-label'].'</span>';
						foreach ($rtwwwap_checkbox_options as $value) {
							
							$rtwwwap_html .= 	'<label for="'.$custom_fields['custom-input-id'].'"><input type="'.$custom_fields['custom-input-type'].'" name="'.$value.'"  id="'.$custom_fields['custom-input-id'].'" class="'.$custom_fields['custom-input-class'].' rtwwwap_extra_field" placeholder="'.esc_attr__( $custom_fields['custom-input-label'], "rtwwwap-wp-wc-affiliate-program" ).'"" value="'.esc_attr__(trim($value),"rtwwwap-wp-wc-affiliate-program").'" />'.esc_html__($value,"rtwwwap-wp-wc-affiliate-program").'</label>';
							
						}
						$rtwwwap_html .= '</div>';
					}
				}elseif ($custom_fields['custom-input-type'] == 'radio') {
					$rtwwwap_checkbox_options = explode('|',$custom_fields['custom-input-options']);
					if(is_array($rtwwwap_checkbox_options) && !empty($rtwwwap_checkbox_options)){
						$rtwwwap_html .='<div class="rtwwwap-custom-radio">';
						$rtwwwap_html .=  '<span>'.$custom_fields['custom-input-label'].'</span>';
						foreach ($rtwwwap_checkbox_options as $value) {
							$rtwwwap_html .= 	' <label for="'.$custom_fields['custom-input-id'].'"><input type="'.$custom_fields['custom-input-type'].'" name="'.$custom_fields['custom-input-id'].'"  id="'.$custom_fields['custom-input-id'].'" class="'.$custom_fields['custom-input-class'].' rtwwwap_extra_field" placeholder="'.esc_attr__( $custom_fields['custom-input-label'], "rtwwwap-wp-wc-affiliate-program" ).'"" value="'.esc_attr__(trim($value),"rtwwwap-wp-wc-affiliate-program").'"/>'.esc_html__($value,"rtwwwap-wp-wc-affiliate-program").'</label>';
							
							
						}
						$rtwwwap_html .='</div>';
					}
				}
				elseif ($custom_fields['custom-input-type'] == 'select') {
					$rtwwwap_checkbox_options = explode('|',$custom_fields['custom-input-options']);
					if(is_array($rtwwwap_checkbox_options) && !empty($rtwwwap_checkbox_options)){
						$rtwwwap_html .= 	'<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fas fa-user-edit"></i></span><select name="'.$custom_fields['custom-input-id'].'"  id="'.$custom_fields['custom-input-id'].'" class="'.$custom_fields['custom-input-class'].' rtwwwap_extra_field" >';
						$rtwwwap_html .= 	'<option >'.esc_html__(trim($custom_fields['custom-input-label']),"rtwwwap-wp-wc-affiliate-program").'</option>';
						
						foreach ($rtwwwap_checkbox_options as $options_value) {
							$rtwwwap_html .= 	'<option value="'.esc_attr__(trim($options_value ),"rtwwwap-wp-wc-affiliate-program").'" >'.esc_html__(trim($options_value),"rtwwwap-wp-wc-affiliate-program").'</option>';
							
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

/**
 *
 * Custom fields of register form.
 *
 */
if (!function_exists('rtwwwap_custom_form_fields_template_three'))
{
function rtwwwap_custom_form_fields_template_three(){
	$rtwwwap_reg_temp_features = get_option( 'rtwwwap_reg_temp_opt' );
	$rtwwwap_reg_custom_fields = isset($rtwwwap_reg_temp_features['custom-input']) ? $rtwwwap_reg_temp_features['custom-input'] : array();
	$rtwwwap_html = '';
	if(is_array($rtwwwap_reg_custom_fields) && !empty($rtwwwap_reg_custom_fields)){
		foreach ($rtwwwap_reg_custom_fields as $custom_fields) {
			if(isset($custom_fields['custom-input-type'])){
				if(($custom_fields['custom-input-type'] == 'text' || $custom_fields['custom-input-type'] == 'number')){
					$rtwwwap_html .= 					'<label>'.esc_html__( $custom_fields['custom-input-label'], "rtwwwap-wp-wc-affiliate-program" ).'</label>';
					$rtwwwap_html .= 	'<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-edit"></i></span><input type="'.$custom_fields['custom-input-type'].'" name="'.$custom_fields['custom-input-id'].'"  id="'.$custom_fields['custom-input-id'].'" class="'.$custom_fields['custom-input-class'].' rtwwwap_extra_field" placeholder="'.esc_attr__( $custom_fields['custom-input-label'], "rtwwwap-wp-wc-affiliate-program" ).'"" /></div>';
				}elseif ($custom_fields['custom-input-type'] == 'textarea') {
					$rtwwwap_html .= 					'<label>'.esc_html__( $custom_fields['custom-input-label'], "rtwwwap-wp-wc-affiliate-program" ).'</label>';
					$rtwwwap_html .=	'<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-envelope"></i></span><textarea name="'.$custom_fields['custom-input-id'].'"  id="'.$custom_fields['custom-input-id'].'" class="'.$custom_fields['custom-input-class'].' rtwwwap_extra_field" placeholder="'.esc_attr__( $custom_fields['custom-input-label'], "rtwwwap-wp-wc-affiliate-program" ).'"></textarea></div>';
				}elseif ($custom_fields['custom-input-type'] == 'checkbox') {
					$rtwwwap_html .= '<div class="rtwwwap-custom-checkbox">';
					$rtwwwap_html .= 					'<span>'.esc_html__( $custom_fields['custom-input-label'], "rtwwwap-wp-wc-affiliate-program" ).'</span>';
					$rtwwwap_checkbox_options = explode('|',$custom_fields['custom-input-options']);
					if(is_array($rtwwwap_checkbox_options) && !empty($rtwwwap_checkbox_options)){
						foreach ($rtwwwap_checkbox_options as $value) {
							$rtwwwap_html .= 	'<label><input type="'.$custom_fields['custom-input-type'].'" name="'.$value.'"  id="'.$custom_fields['custom-input-id'].'" class="'.$custom_fields['custom-input-class'].' rtwwwap_extra_field" placeholder="'.esc_attr__( $custom_fields['custom-input-label'], "rtwwwap-wp-wc-affiliate-program" ).'"" value="'.esc_attr__(trim($value),"rtwwwap-wp-wc-affiliate-program").'" />'.esc_html__($value,"rtwwwap-wp-wc-affiliate-program").'</label>';
							
						}
						$rtwwwap_html .= '</div>';
					}
				}elseif ($custom_fields['custom-input-type'] == 'radio') {
					$rtwwwap_html .= 					'<label>'.esc_html__( $custom_fields['custom-input-label'], "rtwwwap-wp-wc-affiliate-program" ).'</label>';
					$rtwwwap_checkbox_options = explode('|',$custom_fields['custom-input-options']);
					if(is_array($rtwwwap_checkbox_options) && !empty($rtwwwap_checkbox_options)){
						$rtwwwap_html .= '<div class="rtwwwap-custom-radio">';
						foreach ($rtwwwap_checkbox_options as $value) {
							$rtwwwap_html .= 	'<label for="'.$custom_fields['custom-input-id'].'"><input type="'.$custom_fields['custom-input-type'].'" name="'.$custom_fields['custom-input-id'].'"  id="'.$custom_fields['custom-input-id'].'" class="'.$custom_fields['custom-input-class'].' rtwwwap_extra_field" placeholder="'.esc_attr__( $custom_fields['custom-input-label'], "rtwwwap-wp-wc-affiliate-program" ).'"" value="'.esc_attr__(trim($value),"rtwwwap-wp-wc-affiliate-program").'"/>'.esc_html__($value,"rtwwwap-wp-wc-affiliate-program").'</label>';
							
						}
						$rtwwwap_html .= '</div>';
					}
				}
				elseif ($custom_fields['custom-input-type'] == 'select') {
					$rtwwwap_html .= 					'<label>'.esc_html__( $custom_fields['custom-input-label'], "rtwwwap-wp-wc-affiliate-program" ).'</label>';
					$rtwwwap_checkbox_options = explode('|',$custom_fields['custom-input-options']);
					if(is_array($rtwwwap_checkbox_options) && !empty($rtwwwap_checkbox_options)){
						$rtwwwap_html .= 	'<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fas fa-user-edit"></i></span><select name="'.$custom_fields['custom-input-id'].'"  id="'.$custom_fields['custom-input-id'].'" class="'.$custom_fields['custom-input-class'].' rtwwwap_extra_field" >';
						foreach ($rtwwwap_checkbox_options as $options_value) {
							$rtwwwap_html .= 	'<option value="'.esc_attr__(trim($options_value ),"rtwwwap-wp-wc-affiliate-program").'" >'.esc_html__(trim($options_value),"rtwwwap-wp-wc-affiliate-program").'</option>';
							
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


		$rtwwwap_extra_features 	= get_option( 'rtwwwap_extra_features_opt' );
		$rtwwwap_signup_bonus_type 	= isset( $rtwwwap_extra_features[ 'signup_bonus_type' ] ) ? $rtwwwap_extra_features[ 'signup_bonus_type' ] : 0;

		$rtwwwap_reg_temp_features = get_option( 'rtwwwap_reg_temp_opt' );
		$rtwwwap_selected_template = isset( $rtwwwap_reg_temp_features[ 'register_page' ] ) ? $rtwwwap_reg_temp_features[ 'register_page' ] : 1;
		$rtwwwap_use_default_color_checked = isset( $rtwwwap_reg_temp_features[ 'temp_colors' ] ) ? $rtwwwap_reg_temp_features[ 'temp_colors' ] : 1;

		if( $rtwwwap_use_default_color_checked ){
			unset( $rtwwwap_reg_temp_features[ 'mainbg_color' ] );
			unset( $rtwwwap_reg_temp_features[ 'bg_color' ] );
			unset( $rtwwwap_reg_temp_features[ 'head_color' ] );
			unset( $rtwwwap_reg_temp_features[ 'button_color' ] );
		}

		$rtwwwap_login_page_id = get_option('rtwwwap_login_page_id');
		$rtwwwap_affiliate_page_id = get_option('rtwwwap_affiliate_page_id');
		
	

		if( !empty($rtwwwap_login_page_id) )
		{	
			if($rtwwwap_extra_features[ 'succes_register_msg' ] != '')
			{
				$rtwwwap_success_message = $rtwwwap_extra_features[ 'succes_register_msg' ];
			}
			else{
				$rtwwwap_success_message = 'Successfully Register';
			}
			$redirect_url = get_permalink($rtwwwap_login_page_id);
			$redirect_url = add_query_arg( 'success', $rtwwwap_success_message, $redirect_url );

		}
		else if(!empty($rtwwwap_affiliate_page_id))
		{
			$redirect_url = get_permalink($rtwwwap_affiliate_page_id);
			if(isset($rtwwwap_extra_features[ 'succes_register_msg' ]) != '')
			{
				$rtwwwap_success_message = $rtwwwap_extra_features[ 'succes_register_msg' ];
			}
			else{
				$rtwwwap_success_message = 'Successfully Register';
			}
			$redirect_url = add_query_arg( 'success', $rtwwwap_success_message, $redirect_url );

		}

		if( $rtwwwap_selected_template == 1 ){
			$rtwwwap_html = '';
			// getting error message from querystring where key is failed		
				$rtwwwap_html .= '<div id="login"></div>';

			$rtwwwap_bg_color 		= isset( $rtwwwap_reg_temp_features[ 'bg_color' ] ) ? $rtwwwap_reg_temp_features[ 'bg_color' ] : '#EEEEEE';
			$rtwwwap_button_color 	= isset( $rtwwwap_reg_temp_features[ 'button_color' ] ) ? $rtwwwap_reg_temp_features[ 'button_color' ] : '#219595';
			$rtwwwap_form_custom_css= isset( $rtwwwap_reg_temp_features[ 'css' ] ) ? $rtwwwap_reg_temp_features[ 'css' ] : '';
			$rtwwwap_form_title 	= isset( $rtwwwap_reg_temp_features[ 'title' ] ) ? $rtwwwap_reg_temp_features[ 'title' ] : '';

			$rtwwwap_html .= 	'<style>';
			$rtwwwap_html .= 	'#login
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
			if( $rtwwwap_form_title != '' ){
				$rtwwwap_html .= 					esc_html( $rtwwwap_form_title );
			}
			else{
				$rtwwwap_html .= 					esc_html__( "Register your Account", "rtwwwap-wp-wc-affiliate-program" );
			}
			$rtwwwap_html .= 					'</h2>';

			$rtwwwap_html .= 				'</div>';

			$rtwwwap_html .= 					'<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-user"></i></span><input type="text" name="" placeholder="'.esc_attr__( "Username", "rtwwwap-wp-wc-affiliate-program" ).'" id="" class="rtwwap_reg_name" required /></div>';

			$rtwwwap_html .= 					'<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-envelope" aria-hidden="true"></i></span><input type="email" name="user_email" placeholder="'.esc_attr__( "E-Mail", "rtwwwap-wp-wc-affiliate-program" ).'" id="user_email" class="rtwwap_reg_email" required /></div>';

			$rtwwwap_html .=     '<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-phone"></i></span><input type="text"  name="" id="" class="rtwwwap_reg_phone" placeholder="'.esc_attr__( "Phone No.", "rtwwwap-wp-wc-affiliate-program" ).'"  required/> </div>';

			$rtwwwap_html .=     '<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-key" aria-hidden="true"></i></span><input type="password"  name="" id="" class="rtwwwap_passsword" placeholder="'.esc_attr__( "Password", "rtwwwap-wp-wc-affiliate-program" ).'"  required/> </div>';
			
			$rtwwwap_html .=     '<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-key" aria-hidden="true"></i></span><input type="password" class="rtwwwap_confirm_passsword" name="" id="" placeholder="'.esc_attr__( "Confirm_Password", "rtwwwap-wp-wc-affiliate-program" ).'"  required/> </div>';
			

			if( $rtwwwap_signup_bonus_type == 1 ){
				$rtwwwap_html .= 				'<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-envelope"></i></span><input type="text" class="" name="rtwwwap_referral_code_field" id="rtwwwap_referral_code_field" value="" placeholder="'.esc_attr__( "Referral Code", "rtwwwap-wp-wc-affiliate-program" ).'" /></div>';
			}
			$rtwwwap_html .=                	rtwwwap_custom_form_fields_template_one();
			$rtwwwap_html .= 					'<div><input type="button" name="" class="rtwwwap_register" value="'.esc_attr__( "Register", "rtwwwap-wp-wc-affiliate-program" ).'" id="rtwwwap_register" /></div>';
			$rtwwwap_html .= 			'</div>';
			$rtwwwap_html .= 		'</div>';
		}
		elseif( $rtwwwap_selected_template == 2 ){
			$rtwwwap_html = '';


				$rtwwwap_html .= "<div id='login'></div>\n";
			

			$rtwwwap_head_color 	= isset( $rtwwwap_reg_temp_features[ 'head_color' ] ) ? $rtwwwap_reg_temp_features[ 'head_color' ] : '#232055';
			$rtwwwap_button_color 	= isset( $rtwwwap_reg_temp_features[ 'button_color' ] ) ? $rtwwwap_reg_temp_features[ 'button_color' ] : '#232055';
			$rtwwwap_form_custom_css= isset( $rtwwwap_reg_temp_features[ 'css' ] ) ? $rtwwwap_reg_temp_features[ 'css' ] : '';
			$rtwwwap_form_title 	= isset( $rtwwwap_reg_temp_features[ 'title' ] ) ? $rtwwwap_reg_temp_features[ 'title' ] : '';

			$rtwwwap_html .= 	'<style>';
			$rtwwwap_html .= 	'#login
								{
									display:none;
									max-width:550px;
									margin-bottom: 20px;
									border-left: 4px solid #00a0d2;
									border-left-color: #dc3232;
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

			$rtwwwap_html .= 	'<div class="rtwwwap-form-wrapper">';
		

			$rtwwwap_html .= 			'<h2>';
			if( $rtwwwap_form_title != '' ){
				$rtwwwap_html .= 			esc_html( $rtwwwap_form_title );
			}
			else{
				$rtwwwap_html .= 			esc_html__( "Registration Form", "rtwwwap-wp-wc-affiliate-program" );
			}
			$rtwwwap_html .= 			'</h2>';

			$rtwwwap_html .= 			'<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-user"></i></span><input type="text" name="" class="rtwwap_reg_name" placeholder="'.esc_attr__( "Username", "rtwwwap-wp-wc-affiliate-program" ).'" required /></div>';
			$rtwwwap_html .= 			'<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-envelope"></i></span><input type="email" name="" class="rtwwap_reg_email" placeholder="'.esc_attr__( "E-Mail", "rtwwwap-wp-wc-affiliate-program" ).'" required ></div>';
			$rtwwwap_html .=     '<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-phone"></i></span><input type="text"  name="" id="" class="rtwwwap_reg_phone"  placeholder="'.esc_attr__( "Phone No.", "rtwwwap-wp-wc-affiliate-program" ).'"  required/> </div>';
			$rtwwwap_html .=     '<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-key" aria-hidden="true"></i></span><input type="password"  name="" id="" class="rtwwwap_passsword" placeholder="'.esc_attr__( "Password", "rtwwwap-wp-wc-affiliate-program" ).'"  required/> </div>';
			
			$rtwwwap_html .=     '<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-key" aria-hidden="true"></i></span><input type="password" class="rtwwwap_confirm_passsword" name="" id="" placeholder="'.esc_attr__( "Confirm_Password", "rtwwwap-wp-wc-affiliate-program" ).'"  required/> </div>';

			if( $rtwwwap_signup_bonus_type == 1 ){
				$rtwwwap_html .= 		'<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-user"></i></span><input type="text" name="rtwwwap_referral_code_field" id="rtwwwap_referral_code_field" value="" placeholder="'.esc_attr__( "Referral Code", "rtwwwap-wp-wc-affiliate-program" ).'" /></div>';
			}
			$rtwwwap_html .=                	rtwwwap_custom_form_fields_template_one();
			$rtwwwap_html .= 			'<div><input type="button" name="" class="rtwwwap_register" value="'.esc_attr__( "Register", "rtwwwap-wp-wc-affiliate-program" ).'" id="rtwwwap_register" /></div>';
		
			$rtwwwap_html .= 	'</div>';
		}
		elseif( $rtwwwap_selected_template == 3 ){
			$rtwwwap_html = '';

				$rtwwwap_html .= "<div id='login'></div>\n";
			

			$rtwwwap_button_color 	= isset( $rtwwwap_reg_temp_features[ 'button_color' ] ) ? $rtwwwap_reg_temp_features[ 'button_color' ] : '#0150C9';
			$rtwwwap_form_custom_css= isset( $rtwwwap_reg_temp_features[ 'css' ] ) ? $rtwwwap_reg_temp_features[ 'css' ] : '';
			$rtwwwap_form_title 	= isset( $rtwwwap_reg_temp_features[ 'title' ] ) ? $rtwwwap_reg_temp_features[ 'title' ] : '';

			$rtwwwap_html .= 	'<style>';
			$rtwwwap_html .= 	'#login
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
			if( $rtwwwap_form_title != '' ){
				$rtwwwap_html .= 				esc_html( $rtwwwap_form_title );
			}
			else{
				$rtwwwap_html .= 				esc_html__( "Registration", "rtwwwap-wp-w	c-affiliate-program" );
			}
			$rtwwwap_html .= 				'</h2>';

			$rtwwwap_html .= 			'</div>';
			$rtwwwap_html .= 			'<div class="rtwwwap-form-content">';
			$rtwwwap_html .= 					'<label>'.esc_html__( "Username", "rtwwwap-wp-wc-affiliate-program" ).'</label>';
		    $rtwwwap_html .= 					'<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-user"></i></span><input type="text" name="" class="rtwwap_reg_name" placeholder="'.esc_attr__( "Username", "rtwwwap-wp-wc-affiliate-program" ).'" required ></div>';
		  	$rtwwwap_html .= 					'<label>'.esc_html__( "E-Mail", "rtwwwap-wp-wc-affiliate-program" ).'</label>';
			$rtwwwap_html .= 					'<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-envelope"></i></span><input type="email" name="" class="rtwwap_reg_email" placeholder="'.esc_attr__( "E-Mail", "rtwwwap-wp-wc-affiliate-program" ).'" required ></div>';
			$rtwwwap_html .= 					'<label>'.esc_html__( "Phone No.", "rtwwwap-wp-wc-affiliate-program" ).'</label>';
			$rtwwwap_html .=             '<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-phone"></i></span><input type="text"  name="" id="" class="rtwwwap_reg_phone"  placeholder="'.esc_attr__( "Phone No.", "rtwwwap-wp-wc-affiliate-program" ).'"/> </div>';

			$rtwwwap_html .= 					'<label>'.esc_html__( "Password", "rtwwwap-wp-wc-affiliate-program" ).'</label>';
			$rtwwwap_html .=     '<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-key" aria-hidden="true"></i></span><input type="password"  name="" id="" class="rtwwwap_passsword" placeholder="'.esc_attr__( "Password", "rtwwwap-wp-wc-affiliate-program" ).'"  required/> </div>';

			$rtwwwap_html .= 					'<label>'.esc_html__( "Confirm Password", "rtwwwap-wp-wc-affiliate-program" ).'</label>';
			$rtwwwap_html .=     '<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-key" aria-hidden="true"></i></span><input type="password" class="rtwwwap_confirm_passsword" name="" id="" placeholder="'.esc_attr__( "Confirm_Password", "rtwwwap-wp-wc-affiliate-program" ).'"  required/> </div>';
		   if( $rtwwwap_signup_bonus_type == 1 ){
				$rtwwwap_html .= 				'<label>'.esc_html__( "Referral Code", "rtwwwap-wp-wc-affiliate-program" ).'</label>';
				$rtwwwap_html .= 				'<input type="text" name="rtwwwap_referral_code_field" id="rtwwwap_referral_code_field" value="" placeholder="'.esc_attr__( "Referral Code", "rtwwwap-wp-wc-affiliate-program" ).'" />';
			}
			$rtwwwap_html .=                	rtwwwap_custom_form_fields_template_three();
			$rtwwwap_html .= 			'<div><input type="button" name="" class="rtwwwap_register" value="'.esc_attr__( "Register", "rtwwwap-wp-wc-affiliate-program" ).'" id="rtwwwap_register" /></div>';
			$rtwwwap_html .= 			'</div>';
			$rtwwwap_html .= 		'</div>';
			$rtwwwap_html .= 	'</div>';
		}
		elseif( $rtwwwap_selected_template == 4 ){
			$rtwwwap_html = '';

				$rtwwwap_html .= "<div id='login'></div>\n";
			

			$rtwwwap_mainbg_color 	= isset( $rtwwwap_reg_temp_features[ 'mainbg_color' ] ) ? $rtwwwap_reg_temp_features[ 'mainbg_color' ] : '#E85A26';
			$rtwwwap_bg_color 		= isset( $rtwwwap_reg_temp_features[ 'bg_color' ] ) ? $rtwwwap_reg_temp_features[ 'bg_color' ] : '#DADAF2';
			$rtwwwap_button_color 	= isset( $rtwwwap_reg_temp_features[ 'button_color' ] ) ? $rtwwwap_reg_temp_features[ 'button_color' ] : '#E85A26';
			$rtwwwap_form_custom_css= isset( $rtwwwap_reg_temp_features[ 'css' ] ) ? $rtwwwap_reg_temp_features[ 'css' ] : '';
			$rtwwwap_form_title 	= isset( $rtwwwap_reg_temp_features[ 'title' ] ) ? $rtwwwap_reg_temp_features[ 'title' ] : '';

			$rtwwwap_html .= 	'<style>';
			$rtwwwap_html .= 	'#login
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
			if( $rtwwwap_form_title != '' ){
				$rtwwwap_html .= 					esc_html( $rtwwwap_form_title );
			}
			else{
				$rtwwwap_html .= 					esc_html__( "Registration", "rtwwwap-wp-wc-affiliate-program" );
			}
			$rtwwwap_html .= 					'</h2>';

			$rtwwwap_html .= 					'<label>'.esc_html__( "Username", "rtwwwap-wp-wc-affiliate-program" ).'</label>';
		    $rtwwwap_html .= 					'<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-user"></i></span><input type="text" name="" class="rtwwap_reg_name"placeholder="'.esc_attr__( "Username", "rtwwwap-wp-wc-affiliate-program" ).'" required ></div>';
		  	$rtwwwap_html .= 					'<label>'.esc_html__( "E-Mail", "rtwwwap-wp-wc-affiliate-program" ).'</label>';
			$rtwwwap_html .= 					'<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-envelope"></i></span><input type="email" name=""class="rtwwap_reg_email"  placeholder="'.esc_attr__( "E-Mail", "rtwwwap-wp-wc-affiliate-program" ).'" required></div>';
			$rtwwwap_html .= 					'<label>'.esc_html__( "Phone No.", "rtwwwap-wp-wc-affiliate-program" ).'</label>';
			$rtwwwap_html .=             '<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-phone"></i></span><input type="text" name="" id="" class="rtwwwap_reg_phone" placeholder="'.esc_attr__( "Phone No.", "rtwwwap-wp-wc-affiliate-program" ).'"/> </div>';
			$rtwwwap_html .= 					'<label>'.esc_html__( "Password", "rtwwwap-wp-wc-affiliate-program" ).'</label>';
			$rtwwwap_html .=     '<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-key" aria-hidden="true"></i></span><input type="password"  name="" id="" class="rtwwwap_passsword" placeholder="'.esc_attr__( "Password", "rtwwwap-wp-wc-affiliate-program" ).'"  required/> </div>';

			$rtwwwap_html .= 					'<label>'.esc_html__( "Confirm Password", "rtwwwap-wp-wc-affiliate-program" ).'</label>';
			$rtwwwap_html .=     '<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-key" aria-hidden="true"></i></span><input type="password" class="rtwwwap_confirm_passsword" name="" id="" placeholder="'.esc_attr__( "Confirm_Password", "rtwwwap-wp-wc-affiliate-program" ).'"  required/> </div>';

		    if( $rtwwwap_signup_bonus_type == 1 ){
				$rtwwwap_html .= 				'<label>'.esc_html__( "Referral Code", "rtwwwap-wp-wc-affiliate-program" ).'</label>';
				$rtwwwap_html .= 				'<div class="rtwwwap-text"><span class="rtwwwap-text-icon"><i class="fa fa-user"></i></span><input type="text" name="rtwwwap_referral_code_field" id="rtwwwap_referral_code_field" value="" placeholder="'.esc_attr__( "Referral Code", "rtwwwap-wp-wc-affiliate-program" ).'" /></div>';
			}

			$rtwwwap_html .=                	rtwwwap_custom_form_fields_template_three();
			$rtwwwap_html .= 			'<div><input type="button" name="" class="rtwwwap_register" value="'.esc_attr__( "Register", "rtwwwap-wp-wc-affiliate-program" ).'" id="rtwwwap_register" /></div>';

			
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