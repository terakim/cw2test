<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MSM_Email_Authentication' ) ) :


	class MSM_Email_Authentication extends WC_Email {
		public $user = null;
		public function __construct() {

			$this->id             = 'email_authencation';
			$this->customer_email = true;

			$this->title          = __( '이메일 인증', 'mshop-members-s2' );
			$this->description    = __( '신규회원 가입시 이메일 인증을 위해 전달되는 메일입니다.', 'mshop-members-s2' );
			$this->template_html  = 'emails/email-authentication.php';
			$this->template_plain = 'emails/email-authentication.php';

			$this->subject = __( '[{site_title}] 이메일 인증', 'mshop-members-s2' );
			$this->heading = __( '이메일 인증', 'mshop-members-s2' );

			// Trigger
			add_action( 'msm_send_authentication_email_notification', array( $this, 'trigger' ) );

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
				'blogname'      => $this->get_blogname(),
				'sent_to_admin' => false,
				'plain_text'    => false,
				'email'         => $this,
				'auth_key'      => get_user_meta( $this->user->ID, 'msm_email_auth_key', true ),
				'user_login'    => $this->user->user_login
			), '', MSM()->plugin_path() . '/templates/' );
		}
		public function get_content_plain() {
			return msm_get_template_html( $this->template_plain, array(
				'email_heading' => $this->get_heading(),
				'blogname'      => $this->get_blogname(),
				'sent_to_admin' => false,
				'plain_text'    => true,
				'email'         => $this,
				'auth_key'      => get_user_meta( $this->user->ID, 'msm_email_auth_key', true ),
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
					'placeholder' => __( '[{site_title}] 이메일 인증', 'mshop-members-s2' ),
					'default'     => '',
					'desc_tip'    => true
				),
				'heading'    => array(
					'title'       => __( 'Email Heading', 'woocommerce' ),
					'type'        => 'text',
					'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'woocommerce' ), $this->heading ),
					'placeholder' => __( '이메일 인증', 'mshop-members-s2' ),
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
				)
			);
		}
	}

endif;

return new MSM_Email_Authentication();
