<?php


//소스에 URL로 직접 접근 방지
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MSPS_Upload_Point' ) ) {

	class MSPS_Upload_Point {

		protected static function get_upload_dir() {
			$upload_dir      = wp_upload_dir();
			$pafw_upload_dir = $upload_dir['basedir'] . '/msps/';
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
						throw new Exception( __( '파일 업로드중 오류가 발생했습니다.', 'mshop-point-ex' ) );
					}
				}
			}

			return $files;
		}
		static function parse_csv( $filename ) {
			$point_infos = array();

			require_once( MSPS()->plugin_path() . '/lib/csv/class-readcsv.php' );

			// Loop through the file lines
			$file_handle = fopen( $filename, 'r' );
			$csv_reader  = new ReadCSV( $file_handle, ',', "\xEF\xBB\xBF" ); // Skip any UTF-8 byte order mark.

			$rownum         = 1;
			$column_headers = array();
			while ( ( $line = $csv_reader->get_row() ) !== null ) {

				if ( empty( $line ) ) {
					if ( 1 == $rownum ) {
						throw new Exception( __( 'CSV 파일에 컬럼 정보가 없습니다.', 'mshop-point-ex' ) );
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

				if ( empty( $line_data['action'] ) || ! in_array( $line_data['action'], array( 'ADD', 'SET' ) ) ) {
					throw new Exception( sprintf( __( '[%d행] action 필드의 값은 ADD 또는 SET 이어야 합니다.', 'mshop-point-ex' ), $rownum ) );
				}

				if ( empty( $line_data['user_id'] ) && empty( $line_data['user_login'] ) ) {
					throw new Exception( sprintf( __( '[%d행] user_id 또는 user_login 필드는 필수입니다.', 'mshop-point-ex' ), $rownum ) );
				}

				if ( floatval( $line_data['point'] ) < 0 ) {
					throw new Exception( sprintf( __( '[%d행] point는 0 보다 크거나 같아야 합니다.', 'mshop-point-ex' ), $rownum ) );
				}

				if ( ! empty( $line_data['user_id'] ) ) {
					$user = get_user_by( 'id', $line_data['user_id'] );
				} else {
					$user = get_user_by( 'login', $line_data['user_login'] );
				}

				if ( ! $user ) {
					throw new Exception( sprintf( __( '[%d행] %s : 올바르지 않은 사용자 정보입니다.', 'mshop-point-ex' ), $rownum, ! empty( $line_data['user_id'] ) ? $line_data['user_id'] : $line_data['user_login'] ) );
				}

				$point_infos[] = $line_data;

				$rownum ++;
			}

			fclose( $file_handle );

			return $point_infos;
		}
		static function process_csv() {
			try {
				$files = self::move_upload_files();

				$point_infos = self::parse_csv( $files[0]['filename'] );

				if ( empty( $point_infos ) ) {
					throw new Exception( __( '포인트 정보가 없습니다.', 'mshop-point-ex' ) );
				}

				ob_start();

				include( 'views/upload-point.php' );

				$data = ob_get_clean();

				wp_send_json_success( $data );

			} catch ( Exception $e ) {
				wp_send_json_error( $e->getMessage() );
			}
		}
		static function process_point() {
			$point_info = $_REQUEST['point_data'];

			if ( ! empty( $point_info['user_id'] ) ) {
				$user = get_user_by( 'id', $point_info['user_id'] );
			} else {
				$user = get_user_by( 'login', $point_info['user_login'] );
			}

			if( ! empty( $point_info['lang'] ) ) {
				$current_language = $point_info['lang'];
			}else {
				$current_language = apply_filters( 'msps_get_current_language', mshop_wpml_get_current_language() );
			}

			$point_user = new MSPS_User( $user, $current_language );
            $wallet_id = msps_get( $point_info, 'wallet_id', 'free_point' );

			if ( 'ADD' == $point_info['action'] ) {
				$remain_point = $point_user->earn_point( $point_info['point'], $wallet_id );
				MSPS_Log::add_log( $user->ID, msps_get_wallet_id( $wallet_id, null, $current_language ), 'earn', 'admin', $point_info['point'], $remain_point, 'completed', 0, $point_info['message'], msps_get_wallet_name( $point_user, $wallet_id ) );
			} else {
				$point_user->set_point( $point_info['point'], $wallet_id );
				MSPS_Log::add_log( $user->ID, msps_get_wallet_id( $wallet_id, null, $current_language ), 'earn', 'admin', $point_info['point'], $point_info['point'], 'completed', 0, $point_info['message'], msps_get_wallet_name( $point_user, $wallet_id ) );
			}
		}

	}

}