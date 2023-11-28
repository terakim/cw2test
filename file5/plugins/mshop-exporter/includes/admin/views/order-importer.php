<?php

wp_enqueue_script( 'msex-file-upload', MSEX()->plugin_url() . '/assets/js/file-upload.js', array ( 'jquery', 'underscore' ), MSEX_VERSION );
wp_enqueue_script( 'msex_order_importer', MSEX()->plugin_url() . '/assets/js/admin/order-importer.js', array ( 'jquery', 'underscore', 'msex-file-upload', 'jquery-blockui' ), MSEX_VERSION );
wp_localize_script( 'msex_order_importer', 'msex_order_importer', array (
	'action_upload_orders' => msex_ajax_command( 'upload_orders' ),
	'action_create_orders' => msex_ajax_command ( 'create_orders' ),
	'_wpnonce'             => wp_create_nonce( 'mshop-exporter' )
) );

wp_enqueue_style( 'msex_order_importer', MSEX()->plugin_url() . '/assets/css/admin/order-importer.css', array (), MSEX_VERSION );
wp_enqueue_style( 'msex-file-upload', MSEX()->plugin_url() . '/assets/css/file-upload.css', array (), MSEX_VERSION );

?>
<style>
    p.msex-usage {
        font-size: 0.9em;
        margin: 0.5em;
    }
</style>

<h3><?php _e( '주문 업로드', 'mshop-exporter' ); ?></h3>
<p class="msex-usage">[1] 주문정보가 기록된 CSV 파일을 선택한 후 "업로드" 버튼을 클릭합니다. 샘플 CSV 파일을 다운로드 받으시려면 <a href="<?php echo MSEX()->plugin_url() . '/assets/data/codem_order_upload_sample.csv';?>">여기</a>를 클릭하세요.</p>
<p class="msex-usage">[2] 업로드된 주문 정보가 올바른지 확인한 후, "주문생성" 버튼을 클릭하면 주문이 생성됩니다.</p>

<form name="pafw-upload-orders">
    <input type="file" name="order-csv">
    <input type="button" class="upload-orders button" value="업로드">
    <input type="button" class="create-orders button" disabled value="주문생성">
</form>

<div class="pafw-upload-data">

</div>