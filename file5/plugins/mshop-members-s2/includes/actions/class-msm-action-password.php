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

if ( ! class_exists( 'MSM_Action_Password' ) ) {

	class MSM_Action_Password {
		public static function lost_password_action( $params, $form ) {
			$result = false;

			$login = msm_get( $params, 'user_login' );

			$user_data = get_user_by( 'login', $login );

			if ( ! $user_data && is_email( $login ) && apply_filters( 'woocommerce_get_username_from_email', true ) ) {
				$user_data = get_user_by( 'email', $login );
			}

			if ( $user_data ) {
				if ( '1' == get_user_meta( $user_data->ID, 'is_unsubscribed', true ) ) {
					throw new Exception( __( '탈퇴한 사용자입니다.', 'mshop-members-s2' ) );
				}

				do_action( 'msm_maybe_verify_recaptcha', $params, $form );

				$_POST['user_login'] = msm_get( $params, 'user_login' );
				wc_clear_notices();
				$result = WC_Shortcode_My_Account::retrieve_password();
			}

			if ( $result ) {
				return true;
			} else {
				throw new Exception( __( '등록된 이메일이 아닙니다.', 'mshop-members-s2' ) );
			}
		}
		protected static function get_template_params( $temporary_password ) {
			return array(
				'쇼핑몰명'   => wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ),
				'임시비밀번호' => $temporary_password
			);
		}
		protected static function send_via_sms( $phone_number, $temporary_password ) {
			$message = get_option( 'msm_issue_temporary_password_sms_template' );

			$template_params = self::get_template_params( $temporary_password );

			$recipients = array(
				array(
					'receiver'        => $phone_number,
					'template_params' => $template_params
				)
			);

			MSSMS_SMS::send_sms( 'SMS', '', $message, $recipients, get_option( 'mssms_rep_send_no' ) );
		}
		protected static function send_via_alimtalk( $phone_number, $temporary_password ) {
			$recipients[] = $phone_number;

			$template_code = get_option( 'msm_issue_temporary_password_alimtalk_template' );

			if ( empty( $template_code ) ) {
				throw new Exception( __( '알림톡 템플릿 등록 후 이용해주세요', 'mshop-members-s2' ) );
			}

			$template = MSSMS_Kakao::get_template( $template_code );

			if ( empty( $template ) ) {
				throw new Exception( __( '템플릿이 존재하지 않습니다.', 'mshop-members-s2' ) );
			}

			$profile = MSSMS_Kakao::get_profile( $template['plus_id'] );

			if ( 'yes' == mssms_get( $profile, 'is_resend' ) ) {
				$resend_params = array(
					'isResend'     => 'true',
					'resendSendNo' => $profile['resend_send_no']
				);
			} else {
				$resend_params = array( 'isResend' => 'false' );
			}

			$template_params = self::get_template_params( $temporary_password );

			MSSMS_Kakao::send_alimtalk( $template_code, $recipients, $template_params, $resend_params );
		}
		public static function temporary_password_action( $params, $form ) {
			$matched_user = null;
			$phone_fields = $form->get_field( array( 'MFD_Phone_Field' ) );

			if ( 'yes' != get_option( 'msm_use_issue_temporary_password', 'no' ) ) {
				throw new Exception( __( '임시 비밀번호 발급 기능을 사용할 수 없습니다. 관리자에게 문의해주세요.', 'mshop-members-s2' ) );
			}

			if ( empty( $phone_fields ) ) {
				throw new Exception( __( '임시 비밀번호 찾기 기능은 휴대폰 인증 기능을 이용하셔야 합니다.', 'mshop-members-s2' ) );
			}

			$phone_field = current( $phone_fields );
			$field_name  = $phone_field->get_name();
			$user_login  = $params[ $field_name . '_user_login' ];

			$phone_number = apply_filters( 'msm_phone_number_to_certificate', $params[ $field_name ] );
			if ( 'yes' == get_option( 'mssms_phone_certification_required', 'no' ) ) {
				$users = MSM_Action_Find_Login::get_users( $phone_number, array( 'mshop_auth_phone' ) );
			}
			if ( empty( $users ) ) {
				$users = MSM_Action_Find_Login::get_users( $phone_number );
			}

			foreach ( $users as $user ) {
				if ( $user->user_login == $user_login || $user->user_email == $user_login ) {
					$matched_user = $user;
					break;
				}
			}

			if( empty( $matched_user ) ) {
				throw new Exception( __( '일치하는 회원 정보가 없습니다.', 'mshop-members-s2' ) );
			}

			$temporary_password = bin2hex( random_bytes( 4 ) );

			wp_update_user( array(
				'ID'        => $matched_user->ID,
				'user_pass' => $temporary_password
			) );

			if ( 'sms' == get_option( 'msm_issue_temporary_password_method', 'alimtalk' ) ) {
				self::send_via_sms( $phone_number, $temporary_password );
			} else {
				self::send_via_alimtalk( $phone_number, $temporary_password );
			}
		}

	}

}

