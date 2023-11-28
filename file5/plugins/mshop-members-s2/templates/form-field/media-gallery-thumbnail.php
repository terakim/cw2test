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
		var gallery_frame;
		var $image_ids = $('input[name=<?php echo mfd_get( $element, 'name' ); ?>]');
		var $images = $('#<?php echo mfd_get( $element, 'name' ); ?>_container').find('ul.product_images');


		var remove_image_handler = function () {
			var element = $(this).closest('li');
			var image_id = element.data('attachment_id');

			var image_ids = $image_ids.val().split(',');
			image_ids = _.without(image_ids, image_id.toString());
			$image_ids.val(image_ids.join(','));

			element.remove();
		};

		$('.delete-button', $images).on('click', remove_image_handler);

		$('.add_images', $images).on('click', function (event) {
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
						multiple: true,
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
						var attachment_image = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

						$('<li class="image" data-attachment_id="' + attachment.id + '"><div class="msm_thumbnail" style="background-image : url(' + attachment_image + ')"><i class="remove circle icon delete-button"></i></div></li>').insertBefore($('.add_images', '#<?php echo mfd_get( $element, 'name' ); ?>_container'));
					}
				});

				$image_ids.val(attachment_ids);

				$('.delete-button', $images).off('click');
				$('.delete-button', $images).on('click', remove_image_handler);
			});

			// Finally, open the modal.
			gallery_frame.open();
		});

		$images.sortable({
			items: 'li.image:not(.ui-state-disabled)',
			cursor: 'move',
			scrollSensitivity: 40,
			forcePlaceholderSize: true,
			forceHelperSize: false,
			helper: 'clone',
			opacity: 0.65,
			placeholder: 'msm-sortable-placeholder',
			start: function (event, ui) {
				ui.item.css('background-color', '#f6f6f6');
			},
			stop: function (event, ui) {
				ui.item.removeAttr('style');
			},
			update: function () {
				var attachment_ids = [];

				$('#<?php echo mfd_get( $element, 'name' ); ?>_container').find('ul li.image').each(function () {
					var attachment_id = $(this).attr('data-attachment_id');
					if (attachment_id) {
						attachment_ids.push(attachment_id);
					}
				});

				$image_ids.val(attachment_ids.join(','));
			}
		});
	});
</script>
<style>
	.add_images {
		cursor: pointer !important;
	}

	.msm-sortable-placeholder {
		width: 120px;
		height: 120px;
		float: left;
		list-style-type: none;
		padding: 2px;
		cursor: pointer;
		background-image: url('http://192.168.10.194/samdi/wp-content/plugins/mshop-members-s2/assets/images/sortable-placeholder.png');
		background-size: contain;
	}

	.product_images .image {
		float: left;
		list-style-type: none;
		cursor: pointer;
	}

	.product_images img {
		padding: 2px;
		width: 100px;
		height: 100px;
	}

	.msm_thumbnail {
		width: 120px;
		height: 120px;
		background-size: cover;
		background-repeat: no-repeat;
		background-position: center;
		margin: 2px;
	}

	.msm_thumbnail .delete-button {
		float: right;
		font-size: 24px;
		color: darkgrey;
	}
</style>
<div class="<?php echo $classes; ?>">
	<?php mfd_output_title( $element ); ?>
	<div id="<?php echo mfd_get( $element, 'name' ); ?>_container">
		<ul class="product_images">
			<?php
			$image_ids = explode( ',', $value );

			$image_ids = array_filter( $image_ids );

			if ( ! empty( $image_ids ) ) {
				foreach ( $image_ids as $image_id ) {
					$image = wp_get_attachment_image_src( $image_id );
					echo '<li class="image" data-attachment_id="' . $image_id . '"><div class="msm_thumbnail" style="background-image : url(\'' . $image[0] . '\');" ><i class="remove circle icon delete-button"></i></div></li>';
				}
			}

			?>
			<li class="image ui-state-disabled add_images">
				<div class="msm_thumbnail"
				     style="background-image : url('<?php echo MSM()->plugin_url(); ?>/assets/images/placeholder.png')">
			</li>
		</ul>

		<input type="hidden"
		       name="<?php echo mfd_get( $element, 'name' ); ?>"
		       value="<?php echo $value; ?>"/>
	</div>
</div>
