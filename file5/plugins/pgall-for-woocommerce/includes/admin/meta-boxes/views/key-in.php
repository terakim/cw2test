<?php

$disabled = in_array( $order->get_status(), array ( 'pending', 'failed', 'cancelled', 'refunded' ) );

$history  = $order->get_meta('_pafw_additional_charge_history' );
$idx      = 1;

?>

<div class="pafw_payment_info">
    <?php $payment_gateway->key_in_payment_form(); ?>
</div>

<div class="pafw_button_wrapper">
    <input type="button" class="button pafw_action_button tips" id="pafw-request-key-in-payment" value="<?php _e( '결제요청', 'pgall-for-woocommerce' ); ?>" data-tip="입력한 신용카드 정보를 이용해서 결제를 요청합니다.">
</div>