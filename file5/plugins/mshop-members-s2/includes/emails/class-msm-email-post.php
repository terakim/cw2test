<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MSM_Email_Post' ) ) :


	class MSM_Email_Post extends WC_Email {
		public $user_login;
		public $user_email;
		public $reset_key;

		public $post_id;
		public $post_params;


		public function __construct() {

			$this->id             = 'msm_post_post_email';
			$this->title          = __( '포스트 등록 안내', 'mshop-members-s2' );
			$this->description    = __( '포스트 등록 안내', 'mshop-members-s2' );
			$this->template_html  = 'emails/post-email-body.php';
			$this->template_plain = 'emails/plain/post-email-body.php';
			$this->subject        = __( '포스트 등록 안내', 'mshop-members-s2' );
			$this->heading        = __( '포스트 등록 안내', 'mshop-members-s2' );

			// Trigger
			add_action( 'msm_post_post_email_notification', array( $this, 'trigger' ), 10, 3 );


			// Call parent constructor
			parent::__construct();
		}

		public function trigger( $post_params, $post_action, $post_id ) {
			$this->post_params = $post_params;
			$this->post_id     = $post_id;

			$this->recipient = $this->get_option( 'recipient', $post_action['email_recipient'] );

			remove_action( 'woocommerce_email_header', array( WC_Emails::instance(), 'email_header' ) );
			add_action( 'woocommerce_email_header', array( $this, 'email_header' ) );


			if ( ! $this->is_enabled() || empty( $this->get_recipient() ) ) {
				return;
			}

			$subject = empty( $post_action['email_subject']) ? $this->get_subject() : $post_action['email_subject'];

			foreach( $post_params as $key => $value ) {
				$subject = str_replace( "{{$key}}", $value, $subject );
			}

			$this->send( $this->get_recipient(), $subject, $this->get_content(), $this->get_headers(), $this->get_attachments() );

		}


		public function email_header( $email_heading ) {
			msm_get_template( 'emails/post-email-header.php', array( 'email_heading' => $email_heading ), '', MSM()->template_path() );
		}

		public function get_content_html() {
			return msm_get_template_html( $this->template_html, array(
				'email_heading' => $this->get_heading(),
				'blogname'      => $this->get_blogname(),
				'sent_to_admin' => false,
				'plain_text'    => false,
				'email'         => $this,
				'post_params'   => $this->post_params,
				'post_id'       => $this->post_id
			), array(), MSM()->plugin_path() . '/templates/' );
		}

		public function get_content_plain() {
			return msm_get_template_html( $this->template_plain, array(
				'email_heading' => $this->get_heading(),
				'user_login'    => $this->user_login,
				'reset_key'     => $this->reset_key,
				'blogname'      => $this->get_blogname(),
				'sent_to_admin' => false,
				'plain_text'    => true,
				'email'         => $this
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
				'recipient'  => array(
					'title'       => __( 'Recipient(s)', 'woocommerce' ),
					'type'        => 'text',
					'description' => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', 'woocommerce' ), esc_attr( get_option( 'admin_email' ) ) ),
					'placeholder' => '',
					'default'     => '',
					'desc_tip'    => true
				),
				'subject'    => array(
					'title'       => __( 'Subject', 'woocommerce' ),
					'type'        => 'text',
					'description' => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'woocommerce' ), $this->subject ),
					'placeholder' => '',
					'css'         => 'color:white;',
					'default'     => '',
					'desc_tip'    => true
				),
				'heading'    => array(
					'title'       => __( 'Email Heading', 'woocommerce' ),
					'type'        => 'text',
					'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'woocommerce' ), $this->heading ),
					'placeholder' => '',
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

