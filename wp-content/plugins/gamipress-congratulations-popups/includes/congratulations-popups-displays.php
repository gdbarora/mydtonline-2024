<?php
/**
 * Congratulations Popups Displays Functions
 *
 * @package     GamiPress\Congratulations_Popups\Congratulations_Popups_Displays_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add popup display to user
 *
 * @since 1.0.0
 *
 * @param int $user_id
 * @param int $congratulations_popup_id
 */
function gamipress_congratulations_popups_add_popup_display_to_user( $user_id, $congratulations_popup_id, $subject, $content ) {

    // Setup table
    $ct_table = ct_setup_table( 'gamipress_congratulations_popups_displays' );

    $ct_table->db->insert( array(
        'user_id' => $user_id,
        'congratulations_popup_id' => $congratulations_popup_id,
        'subject' => $subject,
        'content' => $content,
        'read' => 0,
    ) );

    ct_reset_setup_table();

}

/**
 * Get user popup displays unread
 *
 * @since 1.0.0
 *
 * @param int $user_id
 *
 * @return array
 */
function gamipress_congratulations_popups_get_user_popup_displays_unread( $user_id ) {

    global $wpdb;

    // Setup table
    $ct_table = ct_setup_table( 'gamipress_congratulations_popups_displays' );

    // Get user popup displays unread
    $popup_displays = $wpdb->get_results( $wpdb->prepare(
        "SELECT *
        FROM {$ct_table->db->table_name} AS cpd
        WHERE cpd.user_id = %d
          AND cpd.read = %d",
        $user_id,
        0
    ) );

    ct_reset_setup_table();

    return $popup_displays;

}

/**
 * Get the number of times a user has seen a popup
 *
 * @since 1.0.0
 *
 * @param int $congratulations_popup_id
 *
 * @return int
 */
function gamipress_congratulations_popups_get_popup_displays_count( $congratulations_popup_id ) {

    global $wpdb;

    // Setup table
    $ct_table = ct_setup_table( 'gamipress_congratulations_popups_displays' );

    // Count the user popup displays
    $displays = absint( $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*)
        FROM {$ct_table->db->table_name} AS cpd
        WHERE cpd.congratulations_popup_id = %d",
        $congratulations_popup_id
    ) ) );

    ct_reset_setup_table();

    return $displays;

}

/**
 * Get the number of times a user has seen a popup
 *
 * @since 1.0.0
 *
 * @param int $user_id
 * @param int $congratulations_popup_id
 *
 * @return int
 */
function gamipress_congratulations_popups_get_user_popup_displays_count( $user_id, $congratulations_popup_id ) {

    global $wpdb;

    // Setup table
    $ct_table = ct_setup_table( 'gamipress_congratulations_popups_displays' );

    // Count the user popup displays
    $displays = absint( $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*)
        FROM {$ct_table->db->table_name} AS cpd
        WHERE cpd.user_id = %d
          AND cpd.congratulations_popup_id = %d",
        $user_id,
        $congratulations_popup_id
    ) ) );

    ct_reset_setup_table();

    return $displays;

}

/**
 * Mark a popup display as read
 *
 * @since 1.0.0
 *
 * @param int $congratulations_popup_display_id
 */
function gamipress_congratulations_popups_mark_popup_display_as_read( $congratulations_popup_display_id ) {

    // Setup table
    $ct_table = ct_setup_table( 'gamipress_congratulations_popups_displays' );

    $ct_table->db->update(
        array(
            'read' => 1
        ),
        array(
            'congratulations_popup_display_id' => $congratulations_popup_display_id
        )
    );

    ct_reset_setup_table();

}