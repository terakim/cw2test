<div id="kboard-worldmap-franchise-list">
	<ul class="kboard-list">
		<?php while($content = $list->hasNext()):?>
		<li class="kboard-list-item<?php if($content->uid == kboard_uid()):?> kboard-list-selected<?php endif?>">
			<a href="<?php echo esc_url($url->getDocumentURLWithUID($content->uid))?>" title="<?php echo esc_attr($content->title)?>">
				<div class="kboard-worldmap-franchise-thumbnail">
				<?php if($content->getThumbnail(600, 600)):?>
					<img src="<?php echo esc_url($content->getThumbnail(600, 600))?>" alt="<?php echo esc_attr($content->title)?>">
				<?php else:?>
					<div class="kboard-worldmap-franchise-no-image"></div>
				<?php endif?>
				</div>
			</a>
			<div class="kboard-worldmap-franchise-wrap">
				<a href="<?php echo esc_url($url->getDocumentURLWithUID($content->uid))?>" title="<?php echo esc_attr($content->getUserName())?>">
					<div class="kboard-worldmap-franchise-title">
						<?php if($content->isNew()):?><span class="kboard-worldmap-franchise-new-notify">New</span><?php endif?>
						<?php if($content->secret):?><img src="<?php echo $skin_path?>/images/icon-lock.png" alt="<?php echo __('Secret', 'kboard')?>"><?php endif?>
						<?php echo $content->title?>
					</div>
				</a>
				
				<?php if($content->category1):?>
				<div class="kboard-worldmap-franchise-area"><?php echo __('Area', 'kboard-worldmap-franchise')?> : <?php echo kboard_worldmap_franchise_branch($content->category1)?></div>
				<?php endif?>
				
				<?php echo $content->getDocumentOptionsHTML()?>
			</div>
		</li>
		<?php endwhile?>
	</ul>
</div>