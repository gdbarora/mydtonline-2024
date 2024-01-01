<?php

/**
 * Plugin Name: BB Login Limit
 * Plugin URI: http://www.wordpress.com
 * Description: This plugin is used to manage user login limit on multiple devices
 * Version: 1.0.0
 * Author: Stature
 * Author URI: http://www.learndash.com
 * Text Domain: bbloginlimit
 * Doman Path: /languages/
 */

if (!defined('ABSPATH')) {
    die();
}

function bbDebug($data)
{
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

function bb_force_logout()
{
    nocache_headers();
    wp_clear_auth_cookie();
    do_action('wp_logout');
    wp_die("The User with this username already login!", '', array('back_link' => true));
}

/**
 * Disable multiple device user login
 *
 * @param string $user_login
 * @return void
 */
function bb_check_loggedin_user($username, $password)
{
    if (!username_exists($username)) {
        return;
    }

    $userData = wp_authenticate($username, $password);
    if (isset($userData->ID)) :
        $sessions = WP_Session_Tokens::get_instance($userData->ID);
        $sessions->destroy_all();
    endif;
}
add_action('wp_authenticate', 'bb_check_loggedin_user', 10, 2);


function bb_check_fb_content($content)
{
    if (str_contains($content, 'fb.') || str_contains($content, 'facebook.') || str_contains($content, 'fbstatic') || str_contains($content, 'fbcdn.') || str_contains($content, 'fburl.') || str_contains($content, 'facebook.com')) :
        return true;
    else :
        return false;
    endif;
}





function bp_get_thread_recipients($thread_id)
{
    global $wpdb;


    $thread_id = (int) $thread_id;
    $bp_loggedin_user_id = bp_loggedin_user_id();

    $bp = buddypress();

    $recipients = array();
    $sql        = $wpdb->prepare("SELECT * FROM {$bp->messages->table_name_recipients} WHERE thread_id = %d", $thread_id);
    $results    = $wpdb->get_results($sql);

    foreach ((array) $results as $recipient) {
        $bp_loggedin_user_id = intval($bp_loggedin_user_id);
        $key = intval($recipient->user_id);

        if ($bp_loggedin_user_id == $key) :

        else :
            $recipients[$recipient->user_id] = $recipient;
        endif;
    }



    // Cast all items from the messages DB table as integers.


    foreach ((array) $recipients as $key => $data) {

        $bp_loggedin_user_id = intval($bp_loggedin_user_id);
        $key = intval($key);

        if ($bp_loggedin_user_id == $data->user_id) :

        else :
            $recipients[$key] = (object) array_map('intval', (array) $data);
        endif;
    }


    /**
     * Filters the recipients of a message thread.
     *
     * @since BuddyPress 2.2.0
     *
     * @param array $recipients Array of recipient objects.
     * @param int   $thread_id  ID of the current thread.
     */
    return apply_filters('bp_messages_thread_get_recipients', $recipients, $thread_id);
}
