<?php
$current_language = apply_filters( 'msps_get_current_language', mshop_wpml_get_current_language() );

$user = new MSPS_User( get_current_user_id(), $current_language );
?>

<?php if ( empty( $logs ) ) : ?>
    <tr>
        <td colspan="7" style="text-align: center;"><?php _e( '포인트 로그가 없습니다.', 'mshop-point-ex' ); ?></td>
    </tr>
<?php else: ?>
	<?php foreach ( $logs as $log ) : ?>
        <tr class="msps-log">
            <td class="no" data-title="<?php esc_attr_e( '순번', 'mshop-point-ex' ); ?>">
				<?php echo $log['id']; ?>
            </td>
            <td class="date" data-title="<?php esc_attr_e( '날짜', 'mshop-point-ex' ); ?>">
				<?php echo date( 'Y-m-d', strtotime( $log['date'] ) ); ?>
            </td>
            <td class="wallet-id" data-title="<?php esc_attr_e( '타입', 'mshop-point-ex' ); ?>">
				<?php
				$wallet_name = msps_get_wallet_name( $user, $log['wallet_id'] );
				echo $wallet_name == $log['wallet_id'] && ! empty( $log['wallet_name'] ) ? $log['wallet_name'] : $wallet_name;
				?>
            </td>
            <td class="amount" data-title="<?php esc_attr_e( '적립포인트', 'mshop-point-ex' ); ?>">
				<?php echo number_format( $log['amount'], wc_get_price_decimals() ); ?>
            </td>
            <td class="amount" data-title="<?php esc_attr_e( '누적포인트', 'mshop-point-ex' ); ?>">
				<?php echo number_format( $log['balance'], wc_get_price_decimals() ); ?>
            </td>
            <td class="status" data-title="<?php esc_attr_e( '상태', 'mshop-point-ex' ); ?>">
				<?php echo 'pending' == $log['status'] ? __( '예정', 'mshop-point-ex' ) : ( 'earn' == $log['type'] ? __( '적립', 'mshop-point-ex' ) : __( '차감', 'mshop-point-ex' ) ); ?>
            </td>
            <td class="desc" data-title="<?php esc_attr_e( '비고', 'mshop-point-ex' ); ?>">
				<?php
				if ( empty( $log['message'] ) ) {
					switch ( $log['action'] ) {
						case 'order' :
							$order = wc_get_order( $log['object_id'] );
							if ( $order && $order->get_customer_id() == get_current_user_id() ) {
								echo sprintf( __( "주문 포인트 <a target='_blank' href='%s'>#%s</a>", 'mshop-point-ex' ), $order->get_view_order_url(), $order->get_order_number() );
							} else {
								echo sprintf( __( "주문 포인트 #%d", 'mshop-point-ex' ), $log['object_id'] );
							}
							break;
						case 'purchase' :
							$order = wc_get_order( $log['object_id'] );
							if ( $order && $order->get_customer_id() == get_current_user_id() ) {
								echo sprintf( __( "포인트 할인 <a target='_blank' href='%s'>#%s</a>", 'mshop-point-ex' ), $order->get_view_order_url(), $order->get_order_number() );
							} else {
								echo sprintf( __( "포인트 할인 #%d", 'mshop-point-ex' ), $log['object_id'] );
							}
							break;
						case 'comment' :
							$comment = get_comment( $log['object_id'] );
							$post    = get_post( $comment->comment_post_ID );

							echo sprintf( __( '댓글 포인트 <a href="%s"><p class="meta"><abbr>%s</abbr></p></a>', 'mshop-point-ex' ), get_comment_link( $comment ), mb_substr( $comment->comment_content, 0, 20 ) );

							break;
						default :
							echo $log['object_id'];
					}
				} else {
					echo $log['message'];
				}
				?>
            </td>
        </tr>
	<?php endforeach; ?>
<?php endif; ?>