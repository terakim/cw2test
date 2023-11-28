<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'MSM_Social_Login' ) ) {

	class MSM_Social_Login {
		protected static $providers = null;

		protected static $redirect_url = '';

		public static function init() {
			add_action( 'init', array( __CLASS__, 'social_disconnect' ), 10 );

			add_action( 'parse_request', array( __CLASS__, 'parse_request' ) );

			add_filter( 'logout_url', array( __CLASS__, 'maybe_change_logout_url' ) );
			add_filter( 'woocommerce_get_endpoint_url', array( __CLASS__, 'maybe_change_endpoint_url' ), 10, 4 );

			add_action( 'msm_before_user_unsubscribe', array( __CLASS__, 'maybe_unlink_social_channel' ), 10, 2 );

			add_action( 'before_output_form_msm_social', array( __CLASS__, 'attach_social_profile_filter' ) );
			add_action( 'msm_social_register', array( __CLASS__, 'process_social_register' ), 10, 2 );

			add_filter( 'msm_oauth_get_user', array( __CLASS__, 'search_wsl_user' ), 10, 3 );
		}
		public static function is_social_registered_user( $user_id ) {
			return ! empty( get_user_meta( $user_id, '_msm_oauth_registered_by', true ) );
		}
		public static function social_disconnect() {
			if ( empty( $_GET['action'] ) || 'msm_social_disconnect' != $_GET['action'] || ! wp_verify_nonce( $_GET['_wpnonce'], 'msm_social_disconnect' ) ) {
				return;
			}

			$provider = MSM_Social_Login::get_provider( $_GET['provider_id'] );

			if ( ! empty( $provider ) ) {
				delete_user_meta( get_current_user_id(), '_msm_oauth_' . $provider->get_id() . '_id' );
			}

			$redirect_url = home_url();

			if ( function_exists( 'wc_get_account_endpoint_url' ) ) {
				if ( MSM_Profile::hide_edit_account() ) {
					$redirect_url = wc_get_account_endpoint_url( 'msm-profile' );
				} else {
					$redirect_url = wc_get_account_endpoint_url( 'edit-account' );
				}
			}

			wp_safe_redirect( apply_filters( 'msm_social_disconnect_redirect_url', $redirect_url ) );

			die();
		}
		public static function search_wsl_user( $user, $profile, $provider ) {
			if ( empty( $user ) ) {
				global $wpdb;

				$profile_table = $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}wslusersprofiles'" ) === $wpdb->prefix . 'wslusersprofiles';

				if ( $profile_table ) {
					$provider_name = $provider->get_name();
					$oauth_id      = $provider->get_oauth_id( $profile );

					$user_id = $wpdb->get_var( "SELECT user_id FROM {$wpdb->prefix}wslusersprofiles WHERE provider='{$provider_name}' AND identifier='{$oauth_id}'" );

					if ( ! empty( $user_id ) ) {
						update_user_meta( $user_id, '_msm_oauth_' . $provider->get_id() . '_id', $oauth_id );
						update_user_meta( $user_id, '_msm_oauth_registered_by', $provider->get_id() );

						$user = get_userdata( $user_id );
					}
				}
			}

			return $user;
		}
		public static function attach_social_profile_filter( $form ) {
			add_filter( 'mfd_get_post_value', array( __CLASS__, 'maybe_set_social_profile' ), 10, 4 );
		}
		public static function maybe_set_social_profile( $value, $name, $post, $form ) {
			$params = json_decode( stripslashes( msm_get( $_COOKIE, 'wp_msm_oauth' ) ), true );

			if ( $params && ! empty( $params['user_data'] ) && ! empty( $params['user_data'][ $name ] ) ) {
				$value = $params['user_data'][ $name ];
			}

			return $value;
		}

		public static function clear_session() {
			$expire = time() + intval( 60 * 5 );
			setcookie( 'wp_msm_oauth', '', $expire, '/', COOKIE_DOMAIN );
		}
		public static function set_session( $value ) {
			self::clear_session();

			$expire = time() + intval( 60 * 5 );
			setcookie( 'wp_msm_oauth', $value, $expire, '/', COOKIE_DOMAIN );
		}
		public static function providers() {
			if ( is_null( self::$providers ) ) {
				include_once 'provider/class-msm-oauth-kakao.php';
				include_once 'provider/class-msm-oauth-naver.php';
				include_once 'provider/class-msm-oauth-line.php';
				include_once 'provider/class-msm-oauth-facebook.php';
				include_once 'provider/class-msm-oauth-google.php';
				include_once 'provider/class-msm-oauth-instagram.php';
				include_once 'provider/class-msm-oauth-apple.php';

				self::$providers = apply_filters( 'msm_oauth_providers', array(
					'kakao'     => new MSM_OAuth_Kakao(),
					'naver'     => new MSM_OAuth_Naver(),
					'line'      => new MSM_OAuth_Line(),
					'facebook'  => new MSM_OAuth_Facebook(),
					'google'    => new MSM_OAuth_Google(),
					'instagram' => new MSM_OAuth_Instagram(),
					'apple'     => new MSM_OAuth_Apple()
				) );
			}

			return self::$providers;
		}
		public static function enabled_providers() {
			$enabled_providers = array_filter( self::providers(), function ( $provider ) {
				return 'yes' == $provider->enabled();
			} );

			return $enabled_providers;
		}
		public static function connected_providers() {
			$connected_providers = array();
			$enabled_providers   = self::enabled_providers();

			if ( ! empty( $enabled_providers ) ) {
				$connected_providers = array_filter( self::enabled_providers(), function ( $provider ) {
					return $provider->is_connected();
				} );
			}

			return array_filter( $connected_providers );
		}
		public static function get_provider( $provider_id ) {
			return msm_get( self::enabled_providers(), $provider_id, null );
		}
		public static function get_login_url( $provider_id, $args = array() ) {
			$provider = self::get_provider( $provider_id );

			return $provider ? $provider->get_login_url( $args ) : '';
		}
		public static function get_matched_provider() {
			$request = $_SERVER['REQUEST_URI'];
			$url     = parse_url( home_url() );

			if ( isset( $url['path'] ) ) {
				$request = str_replace( $url['path'], '', $request );
			}

			foreach ( self::enabled_providers() as $provider ) {
				if ( 0 === strpos( $request, $provider->get_redirect_uri() ) ) {
					return $provider;
				}
			}

			return null;
		}
		public static function process_social_register( $params, $form ) {
			$session = json_decode( stripslashes( msm_get( $_COOKIE, 'wp_msm_oauth' ) ), true );

			if ( $session ) {
				if ( ! empty( $params['email'] ) ) {
					if ( ! is_email( $params['email'] ) ) {
						throw new Exception( '잘못된 이메일 형식입니다.' );
					}

					if ( email_exists( $params['email'] ) ) {
						throw new Exception( '이미 사용중인 이메일입니다.' );
					}
				}

				$provider_id = msm_get( $session, 'provider_id' );
				$profile     = msm_get( $session, 'profile' );
				$auth_token  = msm_get( $session, 'auth_token' );

				$provider  = self::get_provider( $provider_id );
				$user_data = apply_filters( 'msm_social_get_user_data', $provider->get_user_data( $profile ) );

				$user_data = array_merge( $user_data, $params );

				if ( $provider ) {
					$user_id = $provider->do_register( $user_data, $profile, $auth_token );

					MSM_Manager::add_post_processing_data( $form, $params );

					MSM_Meta::update_user_meta( $user_id, MSM_Manager::get_post_processing_data(), '_msm_register_fields', array(
						'except_fields' => array(
							'login',
							'user_login',
							'password',
							'confirm_password'
						)
					) );
				}

				self::$redirect_url = get_transient( 'msm_bouncer_redirect_url_' . $provider->get_state() );

				if ( ! empty( self::$redirect_url ) ) {
					delete_transient( 'msm_bouncer_redirect_url_' . $provider->get_state() );

					add_filter( 'msm_post_action_redirect', function ( $response, $form, $action, $params ) {
						$response['redirect_url'] = home_url( str_replace( home_url(), '', self::$redirect_url ) );

						return $response;
					}, 99, 4 );
				}
			}
		}
		public static function process_login( $provider, $auth_token, $profile ) {
			try {
				$user = $provider->get_user( $profile );

				if ( $user ) {
					if ( '1' == get_user_meta( $user->ID, 'is_unsubscribed', true ) ) {
						throw new Exception( __( '탈퇴한 사용자입니다.', 'mshop-members-s2' ) );
					}

					$provider->do_login( $user, $profile, $auth_token );
				} else {
					if ( 'yes' == get_option( 'msm_use_bouncer', 'no' ) ) {
						$params = array(
							'provider_id' => $provider->get_id(),
							'user_data'   => apply_filters( 'msm_social_get_user_data', $provider->get_user_data( $profile ) ),
							'profile'     => $profile,
							'auth_token'  => $auth_token
						);
						self::set_session( json_encode( $params ) );

						$page = get_option( 'msm_bouncer_page' );
						$redirect_url = get_transient( 'msm_oauth_redirect_url_' . msm_get_state() );
						if ( ! empty( $redirect_url ) ) {
							set_transient( 'msm_bouncer_redirect_url_' . msm_get_state(), $redirect_url, 3 * MINUTE_IN_SECONDS );
						}

						if ( is_array( $page ) ) {
							$page = apply_filters( 'msm_bouncer_page_' . $provider->get_id(), current( array_keys( $page ) ), $provider );

							if ( $page > 0 && 'page' == get_post_type( $page ) ) {
								wp_safe_redirect( get_permalink( $page ) );
								die();
							}
						}
					}

					$provider->do_register( apply_filters( 'msm_social_get_user_data', $provider->get_user_data( $profile ) ), $profile, $auth_token );
				}
			} catch ( Exception $e ) {
				set_transient( 'msm_oauth_error_' . $provider->get_state(), $e->getMessage(), 3 * MINUTE_IN_SECONDS );

				wp_safe_redirect( wp_login_url() );
				die();
			}
		}
		public static function process_connect( $provider, $auth_token, $profile ) {
			try {
				if ( ! empty( get_user_meta( get_current_user_id(), '_msm_oauth_' . $provider->get_id() . '_id', true ) ) ) {
					throw new Exception( sprintf( __( '이미 연결된 소셜 채널입니다. [%s]', 'mshop-members-s2' ), $provider->get_id() ) );
				}
				update_user_meta( get_current_user_id(), '_msm_oauth_' . $provider->get_id() . '_id', $provider->get_oauth_id( $profile ) );

				$redirect_url = home_url();

				if ( function_exists( 'wc_get_account_endpoint_url' ) ) {
					if ( MSM_Profile::hide_edit_account() ) {
						$redirect_url = wc_get_account_endpoint_url( 'msm-profile' );
					} else {
						$redirect_url = wc_get_account_endpoint_url( 'edit-account' );
					}
				}

				wp_safe_redirect( apply_filters( 'msm_social_connect_redirect_url', $redirect_url ) );

				die();
			} catch ( Exception $e ) {
				set_transient( 'msm_oauth_error_' . $provider->get_state(), $e->getMessage(), 3 * MINUTE_IN_SECONDS );

				wp_safe_redirect( wp_login_url() );
				die();
			}
		}
		public static function parse_request() {
			if ( 'yes' == msm_get( $_REQUEST, 'msm-logout' ) ) {
				wp_logout();
				wp_set_current_user( null );
				wp_redirect( home_url() );
				die();
			} else if ( ! empty( $_REQUEST['state'] ) ) {
				$provider = self::get_matched_provider();

				if ( $provider ) {
					$params = $provider->get_social_login_params();

					if ( ! empty( $params['code'] ) ) {
						if ( $provider->validate( $params ) ) {
							try {
								$code       = msm_get( $params, 'code' );
								$auth_token = $provider->get_access_token( array(
									'code' => $code
								) );

								if ( $auth_token && ! empty( $auth_token['access_token'] ) ) {
									$profile = $provider->get_profile( $auth_token );

									if ( ! is_user_logged_in() ) {
										self::process_login( $provider, $auth_token, $profile );
									} else {
										self::process_connect( $provider, $auth_token, $profile );
									}
								}
							} catch ( Exception $e ) {
								set_transient( 'msm_oauth_error_' . $provider->get_state(), $e->getMessage(), 3 * MINUTE_IN_SECONDS );

								wp_safe_redirect( wp_login_url() );
								die();
							}
						}
					} else {
						set_transient( 'msm_oauth_error_' . $provider->get_state(), msm_get( $params, 'error_description' ), 3 * MINUTE_IN_SECONDS );

						wp_safe_redirect( wp_login_url() );
						die();
					}
				}
			} else if ( ! empty( $_GET['msm-social-connect'] ) ) {
				$provider = MSM_Social_Login::get_provider( $_GET['msm-social-connect'] );

				if ( $provider ) {
					wp_redirect( $provider->get_login_url() );
					die();
				} else {
					wp_safe_redirect( home_url() );
					die();
				}
			}
		}
		public static function maybe_change_logout_url( $logout_url ) {
			foreach ( self::connected_providers() as $provider ) {
				if ( $provider->supports( 'logout' ) && 'yes' == get_option( 'msm_oauth_' . $provider->get_id() . '_logout_enabled', 'no' ) ) {
					$logout_url = $provider->get_logout();
					break;
				}
			}

			return $logout_url;
		}
		public static function maybe_change_endpoint_url( $url, $endpoint, $value, $permalink ) {
			if ( 'customer-logout' == $endpoint || 'logout' == $endpoint ) {
				$url = self::maybe_change_logout_url( $url );
			}

			return $url;
		}
		public static function maybe_unlink_social_channel( $user_id, $params ) {
			foreach ( self::connected_providers() as $provider ) {
				if ( $provider->supports( 'unlink' ) ) {
					$auth_token = array(
						'access_token' => get_user_meta( $user_id, '_msm_oauth_access_token', true ),
					);

					$provider->unlink( $auth_token );
				}
			}
		}
	}

	MSM_Social_Login::init();
}