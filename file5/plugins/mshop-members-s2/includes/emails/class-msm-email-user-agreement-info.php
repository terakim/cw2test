<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'MSM_Email_User_Agreement_Information' ) ) :


    class MSM_Email_User_Agreement_Information extends WC_Email {
        public $user = null;
        public function __construct() {
            $this->id             = 'email_user_agreement_info';
            $this->customer_email = true;

            $this->title          = __( '정기적 수신동의 확인 안내', 'mshop-members-s2' );
            $this->description    = __( '정기적 수신동의 확인 안내를 위해 전달되는 메일입니다.', 'mshop-members-s2' );
            $this->template_html  = 'emails/email-user-agreement-information.php';
            $this->template_plain = 'emails/email-user-agreement-information.php';

            $this->subject = __( '[{site_title}] 정기적 수신동의 확인 안내', 'mshop-members-s2' );
            $this->heading = __( '정기적 수신동의 확인 안내', 'mshop-members-s2' );

            // Trigger
            add_action( 'msm_send_user_agreement_info_email_notification', array( $this, 'trigger' ) );

            // Call parent constructor
            parent::__construct();
        }
        public function trigger( $user ) {
            $this->setup_locale();

            $this->user      = $user;
            $this->recipient = $user->user_email;

            if ( ! $this->is_enabled() || empty( $this->get_recipient() ) ) {
                return;
            }

            $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );

            $this->restore_locale();
        }
        public function get_content_html() {
            return msm_get_template_html( $this->template_html, array(
                'email_heading' => $this->get_heading(),
                'blogname'      => get_option('msm_personal_info_blogname') ? get_option('msm_personal_info_blogname') : $this->get_blogname(),
                'button_color'  => get_option('msm_personal_info_button_color', '42839f'),
                'sent_to_admin' => false,
                'plain_text'    => false,
                'email'         => $this,
                'user_login'    => $this->user->user_login
            ), '', MSM()->plugin_path() . '/templates/' );
        }
        public function get_content_plain() {
            return msm_get_template_html( $this->template_plain, array(
                'email_heading' => $this->get_heading(),
                'blogname'      => get_option('msm_personal_info_blogname') ? get_option('msm_personal_info_blogname') : $this->get_blogname(),
                'button_color'  => get_option('msm_personal_info_button_color', '42839f'),
                'sent_to_admin' => false,
                'plain_text'    => true,
                'email'         => $this,
                'user_login'    => $this->user->user_login
            ) );
        }
        public function init_form_fields() {
            $this->form_fields = array(
                'enabled'    => array(
                    'title'   => __( 'Enable/Disable', 'woocommerce' ),
                    'type'    => 'checkbox',
                    'label'   => __( 'Enable this email notification', 'woocommerce' ),
                    'default' => 'yes'
                ),
                'subject'    => array(
                    'title'       => __( 'Subject', 'woocommerce' ),
                    'type'        => 'text',
                    'description' => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'woocommerce' ), $this->subject ),
                    'placeholder' => __( '[{site_title}] 정기적 수신동의 확인 안내', 'mshop-members-s2' ),
                    'default'     => '',
                    'desc_tip'    => true
                ),
                'email_type' => array(
                    'title'       => __( 'Email type', 'woocommerce' ),
                    'type'        => 'select',
                    'description' => __( 'Choose which format of email to send.', 'woocommerce' ),
                    'default'     => 'html',
                    'class'       => 'email_type wc-enhanced-select',
                    'options'     => $this->get_email_type_options(),
                    'desc_tip'    => true
                ),
                'blogname'    => array(
                    'title'       => __( '사이트명', 'woocommerce' ),
                    'type'        => 'text',
                    'description' => sprintf( __( '사이트명 미 입력 시 워드프레스 기본 설정의 사이트 제목으로 대체됩니다.' ) ),
                    'placeholder' => __( '사이트명을 입력해주세요.', 'mshop-members-s2' ),
                    'default'     => '',
                    'desc_tip'    => true
                ),
                'color'    => array(
                    'title'       => __( '색상 코드', 'woocommerce' ),
                    'type'        => 'text',
                    'description' => sprintf( __( '템플릿의 색상 코드를 입력해주세요. 기본값은 #000000 입니다.' ) ),
                    'placeholder' => __( '이메일 템플릿의 색상 코드를 입력해주세요.', 'mshop-members-s2' ),
                    'default'     => '#000000',
                    'desc_tip'    => true
                ),
            );
        }
    }

endif;

return new MSM_Email_User_Agreement_Information();
