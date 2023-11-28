<?php

$class  = esc_attr( implode( ' ', $args['class'] ) );
$id     = esc_attr( $args["id"] );
$phone  = explode( '-', $value );

?>
<script>
    jQuery(document).ready( function($){
        var $wrapper = $('#<?php echo $id; ?>');

        $('.phone-part', $wrapper).on('change', function(){
            var values = [];
            var value_length = [3,4,4];
            var $parts = $('.phone-part', $wrapper );

            $.each( $parts, function( idx, part ) {
                var value = $(part).val();

                if( value.length === value_length[idx] ) {
                    values.push( value );
                }
            });

            if( values.length === 3 ) {
                $('.phone-combination', $wrapper).val(values.join('-'));
            }else{
                $('.phone-combination', $wrapper).val('');
            }
        });
    });

</script>
<p class="form-row form-row-last <?php echo $class; ?>" id="<?php echo $id; ?>">
    <label for="<?php echo $id; ?>-p1" class=""><?php _e('전화번호 ','mshop-address-ex'); ?><abbr class="required" title="<?php _e('필수', 'mshop-address-ex'); ?>">*</abbr></label>
    <select name="<?php echo $id; ?>-p1" class="<?php echo $id; ?>-p1 phone-part phone-part1" style="width: 30%; float: left; margin-right: 5px;">
        <option value="010" selected>010</option>
        <option value="011">011</option>
    </select>
    <input type="text" name="<?php echo $id; ?>-p2" class="input-text phone-part phone-part2" value="<?php echo isset( $phone[1] ) ? $phone[1] : ''; ?>" maxlength=4 style="width: 30%; float: left; margin-right: 5px;">
    <input type="text" name="<?php echo $id; ?>-p3" class="input-text phone-part phone-part3" value="<?php echo isset( $phone[2] ) ? $phone[2] : ''; ?>" maxlength=4 style="width: 30%; float: left; margin-right: 5px;">
    <input type="hidden" id="<?php echo $id; ?>" name="<?php echo $id; ?>" data-fields="p1,p2,p3" data-delimeter='-' class="combination-field phone-combination" value="<?php echo $value; ?>">
</p>
