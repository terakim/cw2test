<?php

wp_enqueue_script( 'msex-file-upload', MSEX()->plugin_url() . '/assets/js/file-upload.js', array ( 'jquery', 'underscore' ), MSEX_VERSION );
wp_enqueue_script( 'msex_sheet_importer', MSEX()->plugin_url() . '/assets/js/admin/sheet-importer.js', array ( 'jquery', 'underscore', 'msex-file-upload', 'jquery-blockui' ), MSEX_VERSION );
wp_localize_script( 'msex_sheet_importer', 'msex_sheet_importer', array (
    'action_upload_sheets'   => msex_ajax_command( 'wcfm_upload_sheets' ),
    'action_register_sheets' => msex_ajax_command( 'wcfm_register_sheets' ),
    'processing_count'       => get_option( 'msex_processing_count', '10' ),
    '_wpnonce'               => wp_create_nonce( 'mshop-exporter' )
) );

?>

<script type="text/javascript">
    var ajaxurl = '<?php echo esc_js( admin_url( 'admin-ajax.php', 'relative' ) ); ?>'
</script>

<div class="collapse wcfm-collapse" id="wpwrap">
    <div class="wcfm-page-headig">
        <span class="wcfmfa fa-shipping-fast"></span>
        <span class="wcfm-page-heading-text"><?php _e( '엠샵 송장 업로드', 'mshop-exporter' ); ?></span>
        <?php do_action( 'wcfm_page_heading' ); ?>
    </div>

	<div class="wcfm-collapse-content dataTables_wrapper">
        <div class="wcfm-container wcfm-top-element-container">
            <h2><?php _e( '송장 업로드', 'mshop-exporter' ); ?></h2>
        </div>
        <br>
        <div class="wcfm-container">
            <div class="wcfm-content">
                <p class="msex-usage">[1] 송장정보가 기록된 CSV 파일을 선택한 후 "업로드" 버튼을 클릭합니다. 샘플 CSV 파일을 다운로드 받으시려면 <a href="<?php echo MSEX()->plugin_url() . '/assets/data/codem_sheet_upload_sample.csv'; ?>" class="wcfm_auctions_title">여기</a>를 클릭하세요.</p>
                <p class="msex-usage">[2] 업로드된 송장 정보가 올바른지 확인한 후, "송장정보 등록" 버튼을 클릭합니다. 등록된 송장정보는 주문편집 화면에서 수정 또는 삭제할 수 있습니다.</p>
                <?php if ( version_compare( WOOCOMMERCE_VERSION, '3.0.0', '>=' ) ) : ?>
                <br>
                <form name="pafw-upload-sheets">
                    <input type="file" name="sheet-csv">
                    <input type="button" class="upload-sheets button wcfm_btn" value="업로드">
                    <input type="button" class="create-sheets button wcfm_btn" disabled value="송장정보 등록">
                </form>

                <div class="pafw-upload-data">
                    <?php else: ?>
                        <h3 class="msex-notification error">송장 업로드 기능은 우커머스 3.0 이상 버전부터 이용하실 수 있습니다.</h3>
                    <?php endif; ?>
                </div>
            </div>
        </div>
	</div>
</div>