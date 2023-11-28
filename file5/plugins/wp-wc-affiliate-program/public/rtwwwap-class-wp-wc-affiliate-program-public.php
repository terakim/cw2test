<?php

use League\Csv\Reader;
use League\Csv\Writer;

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.redefiningtheweb.com
 * @since      1.0.0
 *
 * @package    Rtwwwap_Wp_Wc_Affiliate_Program
 * @subpackage Rtwwwap_Wp_Wc_Affiliate_Program/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Rtwwwap_Wp_Wc_Affiliate_Program
 * @subpackage Rtwwwap_Wp_Wc_Affiliate_Program/public
 * @author     RedefiningTheWeb <developer@redefiningtheweb.com>
 */


class Rtwwwap_Wp_Wc_Affiliate_Program_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $rtwwwap_plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $rtwwwap_version;


	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $rtwwwap_plugin_name       The name of the plugin.
	 * @param      string    $rtwwwap_version    The version of this plugin.
	 */
	public function __construct( $rtwwwap_plugin_name, $rtwwwap_version ) {

		$rtwwwap_verification_done = get_option( 'rtwwwap_verification_done', array() );
		$rtwwwap_verification_done_status = isset($rtwwwap_verification_done['status']) ? $rtwwwap_verification_done['status'] : false;
		$rtwwwap_verification_done_purchase = isset($rtwwwap_verification_done['purchase_code']) ? $rtwwwap_verification_done['purchase_code'] : false;
		if( empty( $rtwwwap_verification_done ) || $rtwwwap_verification_done_status == false || empty($rtwwwap_verification_done_purchase) )
		{
			return;
		}
		$this->rtwwwap_plugin_name 	= $rtwwwap_plugin_name;
		$this->rtwwwap_version 		= $rtwwwap_version;

		add_shortcode( 'rtwwwap_affiliate_page', array( $this, 'rtwwwap_affiliate_page_callback') );

		add_shortcode( 'rtwwwap_aff_reg_page', array( $this, 'rtwwwap_aff_reg_page_callback') );

		add_shortcode( 'rtwwwap_aff_login_page', array( $this, 'rtwwwap_aff_login_page_callback') );

		// session_start();

		//add_shortcode( 'rtwwwap_aff_reset_password', array( $this, 'rtwwwap_aff_reset_password_page_callback') );
		if(isset($_GET['affiliate_csv_download']) && !empty($_GET['affiliate_csv_download']))
		{
			add_action('init', array($this, 'rtwwwap_download_csv_from_filname'));
		}
	}

// add template


function rtwwwap_download_csv_from_filname()
{
	header('Content-Type: text/csv; charset=UTF-8');
	header('Content-Description: File Transfer');
	header('Content-Disposition: attachment; filename="'.$_GET['affiliate_csv_download'].'"');
	
	require_once( WP_PLUGIN_DIR."/wp-wc-affiliate-program/third_party/csv-9.8.0/autoload.php");
	$reader = Reader::createFromPath(RTWWWAP_DIR.'assets/csv/'.$_GET['affiliate_csv_download'], 'r');
	$reader->output();
	die;
}


function rtwwwap_add_template_to_select($post_templates, $wp_theme, $post, $post_type)
	{
		
		$post_templates['template.php'] = esc_html__('Affiliate Template',"rtwwwap-wp-wc-affiliate-program");
		return $post_templates;
	}
	
	function rtwwwap_include_temp3($rtwwwap_template )
	{
		$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );

				$rtwwwap_user_id 			= get_current_user_id();

				$rtwwwap_ask_aff_approval 	= isset( $rtwwwap_extra_features[ 'aff_verify' ] ) ? $rtwwwap_extra_features[ 'aff_verify' ] : 0;
				$rtwwwap_is_aff_approved 	= ( $rtwwwap_ask_aff_approval ) ? get_user_meta( $rtwwwap_user_id, 'rtwwwap_aff_approved', true ) : 1;
				$rtwwwap_is_affiliate 		= get_user_meta( $rtwwwap_user_id, 'rtwwwap_affiliate', true );

		$rtwwwap_affiliate_page_id = get_option('rtwwwap_affiliate_page_id');
		$rtwwwap_cuttent_page_id = get_the_ID();
		$rtwwwap_affilaite_template = isset($rtwwwap_extra_features[ 'affiliate_page_template' ]) ? $rtwwwap_extra_features[ 'affiliate_page_template' ] : 1 ;
			if(	($rtwwwap_cuttent_page_id == $rtwwwap_affiliate_page_id ) && is_user_logged_in() && $rtwwwap_is_affiliate && ($rtwwwap_is_aff_approved))
			{
				$rtwwwap_template = RTWWWAP_DIR.'assets/template/template.php';	
			}
		
		return $rtwwwap_template;
		
}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function rtwwwap_enqueue_styles() {
	

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Rtwwwap_Wp_Wc_Affiliate_Program_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rtwwwap_Wp_Wc_Affiliate_Program_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );

		$rtwwwap_affilaite_template = isset($rtwwwap_extra_features[ 'affiliate_page_template' ]) ? $rtwwwap_extra_features[ 'affiliate_page_template' ] : 1 ;

		if($rtwwwap_affilaite_template == 1)
		{
			wp_enqueue_style( $this->rtwwwap_plugin_name, plugin_dir_url( __FILE__ ) . 'css/rtwwwap-wp-wc-affiliate-program-public.css', array(), $this->rtwwwap_version, 'all' );
			wp_enqueue_style( "datatable", RTWWWAP_URL. '/assets/Datatables/css/jquery.dataTables.min.css', array(), $this->rtwwwap_version, 'all' );

		}
		elseif($rtwwwap_affilaite_template == 2)
		{
			wp_enqueue_style( $this->rtwwwap_plugin_name, plugin_dir_url( __FILE__ ) . 'css/rtwwwap-wp-wc-affiliate-program-temp-2.css', array(), $this->rtwwwap_version, 'all' );
			wp_enqueue_style( "datatable", RTWWWAP_URL. '/assets/Datatables/css/jquery.dataTables.min.css', array(), $this->rtwwwap_version, 'all' );

		}
		elseif($rtwwwap_affilaite_template == 3)
		{
			global $wp_query ;
				
			$rtwwwap_current_page_id = $wp_query->get_queried_object_id() ; 
			
			$rtwwwap_affiliate_page_id = get_option('rtwwwap_affiliate_page_id');

			// echo '<pre>';
			// print_r($rtwwwap_current_page_id);
			// echo '</pre>/////';
			// var_dump($rtwwwap_affiliate_page_id);
			// die("dgvfchb");
	
			// if( $rtwwwap_current_page_id == $rtwwwap_affiliate_page_id )
			// {
				wp_enqueue_style( "material_bundle_css",plugin_dir_url( __FILE__ ) .'/css/temp3/bundle.css', array(), $this->rtwwwap_version, 'all' );

				
				$rtwwwap_user_id 			= get_current_user_id();
				$rtwwwap_theme = get_user_meta( $rtwwwap_user_id, 'rtwwwap_theme', true );

				if($rtwwwap_theme == "dark")
				{
					wp_enqueue_style( "custom_temp3_css",plugin_dir_url( __FILE__ ) .'/css/custome.css', array(), $this->rtwwwap_version, 'all' );
				}
				else {
					wp_enqueue_style( "custom_temp3_css",plugin_dir_url( __FILE__ ) .'/css/custome-light.css', array(), $this->rtwwwap_version, 'all' );
					
				}
				wp_enqueue_style("$this->rtwwwap_plugin_name", plugin_dir_url( __FILE__ ) . 'css/rtwwwap-wp-wc-affiliate-public-template3.css', array(), $this->rtwwwap_version, 'all' );
				
				wp_enqueue_style('material_min_css', 'https://cdnjs.cloudflare.com/ajax/libs/material-design-lite/1.1.0/material.min.css');


				wp_enqueue_style('datatable_material_min_css', 'https://cdn.datatables.net/1.10.20/css/dataTables.material.min.css');
				
		
				wp_enqueue_style('material_icons_min_css', 'https://cdnjs.cloudflare.com/ajax/libs/material-design-icons/3.0.1/iconfont/material-icons.min.css
				');
			// }
		
		}
		


		wp_enqueue_style( "select2", RTWWWAP_URL. '/assets/Datatables/css/rtwwwap-wp-select2.min.css', array(), $this->rtwwwap_version, 'all' );
		$rtwwwap_verification_done = get_option( 'rtwwwap_verification_done', array() );
		$rtwwwap_verification_done_status = isset($rtwwwap_verification_done['status']) ? $rtwwwap_verification_done['status'] : false;
		$rtwwwap_verification_done_purchase = isset($rtwwwap_verification_done['purchase_code']) ? $rtwwwap_verification_done['purchase_code'] : false;
		if( empty( $rtwwwap_verification_done ) || $rtwwwap_verification_done_status == false || empty($rtwwwap_verification_done_purchase) )
		{
			return;
		}
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( "modal_css", plugin_dir_url( __FILE__ ) . 'css/jquery.modal.css', array(), $this->rtwwwap_version, 'all' );

		
		
		wp_enqueue_style( "orgchart_css", RTWWWAP_URL. '/assets/orgChart/jquery.orgchart.css', array(), $this->rtwwwap_version, 'all' );
		wp_enqueue_style('font-awesome_css', 'https://pro.fontawesome.com/releases/v5.1.0/css/all.css');
		
	
		
		

	}

	function rtwwwap_control_style()
	{

		global $wp_styles;
		global $wp_query ;
	
		$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );
			$rtwwwap_affilaite_template = isset($rtwwwap_extra_features[ 'affiliate_page_template' ]) ? $rtwwwap_extra_features[ 'affiliate_page_template' ] : 1 ;
			$rtwwwap_current_page_id = $wp_query->get_queried_object_id() ; 
			
			$rtwwwap_affiliate_page_id = get_option('rtwwwap_affiliate_page_id');
	
			if($rtwwwap_affilaite_template == 3 && $rtwwwap_current_page_id == $rtwwwap_affiliate_page_id )
			{
				foreach( $wp_styles->queue as $style ):
					
			
					$handle = $wp_styles->registered[$style]->handle;
			$rtwwwap_css_exception = array(
			
				"rtwwwap-wp-wc-affiliate-program",
				"custom_temp3_css",
				"modal_css",
				"wp-color-picker",
				"orgchart_css",
				"font-awesome_css",
				"material_min_css",
				"datatable_material_min_css",
			
				"material_icons_min_css",	
				"material_bundle_css",	
			);
		
			$rtwwwap_css_exception = apply_filters("rtwwwap_include_css",$rtwwwap_css_exception ); 
	
			if( !in_array( $handle, $rtwwwap_css_exception ) ){
				wp_dequeue_style( $handle );
				wp_deregister_style( $handle );
			}
			
		endforeach;
		
	   }

	}


/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function rtwwwap_enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Rtwwwap_Wp_Wc_Affiliate_Program_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rtwwwap_Wp_Wc_Affiliate_Program_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );
		$rtwwwap_affilaite_template = isset($rtwwwap_extra_features[ 'affiliate_page_template' ]) ? $rtwwwap_extra_features[ 'affiliate_page_template' ] : 1 ;
	
		wp_enqueue_script( "select2", RTWWWAP_URL. '/assets/Datatables/js/rtwwwap-wp-select2.min.js', array( 'jquery' ), $this->rtwwwap_version, true );

		if($rtwwwap_affilaite_template == 3)
		{	
			global $wp_query ;		
			$rtwwwap_current_page_id = $wp_query->get_queried_object_id() ; 
			
			$rtwwwap_affiliate_page_id = get_option('rtwwwap_affiliate_page_id');
	
			// if( $rtwwwap_current_page_id == $rtwwwap_affiliate_page_id )
			// {

				wp_enqueue_script( $this->rtwwwap_plugin_name, plugin_dir_url( __FILE__ ) . 'js/rtwwwap-wp-wc-affiliate-program-temp3.js', array( 'jquery', 'jquery-ui-accordion','select2'), $this->rtwwwap_version, true );

				wp_enqueue_script( "jquery_datatable", 'https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js', array( 'jquery', 'jquery-ui-accordion' ), $this->rtwwwap_version, true );

				wp_enqueue_script( "material_datatable", 'https://cdn.datatables.net/1.10.20/js/dataTables.material.min.js',array( 'jquery', 'jquery-ui-accordion' ), $this->rtwwwap_version, true );
				
				wp_enqueue_script( "nice_scrol", 'https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js', array( 'jquery', 'jquery-ui-accordion' ), $this->rtwwwap_version, true );
				
				wp_enqueue_script('chart-js', plugin_dir_url( __FILE__ ) . 'js/temp3/Chart.min.js', array( 'jquery'), $this->rtwwwap_version, true );


				wp_enqueue_script( "notify_js", RTWWWAP_URL. '/assets/notify.min.js', array( 'jquery' ), $this->rtwwwap_version, false );
				
				wp_enqueue_script('animejs','https://cdnjs.cloudflare.com/ajax/libs/animejs/2.0.2/anime.min.js');
			
				wp_enqueue_script('apexchartjs','https://cdnjs.cloudflare.com/ajax/libs/apexcharts/2.4.0/apexcharts.min.js');
			// }
			
		}
		else{
			wp_register_script( $this->rtwwwap_plugin_name, plugin_dir_url( __FILE__ ) . 'js/rtwwwap-wp-wc-affiliate-program-public.js', array( 'jquery', 'jquery-ui-accordion' ), $this->rtwwwap_version, true );
			wp_enqueue_script( "jquery_datatable", 'https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js', array( 'jquery', 'jquery-ui-accordion' ), $this->rtwwwap_version, true );
			wp_enqueue_script( "datatable", RTWWWAP_URL. '/assets/Datatables/js/jquery.dataTables.min.js', array( 'jquery' ), $this->rtwwwap_version, false );
		
		}
		wp_enqueue_script( 'iris', admin_url( 'js/iris.min.js' ), array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), $this->rtwwwap_version, true );

		wp_enqueue_script( "blockUI", RTWWWAP_URL. '/assets/Datatables/js/rtwwwap-wp-blockui.js', array( 'jquery' ), $this->rtwwwap_version, false );
		
	
		// wp_enqueue_script( 'wp-color-picker', admin_url( 'js/color-picker.min.js' ), array( 'iris' ), $this->rtwwwap_version, true );

		$rtwwwap_colorpicker_l10n = array(
	        'clear' 		=> esc_html__( 'Clear' ),
	        'defaultString' => esc_html__( 'Default' ),
	        'pick' 			=> esc_html__( 'Select Color' ),
	        'current' 		=> esc_html__( 'Current Color' )
	    );
		$rtwwwap_verification_done = get_option( 'rtwwwap_verification_done', array() );
		$rtwwwap_verification_done_status = isset($rtwwwap_verification_done['status']) ? $rtwwwap_verification_done['status'] : false;
		$rtwwwap_verification_done_purchase = isset($rtwwwap_verification_done['purchase_code']) ? $rtwwwap_verification_done['purchase_code'] : false;
		if( empty( $rtwwwap_verification_done ) || $rtwwwap_verification_done_status == false || empty($rtwwwap_verification_done_purchase) )
		{
			return;
		}
	  	wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $rtwwwap_colorpicker_l10n );

		//for model		
		
		$rtwwwap_ajax_nonce 		= wp_create_nonce( "rtwwwap-ajax-security-string" );
		$rtwwwap_whatsapp_device 	= esc_url( 'https://web.whatsapp.com/send?text=' );
		if( wp_is_mobile() ){
			$rtwwwap_whatsapp_device= 'whatsapp://send?text=';
		}
		$rtwwwap_translation_array 	= array(
										'rtwwwap_ajaxurl' 		=> esc_url(admin_url( 'admin-ajax.php' )),
										'rtwwwap_nonce' 		=> $rtwwwap_ajax_nonce,

										'rtwwwap_copy_script' 	=> esc_html__( 'Copy Script', 'rtwwwap-wp-wc-affiliate-program' ),
										'rtwwwap_copy_html' 	=> esc_html__( 'Copy Html', 'rtwwwap-wp-wc-affiliate-program' ),
										'rtwwwap_buy_now' 		=> esc_html__( 'Buy Now', 'rtwwwap-wp-wc-affiliate-program' ),
										'rtwwwap_preview' 		=> esc_html__( 'Preview', 'rtwwwap-wp-wc-affiliate-program' ),
										'rtwwwap_list_price' 	=> esc_html__( 'List Price', 'rtwwwap-wp-wc-affiliate-program' ),
										'rtwwwap_our_price' 	=> esc_html__( 'Our Price', 'rtwwwap-wp-wc-affiliate-program' ),
										'rtwwwap_text_color' 	=> esc_html__( 'Text Color', 'rtwwwap-wp-wc-affiliate-program' ),
										'rtwwwap_link_color' 	=> esc_html__( 'Link Color', 'rtwwwap-wp-wc-affiliate-program' ),
										'rtwwwap_background_color' => esc_html__( 'Background Color', 'rtwwwap-wp-wc-affiliate-program' ),
										'rtwwwap_show_price' 	=> esc_html__( 'Show Price', 'rtwwwap-wp-wc-affiliate-program' ),
										'rtwwwap_border_color' 	=> esc_html__( 'Border Color', 'rtwwwap-wp-wc-affiliate-program' ),
										'rtwwwap_home_url' 		=> esc_url( home_url() ),
										'rtwwwap_enter_valid_url' => esc_html__( 'Enter valid Link', 'rtwwwap-wp-wc-affiliate-program' ),
										'rtwwwap_twitter_url' 	=> esc_url( 'https://twitter.com/intent/tweet?text=' ),
										'rtwwwap_mail_url' 		=> esc_url( 'mailto:enteryour@addresshere.com?subject=Click on this link &body=Check%20this%20out: ' ),
										'rtwwwap_fb_url' 		=> esc_url( 'https://www.facebook.com/sharer/sharer.php?u=' ),
										'rtwwwap_whatsapp_url' 	=> $rtwwwap_whatsapp_device,
										'rtwwwap_valid_coupon_less_msg' => esc_html__( 'Coupon amount must be greater than', 'rtwwwap-wp-wc-affiliate-program' ),
										'rtwwwap_valid_coupon_more_msg' => esc_html__( 'Coupon amount must be less than', 'rtwwwap-wp-wc-affiliate-program' ),
										'rtwwwap_copied' 		=> esc_html__( 'Copied', 'rtwwwap-wp-wc-affiliate-program' ),
										'rtwwwap_mlm_user_activate' 	=> esc_html__( 'Activate', 'rtwwwap-wp-wc-affiliate-program' ),
										'rtwwwap_mlm_user_deactivate' 	=> esc_html__( 'Deactivate', 'rtwwwap-wp-wc-affiliate-program' ),
										'rtwwwap_disabled' 	=> esc_html__( 'Disabled', 'rtwwwap-wp-wc-affiliate-program' ),
										'rtwwwap_enabled' 	=> esc_html__( 'Enabled', 'rtwwwap-wp-wc-affiliate-program' ),
										'rtwwwap_parent' 	=> esc_html__( 'Parent', 'rtwwwap-wp-wc-affiliate-program' ),
										'rtwwwap_rqst_sure' => esc_html__( 'Are you sure to send the request?', 'rtwwwap-wp-wc-affiliate-program' ),
										'rtwwwap_login_field_missing_msg' => esc_html__( '* Fill the Required Details', 'rtwwwap-wp-wc-affiliate-program' ),
										'rtwwwap_register_error_msg' => esc_html__( '* Fill the required Details', 'rtwwwap-wp-wc-affiliate-program' ),
										'rtwwwap_email_validation' => esc_html__( 'Enter Valid Email Address', 'rtwwwap-wp-wc-affiliate-program' ),
										'rtwwwap_password_length' => esc_html__( 'Password should be more than 6 characters', 'rtwwwap-wp-wc-affiliate-program' ),
										'rtwwwap_strong_password_err' => esc_html__( "To make Strong password also Use Special characters & numbers in your password", 'rtwwwap-wp-wc-affiliate-program' ),
										'rtwwwap_add_rqst_msg' => esc_html__( 'Please write a message', 'rtwwwap-wp-wc-affiliate-program' ),
										'rtwwwap_confirm_password' => esc_html__( 'Password Not Matched', 'rtwwwap-wp-wc-affiliate-program' ),
										'rtwwwap_chart' => $this->rtwwwap_get_chart_data(),
										'rtwwwap_dashboard_report_line_chart' => $this->rtwwwap_dash_line_chart(),
										'rtwwwap_report_chart' => $this->rtwwwap_get_report_chart_data(),
										'rtwwwap_report_chart_device' => $this->rtwwwap_get_report_chart_device_data(),
										'rtwwwap_error_set_payment_method' => esc_html__( 'Please Set Payment Method First', 'rtwwwap-wp-wc-affiliate-program' ),
										'rtwwwap_error_with_amount' => esc_html__( 'Please input valid number', 'rtwwwap-wp-wc-affiliate-program' ),
									);
		wp_localize_script( $this->rtwwwap_plugin_name, 'rtwwwap_global_params', $rtwwwap_translation_array );
		wp_enqueue_script( $this->rtwwwap_plugin_name );

		wp_enqueue_script( "qrcode", RTWWWAP_URL. '/assets/QrCodeJs/qrcode.min.js', array( 'jquery' ), $this->rtwwwap_version, false );
		wp_enqueue_script( "jquery.nicescroll-master", RTWWWAP_URL. '/assets/jquery.nicescroll-master/jquery.nicescroll.js', array( 'jquery' ), $this->rtwwwap_version, false );

		wp_enqueue_script( "orgchart", RTWWWAP_URL. '/assets/orgChart/jquery.orgchart.js', array( 'jquery' ), $this->rtwwwap_version, false );
		wp_register_script( 'FontAwesome', 'https://use.fontawesome.com/releases/v5.0.2/js/all.js', null, null, true );
		
		if($rtwwwap_affilaite_template == 3)
		{
			wp_enqueue_script( "maretial_bundle", plugin_dir_url( __FILE__ ) . 'js/temp3/bundle.js', array( 'jquery' ), $this->rtwwwap_version, true);
		}
	}

	function rtwwwap_controll_js()
	{
		global $wp_scripts;
		global $wp_query ;

		$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );
		$rtwwwap_affilaite_template = isset($rtwwwap_extra_features[ 'affiliate_page_template' ]) ? $rtwwwap_extra_features[ 'affiliate_page_template' ] : 1 ;

		$rtwwwap_current_page_id = $wp_query->get_queried_object_id() ; 
		
		$rtwwwap_affiliate_page_id = get_option('rtwwwap_affiliate_page_id');

		if($rtwwwap_affilaite_template == 3 && $rtwwwap_current_page_id == $rtwwwap_affiliate_page_id )
		{
				
		foreach( $wp_scripts->queue as $style ):
			$handle = $wp_scripts->registered[$style]->handle;
			$rtwwwap_js_exception = array(
				"select2",
				"iris",
				"blockUI",
				"nice_scrol",
				"chart-js",
				"jquery",
				"jquery-ui-accordion",
				"rtwwwap-wp-wc-affiliate-program",
				"material_jquery",
				"jquery_datatable",
				"material_datatable", 
				"animejs",
				"rtwwwap-modal",
				"wp-color-picker",
				"orgchart",
				"FontAwesome",
				"rtwwwap_global_params",
				// "kit_fontawesome",
				"maretial_bundle", 
				"notify_js",
			);
		
			$rtwwwap_js_exception = apply_filters("rtwwwap_include_js",$rtwwwap_js_exception ); 

			if( !in_array( $handle, $rtwwwap_js_exception ) ){
				wp_dequeue_script( $handle );
				wp_deregister_script( $handle );
			}
			
		endforeach;
		
	}
	}

	function rtwwwap_dash_line_chart()
	{

		global $wpdb;
		$rtwwwap_user_id = get_current_user_id();
		$rtwwwap_total_order = $wpdb->get_results( $wpdb->prepare( "SELECT COUNT(`id`) as total_order, DATE(date) as date_wise FROM `".$wpdb->prefix."rtwwwap_referrals` WHERE `aff_id`=%d GROUP BY DATE(date_wise) ORDER BY `date` DESC", $rtwwwap_user_id ),ARRAY_A );

		$rtwwwap_total_referral_commission = $wpdb->get_results( $wpdb->prepare( "SELECT SUM(amount) as commission , DATE(date) as date_wise FROM `".$wpdb->prefix."rtwwwap_referrals` WHERE `aff_id`=%d AND `type`= 0 AND `status`= %d OR `status`= %d  GROUP BY DATE(date_wise)"  , $rtwwwap_user_id, 1,2 ),ARRAY_A );

		$rtwwwap_total_manual_commission = $wpdb->get_results( $wpdb->prepare( "SELECT SUM(amount) as commission , DATE(date) as date_wise FROM `".$wpdb->prefix."rtwwwap_referrals` WHERE `aff_id`=%d AND `type`= 6 AND `status`= %d OR `status`= %d  GROUP BY DATE(date_wise)"  , $rtwwwap_user_id, 1,2 ),ARRAY_A );

		$rtwwwap_total_mlm_commission = $wpdb->get_results( $wpdb->prepare( "SELECT SUM(amount) as commission , DATE(date) as date_wise FROM `".$wpdb->prefix."rtwwwap_referrals` WHERE `aff_id`=%d AND `type`= 4 AND `status`= %d OR `status`= %d  GROUP BY DATE(date_wise)"  , $rtwwwap_user_id, 1,2 ),ARRAY_A );

		$rtwwwap_total_signup_commission = $wpdb->get_results( $wpdb->prepare( "SELECT SUM(amount) as commission , DATE(date) as date_wise FROM `".$wpdb->prefix."rtwwwap_referrals` WHERE `aff_id`=%d AND `type`= 1 AND `status`= %d OR `status`= %d  GROUP BY DATE(date_wise)"  , $rtwwwap_user_id, 1,2 ),ARRAY_A );

		$rtwwwap_total_performance_commission = $wpdb->get_results( $wpdb->prepare( "SELECT SUM(amount) as commission , DATE(date) as date_wise FROM `".$wpdb->prefix."rtwwwap_referrals` WHERE `aff_id`=%d AND `type`= 2 AND `status`= %d OR `status`= %d  GROUP BY DATE(date_wise)"  , $rtwwwap_user_id, 1,2 ),ARRAY_A );

		$final_commission_array = array();

		foreach($rtwwwap_total_referral_commission as $ref_key => $ref_value )
		{
			if(array_key_exists($ref_value['date_wise'],$final_commission_array))
			{
				$final_commission_array[$ref_value['date_wise']]['total_commission'] = $ref_value['commission'];
			}
			else{
				$final_commission_array[$ref_value['date_wise']]['total_commission'] = $ref_value['commission'];
			}
		}

		foreach($rtwwwap_total_manual_commission as $ref_key => $ref_value )
		{
			if(array_key_exists($ref_value['date_wise'],$final_commission_array))
			{
				$com = $final_commission_array[$ref_value['date_wise']]['total_commission'];
				$final_commission_array[$ref_value['date_wise']]['total_commission'] = $com + $ref_value['commission'];
			}
			else{
				$final_commission_array[$ref_value['date_wise']]['total_commission'] = $ref_value['commission'];
			}
		}

		foreach($rtwwwap_total_mlm_commission as $ref_key => $ref_value )
		{
			if(array_key_exists($ref_value['date_wise'],$final_commission_array))
			{
				$com = $final_commission_array[$ref_value['date_wise']]['total_commission'];
				$final_commission_array[$ref_value['date_wise']]['total_commission'] = $com + $ref_value['commission'];
			}
			else{
				$final_commission_array[$ref_value['date_wise']]['total_commission'] = $ref_value['commission'];
			}
		}

		foreach($rtwwwap_total_signup_commission as $ref_key => $ref_value )
		{
			if(array_key_exists($ref_value['date_wise'],$final_commission_array))
			{
				$com = $final_commission_array[$ref_value['date_wise']]['total_commission'];
				$final_commission_array[$ref_value['date_wise']]['total_commission'] = $com + $ref_value['commission'];
			}
			else{
				$final_commission_array[$ref_value['date_wise']]['total_commission'] = $ref_value['commission'];
			}
		}

		foreach($rtwwwap_total_performance_commission as $ref_key => $ref_value )
		{
			if(array_key_exists($ref_value['date_wise'],$final_commission_array))
			{
				$com = $final_commission_array[$ref_value['date_wise']]['total_commission'];
				$final_commission_array[$ref_value['date_wise']]['total_commission'] = $com + $ref_value['commission'];
			}
			else{
				$final_commission_array[$ref_value['date_wise']]['total_commission'] = $ref_value['commission'];
			}
		}

		$rtwwwap_order_ids = $wpdb->get_results( $wpdb->prepare( "SELECT `order_id` as order_wise, DATE(date) as date_wise  FROM `".$wpdb->prefix."rtwwwap_referrals` WHERE `aff_id`=%d "  , $rtwwwap_user_id ),ARRAY_A );

		$rtwwwap_order_id = array();
		


		foreach($rtwwwap_order_ids as $key => $value) {
			if(array_key_exists($value['date_wise'],$rtwwwap_order_id))
			{
				if($value["order_wise"] > 0)
				{
					$rtwwwap_product_details = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `order_id`= '%d' AND (`status` = 1 OR `status` = 2)  AND `type` = 0 ", $value["order_wise"] ) );
						if(!empty($rtwwwap_product_details) )
						{
							$rtwwwap_product_detail =  json_decode($rtwwwap_product_details[0]->product_details,true);
							$rtwwwap_product = wc_get_product($rtwwwap_product_detail[0]['product_id']);
							$rtwwwap_product_price = $rtwwwap_product_detail[0]['product_price'];
							$rtwwwap_order_id[$value["date_wise"]][$value["order_wise"]] = $rtwwwap_product_price;
						}
				}
			}
			else {
				if($value["order_wise"] > 0)
				{
				$rtwwwap_product_details = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `order_id`= '%d' AND DATE(date) = '%s' AND `aff_id` = '%d'  AND (`status` = 1 OR `status` = 2)  AND `type` = 0  ", $value["order_wise"],$value['date_wise'],$rtwwwap_user_id));	
					if(!empty($rtwwwap_product_details))
					{
					$rtwwwap_product_detail =  json_decode($rtwwwap_product_details[0]->product_details,true);
					$rtwwwap_product = wc_get_product($rtwwwap_product_detail[0]['product_id']);
					$rtwwwap_product_price = $rtwwwap_product_detail[0]['product_price'];
					$rtwwwap_order_id[$value["date_wise"]][$value["order_wise"]] =  $rtwwwap_product_price ;
					}
				}
				
			}
		
		}


		$rtwwwap_total_sales = array();

		foreach($rtwwwap_order_id as $key => $value)
		{
			$total= 0;
			foreach($value as $key1 => $value1)
			{
				$total +=  $value1;
			}
				$rtwwwap_total_sales[ $key] = $total; 
		}

		foreach($final_commission_array as $key => $value)
		{
			if(array_key_exists($key,$rtwwwap_total_sales))
			{
				$final_commission_array[$key]['total_prod_price'] = $rtwwwap_total_sales[$key];
			}
			else
			{
				$final_commission_array[$key]['total_prod_price'] = 0;
			}
		}

		foreach($rtwwwap_total_order as $key => $value)
		{
			if(array_key_exists($value['date_wise'],$final_commission_array))
			{
				$final_commission_array[$value['date_wise']]['total_order'] = $value['total_order'];
			}
		}


		$rtwwap_chart_data = array();
		foreach($final_commission_array as $key => $value)
		{
			$rtwwap_chart_data['dates'][] = $key;
		}

		foreach($final_commission_array as $key => $value)
		{
			$rtwwap_chart_data['commission'][] = $value['total_commission'];
		}
		foreach($final_commission_array as $key => $value)
		{
			$rtwwap_chart_data['product_price'][] = $value['total_prod_price'];
		}
		foreach($final_commission_array as $key => $value)
		{
			$rtwwap_chart_data['orders'][] = isset($value['total_order']) ? $value['total_order'] : 0;
		}

		return $rtwwap_chart_data;

	}


	function rtwwwap_get_chart_data()
	{
		global $wpdb;
		$rtwwwap_user_id 			= get_current_user_id();
		$rtwwwap_total_referrals 	= $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(`id`) as total_referrals FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `aff_id`=%d", $rtwwwap_user_id ) );
		$rtwwwap_pending_comm 		= $wpdb->get_var( $wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `aff_id`=%d AND `status`=%d AND `capped`!=%d", $rtwwwap_user_id, 0, 1 ) );
		$rtwwwap_approved_comm 		= $wpdb->get_var( $wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `aff_id`=%d AND `status`=%d", $rtwwwap_user_id, 1 ) );
		$rtwwwap_total_comm 		= $wpdb->get_var( $wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `aff_id`=%d AND `status`=%d", $rtwwwap_user_id, 2 ) );
		$rtwwwap_rejected_comm 		= $wpdb->get_var( $wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `aff_id`=%d AND `status`=%d", $rtwwwap_user_id, 3 ) );

		$rtwwwap_total_comm 		= $rtwwwap_total_comm+$rtwwwap_approved_comm;
		$rtwwwap_wallet 			= get_user_meta( $rtwwwap_user_id, 'rtw_user_wallet', true );
		$rtwwwap_wallet   			= isset($rtwwwap_wallet) ? $rtwwwap_wallet : '0';

		$rtwwwap_all_comm 		= $wpdb->get_var( $wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `aff_id`=%d AND `capped`!=%d", $rtwwwap_user_id, 1 ) );
	
		$array = array(
			"title" => [esc_html__( 'Total + Pending', 'rtwwwap-wp-wc-affiliate-program' ),
						esc_html__( 'Total commission', 'rtwwwap-wp-wc-affiliate-program' ),
						esc_html__( 'WALLET', 'rtwwwap-wp-wc-affiliate-program' ),
						esc_html__( 'APPROVED COMMISSION', 'rtwwwap-wp-wc-affiliate-program' ),
						esc_html__( 'PENDING COMMISSION', 'rtwwwap-wp-wc-affiliate-program' ),
						esc_html__( 'REJECTED COMMISSION', 'rtwwwap-wp-wc-affiliate-program' ),

						],
			"data" =>  [$rtwwwap_all_comm,$rtwwwap_total_comm ,$rtwwwap_wallet  ,$rtwwwap_approved_comm,$rtwwwap_pending_comm,$rtwwwap_rejected_comm],
		);

		return $array;
	}

	/// report chart data
	function rtwwwap_get_report_chart_device_data()
	{
		global $wpdb;
		$rtwwwap_user_id 			= get_current_user_id();

		$rtwwwap_desktop_count	=  (int)$wpdb->get_var($wpdb->prepare("SELECT Count(`id`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `device`= 'desktop' AND `aff_id` = %d AND `type` = 0 ",$rtwwwap_user_id));
		$rtwwwap_other 	= (int)$wpdb->get_var($wpdb->prepare("SELECT Count(`id`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `device`!= 'desktop' AND `aff_id` = %d AND `type` = 0",$rtwwwap_user_id));
	

			
		
		$array = array(
			"title" => [esc_html__( 'Desktop', 'rtwwwap-wp-wc-affiliate-program' ),
						esc_html__( 'Other', 'rtwwwap-wp-wc-affiliate-program' ),			
						],
			"data" =>  [$rtwwwap_desktop_count,$rtwwwap_other],
		);
		return $array;

	}


	function rtwwwap_get_report_chart_data()
	{
		global $wpdb;
		$rtwwwap_user_id 			= get_current_user_id();

		$rtwwwap_total_click 	=  (int)$wpdb->get_var( $wpdb->prepare( "SELECT SUM(`link_open`) as link_open FROM ".$wpdb->prefix."rtwwwap_referral_link WHERE `aff_id`= %d", $rtwwwap_user_id ) );
		$rtwwwap_total_purchase 	= (int)$wpdb->get_var( $wpdb->prepare( "SELECT SUM(`link_purchase`) as link_open FROM ".$wpdb->prefix."rtwwwap_referral_link WHERE `aff_id`= %d", $rtwwwap_user_id ) );
	
		if($rtwwwap_total_click != 0)
		{
		$rtwwwap_conversion = floatval(($rtwwwap_total_purchase /$rtwwwap_total_click) * 100);
		}
		else
		{
			$rtwwwap_conversion = 0;
		}
			
		
		$array = array(
			"title" => [esc_html__( $rtwwwap_total_click.' Clicked', 'rtwwwap-wp-wc-affiliate-program' ),
						esc_html__( $rtwwwap_total_purchase.' Purchased', 'rtwwwap-wp-wc-affiliate-program' ),
						esc_html__( 'Conversion Ratio('.$rtwwwap_conversion.'%)', 'rtwwwap-wp-wc-affiliate-program' ),

					
						],
			"data" =>  [$rtwwwap_total_click,$rtwwwap_total_purchase,$rtwwwap_conversion],
		);
		return $array;
	}


	/*
	* function to show under WooCommerce Account
	*/
	function rtwwwap_add_account_menu_item_endpoint(){

		$rtwwwap_verification_done = get_option( 'rtwwwap_verification_done', array() );
		$rtwwwap_verification_done_status = isset($rtwwwap_verification_done['status']) ? $rtwwwap_verification_done['status'] : false;
		$rtwwwap_verification_done_purchase = isset($rtwwwap_verification_done['purchase_code']) ? $rtwwwap_verification_done['purchase_code'] : false;
		if( empty( $rtwwwap_verification_done ) || $rtwwwap_verification_done_status == false || empty($rtwwwap_verification_done_purchase) )
		{
			return;
		}
		add_rewrite_endpoint( 'rtwwwap_affiliate_menu', EP_PAGES );
	}

	/*
	* function to show under WooCommerce Account
	*/
	function rtwwwap_add_account_menu_item( $rtwwwap_menu_links ){
		$rtwwwap_verification_done = get_option( 'rtwwwap_verification_done', array() );
		$rtwwwap_verification_done_status = isset($rtwwwap_verification_done['status']) ? $rtwwwap_verification_done['status'] : false;
		$rtwwwap_verification_done_purchase = isset($rtwwwap_verification_done['purchase_code']) ? $rtwwwap_verification_done['purchase_code'] : false;
		if( empty( $rtwwwap_verification_done ) || $rtwwwap_verification_done_status == false || empty($rtwwwap_verification_done_purchase) )
		{
			return;
		}
		$rtwwwap_new = array( 'rtwwwap_affiliate_menu' => esc_html__( 'Affiliate', 'rtwwwap-wp-wc-affiliate-program' ) );

		$rtwwwap_menu_links = array_slice( $rtwwwap_menu_links, 0, 1, true )
		+ $rtwwwap_new
		+ array_slice( $rtwwwap_menu_links, 1, NULL, true );

		return $rtwwwap_menu_links;
	}

	/*
	*
	*/
	function rtwwwap_add_account_menu_item_endpoint_content( $rtwwwap_url, $rtwwwap_endpoint ){

	
		$rtwwwap_verification_done = get_option( 'rtwwwap_verification_done', array() );
		$rtwwwap_verification_done_status = isset($rtwwwap_verification_done['status']) ? $rtwwwap_verification_done['status'] : false;
		$rtwwwap_verification_done_purchase = isset($rtwwwap_verification_done['purchase_code']) ? $rtwwwap_verification_done['purchase_code'] : false;
		if( empty( $rtwwwap_verification_done ) || $rtwwwap_verification_done_status == false || empty($rtwwwap_verification_done_purchase) )
		{
			return;
		}
		if( $rtwwwap_endpoint === 'rtwwwap_affiliate_menu' )
		{
			$rtwwwap_page_id = get_option( 'rtwwwap_affiliate_page_id' );

			if( $rtwwwap_page_id ){
				$rtwwwap_url = get_the_permalink( $rtwwwap_page_id );
				return esc_url( $rtwwwap_url.'?rtwwwap_tab=overview' );
			}
		}
		return $rtwwwap_url;
	}

/**
	 * This function is for front end user to become affiliate
	 */
	function rtwwwap_become_affiliate_callback()
	{
	
		$rtwwwap_verification_done = get_option( 'rtwwwap_verification_done', array() );
		$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );
		$rtwwwap_verification_done_status = isset($rtwwwap_verification_done['status']) ? $rtwwwap_verification_done['status'] : false;
		$rtwwwap_verification_done_purchase = isset($rtwwwap_verification_done['purchase_code']) ? $rtwwwap_verification_done['purchase_code'] : false;
		if( empty( $rtwwwap_verification_done ) || $rtwwwap_verification_done_status == false || empty($rtwwwap_verification_done_purchase) )
		{
			return;
		}
		$rtwwwap_check_ajax = check_ajax_referer( 'rtwwwap-ajax-security-string', 'rtwwwap_security_check' );
		$rtwwwap_membership_plan = isset($rtwwwap_extra_features['rtwwwap_active_membership']) ? $rtwwwap_extra_features['rtwwwap_active_membership']: 0;

		
		if ( $rtwwwap_check_ajax ) {

			// update code starts here
			
			$rtwwwap_randomString = $this->rtwwwap_generate_custom_code(6);
			$rtwwwap_user_id = get_current_user_id();
			update_user_meta( $rtwwwap_user_id, 'rtwwwap_referee_custom_str', $rtwwwap_randomString );

			// ends here
			
			if($rtwwwap_membership_plan == 1)
			{
				$rtwwwap_info =	$this->rtwwwap_process_payment($rtwwwap_extra_features);
				$rtwwwap_updated = 1;
				$rtwwwap_message = esc_html__( 'paypal payment', 'rtwwwap-wp-wc-affiliate-program'  ) ;
				 
				
			}
			elseif($rtwwwap_membership_plan == 0){
				
				$rtwwwap_updated = $this->rtwwwap_become_affiliate();
				$rtwwwap_info = '';
				$rtwwwap_mlm = get_option( 'rtwwwap_mlm_opt' );
				$rtwwwap_allowed_childs = isset( $rtwwwap_mlm[ 'child' ] ) ? $rtwwwap_mlm[ 'child' ] : 1;
				if( $rtwwwap_updated == 'restrict_aff' ){
					$rtwwwap_message = esc_html__( "You are already refer $rtwwwap_allowed_childs members", 'rtwwwap-wp-wc-affiliate-program' );
					$rtwwwap_info = 1;
				}
				else if( $rtwwwap_updated ){

					$rtwwwap_message = esc_html__( 'You are now an affiliate', 'rtwwwap-wp-wc-affiliate-program' );
				}
				else{
					$rtwwwap_message = esc_html__( 'Something went wrong', 'rtwwwap-wp-wc-affiliate-program' );
				}

			}
			else{
				$rtwwwap_info = '';
				$rtwwwap_updated = false;
				$rtwwwap_message = esc_html__( 'membership Not setup correctly', 'rtwwwap-wp-wc-affiliate-program'  ) ;

			}

			echo json_encode( array( 'rtwwwap_status' => $rtwwwap_updated, 'rtwwwap_message' => $rtwwwap_message ,'rtwwwap_redirect' => $rtwwwap_info ,'membership' => $rtwwwap_membership_plan ) );
		die;

		
		}
	}


/*
* function to make payment through paypal
*/
	function rtwwwap_process_payment( $rtwwwap_extra_features ){


		global $wpdb;
		$rtwwwap_paypal_type 	= isset( $rtwwwap_extra_features[ 'paypal_type' ] ) ? $rtwwwap_extra_features[ 'paypal_type' ] : '';

		$rtwwwap_request_url = ($rtwwwap_paypal_type == 'sandbox') ? 'https://www.sandbox.paypal.com/cgi-bin/webscr?' : 'https://www.paypal.com/cgi-bin/webscr?';
		
		// $membership_amount  = isset( $rtwwwap_extra_features[ 'membership_amount' ] ) ? $rtwwwap_extra_features[ 'membership_amount' ] : 1 ;
		
		$rtwwwap_affiliate_page_id = get_option('rtwwwap_affiliate_page_id');
		$rtwwwap_redirect_link = get_permalink($rtwwwap_affiliate_page_id);

		$rtwwwap_membership_amount = $rtwwwap_extra_features[ 'membership_amount' ];
		$rtwwwap_client_mail = $rtwwwap_extra_features[ 'paypal_sandbox_client_eamil' ];

		$rtwwwap_url = get_option( 'rtwwwap_return_url', $rtwwwap_redirect_link );      
			
		$rtwwwap_cancel_url = add_query_arg(
			array(
			'cancel_payment' => 'true',
			'affiliate_id' => get_current_user_id(),
			'_rtwbmanonce' => wp_create_nonce( 'rtwwwap-cancel_payment' ),
			),
			$rtwwwap_url
			);
						
		$rtwwwap_return_url = add_query_arg(
		array(
		'success' => 'true',
		'affiliate_id' => get_current_user_id(),
		'_rtwwwapnonce' => wp_create_nonce( 'rtwwwap-success_payment' ),
		),
		$rtwwwap_url
		);
		
		$rtwwwap_notify_url = add_query_arg(
		array(
		'success' => 'false',
		'affiliate_id' => get_current_user_id(),
		'_rtwwwapnotify' => 'true',
		),
		$rtwwwap_url
		);
		
		$rtwwwap_get_payment = get_option('rtwwwap_payment_option', array());
		if( RTWWWAP_IS_WOO != 1 ){
			$rtwwwap_currency = $rtwwwap_extra_features['currency'];
		}
		elseif(RTWWWAP_IS_WOO == 1)
			{
			$rtwwwap_currency = get_woocommerce_currency();
			}	
		$rtwwwap_user_email = wp_get_current_user();

		$rtwwwap_query_array = array(
		'cmd' => '_xclick',
		'business' => $rtwwwap_client_mail,
		'currency_code' => isset($rtwwwap_currency)? $rtwwwap_currency : 'USD',
		'return' => $rtwwwap_return_url,
		'cancel_return' => $rtwwwap_cancel_url,
		'notify_url' => $rtwwwap_notify_url,
		'first_name' =>  $rtwwwap_user_email->user_firstname ,
		'last_name' =>  $rtwwwap_user_email->user_lastname ,
		'email' => $rtwwwap_user_email->user_email,
		'night_phone_a' => get_user_meta($rtwwwap_user_email->ID,'billing_phone',true),
		'custom' => wp_json_encode(
		array(
		'affiliate_id' => get_current_user_id()
		)
		),
		'amount' => $rtwwwap_membership_amount
		
		);
		
		return $rtwwwap_request_url . http_build_query( $rtwwwap_query_array, '', '&' );
	}


	function rtwwwap_member_redirect_successful()
	{
		if(isset($_GET['success']) && isset($_GET['affiliate_id']))
		{
			if($_GET['success'] == 'true' &&  $_GET['affiliate_id'] == get_current_user_id())
			{
				$rtwwwap_user_id 	= $_GET['affiliate_id'];

				$rtwwwap_updated 	= update_user_meta( $rtwwwap_user_id, 'rtwwwap_affiliate', 1 );
				$rtwwwap_updated 	= update_user_meta( $rtwwwap_user_id, 'rtwwwap_affiliate', 1 );
				$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );
				$rtwwwap_aff_approved 	= isset( $rtwwwap_extra_features[ 'aff_verify' ] ) ? $rtwwwap_extra_features[ 'aff_verify' ] : 0;

				if( $rtwwwap_aff_approved == 0 ){
					update_user_meta( $rtwwwap_user_id, 'rtwwwap_aff_approved', 1 );
					$rtwwwap_mlm = get_option( 'rtwwwap_mlm_opt' );
					if( isset( $rtwwwap_mlm[ 'activate' ] ) && $rtwwwap_mlm[ 'activate' ] == 1 )
					{
						global $wpdb;
						//check if already in MLM chain
						$rtwwwap_already_a_child = $wpdb->get_var( $wpdb->prepare( "SELECT `id` FROM ".$wpdb->prefix."rtwwwap_mlm WHERE `aff_id` = %d", $rtwwwap_user_id ) );

						if( is_null( $rtwwwap_already_a_child  ) ){
							$rtwwwap_allowed_childs = isset( $rtwwwap_mlm[ 'child' ] ) ? $rtwwwap_mlm[ 'child' ] : 1;

							$rtwwwap_parent_id = $wpdb->get_var( $wpdb->prepare( "SELECT `aff_id` FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `signed_up_id` = %d", $rtwwwap_user_id ) );

							if( $rtwwwap_parent_id ){
								$rtwwwap_current_childs = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( `id` ) FROM ".$wpdb->prefix."rtwwwap_mlm WHERE `parent_id` = %d", $rtwwwap_parent_id ) );

								if( $rtwwwap_allowed_childs > $rtwwwap_current_childs ){
									$rtwwwap_updated = 	$wpdb->insert(
															$wpdb->prefix.'rtwwwap_mlm',
															array(
																'aff_id'    	=> $rtwwwap_user_id,
																'parent_id'    	=> $rtwwwap_parent_id,
																'status'    	=> 1,
																'last_activity'	=> '0000-00-00 00:00:00',
																'added_date'    => date( 'Y-m-d H:i:s' )
															)
														);
								}
								else{
									$rtwwwap_get_first_child = $wpdb->get_results( $wpdb->prepare( "SELECT `aff_id` FROM ".$wpdb->prefix."rtwwwap_mlm WHERE `parent_id` = %d ORDER BY `added_date` ASC", $rtwwwap_parent_id ), ARRAY_A );
									$rtwwwap_child_to_get_child = "" ;
									foreach( $rtwwwap_get_first_child as $rtwwwap_child_key => $rtwwwap_child_value )
									{
										$rtwwwap_childs_child = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( `id` ) FROM ".$wpdb->prefix."rtwwwap_mlm WHERE `parent_id` = %d", $rtwwwap_child_value[ 'aff_id' ] ) );

										if( $rtwwwap_allowed_childs > $rtwwwap_childs_child )
										{
											$rtwwwap_child_to_get_child = $rtwwwap_child_value[ 'aff_id' ];
											break;
										}
									}

									$rtwwwap_updated = 	$wpdb->insert(
															$wpdb->prefix.'rtwwwap_mlm',
															array(
																'aff_id'    	=> $rtwwwap_user_id,
																'parent_id'    	=> $rtwwwap_child_to_get_child,
																'status'    	=> 1,
																'last_activity'	=> '0000-00-00 00:00:00',
																'added_date'    => date( 'Y-m-d H:i:s' )
															)
														);
								}
							}
						}
					}
				}
				
				if( $rtwwwap_aff_approved == 1 ){
					update_user_meta( $rtwwwap_user_id, 'rtwwwap_aff_approved', 0 );
				}
				if( $rtwwwap_updated ){
					$rtwwwap_affiliate_page_id = get_option('rtwwwap_affiliate_page_id');
					$rtwwwap_redirect_link = get_permalink($rtwwwap_affiliate_page_id);
					wp_redirect( $rtwwwap_redirect_link );
				}
				else{
					$rtwwwap_affiliate_page_id = get_option('rtwwwap_affiliate_page_id');
					$rtwwwap_redirect_link = get_permalink($rtwwwap_affiliate_page_id);
					wp_redirect( $rtwwwap_redirect_link );
				}
				
			}
		}

	}
	

	function rtwwwap_become_affiliate()
	{
		global $wpdb;
		$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );
		$rtwwwap_user_id 	= sanitize_text_field( $_POST[ 'rtwwwap_user_id' ] );
		$rtwwwap_updated 	= update_user_meta( $rtwwwap_user_id, 'rtwwwap_affiliate', 1 );
		$rtwwwap_aff_approved 	= isset( $rtwwwap_extra_features[ 'aff_verify' ] ) ? $rtwwwap_extra_features[ 'aff_verify' ] : 0;

		if( $rtwwwap_aff_approved == 0 ){
			update_user_meta( $rtwwwap_user_id, 'rtwwwap_aff_approved', 1 );

			$rank_requirement_fields = get_option('rtwwwap_rank_details');

			if(!empty($rank_requirement_fields)){
				
				foreach($rank_requirement_fields as $option_name => $option_val ){

					$rank_details = array();
	
					array_push($rank_details,$option_val['rank_name'], $option_val['rank_desc'], $option_val['rank_priority'], $option_val['rank_commission']);
	
					if(count($option_val['rank_requirement']) == 1 && $option_val['rank_requirement'][0]['optionField'] ==1){
	
						update_user_meta($rtwwwap_user_id,'rank_detail', $rank_details);
						break;
					}
					
				}
			}

			$rtwwwap_rank_detail = get_user_meta($rtwwwap_user_id,'rank_detail',true);
			
			$rtwwwap_rank_commision =  isset($rtwwwap_rank_detail[3])? $rtwwwap_rank_detail[3]: "";
			$rtwwwap_currency = get_woocommerce_currency();

			if($rtwwwap_rank_detail && $rtwwwap_rank_commision > 0 ){

				$rtwwwap_updated = $wpdb->insert(
					$wpdb->prefix.'rtwwwap_referrals',
					array(
					'aff_id' => $rtwwwap_user_id,
					'type' => 15,
					'order_id' => "",
					'date' => date( 'Y-m-d H:i:s' ),
					'status' => 0,
					'amount' => $rtwwwap_rank_commision ,
					'capped' => "",
					'currency' => $rtwwwap_currency,
					'product_details' => "",
					)
				);
			}

			$rtwwwap_mlm = get_option( 'rtwwwap_mlm_opt' );

			if( isset( $rtwwwap_mlm[ 'activate' ] ) && $rtwwwap_mlm[ 'activate' ] == 1 )
			{
				$rtwwwap_mlm_type = isset( $rtwwwap_mlm[ 'mlm_type' ] ) ? $rtwwwap_mlm[ 'mlm_type' ] : "";

				global $wpdb;
				//check if already in MLM chain
				$rtwwwap_already_a_child = $wpdb->get_var( $wpdb->prepare( "SELECT `id` FROM ".$wpdb->prefix."rtwwwap_mlm WHERE `aff_id` = %d", $rtwwwap_user_id ) );

				if( is_null( $rtwwwap_already_a_child  ) ){
					$rtwwwap_allowed_childs = isset( $rtwwwap_mlm[ 'child' ] ) ? $rtwwwap_mlm[ 'child' ] : 1;

					$rtwwwap_parent_id = $wpdb->get_var( $wpdb->prepare( "SELECT `aff_id` FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `signed_up_id` = %d", $rtwwwap_user_id ) );

					if( $rtwwwap_parent_id )
					{
						$rtwwwap_current_childs = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( `id` ) FROM ".$wpdb->prefix."rtwwwap_mlm WHERE `parent_id` = %d", $rtwwwap_parent_id ) );

						if( $rtwwwap_allowed_childs > $rtwwwap_current_childs){
							$rtwwwap_updated = 	$wpdb->insert(
													$wpdb->prefix.'rtwwwap_mlm',
													array(
														'aff_id'    	=> $rtwwwap_user_id,
														'parent_id'    	=> $rtwwwap_parent_id,
														'status'    	=> 1,
														'last_activity'	=> '0000-00-00 00:00:00',
														'added_date'    => date( 'Y-m-d H:i:s' )
													)
												);
						}
						else{

							$rtwwwap_updated = "restrict_aff";

							// $rtwwwap_get_first_child = $wpdb->get_results( $wpdb->prepare( "SELECT `aff_id` FROM ".$wpdb->prefix."rtwwwap_mlm WHERE `parent_id` = %d ORDER BY `added_date` ASC", $rtwwwap_parent_id ), ARRAY_A );
							// $rtwwwap_child_to_get_child = "" ;
							// foreach( $rtwwwap_get_first_child as $rtwwwap_child_key => $rtwwwap_child_value )
							// {
							// 	$rtwwwap_childs_child = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( `id` ) FROM ".$wpdb->prefix."rtwwwap_mlm WHERE `parent_id` = %d", $rtwwwap_child_value[ 'aff_id' ] ) );

							// 	if( $rtwwwap_allowed_childs > $rtwwwap_childs_child )
							// 	{
							// 		$rtwwwap_child_to_get_child = $rtwwwap_child_value[ 'aff_id' ];
							// 		break;
							// 	}
							// }

							// $rtwwwap_updated = 	$wpdb->insert(
							// 						$wpdb->prefix.'rtwwwap_mlm',
							// 						array(
							// 							'aff_id'    	=> $rtwwwap_user_id,
							// 							'parent_id'    	=> $rtwwwap_child_to_get_child,
							// 							'status'    	=> 1,
							// 							'last_activity'	=> '0000-00-00 00:00:00',
							// 							'added_date'    => date( 'Y-m-d H:i:s' )
							// 						)
							// 					);
						}
					}
				}
			}
		}
		
		if( $rtwwwap_aff_approved == 1 ){
			update_user_meta( $rtwwwap_user_id, 'rtwwwap_aff_approved', 0 );

			$rtwwwap_from 			= get_user_by( 'ID', $rtwwwap_user_id );
			$rtwwwap_user_email 	= esc_html( $rtwwwap_from->user_email );
			$rtwwwap_user_name      = esc_html( $rtwwwap_from->user_login );

			$all_emails = get_option('customize_email', false);

			$signup_email = get_option('signup_email','null');

			if(isset($all_emails['Become an affiliate Email']['subject'])){
				$rtwwwap_subject_text = $all_emails['Become an affiliate Email']['subject'];
				$rtwwwap_message_text = $all_emails['Become an affiliate Email']['content'];
			}

			$rtwwwap_subject 		= esc_html__( $rtwwwap_user_email." ".$rtwwwap_subject_text, 'rtwwwap-wp-wc-affiliate-program' );


			$rtwwwap_message 		= sprintf( '%s', esc_html__( $rtwwwap_user_name." ".$rtwwwap_message_text, 'rtwwwap-wp-wc-affiliate-program' ));
			
			$rtwwwap_to 	= esc_html( get_bloginfo( 'admin_email' ) );
			$rtwwwap_headers[] 		= 'Content-Type: text/html; charset=utf-8';

			$rtwwwap_headers[] 		= sprintf( '%s: %s <%s>', esc_html__( 'From', 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_user_name, $rtwwwap_user_email );

			if($signup_email == "true"){
				wp_mail( $rtwwwap_to, $rtwwwap_subject, $rtwwwap_message, $rtwwwap_headers );
			}
				

		}
		return $rtwwwap_updated;

	}

	/*
	* To show affiliate page with shortcode
	*/
	function rtwwwap_affiliate_page_callback(){
		$rtwwwap_verification_done = get_option( 'rtwwwap_verification_done', array() );
		$rtwwwap_verification_done_status = isset($rtwwwap_verification_done['status']) ? $rtwwwap_verification_done['status'] : false;
		$rtwwwap_verification_done_purchase = isset($rtwwwap_verification_done['purchase_code']) ? $rtwwwap_verification_done['purchase_code'] : false;
		if( empty( $rtwwwap_verification_done ) || $rtwwwap_verification_done_status == false || empty($rtwwwap_verification_done_purchase) )
		{
			return;
		}
		$rtwwwap_html = include( RTWWWAP_DIR.'public/templates/rtwwwap_affiliate.php' );
		return $rtwwwap_html;
	}

	/*
	* Creates cookie when a affiliate URL is opened
	*/
	function rtwwwap_url_check()
	{
		$this->pay_per_click_bonus();

		$this->rtwwwap_coupon_check();
		$rtwwwap_verification_done = get_option( 'rtwwwap_verification_done', array() );
		$rtwwwap_verification_done_status = isset($rtwwwap_verification_done['status']) ? $rtwwwap_verification_done['status'] : false;
		$rtwwwap_verification_done_purchase = isset($rtwwwap_verification_done['purchase_code']) ? $rtwwwap_verification_done['purchase_code'] : false;
		if( empty( $rtwwwap_verification_done ) || $rtwwwap_verification_done_status == false || empty($rtwwwap_verification_done_purchase) )
		{
			return;
		}
		$rtwwwap_user_id = get_current_user_id();

		if($rtwwwap_user_id)
		{
			$rtwwwap_user_meta = get_userdata($rtwwwap_user_id);
			$rtwwwap_user_roles = $rtwwwap_user_meta->roles[0];
	
			if($rtwwwap_user_roles != 'administrator')
			{
				update_user_meta($rtwwwap_user_id,'show_admin_bar_front', false);
			}
		}
	
		$rtwwwap_extra_features 	= get_option( 'rtwwwap_extra_features_opt' );
		$rtwwwap_affiliate_slug 		= isset( $rtwwwap_extra_features[ 'affiliate_slug' ] ) ? $rtwwwap_extra_features[ 'affiliate_slug' ] : esc_html__( 'rtwwwap_aff', 'rtwwwap-wp-wc-affiliate-program' ) ;


		if( isset( $_GET[ $rtwwwap_affiliate_slug ] ) ){

			// update code starts here 

			if(isset($_GET['action'])){
				$rtwwwap_aff_share = $_GET['action'];
			}
			else{
				$rtwwwap_aff_share = 0;
			}

			$custom_str = $_GET[ $rtwwwap_affiliate_slug ];
			$rtwwwap_referee_aff = get_users(array(
				'meta_key' => 'rtwwwap_referee_custom_str',
				'meta_value' => $custom_str
			));

			if($rtwwwap_referee_aff){
				$rtwwwap_affiliate_id = $rtwwwap_referee_aff[0]->ID;
			}

			// ends here

			// $rtwwwap_aff 			= explode( '_', $_GET[ $rtwwwap_affiliate_slug ] );
			// if( end($rtwwwap_aff) == "share")
			// {
			// 	$rtwwwap_affiliate_id 	= $rtwwwap_aff[count($rtwwwap_aff) - 2];
			// 	$rtwwwap_aff_share 		= end($rtwwwap_aff);
			// }
			// else{
			// 	$rtwwwap_affiliate_id 	= end($rtwwwap_aff);
			// 	$rtwwwap_aff_share 		= 0;
			// }

			//// get URL host+REQUEST_URI
			$rtwwwap_cookie_time 	= isset( $rtwwwap_extra_features[ 'cookie_time' ] ) ? $rtwwwap_extra_features[ 'cookie_time' ] : 0;

			$rtwwwap_referral_link = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

			$rtwwwap_referrals_link = remove_query_arg(array($rtwwwap_affiliate_slug," "),$rtwwwap_referral_link);
			setcookie( 'rtwwwap_referral_link', $rtwwwap_referrals_link, $rtwwwap_cookie_time, '/' );

			global $wpdb;
			
		
			$rtwwwap_link_present = $wpdb->get_var( $wpdb->prepare( "SELECT count('id') FROM ".$wpdb->prefix."rtwwwap_referral_link WHERE `aff_link`=%d   AND `aff_id`=%d  ", $rtwwwap_referrals_link , $rtwwwap_affiliate_id ) );
			
				if($rtwwwap_link_present)
				{
					$rtwwwap_increase_hit_count = $wpdb->get_var( $wpdb->prepare( "SELECT `link_open` FROM ".$wpdb->prefix."rtwwwap_referral_link WHERE `aff_id` = %d AND `aff_link` = %d ", $rtwwwap_affiliate_id , $rtwwwap_referrals_link ) );
					$rtwwwap_update_count =  $rtwwwap_increase_hit_count + 1;

					$rtwwwap_update_hit_count = $wpdb->get_var( $wpdb->prepare( "UPDATE ".$wpdb->prefix."rtwwwap_referral_link SET `link_open`= %d WHERE `aff_id`= %d AND `aff_link` = %d", $rtwwwap_update_count, $rtwwwap_affiliate_id, $rtwwwap_referrals_link ));
					
				}
				else
				{
					$rtwwwap_updated = 	$wpdb->insert(
						$wpdb->prefix.'rtwwwap_referral_link',
						array(
							'aff_id' => $rtwwwap_affiliate_id,
						'aff_link' => $rtwwwap_referrals_link ,
						'link_open' => 1,
						'link_purchase' => 0
						)
					);	
				}
			
			if( get_user_meta( $rtwwwap_affiliate_id, 'rtwwwap_affiliate', true ) ){
				$rtwwwap_commission_settings 	= get_option( 'rtwwwap_commission_settings_opt' );
				$rtwwwap_commission_type 		= isset( $rtwwwap_commission_settings[ 'only_open_url' ] ) ? $rtwwwap_commission_settings[ 'only_open_url' ] : 0;

				//lifetime
				$rtwwwap_unlimit_comm = isset( $rtwwwap_commission_settings[ 'unlimit_comm' ] ) ? $rtwwwap_commission_settings[ 'unlimit_comm' ] : '0';

				if( $rtwwwap_unlimit_comm == '1' ){
					$rtwwwap_current_user_id = get_current_user_id();

					if( $rtwwwap_current_user_id ){
						$rtwwwap_override_unlimit_user_id = isset( $rtwwwap_commission_settings[ 'override_unlimit_comm' ] ) ? $rtwwwap_commission_settings[ 'override_unlimit_comm' ] : '0';

						if( $rtwwwap_override_unlimit_user_id == '1' ){
							update_user_meta( $rtwwwap_current_user_id, 'rtwwwap_lifetime_user_id', $rtwwwap_affiliate_id );
						}
						else{
							$rtwwwap_if_unlimit = get_user_meta( $rtwwwap_current_user_id, 'rtwwwap_lifetime_user_id', true );
							if( !$rtwwwap_if_unlimit ){
								update_user_meta( $rtwwwap_current_user_id, 'rtwwwap_lifetime_user_id', $rtwwwap_affiliate_id );
							}
						}
					}
				}

				$rtwwwap_prod_id 	= get_the_ID();
				$rtwwwap_cookie_arr = array( "rtwwwap_aff_id" => $rtwwwap_affiliate_id );

				$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );

				if( $rtwwwap_cookie_time ){
					$rtwwwap_cookie_time = time()+( $rtwwwap_cookie_time * 24 * 60 * 60 );
				}

				if( $rtwwwap_commission_type == 1 ){
					if ( get_post_type( $rtwwwap_prod_id ) == 'product' ) {
						$rtwwwap_cookie_arr[ "rtwwwap_prod_id" ] = $rtwwwap_prod_id;
					}
				}

				if( $rtwwwap_aff_share ){
					$rtwwwap_cookie_arr[ 'share' ] = 'share';
				}

				if( isset( $_COOKIE[ 'rtwwwap_referral' ] ) ){
					unset( $_COOKIE[ 'rtwwwap_referral' ] );
				}

				$rtwwwap_cookie_value = implode( '#', $rtwwwap_cookie_arr );
				setcookie( 'rtwwwap_referral', $rtwwwap_cookie_value, $rtwwwap_cookie_time, '/' );
			}	
		}
	}


	function pay_per_click_bonus()
	{

		$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );
		$rtwwwap_per_click_bonus 	= isset( $rtwwwap_extra_features[ 'pay_per_click' ] ) ? esc_html( $rtwwwap_extra_features[ 'pay_per_click' ] ) : 0;

		// print_r($rtwwwap_per_click_bonus);
		// print_r($rtwwwap_signup_bonus_type);
		//  die("dfvega");

		if( $rtwwwap_per_click_bonus != 0 ){

			if(!wp_doing_ajax())
			{

				$rtwwwap_affiliate_slug = isset( $rtwwwap_extra_features[ 'affiliate_slug' ] ) ? $rtwwwap_extra_features[ 'affiliate_slug' ] : esc_html__( 'rtwwwap_aff', 'rtwwwap-wp-wc-affiliate-program' ) ;

				if( isset( $_GET[ $rtwwwap_affiliate_slug ] ) )                
				{
					$rtwwwap_aff = explode( '_', $_GET[ $rtwwwap_affiliate_slug ] );
					$rtwwwap_affiliate_id 	= end($rtwwwap_aff);
					global $wpdb;

					$rtwwwap_ip_address = '';
					if (isset($_SERVER['HTTP_CLIENT_IP'])){
						$rtwwwap_ip_address = $_SERVER['HTTP_CLIENT_IP'];
					}
					else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
						$rtwwwap_ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
					}
					else if(isset($_SERVER['HTTP_X_FORWARDED'])){
						$rtwwwap_ip_address = $_SERVER['HTTP_X_FORWARDED'];
					}
					else if(isset($_SERVER['HTTP_FORWARDED_FOR'])){
						$rtwwwap_ip_address = $_SERVER['HTTP_FORWARDED_FOR'];
					}
					else if(isset($_SERVER['HTTP_FORWARDED'])){
						$rtwwwap_ip_address = $_SERVER['HTTP_FORWARDED'];
					}
					else if(isset($_SERVER['REMOTE_ADDR'])){
						$rtwwwap_ip_address = $_SERVER['REMOTE_ADDR'];
					}
					$rtwwwap_currency 		= get_woocommerce_currency();

					$rtwwwap_get_ip = $wpdb->get_results( $wpdb->prepare( "SELECT `ip` FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `ip`=%s ", $rtwwwap_ip_address ), ARRAY_A );

					if(empty($rtwwwap_get_ip)){

						$rtwwwap_updated = $wpdb->insert(
							$wpdb->prefix.'rtwwwap_referrals',
							array(
								'aff_id'    			=> $rtwwwap_affiliate_id,
								'type'    				=> 7,
								'order_id'    			=> "",
								'date'    				=> date( 'Y-m-d H:i:s' ),
								'status'    			=> 0,
								'amount'    			=> $rtwwwap_per_click_bonus,
								'capped'    			=> "",
								'currency'    			=> $rtwwwap_currency,
								'product_details'   	=> "",
								'device'   				=> "",
								'ip'					=> $rtwwwap_ip_address
							)
						);
					}
						
				}
			}
		}
			
	}

	/*
	* To create successful referral
	*/
	function rtwwwap_referred_item_ordered( $rtwwwap_order_id ){ 

		$rtwwwap_verification_done = get_option( 'rtwwwap_verification_done', array() );
		$rtwwwap_verification_done_status = isset($rtwwwap_verification_done['status']) ? $rtwwwap_verification_done['status'] : false;
		$rtwwwap_verification_done_purchase = isset($rtwwwap_verification_done['purchase_code']) ? $rtwwwap_verification_done['purchase_code'] : false;
		if( empty( $rtwwwap_verification_done ) || $rtwwwap_verification_done_status == false || empty($rtwwwap_verification_done_purchase) )
		{
			return;
		}

		
		if(isset($_COOKIE['rtwwwap_referral_link']) && $_COOKIE['rtwwwap_referral_link'] != '' )
		{
			$rtwwwap_referrals_link = $_COOKIE['rtwwwap_referral_link'];

			$rtwwwap_referral 	= explode( '#', $_COOKIE[ 'rtwwwap_referral' ] );
			$rtwwwap_affiliate_id 	= esc_html( $rtwwwap_referral[ 0 ] );

			global $wpdb;
			
		
			$rtwwwap_link_present = $wpdb->get_var( $wpdb->prepare( "SELECT count('id') FROM ".$wpdb->prefix."rtwwwap_referral_link WHERE `aff_link`=%d   AND `aff_id`=%d  ", $rtwwwap_referrals_link , $rtwwwap_affiliate_id ) );


					$rtwwwap_increase_purchase_count = $wpdb->get_var( $wpdb->prepare( "SELECT `link_purchase` FROM ".$wpdb->prefix."rtwwwap_referral_link WHERE `aff_id` = %d AND `aff_link` = %d ", $rtwwwap_affiliate_id , $rtwwwap_referrals_link ) );
					$rtwwwap_update_purchase_count =  $rtwwwap_increase_purchase_count + 1;

					$rtwwwap_update_purchase_count = $wpdb->get_var( $wpdb->prepare( "UPDATE ".$wpdb->prefix."rtwwwap_referral_link SET `link_purchase`= %d WHERE `aff_id`= %d AND `aff_link` = %d", $rtwwwap_update_purchase_count, $rtwwwap_affiliate_id, $rtwwwap_referrals_link ));

				
		}

		//referral code
		$rtwwwap_extra_features 	= get_option( 'rtwwwap_extra_features_opt' );
		$rtwwwap_signup_bonus_type 	= isset( $rtwwwap_extra_features[ 'signup_bonus_type' ] ) ? esc_html( $rtwwwap_extra_features[ 'signup_bonus_type' ] ) : 0;

		//mlm
		$rtwwwap_mlm 		= get_option( 'rtwwwap_mlm_opt' );
		$rtwwwap_mlm_active	= isset( $rtwwwap_mlm[ 'activate' ] ) ? $rtwwwap_mlm[ 'activate' ] : 0;

		

		//lifetime
		$rtwwwap_commission_settings = get_option( 'rtwwwap_commission_settings_opt' );
		$rtwwwap_unlimit_comm = isset( $rtwwwap_commission_settings[ 'unlimit_comm' ] ) ? $rtwwwap_commission_settings[ 'unlimit_comm' ] : '0';

	
		// if( $rtwwwap_signup_bonus_type == 1 && $rtwwwap_mlm_active ){
		// 	$this->rtwwwap_referral_code_comm( $rtwwwap_order_id );
		// }
		if( isset( $_COOKIE[ 'rtwwwap_referral' ] ) || $rtwwwap_unlimit_comm == 1 )
		{

			global $wpdb;
			$rtwwwap_referrer_id = 0;
			if( $rtwwwap_unlimit_comm == '1' ){
				$rtwwwap_current_user_id = get_current_user_id();

				if( $rtwwwap_current_user_id ){
					$rtwwwap_referrer_id = get_user_meta( $rtwwwap_current_user_id, 'rtwwwap_lifetime_user_id', true );
					if( !$rtwwwap_referrer_id )
					{
						$rtwwwap_referral 	= explode( '#', $_COOKIE[ 'rtwwwap_referral' ] );
						$rtwwwap_affiliate_id 			= esc_html( $rtwwwap_referral[ 0 ] );
						update_user_meta( $rtwwwap_current_user_id, 'rtwwwap_lifetime_user_id', $rtwwwap_affiliate_id );
						$rtwwwap_referrer_id = $rtwwwap_affiliate_id;
					}
				}
			}

			if( $rtwwwap_referrer_id ){
				$this->rtwwwap_unlimited_reff_comm( $rtwwwap_order_id, $rtwwwap_referrer_id );
			}
			elseif( isset( $_COOKIE[ 'rtwwwap_referral' ] ) ){
				$rtwwwap_referral 	= explode( '#', $_COOKIE[ 'rtwwwap_referral' ] );
				$rtwwwap_order 		= wc_get_order( $rtwwwap_order_id );
				$order_total = $rtwwwap_order->get_total();
				$rtwwwap_comm_base 	= isset( $rtwwwap_commission_settings[ 'comm_base' ] ) ? $rtwwwap_commission_settings[ 'comm_base' ] : '1';
				$rtwwwap_total_commission	= 0;
				$rtwwwap_aff_prod_details 	= array();
				$rtwwwap_aff_prod_price 	= array();
				$rtwwwap_user_id 			= esc_html( $rtwwwap_referral[ 0 ] );

				if( RTWWWAP_IS_WOO == 1 ){
					$rtwwwap_currency 		= get_woocommerce_currency();
					$rtwwwap_currency_sym 	= get_woocommerce_currency_symbol();
				}
				else{
					require_once( RTWWWAP_DIR.'includes/rtwaffiliatehelper.php' );

					$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );
					$rtwwwap_currency 		= isset( $rtwwwap_extra_features[ 'currency' ] ) ? $rtwwwap_extra_features[ 'currency' ] : 'USD';
					$rtwwwap_curr_obj 		= new RtwAffiliateHelper();
					$rtwwwap_currency_sym 	= $rtwwwap_curr_obj->rtwwwap_curr_symbol( $rtwwwap_currency );
				}

				$rtwwwap_commission_type 	= isset( $rtwwwap_commission_settings[ 'only_open_url' ] ) ? $rtwwwap_commission_settings[ 'only_open_url' ] : 0;
				$rtwwwap_shared 			= strpos( $_COOKIE[ 'rtwwwap_referral' ], 'share' );
				$rtwwwap_product_url 		= false;
				$rtwwwap_order_data   = $rtwwwap_order->get_data();
				$current_date = $rtwwwap_order_data['date_created']->date('Y-m-d');

				if( $rtwwwap_comm_base == 1 ){
					$rtwwwap_per_prod_mode 			= isset( $rtwwwap_commission_settings[ 'per_prod_mode' ] ) ? $rtwwwap_commission_settings[ 'per_prod_mode' ] : 0;
					$rtwwwap_all_commission 		= isset( $rtwwwap_commission_settings[ 'all_commission' ] ) ? $rtwwwap_commission_settings[ 'all_commission' ] : 0;
					$rtwwwap_all_commission_type 	= isset( $rtwwwap_commission_settings[ 'all_commission_type' ] ) ? $rtwwwap_commission_settings[ 'all_commission_type' ] : 'percentage';
					$rtwwwap_per_cat 				= isset( $rtwwwap_commission_settings[ 'per_cat' ] ) ? $rtwwwap_commission_settings[ 'per_cat' ] : array();
					$rtwwwap_per_cat_special 		= isset( $rtwwwap_commission_settings[ 'cat_opt_special' ] ) ? $rtwwwap_commission_settings[ 'cat_opt_special' ] : array();

					$rtwwwap_per_cat_special_percent_comm 		= isset( $rtwwwap_commission_settings[ 'percent' ] ) ? $rtwwwap_commission_settings[ 'percent' ] : 0;
					$rtwwwap_per_cat_special_fixed_comm		= isset( $rtwwwap_commission_settings[ 'fixed' ] ) ? $rtwwwap_commission_settings[ 'fixed' ] : 0;

					$rtwwwap_start_date 		= isset( $rtwwwap_commission_settings[ 'start_date' ] ) ? $rtwwwap_commission_settings[ 'start_date' ] : array();
					$rtwwwap_end_date		= isset( $rtwwwap_commission_settings[ 'end_date' ] ) ? $rtwwwap_commission_settings[ 'end_date' ] : array();
										

					foreach( $rtwwwap_order->get_items() as $rtwwwap_item_key => $rtwwwap_item_values )
					{
						$rtwwwap_prod_comm 		= 0;
						$rtwwwap_product_id 	= $rtwwwap_item_values->get_product_id();
						$rtwwwap_product_price	= $rtwwwap_item_values->get_total();
						$rtwwwap_product_terms 	= get_the_terms( $rtwwwap_product_id, 'product_cat' );
						$rtwwwap_aff_prod_price [] = $rtwwwap_product_price; 
						$rtwwwap_flag = false;

						$rtwwwap_product_cat_id = $rtwwwap_product_terms[0]->term_id;

						if(!empty($rtwwwap_per_cat_special)){
							foreach( $rtwwwap_per_cat_special as $rtwwwap_key => $rtwwwap_value ){
								
								if( $rtwwwap_product_cat_id == $rtwwwap_value ){
									
									$rtwwwap_cat_per_comm = $rtwwwap_per_cat_special_percent_comm;
									$rtwwwap_cat_fix_comm = $rtwwwap_per_cat_special_fixed_comm;
									$rtwwwap_flag = true;
									break;
								}
							}
						}

						if(($current_date >= $rtwwwap_start_date ) && ( $current_date <= $rtwwwap_end_date ) && $rtwwwap_flag && $rtwwwap_per_cat_special_percent_comm || $rtwwwap_per_cat_special_fixed_comm){			
							$rtwwwap_cat_per_comm = 0;
							$rtwwwap_cat_fix_comm = 0;
							
							foreach( $rtwwwap_per_cat_special as $rtwwwap_key => $rtwwwap_value ){
							
								if( $rtwwwap_product_cat_id == $rtwwwap_value ){
									
									$rtwwwap_cat_per_comm = $rtwwwap_per_cat_special_percent_comm;
									$rtwwwap_cat_fix_comm = $rtwwwap_per_cat_special_fixed_comm;
									$rtwwwap_flag = true;
	
									break;
								}
							}

							if( $rtwwwap_cat_per_comm > 0 ){
								$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_cat_per_comm ) / 100;
							}
							else if( $rtwwwap_cat_fix_comm > 0 ){
								$rtwwwap_prod_comm = $rtwwwap_cat_fix_comm;
							}
	
							if( $rtwwwap_prod_comm != '' ){
								$rtwwwap_aff_prod_details[] = array(
											'product_id' 		=> $rtwwwap_product_id,
											'product_price' 	=> $rtwwwap_product_price,
											'commission_fix' 	=> $rtwwwap_cat_fix_comm,
											'commission_perc' 	=> $rtwwwap_cat_per_comm,
											'prod_commission' 	=> $rtwwwap_prod_comm
										);
	
								$rtwwwap_total_commission += $rtwwwap_prod_comm;
							}
						}

						else if( $rtwwwap_commission_type == 1 && array_key_exists( 1, $rtwwwap_referral ) && ( $rtwwwap_product_id == $rtwwwap_referral[ 1 ] ) )
						{

							$rtwwwap_product_url = true;
							if( $rtwwwap_per_prod_mode == 1 ){

								$rtwwwap_prod_per_comm = get_post_meta( $rtwwwap_product_id, 'rtwwwap_percentage_commission_box', true );

								if( $rtwwwap_prod_per_comm > 0 ){
									$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_prod_per_comm ) / 100;
									$rtwwwap_aff_prod_details[] = array(
							    					'product_id' 		=> $rtwwwap_product_id,
							    					'product_price' 	=> $rtwwwap_product_price,
							    					'commission_fix' 	=> '',
							    					'commission_perc' 	=> $rtwwwap_prod_per_comm,
							    					'prod_commission' 	=> $rtwwwap_prod_comm
							    				);

						    		$rtwwwap_total_commission += $rtwwwap_prod_comm;
								}
								elseif( $rtwwwap_prod_per_comm === '0' ){
									// no commission needs to be generated for this product
								}
								else{
									if( !empty( $rtwwwap_per_cat ) ){
										$rtwwwap_cat_per_comm = 0;
										$rtwwwap_cat_fix_comm = 0;
										$rtwwwap_flag = false;
										foreach( $rtwwwap_per_cat as $rtwwwap_key => $rtwwwap_value ){
											if( in_array( $rtwwwap_product_cat_id, $rtwwwap_value[ 'ids' ] ) ){
												$rtwwwap_cat_per_comm = $rtwwwap_value[ 'cat_percentage_commission' ];
												$rtwwwap_cat_fix_comm = $rtwwwap_value[ 'cat_fixed_commission' ];
												$rtwwwap_flag = true;

												break;
											}
										}
										if( $rtwwwap_flag ){
											if( $rtwwwap_cat_per_comm > 0 ){
												$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_cat_per_comm ) / 100;
											}
											if( $rtwwwap_cat_fix_comm > 0 ){
												$rtwwwap_prod_comm += $rtwwwap_cat_fix_comm;
											}

											if( $rtwwwap_prod_comm != '' ){
												$rtwwwap_aff_prod_details[] = array(
									    					'product_id' 		=> $rtwwwap_product_id,
									    					'product_price' 	=> $rtwwwap_product_price,
									    					'commission_fix' 	=> $rtwwwap_cat_fix_comm,
									    					'commission_perc' 	=> $rtwwwap_cat_per_comm,
									    					'prod_commission' 	=> $rtwwwap_prod_comm
									    				);

								    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
								    		}
										}
										else{
											if( $rtwwwap_all_commission ){
												if( $rtwwwap_all_commission_type == 'percentage' ){
													$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
												}
												elseif( $rtwwwap_all_commission_type == 'fixed' ){
													$rtwwwap_prod_comm += $rtwwwap_all_commission;
												}
												$rtwwwap_aff_prod_details[] = array(
									    					'product_id' 		=> $rtwwwap_product_id,
									    					'product_price' 	=> $rtwwwap_product_price,
									    					'commission_fix' 	=> '',
									    					'commission_perc' 	=> '',
									    					'prod_commission' 	=> $rtwwwap_prod_comm
									    				);

								    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
								    		}
										}
									}
									else{
										if( $rtwwwap_all_commission ){
											if( $rtwwwap_all_commission_type == 'percentage' ){
												$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
											}
											elseif( $rtwwwap_all_commission_type == 'fixed' ){
												$rtwwwap_prod_comm += $rtwwwap_all_commission;
											}
											$rtwwwap_aff_prod_details[] = array(
								    					'product_id' 		=> $rtwwwap_product_id,
								    					'product_price' 	=> $rtwwwap_product_price,
								    					'commission_fix' 	=> '',
									    				'commission_perc' 	=> '',
								    					'prod_commission' 	=> $rtwwwap_prod_comm
								    				);

							    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
							    		}
									}
								}
							}
							elseif( $rtwwwap_per_prod_mode == 2 ){
								$rtwwwap_prod_fix_comm = get_post_meta( $rtwwwap_product_id, 'rtwwwap_fixed_commission_box', true );

								if( $rtwwwap_prod_fix_comm > 0 ){
									$rtwwwap_prod_comm = $rtwwwap_prod_fix_comm;
									$rtwwwap_aff_prod_details[] = array(
							    					'product_id' 		=> $rtwwwap_product_id,
							    					'product_price' 	=> $rtwwwap_product_price,
							    					'commission_fix' 	=> $rtwwwap_prod_fix_comm,
							    					'commission_perc' 	=> '',
							    					'prod_commission' 	=> $rtwwwap_prod_comm
							    				);

						    		$rtwwwap_total_commission += $rtwwwap_prod_comm;
								}
								elseif( $rtwwwap_prod_fix_comm === '0' ){
									// no commission needs to be generated for this product
								}
								else{
									if( !empty( $rtwwwap_per_cat ) ){
										$rtwwwap_cat_per_comm = 0;
										$rtwwwap_cat_fix_comm = 0;
										$rtwwwap_flag = false;
										foreach( $rtwwwap_per_cat as $rtwwwap_key => $rtwwwap_value ){
											if( in_array( $rtwwwap_product_cat_id, $rtwwwap_value[ 'ids' ] ) ){
												$rtwwwap_cat_per_comm = $rtwwwap_value[ 'cat_percentage_commission' ];
												$rtwwwap_cat_fix_comm = $rtwwwap_value[ 'cat_fixed_commission' ];
												$rtwwwap_flag = true;

												break;
											}
										}
										if( $rtwwwap_flag ){
											if( $rtwwwap_cat_per_comm > 0 ){
												$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_cat_per_comm ) / 100;
											}
											if( $rtwwwap_cat_fix_comm > 0 ){
												$rtwwwap_prod_comm += $rtwwwap_cat_fix_comm;
											}

											if( $rtwwwap_prod_comm != '' ){
												$rtwwwap_aff_prod_details[] = array(
									    					'product_id' 		=> $rtwwwap_product_id,
									    					'product_price' 	=> $rtwwwap_product_price,
									    					'commission_fix' 	=> $rtwwwap_cat_fix_comm,
									    					'commission_perc' 	=> $rtwwwap_cat_per_comm,
									    					'prod_commission' 	=> $rtwwwap_prod_comm
									    				);

								    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
								    		}
										}
										else{
											if( $rtwwwap_all_commission ){
												if( $rtwwwap_all_commission_type == 'percentage' ){
													$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
												}
												elseif( $rtwwwap_all_commission_type == 'fixed' ){
													$rtwwwap_prod_comm += $rtwwwap_all_commission;
												}
												$rtwwwap_aff_prod_details[] = array(
									    					'product_id' 		=> $rtwwwap_product_id,
									    					'product_price' 	=> $rtwwwap_product_price,
									    					'commission_fix' 	=> '',
									    					'commission_perc' 	=> '',
									    					'prod_commission' 	=> $rtwwwap_prod_comm
									    				);

								    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
								    		}
										}
									}
									else{
										if( $rtwwwap_all_commission ){
											if( $rtwwwap_all_commission_type == 'percentage' ){
												$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
											}
											elseif( $rtwwwap_all_commission_type == 'fixed' ){
												$rtwwwap_prod_comm += $rtwwwap_all_commission;
											}
											$rtwwwap_aff_prod_details[] = array(
								    					'product_id' 		=> $rtwwwap_product_id,
								    					'product_price' 	=> $rtwwwap_product_price,
								    					'commission_fix' 	=> '',
									    				'commission_perc' 	=> '',
								    					'prod_commission' 	=> $rtwwwap_prod_comm
								    				);

							    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
							    		}
									}
								}
							}
							elseif( $rtwwwap_per_prod_mode == 3 ){
								$rtwwwap_prod_per_comm = get_post_meta( $rtwwwap_product_id, 'rtwwwap_percentage_commission_box', true );
								$rtwwwap_prod_fix_comm = get_post_meta( $rtwwwap_product_id, 'rtwwwap_fixed_commission_box', true );

								if( $rtwwwap_prod_per_comm > 0 ){
									$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_prod_per_comm ) / 100;
								}

								if( $rtwwwap_prod_fix_comm > 0 ){
									$rtwwwap_prod_comm += $rtwwwap_prod_fix_comm;
								}

								if( $rtwwwap_prod_comm === '' ){
									if( $rtwwwap_prod_per_comm !== '0' && $rtwwwap_prod_fix_comm !== '0' ){
										if( !empty( $rtwwwap_per_cat ) ){
											$rtwwwap_cat_per_comm = 0;
											$rtwwwap_cat_fix_comm = 0;
											$rtwwwap_flag = false;
											foreach( $rtwwwap_per_cat as $rtwwwap_key => $rtwwwap_value ){
												if( in_array( $rtwwwap_product_cat_id, $rtwwwap_value[ 'ids' ] ) ){
													$rtwwwap_cat_per_comm = $rtwwwap_value[ 'cat_percentage_commission' ];
													$rtwwwap_cat_fix_comm = $rtwwwap_value[ 'cat_fixed_commission' ];
													$rtwwwap_flag = true;

													break;
												}
											}
											if( $rtwwwap_flag ){
												if( $rtwwwap_cat_per_comm > 0 ){
													$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_cat_per_comm ) / 100;
												}
												if( $rtwwwap_cat_fix_comm > 0 ){
													$rtwwwap_prod_comm += $rtwwwap_cat_fix_comm;
												}

												if( $rtwwwap_prod_comm != '' ){
													$rtwwwap_aff_prod_details[] = array(
										    					'product_id' 		=> $rtwwwap_product_id,
										    					'product_price' 	=> $rtwwwap_product_price,
										    					'commission_fix' 	=> $rtwwwap_cat_fix_comm,
										    					'commission_perc' 	=> $rtwwwap_cat_per_comm,
										    					'prod_commission' 	=> $rtwwwap_prod_comm
										    				);

									    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
									    		}
											}
											else{
												if( $rtwwwap_all_commission ){
													if( $rtwwwap_all_commission_type == 'percentage' ){
														$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
													}
													elseif( $rtwwwap_all_commission_type == 'fixed' ){
														$rtwwwap_prod_comm += $rtwwwap_all_commission;
													}
													$rtwwwap_aff_prod_details[] = array(
										    					'product_id' 		=> $rtwwwap_product_id,
										    					'product_price' 	=> $rtwwwap_product_price,
										    					'commission_fix' 	=> '',
										    					'commission_perc' 	=> '',
										    					'prod_commission' 	=> $rtwwwap_prod_comm
										    				);

									    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
									    		}
											}
										}
										else{
											if( $rtwwwap_all_commission ){
												if( $rtwwwap_all_commission_type == 'percentage' ){
													$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
												}
												elseif( $rtwwwap_all_commission_type == 'fixed' ){
													$rtwwwap_prod_comm += $rtwwwap_all_commission;
												}
												$rtwwwap_aff_prod_details[] = array(
									    					'product_id' 		=> $rtwwwap_product_id,
									    					'product_price' 	=> $rtwwwap_product_price,
									    					'commission_fix' 	=> '',
										    				'commission_perc' 	=> '',
									    					'prod_commission' 	=> $rtwwwap_prod_comm
									    				);

								    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
								    		}
										}
									}
								}
								else{
									$rtwwwap_aff_prod_details[] = array(
						    					'product_id' 		=> $rtwwwap_product_id,
						    					'product_price' 	=> $rtwwwap_product_price,
						    					'commission_fix' 	=> $rtwwwap_prod_fix_comm,
							    				'commission_perc' 	=> $rtwwwap_prod_per_comm,
						    					'prod_commission' 	=> $rtwwwap_prod_comm
						    				);

					    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
								}
							}
							elseif( $rtwwwap_all_commission ){
								if( $rtwwwap_all_commission_type == 'percentage' ){
									$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
								}
								elseif( $rtwwwap_all_commission_type == 'fixed' ){
									$rtwwwap_prod_comm += $rtwwwap_all_commission;
								}
								$rtwwwap_aff_prod_details[] = array(
					    					'product_id' 		=> $rtwwwap_product_id,
					    					'product_price' 	=> $rtwwwap_product_price,
					    					'commission_fix' 	=> '',
						    				'commission_perc' 	=> '',
					    					'prod_commission' 	=> $rtwwwap_prod_comm
					    				);

				    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
				    		}
						}
						elseif( $rtwwwap_commission_type == 0 )
						{
						    if( $rtwwwap_per_prod_mode == 1 ){
								$rtwwwap_prod_per_comm = get_post_meta( $rtwwwap_product_id, 'rtwwwap_percentage_commission_box', true );

								if( $rtwwwap_prod_per_comm > 0 ){
									$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_prod_per_comm ) / 100;
									$rtwwwap_aff_prod_details[] = array(
							    					'product_id' 		=> $rtwwwap_product_id,
							    					'product_price' 	=> $rtwwwap_product_price,
							    					'commission_fix' 	=> '',
							    					'commission_perc' 	=> $rtwwwap_prod_per_comm,
							    					'prod_commission' 	=> $rtwwwap_prod_comm
							    				);

						    		$rtwwwap_total_commission += $rtwwwap_prod_comm;
								}
								elseif( $rtwwwap_prod_per_comm === '0' ){
									// no commission needs to be generated for this product
								}
								else{
									if( !empty( $rtwwwap_per_cat ) ){
										$rtwwwap_cat_per_comm = 0;
										$rtwwwap_cat_fix_comm = 0;
										$rtwwwap_flag = false;
										foreach( $rtwwwap_per_cat as $rtwwwap_key => $rtwwwap_value ){
											if( in_array( $rtwwwap_product_cat_id, $rtwwwap_value[ 'ids' ] ) ){
												$rtwwwap_cat_per_comm = $rtwwwap_value[ 'cat_percentage_commission' ];
												$rtwwwap_cat_fix_comm = $rtwwwap_value[ 'cat_fixed_commission' ];
												$rtwwwap_flag = true;

												break;
											}
										}
										if( $rtwwwap_flag ){
											if( $rtwwwap_cat_per_comm > 0 ){
												$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_cat_per_comm ) / 100;
											}
											if( $rtwwwap_cat_fix_comm > 0 ){
												$rtwwwap_prod_comm += $rtwwwap_cat_fix_comm;
											}

											if( $rtwwwap_prod_comm != '' ){
												$rtwwwap_aff_prod_details[] = array(
									    					'product_id' 		=> $rtwwwap_product_id,
									    					'product_price' 	=> $rtwwwap_product_price,
									    					'commission_fix' 	=> $rtwwwap_cat_fix_comm,
									    					'commission_perc' 	=> $rtwwwap_cat_per_comm,
									    					'prod_commission' 	=> $rtwwwap_prod_comm
									    				);

								    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
								    		}
										}
										else{
											if( $rtwwwap_all_commission ){
												if( $rtwwwap_all_commission_type == 'percentage' ){
													$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
												}
												elseif( $rtwwwap_all_commission_type == 'fixed' ){
													$rtwwwap_prod_comm += $rtwwwap_all_commission;
												}
												$rtwwwap_aff_prod_details[] = array(
									    					'product_id' 		=> $rtwwwap_product_id,
									    					'product_price' 	=> $rtwwwap_product_price,
									    					'commission_fix' 	=> '',
									    					'commission_perc' 	=> '',
									    					'prod_commission' 	=> $rtwwwap_prod_comm
									    				);

								    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
								    		}
										}
									}
									else{
										if( $rtwwwap_all_commission ){
											if( $rtwwwap_all_commission_type == 'percentage' ){
												$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
											}
											elseif( $rtwwwap_all_commission_type == 'fixed' ){
												$rtwwwap_prod_comm += $rtwwwap_all_commission;
											}
											$rtwwwap_aff_prod_details[] = array(
								    					'product_id' 		=> $rtwwwap_product_id,
								    					'product_price' 	=> $rtwwwap_product_price,
								    					'commission_fix' 	=> '',
									    				'commission_perc' 	=> '',
								    					'prod_commission' 	=> $rtwwwap_prod_comm
								    				);

							    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
							    		}
									}
								}
							}
							elseif( $rtwwwap_per_prod_mode == 2 ){
								$rtwwwap_prod_fix_comm = get_post_meta( $rtwwwap_product_id, 'rtwwwap_fixed_commission_box', true );

								if( $rtwwwap_prod_fix_comm > 0 ){
									$rtwwwap_prod_comm = $rtwwwap_prod_fix_comm;
									$rtwwwap_aff_prod_details[] = array(
							    					'product_id' 		=> $rtwwwap_product_id,
							    					'product_price' 	=> $rtwwwap_product_price,
							    					'commission_fix' 	=> $rtwwwap_prod_fix_comm,
							    					'commission_perc' 	=> '',
							    					'prod_commission' 	=> $rtwwwap_prod_comm
							    				);

						    		$rtwwwap_total_commission += $rtwwwap_prod_comm;
								}
								elseif( $rtwwwap_prod_fix_comm === '0' ){
									// no commission needs to be generated for this product
								}
								else{
									if( !empty( $rtwwwap_per_cat ) ){
										$rtwwwap_cat_per_comm = 0;
										$rtwwwap_cat_fix_comm = 0;
										$rtwwwap_flag = false;
										foreach( $rtwwwap_per_cat as $rtwwwap_key => $rtwwwap_value ){
											if( in_array( $rtwwwap_product_cat_id, $rtwwwap_value[ 'ids' ] ) ){
												$rtwwwap_cat_per_comm = $rtwwwap_value[ 'cat_percentage_commission' ];
												$rtwwwap_cat_fix_comm = $rtwwwap_value[ 'cat_fixed_commission' ];
												$rtwwwap_flag = true;

												break;
											}
										}
										if( $rtwwwap_flag ){
											if( $rtwwwap_cat_per_comm > 0 ){
												$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_cat_per_comm ) / 100;
											}
											if( $rtwwwap_cat_fix_comm > 0 ){
												$rtwwwap_prod_comm += $rtwwwap_cat_fix_comm;
											}

											if( $rtwwwap_prod_comm != '' ){
												$rtwwwap_aff_prod_details[] = array(
									    					'product_id' 		=> $rtwwwap_product_id,
									    					'product_price' 	=> $rtwwwap_product_price,
									    					'commission_fix' 	=> $rtwwwap_cat_fix_comm,
									    					'commission_perc' 	=> $rtwwwap_cat_per_comm,
									    					'prod_commission' 	=> $rtwwwap_prod_comm
									    				);

								    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
								    		}
										}
										else{
											if( $rtwwwap_all_commission ){
												if( $rtwwwap_all_commission_type == 'percentage' ){
													$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
												}
												elseif( $rtwwwap_all_commission_type == 'fixed' ){
													$rtwwwap_prod_comm += $rtwwwap_all_commission;
												}
												$rtwwwap_aff_prod_details[] = array(
									    					'product_id' 		=> $rtwwwap_product_id,
									    					'product_price' 	=> $rtwwwap_product_price,
									    					'commission_fix' 	=> '',
									    					'commission_perc' 	=> '',
									    					'prod_commission' 	=> $rtwwwap_prod_comm
									    				);

								    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
								    		}
										}
									}
									else{
										if( $rtwwwap_all_commission ){
											if( $rtwwwap_all_commission_type == 'percentage' ){
												$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
											}
											elseif( $rtwwwap_all_commission_type == 'fixed' ){
												$rtwwwap_prod_comm += $rtwwwap_all_commission;
											}
											$rtwwwap_aff_prod_details[] = array(
								    					'product_id' 		=> $rtwwwap_product_id,
								    					'product_price' 	=> $rtwwwap_product_price,
								    					'commission_fix' 	=> '',
									    				'commission_perc' 	=> '',
								    					'prod_commission' 	=> $rtwwwap_prod_comm
								    				);

							    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
							    		}
									}
								}
							}
							elseif( $rtwwwap_per_prod_mode == 3 ){
								$rtwwwap_prod_per_comm = get_post_meta( $rtwwwap_product_id, 'rtwwwap_percentage_commission_box', true );
								$rtwwwap_prod_fix_comm = get_post_meta( $rtwwwap_product_id, 'rtwwwap_fixed_commission_box', true );

								if( $rtwwwap_prod_per_comm > 0 ){
									$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_prod_per_comm ) / 100;
								}

								if( $rtwwwap_prod_fix_comm > 0 ){
									$rtwwwap_prod_comm += $rtwwwap_prod_fix_comm;
								}

								if( $rtwwwap_prod_comm === '' ){
									if( $rtwwwap_prod_per_comm !== '0' && $rtwwwap_prod_fix_comm !== '0' ){
										if( !empty( $rtwwwap_per_cat ) ){
											$rtwwwap_cat_per_comm = 0;
											$rtwwwap_cat_fix_comm = 0;
											$rtwwwap_flag = false;
											foreach( $rtwwwap_per_cat as $rtwwwap_key => $rtwwwap_value ){
												if( in_array( $rtwwwap_product_cat_id, $rtwwwap_value[ 'ids' ] ) ){
													$rtwwwap_cat_per_comm = $rtwwwap_value[ 'cat_percentage_commission' ];
													$rtwwwap_cat_fix_comm = $rtwwwap_value[ 'cat_fixed_commission' ];
													$rtwwwap_flag = true;

													break;
												}
											}
											if( $rtwwwap_flag ){
												if( $rtwwwap_cat_per_comm > 0 ){
													$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_cat_per_comm ) / 100;
												}
												if( $rtwwwap_cat_fix_comm > 0 ){
													$rtwwwap_prod_comm += $rtwwwap_cat_fix_comm;
												}

												if( $rtwwwap_prod_comm != '' ){
													$rtwwwap_aff_prod_details[] = array(
										    					'product_id' 		=> $rtwwwap_product_id,
										    					'product_price' 	=> $rtwwwap_product_price,
										    					'commission_fix' 	=> $rtwwwap_cat_fix_comm,
										    					'commission_perc' 	=> $rtwwwap_cat_per_comm,
										    					'prod_commission' 	=> $rtwwwap_prod_comm
										    				);

									    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
									    		}
											}
											else{
												if( $rtwwwap_all_commission ){
													if( $rtwwwap_all_commission_type == 'percentage' ){
														$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
													}
													elseif( $rtwwwap_all_commission_type == 'fixed' ){
														$rtwwwap_prod_comm += $rtwwwap_all_commission;
													}
													$rtwwwap_aff_prod_details[] = array(
										    					'product_id' 		=> $rtwwwap_product_id,
										    					'product_price' 	=> $rtwwwap_product_price,
										    					'commission_fix' 	=> '',
										    					'commission_perc' 	=> '',
										    					'prod_commission' 	=> $rtwwwap_prod_comm
										    				);

									    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
									    		}
											}
										}
										else{
											if( $rtwwwap_all_commission ){
												if( $rtwwwap_all_commission_type == 'percentage' ){
													$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
												}
												elseif( $rtwwwap_all_commission_type == 'fixed' ){
													$rtwwwap_prod_comm += $rtwwwap_all_commission;
												}
												$rtwwwap_aff_prod_details[] = array(
									    					'product_id' 		=> $rtwwwap_product_id,
									    					'product_price' 	=> $rtwwwap_product_price,
									    					'commission_fix' 	=> '',
										    				'commission_perc' 	=> '',
									    					'prod_commission' 	=> $rtwwwap_prod_comm
									    				);

								    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
								    		}
										}
									}
								}
								else{
									$rtwwwap_aff_prod_details[] = array(
						    					'product_id' 		=> $rtwwwap_product_id,
						    					'product_price' 	=> $rtwwwap_product_price,
						    					'commission_fix' 	=> $rtwwwap_prod_fix_comm,
							    				'commission_perc' 	=> $rtwwwap_prod_per_comm,
						    					'prod_commission' 	=> $rtwwwap_prod_comm
						    				);

					    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
								}
							}
							elseif( $rtwwwap_all_commission ){
								if( $rtwwwap_all_commission_type == 'percentage' ){
									$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
								}
								elseif( $rtwwwap_all_commission_type == 'fixed' ){
									$rtwwwap_prod_comm += $rtwwwap_all_commission;
								}
								$rtwwwap_aff_prod_details[] = array(
					    					'product_id' 		=> $rtwwwap_product_id,
					    					'product_price' 	=> $rtwwwap_product_price,
					    					'commission_fix' 	=> '',
						    				'commission_perc' 	=> '',
					    					'prod_commission' 	=> $rtwwwap_prod_comm
					    				);

				    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
				    		}
						}
					}
				}
				else
				{
					$rtwwwap_levels_settings 	= get_option( 'rtwwwap_levels_settings_opt' );
					$rtwwwap_user_level 		= get_user_meta( $rtwwwap_user_id, 'rtwwwap_affiliate_level', true );
					$rtwwwap_aff_prod_price = array();
					if(!$rtwwwap_user_level){
						$rtwwwap_user_level = "0";
					}
					//$rtwwwap_user_level 		= isset($rtwwwap_user_level ) ? $rtwwwap_user_level : "0";
				

					$rtwwwap_user_level_details = isset( $rtwwwap_levels_settings[ $rtwwwap_user_level ] ) ? $rtwwwap_levels_settings[ $rtwwwap_user_level ] : '';

					
					if( !empty( $rtwwwap_user_level_details ) ){
						$rtwwwap_level_comm_type 		= $rtwwwap_user_level_details[ 'level_commission_type' ];
						$rtwwwap_level_comm_amount 		= $rtwwwap_user_level_details[ 'level_comm_amount' ];
						$rtwwwap_level_criteria_type 	= $rtwwwap_user_level_details[ 'level_criteria_type' ];
						$rtwwwap_level_criteria_val 	= $rtwwwap_user_level_details[ 'level_criteria_val' ];
				
						foreach( $rtwwwap_order->get_items() as $rtwwwap_item_key => $rtwwwap_item_values )
						{
							$rtwwwap_prod_comm 		= '';
							$rtwwwap_product_id 	= $rtwwwap_item_values->get_product_id();
							$rtwwwap_product_price	= $rtwwwap_item_values->get_total();

							$rtwwwap_aff_prod_price [] = $rtwwwap_product_price; 

						
							if( $rtwwwap_commission_type == 1 && array_key_exists( 1, $rtwwwap_referral ) && ( $rtwwwap_product_id == $rtwwwap_referral[ 1 ] ) )
							{
								$rtwwwap_product_url = true;

								if( $rtwwwap_level_comm_type == 0 ){
									$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_level_comm_amount ) / 100;
									$rtwwwap_aff_prod_details[] = array(
							    					'product_id' 		=> $rtwwwap_product_id,
							    					'product_price' 	=> $rtwwwap_product_price,
							    					'commission_fix' 	=> 'user',
							    					'commission_perc' 	=> $rtwwwap_level_comm_amount,
							    					'prod_commission' 	=> $rtwwwap_prod_comm
							    				);

						    		$rtwwwap_total_commission += $rtwwwap_prod_comm;
								}
								else{
									$rtwwwap_prod_comm = $rtwwwap_level_comm_amount;
									$rtwwwap_aff_prod_details[] = array(
							    					'product_id' 		=> $rtwwwap_product_id,
							    					'product_price' 	=> $rtwwwap_product_price,
							    					'commission_fix' 	=> $rtwwwap_level_comm_amount,
							    					'commission_perc' 	=> 'user',
							    					'prod_commission' 	=> $rtwwwap_prod_comm
							    				);

						    		$rtwwwap_total_commission += $rtwwwap_prod_comm;
								}
							}
							elseif( $rtwwwap_commission_type == 0 )
							{
								if( $rtwwwap_level_comm_type == 0 ){
									$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_level_comm_amount ) / 100;
									$rtwwwap_aff_prod_details[] = array(
							    					'product_id' 		=> $rtwwwap_product_id,
							    					'product_price' 	=> $rtwwwap_product_price,
							    					'commission_fix' 	=> 'user',
							    					'commission_perc' 	=> $rtwwwap_level_comm_amount,
							    					'prod_commission' 	=> $rtwwwap_prod_comm
							    				);

						    		$rtwwwap_total_commission += $rtwwwap_prod_comm;
								}
								else{
									$rtwwwap_prod_comm = $rtwwwap_level_comm_amount;
									$rtwwwap_aff_prod_details[] = array(
							    					'product_id' 		=> $rtwwwap_product_id,
							    					'product_price' 	=> $rtwwwap_product_price,
							    					'commission_fix' 	=> 'user',
							    					'commission_perc' 	=> $rtwwwap_level_comm_amount,
							    					'prod_commission' 	=> $rtwwwap_prod_comm
							    				);

						    		$rtwwwap_total_commission += $rtwwwap_prod_comm;
								}
							}
						}
					}
				}

				if( isset( $rtwwwap_total_commission ) && $rtwwwap_total_commission !== '' && $rtwwwap_total_commission !== 0 ){

					$rtwwwap_curr_user_id = get_current_user_id();
					$rtwwwap_order_status = $rtwwwap_order->get_data()['status'];

					$qulification_extend_check = false;
					if(in_array('addon_for_MLM_qualification/affiliate_mlm_addon.php', apply_filters('active_plugins', get_option('active_plugins') ) ) ){
						$qulification_extend_check = apply_filters('rtwwwap_extend_mlm_qualification',$order_total,$rtwwwap_curr_user_id,$rtwwwap_order_status,$current_date);
					}
					if($qulification_extend_check){
						update_user_meta($rtwwwap_curr_user_id, 'rtwwwap_aff_qualify',$qulification_extend_check);
					}

					$rtwwwap_capped 		= 0;
					$rtwwwap_current_year 	= date("Y");
					$rtwwwap_current_month 	= date("m");

					$rtwwwap_commission_settings	= get_option( 'rtwwwap_commission_settings_opt' );
					$rtwwwap_max_comm 				= isset( $rtwwwap_commission_settings[ 'max_commission' ] ) ? $rtwwwap_commission_settings[ 'max_commission' ] : '0';

					if( $rtwwwap_max_comm != 0 )
					{
						$rtwwwap_month_commission 	= $wpdb->get_var( $wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE YEAR(date)=%d AND MONTH(date)=%d AND `aff_id`=%d", $rtwwwap_current_year, $rtwwwap_current_month, $rtwwwap_user_id ) );
						$rtwwwap_month_commission 	= isset( $rtwwwap_month_commission ) ? $rtwwwap_month_commission : 0;

						if( $rtwwwap_month_commission < $rtwwwap_max_comm ){
							$rtwwwap_this_month_left = $rtwwwap_max_comm - $rtwwwap_month_commission;
							if( $rtwwwap_this_month_left < $rtwwwap_total_commission ){
								$rtwwwap_total_commission = $rtwwwap_this_month_left;
							}
							else{
								$rtwwwap_total_commission = $rtwwwap_total_commission;
							}
						}
						else{
							$rtwwwap_capped = 1;
						}
					}

					// inserting into DB
					if( !empty( $rtwwwap_aff_prod_details ) ){
						if( get_user_meta( $rtwwwap_user_id, 'rtwwwap_referral_mail', true ) == 'on' ){
							$rtwwwap_decimal_places = $rtwwwap_extra_features['decimal_places'].'f';
							$rtwwwap_to 			= get_user_by( 'id', $rtwwwap_user_id );
							$rtwwwap_to 			= esc_html( $rtwwwap_to->user_email );

							$all_emails = get_option('customize_email', false);

							$generate_commission = get_option('generate_commission','null');

							if(isset($all_emails['Email on Generating Commission']['subject'])){
								$rtwwwap_subject_text = $all_emails['Email on Generating Commission']['subject'];
								$rtwwwap_message_text = $all_emails['Email on Generating Commission']['content'];
							}

							$rtwwwap_subject 		= esc_html__( $rtwwwap_subject_text, 'rtwwwap-wp-wc-affiliate-program' );
							$rtwwwap_message 		= sprintf( '%s %s%01.'.$rtwwwap_decimal_places, esc_html__( $rtwwwap_message_text, 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_currency_sym, $rtwwwap_total_commission );
							$rtwwwap_from_name 		= esc_html( get_bloginfo( 'name' ) );
							$rtwwwap_from_email 	= esc_html( get_bloginfo( 'admin_email' ) );

							$rtwwwap_headers[] 		= 'Content-Type: text/html; charset=utf-8';
							$rtwwwap_headers[] 		= sprintf( '%s: %s <%s>', esc_html__( 'From', 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_from_name, $rtwwwap_from_email );

							// mail to affiliate
							if($generate_commission){
								wp_mail( $rtwwwap_to, $rtwwwap_subject, $rtwwwap_message, $rtwwwap_headers );
							}

							if( isset( $rtwwwap_extra_features[ 'mail_to_admin' ] ) && $rtwwwap_extra_features[ 'mail_to_admin' ] == 1 ){
								// mail to admin
								$rtwwwap_message = sprintf( '%s %s%01.'.$rtwwwap_decimal_places, esc_html__( 'Generated a new referral of amount', 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_currency_sym, $rtwwwap_total_commission );
								wp_mail( $rtwwwap_from_email, $rtwwwap_subject, $rtwwwap_message, $rtwwwap_headers );
							}
						}

						$rtwwwap_aff_prod_details = json_encode( $rtwwwap_aff_prod_details );
						$rtwwwap_device = ( wp_is_mobile() ) ? 'mobile' : 'desktop';

						$rtwwwap_locale = get_locale();
						setlocale( LC_NUMERIC, $rtwwwap_locale );

						$rtwwwap_updated = $wpdb->insert(
				            $wpdb->prefix.'rtwwwap_referrals',
				            array(
				                'aff_id'    			=> $rtwwwap_user_id,
				                'type'    				=> 0,
				                'order_id'    			=> esc_html( $rtwwwap_order_id ),
				                'date'    				=> date( 'Y-m-d H:i:s' ),
				                'status'    			=> 0,
				                'amount'    			=> $rtwwwap_total_commission,
				                'capped'    			=> esc_html( $rtwwwap_capped ),
				                'currency'    			=> $rtwwwap_currency,
				                'product_details'   	=> $rtwwwap_aff_prod_details,
				                'device'   				=> $rtwwwap_device
				            )
				        );
				        $rtwwwap_lastid = $wpdb->insert_id;

				        if( $rtwwwap_shared !== false ){
				        	$rtwwwap_share_commission = 0;
							$rtwwwap_sharing_bonus 	= isset( $rtwwwap_extra_features[ 'sharing_bonus' ] ) ? $rtwwwap_extra_features[ 'sharing_bonus' ] : 0;

							if( $rtwwwap_sharing_bonus ){
								$rtwwwap_sharing_bonus_time_limit = isset( $rtwwwap_extra_features[ 'sharing_bonus_time_limit' ] ) ? $rtwwwap_extra_features[ 'sharing_bonus_time_limit' ] : 0;

								$rtwwwap_sharing_bonus_amount_limit = isset( $rtwwwap_extra_features[ 'sharing_bonus_amount_limit' ] ) ? $rtwwwap_extra_features[ 'sharing_bonus_amount_limit' ] : 0;


								if( $rtwwwap_sharing_bonus_time_limit == 0 ){
									$rtwwwap_share_commission = $rtwwwap_sharing_bonus;
								}
								elseif( $rtwwwap_sharing_bonus_time_limit == 1 ){
									$rtwwwap_current_day = date( 'Y-m-d' );

									$rtwwwap_daily_old_bonus = $wpdb->get_var( $wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE DATE(date)=%s AND `aff_id`=%d", $rtwwwap_current_day, $rtwwwap_user_id ) );

									if( $rtwwwap_daily_old_bonus < $rtwwwap_sharing_bonus_amount_limit )
									{
										$rtwwwap_left_amount = $rtwwwap_sharing_bonus_amount_limit - $rtwwwap_daily_old_bonus;

										if( $rtwwwap_left_amount < $rtwwwap_sharing_bonus ){
											$rtwwwap_share_commission = $rtwwwap_left_amount;
										}
										else{
											$rtwwwap_share_commission = $rtwwwap_sharing_bonus;
										}
									}
								}
								elseif( $rtwwwap_sharing_bonus_time_limit == 2 ){
									$rtwwwap_current_week = date('W');

									$rtwwwap_weekly_old_bonus = $wpdb->get_var( $wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE WEEK(`date`,1)=%d AND `aff_id`=%d", $rtwwwap_current_week, $rtwwwap_user_id ) );

									if( $rtwwwap_weekly_old_bonus < $rtwwwap_sharing_bonus_amount_limit )
									{
										$rtwwwap_left_amount = $rtwwwap_sharing_bonus_amount_limit - $rtwwwap_weekly_old_bonus;

										if( $rtwwwap_left_amount < $rtwwwap_sharing_bonus ){
											$rtwwwap_share_commission = $rtwwwap_left_amount;
										}
										else{
											$rtwwwap_share_commission = $rtwwwap_sharing_bonus;
										}
									}
								}
								elseif( $rtwwwap_sharing_bonus_time_limit == 3 ){
									$rtwwwap_current_month = date('m');

									$rtwwwap_monthly_old_bonus = $wpdb->get_var( $wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE MONTH(date)=%d AND `aff_id`=%d", $rtwwwap_current_month, $rtwwwap_user_id ) );

									if( $rtwwwap_monthly_old_bonus < $rtwwwap_sharing_bonus_amount_limit )
									{
										$rtwwwap_left_amount = $rtwwwap_sharing_bonus_amount_limit - $rtwwwap_monthly_old_bonus;

										if( $rtwwwap_left_amount < $rtwwwap_sharing_bonus ){
											$rtwwwap_share_commission = $rtwwwap_left_amount;
										}
										else{
											$rtwwwap_share_commission = $rtwwwap_sharing_bonus;
										}
									}
								}

								if( $rtwwwap_commission_type == 1 ){
									if( !$rtwwwap_product_url ){
										$rtwwwap_share_commission = 0;
									}
								}

								if( $rtwwwap_share_commission ){
									$rtwwwap_share_bonus = $wpdb->insert(
							            $wpdb->prefix.'rtwwwap_referrals',
							            array(
							                'aff_id'    			=> $rtwwwap_user_id,
							                'type'    				=> 5,
							                'order_id'    			=> esc_html( $rtwwwap_order_id ),
							                'date'    				=> date( 'Y-m-d H:i:s' ),
							                'status'    			=> 0,
							                'amount'    			=> $rtwwwap_share_commission,
							                'capped'    			=> esc_html( $rtwwwap_capped ),
							                'currency'    			=> $rtwwwap_currency,
							                'product_details'   	=> '',
							                'device'   				=> $rtwwwap_device
							            )
							        );
								}
							}
				        }

				        setlocale( LC_ALL, $rtwwwap_locale );

				        if( $rtwwwap_updated ){
				        	unset( $_COOKIE[ 'rtwwwap_referral' ] );
					        $rtwwwap_referral_noti = get_option( 'rtwwwap_referral_noti' )+1;
					        update_option( 'rtwwwap_referral_noti', $rtwwwap_referral_noti );
						}

						$rtwwwap_mlm = get_option( 'rtwwwap_mlm_opt' );
						if( isset( $rtwwwap_mlm[ 'activate' ] ) && $rtwwwap_mlm[ 'activate' ] == 1 )
						{
							$rtwwwap_child = isset( $rtwwwap_mlm[ 'child' ] ) ? $rtwwwap_mlm[ 'child' ] : 1;
							$rtwwwap_check_have_child = $this->rtwwwap_check_child_in_mlm( $rtwwwap_user_id, $rtwwwap_child );
							
							$rtwwwap_mlm_com_base = isset($rtwwwap_mlm['mlm_commission_base']) ? $rtwwwap_mlm['mlm_commission_base'] : 1;


							if($rtwwwap_mlm_com_base == 0)
							{
								$rtwwwap_total_commission = array_sum($rtwwwap_aff_prod_price);
							}		
							if( $rtwwwap_check_have_child ){
								$this->rtwwwap_give_mlm_comm( $rtwwwap_user_id, $rtwwwap_lastid, $rtwwwap_total_commission, $rtwwwap_currency, $rtwwwap_currency_sym, $rtwwwap_device, $rtwwwap_mlm[ 'mlm_levels' ], $rtwwwap_child, $rtwwwap_order_id, $order_total,$current_date );
							}
						}
					}
				}
			}
		}
	}

	/*
	* To create successful referral for easy digital downloads 
	*/
	function rtwwwap_referred_item_ordered_easy( $rtwwwap_order_id ){ 
		$rtwwwap_verification_done = get_option( 'rtwwwap_verification_done', array() );
		$rtwwwap_verification_done_status = isset($rtwwwap_verification_done['status']) ? $rtwwwap_verification_done['status'] : false;
		$rtwwwap_verification_done_purchase = isset($rtwwwap_verification_done['purchase_code']) ? $rtwwwap_verification_done['purchase_code'] : false;
		if( empty( $rtwwwap_verification_done ) || $rtwwwap_verification_done_status == false || empty($rtwwwap_verification_done_purchase) )
		{
			return;
		}
		//referral code
		$rtwwwap_extra_features 	= get_option( 'rtwwwap_extra_features_opt' );
		$rtwwwap_signup_bonus_type 	= isset( $rtwwwap_extra_features[ 'signup_bonus_type' ] ) ? esc_html( $rtwwwap_extra_features[ 'signup_bonus_type' ] ) : 0;

		//mlm
		$rtwwwap_mlm 		= get_option( 'rtwwwap_mlm_opt' );
		$rtwwwap_mlm_active	= isset( $rtwwwap_mlm[ 'activate' ] ) ? $rtwwwap_mlm[ 'activate' ] : 0;



		//lifetime
		$rtwwwap_commission_settings = get_option( 'rtwwwap_commission_settings_opt' );
		$rtwwwap_unlimit_comm = isset( $rtwwwap_commission_settings[ 'unlimit_comm' ] ) ? $rtwwwap_commission_settings[ 'unlimit_comm' ] : '0';

		if( $rtwwwap_signup_bonus_type == 1 && $rtwwwap_mlm_active ){
			$this->rtwwwap_referral_code_comm_easy( $rtwwwap_order_id );
		}
		elseif( isset( $_COOKIE[ 'rtwwwap_referral' ] ) || $rtwwwap_unlimit_comm == 1 )
		{
			global $wpdb;
			$rtwwwap_referrer_id = 0;
			if( $rtwwwap_unlimit_comm == '1' ){
				$rtwwwap_current_user_id = get_current_user_id();

				if( $rtwwwap_current_user_id ){
					$rtwwwap_referrer_id = get_user_meta( $rtwwwap_current_user_id, 'rtwwwap_lifetime_user_id', true );
					if( !$rtwwwap_referrer_id )
					{
						$rtwwwap_referral 	= explode( '#', $_COOKIE[ 'rtwwwap_referral' ] );
						$rtwwwap_affiliate_id 			= esc_html( $rtwwwap_referral[ 0 ] );
						update_user_meta( $rtwwwap_current_user_id, 'rtwwwap_lifetime_user_id', $rtwwwap_affiliate_id );
						$rtwwwap_referrer_id = $rtwwwap_affiliate_id;
					}
				}
			}

			if( $rtwwwap_referrer_id ){
				$this->rtwwwap_unlimited_reff_comm_easy( $rtwwwap_order_id, $rtwwwap_referrer_id );
			}
			elseif( isset( $_COOKIE[ 'rtwwwap_referral' ] ) ){
				$rtwwwap_referral 	= explode( '#', $_COOKIE[ 'rtwwwap_referral' ] );
				$rtwwwap_order 		= edd_get_payment( $rtwwwap_order_id );
				$rtwwwap_comm_base 	= isset( $rtwwwap_commission_settings[ 'comm_base' ] ) ? $rtwwwap_commission_settings[ 'comm_base' ] : '1';
				$rtwwwap_total_commission	= 0;
				$rtwwwap_aff_prod_details 	= array();
				$rtwwwap_user_id 			= esc_html( $rtwwwap_referral[ 0 ] );

				if( RTWWWAP_IS_WOO == 1 ){
					$rtwwwap_currency 		= get_woocommerce_currency();
					$rtwwwap_currency_sym 	= get_woocommerce_currency_symbol();
				}
				else{
					require_once( RTWWWAP_DIR.'includes/rtwaffiliatehelper.php' );

					$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );
					$rtwwwap_currency 		= isset( $rtwwwap_extra_features[ 'currency' ] ) ? $rtwwwap_extra_features[ 'currency' ] : 'USD';
					$rtwwwap_curr_obj 		= new RtwAffiliateHelper();
					$rtwwwap_currency_sym 	= $rtwwwap_curr_obj->rtwwwap_curr_symbol( $rtwwwap_currency );
				}

				$rtwwwap_commission_type 	= isset( $rtwwwap_commission_settings[ 'only_open_url' ] ) ? $rtwwwap_commission_settings[ 'only_open_url' ] : 0;
				$rtwwwap_shared 			= strpos( $_COOKIE[ 'rtwwwap_referral' ], 'share' );
				$rtwwwap_product_url 		= false;

				if( $rtwwwap_comm_base == 1 ){
					$rtwwwap_per_prod_mode 			= isset( $rtwwwap_commission_settings[ 'per_prod_mode' ] ) ? $rtwwwap_commission_settings[ 'per_prod_mode' ] : 0;
					$rtwwwap_all_commission 		= isset( $rtwwwap_commission_settings[ 'all_commission' ] ) ? $rtwwwap_commission_settings[ 'all_commission' ] : 0;
					$rtwwwap_all_commission_type 	= isset( $rtwwwap_commission_settings[ 'all_commission_type' ] ) ? $rtwwwap_commission_settings[ 'all_commission_type' ] : 'percentage';
					$rtwwwap_per_cat 				= isset( $rtwwwap_commission_settings[ 'per_cat' ] ) ? $rtwwwap_commission_settings[ 'per_cat' ] : array();

					foreach( $rtwwwap_order->cart_details as $rtwwwap_item_key => $rtwwwap_item_values )
					{
						$rtwwwap_prod_comm 		= '';
						$rtwwwap_product_id 	= $rtwwwap_item_values['id'];
						$rtwwwap_product_price	= $rtwwwap_item_values['price'];
						// $rtwwwp_product_category_taxonomy = 'download_category';
						$rtwwwap_product_terms 	= get_the_terms( $rtwwwap_product_id, 'download_category' );
						$rtwwwap_product_cat_id = $rtwwwap_product_terms[0]->term_id;

						if( $rtwwwap_commission_type == 1 && array_key_exists( 1, $rtwwwap_referral ) && ( $rtwwwap_product_id == $rtwwwap_referral[ 1 ] ) )
						{
							$rtwwwap_product_url = true;
							if( $rtwwwap_per_prod_mode == 1 ){
								$rtwwwap_prod_per_comm = get_post_meta( $rtwwwap_product_id, 'rtwwwap_percentage_commission_box', true );

								if( $rtwwwap_prod_per_comm > 0 ){
									$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_prod_per_comm ) / 100;
									$rtwwwap_aff_prod_details[] = array(
							    					'product_id' 		=> $rtwwwap_product_id,
							    					'product_price' 	=> $rtwwwap_product_price,
							    					'commission_fix' 	=> '',
							    					'commission_perc' 	=> $rtwwwap_prod_per_comm,
							    					'prod_commission' 	=> $rtwwwap_prod_comm
							    				);

						    		$rtwwwap_total_commission += $rtwwwap_prod_comm;
								}
								elseif( $rtwwwap_prod_per_comm === '0' ){
									// no commission needs to be generated for this product
								}
								else{
									if( !empty( $rtwwwap_per_cat ) ){
										$rtwwwap_cat_per_comm = 0;
										$rtwwwap_cat_fix_comm = 0;
										$rtwwwap_flag = false;
										foreach( $rtwwwap_per_cat as $rtwwwap_key => $rtwwwap_value ){
											if( in_array( $rtwwwap_product_cat_id, $rtwwwap_value[ 'ids' ] ) ){
												$rtwwwap_cat_per_comm = $rtwwwap_value[ 'cat_percentage_commission' ];
												$rtwwwap_cat_fix_comm = $rtwwwap_value[ 'cat_fixed_commission' ];
												$rtwwwap_flag = true;

												break;
											}
										}
										if( $rtwwwap_flag ){
											if( $rtwwwap_cat_per_comm > 0 ){
												$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_cat_per_comm ) / 100;
											}
											if( $rtwwwap_cat_fix_comm > 0 ){
												$rtwwwap_prod_comm += $rtwwwap_cat_fix_comm;
											}

											if( $rtwwwap_prod_comm != '' ){
												$rtwwwap_aff_prod_details[] = array(
									    					'product_id' 		=> $rtwwwap_product_id,
									    					'product_price' 	=> $rtwwwap_product_price,
									    					'commission_fix' 	=> $rtwwwap_cat_fix_comm,
									    					'commission_perc' 	=> $rtwwwap_cat_per_comm,
									    					'prod_commission' 	=> $rtwwwap_prod_comm
									    				);

								    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
								    		}
										}
										else{
											if( $rtwwwap_all_commission ){
												if( $rtwwwap_all_commission_type == 'percentage' ){
													$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
												}
												elseif( $rtwwwap_all_commission_type == 'fixed' ){
													$rtwwwap_prod_comm += $rtwwwap_all_commission;
												}
												$rtwwwap_aff_prod_details[] = array(
									    					'product_id' 		=> $rtwwwap_product_id,
									    					'product_price' 	=> $rtwwwap_product_price,
									    					'commission_fix' 	=> '',
									    					'commission_perc' 	=> '',
									    					'prod_commission' 	=> $rtwwwap_prod_comm
									    				);

								    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
								    		}
										}
									}
									else{
										if( $rtwwwap_all_commission ){
											if( $rtwwwap_all_commission_type == 'percentage' ){
												$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
											}
											elseif( $rtwwwap_all_commission_type == 'fixed' ){
												$rtwwwap_prod_comm += $rtwwwap_all_commission;
											}
											$rtwwwap_aff_prod_details[] = array(
								    					'product_id' 		=> $rtwwwap_product_id,
								    					'product_price' 	=> $rtwwwap_product_price,
								    					'commission_fix' 	=> '',
									    				'commission_perc' 	=> '',
								    					'prod_commission' 	=> $rtwwwap_prod_comm
								    				);

							    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
							    		}
									}
								}
							}
							elseif( $rtwwwap_per_prod_mode == 2 ){
								$rtwwwap_prod_fix_comm = get_post_meta( $rtwwwap_product_id, 'rtwwwap_fixed_commission_box', true );

								if( $rtwwwap_prod_fix_comm > 0 ){
									$rtwwwap_prod_comm = $rtwwwap_prod_fix_comm;
									$rtwwwap_aff_prod_details[] = array(
							    					'product_id' 		=> $rtwwwap_product_id,
							    					'product_price' 	=> $rtwwwap_product_price,
							    					'commission_fix' 	=> $rtwwwap_prod_fix_comm,
							    					'commission_perc' 	=> '',
							    					'prod_commission' 	=> $rtwwwap_prod_comm
							    				);

						    		$rtwwwap_total_commission += $rtwwwap_prod_comm;
								}
								elseif( $rtwwwap_prod_fix_comm === '0' ){
									// no commission needs to be generated for this product
								}
								else{
									if( !empty( $rtwwwap_per_cat ) ){
										$rtwwwap_cat_per_comm = 0;
										$rtwwwap_cat_fix_comm = 0;
										$rtwwwap_flag = false;
										foreach( $rtwwwap_per_cat as $rtwwwap_key => $rtwwwap_value ){
											if( in_array( $rtwwwap_product_cat_id, $rtwwwap_value[ 'ids' ] ) ){
												$rtwwwap_cat_per_comm = $rtwwwap_value[ 'cat_percentage_commission' ];
												$rtwwwap_cat_fix_comm = $rtwwwap_value[ 'cat_fixed_commission' ];
												$rtwwwap_flag = true;

												break;
											}
										}
										if( $rtwwwap_flag ){
											if( $rtwwwap_cat_per_comm > 0 ){
												$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_cat_per_comm ) / 100;
											}
											if( $rtwwwap_cat_fix_comm > 0 ){
												$rtwwwap_prod_comm += $rtwwwap_cat_fix_comm;
											}

											if( $rtwwwap_prod_comm != '' ){
												$rtwwwap_aff_prod_details[] = array(
									    					'product_id' 		=> $rtwwwap_product_id,
									    					'product_price' 	=> $rtwwwap_product_price,
									    					'commission_fix' 	=> $rtwwwap_cat_fix_comm,
									    					'commission_perc' 	=> $rtwwwap_cat_per_comm,
									    					'prod_commission' 	=> $rtwwwap_prod_comm
									    				);

								    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
								    		}
										}
										else{
											if( $rtwwwap_all_commission ){
												if( $rtwwwap_all_commission_type == 'percentage' ){
													$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
												}
												elseif( $rtwwwap_all_commission_type == 'fixed' ){
													$rtwwwap_prod_comm += $rtwwwap_all_commission;
												}
												$rtwwwap_aff_prod_details[] = array(
									    					'product_id' 		=> $rtwwwap_product_id,
									    					'product_price' 	=> $rtwwwap_product_price,
									    					'commission_fix' 	=> '',
									    					'commission_perc' 	=> '',
									    					'prod_commission' 	=> $rtwwwap_prod_comm
									    				);

								    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
								    		}
										}
									}
									else{
										if( $rtwwwap_all_commission ){
											if( $rtwwwap_all_commission_type == 'percentage' ){
												$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
											}
											elseif( $rtwwwap_all_commission_type == 'fixed' ){
												$rtwwwap_prod_comm += $rtwwwap_all_commission;
											}
											$rtwwwap_aff_prod_details[] = array(
								    					'product_id' 		=> $rtwwwap_product_id,
								    					'product_price' 	=> $rtwwwap_product_price,
								    					'commission_fix' 	=> '',
									    				'commission_perc' 	=> '',
								    					'prod_commission' 	=> $rtwwwap_prod_comm
								    				);

							    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
							    		}
									}
								}
							}
							elseif( $rtwwwap_per_prod_mode == 3 ){
								$rtwwwap_prod_per_comm = get_post_meta( $rtwwwap_product_id, 'rtwwwap_percentage_commission_box', true );
								$rtwwwap_prod_fix_comm = get_post_meta( $rtwwwap_product_id, 'rtwwwap_fixed_commission_box', true );

								if( $rtwwwap_prod_per_comm > 0 ){
									$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_prod_per_comm ) / 100;
								}

								if( $rtwwwap_prod_fix_comm > 0 ){
									$rtwwwap_prod_comm += $rtwwwap_prod_fix_comm;
								}

								if( $rtwwwap_prod_comm === '' ){
									if( $rtwwwap_prod_per_comm !== '0' && $rtwwwap_prod_fix_comm !== '0' ){
										if( !empty( $rtwwwap_per_cat ) ){
											$rtwwwap_cat_per_comm = 0;
											$rtwwwap_cat_fix_comm = 0;
											$rtwwwap_flag = false;
											foreach( $rtwwwap_per_cat as $rtwwwap_key => $rtwwwap_value ){
												if( in_array( $rtwwwap_product_cat_id, $rtwwwap_value[ 'ids' ] ) ){
													$rtwwwap_cat_per_comm = $rtwwwap_value[ 'cat_percentage_commission' ];
													$rtwwwap_cat_fix_comm = $rtwwwap_value[ 'cat_fixed_commission' ];
													$rtwwwap_flag = true;

													break;
												}
											}
											if( $rtwwwap_flag ){
												if( $rtwwwap_cat_per_comm > 0 ){
													$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_cat_per_comm ) / 100;
												}
												if( $rtwwwap_cat_fix_comm > 0 ){
													$rtwwwap_prod_comm += $rtwwwap_cat_fix_comm;
												}

												if( $rtwwwap_prod_comm != '' ){
													$rtwwwap_aff_prod_details[] = array(
										    					'product_id' 		=> $rtwwwap_product_id,
										    					'product_price' 	=> $rtwwwap_product_price,
										    					'commission_fix' 	=> $rtwwwap_cat_fix_comm,
										    					'commission_perc' 	=> $rtwwwap_cat_per_comm,
										    					'prod_commission' 	=> $rtwwwap_prod_comm
										    				);

									    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
									    		}
											}
											else{
												if( $rtwwwap_all_commission ){
													if( $rtwwwap_all_commission_type == 'percentage' ){
														$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
													}
													elseif( $rtwwwap_all_commission_type == 'fixed' ){
														$rtwwwap_prod_comm += $rtwwwap_all_commission;
													}
													$rtwwwap_aff_prod_details[] = array(
										    					'product_id' 		=> $rtwwwap_product_id,
										    					'product_price' 	=> $rtwwwap_product_price,
										    					'commission_fix' 	=> '',
										    					'commission_perc' 	=> '',
										    					'prod_commission' 	=> $rtwwwap_prod_comm
										    				);

									    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
									    		}
											}
										}
										else{
											if( $rtwwwap_all_commission ){
												if( $rtwwwap_all_commission_type == 'percentage' ){
													$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
												}
												elseif( $rtwwwap_all_commission_type == 'fixed' ){
													$rtwwwap_prod_comm += $rtwwwap_all_commission;
												}
												$rtwwwap_aff_prod_details[] = array(
									    					'product_id' 		=> $rtwwwap_product_id,
									    					'product_price' 	=> $rtwwwap_product_price,
									    					'commission_fix' 	=> '',
										    				'commission_perc' 	=> '',
									    					'prod_commission' 	=> $rtwwwap_prod_comm
									    				);

								    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
								    		}
										}
									}
								}
								else{
									$rtwwwap_aff_prod_details[] = array(
						    					'product_id' 		=> $rtwwwap_product_id,
						    					'product_price' 	=> $rtwwwap_product_price,
						    					'commission_fix' 	=> $rtwwwap_prod_fix_comm,
							    				'commission_perc' 	=> $rtwwwap_prod_per_comm,
						    					'prod_commission' 	=> $rtwwwap_prod_comm
						    				);

					    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
								}
							}
							elseif( $rtwwwap_all_commission ){
								if( $rtwwwap_all_commission_type == 'percentage' ){
									$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
								}
								elseif( $rtwwwap_all_commission_type == 'fixed' ){
									$rtwwwap_prod_comm += $rtwwwap_all_commission;
								}
								$rtwwwap_aff_prod_details[] = array(
					    					'product_id' 		=> $rtwwwap_product_id,
					    					'product_price' 	=> $rtwwwap_product_price,
					    					'commission_fix' 	=> '',
						    				'commission_perc' 	=> '',
					    					'prod_commission' 	=> $rtwwwap_prod_comm
					    				);

				    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
				    		}
						}
						elseif( $rtwwwap_commission_type == 0 )
						{
						    if( $rtwwwap_per_prod_mode == 1 ){
								$rtwwwap_prod_per_comm = get_post_meta( $rtwwwap_product_id, 'rtwwwap_percentage_commission_box', true );

								if( $rtwwwap_prod_per_comm > 0 ){
									$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_prod_per_comm ) / 100;
									$rtwwwap_aff_prod_details[] = array(
							    					'product_id' 		=> $rtwwwap_product_id,
							    					'product_price' 	=> $rtwwwap_product_price,
							    					'commission_fix' 	=> '',
							    					'commission_perc' 	=> $rtwwwap_prod_per_comm,
							    					'prod_commission' 	=> $rtwwwap_prod_comm
							    				);

						    		$rtwwwap_total_commission += $rtwwwap_prod_comm;
								}
								elseif( $rtwwwap_prod_per_comm === '0' ){
									// no commission needs to be generated for this product
								}
								else{
									if( !empty( $rtwwwap_per_cat ) ){
										$rtwwwap_cat_per_comm = 0;
										$rtwwwap_cat_fix_comm = 0;
										$rtwwwap_flag = false;
										foreach( $rtwwwap_per_cat as $rtwwwap_key => $rtwwwap_value ){
											if( in_array( $rtwwwap_product_cat_id, $rtwwwap_value[ 'ids' ] ) ){
												$rtwwwap_cat_per_comm = $rtwwwap_value[ 'cat_percentage_commission' ];
												$rtwwwap_cat_fix_comm = $rtwwwap_value[ 'cat_fixed_commission' ];
												$rtwwwap_flag = true;

												break;
											}
										}
										if( $rtwwwap_flag ){
											if( $rtwwwap_cat_per_comm > 0 ){
												$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_cat_per_comm ) / 100;
											}
											if( $rtwwwap_cat_fix_comm > 0 ){
												$rtwwwap_prod_comm += $rtwwwap_cat_fix_comm;
											}

											if( $rtwwwap_prod_comm != '' ){
												$rtwwwap_aff_prod_details[] = array(
									    					'product_id' 		=> $rtwwwap_product_id,
									    					'product_price' 	=> $rtwwwap_product_price,
									    					'commission_fix' 	=> $rtwwwap_cat_fix_comm,
									    					'commission_perc' 	=> $rtwwwap_cat_per_comm,
									    					'prod_commission' 	=> $rtwwwap_prod_comm
									    				);

								    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
								    		}
										}
										else{
											if( $rtwwwap_all_commission ){
												if( $rtwwwap_all_commission_type == 'percentage' ){
													$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
												}
												elseif( $rtwwwap_all_commission_type == 'fixed' ){
													$rtwwwap_prod_comm += $rtwwwap_all_commission;
												}
												$rtwwwap_aff_prod_details[] = array(
									    					'product_id' 		=> $rtwwwap_product_id,
									    					'product_price' 	=> $rtwwwap_product_price,
									    					'commission_fix' 	=> '',
									    					'commission_perc' 	=> '',
									    					'prod_commission' 	=> $rtwwwap_prod_comm
									    				);

								    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
								    		}
										}
									}
									else{
										if( $rtwwwap_all_commission ){
											if( $rtwwwap_all_commission_type == 'percentage' ){
												$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
											}
											elseif( $rtwwwap_all_commission_type == 'fixed' ){
												$rtwwwap_prod_comm += $rtwwwap_all_commission;
											}
											$rtwwwap_aff_prod_details[] = array(
								    					'product_id' 		=> $rtwwwap_product_id,
								    					'product_price' 	=> $rtwwwap_product_price,
								    					'commission_fix' 	=> '',
									    				'commission_perc' 	=> '',
								    					'prod_commission' 	=> $rtwwwap_prod_comm
								    				);

							    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
							    		}
									}
								}
							}
							elseif( $rtwwwap_per_prod_mode == 2 ){
								$rtwwwap_prod_fix_comm = get_post_meta( $rtwwwap_product_id, 'rtwwwap_fixed_commission_box', true );

								if( $rtwwwap_prod_fix_comm > 0 ){
									$rtwwwap_prod_comm = $rtwwwap_prod_fix_comm;
									$rtwwwap_aff_prod_details[] = array(
							    					'product_id' 		=> $rtwwwap_product_id,
							    					'product_price' 	=> $rtwwwap_product_price,
							    					'commission_fix' 	=> $rtwwwap_prod_fix_comm,
							    					'commission_perc' 	=> '',
							    					'prod_commission' 	=> $rtwwwap_prod_comm
							    				);

						    		$rtwwwap_total_commission += $rtwwwap_prod_comm;
								}
								elseif( $rtwwwap_prod_fix_comm === '0' ){
									// no commission needs to be generated for this product
								}
								else{
									if( !empty( $rtwwwap_per_cat ) ){
										$rtwwwap_cat_per_comm = 0;
										$rtwwwap_cat_fix_comm = 0;
										$rtwwwap_flag = false;
										foreach( $rtwwwap_per_cat as $rtwwwap_key => $rtwwwap_value ){
											if( in_array( $rtwwwap_product_cat_id, $rtwwwap_value[ 'ids' ] ) ){
												$rtwwwap_cat_per_comm = $rtwwwap_value[ 'cat_percentage_commission' ];
												$rtwwwap_cat_fix_comm = $rtwwwap_value[ 'cat_fixed_commission' ];
												$rtwwwap_flag = true;

												break;
											}
										}
										if( $rtwwwap_flag ){
											if( $rtwwwap_cat_per_comm > 0 ){
												$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_cat_per_comm ) / 100;
											}
											if( $rtwwwap_cat_fix_comm > 0 ){
												$rtwwwap_prod_comm += $rtwwwap_cat_fix_comm;
											}

											if( $rtwwwap_prod_comm != '' ){
												$rtwwwap_aff_prod_details[] = array(
									    					'product_id' 		=> $rtwwwap_product_id,
									    					'product_price' 	=> $rtwwwap_product_price,
									    					'commission_fix' 	=> $rtwwwap_cat_fix_comm,
									    					'commission_perc' 	=> $rtwwwap_cat_per_comm,
									    					'prod_commission' 	=> $rtwwwap_prod_comm
									    				);

								    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
								    		}
										}
										else{
											if( $rtwwwap_all_commission ){
												if( $rtwwwap_all_commission_type == 'percentage' ){
													$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
												}
												elseif( $rtwwwap_all_commission_type == 'fixed' ){
													$rtwwwap_prod_comm += $rtwwwap_all_commission;
												}
												$rtwwwap_aff_prod_details[] = array(
									    					'product_id' 		=> $rtwwwap_product_id,
									    					'product_price' 	=> $rtwwwap_product_price,
									    					'commission_fix' 	=> '',
									    					'commission_perc' 	=> '',
									    					'prod_commission' 	=> $rtwwwap_prod_comm
									    				);

								    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
								    		}
										}
									}
									else{
										if( $rtwwwap_all_commission ){
											if( $rtwwwap_all_commission_type == 'percentage' ){
												$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
											}
											elseif( $rtwwwap_all_commission_type == 'fixed' ){
												$rtwwwap_prod_comm += $rtwwwap_all_commission;
											}
											$rtwwwap_aff_prod_details[] = array(
								    					'product_id' 		=> $rtwwwap_product_id,
								    					'product_price' 	=> $rtwwwap_product_price,
								    					'commission_fix' 	=> '',
									    				'commission_perc' 	=> '',
								    					'prod_commission' 	=> $rtwwwap_prod_comm
								    				);

							    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
							    		}
									}
								}
							}
							elseif( $rtwwwap_per_prod_mode == 3 ){
								$rtwwwap_prod_per_comm = get_post_meta( $rtwwwap_product_id, 'rtwwwap_percentage_commission_box', true );
								$rtwwwap_prod_fix_comm = get_post_meta( $rtwwwap_product_id, 'rtwwwap_fixed_commission_box', true );

								if( $rtwwwap_prod_per_comm > 0 ){
									$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_prod_per_comm ) / 100;
								}

								if( $rtwwwap_prod_fix_comm > 0 ){
									$rtwwwap_prod_comm += $rtwwwap_prod_fix_comm;
								}

								if( $rtwwwap_prod_comm === '' ){
									if( $rtwwwap_prod_per_comm !== '0' && $rtwwwap_prod_fix_comm !== '0' ){
										if( !empty( $rtwwwap_per_cat ) ){
											$rtwwwap_cat_per_comm = 0;
											$rtwwwap_cat_fix_comm = 0;
											$rtwwwap_flag = false;
											foreach( $rtwwwap_per_cat as $rtwwwap_key => $rtwwwap_value ){
												if( in_array( $rtwwwap_product_cat_id, $rtwwwap_value[ 'ids' ] ) ){
													$rtwwwap_cat_per_comm = $rtwwwap_value[ 'cat_percentage_commission' ];
													$rtwwwap_cat_fix_comm = $rtwwwap_value[ 'cat_fixed_commission' ];
													$rtwwwap_flag = true;

													break;
												}
											}
											if( $rtwwwap_flag ){
												if( $rtwwwap_cat_per_comm > 0 ){
													$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_cat_per_comm ) / 100;
												}
												if( $rtwwwap_cat_fix_comm > 0 ){
													$rtwwwap_prod_comm += $rtwwwap_cat_fix_comm;
												}

												if( $rtwwwap_prod_comm != '' ){
													$rtwwwap_aff_prod_details[] = array(
										    					'product_id' 		=> $rtwwwap_product_id,
										    					'product_price' 	=> $rtwwwap_product_price,
										    					'commission_fix' 	=> $rtwwwap_cat_fix_comm,
										    					'commission_perc' 	=> $rtwwwap_cat_per_comm,
										    					'prod_commission' 	=> $rtwwwap_prod_comm
										    				);

									    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
									    		}
											}
											else{
												if( $rtwwwap_all_commission ){
													if( $rtwwwap_all_commission_type == 'percentage' ){
														$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
													}
													elseif( $rtwwwap_all_commission_type == 'fixed' ){
														$rtwwwap_prod_comm += $rtwwwap_all_commission;
													}
													$rtwwwap_aff_prod_details[] = array(
										    					'product_id' 		=> $rtwwwap_product_id,
										    					'product_price' 	=> $rtwwwap_product_price,
										    					'commission_fix' 	=> '',
										    					'commission_perc' 	=> '',
										    					'prod_commission' 	=> $rtwwwap_prod_comm
										    				);

									    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
									    		}
											}
										}
										else{
											if( $rtwwwap_all_commission ){
												if( $rtwwwap_all_commission_type == 'percentage' ){
													$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
												}
												elseif( $rtwwwap_all_commission_type == 'fixed' ){
													$rtwwwap_prod_comm += $rtwwwap_all_commission;
												}
												$rtwwwap_aff_prod_details[] = array(
									    					'product_id' 		=> $rtwwwap_product_id,
									    					'product_price' 	=> $rtwwwap_product_price,
									    					'commission_fix' 	=> '',
										    				'commission_perc' 	=> '',
									    					'prod_commission' 	=> $rtwwwap_prod_comm
									    				);

								    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
								    		}
										}
									}
								}
								else{
									$rtwwwap_aff_prod_details[] = array(
						    					'product_id' 		=> $rtwwwap_product_id,
						    					'product_price' 	=> $rtwwwap_product_price,
						    					'commission_fix' 	=> $rtwwwap_prod_fix_comm,
							    				'commission_perc' 	=> $rtwwwap_prod_per_comm,
						    					'prod_commission' 	=> $rtwwwap_prod_comm
						    				);

					    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
								}
							}
							elseif( $rtwwwap_all_commission ){
								if( $rtwwwap_all_commission_type == 'percentage' ){
									$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
								}
								elseif( $rtwwwap_all_commission_type == 'fixed' ){
									$rtwwwap_prod_comm += $rtwwwap_all_commission;
								}
								$rtwwwap_aff_prod_details[] = array(
					    					'product_id' 		=> $rtwwwap_product_id,
					    					'product_price' 	=> $rtwwwap_product_price,
					    					'commission_fix' 	=> '',
						    				'commission_perc' 	=> '',
					    					'prod_commission' 	=> $rtwwwap_prod_comm
					    				);

				    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
				    		}
						}
					}
				}
				else
				{
					$rtwwwap_levels_settings 	= get_option( 'rtwwwap_levels_settings_opt' );
					$rtwwwap_user_level 		= get_user_meta( $rtwwwap_user_id, 'rtwwwap_affiliate_level', true );
					$rtwwwap_user_level 		= ( $rtwwwap_user_level ) ? $rtwwwap_user_level : '0';

					$rtwwwap_user_level_details = isset( $rtwwwap_levels_settings[ $rtwwwap_user_level ] ) ? $rtwwwap_levels_settings[ $rtwwwap_user_level ] : '';

					if( !empty( $rtwwwap_user_level_details ) ){
						$rtwwwap_level_comm_type 		= $rtwwwap_user_level_details[ 'level_commission_type' ];
						$rtwwwap_level_comm_amount 		= $rtwwwap_user_level_details[ 'level_comm_amount' ];
						$rtwwwap_level_criteria_type 	= $rtwwwap_user_level_details[ 'level_criteria_type' ];
						$rtwwwap_level_criteria_val 	= $rtwwwap_user_level_details[ 'level_criteria_val' ];

						foreach( $rtwwwap_order->cart_details as $rtwwwap_item_key => $rtwwwap_item_values )
						{
							$rtwwwap_prod_comm 		= '';
							$rtwwwap_product_id 	= $rtwwwap_item_values['ID'];
							$rtwwwap_product_price	= $rtwwwap_item_values['price'];

							if( $rtwwwap_commission_type == 1 && array_key_exists( 1, $rtwwwap_referral ) && ( $rtwwwap_product_id == $rtwwwap_referral[ 1 ] ) )
							{
								$rtwwwap_product_url = true;
								if( $rtwwwap_level_comm_type == 0 ){
									$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_level_comm_amount ) / 100;
									$rtwwwap_aff_prod_details[] = array(
							    					'product_id' 		=> $rtwwwap_product_id,
							    					'product_price' 	=> $rtwwwap_product_price,
							    					'commission_fix' 	=> 'user',
							    					'commission_perc' 	=> $rtwwwap_level_comm_amount,
							    					'prod_commission' 	=> $rtwwwap_prod_comm
							    				);

						    		$rtwwwap_total_commission += $rtwwwap_prod_comm;
								}
								else{
									$rtwwwap_prod_comm = $rtwwwap_level_comm_amount;
									$rtwwwap_aff_prod_details[] = array(
							    					'product_id' 		=> $rtwwwap_product_id,
							    					'product_price' 	=> $rtwwwap_product_price,
							    					'commission_fix' 	=> $rtwwwap_level_comm_amount,
							    					'commission_perc' 	=> 'user',
							    					'prod_commission' 	=> $rtwwwap_prod_comm
							    				);

						    		$rtwwwap_total_commission += $rtwwwap_prod_comm;
								}
							}
							elseif( $rtwwwap_commission_type == 0 )
							{
								if( $rtwwwap_level_comm_type == 0 ){
									$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_level_comm_amount ) / 100;
									$rtwwwap_aff_prod_details[] = array(
							    					'product_id' 		=> $rtwwwap_product_id,
							    					'product_price' 	=> $rtwwwap_product_price,
							    					'commission_fix' 	=> 'user',
							    					'commission_perc' 	=> $rtwwwap_level_comm_amount,
							    					'prod_commission' 	=> $rtwwwap_prod_comm
							    				);

						    		$rtwwwap_total_commission += $rtwwwap_prod_comm;
								}
								else{
									$rtwwwap_prod_comm = $rtwwwap_level_comm_amount;
									$rtwwwap_aff_prod_details[] = array(
							    					'product_id' 		=> $rtwwwap_product_id,
							    					'product_price' 	=> $rtwwwap_product_price,
							    					'commission_fix' 	=> 'user',
							    					'commission_perc' 	=> $rtwwwap_level_comm_amount,
							    					'prod_commission' 	=> $rtwwwap_prod_comm
							    				);

						    		$rtwwwap_total_commission += $rtwwwap_prod_comm;
								}
							}
						}
					}
				}

				if( isset( $rtwwwap_total_commission ) && $rtwwwap_total_commission !== '' && $rtwwwap_total_commission !== 0 ){
					$rtwwwap_capped 		= 0;
					$rtwwwap_current_year 	= date("Y");
					$rtwwwap_current_month 	= date("m");

					$rtwwwap_commission_settings	= get_option( 'rtwwwap_commission_settings_opt' );
					$rtwwwap_max_comm 				= isset( $rtwwwap_commission_settings[ 'max_commission' ] ) ? $rtwwwap_commission_settings[ 'max_commission' ] : '0';

					if( $rtwwwap_max_comm != 0 )
					{
						$rtwwwap_month_commission 	= $wpdb->get_var( $wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE YEAR(date)=%d AND MONTH(date)=%d AND `aff_id`=%d", $rtwwwap_current_year, $rtwwwap_current_month, $rtwwwap_user_id ) );
						$rtwwwap_month_commission 	= isset( $rtwwwap_month_commission ) ? $rtwwwap_month_commission : 0;

						if( $rtwwwap_month_commission < $rtwwwap_max_comm ){
							$rtwwwap_this_month_left = $rtwwwap_max_comm - $rtwwwap_month_commission;
							if( $rtwwwap_this_month_left < $rtwwwap_total_commission ){
								$rtwwwap_total_commission = $rtwwwap_this_month_left;
							}
							else{
								$rtwwwap_total_commission = $rtwwwap_total_commission;
							}
						}
						else{
							$rtwwwap_capped = 1;
						}
					}

					// inserting into DB
					if( !empty( $rtwwwap_aff_prod_details ) ){
						if( get_user_meta( $rtwwwap_user_id, 'rtwwwap_referral_mail', true ) == 'on' ){
							$rtwwwap_decimal_places = $rtwwwap_extra_features['decimal_places'].'f';
							$rtwwwap_to 			= get_user_by( 'id', $rtwwwap_user_id );
							$rtwwwap_to 			= esc_html( $rtwwwap_to->user_email );

							$all_emails = get_option('customize_email', false);

							$generate_commission = get_option('generate_commission','null');

							if(isset($all_emails['Email on Generating Commission']['subject'])){
								$rtwwwap_subject_text = $all_emails['Email on Generating Commission']['subject'];
								$rtwwwap_message_text = $all_emails['Email on Generating Commission']['content'];
							}

							$rtwwwap_subject 		= esc_html__( $rtwwwap_subject_text, 'rtwwwap-wp-wc-affiliate-program' );
							$rtwwwap_message 		= sprintf( '%s %s%01.'.$rtwwwap_decimal_places, esc_html__( $rtwwwap_message_text , 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_currency_sym, $rtwwwap_total_commission );
							$rtwwwap_from_name 		= esc_html( get_bloginfo( 'name' ) );
							$rtwwwap_from_email 	= esc_html( get_bloginfo( 'admin_email' ) );

							$rtwwwap_headers[] 		= 'Content-Type: text/html; charset=utf-8';
							$rtwwwap_headers[] 		= sprintf( '%s: %s <%s>', esc_html__( 'From', 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_from_name, $rtwwwap_from_email );

							// mail to affiliate
							if($generate_commission == "true"){
								wp_mail( $rtwwwap_to, $rtwwwap_subject, $rtwwwap_message, $rtwwwap_headers );
							}

							if( isset( $rtwwwap_extra_features[ 'mail_to_admin' ] ) && $rtwwwap_extra_features[ 'mail_to_admin' ] == 1 ){
								// mail to admin
								$rtwwwap_message = sprintf( '%s %s%01.'.$rtwwwap_decimal_places, esc_html__( 'Generated a new referral of amount', 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_currency_sym, $rtwwwap_total_commission );
								wp_mail( $rtwwwap_from_email, $rtwwwap_subject, $rtwwwap_message, $rtwwwap_headers );
							}
						}

						$rtwwwap_aff_prod_details = json_encode( $rtwwwap_aff_prod_details );
						$rtwwwap_device = ( wp_is_mobile() ) ? 'mobile' : 'desktop';

						$rtwwwap_locale = get_locale();
						setlocale( LC_NUMERIC, $rtwwwap_locale );

						$rtwwwap_updated = $wpdb->insert(
				            $wpdb->prefix.'rtwwwap_referrals',
				            array(
				                'aff_id'    			=> $rtwwwap_user_id,
				                'type'    				=> 0,
				                'order_id'    			=> esc_html( $rtwwwap_order_id ),
				                'date'    				=> date( 'Y-m-d H:i:s' ),
				                'status'    			=> 0,
				                'amount'    			=> $rtwwwap_total_commission,
				                'capped'    			=> esc_html( $rtwwwap_capped ),
				                'currency'    			=> $rtwwwap_currency,
				                'product_details'   	=> $rtwwwap_aff_prod_details,
				                'device'   				=> $rtwwwap_device
				            )
				        );
				        $rtwwwap_lastid = $wpdb->insert_id;

				        if( $rtwwwap_shared !== false ){
				        	$rtwwwap_share_commission = 0;
							$rtwwwap_sharing_bonus 	= isset( $rtwwwap_extra_features[ 'sharing_bonus' ] ) ? $rtwwwap_extra_features[ 'sharing_bonus' ] : 0;

							if( $rtwwwap_sharing_bonus ){
								$rtwwwap_sharing_bonus_time_limit = isset( $rtwwwap_extra_features[ 'sharing_bonus_time_limit' ] ) ? $rtwwwap_extra_features[ 'sharing_bonus_time_limit' ] : 0;

								$rtwwwap_sharing_bonus_amount_limit = isset( $rtwwwap_extra_features[ 'sharing_bonus_amount_limit' ] ) ? $rtwwwap_extra_features[ 'sharing_bonus_amount_limit' ] : 0;


								if( $rtwwwap_sharing_bonus_time_limit == 0 ){
									$rtwwwap_share_commission = $rtwwwap_sharing_bonus;
								}
								elseif( $rtwwwap_sharing_bonus_time_limit == 1 ){
									$rtwwwap_current_day = date( 'Y-m-d' );

									$rtwwwap_daily_old_bonus = $wpdb->get_var( $wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE DATE(date)=%s AND `aff_id`=%d", $rtwwwap_current_day, $rtwwwap_user_id ) );

									if( $rtwwwap_daily_old_bonus < $rtwwwap_sharing_bonus_amount_limit )
									{
										$rtwwwap_left_amount = $rtwwwap_sharing_bonus_amount_limit - $rtwwwap_daily_old_bonus;

										if( $rtwwwap_left_amount < $rtwwwap_sharing_bonus ){
											$rtwwwap_share_commission = $rtwwwap_left_amount;
										}
										else{
											$rtwwwap_share_commission = $rtwwwap_sharing_bonus;
										}
									}
								}
								elseif( $rtwwwap_sharing_bonus_time_limit == 2 ){
									$rtwwwap_current_week = date('W');

									$rtwwwap_weekly_old_bonus = $wpdb->get_var( $wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE WEEK(`date`,1)=%d AND `aff_id`=%d", $rtwwwap_current_week, $rtwwwap_user_id ) );

									if( $rtwwwap_weekly_old_bonus < $rtwwwap_sharing_bonus_amount_limit )
									{
										$rtwwwap_left_amount = $rtwwwap_sharing_bonus_amount_limit - $rtwwwap_weekly_old_bonus;

										if( $rtwwwap_left_amount < $rtwwwap_sharing_bonus ){
											$rtwwwap_share_commission = $rtwwwap_left_amount;
										}
										else{
											$rtwwwap_share_commission = $rtwwwap_sharing_bonus;
										}
									}
								}
								elseif( $rtwwwap_sharing_bonus_time_limit == 3 ){
									$rtwwwap_current_month = date('m');

									$rtwwwap_monthly_old_bonus = $wpdb->get_var( $wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE MONTH(date)=%d AND `aff_id`=%d", $rtwwwap_current_month, $rtwwwap_user_id ) );

									if( $rtwwwap_monthly_old_bonus < $rtwwwap_sharing_bonus_amount_limit )
									{
										$rtwwwap_left_amount = $rtwwwap_sharing_bonus_amount_limit - $rtwwwap_monthly_old_bonus;

										if( $rtwwwap_left_amount < $rtwwwap_sharing_bonus ){
											$rtwwwap_share_commission = $rtwwwap_left_amount;
										}
										else{
											$rtwwwap_share_commission = $rtwwwap_sharing_bonus;
										}
									}
								}

								if( $rtwwwap_commission_type == 1 ){
									if( !$rtwwwap_product_url ){
										$rtwwwap_share_commission = 0;
									}
								}

								if( $rtwwwap_share_commission ){
									$rtwwwap_share_bonus = $wpdb->insert(
							            $wpdb->prefix.'rtwwwap_referrals',
							            array(
							                'aff_id'    			=> $rtwwwap_user_id,
							                'type'    				=> 5,
							                'order_id'    			=> esc_html( $rtwwwap_order_id ),
							                'date'    				=> date( 'Y-m-d H:i:s' ),
							                'status'    			=> 0,
							                'amount'    			=> $rtwwwap_share_commission,
							                'capped'    			=> esc_html( $rtwwwap_capped ),
							                'currency'    			=> $rtwwwap_currency,
							                'product_details'   	=> '',
							                'device'   				=> $rtwwwap_device
							            )
							        );
								}
							}
				        }

				        setlocale( LC_ALL, $rtwwwap_locale );

				        if( $rtwwwap_updated ){
				        	unset( $_COOKIE[ 'rtwwwap_referral' ] );
					        $rtwwwap_referral_noti = get_option( 'rtwwwap_referral_noti' )+1;
					        update_option( 'rtwwwap_referral_noti', $rtwwwap_referral_noti );
						}

						$rtwwwap_mlm = get_option( 'rtwwwap_mlm_opt' );
						if( isset( $rtwwwap_mlm[ 'activate' ] ) && $rtwwwap_mlm[ 'activate' ] == 1 )
						{
							$rtwwwap_child = isset( $rtwwwap_mlm[ 'child' ] ) ? $rtwwwap_mlm[ 'child' ] : 1;
							$rtwwwap_check_have_child = $this->rtwwwap_check_child_in_mlm( $rtwwwap_user_id, $rtwwwap_child );

							if( $rtwwwap_check_have_child ){
								$this->rtwwwap_give_mlm_comm( $rtwwwap_user_id, $rtwwwap_lastid, $rtwwwap_total_commission, $rtwwwap_currency, $rtwwwap_currency_sym, $rtwwwap_device, $rtwwwap_mlm[ 'mlm_levels' ], $rtwwwap_child, $rtwwwap_order_id,"","");
							}
						}
					}
				}
			}
		}
	}


	/*
	 * Feature to give comm. on referral code
	 */
	function rtwwwap_referral_code_comm( $rtwwwap_order_id ){
		global $wpdb;
		$rtwwwap_current_user_id = get_current_user_id();

		//get parent
		$rtwwwap_parent = $wpdb->get_var( $wpdb->prepare( "SELECT `parent_id` FROM ".$wpdb->prefix."rtwwwap_mlm WHERE `aff_id` = %d AND `status` = %d", $rtwwwap_current_user_id, 1 ) );

		if( $rtwwwap_parent ){
			$rtwwwap_user_id 	= $rtwwwap_parent;
			$rtwwwap_order 		= wc_get_order( $rtwwwap_order_id );
			$rtwwwap_commission_settings = get_option( 'rtwwwap_commission_settings_opt' );
			$rtwwwap_comm_base 	= isset( $rtwwwap_commission_settings[ 'comm_base' ] ) ? $rtwwwap_commission_settings[ 'comm_base' ] : '1';
			$rtwwwap_total_commission	= 0;
			$rtwwwap_aff_prod_details 	= array();
			$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );
			$rtwwwap_decimal_places = $rtwwwap_extra_features['decimal_places'].'f';
			if( RTWWWAP_IS_WOO == 1 ){
				$rtwwwap_currency 		= get_woocommerce_currency();
				$rtwwwap_currency_sym 	= get_woocommerce_currency_symbol();
			}
			else{
				require_once( RTWWWAP_DIR.'includes/rtwaffiliatehelper.php' );

				$rtwwwap_currency 		= isset( $rtwwwap_extra_features[ 'currency' ] ) ? $rtwwwap_extra_features[ 'currency' ] : 'USD';
				$rtwwwap_curr_obj 		= new RtwAffiliateHelper();
				$rtwwwap_currency_sym 	= $rtwwwap_curr_obj->rtwwwap_curr_symbol( $rtwwwap_currency );
			}

			$rtwwwap_commission_type 	= 0;

			if( $rtwwwap_comm_base == 1 ){
				$rtwwwap_per_prod_mode 			= isset( $rtwwwap_commission_settings[ 'per_prod_mode' ] ) ? $rtwwwap_commission_settings[ 'per_prod_mode' ] : 0;
				$rtwwwap_all_commission 		= isset( $rtwwwap_commission_settings[ 'all_commission' ] ) ? $rtwwwap_commission_settings[ 'all_commission' ] : 0;
				$rtwwwap_all_commission_type 	= isset( $rtwwwap_commission_settings[ 'all_commission_type' ] ) ? $rtwwwap_commission_settings[ 'all_commission_type' ] : 'percentage';
				$rtwwwap_per_cat 				= isset( $rtwwwap_commission_settings[ 'per_cat' ] ) ? $rtwwwap_commission_settings[ 'per_cat' ] : array();

				foreach( $rtwwwap_order->get_items() as $rtwwwap_item_key => $rtwwwap_item_values )
				{
					$rtwwwap_prod_comm 		= '';
					$rtwwwap_product_id 	= $rtwwwap_item_values->get_product_id();
					$rtwwwap_product_price	= $rtwwwap_item_values->get_total();
					$rtwwwap_product_terms 	= get_the_terms( $rtwwwap_product_id, 'product_cat' );
					$rtwwwap_product_cat_id = $rtwwwap_product_terms[0]->term_id;

					if( $rtwwwap_commission_type == 0 )
					{
					    if( $rtwwwap_per_prod_mode == 1 ){
							$rtwwwap_prod_per_comm = get_post_meta( $rtwwwap_product_id, 'rtwwwap_percentage_commission_box', true );

							if( $rtwwwap_prod_per_comm > 0 ){
								$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_prod_per_comm ) / 100;
								$rtwwwap_aff_prod_details[] = array(
						    					'product_id' 		=> $rtwwwap_product_id,
						    					'product_price' 	=> $rtwwwap_product_price,
						    					'commission_fix' 	=> '',
						    					'commission_perc' 	=> $rtwwwap_prod_per_comm,
						    					'prod_commission' 	=> $rtwwwap_prod_comm
						    				);

					    		$rtwwwap_total_commission += $rtwwwap_prod_comm;
							}
							elseif( $rtwwwap_prod_per_comm === '0' ){
								// no commission needs to be generated for this product
							}
							else{
								if( !empty( $rtwwwap_per_cat ) ){
									$rtwwwap_cat_per_comm = 0;
									$rtwwwap_cat_fix_comm = 0;
									$rtwwwap_flag = false;
									foreach( $rtwwwap_per_cat as $rtwwwap_key => $rtwwwap_value ){
										if( in_array( $rtwwwap_product_cat_id, $rtwwwap_value[ 'ids' ] ) ){
											$rtwwwap_cat_per_comm = $rtwwwap_value[ 'cat_percentage_commission' ];
											$rtwwwap_cat_fix_comm = $rtwwwap_value[ 'cat_fixed_commission' ];
											$rtwwwap_flag = true;

											break;
										}
									}
									if( $rtwwwap_flag ){
										if( $rtwwwap_cat_per_comm > 0 ){
											$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_cat_per_comm ) / 100;
										}
										if( $rtwwwap_cat_fix_comm > 0 ){
											$rtwwwap_prod_comm += $rtwwwap_cat_fix_comm;
										}

										if( $rtwwwap_prod_comm != '' ){
											$rtwwwap_aff_prod_details[] = array(
								    					'product_id' 		=> $rtwwwap_product_id,
								    					'product_price' 	=> $rtwwwap_product_price,
								    					'commission_fix' 	=> $rtwwwap_cat_fix_comm,
								    					'commission_perc' 	=> $rtwwwap_cat_per_comm,
								    					'prod_commission' 	=> $rtwwwap_prod_comm
								    				);

							    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
							    		}
									}
									else{
										if( $rtwwwap_all_commission ){
											if( $rtwwwap_all_commission_type == 'percentage' ){
												$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
											}
											elseif( $rtwwwap_all_commission_type == 'fixed' ){
												$rtwwwap_prod_comm += $rtwwwap_all_commission;
											}
											$rtwwwap_aff_prod_details[] = array(
								    					'product_id' 		=> $rtwwwap_product_id,
								    					'product_price' 	=> $rtwwwap_product_price,
								    					'commission_fix' 	=> '',
								    					'commission_perc' 	=> '',
								    					'prod_commission' 	=> $rtwwwap_prod_comm
								    				);

							    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
							    		}
									}
								}
								else{
									if( $rtwwwap_all_commission ){
										if( $rtwwwap_all_commission_type == 'percentage' ){
											$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
										}
										elseif( $rtwwwap_all_commission_type == 'fixed' ){
											$rtwwwap_prod_comm += $rtwwwap_all_commission;
										}
										$rtwwwap_aff_prod_details[] = array(
							    					'product_id' 		=> $rtwwwap_product_id,
							    					'product_price' 	=> $rtwwwap_product_price,
							    					'commission_fix' 	=> '',
								    				'commission_perc' 	=> '',
							    					'prod_commission' 	=> $rtwwwap_prod_comm
							    				);

						    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
						    		}
								}
							}
						}
						elseif( $rtwwwap_per_prod_mode == 2 ){
							$rtwwwap_prod_fix_comm = get_post_meta( $rtwwwap_product_id, 'rtwwwap_fixed_commission_box', true );

							if( $rtwwwap_prod_fix_comm > 0 ){
								$rtwwwap_prod_comm = $rtwwwap_prod_fix_comm;
								$rtwwwap_aff_prod_details[] = array(
						    					'product_id' 		=> $rtwwwap_product_id,
						    					'product_price' 	=> $rtwwwap_product_price,
						    					'commission_fix' 	=> $rtwwwap_prod_fix_comm,
						    					'commission_perc' 	=> '',
						    					'prod_commission' 	=> $rtwwwap_prod_comm
						    				);

					    		$rtwwwap_total_commission += $rtwwwap_prod_comm;
							}
							elseif( $rtwwwap_prod_fix_comm === '0' ){
								// no commission needs to be generated for this product
							}
							else{
								if( !empty( $rtwwwap_per_cat ) ){
									$rtwwwap_cat_per_comm = 0;
									$rtwwwap_cat_fix_comm = 0;
									$rtwwwap_flag = false;
									foreach( $rtwwwap_per_cat as $rtwwwap_key => $rtwwwap_value ){
										if( in_array( $rtwwwap_product_cat_id, $rtwwwap_value[ 'ids' ] ) ){
											$rtwwwap_cat_per_comm = $rtwwwap_value[ 'cat_percentage_commission' ];
											$rtwwwap_cat_fix_comm = $rtwwwap_value[ 'cat_fixed_commission' ];
											$rtwwwap_flag = true;

											break;
										}
									}
									if( $rtwwwap_flag ){
										if( $rtwwwap_cat_per_comm > 0 ){
											$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_cat_per_comm ) / 100;
										}
										if( $rtwwwap_cat_fix_comm > 0 ){
											$rtwwwap_prod_comm += $rtwwwap_cat_fix_comm;
										}

										if( $rtwwwap_prod_comm != '' ){
											$rtwwwap_aff_prod_details[] = array(
								    					'product_id' 		=> $rtwwwap_product_id,
								    					'product_price' 	=> $rtwwwap_product_price,
								    					'commission_fix' 	=> $rtwwwap_cat_fix_comm,
								    					'commission_perc' 	=> $rtwwwap_cat_per_comm,
								    					'prod_commission' 	=> $rtwwwap_prod_comm
								    				);

							    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
							    		}
									}
									else{
										if( $rtwwwap_all_commission ){
											if( $rtwwwap_all_commission_type == 'percentage' ){
												$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
											}
											elseif( $rtwwwap_all_commission_type == 'fixed' ){
												$rtwwwap_prod_comm += $rtwwwap_all_commission;
											}
											$rtwwwap_aff_prod_details[] = array(
								    					'product_id' 		=> $rtwwwap_product_id,
								    					'product_price' 	=> $rtwwwap_product_price,
								    					'commission_fix' 	=> '',
								    					'commission_perc' 	=> '',
								    					'prod_commission' 	=> $rtwwwap_prod_comm
								    				);

							    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
							    		}
									}
								}
								else{
									if( $rtwwwap_all_commission ){
										if( $rtwwwap_all_commission_type == 'percentage' ){
											$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
										}
										elseif( $rtwwwap_all_commission_type == 'fixed' ){
											$rtwwwap_prod_comm += $rtwwwap_all_commission;
										}
										$rtwwwap_aff_prod_details[] = array(
							    					'product_id' 		=> $rtwwwap_product_id,
							    					'product_price' 	=> $rtwwwap_product_price,
							    					'commission_fix' 	=> '',
								    				'commission_perc' 	=> '',
							    					'prod_commission' 	=> $rtwwwap_prod_comm
							    				);

						    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
						    		}
								}
							}
						}
						elseif( $rtwwwap_per_prod_mode == 3 ){
							$rtwwwap_prod_per_comm = get_post_meta( $rtwwwap_product_id, 'rtwwwap_percentage_commission_box', true );
							$rtwwwap_prod_fix_comm = get_post_meta( $rtwwwap_product_id, 'rtwwwap_fixed_commission_box', true );

							if( $rtwwwap_prod_per_comm > 0 ){
								$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_prod_per_comm ) / 100;
							}

							if( $rtwwwap_prod_fix_comm > 0 ){
								$rtwwwap_prod_comm += $rtwwwap_prod_fix_comm;
							}

							if( $rtwwwap_prod_comm === '' ){
								if( $rtwwwap_prod_per_comm !== '0' && $rtwwwap_prod_fix_comm !== '0' ){
									if( !empty( $rtwwwap_per_cat ) ){
										$rtwwwap_cat_per_comm = 0;
										$rtwwwap_cat_fix_comm = 0;
										$rtwwwap_flag = false;
										foreach( $rtwwwap_per_cat as $rtwwwap_key => $rtwwwap_value ){
											if( in_array( $rtwwwap_product_cat_id, $rtwwwap_value[ 'ids' ] ) ){
												$rtwwwap_cat_per_comm = $rtwwwap_value[ 'cat_percentage_commission' ];
												$rtwwwap_cat_fix_comm = $rtwwwap_value[ 'cat_fixed_commission' ];
												$rtwwwap_flag = true;

												break;
											}
										}
										if( $rtwwwap_flag ){
											if( $rtwwwap_cat_per_comm > 0 ){
												$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_cat_per_comm ) / 100;
											}
											if( $rtwwwap_cat_fix_comm > 0 ){
												$rtwwwap_prod_comm += $rtwwwap_cat_fix_comm;
											}

											if( $rtwwwap_prod_comm != '' ){
												$rtwwwap_aff_prod_details[] = array(
									    					'product_id' 		=> $rtwwwap_product_id,
									    					'product_price' 	=> $rtwwwap_product_price,
									    					'commission_fix' 	=> $rtwwwap_cat_fix_comm,
									    					'commission_perc' 	=> $rtwwwap_cat_per_comm,
									    					'prod_commission' 	=> $rtwwwap_prod_comm
									    				);

								    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
								    		}
										}
										else{
											if( $rtwwwap_all_commission ){
												if( $rtwwwap_all_commission_type == 'percentage' ){
													$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
												}
												elseif( $rtwwwap_all_commission_type == 'fixed' ){
													$rtwwwap_prod_comm += $rtwwwap_all_commission;
												}
												$rtwwwap_aff_prod_details[] = array(
									    					'product_id' 		=> $rtwwwap_product_id,
									    					'product_price' 	=> $rtwwwap_product_price,
									    					'commission_fix' 	=> '',
									    					'commission_perc' 	=> '',
									    					'prod_commission' 	=> $rtwwwap_prod_comm
									    				);

								    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
								    		}
										}
									}
									else{
										if( $rtwwwap_all_commission ){
											if( $rtwwwap_all_commission_type == 'percentage' ){
												$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
											}
											elseif( $rtwwwap_all_commission_type == 'fixed' ){
												$rtwwwap_prod_comm += $rtwwwap_all_commission;
											}
											$rtwwwap_aff_prod_details[] = array(
								    					'product_id' 		=> $rtwwwap_product_id,
								    					'product_price' 	=> $rtwwwap_product_price,
								    					'commission_fix' 	=> '',
									    				'commission_perc' 	=> '',
								    					'prod_commission' 	=> $rtwwwap_prod_comm
								    				);

							    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
							    		}
									}
								}
							}
							else{
								$rtwwwap_aff_prod_details[] = array(
					    					'product_id' 		=> $rtwwwap_product_id,
					    					'product_price' 	=> $rtwwwap_product_price,
					    					'commission_fix' 	=> $rtwwwap_prod_fix_comm,
						    				'commission_perc' 	=> $rtwwwap_prod_per_comm,
					    					'prod_commission' 	=> $rtwwwap_prod_comm
					    				);

				    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
							}
						}
						elseif( $rtwwwap_all_commission ){
							if( $rtwwwap_all_commission_type == 'percentage' ){
								$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
							}
							elseif( $rtwwwap_all_commission_type == 'fixed' ){
								$rtwwwap_prod_comm += $rtwwwap_all_commission;
							}
							$rtwwwap_aff_prod_details[] = array(
				    					'product_id' 		=> $rtwwwap_product_id,
				    					'product_price' 	=> $rtwwwap_product_price,
				    					'commission_fix' 	=> '',
					    				'commission_perc' 	=> '',
				    					'prod_commission' 	=> $rtwwwap_prod_comm
				    				);

			    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
			    		}
					}
				}
			}
			else
			{
				$rtwwwap_levels_settings 	= get_option( 'rtwwwap_levels_settings_opt' );
				$rtwwwap_user_level 		= get_user_meta( $rtwwwap_user_id, 'rtwwwap_affiliate_level', true );
				$rtwwwap_user_level 		= ( $rtwwwap_user_level ) ? $rtwwwap_user_level : '0';

				$rtwwwap_user_level_details = isset( $rtwwwap_levels_settings[ $rtwwwap_user_level ] ) ? $rtwwwap_levels_settings[ $rtwwwap_user_level ] : '';

				if( !empty( $rtwwwap_user_level_details ) ){
					$rtwwwap_level_comm_type 		= $rtwwwap_user_level_details[ 'level_commission_type' ];
					$rtwwwap_level_comm_amount 		= $rtwwwap_user_level_details[ 'level_comm_amount' ];
					$rtwwwap_level_criteria_type 	= $rtwwwap_user_level_details[ 'level_criteria_type' ];
					$rtwwwap_level_criteria_val 	= $rtwwwap_user_level_details[ 'level_criteria_val' ];

					foreach( $rtwwwap_order->get_items() as $rtwwwap_item_key => $rtwwwap_item_values )
					{
						$rtwwwap_prod_comm 		= '';
						$rtwwwap_product_id 	= $rtwwwap_item_values->get_product_id();
						$rtwwwap_product_price	= $rtwwwap_item_values->get_total();

						if( $rtwwwap_commission_type == 0 )
						{
							if( $rtwwwap_level_comm_type == 0 ){
								$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_level_comm_amount ) / 100;
								$rtwwwap_aff_prod_details[] = array(
						    					'product_id' 		=> $rtwwwap_product_id,
						    					'product_price' 	=> $rtwwwap_product_price,
						    					'commission_fix' 	=> 'user',
						    					'commission_perc' 	=> $rtwwwap_level_comm_amount,
						    					'prod_commission' 	=> $rtwwwap_prod_comm
						    				);

					    		$rtwwwap_total_commission += $rtwwwap_prod_comm;
							}
							else{
								$rtwwwap_prod_comm = $rtwwwap_level_comm_amount;
								$rtwwwap_aff_prod_details[] = array(
						    					'product_id' 		=> $rtwwwap_product_id,
						    					'product_price' 	=> $rtwwwap_product_price,
						    					'commission_fix' 	=> 'user',
						    					'commission_perc' 	=> $rtwwwap_level_comm_amount,
						    					'prod_commission' 	=> $rtwwwap_prod_comm
						    				);

					    		$rtwwwap_total_commission += $rtwwwap_prod_comm;
							}
						}
					}
				}
			}

			if( isset( $rtwwwap_total_commission ) && $rtwwwap_total_commission !== '' && $rtwwwap_total_commission !== 0 ){
				$rtwwwap_capped 		= 0;
				$rtwwwap_current_year 	= date("Y");
				$rtwwwap_current_month 	= date("m");

				$rtwwwap_commission_settings	= get_option( 'rtwwwap_commission_settings_opt' );
				$rtwwwap_max_comm 				= isset( $rtwwwap_commission_settings[ 'max_commission' ] ) ? $rtwwwap_commission_settings[ 'max_commission' ] : '0';

				if( $rtwwwap_max_comm != 0 )
				{
					$rtwwwap_month_commission 	= $wpdb->get_var( $wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE YEAR(date)=%d AND MONTH(date)=%d AND `aff_id`=%d", $rtwwwap_current_year, $rtwwwap_current_month, $rtwwwap_user_id ) );
					$rtwwwap_month_commission 	= isset( $rtwwwap_month_commission ) ? $rtwwwap_month_commission : 0;

					if( $rtwwwap_month_commission < $rtwwwap_max_comm ){
						$rtwwwap_this_month_left = $rtwwwap_max_comm - $rtwwwap_month_commission;
						if( $rtwwwap_this_month_left < $rtwwwap_total_commission ){
							$rtwwwap_total_commission = $rtwwwap_this_month_left;
						}
						else{
							$rtwwwap_total_commission = $rtwwwap_total_commission;
						}
					}
					else{
						$rtwwwap_capped = 1;
					}
				}

				// inserting into DB
				if( !empty( $rtwwwap_aff_prod_details ) ){
					if( get_user_meta( $rtwwwap_user_id, 'rtwwwap_referral_mail', true ) == 'on' ){
						$rtwwwap_to 			= get_user_by( 'id', $rtwwwap_user_id );
						$rtwwwap_to 			= esc_html( $rtwwwap_to->user_email );

						$all_emails = get_option('customize_email', false);

						$generate_commission = get_option('generate_commission','null');

						if(isset($all_emails['Email on Generating Commission']['subject'])){
							$rtwwwap_subject_text = $all_emails['Email on Generating Commission']['subject'];
							$rtwwwap_message_text = $all_emails['Email on Generating Commission']['content'];
						}

						$rtwwwap_subject 		= esc_html__( $rtwwwap_subject_text, 'rtwwwap-wp-wc-affiliate-program' );
						$rtwwwap_message 		= sprintf( '%s %s%01.'.$rtwwwap_decimal_places, esc_html__( $rtwwwap_message_text, 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_currency_sym, $rtwwwap_total_commission );
						$rtwwwap_from_name 		= esc_html( get_bloginfo( 'name' ) );
						$rtwwwap_from_email 	= esc_html( get_bloginfo( 'admin_email' ) );

						$rtwwwap_headers[] 		= 'Content-Type: text/html; charset=utf-8';
						$rtwwwap_headers[] 		= sprintf( '%s: %s <%s>', esc_html__( 'From', 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_from_name, $rtwwwap_from_email );

						// mail to affiliate
						if($generate_commission == "true"){
							wp_mail( $rtwwwap_to, $rtwwwap_subject, $rtwwwap_message, $rtwwwap_headers );
						}

						$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );
						if( isset( $rtwwwap_extra_features[ 'mail_to_admin' ] ) && $rtwwwap_extra_features[ 'mail_to_admin' ] == 1 ){
							// mail to admin
							$rtwwwap_message = sprintf( '%s %s%01.'.$rtwwwap_decimal_places, esc_html__( 'Generated a new referral of amount', 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_currency_sym, $rtwwwap_total_commission );
							wp_mail( $rtwwwap_from_email, $rtwwwap_subject, $rtwwwap_message, $rtwwwap_headers );
						}
					}
				
					$rtwwwap_aff_prod_details = json_encode( $rtwwwap_aff_prod_details );
					$rtwwwap_device = ( wp_is_mobile() ) ? 'mobile' : 'desktop';

					$rtwwwap_locale = get_locale();
					setlocale( LC_NUMERIC, $rtwwwap_locale );

					$rtwwwap_updated = $wpdb->insert(
			            $wpdb->prefix.'rtwwwap_referrals',
			            array(
			                'aff_id'    			=> $rtwwwap_user_id,
			                'type'    				=> 0,
			                'order_id'    			=> esc_html( $rtwwwap_order_id ),
			                'date'    				=> date( 'Y-m-d H:i:s' ),
			                'status'    			=> 0,
			                'amount'    			=> $rtwwwap_total_commission,
			                'capped'    			=> esc_html( $rtwwwap_capped ),
			                'currency'    			=> $rtwwwap_currency,
			                'product_details'   	=> $rtwwwap_aff_prod_details,
			                'device'   				=> $rtwwwap_device
			            )
			        );
			        $rtwwwap_lastid = $wpdb->insert_id;
			        setlocale( LC_ALL, $rtwwwap_locale );

			        if( $rtwwwap_updated ){
				        $rtwwwap_referral_noti = get_option( 'rtwwwap_referral_noti' )+1;
				        update_option( 'rtwwwap_referral_noti', $rtwwwap_referral_noti );
					}

					$rtwwwap_mlm = get_option( 'rtwwwap_mlm_opt' );
					if( isset( $rtwwwap_mlm[ 'activate' ] ) && $rtwwwap_mlm[ 'activate' ] == 1 )
					{
						$rtwwwap_child = isset( $rtwwwap_mlm[ 'child' ] ) ? $rtwwwap_mlm[ 'child' ] : 1;
						$rtwwwap_check_have_child = $this->rtwwwap_check_child_in_mlm( $rtwwwap_user_id, $rtwwwap_child );

						if( $rtwwwap_check_have_child ){
							$this->rtwwwap_give_mlm_comm( $rtwwwap_user_id, $rtwwwap_lastid, $rtwwwap_total_commission, $rtwwwap_currency, $rtwwwap_currency_sym, $rtwwwap_device, $rtwwwap_mlm[ 'mlm_levels' ], $rtwwwap_child, $rtwwwap_order_id,"","");
						}
					}
				}
			}
			elseif( $rtwwwap_total_commission == 0 ){
				$rtwwwap_total_commission = $rtwwwap_order->get_subtotal();
				$rtwwwap_capped 		= 0;
				$rtwwwap_current_year 	= date("Y");
				$rtwwwap_current_month 	= date("m");
				$rtwwwap_device 		= ( wp_is_mobile() ) ? 'mobile' : 'desktop';

				$rtwwwap_commission_settings	= get_option( 'rtwwwap_commission_settings_opt' );
				$rtwwwap_max_comm 				= isset( $rtwwwap_commission_settings[ 'max_commission' ] ) ? $rtwwwap_commission_settings[ 'max_commission' ] : '0';

				$rtwwwap_mlm = get_option( 'rtwwwap_mlm_opt' );
				if( isset( $rtwwwap_mlm[ 'activate' ] ) && $rtwwwap_mlm[ 'activate' ] == 1 )
				{
					$rtwwwap_child = isset( $rtwwwap_mlm[ 'child' ] ) ? $rtwwwap_mlm[ 'child' ] : 1;
					$rtwwwap_check_have_child = $this->rtwwwap_check_child_in_mlm( get_current_user_id(), $rtwwwap_child );

					if( $rtwwwap_check_have_child ){
						$this->rtwwwap_give_mlm_comm( get_current_user_id(), '', $rtwwwap_total_commission, $rtwwwap_currency, $rtwwwap_currency_sym, $rtwwwap_device, $rtwwwap_mlm[ 'mlm_levels' ], $rtwwwap_child, $rtwwwap_order_id,"","" );
					}
				}
			}
		}
	}

	//common code for easy digital downloads

	function rtwwwap_referral_code_comm_easy( $rtwwwap_order_id ){
		global $wpdb;
		$rtwwwap_current_user_id = get_current_user_id();

		//get parent
		$rtwwwap_parent = $wpdb->get_var( $wpdb->prepare( "SELECT `parent_id` FROM ".$wpdb->prefix."rtwwwap_mlm WHERE `aff_id` = %d AND `status` = %d", $rtwwwap_current_user_id, 1 ) );

		if( $rtwwwap_parent ){
			$rtwwwap_user_id 	= $rtwwwap_parent;
			$rtwwwap_order 		= edd_get_payment( $rtwwwap_order_id );
			$rtwwwap_commission_settings = get_option( 'rtwwwap_commission_settings_opt' );
			$rtwwwap_comm_base 	= isset( $rtwwwap_commission_settings[ 'comm_base' ] ) ? $rtwwwap_commission_settings[ 'comm_base' ] : '1';
			$rtwwwap_total_commission	= 0;
			$rtwwwap_aff_prod_details 	= array();
			$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );
			$rtwwwap_decimal_places = $rtwwwap_extra_features['decimal_places'].'f';
			if( RTWWWAP_IS_WOO == 1 ){
				$rtwwwap_currency 		= get_woocommerce_currency();
				$rtwwwap_currency_sym 	= get_woocommerce_currency_symbol();
			}
			else{
				require_once( RTWWWAP_DIR.'includes/rtwaffiliatehelper.php' );

				$rtwwwap_currency 		= isset( $rtwwwap_extra_features[ 'currency' ] ) ? $rtwwwap_extra_features[ 'currency' ] : 'USD';
				$rtwwwap_curr_obj 		= new RtwAffiliateHelper();
				$rtwwwap_currency_sym 	= $rtwwwap_curr_obj->rtwwwap_curr_symbol( $rtwwwap_currency );
			}

			$rtwwwap_commission_type 	= 0;

			if( $rtwwwap_comm_base == 1 ){
				$rtwwwap_per_prod_mode 			= isset( $rtwwwap_commission_settings[ 'per_prod_mode' ] ) ? $rtwwwap_commission_settings[ 'per_prod_mode' ] : 0;
				$rtwwwap_all_commission 		= isset( $rtwwwap_commission_settings[ 'all_commission' ] ) ? $rtwwwap_commission_settings[ 'all_commission' ] : 0;
				$rtwwwap_all_commission_type 	= isset( $rtwwwap_commission_settings[ 'all_commission_type' ] ) ? $rtwwwap_commission_settings[ 'all_commission_type' ] : 'percentage';
				$rtwwwap_per_cat 				= isset( $rtwwwap_commission_settings[ 'per_cat' ] ) ? $rtwwwap_commission_settings[ 'per_cat' ] : array();
				
	
					foreach( $rtwwwap_order->cart_details as $rtwwwap_item_key => $rtwwwap_item_values )
					{
						$rtwwwap_prod_comm 		= '';
						$rtwwwap_product_id 	= $rtwwwap_item_values['ID'];
						$rtwwwap_product_price	= $rtwwwap_item_values['price'];					
						$rtwwwp_product_category_taxonomy = 'download_category';
						$rtwwwap_product_terms 	= get_the_terms( $rtwwwap_product_id, 'download_category' );
						$rtwwwap_product_cat_id = $rtwwwap_product_terms[0]->term_id;

					if( $rtwwwap_commission_type == 0 )
					{
					    if( $rtwwwap_per_prod_mode == 1 ){
							$rtwwwap_prod_per_comm = get_post_meta( $rtwwwap_product_id, 'rtwwwap_percentage_commission_box', true );

							if( $rtwwwap_prod_per_comm > 0 ){
								$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_prod_per_comm ) / 100;
								$rtwwwap_aff_prod_details[] = array(
						    					'product_id' 		=> $rtwwwap_product_id,
						    					'product_price' 	=> $rtwwwap_product_price,
						    					'commission_fix' 	=> '',
						    					'commission_perc' 	=> $rtwwwap_prod_per_comm,
						    					'prod_commission' 	=> $rtwwwap_prod_comm
						    				);

					    		$rtwwwap_total_commission += $rtwwwap_prod_comm;
							}
							elseif( $rtwwwap_prod_per_comm === '0' ){
								// no commission needs to be generated for this product
							}
							else{
								if( !empty( $rtwwwap_per_cat ) ){
									$rtwwwap_cat_per_comm = 0;
									$rtwwwap_cat_fix_comm = 0;
									$rtwwwap_flag = false;
									foreach( $rtwwwap_per_cat as $rtwwwap_key => $rtwwwap_value ){
										if( in_array( $rtwwwap_product_cat_id, $rtwwwap_value[ 'ids' ] ) ){
											$rtwwwap_cat_per_comm = $rtwwwap_value[ 'cat_percentage_commission' ];
											$rtwwwap_cat_fix_comm = $rtwwwap_value[ 'cat_fixed_commission' ];
											$rtwwwap_flag = true;

											break;
										}
									}
									if( $rtwwwap_flag ){
										if( $rtwwwap_cat_per_comm > 0 ){
											$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_cat_per_comm ) / 100;
										}
										if( $rtwwwap_cat_fix_comm > 0 ){
											$rtwwwap_prod_comm += $rtwwwap_cat_fix_comm;
										}

										if( $rtwwwap_prod_comm != '' ){
											$rtwwwap_aff_prod_details[] = array(
								    					'product_id' 		=> $rtwwwap_product_id,
								    					'product_price' 	=> $rtwwwap_product_price,
								    					'commission_fix' 	=> $rtwwwap_cat_fix_comm,
								    					'commission_perc' 	=> $rtwwwap_cat_per_comm,
								    					'prod_commission' 	=> $rtwwwap_prod_comm
								    				);

							    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
							    		}
									}
									else{
										if( $rtwwwap_all_commission ){
											if( $rtwwwap_all_commission_type == 'percentage' ){
												$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
											}
											elseif( $rtwwwap_all_commission_type == 'fixed' ){
												$rtwwwap_prod_comm += $rtwwwap_all_commission;
											}
											$rtwwwap_aff_prod_details[] = array(
								    					'product_id' 		=> $rtwwwap_product_id,
								    					'product_price' 	=> $rtwwwap_product_price,
								    					'commission_fix' 	=> '',
								    					'commission_perc' 	=> '',
								    					'prod_commission' 	=> $rtwwwap_prod_comm
								    				);

							    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
							    		}
									}
								}
								else{
									if( $rtwwwap_all_commission ){
										if( $rtwwwap_all_commission_type == 'percentage' ){
											$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
										}
										elseif( $rtwwwap_all_commission_type == 'fixed' ){
											$rtwwwap_prod_comm += $rtwwwap_all_commission;
										}
										$rtwwwap_aff_prod_details[] = array(
							    					'product_id' 		=> $rtwwwap_product_id,
							    					'product_price' 	=> $rtwwwap_product_price,
							    					'commission_fix' 	=> '',
								    				'commission_perc' 	=> '',
							    					'prod_commission' 	=> $rtwwwap_prod_comm
							    				);

						    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
						    		}
								}
							}
						}
						elseif( $rtwwwap_per_prod_mode == 2 ){
							$rtwwwap_prod_fix_comm = get_post_meta( $rtwwwap_product_id, 'rtwwwap_fixed_commission_box', true );

							if( $rtwwwap_prod_fix_comm > 0 ){
								$rtwwwap_prod_comm = $rtwwwap_prod_fix_comm;
								$rtwwwap_aff_prod_details[] = array(
						    					'product_id' 		=> $rtwwwap_product_id,
						    					'product_price' 	=> $rtwwwap_product_price,
						    					'commission_fix' 	=> $rtwwwap_prod_fix_comm,
						    					'commission_perc' 	=> '',
						    					'prod_commission' 	=> $rtwwwap_prod_comm
						    				);

					    		$rtwwwap_total_commission += $rtwwwap_prod_comm;
							}
							elseif( $rtwwwap_prod_fix_comm === '0' ){
								// no commission needs to be generated for this product
							}
							else{
								if( !empty( $rtwwwap_per_cat ) ){
									$rtwwwap_cat_per_comm = 0;
									$rtwwwap_cat_fix_comm = 0;
									$rtwwwap_flag = false;
									foreach( $rtwwwap_per_cat as $rtwwwap_key => $rtwwwap_value ){
										if( in_array( $rtwwwap_product_cat_id, $rtwwwap_value[ 'ids' ] ) ){
											$rtwwwap_cat_per_comm = $rtwwwap_value[ 'cat_percentage_commission' ];
											$rtwwwap_cat_fix_comm = $rtwwwap_value[ 'cat_fixed_commission' ];
											$rtwwwap_flag = true;

											break;
										}
									}
									if( $rtwwwap_flag ){
										if( $rtwwwap_cat_per_comm > 0 ){
											$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_cat_per_comm ) / 100;
										}
										if( $rtwwwap_cat_fix_comm > 0 ){
											$rtwwwap_prod_comm += $rtwwwap_cat_fix_comm;
										}

										if( $rtwwwap_prod_comm != '' ){
											$rtwwwap_aff_prod_details[] = array(
								    					'product_id' 		=> $rtwwwap_product_id,
								    					'product_price' 	=> $rtwwwap_product_price,
								    					'commission_fix' 	=> $rtwwwap_cat_fix_comm,
								    					'commission_perc' 	=> $rtwwwap_cat_per_comm,
								    					'prod_commission' 	=> $rtwwwap_prod_comm
								    				);

							    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
							    		}
									}
									else{
										if( $rtwwwap_all_commission ){
											if( $rtwwwap_all_commission_type == 'percentage' ){
												$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
											}
											elseif( $rtwwwap_all_commission_type == 'fixed' ){
												$rtwwwap_prod_comm += $rtwwwap_all_commission;
											}
											$rtwwwap_aff_prod_details[] = array(
								    					'product_id' 		=> $rtwwwap_product_id,
								    					'product_price' 	=> $rtwwwap_product_price,
								    					'commission_fix' 	=> '',
								    					'commission_perc' 	=> '',
								    					'prod_commission' 	=> $rtwwwap_prod_comm
								    				);

							    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
							    		}
									}
								}
								else{
									if( $rtwwwap_all_commission ){
										if( $rtwwwap_all_commission_type == 'percentage' ){
											$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
										}
										elseif( $rtwwwap_all_commission_type == 'fixed' ){
											$rtwwwap_prod_comm += $rtwwwap_all_commission;
										}
										$rtwwwap_aff_prod_details[] = array(
							    					'product_id' 		=> $rtwwwap_product_id,
							    					'product_price' 	=> $rtwwwap_product_price,
							    					'commission_fix' 	=> '',
								    				'commission_perc' 	=> '',
							    					'prod_commission' 	=> $rtwwwap_prod_comm
							    				);

						    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
						    		}
								}
							}
						}
						elseif( $rtwwwap_per_prod_mode == 3 ){
							$rtwwwap_prod_per_comm = get_post_meta( $rtwwwap_product_id, 'rtwwwap_percentage_commission_box', true );
							$rtwwwap_prod_fix_comm = get_post_meta( $rtwwwap_product_id, 'rtwwwap_fixed_commission_box', true );

							if( $rtwwwap_prod_per_comm > 0 ){
								$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_prod_per_comm ) / 100;
							}

							if( $rtwwwap_prod_fix_comm > 0 ){
								$rtwwwap_prod_comm += $rtwwwap_prod_fix_comm;
							}

							if( $rtwwwap_prod_comm === '' ){
								if( $rtwwwap_prod_per_comm !== '0' && $rtwwwap_prod_fix_comm !== '0' ){
									if( !empty( $rtwwwap_per_cat ) ){
										$rtwwwap_cat_per_comm = 0;
										$rtwwwap_cat_fix_comm = 0;
										$rtwwwap_flag = false;
										foreach( $rtwwwap_per_cat as $rtwwwap_key => $rtwwwap_value ){
											if( in_array( $rtwwwap_product_cat_id, $rtwwwap_value[ 'ids' ] ) ){
												$rtwwwap_cat_per_comm = $rtwwwap_value[ 'cat_percentage_commission' ];
												$rtwwwap_cat_fix_comm = $rtwwwap_value[ 'cat_fixed_commission' ];
												$rtwwwap_flag = true;

												break;
											}
										}
										if( $rtwwwap_flag ){
											if( $rtwwwap_cat_per_comm > 0 ){
												$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_cat_per_comm ) / 100;
											}
											if( $rtwwwap_cat_fix_comm > 0 ){
												$rtwwwap_prod_comm += $rtwwwap_cat_fix_comm;
											}

											if( $rtwwwap_prod_comm != '' ){
												$rtwwwap_aff_prod_details[] = array(
									    					'product_id' 		=> $rtwwwap_product_id,
									    					'product_price' 	=> $rtwwwap_product_price,
									    					'commission_fix' 	=> $rtwwwap_cat_fix_comm,
									    					'commission_perc' 	=> $rtwwwap_cat_per_comm,
									    					'prod_commission' 	=> $rtwwwap_prod_comm
									    				);

								    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
								    		}
										}
										else{
											if( $rtwwwap_all_commission ){
												if( $rtwwwap_all_commission_type == 'percentage' ){
													$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
												}
												elseif( $rtwwwap_all_commission_type == 'fixed' ){
													$rtwwwap_prod_comm += $rtwwwap_all_commission;
												}
												$rtwwwap_aff_prod_details[] = array(
									    					'product_id' 		=> $rtwwwap_product_id,
									    					'product_price' 	=> $rtwwwap_product_price,
									    					'commission_fix' 	=> '',
									    					'commission_perc' 	=> '',
									    					'prod_commission' 	=> $rtwwwap_prod_comm
									    				);

								    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
								    		}
										}
									}
									else{
										if( $rtwwwap_all_commission ){
											if( $rtwwwap_all_commission_type == 'percentage' ){
												$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
											}
											elseif( $rtwwwap_all_commission_type == 'fixed' ){
												$rtwwwap_prod_comm += $rtwwwap_all_commission;
											}
											$rtwwwap_aff_prod_details[] = array(
								    					'product_id' 		=> $rtwwwap_product_id,
								    					'product_price' 	=> $rtwwwap_product_price,
								    					'commission_fix' 	=> '',
									    				'commission_perc' 	=> '',
								    					'prod_commission' 	=> $rtwwwap_prod_comm
								    				);

							    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
							    		}
									}
								}
							}
							else{
								$rtwwwap_aff_prod_details[] = array(
					    					'product_id' 		=> $rtwwwap_product_id,
					    					'product_price' 	=> $rtwwwap_product_price,
					    					'commission_fix' 	=> $rtwwwap_prod_fix_comm,
						    				'commission_perc' 	=> $rtwwwap_prod_per_comm,
					    					'prod_commission' 	=> $rtwwwap_prod_comm
					    				);

				    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
							}
						}
						elseif( $rtwwwap_all_commission ){
							if( $rtwwwap_all_commission_type == 'percentage' ){
								$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
							}
							elseif( $rtwwwap_all_commission_type == 'fixed' ){
								$rtwwwap_prod_comm += $rtwwwap_all_commission;
							}
							$rtwwwap_aff_prod_details[] = array(
				    					'product_id' 		=> $rtwwwap_product_id,
				    					'product_price' 	=> $rtwwwap_product_price,
				    					'commission_fix' 	=> '',
					    				'commission_perc' 	=> '',
				    					'prod_commission' 	=> $rtwwwap_prod_comm
				    				);

			    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
			    		}
					}
				}
			}
			else
			{
				$rtwwwap_levels_settings 	= get_option( 'rtwwwap_levels_settings_opt' );
				$rtwwwap_user_level 		= get_user_meta( $rtwwwap_user_id, 'rtwwwap_affiliate_level', true );
				$rtwwwap_user_level 		= ( $rtwwwap_user_level ) ? $rtwwwap_user_level : '0';

				$rtwwwap_user_level_details = isset( $rtwwwap_levels_settings[ $rtwwwap_user_level ] ) ? $rtwwwap_levels_settings[ $rtwwwap_user_level ] : '';

				if( !empty( $rtwwwap_user_level_details ) ){
					$rtwwwap_level_comm_type 		= $rtwwwap_user_level_details[ 'level_commission_type' ];
					$rtwwwap_level_comm_amount 		= $rtwwwap_user_level_details[ 'level_comm_amount' ];
					$rtwwwap_level_criteria_type 	= $rtwwwap_user_level_details[ 'level_criteria_type' ];
					$rtwwwap_level_criteria_val 	= $rtwwwap_user_level_details[ 'level_criteria_val' ];

					foreach( $rtwwwap_order->cart_details as $rtwwwap_item_key => $rtwwwap_item_values )
					{
						$rtwwwap_prod_comm 		= '';
						$rtwwwap_product_id 	= $rtwwwap_item_values['ID'];
						$rtwwwap_product_price	= $rtwwwap_item_values['price'];	

						if( $rtwwwap_commission_type == 0 )
						{
							if( $rtwwwap_level_comm_type == 0 ){
								$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_level_comm_amount ) / 100;
								$rtwwwap_aff_prod_details[] = array(
						    					'product_id' 		=> $rtwwwap_product_id,
						    					'product_price' 	=> $rtwwwap_product_price,
						    					'commission_fix' 	=> 'user',
						    					'commission_perc' 	=> $rtwwwap_level_comm_amount,
						    					'prod_commission' 	=> $rtwwwap_prod_comm
						    				);

					    		$rtwwwap_total_commission += $rtwwwap_prod_comm;
							}
							else{
								$rtwwwap_prod_comm = $rtwwwap_level_comm_amount;
								$rtwwwap_aff_prod_details[] = array(
						    					'product_id' 		=> $rtwwwap_product_id,
						    					'product_price' 	=> $rtwwwap_product_price,
						    					'commission_fix' 	=> 'user',
						    					'commission_perc' 	=> $rtwwwap_level_comm_amount,
						    					'prod_commission' 	=> $rtwwwap_prod_comm
						    				);

					    		$rtwwwap_total_commission += $rtwwwap_prod_comm;
							}
						}
					}
				}
			}

			if( isset( $rtwwwap_total_commission ) && $rtwwwap_total_commission !== '' && $rtwwwap_total_commission !== 0 ){
				$rtwwwap_capped 		= 0;
				$rtwwwap_current_year 	= date("Y");
				$rtwwwap_current_month 	= date("m");

				$rtwwwap_commission_settings	= get_option( 'rtwwwap_commission_settings_opt' );
				$rtwwwap_max_comm 				= isset( $rtwwwap_commission_settings[ 'max_commission' ] ) ? $rtwwwap_commission_settings[ 'max_commission' ] : '0';

				if( $rtwwwap_max_comm != 0 )
				{
					$rtwwwap_month_commission 	= $wpdb->get_var( $wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE YEAR(date)=%d AND MONTH(date)=%d AND `aff_id`=%d", $rtwwwap_current_year, $rtwwwap_current_month, $rtwwwap_user_id ) );
					$rtwwwap_month_commission 	= isset( $rtwwwap_month_commission ) ? $rtwwwap_month_commission : 0;

					if( $rtwwwap_month_commission < $rtwwwap_max_comm ){
						$rtwwwap_this_month_left = $rtwwwap_max_comm - $rtwwwap_month_commission;
						if( $rtwwwap_this_month_left < $rtwwwap_total_commission ){
							$rtwwwap_total_commission = $rtwwwap_this_month_left;
						}
						else{
							$rtwwwap_total_commission = $rtwwwap_total_commission;
						}
					}
					else{
						$rtwwwap_capped = 1;
					}
				}

				// inserting into DB
				if( !empty( $rtwwwap_aff_prod_details ) ){
					if( get_user_meta( $rtwwwap_user_id, 'rtwwwap_referral_mail', true ) == 'on' ){
						$rtwwwap_to 			= get_user_by( 'id', $rtwwwap_user_id );
						$rtwwwap_to 			= esc_html( $rtwwwap_to->user_email );

						$all_emails = get_option('customize_email', false);

						$generate_commission = get_option('generate_commission','null');

						if(isset($all_emails['Email on Generating Commission']['subject'])){
							$rtwwwap_subject_text = $all_emails['Email on Generating Commission']['subject'];
							$rtwwwap_message_text = $all_emails['Email on Generating Commission']['content'];
						}

						$rtwwwap_subject 		= esc_html__( $rtwwwap_subject_text, 'rtwwwap-wp-wc-affiliate-program' );
						$rtwwwap_message 		= sprintf( '%s %s%01.'.$rtwwwap_decimal_places, esc_html__( $rtwwwap_message_text , 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_currency_sym, $rtwwwap_total_commission );
						$rtwwwap_from_name 		= esc_html( get_bloginfo( 'name' ) );
						$rtwwwap_from_email 	= esc_html( get_bloginfo( 'admin_email' ) );

						$rtwwwap_headers[] 		= 'Content-Type: text/html; charset=utf-8';
						$rtwwwap_headers[] 		= sprintf( '%s: %s <%s>', esc_html__( 'From', 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_from_name, $rtwwwap_from_email );

						// mail to affiliate
						if($generate_commission == "true"){
							wp_mail( $rtwwwap_to, $rtwwwap_subject, $rtwwwap_message, $rtwwwap_headers );
						}

						$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );
						if( isset( $rtwwwap_extra_features[ 'mail_to_admin' ] ) && $rtwwwap_extra_features[ 'mail_to_admin' ] == 1 ){
							// mail to admin
							$rtwwwap_message = sprintf( '%s %s%01.'.$rtwwwap_decimal_places, esc_html__( 'Generated a new referral of amount', 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_currency_sym, $rtwwwap_total_commission );
							wp_mail( $rtwwwap_from_email, $rtwwwap_subject, $rtwwwap_message, $rtwwwap_headers );
						}
					}

					$rtwwwap_aff_prod_details = json_encode( $rtwwwap_aff_prod_details );
					$rtwwwap_device = ( wp_is_mobile() ) ? 'mobile' : 'desktop';

					$rtwwwap_locale = get_locale();
					setlocale( LC_NUMERIC, $rtwwwap_locale );

					$rtwwwap_updated = $wpdb->insert(
			            $wpdb->prefix.'rtwwwap_referrals',
			            array(
			                'aff_id'    			=> $rtwwwap_user_id,
			                'type'    				=> 0,
			                'order_id'    			=> esc_html( $rtwwwap_order_id ),
			                'date'    				=> date( 'Y-m-d H:i:s' ),
			                'status'    			=> 0,
			                'amount'    			=> $rtwwwap_total_commission,
			                'capped'    			=> esc_html( $rtwwwap_capped ),
			                'currency'    			=> $rtwwwap_currency,
			                'product_details'   	=> $rtwwwap_aff_prod_details,
			                'device'   				=> $rtwwwap_device
			            )
			        );
			        $rtwwwap_lastid = $wpdb->insert_id;
			        setlocale( LC_ALL, $rtwwwap_locale );

			        if( $rtwwwap_updated ){
				        $rtwwwap_referral_noti = get_option( 'rtwwwap_referral_noti' )+1;
				        update_option( 'rtwwwap_referral_noti', $rtwwwap_referral_noti );
					}

					$rtwwwap_mlm = get_option( 'rtwwwap_mlm_opt' );
					if( isset( $rtwwwap_mlm[ 'activate' ] ) && $rtwwwap_mlm[ 'activate' ] == 1 )
					{
						$rtwwwap_child = isset( $rtwwwap_mlm[ 'child' ] ) ? $rtwwwap_mlm[ 'child' ] : 1;
						$rtwwwap_check_have_child = $this->rtwwwap_check_child_in_mlm( $rtwwwap_user_id, $rtwwwap_child );

						if( $rtwwwap_check_have_child ){
							$this->rtwwwap_give_mlm_comm( $rtwwwap_user_id, $rtwwwap_lastid, $rtwwwap_total_commission, $rtwwwap_currency, $rtwwwap_currency_sym, $rtwwwap_device, $rtwwwap_mlm[ 'mlm_levels' ], $rtwwwap_child, $rtwwwap_order_id,"","");
						}
					}
				}
			}
			elseif( $rtwwwap_total_commission == 0 ){
				$rtwwwap_total_commission = $rtwwwap_order->get_subtotal();
				$rtwwwap_capped 		= 0;
				$rtwwwap_current_year 	= date("Y");
				$rtwwwap_current_month 	= date("m");
				$rtwwwap_device 		= ( wp_is_mobile() ) ? 'mobile' : 'desktop';

				$rtwwwap_commission_settings	= get_option( 'rtwwwap_commission_settings_opt' );
				$rtwwwap_max_comm 				= isset( $rtwwwap_commission_settings[ 'max_commission' ] ) ? $rtwwwap_commission_settings[ 'max_commission' ] : '0';

				$rtwwwap_mlm = get_option( 'rtwwwap_mlm_opt' );
				if( isset( $rtwwwap_mlm[ 'activate' ] ) && $rtwwwap_mlm[ 'activate' ] == 1 )
				{
					$rtwwwap_child = isset( $rtwwwap_mlm[ 'child' ] ) ? $rtwwwap_mlm[ 'child' ] : 1;
					$rtwwwap_check_have_child = $this->rtwwwap_check_child_in_mlm( get_current_user_id(), $rtwwwap_child );

					if( $rtwwwap_check_have_child ){
						$this->rtwwwap_give_mlm_comm( get_current_user_id(), '', $rtwwwap_total_commission, $rtwwwap_currency, $rtwwwap_currency_sym, $rtwwwap_device, $rtwwwap_mlm[ 'mlm_levels' ], $rtwwwap_child, $rtwwwap_order_id,"","" );
					}
				}
			}
		}
	}




	/*
	* To Search products for creating banner
	*/
	function rtwwwap_search_prod_callback(){
		$rtwwwap_check_ajax = check_ajax_referer( 'rtwwwap-ajax-security-string', 'rtwwwap_security_check' );

		if ( $rtwwwap_check_ajax ) {
			$rtwwwap_prod_name 	= sanitize_text_field( $_POST[ 'rtwwwap_prod_name' ] );
			$rtwwwap_cat_id 	= sanitize_text_field( $_POST[ 'rtwwwap_cat_id' ] );
			$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );


			global $wpdb;
			$rtwwwap_wild = '%';
			$rtwwwap_like = $rtwwwap_wild . $wpdb->esc_like( $rtwwwap_prod_name ) . $rtwwwap_wild;
			if(RTWWWAP_IS_WOO)
			{
				$rtwwwap_post_type = 'product';
			}
			else
			{
				$rtwwwap_post_type = 'download';
			}
			$rtwwwap_message = "";
		
			$rtwwwap_query = $wpdb->prepare( "SELECT * FROM ".$wpdb->posts." JOIN ".$wpdb->term_relationships." ON ".$wpdb->posts.".`ID` = ".$wpdb->term_relationships.".`object_id` JOIN ".$wpdb->term_taxonomy." ON ".$wpdb->term_relationships.".`term_taxonomy_id` = ".$wpdb->term_taxonomy.".`term_taxonomy_id` WHERE ".$wpdb->posts.".`post_title` LIKE %s AND ".$wpdb->posts.".`post_type` LIKE '".$rtwwwap_post_type."' AND ".$wpdb->term_taxonomy.".`term_id` =%d", $rtwwwap_like, $rtwwwap_cat_id );
			$rtwwwap_prods = $wpdb->get_results( $rtwwwap_query, ARRAY_A );
			
			$rtwwwap_html = '';
			

			if( !empty( $rtwwwap_prods ) ){
				if( RTWWWAP_IS_WOO == 1 ){
					$rtwwwap_currency 		= get_woocommerce_currency();
					$rtwwwap_currency_sym 	= get_woocommerce_currency_symbol();
				}
				else{
					require_once( RTWWWAP_DIR.'includes/rtwaffiliatehelper.php' );
	
					$rtwwwap_currency 		= isset( $rtwwwap_extra_features[ 'currency' ] ) ? $rtwwwap_extra_features[ 'currency' ] : 'USD';
					$rtwwwap_curr_obj 		= new RtwAffiliateHelper();
					$rtwwwap_currency_sym 	= $rtwwwap_curr_obj->rtwwwap_curr_symbol( $rtwwwap_currency );
				}

				foreach( $rtwwwap_prods as $rtwwwap_key => $rtwwwap_value ){
					$rtwwwap_img_url 	= wp_get_attachment_image_src( get_post_thumbnail_id( $rtwwwap_value[ 'ID' ] ), 'full' );
					$rtwwwap_prod_url 	= get_permalink( $rtwwwap_value[ 'ID' ], false );
					$rtwwwap_affilaite_template =  isset($rtwwwap_extra_features[ 'affiliate_page_template' ]) ? $rtwwwap_extra_features[ 'affiliate_page_template' ] : 1 ;
					$rtwwwap_affiliate_slug 		= isset( $rtwwwap_extra_features[ 'affiliate_slug' ] ) ? $rtwwwap_extra_features[ 'affiliate_slug' ] : esc_html__( 'rtwwwap_aff', 'rtwwwap-wp-wc-affiliate-program' ) ;
					if(RTWWWAP_IS_Easy == 1 && $rtwwwap_affilaite_template == 1){

							$rtwwwap_prod_price = new EDD_Download( $rtwwwap_value[ 'ID' ] );
							
							$rtwwwap_html .= 	'<div class="rtwwwap_searched_prod">';
							$rtwwwap_html .= 		'<img src="'.esc_url( $rtwwwap_img_url[0] ).'" class="rtwwwap_prod_img" alt="">';
							$rtwwwap_html .= 		'<div class="rtwwwap_inner">';
							$rtwwwap_html .= 			'<div>';
							$rtwwwap_html .= 				'<p class="rtwwwap_prod_name">'.$rtwwwap_value[ 'post_title' ].'</p>';
							$rtwwwap_html .= 				'<p class="rtwwwap_prod_price">'.$rtwwwap_currency_sym.$rtwwwap_prod_price->get_price().'</p>';
							$rtwwwap_html .= 			'</div>';
							$rtwwwap_html .= 			'<p data-rtwwwap_slug="'.$rtwwwap_affiliate_slug.'" data-rtwwwap_id="'.esc_attr( $rtwwwap_value[ 'ID' ] ).'" data-rtwwwap_title="'.esc_attr( $rtwwwap_value[ 'post_title' ] ).'" data-rtwwwap_url="'.esc_attr( esc_url( $rtwwwap_prod_url ) ).'" data-rtwwwap_displayprice="'.esc_attr( $rtwwwap_prod_price->get_price() ).'" data-rtwwwap_image="'.esc_attr( $rtwwwap_img_url[0] ).'" >';
							$rtwwwap_html .= 				'<input type="button" id="rtwwwap_create_link" value="'.esc_attr__( "Link", "rtwwwap-wp-wc-affiliate-program" ).'" />';
							$rtwwwap_html .= 				'<input type="button" id="rtwwwap_create_banner" value="'.esc_attr__( "Banner", "rtwwwap-wp-wc-affiliate-program" ).'" />';
							$rtwwwap_html .= 			'</p>';
							$rtwwwap_html .= 		'</div>';
							$rtwwwap_html .= 	'</div>';

					}
					if(RTWWWAP_IS_Easy == 1 && $rtwwwap_affilaite_template == 2){
						$rtwwwap_prod_price = new EDD_Download( $rtwwwap_value[ 'ID' ] );
						$rtwwwap_html .= 	'<div class="	-product-box-row">';
						$rtwwwap_html .= 		'<div class="rtwwwap-product-image">';
						$rtwwwap_html .= 			'<img src="'.esc_url( $rtwwwap_img_url[0] ).'">';
						$rtwwwap_html .= 		'</div>';
						$rtwwwap_html .= 		'<div class="rtwwwap-product-description">';
						$rtwwwap_html .= 			'<div class="rtwwwap-product-name">';
						$rtwwwap_html .= 				'<h2>'.$rtwwwap_value[ 'post_title' ].'</h2>';
						$rtwwwap_html .= 				'<p class="rtwwwap-product-price">'.$rtwwwap_currency_sym.$rtwwwap_prod_price->get_price().'</p>';
						$rtwwwap_html .= 			'</div>';
						$rtwwwap_html .= 			'<p class="rtwwwap-add-cart-btn-section" data-rtwwwap_slug="'.$rtwwwap_affiliate_slug.'" data-rtwwwap_id="'.esc_attr( $rtwwwap_value[ 'ID' ] ).'" data-rtwwwap_title="'.esc_attr( $rtwwwap_value[ 'post_title' ] ).'" data-rtwwwap_url="'.esc_attr( esc_url( $rtwwwap_prod_url ) ).'" data-rtwwwap_displayprice="'.esc_attr( $rtwwwap_prod_price->get_price_html() ).'" data-rtwwwap_image="'.esc_attr( $rtwwwap_img_url[0] ).'">';
						$rtwwwap_html .= 				'<input type="button" class ="rtwwwap-create-link-btn" id="rtwwwap_create_link" value="'.esc_attr__( "Link", "rtwwwap-wp-wc-affiliate-program" ).'">';
						$rtwwwap_html .= 				'<input type="button" class="rtwwwap-create-link-btn"  id="rtwwwap_create_banner" value="'.esc_attr__( "Banner", "rtwwwap-wp-wc-affiliate-program" ).'" >';
						$rtwwwap_html .= 			'</p>';
						$rtwwwap_html .= 		'</div>';
						$rtwwwap_html .= 	'</div>';
					}
					if(RTWWWAP_IS_WOO == 1 && $rtwwwap_affilaite_template == 1){

						$rtwwwap_prod_price = new WC_Product( $rtwwwap_value[ 'ID' ] );	
						$rtwwwap_html .= 	'<div class="rtwwwap_searched_prod">';
						$rtwwwap_html .= 		'<img src="'.esc_url( $rtwwwap_img_url[0] ).'" class="rtwwwap_prod_img" alt="">';
						$rtwwwap_html .= 		'<div class="rtwwwap_inner">';
						$rtwwwap_html .= 			'<div>';
						$rtwwwap_html .= 				'<p class="rtwwwap_prod_name">'.$rtwwwap_value[ 'post_title' ].'</p>';
						$rtwwwap_html .= 				'<p class="rtwwwap_prod_price">'.$rtwwwap_prod_price->get_price_html().'</p>';
						$rtwwwap_html .= 			'</div>';
						$rtwwwap_html .= 			'<p data-rtwwwap_slug="'.$rtwwwap_affiliate_slug.'" data-rtwwwap_id="'.esc_attr( $rtwwwap_value[ 'ID' ] ).'" data-rtwwwap_title="'.esc_attr( $rtwwwap_value[ 'post_title' ] ).'" data-rtwwwap_url="'.esc_attr( esc_url( $rtwwwap_prod_url ) ).'" data-rtwwwap_displayprice="'.esc_attr( $rtwwwap_prod_price->get_price_html() ).'" data-rtwwwap_image="'.esc_attr( $rtwwwap_img_url[0] ).'" >';
						$rtwwwap_html .= 				'<input type="button" id="rtwwwap_create_link" value="'.esc_attr__( "Link", "rtwwwap-wp-wc-affiliate-program" ).'" />';
						$rtwwwap_html .= 				'<input type="button" data-rtwwwap_template="'.$rtwwwap_affilaite_template.'" id="rtwwwap_create_banner" value="'.esc_attr__( "Banner", "rtwwwap-wp-wc-affiliate-program" ).'" />';
						$rtwwwap_html .= 			'</p>';
						$rtwwwap_html .= 		'</div>';
						$rtwwwap_html .= 	'</div>';
					}
					if(RTWWWAP_IS_WOO == 1 && $rtwwwap_affilaite_template == 2){
						$rtwwwap_prod_price = new WC_Product( $rtwwwap_value[ 'ID' ] );	

						$rtwwwap_html .= 	'<div class="rtwwwap-product-box-row">';
						$rtwwwap_html .= 		'<div class="rtwwwap-product-image">';
						$rtwwwap_html .= 			'<img src="'.esc_url( $rtwwwap_img_url[0] ).'">';
						$rtwwwap_html .= 		'</div>';
						$rtwwwap_html .= 		'<div class="rtwwwap-product-description">';
						$rtwwwap_html .= 			'<div class="rtwwwap-product-name">';
						$rtwwwap_html .= 				'<h2>'.$rtwwwap_value[ 'post_title' ].'</h2>';
						$rtwwwap_html .= 				'<p class="rtwwwap-product-price">'.$rtwwwap_prod_price->get_price_html().'	</p>';
						$rtwwwap_html .= 			'</div>';
						$rtwwwap_html .= 			'<p class="rtwwwap-add-cart-btn-section" data-rtwwwap_slug="'.$rtwwwap_affiliate_slug.'" data-rtwwwap_template="'.esc_attr( $rtwwwap_affilaite_template ).'"  data-rtwwwap_id="'.esc_attr( $rtwwwap_value[ 'ID' ] ).'" data-rtwwwap_title="'.esc_attr( $rtwwwap_value[ 'post_title' ] ).'" data-rtwwwap_url="'.esc_attr( esc_url( $rtwwwap_prod_url ) ).'" data-rtwwwap_displayprice="'.esc_attr( $rtwwwap_prod_price->get_price_html() ).'" data-rtwwwap_image="'.esc_attr( $rtwwwap_img_url[0] ).'">';
						$rtwwwap_html .= 				'<input type="button" class ="rtwwwap-create-link-btn" id="rtwwwap_create_link" value="'.esc_attr__( "Link", "rtwwwap-wp-wc-affiliate-program" ).'">';
						$rtwwwap_html .= 				'<input type="button" data-rtwwwap_template="'.$rtwwwap_affilaite_template.'" class="rtwwwap-create-link-btn"  id="rtwwwap_create_banner" value="'.esc_attr__( "Banner", "rtwwwap-wp-wc-affiliate-program" ).'" >';
						$rtwwwap_html .= 			'</p>';
						$rtwwwap_html .= 		'</div>';
						$rtwwwap_html .= 	'</div>';
					}
				}
			}

			if( empty( $rtwwwap_prods ) ){
				$rtwwwap_message = esc_html__( 'No Result Found', 'rtwwwap-wp-wc-affiliate-program' );
			}


			echo json_encode( array( 'rtwwwap_products' => $rtwwwap_html, 'rtwwwap_message' => $rtwwwap_message ) );
			die;
		}
	}


		/*
	* To Search products for creating banner for tempalte 3
	*/
	function rtwwwap_search_product_temp3_callback(){
		$rtwwwap_check_ajax = check_ajax_referer( 'rtwwwap-ajax-security-string', 'rtwwwap_security_check' );

		$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );
		$rtwwwap_affilaite_template =  isset($rtwwwap_extra_features[ 'affiliate_page_template' ]) ? $rtwwwap_extra_features[ 'affiliate_page_template' ] : 1 ;

		if ( $rtwwwap_check_ajax ) {
			
			$rtwwwap_prod_name 	= sanitize_text_field( $_POST[ 'rtwwwap_prod_name' ] );
			$rtwwwap_cat_id 	= sanitize_text_field( $_POST[ 'rtwwwap_cat_id' ] );
			$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );


			$rtwwwap_affiliate_slug 		= isset( $rtwwwap_extra_features[ 'affiliate_slug' ] ) ? $rtwwwap_extra_features[ 'affiliate_slug' ] : esc_html__( 'rtwwwap_aff', 'rtwwwap-wp-wc-affiliate-program' ) ;


			$rtwwwap_user_id 	= get_current_user_id();
			
			$rtwwwap_userdata 	= get_userdata( $rtwwwap_user_id );
		
			$rtwwwap_user_name 	= $rtwwwap_userdata->data->user_login;


			global $wpdb;
			$rtwwwap_wild = '%';
			$rtwwwap_like = $rtwwwap_wild . $wpdb->esc_like( $rtwwwap_prod_name ) . $rtwwwap_wild;
			if(RTWWWAP_IS_WOO)
			{
				$rtwwwap_post_type = 'product';
			}
			else
			{
				$rtwwwap_post_type = 'download';
			}
		
			$rtwwwap_query = $wpdb->prepare( "SELECT * FROM ".$wpdb->posts." JOIN ".$wpdb->term_relationships." ON ".$wpdb->posts.".`ID` = ".$wpdb->term_relationships.".`object_id` JOIN ".$wpdb->term_taxonomy." ON ".$wpdb->term_relationships.".`term_taxonomy_id` = ".$wpdb->term_taxonomy.".`term_taxonomy_id` WHERE ".$wpdb->posts.".`post_title` LIKE %s AND ".$wpdb->posts.".`post_type` LIKE '".$rtwwwap_post_type."' AND ".$wpdb->term_taxonomy.".`term_id` =%d", $rtwwwap_like, $rtwwwap_cat_id );
			$rtwwwap_prods = $wpdb->get_results( $rtwwwap_query, ARRAY_A );
			
			$rtwwwap_html = '';
			

			if( !empty( $rtwwwap_prods ) ){
				if( RTWWWAP_IS_WOO == 1 ){
					$rtwwwap_currency 		= get_woocommerce_currency();
					$rtwwwap_currency_sym 	= get_woocommerce_currency_symbol();
				}
				else{
					require_once( RTWWWAP_DIR.'includes/rtwaffiliatehelper.php' );
	
					$rtwwwap_currency 		= isset( $rtwwwap_extra_features[ 'currency' ] ) ? $rtwwwap_extra_features[ 'currency' ] : 'USD';
					$rtwwwap_curr_obj 		= new RtwAffiliateHelper();
					$rtwwwap_currency_sym 	= $rtwwwap_curr_obj->rtwwwap_curr_symbol( $rtwwwap_currency );
				}

				foreach( $rtwwwap_prods as $rtwwwap_key => $rtwwwap_value ){
					$rtwwwap_img_url 	= wp_get_attachment_image_src( get_post_thumbnail_id( $rtwwwap_value[ 'ID' ] ), 'full' );
					$rtwwwap_prod_url 	= get_permalink( $rtwwwap_value[ 'ID' ], false );
		
					$rtwwwap_prod_price = new WC_Product( $rtwwwap_value[ 'ID' ] );	
					
						$rtwwwap_affiliate_slug = isset( $rtwwwap_extra_features[ 'affiliate_slug' ] ) ? $rtwwwap_extra_features[ 'affiliate_slug' ] : esc_html__( 'rtwwwap_aff', 'rtwwwap-wp-wc-affiliate-program' ) ;

						$rtwwwap_html .=	'<div class="rtwwwap-prdct-box">
						<div class="mdc-card">
							<div class="mdc-card__primary-action" tabindex="0">
								<div class="mdc-card__media">
								<img src="'.esc_url( $rtwwwap_img_url[0] ).'">
								</div>
								<div class="rtwwwap-card-overlay"></div>
								<div class="rtwwwap-card-overlay-btn rtwwwap-fadeIn-top"> 
								<p class="rtwwwap-add-cart-btn-section" data-rtwwwap_slug="'.$rtwwwap_affiliate_slug.'" data-rtwwwap_template="'.esc_attr( $rtwwwap_affilaite_template ).'"  data-rtwwwap_id="'.esc_attr( $rtwwwap_value[ 'ID' ] ).'" data-rtwwwap_title="'.esc_attr( $rtwwwap_value[ 'post_title' ] ).'" data-rtwwwap_url="'.esc_attr( esc_url( $rtwwwap_prod_url ) ).'" data-rtwwwap_displayprice="'.esc_attr( $rtwwwap_prod_price->get_price() ).'" data-rtwwwap_image="'.esc_attr( $rtwwwap_img_url[0] ).'">
									<button class="mdc-button mdc-button--raised" id="rtwwwap_create_banner">
										<span class="mdc-button__label">Banner</span>
									</button>
									</p>
								</div>
							</div>
							<div class="rtwwap-bottom">
								<div class="rtwwwap-prdct-name rtwwwap-prdct-box-primary">
									<p class="rtwwap-card-title"> '.$rtwwwap_value[ 'post_title' ].'</p>
									<div class= "rtwwwap-card-price">
										<span>'.$rtwwwap_prod_price->get_price().'</span>
										
									</div>
								</div>
								<div class="rtwwwap-pedct-description">
									<p></p>
								</div>
							

								<button class="mdc-button mdc-button--raised data-rtwwwap_template="'.esc_attr( $rtwwwap_affilaite_template ).'"  data-rtwwwap_id="'.esc_attr( $rtwwwap_value[ 'ID' ] ).'" data-rtwwwap_title="'.esc_attr( $rtwwwap_value[ 'post_title' ] ).'" data-rtwwwap_url="'.esc_attr( esc_url( $rtwwwap_prod_url ) ).'" data-rtwwwap_displayprice="'.esc_attr( $rtwwwap_prod_price->get_price() ).'" data-rtwwwap_image="'.esc_attr( $rtwwwap_img_url[0] ).'"">
									<span class="mdc-button__label" id="rtwwwap_banner_link_button" data-rtwwwap_aff_id="'.esc_attr( get_current_user_id() ).'" data-rtwwwap_aff_name="'.esc_attr( $rtwwwap_user_name ).'" data-rtwwwap_slug="'.$rtwwwap_affiliate_slug.'" data-rtwwwap_home_url="'.esc_attr( home_url() ).'">Link</span>
								</button>
							</div>
						</div>
					</div>';
				}
			}

			if( empty( $rtwwwap_prods ) ){
				$rtwwwap_message = esc_html__( 'No Result Found', 'rtwwwap-wp-wc-affiliate-program' );
			}

			echo json_encode( array( 'rtwwwap_products' => $rtwwwap_html, 'rtwwwap_message' => "Successfully executed" ) );
			die;
		}
	}

	/*
	* To generate CSV of a category
	*/


	function rtwwwap_generate_csv_callback(){

		$rtwwwap_verification_done = get_option( 'rtwwwap_verification_done', array() );
		$rtwwwap_verification_done_status = isset($rtwwwap_verification_done['status']) ? $rtwwwap_verification_done['status'] : false;
		$rtwwwap_verification_done_purchase = isset($rtwwwap_verification_done['purchase_code']) ? $rtwwwap_verification_done['purchase_code'] : false;
		

		if(RTWWWAP_IS_WOO == 1 )
		{
			$rtwwwp_product_category_taxonomy = 'product_cat';
		}
		else if(RTWWWAP_IS_Easy == 1 )
		{
			$rtwwwp_product_category_taxonomy = 'download_category';
		}
		if( empty( $rtwwwap_verification_done ) || $rtwwwap_verification_done_status == false || empty($rtwwwap_verification_done_purchase) )
		{
			return ;
		}
		$rtwwwap_check_ajax = check_ajax_referer( 'rtwwwap-ajax-security-string', 'rtwwwap_security_check' );

		$rtwwwap_cat_id 	= sanitize_text_field( $_POST[ 'rtwwwap_cat_id' ] );
		$rtwwwap_term 		= get_term_by( 'id', $rtwwwap_cat_id, $rtwwwp_product_category_taxonomy );
		$rtwwwap_cat_name 	= esc_html( $rtwwwap_term->name );


		if(RTWWWAP_IS_WOO == 1)
		{
			$rtwwwap_post_type = 'product';
		}
		else
		{
			$rtwwwap_post_type = 'download';
		}	

		$rtwwwap_args = array(
			'post_type'             => $rtwwwap_post_type,
			'post_status'           => 'publish',
			'ignore_sticky_posts'   => 1,
			'posts_per_page'        => '12',
			'tax_query'             => array(
				array(
					'taxonomy'      => $rtwwwp_product_category_taxonomy,
					'field' 		=> 'term_id', //This is optional, as it defaults to 'term_id'
					'terms'         => $rtwwwap_cat_id,
					'operator'      => 'IN' // Possible values are 'IN', 'NOT IN', 'AND'.
				)
			)
		);
		
		
		$rtwwwap_products = new WP_Query( $rtwwwap_args );
				$rtwwwap_user_id 	= get_current_user_id();
		
		$rtwwwap_userdata 	= get_userdata( $rtwwwap_user_id );
	
		$rtwwwap_user_name 	= $rtwwwap_userdata->data->user_login;
		$rtwwwap_counter =0;


		require_once( WP_PLUGIN_DIR."/wp-wc-affiliate-program/third_party/csv-9.8.0/autoload.php");
		$rtwwwap_filename 	= "export-labels--".time().".csv";
		$csv = Writer::createFromPath(RTWWWAP_DIR.'assets/csv/'.$rtwwwap_filename, 'w+');
		$header = ['S.NO.', 'PRODUCT NAME', 'URL','CATEGORY','DESCRIPTION','LIST PRICE','SALE PRICE'];

		$csv->insertOne($header);

		foreach( $rtwwwap_products->posts as $rtwwwap_key => $rtwwwap_value ){

			$rtwwwap_reff_url 		= get_permalink( $rtwwwap_value->ID );
			
			$rtwwwap_generated_url 	= '';
			if( strpos( $rtwwwap_reff_url, '?' ) ){
				$rtwwwap_generated_url = $rtwwwap_reff_url.'&rtwwwap_aff='.$rtwwwap_user_name.'_'.$rtwwwap_user_id;
			}
			else{
				$rtwwwap_generated_url = $rtwwwap_reff_url.'?rtwwwap_aff='.$rtwwwap_user_name.'_'.$rtwwwap_user_id;
			}
			$rtwwwap_counter++;
			if(RTWWWAP_IS_WOO == 1)
				{
				$rtwwwap_prod_price = new WC_Product( $rtwwwap_value->ID );
				}
			elseif(RTWWWAP_IS_Easy == 1 )
			{
				$rtwwwap_prod_price = new EDD_Download( $rtwwwap_value->ID );
			}

			$csv->insertOne([$rtwwwap_counter-1, $rtwwwap_value->post_name, $rtwwwap_generated_url, $rtwwwap_cat_name,$rtwwwap_value->post_content,$rtwwwap_prod_price->get_price(),$rtwwwap_prod_price->get_price()]);

		}

		echo json_encode( array('status' => true, 'filename' => $rtwwwap_filename) );
		wp_die();

		
	}

	function rtwwwap_create_coupon_callback(){
		$rtwwwap_check_ajax = check_ajax_referer( 'rtwwwap-ajax-security-string', 'rtwwwap_security_check' );

		if ( $rtwwwap_check_ajax ) {
			$rtwwwap_user_id 		= get_current_user_id();
			$rtwwwap_amount 		= sanitize_text_field( $_POST[ 'rtwwwap_amount' ] );
			$rtwwwap_total_comm 	= get_user_meta( $rtwwwap_user_id, 'rtw_user_wallet', true );

			if( $rtwwwap_amount > $rtwwwap_total_comm ){
				$rtwwwap_amount 	= $rtwwwap_total_comm;
			}

			$rtwwwap_coupon_code 	= substr( "abcdefghijklmnopqrstuvwxyz123456789", mt_rand(0, 50) , 1) .substr( md5( time() ), 1); // Code
			$rtwwwap_coupon_code 	= substr( $rtwwwap_coupon_code, 0, 10 ); // create 10 letters coupon
			$rtwwwap_discount_type = 'fixed_cart'; // Type: fixed_cart, percent, fixed_product, percent_product

			$rtwwwap_coupon = array(
				'post_title' 	=> $rtwwwap_coupon_code,
				'post_content' 	=> '',
				'post_status' 	=> 'publish',
				'post_author' 	=> 1,
				'post_type'		=> 'shop_coupon'
			);

			$rtwwwap_new_coupon_id 	= wp_insert_post( $rtwwwap_coupon );
			$rtwwwap_userdata 		= get_userdata( $rtwwwap_user_id );
			$rtwwwap_user_email 	= $rtwwwap_userdata->user_email;
			// Add meta
			update_post_meta( $rtwwwap_new_coupon_id, 'discount_type', $rtwwwap_discount_type );
			update_post_meta( $rtwwwap_new_coupon_id, 'coupon_amount', $rtwwwap_amount );
			update_post_meta( $rtwwwap_new_coupon_id, 'individual_use', 'no' );
			update_post_meta( $rtwwwap_new_coupon_id, 'product_ids', '' );
			update_post_meta( $rtwwwap_new_coupon_id, 'exclude_product_ids', '' );
			update_post_meta( $rtwwwap_new_coupon_id, 'usage_limit', '' );
			update_post_meta( $rtwwwap_new_coupon_id, 'expiry_date', '' );
			update_post_meta( $rtwwwap_new_coupon_id, 'apply_before_tax', 'yes' );
			update_post_meta( $rtwwwap_new_coupon_id, 'free_shipping', 'no' );
			update_post_meta( $rtwwwap_new_coupon_id, 'rtwwwap_coupon', 1 );
			update_post_meta( $rtwwwap_new_coupon_id, 'customer_email', array( $rtwwwap_user_email ) );

			// Update user meta
			$rtwwwap_coupons = get_user_meta( $rtwwwap_user_id, 'rtwwwap_coupons', true );

			if( empty( $rtwwwap_coupons ) ){
				$rtwwwap_coupons = array();
			}
			$rtwwwap_coupons[] = $rtwwwap_new_coupon_id;
			update_user_meta( $rtwwwap_user_id, 'rtwwwap_coupons', $rtwwwap_coupons );

			$rtwwwap_aff_overall_comm = get_user_meta( $rtwwwap_user_id, 'rtw_user_wallet', true );
			$rtwwwap_aff_overall_comm -= $rtwwwap_amount;
			update_user_meta( $rtwwwap_user_id, 'rtw_user_wallet', $rtwwwap_aff_overall_comm );
		}
	}

	function rtwwwap_woocommerce_order_add_coupon( $rtwwwap_order_id, $rtwwwap_item_id, $rtwwwap_coupon_code, $rtwwwap_discount_amount, $rtwwwap_discount_amount_tax )
	{
		$rtwwwap_the_coupon = new WC_Coupon( $rtwwwap_coupon_code );
		if( isset( $rtwwwap_the_coupon->id ) )
		{
			$rtwwwap_coupon_id 		= $rtwwwap_the_coupon->id;
			$rtwwwap_is_rtw_coupon 	= get_post_meta( $rtwwwap_coupon_id, 'rtwwwap_coupon', true );

			if( !empty( $rtwwwap_is_rtw_coupon ) )
			{
				$rtwwwap_amount 		= get_post_meta( $rtwwwap_coupon_id, 'coupon_amount', true );
				$rtwwwap_total_discount = $rtwwwap_discount_amount+$rtwwwap_discount_amount_tax;
				if( $rtwwwap_amount < $rtwwwap_total_discount )
				{
					$rtwwwap_remaining_amount = 0;
				}
				else
				{
					$rtwwwap_remaining_amount = $rtwwwap_amount - $rtwwwap_total_discount;
				}
				update_post_meta( $rtwwwap_coupon_id, 'coupon_amount', $rtwwwap_remaining_amount );
			}
		}
	}

	function rtwwwap_user_register_signup_bonus( $rtwwwap_user_id , $referral_code){

		$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );
		$rtwwwap_signup_bonus 	= isset( $rtwwwap_extra_features[ 'signup_bonus' ] ) ? esc_html( $rtwwwap_extra_features[ 'signup_bonus' ] ) : 0;
		$rtwwwap_signup_bonus_type = isset( $rtwwwap_extra_features[ 'signup_bonus_type' ] ) ? esc_html( $rtwwwap_extra_features[ 'signup_bonus_type' ] ) : 0;

		if( $rtwwwap_signup_bonus_type == 1 ){
			$rtwwwap_referral_code = $referral_code;
			global $wpdb;
			// $rtwwwap_referral 		= explode( '_', $rtwwwap_referral_code );
			// $rtwwwap_reff_id 		= esc_html( $rtwwwap_referral[ 1 ] );

			// update code starts here 

			$rtwwwap_referee_aff = get_users(array(
				'meta_key' => 'rtwwwap_referee_custom_str',
				'meta_value' => $rtwwwap_referral_code
			));

			if($rtwwwap_referee_aff){
				$rtwwwap_reff_id = $rtwwwap_referee_aff[0]->ID;
			}
			else{
				return false;
			}

			// ends here 


			$rtwwwap_device 		= ( wp_is_mobile() ) ? 'mobile' : 'desktop';

			$this->rtwwwap_referral_code_cookie_generation($rtwwwap_reff_id);

			if( RTWWWAP_IS_WOO == 1 ){
				$rtwwwap_currency = esc_html( get_woocommerce_currency() );
			}
			else{
				require_once( RTWWWAP_DIR.'includes/rtwaffiliatehelper.php' );

				$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );
				$rtwwwap_currency 		= isset( $rtwwwap_extra_features[ 'currency' ] ) ? $rtwwwap_extra_features[ 'currency' ] : 'USD';
			}

			$rtwwwap_current_year 	= date("Y");
			$rtwwwap_current_month 	= date("m");
			$rtwwwap_capped 		= 0;

			if( $rtwwwap_signup_bonus && $rtwwwap_reff_id){
				$rtwwwap_commission_settings 	= get_option( 'rtwwwap_commission_settings_opt' );
				$rtwwwap_max_comm 				= isset( $rtwwwap_commission_settings[ 'max_commission' ] ) ? $rtwwwap_commission_settings[ 'max_commission' ] : '0';

				if( $rtwwwap_max_comm != 0 ){
					$rtwwwap_month_commission 	= $wpdb->get_var( $wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE YEAR(date)=%d AND MONTH(date)=%d AND `aff_id`=%d", $rtwwwap_current_year, $rtwwwap_current_month, $rtwwwap_reff_id ) );
					$rtwwwap_month_commission 	= isset( $rtwwwap_month_commission ) ? $rtwwwap_month_commission : 0;

					if( $rtwwwap_month_commission < $rtwwwap_max_comm ){
						$rtwwwap_this_month_left = $rtwwwap_max_comm - $rtwwwap_month_commission;
						if( $rtwwwap_this_month_left < $rtwwwap_signup_bonus ){
							$rtwwwap_signup_bonus = $rtwwwap_this_month_left;
						}
						else{
							$rtwwwap_signup_bonus = $rtwwwap_signup_bonus;
						}
					}
					else{
						$rtwwwap_capped = 1;
					}
				}

				$rtwwwap_locale = get_locale();
				setlocale( LC_NUMERIC, $rtwwwap_locale );

				$wpdb->insert(
		            $wpdb->prefix.'rtwwwap_referrals',
		            array(
		                'aff_id'    			=> $rtwwwap_reff_id,
		                'type'    				=> 1,
		                'order_id'    			=> 0,
		                'date'    				=> date( 'Y-m-d H:i:s' ),
		                'status'    			=> 0,
		                'amount'    			=> esc_html( $rtwwwap_signup_bonus ),
		                'capped'    			=> esc_html( $rtwwwap_capped ),
		                'currency'    			=> $rtwwwap_currency,
		                'product_details'   	=> '',
		                'device'   				=> $rtwwwap_device,
		                'signed_up_id' 			=> $rtwwwap_user_id
		            )
		        );

		        setlocale( LC_ALL, $rtwwwap_locale );
			}
			else{
				$rtwwwap_mlm = get_option( 'rtwwwap_mlm_opt' );
				if( isset( $rtwwwap_mlm[ 'activate' ] ) && $rtwwwap_mlm[ 'activate' ] == 1 )
				{
					$wpdb->insert(
			            $wpdb->prefix.'rtwwwap_referrals',
			            array(
			                'aff_id'    			=> $rtwwwap_reff_id,
			                'type'    				=> 3,
			                'order_id'    			=> 0,
			                'date'    				=> date( 'Y-m-d H:i:s' ),
			                'status'    			=> 0,
			                'amount'    			=> 0,
			                'capped'    			=> 0,
			                'currency'    			=> 0,
			                'product_details'   	=> '',
			                'device'   				=> $rtwwwap_device,
			                'signed_up_id' 			=> $rtwwwap_user_id
			            )
			        );
				}
			}
		}
		elseif( isset( $_COOKIE[ 'rtwwwap_referral' ] ) ){
			global $wpdb;
			$rtwwwap_referral 		= explode( '#', $_COOKIE[ 'rtwwwap_referral' ] );
			$rtwwwap_reff_id 		= esc_html( $rtwwwap_referral[ 0 ] );
			$rtwwwap_device 		= ( wp_is_mobile() ) ? 'mobile' : 'desktop';

			if( RTWWWAP_IS_WOO == 1 ){
				$rtwwwap_currency = esc_html( get_woocommerce_currency() );
			}
			else{
				require_once( RTWWWAP_DIR.'includes/rtwaffiliatehelper.php' );

				$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );
				$rtwwwap_currency 		= isset( $rtwwwap_extra_features[ 'currency' ] ) ? $rtwwwap_extra_features[ 'currency' ] : 'USD';
			}

			$rtwwwap_current_year 	= date("Y");
			$rtwwwap_current_month 	= date("m");
			$rtwwwap_capped 		= 0;

			if( $rtwwwap_signup_bonus && $rtwwwap_reff_id ){
				$rtwwwap_commission_settings 	= get_option( 'rtwwwap_commission_settings_opt' );
				$rtwwwap_max_comm 				= isset( $rtwwwap_commission_settings[ 'max_commission' ] ) ? $rtwwwap_commission_settings[ 'max_commission' ] : '0';

				if( $rtwwwap_max_comm != 0 ){
					$rtwwwap_month_commission 	= $wpdb->get_var( $wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE YEAR(date)=%d AND MONTH(date)=%d AND `aff_id`=%d", $rtwwwap_current_year, $rtwwwap_current_month, $rtwwwap_reff_id ) );
					$rtwwwap_month_commission 	= isset( $rtwwwap_month_commission ) ? $rtwwwap_month_commission : 0;

					if( $rtwwwap_month_commission < $rtwwwap_max_comm ){
						$rtwwwap_this_month_left = $rtwwwap_max_comm - $rtwwwap_month_commission;
						if( $rtwwwap_this_month_left < $rtwwwap_signup_bonus ){
							$rtwwwap_signup_bonus = $rtwwwap_this_month_left;
						}
						else{
							$rtwwwap_signup_bonus = $rtwwwap_signup_bonus;
						}
					}
					else{
						$rtwwwap_capped = 1;
					}
				}

				$rtwwwap_locale = get_locale();
				setlocale( LC_NUMERIC, $rtwwwap_locale );

				$wpdb->insert(
		            $wpdb->prefix.'rtwwwap_referrals',
		            array(
		                'aff_id'    			=> $rtwwwap_reff_id,
		                'type'    				=> 1,
		                'order_id'    			=> 0,
		                'date'    				=> date( 'Y-m-d H:i:s' ),
		                'status'    			=> 0,
		                'amount'    			=> esc_html( $rtwwwap_signup_bonus ),
		                'capped'    			=> esc_html( $rtwwwap_capped ),
		                'currency'    			=> $rtwwwap_currency,
		                'product_details'   	=> '',
		                'device'   				=> $rtwwwap_device,
		                'signed_up_id' 			=> $rtwwwap_user_id
		            )
		        );

		        setlocale( LC_ALL, $rtwwwap_locale );
			}
			else{
				$rtwwwap_mlm = get_option( 'rtwwwap_mlm_opt' );
				if( isset( $rtwwwap_mlm[ 'activate' ] ) && $rtwwwap_mlm[ 'activate' ] == 1 )
				{
					$wpdb->insert(
			            $wpdb->prefix.'rtwwwap_referrals',
			            array(
			                'aff_id'    			=> $rtwwwap_reff_id,
			                'type'    				=> 3,
			                'order_id'    			=> 0,
			                'date'    				=> date( 'Y-m-d H:i:s' ),
			                'status'    			=> 0,
			                'amount'    			=> 0,
			                'capped'    			=> 0,
			                'currency'    			=> 0,
			                'product_details'   	=> '',
			                'device'   				=> $rtwwwap_device,
			                'signed_up_id' 			=> $rtwwwap_user_id
			            )
			        );
				}
			}
		}

		// give reward points to the affiliate
	}

	function rtwwwap_referral_code_cookie_generation($rtwwwap_affiliate_id){

		$rtwwwap_user_id = get_current_user_id();

		if($rtwwwap_user_id)
		{

			$rtwwwap_user_meta = get_userdata($rtwwwap_user_id);
			$rtwwwap_user_roles = $rtwwwap_user_meta->roles[0];
			if($rtwwwap_user_roles != 'administrator')
			{
				update_user_meta($rtwwwap_user_id,'show_admin_bar_front', false);
			}
		}
		$rtwwwap_cookie_time 	= isset( $rtwwwap_extra_features[ 'cookie_time' ] ) ? $rtwwwap_extra_features[ 'cookie_time' ] : 0;
		if( get_user_meta( $rtwwwap_affiliate_id, 'rtwwwap_affiliate', true ) ){
			setcookie( 'rtwwwap_referral', $rtwwwap_affiliate_id, $rtwwwap_cookie_time, '/' );
		}
	}

	function rtwwwap_check_child_in_mlm( $rtwwwap_user_id, $rtwwwap_childs_to_start = 1 ){
		
		global $wpdb;
		$rtwwwap_parent = $wpdb->get_var( $wpdb->prepare( "SELECT `parent_id` FROM ".$wpdb->prefix."rtwwwap_mlm WHERE `aff_id` = %d AND `status` = %d", $rtwwwap_user_id, 1 ) );

		$rtwwwap_mlm = get_option( 'rtwwwap_mlm_opt' );

		$rtwwwap_mlm_type = $rtwwwap_mlm[ 'mlm_type' ];

		
		if( $rtwwwap_parent )
		{
			$rtwwwap_parent_childs = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(`id`) FROM ".$wpdb->prefix."rtwwwap_mlm WHERE `parent_id` = %d AND `status` = %d", $rtwwwap_parent, 1 ) );
			if( $rtwwwap_parent_childs == $rtwwwap_childs_to_start || $rtwwwap_mlm_type == 3){
				return $rtwwwap_parent;
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}
	function rtwwwap_give_mlm_comm( $rtwwwap_user_id, $rtwwwap_lastid, $rtwwwap_total_commission, $rtwwwap_currency, $rtwwwap_currency_sym, $rtwwwap_device, $rtwwwap_mlm_levels, $rtwwwap_childs_to_start, $rtwwwap_order_id, $order_total,$current_date )
	{
		$mlm_comm_check = true;
		if( !empty( $rtwwwap_mlm_levels ) )
		{
			foreach( $rtwwwap_mlm_levels as $rtwwwap_mlm_key => $rtwwwap_mlm_value ){
				$rtwwwap_parent_id = $this->rtwwwap_check_child_in_mlm( $rtwwwap_user_id, $rtwwwap_childs_to_start );


				if(in_array('addon_for_MLM_qualification/affiliate_mlm_addon.php', apply_filters('active_plugins', get_option('active_plugins') ) ) ){
					$mlm_comm_check = apply_filters('rtwwwap_check_mlm_qualification',$current_date,$order_total,$rtwwwap_parent_id);	
				}

				// var_dump($mlm_comm_check);
				// die("adczsfg");
				
				if( $rtwwwap_parent_id && $mlm_comm_check ){
				
					$rtwwwap_user_id = $rtwwwap_parent_id;
					$rtwwwap_commission = 0;
					if( $rtwwwap_mlm_value[ 'mlm_level_comm_type' ] == 0 ){
						$rtwwwap_commission = ( $rtwwwap_total_commission*$rtwwwap_mlm_value[ 'mlm_level_comm_amount' ] )/100;
					}
					elseif( $rtwwwap_mlm_value[ 'mlm_level_comm_type' ] == 1 ){
						$rtwwwap_commission = $rtwwwap_mlm_value[ 'mlm_level_comm_amount' ];
					}

					if( get_user_meta( $rtwwwap_user_id, 'rtwwwap_referral_mail', true ) == 'on' ){
						$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );
						$rtwwwap_decimal_places = $rtwwwap_extra_features['decimal_places'].'f';
						$rtwwwap_to 			= get_user_by( 'id', $rtwwwap_user_id );
						$rtwwwap_to 			= esc_html( $rtwwwap_to->user_email );

						$all_emails = get_option('customize_email', false);

						$generate_mlm_commission = get_option('generate_mlm_commission','null');

						if(isset($all_emails['Email on Generating MLM Commission']['subject'])){
							$rtwwwap_subject_text = $all_emails['Email on Generating MLM Commission']['subject'];
							$rtwwwap_message_text = $all_emails['Email on Generating MLM Commission']['content'];
						}

						$rtwwwap_subject 		= esc_html__( $rtwwwap_subject_text, 'rtwwwap-wp-wc-affiliate-program' );
						$rtwwwap_message 		= sprintf( '%s %s%01.'.$rtwwwap_decimal_places, esc_html__( $rtwwwap_message_text , 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_currency_sym, $rtwwwap_commission );
						$rtwwwap_from_name 		= esc_html( get_bloginfo( 'name' ) );
						$rtwwwap_from_email 	= esc_html( get_bloginfo( 'admin_email' ) );

						$rtwwwap_headers[] 		= 'Content-Type: text/html; charset=utf-8';
						$rtwwwap_headers[] 		= sprintf( '%s: %s <%s>', esc_html__( 'From', 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_from_name, $rtwwwap_from_email );

						// mail to affiliate
						if($generate_mlm_commission == "true"){
							wp_mail( $rtwwwap_to, $rtwwwap_subject, $rtwwwap_message, $rtwwwap_headers );
						}

						if( isset( $rtwwwap_extra_features[ 'mail_to_admin' ] ) && $rtwwwap_extra_features[ 'mail_to_admin' ] == 1 ){
							// mail to admin
							$rtwwwap_message = sprintf( '%s %s%01.'.$rtwwwap_decimal_places, esc_html__( 'Generated a new MLM commission of amount', 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_currency_sym, $rtwwwap_commission );
							wp_mail( $rtwwwap_from_email, $rtwwwap_subject, $rtwwwap_message, $rtwwwap_headers );
						}
					}

					//insert mlm row for this level
					if( $rtwwwap_commission ){
						global $wpdb;
						$rtwwwap_prod_details = 'mlm_'.$rtwwwap_order_id;

						$rtwwwap_locale = get_locale();
						setlocale( LC_NUMERIC, $rtwwwap_locale );

						$rtwwwap_updated = $wpdb->insert(
				            $wpdb->prefix.'rtwwwap_referrals',
				            array(
				                'aff_id'    			=> $rtwwwap_user_id,
				                'type'    				=> 4,
				                'order_id'    			=> esc_html( $rtwwwap_order_id ),
				                'date'    				=> date( 'Y-m-d H:i:s' ),
				                'status'    			=> 0,
				                'amount'    			=> esc_html( $rtwwwap_commission ),
				                'capped'    			=> 0,
				                'currency'    			=> $rtwwwap_currency,
				                'product_details'   	=> $rtwwwap_prod_details,
				                'device'   				=> $rtwwwap_device
				            )
				        );

						setlocale( LC_ALL, $rtwwwap_locale );

						if( $rtwwwap_updated ){
					        $rtwwwap_referral_noti = get_option( 'rtwwwap_referral_noti' )+1;
					        update_option( 'rtwwwap_referral_noti', $rtwwwap_referral_noti );
						}
					}
				}
			}
		}
	}

	function rtwwwap_loop_each_parent_without_html($rtwwwap_user_id,$aff_count,$rtwwwap_mlm_depth,$rtwwwap_count, $rtwwwap_active=0, $rtwwwap_mlm_child=0)
	{
		global $wpdb;
		$rtwwwap_count = $rtwwwap_count+1;

		if( $rtwwwap_active == 'false' ){
			$rtwwwap_mlm_chain = $wpdb->get_results( $wpdb->prepare( "SELECT `aff_id`, `status` FROM ".$wpdb->prefix."rtwwwap_mlm WHERE `parent_id`=%d AND `status`=1", $rtwwwap_user_id ), ARRAY_A );
		}
		else{
			$rtwwwap_mlm_chain = $wpdb->get_results( $wpdb->prepare( "SELECT `aff_id`, `status` FROM ".$wpdb->prefix."rtwwwap_mlm WHERE `parent_id`=%d", $rtwwwap_user_id ), ARRAY_A );
		}

		if( !empty( $rtwwwap_mlm_chain ) ){
			if( count( $rtwwwap_mlm_chain ) > $rtwwwap_mlm_child && $rtwwwap_active == 'false' ){
				global $rtwwwap_improper_chain;
				$rtwwwap_improper_chain = true;
			}
			$rtwwwap_mlm = get_option( 'rtwwwap_mlm_opt' );
			$rtwwwap_mlm_user_status_checked = 0;
			if( isset( $rtwwwap_mlm[ 'user_status' ] ) && $rtwwwap_mlm[ 'user_status' ] == 1 ){
				$rtwwwap_mlm_user_status_checked = 1;
			}

			foreach( $rtwwwap_mlm_chain as $rtwwwap_key => $rtwwwap_value ){
				$rtwwwap_name = get_userdata( $rtwwwap_value[ 'aff_id' ] );
				// $rtwwwap_name = $rtwwwap_name->data->display_name;

				if( $rtwwwap_mlm_user_status_checked ){
					if( $rtwwwap_value[ 'status' ] != 0 ){
						$aff_count++;
						// $aff_count[] = array('id' =>"current_aff_id","Rank"=>"current_aff_rank");
					}
				}
				else{
					if( $rtwwwap_value[ 'status' ] != 0 ){
						$aff_count++;
					}
				}


				if( $rtwwwap_count <= $rtwwwap_mlm_depth ){
					$rtwwwap_get_return = $this->rtwwwap_loop_each_parent_without_html( $rtwwwap_value[ 'aff_id' ], $aff_count, $rtwwwap_mlm_depth, $rtwwwap_count, $rtwwwap_active, $rtwwwap_mlm_child );

					if( $rtwwwap_get_return ){
						$aff_count = $rtwwwap_get_return;
					}
				}
			}
		}

		return $aff_count;
	}


	function rtwwwap_loop_each_parent_all_aff_rank($rtwwwap_user_id,$aff_count,$rtwwwap_mlm_depth,$rtwwwap_count, $rtwwwap_active=0, $rtwwwap_mlm_child=0)
	{
		global $wpdb;
		$rtwwwap_count = $rtwwwap_count+1;

		if( $rtwwwap_active == 'false' ){
			$rtwwwap_mlm_chain = $wpdb->get_results( $wpdb->prepare( "SELECT `aff_id`, `status` FROM ".$wpdb->prefix."rtwwwap_mlm WHERE `parent_id`=%d AND `status`=1", $rtwwwap_user_id ), ARRAY_A );
		}
		else{
			$rtwwwap_mlm_chain = $wpdb->get_results( $wpdb->prepare( "SELECT `aff_id`, `status` FROM ".$wpdb->prefix."rtwwwap_mlm WHERE `parent_id`=%d", $rtwwwap_user_id ), ARRAY_A );
		}
	
		if( !empty( $rtwwwap_mlm_chain ) ){
			if( count( $rtwwwap_mlm_chain ) > $rtwwwap_mlm_child && $rtwwwap_active == 'false' ){
				global $rtwwwap_improper_chain;
				$rtwwwap_improper_chain = true;
			}
			$rtwwwap_mlm = get_option( 'rtwwwap_mlm_opt' );
			$rtwwwap_mlm_user_status_checked = 0;
			if( isset( $rtwwwap_mlm[ 'user_status' ] ) && $rtwwwap_mlm[ 'user_status' ] == 1 ){
				$rtwwwap_mlm_user_status_checked = 1;
			}
			
			foreach( $rtwwwap_mlm_chain as $rtwwwap_key => $rtwwwap_value ){
				$rank_detail = get_user_meta($rtwwwap_value[ 'aff_id' ],'rank_detail',true);
				$rtwwwap_name = get_userdata( $rtwwwap_value[ 'aff_id' ] );
				// $rtwwwap_name = $rtwwwap_name->data->display_name;
				
			
				if( $rtwwwap_mlm_user_status_checked ){
					if( $rtwwwap_value[ 'status' ] != 0 ){
						
						$aff_count = array(
						'rnk_aff_id' =>$rtwwwap_value[ 'aff_id' ],
						"curr_rank"=> $rank_detail,
						);

					}
					
				}

				else{
					if( $rtwwwap_value[ 'status' ] != 0 ){
						$aff_count[] = array(
							'rnk_aff_id' =>$rtwwwap_value[ 'aff_id' ],
							"curr_rank"=> $rank_detail,
							// "curr_rnk_id" => $rtwwdp_post_id
						);
					}
				}


				if( $rtwwwap_count <= $rtwwwap_mlm_depth ){
					$rtwwwap_get_return = $this->rtwwwap_loop_each_parent_all_aff_rank( $rtwwwap_value[ 'aff_id' ], $aff_count, $rtwwwap_mlm_depth, $rtwwwap_count, $rtwwwap_active, $rtwwwap_mlm_child );
					
					if( $rtwwwap_get_return ){
						$aff_count = $rtwwwap_get_return;
					}
				}
				
			}
			
		}

		return $aff_count;
	}


	function rtwwwap_loop_each_parent( $rtwwwap_user_id, $rtwwwap_html, $rtwwwap_mlm_depth, $rtwwwap_count, $rtwwwap_active = 0, $rtwwwap_mlm_child = 0){
		$rtwwwap_verification_done = get_option( 'rtwwwap_verification_done', array() );
		$rtwwwap_verification_done_status = isset($rtwwwap_verification_done['status']) ? $rtwwwap_verification_done['status'] : false;
		$rtwwwap_verification_done_purchase = isset($rtwwwap_verification_done['purchase_code']) ? $rtwwwap_verification_done['purchase_code'] : false;
		if( empty( $rtwwwap_verification_done ) || $rtwwwap_verification_done_status == false || empty($rtwwwap_verification_done_purchase) )
		{
			return;
		}
		global $wpdb;
		$rtwwwap_count = $rtwwwap_count+1;

		if( $rtwwwap_active == 'false' ){
			$rtwwwap_mlm_chain = $wpdb->get_results( $wpdb->prepare( "SELECT `aff_id`, `status` FROM ".$wpdb->prefix."rtwwwap_mlm WHERE `parent_id`=%d AND `status`=1", $rtwwwap_user_id ), ARRAY_A );
		}
		else{
			$rtwwwap_mlm_chain = $wpdb->get_results( $wpdb->prepare( "SELECT `aff_id`, `status` FROM ".$wpdb->prefix."rtwwwap_mlm WHERE `parent_id`=%d", $rtwwwap_user_id ), ARRAY_A );
		}

		if( !empty( $rtwwwap_mlm_chain ) ){
			if( count( $rtwwwap_mlm_chain ) > $rtwwwap_mlm_child && $rtwwwap_active == 'false' ){
				global $rtwwwap_improper_chain;
				$rtwwwap_improper_chain = true;
			}
			$rtwwwap_mlm = get_option( 'rtwwwap_mlm_opt' );
			$rtwwwap_mlm_user_status_checked = 0;
			if( isset( $rtwwwap_mlm[ 'user_status' ] ) && $rtwwwap_mlm[ 'user_status' ] == 1 ){
				$rtwwwap_mlm_user_status_checked = 1;
			}

			$rtwwwap_html .= '<ul>';
			foreach( $rtwwwap_mlm_chain as $rtwwwap_key => $rtwwwap_value ){
				$rtwwwap_name = get_userdata( $rtwwwap_value[ 'aff_id' ] );
				$rtwwwap_name = $rtwwwap_name->data->display_name;

				if( $rtwwwap_mlm_user_status_checked ){
					if( $rtwwwap_value[ 'status' ] == 0 ){
						$rtwwwap_html .= 	'<li data-class="rtwwwap_disabled" data-id="'.$rtwwwap_value[ 'aff_id' ].'">';
					}
					else{
						$rtwwwap_html .= 	'<li data-class="rtwwwap_enabled" data-id="'.$rtwwwap_value[ 'aff_id' ].'">';
					}
				}
				else{
					if( $rtwwwap_value[ 'status' ] == 0 ){
						$rtwwwap_html .= 	'<li data-class="rtwwwap_noedit_disabled" data-id="'.$rtwwwap_value[ 'aff_id' ].'">';
					}
					else{
						$rtwwwap_html .= 	'<li data-class="rtwwwap_noedit" data-id="'.$rtwwwap_value[ 'aff_id' ].'">';
					}
				}

				$rtwwwap_html .= $rtwwwap_name;

				if( $rtwwwap_count <= $rtwwwap_mlm_depth ){
					$rtwwwap_get_return = $this->rtwwwap_loop_each_parent( $rtwwwap_value[ 'aff_id' ], $rtwwwap_html, $rtwwwap_mlm_depth, $rtwwwap_count, $rtwwwap_active, $rtwwwap_mlm_child );

					if( $rtwwwap_get_return ){
						$rtwwwap_html = $rtwwwap_get_return;
						$rtwwwap_html .= '</li>';
					}
				}
				else{
					$rtwwwap_html .= '</li>';
				}
			}
			$rtwwwap_html .= '</ul>';
		}

		return $rtwwwap_html;
	}

	function rtwwwap_public_get_mlm_chain_callback(){
		$rtwwwap_verification_done = get_option( 'rtwwwap_verification_done', array() );
		$rtwwwap_verification_done_status = isset($rtwwwap_verification_done['status']) ? $rtwwwap_verification_done['status'] : false;
		$rtwwwap_verification_done_purchase = isset($rtwwwap_verification_done['purchase_code']) ? $rtwwwap_verification_done['purchase_code'] : false;
		if( empty( $rtwwwap_verification_done ) || $rtwwwap_verification_done_status == false || empty($rtwwwap_verification_done_purchase) )
		{
			return;
		}
		global $rtwwwap_improper_chain;
		$rtwwwap_improper_chain = false;

		$rtwwwap_mlm 		= get_option( 'rtwwwap_mlm_opt' );
		$rtwwwap_mlm_depth 	= isset( $rtwwwap_mlm[ 'depth' ] ) ? $rtwwwap_mlm[ 'depth' ] : 0;
		$rtwwwap_mlm_child 	= isset( $rtwwwap_mlm[ 'child' ] ) ? $rtwwwap_mlm[ 'child' ] : 1;
		$rtwwwap_user_id 	= isset($_POST[ 'rtwwwap_user_id' ])?$_POST[ 'rtwwwap_user_id' ]:"";
		$rtwwwap_active 	= isset($_POST[ 'rtwwwap_active' ])?$_POST[ 'rtwwwap_active' ]:"";

		$rtwwwap_mlm_user_status_checked = 0;
		if( isset( $rtwwwap_mlm[ 'user_status' ] ) && $rtwwwap_mlm[ 'user_status' ] == 1 ){
			$rtwwwap_mlm_user_status_checked = 1;
		}

		$rtwwwap_name = get_userdata( $rtwwwap_user_id );
		$rtwwwap_name = $rtwwwap_name->data->display_name;
		
		$rtwwwap_html = '';
		$rtwwwap_html .= 	'<ul id="rtwwwap_mlm_data">';
		$rtwwwap_html .= 		'<li data-id="'.$rtwwwap_user_id.'" >'.$rtwwwap_name;

		if( $rtwwwap_mlm_depth ){
			$rtwwwap_final_html = $this->rtwwwap_loop_each_parent( $rtwwwap_user_id, $rtwwwap_html, $rtwwwap_mlm_depth, 1, $rtwwwap_active, $rtwwwap_mlm_child );
			$rtwwwap_final_html .= '</li></ul>';
		}
		else{
			$rtwwwap_html .= '</li></ul>';
			$rtwwwap_final_html = $rtwwwap_html;
		}

		echo json_encode( array( 'rtwwwap_tree_html' => $rtwwwap_final_html, 'rtwwwap_improper_chain' => $rtwwwap_improper_chain, 'rtwwwap_mlm_user_status_checked' => $rtwwwap_mlm_user_status_checked ) ); die;
	}

	function rtwwwap_public_deactive_aff_callback(){
		$rtwwwap_verification_done = get_option( 'rtwwwap_verification_done', array() );
		$rtwwwap_verification_done_status = isset($rtwwwap_verification_done['status']) ? $rtwwwap_verification_done['status'] : false;
		$rtwwwap_verification_done_purchase = isset($rtwwwap_verification_done['purchase_code']) ? $rtwwwap_verification_done['purchase_code'] : false;
		if( empty( $rtwwwap_verification_done ) || $rtwwwap_verification_done_status == false || empty($rtwwwap_verification_done_purchase) )
		{
			return;
		}
		global $wpdb;
		$rtwwwap_aff_id 	= $_POST[ 'rtwwwap_aff_id' ];
		$rtwwwap_parent_id 	= $_POST[ 'rtwwwap_parent_id' ];

		$rtwwwap_updated = 	$wpdb->update(
								$wpdb->prefix.'rtwwwap_mlm',
								array( 'status' => 0 ),
								array( 'aff_id' => $rtwwwap_aff_id, 'parent_id' => $rtwwwap_parent_id ),
								array( '%d' ),
								array( '%d', '%d' )
							);

		if( $rtwwwap_updated ){
			echo json_encode( array( 'rtwwwap_status' => true, 'rtwwwap_message' => esc_html__( 'Deactivated', 'rtwwwap-wp-wc-affiliate-program' ) ) );
			die;
		}
		else{
			echo json_encode( array( 'rtwwwap_status' => false, 'rtwwwap_message' => esc_html__( 'Something Went Wrong', 'rtwwwap-wp-wc-affiliate-program' ) ) );
			die;
		}
	}

	function rtwwwap_public_active_aff_callback(){
		$rtwwwap_verification_done = get_option( 'rtwwwap_verification_done', array() );
		$rtwwwap_verification_done_status = isset($rtwwwap_verification_done['status']) ? $rtwwwap_verification_done['status'] : false;
		$rtwwwap_verification_done_purchase = isset($rtwwwap_verification_done['purchase_code']) ? $rtwwwap_verification_done['purchase_code'] : false;
		if( empty( $rtwwwap_verification_done ) || $rtwwwap_verification_done_status == false || empty($rtwwwap_verification_done_purchase) )
		{
			return;
		}
		global $wpdb;
		$rtwwwap_aff_id 	= $_POST[ 'rtwwwap_aff_id' ];
		$rtwwwap_parent_id 	= $_POST[ 'rtwwwap_parent_id' ];

		$rtwwwap_updated = 	$wpdb->update(
								$wpdb->prefix.'rtwwwap_mlm',
								array( 'status' => 1 ),
								array( 'aff_id' => $rtwwwap_aff_id, 'parent_id' => $rtwwwap_parent_id ),
								array( '%d' ),
								array( '%d', '%d' )
							);

		if( $rtwwwap_updated ){
			echo json_encode( array( 'rtwwwap_status' => true, 'rtwwwap_message' => esc_html__( 'Activated', 'rtwwwap-wp-wc-affiliate-program' ) ) );
			die;
		}
		else{
			echo json_encode( array( 'rtwwwap_status' => false, 'rtwwwap_message' => esc_html__( 'Something Went Wrong', 'rtwwwap-wp-wc-affiliate-program' ) ) );
			die;
		}
	}

	function rtwwwap_send_rqst_callback($withdrawal_amount, $rtwwwap_user_id){
		
		
			if( RTWWWAP_IS_WOO == 1 ){
				$rtwwwap_currency_sym = esc_html( get_woocommerce_currency_symbol() );
			}
			else{
				require_once( RTWWWAP_DIR.'includes/rtwaffiliatehelper.php' );

				$rtwwwap_currency		= isset( $rtwwwap_extra_features[ 'currency' ] ) ? $rtwwwap_extra_features[ 'currency' ] : 'USD';
				$rtwwwap_curr_obj 		= new RtwAffiliateHelper();
				$rtwwwap_currency_sym 	= $rtwwwap_curr_obj->rtwwwap_curr_symbol( $rtwwwap_currency );

			}

		$rtwwwap_currency = get_woocommerce_currency();
		$rtwwwap_to 			= esc_html( get_bloginfo( 'admin_email' ) );

		$all_emails = get_option('customize_email', false);
		if(isset($all_emails['Email on Withdral Request']['subject'])){
			$rtwwwap_subject_text = $all_emails['Email on Withdral Request']['subject'];
			$rtwwwap_message_text = $all_emails['Email on Withdral Request']['content'];
		}

		$rtwwwap_subject 		= esc_html__( $rtwwwap_subject_text , 'rtwwwap-wp-wc-affiliate-program' );
		$rtwwwap_message 		= $rtwwwap_message_text." ".$rtwwwap_currency_sym.$withdrawal_amount;
		$rtwwwap_userdata 		= get_user_by( 'id', $rtwwwap_user_id );
		$rtwwwap_from_email 	= esc_html( $rtwwwap_userdata->data->user_email );
		$rtwwwap_from_name 		= esc_html( $rtwwwap_userdata->data->user_login );

		$rtwwwap_headers[] 		= 'Content-Type: text/html; charset=utf-8';
		$rtwwwap_headers[] 		= sprintf( '%s: %s <%s>', esc_html__( 'From', 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_from_name, $rtwwwap_from_email );
		$rtwwwap_success 		= wp_mail( $rtwwwap_to, $rtwwwap_subject, $rtwwwap_message, $rtwwwap_headers );

		return true;
		
	}


	// signup through woocommerce

	function rtwwwap_create_signup_referral_from_woo($rtwwwap_user_id)
	{
		// $rtwwwap_check_ajax = check_ajax_referer( 'rtwwwap-ajax-security-string', 'rtwwwap_security_check' );
		// if($rtwwwap_check_ajax)
		// {	
			$rtwwwap_refer_code = isset($_POST['rtwwwap_referral_code_field']) ? sanitize_text_field($_POST['rtwwwap_referral_code_field']) : "";

			$rtwwwap_user_email = isset($_POST['email']) ? sanitize_text_field($_POST['email']) : "";

			$rtwwwap_user_name = isset($_POST['username']) ? sanitize_text_field($_POST['username']) : "";   
			
			// $rtwwwap_login_page_id = get_option('rtwwwap_login_page_id');
			// $rtwwwap_affiliate_page_id = get_option('rtwwwap_affiliate_page_id');
			// if($rtwwwap_login_page_id)
			// {
			// 	$rtwwwap_register_redirect = get_permalink($rtwwwap_login_page_id);
			// }
			// else{
			// 	$rtwwwap_register_redirect = get_permalink($rtwwwap_affiliate_page_id);
			// }

			// send mail to the user

			$all_emails = get_option('customize_email', false);

			$signup_email = get_option('signup_email','null');

			if(isset($all_emails['Signup Email']['subject'])){
				$rtwwwap_subject_text = $all_emails['Signup Email']['subject'];
				$rtwwwap_message_text = $all_emails['Signup Email']['content'];
			}

			$rtwwwap_html = $rtwwwap_message_text;
				$rtwwwap_from 	= esc_html( get_bloginfo( 'admin_email' ) );
				$rtwwwap_user_name 	= esc_html( get_bloginfo( 'user_name' ) );
				$rtwwwap_subject = esc_html__( $rtwwwap_subject_text, 'rtwwwap-wp-wc-affiliate-program' );
				$rtwwwap_headers[] 		= 'Content-Type: text/html; charset=utf-8';
				$rtwwwap_headers[] 		= sprintf( '%s: %s <%s>', esc_html__( 'From', 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_user_name,$rtwwwap_from);
			
			if( $signup_email == "true"){
				wp_mail( $rtwwwap_user_email, $rtwwwap_subject,$rtwwwap_html,$rtwwwap_headers);
			}

			// code ended here

			
			$this->rtwwwap_user_register_signup_bonus($rtwwwap_user_id,$rtwwwap_refer_code);
		

	}


	function rtwwwap_add_code_field(){
		$rtwwwap_verification_done = get_option( 'rtwwwap_verification_done', array() );
		$rtwwwap_verification_done_status = isset($rtwwwap_verification_done['status']) ? $rtwwwap_verification_done['status'] : false;
		$rtwwwap_verification_done_purchase = isset($rtwwwap_verification_done['purchase_code']) ? $rtwwwap_verification_done['purchase_code'] : false;
		if( empty( $rtwwwap_verification_done ) || $rtwwwap_verification_done_status == false || empty($rtwwwap_verification_done_purchase) )
		{
			return;
		}
		$rtwwwap_extra_features 	= get_option( 'rtwwwap_extra_features_opt' );
		$rtwwwap_signup_bonus_type 	= isset( $rtwwwap_extra_features[ 'signup_bonus_type' ] ) ? $rtwwwap_extra_features[ 'signup_bonus_type' ] : 0;

		if( $rtwwwap_signup_bonus_type == 1 ){
		?>
		    <p class="form-row">
		        <label for="rtwwwap_referral_code_field"><?php esc_html_e( 'Referral Code', 'rtwwwap-wp-wc-affiliate-program' ); ?>
		        </label>
		        <input type="text" class="input-text" name="rtwwwap_referral_code_field" id="rtwwwap_referral_code_field" value="" />
		    </p>
		    <div class="clear"></div>
	    <?php
		}
    }

    /*
	* To show affiliate registartion page with shortcode
	*/
	function rtwwwap_aff_reg_page_callback(){
		$rtwwwap_verification_done = get_option( 'rtwwwap_verification_done', array() );
		$rtwwwap_verification_done_status = isset($rtwwwap_verification_done['status']) ? $rtwwwap_verification_done['status'] : false;
		$rtwwwap_verification_done_purchase = isset($rtwwwap_verification_done['purchase_code']) ? $rtwwwap_verification_done['purchase_code'] : false;
		if( empty( $rtwwwap_verification_done ) || $rtwwwap_verification_done_status == false || empty($rtwwwap_verification_done_purchase) )
		{
			return;
		}

		$rtwwwap_html = include( RTWWWAP_DIR.'public/templates/rtwwwap_aff_reg_page.php' );
		return $rtwwwap_html;
	}

	/*
	 To show affiliate login page with shortcode
	*/
	function rtwwwap_aff_login_page_callback(){
		$rtwwwap_verification_done = get_option( 'rtwwwap_verification_done', array() );
		$rtwwwap_verification_done_status = isset($rtwwwap_verification_done['status']) ? $rtwwwap_verification_done['status'] : false;
		$rtwwwap_verification_done_purchase = isset($rtwwwap_verification_done['purchase_code']) ? $rtwwwap_verification_done['purchase_code'] : false;
		if( empty( $rtwwwap_verification_done ) || $rtwwwap_verification_done_status == false || empty($rtwwwap_verification_done_purchase) )
		{
			return;
		}

		$rtwwwap_html = include( RTWWWAP_DIR.'public/templates/rtwwwap_aff_login_page.php' );
		return $rtwwwap_html;
	}

	


	/*
	 To show affiliate reset password page with shortcode
	*/
	public function rtwwwap_aff_reset_password_page_callback(){
		$rtwwwap_verification_done = get_option( 'rtwwwap_verification_done', array() );
		$rtwwwap_verification_done_status = isset($rtwwwap_verification_done['status']) ? $rtwwwap_verification_done['status'] : false;
		$rtwwwap_verification_done_purchase = isset($rtwwwap_verification_done['purchase_code']) ? $rtwwwap_verification_done['purchase_code'] : false;
		if( empty( $rtwwwap_verification_done ) || $rtwwwap_verification_done_status == false || empty($rtwwwap_verification_done_purchase) )
		{
			return;
		}
		wp_enqueue_script( 'utils' );
		wp_enqueue_script( 'user-profile' );
			$rtwwwap_html = include( RTWWWAP_DIR.'public/templates/rtwwwap_aff_reset_password_page.php' );
			return $rtwwwap_html;
	}


	/*
	 * to provide unlimited or lifetime commission
	 */
	function rtwwwap_unlimited_reff_comm( $rtwwwap_order_id = 0, $rtwwwap_referrer_id = 0 )
	{
		$rtwwwap_aff_prod_price 	= array();
		$rtwwwap_verification_done = get_option( 'rtwwwap_verification_done', array() );
		$rtwwwap_verification_done_status = isset($rtwwwap_verification_done['status']) ? $rtwwwap_verification_done['status'] : false;
		$rtwwwap_verification_done_purchase = isset($rtwwwap_verification_done['purchase_code']) ? $rtwwwap_verification_done['purchase_code'] : false;
		if( empty( $rtwwwap_verification_done ) || $rtwwwap_verification_done_status == false || empty($rtwwwap_verification_done_purchase) )
		{
			return;
		}
		if( $rtwwwap_order_id && $rtwwwap_referrer_id ){
			global $wpdb;
			$rtwwwap_commission_settings = get_option( 'rtwwwap_commission_settings_opt' );
			$rtwwwap_referral 	= array( $rtwwwap_referrer_id );
			$rtwwwap_order 		= wc_get_order( $rtwwwap_order_id );
			$rtwwwap_comm_base 	= isset( $rtwwwap_commission_settings[ 'comm_base' ] ) ? $rtwwwap_commission_settings[ 'comm_base' ] : '1';
			$rtwwwap_total_commission	= 0;
			$rtwwwap_aff_prod_details 	= array();
			$rtwwwap_user_id 			= esc_html( $rtwwwap_referral[ 0 ] );

			if( RTWWWAP_IS_WOO == 1 ){
				$rtwwwap_currency 		= get_woocommerce_currency();
				$rtwwwap_currency_sym 	= get_woocommerce_currency_symbol();
			}
			else{
				require_once( RTWWWAP_DIR.'includes/rtwaffiliatehelper.php' );

				$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );
				$rtwwwap_currency 		= isset( $rtwwwap_extra_features[ 'currency' ] ) ? $rtwwwap_extra_features[ 'currency' ] : 'USD';
				$rtwwwap_curr_obj 		= new RtwAffiliateHelper();
				$rtwwwap_currency_sym 	= $rtwwwap_curr_obj->rtwwwap_curr_symbol( $rtwwwap_currency );
			}

			$rtwwwap_commission_type 	= 0;
			$rtwwwap_shared 			= false;
			$rtwwwap_product_url 		= false;

			if( $rtwwwap_comm_base == 1 ){
				$rtwwwap_per_prod_mode 			= isset( $rtwwwap_commission_settings[ 'per_prod_mode' ] ) ? $rtwwwap_commission_settings[ 'per_prod_mode' ] : 0;
				$rtwwwap_all_commission 		= isset( $rtwwwap_commission_settings[ 'all_commission' ] ) ? $rtwwwap_commission_settings[ 'all_commission' ] : 0;
				$rtwwwap_all_commission_type 	= isset( $rtwwwap_commission_settings[ 'all_commission_type' ] ) ? $rtwwwap_commission_settings[ 'all_commission_type' ] : 'percentage';
				$rtwwwap_per_cat 				= isset( $rtwwwap_commission_settings[ 'per_cat' ] ) ? $rtwwwap_commission_settings[ 'per_cat' ] : array();

				foreach( $rtwwwap_order->get_items() as $rtwwwap_item_key => $rtwwwap_item_values )
				{
					$rtwwwap_prod_comm 		= '';
					$rtwwwap_product_id 	= $rtwwwap_item_values->get_product_id();
					$rtwwwap_product_price	= $rtwwwap_item_values->get_total();
					$rtwwwap_product_terms 	= get_the_terms( $rtwwwap_product_id, 'product_cat' );
					$rtwwwap_product_cat_id = $rtwwwap_product_terms[0]->term_id;
					$rtwwwap_aff_prod_price [] = $rtwwwap_product_price; 

					if( $rtwwwap_commission_type == 0 )
					{
					    if( $rtwwwap_per_prod_mode == 1 ){
							$rtwwwap_prod_per_comm = get_post_meta( $rtwwwap_product_id, 'rtwwwap_percentage_commission_box', true );

							if( $rtwwwap_prod_per_comm > 0 ){
								$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_prod_per_comm ) / 100;
								$rtwwwap_aff_prod_details[] = array(
						    					'product_id' 		=> $rtwwwap_product_id,
						    					'product_price' 	=> $rtwwwap_product_price,
						    					'commission_fix' 	=> '',
						    					'commission_perc' 	=> $rtwwwap_prod_per_comm,
						    					'prod_commission' 	=> $rtwwwap_prod_comm
						    				);

					    		$rtwwwap_total_commission += $rtwwwap_prod_comm;
							}
							elseif( $rtwwwap_prod_per_comm === '0' ){
								// no commission needs to be generated for this product
							}
							else{
								if( !empty( $rtwwwap_per_cat ) ){
									$rtwwwap_cat_per_comm = 0;
									$rtwwwap_cat_fix_comm = 0;
									$rtwwwap_flag = false;
									foreach( $rtwwwap_per_cat as $rtwwwap_key => $rtwwwap_value ){
										if( in_array( $rtwwwap_product_cat_id, $rtwwwap_value[ 'ids' ] ) ){
											$rtwwwap_cat_per_comm = $rtwwwap_value[ 'cat_percentage_commission' ];
											$rtwwwap_cat_fix_comm = $rtwwwap_value[ 'cat_fixed_commission' ];
											$rtwwwap_flag = true;

											break;
										}
									}
									if( $rtwwwap_flag ){
										if( $rtwwwap_cat_per_comm > 0 ){
											$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_cat_per_comm ) / 100;
										}
										if( $rtwwwap_cat_fix_comm > 0 ){
											$rtwwwap_prod_comm += $rtwwwap_cat_fix_comm;
										}

										if( $rtwwwap_prod_comm != '' ){
											$rtwwwap_aff_prod_details[] = array(
								    					'product_id' 		=> $rtwwwap_product_id,
								    					'product_price' 	=> $rtwwwap_product_price,
								    					'commission_fix' 	=> $rtwwwap_cat_fix_comm,
								    					'commission_perc' 	=> $rtwwwap_cat_per_comm,
								    					'prod_commission' 	=> $rtwwwap_prod_comm
								    				);

							    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
							    		}
									}
									else{
										if( $rtwwwap_all_commission ){
											if( $rtwwwap_all_commission_type == 'percentage' ){
												$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
											}
											elseif( $rtwwwap_all_commission_type == 'fixed' ){
												$rtwwwap_prod_comm += $rtwwwap_all_commission;
											}
											$rtwwwap_aff_prod_details[] = array(
								    					'product_id' 		=> $rtwwwap_product_id,
								    					'product_price' 	=> $rtwwwap_product_price,
								    					'commission_fix' 	=> '',
								    					'commission_perc' 	=> '',
								    					'prod_commission' 	=> $rtwwwap_prod_comm
								    				);

							    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
							    		}
									}
								}
								else{
									if( $rtwwwap_all_commission ){
										if( $rtwwwap_all_commission_type == 'percentage' ){
											$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
										}
										elseif( $rtwwwap_all_commission_type == 'fixed' ){
											$rtwwwap_prod_comm += $rtwwwap_all_commission;
										}
										$rtwwwap_aff_prod_details[] = array(
							    					'product_id' 		=> $rtwwwap_product_id,
							    					'product_price' 	=> $rtwwwap_product_price,
							    					'commission_fix' 	=> '',
								    				'commission_perc' 	=> '',
							    					'prod_commission' 	=> $rtwwwap_prod_comm
							    				);

						    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
						    		}
								}
							}
						}
						elseif( $rtwwwap_per_prod_mode == 2 ){
							$rtwwwap_prod_fix_comm = get_post_meta( $rtwwwap_product_id, 'rtwwwap_fixed_commission_box', true );

							if( $rtwwwap_prod_fix_comm > 0 ){
								$rtwwwap_prod_comm = $rtwwwap_prod_fix_comm;
								$rtwwwap_aff_prod_details[] = array(
						    					'product_id' 		=> $rtwwwap_product_id,
						    					'product_price' 	=> $rtwwwap_product_price,
						    					'commission_fix' 	=> $rtwwwap_prod_fix_comm,
						    					'commission_perc' 	=> '',
						    					'prod_commission' 	=> $rtwwwap_prod_comm
						    				);

					    		$rtwwwap_total_commission += $rtwwwap_prod_comm;
							}
							elseif( $rtwwwap_prod_fix_comm === '0' ){
								// no commission needs to be generated for this product
							}
							else{
								if( !empty( $rtwwwap_per_cat ) ){
									$rtwwwap_cat_per_comm = 0;
									$rtwwwap_cat_fix_comm = 0;
									$rtwwwap_flag = false;
									foreach( $rtwwwap_per_cat as $rtwwwap_key => $rtwwwap_value ){
										if( in_array( $rtwwwap_product_cat_id, $rtwwwap_value[ 'ids' ] ) ){
											$rtwwwap_cat_per_comm = $rtwwwap_value[ 'cat_percentage_commission' ];
											$rtwwwap_cat_fix_comm = $rtwwwap_value[ 'cat_fixed_commission' ];
											$rtwwwap_flag = true;

											break;
										}
									}
									if( $rtwwwap_flag ){
										if( $rtwwwap_cat_per_comm > 0 ){
											$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_cat_per_comm ) / 100;
										}
										if( $rtwwwap_cat_fix_comm > 0 ){
											$rtwwwap_prod_comm += $rtwwwap_cat_fix_comm;
										}

										if( $rtwwwap_prod_comm != '' ){
											$rtwwwap_aff_prod_details[] = array(
								    					'product_id' 		=> $rtwwwap_product_id,
								    					'product_price' 	=> $rtwwwap_product_price,
								    					'commission_fix' 	=> $rtwwwap_cat_fix_comm,
								    					'commission_perc' 	=> $rtwwwap_cat_per_comm,
								    					'prod_commission' 	=> $rtwwwap_prod_comm
								    				);

							    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
							    		}
									}
									else{
										if( $rtwwwap_all_commission ){
											if( $rtwwwap_all_commission_type == 'percentage' ){
												$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
											}
											elseif( $rtwwwap_all_commission_type == 'fixed' ){
												$rtwwwap_prod_comm += $rtwwwap_all_commission;
											}
											$rtwwwap_aff_prod_details[] = array(
								    					'product_id' 		=> $rtwwwap_product_id,
								    					'product_price' 	=> $rtwwwap_product_price,
								    					'commission_fix' 	=> '',
								    					'commission_perc' 	=> '',
								    					'prod_commission' 	=> $rtwwwap_prod_comm
								    				);

							    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
							    		}
									}
								}
								else{
									if( $rtwwwap_all_commission ){
										if( $rtwwwap_all_commission_type == 'percentage' ){
											$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
										}
										elseif( $rtwwwap_all_commission_type == 'fixed' ){
											$rtwwwap_prod_comm += $rtwwwap_all_commission;
										}
										$rtwwwap_aff_prod_details[] = array(
							    					'product_id' 		=> $rtwwwap_product_id,
							    					'product_price' 	=> $rtwwwap_product_price,
							    					'commission_fix' 	=> '',
								    				'commission_perc' 	=> '',
							    					'prod_commission' 	=> $rtwwwap_prod_comm
							    				);

						    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
						    		}
								}
							}
						}
						elseif( $rtwwwap_per_prod_mode == 3 ){
							$rtwwwap_prod_per_comm = get_post_meta( $rtwwwap_product_id, 'rtwwwap_percentage_commission_box', true );
							$rtwwwap_prod_fix_comm = get_post_meta( $rtwwwap_product_id, 'rtwwwap_fixed_commission_box', true );

							if( $rtwwwap_prod_per_comm > 0 ){
								$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_prod_per_comm ) / 100;
							}

							if( $rtwwwap_prod_fix_comm > 0 ){
								$rtwwwap_prod_comm += $rtwwwap_prod_fix_comm;
							}

							if( $rtwwwap_prod_comm === '' ){
								if( $rtwwwap_prod_per_comm !== '0' && $rtwwwap_prod_fix_comm !== '0' ){
									if( !empty( $rtwwwap_per_cat ) ){
										$rtwwwap_cat_per_comm = 0;
										$rtwwwap_cat_fix_comm = 0;
										$rtwwwap_flag = false;
										foreach( $rtwwwap_per_cat as $rtwwwap_key => $rtwwwap_value ){
											if( in_array( $rtwwwap_product_cat_id, $rtwwwap_value[ 'ids' ] ) ){
												$rtwwwap_cat_per_comm = $rtwwwap_value[ 'cat_percentage_commission' ];
												$rtwwwap_cat_fix_comm = $rtwwwap_value[ 'cat_fixed_commission' ];
												$rtwwwap_flag = true;

												break;
											}
										}
										if( $rtwwwap_flag ){
											if( $rtwwwap_cat_per_comm > 0 ){
												$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_cat_per_comm ) / 100;
											}
											if( $rtwwwap_cat_fix_comm > 0 ){
												$rtwwwap_prod_comm += $rtwwwap_cat_fix_comm;
											}

											if( $rtwwwap_prod_comm != '' ){
												$rtwwwap_aff_prod_details[] = array(
									    					'product_id' 		=> $rtwwwap_product_id,
									    					'product_price' 	=> $rtwwwap_product_price,
									    					'commission_fix' 	=> $rtwwwap_cat_fix_comm,
									    					'commission_perc' 	=> $rtwwwap_cat_per_comm,
									    					'prod_commission' 	=> $rtwwwap_prod_comm
									    				);

								    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
								    		}
										}
										else{
											if( $rtwwwap_all_commission ){
												if( $rtwwwap_all_commission_type == 'percentage' ){
													$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
												}
												elseif( $rtwwwap_all_commission_type == 'fixed' ){
													$rtwwwap_prod_comm += $rtwwwap_all_commission;
												}
												$rtwwwap_aff_prod_details[] = array(
									    					'product_id' 		=> $rtwwwap_product_id,
									    					'product_price' 	=> $rtwwwap_product_price,
									    					'commission_fix' 	=> '',
									    					'commission_perc' 	=> '',
									    					'prod_commission' 	=> $rtwwwap_prod_comm
									    				);

								    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
								    		}
										}
									}
									else{
										if( $rtwwwap_all_commission ){
											if( $rtwwwap_all_commission_type == 'percentage' ){
												$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
											}
											elseif( $rtwwwap_all_commission_type == 'fixed' ){
												$rtwwwap_prod_comm += $rtwwwap_all_commission;
											}
											$rtwwwap_aff_prod_details[] = array(
								    					'product_id' 		=> $rtwwwap_product_id,
								    					'product_price' 	=> $rtwwwap_product_price,
								    					'commission_fix' 	=> '',
									    				'commission_perc' 	=> '',
								    					'prod_commission' 	=> $rtwwwap_prod_comm
								    				);

							    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
							    		}
									}
								}
							}
							else{
								$rtwwwap_aff_prod_details[] = array(
					    					'product_id' 		=> $rtwwwap_product_id,
					    					'product_price' 	=> $rtwwwap_product_price,
					    					'commission_fix' 	=> $rtwwwap_prod_fix_comm,
						    				'commission_perc' 	=> $rtwwwap_prod_per_comm,
					    					'prod_commission' 	=> $rtwwwap_prod_comm
					    				);

				    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
							}
						}
						elseif( $rtwwwap_all_commission ){
							if( $rtwwwap_all_commission_type == 'percentage' ){
								$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
							}
							elseif( $rtwwwap_all_commission_type == 'fixed' ){
								$rtwwwap_prod_comm += $rtwwwap_all_commission;
							}
							$rtwwwap_aff_prod_details[] = array(
				    					'product_id' 		=> $rtwwwap_product_id,
				    					'product_price' 	=> $rtwwwap_product_price,
				    					'commission_fix' 	=> '',
					    				'commission_perc' 	=> '',
				    					'prod_commission' 	=> $rtwwwap_prod_comm
				    				);

			    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
			    		}
					}
				}
			}
			else
			{
				$rtwwwap_levels_settings 	= get_option( 'rtwwwap_levels_settings_opt' );
				$rtwwwap_user_level 		= get_user_meta( $rtwwwap_user_id, 'rtwwwap_affiliate_level', true );
				$rtwwwap_user_level 		= ( $rtwwwap_user_level ) ? $rtwwwap_user_level : '0';

				$rtwwwap_user_level_details = isset( $rtwwwap_levels_settings[ $rtwwwap_user_level ] ) ? $rtwwwap_levels_settings[ $rtwwwap_user_level ] : '';

				if( !empty( $rtwwwap_user_level_details ) ){
					$rtwwwap_level_comm_type 		= $rtwwwap_user_level_details[ 'level_commission_type' ];
					$rtwwwap_level_comm_amount 		= $rtwwwap_user_level_details[ 'level_comm_amount' ];
					$rtwwwap_level_criteria_type 	= $rtwwwap_user_level_details[ 'level_criteria_type' ];
					$rtwwwap_level_criteria_val 	= $rtwwwap_user_level_details[ 'level_criteria_val' ];

					foreach( $rtwwwap_order->get_items() as $rtwwwap_item_key => $rtwwwap_item_values )
					{
						$rtwwwap_prod_comm 		= '';
						$rtwwwap_product_id 	= $rtwwwap_item_values->get_product_id();
						$rtwwwap_product_price	= $rtwwwap_item_values->get_total();
						$rtwwwap_aff_prod_price [] = $rtwwwap_product_price; 

						if( $rtwwwap_commission_type == 0 )
						{
							if( $rtwwwap_level_comm_type == 0 ){
								$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_level_comm_amount ) / 100;
								$rtwwwap_aff_prod_details[] = array(
						    					'product_id' 		=> $rtwwwap_product_id,
						    					'product_price' 	=> $rtwwwap_product_price,
						    					'commission_fix' 	=> 'user',
						    					'commission_perc' 	=> $rtwwwap_level_comm_amount,
						    					'prod_commission' 	=> $rtwwwap_prod_comm
						    				);

					    		$rtwwwap_total_commission += $rtwwwap_prod_comm;
							}
							else{
								$rtwwwap_prod_comm = $rtwwwap_level_comm_amount;
								$rtwwwap_aff_prod_details[] = array(
						    					'product_id' 		=> $rtwwwap_product_id,
						    					'product_price' 	=> $rtwwwap_product_price,
						    					'commission_fix' 	=> 'user',
						    					'commission_perc' 	=> $rtwwwap_level_comm_amount,
						    					'prod_commission' 	=> $rtwwwap_prod_comm
						    				);

					    		$rtwwwap_total_commission += $rtwwwap_prod_comm;
							}
						}
					}
				}
			}

			if( isset( $rtwwwap_total_commission ) && $rtwwwap_total_commission !== '' && $rtwwwap_total_commission !== 0 ){
				$rtwwwap_capped 		= 0;
				$rtwwwap_current_year 	= date("Y");
				$rtwwwap_current_month 	= date("m");

				$rtwwwap_commission_settings	= get_option( 'rtwwwap_commission_settings_opt' );
				$rtwwwap_max_comm 				= isset( $rtwwwap_commission_settings[ 'max_commission' ] ) ? $rtwwwap_commission_settings[ 'max_commission' ] : '0';

				if( $rtwwwap_max_comm != 0 )
				{
					$rtwwwap_month_commission 	= $wpdb->get_var( $wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE YEAR(date)=%d AND MONTH(date)=%d AND `aff_id`=%d", $rtwwwap_current_year, $rtwwwap_current_month, $rtwwwap_user_id ) );
					$rtwwwap_month_commission 	= isset( $rtwwwap_month_commission ) ? $rtwwwap_month_commission : 0;

					if( $rtwwwap_month_commission < $rtwwwap_max_comm ){
						$rtwwwap_this_month_left = $rtwwwap_max_comm - $rtwwwap_month_commission;
						if( $rtwwwap_this_month_left < $rtwwwap_total_commission ){
							$rtwwwap_total_commission = $rtwwwap_this_month_left;
						}
						else{
							$rtwwwap_total_commission = $rtwwwap_total_commission;
						}
					}
					else{
						$rtwwwap_capped = 1;
					}
				}

				// inserting into DB
				if( !empty( $rtwwwap_aff_prod_details ) ){
					if( get_user_meta( $rtwwwap_user_id, 'rtwwwap_referral_mail', true ) == 'on' ){
						$rtwwwap_decimal_places = $rtwwwap_extra_features['decimal_places'].'f';
						$rtwwwap_to 			= get_user_by( 'id', $rtwwwap_user_id );
						$rtwwwap_to 			= esc_html( $rtwwwap_to->user_email );

						$all_emails = get_option('customize_email', false);

						$generate_commission = get_option('generate_commission','null');

						if(isset($all_emails['Email on Generating Commission']['subject'])){
							$rtwwwap_subject_text = $all_emails['Email on Generating Commission']['subject'];
							$rtwwwap_message_text = $all_emails['Email on Generating Commission']['content'];
						}

						$rtwwwap_subject 		= esc_html__( $rtwwwap_subject_text, 'rtwwwap-wp-wc-affiliate-program' );
						$rtwwwap_message 		= sprintf( '%s %s%01.'.$rtwwwap_decimal_places, esc_html__( $rtwwwap_message_text , 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_currency_sym, $rtwwwap_total_commission );
						$rtwwwap_from_name 		= esc_html( get_bloginfo( 'name' ) );
						$rtwwwap_from_email 	= esc_html( get_bloginfo( 'admin_email' ) );

						$rtwwwap_headers[] 		= 'Content-Type: text/html; charset=utf-8';
						$rtwwwap_headers[] 		= sprintf( '%s: %s <%s>', esc_html__( 'From', 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_from_name, $rtwwwap_from_email );

						// mail to affiliate
						if($generate_commission == "true"){
							wp_mail( $rtwwwap_to, $rtwwwap_subject, $rtwwwap_message, $rtwwwap_headers );
						}

						if( isset( $rtwwwap_extra_features[ 'mail_to_admin' ] ) && $rtwwwap_extra_features[ 'mail_to_admin' ] == 1 ){
							// mail to admin
							$rtwwwap_message = sprintf( '%s %s%01.'.$rtwwwap_decimal_places, esc_html__( 'Generated a new referral of amount', 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_currency_sym, $rtwwwap_total_commission );
							wp_mail( $rtwwwap_from_email, $rtwwwap_subject, $rtwwwap_message, $rtwwwap_headers );
						}
					}

					$rtwwwap_aff_prod_details = json_encode( $rtwwwap_aff_prod_details );
					$rtwwwap_device = ( wp_is_mobile() ) ? 'mobile' : 'desktop';

					$rtwwwap_locale = get_locale();
					setlocale( LC_NUMERIC, $rtwwwap_locale );

					$rtwwwap_updated = $wpdb->insert(
			            $wpdb->prefix.'rtwwwap_referrals',
			            array(
			                'aff_id'    			=> $rtwwwap_user_id,
			                'type'    				=> 0,
			                'order_id'    			=> esc_html( $rtwwwap_order_id ),
			                'date'    				=> date( 'Y-m-d H:i:s' ),
			                'status'    			=> 0,
			                'amount'    			=> $rtwwwap_total_commission,
			                'capped'    			=> esc_html( $rtwwwap_capped ),
			                'currency'    			=> $rtwwwap_currency,
			                'product_details'   	=> $rtwwwap_aff_prod_details,
			                'device'   				=> $rtwwwap_device
			            )
			        );
			        $rtwwwap_lastid = $wpdb->insert_id;

			        setlocale( LC_ALL, $rtwwwap_locale );

			        if( $rtwwwap_updated ){
			        	unset( $_COOKIE[ 'rtwwwap_referral' ] );
				        $rtwwwap_referral_noti = get_option( 'rtwwwap_referral_noti' )+1;
				        update_option( 'rtwwwap_referral_noti', $rtwwwap_referral_noti );
					}

					$rtwwwap_mlm = get_option( 'rtwwwap_mlm_opt' );
					if( isset( $rtwwwap_mlm[ 'activate' ] ) && $rtwwwap_mlm[ 'activate' ] == 1 )
					{
						$rtwwwap_child = isset( $rtwwwap_mlm[ 'child' ] ) ? $rtwwwap_mlm[ 'child' ] : 1;
						$rtwwwap_check_have_child = $this->rtwwwap_check_child_in_mlm( $rtwwwap_user_id, $rtwwwap_child );

						$rtwwwap_mlm_com_base = isset($rtwwwap_mlm['mlm_commission_base']) ? $rtwwwap_mlm['mlm_commission_base'] : 1;

						if($rtwwwap_mlm_com_base == 0)
						{
							$rtwwwap_total_commission = array_sum($rtwwwap_aff_prod_price);
						}

						if( $rtwwwap_check_have_child ){
							$this->rtwwwap_give_mlm_comm( $rtwwwap_user_id, $rtwwwap_lastid, $rtwwwap_total_commission, $rtwwwap_currency, $rtwwwap_currency_sym, $rtwwwap_device, $rtwwwap_mlm[ 'mlm_levels' ], $rtwwwap_child, $rtwwwap_order_id,"","");
						}
					}
				}
			}
		}
	}

	//unlimited comm for easy digital downloads

	function rtwwwap_unlimited_reff_comm_easy( $rtwwwap_order_id = 0, $rtwwwap_referrer_id = 0 )
	{
		$rtwwwap_verification_done = get_option( 'rtwwwap_verification_done', array() );
		$rtwwwap_verification_done_status = isset($rtwwwap_verification_done['status']) ? $rtwwwap_verification_done['status'] : false;
		$rtwwwap_verification_done_purchase = isset($rtwwwap_verification_done['purchase_code']) ? $rtwwwap_verification_done['purchase_code'] : false;
		if( empty( $rtwwwap_verification_done ) || $rtwwwap_verification_done_status == false || empty($rtwwwap_verification_done_purchase) )
		{
			return;
		}
		if( $rtwwwap_order_id && $rtwwwap_referrer_id ){
			global $wpdb;
			$rtwwwap_commission_settings = get_option( 'rtwwwap_commission_settings_opt' );
			$rtwwwap_referral 	= array( $rtwwwap_referrer_id );
			$rtwwwap_order 		= edd_get_payment( $rtwwwap_order_id );
			$rtwwwap_comm_base 	= isset( $rtwwwap_commission_settings[ 'comm_base' ] ) ? $rtwwwap_commission_settings[ 'comm_base' ] : '1';
			$rtwwwap_total_commission	= 0;
			$rtwwwap_aff_prod_details 	= array();
			$rtwwwap_user_id 			= esc_html( $rtwwwap_referral[ 0 ] );

			if( RTWWWAP_IS_WOO == 1 ){
				$rtwwwap_currency 		= get_woocommerce_currency();
				$rtwwwap_currency_sym 	= get_woocommerce_currency_symbol();
			}
			else{
				require_once( RTWWWAP_DIR.'includes/rtwaffiliatehelper.php' );

				$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );
				$rtwwwap_currency 		= isset( $rtwwwap_extra_features[ 'currency' ] ) ? $rtwwwap_extra_features[ 'currency' ] : 'USD';
				$rtwwwap_curr_obj 		= new RtwAffiliateHelper();
				$rtwwwap_currency_sym 	= $rtwwwap_curr_obj->rtwwwap_curr_symbol( $rtwwwap_currency );
			}

			$rtwwwap_commission_type 	= 0;
			$rtwwwap_shared 			= false;
			$rtwwwap_product_url 		= false;

			if( $rtwwwap_comm_base == 1 ){
				$rtwwwap_per_prod_mode 			= isset( $rtwwwap_commission_settings[ 'per_prod_mode' ] ) ? $rtwwwap_commission_settings[ 'per_prod_mode' ] : 0;
				$rtwwwap_all_commission 		= isset( $rtwwwap_commission_settings[ 'all_commission' ] ) ? $rtwwwap_commission_settings[ 'all_commission' ] : 0;
				$rtwwwap_all_commission_type 	= isset( $rtwwwap_commission_settings[ 'all_commission_type' ] ) ? $rtwwwap_commission_settings[ 'all_commission_type' ] : 'percentage';
				$rtwwwap_per_cat 				= isset( $rtwwwap_commission_settings[ 'per_cat' ] ) ? $rtwwwap_commission_settings[ 'per_cat' ] : array();

				
				foreach( $rtwwwap_order->cart_details as $rtwwwap_item_key => $rtwwwap_item_values )
					{
						$rtwwwap_prod_comm 		= '';
						$rtwwwap_product_id 	= $rtwwwap_item_values['ID'];
						$rtwwwap_product_price	= $rtwwwap_item_values['price'];					
						$rtwwwp_product_category_taxonomy = 'download_category';
					
					$rtwwwap_product_terms 	= get_the_terms( $rtwwwap_product_id, $rtwwwp_product_category_taxonomy  );
					$rtwwwap_product_cat_id = $rtwwwap_product_terms[0]->term_id;

					if( $rtwwwap_commission_type == 0 )
					{
					    if( $rtwwwap_per_prod_mode == 1 ){
							$rtwwwap_prod_per_comm = get_post_meta( $rtwwwap_product_id, 'rtwwwap_percentage_commission_box', true );

							if( $rtwwwap_prod_per_comm > 0 ){
								$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_prod_per_comm ) / 100;
								$rtwwwap_aff_prod_details[] = array(
						    					'product_id' 		=> $rtwwwap_product_id,
						    					'product_price' 	=> $rtwwwap_product_price,
						    					'commission_fix' 	=> '',
						    					'commission_perc' 	=> $rtwwwap_prod_per_comm,
						    					'prod_commission' 	=> $rtwwwap_prod_comm
						    				);

					    		$rtwwwap_total_commission += $rtwwwap_prod_comm;
							}
							elseif( $rtwwwap_prod_per_comm === '0' ){
								// no commission needs to be generated for this product
							}
							else{
								if( !empty( $rtwwwap_per_cat ) ){
									$rtwwwap_cat_per_comm = 0;
									$rtwwwap_cat_fix_comm = 0;
									$rtwwwap_flag = false;
									foreach( $rtwwwap_per_cat as $rtwwwap_key => $rtwwwap_value ){
										if( in_array( $rtwwwap_product_cat_id, $rtwwwap_value[ 'ids' ] ) ){
											$rtwwwap_cat_per_comm = $rtwwwap_value[ 'cat_percentage_commission' ];
											$rtwwwap_cat_fix_comm = $rtwwwap_value[ 'cat_fixed_commission' ];
											$rtwwwap_flag = true;

											break;
										}
									}
									if( $rtwwwap_flag ){
										if( $rtwwwap_cat_per_comm > 0 ){
											$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_cat_per_comm ) / 100;
										}
										if( $rtwwwap_cat_fix_comm > 0 ){
											$rtwwwap_prod_comm += $rtwwwap_cat_fix_comm;
										}

										if( $rtwwwap_prod_comm != '' ){
											$rtwwwap_aff_prod_details[] = array(
								    					'product_id' 		=> $rtwwwap_product_id,
								    					'product_price' 	=> $rtwwwap_product_price,
								    					'commission_fix' 	=> $rtwwwap_cat_fix_comm,
								    					'commission_perc' 	=> $rtwwwap_cat_per_comm,
								    					'prod_commission' 	=> $rtwwwap_prod_comm
								    				);

							    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
							    		}
									}
									else{
										if( $rtwwwap_all_commission ){
											if( $rtwwwap_all_commission_type == 'percentage' ){
												$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
											}
											elseif( $rtwwwap_all_commission_type == 'fixed' ){
												$rtwwwap_prod_comm += $rtwwwap_all_commission;
											}
											$rtwwwap_aff_prod_details[] = array(
								    					'product_id' 		=> $rtwwwap_product_id,
								    					'product_price' 	=> $rtwwwap_product_price,
								    					'commission_fix' 	=> '',
								    					'commission_perc' 	=> '',
								    					'prod_commission' 	=> $rtwwwap_prod_comm
								    				);

							    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
							    		}
									}
								}
								else{
									if( $rtwwwap_all_commission ){
										if( $rtwwwap_all_commission_type == 'percentage' ){
											$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
										}
										elseif( $rtwwwap_all_commission_type == 'fixed' ){
											$rtwwwap_prod_comm += $rtwwwap_all_commission;
										}
										$rtwwwap_aff_prod_details[] = array(
							    					'product_id' 		=> $rtwwwap_product_id,
							    					'product_price' 	=> $rtwwwap_product_price,
							    					'commission_fix' 	=> '',
								    				'commission_perc' 	=> '',
							    					'prod_commission' 	=> $rtwwwap_prod_comm
							    				);

						    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
						    		}
								}
							}
						}
						elseif( $rtwwwap_per_prod_mode == 2 ){
							$rtwwwap_prod_fix_comm = get_post_meta( $rtwwwap_product_id, 'rtwwwap_fixed_commission_box', true );

							if( $rtwwwap_prod_fix_comm > 0 ){
								$rtwwwap_prod_comm = $rtwwwap_prod_fix_comm;
								$rtwwwap_aff_prod_details[] = array(
						    					'product_id' 		=> $rtwwwap_product_id,
						    					'product_price' 	=> $rtwwwap_product_price,
						    					'commission_fix' 	=> $rtwwwap_prod_fix_comm,
						    					'commission_perc' 	=> '',
						    					'prod_commission' 	=> $rtwwwap_prod_comm
						    				);

					    		$rtwwwap_total_commission += $rtwwwap_prod_comm;
							}
							elseif( $rtwwwap_prod_fix_comm === '0' ){
								// no commission needs to be generated for this product
							}
							else{
								if( !empty( $rtwwwap_per_cat ) ){
									$rtwwwap_cat_per_comm = 0;
									$rtwwwap_cat_fix_comm = 0;
									$rtwwwap_flag = false;
									foreach( $rtwwwap_per_cat as $rtwwwap_key => $rtwwwap_value ){
										if( in_array( $rtwwwap_product_cat_id, $rtwwwap_value[ 'ids' ] ) ){
											$rtwwwap_cat_per_comm = $rtwwwap_value[ 'cat_percentage_commission' ];
											$rtwwwap_cat_fix_comm = $rtwwwap_value[ 'cat_fixed_commission' ];
											$rtwwwap_flag = true;

											break;
										}
									}
									if( $rtwwwap_flag ){
										if( $rtwwwap_cat_per_comm > 0 ){
											$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_cat_per_comm ) / 100;
										}
										if( $rtwwwap_cat_fix_comm > 0 ){
											$rtwwwap_prod_comm += $rtwwwap_cat_fix_comm;
										}

										if( $rtwwwap_prod_comm != '' ){
											$rtwwwap_aff_prod_details[] = array(
								    					'product_id' 		=> $rtwwwap_product_id,
								    					'product_price' 	=> $rtwwwap_product_price,
								    					'commission_fix' 	=> $rtwwwap_cat_fix_comm,
								    					'commission_perc' 	=> $rtwwwap_cat_per_comm,
								    					'prod_commission' 	=> $rtwwwap_prod_comm
								    				);

							    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
							    		}
									}
									else{
										if( $rtwwwap_all_commission ){
											if( $rtwwwap_all_commission_type == 'percentage' ){
												$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
											}
											elseif( $rtwwwap_all_commission_type == 'fixed' ){
												$rtwwwap_prod_comm += $rtwwwap_all_commission;
											}
											$rtwwwap_aff_prod_details[] = array(
								    					'product_id' 		=> $rtwwwap_product_id,
								    					'product_price' 	=> $rtwwwap_product_price,
								    					'commission_fix' 	=> '',
								    					'commission_perc' 	=> '',
								    					'prod_commission' 	=> $rtwwwap_prod_comm
								    				);

							    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
							    		}
									}
								}
								else{
									if( $rtwwwap_all_commission ){
										if( $rtwwwap_all_commission_type == 'percentage' ){
											$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
										}
										elseif( $rtwwwap_all_commission_type == 'fixed' ){
											$rtwwwap_prod_comm += $rtwwwap_all_commission;
										}
										$rtwwwap_aff_prod_details[] = array(
							    					'product_id' 		=> $rtwwwap_product_id,
							    					'product_price' 	=> $rtwwwap_product_price,
							    					'commission_fix' 	=> '',
								    				'commission_perc' 	=> '',
							    					'prod_commission' 	=> $rtwwwap_prod_comm
							    				);

						    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
						    		}
								}
							}
						}
						elseif( $rtwwwap_per_prod_mode == 3 ){
							$rtwwwap_prod_per_comm = get_post_meta( $rtwwwap_product_id, 'rtwwwap_percentage_commission_box', true );
							$rtwwwap_prod_fix_comm = get_post_meta( $rtwwwap_product_id, 'rtwwwap_fixed_commission_box', true );

							if( $rtwwwap_prod_per_comm > 0 ){
								$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_prod_per_comm ) / 100;
							}

							if( $rtwwwap_prod_fix_comm > 0 ){
								$rtwwwap_prod_comm += $rtwwwap_prod_fix_comm;
							}

							if( $rtwwwap_prod_comm === '' ){
								if( $rtwwwap_prod_per_comm !== '0' && $rtwwwap_prod_fix_comm !== '0' ){
									if( !empty( $rtwwwap_per_cat ) ){
										$rtwwwap_cat_per_comm = 0;
										$rtwwwap_cat_fix_comm = 0;
										$rtwwwap_flag = false;
										foreach( $rtwwwap_per_cat as $rtwwwap_key => $rtwwwap_value ){
											if( in_array( $rtwwwap_product_cat_id, $rtwwwap_value[ 'ids' ] ) ){
												$rtwwwap_cat_per_comm = $rtwwwap_value[ 'cat_percentage_commission' ];
												$rtwwwap_cat_fix_comm = $rtwwwap_value[ 'cat_fixed_commission' ];
												$rtwwwap_flag = true;

												break;
											}
										}
										if( $rtwwwap_flag ){
											if( $rtwwwap_cat_per_comm > 0 ){
												$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_cat_per_comm ) / 100;
											}
											if( $rtwwwap_cat_fix_comm > 0 ){
												$rtwwwap_prod_comm += $rtwwwap_cat_fix_comm;
											}

											if( $rtwwwap_prod_comm != '' ){
												$rtwwwap_aff_prod_details[] = array(
									    					'product_id' 		=> $rtwwwap_product_id,
									    					'product_price' 	=> $rtwwwap_product_price,
									    					'commission_fix' 	=> $rtwwwap_cat_fix_comm,
									    					'commission_perc' 	=> $rtwwwap_cat_per_comm,
									    					'prod_commission' 	=> $rtwwwap_prod_comm
									    				);

								    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
								    		}
										}
										else{
											if( $rtwwwap_all_commission ){
												if( $rtwwwap_all_commission_type == 'percentage' ){
													$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
												}
												elseif( $rtwwwap_all_commission_type == 'fixed' ){
													$rtwwwap_prod_comm += $rtwwwap_all_commission;
												}
												$rtwwwap_aff_prod_details[] = array(
									    					'product_id' 		=> $rtwwwap_product_id,
									    					'product_price' 	=> $rtwwwap_product_price,
									    					'commission_fix' 	=> '',
									    					'commission_perc' 	=> '',
									    					'prod_commission' 	=> $rtwwwap_prod_comm
									    				);

								    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
								    		}
										}
									}
									else{
										if( $rtwwwap_all_commission ){
											if( $rtwwwap_all_commission_type == 'percentage' ){
												$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
											}
											elseif( $rtwwwap_all_commission_type == 'fixed' ){
												$rtwwwap_prod_comm += $rtwwwap_all_commission;
											}
											$rtwwwap_aff_prod_details[] = array(
								    					'product_id' 		=> $rtwwwap_product_id,
								    					'product_price' 	=> $rtwwwap_product_price,
								    					'commission_fix' 	=> '',
									    				'commission_perc' 	=> '',
								    					'prod_commission' 	=> $rtwwwap_prod_comm
								    				);

							    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
							    		}
									}
								}
							}
							else{
								$rtwwwap_aff_prod_details[] = array(
					    					'product_id' 		=> $rtwwwap_product_id,
					    					'product_price' 	=> $rtwwwap_product_price,
					    					'commission_fix' 	=> $rtwwwap_prod_fix_comm,
						    				'commission_perc' 	=> $rtwwwap_prod_per_comm,
					    					'prod_commission' 	=> $rtwwwap_prod_comm
					    				);

				    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
							}
						}
						elseif( $rtwwwap_all_commission ){
							if( $rtwwwap_all_commission_type == 'percentage' ){
								$rtwwwap_prod_comm += ( $rtwwwap_product_price * $rtwwwap_all_commission ) / 100;
							}
							elseif( $rtwwwap_all_commission_type == 'fixed' ){
								$rtwwwap_prod_comm += $rtwwwap_all_commission;
							}
							$rtwwwap_aff_prod_details[] = array(
				    					'product_id' 		=> $rtwwwap_product_id,
				    					'product_price' 	=> $rtwwwap_product_price,
				    					'commission_fix' 	=> '',
					    				'commission_perc' 	=> '',
				    					'prod_commission' 	=> $rtwwwap_prod_comm
				    				);

			    			$rtwwwap_total_commission += $rtwwwap_prod_comm;
			    		}
					}
				}
			}
			else
			{
				$rtwwwap_levels_settings 	= get_option( 'rtwwwap_levels_settings_opt' );
				$rtwwwap_user_level 		= get_user_meta( $rtwwwap_user_id, 'rtwwwap_affiliate_level', true );
				$rtwwwap_user_level 		= ( $rtwwwap_user_level ) ? $rtwwwap_user_level : '0';

				$rtwwwap_user_level_details = isset( $rtwwwap_levels_settings[ $rtwwwap_user_level ] ) ? $rtwwwap_levels_settings[ $rtwwwap_user_level ] : '';

				if( !empty( $rtwwwap_user_level_details ) ){
					$rtwwwap_level_comm_type 		= $rtwwwap_user_level_details[ 'level_commission_type' ];
					$rtwwwap_level_comm_amount 		= $rtwwwap_user_level_details[ 'level_comm_amount' ];
					$rtwwwap_level_criteria_type 	= $rtwwwap_user_level_details[ 'level_criteria_type' ];
					$rtwwwap_level_criteria_val 	= $rtwwwap_user_level_details[ 'level_criteria_val' ];

					
					foreach( $rtwwwap_order->cart_details as $rtwwwap_item_key => $rtwwwap_item_values )
					{
						$rtwwwap_prod_comm 		= '';
						$rtwwwap_product_id 	= $rtwwwap_item_values['ID'];
						$rtwwwap_product_price	= $rtwwwap_item_values['price'];					
						$rtwwwp_product_category_taxonomy = 'download_category';
						if( $rtwwwap_commission_type == 0 )
						{
							if( $rtwwwap_level_comm_type == 0 ){
								$rtwwwap_prod_comm = ( $rtwwwap_product_price * $rtwwwap_level_comm_amount ) / 100;
								$rtwwwap_aff_prod_details[] = array(
						    					'product_id' 		=> $rtwwwap_product_id,
						    					'product_price' 	=> $rtwwwap_product_price,
						    					'commission_fix' 	=> 'user',
						    					'commission_perc' 	=> $rtwwwap_level_comm_amount,
						    					'prod_commission' 	=> $rtwwwap_prod_comm
						    				);

					    		$rtwwwap_total_commission += $rtwwwap_prod_comm;
							}
							else{
								$rtwwwap_prod_comm = $rtwwwap_level_comm_amount;
								$rtwwwap_aff_prod_details[] = array(
						    					'product_id' 		=> $rtwwwap_product_id,
						    					'product_price' 	=> $rtwwwap_product_price,
						    					'commission_fix' 	=> 'user',
						    					'commission_perc' 	=> $rtwwwap_level_comm_amount,
						    					'prod_commission' 	=> $rtwwwap_prod_comm
						    				);

					    		$rtwwwap_total_commission += $rtwwwap_prod_comm;
							}
						}
					}
				}
			}

			if( isset( $rtwwwap_total_commission ) && $rtwwwap_total_commission !== '' && $rtwwwap_total_commission !== 0 ){
				$rtwwwap_capped 		= 0;
				$rtwwwap_current_year 	= date("Y");
				$rtwwwap_current_month 	= date("m");

				$rtwwwap_commission_settings	= get_option( 'rtwwwap_commission_settings_opt' );
				$rtwwwap_max_comm 				= isset( $rtwwwap_commission_settings[ 'max_commission' ] ) ? $rtwwwap_commission_settings[ 'max_commission' ] : '0';

				if( $rtwwwap_max_comm != 0 )
				{
					$rtwwwap_month_commission 	= $wpdb->get_var( $wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE YEAR(date)=%d AND MONTH(date)=%d AND `aff_id`=%d", $rtwwwap_current_year, $rtwwwap_current_month, $rtwwwap_user_id ) );
					$rtwwwap_month_commission 	= isset( $rtwwwap_month_commission ) ? $rtwwwap_month_commission : 0;

					if( $rtwwwap_month_commission < $rtwwwap_max_comm ){
						$rtwwwap_this_month_left = $rtwwwap_max_comm - $rtwwwap_month_commission;
						if( $rtwwwap_this_month_left < $rtwwwap_total_commission ){
							$rtwwwap_total_commission = $rtwwwap_this_month_left;
						}
						else{
							$rtwwwap_total_commission = $rtwwwap_total_commission;
						}
					}
					else{
						$rtwwwap_capped = 1;
					}
				}

				// inserting into DB
				if( !empty( $rtwwwap_aff_prod_details ) ){
					if( get_user_meta( $rtwwwap_user_id, 'rtwwwap_referral_mail', true ) == 'on' ){
						$rtwwwap_decimal_places = $rtwwwap_extra_features['decimal_places'].'f';
						$rtwwwap_to 			= get_user_by( 'id', $rtwwwap_user_id );
						$rtwwwap_to 			= esc_html( $rtwwwap_to->user_email );

						$all_emails = get_option('customize_email', false);

						$generate_commission = get_option('generate_commission','null');

						if(isset($all_emails['Email on Generating Commission']['subject'])){
							$rtwwwap_subject_text = $all_emails['Email on Generating Commission']['subject'];
							$rtwwwap_message_text = $all_emails['Email on Generating Commission']['content'];
						}

						$rtwwwap_subject 		= esc_html__( $rtwwwap_subject_text, 'rtwwwap-wp-wc-affiliate-program' );
						$rtwwwap_message 		= sprintf( '%s %s%01.'.$rtwwwap_decimal_places, esc_html__( $rtwwwap_message_text , 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_currency_sym, $rtwwwap_total_commission );
						$rtwwwap_from_name 		= esc_html( get_bloginfo( 'name' ) );
						$rtwwwap_from_email 	= esc_html( get_bloginfo( 'admin_email' ) );

						$rtwwwap_headers[] 		= 'Content-Type: text/html; charset=utf-8';
						$rtwwwap_headers[] 		= sprintf( '%s: %s <%s>', esc_html__( 'From', 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_from_name, $rtwwwap_from_email );

						// mail to affiliate
						if($generate_commission == "true"){
							wp_mail( $rtwwwap_to, $rtwwwap_subject, $rtwwwap_message, $rtwwwap_headers );
						}

						if( isset( $rtwwwap_extra_features[ 'mail_to_admin' ] ) && $rtwwwap_extra_features[ 'mail_to_admin' ] == 1 ){
							// mail to admin
							$rtwwwap_message = sprintf( '%s %s%01.'.$rtwwwap_decimal_places, esc_html__( 'Generated a new referral of amount', 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_currency_sym, $rtwwwap_total_commission );
							wp_mail( $rtwwwap_from_email, $rtwwwap_subject, $rtwwwap_message, $rtwwwap_headers );
						}
					}

					$rtwwwap_aff_prod_details = json_encode( $rtwwwap_aff_prod_details );
					$rtwwwap_device = ( wp_is_mobile() ) ? 'mobile' : 'desktop';

					$rtwwwap_locale = get_locale();
					setlocale( LC_NUMERIC, $rtwwwap_locale );

					$rtwwwap_updated = $wpdb->insert(
			            $wpdb->prefix.'rtwwwap_referrals',
			            array(
			                'aff_id'    			=> $rtwwwap_user_id,
			                'type'    				=> 0,
			                'order_id'    			=> esc_html( $rtwwwap_order_id ),
			                'date'    				=> date( 'Y-m-d H:i:s' ),
			                'status'    			=> 0,
			                'amount'    			=> $rtwwwap_total_commission,
			                'capped'    			=> esc_html( $rtwwwap_capped ),
			                'currency'    			=> $rtwwwap_currency,
			                'product_details'   	=> $rtwwwap_aff_prod_details,
			                'device'   				=> $rtwwwap_device
			            )
			        );
			        $rtwwwap_lastid = $wpdb->insert_id;

			        setlocale( LC_ALL, $rtwwwap_locale );

			        if( $rtwwwap_updated ){
			        	unset( $_COOKIE[ 'rtwwwap_referral' ] );
				        $rtwwwap_referral_noti = get_option( 'rtwwwap_referral_noti' )+1;
				        update_option( 'rtwwwap_referral_noti', $rtwwwap_referral_noti );
					}

					$rtwwwap_mlm = get_option( 'rtwwwap_mlm_opt' );
					if( isset( $rtwwwap_mlm[ 'activate' ] ) && $rtwwwap_mlm[ 'activate' ] == 1 )
					{
						$rtwwwap_child = isset( $rtwwwap_mlm[ 'child' ] ) ? $rtwwwap_mlm[ 'child' ] : 1;
						$rtwwwap_check_have_child = $this->rtwwwap_check_child_in_mlm( $rtwwwap_user_id, $rtwwwap_child );

						if( $rtwwwap_check_have_child ){
							$this->rtwwwap_give_mlm_comm( $rtwwwap_user_id, $rtwwwap_lastid, $rtwwwap_total_commission, $rtwwwap_currency, $rtwwwap_currency_sym, $rtwwwap_device, $rtwwwap_mlm[ 'mlm_levels' ], $rtwwwap_child, $rtwwwap_order_id, "","");
						}
					}
				}
			}
		}
	}

	

	function rtwwwap_cart_loaded_from_session($cart)
	{

		$rtwwwap_commission_settings 	= get_option( 'rtwwwap_commission_settings_opt' );
		//lifetime
		$rtwwwap_unlimit_comm = isset( $rtwwwap_commission_settings[ 'unlimit_comm' ] ) ? $rtwwwap_commission_settings[ 'unlimit_comm' ] : '0';
		if( isset( $_COOKIE[ 'rtwwwap_referral' ] ) || $rtwwwap_unlimit_comm == 1 )
		{
			global $wpdb;
			$rtwwwap_referrer_id = 0;
			$rtwwwap_current_user_id = get_current_user_id();

			if( $rtwwwap_current_user_id ){
				$rtwwwap_referrer_id = get_user_meta( $rtwwwap_current_user_id, 'rtwwwap_lifetime_user_id', true );
			}
			if( $rtwwwap_referrer_id || isset( $_COOKIE[ 'rtwwwap_referral' ] ) )
			{
				global $woocommerce;
				global $wpdb;
				$rtwwwap_sorted_cart = array();
				if ( sizeof( $cart->cart_contents ) > 0 ) {
					foreach ( $cart->cart_contents as $cart_item_key => &$values ) {
						if ( $values === null ) {
							continue;
						}

						if ( isset( $cart->cart_contents[ $cart_item_key ]['discounts'] ) ) {
							unset( $cart->cart_contents[ $cart_item_key ]['discounts'] );
						}
						$rtwwwap_sorted_cart[ $cart_item_key ] = &$values;
					}
				}

				if ( empty( $rtwwwap_sorted_cart ) ) {
					return;
				}
				$rtwwwap_verification_done = get_option( 'rtwwwap_verification_done', array() );
				$rtwwwap_verification_done_status = isset($rtwwwap_verification_done['status']) ? $rtwwwap_verification_done['status'] : false;
				$rtwwwap_verification_done_purchase = isset($rtwwwap_verification_done['purchase_code']) ? $rtwwwap_verification_done['purchase_code'] : false;
				if( empty( $rtwwwap_verification_done ) || $rtwwwap_verification_done_status == false || empty($rtwwwap_verification_done_purchase) )
				{
					return;
				}
				$rtwwwap_temp_cart = $rtwwwap_sorted_cart;
				global $woocommerce;
				$rtwwwap_cart_prod_count = $woocommerce->cart->cart_contents;
				$rtwwwap_prod_count = 0;
				if( is_array($rtwwwap_cart_prod_count) && !empty($rtwwwap_cart_prod_count) )
				{
					foreach ($rtwwwap_cart_prod_count as $key => $value) {
						$rtwwwap_prod_count += $value['quantity'];
					}
				}

				foreach ( $rtwwwap_temp_cart as $rtwwwap_cart => $rtwwwap_value ) {
					$rtwwwap_temp_cart[ $rtwwwap_cart ]                       = $rtwwwap_value;
					$rtwwwap_temp_cart[ $rtwwwap_cart ]['available_quantity'] = $rtwwwap_value['quantity'];
				}
				$set_id = 0;
				foreach ( $rtwwwap_temp_cart as $cart_item_key => $cart_item )
				{
					if ( ! $this->rtwwdpd_is_cumulative( $cart_item, $cart_item_key ) )
					{
						if ( $this->rtwwdpd_is_item_discounted( $cart_item, $cart_item_key ) ) {
							continue;
						}
					}

					$rtwwdpd_discounted = isset( WC()->cart->cart_contents[ $cart_item_key ]['discounts'] );

					if ($rtwwdpd_discounted){
						$rtwwdpd_d = WC()->cart->cart_contents[ $cart_item_key ]['discounts'];
						if (in_array('rtwwwap_referral_discount', $rtwwdpd_d['by'])) {
							continue;
						}
					}
					$rtwwdpd_original_price = $this->rtw_get_price_to_discount( $cart_item, $cart_item_key, true );

					if ( $rtwwdpd_original_price )
					{
						
							$comm_type = get_post_meta($cart_item['product_id'], '_rtwwwap_cust_comm_type', true);
							$comm_value = (float)get_post_meta($cart_item['product_id'], '_rtwwwap_cust_comm_value', true);
							if($comm_type == 'percentage')
							{
								$rtwwdpd_amount = $comm_value / 100;
								$rtwwdpd_dscnted_val = ( floatval( $rtwwdpd_amount ) * $rtwwdpd_original_price );
								$rtwwdpd_price_adjusted = ( floatval( $rtwwdpd_original_price ) - $rtwwdpd_dscnted_val );
								if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
									$this->rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'rtwwwap_referral_discount', $set_id );
									$set_id++;
									break;
								}
							}
							else if($comm_type == 'fixed')
							{
								$rtwwdpd_amount = floatval( $comm_value / $rtwwwap_prod_count );
								$rtwwdpd_price_adjusted = floatval( $rtwwdpd_original_price - $rtwwdpd_amount );
								if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
									$this->rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'rtwwwap_referral_discount', $set_id );
									$set_id++;
									break;
								}
							}
						
					}
				}
			}
		}
	}


	/**
	 * Function to get product price on which discount is applied.
	 *
	 * @since    1.0.0
	 */
	function rtw_get_price_to_discount( $rtwwdpd_cart_item, $rtwwdpd_cart_item_key, $rtw_stack_rules = false, $rtwwdpd_already_discounted ='rtwwwap_referral_discount' ) {
		global $woocommerce;
		$rtwwdpd_setting_pri = get_option('rtwwdpd_setting_priority');
		$rtwwdpd_result = false;
		do_action( 'rtwwdpd_memberships_discounts_disable_price_adjustments' );

		$rtwwdpd_filter_cart_item = $rtwwdpd_cart_item;
		if ( isset( WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ] ) ) {
			$rtwwdpd_filter_cart_item = WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ];

			if ( isset( WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ]['discounts'] ) ) {
				if ( $this->rtwwdpd_is_cumulative( $rtwwdpd_cart_item, $rtwwdpd_cart_item_key, $rtwwdpd_already_discounted ) || $rtw_stack_rules ) {
					$rtwwdpd_result = WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ]['discounts']['price_adjusted'];
				} else {
					$rtwwdpd_result = WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ]['discounts']['price_base'];
				}
			} else {
				if( isset( $rtwwdpd_setting_pri['rtw_dscnt_on'] ) && $rtwwdpd_setting_pri['rtw_dscnt_on'] == 'rtw_sale_price')
				{
					if ( apply_filters( 'rtwwdpd_dynamic_pricing_get_use_sale_price', true, $rtwwdpd_filter_cart_item['data'] ) ) {
						$rtwwdpd_result = WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ]['data']->get_price('edit');
					}
					else {
						$rtwwdpd_result = WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ]['data']->get_regular_price('edit');
					}
				}
				else{
					$rtwwdpd_result = WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ]['data']->get_regular_price('edit');
				}
			}
		}

		return $rtwwdpd_result;
	}

	/**
	 * Function to check if a product is discounted.
	 *
	 * @since    1.0.0
	 */
	function rtwwdpd_is_item_discounted( $rtwwdpd_cart_item, $rtwwdpd_cart_item_key ) {
		global $woocommerce;

		return isset( WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ]['discounts'] );
	}

	/**
	 * Function to check if a product is already discounted by the same rule.
	 *
	 * @since    1.0.0
	 */
	function rtwwdpd_is_cumulative( $rtwwdpd_cart_item, $rtwwdpd_cart_item_key, $rtwwdpd_default = true, $rtwwdpd_already_discounted = 'rtwwwap_referral_discount' ) {
		//Check to make sure the item has not already been discounted by this module.  This could happen if update_totals is called more than once in the cart.
		$rtwwdpd_cart = WC()->cart->get_cart();

		if ( isset( $rtwwdpd_cart ) && is_array( $rtwwdpd_cart ) && isset( $rtwwdpd_cart[ $rtwwdpd_cart_item_key ]['discounts'] ) && in_array( $rtwwdpd_already_discounted, WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ]['discounts']['by'] ) ) {

			return false;
		} else {
			return apply_filters( 'rtwwdpd_is_cumulative', $rtwwdpd_default, $rtwwdpd_already_discounted, $rtwwdpd_cart_item, $rtwwdpd_cart_item_key );
		}
	}



	function rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_adjusted_price, $module, $set_id ) {
		
		//Allow extensions to stop processing of applying the discount.  Added for subscriptions signup fee compatibility
		if ( $rtwwdpd_adjusted_price === false ) {
			return;
		}

		if ( isset( WC()->cart->cart_contents[ $cart_item_key ] ) && ! empty( WC()->cart->cart_contents[ $cart_item_key ] ) ) {


			$_product = WC()->cart->cart_contents[ $cart_item_key ]['data'];

			if ( apply_filters( 'rtwwdpd_dynamic_pricing_get_use_sale_price', true, $_product ) ) {
				$rtwwdpd_display_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? wc_get_price_excluding_tax( $_product ) : wc_get_price_including_tax( $_product );
			} else {
				$rtwwdpd_display_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? wc_get_price_excluding_tax( $_product, array( 'price' => $rtwwdpd_original_price ) ) : wc_get_price_including_tax( $_product, array( 'price' => $rtwwdpd_original_price ) );
			}
			if( isset( $rtwwdpd_setting_pri['rtw_dscnt_on'] ) && $rtwwdpd_setting_pri['rtw_dscnt_on'] == 'rtw_sale_price')
			{
				$rtwwdpd_display_price = $_product->get_price();
			}
			else{
				$rtwwdpd_display_price = $_product->get_regular_price();
			}

			WC()->cart->cart_contents[ $cart_item_key ]['data']->set_price( $rtwwdpd_adjusted_price );
			
			if ( $_product->get_type() == 'composite' ) {
				WC()->cart->cart_contents[ $cart_item_key ]['data']->base_price = $rtwwdpd_adjusted_price;
			}

			if ( ! isset( WC()->cart->cart_contents[ $cart_item_key ]['discounts'] ) ) {

				$rtwwdpd_discount_data                                           = array(
					'by'                => array( $module ),
					'set_id'            => $set_id,
					'price_base'        => $rtwwdpd_original_price,
					'display_price'     => $rtwwdpd_display_price,
					'price_adjusted'    => $rtwwdpd_adjusted_price,
					'applied_discounts' => array(
						array(
							'by'             => $module,
							'set_id'         => $set_id,
							'price_base'     => $rtwwdpd_original_price,
							'price_adjusted' => $rtwwdpd_adjusted_price
						)
					)
				);
				WC()->cart->cart_contents[ $cart_item_key ]['discounts'] = $rtwwdpd_discount_data;
			} else {

				$rtwwdpd_existing = WC()->cart->cart_contents[ $cart_item_key ]['discounts'];

				$rtwwdpd_discount_data = array(
					'by'             => $rtwwdpd_existing['by'],
					'set_id'         => $set_id,
					'price_base'     => $rtwwdpd_original_price,
					'display_price'  => $rtwwdpd_existing['display_price'],
					'price_adjusted' => $rtwwdpd_adjusted_price
				);

				WC()->cart->cart_contents[ $cart_item_key ]['discounts'] = $rtwwdpd_discount_data;

				$history = array(
					'by'             => $rtwwdpd_existing['by'],
					'set_id'         => $rtwwdpd_existing['set_id'],
					'price_base'     => $rtwwdpd_existing['price_base'],
					'price_adjusted' => $rtwwdpd_existing['price_adjusted']
				);
				array_push( WC()->cart->cart_contents[ $cart_item_key ]['discounts']['by'], $module );
				WC()->cart->cart_contents[ $cart_item_key ]['discounts']['applied_discounts'][] = $history;
			}
		}
		
	}


	// Change sale price html
	function rtwwwap_on_display_cart_item_price_html($rtwwdpd_html, $rtwwdpd_cart_item, $rtwwdpd_cart_item_key)
	{
		if ( $this->rtwwdpd_is_item_discounted( $rtwwdpd_cart_item, $rtwwdpd_cart_item_key ) ) {
			$_product = $rtwwdpd_cart_item['data'];

			if ( function_exists( 'get_product' ) ) {
				if (isset($rtwwdpd_cart_item['is_deposit']) && $rtwwdpd_cart_item['is_deposit']) {
					$rtwwdpd_price_to_calculate = isset( $rtwwdpd_cart_item['discounts'] ) ? $rtwwdpd_cart_item['discounts']['price_adjusted'] : $rtwwdpd_cart_item['data']->get_price();
				} else {
					$rtwwdpd_price_to_calculate = $rtwwdpd_cart_item['data']->get_price();
				}

				$rtwwdpd_price_adjusted = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? wc_get_price_excluding_tax($_product, array('price' => $rtwwdpd_price_to_calculate, 'qty' => 1)) : wc_get_price_including_tax($_product, array('price' => $rtwwdpd_price_to_calculate, 'qty' => 1));
				$rtwwdpd_price_base = $rtwwdpd_cart_item['discounts']['display_price'];

			} else {
				if ( get_option( 'rtwwdpd_display_cart_prices_excluding_tax' ) == 'yes' ) :
					$rtwwdpd_price_adjusted = wc_get_price_excluding_tax($rtwwdpd_cart_item['data']);
					$rtwwdpd_price_base = $rtwwdpd_cart_item['discounts']['display_price'];
				else :
					$rtwwdpd_price_adjusted = $rtwwdpd_cart_item['data']->get_price();
					$rtwwdpd_price_base = $rtwwdpd_cart_item['discounts']['display_price'];
				endif;
			}

			if($rtwwdpd_price_adjusted != $rtwwdpd_price_base){

				if ( !empty( $rtwwdpd_price_adjusted ) || $rtwwdpd_price_adjusted === 0 || $rtwwdpd_price_adjusted === 0.00 ) {
					if ( apply_filters( 'rtwwdpd_use_discount_format', true ) ) {
						$rtwwdpd_html = '<del>' . wc_price( $rtwwdpd_price_base ) . '</del><ins> ' . wc_price( $rtwwdpd_price_adjusted ) . '</ins>';
					} else {
						$rtwwdpd_html = '<span class="amount">' . wc_price( $rtwwdpd_price_adjusted ) . '</span>';
					}
				}
			}
		}
		return $rtwwdpd_html;
	}

// Change sale price html for easy digital downloads 

	function rtwwwap_on_display_cart_item_price_html_edd($rtwwwap_price, $rtwwwap_prod_id)
	{
	
		$rtwwwap_commission_settings 	= get_option( 'rtwwwap_commission_settings_opt' );
		//lifetime
		$rtwwwap_unlimit_comm = isset( $rtwwwap_commission_settings[ 'unlimit_comm' ] ) ? $rtwwwap_commission_settings[ 'unlimit_comm' ] : '0';
	

		if( isset( $_COOKIE[ 'rtwwwap_referral' ] ) || $rtwwwap_unlimit_comm == 1 )
		{
			global $wpdb;
			$rtwwwap_referrer_id = 0;
			$rtwwwap_current_user_id = get_current_user_id();

			if( $rtwwwap_current_user_id ){
				$rtwwwap_referrer_id = get_user_meta( $rtwwwap_current_user_id, 'rtwwwap_lifetime_user_id', true );
			}
			if( $rtwwwap_referrer_id || isset( $_COOKIE[ 'rtwwwap_referral' ] ) )
			{
			
				$rtwwwap_verification_done = get_option( 'rtwwwap_verification_done', array() );
				$rtwwwap_verification_done_status = isset($rtwwwap_verification_done['status']) ? $rtwwwap_verification_done['status'] : false;
				$rtwwwap_verification_done_purchase = isset($rtwwwap_verification_done['purchase_code']) ? $rtwwwap_verification_done['purchase_code'] : false;
				if( empty( $rtwwwap_verification_done ) || $rtwwwap_verification_done_status == false || empty($rtwwwap_verification_done_purchase) )
				{
					return $rtwwwap_price;
				}

						$cart = EDD()->session->get( 'edd_cart' );

				if(!empty($cart))
				{
					$rtwwwap_prod_index = false;
					foreach($cart as $key => $value)
					{
						if($value['id'] == $rtwwwap_prod_id)
						{
							$rtwwwap_prod_index = $key;
						break;
						}
					}
					if($rtwwwap_prod_index === false || (isset($cart[$rtwwwap_prod_index]['rtwwwap_is_twoway']) && $cart[$rtwwwap_prod_index]['rtwwwap_is_twoway'] == true))
					{
						return $rtwwwap_price;
					}					
					$comm_type = get_post_meta($value['id'], '_rtwwwap_cust_comm_type', true);
					$comm_value = get_post_meta($value['id'], '_rtwwwap_cust_comm_value', true);
					
					if($comm_type == 'percentage')
					{
						$rtwwdpd_amount = $comm_value / 100;
						$rtwwwap_dscnted_val = ( floatval( $rtwwdpd_amount ) * $rtwwwap_price);
						$rtwwwap_price = ( floatval( $rtwwwap_price ) - $rtwwwap_dscnted_val );
						 $cart[$rtwwwap_prod_index]['rtwwwap_is_twoway'] = true ; 
					}
					else if($comm_type == 'fixed')
					{
						$rtwwwap_price = ( $rtwwwap_price - $comm_value );
						$cart[$rtwwwap_prod_index]['rtwwwap_is_twoway'] = true ; 
					}		
					return $rtwwwap_price;
				}
				
			}
		}
		
		return $rtwwwap_price;
	}

	function rtwwwap_login_fail_redirect($redirect_to, $requested_redirect_to, $user)
	{
		
		$rtwwwap_login_page_id = get_option('rtwwwap_login_page_id');
		$rtwwwap_affiliate_page_id = get_option('rtwwwap_affiliate_page_id');
		$referrer = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : $_SERVER['PHP_SELF'];
		if( !empty($rtwwwap_login_page_id) )
		{
			$redirect_url = get_permalink($rtwwwap_login_page_id);
			if (is_wp_error($user) && !empty($referrer) && (strstr($referrer, get_permalink($rtwwwap_login_page_id)) ||   strstr($referrer, get_permalink($rtwwwap_affiliate_page_id)) ))
			{
				$redirect_url = add_query_arg('login_errors', urlencode(wp_kses($user->get_error_message(), array('strong' => array(0)))), $redirect_url);
				wp_redirect($redirect_url);
			}
		}
		else if( !empty($rtwwwap_affiliate_page_id) )
		{
			$redirect_url = get_permalink($rtwwwap_affiliate_page_id);
			$redirect_login_url=get_permalink($rtwwwap_login_page_id);
			if (is_wp_error($user) && !empty($redirect_login_url) && (strstr($referrer, $redirect_login_url) || !empty($redirect_url) &&   strstr($referrer, $redirect_url)  ))
			{
				$redirect_url = add_query_arg('login_errors', urlencode(wp_kses($user->get_error_message(), array('strong' => array(0)))), $redirect_url);
				wp_redirect($redirect_url);
			}
		}
		return $redirect_to;
	}

	function rtwwwap_register_fail_redirect($user)
	{
		$rtwwwap_register_page_id = get_option('rtwwwap_register_page_id');
		$rtwwwap_affiliate_page_id = get_option('rtwwwap_affiliate_page_id');
		$referrer = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : $_SERVER['PHP_SELF'];
		
		if( !empty($rtwwwap_register_page_id) )
		{	
			$redirect_url = get_permalink($rtwwwap_register_page_id);
			if (is_wp_error($user) && !empty($referrer) && (strstr($referrer, get_permalink($rtwwwap_register_page_id)) ||   strstr($referrer, get_permalink($rtwwwap_affiliate_page_id))  ))
			{
				$redirect_url = add_query_arg('failed', urlencode(wp_kses($user->get_error_message(), array('strong' => array(0)))), $redirect_url);
				wp_redirect($redirect_url);
			}
		}
		else if( !empty($rtwwwap_affiliate_page_id) )
		{
			$redirect_url = get_permalink($rtwwwap_affiliate_page_id);
			if (is_wp_error($user) && !empty($referrer) && (strstr($referrer, get_permalink($rtwwwap_register_page_id)) ||   strstr($referrer, get_permalink($rtwwwap_affiliate_page_id))  ))
			{				
				$redirect_url = add_query_arg('failed', urlencode(wp_kses($user->get_error_message(), array('strong' => array(0)))), $redirect_url);
				wp_redirect($redirect_url);
			}
		}
		return $user;
	}


	function rtwwwap_override_reset_password_form_redirect() {
		$action = isset( $_GET['action'] ) ? $_GET['action'] : '';
		$rp_key = isset( $_GET['key'] ) ? $_GET['key'] : '';
		$rp_login = isset( $_GET['login'] ) ? $_GET['login'] : '';
		$rtwwwap_affiliate_page_id = get_option('rtwwwap_affiliate_page_id');
		$redirect_url = get_permalink($rtwwwap_affiliate_page_id);

		if ( $_SERVER['REQUEST_METHOD'] != 'POST' )
		{
			if ( 'wp-login.php' === $GLOBALS['pagenow'] && ( 'rp' == $action  || 'resetpass' == $action ) )
			{
				$redirect_url = add_query_arg( 'rp_login', esc_attr( $_GET['login'] ), $redirect_url );
				$redirect_url = add_query_arg( 'rp_key', esc_attr( $_GET['key'] ), $redirect_url );
				$redirect_url = add_query_arg( 'action', esc_attr( $_GET['action'] ), $redirect_url );
				wp_redirect( $redirect_url );
				exit;
			}
		}
	}


	function rtwwwap_do_password_reset()
	{
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] )
		 {
			$key = $_POST['rp_key'];
			$login = $_POST['rp_login'];
			$rtwwwap_affiliate_page_id = get_option('rtwwwap_affiliate_page_id');
			$rtwwwap_redirect_url = get_permalink($rtwwwap_affiliate_page_id);
			$user = check_password_reset_key( $key, $login );

				if(isset($_POST['pass1']) && !empty($_POST['pass1']))
				{
					reset_password( $user, $_POST['pass1'] );
					wp_redirect( $rtwwwap_redirect_url );
					exit;
				}
		}
	 
			
			
	}

	function rtwwwap_login_register_page_redirect()
	{
		global $wp_query ;
		$rtwwwap_current_page_id = $wp_query->get_queried_object_id() ; 
		
		$rtwwwap_login_page_id = get_option('rtwwwap_login_page_id');
		$rtwwwap_register_page_id = get_option('rtwwwap_register_page_id');
		$rtwwwap_affiliate_page_id = get_option('rtwwwap_affiliate_page_id');

		
		if(is_user_logged_in() && !empty($rtwwwap_current_page_id))
		{
			if($rtwwwap_current_page_id == $rtwwwap_login_page_id)
			{
				$rtwwwap_redirect_link = get_permalink($rtwwwap_affiliate_page_id);
				wp_redirect( $rtwwwap_redirect_link );
			}
			elseif($rtwwwap_current_page_id == $rtwwwap_register_page_id)
			{
				$rtwwwap_redirect_link = get_permalink($rtwwwap_affiliate_page_id);
				wp_redirect( $rtwwwap_redirect_link );
			}
		}
		
		
	}

		function rtwwwap_payout_referral_email_callback()
		{
			$rtwwwap_user_id 			= get_current_user_id();
			$rtwwwap_verification_done = get_option( 'rtwwwap_verification_done', array() );
			$rtwwwap_verification_done_status = isset($rtwwwap_verification_done['status']) ? $rtwwwap_verification_done['status'] : false;
			$rtwwwap_verification_done_purchase = isset($rtwwwap_verification_done['purchase_code']) ? $rtwwwap_verification_done['purchase_code'] : false;
			if( empty( $rtwwwap_verification_done ) || $rtwwwap_verification_done_status == false || empty($rtwwwap_verification_done_purchase) )
			{
				return;
			}

	
		$rtwwwap_referral_email = isset($_POST['rtwwwap_referral_email']) ? $_POST['rtwwwap_referral_email'] : '' ;

		update_user_meta( $rtwwwap_user_id, 'rtwwwap_referral_mail', $rtwwwap_referral_email );

		echo json_encode( array( 'rtwwwap_status' => true, 'rtwwwap_message' => esc_html__( 'Referral email updated', 'rtwwwap-wp-wc-affiliate-program' ) ) );
		die;

		}

	function rtwwwap_payout_save_callback()
	{
		$rtwwwap_user_id 			= get_current_user_id();
		$rtwwwap_verification_done = get_option( 'rtwwwap_verification_done', array() );
		$rtwwwap_verification_done_status = isset($rtwwwap_verification_done['status']) ? $rtwwwap_verification_done['status'] : false;
		$rtwwwap_verification_done_purchase = isset($rtwwwap_verification_done['purchase_code']) ? $rtwwwap_verification_done['purchase_code'] : false;
		if( empty( $rtwwwap_verification_done ) || $rtwwwap_verification_done_status == false || empty($rtwwwap_verification_done_purchase) )
		{
			return;
		}

		$rtwwwap_direct_bank = isset($_POST['rtwwwap_direct_bank']) ? $_POST['rtwwwap_direct_bank'] : '' ;
		$rtwwwap_paypal_id = isset($_POST['rtwwwap_paypal_id']) ? $_POST['rtwwwap_paypal_id'] : '' ;
		$rtwwwap_stripe_id = isset($_POST['rtwwwap_stripe_id']) ? $_POST['rtwwwap_stripe_id'] : '' ;
		$rtwwwap_swift_code = isset($_POST['rtwwwap_swift_code']) ? $_POST['rtwwwap_swift_code'] : '' ;

        update_user_meta( $rtwwwap_user_id, 'rtwwwap_paypal_email', $rtwwwap_paypal_id );
        update_user_meta( $rtwwwap_user_id, 'rtwwwap_stripe_email', $rtwwwap_stripe_id );
        update_user_meta( $rtwwwap_user_id, 'rtwwwap_direct', $rtwwwap_direct_bank );
		update_user_meta( $rtwwwap_user_id, 'rtwwwap_swift_code', $rtwwwap_swift_code );

		echo json_encode( array( 'rtwwwap_status' => true, 'rtwwwap_message' => esc_html__( 'Setting Saved Successfully', 'rtwwwap-wp-wc-affiliate-program' ) ) );
		die;

	}


	function rtwwwap_save_profile_callback()
	{
		$rtwwwap_user_id 			= get_current_user_id();
		$rtwwwap_verification_done = get_option( 'rtwwwap_verification_done', array() );
		$rtwwwap_verification_done_status = isset($rtwwwap_verification_done['status']) ? $rtwwwap_verification_done['status'] : false;
		$rtwwwap_verification_done_purchase = isset($rtwwwap_verification_done['purchase_code']) ? $rtwwwap_verification_done['purchase_code'] : false;
		if( empty( $rtwwwap_verification_done ) || $rtwwwap_verification_done_status == false || empty($rtwwwap_verification_done_purchase) )
		{
			return;
		}
		$extra_data = isset($_POST['extra_data']) ? $_POST['extra_data'] : '' ;

			foreach( $extra_data as $key => $value)
			{
				update_user_meta( $rtwwwap_user_id, $key, $value );
			}
		echo json_encode( array( 'rtwwwap_status' => true, 'rtwwwap_message' => esc_html__( 'Profile Updated', 'rtwwwap-wp-wc-affiliate-program' ) ) );
		die;
	}


	function rtwwwap_payment_method_callback()
	{
		$rtwwwap_user_id 			= get_current_user_id();
		$rtwwwap_verification_done = get_option( 'rtwwwap_verification_done', array() );
		$rtwwwap_verification_done_status = isset($rtwwwap_verification_done['status']) ? $rtwwwap_verification_done['status'] : false;
		$rtwwwap_verification_done_purchase = isset($rtwwwap_verification_done['purchase_code']) ? $rtwwwap_verification_done['purchase_code'] : false;
		if( empty( $rtwwwap_verification_done ) || $rtwwwap_verification_done_status == false || empty($rtwwwap_verification_done_purchase) )
		{
			return;
		}
		$rtwwwap_payment_method = isset($_POST['rtwwwap_payment_method']) ? $_POST['rtwwwap_payment_method'] : '' ;

		update_user_meta( $rtwwwap_user_id, 'rtwwwap_payment_method', $rtwwwap_payment_method );
		
		echo json_encode( array( 'rtwwwap_status' => true, 'rtwwwap_message' => esc_html__( 'Payment Method Updated', 'rtwwwap-wp-wc-affiliate-program' ) ) );
		die;
	}


	
	function rtwwwap_rtwwwap_theme_change_callback()
	{
		$rtwwwap_user_id 			= get_current_user_id();

		$rtwwwap_theme = isset($_POST['rtwwwap_theme']) ? $_POST['rtwwwap_theme'] : '' ;
			
	         	update_user_meta( $rtwwwap_user_id, 'rtwwwap_theme', $rtwwwap_theme );
			echo json_encode( array( 'rtwwwap_status' => true ) );
				
			
				die;
				
	}

	public function rtwwwap_withdrawal_request_callback()
	{
		$rtwwwap_check_ajax = check_ajax_referer( 'rtwwwap-ajax-security-string', 'rtwwwap_security_check' );
		$payment_method = isset($_POST['rtwwwap_payment_method'])? $_POST['rtwwwap_payment_method']: "";
		$rtwwwap_swift_code = isset($_POST['rtwwwap_swift_code'])? $_POST['rtwwwap_swift_code']: "";
		$account_details = isset($_POST['rtwwwap_bank_account'])? $_POST['rtwwwap_bank_account']: "";

		if($payment_method == 'rtwwwap_payment_paystack'){
			$payment_method = 'Paystack Payment';
		}
		else if( $payment_method == 'rtwwwap_payment_direct' ){
			$payment_method = 'Bank Transfer';
		}
		else if( $payment_method == 'rtwwwap_payment_paypal' ){
			$payment_method = 'PayPal Payment';
		}
		else if( $payment_method == 'rtwwwap_payment_stripe' ){
			$payment_method = 'Stripe Payment';
		}
		else{
			$payment_method = '';
		}

		if($rtwwwap_check_ajax)
		{	
			global $wpdb;
			$rtwwwap_aff_id = get_current_user_id();
			$rtwwwap_with_amount = isset($_POST['rtwwwap_with_amount']) ? $_POST['rtwwwap_with_amount'] : 0;

			$rtwwwap_commission_settings = get_option( 'rtwwwap_commission_settings_opt' );
			$min_withdrawal_amount = isset($rtwwwap_commission_settings['minimum_ammount_for_affiliate'])? $rtwwwap_commission_settings['minimum_ammount_for_affiliate']:0;

			if($rtwwwap_with_amount < $min_withdrawal_amount){

				if(in_array('addon_for_MLM_qualification/affiliate_mlm_addon.php', apply_filters('active_plugins', get_option('active_plugins') ) ) ){

					$update = $wpdb->insert($wpdb->prefix.'rtwwwap_wallet_transaction',
						array(
						'aff_id'    	=> $rtwwwap_aff_id,
						'request_date'  => date( 'Y-m-d H:i:s' ),
						'amount'		=> $rtwwwap_with_amount ,
						'pay_status'    => "pending",
						'bank_details'	=> $account_details,
						'swift_code'	=> $rtwwwap_swift_code,
						'batch_id'  	=> $payment_method
						));
					if($update)
					{
						$rtwwwap_wallet_amount = get_user_meta( $rtwwwap_aff_id, 'rtw_user_wallet', true );
						$rtwwwap_total_wallet_amount = $rtwwwap_wallet_amount -  $rtwwwap_with_amount;
						update_user_meta( $rtwwwap_aff_id, 'rtw_user_wallet', $rtwwwap_total_wallet_amount );

						$this->rtwwwap_send_rqst_callback($rtwwwap_with_amount,$rtwwwap_aff_id);
						echo json_encode( array( 'rtwwwap_status' => true ) );
					}
					else{
						echo json_encode( array( 'rtwwwap_status' => false,"rtwwwap_message" => esc_html__( 'Something Went Wrong', 'rtwwwap-wp-wc-affiliate-program' )  ) );
					}
					die();
				}
				else{
					$update = $wpdb->insert($wpdb->prefix.'rtwwwap_wallet_transaction',
						array(
						'aff_id'    	=> $rtwwwap_aff_id,
						'request_date'  => date( 'Y-m-d H:i:s' ),
						'amount'		=> $rtwwwap_with_amount ,
						'pay_status'    => "pending"
						));
					if($update)
					{
						$rtwwwap_wallet_amount = get_user_meta( $rtwwwap_aff_id, 'rtw_user_wallet', true );
						$rtwwwap_total_wallet_amount = $rtwwwap_wallet_amount -  $rtwwwap_with_amount;
						update_user_meta( $rtwwwap_aff_id, 'rtw_user_wallet', $rtwwwap_total_wallet_amount );

						$this->rtwwwap_send_rqst_callback($rtwwwap_with_amount,$rtwwwap_aff_id);
						echo json_encode( array( 'rtwwwap_status' => true ) );
					}
					else{
						echo json_encode( array( 'rtwwwap_status' => false,"rtwwwap_message" => esc_html__( 'Something Went Wrong', 'rtwwwap-wp-wc-affiliate-program' )  ) );
					}
					die();
				}
			}
			else{
				echo json_encode( array( 'rtwwwap_status' => false,"rtwwwap_message" => esc_html__( "You can not withdrawal more than minimum withdrawal amount i.e $min_withdrawal_amount", 'rtwwwap-wp-wc-affiliate-program' )  ) );
			}
 		}
		wp_die();
	}

	function rtwwwap_login_request_callback()
	{

		$rtwwwap_check_ajax = check_ajax_referer( 'rtwwwap-ajax-security-string', 'rtwwwap_security_check' );
		if($rtwwwap_check_ajax)
		{	
			$rtwwwap_login_page_id = get_option('rtwwwap_login_page_id');
			$rtwwwap_affiliate_page_id = get_option('rtwwwap_affiliate_page_id');
			$rtwwwap_redirect_link = get_permalink($rtwwwap_affiliate_page_id);

			if($rtwwwap_login_page_id)
			{
				$rtwwwap_login_error_redirect = get_permalink($rtwwwap_login_page_id);
			}
			else{
				$rtwwwap_login_error_redirect = get_permalink($rtwwwap_affiliate_page_id);
			}
			

			$rtwwwap_email_id = isset($_POST['user_id_email']) ? $_POST['user_id_email'] : "";
			$rtwwwap_pass = isset($_POST['user_pass']) ? $_POST['user_pass'] : "";
			$email_valid = isset($_POST['email_valid']) ? $_POST['email_valid'] : "";

			if($email_valid == "true")
			{
				$rtwwwap_check_email = get_user_by('email', $rtwwwap_email_id);
				if( $rtwwwap_check_email )
				{
					wp_set_current_user($rtwwwap_check_email->data->ID);
					wp_set_auth_cookie($rtwwwap_check_email->data->ID);
					$rtwwwap_creds = array(
						'user_login'    => $rtwwwap_check_email->data->user_login,
						'user_password' => $rtwwwap_pass,
						'remember'      => true
					);
					$rtwwwap_user = wp_signon( $rtwwwap_creds);
					if ( is_wp_error( $rtwwwap_user ) ) {
						$rtwwwap_err_msg = esc_html__( 'User Name / Email OR Pasword is incorrect', 'rtwwwap-wp-wc-affiliate-program' );
						echo json_encode( array( 'rtwwwap_status' => false,"rtwwwap_message" => $rtwwwap_err_msg ,"rtwwwap_redirect" => $rtwwwap_login_error_redirect  ) );	
					}
					else{
						echo json_encode( array( 'rtwwwap_status' => true,"rtwwwap_redirect" => $rtwwwap_redirect_link  ) );
					}
				}
				else
				{
					$rtwwwap_err_msg = esc_html__( 'User Name / Email OR Pasword is incorrect', 'rtwwwap-wp-wc-affiliate-program' );
					echo json_encode( array( 'rtwwwap_status' => false,"rtwwwap_message" => $rtwwwap_err_msg ,"rtwwwap_redirect" => $rtwwwap_login_error_redirect ) );
				}
			}
			else{
					$rtwwwap_creds = array(
						'user_login'    => $rtwwwap_email_id,
						'user_password' => $rtwwwap_pass,
						'remember'      => true
					);
				$rtwwwap_user = wp_signon( $rtwwwap_creds);
				if ( is_wp_error( $rtwwwap_user ) ) {
					$rtwwwap_err_msg = esc_html__( 'User Name / Email OR Pasword is incorrect', 'rtwwwap-wp-wc-affiliate-program' );
					echo json_encode( array( 'rtwwwap_status' => false,"rtwwwap_message" => $rtwwwap_err_msg ,"rtwwwap_redirect" => $rtwwwap_login_error_redirect  ) );	
				}
				else{
					echo json_encode( array( 'rtwwwap_status' => true,"rtwwwap_redirect" => $rtwwwap_redirect_link  ) );
				}
			}
			
		}
		wp_die();
	}


	function rtwwwap_register_request_callback()
	{
		$rtwwwap_check_ajax = check_ajax_referer( 'rtwwwap-ajax-security-string', 'rtwwwap_security_check' );
		if($rtwwwap_check_ajax)
		{	
			$rtwwwap_extra_fields = isset($_POST['extra_fields']) ? $_POST['extra_fields'] : "";
			$rtwwwap_user_name = isset($_POST['user_name']) ? $_POST['user_name'] : "";
			$rtwwwap_user_email = isset($_POST['user_email']) ? $_POST['user_email'] : "";
			$rtwwwap_user_pass = isset($_POST['user_pass']) ? $_POST['user_pass'] : "";
			$rtwwwap_user_conf_pass = isset($_POST['user_conf_pass']) ? $_POST['user_conf_pass'] : "";
			$rtwwwap_phone = isset($_POST['user_phone']) ? $_POST['user_phone'] : "";
			$rtwwwap_refer_code = isset($_POST['user_referral_code']) ? $_POST['user_referral_code'] : "";
			
			$rtwwwap_custom_user_register = array(
				'user_login' =>  $rtwwwap_user_name,
				'user_email'   =>  $rtwwwap_user_email,
				'role'   =>  'customer',
				'user_pass'   =>  $rtwwwap_user_pass
				); 
				$rtwwwap_custom_create_user = wp_insert_user( $rtwwwap_custom_user_register );        

			if ( ! is_wp_error( $rtwwwap_custom_create_user ) ) 
			{
				// update_user_meta($rtwwwap_custom_create_user,'billing_phone',$rtwwwap_phone);
				$rtwwwap_login_page_id = get_option('rtwwwap_login_page_id');
				$rtwwwap_affiliate_page_id = get_option('rtwwwap_affiliate_page_id');
				if($rtwwwap_login_page_id)
				{
					$rtwwwap_register_redirect = get_permalink($rtwwwap_login_page_id);
				}
				else{
					$rtwwwap_register_redirect = get_permalink($rtwwwap_affiliate_page_id);
				}
		
				if(is_array($rtwwwap_extra_fields) && !empty($rtwwwap_extra_fields)){
					foreach ($rtwwwap_extra_fields as $user_meta_key => $user_meta_value) {
						if($user_meta_key != "user_login" && $user_meta_key != "user_email")
						update_user_meta( $rtwwwap_custom_create_user,  $user_meta_key, $user_meta_value );
					}
				}

				$all_emails = get_option('customize_email', false);
				$signup_email = get_option('signup_email','null');

				if(isset($all_emails['Signup Email']['subject'])){
					$rtwwwap_subject_text = $all_emails['Signup Email']['subject'];
					$rtwwwap_message_text = $all_emails['Signup Email']['content'];
				}

				// send mail to the user

				$rtwwwap_html = $rtwwwap_message_text;
            		$rtwwwap_from 	= esc_html( get_bloginfo( 'admin_email' ) );
            		$rtwwwap_user_name 	= esc_html( get_bloginfo( 'user_name' ) );
            		$rtwwwap_subject = esc_html__( $rtwwwap_subject_text, 'rtwwwap-wp-wc-affiliate-program' );
            		$rtwwwap_headers[] 		= 'Content-Type: text/html; charset=utf-8';
            		$rtwwwap_headers[] 		= sprintf( '%s: %s <%s>', esc_html__( 'From', 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_user_name,$rtwwwap_from);
                    
					if($signup_email == "true"){
                    	wp_mail( $rtwwwap_user_email, $rtwwwap_subject,$rtwwwap_html,$rtwwwap_headers);
					}

				// code ended here

			
				$this->rtwwwap_user_register_signup_bonus($rtwwwap_custom_create_user,$rtwwwap_refer_code);
				
				$rtwwwap_register_redirect = add_query_arg( 'register', true , $rtwwwap_register_redirect );
				echo json_encode( array('rtwwwap_status' => true ,"redirect_link" => $rtwwwap_register_redirect , "rtwwwap_message" => esc_html__( "Successfully Register", "rtwwwap-wp-wc-affiliate-program" )));  
			}
			else{
				echo json_encode( array('rtwwwap_status' => false ,'error' => esc_html__(  $rtwwwap_custom_create_user->get_error_message(), "rtwwwap-wp-wc-affiliate-program" )));   
			   }
		}
		wp_die();
	}


	function rtwwwap_apply_coupon($coupon)
	{
		global $woocommerce;
		$rtwwwap_coupon_obj = new WC_Coupon($coupon);
		$rtwwwap_coupon_id =  $rtwwwap_coupon_obj->get_id();
		$rtwwwap_aff_id = get_post_meta( $rtwwwap_coupon_id, 'rtwwwap_coupon_aff_id');
		if($rtwwwap_aff_id)
		{
			if(	$rtwwwap_aff_id[0] && $rtwwwap_aff_id[0] > 0)
			{
				$this->rtwwwap_set_cupon_id_cookie($rtwwwap_aff_id[0]);
			}
		}
	}

	// //set_coupon_cookie
	
	function rtwwwap_set_cupon_id_cookie($rtwwwap_aff_id)
	{
		$rtwwwap_extra_features 	= get_option( 'rtwwwap_extra_features_opt' );
		$rtwwwap_cookie_time 	= isset( $rtwwwap_extra_features[ 'cookie_time' ] ) ? $rtwwwap_extra_features[ 'cookie_time' ] : 0;
		if( $rtwwwap_cookie_time ){
			$rtwwwap_cookie_time = time()+( $rtwwwap_cookie_time * 24 * 60 * 60 );
		}
		if( isset( $_COOKIE[ 'rtwwwap_referral' ] ) ){
			unset( $_COOKIE[ 'rtwwwap_referral' ] );
		}
		setcookie( 'rtwwwap_referral', $rtwwwap_aff_id, $rtwwwap_cookie_time, '/' );
	}

	function rtwwwap_noti_id_callback()
	{
		$rtwwwap_check_ajax = check_ajax_referer( 'rtwwwap-ajax-security-string', 'rtwwwap_security_check' );
		if($rtwwwap_check_ajax)
		{	
			$rtwwwap_user_id = get_current_user_id();
			$rtwwwap_noti_ID = isset($_POST['rtwwwap_noti_ID']) ? sanitize_text_field($_POST['rtwwwap_noti_ID']) : "" ;
			if($rtwwwap_noti_ID && $rtwwwap_user_id )
			{
				$rtwwwap_user_noti_id = get_user_meta($rtwwwap_user_id,'rtwwwap_user_noti_id');
				$rtwwwap_user_noti_id = isset($rtwwwap_user_noti_id[0])? $rtwwwap_user_noti_id[0] : $rtwwwap_user_noti_id;
		
				if(!empty($rtwwwap_user_noti_id))
				{
					if(!in_array($rtwwwap_noti_ID,$rtwwwap_user_noti_id))
					{
						$rtwwwap_user_noti_id[] = $rtwwwap_noti_ID;
						update_user_meta($rtwwwap_user_id,'rtwwwap_user_noti_id',$rtwwwap_user_noti_id);
						$rtwwwap_user_noti_id = get_user_meta($rtwwwap_user_id,'rtwwwap_user_noti_id');
					}
				}
				else
				{				
					$rtwwwap_temp_id[] = $rtwwwap_noti_ID;
					update_user_meta($rtwwwap_user_id,'rtwwwap_user_noti_id',$rtwwwap_temp_id);
					$rtwwwap_user_noti_id = get_user_meta($rtwwwap_user_id,'rtwwwap_user_noti_id');		
				}
			}
			$rtwwwap_noti_unseend_count =  self::notification_counting($rtwwwap_user_id);		
			echo json_encode( array('rtwwwap_status' => true,'rtwwwap_noti_unseen_count' => $rtwwwap_noti_unseend_count));   
		}
		wp_die();
	}

	private function notification_counting($rtwwwap_user_id)
	{
		$rtwwwap_noti_option = get_option("rtwwwap_noti_arr");
		$rtwwwap_user_noti_id = get_user_meta($rtwwwap_user_id,'rtwwwap_user_noti_id');
		$rtwwwap_user_seen_noti = isset($rtwwwap_user_noti_id[0]) ? count($rtwwwap_user_noti_id[0]) : 0;
		$rtwwwap_count_noti = isset($rtwwwap_noti_option) ? count($rtwwwap_noti_option) : 0;
		$rtwwwap_final_count_show = $rtwwwap_count_noti - $rtwwwap_user_seen_noti;

		return $rtwwwap_final_count_show;
	}

	public function rtwwwap_coupon_removed_action($coupon)
	{
		global $woocommerce;
		$rtwwwap_coupon_obj = new WC_Coupon($coupon);
		$rtwwwap_coupon_id =  $rtwwwap_coupon_obj->get_id();
		$rtwwwap_aff_id = get_post_meta( $rtwwwap_coupon_id, 'rtwwwap_coupon_aff_id');
		if( isset( $_COOKIE[ 'rtwwwap_referral' ] ) ){
			$rtwwwap_referral 	= explode( '#', $_COOKIE[ 'rtwwwap_referral' ] );
			$rtwwwap_affiliate_id 	= esc_html( $rtwwwap_referral[ 0 ] );
			if($rtwwwap_aff_id[0] && $rtwwwap_aff_id[0] > 0)
			{
				if($rtwwwap_aff_id[0] == $rtwwwap_affiliate_id )
				{
					setcookie("rtwwwap_referral", '', time()-1000, '/');
				}
			}
		}
	
	}

	public function rtwwwap_coupon_check()
	{
		if(RTWWWAP_IS_WOO)
		{
			if(!is_admin())
			{
				global $woocommerce;
				if( is_object(WC()->cart))
				{
					$rtwwwap_coupon_array = WC()->cart->get_coupons();

					if( !empty( $rtwwwap_coupon_array ) )
					{
						foreach($rtwwwap_coupon_array as $key => $value)
						{
							$rtwwwap_coupon_obj = new WC_Coupon($key);
							$rtwwwap_coupon_id =  $rtwwwap_coupon_obj->get_id();
							$rtwwwap_aff_id = get_post_meta( $rtwwwap_coupon_id, 'rtwwwap_coupon_aff_id');
							if( isset( $_COOKIE[ 'rtwwwap_referral' ] ) )
							{
								$rtwwwap_referral 	= explode( '#', $_COOKIE[ 'rtwwwap_referral' ] );
								$rtwwwap_affiliate_id 	= esc_html( $rtwwwap_referral[ 0 ] );

								if(isset($rtwwwap_aff_id[0]) && $rtwwwap_aff_id[0] > 0)
								{
									if($rtwwwap_aff_id[0] != $rtwwwap_affiliate_id )
									{
										WC()->cart->remove_coupon($key);
									}
								}
							}
						}
					}

				}
				
			}
		}
	}

	private function rtwwwap_get_browser() { 
		$rtwwwap_u_agent = $_SERVER['HTTP_USER_AGENT'];
		$rtwwwap_bname = 'Unknown';
		$rtwwwap_platform = 'Unknown';
	
		//First get the platform?
		if (preg_match('/Android/i', $rtwwwap_u_agent)) {
			$rtwwwap_platform = 'Android';
		}elseif (preg_match('/iPhone.*Mobile|iPod|iPad|AppleCoreMedia/i', $rtwwwap_u_agent)) {
			$rtwwwap_platform = 'iOS';
		}elseif (preg_match('/blackberry|BB10|rim tablet os/i', $rtwwwap_u_agent)) {
			$rtwwwap_platform = 'BlackBerry';
		}elseif (preg_match('/macintosh|mac os x/i', $rtwwwap_u_agent)) {
			$rtwwwap_platform = 'Mac';
		}elseif (preg_match('/windows|win32/i', $rtwwwap_u_agent)) {
			$rtwwwap_platform = 'Windows';
		}elseif (preg_match('/linux/i', $rtwwwap_u_agent)) {
			$rtwwwap_platform = 'Linux';
		}
	
		// Next get the name of the useragent yes seperately and for good reason
		if(preg_match('/MSIE/i',$rtwwwap_u_agent) && !preg_match('/Opera/i',$rtwwwap_u_agent)){
			$rtwwwap_bname = 'Internet Explorer';
			$ub = "MSIE";
		}elseif(preg_match('/Firefox/i',$rtwwwap_u_agent) || preg_match('/FxiOS/i',$rtwwwap_u_agent)){
			$rtwwwap_bname = 'Mozilla Firefox';
			$ub = "Firefox";
		}elseif(preg_match('/OPR/i',$rtwwwap_u_agent)){
			$rtwwwap_bname = 'Opera';
			$ub = "Opera";
		}elseif((preg_match('/Chrome/i',$rtwwwap_u_agent) || preg_match('/CriOS/i',$rtwwwap_u_agent) || preg_match('/CrMo/i',$rtwwwap_u_agent)) && !preg_match('/Edge/i',$rtwwwap_u_agent)){
			$rtwwwap_bname = 'Google Chrome';
			$ub = "Chrome";
		}elseif(preg_match('/Safari/i',$rtwwwap_u_agent) && !preg_match('/Edge/i',$rtwwwap_u_agent)){
			$rtwwwap_bname = 'Apple Safari';
			$ub = "Safari";
		}elseif(preg_match('/Netscape/i',$rtwwwap_u_agent)){
			$rtwwwap_bname = 'Netscape';
			$ub = "Netscape";
		}elseif(preg_match('/Edge/i',$rtwwwap_u_agent)){
			$rtwwwap_bname = 'Edge';
			$ub = "Edge";
		}elseif(preg_match('/Trident/i',$rtwwwap_u_agent)){
			$rtwwwap_bname = 'Internet Explorer';
			$ub = "MSIE";
		}
	
		return array(
			'name'      => $rtwwwap_bname,
			'platform'  => $rtwwwap_platform
		);
	} 

	public function rtwwwap_get_user_ip()
	{
		if(!wp_doing_ajax())
		{
			if(isset($_COOKIE['rtwwwap_referral_link']) && isset( $_COOKIE[ 'rtwwwap_referral' ] ) && $_COOKIE['rtwwwap_referral_link'] != '' )                
			{
				global $wpdb;
				if(!class_exists('Mobile_Detect')){
					require_once RTWWWAP_DIR."third_party/mobile_detect/Mobile_Detect.php";
				}
				$rtwwwap_detect = new Mobile_Detect;
				
							// Any mobile device (phones or tablets).
				if ( $rtwwwap_detect->isMobile() ) {
					$rtwwwap_device = 'Mobile';
				}
				// Any tablet device.
				elseif( $rtwwwap_detect->isTablet() ){
					$rtwwwap_device = 'Tablet';
				}
				else 
				{
					$rtwwwap_device = 'Desktop';
				}
				
				$rtwwwap_brow_plat = $this->rtwwwap_get_browser();
				$rtwwwap_browser = $rtwwwap_brow_plat['name'];
				$rtwwwap_platform = $rtwwwap_brow_plat['platform'];
				// Check for a specific platform with the help of the magic methods:

				$rtwwwap_referral 	= explode( '#', $_COOKIE[ 'rtwwwap_referral' ] );
				$rtwwwap_affiliate_id 	= esc_html( $rtwwwap_referral[ 0 ] );

				$rtwwwap_user_id = get_current_user_id();
				$rtwwwap_current_date = date( 'Y-m-d H:i:s' );
				$rtwwwap_next_date = date('Y-m-d H:i:s', strtotime(' -1 hours'));
				$rtwwwap_current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
				$rtwwwap_ip_address = '';
				if (isset($_SERVER['HTTP_CLIENT_IP'])){
					$rtwwwap_ip_address = $_SERVER['HTTP_CLIENT_IP'];
				}
				else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
					$rtwwwap_ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
				}
				else if(isset($_SERVER['HTTP_X_FORWARDED'])){
					$rtwwwap_ip_address = $_SERVER['HTTP_X_FORWARDED'];
				}
				else if(isset($_SERVER['HTTP_FORWARDED_FOR'])){
					$rtwwwap_ip_address = $_SERVER['HTTP_FORWARDED_FOR'];
				}
				else if(isset($_SERVER['HTTP_FORWARDED'])){
					$rtwwwap_ip_address = $_SERVER['HTTP_FORWARDED'];
				}
				else if(isset($_SERVER['REMOTE_ADDR'])){
					$rtwwwap_ip_address = $_SERVER['REMOTE_ADDR'];
				}
				else{
					$rtwwwap_ip_address = 'UNKNOWN';
				}
			
				$rtwwwap_all_track 	= $wpdb->get_results( $wpdb->prepare("SELECT `count`,`id` FROM ".$wpdb->prefix."rtwwwap_visitors_track WHERE `ip` = %s AND `date` >= %s AND `date` < %s AND `ref_link` = %s AND device= %s", $rtwwwap_ip_address, $rtwwwap_next_date, $rtwwwap_current_date, $rtwwwap_current_url, $rtwwwap_device ), ARRAY_A );
				if( empty( $rtwwwap_all_track ) && !is_cart() && !is_checkout() )
				{
		
					$rtwwwap_ip_addresses = $wpdb->insert(
						$wpdb->prefix.'rtwwwap_visitors_track',
						array(
							'aff_id'    	=> $rtwwwap_affiliate_id,
							'ref_link'	=> $rtwwwap_current_url,
							'date'		=> date( 'Y-m-d H:i:s' ),
							'agent'    	=> $rtwwwap_browser,
							'device' => $rtwwwap_device,
							'platform' => $rtwwwap_platform,
							'ip'    	=> $rtwwwap_ip_address,
							'count' => 1
						)
					);
				}
				else if( !empty( $rtwwwap_all_track ) && !is_cart() && !is_checkout() )
				{	
						$update_count = $rtwwwap_all_track[0]['count'] + 1; 
							$rtwwwap_updated = 	$wpdb->update(
							$wpdb->prefix.'rtwwwap_visitors_track',
							array( 'count' => 	$update_count  ),
							array( 'id' => $rtwwwap_all_track[0]['id'] )
						);

				}
			}
		}
	}

	function rtwwwap_send_email_callback(){
	    
	    if (!check_ajax_referer( 'rtwwwap-ajax-security-string', 'rtwwwap_security_check' ))
		{
			return;
		}
	    
	    $rtwwwap_to = isset($_POST['useremail']) ? sanitize_text_field($_POST['useremail']):"";
	    $time=time();
	    
	    $user = get_user_by( 'email', $rtwwwap_to );
	    if(!$user){
	       echo json_encode( array('rtwwwap_status' => false,'rtwwwap_error_msg' => "This mail does not exist")); 
           wp_die();
	    }
	    else{
	        $userId = $user->ID;
    	    $rtwwwap_email_otp = rand(000000,999999);
    		$rtwwwap_from 	= esc_html( get_bloginfo( 'admin_email' ) );
    		$rtwwwap_user_name 	= esc_html( get_bloginfo( 'user_name' ) );
    		$rtwwwap_subject = esc_html__( "OTP", 'rtwwwap-wp-wc-affiliate-program' );
    		$rtwwwap_headers[] 		= 'Content-Type: text/html; charset=utf-8';
    		$rtwwwap_headers[] 		= sprintf( '%s: %s <%s>', esc_html__( 'From', 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_user_name,$rtwwwap_from);
    	    $rtwwwap_store_otp = array(
    	            'otp' => $rtwwwap_email_otp,
    	            'time' => $time,
    	            'useremail' => $rtwwwap_to
    	       );
    	       
    	   update_user_meta($userId , 'otp' , $rtwwwap_store_otp);
    	//    $_SESSION['rtwwwap_email'] = $rtwwwap_to;
            
           $rtwwwap_result = wp_mail( $rtwwwap_to, $rtwwwap_subject,$rtwwwap_email_otp,$rtwwwap_headers);

		//    echo json_encode( array('rtwwwap_status' => false ,'error' => esc_html__(  $rtwwwap_custom_create_user->get_error_message(), "rtwwwap-wp-wc-affiliate-program" ))); 

           if($rtwwwap_result){
               echo json_encode( array('rtwwwap_status' => true,'rtwwwap_success_msg' => esc_html__("OTP sent successfuly","rtwwwap-wp-wc-affiliate-program"))); 
               wp_die();
           }
           else{
               echo json_encode( array('rtwwwap_status' => false,'rtwwwap_error_msg' => esc_html__("please fill the valid email","rtwwwap-wp-wc-affiliate-program")));
               wp_die();
           }
    	}
        
    }

	function rtwwwap_verify_otp_psw_callback(){
        
        if (!check_ajax_referer( 'rtwwwap-ajax-security-string', 'rtwwwap_security_check' ))
		{
			return;
		}
        
        $otp = isset($_POST['otp']) ? sanitize_text_field($_POST['otp']): "";
        $current_time_stamp = time();
        $password = isset($_POST['password']) ? sanitize_text_field($_POST['password']) : "";
        $confirmPassword = isset($_POST['confirmPassword']) ? sanitize_text_field($_POST['confirmPassword']) :"";
		$user_email = isset($_POST['user_email']) ? sanitize_text_field($_POST['user_email']) :"";
        // $user_email = $_SESSION['rtwwwap_email'];
        $user = get_user_by( 'email', $user_email );
        $userId = $user->ID;
        
        $get_value = get_user_meta($userId , 'otp', false);
        
        $rtwwwap_from 	= esc_html( get_bloginfo( 'admin_email' ) );
		$rtwwwap_user_name 	= esc_html( get_bloginfo( 'user_name' ) );
		$rtwwwap_subject = esc_html__( "changed password", 'rtwwwap-wp-wc-affiliate-program' );
		$rtwwwap_headers[] 		= 'Content-Type: text/html; charset=utf-8';
		$rtwwwap_headers[] 		= sprintf( '%s: %s <%s>', esc_html__( 'From', 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_user_name,$rtwwwap_from);
        
        $get_otp_from_table = $get_value[0]['otp'];
        $get_timestamp = $get_value[0]['time'];
        $time_difference = $current_time_stamp - $get_timestamp;
        if($password != $confirmPassword){
            echo json_encode( array('rtwwwap_status' => false,'rtwwwap_error_msg' =>"Both password must be same"));
            wp_die();
        }
        else{
             if($otp == $get_otp_from_table){
                if($time_difference<=300){
                    wp_set_password($password , $userId);
                    wp_mail( $user_email, $rtwwwap_subject,$password,$rtwwwap_headers);
                    echo json_encode( array('rtwwwap_status' => true,'rtwwwap_success_msg' => esc_html__("password changed successfuly",'rtwwwap-wp-wc-affiliate-program'))); 
                    wp_die();
                }
                else{
                    echo json_encode( array('rtwwwap_status' => false,'rtwwwap_error_msg' =>esc_html__("OTP time is expired",'rtwwwap-wp-wc-affiliate-program')));
                    wp_die();
                }
                
            }
            else{
                echo json_encode( array('rtwwwap_status' => false,'rtwwwap_error_msg' => esc_html__("OTP does not match",'rtwwwap-wp-wc-affiliate-program')));
                wp_die();
            }
        }
        
        
    }
    
    // function rtwwwap_verify_old_psw_callback(){
        
    //     if (!check_ajax_referer( 'rtwwwap-ajax-security-string', 'rtwwwap_security_check' ))
	// 	{
	// 		return;
	// 	}
        
    //     $rtwwwap_current_user_id = get_current_user_id();
    //     $oldPassword = isset($_POST['oldPassword'])? sanitize_text_field($_POST['oldPassword']) : "";
    //     $password = isset($_POST['password'])? sanitize_text_field($_POST['password']) : "";
    //     $confirmPassword = isset($_POST['confirmPassword']) ? sanitize_text_field($_POST['confirmPassword']) : "";
    //     $user_detail = get_userdata($rtwwwap_current_user_id);
    //     $wp_password = $user_detail->user_pass;
    //     $user_email = $user_detail->user_email;
    //     $check = wp_check_password($oldPassword , $wp_password, $rtwwwap_current_user_id);
    //     $rtwwwap_from 	= esc_html( get_bloginfo( 'admin_email' ) );
	// 	$rtwwwap_user_name 	= esc_html( get_bloginfo( 'user_name' ) );
	// 	$rtwwwap_subject = esc_html__( "changed password", 'rtwwwap-wp-wc-affiliate-program' );
	// 	$rtwwwap_headers[] 		= 'Content-Type: text/html; charset=utf-8';
	// 	$rtwwwap_headers[] 		= sprintf( '%s: %s <%s>', esc_html__( 'From', 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_user_name,$rtwwwap_from);
        
    //     if(!$check){
    //         echo json_encode( array('rtwwwap_status' => false,'rtwwwap_error_msg' => "Please fill correct old password"));
    //         wp_die();
    //     }
    //     else if($password != $confirmPassword){
    //         echo json_encode( array('rtwwwap_status' => false,'rtwwwap_error_msg' => "Both password must be same"));
    //         wp_die();
    //     }
    //     else{
    //         wp_set_password($password , $rtwwwap_current_user_id);
    //         wp_mail( $user_email, $rtwwwap_subject,$password,$rtwwwap_headers);
    //         echo json_encode( array('rtwwwap_status' => true,'rtwwwap_success_msg' => "Password changed successfully"));
    //         wp_die();
    //     }
    // }

	function rtwwwap_verify_old_psw_callback(){
        
        if (!check_ajax_referer( 'rtwwwap-ajax-security-string', 'rtwwwap_security_check' ))
		{
			return;
		}
        
        $rtwwwap_current_user_id = get_current_user_id();
        $oldPassword = isset($_POST['oldPassword'])? sanitize_text_field($_POST['oldPassword']) : "";
        $password = isset($_POST['password'])? sanitize_text_field($_POST['password']) : "";
        $confirmPassword = isset($_POST['confirmPassword']) ? sanitize_text_field($_POST['confirmPassword']) : "";
        $user_detail = get_userdata($rtwwwap_current_user_id);
        $wp_password = $user_detail->user_pass;
        $user_email = $user_detail->user_email;
        $check = wp_check_password($oldPassword , $wp_password, $rtwwwap_current_user_id);
        $rtwwwap_from 	= esc_html( get_bloginfo( 'admin_email' ) );
		$rtwwwap_user_name 	= esc_html( get_bloginfo( 'user_name' ) );
		$rtwwwap_subject = esc_html__( "changed password", 'rtwwwap-wp-wc-affiliate-program' );
		$rtwwwap_headers[] 		= 'Content-Type: text/html; charset=utf-8';
		$rtwwwap_headers[] 		= sprintf( '%s: %s <%s>', esc_html__( 'From', 'rtwwwap-wp-wc-affiliate-program' ), $rtwwwap_user_name,$rtwwwap_from);
        
        if(!$check){
            echo json_encode( array('rtwwwap_status' => false,'rtwwwap_error_msg' => esc_html__("Please fill correct old password",'rtwwwap-wp-wc-affiliate-program')));
            wp_die();
        }
        else if($password != $confirmPassword){
            echo json_encode( array('rtwwwap_status' => false,'rtwwwap_error_msg' => esc_html__("Both password must be same",'rtwwwap-wp-wc-affiliate-program')));
            wp_die();
        }
        else{
            wp_set_password($password , $rtwwwap_current_user_id);
			// wp_update_user(array('ID' => $rtwwwap_current_user_id, 'user_pass' => $password));
            wp_mail( $user_email, $rtwwwap_subject,$password,$rtwwwap_headers);
            echo json_encode( array('rtwwwap_status' => true,'rtwwwap_success_msg' => esc_html__("Password changed successfully",'rtwwwap-wp-wc-affiliate-program')));
            wp_die();
        }
    }

	function rtwwwap_generate_custom_code($rtwwwap_len){
		$rtwwwap_characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$rtwwwap_randomString = '';
		// $n = 6;
	
		for ($rtwwwap_init = 0; $rtwwwap_init < $rtwwwap_len; $rtwwwap_init++) {
			$rtwwwap_index = rand(0, strlen($rtwwwap_characters) - 1);
			$rtwwwap_randomString .= $rtwwwap_characters[$rtwwwap_index];
		}
		$rtwwwap_all_users = get_users(array(
			'meta_key'     => 'rtwwwap_referee_custom_str',
			'meta_value'   => $rtwwwap_randomString,
		));
		
		if($rtwwwap_all_users){
			$this->rtwwwap_generate_custom_code(6);
		}
		else{
			return $rtwwwap_randomString;
		}
		
	}

	// public function rtwwwap_order_details_callback(){
	// 	global $wpdb;
	// 	$rtwwwap_user_id = isset($_POST['rtwwwap_user_id'])? $_POST['rtwwwap_user_id']: " " ;

	// 	$rtwwwap_html = "";

	// 	$phone = get_user_meta($rtwwwap_user_id , 'billing_phone' , false);

	// 	$user_date = get_userdata($rtwwwap_user_id);
	// 	$email = $user_date->data->user_email;
	// 	// print_r($email);
	// 	// die("ffdrrd");
		
	// 	$rtwwwap_order_id = $wpdb->get_results( $wpdb->prepare( "SELECT `order_id` FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `aff_id` = %d AND `type` = %d",  $rtwwwap_user_id , 0), ARRAY_A );

	// 	// echo '<pre>';
	// 	// print_r($rtwwwap_order_id);
	// 	// echo '</pre>';
	// 	// die("dxgvdxf");

	// 	if( !empty($rtwwwap_order_id) ){

	// 		foreach($rtwwwap_order_id as $key => $value){
	// 			$order = wc_get_order( $value['order_id'] );
	// 			$name = "";
	// 			$user_id = $order->get_user_id();
				
	// 			$name = $order->get_billing_first_name().' '.$order->get_billing_last_name();

	// 			$rtwwcpig_order_data   = $order->get_data();
	// 			$date = $rtwwcpig_order_data['date_created']->date('d/m/Y');

	// 			$amount = $order->get_total();
	// 			$rtwwwap_html .= '<tr><td>';
	// 			$rtwwwap_html .= esc_html__( $value['order_id'], "rtwwwap-wp-wc-affiliate-program" );
	// 			$rtwwwap_html .= '</td><td>'; 
	// 			$rtwwwap_html .= esc_html__( $date, "rtwwwap-wp-wc-affiliate-program" ); 
	// 			$rtwwwap_html .= '</td><td>';
	// 			$rtwwwap_html .= esc_html__( $amount, "rtwwwap-wp-wc-affiliate-program" );
	// 			$rtwwwap_html .= '</td><td>';
	// 			$rtwwwap_html .= esc_html__( $name, "rtwwwap-wp-wc-affiliate-program" );
	// 			$rtwwwap_html .= '</td></tr>';
	// 		}
			
	// 	}
	// 	else{
	// 		$rtwwwap_html .= '<tr><td colspan = "4" style = "text-align: center;">';
	// 		$rtwwwap_html .= esc_html__( "No order available", "rtwwwap-wp-wc-affiliate-program" );
	// 		$rtwwwap_html .= '</td></tr>';
	// 	}
	// 	echo json_encode( array( 'rtwwwap_status'=> true,'rtwwwap_tree_html' => $rtwwwap_html, 'rtwwwap_email' =>$email, 'rtwwwap_phone' => $phone) ); die;
		
	// }

}
