<?php

/**
 * Remove Indirect Referrals from the Direct Referrals List in the Affiliate Area
 *
 * @since  1.1.2
 */
function affwp_mlm_remove_indirect_referrals_from_referrals_tab ( $referrals ) {
	
	if ( $referrals ) {

		foreach ( $referrals as $referral ) {
			// Remove Indirect Referrals from Direct Referrals Table
			if ( $referral->custom == 'indirect' ) {
				continue;
			}	
		}
	}
}
add_filter( 'affwp_dashboard_referrals', 'affwp_mlm_remove_indirect_referrals_from_referrals_tab', 10, 1 );

/**
 * Add Email Tags for MLM Emails
 *
 * @since  1.1.4
 */
function affwp_mlm_add_email_tags( $email_tags, $emails_object ) {

	$mlm_tags = array(
		
		// Parent Affiliate
		array(
			'tag'         => 'parent_affiliate_name',
			'description' => __( 'The name of the affiliate\'s parent affiliate.', 'affiliatewp-multi-level-marketing' ),
			'function'    => 'affwp_mlm_email_tag_parent_affiliate_name'
		),		
		array(
			'tag'         => 'parent_affiliate_username',
			'description' => __( 'The user name of the affiliate\'s parent affiliate.', 'affiliatewp-multi-level-marketing' ),
			'function'    => 'affwp_mlm_email_tag_parent_affiliate_user_name'
		),
		array(
			'tag'         => 'parent_affiliate_user_email',
			'description' => __( 'The email address of the affiliate\'s parent affiliate.', 'affiliatewp-multi-level-marketing' ),
			'function'    => 'affwp_mlm_email_tag_parent_affiliate_user_email'
		),	
		array(
			'tag'         => 'parent_affiliate_id',
			'description' => __( 'The ID of the affiliate\'s parent affiliate.', 'affiliatewp-multi-level-marketing' ),
			'function'    => 'affwp_mlm_email_tag_parent_affiliate_id'
		),
		
		// Direct Affiliate
		array(
			'tag'         => 'direct_affiliate_name',
			'description' => __( 'The name of the affiliate\'s direct affiliate.', 'affiliatewp-multi-level-marketing' ),
			'function'    => 'affwp_mlm_email_tag_direct_affiliate_name'
		),		
		array(
			'tag'         => 'direct_affiliate_username',
			'description' => __( 'The user name of the affiliate\'s direct affiliate.', 'affiliatewp-multi-level-marketing' ),
			'function'    => 'affwp_mlm_email_tag_direct_affiliate_user_name'
		),
		array(
			'tag'         => 'direct_affiliate_user_email',
			'description' => __( 'The email address of the affiliate\'s direct affiliate.', 'affiliatewp-multi-level-marketing' ),
			'function'    => 'affwp_mlm_email_tag_direct_affiliate_user_email'
		),	
		array(
			'tag'         => 'direct_affiliate_id',
			'description' => __( 'The ID of the affiliate\'s direct affiliate.', 'affiliatewp-multi-level-marketing' ),
			'function'    => 'affwp_mlm_email_tag_direct_affiliate_id'
		),
		
		// Direct Referral
		array(
			'tag'         => 'direct_referral_affiliate_name',
			'description' => __( 'The name of the affiliate that directly referred the sale.', 'affiliatewp-multi-level-marketing' ),
			'function'    => 'affwp_mlm_email_tag_direct_referral_affiliate_name'
		),		
		array(
			'tag'         => 'direct_referral_affiliate_username',
			'description' => __( 'The user name of the affiliate that directly referred the sale.', 'affiliatewp-multi-level-marketing' ),
			'function'    => 'affwp_mlm_email_tag_direct_referral_affiliate_user_name'
		),
		array(
			'tag'         => 'direct_referral_affiliate_user_email',
			'description' => __( 'The email address of the affiliate that directly referred the sale.', 'affiliatewp-multi-level-marketing' ),
			'function'    => 'affwp_mlm_email_tag_direct_referral_affiliate_user_email'
		),	
		array(
			'tag'         => 'direct_referral_affiliate_id',
			'description' => __( 'The ID of the affiliate that directly referred the sale.', 'affiliatewp-multi-level-marketing' ),
			'function'    => 'affwp_mlm_email_tag_direct_referral_affiliate_id'
		),
		array(
			'tag'         => 'direct_referral_amount',
			'description' => __( 'The amount of the order\'s direct referral.', 'affiliatewp-multi-level-marketing' ),
			'function'    => 'affwp_mlm_email_tag_direct_referral_amount'
		)		

	);
	
	$email_tags = array_merge( $email_tags, $mlm_tags );
	return $email_tags;
}
add_filter( 'affwp_email_tags', 'affwp_mlm_add_email_tags', 10, 2 );

/**
 * Prevent Sending Duplicate New Referral Email Notifications for Indirect Referrals
 *
 * @since  1.1.4
 */
function affwp_mlm_prevent_notify_on_new_indirect_referral( $can_send, $referral ) {

	// Only apply to Indirect Referrals
	if ( $referral->custom != 'indirect' ) return $can_send;
	
	$affiliate_id = $referral->affiliate_id;
	
	// Don't send to this affiliate if indirect referral notifications are enabled
	if ( affwp_email_notification_enabled( 'affiliate_new_indirect_referral_email', $affiliate_id ) ) return false;		

	return $can_send;
	
}
add_filter( 'affwp_notify_on_new_referral', 'affwp_mlm_prevent_notify_on_new_indirect_referral', 10, 2 );
