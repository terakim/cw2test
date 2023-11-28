<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class MSM_Autoloader {
	private $include_path = '';
	public function __construct() {
		if ( function_exists( "__autoload" ) ) {
			spl_autoload_register( "__autoload" );
		}

		spl_autoload_register( array( $this, 'autoload' ) );

		$this->include_path = untrailingslashit( plugin_dir_path( MSM_PLUGIN_FILE ) ) . '/includes/';
	}
	private function get_file_name_from_class( $class ) {
		return 'class-' . str_replace( '_', '-', $class ) . '.php';
	}
	private function load_file( $path ) {
		if ( $path && is_readable( $path ) ) {
			include_once( $path );

			return true;
		}

		return false;
	}
	public function autoload( $class ) {
		$class = strtolower( $class );

		if ( strpos( $class, 'msm_' ) === false && strpos( $class, 'mfd_' ) === false ) {
			return;
		}

		$file = $this->get_file_name_from_class( $class );
		$path = '';

		if ( strpos( $class, 'msm_admin' ) === 0 ) {
			$path = $this->include_path . 'admin/';
		} elseif ( strpos( $class, 'msm_settings' ) === 0 ) {
			$path = $this->include_path . 'admin/settings/';
		} elseif ( strpos( $class, 'msm_meta_box' ) === 0 ) {
			$path = $this->include_path . 'admin/meta-boxes/';
		} elseif ( strpos( $class, 'msm_action_' ) === 0 ) {
			$path = $this->include_path . 'actions/';
		} elseif ( strpos( $class, 'mfd_' ) === 0 ) {
			$path = $this->include_path . 'fields/';
		}

		if ( empty( $path ) || ( ! $this->load_file( $path . $file ) && strpos( $class, 'msm_' ) === 0 ) ) {
			$this->load_file( $this->include_path . $file );
		}

	}
}

new MSM_Autoloader();
