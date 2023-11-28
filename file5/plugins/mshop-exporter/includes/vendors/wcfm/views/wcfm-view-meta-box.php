<?php

global $WCFM;
global $wp_scripts;

$order = wc_get_order( $order_id );

wp_enqueue_script( 'msex-meta-box-order', MSEX()->plugin_url() . '/assets/js/admin/msex-wcfm-meta-box-order.js', array ( 'jquery' ), MSEX_VERSION );
wp_localize_script( 'msex-meta-box-order', '_msex_meta_box_order', array (
    'ajax_url'      => admin_url( 'admin-ajax.php', 'relative' ),
    'slug'          => MSEX_AJAX_PREFIX,
    'order_id'      => msex_get_object_property( $order, 'id' ),
    'action_delete' => msex_ajax_command( 'wcfm_delete_sheet_info' ),
    'action_update' => msex_ajax_command( 'wcfm_update_sheet_info' ),
    '_wpnonce'      => wp_create_nonce( 'mshop-exporter' )
) );

wp_print_scripts( 'msex-meta-box-order' );


$dlv_company = msex_load_dlv_company_info();

$dlv_code      = msex_get_meta( $order, '_msex_dlv_code', true );
$dlv_name      = msex_get_meta( $order, '_msex_dlv_name', true );
$sheet_no      = msex_get_meta( $order, '_msex_sheet_no', true );
$dlv_url       = msex_get_track_url( $dlv_code, $sheet_no );
$register_date = msex_get_meta( $order, '_msex_register_date', true );
?>

<script src="/wp-includes/js/dist/vendor/lodash.min.js?ver=4.17.19" id="lodash-js"></script>

<br>
<div id="msex-sheet-info" class="postbox">
    <div class="page_collapsible"><?php _e('송장관리', 'mshop-exporter'); ?><span></span></div>
    <div class="wcfm-container">
        <div class="wcfm-content">
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
            <?php if ( ! empty( $register_date ) ) : ?>
                <p class="register_date">등록일 : <?php echo $register_date; ?></p>
            <?php endif; ?>
        </div>
        <div class="msex_button_wrapper">
            <input type="button" class="button msex_action_button delete wcfm_btn" <?php echo empty( $dlv_code ) ? 'disabled' : ''; ?> value="송장정보 삭제">
            <input type="button" class="button msex_action_button update wcfm_btn" value="송장정보 업데이트">
        </div>
    <?php if ( ! empty( $dlv_url ) ) : ?>
        <div class="msex_button_wrapper">
            <?php echo sprintf( '<a target="_blank" style="text-align: center;" class="button msex_action_button wcfm_btn" href="%s">배송조회</a>', $dlv_url ); ?>
        </div>
    <?php endif; ?>
        </div>
    </div>
</div>
