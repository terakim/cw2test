<div id="kboard-discover-faq-list">
	<div class="kboard-discover-faq-list">
		<!-- 게시판 정보 시작 -->
		<div class="kboard-list-header">
			<?php if(!$board->isPrivate()):?>
				<div class="kboard-total-count">
					<?php echo __('Total', 'kboard')?> <?php echo number_format($board->getListTotal())?>
				</div>
			<?php endif?>
			
			<div class="kboard-sort">
				<form id="kboard-sort-form-<?php echo $board->id?>" method="get" action="<?php echo $url->toString()?>">
					<?php echo $url->set('pageid', '1')->set('category1', '')->set('category2', '')->set('target', '')->set('keyword', '')->set('mod', 'list')->set('kboard_list_sort_remember', $board->id)->toInput()?>
					
					<select name="kboard_list_sort" onchange="jQuery('#kboard-sort-form-<?php echo $board->id?>').submit();">
						<option value="newest"<?php if($list->getSorting() == 'newest'):?> selected<?php endif?>><?php echo __('Newest', 'kboard')?></option>
						<option value="best"<?php if($list->getSorting() == 'best'):?> selected<?php endif?>><?php echo __('Best', 'kboard')?></option>
						<option value="viewed"<?php if($list->getSorting() == 'viewed'):?> selected<?php endif?>><?php echo __('Viewed', 'kboard')?></option>
						<option value="updated"<?php if($list->getSorting() == 'updated'):?> selected<?php endif?>><?php echo __('Updated', 'kboard')?></option>
					</select>
				</form>
			</div>
		</div>
		<!-- 게시판 정보 끝 -->
		
		<!-- 검색폼 시작 -->
		<div class="kboard-search">
			<form id="kboard-search-form-<?php echo $board->id?>" method="get" action="<?php echo $url->toString()?>">
				<?php echo $url->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->toInput()?>
				
				<select name="target">
					<option value=""><?php echo __('All', 'kboard')?></option>
					<option value="title"<?php if(kboard_target() == 'title'):?> selected<?php endif?>><?php echo __('Title', 'kboard')?></option>
					<option value="content"<?php if(kboard_target() == 'content'):?> selected<?php endif?>><?php echo __('Content', 'kboard')?></option>
					<option value="member_display"<?php if(kboard_target() == 'member_display'):?> selected<?php endif?>><?php echo __('Author', 'kboard')?></option>
				</select>
				<input type="text" name="keyword" value="<?php echo esc_attr(kboard_keyword())?>">
				<button type="submit" class="kboard-discover-faq-button-small"><?php echo __('Search', 'kboard')?></button>
			</form>
		</div>
		<!-- 검색폼 끝 -->
		
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
		<div class="kboard-list">
			<?php while($content = $list->hasNextNotice()):?>
			<div class="kboard-list-item kboard-list-notice<?php if($content->uid == kboard_uid()):?> kboard-list-selected<?php endif?>">
				<button type="button" class="kboard-list-button" onclick="kboard_discover_faq_toggle(this)">
					<div class="kboard-list-title">
						<?php if($content->secret):?><img src="<?php echo $skin_path?>/images/icon-lock.png" class="icon-lock" alt="<?php echo __('Secret', 'kboard')?>"><?php endif?>
						[<?php echo __('Notice', 'kboard')?>] <?php echo $content->title?>
						<span class="kboard-comments-count"><?php echo $content->getCommentsCount()?></span>
						<span class="accordion"><i class="fas fa-plus"></i></span>
					</div>
				</button>
				
				<div class="kboard-list-description-wrap">
					<div class="kboard-list-description">
						<?php if($content->category1 || $content->category2 || $content->option->tree_category_1):?>
						<div class="kboard-list-category">
							<?php if($content->category1):?>
							<span class="kboard-info-value">#<?php echo $content->category1?></span>
							<?php endif?>
							<?php if($content->category2):?>
							<span class="kboard-info-value">#<?php echo $content->category2?></span>
							<?php endif?>
							<?php if($content->option->tree_category_1):?>
								<?php for($i=1; $i<=$content->getTreeCategoryDepth(); $i++):?>
								<span class="kboard-info-value">#<?php echo $content->option->{'tree_category_'.$i}?></span>
								<?php endfor?>
							<?php endif?>
						</div>
						<?php endif?>
						
						<?php if($content->secret && !$content->isEditor()):?>
							<?php if($content->isAttached()):?>
								<div class="kboard-attach-wrap">
									<?php foreach($content->getAttachmentList() as $key=>$attach):?>
									<button class="kboard-attach" onclick="alert('<?php echo __('You do not have permission.', 'kboard')?>');return false;" title="<?php echo sprintf(__('Download %s', 'kboard'), $attach[1])?>">
										<div class="file-info">
											<div class="file-name"><?php echo $attach[1]?></div>
											<div class="file-size"><?php echo kboard_discover_faq_get_file_size($attach['file_size'])?></div>
										</div>
										<div class="download-icon">
											<i class="fas fa-arrow-down"></i>
										</div>
									</button>
									<?php endforeach?>
								</div>
							<?php endif?>
						<?php else:?>
							<?php if($content->isAttached()):?>
								<div class="kboard-attach-wrap">
									<?php foreach($content->getAttachmentList() as $key=>$attach):?>
									<button class="kboard-attach" onclick="window.location.href='<?php echo $url->getDownloadURLWithAttach($content->uid, $key)?>'" title="<?php echo sprintf(__('Download %s', 'kboard'), $attach[1])?>">
										<div class="file-info">
											<div class="file-name"><?php echo $attach[1]?></div>
											<div class="file-size"><?php echo kboard_discover_faq_get_file_size($attach['file_size'])?></div>
										</div>
										<div class="download-icon">
											<i class="fas fa-arrow-down"></i>
										</div>
									</button>
									<?php endforeach?>
								</div>
							<?php endif?>
						<?php endif?>
						
						<div class="kboard-list-content">
							<div class="content-view">
								<?php if($content->secret && !$content->isEditor()):?>
									<a href="<?php echo $url->getDocumentURLWithUID($content->uid)?>" class="kboard-discover-faq-button-small"><?php echo __('비밀글 보기', 'kboard')?></a>
								<?php else:?>
									<?php echo $content->getDocumentOptionsHTML()?>
									<?php echo kboard_discover_faq_content($board, $boardBuilder, $content)?>
								<?php endif?>
							</div>
						</div>
						
						<div class="kboard-list-action">
							<div class="left">
								<?php if(!$content->secret || $content->isEditor()):?>
								<span class="kboard-helpful">
									<?php if($board->meta->discover_faq_count):?><span class="kboard-discover-faq-view"><?php echo __('Views', 'kboard')?> <?php echo $content->view?> · </span><?php endif?>
									<span class="kboard-helpful-text"><?php echo discover_faq_helpful_default_helpful_message()?></span>
									<button type="button" class="kboard-button-action kboard-button-like" onclick="kboard_document_like(this)" data-uid="<?php echo $content->uid?>" title="<?php echo __('Like', 'kboard')?>"><i class="far fa-thumbs-up"></i> <?php echo __('Yes', 'kboard')?> <span class="kboard-document-like-count"><?php echo intval($content->like)?></span></button>
									<button type="button" class="kboard-button-action kboard-button-unlike" onclick="kboard_document_unlike(this)" data-uid="<?php echo $content->uid?>" title="<?php echo __('Unlike', 'kboard')?>"><i class="far fa-thumbs-down"></i> <?php echo __('No', 'kboard')?> <span class="kboard-document-unlike-count"><?php echo intval($content->unlike)?></span></button>
								</span>
								<?php endif?>
							</div>
							<?php if($content->isEditor() || $board->permission_write=='all'):?>
							<div class="right">
								<a href="<?php echo $url->getContentEditor($content->uid)?>" class="kboard-button-action" title="<?php echo __('Edit', 'kboard')?>"><i class="fas fa-eraser"></i></a>
								<a href="<?php echo $url->getContentRemove($content->uid)?>" class="kboard-button-action" title="<?php echo __('Delete', 'kboard')?>" onclick="return confirm('<?php echo __('Are you sure you want to delete?', 'kboard')?>');"><i class="fas fa-trash"></i></a>
							</div>
							<?php endif?>
						</div>
					</div>
					
					<?php if($content->visibleComments()):?>
					<div class="kboard-comments-area"><?php echo $board->buildComment($content->uid)?></div>
					<?php endif?>
				</div>
			</div>
			<?php endwhile?>
			<?php while($content = $list->hasNext()):?>
			<div class="kboard-list-item<?php if($content->uid == kboard_uid()):?> kboard-list-selected<?php endif?>">
				<button type="button" class="kboard-list-button" onclick="kboard_discover_faq_toggle(this)">
					<div class="kboard-list-title">
						<?php if($content->secret):?><img src="<?php echo $skin_path?>/images/icon-lock.png" class="icon-lock" alt="<?php echo __('Secret', 'kboard')?>"><?php endif?>
						<?php echo $content->title?>
						<span class="kboard-comments-count"><?php echo $content->getCommentsCount()?></span>
						<span class="accordion"><i class="fas fa-plus"></i></span>
					</div>
				</button>
				
				<div class="kboard-list-description-wrap">
					<div class="kboard-list-description">
						<?php if($content->category1 || $content->category2 || $content->option->tree_category_1):?>
						<div class="kboard-list-category">
							<?php if($content->category1):?>
							<span class="kboard-info-value">#<?php echo $content->category1?></span>
							<?php endif?>
							<?php if($content->category2):?>
							<span class="kboard-info-value">#<?php echo $content->category2?></span>
							<?php endif?>
							<?php if($content->option->tree_category_1):?>
								<?php for($i=1; $i<=$content->getTreeCategoryDepth(); $i++):?>
								<span class="kboard-info-value">#<?php echo $content->option->{'tree_category_'.$i}?></span>
								<?php endfor?>
							<?php endif?>
						</div>
						<?php endif?>
						
						<?php if($content->secret && !$content->isEditor()):?>
							<?php if($content->isAttached()):?>
								<div class="kboard-attach-wrap">
									<?php foreach($content->getAttachmentList() as $key=>$attach):?>
									<button class="kboard-attach" onclick="alert('<?php echo __('You do not have permission.', 'kboard')?>');return false;" title="<?php echo sprintf(__('Download %s', 'kboard'), $attach[1])?>">
										<div class="file-info">
											<div class="file-name"><?php echo $attach[1]?></div>
											<div class="file-size"><?php echo kboard_discover_faq_get_file_size($attach['file_size'])?></div>
										</div>
										<div class="download-icon">
											<i class="fas fa-arrow-down"></i>
										</div>
									</button>
									<?php endforeach?>
								</div>
							<?php endif?>
						<?php else:?>
							<?php if($content->isAttached()):?>
								<div class="kboard-attach-wrap">
									<?php foreach($content->getAttachmentList() as $key=>$attach):?>
									<button class="kboard-attach" onclick="window.location.href='<?php echo $url->getDownloadURLWithAttach($content->uid, $key)?>'" title="<?php echo sprintf(__('Download %s', 'kboard'), $attach[1])?>">
										<div class="file-info">
											<div class="file-name"><?php echo $attach[1]?></div>
											<div class="file-size"><?php echo kboard_discover_faq_get_file_size($attach['file_size'])?></div>
										</div>
										<div class="download-icon">
											<i class="fas fa-arrow-down"></i>
										</div>
									</button>
									<?php endforeach?>
								</div>
							<?php endif?>
						<?php endif?>
						
						<div class="kboard-list-content">
							<div class="content-view">
								<?php if($content->secret && !$content->isEditor()):?>
									<a href="<?php echo $url->getDocumentURLWithUID($content->uid)?>" class="kboard-discover-faq-button-small"><?php echo __('비밀글 보기', 'kboard')?></a>
								<?php else:?>
									<?php echo $content->getDocumentOptionsHTML()?>
									<?php echo kboard_discover_faq_content($board, $boardBuilder, $content)?>
								<?php endif?>
							</div>
						</div>
						
						<div class="kboard-list-action">
							<div class="left">
								<?php if(!$content->secret || $content->isEditor()):?>
								<span class="kboard-helpful">
									<?php if($board->meta->discover_faq_count):?><span class="kboard-discover-faq-view"><?php echo __('Views', 'kboard')?> <?php echo $content->view?> · </span><?php endif?>
									<span class="kboard-helpful-text"><?php echo discover_faq_helpful_default_helpful_message()?></span>
									<button type="button" class="kboard-button-action kboard-button-like" onclick="kboard_document_like(this)" data-uid="<?php echo $content->uid?>" title="<?php echo __('Like', 'kboard')?>"><i class="far fa-thumbs-up"></i> <?php echo __('Yes', 'kboard')?> <span class="kboard-document-like-count"><?php echo intval($content->like)?></span></button>
									<button type="button" class="kboard-button-action kboard-button-unlike" onclick="kboard_document_unlike(this)" data-uid="<?php echo $content->uid?>" title="<?php echo __('Unlike', 'kboard')?>"><i class="far fa-thumbs-down"></i> <?php echo __('No', 'kboard')?> <span class="kboard-document-unlike-count"><?php echo intval($content->unlike)?></span></button>
									
								<?php endif?>
							</div>
							<?php if($content->isEditor() || $board->permission_write=='all'):?>
							<div class="right">
								<a href="<?php echo $url->getContentEditor($content->uid)?>" class="kboard-button-action" title="<?php echo __('Edit', 'kboard')?>"><i class="fas fa-eraser"></i></a>
								<a href="<?php echo $url->getContentRemove($content->uid)?>" class="kboard-button-action" title="<?php echo __('Delete', 'kboard')?>" onclick="return confirm('<?php echo __('Are you sure you want to delete?', 'kboard')?>');"><i class="fas fa-trash"></i></a>
							</div>
							<?php endif?>
						</div>
					</div>
					
					<?php if($content->visibleComments()):?>
					<div class="kboard-comments-area"><?php echo $board->buildComment($content->uid)?></div>
					<?php endif?>
				</div>
			</div>
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
			<a href="<?php echo $url->getContentEditor()?>" class="kboard-discover-faq-button-small"><?php echo __('New', 'kboard')?></a>
		</div>
		<!-- 버튼 끝 -->
		<?php endif?>
		
		<?php if($board->contribution()):?>
		<div class="kboard-discover-faq-poweredby">
			<a href="https://www.cosmosfarm.com/products/kboard" onclick="window.open(this.href);return false;" title="<?php echo __('KBoard is the best community software available for WordPress', 'kboard')?>">Powered by KBoard</a>
		</div>
		<?php endif?>
	</div>
</div>

<?php
wp_enqueue_script('kboard-discover-faq-list', "{$skin_path}/list.js", array(), KBOARD_DISCOVER_FAQ_VERSION, true);
?>