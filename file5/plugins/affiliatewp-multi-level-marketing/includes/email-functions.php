<?php

/**
 * Email template tag: parent_affiliate_name
 * The parent affiliate's name
 *
 * @return string name
 */
function affwp_mlm_email_tag_parent_affiliate_name( $affiliate_id = 0 ) {
	
	$parent_affiliate_id = affwp_mlm_get_parent_affiliate( $affiliate_id );
	
	return affiliate_wp()->affiliates->get_affiliate_name( $parent_affiliate_id );
}

/**
 * Email template tag: parent_affiliate_username
 * The parent affiliate's username on the site
 *
 * @return string username
 */
function affwp_mlm_email_tag_parent_affiliate_user_name( $affiliate_id = 0 ) {
	
	$parent_affiliate_id = affwp_mlm_get_parent_affiliate( $affiliate_id );
	$user_info = get_userdata( affwp_get_affiliate_user_id( $parent_affiliate_id ) );

	return $user_info->user_login;
}

/**
 * Email template tag: parent_affiliate_user_email
 * The parent affiliate's email
 *
 * @return string email
 */
function affwp_mlm_email_tag_parent_affiliate_user_email( $affiliate_id = 0 ) {
	
	$parent_affiliate_id = affwp_mlm_get_parent_affiliate( $affiliate_id );
	
	return affwp_get_affiliate_email( $parent_affiliate_id );
}

/**
 * Email template tag: parent_affiliate_id
 * Parent Affiliate's ID
 *
 * @return int affiliate ID
 */
function affwp_mlm_email_tag_parent_affiliate_id( $affiliate_id = 0 ) {
	
	$parent_affiliate_id = affwp_mlm_get_parent_affiliate( $affiliate_id );
	
	return $parent_affiliate_id;
}

/**
 * Email template tag: direct_affiliate_name
 * The direct affiliate's name
 *
 * @return string name
 */
function affwp_mlm_email_tag_direct_affiliate_name( $affiliate_id = 0 ) {
	
	$direct_affiliate_id = affwp_mlm_get_direct_affiliate( $affiliate_id );
		
	return affiliate_wp()->affiliates->get_affiliate_name( $direct_affiliate_id );
}

/**
 * Email template tag: direct_affiliate_username
 * The direct affiliate's username on the site
 *
 * @return string username
 */
function affwp_mlm_email_tag_direct_affiliate_user_name( $affiliate_id = 0 ) {
	
	$direct_affiliate_id = affwp_mlm_get_direct_affiliate( $affiliate_id );
	$user_info = get_userdata( affwp_get_affiliate_user_id( $direct_affiliate_id ) );

	return $user_info->user_login;
}

/**
 * Email template tag: direct_affiliate_user_email
 * The direct affiliate's email
 *
 * @return string email
 */
function affwp_mlm_email_tag_direct_affiliate_user_email( $affiliate_id = 0 ) {
	
	$direct_affiliate_id = affwp_mlm_get_direct_affiliate( $affiliate_id );
	
	return affwp_get_affiliate_email( $direct_affiliate_id );
}

/**
 * Email template tag: direct_affiliate_id
 * Direct Affiliate's ID
 *
 * @return int affiliate ID
 */
function affwp_mlm_email_tag_direct_affiliate_id( $affiliate_id = 0 ) {
	
	$direct_affiliate_id = affwp_mlm_get_direct_affiliate( $affiliate_id );
	
	return $direct_affiliate_id;
}

/**
 * Email template tag: direct_referral_affiliate_name
 * The Name of the Affiliate for the Original Affiliate Transaction for the Order (Direct Referral)
 *
 * @return string name
 */
function affwp_mlm_email_tag_direct_referral_affiliate_name( $affiliate_id = 0, $referral ) {
	
	$referrals = affwp_mlm_get_referrals_for_order( $referral->reference, $referral->context );
	$direct_referral = $referrals[0]; // Get the original direct referral for this order
	$direct_referral_aff_id = $direct_referral->affiliate_id;	
	
	return affiliate_wp()->affiliates->get_affiliate_name( $direct_referral_aff_id );
}

/**
 * Email template tag: direct_referral_affiliate_username
 * The Username of the Affiliate for the Original Affiliate Transaction for the Order (Direct Referral)
 *
 * @return string username
 */
function affwp_mlm_email_tag_direct_referral_affiliate_user_name( $affiliate_id = 0, $referral ) {
	
	$referrals = affwp_mlm_get_referrals_for_order( $referral->reference, $referral->context );
	$direct_referral = $referrals[0]; // Get the original direct referral for this order
	$user_info = get_userdata( affwp_get_affiliate_user_id( $direct_referral->affiliate_id ) );

	return $user_info->user_login;
}

/**
 * Email template tag: direct_referral_affiliate_id
 * The Affiliate ID for the Original Affiliate Transaction for the Order (Direct Referral)
 *
 * @return int affiliate ID
 */
function affwp_mlm_email_tag_direct_referral_affiliate_id( $affiliate_id = 0, $referral ) {
	
	$referrals = affwp_mlm_get_referrals_for_order( $referral->reference, $referral->context );
	$direct_referral = $referrals[0]; // Get the original direct referral for this order
	
	return $direct_referral->affiliate_id;
}

/**
 * Email template tag: direct_referral_amount
 * The Amount of the Original Affiliate Transaction for the Order (Direct Referral)
 *
 * @return int affiliate ID
 */
function affwp_mlm_email_tag_direct_referral_amount( $affiliate_id = 0, $referral ) {
	
	$referrals = affwp_mlm_get_referrals_for_order( $referral->reference, $referral->context );
	$direct_referral = $referrals[0]; // Get the original direct referral for this order
	
	return html_entity_decode( affwp_currency_filter( $direct_referral->amount ), ENT_COMPAT, 'UTF-8' );
}


/**
 * Sends the Parent Affiliate an Email for a New Sub Affiliate
 *
 * @since  1.1.4
 */
function affwp_mlm_send_new_sub_affiliate_email( $parent_affiliate_id = 0, $sub_affiliate_id = 0 ) {

	if ( empty( $parent_affiliate_id ) || empty( $sub_affiliate_id ) ) return;	
	
	// Don't send to this parent if notification isn't enabled
	if ( ! affwp_email_notification_enabled( 'affiliate_new_sub_affiliate_email', $parent_affiliate_id ) ) return;
	
	//$user_id = affwp_get_affiliate_user_id( $parent_affiliate_id );
	
	// Don't send if the notification is disabled by the affiliate
	//if ( ! get_user_meta( $user_id, 'affwp_mlm_sub_affiliate_notifications', true ) ) return;	
	
	$emails           = new Affiliate_WP_Emails;
	$emails->__set( 'affiliate_id', $sub_affiliate_id ); // Used to display sub affiliate's info via email tags

	$email            = affwp_get_affiliate_email( $parent_affiliate_id );
	$default_subject  = __( 'New Sub Affiliate', 'affiliatewp-multi-level-marketing' );
	$subject 		  = affiliate_wp()->settings->get( 'affwp_mlm_new_sub_affiliate_email_subject', $default_subject );
	$default_message  = affiliate_wp_mlm()->settings->get_new_sub_affiliate_default_message();
	$message          = affiliate_wp()->settings->get( 'affwp_mlm_new_sub_affiliate_email_message', $default_message );

	// $args is setup for backwards compatibility with < 1.6
	$args    = array( 'affiliate_id' => $sub_affiliate_id, 'parent_affiliate_id' => $parent_affiliate_id );
	$subject = apply_filters( 'affwp_mlm_new_sub_affiliate_email_subject', $subject, $args );
	$message = apply_filters( 'affwp_mlm_new_sub_affiliate_email_message', $message, $args );	
	
	$emails->send( $email, $subject, $message );	
	
}
