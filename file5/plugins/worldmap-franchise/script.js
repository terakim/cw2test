/**
 * @author http://www.cosmosfarm.com/
 */

function kboard_editor_execute(form){
	jQuery.fn.exists = function(){
		return this.length>0;
	};
	
	/*
	 * 잠시만 기다려주세요.
	 */
	if(jQuery(form).data('submitted')){
		alert(kboard_localize_strings.please_wait);
		return false;
	}
	
	/*
	 * 폼 유효성 검사
	 */
	if(!jQuery('input[name=title]', form).val()){
		// 제목 필드는 항상 필수로 입력합니다.
		alert(kboard_localize_strings.please_enter_the_title);
		jQuery('input[name=title]', form).focus();
		return false;
	}
	if(jQuery('input[name=kboard_option_map_address]', form).exists() && !jQuery('input[name=kboard_option_map_address]', form).val()){
		// 지도 표시 주소 필드는 항상 필수로 입력합니다.
		alert('지도 표시 주소를 입력해주세요.');
		jQuery('[name=kboard_option_map_address]', form).focus();
		return false;
	}
	if(jQuery('input[name=kboard_option_map_location_lat]', form).exists() && !jQuery('input[name=kboard_option_map_location_lat]', form).val()){
		// 지도 표시 좌표는 항상 필수로 입력합니다.
		alert('지도 표시 좌표 (위도)를 입력해주세요.');
		jQuery('[name=kboard_option_map_location_lat]', form).focus();
		return false;
	}
	if(jQuery('input[name=kboard_option_map_location_lng]', form).exists() && !jQuery('input[name=kboard_option_map_location_lng]', form).val()){
		// 지도 표시 좌표는 항상 필수로 입력합니다.
		alert('지도 표시 좌표 (경도)를 입력해주세요.');
		jQuery('[name=kboard_option_map_location_lng]', form).focus();
		return false;
	}
	if(parseInt(jQuery('input[name=user_id]', form).val()) > 0){
		// 로그인 사용자의 경우 비밀글 체크시에만 비밀번호를 필수로 입력합니다.
		if(jQuery('input[name=secret]', form).prop('checked') && !jQuery('input[name=password]', form).val()){
			alert(kboard_localize_strings.please_enter_the_password);
			jQuery('input[name=password]', form).focus();
			return false;
		}
	}
	else{
		// 비로그인 사용자는 반드시 비밀번호를 입력해야 합니다.
		if(!jQuery('input[name=password]', form).val()){
			alert(kboard_localize_strings.please_enter_the_password);
			jQuery('input[name=password]', form).focus();
			return false;
		}
	}
	if(jQuery('input[name=captcha]', form).exists() && !jQuery('input[name=captcha]', form).val()){
		// 캡차 필드가 있을 경우 필수로 입력합니다.
		alert(kboard_localize_strings.please_enter_the_CAPTCHA);
		jQuery('input[name=captcha]', form).focus();
		return false;
	}
	
	jQuery(form).data('submitted', 'submitted');
	jQuery('[type=submit]', form).text(kboard_localize_strings.please_wait);
	jQuery('[type=submit]', form).val(kboard_localize_strings.please_wait);
	return true;
}

function kboard_toggle_password_field(checkbox){
	var form = jQuery(checkbox).parents('.kboard-form');
	if(jQuery(checkbox).prop('checked')){
		jQuery('.secret-password-row', form).show();
		setTimeout(function(){
			jQuery('.secret-password-row input[name=password]', form).focus();
		}, 0);
	}
	else{
		jQuery('.secret-password-row', form).hide();
		jQuery('.secret-password-row input[name=password]', form).val('');
	}
}

function kboard_worldmap_franchise_address_to_gps(form){
	var address = jQuery('input[name=kboard_option_map_address]', form).val();
	if(address){
		jQuery.post(worldmap_franchise_editor.permalink, {action:'kboard_worldmap_franchise_geocode', board_id:worldmap_franchise_editor.board_id, address:address, security:worldmap_franchise_editor.security}, function(res){
			if(res.result == 'success'){
				jQuery('input[name=kboard_option_map_location_lat]', form).val(res.data[0].geometry.location.lat);
				jQuery('input[name=kboard_option_map_location_lng]', form).val(res.data[0].geometry.location.lng);
				setTimeout(function(){
					alert('입력되었습니다.');
				});
			}
		});
	}
	else{
		alert('지도 표시 주소를 입력해주세요.');
		jQuery('input[name=kboard_option_map_address]', form).focus();
	}
}

function kboard_worldmap_franchise_gps_to_address(form){
	var lat = jQuery('input[name=kboard_option_map_location_lat]', form).val();
	var lng = jQuery('input[name=kboard_option_map_location_lng]', form).val();
	var address = lat + ',' + lng;
	
	if(lat && lng){
		jQuery.post(worldmap_franchise_editor.permalink, {action:'kboard_worldmap_franchise_geocode', board_id:worldmap_franchise_editor.board_id, address:address, security:worldmap_franchise_editor.security}, function(res){
			if(res.result == 'success'){
				jQuery('input[name=kboard_option_map_address]', form).val(res.data[0].formatted_address);
				setTimeout(function(){
					alert('입력되었습니다.');
				});
			}
		});
	}
	else if(!lat){
		alert('지도 표시 좌표 (위도)를 입력해주세요.');
		jQuery('input[name=kboard_option_map_location_lat]', form).focus();
	}
	else{
		alert('지도 표시 좌표 (경도)를 입력해주세요.');
		jQuery('input[name=kboard_option_map_location_lng]', form).focus();
	}
}

jQuery(window).bind('beforeunload',function(e){
	e = e || window.event;
	if(jQuery('.kboard-form').data('submitted') != 'submitted'){
		var dialogText = kboard_localize_strings.changes_you_made_may_not_be_saved;
		e.returnValue = dialogText;
		return dialogText;
	}
});