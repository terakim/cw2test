<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$classes = mfd_make_class( array(
	mfd_get( $element, 'class' ),
	mfd_get( $element, 'width', 'sixteen wide' ),
	'field mshop-header-widget',
	implode( ' ', mfd_get_conditional_class( $element ) )
) );

?>

<div class="<?php echo $classes; ?>">
	<h3 class="ui header title"><?php echo mfd_get( $element, 'title' ); ?></h3>
	<i class="icon remove outline circle msl_close_btn"></i>
</div>