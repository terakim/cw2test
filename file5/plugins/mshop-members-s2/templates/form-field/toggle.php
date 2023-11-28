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

$check_type    = mfd_get( $element, 'checkType' );
$input_type    = 'radio' == $check_type ? 'radio' : 'checkbox';
$checked_value = mfd_get( $element, 'value', 'on' );

if ( empty( $value ) ) {
    $value = msm_get( $element, 'default' );
}

?>
<?php if ( ! empty( $value) && msm_get( $element, 'value' ) == $value ) : ?>
    <script>
        jQuery( document ).ready( function ( $ ) {
            $( '.ui.checkbox.<?php echo mfd_get( $element, 'name' ); ?>[data-value="<?php echo $value;?>"]' ).checkbox( 'check' );
        } );
    </script>
<?php endif; ?>

<?php if ( 'custom' != mfd_get( $element, 'checkType' ) ) : ?>
    <div class="<?php echo $classes; ?>">
        <?php if ( 'checkbox' == $input_type && ! empty( $element['title'] ) ) : ?>
            <?php mfd_output_title( $element ); ?>
        <?php endif; ?>
        <div class="ui <?php echo $check_type; ?> checkbox <?php echo mfd_get( $element, 'class' ); ?> <?php echo mfd_get( $element, 'name' ); ?>" data-value="<?php echo mfd_get( $element, 'value' ); ?>">
            <input type="<?php echo $input_type; ?>" name="<?php echo mfd_get( $element, 'name' ); ?>" value="<?php echo $checked_value; ?>"/>
            <label class="except"><?php echo mfd_get( $element, 'label' ); ?></label>
        </div>
    </div>
<?php else: ?>
    <div class="<?php echo $classes; ?> msm-custom-checkbox field">
        <?php mfd_output_title( $element ); ?>
        <div class="form-row ui checkbox custom-checkbox <?php echo mfd_get( $element, 'name' ); ?>" data-value="<?php echo mfd_get( $element, 'value' ); ?>">
            <input type="checkbox" class="input-checkbox" name="<?php echo mfd_get( $element, 'name' ); ?>">
            <label for="custom-checkbox" class="checkbox"><?php echo mfd_get( $element, 'label' ); ?></label>
        </div>
    </div>
<?php endif; ?>