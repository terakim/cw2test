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

if ( ! class_exists( 'MSM_Message' ) ) {

	class MSM_Message {
		public static function init() {
			add_filter( 'mssms_order_statuses', array( __CLASS__, 'add_status_for_new_account' ) );
			add_filter( 'msm_user_registered', array( __CLASS__, 'send_sms' ) );
			add_filter( 'msm_user_registered', array( __CLASS__, 'send_alimtalk' ) );
		}
		public static function add_status_for_new_account( $order_statuses ) {
			return array_merge( array(
				'msm_new_account' => __( '신규회원가입', 'mshop-members-s2' )
			), $order_statuses );
		}
		public static function send_sms( $user_id ) {
			$user = get_userdata( $user_id );

			if ( ! class_exists('MSSMS_Manager' ) || ! $user ) {
				return;
			}

			$targets = array( 'admin', 'user' );

			foreach ( $targets as $target ) {
				try {
					$message = MSSMS_SMS::get_message_by_order_status( 'msm_new_account', $target );

					if ( ! empty( $messages ) ) {
						if ( 'admin' == $target ) {
							$recipients = MSSMS_Manager::get_admin_phone_numbers();
						} else {
							$recipients[] = get_user_meta( $user->ID, 'billing_phone', true );
						}

						if ( empty( $recipients ) ) {
							continue;
						}

						$template_params        = MSSMS_Manager::get_template_params( null );
						$template_params['고객명'] = get_user_meta( $user->ID, 'billing_first_name', true ) . get_user_meta( $user->ID, 'billing_first_name', true );

						$recipients = array_map( function ( $receiver ) use ( $template_params ) {
							return array(
								'receiver'        => $receiver,
								'template_params' => $template_params
							);
						}, $recipients );

						$type = MSSMS_SMS::get_sms_type( $message, $template_params );

						MSSMS_SMS::send_sms( $type, '', $message, $recipients, '', MSSMS_Manager::get_request_date() );
					}
				} catch ( Exception $e ) {

				}
			}
		}
		public static function send_alimtalk( $user_id ) {
			$user = get_userdata( $user_id );

			if ( ! class_exists('MSSMS_Manager' ) || ! $user ) {
				return;
			}

			$targets = array( 'admin', 'user' );

			foreach ( $targets as $target ) {
				try {
					$template_code = MSSMS_Kakao::get_template_code_by_order_status( 'msm_new_account', $target );

					$template_infos = array(
						array(
							'template_code' => $template_code,
							'resend_params' => array()
						)
					);

					if ( ! empty( $template_infos ) ) {
						if ( 'admin' == $target ) {
							$recipients = MSSMS_Manager::get_admin_phone_numbers();
						} else {
							$recipients[] = get_user_meta( $user->ID, 'billing_phone', true );
						}

						if ( empty( $recipients ) ) {
							continue;
						}

						$template_params        = MSSMS_Manager::get_template_params( null );
						$template_params['고객명'] = get_user_meta( $user->ID, 'billing_first_name', true ) . get_user_meta( $user->ID, 'billing_first_name', true );

						foreach ( $template_infos as $template_info ) {
							if ( ! empty( $template_info['template_code'] ) ) {
								$template = MSSMS_Kakao::get_template( $template_info['template_code'] );

								if ( $template ) {
									$profile = MSSMS_Kakao::get_profile( $template['plus_id'] );
									if ( empty( $template_info['resend_params'] ) ) {
										$resend_params = MSSMS_Kakao::get_resend_params( $profile, 'msm_new_account', $target );
									} else {
										$resend_params = $template_info['resend_params'];
									}

									MSSMS_Kakao::send_alimtalk( $template_info['template_code'], $recipients, $template_params, $resend_params );
								}
							}
						}
					}
				} catch ( Exception $e ) {

				}
			}
		}
	}

	MSM_Message::init();
}