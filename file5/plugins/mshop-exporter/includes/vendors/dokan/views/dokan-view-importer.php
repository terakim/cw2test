<?php

wp_enqueue_script( 'msex-file-upload', MSEX()->plugin_url() . '/assets/js/file-upload.js', array ( 'jquery', 'underscore' ), MSEX_VERSION );
wp_enqueue_script( 'msex_sheet_importer', MSEX()->plugin_url() . '/assets/js/admin/sheet-importer.js', array ( 'jquery', 'underscore', 'msex-file-upload', 'jquery-blockui' ), MSEX_VERSION );
wp_localize_script( 'msex_sheet_importer', 'msex_sheet_importer', array (
    'action_upload_sheets'   => msex_ajax_command( 'dokan_upload_sheets' ),
    'action_register_sheets' => msex_ajax_command( 'dokan_register_sheets' ),
    'processing_count'       => get_option( 'msex_processing_count', '10' ),
    '_wpnonce'               => wp_create_nonce( 'mshop-exporter' )
) );

wp_enqueue_style( 'msex_sheet_importer', MSEX()->plugin_url() . '/assets/css/admin/sheet-importer.css', array (), MSEX_VERSION );
wp_enqueue_style( 'msex-file-upload', MSEX()->plugin_url() . '/assets/css/file-upload.css', array (), MSEX_VERSION );

?>
<script type="text/javascript">
    var ajaxurl = '<?php echo esc_js( admin_url( 'admin-ajax.php', 'relative' ) ); ?>'
</script>

<style>
    p.msex-usage {
        font-size: 0.8em;
        margin: 0.5em;
    }

    .dokan-dashboard-wrap .msex-usage a {
        color: #2271b1;
        text-decoration: underline;
    }

    .dokan-dashboard-wrap table {
        position: relative !important;
    }

    .dokan-dashboard-wrap table.msex-sheet-importer thead th.status {
        width: 80px;
    }

    .dokan-dashboard-wrap table tbody tr.upload-sheet-items:before {
        display: none;
    }

    .dokan-dashboard-wrap table tbody tr.upload-sheet-items.success {
        background-color: #f6f7f7;
    }

    .dokan-dashboard-wrap .file-upload-button {
        font-weight: normal;
        background-color: #f05025;
        color: #fff;
        padding: 6px 12px;
        min-height: 25px;
        line-height: 1.42857143;
        border-radius: 3px;
    }

    .dokan-dashboard-wrap input[type='button'].button {
        font-weight: normal;
        font-size: 13px;
        background-color: #fff;
        border-color: #2271b1;
        color: #2271b1;
        padding: 4px 12px;
        min-height: 25px;
        line-height: 1.42857143;
        border-radius: 3px;
    }

    .dokan-dashboard-wrap .button[disabled] {
        color: #a7aaad !important;
        border-color: #dcdcde !important;
        background: #f6f7f7 !important;
        box-shadow: none !important;
        cursor: default;
        transform: none !important;
    }
</style>
<div class="dokan-dashboard-wrap">
    <?php
    do_action( 'dokan_dashboard_content_before' );
    ?>

    <div class="dokan-dashboard-content">

        <?php
        do_action( 'dokan_help_content_inside_before' );
        ?>

        <div id="wpwrap">
            <header class="dokan-dashboard-header">
                <h1 class="entry-title"><?php _e( '송장 업로드', 'mshop-exporter' ); ?></h1>
            </header>
            <div class="dokan-panel dokan-panel-default">
                <div class="dokan-panel-body general-details">
                    <p class="msex-usage">[1] 송장정보가 기록된 CSV 파일을 선택한 후 "업로드" 버튼을 클릭합니다. 샘플 CSV 파일을 다운로드 받으시려면 <a href="<?php echo MSEX()->plugin_url() . '/assets/data/codem_sheet_upload_sample.csv'; ?>">여기</a>를 클릭하세요.</p>
                    <p class="msex-usage">[2] 업로드된 송장 정보가 올바른지 확인한 후, "송장정보 등록" 버튼을 클릭합니다. 등록된 송장정보는 주문편집 화면에서 수정 또는 삭제할 수 있습니다.</p>
                    <?php if ( version_compare( WOOCOMMERCE_VERSION, '3.0.0', '>=' ) ) : ?>
                    <form name="pafw-upload-sheets">
                        <input type="file" name="sheet-csv">
                        <input type="button" class="upload-sheets button" value="업로드">
                        <input type="button" class="create-sheets button" disabled value="송장정보 등록">
                    </form>

                    <div class="pafw-upload-data">
                        <?php else: ?>
                            <h3 class="msex-notification error">송장 업로드 기능은 우커머스 3.0 이상 버전부터 이용하실 수 있습니다.</h3>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php
        do_action( 'dokan_dashboard_content_inside_after' );
        ?>


    </div><!-- .dokan-dashboard-content -->

    <?php
    do_action( 'dokan_dashboard_content_after' );
    ?>

</div><!-- .dokan-dashboard-wrap -->