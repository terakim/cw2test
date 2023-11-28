<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MSM_OAuth_Kakao' ) ) {
	include_once( MSM()->plugin_path() . '/includes/abstract/abstract-msm-oauth.php' );

	class MSM_OAuth_Kakao extends MSM_OAuth {

		protected $terms_url = "https://kapi.kakao.com/v1/user/service/terms?extra=app_service_terms";
		protected $shipping_addresses_url = "https://kapi.kakao.com/v1/user/shipping_address";

		public function __construct() {
			$this->provider_id    = 'kakao';
			$this->provider_name  = 'Kakao';
			$this->provider_title = __( '카카오 ( Kakao )', 'mshop-members-s2' );

			$this->profile_url   = "https://kapi.kakao.com/v2/user/me";
			$this->authorize_url = "https://kauth.kakao.com/oauth/authorize";
			$this->token_url     = "https://kauth.kakao.com/oauth/token";
			$this->logout_url    = "https://kauth.kakao.com/oauth/logout";
			$this->unlink_url    = "https://kapi.kakao.com/v1/user/unlink";

			$scopes      = get_option( 'msm_oauth_kakao_scope', 'profile' );
			$this->scope = str_replace( ',', ' ', $scopes );

			add_filter( 'msm_oauth_access_token_args_' . $this->get_id(), array( $this, 'filter_access_token_params' ), 10, 2 );
			if ( 'yes' == get_option( 'msm_oauth_kakao_sync', 'no' ) ) {
				add_filter( 'msm_after_social_register_' . $this->get_id(), array( $this, 'process_kakao_sync' ), 10, 4 );
				add_filter( 'msm_after_social_login_' . $this->get_id(), array( $this, 'maybe_update_kakao_sync' ), 10, 3 );
			}

			add_filter( 'msm_bouncer_page_kakao', array( $this, 'maybe_change_bouncer_page' ), 10, 2 );

			$this->supports[] = 'logout';
			$this->supports[] = 'unlink';
		}
		public function filter_access_token_params( $params, $profiver ) {
			return http_build_query( $params );
		}

		public function get_user_data( $profile ) {
			$properties    = msm_get( $profile, 'properties' );
			$kakao_account = msm_get( $profile, 'kakao_account' );
			$user_data     = array( 'user_login' => 'kakao_' . $this->get_oauth_id( $profile ) );

			if ( $properties ) {
				$user_data = array(
					'user_login'      => 'kakao_' . $this->get_oauth_id( $profile ),
					'first_name'      => $properties['nickname'],
					'display_name'    => $properties['nickname'],
					'user_nicename'   => $properties['nickname'],
					'profile_image'   => $properties['profile_image'],
					'thumbnail_image' => $properties['thumbnail_image']
				);
			}

			if ( empty( $kakao_account ) ) {
				return $user_data;
			}

			if ( ! empty( msm_get( $kakao_account['profile'], 'nickname' ) ) && ! msm_get( $kakao_account, 'profile_nickname_needs_agreement', false ) ) {
				$user_data['first_name']    = msm_get( $kakao_account['profile'], 'nickname' );
				$user_data['display_name']  = msm_get( $kakao_account['profile'], 'nickname' );
				$user_data['user_nicename'] = msm_get( $kakao_account['profile'], 'nickname' );
			}

			if ( ! empty( msm_get( $kakao_account['profile_image'], 'nickname' ) ) && ! msm_get( $kakao_account, 'profile_image_needs_agreement', false ) ) {
				$user_data['profile_image']   = msm_get( $kakao_account['profile_image'], 'nickname' );
				$user_data['thumbnail_image'] = msm_get( $kakao_account['thumbnail_image'], 'nickname' );
			}

			if ( ! empty( msm_get( $kakao_account, 'name' ) ) && ! msm_get( $kakao_account, 'name_needs_agreement', false ) ) {
				$user_data['first_name'] = msm_get( $kakao_account, 'name' );
			}

			if ( ! empty( msm_get( $kakao_account, 'email' ) ) && msm_get( $kakao_account, 'has_email', false ) ) {
				$user_data['email'] = msm_get( $kakao_account, 'email' );
			}

            $phone_number = msm_get( $kakao_account, 'phone_number' );

			if ( ! empty( $phone_number ) && msm_get( $kakao_account, 'has_phone_number', false ) ) {
                $phone_number = preg_replace( '~\D~', '', $phone_number );
                $phone_number = preg_replace( '/^821|^8201/', '01', $phone_number );
                $phone_number = preg_replace( '/([0-9]{3})([0-9]{4})([0-9]{4})/', '$1-$2-$3', $phone_number );

				$user_data['billing_phone'] = $phone_number;
			}

			if ( ! empty( msm_get( $kakao_account, 'gender' ) ) && msm_get( $kakao_account, 'has_gender', false ) ) {
				$user_data['gender'] = msm_get( $kakao_account, 'gender' );
			}

			if ( ! empty( msm_get( $kakao_account, 'age_range' ) ) && msm_get( $kakao_account, 'has_age_range', false ) ) {
				$user_data['age_range'] = msm_get( $kakao_account, 'age_range' );
			}

			if ( ! empty( msm_get( $kakao_account, 'birthyear' ) ) && msm_get( $kakao_account, 'has_birthyear', false ) ) {
				$user_data['birthyear'] = msm_get( $kakao_account, 'birthyear' );
			}

			if ( ! empty( msm_get( $kakao_account, 'birthday' ) ) && msm_get( $kakao_account, 'has_birthday', false ) ) {
				$user_data['birthday'] = msm_get( $kakao_account, 'birthday' );
			}

			if ( ! empty( $user_data['birthyear'] ) && ! empty( $user_data['birthday'] ) ) {
				$month                 = substr( $user_data['birthday'], 0, 2 );
				$day                   = substr( $user_data['birthday'], 2, 2 );
				$user_data['birthday'] = $user_data['birthyear'] . '-' . $month . '-' . $day;
			}

			if ( ! empty( msm_get( $kakao_account, 'ci' ) ) && ! msm_get( $kakao_account, 'ci_needs_agreement', false ) ) {
				$user = get_users(
					array(
						'meta_key'    => 'mshop_auth_dupinfo',
						'meta_value'  => msm_get( $kakao_account, 'ci' ),
						'number'      => 1,
						'count_total' => false
					)
				);

				if ( count( $user ) > 0 ) {
					throw new Exception( sprintf( __( '이미 가입된 사용자(%s)입니다. 로그인 후 소셜 계정 연동을 진행해주세요', 'mshop-members-s2' ), $user[0]->data->user_login ) );
				}
			}

			return $user_data;
		}
		protected function get_terms( $auth_token ) {
			$headers = array(
				'Authorization: Bearer ' . $auth_token['access_token']
			);

			return $this->call( $this->terms_url, array(), $headers );
		}
		protected function get_shipping_addresses( $auth_token ) {
			$headers = array(
				'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
				'Authorization: Bearer ' . $auth_token['access_token']
			);

			return $this->call( $this->shipping_addresses_url, array(), $headers );
		}

		public function update_address( $user_id, $load_address, $shipping_address ) {
			update_user_meta( $user_id, $load_address . '_country', 'KR' );
			update_user_meta( $user_id, $load_address . '_address_1', $shipping_address['base_address'] );
			update_user_meta( $user_id, $load_address . '_address_2', $shipping_address['detail_address'] );
			update_user_meta( $user_id, $load_address . '_postcode', $shipping_address['zone_number'] );
			update_user_meta( $user_id, $load_address . '_first_name', $shipping_address['receiver_name'] );
			update_user_meta( $user_id, $load_address . '_phone', $shipping_address['receiver_phone_number1'] );
			update_user_meta( $user_id, $load_address . '_email', $shipping_address['email'] );
		}

		public function update_mshop_address( $user_id, $load_address, $shipping_address ) {
			if ( $load_address == 'billing' ) {
				update_user_meta( $user_id, $load_address . '_phone_kr', $shipping_address['receiver_phone_number1'] );
				update_user_meta( $user_id, $load_address . '_email_kr', $shipping_address['email'] );
			}
			update_user_meta( $user_id, $load_address . '_first_name_kr', $shipping_address['receiver_name'] );
			update_user_meta( $user_id, 'mshop_' . $load_address . '_address-postnum', $shipping_address['zone_number'] );
			update_user_meta( $user_id, 'mshop_' . $load_address . '_address-addr1', $shipping_address['base_address'] );
			update_user_meta( $user_id, 'mshop_' . $load_address . '_address-addr2', $shipping_address['detail_address'] );

			if ( msaddr_enabled() && MSADDR_Address_Book::is_enabled() && $load_address == 'shipping' ) {
				$msaddr_array = array(
					'shipping_country'               => 'KR',
					'shipping_first_name'            => $shipping_address['receiver_name'],
					'shipping_first_name_kr'         => $shipping_address['receiver_name'],
					'shipping_address_1'             => $shipping_address['base_address'],
					'shipping_address_2'             => $shipping_address['detail_address'],
					'mshop_shipping_address-addr1'   => $shipping_address['base_address'],
					'mshop_shipping_address-addr2'   => $shipping_address['detail_address'],
					'shipping_postcode'              => $shipping_address['zone_number'],
					'mshop_shipping_address-postnum' => $shipping_address['zone_number'],
					'shipping_phone'                 => $shipping_address['receiver_phone_number1'],
					'shipping_email'                 => $shipping_address['email']
				);

				$options        = get_user_meta( $user_id, '_msaddr_shipping_history', true );
				$shipping_infos = array();
				$msaddr_key     = array_keys( $msaddr_array );

				foreach ( MSADDR_Address_Book::get_shipping_fields() as $field ) {
					if ( in_array( $field, $msaddr_key ) ) {
						$shipping_infos[ $field ] = $msaddr_array[ $field ];
					}
				}
				if ( empty( $options ) ) {
					$options = array();
				}

				$key = md5( json_encode( $shipping_infos ) );

				if ( ! isset( $options[ $key ] ) ) {
					$options = array_merge(
						array( $key => $shipping_infos ),
						array_slice( $options, 0, ( get_option( 'mshop_address_shipping_adress_book_count', '3' ) - 1 ), true )
					);
					update_user_meta( $user_id, '_msaddr_shipping_history', $options );
				}
			}
		}
		function maybe_update_kakao_sync( $user, $profile, $auth_token ) {
			if ( 'yes' != get_user_meta( $user->ID, 'msm_process_kakao_sync', true ) ) {
				update_user_meta( $user->ID, 'msm_process_kakao_sync', 'yes' );
				$this->process_kakao_sync( $user->ID, array(), $profile, $auth_token );
			}
		}
		function process_kakao_sync( $user_id, $customer_data, $profile, $auth_token ) {
			update_user_meta( $user_id, 'msm_process_kakao_sync', 'yes' );

			$terms       = $this->get_terms( $auth_token );
			$agree_terms = array_column( $terms['allowed_service_terms'], 'tag' );
			foreach ( $terms['app_service_terms'] as $term ) {
				if ( in_array( $term['tag'], $agree_terms ) ) {
					$term_value = '_mshop_acceptance_of_terms' == $term['tag'] ? 'yes' : 'on';
					update_user_meta( $user_id, $term['tag'], $term_value );
					update_user_meta( $user_id, $term['tag'] . '_label', 'YES' );
				} else {
					update_user_meta( $user_id, $term['tag'], '' );
					update_user_meta( $user_id, $term['tag'] . '_label', 'NO' );
				}
			}

			if ( strpos( $this->scope, 'shipping_address' ) ) {
				$shipping_addresses = $this->get_shipping_addresses( $auth_token );
				if ( ! empty( $shipping_addresses ) && msm_get( $shipping_addresses, 'has_shipping_addresses', false ) ) {
					update_user_meta( $user_id, 'kakao_shipping_addresses', $shipping_addresses );
					$user_data = self::get_user_data( $profile );

					foreach ( $shipping_addresses['shipping_addresses'] as $shipping_address ) {
						$shipping_address['email'] = $user_data['email'];

						if ( $shipping_address['is_default'] ) {
							$this->update_address( $user_id, 'billing', $shipping_address );
							if ( function_exists( 'msaddr_enabled' ) && msaddr_enabled() ) {
								$this->update_mshop_address( $user_id, 'billing', $shipping_address );
							}
						} else {
							$this->update_address( $user_id, 'shipping', $shipping_address );
							if ( function_exists( 'msaddr_enabled' ) && msaddr_enabled() ) {
								$this->update_mshop_address( $user_id, 'shipping', $shipping_address );
							}
						}
					}
				}
			}
		}

		function maybe_change_bouncer_page( $page, $provider ) {
			if ( 'yes' == get_option( 'msm_oauth_kakao_sync', 'no' ) ) {
				$sync_page = get_option( 'msm_bouncer_page_kakao' );

				if ( is_array( $sync_page ) ) {
					$page = current( array_keys( $sync_page ) );
				}
			}

			return $page;
		}
	}
}