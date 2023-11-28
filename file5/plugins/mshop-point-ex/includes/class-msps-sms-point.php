<?php

/*
=====================================================================================
                엠샵 프리미엄 포인트 / Copyright 2014-2015 by CodeM(c)
=====================================================================================

  [ 우커머스 버전 지원 안내 ]

   워드프레스 버전 : WordPress 4.3.1 이상

   우커머스 버전 : WooCommerce 3.0 이상


  [ 코드엠 플러그인 라이센스 규정 ]

   (주)코드엠에서 개발된 워드프레스  플러그인을 사용하시는 분들에게는 다음 사항에 대한 동의가 있는 것으로 간주합니다.

   1. 코드엠에서 개발한 워드프레스 우커머스용 엠샵 프리미엄 포인트 플러그인의 저작권은 (주)코드엠에게 있습니다.
   
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

if ( ! class_exists( 'MSPS_SMS_Point' ) ) {

	class MSPS_SMS_Point {
		protected static $sms_point_rules = null;
		public static function init() {
		}
		public static function enabled() {
			return 'yes' == get_option( 'msps_use_sms_point_rule', 'no' );
		}
		public static function get_sms_point_rules() {
			if ( is_null( self::$sms_point_rules ) ) {
				$rules = get_option( 'msps_sms_point_rule', array() );

				self::$sms_point_rules = array_combine( array_column( $rules, 'role' ), $rules );
			}

			return self::$sms_point_rules;
		}
		public static function get_sms_point_rule( $user_role ) {
			$rules = self::get_sms_point_rules();

			return msps_get( $rules, $user_role, array() );
		}
		public static function maybe_apply_sms_point_option( $point_option, $user_role, $user_id ) {
			if ( self::enabled() && 'on' == get_user_meta( $user_id, 'mssms_agreement', true ) ) {
				$rule = self::get_sms_point_rule( $user_role );

				if ( ! empty( $rule ) ) {
					$point_option['ratio'] = floatval( $point_option['ratio'] ) + floatval( $rule['ratio'] );
					$point_option['fixed'] = floatval( $point_option['fixed'] ) + floatval( $rule['fixed'] );
				}
			}

			return $point_option;
		}
	}

	MSPS_SMS_Point::init();
}

