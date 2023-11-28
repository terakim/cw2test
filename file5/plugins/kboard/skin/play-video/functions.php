<?php
if(!defined('ABSPATH')) exit;

global $play_video_skin_dir_name;
$play_video_skin_dir_name = basename(dirname(__FILE__));

load_plugin_textdomain('kboard-play-video', false, dirname(plugin_basename(__FILE__)) . '/languages');

add_action('wp_enqueue_scripts', 'kboard_play_video_scripts', 999);
add_action('kboard_switch_to_blog', 'kboard_play_video_scripts');
function kboard_play_video_scripts(){
	// 번역 등록
	$localize = array(
		'no_upload' => __('The thumbnail could not be loaded automatically. Do you want to upload the thumbnail directly?', 'kboard-play-video'),
		'save' => __('Save', 'kboard')
	);
	wp_localize_script('kboard-script', 'kboard_play_video_localize_strings', apply_filters('kboard_play_video_localize_strings', $localize));
}

if(!function_exists('kboard_play_video_skin_field')){
	add_filter('kboard_skin_fields', 'kboard_play_video_skin_field', 10, 2);
	function kboard_play_video_skin_field($fields, $board){
		if($board->skin == 'play-video'){
			if(!isset($fields['youtube_id'])){
				$fields['youtube_id'] = array(
					'field_type' => 'text',
					'field_label' => __('YouTube ID', 'kboard-play-video'),
					'field_name' => __('YouTube ID', 'kboard-play-video'),
					'class' => 'kboard-attr-row kboard-attr-youtube_id',
					'custom_class' => '',
					'meta_key' => 'youtube_id',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'placeholder' => __('YouTube Video ID', 'kboard-play-video'),
					'description' => '',
					'required' => '',
					'show_document' => '',
					'hidden' => '',
					'close_button' => 'yes'
				);
			}
			if(!isset($fields['vimeo_id'])){
				$fields['vimeo_id'] = array(
					'field_type' => 'text',
					'field_label' => __('Vimeo ID', 'kboard-play-video'),
					'field_name' => __('Vimeo ID', 'kboard-play-video'),
					'class' => 'kboard-attr-row kboard-attr-vimeo_id',
					'custom_class' => '',
					'meta_key' => 'vimeo_id',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'placeholder' => __('Vimeo Video ID', 'kboard-play-video'),
					'description' => '',
					'required' => '',
					'show_document' => '',
					'hidden' => '',
					'close_button' => 'yes'
				);
			}
			if(!isset($fields['video_url'])){
				$fields['video_url'] = array(
					'field_type' => 'text',
					'field_label' => __('Video URL', 'kboard-play-video'),
					'field_name' => __('Video URL', 'kboard-play-video'),
					'class' => 'kboard-attr-row kboard-attr-video-url',
					'custom_class' => '',
					'meta_key' => 'video_url',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'placeholder' => __('Video URL', 'kboard-play-video'),
					'description' => '',
					'required' => '',
					'show_document' => '',
					'hidden' => '',
					'close_button' => 'yes'
				);
			}
			if(!isset($fields['video_view'])){
				$fields['video_view'] = array(
					'field_type' => 'select',
					'field_label' => __('Screen Ratio', 'kboard-play-video'),
					'field_name' => __('Screen Ratio', 'kboard-play-video'),
					'class' => 'kboard-attr-row kboard-attr-video_view',
					'custom_class' => '',
					'meta_key' => 'video_view',
					'row' => array(
						''=>array('label'=>__('Wide Screen (16:9)', 'kboard-play-video')),
						'normal'=>array('label'=>__('Standard (4:3)', 'kboard-play-video'))
					),
					'default_value' => '',
					'permission' => '',
					'roles' => array(),
					'description' => '',
					'required' => '',
					'show_document' => '',
					'close_button' => 'yes'
				);
			}
			if(!isset($fields['autoplay'])){
				$fields['autoplay'] = array(
					'field_type' => 'select',
					'field_label' => __('Auto Play', 'kboard-play-video'),
					'field_name' => __('Auto Play', 'kboard-play-video'),
					'class' => 'kboard-attr-row kboard-attr-autoplay',
					'custom_class' => '',
					'meta_key' => 'autoplay',
					'row' => array(
						''=>array('label'=>'OFF'),
						'1'=>array('label'=>'ON'),
					),
					'default_value' => '',
					'permission' => '',
					'roles' => array(),
					'description' => '',
					'required' => '',
					'show_document' => '',
					'close_button' => 'yes'
				);
			}
		}
		return $fields;
	}
}

if(!function_exists('kboard_play_video_skin_field_after_youtube_id')){
	add_action('kboard_skin_field_after_youtube_id', 'kboard_play_video_skin_field_after_youtube_id', 10, 3);
	function kboard_play_video_skin_field_after_youtube_id($field, $content, $board){
		if($board->skin == 'play-video'){
			?>
			<div class="kboard-attr-row">
				<div class="attr-value">
					<input type="hidden" name="kboard_option_youtube_thumbnail_url" value="<?php echo esc_url($content->option->youtube_thumbnail_url)?>">
					<div class="description"><?php echo __('※ Please enter only the ID value at the end of the url.', 'kboard-play-video')?> (<?php echo __('ex', 'kboard-play-video')?>:https://www.youtube.com/watch?v=<span class="text-bold">eL8ebkPxYrM</span>)</div>
				</div>
			</div>
			<?php
		}
	}
}

if(!function_exists('kboard_play_video_skin_field_after_vimeo_id')){
	add_action('kboard_skin_field_after_vimeo_id', 'kboard_play_video_skin_field_after_vimeo_id', 10, 3);
	function kboard_play_video_skin_field_after_vimeo_id($field, $content, $board){
		if($board->skin == 'play-video'){
			?>
			<div class="kboard-attr-row">
				<div class="attr-value">
					<input type="hidden" name="kboard_option_vimeo_thumbnail_url" value="<?php echo esc_url($content->option->vimeo_thumbnai_url)?>">
					<div class="description"><?php echo __('※ Please enter only the ID value at the end of the url.', 'kboard-play-video')?> (<?php echo __('ex', 'kboard-play-video')?>:https://vimeo.com/<span class="text-bold">237551523</span>)</div>
				</div>
			</div>
			<?php
		}
	}
}

if(!function_exists('kboard_play_video_get_template_field_html')){
	add_filter('kboard_get_template_field_html', 'kboard_play_video_get_template_field_html', 10, 4);
	function kboard_play_video_get_template_field_html($field_html, $field, $content, $board){
		if($board->skin == 'play-video'){
			$meta_key = (isset($field['meta_key']) && $field['meta_key']) ? $field['meta_key'] : '';
			$field_name = (isset($field['field_name']) && $field['field_name']) ? $field['field_name'] : $board->fields()->getFieldLabel($field);
			$required = (isset($field['required']) && $field['required']) ? 'required' : '';
			$default_value = (isset($field['default_value']) && $field['default_value']) ? $field['default_value'] : '';
			$fields = $board->fields();
			
			if($meta_key == 'video_view'){
				ob_start();
				?>
				<div class="kboard-attr-row <?php echo esc_attr($field['class'])?> meta-key-<?php echo esc_attr($meta_key)?> <?php echo esc_attr($required)?>">
					<label class="attr-name" for="<?php echo esc_attr($meta_key)?>"><span class="field-name"><?php echo esc_html($field_name)?></span><?php if($required):?> <span class="attr-required-text">*</span><?php endif?></label>
					<div class="attr-value">
						<select id="<?php echo esc_attr($meta_key)?>" name="<?php echo esc_attr($fields->getOptionFieldName($meta_key))?>"class="<?php echo esc_attr($required)?>">
							<option value=""><?php echo __('Wide Screen (16:9)', 'kboard-play-video')?></option>
							<option value="normal"<?php if($content->option->video_view):?> selected<?php endif?>><?php echo __('Standard (4:3)', 'kboard-play-video')?></option>
						</select>
						<?php if(isset($field['description']) && $field['description']):?><div class="description"><?php echo esc_html($field['description'])?></div><?php endif?>
					</div>
				</div>
				<?php
				$field_html = ob_get_clean();
				
			}
			else if($meta_key == 'autoplay'){
				ob_start();
				?>
				<div class="kboard-attr-row <?php echo esc_attr($field['class'])?> meta-key-<?php echo esc_attr($meta_key)?> <?php echo esc_attr($required)?>">
					<label class="attr-name" for="<?php echo esc_attr($meta_key)?>"><span class="field-name"><?php echo esc_html($field_name)?></span><?php if($required):?> <span class="attr-required-text">*</span><?php endif?></label>
					<div class="attr-value">
						<select id="<?php echo esc_attr($meta_key)?>" name="<?php echo esc_attr($fields->getOptionFieldName($meta_key))?>"class="<?php echo esc_attr($required)?>">
							<option value="">OFF</option>
							<option value="1"<?php if($content->option->autoplay):?> selected<?php endif?>>ON</option>
						</select>
						<?php if(isset($field['description']) && $field['description']):?><div class="description"><?php echo esc_html($field['description'])?></div><?php endif?>
					</div>
				</div>
				<?php
				$field_html = ob_get_clean();
				
			}
		}
		return $field_html;
	}
}

if(!function_exists('kboard_play_video_skin_field_after_autoplay')){
	add_action('kboard_skin_field_after_autoplay', 'kboard_play_video_skin_field_after_autoplay', 10, 3);
	function kboard_play_video_skin_field_after_autoplay($field, $content, $board){
		if($board->skin == 'play-video'){
			?>
			<div class="kboard-attr-row">
				<div class="attr-value">
					<div class="description"><?php echo __('※ Not applicable in all environments, such as mobile.', 'kboard-play-video')?></div>
				</div>
			</div>
			<?php
		}
	}
}

add_action('kboard_skin_field_after_thumbnail', 'kboard_play_video_skin_field_after_thumbnail', 10, 3);
function kboard_play_video_skin_field_after_thumbnail($field, $content, $board){
	if($board->skin == 'play-video'){
		?>
		<input type="hidden" id="kboard-play-video-thumbnail" value="<?php if($content->getThumbnail()):?>1<?php endif?>">
		<?php
	}
}

add_filter("kboard_{$play_video_skin_dir_name}_extends_setting", 'kboard_play_video_extends_setting', 10, 3);
if(!function_exists('kboard_play_video_extends_setting')){
	function kboard_play_video_extends_setting($html, $meta, $board_id){
		$board = new KBoard($board_id);
		$page_rpp = $board->meta->mobile_page_rpp ? $board->meta->mobile_page_rpp : '';
		
		ob_start();
		?>
		<h3>KBoard 플레이 비디오 스킨 : 기본 설정</h3>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row" style="width: 210px;"><label for="mobile_page_rpp">모바일 게시글 표시 수</label></th>
					<td>
						<select name="mobile_page_rpp" id="mobile_page_rpp">
							<?php if(!$board->meta->mobile_page_rpp) $board->meta->mobile_page_rpp=10;?>
							<option value="1"<?php if($board->meta->mobile_page_rpp == 1):?> selected<?php endif?>>1개</option>
							<option value="2"<?php if($board->meta->mobile_page_rpp == 2):?> selected<?php endif?>>2개</option>
							<option value="3"<?php if($board->meta->mobile_page_rpp == 3):?> selected<?php endif?>>3개</option>
							<option value="4"<?php if($board->meta->mobile_page_rpp == 4):?> selected<?php endif?>>4개</option>
							<option value="5"<?php if($board->meta->mobile_page_rpp == 5):?> selected<?php endif?>>5개</option>
							<option value="6"<?php if($board->meta->mobile_page_rpp == 6):?> selected<?php endif?>>6개</option>
							<option value="7"<?php if($board->meta->mobile_page_rpp == 7):?> selected<?php endif?>>7개</option>
							<option value="8"<?php if($board->meta->mobile_page_rpp == 8):?> selected<?php endif?>>8개</option>
							<option value="9"<?php if($board->meta->mobile_page_rpp == 9):?> selected<?php endif?>>9개</option>
							<option value="10"<?php if($board->meta->mobile_page_rpp == 10):?> selected<?php endif?>>10개</option>
							<option value="11"<?php if($board->meta->mobile_page_rpp == 11):?> selected<?php endif?>>11개</option>
							<option value="12"<?php if($board->meta->mobile_page_rpp == 12):?> selected<?php endif?>>12개</option>
							<option value="13"<?php if($board->meta->mobile_page_rpp == 13):?> selected<?php endif?>>13개</option>
							<option value="14"<?php if($board->meta->mobile_page_rpp == 14):?> selected<?php endif?>>14개</option>
							<option value="15"<?php if($board->meta->mobile_page_rpp == 15):?> selected<?php endif?>>15개</option>
							<option value="16"<?php if($board->meta->mobile_page_rpp == 16):?> selected<?php endif?>>16개</option>
							<option value="17"<?php if($board->meta->mobile_page_rpp == 17):?> selected<?php endif?>>17개</option>
							<option value="18"<?php if($board->meta->mobile_page_rpp == 18):?> selected<?php endif?>>18개</option>
							<option value="19"<?php if($board->meta->mobile_page_rpp == 19):?> selected<?php endif?>>19개</option>
							<option value="20"<?php if($board->meta->mobile_page_rpp == 20):?> selected<?php endif?>>20개</option>
							<option value="21"<?php if($board->meta->mobile_page_rpp == 21):?> selected<?php endif?>>21개</option>
							<option value="22"<?php if($board->meta->mobile_page_rpp == 22):?> selected<?php endif?>>22개</option>
							<option value="23"<?php if($board->meta->mobile_page_rpp == 23):?> selected<?php endif?>>23개</option>
							<option value="24"<?php if($board->meta->mobile_page_rpp == 24):?> selected<?php endif?>>24개</option>
							<option value="25"<?php if($board->meta->mobile_page_rpp == 25):?> selected<?php endif?>>25개</option>
							<option value="26"<?php if($board->meta->mobile_page_rpp == 26):?> selected<?php endif?>>26개</option>
							<option value="27"<?php if($board->meta->mobile_page_rpp == 27):?> selected<?php endif?>>27개</option>
							<option value="28"<?php if($board->meta->mobile_page_rpp == 28):?> selected<?php endif?>>28개</option>
							<option value="29"<?php if($board->meta->mobile_page_rpp == 29):?> selected<?php endif?>>29개</option>
							<option value="30"<?php if($board->meta->mobile_page_rpp == 30):?> selected<?php endif?>>30개</option>
							<option value="40"<?php if($board->meta->mobile_page_rpp == 40):?> selected<?php endif?>>40개</option>
							<option value="50"<?php if($board->meta->mobile_page_rpp == 50):?> selected<?php endif?>>50개</option>
							<option value="60"<?php if($board->meta->mobile_page_rpp == 60):?> selected<?php endif?>>60개</option>
							<option value="70"<?php if($board->meta->mobile_page_rpp == 70):?> selected<?php endif?>>70개</option>
							<option value="80"<?php if($board->meta->mobile_page_rpp == 80):?> selected<?php endif?>>80개</option>
							<option value="90"<?php if($board->meta->mobile_page_rpp == 90):?> selected<?php endif?>>90개</option>
							<option value="100"<?php if($board->meta->mobile_page_rpp == 100):?> selected<?php endif?>>100개</option>
						</select>
						<p class="description">모바일에서 한 페이지에 보여지는 게시글 개수를 정합니다.</p>
						<p class="description">PC는 <a href="#tab-kboard-setting-0" onclick="kboard_setting_tab_change(0);">기본설정</a> <span style="font-weight:bold">게시글 표시 수</span>로 설정하실 수 있습니다.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" style="width: 210px;"><label for="pc_row">PC 한 줄에 표시할 게시글 수</label></th>
					<td>
						<select name="pc_row" id="pc_row">
							<option value="play-video-row1"<?php if($board->meta->pc_row == 'play-video-row1'):?> selected<?php endif?>>1개</option>
							<option value="play-video-row2"<?php if($board->meta->pc_row == 'play-video-row2'):?> selected<?php endif?>>2개</option>
							<option value="play-video-row3"<?php if($board->meta->pc_row == 'play-video-row3'):?> selected<?php endif?>>3개</option>
							<option value="play-video-row4"<?php if(!$board->meta->pc_row || $board->meta->pc_row == 'play-video-row4'):?> selected<?php endif?>>4개 (기본)</option>
							<option value="play-video-row5"<?php if($board->meta->pc_row == 'play-video-row5'):?> selected<?php endif?>>5개</option>
						</select>
						<p class="description">자동으로 설정할 경우 게시판 너비에 따라 한 줄에 표시되는 게시글의 수가 설정됩니다.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" style="width: 210px;"><label for="mobile_row">모바일 한 줄에 표시할 게시글 수</label></th>
					<td>
						<select name="mobile_row" id="mobile_row">
							<option value="play-video-row1"<?php if(!$board->meta->mobile_row || $board->meta->mobile_row == 'play-video-row1'):?> selected<?php endif?>>1개 (기본)</option>
							<option value="play-video-row2"<?php if($board->meta->mobile_row == 'play-video-row2'):?> selected<?php endif?>>2개</option>
							<option value="play-video-row3"<?php if($board->meta->mobile_row == 'play-video-row3'):?> selected<?php endif?>>3개</option>
							<option value="play-video-row4"<?php if($board->meta->mobile_row == 'play-video-row4'):?> selected<?php endif?>>4개</option>
							<option value="play-video-row5"<?php if($board->meta->mobile_row == 'play-video-row5'):?> selected<?php endif?>>5개</option>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" style="width: 210px;"><label for="pc_latest_row">PC 한 줄에 표시할 최신글 수</label></th>
					<td>
						<select name="pc_latest_row" id="pc_latest_row">
							<option value="play-video-row1"<?php if($board->meta->pc_latest_row == 'play-video-row1'):?> selected<?php endif?>>1개</option>
							<option value="play-video-row2"<?php if(!$board->meta->pc_latest_row || $board->meta->pc_latest_row == 'play-video-row2'):?> selected<?php endif?>>2개 (기본)</option>
							<option value="play-video-row3"<?php if($board->meta->pc_latest_row == 'play-video-row3'):?> selected<?php endif?>>3개</option>
							<option value="play-video-row4"<?php if($board->meta->pc_latest_row == 'play-video-row4'):?> selected<?php endif?>>4개</option>
							<option value="play-video-row5"<?php if($board->meta->pc_latest_row == 'play-video-row5'):?> selected<?php endif?>>5개</option>
						</select>
						<p class="description">자동으로 설정할 경우 게시판 너비에 따라 한 줄에 표시되는 게시글의 수가 설정됩니다.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" style="width: 210px;"><label for="mobile_row">모바일 한 줄에 표시할 최신글 수</label></th>
					<td>
						<select name="mobile_latest_row" id="mobile_latest_row">
							<option value="play-video-row1"<?php if(!$board->meta->mobile_latest_row || $board->meta->mobile_latest_row == 'play-video-row1'):?> selected<?php endif?>>1개 (기본)</option>
							<option value="play-video-row2"<?php if($board->meta->mobile_latest_row == 'play-video-row2'):?> selected<?php endif?>>2개</option>
							<option value="play-video-row3"<?php if($board->meta->mobile_latest_row == 'play-video-row3'):?> selected<?php endif?>>3개</option>
							<option value="play-video-row4"<?php if($board->meta->mobile_latest_row == 'play-video-row4'):?> selected<?php endif?>>4개</option>
							<option value="play-video-row5"<?php if($board->meta->mobile_latest_row == 'play-video-row5'):?> selected<?php endif?>>5개</option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
		$html = ob_get_clean();
		return $html;
	}
}

if(!function_exists('kboard_play_video_skin_header')){
	add_action('kboard_skin_header', 'kboard_play_video_skin_header', 10, 1);
	function kboard_play_video_skin_header($builder){
		global $play_video_skin_dir_name;

		$board = $builder->board;
		if($board->skin == $play_video_skin_dir_name){ // 실제 게시판 id로 적용해주세요.
			if(wp_is_mobile() && $board->meta->mobile_page_rpp){
				$builder->rpp = $board->meta->mobile_page_rpp; // 모바일에서 표시할 게시글의 수
			}
		}
	}
}

add_filter("kboard_{$play_video_skin_dir_name}_extends_setting_update", 'kboard_play_video_extends_setting_update', 10, 2);
if(!function_exists('kboard_play_video_extends_setting_update')){
	function kboard_play_video_extends_setting_update($board_meta, $board_id){
		$board_meta->mobile_page_rpp	= isset($_POST['mobile_page_rpp'])	 ? sanitize_textarea_field($_POST['mobile_page_rpp'])	: '';
		$board_meta->pc_row				= isset($_POST['pc_row'])			 ? sanitize_textarea_field($_POST['pc_row'])			: '';
		$board_meta->mobile_row			= isset($_POST['mobile_row'])		 ? sanitize_textarea_field($_POST['mobile_row'])		: '';
		$board_meta->pc_latest_row		= isset($_POST['pc_latest_row'])	 ? sanitize_textarea_field($_POST['pc_latest_row'])		: '';
		$board_meta->mobile_latest_row  = isset($_POST['mobile_latest_row']) ? sanitize_textarea_field($_POST['mobile_latest_row']) : '';
	}
}

if(!function_exists('kboard_play_video_list')){
	function kboard_play_video_list($board){
		$classes = array();
		if(!wp_is_mobile() && $board->meta->pc_row){
			$classes[] = "{$board->meta->pc_row}";
		}
		if(wp_is_mobile() && $board->meta->mobile_row){
			$classes[] = "{$board->meta->mobile_row}";
		}
		
		$classes = implode(' ', $classes);
		
		return $classes;
	}
}

if(!function_exists('kboard_play_video_latest')){
	function kboard_play_video_latest($board){
		$classes = array();
		if(!wp_is_mobile() && $board->meta->pc_latest_row){
			$classes[] = "{$board->meta->pc_latest_row}";
		}
		if(wp_is_mobile() && $board->meta->mobile_latest_row){
			$classes[] = "{$board->meta->mobile_latest_row}";
		}
		
		$classes = implode(' ', $classes);
		
		return $classes;
	}
}