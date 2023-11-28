<?php


//소스에 URL로 직접 접근 방지
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MSEX_Upload_Sheets' ) ) {

	class MSEX_Upload_Sheets {

		protected static $order_statuses = null;

		protected static function get_order_statuses() {
			if ( is_null( self::$order_statuses ) ) {
				self::$order_statuses = array_map( function ( $status ) {
					return 'wc-' === substr( $status, 0, 3 ) ? substr( $status, 3 ) : $status;
				}, array_flip( wc_get_order_statuses() ) );
			}

			return self::$order_statuses;
		}

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
			$sheet_infos = array();

			require_once( MSEX()->plugin_path() . '/lib/csv/class-readcsv.php' );

			// Loop through the file lines
			$file_handle = fopen( $filename, 'r' );
			$csv_reader  = new ReadCSV( $file_handle, ',', "\xEF\xBB\xBF" ); // Skip any UTF-8 byte order mark.

			$rownum         = 1;
			$column_headers = array();
			while ( ( $line = $csv_reader->get_row() ) !== null ) {

				if ( empty( $line ) ) {
					if ( 1 == $rownum ) {
						throw new Exception( __( 'CSV 파일에 컬럼 정보가 없습니다.', 'mshop-exporter' ) );
						break;
					} else {
						foreach ( $line as $ckey => $column ) {
							$column_headers[ $ckey ] = trim( $column );
						}
						continue;
					}
				}

				if ( 1 == $rownum ) {
					$rownum ++;
					foreach ( $line as $ckey => $column ) {
						$column_headers[ $ckey ] = trim( $column );
					}
					continue;
				}

				$line_data = array();
				foreach ( $line as $ckey => $column ) {
					$line_data[ $column_headers[ $ckey ] ] = trim( $column );
				}

				if ( ! empty( $line_data['dlv_code'] ) && is_null( msex_get_dlv_company_info( $line_data['dlv_code'] ) ) ) {
					throw new Exception( sprintf( __( '[%d행] 택배사 코드가 잘못되었습니다.', 'mshop-exporter' ), $rownum, $line_data['dlv_code'] ) );
				}

				if ( empty( $line_data['order_id'] ) && empty( $line_data['order_item_id'] ) ) {
					throw new Exception( sprintf( __( '[%d행] 주문 번호(order_id) 또는 주문 아이템 번호(order_item_id)는 필수 필드입니다.', 'mshop-exporter' ), $rownum ) );
				}

				if ( empty( $line_data['order_id'] ) ) {
					$order_id = wc_get_order_id_by_order_item_id( $line_data['order_item_id'] );
				} else {
					$order_id = $line_data['order_id'];
				}

				$order = apply_filters( 'msex_get_order', wc_get_order( $order_id ), $order_id );

				if ( ! $order ) {
					throw new Exception( sprintf( __( '[%d행] #%d : 올바르지 않은 주문 번호입니다.', 'mshop-exporter' ), $rownum, $line_data['order_id'] ) );
				}

				if ( ! empty( $line_data['order_status'] ) && ! in_array( $line_data['order_status'], self::get_order_statuses() ) ) {
					throw new Exception( sprintf( __( '[%d행] %s - 잘못된 주문상태입니다.', 'mshop-exporter' ), $rownum, $line_data['order_status'] ) );
				}

				$sheet_infos[] = $line_data;

				$rownum ++;
			}

			fclose( $file_handle );

			return $sheet_infos;
		}
		static function process_csv() {
			try {
				$files = self::move_upload_files();

				$sheet_infos = self::parse_csv( $files[0]['filename'] );

				if ( empty( $sheet_infos ) ) {
					throw new Exception( __( '주문 정보가 없습니다.', 'mshop-exporter' ) );
				}

				ob_start();

				include( 'views/upload-sheets.php' );

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
		static function register_sheet_by_order( $order, $sheet_data ) {
			if ( ! empty( $sheet_data['dlv_code'] ) ) {
				$dlv_code = $sheet_data['dlv_code'];
				$sheet_no = $sheet_data['sheet_no'];

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

				if ( 'naverpay' == $order->get_payment_method() ) {
					$sheet_datas = array();

					foreach ( $order->get_items() as $item_id => $item ) {

						$sheet_datas[] = array(
							'order_id'         => $order->get_id(),
							'order_item_id'    => $item_id,
							'dlv_company_code' => $dlv_code,
							'sheet_no'         => $sheet_no
						);
					}

					do_action( 'mnp_bulk_ship_order', $sheet_datas );
				} else {
					$order->update_status( $order_status, sprintf( __( '송장정보가 등록되었습니다. [ %s : %s ],', 'mshop-exporter' ), $dlv_code, $sheet_no ) );
				}
			}

			if ( ! empty( $sheet_data['order_status'] ) ) {
				$order->update_status( $sheet_data['order_status'], __( '송장업로드 기능으로 주문상태가 변경되었습니다.', 'mshop-exporter' ) );
			}
		}
		static function register_sheet_by_order_item_id( $order, $order_item_id, $sheet_data ) {
			if ( ! empty( $sheet_data['dlv_code'] ) ) {
				$dlv_code = $sheet_data['dlv_code'];
				$sheet_no = $sheet_data['sheet_no'];

				$dlv_company = msex_get_dlv_company_info( $dlv_code );
				$dlv_url     = str_replace( '{msex_sheet_no}', $sheet_no, $dlv_company['dlv_url'] );

				wc_delete_order_item_meta( $order_item_id, '_msex_dlv_code' );
				wc_delete_order_item_meta( $order_item_id, '_msex_dlv_name' );
				wc_delete_order_item_meta( $order_item_id, '_msex_sheet_no' );
				wc_delete_order_item_meta( $order_item_id, '_msex_register_date' );

				wc_update_order_item_meta( $order_item_id, '_msex_dlv_code', $dlv_code );
				wc_update_order_item_meta( $order_item_id, '_msex_dlv_name', $dlv_company['dlv_name'] );
				wc_update_order_item_meta( $order_item_id, '_msex_sheet_no', $sheet_no );
				wc_update_order_item_meta( $order_item_id, '_msex_register_date', current_time( 'mysql' ) );

				if ( 'naverpay' == $order->get_payment_method() ) {
					$sheet_datas = array(
						array(
							'order_id'         => $order->get_id(),
							'order_item_id'    => $order_item_id,
							'dlv_company_code' => $dlv_code,
							'sheet_no'         => $sheet_no
						)
					);
					do_action( 'mnp_bulk_ship_order', $sheet_datas );
				} else {
					$flag = true;
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

						$order_status = get_option( 'msex_order_status_after_shipping', 'wc-shipping' );
						$order->update_status( $order_status, sprintf( __( '송장정보가 등록되었습니다. [ %s : %s ],', 'mshop-exporter' ), $dlv_code, $sheet_no ) );
					}

					$order->add_order_note( sprintf( __( '송장정보가 등록되었습니다. [ %s : %s : %s ],', 'mshop-exporter' ), $order_item_id, $dlv_code, $sheet_no ) );
				}
			}
		}
		static function register_sheets() {
			foreach ( $_REQUEST['sheet_data'] as $sheet_data ) {
				if ( ! empty( $sheet_data['order_item_id'] ) ) {
					$order_id = wc_get_order_id_by_order_item_id( $sheet_data['order_item_id'] );

					$order = apply_filters( 'msex_get_order', wc_get_order( $order_id ), $order_id );

					if ( $order ) {
						self::register_sheet_by_order_item_id( $order, $sheet_data['order_item_id'], $sheet_data );
					} else {
						throw new Exception( sprintf( __( '#%d : 올바르지 않은 주문 아이템 번호입니다.', 'mshop-exporter' ), $sheet_data['order_item_id'] ) );
					}
				} else if ( ! empty( $sheet_data['order_id'] ) ) {
					$order = apply_filters( 'msex_get_order', wc_get_order( $sheet_data['order_id'] ), $sheet_data['order_id'] );

					if ( $order ) {
						self::register_sheet_by_order( $order, $sheet_data );
					} else {
						throw new Exception( sprintf( __( '#%d : 올바르지 않은 주문 번호입니다.', 'mshop-exporter' ), $sheet_data['order_id'] ) );
					}
				}

			}
		}

		public static function woocommerce_hidden_order_itemmeta( $metas ) {
			return array_merge( $metas, array( '_msex_dlv_url', '_msex_dlv_code', '_msex_dlv_name', '_msex_sheet_no', '_msex_register_date' ) );
		}

		public static function woocommerce_attribute_label( $label, $name ) {
			switch ( $name ) {
				case '_msex_dlv_name' :
					$label = __( '택배사', 'mshop-exporter' );
					break;
				case '_msex_sheet_no' :
					$label = __( '송장번호', 'mshop-exporter' );
					break;
			}

			return $label;
		}
		public static function woocommerce_order_items_meta_display( $output, $order_item_meta ) {

			if ( isset( $order_item_meta->meta['_msex_sheet_no'] ) && is_array( $order_item_meta->meta['_msex_dlv_code'] ) ) {
				$sheet_no = array_shift( $order_item_meta->meta['_msex_sheet_no'] );
				$dlv_code = array_shift( $order_item_meta->meta['_msex_dlv_code'] );
				$dlv_name = array_shift( $order_item_meta->meta['_msex_dlv_name'] );
				$dlv_url  = msex_get_track_url( $dlv_code, $sheet_no );

				if ( ! empty( $sheet_no ) ) {
					$output .= '<dl class="variation"><dt>';
					$output .= sprintf( '%s ( <a target="_blank" href="%s">%s</a> )', $dlv_name, $dlv_url, $sheet_no );
					$output .= '</dt></dl>';
				}
			}

			return $output;
		}
		public static function woocommerce_display_item_meta( $output, $order_item_meta, $args ) {
			$sheet_no = $order_item_meta->get_meta( '_msex_sheet_no' );
			$dlv_code = $order_item_meta->get_meta( '_msex_dlv_code' );

			if ( ! empty( $sheet_no ) && ! empty( $dlv_code ) ) {
				$dlv_name = $order_item_meta->get_meta( '_msex_dlv_name' );
				$dlv_url  = msex_get_track_url( $dlv_code, $sheet_no );

				$output .= msex_get( $args, 'before', '<dl class="variation"><dt>' );
				$output .= sprintf( '%s ( <a target="_blank" href="%s">%s</a> )', $dlv_name, $dlv_url, $sheet_no );
				$output .= msex_get( $args, 'after', '</dt></dl>' );
			}

			return $output;
		}
	}

}