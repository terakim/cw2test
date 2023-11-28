<div id="kboard-worldmap-franchise-list">
	<?php if($board->isAdmin() && (!kboard_worldmap_franchise_google_maps_api_key($board) || !kboard_worldmap_franchise_google_geocoding_api_key($board))):?>
	<div class="kboard-worldmap-franchise-google-api-key">
		<p>※ 구글 API 키가 등록되어 있지 않습니다.</p>
		<p>구글 지도 자바스크립트 API, 구글 지오코딩 API 를 사용할 수 있어야 합니다.</p>
		<p>워드프레스 관리자 -> KBoard -> 게시판 목록 -> 게시판 선택 -> 확장설정 페이지에서 구글 API 키를 등록해주세요.</p>
		<p>또는 <code>kboard_worldmap_franchise_google_maps_api_key</code>, <code>kboard_worldmap_franchise_google_geocoding_api_key</code> 필터를 사용해서 구글 API 키를 입력하거나 편집할 수 있습니다.</p>
	</div>
	<?php endif?>
	
	<?php if(kboard_keyword()):?>
		<?php
		$geocode = kboard_worldmap_franchise_geocode_with_keyword(kboard_keyword(), $board);
		if($geocode->lat && $geocode->lng):
		?>
		<input type="hidden" name="kboard_worldmap_franchise_map_location_lat" value="<?php echo esc_attr($geocode->lat)?>">
		<input type="hidden" name="kboard_worldmap_franchise_map_location_lng" value="<?php echo esc_attr($geocode->lng)?>">
		<?php endif?>
	<?php endif?>
	<input type="hidden" name="kboard_worldmap_franchise_current_category" value="<?php echo esc_attr(kboard_category1())?>">
	<input type="hidden" name="kboard_worldmap_franchise_current_category2" value="<?php echo esc_attr(kboard_category2())?>">
	<input type="hidden" name="kboard_worldmap_franchise_default_location" value="<?php echo esc_attr(kboard_worldmap_franchise_default_location($board))?>">
	<input type="hidden" name="kboard_worldmap_franchise_default_zoom" value="<?php echo esc_attr(kboard_worldmap_franchise_default_zoom($board))?>">
	<input type="hidden" name="kboard_worldmap_franchise_skin_path" value="<?php echo esc_attr($skin_path)?>">
	
	<?php if(kboard_mod() != 'document'):?>
	<div class="kboard-header kboard-list-header">
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
			<form method="get" action="<?php echo $url->toString()?>">
				<?php echo $url->set('pageid', '1')->set('category1', '')->set('category2', '')->set('target', '')->set('keyword', '')->set('mod', 'list')->toInput()?>
				<select name="category1" onchange="this.form.submit()">
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
	<?php endif?>
	
	<!-- 리스트 시작 -->
	<ul class="kboard-list">
		<?php while($content = $list->hasNextNotice()):?>
		<li class="kboard-list-item<?php if($content->uid == kboard_uid()):?> kboard-list-selected<?php endif?>">
			<a href="<?php echo esc_url($url->getDocumentURLWithUID($content->uid))?>" title="<?php echo esc_attr($content->title)?>">
				<div class="kboard-worldmap-franchise-thumbnail">
				<?php if(esc_url($content->getThumbnail(600, 600))):?>
					<img src="<?php echo esc_url($content->getThumbnail(600, 600))?>" alt="<?php echo esc_attr($content->title)?>">
				<?php else:?>
					<div class="kboard-worldmap-franchise-no-image"></div>
				<?php endif?>
				</div>
			</a>
			<div class="kboard-worldmap-franchise-wrap">
				<a href="<?php echo esc_url($url->getDocumentURLWithUID($content->uid))?>" title="<?php echo esc_attr($content->getUserName())?>">
					<div class="kboard-worldmap-franchise-title">
						[<?php echo __('Notice', 'kboard')?>]
						<?php if($content->isNew()):?><span class="kboard-worldmap-franchise-new-notify">New</span><?php endif?>
						<?php if($content->secret):?><img src="<?php echo $skin_path?>/images/icon-lock.png" alt="<?php echo __('Secret', 'kboard')?>"><?php endif?>
						<?php echo $content->title?>
					</div>
				</a>
				
				<?php if($content->category1):?>
				<div class="kboard-worldmap-franchise-area"><?php echo __('Area', 'kboard-worldmap-franchise')?> : <?php echo kboard_worldmap_franchise_branch($content->category1)?></div>
				<?php endif?>
				
				<?php echo $content->getDocumentOptionsHTML()?>
			</div>
		</li>
		<?php endwhile?>
		<?php while($content = $list->hasNext()):?>
		<li class="kboard-list-item<?php if($content->uid == kboard_uid()):?> kboard-list-selected<?php endif?>">
			<a href="<?php echo esc_url($url->getDocumentURLWithUID($content->uid))?>" title="<?php echo esc_attr($content->title)?>">
				<div class="kboard-worldmap-franchise-thumbnail">
				<?php if(esc_url($content->getThumbnail(600, 600))):?>
					<img src="<?php echo esc_url($content->getThumbnail(600, 600))?>" alt="<?php echo esc_attr($content->title)?>">
				<?php else:?>
					<div class="kboard-worldmap-franchise-no-image"></div>
				<?php endif?>
				</div>
			</a>
			<div class="kboard-worldmap-franchise-wrap">
				<a href="<?php echo esc_url($url->getDocumentURLWithUID($content->uid))?>" title="<?php echo esc_attr($content->getUserName())?>">
					<div class="kboard-worldmap-franchise-title">
						<?php if($content->isNew()):?><span class="kboard-worldmap-franchise-new-notify">New</span><?php endif?>
						<?php if($content->secret):?><img src="<?php echo $skin_path?>/images/icon-lock.png" alt="<?php echo __('Secret', 'kboard')?>"><?php endif?>
						<?php echo $content->title?>
					</div>
				</a>
				
				<?php if($content->category1):?>
				<div class="kboard-worldmap-franchise-area"><?php echo __('Area', 'kboard-worldmap-franchise')?> : <?php echo kboard_worldmap_franchise_branch($content->category1)?></div>
				<?php endif?>
				
				<?php echo $content->getDocumentOptionsHTML()?>
			</div>
		</li>
		<?php endwhile?>
	</ul>
	<!-- 리스트 끝 -->
	
	<!-- 페이징 시작 -->
	<div class="kboard-pagination">
		<ul class="kboard-pagination-pages">
			<?php echo kboard_pagination($list->page, $list->total, $list->rpp)?>
		</ul>
	</div>
	<!-- 페이징 끝 -->
	
	<?php if($board->isWriter()):?>
	<!-- 버튼 시작 -->
	<div class="kboard-control">
		<a href="<?php echo esc_url($url->getContentEditor())?>" class="kboard-worldmap-franchise-button-small"><?php echo __('Register Place', 'kboard-worldmap-franchise')?></a>
	</div>
	<!-- 버튼 끝 -->
	<?php endif?>
	
	<?php if($board->contribution()):?>
	<div class="kboard-worldmap-franchise-poweredby">
		<a href="https://www.cosmosfarm.com/products/kboard" onclick="window.open(this.href);return false;" title="<?php echo __('KBoard is the best community software available for WordPress', 'kboard')?>">Powered by KBoard</a>
	</div>
	<?php endif?>
</div>

<script>
var worldmap_franchise = {
	request_uri:'<?php echo esc_js($_SERVER['REQUEST_URI'])?>',
	security:'<?php echo wp_create_nonce('kboard_worldmap_franchise_get_gps_list')?>'
}
</script>

<?php
wp_enqueue_script('kboard-worldmap-franchise-script', "{$skin_path}/google-maps.js", array(), KBOARD_VERSION, true);
wp_enqueue_script('kboard-worldmap-franchise-google-maps', 'https://maps.googleapis.com/maps/api/js?key='.kboard_worldmap_franchise_google_maps_api_key($board).'&language='.get_locale().'&callback=kboard_worldmap_franchise_init_map', array(), KBOARD_VERSION, true);
wp_enqueue_script('kboard-worldmap-franchise-marker-clusterer', "{$skin_path}/markerclusterer.js", array(), '1.0.3', true);
?>