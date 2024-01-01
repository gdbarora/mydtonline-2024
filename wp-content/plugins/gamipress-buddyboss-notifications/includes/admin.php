<?php
/**
 * Admin
 *
 * @package     GamiPress\BuddyBoss_Notifications\Admin
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Shortcut function to get plugin options
 *
 * @since  1.0.0
 *
 * @param string    $option_name
 * @param bool      $default
 *
 * @return mixed
 */
function gamipress_buddyboss_notifications_get_option( $option_name, $default = false ) {

    $prefix = 'gamipress_buddyboss_notifications_';

    return gamipress_get_option( $prefix . $option_name, $default );
}

/**
 * GamiPress BuddyBoss Notifications Settings meta boxes
 *
 * @since  1.0.0
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_buddyboss_notifications_settings_meta_boxes( $meta_boxes ) {

    $prefix = 'gamipress_buddyboss_notifications_';

    $meta_boxes['gamipress-buddyboss-notifications-settings'] = array(
        'title' => gamipress_dashicon( 'admin-comments' ) . __( 'BuddyBoss Notifications', 'gamipress-buddyboss-notifications' ),
        'fields' => apply_filters( 'gamipress_buddyboss_notifications_settings_fields', array(

            // Achievements

            $prefix . 'achievements_notice' => array(
                'type' => 'html',
                'content' => '<div class="gamipress-buddyboss-notifications-notice">'
                    . sprintf(
                        __(  '<strong>Note:</strong> You can customize these settings by type on each <a href="%s" target="_blank">achievement type\'s edit screen</a>.', 'gamipress-buddyboss-notifications' ),
                        admin_url( 'edit.php?post_type=achievement-type' )
                    )
                . '</div>',
            ),
            $prefix . 'disable_achievements' => array(
                'name' => __( 'Disable achievements notifications', 'gamipress-buddyboss-notifications' ),
                'desc' => __( 'Check this option to do not notify users about achievements unlocked.', 'gamipress-buddyboss-notifications' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'achievement_content' => array(
                'name' => __( 'Notification content', 'gamipress-buddyboss-notifications' ),
                'desc' => __( 'Achievement notification content. Available tags:', 'gamipress-buddyboss-notifications' )
                    . gamipress_buddyboss_notifications_get_achievement_pattern_tags_html(),
                'type' => 'wysiwyg',
            ),

            // Steps

            $prefix . 'steps_notice' => array(
                'type' => 'html',
                'content' => '<div class="gamipress-buddyboss-notifications-notice">'
                    . sprintf(
                        __(  '<strong>Note:</strong> You can customize these settings by type on each <a href="%s" target="_blank">achievement type\'s edit screen</a>.', 'gamipress-buddyboss-notifications' ),
                        admin_url( 'edit.php?post_type=achievement-type' )
                    )
                    . '</div>',
            ),
            $prefix . 'disable_steps' => array(
                'name' => __( 'Disable steps notifications', 'gamipress-buddyboss-notifications' ),
                'desc' => __( 'Check this option to do not notify users about steps completed.', 'gamipress-buddyboss-notifications' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'step_content' => array(
                'name' => __( 'Notification content', 'gamipress-buddyboss-notifications' ),
                'desc' => __( 'Step notification content. Available tags:', 'gamipress-buddyboss-notifications' )
                    . gamipress_buddyboss_notifications_get_step_pattern_tags_html(),
                'type' => 'wysiwyg',
            ),

            // Points awards

            $prefix . 'points_awards_notice' => array(
                'type' => 'html',
                'content' => '<div class="gamipress-buddyboss-notifications-notice">'
                    . sprintf(
                        __(  '<strong>Note:</strong> You can customize these settings by type on each <a href="%s" target="_blank">points type\'s edit screen</a>.', 'gamipress-buddyboss-notifications' ),
                        admin_url( 'edit.php?post_type=points-type' )
                    )
                    . '</div>',
            ),
            $prefix . 'disable_points_awards' => array(
                'name' => __( 'Disable points awards notifications', 'gamipress-buddyboss-notifications' ),
                'desc' => __( 'Check this option to do not notify users about points awards.', 'gamipress-buddyboss-notifications' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'points_award_content' => array(
                'name' => __( 'Notification content', 'gamipress-buddyboss-notifications' ),
                'desc' => __( 'Points award notification content. Available tags:', 'gamipress-buddyboss-notifications' )
                    . gamipress_buddyboss_notifications_get_points_award_pattern_tags_html(),
                'type' => 'wysiwyg',
            ),

            // Points deducts

            $prefix . 'points_deducts_notice' => array(
                'type' => 'html',
                'content' => '<div class="gamipress-buddyboss-notifications-notice">'
                    . sprintf(
                        __(  '<strong>Note:</strong> You can customize these settings by type on each <a href="%s" target="_blank">points type\'s edit screen</a>.', 'gamipress-buddyboss-notifications' ),
                        admin_url( 'edit.php?post_type=points-type' )
                    )
                    . '</div>',
            ),
            $prefix . 'disable_points_deducts' => array(
                'name' => __( 'Disable points deductions notifications', 'gamipress-buddyboss-notifications' ),
                'desc' => __( 'Check this option to do not notify users about points deductions.', 'gamipress-buddyboss-notifications' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'points_deduct_content' => array(
                'name' => __( 'Notification content', 'gamipress-buddyboss-notifications' ),
                'desc' => __( 'Points deduction notification content. Available tags:', 'gamipress-buddyboss-notifications' )
                    . gamipress_buddyboss_notifications_get_points_deduct_pattern_tags_html(),
                'type' => 'wysiwyg',
            ),

            // Ranks

            $prefix . 'ranks_notice' => array(
                'type' => 'html',
                'content' => '<div class="gamipress-buddyboss-notifications-notice">'
                    . sprintf(
                        __(  '<strong>Note:</strong> You can customize these settings by type on each <a href="%s" target="_blank">rank type\'s edit screen</a>.', 'gamipress-buddyboss-notifications' ),
                        admin_url( 'edit.php?post_type=rank-type' )
                    )
                    . '</div>',
            ),
            $prefix . 'disable_ranks' => array(
                'name' => __( 'Disable ranks notifications', 'gamipress-buddyboss-notifications' ),
                'desc' => __( 'Check this option to do not notify users about ranks reached.', 'gamipress-buddyboss-notifications' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'rank_content' => array(
                'name' => __( 'Notification content', 'gamipress-buddyboss-notifications' ),
                'desc' => __( 'Rank notification content. Available tags:', 'gamipress-buddyboss-notifications' )
                    . gamipress_buddyboss_notifications_get_rank_pattern_tags_html(),
                'type' => 'wysiwyg',
            ),

            // Rank Requirements

            $prefix . 'rank_requirements_notice' => array(
                'type' => 'html',
                'content' => '<div class="gamipress-buddyboss-notifications-notice">'
                    . sprintf(
                        __(  '<strong>Note:</strong> You can customize these settings by type on each <a href="%s" target="_blank">rank type\'s edit screen</a>.', 'gamipress-buddyboss-notifications' ),
                        admin_url( 'edit.php?post_type=rank-type' )
                    )
                    . '</div>',
            ),
            $prefix . 'disable_rank_requirements' => array(
                'name' => __( 'Disable rank requirements notifications', 'gamipress-buddyboss-notifications' ),
                'desc' => __( 'Check this option to do not notify to users about rank requirements completed.', 'gamipress-buddyboss-notifications' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'rank_requirement_content' => array(
                'name' => __( 'Notification content', 'gamipress-buddyboss-notifications' ),
                'desc' => __( 'Rank requirement notification content. Available tags:', 'gamipress-buddyboss-notifications' )
                    . gamipress_buddyboss_notifications_get_rank_requirement_pattern_tags_html(),
                'type' => 'wysiwyg',
            ),

        ) ),
        'tabs' => apply_filters( 'gamipress_buddyboss_notifications_settings_tabs', array(
            'achievement' => array(
                'icon' => 'dashicons-awards',
                'title' => __( 'Achievements', 'gamipress-buddyboss-notifications' ),
                'fields' => array(
                    $prefix . 'achievements_notice',
                    $prefix . 'disable_achievements',
                    $prefix . 'achievement_content',
                ),
            ),
            'steps' => array(
                'icon' => 'dashicons-editor-ol',
                'title' => __( 'Steps', 'gamipress-buddyboss-notifications' ),
                'fields' => array(
                    $prefix . 'steps_notice',
                    $prefix . 'disable_steps',
                    $prefix . 'step_content',
                ),
            ),
            'points_awards' => array(
                'icon' => 'dashicons-star-filled',
                'title' => __( 'Points Awards', 'gamipress-buddyboss-notifications' ),
                'fields' => array(
                    $prefix . 'points_awards_notice',
                    $prefix . 'disable_points_awards',
                    $prefix . 'points_award_content',
                ),
            ),
            'points_deducts' => array(
                'icon' => 'dashicons-star-empty',
                'title' => __( 'Points Deducts', 'gamipress-buddyboss-notifications' ),
                'fields' => array(
                    $prefix . 'points_deducts_notice',
                    $prefix . 'disable_points_deducts',
                    $prefix . 'points_deduct_content',
                ),
            ),
            'ranks' => array(
                'icon' => 'dashicons-rank',
                'title' => __( 'Ranks', 'gamipress-buddyboss-notifications' ),
                'fields' => array(
                    $prefix . 'ranks_notice',
                    $prefix . 'disable_ranks',
                    $prefix . 'rank_content',
                ),
            ),
            'rank_requirements' => array(
                'icon' => 'dashicons-editor-ol',
                'title' => __( 'Rank Requirements', 'gamipress-buddyboss-notifications' ),
                'fields' => array(
                    $prefix . 'rank_requirements_notice',
                    $prefix . 'disable_rank_requirements',
                    $prefix . 'rank_requirement_content',
                ),
            ),
        ) ),
        'vertical_tabs' => true
    );

    return $meta_boxes;

}
add_filter( 'gamipress_settings_addons_meta_boxes', 'gamipress_buddyboss_notifications_settings_meta_boxes' );

/**
 * Register plugin meta boxes
 *
 * @since  1.0.0
 */
function gamipress_buddyboss_notifications_meta_boxes() {

    $prefix = '_gamipress_buddyboss_notifications_';

    // -------------------------------
    // Achievement Type
    // -------------------------------

    gamipress_add_meta_box(
        'achievement-buddyboss-notifications',
        __( 'BuddyBoss Notifications', 'gamipress-buddyboss-notifications' ),
        'achievement-type',
        array(

            // Achievements

            $prefix . 'disable_achievements' => array(
                'name' => __( 'Disable achievements notifications', 'gamipress-buddyboss-notifications' ),
                'desc' => __( 'Check this option to do not notify users about achievements unlocked of this achievement type.', 'gamipress-buddyboss-notifications' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'achievement_content' => array(
                'name' => __( 'Notification content', 'gamipress-buddyboss-notifications' ),
                'desc' => __( 'Achievement notification content (leave blank to keep the content configured on settings). Available tags:', 'gamipress-buddyboss-notifications' )
                    . gamipress_buddyboss_notifications_get_achievement_pattern_tags_html(),
                'type' => 'wysiwyg',
            ),

            // Steps

            $prefix . 'disable_steps' => array(
                'name' => __( 'Disable steps notifications', 'gamipress-buddyboss-notifications' ),
                'desc' => __( 'Check this option to do not notify users about steps completed from achievements of this achievement type.', 'gamipress-buddyboss-notifications' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'step_content' => array(
                'name' => __( 'Notification content', 'gamipress-buddyboss-notifications' ),
                'desc' => __( 'Step notification content (leave blank to keep the content configured on settings). Available tags:', 'gamipress-buddyboss-notifications' )
                    . gamipress_buddyboss_notifications_get_step_pattern_tags_html(),
                'type' => 'wysiwyg',
            ),

        ),
        array(
            'tabs' => array(
                'achievement' => array(
                    'icon' => 'dashicons-awards',
                    'title' => __( 'Achievements', 'gamipress-buddyboss-notifications' ),
                    'fields' => array(
                        $prefix . 'disable_achievements',
                        $prefix . 'achievement_content',
                    ),
                ),
                'steps' => array(
                    'icon' => 'dashicons-editor-ol',
                    'title' => __( 'Steps', 'gamipress-buddyboss-notifications' ),
                    'fields' => array(
                        $prefix . 'disable_steps',
                        $prefix . 'step_content',
                    ),
                ),
            ),
            'vertical_tabs' => true
        )
    );

    // -------------------------------
    // Points Type
    // -------------------------------

    gamipress_add_meta_box(
        'points-type-buddyboss-notifications',
        __( 'BuddyBoss Notifications', 'gamipress-buddyboss-notifications' ),
        'points-type',
        array(

            // Points awards

            $prefix . 'disable_points_awards' => array(
                'name' => __( 'Disable points awards notifications', 'gamipress-buddyboss-notifications' ),
                'desc' => __( 'Check this option to do not notify users about points awards of this points type.', 'gamipress-buddyboss-notifications' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'points_award_content' => array(
                'name' => __( 'Notification content', 'gamipress-buddyboss-notifications' ),
                'desc' => __( 'Points award notification content (leave blank to keep the content configured on settings). Available tags:', 'gamipress-buddyboss-notifications' )
                    . gamipress_buddyboss_notifications_get_points_award_pattern_tags_html(),
                'type' => 'wysiwyg',
            ),

            // Points deducts

            $prefix . 'disable_points_deducts' => array(
                'name' => __( 'Disable points deductions notifications', 'gamipress-buddyboss-notifications' ),
                'desc' => __( 'Check this option to do not notify users about points deductions of this points type.', 'gamipress-buddyboss-notifications' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'points_deduct_content' => array(
                'name' => __( 'Notification content', 'gamipress-buddyboss-notifications' ),
                'desc' => __( 'Points deduction notification content (leave blank to keep the content configured on settings). Available tags:', 'gamipress-buddyboss-notifications' )
                    . gamipress_buddyboss_notifications_get_points_deduct_pattern_tags_html(),
                'type' => 'wysiwyg',
            ),

        ),
        array(
            'tabs' => array(
                'points_awards' => array(
                    'icon' => 'dashicons-star-filled',
                    'title' => __( 'Points Awards', 'gamipress-buddyboss-notifications' ),
                    'fields' => array(
                        $prefix . 'disable_points_awards',
                        $prefix . 'points_award_content',
                    ),
                ),
                'points_deducts' => array(
                    'icon' => 'dashicons-star-empty',
                    'title' => __( 'Points Deducts', 'gamipress-buddyboss-notifications' ),
                    'fields' => array(
                        $prefix . 'disable_points_deducts',
                        $prefix . 'points_deduct_content',
                    ),
                ),
            ),
            'vertical_tabs' => true
        )
    );

    // -------------------------------
    // Rank Type
    // -------------------------------

    gamipress_add_meta_box(
        'rank-type-buddyboss-notifications',
        __( 'BuddyBoss Notifications', 'gamipress-buddyboss-notifications' ),
        'rank-type',
        array(

            // Ranks

            $prefix . 'disable_ranks' => array(
                'name' => __( 'Disable ranks notifications', 'gamipress-buddyboss-notifications' ),
                'desc' => __( 'Check this option to do not notify users about ranks reached of this rank type.', 'gamipress-buddyboss-notifications' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'rank_content' => array(
                'name' => __( 'Notification content', 'gamipress-buddyboss-notifications' ),
                'desc' => __( 'Rank notification content (leave blank to keep the content configured on settings). Available tags:', 'gamipress-buddyboss-notifications' )
                    . gamipress_buddyboss_notifications_get_rank_pattern_tags_html(),
                'type' => 'wysiwyg',
            ),

            // Rank Requirements

            $prefix . 'disable_rank_requirements' => array(
                'name' => __( 'Disable rank requirements notifications', 'gamipress-buddyboss-notifications' ),
                'desc' => __( 'Check this option to do not notify to users about rank requirements completed from ranks of this rank type.', 'gamipress-buddyboss-notifications' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'rank_requirement_content' => array(
                'name' => __( 'Notification content', 'gamipress-buddyboss-notifications' ),
                'desc' => __( 'Rank requirement notification content (leave blank to keep the content configured on settings). Available tags:', 'gamipress-buddyboss-notifications' )
                    . gamipress_buddyboss_notifications_get_rank_requirement_pattern_tags_html(),
                'type' => 'wysiwyg',
            ),

        ),
        array(
            'tabs' => array(
                'rank' => array(
                    'icon' => 'dashicons-rank',
                    'title' => __( 'Ranks', 'gamipress-buddyboss-notifications' ),
                    'fields' => array(
                        $prefix . 'disable_ranks',
                        $prefix . 'rank_content',
                    ),
                ),
                'rank_requirements' => array(
                    'icon' => 'dashicons-editor-ol',
                    'title' => __( 'Rank Requirements', 'gamipress-buddyboss-notifications' ),
                    'fields' => array(
                        $prefix . 'disable_rank_requirements',
                        $prefix . 'rank_requirement_content',
                    ),
                ),
            ),
            'vertical_tabs' => true
        )
    );

}
add_action( 'cmb2_admin_init', 'gamipress_buddyboss_notifications_meta_boxes' );

/**
 * GamiPress BuddyBoss Notifications Licensing meta box
 *
 * @since  1.0.0
 *
 * @param $meta_boxes
 *
 * @return mixed
 */
function gamipress_buddyboss_notifications_licenses_meta_boxes( $meta_boxes ) {

    $meta_boxes['gamipress-buddyboss-notifications-license'] = array(
        'title' => __( 'GamiPress BuddyBoss Notifications', 'gamipress-buddyboss-notifications' ),
        'fields' => array(
            'gamipress_buddyboss_notifications_license' => array(
                'name' => __( 'License', 'gamipress-buddyboss-notifications' ),
                'type' => 'edd_license',
                'file' => GAMIPRESS_BUDDYBOSS_NOTIFICATIONS_FILE,
                'item_name' => 'BuddyBoss Notifications',
            ),
        )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_settings_licenses_meta_boxes', 'gamipress_buddyboss_notifications_licenses_meta_boxes' );

/**
 * GamiPress BuddyBoss Notifications automatic updates
 *
 * @since  1.0.0
 *
 * @param array $automatic_updates_plugins
 *
 * @return array
 */
function gamipress_buddyboss_notifications_automatic_updates( $automatic_updates_plugins ) {

    $automatic_updates_plugins['gamipress-buddyboss-notifications'] = __( 'BuddyBoss Notifications', 'gamipress-buddyboss-notifications' );

    return $automatic_updates_plugins;
}
add_filter( 'gamipress_automatic_updates_plugins', 'gamipress_buddyboss_notifications_automatic_updates' );