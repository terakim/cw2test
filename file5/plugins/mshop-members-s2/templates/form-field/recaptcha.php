<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$classes = mfd_make_class( array (
	mfd_get( $element, 'class' ),
	mfd_get( $element, 'width', 'sixteen wide' ),
	'field'
) );

$hl = '&hl=ko';

if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
	$lang_code = ICL_LANGUAGE_CODE;

	$hl = '&hl=' . substr( $lang_code, 0, 2 );
}

wp_deregister_script( 'google-recaptcha' );
wp_dequeue_script( 'google-recaptcha' );
wp_register_script( 'google-recaptcha', 'https://www.google.com/recaptcha/api.js?onload=msmRecaptchaCallback&render=explicit&ver=2.0' . $hl );
wp_enqueue_script( 'google-recaptcha' );

$style = 'display: flex; flex-flow: row nowrap; ';
if ( 'center' == mfd_get( $element, 'align' ) ) {
	$style .= 'justify-content: center;';
} else if ( 'right' == mfd_get( $element, 'align' ) ) {
	$style .= 'justify-content: flex-end;';
}

?>
    <div class="<?php echo $classes; ?>" style="<?php echo $style; ?>">
        <div id="recaptcha"></div>
        <input type="hidden" name="_grecaptcha">
    </div>
    <script type="text/javascript">
        var msmRecaptchaCallback = function () {
            grecaptcha.render( 'recaptcha', {
                sitekey : '<?php echo mfd_get( $element, 'site_key' ); ?>',
                callback: function () {
                    jQuery( 'input[name=_grecaptcha]' )
                        .val( grecaptcha.getResponse() )
                        .trigger( 'change' );
                }
            } );
        };
    </script>

<?php
