<?php

//소스에 URL로 직접 접근 방지
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WC_Payment_Gateway' ) ) {

	class WC_Gateway_NPay extends PAFW_Payment_Gateway {

		protected $order_for_approval = null;

		protected $target_recurrent_id = null;

		protected $product_code = null;

		public function __construct() {
			$this->master_id = 'npay';

			parent::__construct( true );

			$this->pg_title     = __( '네이버페이', 'pgall-for-woocommerce' );
			$this->method_title = __( '네이버페이', 'pgall-for-woocommerce' );
			$test_user_id = trim( pafw_get( $this->settings, 'test_user_id' ) );

			if ( is_checkout() && ! empty( $test_user_id ) ) {
				$user = wp_get_current_user();

				if ( ! current_user_can( 'manage_options' ) && ( empty( $user ) || ! is_user_logged_in() || $user->user_login != $test_user_id ) ) {
					$this->enabled = 'no';
				}
			}

			if ( 'yes' == $this->enabled ) {
				add_action( 'pafw_payment_info_meta_box_action_button_' . $this->id, array( $this, 'add_cash_amount_button' ) );

				add_filter( 'pafw_register_order_params_' . $this->id, array( $this, 'add_register_order_request_params' ), 10, 2 );
				add_filter( 'pafw_cancel_params_' . $this->id, array( $this, 'add_cancel_request_params' ), 10, 2 );

				add_action( 'pafw_approval_response_' . $this->id, array( $this, 'process_approval_response' ), 10, 2 );
			}
		}
		public function get_merchant_id() {
			return pafw_get( $this->settings, 'client_id' );
		}
		public function get_merchant_key() {
			return pafw_get( $this->settings, 'client_secret' );
		}

		function payment_window_mode() {
			if ( wp_is_mobile() ) {
				return 'page';
			} else {
				return 'popup';
			}
		}
		public function get_subscription_meta_key( $meta_key ) {
			return '_pafw_npay_' . $meta_key;
		}
		public function cancel_bill_key_when_change_to_same_payment_method() {
			return false;
		}
		public function get_confirm_day( $order ) {
			$confirm_day = 0;

			if ( is_a( $order, 'WC_Order' ) && 'normal' == pafw_get( $this->settings, 'mall_type', 'normal' ) && 'paid_date' == pafw_get( $this->settings, 'point_method', 'paid_date' ) ) {
				foreach ( $order->get_items() as $item ) {
					$product = $item->get_product();
					if ( $product->get_parent_id() > 0 ) {
						$product = wc_get_product( $product->get_parent_id() );
					}

					if ( is_a( $product, 'WC_Product' ) && 'yes' == $product->get_meta( '_pafw_npay_use_confirm_day' ) ) {
						$item_confirm_day = intval( $product->get_meta( '_pafw_npay_confirm_day' ) );
					} else {
						$item_confirm_day = intval( pafw_get( $this->settings, 'confirm_day', '30' ) );
					}

					$item_confirm_day = apply_filters( 'pafw_npay_get_confirm_day_for_item', $item_confirm_day, $item, $order );

					$confirm_day = max( $confirm_day, $item_confirm_day );
				}
			}

			return apply_filters( 'pafw_npay_get_confirm_day', $confirm_day, $order );
		}
		public function add_register_order_request_params( $params, $order ) {
			$params[ $this->get_master_id() ] = array(
				'product_code' => $this->product_code,
				'partner_id'   => pafw_get( $this->settings, 'partner_id' ),
				'chain_id'     => pafw_get( $this->settings, 'chain_id' ),
				'mall_type'    => pafw_get( $this->settings, 'mall_type', 'normal' ),
				'point_method' => pafw_get( $this->settings, 'point_method', 'paid_date' ),
				'confirm_day'  => $this->get_confirm_day( $order ),
			);

			if ( ! empty( $this->target_recurrent_id ) ) {
				$params[ $this->get_master_id() ]['target_recurrent_id'] = $this->target_recurrent_id;
			}

			return $params;
		}
		public function add_cancel_request_params( $params, $order ) {
			$params[ $this->get_master_id() ] = array(
				'partner_id' => pafw_get( $this->settings, 'partner_id' ),
				'chain_id'   => pafw_get( $this->settings, 'chain_id' ),
			);

			return $params;
		}
		public function add_cash_amount_button( $order ) {
			wp_enqueue_script( 'jquery-ui-dialog' );
			?>
            <input type="button" class="button pafw_action_button tips" id="pafw-cash-amount" name="cash-amount" data-tip="<?php _e( '현금영수증 금액조회', 'pgall-for-woocommerce' ); ?>" value="<?php _e( '현금영수증 금액조회', 'pgall-for-woocommerce' ); ?>">
			<?php
		}
		public function get_payment_form( $order_id = null, $order_key = null ) {
			$order = wc_get_order( $order_id );

			if ( $order->get_total() < 100 ) {
				wp_send_json( array(
					'result'   => 'failure',
					'messages' => __( '100원 미만은 네이버페이로 결제하실 수 없습니다.', 'pgall-for-woocommerce' )
				) );
			}

			return parent::get_payment_form( $order_id, $order_key );
		}
		public function cash_amount() {
			if ( ! is_super_admin() || empty( $_REQUEST['order_id'] ) ) {
				die();
			}

			$order = wc_get_order( absint( wp_unslash( $_REQUEST['order_id'] ) ) );

			$params = array(
				'gateway' => $this->get_gateway_params(),
				'npay'    => array(
					'payment_id'     => wc_clean( $_REQUEST['paymentId'] ),
					'partner_id'     => pafw_get( $this->settings, 'partner_id' ),
					'chain_id'       => pafw_get( $this->settings, 'chain_id' ),
					'transaction_id' => $order->get_transaction_id()
				)
			);

			$params = PAFW_Gateway::call( 'cash_amount', $params, $order, $this );

			ob_start();
			include( 'templates/cash_amount.php' );
			$response = ob_get_clean();

			wp_send_json_success( $response );
		}
		public function request_purchase_confirm( $order ) {
			try {
				if ( 'yes' != $order->get_meta( '_pafw_npay_purchase_confirmed' ) ) {
					$params = array(
						'npay' => array(
							'partner_id'     => pafw_get( $this->settings, 'partner_id' ),
							'chain_id'       => pafw_get( $this->settings, 'chain_id' ),
							'transaction_id' => $order->get_transaction_id()
						)
					);

					$response = PAFW_Gateway::call( 'purchase_confirm', $params, $order, $this );

					$this->add_payment_log( $order, __( '[거래완료 요청 성공]', 'pgall-for-woocommerce' ), array(
						'구매 확정 시간' => $response['confirm_time']
					) );

					$order->update_meta_data( '_pafw_npay_purchase_confirmed', 'yes' );
					$order->save_meta_data();
				}
			} catch ( Exception $e ) {
				$this->add_payment_log( $order, __( '[거래완료 요청 오류]', 'pgall-for-woocommerce' ), array(
					'오류코드'  => $e->getCode(),
					'오뮤메시지' => $e->getMessage()
				), false );
			}
		}
		public function request_earn_point( $order ) {
			try {
				if ( 'yes' != $order->get_meta( '_pafw_npay_point_earned' ) ) {
					$params = array(
						'npay' => array(
							'partner_id'     => pafw_get( $this->settings, 'partner_id' ),
							'chain_id'       => pafw_get( $this->settings, 'chain_id' ),
							'transaction_id' => $order->get_transaction_id()
						)
					);

					PAFW_Gateway::call( 'earn_point', $params, $order, $this );

					$this->add_payment_log( $order, __( '[포인트적립 요청 성공]', 'pgall-for-woocommerce' ) );

					$order->update_meta_data( '_pafw_npay_point_earned', 'yes' );
					$order->save_meta_data();
				}
			} catch ( Exception $e ) {
				$this->add_payment_log( $order, __( '[포인트적립 요청 오류]', 'pgall-for-woocommerce' ), array(
					'오류코드'  => $e->getCode(),
					'오뮤메시지' => $e->getMessage()
				), false );
			}
		}
		public static function maybe_purchase_confirm_or_request_earn_point( $order_id, $old_status, $new_status ) {
			if ( 'yes' == get_option( 'pafw-gw-npay' ) ) {
				$order = wc_get_order( $order_id );

				if ( in_array( $order->get_payment_method(), array( 'npay_easypay', 'npay_subscription' ) ) ) {
					$options = get_option( 'pafw_mshop_npay' );

					if ( 'escrow' == pafw_get( $options, 'mall_type' ) && 'yes' == pafw_get( $options, 'use_purchase_confirm_api', 'yes' ) ) {
						$order_statuses = explode( ',', pafw_get( $options, 'purchase_confirm_order_status', 'completed' ) );

						if ( in_array( $order->get_status(), $order_statuses ) ) {
							$gateway = pafw_get_payment_gateway( $order->get_payment_method() );

							$gateway->request_purchase_confirm( $order );
						}
					} else if ( 'api' == pafw_get( $options, 'point_method', 'paid_date' ) ) {
						$order_statuses = explode( ',', pafw_get( $options, 'earn_point_order_status', 'completed' ) );

						if ( in_array( $order->get_status(), $order_statuses ) ) {
							$gateway = pafw_get_payment_gateway( $order->get_payment_method() );

							$gateway->request_earn_point( $order );
						}
					}
				}
			}
		}
		public function process_approval_response( $order, $response ) {
			$metas = array(
				"_pafw_payment_method" => $response['payment_method'],
				"_pafw_txnid"          => $response['txnid'],
				"_pafw_paid_date"      => $response['paid_date'],
				"_pafw_total_price"    => $response['total_price'],
			);

			if ( 'CARD' == $response['payment_method'] ) {
				$metas = array_merge( $metas, array(
					"_pafw_card_num"  => $response['card_num'],
					"_pafw_card_code" => $response['card_code'],
					"_pafw_card_name" => $response['card_name']
				) );
			} else if ( 'BANK' == $response['payment_method'] ) {
				$metas = array_merge( $metas, array(
					"_pafw_bank_code" => $response['bank_code'],
					"_pafw_bank_name" => $response['bank_name']
				) );
			}

			foreach ( $metas as $key => $value ) {
				$order->update_meta_data( $key, $value );
			}
			$order->save_meta_data();

			$this->add_payment_log( $order, '[ 결제 승인 완료 ]', array(
				'총금액'       => number_format( $response['total_price'] ),
				'결제금액'      => number_format( pafw_get( $response, 'pay_amount', 0 ) ),
				'네이버페이 포인트' => number_format( pafw_get( $response, 'npoint_amount', 0 ) )
			) );
		}
	}
}