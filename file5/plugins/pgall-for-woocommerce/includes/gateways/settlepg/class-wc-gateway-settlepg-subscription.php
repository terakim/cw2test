<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WC_Payment_Gateway' ) ) {

	if ( ! class_exists( 'WC_Gateway_SettlePG_Subscription' ) ) {

		class WC_Gateway_SettlePG_Subscription extends WC_Gateway_SettlePG {

			public function __construct() {

				$this->id = 'settlepg_subscription';

				parent::__construct();

				if ( empty( $this->settings['title'] ) ) {
					$this->title       = __( '신용카드', 'pgall-for-woocommerce' );
					$this->description = __( '신용카드로 결제합니다.', 'pgall-for-woocommerce' );
				} else {
					$this->title       = $this->settings['title'];
					$this->description = $this->settings['description'];
				}

				$this->countries = array( 'KR' );
				$this->supports  = array(
					'products',
					'subscriptions',
					'multiple_subscriptions',
					'subscription_cancellation',
					'subscription_suspension',
					'subscription_reactivation',
					'subscription_amount_changes',
					'subscription_date_changes',
					'subscription_payment_method_change_customer',
					'pafw',
					'refunds',
					'pafw_additional_charge',
					'pafw_cancel_bill_key'
				);

				add_action( 'woocommerce_scheduled_subscription_payment_' . $this->id, array( $this, 'woocommerce_scheduled_subscription_payment' ), 10, 2 );
				add_action( 'woocommerce_subscription_status_cancelled', array( $this, 'cancel_subscription' ) );
				add_action( 'woocommerce_subscription_cancelled_' . $this->id, array( $this, 'cancel_subscription' ) );

				add_action( 'woocommerce_subscriptions_pre_update_payment_method', array( $this, 'maybe_remove_subscription_cancelled_callback' ), 10, 3 );
				add_action( 'woocommerce_subscription_payment_method_updated', array( $this, 'maybe_reattach_subscription_cancelled_callback' ), 10, 3 );

				add_filter( 'pafw_bill_key_params_' . $this->id, array( $this, 'add_bill_key_request_params' ), 10, 2 );
				add_filter( 'pafw_subscription_register_complete_params_' . $this->id, array( $this, 'add_bill_key_request_params' ), 10, 2 );

				add_filter( 'pafw_subscription_payment_params_' . $this->id, array( $this, 'add_subscription_payment_request_params' ), 10, 2 );
				add_filter( 'pafw_cancel_bill_key_params_' . $this->id, array( $this, 'add_subscription_payment_request_params' ), 10, 2 );
			}

			function adjust_settings() {
				$this->settings['operation_mode'] = $this->settings['subscription_operation_mode'];
				$this->settings['test_user_id']   = $this->settings['subscription_test_user_id'];
				$this->settings['merchant_id']    = $this->settings['subscription_merchant_id'];
				$this->settings['merchant_key']   = $this->settings['subscription_merchant_key'];
				$this->settings['license_key']    = $this->settings['subscription_license_key'];
			}
			public function add_subscription_payment_request_params( $params, $order ) {
				$params[ $this->get_master_id() ] = array(
					'license_key' => pafw_get( $this->settings, 'license_key' )
				);

				return $params;
			}
			function issue_bill_key_mode() {
				return 'api';
			}
			public function add_bill_key_request_params( $params, $order ) {
				if ( 'process_order_pay' == pafw_get( $_REQUEST, 'payment_action' ) && ! empty( pafw_get( $_REQUEST, 'data' ) ) ) {
					$post_params = array();
					parse_str( pafw_get( $_REQUEST, 'data' ), $post_params );
				} else if ( ! empty( $_REQUEST['params'] ) ) {
					$post_params = array();
					parse_str( pafw_get( $_REQUEST, 'params' ), $post_params );
				} else {
					$post_params = wc_clean( $_REQUEST );
				}

				$post_params = apply_filters( 'pafw_subscription_payment_info', $post_params, $this );

				$params[ $this->get_master_id() ] = array(
					'license_key'     => pafw_get( $this->settings, 'license_key' ),
					'card_quota'      => $this->get_card_param( $post_params, 'card_quota', '00' ),
					'card_no'         => $this->get_card_param( $post_params, 'card_no' ),
					'expiry_year'     => $this->get_card_param( $post_params, 'expiry_year' ),
					'expiry_month'    => $this->get_card_param( $post_params, 'expiry_month' ),
					'cert_no'         => $this->get_card_param( $post_params, 'cert_no' ),
					'password'        => $this->get_card_param( $post_params, 'card_pw' ),
					'card_type'       => $this->get_card_param( $post_params, 'card_type' ),
					'amount'          => $order->get_total(),
					'tax_amount'      => PAFW_Tax::get_tax_amount( $order ),
					'tax_free_amount' => PAFW_Tax::get_tax_free_amount( $order ),
					'vat'             => PAFW_Tax::get_total_tax( $order ),
				);

				if ( ! isset( $params['customer'] ) ) {
					$user_id = get_current_user_id();

					$params['customer'] = array(
						'user_id'    => $user_id,
						'user_name'  => get_user_meta( $user_id, 'billing_first_name', true ) . get_user_meta( $user_id, 'billing_last_name', true ),
						'user_phone' => preg_replace( "/[^0-9]*/s", "", get_user_meta( $user_id, 'billing_phone', true ) ),
						'user_email' => get_user_meta( $user_id, 'billing_email', true ),
						'client_ip'  => $_SERVER['REMOTE_ADDR'],
					);
				}

				return $params;
			}

			public function payment_fields() {
				if ( $this->is_available() ) {
					ob_start();
					wc_get_template( 'pafw/settlepg/form-payment-fields.php', array( 'gateway' => $this ), '', PAFW()->template_path() );
					ob_end_flush();
				}
			}
			public function maybe_clear_bill_key( $response, $order, $gateway, $user_id ) {
				$this->before_change_payment_method_for_subscription( $order, pafw_is_issue_bill_key_request( $this ) );
			}
			function process_payment( $order_id ) {
				$order = wc_get_order( $order_id );

				do_action( 'pafw_process_payment', $order );

				try {
					$bill_key = $this->get_bill_key( $order );

					if ( pafw_is_subscription( $order ) ) {
						add_action( 'pafw_before_update_bill_key', array( $this, 'maybe_clear_bill_key' ), 10, 4 );
						if ( pafw_is_issue_bill_key_request( $this ) ) {
							PAFW_Gateway::issue_bill_key( $order, $this );
						}

						$this->after_change_payment_method_for_subscription( $order );

						return array(
							'result'       => 'success',
							'redirect_url' => $order->get_view_order_url()
						);
					} else {
						if ( pafw_is_issue_bill_key_request( $this ) || empty( $bill_key ) ) {
							PAFW_Gateway::issue_bill_key( $order, $this );
						}

						$order->set_payment_method( $this );

						if ( $order->get_total() > 0 ) {
							PAFW_Gateway::request_subscription_payment( $order, $this );
						} else {
							$order->payment_complete();
						}

						return array(
							'result'       => 'success',
							'redirect_url' => $order->get_checkout_order_received_url()
						);
					}
				} catch ( Exception $e ) {
					$message = sprintf( "[결제오류] %s [%s]", $e->getMessage(), $e->getCode() );

					$order->add_order_note( $message );

					do_action( 'pafw_payment_fail', $order, $e->getCode(), $e->getMessage() );

					throw $e;
				}
			}

			public function subscription_payment_info() {
				$bill_key = get_user_meta( get_current_user_id(), $this->get_subscription_meta_key( 'bill_key' ), true );

				ob_start();

				wc_get_template( 'pafw/settlepg/card-info.php', array( 'payment_gateway' => $this, 'bill_key' => $bill_key ), '', PAFW()->template_path() );

				return ob_get_clean();
			}
			function register_payment_method() {
				try {
					$user = get_currentuserinfo();

					PAFW_Gateway::register_complete( $user, $this );

					wp_send_json_success();
				} catch ( Exception $e ) {
					wp_send_json_error( $e->getMessage() );
				}
			}
		}
	}

}
