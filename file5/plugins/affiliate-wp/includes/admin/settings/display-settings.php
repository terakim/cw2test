<?php
/**
 * Admin: Settings
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Settings
 * @copyright   Copyright (c) 2014, Sandhills Development, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * AffWP Admin Header
 *
 * @since 2.9.2
 * @return void
 */
function affwp_admin_header() {
	if ( ! affwp_is_admin_page() ) {
		return;
	}

	$number_notifications     = affiliate_wp()->notifications->count_active_notifications();
	$notifications_panel_file = AFFILIATEWP_PLUGIN_DIR . 'includes/components/notifications/views/notifications-panel.php';

	if ( file_exists( $notifications_panel_file ) ){
		require_once $notifications_panel_file;
	}
	?>
	<div id="affwp-header">
		<div id="affwp-header-wrapper">
			<section class="affwp-header-title">
				<img width="190" height="32" alt="AffiliateWP logo" src="<?php echo AFFILIATEWP_PLUGIN_URL . 'assets/images/logo-affiliatewp.svg'; ?>" />

				<?php if ( 'affiliate-wp-setup-screen' === affwp_get_current_screen() ) : ?>
					<h1><?php echo esc_html__( 'Setup Guide', 'affiliate-wp' ); ?></h1>
				<?php endif; ?>
			</section>
			<div id="affwp-header-actions">
				<button
					id="affwp-notification-button"
					class="affwp-round"
					x-data
					x-init="$store.affwpNotifications.numberActiveNotifications = <?php echo esc_js( $number_notifications ); ?>"
					@click="$store.affwpNotifications.openPanel()"
				>
					<span
						class="affwp-round affwp-number<?php echo 0 === $number_notifications ? ' affwp-hidden' : ''; ?>"
						x-show="$store.affwpNotifications.numberActiveNotifications > 0"
					>
						<?php
						echo wp_kses(
							sprintf(
								/* Translators: %1$s number of notifications; %2$s opening span tag; %3$s closing span tag */
								__( '%1$s %2$sunread notifications%3$s', 'affiliate-wp' ),
								'<span x-text="$store.affwpNotifications.numberActiveNotifications"></span>',
								'<span class="screen-reader-text">',
								'</span>'
							),
							array(
								'span' => array(
									'class' => true,
									'x-text' => true,
								)
							)
						);
						?>
					</span>

					<svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="affwp-notifications-icon"><path fill-rule="evenodd" clip-rule="evenodd" d="M15.8333 2.5H4.16667C3.25 2.5 2.5 3.25 2.5 4.16667V15.8333C2.5 16.75 3.24167 17.5 4.16667 17.5H15.8333C16.75 17.5 17.5 16.75 17.5 15.8333V4.16667C17.5 3.25 16.75 2.5 15.8333 2.5ZM15.8333 15.8333H4.16667V13.3333H7.13333C7.70833 14.325 8.775 15 10.0083 15C11.2417 15 12.3 14.325 12.8833 13.3333H15.8333V15.8333ZM11.675 11.6667H15.8333V4.16667H4.16667V11.6667H8.34167C8.34167 12.5833 9.09167 13.3333 10.0083 13.3333C10.925 13.3333 11.675 12.5833 11.675 11.6667Z" fill="currentColor"></path></svg>
				</button>
				<span class="affwp-round">
					<a href="https://affiliatewp.com/docs/" target="_blank" rel="noopener noreferrer">
						<svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M1.6665 10.0001C1.6665 5.40008 5.39984 1.66675 9.99984 1.66675C14.5998 1.66675 18.3332 5.40008 18.3332 10.0001C18.3332 14.6001 14.5998 18.3334 9.99984 18.3334C5.39984 18.3334 1.6665 14.6001 1.6665 10.0001ZM10.8332 13.3334V15.0001H9.1665V13.3334H10.8332ZM9.99984 16.6667C6.32484 16.6667 3.33317 13.6751 3.33317 10.0001C3.33317 6.32508 6.32484 3.33341 9.99984 3.33341C13.6748 3.33341 16.6665 6.32508 16.6665 10.0001C16.6665 13.6751 13.6748 16.6667 9.99984 16.6667ZM6.6665 8.33341C6.6665 6.49175 8.15817 5.00008 9.99984 5.00008C11.8415 5.00008 13.3332 6.49175 13.3332 8.33341C13.3332 9.40251 12.6748 9.97785 12.0338 10.538C11.4257 11.0695 10.8332 11.5873 10.8332 12.5001H9.1665C9.1665 10.9824 9.9516 10.3806 10.6419 9.85148C11.1834 9.43642 11.6665 9.06609 11.6665 8.33341C11.6665 7.41675 10.9165 6.66675 9.99984 6.66675C9.08317 6.66675 8.33317 7.41675 8.33317 8.33341H6.6665Z" fill="currentColor"></path></svg>
					</a>
				</span>
			</div>
		</div>
	</div>
	<?php
}
add_action( 'in_admin_header', 'affwp_admin_header', 1 );

/**
 * Options Page
 *
 * Renders the options page contents.
 *
 * @since 1.0
 * @return void
 */
function affwp_settings_admin() {

	$active_tab = isset( $_GET[ 'tab' ] ) && array_key_exists( $_GET['tab'], affwp_get_settings_tabs() ) ? $_GET[ 'tab' ] : 'general';

	ob_start();
	?>
	<div class="wrap">
		<h2 class="nav-tab-wrapper">
			<?php affwp_navigation_tabs( affwp_get_settings_tabs(), $active_tab, array( 'settings-updated' => false ) ); ?>
		</h2>
		<div id="tab_container">
			<form method="post" action="options.php">
				<table class="form-table">
				<?php
				settings_fields( 'affwp_settings' );
				affwp_do_settings_fields( 'affwp_settings_' . $active_tab, 'affwp_settings_' . $active_tab );
				?>
				</table>
				<?php submit_button(); ?>
			</form>
		</div><!-- #tab_container-->
	</div><!-- .wrap -->
	<?php
	echo ob_get_clean();
}


/**
 * Retrieves the settings tabs.
 *
 * @since 1.0
 *
 * @return array $tabs Settings tabs.
 */
function affwp_get_settings_tabs() {

	$tabs                    = array();
	$tabs['general']         = __( 'General', 'affiliate-wp' );
	$tabs['integrations']    = __( 'Integrations', 'affiliate-wp' );
	$tabs['opt_in_forms']    = __( 'Opt-In Form', 'affiliate-wp' );
	$tabs['emails']          = __( 'Emails', 'affiliate-wp' );
	$tabs['misc']            = __( 'Misc', 'affiliate-wp' );
	$tabs['payouts_service'] = __( 'Payouts Service', 'affiliate-wp' );

	if ( affwp_get_dynamic_coupons_integrations() ) {
		$tabs['coupons'] = __( 'Coupons', 'affiliate-wp' );
	}

	/**
	 * Filters the list of settings tabs.
	 *
	 * @since 1.0
	 *
	 * @param array $tabs Settings tabs.
	 */
	return apply_filters( 'affwp_settings_tabs', $tabs );
}

/**
 * Forces a license key check anytime the General settings tab is loaded
 *
 * @since 2.1.4
 *
 * @return void
 */
function affwp_check_license_before_settings_load() {

	if( empty( $_GET['page'] ) || 'affiliate-wp-settings' !== $_GET['page'] ) {
		return;
	}

	if( empty( $_GET['tab'] ) ) {
		return;
	}

	$active_tab = isset( $_GET[ 'tab' ] ) && array_key_exists( $_GET['tab'], affwp_get_settings_tabs() ) ? $_GET[ 'tab' ] : 'general';

	if( 'general' === $active_tab && affiliate_wp()->settings->get_license_key() ) {
		affiliate_wp()->settings->check_license( true );
	}

}
add_action( 'admin_init', 'affwp_check_license_before_settings_load', 0 );
