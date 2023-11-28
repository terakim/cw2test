<?php
$category_list = array();
if($content->category1) $category_list[] = $content->category1;
if($content->category2) $category_list[] = $content->category2;
if($content->option->tree_category_1){
	for($i=1; $i<=$content->getTreeCategoryDepth(); $i++){
		$category_list[] = $content->option->{'tree_category_'.$i};
	}
}
$skin_field = $board->fields()->getSkinFields();
?>
<div id="kboard-document">
	<div id="kboard-cross-calendar-document" itemscope itemtype="http://schema.org/Article">
		<div class="kboard-detail-top-wrap">
			<span class="kboard-detail-top-schedule"><?php echo __('Schedule', 'kboard-cross-calendar')?></span>
		</div>
		
		<hr class="kboard-detail-top-hr">
		
		<?php if($category_list):?>
		<div class="kboard-detail-event"><?php echo implode(', ', $category_list)?></div>
		<?php endif?>
		
		<div class="kboard-detail-event-content<?php if($content->getThumbnail(600, 300)):?> kboard-thumbnail<?php endif?>">
			<?php if($content->getThumbnail(600, 300)):?>
			<div class="kboard-thumbnail-wrap">
				<div class="kboard-thumbnail" style="background-image: url(<?php echo $content->getThumbnail(600, 300)?>);"></div>
			</div>
			<?php endif?>
			
			<div class="kboard-right-wrap">
				<div class="kboard-mobile-detail-right">
					<h1 class="kboard-detail-title" itemprop="name"><?php echo $content->title?></h1>
					
					<?php if($category_list):?>
					<div class="kboard-mobile-detail-event"><?php echo implode(', ', $category_list)?></div>
					<?php endif?>
				</div>
				<?php foreach($skin_field as $field):?>
					<?php echo kboard_cross_calendar_document_top_option_html($field, $content, $board)?>
				<?php endforeach?>
			</div>
		</div>
		
		<div class="kboard-detail-summary-wrap">
			<?php echo kboard_cross_calendar_document_summary_option_html($content, $board)?>
		</div>
		
		<div class="kboard-detail-summary-content-wrap">
			<?php foreach($skin_field as $field):?>
				<?php echo kboard_cross_calendar_document_summary_item_option_html($field, $content, $board)?>
			<?php endforeach?>
		</div>
		
		<div class="kboard-content" itemprop="description">
			<div class="content-view">
				<?php echo $content->content?>
			</div>
		</div>
		
		<div class="kboard-detail">
			<a>
				<?php echo $content->getUserDisplay(sprintf('%s %s', get_avatar($content->getUserID(), 20, '', $content->getUserName()), $content->getUserName()))?>
			</a>
			·
			<?php echo date('Y-m-d H:i', strtotime($content->date))?>
			·
			<?php echo __('Views', 'kboard')?> <?php echo $content->view?>
		</div>
		
		<div class="kboard-document-action">
			<div class="left">
				<button type="button" class="kboard-button-action kboard-button-like" onclick="kboard_document_like(this)" data-uid="<?php echo $content->uid?>" title="<?php echo __('Like', 'kboard')?>"><?php echo __('Like', 'kboard')?> <span class="kboard-document-like-count"><?php echo intval($content->like)?></span></button>
				<button type="button" class="kboard-button-action kboard-button-unlike" onclick="kboard_document_unlike(this)" data-uid="<?php echo $content->uid?>" title="<?php echo __('Unlike', 'kboard')?>"><?php echo __('Unlike', 'kboard')?> <span class="kboard-document-unlike-count"><?php echo intval($content->unlike)?></span></button>
			</div>
			<div class="right">
				<button type="button" class="kboard-button-action kboard-button-print" onclick="kboard_document_print('<?php echo esc_url($url->getDocumentPrint($content->uid))?>')" title="<?php echo __('Print', 'kboard')?>"><?php echo __('Print', 'kboard')?></button>
			</div>
		</div>
		
		<?php if($content->isAttached()): ?>
		<div class="kboard-attach">
			<?php foreach($content->getAttachmentList() as $key=>$attach):?>
			<button type="button" class="kboard-button-action kboard-button-download" onclick="window.location.href='<?php echo esc_url($url->getDownloadURLWithAttach($content->uid, $key))?>'" title="<?php echo sprintf(__('Download %s', 'kboard'), $attach[1])?>"><?php echo $attach[1]?></button>
			<?php endforeach?>
		</div>
		<?php endif?>
		
		<?php if($content->visibleComments()):?>
		
		<div class="kboard-comments-area"><?php echo $board->buildComment($content->uid)?></div>
		<?php endif?>
		
		<div class="kboard-control">
			<div class="left">
				<a href="<?php echo esc_url($url->getBoardList())?>" class="kboard-cross-calendar-button-small"><?php echo __('List', 'kboard')?></a>
			</div>
			<div class="right">
				<?php if($content->isEditor() || $board->permission_write=='all'):?>
				<a href="<?php echo esc_url($url->getContentEditor($content->uid))?>" class="kboard-cross-calendar-button-small"><?php echo __('Edit Schedule', 'kboard-cross-calendar')?></a>
				<a href="<?php echo esc_url($url->getContentRemove($content->uid))?>" class="kboard-cross-calendar-button-small" onclick="return confirm('<?php echo __('Are you sure you want to delete?', 'kboard')?>');"><?php echo __('Delete Schedule', 'kboard-cross-calendar')?></a>
				<?php endif?>
			</div>
		</div>
		
		<?php if($board->contribution() && !$board->meta->always_view_list):?>
		<div class="kboard-cross-calendar-poweredby">
			<a href="https://www.cosmosfarm.com/products/kboard" onclick="window.open(this.href);return false;" title="<?php echo __('KBoard is the best community software available for WordPress', 'kboard')?>">Powered by KBoard</a>
		</div>
		<?php endif?>
	</div>
</div>