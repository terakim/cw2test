<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WC_Payment_Gateway' ) ) {

	if ( ! class_exists( 'WC_Gateway_TossPayments_Subscription' ) ) {

		class WC_Gateway_TossPayments_Subscription extends WC_Gateway_TossPayments {
			public function __construct() {
				$this->id = 'tosspayments_subscription';

				parent::__construct();

				if ( empty( $this->settings['title'] ) ) {
					$this->title       = __( '신용카드 정기결제', 'pgall-for-woocommerce' );
					$this->description = __( '신용카드 정기결제를 진행합니다.', 'pgall-for-woocommerce' );
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
					'pafw_key_in_payment',
				);

				add_action( 'woocommerce_scheduled_subscription_payment_' . $this->id, array( $this, 'woocommerce_scheduled_subscription_payment' ), 10, 2 );
				add_action( 'woocommerce_subscription_status_cancelled', array( $this, 'cancel_subscription' ) );
				add_action( 'woocommerce_subscription_cancelled_' . $this->id, array( $this, 'cancel_subscription' ) );

				add_action( 'woocommerce_subscriptions_pre_update_payment_method', array( $this, 'maybe_remove_subscription_cancelled_callback' ), 10, 3 );
				add_action( 'woocommerce_subscription_payment_method_updated', array( $this, 'maybe_reattach_subscription_cancelled_callback' ), 10, 3 );

				add_filter( 'pafw_subscription_register_complete_params_' . $this->id, array( $this, 'add_subscription_register_complete_params' ), 10, 2 );

				add_filter( 'pafw_bill_key_params_' . $this->id, array( $this, 'add_bill_key_request_params' ), 10, 2 );
				add_filter( 'pafw_subscription_payment_params_' . $this->id, array( $this, 'add_subscription_payment_request_params' ), 10, 2 );

				add_action( 'pafw_subscription_payment_response_' . $this->id, array( $this, 'process_subscription_payment_response' ), 10, 2 );
			}
			public function add_subscription_payment_request_params( $params, $order ) {
				$params[ $this->get_master_id() ] = array(
					'secret_key' => pafw_get( $this->settings, 'secret_key' ),
				);

				return $params;
			}
			public function process_subscription_payment_response( $order, $response ) {
				$order->update_meta_data( '_pafw_receipt_url', pafw_get( $response, 'receipt_url' ) );
				$order->save_meta_data();
			}

			function issue_bill_key_mode() {
				return 'api';
			}

			function adjust_settings() {
				$this->settings['merchant_id']    = $this->settings['subscription_merchant_id'];
				$this->settings['operation_mode'] = $this->settings['operation_mode_subscription'];
				$this->settings['client_key']     = $this->settings['subscription_client_key'];
				$this->settings['secret_key']     = $this->settings['subscription_secret_key'];
			}

			public function get_subscription_meta_key( $meta_key ) {
				return '_pafw_tosspayments_' . $meta_key;
			}
			public function add_subscription_register_complete_params( $params, $order ) {
				$user_id      = 0;
				$payment_info = array();

				parse_str( $_POST['params'], $payment_info ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

				$payment_info = apply_filters( 'pafw_subscription_payment_info', $payment_info, $this );

				if ( is_a( $order, 'WC_Order' ) ) {
					$user_id = $order->get_customer_id();
				} else if ( is_a( $order, 'WP_User' ) ) {
					$user_id = $order->ID;
				}

				$params[ $this->get_master_id() ] = array(
					'secret_key'   => pafw_get( $this->settings, 'secret_key' ),
					'order_id'     => 'PAFW-BILL-' . strtoupper( bin2hex( openssl_random_pseudo_bytes( 6 ) ) ),
					'card_no'      => $this->get_card_param( $payment_info, 'card_no' ),
					'expiry_year'  => $this->get_card_param( $payment_info, 'expiry_year' ),
					'expiry_month' => $this->get_card_param( $payment_info, 'expiry_month' ),
					'cert_no'      => $this->get_card_param( $payment_info, 'cert_no' ),
					'password'     => $this->get_card_param( $payment_info, 'card_pw' ),
					'card_type'    => $this->get_card_param( $payment_info, 'card_type' ),
					'user_id'      => $user_id
				);

				return $params;
			}
			public function add_bill_key_request_params( $params, $order ) {
				$user_id = 0;

				if ( 'process_order_pay' == pafw_get( $_REQUEST, 'payment_action' ) && ! empty( pafw_get( $_REQUEST, 'data' ) ) ) {
					$post_params = array();
					parse_str( pafw_get( $_REQUEST, 'data' ), $post_params );
				} else {
					$post_params = wc_clean( $_REQUEST );
				}

				if ( is_a( $order, 'WC_Order' ) ) {
					$user_id = $order->get_customer_id();
				} else if ( is_a( $order, 'WP_User' ) ) {
					$user_id = $order->ID;
				}

				$post_params = apply_filters( 'pafw_subscription_payment_info', $post_params, $this );

				$params[ $this->get_master_id() ] = array(
					'secret_key'   => pafw_get( $this->settings, 'secret_key' ),
					'card_quota'   => $this->get_card_param( $post_params, 'card_quota', '00' ),
					'card_no'      => $this->get_card_param( $post_params, 'card_no' ),
					'expiry_year'  => $this->get_card_param( $post_params, 'expiry_year' ),
					'expiry_month' => $this->get_card_param( $post_params, 'expiry_month' ),
					'cert_no'      => $this->get_card_param( $post_params, 'cert_no' ),
					'password'     => $this->get_card_param( $post_params, 'card_pw' ),
					'card_type'    => $this->get_card_param( $post_params, 'card_type' ),
					'user_id'      => $user_id
				);

				return $params;
			}

			public function payment_fields() {
				if ( $this->is_available() ) {
					ob_start();
					wc_get_template( 'pafw/tosspayments/form-payment-fields.php', array( 'gateway' => $this ), '', PAFW()->template_path() );
					ob_end_flush();
				}
			}

			public function key_in_payment_form() {
				ob_start();
				wc_get_template( 'pafw/tosspayments/form-key-in.php', array( 'gateway' => $this ), '', PAFW()->template_path() );
				ob_end_flush();
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
							$this->clear_txnid( $order );
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

				wc_get_template( 'pafw/tosspayments/card-info.php', array( 'payment_gateway' => $this, 'bill_key' => $bill_key ), '', PAFW()->template_path() );

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

			function key_in_payment() {
				try {
					check_ajax_referer( 'pgall-for-woocommerce' );

					if ( ! current_user_can( 'publish_shop_orders' ) ) {
						throw new Exception( __( '주문 관리 권한이 없습니다.', 'pgall-for-woocommerce' ) );
					}

					$order = wc_get_order( wc_clean( $_POST['order_id'] ) );

					if ( $order ) {
						PAFW_Gateway::issue_bill_key( $order, $this, false );
					}

					wp_send_json_success( '결제가 정상적으로 처리되었습니다.' );
				} catch ( Exception $e ) {
					wp_send_json_error( sprintf( __( '[ 결제실패 ][PAFW-ERR-%s] %s', 'pgall-for-woocommerce' ), $e->getCode(), $e->getMessage() ) );
				}
			}
		}

	}
}
