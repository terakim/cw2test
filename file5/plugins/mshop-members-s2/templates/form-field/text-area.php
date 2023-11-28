<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$classes = mfd_make_class( array (
	'yes' == mfd_get( $element, 'required' ) ? 'required' : '',
	mfd_get( $element, 'class' ),
	mfd_get( $element, 'width', 'sixteen wide' ),
	'field',
	implode( ' ', mfd_get_conditional_class( $element ) )
) );

?>

<div class="<?php echo $classes; ?>">
	<?php mfd_output_title( $element ); ?>
	<textarea
		type="<?php echo mfd_get( $element, 'type', 'text' ); ?>"
		name="<?php echo mfd_get( $element, 'name' ); ?>"
		rows="<?php echo mfd_get( $element, 'rows' ); ?>"
		<?php echo 'yes' == mfd_get( $element, 'readonly' ) ? 'readonly' : ''; ?>
		placeholder="<?php echo mfd_get( $element, 'placeHolder' ); ?>"><?php echo $value; ?></textarea>
</div>