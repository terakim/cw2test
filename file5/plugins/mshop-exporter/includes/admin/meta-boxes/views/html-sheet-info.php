<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$dlv_code      = $item->get_meta( '_msex_dlv_code' );
$dlv_name      = $item->get_meta( '_msex_dlv_name' );
$sheet_no      = $item->get_meta( '_msex_sheet_no' );
$register_date = $item->get_meta( '_msex_register_date' );

?>

<style>
    div.msex-sheet-info {
    }

    div.msex-sheet-info p {
        margin: 3px 0 !important;
        font-size: 0.9em !important;
    }

    .msex-show-edit-sheet {
        width: 16px !important;
        height: 16px !important;
        vertical-align: middle;
        cursor: pointer;
    }

    div.msex-edit-sheet-info {
        display: flex;
    }

    div.msex-edit-sheet-info input,
    div.msex-edit-sheet-info select {
        flex: 1;
        margin: 3px;
        font-size: 0.9em !important;
    }

    div.msex-edit-sheet-info select {
        flex: 0.7;
    }

    div.msex-edit-sheet-info input[type=button] {
        font-size: 0.9em !important;
        line-height: inherit !important;
        margin: 3px;
    }

    div.msex-edit-sheet-info-wrapper.show-edit-sheet {
        display: block !important;;
    }
</style>
<div class="msex-sheet-info" data-item_id="<?php echo $item_id; ?>">
	<?php if ( ! empty( $dlv_code ) && ! empty( $sheet_no ) ) : ?>
        <div>
            <p class="product_order_id"><?php echo sprintf( __( '택배사 : %s', 'mshop-exporter' ), $dlv_name ); ?></p>
        </div>
        <div>
            <p class="product_order_status"><?php echo sprintf( __( '송장번호 : %s', 'mshop-exporter' ), $sheet_no ); ?>
                <img src="<?php echo MSEX()->plugin_url() . '/assets/images/detail-info.png'; ?>" class="msex-show-edit-sheet">
            </p>
        </div>
        <div>
            <p class="register_date"><?php echo sprintf( __( '등록일 : %s', 'mshop-exporter' ), $register_date ); ?></p>
        </div>
	<?php else: ?>
        <div>
            <p class="product_order_status"><?php _e( '송장정보가 없습니다.', 'mshop-exporter' ); ?>
                <img src="<?php echo MSEX()->plugin_url() . '/assets/images/detail-info.png'; ?>" class="msex-show-edit-sheet">
            </p>
        </div>
	<?php endif; ?>
    <div class="msex-edit-sheet-info-wrapper" style="display: none;">
        <div class="msex-edit-sheet-info">
            <select name="dlv_code[<?php echo $item_id; ?>]">
                <option value="">택배사</option>
				<?php foreach ( msex_load_dlv_company_info() as $company ) : ?>
					<?php echo sprintf( '<option value="%s" %s>%s</option>', $company['dlv_code'], $dlv_code == $company['dlv_code'] ? 'selected' : '', $company['dlv_name'] ); ?>
				<?php endforeach; ?>
            </select>
            <input type="text" name="sheet_no[<?php echo $item_id; ?>]" value="<?php echo $sheet_no; ?>" placeholder="<?php _e( '송장번호', 'mshop-exporter' ); ?>">
        </div>
        <div class="msex-edit-sheet-info">
            <input type="button" class="button delete-sheet" <?php echo empty( $dlv_code ) ? 'disabled' : ''; ?> value="삭제하기">
            <input type="button" class="button update-sheet" value="업데이트">
            <input type="button" class="button view-sheet" <?php echo empty( $dlv_code ) ? 'disabled' : ''; ?> value="배송조회" data-url="<?php echo msex_get_track_url( $dlv_code, $sheet_no ); ?>">
        </div>
    </div>
</div>