<div id="kboard-cross-calendar-editor">
	<form class="kboard-form" method="post" action="<?php echo esc_url($url->getContentEditorExecute())?>" enctype="multipart/form-data" onsubmit="return kboard_editor_execute(this);">
		<?php $skin->editorHeader($content, $board)?>
		
		<h4 class="kboard-attr-wrap-title">
			<?php if($content->uid):?>
				<?php echo __('Edit Schedule', 'kboard-cross-calendar')?>
			<?php else:?>
				<?php echo __('New Schedule', 'kboard-cross-calendar')?>
			<?php endif?>
		</h4>
		
		<?php foreach($board->fields()->getSkinFields() as $key=>$field):?>
			<?php echo $board->fields()->getTemplate($field, $content, $boardBuilder)?>
		<?php endforeach?>
		
		<div class="kboard-control">
			<div class="left">
				<?php if($content->uid):?>
				<a href="<?php echo esc_url($url->getDocumentURLWithUID($content->uid))?>" class="kboard-cross-calendar-button-small"><?php echo __('Back', 'kboard')?></a>
				<a href="<?php echo esc_url($url->getBoardList())?>" class="kboard-cross-calendar-button-small"><?php echo __('List', 'kboard')?></a>
				<?php else:?>
				<a href="<?php echo esc_url($url->set('mod', 'list')->set('ymd', '')->toString())?>" class="kboard-cross-calendar-button-small"><?php echo __('Back', 'kboard')?></a>
				<?php endif?>
			</div>
			<div class="right">
				<?php if($board->isWriter()):?>
				<button type="submit" class="kboard-cross-calendar-button-small"><?php echo __('Save', 'kboard')?></button>
				<?php endif?>
			</div>
		</div>
	</form>
</div>
<script>
var kboard_cross_calendar_editor_settings = {
	locale:'<?php echo get_locale()?>',
	end_date_check_message:'<?php echo __('End date can not be set before start date.', 'kboard-cross-calendar')?>'
};
</script>
<?php
wp_enqueue_script('kboard-cross-calendar-script', "{$skin_path}/script.js", array(), KBOARD_CROSS_CALENDAR_VERSION, true);
wp_enqueue_script('jquery-ui-datepicker');
wp_enqueue_script('jquery-timepicker', KBOARD_URL_PATH . '/template/js/jquery.timepicker.js', array(), KBOARD_VERSION);
wp_enqueue_style('jquery-flick-style', KBOARD_URL_PATH.'/template/css/jquery-ui.css', array(), '1.12.1');
wp_enqueue_style('jquery-timepicker', KBOARD_URL_PATH.'/template/css/jquery.timepicker.css', array(), KBOARD_VERSION);
?>