<div id="kboard-ocean-download-list">
	<div class="kboard-header">
		<!-- 카테고리 시작 -->
    	<?php
    	if($board->use_category == 'yes'){
    		if($board->isTreeCategoryActive()){
    			$category_type = 'tree-select';
    		}
    		else{
    			$category_type = 'default';
    		}
    		$category_type = apply_filters('kboard_skin_category_type', $category_type, $board, $boardBuilder);
    		echo $skin->load($board->skin, "list-category-{$category_type}.php", $vars);
    	}
    	?>
    	<!-- 카테고리 끝 -->
	</div>
	
	<!-- 리스트 시작 -->
	<ul class="kboard-list">
	<?php while($content = $list->hasNextNotice()):?>
		<li class="kboard-list-item">
			<div class="kboard-wrap-left">
				<?php if($content->getThumbnail(70, 70)):?><img src="<?php echo $content->getThumbnail(70, 70)?>" alt="<?php echo esc_attr($content->title)?>"><?php else:?><img src="<?php echo $skin_path?>/images/default-thumbnail.png" alt="<?php echo esc_attr($content->title)?>"><?php endif?>
			</div>
			<div class="kboard-wrap-center">
				<div class="kboard-item-title kboard-ocean-download-cut-strings"><a href="<?php echo $url->getDocumentURLWithUID($content->uid)?>"><?php echo $content->title?></a></div>
				<div class="kboard-item-info"><?php echo date("Y.m.d", strtotime($content->date))?></div>
				<div class="kboard-item-content kboard-ocean-download-cut-strings"><a href="<?php echo $url->getDocumentURLWithUID($content->uid)?>"><?php echo strip_tags($content->content)?></a></div>
			</div>
			<div class="kboard-wrap-right">
				<div class="kboard-item-download-icon">
					<img src="<?php echo $skin_path?>/images/download.png" style="width: 56px; height: 47px;">
				</div>
				
				<div class="kbaord-item-download-list">
				<?php if($content->isAttached()):?>
					<?php $attach_index=0; foreach($content->getAttachmentList() as $key=>$attach): $attach_index++; if($attach_index>3) continue;?>
					<div class="kboard-download-item"><button type="button" class="kboard-button-action kboard-button-download" onclick="window.location.href='<?php echo $url->getDownloadURLWithAttach($content->uid, $key)?>'" title="<?php echo sprintf(__('Download %s', 'kboard'), $attach[1])?>"><?php echo $attach[1]?></button></div>
					<?php endforeach?>
					
					
					<?php if($attach_index>3):?>
					<div class="kboard-download-item"><a href="<?php echo $url->getDocumentURLWithUID($content->uid)?>">more..</a></div>
					<?php endif?>
				<?php endif?>
				</div>
			</div>
		</li>
	<?php endwhile?>
	<?php while($content = $list->hasNext()):?>
		<li class="kboard-list-item">
			<div class="kboard-wrap-left">
				<?php if($content->getThumbnail(70, 70)):?><img src="<?php echo $content->getThumbnail(70, 70)?>" alt="<?php echo esc_attr($content->title)?>"><?php else:?><img src="<?php echo $skin_path?>/images/default-thumbnail.png" alt="<?php echo esc_attr($content->title)?>"><?php endif?>
			</div>
			<div class="kboard-wrap-center">
				<div class="kboard-item-title kboard-ocean-download-cut-strings"><a href="<?php echo $url->getDocumentURLWithUID($content->uid)?>"><?php echo $content->title?></a></div>
				<div class="kboard-item-info"><?php echo date("Y.m.d", strtotime($content->date))?></div>
				<div class="kboard-item-content kboard-ocean-download-cut-strings"><a href="<?php echo $url->getDocumentURLWithUID($content->uid)?>"><?php echo strip_tags($content->content)?></a></div>
			</div>
			<div class="kboard-wrap-right">
				<div class="kboard-item-download-icon">
					<img src="<?php echo $skin_path?>/images/download.png" style="width: 56px; height: 47px;">
				</div>
				
				<div class="kbaord-item-download-list">
				<?php if($content->isAttached()):?>
					<?php $attach_index=0; foreach($content->getAttachmentList() as $key=>$attach): $attach_index++; if($attach_index>3) continue;?>
					<div class="kboard-download-item"><button type="button" class="kboard-button-action kboard-button-download" onclick="window.location.href='<?php echo $url->getDownloadURLWithAttach($content->uid, $key)?>'" title="<?php echo sprintf(__('Download %s', 'kboard'), $attach[1])?>"><?php echo $attach[1]?></button></div>
					<?php endforeach?>
					
					
					<?php if($attach_index>3):?>
					<div class="kboard-download-item"><a href="<?php echo $url->getDocumentURLWithUID($content->uid)?>">more..</a></div>
					<?php endif?>
				<?php endif?>
				</div>
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
	
	<div class="kboard-search">
    	<form id="kboard-search-form" method="get" action="<?php echo $url->toString()?>">
    		<?php echo $url->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->toInput()?>
    		
    			<select name="target">
    				<option value=""><?php echo __('All', 'kboard')?></option>
    				<option value="title"<?php if(kboard_target() == 'title'):?> selected="selected"<?php endif?>><?php echo __('Title', 'kboard')?></option>
    				<option value="content"<?php if(kboard_target() == 'content'):?> selected="selected"<?php endif?>><?php echo __('Content', 'kboard')?></option>
    				<option value="member_display"<?php if(kboard_target() == 'member_display'):?> selected="selected"<?php endif?>><?php echo __('Author', 'kboard')?></option>
    			</select>
    			<input type="text" name="keyword" value="<?php echo kboard_keyword()?>">
    			<button type="submit" class="kboard-ocean-download-button-small"><?php echo __('Search', 'kboard')?></button>
    	</form>
	</div>
	
	<?php if($board->isWriter()):?>
	<!-- 버튼 시작 -->
	<div class="kboard-control">
		<a href="<?php echo $url->getContentEditor()?>" class="kboard-ocean-download-button-small"><?php echo __('New', 'kboard')?></a>
	</div>
	<!-- 버튼 끝 -->
	<?php endif?>
	
	<?php if($board->contribution()):?>
	<div class="kboard-ocean-download-poweredby">
		<a href="https://www.cosmosfarm.com/products/kboard" onclick="window.open(this.href);return false;" title="<?php echo __('KBoard is the best community software available for WordPress', 'kboard')?>">Powered by KBoard</a>
	</div>
	<?php endif?>
</div>