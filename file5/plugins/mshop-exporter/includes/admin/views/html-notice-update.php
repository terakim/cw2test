<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="notice notice-error">
    <p><strong><?php _e( '엠샵 Import/Export 데이터 업데이트', 'mshop-exporter' ); ?></strong> &#8211; <?php _e( '엠샵 Import/Export 데이터베이스를 최신 버전으로 업데이트 하셔야 합니다.', 'mshop-exporter' ); ?></p>
    <p class="submit"><a href="<?php echo esc_url( add_query_arg( 'do_update_msex', 'true', admin_url( 'admin.php?page=mshop_exporter' ) ) ); ?>" class="msex-update-now button-primary"><?php _e( '업데이트 진행하기', 'mshop-exporter' ); ?></a></p>
</div>
<script type="text/javascript">
    jQuery( '.msex-update-now' ).click( 'click', function() {
        return window.confirm( '<?php echo esc_js( __( '업데이트를 진행하기 전에 데이터베이스를 백업 받으시는 것을 추천합니다. 업데이트를 진행하시겠습니까?', 'mshop-exporter' ) ); ?>' );
    });
</script>
