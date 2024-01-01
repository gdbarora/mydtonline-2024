<?php
/**
 * Custom Tables
 *
 * @package     GamiPress\Congratulations_Popups\Custom_Tables
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

require_once GAMIPRESS_CONGRATULATIONS_POPUPS_DIR . 'includes/custom-tables/congratulations-popups.php';

/**
 * Register all plugin Custom DB Tables
 *
 * @since  1.0.0
 *
 * @return void
 */
function gamipress_congratulations_popups_register_custom_tables() {

    // Congratulations Popups Table
    ct_register_table( 'gamipress_congratulations_popups', array(
        'singular' => __( 'Congratulations Popup', 'gamipress-congratulations-popups' ),
        'plural' => __( 'Congratulations Popups', 'gamipress-congratulations-popups' ),
        'show_ui' => true,
        'version' => 1,
        'global' => gamipress_is_network_wide_active(),
        'capability' => gamipress_get_manager_capability(),
        'supports' => array( 'meta' ),
        'views' => array(
            'list' => array(
                'menu_title' => __( 'Congratulations Popups', 'gamipress-congratulations-popups' ),
                'parent_slug' => 'gamipress'
            ),
            'add' => array(
                'show_in_menu' => false,
            ),
            'edit' => array(
                'show_in_menu' => false,
            ),
        ),
        'schema' => array(
            'congratulations_popup_id' => array(
                'type' => 'bigint',
                'length' => '20',
                'auto_increment' => true,
                'primary_key' => true,
            ),
            'title' => array(
                'type' => 'text',
            ),
            'subject' => array(
                'type' => 'text',
            ),
            'content' => array(
                'type' => 'longtext',
            ),
            'status' => array(
                'type' => 'text',
            ),
            'date' => array(
                'type' => 'datetime',
                'default' => '0000-00-00 00:00:00'
            ),
        ),
    ) );

    // Congratulations Popups Displays Table
    ct_register_table( 'gamipress_congratulations_popups_displays', array(
        'singular' => __( 'Congratulations Popup Display', 'gamipress-congratulations-popups' ),
        'plural' => __( 'Congratulations Popup Displays', 'gamipress-congratulations-popups' ),
        'show_ui' => false,
        'version' => 1,
        'global' => gamipress_is_network_wide_active(),
        'supports' => array( 'meta' ),
        'schema' => array(
            'congratulations_popup_display_id' => array(
                'type' => 'bigint',
                'length' => '20',
                'auto_increment' => true,
                'primary_key' => true,
            ),
            'congratulations_popup_id' => array(
                'type' => 'bigint',
                'length' => '20',
                'key' => true,
            ),
            'user_id' => array(
                'type' => 'bigint',
                'length' => '20',
                'key' => true,
            ),

            // Fields

            'subject' => array(
                'type' => 'text',
            ),
            'content' => array(
                'type' => 'text',
            ),
            'read' => array(
                'type' => 'tinyint',
                'default' => 0
            ),
        ),
    ) );

}
add_action( 'ct_init', 'gamipress_congratulations_popups_register_custom_tables' );