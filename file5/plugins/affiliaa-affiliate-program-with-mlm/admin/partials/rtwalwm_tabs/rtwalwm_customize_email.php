<?php 

$rtwalwm_section_show = 'signup-email';
settings_fields( 'rtwalwm_email_features');

$rtwalwm_email_features = get_option( 'rtwalwm_email_features_opt' );

$test = get_option('customize_email',true);

?>


<div class="main-wrapper">
	<div class="rtwalwm-data-table-wrapper">
		<table class="rtwalwm_affiliates_table rtwalwm_data_table stripe" class="display dtr-inline" cellspacing="0">
		  	<thead>
			  	<tr>
			    	<th><?php esc_html_e( 'Email type', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Subject', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
                    <th><?php esc_html_e( 'Activate/Deactivate', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
                    <th><?php esc_html_e( 'Action', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			  	</tr>
		  	</thead>
		  	<tbody>
                <?php
                    if( isset($test) && !empty($test)){
                    foreach( $test as $key=> $val ){
						$rtwalwm_email_content_without_html = $val['content'];
                    ?>
					<tr>	 
                        <td><?php echo esc_html_e( $key ); ?></td>
                        <td class='subject'><?php echo esc_html_e( $val['subject'], 'rtwalwm-wp-wc-affiliate-program'  ); ?></td>
                        <td><label class="rtwalwm_switch"><input type="checkbox" class="rtwalwm_email_check"><span class="rtwalwm_slider round"></span></label></td>
                        
                        <td><input type='button' value='Edit Email' class='rtwalwm_customize_email' data-email_type="<?php esc_attr_e($key); ?>" /></td>	
                    </tr>
                    <?php
                        }
                    }
                ?>	
			</tbody>

			<tfoot>
			  	<tr>
			    	<th><?php esc_html_e( 'Email type', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Subject', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
                    <th><?php esc_html_e( 'Activate/Deactivate', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
                    <th><?php esc_html_e( 'Action', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			  	</tr>
		  	</tfoot>
		</table>

        <!-- <div class="rtwwwap_available_in_pro">
            <h2>
            </h2>
        </div> -->

    </div>
    <?php include_once( RTWALWM_DIR . '/admin/partials/rtwalwm_tabs/rtwalwm_footer.php' ); ?>
</div>

