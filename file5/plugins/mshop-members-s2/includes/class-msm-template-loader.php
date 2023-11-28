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

if ( ! class_exists( 'MSM_Template_Loader' ) ) {

	class MSM_Template_Loader {
		public static function woocommerce_locate_template( $template, $template_name, $template_path ) {
			if ( apply_filters( 'msm_is_checkout', is_checkout() ) && ! is_user_logged_in() && 'yes' === get_option( 'mshop_members_require_tac_for_guest', 'no' ) ) {
				if ( 'checkout/form-checkout.php' === $template_name ) {
					if ( 'yes' != get_transient( msm_get( $_COOKIE, 'wp_msm_state' ) . '-mshop_accept_terms_and_conditions' ) ) {
						$tac_form = MSM_Manager::get_form_by_slug( get_option( 'mshop_members_tac_form_for_guest' ) );

						if ( ! is_null( $tac_form ) ) {
							add_filter( 'msm_skip_on_checkout', '__return_false' );

							return MSM()->plugin_path() . '/templates/terms/guest.php';
						}
					}
				}
			} else if ( ! msm_is_ajax() ) {
				delete_transient( msm_get( $_COOKIE, 'wp_msm_state' ) . '-mshop_accept_terms_and_conditions' );
			}

			return $template;
		}
		public static function template_include( $template ) {
			global $wp_query;

			if ( is_user_logged_in() && ! is_super_admin() ) {
				if ( 'yes' === get_option( 'mshop_members_use_terms_and_conditions', 'no' ) && 'yes' === get_option( 'mshop_members_require_tac_for_customer', 'no' ) && 'yes' != get_user_meta( get_current_user_id(), '_mshop_acceptance_of_terms', true ) ) {

					$tac_form = MSM_Manager::get_form_by_slug( get_option( 'mshop_members_tac_form_for_customer' ) );

					if ( ! is_null( $tac_form ) ) {
						add_filter( 'msm_skip_on_checkout', '__return_false' );

						return MSM()->plugin_path() . '/templates/terms/customer.php';
					}
				}
			}

			if ( ! empty( $_GET['msm_preview'] ) ) {
				return MSM()->plugin_path() . '/templates/preview.php';
			}

			return $template;
		}

		public static function template_redirect() {
			if ( MSM_Security::validate_url() ) {
				return;
			}

			if ( is_user_logged_in() && ! is_super_admin() ) {
				if ( ! is_page( 'email-authentication' ) && 'yes' === get_option( 'msm_required', 'no' ) && 'yes' != get_user_meta( get_current_user_id(), 'msm_email_certified', true ) ) {
					if ( 'yes' != get_option( 'msm_social_except', 'no' ) || empty( get_user_meta( get_current_user_id(), 'wsl_current_provider', true ) ) ) {
						$email_auth_page = get_page_by_path( 'email-authentication' );

						if ( ! is_null( $email_auth_page ) ) {
							wp_safe_redirect( get_permalink( $email_auth_page ) );
							exit();
						}
					}
				}
			}
		}
	}
}