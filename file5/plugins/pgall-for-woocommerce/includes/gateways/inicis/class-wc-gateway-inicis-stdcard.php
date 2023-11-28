<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WC_Gateway_Inicis_Stdcard' ) ) {
	return;
}

class WC_Gateway_Inicis_Stdcard extends WC_Gateway_Inicis {
	public function __construct() {
		$this->id = 'inicis_stdcard';

		parent::__construct();

		if ( empty( $this->settings['title'] ) ) {
			$this->title       = __( '신용카드 결제', 'pgall-for-woocommerce' );
			$this->description = __( '구글크롬, IE, Safari 에서 결제 가능한 웹표준 결제 입니다. 결제를 진행해 주세요.', 'pgall-for-woocommerce' );
		} else {
			$this->title       = $this->settings['title'];
			$this->description = $this->settings['description'];
		}

		$this->supports[] = 'refunds';
	}
	public function get_accept_methods() {
		$accept_methods = parent::get_accept_methods();

		if ( wp_is_mobile() ) {
			$accept_methods[] = 'twotrs_isp=Y';
			$accept_methods[] = 'block_isp=Y';
			$accept_methods[] = 'twotrs_isp_noti=N';
			$accept_methods[] = 'apprun_check=Y';
			$accept_methods[] = 'extension_enable=Y';
			if ( 'yes' == pafw_get( $this->settings, 'cardpoint', 'no' ) ) {
				$accept_methods[] = 'cp_yn=Y';
			}

			if ( 'yes' == pafw_get( $this->settings, 'use_nointerest', 'no' ) ) {
				$accept_methods[] = 'merc_noint=Y';
				$accept_methods[] = 'noint_quota=' . str_replace( ',', '^', pafw_get( $this->settings, 'nointerest' ) );
			}

			if ( 'yes' == pafw_get( $this->settings, 'global_visa3d', 'no' ) ) {
				$accept_methods[] = 'global_visa3d=Y';
			}
		} else {
			if ( 'yes' == pafw_get( $this->settings, 'cardpoint', 'no' ) ) {
				$accept_methods[] = 'cardpoint';
			}
		}

		return $accept_methods;
	}
	public function process_approval_response( $order, $response ) {
		$order->update_meta_data( "_pafw_card_num", $response['card_num'] );
		$order->update_meta_data( "_pafw_card_code", $response['card_code'] );
		$order->update_meta_data( "_pafw_card_bank_code", $response['card_bank_code'] );
		$order->update_meta_data( "_pafw_card_name", $response['card_name'] );
		$order->update_meta_data( "_pafw_card_other_pay_type", $response['card_other_pay_type'] );
		$order->save_meta_data();

		$this->add_payment_log( $order, '[ 결제 승인 완료 ]', array (
			'거래번호' => $response['transaction_id']
		) );
	}
}