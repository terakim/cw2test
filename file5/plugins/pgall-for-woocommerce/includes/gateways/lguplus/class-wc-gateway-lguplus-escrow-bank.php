<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WC_Payment_Gateway' ) ) {

	if ( ! class_exists( 'WC_Gateway_Lguplus_Escrow_Bank' ) ) {

		class WC_Gateway_Lguplus_Escrow_Bank extends WC_Gateway_Lguplus {
			public function __construct() {
				$this->id = 'lguplus_escrow_bank';

				parent::__construct();

				$this->method_title = __( '실시간계좌이체(에스크로)', 'pgall-for-woocommerce' );

				if ( empty( $this->settings['title'] ) ) {
					$this->title       = __( '실시간계좌이체(에스크로)', 'pgall-for-woocommerce' );
					$this->description = __( '에스크로 방식으로 계좌에서 바로 결제하는 에스크로 실시간 계좌이체 입니다.', 'pgall-for-woocommerce' );
				} else {
					$this->title       = $this->settings['title'];
					$this->description = $this->settings['description'];
				}
				$this->supports[] = 'pafw-cash-receipt';
				$this->supports[] = 'pafw-escrow';
			}
			public function process_approval_response( $order, $response ) {
				$order->update_meta_data( "_pafw_bank_code", $response['bank_code'] );
				$order->update_meta_data( "_pafw_bank_name", $response['bank_name'] );
				$order->update_meta_data( "_pafw_cash_receipts", $response['cash_receipts'] );
				$order->save_meta_data();

				$this->add_payment_log( $order, '[ 결제 승인 완료 ]', array (
					'거래번호' => $response['transaction_id']
				) );
			}
//
//			function get_register_delivery_url() {
//				if ( 'production' == pafw_get( $this->settings, 'operation_mode', 'sandbox' ) ) {
//					return 'http://pgweb.uplus.co.kr/pg/wmp/mertadmin/jsp/escrow/rcvdlvinfo.jsp';
//				} else {
//					return 'http://pgweb.uplus.co.kr:7085/pg/wmp/mertadmin/jsp/escrow/rcvdlvinfo.jsp';
//				}
//			}
//
//			/**
//			 * 에스크로 배송정보를 등록(수정) 한다.
//			 *
//			 * @throws Exception
//			 */
//			function escrow_register_delivery_info() {
//				$this->check_shop_order_capability();
//
//				$order = $this->get_order();
//
//				/** @var 'I'|'U' $escrow_type - 'I' : 배송등록, 'U' : 배송정보수정 */
//				$escrow_type     = isset( $_REQUEST['escrow_type'] ) ? $_REQUEST['escrow_type'] : '';
//				$tracking_number = isset( $_REQUEST['tracking_number'] ) ? $_REQUEST['tracking_number'] : '';
//
//				if ( empty( $tracking_number ) || empty( $escrow_type ) ) {
//					throw new Exception( __( '필수 파라미터가 누락되었습니다.', 'pgall-for-woocommerce' ) );
//				}
//
//				$url = $this->get_register_delivery_url();
//
//				$mid         = ( 'sandbox' == pafw_get( $this->settings, 'operation_mode', 'sandbox' ) ? 't' : '' ) . $this->merchant_id;
//				$oid         = $order->get_meta( '_pafw_txnid' );
//				$dlvdate     = date( 'YmdHi', strtotime( current_time( 'mysql' ) ) );
//				$dlvcompcode = $this->delivery_company_name;
//				$dlvno       = $tracking_number;
//				$mertkey      = $this->merchant_key;
//
//				$hashdata = MD5( $mid . $oid . $dlvdate . $dlvcompcode . $dlvno . $mertkey );
//
//				$params = array (
//					'mid'          => $mid,
//					'oid'          => $oid,
//					'dlvtype'      => '03',
//					'dlvdate'      => $dlvdate,
//					'dlvcompcode'  => $dlvcompcode,
//					'dlvno'        => $tracking_number,
//					'dlvworker'    => $this->delivery_sender_name,
//					'dlvworkertel' => $this->delivery_sender_phone,
//					'hashdata'     => $hashdata
//				);
//
//				$response = wp_remote_post( $url, array (
//					'method'      => 'POST',
//					'timeout'     => 45,
//					'redirection' => 5,
//					'httpversion' => '1.0',
//					'blocking'    => true,
//					'headers'     => array (),
//					'body'        => $params,
//					'cookies'     => array ()
//				) );
//
//				if ( 0 === strpos( 'OK', trim( $response['body'] ) ) ) {
//					$order->update_meta_data( '_pafw_escrow_tracking_number', $tracking_number );
//					$order->update_meta_data( '_pafw_escrow_register_delivery_info', 'yes' );
//					$order->update_meta_data( '_pafw_escrow_register_delivery_time', current_time( 'mysql' ) );
//					$order->save_meta_data();
//
//					$order->add_order_note( __( '판매자님께서 고객님의 에스크로 결제 주문을 배송 등록 또는 수정 처리하였습니다.', 'pgall-for-woocommerce' ), true );
//					$order->update_status( $this->order_status_after_enter_shipping_number );
//				} else {
//					throw new Exception( sprintf( __( '배송등록중 오류가 발생했습니다. %s', 'pgall-for-woocommerce' ), mb_convert_encoding( trim( $response['body'] ), "UTF-8", "EUC-KR" ) ) );
//				}
//
//				wp_send_json_success( __( '배송등록이 처리되었습니다.', 'pgall-for-woocommerce' ) );
//			}

		}

	}

} // class_exists function end