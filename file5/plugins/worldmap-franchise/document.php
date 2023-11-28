<div id="kboard-worldmap-franchise-document">
	<?php if($board->isAdmin() && (!kboard_worldmap_franchise_google_maps_api_key($board) || !kboard_worldmap_franchise_google_geocoding_api_key($board))):?>
	<div class="kboard-worldmap-franchise-google-api-key">
		<p>※ 구글 API 키가 등록되어 있지 않습니다.</p>
		<p>구글 지도 자바스크립트 API, 구글 지오코딩 API 를 사용할 수 있어야 합니다.</p>
		<p>워드프레스 관리자 -> KBoard -> 게시판 목록 -> 게시판 선택 -> 확장설정 페이지에서 구글 API 키를 등록해주세요.</p>
		<p>또는 <code>kboard_worldmap_franchise_google_maps_api_key</code>, <code>kboard_worldmap_franchise_google_geocoding_api_key</code> 필터를 사용해서 구글 API 키를 입력하거나 편집할 수 있습니다.</p>
	</div>
	<?php endif?>
	
	<input type="hidden" name="kboard_worldmap_franchise_map_location_lat" value="<?php echo esc_attr($content->option->map_location_lat)?>">
	<input type="hidden" name="kboard_worldmap_franchise_map_location_lng" value="<?php echo esc_attr($content->option->map_location_lng)?>">
	<input type="hidden" name="kboard_worldmap_franchise_current_category" value="<?php echo !$board->meta->always_view_list ? esc_attr($content->category1) : esc_attr(kboard_category1())?>">
	<input type="hidden" name="kboard_worldmap_franchise_current_category2" value="<?php echo !$board->meta->always_view_list ? esc_attr($content->category2) : esc_attr(kboard_category2())?>">
	<input type="hidden" name="kboard_worldmap_franchise_default_location" value="<?php echo esc_attr(kboard_worldmap_franchise_default_location($board))?>">
	<input type="hidden" name="kboard_worldmap_franchise_default_zoom" value="<?php echo esc_attr(kboard_worldmap_franchise_default_zoom($board))?>">
	<input type="hidden" name="kboard_worldmap_franchise_skin_path" value="<?php echo esc_attr($skin_path)?>">
	
	<div class="kboard-header kboard-document-header">
		<div class="kboard-category category-pc">
			<ul class="kboard-category-list">
				<li<?php if(!kboard_category1()):?> class="kboard-category-selected"<?php endif?>><a href="<?php echo $url->set('category1', '')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>"><?php echo __('All', 'kboard')?></a></li>
				<?php foreach(kboard_worldmap_franchise_branch_list() as $key=>$item): if(!$board->getCategoryCount(array('category1'=>$key))) continue?>
				<li<?php if(kboard_category1() == $key):?> class="kboard-category-selected"<?php endif?>><a href="<?php echo $url->set('category1', $key)->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>"><?php echo $item['name']?></a></li>
				<?php endforeach?>
			</ul>
			
			<?php if($board->use_category == 'yes' && $board->initCategory2()):?>
				<ul class="kboard-category-list">
					<li<?php if(!kboard_category2()):?> class="kboard-category-selected"<?php endif?>><a href="<?php echo $url->set('category2', '')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>"><?php echo __('All', 'kboard')?></a></li>
					<?php while($board->hasNextCategory()):?>
					<li<?php if(kboard_category2() == $board->currentCategory()):?> class="kboard-category-selected"<?php endif?>>
						<a href="<?php echo $url->set('category2', $board->currentCategory())->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->toString()?>"><?php echo $board->currentCategory()?></a>
					</li>
					<?php endwhile?>
				</ul>
			<?php endif?>
		</div>
		<div class="kboard-category category-mobile">
			<form method="get" action="<?php echo esc_url($url->toString())?>">
				<?php echo $url->set('pageid', '1')->set('category1', '')->set('category2', '')->set('target', '')->set('keyword', '')->set('mod', 'list')->toInput()?>
				<select name="category1" onchange="this.form.submit();">
					<option value=""><?php echo __('All', 'kboard')?></option>
					<?php foreach(kboard_worldmap_franchise_branch_list() as $key=>$item): if(!$board->getCategoryCount(array('category1'=>$key))) continue?>
					<option value="<?php echo $key?>"<?php if(kboard_category1() == $key):?> selected<?php endif?>><?php echo $item['name']?></option>
					<?php endforeach?>
				</select>
				
				<?php if($board->use_category == 'yes' && $board->initCategory2()):?>
					<select name="category2" onchange="this.form.submit();">
						<option value=""><?php echo __('All', 'kboard')?></option>
						<?php while($board->hasNextCategory()):?>
						<option value="<?php echo $board->currentCategory()?>"<?php if(kboard_category2() == $board->currentCategory()):?> selected<?php endif?>><?php echo $board->currentCategory()?></option>
						<?php endwhile?>
					</select>
				<?php endif?>
			</form>
		</div>
		
		<div id="kboard-worldmap-franchise-canvas" class="kboard-map"></div>
		
		<div class="kboard-search">
			<form method="get" action="<?php echo esc_url($url->toString())?>">
				<?php echo $url->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->toInput()?>
				<select name="target">
					<option value=""><?php echo __('Title', 'kboard')?>+<?php echo __('Content', 'kboard')?></option>
					<option value="kboard_option_address"<?php if(kboard_target() == 'kboard_option_address'):?> selected<?php endif?>><?php echo __('Address', 'kboard-worldmap-franchise')?></option>
				</select>
				<input type="text" name="keyword" value="<?php echo esc_attr(kboard_keyword())?>">
				<button type="submit" class="kboard-worldmap-franchise-button-small"><?php echo __('Search', 'kboard')?></button>
			</form>
		</div>
	</div>
	
	<div class="kboard-document-wrap" itemscope itemtype="http://schema.org/Article">
		<div class="kboard-detail">
			<div class="detail-attr detail-date">
				<div class="detail-name"><?php echo __('Date', 'kboard')?></div>
				<div class="detail-value"><?php echo date("Y-m-d H:i", strtotime($content->date))?></div>
			</div>
			<div class="detail-attr detail-view">
				<div class="detail-name"><?php echo __('Views', 'kboard')?></div>
				<div class="detail-value"><?php echo $content->view?></div>
			</div>
		</div>
		
		<div class="kboard-worldmap-franchise-info <?php if($content->getThumbnail(400, 400)):?>add-franchise-thumbnail<?php endif?>">
			<div class="kboard-worldmap-franchise-info-wrap">
				<?php if($content->getThumbnail(400, 400)):?>
				<div class="kboard-franchise-thumbnail">
					<img src="<?php echo esc_url($content->getThumbnail(400, 400))?>" alt="">
				</div>
				<?php endif?>
				<div class="kboard-franchise-attr-wrap">
					<h1 class="kboard-franchise-title"><?php echo $content->title?></h1>
				</div>
				<?php if($content->category1):?>
				<div class="kboard-franchise-attr-wrap kboard-franchise-area">
					<div class="kboard-franchise-attr"><?php echo __('Area', 'kboard-worldmap-franchise')?> :</div>
					<div class="kboard-franchise-value"><?php echo kboard_worldmap_franchise_branch($content->category1)?></div>
				</div>
				<?php endif?>
				<?php echo $content->getDocumentOptionsHTML()?>
			</div>
		</div>
		
		<?php if($content->getThumbnail(600, 600)):?>
		<div class="mobile-thumbnail">
			<img src="<?php echo esc_url($content->getThumbnail(600, 600))?>" alt="">
		</div>
		<?php endif?>
		
		<div class="kboard-content" itemprop="description">
			<div class="content-view">
				<?php echo $content->content?>
			</div>
		</div>
		
		<?php if($content->isAttached()):?>
			<?php foreach($content->getAttachmentList() as $key=>$attach):?>
				<div class="kboard-attach">
					<?php echo __('Attachment', 'kboard')?> : <button type="button" class="kboard-button-download" onclick="window.location.href='<?php echo esc_url($url->getDownloadURLWithAttach($content->uid, $key))?>'" title="<?php echo sprintf(__('Download %s', 'kboard'), $attach[1])?>"><?php echo $attach[1]?></button>
				</div>
			<?php endforeach?>
		<?php endif?>
	</div>
	
	<?php if($content->visibleComments()):?>
	<div class="kboard-comments-area"><?php echo $board->buildComment($content->uid)?></div>
	<?php endif?>
	
	<div class="kboard-control">
		<div class="left">
			<a href="<?php echo esc_url($url->getBoardList())?>" class="kboard-worldmap-franchise-button-small"><?php echo __('List', 'kboard')?></a>
			<!--
			<a href="<?php echo $url->getDocumentURLWithUID($content->getPrevUID())?>" class="kboard-worldmap-franchise-button-small"><?php echo __('Prev', 'kboard')?></a>
			<a href="<?php echo $url->getDocumentURLWithUID($content->getNextUID())?>" class="kboard-worldmap-franchise-button-small"><?php echo __('Next', 'kboard')?></a>
			-->
		</div>
		<div class="right">
			<?php if($content->isEditor() || $board->permission_write=='all'):?>
			<a href="<?php echo esc_url($url->getContentEditor($content->uid))?>" class="kboard-worldmap-franchise-button-small"><?php echo __('Edit Place', 'kboard-worldmap-franchise')?></a>
			<a href="<?php echo esc_url($url->getContentRemove($content->uid))?>" class="kboard-worldmap-franchise-button-small" onclick="return confirm('<?php echo __('Are you sure you want to delete?', 'kboard')?>');"><?php echo __('Delete Place', 'kboard-worldmap-franchise')?></a>
			<?php endif?>
		</div>
	</div>
	
	<?php if($board->contribution() && !$board->meta->always_view_list):?>
	<div class="kboard-worldmap-franchise-poweredby">
		<a href="https://www.cosmosfarm.com/products/kboard" onclick="window.open(this.href);return false;" title="<?php echo __('KBoard is the best community software available for WordPress', 'kboard')?>">Powered by KBoard</a>
	</div>
	<?php endif?>
</div>

<?php
if(!$board->meta->always_view_list){
	?>
	<script>
	var worldmap_franchise = {
		request_uri:'<?php echo $_SERVER['REQUEST_URI']?>',
		security:'<?php echo wp_create_nonce('kboard_worldmap_franchise_get_gps_list')?>'
	}
	</script>
	<?php
	wp_enqueue_script('kboard-worldmap-franchise-script', "{$skin_path}/google-maps.js", array(), KBOARD_VERSION, true);
	wp_enqueue_script('kboard-worldmap-franchise-google-maps', 'https://maps.googleapis.com/maps/api/js?key='.kboard_worldmap_franchise_google_maps_api_key($board).'&language='.get_locale().'&callback=kboard_worldmap_franchise_init_map', array(), KBOARD_VERSION, true);
	wp_enqueue_script('kboard-worldmap-franchise-marker-clusterer', "{$skin_path}/markerclusterer.js", array(), '1.0.3', true);
}
?>