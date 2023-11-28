<?php
/*
=====================================================================================
                엠샵 업다운로드 / Copyright 2016 by CodeM(c)
=====================================================================================

  [ 우커머스 버전 지원 안내 ]

   워드프레스 버전 : WordPress 4.6 이상

   우커머스 버전 : WooCommerce 2.6 이상


  [ 코드엠 플러그인 라이센스 규정 ]

   (주)코드엠에서 개발된 워드프레스  플러그인을 사용하시는 분들에게는 다음 사항에 대한 동의가 있는 것으로 간주합니다.

   1. 코드엠에서 개발한 워드프레스 우커머스용 엠샵 업다운로드 플러그인의 저작권은 (주)코드엠에게 있습니다.
   
   2. 플러그인은 사용권을 구매하는 것이며, 프로그램 저작권에 대한 구매가 아닙니다.

   3. 플러그인을 구입하여 다수의 사이트에 복사하여 사용할 수 없으며, 1개의 라이센스는 1개의 사이트에만 사용할 수 있습니다. 
      이를 위반 시 지적 재산권에 대한 손해 배상 의무를 갖습니다.

   4. 플러그인은 구입 후 1년간 업데이트를 지원합니다.

   5. 플러그인은 워드프레스, 테마, 플러그인과의 호환성에 대한 책임이 없습니다.

   6. 플러그인 설치 후 버전에 관련한 운용 및 관리의 책임은 사이트 당사자에게 있습니다.

   7. 다운로드한 플러그인은 환불되지 않습니다.

=====================================================================================
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MSEX_Fields' ) ) {

	class MSEX_Fields {

		public static function get_default_order_fields() {
			return apply_filters('msex_default_order_fields',
				array (
					array (
						'field_type'  => 'order_date',
						'field_label'  => '주문 일시'
					),
					array (
						'field_type'  => 'order_id',
						'field_label'  => '주문 번호'
					),
					array (
						'field_type'  => 'order_status',
						'field_label'  => '주문 상태'
					),
					array (
						'field_type'  => 'billing_name',
						'field_label'  => '주문자 이름'
					),
					array (
						'field_type'  => 'billing_phone',
						'field_label'  => '주문자 전화번호'
					),
                    array (
                        'field_type'  => 'billing_postcode',
                        'field_label'  => '주문자 우편번호'
                    ),
                    array (
                        'field_type'  => 'billing_address',
                        'field_label'  => '주문자 주소'
                    ),
					array (
						'field_type'  => 'product_sku',
						'field_label'  => '상품 코드'
					),
					array (
						'field_type'  => 'product_id',
						'field_label'  => '상품 ID'
					),
					array (
						'field_type'  => 'product_name',
						'field_label'  => '상품명'
					),
					array (
						'field_type'  => 'order_option',
						'field_label'  => '주문옵션명'
					),
					array (
						'field_type'  => 'product_qty',
						'field_label'  => '주문수량'
					),
					array (
						'field_type'  => 'product_price',
						'field_label'  => '상품금액'
					),
					array (
						'field_type'  => 'product_total',
						'field_label'  => '주문총액'
					),
					array (
						'field_type'  => 'order_used_point',
						'field_label'  => '포인트 결제'
					),
					array (
						'field_type'  => 'order_discount_price',
						'field_label'  => '쿠폰 결제'
					),
					array (
						'field_type'  => 'order_shipping_price',
						'field_label'  => '배송비 결제'
					),
					array (
						'field_type'  => 'order_refunded_price',
						'field_label'  => '환불 금액'
					),
					array (
						'field_type'  => 'order_total',
						'field_label'  => '실 결제 금액'
					),
					array (
						'field_type'  => 'payment_method',
						'field_label'  => '결제수단'
					),
					array (
						'field_type'  => 'shipping_method',
						'field_label'  => '배송방법'
					),
					array (
						'field_type'  => 'shipping_name',
						'field_label'  => '수령자'
					),
					array (
						'field_type'  => 'shipping_phone',
						'field_label'  => '수령자 전화번호'
					),
					array (
						'field_type'  => 'shipping_postcode',
						'field_label'  => '수령자 우편번호'
					),
					array (
						'field_type'  => 'shipping_address',
						'field_label'  => '수령자 주소'
					),
					array (
						'field_type'  => 'order_note',
						'field_label'  => '배송요구사항'
					),
					array (
						'field_type'  => 'used_coupon',
						'field_label'  => '쿠폰번호'
					),
					array (
						'field_type'  => 'file_id',
						'field_label'  => '다운로드 상품 번호'
					),
					array (
						'field_type'  => 'file_name',
						'field_label'  => '다운로드 파일명'
					),
					array (
						'field_type'  => 'download_count',
						'field_label'  => '다운로드 횟수'
					),
					array (
						'field_type'  => 'downloads_remaining',
						'field_label'  => '잔여 다운로드 횟수'
					),
					array (
						'field_type'  => 'access_expires',
						'field_label'  => '다운로드 만료일'
					)
				)
			);
		}

		public static function get_default_user_fields() {
			return apply_filters('msex_default_user_fields',
				array (
					array (
						'field_type'  => 'id',
						'field_label'  => '고객번호'
					),
					array (
						'field_type'  => 'user_name',
						'field_label'  => '이름'
					),
					array (
						'field_type'  => 'user_login',
						'field_label'  => '아이디'
					),
					array (
						'field_type'  => 'user_email',
						'field_label'  => '이메일'
					),
					array (
						'field_type'  => 'user_role_name',
						'field_label'  => '사이트 역할'
					),
					array (
						'field_type'  => 'user_posts_count',
						'field_label'  => '글작성수',
					),
					array (
						'field_type'  => 'user_status',
						'field_label'  => '회원 상태',
					),
					array (
						'field_type'  => 'user_zipcode',
						'field_label'  => '우편번호',
					),

					array (
						'field_type'  => 'user_address1',
						'field_label'  => '기본주소'
					),
					array (
						'field_type'  => 'user_address2',
						'field_label'  => '상세주소'
					),
					array (
						'field_type'  => 'user_phone',
						'field_label'  => '전화번호'
					),
					array (
						'field_type'  => 'mshop_point',
						'field_label'  => '포인트'
					),
					array (
						'field_type'  => 'mshop_money_spent',
						'field_label'  => '총 결제 금액'
					),
					array (
						'field_type'  => 'mshop_order_total',
						'field_label'  => '총 주문 금액'
					),
					array (
						'field_type'  => 'register_date',
						'field_label'  => '회원가입일'
					),
					array (
						'field_type'  => 'last_login_date',
						'field_label'  => '마지막 로그인'
					)
				)
			);
		}

		public static function get_default_product_fields() {
			return apply_filters('msex_default_product_fields',
				array (
					array (
						'field_type'  => 'post_id',
						'field_label'  => '상품 ID'
					),
					array (
						'field_type'  => 'post_date',
						'field_label'  => '상품등록일'
					),
					array (
						'field_type'  => 'sku',
						'field_label'  => 'SKU'
					),
					array (
						'field_type'  => 'product_name',
						'field_label'  => '상품명'
					),
					array (
						'field_type'  => 'product_attributes',
						'field_label'  => '상품속성'
					),
					array (
						'field_type'  => 'product_type',
						'field_label'  => '상품종류',
					),
					array (
						'field_type'  => 'regular_price',
						'field_label'  => '정상가격',
					),
					array (
						'field_type'  => 'sale_price',
						'field_label'  => '할인가격',
					),
					array (
						'field_type'  => 'stock_status',
						'field_label'  => '재고상태'
					),
					array (
						'field_type'  => 'stock_count',
						'field_label'  => '재고수량'
					),
					array (
						'field_type'  => 'categories',
						'field_label'  => '카테고리'
					),
				)
			);
		}
	}
}