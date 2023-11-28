


	<p class="rtwalwm_add_new_level">
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=rtwalwm&rtwalwm_tab=rtwalwm_levels_add_edit' ) ); ?>" target="_blank" >
			<input type="button" value="<?php esc_attr_e( 'Add New Level', 'rtwalwm-wp-wc-affiliate-program' ); ?>" class="rtwalwm-button" name="rtwalwm_add_new_level" />
		</a>
		<a href="javascript:void(0);">
			<input type="button" value="<?php esc_attr_e( 'Update Order', 'rtwalwm-wp-wc-affiliate-program' ); ?>" class="rtwalwm-button rtwalwm_update_level_order" name=""  />
		</a>
	</p>
<div class ="rtwwdpdl_pro_text_overlay">
	<div class= "main-wrapper " >
		<div class="rtwalwm-data-table-wrapper">
			<table class="rtwalwm_levels_table rtwalwm_data_table stripe" class="display dtr-inline" cellspacing="0">
			  	<thead>
				  	<tr>
				    	<th><?php esc_html_e( 'Sort', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
				    	<th><?php esc_html_e( 'Level', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
				    	<th><?php esc_html_e( 'Name', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
				    	<th><?php esc_html_e( 'Commission', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
				    	<th><?php esc_html_e( 'To Reach', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
				    	<th><?php esc_html_e( 'Actions', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
				  	</tr>
			  	</thead>
			  	<tbody>
			  
						  	<tr >
						    	<td></td>
						  	</tr>
				
				</tbody>
				<tfoot>
				  	<tr>
				  		<th><?php esc_html_e( 'Sort', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
				  		<th><?php esc_html_e( 'Level', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
				    	<th><?php esc_html_e( 'Name', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
				    	<th><?php esc_html_e( 'Commission', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
				    	<th><?php esc_html_e( 'To Reach', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
				    	<th><?php esc_html_e( 'Actions', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
				  	</tr>
			  	</tfoot>
			</table>
		</div>
</div>
		<?php include_once( RTWALWM_DIR . '/admin/partials/rtwalwm_tabs/rtwalwm_footer.php' ); ?>
	</div>
