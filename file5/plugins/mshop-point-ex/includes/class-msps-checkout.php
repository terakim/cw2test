<?php
class MSPS_Checkout {
	public static function woocommerce_before_checkout_form() {
		$coupons = array_diff( WC()->cart->get_applied_coupons(), array( 'msms_discount', 'msms_recurring_discount' ) );

		if ( ! MSPS_Manager::allow_coupon() && count( $coupons ) > 0 ) {
			wp_enqueue_style( 'mshop-point', MSPS()->plugin_url() . '/assets/css/frontend.css', array(), MSPS_VERSION );
			mshop_point_print_notice( __( '쿠폰을 사용하여 포인트가 적립되지 않습니다', 'mshop-point-ex' ) );

			return;
		}

		wp_enqueue_style( 'mshop-point', MSPS()->plugin_url() . '/assets/css/frontend.css', array(), MSPS_VERSION );

		$message = MSPS_Manager::show_message_for_checkout( WC()->cart );
		if ( ! empty( $message ) ) {
			mshop_point_print_notice( $message );
		}
	}
	public static function woocommerce_checkout_cart_item_quantity( $title, $cart_item, $cart_item_key ) {
		$product = new MSPS_Product( $cart_item['data'] );

		if ( ! $product->is_point_purchasable() ) {
			$title .= '<br><span style="margin-left: 5px; font-size: 0.9em; color: blue;">' . __( '( 포인트 구매불가 상품 )', 'mshop-point-ex' ) . '</span>';
		}

		if ( ! $product->must_purchase_by_point() ) {
			$title .= '<br><span style="margin-left: 5px; font-size: 0.9em; color: #ff8f20;">' . __( '( 포인트로만 구매하실 수 있습니다. )', 'mshop-point-ex' ) . '</span>';
		}

		return $title;
	}
	public static function woocommerce_review_order_after_shipping() {
		if ( ! is_user_logged_in() || ! MSPS_Manager::is_valid_user( mshop_point_get_user_role() ) || ! apply_filters( 'msps_process_calculate_point', true, null ) ) {
			return;
		}

		$max_useable_amount      = MSPS_Manager::max_useable_amount( WC()->cart );
		$max_useable_point       = MSPS_Manager::max_useable_point( WC()->cart );
		$purchase_minimum_point  = MSPS_Manager::purchase_minimum_point();
		$purchase_minimum_amount = MSPS_Manager::purchase_minimum_amount();
		$point_exchange_ratio    = MSPS_Manager::point_exchange_ratio();
		$used_point              = isset( WC()->cart->mshop_point ) ? WC()->cart->mshop_point : 0;

		$user       = new MSPS_User( get_current_user_id() );
		$user_point = $user->get_point();

		$coupons = array_diff( WC()->cart->get_applied_coupons(), array( 'msms_discount', 'msms_recurring_discount' ) );

		if ( 'yes' == get_option( 'msps_cannot_use_point_with_coupons', 'no' ) && ! empty( $coupons ) ) {
			wc_get_template( 'checkout/cannot_use_point_with_coupons.php', array( 'user_point' => $user_point ), '', MSPS()->template_path() );
		} else if ( 0 == $used_point && $max_useable_amount <= 0 ) {
			wc_get_template( 'checkout/no-purchaseable-product.php', array( 'user_point' => $user_point ), '', MSPS()->template_path() );
		} else if ( $max_useable_amount < $purchase_minimum_amount ) {
			wc_get_template( 'checkout/minimum-purchase-amount.php', array(
				'user_point'              => $user_point,
				'purchase_minimum_amount' => $purchase_minimum_amount
			), '', MSPS()->template_path() );
		} else if ( $user->get_point() == 0 || $user->get_point() < $purchase_minimum_point ) {
			wc_get_template( 'checkout/shortage-point.php', array(
				'user_point'             => $user_point,
				'purchase_minimum_point' => $purchase_minimum_point
			), '', MSPS()->template_path() );
		} else {
			$update_timeout = get_option( 'msps_update_timeout', 1000 );
			$update_timeout = intval( $update_timeout );
			if ( $update_timeout > 1000 ) {
				wp_enqueue_script( 'msps_checkout', MSPS()->plugin_url() . '/assets/js/msps-checkout.js', array( 'jquery' ), MSPS()->version );
				wp_localize_script( 'msps_checkout', 'msps_checkout_params', array( 'update_timeout' => get_option( 'msps_update_timeout' ) ) );
			}

			wc_get_template( 'checkout/form-use-point-' . get_option( 'msps_form_use_point_template', 'type-a' ) . '.php', array(
				'used_point'           => $used_point,
				'user_point'           => $user_point,
				'max_useable_point'    => $used_point * MSPS_Manager::purchase_maximum_ratio() / 100 + $max_useable_point,
				'point_exchange_ratio' => $point_exchange_ratio,
				'update_by_wc'         => $update_timeout <= 1000,
			), '', MSPS()->template_path() );
		}

		wc_get_template( 'checkout/used-point.php', array(
			'used_point'           => WC()->cart->mshop_point,
			'point_exchange_ratio' => $point_exchange_ratio
		), '', MSPS()->template_path() );

	}
	private static function get_point_param() {
		$point = 0;

		if ( ! empty( $_POST['post_data'] ) ) {
			parse_str( $_POST['post_data'], $params );

			if ( isset( $params['mshop_point'] ) && '' != $params['mshop_point'] ) {
				$point = floatval( $params['mshop_point'] );
			} else if ( isset( $params['_mshop_point'] ) ) {
				$point = floatval( $params['_mshop_point'] );
			}
		} else if ( isset( $_POST['_mshop_point'] ) ) {
			$point = floatval( $_POST['_mshop_point'] );
		}

		return $point;
	}
	public static function get_used_point_from_order( $order ) {
		$point = 0;
		foreach ( $order->get_items( 'fee' ) as $item_id => $item ) {
			if ( in_array( $item['name'], array( __( '포인트 할인', 'mshop-point-ex' ), __( '포인트 할인 (비과세)', 'mshop-point-ex' ), __( '포인트 할인 (과세)', 'mshop-point-ex' ) ) ) ) {
				if ( wc_prices_include_tax() ) {
					$point += abs( round( $item['line_total'] + $item['line_tax'], wc_get_price_decimals() ) );
				} else {
					$point += abs( $item['line_total'] );
				}
			}
		}

		return $point;
	}
	public static function woocommerce_checkout_order_processed( $order_id, $post ) {
		$current_language = apply_filters( 'msps_get_current_language', mshop_wpml_get_current_language() );

		$point      = self::get_point_param();
		$order      = wc_get_order( $order_id );
		$user       = new MSPS_User( $order->get_user_id() );
		$prev_point = $user->get_point();

		$used_point = self::get_used_point_from_order( $order );

		if ( $point * MSPS_Manager::point_exchange_ratio() != $used_point || ( $point > 0 && $user->get_point() < $point ) ) {
			throw new Exception( __( '보유하신 포인트가 부족합니다. 페이지를 새로고침 하신 후 다시 시도해주세요.', 'mshop-point-ex' ) );
		}

		if ( $point != 0 && 'yes' != $order->get_meta( '_mshop_point_purchase_processed' ) ) {

			$deduction_info = $user->wallet->get_deduction_info( $point );
			MSPS_Order::update_used_point( $order, $order->get_user_id(), $deduction_info );
			$user->wallet->deduct( $deduction_info );

			$remain_point = $user->get_point();
			$current_balance = $prev_point;
			foreach ( $deduction_info as $item_id => $amount ) {
				$current_balance -= $amount;
				MSPS_Log::add_log( $order->get_user_id(), msps_get_wallet_id( $item_id, null, $current_language ), 'deduct', 'purchase', -1 * $amount, $current_balance, 'completed', $order_id, '', msps_get_wallet_name( $user, $item_id ) );
			}

			$message = sprintf( __( '주문(<a href="%1$s">#%2$s</a>) 결제시 포인트 사용으로 %3$s포인트가 차감되었습니다.<br>보유포인트가 %4$s포인트에서 %5$s포인트로 변경되었습니다.', 'mshop-point-ex' ), get_edit_post_link( $order_id ), $order_id, number_format( floatval( $point ), wc_get_price_decimals() ), number_format( $prev_point, wc_get_price_decimals() ), number_format( $remain_point, wc_get_price_decimals() ) );
			$order->add_order_note( __( '[포인트 알림] ', 'mshop-point-ex' ) . $message );
		}
	}

	public static function adjust_tax_amount( $tax_amount ) {
		if ( wc_prices_include_tax() ) {
			$tax_rates  = WC_Tax::get_rates();
			$fee_taxes  = WC_Tax::calc_inclusive_tax( $tax_amount, $tax_rates );
			$tax_amount -= array_sum( $fee_taxes );
		}

		return $tax_amount;
	}
	public static function woocommerce_cart_calculate_fees( $cart ) {
		if ( is_checkout() ) {
			$current_language = apply_filters( 'msps_get_current_language', mshop_wpml_get_current_language() );

			$user = new MSPS_User( get_current_user_id() );

			//세션에서 주문정보를 가져옴
			$order_id = absint( WC()->session->order_awaiting_payment );

			// 기존 주문이 있으면 포인트 확인후, 재적립 처리
			if ( $order_id > 0 && ( $order = wc_get_order( $order_id ) ) && $order->has_status( array( 'pending', 'failed' ) ) ) {
				$order = wc_get_order( $order_id );

				if ( MSPS_Order::is_order_with_point( $order, get_current_user_id() ) ) {

					$deduction_info = MSPS_Order::get_deduction_info( $order );
					$used_point     = MSPS_Order::get_used_point( $order );

					if ( floatval( $used_point ) > 0 && MSPS_Order::update_order( $order, $used_point ) ) {
						$current_balance = $user->get_point();

						$remain_point = $user->wallet->redeposit( $deduction_info );
						foreach ( $deduction_info as $item_id => $amount ) {
							$current_balance += $amount;
							MSPS_Log::add_log( $order->get_user_id(), msps_get_wallet_id( $item_id, null, $current_language ), 'earn', 'purchase', $amount, $current_balance, 'completed', $order_id, '', msps_get_wallet_name( $user, $item_id ) );
						}
						foreach ( $order->get_fees() as $item_id => $fee ) {
							if ( in_array( $fee->get_name(), array( __( '포인트 할인', 'mshop-point-ex' ), __( '포인트 할인 (비과세)', 'mshop-point-ex' ), __( '포인트 할인 (과세)', 'mshop-point-ex' ) ) ) ) {
								$order->remove_item( $item_id );
							}
						}
						$order->calculate_totals();
						$order->save();

						$message = sprintf( __( '주문(<a href="%1$s">#%2$s</a>) 결제시 포인트 사용금액 변경으로 사용된 %3$s포인트가 재적립 되었습니다.', 'mshop-point-ex' ), get_edit_post_link( $order_id ), $order_id, number_format( floatval( $used_point ), wc_get_price_decimals() ) );
						$order->add_order_note( __( '[포인트 알림] ', 'mshop-point-ex' ) . $message );

						MSPS_Order::update_used_point( $order, get_current_user_id(), 0 );
					}
				}
			}

			$point_exchange_ratio = MSPS_Manager::point_exchange_ratio();
			$max_useable_point    = MSPS_Manager::max_useable_point( WC()->cart );

			$user_point        = $user->get_point();
			$want_to_use_point = self::get_point_param();
			if ( $max_useable_point < $want_to_use_point ) {
				$want_to_use_point = $max_useable_point;
			}
			if ( $user_point < $want_to_use_point ) {
				$want_to_use_point = $user_point;
			}
			$point_unit = get_option( 'mshop_point_system_point_unit_number' );
			if ( ! empty( $point_unit ) ) {
				$want_to_use_point = intval( $want_to_use_point / $point_unit ) * $point_unit;
			}

			$coupons = array_diff( WC()->cart->get_applied_coupons(), array( 'msms_discount', 'msms_recurring_discount' ) );

			if ( $want_to_use_point > 0 && ( 'no' == get_option( 'msps_cannot_use_point_with_coupons', 'no' ) || empty( $coupons ) ) ) {
				$cart->mshop_point = $want_to_use_point;
				if ( wc_tax_enabled() && apply_filters( 'msps_apply_tax_calculation', true ) ) {
					$tax_amount    = 0;
					$no_tax_amount = 0;
					foreach ( $cart->cart_contents as $cart_content ) {
						if ( $cart_content['line_tax'] <= 0 ) {
							$no_tax_amount += $cart_content['line_total'];
						} else {
							if ( wc_prices_include_tax() ) {
								$tax_amount += $cart_content['line_total'] + $cart_content['line_tax'];
							} else {
								$tax_amount += $cart_content['line_total'];
							}
						}
					}

					if ( 'lowest' == get_option( 'mshop_point_system_apply_order_for_tax', 'lowest' ) ) {
						if ( $no_tax_amount > 0 && $no_tax_amount >= $want_to_use_point * $point_exchange_ratio ) {
							$cart->add_fee( __( '포인트 할인', 'mshop-point-ex' ), -1 * $want_to_use_point * $point_exchange_ratio );
						} else {
							$tax_amount = self::adjust_tax_amount( $want_to_use_point * $point_exchange_ratio - $no_tax_amount );
							if ( $no_tax_amount > 0 ) {
								$cart->add_fee( __( '포인트 할인 (비과세)', 'mshop-point-ex' ), -1 * $no_tax_amount );
							}
							if ( $tax_amount > 0 ) {
								$cart->add_fee( __( '포인트 할인 (과세)', 'mshop-point-ex' ), -1 * $tax_amount, true );
							}
						}
					} else {
						if ( $tax_amount >= $want_to_use_point * $point_exchange_ratio ) {
							$tax_amount = self::adjust_tax_amount( $want_to_use_point * $point_exchange_ratio );
							$cart->add_fee( __( '포인트 할인', 'mshop-point-ex' ), -1 * $tax_amount, true );
						} else {
							$no_tax_amount = $want_to_use_point * $point_exchange_ratio - $tax_amount;
							if ( $no_tax_amount > 0 ) {
								$cart->add_fee( __( '포인트 할인 (비과세)', 'mshop-point-ex' ), -1 * $no_tax_amount );
							}
							$tax_amount = self::adjust_tax_amount( $tax_amount );
							if ( $tax_amount > 0 ) {
								$cart->add_fee( __( '포인트 할인 (과세)', 'mshop-point-ex' ), -1 * $tax_amount, true );
							}
						}
					}

				} else {
					$cart->add_fee( __( '포인트 할인', 'mshop-point-ex' ), -1 * $want_to_use_point * $point_exchange_ratio );
				}

			} else {
				unset( $cart->mshop_point );
			}
		}
	}
	public static function woocommerce_cart_totals_get_fees_from_cart_taxes( $taxes, $fee, $cart ) {
		try {
			if ( $fee->object->name == __( '포인트 할인', 'mshop-point-ex' ) || $fee->object->name == __( '포인트 할인 (비과세)', 'mshop-point-ex' ) || $fee->object->name == __( '포인트 할인 (과세)', 'mshop-point-ex' ) ) {
				if ( ! $fee->taxable ) {
					$taxes = array();
				}
			}
		} catch ( Exception $e ) {

		}

		return $taxes;
	}
	public static function validate_must_purchase_by_point( $order_id, $post ) {
		$order = wc_get_order( $order_id );

        if ( "mshop-point" != $order->get_payment_method() ) {
            $used_point = MSPS_Checkout::get_used_point_from_order( $order );

            $have_to_use_point = 0;
            $product_name      = array ();

            foreach ( $order->get_items() as $item ) {
                $product = $item->get_product();

                if ( is_a( $product, 'WC_Product' ) && 'yes' == $product->get_meta( '_msps_must_purchase_by_point' ) ) {
                    $product_name[]    = sprintf( '"%s"', $item->get_name() );
                    $have_to_use_point += $item->get_total();
                }
            }

            if ( $have_to_use_point > 0 && $used_point < $have_to_use_point ) {
                $error = sprintf( __( '%s 상품은 포인트로만 구매하실 수 있습니다.', 'mshop-point-ex' ), implode( ',', $product_name ) );
                throw new Exception( $error );
            }
        }
	}

}