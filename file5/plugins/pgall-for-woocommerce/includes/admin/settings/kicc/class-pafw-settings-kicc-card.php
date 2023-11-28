<?php

//소스에 URL로 직접 접근 방지
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'PAFW_Settings_Kicc_Card' ) ) {
	class PAFW_Settings_Kicc_Card extends PAFW_Settings_Kicc {
		function get_quotabase() {
			$quotabase = array ();
			for ( $i = 2; $i < 37; $i ++ ) {
				$quotabase[ $i ] = $i . '개월';
			}

			return $quotabase;
		}
		function get_setting_fields() {
			return array (
				array (
					'type'     => 'Section',
					'title'    => '신용카드 설정',
					'elements' => array (
						array (
							'id'        => 'kicc_card_title',
							'title'     => '결제수단 이름',
							'className' => 'fluid',
							'type'      => 'Text',
							'default'   => '신용카드',
							'tooltip'   => array (
								'title' => array (
									'content' => __( '결제 페이지에서 구매자들이 결제 진행 시 선택하는 결제수단명 입니다.', 'pgall-for-woocommerce' )
								)
							)
						),
						array (
							'id'        => 'kicc_card_description',
							'title'     => '결제수단 설명',
							'className' => 'fluid',
							'type'      => 'TextArea',
							'default'   => "신용카드 결제를 진행 합니다.",
							'tooltip'   => array (
								'title' => array (
									'content' => __( '결제 페이지에서 구매자들이 결제 진행 시 제공되는 결제수단 상세설명 입니다.', 'pgall-for-woocommerce' )
								)
							)
						),
						array (
							'id'        => 'kicc_card_quotabase',
							'title'     => __( '할부 개월수', 'pgall-for-woocommerce' ),
							'className' => '',
							'type'      => 'Select',
							'default'   => '',
							'multiple'  => true,
							'options'   => $this->get_quotabase(),
							'tooltip'   => array (
								'title' => array (
									'content' => __( '할부 구매를 허용할 개월수를 선택합니다. 카드사 및 가맹점 정책에 따라 할부 개월수가 제한될 수 있습니다. 할부 구매 미선택시 일시불 결제만 가능합니다.', 'pgall-for-woocommerce' ),
								)
							)
						),
						array (
							'id'        => 'kicc_card_use_nointerest',
							'title'     => __( '가맹점 부담 무이자 할부 사용', 'pgall-for-woocommerce' ),
							'className' => '',
							'type'      => 'Toggle',
							'default'   => 'no',
							'tooltip'   => array (
								'title' => array (
									'content' => __( '가맹점 부담 무이자 할부 사용은 결제 대행사와 별도 계약이 되어 있어야 이용이 가능합니다.', 'pgall-for-woocommerce' ),
								)
							)
						),
						array (
							'id'        => 'kicc_card_nointerest',
							'title'     => __( '무이자 할부 설정', 'pgall-for-woocommerce' ),
							'showIf'    => array ( 'kicc_card_use_nointerest' => 'yes' ),
							'className' => 'fluid',
							'type'      => 'Text',
							'default'   => '',
							'tooltip'   => array (
								'title' => array (
									'content' => __( '가맹점에서 이자를 부담하고 구매자에게 무이자 할부를 제공하는 기능으로, 설정은 매뉴얼을 참고 해 주세요.', 'pgall-for-woocommerce' ),
								)
							)
						),
						array (
							'id'        => 'kicc_card_cardpoint',
							'title'     => __( '카드 포인트 결제 허용', 'pgall-for-woocommerce' ),
							'className' => '',
							'type'      => 'Toggle',
							'default'   => 'no',
							'tooltip'   => array (
								'title' => array (
									'content' => __( '카드 포인트 결제 허용 여부를 설정 할 수 있습니다. 카드 포인트 결제는 결제 대행사의 별도 계약이 되어 있어야 이용이 가능합니다.', 'pgall-for-woocommerce' ),
								)
							)
						),
					)
				),
				array (
					'type'     => 'Section',
					'title'    => '신용카드 고급 설정',
					'elements' => array (
						array (
							'id'        => 'kicc_card_use_advanced_setting',
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
							'id'        => 'kicc_card_order_status_after_payment',
							'title'     => __( '결제완료시 변경될 주문상태', 'pgall-for-woocommerce' ),
							'showIf'    => array ( 'kicc_card_use_advanced_setting' => 'yes' ),
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
									'content' => __( '신용카드 결제건에 한해서, 결제(입금)이 완료되면 지정된 주문상태로 변경합니다.', 'pgall-for-woocommerce' ),
								)
							)
						),
						array (
							'id'        => 'kicc_card_possible_refund_status_for_mypage',
							'title'     => __( '구매자 주문취소 가능상태', 'pgall-for-woocommerce' ),
							'showIf'    => array ( 'kicc_card_use_advanced_setting' => 'yes' ),
							'className' => '',
							'type'      => 'Select',
							'default'   => 'pending,on-hold',
							'multiple'  => true,
							'options'   => $this->get_order_statuses(),
							'tooltip'   => array (
								'title' => array (
									'content' => __( '신용카드 결제건에 한해서, 구매자가 내계정 페이지에서 주문취소 요청을 할 수 있는 주문 상태를 지정합니다.', 'pgall-for-woocommerce' ),
								)
							)
						)
					)
				)
			);
		}
	}
}