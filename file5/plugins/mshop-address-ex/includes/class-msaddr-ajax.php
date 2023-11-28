<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class MSADDR_Ajax {
	public static function init() {
		self::add_ajax_events();
	}
	public static function add_ajax_events() {

		$ajax_events = array(
			'update_address'      => true,
			'upload_file'         => true,
			'delete_address_item' => false,
			'delete_destination'  => false,
			'save_destination'    => false,
			'load_destinations'   => false,
			'search_destinations' => false,
		);

		if ( is_admin() ) {
			$ajax_events = array_merge( $ajax_events, array(
				'update_settings'                => false,
				'reset_checkout_fields'          => false,
				'target_search'                  => false,
				'set_default_address'            => false,
				'update_checkout_field_settings' => false,
			) );
		}

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_' . msaddr_ajax_command( $ajax_event ), array( __CLASS__, $ajax_event ) );

			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_' . msaddr_ajax_command( $ajax_event ), array( __CLASS__, $ajax_event ) );
			}
		}
	}

	public static function reset_checkout_fields() {
		if ( current_user_can( 'manage_woocommerce' ) ) {
			delete_option( 'msaddr_billing_fields' );
			delete_option( 'msaddr_shipping_fields' );
			delete_option( 'msaddr_order_fields' );
			wp_send_json_success( array( 'message' => __( '체크아웃 필드 설정이 초기화되었습니다.', 'mshop-address-ex' ), 'reload' => true ) );
		}

		wp_send_json_error();
	}
	public static function delete_address_item() {
		check_ajax_referer( 'delete_address_item' );

		if ( ! is_user_logged_in() || empty( $_REQUEST['key'] ) ) {
			wp_send_json_error( __( '잘못된 요청입니다.', 'mshop-address-ex' ) );
		}

		$key = $_REQUEST['key'];

		$address_book = get_user_meta( get_current_user_id(), '_msaddr_shipping_history', true );
		unset( $address_book[ $key ] );
		update_user_meta( get_current_user_id(), '_msaddr_shipping_history', $address_book );

		wp_send_json_success();
	}

	public static function update_settings() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

		MSADDR_Settings::update_settings();
	}

	public static function update_checkout_field_settings() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

		MSADDR_Settings_Checkout_Fields::update_settings();
	}

	public static function update_address() {

		if ( ! wp_verify_nonce( $_REQUEST['_ajax_nonce'], 'update_address' ) ) {
			wp_send_json_error( '잘못된 요청입니다.' );
		}

		check_ajax_referer( 'update_address' );

		$order_id = $_REQUEST['order_id'];
		$order    = wc_get_order( $order_id );

		parse_str( $_REQUEST['params'], $params );

		foreach ( $params as $key => $value ) {
			if ( 'order_comments' == $key ) {
				$key = 'customer_note';
			}

			msaddr_update_checkout_field_value( $order, $key, $value );
		}

		$order->save();

		if ( 'billing_only' == get_option( 'woocommerce_ship_to_destination' ) ) {
			foreach ( $params as $key => $value ) {
				if ( false !== strpos( $key, 'shipping' ) ) {
					$params[ str_replace( 'shipping', 'billing', $key ) ] = $value;
				}
			}
		}

		$_POST = array_merge( $_POST, $params );

		$_POST['ship_to_different_address'] = true;

		do_action( 'msaddr_before_update_address', $order_id, $_POST );

		$order = wc_get_order( $order_id );

		MSADDR_Checkout::checkout_update_order_meta( $order_id, $params, $order );

		if ( 'billing_only' == get_option( 'woocommerce_ship_to_destination' ) && msaddr_process_billing() ) {
			msaddr_update_checkout_field_value( $order, 'billing_first_name_kr', $_POST['billing_first_name_kr'] );
			msaddr_update_checkout_field_value( $order, 'billing_email_kr', $_POST['billing_email'] );
			msaddr_update_checkout_field_value( $order, 'billing_phone_kr', $_POST['billing_phone'] );
			msaddr_update_checkout_field_value( $order, 'billing_first_name', $_POST['billing_first_name_kr'] );
			msaddr_update_checkout_field_value( $order, 'billing_email', $_POST['billing_email'] );
			msaddr_update_checkout_field_value( $order, 'billing_phone', $_POST['billing_phone'] );
		}

		if ( msaddr_process_shipping() ) {
			msaddr_update_checkout_field_value( $order, 'shipping_first_name', $_POST['shipping_first_name_kr'] );
		}

		if ( is_callable( array( $order, 'save' ) ) ) {
			$order->save();
		}

		do_action( 'msaddr_after_update_address', $order_id, $_POST );

		wp_send_json_success();
	}
	static function make_taxonomy_tree( $taxonomy, $args, $depth = 0, $parent = 0, $paths = array() ) {
		$results = array();

		$args['parent'] = $parent;
		$terms          = get_terms( $taxonomy, $args );

		foreach ( $terms as $term ) {
			$current_paths = array_merge( $paths, array( $term->name ) );
			$results[]     = array(
				"name"  => '<span class="tree-indicator-desc">' . implode( '-', $current_paths ) . '</span><span class="tree-indicator" style="margin-left: ' . ( $depth * 8 ) . 'px;">' . $term->name . '</span>',
				"value" => $term->term_id
			);

			$results = array_merge( $results, self::make_taxonomy_tree( $taxonomy, $args, $depth + 1, $term->term_id, $current_paths ) );
		}

		return $results;
	}
	static function target_search_category( $depth = 0, $parent = 0 ) {
		$args = array();

		if ( ! empty( $_REQUEST['args'] ) ) {
			$args['name__like'] = $_REQUEST['args'];
		}

		$results = self::make_taxonomy_tree( 'product_cat', $args );

		$respose = array(
			'success' => true,
			'results' => $results
		);

		echo json_encode( $respose );
		die();
	}
	static function target_search_product_posts_title_like( $where, &$wp_query ) {
		global $wpdb;
		if ( $posts_title = $wp_query->get( 'posts_title' ) ) {
			$where .= ' AND ' . $wpdb->posts . '.post_title LIKE "%' . $posts_title . '%"';
		}

		return $where;
	}
	static function target_search_product() {
		$keyword = ! empty( $_REQUEST['args'] ) ? $_REQUEST['args'] : '';

		add_filter( 'posts_where', array( __CLASS__, 'target_search_product_posts_title_like' ), 10, 2 );
		$args = array(
			'post_type'      => 'product',
			'posts_title'    => $keyword,
			'post_status'    => 'publish',
			'posts_per_page' => - 1
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

	public static function target_search() {
		if ( ! empty( $_REQUEST['type'] ) ) {
			$type = $_REQUEST['type'];

			switch ( $type ) {
				case 'product' :
				case 'product-category' :
					self::target_search_product();
					break;
				case 'category' :
					self::target_search_category();
					break;
				default:
					die();
					break;
			}
		}
	}
	public static function set_default_address() {
		check_ajax_referer( 'msaddr-diy-checkout' );

		MSADDR_DIY_Checkout::set_default_destination( $_POST['key'], msaddr_get( $_POST, 'address_type', 'billing' ) );

		wp_send_json_success();
	}
	public static function upload_file() {
		check_ajax_referer( 'msaddr-upload-file' );

		MSADDR_File::upload_temp_file();
	}
	public static function delete_destination() {
		check_ajax_referer( 'msaddr-diy-checkout' );

		MSADDR_DIY_Checkout::delete_destination( $_POST['key'] );

		wp_send_json_success();
	}
	public static function save_destination() {
		check_ajax_referer( 'msaddr-diy-checkout' );

		$args = array();

		parse_str( $_POST['params'], $args );

		$address_type = msaddr_get( $_POST, 'address_type', 'billing' );

		$match_key = array(
			$address_type . '_first_name_kr'              => $address_type . '_first_name',
			'mshop_' . $address_type . '_address-postnum' => $address_type . '_postcode',
			'mshop_' . $address_type . '_address-addr1'   => $address_type . '_address_1',
			'mshop_' . $address_type . '_address-addr2'   => $address_type . '_address_2',
			$address_type . '_email_kr'                   => $address_type . '_email',
			$address_type . '_phone_kr'                   => $address_type . '_phone',
			$address_type . '_email'                      => $address_type . '_email_kr',
			$address_type . '_phone'                      => $address_type . '_phone_kr'
		);

		foreach ( $match_key as $src_key => $destination_key ) {
			if ( ! empty( $args[ $src_key ] ) ) {
				$args[ $destination_key ] = $args[ $src_key ];
			}
		}

		MSADDR_DIY_Checkout::update_destination( $args, $address_type );

		wp_send_json_success();
	}
	static function load_destinations() {
		check_ajax_referer( 'msaddr-diy-checkout' );

		$page          = msaddr_get( $_POST, 'page', 1 );
		$keyword       = msaddr_get( $_POST, 'keyword', '' );
		$template      = msaddr_get( $_POST, 'template', 'type-b' );
		$template_type = msaddr_get( $_POST, 'template_type', '' );
		$address_type  = msaddr_get( $_POST, 'address_type', 'billing' );

		$destinations     = MSADDR_DIY_Checkout::get_shipping_destinations( $page, $keyword, $address_type );
		$address_per_page = apply_filters( 'msaddr_address_per_page', 5 );

		ob_start();

		if ( 'address-book' == $template_type ) {
			wc_get_template( "myaccount/address-book-fragment.php", array( 'destinations' => $destinations, 'address_type' => $address_type ), '', MSADDR()->template_path() );
		} else {
			wc_get_template( "pafw-diy-checkout/{$address_type}-fields/{$template}-fragment.php", array( 'destinations' => $destinations ), '', PAFW_DIY_CHECKOUT()->template_path() );
		}

		$fragment = ob_get_clean();

		wp_send_json_success( array(
			'total'        => ceil( $destinations['total'] / $address_per_page ),
			'page'         => $destinations['page'],
			'keyword'      => $destinations['keyword'],
			'address_type' => $destinations['address_type'],
			'fragment'     => $fragment
		) );
	}
	static function search_destinations() {
		check_ajax_referer( 'msaddr-diy-checkout' );

		$page          = msaddr_get( $_POST, 'page', 1 );
		$keyword       = msaddr_get( $_POST, 'keyword', '' );
		$template      = msaddr_get( $_POST, 'template', 'type-b' );
		$template_type = msaddr_get( $_POST, 'template_type', '' );
		$address_type  = msaddr_get( $_POST, 'address_type', 'billing' );

		$destinations     = MSADDR_DIY_Checkout::get_shipping_destinations( $page, $keyword, $address_type );
		$address_per_page = apply_filters( 'msaddr_address_per_page', 5 );

		ob_start();

		if ( 'address-book' == $template_type ) {
			wc_get_template( "myaccount/address-book-fragment.php", array( 'destinations' => $destinations, 'address_type' => $address_type ), '', MSADDR()->template_path() );
		} else {
			wc_get_template( "pafw-diy-checkout/{$address_type}-fields/{$template}-fragment.php", array( 'destinations' => $destinations ), '', PAFW_DIY_CHECKOUT()->template_path() );
		}

		$fragment = ob_get_clean();

		wp_send_json_success( array(
			'total'        => ceil( $destinations['total'] / $address_per_page ),
			'page'         => $destinations['page'],
			'keyword'      => $destinations['keyword'],
			'address_type' => $destinations['address_type'],
			'fragment'     => $fragment
		) );
	}
}

MSADDR_Ajax::init();
