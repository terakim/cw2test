<?php

//소스에 URL로 직접 접근 방지
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'PAFW_Settings_Kicc_Advanced' ) ) {
	class PAFW_Settings_Kicc_Advanced extends PAFW_Settings_Kicc {
		function get_setting_fields() {
			return array (
				array (
					'type'     => 'Section',
					'title'    => '고급 설정',
					'elements' => array (
						array (
							'id'        => 'language_code',
							'title'     => __( '결제창 언어', 'pgall-for-woocommerce' ),
							'className' => '',
							'type'      => 'Select',
							'default'   => 'KOR',
							'options'   => array (
								'KOR' => '한국어',
								'ENG' => '영어',
								'CHN' => '중국어',
								'JPN' => '일본어',
							),
							'tooltip'   => array (
								'title' => array (
									'content' => __( '결제창의 언어를 설정합니다.', 'pgall-for-woocommerce' ),
								)
							)
						),
						array (
							'id'        => 'payment_tag',
							'title'     => __( '결제 페이지 태그 설정', 'pgall-for-woocommerce' ),
							'className' => 'fluid',
							'type'      => 'Text',
							'default'   => '#order_review input[name=payment_method]:checked',
							'tooltip'   => array (
								'title' => array (
									'content' => __( '결제 페이지가 우커머스 기본 결제 태그와 다른 경우, 결제수단 확인이 가능한 별도 태그를 넣어 지정할 수 있습니다.', 'pgall-for-woocommerce' ),
								)
							)
						),
						array (
							'id'        => 'site_logo',
							'title'     => __( '사이트 로고', 'pgall-for-woocommerce' ),
							'className' => 'fluid',
							'type'      => 'Text',
							'default'   => PAFW()->plugin_url() . '/assets/images/default-logo.jpg',
							'tooltip'   => array (
								'title' => array (
									'content' => __( '결제 창 왼쪽 상단에 가맹점 사이트의 로고를 표시합니다. 가맹점의 로고가 있는 URL을 정확히 입력하셔야 하며, 입력하지 않으면 표시되지 않습니다. 권장 사이즈는 89 * 18 픽셀 입니다. 해당 사이즈에 맞춰 자동 리사이즈 됩니다. (예 : http://www.aaa.com/a.jpg)', 'pgall-for-woocommerce' ),
								)
							)
						),
						array (
							'id'        => 'show_save_button',
							'title'     => __( '변경사항 버튼노출', 'pgall-for-woocommerce' ),
							'className' => '',
							'type'      => 'Toggle',
							'default'   => 'no',
							'tooltip'   => array (
								'title' => array (
									'content' => __( '우커머스 기본 설정 변경 버튼을 노출합니다. 설정된 경우 버튼이 노출되며 설정되지 않은 경우 버튼이 노출되지 않습니다. 특수한 경우에만 사용하도록 제공되는 옵션으로 일반적인 경우 사용하지 않아도 됩니다.', 'pgall-for-woocommerce' ),
								)
							)
						),
					)
				)
			);
		}
	}
}
