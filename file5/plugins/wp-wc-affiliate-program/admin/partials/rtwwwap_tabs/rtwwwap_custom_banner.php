<?php
	$rtwwwap_custom_banner = get_option( 'rtwwwap_custom_banner_opt' );


?>
<p class="rtwwwap_add_new_banner">
	<input type="button" value="<?php esc_attr_e( 'Add new Banner', 'rtwwwap-wp-wc-affiliate-program' ); ?>" class="rtwwwap-button rtwwwap_add_custom_banner" name="rtwwwap_add_custom_banner" />
</p>


<div class="main-wrapper">
	<div id="dialogForm">
	</div>
	<div class="rtwwwap-data-table-wrapper">
		<table class="rtwwwap_custom_banner_table rtwwwap_data_table stripe" class="display dtr-inline" cellspacing="0">
		  	<thead>
			  	<tr>
			    	<th><?php esc_html_e( 'Banner Image', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
					<th><?php esc_html_e( 'Target Link', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
					<th><?php esc_html_e( 'Image Size', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
					<th><?php esc_html_e( 'Actions', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			  	</tr>
			  </thead>
			  <tbody>
			  <?php
			  if($rtwwwap_custom_banner != '' )
			  { 
			  foreach($rtwwwap_custom_banner as $key => $value)
			  {	
				  ?>
				  <tr>
					<td>
						<img src="<?php echo  wp_get_attachment_url($value['image_id']) ?>" width="200px" height="150px" > 
					</td>
					<td>
						<?php echo esc_url($value['target_link']); ?>
					</td>
					<td>
						<?php echo esc_attr($value['image_width']." x ".$value['image_height']); ?>
					</td>
					<td>
					    		<a class="rtwwwap-delete-link rtwwwap_custom_banner_delete" data-image_id="<?php echo $value['image_id'] ?>"  data-target_link="<?php echo $value['target_link'] ?>" href="javascript:void(0);">
					    			<span class="dashicons dashicons-trash"></span>
					    		</a>
					</td>
				  </tr>
			  <?php  }
			  
			   } ?>
				</tbody>
	
			
			<tfoot>
			  	<tr>
			    	<th><?php esc_html_e( 'Banner Image', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
					<th><?php esc_html_e( 'Target Link', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
					<th><?php esc_html_e( 'Image Size', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
					<th><?php esc_html_e( 'Actions', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			  	</tr>
		  	</tfoot>
		</table>
    </div>

    <?php include_once( RTWWWAP_DIR . '/admin/partials/rtwwwap_tabs/rtwwwap_footer.php' ); ?>
	<div class="rtwwwap_add_custom_banner_wrapper">
		<div class="rtwwwap-popup-content">
			  <h3 class="rtwwwap-popup-heading"><?php esc_html_e( 'Upload Custom Banner', 'rtwwwap-wp-wc-affiliate-program' ); ?></h3>
				<div class="rtwwwap_custom_main">	  
			  		<div class="rtwwwap-image-add">
                        	<img id="rtwwwap-image-preview" src="">
					</div>
					<div class= "rtwwwap_banner_info_section">

								<p class= "rtwwwap_custom_banner_image_detail">
									<input type="button" value="UPLOAD IMAGE" class="rtwwwap-button rtwwwap_custom_banner_image" name="rtwwwap_custom_banner_image" >
									<input type="hidden" value="" id="rtwwwap-image_attachment_id">
								</p>
								<p class= "rtwwwap_custom_banner_product_url_detail">
									<label class="rtwwwap_custom_ban_label"><?php esc_html_e( 'Enter Target URL', 'rtwwwap-wp-wc-affiliate-program' ); ?></label>
									<input type="text" value="" class="rtwwwap_custom_banner_url_detail" name="" >
								</p>
									<span class= "rtwwwap_select_dimention"><?php esc_html_e( 'Select Image Width x Height ', 'rtwwwap-wp-wc-affiliate-program' ); ?></span>
								<select class="rtwwwap_select_image_size">
									<option value="0" selected> <?php esc_html_e( 'Select Image Dimention', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>\
									<option value="250x250"> <?php esc_html_e( 'Square – 250 x 250', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>
									<option value="200x200"> <?php esc_html_e( 'Small Square – 200 x 200', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>
									<option value="468x60"> <?php esc_html_e( 'Banner – 468 x 60', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>
									<option value="728x90"> <?php esc_html_e( 'Leaderboard – 728 x 90', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>
									<option value="300x250"> <?php esc_html_e( 'Inline Rectangle – 300 x 250', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>
									<option value="336x280"> <?php esc_html_e( 'Large Rectangle – 336 x 280', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>
									<option value="120x600"> <?php esc_html_e( 'Skyscraper – 120 x 600', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>
									<option value="160x600"> <?php esc_html_e( 'Wide Skyscraper – 160 x 600', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>
									<option value="300x600"> <?php esc_html_e( 'Half-Page Ad – 300 x 600', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>
									<option value="970x90"> <?php esc_html_e( 'Large Leaderboard – 970 x 90', 'rtwwwap-wp-wc-affiliate-program' ); ?></option>
								</select>
								<p class="rtwwwap_image_width_detail">
									<span><?php esc_html_e( 'Selected Image Width  :: ', 'rtwwwap-wp-wc-affiliate-program' ); ?></span>
									<label id = "rtwwwap_image_width"><label> 
								</p>
								<p class="rtwwwap_image_height_detail">
									<span><?php esc_html_e( 'Selected Image Height :: ', 'rtwwwap-wp-wc-affiliate-program' ); ?></span>
									<label id = "rtwwwap_image_height" ></label>
								</p>
					
					</div>	

				</div>
				<div class="rtwwwap-popup-footer">
					<input type="button" value="<?php esc_html_e( 'Save', 'rtwwwap-wp-wc-affiliate-program' ); ?>" class="rtwwwap-button" id="rtwwwap_save_custom_banner">
					<input type="reset" name="" value="<?php esc_html_e( 'Cancel', 'rtwwwap-wp-wc-affiliate-program' ); ?>" class="rtwwwap-button rtwwwap-button-reset" id="rtwwwap_cancle_custom_banner">
				</div>
		</div>
	</div>
</div>
