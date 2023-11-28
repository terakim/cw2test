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

class MSEX_Meta_Box_User_Template {
	static function get_setting_fields() {
		$meta_key_types = apply_filters( 'msex_user_meta_key', array ( 'field_type' => 'user_meta,custom,text' ) );

		return array (
			'type'        => 'Page',
			'title'       => '주문 정보',
			'dom-element' => 'msex-fields',
			'elements'    => array (
				array (
					'type'              => 'Section',
					'hideSectionHeader' => true,
					'elements'          => array (
						array (
							'id'        => '_msex_slug',
							'title'     => '슬러그',
							'className' => 'fluid',
							'type'      => 'Text',
							'default'   => "user_template",
							'desc2'     => __( '<div class="desc2">슬러그는 영문 및 숫자로 입력해주세요.</div>', 'mshop-exporter' ),
						),
						array (
							'id'          => '_msex_download_type',
							'title'       => '다운로드 포맷',
							'className'   => 'fluid',
							'type'        => 'Select',
							'default'     => "excel",
							'placeholder' => "필드선택",
							"options"     => array (
								"excel" => "EXCEL",
								"csv"   => "CSV"
							)
						),
						array (
							'id'          => '_msex_posts_per_page',
							'title'       => '한 번에 처리할 아이템 개수',
							'className'   => '',
							'type'        => 'Text',
							'default'     => '250',
							'placeholder' => '',
							'desc2'       => '<div class="desc2">이 값이 10일때 다운로드 받으려는 사용자가 100명이면 10번에 나눠서 데이터를 생성합니다.</div>'
						),
						array (
							"id"       => "_msex_fields",
							"type"     => "SortableTable",
							"sortable" => true,
							"editable" => true,
							"repeater" => true,
							"default"  => MSEX_Fields::get_default_user_fields(),
							"elements" => array (
								array (
									'id'          => 'field_type',
									'title'       => '필드',
									"className"   => "five wide column fluid",
									"type"        => "Select",
									'default'     => '',
									'placeholder' => "필드선택",
									"options"     => apply_filters( 'msex_user_field_type',
										array (
                                            "id"                       => "고객번호",
                                            "user_name"                => "이름",
                                            "user_login"               => "아이디",
                                            "user_email"               => "이메일",
                                            "user_role_name"           => "사이트 역할",
                                            "user_posts_count"         => "글 작성 수",
                                            "user_status"              => "회원 상태",
                                            "user_zipcode"             => "우편번호",
                                            "user_address1"            => "기본 주소",
                                            "user_address2"            => "상세 주소",
                                            "user_phone"               => "전화번호",
                                            "mshop_point"              => "보유 포인트",
                                            "free_point"               => "무상 포인트",
                                            "recommender_point"        => "추천인 포인트",
                                            "mshop_money_spent"        => "총 결제 금액",
                                            "mshop_order_total"        => "총 주문 금액",
                                            "register_date"            => "회원가입일",
                                            "last_login_date"          => "마지막 로그인",
                                            "user_order_count"         => "결제된 주문 개수",
                                            "subscription_order_count" => "정기결제 주문 개수",
                                            "subscription_count"       => "정기결제권 개수",
                                            "subscription_active"      => "활성화 된 정기결제권 여부",
                                            "user_meta"                => "사용자 메타",
                                            "custom"                   => "커스텀 필드",
                                            "text"                     => "텍스트",
										)
									)
								),
								array (
									'id'          => "field_label",
									"className"   => "five wide column fluid",
									"title"       => "엑셀 표기 이름",
									"type"        => "Text",
									'placeholder' => "엑셀에서 표시할 이름을 입력하세요.",
								),
								array (
									'id'          => "meta_key",
									'showIf'      => $meta_key_types,
									"title"       => "메타키",
									"className"   => "four wide column fluid",
									"type"        => "Text",
									'placeholder' => "추가 필드를 입력하세요",
								)
							)
						)
					)
				)
			)
		);
	}

	static function enqueue_scripts() {
		wp_enqueue_style( 'mshop-setting-manager', MSEX()->plugin_url() . '/includes/admin/setting-manager/css/setting-manager.min.css' );
		wp_enqueue_script( 'mshop-setting-manager', MSEX()->plugin_url() . '/includes/admin/setting-manager/js/setting-manager.min.js', array ( 'jquery', 'jquery-ui-core', 'underscore' ) );
	}

	public static function output_meta_box( $post ) {
		require_once MSEX()->plugin_path() . '/includes/admin/setting-manager/mshop-setting-helper.php';
		self::enqueue_scripts();

		$settings = self::get_setting_fields();

		$license_info = null;

		$license_info = json_decode( get_option( 'msl_license_' . MSEX()->slug(), json_encode( array (
			'slug'   => MSEX()->slug(),
			'domain' => preg_replace( '#^https?://#', '', home_url() )
		) ) ), true );

		$license_info = apply_filters( 'mshop_get_license', $license_info, MSEX()->slug() );
		wp_localize_script( 'mshop-setting-manager', 'mshop_setting_manager', array (
			'element'     => 'mshop-setting-wrapper',
			'ajaxurl'     => admin_url( 'admin-ajax.php' ),
			'settings'    => $settings,
			'slug'        => MSEX()->slug(),
			'domain'      => preg_replace( '#^https?://#', '', site_url() ),
			'licenseInfo' => json_encode( $license_info )
		) );

		wp_nonce_field( 'msex_save_data', 'msex_meta_nonce' );

		?>
        <style>
            div#msex-fields div.inside {
                margin: 0 !important;
                padding: 0 !important;
            }

            div#mshop-setting-wrapper,
            div#mshop-setting-wrapper > div {
                padding: 0 !important;
                border: none;
                box-shadow: none;
            }

            div.mshop-setting-section > table {
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
            }

            div.mshop-setting-section > table > tbody > tr:first-child > td {
                border-top: 1px solid rgba(34, 36, 38, 0.1) !important;
            }

            table#_msex_fields td {
                border-top: 1px solid rgba(34, 36, 38, 0.1) !important;
            }
        </style>
        <script>
            jQuery( document ).ready( function ( $ ) {
                $( this ).trigger( 'mshop-setting-manager', ['mshop-setting-wrapper', '200', <?php echo json_encode( MSSHelper::get_settings( $settings, $post->ID ) ); ?>, <?php echo json_encode( $license_info ); ?>, null] );
            } );
        </script>

        <div id="mshop-setting-wrapper"></div>
        <input type="text" name="msex-fields" style="display: none;" value="<?php echo esc_html( get_post_meta( $post->ID, 'msex_fields', true ) ); ?>">
		<?php
	}
	public static function save_meta_box( $post_id, $post ) {
		$values = json_decode( stripslashes( msex_get( $_REQUEST, 'msex-fields', '' ) ), true );
		update_post_meta( $post_id, 'msex_fields', $_REQUEST['msex-fields'] );
		update_post_meta( $post_id, '_msex_fields', msex_get( $values, '_msex_fields' ) );
		update_post_meta( $post_id, '_msex_download_type', msex_get( $values, '_msex_download_type' ) );
		update_post_meta( $post_id, '_msex_slug', msex_get( $values, '_msex_slug' ) );
		update_post_meta( $post_id, '_msex_posts_per_page', msex_get( $values, '_msex_posts_per_page' ) );
	}
}
