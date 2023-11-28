<div id="kboard-worldmap-franchise-editor">
	<form class="kboard-form" method="post" action="<?php echo esc_url($url->getContentEditorExecute())?>" enctype="multipart/form-data" onsubmit="return kboard_editor_execute(this);">
		<?php $skin->editorHeader($content, $board)?>
		
		<h4 class="kboard-attr-wrap-title">
			<?php if($content->uid):?>
				<?php echo __('Edit Place', 'kboard-worldmap-franchise')?>
			<?php else:?>
				<?php echo __('Register Place', 'kboard-worldmap-franchise')?>
			<?php endif?>
		</h4>
		
		<?php foreach($board->fields()->getSkinFields() as $key=>$field):?>
			<?php echo $board->fields()->getTemplate($field, $content, $boardBuilder)?>
		<?php endforeach?>
		
		<div class="kboard-attr-row kboard-control">
			<div class="left">
				<?php if($content->uid):?>
				<a href="<?php echo esc_url($url->getDocumentURLWithUID($content->uid))?>" class="kboard-worldmap-franchise-button-small"><?php echo __('Back', 'kboard')?></a>
				<a href="<?php echo esc_url($url->getBoardList())?>" class="kboard-worldmap-franchise-button-small"><?php echo __('List', 'kboard')?></a>
				<?php else:?>
				<a href="<?php echo esc_url($url->getBoardList())?>" class="kboard-worldmap-franchise-button-small"><?php echo __('Back', 'kboard')?></a>
				<?php endif?>
			</div>
			<div class="right">
				<?php if($board->isWriter()):?>
				<button type="submit" class="kboard-worldmap-franchise-button-small"><?php echo __('Save', 'kboard')?></button>
				<?php endif?>
			</div>
		</div>
	</form>
</div>

<script>
var worldmap_franchise_editor = {
	board_id:'<?php echo intval($board->id)?>',
	permalink:'<?php echo get_permalink()?>',
	security:'<?php echo wp_create_nonce('kboard_worldmap_franchise_geocode')?>'
}
</script>
<?php wp_enqueue_script('kboard-worldmap-franchise-script', "{$skin_path}/script.js", array(), KBOARD_VERSION, true)?>