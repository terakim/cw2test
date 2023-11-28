<?php

//소스에 URL로 직접 접근 방지
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'PAFW_Settings_NPay' ) ) {
	abstract class PAFW_Settings_NPay extends PAFW_Settings {

		public function __construct() {
			$this->master_id = 'npay';

			$this->prefix = '';

			parent::__construct();
		}
		function get_basic_setting_fields() {
			$instance = pafw_get_settings( 'npay_basic' );

			return $instance->get_setting_fields();
		}
		function get_advanced_setting_fields() {
			$instance = pafw_get_settings( 'npay_advanced' );

			return $instance->get_setting_fields();
		}
	}
}
