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

if ( ! class_exists( 'MSM_Profile' ) ) {

	class MSM_Profile {
		static $_page_settings = null;

		public static function init() {
			add_filter( 'woocommerce_account_menu_items', array( __CLASS__, 'add_profile_menu_item' ) );
			add_action( 'woocommerce_account_msm-profile_endpoint', array( __CLASS__, 'output_user_profile_page' ) );

			add_action( 'msm_submit_action', array( __CLASS__, 'add_profile_actions' ) );
			add_action( 'msm_post_action_redirect', array( __CLASS__, 'maybe_unset_redirect_url' ), 10, 4 );

			add_action( 'msm_action_edit_user_profile', array( __CLASS__, 'do_edit_user_profile' ), 10, 2 );
			add_action( 'msm_action_edit_password', array( __CLASS__, 'do_edit_password' ), 10, 2 );
			add_action( 'msm_post_data', array( __CLASS__, 'post_data' ), 10, 2 );


			add_filter( 'pre_get_avatar', array( __CLASS__, 'maybe_change_avatar_image' ), 10, 3 );
			add_filter( 'get_avatar', array( __CLASS__, 'maybe_change_avatar' ), 10, 6 );
			add_filter( 'woocommerce_prevent_admin_access', array( __CLASS__, 'maybe_allow_admin_access' ) );
		}
		public static function add_profile_menu_item( $items ) {
			if ( self::show_profile() ) {
				if ( self::hide_edit_account() ) {
					unset( $items['edit-account'] );
				}
				$items['msm-profile'] = __( '프로필', 'mshop-members-s2' );
			}

			return $items;
		}
		public static function output_user_profile_page() {
			$slug = self::get_profile_form( 'edit' );

			if ( ! empty( $slug ) ) {
				msm_get_template( 'myaccount/edit-profile.php', array( 'slug' => $slug ), array(), MSM()->plugin_path() . '/templates/' );
			}
		}
		static function get_profile_page_settings() {
			if ( is_null( self::$_page_settings ) ) {
				self::$_page_settings = get_option( 'msm_profile_page_setting' );
			}

			return self::$_page_settings;
		}
		static function get_user_profile_page( $user_id = null ) {
			$settings  = self::get_profile_page_settings();
			$user_role = msm_get_user_role( $user_id );

			if ( ! empty( $settings ) ) {
				foreach ( $settings as $setting ) {
					$roles = explode( ',', $setting['user_roles'] );
					if ( in_array( $user_role, $roles ) ) {
						if ( 'exclude' == msm_get( $setting, 'social_login', 'include' ) && ! empty( get_user_meta( get_current_user_id(), '_msm_oauth_registered_by', true ) ) ) {
							continue;
						}

						return $setting;
					}
				}
			}

			return null;
		}
		public static function hide_edit_account() {
			return 'yes' == get_option( 'msm_profile_hide_edit_account', 'yes' );
		}
		public static function show_profile() {
			return self::get_user_profile_page();
		}
		public static function get_profile_form( $type = 'view', $user_id = null ) {
			$profile = self::get_user_profile_page( $user_id );

			if ( ! empty( $profile ) ) {
				if ( 'view' == $type ) {
					return $profile['view_form_id'];
				} else if ( 'edit' == $type ) {
					return $profile['edit_form_id'];
				}
			}

			return '';
		}
		public static function add_profile_actions( $actions ) {
			$actions['msm_action_edit_user_profile'] = __( '사용자 프로필 편집', 'mshop-members-s2' );
			$actions['msm_action_show_user_profile'] = __( '사용자 프로필 보기', 'mshop-members-s2' );
			$actions['msm_action_edit_password']     = __( '비밀번호 변경', 'mshop-members-s2' );

			return $actions;
		}

		public static function maybe_unset_redirect_url( $response, $form, $action, $params ) {
			if ( ! apply_filters( 'msm_profile_unset_redirect_action', true ) ) {
				return $response;
			}

			if ( 'msm_action_edit_user_profile' == $form->submit_action && empty( $params['password'] ) ) {
				unset( $response['redirect_url'] );
			}

			return $response;
		}
		static function do_edit_user_profile( $params, $form ) {
			if ( is_user_logged_in() ) {
				$user = wp_get_current_user();
				if ( ! empty( $params['current_password'] ) ) {

					if ( ! wp_check_password( $params['current_password'], $user->data->user_pass, $user->ID ) ) {
						throw new Exception( '비밀번호가 올바르지 않습니다.' );
					}

					if ( empty( $params['password'] ) || empty( $params['confirm_password'] ) || $params['password'] != $params['confirm_password'] ) {
						throw new Exception( '새 비밀번호가 올바르지 않습니다.' );
					}

					wp_update_user( array(
						'ID'        => $user->ID,
						'user_pass' => $params['password']
					) );
				}
				$user_data = array();
				foreach ( $params as $key => $value ) {
					switch ( $key ) {
						case 'display_name' :
						case 'first_name' :
							$user_data['display_name'] = $value;
							break;
						case 'user_nicename' :
							$user_data['user_nicename'] = $value;
							break;
						case 'billing_email' :
						case 'user_email' :
							$user_data['user_email'] = $value;
							break;
					}
				}

				if ( ! empty( $user_data ) ) {
					$user_data['ID'] = $user->ID;
					$user_id         = wp_update_user( $user_data );

					if ( is_wp_error( $user_id ) ) {
						throw new Exception( $user_id->get_error_message() );
					}
				}
				MSM_Meta::update_user_meta(
					$user->ID,
					array(
						array(
							'form'   => $form,
							'params' => $params
						)
					),
					'_msm_profile_fields',
					array(
						'except_fields' => array(
							'current_password',
							'password',
							'confirm_password',
							'display_name',
							'user_nicename',
							'user_email',
						)
					)
				);
			} else {
				throw new Exception( '올바르지 않은 요청입니다.' );
			}
		}
		static function do_edit_password( $params, $form ) {
			if ( is_user_logged_in() ) {
				$user = wp_get_current_user();
				if ( ! empty( $params['current_password'] ) ) {

					if ( ! wp_check_password( $params['current_password'], $user->data->user_pass, $user->ID ) ) {
						throw new Exception( '비밀번호가 올바르지 않습니다.' );
					}

					if ( empty( $params['password'] ) || empty( $params['confirm_password'] ) || $params['password'] != $params['confirm_password'] ) {
						throw new Exception( '새 비밀번호가 올바르지 않습니다.' );
					}

					wp_update_user( array(
						'ID'        => $user->ID,
						'user_pass' => $params['password']
					) );
				}
			} else {
				throw new Exception( '올바르지 않은 요청입니다.' );
			}
		}
		public static function post_data( $post, $form ) {
			if ( in_array( $form->submit_action, array( 'msm_action_edit_user_profile', 'msm_action_edit_password' ) ) ) {
				$post = wp_get_current_user();
			} else if ( 'msm_action_show_user_profile' == $form->submit_action && ! empty( $_REQUEST['id'] ) ) {
				$post = get_user_by( 'id', $_REQUEST['id'] );
			}

			return $post;
		}
		public static function maybe_change_avatar_image( $image, $id_or_email, $args ) {
			if ( 'yes' == get_option( 'msm_profile_image_review' ) && true == get_option( 'show_avatars' ) ) {
				$user = null;

				if ( is_numeric( $id_or_email ) ) {
					$user = get_user_by( 'id', absint( $id_or_email ) );
				} elseif ( $id_or_email instanceof WP_User ) {
					$user = $id_or_email;
				} elseif ( $id_or_email instanceof WP_Post ) {
					$user = get_user_by( 'id', (int) $id_or_email->post_author );
				} elseif ( $id_or_email instanceof WP_Comment ) {
					if ( ! empty( $id_or_email->user_id ) ) {
						$user = get_user_by( 'id', (int) $id_or_email->user_id );
					}
				}

				if ( is_a( $user, 'WP_User' ) ) {
					$image_id = get_user_meta( $user->ID, 'profile_image', true );

					if ( ! empty( $image_id ) ) {
						if ( is_numeric( $image_id ) && wp_attachment_is_image( $image_id ) ) {
							$image_src = wp_get_attachment_url( $image_id );
							if ( ! empty( $image_src ) ) {
								$image = sprintf( "<img alt src='%s' class='avatar avatar-%d photo' height='%d' width='%d'/>", $image_src, $args['size'], $args['height'], $args['width'] );
							}
						} else if ( is_string( $image_id ) ) {
							$image = sprintf( "<img alt src='%s' class='avatar avatar-%d photo' height='%d' width='%d'/>", $image_id, $args['size'], $args['height'], $args['width'] );
						}
					}
				}
			}

			return $image;
		}
		static function maybe_change_avatar( $avatar, $id_or_email, $size, $default, $alt, $args ) {
			if ( is_numeric( $id_or_email ) ) {
				$profile  = MSM_Profile::get_user_profile_page( $id_or_email );
				$image_id = get_user_meta( $id_or_email, 'profile_image', true );

				if ( ! empty( $image_id ) ) {
					if ( is_numeric( $image_id ) && wp_attachment_is_image( $image_id ) && ! empty( $profile ) ) {
						$image_src = wp_get_attachment_image( $image_id, array( $size, $size ) );
						if ( ! empty( $image_src ) ) {
							$avatar = $image_src;
						}
					} else if ( is_string( $image_id ) ) {
						$avatar = sprintf( "<img width='%d' height='%d' src='%s'/>", $size, $size, $image_id );
					}
				}
			}

			return $avatar;
		}
		static function maybe_allow_admin_access( $prevent_access ) {
			if ( 'yes' == get_option( 'msm_profile_image_review' ) ) {
				$prevent_access = $prevent_access && ( ! isset( $_POST['action'] ) || 'upload-attachment' != $_POST['action'] );
			}

			return $prevent_access;
		}
	}

	MSM_Profile::init();
}