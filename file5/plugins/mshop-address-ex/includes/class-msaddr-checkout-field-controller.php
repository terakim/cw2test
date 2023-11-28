<?php

/*
=====================================================================================
                ﻿엠샵 주소 체크아웃 / Copyright 2015 by CodeM(c)
=====================================================================================

  [ 우커머스 버전 지원 안내 ]

   워드프레스 버전 : WordPress 4.3

   우커머스 버전 : WooCommerce 2.4


  [ 코드엠 플러그인 라이센스 규정 ]

   (주)코드엠에서 개발된 워드프레스  플러그인을 사용하시는 분들에게는 다음 사항에 대한 동의가 있는 것으로 간주합니다.

   1. 코드엠에서 개발한 워드프레스 우커머스용 ﻿엠샵 주소 체크아웃 플러그인의 저작권은 (주)코드엠에게 있습니다.
   
   2. 플러그인은 사용권을 구매하는 것이며, 프로그램 저작권에 대한 구매가 아닙니다.

   3. 플러그인을 구입하여 다수의 사이트에 복사하여 사용할 수 없으며, 1개의 라이센스는 1개의 사이트에만 사용할 수 있습니다. 
      이를 위반 시 지적 재산권에 대한 손해 배상 의무를 갖습니다.

   4. 플러그인은 구입 후 1년간 업데이트를 지원합니다.

   5. 플러그인은 워드프레스, 테마, 플러그인과의 호환성에 대한 책임이 없습니다.

   6. 플러그인 설치 후 버전에 관련한 운용 및 관리의 책임은 사이트 당사자에게 있습니다.

   7. 다운로드한 플러그인은 환불되지 않습니다.

=====================================================================================
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MSADDR_Checkout_Field_Controller' ) ) :

	class MSADDR_Checkout_Field_Controller {

		protected static $rules = null;

		protected static $cart_product_ids = null;

		protected static $matched_rules = null;

		protected static function get_rules() {
			if ( is_null( self::$rules ) ) {
				self::$rules = array(
					'role'     => get_option( 'msaddr-filter-field-by-role', array() ),
					'product'  => get_option( 'msaddr-filter-field-by-product', array() ),
					'category' => get_option( 'msaddr-filter-field-by-category', array() ),
				);
			}

			return self::$rules;
		}

		protected static function get_matched_rules( $field, $fieldset ) {
			$matched_rules = array(
				'role'     => array(),
				'product'  => array(),
				'category' => array(),
			);
			$rules         = self::get_rules();

			if ( ! empty( $rules['role'] ) ) {
				foreach ( $rules['role'] as $rule ) {
					if ( 'yes' == $rule['enabled'] && ! empty( $rule[ $fieldset . '_fields' ] ) ) {
						$fields = explode( ',', $rule[ $fieldset . '_fields' ] );
						if ( in_array( $field['id'], $fields ) ) {
							$matched_rules['role'][] = $rule;
						}
					}
				}
			}

			if ( ! empty( $rules['product'] ) ) {
				foreach ( $rules['product'] as $rule ) {
					if ( 'yes' == $rule['enabled'] && ! empty( $rule[ $fieldset . '_fields' ] ) ) {
						$fields = explode( ',', $rule[ $fieldset . '_fields' ] );
						if ( in_array( $field['id'], $fields ) ) {
							$matched_rules['product'][] = $rule;
						}
					}
				}
			}

			if ( ! empty( $rules['category'] ) ) {
				foreach ( $rules['category'] as $rule ) {
					if ( 'yes' == $rule['enabled'] && ! empty( $rule[ $fieldset . '_fields' ] ) ) {
						$fields = explode( ',', $rule[ $fieldset . '_fields' ] );
						if ( in_array( $field['id'], $fields ) ) {
							$matched_rules['category'][] = $rule;
						}
					}
				}
			}

			return $matched_rules;
		}

		static function filter_address_field_by_rule( $enabled, $field, $fieldset ) {

			$rules = self::get_matched_rules( $field, $fieldset );

			if ( empty( $rules['role'] ) && empty( $rules['product'] ) && empty( $rules['category'] ) ) {
				return $enabled;
			}

			if ( ! empty( $rules['role'] ) ) {
				foreach ( $rules['role'] as $rule ) {
					$rule_roles = explode( ',', $rule['roles'] );
					$user_roles = msaddr_get_current_user_roles();

					if ( ! empty( array_intersect( $rule_roles, $user_roles ) ) ) {
						return true;
					}
				}
			}

			$cart_product_ids = self::get_cart_product_ids();

			if ( ! empty( $rules['product'] ) ) {
				foreach ( $rules['product'] as $rule ) {
					$product_ids = array_keys( $rule['products'] );

					foreach ( $cart_product_ids['parent_id'] as $cart_product_id ) {
						if ( in_array( $cart_product_id, $product_ids ) ) {
							return true;
						}
					}
				}
			}

			if ( ! empty( $rules['category'] ) ) {
				foreach ( $rules['category'] as $rule ) {
					foreach ( $cart_product_ids['parent_id'] as $cart_product_id ) {

						$terms = get_the_terms( $cart_product_id, 'product_cat' );

						if ( ! empty( $terms ) ) {
							$term_ids = array_flip( array_map( function ( $term ) {
								$term_id = apply_filters( 'wpml_object_id', $term->term_id, 'product_cat', true, msaddr_get_default_language() );

								return $term_id;
							}, $terms ) );

							if ( ! empty( array_intersect_key( $term_ids, $rule['categories'] ) ) ) {
								return true;
							}
						}
					}
				}
			}

			return false;
		}
		static function get_cart_product_ids( $cart = null ) {
			if ( is_null( self::$cart_product_ids ) ) {
				$product_ids = array(
					'variations' => array(),
					'parent_id'  => array()
				);

				if ( is_null( $cart ) ) {
					$cart = WC()->cart;
				}

				if ( is_callable( array( $cart, 'get_cart_contents' ) ) ) {
					$cart_contents = $cart->get_cart_contents();
				} else {
					$cart_contents = $cart->cart_contents;
				}

				foreach ( $cart_contents as $content ) {
					$product = $content['data'];

					foreach ( msaddr_get( $content, 'variation', array() ) as $attribute => $slug ) {
						$product_ids['variations'][] = array(
							'attribute' => str_replace( 'attribute_', '', $attribute ),
							'slug'      => $slug
						);
					}
					$product_ids['parent_id'][] = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
				}

				self::$cart_product_ids = $product_ids;
			}

			return self::$cart_product_ids;
		}
		static function filter_address_field( $enabled, $field, $fieldset ) {
			if ( is_checkout() ) {
				$enabled = self::filter_address_field_by_rule( $enabled, $field, $fieldset );
			}

			return $enabled;
		}
	}

endif;
