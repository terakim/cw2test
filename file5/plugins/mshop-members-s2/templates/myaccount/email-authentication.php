<?php
$theme_color = get_option( 'msm_theme_color', 'red' );

?>
<div class="msmea msmea-email-autheication">
    <div class="msmea-img"><img src="<?php echo MSM()->plugin_url() . '/assets/images/' . $theme_color . '_email.png'; ?>" width="140" height="140"></div>
    <div class="msmea-txt">
        <h1 class="title"><?php printf( __( '환영합니다! <span class="%s">이메일 주소</span>를 인증해 주세요.', 'mshop-members-s2' ), $theme_color ); ?></h1>
        <p><?php _e( '이메일 인증을 위한 메일이 발송되었습니다.', 'mshop-members-s2' ); ?><br><?php _e( '회원가입 완료를 위한 이메일 인증을 진행 해 주세요.', 'mshop-members-s2' ); ?></p>
		<?php $user_email = ! empty( wp_get_current_user()->user_email ) ? wp_get_current_user()->user_email : ''; ?>
        <p><?php printf( __( '가입 이메일 주소 : %s', 'mshop-members-s2' ), $user_email ); ?></p>
        <p><?php _e( "이메일 주소를 잘못 입력하신 경우 <br>'고객문의'로 이메일 주소 수정을 요청해 주시기 바랍니다.", 'mshop-members-s2' ); ?></p>
        <p><?php _e( "이메일을 받지 못하셨나요?", 'mshop-members-s2' ); ?><br>
            <a href="<?php echo esc_url( add_query_arg( array( 're-send' => 'yes' ), get_permalink( get_page_by_path( 'email-authentication' ) ) ) ); ?>" class="msmea-btn msmea-<?php echo $theme_color; ?>btn"><?php _e( "이메일 다시 보내기", 'mshop-members-s2' ); ?></a></p>
    </div>
</div>
