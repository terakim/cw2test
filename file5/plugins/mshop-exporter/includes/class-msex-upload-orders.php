<?php


//소스에 URL로 직접 접근 방지
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MSEX_Upload_Orders' ) ) {

	class MSEX_Upload_Orders {

		protected static function get_upload_dir() {
			$upload_dir      = wp_upload_dir();
			$pafw_upload_dir = $upload_dir['basedir'] . '/msex/';
			if ( ! file_exists( $pafw_upload_dir ) ) {
				wp_mkdir_p( $pafw_upload_dir );
			}

			return $pafw_upload_dir;
		}

		static function move_upload_files() {
			$files = array();

			if ( isset( $_FILES ) ) {
				foreach ( $_FILES as $key => $file ) {
					$destination = self::get_upload_dir() . basename( urlencode( $file['name'] ) );

					if ( move_uploaded_file( $file['tmp_name'], $destination ) ) {
						$files[] = array(
							'field_key' => explode( '#', $key )[0],
							'filename'  => $destination
						);
					} else {
						throw new Exception( __( '파일 업로드중 오류가 발생했습니다.', 'mshop-exporter' ) );
					}
				}
			}

			return $files;
		}
		static function parse_csv( $filename ) {
			$order_infos = array();

			require_once( MSEX()->plugin_path() . '/lib/csv/class-readcsv.php' );

			// Loop through the file lines
			$file_handle = fopen( $filename, 'r' );

			if ( 'yes' == get_option( 'msex_convert_encoding', 'no' ) ) {
				$file_contents = file_get_contents( $filename );

				$file_contents = msex_maybe_convert_to_utf8( $file_contents );

				file_put_contents( $filename, $file_contents );
			}

			$csv_reader = new ReadCSV( $file_handle, ',', "\xEF\xBB\xBF" ); // Skip any UTF-8 byte order mark.

			$rownum         = 1;
			$column_headers = array();
			while ( ( $line = $csv_reader->get_row() ) !== null ) {

				if ( empty( $line ) ) {
					if ( 1 == $rownum ) {
						throw new Exception( __( 'CSV 파일에 컬럼 정보가 없습니다.', 'mshop-exporter' ) );
						break;
					} else {
						foreach ( $line as $ckey => $column ) {
							$column_headers[ $ckey ] = $column;
						}
						continue;
					}
				}

				if ( 1 == $rownum ) {
					$rownum ++;
					$column_headers = $line;
					continue;
				}

				foreach ( $line as $ckey => $column ) {
					$line_data[ $column_headers[ $ckey ] ] = $column;
				}

				if ( empty( $line_data['product_id'] ) ) {
					throw new Exception( sprintf( __( '[%d행] 상품 번호가 없습니다.', 'mshop-exporter' ), $rownum ) );
				}

				$product = wc_get_product( $line_data['product_id'] );

				if ( ! $product ) {
					throw new Exception( sprintf( __( '[%d행] #%d : 올바르지 않은 상품 번호입니다.', 'mshop-exporter' ), $rownum, $line_data['product_id'] ) );
				}

				$order_infos[] = $line_data;

				$rownum ++;
			}

			fclose( $file_handle );

			return $order_infos;
		}
		static function process_csv() {
			try {
				$files = self::move_upload_files();

				$order_infos = self::parse_csv( $files[0]['filename'] );

				if ( empty( $order_infos ) ) {
					throw new Exception( __( '주문 정보가 없습니다.', 'mshop-exporter' ) );
				}

				ob_start();

				include( 'views/upload-orders.php' );

				$data = ob_get_clean();

				wp_send_json_success( $data );

			} catch ( Exception $e ) {
				wp_send_json_error( $e->getMessage() );
			}
		}

		static function woocommerce_package_rates( $rates, $package ) {
			return array();
		}
		public static function woocommerce_add_order_item_meta( $item_id, $values, $cart_item_key ) {
			$order_item_meta = msex_get( $values, 'msex_order_item_meta', array() );

			foreach ( $order_item_meta as $key => $value ) {
				wc_add_order_item_meta( $item_id, $key, $value );
			}
		}
		static function create_order() {
			define( 'WOOCOMMERCE_CART', true );
			add_action( 'woocommerce_add_order_item_meta', array( __CLASS__, 'woocommerce_add_order_item_meta' ), 10, 3 );

			$order_data = $_REQUEST['order_data'];

			WC()->cart->empty_cart( true );


			$product = wc_get_product( $order_data['product_info']['id'] );
			if ( ! $product ) {
				wp_send_json_error( '상품 정보를 찾을 수 없습니다.' );
			}

			if ( $product->is_type( 'variation' ) ) {
				if ( version_compare( WOOCOMMERCE_VERSION, '3.0.0', '>=' ) ) {
					$product_id   = $product->get_parent_id();
					$variation_id = $product->get_id();
				} else {
					$product_id   = $product->get_id();
					$variation_id = $product->variation_id;
				}
			} else if ( $product->is_type( 'variable' ) ) {
				$product_id   = $product->get_id();
				$variation_id = '';
				$available_variations = $product->get_available_variations();

				foreach ( $available_variations as $available_variation ) {
					$found = true;

					foreach ( $order_data['product_info']['attributes'] as $key => $attribute ) {
						$attribute_key = 'attribute_' . $key;

						if ( '' != $available_variation['attributes'][ $attribute_key ] && $attribute['slug'] != $available_variation['attributes'][ $attribute_key ] ) {
							$found = false;
							break;
						}
					}

					if ( $found ) {
						$variation_id = $available_variation['variation_id'];
						break;
					}
				}
			} else {
				$product_id   = $product->get_id();
				$variation_id = '';
			}

			$variations = array();

			foreach ( $order_data['product_info']['attributes'] as $key => $attribute ) {
				$variations[ $key ] = $attribute['slug'];
			}

			if ( 'free-shipping' == $order_data['shipping_method'] ) {
				add_filter( 'woocommerce_package_rates', array( __CLASS__, '::woocommerce_package_rates', 10, 2 ) );
			}

			WC()->cart->add_to_cart( $product_id, $order_data['product_info']['qty'], $variation_id, $variations, array(
				'msex_order_item_meta' => $order_data['product_info']['item_meta'],
				'price'                => $order_data['product_info']['price']
			) );

			foreach ( WC()->cart->cart_contents as &$cart_item ) {
				if ( ! empty( $cart_item['price'] ) ) {
					msex_set_object_property( $cart_item['data'], 'price', $cart_item['price'] );
				}
			}

			if ( version_compare( WOOCOMMERCE_VERSION, '3.0.0', '<' ) ) {
				WC()->cart->calculate_totals();
			}

			if ( 'free-shipping' == $order_data['shipping_method'] ) {
				remove_filter( 'woocommerce_package_rates', array( __CLASS__, '::woocommerce_package_rates', 10 ) );
			}
			$data                              = array();
			$data['billing_first_name']        = $order_data['billing']['billing_first_name'];
			$data['billing_phone']             = $order_data['billing']['billing_phone'];
			$data['billing_email']             = $order_data['billing']['billing_email'];
			$data['ship_to_different_address'] = true;
			$data['shipping_country']          = $order_data['shipping']['shipping_country'];
			$data['shipping_first_name']       = $order_data['shipping']['shipping_first_name'];
			$data['shipping_phone']            = $order_data['shipping']['shipping_phone'];
			$data['shipping_postcode']         = $order_data['shipping']['shipping_postcode'];
			$data['shipping_address_1']        = $order_data['shipping']['shipping_address'];
			$data['shipping_address_2']        = '';
			$data['order_comments']            = $order_data['order_comments'];

			if ( version_compare( WOOCOMMERCE_VERSION, '3.0.0', '>=' ) ) {
				$order_id = WC()->checkout()->create_order( $data );
			} else {
				WC()->checkout()->posted = $data;

				$order_id = WC()->checkout()->create_order();
			}

			$order = wc_get_order( $order_id );
			if ( ! empty( $order_data['billing']['id'] ) ) {
				$order->set_customer_id( $order_data['billing']['id'] );
			}

			if ( ! empty( $order_data['discount'] ) ) {
				if ( version_compare( WOOCOMMERCE_VERSION, '3.0.0', '>=' ) ) {
					$item = new WC_Order_Item_Fee();
					$item->set_props( array(
						'name'      => __( '할인', 'mshop-exporter' ),
						'tax_class' => 0,
						'amount'    => - 1 * $order_data['discount'],
						'total'     => - 1 * $order_data['discount'],
						'total_tax' => 0,
						'taxes'     => array(
							'total' => array(),
						),
					) );

					// Add item to order and save.
					$order->add_item( $item );
				} else {
					$fee            = new stdClass();
					$fee->name      = __( '할인', 'mshop-exporter' );
					$fee->tax_class = '';
					$fee->taxable   = 0;
					$fee->amount    = - 1 * $order_data['discount'];
					$fee->tax       = '';
					$fee->tax_data  = array();

					$order->add_fee( $fee );
				}

				$order->calculate_totals();
			}

			foreach ( $order_data['order_meta'] as $key => $value ) {
				msex_update_meta_data( $order, $key, $value );
			}

			msex_update_meta_data( $order, '_shipping_email', $order_data['shipping']['shipping_email'] );
			msex_update_meta_data( $order, '_mshop_shipping_address-postnum', $order_data['shipping']['shipping_postcode'] );
			msex_update_meta_data( $order, '_mshop_shipping_address-addr1', $order_data['shipping']['shipping_address'] );
			msex_update_meta_data( $order, '_mshop_shipping_address-addr2', '' );

			if ( ! empty( $order_data['order_note'] ) ) {
				$order->add_order_note( $order_data['order_note'] );
			}

			$order->add_order_note( __( '엠샵 주문 업로드 기능으로 성성된 주문입니다.', 'mshop-exporter' ) );
			$order->update_status( $order_data['order_status'] );

			remove_action( 'woocommerce_add_order_item_meta', array( __CLASS__, 'woocommerce_add_order_item_meta' ), 10 );

			return $order;
		}
	}

}