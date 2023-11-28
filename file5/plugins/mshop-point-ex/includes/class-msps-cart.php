<?php
class MSPS_Cart {
	public static function woocommerce_after_add_to_cart_form() {
		global $post;

		if ( ! apply_filters( 'msps_price_notice', true, 'cart' ) ) {
			return;
		}

		$product_id = apply_filters( 'wpml_object_id', $post->ID, 'product', true, mshop_wpml_get_default_language() );
		$product    = wc_get_product( $product_id );
		if ( ! $product || is_archive() ) {
			return;
		}

		$price = ( 'variable' == $product->get_type() ) ? $product->get_variation_price( 'max' ) : $product->get_price();

		$apply_point = apply_filters( 'msp_apply_point', $product->is_purchasable() && $price > 0, $product );

		if ( $apply_point ) {
			mshop_point_print_notice( '', true );

			$language_args = mshop_wpml_get_current_language_args();
			if ( ! empty( $language_args ) ) {
				$language_args = '?' . $language_args;
			}

			$rule_infos = MSPS_Manager::get_ruleset_for_product( $product, 1, mshop_point_get_user_role() );

			$additional_message = '';
			$additional_info    = apply_filters( 'mshop_point_translate_string', get_option( 'mshop_point_system_guide_notice_info_at_product_detail' ), 'guide_notice_info_at_product_detail' );
			if ( ! empty( $additional_info ) ) {
				$infos              = explode( "\n", $additional_info );
				$infos              = array_filter( $infos );
				$additional_message = '<li>' . implode( '</li><li>', $infos ) . '</li>';
			}

			wp_enqueue_script( 'mshop-point-add-to-cart', MSPS()->plugin_url() . '/assets/js/mshop-point-add-to-cart.js', array( 'jquery', 'jquery-ui-core' ) );

			$product_id = $product->get_id();
			if ( in_array( $product->get_type(), array( 'subscription' ) ) && floatval( WC_Subscriptions_Product::get_sign_up_fee( $product ) ) > 0 ) {
				$regular_price = floatval( $product->get_regular_price() ) + floatval( WC_Subscriptions_Product::get_sign_up_fee( $product ) );
				if ( $product->get_sale_price() > 0 ) {
					$sale_price = floatval( $product->get_sale_price() ) + floatval( WC_Subscriptions_Product::get_sign_up_fee( $product ) );
				} else {
					$sale_price = floatval( $product->get_regular_price() ) + floatval( WC_Subscriptions_Product::get_sign_up_fee( $product ) );
				}
			} else {
				$regular_price = $product->get_regular_price();
				$sale_price    = $product->get_sale_price();
			}

			wp_localize_script( 'mshop-point-add-to-cart', '_mshop_point_add_to_cart', array(
				'ajaxurl'                       => admin_url( 'admin-ajax.php' . $language_args ),
				'product_id'                    => $product_id,
				'regular_price'                 => apply_filters( 'msps_regular_price', $regular_price, $product ),
				'sale_price'                    => apply_filters( 'msps_sale_price', $sale_price, $product ),
				'currency_symbol'               => get_woocommerce_currency_symbol(),
				'currency_pos'                  => get_option( 'woocommerce_currency_pos', 'left' ),
				'thousand_separator'            => wc_get_price_thousand_separator(),
				'decimals'                      => wc_get_price_decimals(),
				'exchange_ratio'                => MSPS_Manager::point_exchange_ratio(),
				'rule_infos'                    => $rule_infos,
				'point_message'                 => apply_filters( 'mshop_point_translate_string', get_option( 'mshop_point_system_notice_at_product_detail', __( '상품 구매시 {point} 포인트가 적립됩니다.', 'mshop-point-ex' ) ), 'point_message' ),
				'point_guide_message_title'     => apply_filters( 'mshop_point_translate_string', get_option( 'mshop_point_system_guide_notice_title_at_product_detail', __( '[ 포인트 적립안내 ]', 'mshop-point-ex' ) ), 'point_guide_message_title' ),
				'additional_message'            => $additional_message,
				'point_guide_message_price_qty' => apply_filters( 'mshop_point_translate_string', get_option( 'mshop_point_system_notice_at_product_detail_price_qty', __( '{desc} 상품 {amount} 이상 또는 {qty}개 이상 구매시 {point} 포인트가 적립됩니다.', 'mshop-point-ex' ) ), 'point_guide_message_price_qty' ),
				'point_guide_message_price'     => apply_filters( 'mshop_point_translate_string', get_option( 'mshop_point_system_notice_at_product_detail_price', __( '{desc} 상품 {amount} 이상 구매시 {point} 포인트가 적립됩니다.', 'mshop-point-ex' ) ), 'point_guide_message_price' ),
				'point_guide_message_qty'       => apply_filters( 'mshop_point_translate_string', get_option( 'mshop_point_system_notice_at_product_detail_qty', __( '{desc} 상품 {qty}개 이상 구매시 {point} 포인트가 적립됩니다.', 'mshop-point-ex' ) ), 'point_guide_message_qty' ),
				'point_message_for_quest'       => is_user_logged_in() ? '' : get_option( 'mshop_point_system_notice_for_quest', __( '회원 가입 후 포인트 적립 혜택을 누리세요.', 'mshop-point-ex' ) ),
				'additional_earn_message'       => __( '(추가적립) ', 'mshop-point-ex' ),
				'show_guide_message'            => MSPS_Manager::use_print_notice( 'product_guide' ),
				'sold_individually'             => $product->is_sold_individually(),
				'multiplying_product_qty'       => 'yes' == get_option( 'msps_apply_filxed_point_by_multiplying_product_qty', 'no' ),
			) );

			wp_enqueue_style( 'mshop-point', MSPS()->plugin_url() . '/assets/css/frontend.css', array(), MSPS_VERSION );
			echo '<input type="hidden" class="mshop-quantity" value="1">';
		}
	}
	public static function woocommerce_after_cart_totals() {
		$coupons = array_diff( WC()->cart->get_applied_coupons(), array( 'msms_discount', 'msms_recurring_discount' ) );

		if ( ! MSPS_Manager::allow_coupon() && count( $coupons ) > 0 ) {
			wp_enqueue_style( 'mshop-point', MSPS()->plugin_url() . '/assets/css/frontend.css', array(), MSPS_VERSION );
			mshop_point_print_notice( __( '쿠폰을 사용하여 포인트가 적립되지 않습니다', 'mshop-point-ex' ) );

			return;
		}

		$message = MSPS_Manager::show_message_for_cart( WC()->cart );
		if ( ! empty( $message ) ) {
			wp_enqueue_style( 'mshop-point', MSPS()->plugin_url() . '/assets/css/frontend.css', array(), MSPS_VERSION );
			mshop_point_print_notice( $message );
		}
	}
	public static function woocommerce_available_variation( $available_variation, $product, $variation ) {
		if ( 'subscription_variation' == $variation->get_type() ) {
			$available_variation['sign_up_fee'] = WC_Subscriptions_Product::get_sign_up_fee( $variation );
		}

		return $available_variation;
	}

	public static function woocommerce_subscriptions_calculated_total( $total ) {
		if ( property_exists( WC()->cart, 'recurring_carts' ) ) {
			foreach ( WC()->cart->recurring_carts as $key => &$cart ) {
				$sub_total = 0;
				$sub_tax   = 0;
				$fees = $cart->get_fees();
				foreach ( $fees as $fee_key => $fee ) {
					if ( in_array( $fee->name, array( __( '포인트 할인', 'mshop-point-ex' ), __( '포인트 할인 (비과세)', 'mshop-point-ex' ), __( '포인트 할인 (과세)', 'mshop-point-ex' ) ) ) ) {
						$sub_total += $fee->total;
						$sub_tax   += $fee->tax;
						unset( $fees [ $fee_key ] );

						if ( wc_tax_enabled() ) {
							$fee_taxes = $cart->get_fee_taxes();
							$fee_taxes = array_diff_key( $fee_taxes, $fee->tax_data );
							$cart->set_fee_taxes( $fee_taxes );
						}
					}
				}
				$cart->fees_api()->set_fees( $fees );
				$totals = $cart->get_totals();

				$totals['total']     -= ( $sub_total + $sub_tax );
				$totals['fee_total'] -= $sub_total;

				if ( wc_tax_enabled() ) {
					$totals['total_tax'] -= $sub_tax;
					$totals['fee_tax']   -= $sub_tax;
				}
				$cart->set_totals( $totals );
			}
		}

		return $total;
	}

	static function print_notice_to_archive() {
		global $product;

        if ( is_user_logged_in() && ! is_product() ) {
			$expected_point = MSPS_Manager::get_expected_point( $product, 1, mshop_point_get_user_role() );

			if ( $expected_point > 0 ) {
                wp_enqueue_style( 'mshop-point', MSPS()->plugin_url() . '/assets/css/frontend.css', array(), MSPS_VERSION );

				$message = str_replace( '{point}', number_format( $expected_point, wc_get_price_decimals() ), get_option( 'msps_print_notice_archive_message', '상품 구매시 {point} 포인트가 적립됩니다.' ) );

				echo sprintf( "<div class='msps-point-notice'>%s</div>", $message );
			}
		}
	}
}
