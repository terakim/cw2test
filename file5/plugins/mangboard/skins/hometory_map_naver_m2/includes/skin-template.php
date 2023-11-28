<?php
				
//템플릿 함수 등록(템플릿 타입의 접두사, 템플릿 함수명)
if(function_exists('mbw_add_template')) mbw_add_template("skin","mbw_get_skin_template");

if(!function_exists('mbw_get_skin_template')){
	function mbw_get_skin_template($mode, $data){
		$template_start		= '';
		$item_type				= $data["type"];

		$map_key		= mbw_get_vars("mb_map_key");
		$pos_lat		= mbw_get_board_item("fn_gps_latitude");
		$pos_lng		= mbw_get_board_item("fn_gps_longitude");

		if(empty($pos_lat)){
			if(mbw_get_vars("mb_map_lat")!="") $pos_lat		= mbw_get_vars("mb_map_lat");
			else $pos_lat		= "33.450701";
		}
		if(empty($pos_lng)){
			if(mbw_get_vars("mb_map_lng")!="") $pos_lng		= mbw_get_vars("mb_map_lng");
			else $pos_lng		= "126.570667";
		}
		if(empty($data["width"])) $data["width"]			= "100%";
		if(empty($data["height"])) $data["height"]		= "360px";
		if(empty($data["style"])) $data["style"]				= "";
		if(empty($data["item_id"])) $data["item_id"]		= "map1";
		if(empty($data["map_init"])) $data["map_init"]		= "true";

		if($item_type=='skin_img_bg'){
			if(empty($data["value"])){
				$args				= mbw_get_vars("shortcode_args");
				if(!empty($args['default_image_url'])){
					$data["value"]		= $args['default_image_url'];
				}
			}
			if(!empty($data["value"])){
				$img_url			= "";
				$img_link		= "";
				$img_size		= "";
				$file_name		= "";
				if(!empty($data["size"])){
					$img_size			= $data["size"];
					$data["size"]		= "&size=".$data["size"];
				} else $data["size"]		= "";
				if(empty($data["width"])) $data["width"]			= "50px";
				if(empty($data["height"])) $data["height"]		= $data["width"];

				$file_attr					= "";
				if(strpos($data["value"],'http')===0){
					$img_url			= $data["value"];
					$img_link		= $data["value"];
				}else if(mbw_is_image_file($data["value"])){					
					if(!empty($data["value"])) {
						$file_name		= $data["value"];
						$index1			= strpos($file_name,"_")+1;
						$file_name		= substr($file_name,$index1,strlen($file_name)-$index1);
					}
					$img_link	= mbw_get_image_url("url",$data["value"]);
					if(!empty($img_size)) $img_url		= mbw_get_image_url("url_".$img_size,$data["value"]);
					else $img_url		= mbw_get_image_url("url",$data["value"]);					
				}
				if(!empty($img_url)){
					if(!empty($file_name)){
						if(empty($data["alt"])) $file_attr			= $file_attr.' alt="'.$file_name.'"';
					}
					if($item_type=='skin_img_bg'){
						if(empty($data["background-size"])) $data["background-size"] = "cover";
						if(empty($data["background-position"])) $data["background-position"] = "center center";
						if(empty($data["background-repeat"])) $data["background-repeat"] = "no-repeat";

						$template_start	.= '<div class="" style="width:100%; height:100%; position:relative;cursor:pointer;" title="'.__MW("W_MOVE_MAP").'">';
							$template_start	.= '<div style="position:absolute; top:0;left:0; width:100%; height:100%; max-width:100%; opacity:0;" class="mb-hover-bg"><span style="position:absolute; top:calc(50% - 15px);left:calc(50% - 15px);font-size:26px; color:#fff !important;opacity:0.9;line-height:0;"><img  src="'.MBW_SKIN_URL.'images/icon_search.png" style="width:30px; height:30px;" /></span></div>';
							$template_start	.= '<div'.$data["ext"].__STYLE("max-width:".$data["width"].";height:".$data["height"].";margin:0 auto;background-image:url(".$img_url.");background-position:".$data["background-position"].";background-size:".$data["background-size"].";background-repeat:".$data["background-repeat"].";".$data["style"]).' ></div>';
						$template_start	.= '</div>';
					}
				}
			}else{
				if(empty($data["width"])) $data["width"]			= "50px";
				if(empty($data["height"])) $data["height"]		= $data["width"];
				$template_start	= '<div'.__STYLE("width:".$data["width"].";height:".$data["height"].";display:table;").' class=""><div style="display:table-cell;vertical-align:middle;">No image</div></div>';
			}
		}else if($item_type=='skin_title_webzine'){
			$add_comment		= "";
			$add_icon				= "";
			$add_start_icon		= "";
			$add_end_icon		= "";

			//카테고리 표시
			if(mbw_get_param("list_type")=="list"){
				if(mbw_get_board_item("fn_category1")!=""){
					$category_item_class		= mbw_get_category_item_class(mbw_get_board_item("fn_category1"));
					$data["value"]		= '<span class="category1-text '.$category_item_class.'">['.mbw_get_board_item("fn_category1").']</span> '.$data["value"];
				}
			}
			//댓글 개수 표시하기
			if(mbw_get_option("use_view_comment") && mbw_get_board_option("fn_use_comment") == 1 && intval(mbw_get_board_item("fn_comment_count"))>0){
				$add_comment		= "<span class='cmt-count'> [<span class='cmt-count-num'>".mbw_get_board_item("fn_comment_count")."</span>]</span>";
			}
			$write_date			= strtotime( mbw_get_board_item('fn_reg_date') );
			//최근글일 경우 아이콘 표시					
			if(mbw_get_timestamp()-(60*60*24)<$write_date)
				$add_end_icon		= $add_end_icon.' <img class="list-i-new" alt="new" style="vertical-align:middle;" src="'.MBW_SKIN_URL.'images/icon_new.gif" />';

			//비밀글일 경우 아이콘 표시
			if(intval(mbw_get_board_item("fn_is_secret"))==1) 
				$add_start_icon		= $add_start_icon.' <img class="list-i-secret" alt="secret" src="'.MBW_SKIN_URL.'images/icon_secret.gif" /> ';					
							
			if(!empty($data["onclick"])){
				$title_url		= ' href="javascript:;" onclick="'.$data["onclick"].';return false;" class="list_'.mbw_get_board_item('fn_pid').'"';
			}else{
				if(!empty($data["link_target"])){
					$title_url		= ' href="'.mbw_get_url(array('vid'=>mbw_get_board_item('fn_pid')),"","").'" target="'.$data["link_target"].'"';
				}else{
					$title_url		= ' href="'.mbw_get_url(array('vid'=>mbw_get_board_item('fn_pid')),"","").'"';
				}
			}
			$reg_date		= mbw_get_board_item("fn_reg_date");
				
			if( strpos($reg_date, date("Y-m-d", mbw_get_timestamp())) !== false){
				$reg_date	= substr($reg_date,11,5);
			}else{
				$reg_date	= substr($reg_date,0,10);
			}

			$template_start	.= '<a'.$title_url.' title="'.__MW("W_VIEW_DETAILS").': '.strip_tags($data["value"]).'"><div class="webzine-item-title"><span>'.$add_start_icon.$data["value"].$add_comment.$add_end_icon.'</span></div><div class="webzine-item-content">';

			$user_pid			= mbw_get_user('fn_pid');
			$user_level			= intval(mbw_get_user("fn_user_level"));
			if(intval(mbw_get_board_item("fn_is_secret"))==1){
				if((intval(mbw_get_board_option("fn_secret_level"))<=$user_level) || (mbw_is_login() && $user_pid==mbw_get_board_item("fn_user_pid",false))){
					$webzine_content			= mbw_get_board_item("fn_content",false);
				}else{
					$webzine_content			= __MM("MSG_SECRET");
				}			
			}else{
				$webzine_content			= mbw_get_board_item("fn_content",false);
			}

			if(mbw_get_board_item("fn_data_type")=="html") $webzine_content			= mbw_htmlspecialchars_decode($webzine_content);
			$webzine_content			= strip_tags(html_entity_decode($webzine_content, ENT_QUOTES));
			$webzine_content			= trim(str_replace(array("&nbsp;","　"," ","  "), " ", $webzine_content));

			if(empty($data["content_maxlength"])) $data["content_maxlength"]		= 50;
			if(mbw_get_vars("device_type")=="mobile")  $data["content_maxlength"]		= 35;
			if(isset($data["content_maxlength"])){
				$maxlength				= intval($data["content_maxlength"]);
				if(function_exists('mb_strlen')) $content_length	= mb_strlen($webzine_content, mbw_get_option("encoding"));
				else $content_length	= strlen($webzine_content);

				if($maxlength<$content_length){
					if(!isset($data["maxtext"])){
						$data["maxtext"]		= "...";
					}
					if(function_exists('mb_substr')) $webzine_content		= mb_substr($webzine_content, 0, $maxlength, mbw_get_option("encoding")).$data["maxtext"];
					else $webzine_content		= substr($webzine_content, 0, $maxlength).$data["maxtext"];
				}
			}

			$template_start	.= mbw_htmlspecialchars($webzine_content);
			$template_start	.= '</div></a>';
			$template_start	.= '<div class="webzine-item-info"><span class="info-group">';
				$title_format	= 'name_date_hit';
				if(!empty($data["title_format"])){
					$title_format	= $data["title_format"];
				}else{
					$shortcode_args		= mbw_get_vars("shortcode_args");
					if(!empty($shortcode_args["title_format"])) $title_format	= $shortcode_args["title_format"];
				}
				$format_array				= explode('_',$title_format);
				if(!empty($format_array)){
					$mobile_template				= array();
					if(mbw_get_board_item("fn_site_link2",false)!='') $mobile_template['name']		= '<span class="info-name">'.mbw_get_board_item("fn_site_link2",false).'</span><span class="mb-text-split">|</span>';
					$mobile_template['date']		= '<span class="info-date">'.$reg_date.'</span><span class="mb-text-split">|</span>';
					$mobile_template['hit']			= '<span class="info_hit">'.mbw_get_board_item("fn_hit").'</span>';
					foreach($format_array as $name){
						if(!empty($name) && isset($mobile_template[$name])) $template_start	.= $mobile_template[$name];
					}
				}
			$template_start	.= '</span></div>';

		}else if($item_type=='skin_address_write'){
			mbw_load_postcode_script("daum");
			$postcode_id		= "mb_map_postcode100";
			$address			= mbw_get_board_item("fn_address");			
			$template_start	.= '<p><input type="text" name="'.mbw_set_form_name($data["item_name"]).'" onclick="getPostcodeMap(this,\''.$postcode_id.'\');return false;" id="mb_map_address"  onkeypress="checkEnterKey(searchMapAddress);" value="'.($address).'" style="width:calc(100% - 85px);">'.mbw_get_btn_template(array("name"=>"W_MAP_SEARCH","title"=>__MM("MSG_MAP_SEARCH_DESC"),"onclick"=>"searchMapAddress();return false;","class"=>"btn btn-default margin-left-5","style"=>"width:80px;")).'</p>';			
			$template_start	.= '<div id="'.$postcode_id.'" class="mb-map-postcode-wrap" style="display:none;border:1px solid;width:100%;overflow-x:auto;height:300px;margin:5px 0;position:relative"><img src="//t1.daumcdn.net/postcode/resource/images/close.png" style="cursor:pointer;position:absolute;right:0px;top:-1px;z-index:1" onclick="document.getElementById(\''.$postcode_id.'\').style.display=\'none\';" alt="'.__MW("W_CLOSE").'"></div>';
		}else if($item_type=='skin_place_address'){
			$name			= mbw_get_board_item("fn_ext1");
			$address			= mbw_get_board_item("fn_address");
			if(!empty($name)) $template_start	.= '<span class="mb-map-place-name">['.$name.']</span> ';
			if(!empty($address)) $template_start	.= '<span class="mb-map-place-address">'.$address.'</span> ';
		}else if($item_type=='skin_map'){
			if(mbw_is_admin_page()){
				if(mbw_get_param("mode")=='list') return '';
				else return 'none';
			}
			$shortcode_args		= mbw_get_vars("shortcode_args");
			$device_type			= mbw_get_vars("device_type");
			if($mode=="list" || ($mode=="write" && mbw_get_param("board_action")!="modify")){
				if(!empty($shortcode_args[$device_type.'_'.$mode.'_map_lat'])) $pos_lat		= $shortcode_args[$device_type.'_'.$mode.'_map_lat'];
				else if(!empty($shortcode_args[$mode.'_map_lat'])) $pos_lat		= $shortcode_args[$mode.'_map_lat'];
				if(!empty($shortcode_args[$device_type.'_'.$mode.'_map_lng'])) $pos_lng		= $shortcode_args[$device_type.'_'.$mode.'_map_lng'];
				else if(!empty($shortcode_args[$mode.'_map_lng'])) $pos_lng		= $shortcode_args[$mode.'_map_lng'];
			}
			if(!empty($shortcode_args[$device_type.'_'.$mode.'_zoom'])){
				$map_zoom		= $shortcode_args[$device_type.'_'.$mode.'_zoom'];
			}else if(!empty($shortcode_args[$mode.'_zoom'])){
				$map_zoom		= $shortcode_args[$mode.'_zoom'];
			}else{
				$map_zoom		= 14;
			}
			if(empty($data["map_options"])) $data["map_options"]		= 'zoom:'.$map_zoom;
			else $data["map_options"]		.= ',zoom:'.$map_zoom;
			if(mbw_get_trace("mbw_load_map_naver")==""){
				mbw_add_trace("mbw_load_map_naver");
				if(!empty($shortcode_args['map_key'])){
					$map_key		= $shortcode_args['map_key'];
				}else{
					$map_key		= mbw_get_vars("mb_map_key");
				}
				//loadScript('//openapi.map.naver.com/openapi/v3/maps.js?ncpClientId='.$map_key.'&submodules=geocoder',"mb-map-naver","");
				wp_enqueue_script('mb-map-naver', '//openapi.map.naver.com/openapi/v3/maps.js?ncpClientId='.$map_key.'&submodules=geocoder', array(), null);
				loadScript(MBW_SKIN_URL."js/MarkerClustering.js");
				loadScript(MBW_SKIN_URL."js/common.js");
			}			

			$item_html		= "";		

			$template_start		.= '<div'.__STYLE("width:".$data["width"].";height:".$data["height"].";".$data["style"].";line-height:0;").' class="mb-map-content" id="'.$data["item_id"].'"></div>';
			$template_start		.= '<input type="hidden" style="width:155px;" name="gps_latitude" id="mb_gps_latitude" value="'.$pos_lat.'" />';
			$template_start		.= '<input type="hidden" style="width:155px;" name="gps_longitude" id="mb_gps_longitude" value="'.$pos_lng.'" />';
		
			if($mode=="list") {
				$template_start		.= '<div class="mb-map-search-box">';
					$template_start		.= '<div>';
					if(intval(mbw_get_board_option("fn_use_list_search"))==1){
						$template_start		.= '<div style="display:inline-block;position:relative;vertical-align:top !important;"><input type="text" class="search-text" name="search_text" title="'.__MW("W_PLACE_SEARCH").'" placeholder="'.__MW("W_PLACE_SEARCH").'" value="'.mbw_htmlspecialchars(mbw_get_param("search_text")).'" onkeypress="checkEnterKey(sendListTemplateData);"/><input style="display:none !important;" type="text"/>';
						$template_start		.= '<div onclick="sendListTemplateData();return false;" class="cursor_pointer" style="position: absolute;top: 5px;right: 5px;font-size:13px;line-height:1.5;"><img src="'.MBW_SKIN_URL.'images/icon_small_search.png" style="width:16px;height:16px;"></div></div>';
					}
					if(mbw_is_ssl()) $template_start		.= ' <button onclick="mb_setMapCurrentPosition();return false;" title="'.__MW("W_MY_LOCATION").'" class="btn btn-default btn-write" type="button" style="vertical-align:top !important;"><span>'.__MW("W_MY_LOCATION").'</span></button>';
					$mb_user_level		= mbw_get_user("fn_user_level");
					if(intval(mbw_get_board_option("fn_write_level"))==1 || intval(mbw_get_board_option("fn_write_level")) <= $mb_user_level){
						$template_start		.= ' '.mbw_get_btn_template(array("name"=>"W_ADD_PLACE","type"=>"button","href"=>mbw_get_url(array("board_pid"=>"","mode"=>"write","board_action"=>"write")),"class"=>"btn btn-default btn-write","style"=>"vertical-align:top !important;"));
					}					
					$template_start		.= '</div>';
				$template_start		.= '</div>';
			}else if($mode=="write") {
				$template_start		.= '<div>';
					$template_start		.= '<div style="padding:3px 2px 0;"><span style="font-weight:600;">'.__MW("W_COORDINATES").' : </span> '.__MW("W_LATITUDE").' <span class="mb-gps-latitude">'.$pos_lat.'</span>, '.__MW("W_LONGITUDE").' <span class="mb-gps-longitude">'.$pos_lng.'</span></div>';
				$template_start		.= '</div>';
			}
			
			$template_start		.= '<script type="text/javascript">';
				$template_start		.= 'function map_init() { ';
					$options									= array();
					$options['cluster_zoom']				= 11;
					$options['label_zoom']					= 15;
					$options['focus_zoom']					= 15;
					$options['max_marker_label_length']	= 30;

					if(!empty($shortcode_args['cluster_zoom'])){
						$options['cluster_zoom']	= $shortcode_args['cluster_zoom'];
					}					
					if(!empty($shortcode_args['label_zoom'])){
						$options['label_zoom']	= $shortcode_args['label_zoom'];
					}
					if(!empty($shortcode_args['focus_zoom'])){
						$options['focus_zoom']	= $shortcode_args['focus_zoom'];
					}
					if(!empty($shortcode_args['max_marker_label_length'])){
						$options['max_marker_label_length']	= $shortcode_args['max_marker_label_length'];
					}			
					if($mode=="" || $mode=="list"){
						$template_start		.= 'if(typeof initMapList==="function"){ initMapList();}';
					}
					$template_start		.= 'var map = new naver.maps.Map(document.getElementById("'.$data["item_id"].'"), {useStyleMap: true, center: new naver.maps.LatLng('.$pos_lat.','.$pos_lng.'),'.$data["map_options"].'});';
					$template_start		.= 'var map_options	= {"cluster_zoom":'.$options['cluster_zoom'].',"label_zoom":'.$options['label_zoom'].',"focus_zoom":'.$options['focus_zoom'].',"max_marker_label_length":'.$options['max_marker_label_length'].'};';
					$template_start		.= 'initMap(map,"'.$mode.'","'.$data["item_id"].'",map_options);';
					$template_start		.= 'if(typeof setMapBoundsData==="function"){ setMapBoundsData("init");}';
					if($mode=="view" || $mode=="write"){
						if(!empty($shortcode_args['marker_label'])) $marker_label		= $shortcode_args['marker_label'];
						else $marker_label		= 'title';
						if($marker_label=='place'){
							$marker_name		= mbw_get_board_item("fn_ext1");
							if(empty($marker_name)) $marker_name		= mbw_get_board_item("fn_title");
						}else{
							$marker_name		= mbw_get_board_item("fn_title");
						}
						if(empty($marker_name)) $marker_name		= 'marker';
						$category1			= mbw_get_board_item('fn_category1');
						$template_start		.= 'addMapMarker("'.$marker_name.'","'.$mode.'","'.$category1.'");';
					}
				$template_start		.= '};jQuery(document).ready(function() {map_init();});';

			$template_start		.= '</script>';
			
		}
		return $template_start;
	}
}







if(!function_exists('mbw_get_view_template')){
	function mbw_get_view_template($data,$tag=null,$echo=true){
		global $mstore,$mb_tags;

		if($tag==null)
			$tag					= array("t_tr"=>"tr","t_th"=>"th","t_td"=>"td");
			
		$data					= mbw_init_item_data("view",$data,$tag);
		
		$template_start		= "";
		$template_end		= "";
		
		if(!empty($data["tpl"]) && $data["tpl"]!="item"){
			$template_start	= mbw_get_extension_template($data);		
		}else{

			if(!empty($mb_tags)){
				if($mb_tags[count($mb_tags)-1]=="table"){
					if(!empty($tag["t_th"]))  $template_start	= '<'.$tag["t_th"].' scope="row"'.$data["th_class"].__STYLE($data["th_style"]).'><span>'.$data["name"].'</span></'.$tag["t_th"].'>';		
					if(!empty($tag["t_td"])){
						if(empty($data["colspan"]))
							$template_start	.= '<'.$tag["t_td"].$data["td_class"].__STYLE($data["td_style"]).'>';
						else
							$template_start	= '<'.$tag["t_td"].$data["td_class"].__STYLE($data["td_style"]).' colspan="'.$data["colspan"].'">';		

						$template_end	= '</'.$tag["t_td"].'>';
					}
				}
			}
			if(!empty($data["link"])){
				if(strpos($data["link"],'fn_')===0){
					if(mbw_get_board_item($data["link"])!="") $link_url		= mbw_get_board_item($data["link"]);
					else $link_url		= mbw_get_url(array('vid'=>mbw_get_board_item('fn_pid')));			
				}else if($data["link"]=="view"){
					if(!empty($data["link_url"])){
						if($data["link_url"]=="vid"){
							$link_url		= mbw_get_url(array('vid'=>mbw_get_board_item('fn_pid')),"","");
						}else{
							$link_url		= mbw_get_url(array('vid'=>mbw_get_board_item('fn_pid')),$data["link_url"],"");
						}
					}else{
						$link_url		= mbw_get_url(array('vid'=>mbw_get_board_item('fn_pid')));
					}
				}else if($data["link"]=="post_id" && !empty($data["link_id"])){
					$link_url		= mbw_get_url(array('vid'=>mbw_get_board_item('fn_pid')),get_permalink($data["link_id"]));
				}else if(mbw_get_option($data["link"])!=""){
					$link_url		= mbw_get_url(array('vid'=>mbw_get_board_item('fn_pid')),mbw_check_permalink(mbw_get_option($data["link"])));
				}else{
					$link_url		= $data["link"];
				}
				if(!empty($data["link_target"])){
					$template_start	.= '<a href="'.$link_url.'" target="'.$data["link_target"].'" title="'.$data["name"].'">';
				}else{
					$template_start	.= '<a href="'.$link_url.'" title="'.$data["name"].'">';
				}
				$template_end	= '</a>'.$template_end;
			}

			if(isset($data["display_check"])) $data	= mbw_is_display_item($data);
			
			if($data["display"]=="hide" || $data["type"]=="hide"){
				$data["tr_style"]			= $data["tr_style"].";display:none;";
				$data["display"]		= "hide";
			}else if($data["display"]=="none" || $data["type"]=="none"){
				return ;
			}

			if(!empty($data['add_start_html'])) $template_start	.= $data['add_start_html'];
			if(!empty($data["type"])){
				if($data["type"]=='title'){					
					$template_start	.= '<div class="view-td-titlebox">';
						$url		= mbw_get_url(array('vid'=>mbw_get_board_item('fn_pid')),'','');
						$template_start	.= '<div class="pull-left"><span class="view-td-title">'.$data["value"].' </span></div><div class="pull-right"><span class="view-td-hit">'.__MW("W_HIT").' '.mbw_get_board_item("fn_hit").' </span><span class="view-td-count">'.__MW("W_VOTE_GOOD").' '.mbw_get_board_item("fn_vote_good_count").'</span></div>';
						$template_start	.= '<div class="clear"></div>';
					$template_start	.= '</div>';
					$template_start	.= '<div class="view-td-userdatebox">';
						if(function_exists('mbw_board_date_format1')){
							$reg_date			= mbw_board_date_format1(mbw_get_board_item('fn_reg_date'),"view");
						}else{
							$reg_date			= substr(mbw_get_board_item('fn_reg_date'),0,-3);
						}
						$template_start	.= '<div class="pull-left"><span class="view-td-user">'.mbw_get_board_item("fn_user_name").'</span></div><div class="pull-right"><span class="view-td-date">'.$reg_date.'</span></div>';
						$template_start	.= '<div class="clear"></div>';
					$template_start	.= '</div>';

				}else if($data["type"]=='title2'){
					$template_start	.= '<div class="view-td2-titlebox">';		
						$template_start	.= '<div class="view-td2-title"><span>'.$data["value"].' </span></div>';
						if(function_exists('mbw_board_date_format1')){
							$reg_date			= mbw_board_date_format1(mbw_get_board_item('fn_reg_date'),"view");
						}else{
							$reg_date			= substr(mbw_get_board_item('fn_reg_date'),0,-3);
						}
						$w_hit	= __MW("W_HIT");
						if($w_hit=='조회') $w_hit	= '조회수';
						$template_start	.= '<div><span class="view-td2-user">'.mbw_get_board_item("fn_user_name").'</span><span class="view-td-hit view-td2-hit"><span style="padding-right:7px;">'.$w_hit.'</span>'.mbw_get_board_item("fn_hit").' </span><div class="pull-right"><span class="view-td2-date">'.$reg_date.'</span></div></div>';	
						$template_start	.= '<div class="clear"></div>';
					$template_start	.= '</div>';
				}else if($data["type"]=='content'){
					$template_start	.= $data["value"];
				}else if($data["type"]=='category1'){				
					$category_data		= mbw_get_board_option("fn_category_data");
					if(empty($category_data)) return;

					if(mbw_get_board_item("fn_category1")!=""){
						$template_start	.= '<span>'.mbw_get_board_item("fn_category1").'</span>';
					}
					if(mbw_get_board_item("fn_category2")!=""){
						$template_start	.= '<span> &gt; '.mbw_get_board_item("fn_category2").'</span>';
					}
					if(mbw_get_board_item("fn_category3")!=""){
						$template_start	.= '<span> &gt; '.mbw_get_board_item("fn_category3").'</span>';
					}
				}else if($data["type"]=='date'){
					$template_start	.= '<span>'.$data["value"].'</span>';
				}else{
					$item_template	= mbw_get_item_template("view",$data);
					if($item_template=="") return;
					$template_start	.= $item_template;
				}
			}else{
				$template_start	.= '<span>'.$data["value"].'</span>';
			}

			if(!empty($data["tooltip"])) $template_start	.= mbw_get_tooltip_template($data["tooltip"]);
			if(!empty($data['add_middle_html'])) $template_start	.= $data['add_middle_html'];
			if(!empty($data['description'])) $template_start	.= '<span class="mb-description">'.$data['description'].'</span>';
			if(!empty($data['add_end_html'])) $template_end		= $data['add_end_html'].$template_end;
			
			if(!empty($mb_tags)){
				if($mb_tags[count($mb_tags)-1]=="table"){
					$template_start		= '<'.$tag['t_tr'].' id="'.mbw_get_id_prefix().'tr_'.$data["item_name"].'"'.$data['tr_class'].__STYLE($data['tr_style']).'>'.$template_start.$template_end.'</'.$tag['t_tr'].'>';
				}
			}
		}
		if($echo) echo $template_start;
		else return $template_start;
	}
}

if(!function_exists('mbw_get_comment_template')){
	function mbw_get_comment_template($data,$tag=null,$echo=true,$action="list"){
		global $mstore,$mb_fields,$mb_tags;	
		$board_name	= $mstore->get_board_name();

		if(!empty($action)) mbw_set_param("board_action",$action);

		if($tag==null)
			$tag					= array("t_tr"=>"tr","t_th"=>"th","t_td"=>"td");
			
		if(empty($data["type"])){
			if($action=="list") $data["type"]		= "";
			else $data["type"]		= "text";
		}
		$data					= mbw_init_item_data("comment",$data,$tag);	
		
		$template_start		= "";
		$template_end		= "";

		if(!empty($data["tpl"]) && $data["tpl"]!="item"){
			$template_start	= mbw_get_extension_template($data);
		}else{
			
			if(isset($data["display_check"])) $data	= mbw_is_display_item($data);
			
			if($data["display"]=="hide" || $data["type"]=="hide"){
				$data["tr_style"]			= $data["tr_style"].";display:none;";
				$data["display"]		= "hide";
			}else if($data["display"]=="none" || $data["type"]=="none"){
				return ;
			}

			$required_text		= "";
			if(isset($data["required"])) $required_text		= $data["required"];

			if(!empty($mb_tags)){
				if($mb_tags[count($mb_tags)-1]=="table"){
					if(!empty($tag["t_th"]))  $template_start		= '<'.$tag["t_th"].' scope="row"'.$data["th_class"].__STYLE($data["th_style"]).'><label for="'.$data["item_id"].'">'.$data["name"].$required_text.'</label></'.$tag["t_th"].'>';
					
					if(!empty($tag["t_td"])){
						if(empty($data["colspan"]))
							$template_start	.= '<'.$tag["t_td"].$data["td_class"].__STYLE($data["td_style"]).'>';
						else
							$template_start	= '<'.$tag["t_td"].$data["td_class"].__STYLE($data["td_style"]).' colspan="'.$data["colspan"].'">';

						$template_end		= '</'.$tag["t_td"].'>';
					}
				}
			}

			if(!empty($data["link"])){
				$link_url			= $data["link"];
				$template_start	.= '<a href="'.$link_url.'" title="'.$data["name"].'">';
				$template_end	= '</a>'.$template_end;
			}
			if(!empty($data['add_start_html'])) $template_start	.= $data['add_start_html'];
			if(!empty($data["type"])){

				if($action=="list"){
					if($data["type"]=='cl_name_date'){
						$template_start	.= "<div style=\"\" class=\"cl_name_item\"><span".$data["ext"].__STYLE($data["style"]).">'+reply_sign+value['".$mb_fields["select_comment"][$data["field"]]."']+'</span></div>";
					}else if($data["type"]=='cl_content'){
						$template_start	.= "<div".$data["ext"].__STYLE($data["style"].";float:left;").">'+value['".$mb_fields["select_comment"][$data["field"]]."']+'<br><span class=\"cmt-date\" style=\"margin-left:0px !important;\">'+value['".$mb_fields["select_comment"]["fn_reg_date"]."']+'</span></div><div class=\"clear\"></div>";
					}else{
						$template_start	.= "<div".$data["ext"].__STYLE($data["style"].";float:left;").">'+value['".$mb_fields["select_comment"][$data["field"]]."']+'</div><div class=\"clear\"></div>";
					}
				}else{
					if($data["type"]=='cw_name'){
						$secret_checked		= "";
						if(mbw_get_comment_item("fn_is_secret")=="1") $secret_checked		= " checked ";
						$template_start	.= '<span  style="float:left";>'.mbw_get_user("fn_user_name").'</span><span style="float:right;"><label><input title="'.__MW("W_SECRET").'" type="checkbox" name="'.mbw_set_form_name("is_secret").'" value="1" '.$secret_checked.'/>'.__MW("W_SECRET").'</label></span>';
					}else if($data["type"]=='cw_content'){
						$template_start	.= '<textarea'.$data["ext"].__STYLE($data["style"]).' name="'.mbw_set_form_name($data["item_name"]).'" id="'.$data["item_id"].'">'.$data["value"].'</textarea>';
						$send_action				= "'".$action."'";
						if($action=="reply") $send_action		= "\'".$action."\'";
					}else{
						$item_template	= mbw_get_item_template("comment",$data);
						if($item_template=="") return;
						$template_start	.= $item_template;
					}
				}				
			}else{
				$template_start	.= "<div".$data["ext"].__STYLE($data["style"]).">'+value['".$mb_fields["select_comment"][$data["field"]]."']+'</div>";
			}

			if(!empty($data["tooltip"])) $template_start	.= mbw_get_tooltip_template($data["tooltip"]);
			if(!empty($data['add_middle_html'])) $template_start	.= $data['add_middle_html'];
			if(!empty($data['description'])) $template_start	.= '<span class="mb-description">'.$data['description'].'</span>';
			if(!empty($data['add_end_html'])) $template_end		= $data['add_end_html'].$template_end;
			


			if(!empty($mb_tags)){
				if($mb_tags[count($mb_tags)-1]=="table"){
					$template_start		= '<'.$tag['t_tr'].$data['tr_class'].__STYLE($data['tr_style']).'>'.$template_start;
					$template_start	.= $template_end.'</'.$tag['t_tr'].'>';
				}
			}
		}
		if($echo) echo $template_start;
		else return $template_start;
	}
}
?>