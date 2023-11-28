<?php

/**
 * Set the Referring Affiliate on the Affiliate Registration Form
 *
 * @since  1.1.1
 */
function affwp_mlm_process_affiliate_registration() {

	$referrer = sanitize_text_field( $_POST['affwp_mlm_referrer'] );

	// Check for valid affiliate by affiliate_id
	if ( is_numeric( $referrer ) ) {

		$affiliate_id = $referrer;

		if ( ! affwp_is_active_affiliate( $affiliate_id ) ) {
			affiliate_wp()->register->add_error( 'referrer_invalid_id', __( 'Unknown referrer. Please try again', 'affiliatewp-multi-level-marketing' ) );

			$valid = false;
		} else{
			$valid = true;
		}

	} else{

		// Check for valid affiliate by username
		if ( username_exists( $referrer ) ) {

			$affiliate_id = affiliate_wp()->tracking->get_affiliate_id_from_login( $referrer );

			if ( ! affwp_is_active_affiliate( $affiliate_id ) ) {
				affiliate_wp()->register->add_error( 'referrer_invalid_username', __( 'Unknown referrer. Please try again', 'affiliatewp-multi-level-marketing' ) );

				$valid = false;
			} else{
				$valid = true;
			}
		}

	}

	if ( ! empty( $valid ) && $valid == true ) {

		if ( session_status() == PHP_SESSION_NONE ) session_start();

		$_SESSION['affwp_mlm_referrer'] = $affiliate_id;

	}

}
add_action( 'affwp_process_register_form', 'affwp_mlm_process_affiliate_registration' );

/**
 * Add parent affiliate, direct affiliate, and matrix level
 *
 * @since 1.0
 */
function affwp_mlm_add_affiliate_connections( $data = array() ) {

	global $wpdb;

	$affiliate_id        = absint( $data['affiliate_id'] );
	$parent_affiliate_id = ! empty( $data['parent_affiliate_id'] ) ? absint( $data['parent_affiliate_id'] ) : '';
	$direct_affiliate_id = ! empty( $data['direct_affiliate_id'] ) ? absint( $data['direct_affiliate_id'] ) : '';
	$matrix_level        = ! empty( $data['matrix_level'] ) ? absint( $data['matrix_level'] ) : 0;

	$affiliate_connection_table = affwp_mlm_get_connections_table();

	$affiliate_data = array(
		'affiliate_id'        => $affiliate_id,
		'affiliate_parent_id' => $parent_affiliate_id,
		'direct_affiliate_id' => $direct_affiliate_id,
		'matrix_level' 		  => $matrix_level
	);

	// Insert data
	$sql = $wpdb->insert( $affiliate_connection_table, $affiliate_data );

	return $sql;
}

/**
 * Update parent affiliate, direct affiliate, and matrix level
 *
 * @since 1.0
 */
function affwp_mlm_update_affiliate_connections( $data = array() ) {

	global $wpdb;

	$affiliate_id        = absint( $data['affiliate_id'] );
	$parent_affiliate_id = absint( $data['parent_affiliate_id'] );
	$direct_affiliate_id = absint( $data['direct_affiliate_id'] );

	// Add affiliate's level in the overall matrix
	$matrix_level 	 	 = isset( $data['matrix_level'] ) ? absint( $data['matrix_level'] ) : 0;
	$parent_connections  = affwp_mlm_get_affiliate_connections( $parent_affiliate_id );

	if( empty( $matrix_level ) ) {

		$matrix_level = !empty( $parent_connections->matrix_level ) ? $parent_connections->matrix_level : 0;
		$matrix_level++;

	}

	$affiliate_connection_table = affwp_mlm_get_connections_table();

	$old_connections = affwp_mlm_get_affiliate_connections( $affiliate_id );

	// Convert object to array
	$old_connections = array(
		'affiliate_id'        => $old_connections->affiliate_id,
		'affiliate_parent_id' => $old_connections->affiliate_parent_id,
		'direct_affiliate_id' => $old_connections->direct_affiliate_id,
		'matrix_level' 		  	=> $old_connections->matrix_level
	);

	$new_connections = array(
		'affiliate_id'        => $affiliate_id,
		'affiliate_parent_id' => $parent_affiliate_id,
		'direct_affiliate_id' => $direct_affiliate_id,
		'matrix_level' 		  	=> $matrix_level
	);

	// Insert data
	$sql = $wpdb->replace( $affiliate_connection_table, $new_connections );

	if ( $sql ) {

		if ( empty( $new_connections['affiliate_parent_id'] ) || $old_connections['affiliate_parent_id'] !== $new_connections['affiliate_parent_id'] ) {

			do_action( 'affwp_mlm_affiliate_disconnected', $affiliate_id, $old_connections );

		}

		do_action( 'affwp_mlm_connections_updated', $affiliate_id, $old_connections, $new_connections );
	}

	return $sql;

}

/**
 * Delete parent affiliate, direct affiliate, and matrix level
 *
 * @since 1.0
 */
function affwp_mlm_delete_affiliate_connections( $affiliate_id = 0 ) {

	if ( empty( $affiliate_id ) ) return;

	global $wpdb;

	$affiliate_connection_table = affwp_mlm_get_connections_table();

	do_action( 'affwp_mlm_before_connections_deleted', $affiliate_id );

	$wpdb->delete( $affiliate_connection_table, array( 'affiliate_id' => $affiliate_id ), array( '%d' ) );

	do_action( 'affwp_mlm_after_connections_deleted', $affiliate_id );

}

/**
 * Link sub-affiliate to parent on affiliate registration
 *
 * @since  1.0
 */
function affwp_mlm_connect_affiliates( $affiliate_id ) {

	// Get currently tracked affiliate from cookie
	$direct_affiliate_id = affiliate_wp()->tracking->get_affiliate_id();

	if ( empty( $direct_affiliate_id ) ) {

		$user_id = affwp_get_affiliate_user_id( $affiliate_id );

		// Get referring affiliate from customer's user account
		$customer_direct_affiliate_id = get_user_meta( $user_id, '_affwp_mlm_direct_affiliate_id', true );

		if ( ! empty( $customer_direct_affiliate_id ) && affwp_is_active_affiliate( $customer_direct_affiliate_id ) ) {

			$direct_affiliate_id = $customer_direct_affiliate_id;

		} else {

			$default_parent = affiliate_wp()->settings->get( 'affwp_mlm_default_affiliate' );

			if ( ! empty( $default_parent ) ) {

				// If no referring affiliate, use default if set
				$direct_affiliate_id = $default_parent;

			} elseif ( affiliate_wp()->settings->get( 'affwp_mlm_forced_matrix' ) ) {

				// Get the 1st active affiliate in the database
				$active_affiliate = affiliate_wp()->affiliates->get_by( 'status', 'active' );

				// If forced matrix is enabled, find the first available affiliate
				$direct_affiliate_id = affwp_mlm_find_open_affiliate( $active_affiliate->affiliate_id );
			}

		}

	}

	// Allow swapping of referring affiliate
	$direct_affiliate_id = apply_filters( 'affwp_mlm_connect_direct_affiliate', $direct_affiliate_id, $affiliate_id );

	if ( affwp_mlm_sub_affiliate_allowed( $direct_affiliate_id ) ) {

		$parent_affiliate_id = $direct_affiliate_id;

	} else {

		// The max number of Downline Levels to search
		$max_depth = 10;

		// Tracked affiliate can't have more subs, get the next available affiliate below the referrer
		$parent_affiliate_id = affwp_mlm_find_open_affiliate( $direct_affiliate_id, $max_depth );

	}

	if ( $parent_affiliate_id ) {

		// Add affiliates level in the overall matrix
		$parent_connections = affwp_mlm_get_affiliate_connections( $parent_affiliate_id );
		$matrix_level = !empty( $parent_connections->matrix_level ) ? $parent_connections->matrix_level : 0;
		$matrix_level++;

		// Add parent affiliate & direct affiliate
		$mlm_data = apply_filters( 'affwp_mlm_data_before_add_connections', array(
			'parent_affiliate_id' => $parent_affiliate_id,
			'direct_affiliate_id' => $direct_affiliate_id,
			'matrix_level' 		  => $matrix_level,
			'affiliate_id'        => $affiliate_id
		) );

		if ( affwp_mlm_add_affiliate_connections( $mlm_data ) ) {

			if ( affiliate_wp()->settings->get( 'affwp_mlm_forced_matrix' ) )
				affwp_mlm_check_upline_complete_cycles( $affiliate_id );

			do_action( 'affwp_mlm_affiliates_connected', $affiliate_id, $mlm_data );

		}
	}

}
add_action( 'affwp_insert_affiliate', 'affwp_mlm_connect_affiliates', 10, 1 );

/**
 * Set the referrer from the affiliate registration form as the direct affiliate
 *
 * @since  1.1.1
 */
function affwp_mlm_connect_form_affiliate( $direct_affiliate_id = 0 ) {

	$referrer = $_SESSION['affwp_mlm_referrer'];

	if ( ! isset( $referrer ) ) return $direct_affiliate_id;

	$direct_affiliate_id = $referrer;

	return $direct_affiliate_id;
}
add_filter( 'affwp_mlm_connect_direct_affiliate', 'affwp_mlm_connect_form_affiliate', 10, 1 );

/**
 * Update the affiliate
 *
 * @since  1.0
 */
function affwp_mlm_process_update_affiliate( $data = array() ) {

	if ( empty( $data['affiliate_id'] ) ) return false;

	if ( ! is_admin() ) return false;

	if ( ! current_user_can( 'manage_affiliates' ) ) {
		wp_die( __( 'You do not have permission to manage affiliates', 'affiliate-wp' ) );
	}

	$affiliate_id = absint( $data['affiliate_id'] );
	$connection_status = '';

	if ( ! affwp_mlm_get_affiliate_connections( $affiliate_id ) ) {
		affwp_mlm_add_affiliate_connections( $data );
	} else {
		affwp_mlm_update_affiliate_connections( $data );
		$connection_status = 'updated';
	}

	if ( $connection_status == 'updated' ) {

		// Get connections data
		$connections = affwp_mlm_get_affiliate_connections( $affiliate_id );
		$parent_affiliate_id = $connections->affiliate_parent_id;
		$direct_affiliate_id = $connections->direct_affiliate_id;
		$matrix_level = ! empty( $connections->matrix_level ) ? $connections->matrix_level : 0;

		// Pass in connections data
		$mlm_data = array(
			'parent_affiliate_id' => $parent_affiliate_id,
			'direct_affiliate_id' => $direct_affiliate_id,
			'matrix_level' 		  	=> $matrix_level,
			'affiliate_id'        => $affiliate_id
		);

		if ( affiliate_wp()->settings->get( 'affwp_mlm_forced_matrix' ) )
			affwp_mlm_check_upline_complete_cycles( $affiliate_id );

		do_action( 'affwp_mlm_affiliates_connected', $affiliate_id, $mlm_data );

	}

}
// Handle updating and deleting parent affiliate data
// Note: Needs to be done at earlier priority because default hooks redirect
// and exit thus preventing later priority actions from running
add_action( 'affwp_update_affiliate', 'affwp_mlm_process_update_affiliate', 5, 1 );

/**
 * Set the Parent Affiliate on the Add New Affiliate Form
 *
 * @since  1.1
 */
function affwp_mlm_process_add_new_affiliate( $add ) {

	// Make sure this runs for admin only
	if ( current_user_can('edit_users') ) {

		if ( !empty( $add ) ) {

			global $_REQUEST;

			$user_id = affwp_get_affiliate_user_id( $add );
			$affiliate_id = affwp_get_affiliate_id( $user_id );
			$parent_affiliate_id = $_REQUEST['parent_affiliate_id'];

			if ( !empty( $affiliate_id ) && !empty( $parent_affiliate_id ) ) {

				// Add affiliates level in the overall matrix
				$parent_connections = affwp_mlm_get_affiliate_connections( $parent_affiliate_id );
				$matrix_level = !empty( $parent_connections->matrix_level ) ? $parent_connections->matrix_level : 0;
				$matrix_level++;

				// Add parent affiliate & direct affiliate
				$mlm_data = array(
					'parent_affiliate_id' => $parent_affiliate_id,
					'direct_affiliate_id' => $parent_affiliate_id,
					'matrix_level' 		  => $matrix_level,
					'affiliate_id'        => $affiliate_id
				);

				if ( affwp_mlm_add_affiliate_connections( $mlm_data ) ) {

					if ( affiliate_wp()->settings->get( 'affwp_mlm_forced_matrix' ) )
						affwp_mlm_check_upline_complete_cycles( $affiliate_id );

					do_action( 'affwp_mlm_affiliates_connected', $affiliate_id, $mlm_data );

				}

			}

		}

	}

}
add_action( 'affwp_post_insert_affiliate', 'affwp_mlm_process_add_new_affiliate' );

/**
 * Create a commission for referring a new Sub Affiliate
 *
 * @since 1.1
 */
function affwp_mlm_create_sub_affiliate_referral( $affiliate_id = 0, $mlm_data = array() ) {

	if ( empty( $affiliate_id ) || empty( $mlm_data ) ) return;

	// Ensure sub affiliate referrals are enabled
	$sub_refs = affiliate_wp()->settings->get( 'affwp_mlm_sub_ref' );

	if ( ! $sub_refs ) return;

	$direct_affiliate_id = $mlm_data['direct_affiliate_id'];

	// See if affiliate approval is required
	$approval = affiliate_wp()->settings->get( 'require_approval' );

	// Award commission only when the new affiliate is active
	if ( $approval && ! did_action( 'affwp_set_affiliate_status' ) ) {

		$affiliate_status = affwp_get_affiliate_status( $affiliate_id );

		if ( 'pending' == $affiliate_status  ) return;

	}

	// Get the amount set for sub affiliate referrals
	$amount = affiliate_wp()->settings->get( 'affwp_mlm_sub_ref_amount' );

	// Get the default status for sub affiliate referrals
	$ref_status = affiliate_wp()->settings->get( 'affwp_mlm_sub_ref_status' );

	$description = __( 'Sub Affiliate Referral', 'affiliatewp-multi-level-marketing' );

	// Store the new affiliate's id as the unique reference
	$reference = $affiliate_id;

	// Add the referral type as custom referral data
	$custom = 'sub_affiliate';

	// Create the referral
	$args = apply_filters( 'affwp_mlm_sub_aff_referral_args', array(
		'affiliate_id' => $direct_affiliate_id,
		'amount'       => $amount,
		'status'       => $ref_status,
		'description'  => $description,
		'reference'    => $reference,
		'visit_id'     => affiliate_wp()->tracking->get_visit_id(),
		'custom'       => $custom,
	), $direct_affiliate_id, $amount, $ref_status, $description, $reference, $visit_id, $custom );

	$referral_id = affwp_add_referral( $args );

	// Update the visit
	affiliate_wp()->visits->update( affiliate_wp()->tracking->get_visit_id(), array( 'referral_id' => $referral_id ), '', 'visit' );


	if ( $referral_id ) {

		do_action( 'affwp_mlm_sub_aff_ref_created', $affiliate_id, $direct_affiliate_id, $referral_id, $mlm_data, $args );

	}
}
add_action( 'affwp_mlm_affiliates_connected', 'affwp_mlm_create_sub_affiliate_referral', 10, 2 );

/**
 * Create a Sub Affiliate referral commission on affiliate approval
 *
 * @since  1.1
 */
function affwp_mlm_create_sub_affiliate_referral_on_approval( $affiliate_id = 0, $status = '', $old_status = '' ) {

	if ( empty( $affiliate_id ) ) return;

	// See if affiliate approval is required
	$approval = affiliate_wp()->settings->get( 'require_approval' );

	if ( ! $approval ) return;

	// Run on affiliate approval
	if ( $old_status == 'pending' && $status == 'active' ) {

		if ( affwp_mlm_is_sub_affiliate( $affiliate_id ) ) {

			// Get connections data
			$parent_connections = affwp_mlm_get_affiliate_connections( $affiliate_id );
			$parent_affiliate_id = $parent_connections->affiliate_parent_id;
			$direct_affiliate_id = $parent_connections->direct_affiliate_id;
			$matrix_level = $parent_connections->matrix_level;

			// Pass in connections data
			$mlm_data = array(
				'parent_affiliate_id' => $parent_affiliate_id,
				'direct_affiliate_id' => $direct_affiliate_id,
				'matrix_level' 		  	=> $matrix_level,
				'affiliate_id'        => $affiliate_id
			);

			affwp_mlm_create_sub_affiliate_referral( $affiliate_id, $mlm_data );
		}

	}

}
add_action( 'affwp_set_affiliate_status', 'affwp_mlm_create_sub_affiliate_referral_on_approval', 10, 3 );

/**
 * Delete affiliate connections when an affiliate is deleted
 *
 * @since  1.0
 */
function affwp_mlm_process_affiliate_deletion( $affiliate_id, $delete_data ) {

	if ( ! is_admin() ) return;

	affwp_mlm_delete_affiliate_connections( $affiliate_id );

}
add_action( 'affwp_affiliate_deleted', 'affwp_mlm_process_affiliate_deletion', 10, 2 );

/**
 * Save sub affiliate view settings per-affiliate
 *
 * @since  1.1.2
 */
function affwp_mlm_update_affiliate_profile_settings( $data ) {

	if ( empty( $data['affwp_mlm_view_subs_aff'] ) ) return;

	$subs_view = $data['affwp_mlm_view_subs_aff'];
	$affiliate_id = absint( $data['affiliate_id'] );

	// Returns an array
	$old_subs_view = affwp_get_affiliate_meta( $affiliate_id, 'view_subs_aff' );
	$old_subs_view = $old_subs_view[0] ? $old_subs_view[0] : '';

	if ( empty( $old_subs_view ) ) {

		// Add subs view
		affwp_add_affiliate_meta( $affiliate_id, 'view_subs_aff', $subs_view );

	} else {

		// Update subs view
		if ( $old_subs_view != $subs_view ) {
			affwp_update_affiliate_meta( $affiliate_id, 'view_subs_aff', $subs_view );
		} else {
			return;
		}
	}

}
add_action( 'affwp_update_affiliate_profile_settings', 'affwp_mlm_update_affiliate_profile_settings', 10, 1 );

/**
 * Check to see if an affiliate has completed level 1
 *
 * @since 1.1.1
 */
function affwp_mlm_check_level_1_complete( $affiliate_id = 0, $mlm_data = array() ) {

	if ( empty( $affiliate_id ) || empty( $mlm_data ) ) return;

	// Check if forced matrix is enabled (Forced Matrix)
	if ( ! affiliate_wp()->settings->get( 'affwp_mlm_forced_matrix' ) ) return;

	$parent_affiliate_id = $mlm_data['parent_affiliate_id'];
	$downline = affwp_mlm_get_downline( $parent_affiliate_id, 1 );
	$level = 1;
	$complete_cycles = affwp_mlm_get_complete_cycles( $parent_affiliate_id );
	$current_cycle = ( empty( $complete_cycles ) ) ? 1 : $complete_cycles;

	// See if the affiliate just completed level 1
	if ( affwp_mlm_is_level_complete( $parent_affiliate_id, $downline, $level, $current_cycle ) ) {

		do_action( 'affwp_mlm_level_1_complete', $affiliate_id, $parent_affiliate_id, $downline, $level, $current_cycle );

	} else {
		return;
	}
}
add_action( 'affwp_mlm_affiliates_connected', 'affwp_mlm_check_level_1_complete', 10, 2 );

/**
 * Lower the Parent's Cycle Count when a Parent is Removed/Changed
 *
 * @since 1.1.6
 */
function affwp_mlm_reduce_complete_cycles_on_disconnect( $affiliate_id = 0, $old_connections = array() ) {

	if ( ! isset( $old_connections['affiliate_parent_id'] ) ) return;

	$parent_id = $old_connections['affiliate_parent_id'];
	$parent_subs = affwp_mlm_get_downline( $parent_id );
	$complete_cycles = affwp_mlm_get_complete_cycles( $parent_id );
	$prev_cycle = $complete_cycles > 0 ? $complete_cycles - 1 : 0;

	// Check current completed cycle in case of complete cycle count reduction
	if ( ! affwp_mlm_is_cycle_complete( $parent_id, $parent_subs, $complete_cycles ) ) {

		// Reduce cycle count to previous cycle
		affwp_mlm_set_complete_cycles( $parent_id, $prev_cycle );

	}
}
add_action( 'affwp_mlm_affiliate_disconnected', 'affwp_mlm_reduce_complete_cycles_on_disconnect', 10, 2 );

/**
 *  Re-Assign the Affiliate to a New Parent when a Parent is Removed
 *
 * @since 1.1.6
 */
function affwp_mlm_reassign_orphaned_affiliate_on_disconnect( $affiliate_id = 0, $old_connections = array() ) {

	if ( ! isset( $old_connections['affiliate_parent_id'] ) ) return;

	$reassign = affiliate_wp()->settings->get( 'affwp_mlm_reassign_affiliate', 'parent' );

	// Stop if reassignment is disabled
	if ( empty( $reassign ) ) return;

	$data = array();
	$parent_id = $old_connections['affiliate_parent_id'];
	$grand_parent_id = affwp_mlm_get_parent_affiliate( $parent_id );
	$direct_id = affwp_mlm_get_direct_affiliate( $affiliate_id );

	// Reassign to Parent's Parent (Grand Parent)
	if ( $reassign == 'parent' && ! empty( $grand_parent_id ) ) {

		$data = array(
			'affiliate_id'        => absint( $affiliate_id ),
			'parent_affiliate_id' => absint( $grand_parent_id ),
			'direct_affiliate_id' => absint( $direct_id )
		);
	}

	// Reassign to Direct Affiliate
	if ( $reassign == 'direct' && ! empty( $direct_id ) ) {

		$data = array(
			'affiliate_id'        => absint( $affiliate_id ),
			'parent_affiliate_id' => absint( $direct_id ),
			'direct_affiliate_id' => absint( $direct_id )
		);
	}

	apply_filters( 'affwp_mlm_orphaned_affiliate_data', $data, $old_connections );

	if ( ! empty( $data ) ) affwp_mlm_update_affiliate_connections( $data );

}
add_action( 'affwp_mlm_affiliate_disconnected', 'affwp_mlm_reassign_orphaned_affiliate_on_disconnect', 10, 2 );

/**
 *  Re-Assign the Affiliate to a New Parent when a Parent is Removed
 *
 * @since 1.1.6
 */
function affwp_mlm_reassign_orphaned_affiliate_on_delete( $affiliate_id = 0 ) {

	$old_connections = affwp_mlm_get_affiliate_connections( $affiliate_id );

	// Convert object to array
	$old_connections = array(
		'affiliate_id'        => $old_connections->affiliate_id,
		'affiliate_parent_id' => $old_connections->affiliate_parent_id,
		'direct_affiliate_id' => $old_connections->direct_affiliate_id,
		'matrix_level' 		  => $old_connections->matrix_level
	);

	$reassign = affiliate_wp()->settings->get( 'affwp_mlm_reassign_affiliate', '' );

	// Stop if reassignment is disabled
	if ( empty( $reassign ) ) return;

	$data = array();
	$parent_id = $old_connections['affiliate_parent_id'];
	$grand_parent_id = affwp_mlm_get_parent_affiliate( $parent_id );
	$direct_id = affwp_mlm_get_direct_affiliate( $affiliate_id );

	// Reassign to Parent's Parent (Grand Parent)
	if ( $reassign == 'parent' && ! empty( $grand_parent_id ) ) {

		$data = array(
			'affiliate_id'        => absint( $affiliate_id ),
			'parent_affiliate_id' => absint( $grand_parent_id ),
			'direct_affiliate_id' => absint( $direct_id )
		);
	}

	// Reassign to Direct Affiliate
	if ( $reassign == 'direct' && ! empty( $direct_id ) ) {

		$data = array(
			'affiliate_id'        => absint( $affiliate_id ),
			'parent_affiliate_id' => absint( $direct_id ),
			'direct_affiliate_id' => absint( $direct_id )
		);
	}

	apply_filters( 'affwp_mlm_orphaned_affiliate_data', $data, $affiliate_id, $old_connections );

	if ( ! empty( $data ) ) affwp_mlm_update_affiliate_connections( $data );

}
//add_action( 'affwp_pre_delete_affiliate', 'affwp_mlm_reassign_orphaned_affiliate_on_delete', 10, 1 );

/**
 * Set the Direct Affiliate as the Lifetime Affiliate
 *
 * @since  1.1.2
 */
function affwp_mlm_sync_lifetime_to_mlm( $direct_affiliate_id = 0 ) {

	if ( ! function_exists( 'affiliate_wp_lifetime_commissions' ) ) return $direct_affiliate_id;

	$sync = affiliate_wp()->settings->get( 'affwp_mlm_lc_sync_lifetime_affiliate' );

	// Make sure Lifetime to MLM syncing is enabled in the settings
	if ( empty( $sync ) || $sync != 'lifetime' ) return $direct_affiliate_id;

	$user_id = get_current_user_id();

	// Check for Lifetime Commissions version 1.3+
	$lc_version_1_3 = ( true === version_compare( AFFWP_LC_VERSION, '1.3', '>=' ) ) ? true : false;

	// Check for Lifetime Commissions version 1.4.1+
	$lc_version_1_4_1 = ( true === version_compare( AFFWP_LC_VERSION, '1.4.1', '>=' ) ) ? true : false;

	if ( $lc_version_1_3 ) {

		$user = get_userdata( $user_id );
		$email = $user->user_email;

	}

	$affwp_lc_integrations = new Affiliate_WP_Lifetime_Commissions_Base;
	$lifetime_affiliate_id = ( $lc_version_1_3 && method_exists( $affwp_lc_integrations, 'get_affiliate_id_from_customer_email' ) ) ? $affwp_lc_integrations->get_affiliate_id_from_customer_email( $email ) : get_user_meta( $user_id, 'affwp_lc_affiliate_id', true );

	// Get the lifetime affiliate using methods from versions 1.4.1+
	if ( $lc_version_1_4_1 ) {

		$affwp_lc = affiliate_wp_lifetime_commissions();

		$lifetime_affiliate_id = false;
		$customer = affwp_get_customer( $email );

		if ( $customer ) {

			$lifetime_affiliate_id = $affwp_lc->lifetime_customers->get_column_by(
				'affiliate_id',
				'affwp_customer_id',
				$customer->ID
			);

		}

		if ( ! $lifetime_affiliate_id ) {

			$customer_meta = affiliate_wp()->customer_meta->get_by( 'meta_value', $email );

			if ( $customer_meta ) {

				$lifetime_affiliate_id = $affwp_lc->lifetime_customers->get_column_by(
					'affiliate_id',
					'affwp_customer_id',
					$customer_meta->affwp_customer_id
				);
			}
		}

	}

	$direct_affiliate_id = $lifetime_affiliate_id ? $lifetime_affiliate_id : $direct_affiliate_id;

	return $direct_affiliate_id;
}
add_filter( 'affwp_mlm_connect_direct_affiliate', 'affwp_mlm_sync_lifetime_to_mlm', 10, 1 );

/**
 * Set the Direct or Parent Affiliate as the Lifetime Affiliate
 *
 * @since 1.1.2
 */
function affwp_mlm_sync_mlm_to_lifetime( $affiliate_id, $mlm_data ) {

	if ( ! function_exists( 'affiliate_wp_lifetime_commissions' ) ) return;

	$sync = affiliate_wp()->settings->get( 'affwp_mlm_lc_sync_lifetime_affiliate' );

	// Make sure MLM to Lifetime syncing is enabled in the settings
	if ( empty( $sync ) || $sync == 'lifetime' ) return;

	$affwp_lc = affiliate_wp_lifetime_commissions();

	// Check for Lifetime Commissions version 1.3+
	$lc_version_1_3 = ( true === version_compare( AFFWP_LC_VERSION, '1.3', '>=' ) ) ? true : false;

	// Check for Lifetime Commissions version 1.4.1+
	$lc_version_1_4_1 = ( true === version_compare( AFFWP_LC_VERSION, '1.4.1', '>=' ) ) ? true : false;

	$sync_affiliate_id = 0;

	// Use the direct referring affiliate
	if ( $sync == 'direct' ) $sync_affiliate_id = $mlm_data['direct_affiliate_id'];

	// Use the parent affiliate
	if ( $sync == 'parent' ) $sync_affiliate_id = $mlm_data['parent_affiliate_id'];

	$user_id = affwp_get_affiliate_user_id( $affiliate_id );
	$user = get_userdata( $user_id );
	$user_email = $user->user_email;

	if ( $lc_version_1_3 ) {

		// Exit if updated version of AffiliateWP is not active
		if ( ! function_exists( 'affwp_get_customer' ) ) return;

		// Check for existing customer id
		$customer = affwp_get_customer( $user_email );

		// Update affiliate for existing customer
		if ( $customer ) {

			$customer_id = $customer->ID;
			$args = array(
				'customer_id'  => $customer_id,
				'affiliate_id' => $sync_affiliate_id
			);

			affwp_update_customer( $args );

			// Update Lifetime customer in db for versions 1.4.1+
			if ( $lc_version_1_4_1 ) {

				$lifetime_customer = $affwp_lc->lifetime_customers->get_by( 'affwp_customer_id', $customer_id );

				// Delete and re-add Lifetime customer in db
				if ( $lifetime_customer ) {

					$affwp_lc->lifetime_customers->delete( $lifetime_customer->lifetime_customer_id );

					$args = array(
						'affwp_customer_id' => $customer_id,
						'affiliate_id'      => $sync_affiliate_id,
					);

					$affwp_lc->lifetime_customers->add( $args );

				}

			}

		} else {

			// Create new customer and store affiliate
			$args = array(
				'affiliate_id' => $sync_affiliate_id,
				'first_name'   => $user->first_name,
				'last_name'    => $user->last_name,
				'user_id'      => $user->ID,
				'email'        => $user->user_email,
				'ip'           => affiliate_wp()->tracking->get_ip()
			);

			// Store the affiliate ID with the user.
			$customer_id = affwp_add_customer( $args );

			// Create lifetime customer db record in versions 1.4.1+
			if ( $customer_id && $lc_version_1_4_1 ) {

				$args = array(
					'affwp_customer_id' => $customer_id,
					'affiliate_id'      => $sync_affiliate_id,
				);

				$affwp_lc->lifetime_customers->add( $args );
			}

		}

	} else {

	$customers = $affwp_lc->integrations->get_affiliates_customer_ids( $sync_affiliate_id );

		// Check for existing customer ids
		if ( is_array( $customers ) ) {

			// Add the customer's WordPress user ID to the affiliate if it doesn't already exist
			$affwp_lc->integrations->maybe_add_customer_id_to_affiliate( $user->ID, $sync_affiliate_id );

		} else {
			$affwp_lc->integrations->add_customer_id_to_affiliate( $user_id, $affiliate_id );
		}

		// Store the affiliate's ID against the user
		$affwp_lc->integrations->add_affiliate_id_to_customer( $user->ID, $sync_affiliate_id );

		// Check for existing customer ids
		if ( is_array( $affwp_lc->integrations->get_affiliates_customer_emails( $sync_affiliate_id ) ) ) {

			// Store the newly registered affiliate's email with the referring Affiliate
			$affwp_lc->integrations->maybe_add_email_to_affiliate( $sync_affiliate_id, $user_email );
		} else {
			$affwp_lc->integrations->add_email_to_affiliate( $sync_affiliate_id, $user_email );
		}

	}

}
add_action( 'affwp_mlm_affiliates_connected', 'affwp_mlm_sync_mlm_to_lifetime', 10, 2 );

/**
 * Remove the customer's email address from upline affiliates
 *
 * @since  1.1.2
 */
function affwp_mlm_remove_lifetime_email_from_upline_referrals( $referral_id, $referral, $reference ) {

	if ( ! function_exists( 'affiliate_wp_lifetime_commissions' ) ) return;

	if ( $referral->custom != 'indirect' ) return;

	$affwp_lc = affiliate_wp_lifetime_commissions();

	// Check for Lifetime Commissions version 1.3+
	$lc_version_1_3 = ( true === version_compare( AFFWP_LC_VERSION, '1.3', '>=' ) ) ? true : false;

	if ( $lc_version_1_3 ) return; // Doesn't apply to Lifetime Commissions 1.3+ (No way to delete customer from list)

	$customer_email = ( $lc_version_1_3 ) ? $affwp_lc->integrations->get_email( $reference ) : $affwp_lc->integrations->get( 'email', $reference, $referral->context );

	// If we can't get the email address using the base class method, use the sub class method based on the context
	if ( empty( $customer_email ) ) {

		$integration_class = '';

		if ( $referral->context == 'edd' ) $integration_class = 'Affiliate_WP_Lifetime_Commissions_EDD';

		if ( $referral->context == 'it-exchange' ) $integration_class = 'Affiliate_WP_Lifetime_Commissions_Exchange';

		if ( $referral->context == 'gravityforms' ) $integration_class = 'Affiliate_WP_Lifetime_Commissions_Gravity_Forms';

		if ( $referral->context == 'ninja-forms' ) $integration_class = 'Affiliate_WP_Lifetime_Commissions_Ninja_Forms';

		if ( $referral->context == 'pmp' ) $integration_class = 'Affiliate_WP_Lifetime_Commissions_PMP';

		if ( $referral->context == 'rcp' ) $integration_class = 'Affiliate_WP_Lifetime_Commissions_RCP';

		if ( $referral->context == 'edd' ) $integration_class = 'Affiliate_WP_Lifetime_Commissions_EDD';

		if ( $referral->context == 'woocommerce' ) $integration_class = 'Affiliate_WP_Lifetime_Commissions_WooCommerce';

		if ( $referral->context == 'geodirectory' ) $integration_class = 'Affiliate_WP_Lifetime_Commissions_GeoDirectory';

		do_action( 'affwp_mlm_lc_integration_class', $integration_class, $referral->context );

		if ( empty( $integration_class ) ) return;

		$integration_object = new $integration_class();
		$customer_email = ( $lc_version_1_3 ) ? $integration_object->get_email( $reference ) : $integration_object->get( 'email', $reference, $referral->context );
	}

	if ( empty( $customer_email ) ) return;

	$affiliate_id = $referral->affiliate_id;
	$customer_emails = ( $lc_version_1_3 ) ?  $affwp_lc->integrations->get_customers_for_affiliate( $sync_affiliate_id ): $affwp_lc->integrations->get_affiliates_customer_emails( $affiliate_id );

	// Remove customer email if found in affiliate's customer email list
	if ( in_array( $customer_email, $customer_emails ) ) {
		$affwp_lc->integrations->delete_customer_email_from_affiliate( $affiliate_id, $customer_email );
	}

}
add_action( 'affwp_mlm_complete_referral', 'affwp_mlm_remove_lifetime_email_from_upline_referrals', 10, 3 );
