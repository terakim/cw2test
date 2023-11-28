<?php

?>
<table class="wp-list-table widefat fixed striped msex-sheet-importer">
    <thead>
    <tr>
        <th class="status">상태</th>
        <th class="user">사용자</th>
        <th class="wallet_id">포인트 월렛</th>
        <th class="action">액션</th>
        <th class="point">포인트</th>
	    <?php if ( has_filter( 'wpml_object_id' ) ) : ?>
            <th class="point">언어</th>
	    <?php endif; ?>
        <th class="message">메시지</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach( $point_infos as $point_info ) : ?>
        <?php include 'upload-point-item.php'; ?>
    <?php endforeach; ?>
    </tbody>
</table>


