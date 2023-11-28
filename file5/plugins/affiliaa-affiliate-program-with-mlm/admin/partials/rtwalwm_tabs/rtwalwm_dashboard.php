<?php
	global $wpdb;

	$rtwalwm_extra_features = get_option( 'rtwwwap_extra_features_opt' );
	if( RTWALWM_IS_WOO == 1 ){
		$rtwalwm_currency_sym = get_woocommerce_currency_symbol();		
	}
	else{
		
		$rtwalwm_currency_sym 	= esc_html__( '&#36;', 'rtwalwm-wp-wc-affiliate-program' );
	
	}
	$rtwalwm_decimal_places = isset($rtwalwm_extra_features['decimal_places']) ? $rtwalwm_extra_features['decimal_places']:'2';
	// isset($rtwwwap_decimal['decimal_places']) ? $rtwwwap_decimal['decimal_places'] : "2" ;
	$rtwalwm_affiliates 		= get_users( array( 'meta_key' => 'rtwwwap_affiliate', 'meta_value' => '1' ) );
	$rtwalwm_total_affiliates 	= ( !empty( $rtwalwm_affiliates ) ) ? count( $rtwalwm_affiliates ) : '0';
	$rtwalwm_total_referrals 	= $wpdb->get_var( "SELECT COUNT(`id`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `type` != 3" );
	$rtwalwm_pending_comm 		= $wpdb->get_var( $wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `status`=%d AND `type` != %d", 0, 3 ) );
	$rtwalwm_approved_comm 		= $wpdb->get_var( $wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `status`=%d  AND `type` != %d", 1, 3 ) );
	$rtwalwm_total_comm 		= $wpdb->get_var( $wpdb->prepare( "SELECT SUM(`amount`) FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `status` != %d  AND `type` != %d", 3, 3 ) );

	$rtwalwm_last_5 			= $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `type` != %d ORDER BY `date` DESC LIMIT %d", 3, 5 ), ARRAY_A );
	$rtwalwm_top_5 				= $wpdb->get_results( $wpdb->prepare( "SELECT SUM( `amount` ) as amount, COUNT( `id` ) as count, `aff_id` FROM ".$wpdb->prefix."rtwwwap_referrals WHERE `status` != %d AND `type` != %d GROUP BY `aff_id` ORDER BY amount DESC LIMIT %d", 3, 3, 5 ), ARRAY_A );
	
?>

<div id="overview">
	<div class="main-wrapper">
		<div class="box-column-row">
			<div class="box-column" id="rtwalwm_total_affiliates">
				<div class="box-content">
					<?php printf( '<h4 class="box-column-heading">%s</h4>', esc_html__( 'Total Affiliates', 'rtwalwm-wp-wc-affiliate-program' ) ); ?>
					<div class="box-content-desc">
						<div class="box-content-icon"><img src="<?php echo esc_url( RTWALWM_URL.'assets/images/affiliate.png' ); ?>" alt=""></div>
						<div class="box-content-right">
								<?php printf( '<span class="box-content-value">%u</span>',  isset( $rtwalwm_total_affiliates ) ? esc_html( $rtwalwm_total_affiliates ) : esc_html( '0' ) ); ?>
							    <p><?php esc_html_e( 'Total no. of affiliates registered', 'rtwalwm-wp-wc-affiliate-program' ); ?></p>
						</div>
					</div>

				</div>
			</div>
			<div class="box-column" id="rtwalwm_total_referrals">
				<div class="box-content">
					<?php printf( '<h4 class="box-column-heading">%s</h4>', esc_html__( 'Total Referrals', 'rtwalwm-wp-wc-affiliate-program' ) ); ?>
					<div class="box-content-desc">
						<div class="box-content-icon"><img src="<?php echo esc_url( RTWALWM_URL.'assets/images/total_referrals.png' ); ?>" alt=""></div>
						<div class="box-content-right">
						<?php printf( '<span class="box-content-value">%u</span>', isset( $rtwalwm_total_referrals ) ? esc_html( $rtwalwm_total_referrals ): esc_html( '0' ) ); ?>
					    <p><?php esc_html_e( 'Total no. of Referrals done', 'rtwalwm-wp-wc-affiliate-program' ); ?></p>
					</div>
					</div>
				</div>
			</div>
			<div class="box-column" id="rtwalwm_total_comm">
				<div class="box-content">
					<?php printf( '<h4 class="box-column-heading">%s</h4>', esc_html__( 'Total Commission', 'rtwalwm-wp-wc-affiliate-program' ) ); ?>
					<div class="box-content-desc">
						<div class="box-content-icon"><img src="<?php echo esc_url( RTWALWM_URL.'assets/images/total_commission.png' ); ?>" alt=""></div>
						<div class="box-content-right">
						<?php printf( '<span class="box-content-value">'. $rtwalwm_currency_sym.number_format( $rtwalwm_total_comm ,$rtwalwm_decimal_places ).'</span>' ); ?>
					    <p><?php esc_html_e( 'Total Commission generated from Referrals', 'rtwalwm-wp-wc-affiliate-program' ); ?></p>
					</div>
					</div>
				</div>
			</div>
			<div class="box-column" id="rtwalwm_approved_comm">
				<div class="box-content">
					<?php printf( '<h4 class="box-column-heading">%s</h4>', esc_html__( 'Approved Commission', 'rtwalwm-wp-wc-affiliate-program' ) ); ?>
					<div class="box-content-desc">
						<div class="box-content-icon"><img src="<?php echo esc_url( RTWALWM_URL.'assets/images/approved_commission.png' ); ?>" alt=""></div>
						<div class="box-content-right">
						<?php printf( '<span class="box-content-value">'. $rtwalwm_currency_sym.number_format( $rtwalwm_approved_comm ,$rtwalwm_decimal_places ).'</span>' ); ?>
					    <p><?php esc_html_e( 'Total Commission that needs to be paid', 'rtwalwm-wp-wc-affiliate-program' ); ?></p>
					</div>
					</div>
				</div>
			</div>
			<div class="box-column" id="rtwalwm_pending_comm">
				<div class="box-content">
					<?php printf( '<h4 class="box-column-heading">%s</h4>', esc_html__( 'Pending Commission', 'rtwalwm-wp-wc-affiliate-program' ) ); ?>
					<div class="box-content-desc">
						<div class="box-content-icon"><img src="<?php echo esc_url( RTWALWM_URL.'assets/images/pending_commission.png' ); ?>" alt=""></div>
						<div class="box-content-right">
						<?php printf( '<span class="box-content-value">'. $rtwalwm_currency_sym.number_format( $rtwalwm_pending_comm ,$rtwalwm_decimal_places ).'</span>' ); ?>
					    <p><?php esc_html_e( 'Total Commission that needs to be reviewed', 'rtwalwm-wp-wc-affiliate-program' ); ?></p>
					</div>
					</div>
				</div>
			</div>
		</div>
	<div class="rtwalwm-referrals-row">
		<div class="rtwalwm-referrals-column">
			<div class="rtwalwm-referrals-column-content">
				<div class="rtwalwm-referrals-header">
					<h3>
						<?php printf( '%s', esc_html__( 'Last 5 Referrals', 'rtwalwm-wp-wc-affiliate-program' ) ); ?>
					</h3>
					<nav class="rtwalwm-nav">
						<a href="javascript:void(0);" class="rtwalwm-referrals-accordian-icon"><span class="dashicons dashicons-arrow-up-alt2"></span></a>
						<a href="javascript:void(0);" class="rtwalwm-referrals-close-icon"><span class="dashicons dashicons-no-alt"></span></a>
					</nav>
				</div>
				<ul>
					<?php
						$rtwalwm_date_format = get_option( 'date_format' );
						$rtwalwm_time_format = get_option( 'time_format' );

						foreach( $rtwalwm_last_5 as $rtwalwm_key => $rtwalwm_value ){

							$rtwalwm_aff_info = get_userdata( $rtwalwm_value[ 'aff_id' ] );
							if( $rtwalwm_aff_info ){
								$rtwalwm_aff_name = $rtwalwm_aff_info->user_login;
					?>
								<li>
									<?php
										$rtwalwm_date_time_format = $rtwalwm_date_format.' '.$rtwalwm_time_format;
										$rtwalwm_local_date = get_date_from_gmt( date( 'Y-m-d H:i:s', strtotime( $rtwalwm_value[ 'date' ] ) ), $rtwalwm_date_time_format );
									?>
									<?php printf( ' %s %s %s %s %s %s', esc_html( $rtwalwm_currency_sym ), esc_html( number_format($rtwalwm_value[ 'amount' ],$rtwalwm_decimal_places,'.',',') ), esc_html__( 'for', 'rtwalwm-wp-wc-affiliate-program' ), esc_html( $rtwalwm_aff_name ), esc_html__( 'on', 'rtwalwm-wp-wc-affiliate-program' ), esc_html( $rtwalwm_local_date ) ); ?>
								</li>
					<?php
							}
						}
					?>
				</ul>
			</div>
		</div>
		<div class="rtwalwm-referrals-column">
			<div class="rtwalwm-referrals-column-content">
				<div class="rtwalwm-referrals-header">
					<h3>
						<?php printf( '%s', esc_html__( 'Top 5 affiliates', 'rtwalwm-wp-wc-affiliate-program' ) ); ?>
					</h3>
					<nav class="rtwalwm-nav">
						<a href="javascript:void(0);" class="rtwalwm-referrals-accordian-icon"><span class="dashicons dashicons-arrow-up-alt2"></span></a>
						<a href="javascript:void(0);" class="rtwalwm-referrals-close-icon"><span class="dashicons dashicons-no-alt"></span></a>
					</nav>
				</div>
				<ol>
					<?php
						foreach( $rtwalwm_top_5 as $rtwalwm_key1 => $rtwalwm_value1 ){
							$rtwalwm_aff_info = get_userdata( $rtwalwm_value1[ 'aff_id' ] );
							if( $rtwalwm_aff_info ){
								$rtwalwm_aff_name = $rtwalwm_aff_info->user_login;
					?>
								<li>
									<span class="rtwalwm-key-count"><?php echo esc_html( $rtwalwm_key1+1 ); ?></span>
									<p><?php printf( '%s (%u)', esc_html( $rtwalwm_aff_name ), esc_html( $rtwalwm_value1[ 'count' ] ) ); ?></p>
									<p><?php printf( '%s %u | %s %s %s', esc_html__( 'Referrals', 'rtwalwm-wp-wc-affiliate-program' ), esc_html( $rtwalwm_value1[ 'count' ] ), esc_html__( 'Total Amount', 'rtwalwm-wp-wc-affiliate-program' ), esc_html( $rtwalwm_currency_sym ), esc_html( number_format($rtwalwm_value1[ 'amount' ],$rtwalwm_decimal_places,'.',',') ) ); ?></p>
								</li>
					<?php
							}
						}
					?>
				</ol>
			</div>
		</div>
	</div>
	<?php include_once( RTWALWM_DIR . '/admin/partials/rtwalwm_tabs/rtwalwm_footer.php' ); ?>
 </div>
</div>
