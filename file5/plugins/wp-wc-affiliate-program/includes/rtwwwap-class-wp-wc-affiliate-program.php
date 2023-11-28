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
 * @package    Rtwwwap_Wp_Wc_Affiliate_Program
 * @subpackage Rtwwwap_Wp_Wc_Affiliate_Program/includes
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
 * @package    Rtwwwap_Wp_Wc_Affiliate_Program
 * @subpackage Rtwwwap_Wp_Wc_Affiliate_Program/includes
 * @author     RedefiningTheWeb <developer@redefiningtheweb.com>
 */
class Rtwwwap_Wp_Wc_Affiliate_Program {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Rtwwwap_Wp_Wc_Affiliate_Program_Loader    $rtwwwap_loader    Maintains and registers all hooks for the plugin.
	 */
	protected $rtwwwap_loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $rtwwwap_plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $rtwwwap_plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $rtwwwap_version    The current version of the plugin.
	 */
	protected $rtwwwap_version;

	protected $rtwwwap_curr;
	
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

		if ( defined( 'RTWWWAP_PLUGIN_NAME_VERSION' ) ) {
			$this->rtwwwap_version = RTWWWAP_PLUGIN_NAME_VERSION;
		} else {
			$this->rtwwwap_version = '1.0.0';
		}
		$this->rtwwwap_plugin_name = 'rtwwwap-wp-wc-affiliate-program';

		$this->rtwwwap_load_dependencies();
		$this->rtwwwap_set_locale();
		if( is_admin() ){
			$this->rtwwwap_define_admin_hooks();
		}
		$rtwwwap_verification_done = get_option( 'rtwwwap_verification_done', array() );
		if( !empty( $rtwwwap_verification_done ) && $rtwwwap_verification_done['status'] == true && !empty($rtwwwap_verification_done['purchase_code']) )
		{
			$this->rtwwwap_define_public_hooks();
		}

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Rtwwwap_Wp_Wc_Affiliate_Program_Loader. Orchestrates the hooks of the plugin.
	 * - Rtwwwap_Wp_Wc_Affiliate_Program_i18n. Defines internationalization functionality.
	 * - Rtwwwap_Wp_Wc_Affiliate_Program_Admin. Defines all hooks for the admin area.
	 * - Rtwwwap_Wp_Wc_Affiliate_Program_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function rtwwwap_load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/rtwwwap-class-wp-wc-affiliate-program-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/rtwwwap-class-wp-wc-affiliate-program-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/rtwwwap-class-wp-wc-affiliate-program-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/rtwwwap-class-wp-wc-affiliate-program-public.php';

		$this->rtwwwap_loader = new Rtwwwap_Wp_Wc_Affiliate_Program_Loader();
	

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Rtwwwap_Wp_Wc_Affiliate_Program_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function rtwwwap_set_locale() {

		$rtwwwap_plugin_i18n = new Rtwwwap_Wp_Wc_Affiliate_Program_i18n();

		$this->rtwwwap_loader->rtwwwap_add_action( 'plugins_loaded', $rtwwwap_plugin_i18n, 'rtwwwap_load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function rtwwwap_define_admin_hooks() {

		$rtwwwap_plugin_admin = new Rtwwwap_Wp_Wc_Affiliate_Program_Admin( $this->rtwwwap_get_plugin_name(), $this->rtwwwap_get_version() );
		
		$this->rtwwwap_loader->rtwwwap_add_action( 'admin_enqueue_scripts', $rtwwwap_plugin_admin, 'rtwwwap_enqueue_styles' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'admin_enqueue_scripts', $rtwwwap_plugin_admin, 'rtwwwap_enqueue_scripts' );

		// exporter
		$this->rtwwwap_loader->rtwwwap_add_filter( 'wp_privacy_personal_data_exporters', $rtwwwap_plugin_admin, 'rtwwwap_export' );
		$this->rtwwwap_loader->rtwwwap_add_filter( 'wp_privacy_personal_data_erasers', $rtwwwap_plugin_admin, 'rtwwwap_eraser' );

		$this->rtwwwap_loader->rtwwwap_add_action( 'admin_menu', $rtwwwap_plugin_admin, 'rtwwwap_add_submenu' );
		

		// adding custom field on add user page
		$this->rtwwwap_loader->rtwwwap_add_action( 'user_new_form', $rtwwwap_plugin_admin, 'rtwwwap_custom_user_profile_fields_add' );

		// saving custom field on add user page
		$this->rtwwwap_loader->rtwwwap_add_action( 'user_register', $rtwwwap_plugin_admin, 'rtwwwap_save_custom_user_profile_fields_add' );

		// adding custom field on edit user page
		$this->rtwwwap_loader->rtwwwap_add_action( 'show_user_profile', $rtwwwap_plugin_admin, 'rtwwwap_custom_user_profile_fields_edit', 99 );
		$this->rtwwwap_loader->rtwwwap_add_action( 'edit_user_profile', $rtwwwap_plugin_admin, 'rtwwwap_custom_user_profile_fields_edit', 99 );

		// saving custom field on edit user page
		$this->rtwwwap_loader->rtwwwap_add_action( 'personal_options_update', $rtwwwap_plugin_admin, 'rtwwwap_save_custom_user_profile_fields_edit' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'edit_user_profile_update', $rtwwwap_plugin_admin, 'rtwwwap_save_custom_user_profile_fields_edit' );

		// adding custom meta box in single product page
		$this->rtwwwap_loader->rtwwwap_add_action( 'add_meta_boxes', $rtwwwap_plugin_admin, 'rtwwwap_add_custom_meta_box' );
		
		// add-on actions
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_generate_payout_csv', $rtwwwap_plugin_admin, 'rtwwwap_generate_payout_csv_callback' );
	
		// saving custom meta box in single product page
		$this->rtwwwap_loader->rtwwwap_add_action( 'save_post_product', $rtwwwap_plugin_admin, 'rtwwwap_save_custom_meta_box', 10, 3 );

		//coupon section
		$this->rtwwwap_loader->rtwwwap_add_action( 'woocommerce_coupon_options', $rtwwwap_plugin_admin, 'rtwwwap_add_coupon_text_field_callback',10);
		$this->rtwwwap_loader->rtwwwap_add_action( 'woocommerce_coupon_options_save', $rtwwwap_plugin_admin, 'rtwwwap_coupon_option_save',10);

		//rank_update_code
		$this->rtwwwap_loader->rtwwwap_add_action( 'init', $rtwwwap_plugin_admin, 'rtwwporg_custom_post_type' );
		/// add meta box in custom post type Rank Type
		$this->rtwwwap_loader->rtwwwap_add_action( 'add_meta_boxes', $rtwwwap_plugin_admin, 'rtwwwapwpse_add_custom_meta_box_2',10,2);
		//close

		// ajax
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_delete_noti', $rtwwwap_plugin_admin, 'rtwwwap_delete_noti_callback' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_add_manual_referral', $rtwwwap_plugin_admin, 'rtwwwap_add_manual_referral' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_change_affiliate', $rtwwwap_plugin_admin, 'rtwwwap_change_affiliate_callback' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_change_affiliate_level', $rtwwwap_plugin_admin, 'rtwwwap_change_affiliate_level_callback' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_change_prod_commission', $rtwwwap_plugin_admin, 'rtwwwap_change_prod_commission_callback' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_paypal', $rtwwwap_plugin_admin, 'rtwwwap_paypal_callback' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_stripe', $rtwwwap_plugin_admin, 'rtwwwap_stripe_callback' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_approve', $rtwwwap_plugin_admin, 'rtwwwap_approve_callback' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_reject', $rtwwwap_plugin_admin, 'rtwwwap_reject_callback' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_aff_approve', $rtwwwap_plugin_admin, 'rtwwwap_aff_approve_callback' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_aff_level_delete', $rtwwwap_plugin_admin, 'rtwwwap_aff_level_delete_callback' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_referral_delete', $rtwwwap_plugin_admin, 'rtwwwap_referral_delete_callback' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_direct_pay', $rtwwwap_plugin_admin, 'rtwwwap_direct_pay_callback' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_update_level_order', $rtwwwap_plugin_admin, 'rtwwwap_update_level_order_callback' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_get_mlm_chain', $rtwwwap_plugin_admin, 'rtwwwap_get_mlm_chain_callback' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_deactive_aff', $rtwwwap_plugin_admin, 'rtwwwap_deactive_aff_callback' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_active_aff', $rtwwwap_plugin_admin, 'rtwwwap_active_aff_callback' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_verify_purchase_code', $rtwwwap_plugin_admin, 'rtwwwap_verify_purchase_code_callback' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_custom_banner', $rtwwwap_plugin_admin, 'rtwwwap_custom_banner_callback' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_delete_banner', $rtwwwap_plugin_admin, 'rtwwwap_delete_banner_callback' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_payment_pay', $rtwwwap_plugin_admin, 'rtwwwap_payment_pay_callback' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_save_notification', $rtwwwap_plugin_admin, 'rtwwwap_save_notification_callback' );

		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_save_rank_requirement', $rtwwwap_plugin_admin, 'rtwwwap_save_rank_requirement_callback' );

		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_delete_rank', $rtwwwap_plugin_admin, 'rtwwwap_delete_rank_callback' );

		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_update_rank_requirement', $rtwwwap_plugin_admin, 'rtwwwap_update_rank_requirement_callback' );
		
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_edit_rank_requirement', $rtwwwap_plugin_admin, 'rtwwwap_edit_rank_requirement' );

		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_delete_custom_field', $rtwwwap_plugin_admin, 'rtwwwap_delete_custom_field_callback' );

		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_save_customize_email', $rtwwwap_plugin_admin, 'rtwwwap_save_customize_email_callback' );

		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_edit_customize_email', $rtwwwap_plugin_admin, 'rtwwwap_edit_customize_email_callback' );

		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_activate_email', $rtwwwap_plugin_admin, 'rtwwwap_activate_email_callback' );
		
		// settings initialize
		$this->rtwwwap_loader->rtwwwap_add_action( 'admin_init', $rtwwwap_plugin_admin, 'rtwwwap_settings_init' );

		$this->rtwwwap_loader->rtwwwap_add_filter( 'custom_field_show_button', $rtwwwap_plugin_admin, 'rtwwwap_custom_field_show_button', 10 );

		if(in_array('addon_for_MLM_qualification/affiliate_mlm_addon.php', apply_filters('active_plugins', get_option('active_plugins') ) ) ){
			$rtwwwap_order_status = array_unique(apply_filters('woocommerce_order_is_paid_statuses', array('processing', 'completed', 'on-hold','pending','refunded','cancelled')));

			if (!in_array('completed', $rtwwwap_order_status)) {
				$rtwwwap_order_status[] = 'completed';
			}

			foreach ($rtwwwap_order_status as $rtwwwap_status_check) 
			{
				$this->rtwwwap_loader->rtwwwap_add_action( 'woocommerce_order_status_'. $rtwwwap_status_check, $rtwwwap_plugin_admin, 'rtwwwap_after_payment_successfull', '' , 2);
			}
		}


		//users column
		$this->rtwwwap_loader->rtwwwap_add_filter( 'manage_users_columns', $rtwwwap_plugin_admin, 'rtwwwap_add_affiliate_column', 10 );
		$this->rtwwwap_loader->rtwwwap_add_filter( 'manage_users_custom_column', $rtwwwap_plugin_admin, 'rtwwwap_manage_affiliate_column', 10, 3 );

		//product column
		if(RTWWWAP_IS_WOO == 1 )
		{
			$this->rtwwwap_loader->rtwwwap_add_filter( 'manage_product_posts_columns', $rtwwwap_plugin_admin, 'rtwwwap_add_commission_column', 10 );
			$this->rtwwwap_loader->rtwwwap_add_action( 'manage_product_posts_custom_column', $rtwwwap_plugin_admin, 'rtwwwap_manage_commission_column', 10, 2 );
		}
		if(RTWWWAP_IS_Easy ==1 )
		{
			$this->rtwwwap_loader->rtwwwap_add_filter( 'edd_download_columns', $rtwwwap_plugin_admin, 'rtwwwap_add_commission_column', 10);
			$this->rtwwwap_loader->rtwwwap_add_action( 'manage_posts_custom_column', $rtwwwap_plugin_admin, 'rtwwwap_manage_commission_column', 10, 2 );
		}
		$this->rtwwwap_loader->rtwwwap_add_filter( 'plugin_action_links_' . RTWWWAP_BASEFILE_NAME, $rtwwwap_plugin_admin, 'rtwwwap_add_setting_links' );

		



		//deleting user from MLM
		$this->rtwwwap_loader->rtwwwap_add_action( 'deleted_user', $rtwwwap_plugin_admin, 'rtwwwap_delete_user_mlm' );

		//delete_purchase_code
		if(isset($_GET['rtwwwap_action']) && $_GET['rtwwwap_action'] == 'delete_purchase_code')
		{
			$this->rtwwwap_loader->rtwwwap_add_action( 'admin_init', $rtwwwap_plugin_admin, 'rtwwwap_delete_purchase_code' );
		}


		//Add a Affiliate product tab for simple.
		$rtwwwap_two_way_comm 		= get_option( 'rtwwwap_commission_settings_opt' );
		$rtwwwap_two_way_comm_checked 	= isset( $rtwwwap_two_way_comm[ 'two_way_comm' ] ) ? $rtwwwap_two_way_comm[ 'two_way_comm' ] : 0;
		if( $rtwwwap_two_way_comm_checked )
		{
			if(RTWWWAP_IS_WOO == 1 )
			{
				$this->rtwwwap_loader->rtwwwap_add_filter( 'woocommerce_product_data_tabs', $rtwwwap_plugin_admin, 'rtwwwap_affiliate_product_tabs', 99);
				//Affiliate Product Tab contents
				$this->rtwwwap_loader->rtwwwap_add_filter( 'woocommerce_product_data_panels', $rtwwwap_plugin_admin, 'rtwwwap_commission_product_tab_content' );
				//Save Referee Setting
				$this->rtwwwap_loader->rtwwwap_add_action( 'woocommerce_process_product_meta_simple', $rtwwwap_plugin_admin, 'rtwwwap_save_commission_fields' );
			}
			else if(RTWWWAP_IS_Easy ==1)
			{
				$this->rtwwwap_loader->rtwwwap_add_action( 'add_meta_boxes', $rtwwwap_plugin_admin, 'rtwwwap_add_two_way_custom_meta_box' );
				$this->rtwwwap_loader->rtwwwap_add_action( 'save_post', $rtwwwap_plugin_admin, 'rtwwwap_save_two_way_commission_fields' );
				
			}



		}
		
	



	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function rtwwwap_define_public_hooks() {

		$rtwwwap_plugin_public = new Rtwwwap_Wp_Wc_Affiliate_Program_Public( $this->rtwwwap_get_plugin_name(), $this->rtwwwap_get_version() );
		$rtwwwap_affilaite_template = isset($rtwwwap_extra_features[ 'affiliate_page_template' ]) ? $rtwwwap_extra_features[ 'affiliate_page_template' ] : 1 ;

		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_enqueue_scripts', $rtwwwap_plugin_public, 'rtwwwap_enqueue_styles' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_enqueue_scripts', $rtwwwap_plugin_public, 'rtwwwap_enqueue_scripts' );

		if($rtwwwap_affilaite_template == 3 && is_user_logged_in())
		{
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_enqueue_scripts', $rtwwwap_plugin_public, 'rtwwwap_control_style', 9999 );
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_enqueue_scripts', $rtwwwap_plugin_public, 'rtwwwap_controll_js', 9999 );
		}

		// For traffic functionality 
		$this->rtwwwap_loader->rtwwwap_add_action( 'init', $rtwwwap_plugin_public,'rtwwwap_get_user_ip' ); 


		$this->rtwwwap_loader->rtwwwap_add_filter( 'login_redirect', $rtwwwap_plugin_public, 'rtwwwap_login_fail_redirect', 10, 3  );
		$this->rtwwwap_loader->rtwwwap_add_filter( 'registration_errors', $rtwwwap_plugin_public, 'rtwwwap_register_fail_redirect', 9, 1  );

		// $this->rtwwwap_loader->rtwwwap_add_action( 'login_form_rp', $rtwwwap_plugin_public,'rtwwwap_override_reset_password_form_redirect' );
		// $this->rtwwwap_loader->rtwwwap_add_action( 'login_form_resetpass', $rtwwwap_plugin_public,'rtwwwap_override_reset_password_form_redirect' );

		// $this->rtwwwap_loader->rtwwwap_add_action( 'login_form_rp', $rtwwwap_plugin_public, 'rtwwwap_do_password_reset' );
		// $this->rtwwwap_loader->rtwwwap_add_action( 'login_form_resetpass', $rtwwwap_plugin_public,'rtwwwap_do_password_reset' );

		// adding a new menu item
		$rtwwwap_extra_features 		= get_option( 'rtwwwap_extra_features_opt' );
		
		if(isset($rtwwwap_extra_features[ 'rtwwwap_active_membership' ]) && $rtwwwap_extra_features[ 'rtwwwap_active_membership' ] == 1  )
		{
			$this->rtwwwap_loader->rtwwwap_add_action( 'init', $rtwwwap_plugin_public, 'rtwwwap_member_redirect_successful' );

		}

		$this->rtwwwap_loader->rtwwwap_add_action( 'wp', $rtwwwap_plugin_public, 'rtwwwap_coupon_check' );


		$rtwwwap_show_in_woo_checked 	= isset( $rtwwwap_extra_features[ 'show_in_woo' ] ) ? $rtwwwap_extra_features[ 'show_in_woo' ] : 1;
		if( $rtwwwap_show_in_woo_checked ){
			$this->rtwwwap_loader->rtwwwap_add_action( 'init', $rtwwwap_plugin_public, 'rtwwwap_add_account_menu_item_endpoint' );
			$this->rtwwwap_loader->rtwwwap_add_filter( 'woocommerce_account_menu_items', $rtwwwap_plugin_public, 'rtwwwap_add_account_menu_item' );
			$this->rtwwwap_loader->rtwwwap_add_action( 'woocommerce_get_endpoint_url', $rtwwwap_plugin_public, 'rtwwwap_add_account_menu_item_endpoint_content', 10, 2 );
		}
		//$this->rtwwwap_loader->rtwwwap_add_action( 'wp', $rtwwwap_plugin_public, 'rtwwwap_login_register_page_redirect', 99 );

	
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp', $rtwwwap_plugin_public, 'rtwwwap_url_check' );
		
	

		// check if referral item is ordered
		if(RTWWWAP_IS_WOO == 1 )
		{
		$this->rtwwwap_loader->rtwwwap_add_action( 'woocommerce_checkout_update_order_meta', $rtwwwap_plugin_public, 'rtwwwap_referred_item_ordered' );
		
		}
		/* check if referral item is ordered for -> easy digital downloads 
		edd_download_batch_export 
		edd_update_cart 
		template_redirect 
		edd_post_update_ 
		edd_straight_to_gateway
		edd_insert_payment
		*/
		if(RTWWWAP_IS_Easy == 1 )
		{
		$this->rtwwwap_loader->rtwwwap_add_action( 'edd_complete_purchase', $rtwwwap_plugin_public, 'rtwwwap_referred_item_ordered_easy' );
		}

		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_login_request', $rtwwwap_plugin_public, 'rtwwwap_login_request_callback' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_register_request', $rtwwwap_plugin_public, 'rtwwwap_register_request_callback' );

		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_nopriv_rtwwwap_login_request', $rtwwwap_plugin_public, 'rtwwwap_login_request_callback' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_nopriv_rtwwwap_register_request', $rtwwwap_plugin_public, 'rtwwwap_register_request_callback' );

		// ajax
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_become_affiliate', $rtwwwap_plugin_public, 'rtwwwap_become_affiliate_callback' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_search_prod', $rtwwwap_plugin_public, 'rtwwwap_search_prod_callback' );

		// $this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_order_details', $rtwwwap_plugin_public, 'rtwwwap_order_details_callback' );
		
		// change password
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_verify_old_psw', $rtwwwap_plugin_public, 'rtwwwap_verify_old_psw_callback' );

		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_generate_csv', $rtwwwap_plugin_public, 'rtwwwap_generate_csv_callback' );
		if(RTWWWAP_IS_WOO == 1 )
		{
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_create_coupon', $rtwwwap_plugin_public, 'rtwwwap_create_coupon_callback' );
		}
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_public_get_mlm_chain', $rtwwwap_plugin_public, 'rtwwwap_public_get_mlm_chain_callback' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_public_deactive_aff', $rtwwwap_plugin_public, 'rtwwwap_public_deactive_aff_callback' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_public_active_aff', $rtwwwap_plugin_public, 'rtwwwap_public_active_aff_callback' );


		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_send_email', $rtwwwap_plugin_public, 'rtwwwap_send_email_callback' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_nopriv_rtwwwap_send_email', $rtwwwap_plugin_public, 'rtwwwap_send_email_callback' );

		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_verify_otp_psw', $rtwwwap_plugin_public, 'rtwwwap_verify_otp_psw_callback' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_nopriv_rtwwwap_verify_otp_psw', $rtwwwap_plugin_public, 'rtwwwap_verify_otp_psw_callback' );
		
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_verify_old_psw', $rtwwwap_plugin_public, 'rtwwwap_verify_old_psw_callback' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_nopriv_rtwwwap_verify_old_psw', $rtwwwap_plugin_public, 'rtwwwap_verify_old_psw_callback' );


		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_search_product_temp3', $rtwwwap_plugin_public, 'rtwwwap_search_product_temp3_callback');
	

		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_reset_password', $rtwwwap_plugin_public, 'rtwwwap_send_reset_password_callback' );

		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_payout_save', $rtwwwap_plugin_public, 'rtwwwap_payout_save_callback' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_save_profile', $rtwwwap_plugin_public, 'rtwwwap_save_profile_callback' );
		
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_theme_change', $rtwwwap_plugin_public, 'rtwwwap_rtwwwap_theme_change_callback' );

		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_payout_referral_email', $rtwwwap_plugin_public, 'rtwwwap_payout_referral_email_callback' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_payment_method', $rtwwwap_plugin_public, 'rtwwwap_payment_method_callback' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_withdrawal_request', $rtwwwap_plugin_public, 'rtwwwap_withdrawal_request_callback' );

		$this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_rtwwwap_noti_id', $rtwwwap_plugin_public, 'rtwwwap_noti_id_callback');

	
		//apply coupon
		$this->rtwwwap_loader->rtwwwap_add_action( 'woocommerce_applied_coupon', $rtwwwap_plugin_public, 'rtwwwap_apply_coupon',10,1 );

		//remove coupon 
		$this->rtwwwap_loader->rtwwwap_add_action( 'woocommerce_removed_coupon', $rtwwwap_plugin_public, 'rtwwwap_coupon_removed_action',10,1 );


		$this->rtwwwap_loader->rtwwwap_add_action( 'rtwwwap_becomeing_affiliate', $rtwwwap_plugin_public, 'rtwwwap_becomeing_affiliate_callback' );

		//template
		$rtwwwap_affilaite_template = isset($rtwwwap_extra_features[ 'affiliate_page_template' ]) ? $rtwwwap_extra_features[ 'affiliate_page_template' ] : 1 ;

		if($rtwwwap_affilaite_template == 3)
		{
		$this->rtwwwap_loader->rtwwwap_add_action('theme_page_templates', $rtwwwap_plugin_public, 'rtwwwap_add_template_to_select', 10, 4);
		$this->rtwwwap_loader->rtwwwap_add_filter('template_include', $rtwwwap_plugin_public, 'rtwwwap_include_temp3');
		}
		//for coupons
		// if(RTWWWAP_IS_WOO == 1 )
		// {
		$this->rtwwwap_loader->rtwwwap_add_action( 'woocommerce_order_add_coupon', $rtwwwap_plugin_public, 'rtwwwap_woocommerce_order_add_coupon', 10, 5 );
	


		
		// }
			// for coupons easy digital downloads 
		// if(RTWWWAP_IS_Easy == 1 )
		// {
		// $this->rtwwwap_loader->rtwwwap_add_action( 'wp_ajax_edd_apply_discount', $rtwwwap_plugin_public, 'rtwwwap_easy_order_add_coupon', 10, 5 );
		// }
		//signup bonus
		// $this->rtwwwap_loader->rtwwwap_add_action( 'user_register', $rtwwwap_plugin_public, 'rtwwwap_user_register_signup_bonus', 10, 5 );
		// $this->rtwwwap_loader->rtwwwap_add_action( 'user_register', $rtwwwap_plugin_public, 'rtwwwap_user_register', 9  );

		//add field in register form for woocommerce
		$this->rtwwwap_loader->rtwwwap_add_action( 'woocommerce_register_form', $rtwwwap_plugin_public, 'rtwwwap_add_code_field' );
		$this->rtwwwap_loader->rtwwwap_add_action( 'woocommerce_created_customer', $rtwwwap_plugin_public, 'rtwwwap_create_signup_referral_from_woo', 10, 3 );


		//Apply discount rule to cart

		$rtwwwap_two_way_comm 		= get_option( 'rtwwwap_commission_settings_opt' );
		$rtwwwap_two_way_comm_checked 	= isset( $rtwwwap_two_way_comm[ 'two_way_comm' ] ) ? $rtwwwap_two_way_comm[ 'two_way_comm' ] : 0;
		if( $rtwwwap_two_way_comm_checked )
		{	
			if(RTWWWAP_IS_WOO == 1 )
			{
			$this->rtwwwap_loader->rtwwwap_add_action( 'woocommerce_cart_loaded_from_session', $rtwwwap_plugin_public, 'rtwwwap_cart_loaded_from_session', 98, 1 );
			//Add sale price html.
			//$this->rtwwwap_loader->rtwwwap_add_filter( 'woocommerce_cart_item_price', $rtwwwap_plugin_public, 'rtwwwap_on_display_cart_item_price_html', 10, 3 );
			}
			// for session cart update
			if(RTWWWAP_IS_Easy == 1 )
			{
				//$this->rtwwwap_loader->rtwwwap_add_action( 'edd_cart_contents_loaded_from_session', $rtwwwap_plugin_public, 'rtwwwap_easy_cart_loaded_from_session', 98, 1 );
			
			
				// for update cart item value when discount applied
				$this->rtwwwap_loader->rtwwwap_add_filter( 'edd_cart_item_price', $rtwwwap_plugin_public, 'rtwwwap_on_display_cart_item_price_html_edd', 10, 2 );
			}
			
		}
		if(RTWWWAP_IS_Social_Share)
		{
			// $this->rtwwwap_loader->rtwwwap_add_action( 'init', $rtwwwap_plugin_public,'rtwwwap_facebook_login_callback' );
		}

	
			//Add sale price html.
		$this->rtwwwap_loader->rtwwwap_add_filter( 'woocommerce_cart_item_price', $rtwwwap_plugin_public, 'rtwwwap_on_display_cart_item_price_html', 10, 3 );
	
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function rtwwwap_run() {
		$this->rtwwwap_loader->rtwwwap_run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function rtwwwap_get_plugin_name() {
		return $this->rtwwwap_plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Rtwwwap_Wp_Wc_Affiliate_Program_Loader    Orchestrates the hooks of the plugin.
	 */
	public function rtwwwap_get_loader() {
		return $this->rtwwwap_rtwwwap_loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function rtwwwap_get_version() {
		return $this->rtwwwap_version;
	}
}

