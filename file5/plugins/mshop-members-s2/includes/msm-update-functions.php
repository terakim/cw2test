<?php

defined( 'ABSPATH' ) || exit;

function msm_update_100_social_login() {
	$providers = array (
		'Facebook',
		'Google',
		'Instagram',
		'Naver',
		'Line',
		'Kakao'
	);

	foreach ( $providers as $provider ) {
		if ( '1' == get_option( 'wsl_settings_' . $provider . '_enabled' ) ) {
			$app_id     = get_option( 'wsl_settings_' . $provider . '_app_id' );
			$app_secret = get_option( 'wsl_settings_' . $provider . '_app_secret' );

			$provider = strtolower( $provider );
			delete_option( 'msm_oauth_' . $provider . '_enabled' );
			delete_option( 'msm_oauth_' . $provider . '_client_id' );
			delete_option( 'msm_oauth_' . $provider . '_client_secret' );

			update_option( 'msm_oauth_' . $provider . '_enabled', 'yes' );
			update_option( 'msm_oauth_' . $provider . '_client_id', $app_id );
			update_option( 'msm_oauth_' . $provider . '_client_secret', $app_secret );
		}
	}
}

function msm_update_100_db_version() {
	MSM_Install::update_db_version( '1.0.0' );
}