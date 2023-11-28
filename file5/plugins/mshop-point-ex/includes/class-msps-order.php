<?php
class MSPS_Order {

	private static $earn_point_statuses = null;

	private static function get_point_param() {
		$point = 0;

		if ( ! empty( $_POST['post_data'] ) ) {
			parse_str( $_POST['post_data'], $params );

			if ( isset( $params['mshop_point'] ) && '' != $params['mshop_point'] ) {
				$point = intval( $params['mshop_point'] );
			} else if ( isset( $params['_mshop_point'] ) ) {
				$point = $params['_mshop_point'];
			}
		} else if ( isset( $_POST['_mshop_point'] ) ) {
			$point = intval( $_POST['_mshop_point'] );
		}

		return $point;
	}
	public static function get_used_point( $order ) {
		$deduction_info = self::get_deduction_info( $order );

		return array_sum( $deduction_info );
	}
	public static function get_deduction_info( $order ) {

		$deduction_info = $order->get_meta( '_mshop_point' );
		if ( ! is_array( $deduction_info ) ) {
			$deduction_info = array( msps_get_wallet_id( 'free_point', $order ) => $deduction_info );
		}

		return $deduction_info;
	}
	public static function is_order_with_point( $order, $user_id ) {
		return 'yes' == $order->get_meta( '_mshop_point_purchase_processed' ) && $user_id == $order->get_meta( '_mshop_point_purchase_user_id' );
	}
	public static function set_earn_point( $order, $amount ) {
		if ( $amount > 0 ) {
			$order->update_meta_data( '_mshop_point_amount', $amount );
		} else {
			$order->delete_meta_data( '_mshop_point_amount' );
		}

		$order->save_meta_data();
	}
	public static function get_earn_point( $order ) {
		$earn_point = $order->get_meta( '_mshop_point_amount' );

		return is_null( $earn_point ) ? 0 : floatval( $earn_point );
	}
	public static function is_earn_processed( $order ) {
		return 'yes' == $order->get_meta( '_mshop_point_processed' );
	}
	public static function set_earn_processed( $order, $flag ) {
		$order->update_meta_data( '_mshop_point_processed', $flag ? 'yes' : 'no' );
		$order->save_meta_data();
	}
	public static function is_redeposit_processed( $order ) {
		return 'yes' == $order->get_meta( '_mshop_point_refunded' );
	}
	public static function set_redeposit_processed( $order, $flag ) {
		$order->update_meta_data( '_mshop_point_refunded', $flag ? 'yes' : 'no' );
		$order->save_meta_data();
	}
	public static function update_used_point( $order, $user_id, $point ) {
		if ( $point > 0 ) {
			$order->update_meta_data( '_mshop_point', $point );
			$order->update_meta_data( '_mshop_point_purchase_processed', 'yes' );
			$order->update_meta_data( '_mshop_point_purchase_user_id', $user_id );
			$order->update_meta_data( '_mshop_point_by_fee', 'yes' );
		} else {
			$order->delete_meta_data( '_mshop_point' );
			$order->delete_meta_data( '_mshop_point_purchase_processed' );
			$order->delete_meta_data( '_mshop_point_purchase_user_id' );
			$order->delete_meta_data( '_mshop_point_by_fee' );
		}

		$order->save_meta_data();
	}

	public static function woocommerce_admin_order_totals_after_discount( $order_id ) {
		$point_exchange_ratio = MSPS_Manager::point_exchange_ratio();

		$order = wc_get_order( $order_id );
		$point = $order->get_meta( '_mshop_point' );

		if ( floatval( $point ) > 0 && 'yes' !== $order->get_meta( '_mshop_point_by_fee' ) ) {
			?>
            <tr>
                <td class="label"><?php _e( '적립금 할인', 'mshop-point-ex' ); ?> <span class="tips" data-tip="<?php _e( 'This is the total discount applied after tax.', 'mshop-point-ex' ); ?>">[?]</span>:</td>
                <td class="total">
                    <div class="view"><?php echo wc_price( $point * $point_exchange_ratio ); ?></div>
                    <div class="edit" style="display: none;">
                        <input type="text" class="wc_input_price" id="_mshop_point" name="_mshop_point" placeholder="<?php echo wc_format_localized_price( 0 ); ?>" value="<?php echo ( isset( $point ) ) ? esc_attr( wc_format_localized_price( $point ) ) : ''; ?>"/>
                        <div class="clear"></div>
                    </div>
                </td>
                <div style="display:none" class="woocommerce_order_items">
                    <input type="hidden" class="line_tax" value="<?php echo ( isset( $point ) ) ? esc_attr( wc_format_localized_price( - 1 * $point ) ) : ''; ?>"/>
                </div>
                <td><?php if ( $order->is_editable() ) : ?>
                        <div class="wc-order-edit-line-item-actions"><a class="edit-order-item" href="#"></a></div><?php endif; ?></td>
            </tr>
			<?php
		}
	}
	public static function woocommerce_get_order_item_totals( $total_rows, $order ) {
		$point_exchange_ratio = MSPS_Manager::point_exchange_ratio();

		$used_point   = MSPS_Order::get_used_point( $order );
		$point_by_fee = $order->get_meta( '_mshop_point_by_fee', 'no' );

		if ( $used_point > 0 && 'yes' !== $point_by_fee ) {
			$point_row = array(
				'label' => __( '포인트 할인:', 'mshop-point-ex' ),
				'value' => wc_price( floatval( $used_point ) * $point_exchange_ratio )
			);

			return array_merge( array_slice( $total_rows, 0, sizeof( $total_rows ) - 1 ), array( $point_row ), array_slice( $total_rows, sizeof( $total_rows ) - 1 ) );
		} else {
			return $total_rows;
		}
	}
	static function update_order( $order, $used_point ) {
		if ( version_compare( WC_VERSION, '3.6.0', '<' ) ) {
			$fee_total = 0;
			$fees      = $order->get_fees();

			foreach ( $order->get_items( 'fee' ) as $item_id => $item ) {
				if ( 0 === strpos( $item['name'], __( '포인트 할인', 'mshop-point-ex' ) ) ) {
					if ( wc_prices_include_tax() ) {
						$fee_total += abs( round( $item['line_total'] + $item['line_tax'], wc_get_price_decimals() ) );
					} else {
						$fee_total += abs( $item['line_total'] );
					}
				}
			}

			if ( $fee_total > 0 && $fee_total == $used_point * MSPS_Manager::point_exchange_ratio() ) {
				foreach ( $fees as $item_id => $item ) {
					if ( 0 === strpos( $item['name'], __( '포인트 할인', 'mshop-point-ex' ) ) ) {
						$item['line_total'] = 0;
						$order->update_fee( $item_id, $item );
					}
				}

				$order->calculate_totals( wc_tax_enabled() );

				return true;
			}

			return false;
		} else {
			return true;
		}
	}
	static function process_redeposit( $order_id, $old_status, $new_status ) {
		$order = wc_get_order( $order_id );

		if ( ! empty( $order->get_user_id() ) ) {
			$user       = new MSPS_User( $order->get_user_id(), $order->get_meta( 'wpml_language' ) );
			$prev_point = $user->get_point();

			$p_payment_method = $order->get_payment_method();

			if ( MSPS_Manager::PAYMENT_GATEWAY_POINT != $p_payment_method ) {

				// Check redeposit feature is supported
				if ( MSPS_Manager::support_redeposit_when_refunded() ) {

					// Check order status and already not redeposited
					if ( in_array( $new_status, array( 'cancelled', 'refunded' ) ) && ! self::is_redeposit_processed( $order ) ) {
						$order = wc_get_order( $order_id );

						$deduction_info = MSPS_Order::get_deduction_info( $order );
						$used_point     = MSPS_Order::get_used_point( $order );

						if ( $used_point > 0 && self::update_order( $order, $used_point ) ) {
							$current_balance = $user->get_point();
							$user->wallet->redeposit( $deduction_info );
							$remain_point = $user->get_point();
							self::set_redeposit_processed( $order, true );
							foreach ( $deduction_info as $item_id => $amount ) {
								$current_balance += $amount;
								MSPS_Log::add_log( $order->get_user_id(), msps_get_wallet_id( $item_id, $order ), 'earn', 'purchase', $amount, $current_balance, 'completed', $order_id, '', msps_get_wallet_name( $user, $item_id ) );
							}

							$message = sprintf( __( '주문(<a href="%1$s">#%2$s</a>)이 취소되어 결제에 사용된 %3$s포인트가 재적립되었습니다.<br>보유포인트가 %4$s포인트에서 %5$s포인트로 변경되었습니다.', 'mshop-point-ex' ), get_edit_post_link( $order_id ), $order_id, number_format( floatval( $used_point ), wc_get_price_decimals() ), number_format( $prev_point, wc_get_price_decimals() ), number_format( $remain_point, wc_get_price_decimals() ) );
							$order->add_order_note( __( '[포인트 알림] ', 'mshop-point-ex' ) . $message );
						}
					}
				}
			} else if ( MSPS_Manager::PAYMENT_GATEWAY_POINT == $p_payment_method ) {

				if ( in_array( $new_status, array( 'cancelled', 'refunded', 'failed' ) ) && ! self::is_redeposit_processed( $order ) ) {
					$prev_point   = $user->get_point();
					$used_point   = $order->get_total() / MSPS_Manager::point_exchange_ratio();
					$remain_point = $user->earn_point( $used_point );
					self::set_redeposit_processed( $order, true );

					MSPS_Log::add_log( $order->get_user_id(), msps_get_wallet_id( 'free_point', $order ), 'earn', 'purchase', $used_point, $remain_point, 'completed', $order_id );

					$message = sprintf( __( '주문(<a href="%s">#%s</a>) 결제 취소로 %s포인트가 재적립 되었습니다.<br>보유포인트가 %s포인트에서 %s포인트로 변경되었습니다.', 'mshop-point-ex' ), get_edit_post_link( $order_id ), $order_id, number_format( floatval( $used_point ), wc_get_price_decimals() ), number_format( $prev_point, wc_get_price_decimals() ), number_format( $remain_point, wc_get_price_decimals() ) );
					$order->add_order_note( __( '[포인트 알림] ', 'mshop-point-ex' ) . $message );

					$order->calculate_totals();

				} else if ( in_array( $old_status, array( 'cancelled', 'refunded', 'failed' ) ) && self::is_redeposit_processed( $order ) ) {
					$prev_point   = $user->get_point();
					$used_point   = $order->get_total() / MSPS_Manager::point_exchange_ratio();
					$remain_point = $user->deduct_point( $used_point );
					self::set_redeposit_processed( $order, false );

					MSPS_Log::add_log( $order->get_user_id(), msps_get_wallet_id( 'free_point', $order ), 'deduct', 'purchase', - 1 * $used_point, $remain_point, 'completed', $order_id );

					$message = sprintf( __( '주문(<a href="%s">#%s</a>) 결제시 포인트 사용으로 %s포인트가 차감되었습니다.<br>보유포인트가 %s포인트에서 %s포인트로 변경되었습니다.', 'mshop-point-ex' ), get_edit_post_link( $order_id ), $order_id, number_format( floatval( - 1 * $used_point ), wc_get_price_decimals() ), number_format( $prev_point, wc_get_price_decimals() ), number_format( $remain_point, wc_get_price_decimals() ) );

					$order->add_order_note( __( '[포인트 알림] ', 'mshop-point-ex' ) . $message );
				}
			}
		}
	}

	static function get_earn_point_status( $order_id ) {
		if ( is_null( self::$earn_point_statuses ) ) {
			$order_statuses = explode( ',', get_option( 'msps_point_eran_status', 'completed' ) );

			self::$earn_point_statuses = apply_filters( 'msps_get_earn_point_status', $order_statuses, $order_id );
		}

		return self::$earn_point_statuses;
	}
	static function process_point( $order_id, $old_status, $new_status ) {
		$order = wc_get_order( $order_id );

		if ( ! empty( $order->get_user_id() ) ) {
			$user       = new MSPS_User( $order->get_user_id(), $order->get_meta( 'wpml_language' ) );
			$used_point = MSPS_Order::get_used_point( $order );
			$prev_point = $user->get_point( array( msps_get_wallet_id( 'free_point', $order ) ), false );
			if ( ! MSPS_Manager::allow_coupon() && count( $order->get_used_coupons() ) > 0 ) {
				return;
			}

			$p_payment_method = $order->get_payment_method();

			if ( MSPS_Manager::PAYMENT_GATEWAY_POINT != $p_payment_method ) {
				if ( $used_point > 0 && ! MSPS_Manager::support_earn_point_for_point_discount() ) {
					return;
				}
			} else if ( MSPS_Manager::PAYMENT_GATEWAY_POINT == $p_payment_method ) {
				if ( ! MSPS_Manager::support_earn_point_for_point_payment() ) {
					return;
				}
			}

			$earn_point = self::get_earn_point( $order );

			if ( $earn_point > 0 ) {
				if ( in_array( $new_status, self::get_earn_point_status( $order_id ) ) && ! self::is_earn_processed( $order ) ) {
					$remain_point = $user->earn_point( $earn_point );
					self::set_earn_processed( $order, true );

					MSPS_Log::add_log( $order->get_user_id(), msps_get_wallet_id( 'free_point', $order ), 'earn', 'order', $earn_point, $remain_point, 'completed', $order_id );

					$message = sprintf( __( '주문(<a href="%1$s">#%2$s</a>)이 완료되어 %3$s포인트가 적립되었습니다.<br>보유포인트가 %4$s포인트에서 %5$s포인트로 변경되었습니다.', 'mshop-point-ex' ), get_edit_post_link( $order_id ), $order_id, number_format( floatval( $earn_point ), wc_get_price_decimals() ), number_format( $prev_point, wc_get_price_decimals() ), number_format( $remain_point, wc_get_price_decimals() ) );
					$order->add_order_note( __( '[포인트 알림] ', 'mshop-point-ex' ) . $message );

					do_action( 'msps_earn_point', $earn_point, $order_id );

				} else if ( ! in_array( $new_status, self::get_earn_point_status( $order_id ) ) && in_array( $old_status, self::get_earn_point_status( $order_id ) ) && self::is_earn_processed( $order ) ) {
					$remain_point = $user->deduct_point( $earn_point );
					self::set_earn_processed( $order, false );

					MSPS_Log::add_log( $order->get_user_id(), msps_get_wallet_id( 'free_point', $order ), 'deduct', 'order', - 1 * $earn_point, $remain_point, 'completed', $order_id );

					$message = sprintf( __( '주문(<a href="%1$s">#%2$s</a>)이 취소되어 %3$s포인트가 차감되었습니다.<br>보유포인트가 %4$s포인트에서 %5$s포인트로 변경되었습니다.', 'mshop-point-ex' ), get_edit_post_link( $order_id ), $order_id, number_format( floatval( $earn_point ), wc_get_price_decimals() ), number_format( $prev_point, wc_get_price_decimals() ), number_format( $remain_point, wc_get_price_decimals() ) );
					$order->add_order_note( __( '[포인트 알림] ', 'mshop-point-ex' ) . $message );

					do_action( 'msps_deduct_point', $earn_point, $order_id );
				}
			}
		}
	}
	public static function woocommerce_checkout_order_processed( $order_id, $post ) {
		if ( is_a( $order_id, 'WC_Order' ) ) {
			$order = $order_id;
		} else if ( is_int( $order_id ) ) {
			$order = wc_get_order( $order_id );
		}

		if ( ! $order ) {
			return;
		}

		if ( ! apply_filters( 'msps_process_calculate_point', true, $order ) ) {
			return;
		}

		if ( ! MSPS_Manager::is_valid_user( mshop_point_get_user_role( $order->get_customer_id() ) ) ) {
			return;
		}

		add_filter( 'mshop_membership_skip_price_calculation', '__return_true' );

		$point_exchange_ratio = MSPS_Manager::point_exchange_ratio();
		$user_role            = mshop_point_get_user_role( $order->get_user_id() );
		$earn_point           = MSPS_Manager::get_expected_point( $order, 0, $user_role );
		$used_point           = MSPS_Order::get_used_point( $order );

		remove_filter( 'mshop_membership_skip_price_calculation', '__return_true' );
		$earn_point = round( $earn_point, wc_get_price_decimals() );

		$p_payment_method = $order->get_payment_method();

		if ( MSPS_Manager::PAYMENT_GATEWAY_POINT != $p_payment_method ) {
			if ( $used_point > 0 && ! MSPS_Manager::support_earn_point_for_point_discount() ) {
				$order->add_order_note( __( '[포인트 알림] 포인트 할인을 받은 주문건으로 포인트가 적립되지 않습니다.', 'mshop-point-ex' ) );

				return;
			}
		}

		if ( MSPS_Manager::PAYMENT_GATEWAY_POINT == $p_payment_method ) {
			if ( ! MSPS_Manager::support_earn_point_for_point_payment() ) {
				$order->add_order_note( __( '[포인트 알림] 포인트 결제건으로 포인트가 적립되지 않습니다.', 'mshop-point-ex' ) );

				return;
			}
		}
		$additional_msg = array();

		if ( $earn_point > 0 && $used_point > 0 && 'yes' == get_option( 'mshop_point_reduce_earn_point', 'no' ) ) {
			$deduct_amount    = $earn_point * ( $used_point * $point_exchange_ratio / ( $order->get_subtotal() - $order->get_discount_total() ) );
			$additional_msg[] = sprintf( __( '결제시 포인트 사용으로 적립예정 포인트가 %s 포인트만큼 차감되었습니다. ( %s -> %s )', 'mshop-point-ex' ), number_format( $deduct_amount, wc_get_price_decimals() ), number_format( $earn_point, wc_get_price_decimals() ), number_format( $earn_point - $deduct_amount, wc_get_price_decimals() ) );
			$earn_point       -= $earn_point * ( $used_point * $point_exchange_ratio / ( $order->get_subtotal() - $order->get_discount_total() ) );
		}

		$earn_point = apply_filters( 'msps_calculate_point_for_order', $earn_point, $order );

		self::set_earn_point( $order, $earn_point );

		if ( $earn_point > 0 ) {
			$current_language = apply_filters( 'msps_get_current_language', mshop_wpml_get_current_language() );
			MSPS_Log::add_log( $order->get_user_id(), msps_get_wallet_id( 'free_point', null, $current_language ), 'earn', 'order', $earn_point, 0, 'pending', $order_id );
			$order->add_order_note( sprintf( __( '[포인트 알림] 주문처리가 완료되면 고객에게 %s 포인트가 적립됩니다.', 'mshop-point-ex' ), number_format( $earn_point, wc_get_price_decimals() ) ) );
		}
	}
	public static function calculate_point_for_renewal_order( $renewal_order, $subscription ) {
		if ( apply_filters( 'msps_process_point_for_renewal_order', true ) ) {
			add_filter( 'msps_is_subscription_renewal_order', '__return_true' );

			do_action( 'msps_before_calculate_point_for_renewal_order', $renewal_order, $subscription );

			$user_role  = mshop_point_get_user_role( $renewal_order->get_user_id() );
			$earn_point = MSPS_Manager::get_expected_point( $renewal_order, 0, $user_role );

			$earn_point = apply_filters( 'msps_calculate_point_for_renewal_order', $earn_point, $renewal_order, $subscription );

			self::set_earn_point( $renewal_order, $earn_point );

			if ( $earn_point > 0 ) {
				$renewal_order->add_order_note( sprintf( __( '[포인트 알림] 주문처리가 완료되면 고객에게 %s 포인트가 적립됩니다.', 'mshop-point-ex' ), number_format( $earn_point, wc_get_price_decimals() ) ) );
			}

			do_action( 'msps_after_calculate_point_for_renewal_order', $renewal_order, $subscription );

			remove_filter( 'msps_is_subscription_renewal_order', '__return_true' );
		}

		return $renewal_order;
	}
	public static function maybe_deduct_point_for_partial_refund( $order_id, $refund_id ) {
		if ( 'yes' == get_option( 'msps_deduct_point_for_partial_refunded', 'no' ) ) {
			if ( is_ajax() && 'woocommerce_refund_line_items' == msps_get( $_REQUEST, 'action' ) ) {
				$order = wc_get_order( $order_id );

				if ( $order->get_customer_id() > 0 ) {
					$earn_point = MSPS_Order::get_earn_point( $order );

					if ( $earn_point > 0 ) {
						$refund_order  = wc_get_order( $refund_id );
						$refund_amount = abs( $refund_order->get_total() );
						$order_total   = $order->get_total() - ( $order->get_total_refunded() - $refund_amount );

						if ( $order_total > $refund_amount ) {
							$deduct_point   = round( $earn_point * ( $refund_amount / $order_total ), wc_get_rounding_precision() );
							$adjusted_point = $earn_point - $deduct_point;

							MSPS_Order::set_earn_point( $order, $adjusted_point );

							$order->add_order_note( sprintf( "[포인트 알림 - 부분환불] 적립예정 포인트가 %sP 차감되어 %sP에서 %sP로 변경되었습니다.", number_format( $deduct_point, wc_get_price_decimals() ), number_format( $earn_point, wc_get_price_decimals() ), number_format( $adjusted_point, wc_get_price_decimals() ) ) );

							if ( MSPS_Order::is_earn_processed( $order ) ) {
								$user         = new MSPS_User( $order->get_customer_id(), $order->get_meta( 'wpml_language' ) );
								$prev_point   = $user->get_point( array( msps_get_wallet_id( 'free_point', $order ) ) );
								$remain_point = $user->deduct_point( $deduct_point, msps_get_wallet_id( 'free_point', $order ) );

								MSPS_Log::add_log( $order->get_customer_id(), msps_get_wallet_id( 'free_point', $order ), 'deduct', 'order', - 1 * $deduct_point, $remain_point, 'completed', $order_id );

								$message = sprintf( __( '주문(<a href="%1$s">#%2$s</a>)이 부분환불되어 %3$sP가 차감되었습니다.<br>보유포인트가 %4$sP에서 %5$sP로 변경되었습니다.', 'mshop-point-ex' ), get_edit_post_link( $order_id ), $order_id, number_format( floatval( $deduct_point ), wc_get_price_decimals() ), number_format( $prev_point, wc_get_price_decimals() ), number_format( $remain_point, wc_get_price_decimals() ) );
								$order->add_order_note( __( '[포인트 알림 - 부분환불] ', 'mshop-point-ex' ) . $message );
							}
						}
					}
				}
			}
		}
	}
}