<?php
load_plugin_textdomain('kboard-ocean-franchise', false, dirname(plugin_basename(__FILE__)) . '/languages');

$skin_dir_name = basename(dirname(__FILE__));

if(!function_exists('kboard_ocean_franchise_extends_setting')){
	add_filter('kboard_'.$skin_dir_name.'_extends_setting', 'kboard_ocean_franchise_extends_setting', 10, 3);
	function kboard_ocean_franchise_extends_setting($html, $meta, $board_id){
		$board = new KBoard($board_id);
		
		echo '<table class="form-table"><tbody>';
		
		echo '<tr valign="top">';
		echo '<th scope="row">구글 지도 자바스크립트 API 키</th><td>';
		echo '<input type="text" class="regular-text" name="google_maps_api_key" value="'.$board->meta->google_maps_api_key.'" placeholder="Maps JavaScript API">';
		echo '<p class="description">구글 지도 표시를 위해 사용됩니다.</p>';
		echo '<p class="description"><a href="https://blog.cosmosfarm.com/?p=389" onclick="window.open(this.href);return false;" title="구글 지도 API 키 발급 받는 방법 (Maps JavaScript API)">구글 지도 API 키 발급 받는 방법 (Maps JavaScript API)</a></p>';
		echo '</td></tr>';
		
		echo '</tbody></table>';
		
		return $html;
	}
}

if(!function_exists('kboard_ocean_franchise_extends_setting_update')){
	add_filter('kboard_'.$skin_dir_name.'_extends_setting_update', 'kboard_ocean_franchise_extends_setting_update', 10, 2);
	function kboard_ocean_franchise_extends_setting_update($board_meta, $board_id){
		$board_meta->google_maps_api_key = isset($_POST['google_maps_api_key']) ? sanitize_text_field($_POST['google_maps_api_key']) : '';
	}
}

if(!function_exists('kboard_ocean_franchise_google_maps_api_key')){
	function kboard_ocean_franchise_google_maps_api_key($board){
		$google_maps_api_key = $board->meta->google_maps_api_key ? $board->meta->google_maps_api_key : get_option('kboard_google_api_key');
		return apply_filters('kboard_ocean_franchise_google_maps_api_key', $google_maps_api_key);
	}
}

if(!function_exists('kboard_ocean_franchise_branch')){
	function kboard_ocean_franchise_branch($branch){
		switch($branch){
			case '서울' : $branch = 'seoul'; break;
			case '부산' : $branch = 'busan'; break;
			case '대구' : $branch = 'daegu'; break;
			case '인천' : $branch = 'incheon'; break;
			case '광주' : $branch = 'gwangju'; break;
			case '대전' : $branch = 'daejeon'; break;
			case '울산' : $branch = 'ulsan'; break;
			case '세종' : $branch = 'sejong'; break;
			case '경기도' : $branch = 'gyeonggi'; break;
			case '경상남도' : $branch = 'gyeongnam'; break;
			case '경상북도' : $branch = 'gyeongbuk'; break;
			case '전라남도' : $branch = 'jeollanam'; break;
			case '전라북도' : $branch = 'jeollabuk'; break;
			case '충청남도' : $branch = 'chungnam'; break;
			case '충청북도' : $branch = 'chungbuk'; break;
			case '강원도' : $branch = 'gangwon'; break;
			case '제주도' : $branch = 'jeju'; break;
		}
		
		$image = "map-v2-$branch.png";
		
		return $image;
	}
}

if(!function_exists('kboard_ocean_franchise_branch_display')){
	function kboard_ocean_franchise_branch_display($branch){
		switch($branch){
			case '서울' : $branch = __('Seoul', 'kboard-ocean-franchise'); break;
			case '부산' : $branch = __('Busan', 'kboard-ocean-franchise'); break;
			case '대구' : $branch = __('Daegu', 'kboard-ocean-franchise'); break;
			case '인천' : $branch = __('Incheon', 'kboard-ocean-franchise'); break;
			case '광주' : $branch = __('Gwangju', 'kboard-ocean-franchise'); break;
			case '대전' : $branch = __('Daejeon', 'kboard-ocean-franchise'); break;
			case '울산' : $branch = __('Ulsan', 'kboard-ocean-franchise'); break;
			case '세종' : $branch = __('Sejong', 'kboard-ocean-franchise'); break;
			case '경기도' : $branch = __('Gyeonggi', 'kboard-ocean-franchise'); break;
			case '경상남도' : $branch = __('Gyeongnam', 'kboard-ocean-franchise'); break;
			case '경상북도' : $branch = __('Gyeongbuk', 'kboard-ocean-franchise'); break;
			case '전라남도' : $branch = __('Jeollanam', 'kboard-ocean-franchise'); break;
			case '전라북도' : $branch = __('Jeollabuk', 'kboard-ocean-franchise'); break;
			case '충청남도' : $branch = __('Chungnam', 'kboard-ocean-franchise'); break;
			case '충청북도' : $branch = __('Chungbuk', 'kboard-ocean-franchise'); break;
			case '강원도' : $branch = __('Gangwon', 'kboard-ocean-franchise'); break;
			case '제주도' : $branch = __('Jeju', 'kboard-ocean-franchise'); break;
		}
		return $branch;
	}
}

if(!function_exists('kboard_ocean_franchise_skin_editor_header_after')){
	add_action('kboard_skin_editor_header_after', 'kboard_ocean_franchise_skin_editor_header_after', 10, 2);
	function kboard_ocean_franchise_skin_editor_header_after($content, $board){
		if($board->skin == 'ocean-franchise'){
		?>
		<h4 class="kboard-attr-wrap-title">
			<?php if($content->uid):?>
				<?php echo __('Edit Branch', 'kboard-ocean-franchise')?>
			<?php else:?>
				<?php echo __('Register Branch', 'kboard-ocean-franchise')?>
			<?php endif?>
		</h4>
		<?php
		}
	}
}

if(!function_exists('kboard_ocean_franchise_skin_field')){
	add_filter('kboard_skin_fields', 'kboard_ocean_franchise_skin_field', 10, 2);
	function kboard_ocean_franchise_skin_field($fields, $board){
		if($board->skin == 'ocean-franchise'){
			if(isset($fields['title'])){
				if(!$fields['title']['field_name']){
					$fields['title']['field_name'] = __('Address', 'kboard-ocean-franchise');
				}
				if(!$fields['title']['description']){
					$fields['title']['description'] = '※ 게시판에 표시되는 주소를 입력해주세요.';
				}
			}
			if(!isset($fields['map_address'])){
				$fields['map_address'] = array(
					'field_type' => 'text',
					'field_label' => '지도 표시 주소',
					'field_name' => '지도 표시 주소',
					'class' => 'kboard-attr-map-address',
					'hidden' => '',
					'meta_key' => 'map_address',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'placeholder' => '(예제) 서울특별시 강남구 강남대로 396',
					'required' => '',
					'show_document' => '',
					'description' => '※ 주소 입력시 구글지도가 자동으로 표시되며 위치는 일부 오차가 발생할 수 있습니다. (지번주소 또는 도로명주소 입력)',
					'close_button' => 'yes'
				);
			}
			if(!isset($fields['map_location'])){
				$fields['map_location'] = array(
					'field_type' => 'text',
					'field_label' => '지도 표시 좌표',
					'field_name' => '지도 표시 좌표',
					'class' => 'kboard-attr-map-location',
					'hidden' => '',
					'meta_key' => 'map_location',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'placeholder' => '(예제) 37.497913, 127.027574',
					'required' => '',
					'show_document' => '',
					'description' => '※ 좌표 입력시 구글지도가 자동으로 표시되며 위치는 일부 오차가 발생할 수 있습니다. 잘못된 좌표입력시 오류가 발생됩니다.',
					'close_button' => 'yes'
				);
			}
			if(!isset($fields['tel'])){
				$fields['tel'] = array(
					'field_type' => 'text',
					'field_label' => '연락처',
					'field_name' => '연락처',
					'class' => 'kboard-attr-tel',
					'hidden' => '',
					'meta_key' => 'tel',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'placeholder' => '(예제) 02-0000-0000',
					'required' => '',
					'show_document' => '1',
					'description' => '',
					'close_button' => 'yes'
				);
			}
			if(!isset($fields['homepage'])){
				$fields['homepage'] = array(
					'field_type' => 'text',
					'field_label' => '홈페이지',
					'field_name' => '홈페이지',
					'class' => 'kboard-attr-homepage',
					'hidden' => '',
					'meta_key' => 'homepage',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'placeholder' => '',
					'required' => '',
					'show_document' => '1',
					'description' => '',
					'close_button' => 'yes'
				);
				
				$new_fields = array(
					'option'=>array('meta_key'=>'option'),
					'category1'=>array('meta_key'=>'category1'),
					'author'=>array('meta_key'=>'author'),
					'title'=>array('meta_key'=>'title'),
					'map_address'=>array('meta_key'=>'map_address'),
					'map_location'=>array('meta_key'=>'map_location'),
					'tel'=>array('meta_key'=>'tel'),
					'homepage'=>array('meta_key'=>'homepage')
				);
				$fields = array_merge($new_fields, $fields);
			}
		}
		
		return $fields;
	}
}

if(!function_exists('kboard_ocean_franchise_get_template_field_html')){
	add_filter('kboard_get_template_field_html', 'kboard_ocean_franchise_get_template_field_html', 10, 4);
	function kboard_ocean_franchise_get_template_field_html($field_html, $field, $content, $board){
		if($board->skin == 'ocean-franchise'){
			$meta_key = (isset($field['meta_key']) && $field['meta_key']) ? $field['meta_key'] : '';
			$field_name = (isset($field['field_name']) && $field['field_name']) ? $field['field_name'] : $board->fields()->getFieldLabel($field);
			$required = (isset($field['required']) && $field['required']) ? 'required' : '';
			$default_value = (isset($field['default_value']) && $field['default_value']) ? $field['default_value'] : '';
			$fields = $board->fields();
			$boardBuilder = new KBoardBuilder($board->id);
			
			if($field['field_type'] == 'option'){
				ob_start();
				?>
				<div class="kboard-attr-row <?php echo esc_attr($field['class'])?>">
					<label class="attr-name" for="<?php echo esc_attr($meta_key)?>"><span class="field-name"><?php echo esc_html($field_name)?></span></label>
					<div class="attr-value">
						<?php if($fields->isUseFields($field['secret_permission'], $field['secret'])):?>
							<label class="attr-value-option"><input type="checkbox" name="secret" value="true" onchange="kboard_toggle_password_field(this)"<?php if($content->secret):?> checked<?php endif?>> <?php echo __('Secret', 'kboard')?></label>
						<?php endif?>
						<?php if($fields->isUseFields($field['notice_permission'], $field['notice'])):?>
							<label class="attr-value-option"><input type="checkbox" name="notice" value="true"<?php if($content->notice):?> checked<?php endif?>> <?php echo __('Notice', 'kboard')?></label>
						<?php endif?>
						<?php do_action('kboard_skin_editor_option', $content, $board, $boardBuilder)?>
						<?php if(isset($field['description']) && $field['description']):?><div class="description"><?php echo esc_html($field['description'])?></div><?php endif?>
					</div>
				</div>
				<?php
				$field_html = ob_get_clean();
			}
			else if($field['field_type'] == 'category1'){
				ob_start();
				?>
				<div class="kboard-attr-row">
					<label class="attr-name" for="category1"><?php echo __('Area', 'kboard-ocean-franchise')?></label>
					<div class="attr-value">
						<select id="category1" name="category1">
							<option value=""><?php echo __('Select', 'kboard')?></option>
							<option value="서울"<?php if($content->category1 == '서울'):?> selected<?php endif?>><?php echo __('Seoul', 'kboard-ocean-franchise')?></option>
							<option value="부산"<?php if($content->category1 == '부산'):?> selected<?php endif?>><?php echo __('Busan', 'kboard-ocean-franchise')?></option>
							<option value="대구"<?php if($content->category1 == '대구'):?> selected<?php endif?>><?php echo __('Daegu', 'kboard-ocean-franchise')?></option>
							<option value="인천"<?php if($content->category1 == '인천'):?> selected<?php endif?>><?php echo __('Incheon', 'kboard-ocean-franchise')?></option>
							<option value="광주"<?php if($content->category1 == '광주'):?> selected<?php endif?>><?php echo __('Gwangju', 'kboard-ocean-franchise')?></option>
							<option value="대전"<?php if($content->category1 == '대전'):?> selected<?php endif?>><?php echo __('Daejeon', 'kboard-ocean-franchise')?></option>
							<option value="울산"<?php if($content->category1 == '울산'):?> selected<?php endif?>><?php echo __('Ulsan', 'kboard-ocean-franchise')?></option>
							<option value="세종"<?php if($content->category1 == '세종'):?> selected<?php endif?>><?php echo __('Sejong', 'kboard-ocean-franchise')?></option>
							<option value="경기도"<?php if($content->category1 == '경기도'):?> selected<?php endif?>><?php echo __('Gyeonggi', 'kboard-ocean-franchise')?></option>
							<option value="경상남도"<?php if($content->category1 == '경상남도'):?> selected<?php endif?>><?php echo __('Gyeongnam', 'kboard-ocean-franchise')?></option>
							<option value="경상북도"<?php if($content->category1 == '경상북도'):?> selected<?php endif?>><?php echo __('Gyeongbuk', 'kboard-ocean-franchise')?></option>
							<option value="전라남도"<?php if($content->category1 == '전라남도'):?> selected<?php endif?>><?php echo __('Jeollanam', 'kboard-ocean-franchise')?></option>
							<option value="전라북도"<?php if($content->category1 == '전라북도'):?> selected<?php endif?>><?php echo __('Jeollabuk', 'kboard-ocean-franchise')?></option>
							<option value="충청남도"<?php if($content->category1 == '충청남도'):?> selected<?php endif?>><?php echo __('Chungnam', 'kboard-ocean-franchise')?></option>
							<option value="충청북도"<?php if($content->category1 == '충청북도'):?> selected<?php endif?>><?php echo __('Chungbuk', 'kboard-ocean-franchise')?></option>
							<option value="강원도"<?php if($content->category1 == '강원도'):?> selected<?php endif?>><?php echo __('Gangwon', 'kboard-ocean-franchise')?></option>
							<option value="제주도"<?php if($content->category1 == '제주도'):?> selected<?php endif?>><?php echo __('Jeju', 'kboard-ocean-franchise')?></option>
						</select>
					</div>
				</div>
				<?php
				$field_html = ob_get_clean();
			}
			else if($field['field_type'] == 'author'){
				ob_start();
				?>
				<div class="kboard-attr-row kboard-attr-author required">
					<label class="attr-name" for="kboard-ocean-franchise-branch">
						<span class="field-name"><?php echo __('Branch', 'kboard-ocean-franchise')?></span>
						<span class="attr-required-text">*</span>
					</label>
					<div class="attr-value">
						<input type="text" id="kboard-ocean-franchise-branch" name="member_display" class="required" value="<?php echo $content->member_display?>">
					</div>
				</div>
				<?php
				$field_html = ob_get_clean();
			}
			else if($meta_key == 'homepage'){
				ob_start();
				?>
				<?php if($board->viewUsernameField()):?>
				<div class="kboard-attr-row">
					<label class="attr-name" for="kboard-input-password">
						<span class="field-name"><?php echo __('Password', 'kboard')?></span>
						<span class="attr-required-text">*</span>
					</label>
					<div class="attr-value"><input type="password" id="kboard-input-password" name="password" value="<?php echo $content->password?>" placeholder="<?php echo __('Password', 'kboard')?>..."></div>
				</div>
				<?php else:?>
				<div style="overflow:hidden;width:0;height:0;">
					<input style="width:0;height:0;background:transparent;color:transparent;border:none;" type="text" name="fake-autofill-fields">
					<input style="width:0;height:0;background:transparent;color:transparent;border:none;" type="password" name="fake-autofill-fields">
				</div>
				<!-- 비밀글 비밀번호 필드 시작 -->
				<div class="kboard-attr-row secret-password-row"<?php if(!$content->secret):?> style="display:none"<?php endif?>>
					<label class="attr-name" for="kboard-input-password">
						<span class="field-name"><?php echo __('Password', 'kboard')?></span>
						<span class="attr-required-text">*</span>
					</label>
					<div class="attr-value"><input type="password" id="kboard-input-password" name="password" value="<?php echo $content->password?>" placeholder="<?php echo __('Password', 'kboard')?>..."></div>
				</div>
				<!-- 비밀글 비밀번호 필드 끝 -->
				<?php endif?>
				<?php
				$field_html .= ob_get_clean();
			}
		}
		
		return $field_html;
	}
}