<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.redefiningtheweb.com
 * @since      1.0.0
 *
 * @package    Rtwalwm_Wp_Wc_Affiliate_Program
 * @subpackage Rtwalwm_Wp_Wc_Affiliate_Program/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rtwalwm_Wp_Wc_Affiliate_Program
 * @subpackage Rtwalwm_Wp_Wc_Affiliate_Program/admin
 * @author     RedefiningTheWeb <developer@redefiningtheweb.com>
 */
class Rtwalwm_Wp_Wc_Affiliate_Program_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $rtwalwm_plugin_name    The ID of this plugin.
	 */
	private $rtwalwm_plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $rtwalwm_version    The current version of this plugin.
	 */
	private $rtwalwm_version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $rtwalwm_plugin_name       The name of this plugin.
	 * @param      string    $rtwalwm_version    The version of this plugin.
	 */
	public function __construct( $rtwalwm_plugin_name, $rtwalwm_version ) {

		$this->rtwalwm_plugin_name = $rtwalwm_plugin_name;
		$this->rtwalwm_version = $rtwalwm_version;

	}

	/**
	 * Register the stylesheets for the admin area.
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
		wp_enqueue_media();

		$rtwalwm_css_allowed_pages = array(
										'edit-product',
										'toplevel_page_rtwalwm',
										'product',
										'user-edit',
										'users',
										'user',
										'profile',
										'download'
									);

		$rtwalwm_screen 	= function_exists( 'get_current_screen' ) ? get_current_screen() : '';
		$rtwalwm_screen_id 	= ( isset( $rtwalwm_screen->id ) ) ? $rtwalwm_screen->id : '';

		if( in_array( $rtwalwm_screen_id, $rtwalwm_css_allowed_pages ) ){

			wp_enqueue_style( $this->rtwalwm_plugin_name, plugin_dir_url( __FILE__ ) . 'css/rtwalwm-wp-wc-affiliate-program-admin.css', array(), $this->rtwalwm_version, 'all' );
			wp_enqueue_style( "banner-css", plugin_dir_url( __FILE__ ) . 'css/rtwalwm-wp-wc-affiliate-banner.css', array(), $this->rtwalwm_version, 'all' );
			wp_enqueue_style( "select2", RTWALWM_URL. '/assets/Datatables/css/rtwalwm-wp-select2.min.css', array(), $this->rtwalwm_version, 'all' );

			wp_enqueue_style( "datatable", RTWALWM_URL. '/assets/Datatables/css/jquery.dataTables.min.css', array(), $this->rtwalwm_version, 'all' );
			wp_enqueue_style( "rowReorder", RTWALWM_URL. '/assets/Datatables/css/rowReorder.dataTables.min.css', array( 'datatable' ), $this->rtwalwm_version, 'all' );
			wp_enqueue_style( "jquery-ui", RTWALWM_URL. '/assets/Datatables/css/rtwalwm-jquery-ui.css', array(), $this->rtwalwm_version, 'all' );
			wp_enqueue_style( "orgchart", RTWALWM_URL. '/assets/orgChart/jquery.orgchart.css', array(), $this->rtwalwm_version, 'all' );
			wp_enqueue_style('font-awesome', RTWALWM_URL. '/assets/Datatables/css/rtwalwm-pro-fontawesome.css', array(), $this->rtwalwm_version, 'all' );

			
		}

	}

	/**
	 * Register the JavaScript for the admin area.
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
		$rtwalwm_js_allowed_pages = array(
										'product',
										'edit-product',
										'toplevel_page_rtwalwm',
										'users',
										'user-edit',
										'user',
										'profile',
										'edit-download'
									);

		$rtwalwm_screen 	= function_exists( 'get_current_screen' ) ? get_current_screen() : '';
		$rtwalwm_screen_id 	= ( isset( $rtwalwm_screen->id ) ) ? $rtwalwm_screen->id : '';

		if( in_array( $rtwalwm_screen_id, $rtwalwm_js_allowed_pages ) ){
			
			wp_enqueue_script( "datatable", RTWALWM_URL. '/assets/Datatables/js/jquery.dataTables.min.js', array( 'jquery' ), $this->rtwalwm_version, false );
			wp_enqueue_script( "rowReorder", RTWALWM_URL. '/assets/Datatables/js/dataTables.rowReorder.min.js', array( 'jquery', 'datatable' ), $this->rtwalwm_version, false );
			wp_enqueue_script( "select2", RTWALWM_URL. '/assets/Datatables/js/rtwalwm-wp-select2.min.js', array( 'jquery' ), $this->rtwalwm_version, false );

			wp_enqueue_script( 'jquery-ui-dialog', '', array( 'jquery' ), $this->rtwalwm_version, false );
			wp_enqueue_script( "blockUI", RTWALWM_URL. '/assets/Datatables/js/rtwalwm-wp-blockui.js', array( 'jquery' ), $this->rtwalwm_version, false );

			wp_register_script( $this->rtwalwm_plugin_name, plugin_dir_url( __FILE__ ) . 'js/rtwalwm-wp-wc-affiliate-program-admin.js', array( 'jquery', 'select2', 'datatable', 'rowReorder' ), $this->rtwalwm_version, false );
			
			

			wp_enqueue_script( "orgchart", RTWALWM_URL. '/assets/orgChart/jquery.orgchart.js', array( 'jquery' ), $this->rtwalwm_version, false );
			wp_register_script( 'FontAwesome',  RTWALWM_URL. '/assets/Datatables/js/rtwalwm-use-font-awesome.js', array( 'jquery' ), $this->rtwalwm_version, true );

			wp_enqueue_script( 'wp-color-picker', admin_url( 'js/color-picker.min.js' ), array( 'iris' ), $this->rtwalwm_version, true );
			$rtwalwm_colorpicker_l10n = array(
		        'clear' 		=> esc_html__( 'Clear' ),
		        'defaultString' => esc_html__( 'Default' ),
		        'pick' 			=> esc_html__( 'Select Color' ),
		        'current' 		=> esc_html__( 'Current Color' )
		    );
		    wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $rtwalwm_colorpicker_l10n );

			$rtwalwm_ajax_nonce 		= wp_create_nonce( "rtwalwm-ajax-security-string" );
			$rtwalwm_translation_array 	= array(
												'rtwalwm_ajaxurl' 	=> esc_url( admin_url( 'admin-ajax.php' ) ),
												'rtwalwm_nonce' 	=> $rtwalwm_ajax_nonce,
												'rtwalwm_digit' 	=> esc_html__( 'Digits Only', 'rtwalwm-wp-wc-affiliate-program' ),
												'rtwalwm_bank_det' 	=> esc_html__( 'Details not filled', 'rtwalwm-wp-wc-affiliate-program' ),
												'rtwalwm_bank_sent' => esc_html__( 'Are you sure that you have completed this bank transfer?', 'rtwalwm-wp-wc-affiliate-program' ),
												'rtwalwm_nothing_marked' => esc_html__( 'Nothing Marked', 'rtwalwm-wp-wc-affiliate-program' ),
												'rtwalwm_approval_sure' => esc_html__( 'Are you sure to approve this Referral? It can\'t be reverted back once approved', 'rtwalwm-wp-wc-affiliate-program' ),
												'rtwalwm_approval_sure_all' => esc_html__( 'Are you sure to approve all the Referrals? It can\'t be reverted back once approved', 'rtwalwm-wp-wc-affiliate-program' ),
												'rtwalwm_reject_sure' 		=> esc_html__( 'Are you sure to reject this Referral? It can\'t be reverted back once rejected', 'rtwalwm-wp-wc-affiliate-program' ),
												'rtwalwm_reject_message_blank' 		=> esc_html__( 'Please input some text in message box', 'rtwalwm-wp-wc-affiliate-program' ),
												'rtwalwm_reject_sure_all' 	=> esc_html__( 'Are you sure to reject all the Referrals? It can\'t be reverted back once rejected', 'rtwalwm-wp-wc-affiliate-program' ),
												'rtwalwm_target_link'  => esc_html__( 'Please Enter Target URL', 'rtwalwm-wp-wc-affiliate-program' ),
												'rtwalwm_image_id'  => esc_html__( 'Please Select Image', 'rtwalwm-wp-wc-affiliate-program' ),
												'rtwalwm_image_parameter_not_match'  => esc_html__( 'Image width or height is not matching with selected Image WIDTH x HEIGHT', 'rtwalwm-wp-wc-affiliate-program' )
											
											);
			wp_localize_script( $this->rtwalwm_plugin_name, 'rtwalwm_global_params', $rtwalwm_translation_array );
			wp_enqueue_script( $this->rtwalwm_plugin_name );
			wp_enqueue_script( 'jquery.validate', plugin_dir_url( __FILE__ ) . 'js/jquery.validate/jquery.validate.js', array( 'jquery' ), $this->rtwalwm_version, false );

		}

	}



	


	/*
	* Function to show settings link
	*/
	function rtwalwm_add_setting_links( $rtwalwm_links ){
		$rtwalwm_links[] = '<a href="' . admin_url( 'admin.php?page=rtwalwm' ) . '">'.esc_html__( 'Settings', 'rtwalwm-wp-wc-affiliate-program' ).'</a>';
		return $rtwalwm_links;
	}

	/*
	* Function to add submenu in WooCommerce menu tab
	*/
	function rtwalwm_add_submenu()
	{
		$rtwalwm_menu_position = '80';
		add_menu_page( esc_html__( 'Affiliaa - WordPress & WooCommerce Affiliate Program', 'rtwalwm-wp-wc-affiliate-program' ), esc_html__( 'Affiliaa Lite', 'rtwalwm-wp-wc-affiliate-program' ), 'manage_options', 'rtwalwm', array( $this, 'rtwalwm_admin_setting' ), RTWALWM_URL.'assets/images/affiliate-menu-icon.png', $rtwalwm_menu_position );
	}

	function rtwalwm_admin_setting()
	{
		include_once( RTWALWM_DIR.'admin/partials/rtwalwm-wp-wc-affiliate-program-admin-display.php');
	}

	/*
	* Function to register settings
	*/
	function rtwalwm_settings_init()
	{
		register_setting( 'rtwwwap_commission_settings', 'rtwwwap_commission_settings_opt', array( $this, 'rtwwwap_save_comm' ) );
		register_setting( 'rtwwwap_extra_features', 'rtwwwap_extra_features_opt', array( $this, 'rtwwwap_save_extra' ) );
		register_setting( 'rtwwwap_mlm', 'rtwwwap_mlm_opt', array( $this, 'rtwwwap_save_mlm' ) );
	
	}

	/*
	* Function to save commission settings
	*/
	function rtwalwm_save_comm( $rtwalwm_option )
	{
		$rtwalwm_option[ 'per_cat' ] = array();
		$i = 0;

		while( isset( $rtwalwm_option[ "per_cat_$i" ] ) )
		{
			
			$rtwalwm_fix_comm 	= $rtwalwm_option[ "per_cat_$i" ][ 'cat_fixed_commission' ];

			
			unset( $rtwalwm_option[ "per_cat_$i" ][ 'cat_fixed_commission' ] );

			$rtwalwm_ids = $rtwalwm_option[ "per_cat_$i" ];
			unset( $rtwalwm_option[ "per_cat_$i" ] );

			$rtwalwm_option[ "per_cat" ][ $i ] = array(
													'ids' 						=> $rtwalwm_ids,
													'cat_percentage_commission' => $rtwalwm_perc_comm,
													'cat_fixed_commission' 		=> $rtwalwm_fix_comm,
												);

			$i++;
		}

		return $rtwalwm_option;
	}

	/*
	* Function to save extra settings
	*/
	function rtwalwm_save_extra( $rtwalwm_option )
	{
		// to add affiliate page id in options table
		$rtwalwm_aff_page_id = $rtwalwm_option['page'];
	

		if( $rtwalwm_aff_page_id ){
			update_option( 'rtwalwm_affiliate_page_id', $rtwalwm_aff_page_id );
		}
			;
		

		unset( $rtwalwm_option['page'] );
	
		
		return $rtwalwm_option;
	}

	function rtwwwap_save_mlm( $rtwwwap_option )
	{
		unset( $rtwwwap_option[ 'mlm_levels' ][ 'mlm_level_comm_type' ] );
		unset( $rtwwwap_option[ 'mlm_levels' ][ 'mlm_level_comm_amount' ] );

		return $rtwwwap_option;
	}

	/*
	* Function to update thead of users table
	*/
	function rtwalwm_add_affiliate_column( $rtwalwm_columns )
	{
		if( current_user_can( 'manage_options' ) ){
			$rtwalwm_columns[ 'rtwalwm_affiliate' ] = esc_attr__( 'Affiliate', 'rtwalwm-wp-wc-affiliate-program' );
			$rtwalwm_commission_settings 	= get_option( 'rtwwwap_commission_settings_opt' );
			$rtwalwm_comm_base 				= isset( $rtwalwm_commission_settings[ 'comm_base' ] ) ? $rtwalwm_commission_settings[ 'comm_base' ] : '1';

			if( $rtwalwm_comm_base == 2 ){
				$rtwalwm_levels_settings = get_option( 'rtwalwm_levels_settings_opt' );
				if( !empty( $rtwalwm_levels_settings ) ){
					$rtwalwm_columns[ 'rtwalwm_affiliate_level' ] = esc_attr__( 'Affiliate Level', 'rtwalwm-wp-wc-affiliate-program' );
				}
			}
		}
		return $rtwalwm_columns;
	}

	/*
	* Function to update tbody of users table
	*/
	function rtwalwm_manage_affiliate_column( $rtwalwm_empty = '', $rtwalwm_column='', $rtwalwm_user_id='')
	{

		$rtwalwm_user_aff = get_user_meta( $rtwalwm_user_id, 'rtwwwap_affiliate', true );
		
		if( $rtwalwm_column == 'rtwalwm_affiliate' ){

			if (RTWALWM_IS_WOO == 1)
			{
				$rtwalwm_check_col = woocommerce_form_field('rtwalwm_affiliate', array(
						'type'          	=> 'checkbox',
						'input_class'       => array( 'rtwalwm_affiliate_checkbox'),
						'custom_attributes' => array( 'data-rtwalwm-num' => $rtwalwm_user_id ),
						'required' 			=> false,
						'return' 			=> true
					),get_user_meta( $rtwalwm_user_id, 'rtwwwap_affiliate', true ));
			}	
			if (RTWALWM_IS_Easy == 1)
			{
				$rtwalwm_check_col='';
				$rtwalwm_check_col .= "<p class='form-row' data-priority=''>";
				$rtwalwm_check_col .= "<span>";
				$rtwalwm_check_col .= "<label class='checkbox' data-rtwalwm-num='".esc_attr($rtwalwm_user_id)."'>";
				if($rtwalwm_user_aff == 1)
				{
				$rtwalwm_check_col .= "<input type='checkbox' class='input-checkbox rtwalwm_affiliate_checkbox' name='rtwalwm_affiliate' id='rtwalwm_affiliate' value='".esc_attr($rtwalwm_user_aff)."' checked >";
				}else
				{
					$rtwalwm_check_col .= "<input type='checkbox' class='input-checkbox rtwalwm_affiliate_checkbox' name='rtwalwm_affiliate' id='rtwalwm_affiliate' value='".esc_attr($rtwalwm_user_aff)."' unchecked >";	
				}
				
				$rtwalwm_check_col .= "<span class='optional'>(optional)</span>";
				$rtwalwm_check_col .= "</label>";
				$rtwalwm_check_col .= "</span>";
				$rtwalwm_check_col .= "</p>";
			}	
		
			return $rtwalwm_check_col;
			
		}

		
		return $rtwalwm_empty;
	}

	function rtwalwm_change_affiliate_callback()
	{
		$rtwalwm_check_ajax = check_ajax_referer( 'rtwalwm-ajax-security-string', 'rtwalwm_security_check' );

		if ( $rtwalwm_check_ajax ) {
			if( current_user_can( 'manage_options' ) )
			{
				$rtwalwm_user_id 	= sanitize_text_field( $_POST[ 'rtwalwm_user_id' ] );
				$rtwalwm_value 		= sanitize_text_field( $_POST[ 'rtwalwm_value' ] );
				$rtwalwm_updated 	= update_user_meta( $rtwalwm_user_id, 'rtwwwap_affiliate', $rtwalwm_value );
				update_user_meta( $rtwalwm_user_id, 'rtwwwap_aff_approved', $rtwalwm_value );

				if( $rtwalwm_updated && $rtwalwm_value ){
					$rtwalwm_message = esc_html__( 'This user is an affiliate now', 'rtwalwm-wp-wc-affiliate-program' );
				}
				if( $rtwalwm_updated && !$rtwalwm_value ){
					$rtwalwm_message = esc_html__( 'This user is not an affiliate now', 'rtwalwm-wp-wc-affiliate-program' );
				}

				echo json_encode( array( 'rtwalwm_status' => $rtwalwm_updated, 'rtwalwm_message' => $rtwalwm_message ) );
				die;
			}
		}
	}

	/*
	* Function to add make affiliate field while adding new user
	*/
	function rtwalwm_custom_user_profile_fields_add(){ ?>
		<h3><?php esc_html_e( "Affiliaa - WordPress & WooCommerce Affiliate Program", 'rtwalwm-wp-wc-affiliate-program' ); ?></h3>
	    <table class="form-table">
	        <tr>
	            <th><label for="rtwalwm_affiliate"><?php esc_html_e( "Become Affiliate", 'rtwalwm-wp-wc-affiliate-program' ); ?></label></th>
	            <td>
	                <input type="checkbox" class="rtwalwm_add_user_affiliate" name="rtwalwm_add_affiliate_checkbox" id="rtwalwm_add_user_affiliate" />
	                <span class="description"><?php esc_html_e( "This will make this user also as an Affiliate", 'rtwalwm-wp-wc-affiliate-program' ); ?></span>
	            </td>
	        </tr>
	        <?php
		        $rtwalwm_commission_settings = get_option( 'rtwwwap_commission_settings_opt' );
				$rtwalwm_comm_base = isset( $rtwalwm_commission_settings[ 'comm_base' ] ) ? $rtwalwm_commission_settings[ 'comm_base' ] : '1';
				if( $rtwalwm_comm_base == 2 ){
					$rtwalwm_levels_settings = get_option( 'rtwalwm_levels_settings_opt' );
					if( !empty( $rtwalwm_levels_settings ) ){
			?>
				        <tr class="rtwalwm_new_user_level">
				            <th><label for="rtwalwm_affiliate"><?php esc_html_e( "Affiliate Level", 'rtwalwm-wp-wc-affiliate-program' ); ?></label></th>
				            <td>
				                <select class="rtwalwm_select2_user_level" id="" name="rtwalwm_affiliate_level" >
				                	<?php
				                		foreach( $rtwalwm_levels_settings as $rtwalwm_key => $rtwalwm_value ){
				                	?>
										<option value="<?php echo esc_attr($rtwalwm_key); ?>" >
											<?php echo esc_html( $rtwalwm_value[ 'level_name' ] ); ?>
										</option>
									<?php
				                		}
				                	?>
								</select>
				            </td>
				        </tr>
			<?php
					}
				}
			?>
	    </table>
	<?php
	}

	/*
	* Function to save make affiliate field while adding new user
	*/
	function rtwalwm_save_custom_user_profile_fields_add( $rtwalwm_user_id ){

		if( !current_user_can( 'manage_options' ) ){
	        return false;
		}
		$rtwalwm_value = ( sanitize_text_field( $_POST[ 'rtwalwm_add_affiliate_checkbox' ] ) == 'on' ) ? 1 : 0;
	    update_user_meta( $rtwalwm_user_id, 'rtwwwap_affiliate', $rtwalwm_value );
	    update_user_meta( $rtwalwm_user_id, 'rtwwwap_aff_approved', $rtwalwm_value );

	  
	}


	/*
	* Function to update thead of products table
	*/
	function rtwalwm_add_commission_column( $rtwalwm_columns)
	{


		if( current_user_can( 'manage_options' ) ){
			$rtwalwm_commission_settings 	= get_option( 'rtwwwap_commission_settings_opt' );
			$rtwalwm_comm_base 				= isset( $rtwalwm_commission_settings[ 'comm_base' ] ) ? $rtwalwm_commission_settings
			[ 'comm_base' ] : '1';
			
			if( $rtwalwm_comm_base == 1 ){
				$rtwalwm_commission_settings = isset( $rtwalwm_commission_settings[ 'per_prod_mode' ] ) ? $rtwalwm_commission_settings[ 'per_prod_mode' ] : 0;

				if( $rtwalwm_commission_settings == 1 ){
					$rtwalwm_columns = array_merge( $rtwalwm_columns, array( 'rtwalwm_prod_perc_commission' => esc_html__( 'Percentage Commission', 'rtwalwm-wp-wc-affiliate-program' ) ) );
					
				}


				if( $rtwalwm_commission_settings == 2 ){
					$rtwalwm_columns = array_merge( $rtwalwm_columns, array( 'rtwalwm_prod_fix_commission' => esc_html__( 'Fixed Commission', 'rtwalwm-wp-wc-affiliate-program' ) ) );
				}
				
			}
		}

		return $rtwalwm_columns;
	}

	/*
	* Function to update tbody of products table
	*/
	function rtwalwm_manage_commission_column( $rtwalwm_column, $rtwalwm_post_id )
	{	

	
	 	$rtwalwm_post_type = get_post_type( $rtwalwm_post_id);
	
		$rtwalwm_fix_col = '' ;

		$rtwalwm_perc_col = '' ;

if( $rtwalwm_column == 'rtwalwm_prod_perc_commission' && current_user_can( 'edit_posts' ) && ($rtwalwm_post_type == 'download' || 'product')){


				$rtwalwm_post_meta = get_post_meta( $rtwalwm_post_id, 'rtwalwm_percentage_commission_box', true );
				
				$rtwalwm_perc_col .= "<p class='form-row ' id='rtwalwm_prod_perc_commission_field' data-priority=''>";
				$rtwalwm_perc_col .=  "<span >";
				$rtwalwm_perc_col .=  "<input type='text' class='input-text rtwalwm_perc_commission_box commission_field_width' name='rtwalwm_prod_perc_commission' id='rtwalwm_prod_perc_commission' placeholder='Percentage ' value='$rtwalwm_post_meta' data-rtwalwm-num='$rtwalwm_post_id' max='99' style='
				width: 105px;'>" ;
				$rtwalwm_perc_col .= "</span>";
				$rtwalwm_perc_col .= "</p>";

			}
			echo  $rtwalwm_perc_col;
	
		if( $rtwalwm_column == 'rtwalwm_prod_fix_commission' && current_user_can( 'edit_posts' ) && ($rtwalwm_post_type == 'download' || 'product') ){
			
				$rtwalwm_post_meta = get_post_meta( $rtwalwm_post_id, 'rtwalwm_fixed_commission_box', true );
			
	
				$rtwalwm_fix_col .= "<p class='form-row ' id='rtwalwm_prod_fix_commission_field' data-priority=''>";
				$rtwalwm_fix_col .=  "<span >";
				$rtwalwm_fix_col .=  "<input type='number' class='input-text rtwalwm_fix_commission_box ' name='rtwalwm_prod_fix_commission' id='rtwalwm_fixed_commission_box' placeholder='Fixed ' value='".esc_attr($rtwalwm_post_meta)."' data-rtwalwm-num='".esc_attr($rtwalwm_post_id)."'max='99' style='
				width: 80px;'>" ;
				$rtwalwm_fix_col .= "</span>";
				$rtwalwm_fix_col .= "</p>";
			
				
			}
			///// custom HTML 

			echo $rtwalwm_fix_col;
		return $rtwalwm_column;
	}

	/**
	 * This function is for changing the product commission
	 */
	function rtwalwm_change_prod_commission_callback()
	{
		$rtwalwm_check_ajax = check_ajax_referer( 'rtwalwm-ajax-security-string', 'rtwalwm_security_check' );

		if ( $rtwalwm_check_ajax ) {
			if( current_user_can( 'edit_posts' ) )
			{
				
				$rtwalwm_post_id 	= sanitize_text_field( $_POST[ 'rtwalwm_post_id' ] );
				$rtwalwm_value 		= sanitize_text_field( $_POST[ 'rtwalwm_value' ] );
				
					
				if( sanitize_text_field( $_POST[ 'rtwalwm_type' ] ) == 'perc_comm' ){
					$rtwalwm_updated = update_post_meta( $rtwalwm_post_id, 'rtwalwm_percentage_commission_box', $rtwalwm_value );
				}
				elseif( sanitize_text_field( $_POST[ 'rtwalwm_type' ] ) == 'fix_comm' ){
					$rtwalwm_updated = update_post_meta( $rtwalwm_post_id, 'rtwalwm_fixed_commission_box', $rtwalwm_value );
				}

				if( $rtwalwm_updated ){
					$rtwalwm_message = esc_html__( 'Commission for this product is updated', 'rtwalwm-wp-wc-affiliate-program' );
				}
				else{
					$rtwalwm_message = esc_html__( 'Something went wrong', 'rtwalwm-wp-wc-affiliate-program' );
				}

				echo json_encode( array( 'rtwalwm_status' => $rtwalwm_updated, 'rtwalwm_message' => $rtwalwm_message ) );
				die;
			}
		}
	}

	/*
	* This function is for creating a commission meta box in single product page
	*/
	
	function rtwalwm_add_custom_meta_box(){
		if( current_user_can( 'manage_options' ) ){
			$rtwalwm_commission_settings 	= get_option( 'rtwwwap_commission_settings_opt' );
			$rtwalwm_comm_base 				= isset( $rtwalwm_commission_settings[ 'comm_base' ] ) ? $rtwalwm_commission_settings[ 'comm_base' ] : '1';
			if(RTWALWM_IS_WOO == 1)
			{
				$rtwalwm_Easy_OR_Woo = 'product';	
			}
			if(RTWALWM_IS_Easy == 1)
			{
				$rtwalwm_Easy_OR_Woo = 'download';	
			}
			if( $rtwalwm_comm_base == 1 ){
				add_meta_box( 'rtwalwm_product_custom_meta_box', esc_html__( 'Add Commission', 'rtwalwm-wp-wc-affiliate-program' ), array( $this, 'rtwalwm_product_custom_meta_box_show' ), $rtwalwm_Easy_OR_Woo );
			}
		}
	}


	/*
	* This function is for displaying the meta box
	*/
	function rtwalwm_product_custom_meta_box_show( $rtwalwm_post ){
	
		$rtwalwm_fix_comm 	= get_post_meta( $rtwalwm_post->ID, 'rtwalwm_fixed_commission_box', true );

		$rtwalwm_commission_settings = get_option( 'rtwwwap_commission_settings_opt' );
		$rtwalwm_commission_settings = isset( $rtwalwm_commission_settings[ 'per_prod_mode' ] ) ? $rtwalwm_commission_settings[ 'per_prod_mode' ] : 0;

	    // We'll use this nonce field later on when saving.
	    wp_nonce_field( 'rtwalwm_commission_nonce', 'rtwalwm_meta_box_nonce' );
	    if( $rtwalwm_commission_settings == 2 ){
	    ?>
	    <p>
	        <label for="rtwalwm_fixed_commission_box"><?php esc_html_e( 'Fixed Commission', 'rtwalwm-wp-wc-affiliate-program' ); ?></label>
	        <input type="number" min="0" name="rtwalwm_fixed_commission_box" id="rtwalwm_fixed_commission_box" value="<?php echo esc_attr( $rtwalwm_fix_comm ); ?>" />
	    </p>
	    <?php
		}
	}


	


	/*
	* Function to save meta box
	*/
	function rtwalwm_save_custom_meta_box( $rtwalwm_post_id, $rtwalwm_post, $rtwalwm_update ) {
	    if( !isset( $_POST[ 'rtwalwm_meta_box_nonce' ] ) || !wp_verify_nonce( sanitize_text_field( $_POST[ 'rtwalwm_meta_box_nonce' ] ), 'rtwalwm_commission_nonce' ) )
	    {
	    	return;
	    }

	    if( !current_user_can( 'edit_post' ) ){
	    	return;
	    }
	    if( isset( $_POST[ 'rtwalwm_fixed_commission_box' ] ) ){
        	update_post_meta( $rtwalwm_post_id, 'rtwalwm_fixed_commission_box', sanitize_text_field( $_POST[ 'rtwalwm_fixed_commission_box' ] ) );
	    }
	}


	function rtwalwm_approve_callback()
	{

		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		$rtwalwm_check_ajax = check_ajax_referer( 'rtwalwm-ajax-security-string', 'rtwalwm_security_check' );

		if ( $rtwalwm_check_ajax ) {

			global $wpdb;
			$rtwalwm_reff_ids 		= isset($_POST[ 'rtwalwm_referral_ids' ])? $_POST[ 'rtwalwm_referral_ids' ] :"";
		
			$rtwalwm_approved_ids 	= array();
			if( RTWALWM_IS_WOO == 1 ){
				$rtwalwm_currency = get_woocommerce_currency();
			}
			else{
				$rtwalwm_currency  = 'USD';
			}
	
			$rtwalwm_currency_sym 	= esc_html( $rtwalwm_currency );

			foreach( $rtwalwm_reff_ids as $rtwalwm_key => $rtwalwm_value ){


		
				$rtwalwm_updated = $wpdb->update(
										$wpdb->prefix.'rtwwwap_referrals',
										array( 'status' => 1 ),
										array( 'id' => $rtwalwm_value ),
										array( '%d' ),
										array( '%d' )
									);

							

				if( $rtwalwm_updated ){
					$rtwalwm_referral_amount 	= $wpdb->get_var( $wpdb->prepare( "SELECT `amount` FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `id`=%d AND `type` != %d", $rtwalwm_value, 3 ) );
					$rtwalwm_referral_user_id 	= $wpdb->get_var( $wpdb->prepare( "SELECT `aff_id` FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `id`=%d", $rtwalwm_value ) );

					$rtwalwm_referral_amount = isset($rtwalwm_referral_amount) && !empty($rtwalwm_referral_amount)? $rtwalwm_referral_amount: 0;

					$rtwalwm_aff_overall_comm 	= get_user_meta( $rtwalwm_referral_user_id, 'rtw_user_wallet', true );
					$rtwalwm_aff_overall_comm = isset($rtwalwm_aff_overall_comm) && !empty($rtwalwm_aff_overall_comm)? $rtwalwm_aff_overall_comm: 0;

					$rtwalwm_aff_wallet_amount = $rtwalwm_aff_overall_comm + $rtwalwm_referral_amount;

					update_user_meta( $rtwalwm_referral_user_id, 'rtw_user_wallet', esc_html( $rtwalwm_aff_wallet_amount ) );

					//performance bonus start
					$rtwalwm_last_incentive_given = get_user_meta( $rtwalwm_referral_user_id, 'rtwalwm_perf_bonus', true );

					$rtwalwm_total_amount_referred = $wpdb->get_results( $wpdb->prepare( "SELECT `product_details` FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `aff_id`=%d AND ( `status`=%d OR `status`=%d ) AND `type`=%d", $rtwalwm_referral_user_id, 1, 2, 0 ), ARRAY_A );

					$rtwalwm_total_referred_amt = 0;
					if( !empty( $rtwalwm_total_amount_referred ) ){
						foreach( $rtwalwm_total_amount_referred as $rtwalwm_key => $rtwalwm_value ){
							$rtwalwm_prod_details = json_decode( $rtwalwm_value[ 'product_details' ], true );
							$rtwalwm_total_referred_amt += array_sum( array_column( $rtwalwm_prod_details, 'product_price' ) );
						}
					}

					$rtwalwm_approved_ids[] = $rtwalwm_value;
				}
			}

			if( sizeof( $rtwalwm_reff_ids ) == sizeof( $rtwalwm_approved_ids ) ){
				$rtwalwm_status = true;
				$rtwalwm_message = esc_html__( 'Approved', 'rtwalwm-wp-wc-affiliate-program' );
			}
			else{
				$rtwalwm_status = false;
				$rtwalwm_message = esc_html__( 'Some referrals are not approved. Try again', 'rtwalwm-wp-wc-affiliate-program' );
			}

			echo json_encode( array( 'rtwalwm_status' => $rtwalwm_status, 'rtwalwm_message' => $rtwalwm_message, 'rtwalwm_approved_ids' => $rtwalwm_approved_ids ) );
			die;
		}
	}

	function rtwalwm_reject_callback()
	{
		$rtwalwm_check_ajax = check_ajax_referer( 'rtwalwm-ajax-security-string', 'rtwalwm_security_check' );

		if ( $rtwalwm_check_ajax ) {
			global $wpdb;
			
			$rtwalwm_reff_ids 		=  sanitize_text_field($_POST['rtwalwm_referral_ids']);

			$rtwalwm_rejected_ids 	= array();

			if(!empty($rtwalwm_reff_ids)){
				foreach( $rtwalwm_reff_ids as $rtwalwm_key => $rtwalwm_value ){
					$rtwalwm_updated = $wpdb->update(
											$wpdb->prefix.'rtwwwap_referrals',
											array( 'status' => 3 ),
											array( 'id' => $rtwalwm_value ),
											array( '%d' ),
											array( '%d' )
										);
	
					if( $rtwalwm_updated ){
						$rtwalwm_rejected_ids[] = $rtwalwm_value;
					}
				}

				if( sizeof( $rtwalwm_reff_ids ) == sizeof( $rtwalwm_rejected_ids ) ){
					$rtwalwm_status = true;
					$rtwalwm_message = esc_html__( 'Rejected', 'rtwalwm-wp-wc-affiliate-program' );
				}
				else{
					$rtwalwm_status = false;
					$rtwalwm_message = esc_html__( 'Some referrals are not rejected. Try again', 'rtwalwm-wp-wc-affiliate-program' );
				}
			}
			else{
				$rtwalwm_status = false;
					$rtwalwm_message = esc_html__( 'Some referrals are not rejected. Try again', 'rtwalwm-wp-wc-affiliate-program' );
			}

			echo json_encode( array( 'rtwalwm_status' => $rtwalwm_status, 'rtwalwm_message' => $rtwalwm_message, 'rtwalwm_rejected_ids' => $rtwalwm_rejected_ids ) );
			die;
		}
	}

	function rtwalwm_aff_approve_callback()
	{
		$rtwalwm_check_ajax = check_ajax_referer( 'rtwalwm-ajax-security-string', 'rtwalwm_security_check' );

		if ( $rtwalwm_check_ajax ) {
			$rtwalwm_reff_ids 		=  sanitize_post( $_POST['rtwalwm_referral_ids'] ) ;

			$rtwalwm_approved_ids 	= array();


			if( sizeof( $rtwalwm_reff_ids ) == sizeof( $rtwalwm_approved_ids ) ){
				$rtwalwm_message = esc_html__( 'Approved', 'rtwalwm-wp-wc-affiliate-program' );
			}
			else{
				$rtwalwm_message = esc_html__( 'Something went wrong. Try again', 'rtwalwm-wp-wc-affiliate-program' );
			}

			echo json_encode( array( 'rtwalwm_status' => true, 'rtwalwm_message' => $rtwalwm_message, 'rtwalwm_approved_ids' => $rtwalwm_approved_ids ) );
			die;
		}
	}

	/**
	 * This function is for delete referrals
	 */
	function rtwalwm_referral_delete_callback()
	{
		global $wpdb;
		$rtwalwm_check_ajax = check_ajax_referer( 'rtwalwm-ajax-security-string', 'rtwalwm_security_check' );

		if ( $rtwalwm_check_ajax ) {
			$rtwalwm_referral_id 		=  sanitize_post( $_POST['rtwalwm_referral_id'] ) ;
			$rtwalwm_delete_referral 	= $wpdb->delete( $wpdb->prefix.'rtwwwap_referrals', array( 'id' => $rtwalwm_referral_id ), array( '%d' ) );

			if( $rtwalwm_delete_referral ){
				$rtwalwm_message 	= esc_html__( 'Deleted', 'rtwalwm-wp-wc-affiliate-program' );
				$rtwalwm_status 	= true;
			}
			else{
				$rtwalwm_message 	= esc_html__( 'Something went wrong. Try again', 'rtwalwm-wp-wc-affiliate-program' );
				$rtwalwm_status 	= false;
			}

			echo json_encode( array( 'rtwalwm_status' => $rtwalwm_status, 'rtwalwm_message' => $rtwalwm_message ) );
			die;
		}
	}

	// update detail into custom banner option

	function rtwalwm_custom_banner_callback()
	{
		$rtwalwm_check_ajax = check_ajax_referer( 'rtwalwm-ajax-security-string', 'rtwalwm_security_check' );
	
		if ( $rtwalwm_check_ajax ) {
			
			$rtwalwm_image_id = sanitize_text_field($_POST[ 'rtwalwm_image_id' ]);
			$rtwalwm_target_link = site_url();
			$rtwalwm_image_dimention_width = sanitize_text_field($_POST[ 'rtwalwm_image_dimention_width' ]);
			$rtwalwm_image_dimention_height = sanitize_text_field($_POST[ 'rtwalwm_image_dimention_height' ]);


			$rtwalwm_custom_banner_opt = get_option( 'rtwwwap_custom_banner_opt' );
			$rtwalwm_status = false;		
				if(!empty($rtwalwm_custom_banner_opt))
				{
					$rtwalwm_custom_banner_opt[] =  array('image_id' => $rtwalwm_image_id , 'target_link' => $rtwalwm_target_link , 'image_width' => $rtwalwm_image_dimention_width , 'image_height'=> $rtwalwm_image_dimention_height);
					$rtwalwm_banner_update = update_option( 'rtwwwap_custom_banner_opt', $rtwalwm_custom_banner_opt);
					$rtwalwm_status = true;
				}
				elseif(empty($rtwalwm_custom_banner_opt))
				{
					$rtwalwm_banner_option[] =  array('image_id' => $rtwalwm_image_id , 'target_link' => $rtwalwm_target_link , 'image_width' => $rtwalwm_image_dimention_width , 'image_height'=> $rtwalwm_image_dimention_height);
					$rtwalwm_banner_update = update_option( 'rtwwwap_custom_banner_opt', $rtwalwm_banner_option);
					$rtwalwm_status = true;

				}

				if($rtwalwm_banner_update) 
				{
				
					echo json_encode( array( 'rtwalwm_status' => $rtwalwm_status, 'rtwalwm_message' => esc_html__( "Successfully Uploaded", 'rtwalwm-wp-wc-affiliate-program' )) );
		
				}
				else{
					echo json_encode( array( 'rtwalwm_status' => $rtwalwm_status, 'rtwalwm_message' => esc_html__( "Failed to upload", 'rtwalwm-wp-wc-affiliate-program' )) );

				}
			}
			wp_die();
	}

	// Delete custom banner 

	function rtwalwm_delete_banner_callback()
	{
		$rtwalwm_check_ajax = check_ajax_referer( 'rtwalwm-ajax-security-string', 'rtwalwm_security_check' );
	
		if ( $rtwalwm_check_ajax ) {
			
			$rtwalwm_image_id = sanitize_text_field($_POST[ 'rtwalwm_image_id' ]) ;
			$rtwalwm_target_link = sanitize_text_field($_POST[ 'rtwalwm_target_link' ]);
			$rtwalwm_custom_banner_opt = get_option( 'rtwwwap_custom_banner_opt' );
			
				if(!empty($rtwalwm_custom_banner_opt))
				{

					foreach($rtwalwm_custom_banner_opt as $key => $value)
					{	
						if(($value['image_id'] == $rtwalwm_image_id) &&  ($value['target_link'] == $rtwalwm_target_link) 	)
						{
							 unset($rtwalwm_custom_banner_opt[$key]);
							$rtwalwm_custom_banner = array_values( $rtwalwm_custom_banner_opt );
							$rtwalwm_banner_update = update_option( 'rtwwwap_custom_banner_opt', $rtwalwm_custom_banner );
						}	
					}	
				}
				if($rtwalwm_banner_update) 
				{
					echo json_encode( array( 'rtwalwm_status' => true , 'rtwalwm_message' => esc_html__( "Deleted", 'rtwalwm-wp-wc-affiliate-program' )) );
				}
				else{
					echo json_encode( array( 'rtwalwm_status' => false, 'rtwalwm_message' => esc_html__( "Not Deleted", 'rtwalwm-wp-wc-affiliate-program' )) );
				}
			}
			wp_die();
		

	}

	function rtwalwm_add_coupon_text_field_callback()
	{
		global $post;
		$rtwalwm_args = array(
			'meta-key' 		=> 	'rtwalwm_aff_approved',
			'meta_value' 	=> 	'1',
			'meta_compare' 	=> 	'=' 
		);
		$rtwalwm_affiliates =  new WP_User_Query( $rtwalwm_args );
		$Affiliates[''] = __( 'Select an affiliate', 'rtwalwm-wp-wc-affiliate-program'); 

		if($rtwalwm_affiliates)
		{
			foreach($rtwalwm_affiliates->results as $key => $value)
			{
				$Affiliates[$value->data->ID] = $value->data->display_name;
			}
		}
		else 
		{
			echo 'No Affiliate users found.';
		}
		woocommerce_wp_select(
			array(
				'id'          => 'rtwalwm_select_affiliate',
				'label'       => __( 'Select Affiliate', 'rtwalwm-wp-wc-affiliate-program' ),
				'options' =>  $Affiliates,
				'value' =>  get_post_meta( $post->ID, 'rtwalwm_coupon_aff_id', true ),
				)
			);
	}

	function woocommerce_save_coupon_callback($post_id)
	{
		$rtwalwm_select_affiliate_id = isset( $_POST['rtwalwm_select_affiliate'] ) ?  $_POST['rtwalwm_select_affiliate'] : 0;
		$rtwalwm_user_meta = update_user_meta($rtwalwm_select_affiliate_id,'rtwalwm_coupon_save',$post_id );
		update_post_meta( $post_id, 'rtwalwm_coupon_aff_id', $rtwalwm_select_affiliate_id );
	}

	function rtwalwm_save_notification_callback()
	{
		$rtwalwm_not_title   =	isset($_POST['rtwalwm_not_title'])? $_POST['rtwalwm_not_title'] : "" ; 
		$rtwalwm_noti_content = isset($_POST['rtwalwm_no_text'])? $_POST['rtwalwm_no_text'] : "" ;
		$rtwalwm_key = isset($_POST['rtwalwm_key'])? $_POST['rtwalwm_key'] : "" ;

		$rtwalwm_time = date('d/m/y_H:i:s');
		$rtwalwm_noti_array = get_option('rtwalwm_noti_arr');
		// echo"<pre>";
		// print_r($rtwalwm_noti_array);
		// echo"</pre>";
		// die("abcdserrgfh");
		
	}

}






