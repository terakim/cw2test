<?php
	$rtwwwap_levels_settings 		= get_option( 'rtwwwap_levels_settings_opt' );
	$rtwwwap_commission_settings 	= get_option( 'rtwwwap_commission_settings_opt' );
	$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );
	$rtwwwap_comm_base = isset( $rtwwwap_commission_settings[ 'comm_base' ] ) ? $rtwwwap_commission_settings[ 'comm_base' ] : '1';

	$rtwwwap_decimal_place = isset($rtwwwap_decimal['decimal_places']) ? $rtwwwap_decimal['decimal_places'] : "2" ;
	$rtwwwap_decimal_separator = isset($rtwwwap_decimal['decimal_separator']) ? $rtwwwap_decimal['decimal_separator'] : ".";
	$rtwwwap_thousand_separator = isset($rtwwwap_decimal['thousand__separator']) ? $rtwwwap_decimal['thousand__separator'] : ",";
	// $rtwwwap_decimal_places = '';
	$rtwwwap_currency = '' ;

	if( RTWWWAP_IS_WOO == 1 ){
		$rtwwwap_currency_sym = get_woocommerce_currency_symbol();
	}
	else{
		require_once( RTWWWAP_DIR.'includes/rtwaffiliatehelper.php' );

		$rtwwwap_currency 		= isset( $rtwwwap_extra_features[ 'currency' ] ) ? $rtwwwap_extra_features[ 'currency' ] : 'USD';
		$rtwwwap_curr_obj 		= new RtwAffiliateHelper();
		$rtwwwap_currency_sym 	= $rtwwwap_curr_obj->rtwwwap_curr_symbol( $rtwwwap_currency );
	}
?>

<?php if( $rtwwwap_comm_base == 1 ){ ?>
	<div class="main-wrapper">
		<p class="rtwwwap_levels_inactive"><?php esc_html_e( 'Please select "Commission Based on = Users" in Commission Setting Tab', 'rtwwwap-wp-wc-affiliate-program' ); ?></p>
		<?php include_once( RTWWWAP_DIR . '/admin/partials/rtwwwap_tabs/rtwwwap_footer.php' ); ?>
	</div>
<?php }else{ ?>
	<p class="rtwwwap_add_new_level">
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=rtwwwap&rtwwwap_tab=rtwwwap_levels_add_edit' ) ); ?>" target="_blank" >
			<input type="button" value="<?php esc_attr_e( 'Add New Level', 'rtwwwap-wp-wc-affiliate-program' ); ?>" class="rtwwwap-button" name="rtwwwap_add_new_level" />
		</a>
		<a href="javascript:void(0);">
			<input type="button" value="<?php esc_attr_e( 'Update Order', 'rtwwwap-wp-wc-affiliate-program' ); ?>" class="rtwwwap-button rtwwwap_update_level_order" name="rtwwwap_update_level_order" />
		</a>
	</p>

	<div class="main-wrapper">
		<div class="rtwwwap-data-table-wrapper">
			<table class="rtwwwap_levels_table rtwwwap_data_table stripe" class="display dtr-inline" cellspacing="0">
			  	<thead>
				  	<tr>
				    	<th><?php esc_html_e( 'Sort', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
				    	<th><?php esc_html_e( 'Level', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
				    	<th><?php esc_html_e( 'Name', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
				    	<th><?php esc_html_e( 'Commission', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
				    	<th><?php esc_html_e( 'To Reach', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
				    	<th><?php esc_html_e( 'Actions', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
				  	</tr>
			  	</thead>
			  	<tbody>
			  	<?php
			  		if( !empty( $rtwwwap_levels_settings ) ){
			  			foreach( $rtwwwap_levels_settings as $rtwwwap_levels_key => $rtwwwap_levels_val ){
			  	?>
						  	<tr data-level_id="<?php echo esc_html( $rtwwwap_levels_key ); ?>">
						    	<td>
						    		<span class="dashicons dashicons-move">
						    			<span class="rtwwwap_level_order_hide">
						    				<?php echo esc_html( $rtwwwap_levels_key ); ?>
						    			</span>
						    		</span>
						    	</td>

						  		<td>
						    		<?php echo esc_html( $rtwwwap_levels_key ); ?>
						    	</td>

						    	<td>
						    		<?php echo esc_html( $rtwwwap_levels_val[ 'level_name' ] ); ?>
						    	</td>
						    	<td>
						    		<?php
						    			if( $rtwwwap_levels_val[ 'level_commission_type' ] == '0' )
						    			{
						    				// printf( '%s%s', esc_html( number_format( $rtwwwap_levels_val[ 'level_comm_amount' ],$rtwwwap_decimal_place,$rtwwwap_decimal_separator, $rtwwwap_thousand_separator )), '%' );

											echo esc_html($rtwwwap_levels_val[ 'level_comm_amount' ]).'%';
						    			}
						    			elseif( $rtwwwap_levels_val[ 'level_commission_type' ] == '1' )
						    			{
											// $rtwwwap_decimal_places = $rtwwwap_extra_features['decimal_places'].'f';
						    				// printf( '%s%s'.$rtwwwap_decimal_place, $rtwwwap_currency, esc_html( number_format( $rtwwwap_levels_val[ 'level_comm_amount' ],$rtwwwap_decimal_place,$rtwwwap_decimal_separator, $rtwwwap_thousand_separator )) );

											echo esc_html($rtwwwap_levels_val[ 'level_comm_amount' ]);

						    			}
						    		?>
						    	</td>

						    	<td>
						    		<?php
						    			if( $rtwwwap_levels_val[ 'level_criteria_type' ] == 0 )
						    			{
						    				printf( '%s', esc_html__( 'Become Affiliate' ) );
						    			}
						    			elseif( $rtwwwap_levels_val[ 'level_criteria_type' ] == 1 )
						    			{
						    				printf( '%s %s', esc_html__( 'No. of referrals' ), esc_html__( $rtwwwap_levels_val[ 'level_criteria_val' ] ) );
						    			}
						    			elseif( $rtwwwap_levels_val[ 'level_criteria_type' ] == 2 )
						    			{
											printf( esc_html__( 'Total sale amount ' ).$rtwwwap_currency_sym.number_format( $rtwwwap_levels_val[ 'level_criteria_val' ],0	,0, $rtwwwap_thousand_separator));
						    				
						    			}
						    		?>
						    	</td>	

						    	<td>
						    		<a class="rtwwwap-edit-link" href="<?php echo esc_url( admin_url( 'admin.php?page=rtwwwap&rtwwwap_tab=rtwwwap_levels_add_edit&edit=' ).$rtwwwap_levels_key ); ?>" target="_blank">
						    			<span class="dashicons dashicons-edit"></span>
						    		</a>
						    		<a class="rtwwwap-delete-link rtwwwap_level_delete" href="javascript:void(0);">
						    			<span class="dashicons dashicons-trash"></span>
						    		</a>
						    	</td>
						  	</tr>
				<?php 	}
					}
				?>
				</tbody>
				<tfoot>
				  	<tr>
				  		<th><?php esc_html_e( 'Sort', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
				  		<th><?php esc_html_e( 'Level', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
				    	<th><?php esc_html_e( 'Name', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
				    	<th><?php esc_html_e( 'Commission', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
				    	<th><?php esc_html_e( 'To Reach', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
				    	<th><?php esc_html_e( 'Actions', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
				  	</tr>
			  	</tfoot>
			</table>
	    </div>
	    <?php include_once( RTWWWAP_DIR . '/admin/partials/rtwwwap_tabs/rtwwwap_footer.php' ); ?>
	</div>
<?php } ?>
