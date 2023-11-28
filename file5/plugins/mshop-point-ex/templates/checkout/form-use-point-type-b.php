<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$ratio = explode( '.', $point_exchange_ratio );
if ( count( $ratio ) > 1 ) {
	$ratio = strlen( $ratio[1] ) > wc_get_price_decimals() ? strlen( $ratio[1] ) : wc_get_price_decimals();
} else {
	$ratio = wc_get_price_decimals();
}

$args = array(
	'decimals' => $ratio
);

$useable_point = $user_point > $max_useable_point ? $max_useable_point : $user_point;

?>
<script>
    jQuery( document ).ready( function ( $ ) {
        $( 'input.mshop_point.input-text' ).on( 'keydown', function ( e ) {
            if (e.keyCode == 13 && e.srcElement.type != 'textarea') {
                return false;
            }
        } );
        $( 'input.msps-use-all-point' ).on( 'click', function ( e ) {
            $( 'input[name=mshop_point' ).val( $( this ).data( 'point' ) );
        } );

        $( 'input.msps-apply-point' ).on( 'click', function ( e ) {
            $( document.body ).trigger( 'update_checkout' );
        } );
    } );
</script>
<style>
    .point-title p {
        padding-bottom: 0px;
    }

    .msps-button-wrapper {
        display: flex;
        width: 100%;
        font-size: 0.9em;
    }

    .msps-button-wrapper .button {
        margin: 0 5px !important;
        flex: 1;
    }
    .msps-button-wrapper .button:first-child {
        margin-left: 0 !important;
    }
    .msps-button-wrapper .button:last-child {
        margin-right: 0 !important;
    }
</style>

<tr>
    <td class="point-title" style="text-align:left"><?php _e( '포인트', 'mshop-point-ex' ); ?></td>
    <td>
        <input type="text" name="mshop_point" class="mshop_point input-text" value="" placeholder="<?php echo sprintf( __( '%s 포인트를 사용할 수 있습니다.', 'mshop-point-ex' ), number_format( floatval( $useable_point ), wc_get_price_decimals() ) ); ?> ">
        <div class="msps-button-wrapper">
            <input type="button" class="button button-primary msps-use-all-point" data-point="<?php echo $user_point > $max_useable_point ? $max_useable_point : $user_point; ?>" value="모두 사용">
            <input type="button" class="button button-primary msps-apply-point" value="할인받기">
        </div>
    </td>
</tr>
