<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<table class="woocommerce-table woocommerce-table--order-details shop_table order_details">
    <tfoot>
	<?php
	foreach ( $fields as $key => $field ) {
		$get_key = 'get_' . $key;

		$value = is_callable( array ( $order, $get_key ) ) ? $order->$get_key : $order->$key;

		if ( ! empty( $value ) && ! in_array( $key, $reserved ) ) {
			?>
            <tr>
                <th><?php echo $field['label'] ?></th>
                <td><?php echo apply_filters( 'msaddr_custom_field_value', $value, $key, $field, $order ); ?></td>
            </tr>
			<?php
		}
	}
	?>
    </tfoot>
</table>
