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

class MSEX_WCFM_Ajax {
    static $slug;

    static $posts_per_page;

    static $tmp_file_name = 'mshop-exporter.tmp.';

    public static function init() {
        self::$slug = 'mshop-exporter';
        self::add_ajax_events();
    }

    public static function add_ajax_events() {
        $ajax_events = array();

        if ( is_admin() ) {
            $ajax_events = array_merge( $ajax_events, array(
                'wcfm_upload_sheets'                => false,
                'wcfm_register_sheets'              => false,
                'wcfm_delete_sheet_info'            => false,
                'wcfm_update_sheet_info'            => false,
                'wcfm_delete_sheet_by_order_item'   => false,
                'wcfm_update_sheet_by_order_item'   => false,
            ) );
        }

        foreach ( $ajax_events as $ajax_event => $nopriv ) {
            add_action( 'wp_ajax_' . msex_ajax_command( $ajax_event ), array( __CLASS__, $ajax_event ) );
            if ( $nopriv ) {
                add_action( 'wp_ajax_nopriv_' . msex_ajax_command( $ajax_event ), array( __CLASS__, $ajax_event ) );
            }
        }
    }

    public static function wcfm_upload_sheets() {
        check_ajax_referer( 'mshop-exporter' );

        if ( ! current_user_can( 'manage_woocommerce' ) && ! wcfm_is_vendor( get_current_user_id() ) ) {
            die( - 1 );
        }

        MSEX_WCFM_Upload_Sheets::process_csv();

        wp_send_json_success();
    }

    public static function wcfm_register_sheets() {
        try {
            if ( ! current_user_can( 'manage_woocommerce' ) && ! wcfm_is_vendor( get_current_user_id() ) ) {
                throw new Exception( __( '권한이 없습니다.', 'mshop-exporter' ) );
            }

            MSEX_WCFM_Upload_Sheets::register_sheets();
            wp_send_json_success();
        } catch ( Exception $e ) {
            wp_send_json_error( $e->getMessage() );
        }
    }
    public static function wcfm_delete_sheet_info() {
        check_ajax_referer( 'mshop-exporter' );

        if ( ! current_user_can( 'manage_woocommerce' ) && ! wcfm_is_vendor( get_current_user_id() ) ) {
            die( - 1 );
        }

        $order = wc_get_order( $_REQUEST['order_id'] );
        if ( $order ) {

            foreach ( $order->get_items() as $item_id => $item ) {
                if( current_user_can( 'manage_woocommerce' ) || get_current_user_id() == $item->get_meta('_vendor_id') || get_current_user_id() == $order->get_meta( '_dokan_vendor_id' ) ) {
                    wc_delete_order_item_meta( $item_id, '_msex_dlv_code' );
                    wc_delete_order_item_meta( $item_id, '_msex_dlv_name' );
                    wc_delete_order_item_meta( $item_id, '_msex_sheet_no' );
                }
            }

            if ( 'dokan' == wcfm_is_marketplace() ) {
                $order = wc_get_order( wc_get_order_id_by_order_item_id( $item_id ) );

                msex_update_meta_data( $order, '_msex_dlv_code', '' );
                msex_update_meta_data( $order, '_msex_dlv_name', '' );
                msex_update_meta_data( $order, '_msex_sheet_no', '' );

                if ( $order ) {
                    $parent_order = wc_get_order( $order->get_parent_id() );
                    if ( $parent_order ) {
                        foreach ( $parent_order->get_items() as $item_id => $item ) {
                            if( get_current_user_id() == get_post( $item->get_product_id() )->post_author ) {
                                wc_delete_order_item_meta( $item_id, '_msex_dlv_code' );
                                wc_delete_order_item_meta( $item_id, '_msex_dlv_name' );
                                wc_delete_order_item_meta( $item_id, '_msex_sheet_no' );
                            }
                        }
                    }
                }
            }

            wp_send_json_success();
        } else {
            wp_send_json_error( __( '주문 정보를 찾을 수 없습니다.', 'mshop-exporter' ) );
        }

    }
    public static function wcfm_update_sheet_info() {
        check_ajax_referer( 'mshop-exporter' );

        if ( ! current_user_can( 'manage_woocommerce' ) && ! wcfm_is_vendor( get_current_user_id() ) ) {
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

            foreach ( $order->get_items() as $item_id => $item ) {
                if( current_user_can( 'manage_woocommerce' ) || get_current_user_id() == $item->get_meta('_vendor_id') || get_current_user_id() == $order->get_meta( '_dokan_vendor_id' ) ) {
                    wc_delete_order_item_meta($item_id, '_msex_dlv_code');
                    wc_delete_order_item_meta($item_id, '_msex_dlv_name');
                    wc_delete_order_item_meta($item_id, '_msex_sheet_no');
                    wc_delete_order_item_meta($item_id, '_msex_register_date');

                    wc_update_order_item_meta($item_id, '_msex_dlv_code', $dlv_code);
                    wc_update_order_item_meta($item_id, '_msex_dlv_name', $dlv_company['dlv_name']);
                    wc_update_order_item_meta($item_id, '_msex_sheet_no', $sheet_no);
                    wc_update_order_item_meta($item_id, '_msex_register_date', current_time('mysql'));
                }
            }

            if ( 'dokan' == wcfm_is_marketplace() ) {
                $order = wc_get_order( wc_get_order_id_by_order_item_id( $item_id ) );

                msex_update_meta_data( $order, '_msex_dlv_code', $dlv_code );
                msex_update_meta_data( $order, '_msex_dlv_name', $dlv_company['dlv_name'] );
                msex_update_meta_data( $order, '_msex_sheet_no', $sheet_no );
                msex_update_meta_data( $order, '_msex_register_date', current_time( 'mysql' ) );

                if ( $order ) {
                    $parent_order = wc_get_order( $order->get_parent_id() );
                    if ( $parent_order ) {
                        foreach ( $parent_order->get_items() as $item_id => $item ) {
                            if( get_current_user_id() == get_post( $item->get_product_id() )->post_author ) {
                                wc_delete_order_item_meta($item_id, '_msex_dlv_code');
                                wc_delete_order_item_meta($item_id, '_msex_dlv_name');
                                wc_delete_order_item_meta($item_id, '_msex_sheet_no');
                                wc_delete_order_item_meta($item_id, '_msex_register_date');

                                wc_update_order_item_meta($item_id, '_msex_dlv_code', $dlv_code);
                                wc_update_order_item_meta($item_id, '_msex_dlv_name', $dlv_company['dlv_name']);
                                wc_update_order_item_meta($item_id, '_msex_sheet_no', $sheet_no);
                                wc_update_order_item_meta($item_id, '_msex_register_date', current_time('mysql'));
                            }
                        }
                    }
                }
            }

            $order_status = get_option( 'msex_order_status_after_shipping', 'wc-shipping' );

            $order->add_order_note( sprintf( __( '송장정보가 등록되었습니다. [ %s : %s ],', 'mshop-exporter' ), $dlv_code, $sheet_no ) );
            $order->update_status( $order_status );

            wp_send_json_success();
        } else {
            wp_send_json_error( __( '주문 정보를 찾을 수 없습니다.', 'mshop-exporter' ) );
        }

    }
    public static function wcfm_delete_sheet_by_order_item() {
        check_ajax_referer( 'mshop-exporter' );

        if ( ! current_user_can( 'manage_woocommerce' ) && ! wcfm_is_vendor( get_current_user_id() ) ) {
            die( - 1 );
        }

        if ( ! empty( $_REQUEST['item_id'] ) ) {
            $item_id = $_REQUEST['item_id'];

            $is_seller   = false;
            if ( 'dokan' == wcfm_is_marketplace() ) {
                $order = wc_get_order( wc_get_order_id_by_order_item_id( $item_id ) );
                $is_seller = get_current_user_id() == $order->get_meta( '_dokan_vendor_id' );
            } else {
                $is_seller = get_current_user_id() == wc_get_order_item_meta( $item_id, '_vendor_id', true );
            }

            if( $is_seller || current_user_can( 'manage_woocommerce' ) ) {
                wc_delete_order_item_meta( $item_id, '_msex_dlv_code' );
                wc_delete_order_item_meta( $item_id, '_msex_dlv_name' );
                wc_delete_order_item_meta( $item_id, '_msex_sheet_no' );
                wc_delete_order_item_meta( $item_id, '_msex_register_date' );

                if ( 'dokan' == wcfm_is_marketplace() ) {
                    $order = wc_get_order( wc_get_order_id_by_order_item_id( $item_id ) );
                    if ( $order ) {
                        $parent_order = wc_get_order( $order->get_parent_id() );
                        if ( $parent_order ) {
                            foreach ( $parent_order->get_items() as $item_id => $item ) {
                                if( get_current_user_id() == get_post( $item->get_product_id() )->post_author ) {
                                    wc_delete_order_item_meta( $item_id, '_msex_dlv_code' );
                                    wc_delete_order_item_meta( $item_id, '_msex_dlv_name' );
                                    wc_delete_order_item_meta( $item_id, '_msex_sheet_no' );
                                    wc_delete_order_item_meta( $item_id, '_msex_register_date' );
                                }
                            }
                        }
                    }
                }
            }

            wp_send_json_success();
        } else {
            wp_send_json_error( __( '잘못된 요청입니다.', 'mshop-exporter' ) );
        }

    }
    public static function wcfm_update_sheet_by_order_item() {
        check_ajax_referer( 'mshop-exporter' );

        if ( ! current_user_can( 'manage_woocommerce' ) && ! wcfm_is_vendor( get_current_user_id() ) ) {
            die( - 1 );
        }

        if ( ! empty( $_REQUEST['item_id'] ) && ! empty( $_REQUEST['dlv_code'] ) && ! empty( $_REQUEST['sheet_no'] ) ) {
            $item_id  = $_REQUEST['item_id'];
            $dlv_code = $_REQUEST['dlv_code'];
            $sheet_no = $_REQUEST['sheet_no'];

            $dlv_company = msex_get_dlv_company_info( $dlv_code );
            $dlv_url     = str_replace( '{msex_sheet_no}', $sheet_no, $dlv_company['dlv_url'] );

            $is_seller   = false;
            if ( 'dokan' == wcfm_is_marketplace() ) {
                $order = wc_get_order( wc_get_order_id_by_order_item_id( $item_id ) );
                $is_seller = get_current_user_id() == $order->get_meta( '_dokan_vendor_id' );
            } else {
                $is_seller = get_current_user_id() == wc_get_order_item_meta( $item_id, '_vendor_id', true );
            }

            if( $is_seller || current_user_can( 'manage_woocommerce' ) ) {
                wc_delete_order_item_meta( $item_id, '_msex_dlv_code' );
                wc_delete_order_item_meta( $item_id, '_msex_dlv_name' );
                wc_delete_order_item_meta( $item_id, '_msex_sheet_no' );
                wc_delete_order_item_meta( $item_id, '_msex_register_date' );

                wc_update_order_item_meta( $item_id, '_msex_dlv_code', $dlv_code );
                wc_update_order_item_meta( $item_id, '_msex_dlv_name', $dlv_company['dlv_name'] );
                wc_update_order_item_meta( $item_id, '_msex_sheet_no', $sheet_no );
                wc_update_order_item_meta( $item_id, '_msex_register_date', current_time( 'mysql' ) );
            }

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
                if ( 'dokan' == wcfm_is_marketplace() ) {
                    $order = wc_get_order( wc_get_order_id_by_order_item_id( $item_id ) );

                    msex_update_meta_data( $order, '_msex_dlv_code', $dlv_code );
                    msex_update_meta_data( $order, '_msex_dlv_name', $dlv_company['dlv_name'] );
                    msex_update_meta_data( $order, '_msex_sheet_no', $sheet_no );
                    msex_update_meta_data( $order, '_msex_register_date', current_time( 'mysql' ) );

                    if ( $order ) {
                        $parent_order = wc_get_order( $order->get_parent_id() );
                        if ( $parent_order ) {
                            foreach ( $parent_order->get_items() as $item_id => $item ) {
                                if( get_current_user_id() == get_post( $item->get_product_id() )->post_author ) {
                                    wc_delete_order_item_meta( $item_id, '_msex_dlv_code' );
                                    wc_delete_order_item_meta( $item_id, '_msex_dlv_name' );
                                    wc_delete_order_item_meta( $item_id, '_msex_sheet_no' );
                                    wc_delete_order_item_meta( $item_id, '_msex_register_date' );

                                    wc_update_order_item_meta( $item_id, '_msex_dlv_code', $dlv_code );
                                    wc_update_order_item_meta( $item_id, '_msex_dlv_name', $dlv_company['dlv_name'] );
                                    wc_update_order_item_meta( $item_id, '_msex_sheet_no', $sheet_no );
                                    wc_update_order_item_meta( $item_id, '_msex_register_date', current_time( 'mysql' ) );
                                }
                            }
                        }
                    }
                }

                $order_status = get_option( 'msex_order_status_after_shipping', 'wc-shipping' );
                $order->update_status( $order_status, sprintf( __( '송장정보가 등록되었습니다. [ %s : %s ],', 'mshop-exporter' ), $dlv_code, $sheet_no ) );
            }

            $order->add_order_note( sprintf( __( '송장정보가 등록되었습니다. [ %s : %s : %s ],', 'mshop-exporter' ), $item_id, $dlv_code, $sheet_no ) );

            wp_send_json_success();
        } else {
            wp_send_json_error( __( '잘못된 요청입니다.', 'mshop-exporter' ) );
        }
    }
}

MSEX_WCFM_Ajax::init();

if ( ! class_exists( 'MSEX_WCFM_Upload_Sheets' ) ) {
    class MSEX_WCFM_Upload_Sheets {

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
                $files = MSEX_Upload_Sheets::move_upload_files();

                $sheet_infos = is_plugin_active( 'dokan-lite/dokan.php' ) ? MSEX_Upload_Sheets::parse_csv( $files[0]['filename'] ) : self::parse_csv( $files[0]['filename'] );

                if ( empty( $sheet_infos ) ) {
                    throw new Exception( __( '주문 정보가 없습니다.', 'mshop-exporter' ) );
                }

                ob_start();

                include('views/wcfm-upload-sheets.php');

                $data = ob_get_clean();

                wp_send_json_success( $data );

            } catch ( Exception $e ) {
                wp_send_json_error( $e->getMessage() );
            }
        }
        static function register_sheet_by_order( $order, $sheet_data ) {
            if ( ! empty( $sheet_data['dlv_code'] ) ) {
                $dlv_code = $sheet_data['dlv_code'];
                $sheet_no = $sheet_data['sheet_no'];

                $dlv_company = msex_get_dlv_company_info( $dlv_code );
                $dlv_url     = str_replace( '{msex_sheet_no}', $sheet_no, $dlv_company['dlv_url'] );

                if ( 'dokan' == wcfm_is_marketplace() ) {
                    msex_update_meta_data( $order, '_msex_dlv_code', $dlv_code );
                    msex_update_meta_data( $order, '_msex_dlv_name', $dlv_company['dlv_name'] );
                    msex_update_meta_data( $order, '_msex_sheet_no', $sheet_no );
                    msex_update_meta_data( $order, '_msex_register_date', current_time( 'mysql' ) );
                }

                foreach ( $order->get_items() as $item_id => $item ) {
                    if( current_user_can( 'manage_woocommerce' ) || get_current_user_id() == $item->get_meta( '_vendor_id' )  || get_current_user_id() == $order->get_meta( '_dokan_vendor_id' ) ) {
                        wc_delete_order_item_meta($item_id, '_msex_dlv_code');
                        wc_delete_order_item_meta($item_id, '_msex_dlv_name');
                        wc_delete_order_item_meta($item_id, '_msex_sheet_no');
                        wc_delete_order_item_meta($item_id, '_msex_register_date');

                        wc_update_order_item_meta($item_id, '_msex_dlv_code', $dlv_code);
                        wc_update_order_item_meta($item_id, '_msex_dlv_name', $dlv_company['dlv_name']);
                        wc_update_order_item_meta($item_id, '_msex_sheet_no', $sheet_no);
                        wc_update_order_item_meta($item_id, '_msex_register_date', current_time('mysql'));
                    }
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

                if( current_user_can( 'manage_woocommerce' ) || get_current_user_id() == wc_get_order_item_meta( $order_item_id, '_vendor_id', true )  || get_current_user_id() == $order->get_meta( '_dokan_vendor_id' ) ) {
                    wc_delete_order_item_meta( $order_item_id, '_msex_dlv_code' );
                    wc_delete_order_item_meta( $order_item_id, '_msex_dlv_name' );
                    wc_delete_order_item_meta( $order_item_id, '_msex_sheet_no' );
                    wc_delete_order_item_meta( $order_item_id, '_msex_register_date' );

                    wc_update_order_item_meta( $order_item_id, '_msex_dlv_code', $dlv_code );
                    wc_update_order_item_meta( $order_item_id, '_msex_dlv_name', $dlv_company['dlv_name'] );
                    wc_update_order_item_meta( $order_item_id, '_msex_sheet_no', $sheet_no );
                    wc_update_order_item_meta( $order_item_id, '_msex_register_date', current_time( 'mysql' ) );
                }

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
                        if ( 'dokan' == wcfm_is_marketplace() ) {
                            msex_update_meta_data( $order, '_msex_dlv_code', $dlv_code );
                            msex_update_meta_data( $order, '_msex_dlv_name', $dlv_company['dlv_name'] );
                            msex_update_meta_data( $order, '_msex_sheet_no', $sheet_no );
                        }

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
                        if ( is_plugin_active( 'dokan-lite/dokan.php' ) ) {
                            MSEX_Dokan_Upload_Sheets::register_sheet_by_order_item_id( $order, $sheet_data['order_item_id'], $sheet_data );
                        } else {
                            self::register_sheet_by_order_item_id( $order, $sheet_data['order_item_id'], $sheet_data );
                        }
                    } else {
                        throw new Exception( sprintf( __( '#%d : 올바르지 않은 주문 아이템 번호입니다.', 'mshop-exporter' ), $sheet_data['order_item_id'] ) );
                    }
                } else if ( ! empty( $sheet_data['order_id'] ) ) {
                    $order = apply_filters( 'msex_get_order', wc_get_order( $sheet_data['order_id'] ), $sheet_data['order_id'] );

                    if ( $order ) {
                        if ( is_plugin_active( 'dokan-lite/dokan.php' ) ) {
                            MSEX_Dokan_Upload_Sheets::register_sheet_by_order( $order, $sheet_data );
                        } else {
                            self::register_sheet_by_order( $order, $sheet_data );
                        }
                    } else {
                        throw new Exception( sprintf( __( '#%d : 올바르지 않은 주문 번호입니다.', 'mshop-exporter' ), $sheet_data['order_id'] ) );
                    }
                }

            }
        }
    }

}
function get_wcfm_importer_url() {
    global $WCFM;

    $wcfm_page = get_wcfm_page();
    $get_wcfm_settings_url = wcfm_get_endpoint_url( 'msex-sheet-importer', '', $wcfm_page );

    return $get_wcfm_settings_url;
}
function msex_get_wcfm_menus($wcfm_menus) {
    $wcfm_menus['msex-sheet-importer'] = array(
        'label' => __( '엠샵 송장 업로드', 'mshop-exporter' ),
        'url' => get_wcfm_importer_url(),
        'icon' => 'shipping-fast',
        'priority' => 36
    );

    return $wcfm_menus;
}
add_filter('wcfm_menus','msex_get_wcfm_menus', 30, 1);
function msex_before_wcfm_load_views ( $end_point ) {
    switch( $end_point ) {
        case 'msex-sheet-importer':
            include('views/wcfm-view-importer.php');
            break;
    }
}
add_action( 'before_wcfm_load_views', 'msex_before_wcfm_load_views' );
function msex_wcfm_query_vars ( $fields ) {
    $wcfm_modified_endpoints = (array) get_option( 'wcfm_endpoints' );
    $fields['msex-sheet-importer'] = ! empty( $wcfm_modified_endpoints['msex-sheet-importer'] ) ? $wcfm_modified_endpoints['msex-sheet-importer'] : 'msex-sheet-importer';

    return $fields;
}
add_filter( 'wcfm_query_vars', 'msex_wcfm_query_vars' );
function msex_wcfm_endpoints_slug ( $fields ) {
    $fields['msex-sheet-importer'] = 'msex-sheet-importer';

    return $fields;
}
add_filter( 'wcfm_endpoints_slug', 'msex_wcfm_endpoints_slug' );
function msex_wcfm_endpoint_title ( $title ) {
    $title = __( '엠샵 송장 업로드', 'mshop-exporter' );

    return $title;
}
add_filter( 'wcfm_endpoint_msex-sheet-importer_title', 'msex_wcfm_endpoint_title' );

function msex_wcfm_endpoint() {
    global $WCFM_Query;

    $WCFM_Query->init_query_vars();
    $WCFM_Query->add_endpoints();

    if( ! get_option( 'wcfm_updated_end_point_msex-sheet-importer' ) ) {
        flush_rewrite_rules();
        update_option( 'wcfm_updated_end_point_msex-sheet-importer', 1 );
    }
}
add_action( 'wcfmaf_affiliate_init', 'msex_wcfm_endpoint' );
function msex_wcfm_get_order ( $order ) {
    if ( $order ) {
        if ( current_user_can( 'manage_woocommerce' ) ) {
            return $order;
        }

        foreach ( $order->get_items() as $order_item ) {
            if( function_exists( 'wcfm_get_vendor_id_by_post' ) ) {
                $vendor_id = wcfm_get_vendor_id_by_post( $order_item->get_product_id() );
                if( get_current_user_id() == $vendor_id ) {
                    return $order;
                }
            }
        }
    }

    return false;
}
add_filter( 'msex_get_order', 'msex_wcfm_get_order' );
function msex_before_wcfm_order_items ( $order_id ) {
    include('views/wcfm-view-meta-box.php');
}
add_action( 'before_wcfm_order_items', 'msex_before_wcfm_order_items' );

add_action( 'woocommerce_after_order_itemmeta', array ( 'MSEX_Meta_Box_Order', 'output_order_item_sheet_info' ), 10, 3 );

function msex_wcfm_styles ( $end_point ) {
    switch( $end_point ) {
        case 'wcfm-orders-details':
            wp_enqueue_style( 'msex_wcfm', MSEX()->plugin_url() . '/assets/css/msex-wcfm.css', array (), MSEX_VERSION );
            wp_enqueue_style( 'msex-meta-box-order', MSEX()->plugin_url() . '/assets/css/admin/msex-meta-box-order.css', array (), MSEX_VERSION );
            break;

        case 'msex-sheet-importer' :
            wp_enqueue_style( 'msex_wcfm', MSEX()->plugin_url() . '/assets/css/msex-wcfm.css', array (), MSEX_VERSION );
            wp_enqueue_style( 'msex_sheet_importer', MSEX()->plugin_url() . '/assets/css/admin/sheet-importer.css', array (), MSEX_VERSION );
            wp_enqueue_style( 'msex-file-upload', MSEX()->plugin_url() . '/assets/css/file-upload.css', array (), MSEX_VERSION );
            break;
    }
}
add_action( 'before_wcfm_load_styles', 'msex_wcfm_styles' );




