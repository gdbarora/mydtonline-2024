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
 * @package           Buddypress_Polls
 *
 * @wordpress-plugin
 * Plugin Name:       Wbcom Designs – BuddyPress Polls
 * Plugin URI:        http://www.wbcomdesigns.com/plugins/
 * Description:       BuddyPress Polls plugin allows you and your community to create polls in posts. The polls can be placed in the main activity stream, in users’ profiles and even in groups.
 * Version:           4.3.6
 * Author:            wbcomdesigns
 * Author URI:        https://wbcomdesigns.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Tested up to:      6.3.0
 * Text Domain:       buddypress-polls
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
if ( ! defined( 'BPOLLS_PLUGIN_VERSION' ) ) {
	define( 'BPOLLS_PLUGIN_VERSION', '4.3.6' );
}

if ( ! defined( 'BPOLLS_PLUGIN_FILE' ) ) {
	define( 'BPOLLS_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'BPOLLS_PLUGIN_URL' ) ) {
	define( 'BPOLLS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'BPOLLS_PLUGIN_PATH' ) ) {
	define( 'BPOLLS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'BPOLLS_PLUGIN_BASENAME' ) ) {
	define( 'BPOLLS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}

defined('BPOLLS_COOKIE_EXPIRATION') or define(
	'BPOLLS_COOKIE_EXPIRATION',
	time() + 1209600
); //Expiration of 14 days.
defined('BPOLLS_COOKIE_NAME') or define('BPOLLS_COOKIE_NAME', 'wbpoll-cookie');
defined('BPOLLS_RAND_MIN') or define('BPOLLS_RAND_MIN', 0);
defined('BPOLLS_RAND_MAX') or define('BPOLLS_RAND_MAX', 999999);
defined('BPOLLS_COOKIE_EXPIRATION_14DAYS') or define(
	'BPOLLS_COOKIE_EXPIRATION_14DAYS',
	time() + 1209600
); //Expiration of 14 days.
defined('BPOLLS_COOKIE_EXPIRATION_7DAYS') or define(
	'BPOLLS_COOKIE_EXPIRATION_7DAYS',
	time() + 604800
); //Expiration of 7 days.
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-buddypress-polls-activator.php
 */
function activate_buddypress_polls() {

	WBPollHelper::install_table();
	
	$wbpolls_settings = get_option( 'wbpolls_settings');

	if ( false === get_option( 'bpolls_settings' ) || empty( get_option( 'bpolls_settings' ) ) ) {
		global $wp_roles;
		$bpolls_settings['limit_poll_activity']    = 'no';
		$bpolls_settings['options_limit']          = '5';
		$bpolls_settings['poll_options_result']    = 'yes';
		$bpolls_settings['poll_list_voters']       = 'yes';
		$bpolls_settings['poll_limit_voters']      = '3';
		$bpolls_settings['polls_background_color'] = '#4caf50';
		$bpolls_settings['multiselect']            = 'no';
		$bpolls_settings['user_additional_option'] = 'no';
		$bpolls_settings['hide_results']           = 'no';
		$bpolls_settings['close_date']             = 'no';
		$bpolls_settings['enable_image']           = 'no';
		$bpolls_settings['poll_revoting']          = 'no';
		$roles                                     = $wp_roles->get_names();
		foreach ( $roles as $role => $role_name ) {
			$bpolls_settings['poll_user_role'][] = $role;
		}
		update_option( 'bpolls_settings', $bpolls_settings );
	}

	/**
	 * create a page for frontend poll
	 */
	$page_title = 'Poll Dashboard';
	$poll_dashboard_page = bpolls_get_page_by_title( $page_title );
	if ( empty($poll_dashboard_page) && (empty($wbpolls_settings) || !isset($wbpolls_settings['poll_dashboard_page'])) ) {
		$dashboard_page_id = wp_insert_post(
			array(
				'post_title'     => $page_title,
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'comment_status' => 'closed',
			)
		);
	}
	/**
	 * create a page for frontend poll
	 */
	$page_title = 'Create Poll';
	$create_poll_page = bpolls_get_page_by_title( $page_title );	
	if ( empty($create_poll_page) && (empty($wbpolls_settings) || !isset($wbpolls_settings['create_poll_page'])) ) {
		$create_page_id = wp_insert_post(
			array(
				'post_title'     => $page_title,
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'comment_status' => 'closed',
			)
		);
	}

	if ( false === get_option( 'wbpolls_settings' ) ) {
		global $wp_roles;
		$bpolls_settings['poll_dashboard_page']       = $dashboard_page_id;
		$bpolls_settings['create_poll_page']          = $create_page_id;
		$bpolls_settings['wbpolls_user_add_extra_op'] = 'no';
		$bpolls_settings['wbpolls_submit_status']     = 'publish';
		$bpolls_settings['wppolls_show_result']       = 'yes';
		$bpolls_settings['wbpolls_logoutuser']        = 'no';
		$bpolls_settings['wbpolls_background_color']  = '#4caf50';
		$roles                                        = $wp_roles->get_names();
		foreach ( $roles as $role => $role_name ) {
			$bpolls_settings['wppolls_who_can_vote'][] = $role;
			$bpolls_settings['wppolls_create_poll'][] = $role;
		}
		update_option( 'wbpolls_settings', $bpolls_settings );
	}

	if ( false === get_option( 'wbpolls_notification_settings' ) ) {
		global $wp_roles;
		$bpolls_settings['wppolls_enable_notification'] = 'yes';
		$bpolls_settings['wppolls_admin_notification']  = 'yes';
		$bpolls_settings['wppolls_member_notification'] = 'no';
		$administrators                                 = get_users(
			array(
				'role' => 'administrator',
			)
		);
		foreach ( $administrators as $role_name ) {
			$bpolls_settings['wppolls_admin_user'][] = $role_name->ID;
		}
		update_option( 'wbpolls_notification_settings', $bpolls_settings );
	}

	if ( false === get_option( 'wbpolls_notification_setting_options' ) ) {
		$emai_content['admin'] = array(
			'notification_subject' => '{site_name} : A new Poll needs your approval',
			'notification_content' => '
				Hi {site_admin},

				<br/>
				Greetings! I want to inform you that there is a new Poll {poll_name} that awaits your approval. Would you like to review it at your earliest convenience?

				<br/>
				Thank You.

				<br/>
				<br/>
				{site_name}
			',
		);

		$emai_content['member'] = array(
			'notification_subject' => '{site_name} : Poll approval notification',
			'notification_content' => '
				Hi {publisher_name} ,

				<br/>
				I am pleased to inform you that your Poll, titled {poll_name}, has been approved.
				<br/>

				Thank You.
				<br/>
				<br/>
				{site_name}
			',
		);

		update_option( 'wbpolls_notification_setting_options', $emai_content );
	}

	update_option( 'permalink_structure', '/%postname%/' );
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-buddypress-polls-deactivator.php
 */
function deactivate_buddypress_polls() {
	if ( function_exists( 'is_multisite' ) && is_multisite() ) {
	}
}

register_activation_hook( __FILE__, 'activate_buddypress_polls' );
register_deactivation_hook( __FILE__, 'deactivate_buddypress_polls' );


/**
 * This class responsible for help methods
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-wbpoll-helper.php';


/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-buddypress-polls.php';

require plugin_dir_path( __FILE__ ) . 'edd-license/edd-plugin-license.php';

/**
 * Poll rest api
 */
require_once plugin_dir_path(__FILE__) . 'restapi/v1/pollrestapi.php';

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
function run_buddypress_polls() {
	global $pagenow;
	
	$admin_page = filter_input( INPUT_GET, 'page' ) ? filter_input( INPUT_GET, 'page' ) : 'buddypress-polls';
	if ( ! get_option( 'bpolls_update_3_8_2' ) && ( isset( $admin_page ) && 'buddypress-polls' === $admin_page || 'plugins.php' === $pagenow ) ) {
		$bpolls_settings                           = get_option( 'bpolls_settings' );
		$bpolls_settings['options_limit']          = '5';
		$bpolls_settings['poll_options_result']    = 'yes';
		$bpolls_settings['poll_list_voters']       = 'yes';
		$bpolls_settings['poll_limit_voters']      = '3';
		$bpolls_settings['polls_background_color'] = '#4caf50';
		update_option( 'bpolls_settings', $bpolls_settings );

		update_option( 'bpolls_update_3_8_2', 1 );

	}

	$plugin = new Buddypress_Polls();
	$plugin->run();

}

// add_action( 'bp_include', 'bpolls_plugin_init' );
/**
 * Check plugin requirement on plugins loaded
 * this plugin requires BuddyPress to be installed and active
 */
function bpolls_plugin_init() {
	if ( bp_polls_check_config() ) {
		run_buddypress_polls();
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'bpolls_plugin_links' );
	}
}
bpolls_plugin_init();
/**
 * Function to check configurations.
 */
function bp_polls_check_config() {
	global $bp;
	$check  = array();
	$config = array(
		'blog_status'    => false,
		'network_active' => false,
		'network_status' => true,
	);
	if ( function_exists( 'bp_get_root_blog_id' ) && get_current_blog_id() == bp_get_root_blog_id() ) {
		$config['blog_status'] = true;
	}

	$network_plugins = get_site_option( 'active_sitewide_plugins', array() );

	// No Network plugins.
	if ( class_exists( 'Buddypress' ) && empty( $network_plugins ) ) {

		// Looking for BuddyPress and bp-activity plugin.
		$check[] = $bp->basename;
	}
	$check[] = BPOLLS_PLUGIN_BASENAME;

	// Are they active on the network ?
	$network_active = array_diff( $check, array_keys( $network_plugins ) );

	// If result is 1, your plugin is network activated
	// and not BuddyPress or vice & versa. Config is not ok.
	if ( count( $network_active ) == 1 ) {
		$config['network_status'] = false;
	}

	// We need to know if the plugin is network activated to choose the right
	// notice ( admin or network_admin ) to display the warning message.
	$config['network_active'] = isset( $network_plugins[ BPOLLS_PLUGIN_BASENAME ] );

	// if BuddyPress config is different than bp-activity plugin.
	if ( class_exists( 'Buddypress' ) && ( ! $config['blog_status'] || ! $config['network_status'] ) ) {

		$warnings = array();
		if ( ! bp_core_do_network_admin() && ! $config['blog_status'] ) {
			add_action( 'admin_notices', 'bpolls_same_blog' );
			$warnings[] = __( 'BuddyPress Polls requires to be activated on the blog where BuddyPress is activated.', 'buddypress-polls' );
		}

		if ( bp_core_do_network_admin() && ! $config['network_status'] ) {
			add_action( 'admin_notices', 'bpolls_same_network_config' );
			$warnings[] = __( 'BuddyPress Polls and BuddyPress need to share the same network configuration.', 'buddypress-polls' );
		}

		if ( ! empty( $warnings ) ) :
			return false;
		endif;
		// Display a warning message in network admin or admin.
	}
	return true;
}

/**
 * Bpolls_same_blog
 */
function bpolls_same_blog() {
	echo '<div class="error"><p>'
	. esc_html( __( 'BuddyPress Polls requires to be activated on the blog where BuddyPress is activated.', 'buddypress-polls' ) )
	. '</p></div>';
}

/**
 * Bpolls_same_network_config
 */
function bpolls_same_network_config() {
	echo '<div class="error"><p>'
	. esc_html( __( 'BuddyPress Polls and BuddyPress need to share the same network configuration.', 'buddypress-polls' ) )
	. '</p></div>';
}

/**
 * Function to add plugin links.
 *
 * @param array $links Plugin action links array.
 */
function bpolls_plugin_links( $links ) {
	$bpolls_links = array(
		'<a href="' . admin_url( 'admin.php?page=buddypress-polls' ) . '">' . __( 'Settings', 'buddypress-polls' ) . '</a>',
		'<a href="https://wbcomdesigns.com/contact/" target="_blank">' . __( 'Support', 'buddypress-polls' ) . '</a>',
	);
	return array_merge( $links, $bpolls_links );
}


add_action( 'admin_init', 'buddypress_polls_migration', 20 );

/**
 * Update existing polls user data into polls activity meta.
 *
 * @author wbcomdesigns
 * @since  3.2.1
 */
function buddypress_polls_migration() {
	global $wpdb, $pagenow;
	
	if ( !class_exists( 'Buddypress' ) ) {
		return;
	}

	$buddypress_polls_migration_3_2_1 = get_option( 'buddypress_polls_migration_3_2_1' );
	if ( ( 'plugins.php' === $pagenow || 'update-core.php' === $pagenow ) && 'update' !== $buddypress_polls_migration_3_2_1 ) {
		$polls_activity_results = $wpdb->get_results( "SELECT * from {$wpdb->prefix}bp_activity where type = 'activity_poll' group by id having date_recorded=max(date_recorded) order by date_recorded desc" );
		if ( ! empty( $polls_activity_results ) ) {
			foreach ( $polls_activity_results as  $activity ) {
				$activity_id                = $activity->id;
				$activity_meta              = bp_activity_get_meta( $activity_id, 'bpolls_meta' );
				$usermeta_query             = array();
				$usermeta_query['relation'] = 'AND';
				$usermeta_query[]           = array(
					'key'     => 'bpoll_user_vote',
					'value'   => '.*i:' . $activity_id . ';a:[0-9]+:*',
					'compare' => 'REGEXP',
				);
				$args                       = array(
					'meta_query' => $usermeta_query,
				);
				$users                      = new WP_User_Query( $args );
				$users_found                = $users->get_results();
				foreach ( $users_found as $user ) {
					$user_id         = $user->ID;
					$user_polls_data = get_user_meta( $user_id, 'bpoll_user_vote', true );
					if ( isset( $user_polls_data[ $activity_id ] ) && ! empty( $user_polls_data[ $activity_id ] ) ) {
						$user_activity_poll_data = isset( $user_polls_data[ $activity_id ] ) ? $user_polls_data[ $activity_id ] : array();
						foreach ( $activity_meta['poll_option'] as $key => $value ) {
							if ( in_array( $key, $user_activity_poll_data, true ) ) {
								$polls_existing_useid                          = isset( $activity_meta['poll_optn_user_votes'][ $key ] ) ? $activity_meta['poll_optn_user_votes'][ $key ] : array();
								$activity_meta['poll_optn_user_votes'][ $key ] = array_unique( array_merge( $polls_existing_useid, array( $user_id ) ) );
							}
						}
						/* saved User id in activity meta */
						$existing_useid              = isset( $activity_meta['poll_users'] ) ? $activity_meta['poll_users'] : array();
						$activity_meta['poll_users'] = array_unique( array_merge( $existing_useid, array( $user_id ) ) );

						bp_activity_update_meta( $activity_id, 'bpolls_meta', $activity_meta );
					}
				}
			}
			update_option( 'buddypress_polls_migration_3_2_1', 'update' );
		}
	}

}
add_action( 'activated_plugin', 'buddypress_polls_activation_redirect_settings' );

/**
 * Redirect to plugin settings page after activated
 *
 * @param plugin $plugin plugin.
 */
function buddypress_polls_activation_redirect_settings( $plugin ) {
	$plugins = filter_input( INPUT_GET, 'plugin' ) ? filter_input( INPUT_GET, 'plugin' ) : '';
	if ( ! isset( $plugins ) ) {
		return;
	}
	if ( plugin_basename( __FILE__ ) === $plugin ) {
		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'activate' && isset( $_REQUEST['plugin'] ) && $_REQUEST['plugin'] == $plugin ) { //phpcs:ignore
			wp_safe_redirect( admin_url( 'admin.php?page=buddypress-polls' ) );
			exit;
		}
	}
}

/**
 * Display Polls Activity in bbpress reply using shortcode.
 *
 * @param content $content bbPress reply content.
 */
function bpolls_bbp_get_reply_content( $content ) {

	return do_shortcode( $content );
}
add_filter( 'bbp_get_reply_content', 'bpolls_bbp_get_reply_content' );

/**
 * Display Polls Activity in bbpress forum using shortcode.
 *
 * @param content $content bbPress forum content.
 */
function bpolls_bbp_get_forum_content( $content ) {

	return do_shortcode( $content );
}

/**
 * Display Polls Activity in bbpress topic using shortcode.
 *
 * @param content $content bbPress topic content.
 */
function bpolls_bbp_get_topic_content( $content ) {

	return do_shortcode( $content );
}
if ( function_exists( 'buddypress' ) && isset( buddypress()->buddyboss ) ) {
	add_filter( 'bbp_get_forum_content', 'bpolls_bbp_get_forum_content' );
	add_filter( 'bbp_get_topic_content', 'bpolls_bbp_get_topic_content' );
}

/**
 * Display Polls Quick tag in bbpress topics and reply editor
 *
 * @ Since 4.0.0
 */
function bpolls_add_quicktags() {
	if ( wp_script_is( 'quicktags' ) && ( function_exists( 'bbp_is_single_forum' ) && bbp_is_single_forum() ) ) {
		?>
	<script type="text/javascript">
		QTags.addButton( 'bp_polls', 'Polls', '[bp_polls activity_id="add your polls activity id"]', '', 'h', 'Add your polls activity', 201 );
	</script>
		<?php
	}
}
add_action( 'wp_footer', 'bpolls_add_quicktags', 99999 );



/**
 *  Check if buddypress activate.
 */
function bpolls_add_page_or_data_buddypress()
{
	
	global $pagenow;
	$admin_page = filter_input(INPUT_GET, 'page') ? filter_input(INPUT_GET, 'page') : 'buddypress-polls';
	if (!get_option('bpolls_update_4_3_0') && (isset($admin_page) && 'buddypress-polls' === $admin_page || !get_option('bpolls_update_4_3_0') && 'plugins.php' === $pagenow)) {
		WBPollHelper::install_table();
		
		/****** update wbpoll settings *****/

		if (false === get_option('bpolls_settings') || empty(get_option('bpolls_settings'))) {
			global $wp_roles;
			$bpolls_settings['limit_poll_activity']    = 'no';
			$bpolls_settings['options_limit']          = '5';
			$bpolls_settings['poll_options_result']    = 'yes';
			$bpolls_settings['poll_list_voters']       = 'yes';
			$bpolls_settings['poll_limit_voters']      = '3';
			$bpolls_settings['polls_background_color'] = '#4caf50';
			$bpolls_settings['multiselect']    = 'no';
			$bpolls_settings['user_additional_option']    = 'no';
			$bpolls_settings['hide_results']    = 'no';
			$bpolls_settings['close_date']    = 'no';
			$bpolls_settings['enable_image']    = 'no';
			$bpolls_settings['poll_revoting']    = 'no';
			$roles                                     = $wp_roles->get_names();
			foreach ($roles as $role => $role_name) {
				$bpolls_settings['poll_user_role'][] = $role;
			}
			update_option('bpolls_settings', $bpolls_settings);
		}
	
		/**
		 * create a page for frontend poll
		 */
		$page_title = 'Poll Dashboard';
		$poll_dashboard_page = bpolls_get_page_by_title( $page_title );
		if ( empty($poll_dashboard_page) && (empty($wbpolls_settings) || !isset($wbpolls_settings['poll_dashboard_page'])) ) {
			$dashboard_page_id = wp_insert_post(
				array(
					'post_title'     => $page_title,
					'post_status'    => 'publish',
					'post_type'      => 'page',
					'comment_status' => 'closed',
				)
			);
		}
		/**
		 * create a page for frontend poll
		 */
		$page_title = 'Create Poll';
		$create_poll_page = bpolls_get_page_by_title( $page_title );	
		if ( empty($create_poll_page) && (empty($wbpolls_settings) || !isset($wbpolls_settings['create_poll_page'])) ) {
			$create_page_id = wp_insert_post(
				array(
					'post_title'     => $page_title,
					'post_status'    => 'publish',
					'post_type'      => 'page',
					'comment_status' => 'closed',
				)
			);
		}
	
		if (false === get_option('wbpolls_settings')) {
			global $wp_roles;
			$bpolls_settings['poll_dashboard_page']    = $dashboard_page_id;
			$bpolls_settings['create_poll_page']    = $create_page_id;
			$bpolls_settings['wbpolls_user_add_extra_op']    = 'no';
			$bpolls_settings['wbpolls_submit_status']       = 'publish';
			$bpolls_settings['wppolls_show_result']      = 'yes';
			$bpolls_settings['wbpolls_logoutuser']      = 'no';
			$bpolls_settings['wbpolls_background_color'] = '#4caf50';
			$roles  = $wp_roles->get_names();
			foreach ($roles as $role => $role_name) {
				$bpolls_settings['wppolls_who_can_vote'][] = $role;
				$bpolls_settings['wppolls_create_poll'][] = $role;
			}
			update_option('wbpolls_settings', $bpolls_settings);
		}
	
		if (false === get_option('wbpolls_notification_settings')) {
			global $wp_roles;
			$bpolls_settings['wppolls_enable_notification']    = 'yes';
			$bpolls_settings['wppolls_admin_notification']    = 'yes';
			$bpolls_settings['wppolls_member_notification']    = 'no';
			$administrators = get_users(array(
				'role' => 'administrator',
			));
			foreach ($administrators as $role_name) {
				$bpolls_settings['wppolls_admin_user'][] = $role_name->ID;
			}
			update_option('wbpolls_notification_settings', $bpolls_settings);
		}
	
		if ( false === get_option( 'wbpolls_notification_setting_options' ) ) {
			$emai_content['admin'] = array(
				'notification_subject' => '{site_name} : A new Poll needs your approval', 
				'notification_content' => '
					Hi {site_admin},

					<br/>
					Greetings! I want to inform you that there is a new Poll {poll_name} that awaits your approval. Would you like to review it at your earliest convenience?
					<br/>
					Thank You.
					<br/>
					{site_name}
				',
			);
	
			$emai_content['member'] = array(
				'notification_subject' => '{site_name} : Poll approval notification',
				'notification_content' => '
					Hi {publisher_name} ,

					<br/>
					I am pleased to inform you that your Poll, titled {poll_name}, has been approved.
					<br/>
					Thank You.
					<br/>
					{site_name}
				',
			);
	
			update_option( 'wbpolls_notification_setting_options', $emai_content );
		}

		update_option('permalink_structure', '/%postname%/');

		update_option('bpolls_update_4_3_0', 1);
	}
}
add_action('admin_init', 'bpolls_add_page_or_data_buddypress');



function bpolls_get_page_by_title( $page_title, $output = OBJECT, $post_type = 'page' ) {	
	global $wpdb;

	if ( is_array( $post_type ) ) {
		$post_type           = esc_sql( $post_type );
		$post_type_in_string = "'" . implode( "','", $post_type ) . "'";
		$sql                 = $wpdb->prepare(
			"
			SELECT ID
			FROM $wpdb->posts
			WHERE post_title = %s
			AND post_type IN ($post_type_in_string)
		",
			$page_title
		);
	} else {
		$sql = $wpdb->prepare(
			"
			SELECT ID
			FROM $wpdb->posts
			WHERE post_title = %s
			AND post_type = %s
		",
			$page_title,
			$post_type
		);
	}

	$page = $wpdb->get_var( $sql );

	if ( $page ) {
		return get_post( $page, $output );
	}

	return null;
}


/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package BuddyPress Polls
 */
function buddypress_polls_navigation() {

	global $wp_query;

	// Don't print empty markup if there's only one page.
	if ( $wp_query->max_num_pages > 1 ) {
		// Make sure the nav element has an aria-label attribute: fallback to the screen reader text.
		if ( ! empty( $args['screen_reader_text'] ) && empty( $args['aria_label'] ) ) {
			$args['aria_label'] = $args['screen_reader_text'];
		}

		$args = wp_parse_args(
			array(
				'mid_size'           => 1,
				'prev_text'          => esc_html__( 'Previous', 'buddypress-polls' ),
				'next_text'          => esc_html__( 'Next', 'buddypress-polls' ),
				'screen_reader_text' => esc_html__( 'Posts navigation', 'buddypress-polls' ),
				'aria_label'         => esc_html__( 'Posts', 'buddypress-polls' ),
			)
		);

		// Make sure we get a string back. Plain is the next best thing.
		if ( isset( $args['type'] ) && 'array' == $args['type'] ) {
			$args['type'] = 'plain';
		}

		// Set up paginated links.
		$links            = paginate_links( $args );

		if ( $links ) {
			echo '<nav class="navigation posts-navigation wbpoll-archive-navigation" role="navigation">';
			echo '<h2 class="screen-reader-text">Posts navigation</h2>';
			echo '<div class="nav-links">' . esc_url( $links ) . '</div>';
			echo '</nav>';
		}
		
	}
}


if ( ! function_exists( 'buddypress_polls_meta' ) ) {
	/**
	 * Prints HTML with meta information.
	 */
	function buddypress_polls_meta() {
		

		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
		}

		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() )
		);

		$posted_on = sprintf(
			/* translators: %s: */
			esc_html_x( '%s', 'post date', 'buddypress-polls' ),
			'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
		);
	

		$avatar = '<i class="fa fa-user-circle"></i>';
		if ( function_exists( 'get_avatar' ) ) {
			$avatar = sprintf(
				'<a href="%1$s" rel="bookmark">%2$s</a>',
				esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
				get_avatar( get_the_author_meta( 'email' ), 55 )
			);
		}

		$byline = sprintf(
			esc_html_x( '%s', 'post author', 'buddypress-polls' ),
			'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
		);

		echo '<span class="byline">' . $avatar . $byline . '<span class="posted-on">' . $posted_on . '</span></span>'; //phpcs:ignore

	}
}



/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function buddypress_polls_widgets_init() {	

	register_sidebar(
		array(
			'name'          => esc_html__( 'BuddyPress Polls Sidebar', 'buddypress-polls' ),
			'id'            => 'buddypress-poll-right',
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h4 class="widget-title"><span>',
			'after_title'   => '</span></h4>',
		)
	);
}

add_action( 'widgets_init', 'buddypress_polls_widgets_init' );