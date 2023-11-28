<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'MSM_OAuth_Google' ) ) {
	include_once( MSM()->plugin_path() . '/includes/abstract/abstract-msm-oauth.php' );

	class MSM_OAuth_Google extends MSM_OAuth {

		public function __construct() {
			$this->provider_id    = 'google';
			$this->provider_name  = 'Google';
			$this->provider_title = __( '구글 ( Google )', 'mshop-members-s2' );

			$this->scope = 'openid email profile';

			$this->profile_url   = "https://openidconnect.googleapis.com/v1/userinfo";
			$this->authorize_url = "https://accounts.google.com/o/oauth2/v2/auth";
			$this->token_url     = "https://oauth2.googleapis.com/token";

			add_filter( 'msm_oauth_access_token_args_' . $this->get_id(), array( $this, 'filter_access_token_params' ), 10, 2 );
		}
		public function filter_access_token_params( $params, $profiver ) {
			return http_build_query( $params );
		}
		function get_oauth_id( $profile ) {
			return msm_get( $profile, 'sub' );
		}

		public function get_user_data( $profile ) {
			$user_data = array(
				'user_login'    => 'google_' . $this->get_oauth_id( $profile ),
				'first_name'    => $profile['name'],
				'display_name'  => $profile['name'],
				'user_nicename' => $profile['name'],
				'email'         => $profile['email'],
				'profile_image' => $profile['picture'],
			);

			return $user_data;
		}
	}
}