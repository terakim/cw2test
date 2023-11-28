<ul id="kboard-ocean-download-latest">
	<?php while($content = $list->hasNext()):?>
		<li class="kboard-ocean-download-latest-item kboard-ocean-download-cut-strings">
			<a href="<?php echo $url->getDocumentURLWithUID($content->uid)?>"><?php echo $content->title?></a><span>, <?php echo $content->getDate()?></span>
		</li>
	<?php endwhile?>
</ul>