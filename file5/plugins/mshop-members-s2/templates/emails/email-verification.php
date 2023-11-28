<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<p style="font-size: 25px;font-weight: normal;text-align: center;"><?php echo sprintf( __( "[%s] 고객님이 요청하신 인증번호를 발송해 드립니다.", "mshop-members-s2" ), $blog_name ); ?></p>

<div style="width: 80%;margin: 0 auto; height: 200px;border: 1px solid lightgray;text-align: center;">
    <p style="font-size: 20px; padding: 20px;"><?php _e( "아래 인증번호 6자리를 인증번호 입력창에 입력해주세요.", "mshop-members-s2" ); ?></p>
    <h3 style="font-size: 25px; color: #1d2fb9"><?php echo $certification_number; ?></h3>
</div>