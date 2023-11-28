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

if( empty( $values ) ) {
    $values = array();
}

$values = array_filter( $values );
if ( empty( $values ) ) {
	$values = array( mfd_get( $element, 'default' ) );
}

?>

<div class="<?php echo $classes; ?>">
	<?php if ( ! empty( mfd_get( $element, 'name' ) ) ) : ?>
        <script>
            jQuery( document ).ready( function ( $ ) {
                var id = '<?php _e( mfd_get( $element, 'name' ) ); ?>';

                $( '#' + id ).dropdown( {
                    onChange: function ( value, text, $selectedItem ) {
                        $( 'input[name=' + id + ']' ).val( value );
                    },
                    fullTextSearch : true
                } );
            } );
        </script>
	<?php endif; ?>

    <?php mfd_output_title( $element ); ?>

    <div id="<?php _e( mfd_get( $element, 'name' ) ); ?>" class="ui fluid <?php _e( 'yes' == mfd_get( $element, 'multiple' ) ? 'multiple' : '' ); ?> search selection dropdown">
        <input type="hidden" name="<?php _e( mfd_get( $element, 'name' ) ); ?>" value="<?php echo implode( ',', $values ); ?>">
        <i class="dropdown icon"></i>
        <div class="default text"><?php _e( mfd_get( $element, 'placeHolder' ) ); ?></div>
        <div class="menu">
			<?php
			foreach ( $options as $key => $value ) {
				echo '<div class="item" data-value="' . $key . '">' . $value . '</div>';
			}
			?>
        </div>
    </div>
</div>