<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class MSIV_Autoloader {
	private $include_path = '';
	public function __construct() {
		if ( function_exists( "__autoload" ) ) {
			spl_autoload_register( "__autoload" );
		}

		spl_autoload_register( array( $this, 'autoload' ) );

		$this->include_path = untrailingslashit( plugin_dir_path( MSIV_PLUGIN_FILE ) ) . '/includes/';
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
		$file  = $this->get_file_name_from_class( $class );
		$path  = '';

		if ( strpos( $class, 'msiv_') === FALSE ){
			return;
		}

		if ( strpos( $class, 'msiv_admin' ) === 0 ) {
			$path = $this->include_path . 'admin/';
		}elseif ( strpos( $class, 'msiv_shipping_korea_zone' ) === 0 ) {
			$path = $this->include_path . '/shipping/korea-zone/';
		}

		if ( empty( $path ) || ( ! $this->load_file( $path . $file ) && strpos( $class, 'msiv_' ) === 0 ) ) {
			$this->load_file( $this->include_path . $file );
		}
	}
}

new MSIV_Autoloader();
