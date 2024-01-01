<?php
/**
 * Scripts
 *
 * @package     GamiPress\BuddyBoss_Notifications\Scripts
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_buddyboss_notifications_admin_register_scripts( $hook ) {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Stylesheets
    wp_register_style( 'gamipress-buddyboss-notifications-admin-css', GAMIPRESS_BUDDYBOSS_NOTIFICATIONS_URL . 'assets/css/gamipress-buddyboss-notifications-admin' . $suffix . '.css', array(), GAMIPRESS_BUDDYBOSS_NOTIFICATIONS_VER, 'all' );

    // Scripts
    wp_register_script( 'gamipress-buddyboss-notifications-admin-js', GAMIPRESS_BUDDYBOSS_NOTIFICATIONS_URL . 'assets/js/gamipress-buddyboss-notifications-admin' . $suffix . '.js', array( 'jquery', 'gamipress-admin-functions-js', 'gamipress-select2-js' ), GAMIPRESS_BUDDYBOSS_NOTIFICATIONS_VER, true );

}
add_action( 'admin_init', 'gamipress_buddyboss_notifications_admin_register_scripts' );

/**
 * Enqueue admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_buddyboss_notifications_admin_enqueue_scripts( $hook ) {

    // Stylesheets
    wp_enqueue_style( 'gamipress-buddyboss-notifications-admin-css' );

    // Scripts
    wp_enqueue_script( 'gamipress-buddyboss-notifications-admin-js' );

}
add_action( 'admin_enqueue_scripts', 'gamipress_buddyboss_notifications_admin_enqueue_scripts', 100 );