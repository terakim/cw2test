<?php
	if(mbw_is_admin_page()){
		require(MBW_SKIN_PATH."list-admin.php");
		return;
	}
	if(mbw_get_vars("device_type")=="desktop") loadScript(MBW_SKIN_URL."js/jquery.nicescroll.min.js");
	$model_data		= mbw_get_model("list");			
	$list_model		= mbw_json_decode($model_data);
	$list_data			= mbw_get_list_setup_data($list_model);
?>
<script type="text/javascript">
function selectTabMenu(obj,category,name,idx){
	if(typeof(idx)==='undefined' || idx =='') idx	= "1";
	jQuery('.tab-menu-on').removeClass("tab-menu-on").addClass("tab-menu-off");
	jQuery(obj).removeClass("tab-menu-off").addClass("tab-menu-on");
	if(jQuery("input[name=category"+idx+"]")) jQuery("input[name=category"+idx+"]").val(category);
	sendListTemplateData({"board_name":name,"category":category});
}
function sendListTemplateData(data){
	listTemplateCheck	= true;
	if(typeof(data)==='undefined') data = {};
	if(typeof(data.board_name)==='undefined' || data.board_name==='undefined') board_name = mb_options["board_name"];	
	else board_name = data.board_name;
	if(typeof(data.mode)==='undefined' || data.mode==='undefined') mode = "";
	else mode = data.mode;
	if(typeof(data.page)==='undefined' || data.page==='undefined') page = 1;
	else page = data.page;	
	
	listTemplateBoard				= board_name;
	listTemplateMode				= mode;
	listTemplateAction				= document.forms[listTemplateBoard+'_form_board_list'].board_action.value;
	document.forms[listTemplateBoard+'_form_board_list'].board_action.value = "load";
	var params		= jQuery('#'+listTemplateBoard+'_form_board_search').serialize();
	params			+= "&"+jQuery('#'+listTemplateBoard+'_form_board_list').serialize();

	if(typeof(data.category)!=='undefined'){
		if(typeof(data.idx)==='undefined') idx = "1";
		else idx = data.idx;
		params		+= "&category"+idx+"="+encodeURIComponent(data.category);
	}
	if(jQuery(".mb-map-box input[name=search_text]").length>0 && jQuery(".mb-map-box input[name=search_text]").val()!=''){
		params		+= "&search_text="+encodeURIComponent(jQuery(".mb-map-box input[name=search_text]").val());
	}
	if(typeof(data.page_type)!=='undefined'){
		params		+= "&page_type="+encodeURIComponent(data.page_type);
	}		
	params			+= "&board_page="+page;	
	sendDataRequest2(mb_urls["template_api"], params, sendListTemplateDataHandler);			
}

function sendListTemplateDataHandler(response, state){		
	document.forms[listTemplateBoard+'_form_board_list'].board_action.value		= listTemplateAction;
	if(listTemplateCheck){
		if(response.state == "success"){
			if(listTemplateMode!="append"){
				jQuery("#"+listTemplateBoard+"_board_body>tr").remove();
				jQuery("#"+listTemplateBoard+"_board_body>div").remove();
			}
			jQuery('.mb-list-info-box div').html(<?php echo "'".__MM("MSG_SEARCH_RESULT",'<span class="mb-search-count">\'+response.data.options.total_count+\'</span>')."'";?>);

			if(response.data["body"]) jQuery("#"+listTemplateBoard+"_board_body").append(response.data["body"]);
			if(response.data["markers"] && response.data["markers"]!=""){					
				setMarkers(JSON.parse(response.data["markers"]),'insert');					
			}
			setMapImageItemEvent();
		}else{
			showAlertPopup(response);
		}
		listTemplateCheck		= false;
	}
}
function sendSearchData(data){
	var search_url					= mb_urls["search"];	
	var params						= jQuery('#'+mb_options["board_name"]+'_form_board_search').serialize();
	if(typeof(data)!=='undefined') params					= params+"&"+data;		
	if(params.indexOf('category')!=-1 && search_url.indexOf('category')!=-1) {
		search_url		= search_url.replace(/(category)(\d{1})=/g,"category_old$2=");
	}
	if(jQuery(".mb-map-box input[name=search_text]").length>0 && jQuery(".mb-map-box input[name=search_text]").val()!=''){
		params		+= "&search_text="+encodeURIComponent(jQuery(".mb-map-box input[name=search_text]").val());
	}
	search_url						= search_url+"&"+params;		
	moveURL(search_url);
}
function showDeleteConfirm(){	
	var check_count	= jQuery(".mb-board input[name='check_array[]']").filter(":checked").length;
	if(check_count > 0) {
		showConfirmPopup(check_count+"<?php echo __MM('MSG_MULTI_DELETE_CONFIRM')?>", {"board_action":"multi_delete"}, sendBoardListData);
	}else{
		showAlertPopup({"code":"1000","message":"<?php echo __MM('MSG_DELETE_SELECT_EMPTY')?>"});
	}
}
function showMoveConfirm(type){	
	var check_count	= jQuery(".mb-board input[name='check_array[]']").filter(":checked").length;
	if(check_count > 0) {
		if(type=="multi_copy"){
			showConfirmPopup(check_count+"<?php echo __MM('MSG_MULTI_COPY_CONFIRM')?>", {"board_action":type}, sendBoardListData);
		}else if(type=="multi_move"){
			showConfirmPopup(check_count+"<?php echo __MM('MSG_MULTI_MOVE_CONFIRM')?>", {"board_action":type}, sendBoardListData);
		}		
	}else{
		if(type=="multi_copy"){
			showAlertPopup({"code":"1000","message":"<?php echo __MM('MSG_COPY_SELECT_EMPTY')?>"});
		}else if(type=="multi_move"){
			showAlertPopup({"code":"1000","message":"<?php echo __MM('MSG_MOVE_SELECT_EMPTY')?>"});
		}
	}
}

function sendBoardListData(args){	
	if(args.board_action=="multi_modify"){
		jQuery('#'+mb_options["board_name"]+'_form_board_list input[name=board_pid]').val(args.board_pid);
	}
	jQuery('#'+mb_options["board_name"]+'_form_board_list input[name=board_action]').val(args.board_action);
	sendFormDataRequest(jQuery('#'+mb_options["board_name"]+'_form_board_list'), mb_urls["board_api"], sendBoardListDataHandler);
}

function sendBoardListDataHandler(response, state)
{
	if(response.state == "success"){
		if(typeof(response.message)!=='undefined' && response.message!="") 
			alert(response.message);
		moveURL("reload");
	}else{			
		showAlertPopup(response);
	}
}
</script>
<?php
if(!empty($args['list_webzine_width'])){
	$list_webzine_width		= $args['list_webzine_width'];
	if(strpos($list_webzine_width,'px')===false) $list_webzine_width	.= "px";
}else $list_webzine_width		= '370px';
$device_type			= mbw_get_vars("device_type");
if(!empty($args[$device_type.'_list_map_height'])){
	$list_map_height		= intval($args[$device_type.'_list_map_height']);
}else if(!empty($args['list_map_height'])){
	$list_map_height		= intval($args['list_map_height']);
}else{
	if($device_type=="mobile"){
		$list_map_height		= '300';
	}else{
		$list_map_height		= 'auto';
	}
}
if(!empty($args['list_layout'])) $list_layout		= $args['list_layout'];
else $list_layout		= 'full';
$add_class1		= '';
if($list_layout=='full' && $device_type!="mobile"){
	$add_class1		= 'mb-map-full-layout';
}
?>
<div class="mb-full-height mb-style1 board-list mb-map-wrap">
	<div class="<?php echo $add_class1;?>">
		<div class="mb-full-height mb-map-box" style="width:calc(100% - <?php echo $list_webzine_width;?>);line-height:0;">
			<?php
				if($device_type=="mobile"){
					$map_height		= 'calc(100% - 26px);';
				}else{
					$map_height		= '100%';
				}
				echo mbw_get_skin_template("list",array("type"=>"skin_map","height"=>$map_height,"style"=>"visibility:hidden;position:relative;"));
			?>
			<div class="icon-map-list-open"><div></div></div>		
		</div>
		<div class="mb-full-height mb-list-box" style="width:<?php echo $list_webzine_width;?>;"><div>		
			
			<form name="<?php echo $mb_board_name;?>_form_board_search" id="<?php echo $mb_board_name;?>_form_board_search" method="post">
			<input type="hidden" name="board_name" value="<?php echo $mb_board_name?>" />
			<?php if(!empty($args["post_id"])){ ?>
	<input type="hidden" name="page_id" value="<?php echo $args["post_id"];?>" />
	<?php }else if(!empty($_REQUEST["page_id"])){ ?>
			<input type="hidden" name="page_id" value="<?php echo mbw_get_param("page_id");?>" />
			<?php } if(!empty($_REQUEST["order_by"])){ ?> 
			<input type="hidden" name="order_by" value="<?php echo mbw_get_param("order_by");?>" />
			<?php } if(!empty($_REQUEST["order_type"])){ ?> 
			<input type="hidden" name="order_type" value="<?php echo mbw_get_param("order_type");?>" />
			<?php } ?>
			<input type="hidden" name="mb_bounds_minx" id="mb_map_bounds_minx" value="" />
			<input type="hidden" name="mb_bounds_miny" id="mb_map_bounds_miny" value="" />
			<input type="hidden" name="mb_bounds_maxx" id="mb_map_bounds_maxx" value="" />
			<input type="hidden" name="mb_bounds_maxy" id="mb_map_bounds_maxy" value="" />
			<input type="hidden" name="mb_map_zoom" id="mb_map_zoom" value="" />
			<?php do_action('mbw_board_skin_search'); ?>
			<div class="list-head">			
				<?php
				echo '<div class="mb-category">';			
					echo mbw_get_category_template(mbw_get_board_option("fn_category_type"),mbw_get_board_option("fn_category_data"));
				echo '</div>';	
				?>	
				<div class="clear"></div>
			</div>
			<div class="mb-list-info-box"><div></div></div>
			</form>

			<?php do_action('mbw_board_skin_header'); ?>		
			<form name="<?php echo $mb_board_name;?>_form_board_list" id="<?php echo $mb_board_name;?>_form_board_list" method="post">	
			<input type="hidden" name="board_name" id="board_name" value="<?php echo $mb_board_name?>" />
			<input type="hidden" name="page_id" id="page_id" value="<?php echo mbw_get_param("page_id")?>" />
			<input type="hidden" name="list_type" id="list_type" value="<?php echo mbw_get_param("list_type")?>" />
			<input type="hidden" name="page" id="page" value="<?php echo mbw_get_param("page")?>" />
			<input type="hidden" name="mode" id="mode" value="list" />
			<input type="hidden" name="board_action" id="board_action" value="" />
			<input type="hidden" name="board_pid" id="board_pid" value="" />
			<?php echo mbw_create_nonce("form"); ?>

			<div class="main-style1" id="<?php echo $mb_board_name;?>_board_box">
				<table cellspacing="0" cellpadding="0" border="0" id="tbl_board_list" class="table table-list" style="position:initial;">
					<colgroup><?php echo $list_data["width"];?></colgroup>
					<tbody id="<?php echo $mb_board_name;?>_board_body"></tbody>
				</table>
			</div>
			<?php do_action('mbw_board_skin_form'); ?>
			</form>			
			<?php do_action('mbw_board_skin_footer'); ?>
		</div></div>
	</div>
</div>

<script type="text/javascript">
	var nMapHeight		= 0;
	var search_text		= '';
	function setLayoutSize(){
		var map_offset	= jQuery(".mb-full-height").offset().top;
		if(typeof(mb_options)!=='undefined' && typeof(mb_options["device_type"])!=='undefined' && mb_options["device_type"]=="mobile"){
			if(map_offset<250) map_offset		= 250;
		}
		<?php
			if($list_map_height=="auto"){
				echo 'nMapHeight		= jQuery(window).height()-map_offset;';
			}else{
				echo 'nMapHeight		= '.$list_map_height.';';
			}
		?>
		if(nMapHeight<200) nMapHeight = 200;
		jQuery(".mb-full-height").css("height",nMapHeight);
	}
	function setMapImageItemEvent(){
		<?php 
			if(!empty($args['image_hover_focus']) && $args['image_hover_focus']=='true'){
				$webzine_event		= 'touchstart mousedown mouseenter';
			}else{
				$webzine_event		= 'touchstart mousedown';
			}
		?>
		jQuery(".mb-list-box .webzine-image-box>div").on("<?php echo $webzine_event;?>",function(){
			var itemIndex		= parseInt(jQuery(this).closest("tr").attr("id").replace("mb_"+mb_options["board_name"]+"_tr_", ""));
			setMarkerItemFocus('enter',itemIndex);			
			jQuery(".mb-list-box tr").removeClass('mb-webzine-item-focus');
			jQuery(this).closest("tr").addClass('mb-webzine-item-focus');
		});
		jQuery(".mb-list-box .webzine-image-box>div").on("mouseenter",function() {
			var itemIndex		= parseInt(jQuery(this).closest("tr").attr("id").replace("mb_"+mb_options["board_name"]+"_tr_", ""));
			setMarkerItemFocus('hover',itemIndex);
		}).on("mouseleave",function() {
			var itemIndex		= parseInt(jQuery(this).closest("tr").attr("id").replace("mb_"+mb_options["board_name"]+"_tr_", ""));
			setMarkerItemFocus('leave',itemIndex);
		});

		jQuery(".mb-list-box").scrollTop(0);
		if(jQuery(".mb-map-box input[name=search_text]").length>0 && jQuery(".mb-map-box input[name=search_text]").val()!=''){
			if(search_text!=jQuery(".mb-map-box input[name=search_text]").val()){
				setMapCenter();
			}			
			search_text		= jQuery(".mb-map-box input[name=search_text]").val();
		}
		<?php if(mbw_get_vars("device_type")=="desktop") echo 'jQuery(".mb-list-box").getNiceScroll().resize();';?>
	}
	function checkMapListOpen(type){
		if(!jQuery('body').hasClass('mb-map-list-shown')) {
			jQuery('body').addClass('mb-map-list-shown');
		}else if(type!='open'){
			jQuery('body').removeClass('mb-map-list-shown');
		}
		setTimeout(function(){  setMapLayoutSize(); },500);
	}
	function initMapList(){
		setLayoutSize();		
		<?php if(mbw_get_vars("device_type")=="desktop") echo 'jQuery(".mb-list-box").niceScroll({});';?>
	}
	if(typeof jQuery != 'undefined') {
		jQuery(window).on("orientationchange resize",function(){
			setLayoutSize();
		});
		jQuery(document).ready(function() {
			jQuery('.icon-map-list-open, .icon-map-list-close').click(function(event) {
				checkMapListOpen('toggle');				
			});			
		});
		jQuery(".mb-list-box").scroll(function() {
			if (jQuery(".mb-list-box>div").height()<=(jQuery(".mb-list-box").scrollTop()+nMapHeight+120)) {				
				if(jQuery(".mb-list-box tr.mb-hide").length>0){
					jQuery(".mb-list-box tr.mb-hide:lt(100)").removeClass("mb-hide");
				}
			}
		});
		<?php
			if($list_layout=='full' && mbw_get_vars("device_type")!="mobile"){
				echo "jQuery('body').css('overflow-y','hidden');setTimeout(function() {jQuery(window).scrollTop(0);},1000);	";
			}
		?>				
	}
</script>