<?php
/**
 * Functions
 *
 * @package     GamiPress\BuddyBoss_Notifications\Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get the last earning ID
 *
 * @since  1.0.0
 *
 * @param  array $query User earning query parameters
 *
 * @return int          The last earning ID
 */
function gamipress_buddyboss_notifications_get_last_earning_id( $query = array() ) {

    global $wpdb;

    // Post data
    $query = wp_parse_args( $query, array(
        'user_id'	        => 0,
        'post_id'	        => 0,
    ) );

    $user_id = absint( $query['user_id'] );
    $post_id = absint( $query['post_id'] );

    if( $user_id === 0 || $post_id === 0 ) {
        return 0;
    }

    $user_earnings = GamiPress()->db->user_earnings;

    return absint( $wpdb->get_var( "SELECT ue.user_earning_id FROM {$user_earnings} AS ue WHERE ue.user_id = {$user_id} AND ue.post_id = {$post_id} ORDER BY ue.date DESC LIMIT 1" ) );

}

/**
 * Get the last earning object
 *
 * @since  1.0.0
 *
 * @param  int $user_earning_id User earning ID
 *
 * @return stdClass|array|null  The earning object
 */
function gamipress_buddyboss_notifications_get_earning( $user_earning_id ) {

    $user_earning_id = absint( $user_earning_id );

    ct_setup_table( 'gamipress_user_earnings' );

    $user_earning = ct_get_object( $user_earning_id );

    ct_reset_setup_table();

    return $user_earning;

}
