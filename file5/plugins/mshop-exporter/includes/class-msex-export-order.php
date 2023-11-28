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

if ( ! class_exists( 'MSEX_Export_Order' ) ) {

	require_once MSEX()->plugin_path() . '/includes/abstract/abstract-msex-export.php';

	class MSEX_Export_Order extends MSEX_Export {

		protected $numeric_fields = null;

		public function get_numeric_fields() {
			if ( is_null( $this->numeric_fields ) ) {
				$this->numeric_fields = array(
					'product_qty',
					'product_total',
					'product_price',
					'order_used_point',
					'order_discount_price',
					'order_shipping_price',
					'order_refunded_price',
					'order_total',
					'additional_charge',
					'partial_refund'
				);
			}

			return $this->numeric_fields;
		}
		public function get_item_option( $item ) {
			$options = array();
			foreach ( $item->get_formatted_meta_data( '_', true ) as $meta_data ) {
				$options[] = $meta_data->display_key . ':' . wp_strip_all_tags( $meta_data->display_value );
			}

			return implode( ', ', $options );
		}
		public function get_order_used_point( $order ) {
			$used_point = 0;

			if ( class_exists( 'MSPS_Order' ) ) {
				if ( function_exists( 'wcs_order_contains_renewal' ) && wcs_order_contains_renewal( $order ) ) {
					return $used_point;
				}

				$used_point = msex_get_meta( $order, '_mshop_point' );

				if ( ! empty( $used_point ) && ! is_numeric( $used_point ) ) {
					$used_point = MSPS_Order::get_used_point( $order );
				}
			}

			return $used_point;
		}
		public function get_used_coupon( $order ) {
			$ids     = msex_get_coupon_codes( $order );
			$coupons = array();

			foreach ( $ids as $id ) {
				$coupon    = new WC_Coupon( $id );
				$coupons[] = msex_get_object_property( $coupon, 'code' );
			}

			return implode( ', ', $coupons );
		}
		public function get_subscription_meta( $order, $meta_key ) {
			$meta_value = '';

			if ( ! empty( $meta_key ) && class_exists( 'WC_Subscription' ) ) {
				if ( 'shop_subscription' == $order->get_type() ) {
					$subscription = $order;
				} else {
					$subscriptions = wcs_get_subscriptions_for_order( $order, array( 'order_type' => 'any' ) );
					$subscription  = reset( $subscriptions );
				}

				if ( ! empty( $subscription ) ) {
					switch ( $meta_key ) {
						case 'date_created' :
						case 'date_modified' :
						case 'date_paid' :
						case 'date_completed' :
						case 'last_order_date_created' :
						case 'last_order_date_paid' :
						case 'last_order_date_completed' :
						case 'next_payment':
						case 'end_date':
							$meta_value = $subscription->get_date( $meta_key );
							if ( ! empty( $meta_value ) ) {
								$meta_value = get_date_from_gmt( $meta_value );
							}
							break;
						case 'related_order_count' :
							$meta_value = count( $subscription->get_related_orders() );
							break;
						case 'paid_order_count' :
							$meta_value = 0;
							$order_ids  = $subscription->get_related_orders( 'ids', array( 'parent', 'renewal' ) );
							foreach ( $order_ids as $order_id ) {
								$order = wc_get_order( $order_id );

								if ( $order && ! in_array( $order->get_status(), array( 'cancelled', 'failed', 'refunded', 'pending', 'on-hold' ) ) ) {
									$meta_value++;
								}
							}
							break;
						case 'paid_order_amount' :
							$meta_value = 0;
							$order_ids  = $subscription->get_related_orders( 'ids', array( 'parent', 'renewal' ) );
							foreach ( $order_ids as $order_id ) {
								$order = wc_get_order( $order_id );

								if ( $order && ! in_array( $order->get_status(), array( 'cancelled', 'failed', 'refunded', 'pending', 'on-hold' ) ) ) {
									$meta_value += $order->get_total();
								}
							}
							break;
						case 'id' :
							$meta_value = $subscription->get_id();
							break;
						case 'status' :
							$meta_value = $subscription->get_status();
							break;
						default:
							$meta_value = msex_get_meta( $subscription, $meta_key );
					}
				} else {
					$row_data[ $meta_key ] = '';
				}
			}

			return $meta_value;
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
		public function get_product_attributes( $meta_key, $product, $order_item = null ) {
			$meta_value = null;

			if ( $order_item ) {
				foreach ( $order_item->get_formatted_meta_data( '_', true ) as $meta_data ) {
					if ( $meta_key == $meta_data->key ) {
						$meta_value = ! empty( $meta_data->display_value ) ? strip_tags( preg_replace( "/[\r\n]+/", "", $meta_data->display_value ) ) : $meta_data->value;
						break;
					}
				}
			}

			if ( is_null( $meta_value ) ) {
				$meta_value = '';

				if ( $product ) {
					$meta_value = $product->get_attribute( $meta_key );
				}

				if ( empty( $meta_value ) && $product && $product->get_parent_id() > 0 ) {
					$product = wc_get_product( $product->get_parent_id() );

					if ( $product ) {
						$meta_value = $product->get_attribute( $meta_key );
					}
				}
			}

			return $meta_value;
		}
		public function get_row( $order, $item_id, $item, $item_index ) {
			$row_data = array();

			$download = self::get_download_info( $order, $item_id );
			$order_id = msex_get_object_property( $order, 'id' );
			$customer = msex_get_customer_info( $order );
			$user_id  = $order->get_user_id();

			$product_id = empty( $item['variation_id'] ) ? $item['product_id'] : $item['variation_id'];
			$product    = wc_get_product( $product_id );

			foreach ( self::get_fields() as $field ) {
				$field_type  = msex_get( $field, 'field_type' );
				$field_label = msex_get( $field, 'field_label' );
				$meta_key    = msex_get( $field, 'meta_key' );
				$field_value = '';

				switch ( $field_type ) {
					case 'order_date' :
						$field_value = get_the_time( _x( 'Y-m-d H:i:s', 'timezone date format' ), $order_id );
						break;
					case 'order_id' :
						$field_value = $order->get_order_number();
						break;
					case 'order_status' :
						$field_value = wc_get_order_status_name( $order->get_status() );
						break;
					case 'billing_name' :
						$field_value = msex_remove_emoji( $customer['billing_name'] );
						break;
					case 'billing_country' :
						$field_value = $this->get_country_name( $order->get_billing_country() );
						break;
					case 'billing_phone' :
						$field_value = $customer['billing_phone'];
						break;
					case 'billing_postcode':
						$field_value = $customer['billing_postcode'];
						break;
					case 'billing_address' :
						$field_value = msex_remove_emoji( $customer['billing_address_1'] . ' ' . $customer['billing_address_2'] );
						break;
					case 'product_sku' :
						$field_value = msex_get_object_property( $product, 'sku' );
						break;
					case 'user_login' :
						$field_value = empty( $user_id ) ? '' : get_userdata( $user_id )->user_login;
						break;
					case 'product_id' :
						$field_value = $product_id;
						break;
					case 'product_name' :
						$field_value = $product ? $product->get_title() : $item->get_name();
						break;
					case 'order_item_name' :
						$field_value = $item->get_name();
						break;
					case 'order_option' :
						$field_value = $this->get_item_option( $item );
						break;
					case 'product_qty' :
						$field_value = $item['qty'];
						break;
					case 'product_total' :
						$field_value = number_format( $item['line_total'] + $item['line_tax'], wc_get_price_decimals(), '.', '' );
						break;
					case 'product_price':
						$field_value = number_format( $item['line_total'] + $item['line_tax'], wc_get_price_decimals(), '.', '' ) / max( intval( $item['qty'] ), 1 );
						break;
					case 'order_used_point' :
						if ( $item_index == 0 ) {
							$field_value = $this->get_order_used_point( $order );
						} else {
							$field_value = 0;
						}
						break;
					case 'order_discount_price' :
						if ( $item_index == 0 ) {
							$field_value = $order->get_total_discount();
						} else {
							$field_value = 0;
						}
						break;
					case 'order_shipping_price' :
						if ( $item_index == 0 ) {
							$shipping_price = $order->get_shipping_total();
							if ( $order->get_meta( '_order_shipping_iv' ) ) {
								$shipping_price += $order->get_meta( '_order_shipping_iv' );
							}
							$field_value = apply_filters( 'msex_order_shipping_price', $shipping_price, $order );
						} else {
							$field_value = 0;
						}
						break;
					case 'order_refunded_price' :
						if ( $item_index == 0 ) {
							$field_value = $order->get_total_refunded();
						} else {
							$field_value = 0;
						}
						break;
					case 'order_total' :
						if ( $item_index == 0 ) {
							$field_value = apply_filters( 'msex_get_order_total', $order->get_total() - $order->get_total_refunded(), $order, $item_id, $item_index, $item );
						} else {
							$field_value = 0;
						}
						break;
					case 'payment_method' :
						$field_value = msex_get_object_property( $order, 'payment_method_title' );
						break;
					case 'shipping_method' :
						$field_value = apply_filters( 'msex_order_shipping_method', msex_get_object_property( $order, 'shipping_method' ), $order );
						break;
					case 'shipping_name' :
						$field_value = msex_remove_emoji( $customer['shipping_name'] );
						break;
					case 'shipping_country' :
						$field_value = $this->get_country_name( $order->get_shipping_country() );
						break;
					case 'shipping_phone':
						$field_value = $customer['shipping_phone'];
						break;
					case 'shipping_postcode':
						$field_value = $customer['shipping_postcode'];
						break;
					case 'shipping_address' :
						$field_value = msex_remove_emoji( $customer['shipping_address_1'] . ' ' . $customer['shipping_address_2'] );
						break;
					case 'order_note' :
						$field_value = msex_remove_emoji( $order->get_customer_note() );
						break;
					case 'used_coupon' :
						$field_value = $this->get_used_coupon( $order );
						break;
					case 'file_id' :
					case 'file_name' :
					case 'download_count' :
					case 'downloads_remaining' :
					case 'access_expires' :
						$field_value = msex_get( $download, $field_type, '' );
						break;
					case 'order_item_id' :
						$field_value = $item_id;
						break;
					case 'additional_charge' :
						$field_value = apply_filters( 'msex_get_additional_charge', 0, $order );
						break;
					case 'partial_refund' :
						$field_value = apply_filters( 'msex_get_partial_refund', 0, $order );
						break;
					case 'order_meta' :
						if ( $order && in_array( $meta_key, array( 'transaction_id' ) ) ) {
							$method      = 'get_' . $meta_key;
							$field_value = $order->$method();
						} else {
							$field_value = msex_get_meta( $order, $meta_key );
						}
						$field_type = $meta_key;
						break;
					case 'order_item_meta' :
						$field_value = msex_get_meta( $item, $meta_key );
						$field_type  = $meta_key;
						break;
					case 'product_meta' :
						$field_value = msex_get_meta( $product, $meta_key );
						$field_type  = $meta_key;
						break;
                    case 'categories' :
                        $field_value = $product ? self::get_categories( $product ) : '';
                        break;
					case 'product_tag' :
						if ( $product && in_array( $product->get_type(), array( 'variation', 'subscription_variation' ) ) ) {
							$field_value = wp_strip_all_tags( wc_get_product_tag_list( $product->get_parent_id(), ',' ) );
						} else {
							$field_value = wp_strip_all_tags( wc_get_product_tag_list( $product_id, ',' ) );
						}
						break;
					case 'subscription_meta' :
						$field_value = self::get_subscription_meta( $order, $meta_key );
						$field_type  = $meta_key;
						break;
					case 'product_attribute' :
						$field_value = self::get_product_attributes( $meta_key, $product, $item );
						$field_type  = $meta_key;
						break;
					case 'user_meta' :
						$user = $order->get_user();
						if ( $user ) {
							if ( in_array( $meta_key, array( 'ID', 'user_login', 'user_nicename', 'user_email', 'user_registered', 'display_name' ) ) ) {
								$field_value = $user->$meta_key;
							} else {
								$field_value = get_user_meta( $user->ID, $meta_key, true );
							}
						}
						$field_type = $meta_key;
						break;
					case 'user_roles' :
						$user_id = $order->get_customer_id();
						if ( ! empty( $user_id ) ) {
							$user        = get_userdata( $user_id );
							$user_roles  = $user->roles;
							$field_value = implode( ',', $user_roles );
						} else {
							$field_value = 'guest';
						}
						break;
					case 'coupons' :
						$field_value = implode( ', ', msex_get_coupon_codes( $order ) );
						break;
					case 'custom' :
						$field_type = $meta_key;
						break;
					case 'text' :
						$field_type  = $field_label;
						$field_value = $meta_key;
						break;
					default :
						$field_type  = $meta_key;
						$field_value = apply_filters( 'msex_export_order_field_value', $field_value, $field, $order, $item, $item_index, $this );
						break;
				}

				if ( 'csv' == $this->get_download_type() && ! in_array( $field_type, $this->get_numeric_fields() ) ) {
					$field_value = '="' . $field_value . '"';
				}

				$row_data = array_merge( $row_data, apply_filters( 'msex_export_order_field_value_array', array( $field_value ), $field, $order, $item, $item_index, $this ) );
			}

			return array_merge( $row_data, apply_filters( 'msex_export_order_row', array(), $order, $item, $item_index, $this ) );
		}
		public function get_data( $order_ids ) {
			$order_data = array();
			foreach ( $order_ids as $order_id ) {
				$order = wc_get_order( $order_id );

				if ( $order ) {
					$idx = 0;
					foreach ( $order->get_items() as $item_id => $item ) {
						if ( apply_filters( 'msex_process_export_order_item_' . $this->get_slug(), true, $item, $order ) ) {
							$order_row = $this->get_row( $order, $item_id, $item, $idx );

							if ( has_filter( 'msex_export_order_item_' . $this->get_slug() ) ) {
								$order_data = array_merge( $order_data, apply_filters( 'msex_export_order_item_' . $this->get_slug(), array( $order_row ), $order, $item_id, $item, $idx, $this ) );
							} else {
								$order_data = array_merge( $order_data, apply_filters( 'msex_export_order_item_row', array( $order_row ), $order, $item_id, $item, $idx, $this ) );
							}

							$idx++;

							if ( ! $this->is_export_all_order_items() ) {
								break;
							}
						}
					}
				}
			}

			return $order_data;
		}
		public function get_download_info( $order, $item ) {
			global $wpdb;
			if ( ! empty( $item ) ) {

				//주문의 포함된 상품정보 리스트에 추가
				$item_list = $order->get_items();

				//상품 아이디 또는 옵션 상품 아이디 확인
				$product_id = empty( $item_list[ $item ]['variation_id'] ) ? $item_list[ $item ]['product_id'] : $item_list[ $item ]['variation_id'];
				$order_id   = msex_get_object_property( $order, 'id' );
				$_product   = wc_get_product( $product_id );

				//상품이 존재하는지 다운로드가 가능한지 확인
				if ( $_product && $_product->exists() && $_product->is_downloadable() ) {

					if ( version_compare( WOOCOMMERCE_VERSION, '2.7.0', '>=' ) ) {
						$p_type = $_product->get_type();
						$p_id   = $order->get_id();
					} else {
						$p_type = $_product->product_type;
						$p_id   = $order_id;
					}

					//다운로드 권한 확인
					if ( $p_type == 'simple' ) {
						$query                = "
                    SELECT * FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions
                    WHERE order_id = %d AND product_id = %d ORDER BY product_id
                    ";
						$download_permissions = $wpdb->get_results( $wpdb->prepare( $query, $p_id, $item_list[ $item ]['product_id'] ) );
					} else if ( $p_type == 'variation' || $p_type == 'variable' ) {
						$download_permissions = $wpdb->get_results( $wpdb->prepare( "
                    SELECT * FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions
                    WHERE order_id = %d AND product_id = %d ORDER BY product_id
                    ", $order_id, $item_list[ $item ]['variation_id'] ) );
					} else {
						$download_permissions = $wpdb->get_results( $wpdb->prepare( "
                    SELECT * FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions
                    WHERE order_id = %d AND product_id = %d ORDER BY product_id
                    ", $order_id, $item_list[ $item ]['product_id'] ) );
					}

					//상품정보에서 파일 정보 가져옴
					$downloads = $_product->get_files();

					//파일 다운로드 가능한 것이 있으면 루프
					foreach ( array_keys( $downloads ) as $download_id ) {

						//파일 다운로드 정보 배열에 저장
						if ( ! empty( $download_permissions ) && count( $download_permissions ) > 0 ) {
							$result = array(
								'file_id'             => $download_permissions[0]->permission_id,
								'filename'            => $downloads[ $download_id ]['name'],
								'fileurl'             => $downloads[ $download_id ]['file'],
								'download_count'      => empty( $download_permissions[0]->download_count ) ? '0' : $download_permissions[0]->download_count,
								//다운로드 횟수
								'downloads_remaining' => empty( $download_permissions[0]->downloads_remaining ) ? '무제한' : $download_permissions[0]->downloads_remaining,
								//남은횟수
								'access_expires'      => empty( $download_permissions[0]->access_expires ) ? '무제한' : $download_permissions[0]->access_expires,
								//만료일
							);
						} else {
							$result = array(
								'file_id'             => '',
								'filename'            => '',
								'fileurl'             => '',
								'download_count'      => '',              //다운로드 횟수
								'downloads_remaining' => '',            //남은횟수
								'access_expires'      => '',              //만료일
							);
						}

						//결과 확인후 리턴
						if ( ! empty( $result ) && count( $result ) > 0 ) {
							return $result;
						} else {
							return '';
						}
					} //end foreach

				} else {
					return '';
				}

			} else {
				//$item 항목이 없는 경우 공백 리턴
				return '';
			}

			return '';
		}

	}
}