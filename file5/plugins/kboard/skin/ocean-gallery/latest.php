<div id="kboard-ocean-gallery-latest">
	<?php while($content = $list->hasNext()):?>
		<a href="<?php echo $url->getDocumentURLWithUID($content->uid)?>">
			<div class="kboard-ocean-gallery-latest-item">
				<div class="kboard-ocean-gallery-latest-thumbnail"><?php if($content->getThumbnail(109, 64)):?><img src="<?php echo $content->getThumbnail(109, 64)?>" style="width:100%;height:100%" alt=""><?php else:?><div class="kboard-no-image"><i class="icon-picture"></i></div><?php endif?></div>
				<div class="kboard-ocean-gallery-latest-title kboard-ocean-gallery-cut-strings"><?php echo $content->title?></div>
			</div>
		</a>
	<?php endwhile?>
</div>