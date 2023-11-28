<div id="kboard-document">
	<div id="kboard-play-video-document">
		
		<div class="kboard-play-video-wrap">
			<?php if($content->option->youtube_id):?>
			<div class="kboard-play-video-container" style="<?php if($content->option->video_view):?>padding-bottom: 75%;<?php else:?>padding-bottom: 56.25%;<?php endif?>">
				<iframe width="560" height="315" src="<?php echo esc_url("https://www.youtube.com/embed/{$content->option->youtube_id}?autoplay={$content->option->autoplay}")?>" frameborder="0" allow="autoplay;" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
			</div>
			
			<?php elseif($content->option->vimeo_id):?>
			<div class="kboard-play-video-container" style="<?php if($content->option->video_view):?>padding-bottom: 75%;<?php else:?>padding-bottom: 56.25%;<?php endif?>">
				<iframe src="<?php echo esc_url("https://player.vimeo.com/video/{$content->option->vimeo_id}?color=ffffff&title=0&byline=0&portrait=0&autoplay={$content->option->autoplay}")?>" frameborder="0" allow="autoplay;" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
			</div>
			
			<?php elseif(count((array)$content->attach) > 0):?>
				<?php foreach($content->attach as $key=>$attach): $extension = strtolower(pathinfo($attach[0], PATHINFO_EXTENSION));?>
					<?php if(in_array($extension, array('mp4'))):?>
					<div class="kboard-play-video-container" style="<?php if($content->option->video_view):?>padding-bottom: 75%;<?php else:?>padding-bottom: 56.25%;<?php endif?>">
						<?php echo do_shortcode('[video src="'.site_url($attach[0]).'" '.($content->option->autoplay?'autoplay="on"':'').']')?>
					</div>
					<?php else: $download[$key] = $attach; endif?>
				<?php endforeach?>
			
			<?php elseif($content->option->video_url):?>
				<div class="kboard-play-video-container" style="<?php if($content->option->video_view):?>padding-bottom: 75%;<?php else:?>padding-bottom: 56.25%;<?php endif?>">
					<?php echo do_shortcode('[video src="'.esc_url($content->option->video_url).'" '.($content->option->autoplay?'autoplay="on"':'').']')?>
				</div>
			<?php endif?>
		</div>
		
		<div class="kboard-document-wrap" itemscope itemtype="http://schema.org/Article">
			
			<div class="kboard-document-top">
				<div class="left">
					<div class="kboard-title" itemprop="name">
						<h1><?php echo $content->title?></h1>
					</div>
					
					<div class="kboard-user">
						<?php echo $content->getUserDisplay(sprintf('%s %s', get_avatar($content->getUserID(), 24, '', $content->getUserName()), $content->getUserName()))?>
					</div>
					
					<div class="kboard-attr">
						<span class="kboard-attr-name"><?php echo __('Date', 'kboard')?></span>
						<?php echo date('Y-m-d H:i', strtotime($content->date))?>
					</div>
					
					<div class="kboard-attr">
						<span class="kboard-attr-name"><?php echo __('Views', 'kboard')?></span>
						<?php echo $content->view?>
					</div>
					
					<?php
					if($content->category1 || $content->category2):
						if($content->category1) $category[] = '<a href="' . esc_url($url->set('mod', 'list')->set('pageid', '1')->set('category1', $content->category1)->set('category2', '')->toString()) . '">' . $content->category1 . '</a>';
						if($content->category2) $category[] = '<a href="' . esc_url($url->set('mod', 'list')->set('pageid', '1')->set('category1', '')->set('category2', $content->category2)->toString()) . '">' . $content->category2 . '</a>';
					?>
					<div class="kboard-attr">
						<span class="kboard-attr-name"><?php echo __('Category', 'kboard')?></span>
						<?php echo implode(', ', $category)?>
					</div>
					<?php endif?>
					
					<?php if($content->option->tree_category_1):?>
					<div class="kboard-attr">
						<span class="kboard-attr-name"><?php echo __('Category', 'kboard')?></span>
						<?php
						for($i=1; $i<=$content->getTreeCategoryDepth(); $i++){
							echo ($i < $content->getTreeCategoryDepth()) ? $content->option->{'tree_category_'.$i} . ', ' : $content->option->{'tree_category_'.$i};
						}
						?>
					</div>
					<?php endif?>
				</div>
				
				<div class="right">
					<div class="kboard-vote">
						<a href="#" onclick="return kboard_document_like(this)" data-uid="<?php echo $content->uid?>" title="<?php echo __('Like', 'kboard')?>">
							<div><img src="<?php echo $skin_path?>/images/icon-heart.png" alt="<?php echo __('Like', 'kboard')?>"></div>
							<div class="kboard-document-like-count"><?php echo intval($content->like)?></div>
						</a>
					</div>
				</div>
			</div>
			
			<div class="kboard-content" itemprop="description">
				<div class="content-view">
					<?php echo $content->getDocumentOptionsHTML()?>
					<?php echo $content->content?>
				</div>
			</div>
			
			<div class="kboard-document-action">
				<div class="right">
					<button type="button" class="kboard-button-action kboard-button-print" onclick="kboard_document_print('<?php echo esc_url($url->getDocumentPrint($content->uid))?>')" title="<?php echo __('Print', 'kboard')?>"><?php echo __('Print', 'kboard')?></button>
				</div>
			</div>
			
			<?php if(isset($download) && $download):?>
			<div class="kboard-attach">
				<?php foreach($download as $key=>$attach):?>
				<button type="button" class="kboard-button-action kboard-button-download" onclick="window.location.href='<?php echo esc_url($url->getDownloadURLWithAttach($content->uid, $key))?>'" title="<?php echo sprintf(__('Download %s', 'kboard'), esc_attr($attach[1]))?>"><?php echo $attach[1]?></button>
				<?php endforeach?>
			</div>
			<?php endif?>
		</div>
		
		<?php if($board->isComment()):?>
		<div class="kboard-comments-area"><?php echo $board->buildComment($content->uid)?></div>
		<?php endif?>
		
		<div class="kboard-document-navi">
			<div class="kboard-prev-document">
				<?php
				$bottom_content_uid = $content->getPrevUID();
				if($bottom_content_uid):
				$bottom_content = new KBContent();
				$bottom_content->initWithUID($bottom_content_uid);
				?>
				<a href="<?php echo esc_url($url->getDocumentURLWithUID($bottom_content_uid))?>">
					<span class="navi-arrow">«</span>
					<span class="navi-document-title cut_strings"><?php echo $bottom_content->title?></span>
				</a>
				<?php endif?>
			</div>
			
			<div class="kboard-next-document">
				<?php
				$top_content_uid = $content->getNextUID();
				if($top_content_uid):
				$top_content = new KBContent();
				$top_content->initWithUID($top_content_uid);
				?>
				<a href="<?php echo esc_url($url->getDocumentURLWithUID($top_content_uid))?>">
					<span class="navi-document-title cut_strings"><?php echo $top_content->title?></span>
					<span class="navi-arrow">»</span>
				</a>
				<?php endif?>
			</div>
		</div>
		
		<div class="kboard-control">
			<div class="left">
				<a href="<?php echo esc_url($url->getBoardList())?>" class="kboard-play-video-button-small"><?php echo __('List', 'kboard')?></a>
			</div>
			<?php if($board->isEditor($content->member_uid) || $board->permission_write=='all'):?>
			<div class="right">
				<a href="<?php echo esc_url($url->getContentEditor($content->uid))?>" class="kboard-play-video-button-small"><?php echo __('Edit', 'kboard')?></a>
				<a href="<?php echo esc_url($url->getContentRemove($content->uid))?>" class="kboard-play-video-button-small" onclick="return confirm('<?php echo __('Are you sure you want to delete?', 'kboard')?>');"><?php echo __('Delete', 'kboard')?></a>
			</div>
			<?php endif?>
		</div>
		
		<?php if($board->contribution() && !$board->meta->always_view_list):?>
		<div class="kboard-play-video-poweredby">
			<a href="https://www.cosmosfarm.com/products/kboard" onclick="window.open(this.href);return false;" title="<?php echo __('KBoard is the best community software available for WordPress', 'kboard')?>">Powered by KBoard</a>
		</div>
		<?php endif?>
	</div>
</div>