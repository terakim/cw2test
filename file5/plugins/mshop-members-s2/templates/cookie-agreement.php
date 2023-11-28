<?php

$message = get_option( 'msmp_cookie_agreement_message' );
$message = str_replace( '{사이트명}', wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ), $message );

?>
<style>
    .cookie-agreement {
        width: 100%;
        background-color: #ececec;
        border: 1px solid #e0dede;
        padding: 20px;
        font-size: 15px;
        font-weight: bold;
        display: flex;
        box-sizing: border-box;
        align-items: center;
    }

    .cookie-agreement .message {
        flex: 1;
    }

    @media (max-width: 768px) {
        .cookie-agreement {
            flex-flow: column;
        }

        .cookie-agreement .message {
            margin-bottom: 15px;
        }
    }

    .cookie-agreement .button.agreement {
        color: #fff !important;
        background-color: #446084 !important;
        background-color: var(--primary-color);
        border-color: rgba(0, 0, 0, 0.05);
        position: relative;
        display: inline-block;
        background-color: transparent;
        text-transform: uppercase;
        font-size: .97em;
        letter-spacing: .03em;
        -ms-touch-action: none;
        touch-action: none;
        cursor: pointer;
        font-weight: bolder;
        text-align: center;
        color: currentColor;
        text-decoration: none;
        border: 1px solid transparent;
        vertical-align: middle;
        border-radius: 0;
        text-shadow: none;
        line-height: 2.4em;
        min-height: 2.5em;
        padding: 0 1.2em;
        max-width: 100%;
        transition: transform .3s, border .3s, background .3s, box-shadow .3s, opacity .3s, color .3s;
        text-rendering: optimizeLegibility;
        box-sizing: border-box;
        margin: 0 !important;
    }
</style>
<div class="cookie-agreement" style="display: none;">
    <div class="message"><?php echo nl2br( $message ); ?></div>
    <div>
        <input type="button" class="button agreement" value="<?php _e( "동의합니다", "mshop-members-s2" ); ?>">
    </div>
</div>
