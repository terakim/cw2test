<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class MSM_Post_Post_types {
	public static function init() {
        add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
	}
	public static function register_post_types() {
		if ( !post_type_exists('msm_post') ) {
			$labels = array(
					'name'               => _x( '엠샵 포스트', 'post type general name', 'mshop-members-s2' ),
					'singular_name'      => _x( '엠샵 포스트', 'post type singular name', 'mshop-members-s2' ),
					'menu_name'          => _x( '엠샵 포스트', 'admin menu', 'mshop-members-s2' ),
					'name_admin_bar'     => _x( '엠샵 포스트', 'add new on admin bar', 'mshop-members-s2' ),
					'all_items'          => __( '엠샵 포스트 목록', 'mshop-members-s2' ),
					'search_items'       => __( '엠샵 포스트 검색', 'mshop-members-s2' ),
					'not_found'          => __( '엠샵 포스트 항목이 없습니다.', 'mshop-members-s2' ),
					'not_found_in_trash' => __( '휴지통에 엠샵포스트 항목이 없습니다.', 'mshop-members-s2' )
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
					'capability_type'    => 'post',
					'capabilities'       => array(
							'create_posts' => false
					),
					'map_meta_cap'       => true,
					'has_archive'        => true,
					'hierarchical'       => false,
					'menu_position'      => null,
					'supports'           => array( 'title', 'editor', 'custom-fields' )
			);

			register_post_type( 'msm_post', $args );
		}

		if ( ! taxonomy_exists( 'msm_post_cat' ) ) {
			$labels = array(
				'name'              => _x( '카테고리', 'taxonomy general name', 'mshop-members-s2'  ),
				'singular_name'     => _x( '카테고리', 'taxonomy singular name', 'mshop-members-s2'  ),
				'search_items'      => __( '카테고리 검색', 'mshop-members-s2'  ),
				'all_items'         => __( '모든 엠샵포스트 카테고리', 'mshop-members-s2'  ),
				'parent_item'       => __( '상위 엠샵포스트 카테고리', 'mshop-members-s2'  ),
				'parent_item_colon' => __( '상위 엠샵포스트 카테고리:', 'mshop-members-s2'  ),
				'edit_item'         => __( '엠샵포스트 카테고리 편집', 'mshop-members-s2'  ),
				'update_item'       => __( '엠샵포스트 카테고리 수정', 'mshop-members-s2'  ),
				'add_new_item'      => __( '엠샵포스트 카테고리 추가' , 'mshop-members-s2' ),
				'new_item_name'     => __( '엠샵포스트 카테고리 이름', 'mshop-members-s2'  ),
				'menu_name'         => __( '엠샵포스트 카테고리', 'mshop-members-s2'  ),
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

			register_taxonomy( 'msm_post_cat', array( 'msm_post' ), $args );
		}
	}
}

MSM_Post_Post_types::init();
