<?php

class MSM_Install_Data {

	protected static $_pages;
	protected static $_forms;
	protected static $_agreements;

	public static function init() {
		add_filter( 'msm_skip_on_checkout', '__return_true' );

		self::$_pages = array(
			array(
				'path'      => 'login',
				'title'     => __( '로그인', 'mshop-members-s2' ),
				'content'   => "<div class='memberswrap'>[mshop_form_designer slug='login' default=true]</div>",
				'post_type' => 'page'
			),
			array(
				'path'      => 'register',
				'title'     => __( '회원가입', 'mshop-members-s2' ),
				'content'   => "<div class='memberswrap'>[mshop_form_designer slug='register' default=true]</div>",
				'post_type' => 'page'
			),
			array(
				'path'      => 'unsubscribe',
				'title'     => __( '회원탈퇴', 'mshop-members-s2' ),
				'content'   => "<div class='memberswrap'>[mshop_form_designer slug='unsubscribe' default=true]</div>",
				'post_type' => 'page'
			),
			array(
				'path'      => 'lostpassword',
				'title'     => __( '비밀번호찾기', 'mshop-members-s2' ),
				'content'   => "<div class='memberswrap'>[mshop_form_designer slug='lostpassword' default=true]</div>",
				'post_type' => 'page'
			),
			array(
				'path'      => 'agreement',
				'title'     => __( '이용약관', 'mshop-members-s2' ),
				'content'   => "<div class='memberswrap'>[mshop_form_designer slug='agreement' default=true]</div>",
				'post_type' => 'page'
			),
			array(
				'path'      => 'personal_policy',
				'title'     => __( '개인정보취급방침', 'mshop-members-s2' ),
				'content'   => "<div class='memberswrap'>[mshop_form_designer slug='personal_policy' default=true]</div>",
				'post_type' => 'page'
			),
			array(
				'path'      => 'email-authentication',
				'title'     => __( '이메일인증', 'mshop-members-s2' ),
				'content'   => "[msm_email_authentication]",
				'post_type' => 'page'
			)
		);

		self::$_agreements = array(
			array(
				'path'      => '',
				'title'     => __( '쇼핑몰 이용약관', 'mshop-members-s2' ),
				'file'      => MSM()->plugin_path() . '/includes/install/data/agreements/shop_use_policy.txt',
				'post_type' => 'mshop_agreement',
				'terms'     => array( 'customer', 'agreement' ),
				'taxonomy'  => 'mshop_agreement_cat',
				'meta'      => array(
					'_mandatory' => 'yes'
				)
			),
			array(
				'path'      => '',
				'title'     => __( '쇼핑몰 이용약관 (비회원)', 'mshop-members-s2' ),
				'file'      => MSM()->plugin_path() . '/includes/install/data/agreements/shop_use_policy_for_guest.txt',
				'post_type' => 'mshop_agreement',
				'terms'     => array( 'guest' ),
				'taxonomy'  => 'mshop_agreement_cat',
				'meta'      => array(
					'_mandatory' => 'yes'
				)
			),
			array(
				'path'      => '',
				'title'     => __( '개인정보 수집항목', 'mshop-members-s2' ),
				'file'      => MSM()->plugin_path() . '/includes/install/data/agreements/collect_items.txt',
				'post_type' => 'mshop_agreement',
				'terms'     => array( 'personal_policy', 'customer', 'guest' ),
				'taxonomy'  => 'mshop_agreement_cat',
				'meta'      => array(
					'_mandatory' => 'yes'
				)
			),
			array(
				'path'      => '',
				'title'     => __( '개인정보 수집목적', 'mshop-members-s2' ),
				'file'      => MSM()->plugin_path() . '/includes/install/data/agreements/purpose_collect_data.txt',
				'post_type' => 'mshop_agreement',
				'terms'     => array( 'personal_policy', 'customer', 'guest' ),
				'taxonomy'  => 'mshop_agreement_cat',
				'meta'      => array(
					'_mandatory' => 'yes'
				)
			),
			array(
				'path'      => '',
				'title'     => __( '개인정보 보유 및 이용기간', 'mshop-members-s2' ),
				'file'      => MSM()->plugin_path() . '/includes/install/data/agreements/hold_and_use.txt',
				'post_type' => 'mshop_agreement',
				'terms'     => array( 'personal_policy', 'customer', 'guest' ),
				'taxonomy'  => 'mshop_agreement_cat',
				'meta'      => array(
					'_mandatory' => 'yes'
				)
			),
			array(
				'path'      => '',
				'title'     => __( '개인정보 제3자 제공동의', 'mshop-members-s2' ),
				'file'      => MSM()->plugin_path() . '/includes/install/data/agreements/providing_third_party_use.txt',
				'post_type' => 'mshop_agreement',
				'terms'     => array( 'personal_policy', 'customer', 'guest' ),
				'taxonomy'  => 'mshop_agreement_cat',
				'meta'      => array(
					'_mandatory' => 'yes'
				)
			),
			array(
				'path'      => '',
				'title'     => __( '회원 탈퇴 안내', 'mshop-members-s2' ),
				'file'      => MSM()->plugin_path() . '/includes/install/data/agreements/unsubscribe.txt',
				'post_type' => 'mshop_agreement',
				'terms'     => array( 'withdrawal' ),
				'taxonomy'  => 'mshop_agreement_cat',
				'meta'      => array(
					'_mandatory' => 'yes'
				)
			)
		);

		self::$_forms = array(
			'register_with_certification',
			'certification_for_register',
			'userinfo_for_register',
			'tac_for_register',
			'tac_for_guest',
			'lostpassword',
			'register',
			'login',
		);
		$default_members_form_cats = array(
			'일반회원'     => array(
				'description' => '회원가입 약관입니다.',
				'slug'        => 'customer'
			),
			'비회원'      => array(
				'description' => '비회원 약관입니다.',
				'slug'        => 'guest'
			),
			'회원탈퇴'     => array(
				'description' => '회원탈퇴 약관입니다.',
				'slug'        => 'withdrawal'
			),
			'개인정보취급방침' => array(
				'description' => '개인정보 취급방침 페이지에서 보여질 약관입니다.',
				'slug'        => 'personal_policy'
			),
			'이용약관'     => array(
				'description' => '이용약관 페이지에서 보여질 약관입니다.',
				'slug'        => 'agreement'
			)
		);

		foreach ( $default_members_form_cats as $name => $args ) {
			wp_insert_term( $name, 'mshop_agreement_cat', $args );
		}
		$default_members_form_cats = array(
			'로그인'    => array(
				'description' => '로그인 템플릿입니다.',
				'slug'        => 'login'
			),
			'회원가입'   => array(
				'description' => '회원가입 템플릿입니다.',
				'slug'        => 'register'
			),
			'이용약관'   => array(
				'description' => '이용약관 템플릿입니다.',
				'slug'        => 'terms_and_conditions'
			),
			'비밀번호찾기' => array(
				'description' => '비밀번호찾기 템플릿입니다.',
				'slug'        => 'lost_passwords'
			),
			'본인인증'   => array(
				'description' => '본인인증 템플릿입니다.',
				'slug'        => 'certification'
			),
			'권한요청'   => array(
				'description' => '권한요청 템플릿입니다.',
				'slug'        => 'role_application'
			),
			'포스트 등록' => array(
				'description' => '포스트 등록 템플릿입니다.',
				'slug'        => 'write_post'
			),
			'커스텀'    => array(
				'description' => '커스텀 템플릿입니다.',
				'slug'        => 'custom'
			),
		);

		foreach ( $default_members_form_cats as $name => $args ) {
			wp_insert_term( $name, 'mshop_members_form_cat', $args );
		}
	}

	public static function install_pages() {
		self::init();
		foreach ( self::$_pages as $page ) {
			$posts = get_page_by_path( $page['path'] );

			if ( ! is_array( $posts ) ) {
				$posts = array( $posts );
			}

			foreach ( $posts as $post ) {
				wp_delete_post( $post->ID, true );
			}
		}

		self::process_install_pages( self::$_pages );
	}

	public static function install_agreements() {
		self::init();

		self::process_install_pages( self::$_agreements );
	}

	public static function install_forms() {
		self::init();

		foreach ( self::$_forms as $page ) {
			$posts = get_page_by_path( $page, OBJECT, 'mshop_members_form' );

			if ( ! is_array( $posts ) ) {
				$posts = array( $posts );
			}

			foreach ( $posts as $post ) {
				wp_delete_post( $post->ID, true );
			}
		}

		$forms = json_decode( file_get_contents( MSM()->plugin_path() . '/includes/install/data/msm_forms.json' ), true );

		self::import_msm_forms( $forms );
	}

	public static function process_install_pages( $pages ) {
		foreach ( $pages as $page ) {
			self::delete_page( $page );

			$content = '';

			if ( ! empty( $page['content'] ) ) {
				$content = $page['content'];
			} else if ( ! empty( $page['file'] ) ) {
				$content = file_get_contents( $page['file'] );
			}

			$args = array(
				'post_title'   => $page['title'],
				'post_name'    => $page['path'],
				'post_content' => $content,
				'post_status'  => 'publish',
				'post_type'    => $page['post_type'],
			);

			$post_id = wp_insert_post( $args );

			if ( isset( $page['taxonomy'] ) ) {
				wp_set_object_terms( $post_id, $page['terms'], $page['taxonomy'] );
			}

			if ( ! empty( $page['meta'] ) ) {
				foreach ( $page['meta'] as $meta_key => $meta_value ) {
					update_post_meta( $post_id, $meta_key, $meta_value );
				}
			}
		}
	}

	public static function delete_page( $page ) {
		$posts = get_page_by_path( $page['path'] );

		if ( ! is_array( $posts ) ) {
			$posts = array( $posts );
		}
		foreach ( $posts as $post ) {
			wp_delete_post( $post->ID, true );
		}
	}

	public static function insert_page( $page, $form_id ) {
		self::delete_page( $page );
		$args = array(
			'post_title'   => $page['title'],
			'post_name'    => $page['path'],
			'post_content' => sprintf( $page['content'], $form_id ),
			'post_status'  => 'publish',
			'post_type'    => 'page',
		);

		wp_insert_post( $args );
	}

	public static function import_msm_forms( $forms ) {
		add_filter( 'wp_targeted_link_rel', '__return_empty_string' );

		foreach ( $forms as $form ) {
			$args = array(
				'post_title'   => $form['title'],
				'post_content' => addslashes( $form['content'] ),
				'post_name'    => $form['name'],
				'post_status'  => 'publish',
				'post_type'    => 'mshop_members_form',
			);

			$form_id = wp_insert_post( $args );

			$cats = array_filter( explode( ',', $form['category'] ) );
			foreach ( $cats as $cat ) {
				wp_set_object_terms( $form_id, $cat, 'mshop_members_form_cat' );
			}

			foreach ( $form['metas'] as $key => $value ) {
				update_post_meta( $form_id, $key, $value );
			}

			if ( ! isset(  $form['metas'] ['msm_custom_style'] ) ) {
				$style = array(
					'_custom_css'   => msm_get(  $form['metas'] , '_custom_css' ),
					'_custom_style' => msm_get(  $form['metas'] , '_custom_style' ),
				);

				update_post_meta( $form_id, 'msm_custom_style', addslashes( json_encode( $style ) ) );
			}

		}

		remove_filter( 'wp_targeted_link_rel', '__return_empty_string' );
	}

	public static function import_forms() {
		$forms = json_decode( file_get_contents( MSM()->plugin_path() . '/includes/install/data/msm_forms.json' ), true );

		self::import_msm_forms( $forms );
	}

}
