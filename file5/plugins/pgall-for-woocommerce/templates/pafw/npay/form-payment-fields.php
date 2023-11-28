<?php

$uid = uniqid( 'pafw_npay_' );

if ( ! is_account_page() && is_user_logged_in() && 'user' == pafw_get( $gateway->settings, 'management_batch_key', 'subscription' ) ) {
	$bill_key = get_user_meta( get_current_user_id(), $gateway->get_subscription_meta_key( 'bill_key' ), true );

	if ( ! empty( $bill_key ) ) {
		$bill_key = substr_replace( $bill_key, 'XXXXXXXXXX', 12 );
	}
}

?>
<script>
    jQuery( document ).ready( function ( $ ) {
        var $wrapper = $( 'div.npay-payment-fields' );

        $( 'input.change-card', $wrapper ).on( 'click', function () {
            $( 'div.billing_info', $wrapper ).css( 'display', 'none' );
            $( 'div.pafw-card-info', $wrapper ).css( 'display', 'block' );
            $( 'input[name=npay_issue_bill_key]', $wrapper ).val( 'yes' );
        } );
    } );
</script>

<div class="npay-payment-fields">
	<?php if ( ! empty( $bill_key ) ) : ?>
        <div class="billing_info" style="margin-bottom: 10px;">
            <span style="margin-right: 20px; font-weight: bold;"><?php echo $bill_key; ?></span>
            <input type="button" class="button change-card" style="margin: 0 !important;" value="<?php _e( '재등록', 'pgall-for-woocommerce' ); ?>">
        </div>
	<?php endif; ?>
	<?php echo $gateway->get_description(); ?>
    <input type="hidden" name="npay_issue_bill_key" value="<?php echo empty( $bill_key ) ? 'yes' : 'no'; ?>">
</div>
