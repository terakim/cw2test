<div class="kboard-attr-row kboard-attr-title">
	<label class="attr-name">지도 표시 주소 <span class="attr-required-text">*</span></label>
	<div class="attr-value">
		<input type="text" name="kboard_option_map_address" value="<?php echo $content->option->map_address?>" placeholder="(예제) 서울특별시 강남구 강남대로 396">
		<div class="description">※ 주소 입력시 구글지도가 자동으로 표시되며 위치는 일부 오차가 발생할 수 있습니다. (지번주소 또는 도로명주소 입력)</div>
		<div class="description"><button type="button" class="kboard-worldmap-franchise-button-small" onclick="kboard_worldmap_franchise_gps_to_address(this.form)">지도 표시 좌표 → 지도 표시 주소 입력</button></div>
	</div>
</div>