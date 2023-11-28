<?php
$desktop_model					= array();
$tablet_model						= array();
$mobile_model					= array();
$desktop_model['version']		= "1.0.0";

// Board Model
$desktop_model['list']		= '
{"field":"fn_image_path","name":"W_IMAGE","width":"100px","height":"82px","mobile_width":"80px","mobile_height":"80px","search":"false","type":"skin_img_bg","td_class":"webzine-image-box","class":"cursor_pointer","size":"small"},
{"field":"fn_title","name":"W_TITLE","width":"","type":"skin_title_webzine","maxlength":"20","content_maxlength":"42","maxtext":"..","td_class":"webzine-item-box","link_target":"_blank"},
{"field":"fn_content","name":"W_CONTENT","type":"search"},
{"field":"fn_tag","name":"W_TAG","type":"search"}
';
$desktop_model['list_admin']		= '
{"type":"list_check","width":"30px","level":"10","class":"list_check"},
{"field":"fn_pid","name":"W_PID","width":"50px","type":"pid","class":"mb-pid","responsive":"mb-hide-mobile mb-hide-tablet"},
{"field":"fn_title","name":"W_TITLE","width":"","type":"title","maxlength":"90","maxtext":"..","td_class":"text-left"},
{"field":"fn_user_name","name":"W_WRITER","width":"115px","class":"mb-user-name","responsive":"mb-hide-mobile"},
{"field":"fn_content","name":"W_CONTENT","type":"search"},
{"field":"fn_reg_date","name":"W_DATE","width":"90px","type":"date","class":"date","responsive":"mb-hide-mobile"},
{"field":"fn_hit","name":"W_HIT","width":"60px","search":"false","class":"hit","responsive":"mb-hide-mobile mb-hide-tablet"},
{"field":"fn_tag","name":"W_TAG","type":"search"}
';

$desktop_model['list_gallery']		= '
{"field":"fn_image_path","name":"W_IMAGE","width":"100%","height":"220px","tablet_height":"200px","mobile_height":"180px","type":"img_bg","class":"img","link":"view","td_class":"gallery-item-img","search":"false","size":"small"},
{"field":"fn_title","name":"W_TITLE","width":"","maxlength":"50","maxtext":"..","td_class":"gallery-title","link":"view"},
{"field":"fn_content","name":"W_CONTENT","type":"search"},
';
$desktop_model['list_calendar']		= '
{"field":"fn_title","name":"W_TITLE","width":"","type":"title_checkbox","maxlength":"8","maxtext":"..","td_class":"text-left"},
{"field":"fn_content","name":"W_CONTENT","type":"search"}
';


//글보기 스킨 수정
$desktop_model['view']		= '
{"tpl":"tag","tag_name":"table","type":"start","name":"W_VIEW_MSG","width":"20%,*","mobile_width":"80px,*","class":"table table-view"},
{"field":"fn_title","name":"W_TITLE","width":"100px","type":"title2","class":"text-left","colspan":"2","td_class":"view-title-wrap"},
{"field":"fn_content","name":"W_CONTENT","width":"100%","type":"content","td_class":"content-box text-left","colspan":"2"},
{"field":"fn_gps_latitude","name":"W_MAP","width":"100%","height":"360px","type":"skin_map","class":"map-view","colspan":"2","td_style":"padding:0px !important","td_class":"content-box"},
{"field":"fn_address","name":"W_ADDRESS","type":"skin_place_address","colspan":"2","td_class":"mb-map-place-info"},
{"field":"fn_category1","name":"W_CATEGORY","width":"100px","display_check":"empty:none","type":"category1","class":"category"},
{"field":"file_download","name":"W_ATTACHMENT","width":"100px","type":"file_download","class":"file-download"},
{"field":"fn_tag","name":"W_TAG","width":"99%","class":"tag","colspan":"2","type":"tag_link"},
{"tpl":"tag","tag_name":"table","type":"end"}
';

//글작성 스킨 수정
$desktop_model['write']		= '
{"tpl":"tag","tag_name":"table","type":"start","name":"W_WRITE","width":"20%,*","mobile_width":"80px,*","class":"table table-write"},
{"field":"fn_category1","name":"W_CATEGORY","width":"100px","type":"category1","class":"category"},
{"field":"fn_user_name","name":"W_WRITER","width":"150px","mobile_width":"100px","modify":"text_static","maxlength":"20","display_check":"login:none","required":"(*)","class":"mb-user-name","filter":"filter_admin","filter_error":"MSG_NAME_UNUSABLE"},
{"field":"fn_passwd","name":"W_PASSWORD","width":"150px","mobile_width":"100px","display_check":"login:hide","maxlength":"16","type":"password","required":"(*)","class":"mb-passwd"},
{"field":"fn_title","name":"W_TITLE","width":"100%","required":"(*)","class":"text-left","required_error":"MSG_FIELD_EMPTY_ERROR2","filter":"filter_swear","filter_error":"MSG_WORD_UNUSABLE"},
{"field":"fn_content","name":"W_CONTENT","width":"100%","height":"360px","type":"content","class":"mb-content","mobile_colspan":"2","td_class":""},
{"field":"fn_ext1","name":"W_PLACE_NAME","width":"100%","required":"(*)","required_error":"MSG_FIELD_EMPTY_ERROR2"},
{"field":"fn_image_path","name":"W_THUMBNAIL","width":"300px","type":"file_singular_upload","class":"file"},
{"field":"fn_address","name":"W_ADDRESS","required":"(*)","width":"200px","type":"skin_address_write","description":"('.__MM("MSG_ADDRESS_DESC").')"},
{"field":"fn_site_link1","name":"W_MAP_ADDRESS","width":"200px","display":"hide"},
{"field":"fn_site_link2","name":"W_REGION","width":"200px","display":"hide"},
{"field":"fn_gps_latitude","name":"W_MAP","width":"100%","height":"360px","type":"skin_map","class":"map-view","mobile_colspan":"2","map_options":"streetViewControl: false,navigationControl: false,mapTypeControl: false"},
{"field":"fn_tag","name":"W_TAG","width":"100%","class":"tag","description":"<br>(MSG_TAG_INPUT)"},
{"type":"kcaptcha_img","name":"W_KCAPTCHA","width":"70px","height":"30px","class":"kcaptcha","level":{"sign":"<","grade":"1"},"modify":"none","description":"<br>(MSG_CAPTCHA_INPUT)"},
{"tpl":"tag","tag_name":"table","type":"end"}
';


// Comment Model
$desktop_model['comment_list']		= '
{"field":"fn_user_name","name":"W_WRITER","width":"100px","class":"cmt-name","type":"cl_name_date"},
{"field":"fn_content","name":"W_CONTENT","width":"60px","type":"content","class":"cmt-content","type":"cl_content"}
';

$desktop_model['comment_write']		= '
{"tpl":"tag","tag_name":"table","type":"start","name":"W_COMMENT","width":"20%,*","mobile_width":"80px,*","class":"table table-comment"},
{"field":"fn_user_name","name":"W_WRITER","width":"100px","login":"cw_name","required":"(*)","class":"mb-user-name","filter":"filter_admin","filter_error":"MSG_NAME_UNUSABLE"},
{"field":"fn_passwd","name":"W_PASSWORD","width":"100px","login":"none","type":"password","required":"(*)","class":"mb-passwd"},
{"type":"kcaptcha_img","name":"W_KCAPTCHA","width":"70px","height":"30px","class":"kcaptcha","level":{"sign":"<","grade":"1"},"modify":"none","description":"<br>(MSG_CAPTCHA_INPUT)"},
{"field":"fn_content","name":"W_CONTENT","width":"100%","type":"cw_content","required":"(*)","class":"mb-comment-content","required_error":"MSG_FIELD_EMPTY_ERROR2","filter":"filter_swear","filter_error":"MSG_WORD_UNUSABLE"},
{"tpl":"tag","tag_name":"table","type":"end"}
';
$desktop_model['comment_reply']	= $desktop_model['comment_write'];


// Tablet Model
$tablet_model					= $desktop_model;

// Mobile Model
$mobile_model				= $desktop_model;

//Filter 사용방법 : 필터에 등록된 단어가 포함되어 있을 경우 에러 출력 (관리자 레벨 이하인 회원에게만 적용)
//{"field":"fn_title","name":"W_TITLE","width":"100px","type":"title","required":"(*)","filter":"filter_swear","filter_error":"금지어가 포함되어 있습니다"},
mbw_set_filter("filter_swear","18아,18놈,18새끼,18뇬,18노,18것,18넘,개년,개놈,개뇬,개새,개색끼,개세끼,개세이,개쉐이,개쉑,개쉽,개시키,개자식,개좆,게색기,게색끼,광뇬,뇬,눈깔,뉘미럴,니귀미,니기미,니미,도촬,되질래,뒈져라,뒈진다,디져라,디진다,디질래,병쉰,병신,뻐큐,뻑큐,뽁큐,삐리넷,새꺄,쉬발,쉬밸,쉬팔,쉽알,스패킹,스팽,시벌,시부랄,시부럴,시부리,시불,시브랄,시팍,시팔,시펄,실밸,십8,십쌔,십창,싶알,쌉년,썅놈,쌔끼,쌩쑈,썅,써벌,썩을년,쎄꺄,쎄엑,쓰바,쓰발,쓰벌,쓰팔,씨8,씨댕,씨바,씨발,씨뱅,씨봉알,씨부랄,씨부럴,씨부렁,씨부리,씨불,씨브랄,씨빠,씨빨,씨뽀랄,씨팍,씨팔,씨펄,아가리,아갈이,엄창,접년,잡놈,재랄,저주글,조까,조빠,조쟁이,조지냐,조진다,조질래,존나,존니,좀물,좁년,좃,좆,좇,쥐랄,쥐롤,쥬디,지랄,지럴,지롤,지미랄,쫍빱,凸,퍽큐,뻑큐,빠큐");
mbw_set_filter("filter_admin","admin,administrator,관리자,운영자");

//Pattern 사용방법 : 입력된 데이타가 요구 패턴에 맞지 않으면 에러 출력 
//{"field":"fn_user_email","name":"W_EMAIL","width":"100px","required":"(*)","pattern":"email","pattern_error":"MSG_EMAIL_FILTER_ERROR"}
mbw_set_pattern("email","/^[0-9a-zA-Z]([-_\.]?[0-9a-zA-Z])*@[0-9a-zA-Z]([-_\.]?[0-9a-zA-Z])*\.[a-zA-Z]{2,3}$/i");


if(!function_exists('mbw_filter_skin_board_model')){
	function mbw_filter_skin_board_model($model){
		if(mbw_get_param("list_type")=="gallery"){
			$args		= mbw_get_vars("shortcode_args");
			if(!empty($args)){
				if(!empty($args["gallery_link_type"])){
					if($args["gallery_link_type"]=="lightbox"){
						$link_attr		= ',"link":"lightbox"';
						$model		= str_replace(',"link":"view"',$link_attr,$model);
					}
				}
			}
		}
		return $model;
	}
}
add_filter('mf_board_model', 'mbw_filter_skin_board_model',3,1);

if(mbw_get_request_mode()=="Frontend"){		// 게시판 모드일 경우에만
	if(mbw_is_admin_page()){		//어드민 페이지에서만 실행
		add_action('mbw_board_skin_search', 'mbw_get_date_search_template');		// 기간 설정 템플릿 추가
		add_action('mbw_board_skin_header', 'mbw_get_copy_move_template');			// 이동, 복사 템플릿 추가
	}
}
?>