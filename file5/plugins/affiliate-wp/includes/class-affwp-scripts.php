<?php
/**
 * Adds support to affwp JS namespace.
 *
 * @package     AffiliateWP
 * @subpackage  Core
 * @copyright   Copyright (c) 2023, Awesome Motive, Inc
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.15.0
 */

namespace AffiliateWP;

/**
 * Scripts class.
 *
 * @since 2.15.0
 */
final class Scripts {

	/**
	 * The script namespace.
	 *
	 * @since 2.15.0
	 * @access private
	 * @var string
	 */
	private string $namespace = 'affiliatewp';

	/**
	 * The JS folder path.
	 *
	 * @since 2.15.0
	 * @access private
	 * @var string
	 */
	private string $path = '';

	/**
	 * Script suffix, can be `.min` or empty string.
	 *
	 * @since 2.15.0
	 * @access private
	 * @var string
	 */
	private string $suffix = '';

	/**
	 * The file version.
	 *
	 * @since 2.15.0
	 * @access private
	 * @var string
	 */
	private string $version = '';

	/**
	 * Initialize props and hooks.
	 *
	 * @since 2.15.0
	 */
	public function __construct() {

		// Set default properties.
		$this->path    = sprintf( '%sassets/js/', AFFILIATEWP_PLUGIN_URL );
		$this->suffix  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$this->version = AFFILIATEWP_VERSION;

		// Set all hooks.
		$this->hooks();
	}

	/**
	 * Register all hooks.
	 *
	 * We run at a lower priority than the default, so we make sure we can overwrite styles in old files like form.css
	 *
	 * @since 2.15.0
	 *
	 * @return void
	 */
	private function hooks() : void {

		// Register namespace.
		add_action( 'wp_enqueue_scripts', array( $this, 'register_namespace' ), 5 );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_namespace' ), 5 );

		// Register and enqueue other scripts, extending our global.
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ), 5 );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ), 5 );
	}

	/**
	 * Register all styles and scripts related to our namespace.
	 *
	 * @since 2.15.0
	 *
	 * @return void
	 */
	public function register_scripts() : void {

		if ( ! ( affwp_is_affiliate_area() || affwp_is_admin_page() ) ) {
			return; // Restrict to affiliate area and admin only.
		}

		wp_register_style(
			'affiliatewp-modal',
			"{$this->path}vendor/fancybox/fancybox.css",
			array(),
			$this->version
		);

		// Register our modal dependencies.
		wp_register_script(
			'affiliatewp-fancybox',
			"{$this->path}vendor/fancybox/fancybox.umd.js",
			array(),
			$this->version,
			true
		);

		wp_register_script(
			'affiliatewp-modal',
			"{$this->path}affiliatewp-modal{$this->suffix}.js",
			array(
				$this->namespace,
				'affiliatewp-fancybox',
			),
			$this->version,
			true
		);

		// Register tooltip dependencies.
		wp_register_script(
			'affiliatewp-popper',
			"{$this->path}vendor/popper/popper.min.js",
			array(),
			$this->version,
			true
		);

		wp_register_script(
			'affiliatewp-tippy',
			"{$this->path}vendor/tippy/tippy.min.js",
			array( 'affiliatewp-popper' ),
			$this->version,
			true
		);

		wp_register_script(
			'affiliatewp-tooltip',
			"{$this->path}affiliatewp-tooltip{$this->suffix}.js",
			array(
				$this->namespace,
				'affiliatewp-tippy',
			),
			$this->version,
			true
		);

		// Register infinite scroll dependencies.
		wp_register_script(
			'affiliatewp-infinite-scroll',
			"{$this->path}affiliatewp-infinite-scroll{$this->suffix}.js",
			array( $this->namespace ),
			$this->version,
			true
		);
	}

	/**
	 * Register the namespace.
	 *
	 * @since 2.15.0
	 */
	public function register_namespace() : void {

		wp_register_script(
			$this->namespace,
			"{$this->path}{$this->namespace}{$this->suffix}.js",
			array(),
			$this->version,
			true
		);
	}

	/**
	 * Handle script enqueuing, extending it into our namespace.
	 *
	 * Use this method instead of normal wp_enqueue_script function to extend our global object.
	 * It handles automatically script dependencies, and it can be also used to pass default settings to
	 * the new object through the namespace API.
	 *
	 * @since 2.15.0
	 *
	 * @param string $handle The name of te script.
	 * @param array  $dependencies Additional dependencies. Can be both scripts or styles.
	 * @param string $src Optional file source. Overrides the default source path.
	 * @return void
	 */
	public function enqueue( string $handle, array $dependencies = array(), string $src = '' ) : void {

		// Prevent duplicated dependencies.
		$dependencies = array_unique( $dependencies );

		// Check for styles dependencies, enqueue if find any and remove from the dependencies array.
		foreach ( $dependencies as $k => $dependency ) {

			if ( wp_style_is( $dependency, 'registered' ) ) {
				wp_enqueue_style( $dependency );
			}

		}

		// Enqueue the script.
		wp_enqueue_script(
			$handle,
			empty( $src )
				? "{$this->path}{$handle}{$this->suffix}.js"
				: $src,
			array_unique(
				array_merge(
					array( $this->namespace ),
					$dependencies
				)
			),
			$this->version,
			true
		);
	}
}
