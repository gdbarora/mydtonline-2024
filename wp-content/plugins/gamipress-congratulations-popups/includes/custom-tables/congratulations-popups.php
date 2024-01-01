<?php
/**
 * Congratulations Popups
 *
 * @package     GamiPress\Congratulations_Popups\Custom_Tables\Congratulations_Popups
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Define the search fields for logs
 *
 * @since 1.0.0
 *
 * @param array $search_fields
 *
 * @return array
 */
function gamipress_congratulations_popups_search_fields( $search_fields ) {

    $search_fields[] = 'title';

    return $search_fields;

}
add_filter( 'ct_query_gamipress_congratulations_popups_search_fields', 'gamipress_congratulations_popups_search_fields' );

/**
 * Columns for logs list view
 *
 * @since 1.2.8
 *
 * @param array $columns
 *
 * @return array
 */
function gamipress_manage_congratulations_popups_columns( $columns = array() ) {

    $columns['title']                   = __( 'Title', 'gamipress-congratulations-popups' );
    $columns['display_effect']          = __( 'Display Effect', 'gamipress-conditional-emails' );
    $columns['max_displays']            = __( 'Max. Displays', 'gamipress-conditional-emails' );
    $columns['max_displays_per_user']   = __( 'Max. Displays Per User', 'gamipress-conditional-emails' );
    $columns['status']                  = __( 'Status', 'gamipress-congratulations-popups' );
    $columns['date']                    = __( 'Date', 'gamipress-congratulations-popups' );

    return $columns;
}
add_filter( 'manage_gamipress_congratulations_popups_columns', 'gamipress_manage_congratulations_popups_columns' );

/**
 * Sortable columns for logs list view
 *
 * @since 1.6.7
 *
 * @param array $sortable_columns
 *
 * @return array
 */
function gamipress_manage_congratulations_popups_sortable_columns( $sortable_columns ) {

    $sortable_columns['title']      = array( 'title', false );
    $sortable_columns['status']     = array( 'status', false );
    $sortable_columns['date']       = array( 'date', true );

    return $sortable_columns;

}
add_filter( 'manage_gamipress_congratulations_popups_sortable_columns', 'gamipress_manage_congratulations_popups_sortable_columns' );

/**
 * Columns rendering for list view
 *
 * @since  1.0.0
 *
 * @param string $column_name
 * @param integer $object_id
 */
function gamipress_congratulations_popups_manage_congratulations_popups_custom_column( $column_name, $object_id ) {

    // Setup vars
    $prefix = '_gamipress_congratulations_popups_';
    $congratulations_popup = ct_get_object( $object_id );

    switch( $column_name ) {
        case 'title':
            ?>

            <strong>
                <a href="<?php echo ct_get_edit_link( 'gamipress_congratulations_popups', $congratulations_popup->congratulations_popup_id ); ?>"><?php echo $congratulations_popup->title . ' (ID:' . $congratulations_popup->congratulations_popup_id . ')'; ?></a>
            </strong>

            <?php
            break;
        case 'display_effect':
            $display_effects = gamipress_congratulations_popups_get_congratulations_popup_display_effects();
            $display_effect = ct_get_object_meta( $object_id, $prefix . 'display_effect', true ); ?>

            <span class="gamipress-congratulations-popups-display-effect gamipress-congratulations-popups-display-effect-<?php echo $display_effect; ?>"><?php echo ( isset( $display_effects[$display_effect] ) ? $display_effects[$display_effect] : $display_effect ); ?></span>

            <?php
            break;
        case 'max_displays':
            $displays = gamipress_congratulations_popups_get_popup_displays_count( $object_id );
            $max_displays = absint( ct_get_object_meta( $object_id, $prefix . 'max_displays', true ) );

            if( $max_displays === 0 )
                echo sprintf( __( 'Unlimited (%d displays)', 'gamipress-congratulations-popups' ), $displays );
            else
                echo $displays . '/' . $max_displays;

            break;
        case 'max_displays_per_user':
            $max_displays_per_user = absint( ct_get_object_meta( $object_id, $prefix . 'max_displays_per_user', true ) );

            if( $max_displays_per_user === 0 )
                echo __( 'Unlimited', 'gamipress-congratulations-popups' );
            else
                echo $max_displays_per_user;

            break;
        case 'status':
            $statuses = gamipress_congratulations_popups_get_congratulations_popup_statuses(); ?>

            <span class="gamipress-congratulations-popups-status gamipress-congratulations-popups-status-<?php echo $congratulations_popup->status; ?>"><?php echo ( isset( $statuses[$congratulations_popup->status] ) ? $statuses[$congratulations_popup->status] : $congratulations_popup->status ); ?></span>

            <?php
            break;
        case 'date':
            ?>

            <abbr title="<?php echo date( 'Y/m/d g:i:s a', strtotime( $congratulations_popup->date ) ); ?>"><?php echo date( 'Y/m/d', strtotime( $congratulations_popup->date ) ); ?></abbr>

            <?php
            break;
    }
}
add_action( 'manage_gamipress_congratulations_popups_custom_column', 'gamipress_congratulations_popups_manage_congratulations_popups_custom_column', 10, 2 );

/**
 * Default data when creating a new item (similar to WP auto draft) see ct_insert_object()
 *
 * @since  1.0.0
 *
 * @param array $default_data
 *
 * @return array
 */
function gamipress_congratulations_popups_default_data( $default_data = array() ) {

    $default_data['title'] = '';
    $default_data['status'] = 'inactive';
    $default_data['date'] = date( 'Y-m-d 00:00:00' );

    return $default_data;
}
add_filter( 'ct_gamipress_congratulations_popups_default_data', 'gamipress_congratulations_popups_default_data' );

/**
 * Register custom CMB2 meta boxes
 *
 * @since  1.0.0
 */
function gamipress_congratulations_popups_congratulations_popups_meta_boxes( ) {

    // Start with an underscore to hide fields from custom fields list
    $prefix = '_gamipress_congratulations_popups_';

    // Title
    gamipress_add_meta_box(
        'gamipress-congratulations-popup-title',
        __( 'Title', 'gamipress-congratulations-popups' ),
        'gamipress_congratulations_popups',
        array(
            'title' => array(
                'name' 	=> __( 'Title', 'gamipress-congratulations-popups' ),
                'type' 	=> 'text',
                'attributes' => array(
                    'placeholder' => __( 'Enter title here', 'gamipress-congratulations-popups' ),
                )
            ),
        ),
        array(
            'priority' => 'core',
        )
    );

    // Audio settings
    $audio_query_args = array(
        'type' => array(
            'audio/midi',
            'audio/mpeg',
            'audio/x-aiff',
            'audio/x-pn-realaudio',
            'audio/x-pn-realaudio-plugin',
            'audio/x-realaudio',
            'audio/x-wav',
        ),
    );

    // Popup Configuration
    gamipress_add_meta_box(
        'gamipress-congratulations-popup-popup',
        __( 'Popup Configuration', 'gamipress-congratulations-popups' ),
        'gamipress_congratulations_popups',
        array(
            'subject' => array(
                'name' 	=> __( 'Title', 'gamipress-congratulations-popups' ),
                'type' 	=> 'text',
                'desc' 	=> __( 'Popup title (leave blank to hide it). For a list available tags, check next field description.', 'gamipress-congratulations-popups' ),
            ),
            'content' => array(
                'name' 	=> __( 'Content', 'gamipress-congratulations-popups' ),
                'desc' 	=> __( 'Popup content. Available tags:', 'gamipress-congratulations-popups' )
                    . ' ' . gamipress_congratulations_popups_get_pattern_tags_html(),
                'type' 	=> 'wysiwyg',
            ),
        ),
        array(
            'priority' => 'core',
        )
    );

    // Style Configuration
    gamipress_add_meta_box(
        'gamipress-congratulations-popup-style',
        __( 'Popup Style &amp; Sound Effects', 'gamipress-congratulations-popups' ),
        'gamipress_congratulations_popups',
        array(

            // Display effect
            $prefix . 'display_effect' => array(
                'name' => __( 'Display effect', 'gamipress-congratulations-popups' ),
                'desc' => __( 'Set the display effect of the popup.', 'gamipress-congratulations-popups' ),
                'type' => 'select',
                'options' => gamipress_congratulations_popups_get_congratulations_popup_display_effects(),
            ),
            $prefix . 'particles_color' => array(
                'name' => __( 'Particles Color', 'gamipress-congratulations-popups' ),
                'desc' => __( 'Set the color of the effect particles. If you leave this setting empty, the default colors will be:', 'gamipress-congratulations-popups' )
                . ' <br>' . __( 'Confetti fireworks and Confetti:', 'gamipress-congratulations-popups' ) . ' ' . __( 'Random colors.', 'gamipress-congratulations-popups' )
                . ' <br>' . __( 'Stars:', 'gamipress-congratulations-popups' ) . ' <span style="background: #FFE566"></span> #FFE566'
                . ' <br>' . __( 'Bubbles:', 'gamipress-congratulations-popups' ) . ' <span style="background: #667AFF"></span> #667AFF',
                'type' => 'colorpicker',
                'options' => array( 'alpha' => true ),
            ),
            $prefix . 'preview' => array(
                'type' => 'html',
                'content' => '<div class="gamipress-congratulations-popup-wrapper" data-display-effect="none" data-particles-color="">' .
                        '<div class="gamipress-congratulations-popup">' .
                        '<div class="gamipress-congratulations-popup-close">x</div>' .
                            '<div class="gamipress-congratulations-popup-content">' .
                                '<p>' . __( 'Popup sample!', 'gamipress-congratulations-popups' ) . '</p>' .
                                '<p>' . __( 'Click the preview button to see the effect in action.', 'gamipress-congratulations-popups' ) . '</p>' .
                        '</div>' .
                        '</div>' .
                    '</div>' .
                    '<div class="gamipress-congratulations-popup-preview-button button"><i class="dashicons dashicons-controls-play"></i> ' . __( 'Preview', 'gamipress-congratulations-popups' ) . '</div>',
            ),

            // Sound settings

            $prefix . 'show_sound' => array(
                'name'    => __( 'Show popup sound effect', 'gamipress-congratulations-popups' ),
                'desc'    => __( 'Upload, choose or paste the URL of the popup sound to play when this popup gets displayed.', 'gamipress-congratulations-popups' ),
                'type'    => 'file',
                'text'    => array(
                    'add_upload_file_text' => __( 'Add or Upload Audio', 'gamipress-congratulations-popups' ),
                ),
                'query_args' => $audio_query_args,
            ),
            $prefix . 'hide_sound' => array(
                'name'    => __( 'Hide popup sound effect', 'gamipress-congratulations-popups' ),
                'desc'    => __( 'Upload, choose or paste the URL of the popup sound to play when this popup gets hidden.', 'gamipress-congratulations-popups' ),
                'type'    => 'file',
                'text'    => array(
                    'add_upload_file_text' => __( 'Add or Upload Audio', 'gamipress-congratulations-popups' ),
                ),
                'query_args' => $audio_query_args,
            ),

            // Color settings

            $prefix . 'background_color' => array(
                'name' => __( 'Background Color', 'gamipress-congratulations-popups' ),
                'desc' => __( 'Set the popup background color.', 'gamipress-congratulations-popups' ),
                'type' => 'colorpicker',
                'options' => array( 'alpha' => true ),
            ),
            $prefix . 'title_color' => array(
                'name' => __( 'Title Color', 'gamipress-congratulations-popups' ),
                'desc' => __( 'Set the text color of the popup title.', 'gamipress-congratulations-popups' ),
                'type' => 'colorpicker',
                'options' => array( 'alpha' => true ),
            ),
            $prefix . 'text_color' => array(
                'name' => __( 'Text Color', 'gamipress-congratulations-popups' ),
                'desc' => __( 'Set the text color of the popup content.', 'gamipress-congratulations-popups' ),
                'type' => 'colorpicker',
                'options' => array( 'alpha' => true ),
            ),
        ),
        array(
            'priority' => 'core',
        )
    );

    // Condition Configuration
    gamipress_add_meta_box(
        'gamipress-congratulations-popup-condition',
        __( 'Condition Configuration', 'gamipress-congratulations-popups' ),
        'gamipress_congratulations_popups',
        array(
            $prefix . 'condition' => array(
                'name' 	    => __( 'Condition', 'gamipress-congratulations-popups' ),
                'type' 	    => 'select',
                'options'   => gamipress_congratulations_popups_get_congratulations_popup_conditions()
            ),
            $prefix . 'points' => array(
                'name' 	    => __( 'Points', 'gamipress-congratulations-popups' ),
                'type' 	    => 'gamipress_points',
            ),
            $prefix . 'achievement_type' => array(
                'name'        => __( 'Achievement Type', 'gamipress-congratulations-popups' ),
                'type'        => 'select',
                'option_all'  => false,
                'option_none' => true,
                'options_cb'  => 'gamipress_options_cb_achievement_types',
            ),
            $prefix . 'achievement' => array(
                'name'              => __( 'Achievement', 'gamipress-congratulations-popups' ),
                'type'              => 'select',
                'classes' 	        => 'gamipress-post-selector',
                'attributes' 	    => array(
                    'data-post-type' => implode( ',',  gamipress_get_achievement_types_slugs() ),
                    'data-placeholder' => __( 'Select an achievement', 'gamipress-congratulations-popups' ),
                ),
                'default'           => '',
                'options_cb'        => 'gamipress_options_cb_posts'
            ),
            $prefix . 'rank' => array(
                'name'              => __( 'Rank', 'gamipress-congratulations-popups' ),
                'type'              => 'select',
                'classes' 	        => 'gamipress-post-selector',
                'attributes' 	    => array(
                    'data-post-type' => implode( ',',  gamipress_get_rank_types_slugs() ),
                    'data-placeholder' => __( 'Select a rank', 'gamipress-congratulations-popups' ),
                ),
                'default'           => '',
                'options_cb'        => 'gamipress_options_cb_posts'
            ),
        ),
        array(
            'priority' => 'core',
        )
    );

    // Details
    gamipress_add_meta_box(
        'gamipress-congratulations-popup-details',
        __( 'Details', 'gamipress-congratulations-popups' ),
        'gamipress_congratulations_popups',
        array(
            'status' => array(
                'name' 	=> __( 'Status', 'gamipress-congratulations-popups' ),
                'type' 	=> 'select',
                'options' => gamipress_congratulations_popups_get_congratulations_popup_statuses()
            ),
            'date' => array(
                'name' 	=> __( 'Date', 'gamipress-congratulations-popups' ),
                'desc' 	=> __( 'Enter the popup creation date. This field is important since first popup will be displayed <strong>after</strong> date selected.', 'gamipress-congratulations-popups' ),
                'type' 	=> 'text_date_timestamp',
            ),
            $prefix . 'max_displays' => array(
                'name' 	=> __( 'Maximum Displays', 'gamipress-congratulations-popups' ),
                'desc' 	=> __( 'Maximum number of times that this popup will be displayed (set 0 for no maximum).', 'gamipress-congratulations-popups' ),
                'type' 	=> 'text',
                'attributes' => array(
                    'type' => 'number'
                ),
                'default' => '0',
            ),
            $prefix . 'max_displays_per_user' => array(
                'name' 	=> __( 'Maximum Displays Per User', 'gamipress-congratulations-popups' ),
                'desc' 	=> __( 'Maximum number of times per user that this popup will be displayed (set 0 for no maximum).', 'gamipress-congratulations-popups' ),
                'type' 	=> 'text',
                'attributes' => array(
                    'type' => 'number'
                ),
                'default' => '0',
            ),
        ),
        array(
            'context' => 'side',
        )
    );

}
add_action( 'cmb2_admin_init', 'gamipress_congratulations_popups_congratulations_popups_meta_boxes' );

/**
 * Turns array of date and time into a valid mysql date on update object data
 *
 * @since 1.0.0
 *
 * @param array $object_data
 * @param array $original_object_data
 *
 * @return array
 */
function gamipress_congratulations_popups_insert_congratulations_popup_data( $object_data, $original_object_data ) {

    global $ct_table;

    // If not is our custom table, return
    if( $ct_table->name !== 'gamipress_congratulations_popups' ) {
        return $object_data;
    }

    // Fix date format
    if( isset( $object_data['date'] ) && ! empty( $object_data['date'] ) ) {
        $object_data['date'] = date( 'Y-m-d 00:00:00', strtotime( $object_data['date'] ) );
    }

    return $object_data;

}
add_filter( 'ct_insert_object_data', 'gamipress_congratulations_popups_insert_congratulations_popup_data', 10, 2 );