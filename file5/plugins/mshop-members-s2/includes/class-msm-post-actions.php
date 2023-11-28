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

if ( ! class_exists( 'MSM_Post_Actions' ) ) {

	class MSM_Post_Actions {
		static $customer_id = 0;
		static $metas = array();
		static $role_params = null;
		public static function init() {
			add_filter( 'msm-post-actions-redirect', array( __CLASS__, 'redirect' ), 10, 4 );
			add_filter( 'msm-post-actions-meta', array( __CLASS__, 'update_meta' ), 10, 4 );
			add_filter( 'msm-post-actions-session', array( __CLASS__, 'update_session' ), 10, 4 );
			add_filter( 'msm-post-actions-trigger', array( __CLASS__, 'trigger' ), 10, 4 );
			add_filter( 'msm-post-actions-set_user_role', array( __CLASS__, 'set_user_role' ), 10, 4 );
			add_filter( 'msm_post_actions-filter_meta_value', array( __CLASS__, 'msm_post_actions_filter_meta_value' ), 10, 2 );
			add_filter( 'msm-post-actions-role', array( __CLASS__, 'change_user_role' ), 10, 4 );

			add_filter( 'msm_get_current_user_id', array( __CLASS__, 'msm_get_current_user_id' ) );

			add_action( 'wp_login', array( __CLASS__, 'wp_login' ), 10, 2 );
			add_action( 'woocommerce_created_customer', array( __CLASS__, 'woocommerce_created_customer' ), 10, 3 );
		}

		static function msm_get_current_user_id( $current_user_id ) {
			if ( self::$customer_id > 0 ) {
				$current_user_id = self::$customer_id;
			}

			return $current_user_id;
		}
		static function msm_post_actions_filter_meta_value( $value, $params ) {
			if ( '$' === $value[0] ) {
				$value = preg_replace("[‘|’]", '"', $value);
				return eval( 'return ' . $value . ';' );
			}

			switch ( $value ) {
				case 'DATETIME' :
					return current_time( 'mysql' );
				default :
					return $value;
			}
		}
		static function get_user_id() {
			if ( 0 == self::$customer_id ) {
				self::$customer_id = get_current_user_id();
			}

			return self::$customer_id;
		}
		public static function woocommerce_created_customer( $customer_id, $new_customer_data, $password_generated ) {
			self::$customer_id = $customer_id;
		}
		public static function wp_login( $user_login, $user ) {
			self::$customer_id = $user->ID;
		}

		public static function msm_submit_success() {
			if ( self::get_user_id() > 0 ) {
				foreach ( self::$metas as $meta_key => $meta_value ) {
					update_user_meta( self::get_user_id(), $meta_key, $meta_value );
				}
			}
		}
		static function redirect( $response, $form, $action, $params ) {
			$response['message'] = msm_get( $action, 'redirect_message' );

			if ( 'url' == $action['redirect_type'] ) {
				if ( ! empty( $action['redirect_url'] ) ) {
					$response['redirect_url'] = home_url( msm_get( $action, 'redirect_url' ) );
				} else {
					$response['redirect_url'] = '';
				}

				$user_role = msm_get_user_role( self::get_user_id() );
				if ( ! empty( $action['roles'] ) ) {
					foreach ( $action['roles'] as $rule ) {
						if ( $user_role == $rule['role'] ) {
							$response['redirect_url'] = $rule['redirect_url'];
							break;
						}
					}
				}
			} else {
				unset( $response['redirect_url'] );
			}

			return apply_filters( 'msm_post_action_redirect', $response, $form, $action, $params );
		}
		static function trigger( $response, $form, $action, $params ) {
			$response['trigger'] = msm_get( $action, 'trigger' );

			return apply_filters( 'msm_post_action_trigger', $response, $form, $action, $params );
		}
		static function update_meta( $response, $form, $action, $params ) {
			$meta_actions = msm_get( $action, 'meta', array() );

			foreach ( $meta_actions as $meta_action ) {
				$field      = msm_get( $meta_action, 'field' );
				$value      = msm_get( $meta_action, 'value' );
				$meta_key   = msm_get( $meta_action, 'meta_key' );
				$meta_value = apply_filters( 'msm_post_actions-filter_meta_value', msm_get( $meta_action, 'meta_value' ), $params );

				if ( ! empty( $meta_key ) && ( empty( $field ) || $value == msm_get( $params, $field ) ) ) {
					if ( self::get_user_id() > 0 ) {
						update_user_meta( self::get_user_id(), $meta_key, $meta_value );
					} else {
						self::$metas[ $meta_key ] = $meta_value;

						if ( ! has_action( 'msm_submit_success', array( __CLASS__, 'msm_submit_success' ) ) ) {
							add_action( 'msm_submit_success', array( __CLASS__, 'msm_submit_success' ) );
						}
					}
				}
			}

			return apply_filters( 'msm_post_action_update_meta', $response, $form, $action, $params );
		}
		static function update_session( $response, $form, $action, $params ) {
			msm_start_session();

			$meta_actions = msm_get( $action, 'session', array() );

			foreach ( $meta_actions as $meta_action ) {
				$field         = msm_get( $meta_action, 'field' );
				$value         = msm_get( $meta_action, 'value' );
				$session_key   = msm_get( $meta_action, 'session_key' );
				$session_value = apply_filters( 'msm_post_actions-filter_meta_value', msm_get( $meta_action, 'session_value' ), $params );

				if ( ! empty( $session_key ) && ( empty( $field ) || $value == msm_get( $params, $field ) ) ) {
					$_SESSION[ $session_key ] = $session_value;

					set_transient( msm_get( $_COOKIE, 'wp_msm_state' ) . '-' . $session_key, $session_value, 5 * MINUTE_IN_SECONDS );
				}
			}

			session_write_close();

			return apply_filters( 'msm_post_action_update_session', $response, $form, $action, $params );
		}
		static function set_user_role( $response, $form, $action, $params ) {
			$new_role = msm_get( $action, 'role', '' );

			if ( self::get_user_id() > 0 && ! empty( $new_role ) ) {
				$user = get_userdata( self::get_user_id() );

				if ( $user ) {
					$user->set_role( $new_role );
				}
			}

			return apply_filters( 'msm_post_action_update_session', $response, $form, $action, $params );
		}

		//회원관리 플러그인
		static function process_change_user_role( $form, $action, $params ) {
			$user         = get_user_by( 'id', self::get_user_id() );
			$roles        = get_editable_roles();
			$current_role = mshop_members_get_user_role( self::get_user_id() );

			if ( 'auto' == msm_get( $action, 'approve_method', 'manual' ) ) {
				$user->set_role( msm_get( $action, 'role' ) );
				update_user_meta( $user->ID, 'role_application_status', 'mshop-approved' );
			} else {
				$args = array(
					'post_title'  => msm_get( $action, 'role' ) . ' 등급 변경 요청',
					'post_type'   => 'mshop_role_request',
					'post_status' => 'mshop-apply'
				);

				$post_id = wp_insert_post( $args );

				update_post_meta( $post_id, 'current_role', $current_role );
				update_post_meta( $post_id, 'request_role', msm_get( $action, 'role' ) );
				update_post_meta( $post_id, 'user_id', self::get_user_id() );
				$timezone_format = _x( 'Y-m-d H:i:s', 'timezone date format' );
				update_post_meta( $post_id, 'request_time', date_i18n( $timezone_format ) );
				update_user_meta( $user->ID, 'role_application_status', 'mshop-apply' );


				MSM_Meta::update_post_meta( $post_id, MSM_Manager::get_post_processing_data(), '_msm_form', array(
					'except_fields' => array(
						'post_*',
						'password',
						'confirm_password'
					)
				) );

				MSM_Meta::update_user_meta(
					$user->ID,
					array(
						array(
							'form'   => $form,
							'params' => $params
						)
					),
					'_msm_role_application_fields',
					array(
						'except_fields' => array(
							'current_password',
							'password',
							'confirm_password'
						)
					)
				);

				do_action( 'msm_new_role_application', $post_id );

			}
		}
		static function change_user_role( $response, $form, $action, $params ) {
			if ( self::get_user_id() > 0 && ! empty( $action['role'] ) ) {
				self::process_change_user_role( $form, $action, $params );
			} else {
				self::$role_params = array(
					'form'   => $form,
					'action' => $action,
					'params' => $params
				);

				add_action( 'msm_submit_success', array( __CLASS__, 'msm_role_submit_success' ) );
			}

			return $response;
		}

		static function msm_role_submit_success() {
			if ( self::get_user_id() > 0 && ! is_null( self::$role_params ) ) {
				self::process_change_user_role( self::$role_params['form'], self::$role_params['action'], self::$role_params['params'] );
			}
		}

	}

}

