<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_action( 'msps-tab-point', 'msps_output_point', 10 );
add_action( 'msps-tab-point-logs', 'msps_output_point_logs', 10 );
