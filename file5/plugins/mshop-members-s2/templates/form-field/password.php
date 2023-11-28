<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$name = mfd_get( $element, 'name' );

$classes = mfd_make_class( array(
	'yes' == mfd_get( $element, 'inline' ) ? 'inline' : '',
	'yes' == mfd_get( $element, 'required' ) ? 'required' : '',
	mfd_get( $element, 'class' ),
	mfd_get( $element, 'width', 'sixteen wide' ),
	'field',
	implode( ' ', mfd_get_conditional_class( $element ) )
) );

$use_strength_meter  = ( 'yes' == mfd_get( $element, 'use_strength_meter' ) );
$use_password_viewer = ( 'yes' == mfd_get( $element, 'use_password_viewer' ) );


if ( $use_strength_meter ) {
	wp_enqueue_script( 'msm-password-strength-meter', MSM()->plugin_url() . '/assets/js/msm-password-strength-meter.js', array( 'password-strength-meter' ), MSM_VERSION );
	wp_localize_script( 'msm-password-strength-meter', 'msm_strength_meter', array(
		'warning_level' => array(
			'step_0' => __( '매우 약함', 'mshop-members-s2' ),
			'step_1' => __( '약함', 'mshop-members-s2' ),
			'step_2' => __( '약함', 'mshop-members-s2' ),
			'step_3' => __( '보통', 'mshop-members-s2' ),
			'step_4' => ''
		),
		'guide_message' => __( ' - 더 안전한 비밀번호를 입력해주세요.<br><span style="font-size: 0.85em; color: grey;">힌트: 비밀번호는 최소한 12자 이상을 사용해야 합니다. 강한 비밀번호를 만드려면 영문 대문자, 소문자, 숫자와 ! " ? $ % & &와 같은 특수문자를 사용하세요.</span>', 'mshop-members-s2' )
	) );
}

?>

<div class="<?php echo $classes; ?>">
	<?php mfd_output_title( $element ); ?>
    <input
            type="password"
            id="<?php echo $name; ?>"
            name="<?php echo $name; ?>"
            value="<?php echo $value; ?>"
            autocomplete="new-password"
            placeholder="<?php echo mfd_get( $element, 'placeHolder' ); ?>"/>

	<?php if ( $use_password_viewer ) : ?>
        <span style="position: absolute; top: 12px; right: 10px; color: #1a1a1a; font-size: 14px; cursor: pointer;" class="password-visible eye-slash" ></span>
        <script>
            jQuery( document ).ready( function ( $ ) {
                $( 'input[name="<?php echo mfd_get( $element, 'name' ); ?>"]' ).closest( 'div.field' ).css( 'position', 'relative' );
                $( 'label ~ input[name="<?php echo mfd_get( $element, 'name' ); ?>"] ~ span' ).css( "top", $( 'input[name="<?php echo mfd_get( $element, 'name' ); ?>"]' ).height() + 1 );
                $( 'input[name="<?php echo mfd_get( $element, 'name' ); ?>"] ~ span' ).on( 'click', function () {
                    $( this ).prev( 'input' ).toggleClass( 'active' );

                    if ($( this ).prev( 'input' ).hasClass( 'active' )) {
                        $( this ).attr( 'class', "password-visible eye" ).prev( 'input' ).attr( 'type', "text" );
                    } else {
                        $( this ).attr( 'class', "password-visible eye-slash" ).prev( 'input' ).attr( 'type', 'password' );
                    }
                } );
            } );
        </script>
	<?php endif; ?>

	<?php if ( $use_strength_meter ) : ?>
        <script>
            jQuery( document ).ready( function ( $ ) {
                (new $.fn.msm_password_strength_meter()).init( $( '#<?php echo $name; ?>' ).closest( 'div' ) );
            } );
        </script>
        <div class="ui tiny progress red strength-meter" data-value="0" data-total="4">
            <div class="bar">
            </div>
            <div class="label"></div>
        </div>
	<?php endif; ?>
</div>