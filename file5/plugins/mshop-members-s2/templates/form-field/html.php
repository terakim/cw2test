<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$style = mfd_get_style( mfd_get( $element, 'style', '' ) );

$classes = mfd_make_class( array(
	mfd_get( $element, 'class' ),
	mfd_get( $element, 'width', 'sixteen wide' ),
	'field',
	implode( ' ', mfd_get_conditional_class( $element ) )
) );

?>

<div class="<?php echo $classes; ?>">
	<?php mfd_output_title( $element ); ?>
    <div style="<?php echo $style; ?>"><?php echo mfd_get( $element, 'html' ); ?></div>
</div>