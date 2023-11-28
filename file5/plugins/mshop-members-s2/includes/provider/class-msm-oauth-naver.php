<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'MSM_OAuth_Naver' ) ) {
	include_once( MSM()->plugin_path() . '/includes/abstract/abstract-msm-oauth.php' );

	class MSM_OAuth_Naver extends MSM_OAuth {

		public function __construct() {
			$this->provider_id    = 'naver';
			$this->provider_name  = 'Naver';
			$this->provider_title = __( '네이버 ( Naver )', 'mshop-members-s2' );

			$this->profile_url   = "https://openapi.naver.com/v1/nid/me";
			$this->authorize_url = "https://nid.naver.com/oauth2.0/authorize";
			$this->token_url     = "https://nid.naver.com/oauth2.0/token";
		}
		function get_oauth_id( $profile ) {
			if ( '00' == $profile['resultcode'] ) {
				return $profile['response']['id'];
			} else {
				throw new Exception( $profile['message'] );
			}
		}
		public function get_user_data( $profile ) {
			if ( '00' == $profile['resultcode'] ) {
				$name     = msm_get( $profile['response'], 'name' );
				$nickname = msm_get( $profile['response'], 'nickname', $name );

				$user_data = array (
					'user_login'    => 'naver_' . $profile['response']['id'],
					'first_name'    => $name,
					'email'         => $profile['response']['email'],
					'display_name'  => $nickname,
					'user_nicename' => $nickname,
					'profile_image' => $profile['response']['profile_image'],
					'age'           => $profile['response']['age'],
					'gender'        => $profile['response']['gender'],
					'birthday'      => $profile['response']['birthday'],
					'billing_phone' => $profile['response']['mobile'],
					'birthyear'     => $profile['response']['birthyear'],
				);

				return $user_data;
			} else {
				throw new Exception( $profile['message'] );
			}
		}
	}
}