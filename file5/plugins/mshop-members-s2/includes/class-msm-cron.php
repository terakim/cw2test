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

if ( ! class_exists( 'MSM_Cron' ) ) {

	class MSM_Cron {

		public function __construct() {
			add_action( 'init', array( $this, 'mshop_members_cron_init' ) );

			//탈퇴회원 관련 크론
			add_action( 'mshop_members_unsubscribe_delete_hook', array( $this, 'mshop_members_unsubscribe_delete' ) );

			//휴면회원 관련 크론
			add_action( 'mshop_members_sleep_warning_hook', array( $this, 'send_sleep_notification' ) );
			add_action( 'mshop_members_sleep_process_hook', array( $this, 'do_process_dormant' ) );
			add_action( 'mshop_members_sleep_delete_hook', array( $this, 'mshop_members_sleep_delete' ) );

			add_action( 'mshop_members_login_time_check_hook', array( $this, 'mshop_members_login_time_check' ) );
		}
		protected function customer_has_active_subscriptions( $user_id ) {
			if ( function_exists( 'wcs_get_users_subscriptions' ) ) {
				$subscriptions = wcs_get_users_subscriptions( $user_id );

				foreach ( $subscriptions as $subscription ) {
					if ( 'active' == $subscription->get_status() ) {
						return true;
					}
				}
			}

			return false;
		}
		public function mshop_members_cron_init() {
			if ( MSM_Manager::use_unsubscribe() ) {
				//탈퇴회원 보관데이터 삭제 처리
				if ( ! wp_next_scheduled( 'mshop_members_unsubscribe_delete_hook' ) ) {
					wp_schedule_event( time(), 'daily', 'mshop_members_unsubscribe_delete_hook' );
				}
			}

			if ( MSM_Manager::use_sleep_account() ) {
				//마지막 로그인 시간이 없는 사용자의 경우 현재 시간을 기준으로 로그인 시간을 기록한다.(500명씩)
				if ( get_option( 'mshop_members_last_login_time_check', 'yes' ) == 'yes' ) {
					if ( ! wp_next_scheduled( 'mshop_members_login_time_check_hook' ) ) {
						wp_schedule_event( time(), 'hourly', 'mshop_members_login_time_check_hook' );
					}
				}

				//휴면예고 처리 (이메일 발송이 있어 시간별로 500명씩 끊어서 처리)
				if ( ! wp_next_scheduled( 'mshop_members_sleep_warning_hook' ) ) {
					wp_schedule_event( time(), 'hourly', 'mshop_members_sleep_warning_hook' );
				}

				//휴면전환 처리 (500명씩 끊어서 처리)
				if ( ! wp_next_scheduled( 'mshop_members_sleep_process_hook' ) ) {
					wp_schedule_event( time(), 'hourly', 'mshop_members_sleep_process_hook' );
				}

				//휴면전환된 회원 삭제 처리
				if ( ! wp_next_scheduled( 'mshop_members_sleep_delete_hook' ) ) {
					wp_schedule_event( time(), 'daily', 'mshop_members_sleep_delete_hook' );
				}
			}

		}
		public function mshop_members_login_time_check() {
			if ( get_option( 'mshop_members_last_login_time_check', 'yes' ) == 'yes' ) {
				//마지막 로그인 시간이 없는 사용자의 리스트를 가져온다.
				$args = array(
					'blog_id'      => $GLOBALS['blog_id'],
					'role'         => '',
					'meta_key'     => 'last_login_time',
					'meta_value'   => '',
					'meta_compare' => 'NOT EXISTS',
					'meta_query'   => array(),
					'date_query'   => array(),
					'include'      => array(),
					'exclude'      => array(),
					'orderby'      => 'ID',
					'order'        => 'ASC',
					'offset'       => '',
					'search'       => '',
					'number'       => '50',    //50명씩 값을 가져와서 처리
					'count_total'  => false,
					'fields'       => 'ID',
					'who'          => ''
				);

				$users = get_users( $args );

				if ( count( $users ) > 0 ) {
					foreach ( $users as $user ) {
						//메타값이 유니크한 메타가 아니면 추가하지 않음.
						add_user_meta( $user, 'last_login_time', current_time( 'mysql' ), true );
					}
				} else if ( count( $users ) == 0 ) {
					//만약 조건에 해당하는 사용자가 0명인경우 현재 크론은 동작하지 않도록 수정
					update_option( 'mshop_members_last_login_time_check', 'no' );
				}
			}
		}
		public function mshop_members_unsubscribe_delete() {
			//탈퇴 처리된 회원중에 로그인한 날짜가 마지막으로부터 일정 기간이 지났는지 확인하여 삭제 처리
			//단, 로그인 시간이 없는 경우에는 바로 삭제 처리를 진행하도록 합니다.

			if ( ! MSM_Manager::use_unsubscribe() ) {
				return;
			}

			require_once( ABSPATH . 'wp-admin/includes/user.php' );

			$process_type = get_option( 'mshop_members_unsubscribe_after_process', 'none' );
			$wait_day     = get_option( 'mshop_members_unsubscribe_auto_delete_wait_day', '' );

			if ( $process_type == 'none' ) {
				if ( ! empty( $wait_day ) && ( (int) $wait_day > 0 ) ) {

					$args = array(
						'blog_id'      => $GLOBALS['blog_id'],
						'role'         => '',
						'meta_key'     => 'is_unsubscribed',
						'meta_value'   => '1',
						'meta_compare' => '=',
						'meta_query'   => array(),
						'date_query'   => array(),
						'include'      => array(),
						'exclude'      => array(),
						'orderby'      => 'ID',
						'order'        => 'ASC',
						'offset'       => '',
						'search'       => '',
						'number'       => '',
						'count_total'  => false,
						'fields'       => 'all',
						'who'          => ''
					);

					$users = get_users( $args );

					if ( count( $users ) > 0 ) {

						foreach ( $users as $no ) {

							//사용자 권한 체크
							$user = new WP_User( $no );
							if ( ! empty( $user->roles ) && is_array( $user->roles ) ) {

								foreach ( $user->roles as $role ) {
									if ( $role != 'administrator' ) {
										//관리자가 아닌 경우 동작
										$unsubscribed_time = get_user_meta( $no->ID, 'unsubscribed_time', true );

										if ( empty( $unsubscribed_time ) ) {
											//탈퇴 시간 기록이 없는 경우(바로 삭제)
											wp_delete_user( $no->ID );
										} else {
											//탈퇴 시간 기록이 있는 경우(시간 비교후, 기간이 지난 이후에는 바로 삭제)
											$exit_date    = new DateTime( $unsubscribed_time );
											$current_date = new DateTime( current_time( 'mysql' ) );
											$diff_day     = $exit_date->diff( $current_date )->format( "%d" );

											if ( (int) $diff_day > (int) $wait_day ) {
												//날짜가 지난 이후이므로 삭제 처리
												wp_delete_user( $no->ID );
											}
										}

									}
								} //end foreach

							} //end if

						}//end foreach

					}
				}
			}
		}
		protected function get_phone_number( $user ) {
			$phone_number = get_user_meta( $user->ID, 'billing_phone', true );

			if ( empty( $phone_number ) ) {
				$phone_number = get_user_meta( $user->ID, 'phone_number', true );
			}

			return $phone_number;
		}
		protected function get_template_params( $user, $warning_day ) {
			return array(
				'쇼핑몰명'      => wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ),
				'고객명'       => $user->display_name,
				'아이디'       => $user->user_login,
				'휴면회원삭제대기일' => $warning_day
			);
		}
		protected function send_sleep_notification_via_sms( $user, $warning_day ) {
			try {
				$message = get_option( 'mshop_members_sleep_notification_sms_template' );

				$phone_number = $this->get_phone_number( $user );

				if ( ! empty( $message ) && ! empty( $phone_number ) ) {
					$template_params = $this->get_template_params( $user, $warning_day );

					$recipients = array(
						array(
							'receiver'        => $phone_number,
							'template_params' => $template_params
						)
					);

					MSSMS_SMS::send_sms( 'LMS', '', $message, $recipients, get_option( 'mssms_rep_send_no' ) );
				}
			} catch ( Exception $e ) {

			}
		}
		protected function send_sleep_notification_via_alimtalk( $user, $warning_day ) {
			try {
				$phone_number = $this->get_phone_number( $user );

				$template_code = get_option( 'mssms_phone_certification_alimtalk_template' );

				if ( empty( $phone_number ) || empty( $template_code ) ) {
					return;
				}

				$template = MSSMS_Kakao::get_template( $template_code );

				if ( empty( $template ) ) {
					return;
				}

				$recipients[] = $phone_number;

				$profile = MSSMS_Kakao::get_profile( $template['plus_id'] );

				if ( 'yes' == mssms_get( $profile, 'is_resend' ) ) {
					$resend_params = array(
						'isResend'     => 'true',
						'resendSendNo' => $profile['resend_send_no']
					);
				} else {
					$resend_params = array( 'isResend' => 'false' );
				}

				$template_params = $this->get_template_params( $user, $warning_day );

				MSSMS_Kakao::send_alimtalk( $template_code, $recipients, $template_params, $resend_params, true );
			} catch ( Exception $e ) {

			}
		}
		protected function send_sleep_notification_via_email( $user, $warning_day ) {
			try {
				$email_contents = get_option( 'mshop_members_sleep_warning_email' );
				$email_contents = $this->replace_string_email( $email_contents, $user );
				$email_title    = get_option( 'mshop_members_sleep_warning_email_title', '휴면 전환 예고입니다.' );

				wp_mail( $user->data->user_email, $email_title, $email_contents );
			} catch ( Exception $e ) {

			}
		}
		public function send_sleep_notification() {
			if ( ! MSM_Manager::use_sleep_account() ) {
				return;
			}

			$notification_methods = explode( ',', get_option( 'mshop_members_sleep_notification_method', 'email' ) );

			$warning_day    = get_option( 'mshop_members_sleep_warning_day', '' );
			$sleep_wait_day = get_option( 'mshop_members_sleep_wait_day', '' );

			//옵션값 확인(각옵션값은 빈값이거나 0이 아니어야함. 휴면처리일이 휴면예고일보다 커야함.
			if ( ! empty( $warning_day ) && ! empty( $sleep_wait_day ) && ( $sleep_wait_day > $warning_day ) ) {

				$diff_day     = ( $sleep_wait_day - $warning_day );
				$current_date = new DateTime( current_time( 'mysql' ) );
				$current_date->modify( "-{$diff_day} day" );
				$search_date = $current_date->format( 'Y-m-d H:i:s' );

				// 마지막 로그인 시간을 비교하여 로그인 시간이 오래된 사용자 중에서 메일 발송이 안된 사용자를 확인
				$args = array(
					'blog_id'    => $GLOBALS['blog_id'],
					'meta_query' => array(
						'relation' => 'AND',
						array(
							array(
								'key'     => 'is_unsubscribed', // 탈퇴하지 않은 회원이어야 한다.
								'compare' => 'NOT EXISTS'
							),
							array(
								'key'     => 'last_login_time', // 로그인 시간이 존재해야한다.
								'compare' => 'EXISTS'
							),
							array(
								'key'     => 'last_login_time', // 메타값이 지정된 기간보다 이전인 사용자를 가져온다.
								'value'   => $search_date,
								'compare' => '<',
								'type'    => 'DATETIME',
							),
							array(
								'key'     => 'mshop_members_sleep_warning_mail_sent',   // 휴면예고 이메일을 발송한적이 없어야 한다
								'compare' => 'NOT EXISTS'
							)
						)
					),
					'number'     => '50',
				);

				$users = new WP_User_Query( $args );

				if ( count( $users->get_results() ) > 0 ) {

					foreach ( $users->get_results() as $user ) {
						//사용자 권한 체크. 관리자 권한인 경우 휴면 처리 하지 않음.
						if ( ! empty( $user->roles ) && is_array( $user->roles ) && ! in_array( 'administrator', $user->roles ) ) {

							if ( $this->customer_has_active_subscriptions( $user->ID ) ) {
								update_user_meta( $user->ID, 'last_login_time', current_time( 'mysql' ) );
							} else {
								if ( in_array( 'email', $notification_methods ) ) {
									$this->send_sleep_notification_via_email( $user, $warning_day );
								}
								if ( in_array( 'sms', $notification_methods ) ) {
									$this->send_sleep_notification_via_sms( $user, $warning_day );
								}
								if ( in_array( 'alimtalk', $notification_methods ) ) {
									$this->send_sleep_notification_via_alimtalk( $user, $warning_day );
								}

								update_user_meta( $user->ID, 'mshop_members_sleep_warning_mail_sent', true );

								do_action( 'msm_send_sleeper_account_warning_email', $user );
							}
						}
					}
				}
			}
		}
		public function do_process_dormant() {
			// 휴면전환 처리 일자가 지난 이후의 사용자라면 휴면회원으로 전환 한다.
			if ( ! MSM_Manager::use_sleep_account() ) {
				return;
			}

			$sleep_wait_day = get_option( 'mshop_members_sleep_wait_day', '' );

			//옵션값 확인(각옵션값은 빈값이거나 0이 아니어야함. 휴면처리일이 휴면예고일보다 커야함.
			if ( ! empty( $sleep_wait_day ) ) {

				$current_date = new DateTime( current_time( 'mysql' ) );
				$current_date->modify( "-{$sleep_wait_day} day" );
				$search_date = $current_date->format( 'Y-m-d H:i:s' );

				// 마지막 로그인 시간을 비교하여 로그인 시간이 오래된 사용자 중에서 메일 발송이 안된 사용자를 확인
				$args = array(
					'blog_id'    => $GLOBALS['blog_id'],
					'meta_query' => array(
						'relation' => 'AND',
						array(
							array(
								'key'     => 'is_unsubscribed', // 탈퇴하지 않은 회원이어야 한다.
								'compare' => 'NOT EXISTS'
							),
							array(
								'key'     => 'last_login_time', // 로그인 시간이 존재해야한다.
								'compare' => 'EXISTS'
							),
							array(
								'key'     => 'last_login_time', // 메타값이 지정된 기간보다 이전인 사용자를 가져온다.
								'value'   => $search_date,
								'compare' => '<',
								'type'    => 'DATETIME',
							),
							array(
								'key'     => 'mshop_members_sleep_warning_mail_sent',   // 휴면예고 이메일을 발송한적이 있어야 한다
								'compare' => 'EXISTS'
							)
						)
					),
					'number'     => '50',
				);

				$users = new WP_User_Query( $args );

				if ( count( $users->get_results() ) > 0 ) {
					foreach ( $users->get_results() as $user ) {
						if ( ! empty( $user->roles ) && is_array( $user->roles ) && ! in_array( 'administrator', $user->roles ) ) {
							update_user_meta( $user->ID, 'is_unsubscribed', '2' );

							do_action( 'msm_process_sleeper_account', $user );
						}
					}
				}
			}
		}

		//휴면처리된 이후 기간이 지난 회원 삭제 처리
		public function mshop_members_sleep_delete() {
			// 휴면회원 중에 휴면전환 처리 일자와 휴면처리후 삭제일자를 합친 기간보다 오래된 사용자를 삭제처리한다.

			if ( MSM_Manager::use_sleep_account() ) {
				return;
			}

			require_once( ABSPATH . 'wp-admin/includes/user.php' );

			$sleep_wait_day  = get_option( 'mshop_members_sleep_wait_day', '' );
			$delete_wait_day = get_option( 'mshop_members_sleep_auto_delete_wait_day', '' );
			//휴면처리일과 휴면처리후 삭제대기일을 합친 기간계산
			$total_delete_wait_day = ( $sleep_wait_day + $delete_wait_day );

			if ( ! empty( $sleep_wait_day ) && ! empty( $delete_wait_day ) && ! empty( $total_delete_wait_day ) ) {

				$current_date = new DateTime( current_time( 'mysql' ) );
				$current_date->modify( "-{$total_delete_wait_day} day" );
				$search_date = $current_date->format( 'Y-m-d H:i:s' );

				// 마지막 로그인 시간을 비교하여 로그인 시간이 오래된 사용자 중에서 메일 발송이 안된 사용자를 확인
				$args = array(
					'blog_id'    => $GLOBALS['blog_id'],
					'meta_query' => array(
						'relation' => 'AND',
						array(
							array(
								'key'     => 'is_unsubscribed', // 탈퇴 또는 휴면 처리가 된 회원이어야 한다.
								'compare' => 'EXISTS'
							),
							array(
								'key'     => 'is_unsubscribed', // 휴면전환 처리가 된 회원이어야 한다.
								'value'   => '2',
								'compare' => '=',
							),
							array(
								'key'     => 'last_login_time', // 로그인 시간이 존재해야한다.
								'compare' => 'EXISTS'
							),
							array(
								'key'     => 'last_login_time', // 메타값이 지정된 기간보다 이전인 사용자를 가져온다.
								'value'   => $search_date,
								'compare' => '<',
								'type'    => 'DATETIME',
							),
							array(
								'key'     => 'mshop_members_sleep_warning_mail_sent',   // 휴면예고 이메일을 발송한적이 있어야 한다
								'compare' => 'EXISTS'
							),
							array(
								'key'     => 'mshop_members_sleep_warning_mail_sent',   // 휴면예고 이메일을 발송한적이 있어야 한다
								'value'   => '1',
								'compare' => '='
							)
						)
					),
					'number'     => '50',
				);

				$users = new WP_User_Query( $args );

				if ( count( $users->get_results() ) > 0 ) {
					foreach ( $users->get_results() as $user ) {
						if ( ! empty( $user->roles ) && is_array( $user->roles ) && ! in_array( 'administrator', $user->roles ) ) {
							do_action( 'msm_delete_sleeper_account', $user );
							wp_delete_user( $user->ID );

						}
					}
				}
			}
		}

		//이메일 내용에 포함되어 있는 문구들을 사용자 정보로 대체하여 발송하도록 처리
		//예약어에 대한 안내 필요.
		public function replace_string_email( $email_contents, $user ) {

			//고객명
			$username = get_user_meta( $user->ID, 'billing_last_name', true ) . get_user_meta( $user->ID, 'billing_first_name', true );
			if ( empty( $username ) ) {
				$username = $user->user_email;
			}

			$email_contents = str_replace( __( "{고객명}", 'mshop-members-s2' ), $username, $email_contents );

			//쇼핑몰명
			if ( is_plugin_active( 'mshop-mcommerce-premium/mshop-mcommerce-premium.php' ) ) {
				$mall_name = get_option( "mall_name", __( "코드엠샵", "mshop-members-s2" ) );
			} else {
				$mall_name = get_option( "blogname", __( "코드엠샵", "mshop-members-s2" ) );
			}
			$email_contents = str_replace( __( "{쇼핑몰명}", 'mshop-members-s2' ), $mall_name, $email_contents );

			//휴면회원삭제대기일
			$wait_day       = get_option( 'mshop_members_sleep_auto_delete_wait_day', '' );
			$email_contents = str_replace( __( "{휴면회원삭제대기일}", 'mshop-members-s2' ), $wait_day, $email_contents );

			return $email_contents;
		}

	} // Class End

	return new MSM_Cron();
} // If End

