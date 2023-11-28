<?php
$connected_provider = null;

foreach ( $enabled_providers as $provider ) {
	if ( $provider->is_connected() ) {
		$connected_provider = $provider;
		break;
	}
}

?>
<?php if ( is_null( $connected_provider ) ) : ?>
    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide msm-social">
        <label for="account_first_name"><?php esc_html_e( 'SNS 연동', 'woocommerce' ); ?></label>
		<?php foreach ( $enabled_providers as $provider ) : ?>
            <a href="<?php echo $provider->get_login_url(); ?>" class="button button-primary connect">
                <img src="<?php echo $provider->get_image(); ?>">
            </a>
		<?php endforeach; ?>
    </p>
<?php else: ?>
    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide msm-social">
        <label for="account_first_name"><?php esc_html_e( 'SNS 연동', 'woocommerce' ); ?></label>
        <img src="<?php echo $provider->get_image(); ?>">
        <span style="margin-left: 5px;"><?php echo sprintf( __( '%s 아이디로 로그인', 'mshop-members-s2' ), $connected_provider->get_title() ); ?></span>
		<?php
		$disconnect_url = wp_nonce_url( add_query_arg( array( 'action' => 'msm_social_disconnect', 'provider_id' => $connected_provider->get_id() ), remove_query_arg( array( 'action', 'provider_id' ) ) ), 'msm_social_disconnect' );
		?>
        <a href="<?php echo $disconnect_url; ?>" class="button button-primary disconnect"><?php _e( 'OFF', 'mshop-members-s2' ); ?></a>
    </p>
<?php endif; ?>