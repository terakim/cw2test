<?php

/*
=====================================================================================
                엠샵 프리미엄 포인트 / Copyright 2014-2015 by CodeM(c)
=====================================================================================

  [ 우커머스 버전 지원 안내 ]

   워드프레스 버전 : WordPress 4.3.1 이상

   우커머스 버전 : WooCommerce 3.0 이상


  [ 코드엠 플러그인 라이센스 규정 ]

   (주)코드엠에서 개발된 워드프레스  플러그인을 사용하시는 분들에게는 다음 사항에 대한 동의가 있는 것으로 간주합니다.

   1. 코드엠에서 개발한 워드프레스 우커머스용 엠샵 프리미엄 포인트 플러그인의 저작권은 (주)코드엠에게 있습니다.
   
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

if ( ! class_exists( 'MSPS_Admin_Meta_Box_Order' ) ) :

	class MSPS_Admin_Meta_Box_Order {
		public static function output( $post ) {
			$order = MSPS_HPOS::get_order( $post );

			if ( $order ) {
				wp_enqueue_style( 'msps-admin', plugins_url( '/assets/css/admin.css', MSPS_PLUGIN_FILE ), array(), MSPS_VERSION );
				wp_enqueue_script( 'msps-admin', plugins_url( '/assets/js/admin/admin.js', MSPS_PLUGIN_FILE ), array( 'jquery' ), MSPS_VERSION );
				wp_localize_script( 'msps-admin', '_msps_admin', array(
					'ajax_url'      => admin_url( 'admin-ajax.php', 'relative' ),
					'slug'          => MSPS_AJAX_PREFIX,
					'order_id'      => $order->get_id(),
					'action_update' => msps_ajax_command( 'update_order_point' ),
					'_wpnonce'      => wp_create_nonce( 'mshop-point-ex' )
				) );

				$status = MSPS_Order::is_earn_processed( $order );
				$point  = MSPS_Order::get_earn_point( $order );

				?>
                <div class="msps_point_info">
                    <p>적립상태 : <?php echo $status ? '적립완료' : '적립예정'; ?></p>
                    <p class="flex"><span>적립포인트&nbsp;:&nbsp;</span><input type="text" name="msps_point" value="<?php echo number_format( $point, 2 ); ?>" <?php echo $status ? 'readonly' : ''; ?>></p>
                </div>
                <div class="msps_button_wrapper">
                    <input type="button" class="button msps_action_button update" value="적립예정 포인트 업데이트" <?php echo $status ? 'disabled' : ''; ?>>
                </div>
				<?php
			}
		}
	}
endif;
