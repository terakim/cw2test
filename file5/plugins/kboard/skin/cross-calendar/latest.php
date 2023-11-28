<?php
$kboard_calendar_type = kboard_get_calendar_latest_type();
$kboard_calendar_year = kboard_get_calendar_latest_year();
$kboard_calendar_month = kboard_get_calendar_latest_month();

/* 이전 년도, 다음 년도 구하기 */
switch($kboard_calendar_month){
    case 1:
        $prev_year = $kboard_calendar_year - 1;
        $next_year = $kboard_calendar_year;
        break;
    case 12:
        $next_year = $kboard_calendar_year + 1;
        $prev_year = $kboard_calendar_year;
        break;
    default:
        $prev_year = $kboard_calendar_year;
        $next_year = $kboard_calendar_year;
        break;
}

$prev_month = ($kboard_calendar_month == 1) ? 12 : ($kboard_calendar_month - 1); // 이전 달
$next_month = ($kboard_calendar_month == 12) ? 1 : ($kboard_calendar_month + 1); // 다음 달
$last_day = date('t', mktime(0, 0, 0, $kboard_calendar_month, 1, $kboard_calendar_year)); // 해당 달의 마지막 날을 구함
$calendar_start_day = date('w', mktime(0, 0, 0, $kboard_calendar_month, 1, $kboard_calendar_year)); // 해당 달의 시작요일 구함(0~6)

$week = array(__('Sunday', 'kboard-cross-calendar'), __('Monday', 'kboard-cross-calendar'), __('Tuesday', 'kboard-cross-calendar'), __('Wednesday', 'kboard-cross-calendar'), __('Thursday', 'kboard-cross-calendar'), __('Friday', 'kboard-cross-calendar'), __('Saturday', 'kboard-cross-calendar'));
$week_mobile =  array(__('Sun', 'kboard-cross-calendar'), __('Mon', 'kboard-cross-calendar'), __('Tue', 'kboard-cross-calendar'), __('Wed', 'kboard-cross-calendar'), __('Thu', 'kboard-cross-calendar'), __('Fri', 'kboard-cross-calendar'), __('Sat', 'kboard-cross-calendar'));

$prev_day_count = date('t', mktime(0, 0, 0, $kboard_calendar_month-1, 1, $kboard_calendar_year)) - $calendar_start_day + 1; // 지난 달 총 일수
$now_day_count = 1;
$next_day_count = 1;

$total_days = ceil(($last_day + $calendar_start_day) / 7);
$today = date('Y-m-d', current_time('timestamp'));

// 달력에 표시될 시작 날짜
$calendar_start_ymd = date('Y-m-d', strtotime("{$kboard_calendar_year}-{$kboard_calendar_month}-01"));
if($calendar_start_day != 0){
	$calendar_start_ymd = date('Y-m-d', mktime(0, 0, 0, $kboard_calendar_month-1, $prev_day_count, $kboard_calendar_year));
}

// 달력에 표시될 마지막 날짜
$calendar_end_ymd = date('Y-m-d', strtotime("{$kboard_calendar_year}-{$kboard_calendar_month}-{$last_day}"));
$next_month_days = 6 - date('w', mktime(0, 0, 0, $kboard_calendar_month, $last_day, $kboard_calendar_year));
if($next_month_days != 0){
	$calendar_end_ymd = date('Y-m-d', mktime(0, 0, 0, $kboard_calendar_month+1, $next_month_days, $kboard_calendar_year));
}

$list->rpp(9999);
$list->page(1);
$list->setSearchOption(array(
		'relation'=>'OR',
		array(
				'relation'=>'AND',
				array('key'=>'start_date', 'value'=>$calendar_start_ymd, 'compare'=>'>='),
				array('key'=>'end_date', 'value'=>$calendar_end_ymd, 'compare'=>'<=')
		),
		array(
				'relation'=>'AND',
				array('key'=>'start_date', 'value'=>$calendar_start_ymd, 'compare'=>'<'),
				array('key'=>'end_date', 'value'=>$calendar_start_ymd, 'compare'=>'>=')
		),
		array(
				'relation'=>'AND',
				array('key'=>'start_date', 'value'=>$calendar_end_ymd, 'compare'=>'<='),
				array('key'=>'end_date', 'value'=>$calendar_end_ymd, 'compare'=>'>')
		),
));
$list->getList('', '', true);

$calendar_event_rows = array();
while($content = $list->hasNext()){
	$start_time = $content->option->start_time ? $content->option->start_time : '00:00';
	$calendar_event_rows["{$content->option->start_date} {$start_time}"][] = $content;
}
?>
<div id="kboard-cross-calendar-latest">
	<?php
	$board_id = '';
	$latestview_id = '';
	
	if(isset($latest) && $latest['type'] == 'latestview'){
		$latestview_id = $latest['id'];
		$board_url = site_url();
	}
	else{
		$board_id = $board->id;
	}
	?>
	<input type="hidden" name="kboard_calendar_latest_type" value="<?php echo $kboard_calendar_type?>">
	<input type="hidden" name="kboard_calendar_latest_board_id" value="<?php echo $board_id?>">
	<input type="hidden" name="kboard_calendar_latest_latestview_id" value="<?php echo $latestview_id?>">
	<input type="hidden" name="kboard_calendar_latest_board_url" value="<?php echo $board_url?>">
	
	<div class="kboard-cross-calendar-list">
		<div class="kboard-header">
			<div class="kboard-search-day-form">
				<form method="get">
					<button type="button" onclick="kboard_calendar_latest_template('<?php echo $prev_year?>', '<?php echo $prev_month?>', '<?php echo $kboard_calendar_type?>');" class="kboard-cross-calendar-arrow arrow-left">
						<img src="<?php echo $skin_path?>/images/icon-arrow-left.png" alt=""  title="<?php echo __('Previous month', 'kboard-cross-calendar')?>">
					</button>
					<select name="kboard_calendar_latest_year" class="kboard-search-year" title="<?php echo __('Select year', 'kboard-cross-calendar')?>" onchange="kboard_calendar_latest_template(this.value, '<?php echo $kboard_calendar_month?>', '<?php echo $kboard_calendar_type?>');">
						<?php for($select_year=1900; $select_year<=2100; $select_year++):?>
						<option value="<?php echo $select_year?>"<?php if($select_year == $kboard_calendar_year):?> selected<?php endif?>><?php echo $select_year?></option>
						<?php endfor?>
					</select>
					<span class="kboard-cross-calendar-sep"> . </span>
					<select name="kboard_calendar_latest_month" class="kboard-search-month" title="<?php echo __('Select month', 'kboard-cross-calendar')?>" onchange="kboard_calendar_latest_template('<?php echo $kboard_calendar_year?>', this.value, '<?php echo $kboard_calendar_type?>');">
					<?php for($select_month=1; $select_month<=12; $select_month++):?>
						<option value="<?php echo $select_month ?>" <?php if($select_month == $kboard_calendar_month):?>selected<?php endif?>>
						<?php echo sprintf("%02d", $select_month)?></option>
					<?php endfor?>
					</select>
					<button type="button" onclick="kboard_calendar_latest_template('<?php echo $next_year?>', '<?php echo $next_month?>', '<?php echo $kboard_calendar_type?>');" class="kboard-cross-calendar-arrow arrow-right">
						<img src="<?php echo $skin_path?>/images/icon-arrow-right.png" alt=""  title="<?php echo __('Next month', 'kboard-cross-calendar')?>">
					</button>
				</form>
			</div>
		</div>
		
		<div class="kboard-change-button-group">
			<div class="kboard-change-button-align">
				<button type="button" onclick="kboard_calendar_latest_template('<?php echo $kboard_calendar_year?>', '<?php echo $kboard_calendar_month?>', 'calendar');" class="kboard-change-button <?php echo $kboard_calendar_type=='calendar'?'active':''?>">
					<img src="<?php echo $skin_path?>/images/icon-calendar-style.png" alt=""  title="<?php echo __('Calendar style', 'kboard-cross-calendar')?>">
				</button>
				<button type="button" onclick="kboard_calendar_latest_template('<?php echo $kboard_calendar_year?>', '<?php echo $kboard_calendar_month?>', 'list');" class="kboard-change-button <?php echo $kboard_calendar_type=='list'?'active':''?>">
					<img src="<?php echo $skin_path?>/images/icon-list-style.png" alt=""  title="<?php echo __('List style', 'kboard-cross-calendar')?>">
				</button>
			</div>
		</div>
		
		<?php if($kboard_calendar_type == 'calendar'):?>
		<!-- 달력 화면 시작 -->
		<table class="kboard-calendar-table">
			<thead>
				<tr class="kboard-week-title">
					<?php for($day_of_the_week=0; $day_of_the_week<7; $day_of_the_week++):?>
					<th class="<?php echo kboard_get_calendar_day_of_the_week($day_of_the_week)?>">
						<span class="wide"><?php echo $week[$day_of_the_week]?></span>
						<span class="short"><?php echo $week_mobile[$day_of_the_week]?></span>
					</th>
					<?php endfor?>
				</tr>
			</thead>
			<tbody>
				<?php for($horizontal_line=0; $horizontal_line<$total_days; $horizontal_line++):?>
				<tr>
					<?php for($vertical_line=0; $vertical_line<7; $vertical_line++):
						$cell_index = (7 * $horizontal_line) + $vertical_line;
						$key = kboard_get_calendar_key($calendar_start_day, $cell_index, $now_day_count, $last_day, $kboard_calendar_year, $kboard_calendar_month, $prev_day_count, $next_day_count);?>
						<td class="<?php echo kboard_get_calendar_day_class($calendar_start_day, $cell_index, $now_day_count, $last_day, $kboard_calendar_year, $kboard_calendar_month)?> <?php if($today == kboard_get_calendar_ymd($calendar_start_day, $cell_index, $now_day_count, $last_day, $kboard_calendar_year, $kboard_calendar_month, $prev_day_count, $next_day_count)): echo 'calendar-column-today'; endif;?>">
							<div class="calendar-icon-day">
								<a href="<?php echo esc_url($url->set('mod', 'editor')->set('ymd', kboard_get_calendar_ymd($calendar_start_day, $cell_index, $now_day_count, $last_day, $kboard_calendar_year, $kboard_calendar_month, $prev_day_count, $next_day_count))->toString())?>" title="<?php echo __('Register Schedule', 'kboard-cross-calendar')?>">
									<?php
									if($calendar_start_day <= $cell_index && $now_day_count <= $last_day){
										echo $now_day_count++;
									}
									else if($cell_index < $calendar_start_day){
										echo $prev_day_count++;
									}
									else if($cell_index >= $last_day){
										echo $next_day_count++;
									}
									?>
								</a>
							</div>
							<?php
							$event_table_item_list = kboard_get_calendar_type_array($calendar_event_rows);
							if(isset($event_table_item_list[$key])):
								for($i=0; $i<count($event_table_item_list[$key]); $i++):
									$event_table_item = $event_table_item_list[$key][$i];
									?>
									<?php if($event_table_item == 'empty'):?>
										<div class="calendar-empty-time"></div>
									<?php else:?>
										<div class="calendar-event">
											<a href="<?php echo esc_url($url->getDocumentURLWithUID($event_table_item->uid))?>" title="<?php echo esc_attr($event_table_item->title)?>">
												<div class="calendar-event-time">
													<?php
													if(date('H:i', strtotime($event_table_item->option->start_time)) != '00:00' && date('H:i', strtotime($event_table_item->option->end_time)) != '00:00'){
														echo date('H:i', strtotime($event_table_item->option->start_time))?>~<?php echo date('H:i', strtotime($event_table_item->option->end_time));
													}
													?>
												</div>
												<div class="calendar-event-name" style="<?php if($event_table_item->option->color):?>background-color: <?php echo $event_table_item->option->color?>;<?php endif?> <?php echo kboard_get_calendar_white_background_style($event_table_item->option->color)?>">
													<!--<?php if($event_table_item->isNew()):?><span class="kboard-cross-calendar-new-notify">New</span><?php endif?>-->
													<?php if($event_table_item->secret):?><img src="<?php echo $skin_path?>/images/icon-lock.png" alt="<?php echo __('Secret', 'kboard')?>"><?php endif?>
                            						<?php echo $event_table_item->title?>
												</div>
											</a>
										</div>
									<?php
									endif;
								endfor;
							endif;
							?>
						</td>
					<?php endfor?>
				</tr>
				<?php endfor?>
			</tbody>
		</table>
		<!-- 달력 화면 끝 -->
		<?php endif?>
		
		<?php if($kboard_calendar_type == 'list'):?>
		<!-- 캘린더 리스트 화면 시작 -->
		<div id="kboard-mobile-calendar-list">
		<?php
		$is_empty_event = true;
		$event_item_list = kboard_get_calendar_list_type_array($calendar_event_rows);
		if($event_item_list):
			$year_month_check = ''; // 같은 년월인지 체크
			$calendar_selected_ym = date('Ym', mktime(0, 0, 0, $kboard_calendar_month, 1, $kboard_calendar_year));
			foreach($event_item_list as $group_date=>$group_list):
				if($kboard_calendar_type != 'search' && $calendar_selected_ym != date('Ym', strtotime($group_date))) continue;
				foreach($group_list as $event_item):
					if(strpos($year_month_check, date('Ym', strtotime($group_date))) === false):
					$year_month_check = $year_month_check . '/' . date('Ym', strtotime($group_date));
					$is_empty_event = false;
					?>
					<div class="kboard-mobile-calendar-header">
						<div class="kboard-mobile-calendar-header-left">
							<div class="kboard-mobile-calendar-header-month"><?php echo date('M', strtotime($group_date))?></div>
							<div class="kboard-mobile-calendar-header-year"><?php echo date('Y', strtotime($group_date))?></div>
						</div>
						<div class="kboard-mobile-calendar-header-right"><?php echo date('Y . m', strtotime($group_date))?></div>
					</div>
					<?php endif?>
					<div class="kboard-mobile-calendar-event-list<?php echo ($group_date == $today) ? ' kboard-today' : ''?>">
						<?php if(strpos($year_month_check, date('Ymd', strtotime($group_date))) === false):
						$year_month_check = $year_month_check . '/' . date('Ymd', strtotime($group_date));?>
						<div class="kboard-mobile-calendar-event-date">
							<div class="kboard-mobile-calendar-event-day-num"><?php echo date('d', strtotime($group_date))?></div>
							<div class="kboard-mobile-calendar-event-day-eng"><?php echo date('D', strtotime($group_date))?></div>
						</div>
						<?php endif?>
						<a href="<?php echo esc_url($url->getDocumentURLWithUID($event_item->uid))?>">
							<div class="kboard-mobile-calendar-event-name">
								<div class="event-time">
									<?php
									if(date('H:i', strtotime($event_item->option->start_time)) != '00:00' && date('H:i', strtotime($event_item->option->end_time)) != '00:00'){
										echo date('H:i', strtotime($event_item->option->start_time))?>~<?php echo date('H:i', strtotime($event_item->option->end_time));
									}
									else{
										echo __('All day', 'kboard-cross-calendar');
									}
									?>
								</div>
					       		<!--<?php if($event_item->isNew()):?><span class="kboard-cross-calendar-new-notify">New</span><?php endif?>-->
					       		<?php if($event_item->secret):?><img src="<?php echo $skin_path?>/images/icon-lock.png" alt="<?php echo __('Secret', 'kboard')?>"><?php endif?>
					       		<?php echo $event_item->title?>
				       			<span class="kboard-comments-count"><?php echo $event_item->getCommentsCount()?></span>
							</div>
						</a>
					</div>
				<?php
				endforeach;
			endforeach;
		endif;
		if($is_empty_event):?>
		<div class="kboard-mobile-calendar-empty-event"><?php echo __('There is no schedule.', 'kboard-cross-calendar')?></div>
		<?php endif?>
		</div>
		<!-- 캘린더 리스트 화면 끝 -->
		<?php endif?>
	</div>
</div>

<?php wp_enqueue_script('kboard-cross-calendar-list', "{$skin_path}/list.js", array(), KBOARD_CROSS_CALENDAR_VERSION, true)?>