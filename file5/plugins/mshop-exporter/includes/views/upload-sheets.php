<?php

?>
<table class="wp-list-table widefat fixed striped msex-sheet-importer">
    <thead>
    <tr>
        <th class="status">상태</th>
        <th class="order">주문정보</th>
        <th class="order-item">주문아이템 정보</th>
        <th class="sheet-info">송장정보</th>
        <th class="order-status">주문상태</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach( $sheet_infos as $sheet_info ) : ?>
        <?php include 'upload-sheet-item.php'; ?>
    <?php endforeach; ?>
    </tbody>
</table>


