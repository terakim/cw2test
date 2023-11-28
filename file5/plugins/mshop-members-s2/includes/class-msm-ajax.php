<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class MSM_Ajax {
	static $slug;

	public static function init() {
		if ( ! WP_DEBUG || ( WP_DEBUG && ! WP_DEBUG_DISPLAY ) ) {
			@ini_set( 'display_errors', 0 );
		}
		$GLOBALS['wpdb']->hide_errors();

		self::$slug = MSM()->slug();

		self::add_ajax_events();
	}
	public static function add_ajax_events() {

		$ajax_events = array(
			'request_certificate_number'        => true,
			'validate_certificate_number'       => true,
			'load_members_form'                 => true,
			'submit'                            => true,
			'check_duplicate'                   => true,
			'request_email_certificate_number'  => true,
			'validate_email_certificate_number' => true,
		);

		if ( is_admin() ) {
			$ajax_events = array_merge( $ajax_events, array(
				'search_page'                => false,
				'page_search'                => false,
				'search_taxonomy'            => false,
				'search_product'             => false,
				'search_payment_method'      => false,
				'search_msm_field'           => false,
				'search_post_type'           => false,
				'search_post_status'         => false,
				'form_search'                => false,
				'update_settings'            => false,
				'update_role_settings'       => false,
				'update_field_settings'      => false,
				'update_user_field_settings' => false,
				'update_settings_social'     => false,
				'update_profile_settings'    => false,
				'install_pages'              => false,
				'install_forms'              => false,
				'install_agreements'         => false,
				'export_forms'               => false,
				'install_default_forms'      => false,
				'import_forms2'              => false,
				'search_agreement_terms'     => false,
				'search_post_category'       => false,
				'approved'                   => false,
				'rejected'                   => false
			) );
		}

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_' . msm_ajax_command( $ajax_event ), array( __CLASS__, $ajax_event ) );

			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_' . msm_ajax_command( $ajax_event ), array( __CLASS__, $ajax_event ) );
			}
		}
	}

	public static function install_pages() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( '사용권한이 없습니다.', 'mshop-members-s2' ) );
		}

		MSM_Install_Data::install_pages();

		wp_send_json_success( __( '엠샵 멤버스 기본 페이지가 생성되었습니다.', 'mshop-members-s2' ) );
	}

	public static function install_forms() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( '사용권한이 없습니다.', 'mshop-members-s2' ) );
		}

		MSM_Install_Data::install_forms();

		wp_send_json_success( __( '엠샵 멤버스 기본 템플릿이 생성되었습니다.', 'mshop-members-s2' ) );
	}

	public static function install_agreements() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( '사용권한이 없습니다.', 'mshop-members-s2' ) );
		}

		MSM_Install_Data::install_agreements();

		wp_send_json_success( __( '엠샵 멤버스 이용약관이 생성되었습니다.', 'mshop-members-s2' ) );
	}

	public static function install_default_forms() {
		MSM_Install_Data::import_forms();

		wp_send_json_success();
	}

	public static function import_forms2() {
		if ( count( $_FILES ) > 0 ) {
			$file = current( $_FILES );

			$contents = file_get_contents( $file['tmp_name'] );
			$bom      = pack( 'H*', 'EFBBBF' );
			$contents = preg_replace( "/^$bom/", '', $contents );

			$forms = json_decode( $contents, true );
			if ( is_null( $forms ) ) {
				wp_send_json_error( '잘못된 파일입니다.' );
			}

			MSM_Install_Data::import_msm_forms( $forms );

			wp_send_json_success( array( 'message' => '정상적으로 처리되었습니다.' ) );
		}

		wp_send_json_error();
	}

	public static function export_msm_forms( $forms ) {
		$fileName = 'msm_forms_' . date( 'Y-m-d' ) . '.json';

		header( 'Content-Description: File Transfer' );
		header( 'Content-Type: application/json' );
		header( 'Content-Disposition: attachment; filename=' . $fileName );

		$form_data = array();
		foreach ( $forms as $form ) {
			$form_types      = array();
			$form_categories = get_the_terms( $form->id, 'mshop_members_form_cat' );
			if ( ! empty( $form_categories ) ) {
				$form_types = wp_list_pluck( $form_categories, 'slug' );
			}

			$form_data[] = array(
				'title'    => $form->post->post_title,
				'content'  => $form->post->post_content,
				'name'     => $form->post->post_name,
				'category' => implode( ',', $form_types ),
				'metas'    => array(
					'_msm_pre_conditions'            => get_post_meta( $form->post->ID, '_msm_pre_conditions', true ),
					'_msm_submit_actions'            => get_post_meta( $form->post->ID, '_msm_submit_actions', true ),
					'msm_pre_conditions'             => get_post_meta( $form->post->ID, 'msm_pre_conditions', true ),
					'msm_submit_actions'             => get_post_meta( $form->post->ID, 'msm_submit_actions', true ),
					'_submit_action'                 => get_post_meta( $form->post->ID, '_submit_action', true ),
					'_custom_action'                 => get_post_meta( $form->post->ID, '_custom_action', true ),
					'_write_post_action_post_type'   => get_post_meta( $form->post->ID, '_write_post_action_post_type', true ),
					'_write_post_action_post_status' => get_post_meta( $form->post->ID, '_write_post_action_post_status', true ),
					'_application_role'              => get_post_meta( $form->post->ID, '_application_role', true ),
					'_approve_method'                => get_post_meta( $form->post->ID, '_approve_method', true ),
					'_custom_css'                    => get_post_meta( $form->post->ID, '_custom_css', true ),
					'_custom_style'                  => get_post_meta( $form->post->ID, '_custom_style', true ),
				)
			);
		}

		ob_clean();
		flush();

		echo json_encode( $form_data, JSON_HEX_QUOT );

		exit;
	}

	public static function export_forms() {
		self::export_msm_forms( MSM_Manager::get_forms() );
	}
	public static function submit() {

		if ( isset( $_REQUEST['form_ids'] ) ) {

			try {
				$response = '';
				$form_ids = explode( ',', $_REQUEST['form_ids'] );
				if ( apply_filters( 'msm_verify_nonce', is_user_logged_in() && ! wp_verify_nonce( $_REQUEST['msm_nonce'], 'mshop-members-s2-' . end( $form_ids ) ) ) ) {
					if ( function_exists( 'WC' ) ) {
						WC()->session->destroy_session();
						WC()->session->cleanup_sessions();
					}
					wp_send_json_error( array( 'message' => __( '잘못된 요청입니다. 페이지를 새로고침 하신 후 다시 시도해주세요.', 'mshop-members-s2' ) ) );
				}

				foreach ( $form_ids as $form_id ) {
					// Get Members Form
					$form = mfd_get_form( $form_id );

					if ( $form instanceof MSM_Form ) {
						// Parse Form Parameter
						parse_str( urldecode( $_REQUEST[ 'form_' . $form_id ] ), $params );

						do_action( 'msm_before_submit_form', $params, $form );
						do_action( $form->get_submit_action(), $params, $form );
						if ( 'msm_action_do_action' == $form->get_submit_action() && ! empty( $form->custom_action ) ) {
							do_action( $form->custom_action, $params, $form );
						}

						do_action( 'msm_submit', $params, $form );
						if ( in_array( $form->get_submit_action(), apply_filters( 'msm_post_processing_action', array( 'msm_action_none' ) ) ) ) {
							MSM_Manager::add_post_processing_data( $form, $params );
						}

						// Do Post Actions
						$response = array();

						if ( ! empty( $form->msm_submit_actions ) ) {
							foreach ( $form->msm_submit_actions as $action ) {
								$response = apply_filters( 'msm-post-actions-' . $action['action_type'], $response, $form, $action, $params );

								if ( is_wp_error( $response ) ) {
									wp_send_json_error( $response->get_error_message() );
								}
							}
						}

						do_action( 'msm_after_submit_form', $params, $form );
					}

				}

				do_action( 'msm_submit_success' );

				wp_send_json_success( apply_filters( 'msm_submit_response', $response ) );

			} catch ( Exception $e ) {
				$response = array(
					'message' => $e->getMessage()
				);

				wp_send_json_error( apply_filters( 'msm_submit_response', $response ) );
			}
		}

		wp_send_json_error( __( '잘못된 요청입니다', 'mshop-members-s2' ) );
	}

	public static function update_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}

		$settings = new MSM_Settings_Members();
		$settings->update_settings();
	}

	public static function update_field_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}

		MSM_Settings_Fields::update_settings();
	}

	public static function update_user_field_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}

		MSM_Settings_User_Fields::update_settings();
	}

	public static function update_settings_social() {
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}

		MSM_Settings_Members_Social::update_settings();
	}

	public static function update_profile_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}

		MSM_Settings_Profile::update_settings();
	}
	public static function agree_terms_and_conditions() {
		if ( ! empty( $_REQUEST['agree_all'] ) && 'on' == $_REQUEST['agree_all'] ) {
			if ( is_user_logged_in() ) {
				update_user_meta( get_current_user_id(), '_mshop_acceptance_of_terms', 'yes' );
				wp_send_json_success();
			} else {
				set_transient( msm_get( $_COOKIE, 'wp_msm_state' ) . '-mshop_accept_terms_and_conditions', 'yes', 5 * MINUTE_IN_SECONDS );
				wp_send_json_success( wc_get_checkout_url() );
			}
		} else {
			wp_send_json_error( __( '약관에 동의하셔야 합니다.' ) );
		}
	}

	public static function form_search() {
		$results = array();

		foreach ( msm_get_members_forms() as $key => $value ) {
			$results[] = array(
				"name"  => $value,
				"value" => $key
			);
		}

		$respose = array(
			'success' => true,
			'results' => $results
		);

		echo json_encode( $respose );

		die();
	}

	public static function update_role_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}

		MSM_Settings_Members_Role::update_settings();
	}
	static function target_search_posts_title_like( $where, &$wp_query ) {
		global $wpdb;
		if ( $posts_title = $wp_query->get( 'posts_title' ) ) {
			$where .= ' AND ' . $wpdb->posts . '.post_title LIKE "%' . $posts_title . '%"';
		}

		return $where;
	}

	static function search_taxonomy() {
		$results    = array();
		$taxonomies = get_taxonomies(
			array(
				'show_ui'           => true,
				'show_in_nav_menus' => true,
				'public'            => true,
				'hierarchical'      => true
			), 'object' );

		foreach ( $taxonomies as $key => $value ) {
			$results[] = array(
				'name'  => $value->label,
				'value' => $value->name
			);
		}

		$respose = array(
			'success' => true,
			'results' => $results
		);

		echo json_encode( $respose );

		die();
	}

	static function search_payment_method() {
		$results = array();

		foreach ( WC()->payment_gateways()->payment_gateways() as $gateway_id => $payment_gateway ) {
			$results[] = array(
				'name'  => $payment_gateway->get_method_title() . ' - ' . $payment_gateway->get_title(),
				'value' => $gateway_id
			);
		}

		$respose = array(
			'success' => true,
			'results' => $results
		);

		echo json_encode( $respose );

		die();
	}

	static function search_product() {
		$keyword = ! empty( $_REQUEST['args'] ) ? $_REQUEST['args'] : '';

		add_filter( 'posts_where', array( __CLASS__, 'target_search_posts_title_like' ), 10, 2 );
		$args = array(
			'post_type'      => array( 'product', 'product_variation' ),
			'posts_title'    => $keyword,
			'post_status'    => 'publish',
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'posts_per_page' => 20
		);

		$query = new WP_Query( $args );

		remove_filter( 'posts_where', array( __CLASS__, 'target_search_posts_title_like' ) );

		$results = array();

		foreach ( $query->posts as $post ) {
			$results[] = array(
				"name"  => '[#' . $post->ID . '] ' . $post->post_title,
				"value" => $post->ID
			);
		}

		$respose = array(
			'success' => true,
			'results' => $results
		);

		echo json_encode( $respose );

		die();
	}

	static function search_msm_field() {
		$results = array();

		foreach ( MSM_Fields::get_fields() as $key => $value ) {
			$results[] = array(
				'name'  => $value,
				'value' => $key
			);
		}

		$respose = array(
			'success' => true,
			'results' => $results
		);

		echo json_encode( $respose );

		die();
	}

	static function search_post_type() {
		$results    = array();
		$post_types = get_post_types( array(), 'objects' );

		foreach ( $post_types as $post_type => $object ) {
			$results[] = array(
				'name'  => $object->label,
				'value' => $post_type
			);
		}

		$respose = array(
			'success' => true,
			'results' => $results
		);

		echo json_encode( $respose );

		die();
	}

	static function search_post_status() {
		global $wp_post_statuses;
		$results = array();

		foreach ( $wp_post_statuses as $post_status => $object ) {
			$results[] = array(
				'name'  => $object->label . ' ( ' . $post_status . ' ) ',
				'value' => $post_status
			);
		}

		$respose = array(
			'success' => true,
			'results' => $results
		);

		echo json_encode( $respose );

		die();
	}

	static function search_agreement_terms() {
		$terms = get_terms( 'mshop_agreement_cat', array(
			'hide_empty' => false,
		) );

		$results = array();

		foreach ( $terms as $term ) {
			$results[] = array(
				"name"  => $term->name,
				"value" => $term->slug
			);
		}

		$respose = array(
			'success' => true,
			'results' => $results
		);

		echo json_encode( $respose );

		die();
	}

	static function search_page() {
		$keyword = ! empty( $_REQUEST['args'] ) ? $_REQUEST['args'] : '';

		add_filter( 'posts_where', array( __CLASS__, 'target_search_posts_title_like' ), 10, 2 );
		$args = array(
			'post_type'      => 'page',
			'posts_title'    => $keyword,
			'post_status'    => 'publish',
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'posts_per_page' => - 1
		);

		$query = new WP_Query( $args );

		remove_filter( 'posts_where', array( __CLASS__, 'target_search_posts_title_like' ) );

		$results = array(
			array(
				"name"  => __( '사용안함', 'mshop-members-s2' ),
				"value" => '0'
			)
		);

		foreach ( $query->posts as $post ) {
			$results[] = array(
				"name"  => '[#' . $post->ID . '] ' . $post->post_title,
				"value" => $post->ID
			);
		}
		$respose = array(
			'success' => true,
			'results' => $results
		);

		echo json_encode( $respose );

		die();
	}

	static function page_search() {

		$args = array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'posts_per_page' => - 1
		);

		$query = new WP_Query( $args );

		$results = array();

		foreach ( $query->posts as $post ) {
			$results[] = array(
				"name"  => '[#' . $post->ID . '] ' . $post->post_title,
				"value" => $post->post_name
			);
		}
		$respose = array(
			'success' => true,
			'results' => $results
		);

		echo json_encode( $respose );

		die();
	}
	static function make_taxonomy_tree( $args, $depth = 0, $parent = 0, $paths = array() ) {
		$results = array();

		$args['parent'] = $parent;
		$terms          = get_categories( $args );

		foreach ( $terms as $term ) {
			$current_paths = array_merge( $paths, array( $term->name ) );
			$results[]     = array(
				"name"  => '<span class="tree-indicator-desc">' . implode( '-', $current_paths ) . '</span><span class="tree-indicator" style="margin-left: ' . ( $depth * 8 ) . 'px;">' . $term->name . '</span>',
				"value" => $term->term_id
			);

			$results = array_merge( $results, self::make_taxonomy_tree( $args, $depth + 1, $term->term_id, $current_paths ) );
		}

		return $results;
	}
	static function search_post_category() {
		$args = array();

		if ( ! empty( $_REQUEST['args'] ) ) {
			$args['name__like'] = $_REQUEST['args'];
		}

		$results = self::make_taxonomy_tree( $args );

		$respose = array(
			'success' => true,
			'results' => $results
		);

		echo json_encode( $respose );
		die();
	}

	static function request_certificate_number() {
		check_ajax_referer( 'mshop-members-s2' );

		try {
			$hash = MSM_Phone_Certification::send_certification_number( $_REQUEST['phone_number'], $_REQUEST['find_login'], $_REQUEST['allow_duplicate'], $_REQUEST['temporary_password'], $_REQUEST['user_login'], $_REQUEST['form_slug'] );

			wp_send_json_success( array( 'certification_hash' => $hash ) );
		} catch ( Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}
	}

	static function validate_certificate_number() {
		check_ajax_referer( 'mshop-members-s2' );

		try {
			MSM_Phone_Certification::validate_certification_number( $_REQUEST['phone_number'], $_REQUEST['certificate_hash'], $_REQUEST['certification_number'], $_REQUEST['form_slug'] );

			wp_send_json_success();
		} catch ( Exception $e ) {
			wp_send_json_error( sprintf( "[%s] %s", $e->getCode(), $e->getMessage() ) );
		}
	}
	static function request_email_certificate_number() {
		check_ajax_referer( 'mshop-members-s2' );

		try {
			$user_email = trim( sanitize_text_field( msm_get( $_POST, 'field_value' ) ) );

			$hash = MSM_Email_Authenticate::send_certification_number( $user_email );

			wp_send_json_success( array( 'certification_hash' => $hash ) );
		} catch ( Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}
	}
	static function validate_email_certificate_number() {
		check_ajax_referer( 'mshop-members-s2' );

		try {
			$user_email           = trim( sanitize_text_field( msm_get( $_POST, 'field_value' ) ) );
			$certificate_hash     = trim( sanitize_text_field( msm_get( $_POST, 'certificate_hash' ) ) );
			$certification_number = trim( sanitize_text_field( msm_get( $_POST, 'certification_number' ) ) );

			MSM_Email_Authenticate::validate_certification_number( $user_email, $certificate_hash, $certification_number );

			wp_send_json_success();
		} catch ( Exception $e ) {
			wp_send_json_error( sprintf( "[%s] %s", $e->getCode(), $e->getMessage() ) );
		}
	}

	static function load_members_form() {
		check_ajax_referer( 'mshop-members-s2' );

		try {
			ob_start();
			echo do_shortcode( "[mshop_form_designer slug='" . $_REQUEST['form_slug'] . "' default=true]" );
			$fragment = ob_get_clean();

			wp_send_json_success( $fragment );
		} catch ( Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}
	}
	public static function approved() {
		if ( is_super_admin() && check_admin_referer( 'msm-role-application' ) ) {
			$role_application = new MSM_Role_Application( $_GET['request_id'] );
			$role_application->approved();
		}

		wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url( 'edit.php?post_type=mshop_role_request' ) );
		exit;
	}
	public static function rejected() {
		if ( is_super_admin() && check_admin_referer( 'msm-role-application' ) ) {
			$role_application = new MSM_Role_Application( $_GET['request_id'] );
			$role_application->rejected();
		}

		wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url( 'edit.php?post_type=mshop_role_request' ) );
		exit;
	}
	public static function check_duplicate() {
		check_ajax_referer( 'mshop-members-s2' );

		try {
			$field_name  = sanitize_text_field( msm_get( $_POST, 'field_name' ) );
			$field_value = sanitize_text_field( msm_get( $_POST, 'field_value' ) );
			$field_label = sanitize_text_field( msm_get( $_POST, 'field_label' ) );

			if ( empty( $field_name ) || empty( $field_value ) || ! in_array( $field_name, apply_filters( 'msm_check_duplicate_fields', array( 'login', 'user_login' ) ) ) ) {
				throw new Exception( __( '잘못된 요청입니다.', 'mshop-members-s2' ), '5001' );
			}

			if ( 'login' == $field_name ) {
				$user = get_user_by( 'login', $field_value );
			} else if ( 'user_login' == $field_name ) {
				if ( ! is_email( $field_value ) ) {
					throw new Exception( __( '이메일 형식이 올바르지 않습니다.', 'mshop-members-s2' ), '5002' );
				} else {
					$user = get_user_by( 'email', $field_value );
				}
			} else {
				$user = apply_filters( 'msm_duplicate_checked_user', null, $field_name, $field_value );
			}

			if ( is_a( $user, 'WP_User' ) ) {
				throw new Exception( sprintf( __( '이미 사용중인 %s 입니다.', 'mshop-members-s2' ), $field_label ), '5003' );
			}

			wp_send_json_success( sprintf( __( '사용할 수 있는 %s 입니다.', 'mshop-members-s2' ), $field_label ) );
		} catch ( Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}
	}

}

MSM_Ajax::init();
