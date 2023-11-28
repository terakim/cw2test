<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
function msaddr_get( $array, $key, $default = '' ) {
	return ! empty( $array[ $key ] ) ? $array[ $key ] : $default;
}
function msaddr_get_user_roles() {
	return array_diff_key( array_merge( wp_roles()->role_names, array( 'guest' => __( 'Guest', 'mshop-address-ex' ) ) ), array( 'administrator' => '' ) );
}
function msaddr_get_current_user_roles() {
	if ( is_user_logged_in() ) {
		$user = get_userdata( get_current_user_id() );

		return $user->roles;
	} else {
		return array( 'guest' );
	}
}
function msaddr_ajax_command( $command ) {
	return MSADDR_AJAX_PREFIX . '_' . $command;
}

if ( ! function_exists( 'msaddr_get_default_language' ) ) {
	function msaddr_get_default_language() {
		if ( function_exists( 'icl_object_id' ) ) {
			global $sitepress;

			return $sitepress->get_default_language();
		} else {
			return '';
		}
	}
}
function msaddr_process_billing( $params = null ) {
	if ( is_null( $params ) ) {
		$params = $_POST;
	}

	return apply_filters( 'msaddr_process_billing', msaddr_enabled() && 'KR' == $params['billing_country'] );
}
function msaddr_shipping_enabled() {
	return msaddr_enabled() && wc_shipping_enabled();
}

function msaddr_process_shipping( $params = null ) {
	if ( is_null( $params ) ) {
		$params = $_POST;
	}

	return apply_filters( 'msaddr_process_shipping', msaddr_shipping_enabled() && ! empty( $params['ship_to_different_address'] ) && 'KR' == $params['shipping_country'] );
}
function msaddr_enabled() {
	static $_msaddr_enabled = null;

	if ( is_null( $_msaddr_enabled ) ) {
		$_msaddr_enabled = 'no' != get_option( 'mshop_address_enable', 'yes' );
	}

	return $_msaddr_enabled;
}
function msaddr_need_scripts() {
	global $wp, $post;

	if ( ! msaddr_enabled() ) {
		return false;
	}

	if ( is_checkout() ) {
		return true;
	}

	if ( is_page( wc_get_page_id( 'myaccount' ) ) && ( isset( $wp->query_vars['orders'] ) || ! empty( $wp->query_vars['edit-address'] ) || is_view_order_page() ) ) {
		return true;
	}

	if ( ! empty( $post->post_content ) ) {
		if ( has_shortcode( $post->post_content, 'woocommerce_order_tracking' ) ) {
			return true;
		}
	}

	if ( $_SERVER['REQUEST_URI'] == '/order-tracking/' ) {
		return true;
	}

	if ( MSADDR_HPOS::enabled() && function_exists( 'get_current_screen') ) {
		$current_screen = get_current_screen();

		if ( $current_screen->id == MSADDR_HPOS::get_order_admin_screen() ) {
			return true;
		}
	}

	if ( is_admin() && $post && in_array( $post->post_type, wc_get_order_types() ) ) {
		return true;
	}

	return false;
}

function msaddr_update_user_address( $user_id, $load_address, $params ) {
	$postcode = msaddr_get( $params, 'mshop_' . $load_address . '_address-postnum' );
	$address1 = msaddr_get( $params, 'mshop_' . $load_address . '_address-addr1' );
	$address2 = msaddr_get( $params, 'mshop_' . $load_address . '_address-addr2' );

	if ( ! empty( $postcode ) && ! empty( $address1 ) ) {
		update_user_meta( $user_id, 'mshop_' . $load_address . '_address-postnum', $postcode );
		update_user_meta( $user_id, 'mshop_' . $load_address . '_address-addr1', $address1 );
		update_user_meta( $user_id, 'mshop_' . $load_address . '_address-addr2', $address2 );

		update_user_meta( $user_id, $load_address . '_postcode', $postcode );
		update_user_meta( $user_id, $load_address . '_address_1', $address1 );
		update_user_meta( $user_id, $load_address . '_address_2', $address2 );
	}
}
function msaddr_checkout_field_is_enabled( $fieldset = '' ) {
	if ( empty( $fieldset ) ) {
		return msaddr_enabled() && 'yes' == get_option( 'msaddr_enable_checkout_fields' );
	} else {
		return msaddr_enabled() && 'yes' == get_option( 'msaddr_enable_checkout_fields' ) && 'yes' == get_option( 'msaddr_enable_' . $fieldset . '_fields' );
	}
}
function msaddr_get_checkout_field_value( $order, $key ) {
	if ( 'order_comments' == $key ) {
		$key = 'customer_note';
	}

	$method = 'get_' . $key;

	if ( is_callable( array( $order, $method ) ) ) {
		$value = $order->$method();
	} else if ( property_exists( $order, $key ) ) {
		$value = $order->$key;
	} else {
		$value = $order->get_meta( '_' . $key );
	}

	return $value;
}
function msaddr_update_checkout_field_value( $order, $key, $value ) {
	$setter = 'set_' . $key;

	if ( is_callable( array( $order, $setter ) ) ) {
		$order->$setter( $value );
	} else {
		$order->update_meta_data( '_' . $key, $value );
	}
}
if ( get_option( 'mshop_address_enable', 'yes' ) == 'yes' ) {
	add_filter( 'woocommerce_billing_fields', array( 'MSADDR_Fields', 'billing_fields' ), 999, 2 );
	add_filter( 'woocommerce_shipping_fields', array( 'MSADDR_Fields', 'shipping_fields' ), 999, 2 );
	add_filter( 'woocommerce_form_field_mshop_address', array( 'MSADDR_Fields', 'output_address_fields' ), 10, 4 );
	add_filter( 'woocommerce_form_field_multiselect', array( 'MSADDR_Fields', 'output_multiselect_fields' ), 10, 4 );
	add_filter( 'woocommerce_admin_billing_fields', array( 'MSADDR_Fields', 'admin_billing_fields' ) );
	add_filter( 'woocommerce_admin_shipping_fields', array( 'MSADDR_Fields', 'admin_shipping_fields' ) );

	add_filter( 'woocommerce_process_checkout_field_billing_first_name', array( 'MSADDR_Fields', 'process_checkout_field' ) );
	add_filter( 'woocommerce_process_checkout_field_billing_email', array( 'MSADDR_Fields', 'process_checkout_field' ) );
	add_filter( 'woocommerce_process_checkout_field_billing_phone', array( 'MSADDR_Fields', 'process_checkout_field' ) );
	add_filter( 'woocommerce_process_checkout_field_shipping_first_name', array( 'MSADDR_Fields', 'process_checkout_field' ) );

	add_filter( 'woocommerce_order_formatted_billing_address', array( 'MSADDR_Fields', 'formatted_billing_address' ), 10, 2 );
	add_filter( 'woocommerce_order_formatted_shipping_address', array( 'MSADDR_Fields', 'formatted_shipping_address' ), 10, 2 );

	add_action( 'woocommerce_checkout_process', array( 'MSADDR_Checkout', 'checkout_process' ) );
	add_action( 'woocommerce_checkout_update_user_meta', array( 'MSADDR_Checkout', 'checkout_update_user_meta' ), 10, 2 );
	add_action( 'woocommerce_checkout_update_user_meta', array( 'MSADDR_Checkout', 'maybe_update_subscription_address_data' ), 99, 2 );
	add_action( 'woocommerce_checkout_update_order_meta', array( 'MSADDR_Checkout', 'checkout_update_order_meta' ), 10, 2 );
	add_action( 'woocommerce_subscriptions_switch_completed', array( 'MSADDR_Checkout', 'update_subscription_address' ) );
	add_filter( 'woocommerce_checkout_posted_data', array( 'MSADDR_Checkout', 'maybe_adjust_checkout_post_data' ) );

	add_action( 'woocommerce_customer_save_address', array( 'MSADDR_Myaccount', 'customer_save_address' ), 10, 2 );
	add_action( 'woocommerce_save_account_details', array( 'MSADDR_Myaccount', 'save_account_details' ) );
	add_action( 'wp_footer', array( 'MSADDR_Myaccount', 'hide_last_name_field' ) );
	add_filter( 'woocommerce_found_customer_details', array( 'MSADDR_Myaccount', 'woocommerce_found_customer_details' ), 10, 3 );
	add_filter( 'woocommerce_ajax_get_customer_details', array( 'MSADDR_Myaccount', 'woocommerce_ajax_get_customer_details' ), 10, 3 );

	add_filter( 'woocommerce_my_account_edit_address_field_value', array( 'MSADDR_Myaccount', 'edit_address_field_value' ), 10, 3 );
	if ( is_plugin_active( 'pgall-for-woocommerce-diy-checkout/pgall-for-woocommerce-diy-checkout.php' ) && 'yes' == get_option( 'pafw_dc_use_address_book', 'no' ) ) {
		add_filter( 'woocommerce_account_menu_items', array( 'MSADDR_Myaccount', 'add_address_book_menu' ) );
		add_filter( 'woocommerce_account_address-book_endpoint', array( 'MSADDR_Myaccount', 'output_address_book' ) );
	}

	add_action( 'woocommerce_admin_order_data_after_billing_address', array( 'MSADDR_Meta_Box_Order_Data', 'order_data_after_billing_address' ) );
	add_action( 'woocommerce_admin_order_data_after_shipping_address', array( 'MSADDR_Meta_Box_Order_Data', 'order_data_after_shipping_address' ) );
	add_action( 'woocommerce_admin_order_totals_after_shipping', array( 'MSADDR_Meta_Box_Order_Data', 'order_totals_after_shipping' ) );
	add_action( 'woocommerce_process_shop_order_meta', array( 'MSADDR_Meta_Box_Order_Data', 'update_address' ), 20, 2 );

	add_filter( 'msm_form_designer_enqueue_scripts', array( 'MSADDR_Members', 'admin_enqueue_scripts' ) );
	add_filter( 'msm_widget_reserved', array( 'MSADDR_Members', 'msm_widget_reserved' ) );
	add_filter( 'msm_field_rule_Address', array( 'MSADDR_Members', 'address_field_rules' ), 10, 2 );
	add_filter( 'msaddr_billing_fields', array( 'MSADDR_Checkout_Fields', 'billing_fields' ), 999, 2 );
	add_filter( 'msaddr_shipping_fields', array( 'MSADDR_Checkout_Fields', 'shipping_fields' ), 999, 2 );
	add_filter( 'woocommerce_checkout_fields', array( 'MSADDR_Checkout_Fields', 'checkout_fields' ), 999 );

	add_action( 'add_meta_boxes', array( 'MSADDR_Meta_Box_Order_Data', 'add_meta_boxes' ), 10, 2 );

	add_action( 'woocommerce_order_details_after_order_table', array( 'MSADDR_Checkout_Fields', 'woocommerce_order_details_after_customer_details' ) );
	add_action( 'woocommerce_checkout_update_order_meta', array( 'MSADDR_Checkout_Fields', 'checkout_update_order_meta' ), 10, 2 );
	add_filter( 'woocommerce_locate_template', 'MSADDR_Address_Book::woocommerce_locate_template', 99, 3 );
	add_action( 'woocommerce_checkout_order_processed', 'MSADDR_Address_Book::woocommerce_checkout_order_processed', 10, 2 );
	add_filter( 'woocommerce_localisation_address_formats', 'MSADDR_Address_Book::localisation_address_formats' );
	add_filter( 'woocommerce_formatted_address_replacements', 'MSADDR_Address_Book::formatted_address_replacements', 10, 2 );
	add_action( 'mshop_address_edit_address_popup', 'MSADDR_Address_Book::edit_address_popup', 10, 2 );
	add_action( 'woocommerce_order_details_after_order_table', array( 'MSADDR_Address_Book', 'woocommerce_order_details_after_customer_details' ), 99 );
	add_action( 'woocommerce_customer_save_address', array( 'MSADDR_WCS', 'maybe_update_subscription_addresses' ), 5, 2 );

	add_action( 'pafw_instant_payment_before_form_billing', 'msaddr_enqueue_script' );
	add_action( 'pafw_dc_before_billing_fields_block', array( 'MSADDR_DIY_Checkout', 'enqueue_script' ) );
	add_action( 'pafw_dc_before_shipping_fields_block', array( 'MSADDR_DIY_Checkout', 'enqueue_script' ) );
	add_action( 'woocommerce_checkout_order_processed', array( 'MSADDR_DIY_Checkout', 'maybe_update_shipping_destinations' ), 10, 2 );
	add_action( 'msaddr_before_update_address', array( 'MSADDR_DIY_Checkout', 'maybe_adjust_shipping_data' ), 10, 2 );
	add_action( 'msaddr_after_update_address', array( 'MSADDR_DIY_Checkout', 'maybe_update_shipping_destinations' ), 10, 2 );

	function msaddr_enqueue_script() {
		MSADDR()->enqueue_script();
	}

	add_action( 'woocommerce_before_checkout_form', 'msaddr_maybe_migrate_address_info' );
	add_action( 'woocommerce_before_edit_account_address_form', 'msaddr_maybe_migrate_address_info' );

	function msaddr_maybe_migrate_address_info( $checkout ) {
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();

			if ( version_compare( get_user_meta( $user_id, '_msaddr_version', true ), MSADDR_VERSION ) < 0 ) {
				$fields = array(
					'mshop_billing_address-postnum'  => 'billing_postcode',
					'mshop_billing_address-addr1'    => 'billing_address_1',
					'mshop_billing_address-addr2'    => 'billing_address_2',
					'billing_email_kr'               => 'billing_email',
					'billing_phone_kr'               => 'billing_phone',
					'billing_first_name_kr'          => array( 'billing_last_name', 'billing_first_name' ),
					'mshop_shipping_address-postnum' => 'shipping_postcode',
					'mshop_shipping_address-addr1'   => 'shipping_address_1',
					'mshop_shipping_address-addr2'   => 'shipping_address_2',
					'shipping_phone_kr'              => 'shipping_phone',
					'shipping_first_name_kr'         => array( 'shipping_last_name', 'shipping_first_name' )
				);

				foreach ( $fields as $target => $src ) {
					if ( empty( get_user_meta( $user_id, $target, true ) ) ) {
						if ( is_array( $src ) ) {
							$value = '';
							foreach ( $src as $src_field ) {
								$value .= get_user_meta( $user_id, $src_field, true );
							}
						} else {
							$value = get_user_meta( $user_id, $src, true );
						}

						update_user_meta( $user_id, $target, $value );
					}
				}

				update_user_meta( $user_id, '_msaddr_version', MSADDR_VERSION );
			}
		}
	}

	add_filter( 'woocommerce_customer_get_billing_first_name', 'msaddr_alter_billing_info', 10, 2 );
	add_filter( 'woocommerce_customer_get_billing_last_name', 'msaddr_alter_billing_info', 10, 2 );
	add_filter( 'woocommerce_customer_get_billing_email', 'msaddr_alter_billing_info', 10, 2 );
	add_filter( 'woocommerce_customer_get_billing_phone', 'msaddr_alter_billing_info', 10, 2 );
	add_filter( 'woocommerce_customer_get_billing_postcode', 'msaddr_alter_billing_info', 10, 2 );
	add_filter( 'woocommerce_customer_get_billing_address_1', 'msaddr_alter_billing_info', 10, 2 );
	add_filter( 'woocommerce_customer_get_billing_address_2', 'msaddr_alter_billing_info', 10, 2 );
	function msaddr_alter_billing_info( $value, $customer ) {
		if ( 'KR' == $customer->get_billing_country() ) {
			switch ( current_filter() ) {
				case 'woocommerce_customer_get_billing_first_name' :
					$value = get_user_meta( $customer->get_id(), 'billing_first_name_kr', true );
					break;
				case 'woocommerce_customer_get_billing_last_name' :
					$value = '';
					break;
				case 'woocommerce_customer_get_billing_email' :
					$value = get_user_meta( $customer->get_id(), 'billing_email_kr', true );
					break;
				case 'woocommerce_customer_get_billing_phone' :
					$value = get_user_meta( $customer->get_id(), 'billing_phone_kr', true );
					break;
				case 'woocommerce_customer_get_billing_postcode' :
					$value = get_user_meta( $customer->get_id(), 'mshop_billing_address-postnum', true );
					break;
				case 'woocommerce_customer_get_billing_address_1' :
					$value = get_user_meta( $customer->get_id(), 'mshop_billing_address-addr1', true );
					break;
				case 'woocommerce_customer_get_billing_address_2' :
					$value = get_user_meta( $customer->get_id(), 'mshop_billing_address-addr2', true );
					break;
			}

		}

		return $value;
	}

	add_filter( 'msaddr_field_is_enabled', array( 'MSADDR_Checkout_Field_Controller', 'filter_address_field' ), 10, 3 );

	add_filter( 'woocommerce_address_to_edit', array( 'MSADDR_Myaccount', 'maybe_populate_subscription_addresses' ), 99 );
}

function msaddr_get_shipping_phone( $order ) {
	return is_callable( array( $order, 'get_shipping_phone' ) ) ? $order->get_shipping_phone() : $order->get_meta( '_shipping_phone' );
}