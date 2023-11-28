<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Sends Upline Emails on New Sub Affiliate Connection
 *
 * @since 1.1.4
 */
function affwp_mlm_notify_on_new_sub_affiliate( $affiliate_id = 0, $mlm_data = array() ) {

	if ( empty( $affiliate_id ) ) return;	
	
	// Get new sub affiliate's upline
	$upline = affwp_mlm_get_upline( $affiliate_id );

	if ( $upline ) {

		$parent_affiliates = $upline;

		foreach( $parent_affiliates as $parent_id ) {
			
			affwp_mlm_send_new_sub_affiliate_email( $parent_id, $affiliate_id );

		}
	}

}
add_action( 'affwp_mlm_affiliates_connected', 'affwp_mlm_notify_on_new_sub_affiliate', 10, 2 );

/**
 * Sends the Parent Affiliate an Email for Earning a New Indirect Referral
 *
 * @since  1.1.4
 */
function affwp_mlm_notify_on_new_indirect_referral( $affiliate_id = 0, $referral ) {

	if ( empty( $affiliate_id ) || empty( $referral ) ) return;
	
	if ( $referral->custom != 'indirect' ) return; // Only run for indirect referrals
	
	// Don't send to this affiliate if notification isn't enabled
	if ( ! affwp_email_notification_enabled( 'affiliate_new_indirect_referral_email', $affiliate_id ) ) return;
	
	//$user_id = affwp_get_affiliate_user_id( $affiliate_id );
	
	// Don't send if the notification is disabled by the affiliate
	//if ( ! get_user_meta( $user_id, 'affwp_referral_notifications', true ) ) return;	
	
	$emails           = new Affiliate_WP_Emails;
	$emails->__set( 'affiliate_id', $affiliate_id );
	$emails->__set( 'referral', $referral );

	$email            = affwp_get_affiliate_email( $affiliate_id );
	$default_subject  = __( 'Indirect Referral Awarded!', 'affiliatewp-multi-level-marketing' );
	$subject 		  = affiliate_wp()->settings->get( 'affwp_mlm_new_indirect_referral_email_subject', $default_subject );
	$default_message  = affiliate_wp_mlm()->settings->get_new_indirect_referral_default_message();
	$message          = affiliate_wp()->settings->get( 'affwp_mlm_new_indirect_referral_email_message', $default_message );	
	
	$amount  		  = html_entity_decode( affwp_currency_filter( $referral->amount ), ENT_COMPAT, 'UTF-8' );
	
	// $args is setup for backwards compatibility with < 1.6
	$args    = array( 'affiliate_id' => $affiliate_id, 'amount' => $referral->amount, 'referral' => $referral );
	$subject = apply_filters( 'affwp_mlm_new_indirect_referral_email_subject', $subject, $args );
	$message = apply_filters( 'affwp_mlm_new_indirect_referral_email_message', $message, $args );

	$emails->send( $email, $subject, $message );	
	
}
add_action( 'affwp_referral_accepted', 'affwp_mlm_notify_on_new_indirect_referral', 10, 2 );

/**
 * Add a Field to the Affiliate Area for Enabling/Disabling New Sub Affiliate Emails (Per-User)
 *
 * @since 1.1.4
 */
function affwp_mlm_add_notification_field_new_sub_affiliate( $affiliate_id = 0, $affiliate_user_id = 0 ) {

	if ( empty( $affiliate_id ) || empty( $affiliate_user_id ) ) return; ?>
	
	<?php if ( affwp_email_notification_enabled( 'affiliate_new_sub_affiliate_email', $affiliate_id ) ) : ?>
	<div class="affwp-wrap affwp-send-notifications-wrap">
		<input id="affwp-sub-affiliate-notifications" type="checkbox" name="sub_affiliate_notifications" value="1" <?php checked( true, get_user_meta( $affiliate_user_id, 'affwp_mlm_sub_affiliate_notifications', true ) ); ?>/>
		<label for="sub-affiliate-notifications"><?php _e( 'Enable New Sub Affiliate Notifications', 'affiliatewp-multi-level-marketing' ); ?></label>
	</div>
	<?php endif;

}
//add_action( 'affwp_affiliate_dashboard_before_submit', 'affwp_mlm_add_notification_field_new_sub_affiliate', 10, 2 );
