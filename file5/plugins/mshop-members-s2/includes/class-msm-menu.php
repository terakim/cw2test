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

if ( ! class_exists( 'MSM_Menu' ) ) {

	class MSM_Menu {
		public static function wp_nav_menu_objects( $sorted_menu_items, $args ) {

			$menu_items = array();

			foreach ( $sorted_menu_items as $menu_item ) {
				$show = true;

				if ( in_array( 'mshop_show_if_login', $menu_item->classes ) ) {
					$show = is_user_logged_in();
				} else if ( in_array( 'mshop_show_if_logout', $menu_item->classes ) ) {
					$show = ! is_user_logged_in();
				}

				if ( $show ) {
					switch ( $menu_item->url ) {
						case '/login' :
							$menu_item->url = wp_login_url();
							break;
						case '/register' :
							$menu_item->url = wp_registration_url();
							break;
						case '/logout' :
							$menu_item->url = wp_logout_url( home_url() );
							break;
						case '/my-account' :
							$menu_item->url = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
							break;
					}

					$menu_items[] = $menu_item;
				} else {
					$url = parse_url( $menu_item->url );
					if ( 'yes' == get_option( 'msm_display_customer_info', 'no' ) && 0 === strpos( $url['path'], '/login' ) ) {
						$user = get_userdata( get_current_user_id() );

						$menu_item->url = '';
						$templates      = apply_filters( 'msm_display_customer_info_template', array(
							'search'  => array( '{고객명}' ),
							'replace' => $user->display_name
						), $user );

						$menu_item->title = str_replace( $templates['search'], $templates['replace'], get_option( 'msm_customer_info_string', '{고객명}님 반갑습니다' ) );
						$menu_items[]     = $menu_item;
					}
				}
			}

			return $menu_items;
		}
	}

}