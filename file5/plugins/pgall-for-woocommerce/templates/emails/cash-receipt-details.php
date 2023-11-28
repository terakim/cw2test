<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$receipt_request = PAFW_Cash_Receipt::get_receipt_request( $order->get_id() );

if ( empty( $receipt_request ) ) {
	return;
}

?>

<div class="pafw-payment-details-section">
    <h2><?php echo __( '현금영수증', 'pgall-for-woocommerce' ); ?></h2>

    <table class="pafw-payment-details woocommerce-table woocommerce-table--order-details shop_table order_details">
        <thead>
        <tr>
            <th class="woocommerce-table__ex-table usage"><?php _e( '용도', 'pgall-for-woocommerce' ); ?></th>
            <th class="woocommerce-table__ex-table reg_number"><?php _e( '발행정보', 'pgall-for-woocommerce' ); ?></th>
            <th class="woocommerce-table__ex-table reg_number"><?php _e( '현금영수증번호', 'pgall-for-woocommerce' ); ?></th>
            <th class="woocommerce-table__ex-table status"><?php _e( '상태', 'pgall-for-woocommerce' ); ?></th>
            <th class="woocommerce-table__ex-table status"><?php _e( '일자', 'pgall-for-woocommerce' ); ?></th>
            <th class="woocommerce-table__ex-table action"><?php _e( '비고', 'pgall-for-woocommerce' ); ?></th>
        </tr>
        </thead>
        <tr>
            <td><?php echo PAFW_Cash_Receipt::get_usage_label( $order->get_meta( '_pafw_bacs_receipt_usage' ) ) ?></td>
            <td><?php echo $order->get_meta( '_pafw_bacs_receipt_reg_number' ) ?></td>
            <td><?php echo PAFW_Cash_Receipt::get_status_name( $receipt_request['status'] ); ?></td>
            <td><?php echo $order->get_meta( '_pafw_bacs_receipt_receipt_number' ) ?></td>
            <td><?php
				$issue_date = $order->get_meta( '_pafw_bacs_receipt_issue_date' );
				if ( ! empty( $issue_date ) ) {
					echo date( 'Y-m-d', strtotime( $issue_date ) );
				}
				?>
            <td><?php echo $receipt_request['message']; ?></td>
            <td>
            </td>
        </tr>
    </table>
</div>

<div class="msmp-email-section payment-info">
    <h4><?php _e( '현금영수증', 'pgall-for-woocommerce' ); ?></h4>
    <table class="pafw-payment-details woocommerce-table woocommerce-table--order-details shop_table order_details">
        <thead>
        <tr>
            <th><?php _e( '용도', 'pgall-for-woocommerce' ); ?></th>
            <td><?php echo PAFW_Cash_Receipt::get_usage_label( $order->get_meta( '_pafw_bacs_receipt_usage' ) ); ?></td>
        </tr>
        <tr>
            <th><?php _e( '발행정보', 'pgall-for-woocommerce' ); ?></th>
            <td><?php echo $order->get_meta( '_pafw_bacs_receipt_reg_number' ); ?></td>
        </tr>
        <tr>
            <th><?php _e( '상태', 'pgall-for-woocommerce' ); ?></th>
            <td><?php echo PAFW_Cash_Receipt::get_status_name( $receipt_request['status'] ); ?></td>
        </tr>
		<?php if ( ! empty( $order->get_meta( '_pafw_bacs_receipt_receipt_number' ) ) ) : ?>
            <tr>
                <th><?php _e( '현금영수증번호', 'pgall-for-woocommerce' ); ?></th>
                <td><?php echo $order->get_meta( '_pafw_bacs_receipt_receipt_number' ) ?></td>
            </tr>
            <tr>
                <th><?php _e( '일자', 'pgall-for-woocommerce' ); ?></th>
                <td><?php echo PAFW_Cash_Receipt::get_usage_label( $order->get_meta( '_pafw_bacs_receipt_usage' ) ) ?></td>
            </tr>
		<?php endif; ?>
        <tr>
            <th><?php _e( '비고', 'pgall-for-woocommerce' ); ?></th>
            <td><?php echo PAFW_Cash_Receipt::get_usage_label( $order->get_meta( '_pafw_bacs_receipt_usage' ) ) ?></td>
        </tr>
    </table>
</div>