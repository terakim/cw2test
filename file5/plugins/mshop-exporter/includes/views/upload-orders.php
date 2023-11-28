<?php

?>
<table class="wp-list-table widefat fixed striped msex-order-importer">
    <thead>
    <tr>
        <th class="status">상태</th>
        <th class="billing">청구정보</th>
        <th class="shipping">배송정보</th>
        <th class="product-info">상품정보</th>
        <th class="meta">메타정보</th>
        <th class="order-comment">주문메모</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach( $order_infos as $order_info ) : ?>
        <?php include 'upload-order-item.php'; ?>
    <?php endforeach; ?>
    </tbody>
</table>


