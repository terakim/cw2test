<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( '\Firebase\JWT\JWT' ) ) {
    include_once( MSM()->plugin_path() . '/includes/lib/php-jwt/src/JWT.php' );
}

if ( ! class_exists( '\Firebase\JWT\JWK' ) ) {
    include_once( MSM()->plugin_path() . '/includes/lib/php-jwt/src/JWK.php' );
}

use Firebase\JWT\JWT;
use Firebase\JWT\JWK;

if ( ! class_exists( 'MSM_OAuth_Apple' ) ) {

	include_once( MSM()->plugin_path() . '/includes/abstract/abstract-msm-oauth.php' );

	class MSM_OAuth_Apple extends MSM_OAuth {

		protected $profile_data = array();

		public function __construct() {
			$this->provider_id    = 'apple';
			$this->provider_name  = 'Apple';
			$this->provider_title = __( '애플 ( Apple )', 'mshop-members-s2' );

			$this->authorize_url = "https://appleid.apple.com/auth/authorize";
			$this->token_url     = "https://appleid.apple.com/auth/token";
			$this->scope         = "name email";

			add_filter( 'msm_oauth_login_args_' . $this->get_id(), array( $this, 'filter_login_params' ), 10, 2 );
			add_filter( 'msm_oauth_access_token_args_' . $this->get_id(), array( $this, 'filter_access_token_params' ), 10, 2 );
		}
		public function get_social_login_params() {
			return $_POST;
		}
		public static function filter_login_params( $params, $provider ) {
			$params['response_type'] = 'code id_token';
			$params['scope']         = 'name email';
			$params['nonce']         = wp_create_nonce( 'mshop-members-s2' );
			$params['response_mode'] = 'form_post';
			unset( $params['nonce'] );

			return $params;
		}
		public function filter_access_token_params( $params, $profiver ) {
			$open_key = wp_remote_get( 'https://appleid.apple.com/auth/keys' );
			$keys     = json_decode( $open_key['body'], true );
			$jwk      = JWK::parseKeySet( $keys );
			$decoded  = JWT::decode( $_POST['id_token'], $jwk, array( 'RS256' ) );

			if ( ! empty( $decoded ) ) {

				$payload = array(
					"iss" => get_option( 'msm_oauth_apple_app_id' ),
					"iat" => time(),
					"exp" => time() + 86400,
					"aud" => "https://appleid.apple.com",
					"sub" => get_option( 'msm_oauth_apple_client_id' ),
				);

				$private_key = get_option( 'msm_oauth_apple_private_key' );

				$client_secret = JWT::encode( $payload, $private_key, 'ES256', get_option( 'msm_oauth_apple_key_id' ) );

				$params['client_secret'] = $client_secret;
			}

			return http_build_query( $params );

		}
		function get_oauth_id( $profile ) {
			return msm_get( $profile, 'sub' );
		}

		function get_profile( $auth_token ) {
			foreach ( explode( '.', $auth_token['id_token'] ) as $tokens ) {
				$decode_array[] = json_decode( base64_decode( str_replace( '_', '/', str_replace( '-', '+', $tokens ) ) ), true );
			}

			$user_data                 = $decode_array[1];
			$this->profile_data['sub'] = md5( $user_data['sub'] );

			$first_user_data            = json_decode( stripslashes( $_REQUEST['user'] ) );
			$this->profile_data['name'] = $first_user_data->name->lastName . $first_user_data->name->firstName;


			$this->profile_data['email'] = $user_data['email'];

			return $this->profile_data;
		}
		public function get_user_data( $profile ) {
			return array(
				'user_login'    => 'apple_' . $this->get_oauth_id( $profile ),
				'first_name'    => $profile['name'],
				'display_name'  => $profile['name'],
				'user_nicename' => $profile['name'],
				'billing_email' => $profile['email'],
				'email'         => $profile['email']
			);
		}
	}
}
