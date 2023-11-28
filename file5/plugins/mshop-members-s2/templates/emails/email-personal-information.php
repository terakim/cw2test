<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

?>

<style>
    body {
        width: 100%;
        background-color: #fff;
        margin: 0;
        font-family: Malgun Gothic, MalgunGothic, malgungothic, 맑은고딕, AppleSDGothic, apple sd gothic neo, noto sans korean, noto sans korean regular, noto sans cjk kr, noto sans cjk, nanum gothic, malgun gothic, dotum, arial, helvetica, MS Gothic, sans-serif!important;
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
        line-height: 170px;
        font-size: 2em;
        color: #fff;
    }
    .email-des {
        width: 630px;
    }
    .email-item {
        font-size: 16px;
        line-height: 1.7;
        padding: 15px;
        word-break: break-word;
    }
    .email-item:last-child {
        font-size: 14px;
    }
    .email-btn {
        background-color: <?php echo ! empty( $email->settings['color'] ) ? $email->settings['color'] : '#000000'; ?>;
        margin: 15px auto;
        text-align: center;
        width: 290px;
        border-radius: 3px;
    }
    .email-btn a {
        font-size: 16px;
        display: inline-block;
        color: #fff;
        text-decoration: none;
        padding: 19px 20px;
    }

    @media screen and (max-width: 640px) {
        .email-container {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding-top: 40px;
        }
        .email .email-header {
            width: 100%;
            max-width: 100%;
        }
        .email-des {
            width: 100%;
        }
    }
</style>
<?php
$blogname = ! empty( $email->settings['blogname'] ) ? $email->settings['blogname'] : get_bloginfo();
?>

<div class="email">
    <div class="email-container">
        <div class="email-header">
            <h2><?php echo sprintf( __( '%s 개인정보 이용내역 안내', 'mshop-members-s2' ), $blogname ); ?></h2>
        </div>
        <div class="email-des">
            <div class="email-item">
                <?php echo sprintf( __( '안녕하세요. %s입니다.', 'mshop-members-s2' ), $blogname ); ?> <br><br>
                <?php echo sprintf( __( '본 메일은 개인정보 보호법 제 39조의8항 (개인정보 이용내역의 통지)에 의거, %s 회원님의 개인정보 이용내역을 안내드립니다.', 'mshop-members-s2' ), $blogname ); ?>
            </div>
            <div class="email-btn">
                <a href="<?php echo ! empty( $email->settings['link'] ) ? $email->settings['link'] : site_url() . '/personal_policy/'; ?>"><?php _e( '개인정보처리방침 자세히 보기', 'mshop-members-s2' ); ?></a>
            </div>
            <div class="email-item">
                <?php _e( '※ 본 메일은 법령에 따른 통지의무 사항으로 수신동의 여부와 관계없이 모든 회원님들께 연 1회 법적 고지로 발송되는 메일입니다.', 'mshop-members-s2' ); ?>
            </div>
        </div>
    </div>
</div>