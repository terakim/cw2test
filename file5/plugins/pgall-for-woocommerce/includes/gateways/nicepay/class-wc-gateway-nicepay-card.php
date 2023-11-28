<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WC_Payment_Gateway' ) ) {

	if ( ! class_exists( 'WC_Gateway_Nicepay_Card' ) ) {

		class WC_Gateway_Nicepay_Card extends WC_Gateway_Nicepay {

			public function __construct() {
				$this->id = 'nicepay_card';

				parent::__construct();

				if ( empty( $this->settings['title'] ) ) {
					$this->title       = __( '신용카드', 'pgall-for-woocommerce' );
					$this->description = __( '카드사를 통해 결제를 진행합니다.', 'pgall-for-woocommerce' );
				} else {
					$this->title       = $this->settings['title'];
					$this->description = $this->settings['description'];
				}

				$this->supports[] = 'refunds';
			}
			public function process_approval_response( $order, $response ) {
				$order->update_meta_data( "_pafw_card_num", $response['card_num'] );
				$order->update_meta_data( "_pafw_card_code", $response['card_code'] );
				$order->update_meta_data( "_pafw_card_bank_code", $response['card_bank_code'] );
				$order->update_meta_data( "_pafw_card_name", $response['card_name'] );
				$order->save_meta_data();

				$this->add_payment_log( $order, '[ 결제 승인 완료 ]', array (
					'거래번호' => $response['transaction_id']
				) );
			}
		}
	}

} // class_exists function end
