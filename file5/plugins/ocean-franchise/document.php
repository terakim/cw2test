<div id="kboard-ocean-franchise-document">
	<div class="kboard-document-wrap" itemscope itemtype="http://schema.org/Article">
		<div class="kboard-detail">
			<div class="detail-attr detail-date">
				<div class="detail-name"><?php echo __('Date', 'kboard')?></div>
				<div class="detail-value"><?php echo date("Y-m-d H:i", strtotime($content->date))?></div>
			</div>
			<div class="detail-attr detail-view">
				<div class="detail-name"><?php echo __('Views', 'kboard')?></div>
				<div class="detail-value"><?php echo $content->view?></div>
			</div>
		</div>
		
		<div id="kboard-franchise-map-canvas" class="kboard-map"></div>
		
		<div class="kboard-franchise-info">
			<table>
				<tr>
					<?php if($content->getThumbnail(200, 150)):?>
					<td class="kboard-franchise-thumbnail" rowspan="5">
						<img src="<?php echo $content->getThumbnail(200, 150)?>" alt="<?php echo esc_attr($content->title)?>">
					</td>
					<?php endif?>
					<td class="kboard-franchise-attr"><?php echo __('Area', 'kboard-ocean-franchise')?> :</td>
					<td class="kboard-franchise-value"><?php echo kboard_ocean_franchise_branch_display($content->category1)?></td>
				</tr>
				<tr>
					<td class="kboard-franchise-attr"><?php echo __('Branch', 'kboard-ocean-franchise')?> :</td>
					<td class="kboard-franchise-value" itemprop="name"><?php echo apply_filters('kboard_user_display', $content->member_display, $content->member_uid, $content->member_display, 'kboard', $boardBuilder)?></td>
				</tr>
				<tr>
					<td class="kboard-franchise-attr"><?php echo __('Address', 'kboard-ocean-franchise')?> :</td>
					<td class="kboard-franchise-value"><?php echo $content->title?></td>
				</tr>
				<tr>
					<td class="kboard-franchise-attr"><?php echo __('Contact', 'kboard-ocean-franchise')?> :</td>
					<td class="kboard-franchise-value"><?php echo $content->option->tel?$content->option->tel:__('None', 'kboard-ocean-franchise')?></td>
				</tr>
				<tr>
					<td class="kboard-franchise-attr"><?php echo __('Homepage', 'kboard-ocean-franchise')?> :</td>
					<td class="kboard-franchise-value"><?php if($content->option->homepage): $kboard_homepage = str_replace(array('http://', 'https://'), '', $content->option->homepage);?><a href="http://<?php echo $kboard_homepage?>" onclick="window.open(this.href); return false;">http://<?php echo $kboard_homepage?></a><?php else:?><?php echo __('None', 'kboard-ocean-franchise')?><?php endif?></td>
				</tr>
			</table>
		</div>
		
		<div class="kboard-content" itemprop="description">
			<div class="content-view">
				<?php echo $content->getDocumentOptionsHTML()?>
				<?php echo $content->content?>
			</div>
		</div>
		
		<?php if($content->isAttached()):?>
			<?php foreach($content->getAttachmentList() as $key=>$attach):?>
				<div class="kboard-attach">
					<?php echo __('Attachment', 'kboard')?> : <button type="button" class="kboard-button-download" onclick="window.location.href='<?php echo $url->getDownloadURLWithAttach($content->uid, $key)?>'" title="<?php echo sprintf(__('Download %s', 'kboard'), $attach[1])?>"><?php echo $attach[1]?></button>
				</div>
			<?php endforeach?>
		<?php endif?>
	</div>
	
	<?php if($content->visibleComments()):?>
	<div class="kboard-comments-area"><?php echo $board->buildComment($content->uid)?></div>
	<?php endif?>
	
	<div class="kboard-control">
		<div class="left">
			<a href="<?php echo $url->getBoardList()?>" class="kboard-ocean-franchise-button-small"><?php echo __('List', 'kboard')?></a>
			<a href="<?php echo $url->getDocumentURLWithUID($content->getPrevUID())?>" class="kboard-ocean-franchise-button-small"><?php echo __('Prev', 'kboard')?></a>
			<a href="<?php echo $url->getDocumentURLWithUID($content->getNextUID())?>" class="kboard-ocean-franchise-button-small"><?php echo __('Next', 'kboard')?></a>
		</div>
		<div class="right">
			<?php if($content->isEditor() || $board->permission_write=='all'):?>
			<a href="<?php echo $url->getContentEditor($content->uid)?>" class="kboard-ocean-franchise-button-small"><?php echo __('Edit Branch', 'kboard-ocean-franchise')?></a>
			<a href="<?php echo $url->getContentRemove($content->uid)?>" class="kboard-ocean-franchise-button-small" onclick="return confirm('<?php echo __('Are you sure you want to delete?', 'kboard')?>');"><?php echo __('Delete Branch', 'kboard-ocean-franchise')?></a>
			<?php endif?>
		</div>
	</div>
	
	<?php if($board->contribution() && !$board->meta->always_view_list):?>
	<div class="kboard-ocean-franchise-poweredby">
		<a href="https://www.cosmosfarm.com/products/kboard" onclick="window.open(this.href);return false;" title="<?php echo __('KBoard is the best community software available for WordPress', 'kboard')?>">Powered by KBoard</a>
	</div>
	<?php endif?>
</div>

<script>
jQuery(document).ready(function(){
	var name = '<?php echo apply_filters('kboard_user_display', $content->member_display, $content->member_uid, $content->member_display, 'kboard', $boardBuilder)?>';
	var address = '<?php echo esc_attr($content->option->map_address)?>';
	<?php if($content->option->map_location):?>
	var location = new google.maps.LatLng(<?php echo esc_attr($content->option->map_location)?>);
	<?php else:?>
	var location = '';
	<?php endif?>
	if(address || location) kboard_franchise_map_initialize(name, address, location);
});
</script>
<?php
wp_enqueue_script('kboard-ocean-franchise-document', "{$skin_path}/document.js", array(), KBOARD_VERSION, true);
wp_enqueue_script('kboard-ocean-franchise-google-maps', 'https://maps.googleapis.com/maps/api/js?key='.kboard_ocean_franchise_google_maps_api_key($board).'&language='.get_locale(), array(), KBOARD_VERSION, true);
?>