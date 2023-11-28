<?php

/**
 * Check whether the current affiliate is a sub-affiliate
 *
 * @since  1.0
 * @return boolean
 */
function affwp_mlm_is_sub_affiliate( $affiliate_id = 0 ) {
	
	if ( empty( $affiliate_id ) ) {
		$affiliate_id = affwp_get_affiliate_id();
	}

	if ( affwp_mlm_get_affiliate_connections( absint( $affiliate_id ) ) ) {
		return true;
	}

	return false;

}

/**
 * Check whether the current affiliate is a parent affiliate
 *
 * @since 1.0
 * @return boolean
 */
function affwp_mlm_is_parent_affiliate( $affiliate_id = 0 ) {
	
	if ( empty( $affiliate_id ) ) {
		$affiliate_id = affwp_get_affiliate_id();
	}

	// Parent affiliates must have sub-affiliates
	if ( affwp_mlm_get_sub_affiliates( absint( $affiliate_id ) ) ) {
		return true;
	}

	return false;
}

/**
 * Check whether the current affiliate can have a new sub affiliate
 *
 * @since 1.0
 * @return boolean
 */
function affwp_mlm_sub_affiliate_allowed( $affiliate_id = 0 ) {

	if ( ! $affiliate_id ) return false;
	
	$allowed = false;
	
	// Make sure affiliate is active
	if ( 'active' !== affwp_get_affiliate_status( $affiliate_id ) ) $allowed = false;
	
	$matrix_depth = affiliate_wp()->settings->get( 'affwp_mlm_matrix_depth' );
	
	// Check if total depth is enabled (Unilevel)
	if ( affiliate_wp()->settings->get( 'affwp_mlm_total_depth' ) ) {
			
		$matrix_level = affwp_mlm_get_matrix_level( $affiliate_id );
		$matrix_level = ( ! empty( $matrix_level ) ) ? intval( $matrix_level ) : 0;
		$sub_level = $matrix_level + 1;
			
		// Check if matrix depth limit has been reached
		if ( $sub_level > $matrix_depth ) $allowed = false;
		
	}
	
	// Check if forced matrix is enabled (Forced Matrix)
	if ( affiliate_wp()->settings->get( 'affwp_mlm_forced_matrix' ) ) {

		$matrix_width = affiliate_wp()->settings->get( 'affwp_mlm_matrix_width' );
		$matrix_width = ! empty( $matrix_width ) ? $matrix_width : 1;
		$extra_width = affiliate_wp()->settings->get( 'affwp_mlm_matrix_width_extra' );
		$matrix_cycles = affiliate_wp()->settings->get( 'affwp_mlm_matrix_cycles' ); // Allowed Cycles
		$matrix_cycles = ! empty( $matrix_cycles ) ? $matrix_cycles : 1;
		$complete_cycles = affwp_mlm_get_complete_cycles( $affiliate_id );
		$complete_cycles = ( empty( $complete_cycles ) ) ? 0 : $complete_cycles;
		$this_cycle = $complete_cycles + 1;
		$next_cycle = $this_cycle + 1;
		$level_1_count = count( affwp_mlm_get_sub_affiliates( absint( $affiliate_id ) ) );	
		
		// Allow if 1st level isn't full
		if ( $level_1_count < $matrix_width ) {
			$allowed = true;
		} else {
			
			$downline = affwp_mlm_get_downline( $affiliate_id );

			// Loop through cycles and see if they are complete
			for ( $cycle_count = 1; $cycle_count <= $matrix_cycles; $cycle_count++ ) {
				
				$current_cycle = $cycle_count;
				
				// Skip to affiliate's next cycle if complete cycles are found (For performance)
				if ( $cycle_count < $complete_cycles ) $cycle_count = $next_cycle;
				
				$cycle_max_width = $matrix_width * $cycle_count;
				$cycle_min_width = $cycle_max_width > 1 ? $cycle_max_width - $matrix_width : 1;
				$new_cycle_allowed = affwp_mlm_new_cycle_allowed( $affiliate_id, $downline, $cycle_count );

				// See if the 1st level for this cycle is full
				if ( $level_1_count == $cycle_max_width ) {

					// Allow if a new cycle is allowed
					if ( $new_cycle_allowed ) {

						$allowed = true;

						do_action( 'affwp_mlm_new_cycle_started', $affiliate_id, $downline, $cycle_count );
						break;
					}

				} else {

					// Allow a sub affiliate, but not a new cycle
					if ( $level_1_count < $cycle_max_width ) {

						$allowed = true;

						// Don't allow if the final cycle has been completed
						if ( $complete_cycles == $matrix_cycles ) $allowed = false;

						// Prevent new cycle if not allowed
						if ( ! $new_cycle_allowed ) {
							
							// Check the current cycle
							if ( $cycle_count == $next_cycle ) {

								$current_cycle_allowed = affwp_mlm_new_cycle_allowed( $affiliate_id, $downline, $this_cycle );
								
								// Allow sub affilate for current cycle only (Filterable)
								if ( ! $current_cycle_allowed ) { 
									$allowed = false;
								} else {

									$this_cycle_max_width = $matrix_width * $this_cycle;

									// Allow sub affilate for current cycle only (Unfiltered)
									if ( $level_1_count < $this_cycle_max_width ) {
										$allowed = true;
									} else {
										$allowed = false;
									}										
									
								}
								
							}							

						} 


					}

				}		
								
			}	
		}
		
	} else {
		$allowed = true;
	}
	
	return apply_filters( 'affwp_mlm_sub_affiliate_allowed', $allowed, $affiliate_id );
}

/**
 * Check whether the current affiliate has completed a level
 *
 * @since 1.1.1
 * @return boolean
 */
function affwp_mlm_is_level_complete( $affiliate_id = 0, $downline = array(), $level = 0, $cycle = 0 ) {
	
	if ( ! isset( $downline ) ) $downline = affwp_mlm_get_downline( $affiliate_id, 1 );
	
	// Remove current affiliate (Level 0)
	if ( isset( $downline[0] ) ) unset( $downline[0] );
	
	if ( empty( $level ) ) $level = 1;
	
	if ( empty( $cycle ) ) $cycle = 1;
	
	$forced_matrix = affiliate_wp()->settings->get( 'affwp_mlm_forced_matrix' );
	$matrix_width = affiliate_wp()->settings->get( 'affwp_mlm_matrix_width' );
	
	if ( ! $forced_matrix || empty( $matrix_width ) ) return false;
	
	$complete = false;
	$level_subs = $downline[ $level ];
	$level_sub_count = count( $level_subs );
	$max_level_subs = pow( $matrix_width, $level ) * $cycle;

	if ( $level_sub_count >= $max_level_subs ) $complete = true;

	return apply_filters( 'affwp_mlm_level_complete', $complete, $affiliate_id, $level_subs, $level, $cycle, $max_level_subs );
}

/**
 * Save the Affiliate's Completed Cycles
 *
 * @since  1.1.1
 * @return void
 */
function affwp_mlm_set_complete_cycles( $affiliate_id = 0, $cycle = 0 ) {
	
	if ( empty( $affiliate_id ) ) return;

	$old_cycles = affwp_mlm_get_complete_cycles( $affiliate_id );
	
	do_action( 'affwp_mlm_pre_set_complete_cycles', $affiliate_id, $cycle, $old_cycles );
	
	// Update complete cycles
	if ( $old_cycles != $cycle ) {
		affwp_update_affiliate_meta( $affiliate_id, 'complete_cycles', $cycle );
	} else {
		return;
	}

	$new_cycles = $cycle;

	do_action( 'affwp_mlm_post_set_complete_cycles', $affiliate_id, $new_cycles, $old_cycles );

}

/**
 * Check whether the current affiliate has completed a given cycle
 *
 * @since 1.1.1
 * @return boolean
 */
function affwp_mlm_is_cycle_complete( $affiliate_id = 0, $downline = array(), $cycle = 0 ) {
	
	if ( empty( $affiliate_id ) ) return false;
	
	if ( ! isset( $downline ) ) $downline = affwp_mlm_get_downline( $affiliate_id );	
	
	if ( empty( $cycle ) ) $cycle = 1;
	
	$matrix_width = affiliate_wp()->settings->get( 'affwp_mlm_matrix_width' );
	$matrix_depth = affiliate_wp()->settings->get( 'affwp_mlm_matrix_depth' );
	
	if ( empty( $matrix_width ) || empty( $matrix_depth ) ) return false;
				
	// Remove current affiliate (Level 0)
	if ( isset( $downline[0] ) ) unset( $downline[0] );
	
	$level_count = 0;
	$complete_levels = 0;

	foreach ( $downline as $lvl ) {

		$level_count++;

		if ( affwp_mlm_is_level_complete( $affiliate_id, $downline, $level_count, $cycle ) ) {
			$complete_levels++;
		}

	}
	
	// Check if all levels are complete in this cycle
	if ( $complete_levels >= $matrix_depth ) return true;
	
	return false;
}

/**
 * Check whether the current affiliate can start a new cycle
 *
 * @since 1.1.1
 * @return boolean
 */
function affwp_mlm_new_cycle_allowed( $affiliate_id = 0, $downline = array(), $cycle = 0 ) {
	
	if ( empty( $affiliate_id ) || ! isset( $downline ) ) return false;
	
	$allowed = false;
	$matrix_width = affiliate_wp()->settings->get( 'affwp_mlm_matrix_width' );
	$matrix_depth = affiliate_wp()->settings->get( 'affwp_mlm_matrix_depth' );
	$matrix_cycles = affiliate_wp()->settings->get( 'affwp_mlm_matrix_cycles' );	
	$matrix_cycles = ( empty( $matrix_cycles ) ) ? 1 : $matrix_cycles;	
	
	// The cycle to check for
	if ( empty( $cycle ) ) {
		$cycle = 2; 
	} else {
		if ( $cycle < 2 ) $cycle = 2;
	}
	
	$last_cycle = $cycle - 1;	
		
	// Allow new cycle if the previous cycle is full
	if ( affwp_mlm_is_cycle_complete( $affiliate_id, $downline, $last_cycle ) ) {
		
		$allowed = true;
		
		$complete_cycles = affwp_mlm_get_complete_cycles( $affiliate_id );
		$complete_cycles = ( empty( $complete_cycles ) ) ? 0 : $complete_cycles;

		// Don't allow if the last cycle has been completed
		if ( $complete_cycles == $matrix_cycles ) $allowed = false;
		
	}
		
	return apply_filters( 'affwp_mlm_new_cycle_allowed', $allowed, $affiliate_id, $downline, $cycle );
}

/**
 * Check to see if an affiliate's upline has completed a new cycle
 * 
 * @since 1.1.6
 */
function affwp_mlm_check_upline_complete_cycles( $affiliate_id = 0 ) {
	
	if ( empty( $affiliate_id ) ) return;
	
	// Check new affiliate's upline for completed cycles
	$upline = affwp_mlm_get_upline( $affiliate_id );

	if ( $upline ) {

		$parent_affiliates = $upline;

		foreach( $parent_affiliates as $parent_id ) {

			$parent_subs = affwp_mlm_get_downline( $parent_id );
			$cycles = affiliate_wp()->settings->get( 'affwp_mlm_matrix_cycles' );
			$cycles = ! empty( $cycles ) ? $cycles : 1;

			$complete_cycles = affwp_mlm_get_complete_cycles( $parent_id );
			$next_cycle = $complete_cycles + 1;

			// Loop through cycles and see if they are complete
			for ( $cycle_count = 1; $cycle_count <= $cycles; $cycle_count++ ) {			
				
				// Skip to affiliate's next cycle if complete cycles are found
				if ( $cycle_count < $complete_cycles ) $cycle_count = $next_cycle;
				
				if ( affwp_mlm_is_cycle_complete( $parent_id, $parent_subs, $cycle_count ) ) {
					
					affwp_mlm_set_complete_cycles( $parent_id, $cycle_count );
					
				} else {
					
					$next_affiliate = true;
					break;
				}
			}

			// Skip to the next upline affiliate if cycle isn't complete
			if ( $next_affiliate == true ) continue;

		}
	}
}

/**
 * Filter an array of Affiliate IDs by each affiliate's level in the matrix
 *
 * @since 1.0
 * @return array
 */
function affwp_mlm_filter_by_level( $affiliate_ids = array(), $levels = 0 ) {

	if ( !empty( $affiliate_ids ) ) {
		
		if ( empty( $levels ) ) {
			
			$matrix_depth = affiliate_wp()->settings->get( 'affwp_mlm_matrix_depth' );
			$levels = $matrix_depth ? $matrix_depth : 15;
			
		}
		
		$level_count = 0;
		
		foreach( $affiliate_ids as $affiliate_id ) {
			
			$level_count++;
			
			if( $level_count > $levels ) {
				break;
			}
			
			$filtered_affiliates[] = $affiliate_id;
		
		}
		
		return $filtered_affiliates;
	
	} else{
		return;
	}
}

/**
 * Filter an array of Affiliate IDs by each affiliate's status
 *
 * @since 1.0.4
 */
function affwp_mlm_filter_by_status( $affiliate_ids = array(), $status = '' ) {
	
	// Stop if the affiliate has no upline
	if ( empty( $affiliate_ids ) ) {
		return $affiliate_ids;
	}

	if ( empty( $status ) ) {
		$status = 'active';
	}
	
	$filtered_affiliates = array();
	
	foreach( $affiliate_ids as $affiliate_id ) {
		
		// Skip affiliates that don't have the given status
		if ( $status != affwp_get_affiliate_status( $affiliate_id ) ) {
			continue;
		}
		
		$filtered_affiliates[] = $affiliate_id;
	
	}
		
		return $filtered_affiliates;
}