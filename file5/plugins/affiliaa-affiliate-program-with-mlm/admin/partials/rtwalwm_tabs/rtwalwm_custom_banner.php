<?php
	$rtwalwm_custom_banner = get_option( 'rtwwwap_custom_banner_opt' );


?>
<p class="rtwalwm_add_new_banner">
	<input type="button" value="<?php esc_attr_e( 'Add new Banner', 'rtwalwm-wp-wc-affiliate-program' ); ?>" class="rtwalwm-button rtwalwm_add_custom_banner" name="rtwalwm_add_custom_banner" />
</p>


<div class="main-wrapper">
	<div id="dialogForm">
	</div>
	<div class="rtwalwm-data-table-wrapper">
		<table class="rtwalwm_custom_banner_table rtwalwm_data_table stripe" class="display dtr-inline" cellspacing="0">
		  	<thead>
			  	<tr>
			    	<th><?php esc_html_e( 'Banner Image', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
					<th><?php esc_html_e( 'Target Link', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
					<th><?php esc_html_e( 'Image Size', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
					<th><?php esc_html_e( 'Actions', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			  	</tr>
			  </thead>
			  <tbody>
			  <?php
			  if($rtwalwm_custom_banner != '' )
			  { 
			  foreach($rtwalwm_custom_banner as $key => $value)
			  {	
				  ?>
				  <tr>
					<td>
						<img src="<?php echo  wp_get_attachment_url(esc_attr($value['image_id'])) ?>" width="200px" height="150px" > 
					</td>
					<td>
						<?php echo esc_url($value['target_link']); ?>
					</td>
					<td>
						<?php echo esc_attr($value['image_width']." x ".$value['image_height']); ?>
					</td>
					<td>
					    		<a class="rtwalwm-delete-link rtwalwm_custom_banner_delete" data-image_id="<?php echo esc_attr(isset($value['image_id']) ? $value['image_id'] : "" );?>"  data-target_link="<?php echo esc_attr(isset($value['target_link']) ? $value['target_link'] : "");?>" href="javascript:void(0);">
					    			<span class="dashicons dashicons-trash"></span>
					    		</a>
					</td>
				  </tr>
			  <?php  }
			  
			   } ?>
				</tbody>
	
			
			<tfoot>
			  	<tr>
			    	<th><?php esc_html_e( 'Banner Image', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
					<th><?php esc_html_e( 'Target Link', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
					<th><?php esc_html_e( 'Image Size', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
					<th><?php esc_html_e( 'Actions', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			  	</tr>
		  	</tfoot>
		</table>
    </div>

	<?php include_once( RTWALWM_DIR . '/admin/partials/rtwalwm_tabs/rtwalwm_footer.php' ); ?>
	<div class="rtwalwm_add_custom_banner_wrapper">
		<div class="rtwalwm-popup-content">
			  <h3 class="rtwalwm-popup-heading"><?php esc_html_e( 'Upload Custom Banner', 'rtwalwm-wp-wc-affiliate-program' ); ?></h3>
				<div class="rtwalwm_custom_main">	  
			  		<div class="rtwalwm-image-add">
                        	<img id="rtwalwm-image-preview" src="">
					</div>
					<div class= "rtwalwm_banner_info_section">

								<p class= "rtwalwm_custom_banner_image_detail">
									<input type="button" value="UPLOAD IMAGE" class="rtwalwm-button rtwalwm_custom_banner_image" name="rtwalwm_custom_banner_image" >
									<input type="hidden" value="" id="rtwalwm-image_attachment_id">
								</p>
								<p class= "rtwalwm_custom_banner_product_url_detail">
									<label class="rtwalwm_custom_ban_label"><?php esc_html_e( 'Enter Target URL', 'rtwalwm-wp-wc-affiliate-program' ); ?></label>
									<input type="text" value="<?php echo esc_url(site_url());?>" class="rtwalwm_custom_banner_url_detail" name="" disabled >
								</p>
									<span class= "rtwalwm_select_dimention"><?php esc_html_e( 'Select Image Width x Height ', 'rtwalwm-wp-wc-affiliate-program' ); ?></span>
								<select class="rtwalwm_select_image_size">
									<option value="0" selected> <?php esc_html_e( 'Select Image Dimention', 'rtwalwm-wp-wc-affiliate-program' ); ?></option>\
									<option value="250x250"> <?php esc_html_e( 'Square – 250 x 250', 'rtwalwm-wp-wc-affiliate-program' ); ?></option>
									<option value="" disabled> <?php esc_html_e( 'Small Square – 200 x 200- PRO', 'rtwalwm-wp-wc-affiliate-program' ); ?></option>
									<option value="" disabled> <?php esc_html_e( 'Banner – 468 x 60- PRO', 'rtwalwm-wp-wc-affiliate-program' ); ?></option>
									<option value="" disabled> <?php esc_html_e( 'Leaderboard – 728 x 90- PRO', 'rtwalwm-wp-wc-affiliate-program' ); ?></option>
									<option value="" disabled> <?php esc_html_e( 'Inline Rectangle – 300 x 250- PRO', 'rtwalwm-wp-wc-affiliate-program' ); ?></option>
									<option value="" disabled> <?php esc_html_e( 'Large Rectangle – 336 x 280- PRO', 'rtwalwm-wp-wc-affiliate-program' ); ?></option>
									<option value="" disabled> <?php esc_html_e( 'Skyscraper – 120 x 600- PRO', 'rtwalwm-wp-wc-affiliate-program' ); ?></option>
									<option value="" disabled> <?php esc_html_e( 'Wide Skyscraper – 160 x 600- PRO', 'rtwalwm-wp-wc-affiliate-program' ); ?></option>
									<option value="" disabled> <?php esc_html_e( 'Half-Page Ad – 300 x 600- PRO', 'rtwalwm-wp-wc-affiliate-program' ); ?></option>
									<option value="" disabled> <?php esc_html_e( 'Large Leaderboard – 970 x 90- PRO', 'rtwalwm-wp-wc-affiliate-program' ); ?></option>
								</select>
								<p class="rtwalwm_image_width_detail">
									<span><?php esc_html_e( 'Selected Image Width  :: ', 'rtwalwm-wp-wc-affiliate-program' ); ?></span>
									<label id = "rtwalwm_image_width"><label> 
								</p>
								<p class="rtwalwm_image_height_detail">
									<span><?php esc_html_e( 'Selected Image Height :: ', 'rtwalwm-wp-wc-affiliate-program' ); ?></span>
									<label id = "rtwalwm_image_height" ></label>
								</p>
					
					</div>	

				</div>
				<div class="rtwalwm-popup-footer">
					<input type="button" value="<?php esc_html_e( 'Save', 'rtwalwm-wp-wc-affiliate-program' ); ?>" class="rtwalwm-button" id="rtwalwm_save_custom_banner">
					<input type="reset" name="" value="<?php esc_html_e( 'Cancel', 'rtwalwm-wp-wc-affiliate-program' ); ?>" class="rtwalwm-button rtwalwm-button-reset" id="rtwalwm_cancle_custom_banner">
				</div>
		</div>
	</div>
</div>
