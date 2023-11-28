<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$options = get_user_meta( get_current_user_id(), '_msaddr_shipping_history', true );

wp_enqueue_script( 'msaddr-book', MSADDR()->plugin_url() . '/assets/js/mshop-address-book.js', array(), MSADDR_VERSION );
wp_localize_script( 'msaddr-book', '_msaddr', array(
	'ajaxurl'     => admin_url( 'admin-ajax.php', 'relative' ),
	'slug'        => MSADDR_AJAX_PREFIX,
	'_ajax_nonce' => wp_create_nonce( 'delete_address_item' ),
	'fields'      => MSADDR_Address_Book::get_shipping_fields(),
	'defaults'    => array(
		'shipping_country' => WC()->countries->get_base_country()
	),
	'i18n'        => array(
		'confirm_delete_address' => __( '선택하신 배송지를 삭제하시겠습니까?', 'mshop-address-ex' )
	)
) );

?>

<div class="woocommerce-shipping-fields">
	<?php if ( true === WC()->cart->needs_shipping_address() ) : ?>
        <h3>
			<?php _e( '배송지 정보', 'mshop-address-ex' ); ?>
        </h3>

        <div id="ship-to-different-address" style="display: none;">
            <input id="ship-to-different-address-checkbox" class="input-checkbox" type="checkbox" name="ship_to_different_address" value="1" checked="checked"/>
        </div>

		<table class="address-book" style="width: 100%; <?php echo empty( $options ) ? 'display: none;' : ''; ?>">
			<?php if ( ! empty( $options ) ) : ?>
				<?php foreach ( $options as $key => $option ) : ?>
					<tr class="history" data-key="<?php echo $key; ?>"
						data-address="<?php echo esc_html( json_encode( $option ) ); ?>">
						<td class="address-checkbox"><input type="radio" name="address_item"><label for="address_item"></label></td>
						<td class="address-info"><?php echo MSADDR_Address_Book::get_formatted_address( $option ); ?></td>
						<?php if ( 'yes' == get_option( 'msaddr_book_can_edit_address', 'no' ) || 'yes' == get_option( 'msaddr_book_can_delete_address', 'no' ) ) : ?>
							<td class="address-btn">
								<?php if ( 'yes' == get_option( 'msaddr_book_can_edit_address', 'no' ) ) : ?>
									<img class="edit-address" src="<?php echo MSADDR()->plugin_url() . '/assets/images/edit_address.png'; ?>">
								<?php endif; ?>
								<?php if ( 'yes' == get_option( 'msaddr_book_can_delete_address', 'no' ) ) : ?>
									<img class="delete-address" src="<?php echo MSADDR()->plugin_url() . '/assets/images/delete_address.png'; ?>">
								<?php endif; ?>
							</td>
						<?php endif; ?>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
			<tr class="new">
				<td><input type="radio" name="address_item"><label for="address_item"></label></td>
				<td><?php _e( '새로운 주소로 배송', 'mshop-address-ex' ); ?></td>
				<?php if ( 'yes' == get_option( 'msaddr_book_can_edit_address', 'no' ) || 'yes' == get_option( 'msaddr_book_can_delete_address', 'no' ) ) : ?>
					<td></td>
				<?php endif; ?>
			</tr>
		</table>

		<div id="msaddr-hidden-container">
			<div class="shipping_address_edit_wrapper shipping_address" style="<?php echo ! empty( $options ) ? 'display: none;' : ''; ?>">

				<?php do_action( 'woocommerce_before_checkout_shipping_form', $checkout ); ?>

				<?php foreach ( $checkout->checkout_fields['shipping'] as $key => $field ) : ?>

					<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

				<?php endforeach; ?>

				<?php do_action( 'woocommerce_after_checkout_shipping_form', $checkout ); ?>

            </div>
        </div>
	<?php endif; ?>

	<?php do_action( 'woocommerce_before_order_notes', $checkout ); ?>

	<?php if ( apply_filters( 'woocommerce_enable_order_notes_field', get_option( 'woocommerce_enable_order_comments', 'yes' ) === 'yes' ) ) : ?>

		<?php if ( ! WC()->cart->needs_shipping() || wc_ship_to_billing_address_only() ) : ?>

            <h3><?php esc_html_e( 'Additional information', 'woocommerce' ); ?></h3>

		<?php endif; ?>

		<?php foreach ( $checkout->checkout_fields['order'] as $key => $field ) : ?>

			<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

		<?php endforeach; ?>

	<?php endif; ?>

	<?php do_action( 'woocommerce_after_order_notes', $checkout ); ?>
</div>
