<?php



if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'PAFW_Admin_Users' ) ) {

	class PAFW_Admin_Users {

		public function __construct() {
			add_action( 'init', array( $this, 'init' ) );
			add_action( 'init', array( $this, 'delete_card' ) );
		}

		public function init() {
			add_filter( 'manage_users_columns', array( $this, 'manage_users_columns' ), 999 );
			add_filter( 'manage_users_custom_column', array( $this, 'manage_users_custom_column' ), 10, 3 );
		}

		function manage_users_custom_column( $value, $column_name, $user_id ) {
			if ( 'pafw_card_info' == $column_name ) {
				$payment_gateways = PAFW_Bill_Key::get_payment_gateways();

				if ( ! empty( $payment_gateways ) ) {
					ob_start();

					foreach ( $payment_gateways as $payment_gateway ) {
						$bill_key = get_user_meta( $user_id, $payment_gateway->get_subscription_meta_key( 'bill_key' ), true );

						if ( ! empty( $bill_key ) ) {
							$url = wp_nonce_url( add_query_arg( array( 'user_id' => $user_id, 'payment_method' => $payment_gateway->id, 'action' => 'pafw_delete_card' ), remove_query_arg( array( 'user_id', 'action', 'payment_method' ) ) ), 'pafw_delete_card' );

							?>
                            <div style="display: flex; font-size: 12px; align-items: center; margin-bottom: 2px;">
                                <span style="flex: 1;"><?php printf( "[%s]", $payment_gateway->get_title() ); ?></span>
                                <a href="<?php echo $url; ?>" class="button" style="font-size: 12px;padding: 2px 5px;" onclick="return confirm( '<?php _e( '등록된 결제수단을 삭제하시겠습니까?', 'pgall-for-woocommerce' ); ?>' );"><?php _e( '삭제하기', 'pgall-for-woocommerce' ); ?></a>
                            </div>
							<?php
						}
					}

					return ob_get_clean();
				}
			}

			return $value;
		}

		function manage_users_columns( $users_columns ) {
			$users_columns['pafw_card_info'] = __( '결제수단', 'pgall-for-woocommerce' );

			return $users_columns;
		}

		function delete_card() {
			if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'pafw_delete_card' ) ) {
				return;
			}

			if ( ! current_user_can( 'edit_users' ) ) {
				return;
			}

			if ( isset( $_GET['action'] ) && isset( $_GET['user_id'] ) && 'pafw_delete_card' == $_GET['action'] ) {

				$gateway = pafw_get_payment_gateway( $_GET['payment_method'] );


				if ( $gateway ) {
					$bill_key = get_user_meta( $_GET['user_id'], $gateway->get_subscription_meta_key( 'bill_key' ), true );

					if ( ! empty( $bill_key ) ) {
						try {
							$gateway->cancel_bill_key( $bill_key );
						} catch ( Exception $e ) {

						}
					}

					$gateway->clear_bill_key( null, $_GET['user_id'] );
				}

				wp_safe_redirect( remove_query_arg( array( 'user_id', 'action', 'payment_method' ) ) );
				die();
			}
		}
	}

	return new PAFW_Admin_Users();

}

