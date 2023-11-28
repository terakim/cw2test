<?php
$current_language = apply_filters( 'msps_get_current_language', mshop_wpml_get_current_language() );

$user  = new MSPS_User( get_current_user_id(), $current_language );
$point = $user->get_point();

$wallet_items = $user->wallet->load_wallet_items();
?>

<table class="shop_table msps_point_statistics responsive my_account_orders">
    <tbody>
        <?php do_action( 'msps_before_myaccount_table_row' ); ?>
        <tr class="msps-info">
            <td class="msps-title"><?php _e( '회원 등급', 'mshop-point-ex' ); ?></td>
            <td class="order-desc"><?php echo mshop_point_get_user_role_name(); ?></td>
        </tr>
        <tr class="msps-info">
            <td class="msps-title"><?php _e( '보유 포인트', 'mshop-point-ex' ); ?></td>
            <td class="order-desc"><?php echo ! empty( $point ) ? number_format( $point, wc_get_price_decimals() ) : 0; ?></td>
        </tr>
        <?php if ( count( $wallet_items ) > 1 ) : ?>
            <tr class="msps-info">
                <td colspan="2">
                    <?php do_action( 'msps_before_point_info' ); ?>
                    <?php foreach ( $wallet_items as $wallet_item ) : ?>
                        <div>
                            <p><?php echo $wallet_item->label; ?></p>
                            <p><?php echo number_format( $wallet_item->get_point(), wc_get_price_decimals() ); ?></p>
                        </div>
                    <?php endforeach; ?>
                    <?php do_action( 'msps_after_point_info' ); ?>
                </td>
            </tr>
        <?php endif; ?>
        <?php do_action( 'msps_after_myaccount_table_row' ); ?>
    </tbody>
</table>

<?php
do_action( 'myaccount_recommender_info' );