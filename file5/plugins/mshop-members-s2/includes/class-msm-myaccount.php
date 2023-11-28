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

if ( ! class_exists( 'MSM_Myaccount' ) ) {

	class MSM_Myaccount {

		public static function init() {
			if ( MSM_Manager::enabled() ) {
				if ( MSM_Manager::use_unsubscribe() ) {
					add_filter( 'woocommerce_account_menu_items', array( __CLASS__, 'woocommerce_account_menu_items' ) );
					add_action( 'woocommerce_account_msm-unsubscribe_endpoint', array( __CLASS__, 'msm_unsubscribe_endpoint' ) );
				}

				if ( 'yes' == get_option( 'msm_user_can_edit_fields', 'no' ) ) {
					add_action( 'woocommerce_edit_account_form', array( __CLASS__, 'add_msm_fields' ) );
					add_action( 'woocommerce_save_account_details', array( __CLASS__, 'save_msm_fields' ) );
				}

				add_action( 'woocommerce_edit_account_form_start', array( __CLASS__, 'show_social_login_connect_status' ) );
				add_action( 'msm_social_connect_status', array( __CLASS__, 'show_social_login_connect_status' ) );
			}
		}

		public static function show_social_login_connect_status() {
			$enabled_providers = MSM_Social_Login::enabled_providers();

			if ( ! empty( $enabled_providers ) ) {
				wp_enqueue_style( 'msm-frontend', plugins_url( 'assets/css/frontend.css', MSM_PLUGIN_FILE ), array(), MSHOP_MEMBERS_VERSION );

				update_user_meta( get_current_user_id(), 'msm_oauth_state', bin2hex( openssl_random_pseudo_bytes( 10 ) ) );

				$registered_by = get_user_meta( get_current_user_id(), '_msm_oauth_registered_by', true );

				if ( empty( $registered_by ) ) {
					msm_get_template( 'myaccount/social-connect-status.php', array( 'enabled_providers' => $enabled_providers ), '', MSM()->plugin_path() . '/templates/' );
				} else {
					$provider = MSM_Social_Login::get_provider( $registered_by );
					if ( $provider ) {
						msm_get_template( 'myaccount/social-connected-info.php', array( 'provider' => $provider ), '', MSM()->plugin_path() . '/templates/' );
					}
				}

			}
		}

		public static function output_row( $rule, $satisfy = false ) {
			$page = msm_get( $rule, 'page' );
			if ( is_array( $page ) ) {
				$page = current( array_keys( $page ) );
			}
			?>
            <tr>
                <td><?php echo msm_get( $rule, 'rule_title' ); ?></td>
                <td><?php echo msm_get( $rule, 'description' ); ?></td>
                <td>
					<?php if ( ! empty( $rule['button_text'] ) ) : ?>
                        <a href="<?php echo get_permalink( $page ); ?>"
                           class="button"><?php echo msm_get( $rule, 'button_text' ); ?></a>
					<?php endif; ?>
                </td>
            </tr>
			<?php
		}

		static function check_additional_condition( $conditions ) {
			foreach ( $conditions as $condition ) {
				$result = $condition['value'] == apply_filters( 'msm_check_rule_conditions', null, $condition );

				if ( ! $result ) {
					return false;
				}
			}

			return true;
		}

		public static function output() {
			$roles     = apply_filters( 'msm_get_roles', array() );
			$user_role = mshop_members_get_user_role();

			if ( isset( $roles[ $user_role ] ) ) {
				$role_name = $roles[ $user_role ];

				msm_get_template( 'myaccount/members-info.php', array( 'roles' => $roles, 'user_role' => $user_role, 'role_name' => $role_name ), '', MSM()->plugin_path() . '/templates/' );
			}
		}

		public static function woocommerce_account_menu_items( $items ) {

			$logout_endpoint = get_option( 'woocommerce_logout_endpoint', 'customer-logout' );
			if ( ! empty( $logout_endpoint ) ) {
				$removed = false;
				if ( isset( $items['customer-logout'] ) ) {
					unset( $items['customer-logout'] );
					$removed = true;
				}
				$items['msm-unsubscribe'] = __( '회원탈퇴', 'mshop-mcommerce-premium-s2' );

				if ( $removed ) {
					$items['customer-logout'] = __( 'Logout', 'woocommerce' );
				}
			} else {
				$items['msm-unsubscribe'] = __( '회원탈퇴', 'mshop-mcommerce-premium-s2' );
			}

			return $items;
		}

		public static function msm_unsubscribe_endpoint() {
			if ( 'no' == get_option( 'msm_prevent_unsubscribe_when_have_active_subscription', 'no' ) || ! function_exists( 'wcs_user_has_subscription' ) || empty( wcs_user_has_subscription( get_current_user_id(), '', 'active' ) ) ) {
				echo do_shortcode( "[mshop_form_designer slug='unsubscribe' default=true]" );
			} else {
				msm_get_template( 'myaccount/can-not-unsubscribe.php' );
			}
		}

		static function add_msm_fields() {
			$metas = MSM_Meta::get_user_meta( get_current_user_id(), '_msm_register_fields' );

			?>
            <fieldset>
				<?php foreach ( $metas as $meta ) : ?>
					<?php if ( ! empty( $meta['title'] ) ) : ?>
                        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                            <label for="<?php echo esc_attr( $meta['name'] ); ?>"><?php echo esc_html( $meta['title'] ); ?></label>
                            <input type="text" class="woocommerce-Input woocommerce-Input--password input-text" name="<?php echo esc_attr( $meta['name'] ); ?>" id="<?php echo esc_attr( $meta['name'] ); ?>" value="<?php echo $meta['value']; ?>" autocomplete="off"/>
                        </p>
					<?php endif; ?>
				<?php endforeach; ?>
            </fieldset>
            <div class="clear"></div>
			<?php
		}


		static function save_msm_fields() {
			$fields = apply_filters( 'msm_users_custom_column', array( 'msm_register_fields' ) );

			foreach ( $fields as $field ) {
				$form_info = get_user_meta( get_current_user_id(), '_' . $field, true );

				if ( ! empty( $form_info ) ) {
					foreach ( $form_info['forms'] as $form_data ) {
						$fields = MSM_Meta::filter_fields( mfd_get_form_fields( $form_data['data'] ), $form_info['args'] );

						foreach ( $fields as $field ) {
							if ( ! empty( $field->name ) ) {
								update_user_meta( get_current_user_id(), $field->name, $_POST[ $field->name ] );
							}
						}
					}
				}

			}
		}
	}

	MSM_Myaccount::init();

}