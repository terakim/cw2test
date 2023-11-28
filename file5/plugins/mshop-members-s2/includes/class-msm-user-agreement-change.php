<?php

defined( 'ABSPATH' ) || exit;
class MSM_User_Agreement_Change {

    protected static $send = false;
	public static function init() {
		add_action( 'msm_before_submit_form', array( __CLASS__, 'maybe_user_agreement_date_profile_update' ), 10, 2 );
		add_action( 'msm_after_submit_form', array( __CLASS__, 'maybe_user_agreement_send' ), 10, 2 );
		add_action( 'msm_user_registered', array( __CLASS__, 'register_user_agreement_date_update' ), 10 );
	}
	static function enabled() {
		return 'yes' == get_option( 'msm_user_agreement_change_noti_enable', 'no' );
	}
	public static function maybe_user_agreement_date_profile_update( $params, $form ) {
		if ( 'msm_action_edit_user_profile' == $form->get_submit_action() ) {

			$fields      = $form->get_fields();
			$field_names = array_map( function ( $field ) {
				return msm_get( $field->property, 'name' );
			}, $fields );

            $user_id      = get_current_user_id();
            $current_date = date( 'Y-m-d H:i:s', strtotime( current_time( 'mysql' ) ) );
            $agreements   = array ( 'email', 'mssms' );

			foreach ( $agreements as $agreement ) {
				if ( in_array( $agreement . '_agreement', $field_names ) ) {
					if ( $user_id ) {
						if ( $params[ $agreement . '_agreement' ] != get_user_meta( $user_id, $agreement . '_agreement', true ) ) {
                            update_user_meta( $user_id, $agreement . '_update_date', $current_date );
                            self::$send = true;
						} else {
                            if ( empty( get_user_meta( $user_id, $agreement . '_update_date', true ) ) ) {
                                update_user_meta( $user_id, $agreement . '_update_date', $current_date );
                            }
						}
					}
				}
			}
		}
	}
	public static function register_user_agreement_date_update( $user_id ) {
		$current_date = date( 'Y-m-d H:i:s', strtotime( current_time( 'mysql' ) ) );
		$agreements   = array( 'email', 'mssms' );

		foreach ( $agreements as $agreement ) {
            if ( is_array( get_user_meta( $user_id, $agreement . '_agreement' ) ) ) {
                update_user_meta( $user_id, $agreement . '_update_date', $current_date );
                self::$send = true;
            }
		}
	}
	public static function maybe_user_agreement_send( $params, $form ) {
		if ( 'msm_action_edit_user_profile' == $form->get_submit_action() || 'msm_action_register' == $form->get_submit_action() ) {
			$send_methods = get_option( 'msm_user_agreement_change_noti_method' );
			$send_methods = explode( ',', $send_methods );
			$user_id      = get_current_user_id();

			if ( self::$send && self::enabled() ) {
				if ( in_array( 'email', $send_methods ) ) {
					MSM_Emails::send_user_agreement_change_email( $user_id );
				}

				if ( class_exists( 'MSSMS_Manager' ) ) {
					if ( in_array( 'sms', $send_methods ) ) {
						self::send_agreement_sms( $user_id );
					}

					if ( in_array( 'alimtalk', $send_methods ) ) {
						self::send_agreement_alimtalk( $user_id );
					}
				}
			}

		}
	}
	protected static function get_template_params( $user_id ) {
		$template_params = array(
			'쇼핑몰명' => wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ),
		);

		if ( is_user_logged_in() ) {
			$user = get_userdata( $user_id );

            $send_date = __( 'Y년 m월 d일', 'mshop-members-s2' );
			$template_params = array_merge( $template_params, array(
				'고객명'       => $user->display_name,
				'아이디'       => $user->user_login,
                '발송일'       => date( $send_date, strtotime( current_time( 'mysql' ) ) ),
				'문자수신동의상태'  => empty( get_user_meta( $user_id, 'mssms_agreement', true ) ) ? __( '수신 거부', 'mshop-members-s2' ) : __( '수신 동의', 'mshop-members-s2' ),
				'문자수신동의날짜'  => msm_get_user_agreement_date_params( $user_id, 'mssms' ),
				'이메일수신동의상태' => empty( get_user_meta( $user_id, 'email_agreement', true ) ) ? __( '수신 거부', 'mshop-members-s2' ) : __( '수신 동의', 'mshop-members-s2' ),
				'이메일수신동의날짜' => msm_get_user_agreement_date_params( $user_id, 'email' ),
			) );
		}

		return $template_params;
	}
	protected static function send_agreement_sms( $user_id ) {
		$message = get_option( 'msm_user_agreement_change_noti_sms' );

		if ( empty( $message ) ) {
			return;
		}

		$template_params = self::get_template_params( $user_id );

		$phone_number = ! empty( get_user_meta( $user_id, 'billing_phone', true ) ) ? get_user_meta( $user_id, 'billing_phone', true ) : get_user_meta( $user_id, 'phone_number', true );

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

		$template_code = get_option( 'msm_user_agreement_change_noti_alimtalk' );

		if ( empty( $template_code ) ) {
			return;
		}

		$template = MSSMS_Kakao::get_template( $template_code );

		if ( empty( $template ) ) {
            return;
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

}

MSM_User_Agreement_Change::init();
