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

if ( ! class_exists( 'MSM_Action_Unsubscribe' ) ) {

	class MSM_Action_Unsubscribe {

		public static function do_action( $params, $form ) {

			if ( MSM_Manager::use_unsubscribe() ) {

				require_once(ABSPATH.'wp-admin/includes/user.php');

				if ( is_super_admin() ) {
					throw new Exception( __( '관리자는 탈퇴 처리를 할 수 없습니다.관리자 페이지 회원 리스트 기능을 이용해주세요.', 'mshop-members-s2' ) );
				}
				if ( ! empty( $params['password'] ) && empty( get_user_meta( get_current_user_id(), '_msm_oauth_registered_by', true ) ) ) {
					$user = get_user_by( 'id', get_current_user_id() );
					if ( ! wp_check_password( $params['password'], $user->data->user_pass, $user->ID ) ) {
						throw new Exception( __( '비밀번호가 일치하지 않습니다.', 'mshop-members-s2' ) );
					}
				}

				//탈퇴 옵션에 따라 데이터 삭제 및 유지 여부에 따라 처리
				$process_type = get_option( 'mshop_members_unsubscribe_after_process', 'none' );

				$user_id = get_current_user_id();
				do_action( 'msm_before_user_unsubscribe', $user_id, $params );
				if ( class_exists( 'WC_Subscriptions_Manager' ) ) {
					WC_Subscriptions_Manager::cancel_users_subscriptions( $user_id );
				}

				if ( $process_type == 'delete' ) {
					wp_delete_user( $user_id );
				} else {
					update_user_meta( $user_id, 'is_unsubscribed', '1' );
					update_user_meta( $user_id, 'unsubscribed_time', current_time( 'mysql' ) );
				}

				do_action( 'msm_after_user_unsubscribe', $user_id, $params );

				wp_logout();

			}

		}
		public static function wp_authenticate_user( $user, $password ) {
			if ( MSM_Manager::use_unsubscribe() ) {
				if ( get_user_meta( $user->ID, 'is_unsubscribed', true ) == "1" ) {
					$error = new WP_Error();
					$error->add( 'unsubscribed_user', __( '<strong>ERROR</strong>: Unsubscribed User.', 'mshop-members-s2' ) );

					return $error;
				}
			}

			if ( MSM_Manager::use_sleep_account() ) {
				if ( get_user_meta( $user->ID, 'is_unsubscribed', true ) == "2" ) {
					$error = new WP_Error();
					$error->add( 'unsubscribed_sleep_user', __( '<strong>ERROR</strong>: Sleep User.', 'mshop-members-s2' ) );

					return $error;
				}
			}

			return $user;
		}

		public static function output_form() {
			if ( MSM_Manager::use_unsubscribe() ) {

				//관리자 권한인 경우 내 계정 탈퇴 버튼 노출 안함.
				if ( is_super_admin() ) {
					return;
				}
				$custom_css = get_option( 'mshop_members_unsubscribe_custom_css', '' );
				if ( ! empty( $custom_css ) ) {
					echo '<style type="text/css">' . $custom_css . '</style>';
				}
				$page = get_page_by_path( 'msm_unsubscribe' );
				if ( ! empty( $page ) ) {
					echo '<div class="mshop_members my_account_unsubscribe"><a class="button button-primary" href="' . get_permalink( $page->ID ) . '">' . __( get_option( 'mshop_members_unsubscribe_button_text', 'unsubscribe' ), 'mshop-members-s2' ) . '</a></div>';
				}
			}
		}
	}

}

