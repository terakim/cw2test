<?php
	//get_option('rtwwwap_track_opt');
	global $wpdb;
	$rtwwwap_args = array(
							'meta_key' 		=> 'rtwwwap_affiliate',
							'meta_value' 	=> '1',
							'orderby' 		=> 'id',
							'order'			=> 'DESC'
						);

	$rtwwwap_users = get_users( $rtwwwap_args );

	$rtwwwap_user_id = get_current_user_id();

	$table_name = $wpdb->prefix . "rtwwwap_visitors_track";

	$rtwwwap_all_track 	= $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."rtwwwap_visitors_track ORDER BY `id`", ARRAY_A );
?>

<div class="main-wrapper">
	<div class="rtwwwap-data-table-wrapper">
		<table class="rtwwwap_affiliates_traffic_table rtwwwap_data_table stripe" class="display dtr-inline" cellspacing="0">
		  	<thead>
			  	<tr>
				  	<th><?php esc_html_e( 'Sn', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
					<th><?php esc_html_e( 'IP', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
					<th><?php esc_html_e( 'URL', 'rtwwwap-wp-wc-affiliate-program' ); ?></th> 
					<th><?php esc_html_e( 'Visits', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
			    	<th><?php esc_html_e( 'Browser', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
					<th><?php esc_html_e( 'Device', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>  
					<th><?php esc_html_e( 'Platform', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
					<th><?php esc_html_e( 'Date', 'rtwwwap-wp-wc-affiliate-program' ); ?></th> 
			  	</tr>
		  	</thead>
			<tbody>
				<?php
				if(!empty($rtwwwap_all_track))
				{
						$rtwwwap_date_format = get_option( 'date_format' );
						$rtwwwap_time_format = get_option( 'time_format' );
						foreach( $rtwwwap_all_track as $rtwwwap_key => $rtwwwap_value ){
					?>
					<tr>
						<td>
							<?php echo esc_html( $rtwwwap_value[ 'id' ] ); ?>
						</td>
						<td>
							<?php
								echo esc_html($rtwwwap_value['ip']);
							?>
						</td>
						<td>
							<?php
								echo esc_html($rtwwwap_value['ref_link']);
							?>
						</td>
						<td>
							<?php
								echo esc_html($rtwwwap_value['count'])
							?>
						</td>
						<td>
							<?php 					 
								echo esc_html($rtwwwap_value['agent']);
							?>
						</td>
						<td>
							<?php 
								if ( $rtwwwap_value['device'] == 'Mobile') {
									?>
									<div class="rtwwwap_device"><img src="<?php echo esc_url( RTWWWAP_URL.'assets/images/mobile.jpg' ); ?>" alt=""></div>
									<?php
								} else if ( $rtwwwap_value['device'] == 'Tablet') {
									?>
									<div class="rtwwwap_device"><img src="<?php echo esc_url( RTWWWAP_URL.'assets/images/tablet.png' ); ?>" alt=""></div>
									<?php
								} else {
									?>
									<div class="rtwwwap_device"><img src="<?php echo esc_url( RTWWWAP_URL.'assets/images/desktop.png' ); ?>" alt=""></div>
									<?php
								}
							?>
						</td>
						<td>
							<?php 					 
								echo esc_html($rtwwwap_value['platform']);
							?>
						</td>
						<td>
							<?php
								echo esc_html($rtwwwap_value['date']);
							?>
						</td>				
					</tr>
					<?php
						}
				} 
				else
				{
					?>
					<tr>
						<td colspan="8">
						<?php esc_html_e( 'No Data Found', 'rtwwwap-wp-wc-affiliate-program' ); ?>
						</td>
					</tr>
				<?php
				}
				?>					
			</tbody>		  	
			<tfoot>
			  	<tr>
				  	<th><?php esc_html_e( 'ID', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
					<th><?php esc_html_e( 'IP', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
					<th><?php esc_html_e( 'Visitors URL', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
					<th><?php esc_html_e( 'Visits', 'rtwwwap-wp-wc-affiliate-program' ); ?></th> 
			    	<th><?php esc_html_e( 'Browser', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
						<th><?php esc_html_e( 'Device', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>  
					<th><?php esc_html_e( 'Platform', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>
					<th><?php esc_html_e( 'Date', 'rtwwwap-wp-wc-affiliate-program' ); ?></th>  
			  	</tr>
		  	</tfoot>
		</table>
    </div>
    <?php include_once( RTWWWAP_DIR . '/admin/partials/rtwwwap_tabs/rtwwwap_footer.php' ); ?>
</div>
