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
	'field date-field',
	mfd_get( $element, 'name' ),
	implode( ' ', $conditional_classes )
) );

if ( ! empty( $value ) ) {
	$dates = explode( '-', $value );
	$year  = isset( $dates[0] ) ? intval( $dates[0] ) : intval( date( 'Y' ) ) - 20;
	$month = isset( $dates[1] ) ? intval( $dates[1] ) : 0;
	$day   = isset( $dates[2] ) ? intval( $dates[2] ) : 0;
} else {
	$year  = date( 'Y' ) - 20;
	$month = 0;
	$day   = 0;
}

?>
<script language="JavaScript">
    jQuery( document ).ready( function ( $ ) {
        $wrapper_<?php echo mfd_get( $element, 'name' ); ?> = $( 'div.date-field.<?php echo mfd_get( $element, 'name' ); ?>' );

        $( 'select', $wrapper_<?php echo mfd_get( $element, 'name' ); ?>).on( 'change', function () {
            var year  = $( 'select[name=year]', $wrapper_<?php echo mfd_get( $element, 'name' ); ?>).val();
            var month = $( 'select[name=month]', $wrapper_<?php echo mfd_get( $element, 'name' ); ?>).val();
            var day   = $( 'select[name=day]', $wrapper_<?php echo mfd_get( $element, 'name' ); ?>).val();

            if (year && month && day) {
                $( 'input[name=<?php echo mfd_get( $element, 'name' ); ?>]', $wrapper_<?php echo mfd_get( $element, 'name' ); ?>).val( year + '-' + month + '-' + day );
            } else {
                $( 'input[name=<?php echo mfd_get( $element, 'name' ); ?>]', $wrapper_<?php echo mfd_get( $element, 'name' ); ?>).val();
            }
        } );
    } );
</script>
<div class="<?php echo $classes; ?>" style="<?php echo mfd_get_conditional_style( $conditional_classes ); ?>">
	<?php mfd_output_title( $element ); ?>
    <div class="three fields">
        <div class="field">
            <select name="year" class="ui dropdown">
                <option value="" disabled selected hidden><?php _e( '년', 'mshop-members-s2' ); ?></option>
				<?php for ( $i = date( 'Y' ) - 100; $i <= date( 'Y' ) - 14; $i ++ ) : ?>
                    <option
                            value="<?php echo $i; ?>" <?php echo( $i == $year ? 'selected' : '' ); ?>><?php echo $i; ?></option>
				<?php endfor; ?>
            </select>
        </div>
        <div class="field">
            <select name="month" class="ui dropdown">
                <option value="" disabled selected hidden><?php _e( '월', 'mshop-members-s2' ); ?></option>
				<?php for ( $i = 1; $i <= 12; $i ++ ) : ?>
					<?php printf( '<option value="%02d" %s>%02d</option>', $i, ( $i == $month ? 'selected' : '' ), $i ); ?>
				<?php endfor; ?>
            </select>
        </div>
        <div class="field">
            <select name="day" class="ui dropdown">
                <option value="" disabled selected hidden><?php _e( '일', 'mshop-members-s2' ); ?></option>
				<?php for ( $i = 1; $i <= 31; $i ++ ) : ?>
					<?php printf( '<option value="%02d" %s>%02d</option>', $i, ( $i == $day ? 'selected' : '' ), $i ); ?>
				<?php endfor; ?>
            </select>
        </div>

        <input type="hidden" name="<?php echo mfd_get( $element, 'name' ); ?>" value="<?php echo $value; ?>"/>
    </div>
</div>