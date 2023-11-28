<?php
$sheet_data = array ();

if ( ! empty( $sheet_info['order_id'] ) ) {
	$order_id = $sheet_info['order_id'];
} else {
	$order_id = wc_get_order_id_by_order_item_id( $sheet_info['order_item_id'] );
}

$order = apply_filters( 'msex_get_order', wc_get_order( $order_id ), $order_id );

if ( ! empty( $sheet_info['dlv_code'] ) ) {
	$dlv_company = msex_get_dlv_company_info( $sheet_info['dlv_code'] );
	$dlv_name    = $dlv_company['dlv_name'];
	$dlv_url     = str_replace( '{msex_sheet_no}', $sheet_info['sheet_no'], $dlv_company['dlv_url'] );
} else {
	$dlv_company = '';
}


?>
<tr class="upload-sheet-items" data-sheet_data="<?php echo esc_attr( json_encode( $sheet_info ) ); ?>">
    <td class="status">준비</td>
    <td>
		<?php echo sprintf( '<a href="%s">#%s</a><br>%s', get_edit_post_link( $order->get_id() ), $order->get_order_number(), $order->get_formatted_shipping_address() ); ?>
    </td>
    <td>
		<?php
		$item_infos  = array ();
		$order_items = $order->get_items();
		foreach ( $order_items as $order_item ) {
		    if( empty( $sheet_info['order_item_id']) || $sheet_info['order_item_id'] == $order_item->get_id() ) {
		        if( current_user_can( 'manage_woocommerce' ) || get_current_user_id() == $order_item->get_meta('_vendor_id') || get_current_user_id() == $order->get_meta( '_dokan_vendor_id' ) ) {
                    $item_infos[] = sprintf( '#%d, %s', $order_item->get_id(), $order_item->get_name() );
                }
		    }
		}

		echo implode( '<br>', $item_infos );
		?>
    </td>
    <td>
		<?php if ( ! empty( $dlv_company ) ) : ?>
			<?php echo sprintf( '%s ( <a target="_blank" href="%s">%s</a> )', $dlv_name, $dlv_url, $sheet_info['sheet_no'] ); ?>
		<?php endif; ?>
    </td>
    <td>
		<?php if ( ! empty( $sheet_info['order_status'] ) ) : ?>
			<?php echo wc_get_order_status_name( $sheet_info['order_status'] ); ?>
		<?php endif; ?>
    </td>
</tr>
