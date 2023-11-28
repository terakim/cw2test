<?php

defined( 'ABSPATH' ) || exit;
class MSM_User_Agreement_Info {
    protected static $action = 'msm_agreement_information_notification';

    public static function init() {

    }
    static function enabled() {
        return 'yes' == get_option( 'msm_user_agreement_info_noti_enable', 'no' );
    }
    static function is_running() {
        return ! empty( get_transient( self::$action ) );
    }
    static function clear() {
        self::deregister_scheduled_action();
        delete_transient( self::$action );
    }
    static function maybe_register_scheduled_action( $page = 0, $immediately = false ) {
        if ( ! self::is_running() ) {
            self::register_scheduled_action( $page, $immediately );
        }
    }
    static function register_scheduled_action( $page = 0, $immediately = false ) {
        if ( $immediately ) {
            as_schedule_single_action(
                time() + MINUTE_IN_SECONDS,
                self::$action,
                array( 'page' => $page )
            );
        } else {
            if ( self::enabled() ) {
                $next_schedule = self::get_next_schedule();
                if ( empty( $next_schedule ) ) {

                    $current_year = date('Y', strtotime( current_time( 'mysql' ) ) );
                    $current_date = date('Y-m-d', strtotime( current_time( 'mysql' ) ) );
                    $next_date = date( 'Y-m-d H:i:s', strtotime( $current_year . '-' . get_option( 'msm_agreement_info_send_date' ) . apply_filters( 'msm_personal_info_send_time', '09:00' ) ) );

                    if( $current_date > $next_date ) {
                        $next_date = date('Y-m-d', strtotime( $next_date . '+1 year' ) );
                    }

                    $next_time = strtotime( $next_date ) - intval( get_option( 'gmt_offset', 0 ) ) * HOUR_IN_SECONDS;

                    if ( $next_time > time() ) {
                        as_schedule_single_action(
                            $next_time,
                            self::$action,
                            array( 'page' => 0 )
                        );
                    }
                }
            }

            delete_transient( self::$action );
        }
    }
    static function deregister_scheduled_action() {
        if ( function_exists( 'as_unschedule_all_actions' ) ) {
            as_unschedule_all_actions( self::$action );
        }
    }
    static function maybe_deregister_scheduled_action() {
        if ( ! self::is_running() ) {
            self::deregister_scheduled_action();
        }
    }
    static function get_next_schedule() {
        if ( self::enabled() && class_exists( 'ActionScheduler_Store' ) ) {
            $actions = as_get_scheduled_actions( array( 'hook' => self::$action, 'status' => ActionScheduler_Store::STATUS_PENDING ) );
            $action  = current( $actions );

            if ( $action ) {
                $schedule = $action->get_schedule();

                if ( is_callable( array( $schedule, 'get_date' ) ) ) {
                    return $schedule->get_date()->modify( '+' . get_option( 'gmt_offset', 0 ) . ' hours' )->format( 'Y-m-d H:i' );
                } else if ( is_callable( array( $schedule, 'next' ) ) ) {
                    return $schedule->next()->modify( '+' . get_option( 'gmt_offset', 0 ) . ' hours' )->format( 'Y-m-d H:i' );
                }
            }
        }

        return '';
    }
    protected static function get_template_params( $user_id ) {
        $template_params = array(
            '쇼핑몰명' => wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ),
        );

        if ( is_user_logged_in() ) {
            $user = get_userdata( get_current_user_id() );

            $send_date = __( 'Y년 m월 d일', 'mshop-members-s2' );
            $template_params = array_merge( $template_params, array (
                '고객명'       => $user->display_name,
                '아이디'       => $user->user_login,
                '발송일'      => date( $send_date, strtotime( current_time( 'mysql' ) ) ),
                '문자수신동의상태'  => empty( get_user_meta( $user_id, 'mssms_agreement', true ) ) ? __( '수신 거부', 'mshop-members-s2' ) : __( '수신 동의', 'mshop-members-s2' ),
                '문자수신동의날짜'  => msm_get_user_agreement_date_params( $user_id, 'mssms' ),
                '이메일수신동의상태' => empty( get_user_meta( $user_id, 'email_agreement', true ) ) ? __( '수신 거부', 'mshop-members-s2' ) : __( '수신 동의', 'mshop-members-s2' ),
                '이메일수신동의날짜' => msm_get_user_agreement_date_params( $user_id, 'email' ),
            ) );
        }

        return $template_params;
    }
    protected static function send_agreement_sms( $user_id ) {
        $message = get_option( 'msm_user_agreement_info_noti_sms' );

        if ( empty( $message ) ) {
            return;
        }

        $template_params = self::get_template_params( $user_id );

        $phone_number = ! empty( get_user_meta( $user_id, 'billing_phone', true ) ) ? get_user_meta( $user_id, 'billing_phone', true ) : get_user_meta( $user_id, 'phone_number', true ) ;

        $recipients = array(
            array(
                'receiver'        => $phone_number,
                'template_params' => $template_params
            )
        );

        $type = MSSMS_SMS::get_sms_type( $message, $template_params );

        MSSMS_SMS::send_sms( $type, '', $message, $recipients );
    }
    protected static function send_agreement_alimtalk( $user_id ) {
        $recipients[] = ! empty( get_user_meta( $user_id, 'billing_phone', true ) ) ? get_user_meta( $user_id, 'billing_phone', true ) : get_user_meta( $user_id, 'phone_number', true );

        $template_code = get_option( 'msm_user_agreement_info_noti_alimtalk' );

        if ( empty( $template_code ) ) {
	        return;
        }

        $template = MSSMS_Kakao::get_template( $template_code );

        if ( empty( $template ) ) {
            throw new Exception( __( '템플릿이 존재하지 않습니다.', 'mshop-members-s2' ) );
        }

        $profile = MSSMS_Kakao::get_profile( $template['plus_id'] );

        if ( 'yes' == mssms_get( $profile, 'is_resend' ) ) {
            $resend_params = array(
                'isResend'     => 'true',
                'resendSendNo' => $profile['resend_send_no']
            );
        } else {
            $resend_params = array( 'isResend' => 'false' );
        }

        $template_params = self::get_template_params( $user_id );

        MSSMS_Kakao::send_alimtalk( $template_code, $recipients, $template_params, $resend_params );
    }
    public static function get_users( $page ) {
        global $wpdb;

        $count_per_loop = 30;

        if ( 0 == $page ) {
            $limit = "LIMIT {$count_per_loop}";
        } else {
            $start = $count_per_loop * $page;

            $limit = "LIMIT {$start}, {$count_per_loop}";
        }

        $email_agreement = 'yes' == get_option( 'msm_user_agreement_use_email', 'no' );
        $mssms_agreement = 'yes' == get_option( 'msm_user_agreement_use_mssms', 'no' );

        if( $email_agreement && $mssms_agreement ) {
            $agreement_query = "AND ( email_meta.meta_value IS NOT NULL AND email_meta.meta_value != '') OR ( mssms_meta.meta_value IS NOT NULL AND mssms_meta.meta_value != '')";
        } else if( $email_agreement && ! $mssms_agreement ) {
            $agreement_query = "AND ( email_meta.meta_value IS NOT NULL AND email_meta.meta_value != '')";
        } else if( ! $email_agreement && $mssms_agreement ) {
            $agreement_query = "AND ( mssms_meta.meta_value IS NOT NULL AND mssms_meta.meta_value != '')";
        }

        $query = " SELECT SQL_CALC_FOUND_ROWS users.ID
				FROM {$wpdb->users} users
				    LEFT JOIN {$wpdb->usermeta} as status_meta ON users.ID = status_meta.user_id AND status_meta.meta_key = 'is_unsubscribed'
				    LEFT JOIN {$wpdb->usermeta} as email_meta ON users.ID = email_meta.user_id AND email_meta.meta_key = 'email_agreement'
				    LEFT JOIN {$wpdb->usermeta} as mssms_meta ON users.ID = mssms_meta.user_id AND mssms_meta.meta_key = 'mssms_agreement'
                WHERE
                      ( status_meta.meta_value IS NULL OR status_meta.meta_value = '0' )
                  {$agreement_query}
				{$limit}";


        return array(
            'users'       => $wpdb->get_results( $query, ARRAY_A ),
            'total_count' => $wpdb->get_var( "SELECT FOUND_ROWS();" ),
        );
    }
    public static function run( $page ) {
        if ( self::enabled() ) {

            set_transient( self::$action, 'yes', DAY_IN_SECONDS );

            $result = self::get_users( $page );

            $send_methods = get_option( 'msm_user_agreement_info_noti_method' );
            $send_methods = explode( ',', $send_methods );

            if ( ! empty( $result['users'] ) ) {
                foreach ( $result['users'] as $user ) {
                    $user = get_userdata( $user['ID'] );

                    if ( in_array( 'email', $send_methods ) ) {
                        MSM_Emails::send_user_agreement_info_email( $user );
                    }

                    if ( class_exists( 'MSSMS_Manager' ) ) {
                        if ( in_array( 'sms', $send_methods ) ) {
                            self::send_agreement_sms( $user->ID );
                        }

                        if ( in_array( 'alimtalk', $send_methods ) ) {
                            self::send_agreement_alimtalk( $user->ID );
                        }
                    }

                }
            }

            self::register_scheduled_action( $page + 1, ! empty( $result['users'] ) );
        }
    }
}

MSM_User_Agreement_Info::init();
