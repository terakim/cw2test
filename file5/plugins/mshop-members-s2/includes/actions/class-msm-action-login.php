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

if ( ! class_exists( 'MSM_Action_Login' ) ) {

	class MSM_Action_Login {
		public static function url( $login_url = '', $redirect = '', $force_reauth = '' ) {
            if ( apply_filters( 'msm_use_login_url_filter', true ) ) {
                $login_url = site_url( '/login' );

                if ( ! empty( $redirect ) ) {
                    $login_url = add_query_arg( 'redirect_to', urlencode( $redirect ), $login_url );
                }

                if ( $force_reauth ) {
                    $login_url = add_query_arg( 'reauth', '1', $login_url );
                }
            }

			return $login_url;
		}
		public static function do_action( $params, $form ) {
			$credential = array(
				'user_login'    => $params['user_login'],
				'user_password' => $params['password'],
				'remember'      => isset( $params['remember'] ) ? true : null
			);

			if ( is_email( $params['user_login'] ) ) {
				$user = get_user_by( 'email', $params['user_login'] );

				if ( isset( $user->user_login ) ) {
					$credential['user_login'] = $user->user_login;
				} else {
					throw new Exception( __( '등록되지 않은 이메일이거나 비밀번호가 잘못되었습니다.', 'mshop-members-s2' ) );
				}

			} else {
				$credential['user_login'] = $params['user_login'];
			}

			$restrict_roles = explode( ',', get_option( 'mshop_members_restrict_login', '' ) );
			if ( ! empty( $restrict_roles ) ) {
				$user = get_user_by( 'login', $credential['user_login'] );
				if ( $user && array_intersect( $restrict_roles, $user->roles ) ) {
					throw new Exception( get_option( 'mshop_members_restrict_login_message', __( '등록되지 않은 이메일이거나 비밀번호가 잘못되었습니다.', 'mshop-members-s2' ) ) );
				}
			}

			$user = get_user_by( 'login', $credential['user_login'] );

			if ( ! $user || ! wp_check_password( $credential['user_password'], $user->data->user_pass, $user->ID ) ) {
				throw new Exception( __( '등록되지 않은 이메일이거나 비밀번호가 잘못되었습니다.', 'mshop-members-s2' ) );
			}

			do_action( 'msm_maybe_verify_recaptcha', $params, $form );

			$user = wp_signon( $credential, is_ssl() );

			if ( is_wp_error( $user ) ) {
				if ( ! empty( $user->errors['unsubscribed_user'] ) ) {
					throw new Exception( __( '탈퇴한 사용자입니다.', 'mshop-members-s2' ) );
				} else if ( ! empty( $user->errors['unsubscribed_sleep_user'] ) ) {
					throw new Exception( __( '휴면 사용자입니다. 비밀번호를 초기화 하여 이용해 주세요.', 'mshop-members-s2' ) );
				} else {
					$message = apply_filters( 'msm_login_error_message', __( '등록되지 않은 이메일이거나 비밀번호가 잘못되었습니다.', 'mshop-members-s2' ), $user );
					throw new Exception( apply_filters( 'login_errors', $message ) );
				}
			}
		}
		public static function comment_form_defaults( $defaults ) {
			$defaults['must_log_in'] = '<p class="must-log-in">' . sprintf( __( 'You must be <a href="%s">logged in</a> to post a comment.', 'mshop-members-s2' ), self::url() ) . '</p>';

			return $defaults;
		}
		public static function wp_login( $user_login, $user ) {
			if ( ! empty( $user ) ) {
				update_user_meta( $user->ID, 'last_login_time', current_time( 'mysql' ) );
			}
		}
		public static function woocommerce_customer_reset_password( $user ) {
			$status = get_user_meta( $user->ID, 'is_unsubscribed', true );
			if ( ! empty( $status ) && $status == '2' ) {
				update_user_meta( $user->ID, 'last_login_time', current_time( 'mysql' ) );
				delete_user_meta( $user->ID, 'mshop_members_sleep_warning_mail_sent' );
				delete_user_meta( $user->ID, 'is_unsubscribed' );
			}
		}

	}
}

