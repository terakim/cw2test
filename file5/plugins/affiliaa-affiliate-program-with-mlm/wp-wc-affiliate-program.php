<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.redefiningtheweb.com
 * @since             1.0.0
 * @package           Rtwalwm_Wp_Wc_Affiliate_Program
 *
 * @wordpress-plugin
 * Plugin Name:       Affiliaa - Affiliate Program with MLM
 * Plugin URI:        https://redefiningtheweb.com/product/wordpress-woocommerce-affiliate-program/1117/
 * Description:       This plugin helps you to turn your E-commerce Site into an Affiliate System, which eventually boost your overall Sales.
 * Version:           2.5.0
 * Author:            RedefiningTheWeb
 * Author URI:        http://www.redefiningtheweb.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       rtwalwm-wp-wc-affiliate-program
 * Domain Path:       /languages
 * Tested up to:	  6.3.2
 * WC requires at least: 4.2.0
 * WC tested up to: 8.2.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'RTWALWM_PLUGIN_NAME_VERSION', '2.5.0' );
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-wc-affiliate-program-activator.php
 */
function rtwalwm_activate_wp_wc_affiliate_program() {

	
	require_once plugin_dir_path( __FILE__ ) . 'includes/rtwalwm-class-wp-wc-affiliate-program-activator.php';
	Rtwalwm_Wp_Wc_Affiliate_Program_Activator::rtwalwm_activate();
		
}
register_activation_hook( __FILE__, 'rtwalwm_activate_wp_wc_affiliate_program' );
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/rtwalwm-class-wp-wc-affiliate-program.php';
/**
 * Check woocommerce and other required setting to run plugin.
 *
 * @since     1.0.0
 * @return    boolean.
 */
function rtwalwm_check_run_allows_easy()
{
	$rtwalwm_easy_status = true;
	if( function_exists('is_multisite') && is_multisite() )
	{
		include_once(ABSPATH. 'wp-admin/includes/plugin.php');
		if(!is_plugin_active('easy-digital-downloads/easy-digital-downloads.php'))
		{
			$rtwalwm_easy_status = false;
		}
	}
	else
	{
		if( !in_array('easy-digital-downloads/easy-digital-downloads.php', apply_filters('active_plugins', get_option('active_plugins'))  ) )
		{
			$rtwalwm_easy_status = false;
		}
	}
	return $rtwalwm_easy_status;
}

function rtwalwm_check_run_allows()
{
	$rtwalwm_woo_status = true;
	if( function_exists('is_multisite') && is_multisite() )
	{
		include_once(ABSPATH. 'wp-admin/includes/plugin.php');
		if(!is_plugin_active('woocommerce/woocommerce.php'))
		{
			$rtwalwm_woo_status = false;
		}
	
	}
	else
	{
		if( !in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))  ) )
		{
			$rtwalwm_woo_status = false;
		}

	}
	return $rtwalwm_woo_status;
}

if( in_array('wp-wc-affiliate-program/wp-wc-affiliate-program.php', apply_filters('active_plugins', get_option('active_plugins') ) ) )
{
	return;
}
//Plugin Constant
define('RTWALWM_DIR', plugin_dir_path( __FILE__ ) );
define('RTWALWM_URL', plugin_dir_url( __FILE__ ) );
define('RTWALWM_BASEFILE_NAME', plugin_basename(__FILE__) );
define('RTWALWM_HOME', home_url() );
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function rtwalwm_run_wp_wc_affiliate_program() {
	$rtwalwm_plugin = new Rtwalwm_Wp_Wc_Affiliate_Program();
	$rtwalwm_plugin->rtwalwm_run();
}
if( rtwalwm_check_run_allows() )
{
	add_action( 'before_woocommerce_init', function() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	} );

	define('RTWALWM_IS_WOO', 1 );
}else{
	define('RTWALWM_IS_WOO', 0 );
}
if( rtwalwm_check_run_allows_easy() )
{
	define('RTWALWM_IS_Easy', 1 );
}else{
	define('RTWALWM_IS_Easy', 0 );
}
rtwalwm_run_wp_wc_affiliate_program();
