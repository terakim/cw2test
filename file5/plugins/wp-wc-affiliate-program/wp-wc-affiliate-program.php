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
 * @package           Rtwwwap_Wp_Wc_Affiliate_Program
 *
 * @wordpress-plugin
 * Plugin Name:       WordPress & WooCommerce Affiliate Program
 * Plugin URI:        http://www.redefiningtheweb.com
 * Description:       This plugin helps you to turn your E-commerce Site into an Affiliate System, which eventually boost your overall Sales.
 * Version:           7.2.0
 * Author:            RedefiningTheWeb
 * Author URI:        http://www.redefiningtheweb.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       rtwwwap-wp-wc-affiliate-program
 * Domain Path:       /languages
 * Tested Up To:      6.3.0
 * WC tested up to:   8.0.2
 * WC requires at least: 2.6.0
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
define( 'RTWWWAP_PLUGIN_NAME_VERSION', '7.2.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-wc-affiliate-program-activator.php
 */


function rtwwwap_activate_wp_wc_affiliate_program() {

	
	require_once plugin_dir_path( __FILE__ ) . 'includes/rtwwwap-class-wp-wc-affiliate-program-activator.php';
	Rtwwwap_Wp_Wc_Affiliate_Program_Activator::rtwwwap_activate();
		
}

register_activation_hook( __FILE__, 'rtwwwap_activate_wp_wc_affiliate_program' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/rtwwwap-class-wp-wc-affiliate-program.php';

/**
 * Check woocommerce and other required setting to run plugin.
 *
 * @since     1.0.0
 * @return    boolean.
 */

function rtwwwap_check_run_allows_easy()
{
	$rtwwwap_easy_status = true;
	if( function_exists('is_multisite') && is_multisite() )
	{
		include_once(ABSPATH. 'wp-admin/includes/plugin.php');
		if(!is_plugin_active('easy-digital-downloads/easy-digital-downloads.php'))
		{
			$rtwwwap_easy_status = false;
		}
	
	}
	else
	{
		if( !in_array('easy-digital-downloads/easy-digital-downloads.php', apply_filters('active_plugins', get_option('active_plugins'))  ) )
		{
			$rtwwwap_easy_status = false;
		}

	}
	return $rtwwwap_easy_status;
}

function rtwwwap_check_run_allows()
{
	$rtwwwap_woo_status = true;
	if( function_exists('is_multisite') && is_multisite() )
	{
		include_once(ABSPATH. 'wp-admin/includes/plugin.php');
		if(!is_plugin_active('woocommerce/woocommerce.php'))
		{
			$rtwwwap_woo_status = false;
		}
	
	}
	else
	{
		if( !in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))  ) )
		{
			$rtwwwap_woo_status = false;
		}

	}
	return $rtwwwap_woo_status;
}

function rtwwwap_check_social_share()
{
	$rtwwwap_social_share = true;
	if( function_exists('is_multisite') && is_multisite() )
	{
		include_once(ABSPATH. 'wp-admin/includes/plugin.php');
		if(!is_plugin_active('rtwsmsap-social-media-share-affiliate-program/rtwsmsap-social-media-share-affiliate-program.php'))
		{
			$rtwwwap_social_share = false;
		}
	
	}
	else
	{
		if( !in_array('rtwsmsap-social-media-share-affiliate-program/rtwsmsap-social-media-share-affiliate-program.php', apply_filters('active_plugins', get_option('active_plugins'))  ) )
		{
			$rtwwwap_social_share = false;
		}

	}
	return $rtwwwap_social_share;
}

$rtwwwap_check_lite = get_option('rtwalwm_affiliate_lite');

function rtwwwap_lite_run_allows()
{
	$rtwwwap_lite_status = true;
	if( function_exists('is_multisite') && is_multisite() )
	{
		include_once(ABSPATH. 'wp-admin/includes/plugin.php');
		if(!is_plugin_active('affiliaa-affiliate-program-with-mlm/wp-wc-affiliate-program.php'))
		{
			$rtwwwap_lite_status = false;
		}
	
	}
	else
	{
		if( !in_array('affiliaa-affiliate-program-with-mlm/wp-wc-affiliate-program.php', apply_filters('active_plugins', get_option('active_plugins'))  ) )
		{
			$rtwwwap_lite_status = false;
		}

	}
	return $rtwwwap_lite_status;
}


	
if (rtwwwap_lite_run_allows() == false)
{
	add_action('admin_notices', 'rtwwwap_error_notice');

	function rtwwwap_error_notice()
	{
		if( is_plugin_active('wp-wc-affiliate-program/wp-wc-affiliate-program.php'))
		{
			deactivate_plugins( 'wp-wc-affiliate-program/wp-wc-affiliate-program.php' );
		}
		$rtwwwap_lite_plugin_link = add_query_arg(
	        array(
	            's' => 'Affiliate Program With MLM',
	            'tab' => 'search',
	            'type' => 'term'
	        ),
	        admin_url( 'plugin-install.php' )
	    );
		?>  
			<style type="text/css">
				.updated.notice
				{
					display: none;
				}
			</style>
			<div class="error notice is-dismissible">
				<p><a href="<?php echo esc_url($rtwwwap_lite_plugin_link) ?>"><?php esc_html_e( 'Affiliaa Lite - Affiliate Program With MLM') ?> </a><?php esc_html_e( 'not activated, Please install/activate it first to Activate ', 'rtwwwap-wp-wc-affiliate-program' );?><strong><?php esc_html_e( 'WordPress & WooCommerce Affiliate Program', 'rtwwwap-wp-wc-affiliate-program' ); ?></strong></p>
		  	</div>	
		<?php	
	
	}

}
else
{
		//Plugin Constant
		define('RTWWWAP_DIR', plugin_dir_path( __FILE__ ) );
		define('RTWWWAP_URL', plugin_dir_url( __FILE__ ) );
		define('RTWWWAP_BASEFILE_NAME', plugin_basename(__FILE__) );
		define('RTWWWAP_HOME', home_url() );


		/**
		 * Begins execution of the plugin.
		 *
		 * Since everything within the plugin is registered via hooks,
		 * then kicking off the plugin from this point in the file does
		 * not affect the page life cycle.
		 *
		 * @since    1.0.0
		 */
		function rtwwwap_run_wp_wc_affiliate_program() {

			$rtwwwap_plugin = new Rtwwwap_Wp_Wc_Affiliate_Program();
			$rtwwwap_plugin->rtwwwap_run();

		}


		if( rtwwwap_check_run_allows() )
		{
			define('RTWWWAP_IS_WOO', 1 );
		}else{
			define('RTWWWAP_IS_WOO', 0 );
		}

		if( rtwwwap_check_run_allows_easy() )
		{
			define('RTWWWAP_IS_Easy', 1 );
		}else{
			define('RTWWWAP_IS_Easy', 0 );
		}
		if( rtwwwap_check_social_share() )
		{
			define('RTWWWAP_IS_Social_Share', 1 );
		}else{
			define('RTWWWAP_IS_Social_Share', 0 );
		}

		rtwwwap_run_wp_wc_affiliate_program();
	
}
	



