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

if ( ! class_exists( 'MSM_Action_Find_Login' ) ) {

	class MSM_Action_Find_Login {

		protected static function search_user( $value, $keys = array( 'billing_phone', 'billing_phone_kr' ) ) {
			$conditions = array( 'relation' => 'OR' );

			foreach ( $keys as $key ) {
				$conditions[] = array(
					'key'   => $key,
					'value' => $value
				);
			}

			$args = array(
				'meta_query' => $conditions
			);

			$user_query = new WP_User_Query( $args );

			return $user_query->get_results();
		}

		public static function get_users( $phone_number, $keys = array( 'billing_phone', 'billing_phone_kr' ) ) {
            $users = array_merge( self::search_user( $phone_number, $keys ), self::search_user( preg_replace( '~\D~', '', $phone_number ), $keys ) );

			if ( empty( $users ) ) {
				$users = self::search_user( preg_replace( '~\D~', '', $phone_number ), $keys );
			}

			return $users;
		}
		public static function get_users_by_auth_result( $phone_number, $user_ci ) {
			$users = self::search_user( $phone_number );

			if ( empty( $users ) ) {
				$phone_number = preg_replace( '/([0-9]{3})([0-9]{4})([0-9]{4})/', '$1-$2-$3', $phone_number );
				$users        = self::search_user( $phone_number );
			}

			if ( empty( $users ) ) {
				$users = self::search_user( $user_ci, array( 'mshop_auth_conninfo', 'mshop_auth_dupinfo' ) );
			}

			return $users;
		}
		public static function do_action( $params, $form ) {
			$phone_field = $form->get_field( array( 'MFD_Phone_Field' ) );
			$auth_field  = $form->get_field( array( 'MFD_Authentication_Field' ) );

			if ( empty( $phone_field ) && empty( $auth_field ) ) {
				throw new Exception( __( '아이디 찾기 기능은 휴대폰 인증 또는 간편인증 기능을 이용하셔야 합니다.', 'mshop-members-s2' ) );
			}

			if ( ! empty( $phone_field ) ) {
				$phone_field = reset( $phone_field );

				$field_name = mfd_get( $phone_field->property, 'name' );

				if ( empty( $params[ $field_name ] ) ) {
					throw new Exception( __( '휴대폰 번호가 없습니다.', 'mshop-members-s2' ) );
				}

				$users = self::get_users( $params[ $field_name ] );

				if ( ! empty( $users ) ) {
					$users = array_filter( $users, function ( $user ) {
						return '1' != get_user_meta( $user->ID, 'is_unsubscribed', true );
					} );

					if ( empty( $users ) ) {
						throw new Exception( __( '탈퇴한 사용자입니다', 'mshop-members-s2' ) );
					}
				} else {
					throw new Exception( __( '고객님의 휴대폰으로 가입된 회원 정보가 없습니다.', 'mshop-members-s2' ) );
				}
			} else {
				$auth_result = get_transient( 'msm_auth_' . $params['msm_auth'] );

				if ( empty( $auth_result ) || empty( $auth_result['user_phone'] ) || empty( $auth_result['user_ci'] ) ) {
					throw new Exception( __( '간편인증 정보가 없습니다.', 'mshop-members-s2' ) );
				}

				$users = self::get_users_by_auth_result( $auth_result['user_phone'], $auth_result['user_ci'] );

				if ( empty( $users ) ) {
					throw new Exception( __( '가입된 회원 정보가 없습니다.', 'mshop-members-s2' ) );
				}
			}

			$message = array();
			foreach ( $users as $user ) {
				$message[] = apply_filters( 'msm_find_login_user_info', sprintf( '%s (%s)', $user->user_login, $user->user_email ), $user );
			}

			wp_send_json_success( array( 'message' => sprintf( __( '고객님의 로그인 아이디는 %s 입니다.', 'mshop-members-s2' ), implode( ', ', $message ) ) ) );
		}
	}
}

