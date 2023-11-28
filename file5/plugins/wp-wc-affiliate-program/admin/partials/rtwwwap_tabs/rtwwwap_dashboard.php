<?php
	global $wpdb;

	$rtwwwap_extra_features = get_option( 'rtwwwap_extra_features_opt' );
	if( RTWWWAP_IS_WOO == 1 ){
		$rtwwwap_currency_sym = get_woocommerce_currency_symbol();		
	}
	else{
		require_once( RTWWWAP_DIR.'includes/rtwaffiliatehelper.php' );

		$rtwwwap_currency 		= isset( $rtwwwap_extra_features[ 'currency' ] ) ? $rtwwwap_extra_features[ 'currency' ] : 'USD';
		$rtwwwap_curr_obj 		= new RtwAffiliateHelper();
		$rtwwwap_currency_sym 	= $rtwwwap_curr_obj->rtwwwap_curr_symbol( $rtwwwap_currency );
	}
	$rtwwwap_decimal_places = isset($rtwwwap_decimal['decimal_places']) ? $rtwwwap_decimal['decimal_places'] : "2" ;
	$rtwwwap_decimal_separator = isset($rtwwwap_decimal['decimal_separator']) ? $rtwwwap_decimal['decimal_separator'] : ".";
	$rtwwwap_thousand_separator = isset($rtwwwap_decimal['thousand__separator']) ? $rtwwwap_decimal['thousand__separator'] : ",";
	
	$rtwwwap_affiliates 		= get_users( array( 'meta_key' => 'rtwwwap_affiliate', 'meta_value' => '1' ) );
	$rtwwwap_total_affiliates 	= ( !empty( $rtwwwap_affiliates ) ) ? count( $rtwwwap_affiliates ) : '0';
	$rtwwwap_total_referrals 	= $wpdb->get_var( "SELECT COUNT(`id`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `type` != 3" );
	$rtwwwap_pending_comm 		= $wpdb->get_var( $wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `status`=%d AND `type` != %d", 0, 3 ) );

	$rtwwwap_pending_comm = isset($rtwwwap_pending_comm) && !empty($rtwwwap_pending_comm)? $rtwwwap_pending_comm : 0;

	$rtwwwap_approved_comm 		= $wpdb->get_var( $wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `status`=%d  AND `type` != %d", 1, 3 ) );

	$rtwwwap_approved_comm = isset($rtwwwap_approved_comm) && !empty($rtwwwap_approved_comm)? $rtwwwap_approved_comm : 0;

	$rtwwwap_total_comm 		= $wpdb->get_var( $wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `status` != %d  AND `type` != %d", 3, 3 ) );

	$rtwwwap_total_comm = isset($rtwwwap_total_comm) && !empty($rtwwwap_total_comm)? $rtwwwap_total_comm : 0;

	$rtwwwap_last_5 			= $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `type` != %d ORDER BY `date` DESC LIMIT %d", 3, 5 ), ARRAY_A );
	$rtwwwap_top_5 				= $wpdb->get_results( $wpdb->prepare( "SELECT SUM( `amount` ) as amount, COUNT( `id` ) as count, `aff_id` FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `status` != %d AND `type` != %d GROUP BY `aff_id` ORDER BY amount DESC LIMIT %d", 3, 3, 5 ), ARRAY_A );
?>

<div id="overview">
	<div class="main-wrapper">
		<div class="box-column-row">
			<div class="box-column" id="rtwwwap_total_affiliates">
				<div class="box-content">
					<?php printf( '<h4 class="box-column-heading">%s</h4>', esc_html__( 'Total Affiliates', 'rtwwwap-wp-wc-affiliate-program' ) ); ?>
					<div class="box-content-desc">
						<div class="box-content-icon"><img src="<?php echo esc_url( RTWWWAP_URL.'assets/images/affiliate.png' ); ?>" alt=""></div>
						<div class="box-content-right">
								<?php printf( '<span class="box-content-value">%u</span>', ( $rtwwwap_total_affiliates ) ? esc_html( $rtwwwap_total_affiliates ) : esc_html( '0' ) ); ?>
							    <p><?php esc_html_e( 'Total no. of affiliates registered', 'rtwwwap-wp-wc-affiliate-program' ); ?></p>
						</div>
					</div>

				</div>
			</div>
			<div class="box-column" id="rtwwwap_total_referrals">
				<div class="box-content">
					<?php printf( '<h4 class="box-column-heading">%s</h4>', esc_html__( 'Total Referrals', 'rtwwwap-wp-wc-affiliate-program' ) ); ?>
					<div class="box-content-desc">
						<div class="box-content-icon"><img src="<?php echo esc_url( RTWWWAP_URL.'assets/images/total_referrals.png' ); ?>" alt=""></div>
						<div class="box-content-right">
						<?php printf( '<span class="box-content-value">%u</span>', ( $rtwwwap_total_referrals ) ? esc_html( $rtwwwap_total_referrals ): esc_html( '0' ) ); ?>
					    <p><?php esc_html_e( 'Total no. of Referrals done', 'rtwwwap-wp-wc-affiliate-program' ); ?></p>
					</div>
					</div>
				</div>
			</div>
			<div class="box-column" id="rtwwwap_total_comm">
				<div class="box-content">
					<?php printf( '<h4 class="box-column-heading">%s</h4>', esc_html__( 'Total Commission', 'rtwwwap-wp-wc-affiliate-program' ) ); ?>
					<div class="box-content-desc">
						<div class="box-content-icon"><img src="<?php echo esc_url( RTWWWAP_URL.'assets/images/total_commission.png' ); ?>" alt=""></div>
						<div class="box-content-right">
						<?php 
						printf( '<span class="box-content-value">'.$rtwwwap_currency_sym.number_format( $rtwwwap_total_comm,$rtwwwap_decimal_places,$rtwwwap_decimal_separator, $rtwwwap_thousand_separator).'</span>'); ?>
					    <p><?php esc_html_e( 'Total Commission generated from Referrals', 'rtwwwap-wp-wc-affiliate-program' ); ?></p>
					</div>
					</div>
				</div>
			</div>
			<div class="box-column" id="rtwwwap_approved_comm">
				<div class="box-content">
					<?php printf( '<h4 class="box-column-heading">%s</h4>', esc_html__( 'Approved Commission', 'rtwwwap-wp-wc-affiliate-program' ) ); ?>
					<div class="box-content-desc">
						<div class="box-content-icon"><img src="<?php echo esc_url( RTWWWAP_URL.'assets/images/approved_commission.png' ); ?>" alt=""></div>
						<div class="box-content-right">
						<?php
						 printf( '<span class="box-content-value">'.$rtwwwap_currency_sym.number_format( $rtwwwap_approved_comm,$rtwwwap_decimal_places,$rtwwwap_decimal_separator, $rtwwwap_thousand_separator).'</span>');
						  ?>
					    <p><?php esc_html_e( 'Total Commission that needs to be paid', 'rtwwwap-wp-wc-affiliate-program' ); ?></p>
					</div>
					</div>
				</div>
			</div>
			<div class="box-column" id="rtwwwap_pending_comm">
				<div class="box-content">
					<?php printf( '<h4 class="box-column-heading">%s</h4>', esc_html__( 'Pending Commission', 'rtwwwap-wp-wc-affiliate-program' ) ); ?>
					<div class="box-content-desc">
						<div class="box-content-icon"><img src="<?php echo esc_url( RTWWWAP_URL.'assets/images/pending_commission.png' ); ?>" alt=""></div>
						<div class="box-content-right">
						<?php 
						 printf( '<span class="box-content-value">'.$rtwwwap_currency_sym.number_format( $rtwwwap_pending_comm,$rtwwwap_decimal_places,$rtwwwap_decimal_separator, $rtwwwap_thousand_separator).'</span>'); ?>
					    <p><?php esc_html_e( 'Total Commission that needs to be reviewed', 'rtwwwap-wp-wc-affiliate-program' ); ?></p>
					</div>
					</div>
				</div>
			</div>
		</div>
	<div class="rtwwwap-referrals-row">
		<div class="rtwwwap-referrals-column">
			<div class="rtwwwap-referrals-column-content">
				<div class="rtwwwap-referrals-header">
					<h3>
						<?php printf( '%s', esc_html__( 'Last 5 Referrals', 'rtwwwap-wp-wc-affiliate-program' ) ); ?>
					</h3>
					<nav class="rtwwwap-nav">
						<a href="javascript:void(0);" class="rtwwwap-referrals-accordian-icon"><span class="dashicons dashicons-arrow-up-alt2"></span></a>
						<a href="javascript:void(0);" class="rtwwwap-referrals-close-icon"><span class="dashicons dashicons-no-alt"></span></a>
					</nav>
				</div>
				<ul>
					<?php
						$rtwwwap_date_format = get_option( 'date_format' );
						$rtwwwap_time_format = get_option( 'time_format' );

						foreach( $rtwwwap_last_5 as $rtwwwap_key => $rtwwwap_value ){
							$rtwwwap_aff_info = get_userdata( $rtwwwap_value[ 'aff_id' ] );
							if( $rtwwwap_aff_info ){
								$rtwwwap_aff_name = $rtwwwap_aff_info->user_login;
					?>
								<li>
									<?php
										$rtwwwap_date_time_format = $rtwwwap_date_format.' '.$rtwwwap_time_format;
										$rtwwwap_local_date = get_date_from_gmt( date( 'Y-m-d H:i:s', strtotime( $rtwwwap_value[ 'date' ] ) ), $rtwwwap_date_time_format );
									?>
									<?php printf( ' %s %s %s %s %s %s', esc_html( $rtwwwap_currency_sym ), esc_html( number_format($rtwwwap_value[ 'amount' ],$rtwwwap_decimal_places,$rtwwwap_decimal_separator,$rtwwwap_thousand_separator) ), esc_html__( 'for', 'rtwwwap-wp-wc-affiliate-program' ), esc_html( $rtwwwap_aff_name ), esc_html__( 'on', 'rtwwwap-wp-wc-affiliate-program' ), esc_html( $rtwwwap_local_date ) ); ?>
								</li>
					<?php
							}
						}
					?>
				</ul>
			</div>
		</div>
		<div class="rtwwwap-referrals-column">
			<div class="rtwwwap-referrals-column-content">
				<div class="rtwwwap-referrals-header">
					<h3>
						<?php printf( '%s', esc_html__( 'Top 5 affiliates', 'rtwwwap-wp-wc-affiliate-program' ) ); ?>
					</h3>
					<nav class="rtwwwap-nav">
						<a href="javascript:void(0);" class="rtwwwap-referrals-accordian-icon"><span class="dashicons dashicons-arrow-up-alt2"></span></a>
						<a href="javascript:void(0);" class="rtwwwap-referrals-close-icon"><span class="dashicons dashicons-no-alt"></span></a>
					</nav>
				</div>
				<ol>
					<?php
						foreach( $rtwwwap_top_5 as $rtwwwap_key1 => $rtwwwap_value1 ){
							$rtwwwap_aff_info = get_userdata( $rtwwwap_value1[ 'aff_id' ] );
							if( $rtwwwap_aff_info ){
								$rtwwwap_aff_name = $rtwwwap_aff_info->user_login;
								
					?>
								<li>
									<span class="rtwwwap-key-count"><?php echo esc_html( $rtwwwap_key1+1 ); ?></span>
									<p><?php printf( '%s (%u)', esc_html( $rtwwwap_aff_name ), esc_html( $rtwwwap_value1[ 'count' ] ) ); ?></p>
									<p><?php printf( '%s %u | %s %s %s', esc_html__( 'Referrals', 'rtwwwap-wp-wc-affiliate-program' ), esc_html( $rtwwwap_value1[ 'count' ] ), esc_html__( 'Total Amount', 'rtwwwap-wp-wc-affiliate-program' ), esc_html( $rtwwwap_currency_sym ), esc_html( number_format($rtwwwap_value1[ 'amount' ],$rtwwwap_decimal_places,$rtwwwap_decimal_separator,$rtwwwap_thousand_separator) ) ); ?></p>
								</li>
					<?php
							}
						}
					?>
				</ol>
			</div>
		</div>
	</div>
	<?php include_once( RTWWWAP_DIR . '/admin/partials/rtwwwap_tabs/rtwwwap_footer.php' );
	?>
 </div>
</div>
