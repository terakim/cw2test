<?php
/*
=====================================================================================
                엠샵 업다운로드 / Copyright 2016 by CodeM(c)
=====================================================================================

  [ 우커머스 버전 지원 안내 ]

   워드프레스 버전 : WordPress 4.6 이상

   우커머스 버전 : WooCommerce 2.6 이상


  [ 코드엠 플러그인 라이센스 규정 ]

   (주)코드엠에서 개발된 워드프레스  플러그인을 사용하시는 분들에게는 다음 사항에 대한 동의가 있는 것으로 간주합니다.

   1. 코드엠에서 개발한 워드프레스 우커머스용 엠샵 업다운로드 플러그인의 저작권은 (주)코드엠에게 있습니다.
   
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
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MSEX_Export_Product' ) ) {

	require_once MSEX()->plugin_path() . '/includes/abstract/abstract-msex-export.php';

	class MSEX_Export_Product extends MSEX_Export {

		public $attribute_count;

		function __construct( $template_id ) {
			parent::__construct( $template_id );

			$this->attribute_count = get_post_meta( $template_id, '_msex_attributes_count', true );

			add_filter( 'msex_get_header_product_attributes', array( $this, 'expand_headers_for_product_attributes' ), 10, 2 );
			add_filter( 'msex_export_product_field_value_array_' . $this->get_slug(), array( $this, 'set_product_attributes' ), 10, 3 );
		}

		public function expand_headers_for_product_attributes( $headers, $field ) {
			$headers = array();

			for ( $i = 1; $i <= $this->attribute_count; $i ++ ) {
				$headers[] = sprintf( __( '속성 #%d', 'mshop-exporter' ), $i );
			}

			return $headers;
		}
		public function set_product_attributes( $values, $field, $product ) {
			if ( 'product_attributes' == $field['field_type'] ) {
				$values = array();

				$idx = 1;

				if ( in_array( $product->get_type(), array( 'variable', 'variable-subscription' ) ) ) {
					foreach ( $product->get_attributes() as $attribute ) {
						$values[] = wc_attribute_label( $attribute['name'] );
						if ( $idx ++ >= $this->attribute_count ) {
							break;
						}
					}
				} else if ( in_array( $product->get_type(), array( 'variation', 'subscription_variation' ) ) ) {
					foreach ( $product->get_attributes() as $slug => $value ) {
						$values[] = $product->get_attribute( $slug );
						if ( $idx ++ >= $this->attribute_count ) {
							break;
						}
					}
				}

				for ( ; $idx <= $this->attribute_count; $idx ++ ) {
					$values[] = '';
				}
			}

			return $values;
		}
		public function get_categories( $product ) {

			$terms = get_the_terms( $product->get_id(), 'product_cat' );

			if ( empty( $terms ) && $product->get_parent_id() > 0 ) {
				$terms = get_the_terms( $product->get_parent_id(), 'product_cat' );
			}

			$categories = array();

			if ( ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					$link = get_term_link( $term, 'product_cat' );
					if ( is_wp_error( $link ) ) {
						return $link;
					}
					$categories[] = $term->name;
				}
			}

			return implode( ',', $categories );
		}
		public function get_row( $product ) {
			$row_data = array();

			foreach ( self::get_fields() as $field ) {
				$field_type  = msex_get( $field, 'field_type' );
				$field_label = msex_get( $field, 'field_label' );
				$meta_key    = msex_get( $field, 'meta_key' );
				$field_value = '';

				switch ( $field_type ) {
					case 'post_id' :
						$field_value = $product->get_id();
						break;
					case 'post_date' :
						$edit_date = $product->get_date_created( 'edit' );
						if ( $edit_date ) {
							$field_value = gmdate( 'Y-m-d H:i:s', $edit_date->getOffsetTimestamp() );
						}
						break;
					case 'sku' :
						$field_value = $product->get_sku();
						break;
					case 'product_name' :
						$field_value = $product->get_title();
						break;
					case 'product_type' :
						$field_value = $product->get_type();
						break;
					case 'regular_price' :
						$field_value = $product->get_regular_price();
						break;
					case 'sale_price' :
						$field_value = $product->get_sale_price();
						break;
					case 'stock_status' :
						$field_value = $product->managing_stock() ? $product->get_stock_status() : '재고관리 안함';
						break;
					case 'stock_count' :
						$field_value = $product->managing_stock() ? $product->get_stock_quantity() : '';
						break;
					case 'categories' :
						$field_value = self::get_categories( $product );
						break;
					case 'product_tag' :
						if ( $product && in_array( $product->get_type(), array( 'variation', 'subscription_variation' ) ) ) {
							$field_value = wp_strip_all_tags( wc_get_product_tag_list( $product->get_parent_id(), ',' ) );
						} else {
							$field_value = wp_strip_all_tags( wc_get_product_tag_list( $product->get_id(), ',' ) );
						}
						break;
					case 'permalink' :
						$field_value = $product->get_permalink();
						break;
					case 'custom_attributes' :
						if ( $product instanceof WC_Product ) {
							$field_value = $product->get_attribute( $field['meta_key'] );
						} else {
							$field_value = '';
						}
						break;
					case 'product_meta' :
						$field_value = msex_get_meta( $product, $meta_key );
						$field_type  = $meta_key;
						break;
					case 'custom' :
						$method = 'get_' . $field['meta_key'];

						if ( $product instanceof WC_Product && is_callable( array( $product, $method ) ) ) {
							$field_value = $product->$method();
							if ( is_array( $field_value ) || is_object( $field_value ) ) {
								$field_value = json_encode( $field_value );
							}
						} else {
							$field_type = $meta_key;
						}
						break;
					case 'text' :
						$field_type  = $field_label;
						$field_value = $meta_key;
						break;
					default :
						$field_value = apply_filters( 'msex_export_product_field_value_' . $this->get_slug(), $field_value, $field, $product );
						break;
				}

				if ( 'csv' == $this->get_download_type() && is_numeric( $field_value ) ) {
					$field_value = '="' . $field_value . '"';
				}

				$row_data = array_merge( $row_data, apply_filters( 'msex_export_product_field_value_array_' . $this->get_slug(), array( $field_value ), $field, $product ) );
			}

			return array_merge( $row_data, apply_filters( 'msex_export_product_row_' . $this->get_slug(), array(), $product, $this ) );
		}
		public function get_data( $product_ids ) {
			$product_data = array();
			foreach ( $product_ids as $product_id ) {
				$product = wc_get_product( $product_id );

				if ( $product ) {
					$product_row = $this->get_row( $product );

					$product_rows = apply_filters( 'msex_export_product_rows', array( $product_row ), $product, $this );

					$product_data = array_merge( $product_data, apply_filters( 'msex_export_product_item_' . $this->get_slug(), $product_rows, $product, $this ) );

					if ( in_array( $product->get_type(), array( 'variable', 'variable-subscription' ) ) ) {
						$variations = $product->get_available_variations();

						foreach ( $variations as $variation ) {
							$product_row  = $this->get_row( wc_get_product( $variation['variation_id'] ) );
							$product_data = array_merge( $product_data, apply_filters( 'msex_export_product_variation_item_' . $this->get_slug(), array( $product_row ), $product, $this ) );
						}
					}
				}
			}

			return $product_data;
		}
	}
}