<?php

$uid = uniqid( 'pafw_inicis_' );

if ( ! is_account_page() && is_user_logged_in() && 'user' == pafw_get( $gateway->settings, 'management_batch_key', 'subscription' ) ) {
	$bill_key = get_user_meta( get_current_user_id(), $gateway->get_subscription_meta_key( 'bill_key' ), true );

	if ( ! empty( $bill_key ) ) {
		$register_date = get_user_meta( get_current_user_id(), $gateway->get_subscription_meta_key( 'register_date' ), true );
		$card_num      = get_user_meta( get_current_user_id(), $gateway->get_subscription_meta_key( 'card_num' ), true );
		$card_name     = get_user_meta( get_current_user_id(), $gateway->get_subscription_meta_key( 'card_name' ), true );

		$card_num = substr_replace( $card_num, '00000000', 4, 8 );
		$card_num = implode( '-', str_split( $card_num, 4 ) );
	}
}

?>
<script>
    jQuery( document ).ready( function ( $ ) {
        var $wrapper = $( 'div.inicis-payment-fields' );

        $( 'input.change-card', $wrapper ).on( 'click', function () {
            $( 'div.billing_info', $wrapper ).css( 'display', 'none' );
            $( 'div.pafw-card-info', $wrapper ).css( 'display', 'block' );
            $( 'input[name=inicis_issue_bill_key]', $wrapper ).val( 'yes' );
        } );
    } );
</script>

<div class="inicis-payment-fields">
	<?php if ( ! empty( $bill_key ) ) : ?>
        <div class="billing_info" style="margin-bottom: 10px; font-size: 13px;">
            <span style="margin-right: 20px; font-weight: bold;"><?php echo $card_name . ' ( ' . $card_num . ' ) '; ?></span>
            <input type="button" class="button change-card" style="margin: 0 !important;" value="<?php _e( '다시등록', 'pgall-for-woocommerce' ); ?>">
        </div>
	<?php endif; ?>
	<?php echo $gateway->get_description(); ?>
    <input type="hidden" name="inicis_issue_bill_key" value="<?php echo empty( $bill_key ) ? 'yes' : 'no'; ?>">
</div>
