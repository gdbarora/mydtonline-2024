<?php
/**
 * Plugin Name:     GamiPress - BuddyBoss Notifications
 * Plugin URI:      https://gamipress.com/add-ons/gamipress-buddyboss-notifications
 * Description:     Instantly notify of achievements, steps and/or points awards completion to your BuddyBoss members.
 * Version:         1.0.7
 * Author:          GamiPress
 * Author URI:      https://gamipress.com/
 * Text Domain:     gamipress-buddyboss-notifications
 * License:         GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package         GamiPress\BuddyBoss_Notifications
 * @author          GamiPress
 * @copyright       Copyright (c) GamiPress
 */

final class GamiPress_BuddyBoss_Notifications {

    /**
     * @var         GamiPress_BuddyBoss_Notifications $instance The one true GamiPress_BuddyBoss_Notifications
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      GamiPress_BuddyBoss_Notifications self::$instance The one true GamiPress_BuddyBoss_Notifications
     */
    public static function instance() {

        if( ! self::$instance ) {

            self::$instance = new GamiPress_BuddyBoss_Notifications();
            self::$instance->constants();
            self::$instance->includes();
            self::$instance->hooks();
            self::$instance->load_textdomain();

        }

        return self::$instance;

    }

    /**
     * Setup plugin constants
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function constants() {
        // Plugin version
        define( 'GAMIPRESS_BUDDYBOSS_NOTIFICATIONS_VER', '1.0.7' );

        // GamiPress minimum required version
        define( 'GAMIPRESS_BUDDYBOSS_NOTIFICATIONS_GAMIPRESS_MIN_VER', '2.0.0' );

        // Plugin file
        define( 'GAMIPRESS_BUDDYBOSS_NOTIFICATIONS_FILE', __FILE__ );

        // Plugin path
        define( 'GAMIPRESS_BUDDYBOSS_NOTIFICATIONS_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'GAMIPRESS_BUDDYBOSS_NOTIFICATIONS_URL', plugin_dir_url( __FILE__ ) );
    }

    /**
     * Include plugin files
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function includes() {

        if( $this->meets_requirements() ) {

            require_once GAMIPRESS_BUDDYBOSS_NOTIFICATIONS_DIR . 'includes/admin.php';
            require_once GAMIPRESS_BUDDYBOSS_NOTIFICATIONS_DIR . 'includes/filters.php';
            require_once GAMIPRESS_BUDDYBOSS_NOTIFICATIONS_DIR . 'includes/functions.php';
            require_once GAMIPRESS_BUDDYBOSS_NOTIFICATIONS_DIR . 'includes/listeners.php';
            require_once GAMIPRESS_BUDDYBOSS_NOTIFICATIONS_DIR . 'includes/scripts.php';
            require_once GAMIPRESS_BUDDYBOSS_NOTIFICATIONS_DIR . 'includes/template-functions.php';

        }
    }

    /**
     * Setup plugin hooks
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function hooks() {
        add_action( 'admin_notices', array( $this, 'admin_notices' ) );
    }

    /**
     * Activation hook for the plugin.
     *
     * @since  1.0.0
     */
    static function activate() {

        GamiPress_BuddyBoss_Notifications::instance();

        // Get stored version
        if( gamipress_is_network_wide_active() ) {
            $stored_version = get_site_option( 'gamipress_settings', '1.0.0' );
        } else {
            $stored_version = get_option( 'gamipress_buddyboss_notifications_version', '1.0.0' );
        }

        // Get GamiPress options
        if( gamipress_is_network_wide_active() ) {
            $gamipress_settings = get_site_option( 'gamipress_settings', array() );
        } else {
            $gamipress_settings = get_option( 'gamipress_settings', array() );
        }

        // Initialize default settings
        $default_settings = array(
            // Achievements
            'achievement_content' => __( 'You have unlocked the {achievement_type} {link}', 'gamipress-buddyboss-notifications' ),
            // Steps
            'step_content' => __( 'You have completed the step "{label}" of the {achievement_type} {achievement_link}', 'gamipress-buddyboss-notifications' ),
            // Points awards
            'points_award_content' => __( 'You have earned {points} {points_label} for completing "{label}"', 'gamipress-buddyboss-notifications' ),
            // Points deducts
            'points_deduct_content' => __( 'You have lost {points} {points_label} for "{label}"', 'gamipress-buddyboss-notifications' ),
            // Ranks
            'rank_content' => __( 'You have reached the {rank_type} {link}', 'gamipress-buddyboss-notifications' ),
            // Rank requirements
            'rank_requirement_content' => __( 'You have completed the requirement "{label}" of the {rank_type} {rank_link}', 'gamipress-buddyboss-notifications' ),
        );

        // Add-on settings prefix
        $prefix = 'gamipress_buddyboss_notifications_';

        foreach( $default_settings as $setting => $value ) {

            // If setting not exists, update it
            if( ! isset( $gamipress_settings[$prefix . $setting] ) ) {
                $gamipress_settings[$prefix . $setting] = $value;
            }

        }

        // Update GamiPress options
        if( gamipress_is_network_wide_active() ) {
            update_site_option( 'gamipress_settings', $gamipress_settings );
        } else {
            update_option( 'gamipress_settings', $gamipress_settings );
        }

        // Updated stored version
        if( gamipress_is_network_wide_active() ) {
            update_site_option( 'gamipress_buddyboss_notifications_version', GAMIPRESS_BUDDYBOSS_NOTIFICATIONS_VER );
        } else {
            update_option( 'gamipress_buddyboss_notifications_version', GAMIPRESS_BUDDYBOSS_NOTIFICATIONS_VER );
        }


    }

    /**
     * Deactivation hook for the plugin.
     *
     * @since  1.0.0
     */
    static function deactivate() {

    }

    /**
     * Plugin admin notices.
     *
     * @since  1.0.0
     */
    public function admin_notices() {

        if ( ! $this->meets_requirements() && ! defined( 'GAMIPRESS_ADMIN_NOTICES' ) ) : ?>

            <div id="message" class="notice notice-error is-dismissible">
                <p>
                    <?php printf(
                        __( 'GamiPress - BuddyBoss integration requires %s (%s or higher) and %s in order to work. Please install and activate them.', 'gamipress-buddyboss-integration' ),
                        '<a href="https://wordpress.org/plugins/gamipress/" target="_blank">GamiPress</a>',
                        GAMIPRESS_BUDDYBOSS_NOTIFICATIONS_GAMIPRESS_MIN_VER,
                        '<a href="https://wordpress.org/plugins/buddyboss/" target="_blank">BuddyBoss</a>'
                    ); ?>
                </p>
            </div>

            <?php define( 'GAMIPRESS_ADMIN_NOTICES', true ); ?>

        <?php endif;

    }

    /**
     * Check if there are all plugin requirements
     *
     * @since  1.0.0
     *
     * @return bool True if installation meets all requirements
     */
    private function meets_requirements() {

        if ( ! class_exists( 'GamiPress' ) ) {
            return false;
        }

        if( version_compare( GAMIPRESS_VER, GAMIPRESS_BUDDYBOSS_NOTIFICATIONS_GAMIPRESS_MIN_VER, '<' ) ) {
            return false;
        }

        // Requirements on multisite install
        if( is_multisite() && gamipress_is_network_wide_active() && is_main_site() ) {
            // On main site, need to check if integrated plugin is installed on any sub site to load all configuration files
            if( gamipress_is_plugin_active_on_network( 'buddyboss-platform/bp-loader.php' ) )
                return true;
        }

        if ( ! defined( 'BP_PLATFORM_VERSION' ) ) {
            return false;
        }

        return true;

    }

    /**
     * Internationalization
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function load_textdomain() {

        // Set filter for language directory
        $lang_dir = GAMIPRESS_BUDDYBOSS_NOTIFICATIONS_DIR . '/languages/';
        $lang_dir = apply_filters( 'gamipress_buddyboss_notifications_languages_directory', $lang_dir );

        // Traditional WordPress plugin locale filter
        $locale = apply_filters( 'plugin_locale', get_locale(), 'gamipress-buddyboss-notifications' );
        $mofile = sprintf( '%1$s-%2$s.mo', 'gamipress-buddyboss-notifications', $locale );

        // Setup paths to current locale file
        $mofile_local   = $lang_dir . $mofile;
        $mofile_global  = WP_LANG_DIR . '/gamipress-buddyboss-notifications/' . $mofile;

        if( file_exists( $mofile_global ) ) {
            // Look in global /wp-content/languages/gamipress/ folder
            load_textdomain( 'gamipress-buddyboss-notifications', $mofile_global );
        } elseif( file_exists( $mofile_local ) ) {
            // Look in local /wp-content/plugins/gamipress/languages/ folder
            load_textdomain( 'gamipress-buddyboss-notifications', $mofile_local );
        } else {
            // Load the default language files
            load_plugin_textdomain( 'gamipress-buddyboss-notifications', false, $lang_dir );
        }

    }

}

/**
 * The main function responsible for returning the one true GamiPress_BuddyBoss_Notifications instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \GamiPress_BuddyBoss_Notifications The one true GamiPress_BuddyBoss_Notifications
 */
function GamiPress_BuddyBoss_Notifications() {
    return GamiPress_BuddyBoss_Notifications::instance();
}
add_action( 'plugins_loaded', 'GamiPress_BuddyBoss_Notifications' );

// Setup our activation and deactivation hooks
register_activation_hook( __FILE__, array( 'GamiPress_BuddyBoss_Notifications', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'GamiPress_BuddyBoss_Notifications', 'deactivate' ) );
