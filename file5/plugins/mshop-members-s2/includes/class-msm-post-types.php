<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class MSM_Post_types {
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 5 );
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );

		if ( isset( $_GET['msm_download'] ) && isset( $_GET['meta_name'] ) && isset( $_GET['type'] ) && isset( $_GET['key'] ) ) {
			add_action( 'init', array( __CLASS__, 'download_file_from_form' ) );
		}
		if ( isset( $_GET['msm_file_download'] ) && isset( $_GET['meta_name'] ) && isset( $_GET['type'] ) && isset( $_GET['key'] ) ) {
			add_action( 'init', array( __CLASS__, 'download_file_from_field' ) );
		}
	}

	static function download_file_from_form() {
		$id        = $_GET['msm_download'];
		$type      = $_GET['type'];
		$key       = $_GET['key'];
		$meta_name = $_GET['meta_name'];

		if ( 'user' == $type || 'users' == $type ) {
			$infos = get_user_meta( $id, $meta_name, true );
		} else {
			$infos = get_post_meta( $id, $meta_name, true );
		}

		if ( ! empty( $infos ) && ! empty( $infos['args'] ) ) {
			$args = $infos['args'];

			if ( ! empty( $args['files'] ) && ! empty( $args['files'][ $key ] ) ) {
				$file = $args['files'][ $key ];

				do_action( 'woocommerce_download_file_force', $file['filename'], basename( $file['filename'] ) );
			}
		}
	}

	static function download_file_from_field() {
		$id        = $_GET['msm_file_download'];
		$type      = $_GET['type'];
		$key       = $_GET['key'];
		$meta_name = $_GET['meta_name'];

		if ( 'user' == $type || 'users' == $type ) {
			$files = get_user_meta( $id, $meta_name, true );

			if ( empty( $files ) && apply_filters( 'msm_find_file_info_from_registration_fields', false ) ) {
				$registration_fields = get_user_meta( $id, '_msm_register_fields', true );

				if ( isset( $registration_fields['args'] ) && isset( $registration_fields['args']['files'] ) ) {
					$files = $registration_fields['args']['files'];
				}

			}
		} else {
			$files = get_post_meta( $id, $meta_name, true );
		}

		if ( ! empty( $files ) && ! empty( $files[ $key ] ) ) {
			do_action( 'woocommerce_download_file_force', $files[ $key ]['filename'], basename( $files[ $key ]['filename'] ) );
		}
	}
	public static function register_taxonomies() {
		if ( ! taxonomy_exists( 'mshop_agreement_cat' ) ) {
			$labels = array(
				'name'              => _x( '이용약관 분류', 'taxonomy general name' ),
				'singular_name'     => _x( '이용약관 분류', 'taxonomy singular name' ),
				'search_items'      => __( '이용약관 분류 검색' ),
				'all_items'         => __( '모든 이용약관 분류' ),
				'parent_item'       => __( '상위 이용약관 분류' ),
				'parent_item_colon' => __( '상위 이용약관 분류:' ),
				'edit_item'         => __( '이용약관 분류 편집' ),
				'update_item'       => __( '이용약관 분류 수정' ),
				'add_new_item'      => __( '이용약관 분류 추가' ),
				'new_item_name'     => __( '이용약관 분류 이름' ),
				'menu_name'         => __( '이용약관 분류' ),
			);

			$args = array(
				'hierarchical'      => true,
				'public'            => false,
				'rewrite'           => false,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_in_menu'      => true,
				'show_admin_column' => true,
				'query_var'         => true
			);

			register_taxonomy( 'mshop_agreement_cat', array( 'mshop_agreement' ), $args );
		}

		if ( ! taxonomy_exists( 'mshop_members_form_cat' ) ) {
			$labels = array(
				'name'              => _x( '템플릿 카테고리', 'taxonomy general name' ),
				'singular_name'     => _x( '템플릿 카테고리', 'taxonomy singular name' ),
				'search_items'      => __( '템플릿 카테고리 검색' ),
				'all_items'         => __( '모든 템플릿 카테고리' ),
				'parent_item'       => __( '상위 템플릿 카테고리' ),
				'parent_item_colon' => __( '상위 템플릿 카테고리:' ),
				'edit_item'         => __( '템플릿 카테고리 편집' ),
				'update_item'       => __( '템플릿 카테고리 수정' ),
				'add_new_item'      => __( '템플릿 카테고리 추가' ),
				'new_item_name'     => __( '템플릿 카테고리 이름' ),
				'menu_name'         => __( '템플릿 카테고리' ),
			);

			$args = array(
				'hierarchical'      => true,
				'public'            => false,
				'rewrite'           => false,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_in_menu'      => true,
				'show_admin_column' => true,
				'query_var'         => true
			);

			register_taxonomy( 'mshop_members_form_cat', array( 'mshop_members_form' ), $args );
		}
	}
	public static function register_post_types() {
		if ( ! post_type_exists( 'mshop_members_form' ) ) {

			$permalinks        = get_option( 'woocommerce_permalinks' );
			$product_permalink = empty( $permalinks['product_base'] ) ? _x( 'product', 'slug', 'woocommerce' ) : $permalinks['product_base'];

			$labels = array(
				'name'               => _x( '엠샵 멤버스 템플릿', 'post type general name', 'mshop-members-s2' ),
				'singular_name'      => _x( '멤버스 템플릿', 'post type singular name', 'mshop-members-s2' ),
				'menu_name'          => _x( '엠샵 멤버스', 'admin menu', 'mshop-members-s2' ),
				'name_admin_bar'     => _x( '엠샵 멤버스 템플릿', 'add new on admin bar', 'mshop-members-s2' ),
				'add_new'            => _x( '멤버스 템플릿 추가', 'mshop_members_form', 'mshop-members-s2' ),
				'add_new_item'       => __( '멤버스 템플릿 추가', 'mshop-members-s2' ),
				'new_item'           => __( '신규 멤버스 템플릿', 'mshop-members-s2' ),
				'edit_item'          => __( '멤버스 템플릿 편집', 'mshop-members-s2' ),
				'view_item'          => __( '멤버스 템플릿 보기', 'mshop-members-s2' ),
				'all_items'          => __( '모든 멤버스 템플릿', 'mshop-members-s2' ),
				'search_items'       => __( '멤버스 템플릿 검색', 'mshop-members-s2' ),
				'parent_item_colon'  => __( '상위 멤버스 템플릿:', 'mshop-members-s2' ),
				'not_found'          => __( '멤버스 템플릿이 없습니다.', 'mshop-members-s2' ),
				'not_found_in_trash' => __( '휴지통에 멤버스 템플릿이 없습니다.', 'mshop-members-s2' )
			);

			$args = array(
				'labels'             => $labels,
				'description'        => __( 'Description.', 'mshop-members-s2' ),
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'menu_icon'          => MSM()->plugin_url() . '/assets/images/mshop-icon.png',
				'query_var'          => true,
				'rewrite'            => array( 'slug' => 'msm_forms', 'with_front' => false ),
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => null,
				'supports'           => array( 'title' )
			);

			register_post_type( 'mshop_members_form', $args );
		}

		if ( ! post_type_exists( 'mshop_agreement' ) ) {
			$labels = array(
				'name'               => _x( '엠샵 이용약관', 'post type general name', 'mshop-members-s2' ),
				'singular_name'      => _x( '이용약관', 'post type singular name', 'mshop-members-s2' ),
				'menu_name'          => _x( '엠샵 이용약관', 'admin menu', 'mshop-members-s2' ),
				'name_admin_bar'     => _x( '엠샵 이용약관', 'add new on admin bar', 'mshop-members-s2' ),
				'add_new'            => _x( '이용약관 추가', 'mshop_agreement', 'mshop-members-s2' ),
				'add_new_item'       => __( '이용약관 추가', 'mshop-members-s2' ),
				'new_item'           => __( '신규 이용약관', 'mshop-members-s2' ),
				'edit_item'          => __( '이용약관 수정', 'mshop-members-s2' ),
				'view_item'          => __( '이용약관 보기', 'mshop-members-s2' ),
				'all_items'          => __( '모든 이용약관', 'mshop-members-s2' ),
				'search_items'       => __( '이용약관 검색', 'mshop-members-s2' ),
				'parent_item_colon'  => __( '상위 이용약관:', 'mshop-members-s2' ),
				'not_found'          => __( '이용약관이 없습니다.', 'mshop-members-s2' ),
				'not_found_in_trash' => __( '휴지통에 이용약관이 없습니다.', 'mshop-members-s2' )
			);

			$args = array(
				'labels'             => $labels,
				'description'        => __( '설명.', 'mshop-members-s2' ),
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'menu_icon'          => MSM()->plugin_url() . '/assets/images/mshop-icon.png',
				'query_var'          => true,
				'rewrite'            => array( 'slug' => 'mshop_agreement' ),
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => null,
				'supports'           => array( 'title', 'editor' )
			);

			register_post_type( 'mshop_agreement', $args );
		}

		if ( ! post_type_exists( 'mshop_role_request' ) ) {
			$labels = array(
				'name'               => _x( '권한요청 목록', 'post type general name', 'mshop-members-s2' ),
				'singular_name'      => _x( '권한요청 목록', 'post type singular name', 'mshop-members-s2' ),
				'menu_name'          => _x( '권한요청 목록', 'admin menu', 'mshop-members-s2' ),
				'name_admin_bar'     => _x( '권한요청 목록', 'add new on admin bar', 'mshop-members-s2' ),
				'all_items'          => __( '권한요청 목록', 'mshop-members-s2' ),
				'search_items'       => __( '권한요청 검색', 'mshop-members-s2' ),
				'not_found'          => __( '권한요청 항목이 없습니다.', 'mshop-members-s2' ),
				'not_found_in_trash' => __( '휴지통에 권한요청 항목이 없습니다.', 'mshop-members-s2' )
			);

			$args = array(
				'labels'             => $labels,
				'description'        => __( 'Description.', 'mshop-members-s2' ),
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => false,
				'menu_icon'          => MSM()->plugin_url() . '/assets/images/mshop-icon.png',
				'query_var'          => true,
				'capability_type'    => 'post',
				'capabilities'       => array(
					'create_posts' => false
				),
				'map_meta_cap'       => true,
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => null,
				'supports'           => array( 'title', 'editor' )
			);

			register_post_type( 'mshop_role_request', $args );

			register_post_status( 'mshop-apply', array(
				'label'                     => _x( 'Apply', 'Role Application Status', 'mshop-members-s2' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Apply <span class="count">(%s)</span>', 'Apply <span class="count">(%s)</span>', 'mshop-members-s2' )
			) );
			register_post_status( 'mshop-approved', array(
				'label'                     => _x( 'Approved', 'Role Application Status', 'mshop-members-s2' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Approved <span class="count">(%s)</span>', 'Approved <span class="count">(%s)</span>', 'mshop-members-s2' )
			) );
			register_post_status( 'mshop-rejected', array(
				'label'                     => _x( 'Rejected', 'Role Application Status', 'mshop-members-s2' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Rejected <span class="count">(%s)</span>', 'Rejected <span class="count">(%s)</span>', 'mshop-members-s2' )
			) );
		}
	}


}

MSM_Post_types::init();
