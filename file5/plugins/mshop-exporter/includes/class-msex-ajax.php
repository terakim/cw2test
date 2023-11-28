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

class MSEX_Ajax {
	static $slug;

	static $posts_per_page;

	static $tmp_file_name = 'mshop-exporter.tmp.';

	public static function init() {
		self::$slug = MSEX()->slug();
		self::add_ajax_events();
	}

	public static function add_ajax_events() {
		$ajax_events = array();

		if ( is_admin() ) {
			$ajax_events = array_merge( $ajax_events, array(
				'search_product'                    => false,
				'update_settings'                   => false,
				'update_product_field_settings'     => false,
				'order_excel'                       => false,
				'file_download'                     => false,
				'get_total_count'                   => false,
				'generate_page_data'                => false,
				'download_file'                     => false,
				'upload_orders'                     => false,
				'create_orders'                     => false,
				'reset_sheet_fields'                => false,
				'upload_sheets'                     => false,
				'register_sheets'                   => false,
				'delete_sheet_info'                 => false,
				'update_sheet_info'                 => false,
                'delete_sheet_by_order_item'        => false,
                'update_sheet_by_order_item'        => false,
				'get_product_list'                  => false,
				'update_product'                    => false,
				'bulk_update_products'              => false,
				'do_bulk_update_product'            => false,
			) );
		}

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_' . msex_ajax_command( $ajax_event ), array( __CLASS__, $ajax_event ) );
			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_' . msex_ajax_command( $ajax_event ), array( __CLASS__, $ajax_event ) );
			}
		}
	}

	public static function get_total_count() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

		try {
			$total_count = MSEX_Exporter::get_total_count();
			wp_send_json_success( array( 'total_count' => $total_count ) );
		} catch ( Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}
	}
	public static function generate_page_data() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

		try {
			$ids = MSEX_Exporter::generate_page_data();
			wp_send_json_success( $ids );
		} catch ( Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}
	}

	public static function download_file() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

		try {
			wp_send_json_success( array( 'download_url' => MSEX_Exporter::get_download_url() ) );
		} catch ( Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}
	}

	public static function update_settings() {
		MSEX_Settings_Sheet::update_settings();
	}

	public static function update_product_field_settings() {
        MSEX_Settings_Product_Fields::update_settings();
	}

	public static function upload_orders() {
		check_ajax_referer( 'mshop-exporter' );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die( - 1 );
		}

		MSEX_Upload_Orders::process_csv();

		wp_send_json_success();
	}

	public static function create_orders() {
		try {
			MSEX_Upload_Orders::create_order();
			wp_send_json_success();
		} catch ( Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}
	}

	static function reset_sheet_fields() {
		if ( current_user_can( 'manage_woocommerce' ) ) {
			delete_option( 'msex_sheet_fields' );
			wp_send_json_success( array( 'message' => __( 'CSV 필드 설정이 초기화되었습니다.', 'mshop-exporter' ), 'reload' => true ) );
		}

		wp_send_json_error();
	}

	public static function upload_sheets() {
		check_ajax_referer( 'mshop-exporter' );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die( - 1 );
		}

		MSEX_Upload_Sheets::process_csv();

		wp_send_json_success();
	}

	public static function register_sheets() {
		try {
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				throw new Exception( __( '권한이 없습니다.', 'mshop-exporter' ) );
			}

			MSEX_Upload_Sheets::register_sheets();
			wp_send_json_success();
		} catch ( Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}
	}
	public static function delete_sheet_info() {
		check_ajax_referer( 'mshop-exporter' );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die( - 1 );
		}

		$order = wc_get_order( $_REQUEST['order_id'] );
		if ( $order ) {
			msex_update_meta_data( $order, '_msex_dlv_code', '' );
			msex_update_meta_data( $order, '_msex_dlv_name', '' );
			msex_update_meta_data( $order, '_msex_sheet_no', '' );

			foreach ( $order->get_items() as $item_id => $item ) {
				wc_delete_order_item_meta( $item_id, '_msex_dlv_code' );
				wc_delete_order_item_meta( $item_id, '_msex_dlv_name' );
				wc_delete_order_item_meta( $item_id, '_msex_sheet_no' );
			}

			wp_send_json_success();
		} else {
			wp_send_json_error( __( '주문 정보를 찾을 수 없습니다.', 'mshop-exporter' ) );
		}

	}
	public static function update_sheet_info() {
		check_ajax_referer( 'mshop-exporter' );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die( - 1 );
		}
		$order = apply_filters( 'msex_get_order', wc_get_order( $_REQUEST['order_id'] ), $_REQUEST['order_id'] );

		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			global $woocommerce_wpml;

			$lang_code = msex_get_meta( $order, 'wpml_language', true );
			if ( ! empty( $lang_code ) ) {
				$woocommerce_wpml->emails->change_email_language( $lang_code );
			}
		}

		if ( $order ) {
			$dlv_code = $_REQUEST['dlv_code'];
			$sheet_no = $_REQUEST['sheet_no'];

			$dlv_company = msex_get_dlv_company_info( $dlv_code );
			$dlv_url     = str_replace( '{msex_sheet_no}', $sheet_no, $dlv_company['dlv_url'] );

			msex_update_meta_data( $order, '_msex_dlv_code', $dlv_code );
			msex_update_meta_data( $order, '_msex_dlv_name', $dlv_company['dlv_name'] );
			msex_update_meta_data( $order, '_msex_sheet_no', $sheet_no );
			msex_update_meta_data( $order, '_msex_register_date', current_time( 'mysql' ) );

			foreach ( $order->get_items() as $item_id => $item ) {
				wc_delete_order_item_meta( $item_id, '_msex_dlv_code' );
				wc_delete_order_item_meta( $item_id, '_msex_dlv_name' );
				wc_delete_order_item_meta( $item_id, '_msex_sheet_no' );
				wc_delete_order_item_meta( $item_id, '_msex_register_date' );

				wc_update_order_item_meta( $item_id, '_msex_dlv_code', $dlv_code );
				wc_update_order_item_meta( $item_id, '_msex_dlv_name', $dlv_company['dlv_name'] );
				wc_update_order_item_meta( $item_id, '_msex_sheet_no', $sheet_no );
				wc_update_order_item_meta( $item_id, '_msex_register_date', current_time( 'mysql' ) );
			}

			$order_status = get_option( 'msex_order_status_after_shipping', 'wc-shipping' );

			$order->add_order_note( sprintf( __( '송장정보가 등록되었습니다. [ %s : %s ],', 'mshop-exporter' ), $dlv_code, $sheet_no ) );
			$order->update_status( $order_status );

			wp_send_json_success();
		} else {
			wp_send_json_error( __( '주문 정보를 찾을 수 없습니다.', 'mshop-exporter' ) );
		}

	}
    public static function delete_sheet_by_order_item() {
        check_ajax_referer( 'mshop-exporter' );

        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            die( - 1 );
        }

        if ( ! empty( $_REQUEST['item_id'] ) ) {
            $item_id = $_REQUEST['item_id'];

            wc_delete_order_item_meta( $item_id, '_msex_dlv_code' );
            wc_delete_order_item_meta( $item_id, '_msex_dlv_name' );
            wc_delete_order_item_meta( $item_id, '_msex_sheet_no' );
            wc_delete_order_item_meta( $item_id, '_msex_register_date' );

            wp_send_json_success();
        } else {
            wp_send_json_error( __( '잘못된 요청입니다.', 'mshop-exporter' ) );
        }

    }
    public static function update_sheet_by_order_item() {
        check_ajax_referer( 'mshop-exporter' );

        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            die( - 1 );
        }

        if ( ! empty( $_REQUEST['item_id'] ) && ! empty( $_REQUEST['dlv_code'] ) && ! empty( $_REQUEST['sheet_no'] ) ) {
            $item_id  = $_REQUEST['item_id'];
            $dlv_code = $_REQUEST['dlv_code'];
            $sheet_no = $_REQUEST['sheet_no'];

            $dlv_company = msex_get_dlv_company_info( $dlv_code );
            $dlv_url     = str_replace( '{msex_sheet_no}', $sheet_no, $dlv_company['dlv_url'] );

            wc_delete_order_item_meta( $item_id, '_msex_dlv_code' );
            wc_delete_order_item_meta( $item_id, '_msex_dlv_name' );
            wc_delete_order_item_meta( $item_id, '_msex_sheet_no' );
            wc_delete_order_item_meta( $item_id, '_msex_register_date' );

            wc_update_order_item_meta( $item_id, '_msex_dlv_code', $dlv_code );
            wc_update_order_item_meta( $item_id, '_msex_dlv_name', $dlv_company['dlv_name'] );
            wc_update_order_item_meta( $item_id, '_msex_sheet_no', $sheet_no );
            wc_update_order_item_meta( $item_id, '_msex_register_date', current_time( 'mysql' ) );

            $order_id = wc_get_order_id_by_order_item_id( $item_id );
            $order    = wc_get_order( $order_id );
            $flag     = true;
            foreach ( $order->get_items() as $item_id => $item ) {
                if ( empty( wc_get_order_item_meta( $item_id, '_msex_dlv_code', true ) ) ) {
                    $flag = false;
                    break;
                }
            }

            if ( $flag ) {
                msex_update_meta_data( $order, '_msex_dlv_code', $dlv_code );
                msex_update_meta_data( $order, '_msex_dlv_name', $dlv_company['dlv_name'] );
                msex_update_meta_data( $order, '_msex_sheet_no', $sheet_no );
                msex_update_meta_data( $order, '_msex_register_date', current_time( 'mysql' ) );

                $order_status = get_option( 'msex_order_status_after_shipping', 'wc-shipping' );
                $order->update_status( $order_status, sprintf( __( '송장정보가 등록되었습니다. [ %s : %s ],', 'mshop-exporter' ), $dlv_code, $sheet_no ) );
            }

            $order->add_order_note( sprintf( __( '송장정보가 등록되었습니다. [ %s : %s : %s ],', 'mshop-exporter' ), $item_id, $dlv_code, $sheet_no ) );

            wp_send_json_success();
        } else {
            wp_send_json_error( __( '잘못된 요청입니다.', 'mshop-exporter' ) );
        }

    }

	static function get_product_list() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

		$responses = array();

		$args = array(
			'limit'    => MSEX_Settings_Products::$number_per_page,
			'page'     => ! empty( $_REQUEST['page'] ) ? $_REQUEST['page'] + 1 : 1,
			'paginate' => true,
			'orderby'  => 'ID',
			'order'    => 'DESC',
		);

		$product_types = array_filter( explode( ',', msex_get( $_REQUEST, 'product_type', '' ) ) );
		if ( ! empty( $product_types ) ) {
			$args['type'] = $product_types;
		} else {
			$args['type'] = array_keys( msex_get_product_types() );
		}

		if ( ! empty( $_REQUEST['manage_stock'] ) ) {
			$args['manage_stock'] = 'yes' == $_REQUEST['manage_stock'] ? true : false;
		}

		if ( ! empty( $_REQUEST['stock_status'] ) ) {
			$args['stock_status'] = $_REQUEST['stock_status'];
		}

		if ( ! empty( $_REQUEST['product'] ) ) {
			$search_ids  = explode( ',', $_REQUEST['product'] );
			$product_ids = explode( ',', $_REQUEST['product'] );

			foreach ( $product_ids as $product_id ) {
				$product = wc_get_product( $product_id );

				if ( in_array( $product->get_type(), array( 'variable', 'variable-subscription' ) ) ) {
					$variations = $product->get_available_variations();
					$search_ids = array_merge( $search_ids, wp_list_pluck( $variations, 'variation_id' ) );
				}
			}

			$args['include'] = $search_ids;
		}

		$results = wc_get_products( $args );

		$product_types = msex_get_product_types();

		foreach ( $results->products as $product ) {
			$responses[] = array(
				"product_id"     => $product->get_id(),
				"sku"            => $product->get_sku(),
				'type'           => $product_types[ $product->get_type() ],
				"title"          => sprintf( '<a href="%s" target="_blank">%s</a>', get_edit_post_link( $product->get_parent_id() ? $product->get_parent_id() : $product->get_id() ), $product->get_name() ),
				"regular_price"  => $product->get_regular_price(),
				"sale_price"     => $product->get_sale_price(),
				"manage_stock"   => $product->managing_stock() ? 'yes' : 'no',
				"stock_status"   => $product->get_stock_status(),
				"stock_quantity" => $product->get_stock_quantity(),
			);
		}

		wp_send_json_success( array(
			'total_count' => $results->total,
			'results'     => $responses
		) );
	}
	protected static function update_single_product( $product, $params ) {
		if ( isset( $params['regular_price'] ) ) {
			$product->set_regular_price( $params['regular_price'] );
		}

		if ( isset( $params['sale_price'] ) ) {
			if ( 0 == floatval( $params['sale_price'] ) ) {
				$params['sale_price'] = '';
			}

			$product->set_sale_price( $params['sale_price'] );
		}

		if ( isset( $params['manage_stock'] ) ) {
			$product->set_manage_stock( 'yes' == $params['manage_stock'] );
			if ( 'yes' == $params['manage_stock'] ) {
				$product->set_stock_status( $params['stock_status'] );
				if ( 'outofstock' == $params['stock_status'] ) {
					$params['stock_quantity'] = 0;
				}

				$product->set_stock_quantity( $params['stock_quantity'] );
			}
		}

		$product->save();
	}

	protected static function __update_single_product( $params ) {
		$product = wc_get_product( $params['product_id'] );

		if ( $product ) {
			$product->set_regular_price( $params['regular_price'] );
			if ( ! empty( $params['sale_price'] ) ) {
				$product->set_sale_price( $params['sale_price'] );
			}

			$product->set_manage_stock( 'yes' == $params['manage_stock'] );
			if ( 'yes' == $params['manage_stock'] ) {
				$product->set_stock_status( $params['stock_status'] );
				$product->set_stock_quantity( $params['stock_quantity'] );
			}

			$product->save();
		}
	}

	static function update_product() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

		$params = json_decode( stripslashes( $_REQUEST['params'] ), true );

		$product = wc_get_product( $params['product_id'] );
		if ( $product ) {
			self::update_single_product( $product, $params );
		}

		wp_send_json_success();
	}

	static function bulk_update_products() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

		foreach ( $_REQUEST['values']['msex_products_target'] as $value ) {
			$product = wc_get_product( $value['product_id'] );
			if ( $product ) {
				self::update_single_product( $product, $value );
			}
		}

		wp_send_json_success( array( 'message' => '상품 정보가 업데이트 되었습니다.' ) );
	}
	static function target_search_product_posts_title_like( $where, &$wp_query ) {
		global $wpdb;
		if ( $posts_title = $wp_query->get( 'posts_title' ) ) {
			$where .= ' AND ' . $wpdb->posts . '.post_title LIKE "%' . $posts_title . '%"';
		}

		return $where;
	}
	static function search_product() {
		$keyword = ! empty( $_REQUEST['args'] ) ? $_REQUEST['args'] : '';

		add_filter( 'posts_where', array( __CLASS__, 'target_search_product_posts_title_like' ), 10, 2 );
		$args = array(
			'post_type'      => 'product',
			'posts_title'    => $keyword,
			'post_status'    => 'publish',
			'posts_per_page' => 20
		);

		$query = new WP_Query( $args );

		remove_filter( 'posts_where', array( __CLASS__, 'target_search_product_posts_title_like' ) );

		$results = array();

		foreach ( $query->posts as $post ) {
			$results[] = array(
				"name"  => $post->post_title,
				"value" => $post->ID
			);
		}
		$respose = array(
			'success' => true,
			'results' => $results
		);

		echo json_encode( $respose );

		die();
	}
	protected static function get_query_params( $args, $params ) {
		$product_types = array_filter( explode( ',', msex_get( $params, 'product_type', '' ) ) );

		if ( ! empty( $product_types ) ) {
			$args['type'] = $product_types;
		} else {
			$args['type'] = array_keys( msex_get_product_types() );
		}

		if ( ! empty( $params['manage_stock'] ) ) {
			$args['manage_stock'] = 'yes' == $params['manage_stock'] ? true : false;
		}

		if ( ! empty( $params['stock_status'] ) ) {
			$args['stock_status'] = $params['stock_status'];
		}

		if ( ! empty( $params['product'] ) ) {
			$search_ids  = explode( ',', $params['product'] );
			$product_ids = explode( ',', $params['product'] );

			foreach ( $product_ids as $product_id ) {
				$product = wc_get_product( $product_id );

				if ( in_array( $product->get_type(), array( 'variable', 'variable-subscription' ) ) ) {
					$variations = $product->get_available_variations();
					$search_ids = array_merge( $search_ids, wp_list_pluck( $variations, 'variation_id' ) );
				}
			}

			$args['include'] = $search_ids;
		}

		return $args;
	}

	static function do_bulk_update_product() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

		$params       = $_REQUEST['values'];
		$options      = array();
		$price_field  = '';
		$price_action = '';
		$price_method = '';

		if ( empty( $params['bulk_action'] ) ) {
			wp_send_json_error( '일괄 설정 액션을 선택해주세요.' );
		}

		if ( 'stock' == $params['bulk_action'] ) {
			if ( empty( $params['manage_stock'] ) ) {
				wp_send_json_error( '재고관리 여부를 지정해주세요.' );
			} else if ( 'yes' == $params['manage_stock'] && empty( $params['stock_status'] ) ) {
				wp_send_json_error( '재고 상태를 지정해주세요.' );
			} else if ( 'yes' == $params['manage_stock'] && 'instock' == $params['stock_status'] && ! isset( $params['stock_quantity'] ) ) {
				wp_send_json_error( '재고 수량을 입력해주세요.' );
			}

			$options['manage_stock']   = $params['manage_stock'];
			$options['stock_status']   = $params['stock_status'];
			$options['stock_quantity'] = $params['stock_quantity'];
		} else if ( 'set_regular_price' == $params['bulk_action'] ) {
			if ( ! isset( $params['set_price'] ) ) {
				wp_send_json_error( '상품 가격을 입력해주세요.' );
			}

			$options['regular_price'] = $params['set_price'];
		} else if ( 'set_sale_price' == $params['bulk_action'] ) {
			if ( ! isset( $params['set_price'] ) ) {
				wp_send_json_error( '할인 가격을 입력해주세요.' );
			}

			$options['sale_price'] = $params['set_price'];
		} else if ( in_array( $params['bulk_action'], array( 'increase_regular_price', 'decrease_regular_price', 'increase_sale_price', 'decrease_sale_price' ) ) ) {
			if ( empty( $params['adjust_amount'] ) ) {
				wp_send_json_error( '고정금액 또는 비율을 입력하세요.' );
			}

			$price_field  = preg_replace( '/^increase_|^decrease_/', '', $params['bulk_action'] );
			$price_action = preg_replace( '/_regular_price$|_sale_price$/', '', $params['bulk_action'] );
			$price_method = 'get_' . $price_field;

			if ( false === strpos( $params['adjust_amount'], '%' ) ) {
				$options['amount'] = floatval( $params['adjust_amount'] );
			} else {
				$options['ratio'] = floatval( $params['adjust_amount'] );
			}
		}

		$args = self::get_query_params( array( 'return' => 'ids', 'limit' => - 1 ), msex_get( $params, 'searchParam' ) );

		$product_ids = wc_get_products( $args );

		foreach ( $product_ids as $product_id ) {
			$product = wc_get_product( $product_id );

			if ( 'increase' == $price_action ) {
				if ( isset( $options['amount'] ) ) {
					$options[ $price_field ] = (float) $product->$price_method() + $options['amount'];
				} else {
					$options[ $price_field ] = (float) $product->$price_method() + ( (float) $product->$price_method() * $options['ratio'] ) / 100;
				}
			} else if ( 'decrease' == $price_action ) {
				if ( isset( $options['amount'] ) ) {
					$options[ $price_field ] = (float) $product->$price_method() - $options['amount'];
				} else {
					$options[ $price_field ] = (float) $product->$price_method() - ( (float) $product->$price_method() * $options['ratio'] ) / 100;
				}
			}

			$options[ $price_field ] = round( $options[ $price_field ], wc_get_price_decimals() );

			self::update_single_product( $product, $options );
		}

		wp_send_json_success( array( 'message' => '상품 정보가 업데이트 되었습니다.' ) );
	}
}

MSEX_Ajax::init();
