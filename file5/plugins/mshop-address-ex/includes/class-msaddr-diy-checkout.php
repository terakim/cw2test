<?php

/*
=====================================================================================
                ﻿엠샵 주소 체크아웃 / Copyright 2015 by CodeM(c)
=====================================================================================

  [ 우커머스 버전 지원 안내 ]

   워드프레스 버전 : WordPress 4.3

   우커머스 버전 : WooCommerce 2.4


  [ 코드엠 플러그인 라이센스 규정 ]

   (주)코드엠에서 개발된 워드프레스  플러그인을 사용하시는 분들에게는 다음 사항에 대한 동의가 있는 것으로 간주합니다.

   1. 코드엠에서 개발한 워드프레스 우커머스용 ﻿엠샵 주소 체크아웃 플러그인의 저작권은 (주)코드엠에게 있습니다.
   
   2. 플러그인은 사용권을 구매하는 것이며, 프로그램 저작권에 대한 구매가 아닙니다.

   3. 플러그인을 구입하여 다수의 사이트에 복사하여 사용할 수 없으며, 1개의 라이센스는 1개의 사이트에만 사용할 수 있습니다. 
      이를 위반 시 지적 재산권에 대한 손해 배상 의무를 갖습니다.

   4. 플러그인은 구입 후 1년간 업데이트를 지원합니다.

   5. 플러그인은 워드프레스, 테마, 플러그인과의 호환성에 대한 책임이 없습니다.

   6. 플러그인 설치 후 버전에 관련한 운용 및 관리의 책임은 사이트 당사자에게 있습니다.

   7. 다운로드한 플러그인은 환불되지 않습니다.

=====================================================================================
*/

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'MSADDR_DIY_Checkout' ) ) {

	class MSADDR_DIY_Checkout {
		protected static $enqueued = false;

		protected static $shipping_destinations = null;
		public static function enqueue_script( $force = false ) {
			if ( $force || ( defined( 'PAFW_DC_CHECKOUT' ) && msaddr_need_scripts() && ! self::$enqueued ) ) {
				wp_enqueue_script( 'ms-address', plugins_url( '/assets/js/mshop-address-diy-checkout.js', MSADDR_PLUGIN_FILE ), array( 'jquery', 'wc-country-select' ), MSADDR_VERSION );
				wp_enqueue_script( 'ms-address-search', plugins_url( '/assets/js/mshop-address-search.js', MSADDR_PLUGIN_FILE ), array( 'jquery', 'underscore' ), MSADDR_VERSION );

				$default_country   = '';
				$allowed_countries = WC()->countries->get_allowed_countries();
				if ( 1 == count( $allowed_countries ) ) {
					$default_country = current( array_keys( $allowed_countries ) );
				}

				if ( 'yes' == get_option( 'msaddr_use_select2', 'yes' ) ) {
					wp_enqueue_script( 'msaddr-select2', plugins_url( '/assets/vendor/select2/js/select2.full.min.js', MSADDR_PLUGIN_FILE ), array( 'jquery' ), MSADDR_VERSION );
					wp_enqueue_style( 'msaddr-select2', plugins_url( '/assets/vendor/select2/css/select2.min.css', MSADDR_PLUGIN_FILE ), array(), MSADDR_VERSION );
				}

				wp_localize_script( 'ms-address-search', '_msaddr_search', array(
					'search_url'               => base64_decode( 'aHR0cHM6Ly9hZGRyZXNzLXMyLmNvZGVtc2hvcC5jb20vczI=' ),
					'confirm_key'              => base64_decode( 'VTAxVFgwRlZWRWd5TURFNE1EUXhNREUzTURRd01ERXdOemd3T1RrPQ==' ),
					'count_per_page'           => 10,
					'nav_size'                 => wp_is_mobile() ? 5 : 10,
					'is_edit_address'          => is_wc_endpoint_url( 'edit-address' ),
					'use_address_book'         => MSADDR_Address_Book::is_enabled(),
					'primary_address_type'     => get_option( 'msaddr_primary_address_type', 'road' ),
					'show_other_address'       => get_option( 'msaddr_show_other_address', 'no' ),
					'autocomplete_fields'      => array(
						'billing_first_name'    => 'shipping_first_name',
						'billing_first_name_kr' => 'shipping_first_name_kr',
						'billing_email'         => 'shipping_email',
						'billing_email_kr'      => 'shipping_email',
						'billing_phone'         => 'shipping_phone',
						'billing_phone_kr'      => 'shipping_phone',
					),
					'tel_numeric'              => get_option( 'msaddr_tel_numeric', 'no' ),
					'form_field_display_style' => apply_filters( 'msaddr_form_field_display_style', 'block' ),
					'default_country'          => $default_country,

				) );

				//Flatsome Theme Exception
				if ( wp_script_is( 'flatsome-magnific-popup', 'registered' ) ) {
					wp_dequeue_script( 'flatsome-magnific-popup' );
				}

				wp_enqueue_script( 'jquery-magnific-popup-address', plugins_url( '/assets/js/jquery.magnific-popup.min.js', MSADDR_PLUGIN_FILE ), array(), MSADDR_VERSION );

				wp_enqueue_style( 'mshop-address', plugins_url( '/assets/css/mshop-address.css', MSADDR_PLUGIN_FILE ) );

				echo '<style type="text/css">' . get_option( 'mshop_address_custom_css' ) . '</style>';

				self::$enqueued = true;
			}
		}
		public static function load_shipping_destinations() {
			if ( is_null( self::$shipping_destinations ) ) {
				self::$shipping_destinations = get_user_meta( get_current_user_id(), '_msaddr_shipping_destinations', true );

				if ( ! is_array( self::$shipping_destinations ) ) {
					self::$shipping_destinations = array();
				}
			}

			return self::$shipping_destinations;
		}
		public static function get_shipping_destinations( $page = 1, $keyword = '', $address_type = 'billing' ) {
			$shipping_destinations = apply_filters( 'msaddr_get_shipping_destinations', null, $page, $keyword, $address_type );

			if ( is_null( $shipping_destinations ) ) {
				$destinations = self::load_shipping_destinations();

				$address_per_page = apply_filters( 'msaddr_address_per_page', 5 );

				if ( ! empty( $keyword ) ) {
					$destinations = array_filter( $destinations, function ( $destination ) use ( $keyword, $address_type ) {
						$first_name = $destination["address"]["{$address_type}_first_name"];
						$phone      = $destination["address"]["{$address_type}_phone_kr"];
						$address    = $destination["address"]["{$address_type}_address_1"];

						return false !== strpos( $first_name, $keyword ) || false !== strpos( $phone, $keyword ) || false !== strpos( $address, $keyword );
					} );
				}

				$shipping_destinations = array(
					'total'        => count( $destinations ),
					'page'         => $page,
					'keyword'      => $keyword,
					'address_type' => $address_type,
					'address'      => array_slice( $destinations, $address_per_page * ( $page - 1 ), $address_per_page, true )
				);
			}

			return $shipping_destinations;
		}
		public static function get_default_shipping_destination_key( $address_type = 'billing' ) {
			$shipping_destination_key = apply_filters( 'msaddr_get_default_shipping_destination_key', null, $address_type );

			if ( is_null( $shipping_destination_key ) ) {
				foreach ( self::load_shipping_destinations() as $key => $destination ) {
					if ( $destination['default'] ) {
						$shipping_destination_key = $key;
						break;
					}
				}
			}

			return $shipping_destination_key;
		}
		public static function set_default_destination( $default_key, $address_type ) {
			if ( ! apply_filters( 'msaddr_set_default_destination', false, $default_key, $address_type ) ) {
				$destinations = self::load_shipping_destinations();

				foreach ( $destinations as $key => &$destination ) {
					$destination['default'] = $key == $default_key;
				}

				update_user_meta( get_current_user_id(), '_msaddr_shipping_destinations', $destinations );
			}
		}
		public static function delete_destination( $delete_key ) {
			if ( ! apply_filters( 'msaddr_delete_destination', false, $delete_key ) ) {
				$reset_default = false;
				$destinations  = self::load_shipping_destinations();

				foreach ( $destinations as $key => $destination ) {
					if ( $key == $delete_key ) {
						if ( $destination['default'] ) {
							$reset_default = true;
						}

						unset( $destinations[ $key ] );
						break;
					}
				}

				if ( $reset_default && ! empty( $destinations ) ) {
					$first_key = current( array_keys( $destinations ) );

					$destinations[ $first_key ]['default'] = true;
				}

				update_user_meta( get_current_user_id(), '_msaddr_shipping_destinations', $destinations );
			}
		}
		public static function update_destination( $args, $address_type ) {
			if ( ! apply_filters( 'msaddr_update_destination', false, $args, $address_type ) ) {
				$destinations = self::load_shipping_destinations();

				$destination_key = $args['msaddr_shipping_destination_key'];
				unset( $args['msaddr_shipping_destination_key'] );

				if ( 'new' == $destination_key ) {
					$destination_key = wp_generate_uuid4();

					$destinations[ $destination_key ] = array(
						'default' => empty( $destinations ),
						'address' => $args
					);
				} else {
					$destinations[ $destination_key ]['address'] = $args;
				}

				update_user_meta( get_current_user_id(), '_msaddr_shipping_destinations', $destinations );
			}
		}
		public static function get_default_shipping_destination( $address_type = 'billing' ) {
			return self::get_shipping_destination( self::get_default_shipping_destination_key(), $address_type );
		}
		public static function get_shipping_destination( $key, $address_type = 'billing' ) {
			$destination = apply_filters( "msaddr_get_shipping_destination", null, $key, $address_type );

			if ( is_null( $destination ) ) {
				$destinations = self::load_shipping_destinations();

				$destination = ! empty( $key ) && isset( $destinations[ $key ] ) ? $destinations[ $key ] : null;
			}

			return $destination;
		}
		public static function maybe_adjust_shipping_data( $order_id, $posted ) {
			if ( isset( $_POST['msaddr_shipping_destination_key'] ) ) {
				$_POST['msaddr_shipping_destination_type'] = get_option( 'pafw_dc_address_book_type', 'billing' );
				$address_type = $_POST['msaddr_shipping_destination_type'];

				$match_key = array(
					$address_type . '_first_name_kr'              => $address_type . '_first_name',
					'mshop_' . $address_type . '_address-postnum' => $address_type . '_postcode',
					'mshop_' . $address_type . '_address-addr1'   => $address_type . '_address_1',
					'mshop_' . $address_type . '_address-addr2'   => $address_type . '_address_2',
					$address_type . '_email_kr'                   => $address_type . '_email',
					$address_type . '_phone_kr'                   => $address_type . '_phone',
					$address_type . '_email'                      => $address_type . '_email_kr',
					$address_type . '_phone'                      => $address_type . '_phone_kr'
				);

				foreach ( $match_key as $src_key => $destination_key ) {
					if ( isset( $_POST[ $src_key ] ) ) {
						$_POST[ $destination_key ] = $_POST[ $src_key ];
					}
				}

				$order = wc_get_order( $order_id );

				if ( 'billing_only' == get_option( 'woocommerce_ship_to_destination' ) && 'billing' == $address_type ) {
					foreach ( $_POST as $key => $value ) {
						if ( false !== strpos( $key, 'billing' ) ) {
							msaddr_update_checkout_field_value( $order, str_replace( 'billing', 'shipping', $key ) , $value );
						}
					}

					$order->save();
				}
			}
		}
		public static function maybe_update_shipping_destinations( $order_id, $posted ) {
			if ( isset( $_POST['msaddr_shipping_destination_key'] ) ) {
				$destination_type = msaddr_get( $_POST, 'msaddr_shipping_destination_type', 'billing' );

				$destination_key = $_POST["msaddr_shipping_destination_key"];

				$posted["{$destination_type}_postcode"]              = $_POST["mshop_{$destination_type}_address-postnum"];
				$posted["{$destination_type}_address_1"]             = $_POST["mshop_{$destination_type}_address-addr1"];
				$posted["{$destination_type}_address_2"]             = $_POST["mshop_{$destination_type}_address-addr2"];
				$posted["mshop_{$destination_type}_address-postnum"] = $_POST["mshop_{$destination_type}_address-postnum"];
				$posted["mshop_{$destination_type}_address-addr1"]   = $_POST["mshop_{$destination_type}_address-addr1"];
				$posted["mshop_{$destination_type}_address-addr2"]   = $_POST["mshop_{$destination_type}_address-addr2"];

				if ( ! apply_filters( 'msaddr_maybe_update_shipping_destination', false, $destination_key, $destination_type, $posted ) ) {

					$destinations = self::load_shipping_destinations();

					if ( 'new' == $destination_key ) {
						$destination_key = wp_generate_uuid4();
						$destination     = array(
							'default' => empty( $destinations ),
							'address' => array()
						);
					} else {
						$destination = $destinations[ $destination_key ];
					}

					if ( 'billing' == $destination_type ) {
						$fields = MSADDR_Address_Book::get_billing_fields();
					} else {
						$fields = MSADDR_Address_Book::get_shipping_fields();
					}

					foreach ( $fields as $field ) {
						$destination['address'][ $field ] = $posted[ $field ];
					}

					$destinations[ $destination_key ] = $destination;

					update_user_meta( get_current_user_id(), '_msaddr_shipping_destinations', $destinations );
				}
			}
		}
	}
}