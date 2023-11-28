<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class MSIV_Shipping_Korea_Zone extends WC_Shipping_Method {

	public function __construct() {
		$this->id           = 'korea_zone_shipping';
		$this->title        = __( '엠샵 추가배송비', 'mshop-iv-delivery-ex' );
		$this->method_title = __( '엠샵 추가배송비', 'mshop-iv-delivery-ex' );

		$this->init();

		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );

		if ( version_compare( WOOCOMMERCE_VERSION, '2.4.0', '>=' ) ) {
			add_filter( 'woocommerce_admin_settings_sanitize_option_mshop_iv_delivery_fee', array( $this, 'woocommerce_admin_settings_sanitize_option_mshop_iv_delivery_fee' ), 10, 3 );
		} else {
			add_action( 'woocommerce_update_option_mshop_iv_delivery_fee', array( $this, 'save' ), 10 );
		}

		add_filter( 'mshop_iv_delivery_fee', array( $this, 'mshop_iv_delivery_fee' ) );
	}

	public function process_admin_options() {
		parent::process_admin_options();

		woocommerce_update_options( $this->form_fields );
	}

	function init() {
		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables
		$this->enabled      = $this->get_option( 'enabled' );
		$this->title        = $this->get_option( 'title' );
		$this->min_amount   = $this->get_option( 'min_amount', 0 );
		$this->availability = $this->get_option( 'availability' );
		$this->countries    = $this->get_option( 'countries' );
		$this->requires     = $this->get_option( 'requires' );

		// Actions
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	public function woocommerce_admin_settings_sanitize_option_mshop_iv_delivery_fee( $value, $option, $raw_value ) {
		$record_info = apply_filters( 'mshop_iv_delivery_fee', $option );

		if ( empty( $record_info ) ) {
			return;
		}

		$options = array();
		$prefix  = '';

		if ( $record_info[0]['type'] == 'checkbox' ) {
			$prefix = '_';
		}
		$count = count( $_POST[ $prefix . $option['id'] . '_' . $record_info[0]['id'] ] );

		for ( $i = 0; $i < $count; $i ++ ) {
			$record = array();
			foreach ( $record_info as $field ) {
				if ( $field['type'] == 'checkbox' ) {
					$record[ $field['id'] ] = $_POST[ '_' . $option['id'] . '_' . $field['id'] ][ $i ];
				} else {
					$data                   = isset( $_POST[ $option['id'] . '_' . $field['id'] ][ $i ] ) ? $_POST[ $option['id'] . '_' . $field['id'] ][ $i ] : '';
					$record[ $field['id'] ] = is_array( $data ) ? implode( ',', $data ) : $data;
					if ( isset( $_POST[ '_' . $option['id'] . '_' . $field['id'] ][ $i ] ) ) {
						$record[ '_' . $field['id'] ] = stripslashes( $_POST[ '_' . $option['id'] . '_' . $field['id'] ][ $i ] );
					}
				}
			}

			$options[] = $record;
		}

		return json_encode( $options );
	}
	function update_setting( $setting ) {
		if ( ! empty( $setting['id'] ) ) {
			if ( method_exists( $this, 'set_' . $setting['id'] ) ) {
				$this->{'set_' . $setting['id']}();
			} else {
				$this->settings[ $setting['id'] ] = $_REQUEST[ $setting['id'] ];
			}
		}

		if ( ! empty( $setting['elements'] ) && empty( $setting['repeater'] ) ) {
			foreach ( $setting['elements'] as $element ) {
				$this->update_setting( $element );
			}
		}
	}

	function save_setting() {
		$_REQUEST = array_merge( $_REQUEST, json_decode( stripslashes( $_REQUEST['values'] ), true ) );

		$setting = $this->get_setting();

		$this->update_setting( $setting );

		update_option( 'woocommerce_korea_zone_shipping_settings', $this->settings );

		$this->init_settings();

		wp_send_json_success();
	}

	public function save( $value ) {
		$record_info = apply_filters( 'mshop_iv_delivery_fee', $value );

		if ( empty( $record_info ) ) {
			return;
		}

		$options = array();
		$prefix  = '';

		if ( $record_info[0]['type'] == 'checkbox' ) {
			$prefix = '_';
		}
		$count = count( $_POST[ $prefix . $value['id'] . '_' . $record_info[0]['id'] ] );

		for ( $i = 0; $i < $count; $i ++ ) {
			$record = array();
			foreach ( $record_info as $field ) {
				if ( $field['type'] == 'checkbox' ) {
					$record[ $field['id'] ] = $_POST[ '_' . $value['id'] . '_' . $field['id'] ][ $i ];
				} else {
					$data                   = $_POST[ $value['id'] . '_' . $field['id'] ][ $i ];
					$record[ $field['id'] ] = is_array( $data ) ? implode( ',', $data ) : $data;
					if ( isset( $_POST[ '_' . $value['id'] . '_' . $field['id'] ][ $i ] ) ) {
						$record[ '_' . $field['id'] ] = stripslashes( $_POST[ '_' . $value['id'] . '_' . $field['id'] ][ $i ] );
					}
				}
			}

			$options[] = $record;
		}

		update_option( $value['id'], json_encode( $options ) );
	}

	public function mshop_iv_delivery_fee( $record ) {
		return array(
			array(
				'title'   => __( '시도', 'mshop-iv-delivery-ex' ),
				'id'      => 'sido',
				'css'     => 'width:100px',
				'default' => '',
				'type'    => 'select_sido',
				'options' => array(
					''        => __( '선택', 'mshop-iv-delivery-ex' ),
					'강원도'     => __( '강원도', 'mshop-iv-delivery-ex' ),
					'경기도'     => __( '경기도', 'mshop-iv-delivery-ex' ),
					'경상남도'    => __( '경상남도', 'mshop-iv-delivery-ex' ),
					'경상북도'    => __( '경상북도', 'mshop-iv-delivery-ex' ),
					'광주광역시'   => __( '광주광역시', 'mshop-iv-delivery-ex' ),
					'대구광역시'   => __( '대구광역시', 'mshop-iv-delivery-ex' ),
					'대전광역시'   => __( '대전광역시', 'mshop-iv-delivery-ex' ),
					'부산광역시'   => __( '부산광역시', 'mshop-iv-delivery-ex' ),
					'서울특별시'   => __( '서울특별시', 'mshop-iv-delivery-ex' ),
					'세종특별자치시' => __( '세종특별자치시', 'mshop-iv-delivery-ex' ),
					'울산광역시'   => __( '울산광역시', 'mshop-iv-delivery-ex' ),
					'인천광역시'   => __( '인천광역시', 'mshop-iv-delivery-ex' ),
					'전라남도'    => __( '전라남도', 'mshop-iv-delivery-ex' ),
					'전라북도'    => __( '전라북도', 'mshop-iv-delivery-ex' ),
					'제주특별자치도' => __( '제주특별자치도', 'mshop-iv-delivery-ex' ),
					'충청남도'    => __( '충청남도', 'mshop-iv-delivery-ex' ),
					'충청북도'    => __( '충청북도', 'mshop-iv-delivery-ex' ),
				),
			),
			array(
				'title'   => __( '시군구', 'mshop-iv-delivery-ex' ),
				'id'      => 'sigungu',
				'css'     => 'width:100px',
				'default' => '',
				'type'    => 'select_sigungu',
				'options' => array(
					'' => __( '선택', 'mshop-iv-delivery-ex' )
				),
			),
			array(
				'title'   => __( '읍면동', 'mshop-iv-delivery-ex' ),
				'id'      => 'umdl',
				'css'     => 'width:100px',
				'default' => '',
				'type'    => 'select_umdl',
				'options' => array(
					'' => __( '선택', 'mshop-iv-delivery-ex' )
				),
			),
			array(
				'title'   => __( '우편번호', 'mshop-iv-delivery-ex' ),
				'id'      => 'postalcode',
				'css'     => '',
				'default' => '',
				'type'    => 'postalcode'
			),
			array(
				'title'   => __( '추가배송비', 'mshop-iv-delivery-ex' ),
				'id'      => 'fee',
				'css'     => 'width:100px;',
				'default' => '',
				'type'    => 'text'
			)
		);
	}

	function init_form_fields() {
		$this->form_fields = array(
			'enabled'                    => array(
				'title'   => __( '사용', 'mshop-iv-delivery-ex' ),
				'type'    => 'checkbox',
				'label'   => __( '사용', 'mshop-iv-delivery-ex' ),
				'default' => 'no',
			),
			'title'                      => array(
				'title'       => __( '방법 제목', 'mshop-iv-delivery-ex' ),
				'type'        => 'text',
				'description' => __( '결제시 사용자에게 보여주는 내용으로 변경이 가능합니다.', 'mshop-iv-delivery-ex' ),
				'default'     => __( '대한민국 지역배송', 'mshop-iv-delivery-ex' ),
				'desc_tip'    => true
			),
			'apply_iv_fee_free_shipping' => array(
				'title' => __( '무료 배송', 'mshop-iv-delivery-ex' ),
				'label' => __( '무료 배송인 경우에도 지역별 배송비를 부과합니다.', 'mshop-iv-delivery-ex' ),
				'id'    => 'apply-iv-fee-free_shipping',
				'type'  => 'checkbox',
				'css'   => ''
			),

			'apply_iv_fee_flat_rate' => array(
				'title' => __( '유료 배송', 'mshop-iv-delivery-ex' ),
				'label' => __( '유료 배송인 경우에도 지역별 배송비를 부과합니다.', 'mshop-iv-delivery-ex' ),
				'id'    => 'apply-iv-fee-flat_rate',
				'type'  => 'checkbox',
				'css'   => ''
			),

			array( 'title' => __( '대한민국 지역 배송비 목록', 'mshop-iv-delivery-ex' ), 'type' => 'title', 'desc' => '', 'id' => 'iv-fee-list' ),

			'mshop_iv_delivery_fee' => array(
				'title' => __( '대한민국 지역 배송비 목록', 'mshop-iv-delivery-ex' ),
				'desc'  => __( '대한민국 지역 추가 배송비를 지정합니다', 'mshop-iv-delivery-ex' ),
				'id'    => 'mshop_iv_delivery_fee',
				'type'  => 'mshop_iv_delivery_fee',
				'css'   => 'availability wc-enhanced-select',
			),
		);
	}

	public function enqueue_script() {
		wp_enqueue_style( 'mshop-setting-manager', MSIV()->plugin_url() . '/includes/admin/setting-manager/css/setting-manager.min.css' );
		wp_enqueue_script( 'mshop-setting-manager', MSIV()->plugin_url() . '/includes/admin/setting-manager/js/setting-manager.min.js', array( 'jquery', 'jquery-ui-core', 'underscore' ) );
	}
	public function get_setting_values( $setting ) {
		$values = array();

		if ( ! empty( $setting['id'] ) ) {
			if ( method_exists( $this, 'get_' . $setting['id'] ) ) {
				$values[ $setting['id'] ] = $this->{'get_' . $setting['id']}();
			} else if ( isset( $this->settings[ $setting['id'] ] ) ) {
				$values[ $setting['id'] ] = $this->settings[ $setting['id'] ];
			} else {
				$values[ $setting['id'] ] = ! empty( $setting['default'] ) ? $setting['default'] : '';
			}
		}

		if ( ! empty( $setting['elements'] ) && empty( $setting['repeater'] ) ) {
			foreach ( $setting['elements'] as $element ) {
				$values = array_merge( $values, $this->get_setting_values( $element ) );
			}
		}

		return $values;
	}

	public function get_shipping_options() {
		$options = array();

		foreach ( WC()->shipping()->get_shipping_methods() as $method_id => $shipping_methods ) {
			if ( ! in_array( $method_id, array( 'free_shipping', 'flat_rate', 'korea_zone_shipping' ) ) ) {
				$options[ $method_id ] = $shipping_methods->method_title;
			}
		}

		return $options;
	}

	public function get_basic_setting() {
		$tax_classes = wc_get_product_tax_class_options();
		array_shift( $tax_classes );

		return array(
			'id'       => 'basic-setting-tab',
			'title'    => '기본설정',
			'class'    => 'active',
			'type'     => 'Page',
			'elements' => array(
				array(
					'type'     => 'Section',
					'title'    => '기본 설정',
					'elements' => array(
						array(
							'id'        => 'enabled',
							'title'     => '활성화',
							'className' => '',
							'type'      => 'Toggle',
							'default'   => 'no',
							'desc'      => __( '<div class="desc2">엠샵 추가배송비 기능을 사용합니다.</div>', 'mshop-iv-delivery-ex' )
						),
						array(
							'id'        => 'title',
							'title'     => '이름',
							'className' => '',
							'type'      => 'Text',
							'showIf'    => array( 'enabled' => 'yes' ),
							'default'   => '추가배송비',
							'desc2'     => __( '<div class="desc2">추가배송비 이름을 입력하세요. 예 : 추가배송비, 도서산간 배송비 등</div>', 'mshop-iv-delivery-ex' )
						),
						array(
							'id'        => 'apply_iv_fee_free_shipping',
							'title'     => '무료배송 적용',
							'className' => '',
							'type'      => 'Toggle',
							'showIf'    => array( 'enabled' => 'yes' ),
							'default'   => 'no',
							'desc'      => __( '<div class="desc2">무료 배송인 경우에도 추가배송비를 부과합니다.</div>', 'mshop-iv-delivery-ex' )
						),
						array(
							'id'        => 'apply_iv_fee_one_time_shipping',
							'title'     => '정기구독 1회배송 적용',
							'className' => '',
							'type'      => 'Toggle',
							'showIf'    => array( array( 'enabled' => 'yes' ), array( 'apply_iv_fee_free_shipping' => class_exists( 'WC_Subscriptions' ) ? 'yes' : 'hidden' ) ) ,
							'default'   => 'no',
							'desc'      => __( '<div class="desc2">정기구독 1회 배송 상품도 갱신결제시 추가배송비를 부과합니다.</div>', 'mshop-iv-delivery-ex' )
						),
                        array(
                            'id'        => 'apply_iv_fee_free_coupon',
                            'title'     => '무료배송 쿠폰 적용',
                            'className' => '',
                            'type'      => 'Toggle',
                            'showIf'    => array( array( 'enabled' => 'yes' ), array( 'apply_iv_fee_free_shipping' => 'yes' ) ),
                            'default'   => 'no',
                            'desc'      => __( '<div class="desc2">무료배송 쿠폰을 적용한 경우 추가배송비를 부과하지 않습니다.</div>', 'mshop-iv-delivery-ex' )
                        ),
						array(
							'id'        => 'apply_iv_fee_flat_rate',
							'title'     => '유료배송 적용',
							'className' => '',
							'type'      => 'Toggle',
							'showIf'    => array( 'enabled' => 'yes' ),
							'default'   => 'no',
							'desc'      => __( '<div class="desc2">유료 배송인 경우에도 추가배송비를 부과합니다.</div>', 'mshop-iv-delivery-ex' )
						),
						array(
							'id'          => 'apply_iv_for_shipping_methods',
							'title'       => '기타 배송 수단',
							'className'   => '',
							'type'        => 'Select',
							'multiple'    => true,
							'showIf'      => array( 'enabled' => 'yes' ),
							'placeholder' => '배송수단을 선택하세요.',
							'options'     => $this->get_shipping_options(),
							'desc2'       => __( '<div class="desc2">선택된 배송 수단에 대해서는 항상 추가배송비를 부과합니다.</div>', 'mshop-iv-delivery-ex' )
						),
					)
				),
				array(
					'type'     => 'Section',
					'title'    => '세금 설정',
					'elements' => array(
						array(
							'id'        => 'calc_taxes',
							'title'     => '세금 계산 활성화',
							'className' => '',
							'type'      => 'Toggle',
							'default'   => 'no',
						),
						array(
							'id'        => 'tax_class',
							'title'     => '세금 클래스',
							'className' => '',
							'type'      => 'Select',
							'showIf'    => array( 'calc_taxes' => 'yes' ),
							'default'   => 'standard',
							'options'   => array_merge( array( 'standard' => __( 'Standard', 'woocommerce' ) ), $tax_classes )
						)
					)
				)
			)
		);
	}

	public function get_shipping_class() {
		$classes = array();

		$shipping_classes = WC()->shipping()->get_shipping_classes();

		foreach ( $shipping_classes as $shipping_class ) {
			$classes[ $shipping_class->slug ] = $shipping_class->name;
		}

		return $classes;
	}

	public function get_rule_setting() {
		$api_url = ( is_ssl() ? 'https' : 'http' ) . '://api.codemshop.com/address/mshop-iv-s2/index.php';

		return array(
			'type'     => 'Page',
			'title'    => '추가배송비 정책',
			'elements' => array(
				array(
					"id"             => "msiv_shipping_rules",
					"type"           => "SortableList",
					"title"          => "추가배송비 정책 목록",
					"showSaveButton" => true,
					"listItemType"   => "MShopShippingRule",
					"repeater"       => true,
					"api_url"        => $api_url,
					"template"       => array(
						'fee_rules' => array(),
					),
					"default"        => array(),
					"elements"       => array(
						'sido'       => array(
							"id"          => "sido",
							"title"       => __( "시도", 'mshop-iv-delivery-ex' ),
							"className"   => " fluid",
							"type"        => "Select",
							"placeHolder" => "시도를 선택하세요.",
							'default'     => 'role',
							'options'     => array(
								''        => __( '선택', 'mshop-iv-delivery-ex' ),
								'전국'      => __( '전국', 'mshop-iv-delivery-ex' ),
								'강원도'     => __( '강원도', 'mshop-iv-delivery-ex' ),
								'경기도'     => __( '경기도', 'mshop-iv-delivery-ex' ),
								'경상남도'    => __( '경상남도', 'mshop-iv-delivery-ex' ),
								'경상북도'    => __( '경상북도', 'mshop-iv-delivery-ex' ),
								'광주광역시'   => __( '광주광역시', 'mshop-iv-delivery-ex' ),
								'대구광역시'   => __( '대구광역시', 'mshop-iv-delivery-ex' ),
								'대전광역시'   => __( '대전광역시', 'mshop-iv-delivery-ex' ),
								'부산광역시'   => __( '부산광역시', 'mshop-iv-delivery-ex' ),
								'서울특별시'   => __( '서울특별시', 'mshop-iv-delivery-ex' ),
								'세종특별자치시' => __( '세종특별자치시', 'mshop-iv-delivery-ex' ),
								'울산광역시'   => __( '울산광역시', 'mshop-iv-delivery-ex' ),
								'인천광역시'   => __( '인천광역시', 'mshop-iv-delivery-ex' ),
								'전라남도'    => __( '전라남도', 'mshop-iv-delivery-ex' ),
								'전라북도'    => __( '전라북도', 'mshop-iv-delivery-ex' ),
								'제주특별자치도' => __( '제주특별자치도', 'mshop-iv-delivery-ex' ),
								'충청남도'    => __( '충청남도', 'mshop-iv-delivery-ex' ),
								'충청북도'    => __( '충청북도', 'mshop-iv-delivery-ex' ),
							),
						),
						'sigungu'    => array(
							"id"          => "sigungu",
							"className"   => " fluid",
							"placeHolder" => "시군구를 선택하세요.",
							"type"        => "Select",
							"multiple"    => true,
							'options'     => array(),
						),
						'bjymdl'     => array(
							"id"          => "bjymdl",
							"className"   => " search fluid",
							"multiple"    => true,
							"placeHolder" => "읍면동을 선택하세요.",
							"type"        => "Select",
							'options'     => array(),
						),
						'postalcode' => array(
							'id'        => 'postalcode',
							"className" => " fluid",
							'type'      => 'TextArea',
							'rows'      => 3
						),

						'fee_rules' => array(
							"id"        => "fee_rules",
							"className" => "",
							"editable"  => 'true',
							"sortable"  => 'true',
							"type"      => "SortableTable",
							"template"  => array(
								'target'         => 'always',
								'shipping_class' => '',
								'cost'           => '0',
							),
							"elements"  => array(
								array(
									'id'          => 'target',
									'title'       => __( '적용대상', 'mshop-iv-delivery-ex' ),
									"className"   => " four wide column fluid",
									"placeHolder" => __( '적용대상을 선택하세요.', 'mshop-iv-delivery-ex' ),
									'type'        => 'Select',
									'default'     => '',
									'options'     => array(
										'always'         => '항상',
										'shipping_class' => '특정 배송클래스',
										'min_amount'     => '구매금액',
									)
								),
								array(
									"id"        => "shipping_class",
									"title"     => __( "배송 클래스", 'mshop-iv-delivery-ex' ),
									"showIf"    => array( "target" => "shipping_class" ),
									"className" => " three wide column fluid",
									"type"      => "Select",
									"multiple"  => "true",
									'options'   => $this->get_shipping_class()
								),
								array(
									"id"        => "min_amount",
									"title"     => __( "구매금액(이하)", 'mshop-iv-delivery-ex' ),
									"showIf"    => array( "target" => "min_amount" ),
									"className" => " three wide column fluid",
									"type"      => "Text",
								),
								array(
									"id"          => "cost",
									"title"       => __( "추가배송비", 'mshop-iv-delivery-ex' ),
									"className"   => " four wide column fluid",
									"type"        => "Text",
									"default"     => "0",
									"placeholder" => "0"
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
			'title'    => __( '매뉴얼', 'mshop-iv-delivery-ex' ),
			'elements' => array()
		);
	}

	public function get_setting() {
		$settings = apply_filters( 'msiv-shipping-korea-zone-settings', array(
			$this->get_basic_setting(),
			$this->get_rule_setting(),
			$this->get_manual_link(),
		) );

		return
			array(
				'type'     => 'Tab',
				'id'       => 'iv-delivery-setting-tab',
				'elements' => $settings
			);
	}

	public function admin_options() {
		$GLOBALS['hide_save_button'] = true;
		$settings                    = $this->get_setting();

		$this->enqueue_script();
		wp_localize_script( 'mshop-setting-manager', 'mshop_setting_manager', array(
			'element'     => 'mshop-setting-wrapper',
			'ajaxurl'     => admin_url( 'admin-ajax.php' ),
			'action'      => msiv_ajax_command( 'save_settings' ),
			'settings'    => $settings,
			'slug'        => MSIV()->slug(),
			'domain'      => preg_replace( '#^https?://#', '', site_url() ),
			'licenseInfo' => get_option( 'msl_license_' . MSIV()->slug(), null )
		) );

		$licenseInfo = get_option( 'msl_license_' . MSIV()->slug(), json_encode( array(
			'slug'   => MSIV()->slug(),
			'domain' => preg_replace( '#^https?://#', '', site_url() )
		) ) );
		?>
        <style>
            .msiv-rule .ui.form .field {
                margin: 0px !important;;
            }

            .msiv-rule .ui.form .field textarea {
                font-size: 11px !important;
            }
        </style>
        <script>
            jQuery( document ).ready( function ( $ ) {
                $( this ).trigger( 'mshop-setting-manager', ['mshop-setting-wrapper', '200', <?php echo json_encode( $this->get_setting_values( $settings ) ); ?>, <?php echo $licenseInfo; ?>, null] );
                $( '.ui.top.attached .manual_link' ).off( 'click' ).on( 'click', function () {
                    window.open( 'https://manual.codemshop.com/docs/additional-fee/', '_blank' );
                    e.preventDefault();
                    e.stopPropagation();
                } )
            } );
        </script>

        <div id="mshop-setting-wrapper"></div>
		<?php
	}

	function is_available( $package ) {
		return false;
	}
}