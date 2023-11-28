<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class MSPS_Ajax {
	static $slug;

	public static function init() {
		if ( ! WP_DEBUG || ( WP_DEBUG && ! WP_DEBUG_DISPLAY ) ) {
			@ini_set( 'display_errors', 0 );
		}
		$GLOBALS['wpdb']->hide_errors();

		self::$slug = MSPS()->slug();

		self::add_ajax_events();
	}
	public static function add_ajax_events() {

		$ajax_events = array(
			'target_search'           => false,
			'mshop_point_search_user' => false,
			'get_logs_fragment'       => false,
		);

		if ( is_admin() ) {
			$ajax_events = array_merge( $ajax_events, array(
				'admin_point_logs'                => false,
				'get_user_point_logs'             => false,
				'update_msps_role_settings'       => false,
				'update_msps_policy_settings'     => false,
				'update_msps_extinction_settings' => false,
				'update_volatile_settings'        => false,
				'adjust_mshop_user_point'         => false,
				'adjust_volatile_point'           => false,
				'get_user_point_list'             => false,
				'batch_adjust_point'              => false,
				'export_logs'                     => false,
				'export_users'                    => false,
				'upload_point'                    => false,
				'process_point'                   => false,
				'update_order_point'              => false,
				'clear_scheduled_action'          => false,
				'batch_adjust_volatile_point'     => false,
				'reset_volatile_wallet'           => false,
				'delete_log_items'                => false,
			) );
		}

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_' . msps_ajax_command( $ajax_event ), array( __CLASS__, $ajax_event ) );

			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_' . msps_ajax_command( $ajax_event ), array( __CLASS__, $ajax_event ) );
			}
		}
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
            'post_status'    => array('publish', 'private'),
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
	static function target_search_taxonomy() {
		$args = array();

		if ( ! empty( $_REQUEST['args'] ) ) {
			$args['name__like'] = $_REQUEST['args'];
		}

		$results = self::make_taxonomy_tree( $_REQUEST['taxonomy'], $args );

		$respose = array(
			'success' => true,
			'results' => $results
		);

		echo json_encode( $respose );
		die();
	}
	static function target_search_shipping_classes() {
		$results          = array();
		$shipping_classes = WC_Shipping::instance()->get_shipping_classes();

		foreach ( $shipping_classes as $shipping_classe ) {
			$results[] = array(
				"name"  => $shipping_classe->name,
				"value" => $shipping_classe->term_id
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
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

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
				case 'shipping-class' :
					self::target_search_shipping_classes();
					break;
				case 'taxonomy' :
					self::target_search_taxonomy();
					break;
				default:
					do_action( 'msps_target_search_' . $type );
					die();
			}
		}
	}
	public static function mshop_point_search_user() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

		global $wpdb;

		$results = array();

		$keyword = isset( $_REQUEST['args'] ) ? esc_attr( $_REQUEST['args'] ) : '';

		$sql = "SELECT user.ID
				FROM {$wpdb->users} user
				WHERE
				    user.user_login like '%{$keyword}%'
				    OR user.user_nicename like '%{$keyword}%'
				    OR user.display_name like '%{$keyword}%'
				    OR user.user_email like '%{$keyword}%'
				LIMIT 20";


		$user_ids = $wpdb->get_col( $sql );

		foreach ( $user_ids as $user_id ) {
			$user      = get_user_by( 'id', $user_id );
			$results[] = array(
				"value" => $user->ID,
				"name"  => $user->data->display_name . ' ( #' . $user->ID . ' - ' . $user->data->user_email . ', ' . $user->billing_last_name . $user->billing_first_name . ')'
			);
		}

		$respose = array(
			'success' => true,
			'results' => $results
		);

		echo json_encode( $respose );

		die();
	}
	public static function adjust_mshop_user_point() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

		$current_language = apply_filters( 'msps_get_current_language', mshop_wpml_get_current_language() );

		$user   = new MSPS_User( $_REQUEST['id'] );
		$action = $_REQUEST['point_action'];
		$amount = $_REQUEST['amount'];
		$note   = $_REQUEST['note'];

		if ( 'earn' === $action ) {
			$user->earn_point( $amount, 'free_point' );
			$note = ! empty( $note ) ? $note : sprintf( __( '관리자에의해 %s포인트가 적립되었습니다.', 'mshop-point-ex' ), number_format( $amount, wc_get_price_decimals() ) );

			MSPS_Log::add_log( $_REQUEST['id'], msps_get_wallet_id( 'free_point', null, $current_language ), 'earn', 'admin', $amount, $user->get_point( array( 'free_point' ) ), 'completed', 0, $note );
		} else if ( 'deduct' == $action ) {
			$user->deduct_point( $amount, 'free_point' );
			$note = ! empty( $note ) ? $note : sprintf( __( '관리자에의해 %s포인트가 차감되었습니다.', 'mshop-point-ex' ), number_format( $amount, wc_get_price_decimals() ) );

			MSPS_Log::add_log( $_REQUEST['id'], msps_get_wallet_id( 'free_point', null, $current_language ), 'deduct', 'admin', - 1 * $amount, $user->get_point( array( 'free_point' ) ), 'completed', 0, $note );
		}

		wp_send_json_success();
	}

	static function adjust_volatile_point() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( __( '사용 권한이 없습니다.', 'mshop-point-ex' ) );
		}

		if ( empty( $_REQUEST['id'] ) || empty( $_REQUEST['point_action'] ) || empty( $_REQUEST['amount'] ) || empty( $_REQUEST['wallet_id'] ) ) {
			wp_send_json_error( __( '잘못된 요청입니다.', 'mshop-point-ex' ) );
		}

		$current_language = apply_filters( 'msps_get_current_language', mshop_wpml_get_current_language() );

		$user      = new MSPS_User( $_REQUEST['id'] );
		$action    = $_REQUEST['point_action'];
		$amount    = $_REQUEST['amount'];
		$note      = $_REQUEST['note'];
		$wallet_id = $_REQUEST['wallet_id'];

		if ( 'earn' === $action ) {
			$user->earn_point( $amount, $wallet_id );
			$note = ! empty( $note ) ? $note : sprintf( __( '관리자에의해 %s포인트가 적립되었습니다.', 'mshop-point-ex' ), number_format( $amount, wc_get_price_decimals() ) );
			MSPS_Log::add_log( $_REQUEST['id'], msps_get_wallet_id( $_REQUEST['wallet_id'], null, $current_language ), 'earn', 'admin', $amount, $user->get_point( array( $_REQUEST['wallet_id'] ) ), 'completed', 0, $note, msps_get_wallet_name( $user, $_REQUEST['wallet_id'] ) );
		} else if ( 'deduct' == $action ) {
			$user->deduct_point( $amount, $wallet_id );
			$note = ! empty( $note ) ? $note : sprintf( __( '관리자에의해 %s포인트가 차감되었습니다.', 'mshop-point-ex' ), number_format( $amount, wc_get_price_decimals() ) );
			MSPS_Log::add_log( $_REQUEST['id'], msps_get_wallet_id( $_REQUEST['wallet_id'], null, $current_language ), 'deduct', 'admin', - 1 * $amount, $user->get_point( array( $_REQUEST['wallet_id'] ) ), 'completed', 0, $note, msps_get_wallet_name( $user, $_REQUEST['wallet_id'] ) );
		}

		wp_send_json_success();
	}

	public static function get_user_point_list() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

		$results = array();

		$args = array(
			'number'      => MSPS_Settings_Manage_Point::$number_per_page,
			'count_total' => true
		);
		if ( empty( $_REQUEST['sortKey'] ) ) {
			$_REQUEST['sortKey'] = 'ID';
		}

		switch ( $_REQUEST['sortKey'] ) {
			case 'point' :
				$args['meta_key'] = '_mshop_point';
				$args['orderby']  = 'meta_value_num';
				break;
			case 'last_date' :
				$args['meta_key'] = '_mshop_last_date';
				$args['orderby']  = 'meta_value';
				break;
			default:
				$args['orderby'] = $_REQUEST['sortKey'];
				break;
		}

		if ( ! empty( $_REQUEST['role'] ) ) {
			$args['role__in'] = explode( ',', $_REQUEST['role'] );
		}

		if ( ! empty( $_REQUEST['sortOrder'] ) ) {
			$args['order'] = $_REQUEST['sortOrder'] == 'ascending' ? 'ASC' : 'DESC';
		} else {
			$args['order'] = 'DESC';
		}

		if ( ! empty( $_REQUEST['user'] ) ) {
			$args['include'] = explode( ',', $_REQUEST['user'] );
		}

		if ( ! empty( $_REQUEST['page'] ) && $_REQUEST['page'] > 0 ) {
			$args['offset'] = $_REQUEST['page'] * MSPS_Settings_Manage_Point::$number_per_page;
		}

		$user_query = new WP_User_Query( $args );

		$users_found = $user_query->get_results();

		if ( $users_found instanceof WP_User ) {
			$users_found = array( $users_found );
		}

		foreach ( $users_found as $user ) {
			$msps_user  = new MSPS_User( $user->ID );
			$free_point = $msps_user->get_point( array( 'free_point' ) );
			$edit_link  = sprintf( '<a href="%s">%s</a>(%s)', get_edit_user_link( $user->ID ), $user->data->display_name, $user->data->user_email );

			$results[] = apply_filters( 'msps_get_user_point_info', array(
				"id"        => $user->ID,
				"name"      => $edit_link,
				'last_date' => get_user_meta( $user->ID, '_mshop_last_date', true ),
				"point"     => array(
					'point'      => $free_point,
					'point_desc' => number_format( $free_point, wc_get_price_decimals() ),
					'id'         => $user->ID,
					'name'       => $user->data->display_name
				)
			), $user );
		}

		wp_send_json_success( array(
			'total_count' => $user_query->get_total(),
			'results'     => $results
		) );
	}

	public static function delete_log_items() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

		$ids = msps_get( $_REQUEST, 'ids', array() );

		if ( empty( $ids ) ) {
			wp_send_json_error( __( '삭제할 로그를 선택해주세요.', 'mshop-point-ex' ) );
		}

		MSPS_Log::delete_logs( msps_get( $_REQUEST, 'ids', array() ) );

		wp_send_json_success( __( '선택한 로그가 삭제되었습니다.', 'mshop-point-ex' ) );
	}

	public static function admin_point_logs() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

		$results = array();
		$current_language = apply_filters( 'msps_get_current_language', mshop_wpml_get_current_language() );

		$logs = MSPS_Log::get_admin_logs( msps_get( $_REQUEST, 'user' ), msps_get( $_REQUEST, 'term' ), msps_get( $_REQUEST, 'wallet_type', 'all' ), msps_get( $_REQUEST, 'sortKey' ), msps_get( $_REQUEST, 'sortOrder' ), msps_get( $_REQUEST, 'page' ), MSPS_Settings_Point_Logs::$number_per_page, $current_language );

		foreach ( $logs['results'] as $log ) {
			$user    = new MSPS_User( $log['user_id'] );
			$message = '';
			if ( empty( $log['message'] ) ) {
				switch ( $log['action'] ) {
					case 'order' :
						$order = wc_get_order( $log['object_id'] );
						if ( $order ) {
							$message = sprintf( "주문 포인트<a target='_blank' href='%s'>#%d</a>", $order->get_edit_order_url(), $order->get_id() );
						} else {
							$message = sprintf( "주문 포인트#%d", $log['object_id'] );
						}
						break;
					case 'purchase' :
						$order = wc_get_order( $log['object_id'] );
						if ( $order && $order->get_customer_id() == get_current_user_id() ) {
							$message = sprintf( "포인트 할인 <a target='_blank' href='%s'>#%d</a>", $order->get_edit_order_url(), $order->get_id() );
						} else {
							$message = sprintf( "포인트 할인 #%d", $log['object_id'] );
						}
						break;
					case 'comment' :
						$comment = get_comment( $log['object_id'] );

						$message = sprintf( __( '댓글 포인트 <a href="%s"><p class="meta"><abbr>%s</abbr></p></a>', 'mshop-point-ex' ), get_comment_link( $comment ), strip_tags( $comment->comment_content ) );

						break;
					default :
						$message = $log['object_id'];
				}
			} else {
				$message = $log['message'];
			}

			$user_name = $user->get_user_info( 'first_name' );
			if ( empty( $user_name ) ) {
				$user_name = $user->get_user_info( 'display_name' );
			}

			$wallet_name = msps_get_wallet_name( $user, $log['wallet_id'] );

			$results[] = array(
				'id'      => $log['id'],
				'date'    => $log['date'],
				'wallet'  => $wallet_name == $log['wallet_id'] && ! empty( $log['wallet_name'] ) ? $log['wallet_name'] : $wallet_name,
				'user'    => sprintf( '<a href="%s">#%d<a> %s, %s', get_edit_user_link( $user->get_user_info( 'ID' ) ), $user->get_user_info( 'ID' ), $user_name, $user->get_user_info( 'user_email' ) ),
				'amount'  => number_format( $log['amount'], wc_get_price_decimals() ),
				'balance' => number_format( $log['balance'], wc_get_price_decimals() ),
				'status'  => 'pending' == $log['status'] ? '예정' : ( 'earn' == $log['type'] ? '적립' : '차감' ),
				'desc'    => apply_filters( 'msps_point_log_description', $message, $log, 'admin' ),
			);
		}

		wp_send_json_success( array(
			'total_count' => $logs['total_count'],
			'results'     => $results
		) );
	}

	public static function get_user_point_logs() {
		if ( ! is_user_logged_in() ) {
			die();
		}

		$results = array();
		$current_language = apply_filters( 'msps_get_current_language', mshop_wpml_get_current_language() );

		$logs = MSPS_Log::get_admin_logs( get_current_user_id(), msps_get( $_REQUEST, 'term' ), msps_get( $_REQUEST, 'wallet_type', 'all' ), msps_get( $_REQUEST, 'sortKey' ), msps_get( $_REQUEST, 'sortOrder' ), msps_get( $_REQUEST, 'page' ), 10, $current_language );

		foreach ( $logs['results'] as $log ) {
			$user    = new MSPS_User( $log['user_id'] );
			$message = '';
			if ( empty( $log['message'] ) ) {
				switch ( $log['action'] ) {
					case 'order' :
						$order = wc_get_order( $log['object_id'] );
						if ( $order ) {
							$message = sprintf( "주문 포인트<a target='_blank' href='%s'>#%d</a>", $order->get_view_order_url(), $order->get_id() );
						} else {
							$message = sprintf( "주문 포인트#%d", $log['object_id'] );
						}
						break;
					case 'purchase' :
						$order = wc_get_order( $log['object_id'] );
						if ( $order && $order->get_customer_id() == get_current_user_id() ) {
							$message = sprintf( "포인트 할인 <a target='_blank' href='%s'>#%d</a>", $order->get_view_order_url(), $order->get_id() );
						} else {
							$message = sprintf( "포인트 할인 #%d", $log['object_id'] );
						}
						break;
					case 'comment' :
						$comment = get_comment( $log['object_id'] );

						$message = sprintf( __( '댓글 포인트 <a href="%s"><p class="meta"><abbr>%s</abbr></p></a>', 'mshop-point-ex' ), get_comment_link( $comment ), strip_tags( $comment->comment_content ) );

						break;
					default :
						$message = $log['object_id'];
				}
			} else {
				$message = $log['message'];
			}

			$wallet_name = msps_get_wallet_name( $user, $log['wallet_id'] );

			$results[] = array(
				'id'      => $log['id'],
				'date'    => date( 'Y-m-d', strtotime( $log['date'] ) ),
				'wallet'  => $wallet_name == $log['wallet_id'] && ! empty( $log['wallet_name'] ) ? $log['wallet_name'] : $wallet_name,
				'amount'  => number_format( $log['amount'], wc_get_price_decimals() ),
				'balance' => number_format( $log['balance'], wc_get_price_decimals() ),
				'status'  => 'pending' == $log['status'] ? '예정' : ( 'earn' == $log['type'] ? '적립' : '차감' ),
				'desc'    => apply_filters( 'msps_point_log_description', $message, $log, 'user' ),
			);
		}

		wp_send_json_success( array(
			'total_count' => $logs['total_count'],
			'results'     => $results
		) );
	}

	static function update_volatile_settings() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

		MSPS_Settings_Volatile::update_settings();
	}

	static function update_msps_role_settings() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

		MSPS_Settings_Point_Role::update_settings();
	}

	static function update_msps_policy_settings() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

		MSPS_Settings_Point::update_settings();
	}

	static function update_msps_extinction_settings() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

		MSPS_Settings_Extinction::update_settings();
	}

	public static function batch_adjust_point( $wallet_id = 'free_point' ) {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

		$wallet_id = empty( $wallet_id ) ? 'free_point' : $wallet_id;

		$users_per_page = apply_filters( 'msps_batch_adjust_user_count', 1000 );
		$current_page   = msps_get( $_REQUEST, 'page', 1 );

		$args = array(
			'fields' => 'ID',
			'order'  => 'ASC',
			'number' => $users_per_page,
			'paged'  => $current_page
		);

		if ( ! empty( $_REQUEST['role'] ) ) {
			$args['role__in'] = explode( ',', $_REQUEST['role'] );
		}

		if ( ! empty( $_REQUEST['user'] ) ) {
			$args['include'] = explode( ',', $_REQUEST['user'] );
		}

		$amount     = $_REQUEST['amount'];
		$action     = $_REQUEST['point_action'];
		$user_query = new WP_User_Query( $args );

		$user_ids = $user_query->get_results();

		$total_users = $user_query->get_total(); // How many users we have in total (beyond the current page)
		$total_page  = ceil( $total_users / $users_per_page );

		$current_language = apply_filters( 'msps_get_current_language', mshop_wpml_get_current_language() );

		foreach ( $user_ids as $user_id ) {
			$msps_user = new MSPS_User( $user_id );

			if ( 'earn' == $action ) {
				$msps_user->earn_point( $amount, $wallet_id );
				$note = ! empty( $note ) ? $note : sprintf( __( '관리자에의해 %s포인트가 적립되었습니다.', 'mshop-point-ex' ), number_format( $amount, wc_get_price_decimals() ) );
				MSPS_Log::add_log( $user_id, msps_get_wallet_id( $wallet_id, null, $current_language ), 'earn', 'admin', $amount, $msps_user->get_point( array( $wallet_id ) ), 'completed', 0, $note, msps_get_wallet_name( $msps_user, $wallet_id ) );
			} else if ( 'deduct' == $action ) {
				$msps_user->deduct_point( $amount, $wallet_id );
				$note = ! empty( $note ) ? $note : sprintf( __( '관리자에의해 %s포인트가 차감되었습니다.', 'mshop-point-ex' ), number_format( $amount, wc_get_price_decimals() ) );
				MSPS_Log::add_log( $user_id, msps_get_wallet_id( $wallet_id, null, $current_language ), 'deduct', 'admin', - 1 * $amount, $msps_user->get_point( array( $wallet_id ) ), 'completed', 0, $note, msps_get_wallet_name( $msps_user, $wallet_id ) );
			} else if ( 'set' == $action ) {
				$prev_point = $msps_user->get_point( array( $wallet_id ) );

				$note = ! empty( $note ) ? $note : sprintf( __( '관리자에의해 %s포인트로 설정되었습니다.', 'mshop-point-ex' ), number_format( $amount, wc_get_price_decimals() ) );

				if ( $prev_point > $amount ) {
					$msps_user->deduct_point( $prev_point - $amount, $wallet_id );
					MSPS_Log::add_log( $user_id, msps_get_wallet_id( $wallet_id, null, $current_language ), 'deduct', 'admin', - 1 * ( $prev_point - $amount ), $msps_user->get_point( array( $wallet_id ) ), 'completed', 0, $note, msps_get_wallet_name( $msps_user, $wallet_id ) );
				} else if ( $prev_point < $amount ) {
					$msps_user->earn_point( $amount - $prev_point, $wallet_id );
					MSPS_Log::add_log( $user_id, msps_get_wallet_id( $wallet_id, null, $current_language ), 'earn', 'admin', $amount - $prev_point, $msps_user->get_point( array( $wallet_id ) ), 'completed', 0, $note, msps_get_wallet_name( $msps_user, $wallet_id ) );
				}
			}

			unset( $msps_user );
		}

		if ( $total_page <= $current_page ) {
			wp_send_json_success( array( 'continue' => false ) );
		} else {
			wp_send_json_success( array( 'continue' => true, 'page' => $current_page + 1 ) );
		}
	}

	static function batch_adjust_volatile_point() {
		$wallet_id = $_REQUEST['wallet_id'];

		self::batch_adjust_point( $wallet_id );
	}

	public static function get_log_data() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

		$results = array();
		$current_language = apply_filters( 'msps_get_current_language', mshop_wpml_get_current_language() );

		$logs = MSPS_Log::get_admin_logs( msps_get( $_REQUEST, 'user' ), msps_get( $_REQUEST, 'term' ), msps_get( $_REQUEST, 'wallet_type', 'all' ), msps_get( $_REQUEST, 'sortKey' ), msps_get( $_REQUEST, 'sortOrder' ), 0, - 1, $current_language );

		foreach ( $logs['results'] as $log ) {
			$user    = new MSPS_User( $log['user_id'] );
			if ( empty( $log['message'] ) ) {
				switch ( $log['action'] ) {
					case 'order' :
						$message = sprintf( "주문 포인트 #%d", $log['object_id'] );
						break;
					case 'purchase' :
						$message = sprintf( "포인트 할인 #%d", $log['object_id'] );
						break;
					case 'comment' :
						$comment = get_comment( $log['object_id'] );
						$message = sprintf( __( '댓글 포인트 %s', 'mshop-point-ex' ), strip_tags( $comment->comment_content ) );
						break;
					default :
						$message = $log['object_id'];
				}
			} else {
				$message = $log['message'];
			}

			$wallet_name = msps_get_wallet_name( $user, $log['wallet_id'] );

			$results[] = array(
				'no'           => $log['id'],
				'date'         => $log['date'],
				'user_id'      => $log['user_id'],
				'user_email'   => $user->get_user_info( 'user_email' ),
				'display_name' => $user->get_user_info( 'display_name' ),
				'wallet'       => $wallet_name == $log['wallet_id'] && ! empty( $log['wallet_name'] ) ? $log['wallet_name'] : $wallet_name,
				'amount'       => number_format( $log['amount'], wc_get_price_decimals() ),
				'balance'      => number_format( $log['balance'], wc_get_price_decimals() ),
				'status'       => 'pending' == $log['status'] ? '예정' : ( 'earn' == $log['type'] ? '적립' : '차감' ),
				'desc'         => strip_tags( apply_filters( 'msps_point_log_description', $message, $log, 'download' ) ),
			);
		}

		return $results;
	}

	public static function export_logs() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

		$fileName = 'msps_logs_' . date( 'Y-m-d' ) . '.csv';

		header( 'Content-Description: File Transfer' );
		header( 'Content-Type: application/csv' );
		header( 'Content-Disposition: attachment; filename=' . $fileName );

		$file = fopen( 'php://output', 'w' );
		fputs( $file, "\xEF\xBB\xBF" );

		fputcsv( $file, array( '순번', '날짜', '아이디', '이메일', '이름', '타입', '포인트', '밸런스', '상태', '비고' ) );

		foreach ( self::get_log_data() as $log ) {
			fputcsv( $file, $log );
		}
		fclose( $file );

		exit;
	}

	public static function get_user_data() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

		$number_per_page = apply_filters( 'msps_get_user_data_number_per_page', 1000 );

		$results = array();

		$args = array(
			'number'      => $number_per_page,
			'count_total' => true
		);
		$search_params = msps_get( $_REQUEST, 'search_params', array() );

		if ( empty( $search_params['sortKey'] ) ) {
			$search_params['sortKey'] = 'ID';
		}

		switch ( $search_params['sortKey'] ) {
			case 'point' :
				$args['meta_key'] = '_mshop_point';
				$args['orderby']  = 'meta_value_num';
				break;
			case 'last_date' :
				$args['meta_key'] = '_mshop_last_date';
				$args['orderby']  = 'meta_value';
				break;
			default:
				$args['orderby'] = $search_params['sortKey'];
				break;
		}

		if ( ! empty( $search_params['role'] ) ) {
			$args['role__in'] = explode( ',', $search_params['role'] );
		}

		if ( ! empty( $search_params['sortOrder'] ) ) {
			$args['order'] = $search_params['sortOrder'] == 'ascending' ? 'ASC' : 'DESC';
		} else {
			$args['order'] = 'DESC';
		}

		if ( ! empty( $search_params['user'] ) ) {
			$args['include'] = explode( ',', $search_params['user'] );
		}

		$args['paged'] = msps_get( $_REQUEST, 'page', 1 );

		$user_query = new WP_User_Query( $args );

		$users_found = $user_query->get_results();

		$total_users = $user_query->get_total(); // How many users we have in total (beyond the current page)
		$total_page  = ceil( $total_users / $number_per_page );

		if ( $users_found instanceof WP_User ) {
			$users_found = array( $users_found );
		}

		foreach ( $users_found as $user ) {
			$msps_user = new MSPS_User( $user->ID );

			$results[] = array(
				"id"        => $user->ID,
				"login"     => $user->user_login,
				"name"      => $user->data->display_name,
				'last_date' => get_user_meta( $user->ID, '_mshop_last_date', true ),
				'point'     => $msps_user->get_point( array( 'free_point' ) )
			);
		}

		return array( 'total_page' => $total_page, 'results' => $results );

	}

	protected static function get_upload_dir() {
		$upload_dir      = wp_upload_dir();
		$pafw_upload_dir = $upload_dir['basedir'] . '/msps/';
		if ( ! file_exists( $pafw_upload_dir ) ) {
			wp_mkdir_p( $pafw_upload_dir );
		}

		return $pafw_upload_dir;
	}

	protected static function get_upload_url( $filename ) {
		$upload_dir = wp_upload_dir();

		return $upload_dir['baseurl'] . '/msps/' . $filename;
	}


	public static function export_users() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

		$current_page = msps_get( $_REQUEST, 'page', 1 );

		$fileName = self::get_upload_dir() . 'msps_point_list.csv';

		$file = fopen( $fileName, $current_page == 1 ? 'w' : 'a' );

		if ( $current_page == 1 ) {
			fputcsv( $file, array( '아이디', '로그인', '이름', '최종적립일', '포인트' ) );
		}

		$user_data = self::get_user_data();

		foreach ( $user_data['results'] as $log ) {
			fputcsv( $file, $log );
		}

		if ( $user_data['total_page'] <= $current_page ) {
			wp_send_json_success( array( 'continue' => false, 'download_url' => self::get_upload_url( 'msps_point_list.csv' ) ) );
		} else {
			wp_send_json_success( array( 'continue' => true, 'page' => $current_page + 1 ) );
		}
	}

	public static function upload_point() {
		check_ajax_referer( 'mshop-point-ex' );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

		MSPS_Upload_Point::process_csv();

		wp_send_json_success();
	}

	public static function process_point() {
		try {
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				die();
			}

			MSPS_Upload_Point::process_point();
			wp_send_json_success();
		} catch ( Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}
	}

	public static function get_logs_fragment() {
		wp_verify_nonce( '_nonce' );

		if ( ! is_user_logged_in() ) {
			die();
		}

		$page      = isset( $_POST['page'] ) ? $_POST['page'] : '1';
		$wallet_id = isset( $_POST['wallet_id'] ) ? $_POST['wallet_id'] : 'all';
		$current_language = apply_filters( 'msps_get_current_language', mshop_wpml_get_current_language() );

		$logs      = MSPS_Log::get_logs( get_current_user_id(), $page, 10, $wallet_id, $current_language );
        $last_page = ceil( $logs['total_count'] / 10 );

		ob_start();
		wc_get_template( '/myaccount/msps-point-log-fragment.php', array( 'logs' => $logs['results'] ), '', MSPS()->template_path() );
		$fragment = ob_get_clean();

        wp_send_json_success( array(
            'fragment'  => $fragment,
            'last_page' => $last_page
        ) );
	}

	public static function update_order_point() {
		check_ajax_referer( 'mshop-point-ex' );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die( - 1 );
		}
		$order = apply_filters( 'msps_get_order', wc_get_order( $_POST['order_id'] ), $_POST['order_id'] );

		if ( $order ) {
			$prev_point = MSPS_Order::get_earn_point( $order );

			MSPS_Order::set_earn_point( $order, $_POST['point'] );

			$order->add_order_note( sprintf( __( '적립예정 포인트가 %s 포인트에서 %s 포인트로 변경되었습니다.', 'mshop-point-ex' ), number_format( $prev_point, 2 ), number_format( $_POST['point'], 2 ) ) );

			wp_send_json_success();
		} else {
			wp_send_json_error( __( '주문 정보를 찾을 수 없습니다.', 'mshop-point-ex' ) );
		}
	}

	public static function clear_scheduled_action() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die( - 1 );
		}

		MSPS_Extinction::clear();
		MSPS_Extinction_Notification::clear();

		wp_send_json_success( array( 'reload' => true ) );
	}

	public static function reset_volatile_wallet() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die( - 1 );
		}

		global $wpdb;

		$balance_table = MSPS_POINT_BALANCE_TABLE;

		$params    = json_decode( stripslashes_deep( $_POST['params'] ), true );
		$wallet_id = $params['id'];

		if ( empty( $wallet_id ) || ! MSPS_Volatile_Wallet::is_volatile_wallet( $wallet_id ) ) {
			wp_send_json_error( __( '잘못된 요청입니다.', 'mshop-point-ex' ) );
		}

		$wpdb->query( "DELETE FROM {$balance_table} WHERE wallet_id = '{$wallet_id}'" );

		wp_send_json_success();
	}
}

MSPS_Ajax::init();
