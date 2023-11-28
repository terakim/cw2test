<?php

global $wp_query;

$page      = msps_get( $wp_query->query_vars, 'mshop-point', 1 );
$wallet_id = 'all';

$current_language = apply_filters( 'msps_get_current_language', mshop_wpml_get_current_language() );

$user  = new MSPS_User( get_current_user_id(), $current_language );
$wallet_items = $user->wallet->load_wallet_items( array(), false );

$logs = MSPS_Log::get_logs( get_current_user_id(), $page, 10, $wallet_id, $current_language );

wp_localize_script( 'msps-myaccount', '_msps', array(
	'ajax_url'    => admin_url( 'admin-ajax.php' ),
	'slug'        => MSPS_AJAX_PREFIX,
	'_ajax_nonce' => wp_create_nonce( 'mshop-point-ex' ),
	'page'        => $page,
	'last_page'   => ceil( $logs['total_count'] / 10 )
) );

?>
<div class="msps-logs-wrapper">
    <div class="msps-logs">
	    <?php if ( count( $wallet_items ) > 0 ) : ?>
            <select name="msps_wallet_id">
                <option value="all" <?php echo 'all' == $wallet_id ? 'selected' : ''; ?>><?php _e( '모두', 'mshop-point-ex' ); ?></option>
			    <?php foreach ( $wallet_items as $wallet_item ) : ?>
                    <option value="<?php echo $wallet_item->get_id(); ?>" <?php echo $wallet_item->get_id() == $wallet_id ? 'selected' : ''; ?>><?php echo $wallet_item->label; ?></option>
			    <?php endforeach; ?>
            </select>
	    <?php endif; ?>
        <table class="msps-logs">
            <thead>
            <tr>
                <th><?php _e( '순번', 'mshop-point-ex' ); ?></th>
                <th><?php _e( '날짜', 'mshop-point-ex' ); ?></th>
                <th><?php _e( '타입', 'mshop-point-ex' ); ?></th>
                <th><?php _e( '적립포인트', 'mshop-point-ex' ); ?></th>
                <th><?php _e( '누적포인트', 'mshop-point-ex' ); ?></th>
                <th><?php _e( '상태', 'mshop-point-ex' ); ?></th>
                <th style="text-align: center"><?php _e( '비고', 'mshop-point-ex' ); ?></th>
            </tr>
            </thead>
            <tbody>
			<?php wc_get_template( '/myaccount/msps-point-log-fragment.php', array( 'logs' => $logs['results'] ), '', MSPS()->template_path() ); ?>
            </tbody>
        </table>
    </div>

    <div class="msps-logs-pagination">
    </div>
</div>