<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.redefiningtheweb.com
 * @since      1.0.0
 *
 * @package    Rtwalwm_Wp_Wc_Affiliate_Program
 * @subpackage Rtwalwm_Wp_Wc_Affiliate_Program/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Rtwalwm_Wp_Wc_Affiliate_Program
 * @subpackage Rtwalwm_Wp_Wc_Affiliate_Program/includes
 * @author     RedefiningTheWeb <developer@redefiningtheweb.com>
 */
class Rtwalwm_Wp_Wc_Affiliate_Program {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Rtwalwm_Wp_Wc_Affiliate_Program_Loader    $rtwalwm_loader    Maintains and registers all hooks for the plugin.
	 */
	protected $rtwalwm_loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $rtwalwm_plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $rtwalwm_plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $rtwalwm_version    The current version of the plugin.
	 */
	protected $rtwalwm_version;

	protected $rtwalwm_curr;
	
	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		
		if ( defined( 'RTWALWM_PLUGIN_NAME_VERSION' ) ) {
			$this->rtwalwm_version = RTWALWM_PLUGIN_NAME_VERSION;
		} else {
			$this->rtwalwm_version = '1.0.0';
		}
		$this->rtwalwm_plugin_name = 'rtwalwm-wp-wc-affiliate-program';

		$this->rtwalwm_load_dependencies();
		$this->rtwalwm_set_locale();
		if( is_admin() ){
			$this->rtwalwm_define_admin_hooks();
		}
		$this->rtwalwm_define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Rtwalwm_Wp_Wc_Affiliate_Program_Loader. Orchestrates the hooks of the plugin.
	 * - Rtwalwm_Wp_Wc_Affiliate_Program_i18n. Defines internationalization functionality.
	 * - Rtwalwm_Wp_Wc_Affiliate_Program_Admin. Defines all hooks for the admin area.
	 * - Rtwalwm_Wp_Wc_Affiliate_Program_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function rtwalwm_load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/rtwalwm-class-wp-wc-affiliate-program-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/rtwalwm-class-wp-wc-affiliate-program-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/rtwalwm-class-wp-wc-affiliate-program-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/rtwalwm-class-wp-wc-affiliate-program-public.php';

		$this->rtwalwm_loader = new Rtwalwm_Wp_Wc_Affiliate_Program_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Rtwalwm_Wp_Wc_Affiliate_Program_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function rtwalwm_set_locale() {

		$rtwalwm_plugin_i18n = new Rtwalwm_Wp_Wc_Affiliate_Program_i18n();

		$this->rtwalwm_loader->rtwalwm_add_action( 'plugins_loaded', $rtwalwm_plugin_i18n, 'rtwalwm_load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function rtwalwm_define_admin_hooks() {

		$rtwalwm_plugin_admin = new Rtwalwm_Wp_Wc_Affiliate_Program_Admin( $this->rtwalwm_get_plugin_name(), $this->rtwalwm_get_version() );

		$this->rtwalwm_loader->rtwalwm_add_action( 'admin_enqueue_scripts', $rtwalwm_plugin_admin, 'rtwalwm_enqueue_styles' );
		$this->rtwalwm_loader->rtwalwm_add_action( 'admin_enqueue_scripts', $rtwalwm_plugin_admin, 'rtwalwm_enqueue_scripts' );

		
		$this->rtwalwm_loader->rtwalwm_add_action( 'admin_menu', $rtwalwm_plugin_admin, 'rtwalwm_add_submenu' );
		

		// adding custom field on add user page
		$this->rtwalwm_loader->rtwalwm_add_action( 'user_new_form', $rtwalwm_plugin_admin, 'rtwalwm_custom_user_profile_fields_add' );
		

		// adding custom meta box in single product page	
		$this->rtwalwm_loader->rtwalwm_add_action( 'add_meta_boxes', $rtwalwm_plugin_admin, 'rtwalwm_add_custom_meta_box' );
		
		
		
		// saving custom meta box in single product page
		$this->rtwalwm_loader->rtwalwm_add_action( 'save_post_product', $rtwalwm_plugin_admin, 'rtwalwm_save_custom_meta_box', 10, 3 );

		
		$this->rtwalwm_loader->rtwalwm_add_action( 'wp_ajax_rtwalwm_approve', $rtwalwm_plugin_admin, 'rtwalwm_approve_callback' );
		$this->rtwalwm_loader->rtwalwm_add_action( 'wp_ajax_rtwalwm_reject', $rtwalwm_plugin_admin, 'rtwalwm_reject_callback' );
		$this->rtwalwm_loader->rtwalwm_add_action( 'wp_ajax_rtwalwm_aff_approve', $rtwalwm_plugin_admin, 'rtwalwm_aff_approve_callback' );
		$this->rtwalwm_loader->rtwalwm_add_action( 'wp_ajax_rtwalwm_referral_delete', $rtwalwm_plugin_admin, 'rtwalwm_referral_delete_callback' );
		$this->rtwalwm_loader->rtwalwm_add_action( 'wp_ajax_rtwalwm_change_affiliate', $rtwalwm_plugin_admin, 'rtwalwm_change_affiliate_callback' );
		$this->rtwalwm_loader->rtwalwm_add_action( 'wp_ajax_rtwalwm_change_prod_commission', $rtwalwm_plugin_admin, 'rtwalwm_change_prod_commission_callback' );
		$this->rtwalwm_loader->rtwalwm_add_action( 'wp_ajax_rtwalwm_custom_banner', $rtwalwm_plugin_admin, 'rtwalwm_custom_banner_callback' );
		// $this->rtwalwm_loader->rtwalwm_add_action( 'wp_ajax_rtwalwm_delete_banner', $rtwalwm_plugin_admin, 'rtwalwm_delete_banner_callback' );

		
		// settings initialize
		$this->rtwalwm_loader->rtwalwm_add_action( 'admin_init', $rtwalwm_plugin_admin, 'rtwalwm_settings_init' );
		//users column
		$this->rtwalwm_loader->rtwalwm_add_filter( 'manage_users_columns', $rtwalwm_plugin_admin, 'rtwalwm_add_affiliate_column', 10 );
		$this->rtwalwm_loader->rtwalwm_add_filter( 'manage_users_custom_column', $rtwalwm_plugin_admin, 'rtwalwm_manage_affiliate_column', 10, 3 );

		//product column
		if(RTWALWM_IS_WOO == 1 )
		{
			$this->rtwalwm_loader->rtwalwm_add_filter( 'manage_product_posts_columns', $rtwalwm_plugin_admin, 'rtwalwm_add_commission_column', 10 );
			$this->rtwalwm_loader->rtwalwm_add_action( 'manage_product_posts_custom_column', $rtwalwm_plugin_admin, 'rtwalwm_manage_commission_column', 10, 2 );
		}
		if(RTWALWM_IS_Easy ==1 )
		{
			$this->rtwalwm_loader->rtwalwm_add_filter( 'edd_download_columns', $rtwalwm_plugin_admin, 'rtwalwm_add_commission_column', 10);
			$this->rtwalwm_loader->rtwalwm_add_action( 'manage_posts_custom_column', $rtwalwm_plugin_admin, 'rtwalwm_manage_commission_column', 10, 2 );
		}
		$this->rtwalwm_loader->rtwalwm_add_filter( 'plugin_action_links_' . RTWALWM_BASEFILE_NAME, $rtwalwm_plugin_admin, 'rtwalwm_add_setting_links' );


		$this->rtwalwm_loader->rtwalwm_add_action( 'woocommerce_coupon_options', $rtwalwm_plugin_admin, 'rtwalwm_add_coupon_text_field_callback',10);

		$this->rtwalwm_loader->rtwalwm_add_action( 'woocommerce_coupon_options_save', $rtwalwm_plugin_admin, 'woocommerce_save_coupon_callback',10);
		
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function rtwalwm_define_public_hooks() {
		
		$rtwalwm_plugin_public = new Rtwalwm_Wp_Wc_Affiliate_Program_Public( $this->rtwalwm_get_plugin_name(), $this->rtwalwm_get_version() );

		$this->rtwalwm_loader->rtwalwm_add_action( 'wp_enqueue_scripts', $rtwalwm_plugin_public, 'rtwalwm_enqueue_styles' );
		$this->rtwalwm_loader->rtwalwm_add_action( 'wp_enqueue_scripts', $rtwalwm_plugin_public, 'rtwalwm_enqueue_scripts' );
		
	


		$this->rtwalwm_loader->rtwalwm_add_filter( 'login_redirect', $rtwalwm_plugin_public, 'rtwalwm_login_fail_redirect', 10, 3  );
		// adding a new menu item
		$rtwalwm_extra_features 		= get_option( 'rtwalwm_extra_features_opt' );
		$rtwalwm_show_in_woo_checked 	= isset( $rtwalwm_extra_features[ 'show_in_woo' ] ) ? $rtwalwm_extra_features[ 'show_in_woo' ] : 1;
		if( $rtwalwm_show_in_woo_checked ){
			$this->rtwalwm_loader->rtwalwm_add_action( 'init', $rtwalwm_plugin_public, 'rtwalwm_add_account_menu_item_endpoint' );
			$this->rtwalwm_loader->rtwalwm_add_filter( 'woocommerce_account_menu_items', $rtwalwm_plugin_public, 'rtwalwm_add_account_menu_item' );
			$this->rtwalwm_loader->rtwalwm_add_action( 'woocommerce_get_endpoint_url', $rtwalwm_plugin_public, 'rtwalwm_add_account_menu_item_endpoint_content', 10, 2 );
		}

    

		// check url
		if(RTWALWM_IS_WOO == 1 )
		{
		$this->rtwalwm_loader->rtwalwm_add_action( 'wp', $rtwalwm_plugin_public, 'rtwalwm_url_check' );

		}
		// check url easy digital downloads 
		if(RTWALWM_IS_Easy == 1 )
		{
		$this->rtwalwm_loader->rtwalwm_add_action( 'wp', $rtwalwm_plugin_public, 'rtwalwm_url_check_edd' );
		}

		//$this->rtwalwm_loader->rtwalwm_add_action( 'woocommerce_applied_coupon', $rtwalwm_plugin_public, 'apply_coupon',10,1);
		// check if referral item is ordered
		if(RTWALWM_IS_WOO == 1 )
		{
		$this->rtwalwm_loader->rtwalwm_add_action( 'woocommerce_checkout_update_order_meta', $rtwalwm_plugin_public, 'rtwalwm_referred_item_ordered' );
		}
	
		if(RTWALWM_IS_Easy == 1 )
		{
		$this->rtwalwm_loader->rtwalwm_add_action( 'edd_complete_purchase', $rtwalwm_plugin_public, 'rtwalwm_referred_item_ordered_easy' );
		}
		// ajax
		$this->rtwalwm_loader->rtwalwm_add_action( 'wp_ajax_rtwalwm_become_affiliate', $rtwalwm_plugin_public, 'rtwalwm_become_affiliate_callback' );
		$this->rtwalwm_loader->rtwalwm_add_action( 'wp_ajax_rtwalwm_search_prod', $rtwalwm_plugin_public, 'rtwalwm_search_prod_callback' );
		// $this->rtwalwm_loader->rtwalwm_add_action( 'wp_ajax_rtwalwm_save_notification', $rtwalwm_plugin_admin, 'rtwalwm_save_notification_callback' );


		$this->rtwalwm_loader->rtwalwm_add_action( 'woocommerce_applied_coupon', $rtwalwm_plugin_public, 'apply_coupon',10,1);

		$this->rtwalwm_loader->rtwalwm_add_action( 'woocommerce_removed_coupon', $rtwalwm_plugin_public, 'remove_coupon',10,1);
	
	
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function rtwalwm_run() {
		$this->rtwalwm_loader->rtwalwm_run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function rtwalwm_get_plugin_name() {
		return $this->rtwalwm_plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Rtwalwm_Wp_Wc_Affiliate_Program_Loader    Orchestrates the hooks of the plugin.
	 */
	public function rtwalwm_get_loader() {
		return $this->rtwalwm_rtwalwm_loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function rtwalwm_get_version() {
		return $this->rtwalwm_version;
	}
}

