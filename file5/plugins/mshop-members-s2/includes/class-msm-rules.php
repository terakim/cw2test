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

if ( ! class_exists( 'MSM_Rules' ) ) {

	class MSM_Rules {

		public static function rule_conditions( $conditions ) {
			$conditions['msm_is_user_logged_in'] = __( '로그인 사용자', 'mshop-members-s2' );
			$conditions['msm_is_super_admin']    = __( '관리자', 'mshop-members-s2' );
            $conditions['msm_apply_role'] = __( '등급변경 요청중', 'mshop-members-s2' );

			return $conditions;
		}

		public static function check_rule_conditions( $value, $condition ) {
			if ( 'msm_is_user_logged_in' == $condition['condition'] ) {
				return is_user_logged_in() ? 'yes' : 'no';
			} else if ( 'msm_is_super_admin' == $condition['condition'] ) {
				return is_super_admin() ? 'yes' : 'no';
			}

            if( 'msm_apply_role' == $condition['condition'] ) {
                $user_id = get_current_user_id();

                $status = get_user_meta( $user_id, 'role_application_status', true );

                return 'mshop-apply' == $status ? 'yes' : 'no';
            }

			return $value;
		}


		public static function check_conditions( $conditions ) {
			foreach ( $conditions as $condition ) {
				$result = $condition['value'] == apply_filters( 'msm_check_rule_conditions', null, $condition );

				if ( $result ) {
					return false;
				}
			}

			return true;
		}
		public static function msm_check_pre_conditions( $result, $form ) {
			global $pagenow;

			if ( $pagenow == 'post.php' || ( function_exists( 'is_checkout' ) && is_checkout() ) ) {
				return true;
			}

			$user_role = mshop_members_get_user_role();

			$pre_conditions = get_post_meta( $form->id, '_msm_pre_conditions', true );

			if ( is_array( $pre_conditions ) ) {
				foreach ( $pre_conditions as $pre_condition ) {
					if ( empty( $pre_condition['role'] ) || in_array( $user_role, explode( ',', $pre_condition['role'] ) ) ) {
						$conditions = msm_get( $pre_condition, 'conditions', array () );

						if ( empty( $conditions ) ) {
							$result = false;
						} else if ( ! self::check_conditions( $conditions ) ) {
							$result = false;
						}
					}

					if ( ! $result ) {
						$redirect_url = apply_filters( 'msm_pre_condition_redirect_url', $pre_condition['redirect'] );
						if ( ! empty( $_GET['redirect_to'] ) ) {
							$redirect_url = $_GET['redirect_to'];
						}

						if ( ! empty( $redirect_url ) ) {
							?>
                            <script>
                                window.location.href = '<?php echo $redirect_url; ?>';
                            </script>
							<?php
						}
						break;
					}

				}
			}

			return $result;
		}

	}
}

