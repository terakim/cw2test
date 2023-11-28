<div class="kboard-attr-row kboard-attr-title">
	<label class="attr-name">지도 표시 좌표 (경도) <span class="attr-required-text">*</span></label>
	<div class="attr-value">
		<input type="text" name="kboard_option_map_location_lng" value="<?php echo $content->option->map_location_lng?>" placeholder="(예제) 127.027574">
		<div class="description">※ 좌표 입력시 구글지도가 자동으로 표시되며 위치는 일부 오차가 발생할 수 있습니다. 잘못된 좌표입력시 오류가 발생됩니다.</div>
		<div class="description"><button type="button" class="kboard-worldmap-franchise-button-small" onclick="kboard_worldmap_franchise_address_to_gps(this.form)">지도 표시 주소 → 지도 표시 좌표 입력</button></div>
	</div>
</div>