<?php

if ( ! class_exists( 'MSIV_Cart' ) ) {

	class MSIV_Cart {

		public function __construct() {
			add_action( 'init', array( $this, 'init' ) );

			add_filter( 'msiv_get_iv_shipping_fee', array( $this, 'get_iv_shipping_fee' ), 10, 2 );
			add_filter( 'pafw_dc_get_shipping_info', array( $this, 'add_iv_shipping_fee' ), 10, 2 );
		}

		public function init() {
			$iv_settings = get_option( 'woocommerce_korea_zone_shipping_settings' );
			if ( is_array( $iv_settings ) && isset( $iv_settings['enabled'] ) && $iv_settings['enabled'] == 'yes' ) {
				add_action( 'woocommerce_cart_calculate_fees', array( &$this, 'woocommerce_cart_calculate_fees' ) );
				add_action( 'woocommerce_checkout_update_order_meta', array( &$this, 'checkout_update_post_meta' ), 10, 2 );
			}
		}

		public function get_postcode() {
			$postcode = '';
			$fields   = $_REQUEST;

			if ( ! empty( $_POST['post_data'] ) ) {
				parse_str( $_POST['post_data'], $fields );
			}

			if ( ! empty( $fields['ship_to_different_address'] ) && '1' == $fields['ship_to_different_address'] && ! empty( $fields['mshop_shipping_address-postnum'] ) ) {
				$postcode = $fields['mshop_shipping_address-postnum'];
			} else if ( ! empty( $fields['mshop_billing_address-postnum'] ) ) {
				$postcode = $fields['mshop_billing_address-postnum'];
			} else if ( ! empty( $fields['ship_to_different_address'] ) && '1' == $fields['ship_to_different_address'] && ! empty( $fields['shipping_postcode'] ) ) {
				$postcode = $fields['shipping_postcode'];
			} else if ( ! empty( $fields['billing_postcode'] ) ) {
				$postcode = $fields['billing_postcode'];
			} else {
				$postcode = ! empty( $fields['postcode'] ) ? $fields['postcode'] : '';
			}

			return apply_filters( 'msiv_get_postcode', $postcode );
		}

		function load_settings() {
			$settings = get_option( 'woocommerce_korea_zone_shipping_settings' );

			return $settings;
		}
		public function woocommerce_cart_calculate_fees( $cart ) {
			$fee = apply_filters( 'msiv_get_iv_shipping_fee', 0, $cart );

			if ( $fee > 0 ) {
				$cart->add_fee( MSIV_Manager::get_title(), $fee, MSIV_Manager::calc_taxes(), MSIV_Manager::get_tax_class() );
			}
		}
		public function checkout_update_post_meta( $order_id, $data ) {
			$order = wc_get_order( $order_id );

			foreach ( WC()->cart->get_fees() as $fee ) {
				if ( MSIV_Manager::get_title() == $fee->name ) {
					$order->update_meta_data( '_order_shipping_iv', $fee->amount );
					$order->save_meta_data();
					break;
				}
			}
		}
		public function get_iv_shipping_fee( $fee, $cart ) {
			$country = 'KR';

			if ( ! empty( $_REQUEST['country'] ) ) {
				$country = $_REQUEST['country'];
			} else if ( ! empty( $_REQUEST['shipping_country'] ) ) {
				$country = $_REQUEST['shipping_country'];
			} else if ( ! empty( $_REQUEST['billing_country'] ) ) {
				$country = $_REQUEST['billing_country'];
			}

			if ( 'yes' == MSIV_Manager::apply_iv_fee_one_time_shipping( 'free_shipping' ) ) {
				add_filter( 'woocommerce_cart_contains_subscriptions_needing_shipping', '__return_true' );
				add_filter( 'woocommerce_subscriptions_product_needs_one_time_shipping', '__return_false' );
			}

			if ( 'KR' == $country && $cart->needs_shipping() ) {
				$postcode = $this->get_postcode();

				$fee = MSIV_Manager::calculate_fees( $cart, $postcode );
			}

			if ( 'yes' == MSIV_Manager::apply_iv_fee_one_time_shipping( 'free_shipping' ) ) {
				remove_filter( 'woocommerce_cart_contains_subscriptions_needing_shipping', '__return_true' );
				remove_filter( 'woocommerce_subscriptions_product_needs_one_time_shipping', '__return_false' );
			}

            if ( 'yes' == MSIV_Manager::apply_iv_fee_free_coupon( 'free_shipping' ) ) {
                $coupon = $cart->get_coupons();
                if( ! empty( $coupon ) ) {
                    $coupon = array_shift( $coupon );
                    if ( $coupon->get_data()['free_shipping'] ) {
                        $fee = 0;
                    }
                }
            }

            return apply_filters( 'mshop_iv_shipping_fee', $fee, $cart);
		}
		public function add_iv_shipping_fee( $shipping_info, $cart ) {
			$fee = apply_filters( 'msiv_get_iv_shipping_fee', 0, $cart );

			if ( $fee > 0 ) {
				if ( MSIV_Manager::calc_taxes() ) {
					$taxes = WC_Tax::calc_tax( $fee, WC_Tax::get_rates( MSIV_Manager::get_tax_class() ) );

					$fee = wc_round_tax_total( $fee + array_sum( $taxes ), wc_get_price_decimals() );
				}

				$shipping_info[] = array(
					'type'      => 'mshop_iv',
					'label'     => MSIV_Manager::get_title(),
					'fee'       => $fee,
					'fee_label' => wc_price( $fee )
				);
			}

			return $shipping_info;
		}
	}

	new MSIV_Cart();
}

