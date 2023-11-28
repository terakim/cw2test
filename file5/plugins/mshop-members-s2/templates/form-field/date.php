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

?>
<?php if ( 'yes'!= msm_get( $element, 'readonly', 'no' ) ) : ?>
    <script>
        jQuery( document ).ready( function ( $ ) {
            var id = '<?php echo mfd_get( $element, 'name' ); ?>';

            $( '#' + id ).calendar( {
                type: 'date',
                text: {
                    days: ['일', '월', '화', '수', '목', '금', '토'],
                    months: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
                    monthsShort: ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'],
                    today: '오늘',
                    now: '지금',
                    am: '오전',
                    pm: '오후'
                },
                formatter: {
                    date: function ( date, settings ) {
                        if (!date) return '';
                        var day   = date.getDate();
                        var month = date.getMonth() + 1;
                        var year  = date.getFullYear();
                        return year + '-' + (month < 10 ? '0' : '') + month + '-' + (day < 10 ? '0' : '') + day;
                    }
                },
                onChange: function ( date, text, mode ) {
                    $( document ).trigger( id + '_changed', [date, text, mode] );
                }
            } );
        } );
    </script>
<?php endif; ?>

<div class="<?php echo $classes; ?>">
	<?php mfd_output_title( $element ); ?>
    <div class="ui calendar" id="<?php echo mfd_get( $element, 'name' ); ?>">
        <div class="ui input left icon">
            <i class="calendar icon"></i>
            <input type="text" name="<?php echo mfd_get( $element, 'name' ); ?>" placeholder="<?php _e( '날짜 선택', 'mshop-members-s2' ); ?>" value="<?php echo $value; ?>" autocomplete="off" readOnly>
        </div>
    </div>
</div>
