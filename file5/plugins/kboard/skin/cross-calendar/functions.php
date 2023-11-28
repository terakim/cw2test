<?php
if(!defined('ABSPATH')) exit;

if(!defined('KBOARD_CROSS_CALENDAR_VERSION')){
	define('KBOARD_CROSS_CALENDAR_VERSION', '2.5');
}

global $cross_calendar_skin_dir_name;
$cross_calendar_skin_dir_name = basename(dirname(__FILE__));

load_plugin_textdomain('kboard-cross-calendar', false, dirname(plugin_basename(__FILE__)) . '/languages');

add_filter('kboard_skin_fields', 'kboard_cross_calendar_skin_field', 10, 2);
if(!function_exists('kboard_cross_calendar_skin_field')){
	function kboard_cross_calendar_skin_field($fields, $board){
		if($board->skin == 'cross-calendar' && !$board->fields()->skin_fields){
			if(!isset($fields['color'])){
				$fields['color'] = array(
					'field_type' => 'color',
					'field_label' => __('Color', 'kboard-cross-calendar'),
					'field_name' => __('Color', 'kboard-cross-calendar'),
					'class' => 'kboard-attr-row kboard-attr-color',
					'hidden' => '',
					'meta_key' => 'color',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'placeholder' => '',
					'required' => '',
					'show_document' => '',
					'description' => '',
					'close_button' => 'yes'
				);
			}
			if(!isset($fields['classification'])){
				$fields['classification'] = array(
					'field_type' => 'text',
					'field_label' => __('Classification', 'kboard-cross-calendar'),
					'field_name' => __('Classification', 'kboard-cross-calendar'),
					'class' => 'kboard-attr-row kboard-attr-classification',
					'hidden' => '',
					'meta_key' => 'classification',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'placeholder' => __('Classification', 'kboard-cross-calendar'),
					'required' => '',
					'show_document' => '1',
					'description' => '',
					'close_button' => 'yes'
				);
			}
			if(!isset($fields['event_name_kor'])){
				$fields['event_name_kor'] = array(
					'field_type' => 'text',
					'field_label' => __('Name of event (Korean)', 'kboard-cross-calendar'),
					'field_name' => __('Name of event (Korean)', 'kboard-cross-calendar'),
					'class' => 'kboard-attr-row kboard-attr-event_name_kor',
					'hidden' => '',
					'meta_key' => 'event_name_kor',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'placeholder' => __('Name of event (Korean)', 'kboard-cross-calendar'),
					'required' => '',
					'show_document' => '1',
					'description' => '',
					'close_button' => 'yes'
				);
			}
			if(!isset($fields['event_name_eng'])){
				$fields['event_name_eng'] = array(
					'field_type' => 'text',
					'field_label' => __('Name of event (English)', 'kboard-cross-calendar'),
					'field_name' => __('Name of event (English)', 'kboard-cross-calendar'),
					'class' => 'kboard-attr-row kboard-attr-event_name_eng',
					'hidden' => '',
					'meta_key' => 'event_name_eng',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'placeholder' => __('Name of event (English)', 'kboard-cross-calendar'),
					'required' => '',
					'show_document' => '1',
					'description' => '',
					'close_button' => 'yes'
				);
			}
			if(!isset($fields['place'])){
				$fields['place'] = array(
					'field_type' => 'text',
					'field_label' => __('Venue', 'kboard-cross-calendar'),
					'field_name' => __('Venue', 'kboard-cross-calendar'),
					'class' => 'kboard-attr-row kboard-attr-place',
					'hidden' => '',
					'meta_key' => 'place',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'placeholder' => __('Venue', 'kboard-cross-calendar'),
					'required' => '',
					'show_document' => '1',
					'description' => '',
					'close_button' => 'yes'
				);
			}
			if(!isset($fields['start_date'])){
				$fields['start_date'] = array(
					'field_type' => 'start_date',
					'field_label' => __('Start date', 'kboard-cross-calendar'),
					'field_name' => __('Start date', 'kboard-cross-calendar'),
					'class' => 'kboard-attr-row kboard-attr-start_date',
					'hidden' => '',
					'meta_key' => 'start_date',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'placeholder' => '',
					'required' => '',
					'show_document' => '1',
					'description' => '',
					'close_button' => 'yes'
				);
			}
			if(!isset($fields['end_date'])){
				$fields['end_date'] = array(
					'field_type' => 'end_date',
					'field_label' => __('End date', 'kboard-cross-calendar'),
					'field_name' => __('End date', 'kboard-cross-calendar'),
					'class' => 'kboard-attr-row kboard-attr-end_date',
					'hidden' => '',
					'meta_key' => 'end_date',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'placeholder' => '',
					'required' => '',
					'show_document' => '1',
					'description' => '',
					'close_button' => 'yes'
				);
			}
			if(!isset($fields['web_site'])){
				$fields['web_site'] = array(
					'field_type' => 'text',
					'field_label' => __('Related Web Sites', 'kboard-cross-calendar'),
					'field_name' => __('Related Web Sites', 'kboard-cross-calendar'),
					'class' => 'kboard-attr-row kboard-attr-web_site',
					'hidden' => '',
					'meta_key' => 'web_site',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'placeholder' => __('Related Web Sites', 'kboard-cross-calendar'),
					'required' => '',
					'show_document' => '1',
					'description' => '',
					'close_button' => 'yes'
				);
			}
			if(!isset($fields['summary'])){
				$fields['summary'] = array(
					'field_type' => 'text',
					'field_label' => __('Overview', 'kboard-cross-calendar'),
					'field_name' => __('Overview', 'kboard-cross-calendar'),
					'class' => 'kboard-attr-row kboard-attr-summary',
					'hidden' => '',
					'meta_key' => 'summary',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'placeholder' => __('Overview', 'kboard-cross-calendar'),
					'required' => '',
					'show_document' => '1',
					'description' => '',
					'close_button' => 'yes'
				);
			}
			if(!isset($fields['host'])){
				$fields['host'] = array(
					'field_type' => 'text',
					'field_label' => __('Host', 'kboard-cross-calendar'),
					'field_name' => __('Host', 'kboard-cross-calendar'),
					'class' => 'kboard-attr-row kboard-attr-host',
					'hidden' => '',
					'meta_key' => 'host',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'placeholder' => __('Host', 'kboard-cross-calendar'),
					'required' => '',
					'show_document' => '1',
					'description' => '',
					'close_button' => 'yes'
				);
			}
			if(!isset($fields['subjectivity'])){
				$fields['subjectivity'] = array(
					'field_type' => 'text',
					'field_label' => __('Subjectivity', 'kboard-cross-calendar'),
					'field_name' => __('Subjectivity', 'kboard-cross-calendar'),
					'class' => 'kboard-attr-row kboard-attr-subjectivity',
					'hidden' => '',
					'meta_key' => 'subjectivity',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'placeholder' => __('Subjectivity', 'kboard-cross-calendar'),
					'required' => '',
					'show_document' => '1',
					'description' => '',
					'close_button' => 'yes'
				);
			}
			if(!isset($fields['support'])){
				$fields['support'] = array(
					'field_type' => 'text',
					'field_label' => __('Sponsor', 'kboard-cross-calendar'),
					'field_name' => __('Sponsor', 'kboard-cross-calendar'),
					'class' => 'kboard-attr-row kboard-attr-support',
					'hidden' => '',
					'meta_key' => 'support',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'placeholder' => __('Sponsor', 'kboard-cross-calendar'),
					'required' => '',
					'show_document' => '1',
					'description' => '',
					'close_button' => 'yes'
				);
			}
			if(!isset($fields['item'])){
				$fields['item'] = array(
					'field_type' => 'text',
					'field_label' => __('Exhibition Items', 'kboard-cross-calendar'),
					'field_name' => __('Exhibition Items', 'kboard-cross-calendar'),
					'class' => 'kboard-attr-row kboard-attr-item',
					'hidden' => '',
					'meta_key' => 'item',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'placeholder' => __('Exhibition Items', 'kboard-cross-calendar'),
					'required' => '',
					'show_document' => '1',
					'description' => '',
					'close_button' => 'yes'
				);
			}
			if(!isset($fields['fee'])){
				$fields['fee'] = array(
					'field_type' => 'text',
					'field_label' => __('Entrance Fee', 'kboard-cross-calendar'),
					'field_name' => __('Entrance Fee', 'kboard-cross-calendar'),
					'class' => 'kboard-attr-row kboard-attr-fee',
					'hidden' => '',
					'meta_key' => 'fee',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'placeholder' => __('Entrance Fee', 'kboard-cross-calendar'),
					'required' => '',
					'show_document' => '1',
					'description' => '',
					'close_button' => 'yes'
				);
			}
			if(!isset($fields['manager'])){
				$fields['manager'] = array(
					'field_type' => 'text',
					'field_label' => __('Manager', 'kboard-cross-calendar'),
					'field_name' => __('Manager', 'kboard-cross-calendar'),
					'class' => 'kboard-attr-row kboard-attr-manager',
					'hidden' => '',
					'meta_key' => 'manager',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'placeholder' => __('Manager', 'kboard-cross-calendar'),
					'required' => '',
					'show_document' => '1',
					'description' => '',
					'close_button' => 'yes'
				);
			}
			if(!isset($fields['tel'])){
				$fields['tel'] = array(
					'field_type' => 'text',
					'field_label' => __('Phone Number', 'kboard-cross-calendar'),
					'field_name' => __('Phone Number', 'kboard-cross-calendar'),
					'class' => 'kboard-attr-row kboard-attr-tel',
					'hidden' => '',
					'meta_key' => 'tel',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'placeholder' => __('Phone Number', 'kboard-cross-calendar'),
					'required' => '',
					'show_document' => '1',
					'description' => '',
					'close_button' => 'yes'
				);
			}
			if(!isset($fields['fax'])){
				$fields['fax'] = array(
					'field_type' => 'text',
					'field_label' => __('Fax Number', 'kboard-cross-calendar'),
					'field_name' => __('Fax Number', 'kboard-cross-calendar'),
					'class' => 'kboard-attr-row kboard-attr-fax',
					'hidden' => '',
					'meta_key' => 'fax',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'placeholder' => __('Fax Number', 'kboard-cross-calendar'),
					'required' => '',
					'show_document' => '1',
					'description' => '',
					'close_button' => 'yes'
				);
			}
			if(!isset($fields['email'])){
				$fields['email'] = array(
					'field_type' => 'email',
					'field_label' => __('E-mail', 'kboard-cross-calendar'),
					'field_name' => __('E-mail', 'kboard-cross-calendar'),
					'class' => 'kboard-attr-row kboard-attr-email',
					'hidden' => '',
					'meta_key' => 'email',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'placeholder' => __('E-mail', 'kboard-cross-calendar'),
					'required' => '',
					'show_document' => '1',
					'description' => '',
					'close_button' => 'yes'
				);
			}
		}
		
		return $fields;
	}
}

add_filter('kboard_get_template_field_html', 'kboard_cross_calendar_get_template_field_html', 10, 4);
if(!function_exists('kboard_cross_calendar_get_template_field_html')){
	function kboard_cross_calendar_get_template_field_html($field_html, $field, $content, $board){
		if($board->skin == 'cross-calendar'){
			$meta_key = (isset($field['meta_key']) && $field['meta_key']) ? $field['meta_key'] : '';
			$field_name = (isset($field['field_name']) && $field['field_name']) ? $field['field_name'] : $board->fields()->getFieldLabel($field);
			$required = (isset($field['required']) && $field['required']) ? 'required' : '';
			$default_value = (isset($field['default_value']) && $field['default_value']) ? $field['default_value'] : '';
			
			$ymd = isset($_GET['ymd']) ? date('Y-m-d', strtotime($_GET['ymd'])) : '';
			$today = date('Y-m-d', current_time('timestamp'));
			
			if($field['field_type'] == 'color'){
				ob_start();
				?>
				<div class="kboard-attr-row">
					<label class="attr-name" for="kboard_option_color"><?php echo __('Color', 'kboard-cross-calendar')?></label>
					<div class="attr-value">
						<div class="event-name-color<?php if($content->option->color=='#ac725e'):?> active<?php endif?>" data-color="#ac725e" style="background-color:#ac725e;" onclick="kboard_set_title_color(this)"></div>
						<div class="event-name-color<?php if($content->option->color=='#d06b64'):?> active<?php endif?>" data-color="#d06b64" style="background-color:#d06b64;" onclick="kboard_set_title_color(this)"></div>
						<div class="event-name-color<?php if($content->option->color=='#f83a22'):?> active<?php endif?>" data-color="#f83a22" style="background-color:#f83a22;" onclick="kboard_set_title_color(this)"></div>
						<div class="event-name-color<?php if($content->option->color=='#fa573c'):?> active<?php endif?>" data-color="#fa573c" style="background-color:#fa573c;" onclick="kboard_set_title_color(this)"></div>
						<div class="event-name-color<?php if($content->option->color=='#ff7537'):?> active<?php endif?>" data-color="#ff7537" style="background-color:#ff7537;" onclick="kboard_set_title_color(this)"></div>
						<div class="event-name-color<?php if($content->option->color=='#ffad46'):?> active<?php endif?>" data-color="#ffad46" style="background-color:#ffad46;" onclick="kboard_set_title_color(this)"></div>
						<div class="event-name-color<?php if($content->option->color=='#42d692'):?> active<?php endif?>" data-color="#42d692" style="background-color:#42d692;" onclick="kboard_set_title_color(this)"></div>
						<div class="event-name-color<?php if($content->option->color=='#16a765'):?> active<?php endif?>" data-color="#16a765" style="background-color:#16a765;" onclick="kboard_set_title_color(this)"></div>
						<div class="event-name-color<?php if($content->option->color=='#7bd148'):?> active<?php endif?>" data-color="#7bd148" style="background-color:#7bd148;" onclick="kboard_set_title_color(this)"></div>
						<div class="event-name-color<?php if($content->option->color=='#b3dc6c'):?> active<?php endif?>" data-color="#b3dc6c" style="background-color:#b3dc6c;" onclick="kboard_set_title_color(this)"></div>
						<div class="event-name-color<?php if($content->option->color=='#fbe983'):?> active<?php endif?>" data-color="#fbe983" style="background-color:#fbe983;" onclick="kboard_set_title_color(this)"></div>
						<div class="event-name-color<?php if($content->option->color=='#fad165'):?> active<?php endif?>" data-color="#fad165" style="background-color:#fad165;" onclick="kboard_set_title_color(this)"></div>
						<div class="event-name-color<?php if($content->option->color=='#92e1c0'):?> active<?php endif?>" data-color="#92e1c0" style="background-color:#92e1c0;" onclick="kboard_set_title_color(this)"></div>
						<div class="event-name-color<?php if($content->option->color=='#9fe1e7'):?> active<?php endif?>" data-color="#9fe1e7" style="background-color:#9fe1e7;" onclick="kboard_set_title_color(this)"></div>
						<div class="event-name-color<?php if($content->option->color=='#9fc6e7'):?> active<?php endif?>" data-color="#9fc6e7" style="background-color:#9fc6e7;" onclick="kboard_set_title_color(this)"></div>
						<div class="event-name-color<?php if($content->option->color=='#4986e7'):?> active<?php endif?>" data-color="#4986e7" style="background-color:#4986e7;" onclick="kboard_set_title_color(this)"></div>
						<div class="event-name-color<?php if($content->option->color=='#9a9cff'):?> active<?php endif?>" data-color="#9a9cff" style="background-color:#9a9cff;" onclick="kboard_set_title_color(this)"></div>
						<div class="event-name-color<?php if($content->option->color=='#b99aff'):?> active<?php endif?>" data-color="#b99aff" style="background-color:#b99aff;" onclick="kboard_set_title_color(this)"></div>
						<div class="event-name-color<?php if($content->option->color=='#c2c2c2'):?> active<?php endif?>" data-color="#c2c2c2" style="background-color:#c2c2c2;" onclick="kboard_set_title_color(this)"></div>
						<div class="event-name-color<?php if($content->option->color=='#cabdbf'):?> active<?php endif?>" data-color="#cabdbf" style="background-color:#cabdbf;" onclick="kboard_set_title_color(this)"></div>
						<div class="event-name-color<?php if($content->option->color=='#cca6ac'):?> active<?php endif?>" data-color="#cca6ac" style="background-color:#cca6ac;" onclick="kboard_set_title_color(this)"></div>
						<div class="event-name-color<?php if($content->option->color=='#f691b2'):?> active<?php endif?>" data-color="#f691b2" style="background-color:#f691b2;" onclick="kboard_set_title_color(this)"></div>
						<div class="event-name-color<?php if($content->option->color=='#cd74e6'):?> active<?php endif?>" data-color="#cd74e6" style="background-color:#cd74e6;" onclick="kboard_set_title_color(this)"></div>
						<div class="event-name-color<?php if($content->option->color=='#a47ae2'):?> active<?php endif?>" data-color="#a47ae2" style="background-color:#a47ae2;" onclick="kboard_set_title_color(this)"></div>
						<input type="text" id="kboard_option_color" name="kboard_option_color" value="<?php echo $content->option->color?>" placeholder="<?php echo __('Color', 'kboard-cross-calendar')?>...">
					</div>
				</div>
				<?php
				$field_html = ob_get_clean();
			}
			
			if($field['field_type'] == 'start_date'){
				ob_start();
				?>
				<div class="kboard-attr-row">
					<label class="attr-name" for="kboard_option_start_date"><?php echo __('Start date', 'kboard-cross-calendar')?></label>
					<div class="attr-value">
						<div class="calendar-event-start-date-set">
							<input type="text" class="datepicker" id="kboard_option_start_date" name="kboard_option_start_date" onchange="kboard_set_start_date(this.value)" value="<?php echo $content->option->start_date ? $content->option->start_date : ($ymd ? $ymd : $today)?>" title="<?php echo __('Start Date Setting', 'kboard-cross-calendar')?>" readonly>
							<input type="text" class="timepicker" name="kboard_option_start_time" maxlength="5" value="<?php echo $content->option->start_time ? $content->option->start_time : date('H:i', strtotime('9:00'))?>" title="<?php echo __('Start Time Setting', 'kboard-cross-calendar')?>">
						</div>
					</div>
				</div>
				<?php
				$field_html = ob_get_clean();
			}

			if($field['field_type'] == 'end_date'){
				ob_start();
				?>
				<div class="kboard-attr-row">
					<label class="attr-name" for="kboard_option_end_date"><?php echo __('End date', 'kboard-cross-calendar')?></label>
					<div class="attr-value">
						<div class="calendar-event-end-date-set">
							<input type="text" class="datepicker" id="kboard_option_end_date" name="kboard_option_end_date" onchange="kboard_end_date_check(this.value)" value="<?php echo $content->option->end_date ? $content->option->end_date : ($ymd ? $ymd : $today)?>" title="<?php echo __('End Date Setting', 'kboard-cross-calendar')?>" readonly>
							<input type="text" class="timepicker" name="kboard_option_end_time" maxlength="5" value="<?php echo $content->option->end_time ? $content->option->end_time : date('H:i', strtotime('18:00'))?>" title="<?php echo __('End Time Setting', 'kboard-cross-calendar')?>">
						</div>
						<label><input type="checkbox" class="attr-checkbox" name="kboard_option_all_day_long" onclick="kboard_event_time_all_day_long(this)"><?php echo __('All day', 'kboard-cross-calendar')?></label>
					</div>
				</div>
				<?php
				$field_html = ob_get_clean();
			}

			if($field['field_type'] == 'email'){
				ob_start();
				?>
				<div class="kboard-attr-row">
					<label class="attr-name" for="kboard_option_email"><?php echo __('E-mail', 'kboard-cross-calendar')?></label>
					<div class="attr-value"><input type="email" id="kboard_option_email" name="kboard_option_email" value="<?php echo $content->option->email?>" placeholder="<?php echo __('E-mail', 'kboard-cross-calendar')?>..."></div>
				</div>
				<?php
				$field_html = ob_get_clean();
			}
		}
		
		return $field_html;
	}
}

if(!function_exists('kboard_cross_calendar_document_top_option_html')){
	function kboard_cross_calendar_document_top_option_html($field, $content, $board){
		$meta_key = (isset($field['meta_key']) && $field['meta_key']) ? $field['meta_key'] : '';
		$field_name = (isset($field['field_name']) && $field['field_name']) ? $field['field_name'] : $board->fields()->getFieldLabel($field);
		$top_option_key = apply_filters('kboard_cross_calendar_document_top_option_key', array('classification', 'event_name_kor', 'event_name_eng', 'start_date', 'start_time', 'place', 'web_site'), $content, $board);
		$html = '';
		
		if($content->option->{$meta_key} && in_array($field['meta_key'], $top_option_key)){
			ob_start();
			
			if($field['meta_key'] == 'start_date'){
				?>
				<?php if($content->option->start_date && $content->option->end_date):?>
					<div class="kboard-detail-item"><?php echo __('period', 'kboard-cross-calendar')?> : <?php echo $content->option->start_date?> ~ <?php echo $content->option->end_date?></div>
				<?php endif?>
				<?php if($content->option->start_time && $content->option->end_time):?>
					<div class="kboard-detail-item"><?php echo __('time', 'kboard-cross-calendar')?> : <?php echo $content->option->start_time?> ~ <?php echo $content->option->end_time?></div>
				<?php endif?>
				<?php
			}
			else if($field['meta_key'] == 'web_site'){
				?>
				<div class="kboard-detail-item"><?php echo __('Related Web Sites', 'kboard-cross-calendar')?> : <a href="<?php echo (strpos($content->option->web_site, 'http://') !== false || strpos($content->option->web_site, 'https://') !== false) ? $content->option->web_site : "http://{$content->option->web_site}"?>" onclick="window.open(this.href);return false;"><?php echo $content->option->web_site;?></a></div>
				<?php
			}
			else{
				?>
				<div class="kboard-detail-item"><?php echo $field['field_name']?> : <?php echo $content->option->{$field['meta_key']}?></div>
				<?php
			}
			
			$html = ob_get_clean();
		}
		
		return apply_filters('kboard_cross_calendar_document_top_option_html', $html, $field, $content, $board);
	}
}

if(!function_exists('kboard_cross_calendar_document_summary_option_html')){
	function kboard_cross_calendar_document_summary_option_html($content, $board){
		$skin_field = $board->fields()->getSkinFields();
		
		$summary_option_key = apply_filters('kboard_cross_calendar_document_summary_option_key', 'summary', $content, $board);
		$summary_field_name = isset($skin_field[$summary_option_key]['field_name']) ? $skin_field[$summary_option_key]['field_name'] : '';
		$html = '';
		
		ob_start();
		?>
		<div class="kboard-detail-summary"><?php echo $summary_field_name?></div>
		<div class="kboard-detail-summary-top"><?php echo $content->option->{$summary_option_key}?></div>
		<?php
		$html = ob_get_clean();
		
		return apply_filters('kboard_cross_calendar_document_summary_option_html', $html, $content, $board);
	}
}

if(!function_exists('kboard_cross_calendar_document_summary_item_option_html')){
	function kboard_cross_calendar_document_summary_item_option_html($field, $content, $board){
		$meta_key = (isset($field['meta_key']) && $field['meta_key']) ? $field['meta_key'] : '';
		$field_name = (isset($field['field_name']) && $field['field_name']) ? $field['field_name'] : $board->fields()->getFieldLabel($field);
		$summary_item_option_key = apply_filters('kboard_cross_calendar_document_summary_item_option_key', array('host', 'subjectivity', 'support', 'item', 'fee', 'manager', 'tel', 'fax', 'email'), $content, $board);
		$html = '';
		
		if($content->option->{$meta_key} && in_array($field['meta_key'], $summary_item_option_key)){
			ob_start();
			
			if($meta_key == 'tel'){
				?>
				<div class="kboard-detail-summary-item-wrap"><div class="kboard-detail-summary-item"><?php echo $field_name?></div><div class="kboard-detail-summary-content"><a href="tel:<?php echo $content->option->{$meta_key}?>"><?php echo $content->option->{$meta_key}?></a></div></div>
				<?php
			}
			else if($meta_key == 'fax'){
				?>
				<div class="kboard-detail-summary-item-wrap"><div class="kboard-detail-summary-item"><?php echo $field_name?></div><div class="kboard-detail-summary-content"><a href="fax:<?php echo $content->option->{$meta_key}?>"><?php echo $content->option->{$meta_key}?></a></div></div>
				<?php
			}
			else if($meta_key == 'email'){
				?>
				<div class="kboard-detail-summary-item-wrap"><div class="kboard-detail-summary-item"><?php echo $field_name?></div><div class="kboard-detail-summary-content"><a href="mailto:<?php echo $content->option->{$meta_key}?>"><?php echo $content->option->{$meta_key}?></a></div></div>
				<?php
			}
			else{
				?>
				<div class="kboard-detail-summary-item-wrap"><div class="kboard-detail-summary-item"><?php echo $field_name?></div><div class="kboard-detail-summary-content"><?php echo $content->option->{$meta_key}?></div></div>
				<?php
			}
			
			$html = ob_get_clean();
		}
		
		return apply_filters('kboard_cross_calendar_document_summary_item_option_html', $html, $field, $content, $board);
	}
}

if(!function_exists('kboard_calendar_latest_template')){
	add_action('template_redirect', 'kboard_calendar_latest_template');
	function kboard_calendar_latest_template(){
		if(isset($_GET['kboard_calendar_latest_template']) && $_GET['kboard_calendar_latest_template'] == 'calendar'){
			$board_id = isset($_GET['kboard_calendar_latest_board_id']) ? intval($_GET['kboard_calendar_latest_board_id']) : '';
			$latestview_id = isset($_GET['kboard_calendar_latest_latestview_id']) ? intval($_GET['kboard_calendar_latest_latestview_id']) : '';
			$board_url = isset($_GET['kboard_calendar_latest_board_url']) ? esc_url($_GET['kboard_calendar_latest_board_url']) : '';
			
			unset($_GET['kboard_calendar_latest_template']);
			unset($_GET['kboard_calendar_latest_board_id']);
			unset($_GET['kboard_calendar_latest_latestview_id']);
			unset($_GET['kboard_calendar_latest_board_url']);
			unset($_GET['kboard_calendar_latest_type']);
			
			if($latestview_id){
				$latestview_id = intval($latestview_id);
				echo kboard_latestview_shortcode(array('id'=>$latestview_id, 'url'=>$board_url));
			}
			else{
				$board_id = intval($board_id);
				echo kboard_latest_shortcode(array('id'=>$board_id, 'url'=>$board_url));
			}
			exit;
		}
	}
}

if(!function_exists('kboard_get_calendar_year')){
	function kboard_get_calendar_year(){
		static $calendar_year;
		if($calendar_year=== null){
			$calendar_year = isset($_GET['kboard_calendar_year'])?intval($_GET['kboard_calendar_year']):date('Y', current_time('timestamp'));
		}
		return $calendar_year;
	}
}

if(!function_exists('kboard_get_calendar_month')){
	function kboard_get_calendar_month(){
		static $calendar_month;
		if($calendar_month=== null){
			$calendar_month = isset($_GET['kboard_calendar_month'])?intval($_GET['kboard_calendar_month']):date('m', current_time('timestamp'));
		}
		return $calendar_month;
	}
}

if(!function_exists('kboard_get_calendar_latest_year')){
	function kboard_get_calendar_latest_year(){
		static $calendar_latest_year;
		if($calendar_latest_year === null){
			$calendar_latest_year = isset($_GET['kboard_calendar_latest_year'])?intval($_GET['kboard_calendar_latest_year']):date('Y', current_time('timestamp'));
			unset($_GET['kboard_calendar_latest_year']);
		}
		return $calendar_latest_year;
	}
}

if(!function_exists('kboard_get_calendar_latest_month')){
	function kboard_get_calendar_latest_month(){
		static $calendar_latest_month;
		if($calendar_latest_month === null){
			$calendar_latest_month = isset($_GET['kboard_calendar_latest_month'])?intval($_GET['kboard_calendar_latest_month']):date('m', current_time('timestamp'));
			unset($_GET['kboard_calendar_latest_month']);
		}
		return $calendar_latest_month;
	}
}

if(isset($_GET['kboard_calendar_type']) && ($_GET['kboard_calendar_type'] == 'calendar' || $_GET['kboard_calendar_type'] == 'list')){
	setcookie('kboard_calendar_type', $_GET['kboard_calendar_type'], current_time('timestamp') + (3600*24*365), COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);
	$GLOBALS['kboard_calendar_type'] = $_GET['kboard_calendar_type'];
}
else if(isset($_COOKIE['kboard_calendar_type']) && ($_COOKIE['kboard_calendar_type'] == 'calendar' || $_COOKIE['kboard_calendar_type'] == 'list')){
	$GLOBALS['kboard_calendar_type'] = $_COOKIE['kboard_calendar_type'];
}
else{
	$GLOBALS['kboard_calendar_type'] = 'calendar';
}

if(!function_exists('kboard_get_calendar_type')){
	function kboard_get_calendar_type(){
		if(kboard_keyword()){
			return 'search';
		}
		return $GLOBALS['kboard_calendar_type'];
	}
}

if(isset($_GET['kboard_calendar_latest_type']) && ($_GET['kboard_calendar_latest_type'] == 'calendar' || $_GET['kboard_calendar_latest_type'] == 'list')){
	setcookie('kboard_calendar_latest_type', $_GET['kboard_calendar_latest_type'], current_time('timestamp') + (3600*24*365), COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);
	$GLOBALS['kboard_calendar_latest_type'] = $_GET['kboard_calendar_latest_type'];
}
else if(isset($_COOKIE['kboard_calendar_latest_type']) && ($_COOKIE['kboard_calendar_latest_type'] == 'calendar' || $_COOKIE['kboard_calendar_latest_type'] == 'list')){
	$GLOBALS['kboard_calendar_latest_type'] = $_COOKIE['kboard_calendar_latest_type'];
}
else{
	$GLOBALS['kboard_calendar_latest_type'] = 'calendar';
}

if(!function_exists('kboard_get_calendar_latest_type')){
	function kboard_get_calendar_latest_type(){
		return $GLOBALS['kboard_calendar_latest_type'];
	}
}

if(!function_exists('kboard_get_calendar_day_class')){
	function kboard_get_calendar_day_class($calendar_start_day, $cell_index, $now_day_count, $last_day, $year, $month){
		$class = 'calendar-column-day';
		if($calendar_start_day <= $cell_index && $now_day_count <= $last_day){
			switch(date("w", mktime(0, 0, 0, $month, $now_day_count, $year))){
				case '6': $class = 'calendar-column-saturday'; break;
				case '0': $class = 'calendar-column-sunday'; break;
			}
		}
		else if($cell_index < $calendar_start_day || $cell_index >= $last_day){
			$class = 'calendar-column-pre-next-day';
		}
		return $class;
	}
}

if(!function_exists('kboard_get_calendar_day_of_the_week')){
	function kboard_get_calendar_day_of_the_week($day_of_the_week){
		$class = 'kboard-cross-calendar-weekday';
		switch($day_of_the_week){
			case '6': $class = 'calendar-column-saturday'; break;
			case '0': $class = 'calendar-column-sunday'; break;
		}
		return $class;
	}
}

if(!function_exists('kboard_get_calendar_key')){
	function kboard_get_calendar_key($calendar_start_day, $cell_index, $now_day_count, $last_day, $year, $month, $prev_day_count, $next_day_count){
		$prev_month = $month -1;
		$next_month = $month +1;
		
		if($calendar_start_day <= $cell_index && $now_day_count <= $last_day){
			$key = date('Y-m-d', strtotime("{$year}-{$month}-{$now_day_count}"));
			return $key;
		}
		else if($cell_index < $calendar_start_day){
			$key = date('Y-m-d', strtotime("{$year}-{$prev_month}-{$prev_day_count}"));
			return $key;
		}
		else if($cell_index >= $last_day){
			$key = date('Y-m-d', strtotime("{$year}-{$next_month}-{$next_day_count}"));
			return $key;
		}
	}
}

if(!function_exists('kboard_get_calendar_ymd')){
	function kboard_get_calendar_ymd($calendar_start_day, $cell_index, $now_day_count, $last_day, $year, $month, $prev_day_count, $next_day_count){
		if($calendar_start_day <= $cell_index && $now_day_count <= $last_day){
			return date('Y-m-d', mktime(0, 0, 0, $month, $now_day_count, $year));
		}
		else if($cell_index < $calendar_start_day){
			return date('Y-m-d', mktime(0, 0, 0, $month-1, $prev_day_count, $year));
		}
		else if($cell_index >= $last_day){
			return date('Y-m-d', mktime(0, 0, 0, $month+1, $next_day_count, $year));
		}
	}
}

if(!function_exists('kboard_get_calendar_white_background_style')){
	function kboard_get_calendar_white_background_style($color){
		if(!$color || $color == '#ffffff'){
			return 'color: #000000;';
		}
		return '';
	}
}

if(!function_exists('kboard_get_calendar_type_array')){
	function kboard_get_calendar_type_array($calendar_event_rows){
		$event_table_item_list = array();
		ksort($calendar_event_rows);
		$event_keys = array_keys($calendar_event_rows);
		foreach($event_keys as $key){
			foreach($calendar_event_rows[$key] as $event_table_item){
				$index = -1;
				for($start_date = $event_table_item->option->start_date; $start_date<=$event_table_item->option->end_date; $start_date=date('Y-m-d', strtotime("{$start_date} +1 day"))){
					if($index >= 0){
						$event_table_item_list[$start_date][$index] = $event_table_item;
						for($i=0; $i<$index; $i++){
							if(isset($event_table_item_list[$start_date][$i]) && $event_table_item_list[$start_date][$i] != 'empty'){
								continue;
							}
							else{
								$event_table_item_list[$start_date][$i] = 'empty';
							}
						}
					}
					else if(!isset($event_table_item_list[$start_date])){
						$index = 0;
						$event_table_item_list[$start_date][$index] = $event_table_item;
					}
					else{
						$push = false;
						foreach($event_table_item_list[$start_date] as $key=>$temp){
							if(isset($temp->uid) && $temp->uid){
								continue;
							}
							else if($temp == 'empty'){
								$event_table_item_list[$start_date][$key] = $event_table_item;
								$push = true;
								break;
							}
						}
						if(!$push){
							$index = count($event_table_item_list[$start_date]);
							$event_table_item_list[$start_date][$index] = $event_table_item;
						}
					}
				}
			}
		}
		return $event_table_item_list;
	}
}

if(!function_exists('kboard_get_calendar_list_type_array')){
	function kboard_get_calendar_list_type_array($calendar_event_rows){
		$event_item_list = array();
		ksort($calendar_event_rows);
		$event_keys = array_keys($calendar_event_rows);
		foreach($event_keys as $key){
			foreach($calendar_event_rows[$key] as $event_item){
				for($start_date = $event_item->option->start_date; $start_date<=$event_item->option->end_date; $start_date=date('Y-m-d', strtotime("{$start_date} +1 day"))){
					$event_item_list[$start_date][] = $event_item;
				}
			}
		}
		return $event_item_list;
	}
}

add_filter('kboard_document_add_option_value_field_html', 'kboard_cross_calendar_document_add_option_value_field_html', 10, 4);
if(!function_exists('kboard_cross_calendar_document_add_option_value_field_html')){
	function kboard_cross_calendar_document_add_option_value_field_html($value_html, $field, $content, $board){
		if($board->skin == 'cross-calendar'){
			$meta_key = (isset($field['meta_key'])&&$field['meta_key']) ? esc_attr($field['meta_key']) : '';
			$field_name = (isset($field['field_name'])&&$field['field_name']) ? esc_html($field['field_name']) : '';
			$option_value = is_array($content->option->{$meta_key}) ? esc_html(implode(', ', $content->option->{$meta_key})) : esc_html($content->option->{$meta_key});
		}
		
		return $value_html;
	}
}

add_filter("kboard_{$cross_calendar_skin_dir_name}_extends_setting_update", 'kboard_cross_calendar_extends_setting_update', 10, 2);
if(!function_exists('kboard_cross_calendar_extends_setting_update')){
	function kboard_cross_calendar_extends_setting_update($board_meta, $board_id){
		$board_meta->cross_calendar_event_setting     = isset($_POST['cross_calendar_event_setting'])      ? $_POST['cross_calendar_event_setting']     : '';
		$board_meta->cross_calendar_event_name        = isset($_POST['cross_calendar_event_name'])         ? $_POST['cross_calendar_event_name']        : '';
		$board_meta->cross_calendar_cross_host        = isset($_POST['cross_calendar_cross_host'])         ? $_POST['cross_calendar_cross_host']        : '';
		$board_meta->cross_calendar_cross_place       = isset($_POST['cross_calendar_cross_place'])        ? $_POST['cross_calendar_cross_place']       : '';
		$board_meta->cross_calendar_cross_thumbnail   = isset($_POST['cross_calendar_cross_thumbnail'])    ? $_POST['cross_calendar_cross_thumbnail']   : '';
	}
}

add_filter("kboard_{$cross_calendar_skin_dir_name}_extends_setting", 'kboard_cross_calendar_extends_setting', 10, 3);
if(!function_exists('kboard_cross_calendar_extends_setting')){
	function kboard_cross_calendar_extends_setting($html, $board_meta, $board_id){
		$board = new KBoard($board_id);
		
		ob_start();
		?>
		<h3>KBoard 크로스 캘린더 : 리스트 설정</h3>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><label for="cross_calendar_event_setting">리스트 화면 설정 옵션</label></th>
					<td>
						<select name="cross_calendar_event_setting" id="cross_calendar_event_setting">
							<option value="">비활성화</option>
							<option value="1"<?php if($board_meta->cross_calendar_event_setting):?> selected<?php endif?>>활성화</option>
						</select>
						<p class="description">설정을 활성화해야만 리스트 화면에 옵션을 추가할 수 있습니다.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="cross_calendar_event_name">행사명 활성화 옵션</label></th>
					<td>
						<select name="cross_calendar_event_name" id="cross_calendar_event_name">
							<option value="">비활성화</option>
							<option value="1"<?php if($board_meta->cross_calendar_event_name):?> selected<?php endif?>>활성화</option>
						</select>
						<p class="description">활성화하면 행사명이 리스트 화면에 표시됩니다.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="cross_calendar_cross_host">주최명 활성화 옵션</label></th>
					<td>
						<select name="cross_calendar_cross_host" id="cross_calendar_cross_host">
							<option value="">비활성화</option>
							<option value="1"<?php if($board_meta->cross_calendar_cross_host):?> selected<?php endif?>>활성화</option>
						</select>
						<p class="description">활성화하면 주최명이 리스트 화면에 표시됩니다.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="cross_calendar_cross_place">개최장소 활성화 옵션</label></th>
					<td>
						<select name="cross_calendar_cross_place" id="cross_calendar_cross_place">
							<option value="">비활성화</option>
							<option value="1"<?php if($board_meta->cross_calendar_cross_place):?> selected<?php endif?>>활성화</option>
						</select>
						<p class="description">활성화하면 개최장소가 리스트 화면에 표시됩니다.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="cross_calendar_cross_thumbnail">썸네일 활성화 옵션</label></th>
					<td>
						<select name="cross_calendar_cross_thumbnail" id="cross_calendar_cross_thumbnail">
							<option value="">비활성화</option>
							<option value="1"<?php if($board_meta->cross_calendar_cross_thumbnail):?> selected<?php endif?>>활성화</option>
						</select>
						<p class="description">활성화하면 썸네일이 리스트 화면에 표시됩니다.</p>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
		$html = ob_get_clean();
		return $html;
	}
}