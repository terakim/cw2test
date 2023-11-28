<style>
    body {
        width: 100%;
        background-color: #fff;
        margin: 0;
        font-family: Malgun Gothic, MalgunGothic, malgungothic, 맑은고딕, AppleSDGothic, apple sd gothic neo, noto sans korean, noto sans korean regular, noto sans cjk kr, noto sans cjk, nanum gothic, malgun gothic, dotum, arial, helvetica, MS Gothic, sans-serif !important;
        overflow-x: hidden;
    }

    .email {
        width: 100%;
    }

    .email-container {
        width: 630px;
        max-width: 632px;
        margin: 40px auto 0;
        border: 1px solid #888;
    }

    .email-header {
        width: 100%;
        max-width: 630px;
        background-color: <?php echo ! empty( $email->settings['color'] ) ? $email->settings['color'] : '#000000'; ?>;
        color: #fff;
        height: 170px;
    }

    .email-header h2 {
        text-align: center;
        margin-top: 0;
        font-size: 2em;
        padding-top: 1.5em;
        font-weight: 400;
        color: #fff;
    }

    .email-header h2 span {
        display: block;
        font-weight: 900;
    }

    .email-desc {
        padding: 20px;
    }

    .email-item {
        font-size: 15px;
        line-height: 1.7;
        padding: 15px;
        word-break: keep-all;
        text-align: left;
    }

    .email-item span {
        font-weight: 900;
        display: block;
    }

    .email-item .email-item-title {
        border-bottom: 2px solid #222;
        padding-bottom: 7px;
        font-weight: 900;
    }

    .email-item .email-item-info {
        padding: 10px 0;
    }

    .email-btn {
        background-color: <?php echo ! empty( $email->settings['color'] ) ? $email->settings['color'] : '#000000'; ?>;
        margin: 15px auto;
        text-align: center;
        width: 290px;
        border-radius: 3px;
    }

    .email-btn a {
        text-decoration: none;
        color: #fff;
        display: inline-block;
        padding: 19px 20px;
    }
</style>

<?php
$current_date = date( 'Y년 m월 d일', strtotime( current_time( 'mysql' ) ) );
$blogname = ! empty( $email->settings['blogname'] ) ? $email->settings['blogname'] : get_bloginfo();

$user_id = $email->user->ID;
$user = get_userdata( $user_id );

$mssms_agreement_date = msm_get_agreement_date( $user_id, 'mssms' );
$email_agreement_date = msm_get_agreement_date( $user_id, 'email' );
?>

<body>
<div class="email">
    <div class="email-container">
        <div class="email-header">
            <h2><?php echo sprintf( __( '%s <span>정기적 수신동의 확인 안내</span>', 'mshop-members-s2' ), $blogname ); ?></h2>
        </div>
        <div class="email-desc">
            <div class="email-item">
                <?php echo sprintf( __( '%s 고객님 안녕하세요.', 'mshop-members-s2' ), $user->display_name ); ?><br>
                <?php echo sprintf( __( '항상 저희 %s 사이트를 이용해 주셔서 진심으로 감사드립니다.', 'mshop-members-s2' ), $blogname ); ?><br><br>
                <?php _e( '<b>정보통신망 이용촉진 및 정보보호 등에 관한 법률 제50조 제8항 및 동법 시행령 제62조의 3</b>에 따라 고객님의 마케팅 정보 수신 동의 여부를 다음과 같이 안내해 드립니다.', 'mshop-members-s2' ); ?>
            </div>
            <?php if ( 'yes' == get_option( 'msm_user_agreement_use_email', 'no' ) ) : ?>
                <div class="email-item">
                    <div class="email-item-title"><?php _e( '이메일 수신 동의', 'mshop-members-s2' ); ?></div>
                    <div class="email-item-info"><?php echo sprintf( __( '%s 동의', 'mshop-members-s2' ), $email_agreement_date ); ?></div>
                </div>
            <?php endif; ?>
            <?php if ( 'yes' == get_option( 'msm_user_agreement_use_mssms', 'no' ) ) : ?>
                <div class="email-item">
                    <div class="email-item-title"><?php _e( '문자/알림톡 수신 동의', 'mshop-members-s2' ); ?></div>
                    <div class="email-item-info"><?php echo sprintf( __( '%s 동의', 'mshop-members-s2' ), $mssms_agreement_date ); ?></div>
                </div>
            <?php endif; ?>
            <div class="email-item">
                <?php _e( '이전 가입자의 경우, 수신 동의 일자가 확인되지 않아, 수신 동의 일자와 실제 설정일이 다를 수 있습니다. ', 'mshop-members-s2' ); ?><br><br>
                <?php _e( '수신 동의 상태를 유지하고자 하시는 경우 별도의 조치가 필요하지 않으며, 수신거부를 원하시는 경우 사이트 내 계정 페이지 또는 고객센터를 통해 언제든지 변경하실 수 있습니다.', 'mshop-members-s2' ); ?>
            </div>
            <div class="email-btn">
                <a href="<?php echo get_site_url(); ?>" target="_blanka"><?php echo sprintf( __( '%s 바로가기', 'mshop-members-s2' ), $blogname ); ?></a>
            </div>
        </div>
    </div>
</div>
</body>