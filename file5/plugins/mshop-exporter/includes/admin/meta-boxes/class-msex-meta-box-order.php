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

class MSEX_Meta_Box_Order {
	public static function add_meta_boxes() {
		add_meta_box( 'msex-sheet-info', __( '송장관리', 'mshop-exporter' ), array ( __CLASS__, 'sheet_info' ), 'shop_order', 'side', 'default' );

		add_action( 'woocommerce_admin_order_item_headers', array ( __CLASS__, 'maybe_attach_edit_sheet_info_hook' ) );
	}
	public static function maybe_attach_edit_sheet_info_hook( $order ) {
		if ( apply_filters( 'msex_skip_edit_sheet_info', 'naverpay' != $order->get_payment_method() && 'shop_order' == $order->get_type(), $order ) ) {
			add_action( 'woocommerce_after_order_itemmeta', array ( __CLASS__, 'output_order_item_sheet_info' ), 10, 3 );
		}
	}
	public static function output_order_item_sheet_info( $item_id, $item, $product ) {
		if ( 'line_item' == $item->get_type() ) {
			include( 'views/html-sheet-info.php' );
		}
	}
	public static function sheet_info( $post ) {
		global $wp_scripts;
		$order = wc_get_order( $post->ID );

		wp_enqueue_style( 'msex-meta-box-order', MSEX()->plugin_url() . '/assets/css/admin/msex-meta-box-order.css', array (), MSEX_VERSION );
		wp_enqueue_script( 'msex-meta-box-order', MSEX()->plugin_url() . '/assets/js/admin/msex-meta-box-order.js', array ( 'jquery' ), MSEX_VERSION );
		wp_localize_script( 'msex-meta-box-order', '_msex_meta_box_order', array (
			'ajax_url'      => admin_url( 'admin-ajax.php', 'relative' ),
			'slug'          => MSEX_AJAX_PREFIX,
			'order_id'      => msex_get_object_property( $order, 'id' ),
			'action_delete' => msex_ajax_command( 'delete_sheet_info' ),
			'action_update' => msex_ajax_command( 'update_sheet_info' ),
			'_wpnonce'      => wp_create_nonce( 'mshop-exporter' )
		) );

		wp_print_scripts( 'msex-meta-box-order' );


		$dlv_company = msex_load_dlv_company_info();

		$dlv_code      = msex_get_meta( $order, '_msex_dlv_code', true );
		$dlv_name      = msex_get_meta( $order, '_msex_dlv_name', true );
		$sheet_no      = msex_get_meta( $order, '_msex_sheet_no', true );
		$dlv_url       = msex_get_track_url( $dlv_code, $sheet_no );
		$register_date = msex_get_meta( $order, '_msex_register_date', true );

		?>
        <div class="msex-sheet-info">
            <p>택배사</p>
            <select name="msex_dlv_code">
                <option value="">택배사를 선택하세요.</option>
				<?php foreach ( $dlv_company as $company ) : ?>
					<?php echo sprintf( '<option value="%s" %s>%s</option>', $company['dlv_code'], $dlv_code == $company['dlv_code'] ? 'selected' : '', $company['dlv_name'] ); ?>
				<?php endforeach; ?>
            </select>
            <p>송장번호</p>
            <input type="text" name="msex_sheet_no" value="<?php echo $sheet_no; ?>">
			<?php if ( ! empty( $register_date ) ) : ?>
                <p>등록일 : <?php echo $register_date; ?></p>
			<?php endif; ?>
        </div>
        <div class="msex_button_wrapper">
            <input type="button" class="button msex_action_button delete" <?php echo empty( $dlv_code ) ? 'disabled' : ''; ?> value="송장정보 삭제">
            <input type="button" class="button msex_action_button update" value="송장정보 업데이트">
        </div>
		<?php if ( ! empty( $dlv_url ) ) : ?>
            <div class="msex_button_wrapper">
				<?php echo sprintf( '<a target="_blank" style="text-align: center;" class="button msex_action_button" href="%s">배송조회</a>', $dlv_url ); ?>
            </div>
		<?php endif; ?>
		<?php
	}
}
