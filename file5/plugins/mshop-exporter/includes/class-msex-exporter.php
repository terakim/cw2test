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

if ( ! class_exists( 'MSEX_Exporter' ) ) {

	class MSEX_Exporter {
		static $tmp_file_name = 'msex.tmp.';
		static $posts_per_page;

		public static function edit_posts_per_page( $posts_per_page ) {
			return self::$posts_per_page;
		}

		static function request_query( $vars ) {
			global $typenow, $wp_post_statuses;

			if ( 'product' === $typenow ) {
				// Sorting
				if ( isset( $vars['orderby'] ) ) {
					if ( 'price' == $vars['orderby'] ) {
						$vars = array_merge( $vars, array(
							'meta_key' => '_price',
							'orderby'  => 'meta_value_num',
						) );
					}
					if ( 'sku' == $vars['orderby'] ) {
						$vars = array_merge( $vars, array(
							'meta_key' => '_sku',
							'orderby'  => 'meta_value',
						) );
					}
				}
			} else if ( in_array( $typenow, wc_get_order_types( 'order-meta-boxes' ) ) ) {
				// Filter the orders by the posted customer.
				if ( isset( $_GET['_customer_user'] ) && $_GET['_customer_user'] > 0 ) {
					$vars['meta_query'] = array(
						array(
							'key'     => '_customer_user',
							'value'   => (int) $_GET['_customer_user'],
							'compare' => '=',
						),
					);
				}

				// Sorting
				if ( isset( $vars['orderby'] ) ) {
					if ( 'order_total' == $vars['orderby'] ) {
						$vars = array_merge( $vars, array(
							'meta_key' => '_order_total',
							'orderby'  => 'meta_value_num',
						) );
					}
				}

				// Status
				if ( empty( $vars['post_status'] ) ) {
					$post_statuses = wc_get_order_statuses();

					foreach ( $post_statuses as $status => $value ) {
						if ( isset( $wp_post_statuses[ $status ] ) && false === $wp_post_statuses[ $status ]->show_in_admin_all_list ) {
							unset( $post_statuses[ $status ] );
						}
					}

					$vars['post_status'] = array_keys( $post_statuses );
				}
			}

			return $vars;
		}

		static function add_custom_query_var( $public_query_vars ) {
			$public_query_vars[] = 'shop_order_search';

			return $public_query_vars;
		}

		static function search_custom_fields( $wp ) {
			global $pagenow;

			if ( empty( $wp->query_vars['s'] ) || 'shop_order' !== $wp->query_vars['post_type'] || ! isset( $_GET['s'] ) ) { // phpcs:ignore  WordPress.Security.NonceVerification.Recommended
				return;
			}

			$post_ids = wc_order_search( wc_clean( wp_unslash( $_GET['s'] ) ) ); // WPCS: input var ok, sanitization ok.

			if ( ! empty( $post_ids ) ) {
				// Remove "s" - we don't want to search order name.
				unset( $wp->query_vars['s'] );

				// so we know we're doing this.
				$wp->query_vars['shop_order_search'] = true;

				// Search by found posts.
				$wp->query_vars['post__in'] = array_merge( $post_ids, array( 0 ) );
			}
		}
		static function get_tmp_file_path( $type ) {
			return self::get_temp_folder_path() . self::$tmp_file_name . $type;
		}
		static function get_temp_folder_path() {
			$default_path = str_replace( untrailingslashit( ABSPATH ), '', MSEX()->plugin_path() . '/temp/' );
			$working_path = get_option( 'msex_working_directory', $default_path );

			return ABSPATH . $working_path;
		}
		static function get_temp_folder_url() {
			$default_path = str_replace( untrailingslashit( ABSPATH ), '', MSEX()->plugin_path() . '/temp/' );

			return site_url( get_option( 'msex_working_directory', $default_path ) );
		}
		static function get_download_file_path( $type, $exporter ) {
			if ( 'excel' == $exporter->get_download_type() ) {
				$download_type = 'xlsx';
			} else {
				$download_type = 'csv';
			}

			return sprintf( "%s/msex-%s.%s", untrailingslashit( self::get_temp_folder_path() ), $type, $download_type );
		}

		static function get_download_file_url( $type, $exporter ) {
			if ( 'excel' == $exporter->get_download_type() ) {
				$download_type = 'xlsx';
			} else {
				$download_type = 'csv';

				if ( 'yes' == get_option( 'msex_convert_encoding', 'no' ) ) {
					$filename = self::get_download_file_path( $type, $exporter );

					$file_contents = file_get_contents( $filename );

					$file_contents = msex_maybe_convert_to_euckr( $file_contents );

					file_put_contents( $filename, $file_contents );
				}
			}

			$tmp_file_name      = sprintf( "msex-%s.%s", $type, $download_type );
			$download_file_name = sprintf( "msex-%s-%s.%s", $type, date( 'YmdHis', strtotime( current_time( 'mysql' ) ) ), $download_type );

			copy( self::get_temp_folder_path() . $tmp_file_name, self::get_temp_folder_path() . $download_file_name );

			return self::get_temp_folder_url() . $download_file_name;
		}
		static function temp_file_init( $type, $exporter ) {
			if ( ! file_exists( self::get_temp_folder_path() ) ) {
				mkdir( self::get_temp_folder_path() );
			}

			unlink( self::get_tmp_file_path( $type ) );

			if ( 'excel' == $exporter->get_download_type() ) {
				@header( "Content-Type: application/vnd.ms-excel" );
				$exporter->setup_layout( $type );
			} else {
				$file = fopen( self::get_tmp_file_path( $type ), 'a' );
				fputs( $file, "\xEF\xBB\xBF" );
				fputcsv( $file, $exporter->get_headers() );
				fclose( $file );
			}
		}
		static function file_output( $type, $exporter, $data ) {
			if ( 'excel' == $exporter->get_download_type() ) {
				@header( "Content-Type: application/vnd.ms-excel" );
				$exporter->outputEXCEL( $data, $type );
			} else {
				$file = fopen( self::get_tmp_file_path( $type ), "a" );
				foreach ( $data as $user_array ) {
					fputcsv( $file, $user_array, ',' );
				}
				fclose( $file );
			}
		}
		protected static function maybe_clean_file() {
			$dir = self::get_temp_folder_path();

			foreach ( glob( $dir . "msex-*" ) as $file ) {
				if ( filemtime( $file ) < time() - 3 * DAY_IN_SECONDS ) {
					unlink( $file );
				}
			}

			foreach ( glob( $dir . "msex.tmp.*" ) as $file ) {
				if ( filemtime( $file ) < time() - 3 * DAY_IN_SECONDS ) {
					unlink( $file );
				}
			}
		}
		public static function get_total_count() {
			global $wp_query, $typenow, $params;

			self::maybe_clean_file();

			ob_clean();

			$ids = msex_get( $_POST, 'ids', array() );

			parse_str( msex_get( $_POST, 'params' ), $params );

            $params['current_action'] = msex_get( $_POST, 'current_action' );

			$typenow = msex_get( $params, 'post_type', 'user' );

			$_REQUEST = $_GET = $params;
			$_GET['action'] = $_POST['action'];

			if ( version_compare( WC_VERSION, '3.5.0', '>=' ) && in_array( $typenow, wc_get_order_types( 'order-meta-boxes' ) ) ) {
				add_filter( 'query_vars', array( __CLASS__, 'add_custom_query_var' ) );
				add_action( 'parse_query', array( __CLASS__, 'search_custom_fields' ) );
			}

			if ( version_compare( WC_VERSION, '3.3.0', '>=' ) ) {
				add_filter( 'request', array( __CLASS__, 'request_query' ) );
			}
			if ( ! empty( $ids ) ) {
				$total_count = count( $ids );
			} else if ( $typenow == "product" || in_array( $typenow, wc_get_order_types() ) ) {
				if ( apply_filters( 'msex_use_object_ids_pre_patch', true ) ) {
					add_filter( 'edit_posts_per_page', function ( $posts_per_page ) {
						return - 1;
					}, 10 );
					add_filter( 'request', array( __CLASS__, 'set_return_fields' ) );

					wp_edit_posts_query();

					remove_filter( 'request', array( __CLASS__, 'set_return_fields' ) );

					set_transient( 'msex_export_ids', $wp_query->posts, HOUR_IN_SECONDS );

					$total_count = $wp_query->found_posts;
				} else {
					wp_edit_posts_query();
					$total_count = $wp_query->found_posts;
				}
			} else {
				$args = array(
					'role'   => empty( $params['role'] ) ? '' : $params['role'],
					'number' => 10
				);

				$args = apply_filters( 'msex_user_query_args', $args );

				$user_query = new WP_User_Query( $args );

				$total_count = $user_query->get_total();
			}

			$exporter = msex_get_exporter( $typenow, $_POST['template_id'] );

			self::temp_file_init( $typenow, $exporter );

			return $total_count;
		}

		public static function set_return_fields( $query_vars ) {
			$query_vars['fields'] = 'ids';

			return $query_vars;
		}
		public static function generate_page_data() {
			global $wp_query, $typenow, $params;

			ob_clean();

			$ids = msex_get( $_POST, 'ids', array() );

			parse_str( msex_get( $_POST, 'params' ), $params );

			$params['current_action'] = msex_get( $_POST, 'current_action' );
			$params['paged'] = msex_get( $_POST, 'paged' );
			self::$posts_per_page = $_POST['posts_per_page'];
			add_filter( 'edit_posts_per_page', array( __CLASS__, 'edit_posts_per_page' ), 10 );

			$typenow = msex_get( $params, 'post_type', 'user' );

			$_REQUEST = $_GET = $params;
			$_GET['action'] = $_POST['action'];

			if ( version_compare( WC_VERSION, '3.5.0', '>=' ) && in_array( $typenow, wc_get_order_types( 'order-meta-boxes' ) ) ) {
				add_filter( 'query_vars', array( __CLASS__, 'add_custom_query_var' ) );
				add_action( 'parse_query', array( __CLASS__, 'search_custom_fields' ) );
			}

			if ( version_compare( WC_VERSION, '3.3.0', '>=' ) ) {
				add_filter( 'request', array( __CLASS__, 'request_query' ) );
			}

			if ( empty( $ids ) ) {
				if ( $typenow == "product" || in_array( $typenow, wc_get_order_types() ) ) {
					if ( apply_filters( 'msex_use_object_ids_pre_patch', true ) ) {
						$export_ids = get_transient( 'msex_export_ids' );
						if ( is_array( $export_ids ) ) {
							$ids = array_splice( $export_ids, 0, $_POST['posts_per_page'] );
							set_transient( 'msex_export_ids', $export_ids, HOUR_IN_SECONDS );
						} else {
							throw new Exception( 'ERROR-001' );
						}
					} else {
						wp_edit_posts_query();
						$ids = wp_list_pluck( $wp_query->posts, 'ID' );
					}
				} else {
					$args = array(
						'role'   => empty( $params['role'] ) ? '' : $params['role'],
						'paged'  => $_POST['paged'],
						'number' => $_POST['posts_per_page']
					);

					$args = apply_filters( 'msex_user_query_args', $args );

					$user_query = new WP_User_Query( $args );
					$ids        = wp_list_pluck( $user_query->get_results(), 'ID' );
				}
			}

			$exporter = msex_get_exporter( $typenow, $_POST['template_id'] );

			self::file_output( $typenow, $exporter, $exporter->get_data( $ids ) );

			if ( $_POST['paged'] == $_POST['total_page'] ) {
				copy( self::get_tmp_file_path( $typenow ), self::get_download_file_path( $typenow, $exporter ) );
			}

			return $ids;
		}

		public static function get_download_url() {
			if ( apply_filters( 'msex_use_object_ids_pre_patch', true ) ) {
				delete_transient( 'msex_export_ids' );
			}

			parse_str( $_REQUEST['params'], $params );

			$typenow = msex_get( $params, 'post_type', 'user' );

			$exporter = msex_get_exporter( $typenow, $_POST['template_id'] );

			return self::get_download_file_url( $typenow, $exporter );
		}
	}
}