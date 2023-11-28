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
	exit;
}

if ( ! class_exists( 'MSEX_Settings_Product_Fields' ) ) :

	class MSEX_Settings_Product_Fields {

		static function update_settings() {
			require_once MSEX()->plugin_path() . '/includes/admin/setting-manager/mshop-setting-helper.php';
			$_REQUEST = array_merge( $_REQUEST, json_decode( stripslashes( $_REQUEST['values'] ), true ) );

			MSSHelper::update_settings( self::get_setting_fields() );

			wp_send_json_success();
		}

        static function get_field_types() {
            return apply_filters( 'msex_product_field_types', array(
                "Text"     => __( 'Text', 'mshop-exporter' ),
                "TextArea" => __( 'TextArea', 'mshop-exporter' ),
                "Toggle"   => __( 'Toggle', 'mshop-exporter' ),
            ) );
        }

        static function get_setting_fields() {
            return array(
                'type'     => 'Page',
                'title'    => __( '상품 필드', 'mshop-exporter' ),
                'class'    => '',
                'elements' => array(
                    array(
                        'type'     => 'Section',
                        'title'    => __( '상품 필드', 'mshop-exporter' ),
                        'elements' => array(
                            array(
                                "id"        => "msex_product_fields",
                                "className" => "",
                                "editable"  => 'true',
                                "sortable"  => 'true',
                                "type"      => "SortableTable",
                                "template"  => array(
                                    'enabled' => 'yes',
                                    'type'    => 'text',
                                ),
                                "elements"  => apply_filters( 'msex_product_fields', array(
                                    array(
                                        "id"            => "enabled",
                                        "title"         => __( "활성화", 'mshop-exporter' ),
                                        "className"     => "center aligned one wide column fluid",
                                        "cellClassName" => "center aligned",
                                        "type"          => "Toggle",
                                        'default'       => 'yes',
                                    ),
                                    array(
                                        "id"            => "type",
                                        "title"         => __( "종류", 'mshop-exporter' ),
                                        "className"     => "center aligned three wide column fluid",
                                        "cellClassName" => "fluid",
                                        "type"          => "Select",
                                        "placeholder"   => __( '필드 타입', 'mshop-exporter' ),
                                        'default'       => '',
                                        'options'       => self::get_field_types()
                                    ),
                                    array(
                                        "id"            => "key",
                                        "title"         => __( "메타키", 'mshop-exporter' ),
                                        "className"     => "center aligned four wide column fluid",
                                        "default"       => "",
                                        "type"          => "Text",
                                    ),
                                    array(
                                        "id"            => "title",
                                        "title"         => __( "제목", 'mshop-exporter' ),
                                        "className"     => "center aligned four wide column fluid",
                                        "default"       => "",
                                        "type"          => "Text",
                                    )
                                ) )
                            ),
                        )
                    ),

                )
            );
        }

		static function enqueue_scripts() {
			wp_enqueue_style( 'mshop-setting-manager', MSEX()->plugin_url() . '/includes/admin/setting-manager/css/setting-manager.min.css' );
			wp_enqueue_script( 'mshop-setting-manager', MSEX()->plugin_url() . '/includes/admin/setting-manager/js/setting-manager.min.js', array( 'jquery', 'jquery-ui-core', 'underscore' ) );
		}
		public static function output() {
			require_once MSEX()->plugin_path() . '/includes/admin/setting-manager/mshop-setting-helper.php';
			$settings = self::get_setting_fields();

			self::enqueue_scripts();

			$license_info = null;

			$license_info = json_decode( get_option( 'msl_license_' . MSEX()->slug(), json_encode( array(
				'slug'   => MSEX()->slug(),
				'domain' => preg_replace( '#^https?://#', '', home_url() )
			) ) ), true );

			$license_info = apply_filters( 'mshop_get_license', $license_info, MSEX()->slug() );
			wp_localize_script( 'mshop-setting-manager', 'mshop_setting_manager', array(
				'element'     => 'mshop-setting-wrapper',
				'ajaxurl'     => admin_url( 'admin-ajax.php' ),
				'action'      => msex_ajax_command( 'update_product_field_settings' ),
				'settings'    => $settings,
				'slug'        => MSEX()->slug(),
				'domain'      => preg_replace( '#^https?://#', '', site_url() ),
				'licenseInfo' => json_encode( $license_info )
			) );

			?>
            <script>
                jQuery( document ).ready( function () {
                    jQuery( this ).trigger( 'mshop-setting-manager', ['mshop-setting-wrapper', '100', <?php echo json_encode( MSSHelper::get_settings( $settings ) ); ?>, <?php echo json_encode( $license_info ); ?>, null] );
                } );
            </script>

            <div id="mshop-setting-wrapper"></div>
			<?php
		}
	}
endif;



