<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'MSM_Access_Control' ) ) :

	class MSM_Access_Control {

		private $block_list = null;

		private $redirect_url = null;

		public function __construct() {
			add_action( 'init', array( $this, 'validate_url' ), 10 );
			add_action( 'init', array( $this, 'remove_wc_form_handler' ), 99 );
		}


		public static function enabled() {
			return 'yes' == get_option( 'msm_use_access_control', 'no' );
		}
		public static function get_default_block_fields() {
			return array(
				array(
					"path"       => "/wp-admin",
					"block_list" => 'msm_security_guest'
				),
				array(
					"path"       => "/wp-login.php",
					"block_list" => 'msm_security_guest'
				),
				array(
					"path"       => "/my-account",
					"block_list" => 'msm_security_guest'
				)

			);
		}
		public static function get_default_exception_fields() {
			return array(
				array(
					"path"     => "/wp-login.php",
					"is_param" => "yes",
					"is_path"  => "no",
					"value"    => "loggedout=true"
				),
				array(
					"path"     => "/my-account",
					"is_param" => "no",
					"is_path"  => "yes",
					"value"    => "/my-account/lost-password"

				),
				array(
					"path"     => "/wp-admin",
					"is_param" => "no",
					"is_path"  => "yes",
					"value"    => "/wp-admin/admin-ajax.php"

				),
				array(
					"path"     => "/wp-login.php",
					"is_param" => "yes",
					"is_path"  => "no",
					"value"    => "action=logout"

				),
				array(
					"path"     => "/wp-login.php",
					"is_param" => "yes",
					"is_path"  => "no",
					"value"    => "action=wordpress_social_authenticate"

				),
				array(
					"path"     => "/wp-login.php",
					"is_param" => "yes",
					"is_path"  => "no",
					"value"    => "action=wordpress_social_authenticated"

				)
			);
		}
		protected function get_block_list() {
			if ( is_null( $this->block_list ) ) {

				$this->block_list = array();
				$list             = get_option( 'msm_security_block_list', self::get_default_block_fields() );

				if ( ! is_array( $list ) ) {
					$option = json_decode( $list, true );
					update_option( 'msm_security_block_list', $option );
					$list = get_option( 'msm_security_block_list', self::get_default_block_fields() );

				}

				foreach ( $list as $path ) {
					$this->block_list[ $path['path'] ] = array(
						'block' => false,
						'param' => array(),
						'path'  => array()
					);

					$block_array_lists = explode( ',', $path['block_list'] );
					$block_list        = array();
					foreach ( $block_array_lists as $key => $value ) {
						$block_list[ strtolower( $value ) ] = 'yes';
					}

					if ( array_key_exists( 'msm_security_' . msm_get_user_role(), $block_list ) ) {
						$this->block_list[ $path['path'] ]['block'] = $block_list[ 'msm_security_' . msm_get_user_role() ] == 'yes' ? true : false;
					}
				}

				$exception_list = get_option( 'msm_security_exception_list', self::get_default_exception_fields() );
				if ( ! is_array( $exception_list ) ) {
					$option = json_decode( $exception_list, true );
					update_option( 'msm_security_exception_list', $option );
					$exception_list = get_option( 'msm_security_exception_list', self::get_default_exception_fields() );

				}

				foreach ( $exception_list as $exception ) {
					if ( isset( $this->block_list[ $exception['path'] ] ) ) {
						if ( msm_get( $exception,'is_param','no' ) == 'yes' ? true : false ) {
							$this->block_list[ $exception['path'] ]['param'][] = $exception['value'];
						} else if ( msm_get( $exception,'is_path','no' ) == 'yes' ? true : false ) {
							$this->block_list[ $exception['path'] ]['path'][] = $exception['value'];
						}
					}
				}
			}

			return $this->block_list;
		}
		protected function get_redirect_url() {
			if ( is_null( $this->redirect_url ) ) {
				$this->redirect_url = get_option( 'msm_security_redirect_url', home_url() );
			}

			return apply_filters( 'msm_security_redirect_url', $this->redirect_url );
		}
		public function validate_url() {
			if ( ! is_super_admin() && self::enabled() ) {
				$toolkit_url = sprintf( 'https://%s:8443/modules/wp-toolkit/index.php', gethostname() );
				if ( ! empty( $_SERVER['HTTP_REFERER'] ) && 0 === strpos( $_SERVER['HTTP_REFERER'], $toolkit_url ) ) {
					return;
				}

				if ( 'wordpress_social_account_linking' == msm_get( $_POST, 'action' ) ) {
					return;
				}

				$url = parse_url( home_url() );
				if ( ! empty( $url['path'] ) ) {
					$request = str_replace( $url['path'], '', $_SERVER['REQUEST_URI'] );
				} else {
					$request = $_SERVER['REQUEST_URI'];
				}

				$block_list = $this->get_block_list();

				foreach ( $block_list as $path => $exception ) {
					if ( strpos( $request, $path ) === 0 ) {
						if ( ! $exception['block'] ) {
							return;
						}

						foreach ( $exception['param'] as $except ) {
							$args = explode( '=', $except );
							if ( isset( $_REQUEST[ $args[0] ] ) && $_REQUEST[ $args[0] ] == $args[1] ) {
								return;
							}
						}

						foreach ( $exception['path'] as $except ) {
							if ( strpos( $request, $except ) === 0 ) {
								return;
							}
						}

						wp_redirect( $this->get_redirect_url() );
						exit();
					}
				}
			}
		}

		public function remove_wc_form_handler() {
			if ( self::enabled() && 'yes' == get_option( 'msm_disable_wc_login', 'yes' ) ) {
				remove_action( 'wp_loaded', array( 'WC_Form_Handler', 'process_registration' ), 20 );
			}
		}
	}

	return new MSM_Access_Control();

endif;
