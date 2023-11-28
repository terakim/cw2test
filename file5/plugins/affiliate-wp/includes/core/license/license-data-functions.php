<?php
/**
 * License Functions
 *
 * @package     AffiliateWP
 * @subpackage  Core/Functions
 * @copyright   Copyright (c) 2023, Sandhills Development, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.15.0
 */
use AffWP\Core\License\License_Data;

/**
 * Checks if a license upgrade is required.
 * 
 * @since 2.15.0
 * 
 * @param string $license_level Minimum license level required. Default is 'plus'.
 * @return bool True if upgrade is required, false otherwise. Default is true.
 */
function affwp_is_upgrade_required( $license_level = 'plus' ) {
    // Get license data.
    $license_data = new License_Data();

    // Get license ID.
    $license_id = $license_data->get_license_id();

    if ( is_null( $license_id ) ) {
        return true;
    }

    // Get license type.
    $license_type = strtolower( $license_data->get_license_type( $license_id ) );

    // Check if license upgrade is required.
    if ( 'plus' === $license_level ) {
        return ! ( 'plus' === $license_type || 'professional' === $license_type || 'ultimate' === $license_type );
    } elseif ( 'professional' === $license_level || 'pro' === $license_level ) {
        return ! ( 'professional' === $license_type || 'ultimate' === $license_type );
    } elseif ( 'ultimate' === $license_level ) {
        return ! ( 'ultimate' === $license_type );
    }

    return true;
}
