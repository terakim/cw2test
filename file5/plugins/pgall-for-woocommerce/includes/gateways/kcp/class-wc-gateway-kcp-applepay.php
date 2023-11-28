<?php

//소스에 URL로 직접 접근 방지
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Gateway_Kcp_Applepay' ) ) :

	class WC_Gateway_Kcp_Applepay extends WC_Gateway_Kcp {

		public function __construct() {
			$this->id = 'kcp_applepay';

			parent::__construct();

			$this->settings['bills_cmd'] = 'card_bill';

			if ( empty( $this->settings['title'] ) ) {
				$this->title       = __( '애플페이 결제', 'pgall-for-woocommerce' );
				$this->description = __( '애플페이로 결제합니다.', 'pgall-for-woocommerce' );
			} else {
				$this->title       = $this->settings['title'];
				$this->description = $this->settings['description'];
			}
		}
		public function is_available() {
			if ( wp_is_mobile() ) {
				$available = preg_match( "/iPhone|iPad/", $_SERVER['HTTP_USER_AGENT'] );
			} else {
				$available = false;
//				$available = str_contains( $_SERVER['HTTP_USER_AGENT'], 'Macintosh' ) && ! str_contains( $_SERVER['HTTP_USER_AGENT'], 'Chrome' );
			}

			return parent::is_available() && $available;
		}
		public function process_approval_response( $order, $response ) {
			$order->update_meta_data( "_pafw_card_num", $response['card_num'] );
			$order->update_meta_data( "_pafw_card_code", $response['card_code'] );
			$order->update_meta_data( "_pafw_card_bank_code", $response['card_bank_code'] );
			$order->update_meta_data( "_pafw_card_name", $response['card_name'] );
			$order->update_meta_data( "_pafw_card_other_pay_type", $response['card_other_pay_type'] );
			$order->save_meta_data();

			$this->add_payment_log( $order, '[ 결제 승인 완료 ]', array(
				'거래번호' => $response['transaction_id']
			) );
		}
	}

endif;