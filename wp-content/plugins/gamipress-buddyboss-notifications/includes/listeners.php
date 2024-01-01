<?php
/**
 * Listeners
 *
 * @package     GamiPress\BuddyBoss_Notifications\Listeners
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Listener for achievement awards
 *
 * @since 1.0.0
 *
 * @param int       $user_id
 * @param int       $achievement_id
 * @param string    $trigger
 * @param int       $site_id
 * @param array     $args
 */
function gamipress_buddyboss_notifications_achievement_listener( $user_id, $achievement_id, $trigger, $site_id, $args ) {

    $disabled = false;

    /**
     * Filter to decide if notifications are disabled for this item
     *
     * @since 1.0.0
     *
     * @param bool  $disabled
     * @param int   $user_id
     * @param int   $post_id
     *
     * @return bool
     */
    $disabled = apply_filters( 'gamipress_buddyboss_notifications_disabled', $disabled, $user_id, $achievement_id );

    if( $disabled ) {
        return;
    }



    // Add the notification to the user
    bp_notifications_add_notification(
        array(
            'user_id'           => $user_id,
            'item_id'           => $achievement_id,
            'secondary_item_id' => gamipress_buddyboss_notifications_get_last_earning_id( array( 'post_id' => $achievement_id, 'user_id' => $user_id ) ),
            'component_name'    => 'gamipress_buddyboss_notifications',
            'component_action'  => 'gamipress_buddyboss_notifications',
            'date_notified'     => bp_core_current_time(),
            'is_new'            => 1,
            'allow_duplicate'   => true,
        )
    );

}
add_action( 'gamipress_award_achievement', 'gamipress_buddyboss_notifications_achievement_listener', 10, 5 );

/**
 * Listener for user rank updates
 *
 * @since 1.0.0
 *
 * @param int       $user_id
 * @param WP_Post   $new_rank
 * @param WP_Post   $old_rank
 * @param int       $admin_id
 * @param int       $achievement_id
 */
function gamipress_buddyboss_notifications_rank_listener( $user_id, $new_rank, $old_rank, $admin_id, $achievement_id ) {

    $disabled = false;

    /**
     * Filter to decide if notifications are disabled for this item
     *
     * @since 1.0.0
     *
     * @param bool  $disabled
     * @param int   $user_id
     * @param int   $post_id
     *
     * @return bool
     */
    $disabled = apply_filters( 'gamipress_buddyboss_notifications_disabled', $disabled, $user_id, $new_rank->ID );

    if( $disabled ) {
        return;
    }

    // Add the notification to the user
    bp_notifications_add_notification(
        array(
            'user_id'           => $user_id,
            'item_id'           => $new_rank->ID,
            'secondary_item_id' => gamipress_buddyboss_notifications_get_last_earning_id( array( 'post_id' => $new_rank->ID, 'user_id' => $user_id ) ),
            'component_name'    => 'gamipress_buddyboss_notifications',
            'component_action'  => 'gamipress_buddyboss_notifications',
            'date_notified'     => bp_core_current_time(),
            'is_new'            => 1,
            'allow_duplicate'   => true,
        )
    );

}
add_action( 'gamipress_update_user_rank', 'gamipress_buddyboss_notifications_rank_listener', 10, 5 );