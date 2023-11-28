<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WC_Payment_Gateway' ) ) {
	class WC_Gateway_Nicepay extends PAFW_Payment_Gateway {

		public static $log;

		protected $key_for_test = array(
			'nicepay00m',
			'nictest04m'
		);
		public function __construct() {
			$this->master_id = 'nicepay';

			$this->view_transaction_url = 'https://npg.nicepay.co.kr/issue/IssueLoaderMail.do?TID=%s&type=0';

			$this->pg_title     = __( '나이스페이', 'pgall-for-woocommerce' );
			$this->method_title = __( '나이스페이', 'pgall-for-woocommerce' );

			parent::__construct();

			if ( 'yes' == $this->enabled ) {
				add_filter( 'pafw_register_order_params_' . $this->id, array( $this, 'add_register_order_request_params' ), 10, 2 );
				add_filter( 'pafw_register_shipping_params_' . $this->id, array( $this, 'add_register_shipping_request_params' ), 10, 2 );
				add_action( 'pafw_approval_response_' . $this->id, array( $this, 'process_approval_response' ), 10, 2 );
			}

			add_filter( 'pafw_cash_receipt_params_' . $this->id, array( $this, 'add_cash_receipt_request_params' ), 10, 2 );
		}
		public function get_merchant_id() {
			return pafw_get( $this->settings, 'merchant_id' );
		}
		public function get_merchant_key() {
			return pafw_get( $this->settings, 'merchant_key' );
		}
		public function add_register_order_request_params( $params, $order ) {
			$params[ $this->get_master_id() ] = array(
				'account_date_limit' => pafw_get( $this->settings, 'account_date_limit', 3 ),
				'shopinterest'       => pafw_get( $this->settings, 'shopinterest' ),
				'quota_interest'     => trim( pafw_get( $this->settings, 'quota_interest' ) ),
				'receipt'            => pafw_get( $this->settings, 'receipt' ),
			);

			return $params;
		}
		public function add_cash_receipt_request_params( $params, $order ) {
			$params[ $this->get_master_id() ] = array(
				'reg_num'      => preg_replace( '~\D~', '', $order->get_meta( '_pafw_bacs_receipt_reg_number' ) ),
				'receipt_type' => 'ID' == $order->get_meta( '_pafw_bacs_receipt_usage' ) ? '1' : '2'
			);

			return $params;
		}
		public function add_register_shipping_request_params( $params, $order ) {
			$params[ $this->get_master_id() ] = array(
				'mall_ip'          => $_SERVER['SERVER_ADDR'],
				'sheet_no'         => wc_clean( $_POST['tracking_number'] ),
				'dlv_company_name' => pafw_get( $this->settings, 'delivery_company_name' ),
				'sender_name'      => pafw_get( $this->settings, 'delivery_register_name' )
			);

			return $params;
		}
		public function get_receipt_popup_params() {
			return array(
				'name'     => 'popupIssue',
				'features' => 'toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=420,height=540'
			);
		}
	}
}