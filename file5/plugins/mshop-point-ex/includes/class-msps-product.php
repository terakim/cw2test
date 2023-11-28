<?php

class MSPS_Product {
	public $id = 0;
	public $product = null;


	public $rules = array();
	public $active_rules = array();

	private $enabled = null;

	private $is_purchasable_by_point = true;
	public function __construct( $post_id ) {

		if ( is_numeric( $post_id ) ) {
			$this->id      = apply_filters( 'wpml_object_id', msps_get_product_id( $post_id ), 'product', true, mshop_wpml_get_default_language() );
			$this->product = wc_get_product( $this->id );
		} elseif ( $post_id instanceof WC_Product ) {
			$this->id = apply_filters( 'wpml_object_id', msps_get_product_id( $post_id ), 'product', true, mshop_wpml_get_default_language() );

			if ( ! has_filter( 'wpml_object_id' ) && $this->id == $post_id->get_id() ) {
				$this->product = $post_id;
			} else {
				$this->product = wc_get_product( $this->id );
			}
		}

		$this->enabled = ( 'yes' === get_post_meta( $this->id, '_mshop_point_use', 'no' ) );

		if ( $this->enabled ) {
			$this->load_rules( $this->id );
		}
	}

	public function enabled() {
		return $this->enabled;
	}
	public function is_point_purchasable() {
		$is_not_purchasable_by_point = ( 'yes' == get_post_meta( $this->id, '_mshop_point_not_purchasable', 'no' ) );
        if ( ! empty ( $this->product ) ) {
            if ( 'variation' == $this->product->get_type() ) {
                $is_not_purchasable_by_point = $is_not_purchasable_by_point || ( 'yes' == get_post_meta( $this->product->get_id(), '_mshop_point_not_purchasable', 'no' ) );
            }
        }

		return ! $is_not_purchasable_by_point;
	}

	public function is_except_earn_point() {
		$is_except_earn_point = ( 'yes' == get_post_meta( $this->id, '_mshop_point_except_earn_point', 'no' ) );
        if ( ! empty ( $this->product ) ) {
            if ( 'variation' == $this->product->get_type() ) {
                $is_except_earn_point = $is_except_earn_point || ( 'yes' == get_post_meta( $this->product->get_id(), '_mshop_point_except_earn_point', 'no' ) );
            }
        }

		return $is_except_earn_point;
	}
	public function must_purchase_by_point() {
		$must_purchase_by_point = ( 'yes' == get_post_meta( $this->id, '_msps_must_purchase_by_point', 'no' ) );
        if ( ! empty ( $this->product ) ) {
            if ( 'variation' == $this->product->get_type() ) {
                $must_purchase_by_point = $must_purchase_by_point || ( 'yes' == get_post_meta( $this->product->get_id(), '_msps_must_purchase_by_point', 'no' ) );
            }
        }

		return ! $must_purchase_by_point;
	}
	private function load_rules( $post_id ) {
		$this->rules = get_post_meta( $post_id, '_mshop_point_rules', true );
		if ( empty( $this->rules ) ) {
			$this->active_rules = array();
		} else {
			$this->active_rules = array_filter( $this->rules, function ( $rule ) {
				if ( ! empty( $rule['use_valid_term'] ) && 'yes' == $rule['use_valid_term'] ) {
					$dates = explode( ',', $rule['valid_term'] );
					$sdate = strtotime( $dates[0] . ' 00:00:00' );
					$edate = strtotime( $dates[1] . ' 23:59:59' );
					$now   = strtotime( date( "Y-m-d H:i:s" ) );

					if ( $sdate > $now || $edate < $now ) {
						return false;
					}
				}

				return true;
			} );
		}
	}
	public function calculate_point( $qty, $user_role, $cart_item ) {
		$rule         = $this->get_matched_rule( $qty, $user_role );
		$point_option = $this->get_user_point_option( $rule, $user_role );
        $amount       = 0;

		if ( ! empty( $point_option ) ) {


			if ( msps_get( $cart_item, 'total', 0 ) > 0 ) {
				$price = $cart_item['total'] / $qty;
			} else {
				if ( in_array( $this->product->get_type(), array( 'subscription', 'subscription_variation' ) ) && floatval( WC_Subscriptions_Product::get_sign_up_fee( $this->product ) ) > 0 ) {
					$price = floatval( $this->product->get_price() ) + floatval( WC_Subscriptions_Product::get_sign_up_fee( $this->product ) );
				} else {
					$price = floatval( $this->product->get_price() );
				}
			}

            $item_price = apply_filters( 'mshop_membership_get_discounted_price', $price, $this->product );

            if( floatval( $item_price ) > 0 ) {
                $amount += $item_price * $qty;
            }

            $fixed_amount = 'yes' == get_option( 'msps_apply_filxed_point_by_multiplying_product_qty', 'no' ) ? floatval( $point_option['fixed'] * $qty ) : $point_option['fixed'];
            $ratio_amount = $amount / 100.0 * floatval( $point_option['ratio'] );

			return floatval( $fixed_amount + $ratio_amount / MSPS_Manager::point_exchange_ratio() );
		}

		return 0;
	}
	public function get_user_point_option( $rule, $user_role ) {
		if ( $rule ) {
			$option = array_filter( $rule['roles'], function ( $role ) use ( $user_role ) {
				return $user_role == $role['role'];
			} );

			return is_array( $option ) ? array_shift( $option ) : $option;
		}

		return null;
	}
	public function get_matched_rule( $qty, $user_role ) {
		$rule_index = self::get_matched_rule_index( $qty, $user_role );

		return $rule_index >= 0 ? $this->active_rules[ $rule_index ] : null;
	}
	public function get_precedence_rule( $qty, $user_role ) {
		$rule_index = self::get_matched_rule_index( $qty, $user_role );

		if ( $rule_index == 0 ) {
			// 최상위 정책에 매칭된 경우
			return null;
		} else {
			if ( $rule_index == - 1 ) {
				$rule_index = count( $this->active_rules );
			}

			for ( $i = $rule_index - 1; $i >= 0; $i -- ) {
				$rule = $this->active_rules[ $i ];

				$option = $this->get_user_point_option( $rule, $user_role );

				if ( ! empty( $option ) && ( $option['fixed'] > 0 || $option['ratio'] > 0 ) ) {
					return $rule;
				}
			}
		}

		return null;
	}
	protected function get_matched_rule_index( $qty, $user_role ) {
		if ( $qty > 0 && count( $this->active_rules ) > 0 ) {
			$amount = $this->product->get_price() * $qty;

			for ( $i = 0; $i < count( $this->active_rules ); $i ++ ) {
				$rule = $this->active_rules[ $i ];
				$rule_amount = $rule['amount'];
				if ( ( $rule['amount'] == 0 && $rule['qty'] == 0 ) ||
				     ( $rule_amount > 0 && $rule_amount <= $amount ) ||
				     ( $rule['qty'] > 0 && $rule['qty'] <= $qty ) ) {

					$option = $this->get_user_point_option( $rule, $user_role );

					if ( ! empty( $option ) && ( $option['fixed'] > 0 || $option['ratio'] > 0 ) ) {
						return $i;
					}
				}
			}
		}

		return - 1;
	}
}
