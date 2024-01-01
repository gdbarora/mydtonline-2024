<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wbcomdesigns.com/
 * @since             1.0.0
 * @package           Buddypress_Hashtags
 *
 * @wordpress-plugin
 * Plugin Name:       Wbcom Designs- BuddyPress Hashtags
 * Plugin URI:        https://wbcomdesigns.com/buddypress-hashtags
 * Description:       The plugin gives the ability to use hashtags on any buddypress,bbpress and WordPress posts and pages.
 * Version:           2.9.8
 * Author:            Wbcom Designs
 * Author URI:        https://wbcomdesigns.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       buddypress-hashtags
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
if ( ! defined( 'BPHT_PLUGIN_VERSION' ) ) {
	define( 'BPHT_PLUGIN_VERSION', '2.9.8' );
}
if ( ! defined( 'BPHT_PLUGIN_FILE' ) ) {
	define( 'BPHT_PLUGIN_FILE', __FILE__ );
}
if ( ! defined( 'BPHT_PLUGIN_BASENAME' ) ) {
	define( 'BPHT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}
if ( ! defined( 'BPHT_PLUGIN_URL' ) ) {
	define( 'BPHT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'BPHT_PLUGIN_PATH' ) ) {
	define( 'BPHT_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-buddypress-hashtags.php';

/**
 * Require plugin license file.
 */
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
function run_buddypress_hashtags() {

	$plugin = new Buddypress_Hashtags();
	$plugin->run();
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'buddypress_hashtags_plugin_links' );

}

add_action( 'plugins_loaded', 'bpht_loaded', 9 );
function bpht_loaded() {

	if ( has_action( 'bp_loaded' ) ) {
		add_action( 'bp_include', 'run_buddypress_hashtags' );
	} elseif ( has_action( 'bbp_loaded' ) ) {
		add_action( 'bbp_includes', 'run_buddypress_hashtags' );
	}
}
/**
 *
 * Function to create table on plugin registration.
 */

register_activation_hook( __FILE__, 'bpht_create_hashtag_table' );
function bpht_create_hashtag_table() {
	global $wpdb;

	$table_name = $wpdb->prefix . 'bpht_hashtags';

	$bpht_charset = $wpdb->get_charset_collate();

	if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
		$bpht_sql = "CREATE TABLE $table_name (ht_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,ht_name varchar(128),ht_type varchar(28),ht_count bigint(20) UNSIGNED NULL DEFAULT '0',ht_last_count TIMESTAMP DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (ht_id),UNIQUE INDEX ( `ht_name`, `ht_type` )) $bpht_charset;";
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $bpht_sql );
	}

	$hashtags_items_table_name = $wpdb->prefix . 'bpht_hashtags_items ';

	if ( $wpdb->get_var( "SHOW TABLES LIKE '$hashtags_items_table_name'" ) != $hashtags_items_table_name ) {
		$bpht_sql = "CREATE TABLE $hashtags_items_table_name (
			id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id  bigint(20),
			item_id bigint(20) UNSIGNED NULL DEFAULT '0',
			type varchar(255),
			hashtag_items  varchar(255),
			created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id)) $bpht_charset;";
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $bpht_sql );
	}
}

function buddypress_hashtags_plugin_links( $links ) {
	$bp_hashtag_links = array(
		'<a href="' . admin_url( 'admin.php?page=buddypress_hashtags' ) . '">' . esc_html__( 'Settings', 'buddypress-hashtags' ) . '</a>',
		'<a href="https://wbcomdesigns.com/contact/" target="_blank">' . esc_html__( 'Support', 'buddypress-hashtags' ) . '</a>',
	);
	return array_merge( $links, $bp_hashtag_links );
}


/**
 * redirect to plugin settings page after activated
 */

add_action( 'activated_plugin', 'buddypress_hashtags_activation_redirect_settings' );
function buddypress_hashtags_activation_redirect_settings( $plugin ) {
	if ( ! isset( $_GET['plugin'] ) ) { //phpcs:ignore
		return;
	}
	if ( $plugin == plugin_basename( __FILE__ ) && ( class_exists( 'Buddypress' ) || class_exists( 'bbPress' ) ) ) {
		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action']  == 'activate' && isset( $_REQUEST['plugin'] ) && $_REQUEST['plugin'] == $plugin) { //phpcs:ignore
			wp_redirect( admin_url( 'admin.php?page=buddypress_hashtags' ) );
			exit;
		}
	}
}


/**
 *  Check if buddypress activate.
 */
if ( ! function_exists( 'buddypress_hashtags_requires_buddypress' ) ) {
	add_action( 'admin_init', 'buddypress_hashtags_requires_buddypress' );
	function buddypress_hashtags_requires_buddypress() {
		global $pagenow;
		if ( ! class_exists( 'Buddypress' ) && ! class_exists( 'bbPress' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			// deactivate_plugins('buddypress-polls/buddypress-polls.php');
			add_action( 'admin_notices', 'buddypress_hashtags_plugin_admin_notice' );
			unset( $_GET['activate'] ); //phpcs:ignore
		} else {

			if ( $pagenow == 'plugins.php' || ( isset( $_GET['page'] ) && $_GET['page'] == 'buddypress_hashtags' ) ) { //phpcs:ignore
				bpht_create_hashtag_table();
			}
		}
	}
}


function buddypress_hashtags_plugin_admin_notice() {
	$buddypress_hashtags_plugin = __( 'BuddyPress Hashtags', 'buddypress-hashtags' );
	$bp_plugin                  = 'BuddyPress';

	echo '<div class="error"><p>'
	/* translators: %1$s: BuddyPress Hashtags ; %2$s: BuddyPress*/
	. sprintf( esc_html__( '%1$s is ineffective as it requires %2$s to be active.', 'buddypress-hashtags' ), '<strong>' . esc_attr( $buddypress_hashtags_plugin ) . '</strong>', '<strong>' . esc_attr( $bp_plugin ) . '</strong>' )
	. '</p></div>';
	if ( isset( $_GET['activate'] ) ) { //phpcs:ignore
		unset( $_GET['activate'] ); //phpcs:ignore
	}
}
