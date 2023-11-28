<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$conditional_classes = mfd_get_conditional_class( $element );

$classes = mfd_make_class( array(
	mfd_get( $element, 'class' ),
	mfd_get( $element, 'width', 'sixteen wide' ),
	'field',
	implode( ' ', $conditional_classes )
) );

?>

<div class="<?php echo $classes; ?>" style="<?php echo mfd_get_conditional_style( $conditional_classes ); ?>">
	<h4 class="ui dividing header" style="margin: 10px 0px;"><?php echo mfd_get( $element, 'title' ); ?></h4>
</div>