<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MSADDR_Admin_Profile', false ) ) :
	class MSADDR_Admin_Profile {
		public function __construct() {
			add_action( 'personal_options_update', array ( $this, 'save_customer_meta_fields' ) );
			add_action( 'edit_user_profile_update', array ( $this, 'save_customer_meta_fields' ) );
		}
		public function save_customer_meta_fields( $user_id ) {
			if ( ! apply_filters( 'woocommerce_current_user_can_edit_customer_meta_fields', current_user_can( 'manage_woocommerce' ), $user_id ) ) {
				return;
			}

			if ( msaddr_enabled() && 'KR' == msaddr_get( $_POST, 'billing_country' ) ) {
				update_user_meta( $user_id, 'mshop_billing_address-postnum', $_POST['billing_postcode'] );
				update_user_meta( $user_id, 'mshop_billing_address-addr1', $_POST['billing_address_1'] );
				update_user_meta( $user_id, 'mshop_billing_address-addr2', $_POST['billing_address_2'] );
				update_user_meta( $user_id, 'billing_phone_kr', $_POST['billing_phone'] );
				update_user_meta( $user_id, 'billing_email_kr', $_POST['billing_email'] );
			}

			if ( msaddr_enabled() && 'KR' == msaddr_get( $_POST, 'shipping_country' ) ) {
				update_user_meta( $user_id, 'mshop_shipping_address-postnum', $_POST['shipping_postcode'] );
				update_user_meta( $user_id, 'mshop_shipping_address-addr1', $_POST['shipping_address_1'] );
				update_user_meta( $user_id, 'mshop_shipping_address-addr2', $_POST['shipping_address_2'] );
			}
		}
	}

endif;

return new MSADDR_Admin_Profile();
