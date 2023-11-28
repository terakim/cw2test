<?php

?>
<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide msm-social">
    <label for="account_first_name"><?php esc_html_e( 'SNS 연동', 'woocommerce' ); ?></label>
    <img src="<?php echo $provider->get_image(); ?>">
    <span style="margin-left: 5px;"><?php echo sprintf( __( '%s 아이디로 로그인', 'mshop-members-s2' ), $provider->get_title() ); ?></span>
</p>