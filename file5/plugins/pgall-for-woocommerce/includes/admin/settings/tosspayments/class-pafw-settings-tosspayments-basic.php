<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'PAFW_Settings_TossPayments_Basic' ) ) {

	class PAFW_Settings_TossPayments_Basic extends PAFW_Settings_TossPayments {

		function get_setting_fields() {
			return array(
				array(
					'type'     => 'Section',
					'title'    => '기본 설정',
					'elements' => array(
						array(
							'id'       => 'pc_pay_method',
							'title'    => '결제수단',
							'default'  => 'tosspayments_card,tosspayments_bank,tosspayments_vbank',
							'type'     => 'Select',
							'multiple' => 'true',
							'options'  => WC_Gateway_PAFW_TossPayments::get_supported_payment_methods()
						),

					)
				),
				array(
					'type'     => 'Section',
					'title'    => '결제 설정',
					'elements' => array(
						array(
							'id'      => 'operation_mode',
							'title'   => '운영 모드',
							'type'    => 'Select',
							'default' => 'sandbox',
							'options' => array(
								'sandbox'    => '개발 환경 (Sandbox)',
								'production' => '운영 환경 (Production)'
							),
						),
						array(
							'id'          => 'test_user_id',
							'title'       => '테스트 사용자 아이디',
							'className'   => 'fluid',
							'placeHolder' => '테스트 사용자 아이디를 선택하세요.',
							'showIf'      => array( 'operation_mode' => 'sandbox' ),
							'type'        => 'Text',
							'default'     => 'pgall_test_user',
							'desc2'       => __( '<div class="desc2">개발 환경 (Sandbox) 모드에서는 관리자 및 테스트 사용자에게만 결제수단이 노출됩니다.</div>', 'pgall-for-woocommerce' ),
						),
						array(
							'id'        => 'merchant_id',
							'title'     => '상점 아이디',
							'className' => 'fluid',
							'default'   => 'tvivarepublica',
							'desc2'     => __( '<div class="desc2">결제 테스트용 상점 아이디는 <code>tvivarepublica</code> 입니다.<br>실 결제용 상점 아이디는 <code>CDM_</code>로 시작해야 합니다.</div>', 'pgall-for-woocommerce' ),
							'type'      => 'Text'
						),
						array(
							'id'        => 'merchant_key',
							'title'     => '상점키',
							'className' => 'fluid',
							'default'   => 'b495c00ba8fcd62b18d69870c2c26979',
							'desc2'     => __( '<div class="desc2">결제 테스트용 상점키는 <code>b495c00ba8fcd62b18d69870c2c26979</code> 입니다.</div>', 'pgall-for-woocommerce' ),
							'type'      => 'Text'
						),
						array(
							'id'        => 'client_key',
							'title'     => '클라이언트 키',
							'className' => 'fluid',
							'default'   => 'test_ck_D5GePWvyJnrK0W0k6q8gLzN97Eoq',
							'desc2'     => __( '<div class="desc2">결제 테스트용 클라이언트 키는 <code>test_ck_D5GePWvyJnrK0W0k6q8gLzN97Eoq</code> 입니다.<br>실 결제용 클라이언트 키는 <code>CDM_</code>로 시작해야 합니다.</div>', 'pgall-for-woocommerce' ),
							'type'      => 'Text'
						),
						array(
							'id'        => 'secret_key',
							'title'     => '시크릿 키',
							'className' => 'fluid',
							'default'   => 'test_sk_zXLkKEypNArWmo50nX3lmeaxYG5R',
							'desc2'     => __( '<div class="desc2">결제 시크릿 키는 <code>test_sk_zXLkKEypNArWmo50nX3lmeaxYG5R</code> 입니다.</div>', 'pgall-for-woocommerce' ),
							'type'      => 'Text'
						)
					)
				),
				array(
					'type'     => 'Section',
					'title'    => '정기결제 설정',
					'showIf'   => array( 'pc_pay_method' => 'tosspayments_subscription' ),
					'elements' => array(
						array(
							'id'        => 'operation_mode_subscription',
							'title'     => '동작모드',
							'className' => '',
							'type'      => 'Select',
							'default'   => 'sandbox',
							'options'   => array(
								'sandbox'    => '개발 환경 (Sandbox)',
								'production' => '운영 환경 (Production)'
							)
						),
						array(
							'id'          => 'test_user_id_subscription',
							'title'       => '테스트 사용자 아이디',
							'className'   => 'fluid',
							'placeHolder' => '테스트 사용자 아이디를 선택하세요.',
							'showIf'      => array( 'operation_mode_subscription' => 'sandbox' ),
							'type'        => 'Text',
							'default'     => 'pgall_test_user',
							'desc2'       => __( '<div class="desc2">개발 환경 (Sandbox) 모드에서는 관리자 및 테스트 사용자에게만 결제수단이 노출됩니다.</div>', 'pgall-for-woocommerce' ),
						),
						array(
							'id'        => 'subscription_merchant_id',
							'title'     => '상점 아이디',
							'className' => 'fluid',
							'default'   => 'tvivarepublica',
							'desc2'     => __( '<div class="desc2">결제 테스트용 상점 아이디는 <code>tvivarepublica</code> 입니다.<br>실 결제용 상점 아이디는 <code>CDM_</code>로 시작해야 합니다.</div>', 'pgall-for-woocommerce' ),
							'type'      => 'Text'
						),
						array(
							'id'        => 'subscription_client_key',
							'title'     => '클라이언트 키',
							'className' => 'fluid',
							'default'   => 'test_ck_D5GePWvyJnrK0W0k6q8gLzN97Eoq',
							'desc2'     => __( '<div class="desc2">결제 테스트용 클라이언트 키는 <code>test_ck_D5GePWvyJnrK0W0k6q8gLzN97Eoq</code> 입니다.<br>실 결제용 클라이언트 키는 <code>CDM_</code>로 시작해야 합니다.</div>', 'pgall-for-woocommerce' ),
							'type'      => 'Text'
						),
						array(
							'id'        => 'subscription_secret_key',
							'title'     => '시크릿 키',
							'className' => 'fluid',
							'default'   => 'test_sk_zXLkKEypNArWmo50nX3lmeaxYG5R',
							'desc2'     => __( '<div class="desc2">결제 시크릿 키는 <code>test_sk_zXLkKEypNArWmo50nX3lmeaxYG5R</code> 입니다.</div>', 'pgall-for-woocommerce' ),
							'type'      => 'Text'
						),
						array(
							'id'        => 'management_batch_key',
							'title'     => '빌키 관리',
							'className' => '',
							'type'      => 'Select',
							'default'   => 'subscription',
							'options'   => array(
								'subscription' => '정기결제권',
								'user'         => '사용자'
							),
						),
						array(
							"id"        => "user_can_delete_batch_key",
							"title"     => __( "결제수단 삭제 허용", "pgall-for-woocommerce" ),
							'showIf'    => array( 'management_batch_key' => 'user' ),
							"className" => "",
							"type"      => "Toggle",
							"default"   => "no",
							"desc"      => __( '<div class="desc2">고객은 "내계정 - 결제수단 관리 페이지"에서 자신이 등록한 결제수단을 삭제할 수 있습니다.</div>', 'pgall-for-woocommerce' )
						)
					)
				),
				array(
					'type'     => 'Section',
					'title'    => '페이팔(Paypal) 설정',
					'showIf'   => array( 'pc_pay_method' => 'tosspayments_paypal' ),
					'elements' => array(
						array(
							'id'        => 'paypal_merchant_id',
							'title'     => '상점 아이디',
							'className' => 'fluid',
							'default'   => 'tvivarepublica',
							'desc2'     => __( '<div class="desc2">결제 테스트용 상점 아이디는 <code>tvivarepublica</code> 입니다.<br>실 결제용 상점 아이디는 <code>CDM_</code>로 시작해야 합니다.</div>', 'pgall-for-woocommerce' ),
							'type'      => 'Text'
						),
						array(
							'id'        => 'paypal_client_key',
							'title'     => '클라이언트 키',
							'className' => 'fluid',
							'default'   => 'test_ck_BE92LAa5PVb1wPvWGxe37YmpXyJj',
							'desc2'     => __( '<div class="desc2">결제 테스트용 클라이언트 키는 <code>test_ck_BE92LAa5PVb1wPvWGxe37YmpXyJj</code> 입니다.<br>실 결제용 클라이언트 키는 <code>CDM_</code>로 시작해야 합니다.</div>', 'pgall-for-woocommerce' ),
							'type'      => 'Text'
						),
						array(
							'id'        => 'paypal_secret_key',
							'title'     => '시크릿 키',
							'className' => 'fluid',
							'default'   => 'test_sk_N5OWRapdA8d7wP41EbYro1zEqZKL',
							'desc2'     => __( '<div class="desc2">결제 시크릿 키는 <code>test_sk_N5OWRapdA8d7wP41EbYro1zEqZKL</code> 입니다.</div>', 'pgall-for-woocommerce' ),
							'type'      => 'Text'
						)
					)
				)
			);
		}
	}
}
