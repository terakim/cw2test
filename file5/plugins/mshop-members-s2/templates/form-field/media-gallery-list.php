<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$classes = mfd_make_class( array (
	'yes' == mfd_get( $element, 'required' ) ? 'required' : '',
	'yes' == mfd_get( $element, 'inline' ) ? 'inline' : '',
	mfd_get( $element, 'class' ),
	mfd_get( $element, 'width', 'sixteen wide' ),
	'field',
	implode( ' ', mfd_get_conditional_class( $element ) )
) );

?>

<script>
	jQuery(document).ready(function ($) {
		var multiple = <?php echo 'yes' == mfd_get( $element, 'multiple' ) ? 'true' : 'false'; ?>;
		var gallery_frame;
		var $image_ids = $('input[name=<?php echo mfd_get( $element, 'name' ); ?>]');
		var $images = $('#<?php echo mfd_get( $element, 'name' ); ?>_container').find('table.attachments');


		function refresh() {
			var image_ids = _.compact($image_ids.val().split(','));

			if (!multiple) {
				var delete_button = $("#<?php echo mfd_get( $element, 'name' ); ?>_container .delete-button-wrapper");

				if (image_ids.length >= 1) {
					$(delete_button).addClass('hide-button');
				} else {
					$(delete_button).removeClass('hide-button');
				}
			}
		}

		var remove_image_handler = function () {
			var element = $(this).closest('tr');
			var image_id = element.data('attachment_id');

			var image_ids = $image_ids.val().split(',');
			image_ids = _.without(image_ids, image_id.toString());
			$image_ids.val(image_ids.join(','));

			element.remove();

			refresh();
		};

		$('.delete-button', $images).on('click', remove_image_handler);

		$('.add_attachments', $images).on('click', function (event) {
			var $el = $(this);

			event.preventDefault();

			// If the media frame already exists, reopen it.
			if (gallery_frame) {
				gallery_frame.open();
				return;
			}

			// Create the media frame.
			gallery_frame = wp.media.frames.product_gallery = wp.media({
				// Set the title of the modal.
				title: $el.data('choose'),
				button: {
					text: $el.data('update')
				},
				states: [
					new wp.media.controller.Library({
						title: $el.data('choose'),
						filterable: 'all',
						multiple: multiple,
						autoSelect: true,
						contentUserSetting: false
					})
				]
			});

			// When an image is selected, run a callback.
			gallery_frame.on('select', function () {
				var selection = gallery_frame.state().get('selection');
				var attachment_ids = $image_ids.val();

				selection.map(function (attachment) {

					attachment = attachment.toJSON();

					if (attachment.id) {
						attachment_ids = attachment_ids ? attachment_ids + ',' + attachment.id : attachment.id;
						$('<tr data-attachment_id="' + attachment.id + '"><td>' + attachment.title + '</td><td class="attachment_row"><i class="remove circle icon delete-button"></i></td></tr>').insertBefore($('.add_attachments', '#<?php echo mfd_get( $element, 'name' ); ?>_container').closest('tr'));
					}
				});

				$image_ids.val(attachment_ids);

				$('.delete-button', $images).off('click');
				$('.delete-button', $images).on('click', remove_image_handler);

				refresh();
			});

			<?php if ( ! empty( mfd_get( $element, 'upload_dir' ) ) ) : ?>
			gallery_frame.on('ready', function () {
				gallery_frame.uploader.options.uploader.params = {
					type: '<?php echo mfd_get( $element, 'upload_dir' ); ?>'
				};
			});
			<?php endif; ?>

			// Finally, open the modal.
			gallery_frame.open();
		});

		refresh();
	});
</script>
<style>
	.attachments .add_attachments,
	.attachments .delete-button {
		cursor: pointer !important;
	}

	.attachments .attachment_row {
		font-size: 24px;
		padding: 0px;
		width: 25px;
		text-align: center;
		color: darkgrey;
	}

	.attachments .add_rows {
		font-size: 24px;
		padding: 0px;
		text-align: center;
		color: darkgrey;
	}

	.delete-button-wrapper.hide-button {
		display: none;
	}
</style>

<div class="<?php echo $classes; ?>">
	<?php mfd_output_title( $element ); ?>
	<div id="<?php echo mfd_get( $element, 'name' ); ?>_container">
		<table class="attachments">
			<?php
			$image_ids = explode( ',', $value );

			$image_ids = array_filter( $image_ids );

			if ( ! empty( $image_ids ) ) {
				foreach ( $image_ids as $image_id ) {
					$filename = $filename_only = basename( get_attached_file( $image_id ) );
					echo '<tr data-attachment_id="' . $image_id . '">';
					echo '<td>' . $filename . '</td>';
					echo '<td class="attachment_row"><i class="remove circle icon delete-button"></i></td>';
					echo '</tr>';
				}
			}
			?>
			<tr class="delete-button-wrapper">
				<td colspan="2" class="add_rows"><i class="add circle icon add_attachments"></i></td>
			</tr>
		</table>

		<input type="hidden"
		       name="<?php echo mfd_get( $element, 'name' ); ?>"
		       value="<?php echo $value; ?>"/>
	</div>
</div>
