<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$wrapper_classes = mfd_make_class( array(
	mfd_get( $element, 'width', 'sixteen wide' ),
	'field'
) );

$classes = mfd_make_class( array(
	mfd_get( $element, 'type', 'button' ),
	mfd_get( $element, 'class' ),
	mfd_get( $element, 'width', 'sixteen wide' ),
	'ui',
	implode( ' ', mfd_get_conditional_class( $element ) )
) );

$provider = MSM_Social_Login::get_provider( strtolower( mfd_get( $element, 'channel' ) ) );

if ( is_null( $provider ) ) {
	return;
}

$url = $provider->get_login_url();

if ( 'Kakao' == mfd_get( $element, 'channel' ) && 'yes' == get_option( 'msm_oauth_kakao_sync', 'no' ) ) {
	$image_url = sprintf( '%s/assets/images/social/%s/%sSync.png', MSM()->plugin_url(), mfd_get( $element, 'type' ), mfd_get( $element, 'channel' ) );
} else {
	$image_url = sprintf( '%s/assets/images/social/%s/%s.png', MSM()->plugin_url(), mfd_get( $element, 'type' ), mfd_get( $element, 'channel' ) );
}

$provider = MSM_Social_Login::get_provider( strtolower( $element['channel'] ) );
?>

<?php do_action( 'mfd_field_before_social_widget', $element, $provider ); ?>

    <div class="<?php echo $wrapper_classes; ?>" style="text-align: <?php echo mfd_get( $element, 'align' ); ?>;">
        <div class="<?php echo $classes; ?>">
            <a href="<?php echo $url; ?>"><img style="display: inline-block;height: <?php echo mfd_get( $element, 'height' ); ?>px;" src="<?php echo $image_url; ?>" alt="<?php echo sprintf( "%s로 로그인", $provider->get_title() ); ?>"/></a>
        </div>
    </div>

<?php do_action( 'mfd_field_after_social_widget', $element, $provider ); ?>