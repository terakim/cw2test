<?php

/*
=====================================================================================
                ﻿엠샵 멤버스 / Copyright 2015 by CodeM(c)
=====================================================================================

  [ 우커머스 버전 지원 안내 ]

   워드프레스 버전 : WordPress 4.3

   우커머스 버전 : WooCommerce 2.4


  [ 코드엠 플러그인 라이센스 규정 ]

   (주)코드엠에서 개발된 워드프레스  플러그인을 사용하시는 분들에게는 다음 사항에 대한 동의가 있는 것으로 간주합니다.

   1. 코드엠에서 개발한 워드프레스 우커머스용 엠샵 멤버스 플러그인의 저작권은 (주)코드엠에게 있습니다.
   
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

class MSM_Meta_Box_Attachment_Info {

    public static function init() {
	    add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes'), 1 );
    }

	public static function add_meta_boxes() {
		add_meta_box( 'mshop-members-attachment-info', __( '의뢰정보', 'mshop-members-s2' ), array( __CLASS__, 'output_attachment_info'), apply_filters( 'msm_attachment_info_post_type', array( 'post' ) ), 'normal', 'core' );
	}
    public static function output_attachment_info( $post ) {

	    $metas = MSM_Meta::get_post_meta( $post->ID, '_msm_form' );

	    if ( ! empty( $metas ) ) {
		    ?>
		    <div class="woocommerce_order_items_wrapper wc-order-items-editable">
			    <table cellpadding="0" cellspacing="0" class="wp-list-table widefat fixed striped">
				    <tbody>
				    <tr>
					    <td class="title" style="width: 15%;">제목</td>
					    <td class="content"><?php echo $post->post_title; ?></td>
				    </tr>
				    <tr>
					    <td>내용</td>
					    <td><p><?php echo str_replace( "\r\n", '<br>', $post->post_content ); ?></p></td>
				    </tr>
				    <?php
				    foreach ( $metas as $meta ) {
					    echo '<tr>';
					    echo '<td>' . $meta['title'] . '</td>';
					    echo '<td class="meta_value">' . str_replace( "\n", "<br>", ! empty( $meta['label'] ) ? $meta['label'] : $meta['value'] ) . '</td>';
					    echo '</tr>';
				    }
				    ?>
				    </tbody>
			    </table>
		    </div>
		    <?php
	    }

    }

}
