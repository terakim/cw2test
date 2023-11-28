<?php

defined( 'ABSPATH' ) || exit;

function msex_update_1424_order_templates() {
	if ( MSEX_Install::needs_db_update() ) {
		$changed_keys = array(
			'billing_telephone'        => '',
			'product_namesku'          => 'product_sku',
			'shipping_phone1'          => 'shipping_phone',
			'shipping_phone2'          => '',
			'order_require_memo'       => 'order_note',
			'custom1'                  => 'order_meta',
			'custom2'                  => 'order_item_meta',
			'custom_product_meta'      => 'product_meta',
			'custom_subscription_meta' => 'subscription_meta',
			'customer_meta'            => 'user_meta',
		);

		$msex_fields = array();

		$old_fields = get_option( 'msex_order_field_params', MSEX_Fields::get_default_order_fields() );
		$old_fields = apply_filters( 'msex_order_field_params', $old_fields );

		foreach ( $old_fields as $field ) {
			$field_type = $field['field_type'];

			if ( array_key_exists( $field_type, $changed_keys ) ) {
				$field_type = msex_get( $changed_keys, $field_type );
				if ( empty( $field_type ) ) {
					continue;
				}
			}

			$msex_fields[] = array(
				'field_type'  => $field_type,
				'field_label' => $field['meta_label'],
				'meta_key'    => $field['meta_field'],
			);
		}

		$params = array(
			'_msex_fields'                 => $msex_fields,
			'_msex_download_type'          => get_option( 'order_download_type', 'excel' ),
			'_msex_slug'                   => 'default_order_template',
			'_msex_posts_per_page'         => '100',
			'_msex_export_all_order_items' => 'yes',
		);

		$page_data = array(
			'post_status'    => 'publish',
			'post_type'      => 'msex_order',
			'post_title'     => '[기본] 주문 다운로드 템플릿',
			'comment_status' => 'closed',
		);

		$post_id = wp_insert_post( $page_data );

		update_post_meta( $post_id, 'msex_fields', json_encode( $params ) );
		update_post_meta( $post_id, '_msex_fields', msex_get( $params, '_msex_fields' ) );
		update_post_meta( $post_id, '_msex_download_type', msex_get( $params, '_msex_download_type' ) );
		update_post_meta( $post_id, '_msex_slug', msex_get( $params, '_msex_slug' ) );
		update_post_meta( $post_id, '_msex_posts_per_page', msex_get( $params, '_msex_posts_per_page' ) );
		update_post_meta( $post_id, '_msex_export_all_order_items', msex_get( $params, '_msex_export_all_order_items' ) );
	}
}

function msex_update_1424_product_templates() {
	if ( MSEX_Install::needs_db_update() ) {
		$changed_keys = array(
			'categorie' => 'categories',
			'custom'    => 'product_meta'
		);

		$msex_fields = array();

		$old_fields = get_option( 'msex_product_field_params', MSEX_Fields::get_default_product_fields() );

		foreach ( $old_fields as $field ) {
			$field_type = $field['field_type'];

			if ( array_key_exists( $field_type, $changed_keys ) ) {
				$field_type = msex_get( $changed_keys, $field_type );
				if ( empty( $field_type ) ) {
					continue;
				}
			}

			$msex_fields[] = array(
				'field_type'  => $field_type,
				'field_label' => $field['meta_label'],
				'meta_key'    => $field['meta_field']
			);
		}

		$params = array(
			'_msex_fields'                 => $msex_fields,
			'_msex_download_type'          => get_option( 'product_download_type', 'excel' ),
			'_msex_slug'                   => 'default_product_template',
			'_msex_posts_per_page'         => '100',
			'_msex_export_all_order_items' => 'yes',
			'_msex_attributes_count'       => '3',
		);

		$page_data = array(
			'post_status'    => 'publish',
			'post_type'      => 'msex_product',
			'post_title'     => '[기본] 상품 다운로드 템플릿',
			'comment_status' => 'closed',
		);

		$post_id = wp_insert_post( $page_data );

		update_post_meta( $post_id, 'msex_fields', json_encode( $params ) );
		update_post_meta( $post_id, '_msex_fields', msex_get( $params, '_msex_fields' ) );
		update_post_meta( $post_id, '_msex_download_type', msex_get( $params, '_msex_download_type' ) );
		update_post_meta( $post_id, '_msex_slug', msex_get( $params, '_msex_slug' ) );
		update_post_meta( $post_id, '_msex_posts_per_page', msex_get( $params, '_msex_posts_per_page' ) );
		update_post_meta( $post_id, '_msex_export_all_order_items', msex_get( $params, '_msex_export_all_order_items' ) );
	}
}


function msex_update_1424_user_templates() {
	if ( MSEX_Install::needs_db_update() ) {
		$changed_keys = array(
			'local_time' => 'register_date',
			'custom'     => 'user_meta'
		);

		$msex_fields = array();

		$old_fields = get_option( 'msex_user_field_params', MSEX_Fields::get_default_user_fields() );

		foreach ( $old_fields as $field ) {
			$field_type = $field['field_type'];

			if ( array_key_exists( $field_type, $changed_keys ) ) {
				$field_type = msex_get( $changed_keys, $field_type );
				if ( empty( $field_type ) ) {
					continue;
				}
			}

			$msex_fields[] = array(
				'field_type'  => $field_type,
				'field_label' => $field['meta_label'],
				'meta_key'    => $field['meta_field']
			);
		}

		$params = array(
			'_msex_fields'                 => $msex_fields,
			'_msex_download_type'          => get_option( 'user_download_type', 'excel' ),
			'_msex_slug'                   => 'default_user_template',
			'_msex_posts_per_page'         => '100',
			'_msex_export_all_order_items' => 'yes',
			'_msex_attributes_count'       => '3',
		);

		$page_data = array(
			'post_status'    => 'publish',
			'post_type'      => 'msex_user',
			'post_title'     => '[기본] 사용자 다운로드 템플릿',
			'comment_status' => 'closed',
		);

		$post_id = wp_insert_post( $page_data );

		update_post_meta( $post_id, 'msex_fields', json_encode( $params ) );
		update_post_meta( $post_id, '_msex_fields', msex_get( $params, '_msex_fields' ) );
		update_post_meta( $post_id, '_msex_download_type', msex_get( $params, '_msex_download_type' ) );
		update_post_meta( $post_id, '_msex_slug', msex_get( $params, '_msex_slug' ) );
		update_post_meta( $post_id, '_msex_posts_per_page', msex_get( $params, '_msex_posts_per_page' ) );
	}
}

function msex_update_1424_db_version() {
	MSEX_Install::update_db_version( '1.4.24' );
}