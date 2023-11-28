<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'PAFW_Settings_Nicepay_Escrow_Bank' ) ) {

	class PAFW_Settings_Nicepay_Escrow_Bank extends PAFW_Settings_Nicepay {

		function get_setting_fields() {
			return array (
				array (
					'type'     => 'Section',
					'title'    => '에스크로 설정',
					'elements' => array (
						array (
							'id'        => 'nicepay_escrow_bank_title',
							'title'     => '결제수단 이름',
							'className' => 'fluid',
							'type'      => 'Text',
							'default'   => __( '에스크로', 'pgall-for-woocommerce' ),
							'tooltip'   => array (
								'title' => array (
									'content' => __( '결제 페이지에서 구매자들이 결제 진행 시 선택하는 결제수단명 입니다.', 'pgall-for-woocommerce' )
								)
							)
						),
						array (
							'id'        => 'nicepay_escrow_bank_description',
							'title'     => '결제수단 설명',
							'className' => 'fluid',
							'type'      => 'TextArea',
							'default'   => __( '구매안전 서비스인 에스크로 계좌이체 결제를 진행 합니다.', 'pgall-for-woocommerce' ),
							'tooltip'   => array (
								'title' => array (
									'content' => __( '결제 페이지에서 구매자들이 결제 진행 시 제공되는 결제수단 상세설명 입니다.', 'pgall-for-woocommerce' )
								)
							)
						),
						array (
							'id'        => 'nicepay_escrow_bank_receipt',
							'title'     => __( '현금 영수증', 'pgall-for-woocommerce' ),
							'className' => '',
							'type'      => 'Toggle',
							'default'   => 'no',
							'tooltip'   => array (
								'title' => array (
									'content' => __( '현금 영수증 발행 여부를 설정 할 수 있습니다. 현금 영수증 발행은 결제 대행사와 별도 계약이 되어 있어야 이용이 가능합니다.', 'pgall-for-woocommerce' ),
								)
							)
						),
					)
				),
				array (
					'type'     => 'Section',
					'title'    => __( '배송설정', 'pgall-for-woocommerce' ),
					'elements' => array (
						array (
							'id'          => 'nicepay_escrow_bank_delivery_company_name',
							'title'       => '택배사명',
							'className'   => '',
							'type'        => 'Text',
							'placeholder' => '일반택배'
						),
						array (
							'id'          => 'nicepay_escrow_bank_delivery_register_name',
							'title'       => '등록자명',
							'className'   => '',
							'type'        => 'Text',
							'placeholder' => '홍길동'
						),
						array (
							'id'          => 'nicepay_escrow_bank_delivery_sender_addr',
							'title'       => '배송지주소',
							'className'   => 'fluid',
							'type'        => 'Text',
							'placeholder' => '(08504) 서울시 금천구 서부샛길 606'
						),
					)
				),
				array (
					'type'     => 'Section',
					'title'    => '에스크로 고급 설정',
					'elements' => array (
						array (
							'id'        => 'nicepay_escrow_bank_use_advanced_setting',
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
							'id'        => 'nicepay_escrow_bank_order_status_after_payment',
							'title'     => __( '결제완료시 변경될 주문상태', 'pgall-for-woocommerce' ),
							'showIf'    => array ( 'nicepay_escrow_bank_use_advanced_setting' => 'yes' ),
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
									'content' => __( '에스크로 결제건에 한해서, 결제(입금)이 완료되면 지정된 주문상태로 변경합니다.', 'pgall-for-woocommerce' ),
								)
							)
						),
						array (
							'id'        => 'nicepay_escrow_bank_order_status_after_enter_shipping_number',
							'title'     => __( '배송정보 등록후 변경될 주문상태', 'pgall-for-woocommerce' ),
							'showIf'    => array ( 'nicepay_escrow_bank_use_advanced_setting' => 'yes' ),
							'className' => '',
							'type'      => 'Select',
							'default'   => 'shipped',
							'options'   => $this->filter_order_statuses( array (
								'pending',
								'cancelled',
								'completed',
								'failed',
								'on-hold',
								'refunded',
							) ),
							'tooltip'   => array (
								'title' => array (
									'content' => __( '에스크로 결제건에 한해서, 관리자가 배송정보를 등록하면 지정된 주문상태로 변경합니다.', 'pgall-for-woocommerce' ),
								)
							)
						),
						array (
							'id'        => 'nicepay_escrow_bank_possible_refund_status_for_mypage',
							'title'     => __( '구매자 주문취소 가능상태', 'pgall-for-woocommerce' ),
							'showIf'    => array ( 'nicepay_escrow_bank_use_advanced_setting' => 'yes' ),
							'className' => '',
							'type'      => 'Select',
							'default'   => 'pending,on-hold',
							'multiple'  => true,
							'options'   => $this->get_order_statuses(),
							'tooltip'   => array (
								'title' => array (
									'content' => __( '에스크로 결제건에 한해서, 구매자가 내계정 페이지에서 주문취소 요청을 할 수 있는 주문 상태를 지정합니다.', 'pgall-for-woocommerce' ),
								)
							)
						),
						array (
							'id'        => 'nicepay_escrow_bank_possible_escrow_confirm_status_for_customer',
							'title'     => __( '구매자 주문확인 및 거절 가능상태', 'pgall-for-woocommerce' ),
							'showIf'    => array ( 'nicepay_escrow_bank_use_advanced_setting' => 'yes' ),
							'className' => '',
							'type'      => 'Select',
							'default'   => 'processing,shipped',
							'multiple'  => true,
							'options'   => $this->get_order_statuses(),
							'tooltip'   => array (
								'title' => array (
									'content' => __( '에스크로 결제건에 한해서, 구매자가 내계정 - 주문상세 페이지에서 주문확인 및 거절을 할 수 있는 주문 상태를 지정합니다.', 'pgall-for-woocommerce' ),
								)
							)
						)
					)
				),

			);
		}
	}
}
