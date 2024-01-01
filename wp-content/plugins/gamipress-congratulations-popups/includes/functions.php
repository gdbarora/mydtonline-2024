<?php
/**
 * Functions
 *
 * @package     GamiPress\Congratulations_Popups\Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Check a specific user popups
 *
 * @since 1.1.3
 *
 * @param int       $user_id        The given user's ID
 * @param bool      $user_points    If true, will return the current user points balances (used on ajax function)
 *
 * @return array                    Format: array( 'notices' => array(), 'last_check' => 0 )
 */
function gamipress_congratulations_popups_get_user_popups( $user_id = null, $user_points = false ) {

    global $gamipress_congratulations_popups_template_args;

    // If user ID not passed set the current logged in user
    if( $user_id === null ) {
        $user_id = get_current_user_id();
    }

    $response = array(
        'popups' => array(),
    );

    // Just continue if user ID is set
    if( $user_id === 0 ) {
        return $response;
    }

    $prefix = '_gamipress_congratulations_popups_';

    $popup_displays = gamipress_congratulations_popups_get_user_popup_displays_unread( $user_id );

    foreach( $popup_displays as $popup_display ) {

        $display_id = $popup_display->congratulations_popup_display_id;
        $popup_id = $popup_display->congratulations_popup_id;

        // Make loop faster by prevent to check empty ids
        if( absint( $popup_id ) === 0 ) continue;

        // Setup table
        // Note: Need to be setup on each loop since parse tags sometimes doesn't reset correctly the table
        $ct_table = ct_setup_table( 'gamipress_congratulations_popups' );

        $congratulations_popup = ct_get_object( $popup_id );

        // Bail if popup not exists
        if( ! $congratulations_popup ) continue;

        // Bail if popup is not active
        if( $congratulations_popup->status !== 'active' ) continue;

        $title = $popup_display->subject;
        $content = $popup_display->content;

        // Text formatting and shortcode execution
        $content = wpautop( $content );
        $content = do_shortcode( $content );

        // Style options
        $display_effect     = ct_get_object_meta( $popup_id, $prefix . 'display_effect', true );
        $particles_color    = ct_get_object_meta( $popup_id, $prefix . 'particles_color', true );
        $background_color   = ct_get_object_meta( $popup_id, $prefix . 'background_color', true );
        $title_color        = ct_get_object_meta( $popup_id, $prefix . 'title_color', true );
        $text_color         = ct_get_object_meta( $popup_id, $prefix . 'text_color', true );
        $show_sound         = ct_get_object_meta( $popup_id, $prefix . 'show_sound', true );
        $hide_sound         = ct_get_object_meta( $popup_id, $prefix . 'hide_sound', true );

        // Check colors
        if( $particles_color === '#' ) $particles_color = '';
        if( $background_color === '#' ) $background_color = '';
        if( $title_color === '#' ) $title_color = '';
        if( $text_color === '#' ) $text_color = '';

        // Initialize template args
        $gamipress_congratulations_popups_template_args = array(
            'congratulations_popup_display_id'  => $display_id,
            'congratulations_popup_id'          => $popup_id,
            'congratulations_popup'             => $congratulations_popup,
            'subject'                           => $title,
            'content'                           => $content,
            'display_effect'                    => $display_effect,
            'particles_color'                   => $particles_color,
            'background_color'                  => $background_color,
            'title_color'                       => $title_color,
            'text_color'                        => $text_color,
            'show_sound'                        => $show_sound,
            'hide_sound'                        => $hide_sound,
        );

        // Setup the popup content
        ob_start();
        gamipress_get_template_part( 'congratulations-popup' );
        $content = ob_get_clean();

        if( ! empty( $content ) ) {
            $response['popups'][] = $content;
        }

        ct_reset_setup_table();

    }

    // Pass the updated information of the current user points if is requested
    if( $user_points ) {

        // Setup an array with all the user points
        $response['user_points'] = array();

        foreach( gamipress_get_points_types_slugs() as $points_type ) {
            $response['user_points'][] = array(
                'points_type' => $points_type,
                'points' => gamipress_get_user_points( $user_id, $points_type )
            );
        }

    }

    /**
     * Filter user popups
     *
     * @since 1.2.0
     *
     * @param array     $response       Array with information about user popups
     * @param int       $user_id        The given user's ID
     * @param bool      $user_points    If true, will return the current user points balances (used on ajax function)
     *
     * @return array                    Format: array( 'popups' => array(), 'user_points' => array() )
     */
    return apply_filters( 'gamipress_congratulations_popups_get_user_popups', $response, $user_id, $user_points );

}

/**
 * Add a new popup to display
 *
 * @since 1.0.0
 *
 * @param int $user_id
 * @param int $congratulations_popup_id
 *
 * @return bool
 */
function gamipress_congratulations_popups_add_popup_to_display( $user_id, $congratulations_popup_id ) {

    $prefix = '_gamipress_congratulations_popups_';

    // Setup table
    ct_setup_table( 'gamipress_congratulations_popups' );

    // Shorthand
    $id = $congratulations_popup_id;

    // Check max displays
    $max_displays = absint( ct_get_object_meta( $id, $prefix . 'max_displays', true ) );

    // Check if max displays has been exceeded (only if max displays is higher than 0
    if( $max_displays > 0 && $max_displays <= gamipress_congratulations_popups_get_popup_displays_count( $id ) ) {
        return false;
    }

    // Check max displays per user
    $max_displays_per_user = absint( ct_get_object_meta( $id, $prefix . 'max_displays_per_user', true ) );

    if( $max_displays_per_user > 0 && $max_displays_per_user <= gamipress_congratulations_popups_get_user_popup_displays_count( $user_id, $id ) ) {
        return false;
    }

    $congratulations_popup = ct_get_object( $id );

    // Bail if popup not exists
    if( ! $congratulations_popup ) return false;

    // Bail if popup is not active
    if( $congratulations_popup->status !== 'active' ) return false;

    /**
     * Filter the conditional popup title
     *
     * @since 1.0.0
     *
     * @param string    $title
     * @param int       $user_id
     * @param int       $congratulations_popup_id
     * @param stdClass  $congratulations_popup
     */
    $title = apply_filters( 'gamipress_congratulations_popups_congratulations_popup_title', $congratulations_popup->subject, $user_id, $id, $congratulations_popup );

    $title = gamipress_congratulations_popups_parse_pattern_tags( $title, $user_id );

    /**
     * Filter the conditional popup content
     *
     * @since 1.0.0
     *
     * @param string    $title
     * @param int       $user_id
     * @param int       $congratulations_popup_id
     * @param stdClass  $congratulations_popup
     */
    $content = apply_filters( 'gamipress_congratulations_popups_congratulations_popup_content', $congratulations_popup->content, $user_id, $id, $congratulations_popup );

    $content = gamipress_congratulations_popups_parse_pattern_tags( $content, $user_id );

    ct_reset_setup_table();

    gamipress_congratulations_popups_add_popup_display_to_user( $user_id, $id, $title, $content );

    return true;

}
