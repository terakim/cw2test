<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
function msm_ajax_command( $command ) {
    return MSM_AJAX_PREFIX . '_' . $command;
}

function msm_is_ajax() {
	if ( function_exists( 'is_ajax' ) ) {
		return is_ajax();
	} else {
		return function_exists( 'wp_doing_ajax' ) ? wp_doing_ajax() : defined( 'DOING_AJAX' );
	}
}

function msm_is_ie9() {
	if ( function_exists( 'is_ie' ) && function_exists( 'get_browser_version' ) ) {
		return is_ie() && get_browser_version() <= 9;
	}

	return false;
}

function msm_start_session() {
	if ( ! headers_sent() && ! session_id() ) {
		session_start();
	}
}

function msm_get_select_fields() {
	if ( false === ( $select_field_taxonomies = get_transient( 'msm_select_field_taxonomies' ) ) ) {
		global $wpdb;

		$select_field_taxonomies = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "msm_select_field_taxonomies order by select_field_name ASC;" );

		set_transient( 'msm_select_field_taxonomies', $select_field_taxonomies );
	}

	return (array) array_filter( apply_filters( 'msm_select_field_taxonomies', $select_field_taxonomies ) );
}

function msm_select_field_taxonomy_name( $select_field_name ) {
	return 'pa_' . urldecode( sanitize_title( $select_field_name ) );
}

function msm_get_select_field_types() {
	return (array) apply_filters( 'msm_select_field_type_selector', array(
		'select' => __( 'Select', 'mshop-members-s2' ),
		'text'   => __( 'Text', 'mshop-members-s2' )
	) );
}

function msm_get_state() {
	$state = '';

	if ( is_user_logged_in() ) {
		$state = get_user_meta( get_current_user_id(), 'msm_oauth_state', true );
	}

	if ( empty( $state ) ) {
		$state = msm_get( $_COOKIE, 'wp_msm_state' );
	}

	return $state;
}
function msm_get_user_role( $user_id = null ) {
	$role = 'guest';

	if ( $user_id === null && is_user_logged_in() ) {
		$user_id = wp_get_current_user();
	}

	if ( is_numeric( $user_id ) ) {
		$user       = new WP_User( $user_id );
		$user_roles = $user->roles;
	} else if ( $user_id instanceof WP_User ) {
		$user_roles = $user_id->roles;
	}

	if ( ! empty( $user_roles ) ) {
		$matched_user_roles = array_intersect( $user_roles, array_keys( msm_get_roles() ) );

		$role = array_shift( $matched_user_roles );
	}

	return apply_filters( 'msm_get_user_role', $role, $user_id );
}
function msm_get( $object, $key, $default = '', $format = null ) {
	$value = $default;

	if ( ! empty( $object ) && ! empty( $object[ $key ] ) ) {
		$value = $object[ $key ];

		if ( ! is_null( $format ) ) {
			$value = sprintf( $format, $value );
		}
	}

	return $value;
}

function msm_get_label( $meta ) {
	return ! empty( $meta['label'] ) ? $meta['label'] : $meta['value'];
}

function mshop_members_get_user_role( $user_id = null ) {
	if ( $user_id === null && is_user_logged_in() ) {
		$user_id = wp_get_current_user();
	}

	if ( is_numeric( $user_id ) ) {
		$user = new WP_User( $user_id );

		return array_shift( $user->roles );
	} else if ( $user_id instanceof WP_User ) {
		$user_roles = $user_id->roles;

		return array_shift( $user_roles );
	}

	return null;
}

function mshop_members_get_user_role_name( $user_id = null ) {
	$user_role = mshop_members_get_user_role( $user_id );
	$role      = get_role( $user_role );

	return $role->name;
}

function msm_get_roles() {
	$results = array();

	if ( ! function_exists( 'get_editable_roles' ) ) {
		require_once( ABSPATH . '/wp-admin/includes/user.php' );
	}

	$roles = wp_roles()->roles;

	$roles['guest'] = array(
		'name' => __( 'Guest', 'mshop-members-s2' )
	);

	$filters = get_option( 'mshop_members_role_filter' );

	if ( ! empty( $filters ) ) {
		foreach ( $filters as $role ) {
			if ( 'yes' === $role['enabled'] && array_key_exists( $role['role'], $roles ) ) {
				$results[ $role['role'] ] = ! empty( $role['nickname'] ) ? $role['nickname'] : $role['name'];
			}
		}
	}

	return $results;
}

function msm_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		$template_path = MSM()->template_path();
	}

	if ( ! $default_path ) {
		$default_path = MSM()->plugin_path() . '/templates/';
	}

	// Look within passed path within the theme - this is priority.
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name
		)
	);

	if ( ! $template ) {
		$template = $default_path . $template_name;
	}

	// Return what we found.
	return apply_filters( 'msm_locate_template', $template, $template_name, $template_path );
}

function msm_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	if ( ! empty( $args ) && is_array( $args ) ) {
		extract( $args );
	}

	$located = msm_locate_template( $template_name, $template_path, $default_path );

	if ( ! file_exists( $located ) ) {
		_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), MSM_VERSION );

		return;
	}

	$located = apply_filters( 'msm_get_template', $located, $template_name, $args, $template_path, $default_path );

	do_action( 'msm_before_template_part', $template_name, $template_path, $located, $args );

	include( $located );

	do_action( 'msm_after_template_part', $template_name, $template_path, $located, $args );
}

function msm_get_template_html( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	ob_start();
	msm_get_template( $template_name, $args, $template_path, $default_path );

	return ob_get_clean();
}
function msm_get_members_forms( $term = null ) {
	$forms = array();

	$args = array(
		'post_type'      => 'mshop_members_form',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'orderby'        => 'title',
		'order'          => 'ASC'
	);

	if ( ! is_null( $term ) ) {
		if ( ! is_array( $term ) ) {
			$term = array( $term );
		}

		$args['tax_query'] = array(
			array(
				'taxonomy' => 'mshop_members_form_cat',
				'field'    => 'slug',
				'terms'    => $term
			)
		);
	}

	$query = new WP_Query( $args );

	foreach ( $query->posts as $post ) {
		$forms[ $post->post_name ] = $post->post_title;
	}

	return apply_filters( 'msm_get_members_forms', $forms, $term );
}
add_filter( 'template_include', 'MSM_Template_Loader::template_include', 999 );
add_action( 'template_redirect', 'MSM_Template_Loader::template_redirect' );
add_action( 'user_register', array( 'MSM_Email_Authenticate', 'maybe_send_email_authentication_email' ) );
if ( 'yes' === get_option( 'mshop_members_use_terms_and_conditions', 'no' ) && 'yes' === get_option( 'mshop_members_require_tac_for_guest', 'no' ) ) {
	add_filter( 'woocommerce_locate_template', 'MSM_Template_Loader::woocommerce_locate_template', 99, 3 );
	add_action( 'woocommerce_new_order', array( 'MSM_Order', 'woocommerce_new_order' ) );
}

add_action( 'wp_login', 'MSM_Action_Login::wp_login', 99, 2 );
add_action( 'woocommerce_customer_reset_password', array( 'MSM_Action_Login', 'woocommerce_customer_reset_password' ), 99 );
add_action( 'comment_form_defaults', 'MSM_Action_Login::comment_form_defaults', 99 );
add_action( 'mshop_members_register_form', 'MSM_Manager::mshop_members_register_form' );

add_action( 'woocommerce_before_my_account', 'MSM_Myaccount::output' );

add_filter( 'wp_nav_menu_objects', 'MSM_Menu::wp_nav_menu_objects', 10, 2 );

add_filter( 'msm_get_roles', 'msm_get_roles' );
add_action( 'msm_action_register', array( 'MSM_Action_Register', 'do_action' ), 10, 3 );
add_action( 'msm_action_login', array( 'MSM_Action_Login', 'do_action' ), 10, 3 );
add_action( 'msm_action_agreement', array( 'MSM_Action_Agreement', 'do_action' ), 10, 3 );
add_action( 'msm_action_lost_passwords', array( 'MSM_Action_Password', 'lost_password_action' ), 10, 3 );
add_action( 'msm_action_temporary_password', array( 'MSM_Action_Password', 'temporary_password_action' ), 10, 3 );
add_action( 'msm_action_ubsubscribe', array( 'MSM_Action_Unsubscribe', 'do_action' ), 10, 3 );
add_action( 'msm_action_write_post', array( 'MSM_Action_Write_Post', 'do_action' ), 10, 3 );
add_action( 'msm_action_find_login', array( 'MSM_Action_Find_Login', 'do_action' ), 10, 3 );

add_filter( 'msm_rule_conditions', array( 'MSM_Rules', 'rule_conditions' ) );
add_filter( 'msm_check_rule_conditions', array( 'MSM_Rules', 'check_rule_conditions' ), 10, 2 );
add_filter( 'msm_check_pre_conditions', array( 'MSM_Rules', 'msm_check_pre_conditions' ), 10, 2 );

add_action( 'woocommerce_after_my_account', array( 'MSM_Action_Unsubscribe', 'output_form' ), 999 );
add_action( 'wp_authenticate_user', array( 'MSM_Action_Unsubscribe', 'wp_authenticate_user' ), 999, 2 );

add_action( 'output_msm_form', 'output_msm_form' );

add_filter( 'login_url', array( 'MSM_Action_Login', 'url' ), 10, 3 );
add_filter( 'register_url', array( 'MSM_Action_Register', 'url' ) );
add_filter( 'woocommerce_new_customer_data', array( 'MSM_Action_Register', 'set_user_role' ) );

function output_msm_form( $slug ) {
	echo do_shortcode( "[mshop_form_designer slug='" . $slug . "' default=true]" );
}

function msm_load_single_template( $template ) {
	global $post;

	if ( $post->post_type == "mshop_members_form" ) {

		$template_name = 'single-mshop_members_form.php';

		if ( $template === get_stylesheet_directory() . '/' . $template_name ) {
			return $template;
		}

		return MSM()->plugin_path() . '/templates/' . $template_name;
	}

	return $template;
}

add_filter( 'single_template', 'msm_load_single_template' );
add_filter( 'show_user_profile', array( 'MSM_Admin_Profile', 'add_members_fields' ) );
add_filter( 'edit_user_profile', array( 'MSM_Admin_Profile', 'add_members_fields' ) );
add_filter( 'user_edit_form_tag', array( 'MSM_Admin_Profile', 'add_form_tag' ) );
add_filter( 'personal_options_update', array( 'MSM_Admin_Profile', 'save_members_fields' ) );
add_filter( 'edit_user_profile_update', array( 'MSM_Admin_Profile', 'save_members_fields' ) );
function msm_wp_targeted_link_rel( $rel, $html = '' ) {
	global $pagenow;

	if ( $pagenow == 'post.php' && 'mshop_members_form' == get_post_type() ) {
		$rel = '';
	}

	return $rel;
}

add_filter( 'wp_targeted_link_rel', 'msm_wp_targeted_link_rel', 10, 2 );
function msm_users_own_attachments( $wp_query_obj ) {

	global $current_user, $pagenow;

	$is_attachment_request = ( $wp_query_obj->get( 'post_type' ) == 'attachment' );

	if ( ! $is_attachment_request ) {
		return;
	}

	if ( ! is_a( $current_user, 'WP_User' ) ) {
		return;
	}

	if ( ! in_array( $pagenow, array( 'upload.php', 'admin-ajax.php' ) ) ) {
		return;
	}

	if ( ! current_user_can( 'delete_pages' ) ) {
		$wp_query_obj->set( 'author', $current_user->ID );
	}

	return;
}

add_filter( 'pre_get_posts', 'msm_users_own_attachments', 100 );


function maybe_output_oauth_error_notification( $form ) {
	$state = '';

	if ( is_user_logged_in() ) {
		$state = get_user_meta( get_current_user_id(), 'msm_oauth_state', true );
	}

	if ( empty( $state ) ) {
		$state = msm_get( $_COOKIE, 'wp_msm_state' );
	}

	$notification = get_transient( 'msm_oauth_error_' . $state );

	if ( ! empty( $notification ) ) {
		echo sprintf( '<div class="ui small negative message">%s</div>', $notification );
		delete_transient( 'msm_oauth_error_' . $state );
	}
}

add_action( 'msm_form_notification', 'maybe_output_oauth_error_notification' );

function msm_enqueue_cookie_script() {
	?>
    <script>
        jQuery( document ).ready( function ( $ ) {
            function getCookie( cname ) {
                let name          = cname + "=";
                let decodedCookie = decodeURIComponent( document.cookie );
                let ca            = decodedCookie.split( ';' );
                for (let i = 0; i < ca.length; i++) {
                    let c = ca[i];
                    while (c.charAt( 0 ) === ' ') {
                        c = c.substring( 1 );
                    }
                    if (c.indexOf( name ) === 0) {
                        return c.substring( name.length, c.length );
                    }
                }
                return "";
            }

            if ('yes' !== getCookie( 'msmp_agree_cookie' )) {
                $( 'div.cookie-agreement' )
                    .css( 'opacity', 0 )
                    .css( 'display', 'flex' )
                    .animate( {
                        opacity: 1
                    } )
            }

            $( 'input.button.agreement' ).on( 'click', function () {
                document.cookie = 'msmp_agree_cookie=yes; path=/; max-age=31536000';
                $( 'div.cookie-agreement' ).remove();
            } );
        } );
    </script>
	<?php
}

add_action( 'wp_footer', 'msm_enqueue_cookie_script', 100 );

function msm_output_cookie_message() {
	if ( 'yes' == get_option( 'msmp_use_cookie_agreement', 'no' ) ) {
		msm_get_template( 'cookie-agreement.php', array(), '', MSM()->plugin_path() . '/templates/' );
	}
}

add_action( 'wp_head', 'msm_output_cookie_message', 1 );

add_action( 'msm_personal_information_notification', array( 'MSM_Personal_Info', 'run' ) );
function msm_get_agreement_status( $user_id, $type ) {
    $agreement = get_user_meta( $user_id, $type . '_agreement', true );
    if( ! empty( $agreement ) ) {
        return __( '수신 동의', 'mshop-members-s2' );
    } else {
        return __( '수신 거부', 'mshop-members-s2' );
    }
}
function msm_get_agreement_date( $user_id, $type ) {
    if( ! empty( get_user_meta( $user_id, $type . '_agreement', true ) ) ) {
        $agreement_date = get_user_meta( $user_id, $type . '_update_date', true );
        if( ! empty( $agreement_date ) ) {
            $agreement_date = date( 'Y년 m월 d일', strtotime( $agreement_date ) );
            return $agreement_date;
        } else {
            return get_option( 'msm_agreement_information_site_date', true );
        }
    } else {
        return __( '동의하지 않음', 'mshop-members-s2' );
    }
}
function msm_get_user_agreement_date_params( $user_id, $type ) {
    if( ! empty( get_user_meta( $user_id, $type . '_agreement', true ) ) ) {
        $date = get_user_meta( $user_id, $type . '_update_date', true );
    }

    if( ! empty( $date ) ) {
        return date( 'Y년 m월 d일', strtotime( $date ) );
    } else {
        return get_option( 'msm_agreement_information_site_date', true );
    }
}

add_action( 'msm_agreement_information_notification', array( 'MSM_User_Agreement_Info', 'run' ) );

//포스트 플러그인
add_filter( 'msm_post_actions', array( 'MSM_Post_Post_Actions', 'post_actions' ) );
add_filter( 'msm_post_actions_settings', array( 'MSM_Post_Post_Actions', 'post_actions_settings' ) );
add_filter( 'msm-post-actions-email', array( 'MSM_Post_Post_Actions', 'send_email' ), 10, 4 );
add_filter( 'msm-post-actions-msm_post_category', array( 'MSM_Post_Post_Actions', 'set_post_category' ), 10, 4 );
add_filter( 'msm-post-actions-msm_post_update_content', array( 'MSM_Post_Post_Actions', 'update_content' ), 10, 4 );
add_action( 'msm-after-write-post-action', array( 'MSM_Post_Post_Actions', 'save_post_id' ), 10, 4 );

//업다운로드 플러그인
add_filter( 'msex_product_fields', array( 'MSM_Exporter', 'add_field_options' ) );
add_filter( 'msex_product_field_types', array( 'MSM_Exporter', 'add_product_field_types' ) );
add_filter( 'msex_product_field_data', array( 'MSM_Exporter', 'get_product_field_options' ), 10, 2 );