<?php

wp_enqueue_script( 'msps-file-upload', MSPS()->plugin_url() . '/assets/js/file-upload.js', array ( 'jquery', 'underscore' ), MSPS_VERSION );
wp_enqueue_script( 'msps_upload_point', MSPS()->plugin_url() . '/assets/js/admin/upload-point.js', array ( 'jquery', 'underscore', 'msps-file-upload', 'jquery-blockui' ), MSPS_VERSION );
wp_localize_script( 'msps_upload_point', 'msps_upload_point', array (
	'action_upload_point'  => msps_ajax_command( 'upload_point' ),
	'action_process_point' => msps_ajax_command( 'process_point' ),
	'_wpnonce'             => wp_create_nonce( 'mshop-point-ex' )
) );

wp_enqueue_style( 'msps_upload_point', MSPS()->plugin_url() . '/assets/css/admin/upload-point.css', array (), MSPS_VERSION );
wp_enqueue_style( 'msps-file-upload', MSPS()->plugin_url() . '/assets/css/file-upload.css', array (), MSPS_VERSION );

?>
<style>
    p.msps-usage {
        font-size: 0.9em;
        margin: 0.5em;
    }
</style>
<h3><?php _e( '포인트 일괄 등록', 'mshop-point-ex' ); ?></h3>
<p class="msps-usage">[1] 포인트 등록 정보가 기록된 CSV 파일을 선택한 후 "업로드" 버튼을 클릭합니다. 샘플 CSV 파일을 다운로드 받으시려면 <a href="<?php echo MSPS()->plugin_url() . '/assets/data/msps_point_upload.csv'; ?>">여기</a>를 클릭하세요.</p>
<p class="msps-usage">[2] 업로드된 포인트 등록 정보가 올바른지 확인한 후, "포인트 등록" 버튼을 클릭합니다. 등록된 포인트 정보는 <a href="<?php echo admin_url( 'admin.php' ); ?>?page=mshop_point_user_point">포인트 로그보기</a> 메뉴에서 확인할 수 있습니다.</p>

<form name="msps-upload-sheets">
    <input type="file" name="point-csv">
    <input type="button" class="upload-point button" value="업로드">
    <input type="button" class="process-point button" disabled value="포인트 일괄 등록">
</form>

<div class="msps-upload-data">
</div>