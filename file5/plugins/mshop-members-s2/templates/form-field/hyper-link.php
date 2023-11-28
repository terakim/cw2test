<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$classes = mfd_make_class( array (
	mfd_get( $element, 'class' ),
	mfd_get( $element, 'width', 'sixteen wide' ),
	'mshop-hyperlink-widget field',
	implode( ' ', mfd_get_conditional_class( $element ) )
) );

?>

<div class="<?php echo $classes; ?>" style="text-align: <?php _e( $element['align'] ); ?>">
	<a href="<?php echo mfd_get( $element, 'url' ); ?>" class="<?php echo mfd_get( $element, 'class' ); ?>"><?php echo mfd_get( $element, 'title' ); ?></a>
</div>