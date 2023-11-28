<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class MSEX_Admin_Post_Types {

	public static function init() {
		add_filter( 'bulk_actions-edit-shop_order', array ( __CLASS__, 'add_bulk_actions' ) );
		add_filter( 'bulk_actions-edit-shop_subscription', array ( __CLASS__, 'add_bulk_actions' ) );
		add_filter( 'bulk_actions-edit-ip_order', array ( __CLASS__, 'add_bulk_actions' ) );
		add_filter( 'bulk_actions-edit-product', array ( __CLASS__, 'add_bulk_actions' ) );
		add_filter( 'bulk_actions-users', array ( __CLASS__, 'add_bulk_actions' ) );

		add_action( 'restrict_manage_posts', array ( __CLASS__, 'restrict_manage_type' ) );
		add_filter( 'request', array ( __CLASS__, 'request_query' ) );
	}

	public static function add_bulk_actions( $action ) {
		global $typenow, $pagenow;

		if ( 'edit.php' == $pagenow && in_array( $typenow, wc_get_order_types() ) ) {
			self::enqueue_scripts( 'msex_order' );
		} else if ( 'edit.php' == $pagenow && 'product' == $typenow ) {
			self::enqueue_scripts( 'msex_product' );
		} else if ( 'users.php' == $pagenow ) {
			self::enqueue_scripts( 'msex_user' );
		}

		$action['msex-export'] = __( '엠샵 다운로드', 'mshop-exporter' );

		return $action;
	}

	public static function request_query( $vars ) {
		global $typenow;

		if ( in_array( $typenow, wc_get_order_types( 'order-meta-boxes' ) ) ) {
			$date_from = msex_get( $_GET, 'msex_date_from' );
			$date_to   = msex_get( $_GET, 'msex_date_to' );

			if ( ! empty( $date_from ) && ! empty( $date_to ) ) {
				if ( 'paid_date' == msex_get( $_GET, 'date_type' ) ) {
					$vars['meta_query'] = array (
						array (
							'key'     => '_paid_date',
							'value'   => array ( $date_from . ' 00:00:00', $date_to . ' 23:59:59' ),
							'compare' => 'BETWEEN',
							'type'    => 'DATE'
						),
					);
				} else {
					$vars['date_query'] = array (
						array (
							'after'     => $date_from,
							'before'    => $date_to,
							'inclusive' => true,
						),
					);
				}
			}
		}

		return $vars;
	}

	public static function restrict_manage_type() {
		global $typenow;

		if ( in_array( $typenow, wc_get_order_types( 'order-meta-boxes' ) ) ) {
			self::shop_order_filters();
		}
	}

	public static function shop_order_filters() {
		global $typenow;

		$date_from = empty( $_REQUEST['msex_date_from'] ) ? '' : $_REQUEST['msex_date_from'];
		$date_to   = empty( $_REQUEST['msex_date_to'] ) ? '' : $_REQUEST['msex_date_to'];

		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_style( 'jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );

		?>
        <script type="text/javascript">
            jQuery( document ).ready( function () {
                jQuery( function ( $ ) {
                    $.datepicker.regional[ 'ko' ] = {
                        closeText         : '닫기',
                        prevText          : '이전달',
                        nextText          : '다음달',
                        currentText       : '오늘',
                        monthNames        : ['1월', '2월', '3월', '4월', '5월', '6월',
                            '7월', '8월', '9월', '10월', '11월', '12월'],
                        monthNamesShort   : ['1월', '2월', '3월', '4월', '5월', '6월',
                            '7월', '8월', '9월', '10월', '11월', '12월'],
                        dayNames          : ['일요일', '월요일', '화요일', '수요일', '목요일', '금요일', '토요일'],
                        dayNamesShort     : ['일', '월', '화', '수', '목', '금', '토'],
                        dayNamesMin       : ['일', '월', '화', '수', '목', '금', '토'],
                        weekHeader        : 'Wk',
                        dateFormat        : 'yy-mm-dd',
                        firstDay          : 0,
                        isRTL             : false,
                        showMonthAfterYear: true,
                        yearSuffix        : '년'
                    };
                    $.datepicker.setDefaults( $.datepicker.regional[ 'ko' ] );
                } );

                jQuery( function () {
                    jQuery( 'input.mshop_datepicker' ).datepicker();
                } );
            } );
        </script>
		<?php
		$search_types = array ( 'create_date' => '생성일', 'paid_date' => '결제일' );

		$selected_date_type = empty( $_REQUEST['date_type'] ) ? 'create_date' : $_REQUEST['date_type'];

		?>
        <select name="date_type" style="float: none;">
			<?php foreach ( $search_types as $key => $value ) : ?>
                <option value="<?php echo $key; ?>" <?php echo $key == $selected_date_type ? 'selected' : ''; ?>><?php echo $value; ?></option>
			<?php endforeach; ?>
        </select>
        <input type="text" class="mshop_datepicker" name="msex_date_from" value="<?php echo $date_from; ?>" placeholder="From date">
        <input type="text" class="mshop_datepicker" name="msex_date_to" value="<?php echo $date_to; ?>" placeholder="To date">
		<?php
	}

	static function enqueue_scripts( $template_type ) {
		$params = array ();

		$templates = get_posts(
			array (
				'post_type'      => $template_type,
				'posts_per_page' => -1,
				'post_status'    => 'publish'
			)
		);

		foreach ( $templates as $template ) {
			$params[ $template->ID ] = array (
				'posts_per_page' => get_post_meta( $template->ID, '_msex_posts_per_page', true )
			);
		}

		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'jquery-ui-progressbar' );
		wp_enqueue_style( 'msex', plugins_url( '/assets/css/msex-exporter.css', MSEX_PLUGIN_FILE ), array (), MSEX_VERSION );
		wp_enqueue_script( 'msex', plugins_url( '/assets/js/admin/msex-exporter.js', MSEX_PLUGIN_FILE ), array ( 'jquery', 'underscore' ), MSEX_VERSION );
		wp_localize_script( 'msex', '_msex', array (
			'ajaxurl'     => admin_url( 'admin-ajax.php' ),
			'slug'        => MSEX_AJAX_PREFIX,
			'params'      => $params,
			'bulk_action' => 'msex-export'
		) );

		wc_get_template( 'download_popup.php', array ( 'templates' => $templates ), '', MSEX()->template_path() );
	}
}

MSEX_Admin_Post_Types::init();