<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WC_Payment_Gateway' ) ) {

	if ( ! class_exists( 'WC_Gateway_NPay_Subscription' ) ) {

		class WC_Gateway_NPay_Subscription extends WC_Gateway_NPay {

			public function __construct() {

				$this->id = 'npay_subscription';

				parent::__construct();

				if ( empty( $this->settings['title'] ) ) {
					$this->title       = __( '네이버페이 정기/반복결제', 'pgall-for-woocommerce' );
					$this->description = __( '네이버페이 정기/반복결제로 결제합니다.', 'pgall-for-woocommerce' );
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
				add_filter( 'pafw_subscription_payment_params_' . $this->id, array( $this, 'add_subscription_payment_request_params' ), 10, 2 );
				add_filter( 'pafw_cancel_bill_key_params_' . $this->id, array( $this, 'add_cancel_bill_key_request_params' ), 10, 2 );
				add_filter( 'pafw_subscription_register_form_params_' . $this->id, array( $this, 'add_subscription_register_form_params' ), 10, 2 );

				add_action( 'pafw_bill_key_response_' . $this->id, array( $this, 'process_bill_key_response' ), 10, 2 );
				add_action( 'pafw_subscription_payment_response_' . $this->id, array( $this, 'process_subscription_payment_response' ), 10, 2 );

				add_action( 'pafw_' . $this->id . '_register', array( $this, 'wc_api_request_register' ) );
			}

			function adjust_settings() {
				$this->settings['chain_id'] = $this->settings['chain_id_subscription'];
			}
			public function add_bill_key_request_params( $params, $order ) {
				$params['npay'] = array(
					'partner_id'      => pafw_get( $this->settings, 'partner_id' ),
					'chain_id'        => pafw_get( $this->settings, 'chain_id' ),
					'reserve_id'      => wc_clean( $_REQUEST['reserveId'] ),
					'temp_receipt_id' => wc_clean( $_REQUEST['tempReceiptId'] )
				);

				return $params;
			}
			public function add_subscription_payment_request_params( $params, $order ) {
				$params['npay'] = array(
					'partner_id'   => pafw_get( $this->settings, 'partner_id' ),
					'chain_id'     => pafw_get( $this->settings, 'chain_id' ),
					'mall_type'    => pafw_get( $this->settings, 'mall_type', 'normal' ),
					'point_method' => pafw_get( $this->settings, 'point_method', 'paid_date' ),
					'confirm_day'  => $this->get_confirm_day( $order ),
				);

				return $params;
			}
			public function add_cancel_bill_key_request_params( $params, $order ) {
				$params['npay'] = array(
					'partner_id' => pafw_get( $this->settings, 'partner_id' ),
					'chain_id'   => pafw_get( $this->settings, 'chain_id' ),
				);

				return $params;
			}
			public function add_subscription_register_form_params( $params, $order ) {
				$params['npay'] = array(
					'partner_id'          => pafw_get( $this->settings, 'partner_id' ),
					'chain_id'            => pafw_get( $this->settings, 'chain_id' ),
					'target_recurrent_id' => $this->get_bill_key( $order ),
					'product_code'        => $this->get_product_code( $order ),
					'mall_type'           => pafw_get( $this->settings, 'mall_type', 'normal' ),
					'point_method'        => pafw_get( $this->settings, 'point_method', 'paid_date' ),
					'confirm_day'         => $this->get_confirm_day( $order )
				);

				return $params;
			}
			public function add_subscription_register_complete_params( $params, $order ) {
				$params['npay'] = array(
					'partner_id'      => pafw_get( $this->settings, 'partner_id' ),
					'chain_id'        => pafw_get( $this->settings, 'chain_id' ),
					'reserve_id'      => wc_clean( $_REQUEST['reserveId'] ),
					'temp_receipt_id' => wc_clean( $_REQUEST['tempReceiptId'] )
				);

				return $params;
			}

			public function payment_fields() {
				if ( $this->is_available() ) {
					ob_start();
					wc_get_template( 'pafw/npay/form-payment-fields.php', array( 'gateway' => $this ), '', PAFW()->template_path() );
					ob_end_flush();
				}
			}
			function get_product_code( $order ) {
				if ( 'user' == pafw_get( $this->settings, 'management_batch_key', 'subscription' ) ) {
					return get_user_meta( $order->get_customer_id(), '_pafw_npay_product_code', true );
				} else {
					return $order->get_meta( '_pafw_npay_product_code', true );
				}
			}
			function process_payment( $order_id ) {
				$order = wc_get_order( $order_id );

				do_action( 'pafw_process_payment', $order );

				try {
					$bill_key = $this->get_bill_key( $order );

					if ( wcs_order_contains_renewal( $order_id ) || pafw_is_subscription( $order ) ) {
						if ( pafw_is_issue_bill_key_request( $this ) ) {

							if ( ! pafw_is_subscription( $order ) && wcs_order_contains_renewal( $order_id ) ) {
								$subscription = current( wcs_get_subscriptions_for_renewal_order( $order_id ) );
							} else {
								$subscription = $order;
							}

							if ( ! empty( $bill_key ) && ! empty( $this->get_product_code( $subscription ) ) ) {
								$this->target_recurrent_id = $this->get_bill_key( $subscription );
								$this->product_code        = $this->get_product_code( $subscription );
							}

							return parent::process_payment( $order_id );
						} else {
							$this->before_change_payment_method_for_subscription( $order, false );
							$this->after_change_payment_method_for_subscription( $order );

							return array(
								'result'       => 'success',
								'redirect_url' => $order->get_view_order_url()
							);
						}
					} else if ( pafw_is_issue_bill_key_request( $this ) || empty( $bill_key ) ) {
						return parent::process_payment( $order_id );
					} else {
						PAFW_Gateway::request_subscription_payment( $order, $this );

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
			function wc_api_request_payment() {
				$order = null;
				try {
					if ( empty( $_GET['transaction_id'] ) || empty( $_GET['auth_token'] ) || empty( $_GET['order_id'] ) ) {
						throw new Exception( __( '잘못된 요청입니다.', 'pgall-for-woocommerce' ), '9000' );
					}

					$order = $this->get_order( wc_clean( $_GET['order_id'] ) );

					if ( pafw_is_subscription( $order ) ) {
						$this->before_change_payment_method_for_subscription( $order );
					}

					$this->validate_order_status( $order );

					PAFW_Gateway::issue_bill_key( $order, $this );

					if ( pafw_is_subscription( $order ) ) {
						$this->after_change_payment_method_for_subscription( $order );
					}

					if ( 'user' == pafw_get( $this->settings, 'management_batch_key', 'subscription' ) ) {
						$tmp_product_code = get_user_meta( $order->get_customer_id(), '_pafw_npay_tmp_product_code', true );
						update_user_meta( $order->get_customer_id(), '_pafw_npay_product_code', $tmp_product_code );
						delete_user_meta( $order->get_customer_id(), '_pafw_npay_tmp_product_code', $tmp_product_code );
					}

					if ( ! pafw_is_subscription( $order ) ) {
						if( $order->get_total() > 0 ) {
							PAFW_Gateway::request_subscription_payment( $order, $this );
						}else{
							$order->payment_complete();
						}
					}

					PAFW_Gateway::redirect( $order, $this );
				} catch ( Exception $e ) {
					$bill_key = $this->get_bill_key( $order );

					if ( ! empty( $bill_key ) ) {
						$this->cancel_bill_key( $bill_key );

						$subscriptions = wcs_get_subscriptions_for_order( $order );
						foreach ( $subscriptions as $subscription ) {
							$this->clear_bill_key( $subscription, $subscription->get_customer_id() );
							$this->add_payment_log( $subscription, '[ 빌링키 삭제 ]', $this->get_title() );
						}
					}

					$this->handle_exception( $e, $order );
				}
			}
			function register_payment_method() {
				try {
					$user = get_currentuserinfo();

					$response = PAFW_Gateway::get_register_form( $user, $this );

					wp_send_json_success( array_merge( array(
						'result' => 'success'
					), $response ) );

				} catch ( Exception $e ) {
					wc_add_notice( $e->getMessage(), 'error' );
				}
			}
			public function process_subscription_payment_response( $order, $response ) {
				if ( ! defined( 'PAFW_ADDITIONAL_CHARGE' ) ) {
					if ( 'BANK' == $response['payment_method'] ) {
						$order->update_meta_data( "_pafw_bank_code", $response['bank_code'] );
						$order->update_meta_data( "_pafw_bank_name", $response['bank_name'] );
						$order->save_meta_data();
					}
				}
			}
			public function process_bill_key_response( $order, $response ) {
				if ( 'user' == pafw_get( $this->settings, 'management_batch_key', 'subscription' ) ) {
					update_user_meta( $order->get_customer_id(), '_pafw_npay_tmp_product_code', $response['product_code'] );
				} else {
					if ( function_exists( 'wcs_is_subscription' ) ) {
						if ( wcs_is_subscription( $order ) ) {
							$subscriptions = array( $order );
						} else {
							$subscriptions = wcs_get_subscriptions_for_order( $order );
						}

						foreach ( $subscriptions as $subscription ) {
							$subscription->update_meta_data( '_pafw_npay_product_code', $response['product_code'] );
							$subscription->save_meta_data();
						}
					}
				}
			}
			function wc_api_request_register() {
				try {
					$user = null;

					if ( empty( $_GET['transaction_id'] ) || empty( $_GET['auth_token'] ) || empty( $_GET['user_id'] ) ) {
						throw new Exception( __( '잘못된 요청입니다.', 'pgall-for-woocommerce' ), '9000' );
					}

					$user_id = str_replace( 'PAFW-BILL-', '', wc_clean( $_GET['user_id'] ) );

					$user = get_userdata( $user_id );

					PAFW_Gateway::register_complete( $user, $this );

					PAFW_Gateway::redirect( $user, $this );
				} catch ( Exception $e ) {
					$this->handle_exception( $e, $user, false );
				}
			}
		}
	}

}