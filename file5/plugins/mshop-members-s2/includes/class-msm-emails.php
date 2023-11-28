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

if ( ! class_exists( 'MSM_Emails' ) ) {
	class MSM_Emails {
		public static function init() {
			add_filter( 'woocommerce_email_classes', array( __CLASS__, 'woocommerce_email_classes' ) );

			add_action( 'msm_new_role_application', array( __CLASS__, 'send_new_role_application' ), 10 );
		}

		static function woocommerce_email_classes( $emails ) {

			$emails['MSM_Email_New_Role_Application'] = include( 'emails/class-msm-email-new-role-application.php' );

            $emails['MSM_Email_Personal_Information'] = include( 'emails/class-msm-email-personal-info.php' );

            $emails['MSM_Email_User_Agreement_Change'] = include( 'emails/class-msm-email-user-agreement-change.php' );

            $emails['MSM_Email_User_Agreement_Information'] = include( 'emails/class-msm-email-user-agreement-info.php' );

            return $emails;
		}

		static function send_new_role_application( $post_id ) {
			if ( class_exists( 'WC_Emails' ) ) {
				WC_Emails::instance();
			}

			do_action( 'msm_new_role_application_notification', $post_id );
		}

        public static function send_personal_info_email( $user ) {
            if ( $user instanceof WP_User && is_email( $user->user_email ) ) {
                if ( 'yes' == get_option( 'mshop_members_personal_info_noti', 'no' ) ) {

                    WC_Emails::instance();

                    do_action( 'msm_send_personal_info_email_notification', $user );
                }
            }
        }

        public static function send_user_agreement_change_email( $user_id ) {
            $user = get_userdata( $user_id );
            if ( $user instanceof WP_User && is_email( $user->user_email ) ) {

                WC_Emails::instance();

                do_action( 'msm_send_user_agreement_change_email_notification', $user );
            }
        }

        public static function send_user_agreement_info_email( $user ) {
            if ( $user instanceof WP_User && is_email( $user->user_email ) ) {

                WC_Emails::instance();

                do_action( 'msm_send_user_agreement_info_email_notification', $user );
            }
        }
	}

	MSM_Emails::init();
}