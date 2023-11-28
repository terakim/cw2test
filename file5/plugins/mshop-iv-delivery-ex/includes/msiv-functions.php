<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
function msiv_ajax_command( $command ) {
    return MSIV_AJAX_PREFIX . '_' . $command;
}