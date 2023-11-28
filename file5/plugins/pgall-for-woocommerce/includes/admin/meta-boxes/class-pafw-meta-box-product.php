<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class PAFW_Meta_Box_Product {

	public static function init() {
		add_action( 'woocommerce_product_options_advanced', array( __CLASS__, 'output_confirm_day' ) );

        foreach( wc_get_product_types() as $product_type => $label ) {
	        add_action( 'woocommerce_process_product_meta_' . $product_type , array( __CLASS__, 'process_product_meta' ) );
        }
	}

	public static function output_confirm_day() {
		global $product_object;
		?>
        <script>
            jQuery( document ).ready( function ( $ ) {
                $( '#_pafw_npay_use_confirm_day' ).on( 'click', function () {
                    if ($( this ).is( ':checked' )) {
                        $( '.show_if_pafw_npay_use_confirm_day' ).removeClass( 'hidden' );
                    } else {
                        $( '.show_if_pafw_npay_use_confirm_day' ).addClass( 'hidden' );
                    }
                } );
            } );
        </script>
        <div class="options_group pafw_npay_confirm_day">
			<?php
			$use_confirm_day = $product_object->get_meta( '_pafw_npay_use_confirm_day' );
			$confirm_day     = $product_object->get_meta( '_pafw_npay_confirm_day' );

			woocommerce_wp_checkbox(
				array(
					'id'      => '_pafw_npay_use_confirm_day',
					'value'   => $use_confirm_day,
					'label'   => __( 'NPAY 이용완료일 설정', 'pgall-for-woocommerce' ),
					'cbvalue' => 'yes',
				)
			);

			woocommerce_wp_text_input(
				array(
					'id'            => '_pafw_npay_confirm_day',
					'wrapper_class' => 'show_if_pafw_npay_use_confirm_day ' . ( 'yes' == $use_confirm_day ? '' : 'hidden' ),
					'value'         => intval( $confirm_day ) > 0 ? $confirm_day : 30,
					'data_type'     => 'number',
					'label'         => __( '이용완료일', 'pgall-for-woocommerce' )
				)
			);
			?>
        </div>
		<?php
	}

	public static function process_product_meta( $post_id ) {
		$product = wc_get_product( $post_id );

		$product->update_meta_data( '_pafw_npay_use_confirm_day', pafw_get( $_REQUEST, '_pafw_npay_use_confirm_day', 'no' ) );
		$product->update_meta_data( '_pafw_npay_confirm_day', pafw_get( $_REQUEST, '_pafw_npay_confirm_day', '30' ) );

		$product->save_meta_data();
	}
}

PAFW_Meta_Box_Product::init();
