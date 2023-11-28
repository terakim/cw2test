<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$wrapper_classes = mfd_make_class( array(
	mfd_get( $element, 'width', 'sixteen wide' ),
	'field'
) );

$classes = mfd_make_class( array(
	mfd_get( $element, 'type', 'button' ),
	mfd_get( $element, 'class' ),
	mfd_get( $element, 'width', 'sixteen wide' ),
	'ui button',
	implode( ' ', mfd_get_conditional_class( $element ) )
) );

$icon = mfd_get( $element, 'icon', '', '<i class="icon %s"></i>' );

$id = mfd_get( $element, 'name' );

?>
<?php if ( 'submit' ==  mfd_get( $element, 'type' ) && ! empty( mfd_get( $element, 'confirmMessage' ) ) ) : ?>
    <script>
        jQuery( document ).ready( function ($) {
            $( '#<?php echo $id; ?>' ).on( 'click', function ( e ) {
                if (!confirm( '<?php echo mfd_get( $element, 'confirmMessage' ); ?>' )) {
                    e.stopPropagation();
                    e.preventDefault();
                    return false;
                }
            } )
        } );
    </script>
<?php endif; ?>
<?php if ( empty ( $icon ) ) { ?>
    <div class="<?php echo $wrapper_classes; ?>" style="text-align: <?php _e( $element['align'] ); ?>">
        <input <?php echo empty( $id ) ? '' : 'id="' . $id . '"'; ?> type="button" class="<?php echo $classes; ?>" name="<?php echo mfd_get( $element, 'name' ); ?>"
                                                                     value="<?php echo mfd_get( $element, 'title' ); ?>">
    </div>
<?php } else { ?>
    <div class="<?php echo $wrapper_classes; ?>" style="text-align: <?php echo mfd_get( $element, 'align' ); ?>;">
        <div <?php echo empty( $id ) ? '' : 'id="' . $id . '"'; ?> class="<?php echo $classes; ?>">
			<?php echo $icon; ?>
			<?php echo mfd_get( $element, 'title' ); ?>
        </div>
    </div>
<?php } ?>