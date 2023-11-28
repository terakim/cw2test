<?php

/*
=====================================================================================
                ﻿엠샵 주소 체크아웃 / Copyright 2015 by CodeM(c)
=====================================================================================

  [ 우커머스 버전 지원 안내 ]

   워드프레스 버전 : WordPress 4.3

   우커머스 버전 : WooCommerce 2.4


  [ 코드엠 플러그인 라이센스 규정 ]

   (주)코드엠에서 개발된 워드프레스  플러그인을 사용하시는 분들에게는 다음 사항에 대한 동의가 있는 것으로 간주합니다.

   1. 코드엠에서 개발한 워드프레스 우커머스용 ﻿엠샵 주소 체크아웃 플러그인의 저작권은 (주)코드엠에게 있습니다.
   
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
	exit;
}

if ( ! class_exists( 'MSADDR_Settings_Checkout_Fields' ) ) :

	class MSADDR_Settings_Checkout_Fields {
		static function init() {
			add_filter( 'msshelper_get_msaddr_billing_fields', array( __CLASS__, 'get_address_fields' ), 100 );
			add_filter( 'msshelper_get_msaddr_shipping_fields', array( __CLASS__, 'get_address_fields' ), 100 );
			add_filter( 'msshelper_get_msaddr_order_fields', array( __CLASS__, 'get_address_fields' ), 100 );
		}

		static function update_settings() {
			include_once MSADDR()->plugin_path() . '/includes/admin/setting-manager/mshop-setting-helper.php';
			$_REQUEST = array_merge( $_REQUEST, json_decode( stripslashes( $_REQUEST['values'] ), true ) );

			MSSHelper::update_settings( self::get_setting_fields() );

			wp_send_json_success();
		}


		static function get_fields( $type ) {
			$_fields = array();

			$fields = get_option( "msaddr_{$type}_fields", array() );

			foreach ( $fields as $field ) {
				if ( ! empty( $field['id'] ) && ! empty( $field['label'] ) ) {
					$_fields[ $field['id'] ] = $field['label'];
				}
			}

			return $_fields;
		}

		static function get_address_fields() {
			$fieldset = array(
				'msaddr_billing_fields'  => 'billing',
				'msaddr_shipping_fields' => 'shipping',
				'msaddr_order_fields'    => 'order',
			);

			$current_filter = current_filter();
			$options        = str_replace( 'msshelper_get_', '', $current_filter );

			$address_fields = get_option( $options );

			if ( is_null( WC()->session ) ) {
				if ( ! function_exists( 'wc_get_cart_item_data_hash' ) ) {
					require_once WC_ABSPATH . 'includes/wc-cart-functions.php';
				}

				$session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );
				WC()->session  = new $session_class();
				if ( is_callable( array( WC()->session, 'init' ) ) ) {
					WC()->session->init();
				}
			}

			$chained_rules = array(
				array(
					'mshop_billing_address',
					'billing_postcode',
					'billing_address_1',
					'billing_address_2',
					'billing_state',
					'billing_city',
				),
				array(
					'billing_first_name',
					'billing_last_name',
					'billing_first_name_kr',
				),
				array(
					'billing_phone',
					'billing_phone_kr',
				),
				array(
					'billing_email',
					'billing_email_kr',
				),
				array(
					'mshop_shipping_address',
					'shipping_postcode',
					'shipping_address_1',
					'shipping_address_2',
					'shipping_state',
					'shipping_city',
				),
				array(
					'shipping_first_name',
					'shipping_last_name',
					'shipping_first_name_kr',
				),
				array(
					'shipping_phone',
					'shipping_phone_kr',
				)
			);

			if ( empty( $address_fields ) ) {
				WC()->customer = get_user_by( 'id', get_current_user_id() );

				if ( empty( WC()->cart ) ) {
					WC()->cart = new WC_Cart();
				}

				if ( is_callable( array( WC()->checkout(), 'get_checkout_fields' ) ) ) {
					$checkout_fields = WC()->checkout()->get_checkout_fields( $fieldset[ $options ] );
				} else {
					$checkout_fields = WC()->checkout()->checkout_fields[ $fieldset[ $options ] ];
				}

				foreach ( $checkout_fields as $key => $value ) {

					if ( ! is_array( $value['class'] ) ) {
						$value['class'] = array();
					}

					$position = array_filter( $value['class'], function ( $class ) {
						return strpos( $class, 'form-row' ) === 0;
					} );

					if ( 'order_comments' == $key || in_array( 'mshop-always-kr', $value['class'] ) ) {
						$display = 'mshop-enable-kr mshop-always-kr';
					} else if ( in_array( 'mshop-enable-kr', $value['class'] ) ) {
						$display = 'mshop-enable-kr';
					} else {
						$display = 'mshop-disable-kr';
					}

					$class = array_diff( $value['class'], $position );
					$class = array_diff( $class, array(
						'mshop-enable-kr',
						'mshop-always-kr',
						'mshop_addr_title'
					) );

					$rule = array();

					foreach ( $chained_rules as $chained_rule ) {
						if ( in_array( $key, $chained_rule ) ) {
							$rule = array(
								'key'       => $chained_rule,
								'field'     => 'enable',
								'key_field' => 'id'
							);
							break;
						}
					}

					$address_fields[] = array(
						'id'            => $key,
						'label'         => msaddr_get( $value, 'label' ),
						'required'      => ( isset( $value['required'] ) && $value['required'] ) ? 'yes' : 'no',
						'placeholder'   => ! empty ( $value['placeholder'] ) ? $value['placeholder'] : '',
						'position'      => current( $position ),
						'type'          => ! empty ( $value['type'] ) ? $value['type'] : 'text',
						'class'         => implode( ',', $class ),
						'display'       => $display,
						'enable'        => 'yes',
						'_removeable'   => 'no',
						'_chained_rule' => $rule
					);
				}
			}

			return $address_fields;
		}

		static function get_field_types( $load_address ) {
			$field_types = array(
				"text"          => __( '텍스트', 'mshop-address-ex' ),
				"textarea"      => __( 'TextArea', 'mshop-address-ex' ),
				"select"        => __( '셀렉트', 'mshop-address-ex' ),
				"email"         => __( '이메일', 'mshop-address-ex' ),
				"tel"           => __( '전화번호', 'mshop-address-ex' ),
				"country"       => __( '국가', 'mshop-address-ex' ),
				"state"         => __( '주/군', 'mshop-address-ex' ),
				"mshop_address" => __( '대한민국 주소', 'mshop-address-ex' ),
				"mshop_file"    => __( '파일업로드', 'mshop-address-ex' ),
			);

			if ( 'order' == $load_address ) {
				$field_types['multiselect'] = __( '멀티셀렉트', 'mshop-address-ex' );
			}

			return apply_filters( 'msaddr_checkout_field_types', $field_types );
		}

		static function get_setting_fields() {
			return array(
				'type'     => 'Tab',
				'id'       => 'mnp-setting-tab',
				'elements' => array(
					self::get_checkout_field_settings(),
					self::get_billing_field_settings(),
					self::get_shipping_field_settings(),
					self::get_order_field_settings(),
					self::get_display_setting(),
				)
			);
		}

		public static function get_checkout_field_settings() {
			return array(
				'type'     => 'Page',
				'title'    => __( '체크아웃 필드', 'mshop-address-ex' ),
				'class'    => 'active',
				'elements' => array(
					array(
						'type'     => 'Section',
						'title'    => '체크아웃 필드 에디터',
						'elements' => array(
							array(
								'id'        => 'msaddr_enable_checkout_fields',
								'title'     => '활성화',
								'className' => '',
								'type'      => 'Toggle',
								'default'   => 'no',
								'desc'      => __( '<div class="desc2">체크아웃 필드 에디터 기능을 사용합니다.</div>', 'mshop-address-ex' ),
							)
						)
					),
					array(
						'type'     => 'Section',
						'title'    => '체크아웃 필드',
						'showIf'   => array( 'msaddr_enable_checkout_fields' => 'yes' ),
						'elements' => array(
							array(
								'id'        => 'msaddr_enable_billing_fields',
								'title'     => '청구지(Billing)',
								'className' => '',
								'type'      => 'Toggle',
								'default'   => 'no',
								'desc'      => __( '<div class="desc2">청구지(Billing) 필드 편집 기능을 사용합니다.</div>', 'mshop-address-ex' ),
							),
							array(
								'id'        => 'msaddr_enable_shipping_fields',
								'title'     => '배송지(Shipping)',
								'className' => '',
								'type'      => 'Toggle',
								'default'   => 'no',
								'desc'      => __( '<div class="desc2">배송지(Shipping) 필드 편집 기능을 사용합니다.</div>', 'mshop-address-ex' ),
							),
							array(
								'id'        => 'msaddr_enable_order_fields',
								'title'     => '주문(Order)',
								'className' => '',
								'type'      => 'Toggle',
								'default'   => 'no',
								'desc'      => __( '<div class="desc2">주문(Order) 필드 편집 기능을 사용합니다.</div>', 'mshop-address-ex' ),
							),
							array(
								'id'        => 'msaddr_save_order_fields',
								'title'     => '주문 필드값 저장',
								'showIf'    => array( 'msaddr_enable_order_fields' => 'yes' ),
								'className' => '',
								'type'      => 'Toggle',
								'default'   => 'yes',
								'desc'      => __( '<div class="desc2">다음 주문을 위해 주문 필드의 입력값을 저장합니다.</div>', 'mshop-address-ex' ),
							),
							array(
								'id'             => 'msaddr_reset_checkout_fields',
								'title'          => '설정 초기화',
								'label'          => '초기화',
								'iconClass'      => 'icon settings',
								'className'      => '',
								'type'           => 'Button',
								'default'        => '',
								'confirmMessage' => __( '체크아웃 필드 설정을 초기화하시겠습니까?', 'mshop-address-ex' ),
								'actionType'     => 'ajax',
								'ajaxurl'        => admin_url( 'admin-ajax.php' ),
								'action'         => msaddr_ajax_command( 'reset_checkout_fields' ),
								'desc'           => __( '<div class="desc2">체크아웃 필드 설정을 초기화합니다.</div>', 'mshop-address-ex' ),
							),
						)
					),
					array(
						'type'     => 'Section',
						'title'    => __( 'Select2 라이브러리', 'mshop-address-ex' ),
						'elements' => array(
							array(
								"id"        => "msaddr_use_select2",
								"title"     => __( '사용', 'mshop-address-ex' ),
								"className" => "fluid",
								"type"      => "Toggle",
								"default"   => "yes",
								'desc'      => __( '<div class="desc2">테마와의 호환 이슈가 발생하는 경우에만 비활성화 해주세요.</div>', 'mshop-address-ex' ),
							)
						)
					),
				)
			);
		}

		static function get_billing_field_settings() {
			return array(
				'type'     => 'Page',
				'title'    => __( '청구지 필드', 'mshop-address-ex' ),
				'class'    => '',
				'showIf'   => array(
					array( 'msaddr_enable_checkout_fields' => 'yes' ),
					array( 'msaddr_enable_billing_fields' => 'yes' )
				),
				'elements' => array(
					array(
						'type'     => 'Section',
						'title'    => __( '필드 설정', 'mshop-address-ex' ),
						'elements' => array()
					),
					array(
						"id"           => "msaddr_billing_fields",
						"className"    => "address-fields",
						"repeater"     => true,
						"sortable"     => true,
						"type"         => "SortableList",
						"listItemType" => "MShopAddressField",
						"editable"     => "true",
						"template"     => array(
							"enable"   => "yes",
							"type"     => "text",
							"display"  => "mshop-enable-kr mshop-always-kr",
							"position" => "form-row-first"
						),
						"keyFields"    => array(
							'type' => array(
								'type'   => 'select',
								'label'  => '타입',
								"option" => self::get_field_types( 'billing' )
							)
						),
						"elements"     => self::get_field_columns( 'billing' )
					)
				)
			);
		}

		static function get_shipping_field_settings() {
			return array(
				'type'     => 'Page',
				'title'    => __( '배송지 필드', 'mshop-address-ex' ),
				'class'    => '',
				'showIf'   => array(
					array( 'msaddr_enable_checkout_fields' => 'yes' ),
					array( 'msaddr_enable_shipping_fields' => 'yes' )
				),
				'elements' => array(
					array(
						'type'     => 'Section',
						'title'    => __( '필드 설정', 'mshop-address-ex' ),
						'elements' => array()
					),
					array(
						"id"           => "msaddr_shipping_fields",
						"className"    => "address-fields",
						"repeater"     => true,
						"sortable"     => true,
						"type"         => "SortableList",
						"listItemType" => "MShopAddressField",
						"editable"     => "true",
						"template"     => array(
							"enable"   => "yes",
							"type"     => "text",
							"display"  => "mshop-enable-kr mshop-always-kr",
							"position" => "form-row-first"
						),
						"keyFields"    => array(
							'type' => array(
								'type'   => 'select',
								'label'  => '타입',
								"option" => self::get_field_types( 'shipping' )
							)
						),
						"elements"     => self::get_field_columns( 'shipping' )
					)
				)
			);
		}

		static function get_order_field_settings() {
			return array(
				'type'     => 'Page',
				'title'    => __( '주문 필드', 'mshop-address-ex' ),
				'class'    => '',
				'showIf'   => array(
					array( 'msaddr_enable_checkout_fields' => 'yes' ),
					array( 'msaddr_enable_order_fields' => 'yes' )
				),
				'elements' => array(
					array(
						'type'     => 'Section',
						'title'    => __( '필드 설정', 'mshop-address-ex' ),
						'elements' => array()
					),
					array(
						"id"           => "msaddr_order_fields",
						"className"    => "address-fields",
						"repeater"     => true,
						"sortable"     => true,
						"type"         => "SortableList",
						"listItemType" => "MShopAddressField",
						"editable"     => "true",
						"template"     => array(
							"enable"   => "yes",
							"type"     => "text",
							"display"  => "mshop-enable-kr mshop-always-kr",
							"position" => "form-row-first"
						),
						"keyFields"    => array(
							'type' => array(
								'type'   => 'select',
								'label'  => '타입',
								"option" => self::get_field_types( 'order' )
							)
						),
						"elements"     => self::get_field_columns( 'order' )
					)
				)
			);
		}

		static function get_field_columns( $load_address ) {
			return array(
				'left'  => array(
					'type'              => 'Section',
					'class'             => 'eight wide column',
					"hideSectionHeader" => true,
					'elements'          => array(
						array(
							'id'        => 'enable',
							'title'     => __( '활성화', 'mshop-address-ex' ),
							'className' => '',
							'type'      => 'Toggle',
							'default'   => "yes",
						),
						array(
							"id"        => 'required',
							"title"     => __( '필수입력', 'mshop-address-ex' ),
							"className" => "one wide column",
							"type"      => "Toggle",
							"default"   => "no",
							"showIf"    => array(
								array( 'id' => '!mshop_billing_address' ),
								array( 'id' => '!mshop_shipping_address' ),
							)
						),
						array(
							"id"        => 'id',
							"title"     => __( '아이디', 'mshop-address-ex' ),
							"className" => "three wide column fluid",
							"type"      => "Text",
						),
						array(
							"id"          => 'type',
							"title"       => __( '종류', 'mshop-address-ex' ),
							"className"   => "two wide column fluid",
							"type"        => "Select",
							"options"     => self::get_field_types( $load_address ),
							"placeholder" => __( '필드 타입', 'mshop-address-ex' ),
							"default"     => "",
						),
						array(
							"id"        => 'label',
							"title"     => __( '제목', 'mshop-address-ex' ),
							"className" => "three wide column fluid",
							"type"      => "Text",
						),
						array(
							"id"        => 'placeholder',
							"title"     => __( '안내문구', 'mshop-address-ex' ),
							"className" => "three wide column fluid",
							"type"      => "Text",
						),

					)
				),
				'right' => array(
					'type'              => 'Section',
					'class'             => 'eight wide column',
					"hideSectionHeader" => true,
					'elements'          => array(
						array(
							"id"        => 'class',
							"title"     => __( '클래스', 'mshop-address-ex' ),
							"className" => "three wide column fluid",
							"type"      => "Text",
						),
						array(
							"id"        => 'position',
							"title"     => __( '위치', 'mshop-address-ex' ),
							"className" => "two wide column fluid",
							"type"      => "Select",
							"options"   => array(
								"form-row-first" => __( 'Left', 'mshop-address-ex' ),
								"form-row-last"  => __( 'Right', 'mshop-address-ex' ),
								"form-row-wide"  => __( 'Full-Width', 'mshop-address-ex' )
							),
							"default"   => "",
						),
						array(
							"id"        => 'display',
							"title"     => __( '국가옵션', 'mshop-address-ex' ),
							"className" => "two wide column",
							"type"      => "Select",
							"options"   => array(
								""                                => __( '국가 옵션', 'mshop-address-ex' ),
								"mshop-disable-kr"                => __( '한국일때 숨김', 'mshop-address-ex' ),
								"mshop-enable-kr"                 => __( '한국일때 표시', 'mshop-address-ex' ),
								"mshop-enable-kr mshop-always-kr" => __( '항상표시', 'mshop-address-ex' )
							),
							"default"   => "mshop-disable-kr",
						),
						array(
							"id"        => 'select_options',
							'type'      => 'SortableTable',
							'className' => 'select_options',
							"repeater"  => true,
							"editable"  => true,
							"sortable"  => true,
							"showIf"    => array( 'type' => 'select,multiselect' ),
							"template"  => array(
								'key'   => '',
								'value' => ''
							),
							"elements"  => array(
								array(
									"id"            => "key",
									"title"         => __( "키", 'mshop-address-ex' ),
									"className"     => "center aligned seven wide column fluid",
									"cellClassName" => "center aligned",
									"type"          => "Text",
								),
								array(
									"id"            => "value",
									"title"         => __( "값", 'mshop-address-ex' ),
									"className"     => "center aligned seven wide column fluid",
									"cellClassName" => "center aligned",
									"type"          => "Text",
								)
							)
						)
					)
				)
			);
		}

		static function get_display_setting() {
			return array(
				'type'     => 'Page',
				'class'    => '',
				'title'    => __( '필드 표시 설정', 'mshop-address-ex' ),
				'showIf'   => array( 'msaddr_enable_checkout_fields' => 'yes' ),
				'elements' => array(
					array(
						'type'     => 'Section',
						'title'    => __( '사용자 등급별', 'mshop-address-ex' ),
						'elements' => array(
							array(
								"id"        => "msaddr-filter-field-by-role",
								"title"     => "",
								"className" => "",
								"editable"  => "true",
								"sortable"  => "true",
								"repeater"  => "true",
								"type"      => "SortableTable",
								"default"   => array(),
								"template"  => array(
									"enabled"         => "yes",
									"roles"           => array(),
									"payment_methods" => "",
								),
								"elements"  => array(
									array(
										"id"        => "enabled",
										"title"     => __( "활성화", "mshop-address-ex" ),
										"className" => " one wide column fluid",
										"default"   => "yes",
										"type"      => "Toggle"
									),
									array(
										"id"          => "billing_fields",
										"title"       => __( "청구지필드", "mshop-address-ex" ),
										"className"   => " three wide column search fluid",
										'multiple'    => true,
										'search'      => true,
										"type"        => "Select",
										"default"     => "",
										'options'     => self::get_fields( 'billing' ),
										"placeholder" => __( "결제수단을 선택하세요.", "mshop-address-ex" )
									),
									array(
										"id"          => "shipping_fields",
										"title"       => __( "배송지필드", "mshop-address-ex" ),
										"className"   => " three wide column search fluid",
										'multiple'    => true,
										'search'      => true,
										"type"        => "Select",
										"default"     => "",
										'options'     => self::get_fields( 'shipping' ),
										"placeholder" => __( "결제수단을 선택하세요.", "mshop-address-ex" )
									),
									array(
										"id"          => "order_fields",
										"title"       => __( "주문필드", "mshop-address-ex" ),
										"className"   => " three wide column search fluid",
										'multiple'    => true,
										'search'      => true,
										"type"        => "Select",
										"default"     => "",
										'options'     => self::get_fields( 'order' ),
										"placeholder" => __( "결제수단을 선택하세요.", "mshop-address-ex" )
									),
									array(
										"id"          => "roles",
										"title"       => __( "사용자 등급", "mshop-address-ex" ),
										"className"   => " six wide column search fluid",
										'multiple'    => true,
										"type"        => "Select",
										"default"     => "",
										"placeholder" => __( "사용자 등급을 선택하세요.", "mshop-address-ex" ),
										"options"     => msaddr_get_user_roles()
									),
								)
							)
						)
					),
					array(
						'type'     => 'Section',
						'title'    => __( '상품별 노출 제어', 'mshop-address-ex' ),
						'elements' => array(
							array(
								"id"        => "msaddr-filter-field-by-product",
								"title"     => "",
								"className" => "",
								"editable"  => "true",
								"sortable"  => "true",
								"repeater"  => "true",
								"type"      => "SortableTable",
								"default"   => array(),
								"template"  => array(
									"enabled"         => "yes",
									"products"        => "",
									"payment_methods" => "",
								),
								"elements"  => array(
									array(
										"id"        => "enabled",
										"title"     => __( "활성화", "mshop-address-ex" ),
										"className" => " one wide column fluid",
										"default"   => "yes",
										"type"      => "Toggle"
									),
									array(
										"id"          => "billing_fields",
										"title"       => __( "청구지필드", "mshop-address-ex" ),
										"className"   => " three wide column search fluid",
										'multiple'    => true,
										'search'      => true,
										"type"        => "Select",
										"default"     => "",
										'options'     => self::get_fields( 'billing' ),
										"placeholder" => __( "결제수단을 선택하세요.", "mshop-address-ex" )
									),
									array(
										"id"          => "shipping_fields",
										"title"       => __( "배송지필드", "mshop-address-ex" ),
										"className"   => " three wide column search fluid",
										'multiple'    => true,
										'search'      => true,
										"type"        => "Select",
										"default"     => "",
										'options'     => self::get_fields( 'shipping' ),
										"placeholder" => __( "결제수단을 선택하세요.", "mshop-address-ex" )
									),
									array(
										"id"          => "order_fields",
										"title"       => __( "주문필드", "mshop-address-ex" ),
										"className"   => " three wide column search fluid",
										'multiple'    => true,
										'search'      => true,
										"type"        => "Select",
										"default"     => "",
										'options'     => self::get_fields( 'order' ),
										"placeholder" => __( "결제수단을 선택하세요.", "mshop-address-ex" )
									),
									array(
										"id"           => "products",
										"title"        => __( "상품", "mshop-address-ex" ),
										"className"    => " six wide column search fluid",
										'multiple'     => true,
										'search'       => true,
										'disableClear' => true,
										"type"         => "SearchSelect",
										"default"      => "",
										'action'       => 'action=' . msaddr_ajax_command( 'target_search' ) . '&type=product',
										"placeholder"  => __( "상품을 선택하세요.", "mshop-address-ex" )
									)
								)
							)
						)
					),
					array(
						'type'     => 'Section',
						'title'    => __( '카테고리별 노출 제어', 'mshop-address-ex' ),
						'elements' => array(
							array(
								"id"        => "msaddr-filter-field-by-category",
								"title"     => "",
								"className" => "",
								"editable"  => "true",
								"sortable"  => "true",
								"repeater"  => "true",
								"type"      => "SortableTable",
								"default"   => array(),
								"template"  => array(
									"enabled"         => "yes",
									"categories"      => "",
									"payment_methods" => "",
								),
								"elements"  => array(
									array(
										"id"        => "enabled",
										"title"     => __( "활성화", "mshop-address-ex" ),
										"className" => " one wide column fluid",
										"default"   => "yes",
										"type"      => "Toggle"
									),
									array(
										"id"          => "billing_fields",
										"title"       => __( "청구지필드", "mshop-address-ex" ),
										"className"   => " three wide column search fluid",
										'multiple'    => true,
										'search'      => true,
										"type"        => "Select",
										"default"     => "",
										'options'     => self::get_fields( 'billing' ),
										"placeholder" => __( "결제수단을 선택하세요.", "mshop-address-ex" )
									),
									array(
										"id"          => "shipping_fields",
										"title"       => __( "배송지필드", "mshop-address-ex" ),
										"className"   => " three wide column search fluid",
										'multiple'    => true,
										'search'      => true,
										"type"        => "Select",
										"default"     => "",
										'options'     => self::get_fields( 'shipping' ),
										"placeholder" => __( "결제수단을 선택하세요.", "mshop-address-ex" )
									),
									array(
										"id"          => "order_fields",
										"title"       => __( "주문필드", "mshop-address-ex" ),
										"className"   => " three wide column search fluid",
										'multiple'    => true,
										'search'      => true,
										"type"        => "Select",
										"default"     => "",
										'options'     => self::get_fields( 'order' ),
										"placeholder" => __( "결제수단을 선택하세요.", "mshop-address-ex" )
									),
									array(
										"id"           => "categories",
										"title"        => __( "상품 카테고리", "mshop-address-ex" ),
										"className"    => " six wide column search fluid",
										'multiple'     => true,
										'search'       => true,
										'disableClear' => true,
										"type"         => "SearchSelect",
										"default"      => "",
										'action'       => 'action=' . msaddr_ajax_command( 'target_search' ) . '&type=category',
										"placeholder"  => __( "카테고리를 선택하세요.", "mshop-address-ex" )
									)
								)
							)
						)
					)
				)
			);
		}

		static function get_manual_link() {
			return array(
				'type'     => 'Page',
				'class'    => 'manual_link',
				'title'    => __( '매뉴얼', 'mshop-address-ex' ),
				'elements' => array()
			);
		}
		public static function output() {
			require_once MSADDR()->plugin_path() . '/includes/admin/setting-manager/mshop-setting-helper.php';
			$settings = self::get_setting_fields();

			MSADDR_Admin::enqueue_scripts();

			wp_localize_script( 'mshop-setting-manager', 'mshop_setting_manager', array(
				'element'  => 'mshop-setting-wrapper',
				'ajaxurl'  => admin_url( 'admin-ajax.php' ),
				'action'   => msaddr_ajax_command( 'update_checkout_field_settings' ),
				'settings' => $settings,
			) );

			$setting_values = MSSHelper::get_settings( $settings );

			?>
            <style>
                .address-fields .ui.table {
                    font-size: 11px !important;;
                }

                .address-fields .ui.table tr td {
                    border-top: 1px solid rgba(34, 36, 38, 0.1) !important;
                }

                .address-fields .ui.table tr:first-child td {
                    border-top: none !important;
                }

                i.lightgray.icon {
                    color: lightgrey !important;
                }
            </style>
            <script>
                jQuery(document).ready(function ($) {
                    $(this).trigger('mshop-setting-manager', ['mshop-setting-wrapper', '100', <?php echo json_encode( $setting_values ); ?>, null, null]);
                });
            </script>

            <div id="mshop-setting-wrapper"></div>
			<?php
		}

	}

	MSADDR_Settings_Checkout_Fields::init();
endif;