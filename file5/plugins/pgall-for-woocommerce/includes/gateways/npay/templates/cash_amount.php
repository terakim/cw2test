<?php
?>
<style>
    #npay_cash_amount {
        padding: 5px !important;
    }

    table.npay-cash-amount {
        width: 100%;
        font-size: 13px;
    }
    table.npay-cash-amount th,
    table.npay-cash-amount td{
        padding: 5px 10px;
        text-align: right;
        font-weight: normal !important;
    }

    table.npay-cash-amount th{
        width: 70%;
    }

    table.npay-cash-amount tr:nth-child(even) {
        background-color: #e4edf0;
    }
</style>
<table class="npay-cash-amount">
    <tr>
        <th><?php _e( '결제수단', 'pgall-for-woocommerce' ); ?></th>
        <td><?php echo $params['primary_pay_means']; ?></td>
    </tr>
    <tr>
        <th><?php _e( '네이버페이 포인트 결제 금액', 'pgall-for-woocommerce' ); ?></th>
        <td><?php echo number_format( $params['npoint_cash_amount'] ); ?></td>
    </tr>
    <tr>
        <th><?php _e( '현금성 주 결제 수단 결제 금액', 'pgall-for-woocommerce' ); ?></th>
        <td><?php echo number_format( $params['primary_cash_amount'] ); ?></td>
    </tr>
    <tr>
        <th><?php _e( '현금 영수증 발행 대상 총 금액', 'pgall-for-woocommerce' ); ?></th>
        <td><?php echo number_format( $params['total_cash_amount'] ); ?></td>
    </tr>
    <tr>
        <th><?php _e( '공급가', 'pgall-for-woocommerce' ); ?></th>
        <td><?php echo number_format( $params['supply_cash_amount'] ); ?></td>
    </tr>
    <tr>
        <th><?php _e( '부가세', 'pgall-for-woocommerce' ); ?></th>
        <td><?php echo number_format( $params['vat_cash_amount'] ); ?></td>
    </tr>
</table>
