<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class MSEX_Post_types {
	public static function init() {
		add_action( 'init', array ( __CLASS__, 'register_post_types' ), 5 );
	}
	public static function register_post_types() {
		if ( ! post_type_exists( 'msex_order' ) ) {
			$labels = array (
				'name'               => _x( '주문 다운로드 템플릿', 'post type general name', 'mshop-exporter' ),
				'singular_name'      => _x( '주문 다운로드 템플릿', 'post type singular name', 'mshop-exporter' ),
				'menu_name'          => _x( '주문 다운로드 템플릿', 'admin menu', 'mshop-exporter' ),
				'name_admin_bar'     => _x( '주문 다운로드 템플릿', 'add new on admin bar', 'mshop-exporter' ),
				'add_new'            => _x( '주문 다운로드 템플릿 추가', 'mshop_members_form', 'mshop-exporter' ),
				'add_new_item'       => __( '주문 다운로드 템플릿 추가', 'mshop-exporter' ),
				'new_item'           => __( '신규 주문 다운로드 템플릿', 'mshop-exporter' ),
				'edit_item'          => __( '주문 다운로드 템플릿 편집', 'mshop-exporter' ),
				'view_item'          => __( '주문 다운로드 템플릿 보기', 'mshop-exporter' ),
				'all_items'          => __( '주문 다운로드 템플릿 목록', 'mshop-exporter' ),
				'search_items'       => __( '주문 다운로드 템플릿 검색', 'mshop-exporter' ),
				'not_found'          => __( '주문 다운로드 템플릿 항목이 없습니다.', 'mshop-exporter' ),
				'not_found_in_trash' => __( '휴지통에 주문 다운로드 템플릿 항목이 없습니다.', 'mshop-exporter' )
			);

			$args = array (
				'labels'             => $labels,
				'description'        => __( 'Description.', 'mshop-exporter' ),
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => false,
				'menu_icon'          => MSEX()->plugin_url() . '/assets/images/mshop-icon.png',
				'query_var'          => true,
				'capability_type'    => 'post',
				'map_meta_cap'       => true,
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => null,
				'supports'           => array ( 'title' )
			);

			register_post_type( 'msex_order', $args );
		}

		if ( ! post_type_exists( 'msex_product' ) ) {
			$labels = array (
				'name'               => _x( '상품 다운로드 템플릿', 'post type general name', 'mshop-exporter' ),
				'singular_name'      => _x( '상품 다운로드 템플릿', 'post type singular name', 'mshop-exporter' ),
				'menu_name'          => _x( '상품 다운로드 템플릿', 'admin menu', 'mshop-exporter' ),
				'name_admin_bar'     => _x( '상품 다운로드 템플릿', 'add new on admin bar', 'mshop-exporter' ),
				'add_new'            => _x( '상품 다운로드 템플릿 추가', 'mshop_members_form', 'mshop-exporter' ),
				'add_new_item'       => __( '상품 다운로드 템플릿 추가', 'mshop-exporter' ),
				'new_item'           => __( '신규 상품 다운로드 템플릿', 'mshop-exporter' ),
				'edit_item'          => __( '상품 다운로드 템플릿 편집', 'mshop-exporter' ),
				'view_item'          => __( '상품 다운로드 템플릿 보기', 'mshop-exporter' ),
				'all_items'          => __( '상품 다운로드 템플릿 목록', 'mshop-exporter' ),
				'search_items'       => __( '상품 다운로드 템플릿 검색', 'mshop-exporter' ),
				'not_found'          => __( '상품 다운로드 템플릿 항목이 없습니다.', 'mshop-exporter' ),
				'not_found_in_trash' => __( '휴지통에 상품 다운로드 템플릿 항목이 없습니다.', 'mshop-exporter' )
			);

			$args = array (
				'labels'             => $labels,
				'description'        => __( 'Description.', 'mshop-exporter' ),
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => false,
				'menu_icon'          => MSEX()->plugin_url() . '/assets/images/mshop-icon.png',
				'query_var'          => true,
				'capability_type'    => 'post',
				'map_meta_cap'       => true,
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => null,
				'supports'           => array ( 'title' )
			);

			register_post_type( 'msex_product', $args );
		}

		if ( ! post_type_exists( 'msex_user' ) ) {
			$labels = array (
				'name'               => _x( '사용자 다운로드 템플릿', 'post type general name', 'mshop-exporter' ),
				'singular_name'      => _x( '사용자 다운로드 템플릿', 'post type singular name', 'mshop-exporter' ),
				'menu_name'          => _x( '사용자 다운로드 템플릿', 'admin menu', 'mshop-exporter' ),
				'name_admin_bar'     => _x( '사용자 다운로드 템플릿', 'add new on admin bar', 'mshop-exporter' ),
				'add_new'            => _x( '사용자 다운로드 템플릿 추가', 'mshop_members_form', 'mshop-exporter' ),
				'add_new_item'       => __( '사용자 다운로드 템플릿 추가', 'mshop-exporter' ),
				'new_item'           => __( '신규 사용자 다운로드 템플릿', 'mshop-exporter' ),
				'edit_item'          => __( '사용자 다운로드 템플릿 편집', 'mshop-exporter' ),
				'view_item'          => __( '사용자 다운로드 템플릿 보기', 'mshop-exporter' ),
				'all_items'          => __( '사용자 다운로드 템플릿 목록', 'mshop-exporter' ),
				'search_items'       => __( '사용자 다운로드 템플릿 검색', 'mshop-exporter' ),
				'not_found'          => __( '사용자 다운로드 템플릿 항목이 없습니다.', 'mshop-exporter' ),
				'not_found_in_trash' => __( '휴지통에 사용자 다운로드 템플릿 항목이 없습니다.', 'mshop-exporter' )
			);

			$args = array (
				'labels'             => $labels,
				'description'        => __( 'Description.', 'mshop-exporter' ),
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => false,
				'menu_icon'          => MSEX()->plugin_url() . '/assets/images/mshop-icon.png',
				'query_var'          => true,
				'capability_type'    => 'post',
				'map_meta_cap'       => true,
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => null,
				'supports'           => array ( 'title' )
			);

			register_post_type( 'msex_user', $args );
		}
	}
}

MSEX_Post_types::init();
