<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<div class="woocommerce-shipping-fields" style="clear: both;">
	<?php foreach ( $checkout_fields['shipping'] as $key => $field ) : ?>
		<?php woocommerce_form_field( $key, array_merge( $field, array ( 'order' => $order ) ), msaddr_get_checkout_field_value( $order, $key ) ); ?>
	<?php endforeach; ?>

	<?php foreach ( $checkout_fields['order'] as $key => $field ) : ?>
		<?php woocommerce_form_field( $key, $field,  msaddr_get_checkout_field_value( $order, $key ) ); ?>
	<?php endforeach; ?>
</div>

