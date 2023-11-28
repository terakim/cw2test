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

if ( ! class_exists( 'MSEX_Meta_Box_Product_Fields' ) ) :

	class MSEX_Meta_Box_Product_Fields {
		public static function woocommerce_product_data_tabs( $tabs ) {
			$tabs['custom_product_fields'] = array(
				'label'  => __( '상품 필드', 'mshop-exporter' ),
				'target' => 'msex_product_fields',
				'class'  => array(),
			);

			return $tabs;
		}

		public static function get_product_field_data() {
            $data   = array();
            $fields = get_option( 'msex_product_fields', array() );

            foreach ( $fields as $field ) {
                if ( 'yes' == $field['enabled'] && ! empty( $field['key'] ) && ! empty( $field['type'] ) ) {
                    $data[] = apply_filters( 'msex_product_field_data', array(
                        'id'        => $field['key'],
                        'title'     => $field['title'],
                        'type'      => $field['type'],
                        "className" => "fluid",
                    ), $field );
                }
            }

            return $data;
        }

        public static function get_setting_fields() {
		    if ( ! empty( self::get_product_field_data() ) ) {
		        $elements = self::get_product_field_data();
            } else {
                $elements = array(
                    array(
                        "id"       => "msex_product_fields_notice",
                        'type'     => 'Label',
                        'readonly' => 'yes',
                        'desc2'    => sprintf( __( '<div class="desc2" style="color: #d47373;">등록된 상품 필드가 없습니다. 상품 필드는 <a href="%s" target="_blank">[엠샵 업다운로드 > 상품 필드 관리]</a>에서 등록하실 수 있습니다.</div>', 'mshop-exporter' ), admin_url( '/admin.php?page=msex_product_fields' ) )
                    )
                );
            }

            return array(
                'type'        => 'Page',
                'dom-element' => 'msex_product_fields',
                'elements'    => array(
                    array(
                        "title"          => __( "상품 필드", 'mshop-exporter' ),
                        "type"           => "Section",
                        "hideSaveButton" => true,
                        "elements"       => $elements
                    )
                )
            );
        }

        static function enqueue_scripts() {
            wp_enqueue_style( 'mshop-setting-manager', MSEX()->plugin_url() . '/includes/admin/setting-manager/css/setting-manager.min.css' );
            wp_enqueue_script( 'mshop-setting-manager', MSEX()->plugin_url() . '/includes/admin/setting-manager/js/setting-manager.min.js', array ( 'jquery', 'jquery-ui-core', 'underscore' ) );
        }

        public static function woocommerce_product_data_panels() {
            global $thepostid, $post, $wp_scripts;
            $thepostid = empty( $thepostid ) ? $post->ID : $thepostid;

            include_once MSEX()->plugin_path() . '/includes/admin/setting-manager/mshop-setting-helper.php';
            $settings = self::get_setting_fields();

            self::enqueue_scripts();

            if ( empty( $wp_scripts->get_data( 'mshop-setting-manager', 'data' ) ) ) {
                wp_localize_script( 'mshop-setting-manager', 'mshop_setting_manager', array(
                    'element'  => 'mshop-setting-msex_product_fields',
                    'ajaxurl'  => admin_url( 'admin-ajax.php' ),
                    'settings' => $settings,
                    'slug'     => MSEX()->slug(),
                ) );
            }

            $values = MSSHelper::get_settings( self::get_setting_fields(), $thepostid );
            ?>
            <script>
                jQuery( document ).ready( function () {
                    jQuery( this ).trigger( 'mshop-setting-manager', ['mshop-setting-msex_product_fields', '<?php echo 'msex-product-fields-' . $thepostid; ?>', <?php echo json_encode( $values ); ?>, null, <?php echo json_encode( $settings ); ?> ] );
                } );
            </script>
            <?php

            echo '<div id="msex_product_fields" class="panel woocommerce_options_panel">';
            echo '<div id="mshop-setting-msex_product_fields" class="mshop-setting-msex_product_fields mshop-setting-product-meta mshop-setting-wrapper"></div>';
            echo '</div>';
            echo '<input type="hidden" name="msex_product_fields" value="' . esc_html( json_encode( $values ) ) . '">';
        }

        public static function process_product_meta( $post_id ) {
            if ( isset( $_POST['msex_product_fields'] ) ) {
                $settings = json_decode( stripslashes( $_POST['msex_product_fields'] ) );

                if ( $settings ) {
                    foreach ( $settings as $key => $value ) {
                        update_post_meta( $post_id, $key, $value );
                    }
                }
            }
        }
	}

endif;
