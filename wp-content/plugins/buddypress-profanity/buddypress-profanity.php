<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.wbcomdesigns.com
 * @since             1.0.0
 * @package           Buddypress_Profanity
 *
 * @wordpress-plugin
 * Plugin Name:       Wbcom Designs - BuddyPress Profanity
 * Plugin URI:        https://wbcomdesigns.com/downloads/buddypress-profanity/
 * Description:       This buddypress plugin filter out any kind of bad language and gives your community peace of mind.The plugin keeps your content family-friendly and don't show any profanity in your community’s posts or comments.
 * Version:           1.9.6
 * Author:            Wbcom Designs
 * Author URI:        http://www.wbcomdesigns.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       buddypress-profanity
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! defined( 'BP_ENABLE_MULTIBLOG' ) ) {
	define( 'BP_ENABLE_MULTIBLOG', false );
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
if ( ! defined( 'BPPROF_PLUGIN_VERSION' ) ) {
	define( 'BPPROF_PLUGIN_VERSION', '1.9.6' );
}
if ( ! defined( 'BPPROF_PLUGIN_FILE' ) ) {
	define( 'BPPROF_PLUGIN_FILE', __FILE__ );
}
if ( ! defined( 'BPPROF_PLUGIN_BASENAME' ) ) {
	define( 'BPPROF_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}
if ( ! defined( 'BPPROF_PLUGIN_URL' ) ) {
	define( 'BPPROF_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'BPPROF_PLUGIN_PATH' ) ) {
	define( 'BPPROF_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-buddypress-profanity-activator.php
 */
function activate_buddypress_profanity() {
	if ( class_exists( 'Buddypress' ) ) {
		if ( bp_profanity_check_config() ) {
			run_buddypress_profanity();
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wbbprof_plugin_links' );
			wbbprof_update_blog();
		}
	}

}

/**
 * BP Profanity configuration on BP Loaded.
 */
function bp_profanity_check_config() {
	global $bp;
	$config = array(
		'blog_status'    => false,
		'network_active' => false,
		'network_status' => true,
	);
	if ( get_current_blog_id() == bp_get_root_blog_id() ) {
		$config['blog_status'] = true;
	}

	$network_plugins = get_site_option( 'active_sitewide_plugins', array() );

	// No Network plugins.
	if ( empty( $network_plugins ) ) {

		// Looking for BuddyPress and bp-activity plugin.
		$check[] = $bp->basename;
	}
	$check[] = BPPROF_PLUGIN_BASENAME;

	// Are they active on the network ?
	$network_active = array_diff( $check, array_keys( $network_plugins ) );

	// If result is 1, your plugin is network activated
	// and not BuddyPress or vice & versa. Config is not ok.
	if ( count( $network_active ) == 1 ) {
		$config['network_status'] = false;
	}

	// We need to know if the plugin is network activated to choose the right
	// notice ( admin or network_admin ) to display the warning message.
	$config['network_active'] = isset( $network_plugins[ BPPROF_PLUGIN_BASENAME ] );

	// if BuddyPress config is different than bp-activity plugin.
	if ( ! $config['blog_status'] || ! $config['network_status'] ) {

		$warnings = array();
		if ( ! bp_core_do_network_admin() && ! $config['blog_status'] ) {
			add_action( 'admin_notices', 'bpprof_same_blog' );
			$warnings[] = __( 'BuddyPress Profanity requires to be activated on the blog where BuddyPress is activated.', 'buddypress-profanity' );
		}

		if ( bp_core_do_network_admin() && ! $config['network_status'] ) {
			add_action( 'admin_notices', 'bpprof_same_network_config' );
			$warnings[] = __( 'BuddyPress Profanity and BuddyPress need to share the same network configuration.', 'buddypress-profanity' );
		}

		if ( ! empty( $warnings ) ) :
			return false;
		endif;
	}
	return true;
}

/**
 * Admin notice when BuddyPress is not activated.
 */
function bpprof_same_blog() {
	echo '<div class="error"><p>'
	. esc_html( __( 'BuddyPress Private Community Pro requires to be activated on the blog where BuddyPress is activated.', 'buddypress-profanity' ) )
	. '</p></div>';
}

/**
 * Admin notice for share the same network configuration.
 */
function bpprof_same_network_config() {
	echo '<div class="error"><p>'
	. esc_html( __( 'BuddyPress Private Community Pro and BuddyPress need to share the same network configuration.', 'buddypress-profanity' ) )
	. '</p></div>';
}
/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-buddypress-profanity-deactivator.php
 */
function deactivate_buddypress_profanity() {
	if ( function_exists( 'is_multisite' ) && is_multisite() ) {
	}
}

register_activation_hook( __FILE__, 'activate_buddypress_profanity' );
register_deactivation_hook( __FILE__, 'deactivate_buddypress_profanity' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-buddypress-profanity.php';

require plugin_dir_path( __FILE__ ) . 'edd-license/edd-plugin-license.php';

require_once __DIR__ . '/vendor/autoload.php';
HardG\BuddyPress120URLPolyfills\Loader::init();
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_buddypress_profanity() {

	$plugin = new Buddypress_Profanity();
	$plugin->run();

}

/**
 * Plugin notice while activiting on multisite.
 */
function wbbprof_network_admin_notices() {
	$wbbprof_plugin = 'BuddyPress Profanity';
	$bp_plugin      = 'BuddyPress';

	echo '<div class="error"><p>'
	/* translators: %1$s: BuddyPress Profanity, %2$s: BuddyPress */
	. sprintf( esc_html__( '%1$s is ineffective as it requires %2$s to be installed and active.', 'buddypress-profanity' ), '<strong>' . esc_html( $wbbprof_plugin ) . '</strong>', '<strong>' . esc_html( $bp_plugin ) . '</strong>' )
	. '</p></div>';
	if ( null !== filter_input( INPUT_GET, 'activate' ) ) {
		$activate = filter_input( INPUT_GET, 'activate' );
		unset( $activate );
	}
}

/**
 * Function to add plugin links.
 *
 * @param array $links Plugin action links array.
 */
function wbbprof_plugin_links( $links ) {
	$wbbprof_links = array(
		'<a href="' . admin_url( 'admin.php?page=buddypress_profanity' ) . '">' . __( 'Settings', 'buddypress-profanity' ) . '</a>',
		'<a href="https://wbcomdesigns.com/contact/" target="_blank">' . __( 'Support', 'buddypress-profanity' ) . '</a>',
	);
	return array_merge( $links, $wbbprof_links );
}

/**
 * Function to add plugin links.
 *
 * @param int $blog_id Blog id.
 */
function wbbprof_update_blog( $blog_id = null ) {
	if ( $blog_id ) {
		switch_to_blog( $blog_id );
	}
	$wbbprof_settings = bp_get_option( 'wbbprof_settings' );
	if ( empty( $wbbprof_settings ) ) {
		$wbbprof_settings = array(
			'keywords'        => 'FrontGate,Profanity,aeolus,ahole,bitch,bang,bollock,breast,enlargement,erotic,goddamn,heroin,hell,kooch,nad,nigger,pecker,tubgirl,unwed,woody,yeasty,yobbo,zoophile',
			'filter_contents' => array(
				'0' => 'status_updates',
				'1' => 'activity_comments',
				'2' => 'messages',
			),
			'word_render'     => 'first_last',
			'character'       => 'asterisk',
			'case'            => 'incase',
			'strict_filter'   => 'on',
		);
		bp_update_option( 'wbbprof_settings', $wbbprof_settings );
	}
	if ( $blog_id ) {
		restore_current_blog();
	}
}

add_action( 'bp_loaded', 'wbbprof_plugin_init' );

/**
 * Function to check buddypress is active to enable disable plugin functionality.
 */
function wbbprof_plugin_init() {
	if ( bp_profanity_check_config() ) {
		run_buddypress_profanity();
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wbbprof_plugin_links' );
	}
}

/**
 *  Check if buddypress activate.
 */
function bpprofanity_requires_buddypress() {
	if ( ! class_exists( 'Buddypress' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		add_action( 'admin_notices', 'bpprofanity_required_plugin_admin_notice' );
		if ( null !== filter_input( INPUT_GET, 'activate' ) ) {
			$activate = filter_input( INPUT_GET, 'activate' );
			unset( $activate );
		}
	}
}
add_action( 'admin_init', 'bpprofanity_requires_buddypress' );

/**
 * Throw an Alert to tell the Admin why it didn't activate.
 *
 * @author wbcomdesigns
 * @since  1.2.0
 */
function bpprofanity_required_plugin_admin_notice() {
	$bpquotes_plugin = esc_html__( 'BuddyPress Profanity', 'buddypress-profanity' );
	$bp_plugin       = esc_html__( 'BuddyPress', 'buddypress-profanity' );
	echo '<div class="error"><p>';
	/* translators: %1$s: BuddyPress Profanity, %2$s: BuddyPress */
	echo sprintf( esc_html__( '%1$s is ineffective now as it requires %2$s to be installed and active.', 'buddypress-profanity' ), '<strong>' . esc_html( $bpquotes_plugin ) . '</strong>', '<strong>' . esc_html( $bp_plugin ) . '</strong>' );
	echo '</p></div>';
	if ( null !== filter_input( INPUT_GET, 'activate' ) ) {
		$activate = filter_input( INPUT_GET, 'activate' );
		unset( $activate );
	}
}


/**
 * Redirect to plugin settings page after activated.
 *
 * @param plugin $plugin plugin.
 */
function bpprofanity_activation_redirect_settings( $plugin ) {
	$plugins = filter_input( INPUT_GET, 'plugin' ) ? filter_input( INPUT_GET, 'plugin' ) : '';
	if ( ! isset( $plugins ) ) {
		return;
	}
	if ( plugin_basename( __FILE__ ) === $plugin && class_exists( 'Buddypress' ) ) {
		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action']  == 'activate' && isset( $_REQUEST['plugin'] ) && $_REQUEST['plugin'] == $plugin) { //phpcs:ignore
			wp_safe_redirect( admin_url( 'admin.php?page=buddypress_profanity' ) );
			exit;
		}
	}
}
add_action( 'activated_plugin', 'bpprofanity_activation_redirect_settings' );

