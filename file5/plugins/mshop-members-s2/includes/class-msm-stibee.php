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

if ( ! class_exists( 'MSM_Stibee' ) ) {

    class MSM_Stibee {

        protected static $endpoint = array (
            'subscribe'   => array (
                'endpoint' => 'subscribers',
                'method'   => 'POST'
            ),
            'unsubscribe' => array (
                'endpoint' => 'subscribers/unsubscribe',
                'method'   => 'PUT'
            ),
            'delete'      => array (
                'endpoint' => 'subscribers',
                'method'   => 'DELETE'
            ),
        );

        public static function init() {
            add_filter( 'msm_user_registered', array ( __CLASS__, 'register_stibee' ) );
            add_action( 'msm_before_user_unsubscribe', array ( __CLASS__, 'delete_stibee' ) );
            add_action( 'msm_action_edit_user_profile', array ( __CLASS__, 'profile_check' ), 100, 2 );
            add_action( 'woocommerce_checkout_order_processed', array ( __CLASS__, 'checkout_stibee' ), 10, 3 );
        }
        protected static function call( $action, $args, $list_id = '' ) {
            if ( empty( $list_id ) ) {
                $list_id = get_option( 'mshop_members_stibee_lists' );
            }
            $response = wp_remote_post( 'https://api.stibee.com/v1/lists/' . $list_id . '/' . self::$endpoint[ $action ][ 'endpoint' ], array (
                    'headers' => array (
                        'AccessToken'  => get_option( 'mshop_members_stibee_api' ),
                        'content-type' => 'application/json',
                    ),
                    'body'    => json_encode( $args ),
                    'method'  => self::$endpoint[ $action ][ 'method' ]
                )
            );

            if ( ! is_wp_error( $response ) ) {
                return json_decode( $response[ 'body' ], true );
            }

        }

        public static function meta_update( $action, $args ) {
            if ( 'subscribe' == $action ) {
                $user = get_userdata( $args );
                update_user_meta( $args, 'stibee_email', $user->user_email );
                update_user_meta( $args, 'stibee_name', $user->first_name );
                update_user_meta( $args, 'stibee_is_subscribe', 'yes' );
            }
            if ( 'delete' == $action ) {
                delete_user_meta( $args, 'stibee_email' );
                delete_user_meta( $args, 'stibee_name' );
                update_user_meta( $args, 'stibee_is_subscribe', 'no' );
            }
            if ( 'order' == $action ) {
                update_user_meta( $args->get_customer_id(), 'stibee_email', $args->get_billing_email() );
                update_user_meta( $args->get_customer_id(), 'stibee_name', $args->get_billing_first_name() . $args->get_billing_last_name() );
                update_user_meta( $args->get_customer_id(), 'stibee_is_subscribe', 'yes' );
            }
        }
        public static function subscribe( $email, $name, $list_id = '', $group_id = '', $type = '' ) {
            if ( empty( $group_id ) ) {
                if ( false == get_option( 'mshop_members_stibee_groupid' ) ) {
                    $group_id = '';
                } else {
                    $group_id = get_option( 'mshop_members_stibee_groupid' );
                }
            }

            if ( empty( $type ) ) {
                $type = 'SUBSCRIBER';
            }

            if ( $group_id ) {
                $args = array (
                    'eventOccuredBy' => $type,
                    'confirmEmailYN' => 'N',
                    'groupIds'       => array ( $group_id ),
                    'subscribers'    => array (
                        array (
                            'email' => $email,
                            'name'  => $name
                        )
                    )
                );
            } else {
                $args = array (
                    'eventOccuredBy' => $type,
                    'confirmEmailYN' => 'N',
                    'subscribers'    => array (
                        array (
                            'email' => $email,
                            'name'  => $name
                        )
                    )
                );
            }

            return self::call( 'subscribe', $args, $list_id );
        }

        public static function unsubscribe( $email, $list_id = '' ) {
            return self::call( 'unsubscribe', array ( $email ), $list_id );
        }
        public static function delete( $email, $list_id = '' ) {
            return self::call( 'delete', array ( $email ), $list_id );
        }
        public static function register_stibee( $user_id ) {
            if ( 'on' == get_user_meta( $user_id, 'email_agreement', true ) ) {
                $user = get_userdata( $user_id );

                $result = self::subscribe( $user->user_email, $user->first_name );

                if ( $result[ 'Ok' ] ) {
                    self::meta_update( 'subscribe', $user_id );
                }
            }
        }
        public static function delete_stibee( $user_id ) {

            self::delete( get_user_meta( $user_id, 'stibee_email', true ) );

            self::meta_update( 'delete', $user_id );
        }
        public static function profile_check( $params, $form ) {
            $user_id            = get_current_user_id();
            $user               = get_userdata( $user_id );
            $stibee_subscribers = get_user_meta( $user_id, 'stibee_is_subscribe', true );
            $stibee_name        = get_user_meta( $user_id, 'stibee_name', true );
            $email_agreement    = get_user_meta( $user_id, 'email_agreement_label', true );
            $stibee_email       = get_user_meta( $user_id, 'stibee_email', true );
            if ( 'YES' == $email_agreement && ( 'no' == $stibee_subscribers || $user->user_email != $stibee_email || $user->first_name != $stibee_name ) ) {
                self::delete( $stibee_email );
                $result = self::subscribe( $user->user_email, $user->first_name );

                if ( $result[ 'Ok' ] ) {
                    self::meta_update( 'subscribe', $user_id );
                }
            }

            if ( 'YES' != $email_agreement && 'yes' == $stibee_subscribers ) {
                $result = self::delete( $stibee_email );
                if ( $result[ 'Ok' ] ) {
                    self::meta_update( 'delete', $user_id );
                }
            }

        }


        public static function checkout_stibee( $order_id, $posted_data, $order ) {
            $stibee_name  = get_user_meta( $order->get_customer_id(), 'stibee_name', true );
            $stibee_email = get_user_meta( $order->get_customer_id(), 'stibee_email', true );
            if ( 'yes' == get_user_meta( $order->get_customer_id(), 'stibee_is_subscribe', true ) && ( $stibee_email != $order->get_billing_email() || $order->get_billing_first_name() . $order->get_billing_last_name() != $stibee_name ) ) {
                self::delete( $stibee_email );
                $result = self::subscribe( $order->get_billing_email(), $order->get_billing_first_name() . $order->get_billing_last_name() );

                if ( $result[ 'Ok' ] ) {
                    self::meta_update( 'order', $order );
                }
            }

        }
    }

    MSM_Stibee::init();
}