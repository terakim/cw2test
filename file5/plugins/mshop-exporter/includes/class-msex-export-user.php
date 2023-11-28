<?php
/*
=====================================================================================
                엠샵 업다운로드 / Copyright 2016 by CodeM(c)
=====================================================================================

  [ 우커머스 버전 지원 안내 ]

   워드프레스 버전 : WordPress 4.6 이상

   우커머스 버전 : WooCommerce 2.6 이상


  [ 코드엠 플러그인 라이센스 규정 ]

   (주)코드엠에서 개발된 워드프레스  플러그인을 사용하시는 분들에게는 다음 사항에 대한 동의가 있는 것으로 간주합니다.

   1. 코드엠에서 개발한 워드프레스 우커머스용 엠샵 업다운로드 플러그인의 저작권은 (주)코드엠에게 있습니다.
   
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
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MSEX_Export_User' ) ) {

	require_once MSEX()->plugin_path() . '/includes/abstract/abstract-msex-export.php';

	class MSEX_Export_User extends MSEX_Export {
		protected $role_list = null;
		protected function get_user_role( $user ) {
			if ( is_null( $this->role_list ) ) {
				$this->role_list = get_editable_roles();
			}

			$user_roles = array_intersect( array_values( $user->roles ), array_keys( get_editable_roles() ) );
			if ( ! empty( $user_roles ) ) {
				$user_role = reset( $user_roles );

				return wp_specialchars_decode( translate_user_role( $this->role_list[ $user_role ]['name'] ) );
			} else {
				return '역할없음';
			}
		}
		protected function get_order_total( $user ) {
			$order_total = 0;

			$args = array(
				'post_status'    => array(
					'wc-completed',
					'wc-order-received',
					'wc-processing'
				),
				'post_type'      => 'shop_order',
				'posts_per_page' => '-1',
				'meta_query'     => array(
					array(
						'key'     => '_customer_user',
						'value'   => $user->ID,
						'compare' => '=',
					),
				)
			);

			$query = new WP_Query( $args );

			foreach ( $query->get_posts() as $post ) {
				$order       = new WC_Order( $post->ID );
				$order_total += $order->get_subtotal();
			}

			return $order_total;
		}
		public function get_row( $user ) {
			$row_data = array();

			foreach ( self::get_fields() as $field ) {
				$field_type  = msex_get( $field, 'field_type' );
				$field_label = msex_get( $field, 'field_label' );
				$meta_key    = msex_get( $field, 'meta_key' );
				$field_value = '';

				switch ( $field_type ) {
					case 'id' :
						$field_value = $user->ID;
						break;
					case 'user_login' :
						$field_value = $user->user_login;
						break;
					case 'user_name' :
						$field_value = $user->user_firstname . ' ' . $user->user_lastname;
						break;
					case 'user_email' :
						$field_value = $user->user_email;
						break;
					case 'user_role_name' :
						$field_value = $this->get_user_role( $user );
						break;
					case 'user_posts_count' :
						$field_value = count_user_posts( $user->ID );
						break;
					case 'user_status' :
						if ( get_user_meta( $user->ID, 'is_unsubscribed', true ) == "1" ) {
							$field_value = '탈퇴';
						} else if ( get_user_meta( $user->ID, 'is_unsubscribed', true ) == "2" ) {
							$field_value = '휴면';
						} else {
							$field_value = '정상';
						}
						break;
					case 'user_zipcode' :
						$field_value = get_user_meta( $user->ID, 'billing_postcode', true );
						break;
					case 'user_address1' :
						$field_value = get_user_meta( $user->ID, 'billing_address_1', true );
						break;
					case 'user_address2' :
						$field_value = get_user_meta( $user->ID, 'billing_address_2', true );
						break;
					case 'user_phone' :
						$field_value = get_user_meta( $user->ID, 'billing_phone', true );
						break;
					case 'mshop_point' :
						if ( class_exists( 'MSPS_User' ) ) {
							$msps_user   = new MSPS_User( $user );
							$field_value = $msps_user->get_point();
						} else {
							$field_value = get_user_meta( $user->ID, '_mshop_point', true );
						}
						break;
					case 'free_point' :
					case 'recommender_point' :
						if ( class_exists( 'MSPS_User' ) ) {
							$msps_user   = new MSPS_User( $user );
							$field_value = $msps_user->get_point( array( $field_type ) );
						} else {
							$field_value = '';
						}
						break;
					case 'mshop_money_spent' :
						$field_value = get_user_meta( $user->ID, '_money_spent', true );
						break;
					case 'mshop_order_total' :
						$field_value = $this->get_order_total( $user );
						break;
					case 'register_date' :
						$field_value = get_date_from_gmt( $user->user_registered );
						break;
					case 'last_login_date' :
						$field_value = get_user_meta( $user->ID, 'last_login_time', true );
						break;
					case 'user_order_count' :
						$args        = array(
							'numberposts' => -1,
							'customer_id' => $user->ID,
							'post_status' => array( 'order-received', 'processing', 'shipping', 'shipped', 'completed', 'delayed' ),
							'post_type'   => 'shop_order',
							'return'      => 'ids',
						);
						$field_value = count( wc_get_orders( $args ) );
						break;
					case 'subscription_order_count' :
						$user_subscriptions = wcs_get_users_subscriptions( $user->ID );
						foreach ( $user_subscriptions as $subscription ) {
							$subscription_orders += count( $subscription->get_related_orders() );
						}
						$field_value = $subscription_orders;
						break;
					case 'subscription_count' :
						$field_value = count( wcs_get_users_subscriptions( $user->ID ) );
						break;
					case 'subscription_active' :
						if ( wcs_user_has_subscription( $user->ID, '', 'active' ) ) {
							$field_value = 'yes';
						} else {
							$field_value = 'no';
						}
						break;
					case 'user_meta' :
						if ( is_callable( array( $user, 'get_' . $meta_key ) ) ) {
							$field_value = $user->{"get_$meta_key"}();
						}

						if ( false == $field_value ) {
							$field_value = get_user_meta( $user->ID, $meta_key, true );
						}
						break;
					case 'custom' :
						$field_type = $meta_key;
						break;
					case 'text' :
						$field_type  = $field_label;
						$field_value = $meta_key;
						break;
					default :
						if ( has_filter( 'msex_user_field_' . $field_type ) ) {
							$field_value = apply_filters( 'msex_user_field_' . $field_type, $field_value, $field, $user );
						} else {
							$field_value = apply_filters( 'msex_export_user_field_value_' . $this->get_slug(), $field_value, $field, $user );
						}
						break;
				}

				if ( 'csv' == $this->get_download_type() && is_numeric( $field_value ) ) {
					$field_value = '="' . $field_value . '"';
				}

				$row_data = array_merge( $row_data, apply_filters( 'msex_export_user_field_value_array_' . $this->get_slug(), array( $field_value ), $field, $user ) );
			}

			return array_merge( $row_data, apply_filters( 'msex_export_user_row_' . $this->get_slug(), array(), $user, $this ) );
		}
		public function get_data( $user_ids ) {
			$user_data = array();
			foreach ( $user_ids as $user_id ) {
				$user = get_userdata( $user_id );

				if ( $user ) {
					$user_row  = $this->get_row( $user );
					$user_data = array_merge( $user_data, apply_filters( 'msex_export_user_item_' . $this->get_slug(), array( $user_row ), $user, $this ) );
				}
			}

			return $user_data;
		}
	}
}