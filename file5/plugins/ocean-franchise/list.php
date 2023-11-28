<div id="kboard-ocean-franchise-list">
	<!-- 검색폼 시작 -->
	<div class="kboard-header">
		<div class="kboard-map-v2">
			<?php if(kboard_category1()):?>
				<img src="<?php echo $skin_path?>/images/<?php echo kboard_ocean_franchise_branch(kboard_category1())?>">
			<?php else:?>
				<img src="<?php echo $skin_path?>/images/map-v2.png">
			<?php endif?>
			<div class="map-area" style="left:8%;top:13%;" data-map="<?php echo $skin_path?>/images/map-v2-incheon.png"><a href="<?php echo $url->set('category1', '인천')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>"><?php echo __('Incheon', 'kboard-ocean-franchise')?></a></div>
			<div class="map-area" style="left:20%;top:16%;" data-map="<?php echo $skin_path?>/images/map-v2-seoul.png"><a href="<?php echo $url->set('category1', '서울')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>"><?php echo __('Seoul', 'kboard-ocean-franchise')?></a></div>
			<div class="map-area" style="left:20%;top:23%;" data-map="<?php echo $skin_path?>/images/map-v2-gyeonggi.png"><a href="<?php echo $url->set('category1', '경기도')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>"><?php echo __('Gyeonggi', 'kboard-ocean-franchise')?></a></div>
			<div class="map-area" style="left:44%;top:14%;" data-map="<?php echo $skin_path?>/images/map-v2-gangwon.png"><a href="<?php echo $url->set('category1', '강원도')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>"><?php echo __('Gangwon', 'kboard-ocean-franchise')?></a></div>
			<div class="map-area" style="left:32%;top:28%;" data-map="<?php echo $skin_path?>/images/map-v2-chungbuk.png"><a href="<?php echo $url->set('category1', '충청북도')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>"><?php echo __('Chungbuk', 'kboard-ocean-franchise')?></a></div>
			<div class="map-area" style="left:2%;top:35%;" data-map="<?php echo $skin_path?>/images/map-v2-chungnam.png"><a href="<?php echo $url->set('category1', '충청남도')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>"><?php echo __('Chungnam', 'kboard-ocean-franchise')?></a></div>
			<div class="map-area" style="left:22%;top:33%;" data-map="<?php echo $skin_path?>/images/map-v2-sejong.png"><a href="<?php echo $url->set('category1', '세종')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>"><?php echo __('Sejong', 'kboard-ocean-franchise')?></a></div>
			<div class="map-area" style="left:22%;top:40%;" data-map="<?php echo $skin_path?>/images/map-v2-daejeon.png"><a href="<?php echo $url->set('category1', '대전')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>"><?php echo __('Daejeon', 'kboard-ocean-franchise')?></a></div>
			<div class="map-area" style="left:50%;top:40%;" data-map="<?php echo $skin_path?>/images/map-v2-gyeongbuk.png"><a href="<?php echo $url->set('category1', '경상북도')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>"><?php echo __('Gyeongbuk', 'kboard-ocean-franchise')?></a></div>
			<div class="map-area" style="left:16%;top:54%;" data-map="<?php echo $skin_path?>/images/map-v2-jeollabuk.png"><a href="<?php echo $url->set('category1', '전라북도')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>"><?php echo __('Jeollabuk', 'kboard-ocean-franchise')?></a></div>
			<div class="map-area" style="left:48%;top:52%;" data-map="<?php echo $skin_path?>/images/map-v2-daegu.png"><a href="<?php echo $url->set('category1', '대구')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>"><?php echo __('Daegu', 'kboard-ocean-franchise')?></a></div>
			<div class="map-area" style="left:67%;top:57%;" data-map="<?php echo $skin_path?>/images/map-v2-ulsan.png"><a href="<?php echo $url->set('category1', '울산')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>"><?php echo __('Ulsan', 'kboard-ocean-franchise')?></a></div>
			<div class="map-area" style="left:41%;top:60%;" data-map="<?php echo $skin_path?>/images/map-v2-gyeongnam.png"><a href="<?php echo $url->set('category1', '경상남도')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>"><?php echo __('Gyeongnam', 'kboard-ocean-franchise')?></a></div>
			<div class="map-area" style="left:10%;top:73%;" data-map="<?php echo $skin_path?>/images/map-v2-jeollanam.png"><a href="<?php echo $url->set('category1', '전라남도')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>"><?php echo __('Jeollanam', 'kboard-ocean-franchise')?></a></div>
			<div class="map-area" style="left:16%;top:66%;" data-map="<?php echo $skin_path?>/images/map-v2-gwangju.png"><a href="<?php echo $url->set('category1', '광주')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>"><?php echo __('Gwangju', 'kboard-ocean-franchise')?></a></div>
			<div class="map-area" style="left:64%;top:64%;" data-map="<?php echo $skin_path?>/images/map-v2-busan.png"><a href="<?php echo $url->set('category1', '부산')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>"><?php echo __('Busan', 'kboard-ocean-franchise')?></a></div>
			<div class="map-area" style="left:10%;top:92%;" data-map="<?php echo $skin_path?>/images/map-v2-jeju.png"><a href="<?php echo $url->set('category1', '제주도')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>"><?php echo __('Jeju', 'kboard-ocean-franchise')?></a></div>
		</div>
		
		<form id="kboard-search-form" method="get" action="<?php echo $url->getBoardList()?>">
			<?php echo $url->set('category1', '')->set('category2', '')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->toInput()?>
			
			<div class="kboard-search">
				<h3 class="kboard-header-title"><?php echo __('Search', 'kboard')?></h3>
				<div class="kboard-target">
					<select name="target">
						<option value=""><?php echo __('Address', 'kboard-ocean-franchise')?></option>
						<option value="member_display"<?php if(kboard_target() == 'member_display'):?> selected<?php endif?>><?php echo __('Branch', 'kboard-ocean-franchise')?></option>
					</select>
					<input type="text" name="keyword" value="<?php echo kboard_keyword()?>">
					<button type="submit" class="kboard-ocean-franchise-button-small"><?php echo __('Search', 'kboard')?></button>
				</div>
			</div>
			
			<div class="kboard-branch">
				<h3 class="kboard-header-title"><?php echo __('Guide', 'kboard-ocean-franchise')?></h3>
				<a class="kboard-branch-button<?php if(!kboard_category1()):?> active<?php endif?>" href="<?php echo $url->set('category1', '')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>" title="전체"><?php echo __('All', 'kboard-ocean-franchise')?></a>
				<a class="kboard-branch-button<?php if(kboard_category1()=='서울'):?> active<?php endif?>" href="<?php echo $url->set('category1', '서울')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>" title="서울"><?php echo __('Seoul', 'kboard-ocean-franchise')?></a>
				<a class="kboard-branch-button<?php if(kboard_category1()=='부산'):?> active<?php endif?>" href="<?php echo $url->set('category1', '부산')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>" title="부산"><?php echo __('Busan', 'kboard-ocean-franchise')?></a>
				<a class="kboard-branch-button<?php if(kboard_category1()=='대구'):?> active<?php endif?>" href="<?php echo $url->set('category1', '대구')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>" title="대구"><?php echo __('Daegu', 'kboard-ocean-franchise')?></a>
				<a class="kboard-branch-button<?php if(kboard_category1()=='인천'):?> active<?php endif?>" href="<?php echo $url->set('category1', '인천')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>" title="인천"><?php echo __('Incheon', 'kboard-ocean-franchise')?></a>
				<a class="kboard-branch-button<?php if(kboard_category1()=='광주'):?> active<?php endif?>" href="<?php echo $url->set('category1', '광주')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>" title="광주"><?php echo __('Gwangju', 'kboard-ocean-franchise')?></a>
				<a class="kboard-branch-button<?php if(kboard_category1()=='대전'):?> active<?php endif?>" href="<?php echo $url->set('category1', '대전')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>" title="대전"><?php echo __('Daejeon', 'kboard-ocean-franchise')?></a>
				<a class="kboard-branch-button<?php if(kboard_category1()=='울산'):?> active<?php endif?>" href="<?php echo $url->set('category1', '울산')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>" title="울산"><?php echo __('Ulsan', 'kboard-ocean-franchise')?></a>
				<a class="kboard-branch-button<?php if(kboard_category1()=='세종'):?> active<?php endif?>" href="<?php echo $url->set('category1', '세종')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>" title="세종"><?php echo __('Sejong', 'kboard-ocean-franchise')?></a>
				<a class="kboard-branch-button<?php if(kboard_category1()=='경기도'):?> active<?php endif?>" href="<?php echo $url->set('category1', '경기도')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>" title="경기도"><?php echo __('Gyeonggi', 'kboard-ocean-franchise')?></a>
				<a class="kboard-branch-button<?php if(kboard_category1()=='경상남도'):?> active<?php endif?>" href="<?php echo $url->set('category1', '경상남도')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>" title="경상남도"><?php echo __('Gyeongnam', 'kboard-ocean-franchise')?></a>
				<a class="kboard-branch-button<?php if(kboard_category1()=='경상북도'):?> active<?php endif?>" href="<?php echo $url->set('category1', '경상북도')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>" title="경상북도"><?php echo __('Gyeongbuk', 'kboard-ocean-franchise')?></a>
				<a class="kboard-branch-button<?php if(kboard_category1()=='전라남도'):?> active<?php endif?>" href="<?php echo $url->set('category1', '전라남도')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>" title="전라남도"><?php echo __('Jeollanam', 'kboard-ocean-franchise')?></a>
				<a class="kboard-branch-button<?php if(kboard_category1()=='전라북도'):?> active<?php endif?>" href="<?php echo $url->set('category1', '전라북도')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>" title="전라북도"><?php echo __('Jeollabuk', 'kboard-ocean-franchise')?></a>
				<a class="kboard-branch-button<?php if(kboard_category1()=='충청남도'):?> active<?php endif?>" href="<?php echo $url->set('category1', '충청남도')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>" title="충청남도"><?php echo __('Chungnam', 'kboard-ocean-franchise')?></a>
				<a class="kboard-branch-button<?php if(kboard_category1()=='충청북도'):?> active<?php endif?>" href="<?php echo $url->set('category1', '충청북도')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>" title="충청북도"><?php echo __('Chungbuk', 'kboard-ocean-franchise')?></a>
				<a class="kboard-branch-button<?php if(kboard_category1()=='강원도'):?> active<?php endif?>" href="<?php echo $url->set('category1', '강원도')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>" title="강원도"><?php echo __('Gangwon', 'kboard-ocean-franchise')?></a>
				<a class="kboard-branch-button<?php if(kboard_category1()=='제주도'):?> active<?php endif?>" href="<?php echo $url->set('category1', '제주도')->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->tostring()?>" title="제주도"><?php echo __('Jeju', 'kboard-ocean-franchise')?></a>
			</div>
		</form>
	</div>
	<!-- 검색폼 끝 -->
	
	<!-- 리스트 시작 -->
	<div class="kboard-list">
		<table>
			<thead>
				<tr>
					<td class="kboard-list-uid"><?php echo __('Number', 'kboard')?></td>
					<td class="kboard-list-branch"><?php echo __('Branch', 'kboard-ocean-franchise')?></td>
					<td class="kboard-list-title"><?php echo __('Address', 'kboard-ocean-franchise')?></td>
					<td class="kboard-list-tel"><?php echo __('Contact', 'kboard-ocean-franchise')?></td>
				</tr>
			</thead>
			<tbody>
				<?php while($content = $list->hasNextNotice()):?>
				<tr class="kboard-list-notice">
					<td class="kboard-list-uid"><?php echo __('Notice', 'kboard')?></td>
					<td class="kboard-list-branch"><?php echo apply_filters('kboard_user_display', $content->member_display, $content->member_uid, $content->member_display, 'kboard', $boardBuilder)?></td>
					<td class="kboard-list-title">
						<div class="kboard-ocean-franchise-cut-strings">
							<a href="<?php echo $url->getDocumentURLWithUID($content->uid)?>">
								<?php if($content->secret):?><img src="<?php echo $skin_path?>/images/icon_lock.png" alt="<?php echo __('Secret', 'kboard')?>"><?php endif?>
								<?php echo $content->title?>
							</a>
							<span class="kboard-comments-count"><?php echo $content->getCommentsCount()?></span>
						</div>
					</td>
					<td class="kboard-list-tel"><?php echo $content->option->tel?></td>
				</tr>
				<?php endwhile?>
				<?php while($content = $list->hasNext()):?>
				<tr>
					<td class="kboard-list-uid"><?php echo $list->index()?></td>
					<td class="kboard-list-branch"><?php echo apply_filters('kboard_user_display', $content->member_display, $content->member_uid, $content->member_display, 'kboard', $boardBuilder)?></td>
					<td class="kboard-list-title">
						<div class="kboard-ocean-franchise-cut-strings">
							<a href="<?php echo $url->getDocumentURLWithUID($content->uid)?>">
								<?php if($content->secret):?><img src="<?php echo $skin_path?>/images/icon_lock.png" alt="<?php echo __('Secret', 'kboard')?>"><?php endif?>
								<?php echo $content->title?>
							</a>
							<span class="kboard-comments-count"><?php echo $content->getCommentsCount()?></span>
						</div>
					</td>
					<td class="kboard-list-tel"><?php echo $content->option->tel?></td>
				</tr>
				<?php endwhile?>
			</tbody>
		</table>
	</div>
	<!-- 리스트 끝 -->
	
	<!-- 페이징 시작 -->
	<div class="kboard-pagination">
		<ul class="kboard-pagination-pages">
			<?php echo kboard_pagination($list->page, $list->total, $list->rpp)?>
		</ul>
	</div>
	<!-- 페이징 끝 -->
	
	<?php if($board->isWriter()):?>
	<!-- 버튼 시작 -->
	<div class="kboard-control">
		<a href="<?php echo $url->getContentEditor()?>" class="kboard-ocean-franchise-button-small"><?php echo __('Register Branch', 'kboard-ocean-franchise')?></a>
	</div>
	<!-- 버튼 끝 -->
	<?php endif?>
	
	<?php if($board->contribution()):?>
	<div class="kboard-ocean-franchise-poweredby">
		<a href="https://www.cosmosfarm.com/products/kboard" onclick="window.open(this.href);return false;" title="<?php echo __('KBoard is the best community software available for WordPress', 'kboard')?>">Powered by KBoard</a>
	</div>
	<?php endif?>
</div>

<?php wp_enqueue_script('kboard-ocean-franchise-list', "{$skin_path}/list.js", array(), KBOARD_VERSION, true)?>