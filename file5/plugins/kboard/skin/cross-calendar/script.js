/**
 * @author https://www.cosmosfarm.com/
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
	var validation = '';
	kboard_fields_validation(form, function(fields){
		if(fields){
			validation = fields;
			jQuery(fields).focus();
		}
	});
	
	if(!validation){
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
		
		jQuery(form).data('validation', 'ok');
	}
	
	if(jQuery(form).data('validation') == 'ok'){
		jQuery(form).data('submitted', 'submitted');
		jQuery('[type=submit]', form).text(kboard_localize_strings.please_wait);
		jQuery('[type=submit]', form).val(kboard_localize_strings.please_wait);
		return true;
	}
	
	return false;
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

function kboard_radio_reset(obj){
	jQuery(obj).parents('.kboard-attr-row').find('input[type=radio]').each(function(){
		jQuery(this).prop('checked',false);
	});
}

jQuery(window).bind('beforeunload',function(e){
	e = e || window.event;
	if(jQuery('.kboard-form').data('submitted') != 'submitted'){
		var dialogText = kboard_localize_strings.changes_you_made_may_not_be_saved;
		e.returnValue = dialogText;
		return dialogText;
	}
});

function kboard_set_start_date(start_date){
	jQuery('#kboard_option_end_date').val(start_date);
}

function kboard_end_date_check(end_date){
	var start_date = jQuery('#kboard_option_start_date').val();
	if(start_date > end_date){
		jQuery('#kboard_option_end_date').val(start_date);
		alert(kboard_cross_calendar_editor_settings.end_date_check_message);
	}
}

function kboard_set_title_color(obj){
	jQuery('.event-name-color').each(function(){
		jQuery(this).removeClass('active');
	});
	jQuery('input[name=kboard_option_color]').val(jQuery(obj).data('color'));
	jQuery(obj).addClass('active');
}

function kboard_event_time_all_day_long(obj){
	if(jQuery(obj).prop('checked')){
		jQuery('input[name=kboard_option_start_time]').val('');
		jQuery('input[name=kboard_option_end_time]').val('');
		jQuery('input[name=kboard_option_start_time]').css('display', 'none');
		jQuery('input[name=kboard_option_end_time]').css('display', 'none');
	}
	else{
		jQuery('input[name=kboard_option_start_time]').val('09:00')
		jQuery('input[name=kboard_option_end_time]').val('18:00')
		jQuery('input[name=kboard_option_start_time]').css('display', 'inline-block');
		jQuery('input[name=kboard_option_end_time]').css('display', 'inline-block');
	}
}

jQuery(document).ready(function(){
	if(kboard_cross_calendar_editor_settings.locale == 'ko_KR'){
		jQuery('.datepicker').datepicker({
			closeText : '닫기',
			prevText : '이전달',
			nextText : '다음달',
			currentText : '오늘',
			monthNames : [ '1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월' ],
			monthNamesShort : [ '1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월' ],
			dayNames : [ '일', '월', '화', '수', '목', '금', '토' ],
			dayNamesShort : [ '일', '월', '화', '수', '목', '금', '토' ],
			dayNamesMin : [ '일', '월', '화', '수', '목', '금', '토' ],
			weekHeader : 'Wk',
			dateFormat : 'yy-mm-dd',
			firstDay : 0,
			isRTL : false,
			duration : 0,
			showAnim : 'show',
			showMonthAfterYear : true,
			yearSuffix : '년'
		});
	}
	else{
		jQuery('.datepicker').datepicker({dateFormat : 'yy-mm-dd'});
	}
	jQuery('.timepicker').timepicker({'timeFormat': 'HH:mm:ss'});
});