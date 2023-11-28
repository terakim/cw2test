<?php

//소스에 URL로 직접 접근 방지
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'PAFW_Settings_NPay_Basic' ) ) {
	class PAFW_Settings_NPay_Basic extends PAFW_Settings_NPay {
		function get_setting_fields() {
			return array(
				array(
					'type'     => 'Section',
					'title'    => '기본 설정',
					'elements' => array(
						array(
							'id'       => 'pc_pay_method',
							'title'    => '결제수단',
							'default'  => 'npay_easypay',
							'type'     => 'Select',
							'multiple' => 'true',
							'options'  => WC_Gateway_PAFW_NPay::get_supported_payment_methods()
						),
					)
				),
				array(
					'type'     => 'Section',
					'title'    => '결제 설정',
					'elements' => array(
						array(
							'id'        => 'operation_mode',
							'title'     => '운영 모드',
							'className' => '',
							'type'      => 'Select',
							'default'   => 'production',
							'options'   => array(
								'sandbox'    => '개발 환경 (Sandbox)',
								'production' => '운영 환경 (Production)'
							)
						),
						array(
							'id'          => 'test_user_id',
							'title'       => '테스트 사용자 아이디',
							'className'   => 'fluid',
							'placeHolder' => '테스트 사용자 아이디를 선택하세요.',
							'type'        => 'Text',
							'default'     => '',
							'desc2'       => __( '<div class="desc2">테스트 사용자 아이디가 지정된 경우, 관리자 및 테스트 사용자에게만 결제수단이 노출됩니다.</div>', 'pgall-for-woocommerce' ),
						),
						array(
							'id'        => 'partner_id',
							'title'     => '파트너 ID',
							'className' => 'fluid',
							'type'      => 'Text',
							'default'   => '',
							'desc2'     => __( '<div class="desc2">네이버페이 가입 후 전달받으신 파트너 아이디를 입력해주세요.</div>', 'pgall-for-woocommerce' ),
						),
						array(
							'id'        => 'client_id',
							'title'     => 'clientId',
							'className' => 'fluid',
							'type'      => 'Text',
							'default'   => '',
							'desc2'     => __( '<div class="desc2">네이버페이 가입 후 전달받으신 clientId를 입력해주세요.</div>', 'pgall-for-woocommerce' ),
						),
						array(
							'id'        => 'client_secret',
							'title'     => 'clientSecret',
							'className' => 'fluid',
							'type'      => 'Text',
							'default'   => '',
							'desc2'     => __( '<div class="desc2">네이버페이 가입 후 전달받으신 clientSecret을 입력해주세요.</div>', 'pgall-for-woocommerce' ),
						),
						array(
							'id'        => 'chain_id',
							'title'     => 'chainId (간편결제)',
							'className' => 'fluid',
							'type'      => 'Text',
							'default'   => '',
							'desc2'     => __( '<div class="desc2">네이버페이에서 그룹형으로 발급한 경우, 전달 받은 간편결제용 chainId값을 설정합니다.</div>', 'pgall-for-woocommerce' ),
						),
						array(
							'id'        => 'chain_id_subscription',
							'showIf'    => array( 'pc_pay_method' => 'npay_subscription' ),
							'title'     => 'chainId (정기/반복결제)',
							'className' => 'fluid',
							'type'      => 'Text',
							'default'   => '',
							'desc2'     => __( '<div class="desc2">네이버페이에서 그룹형으로 발급한 경우, 전달 받은 정기/반복결제용 chainId값을 설정합니다.</div>', 'pgall-for-woocommerce' ),
						)
					)
				),
				array(
					'type'     => 'Section',
					'title'    => '운영 설정',
					'elements' => array(
						array(
							'id'        => 'mall_type',
							'title'     => '가맹점 타입',
							'className' => '',
							'type'      => 'Select',
							'default'   => 'escrow',
							'options'   => array(
								'normal' => '비 에스크로 가맹점',
								'escrow' => '에스크로 가맹점'
							),
							'desc2'     => __( '<div class="desc2">에스크로 타입의 경우 주문처리 완료 시 거래완료 API 연동이 이뤄지며, 비에스크로 타입의 경우 이용완료일 또는 포인트 적립요청 API 연동이 이뤄집니다.</div>', 'pgall-for-woocommerce' ),
						),
						array(
							"id"        => "use_purchase_confirm_api",
							"title"     => __( "거래완료 API 사용", "pgall-for-woocommerce" ),
							'showIf'    => array( 'mall_type' => 'escrow' ),
							"className" => "",
							"type"      => "Toggle",
							"default"   => "yes",
							"desc"      => __( '<div class="desc2">거래완료 API를 사용합니다. 거래완료 API 연동이 필요하지 않은 경우 비활성화 해주시기 바랍니다.</div>', 'pgall-for-woocommerce' )
						),
						array(
							'id'        => 'purchase_confirm_order_status',
							'title'     => __( '거래완료 등록 주문상태', 'pgall-for-woocommerce' ),
							'showIf'    => array( array( 'mall_type' => 'escrow' ), array( 'use_purchase_confirm_api' => 'yes' ) ),
							'className' => '',
							'type'      => 'Select',
							'default'   => 'completed',
							'multiple'  => true,
							'options'   => self::filter_order_statuses( array(
								'pending',
								'on-hold',
								'cancelled',
								'failed',
								'refunded',
							) )
						),
						array(
							'id'        => 'point_method',
							'title'     => '포인트 적립 방식',
							'showIf'    => array( 'mall_type' => 'normal' ),
							'className' => '',
							'type'      => 'Select',
							'default'   => 'paid_date',
							'options'   => array(
								'paid_date' => '이용완료일 기준 적립',
								'api'       => '가맹점 요청 시 적립',
								'auto'      => '자동적립'
							),
							'desc2'     => __( '<div class="desc2">가맹점 제휴 협의 시 선택한 포인트 적립 기준을 지정합니다.<br><span style="color:#ff5858;font-weight: bold;">이용완료일 기준 적립</span> - 이용완료일을 기준으로 포인트가 적립됩니다.<br><span style="color:#ff5858;font-weight: bold;">가맹점 요청 시 적립</span> - 주문이 지정된 상태로 변경될 때 적립됩니다.<br><span style="color:#ff5858;font-weight: bold;">자동적립</span> - 사전 협의에 따라 결제일 기준 15일 혹은 30일 후 자동 적립됩니다.</div>', 'pgall-for-woocommerce' ),
						),
						array(
							'id'        => 'confirm_day',
							'title'     => '이용완료일',
							'showIf'    => array( array( 'mall_type' => 'normal' ), array( 'point_method' => 'paid_date' ) ),
							'className' => '',
							"type"      => "LabeledInput",
							"label"     => __( "일", 'pgall-for-woocommerce' ),
							"default"   => "0",
						),
						array(
							'id'        => 'earn_point_order_status',
							'title'     => __( '포인트 적립 주문상태', 'pgall-for-woocommerce' ),
							'showIf'    => array( array( 'mall_type' => 'normal' ), array( 'point_method' => 'api' ) ),
							'className' => '',
							'type'      => 'Select',
							'default'   => 'completed',
							'multiple'  => true,
							'options'   => self::filter_order_statuses( array(
								'pending',
								'on-hold',
								'cancelled',
								'failed',
								'refunded',
							) )
						)
					)
				)
			);
		}
	}
}
