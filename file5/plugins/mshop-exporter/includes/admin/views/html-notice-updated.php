<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="message" class="updated msex-message wc-connect msex-message--success">
	<a class="msex-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'msex-hide-notice', 'update', remove_query_arg( 'do_update_msex' ) ), 'msex_hide_notices_nonce', '_msex_notice_nonce' ) ); ?>"><?php _e( 'Dismiss', 'mshop-exporter' ); ?></a>

	<p><?php _e( '엠샵 Import/Export 데이터베이스 업데이트가 완료되었습니다.', 'mshop-exporter' ); ?></p>
</div>
