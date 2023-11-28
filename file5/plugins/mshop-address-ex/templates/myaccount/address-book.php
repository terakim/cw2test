<?php
wp_enqueue_script( 'jquery-ui-tabs' );

add_filter( 'msaddr_form_field_display_style', function ( $style ) {
	return 'flex';
} );

$destinations = MSADDR_DIY_Checkout::get_shipping_destinations( 1, '', $address_type );

$default_destination_key = MSADDR_DIY_Checkout::get_default_shipping_destination_key( $address_type );
$default_destination     = MSADDR_DIY_Checkout::get_default_shipping_destination( $address_type );
$address_per_page        = apply_filters( 'msaddr_address_per_page', 5 );

wp_enqueue_style( 'msaddr_fontawesome', plugins_url( '/assets/vendor/fontawesome/css/all.min.css', MSADDR_PLUGIN_FILE ), array(), MSADDR_VERSION );
wp_enqueue_script( 'msaddr_diy_checkout', plugins_url( '/assets/js/diy-address-book.js', MSADDR_PLUGIN_FILE ), array( 'jquery', 'underscore' ), MSADDR_VERSION );
wp_localize_script( 'msaddr_diy_checkout', '_msaddr_diy_checkout', array(
	'ajax_url'            => admin_url( 'admin-ajax.php' ),
	'slug'                => MSADDR_AJAX_PREFIX,
	'destinations'        => $destinations,
	'default_key'         => $default_destination_key,
	'default_destination' => $default_destination,
	'address_type'        => $address_type,
	'update_shipping'     => 'yes' == msaddr_get( $params, 'update_shipping', 'no' ),
	'fields'              => 'billing' == $address_type ? MSADDR_Address_Book::get_billing_fields() : MSADDR_Address_Book::get_shipping_fields(),
	'last_page'           => ceil( $destinations['total'] / $address_per_page ),
	'_wpnonce'            => wp_create_nonce( 'msaddr-diy-checkout' )
) );

$form_classes = array(
	"pafw-checkout",
	"pafw-{$address_type}-fields",
	"woocommerce-checkout",
	"addr-style-" . msaddr_get( $params, 'addr_style' )
);

if ( ! empty( $params['order_id'] ) ) {
	$form_classes = array_merge( $form_classes, array( "edit_address_popup", "popup_" . $params['order_id'] ) );
}

?>
<div class="pafw-checkout-block type-b">
	<?php do_action( 'pafw_dc_before_' . $address_type . '_fields_block', $params ); ?>
    <h3><?php _e( '배송지 정보', 'mshop-address-ex' ); ?></h3>
    <div id="pafw-dc-address" class="tab-style-<?php echo $params['tab_style']; ?> list-style-<?php echo $params['list_style']; ?>" data-default_destination="<?php echo esc_html( json_encode( $default_destination ) ); ?>" data-default_key="<?php echo $default_destination_key; ?>">
        <ul>
            <li class="destinations-tab">
                <input type="radio" name="pafw-dc-address" value="default-shipping" class="hidden">
                <label><a href="#destinations"><?php _e( '배송지목록', 'mshop-address-ex' ); ?></a></label>
            </li>
            <li class="new-destination-tab">
                <input type="radio" name="pafw-dc-address" value="default-shipping" class="hidden">
                <label><a href="#new-destination"><?php _e( '신규배송지', 'mshop-address-ex' ); ?></a></label>
            </li>
        </ul>
        <div id="new-destination" class="info-panel">
            <div class="field-style-<?php echo msaddr_get( $params, 'field_style' ); ?>">
                <form name="pafw-<?php echo $address_type; ?>-fields" class="<?php echo implode( " ", $form_classes ); ?>">
                    <div class="woocommerce-<?php echo $address_type; ?>-fields__field-wrapper">
						<?php
						$fields = WC()->checkout()->get_checkout_fields( $address_type );

						foreach ( $fields as $key => $field ) {
							woocommerce_form_field( $key, $field, $address_type . '_country' == $key ? 'KR' : get_user_meta( get_current_user_id(), $key, true ) );
						}
						?>

                        <input type="hidden" name="msaddr_shipping_destination_key" value="new">
                        <div class="save-address-row">
                            <input type="button" class="button button-primary save-address" value="저장">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div id="destinations" class="info-panel">
            <div class="destination-search">
                <input type="text" name="msaddr-search-key" placeholder="<?php _e( '이름 및 전화번호, 주소로 배송지를 검색할 수 있습니다.', 'mshop-address-ex' ); ?>">
            </div>
            <div class="destinations-wrap">
				<?php wc_get_template( "myaccount/address-book-fragment.php", array( 'destinations' => $destinations, 'address_type' => $address_type ), '', MSADDR()->template_path() ); ?>
            </div>
            <div class="destinations-nav">
            </div>
        </div>
    </div>

	<?php do_action( 'pafw_dc_after_' . $address_type . '_fields_block', $params ); ?>
</div>
