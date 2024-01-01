<?php
/**
 * Scripts
 *
 * @package     GamiPress\Congratulations_Popups\Scripts
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register frontend scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_congratulations_popups_register_scripts() {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Stylesheets
    wp_register_style( 'gamipress-congratulations-popups-css', GAMIPRESS_CONGRATULATIONS_POPUPS_URL . 'assets/css/gamipress-congratulations-popups' . $suffix . '.css', array( ), GAMIPRESS_CONGRATULATIONS_POPUPS_VER, 'all' );

    // Scripts
    wp_register_script( 'gamipress-congratulations-popups-party-js', GAMIPRESS_CONGRATULATIONS_POPUPS_URL . 'assets/libs/party-js/party' . $suffix . '.js', array( 'jquery' ), GAMIPRESS_CONGRATULATIONS_POPUPS_VER, true );
    wp_register_script( 'gamipress-congratulations-popups-effects-js', GAMIPRESS_CONGRATULATIONS_POPUPS_URL . 'assets/js/gamipress-congratulations-popups-effects' . $suffix . '.js', array( 'jquery', 'gamipress-congratulations-popups-party-js' ), GAMIPRESS_CONGRATULATIONS_POPUPS_VER, true );
    wp_register_script( 'gamipress-congratulations-popups-js', GAMIPRESS_CONGRATULATIONS_POPUPS_URL . 'assets/js/gamipress-congratulations-popups' . $suffix . '.js', array( 'jquery', 'gamipress-congratulations-popups-party-js', 'gamipress-congratulations-popups-effects-js' ), GAMIPRESS_CONGRATULATIONS_POPUPS_VER, true );

}
add_action( 'init', 'gamipress_congratulations_popups_register_scripts' );

/**
 * Enqueue frontend scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_congratulations_popups_enqueue_scripts( $hook = null ) {

    $current_user = get_current_user_id();

    // Popups for guests are not supported
    if( $current_user === 0 ) {
        return;
    }

    // Stylesheets
    wp_enqueue_style( 'gamipress-congratulations-popups-css' );

    $excluded_urls = array();

    /**
     * Filter to let plugins exclude URLs from the popups check
     *
     * @since 1.4.0
     *
     * @param array $excluded_urls
     *
     * @return array
     */
    $excluded_urls = apply_filters( 'gamipress_congratulations_popups_excluded_urls', $excluded_urls );

    $excluded_data = array();

    /**
     * Filter to let plugins exclude request data from the popups check
     *
     * @since 1.4.0
     *
     * @param array $excluded_data
     *
     * @return array
     */
    $excluded_data = apply_filters( 'gamipress_congratulations_popups_excluded_data', $excluded_data );

    $excluded_ajax_actions = array(
        'gamipress_congratulations_popups_get_popups',
        'gamipress_congratulations_popups_popup_shown',
        // Notifications support
        'gamipress_notifications_get_notices',
        'gamipress_notifications_last_check',
        // AutomatorWP compatibility
        'automatorwp_check_for_redirect',
    );

    /**
     * Filter to let plugins exclude ajax actions from the popups check
     *
     * @since 1.4.0
     *
     * @param array $excluded_ajax_actions
     *
     * @return array
     */
    $excluded_ajax_actions = apply_filters( 'gamipress_congratulations_popups_excluded_ajax_actions', $excluded_ajax_actions );

    // Localize scripts
    wp_localize_script( 'gamipress-congratulations-popups-js', 'gamipress_congratulations_popups', array(
        'ajaxurl'               => esc_url( admin_url( 'admin-ajax.php', 'relative' ) ),
        'nonce'                 => wp_create_nonce( 'gamipress_congratulations_popups' ),
        'last_check_delay'      => apply_filters( 'gamipress_congratulations_popups_last_check_delay', 5000 ),
        'ajax_check_delay'      => apply_filters( 'gamipress_congratulations_popups_ajax_check_delay', 200 ),
        'excluded_urls'         => $excluded_urls,
        'excluded_data'         => $excluded_data,
        'excluded_ajax_actions' => $excluded_ajax_actions,
    ) );

    // Scripts
    gamipress_congratulations_popups_enqueue_effects_scripts();
    wp_enqueue_script( 'gamipress-congratulations-popups-js' );

}
add_action( 'wp_enqueue_scripts', 'gamipress_congratulations_popups_enqueue_scripts', 100 );

/**
 * Register admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_congratulations_popups_admin_register_scripts( $hook ) {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Stylesheets
    wp_register_style( 'gamipress-congratulations-popups-admin-css', GAMIPRESS_CONGRATULATIONS_POPUPS_URL . 'assets/css/gamipress-congratulations-popups-admin' . $suffix . '.css', array(), GAMIPRESS_CONGRATULATIONS_POPUPS_VER, 'all' );

    // Scripts
    wp_register_script( 'gamipress-congratulations-popups-party-js', GAMIPRESS_CONGRATULATIONS_POPUPS_URL . 'assets/libs/party-js/party' . $suffix . '.js', array( 'jquery' ), GAMIPRESS_CONGRATULATIONS_POPUPS_VER, true );
    wp_register_script( 'gamipress-congratulations-popups-effects-js', GAMIPRESS_CONGRATULATIONS_POPUPS_URL . 'assets/js/gamipress-congratulations-popups-effects' . $suffix . '.js', array( 'jquery', 'gamipress-congratulations-popups-party-js' ), GAMIPRESS_CONGRATULATIONS_POPUPS_VER, true );
    wp_register_script( 'gamipress-congratulations-popups-admin-js', GAMIPRESS_CONGRATULATIONS_POPUPS_URL . 'assets/js/gamipress-congratulations-popups-admin' . $suffix . '.js', array( 'jquery', 'gamipress-admin-functions-js', 'gamipress-select2-js', 'gamipress-congratulations-popups-party-js', 'gamipress-congratulations-popups-effects-js' ), GAMIPRESS_CONGRATULATIONS_POPUPS_VER, true );

}
add_action( 'admin_init', 'gamipress_congratulations_popups_admin_register_scripts' );

/**
 * Enqueue admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_congratulations_popups_admin_enqueue_scripts( $hook ) {

    //Scripts

    // Congratulations Popups add/edit screen
    if( $hook === 'gamipress_page_gamipress_congratulations_popups' || $hook === 'admin_page_edit_gamipress_congratulations_popups' ) {

        // Enqueue admin functions
        gamipress_enqueue_admin_functions_script();

        // Stylesheets
        wp_enqueue_style( 'gamipress-congratulations-popups-admin-css' );

        // Scripts
        gamipress_congratulations_popups_enqueue_effects_scripts();
        wp_enqueue_script( 'gamipress-congratulations-popups-admin-js' );

    }

}
add_action( 'admin_enqueue_scripts', 'gamipress_congratulations_popups_admin_enqueue_scripts', 100 );

function gamipress_congratulations_popups_enqueue_effects_scripts() {

    $effects_vars = array(
        // Fireworks
        'fireworks' => array(
            'particles' => array(
                'min' => 70,
                'max' => 80,
            )
        ),
        // Confetti
        'confetti' => array(
            'particles' => array(
                'min' => 200,
                'max' => 250,
            )
        ),
        // Stars
        'stars' => array(
            'particles' => array(
                'min' => 100,
                'max' => 120,
            )
        ),
        // Bubbles
        'bubbles' => array(
            'particles' => array(
                'min' => 100,
                'max' => 120,
            )
        ),
    );

    /**
     * Filter available to override the effects particles
     *
     * @since 1.0.2
     *
     * @param array $effects_vars
     *
     * @return array
     */
    $effects_vars = apply_filters( 'gamipress_congratulations_popups_effects_vars', $effects_vars );

    // Localize scripts
    wp_localize_script( 'gamipress-congratulations-popups-effects-js', 'gamipress_congratulations_popups_effects', $effects_vars );

    wp_enqueue_script( 'gamipress-congratulations-popups-party-js' );
    wp_enqueue_script( 'gamipress-congratulations-popups-effects-js' );
}