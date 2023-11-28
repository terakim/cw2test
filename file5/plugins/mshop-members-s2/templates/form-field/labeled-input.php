<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$conditional_classes = mfd_get_conditional_class( $element );

$classes = mfd_make_class( array(
	'yes' == mfd_get( $element, 'required' ) ? 'required' : '',
	'yes' == mfd_get( $element, 'inline' ) ? 'inline' : '',
	mfd_get( $element, 'class' ),
	mfd_get( $element, 'width', 'sixteen wide' ),
	'field',
	implode( ' ', $conditional_classes )
) );

?>

<div class="<?php echo $classes; ?>">
	<?php mfd_output_title( $element ); ?>
    <div class="ui <?php echo mfd_get( $element, 'position' ); ?> labeled fluid input">
		<?php if ( 'left' == mfd_get( $element, 'position' ) ) : ?>
            <div class="ui basic label">
				<?php echo mfd_get( $element, 'label' ); ?>
            </div>
		<?php endif; ?>
        <input
                type="text" placeholder="<?php echo mfd_get( $element, 'placeHolder' ); ?>"
                name="<?php echo mfd_get( $element, 'name' ); ?>"
                value="<?php echo ! empty( $value ) ? $value : mfd_get( $element, 'default' ); ?>"/>
		<?php if ( 'right' == mfd_get( $element, 'position' ) ) : ?>
            <div class="ui basic label">
				<?php echo mfd_get( $element, 'label' ); ?>
            </div>
		<?php endif; ?>
    </div>
</div>