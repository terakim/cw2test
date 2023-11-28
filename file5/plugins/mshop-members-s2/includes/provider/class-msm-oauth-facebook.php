<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'MSM_OAuth_Facebook' ) ) {
	include_once( MSM()->plugin_path() . '/includes/abstract/abstract-msm-oauth.php' );

	class MSM_OAuth_Facebook extends MSM_OAuth {

		public function __construct() {
			$this->provider_id    = 'facebook';
			$this->provider_name  = 'Facebook';
			$this->provider_title = __( '페이스북 ( Facebook )', 'mshop-members-s2' );

			$this->profile_url   = "https://graph.facebook.com/me?fields=id,name,picture,age_range,email&access_token=";
			$this->authorize_url = "https://www.facebook.com/v5.0/dialog/oauth";
			$this->token_url     = "https://graph.facebook.com/v5.0/oauth/access_token";

			add_filter( 'msm_oauth_login_args_' . $this->get_id(), array( $this, 'filter_login_params' ), 10, 2 );
		}
		public function filter_login_params( $params, $provider ) {
			unset( $params['response_type'] );
			unset( $params['scope'] );

			return $params;
		}

		public function get_profile( $auth_token ) {
			return $this->call( $this->profile_url . $auth_token['access_token'] );
		}
		public function get_user_data( $profile ) {
			$profile_image = '';

			if ( ! empty( $profile['picture'] ) ) {
				$profile_image = $profile['picture']['data']['url'];
			}

			$user_data = array(
				'user_login'    => 'facebook_' . $this->get_oauth_id( $profile ),
				'first_name'    => $profile['name'],
				'display_name'  => $profile['name'],
				'user_nicename' => $profile['name'],
				'email'         => msm_get( $profile, 'email' ),
				'profile_image' => $profile_image
			);

			return $user_data;
		}
	}
}