<?php


class MSADDR_WCS {
	static $addr_fields = array(
		'postcode'   => array(
			'prefix'  => 'mshop_',
			'postfix' => '_address-postnum',
			'update'  => true
		),
		'address_1'  => array(
			'prefix'  => 'mshop_',
			'postfix' => '_address-addr1',
			'update'  => true
		),
		'address_2'  => array(
			'prefix'  => 'mshop_',
			'postfix' => '_address-addr2',
			'update'  => true
		),
		'first_name' => array(
			'prefix'  => '',
			'postfix' => '_first_name_kr',
			'update'  => false
		),
		'email'      => array(
			'prefix'  => '',
			'postfix' => '_email_kr',
			'update'  => false
		),
		'phone'      => array(
			'prefix'  => '',
			'postfix' => '_phone_kr',
			'update'  => false
		),
	);

	static function get_field_name( $field, $address_type ) {
		return $field['prefix'] . $address_type . $field['postfix'];
	}
	public static function maybe_update_subscription_addresses( $user_id, $address_type ) {
		if ( class_exists( 'WC_Subscriptions' ) ) {
			if ( ! wcs_user_has_subscription( $user_id ) || wc_notice_count( 'error' ) > 0 || empty( $_POST['_wcsnonce'] ) || ! wp_verify_nonce( $_POST['_wcsnonce'], 'wcs_edit_address' ) ) {
				return;
			}

			$address_type   = ( 'billing' == $address_type || 'shipping' == $address_type ) ? $address_type : '';
			$address_fields = WC()->countries->get_address_fields( esc_attr( $_POST[ $address_type . '_country' ] ), $address_type . '_' );
			$address        = array();

			if ( isset( $address_fields[ 'mshop_' . $address_type . '_address' ] ) ) {
				foreach ( self::$addr_fields as $key => $field ) {
					if ( isset( $_POST[ self::get_field_name( $field, $address_type ) ] ) ) {
						$_POST[ $address_type . '_' . $key ] = $_POST[ self::get_field_name( $field, $address_type ) ];
					}
				}
			}

			if ( isset( $_POST['update_all_subscriptions_addresses'] ) ) {

				$users_subscriptions = wcs_get_users_subscriptions( $user_id );

				foreach ( $users_subscriptions as $subscription ) {
					if ( $subscription->has_status( array( 'active', 'on-hold' ) ) ) {
						foreach ( self::$addr_fields as $key => $field ) {
							$field_name = self::get_field_name( $field, $address_type );
							if ( isset( $_POST[ $field_name ] ) ) {
								self::update_meta_data( $subscription, '_' . $field_name, $_POST[ $field_name ] );
							}
						}
					}
				}
			} elseif ( isset( $_POST['update_subscription_address'] ) ) {

				$subscription = wcs_get_subscription( intval( $_POST['update_subscription_address'] ) );

				// Update the address only if the user actually owns the subscription
				if ( ! empty( $subscription ) ) {
					foreach ( self::$addr_fields as $key => $field ) {
						$field_name = self::get_field_name( $field, $address_type );
						if ( isset( $_POST[ $field_name ] ) ) {
							self::update_meta_data( $subscription, '_' . $field_name, $_POST[ $field_name ] );
						}
					}
				}
			}
		}
	}
	static function update_meta_data( $object, $key, $value ) {
		$object->update_meta_data( $key, $value );
		$object->save_meta_data();
	}
}
