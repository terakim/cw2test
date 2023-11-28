<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<script language="JavaScript">
    jQuery(document).ready(function($) {
        $(document).on('edit_address_popup_init', function() {
            (new window.edit_address_popup()).init( <?php echo $order->get_id(); ?>, '<?php echo $edit_address; ?>');
        });
    });
</script>
<input type="button" class="button button-primary ty-btn msaddr_edit_btn edit_address_<?php echo $order->get_id(); ?>" value="<?php _e( '배송지 변경', 'mshop-address-ex' ); ?>"/>

<div class="msaddr_edit_address_popup hide">
    <div class="msaddr_edit_address_popup_bg"></div>
    <div class="edit_address_popup_wrapper">
        <div class="edit_address_popup_head">
            <h3><?php _e( '배송지 변경', 'mshop-address-ex' ); ?></h3>
            <span class="msaddr_close"><img class="lf_mobile_close" src="<?php echo MSADDR()->plugin_url() . '/assets/images/m_menu_close.png'; ?>"></span>
        </div>
        <div class="edit_address_content">
            <?php if ( class_exists( 'PGALL_For_WooCommerce_DIY_Checkout' ) && 'yes' == get_option( 'pafw_dc_use_address_book', 'no' ) ) : ?>
	            <?php
	            MSADDR_Myaccount::output_address_book( array( 'update_shipping' => 'yes', 'order_id' => $order->get_id() ) );
	            ?>
            <?php else: ?>
                <form name="update_shipping" class="edit_address_popup popup_<?php echo $order->get_id(); ?>">
		            <?php
		            wc_get_template( 'mshop-address/form-shipping.php', array (
			            'checkout_fields' => $checkout_fields,
			            'order'           => $order
		            ), '', MSADDR()->template_path() );
		            ?>
                </form>
            <?php endif; ?>
            <div class="msaddr_shipping_buttons">
                <input type="button" class="msaddr_update" value="수정">
                <input type="button" class="msaddr_close" value="닫기">
            </div>
        </div>
    </div>
</div>
