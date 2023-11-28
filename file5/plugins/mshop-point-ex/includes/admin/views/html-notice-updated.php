<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="message" class="updated msps-message wc-connect msps-message--success">
	<a class="msps-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'msps-hide-notice', 'update', remove_query_arg( 'do_update_mshop_point' ) ), 'msps_hide_notices_nonce', '_msps_notice_nonce' ) ); ?>"><?php _e( 'Dismiss', 'mshop-point-ex' ); ?></a>

	<p><?php _e( '포인트 데이터베이스 업데이트가 완료되었습니다.', 'mshop-point-ex' ); ?></p>
</div>
