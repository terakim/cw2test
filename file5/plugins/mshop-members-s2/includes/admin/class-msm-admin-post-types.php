<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class MSM_Admin_Post_types {

	public static function init() {
		add_action( 'manage_mshop_members_form_posts_columns', array( __CLASS__, 'mshop_members_form_posts_columns' ), 10 );
		add_action( 'manage_mshop_members_form_posts_custom_column', array( __CLASS__, 'mshop_members_form_posts_custom_column' ), 10, 2 );

		add_action( 'manage_mshop_agreement_posts_columns', array( __CLASS__, 'mshop_agreement_posts_columns' ), 10 );
		add_action( 'manage_mshop_agreement_posts_custom_column', array( __CLASS__, 'mshop_agreement_posts_custom_column' ), 10, 2 );

		add_action( 'restrict_manage_posts', array( __CLASS__, 'restrict_manage_posts' ) );
		add_filter( 'parse_query', array( __CLASS__, 'parse_query' ) );

		add_filter( 'manage_users_columns', array( __CLASS__, 'manage_users_columns' ) );
		add_filter( 'manage_users_custom_column', array( __CLASS__, 'manage_users_custom_column' ), 10, 3 );
		add_filter( 'manage_users_sortable_columns', array( __CLASS__, 'manage_users_sortable_columns' ) );

		add_action( 'bulk_actions-edit-mshop_members_form', array( __CLASS__, 'add_bulk_actions' ) );
		add_action( 'handle_bulk_actions-edit-mshop_members_form', array( __CLASS__, 'do_bulk_action' ), 10, 3 );

		add_filter( 'restrict_manage_users', array( __CLASS__, 'restrict_manage_users' ), 1000 );
		add_filter( 'pre_get_users', array( __CLASS__, 'pre_get_users' ) );
		add_filter( 'msex_user_query_args', array( __CLASS__, 'get_query_args' ) );

		//역할관리 플러그인
		add_filter( 'post_row_actions', array( __CLASS__, 'post_row_actions' ), 10, 2 );
		add_filter( 'bulk_actions-edit-mshop_role_request', array( __CLASS__, 'bulk_actions' ) );
		add_action( 'handle_bulk_actions-edit-mshop_role_request', array( __CLASS__, 'handle_role_bulk_actions' ), 10, 3 );
		add_action( 'bulk_actions-edit-mshop_role_request', array( __CLASS__, 'add_role_bulk_actions' ) );

		add_action( 'manage_mshop_role_request_posts_columns', array( __CLASS__, 'mshop_role_request_manage_posts_columns' ), 10 );
		add_action( 'manage_mshop_role_request_posts_custom_column', array( __CLASS__, 'mshop_role_request_manage_posts_custom_column' ), 10, 2 );

		//포스트 플러그인
		add_filter( 'bulk_actions-edit-msm_post', array( __CLASS__, 'post_bulk_actions' ) );

		add_action( 'manage_msm_post_posts_columns', array( __CLASS__, 'msm_post_manage_posts_columns' ), 10 );
		add_action( 'manage_msm_post_posts_custom_column', array( __CLASS__, 'msm_post_manage_posts_custom_column' ), 10, 2 );

		add_filter( 'handle_bulk_actions-edit-msm_post', array( __CLASS__, 'post_handle_bulk_actions' ), 10, 3 );

		//포스트 플러그인
		add_filter( 'bulk_actions-users', array( __CLASS__, 'add_bulk_actions' ) );
		add_action( 'handle_bulk_actions-users', array( __CLASS__, 'do_bulk_action' ), 10, 3 );

		add_filter( 'msex_user_field_type', array( __CLASS__, 'add_msex_user_field_type' ) );
		add_filter( 'msex_user_field_msm_agreement', array( __CLASS__, 'get_msm_agreement_value' ), 10, 3 );
	}

	static function do_bulk_action( $redirect_to, $action, $ids ) {
		global $wp_query;
		$wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
		$action        = $wp_list_table->current_action();

		//액션에 따라 처리
		if ( $action == 'export_forms' && isset( $_REQUEST['post'] ) && count( $_REQUEST['post'] ) ) {
			MSM_Ajax::export_msm_forms( MSM_Manager::get_forms_by_id( $_REQUEST['post'] ) );
		} else if ( 'force_email_auth' == $action && ! empty( $ids ) ) {
			foreach ( $ids as $id ) {
				update_user_meta( $id, 'msm_email_certified', 'yes' );
			}

			wp_redirect( $redirect_to );
			exit;
		} else if ( 'reset_email_auth' == $action && ! empty( $ids ) ) {
			foreach ( $ids as $id ) {
				delete_user_meta( $id, 'msm_email_certified' );
			}

			wp_redirect( $redirect_to );
			exit;
		} else if ( 'force_phone_auth' == $action && ! empty( $ids ) ) {
			foreach ( $ids as $id ) {
				update_user_meta( $id, 'mshop_auth_method', 'mshop-sms' );
				update_user_meta( $id, 'mshop_auth_phone', '000-000-0000' );
			}

			wp_redirect( $redirect_to );
			exit;
		} else if ( 'reset_phone_auth' == $action && ! empty( $ids ) ) {
			foreach ( $ids as $id ) {
				delete_user_meta( $id, 'mshop_auth_method' );
				delete_user_meta( $id, 'mshop_auth_phone' );
			}

			wp_redirect( $redirect_to );
			exit;
		}
	}

	static function add_bulk_actions( $action ) {
		global $post_type, $pagenow;

		if ( 'mshop_members_form' == $post_type && 'edit.php' == $pagenow ) {
			$action['export_forms'] = __( '내보내기(Export)', 'mshop-members-s2' );
		} else if ( 'users.php' == $pagenow ) {
			$action['force_email_auth'] = __( '이메일 인증 설정', 'mshop-members-s2' );
			$action['reset_email_auth'] = __( '이메일 인증 초기화', 'mshop-members-s2' );
			$action['force_phone_auth'] = __( '휴대폰 인증 설정', 'mshop-members-s2' );
			$action['reset_phone_auth'] = __( '휴대폰 인증 초기화', 'mshop-members-s2' );
		}

		return $action;
	}
	static function restrict_manage_posts() {
		global $typenow;

		if ( 'mshop_agreement' == $typenow ) {
			self::mshop_agreement_filters();
		} elseif ( 'mshop_members_form' == $typenow ) {
			self::mshop_members_form_filters();
		} elseif ( 'msm_post' == $typenow ) {
			self::msm_post_filters();
		}
	}
	static function mshop_agreement_filters() {
		global $wp_query;

		$output = '';

		$terms = get_terms( 'mshop_agreement_cat', array( 'hide_empty' => 0 ) );

		$output .= '<select name="mshop_agreement_cat" id="mshop_agreement_cat">';
		$output .= '<option value="">' . __( '모든 분류 보기', 'mshop-members-s2' ) . '</option>';

		foreach ( $terms as $term ) {
			$output .= '<option value="' . sanitize_title( $term->slug ) . '" ';

			if ( isset( $_REQUEST['mshop_agreement_cat'] ) ) {
				$output .= selected( $term->slug, $_REQUEST['mshop_agreement_cat'], false );
			}

			$output .= '>';
			$output .= $term->name;
			$output .= '</option>';

		}

		$output .= '</select>';

		echo apply_filters( 'mshop_agreement_filters', $output );
	}
	static function mshop_members_form_filters() {
		global $wp_query;

		$output = '';

		$terms = get_terms( 'mshop_members_form_cat', array( 'hide_empty' => 0 ) );

		$output .= '<select name="mshop_members_form_cat" id="mshop_members_form_cat">';
		$output .= '<option value="">' . __( '모든 카테고리 보기', 'mshop-members-s2' ) . '</option>';

		foreach ( $terms as $term ) {

			$output .= '<option value="' . sanitize_title( $term->slug ) . '" ';

			if ( isset( $_REQUEST['mshop_members_form_cat'] ) ) {

				$output .= selected( $term->slug, $_REQUEST['mshop_members_form_cat'], false );

			}

			$output .= '>';
			$output .= $term->name;
			$output .= '</option>';

		}

		$output .= '</select>';

		echo apply_filters( 'mshop_members_form_filters', $output );
	}
	static function parse_query( $query ) {

		global $typenow;

		if ( 'mshop_agreement' == $typenow ) {

			self::mshop_agreement_filters_query( $query );

		} elseif ( 'mshop_members_form' == $typenow ) {

			self::mshop_members_form_filters_query( $query );

		} elseif ( 'msm_post' == $typenow ) {
			self::msm_post_filters_query( $query );
		}

	}
	static function mshop_agreement_filters_query( $query ) {

		if ( isset( $_REQUEST['mshop_agreement_cat'] ) && ! empty( $_REQUEST['mshop_agreement_cat'] ) ) {

			$tax_query = array(
				array(
					'taxonomy' => 'mshop_agreement_cat',
					'field'    => 'slug',
					'terms'    => $_REQUEST['mshop_agreement_cat']
				)
			);

			$query->set( 'tax_query', $tax_query );

		}

	}
	static function mshop_members_form_filters_query( $query ) {

		if ( isset( $_REQUEST['mshop_members_form_cat'] ) && ! empty( $_REQUEST['mshop_members_form_cat'] ) ) {

			$tax_query = array(
				array(
					'taxonomy' => 'mshop_members_form_cat',
					'field'    => 'slug',
					'terms'    => $_REQUEST['mshop_members_form_cat']
				)
			);

			$query->set( 'tax_query', $tax_query );

		}

	}

	static function mshop_members_form_posts_custom_column( $column, $post_id ) {
		switch ( $column ) {
			case 'shortcodes':
				$post = get_post( $post_id );
				if ( ! empty( $post->post_name ) ) {
					printf( "<a href='%s/?msm_preview=%s' target='_blank'>[mshop_form_designer slug='%s' default=true]</a>", site_url(), $post->post_name, $post->post_name );
				}
				break;
			case 'slug' :
				$post = get_post( $post_id );
				echo $post->post_name;
				break;

		}
	}

	static function mshop_members_form_posts_columns( $posts_columns ) {
		return array_merge(
			array_slice( $posts_columns, 0, count( $posts_columns ) - 1 ),
			array(
				'slug'       => __( '슬러그', 'mshop-members-s2' ),
				'shortcodes' => __( '숏코드', 'mshop-members-s2' )
			),
			array_slice( $posts_columns, count( $posts_columns ) - 1, 1 ) );
	}


	static function mshop_agreement_posts_columns( $posts_columns ) {
		return array_merge(
			array_slice( $posts_columns, 0, count( $posts_columns ) - 1 ),
			array(
				'mandatory' => __( '옵션', 'mshop-members-s2' )
			),
			array_slice( $posts_columns, count( $posts_columns ) - 1, 1 ) );
	}

	static function mshop_agreement_posts_custom_column( $column, $post_id ) {
		switch ( $column ) {
			case 'mandatory':
				$mandatory = get_post_meta( $post_id, '_mandatory', true );
				echo 'yes' == $mandatory ? '필수' : '선택';
				break;
		}
	}

	public static function manage_users_custom_column( $value, $column_name, $userid ) {
		if ( 'user_registered' == $column_name ) {
			$user_data = get_userdata( $userid );

			return get_date_from_gmt( $user_data->user_registered );
		} else if ( 'msm_user_status' == $column_name ) {
			if ( get_user_meta( $userid, 'is_unsubscribed', true ) == "1" ) {
				return '<span>' . __( '탈퇴', 'mshop-members-s2' ) . '</span>';
			} else if ( get_user_meta( $userid, 'is_unsubscribed', true ) == "2" ) {
				return '<span>' . __( '휴면', 'mshop-members-s2' ) . '</span>';
			} else {
				return '<span>' . __( '정상', 'mshop-members-s2' ) . '</span>';
			}
		} else if ( 'msm_email_authenticate' == $column_name ) {
			if ( 'yes' == get_user_meta( $userid, 'msm_email_certified', true ) ) {
				return '<span>' . __( '인증됨', 'mshop-members-s2' ) . '</span>';
			}
		} else if ( 'msm_phone_certification' == $column_name ) {
			if ( 'mshop-sms' == get_user_meta( $userid, 'mshop_auth_method', true ) ) {
				return '<span>' . __( '인증됨', 'mshop-members-s2' ) . '</span>';
			}
		} else if ( 'msm_last_login' == $column_name ) {
			$last_login = get_user_meta( $userid, 'last_login_time', true );

			if ( ! empty( $last_login ) ) {
				return date( 'Y-m-d', strtotime( $last_login ) );
			} else {
				return '';
			}
		} else if ( 'mssms_agreement_label' == $column_name ) {
			return get_user_meta( $userid, 'mssms_agreement_label', true );
		} else if ( 'email_agreement_label' == $column_name ) {
			return get_user_meta( $userid, 'email_agreement_label', true );
		} else if ( 'msm_tac' == $column_name ) {
			$outputs = array();

			$user_tac = get_user_meta( $userid, 'msm_tac', true );

			if ( is_array( $user_tac ) ) {
				foreach ( $user_tac as $label => $tac ) {
					if ( 'yes' == $tac['agree'] ) {
						$outputs[] = sprintf( "%s : <span style='color: blue'>%s</span>", $label, __( '동의', 'mshop-members-s2' ) );
					} else {
						$outputs[] = sprintf( "%s : <span style='color: red'>%s</span>", $label, __( '미동의', 'mshop-members-s2' ) );
					}
				}
			}

			return implode( '<br>', $outputs );
		} else if ( 'msm_user_fields' == $column_name ) {
			$user_fields = get_option( 'msm_user_fields', array() );
			$value       = array();

			foreach ( $user_fields as $field ) {
				$user_data = 'file' == $field['type'] ? get_user_meta( $userid, $field['key'] . '_label', true ) : get_user_meta( $userid, $field['key'], true );

				if ( 'yes' == $field['enabled'] && ! empty( $field['title'] ) && ! empty( $user_data ) ) {
					$value [] = sprintf( '<span class="title">%s</span> : <span class="value">%s</span>', $field['title'], $user_data );
				}
			}

			return is_array( $value ) ? implode( '<br>', $value ) : '';
		} else {
			$msm_fields = apply_filters( 'msm_users_custom_column', array( 'msm_register_fields' ) );

			if ( in_array( $column_name, $msm_fields ) ) {
				$value = array();
				$metas = MSM_Meta::get_user_meta( $userid, '_' . $column_name );
				foreach ( $metas as $meta ) {
					if ( ! empty( $meta['title'] ) ) {
						$value [] = sprintf( '<span class="title">%s</span> : <span class="value">%s</span>', $meta['title'], ! empty( $meta['label'] ) ? $meta['label'] : $meta['value'] );
					}
				}

				return implode( '<br>', $value );
			}
		}

		return $value;
	}

	public static function manage_users_columns( $users_columns ) {
		$columns = apply_filters( 'msm_users_columns', array(
			'user_registered'       => __( '가입일', 'mshop-members-s2' ),
			'msm_register_fields'   => __( '멤버스 필드', 'mshop-members-s2' ),
			'msm_user_fields'       => __( '사용자 필드', 'mshop-members-s2' ),
			'msm_user_status'       => __( '회원 상태', 'mshop-members-s2' ),
			'msm_last_login'        => __( '마지막 로그인', 'mshop-members-s2' ),
			'mssms_agreement_label' => __( '문자수신동의', 'mshop-members-s2' ),
			'email_agreement_label' => __( '이메일수신동의', 'mshop-members-s2' ),
			'msm_profile_fields'    => __( '프로필', 'mshop-members-s2' ),
			'role_processing'       => __( '등급변경 처리일', 'mshop-members-s2' ),
			'msm_tac'               => __( '이용약관 동의', 'mshop-members-s2' ),
		) );

		if ( 'yes' == get_option( 'msm_required' ) ) {
			$columns['msm_email_authenticate'] = __( '이메일인증', 'mshop-members-s2' );
		}

		if ( 'yes' == get_option( 'mssms_use_phone_certification' ) ) {
			$columns['msm_phone_certification'] = __( '휴대폰인증', 'mshop-members-s2' );
		}

		$users_columns = array_merge( $users_columns, $columns );

		return $users_columns;
	}
	static function manage_users_sortable_columns( $users_columns ) {
		return array_merge( $users_columns, array(
			'msm_last_login'  => 'msm_last_login',
			'user_registered' => 'user_registered',
		) );
	}

	public static function restrict_manage_users( $which ) {
		if ( 'top' == $which ) {
			self::date_filters();

			$statuses = array(
				'all'         => '모두',
				'active'      => '정상',
				'unsubscribe' => '탈퇴',
				'sleep'       => '휴면',
			);

			$selected = msm_get( $_GET, 'msm_status', 'all' );

			?>
            <input type="text" name="billing_first_name" value="<?php echo msm_get( $_REQUEST, 'billing_first_name' ); ?>" placeholder="고객명">
            <input type="text" name="billing_phone" value="<?php echo msm_get( $_REQUEST, 'billing_phone' ); ?>" placeholder="전화번호">
            <span style="font-size: 0.9em;">
                <span>회원상태 : </span>
                <select name="msm_status" class="mshop_members_status" style="float: none;">
                    <?php foreach ( $statuses as $key => $label ) : ?>
                        <option value='<?php echo $key; ?>' <?php echo $selected == $key ? 'selected' : ''; ?>><?php echo $label; ?></option>
                    <?php endforeach; ?>
				</select>
                <input type="submit" class="button action" value="필터"/>
            </span>
			<?php
		}
	}

	public static function date_filters() {
		global $typenow;

		$date_from = empty( $_REQUEST['msm_date_from'] ) ? '' : $_REQUEST['msm_date_from'];
		$date_to   = empty( $_REQUEST['msm_date_to'] ) ? '' : $_REQUEST['msm_date_to'];

		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_style( 'jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );

		?>
        <script type="text/javascript">
            jQuery(document).ready(function() {
                jQuery(function($) {
                    $.datepicker.regional['ko'] = {
                        closeText: '닫기',
                        prevText: '이전달',
                        nextText: '다음달',
                        currentText: '오늘',
                        monthNames: ['1월', '2월', '3월', '4월', '5월', '6월',
                            '7월', '8월', '9월', '10월', '11월', '12월'],
                        monthNamesShort: ['1월', '2월', '3월', '4월', '5월', '6월',
                            '7월', '8월', '9월', '10월', '11월', '12월'],
                        dayNames: ['일요일', '월요일', '화요일', '수요일', '목요일', '금요일', '토요일'],
                        dayNamesShort: ['일', '월', '화', '수', '목', '금', '토'],
                        dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
                        weekHeader: 'Wk',
                        dateFormat: 'yy-mm-dd',
                        firstDay: 0,
                        isRTL: false,
                        showMonthAfterYear: true,
                        yearSuffix: '년'
                    };
                    $.datepicker.setDefaults($.datepicker.regional['ko']);
                });

                jQuery(function() {
                    jQuery('input.mshop_datepicker').datepicker();
                });
            });
        </script>
		<?php
		$search_types = array( 'register' => '가입일', 'last_login' => '마지막로그인' );

		$selected_date_type = msm_get( $_REQUEST, 'date_type' );

		?>
        <select name="date_type" style="float: none;">
			<?php foreach ( $search_types as $key => $value ) : ?>
                <option value="<?php echo $key; ?>" <?php echo $key == $selected_date_type ? 'selected' : ''; ?>><?php echo $value; ?></option>
			<?php endforeach; ?>
        </select>
        <input type="text" class="mshop_datepicker" name="msm_date_from" value="<?php echo $date_from; ?>" placeholder="From date">
        <input type="text" class="mshop_datepicker" name="msm_date_to" value="<?php echo $date_to; ?>" placeholder="To date">
		<?php
	}

	public static function get_query_args( $args = array() ) {
		$status = msm_get( $_GET, 'msm_status' );

		if ( $status != 'all' ) {
			switch ( $status ) {
				case 'unsubscribe':
					$type = '1';
					break;
				case 'sleep':
					$type = '2';
					break;
				default :
					$type = '0';
			}

			if ( $type == '0' ) {
				$meta_query         = array(
					'relation' => 'OR',
					array(
						'compare' => 'NOT EXISTS',
						'key'     => 'is_unsubscribed',
					),
					array(
						'key'   => 'is_unsubscribed',
						'value' => $type
					)
				);
				$args['meta_query'] = $meta_query;
			} else {
				$meta_query = array(
					array(
						'key'   => 'is_unsubscribed',
						'value' => $type
					)
				);

				$args['meta_query'] = $meta_query;
			}
		}

		$date_from = msm_get( $_GET, 'msm_date_from' );
		$date_to   = msm_get( $_GET, 'msm_date_to' );

		if ( ! empty( $date_from ) && ! empty( $date_to ) ) {
			if ( 'last_login' == msm_get( $_GET, 'date_type' ) ) {
				$meta_query = msm_get( $args, 'meta_query', array() );

				$meta_query = array_merge( $meta_query, array(
					array(
						'key'     => '_mshop_last_date',
						'value'   => array( $date_from . ' 00:00:00', $date_to . ' 23:59:59' ),
						'compare' => 'BETWEEN',
						'type'    => 'DATE'
					)
				) );

				$args['meta_query'] = $meta_query;
			} else {
				$args['date_query'] = array(
					array(
						'after'     => $date_from,
						'before'    => $date_to,
						'inclusive' => true,
					)
				);
			}
		}

		if ( ! empty( $_GET['billing_first_name'] ) ) {
			$meta_query = msm_get( $args, 'meta_query', array() );

			$meta_query = array_merge( $meta_query, array(
				array(
					'key'   => 'billing_first_name',
					'value' => $_GET['billing_first_name']
				)
			) );

			$args['meta_query'] = $meta_query;
		}

		if ( ! empty( $_GET['billing_phone'] ) ) {
			$meta_query = msm_get( $args, 'meta_query', array() );

			$meta_query = array_merge( $meta_query, array(
				array(
					'key'   => 'billing_phone',
					'value' => $_GET['billing_phone']
				)
			) );

			$args['meta_query'] = $meta_query;
		}


		return $args;
	}
	public static function pre_get_users( $query ) {
		global $pagenow;

		if ( is_admin() && 'users.php' == $pagenow ) {
			if ( isset( $_GET['msm_status'] ) ) {
				$args = self::get_query_args();

				if ( ! empty( $args['meta_query'] ) ) {
					$meta_query = $query->get( 'meta_query' );

					if ( is_null( $meta_query ) ) {
						$meta_query = array();
					}

					$meta_query = array_merge( $meta_query, $args['meta_query'] );

					$query->set( 'meta_query', $meta_query );
				}

				if ( ! empty( $args['date_query'] ) ) {
					$query->set( 'date_query', $args['date_query'] );
				}
			}

			if ( 'msm_last_login' == msm_get( $_GET, 'orderby' ) ) {
				$query->set( 'meta_key', 'last_login_time' );
				$query->set( 'orderby', 'meta_value' );
			}
		}
	}

	//역할관리 플러그인
	public static function post_row_actions( $actions, $post ) {
		if ( 'mshop_role_request' == $post->post_type ) {
			return array();
		} elseif ( 'msm_post' == $post->post_type ) {
			return array();
		}

		return $actions;
	}

	public static function bulk_actions( $action ) {
		unset( $action['edit'] );

		return $action;
	}

	public static function handle_role_bulk_actions( $redirect_to, $action, $ids ) {
		$wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
		$action        = $wp_list_table->current_action();

		if ( strpos( $action, 'mark_' ) === false ) {
			return;
		}

		$action     = substr( $action, 5 );
		$new_status = 'mshop-' . $action;

		$statuses = MSM_Role_Application::get_statuses();
		if ( ! array_key_exists( $new_status, $statuses ) ) {
			return;
		}

		$post_ids = array_map( 'absint', (array) $_REQUEST['post'] );

		foreach ( $post_ids as $post_id ) {
			$role_application = new MSM_Role_Application( $post_id );
			$role_application->$action();
		}
	}

	public static function add_role_bulk_actions( $action ) {
		global $post_type;

		if ( 'mshop_role_request' == $post_type && function_exists( 'MSM' ) ) {
			wp_enqueue_style( 'msm-font-awesome', MSM()->plugin_url() . '/assets/font-awesome/css/font-awesome.min.css', array(), MSM()->version );

			$action['mark_approved'] = __( '승인', 'mshop-members-s2' );
			$action['mark_rejected'] = __( '반려', 'mshop-members-s2' );

		}

		return $action;
	}

	public static function mshop_role_request_manage_posts_columns( $posts_columns ) {
		if ( class_exists( 'WC' ) ) {
			wp_enqueue_style( 'wc-admin-style', WC()->plugin_url() . '/assets/css/admin.css' );
		}
		array_pop( $posts_columns );

		$columns = array_merge(
			array_slice( $posts_columns, 0, count( $posts_columns ) - 1 ),
			array(
				'msm_status'             => '',
				'msm_user'               => __( '사용자', 'mshop-members-s2' ),
				'msm_request_role'       => __( '요청역할', 'mshop-members-s2' ),
				'msm_processing_time'    => __( '처리일', 'mshop-members-s2' ),
				'msm_application_info'   => __( '신청정보', 'mshop-members-s2' ),
				'msm_user_certification' => __( '본인인증', 'mshop-members-s2' ),
				'msm_action'             => __( '액션', 'mshop-members-s2' ),
			),
			array_slice( $posts_columns, count( $posts_columns ) - 1, 1 )
		);

		unset( $columns['title'] );

		return $columns;
	}

	public static function mshop_role_request_manage_posts_custom_column( $column, $post_id ) {
		switch ( $column ) {
			case 'msm_status':
				$status = get_post_status( $post_id );
				if ( 'mshop-apply' === $status ) {
					echo '<i style="color: green" class="fa fa-minus-circle"></i>';
				} else if ( 'mshop-approved' === $status ) {
					echo '<i style="color: blue" class="fa fa-check-circle"></i>';
				} else if ( 'mshop-rejected' === $status ) {
					echo '<i style="color: red" class="fa fa-times-circle"></i>';
				}
				break;
			case 'msm_user':
				$user         = get_user_by( 'id', get_post_meta( $post_id, 'user_id', true ) );
				$roles        = apply_filters( 'msm_get_roles', array() );
				$current_role = get_post_meta( $post_id, 'current_role', true );
				$user_role    = ! empty( $roles[ $current_role ] ) ? $roles[ $current_role ] : '';
				if ( $user instanceof WP_User ) {
					printf( '%s<br>( <a href="%s">#%d</a>, %s )', $user->nickname, get_edit_user_link( $user->ID ), $user->ID, $user_role );
				}

				break;
			case 'msm_request_role':
				$roles        = apply_filters( 'msm_get_roles', array() );
				$request_role = get_post_meta( $post_id, 'request_role', true );
				echo ! empty( $roles[ $request_role ] ) ? $roles[ $request_role ] : '';
				echo '<br><span class="date">(' . get_post_meta( $post_id, 'request_time', true ) . ')</span>';
				break;
			case 'msm_processing_time':
				echo get_post_meta( $post_id, 'processing_time', true );
				break;
			case 'msm_application_info':
				$metas = MSM_Meta::get_post_meta( $post_id, '_msm_form' );

				if ( ! empty( $metas ) ) {

					echo '<table class="application_info">';
					foreach ( $metas as $meta ) {
						if ( ! empty( $meta['title'] ) ) {
							echo '<tr>';
							echo '<td class="meta_key">' . $meta['title'] . '</td>';
							echo '<td class="meta_value">' . str_replace( "\n", "<br>", ! empty( $meta['label'] ) ? $meta['label'] : $meta['value'] ) . '</td>';
							echo '</tr>';
						}
					}
					echo '</table>';
				}

				do_action( 'msm_role_request_posts_column_msm_application_info', $post_id );
				break;
			case 'msm_user_certification' :
				$values = array();

				if ( get_post_meta( $post_id, 'mshop_auth_method', true ) == "checkplus" ) {
					$values[] = __( '인증방식 : 휴대폰', 'mshop-members-s2' );
				} else if ( get_post_meta( $post_id, 'mshop_auth_method', true ) == "ipin" ) {
					$values[] = __( '인증방식 : 아이핀', 'mshop-members-s2' );
				} else {
					return;
				}

				$values[] = '실명 : ' . get_post_meta( $post_id, 'mshop_auth_name', true );
				$values[] = '생년월일 : ' . get_post_meta( $post_id, 'mshop_auth_birthdate', true );
				$values[] = '성별 : ' . ( '1' == get_post_meta( $post_id, 'mshop_auth_gender', true ) ? __( '남성', 'mshop-members-s2' ) : __( '여성', 'mshop-members-s2' ) );
				$values[] = '국적 : ' . ( '1' == get_post_meta( $post_id, 'mshop_auth_nationalinfo', true ) ? __( '외국인', 'mshop-members-s2' ) : __( '내국인', 'mshop-members-s2' ) );

				echo implode( '<br>', $values );
				break;
			case 'msm_action':
				$status      = get_post_status( $post_id );
				$approve_url = wp_nonce_url( admin_url( 'admin-ajax.php?action=' . msm_ajax_command( 'approved&request_id=' . $post_id ) ), 'msm-role-application' );
				$reject_url  = wp_nonce_url( admin_url( 'admin-ajax.php?action=' . msm_ajax_command( 'rejected&request_id=' . $post_id ) ), 'msm-role-application' );
				if ( 'mshop-apply' === $status ) {
					printf( '<a class="button tips approved" href="%s">승인</a>', $approve_url );
					printf( '<a class="button tips rejected" href="%s">반려</a>', $reject_url );
				} else if ( 'mshop-approved' === $status ) {
					printf( '<a class="button tips rejected" href="%s">반려</a>', $reject_url );
				} else if ( 'mshop-rejected' === $status ) {
					printf( '<a class="button tips approved" href="%s">승인</a>', $approve_url );
				}
				break;
		}
	}

	//포스트 플러그인
	public static function post_bulk_actions( $action ) {
		unset( $action['edit'] );

		$action = apply_filters( 'msm_post_bulk_action', $action );

		return $action;
	}

	public function post_handle_bulk_actions( $redirect_to, $action, $ids ) {
		$redirect_to = apply_filters( 'msm_post_handle_bulk_actions', $redirect_to, $action, $ids );

		return esc_url_raw( $redirect_to );
	}
	static function msm_post_filters() {
		global $wp_query;

		$output = '';

		$terms = get_categories( array( 'taxonomy' => 'msm_post_cat', 'hide_empty' => false ) );

		$output .= '<select name="msm_post_cat" id="msm_post_cat">';
		$output .= '<option value="">' . __( '모든 카테고리 보기', 'mshop-members-s2' ) . '</option>';

		foreach ( $terms as $term ) {

			$output .= '<option value="' . sanitize_title( $term->slug ) . '" ';

			if ( isset( $_REQUEST['msm_post_cat'] ) ) {
				$output .= selected( $term->slug, $_REQUEST['msm_post_cat'], false );
			}

			$output .= '>';
			$output .= $term->name;
			$output .= '</option>';

		}

		$output .= '</select>';

		echo apply_filters( 'msm_post_filter', $output );
	}
	static function msm_post_filters_query( $query ) {

		if ( isset( $_REQUEST['msm_post_cat'] ) && ! empty( $_REQUEST['msm_post_cat'] ) ) {

			$tax_query = array(
				array(
					'taxonomy' => 'msm_post_cat',
					'field'    => 'slug',
					'terms'    => $_REQUEST['msm_post_cat']
				)
			);

			$query->set( 'tax_query', $tax_query );

		}
	}

	public static function msm_post_manage_posts_custom_column( $column, $post_id ) {
		$post = get_post( $post_id );

		switch ( $column ) {
			case 'status' :
				$post_status = get_post_status_object( $post->post_status );
				if ( $post_status ) {
					echo $post_status->label;
				} else {
					echo $post->post_status;
				}
				break;
			case 'ID' :
				$user      = get_user_by( 'id', $post->post_author );
				$user_name = implode( ' ', preg_split( '//u', $user->display_name, null, PREG_SPLIT_NO_EMPTY ) );

				if ( $user instanceof WP_User ) {
					printf( '<a href="%s">#%s</a> <a href="%s">%s</a>', get_edit_post_link( $post->ID ), $post->ID, get_edit_user_link( $user->ID ), $user->display_name );
					printf( '<br>%s', $user->user_email );
				} else {
					printf( '<a href="%s">#%s</a> GUEST', get_edit_post_link( $post->ID ), $post->ID );
				}
				break;
			case 'post_title' :
				echo $post->post_title;
				break;
			case 'post_date' :
				echo date( 'Y-m-d H:i:s', strtotime( $post->post_date ) );
				break;
			case 'post_content':
				echo $post->post_content;
				break;
		}
	}

	public static function msm_post_manage_posts_columns( $posts_columns ) {
		if ( class_exists( 'WC' ) ) {
			wp_enqueue_style( 'wc-admin-style', WC()->plugin_url() . '/assets/css/admin.css' );
		}

		array_pop( $posts_columns );

		$columns = array_merge(
			array_slice( $posts_columns, 0, count( $posts_columns ) - 1 ),
			array(
				'status'       => __( '상태', 'mshop-members-s2' ),
				'ID'           => __( '포스트', 'mshop-members-s2' ),
				'post_title'   => __( '제목', 'mshop-members-s2' ),
				'post_content' => __( '포스트 데이터', 'mshop-members-s2' ),
			),
			array_slice( $posts_columns, count( $posts_columns ) - 1, 1 ),
			array(
				'post_date' => __( '날짜', 'mshop-members-s2' ),
			)
		);

		unset( $columns['title'] );

		return $columns;
	}
	public static function add_msex_user_field_type( $fields ) {
		$fields['msm_agreement'] = __( '이용약관동의', 'mshop-members-s2' );

		return $fields;
	}
	public static function get_msm_agreement_value( $field_value, $field, $user ) {
		$user_tac = get_user_meta( $user->ID, 'msm_tac', true );

		if ( is_array( $user_tac ) && ! empty( msm_get( $user_tac, $field['field_label'], array() ) ) ) {
			$agreement = msm_get( $user_tac, $field['field_label'], array() );

			if ( is_array( $agreement ) ) {
				$field_value = 'yes' == msm_get( $agreement, 'agree' ) ? __( '동의', 'mshop-members-s2' ) : __( '미동의', 'mshop-members-s2' );
			}
		}

		return $field_value;
	}
}
