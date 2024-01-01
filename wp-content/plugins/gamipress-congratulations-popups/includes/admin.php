<?php
/**
 * Admin
 *
 * @package     GamiPress\Congratulations_Popups\Admin
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add plugin admin bar menu
 *
 * @since 1.0.0
 *
 * @param WP_Admin_Bar $wp_admin_bar
 */
function gamipress_congratulations_popups_admin_bar_menu( $wp_admin_bar ) {

    // - Congratulations Popups
    $wp_admin_bar->add_node( array(
        'id'     => 'gamipress-congratulations-popups',
        'title'  => __( 'Congratulations Popups', 'gamipress-congratulations-popups' ),
        'parent' => 'gamipress',
        'href'   => admin_url( 'admin.php?page=gamipress_congratulations_popups' )
    ) );

}
add_action( 'admin_bar_menu', 'gamipress_congratulations_popups_admin_bar_menu', 150 );


/**
 * Plugin Licensing meta box
 *
 * @since  1.0.0
 *
 * @param $meta_boxes
 *
 * @return mixed
 */
function gamipress_congratulations_popups_licenses_meta_boxes( $meta_boxes ) {

    $meta_boxes['gamipress-congratulations-popups-license'] = array(
        'title' => __( 'GamiPress Congratulations Popups', 'gamipress-congratulations-popups' ),
        'fields' => array(
            'gamipress_congratulations_popups_license' => array(
                'name' => __( 'License', 'gamipress-congratulations-popups' ),
                'type' => 'edd_license',
                'file' => GAMIPRESS_CONGRATULATIONS_POPUPS_FILE,
                'item_name' => 'Congratulations Popups',
            ),
        )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_settings_licenses_meta_boxes', 'gamipress_congratulations_popups_licenses_meta_boxes' );

/**
 * Plugin automatic updates
 *
 * @since  1.0.0
 *
 * @param array $automatic_updates_plugins
 *
 * @return array
 */
function gamipress_congratulations_popups_automatic_updates( $automatic_updates_plugins ) {

    $automatic_updates_plugins['gamipress-congratulations-popups'] = __( 'Congratulations Popups', 'gamipress-congratulations-popups' );

    return $automatic_updates_plugins;
}
add_filter( 'gamipress_automatic_updates_plugins', 'gamipress_congratulations_popups_automatic_updates' );