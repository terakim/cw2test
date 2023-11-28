<div id="kboard-modern-gallery-list">
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
	<?php while($content = $list->hasNext()): $resize_img_src = $content->getThumbnail(290, 215);?>
		<li class="kboard-list-item">
			<a href="<?php echo esc_url($url->getDocumentURLWithUID($content->uid))?>" class="kboard-item-thumbnail" style="background-image:url(<?php echo $resize_img_src?>);filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php echo $resize_img_src?>',sizingMethod='scale');-ms-filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php echo $resize_img_src?>',sizingMethod='scale');">
				<?php if(!$resize_img_src):?><i class="icon-picture"></i><?php endif?>
			</a>
			<div class="kboard-item-description">
				<a href="<?php echo esc_url($url->getDocumentURLWithUID($content->uid))?>" class="kboard-item-avatar">
					<?php echo get_avatar($content->getUserID(), 24, $skin_path.'/images/default-avatar.png', $content->getUserName())?>
					<img src="<?php echo $skin_path?>/images/avatar-mask.png" alt="" class="kboard-item-avatar-mask">
				</a>
				<p class="kboard-item-title kboard-modern-gallery-cut-strings"><a href="<?php echo $url->getDocumentURLWithUID($content->uid)?>"><?php echo $content->title?></a></p>
				<p class="kboard-item-user">by <span><?php echo $content->getUserDisplay()?></span></p>
			</div>
			<div class="kboard-item-info">
				<span class="kboard-item-info-views"><?php echo $content->view?></span>
				<span class="kboard-item-info-like"><?php echo $content->vote?></span>
				<span class="kboard-item-info-comments"><?php echo $content->getCommentsCount()?></span>
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
	
	<form id="kboard-search-form" method="get" action="<?php echo esc_url($url->toString())?>">
		<?php echo $url->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->toInput()?>
		
		<div class="kboard-search">
			<select name="target">
				<option value=""><?php echo __('All', 'kboard')?></option>
				<option value="title"<?php if(kboard_target() == 'title'):?> selected<?php endif?>><?php echo __('Title', 'kboard')?></option>
				<option value="content"<?php if(kboard_target() == 'content'):?> selected<?php endif?>><?php echo __('Content', 'kboard')?></option>
				<option value="member_display"<?php if(kboard_target() == 'member_display'):?> selected<?php endif?>><?php echo __('Author', 'kboard')?></option>
			</select>
			<input type="text" name="keyword" value="<?php echo kboard_keyword()?>">
			<button type="submit" class="kboard-modern-gallery-button-small"><?php echo __('Search', 'kboard')?></button>
		</div>
	</form>
	
	<?php if($board->isWriter()):?>
	<!-- 버튼 시작 -->
	<div class="kboard-control">
		<a href="<?php echo esc_url($url->getContentEditor())?>" class="kboard-modern-gallery-button-small"><?php echo __('New', 'kboard')?></a>
	</div>
	<!-- 버튼 끝 -->
	<?php endif?>
	
	<?php if($board->contribution()):?>
	<div class="kboard-modern-gallery-poweredby">
		<a href="https://www.cosmosfarm.com/products/kboard" onclick="window.open(this.href);return false;" title="<?php echo __('KBoard is the best community software available for WordPress', 'kboard')?>">Powered by KBoard</a>
	</div>
	<?php endif?>
</div>