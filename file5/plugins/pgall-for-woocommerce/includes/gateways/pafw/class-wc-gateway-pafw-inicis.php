<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Gateway_PAFW_Inicis' ) ) {

	include_once( 'class-wc-gateway-pafw.php' );
	class WC_Gateway_PAFW_Inicis extends WC_Gateway_PAFW {
		public function __construct() {
			$this->id = 'mshop_inicis';

			$this->init_settings();

			$this->title              = __( 'KG 이니시스', 'pgall-for-woocommerce' );
			$this->method_title       = __( 'KG 이니시스', 'pgall-for-woocommerce' );
			$this->method_description = '<div style="font-size: 0.9em;">이니시스 일반결제 및 간편결제를 이용합니다. (신용카드, 실시간 계좌이체, 가상계좌, 간편결제, 삼성페이, 휴대폰 소액결제, 에스크로, 정기결제)</div>';

			parent::__construct();
		}
		public static function get_supported_payment_methods() {
			return array(
				'inicis_stdcard'        => '신용카드',
				'inicis_stdbank'        => '실시간 계좌이체',
				'inicis_stdvbank'       => '가상계좌',
				'inicis_stdhpp'         => '휴대폰 소액결제',
				'inicis_stdescrow_bank' => '에스크로',
				'inicis_stdsamsungpay'  => '삼성페이',
				'inicis_applepay'       => '애플페이',
				'inicis_subscription'   => '신용카드 정기결제',
			);
		}
		public function admin_options() {

			parent::admin_options();

			$options = get_option( 'pafw_mshop_inicis' );

			$GLOBALS['hide_save_button'] = 'yes' != pafw_get( $options, 'show_save_button', 'no' );

			$settings = $this->get_settings( 'inicis', self::get_supported_payment_methods() );

			$this->enqueue_script();
			wp_localize_script( 'mshop-setting-manager', 'mshop_setting_manager', array(
				'element'  => 'mshop-setting-wrapper',
				'ajaxurl'  => admin_url( 'admin-ajax.php' ),
				'action'   => PAFW()->slug() . '-update_inicis_settings',
				'settings' => $settings
			) );

			?>
            <script>
                jQuery(document).ready(function ($) {
                    $(this).trigger('mshop-setting-manager', ['mshop-setting-wrapper', '200', <?php echo json_encode( $this->get_setting_values( $this->id, $settings ) ); ?>, null, null]);
                });
            </script>

            <div id="mshop-setting-wrapper"></div>
			<?php

		}

		protected function get_key() {
			return pafw_get( $_REQUEST, 'merchant_id' );
		}
	}
}