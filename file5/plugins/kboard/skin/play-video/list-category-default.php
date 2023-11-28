<div class="kboard-header">
	<?php if($board->use_category == 'yes'):?>
	<form id="kboard-search-form" method="get" action="<?php echo $url->set('mod', 'list')->toString()?>">
		<?php echo $url->set('pageid', '1')->set('mod', 'list')->toInput()?>
		<div class="kboard-category">
			<?php if($board->initCategory1()):?>
				<select name="category1" onchange="jQuery('#kboard-search-form').submit();">
					<option value=""><?php echo __('All', 'kboard')?></option>
					<?php while($board->hasNextCategory()):?>
					<option value="<?php echo $board->currentCategory()?>"<?php if($_GET['category1'] == $board->currentCategory()):?> selected="selected"<?php endif?>><?php echo $board->currentCategory()?></option>
					<?php endwhile?>
				</select>
			<?php endif?>
			<?php if($board->initCategory2()):?>
				<select name="category2" onchange="jQuery('#kboard-search-form').submit();">
					<option value=""><?php echo __('All', 'kboard')?></option>
					<?php while($board->hasNextCategory()):?>
					<option value="<?php echo $board->currentCategory()?>"<?php if($_GET['category2'] == $board->currentCategory()):?> selected="selected"<?php endif?>><?php echo $board->currentCategory()?></option>
					<?php endwhile?>
				</select>
			<?php endif?>
		</div>
	</form>
	<?php endif?>
</div>