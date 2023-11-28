<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'MSM_OAuth_Line' ) ) {
	include_once( MSM()->plugin_path() . '/includes/abstract/abstract-msm-oauth.php' );

	class MSM_OAuth_Line extends MSM_OAuth {

		public function __construct() {
			$this->provider_id    = 'line';
			$this->provider_name  = 'Line';
			$this->provider_title = __( '라인 ( Line )', 'mshop-members-s2' );

			$this->profile_url   = "https://api.line.me/v2/profile";
			$this->authorize_url = "https://access.line.me/oauth2/v2.1/authorize";
			$this->token_url     = "https://api.line.me/oauth2/v2.1/token";

			add_filter( 'msm_oauth_access_token_args_' . $this->get_id(), array( $this, 'filter_access_token_params' ), 10, 2 );
		}
		public function filter_access_token_params( $params, $profiver ) {
			return http_build_query( $params );
		}
		function get_oauth_id( $profile ) {
			return msm_get( $profile, 'userId' );
		}
		public function get_user_data( $profile ) {
			$user_data = array(
				'user_login'    => 'line_' . $this->get_oauth_id( $profile ),
				'first_name'    => $profile['displayName'],
				'display_name'  => $profile['displayName'],
				'user_nicename' => $profile['displayName'],
				'profile_image' => $profile['pictureUrl']
			);

			return $user_data;
		}
	}
}