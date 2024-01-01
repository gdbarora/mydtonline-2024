<?php
/**
 * Ajax Functions
 *
 * @package     GamiPress\Congratulations_Popups\Ajax_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Ajax function to check live user popups
 *
 * @since   1.0.0
 */
function gamipress_congratulations_popups_ajax_get_popups() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_congratulations_popups', 'nonce' );

    $user_id = get_current_user_id();

    // Bail if user is not logged in
    if( $user_id === 0 ) {
        return;
    }

    ignore_user_abort( false );

    define( 'GAMIPRESS_CONGRATULATIONS_POPUPS_AJAX', true );

    // Setup vars
    $user_points = ( isset( $_REQUEST['user_points'] ) && (bool) $_REQUEST['user_points'] );

    // Get user notices
    $response = gamipress_congratulations_popups_get_user_popups( $user_id, $user_points );

    // Return user notices in format: array( 'popups' => array(), 'user_points' => 0 )
    wp_send_json_success( $response );

}
add_action( 'wp_ajax_gamipress_congratulations_popups_get_popups', 'gamipress_congratulations_popups_ajax_get_popups' );
add_action( 'wp_ajax_nopriv_gamipress_congratulations_popups_get_popups', 'gamipress_congratulations_popups_ajax_get_popups' );

function gamipress_congratulations_popups_ajax_popup_shown() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_congratulations_popups', 'nonce' );

    $user_id = get_current_user_id();

    // Bail if user is not logged in
    if( $user_id === 0 ) {
        return;
    }

    ignore_user_abort( false );

    define( 'GAMIPRESS_CONGRATULATIONS_POPUPS_AJAX', true );

    // Setup vars
    $congratulations_popup_display_id = ( isset( $_REQUEST['congratulations_popup_display_id'] ) ? absint( $_REQUEST['congratulations_popup_display_id'] ) : 0 );

    // Bail if not a valid ID provided
    if( $congratulations_popup_display_id === 0 ) {
        return;
    }

    gamipress_congratulations_popups_mark_popup_display_as_read( $congratulations_popup_display_id );

    // Return user notices in format: array( 'popups' => array(), 'user_points' => 0 )
    wp_send_json_success( array( __( 'Done', 'gamipress-congratulations-popups' ) ) );

}
add_action( 'wp_ajax_gamipress_congratulations_popups_popup_shown', 'gamipress_congratulations_popups_ajax_popup_shown' );
add_action( 'wp_ajax_nopriv_gamipress_congratulations_popups_popup_shown', 'gamipress_congratulations_popups_ajax_popup_shown' );