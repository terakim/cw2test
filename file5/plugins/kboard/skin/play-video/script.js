/**
 * @author https://www.cosmosfarm.com/
 */

var kboard_play_video_form_submit = false;

function kboard_editor_execute(form){
	jQuery.fn.exists = function(){
		return this.length>0;
	};
	
	if(kboard_play_video_form_submit){
		return true;
	}
	
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
		
		jQuery('input[name=kboard_option_youtube_thumbnail_url]', form).val('');
		jQuery('input[name=kboard_option_vimeo_thumbnail_url]', form).val('');
		
		if(jQuery('input[name=kboard_option_youtube_id]', form).val()){
			jQuery('input[name=kboard_option_youtube_thumbnail_url]', form).val('https://img.youtube.com/vi/' + jQuery('input[name=kboard_option_youtube_id]', form).val() + '/hqdefault.jpg');
		}
		
		if(jQuery('input[name=kboard_option_vimeo_id]', form).val() && !jQuery('input[name=thumbnail]', form).val() && !jQuery('#kboard-play-video-thumbnail').val()){
			jQuery.ajax({
				url: "//vimeo.com/api/oembed.json?url=https://vimeo.com/" + jQuery('input[name=kboard_option_vimeo_id]', form).val(),
				success: function(data){
					var vimeo_thumbnail = kboard_vimeo_thumbnail(data);
					
					if(!vimeo_thumbnail && confirm(kboard_play_video_localize_strings.no_upload)){
						jQuery(form).data('submitted', '');
						jQuery('[type=submit]', form).text(kboard_play_video_localize_strings.save);
						jQuery('[type=submit]', form).val('');
					}
					else{
						kboard_play_video_form_submit = true;
						jQuery("#kboard-play-video-form").submit();
					}
				},
				error: function(){
					if(confirm(kboard_play_video_localize_strings.no_upload)){
						jQuery(form).data('submitted', '');
						jQuery('[type=submit]', form).text(kboard_play_video_localize_strings.save);
						jQuery('[type=submit]', form).val('');
					}
					else{
						kboard_play_video_form_submit = true;
						jQuery("#kboard-play-video-form").submit();
					}
				}
			});
		}
		else{
			kboard_play_video_form_submit = true;
			jQuery("#kboard-play-video-form").submit();
		}
	}
	
	return false;
}

function kboard_vimeo_thumbnail(data){
	if(data && data.thumbnail_url){
		jQuery('input[name=kboard_option_vimeo_thumbnail_url]').val(data.thumbnail_url);
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

jQuery(document).ready(function(){
	jQuery('#video_url').change(function(){
		if(jQuery(this).val().indexOf('youtube') >= 0 || jQuery(this).val().indexOf('youtu.be') >= 0){
			jQuery('#youtube_id').val(kboard_youtube_parser(jQuery(this).val()));
		}
		else if(jQuery(this).val().indexOf('vimeo') >= 0){
			jQuery('#vimeo_id').val(kboard_vimeo_parser(jQuery(this).val()));
		}
	});
});

function kboard_youtube_parser(url){
	var regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
	var match = url.match(regExp);
	return (match&&match[7].length==11) ? match[7] : '';
}

function kboard_vimeo_parser(url){
	var m = url.match(/^.+vimeo.com\/(.*\/)?([^#\?]*)/);
	return m ? m[2] || m[1] : '';
}