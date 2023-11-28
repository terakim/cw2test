<?php

/*
=====================================================================================
                ﻿엠샵 멤버스 / Copyright 2015 by CodeM(c)
=====================================================================================

  [ 우커머스 버전 지원 안내 ]

   워드프레스 버전 : WordPress 4.3

   우커머스 버전 : WooCommerce 2.4


  [ 코드엠 플러그인 라이센스 규정 ]

   (주)코드엠에서 개발된 워드프레스  플러그인을 사용하시는 분들에게는 다음 사항에 대한 동의가 있는 것으로 간주합니다.

   1. 코드엠에서 개발한 워드프레스 우커머스용 엠샵 멤버스 플러그인의 저작권은 (주)코드엠에게 있습니다.
   
   2. 플러그인은 사용권을 구매하는 것이며, 프로그램 저작권에 대한 구매가 아닙니다.

   3. 플러그인을 구입하여 다수의 사이트에 복사하여 사용할 수 없으며, 1개의 라이센스는 1개의 사이트에만 사용할 수 있습니다. 
      이를 위반 시 지적 재산권에 대한 손해 배상 의무를 갖습니다.

   4. 플러그인은 구입 후 1년간 업데이트를 지원합니다.

   5. 플러그인은 워드프레스, 테마, 플러그인과의 호환성에 대한 책임이 없습니다.

   6. 플러그인 설치 후 버전에 관련한 운용 및 관리의 책임은 사이트 당사자에게 있습니다.

   7. 다운로드한 플러그인은 환불되지 않습니다.

=====================================================================================
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'MSM_Action_Register' ) ) {

	class MSM_Action_Register {
		protected static $current_user_id = null;
		static function maybe_force_logout_restricted_user( $response, $form, $action, $params ) {
			if ( ! self::user_can_login() ) {
				wp_logout();
				wp_set_current_user( null );

				$redirect_url = get_option( 'mshop_members_restrict_login_redirect_url' );

				if ( ! empty( $redirect_url ) ) {
					$response['redirect_url'] = $redirect_url;
				}
			}

			return $response;
		}
		static function user_can_login() {
			$restrict_roles = explode( ',', get_option( 'mshop_members_restrict_login', '' ) );
			if ( ! empty( $restrict_roles ) ) {
				$user = get_user_by( 'id', self::$current_user_id );

				if ( $user && array_intersect( $restrict_roles, $user->roles ) ) {
					return false;
				}
			}

			return true;
		}
		public static function do_action( $params, $form ) {
			$params = apply_filters( 'msm_user_register_params', $params, $form );

			$user = array(
				'login'    => empty( $params['login'] ) ? '' : sanitize_text_field( urldecode( $params['login'] ) ),
				'email'    => empty( $params['user_login'] ) ? '' : sanitize_text_field( urldecode( $params['user_login'] ) ),
				'password' => empty( $params['password'] ) ? '' : sanitize_text_field( urldecode( $params['password'] ) ),
			);
			if ( ! empty( $params['login'] ) ) {
				if ( username_exists( $params['login'] ) ) {
					throw new Exception( __( '이미 사용중인 아이디입니다.', 'mshop-members-s2' ) );
				}

				if ( is_email( $params['login'] ) && empty( $user['email'] ) ) {
					$user['email'] = $params['login'];
				}

				if ( ! empty( $user['email'] ) && ( ! is_email( $user['email'] ) || email_exists( $user['email'] ) ) ) {
					throw new Exception( __( '이메일이 올바르지 않거나 이미 사용중입니다.', 'mshop-members-s2' ) );
				}
			}
			if ( empty( $params['login'] ) ) {
				if ( empty( $user['email'] ) || ! is_email( $user['email'] ) ) {
					throw new Exception( __( '이메일이 올바르지 않거나 이미 사용중입니다.', 'mshop-members-s2' ) );
				}
				if ( email_exists( $user['email'] ) ) {
					throw new Exception( __( '이메일이 올바르지 않거나 이미 사용중입니다.', 'mshop-members-s2' ) );
				}
			}

			$_POST = array_merge( $_POST, $params );

			$validation_error = new WP_Error();
			$validation_error = apply_filters( 'woocommerce_process_registration_errors', $validation_error, $user['login'], $user['password'], $user['email'] );

			if ( $validation_error->get_error_code() ) {
				if ( msm_is_ajax() ) {
					throw new Exception( $validation_error->get_error_message() );;
				} else {
					return $validation_error;
				}
			}

			if ( ! empty( $params['password'] ) && ! empty( $params['confirm_password'] ) && $params['password'] != '' && $params['confirm_password'] != '' && $params['password'] == $params['confirm_password'] ) {

				if ( empty( $user['login'] ) ) {
					$user['login'] = $user['email'];
				}

				do_action( 'msm_maybe_verify_recaptcha', $params, $form );

				$user_id = wp_create_user( $user['login'], $user['password'], $user['email'] );

				if ( ! is_wp_error( $user_id ) ) {
				    if ( apply_filters( 'msm_hide_admin_bar_front', true ) ) {
                        update_user_meta( $user_id, 'show_admin_bar_front', 'false' );
                    }

					self::$current_user_id = $user_id;
					add_filter( 'msm_post_action_redirect', array( __CLASS__, 'maybe_force_logout_restricted_user' ), 999, 4 );

					$wp_signon = wp_signon(
						array(
							'user_login'    => $user['login'],
							'user_password' => $user['password'],
							'remember'      => false
						), false );

					$new_customer_data = apply_filters( 'woocommerce_new_customer_data', array(
						'user_login' => $user['login'],
						'user_pass'  => $user['password'],
						'user_email' => $user['email']
					) );

					do_action( 'woocommerce_created_customer', $user_id, $new_customer_data, false );

					do_action( 'msm_user_register', $user_id, $new_customer_data, $params );

					MSM_Manager::add_post_processing_data( $form, $params );
					$user_fields = array(
						'display_name',
						'nickname',
						'first_name',
						'last_name',
						'user_nicename',
					);

					$user_data = array();

					foreach ( $params as $key => $value ) {
						if ( in_array( $key, $user_fields ) ) {
							$user_data[ $key ] = $value;

							if ( 'display_name' == $key ) {
								$user_data['nickname']   = $value;
								$user_data['first_name'] = $value;
								$user_data['last_name']  = '';
							}
						}
					}

					if ( ! empty( $user_data ) ) {
						$user_data['ID'] = $user_id;

						wp_update_user( $user_data );
					}

					MSM_Meta::update_user_meta( $user_id, MSM_Manager::get_post_processing_data(), '_msm_register_fields', array(
						'except_fields' => array(
							'login',
							'user_login',
							'password',
							'confirm_password'
						)
					) );

					do_action( 'msm_user_registered', $user_id );

					return true;
				} else {
					throw new Exception( __( '이메일이 올바르지 않거나 이미 사용중입니다.', 'mshop-members-s2' ) );
				}

			} else {
				throw new Exception( __( '비밀번호가 일치하지 않거나 입력되지 않았습니다.', 'mshop-members-s2' ) );
			}
		}

		public static function url() {
			return site_url( '/register' );
		}
		public static function set_user_role( $data ) {
			$data['role'] = get_option( 'default_role' );

			return $data;
		}
	}
}