<div id="kboard-ocean-gallery-list">
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
	<div class="kboard-list">
	<?php while($content = $list->hasNextNotice()):?>
		<div class="kboard-gallery-item kboard-list-notice<?php if($content->uid == kboard_uid()):?> kboard-list-selected<?php endif?>">
			<a href="<?php echo $url->getDocumentURLWithUID($content->uid)?>">
				<div class="kboard-gallery-thumbnail">
					<?php if($content->getThumbnail(300, 450)):?>
					<img src="<?php echo $content->getThumbnail(300, 450)?>" alt="<?php echo $content->title?>">
					<?php else:?>
					<div class="kboard-no-image"><i class="icon-picture"></i></div>
					<?php endif?>
					<div class="kboard-gallery-foreground"><img src="<?php echo KBOARD_URL_PATH . '/skin/ocean-gallery/images/over-foreground.png'?>" alt=""></div>
					<div class="kboard-gallery-username"><?php echo date("Y/m/d", strtotime($content->date))?> by. <?php echo apply_filters('kboard_user_display', $content->member_display, $content->member_uid, $content->member_display, 'kboard', $boardBuilder)?></div>
				</div>
				<div class="kboard-gallery-title">[<?php echo __('Notice', 'kboard')?>] <?php echo $content->title?></div>
                
			</a>
		</div>
	<?php endwhile?>
	<?php while($content = $list->hasNext()):?>
		<div class="kboard-gallery-item<?php if($content->uid == kboard_uid()):?> kboard-list-selected<?php endif?>">
			<a href="<?php echo $url->getDocumentURLWithUID($content->uid)?>">
				<div class="kboard-gallery-thumbnail">
					<?php if($content->getThumbnail(300, 450)):?>
					<img src="<?php echo $content->getThumbnail(300, 450)?>" alt="<?php echo $content->title?>">
					<?php else:?>
					<div class="kboard-no-image"><i class="icon-picture"></i></div>
					<?php endif?>
					<div class="kboard-gallery-foreground"></div>
					<div class="kbaord-content-container">
                    	<div class="kboard-gallery-title"><?php echo $content->title?>						</div>
                        <div class="kboard-gallery-content kboard-gallery-cut-strings">
						<?php if($content->category1):?><span class="kboard-category"><?php echo $content->category1?> ·</span><?php endif?>
						<?php if($content->category2):?><span class="kboard-category"><?php echo $content->category2?> ·</span><?php endif?>
						<?php if($content->secret):?>
							<?php echo __('Secret', 'kboard')?>
						<?php else:?>
							<?php
							$content->content = str_replace('[', '&#91;', $content->getContent());
							$content->content = str_replace(']', '&#93;', $content->getContent());
							echo strip_tags($content->content)
							?>
						<?php endif?>
					</div>
                        
                        </div>
					<div class="kboard-gallery-info" style="user-select: auto;">
                    <span class="kboard-list-view">
						<?php echo __('Views', 'kboard')?></span>
                     <span class="kboard-list-view">
<?php echo $content->view?>
					 </span>
                        <span class="kboard-date" style="user-select: auto;"><?php echo date("Y-m-d", strtotime($content->date))?>
				</span>
                </div></div>
				
			</a>
		</div>
	<?php endwhile?>
	</div>
	<!-- 리스트 끝 -->
	
	<!-- 페이징 시작 -->
	<div class="kboard-pagination">
		<ul class="kboard-pagination-pages">
			<?php echo kboard_pagination($list->page, $list->total, $list->rpp)?>
		</ul>
	</div>
	<!-- 페이징 끝 -->
	<form id="kboard-search-form-<?php echo $board->id?>" method="get" action="<?php echo $url->toString()?>">
		<?php echo $url->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->toInput()?>
		
		<div class="kboard-search">
			<select name="target">
				<option value=""><?php echo __('All', 'kboard')?></option>
				<option value="title"<?php if(kboard_target() == 'title'):?> selected<?php endif?>><?php echo __('Title', 'kboard')?></option>
				<option value="content"<?php if(kboard_target() == 'content'):?> selected<?php endif?>><?php echo __('Content', 'kboard')?></option>
				<option value="member_display"<?php if(kboard_target() == 'member_display'):?> selected<?php endif?>><?php echo __('Author', 'kboard')?></option>
			</select>
			<input type="text" name="keyword" value="<?php echo esc_attr(kboard_keyword())?>">
			<button type="submit" class="kboard-ocean-gallery-button-small"><?php echo __('Search', 'kboard')?></button>
		</div>
	</form>
	
	<?php if($board->isWriter()):?>
	<!-- 버튼 시작 -->
	<div class="kboard-control">
		<a href="<?php echo $url->getContentEditor()?>" class="kboard-ocean-gallery-button-small"><?php echo __('New', 'kboard')?></a>
	</div>
	<!-- 버튼 끝 -->
	<?php endif?>
	
	<?php if($board->contribution()):?>
	<div class="kboard-ocean-gallery-poweredby">
		<a href="http://www.cosmosfarm.com/products/kboard" onclick="window.open(this.href);return false;" title="<?php echo __('KBoard is the best community software available for WordPress', 'kboard')?>">Powered by KBoard</a>
	</div>
	<?php endif?>
</div>