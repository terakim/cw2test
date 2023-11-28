<?php
defined( 'ABSPATH' ) || exit;

$max_useable_amount      = MSPS_Manager::max_useable_amount( WC()->cart );
$max_useable_point       = MSPS_Manager::max_useable_point( WC()->cart );
$purchase_minimum_point  = MSPS_Manager::purchase_minimum_point();
$purchase_minimum_amount = MSPS_Manager::purchase_minimum_amount();
$point_exchange_ratio    = MSPS_Manager::point_exchange_ratio();
$point_unit = get_option( 'mshop_point_system_point_unit_number' );

$used_point = isset( WC()->cart->mshop_point ) ? WC()->cart->mshop_point : 0;

$user       = new MSPS_User( get_current_user_id() );
$user_point = $user->get_point();

$enabled     = true;
$placeholder = 0 == $used_point ? sprintf( __( '%s 포인트를 사용할 수 있습니다.', 'mshop-point-ex' ), number_format( min( $max_useable_point, $user_point ) ) ) : '';

if ( 0 == $used_point && $max_useable_amount <= 0 ) {
	$enabled     = false;
	$placeholder = __( '포인트로 구매가능한 상품이 없습니다.', 'mshop-point-ex' );
} else if ( $used_point + $max_useable_amount < $purchase_minimum_amount ) {
	$enabled     = false;
	$placeholder = sprintf( __( '%s 이상 구매 시 포인트 사용이 가능합니다.', 'mshop-point-ex' ), wp_strip_all_tags( wc_price( floatval( $purchase_minimum_amount ) ) ) );
} else if ( $user->get_point() == 0 || $user->get_point() < $purchase_minimum_point ) {
	$enabled = false;
	if ( $user_point > 0 ) {
		$placeholder = sprintf( __( '%s 포인트 이상부터 사용이 가능합니다.', 'mshop-point-ex' ), number_format( floatval( $purchase_minimum_point ), wc_get_price_decimals() ) );
	} else {
		$placeholder = __( '보유 포인트가 없습니다.', 'mshop-point-ex' );
	}
}

$tooltips = array();
if ( $user_point > 0 ) {
	$tooltips[] = sprintf( __( '보유포인트 : %s', 'mshop-point-ex' ), number_format( floatval( $user_point ), wc_get_price_decimals() ) );
}
if ( ! empty( $point_unit ) ) {
	$tooltips[] = sprintf( __( '%s 포인트 단위로 사용할 수 있습니다.', 'mshop-point-ex' ), $point_unit );
}
?>
<style>
    div.cmpoint .point.disabled::placeholder {
        color: #fd7e7e;
    }

    div.tooltipster-content .pafw-coupon-popup.point-description {
        display: block !important;
    }

    div.tooltipster-content .pafw-coupon-popup.point-description td {
        font-weight: bold !important;
    }
</style>
<?php if ( ! empty( $tooltips ) ) : ?>
    <div class="pafw-coupon-popup point-description" style="display: none; width: auto !important;">
        <table class="pafw-coupon-table">
            <tr>
                <td><?php echo implode( '</td></tr><tr><td>', $tooltips ); ?></td>
            </tr>
        </table>
    </div>
<?php endif; ?>
<div class="pafw-point-wrap">
    <label class="cmpoint-title">포인트<span class="more-coupon" data-tooltip-content=".pafw-coupon-popup.point-description"><img src="<?php echo plugins_url( '/assets/images/m_more_que.png', PAFW_DIY_CHECKOUT_PLUGIN_FILE ); ?>" alt="more table" width="14"></span></label>
    <div class="cmpoint">
        <div class="coupon">
            <input type="text" name="mshop_point" class="input-text point <?php echo ! $enabled ? 'disabled' : ''; ?>" value="" placeholder="<?php echo $placeholder; ?>" <?php echo ! $enabled ? 'disabled' : ''; ?>>
			<?php if ( $enabled ) : ?>
                <input type="hidden" name="_mshop_point" value="<?php echo $used_point; ?>">
                <input type="button" class="pafw_apply_point" name="pafw_apply_point" value="<?php _e( '포인트 할인받기', 'mshop-point-ex' ); ?>" <?php echo ! $enabled ? 'disabled' : ''; ?>>
			<?php endif; ?>
        </div>
    </div>
</div>