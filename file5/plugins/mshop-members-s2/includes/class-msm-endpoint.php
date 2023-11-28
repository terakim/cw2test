<?php

if ( ! defined( 'ABSPATH' ) ){
	exit;
}

class MSM_Endpoint {
	public static $endpoints = array();
	public function __construct() {
		self::$endpoints = array(
			'msm-unsubscribe' => __( '회원탈퇴', 'mshop-members-s2'),
			'msm-profile' => __( '프로필', 'mshop-members-s2')
		);

		// Actions used to insert a new endpoint in the WordPress.
		add_action( 'init', array( $this, 'add_endpoints' ) );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );

		// Change the My Accout page title.
		add_filter( 'the_title', array( $this, 'endpoint_title' ) );
	}
	public function add_endpoints() {
		foreach ( self::$endpoints as $endpoint => $label ) {
			add_rewrite_endpoint( $endpoint, EP_ROOT | EP_PAGES );
		}
	}
	public function add_query_vars( $vars ) {
		foreach ( self::$endpoints as $endpoint => $label ) {
			$vars[] = $endpoint;
		}

		return $vars;
	}
	public function endpoint_title( $title ) {
		global $wp_query;

		foreach ( self::$endpoints as $endpoint => $label ) {
			$is_endpoint = isset( $wp_query->query_vars[ $endpoint ] );

			if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
				// New page title.
				$title = $label;

				remove_filter( 'the_title', array( $this, 'endpoint_title' ) );
			}
		}

		return $title;
	}
	public static function install() {
		foreach ( self::$endpoints as $endpoint => $label ) {
			add_rewrite_endpoint( $endpoint, EP_ROOT | EP_PAGES );
		}

		flush_rewrite_rules();
	}
}

new MSM_Endpoint();
