<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'MSM_OAuth' ) ) {

	abstract class MSM_OAuth {
		protected $profile_url = null;
		protected $authorize_url = null;
		protected $token_url = null;
		protected $logout_url = null;
		protected $unlink_url = null;
		protected $provider_id = '';
		protected $provider_name = '';
		protected $provider_title = '';
		protected $scope = 'profile';
		protected $supports = array();

		public function get_id() {
			return $this->provider_id;
		}

		public function get_name() {
			return $this->provider_name;
		}

		public function get_title() {
			return $this->provider_title;
		}

		public function get_logout_url() {
			return $this->logout_url;
		}

		public function get_unlink_url() {
			return $this->unlink_url;
		}

		public function get_image() {
			return plugins_url( 'assets/images/social/icon/' . $this->get_name() . '.png', MSM_PLUGIN_FILE );
		}
		protected function get_client_id() {
			return get_option( 'msm_oauth_' . $this->provider_id . '_client_id' );
		}
		protected function get_client_secret() {
			return get_option( 'msm_oauth_' . $this->provider_id . '_client_secret', '' );
		}
		public function get_redirect_uri() {
			return '/msm_' . $this->provider_id;
		}
		public function get_logout_redirect_uri() {
			return '/?msm-logout=yes';
		}

		public function enabled() {
			return apply_filters( 'msm_provider_enabled', get_option( 'msm_oauth_' . $this->provider_id . '_enabled', 'no' ), $this );
		}
		public function get_state() {
			return msm_get_state();
		}
		public function supports( $feature ) {
			return in_array( $feature, $this->supports );
		}
		public function is_connected() {
			$connected = false;

			if ( is_user_logged_in() ) {
				$connected = ! empty( get_user_meta( get_current_user_id(), '_msm_oauth_' . $this->get_id() . '_id', true ) );
			}

			return $connected;
		}
		public function get_login_url( $args = array() ) {
			$args = apply_filters( 'msm_oauth_login_args_' . $this->get_id(), array_merge( array(
				'client_id'     => $this->get_client_id(),
				'redirect_uri'  => home_url() . $this->get_redirect_uri(),
				'response_type' => 'code',
				'scope'         => $this->scope,
				'state'         => $this->get_state()
			), $args ), $this );

			return $this->authorize_url . '?' . http_build_query( $args );
		}
		public function validate( $params ) {
			return $params['state'] === $this->get_state();
		}
		public function call( $url, $args = array(), $headers = array() ) {
			$cl = curl_init();

			curl_setopt( $cl, CURLOPT_URL, $url );

			if ( ! empty( $args ) ) {
				curl_setopt( $cl, CURLOPT_POST, 1 );
				curl_setopt( $cl, CURLOPT_POSTFIELDS, $args );
			}

			if ( ! empty( $headers ) ) {
				curl_setopt( $cl, CURLOPT_HTTPHEADER, $headers );
			}
			curl_setopt( $cl, CURLOPT_FOLLOWLOCATION, true );
			curl_setopt( $cl, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $cl, CURLOPT_RETURNTRANSFER, true );

			$result    = curl_exec( $cl );
			$http_code = curl_getinfo( $cl, CURLINFO_HTTP_CODE );

			curl_close( $cl );

			if ( $http_code == 200 ) {
				return json_decode( $result, true );
			} else {
				throw new Exception( $result, $http_code );
			}
		}
		public function get_access_token( $args ) {
			$args = apply_filters( 'msm_oauth_access_token_args_' . $this->get_id(), array_merge( array(
				'client_id'     => $this->get_client_id(),
				'client_secret' => $this->get_client_secret(),
				'redirect_uri'  => home_url() . $this->get_redirect_uri(),
				'grant_type'    => 'authorization_code'
			), $args ), $this );

			return $this->call( $this->token_url, $args );
		}
		public function get_logout( $args = array() ) {
			$args = apply_filters( 'msm_logout_args_' . $this->get_id(), array_merge( array(
				'client_id'           => $this->get_client_id(),
				'logout_redirect_uri' => home_url() . $this->get_logout_redirect_uri()
			), $args ), $this );

			return $this->logout_url . '?' . http_build_query( $args );
		}
		public function unlink( $auth_token ) {
			$headers = array(
				'Content-Type: application/x-www-form-urlencoded',
				'Authorization: Bearer ' . $auth_token['access_token']
			);

			return $this->call( $this->unlink_url, array(), $headers );
		}
		public function refresh_access_token( $args ) {
			$args = apply_filters( 'msm_oauth_access_token_args_' . $this->get_id(), array_merge( array(
				'client_id'     => $this->get_client_id(),
				'client_secret' => $this->get_client_secret(),
				'redirect_uri'  => home_url() . $this->get_redirect_uri(),
				'grant_type'    => 'refresh_token'
			), $args ), $this );

			return $this->call( $this->token_url, $args );
		}
		public function get_profile( $auth_token ) {
			$headers = array(
				'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
				'Authorization: Bearer ' . $auth_token['access_token']
			);

			return $this->call( $this->profile_url, array(), $headers );
		}
		public function get_user( $profile ) {
			$oauth_id = $this->get_oauth_id( $profile );

			if ( ! empty( $oauth_id ) ) {
				$users = get_users(
					array(
						'meta_key'    => '_msm_oauth_' . $this->get_id() . '_id',
						'meta_value'  => $oauth_id,
						'number'      => 1,
						'count_total' => false
					)
				);

				return apply_filters( 'msm_oauth_get_user', ! empty( $users ) ? reset( $users ) : null, $profile, $this );
			}

			return null;
		}
		public function refresh_user_profile( $user_id = null, $refresh_access_token = false ) {
			$user_profile = null;

			if ( is_null( $user_id ) ) {
				$user_id = get_current_user_id();
			}

			try {
				if ( $refresh_access_token ) {
					$access_token = $this->refresh_user_access_token( $user_id );
				} else {
					$access_token = $this->get_user_access_token( $user_id );
				}

				if ( empty( $access_token ) ) {
					throw new Exception( 'Access token is null' );
				}

				$user_profile = self::get_profile( array(
					'access_token' => $access_token
				) );
			} catch ( Exception $e ) {
				if ( ! $refresh_access_token ) {
					return $this->refresh_user_profile( $user_id, true );
				} else {
					wp_logout();
					wp_set_current_user( null );
					?>
                    <script>
                        window.location.href = '<?php echo $this->get_login_url(); ?>';
                    </script>
					<?php
					wp_redirect( $this->get_login_url() );
					die();
				}
			}

			return $user_profile;
		}
		public function refresh_user_access_token( $user_id ) {
			$access_token = null;
			delete_user_meta( $user_id, '_msm_oauth_access_token' );

			$refresh_token = get_user_meta( $user_id, '_msm_oauth_refresh_token', true );

			if ( ! empty( $refresh_token ) ) {
				$auth_token = $this->refresh_access_token( array(
					'refresh_token' => $refresh_token,
				) );

				update_user_meta( $user_id, '_msm_oauth_access_token', $auth_token['access_token'] );
				update_user_meta( $user_id, '_msm_oauth_refresh_token', $auth_token['refresh_token'] );
				update_user_meta( $user_id, '_msm_oauth_expire_in', strtotime( '+ ' . $auth_token['expires_in'] . ' seconds' ) );

				$access_token = $auth_token['access_token'];
			}

			return $access_token;
		}
		public function get_user_access_token( $user_id = null ) {
			if ( is_null( $user_id ) ) {
				$user_id = get_current_user_id();
			}

			$access_token = get_user_meta( $user_id, '_msm_oauth_access_token', true );
			$expire_in    = get_user_meta( $user_id, '_msm_oauth_expire_in', true );

			if ( empty( $access_token ) || $expire_in < time() ) {
				$access_token = $this->refresh_user_access_token( $user_id );
			}

			return $access_token;
		}
		public function do_login( $user, $profile, $auth_token ) {
			do_action( 'msm_before_social_login_' . $this->get_id(), $user, $profile, $auth_token );

			$state = $this->get_state();

			update_user_meta( $user->ID, '_msm_oauth_access_token', $auth_token['access_token'] );
			update_user_meta( $user->ID, '_msm_oauth_refresh_token', $auth_token['refresh_token'] );
			update_user_meta( $user->ID, '_msm_oauth_expire_in', strtotime( '+ ' . $auth_token['expires_in'] . ' seconds' ) );
			clean_user_cache( $user->ID );
			wp_clear_auth_cookie();
			wp_set_current_user( $user->ID, $user->user_login );
			wp_set_auth_cookie( $user->ID, apply_filters( 'msm_social_login_remember_user', false, $this ), is_ssl() );
			update_user_caches( $user );

			do_action( 'wp_login', $user->user_login, $user );

			$this->update_user_data( $user, $profile );

			do_action( 'msm_after_social_login_' . $this->get_id(), $user, $profile, $auth_token );

			if ( ! defined( 'DOING_AJAX' ) ) {
				$redirect_url = get_transient( 'msm_oauth_redirect_url_' . $state );

				if ( ! empty( $redirect_url ) ) {
					delete_transient( 'msm_oauth_redirect_url_' . $state );
				} else if ( function_exists( 'wc_get_page_permalink' ) ) {
					$redirect_url = get_option( 'msm_oauth_redirect_url', wc_get_page_permalink( 'myaccount' ) );
				} else {
					$redirect_url = get_option( 'msm_oauth_redirect_url', home_url() );
				}

				wp_safe_redirect( apply_filters( 'msm_form_redirect_url', $redirect_url ) );
				die();
			}
		}
		function get_user_data( $profile ) {
			return array();
		}
		function get_oauth_id( $profile ) {
			return msm_get( $profile, 'id' );
		}

		function update_user_data( $user, $profile ) {
		}
		function get_social_login_params() {
			return $_GET;
		}
		public function do_register( $customer_data, $profile, $auth_token ) {
			if ( ! empty( $customer_data['password'] ) && ! empty( $customer_data['confirm_password'] ) ) {
				$user_password = $customer_data['password'];
			} else {
				$user_password = wp_generate_password();
			}

			if ( email_exists( $customer_data['email'] ) ) {
				throw new Exception( sprintf( __( '이미 가입된 사용자(%s)입니다. 로그인 후 소셜 계정 연동을 진행해주세요', 'mshop-members-s2' ), $customer_data['email'] ) );
			}

			$user_id = wp_create_user( $customer_data['user_login'], $user_password );

			if ( is_wp_error( $user_id ) ) {
				throw new Exception( sprintf( __( '사용자를 생성할 수 없습니다. [%s]', 'mshop-members-s2' ), $customer_data['user_login'] ) );
			}
			$user_data = array(
				'ID'            => $user_id,
				'display_name'  => $customer_data['display_name'],
				'first_name'    => $customer_data['first_name'],
				'user_nicename' => $customer_data['user_nicename']
			);

			wp_update_user( $user_data );
			if ( ! empty( $customer_data['email'] ) ) {
				$user_data = array(
					'ID'         => $user_id,
					'user_email' => $customer_data['email']
				);
				wp_update_user( $user_data );

				update_user_meta( $user_id, 'billing_email', $customer_data['email'] );
				update_user_meta( $user_id, 'billing_email_kr', $customer_data['email'] );
			}

			if ( ! empty( $customer_data['billing_phone'] ) ) {
				update_user_meta( $user_id, 'billing_phone', $customer_data['billing_phone'] );
				update_user_meta( $user_id, 'billing_phone_kr', $customer_data['billing_phone'] );
			}
			$reserved_key = apply_filters( 'msm_social_reserved_user_meta', array(
				'user_login',
				'first_name',
				'email',
				'display_name',
				'user_nicename',
				'password',
				'confirm_password'
			) );

			foreach ( $customer_data as $key => $value ) {
				if ( ! in_array( $key, $reserved_key ) ) {
					update_user_meta( $user_id, $key, $value );
				}
			}

			update_user_meta( $user_id, 'billing_first_name', $customer_data['first_name'] );
			update_user_meta( $user_id, 'billing_first_name_kr', $customer_data['first_name'] );
			update_user_meta( $user_id, '_msm_oauth_' . $this->get_id() . '_id', $this->get_oauth_id( $profile ) );
			update_user_meta( $user_id, '_msm_oauth_registered_by', $this->get_id() );

			if ( apply_filters( 'msm_hide_admin_bar_front', true ) ) {
				update_user_meta( $user_id, 'show_admin_bar_front', 'false' );
			}
			update_user_meta( $user_id, 'msm_email_certified', 'yes' );

			$new_customer_data = apply_filters( 'woocommerce_new_customer_data', array(
				'user_login' => $customer_data['user_login'],
				'user_pass'  => $user_password,
				'user_email' => $customer_data['email']
			) );

			do_action( 'woocommerce_created_customer', $user_id, $new_customer_data, false );

			do_action( 'msm_user_register', $user_id, $new_customer_data, $customer_data );

			do_action( 'msm_user_registered', $user_id );

			do_action( 'msm_after_social_register_' . $this->get_id(), $user_id, $customer_data, $profile, $auth_token );

			self::do_login( get_userdata( $user_id ), $profile, $auth_token );

			return $user_id;
		}

	}
}