<?php

if (!defined('TERMS_PAGE_ID')):
	define('TERMS_PAGE_ID', 1124);
endif;


/**
 * @package BuddyBoss Child
 * The parent theme functions are located at /buddyboss-theme/inc/theme/functions.php
 * Add your own functions at the bottom of this file.
 */

/****************************** THEME SETUP ******************************/

/**
 * Sets up theme for translation
 *
 * @since BuddyBoss Child 1.0.0
 */
function buddyboss_theme_child_languages()
{
	/**
	 * Makes child theme available for translation.
	 * Translations can be added into the /languages/ directory.
	 */

	// Translate text from the PARENT theme.
	load_theme_textdomain('buddyboss-theme', get_stylesheet_directory() . '/languages');

	// Translate text from the CHILD theme only.
	// Change 'buddyboss-theme' instances in all child theme files to 'buddyboss-theme-child'.
	// load_theme_textdomain( 'buddyboss-theme-child', get_stylesheet_directory() . '/languages' );

}
add_action('after_setup_theme', 'buddyboss_theme_child_languages');

/**
 * Enqueues scripts and styles for child theme front-end.
 *
 * @since Boss Child Theme  1.0.0
 */
function buddyboss_theme_child_scripts_styles()
{
	/**
	 * Scripts and Styles loaded by the parent theme can be unloaded if needed
	 * using wp_deregister_script or wp_deregister_style.
	 *
	 * See the WordPress Codex for more information about those functions:
	 * http://codex.wordpress.org/Function_Reference/wp_deregister_script
	 * http://codex.wordpress.org/Function_Reference/wp_deregister_style
	 **/

	$version = strtotime("today");

	// Styles
	wp_enqueue_style('buddyboss-child-css', get_stylesheet_directory_uri() . '/assets/css/custom.css', '', $version);

	// Javascript
	wp_enqueue_script('buddyboss-child-js', get_stylesheet_directory_uri() . '/assets/js/custom.js', '', $version);
	wp_dequeue_script('buddyboss-theme-learndash-js');
	wp_deregister_script('buddyboss-theme-learndash-js');
	wp_enqueue_script('buddyboss-child-learndash-js', get_stylesheet_directory_uri() . '/assets/js/learndash.min.js', array(), array(), true);

	if (is_page('leaderboard') && current_user_can('activate_plugins')) {
		// Enqueue the script
		wp_enqueue_script('community-leaderboard-script', get_stylesheet_directory_uri() . '/assets/js/community-leaderboard.js', '', '1.0', true);
	}


	wp_localize_script(
		'buddyboss-child-js',
		'bb_vars',
		array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'security' => wp_create_nonce('file_upload'),
		)
	);



	global $wp_query;
	$page = get_query_var("pagename");
	$id = get_query_var("page");

	if (($page == 'view') && (!empty($id))):

		global $wpdb;
		$tblName = $wpdb->prefix . 'bp_messages_meta';
		$threadData = $wpdb->get_var("SELECT meta_value FROM $tblName WHERE meta_key='group_message_thread_id_$id' ORDER BY id DESC LIMIT 1");
		if (!empty($threadData)):

			$members = groups_get_group_members(
				array(
					'exclude_admins_mods' => false,
					'group_id' => $threadData,
					'per_page' => 20,
					'page' => 1,
				)
			);
			$results[] = array(
				'ID' => 'all',
				'user_nicename' => 'All',
				'name' => 'All',
				'user_id' => 0,
				'image' => bb_get_buddyboss_profile_avatar(),
			);

			if (!empty($members['members'])) {
				foreach ($members['members'] as $user) {
					$result = new stdClass();
					$result->ID = bp_activity_get_user_mentionname($user->ID);
					$result->user_nicename = $user->user_nicename;
					$result->image = bp_core_fetch_avatar(
						array(
							'html' => false,
							'item_id' => $user->ID,
						)
					);
					if (!empty($user->display_name) && !bp_disable_profile_sync()) {
						$result->name = bp_core_get_user_displayname($user->ID);
					} else {
						$result->name = bp_core_get_user_displayname($user->ID);
					}
					$result->user_id = $user->ID;

					$results[] = $result;
				}

				wp_localize_script(
					'bp-mentions',
					'BP_Suggestions',
					array(
						'members' => $results
					)
				);
			}

		endif;



	endif;
}
add_action('wp_enqueue_scripts', 'buddyboss_theme_child_scripts_styles', 9999);


/****************************** CUSTOM FUNCTIONS ******************************/
//Includes the my groups widget file
include_once 'widgets/user_groups.php';
include_once 'widgets/user_coach_badge.php';
include_once 'widgets/user_rank.php';
include_once 'widgets/user_points_gamipress.php';
include_once 'achievements_update.php';
include_once 'inc/video_albums.php';




// Add your own custom functions here
require_once get_stylesheet_directory() . '/inc/groups-in-announcements.php';
require_once get_stylesheet_directory() . '/inc/term-meta.php';
require_once get_stylesheet_directory() . '/inc/ajax-helper.php';
//require_once get_stylesheet_directory() . '/shortcodes/index.php';

add_filter('buddyboss_theme_redux_is_theme', '__return_true', 999);
add_filter('bp_activity_do_mentions', '__return_true');

/**
 * Get path from template directory to current file.
 * 
 * 
 * @param string $file_path Current file path
 * 
 * @uses get_template() Get active template directory name.
 * 
 * @return string
 */
function buddyboss_theme_dir_to_current_file_path($file_path)
{
	// Format current file path with only right slash.
	$file_path = trailingslashit($file_path);
	$file_path = str_replace('\\', '/', $file_path);
	$file_path = str_replace('//', '/', $file_path);
	$chunks = explode('/', $file_path);
	if (!is_array($chunks)) {
		$chunks = array();
	}
	// Reverse array for child to parent or current file to template directory.
	$chunks = array_reverse($chunks);
	$template = get_template();
	$tmp_file = array();
	foreach ($chunks as $path) {
		if (empty($path)) {
			continue;
		}
		if ($path == $template) {
			break;
		}
		// Set all directory name from current file to template directory.
		$tmp_file[] = $path;
	}
	// Reverse array for parent to child or template directory to file directory.
	$tmp_file = array_reverse($tmp_file);
	$tmp_file = implode('/', $tmp_file);
	return $tmp_file;
}

/**
 * Filter Redux URL
 * 
 * @param string $url Redux url.
 * 
 * @uses buddyboss_theme_dir_to_current_file_path() Get relative path.
 * 
 * @return string
 */
function buddyboss_theme_redux_url($url)
{
	/**
	 * When some parts of current file path and template directory path are match from the beginning.
	 * 
	 * Example
	 * current_path = /bitnami/wordpress/wp-content/
	 * tmpdir_path  = /bitnami/wordpress/wp-content/themes/buddyboss-theme/inc/admin/framework/ReduxCore/
	 */
	if (strpos(Redux_Helpers::cleanFilePath(__FILE__), Redux_Helpers::cleanFilePath(get_template_directory())) !== false) {
		return $url;
	} else if (strpos(Redux_Helpers::cleanFilePath(__FILE__), Redux_Helpers::cleanFilePath(get_stylesheet_directory())) !== false) {
		return $url;
	}
	/**
	 * When some parts of current file path and template directory path are not match from the beginning.
	 * 
	 * Example
	 * current_path = /opt/bitnami/wordpress/wp-content/
	 * tmpdir_path  = /bitnami/wordpress/wp-content/themes/buddyboss-theme/inc/admin/framework/ReduxCore/
	 */
	// Get template url.
	$tem_dir = trailingslashit(get_template_directory_uri());
	// Get template to current file directory path.
	$file_dir = buddyboss_theme_dir_to_current_file_path($url);
	// Set url for ReduxCore directory
	$redux_url = trailingslashit($tem_dir . $file_dir);
	// Check valid url
	if (filter_var($redux_url, FILTER_VALIDATE_URL)) {
		return $redux_url;
	}
	return $url;
}
add_filter('redux/_url', 'buddyboss_theme_redux_url');

add_filter('style_loader_src', 'bb_fix_theme_option_for_custom_wp_installation');
add_filter('script_loader_src', 'bb_fix_theme_option_for_custom_wp_installation');
function bb_fix_theme_option_for_custom_wp_installation($url)
{
	if (is_admin()) {
		$url = str_replace('plugins/bitnami/wordpress/wp-content/themes/buddyboss-theme/', 'themes/buddyboss-theme/', $url);
	}
	return $url;
}

function bb_get_thread_by_id($thread, $id)
{
	foreach ($thread as $element):
		if ($element->thread_id == $id):
			return $element;
		endif;
	endforeach;
}


//This Generate Channel
function bb_generate_group_thread()
{
	global $wpdb;
	$tblMsg = $wpdb->prefix . 'bp_messages_messages';
	$tblMeta = $wpdb->prefix . 'bp_messages_meta';
	$groupId = bp_get_current_group_id();
	$getThreadId = $wpdb->get_var("SELECT msg.thread_id FROM $tblMsg as msg JOIN $tblMeta as meta ON msg.id=meta.message_id WHERE meta.meta_key='group_id' AND meta.meta_value=$groupId ORDER BY meta.id DESC LIMIT 1");


	if ((empty($getThreadId) || ($getThreadId == '') || ($getThreadId == 0)) && ($groupId != 0 || $groupId != '')):

		// Fetch all the group members.
		$members = BP_Groups_Member::get_group_member_ids((int) $groupId);

		// if (in_array(bp_loggedin_user_id(), $members, true)) {
		//         $members = array_values(array_diff($members, array(bp_loggedin_user_id())));
		// }



		if (!$getThreadId):

			$gAdmins = groups_get_group_admins($groupId);
			$senderID = (is_array($gAdmins)) ? $gAdmins[0]->user_id : bp_loggedin_user_id();
			$username = bp_get_loggedin_user_username();
			$content = '<p><span class="atwho-inserted" data-atwho-at-query="@' . $username . '" contenteditable="false">@' . $username . '</span>  - Welcome is test message On Boarded</p>';

			$obj = array(
				'recipients' => $members,
				'subject' => wp_trim_words($content, messages_get_default_subject_length()),
				'content' => $content,
				'error_type' => 'wp_error',
				'append_thread' => false,
			);
			$send = bp_groups_messages_new_message($obj);
			if (!is_wp_error($send) && !empty($send)):
				global $wpdb;
				$tblMsgMeta = $wpdb->prefix . 'bp_messages_meta';
				$tblMsg = $wpdb->prefix . "bp_messages_messages";
				$msgData = $wpdb->get_row("SELECT id, thread_id FROM $tblMsg ORDER BY id DESC LIMIT 1");
				groups_update_groupmeta($groupId, 'group_message_thread', $msgData->thread_id);

				$wpdb->insert($tblMsgMeta, ["message_id" => $msgData->id, "meta_key" => "group_id", "meta_value" => $groupId]);
				$wpdb->insert($tblMsgMeta, ["message_id" => $msgData->id, "meta_key" => "group_message_users", "meta_value" => "all"]);
				$wpdb->insert($tblMsgMeta, ["message_id" => $msgData->id, "meta_key" => "group_message_type", "meta_value" => "open"]);
				$wpdb->insert($tblMsgMeta, ["message_id" => $msgData->id, "meta_key" => "group_message_thread_type", "meta_value" => "new"]);
				$wpdb->insert($tblMsgMeta, ["message_id" => $msgData->id, "meta_key" => "group_message_fresh", "meta_value" => "yes"]);
				$wpdb->insert($tblMsgMeta, ["message_id" => $msgData->id, "meta_key" => "group_message_thread_id_" . $msgData->thread_id, "meta_value" => $groupId]);
				$wpdb->insert($tblMsgMeta, ["message_id" => $msgData->id, "meta_key" => "message_from", "meta_value" => "group"]);
				$wpdb->insert($tblMsgMeta, ["message_id" => $msgData->id, "meta_key" => "message_users_ids", "meta_value" => $members[0]]);
				$wpdb->insert($tblMsgMeta, ["message_id" => $msgData->id, "meta_key" => "group_message_thread_id", "meta_value" => $msgData->thread_id]);
				$wpdb->insert($tblMsgMeta, ["message_id" => $msgData->id, "meta_key" => "thread_action", "meta_value" => "groups_get_group_members_send_message"]);

			endif;
		endif;
	endif;
}
add_action("groups_created_group", "bb_generate_group_thread");

//For Mention Suggestions
function get_thread_data_callback()
{
	// Check if the 'thread_id' parameter is set in the AJAX request
	if (isset($_POST['thread_id'])) {
		$thread_id = $_POST['thread_id'];
		$current_user = $_POST['current_user_id'];

		// Perform any data retrieval or processing here based on the thread ID
		// Example: Fetch thread data from the database
		$group_id = get_group_id_for_thread($thread_id);
		$members = get_thread_mentions($group_id, $current_user);

		// Send a JSON response back to the client
		wp_send_json(json_encode($members));
	}
	// You can add an error response here if needed
}
add_action('wp_ajax_get_thread_data', 'get_thread_data_callback'); // For logged-in users
add_action('wp_ajax_nopriv_get_thread_data', 'get_thread_data_callback'); // For non-logged-in users

function get_group_id_for_thread($thread_id)
{
	global $wpdb;

	// Prepare the subquery to get the latest message_id for the given thread_id
	$subquery = $wpdb->prepare(
		"SELECT `message_id`
            FROM {$wpdb->prefix}bp_messages_meta
            WHERE meta_key = 'group_message_thread_id' AND meta_value = %d
            ORDER BY `message_id` DESC
            LIMIT 1",
		$thread_id
	);

	// Use the subquery in the main query to retrieve the 'group_id'
	$query = $wpdb->prepare(
		"SELECT meta_value
            FROM {$wpdb->prefix}bp_messages_meta
            WHERE meta_key = 'group_id'
              AND message_id = ({$subquery})"
	);

	// Execute the query
	$group_id = $wpdb->get_var($query);

	return $group_id;
}


function get_thread_mentions($group_id, $current_user)
{
	// Get all members of the group
	$group_members = groups_get_group_members(
		array(
			'group_id' => $group_id,
			'exclude_admins_mods' => false,
			// Set to true to exclude group admins and moderators
			'per_page' => 999999,
			// Use -1 to retrieve all members, or set to the desired number of members per page
			'page' => 1,
			// Page number if you want to paginate results
			'exclude' => array($current_user)
		)
	);
	return $group_members;
	if (!empty($group_members['members'])) {
		return $group_members;
	} else {
		return '';
	}
}

//Notification for tagged users
remove_action('messages_message_sent', 'bp_messages_message_sent_add_notification', 10);
function bp_messages_message_sent_add_notification_restructure($message)
{
	if (!empty($message->recipients)) {
		$message_from = bp_messages_get_meta($message->id, 'message_from', true); // group.
		$action = 'new_message';
		error_log($message_from);
		if (!bb_enabled_legacy_email_preference()) {
			$action = 'bb_messages_new';
			// if ('group' === $message_from) {
			//         $action = 'bb_groups_new_message';
			// }
		}
		// Disabled the notification for user who archived this thread.
		foreach ((array) $message->recipients as $r_key => $recipient) {
			if (isset($recipient->is_hidden) && $recipient->is_hidden) {
				unset($message->recipients[$r_key]);
			}
		}
		if (
			function_exists('bb_notifications_background_enabled') &&
			true === bb_notifications_background_enabled() &&
			count($message->recipients) > 20
		) {
			global $bb_notifications_background_updater;
			$recipients = (array) $message->recipients;
			$user_ids = wp_list_pluck($recipients, 'user_id');
			$bb_notifications_background_updater->data(
				array(
					array(
						'callback' => 'bb_add_background_notifications',
						'args' => array(
							$user_ids,
							$message->id,
							$message->sender_id,
							buddypress()->messages->id,
							$action,
							bp_core_current_time(),
							true,
						),
					),
				)
			);
			$bb_notifications_background_updater->save()->dispatch();
		} else {
			$mentions = bp_activity_find_mentions($message->message);
			$mentionsUsers = [];
			if (is_array($mentions)):
				$mentionsUsers = array_keys($mentions);
			endif;
			foreach ((array) $message->recipients as $recipient) {
				if (is_array($mentionsUsers) && count($mentionsUsers) > 0 && (in_array($recipient->user_id, $mentionsUsers))):

					bp_notifications_add_notification(
						array(
							'user_id' => $recipient->user_id,
							'item_id' => $message->id,
							'secondary_item_id' => $message->sender_id,
							'component_name' => buddypress()->messages->id,
							'component_action' => $action,
							'date_notified' => bp_core_current_time(),
							'is_new' => 1,
						)
					);
				elseif (strpos($message->message, "@all") !== false || empty($mentionsUsers)):
					bp_notifications_add_notification(
						array(
							'user_id' => $recipient->user_id,
							'item_id' => $message->id,
							'secondary_item_id' => $message->sender_id,
							'component_name' => buddypress()->messages->id,
							'component_action' => $action,
							'date_notified' => bp_core_current_time(),
							'is_new' => 1,
						)
					);
				endif;
			}
		}
	}
}
add_action('messages_message_sent', 'bp_messages_message_sent_add_notification_restructure', 10);

function prevent_facebook_links_in_activity_update($content)
{
	if (bb_check_fb_content($content)):
		wp_send_json_error(
			array(
				'message' => __('Facebook Content not allowed.', 'buddyboss'),
			)
		);
		die;
	endif;

	return $content;
}

add_filter('bp_activity_new_update_content', 'prevent_facebook_links_in_activity_update', 10, 1);

//function to show forum photos in group photos
function custom_bp_has_media_modify_args($r)
{
	// Check if BuddyPress Groups and Forums are active.
	if (bp_is_active('groups') && bp_is_active('forums')) {
		// Check if you're on a group page and not in a specific forum action.
		if (bp_is_group() && (!isset($_GET['action']) || 'bp_search_ajax' !== $_GET['action'])) {
			$r['privacy'] = false;
			$r['scope'] = 'grouponly';
		}
	}

	return $r;
}

add_filter('bp_before_has_media_parse_args', 'custom_bp_has_media_modify_args');



function add_group_admin_role()
{
	$group_id = bp_get_group_id();
	$group_admins = groups_get_group_admins($group_id);

	$admin_id = $group_admins[0]->user_id;
	// $u = new WP_User( $admin_id );
	// // Add role
	// $u->add_role( 'editor' );
}
//add_action('bp_init', 'add_group_admin_role');

/**
 * Popup in footer
 */
function bb_render_terms_popup()
{
	if (is_user_logged_in() && (get_user_meta(bp_loggedin_user_id(), 'terms_accepted', true) !== 'yes')):
		require_once get_stylesheet_directory() . '/inc/terms-popup.php';
	endif;
}
add_action('wp_footer', 'bb_render_terms_popup');


function bb_accept_tnc_callback()
{
	update_user_meta(bp_loggedin_user_id(), 'terms_accepted', 'yes');
	wp_die('sucess');
}

add_action('wp_ajax_accept_tnc', 'bb_accept_tnc_callback');
add_action('wp_ajax_nopriv_accept_tnc', 'bb_accept_tnc_callback');



function bb_tnc_update_callback($post_ID, $post_after, $post_before)
{
	if ($post_ID == TERMS_PAGE_ID):
		global $wpdb;
		$tblName = $wpdb->prefix . "usermeta";
		$wpdb->query("DELETE FROM $tblName WHERE meta_key='terms_accepted'");
	endif;
}

add_action('post_updated', 'bb_tnc_update_callback', 10, 3);

function filtering_activity_default($query, $object)
{
	if ($object == 'media'):
		parse_str(urldecode($query), $queryArgs);
		$queryArgs['scope'] = 'all';
		$queryArgs['rj_atom_media'] = 'all';
		$query = http_build_query($queryArgs);
		return $query;
	endif;
	return $query;
}
add_filter('bp_ajax_querystring', 'filtering_activity_default', 9999, 2);

function bb_announcements_change_posts_order($query)
{
	if (is_post_type_archive('announcements') && !is_admin() && !isset($_GET['post_type']) && ($query->get('post_type') == "announcements")):
		$orderBy = (isset($_GET['view']) && !empty($_GET['view'])) ? $_GET['view'] : 'latest';
		if ($orderBy == "title"):
			$query->set('orderby', 'post_title');
			$query->set('order', 'ASC');
		elseif ($orderBy == "latest"):
			$query->set('meta_query', array(
				array(
					'key' => 'bb_announcement_date',
					'type' => 'NUMERIC'
				)
			)
			);

			$query->set('meta_key', 'bb_announcement_date');
			$query->set('orderby', 'meta_value_num');
			$query->set('order', 'DESC');
		elseif ($orderBy == "oldest"):
			$query->set('meta_query', array(
				array(
					'key' => 'bb_announcement_date',
					'type' => 'NUMERIC'
				)
			)
			);
			$query->set('meta_key', 'bb_announcement_date');
			$query->set('orderby', 'meta_value_num');
			$query->set('order', 'ASC');
		endif;
	endif;
	return $query;
}
;
add_action('pre_get_posts', 'bb_announcements_change_posts_order', 999);



function bb_read_announcement_callback()
{
	$aid = sanitize_text_field($_REQUEST['pid']);
	$uid = sanitize_text_field($_REQUEST['uid']);
	update_user_meta($uid, 'bb_read_announcement_id', $aid);
	$data = [
		'view_announcement' => true,
		'aid' => $aid,
		'uid' => $uid,
		'aurl' => get_permalink($aid),
	];
	wp_send_json($data);
	wp_die();
}
add_action("wp_ajax_read_announcement", "bb_read_announcement_callback");
add_action("wp_ajax_nopriv_read_announcement", "bb_read_announcement_callback");


function register_profile_image_field_types($fields)
{
	require_once "inc/class-field-type-profile-image.php";
	$fields = array_merge($fields, ['profileimage' => 'BPXProfileCFTR\Field_Types\Field_Type_Profile_Image',]);
	return $fields;
}
add_filter('bp_xprofile_get_field_types', 'register_profile_image_field_types');


function rename_group_tab_discussion()
{

	if (!bp_is_group()) {
		return;
	}

	buddypress()->groups->nav->edit_nav(array('name' => __('Key Topics', 'discussions')), 'forum', bp_current_item());
}
add_action('bp_actions', 'rename_group_tab_discussion');

/**
 * Manage Xprofile Data For Profile Image
 */
add_action('init', function () {

	if (is_user_logged_in()):

		$userID = bp_loggedin_user_id();
		$getUserData = get_user_meta($userID, 'profile-image-setup', true);

		if ($getUserData !== 'complete'):
			global $wpdb;
			$tblXPD = $wpdb->prefix . 'bp_xprofile_data';
			$getData = $wpdb->get_row("SELECT value FROM $tblXPD WHERE user_id=$userID AND field_id=2019");
			if (is_object($getData) && isset($getData->value) && !empty($getData->value)):
				$profileImageURL = get_attached_file($getData->value);
				$filePath = $profileImageURL;
				$target_dir = wp_get_upload_dir()['basedir'] . '/avatars/' . $userID;

				if ($filePath):
					if (!is_dir($target_dir)):
						wp_mkdir_p($target_dir);
						chmod($target_dir, 0777);
					endif;

					$ext = pathinfo($filePath, PATHINFO_EXTENSION);
					$baseNameFull = md5($userID) . "-bpfull." . $ext;
					$baseNameThumb = md5($userID) . "-bpthumb." . $ext;
					$targetFullName = $target_dir . '/' . $baseNameFull;
					$targetThumb = $target_dir . '/' . $baseNameThumb;

					if ($filePath && $targetFullName):
						copy($filePath, $targetFullName);
					endif;
					if ($filePath && $targetThumb):
						copy($filePath, $targetThumb);
					endif;
					update_user_meta($userID, 'profile-image-setup', 'complete');
				endif;
			endif;
		endif;
	endif;
});

function remove_group_tabs()
{
	if (!bp_is_group()) {
		return;
	}

	$userId = bp_loggedin_user_id();
	$groupId = bp_get_current_group_id();
	$isGroupAdmin = groups_is_user_admin($userId, $groupId);
	$isSiteAdmin = is_super_admin($userId);

	if ($isSiteAdmin || $isGroupAdmin):
		//User is whether Site Admin or Group Admin
	else:
		bp_core_remove_subnav_item(bp_current_item(), 'invite', 'groups');
		bp_core_remove_subnav_item(bp_current_item(), 'members', 'groups');
	endif;
}
add_action('bp_setup_nav', 'remove_group_tabs');

function bb_manage_dynamic_css_js()
{
	if (!bp_is_group()) {
		return;
	}

	$userId = bp_loggedin_user_id();
	$groupId = bp_get_current_group_id();
	$isGroupAdmin = groups_is_user_admin($userId, $groupId);
	$isSiteAdmin = is_super_admin($userId);
	if ($isSiteAdmin || $isGroupAdmin):
		//User is whether Site Admin or Group Admin
	else:
		?>
		<style>
			#bbpress-forums .bbp-forum-buttons-wrap .bbp_before_forum_new_post a.button.full.btn-new-topic {
				display: none !important;
			}
		</style>
		<?php
	endif;
}
add_action('wp_head', 'bb_manage_dynamic_css_js');


//member-stats fn starts
function get_buddypress_member_counts($days = 7)
{
	// Make sure BuddyPress is active
	if (!function_exists('bp_is_active') || !bp_is_active('members')) {
		return false;
	}

	global $wpdb;

	// Get the current date and the date from X days ago
	$current_date = current_time('mysql');
	$days_ago = date('Y-m-d H:i:s', strtotime("-$days days"));

	// Get the total BuddyPress members count
	$total_members_count = bp_core_get_active_member_count();

	// Get the active BuddyPress members count within the selected timeframe
	$active_members_count = $wpdb->get_var($wpdb->prepare(
		"SELECT COUNT(DISTINCT user_id) FROM {$wpdb->prefix}bp_activity WHERE DATE(date_recorded) >= %s",
		$days_ago
	));

	// Calculate the inactive BuddyPress members count as the difference between total and active members
	$inactive_members_count = $total_members_count - $active_members_count;

	// Return the results as an array
	return array(
		'total_members' => $total_members_count,
		'active_members' => $active_members_count,
		'inactive_members' => $inactive_members_count,
	);
}

function buddypress_member_counts_shortcode($atts)
{
	$atts = shortcode_atts(
		array(
			'days' => 7, // Default to 7 days
		), $atts);

	// Get the initial member counts
	$member_counts = get_buddypress_member_counts($atts['days']);

	ob_start();
	?>
	<div class="buddypress-member-counts" data-days="<?php echo esc_attr($atts['days']); ?>">
		<h3>Members Statistics</h3>
		<hr>
		<select id="member-counts-timeframe">
			<option value="7" <?php selected($atts['days'], 7); ?>>7 Days</option>
			<option value="30" <?php selected($atts['days'], 30); ?>>30 Days</option>
			<option value="60" <?php selected($atts['days'], 60); ?>>60 Days</option>
			<option value="90" <?php selected($atts['days'], 90); ?>>90 Days</option>
		</select>
		<p><span>Total Members: </span><span class="total-members">
				<?php echo $member_counts['total_members']; ?>
			</span></p>
		<!--             <p><span>Active Members: </span><span class="active-members"><?php //echo $member_counts['active_members']; ?></span></p> -->
		<!--              <p><span>Inactive Members: </span><span class="inactive-members"><?php //echo $member_counts['inactive_members']; ?></span></p> -->

		<div style="width: 100%;">
			<canvas id="myPieChart"></canvas>
		</div>


	</div>
	<?php
	return ob_get_clean();
}
add_shortcode('buddypress_member_counts', 'buddypress_member_counts_shortcode');



function buddypress_member_counts_shortcode_ajax_handler()
{
	// Check if the "days" parameter is set in the AJAX request
	$days = isset($_POST['days']) ? intval($_POST['days']) : 7;

	// Get the updated member counts
	$member_counts = get_buddypress_member_counts($days);

	// Return the response in JSON format
	wp_send_json_success($member_counts);
}
add_action('wp_ajax_buddypress_member_counts', 'buddypress_member_counts_shortcode_ajax_handler');
add_action('wp_ajax_nopriv_buddypress_member_counts', 'buddypress_member_counts_shortcode_ajax_handler');

//member-stats widget ends


if (function_exists('acf_add_options_page')) {
	acf_add_options_page(array(
		'page_title' => 'Course Content',
		'menu_title' => 'Course Content',
		'menu_slug' => 'course-content',
		'capability' => 'edit_posts',
		'redirect' => false
	));
}

function custom_learndash_taxonomies($taxonomies)
{
	// Add a custom taxonomy to the list
	$taxonomies['slug'] = 'custom_taxonomy';
	$taxonomies['singular_name'] = 'Course Topic';

	return $taxonomies;
}
add_filter('sfwd_cpt_register_tax', 'custom_learndash_taxonomies');


// function custom_forum_tags_search() {
//     if (bp_is_active('forums')) {
//         echo '<form role="search" method="get" id="forum-tag-search" action="' . esc_url(home_url()) . '">';
//         echo '<label for="forum-tag-search-input">Search by Tag:</label>';
//         echo '<input type="text" name="tag" id="forum-tag-search-input" value="' . esc_attr(get_query_var('tag')) . '" placeholder="Enter a tag" />';
//         echo '<input type="submit" id="forum-tag-submit" value="Search" />';
//         echo '</form>';
//     }
// }
// add_action('bbp_before_group_forum_display', 'custom_forum_tags_search');




function getSelectedMasterMind($folder_id, $sort_by = 'date', $order = 'desc', $page = '1')
{
	$access_token = get_field('access_token', 2935);
	$referrer = "https://mydtonline.com";
	$fields = 'uri,pictures.base_link,embed.html,description,created_time,modified_time,tags'; // Specify the fields you want
	$per_page = 100; // Number of results per page

	$isHidden = 'hidemastermind';
	$api_url = "https://api.vimeo.com/me/projects/$folder_id/videos?sort=$sort_by&direction=$order&page=$page&fields=$fields&per_page=$per_page";

	if (!current_user_can('administrator')) {
		$api_url = $api_url . '&' . http_build_query(['filter_tag_exclude' => $isHidden]);
	}


	$ch = curl_init($api_url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Authorization: Bearer ' . $access_token,
		'Referer: ' . $referrer,
	));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$response = curl_exec($ch);
	curl_close($ch);

	$data = json_decode($response, true);
	return $data;
}

function updateVimeoTag($video_id, $action)
{
	// Retrieve Vimeo access token
	$access_token = get_field('access_token', 2935);

	// Define tag word and construct API URL
	$tag_word = 'hidemastermind';
	$api_url = "https://api.vimeo.com/videos/{$video_id}/tags/{$tag_word}";

	$ch = curl_init($api_url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $action);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
		'Authorization: Bearer ' . $access_token,
	]);

	// Execute cURL session to remove tag
	$updated = curl_exec($ch);
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	$response = array();
	$response['httpCode'] = $httpCode;
	if ($httpCode === 200) {
		$response['isHidden'] = true;
	} elseif ($httpCode === 204) {
		$response['isHidden'] = false;
	}

	return $response;
}

// WordPress AJAX handler
add_action('wp_ajax_video_visibility_toggler_mastermind', 'video_visibility_toggler_mastermind');
add_action('wp_ajax_nopriv_video_visibility_toggler_mastermind', 'video_visibility_toggler_mastermind');

function video_visibility_toggler_mastermind()
{
	// Get video ID and action from the AJAX request
	$video_id = sanitize_text_field($_POST['video_id']);
	$vimeoAction = sanitize_text_field($_POST['vimeoAction']);
	$result = updateVimeoTag($video_id, $vimeoAction);
	if ($result) {
		wp_send_json_success($result);
	} else {
		wp_send_json_error($result);
	}

}



function ajax_get_selected_mastermind()
{
	// Check for security and nonce validation if needed.

	$folder_id = $_POST['folder_id'];
	$sort_by = $_POST['sort_by'];
	$order = $_POST['order'];
	$page = $_POST['page'];

	// Call the getSelectedMasterMind function to fetch data.
	$data = getSelectedMasterMind($folder_id, $sort_by, $order, $page);

	wp_send_json($data);
}


add_action('wp_ajax_get_selected_mastermind', 'ajax_get_selected_mastermind');
add_action('wp_ajax_nopriv_get_selected_mastermind', 'ajax_get_selected_mastermind');


function populate_roles_visibility_field($field)
{
	// Check if this is the 'wordpress_roles_visibility' field
	if ($field['name'] === 'wordpress_roles_visibility') {
		// Get the list of user roles
		global $wp_roles;
		$user_roles = $wp_roles->get_names();

		// Update the field's choices with all roles and set them as selected by default
		$field['choices'] = $user_roles;

		// Set the default value to include all roles
		$field['default_value'] = array_keys($user_roles);
	}

	return $field;
}

add_filter('acf/load_field', 'populate_roles_visibility_field');


//Function to create alphabetical list of current user groups
function current_user_buddypress_groups_shortcode()
{
	// Check if BuddyPress is active
	if (function_exists('bp_is_active')) {

		$current_user_id = get_current_user_id();
		$groups_id = groups_get_user_groups($current_user_id)['groups'];

		if (!empty($groups_id)) {
			echo '<div class="my-groups-profile" style="max-height: 300px; overflow-y: scroll;">';
			echo '<h3>My Groups</h3>';
			echo '<ul>';
			$group_names = array();

			foreach ($groups_id as $group_id) {
				$group_names[] = groups_get_group($group_id)->name;
			}

			asort($group_names);
			$sn = 0;
			foreach ($group_names as $group_name) {
				if (!empty($group_name)) {
					$sn++;
					echo '<li>' . $sn . ' ' . $group_name . '</li>';
				}
			}
			echo '</ul>';
			echo '</div>';
			echo '</div>';
		}
	}
}

function updateVimeoMeta($video_id, $new_description)
{
	$access_token = get_field('access_token', 2935);
	$api_url = "https://api.vimeo.com/videos/$video_id";

	$data = array(
		'description' => $new_description,
	);

	$ch = curl_init($api_url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Authorization: Bearer ' . $access_token,
		'Content-Type: application/json',
	));
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$response = curl_exec($ch);
	curl_close($ch);

	$updated_data = json_decode($response, true);
	return $updated_data;
}

// WordPress AJAX callback for updating video description
function update_video_description_callback()
{
	if (isset($_POST['video_id']) && isset($_POST['new_description'])) {
		$video_id = $_POST['video_id'];
		$new_description = $_POST['new_description'];

		// Call the function to update the video description
		$result = updateVimeoMeta($video_id, $new_description);

		wp_send_json($result);
	} else {
		echo json_encode(array('message' => 'Invalid input.'));
	}

	// Make sure to exit to prevent extra output
	wp_die();
}
add_action('wp_ajax_update_video_description', 'update_video_description_callback');
add_action('wp_ajax_nopriv_update_video_description', 'update_video_description_callback');

function getVimeoDescription($video_id)
{
	$access_token = get_field('access_token', 2935);
	$api_url = "https://api.vimeo.com/videos/$video_id?fields=description";

	$ch = curl_init($api_url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Authorization: Bearer ' . $access_token,
		'Content-Type: application/json',
	));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$response = curl_exec($ch);
	curl_close($ch);

	$video_data = json_decode($response, true);

	if (isset($video_data['description'])) {
		return $video_data['description'];
	} else {
		return "Description not available.";
	}
}

// WordPress AJAX callback for updating video description
function get_video_description_callback()
{
	if (isset($_POST['video_id'])) {
		$video_id = $_POST['video_id'];

		// Call the function to update the video description
		$result = getVimeoDescription($video_id);

		wp_send_json($result);
	} else {
		echo json_encode(array('message' => 'Invalid input.'));
	}

	// Make sure to exit to prevent extra output
	wp_die();
}
add_action('wp_ajax_get_video_description', 'get_video_description_callback');


function get_tag_suggestions()
{
	$query = sanitize_text_field($_POST['query']);
	$forum_id = sanitize_text_field($_POST['forum_id']);
	$topic_tag_identifier = sanitize_text_field($_POST['topic_tag']); // Field can be either slug or name
	$suggestions = array();

	// Load the terms using LIKE
	$term_args = array(
		'taxonomy' => bbp_get_topic_tag_tax_id(), // Replace with your taxonomy
		'name__like' => $query,
		'fields' => 'ids',
	);

	$term_ids = get_terms($term_args);

	// Define a custom query to retrieve bbPress topics
	$topic_args = array(
		'post_type' => bbp_get_topic_post_type(),
		'posts_per_page' => -1,
		'post_parent' => $forum_id,
		'tax_query' => array(
			'relation' => 'OR',
			array(
				'taxonomy' => bbp_get_topic_tag_tax_id(),
				'field' => 'term_id', // Use 'term_id' instead of 'id'
				'terms' => $term_ids,
			),
		),
	);

	$topics = new WP_Query($topic_args);


	if ($topics->have_posts()) {
		while ($topics->have_posts()) {
			$topics->the_post();
			$topic_id = get_the_ID();
			$author_id = get_the_author_ID();
			$permalink = get_permalink();
			$author_url = bp_core_get_userlink($author_id, false, true);
			$author_name = bp_core_get_user_displayname($author_id);
			$avatar_url = bp_core_fetch_avatar(array(
				'item_id' => $author_id,
				'type' => 'full',
				'html' => FALSE,
			));
			$topic_title = get_the_title();
			$topic_tag_excerpt = get_the_excerpt();
			$topic_excerpt =  mb_strimwidth($topic_tag_excerpt, 0, 110) . '...';
			$how_old = bp_core_time_since(strtotime(get_the_date()));
			$reply_count = bbp_get_topic_reply_count($topic_id);

			// Build the tag suggestion array with keys
			$tag_suggestion = array(
				'topic_id' => $topic_id,
				'author_id' => $author_id,
				'permalink' => $permalink,
				'author_url' => $author_url,
				'author_name' => $author_name,
				'avatar_url' => $avatar_url,
				'topic_title' => $topic_title,
				'topic_excerpt' => $topic_excerpt,
				'how_old' => $how_old,
				'reply_count' => $reply_count,
			);

			$suggestions[] = $tag_suggestion;
		}

	}

	// Reset the post data
	wp_reset_postdata();

	// Send the HTML directly
	wp_send_json($suggestions);
	wp_die();
}



add_action('wp_ajax_get_tag_suggestions', 'get_tag_suggestions');
add_action('wp_ajax_nopriv_get_tag_suggestions', 'get_tag_suggestions');




function add_search_form_before_topics_loop()
{
	?>
	<!-- Your search form HTML goes here -->
	<div id="topics-search" class="ui-front">
		<div class="search-suggestions" style="display:none;">
			<div id="bbp-search-form">
				<input type="text" value="" name="s" id="bbp-s" placeholder="Search Topics by Tag" autocomplete="off" />
			</div>
		</div>
		<i class="bb-icon-l bb-icon-search"></i>
	</div>
	<!-- End of search form -->
	<?php

}
add_action('custom_bbp_template_before_new_post', 'add_search_form_before_topics_loop');


function assign_courses_by_tag_to_user($tag_name, $user_id)
{
	$args = array(
		'post_type' => 'sfwd-courses',
		'posts_per_page' => -1,
		'tax_query' => array(
			array(
				'taxonomy' => 'ld_course_tag',
				// LearnDash course tag custom taxonomy
				'field' => 'name',
				// Field to search by tag name
				'terms' => $tag_name,
			),
		),
	);

	$course_query = new WP_Query($args);

	if ($course_query->have_posts()) {
		while ($course_query->have_posts()) {
			$course_query->the_post();
			$course_id = get_the_ID();
			$result = ld_update_course_access($user_id, $course_id);
		}
		wp_reset_postdata();
		return true; // Success
	}

	return false; // No courses found with the specified tag
}


function assign_courses_based_on_role($user_login, $user_data)
{
	$user_id = $user_data->data->ID;
	global $wp_roles;
	$all_roles = $wp_roles->roles;
	$course_tags = get_terms(
		array(
			'taxonomy' => 'ld_course_tag',
			'hide_empty' => false,
		)
	);

	$course_tag_names = array();

	if (!empty($course_tags) && !is_wp_error($course_tags)) {
		foreach ($course_tags as $course_tag) {
			$course_tag_names[] = $course_tag->name;
		}
	}

	//$user_data = get_userdata($user_id);
	$user_roles = $user_data->roles;
	$role_names = array();
	foreach ($user_roles as $role) {
		foreach ($all_roles as $role_key => $role_details) {
			if ($role_key == $role) {
				$role_name = $role_details['name'];
				$role_names[] = $role_name;
			}
		}
	}
	foreach ($role_names as $role_name) {
		if (in_array(trim($role_name), $course_tag_names)) {

			assign_courses_by_tag_to_user(trim($role_name), $user_id);
		}

	}

}

add_action('wp_login', 'assign_courses_based_on_role', 10, 2);



// Assuming you have identified a hook like 'mec_before_main_content'
add_action('mec_full_skin_head', 'add_custom_button_for_admin');

function add_custom_button_for_admin()
{
	// Check if the current user is an admin
	if (current_user_can('manage_options')) {
		// Output a button or link that triggers the calendar creation function
		echo '<button id="create_new_calendar">Create New Event</button>';
	}
}

function custom_css_for_mec_events()
{
	// Get the current URL path
	$current_url_path = $_SERVER['REQUEST_URI'];

	// Check if the URL path contains '/calendar/' and has an event name
	if (strpos($current_url_path, '/calendar/') !== false && preg_match('/\/calendar\/([^\/]+)/', $current_url_path, $matches)) {
		// Extract the event name from the URL
		$event_name = $matches[1];

		// Check if the event name is not empty
		if (!empty($event_name)) {
			// Echo the custom CSS directly in the head
			echo '<style type="text/css">#comments { display: none; }</style>';
		}
	}
}

add_action('wp_head', 'custom_css_for_mec_events');

// add event popup only on event page because conflicting the popup with chat
function add_event_popup_to_specific_page()
{
	// Check if the body class contains "post-type-archive-mec-events"
	$body_classes = get_body_class();
	if (in_array('post-type-archive-mec-events', $body_classes)) {
		echo '<section class="event-popup-overlay">
                  <div class="create-event">  
                      <button class="event-popup-close"><span class="cross">×</span></button>
                      ' . do_shortcode('[MEC_fes_form]') . '
                  </div>
              </section>';
	}
}

add_action('wp_footer', 'add_event_popup_to_specific_page');


//Stature Team LeaderBoard
// AJAX handler for profile suggestions
function get_profile_suggestions()
{
	$searchTerm = sanitize_text_field($_GET['term']);

	// Initialize user query arguments
	$args = array(
		'search' => '*' . $searchTerm . '*',
		'search_columns' => array('user_nicename', 'user_login', 'user_email', 'display_name'),
		'number' => -1,
	);

	// Create a new WP_User_Query
	$user_query = new WP_User_Query($args);

	if (!empty($user_query->results)) {
		$userSuggestions = array_map(function ($user) {
			$full_name = $user->display_name;
			$avatar_url = get_avatar_url($user->ID, array('size' => 'full'));
			$user_id = $user->ID;
			return array(
				'full_name' => $full_name,
				'avatar_url' => $avatar_url,
				'user_id' => $user_id,
			);
		}, $user_query->results);

		// Filter suggestions based on the search term
		$filteredSuggestions = array_filter($userSuggestions, function ($user) use ($searchTerm) {
			return stripos($user['full_name'], $searchTerm) !== false;
		});

		// Return the filtered suggestions as JSON
		wp_send_json_success(array_values($filteredSuggestions));
	} else {
		// No users found
		wp_send_json_error('No suggestions found.');
	}
}

// Hook for the AJAX handler
add_action('wp_ajax_get_profile_suggestions', 'get_profile_suggestions');
add_action('wp_ajax_nopriv_get_profile_suggestions', 'get_profile_suggestions');


function save_buyer_rank()
{
	// Security checks and nonce verification should be added here for production use.
	$formData = isset($_POST['formData']) ? ($_POST['formData']) : [];

	$user_id = sanitize_text_field($formData['userId']);
	$points = sanitize_text_field($formData['points']);
	$place = intval($formData['place']);
	$rank = '';
	$rank1 = '<img class="rank-stamp" src="' . esc_url('https://www.mydtonline.com/wp-content/uploads/2023/12/1st-place-badge-1.png') . '" alt="Thumbnail">';
	$rank2 = '<img class="rank-stamp" src="' . esc_url('https://www.mydtonline.com/wp-content/uploads/2023/12/2nd-place-badge-1.png') . '" alt="Thumbnail">';
	$rank3 = '<img class="rank-stamp" src="' . esc_url('https://www.mydtonline.com/wp-content/uploads/2023/12/3rd-place-badge-1.png') . '" alt="Thumbnail">';

	if ($place === 1) {
		$rank = $rank1;
	} elseif ($place === 2) {
		$rank = $rank2;
	} else {
		$rank = $rank3;
	}
	// Set the time zone to Pacific
	$pacificTimeZone = new DateTimeZone('America/Los_Angeles');

	// Create a DateTime object with the current date and time in the Pacific Time Zone
	$date = new DateTime('now', $pacificTimeZone);
	// Format the date using the MySQL date format
	$formattedDate = $date->format('Y-m-d H:i:s');

	$publish_status = 1; // Adjusted to a tinyint(1) column

	global $wpdb;

	$table = $wpdb->prefix . 'dt_buyers_ranked';

	// Check if the table exists
	if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
		// Table does not exist, create it
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			added_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			publish_status tinyint(1) DEFAULT '0' NOT NULL,
			points int(11) DEFAULT '0' NOT NULL,
			place varchar(255) DEFAULT '' NOT NULL,
			rank int(11) DEFAULT '1' NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY id (id)
		) $charset_collate;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	// Prepare data for insertion
	$data_to_insert = array(
		'user_id' => $user_id,
		'points' => $points,
		'place' => $rank, // Corrected to match the actual column name
		'added_date' => $formattedDate,
		'publish_status' => $publish_status,
		'rank' => $place // Corrected to match the actual column name
	);

	// Insert data into the table
	$result = $wpdb->insert($table, $data_to_insert);
	$data_to_insert['fullname'] = bp_core_get_userlink($user_id);
	$data_to_insert['avatar'] = bp_core_fetch_avatar(array('item_id' => $user_id, 'type' => 'full'));
	if ($result) {
		$inserted_id = $wpdb->insert_id;
		$data_to_insert['id'] = $inserted_id;
		wp_send_json_success($data_to_insert);
	} else {
		wp_send_json_error($result);
	}
}

// Hook to handle the AJAX request
add_action('wp_ajax_save_buyer_rank', 'save_buyer_rank');
add_action('wp_ajax_nopriv_save_buyer_rank', 'save_buyer_rank');


// Add this code in your theme's functions.php or in a custom plugin file

add_action('wp_ajax_remove_buyer_action', 'remove_buyer_action');
function remove_buyer_action()
{
	// Security checks and nonce verification should be added here for production use.
	$row_id = isset($_POST['row_id']) ? intval($_POST['row_id']) : 0;

	// Perform removal from the database
	if ($row_id > 0) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'dt_buyers_ranked';

		$result = $wpdb->delete($table_name, array('id' => $row_id), array('%d'));

		if ($result !== false) {
			wp_send_json_success($result);
		} else {
			wp_send_json_error('Error removing buyer.');
		}
	} else {
		wp_send_json_error('Invalid row ID.');
	}
}


add_filter('gamipress_leaderboards_leaderboard_column_display_name', 'my_prefix_buddypress_link_on_leaderboards', 10, 6);
function my_prefix_buddypress_link_on_leaderboards($output, $leaderboard_id, $position, $item, $column_name, $leaderboard_table)
{
	$url = bp_core_get_user_domain($item['user_id']);

	if ($leaderboard_id === 3231) {
		return sprintf('<a href="%s">%s</a>',
			$url,
			$output
		);
	}

	$country_name = xprofile_get_field_data('Country', $item['user_id']);
	$country = empty($country_name) ? '' : ' (' . $country_name . ')';
	return sprintf('<a href="%s">%s</a>' . $country,
		$url,
		$output
	);
}


add_filter('gamipress_leaderboards_leaderboard_column_avatar', 'custom_badge_on_leaderboards', 10, 6);
function custom_badge_on_leaderboards($output, $leaderboard_id, $position, $item, $column_name, $leaderboard_table)
{
	if ($leaderboard_id !== 3231) {
		return $output;
	}



	$member_rank = intval($item['card-star']);
	$rank1 = '<img class="rank-stamp" src="' . esc_url('https://www.mydtonline.com/wp-content/uploads/2023/12/1st-place-badge-1.png') . '" alt="Thumbnail">';
	$rank2 = '<img class="rank-stamp" src="' . esc_url('https://www.mydtonline.com/wp-content/uploads/2023/12/2nd-place-badge-1.png') . '" alt="Thumbnail" >';
	$rank3 = '<img class="rank-stamp" src="' . esc_url('https://www.mydtonline.com/wp-content/uploads/2023/12/3rd-place-badge-1.png') . '" alt="Thumbnail">';

	$url = bp_core_get_user_domain($item['user_id']);

	if ($member_rank === 1) {
		return sprintf($rank1 . '<a href="%s">%s</a> ',
			$url,
			$output
		);
	} elseif ($member_rank === 2) {
		return sprintf($rank2 . '<a href="%s">%s</a> ',
			$url,
			$output
		);
	} elseif ($member_rank === 3) {
		return sprintf($rank3 . '<a href="%s">%s</a> ',
			$url,
			$output
		);
	} else {
		return $output;
	}
}

function my_prefix_custom_leaderboard_columns($columns, $leaderboard_id, $leaderboard)
{
	if($leaderboard_id == 3606) {
		unset($columns['position']);
		$columns['enagic-rank'] = __('Enagic Rank');
	}
	if ($leaderboard_id === 3234 || $leaderboard_id===3231) {
		unset($columns['position']);
		$columns['card-star'] = __('Rank');
		$columns['point'] = __('Points');
		
		if ($leaderboard_id === 3231) {
			unset($columns['card-star']);
		}
	}

	
	return $columns;
}

add_filter('gamipress_leaderboards_leaderboard_columns_info', 'my_prefix_custom_leaderboard_columns', 10, 3);

function my_prefix_custom_leaderboard_column_output($output, $leaderboard_id, $position, $item, $column_name, $leaderboard_table)
{

	$listed_users = $leaderboard_table->items;
	if ($position === 0) {
		$listed_users[$position]['rank'] = 1;
	} else {
		$previousPosition = $position - 1;
		$previousItem = $listed_users[$previousPosition];
		$previousRank = $previousItem['rank'];
		$previousUserPoints = $previousItem['point'];

		$currentUserPoints = $listed_users[$position]['point'];

		$hasMorePoints = ($previousUserPoints > $currentUserPoints) ? true : false;

		if ($hasMorePoints) {
			$listed_users[$position]['rank'] = $previousRank + 1;
		} else {
			$listed_users[$position]['rank'] = $previousRank;
		}
	}
	$leaderboard_table->items = $listed_users;
	return $listed_users[$position]['rank'];
}
add_filter('gamipress_leaderboards_leaderboard_column_rank', 'my_prefix_custom_leaderboard_column_output', 10, 6);

add_filter('gamipress_leaderboards_leaderboard_column_point', 'custom_points_suffix', 10, 6);
function custom_points_suffix($output, $leaderboard_id, $position, $item, $column_name, $leaderboard_table){
	if($leaderboard_id=== 3231){
		$output .= ' Points';
	}
	return $output;
}


//====================================

// Shortcode to display completed and in-progress courses button and details
function completed_courses_shortcode($atts) {
    // Check if the user is logged in
    if (is_user_logged_in()) {
        $user_id = bp_displayed_user_id();

        // Get all enrolled courses for the user
        $enrolled_courses = learndash_user_get_enrolled_courses($user_id);

        $user_data = get_userdata($user_id);
        $user_name = $user_data->data->display_name;
        $output = '';

        $output .= "<button id='show-completed-courses'>Show {$user_name}'s Courses</button>";

        // Popup container for course details
        $output .= '<div id="completed-courses-popup" class="ld-course-popup-container" style="display: none;">';
        $output .= '<div class="ld-course-popup-content">';
        $output .= '<h2>Courses List</h2>';
        $output .= '<span class="close-course-complete-popup" id="closeCompletedCoursesPopup">&times;</span>';
        $output .= '<div class="course-grid">'; // Added a container for the course grid
        
        // Completed courses will be displayed on the left side
        $output .= '<div class="completed-courses">';
        $output .= '<h3>Completed Courses</h3>';
        $output .= '<ul class="completed-courses-list">';
        
        // Loop through enrolled courses to display completed courses
        foreach ($enrolled_courses as $course_id) {
            // Check if the course is completed
            if (learndash_course_completed($user_id, $course_id)) {
                $course = get_post($course_id);
                $total_steps = learndash_get_course_steps_count($course_id);
                $completed_steps = learndash_course_get_completed_steps($user_id, $course_id);
              $completion_percentage = ($completed_steps / $total_steps) * 100;

                // Display course information with steps
                $output .= '<li class="course-item completed-course-item">';
                $output .= '<div class="course-details">';
//                 $output .= '<div class="ld-status-icon ld-status-complete ld-secondary-complete-icon" title="Completed">';
//                 $output .= '<img src="https://www.mydtonline.com/wp-content/uploads/2023/12/a52d4248ee1559908b63f3c2c7f73239-100-percent-circle-graph.png" alt="Completed">';
//                 $output .= '</div>';
          	$output .= '<div class="radial-progress progress" data-progress="' . round($completion_percentage) . '" style=" background-color: #138C51;';
				$output .= '    background: conic-gradient(#138C51 0% ' . round($completion_percentage) . '%, #eee ' . round($completion_percentage) . '% 100%);">';
				$output .= '    <div class="overlay">';
				$output .= '        <span>' . round($completion_percentage) . '%</span>';
				$output .= '    </div>';
				$output .= '</div>';
                $output .= '<h4 class="course-title"><a href="' . esc_url(get_permalink($course_id)) . '" target="_blank">' . esc_html($course->post_title) . '</a></h4>';
                $output .= '<p class="course-steps"><span style="color: #05d786;">' . $completed_steps . '/' . $total_steps . '</span> steps completed</p>';
                // Additional course details can be added here
                $output .= '</div>'; // Close course-details
                $output .= '</li>';
            }
        }

        $output .= '</ul>'; // Close completed-courses-list
        $output .= '</div>'; // Close completed-courses
        

        // In-progress courses will be displayed on the right side
        $output .= '<div class="in-progress-courses">';
        $output .= '<h3>In Progress Courses</h3>';
        $output .= '<ul class="in-progress-courses-list">';
        
        // Loop through enrolled courses to display in-progress courses
        foreach ($enrolled_courses as $course_id) {
            // Check if the course is in-progress (not completed)
            if (!learndash_course_completed($user_id, $course_id)) {
                $course = get_post($course_id);
                $total_steps = learndash_get_course_steps_count($course_id);
                $completed_steps = learndash_course_get_completed_steps($user_id, $course_id);

                // Calculate completion percentage
                $completion_percentage = ($completed_steps / $total_steps) * 100;
				
				


				// Display course information with steps
				$output .= '<li class="course-item in-progress-course-item">';
				$output .= '<div class="course-details">';

				$output .= '<div class="radial-progress progress" data-progress="' . round($completion_percentage) . '" style=" background-color: #138C51;';
				$output .= '    background: conic-gradient(#138C51 0% ' . round($completion_percentage) . '%, #eee ' . round($completion_percentage) . '% 100%);">';
				$output .= '    <div class="overlay">';
				$output .= '        <span>' . round($completion_percentage) . '%</span>';
				$output .= '    </div>';
				$output .= '</div>';

				

                $output .= '<h4 class="course-title"><a href="' . esc_url(get_permalink($course_id)) . '" target="_blank">' . esc_html($course->post_title) . '</a></h4>';
                $output .= '<p class="course-steps"><span style="color: #05d786;">' . $completed_steps . '/' . $total_steps . '</span> steps completed</p>';
                // Additional course details can be added here
                $output .= '</div>'; // Close course-details
                $output .= '</li>';
            }
        }

        $output .= '</ul>'; // Close in-progress-courses-list
        $output .= '</div>'; // Close in-progress-courses

        $output .= '</div>'; // Close course-grid
        $output .= '</div>'; // Close popup-content
        $output .= '</div>'; // Close completed-courses-popup


        return $output;
    }

    return ''; // Return an empty string if the user is not logged in
}

add_shortcode('completed_courses', 'completed_courses_shortcode');


//=====================

function learndash_custom_user_status_shortcode( $atts = array(), $content = '', $shortcode_slug = 'learndash_user_status' ) {
	if ( learndash_is_active_theme( 'legacy' ) ) {
		return $content;
	}

	/** This filter is documented in includes/shortcodes/ld_course_resume.php */
	$atts = apply_filters( 'learndash_shortcode_atts', $atts, $shortcode_slug );


		$user_id = bp_displayed_user_id();

	 

	if ( empty( $atts ) ) {
		$atts = array( 'return' => true );
	} elseif ( ! isset( $atts['return'] ) ) {
		$atts['return'] = true;
	}

	$atts['isblock'] = true;

	$course_info = SFWD_LMS::get_course_info( $user_id, $atts );

	ob_start();

	SFWD_LMS::get_template(
		'shortcodes/user-status.php',
		array(
			'course_info'    => $course_info,
			'shortcode_atts' => $atts,
		),
		true
	);

	$content .= ob_get_clean();

	return $content;

}
add_shortcode( 'learndash_custom_user_status', 'learndash_custom_user_status_shortcode', 3 );

//==================================================

add_filter('learndash_taxonomy_args', 'custom_learndash_taxonomy_args', 10, 2);
function custom_learndash_taxonomy_args($tax_options, $tax_slug)
{
	$course_language_args = array(
		'public' => true,
		'hierarchical' => true, // Set this to false for a tag-type taxonomy
		'show_ui' => true,
		'show_in_menu' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'show_in_rest' => true,
		'rewrite' => array(
			'slug' => 'ld_course_language'
		),
		'labels' => array(
			'name' => 'Course Languages',
			'singular_name' => 'Course Language',
			'search_items' => 'Search Course Language',
			'all_items' => 'All Course Languages',
			'parent_item' => 'Parent Course Language',
			'parent_item_colon' => 'Parent Course Language:',
			'edit_item' => 'Edit Course Language',
			'update_item' => 'Update Course Language',
			'add_new_item' => 'Add New Course Language',
			'new_item_name' => 'New Course Language Name',
			'menu_name' => 'Course Languages'
		),
	);

	// Merge the custom taxonomy arguments with the existing tax_options
	$tax_options[] = array(
		'post_types' => array('sfwd-courses'),
		'tax_args' => $course_language_args,
	);

	// Register the custom taxonomy
	register_taxonomy('ld_course_language', 'sfwd-courses', $course_language_args);

	do_action('ld_custom_register_taxonomies');
	// Return the modified $tax_options
	return $tax_options;
}


function show_enagic_rank_in_qualifiication_table($query_vars, $leaderboard_id, $leaderboard)
{
	if ($leaderboard_id === 3606) {
		$current_month = date('Y-m-01');
		$query_vars['select'] = 'u.ID AS user_id, ( 
			SELECT ue2.title 
			FROM wp_gamipress_user_earnings AS ue2 
			WHERE ue2.user_id = u.ID 
			AND ue2.post_type = "enagic-rank"
			ORDER BY ue2.date DESC
			LIMIT 1
			) AS `enagic-rank`';

		$query_vars['order_by'] = "ORDER BY `enagic-rank` ASC";

		$query_vars['where'] = "WHERE 1=1 AND EXISTS (
				SELECT 1
				FROM wp_gamipress_user_earnings AS ue2
				WHERE ue2.user_id = u.ID
				AND ue2.post_type = 'enagic-rank'
				AND ue2.date >= '{$current_month} 00:00:00'
				)";
	}
	return $query_vars;

}
add_filter('gamipress_leaderboards_leaderboard_query_vars', 'show_enagic_rank_in_qualifiication_table', 10, 3);

add_filter("gamipress_leaderboards_leaderboard_query", 'show_rank_in_leaderboard_table', 10, 4);

function show_rank_in_leaderboard_table($query, $query_vars, $leaderboard_id, $leaderboard)
{	$current_year_start = date('Y-01-01');
	$current_year_end = date('Y-12-31 23:59:59');
	
	$current_month_start = date('Y-m-01');
	$current_month_end = date('Y-m-t 23:59:59');
	
	if ($leaderboard_id === 3234) {
		$query = "SELECT SQL_CALC_FOUND_ROWS
					user_id,
					`rank` as `card-star`,
					`point`
				FROM (
					SELECT
						user_id,
						`point`,
						@rank := IF(@prev_point = `prev_point`, @rank, @rank + 1) AS `rank`,
						@prev_point := `prev_point`
					FROM (
						SELECT DISTINCT
							u.ID AS user_id,
							(
								SELECT GREATEST(
									IFNULL(SUM(pm1.meta_value), 0),
									0
								)
								FROM wp_gamipress_logs AS l1
								INNER JOIN wp_gamipress_logs_meta AS pm1 ON (pm1.log_id = l1.log_id AND pm1.meta_key = '_gamipress_points')
								INNER JOIN wp_gamipress_logs_meta AS ptm1 ON (ptm1.log_id = l1.log_id)
								WHERE l1.user_id = u.ID
									AND pm1.meta_value != 0
									AND ptm1.meta_key = '_gamipress_points_type'
									AND ptm1.meta_value = 'point'
									AND l1.date >= '{$current_month_start}'
									AND l1.date <= '{$current_month_end}'
							) AS `point`,
							(
								SELECT GREATEST(
									IFNULL(SUM(pm1.meta_value), 0),
									0
								)
								FROM wp_gamipress_logs AS l1
								INNER JOIN wp_gamipress_logs_meta AS pm1 ON (pm1.log_id = l1.log_id AND pm1.meta_key = '_gamipress_points')
								INNER JOIN wp_gamipress_logs_meta AS ptm1 ON (ptm1.log_id = l1.log_id)
								WHERE l1.user_id = u.ID
									AND pm1.meta_value != 0
									AND ptm1.meta_key = '_gamipress_points_type'
									AND ptm1.meta_value = 'point'
									AND l1.date >= '{$current_month_start}'
									AND l1.date <= '{$current_month_end}'
							) AS `prev_point`
						FROM wp_users AS u
						WHERE 1=1
						ORDER BY `point` DESC
					) AS subquery_alias
					JOIN (SELECT @rank := 0, @prev_point := NULL) AS rank_init
					ORDER BY `rank` ASC
				) AS final_result
				WHERE `rank` <= 20;";
	}
	elseif ($leaderboard_id===3231){
		$query = "SELECT SQL_CALC_FOUND_ROWS
					user_id,
					`rank` as `card-star`,
					`point`
				FROM (
					SELECT
						user_id,
						`point`,
						@rank := IF(@prev_point = `prev_point`, @rank, @rank + 1) AS `rank`,
						@prev_point := `prev_point`
					FROM (
						SELECT DISTINCT
							u.ID AS user_id,
							(
								SELECT GREATEST(
									IFNULL(SUM(pm1.meta_value), 0),
									0
								)
								FROM wp_gamipress_logs AS l1
								INNER JOIN wp_gamipress_logs_meta AS pm1 ON (pm1.log_id = l1.log_id AND pm1.meta_key = '_gamipress_points')
								INNER JOIN wp_gamipress_logs_meta AS ptm1 ON (ptm1.log_id = l1.log_id)
								WHERE l1.user_id = u.ID
									AND pm1.meta_value != 0
									AND ptm1.meta_key = '_gamipress_points_type'
									AND ptm1.meta_value = 'point'
									AND l1.date >= '{$current_year_start}'
									AND l1.date <= '{$current_year_end}'
							) AS `point`,
							(
								SELECT GREATEST(
									IFNULL(SUM(pm1.meta_value), 0),
									0
								)
								FROM wp_gamipress_logs AS l1
								INNER JOIN wp_gamipress_logs_meta AS pm1 ON (pm1.log_id = l1.log_id AND pm1.meta_key = '_gamipress_points')
								INNER JOIN wp_gamipress_logs_meta AS ptm1 ON (ptm1.log_id = l1.log_id)
								WHERE l1.user_id = u.ID
									AND pm1.meta_value != 0
									AND ptm1.meta_key = '_gamipress_points_type'
									AND ptm1.meta_value = 'point'
									AND l1.date >= '{$current_year_start}'
									AND l1.date <= '{$current_year_end}'
							) AS `prev_point`
						FROM wp_users AS u
						WHERE 1=1
						ORDER BY `point` DESC
					) AS subquery_alias
					JOIN (SELECT @rank := 0, @prev_point := NULL) AS rank_init
					ORDER BY `rank` ASC
				) AS final_result
				WHERE `rank` <= 3;";
	}
	return $query;
}

// Site tour guide line
function enqueue_shepherd() {
    // Enqueue Shepherd.js stylesheet from CDN
    wp_enqueue_style('shepherd-css', 'https://cdn.jsdelivr.net/npm/shepherd.js@10.0.1/dist/css/shepherd.css');

    // Enqueue Shepherd.js script from CDN
    wp_enqueue_script('shepherd-js', 'https://cdn.jsdelivr.net/npm/shepherd.js@10.0.1/dist/js/shepherd.min.js', array('jquery'), null, true);
}

//add_action('wp_enqueue_scripts', 'enqueue_shepherd');




function show_site_tour() {
    // Enqueue the script to show the tour
    wp_enqueue_script('site-tour-script', get_stylesheet_directory_uri() . '/assets/js/site-tour-script.js', array('jquery'), null, true);
}

//add_action('wp_enqueue_scripts', 'show_site_tour');

function custom_learndash_completion_redirect($redirect_url, $post_id) {
	$courseId = learndash_get_course_id($post_id);//displayed course id
	$currentCourseSteps = learndash_get_course_steps($courseId);//displayed course steps
	$currentResourcePosition = array_search($post_id, $currentCourseSteps);//displayed resource position

	$associatedCourses = get_post_meta($courseId, '_numeric_value', true);//comma seperated corresponding courses
	$associatedCoursesArray = explode(',', $associatedCourses);//array converted
	$user_id = bp_displayed_user_id();

	foreach ($associatedCoursesArray as $associatedCourse) {
		$courseSteps = learndash_get_course_steps($associatedCourse);
		$correspondingResourceId = $courseSteps[$currentResourcePosition];//corresponding resource id

		$post = get_post($correspondingResourceId);
		learndash_process_mark_complete( $user_id, $correspondingResourceId );

	}

    return $redirect_url;
}

add_filter('learndash_completion_redirect', 'custom_learndash_completion_redirect', 10, 2);


add_filter('learndash_process_mark_complete', 'always_return_true', 10, 3);

function always_return_true($result, $post, $current_user) {
    return true;
}
function complete_corresponding_quizzes($quizdata, $current_user)
{
	$user_id = $current_user->ID;
	$quizId = $quizdata['quiz'];
	$course = $quizdata['course'];
	$courseId = $course->ID;
	$quizzes = learndash_get_course_quizzes($courseId);
	$currentQuizPosition = array_search($quizId, $quizzes); //displayed resource position

	$associatedCourses = get_post_meta($courseId, '_numeric_value', true); //comma seperated corresponding courses
	$associatedCoursesArray = explode(',', $associatedCourses); //array converted'

	$correspondingQuizData = $quizdata;

	foreach ($associatedCoursesArray as $associatedCourseId) {
		$associatedQuizzes = learndash_get_course_quizzes($associatedCourseId);
		$correspondingQuizId = $associatedQuizzes[$currentQuizPosition];
		$pro_quiz_id = get_post_meta($correspondingQuizId, 'quiz_pro_id', true);
		$course = get_post(learndash_get_course_id($correspondingQuizId));
		$lesson = get_post(learndash_get_lesson_id($correspondingQuizId));
		$questions = learndash_get_quiz_questions($quizId);
		foreach ($questions as &$question) {
			$question = new stdClass(); // Reset the $question variable to an empty object
		}

		$quiz_key = $correspondingQuizData['completed'] . '_' . absint($pro_quiz_id) . '_' . absint($correspondingQuizId) . '_' . absint($associatedCourseId);

		$correspondingQuizData['quiz'] = $correspondingQuizId;
		$correspondingQuizData['pro_quizid'] = $pro_quiz_id;
		$correspondingQuizData['course'] = $course->ID;
		$correspondingQuizData['lesson'] = $lesson;
		$correspondingQuizData['quiz_key'] = $quiz_key;

		$usermeta = get_user_meta($user_id, '_sfwd-quizzes', true);
		$usermeta = maybe_unserialize($usermeta);
		if (!is_array($usermeta)) {
			$usermeta = array();
		}
		$usermeta[] = $correspondingQuizData;


		update_user_meta($user_id, '_sfwd-quizzes', $usermeta);
	}

	return;
}

// Add the action hook with add_action
add_action('learndash_quiz_submitted', 'complete_corresponding_quizzes', 10, 2);

function learndash_get_course_quizzes($course_id = 0)
{
	if (!empty($course_id)) {
		$query_args = array(
			'post_type'      => 'sfwd-quiz',
			'posts_per_page' => -1,
			'meta_key'       => 'course_id',
			'meta_value'     => $course_id,
			'meta_compare'   => '=',
			'orderby'        => 'menu_order',  // Order by menu order
			'order'          => 'ASC',         // Choose 'ASC' for ascending or 'DESC' for descending
			// This tells WP_Query to return only the post IDs. Comment 
			// out if you want full Post object
			'fields'         => 'ids'
		);

		$query_results = new WP_Query($query_args);

		if (!empty($query_results->posts)) {
			return $query_results->posts;
		}
	}
}


// Function to create ld_course_tags for WordPress roles
function create_ld_course_tags_for_roles() {
    // Get all WordPress roles
    $wp_roles = wp_roles()->roles;

    // Roles to filter out
    $exclude_roles = array('administrator', 'editor', 'author', 'contributor', 'subscriber');

    // Check if there are roles
    if (is_array($wp_roles) && !empty($wp_roles)) {
        foreach ($wp_roles as $role_slug => $role_info) {
            // Check if the role should be excluded
            if (!in_array($role_slug, $exclude_roles)) {
                // Check if the term already exists
                $term_exists = term_exists($role_info['name'], 'ld_course_tag');

                // If the term doesn't exist, add it
                if (!$term_exists || is_wp_error($term_exists)) {
                    wp_insert_term($role_info['name'], 'ld_course_tag');
                }
            }
        }
    }
}

// Hook the function to run on LearnDash custom taxonomies registration
add_action('ld_custom_register_taxonomies', 'create_ld_course_tags_for_roles');
