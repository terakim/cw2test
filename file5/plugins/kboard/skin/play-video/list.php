<div id="kboard-play-video-list"<?php if(kboard_play_video_list($board)):?> class="<?php echo kboard_play_video_list($board)?>"<?php endif?>>

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
	
	<!-- 리스트 시작 -->
	<ul class="kboard-list">
		<?php while($content = $list->hasNextNotice()):?>
		<li class="kboard-list-item<?php if($content->uid == kboard_uid()):?> kboard-selected<?php endif?>">
			<a href="<?php echo esc_url($url->getDocumentURLWithUID($content->uid))?>#kboard-document">
				
				<?php if($content->getThumbnail(330, 160)):?>
					<div class="kboard-list-thumbnail" style="background-image:url(<?php echo $content->getThumbnail(330, 160)?>)"></div>
				<?php elseif($content->option->youtube_id):?>
					<div class="kboard-list-thumbnail" style="background-image:url(<?php echo $content->option->youtube_thumbnail_url?>)"></div>
				<?php elseif($content->option->vimeo_id):?>
					<div class="kboard-list-thumbnail" style="background-image:url(<?php echo $content->option->vimeo_thumbnail_url?>)"></div>
				<?php else:?>
					<div class="kboard-list-thumbnail"></div>
				<?php endif?>
				
				<div class="kboard-list-title kboard-ell">
					<?php if($content->isNew()):?><span class="kboard-play-video-new-notify">New</span><?php endif?>
					<?php if($content->secret):?><img src="<?php echo $skin_path?>/images/icon-lock.png" alt="<?php echo __('Secret', 'kboard')?>"><?php endif?>
					<?php echo $content->title?>
					<?php echo $content->getCommentsCount()?>
				</div>
				
				<div class="kboard-video-info">
					<span class="info-item kboard-user"><?php echo $content->getUserDisplay(sprintf('%s %s', get_avatar($content->getUserID(), 16, '', $content->getUserName()), $content->getUserName()))?></span>
					<div class="kboard-ell">
						<span class="info-item kboard-view"><?php echo __('Views', 'kboard')?> <?php echo number_format(intval($content->view))?></span>
						<span class="info-separator kboard-date">·</span>
						<span class="info-item kboard-date"><?php echo date('Y-m-d', strtotime($content->date))?></span>
					</div>
				</div>
				
				<div class="kboard-list-vote"><img src="<?php echo $skin_path?>/images/icon-heart.png" alt="<?php echo __('Votes', 'kboard')?>"> <?php echo intval($content->vote)?></div>
				
				<div class="kboard-selected-background">
					<div class="kboard-selected-wrap">
						<div class="kboard-selected-table">
							<div class="kboard-selected-cell">
								<img src="<?php echo $skin_path?>/images/icon-play.png" alt="<?php echo __('Play', 'kboard')?>">
								<?php echo __('Play', 'kboard')?>
							</div>
						</div>
					</div>
				</div>
			</a>
		</li>
		<?php endwhile?>
		<?php while($content = $list->hasNext()):?>
		<li class="kboard-list-item<?php if($content->uid == kboard_uid()):?> kboard-selected<?php endif?>">
			<a href="<?php echo esc_url($url->getDocumentURLWithUID($content->uid))?>#kboard-document">
				
				<?php if($content->getThumbnail(330, 160)):?>
					<div class="kboard-list-thumbnail" style="background-image:url(<?php echo $content->getThumbnail(330, 160)?>)"></div>
				<?php elseif($content->option->youtube_id):?>
					<div class="kboard-list-thumbnail" style="background-image:url(<?php echo $content->option->youtube_thumbnail_url?>)"></div>
				<?php elseif($content->option->vimeo_id):?>
					<div class="kboard-list-thumbnail" style="background-image:url(<?php echo $content->option->vimeo_thumbnail_url?>)"></div>
				<?php else:?>
					<div class="kboard-list-thumbnail"></div>
				<?php endif?>
				
				<div class="kboard-list-title kboard-ell">
					<?php if($content->isNew()):?><span class="kboard-play-video-new-notify">New</span><?php endif?>
					<?php if($content->secret):?><img src="<?php echo $skin_path?>/images/icon-lock.png" alt="<?php echo __('Secret', 'kboard')?>"><?php endif?>
					<?php echo $content->title?>
					<?php echo $content->getCommentsCount()?>
				</div>
				
				<div class="kboard-video-info">
					<span class="info-item kboard-user"><?php echo $content->getUserDisplay(sprintf('%s %s', get_avatar($content->getUserID(), 16, '', $content->getUserName()), $content->getUserName()))?></span>
					<div class="kboard-ell">
						<span class="info-item kboard-view"><?php echo __('Views', 'kboard')?> <?php echo number_format(intval($content->view))?></span>
						<span class="info-separator kboard-date">·</span>
						<span class="info-item kboard-date"><?php echo date('Y-m-d', strtotime($content->date))?></span>
					</div>
				</div>
				
				<div class="kboard-list-vote"><img src="<?php echo $skin_path?>/images/icon-heart.png" alt="<?php echo __('Votes', 'kboard')?>"> <?php echo intval($content->vote)?></div>
				
				<div class="kboard-selected-background">
					<div class="kboard-selected-wrap">
						<div class="kboard-selected-table">
							<div class="kboard-selected-cell">
								<img src="<?php echo $skin_path?>/images/icon-play.png" alt="<?php echo __('Play', 'kboard')?>">
								<?php echo __('Play', 'kboard')?>
							</div>
						</div>
					</div>
				</div>
			</a>
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
	
	<!-- 검색폼 시작 -->
	<form method="get" action="<?php echo esc_url($url->toString())?>">
		<?php echo $url->set('pageid', '1')->set('mod', 'list')->toInput()?>
		
		<div class="kboard-search">
			<select name="target">
				<option value=""><?php echo __('All', 'kboard')?></option>
				<option value="title"<?php if(kboard_target() == 'title'):?> selected="selected"<?php endif?>><?php echo __('Title', 'kboard')?></option>
				<option value="content"<?php if(kboard_target() == 'content'):?> selected="selected"<?php endif?>><?php echo __('Content', 'kboard')?></option>
				<option value="member_display"<?php if(kboard_target() == 'member_display'):?> selected="selected"<?php endif?>><?php echo __('Author', 'kboard')?></option>
			</select>
			<input type="text" name="keyword" value="<?php echo esc_attr(kboard_keyword())?>" placeholder="<?php echo __('Search', 'kboard')?>...">
			<button type="submit" class="kboard-play-video-button-small"><?php echo __('Search', 'kboard')?></button>
		</div>
	</form>
	<!-- 검색폼 끝 -->
	
	<?php if($board->isWriter()):?>
	<!-- 버튼 시작 -->
	<div class="kboard-control">
		<button type="button" onclick="window.location.href='<?php echo esc_url($url->getContentEditor())?>'" class="kboard-play-video-button-small"><?php echo __('New', 'kboard')?></button>
	</div>
	<!-- 버튼 끝 -->
	<?php endif?>
	
	<?php if($board->contribution()):?>
	<div class="kboard-play-video-poweredby">
		<a href="https://www.cosmosfarm.com/products/kboard" onclick="window.open(this.href);return false;" title="<?php echo __('KBoard is the best community software available for WordPress', 'kboard')?>">Powered by KBoard</a>
	</div>
	<?php endif?>
</div>