<?php

if ( ! class_exists( 'MSIV_Manager' ) ) {

	class MSIV_Manager {

		static $_settings = null;
		static $_rules = null;
		static function load_rules() {
			if ( is_null( self::$_rules ) ) {
				self::$_settings = get_option( 'woocommerce_korea_zone_shipping_settings' );

				self::$_settings['apply_iv_for_shipping_methods'] = array_filter( explode( ',', self::$_settings['apply_iv_for_shipping_methods'] ) );

				if ( ! empty( self::$_settings ) && ! empty( self::$_settings['msiv_shipping_rules'] ) ) {
					self::$_rules = self::$_settings['msiv_shipping_rules'];
				} else {
					self::$_rules = array();
				}
			}

			return self::$_rules;
		}
		static function get_title() {
			self::load_rules();

			return self::$_settings['title'];
		}
		static function apply_iv_fee( $shipping_method ) {
			self::load_rules();

			return self::$_settings[ 'apply_iv_fee_' . $shipping_method ];
		}
		static function apply_iv_fee_one_time_shipping( $shipping_method ) {
			self::load_rules();

			return 'yes' == self::apply_iv_fee( $shipping_method ) ? self::$_settings['apply_iv_fee_one_time_shipping'] : 'no';
		}
		static function apply_iv_fee_free_coupon( $shipping_method ) {
			self::load_rules();

			return 'yes' == self::apply_iv_fee( $shipping_method ) ? self::$_settings['apply_iv_fee_free_coupon'] : 'no';
		}
		static function find_rule( $postalcode ) {
			$rules = self::load_rules();

			foreach ( $rules as $rule ) {
				if ( ! empty( $rule['postalcode'] ) ) {
					$iv_codes = explode( ',', $rule['postalcode'] );

					foreach ( $iv_codes as $iv_code ) {
						$iv_code = explode( '..', $iv_code );
						if ( count( $iv_code ) > 1 ) {
							if ( $postalcode >= $iv_code[0] && $postalcode <= $iv_code[1] ) {
								return $rule;
							}
						} else {
							if ( 0 === strpos( $postalcode, $iv_code[0] ) ) {
								return $rule;
							}
						}
					}
				}
			}

			return null;
		}
		static function get_shipping_classes( $package, $shipping_classes ) {
			$classes = array();

			foreach ( $package['contents'] as $content ) {
				$product        = $content['data'];
				$shipping_class = $product->get_shipping_class();

				if ( ! empty( $shipping_class ) && in_array( $shipping_class, $shipping_classes ) ) {
					if ( ! isset( $classes[ $shipping_class ] ) ) {
						$classes[ $shipping_class ] = 0;
					}

					$classes[ $shipping_class ] += intval( $content['quantity'] );
				} else if ( empty( $shipping_class ) && empty( $shipping_classes ) ) {
					if ( ! isset( $classes['none'] ) ) {
						$classes['none'] = 0;
					}

					$classes['none'] += intval( $content['quantity'] );
				}
			}

			return $classes;
		}
		static function get_shipping_method( $chosen_method, $package ) {
			$shipping_method = null;

			$method_info = explode( ':', $chosen_method );

			if ( count( $method_info ) >= 2 ) {
				$shipping_method = WC_Shipping_Zones::get_shipping_method( $method_info[1] );
			}

			return apply_filters( 'msiv_shipping_method', $shipping_method, $chosen_method, $package );
		}
		static function is_applicable( $shipping_method ) {
			return 'yes' == self::$_settings[ 'apply_iv_fee_' . $shipping_method->id ] || in_array( $shipping_method->id, self::$_settings['apply_iv_for_shipping_methods'] );
		}
		public static function calculate_fees( $cart, $postalcode ) {
			include_once( WC()->plugin_path() . '/includes/libraries/class-wc-eval-math.php' );

			$cost = 0;
			$rule = self::find_rule( $postalcode );

			if ( is_null( $rule ) ) {
				return 0;
			}
			add_filter( 'woocommerce_subscriptions_product_trial_length', '__return_false' );
			$packages = $cart->get_shipping_packages();
			remove_filter( 'woocommerce_subscriptions_product_trial_length', '__return_false' );

			$items = array();

			foreach ( $packages as $i => $package ) {
				$chosen_method = ! empty( $_POST['shipping_method'][ $i ] ) ? $_POST['shipping_method'][ $i ] : '';
				if ( empty( $chosen_method ) ) {
					$shipping_rates = $cart->calculate_shipping();

					if ( ! empty( $shipping_rates ) ) {
						$shipping_rate = current( $shipping_rates );
						$chosen_method = $shipping_rate->get_id();
					}
				}

				$shipping_method = self::get_shipping_method( $chosen_method, $package );

				if ( is_null( $shipping_method ) ) {
					$available_methods = WC()->shipping()->load_shipping_methods( $package );

					if ( $chosen_method ) {
						$shipping_method = $available_methods[ $chosen_method ];
					} else {
						$shipping_method = current( $available_methods );
					}
				}

				if ( ! empty( $shipping_method ) ) {
					if ( self::is_applicable( $shipping_method ) ) {
						$items[] = array(
							'shipping_method' => $shipping_method,
							'package'         => $package
						);
					}
				}
			}

			if ( empty( $items ) ) {
				return 0;
			}

			foreach ( $items as $item ) {
				$package         = $item['package'];
				$shipping_method = $item['shipping_method'];
				$class_fees = array();

				$quantities = array_map( function ( $content ) {
					return $content['quantity'];
				}, $item['package']['contents'] );

				$quantity = array_sum( $quantities );

				$is_set          = false;
				$is_shipping_set = false;
				foreach ( $rule['fee_rules'] as $fee_rule ) {
					if ( ! $is_shipping_set && 'always' == $fee_rule['target'] ) {
						$cost  += WC_Eval_Math::evaluate( str_replace( '[qty]', $quantity, $fee_rule['cost'] ) );
						$isset = true;
					} else if ( ! $is_shipping_set && 'min_amount' == $fee_rule['target'] ) {
						if ( $package['contents_cost'] <= $fee_rule['min_amount'] ) {
							$cost   = WC_Eval_Math::evaluate( str_replace( '[qty]', $quantity, $fee_rule['cost'] ) );
							$is_set = true;
						}
					} else if ( 'shipping_class' == $fee_rule['target'] ) {
						$shipping_classes = self::get_shipping_classes( $package, array_filter( explode( ',', $fee_rule['shipping_class'] ) ) );

						if ( ! empty( $shipping_classes ) ) {
							foreach ( $shipping_classes as $shipping_class => $class_quantity ) {
								if ( ! isset( $class_fees[ $shipping_class ] ) ) {
									$class_fees[ $shipping_class ] = 0;
								}

								$class_fees[ $shipping_class ] += WC_Eval_Math::evaluate( str_replace( '[qty]', $class_quantity, $fee_rule['cost'] ) );

								if ( apply_filters( 'msiv_prevent_apply_shipping_class_rule_with_other_rule', false, $package, $fee_rule ) ) {
									$is_shipping_set = true;
								}
							}
						}
					}

					if ( $is_set ) {
						break;
					}
				}

				if ( ! empty( $class_fees ) ) {
					if ( version_compare( WOOCOMMERCE_VERSION, '2.6.0', '>=' ) ) {
						$class_option = $shipping_method->get_instance_option( 'type' );
					} else {
						$class_option = $shipping_method->settings['type'];
					}

					if ( 'class' == $class_option ) {
						$cost += array_sum( $class_fees );
					} else {
						$cost += max( $class_fees );
					}
				}

			}

			return $cost;
		}

		public static function calc_taxes() {
			return 'yes' == self::$_settings['calc_taxes'];
		}

		public static function get_tax_class() {
			if ( self::calc_taxes() ) {
				return 'standard' == self::$_settings['tax_class'] ? '' : self::$_settings['tax_class'];
			}

			return '';
		}
	}
}

