<?php
	$rtwalwm_new_url = admin_url( 'user-new.php' );

	$rtwalwm_args = array(
						'meta_key' 		=> 'rtwwwap_affiliate',
						'meta_value' 	=> '1',
						'orderby' 		=> 'id',
						'order' 		=> 'desc'
					);
    
	$rtwalwm_users = get_users( $rtwalwm_args );
	

	$rtwalwm_levels_settings 		= get_option( 'rtwwwap_levels_settings_opt' );
	$rtwalwm_commission_settings 	= get_option( 'rtwwwap_commission_settings' );
	$rtwalwm_comm_base 				= isset( $rtwalwm_commission_settings[ 'comm_base' ] ) ? $rtwalwm_commission_settings[ 'comm_base' ] : '1';

?>

<p class="rtwalwm_add_new_affiliate">
	<a href="<?php echo esc_url( $rtwalwm_new_url ); ?>" target="_blank">
		<input type="button" value="<?php esc_attr_e( 'Add New Affiliate', 'rtwalwm-wp-wc-affiliate-program' ); ?>" class="rtwalwm-button" name="rtwalwm_add_new_affiliate" />
	</a>
	<?php 
		$rtwalwm_extra_features = get_option( 'rtwwwap_extra_features_opt' );
		if( isset($rtwalwm_extra_features[ 'aff_verify' ]) && $rtwalwm_extra_features[ 'aff_verify' ] == 1 ){
	?>
		<input type="button" value="<?php esc_attr_e( 'Approve all marked Affiliates', 'rtwalwm-wp-wc-affiliate-program' ); ?>" class="rtwalwm-button rtwalwm_approve_all_affiliate" name="rtwalwm_approve_all_affiliate" />
	<?php } ?>
</p>

<div class="main-wrapper">
	<div class="rtwalwm-data-table-wrapper">
		<table class="rtwalwm_affiliates_table rtwalwm_data_table stripe" class="display dtr-inline" cellspacing="0">
		  	<thead>
			  	<tr>
			    	<th>
			    		<span class="rtwalwm-checkbox">
			    			<input class="rtwalwm_affiliate_check_all" id="rtwalwm_checkbox-th" type="checkbox" name=""/>
			    			<label for="rtwalwm_checkbox-th"></label>
			    		</span>
			    	</th>
			    	<th><?php esc_html_e( 'ID', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Username', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Name', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			    
			    	<th><?php esc_html_e( 'Email', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>

					<th><?php esc_html_e( 'Wp role', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>

					<th><?php esc_html_e( 'Parent Id', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Parent Name', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Actions', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			  	</tr>
		  	</thead>
		  	<tbody>
		  		<?php
		  			foreach( $rtwalwm_users as $rtwalwm_key => $rtwalwm_user ){
						 
		  		?>
					  	<tr data-referral_id="<?php echo esc_attr( $rtwalwm_user->ID ); ?>">
					    	<td>
					    		<?php 
									  $rtwalwm_aff_is_approved = get_user_meta( $rtwalwm_user->ID, 'rtwwwap_aff_approved', true );
									
						  			if( isset( $rtwalwm_extra_features[ 'aff_verify' ] ) && $rtwalwm_extra_features[ 'aff_verify' ] == 1 && !$rtwalwm_aff_is_approved ){
						  		?>
							    		<span class="rtwalwm-checkbox">
							    			<input id="rtwalwm_checkbox_<?php echo esc_attr( $rtwalwm_key ); ?>" type="checkbox" name=""/>
							    			<label for="rtwalwm_checkbox_<?php echo esc_attr( $rtwalwm_key ); ?>"></label>
							    		</span>
						    	<?php } ?>
					    	</td>
					    	<td>
					    		<?php echo esc_html( $rtwalwm_user->ID ); ?>
					    	</td>

					    	<td>
					    		<?php echo esc_html( $rtwalwm_user->data->user_login ); ?>
					    	</td>

					    	<td>
					    		<?php echo esc_html( $rtwalwm_user->data->display_name ); ?>
					    	</td>
							
							<?php 
								if( $rtwalwm_comm_base == 2 ){ 
									if( !empty( $rtwalwm_levels_settings ) ){
							?>
							    	<td>
							    		<?php
							    			$rtwalwm_user_level = get_user_meta( $rtwalwm_user->ID, 'rtwalwm_affiliate_level', true );
							    			if( $rtwalwm_user_level ){
							    				echo esc_html( $rtwalwm_levels_settings[ $rtwalwm_user_level ][ 'level_name' ] );
							    			}
							    			else{
							    				echo esc_html( $rtwalwm_levels_settings[0][ 'level_name' ] );
							    			}
							    		?>
							    	</td>
					    	<?php 
					    			} 
					    		}
					    	?>

					    	<td>
					    		<?php echo esc_html( $rtwalwm_user->data->user_email ); ?>
					    	</td>

							<td>
					    		<?php echo esc_html( $rtwalwm_user->roles[0] ); ?>
					    	</td>

							<td>
								<?php
									

								echo esc_html_e('MLM not active', 'rtwalwm-wp-wc-affiliate-program');

 								?>
					    	</td>
							<td>
							<?php 
								
								echo esc_html_e('MLM not active', 'rtwalwm-wp-wc-affiliate-program');


							?>
							</td>
						
					    	<td>
					    		<a class="rtwalwm-edit-link" href="<?php echo esc_url( get_edit_user_link( $rtwalwm_user->ID ) ); ?>" target="_blank">
					    			<span class="dashicons dashicons-edit"></span>
					    		</a>
					    		<?php 
						  			$rtwalwm_aff_is_approved = get_user_meta( $rtwalwm_user->ID, 'rtwwwap_aff_approved', true );
						  			if( isset($rtwalwm_extra_features[ 'aff_verify' ]) && $rtwalwm_extra_features[ 'aff_verify' ] == 1 ){
						  		?>
							    		<a class="rtwalwm-add-link" href="javascript:void(0);">
							    			<span class="dashicons dashicons-yes <?php echo ( esc_attr($rtwalwm_aff_is_approved) ) ? esc_attr( 'rtwalwm_aff_approved' ) : esc_attr( 'rtwalwm_aff_approve' ); ?>"></span>
							    		</a>
						    	<?php } ?>
					    	</td>
					  	</tr>
				<?php } ?>
			</tbody>
			<tfoot>
			  	<tr>
			    	<th></th>
			    	<th><?php esc_html_e( 'ID', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Username', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Name', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Email', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
					<th><?php esc_html_e( 'Wp role', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
					<th><?php esc_html_e( 'Parent Id', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Parent Name', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Actions', 'rtwalwm-wp-wc-affiliate-program' ); ?></th>
			  	</tr>
		  	</tfoot>
		</table>
    </div>
    <?php include_once( RTWALWM_DIR . '/admin/partials/rtwalwm_tabs/rtwalwm_footer.php' ); ?>
</div>



