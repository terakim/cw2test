<?php
/**
 * Admin: New Creative View
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Creatives
 * @copyright   Copyright (c) 2014, Sandhills Development, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.2
 */
use AffWP\Core\License\License_Data;

?>
<div class="wrap">

	<h2><?php esc_html_e( 'New Creative', 'affiliate-wp' ); ?></h2>

	<form method="post" id="affwp_add_creative">

		<?php
		/**
		 * Fires at the top of the new-creative admin screen.
		 *
		 * @since 1.0
		 */
		do_action( 'affwp_new_creative_top' );
		?>

		<p><?php esc_html_e( 'Use this screen to add a new creative, such as a text link or image banner.', 'affiliate-wp' ); ?></p>

		<table class="form-table" data-current-context="image">

			<tr class="form-row form-required" data-row="name">

				<th scope="row">
					<label for="name"><?php esc_html_e( 'Name', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input type="text" name="name" id="name" required class="regular-text" />
					<p class="description"><?php esc_html_e( 'The name of this creative. Use this to briefly describe the creative to your affiliates.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<?php

			/**
			 * Fires before description.
			 *
			 * @since 2.12.0
			 */
			do_action( 'affwp_new_before_description' );

			?>

			<tr class="form-row form-required" data-row="description">

				<th scope="row">
					<label for="description"><?php esc_html_e( 'Description', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<textarea name="description" rows="5" cols="50" id="description" class="large-text"></textarea>
					<p class="description"><?php esc_html_e( 'An optional description for this creative. Use this to provide additional information about the creative to your affiliates.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row form-required" data-row="type">
				<th scope="row">
					<label for="type"><?php esc_html_e( 'Type', 'affiliate-wp' ); ?></label>
				</th>
				<td>
					<select name="type" id="type">
						<?php foreach ( affwp_get_creative_types() as $creative_type => $label ) : ?>
							<option value="<?php echo esc_attr( $creative_type ); ?>">
								<?php echo esc_html( $label ); ?>
							</option>
						<?php endforeach; ?>
					</select>
					<p class="description"><?php esc_html_e( 'Select the type of the creative.', 'affiliate-wp' ); ?></p>
				</td>
			</tr>

			<tr class="form-row form-required" data-row="image">

				<th scope="row">
					<label for="image"><?php esc_html_e( 'Image', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input id="image" name="image" type="text" required class="upload_field regular-text" />
					<input class="upload_image_button button-secondary" type="button" value="Choose Image" />
					<p class="description"><?php esc_html_e( 'Select your image. You can also enter an image URL if your image is hosted elsewhere.', 'affiliate-wp' ); ?></p>

					<div id="preview_image"></div>
				</td>

			</tr>

			<tr class="form-row form-required" data-row="text">

				<th scope="row">
					<label for="text" data-context="text_link"><?php esc_html_e( 'Text', 'affiliate-wp' ); ?></label>
					<label for="text" data-context="image"><?php esc_html_e( 'Alt Text', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input type="text" name="text" id="text" class="regular-text" maxlength="255" />
					<p class="description" data-context="text_link"><?php _e( 'Text for this creative.', 'affiliate-wp' ); ?></p>
					<p class="description" data-context="image"><?php esc_html_e( "Enter descriptive text for the image's alternative text (alt text).", 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row form-required" data-row="url">

				<th scope="row">
					<label for="url"><?php esc_html_e( 'URL', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input type="text" name="url" id="url" class="regular-text" />
					<p class="description"><?php esc_html_e( 'The URL this creative should link to. Based on your Referral Settings, the affiliate&#8217;s ID or username will be automatically appended.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<?php affwp_creative_privacy_toggle(); ?>

			<?php

			/**
			 * Fires before status row.
			 *
			 * @since 2.13.0
			 */
			do_action( 'affwp_new_creative_before_status' );

			?>

			<tr class="form-row form-required" data-row="status">

				<th scope="row">
					<label for="status"><?php esc_html_e( 'Status', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<select name="status" id="status">
						<option value="active"><?php esc_html_e( 'Active', 'affiliate-wp' ); ?></option>
						<option value="inactive"><?php esc_html_e( 'Inactive', 'affiliate-wp' ); ?></option>
						<option value="scheduled" class="<?php echo true === affwp_is_upgrade_required( 'pro' ) ? 'addProBadge' : '' ?>"><?php esc_html_e( 'Scheduled', 'affiliate-wp' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'Select the status of the creative. A creative can be Active, Inactive, or Scheduled.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row affwp-schedule-creatives-setting affwp-hidden" data-row="schedule">

				<th scope="row">
					<label><?php esc_html_e( 'Schedule', 'affiliate-wp' ); ?></label>
				</th>

				<td class="schedule-creative-date-fields">
					<?php if ( true === affwp_is_upgrade_required( 'pro' ) ) : ?>
						<div class="affwp-upgrade-setting-cta">
							<p>
								<?php
								echo esc_html( 'Scheduling allows you to set start and end dates for your creatives, giving you more flexibility and control over your affiliate campaigns.', 'affiliate-wp' )
								?>
							</p>
							<h4>
								<a href="<?php echo esc_url( affwp_admin_upgrade_link( 'affiliatewp-creatives-new', 'Upgrade to AffiliateWP Pro' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Upgrade to AffiliateWP Pro to Unlock Creative Scheduling', 'affiliate-wp' ); ?></a>
							</h4>
						</div>
					<?php else : ?>
						<p class="description"><?php esc_html_e( "Set a specific time frame for your creative's visibility.", 'affiliate-wp' ); ?></p>
					<?php endif; ?>
					<div>
						<input type="text" class="affwp-schedule-creative-datepicker" autocomplete="off" name="start_date"
							placeholder="<?php esc_html_e( 'mm/dd/yyyy', 'affiliate-wp' ); ?>"
							<?php echo esc_attr( true === affwp_is_upgrade_required( 'pro' ) ? 'disabled' : '' ); ?> />
						<p class="description"><?php esc_html_e( 'Start date.', 'affiliate-wp' ); ?></p>
					</div>
					<div>
						<input type="text" class="affwp-schedule-creative-datepicker" autocomplete="off" name="end_date"
							placeholder="<?php esc_html_e( 'mm/dd/yyyy', 'affiliate-wp' ); ?>"
							<?php  echo esc_attr( true === affwp_is_upgrade_required( 'pro' ) ? 'disabled' : '' ); ?>/>
						<p class="description"><?php esc_html_e( 'End date.', 'affiliate-wp' ); ?></p>
					</div>
				</td>

			</tr>

			<tr class="form-row">

				<th scope="row">
					<label for="notes"><?php esc_html_e( 'Notes', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<textarea name="notes" rows="5" cols="50" id="notes" class="large-text"></textarea>
					<p class="description"><?php esc_html_e( 'Enter any notes for this creative. Notes are only visible to an affiliate manager.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

		</table>

		<?php
		/**
		 * Fires at the bottom of the new-creative admin screen.
		 *
		 * @since 1.0
		 */
		do_action( 'affwp_new_creative_bottom' );

		?>

		<input type="hidden" name="affwp_action" value="add_creative" />

		<?php submit_button( __( 'Add Creative', 'affiliate-wp' ) ); ?>

	</form>

</div>
