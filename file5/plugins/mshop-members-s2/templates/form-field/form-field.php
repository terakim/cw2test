<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$conditional_classes = mfd_get_conditional_class( $element );

$classes = mfd_make_class( array (
	'yes' == mfd_get( $element, 'inline' ) ? 'inline' : '',
	mfd_get( $element, 'class' ),
	'fields'
) );

$tmp_classes = explode( ' ', $classes );

if ( in_array( 'combination', $tmp_classes ) ) {
	$conditional_classes[] = 'combination';
}

?>

<div class="field <?php echo 'yes' == mfd_get( $element, 'required' ) ? 'required' : ''; ?> <?php echo implode( ' ', $conditional_classes ); ?>" style="<?php echo mfd_get_conditional_style( $conditional_classes ); ?>">
	<?php mfd_output_title( $element ); ?>
    <div class="<?php echo $classes; ?>">
		<?php
        if( ! empty( $element[ 'items' ] ) ) {
            foreach ( $element[ 'items' ] as $item ) {
                mfd_output( $item, $msm_post, $msm_form, true );
            }
        }
		?>
    </div>
</div>