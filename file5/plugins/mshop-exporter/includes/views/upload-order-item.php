<?php
$order_data = array ();

$user = get_user_by( 'email', $order_info['billing_email'] );

$billing               = array (
	'id'                 => $user ? $user->ID : 0,
	'billing_first_name' => $order_info['billing_first_name'],
	'billing_email'      => $order_info['billing_email'],
	'billing_phone'      => $order_info['billing_phone'],
);
$order_data['billing'] = $billing;

$shipping               = array (
	'shipping_country'    => msex_get( $order_info, 'shipping_country', 'KR' ),
	'shipping_first_name' => msex_get( $order_info, 'shipping_first_name', $order_info['billing_first_name'] ),
	'shipping_phone'      => msex_get( $order_info, 'shipping_phone', $order_info['billing_phone'] ),
	'shipping_email'      => msex_get( $order_info, 'shipping_email', $order_info['billing_email'] ),
	'shipping_postcode'   => msex_get( $order_info, 'shipping_postcode' ),
	'shipping_address'    => msex_get( $order_info, 'shipping_address' ),
);
$order_data['shipping'] = $shipping;
$attributes     = array ();
$attribute_keys = array ();
foreach ( $order_info as $key => $value ) {
	if ( 0 === strpos( $key, 'pa_' ) && ! empty( $value ) ) {
		$attribute_keys[] = $key;
	}
}

foreach ( $attribute_keys as $attribute_key ) {
	$term = get_term_by( 'name', $order_info[ $attribute_key ], $attribute_key );

	$attributes[ $attribute_key ] = array (
		'slug'  => $term->slug,
		'name'  => $order_info[ $attribute_key ],
		'label' => wc_attribute_label( $attribute_key )
	);
}
$product = wc_get_product( $order_info['product_id'] );

$product_info               = array (
	'id'         => $order_info['product_id'],
	'title'      => $product->get_title(),
	'attributes' => $attributes,
	'qty'        => $order_info['qty'],
	'item_meta'  => msex_get_meta_data( $order_info, '_order_item_meta' ),
	'price'      => msex_get( $order_info, 'price' ),
);
$order_data['product_info'] = $product_info;
$order_data['order_meta']      = msex_get_meta_data( $order_info, '_order_meta' );
$order_data['order_comments']  = msex_get( $order_info, 'order_comments' );
$order_data['shipping_method'] = msex_get( $order_info, 'shipping_method' );
$order_data['order_status']    = msex_get( $order_info, 'order_status', 'processing' );
$order_data['order_note']      = msex_get( $order_info, 'order_note' );
$order_data['discount']      = msex_get( $order_info, 'discount' );

?>
<tr class="upload-order-items" data-order_data="<?php echo esc_attr( json_encode( $order_data ) ); ?>">
    <td class="status">준비</td>
    <td>
		<?php if ( ! empty( $billing['id'] ) ) : ?>
			<?php echo sprintf( '%s ( <a href="%s">#%d</a>, %s )<br>%s', $billing['billing_first_name'], get_edit_user_link( $billing['id'] ), $billing['id'], $billing['billing_email'], $billing['billing_phone'] ); ?>
		<?php else : ?>
			<?php echo sprintf( '%s ( %s )<br>%s', $billing['billing_first_name'], $billing['billing_email'], $billing['billing_phone'] ); ?>
		<?php endif; ?>
    </td>
    <td>
		<?php echo sprintf( '%s ( %s )', $shipping['shipping_first_name'], $shipping['shipping_phone'] ); ?>
		<?php echo sprintf( '<br>(%s) %s', $shipping['shipping_postcode'], $shipping['shipping_address'] ); ?>
    </td>
    <td>
		<?php
		$output = array ();
		foreach ( $product_info['attributes'] as $attribute ) {
			$output[] = sprintf( '%s : %s', $attribute['label'], $attribute['name'] );
		}

		?>
		<?php echo sprintf( '<a href="%s">#%d</a>,  %s x %d<br>%s', get_edit_post_link( $product_info['id'] ), $product_info['id'], $product_info['title'], $product_info['qty'], implode( '<br>', $output ) ); ?>
    </td>
    <td>
		<?php
		$meta_info = array ();

		$output = array ();
		foreach ( msex_get_meta_data( $order_info, '_order_meta' ) as $key => $value ) {
			$output[] = sprintf( '%s : %s', $key, $value );
		}

		if ( ! empty( $output ) ) {
			$meta_info[] = sprintf( __( '주문 메타<br>%s', 'mshop-exporter' ), implode( '<br>', $output ) );
		}

		$output = array ();
		foreach ( msex_get_meta_data( $order_info, '_order_item_meta' ) as $key => $value ) {
			$output[] = sprintf( '%s : %s', $key, $value );
		}

		if ( ! empty( $output ) ) {
			$meta_info[] = sprintf( __( '주문아이템 메타<br>%s', 'mshop-exporter' ), implode( '<br>', $output ) );
		}

		?>
		<?php echo implode( '<br>', $meta_info ); ?>
    </td>
    <td>
		<?php echo sprintf( '<p>%s</p>', $order_info['order_comments'] ); ?>
    </td>
</tr>
