<div id="kboard-modern-gallery-latest">
	<?php while($content = $list->hasNext()): $resize_img_src = $content->getThumbnail(188, 130);?>
		<div class="kboard-modern-gallery-latest-item">
			<a href="<?php echo esc_url($url->getDocumentURLWithUID($content->uid))?>" class="kboard-modern-gallery-latest-thumbnail" style="background-image:url(<?php echo $resize_img_src?>);filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php echo $resize_img_src?>',sizingMethod='scale');-ms-filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php echo $resize_img_src?>',sizingMethod='scale');">
				<?php if(!$resize_img_src):?><i class="icon-picture"></i><?php endif?>
			</a>
			<p class="kboard-modern-gallery-latest-title kboard-modern-gallery-cut-strings"><a href="<?php echo esc_url($url->getDocumentURLWithUID($content->uid))?>"><?php echo $content->title?></a></p>
		</div>
	<?php endwhile?>
</div>