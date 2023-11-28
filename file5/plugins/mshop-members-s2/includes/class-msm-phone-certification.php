<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MSM_Phone_Certification' ) ) {

	class MSM_Phone_Certification {
		static $customer_id = 0;

		static $role_params = null;

		public static function init() {
			if ( 'yes' == get_option( 'mssms_use_phone_certification', 'no' ) ) {
				add_action( 'msm_submit_action', array( __CLASS__, 'add_phone_authentication_action' ) );
				add_action( 'msm_action_phone_authentication', array( __CLASS__, 'process_authentication' ), 10, 2 );

				if ( 'yes' == get_option( 'mssms_use_phone_certification_for_guest', 'no' ) ) {
					add_filter( 'woocommerce_checkout_get_value', array( __CLASS__, 'maybe_set_phone_field_value' ), 10, 2 );
					add_filter( 'woocommerce_form_field_args', array( __CLASS__, 'maybe_change_form_field_args' ), 10, 3 );
					add_action( 'msm_submit', array( __CLASS__, 'maybe_save_certified_phone_number' ), 10, 2 );
				}
			}
		}
		public static function add_phone_authentication_action( $actions ) {
			$actions['msm_action_phone_authentication'] = __( '휴대폰인증', 'mshop-members-s2' );

			return $actions;
		}
		protected static function get_certification_number() {
			return rand( 100000, 999999 );
		}
		public static function get_certification_hash( $certification_number, $salt ) {
			return md5( $certification_number . $salt );
		}
		protected static function get_certification_method() {
			return get_option( 'mssms_phone_certification_method', 'alimtalk' );
		}
		protected static function get_template_params( $certification_number ) {
			$template_params = array(
				'쇼핑몰명' => wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ),
				'인증번호' => $certification_number
			);

			if ( is_user_logged_in() ) {
				$user = get_userdata( get_current_user_id() );

				$template_params = array_merge( $template_params, array(
					'고객명' => $user->display_name,
					'아이디' => $user->user_login
				) );
			}

			return $template_params;
		}
		protected static function send_certification_number_via_sms( $phone_number, $certification_number ) {
			$message = get_option( 'mssms_phone_certification_sms_template' );

			$template_params = self::get_template_params( $certification_number );

			$recipients = array(
				array(
					'receiver'        => $phone_number,
					'template_params' => $template_params
				)
			);

			MSSMS_SMS::send_sms( 'SMS', '', $message, $recipients, get_option( 'mssms_rep_send_no' ), '', true );
		}
		protected static function send_certification_number_via_alimtalk( $phone_number, $certification_number ) {
			$recipients[] = $phone_number;

			$template_code = get_option( 'mssms_phone_certification_alimtalk_template' );

			if ( empty( $template_code ) ) {
				throw new Exception( __( '알림톡 템플릿 등록 후 이용해주세요', 'mshop-members-s2' ) );
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

			$template_params = self::get_template_params( $certification_number );

			MSSMS_Kakao::send_alimtalk( $template_code, $recipients, $template_params, $resend_params, true );
		}
		public static function send_certification_number( $phone_number, $find_login, $allow_duplicate, $temporary_password, $user_login, $form_slug ) {
			$phone_number = apply_filters( 'msm_phone_number_to_certificate', $phone_number );

			if ( 'yes' != get_option( 'mssms_use_phone_certification', 'no' ) ) {
				throw new Exception( __( '휴대폰 인증 기능을 활성화 해주세요.', 'mshop-members-s2' ) );
			}

			if ( empty( $phone_number ) ) {
				throw new Exception( __( '휴대폰 번호가 누락되었습니다.', 'mshop-members-s2' ) );
			}

			if ( ! class_exists( 'MSSMS_SMS' ) ) {
				throw new Exception( __( '휴대폰 인증 기능을 이용하려면 엠샵 문자 알림 플러그인이 필요합니다.', 'mshop-members-s2' ) );
			}

			if ( 'yes' == $find_login || 'yes' == $temporary_password ) {
				if ( 'yes' == get_option( 'mssms_phone_certification_required', 'no' ) ) {
					$users = MSM_Action_Find_Login::get_users( $phone_number, array( 'mshop_auth_phone' ) );
				}
				if ( empty( $users ) ) {
					$users = MSM_Action_Find_Login::get_users( $phone_number );
				}

				if ( empty( $users ) ) {
					throw new Exception( __( '가입된 회원 정보가 없습니다.', 'mshop-members-s2' ) );
				}

				if ( 'yes' == $temporary_password ) {
					$user_exist = false;

					foreach ( $users as $user ) {
						if ( $user->user_login == $user_login || $user->user_email == $user_login ) {
							$user_exist = true;
							break;
						}
					}

					if ( ! $user_exist ) {
						throw new Exception( __( '일치하는 회원 정보가 없습니다.', 'mshop-members-s2' ) );
					}
				}

			} else if ( 'yes' == get_option( 'mssms_phone_certification_restrict_duplicate', 'no' ) && 'yes' != $allow_duplicate ) {
				if ( 'yes' == get_option( 'mssms_phone_certification_required', 'no' ) ) {
					$users = MSM_Action_Find_Login::get_users( $phone_number, array( 'mshop_auth_phone' ) );
				} else {
					$users = MSM_Action_Find_Login::get_users( $phone_number );
				}

				if ( ! empty( $users ) ) {
					if ( is_user_logged_in() ) {
						throw new Exception( __( '이미 등록(인증)된 휴대폰 번호 입니다.', 'mshop-members-s2' ) );
					} else {
						throw new Exception( __( '이미 등록(인증)된 휴대폰 번호 입니다. 로그인 후 이용해주세요.', 'mshop-members-s2' ) );
					}
				}
			}

			delete_transient( 'msm_phone_certification_' . preg_replace( '~\D~', '', $phone_number ) );
			delete_transient( 'msm_phone_certification_salt_' . preg_replace( '~\D~', '', $phone_number ) );
			delete_transient( 'msm_phone_certification_retry_' . preg_replace( '~\D~', '', $phone_number ) );

			do_action( 'msm_before_send_certification_number', $phone_number, $find_login, $allow_duplicate, $form_slug );

			$certification_number = self::get_certification_number();

			if ( 'sms' == self::get_certification_method() ) {
				self::send_certification_number_via_sms( $phone_number, $certification_number );
			} else {
				self::send_certification_number_via_alimtalk( $phone_number, $certification_number );
			}

			$salt = bin2hex( random_bytes( 10 ) );
			$hash = self::get_certification_hash( $certification_number, $salt );

			$expiration = apply_filters( 'msm_phone_certification_expiration', 10 * MINUTE_IN_SECONDS );

			set_transient( 'msm_phone_certification_' . preg_replace( '~\D~', '', $phone_number ), $hash, $expiration );
			set_transient( 'msm_phone_certification_salt_' . preg_replace( '~\D~', '', $phone_number ), $salt, $expiration);
			set_transient( 'msm_phone_certification_retry_' . preg_replace( '~\D~', '', $phone_number ), 0, $expiration );

			do_action( 'msm_after_send_certification_number', $phone_number, $find_login, $allow_duplicate, $form_slug, $hash );

			return $hash;
		}
		public static function validate_certification_number( $phone_number, $certificate_hash, $certification_number, $form_slug ) {
			$phone_number = preg_replace( '~\D~', '', $phone_number );
			$saved_hash   = get_transient( 'msm_phone_certification_' . $phone_number );
			$saved_salt   = get_transient( 'msm_phone_certification_salt_' . $phone_number );
			$retry_count  = intval( get_transient( 'msm_phone_certification_retry_' . $phone_number ) );

			if ( $retry_count >= 5 ) {
				throw new Exception( __( '인증 가능 횟수가 초과되었습니다.', 'mshop-members-s2' ), '2001' );
			} else {
				$expiration = apply_filters( 'msm_phone_certification_expiration', 10 * MINUTE_IN_SECONDS );

				set_transient( 'msm_phone_certification_retry_' . preg_replace( '~\D~', '', $phone_number ), $retry_count + 1, $expiration );
			}

			if ( empty( $saved_hash ) || empty( $saved_salt ) ) {
				throw new Exception( __( '인증 정보가 존재하지 않습니다.', 'mshop-members-s2' ), '2002' );
			}

			if ( $saved_hash != $certificate_hash ) {
				throw new Exception( __( '인증 정보가 올바르지 않습니다.', 'mshop-members-s2' ), '2003' );
			}

			if ( $saved_hash != MSM_Phone_Certification::get_certification_hash( $certification_number, $saved_salt ) ) {
				throw new Exception( sprintf( __( '올바르지 않은 인증번호입니다. 남은 시도횟수 : %d', 'mshop-members-s2' ), 5 - ( $retry_count + 1 ) ), '2004' );
			}

			return true;
		}
		public static function save_user_info( $user_id, $new_customer_data, $params ) {
			self::$customer_id = $user_id;
		}
		static function get_user_id() {
			if ( 0 == self::$customer_id ) {
				self::$customer_id = get_current_user_id();
			}

			return self::$customer_id;
		}
		public static function register_post_actions( $actions ) {
			$actions['send_sms']      = __( '문자발송', 'mshop-members-s2' );
			$actions['send_alimtalk'] = __( '알림톡발송', 'mshop-members-s2' );

			return $actions;
		}
		public static function register_post_actions_settings( $settings ) {
			$settings['send_sms'] = array(
				'type'              => 'Section',
				"hideSectionHeader" => true,
				'elements'          => array(
					array(
						"id"          => "title",
						"title"       => "문자제목",
						"className"   => "fluid",
						"placeholder" => "고객에게 발송할 문자 제목을 입력해주세요.",
						"type"        => "Text"
					),
					array(
						"id"          => "message",
						"title"       => "문자내용",
						"className"   => "",
						"placeholder" => "고객에게 발송할 문자 내용을 입력해주세요.",
						"type"        => "TextArea"
					)
				)
			);

			$settings['send_alimtalk'] = array(
				'type'              => 'Section',
				"hideSectionHeader" => true,
				'elements'          => array(
					array(
						"id"          => "template_code",
						"title"       => __( "알림톡 템플릿", 'mshop-members-s2' ),
						"className"   => "fluid",
						"type"        => "Select",
						"placeholder" => "알림톡 템플릿을 선택하세요.",
						"options"     => MSSMS_Settings_Alimtalk_Send::get_templates()
					),
					array(
						"id"        => "resend_method",
						"title"     => __( "문자 대체 발송", 'mshop-members-s2' ),
						"className" => "fluid",
						"type"      => "Select",
						"default"   => "none",
						"options"   => array(
							'none'     => __( "사용안함", 'mshop-members-s2' ),
							'alimtalk' => __( "알림톡 내용전달", 'mshop-members-s2' )
						)
					),
				)
			);

			return $settings;
		}
		static function send_sms( $response, $form, $action, $params ) {
			if ( self::get_user_id() > 0 && ! empty( $action['message'] ) ) {
				$phone_number = get_user_meta( self::get_user_id(), 'billing_phone', true );

				if ( ! empty( $phone_number ) ) {
					$user = get_userdata( self::get_user_id() );

					$recipients = array(
						array(
							'receiver'        => $phone_number,
							'template_params' => array(
								'쇼핑몰명' => wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ),
								'고객명'  => $user->display_name,
								'아이디'  => $user->user_login
							)
						)
					);

					MSSMS_SMS::send_sms( 'LMS', $action['title'], stripslashes( $action['message'] ), $recipients, get_option( 'mssms_rep_send_no' ) );
				}
			}

			return $response;
		}
		static function send_alimtalk( $response, $form, $action, $params ) {
			if ( self::get_user_id() > 0 && ! empty( $action['template_code'] ) ) {
				$phone_number = get_user_meta( self::get_user_id(), 'billing_phone', true );

				if ( ! empty( $phone_number ) ) {
					$recipients[] = $phone_number;

					$resend_params = array(
						'isResend' => 'false'
					);

					$user = get_userdata( self::get_user_id() );

					$template_code = $action['template_code'];
					$template      = MSSMS_Kakao::get_template( $action['template_code'] );


					$profile = MSSMS_Kakao::get_profile( $template['plus_id'] );
					if ( 'alimtalk' == $action['resend_method'] ) {
						$resend_params = array(
							'isResend'     => 'true',
							'resendSendNo' => $profile['resend_send_no']
						);
					}

					$template_params = array(
						'쇼핑몰명' => wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ),
						'고객명'  => $user->display_name,
						'아이디'  => $user->user_login
					);

					MSSMS_Kakao::send_alimtalk( $template_code, $recipients, $template_params, $resend_params );
				}
			}

			return $response;
		}
		public static function process_authentication( $params, $form ) {
			$phone_field = $form->get_field( array( 'MFD_Phone_Field' ) );

			if ( ! empty( $phone_field ) ) {
				$phone_field = reset( $phone_field );

				update_user_meta( get_current_user_id(), 'mshop_auth_method', 'mshop-sms' );
				update_user_meta( get_current_user_id(), 'mshop_auth_phone', $params[ $phone_field->get_name() ] );
			}
		}
		public static function maybe_save_certified_phone_number( $params, $form ) {
			if ( ! is_user_logged_in() ) {
				$phone_field = $form->get_field( array( 'MFD_Phone_Field' ) );

				if ( ! empty( $phone_field ) ) {
					$expiration = apply_filters( 'msm_phone_certification_expiration', 10 * MINUTE_IN_SECONDS );
					$phone_field = reset( $phone_field );

					set_transient( msm_get_state() . '-msm_auth_phone_number', $params[ $phone_field->get_name() ], $expiration );
				}
			}
		}
		public static function maybe_set_phone_field_value( $value, $input ) {
			if ( ! is_user_logged_in() && str_starts_with( $input, 'billing_phone' ) ) {
				$value = get_transient( msm_get_state() . '-msm_auth_phone_number' );
			}

			return $value;
		}
		public static function maybe_change_form_field_args( $args, $key, $value ) {
			if ( ! is_user_logged_in() && 0 === strpos( $key, 'billing_phone' ) && ! empty( get_transient( msm_get_state() . '-msm_auth_phone_number' ) ) ) {
				$args['custom_attributes']['readonly'] = 'true';
			}

			return $args;
		}
	}

	MSM_Phone_Certification::init();
}
