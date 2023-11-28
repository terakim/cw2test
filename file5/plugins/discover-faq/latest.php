<div id="kboard-discover-faq-latest">
	<div class="kboard-discover-faq-list">
		<div class="kboard-list">
			<?php while($content = $list->hasNext()):?>
			<div class="kboard-list-item<?php if($content->uid == kboard_uid()):?> kboard-list-selected<?php endif?>">
				<button type="button" class="kboard-list-button" onclick="kboard_discover_faq_toggle(this)">
					<div class="kboard-list-title">
						<?php if($content->secret):?><img src="<?php echo $skin_path?>/images/icon-lock.png" class="icon-lock" alt="<?php echo __('Secret', 'kboard')?>"><?php endif?>
						<?php if($content->notice):?>
							[<?php echo __('Notice', 'kboard')?>] <?php echo $content->title?>
						<?php else:?>
							<?php echo $content->title?>
						<?php endif?>
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
								<div class="kboard-helpful">
									<span class="kboard-helpful-text"><?php echo discover_faq_helpful_default_helpful_message()?></span>
									<button type="button" class="kboard-button-action kboard-button-like" onclick="kboard_document_like(this)" data-uid="<?php echo $content->uid?>" title="<?php echo __('Like', 'kboard')?>"><i class="far fa-thumbs-up"></i> <?php echo __('Yes', 'kboard')?> <span class="kboard-document-like-count"><?php echo intval($content->like)?></span></button>
									<button type="button" class="kboard-button-action kboard-button-unlike" onclick="kboard_document_unlike(this)" data-uid="<?php echo $content->uid?>" title="<?php echo __('Unlike', 'kboard')?>"><i class="far fa-thumbs-down"></i> <?php echo __('No', 'kboard')?> <span class="kboard-document-unlike-count"><?php echo intval($content->unlike)?></span></button>
								</div>
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
		</div>
	</div>
</div>

<?php
wp_enqueue_script('kboard-discover-faq-list', "{$skin_path}/list.js", array(), KBOARD_DISCOVER_FAQ_VERSION, true);
?>