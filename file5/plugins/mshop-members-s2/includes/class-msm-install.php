<?php

defined( 'ABSPATH' ) || exit;
class MSM_Install {
	private static $db_updates = array (
		'1.0.0' => array (
			'msm_update_100_social_login',
			'msm_update_100_db_version',
		)
	);
	public static function init() {
		add_action( 'init', array ( __CLASS__, 'check_version' ), 5 );
	}

	public static function check_version() {
		if ( ! defined( 'IFRAME_REQUEST' ) && version_compare( get_option( 'msm_db_version' ), MSM_DB_VERSION, '<' ) ) {
			self::install();
		}
	}
	public static function install() {
		if ( ! is_blog_installed() ) {
			return;
		}

		// Check if we are not already running this routine.
		if ( 'yes' === get_transient( 'msm_installing' ) ) {
			return;
		}

		// If we made it till here nothing is running yet, lets set the transient now.
		set_transient( 'msm_installing', 'yes', MINUTE_IN_SECONDS * 10 );

		self::maybe_update_db_version();
        self::create_roles();

		delete_transient( 'msm_installing' );
	}

	public static function needs_db_update() {
		$current_db_version = get_option( 'msm_db_version', null );
		$updates            = self::get_db_update_callbacks();

		return version_compare( $current_db_version, max( array_keys( $updates ) ), '<' );
	}
	private static function maybe_update_db_version() {
		if ( self::needs_db_update() ) {
			self::update();
		} else {
			self::update_db_version();
		}
	}
	public static function get_db_update_callbacks() {
		return self::$db_updates;
	}
	private static function update() {
		$current_db_version = get_option( 'msm_db_version' );

		foreach ( self::get_db_update_callbacks() as $version => $update_callbacks ) {
			if ( version_compare( $current_db_version, $version, '<' ) ) {
				foreach ( $update_callbacks as $update_callback ) {
					$update_callback();
				}
			}
		}
	}
	public static function update_db_version( $version = null ) {
		delete_option( 'msm_db_version' );
		add_option( 'msm_db_version', is_null( $version ) ? MSM_DB_VERSION : $version );
	}
    public static function create_roles() {
        global $wp_roles;

        if ( ! class_exists( 'WP_Roles' ) ) {
            return;
        }

        if ( ! isset( $wp_roles ) ) {
            $wp_roles = new WP_Roles();
        }

        $capabilities = self::get_core_capabilities();

        foreach ( $capabilities as $cap_group ) {
            foreach ( $cap_group as $cap ) {
                $wp_roles->add_cap( 'administrator', $cap );
            }
        }
    }
    private static function get_core_capabilities() {
        $capabilities = array();

        $capability_types = array( 'mshop_role_request', 'msm_post' );

        foreach ( $capability_types as $capability_type ) {

            $capabilities[ $capability_type ] = array(
                // Post type
                "edit_{$capability_type}",
                "read_{$capability_type}",
                "delete_{$capability_type}",
                "edit_{$capability_type}s",
                "edit_others_{$capability_type}s",
                "publish_{$capability_type}s",
                "read_private_{$capability_type}s",
                "delete_{$capability_type}s",
                "delete_private_{$capability_type}s",
                "delete_published_{$capability_type}s",
                "delete_others_{$capability_type}s",
                "edit_private_{$capability_type}s",
                "edit_published_{$capability_type}s",

                // Terms
                "manage_{$capability_type}_terms",
                "edit_{$capability_type}_terms",
                "delete_{$capability_type}_terms",
                "assign_{$capability_type}_terms",
            );
        }

        return $capabilities;
    }

}

MSM_Install::init();
