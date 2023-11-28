<div id="kboard-play-video-latest"<?php if(kboard_play_video_latest($board)):?> class="<?php echo esc_attr(kboard_play_video_latest($board))?>"<?php endif?>>
	<ul class="kboard-list">
		<?php while($content = $list->hasNextNotice()):?>
		<li class="kboard-list-item<?php if($content->uid == kboard_uid()):?> kboard-selected<?php endif?>">
			<a href="<?php echo esc_url($url->getDocumentURLWithUID($content->uid))?>#kboard-document">
				
				<?php if($content->getThumbnail(330, 160)):?>
					<div class="kboard-list-thumbnail" style="background-image:url(<?php echo esc_url($content->getThumbnail(330, 160))?>)"></div>
				<?php elseif($content->option->youtube_id):?>
					<div class="kboard-list-thumbnail" style="background-image:url(<?php echo esc_url($content->option->youtube_thumbnail_url)?>)"></div>
				<?php elseif($content->option->vimeo_id):?>
					<div class="kboard-list-thumbnail" style="background-image:url(<?php echo esc_url($content->option->vimeo_thumbnail_url)?>)"></div>
				<?php else:?>
					<div class="kboard-list-thumbnail"></div>
				<?php endif?>
				
				<div class="kboard-list-title kboard-ell">
					<?php if($content->secret):?><img src="<?php echo $skin_path?>/images/icon-lock.png" alt="<?php echo __('Secret', 'kboard')?>"><?php endif?>
					<?php echo $content->title?>
					<?php echo $content->getCommentsCount()?>
				</div>
				
				<div class="kboard-video-info">
					<span><?php echo $content->getUserDisplay(sprintf('%s %s', get_avatar($content->getUserID(), 16, '', $content->getUserName()), $content->getUserName()))?></span>
					<div class="kboard-ell">
						<span><?php echo __('Views', 'kboard')?> <?php echo number_format(intval($content->view))?></span>
						<span>·</span>
						<span><?php echo date('Y-m-d', strtotime($content->date))?></span>
					</div>
				</div>
				
				<div class="kboard-list-vote"><img src="<?php echo $skin_path?>/images/icon-heart.png" alt="<?php echo __('Votes', 'kboard')?>"> <?php echo intval($content->vote)?></div>
				
				<div class="kboard-selected-background">
					<div class="kboard-selected-wrap">
						<div class="kboard-selected-table">
							<div class="kboard-selected-cell">
								<img src="<?php echo $skin_path?>/images/icon-play.png" alt="<?php echo __('Play', 'kboard')?>">
								<?php echo __('Play', 'kboard')?>
							</div>
						</div>
					</div>
				</div>
			</a>
		</li>
		<?php endwhile?>
		<?php while($content = $list->hasNext()):?>
		<li class="kboard-list-item<?php if($content->uid == kboard_uid()):?> kboard-selected<?php endif?>">
			<a href="<?php echo esc_url($url->getDocumentURLWithUID($content->uid))?>#kboard-document">
				
				<?php if($content->getThumbnail(330, 160)):?>
					<div class="kboard-list-thumbnail" style="background-image:url(<?php echo esc_url($content->getThumbnail(330, 160))?>)"></div>
				<?php elseif($content->option->youtube_id):?>
					<div class="kboard-list-thumbnail" style="background-image:url(<?php echo esc_url($content->option->youtube_thumbnail_url)?>)"></div>
				<?php elseif($content->option->vimeo_id):?>
					<div class="kboard-list-thumbnail" style="background-image:url(<?php echo esc_url($content->option->vimeo_thumbnail_url)?>)"></div>
				<?php else:?>
					<div class="kboard-list-thumbnail"></div>
				<?php endif?>
				
				<div class="kboard-list-title kboard-ell">
					<?php if($content->secret):?><img src="<?php echo $skin_path?>/images/icon-lock.png" alt="<?php echo __('Secret', 'kboard')?>"><?php endif?>
					<?php echo $content->title?>
					<?php echo $content->getCommentsCount()?>
				</div>
				
				<div class="kboard-video-info">
					<span><?php echo $content->getUserDisplay(sprintf('%s %s', get_avatar($content->getUserID(), 16, '', $content->getUserName()), $content->getUserName()))?></span>
					<div class="kboard-ell">
						<span><?php echo __('Views', 'kboard')?> <?php echo number_format(intval($content->view))?></span>
						<span>·</span>
						<span><?php echo date('Y-m-d', strtotime($content->date))?></span>
					</div>
				</div>
				
				<div class="kboard-list-vote"><img src="<?php echo $skin_path?>/images/icon-heart.png" alt="<?php echo __('Votes', 'kboard')?>"> <?php echo intval($content->vote)?></div>
				
				<div class="kboard-selected-background">
					<div class="kboard-selected-wrap">
						<div class="kboard-selected-table">
							<div class="kboard-selected-cell">
								<img src="<?php echo $skin_path?>/images/icon-play.png" alt="<?php echo __('Play', 'kboard')?>">
								<?php echo __('Play', 'kboard')?>
							</div>
						</div>
					</div>
				</div>
			</a>
		</li>
		<?php endwhile?>
	</ul>
</div>