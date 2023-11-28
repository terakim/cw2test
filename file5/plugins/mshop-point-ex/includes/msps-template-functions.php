<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


if ( ! function_exists( 'msps_output_point' ) ) {
	function msps_output_point() {
		wc_get_template( '/myaccount/msps-point.php', array(), '', MSPS()->template_path() );
	}
}

if ( ! function_exists( 'msps_output_point_logs' ) ) {
	function msps_output_point_logs() {
		wc_get_template( '/myaccount/msps-point-log.php', array(), '', MSPS()->template_path() );
	}
}
