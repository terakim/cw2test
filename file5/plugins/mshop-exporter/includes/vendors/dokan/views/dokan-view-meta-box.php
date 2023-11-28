<?php

global $wp_scripts;

wp_enqueue_style( 'msex-meta-box-order', MSEX()->plugin_url() . '/assets/css/admin/msex-meta-box-order.css', array (), MSEX_VERSION );
wp_enqueue_script( 'msex-meta-box-order', MSEX()->plugin_url() . '/assets/js/admin/msex-meta-box-order.js', array ( 'jquery' ), MSEX_VERSION );
wp_localize_script( 'msex-meta-box-order', '_msex_meta_box_order', array (
    'ajax_url'      => admin_url( 'admin-ajax.php', 'relative' ),
    'slug'          => MSEX_AJAX_PREFIX,
    'order_id'      => msex_get_object_property( $order, 'id' ),
    'action_delete' => msex_ajax_command( 'dokan_delete_sheet_info' ),
    'action_update' => msex_ajax_command( 'dokan_update_sheet_info' ),
    '_wpnonce'      => wp_create_nonce( 'mshop-exporter' )
) );

$dlv_company = msex_load_dlv_company_info();

$dlv_code      = msex_get_meta( $order, '_msex_dlv_code', true );
$dlv_name      = msex_get_meta( $order, '_msex_dlv_name', true );
$sheet_no      = msex_get_meta( $order, '_msex_sheet_no', true );
$dlv_url       = msex_get_track_url( $dlv_code, $sheet_no );
$register_date = msex_get_meta( $order, '_msex_register_date', true );

?>
<script src="/wp-includes/js/dist/vendor/lodash.min.js?ver=4.17.19" id="lodash-js"></script><div id="msex-sheet-info" class="postbox">
<div class="" style="width:100%">
    <div class="dokan-panel dokan-panel-default">
        <div class="dokan-panel-heading"><strong><?php _e('송장관리', 'mshop-exporter'); ?></strong></div>
        <div class="dokan-panel-body" id="dokan-msex-sheet-info" style="font-size: 13px;">
            <div class="msex-sheet-info">
                <p>택배사</p>
                <select name="msex_dlv_code">
                    <option value="">택배사를 선택하세요.</option>
                    <?php foreach ( $dlv_company as $company ) : ?>
                        <?php echo sprintf( '<option value="%s" %s>%s</option>', $company['dlv_code'], $dlv_code == $company['dlv_code'] ? 'selected' : '', $company['dlv_name'] ); ?>
                    <?php endforeach; ?>
                </select>
                <p>송장번호</p>
                <input type="text" name="msex_sheet_no" value="<?php echo $sheet_no; ?>">
                <?php if ( ! empty( $register_date ) ) : ?>등록일 : <?php echo $register_date; ?><?php endif; ?>
            </div>
            <div class="msex_button_wrapper">
                <input type="button" class="button msex_action_button delete" <?php echo empty( $dlv_code ) ? 'disabled' : ''; ?> value="송장정보 삭제">
                <input type="button" class="button msex_action_button update" value="송장정보 업데이트">
            </div>
            <?php if ( ! empty( $dlv_url ) ) : ?>
                <div class="msex_button_wrapper">
                    <?php echo sprintf( '<a target="_blank" style="text-align: center;" class="button msex_action_button" href="%s">배송조회</a>', $dlv_url ); ?>
                </div>
            <?php endif; ?>
        </div>
        </div>
    </div>
</div>
