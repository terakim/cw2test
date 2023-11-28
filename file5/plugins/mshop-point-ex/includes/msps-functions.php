<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
function msps_get( $array, $key, $default = '' ) {
	return ! empty( $array[ $key ] ) ? $array[ $key ] : $default;
}
function msps_ajax_command( $command ) {
	return MSPS_AJAX_PREFIX . '_' . $command;
}
function msps_get_user_roles() {
	$results = array();

	$roles = wp_roles()->roles;;

	$filters = get_option( 'mshop_point_system_role_filter' );

	foreach ( $filters as $role ) {
		if ( 'yes' === $role['enabled'] && array_key_exists( $role['role'], $roles ) ) {
			$results[ $role['role'] ] = ! empty( $role['nickname'] ) ? $role['nickname'] : $role['name'];
		}
	}

	return $results;
}
function msps_get_product_id( $product ) {
	if ( is_numeric( $product ) ) {
		$product = wc_get_product( $product );
	}

	if ( $product ) {
		return $product->get_parent_id() > 0 ? $product->get_parent_id() : $product->get_id();
	}

	return 0;
}
function mshop_get_point_rule( $the_point_rule = false, $args = array() ) {
	return MSPS()->point_rule_factory->get_point_rule( $the_point_rule, $args );
}
function mshop_point_print_notice( $message, $hidden = false ) {
	wc_get_template( 'notice/product_notice.php', array( 'message' => $message, 'hidden' => $hidden ), '', MSPS()->template_path() );
}
function mshop_point_print_post_notice( $message ) {
	wc_get_template( 'notice/post_notice.php', array( 'message' => $message ), '', MSPS()->template_path() );
}
function mshop_point_get_user_role( $user_id = null ) {
	$role = 'guest';

	if ( $user_id === null && is_user_logged_in() ) {
		$user_id = wp_get_current_user();
	}

	if ( is_numeric( $user_id ) ) {
		$user       = new WP_User( $user_id );
		$user_roles = $user->roles;
	} else if ( $user_id instanceof WP_User ) {
		$user_roles = $user_id->roles;
	}

	if ( ! empty( $user_roles ) ) {
		$matched_user_roles = array_intersect( $user_roles, array_keys( msps_get_user_roles() ) );

		$role = array_shift( $matched_user_roles );
	}

	return apply_filters( 'msps_get_user_role', $role, $user_id );
}

function mshop_point_get_user_role_name( $user_role = null ) {
	if ( is_null( $user_role ) ) {
		$user_role = mshop_point_get_user_role();
	}

	$roles = get_option( 'mshop_point_system_role_filter', array() );

	foreach ( $roles as $role ) {
		if ( 'yes' === $role['enabled'] && $role['role'] == $user_role ) {
			return ! empty( $role['nickname'] ) ? $role['nickname'] : $role['name'];
		}
	}

	return '';
}
function msps_wcs_renewal_order_items( $items, $new_order, $subscription ) {
	$items = array_filter( $items,
		function ( $item ) {
			return $item['type'] != 'fee' || $item['name'] != __( '포인트 할인', 'mshop-point-ex' );
		}
	);

	return $items;
}
function msps_wcs_resubscribe_order_items( $items, $new_order, $subscription ) {
	$items = array_filter( $items,
		function ( $item ) {
			return $item['type'] != 'fee' || $item['name'] != __( '포인트 할인', 'mshop-point-ex' );
		}
	);

	return $items;
}
function msps_wcs_new_order_created( $new_order, $subscription, $type ) {
	$new_order = wc_get_order( $new_order->get_id() );

	$new_order->calculate_totals( true );

	return $new_order;
}

function msps_get_wallet_id( $item_type, $object, $language = '' ) {
	if ( $object instanceof MSPS_User ) {
		$item_type = $object->get_wallet_id( $item_type );
	} else if ( $object instanceof WC_Order ) {
		$wpml_language = $object->get_meta( 'wpml_language' );
		if ( ! empty( $wpml_language ) ) {
			$item_type .= '_' . $wpml_language;
		}
	} else if ( ! empty( $language ) ) {
		$item_type .= '_' . $language;
	}

	return apply_filters( 'msps_get_wallet_id', $item_type, $object, $language );
}
function msps_get_wallet_name( $user, $wallet_id ) {
	$wallets = $user->wallet->load_wallet_items( array( $wallet_id ), false );

	if ( ! empty( $wallets ) ) {
		$wallet = current( $wallets );

		return $wallet->get_name();
	}

	return $wallet_id;
}

function msps_get_volatile_wallet_name( $volatile_wallet ) {
	return $volatile_wallet['name'];
}
function msps_volatile_wallet_is_valid( $volatile_wallet ) {
	return 'yes' == $volatile_wallet['enabled'] && ( empty( $volatile_wallet['valid_until'] ) || $volatile_wallet['valid_until'] >= date( 'Y-m-d', strtotime( current_time( 'mysql' ) ) ) );
}

if ( ! MSPS_Install::needs_db_update() && MSPS_Manager::enabled() ) {
	add_action( 'woocommerce_checkout_order_processed', array( 'MSPS_Checkout', 'validate_must_purchase_by_point' ), 10, 2 );
	add_filter( 'wcs_renewal_order_items', 'msps_wcs_renewal_order_items', 10, 3 );
	add_filter( 'wcs_renewal_order_items', 'msps_wcs_resubscribe_order_items', 10, 3 );
	add_filter( 'wcs_new_order_created', 'msps_wcs_new_order_created', 10, 3 );
	if ( MSPS_Manager::is_purchase_method_checkout_point() ) {
		add_action( 'pafw_dc_before_discount_form', array( 'MSPS_PAFW_DIY_Checkout', 'point_form' ) );
		add_action( 'woocommerce_review_order_before_order_total', 'MSPS_Checkout::woocommerce_review_order_after_shipping' );
		add_action( 'woocommerce_checkout_order_processed', 'MSPS_Checkout::woocommerce_checkout_order_processed', 100, 2 );
		add_filter( 'woocommerce_subscriptions_calculated_total', 'MSPS_Cart::woocommerce_subscriptions_calculated_total' );
	}

	if ( MSPS_Manager::is_purchase_method_payment_gateway() ) {
		add_filter( 'woocommerce_payment_gateways', 'MShop_Point::woocommerce_payment_gateways' );
	}
	if ( MSPS_Manager::use_user_register_rule() ) {
		add_action( 'woocommerce_register_form', 'MSPS_Myaccount::woocommerce_register_form' );
		add_action( 'user_register', 'MSPS_Myaccount::user_register' );
	}
	add_filter( 'woocommerce_admin_order_totals_after_discount', 'MSPS_Order::woocommerce_admin_order_totals_after_discount' );
	add_filter( 'woocommerce_get_order_item_totals', 'MSPS_Order::woocommerce_get_order_item_totals', 10, 2 );

	add_action( 'woocommerce_cart_calculate_fees', 'MSPS_Checkout::woocommerce_cart_calculate_fees', 100 );
	add_action( 'woocommerce_cart_totals_get_fees_from_cart_taxes', 'MSPS_Checkout::woocommerce_cart_totals_get_fees_from_cart_taxes', 10, 3 );

	if ( MSPS_Manager::use_purchase_point_rule() ) {

		if ( MSPS_Manager::use_print_notice( 'product' ) ) {
			add_action( get_option( 'msps_notice_product_hook', 'woocommerce_after_add_to_cart_form' ), array( 'MSPS_Cart', 'woocommerce_after_add_to_cart_form' ) );
			add_action( 'woocommerce_before_cart_table', 'MSPS_Cart::woocommerce_after_cart_totals' );
			add_action( 'woocommerce_before_checkout_form', 'MSPS_Checkout::woocommerce_before_checkout_form' );
			add_action( 'msps_point_notice', 'MSPS_Checkout::woocommerce_before_checkout_form' );
		}

		if ( MSPS_Manager::use_print_notice( 'archive' ) ) {
			add_action( 'woocommerce_after_shop_loop_item_title', array( 'MSPS_Cart', 'print_notice_to_archive' ), 15 );
		}

		add_filter( 'woocommerce_checkout_cart_item_quantity', 'MSPS_Checkout::woocommerce_checkout_cart_item_quantity', 10, 3 );
		add_filter( 'woocommerce_product_data_tabs', 'MSPS_Admin_Meta_Box_Product_Point::woocommerce_product_data_tabs' );
		add_action( 'woocommerce_product_data_panels', 'MSPS_Admin_Meta_Box_Product_Point::woocommerce_product_data_panels' );
		add_action( 'wp_ajax_mshop_point_update_product_settings', 'MSPS_Admin_Meta_Box_Product_Point::mshop_point_update_product_settings' );

		add_filter( 'wcs_renewal_order_created', 'MSPS_Order::calculate_point_for_renewal_order', 20, 2 );
		add_filter( 'woocommerce_available_variation', 'MSPS_Cart::woocommerce_available_variation', 10, 3 );

		add_action( 'woocommerce_order_status_changed', array( 'MSPS_Order', 'process_point' ), 100, 3 );
		add_action( 'woocommerce_checkout_order_processed', array( 'MSPS_Order', 'woocommerce_checkout_order_processed' ), 100, 2 );

		add_action( 'msps_point_option', array( 'MSPS_SMS_Point', 'maybe_apply_sms_point_option' ), 10, 3 );

		add_action( 'woocommerce_order_refunded', array( 'MSPS_Order', 'maybe_deduct_point_for_partial_refund' ), 10, 2 );
		add_filter( 'msgift_calculate_total_of_matching_to_rule', function ( $item_total, $rule, $cart ) {
			if ( property_exists( $cart, 'mshop_point' ) && floatval( $cart->mshop_point ) > 0 ) {
				$used_point = $cart->mshop_point;

				$applied_point = wc_cart_round_discount( $used_point * ( $item_total / $cart->get_cart_contents_total() ), wc_get_price_decimals() );

				$item_total -= $applied_point;
			}

			return $item_total;
		}, 10, 3 );
	}

	add_action( 'woocommerce_order_status_changed', 'MSPS_Order::process_redeposit', 90, 3 );

	if ( MSPS_Post_Manager::use_post_point_rule() ) {
		add_action( 'wp_insert_comment', 'MSPS_Comment::wp_insert_comment', 100, 2 );
		add_action( 'wp_set_comment_status', 'MSPS_Comment::wp_set_comment_status', 100, 2 );
		add_action( 'comment_form', 'MSPS_Post_Manager::comment_form', 100, 2 );

		add_action( 'transition_post_status', 'MSPS_Post::transition_post_status', 100, 3 );
	}

	if ( defined( 'DOING_AJAX' ) ) {
		add_filter( 'wcml_load_multi_currency', 'mshop_point_wcml_load_multi_currency' );

		function mshop_point_wcml_load_multi_currency( $flag ) {
			return true;
		}
	}
	add_action( 'delete_user', 'msps_reset_user_point', 10, 2 );
	function msps_reset_user_point( $user_id, $reassign ) {
		$active_languages = mshop_wpml_get_active_languages();

		if ( empty( $active_languages ) ) {
			$user = new MSPS_User( $user_id );

			if ( $user ) {
				$user->reset_user_point();
			}
		} else {
			foreach ( $active_languages as $language => $data ) {
				$user = new MSPS_User( $user_id, $language );

				if ( $user ) {
					$user->reset_user_point( $language );
				}
			}
		}
	}

	add_action( 'msps_point_extinction', array( 'MSPS_Extinction', 'run' ) );
	add_action( 'msps_point_extinction_notification', array( 'MSPS_Extinction_Notification', 'run' ), 10, 2 );
	function msps_set_point_discount_info( $discount_info, $cart ) {
		foreach ( $cart->get_fees() as $fee_key => $fee ) {
			if ( in_array( $fee->name, array( __( '포인트 할인', 'mshop-point-ex' ), __( '포인트 할인 (비과세)', 'mshop-point-ex' ), __( '포인트 할인 (과세)', 'mshop-point-ex' ) ) ) ) {
				$discount_info[] = array(
					'label'         => $fee->name,
					'amount'        => abs( $fee->total + $fee->tax ),
					'free_shipping' => false,
					'type'          => 'point',
					'code'          => $fee_key,
					'object'        => $fee
				);
			}
		}

		return $discount_info;
	}

	add_filter( 'pafw_dc_get_discount_info', 'msps_set_point_discount_info', 10, 2 );
}