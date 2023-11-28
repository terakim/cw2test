<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://www.redefiningtheweb.com
 * @since      1.0.0
 *
 * @package    Rtwwwap_Wp_Wc_Affiliate_Program
 * @subpackage Rtwwwap_Wp_Wc_Affiliate_Program/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Rtwwwap_Wp_Wc_Affiliate_Program
 * @subpackage Rtwwwap_Wp_Wc_Affiliate_Program/includes
 * @author     RedefiningTheWeb <developer@redefiningtheweb.com>
 */
class Rtwwwap_Wp_Wc_Affiliate_Program_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function rtwwwap_load_plugin_textdomain() {

		load_plugin_textdomain(
			'rtwwwap-wp-wc-affiliate-program',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}
}
