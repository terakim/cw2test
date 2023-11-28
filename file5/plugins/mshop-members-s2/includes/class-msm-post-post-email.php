<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'MSM_Post_Post_Email' ) ) {

    class MSM_Post_Post_Email {
        public static function init() {
            add_filter( 'woocommerce_email_classes', array ( __CLASS__, 'woocommerce_email_classes' ) );

            add_action( 'msm_post_post_email', array ( __CLASS__, 'send_email' ), 10, 3 );
        }


        static function woocommerce_email_classes( $emails ) {

            include( 'emails/class-msm-email-post.php' );

            $emails[ 'MSM_Email_Post' ] = new MSM_Email_Post();

            return $emails;
        }

        static function send_email( $msm_post_post_params, $msm_post_post_action, $post_id ) {
            WC_Emails::instance();

            do_action( 'msm_post_post_email_notification', $msm_post_post_params, $msm_post_post_action, $post_id );
        }
    }

    MSM_Post_Post_Email::init();

}