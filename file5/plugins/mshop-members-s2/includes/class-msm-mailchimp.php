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

if ( ! class_exists( 'MSM_Mailchimp' ) ) {

    class MSM_Mailchimp {
        public static function init() {
            add_filter( 'msm_user_registered', array ( __CLASS__, 'register_mailchimp' ) );
            add_action( 'msm_before_user_unsubscribe', array ( __CLASS__, 'delete_mailchimp' ) );
            add_action( 'msm_action_edit_user_profile', array ( __CLASS__, 'profile_check_mailchimp' ), 100, 2 );
            add_action( 'woocommerce_checkout_order_processed', array ( __CLASS__, 'checkout_mailchimp' ), 10, 3 );
        }

        public static function mailchimp_client() {
            require_once MSM()->plugin_path() . '/assets/mailchimp-vendor/autoload.php';
            $client = new MailchimpMarketing\ApiClient();
            $client->setConfig( [
                'apiKey' => get_option( 'mshop_members_mailchimp_api' ),
                'server' => get_option( 'mshop_members_mailchimp_prefix' ),
            ] );

            return $client;
        }

        public static function meta_update( $action, $args ) {
            if ( 'subscribe' == $action ) {
                $user = get_userdata( $args );
                update_user_meta( $args, 'mailchimp_email', $user->user_email );
                update_user_meta( $args, 'mailchimp_name', $user->first_name );
                update_user_meta( $args, 'mailchimp_is_subscribe', 'yes' );
            }
            if ( 'delete' == $action ) {
                delete_user_meta( $args, 'mailchimp_email' );
                delete_user_meta( $args, 'mailchimp_name' );
                update_user_meta( $args, 'mailchimp_is_subscribe', 'no' );
            }
            if ( 'order' == $action ) {
                update_user_meta( $args->get_customer_id(), 'mailchimp_email', $args->get_billing_email() );
                update_user_meta( $args->get_customer_id(), 'mailchimp_name', $args->get_billing_first_name() . $args->get_billing_last_name() );
                update_user_meta( $args->get_customer_id(), 'mailchimp_is_subscribe', 'yes' );
            }
        }
        public static function subscribe( $email, $name = '', $list_id = '', $status = '' ) {
            if ( empty( $list_id ) ) {
                $list_id = get_option( 'mshop_members_mailchimp_list_id' );
            }
            if ( empty( $status ) ) {
                $status = 'subscribed';
            }

            $client = self::mailchimp_client();

            $response = $client->lists->setListMember( $list_id, md5( $email ), [
                "email_address" => $email,
                "status_if_new" => $status,
                "status"        => $status,
                "merge_fields"  => [
                    "FNAME" => $name,
                ]
            ] );

            return $response;
        }
        public static function delete( $email, $list_id = '' ) {
            if ( empty( $list_id ) ) {
                $list_id = get_option( 'mshop_members_mailchimp_list_id' );
            }

            $client = self::mailchimp_client();

            $response = $client->lists->deleteListMember( $list_id, md5( $email ) );

            return $response;

        }

        public static function register_mailchimp( $user_id ) {
            if ( get_user_meta( $user_id, 'email_agreement', true ) ) {
                try {
                    $user = get_userdata( $user_id );
                    self::subscribe( $user->user_email, $user->first_name );
                    self::meta_update( 'subscribe', $user_id );
                } catch ( Exception $e ) {
                    $message = sprintf( "에러 %s [%s]", $e->getMessage(), $e->getCode() );
                    wc_add_notice( $message, 'error' );
                }
            }
        }

        public static function delete_mailchimp( $user_id ) {
            try {
                $user = get_userdata( $user_id );
                self::delete( $user->user_email );
                self::meta_update( 'delete', $user_id );
            } catch ( Exception $e ) {
                $message = sprintf( "에러 %s [%s]", $e->getMessage(), $e->getCode() );
                wc_add_notice( $message, 'error' );
            }

        }
        public static function profile_check_mailchimp( $params, $form ) {
            $user_id               = get_current_user_id();
            $user                  = get_userdata( $user_id );
            $mailchimp_subscribers = get_user_meta( $user_id, 'mailchimp_is_subscribe', true );
            $mailchimp_name        = get_user_meta( $user_id, 'mailchimp_name', true );
            $email_agreement       = get_user_meta( $user_id, 'email_agreement', true );
            $mailchimp_email       = get_user_meta( $user_id, 'mailchimp_email', true );
            if ( 'on' == $email_agreement && ( 'no' == $mailchimp_subscribers || $user->user_email != $mailchimp_email || $user->first_name != $mailchimp_name ) ) {
                if ( $user->user_email != $mailchimp_email ) {
                    if ( 'yes' == get_user_meta( $user_id, 'mailchimp_is_subscribe', true ) ) {
                        try {
                            self::delete( $mailchimp_email );
                        } catch ( Exception $e ) {
                            $message = sprintf( "에러 %s [%s]", $e->getMessage(), $e->getCode() );
                            wc_add_notice( $message, 'error' );
                        }
                    }
                }
                try {
                    self::subscribe( $user->user_email, $user->first_name );
                    self::meta_update( 'subscribe', $user_id );
                } catch ( Exception $e ) {
                    $message = sprintf( "에러 %s [%s]", $e->getMessage(), $e->getCode() );
                    wc_add_notice( $message, 'error' );
                }
            }
            if ( 'on' != $email_agreement && 'yes' == $mailchimp_subscribers ) {
                try {
                    self::delete( $mailchimp_email );
                    self::meta_update( 'delete', $user_id );
                } catch ( Exception $e ) {
                    $message = sprintf( "에러 %s [%s]", $e->getMessage(), $e->getCode() );
                    wc_add_notice( $message, 'error' );
                }
            }

        }

        public static function checkout_mailchimp( $order_id, $posted_data, $order ) {
            $mailchimp_name  = get_user_meta( $order->get_customer_id(), 'mailchimp_name', true );
            $mailchimp_email = get_user_meta( $order->get_customer_id(), 'mailchimp_email', true );
            if ( 'yes' == get_user_meta( $order->get_customer_id(), 'mailchimp_is_subscribe', true ) && ( $mailchimp_email != $order->get_billing_email() || $order->get_billing_first_name() . $order->get_billing_last_name() != $mailchimp_name ) ) {
                if ( $order->get_billing_email() != $mailchimp_email ) {
                    if ( 'yes' == get_user_meta( get_current_user_id(), 'mailchimp_is_subscribe', true ) ) {
                        try {
                            self::delete( $mailchimp_email );
                        } catch ( Exception $e ) {
                            $message = sprintf( "에러 %s [%s]", $e->getMessage(), $e->getCode() );
                        }
                    }
                }

                try {
                    self::subscribe( $order->get_billing_email(), $order->get_billing_first_name() . $order->get_billing_last_name() );
                    self::meta_update( 'order', $order );
                } catch ( Exception $e ) {
                    $message = sprintf( "에러 %s [%s]", $e->getMessage(), $e->getCode() );
                }
            }

        }

    }

    MSM_Mailchimp::init();
}
