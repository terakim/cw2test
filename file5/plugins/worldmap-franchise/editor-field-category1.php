<div class="kboard-attr-row">
	<label class="attr-name"><?php echo __('Area', 'kboard-worldmap-franchise')?></label>
	<div class="attr-value">
		<select name="category1">
			<option value=""><?php echo __('Select', 'kboard')?></option>
			<?php foreach(kboard_worldmap_franchise_branch_list() as $key=>$item):?>
			<option value="<?php echo $key?>"<?php if($content->category1 == $key):?> selected<?php endif?>><?php echo $item['name']?></option>
			<?php endforeach?>
		</select>
	</div>
</div>