<?php
$theme_color = get_option( 'msm_theme_color', 'red' );
$msm_finish_url = get_option( 'msm_finish_url', home_url() );
$msm_finish_text = get_option( 'msm_finish_text', '메인으로 이동' );

?>

<div class="msmea msmea-email-certified">
     <div class="msmea-img"><img src="<?php echo MSM()->plugin_url() . '/assets/images/' . $theme_color . '_certified.png';?>" width="140" height="140"></div>
    <div class="msmea-txt">
	    <h1 class="title"><?php _e( "이메일 인증이 완료되었습니다.", 'mshop-members-s2' ); ?></h1>
        <p><a href="<?php echo $msm_finish_url; ?>" class="msmea-btn msmea-<?php echo $theme_color; ?>btn"><?php echo $msm_finish_text; ?></a></p>
    </div>
</div>
