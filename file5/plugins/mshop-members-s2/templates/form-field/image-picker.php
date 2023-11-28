<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$classes = mfd_make_class( array(
	'yes' == mfd_get( $element, 'required' ) ? 'required' : '',
	'yes' == mfd_get( $element, 'inline' ) ? 'inline' : '',
	mfd_get( $element, 'class' ),
	mfd_get( $element, 'width', 'sixteen wide' ),
	'field',
	implode( ' ', mfd_get_conditional_class( $element ) )
) );

if ( $value ) {
	if ( is_scalar( $value ) && wp_attachment_is_image( $value ) ) {
	    $image_src = wp_get_attachment_image_src( $value );
        if( ! empty( $image_src ) ) {
	        $image = current( $image_src );
        }
	} else if ( is_string( $value ) ) {
		$image = $value;
	}
} else {
	if ( 'yes' == mfd_get( $element, 'avatar', 'no' ) ) {
		if ( function_exists( 'wsl_get_user_custom_avatar' ) ) {
			$image = wsl_get_user_custom_avatar( get_current_user_id() );
		} else {
			$image = get_avatar_url( get_current_user_id() );
		}
	} else {
		$image = mfd_get( $element, 'placeholder' );
	}
}

if ( empty( $image ) ) {
	$image = MSM()->plugin_url() . '/assets/images/placeholder.png';
}

if ( 'yes' == mfd_get( $element, 'readonly' ) ) {
	?>
    <style>
        #<?php echo mfd_get( $element, 'name' ); ?>_image_picker_container .add_images {
            cursor: pointer !important;
        }

        #<?php echo mfd_get( $element, 'name' ); ?>_image_picker_container .product_images .image {
            float: left;
            list-style-type: none;
            cursor: pointer;
        }

        #<?php echo mfd_get( $element, 'name' ); ?>_image_picker_container .product_images img {
            padding: 2px;
            width: 100px;
            height: 100px;
        }

        #<?php echo mfd_get( $element, 'name' ); ?>_image_picker_container .msm_thumbnail {
            width: <?php echo mfd_get( $element, 'size' ); ?>px;
            height: <?php echo mfd_get( $element, 'size' ); ?>px;
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            margin: 2px;
        }

        #<?php echo mfd_get( $element, 'name' ); ?>_image_picker_container .image_picker {
            margin: 0 auto;
            border-radius: <?php echo mfd_get( $element, 'radius' ); ?>px;
        }

        #<?php echo mfd_get( $element, 'name' ); ?>_image_picker_container .msm_thumbnail .delete-button {
            float: right;
            font-size: 24px;
            color: darkgrey;
        }
    </style>
    <div class="<?php echo $classes; ?>">
		<?php if ( ! empty( $element['title'] ) ) : ?>
            <label><?php _e( $element['title'] ); ?></label>
		<?php endif; ?>
        <div id="<?php echo mfd_get( $element, 'name' ); ?>_image_picker_container">
            <div class="msm_thumbnail image_picker add_images" style="background-image : url('<?php echo $image; ?>')">
                <input type="hidden"
                       name="<?php echo mfd_get( $element, 'name' ); ?>"
                       value="<?php echo $value; ?>"/>
            </div>
        </div>
    </div>
	<?php
	return;
}

?>
<script>
    jQuery( document ).ready( function ( $ ) {
        var placeholder_url = "<?php echo MSM()->plugin_url(); ?>/assets/images/placeholder.png";
        var gallery_frame;
        var $image_ids      = $( 'input[name=<?php echo mfd_get( $element, 'name' ); ?>]' );
        var $images         = $( '#<?php echo mfd_get( $element, 'name' ); ?>_image_picker_container' );


        var remove_image_handler = function ( e ) {
            e.preventDefault();
            e.stopPropagation();
            $( '.msm_thumbnail', $images ).css( 'background-image', "url('" + placeholder_url + "')" );
            $( '.delete-button', $images ).css( 'display', 'none' );
            $image_ids.val( '' );

        };

        $( '.delete-button', $images ).on( 'click', remove_image_handler );

        $( '.add_images', $images ).on( 'click', function ( event ) {
            var $el = $( this );

            event.preventDefault();

            // If the media frame already exists, reopen it.
            if (gallery_frame) {
                gallery_frame.open();
                return;
            }

            // Create the media frame.
            gallery_frame = wp.media.frames.product_gallery = wp.media( {
                // Set the title of the modal.
                title: $el.data( 'choose' ),
                button: {
                    text: $el.data( 'update' )
                },
                states: [
                    new wp.media.controller.Library( {
                        title: $el.data( 'choose' ),
                        filterable: 'all',
                        multiple: true,
                        autoSelect: true
                    } )
                ]
            } );

            // When an image is selected, run a callback.
            gallery_frame.on( 'select', function () {
                var selection      = gallery_frame.state().get( 'selection' );
                var attachment_ids = $image_ids.val();

                if (selection.first()) {
                    var attachment       = selection.first().toJSON();
                    var attachment_image = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                    $( '.msm_thumbnail', $images ).css( 'background-image', "url('" + attachment_image + "')" );


                    $( '.delete-button', $images ).off( 'click' );
                    $( '.delete-button', $images ).on( 'click', remove_image_handler );
                    $( '.delete-button', $images ).css( 'display', 'block' );
                    $image_ids.val( attachment.id );
                }

            } );

            // Finally, open the modal.
            gallery_frame.open();
        } );

        $images.sortable( {
            items: 'li.image:not(.ui-state-disabled)',
            cursor: 'move',
            scrollSensitivity: 40,
            forcePlaceholderSize: true,
            forceHelperSize: false,
            helper: 'clone',
            opacity: 0.65,
            placeholder: 'msm-sortable-placeholder',
            start: function ( event, ui ) {
                ui.item.css( 'background-color', '#f6f6f6' );
            },
            stop: function ( event, ui ) {
                ui.item.removeAttr( 'style' );
            },
            update: function () {
                var attachment_ids = [];

                $( '#<?php echo mfd_get( $element, 'name' ); ?>_container' ).find( 'ul li.image' ).each( function () {
                    var attachment_id = $( this ).attr( 'data-attachment_id' );
                    if (attachment_id) {
                        attachment_ids.push( attachment_id );
                    }
                } );

                $image_ids.val( attachment_ids.join( ',' ) );
            }
        } );
    } );
</script>
<style>
    #<?php echo mfd_get( $element, 'name' ); ?>_image_picker_container .add_images {
        cursor: pointer !important;
    }

    #<?php echo mfd_get( $element, 'name' ); ?>_image_picker_container .product_images .image {
        float: left;
        list-style-type: none;
        cursor: pointer;
    }

    #<?php echo mfd_get( $element, 'name' ); ?>_image_picker_container .product_images img {
        padding: 2px;
        width: 100px;
        height: 100px;
    }

    #<?php echo mfd_get( $element, 'name' ); ?>_image_picker_container .msm_thumbnail {
        width: <?php echo mfd_get( $element, 'size', 120 ); ?>px;
        height: <?php echo mfd_get( $element, 'size', 120 ); ?>px;
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
        margin: 2px;
    }

    #<?php echo mfd_get( $element, 'name' ); ?>_image_picker_container .image_picker {
        margin: 0 auto;
        border-radius: <?php echo mfd_get( $element, 'radius', 0 ); ?>px;
    }

    #<?php echo mfd_get( $element, 'name' ); ?>_image_picker_container .msm_thumbnail .delete-button {
        float: right;
        font-size: 24px;
        color: darkgrey;
    }
</style>

<div class="<?php echo $classes; ?>">
	<?php mfd_output_title( $element ); ?>
    <div id="<?php echo mfd_get( $element, 'name' ); ?>_image_picker_container">
        <div class="msm_thumbnail image_picker add_images" style="background-image : url('<?php echo $image; ?>')">
            <i class="remove circle icon delete-button"
               style="display: <?php echo $image ? 'block' : 'none' ?>;"></i>
            <input type="hidden"
                   name="<?php echo mfd_get( $element, 'name' ); ?>"
                   value="<?php echo $value; ?>"/>
        </div>
    </div>
</div>
