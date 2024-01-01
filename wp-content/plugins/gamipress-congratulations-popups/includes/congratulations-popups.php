<?php
/**
 * Congratulations Popups Functions
 *
 * @package     GamiPress\Congratulations_Popups\Congratulations_Popups_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get the registered congratulations popups statuses
 *
 * @since  1.0.0
 *
 * @return array Array of congratulations popups statuses
 */
function gamipress_congratulations_popups_get_congratulations_popup_statuses() {

    return apply_filters( 'gamipress_congratulations_popups_get_congratulations_popup_statuses', array(
        'active'    => __( 'Active', 'gamipress-congratulations-popups' ),
        'inactive'  => __( 'Inactive', 'gamipress-congratulations-popups' ),
    ) );

}

/**
 * Get the registered congratulations popups display effects
 *
 * @since  1.0.0
 *
 * @return array Array of congratulations popups display effects
 */
function gamipress_congratulations_popups_get_congratulations_popup_display_effects() {

    return apply_filters( 'gamipress_congratulations_popups_get_congratulations_popup_display_effects', array(
        'none'      => __( 'None', 'gamipress-congratulations-popups' ),
        'fireworks' => __( 'Confetti fireworks', 'gamipress-congratulations-popups' ),
        'confetti'  => __( 'Confetti', 'gamipress-congratulations-popups' ),
        'stars'     => __( 'Stars', 'gamipress-congratulations-popups' ),
        'bubbles'   => __( 'Bubbles', 'gamipress-congratulations-popups' ),
    ) );

}

/**
 * Get the registered congratulations popups conditions
 *
 * @since  1.0.0
 *
 * @return array Array of congratulations popups conditions
 */
function gamipress_congratulations_popups_get_congratulations_popup_conditions() {

    return apply_filters( 'gamipress_congratulations_popups_get_congratulations_popup_conditions', array(
        'points-balance'        => __( 'Reach a points balance', 'gamipress-congratulations-popups' ),
        'specific-achievement'  => __( 'Unlock a specific achievement', 'gamipress-congratulations-popups' ),
        'any-achievement'       => __( 'Unlock any achievement of type', 'gamipress-congratulations-popups' ),
        'all-achievements'     	=> __( 'Unlock all achievements of type', 'gamipress-congratulations-popups' ),
        'specific-rank'         => __( 'Reach a specific rank', 'gamipress-congratulations-popups' ),
    ) );

}

/**
 * Get all active congratulations popups
 *
 * @since  1.0.0
 *
 * @return array
 */
function gamipress_congratulations_popups_all_active_congratulations_popups() {

    global $wpdb;

    // Setup table
    $ct_table = ct_setup_table( 'gamipress_congratulations_popups' );

    // Search all congratulations popups actives and published before current date
    $congratulations_popups = $wpdb->get_results( $wpdb->prepare(
        "SELECT *
        FROM {$ct_table->db->table_name} AS cn
        WHERE cn.status = %s
          AND cn.date <= %s",
        'active',
        date( 'Y-m-d 00:00:00', current_time('timestamp') )
    ) );

    ct_reset_setup_table();

    /**
     * Filter all active congratulations popups
     *
     * @since  1.0.0
     *
     * @param array     $congratulations_popups
     *
     * @return array
     */
    return apply_filters( 'gamipress_congratulations_popups_all_active_congratulations_popups', $congratulations_popups );

}

/**
 * Get all active congratulations popups based on given condition
 *
 * @since  1.0.0
 *
 * @param string $condition
 *
 * @return array
 */
function gamipress_congratulations_popups_get_active_congratulations_popups( $condition ) {

    global $wpdb;

    $prefix = '_gamipress_congratulations_popups_';

    // Setup table
    $ct_table = ct_setup_table( 'gamipress_congratulations_popups' );

    // Search all congratulations popups actives, published before current date and based on a given condition
    $congratulations_popups = $wpdb->get_results( $wpdb->prepare(
        "SELECT *
        FROM {$ct_table->db->table_name} AS cn
        LEFT JOIN {$ct_table->meta->db->table_name} AS cnm ON ( cn.congratulations_popup_id = cnm.congratulations_popup_id AND cnm.meta_key = %s )
        WHERE cnm.meta_value = %s
          AND cn.status = %s
          AND cn.date <= %s",
        $prefix . 'condition',
        $condition,
        'active',
        date( 'Y-m-d 00:00:00', current_time('timestamp') )
    ) );

    ct_reset_setup_table();

    /**
     * Filter active congratulations popups based on given condition
     *
     * @since  1.0.0
     *
     * @param array     $congratulations_popups
     * @param string    $condition
     *
     * @return array
     */
    return apply_filters( 'gamipress_congratulations_popups_get_active_congratulations_popups', $congratulations_popups, $condition );

}