<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'MSM_OAuth_Instagram' ) ) {
	include_once( MSM()->plugin_path() . '/includes/abstract/abstract-msm-oauth.php' );

	class MSM_OAuth_Instagram extends MSM_OAuth {

		public function __construct() {
			$this->provider_id    = 'instagram';
			$this->provider_name  = 'Instagram';
			$this->provider_title = __( '인스타그램 ( Instagram )', 'mshop-members-s2' );

			$this->scope = 'user_profile';

			$this->profile_url   = "https://graph.instagram.com/me?fields=id,username&access_token=";
			$this->authorize_url = "https://api.instagram.com/oauth/authorize";
			$this->token_url     = "https://api.instagram.com/oauth/access_token";

			add_filter( 'msm_oauth_login_args_' . $this->get_id(), array( $this, 'filter_login_params' ), 10, 2 );
			add_filter( 'msm_oauth_access_token_args_' . $this->get_id(), array( $this, 'filter_access_token_params' ), 10, 2 );
		}
		public function filter_login_params( $params, $provider ) {
			$params['app_id'] = $params['client_id'];
			unset( $params['client_id'] );

			return $params;
		}

		public function filter_access_token_params( $params, $profiver ) {
			$params['app_id']     = $params['client_id'];
			$params['app_secret'] = $params['client_secret'];
			unset( $params['client_id'] );
			unset( $params['client_secret'] );

			return $params;
		}


		public function get_profile( $auth_token ) {
			return $this->call( $this->profile_url . $auth_token['access_token'] );
		}
		public function get_user_data( $profile ) {
			$user_data = array(
				'user_login'    => 'instagram_' . $this->get_oauth_id( $profile ),
				'first_name'    => $profile['username'],
				'display_name'  => $profile['username'],
				'user_nicename' => $profile['username']
			);

			return $user_data;
		}
	}
}