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

if ( ! class_exists( 'MSADDR_File' ) ) {

	class MSADDR_File {

		public static function init() {
			add_action( 'init', array( __CLASS__, 'download' ), 20 );
			add_action( 'woocommerce_checkout_update_order_meta', array( __CLASS__, 'process_upload_file' ), 10, 2 );
			add_filter( 'woocommerce_form_field_mshop_file', array( __CLASS__, 'output_file_upload_fields' ), 10, 4 );
			add_filter( 'msaddr_custom_field_value', array( __CLASS__, 'file_upload_field_value' ), 10, 4 );
		}

		public static function output_file_upload_fields( $field, $key, $args, $value ) {
			ob_start();
			wp_enqueue_script( 'msaddr-file-upload', MSADDR()->plugin_url() . '/assets/js/file-upload.js', array(), MSM_VERSION );
			wp_localize_script( 'msaddr-file-upload', '_msaddr_upload', array(
				'ajaxurl'  => admin_url( 'admin-ajax.php' ),
				'slug'     => MSADDR_AJAX_PREFIX,
				'_wpnonce' => wp_create_nonce( 'msaddr-upload-file' )
			) );
			wp_enqueue_style( 'msaddr-file-upload', MSADDR()->plugin_url() . '/assets/css/file-upload.css', array(), MSM_VERSION );

			$filename = array();

			if ( is_user_logged_in() ) {
				$value = get_user_meta( get_current_user_id(), substr( $key, 1 ), true );

				if ( is_array( $value ) ) {
					$filename = array_map( function ( $file ) {
						return is_array( $file ) ? urldecode( basename( $file['filename'] ) ) : '';
					}, $value );
				}

			}

			?>
            <script>
                jQuery( document ).ready( function ( $ ) {
                    $( 'input#<?php echo $args['id']; ?>' ).customFile();
                } );
            </script>

            <p class="form-row form-row-wide <?php echo esc_attr( implode( ' ', $args['class'] ) ); ?>">
				<?php echo $args['label']; ?>
                <input style="display: none;" type="file"
                       id="<?php echo $args['id']; ?>"
                       value="<?php echo implode( ',', $filename ); ?>"
                       placeholder="<?php echo $args['placeholder']; ?>"/>
                <input type="hidden" name="<?php echo $args['id']; ?>" value="MSADDR_FILE_DEFAULT"/>
            </p>

			<?php

			return ob_get_clean();
		}
		protected static function get_temp_dir( $upload_key = '' ) {
			$upload_dir = wp_upload_dir();

			$temp_dir = $upload_dir['basedir'] . '/mshop-address/temp/';

			if ( ! empty( $upload_key ) ) {
				$temp_dir .= $upload_key . '/';
			}

			if ( ! file_exists( $temp_dir ) ) {
				wp_mkdir_p( $temp_dir );
			}

			return $temp_dir;
		}
		protected static function get_order_dir( $order_id, $upload_key ) {
			$upload_dir = wp_upload_dir();

			return $upload_dir['basedir'] . '/mshop-address/order/' . $order_id . '/' . $upload_key . '/';
		}

		protected static function get_user_idr( $user_id ) {
			$upload_dir = wp_upload_dir();

			return $upload_dir['basedir'] . '/mshop_members/user/' . $user_id . '/';
		}

		protected static function clean( $path ) {
			foreach ( array_filter( glob( $path . "*" ), 'is_file' ) as $file ) {
				unlink( $file );
			}
		}

		protected static function copy_file_to_order( $order_id, $upload_key ) {
			$src = self::get_temp_dir( $upload_key );

			if ( ! file_exists( $src ) ) {
				return;
			}

			$dest = self::get_order_dir( $order_id, $upload_key );

			if ( ! file_exists( $dest ) ) {
				wp_mkdir_p( $dest );
			}

			self::clean( $dest );

			foreach ( array_filter( glob( $src . "*" ), 'is_file' ) as $file ) {
				copy( $file, $dest . basename( $file ) );
			}
		}

		protected static function copy_file_to_user( $key, $order_id, $upload_key ) {
			$meta_key = substr( $key, 1 );

			$src = self::get_temp_dir( $upload_key );

			if ( ! file_exists( $src ) ) {
				return;
			}

			$order = wc_get_order( $order_id );

			if ( $order && $order->get_customer_id() > 0 ) {
				$attached_files = array();
				$labels         = array();

				$dest = self::get_user_idr( $order->get_customer_id() );

				foreach ( array_filter( glob( $src . "*" ), 'is_file' ) as $file ) {
					copy( $file, $dest . basename( $file ) );

					$attached_files[ uniqid() ] = array(
						'field_key'  => $meta_key,
						'upload_key' => $upload_key,
						'filename'   => $dest . basename( $file )
					);
				}

				foreach ( $attached_files as $file_key => $attached_file ) {
					$url      = sprintf( '%s/?msm_download=%d&key=%s&type=users&meta_name=%s', site_url(), $order->get_customer_id(), $file_key, $meta_key );
					$labels[] = '<a href="' . $url . '">' . urldecode( basename( $attached_file['filename'] ) ) . '</a>';
				}

				update_user_meta( $order->get_customer_id(), $meta_key, $attached_files );
				update_user_meta( $order->get_customer_id(), $meta_key . '_label', implode( '<br>', $labels ) );
			}
		}

		protected static function copy_default_file_to_order( $key, $order_id ) {
			$order = wc_get_order( $order_id );

			if ( $order && $order->get_customer_id() > 0 ) {
				$src_files = array();

				$value = get_user_meta( get_current_user_id(), substr( $key, 1 ), true );

				if ( is_array( $value ) ) {
					$src_files = array_map( function ( $file ) {
						return is_array( $file ) ? $file['filename'] : '';
					}, $value );
				}

				$upload_key = date( 'YmdHi' ) . bin2hex( openssl_random_pseudo_bytes( 4 ) );

				$dest = self::get_order_dir( $order_id, $upload_key );

				if ( ! file_exists( $dest ) ) {
					wp_mkdir_p( $dest );
				}

				self::clean( $dest );

				foreach ( $src_files as $src_file ) {
					copy( $src_file, $dest . basename( $src_file ) );
				}

				$order->update_meta_data( '_' . $key, 'MSADDR_FILE:' . $upload_key );
				$order->save_meta_data();
			}
		}

		public static function process_upload_file( $order_id, $posted ) {
			foreach ( $posted as $key => $value ) {
				if ( is_string( $value ) ) {
					if ( 0 === strpos( $value, 'MSADDR_FILE:' ) ) {
						$upload_key = str_replace( 'MSADDR_FILE:', '', $value );

						self::copy_file_to_order( $order_id, $upload_key );

						self::copy_file_to_user( $key, $order_id, $upload_key );
					} else if ( 0 === strpos( $value, 'MSADDR_FILE_DEFAULT' ) ) {
						self::copy_default_file_to_order( $key, $order_id );
					}
				}
			}
		}

		public static function validate( $action, $args = array() ) {
			if ( empty( $_GET['action'] ) || $action != $_GET['action'] || ! wp_verify_nonce( $_GET['_wpnonce'], $action ) ) {
				return false;
			}

			foreach ( $args as $arg ) {
				if ( empty( $_GET[ $arg ] ) ) {
					return false;
				}
			}

			return true;
		}

		public static function download() {
			if ( ! self::validate( 'msaddr-download', array( 'order_id', 'key', 'filename' ) ) ) {
				return;
			}

			$order = wc_get_order( $_GET['order_id'] );

			if ( ! $order || ( ! current_user_can( 'manage_woocommerce' ) && $order->get_customer_id() != get_current_user_id() ) ) {
				return;
			}

			$file_name = $_GET['filename'];
			if ( apply_filters( 'msaddr_url_encode_to_upload_filename', true ) ) {
				$file_name = urlencode( $file_name );
			}

			WC_Download_Handler::download( self::get_order_dir( $_GET['order_id'], $_GET['key'] ) . $file_name, 0 );

			die();
		}

		public static function file_upload_field_value( $value, $key, $field, $order ) {
			if ( is_string( $value ) && 0 === strpos( $value, 'MSADDR_FILE:' ) ) {
				$files = array();

				$upload_key = str_replace( 'MSADDR_FILE:', '', $value );

				$src = self::get_order_dir( $order->get_id(), $upload_key );

				foreach ( scandir( $src ) as $file ) {
					if ( ! in_array( $file, array( '..', '.' ) ) && is_file( $src . $file ) ) {
						$download_url = wp_nonce_url( add_query_arg( array(
							'action'   => 'msaddr-download',
							'order_id' => $order->get_id(),
							'key'      => $upload_key,
							'filename' => $file,
						), home_url() ), 'msaddr-download' );

						$files[] = sprintf( "<a href='%s'>%s</a>", $download_url, urldecode( $file ) );
					}
				}

				$value = implode( '<br>', $files );
			}

			return $value;
		}

		public static function upload_temp_file() {
			check_ajax_referer( 'msaddr-upload-file' );

			if ( isset( $_FILES ) ) {
				if ( ! empty( $_REQUEST['upload_key'] ) && 0 === strpos( $_REQUEST['upload_key'], 'MSADDR_FILE:' ) ) {
					$upload_key = str_replace( 'MSADDR_FILE:', '', $_REQUEST['upload_key'] );
				} else {
					$upload_key = date( 'YmdHi' ) . bin2hex( openssl_random_pseudo_bytes( 4 ) );
				}

				$upload_dir = self::get_temp_dir( $upload_key );
				self::clean( $upload_dir );

				foreach ( $_FILES as $key => $file ) {
					$file_name = apply_filters( 'msaddr_upload_file_name', $file['name'], $key );

					if ( apply_filters( 'msaddr_url_encode_to_upload_filename', true ) ) {
						$file_name = urlencode( $file_name );
					}

					$destination = $upload_dir . basename( $file_name );

					if ( move_uploaded_file( $file['tmp_name'], $destination ) ) {
						wp_send_json_success( 'MSADDR_FILE:' . $upload_key );
					} else {
						wp_send_json_error( __( '파일 업로드중 오류가 발생했습니다.', 'mshop-address-ex' ) );
					}
				}
			}
		}
	}

	MSADDR_File::init();
}