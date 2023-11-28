<div id="kboard-document">
	<div id="kboard-modern-gallery-document">
		<div class="kboard-document-wrap" itemscope itemtype="http://schema.org/Article">
			<div class="kboard-detail">
				<?php if($content->category1):?>
				<div class="detail-attr detail-category1">
					<div class="detail-name"><?php echo $content->category1?></div>
				</div>
				<?php endif?>
				<?php if($content->category2):?>
				<div class="detail-attr detail-category2">
					<div class="detail-name"><?php echo $content->category2?></div>
				</div>
				<?php endif?>
				<?php if($content->option->tree_category_1):?>
				<?php for($i=1; $i<=$content->getTreeCategoryDepth(); $i++):?>
				<div class="detail-attr detail-tree-category-<?php echo $i?>">
					<div class="detail-name"><?php echo $content->option->{'tree_category_'.$i}?></div>
				</div>
				<?php endfor?>
				<?php endif?>
				<div class="detail-attr detail-writer">
					<div class="detail-name"><?php echo __('Author', 'kboard')?></div>
					<div class="detail-value"><?php echo $content->getUserDisplay()?></div>
				</div>
				<div class="detail-attr detail-date">
					<div class="detail-name"><?php echo __('Date', 'kboard')?></div>
					<div class="detail-value"><?php echo date('Y-m-d H:i', strtotime($content->date))?></div>
				</div>
				<div class="detail-attr detail-view">
					<div class="detail-name"><?php echo __('Views', 'kboard')?></div>
					<div class="detail-value"><?php echo $content->view?></div>
				</div>
			</div>
			
			<div class="kboard-title" itemprop="name">
				<p><?php echo $content->title?></p>
			</div>
			
			<div class="kboard-content" itemprop="description">
				<div class="content-view">
					<?php echo $content->getDocumentOptionsHTML()?>
					<?php echo $content->content?>
				</div>
			</div>
			
			<div class="kboard-document-action">
				<div class="left">
					<button type="button" class="kboard-modern-gallery-button-small" onclick="kboard_document_like(this)" data-uid="<?php echo $content->uid?>" title="<?php echo __('Like', 'kboard')?>"><?php echo __('Like', 'kboard')?> <span class="kboard-document-like-count"><?php echo intval($content->like)?></span></button>
					<button type="button" class="kboard-modern-gallery-button-small" onclick="kboard_document_unlike(this)" data-uid="<?php echo $content->uid?>" title="<?php echo __('Unlike', 'kboard')?>"><?php echo __('Unlike', 'kboard')?> <span class="kboard-document-unlike-count"><?php echo intval($content->unlike)?></span></button>
				</div>
				<div class="right">
					<button type="button" class="kboard-modern-gallery-button-small" onclick="kboard_document_print('<?php echo $url->getDocumentPrint($content->uid)?>')" title="<?php echo __('Print', 'kboard')?>"><?php echo __('Print', 'kboard')?></button>
				</div>
			</div>
			
			<?php if($content->isAttached()):?>
				<?php foreach($content->getAttachmentList() as $key=>$attach):?>
				<div class="kboard-attach">
					<?php echo __('Attachment', 'kboard')?> : <button type="button" class="kboard-button-action kboard-button-download" onclick="window.location.href='<?php echo $url->getDownloadURLWithAttach($content->uid, $key)?>'" title="<?php echo sprintf(__('Download %s', 'kboard'), $attach[1])?>"><?php echo $attach[1]?></button>
				</div>
				<?php endforeach?>
			<?php endif?>
		</div>
		
		<?php if($content->visibleComments()):?>
		<div class="kboard-comments-area"><?php echo $board->buildComment($content->uid)?></div>
		<?php endif?>
		
		<div class="kboard-control">
			<div class="left">
				<a href="<?php echo esc_url($url->getBoardList())?>" class="kboard-modern-gallery-button-small"><?php echo __('List', 'kboard')?></a>
				<a href="<?php echo esc_url($url->getDocumentURLWithUID($content->getPrevUID()))?>" class="kboard-modern-gallery-button-small"><?php echo __('Prev', 'kboard')?></a>
				<a href="<?php echo esc_url($url->getDocumentURLWithUID($content->getNextUID()))?>" class="kboard-modern-gallery-button-small"><?php echo __('Next', 'kboard')?></a>
			</div>
			<?php if($content->isEditor() || $board->permission_write=='all'):?>
			<div class="right">
				<a href="<?php echo esc_url($url->getContentEditor($content->uid))?>" class="kboard-modern-gallery-button-small"><?php echo __('Edit', 'kboard')?></a>
				<a href="<?php echo esc_url($url->getContentRemove($content->uid))?>" class="kboard-modern-gallery-button-small" onclick="return confirm('<?php echo __('Are you sure you want to delete?', 'kboard')?>');"><?php echo __('Delete', 'kboard')?></a>
			</div>
			<?php endif?>
		</div>
		
		<?php if($board->contribution() && !$board->meta->always_view_list):?>
		<div class="kboard-modern-gallery-poweredby">
			<a href="https://www.cosmosfarm.com/products/kboard" onclick="window.open(this.href);return false;" title="<?php echo __('KBoard is the best community software available for WordPress', 'kboard')?>">Powered by KBoard</a>
		</div>
		<?php endif?>
	</div>
</div>