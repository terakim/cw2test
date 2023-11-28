<div id="kboard-ocean-franchise-latest">
	<table>
		<thead>
			<tr>
				<th class="kboard-latest-title"><?php echo __('Title', 'kboard')?></th>
				<th class="kboard-latest-date"><?php echo __('Date', 'kboard')?></th>
			</tr>
		</thead>
		<tbody>
			<?php while($content = $list->hasNext()):?>
			<tr>
				<td class="kboard-latest-title">
					<div class="cut_strings">
						<a href="<?php echo $url->getDocumentURLWithUID($content->uid)?>"><?php echo $content->title?></a>
					</div>
				</td>
				<td class="kboard-latest-date"><?php echo $content->getDate()?></td>
			</tr>
			<?php endwhile?>
		</tbody>
	</table>
</div>