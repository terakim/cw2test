<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.redefiningtheweb.com
 * @since      1.0.0
 *
 * @package    Rtwalwm_Wp_Wc_Affiliate_Program
 * @subpackage Rtwalwm_Wp_Wc_Affiliate_Program/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Rtwalwm_Wp_Wc_Affiliate_Program
 * @subpackage Rtwalwm_Wp_Wc_Affiliate_Program/public
 * @author     RedefiningTheWeb <developer@redefiningtheweb.com>
 */
class Rtwalwm_Wp_Wc_Affiliate_Program_Public {
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $rtwalwm_plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $rtwalwm_version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $rtwalwm_plugin_name       The name of the plugin.
	 * @param      string    $rtwalwm_version    The version of this plugin.
	 */
	public function __construct( $rtwalwm_plugin_name, $rtwalwm_version ) {
		$this->rtwalwm_plugin_name 	= $rtwalwm_plugin_name;
		$this->rtwalwm_version 		= $rtwalwm_version;
		add_shortcode( 'rtwwwap_affiliate_page', array( $this, 'rtwalwm_affiliate_page_callback') );
		add_shortcode( 'rtwwwap_aff_login_page', array( $this, 'rtwalwm_aff_login_page_callback') );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function rtwalwm_enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Rtwalwm_Wp_Wc_Affiliate_Program_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rtwalwm_Wp_Wc_Affiliate_Program_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->rtwalwm_plugin_name, plugin_dir_url( __FILE__ ) . 'css/rtwalwm-wp-wc-affiliate-program-public.css', array(), $this->rtwalwm_version, 'all' );
		wp_enqueue_style( "select2", RTWALWM_URL. '/assets/Datatables/css/rtwalwm-wp-select2.min.css', array(), $this->rtwalwm_version, 'all' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( $this->rtwalwm_plugin_name, plugin_dir_url( __FILE__ ) . 'css/jquery.modal.css', array(), $this->rtwalwm_version, 'all' );
		wp_enqueue_style( "datatable", RTWALWM_URL. '/assets/Datatables/css/jquery.dataTables.min.css', array(), $this->rtwalwm_version, 'all' );
		wp_enqueue_style( "orgchart", RTWALWM_URL. '/assets/orgChart/jquery.orgchart.css', array(), $this->rtwalwm_version, 'all' );
		wp_enqueue_style('font-awesome', RTWALWM_URL. '/assets/Datatables/css/rtwalwm-pro-fontawesome.css', array(), $this->rtwalwm_version, 'all' );


	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function rtwalwm_enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Rtwalwm_Wp_Wc_Affiliate_Program_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rtwalwm_Wp_Wc_Affiliate_Program_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( "datatable", RTWALWM_URL. '/assets/Datatables/js/jquery.dataTables.min.js', array( 'jquery' ), $this->rtwalwm_version, false );

		wp_enqueue_script( "select2", RTWALWM_URL. '/assets/Datatables/js/rtwalwm-wp-select2.min.js', array( 'jquery' ), $this->rtwalwm_version, true );

		wp_enqueue_script( 'iris', admin_url( 'js/iris.min.js' ), array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), $this->rtwalwm_version, true );

		wp_enqueue_script( "blockUI", RTWALWM_URL. '/assets/Datatables/js/rtwalwm-wp-blockui.js', array( 'jquery' ), $this->rtwalwm_version, false );

		wp_enqueue_script( 'wp-color-picker', admin_url( 'js/color-picker.min.js' ), array( 'iris' ), $this->rtwalwm_version, true );

		$rtwalwm_colorpicker_l10n = array(
			'clear' 		=> esc_html__( 'Clear' ),
			'defaultString' => esc_html__( 'Default' ),
			'pick' 			=> esc_html__( 'Select Color' ),
			'current' 		=> esc_html__( 'Current Color' )
		);
		wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $rtwalwm_colorpicker_l10n );
		wp_enqueue_script( 'rtwwap-modal', plugin_dir_url( __FILE__ ) . 'js/jquery.modal.js', array('jquery', 'jquery-ui-accordion'), $this->rtwalwm_version, true );
		//for model		
		wp_register_script( $this->rtwalwm_plugin_name, plugin_dir_url( __FILE__ ) . 'js/rtwalwm-wp-wc-affiliate-program-public.js', array( 'jquery', 'jquery-ui-accordion' ), $this->rtwalwm_version, true );
		$rtwalwm_ajax_nonce 		= wp_create_nonce( "rtwalwm-ajax-security-string" );
		$rtwalwm_whatsapp_device 	= esc_url( 'https://web.whatsapp.com/send?text=' );
		if( wp_is_mobile() ){
			$rtwalwm_whatsapp_device= 'whatsapp://send?text=';
		}
		$rtwalwm_translation_array 	= array(
										'rtwalwm_ajaxurl' 		=> esc_url(admin_url( 'admin-ajax.php' )),
										'rtwalwm_nonce' 		=> $rtwalwm_ajax_nonce,
										'rtwalwm_home_url' 		=> esc_url( home_url() ),
										'rtwalwm_enter_valid_url' => esc_html__( 'Enter valid Link', 'rtwalwm-wp-wc-affiliate-program' ),
										'rtwalwm_whatsapp_url' 	=> $rtwalwm_whatsapp_device,
										'rtwalwm_copied' 		=> esc_html__( 'Copied', 'rtwalwm-wp-wc-affiliate-program' ),
										'rtwalwm_rqst_sure' => esc_html__( 'Are you sure to send the request?', 'rtwalwm-wp-wc-affiliate-program' )
									);
		wp_localize_script( $this->rtwalwm_plugin_name, 'rtwalwm_global_params', $rtwalwm_translation_array );
		wp_enqueue_script( $this->rtwalwm_plugin_name );

		wp_enqueue_script( "qrcode", RTWALWM_URL. '/assets/QrCodeJs/qrcode.min.js', array( 'jquery' ), $this->rtwalwm_version, false );
		wp_enqueue_script( "orgchart", RTWALWM_URL. '/assets/orgChart/jquery.orgchart.js', array( 'jquery' ), $this->rtwalwm_version, false );
		wp_register_script( 'FontAwesome',  RTWALWM_URL. '/assets/Datatables/js/rtwalwm-use-font-awesome.js', array( 'jquery' ), $this->rtwalwm_version, true );

	}

	
	/*
	* function to show under WooCommerce Account
	*/
	function rtwalwm_add_account_menu_item_endpoint(){
	
		add_rewrite_endpoint( 'rtwalwm_affiliate_menu', EP_PAGES );
	}

	/*
	* function to show under WooCommerce Account
	*/
	function rtwalwm_add_account_menu_item( $rtwalwm_menu_links ){
		
		$rtwalwm_new = array( 'rtwalwm_affiliate_menu' => esc_html__( 'Affiliate', 'rtwalwm-wp-wc-affiliate-program' ) );

		$rtwalwm_menu_links = array_slice( $rtwalwm_menu_links, 0, 1, true )
		+ $rtwalwm_new
		+ array_slice( $rtwalwm_menu_links, 1, NULL, true );

		return $rtwalwm_menu_links;
	}
	/*
	*
	*/
	function rtwalwm_add_account_menu_item_endpoint_content( $rtwalwm_url, $rtwalwm_endpoint ){
	
		if( $rtwalwm_endpoint === 'rtwalwm_affiliate_menu' )
		{
			$rtwalwm_page_id = get_option( 'rtwalwm_affiliate_page_id' );

			if( $rtwalwm_page_id ){
				$rtwalwm_url = get_the_permalink( $rtwalwm_page_id );
				return esc_url( $rtwalwm_url.'?rtwalwm_tab=overview' );
			}
		}
		return $rtwalwm_url;
	}
	/**
	 * This function is for front end user to become affiliate
	 */
	function rtwalwm_become_affiliate_callback()
	{
		$rtwalwm_check_ajax = check_ajax_referer( 'rtwalwm-ajax-security-string', 'rtwalwm_security_check' );

		if ( $rtwalwm_check_ajax ) {
			$rtwalwm_user_id 	= sanitize_text_field( $_POST[ 'rtwalwm_user_id' ] );
			$rtwalwm_updated 	= update_user_meta( $rtwalwm_user_id, 'rtwwwap_affiliate', 1 );
			$rtwalwm_aff_approved = 0;
			

			if( $rtwalwm_aff_approved == 0 ){
				update_user_meta( $rtwalwm_user_id, 'rtwwwap_aff_approved', 1 );
				$rtwwwap_mlm = get_option( 'rtwwwap_mlm_opt' );
				if( isset( $rtwwwap_mlm[ 'activate' ] ) && $rtwwwap_mlm[ 'activate' ] == 1 )
				{
					global $wpdb;
					//check if already in MLM chain
					$rtwwwap_already_a_child = $wpdb->get_var( $wpdb->prepare( "SELECT `id` FROM ".$wpdb->prefix."rtwwwap_mlm WHERE `aff_id` = %d", $rtwalwm_user_id ) );

					if( is_null( $rtwwwap_already_a_child  ) ){
						$rtwwwap_allowed_childs = isset( $rtwwwap_mlm[ 'child' ] ) ? $rtwwwap_mlm[ 'child' ] : 1;

						$rtwwwap_parent_id = $wpdb->get_var( $wpdb->prepare( "SELECT `aff_id` FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `signed_up_id` = %d", $rtwalwm_user_id ) );

						if( $rtwwwap_parent_id ){
							$rtwwwap_current_childs = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( `id` ) FROM ".$wpdb->prefix."rtwwwap_mlm WHERE `parent_id` = %d", $rtwwwap_parent_id ) );

							if( $rtwwwap_allowed_childs > $rtwwwap_current_childs ){
								$rtwwwap_updated = 	$wpdb->insert(
											            $wpdb->prefix.'rtwwwap_mlm',
											            array(
											                'aff_id'    	=> $rtwalwm_user_id,
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
											                'aff_id'    	=> $rtwalwm_user_id,
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
			if( $rtwalwm_aff_approved == 1 ){
				update_user_meta( $rtwalwm_user_id, 'rtwwwap_aff_approved', 0 );
			}

			if( $rtwalwm_updated ){
				$rtwalwm_message = esc_html__( 'You are now an affiliate', 'rtwalwm-wp-wc-affiliate-program' );
			}
			else{
				$rtwalwm_message = esc_html__( 'Something went wrong', 'rtwalwm-wp-wc-affiliate-program' );
			}

			echo json_encode( array( 'rtwalwm_status' => $rtwalwm_updated, 'rtwalwm_message' => $rtwalwm_message ) );
			die;
		}
	}
	/*
	* To show affiliate page with shortcode
	*/
	function rtwalwm_affiliate_page_callback(){
		
		$rtwalwm_html = include( RTWALWM_DIR.'public/templates/rtwalwm_affiliate.php' );
		return $rtwalwm_html;
	}
	/*
	* Creates cookie when a affiliate URL is opened
	*/
	function rtwalwm_url_check(){
	
		if( isset( $_GET[ 'rtwalwm_aff' ] ) ){
			$rtwalwm_aff 			= explode( '_', sanitize_text_field($_GET[ 'rtwalwm_aff' ]) );
			$rtwalwm_affiliate_id 	= $rtwalwm_aff[1];
			$rtwalwm_aff_share 		= isset( $rtwalwm_aff[2] ) ? $rtwalwm_aff[2] : 0;

			if( get_user_meta( $rtwalwm_affiliate_id, 'rtwwwap_affiliate', true ) ){
				$rtwalwm_commission_settings 	= get_option( 'rtwwwap_commission_settings_opt' );
				$rtwalwm_commission_type 		= isset( $rtwalwm_commission_settings[ 'only_open_url' ] ) ? $rtwalwm_commission_settings[ 'only_open_url' ] : 0;

				$rtwalwm_prod_id 	= get_the_ID();
				$rtwalwm_cookie_arr = array( "rtwalwm_aff_id" => $rtwalwm_affiliate_id );

				$rtwalwm_cookie_time 	=  0;

				if( $rtwalwm_cookie_time ){
					$rtwalwm_cookie_time = time()+( $rtwalwm_cookie_time * 24 * 60 * 60 );
				}

				if( $rtwalwm_commission_type == 1 ){
					if ( get_post_type( $rtwalwm_prod_id ) == 'product' ) {
						$rtwalwm_cookie_arr[ "rtwalwm_prod_id" ] = $rtwalwm_prod_id;
					}
				}

				if( $rtwalwm_aff_share ){
					$rtwalwm_cookie_arr[ 'share' ] = 'share';
				}

				if( isset( $_COOKIE[ 'rtwalwm_referral' ] ) ){
					unset( $_COOKIE[ 'rtwalwm_referral' ] );
				}

				$rtwalwm_cookie_value = implode( '#', $rtwalwm_cookie_arr );
				setcookie( 'rtwalwm_referral', $rtwalwm_cookie_value, $rtwalwm_cookie_time, '/' );
			}
		}
	}
	/*
	* Creates cookie when a affiliate URL is opened for easy digital downloads 
	*/
	function rtwalwm_url_check_edd(){
		if( isset( $_GET[ 'rtwalwm_aff' ] ) ){
			$rtwalwm_aff 			= explode( '_', sanitize_text_field($_GET[ 'rtwalwm_aff' ])  );
			$rtwalwm_affiliate_id 	= $rtwalwm_aff[1];
			$rtwalwm_aff_share 		= isset( $rtwalwm_aff[2] ) ? $rtwalwm_aff[2] : 0;

			if( get_user_meta( $rtwalwm_affiliate_id, 'rtwwwap_affiliate', true ) ){
				$rtwalwm_commission_settings 	= get_option( 'rtwwwap_commission_settings_opt' );
				$rtwalwm_commission_type 		= isset( $rtwalwm_commission_settings[ 'only_open_url' ] ) ? $rtwalwm_commission_settings[ 'only_open_url' ] : 0;
				$rtwalwm_prod_id 	= get_the_ID();
				$rtwalwm_cookie_arr = array( "rtwalwm_aff_id" => $rtwalwm_affiliate_id );
				$rtwalwm_cookie_time 	=  0 ;
				if( $rtwalwm_cookie_time ){
					$rtwalwm_cookie_time = time()+( $rtwalwm_cookie_time * 24 * 60 * 60 );
					
				}
				if( $rtwalwm_commission_type == 1 ){					
					if ( get_post_type( $rtwalwm_prod_id ) == 'download' ) {
						$rtwalwm_cookie_arr[ "rtwalwm_prod_id" ] = $rtwalwm_prod_id;						
					}
				}

				if( $rtwalwm_aff_share ){
					$rtwalwm_cookie_arr[ 'share' ] = 'share';
				}
				if( isset( $_COOKIE[ 'rtwalwm_referral' ] ) ){
					unset( $_COOKIE[ 'rtwalwm_referral' ] );
				}
				$rtwalwm_cookie_value = implode( '#', $rtwalwm_cookie_arr );
				setcookie( 'rtwalwm_referral', $rtwalwm_cookie_value, (int)$rtwalwm_cookie_time, '/' );
				
			}
		}
			
	}

	/*
	* To create successful referral
	*/

		
	function rtwalwm_referred_item_ordered( $rtwalwm_order_id )
	{ 	
		$rtwalwm_commission_settings = get_option( 'rtwwwap_commission_settings_opt' );
	

		$rtwalwm_cookie = sanitize_text_field( $_COOKIE[ 'rtwalwm_referral' ]);


		global $wpdb;
			
		if( isset($rtwalwm_cookie) ){
			$rtwalwm_referral 	= explode( '#',$rtwalwm_cookie );
			$rtwalwm_order 		= wc_get_order( $rtwalwm_order_id );
			$rtwalwm_comm_base 	= isset( $rtwalwm_commission_settings[ 'comm_base' ] ) ? $rtwalwm_commission_settings[ 'comm_base' ] : '1';

			$rtwalwm_total_commission	= 0;
			$rtwalwm_aff_prod_details 	= array();
			$rtwalwm_user_id 			= esc_html( $rtwalwm_referral[ 0 ] );

			if( RTWALWM_IS_WOO == 1 ){
				$rtwalwm_currency 		= get_woocommerce_currency();
				$rtwalwm_currency_sym 	= get_woocommerce_currency_symbol();
			}
			else{
				$rtwalwm_currency_sym 	= esc_html__( '&#36;', 'rtwalwm-wp-wc-affiliate-program' );

			}
			$rtwalwm_shared 			= strpos( $rtwalwm_cookie, 'share' );
			$rtwalwm_product_url 		= false;

			if( $rtwalwm_comm_base == 1 )
			{

				$rtwalwm_per_prod_mode 			= isset( $rtwalwm_commission_settings[ 'per_prod_mode' ] ) ? $rtwalwm_commission_settings[ 'per_prod_mode' ] : 0;
				$rtwalwm_all_commission 		= isset( $rtwalwm_commission_settings[ 'all_commission' ] ) ? $rtwalwm_commission_settings[ 'all_commission' ] : 0;
				$rtwalwm_all_commission_type 	= isset( $rtwalwm_commission_settings[ 'all_commission_type' ] ) ? $rtwalwm_commission_settings[ 'all_commission_type' ] : 'percentage';
			
				foreach( $rtwalwm_order->get_items() as $rtwalwm_item_key => $rtwalwm_item_values )
				{

					$rtwalwm_prod_comm 		= 0;
					$rtwalwm_product_id 	= $rtwalwm_item_values->get_product_id();
					$rtwalwm_product_price	= $rtwalwm_item_values->get_total();
					$rtwalwm_product_terms 	= get_the_terms( $rtwalwm_product_id, 'product_cat' );
					$rtwalwm_product_cat_id = $rtwalwm_product_terms[0]->term_id;
					if( $rtwalwm_per_prod_mode == 1 )
					{
						$rtwalwm_prod_per_comm = get_post_meta( $rtwalwm_product_id, 'rtwalwm_percentage_commission_box', true );

						if( $rtwalwm_prod_per_comm > 0 ){
							$rtwalwm_prod_comm = ( $rtwalwm_product_price * $rtwalwm_prod_per_comm ) / 100;
							$rtwalwm_aff_prod_details[] = array(
											'product_id' 		=> $rtwalwm_product_id,
											'product_price' 	=> $rtwalwm_product_price,
											'commission_fix' 	=> '',
											'commission_perc' 	=> $rtwalwm_prod_per_comm,
											'prod_commission' 	=> $rtwalwm_prod_comm
										);

							$rtwalwm_total_commission += $rtwalwm_prod_comm;
						}
						elseif( $rtwalwm_prod_per_comm === '0' ){
							// no commission needs to be generated for this product
						}
						else{
							if( !empty( $rtwalwm_per_cat ) ){
								$rtwalwm_cat_per_comm = 0;
								$rtwalwm_cat_fix_comm = 0;
								$rtwalwm_flag = false;
								foreach( $rtwalwm_per_cat as $rtwalwm_key => $rtwalwm_value ){
									if( in_array( $rtwalwm_product_cat_id, $rtwalwm_value[ 'ids' ] ) ){
										$rtwalwm_cat_per_comm = $rtwalwm_value[ 'cat_percentage_commission' ];
										$rtwalwm_cat_fix_comm = $rtwalwm_value[ 'cat_fixed_commission' ];
										$rtwalwm_flag = true;

										break;
									}
								}
								if( $rtwalwm_flag ){
									if( $rtwalwm_cat_per_comm > 0 ){
										$rtwalwm_prod_comm += ( $rtwalwm_product_price * $rtwalwm_cat_per_comm ) / 100;
									}
									if( $rtwalwm_cat_fix_comm > 0 ){
										$rtwalwm_prod_comm += $rtwalwm_cat_fix_comm;
									}

									if( $rtwalwm_prod_comm != '' ){
										$rtwalwm_aff_prod_details[] = array(
													'product_id' 		=> $rtwalwm_product_id,
													'product_price' 	=> $rtwalwm_product_price,
													'commission_fix' 	=> $rtwalwm_cat_fix_comm,
													'commission_perc' 	=> $rtwalwm_cat_per_comm,
													'prod_commission' 	=> $rtwalwm_prod_comm
												);

										$rtwalwm_total_commission += $rtwalwm_prod_comm;
									}
								}
								else{
									if( $rtwalwm_all_commission ){
										if( $rtwalwm_all_commission_type == 'percentage' ){



											$rtwalwm_prod_comm += ( $rtwalwm_product_price * $rtwalwm_all_commission ) / 100;
										}
										elseif( $rtwalwm_all_commission_type == 'fixed' ){
											$rtwalwm_prod_comm += $rtwalwm_all_commission;
										}
										$rtwalwm_aff_prod_details[] = array(
													'product_id' 		=> $rtwalwm_product_id,
													'product_price' 	=> $rtwalwm_product_price,
													'commission_fix' 	=> '',
													'commission_perc' 	=> '',
													'prod_commission' 	=> $rtwalwm_prod_comm
												);

										$rtwalwm_total_commission += $rtwalwm_prod_comm;
									}
								}
							}
							else{
								if( $rtwalwm_all_commission ){
									if( $rtwalwm_all_commission_type == 'percentage' ){
										$rtwalwm_prod_comm += ( $rtwalwm_product_price * $rtwalwm_all_commission ) / 100;
									}
									elseif( $rtwalwm_all_commission_type == 'fixed' ){
										$rtwalwm_prod_comm += $rtwalwm_all_commission;
									}
									$rtwalwm_aff_prod_details[] = array(
												'product_id' 		=> $rtwalwm_product_id,
												'product_price' 	=> $rtwalwm_product_price,
												'commission_fix' 	=> '',
												'commission_perc' 	=> '',
												'prod_commission' 	=> $rtwalwm_prod_comm
											);

									$rtwalwm_total_commission += $rtwalwm_prod_comm;
								}
							}
						}
					}


						elseif( $rtwalwm_per_prod_mode == 2 ){
							$rtwalwm_prod_fix_comm = get_post_meta( $rtwalwm_product_id, 'rtwalwm_fixed_commission_box', true );

							if( $rtwalwm_prod_fix_comm > 0 ){
								$rtwalwm_prod_comm = $rtwalwm_prod_fix_comm;
								$rtwalwm_aff_prod_details[] = array(
												'product_id' 		=> $rtwalwm_product_id,
												'product_price' 	=> $rtwalwm_product_price,
												'commission_fix' 	=> $rtwalwm_prod_fix_comm,
												'commission_perc' 	=> '',
												'prod_commission' 	=> $rtwalwm_prod_comm
											);

								$rtwalwm_total_commission += $rtwalwm_prod_comm;
							}
							elseif( $rtwalwm_prod_fix_comm === '0' ){
								// no commission needs to be generated for this product
							}
							else{
									if( $rtwalwm_all_commission ){
										if( $rtwalwm_all_commission_type == 'percentage' ){
											$rtwalwm_prod_comm += ( $rtwalwm_product_price * $rtwalwm_all_commission ) / 100;
										}
										if( $rtwalwm_all_commission_type == 'fixed' ){
											$rtwalwm_prod_comm += $rtwalwm_all_commission;
										}
										$rtwalwm_aff_prod_details[] = array(
													'product_id' 		=> $rtwalwm_product_id,
													'product_price' 	=> $rtwalwm_product_price,
													'commission_fix' 	=> '',
													'commission_perc' 	=> '',
													'prod_commission' 	=> $rtwalwm_prod_comm
												);

										$rtwalwm_total_commission += $rtwalwm_prod_comm;
									}
								
							}
						}
					
						elseif( $rtwalwm_all_commission ){
							if( $rtwalwm_all_commission_type == 'percentage' ){
								$rtwalwm_prod_comm += ( $rtwalwm_product_price * $rtwalwm_all_commission ) / 100;
							}
							if( $rtwalwm_all_commission_type == 'fixed' ){
								$rtwalwm_prod_comm += $rtwalwm_all_commission;
							}
							$rtwalwm_aff_prod_details[] = array(
										'product_id' 		=> $rtwalwm_product_id,
										'product_price' 	=> $rtwalwm_product_price,
										'commission_fix' 	=> '',
										'commission_perc' 	=> '',
										'prod_commission' 	=> $rtwalwm_prod_comm
									);

							$rtwalwm_total_commission += $rtwalwm_prod_comm;
						}
					}
			}
		}


		// echo '<pre>';
		// print_r($rtwalwm_total_commission);
		// echo '</pre>';
		// die("ffdfdd");
				

		if( isset( $rtwalwm_total_commission ) && $rtwalwm_total_commission !== '' && $rtwalwm_total_commission !== 0 ){
			$rtwalwm_capped 		= 0;
			$rtwalwm_current_year 	= date("Y");
			$rtwalwm_current_month 	= date("m");

		


			// inserting into DB
			if( !empty( $rtwalwm_aff_prod_details ) ){
				

				$rtwalwm_aff_prod_details = json_encode( $rtwalwm_aff_prod_details );
				$rtwalwm_device = ( wp_is_mobile() ) ? 'mobile' : 'desktop';

				$rtwalwm_locale = get_locale();
				setlocale( LC_NUMERIC, $rtwalwm_locale );

				$rtwalwm_updated = $wpdb->insert(
					$wpdb->prefix.'rtwwwap_referrals',
					array(
						'aff_id'    			=> $rtwalwm_user_id,
						'type'    				=> 0,
						'order_id'    			=> esc_html( $rtwalwm_order_id ),
						'date'    				=> date( 'Y-m-d H:i:s' ),
						'status'    			=> 0,
						'amount'    			=> $rtwalwm_total_commission,
						'capped'    			=> esc_html( $rtwalwm_capped ),
						'currency'    			=> $rtwalwm_currency,
						'product_details'   	=> $rtwalwm_aff_prod_details,
						'device'   				=> $rtwalwm_device
					)
				);
				$rtwalwm_lastid = $wpdb->insert_id;

				setlocale( LC_ALL, $rtwalwm_locale );

				if( $rtwalwm_updated ){
					unset( $rtwalwm_cookie);
					$rtwalwm_referral_noti = get_option( 'rtwalwm_referral_noti' )+1;
					update_option( 'rtwalwm_referral_noti', $rtwalwm_referral_noti );
				}


				$rtwwwap_mlm = get_option( 'rtwwwap_mlm_opt' );
				if( isset( $rtwwwap_mlm[ 'activate' ] ) && $rtwwwap_mlm[ 'activate' ] == 1 )
				{
					$rtwwwap_child = isset( $rtwwwap_mlm[ 'child' ] ) ? $rtwwwap_mlm[ 'child' ] : 1;
					$rtwwwap_check_have_child = $this->rtwwwap_check_child_in_mlm( $rtwalwm_user_id, $rtwwwap_child );
					
					$rtwwwap_level_restrict = count($rtwwwap_mlm[ 'mlm_levels' ]);
					if($rtwwwap_level_restrict > 3)
					{
						$rtwwwap_levels = 3;
					}
					else{
						$rtwwwap_levels = $rtwwwap_mlm[ 'mlm_levels' ];
					}

					if( $rtwwwap_check_have_child ){
						$this->rtwwwap_give_mlm_comm( $rtwalwm_user_id, $rtwalwm_lastid, $rtwalwm_total_commission, $rtwalwm_currency, $rtwalwm_currency_sym, $rtwalwm_device, $rtwwwap_levels, $rtwwwap_child, $rtwwwap_order_id );
					}
				}
				
			}
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

		/*
	* To create successful referral for easy digital downloads 
	*/
	function rtwalwm_referred_item_ordered_easy( $rtwalwm_order_id ){ 
	
		$rtwalwm_commission_settings = get_option( 'rtwalwm_commission_settings_opt' );
		
		$rtwalwm_cookie = sanitize_text_field( $_COOKIE[ 'rtwalwm_referral' ]);
	
		if( $rtwalwm_cookie )
		{
			global $wpdb;
			$rtwalwm_referrer_id = 0;
			
			if( isset($rtwalwm_cookie ) ){
				$rtwalwm_referral 	= explode( '#', $rtwalwm_cookie);
				$rtwalwm_order 		= wc_get_order( $rtwalwm_order_id );
				$rtwalwm_comm_base 	= isset( $rtwalwm_commission_settings[ 'comm_base' ] ) ? $rtwalwm_commission_settings[ 'comm_base' ] : '1';
				$rtwalwm_total_commission	= 0;
				$rtwalwm_aff_prod_details 	= array();
				$rtwalwm_user_id 			= esc_html( $rtwalwm_referral[ 0 ] );

				if( RTWALWM_IS_WOO == 1 ){
					$rtwalwm_currency 		= get_woocommerce_currency();
					$rtwalwm_currency_sym 	= get_woocommerce_currency_symbol();
				}
				else{
					$rtwalwm_currency_sym 	= esc_html__( '&#36;', 'rtwalwm-wp-wc-affiliate-program' );

				}
				$rtwalwm_shared 			= strpos( $rtwalwm_cookie, 'share' );
				$rtwalwm_product_url 		= false;

				if( $rtwalwm_comm_base == 1 ){
					$rtwalwm_per_prod_mode 			= isset( $rtwalwm_commission_settings[ 'per_prod_mode' ] ) ? $rtwalwm_commission_settings[ 'per_prod_mode' ] : 0;
					$rtwalwm_all_commission 		= isset( $rtwalwm_commission_settings[ 'all_commission' ] ) ? $rtwalwm_commission_settings[ 'all_commission' ] : 0;
					$rtwalwm_all_commission_type 	= isset( $rtwalwm_commission_settings[ 'all_commission_type' ] ) ? $rtwalwm_commission_settings[ 'all_commission_type' ] : 'percentage';

				foreach( $rtwalwm_order->cart_details as $rtwalwm_item_key => $rtwalwm_item_values )
					{
						$rtwalwm_prod_comm 		= '';
						$rtwalwm_product_id 	= $rtwalwm_item_values->get_product_id();
						$rtwalwm_product_price	= $rtwalwm_item_values->get_total();
						$rtwalwm_product_terms 	= get_the_terms( $rtwalwm_product_id, 'product_cat' );
						$rtwalwm_product_cat_id = $rtwalwm_product_terms[0]->term_id;

							if( $rtwalwm_per_prod_mode == 2 ){
								$rtwalwm_prod_fix_comm = get_post_meta( $rtwalwm_product_id, 'rtwalwm_fixed_commission_box', true );

								if( $rtwalwm_prod_fix_comm > 0 ){
									$rtwalwm_prod_comm = $rtwalwm_prod_fix_comm;
									$rtwalwm_aff_prod_details[] = array(
													'product_id' 		=> $rtwalwm_product_id,
													'product_price' 	=> $rtwalwm_product_price,
													'commission_fix' 	=> $rtwalwm_prod_fix_comm,
													'commission_perc' 	=> '',
													'prod_commission' 	=> $rtwalwm_prod_comm
												);

									$rtwalwm_total_commission += $rtwalwm_prod_comm;
								}
								elseif( $rtwalwm_prod_fix_comm === '0' ){
									// no commission needs to be generated for this product
								}
								else{
										if( $rtwalwm_all_commission ){
										
											if( $rtwalwm_all_commission_type == 'fixed' ){
												$rtwalwm_prod_comm += $rtwalwm_all_commission;
											}
											$rtwalwm_aff_prod_details[] = array(
														'product_id' 		=> $rtwalwm_product_id,
														'product_price' 	=> $rtwalwm_product_price,
														'commission_fix' 	=> '',
														'commission_perc' 	=> '',
														'prod_commission' 	=> $rtwalwm_prod_comm
													);

											$rtwalwm_total_commission += $rtwalwm_prod_comm;
										}
									
								}
							}
						
							elseif( $rtwalwm_all_commission ){
							
								if( $rtwalwm_all_commission_type == 'fixed' ){
									$rtwalwm_prod_comm += $rtwalwm_all_commission;
								}
								$rtwalwm_aff_prod_details[] = array(
											'product_id' 		=> $rtwalwm_product_id,
											'product_price' 	=> $rtwalwm_product_price,
											'commission_fix' 	=> '',
											'commission_perc' 	=> '',
											'prod_commission' 	=> $rtwalwm_prod_comm
										);

								$rtwalwm_total_commission += $rtwalwm_prod_comm;
							}
						}
					}
				}
				

				if( isset( $rtwalwm_total_commission ) && $rtwalwm_total_commission !== '' && $rtwalwm_total_commission !== 0 ){
					$rtwalwm_capped 		= 0;
					$rtwalwm_current_year 	= date("Y");
					$rtwalwm_current_month 	= date("m");

				


					// inserting into DB
					if( !empty( $rtwalwm_aff_prod_details ) ){
						

						$rtwalwm_aff_prod_details = json_encode( $rtwalwm_aff_prod_details );
						$rtwalwm_device = ( wp_is_mobile() ) ? 'mobile' : 'desktop';

						$rtwalwm_locale = get_locale();
						setlocale( LC_NUMERIC, $rtwalwm_locale );

						$rtwalwm_updated = $wpdb->insert(
							$wpdb->prefix.'rtwwwap_referrals',
							array(
								'aff_id'    			=> $rtwalwm_user_id,
								'type'    				=> 0,
								'order_id'    			=> esc_html( $rtwalwm_order_id ),
								'date'    				=> date( 'Y-m-d H:i:s' ),
								'status'    			=> 0,
								'amount'    			=> $rtwalwm_total_commission,
								'capped'    			=> esc_html( $rtwalwm_capped ),
								'currency'    			=> $rtwalwm_currency,
								'product_details'   	=> $rtwalwm_aff_prod_details,
								'device'   				=> $rtwalwm_device
							)
						);
						$rtwalwm_lastid = $wpdb->insert_id;

						setlocale( LC_ALL, $rtwalwm_locale );

						if( $rtwalwm_updated ){
							unset( $rtwalwm_cookie);
							$rtwalwm_referral_noti = get_option( 'rtwalwm_referral_noti' )+1;
							update_option( 'rtwalwm_referral_noti', $rtwalwm_referral_noti );
						}


						$rtwwwap_mlm = get_option( 'rtwwwap_mlm_opt' );
						if( isset( $rtwwwap_mlm[ 'activate' ] ) && $rtwwwap_mlm[ 'activate' ] == 1 )
						{
							$rtwwwap_child = isset( $rtwwwap_mlm[ 'child' ] ) ? $rtwwwap_mlm[ 'child' ] : 1;
							$rtwwwap_check_have_child = $this->rtwwwap_check_child_in_mlm( $rtwalwm_user_id, $rtwwwap_child );
							
							if( $rtwwwap_check_have_child ){
								$this->rtwwwap_give_mlm_comm( $rtwalwm_user_id, $rtwalwm_lastid, $rtwalwm_total_commission, $rtwalwm_currency, $rtwalwm_currency_sym, $rtwalwm_device, $rtwwwap_mlm[ 'mlm_levels' ], $rtwwwap_child, $rtwwwap_order_id );
							}
						}

						
					}
				}
			}
		}	
	
		function rtwwwap_give_mlm_comm( $rtwwwap_user_id, $rtwwwap_lastid, $rtwwwap_total_commission, $rtwwwap_currency, $rtwwwap_currency_sym, $rtwwwap_device, $rtwwwap_mlm_levels, $rtwwwap_childs_to_start, $rtwalwm_order_id )
	{
		if( !empty( $rtwwwap_mlm_levels ) )
		{
			foreach( $rtwwwap_mlm_levels as $rtwwwap_mlm_key => $rtwwwap_mlm_value ){
				$rtwwwap_parent_id = $this->rtwwwap_check_child_in_mlm( $rtwwwap_user_id, $rtwwwap_childs_to_start );

				if( $rtwwwap_parent_id ){
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
						$rtwwwap_subject 		= esc_html__( 'New MLM commission', 'rtwalwm-wp-wc-affiliate-program' );
						$rtwwwap_message 		= sprintf( '%s %s%01.'.$rtwwwap_decimal_places, esc_html__( 'You got a new MLM commission of amount', 'rtwalwm-wp-wc-affiliate-program' ), $rtwwwap_currency_sym, $rtwwwap_commission );
						$rtwwwap_from_name 		= esc_html( get_bloginfo( 'name' ) );
						$rtwwwap_from_email 	= esc_html( get_bloginfo( 'admin_email' ) );

						$rtwwwap_headers[] 		= sprintf( '%s: %s <%s>', esc_html__( 'From', 'rtwalwm-wp-wc-affiliate-program' ), $rtwwwap_from_name, $rtwwwap_from_email );

						// mail to affiliate
						wp_mail( $rtwwwap_to, $rtwwwap_subject, $rtwwwap_message, $rtwwwap_headers );

						if( isset( $rtwwwap_extra_features[ 'mail_to_admin' ] ) && $rtwwwap_extra_features[ 'mail_to_admin' ] == 1 ){
							// mail to admin
							$rtwwwap_message = sprintf( '%s %s%01.'.$rtwwwap_decimal_places, esc_html__( 'Generated a new MLM commission of amount', 'rtwalwm-wp-wc-affiliate-program' ), $rtwwwap_currency_sym, $rtwwwap_commission );
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

	/*
	* To Search products for creating banner
	*/
	function rtwalwm_search_prod_callback(){
		$rtwalwm_check_ajax = check_ajax_referer( 'rtwalwm-ajax-security-string', 'rtwalwm_security_check' );

		if ( $rtwalwm_check_ajax ) {
			$rtwalwm_prod_name 	= sanitize_text_field( $_POST[ 'rtwalwm_prod_name' ] );
			$rtwalwm_cat_id 	= sanitize_text_field( $_POST[ 'rtwalwm_cat_id' ] );

			global $wpdb;
			$rtwalwm_wild = '%';
			$rtwalwm_like = $rtwalwm_wild . $wpdb->esc_like( $rtwalwm_prod_name ) . $rtwalwm_wild;
			if(RTWALWM_IS_WOO)
			{
				$rtwalwm_post_type = 'product';
			}
			else
			{
				$rtwalwm_post_type = 'download';
			}
		
			$rtwalwm_query = $wpdb->prepare( "SELECT * FROM ".$wpdb->posts." JOIN ".$wpdb->term_relationships." ON ".$wpdb->posts.".`ID` = ".$wpdb->term_relationships.".`object_id` JOIN ".$wpdb->term_taxonomy." ON ".$wpdb->term_relationships.".`term_taxonomy_id` = ".$wpdb->term_taxonomy.".`term_taxonomy_id` WHERE ".$wpdb->posts.".`post_title` LIKE %s AND ".$wpdb->posts.".`post_type` LIKE '".$rtwalwm_post_type."' AND ".$wpdb->term_taxonomy.".`term_id` =%d", $rtwalwm_like, $rtwalwm_cat_id );
			$rtwalwm_prods = $wpdb->get_results( $rtwalwm_query, ARRAY_A );
			
			$rtwalwm_html = '';
			

			if( !empty( $rtwalwm_prods ) ){
				if( RTWALWM_IS_WOO == 1 ){
					$rtwalwm_currency 		= get_woocommerce_currency();
					$rtwalwm_currency_sym 	= get_woocommerce_currency_symbol();
				}
				else{
					$rtwalwm_currency_sym 	= esc_html__( '&#36;', 'rtwalwm-wp-wc-affiliate-program' );
				}

				foreach( $rtwalwm_prods as $rtwalwm_key => $rtwalwm_value ){
					$rtwalwm_img_url 	= wp_get_attachment_image_src( get_post_thumbnail_id( $rtwalwm_value[ 'ID' ] ), 'full' );
					$rtwalwm_prod_url 	= get_permalink( $rtwalwm_value[ 'ID' ], false );
					
					if(RTWALWM_IS_Easy == 1){

							$rtwalwm_prod_price = new EDD_Download( $rtwalwm_value[ 'ID' ] );
							
							$rtwalwm_html .= 	'<div class="rtwalwm_searched_prod">';
							$rtwalwm_html .= 		'<img src="'.esc_url( $rtwalwm_img_url[0] ).'" class="rtwalwm_prod_img" alt="">';
							$rtwalwm_html .= 		'<div class="rtwalwm_inner">';
							$rtwalwm_html .= 			'<div>';
							$rtwalwm_html .= 				'<p class="rtwalwm_prod_name">'.$rtwalwm_value[ 'post_title' ].'</p>';
							$rtwalwm_html .= 				'<p class="rtwalwm_prod_price">'.$rtwalwm_currency_sym.$rtwalwm_prod_price->price.'</p>';
							$rtwalwm_html .= 			'</div>';
							$rtwalwm_html .= 			'<p data-rtwalwm_id="'.esc_attr( $rtwalwm_value[ 'ID' ] ).'" data-rtwalwm_title="'.esc_attr( $rtwalwm_value[ 'post_title' ] ).'" data-rtwalwm_url="'.esc_attr( esc_url( $rtwalwm_prod_url ) ).'" data-rtwalwm_displayprice="'.esc_attr( $rtwalwm_prod_price->price ).'" data-rtwalwm_image="'.esc_attr( $rtwalwm_img_url[0] ).'" >';
							$rtwalwm_html .= 				'<input type="button" id="rtwalwm_create_link" value="'.esc_attr__( "Link", "rtwalwm-wp-wc-affiliate-program" ).'" disabled/>';
							$rtwalwm_html .= 				'<input type="button" id="rtwalwm_create_banner" value="'.esc_attr__( "Banner", "rtwalwm-wp-wc-affiliate-program" ).'" disabled/>';
							$rtwalwm_html .= 			'</p>';
							$rtwalwm_html .= 		'</div>';
							$rtwalwm_html .= 	'</div>';

					}
					if(RTWALWM_IS_WOO == 1){

						$rtwalwm_prod_price = new WC_Product( $rtwalwm_value[ 'ID' ] );	
						$rtwalwm_html .= 	'<div class="rtwalwm_searched_prod">';
						$rtwalwm_html .= 		'<img src="'.esc_url( $rtwalwm_img_url[0] ).'" class="rtwalwm_prod_img" alt="">';
						$rtwalwm_html .= 		'<div class="rtwalwm_inner">';
						$rtwalwm_html .= 			'<div>';
						$rtwalwm_html .= 				'<p class="rtwalwm_prod_name">'.$rtwalwm_value[ 'post_title' ].'</p>';
						$rtwalwm_html .= 				'<p class="rtwalwm_prod_price">'.$rtwalwm_prod_price->get_price_html().'</p>';
						$rtwalwm_html .= 			'</div>';
						$rtwalwm_html .= 			'<p data-rtwalwm_id="'.esc_attr( $rtwalwm_value[ 'ID' ] ).'" data-rtwalwm_title="'.esc_attr( $rtwalwm_value[ 'post_title' ] ).'" data-rtwalwm_url="'.esc_attr( esc_url( $rtwalwm_prod_url ) ).'" data-rtwalwm_displayprice="'.esc_attr( $rtwalwm_prod_price->get_price_html() ).'" data-rtwalwm_image="'.esc_attr( $rtwalwm_img_url[0] ).'" >';
						$rtwalwm_html .= 				'<input type="button" id="rtwalwm_create_link" value="'.esc_attr__( "Link", "rtwalwm-wp-wc-affiliate-program" ).'" disabled/>';
						$rtwalwm_html .= 				'<input type="button" id="rtwalwm_create_banner" value="'.esc_attr__( "Banner", "rtwalwm-wp-wc-affiliate-program" ).'" disabled/>';
						$rtwalwm_html .= 			'</p>';
						$rtwalwm_html .= 		'</div>';
						$rtwalwm_html .= 	'</div>';
					}
					
				}
			}

			if( empty( $rtwalwm_prods ) ){
				$rtwalwm_message = esc_html__( 'No Result Found', 'rtwalwm-wp-wc-affiliate-program' );
			}

			


			echo json_encode( array( 'rtwalwm_products' => $rtwalwm_html, 'rtwalwm_message' => $rtwalwm_message ) );
			die;
		}
	}

	/*
	To show affiliate login page with shortcode
	*/
	function rtwalwm_aff_login_page_callback(){

		$rtwalwm_html = include( RTWALWM_DIR.'public/templates/rtwalwm_aff_login_page.php' );
		return $rtwalwm_html;

	}

	function rtwalwm_login_fail_redirect($redirect_to, $requested_redirect_to, $user)
	{
		$rtwalwm_affiliate_page_id = get_option('rtwalwm_affiliate_page_id');
		$referrer = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : $_SERVER['PHP_SELF'];
		if( !empty($rtwalwm_affiliate_page_id) )
		{
			$redirect_url = get_permalink($rtwalwm_affiliate_page_id);
			if (is_wp_error($user) && !empty($referrer) &&  strstr($referrer, get_permalink($rtwalwm_affiliate_page_id))  )
			{
				$redirect_url = add_query_arg('login_errors', urlencode(wp_kses($user->get_error_message(), array('strong' => array(0)))), $redirect_url);
				wp_redirect($redirect_url);
			}
		}
		return $redirect_to;
	}

	//apply Coupon

	function apply_coupon($coupon_code)
		{
			global $woocommerce;
			$coupon_code = wc_format_coupon_code( $coupon_code );
			$the_coupon = new WC_Coupon( $coupon_code );
			$the_coupon_id = $the_coupon->get_id();
			$rtwalwm_aff_id = get_post_meta( $the_coupon_id, 'rtwalwm_coupon_aff_id');
			if($rtwalwm_aff_id )
			{
				$this->the_coupon_id_set_cookies($rtwalwm_aff_id[0]);
			}
		}

		function the_coupon_id_set_cookies($rtwalwm_aff_id)
		{
			if(isset( $_COOKIE[ 'rtwalwm_referral' ] ))
			{
				unset( $_COOKIE[ 'rtwalwm_referral' ] );
			}
			else
			{
				$rtwalwm_cookie_time = 0;
				if( $rtwalwm_cookie_time )
				{
					$rtwalwm_cookie_time = time()+( $rtwalwm_cookie_time * 24 * 60 * 60 );
				}
				setcookie( 'rtwalwm_referral', $rtwalwm_aff_id, $rtwalwm_cookie_time, '/' );
			}
		}

		function remove_coupon($coupon_code){
			global $woocommerce;
			$coupon_code = wc_format_coupon_code( $coupon_code );
			$the_coupon = new WC_Coupon( $coupon_code );
			$the_coupon_id = $the_coupon->get_id();
			$rtwalwm_aff_id = get_post_meta( $the_coupon_id, 'rtwalwm_coupon_aff_id');
			if($rtwalwm_aff_id )
			{
				if($rtwalwm_aff_id[0])
				{
					$this->remove_coupon_id_unset_cookie($rtwalwm_aff_id[0]);
				}
			}	
		}

		function remove_coupon_id_unset_cookie($rtwalwm_aff_id)
		{
			$current_user_id=get_current_user_id();	
			$rtwalwm_cookie_time = 0;		
			if($current_user_id)
			{
				// unset($_COOKIE['rtwalwm_referral']); 
				setcookie( 'rtwalwm_referral', $current_user_id, time() + (86400 * 30), "/");
			}
			else
			{
				if (isset($_COOKIE['rtwalwm_referral'])) 
				{		
					unset($_COOKIE['rtwalwm_referral']); 
					setcookie('rtwalwm_referral', null, -1, '/');
				}
			}	
		}

}
	




	


