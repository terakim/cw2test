<?php
define("MBW_REQUEST_MODE", "API");
if(!defined('_MB_')) exit();

do_action('mbw_template_api_init');

if(!mbw_verify_nonce()){
	mbw_error_message("MSG_NONCE_MATCH_ERROR", "","1401");
}

$query_command		= "";
$send_data					= array();
$where_data				= array();
$query_data				= array();

do_action('mbw_template_api_header');

if($mstore->get_result_data("state")=="error"){
	echo mbw_data_encode($mstore->result_data);	
	exit;
}
$mb_user_level			= mbw_get_user("fn_user_level");

if(mbw_get_param("mode")=="user"){
	if(mbw_get_param("board_action")=="menu"){
		$user_name		= "";
		$user_point		= "0";
		$user_level		= "0";
		$menu_body		= array();
		$menu_data		= array();

		if(mbw_get_option("show_name_popup")!==0){
			$menu_field		= "*";
			$select_query	= mbw_get_add_query(array("column"=>$menu_field,"table"=>$mb_admin_tables["users"]));
			$user_data		= $mdb->get_row($mdb->prepare($select_query." where ".$mb_fields["users"]["fn_pid"]."=%d", mbw_get_param("pid")),ARRAY_A);
			if(!empty($user_data)){
				$menu_options	= mbw_get_param("option");
				$board_type		= mbw_get_board_option("fn_board_type");

				if(($board_type!="admin" && $board_type!="custom")){
					if(strpos($menu_options, 'search')!==false) $menu_body[]		= '<a href="'.mbw_get_url( array("board_pid"=>"","mode"=>"list","board_page"=>1,"search_field"=>"","search_text"=>"","category1"=>mbw_get_param("category1"),"category2"=>mbw_get_param("category2"),"category3"=>mbw_get_param("category3"),"list_type"=>mbw_get_param("list_type")) ).'&search_field=fn_user_pid&search_text='.$user_data[$mb_fields["users"]["fn_pid"]].'"><span>'.__MW("W_USER_SEARCH").'</span></a>';
				}

				if($mb_user_level>=10 && strpos($menu_options, 'email')!==false && !empty($user_data[$mb_fields["users"]["fn_user_email"]])) $menu_body[]		= '<a href="mailto:'.$user_data[$mb_fields["users"]["fn_user_email"]].'"><span>'.__MW("W_USER_EMAIL").'</span></a>';
				if(strpos($menu_options, 'homepage')!==false && !empty($user_data[$mb_fields["users"]["fn_user_homepage"]])) $menu_body[]		= '<a href="'.$user_data[$mb_fields["users"]["fn_user_homepage"]].'" target="_blank"><span>'.__MW("W_USER_HOMEPAGE").'</span></a>';
				if(strpos($menu_options, 'blog')!==false && !empty($user_data[$mb_fields["users"]["fn_user_blog"]])) $menu_body[]		= '<a href="'.$user_data[$mb_fields["users"]["fn_user_blog"]].'" target="_blank"><span>'.__MW("W_USER_BLOG").'</span></a>';
				
				$user_name		= $user_data[$mb_fields["users"]["fn_user_name"]];
				$user_point		= $user_data[$mb_fields["users"]["fn_user_point"]];
				$user_level		= $user_data[$mb_fields["users"]["fn_user_level"]];
			}
			if(has_filter('mf_user_popup_menu')) $menu_body			= apply_filters("mf_user_popup_menu",$menu_body);
		}
		$menu_data["head"]	= '';
		$menu_data["user"]		= array("name"=>$user_name,"point"=>$user_point,"level"=>$user_level);
		$menu_data["body"]	= $menu_body;
		$menu_data["foot"]		= '';		
		$mstore->set_result_data(array("data"=>$menu_data));
	}
}else if(mbw_get_param("mode")=="plugin"){
	do_action('mbw_template_api_plugin');
}else if(mbw_get_param("mode")=="comment"){
	$comment_pid				= intval(mbw_get_param("comment_pid"));	
	$select_query					= mbw_get_add_query(array("column"=>"*","join"=>"none","table"=>$mb_comment_table_name), array(array("field"=>$mb_fields["select_comment"]["fn_pid"],"value"=>$comment_pid)));
	mbw_set_comment_item_query($select_query);
	if(mbw_get_param("board_action")=="modify"){

		$mb_user_pid							= intval(mbw_get_user("fn_pid"));		

		//자신이 쓴 댓글이거나 수정 권한이 있을 경우에만 댓글 수정폼을 출력하고 아니면 에러 메시지 출력
		if($mb_user_pid==mbw_get_comment_item("fn_user_pid") || intval(mbw_get_board_option("fn_modify_level")) <= $mb_user_level){
			$modify_html		= '<div class="cmt-input-box">';
			$modify_html		= $modify_html.'<div class="cmt-input-head">';
			$modify_html		= $modify_html.'<form name="'.mbw_get_param("board_name").'_form_comment_modify" id="'.mbw_get_param("board_name").'_form_comment_modify" method="post" action="">';

			$comment_modify_data				= mbw_json_decode(mbw_get_model("comment_write"));	
			foreach($comment_modify_data as $data){
				if(mbw_check_item($data)) $modify_html		= $modify_html.mbw_get_comment_template($data,null,false,"modify");
			}

			$modify_html		= $modify_html.'</form>';
			$modify_html		= $modify_html.'</div>';

			$modify_html		= $modify_html.'<div class="comment-btn"><div class="btn-box-right">';
			$modify_html		= $modify_html.mbw_get_btn_template(array("name"=>"Send_Comment_Modify","onclick"=>"sendCommentData('modify')","class"=>"btn btn-default"));			
			$modify_html		= $modify_html.'</div></div>';

			$modify_html		= $modify_html.'</div>';

			$mstore->set_result_data(array("data"=>$modify_html));
		}else{
			mbw_error_message("MSG_PERMISSION_ERROR", $mb_languages["W_MODIFY"]);
		}
	}	

}else{	
	$board_pid			= intval(mbw_get_param("board_pid"));
	$board_type		= mbw_get_board_option("fn_board_type");

	global $mb_board_table_name;
	if((mbw_is_admin_table($mb_board_table_name) || $board_type=="user" || $board_type=="commerce") && !mbw_is_admin_page()){
		mbw_error_message("MSG_NONCE_MATCH_ERROR", "","1401");
		echo mbw_data_encode($mstore->result_data);	
		exit;
	}
	
	if(mbw_get_param("board_action")=="content" && intval(mbw_get_board_option("fn_view_level")) <= $mb_user_level){
		$select_query					= mbw_get_add_query(array("column"=>$mb_fields["select_board"]["fn_content"].','.$mb_fields["select_board"]["fn_data_type"].','.$mb_fields["select_board"]["fn_is_secret"].','.$mb_fields["select_board"]["fn_user_pid"].','.$mb_fields["select_board"]["fn_parent_user_pid"].','.$mb_fields["select_board"]["fn_passwd"],"join"=>"none"), array(array("field"=>$mb_fields["select_board"]["fn_pid"],"value"=>mbw_get_param("board_pid"))));
		mbw_get_board_item_query($select_query);
		
		if(intval(mbw_get_board_item("fn_is_secret"))==1 && mbw_is_secret(array(mbw_get_board_item("fn_user_pid"),mbw_get_board_item("fn_parent_user_pid")),mbw_get_board_item("fn_passwd"))){
			$content		= "<strong>".mbw_error_message("MSG_SECRET")."</strong>";
		}else{
			$content		= mbw_get_board_item("fn_content");
			mbw_analytics("today_page_view");
		}

		$mstore->set_result_data(array("data"=>$content));

	}else if(mbw_get_param("board_action")=="content_reply" && intval(mbw_get_board_option("fn_view_level")) <= $mb_user_level){
		$select_query					= mbw_get_add_query(array("column"=>$mb_fields["select_board"]["fn_content"].','.$mb_fields["select_board"]["fn_data_type"].','.$mb_fields["select_board"]["fn_is_secret"].','.$mb_fields["select_board"]["fn_user_pid"].','.$mb_fields["select_board"]["fn_parent_user_pid"].','.$mb_fields["select_board"]["fn_passwd"],"join"=>"none"), array(array("field"=>$mb_fields["select_board"]["fn_pid"],"value"=>mbw_get_param("board_pid"))));
		mbw_get_board_item_query($select_query);
		
		if(intval(mbw_get_board_item("fn_is_secret"))==1 && mbw_is_secret(array(mbw_get_board_item("fn_user_pid"),mbw_get_board_item("fn_parent_user_pid")),mbw_get_board_item("fn_passwd"))){
			$content		= "<strong>".mbw_error_message("MSG_SECRET")."</strong>";
		}else{
			$select_query		= mbw_get_add_query(array("column"=>"count(*)","join"=>"none"), array(array("field"=>$mb_fields["select_board"]["fn_gid"],"value"=>mbw_get_param("board_pid"))));
			$reply_count		= $mdb->get_var($select_query);			

			if($reply_count>1){
				$select_query		= mbw_get_add_query(array("column"=>$mb_fields["select_board"]["fn_content"],"join"=>"none"), array(array("field"=>$mb_fields["select_board"]["fn_gid"],"value"=>mbw_get_param("board_pid"))));
				$select_query		.= ' order by '.$mb_fields["select_board"]["fn_reply"].' asc';
				$content_items	= $mdb->get_results($select_query,ARRAY_A);
				$content			= '';
				foreach($content_items as $item){
					if(!empty($content)){
						//답변
						$content		.= '<div class="mb-open-qa-border" ></div>';
						$content		.= '<div class="mb-open-qa-reply"><div class="mb-open-qa-text mb-open-qa-text-a">A</div><div class="mb-open-qa-item">'.mbw_htmlspecialchars_decode($item[$mb_fields["select_board"]["fn_content"]]).'</div></div>';
					}else{
						//질문
						$content		.= '<div class="mb-open-qa-question"><div class="mb-open-qa-text mb-open-qa-text-q">Q</div><div class="mb-open-qa-item">'.mbw_htmlspecialchars_decode($item[$mb_fields["select_board"]["fn_content"]]).'</div></div>';
					}					
				}
				$content			= '<div class="mb-open-qa-box">'.$content.'</div>';
			}else{
				$content		= mbw_get_board_item("fn_content");
			}
			mbw_analytics("today_page_view");
		}

		$mstore->set_result_data(array("data"=>$content));


	}else if(mbw_get_param("board_action")=="load" && intval(mbw_get_board_option("fn_list_level")) <= $mb_user_level){
		$list_html				= "";
		$board_data			= array();		

		if(mbw_get_param("list_type")=="calendar"){
			$list_html		= mbw_get_calendar_template("api",mbw_get_param("calendar_date"));
			$board_data	= $list_html;
		}else if(mbw_get_param("list_type")=="gallery"){
			mbw_set_board_where(array("field"=>"fn_image_path", "value"=>"", "sign"=>"!="));		//이미지가 없는 글 제외
			mbw_set_board_where(array("field"=>"fn_is_secret", "value"=>"0", "sign"=>"="));			//비밀글 제외

			$list_model			= mbw_json_decode(mbw_get_model("list_gallery"));
			$list_data			= mbw_get_list_setup_data($list_model);

			$select_query				= mbw_get_add_query(array("column"=>"*"), "where", "order")." limit 0, 2000";
			mbw_set_board_items_query($select_query);
		
			if($list_data["total_count"] > 0){
				$board_items		= mbw_get_board_items();
				$load_count		= count($board_items);
				foreach($board_items as $item){	
					mbw_set_board_item($item);
					$category_item_class		= mbw_get_category_item_class(mbw_get_board_item("fn_category1"));
					$list_html						= $list_html.'<div class="gallery-item-box '.$category_item_class.' '.mbw_get_param("responsive_class").'" style=""><div class="gallery-item-wrap">';
					foreach($list_model as $data){
						if(mbw_check_item($data)){
							if(mbw_check_item($data)) $list_html			= $list_html.mbw_get_list_template($data,array("t_td"=>"div"),false);
						}
					}
					$list_html			= $list_html.'</div></div>';
				}
			}else{
				$list_html			= $list_html.'<div style="text-align:center;padding:20px !important;">'.__MM("MSG_LIST_ITEM_EMPTY")."</div>";
			}
			$board_data["head"]						= "";		
			$board_data["body"]						= $list_html;		
			$board_data["foot"]						= "";		
			$board_data["options"]					= $list_data;
			$board_data["pagination"]				= mbw_get_pagination_template(array("total_count"=>$list_data["total_count"]));

		}else if(mbw_get_param("list_type")=="list"){
			if(mbw_get_param('search_text')!=''){
				mbw_init_board_where();
				$search_text			= mbw_get_param('search_text');
				$search_array		= array();
				$search_array[]		= $mdb->prepare("title like %s",'%'.$search_text.'%');
				$search_array[]		= $mdb->prepare("content like %s",'%'.$search_text.'%');
				$search_array[]		= $mdb->prepare("tag like %s",'%'.$search_text.'%');
				$search_array[]		= $mdb->prepare("category1 like %s",'%'.$search_text.'%');
				$search_array[]		= $mdb->prepare("site_link1 like %s",'%'.$search_text.'%');
				$search_query		= implode( ' OR ', $search_array);
				mbw_set_board_where(array("query"=>$search_query));
			}
			$map_zoom		= mbw_get_param('mb_map_zoom');
			if(mbw_get_param('mb_bounds_minx')!='' && mbw_get_param('mb_bounds_miny')!=''){
				$bounds_minx		= mbw_get_param('mb_bounds_minx');
				$bounds_maxx		= mbw_get_param('mb_bounds_maxx');
				$bounds_miny		= mbw_get_param('mb_bounds_miny');
				$bounds_maxy		= mbw_get_param('mb_bounds_maxy');
				if(intval($bounds_minx)>intval($bounds_maxx)){
					$bounds_minx		= '-180';
					$bounds_maxx		= '180';
				}
				mbw_set_board_where(array("field"=>"fn_gps_longitude", "value"=>$bounds_minx, "sign"=>">="));
				mbw_set_board_where(array("field"=>"fn_gps_longitude", "value"=>$bounds_maxx, "sign"=>"<="));
				mbw_set_board_where(array("field"=>"fn_gps_latitude", "value"=>$bounds_miny, "sign"=>">="));
				mbw_set_board_where(array("field"=>"fn_gps_latitude", "value"=>$bounds_maxy, "sign"=>"<="));
			}

			$select_query			= mbw_get_add_query(array("column"=>"*","join"=>"none"), "where", "order")." limit 0, 2000";
			//최적화 쿼리
			//$select_query			= mbw_get_add_query(array("column"=>"pid,gid,title,content,image_path,category1,category2,category3,gps_latitude,gps_longitude,comment_count,reg_date,is_notice,is_secret,user_pid,data_type,site_link2,hit,ext1","join"=>"none"), "where", "order")." limit 0, 2000";			
			mbw_set_board_items_query($select_query);
			$list_model			= mbw_json_decode(mbw_get_model("list"));
			$list_data				= mbw_get_list_setup_data($list_model);

			if($list_data["total_count"] > 0){
				$markers			= array();
				$list_index		= (intval(mbw_get_board_option("fn_page_size"))*(intval(mbw_get_param("board_page")-1)))+1;
				$shortcode_args		= mbw_get_vars("shortcode_args");
				if(!empty($shortcode_args['marker_label'])) $marker_label		= $shortcode_args['marker_label'];
				else $marker_label		= 'title';
				$items	= mbw_get_board_items();
				foreach($items as $item){
					mbw_set_board_item($item);
					if($marker_label=='place'){
						$marker_name		= mbw_get_board_item("fn_ext1");
						if(empty($marker_name)) $marker_name		= mbw_get_board_item("fn_title");
					}else{
						$marker_name		= mbw_get_board_item("fn_title");
					}
					$gps_latitude			= mbw_get_board_item("fn_gps_latitude");
					$gps_longitude		= mbw_get_board_item("fn_gps_longitude");
					$markers[]			= '{"pid":"'.mbw_get_board_item("fn_pid").'","gid":"'.mbw_get_board_item("fn_gid").'","index":"'.$list_index.'","type":"1","title":"'.trim($marker_name).'","img":"'.mbw_get_board_item("fn_image_path").'","latitude":"'.$gps_latitude.'","longitude":"'.$gps_longitude.'","category1":"'.mbw_get_board_item("fn_category1",false).'","category2":"'.mbw_get_board_item("fn_category2",false).'","category3":"'.mbw_get_board_item("fn_category3",false).'","url":"'.mbw_get_url(array('vid'=>mbw_get_board_item('fn_pid')),"","").'"}';

					if(intval(mbw_get_board_item("fn_is_notice"))==1){
						$item_class		= ' class="notice"';
					}else if($list_index>100){
						$item_class		= ' class="mb-hide"';
					}else{
						$item_class		= '';
					}
					$list_html			= $list_html.'<tr id="'.mbw_get_id_prefix()."tr_".$list_index.'"'.$item_class.'>';
					foreach($list_model as $data){
						if(mbw_check_item($data)) $list_html			= $list_html.mbw_get_list_template($data,null,false);
					}	
					$list_html			= $list_html.'</tr>';
					//아래 주석 제거하면 목록에서 콘텐츠 내용도 보이도록 표시함
					//echo '<tr><td colspan="'.$list_data["cols"].'">'.mbw_get_board_item("fn_content").'</td></tr>';
					$list_index++;
				}
				unset($items);
			}else{
				$list_html			= $list_html.'<tr><td colspan="'.$list_data["cols"].'" align="center" style="text-align:center;"></td></tr>';
			}			
			$board_data["head"]					= "";		
			$board_data["body"]					= $list_html;		
			$board_data["foot"]						= "";
			$board_data["options"]					= $list_data;
			$board_data["markers"]				= '['.implode( ',', $markers).']';
			$board_data["pagination"]				= "";
		}		
		$mstore->set_result_data(array("data"=>$board_data));		
	}
}


if($mstore->get_result_data("state")=="error"){
	echo mbw_data_encode($mstore->result_data);	
	exit;
}

do_action('mbw_template_api_footer');
echo mbw_data_encode($mstore->get_result_datas(array("state"=>"success")));	
exit;
?>	