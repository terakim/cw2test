<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MSM_Email_New_Role_Application' ) ) :


class MSM_Email_New_Role_Application extends WC_Email {
	public $user_login;
	public $user_email;
	public $reset_key;

	public $post_id;
	public function __construct() {

		$this->id             = 'new_role_application';
		$this->title          = __( '등급변경 요청', 'mshop-members-s2' );
		$this->description    = __( '등급변경 요청이 접수되었을때, 관리자에게 전달되는 메일입니다.', 'mshop-members-s2' );
		$this->template_html  = 'emails/new-role-application.php';
		$this->template_plain = 'emails/plain/new-role-application.php';

		$this->subject = __( '신규 등급변경 요청', 'mshop-members-s2' );
		$this->heading = __( '신규 등급변경 요청', 'mshop-members-s2' );

		$this->recipient = $this->get_option( 'recipient', get_option( 'admin_email' ) );

		// Trigger
		add_action( 'msm_new_role_application_notification', array( $this, 'trigger' ), 10, 2 );

		// Call parent constructor
		parent::__construct();
	}

	function get_user_cert_info( $post_id ) {
		$values = array();

		if ( get_post_meta( $post_id, 'mshop_auth_method', true ) == "checkplus" ) {
			$values[] = __( '인증방식 : 휴대폰', 'mshop-members-s2' );
		} else if ( get_post_meta( $post_id, 'mshop_auth_method', true ) == "ipin" ) {
			$values[] = __( '인증방식 : 아이핀', 'mshop-members-s2' );
		} else {
			return '';
		}

		$values[] = '실명 : ' . get_post_meta( $post_id, 'mshop_auth_name', true );
		$values[] = '생년월일 : ' . get_post_meta( $post_id, 'mshop_auth_birthdate', true );
		$values[] = '성별 : ' . ( '1' == get_post_meta( $post_id, 'mshop_auth_gender', true ) ? __( '남성', 'mshop-members-s2' ) : __( '여성', 'mshop-members-s2' ) );
		$values[] = '국적 : ' . ( '1' == get_post_meta( $post_id, 'mshop_auth_nationalinfo', true ) ? __( '외국인', 'mshop-members-s2' ) : __( '내국인', 'mshop-members-s2' ) );

		echo implode( '<br>', $values );
	}
	public function trigger( $post_id ) {

		$this->post_id = $post_id;

		if ( ! $this->is_enabled() || empty( $this->get_recipient() ) ) {
			return;
		}

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );

	}
	public function get_content_html() {
		return msm_get_template_html( $this->template_html, array(
			'email_heading' => $this->get_heading(),
			'blogname'      => $this->get_blogname(),
			'sent_to_admin' => false,
			'plain_text'    => false,
			'email'         => $this,
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
			'enabled' => array(
				'title'         => __( 'Enable/Disable', 'woocommerce' ),
				'type'          => 'checkbox',
				'label'         => __( 'Enable this email notification', 'woocommerce' ),
				'default'       => 'yes'
			),
			'recipient' => array(
				'title'         => __( 'Recipient(s)', 'woocommerce' ),
				'type'          => 'text',
				'description'   => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', 'woocommerce' ), esc_attr( get_option('admin_email') ) ),
				'placeholder'   => '',
				'default'       => '',
				'desc_tip'      => true
			),
			'subject' => array(
				'title'         => __( 'Subject', 'woocommerce' ),
				'type'          => 'text',
				'description'   => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'woocommerce' ), $this->subject ),
				'placeholder'   => '',
				'default'       => '',
				'desc_tip'      => true
			),
			'heading' => array(
				'title'         => __( 'Email Heading', 'woocommerce' ),
				'type'          => 'text',
				'description'   => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'woocommerce' ), $this->heading ),
				'placeholder'   => '',
				'default'       => '',
				'desc_tip'      => true
			),
			'email_type' => array(
				'title'         => __( 'Email type', 'woocommerce' ),
				'type'          => 'select',
				'description'   => __( 'Choose which format of email to send.', 'woocommerce' ),
				'default'       => 'html',
				'class'         => 'email_type wc-enhanced-select',
				'options'       => $this->get_email_type_options(),
				'desc_tip'      => true
			)
		);
	}
}

endif;

return new MSM_Email_New_Role_Application();
