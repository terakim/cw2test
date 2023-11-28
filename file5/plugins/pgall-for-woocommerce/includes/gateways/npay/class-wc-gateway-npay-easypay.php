<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WC_Payment_Gateway' ) ) {

	if ( ! class_exists( 'WC_Gateway_NPay_Easypay' ) ) {

		class WC_Gateway_NPay_Easypay extends WC_Gateway_NPay{

			public function __construct() {

				$this->id = 'npay_easypay';

				parent::__construct();

				if ( empty( $this->settings['title'] ) ) {
					$this->title       = __( '네이버페이 간편결제', 'pgall-for-woocommerce' );
					$this->description = __( '네이버페이 간편결제로 결제합니다.', 'pgall-for-woocommerce' );
				} else {
					$this->title       = $this->settings['title'];
					$this->description = $this->settings['description'];
				}

				$this->supports[] = 'refunds';
			}
		}
	}

}
