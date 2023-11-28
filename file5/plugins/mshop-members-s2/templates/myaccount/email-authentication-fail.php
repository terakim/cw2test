<?php
$theme_color = get_option( 'msm_theme_color', 'red' );
?>

<div class="msmea email-authentication-error">
    <div class="msmea-img"><img src="<?php echo MSM()->plugin_url() . '/assets/images/' . $theme_color . '_fail.png'; ?>" width="140" height="140"></div>
    <div class="msmea-txt">
	    <h1 class="title"><?php _e( "잘못된 요청입니다.", 'mshop-members-s2' ); ?></h1>
	</div>
</div>