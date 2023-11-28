<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'PAFW_Settings_Nicepay_Subscription' ) ) {

	class PAFW_Settings_Nicepay_Subscription extends PAFW_Settings_Nicepay {

		function get_setting_fields() {
			return array (
				array (
					'type'     => 'Section',
					'title'    => '정기결제 설정',
					'elements' => array (
						array (
							'id'        => 'nicepay_subscription_title',
							'title'     => '결제수단 이름',
							'className' => 'fluid',
							'type'      => 'Text',
							'default'   => '나이스페이 정기결제',
							'tooltip'   => array (
								'title' => array (
									'content' => __( '결제 페이지에서 구매자들이 결제 진행 시 선택하는 결제수단명 입니다.', 'pgall-for-woocommerce' )
								)
							)
						),
						array (
							'id'        => 'nicepay_subscription_description',
							'title'     => '결제수단 설명',
							'className' => 'fluid',
							'type'      => 'TextArea',
							'default'   => __( '나이스페이 정기결제를 진행합니다.', 'pgall-for-woocommerce' ),
							'tooltip'   => array (
								'title' => array (
									'content' => __( '결제 페이지에서 구매자들이 결제 진행 시 제공되는 결제수단 상세설명 입니다.', 'pgall-for-woocommerce' )
								)
							)
						),
						array (
							'id'        => 'nicepay_subscription_enable_quota',
							'title'     => __( '할부 지원', 'pgall-for-woocommerce' ),
							'className' => '',
							'type'      => 'Toggle',
							'default'   => '',
							'tooltip'   => array (
								'title' => array (
									'content' => __( '신용카드 결제 시 고객이 할부여부를 지정할 수 있습니다.', 'pgall-for-woocommerce' ),
								)
							)
						),
						array (
							'id'        => 'nicepay_subscription_quota',
							'title'     => __( '할부 개월수', 'pgall-for-woocommerce' ),
							'showIf'    => array ( 'nicepay_subscription_enable_quota' => 'yes' ),
							'className' => '',
							'type'      => 'Select',
							'default'   => '',
							'multiple'  => true,
							'options'   => pafw_get_quotas(),
							'tooltip'   => array (
								'title' => array (
									'content' => __( '할부 구매를 허용할 개월수를 선택합니다. 카드사 및 가맹점 정책에 따라 할부 개월수가 제한될 수 있습니다. 할부 구매 미선택시 일시불 결제만 가능합니다.', 'pgall-for-woocommerce' ),
								)
							)
						)
					)
				),
				array (
					'type'     => 'Section',
					'title'    => '정기결제 고급 설정',
					'elements' => array (
						array (
							'id'        => 'nicepay_subscription_use_advanced_setting',
							'title'     => '사용',
							'className' => '',
							'type'      => 'Toggle',
							'default'   => 'no',
							'tooltip'   => array (
								'title' => array (
									'content' => __( '고급 설정 사용 시, 기본 설정에 우선합니다.', 'pgall-for-woocommerce' ),
								)
							)
						),
						array (
							'id'        => 'nicepay_subscription_order_status_after_payment',
							'title'     => __( '결제완료시 변경될 주문상태', 'pgall-for-woocommerce' ),
							'showIf'    => array ( 'nicepay_subscription_use_advanced_setting' => 'yes' ),
							'className' => '',
							'type'      => 'Select',
							'default'   => 'processing',
							'options'   => $this->filter_order_statuses( array (
								'cancelled',
								'failed',
								'on-hold',
								'refunded'
							) ),
							'tooltip'   => array (
								'title' => array (
									'content' => __( '실시간 계좌이체 결제건에 한해서, 결제(입금)이 완료되면 지정된 주문상태로 변경합니다.', 'pgall-for-woocommerce' ),
								)
							)
						),
						array (
							'id'        => 'nicepay_subscription_possible_refund_status_for_mypage',
							'title'     => __( '구매자 주문취소 가능상태', 'pgall-for-woocommerce' ),
							'showIf'    => array ( 'nicepay_subscription_use_advanced_setting' => 'yes' ),
							'className' => '',
							'type'      => 'Select',
							'default'   => 'pending,on-hold',
							'multiple'  => true,
							'options'   => $this->get_order_statuses(),
							'tooltip'   => array (
								'title' => array (
									'content' => __( '실시간 계좌이체 결제건에 한해서, 구매자가 내계정 페이지에서 주문취소 요청을 할 수 있는 주문 상태를 지정합니다.', 'pgall-for-woocommerce' ),
								)
							)
						)
					)
				)
			);
		}
	}
}
