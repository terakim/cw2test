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
}

if ( ! class_exists( 'MSM_Security' ) ) {

	class MSM_Security {

		protected static $maybe_postpone_verify_recaptcha = true;

		public static function init() {
			add_action( 'msm_before_submit_form', array( __CLASS__, 'verify_recaptcha' ), 10, 2 );
			add_action( 'msm_before_submit_form', array( __CLASS__, 'verify_phone_number' ), 10, 2 );
			add_action( 'msm_before_submit_form', array( __CLASS__, 'verify_email_verification' ), 10, 2 );

			add_action( 'template_redirect', array( __CLASS__, 'maybe_check_phone_number_authentication_is_needed' ) );
			add_filter( 'comment_author', array( __CLASS__, 'maybe_hide_comment_author' ), 999 );
			add_filter( 'get_comment_author', array( __CLASS__, 'maybe_hide_comment_author' ), 999 );
		}
		public static function verify_phone_number( $params, $form ) {
			$phone_field = $form->get_field( array( 'MFD_Phone_Field' ) );

			if ( ! empty( $phone_field ) ) {
				$phone_field = reset( $phone_field );

				$require_certification = 'yes' == mfd_get( $phone_field->property, 'certification' );

				if ( $require_certification ) {
					$field_name = mfd_get( $phone_field->property, 'name' );

					if ( empty( $params[ $field_name ] ) || empty( $params[ $field_name . '_certification_number' ] ) ) {
						throw new Exception( __( '휴대폰 인증 정보가 올바르지 않습니다.', 'mshop-members-s2' ) );
					}

					$phone_number = preg_replace( '~\D~', '', $params[ $field_name ] );
					$saved_hash   = get_transient( 'msm_phone_certification_' . $phone_number );
					$saved_salt   = get_transient( 'msm_phone_certification_salt_' . $phone_number );
					$retry_count  = intval( get_transient( 'msm_phone_certification_retry_' . $phone_number ) );

					if ( $retry_count > 5 ) {
						throw new Exception( __( '인증 가능 횟수가 초과되었습니다.', 'mshop-members-s2' ), '2001' );
					}

					$success = ! empty( $saved_hash ) && $saved_hash === MSM_Phone_Certification::get_certification_hash( $params[ $field_name . '_certification_number' ], $saved_salt );

					if ( ! apply_filters( 'msm_verify_phone_number', $success, $params, $phone_field, $form ) ) {
						throw new Exception( __( '휴대폰 인증 정보가 올바르지 않습니다.', 'mshop-members-s2' ) );
					}
				}
			}
		}
		public static function verify_email_verification( $params, $form ) {
			$input_fields = $form->get_field( array( 'MFD_Input_Field' ) );

			if ( ! empty( $input_fields ) ) {
				foreach ( $input_fields as $input_field ) {

					$require_verification = 'yes' == mfd_get( $input_field->property, 'emailVerification' );

					if ( $require_verification ) {
						$field_name = mfd_get( $input_field->property, 'name' );

						if ( empty( $params[ $field_name ] ) || empty( $params[ $field_name . '_certification_number' ] ) ) {
							throw new Exception( __( '에메일 인증 정보가 올바르지 않습니다.', 'mshop-members-s2' ) );
						}

						$user_email = $params[ $field_name ];

						$saved_hash  = get_transient( 'msm_email_verification_' . $user_email );
						$saved_salt  = get_transient( 'msm_email_verification_salt_' . $user_email );
						$retry_count = intval( get_transient( 'msm_email_verification_retry_' . $user_email ) );

						if ( $retry_count > 5 ) {
							throw new Exception( __( '인증 가능 횟수가 초과되었습니다.', 'mshop-members-s2' ), '2001' );
						}

						$success = ! empty( $saved_hash ) && $saved_hash === MSM_Email_Authenticate::get_certification_hash( $params[ $field_name . '_certification_number' ], $saved_salt );

						if ( ! apply_filters( 'msm_verify_email_address', $success, $params, $input_field, $form ) ) {
							throw new Exception( __( '이메일 인증 정보가 올바르지 않습니다.', 'mshop-members-s2' ) );
						}
					}
				}
			}
		}
		public static function verify_recaptcha( $params, $form ) {
			if ( self::$maybe_postpone_verify_recaptcha && in_array( $form->get_submit_action(), apply_filters( 'msm_postpone_verify_recaptcha_actions', array( 'msm_action_login', 'msm_action_register', 'msm_action_lost_passwords' ) ) ) ) {
				self::$maybe_postpone_verify_recaptcha = false;
				add_action( 'msm_maybe_verify_recaptcha', array( __CLASS__, 'verify_recaptcha' ), 10, 2 );

				return;
			}

			$recaptcha = $form->get_field( array( 'MFD_Recaptcha_Field' ) );

			if ( ! empty( $recaptcha ) ) {

				$recaptcha = reset( $recaptcha );

				if ( empty( $params['g-recaptcha-response'] ) ) {
					throw new Exception( __( '잘못된 요청입니다', 'mshop-members-s2' ) );
				}

				$response = wp_remote_post(
					'https://www.google.com/recaptcha/api/siteverify',
					array(
						'method'      => 'POST',
						'timeout'     => 3,
						'redirection' => 5,
						'httpversion' => '1.0',
						'blocking'    => true,
						'body'        => array(
							'secret'   => $recaptcha->secret_key,
							'response' => $params['g-recaptcha-response'],
							'remoteip' => $_SERVER['REMOTE_ADDR']
						)
					)
				);

				$result = json_decode( $response['body'], true );

				if ( ! $result['success'] ) {
					$error_message = apply_filters( 'msm_recaptcha_error_message', implode( ',', $result['error-codes'] ) );

					throw new Exception( $error_message );
				}
			}

		}
		public static function get_block_exception_list() {
			$block_exception_lists = get_option( 'msm_exception_list', '' );
			$result_array          = array();

			if ( ! empty( $block_exception_lists ) ) {
				foreach ( $block_exception_lists as $block_exception_list ) {
					if ( ! empty( $block_exception_list['url'] ) ) {
						$result_array[ $block_exception_list['url'] ] = $block_exception_list['enabled'];
					}
				}
			}

			return $result_array;
		}

		public static function validate_url() {
			$block_exception_list = self::get_block_exception_list();

			$url     = parse_url( home_url() );
			$request = $_SERVER['REQUEST_URI'];

			if ( isset( $url['path'] ) ) {
				$request = str_replace( rtrim( $url['path'], '/' ), '', $request );
			}

			if ( strpos( $request, '/terms' ) === 0 ) {
				return true;
			}

			if ( ! empty( $block_exception_list ) ) {
				foreach ( $block_exception_list as $path => $enabled ) {
					if ( strpos( $request, $path ) === 0 ) {
						if ( $enabled == 'yes' ) {
							return true;
						}
					}
				}
			}

			return false;
		}
		public static function maybe_check_phone_number_authentication_is_needed() {
			if ( ! msm_is_ajax() && is_user_logged_in() && ! current_user_can( 'manage_woocommerce' ) && 'yes' == get_option( 'mssms_use_phone_certification', 'no' ) && 'yes' == get_option( 'mssms_phone_certification_required', 'no' ) ) {
				if ( 'yes' == get_option( 'mssms_phone_certification_only_checkout', 'no' ) && ! apply_filters( 'msm_is_checkout', is_checkout() ) ) {
					return;
				}
				if ( 'yes' == get_option( 'msm_phone_certification_social_except', 'no' ) && ! empty( get_user_meta( get_current_user_id(), '_msm_oauth_registered_by', true ) ) ) {
					return;
				}
				if ( empty( get_user_meta( get_current_user_id(), 'mshop_auth_method', true ) ) ) {
					$redirect_url = str_replace( home_url(), '', get_permalink( get_option( 'mssms_phone_certification_page_id' ) ) );

					if ( ! empty( $redirect_url ) && ! apply_filters( 'msm_is_phone_certification_page', 0 !== strpos( $_SERVER['REQUEST_URI'], $redirect_url ), $_SERVER['REQUEST_URI'], $redirect_url ) ) {
						wp_safe_redirect( $redirect_url );
					}
				}
			}
		}
		public static function maybe_hide_comment_author( $author ) {
			$author_option = get_option( 'msm_security_author_display', 'no' );

			if ( 'no' != $author_option ) {
				$length = 4;
				if ( mb_strlen( $author ) != strlen( $author ) ) {
					$length = 2;
				}

				switch ( $author_option ) {
					case 'left':
						$author = "**" . mb_substr( $author, 2, $length );
						break;
					case 'right':
						$author = mb_substr( $author, 0, mb_strlen( $author ) - 2 ) . "**";
						break;
					case 'email':
						$author = "******" . mb_substr( $author, 6, $length );
						break;
					default:
						break;
				}

			}

			return $author;
		}
	}

	MSM_Security::init();
}