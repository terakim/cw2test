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

class MSEX_Dokan_Ajax {
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
                'dokan_upload_sheets'               => false,
                'dokan_register_sheets'             => false,
                'dokan_delete_sheet_info'           => false,
                'dokan_update_sheet_info'           => false,
            ) );
        }

        foreach ( $ajax_events as $ajax_event => $nopriv ) {
            add_action( 'wp_ajax_' . msex_ajax_command( $ajax_event ), array( __CLASS__, $ajax_event ) );
            if ( $nopriv ) {
                add_action( 'wp_ajax_nopriv_' . msex_ajax_command( $ajax_event ), array( __CLASS__, $ajax_event ) );
            }
        }
    }

    public static function dokan_upload_sheets() {
        check_ajax_referer( 'mshop-exporter' );

        if ( ! current_user_can( 'manage_woocommerce' ) && ! dokan_is_user_seller( get_current_user_id() ) ) {
            die( - 1 );
        }

        MSEX_Upload_Sheets::process_csv();

        wp_send_json_success();
    }

    public static function dokan_register_sheets() {
        try {
            if ( ! current_user_can( 'manage_woocommerce' ) && ! dokan_is_user_seller( get_current_user_id() ) ) {
                throw new Exception( __( '권한이 없습니다.', 'mshop-exporter' ) );
            }

            MSEX_Dokan_Upload_Sheets::register_sheets();
            wp_send_json_success();
        } catch ( Exception $e ) {
            wp_send_json_error( $e->getMessage() );
        }
    }
    public static function dokan_delete_sheet_info() {
        check_ajax_referer( 'mshop-exporter' );

        if ( ! current_user_can( 'manage_woocommerce' ) && ! dokan_is_user_seller( get_current_user_id() ) ) {
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

            wp_send_json_success();
        } else {
            wp_send_json_error( __( '주문 정보를 찾을 수 없습니다.', 'mshop-exporter' ) );
        }

    }
    public static function dokan_update_sheet_info() {
        check_ajax_referer( 'mshop-exporter' );

        if ( ! current_user_can( 'manage_woocommerce' ) && ! dokan_is_user_seller( get_current_user_id() ) ) {
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

            $order_status = get_option( 'msex_order_status_after_shipping', 'wc-shipping' );

            $order->add_order_note( sprintf( __( '송장정보가 등록되었습니다. [ %s : %s ],', 'mshop-exporter' ), $dlv_code, $sheet_no ) );
            $order->update_status( $order_status );

            wp_send_json_success();
        } else {
            wp_send_json_error( __( '주문 정보를 찾을 수 없습니다.', 'mshop-exporter' ) );
        }

    }
}

MSEX_Dokan_Ajax::init();


if ( ! class_exists( 'MSEX_Upload_Sheets' ) ) {
    class MSEX_Dokan_Upload_Sheets {
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

                $parent_order = wc_get_order( $order->get_parent_id() );

                if ( $parent_order ) {
                    foreach ( $parent_order->get_items() as $item_id => $item ) {
                        if( get_current_user_id() == get_post( $item->get_product_id() )->post_author ) {
                            if( $item->get_product_id() == wcs_get_order_items_product_id( $order_item_id ) ) {
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

    }
}
function msex_dokan_load_document_menu( $query_vars ) {
    $query_vars['msex'] = 'msex';
    return $query_vars;
}
add_filter( 'dokan_query_var_filter', 'msex_dokan_load_document_menu' );
function dokan_add_msex_menu( $urls ) {
    $urls['msex'] = array(
        'title' => __( '엠샵 송장 업로드', 'mshop-exporter' ),
        'icon'  => '<i class="fa fa-user"></i>',
        'url'   => dokan_get_navigation_url( 'msex' ),
        'pos'   => 51
    );
    return $urls;
}
add_filter( 'dokan_get_dashboard_nav', 'dokan_add_msex_menu' );
function msex_dokan_load_template( $query_vars ) {
    if ( isset( $query_vars['msex'] ) ) {
        include('views/dokan-view-importer.php');
    }
}
add_action( 'dokan_load_custom_template', 'msex_dokan_load_template' );
function msex_dokan_get_order( $order ) {
    if ( $order ) {
        if ( current_user_can( 'manage_woocommerce' ) || get_current_user_id() == $order->get_meta( '_dokan_vendor_id' ) ) {
            return $order;
        }
    }

    return false;
}
add_filter( 'msex_get_order', 'msex_dokan_get_order' );
function msex_dokan_order_detail( $order ) {
    include('views/dokan-view-meta-box.php');
}
add_action( 'dokan_order_detail_after_order_notes', 'msex_dokan_order_detail' );