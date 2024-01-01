<?php

/**
 * Plugin Name:       Guest Post
 * Plugin URI:        https://profiles.wordpress.org/sunilkumarthz/
 * Description:       Handle Guest Post Submission
 * Version:           1.0.0
 * Author:            Sunil Kumar Sharma
 * Author URI:        https://www.linkedin.com/in/sunilkumarthz/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       guest-post
 */

if (!defined('GPS_PLUGIN_NAME')) :
	define('GPS_PLUGIN_NAME', 'Guest Post Submission');
endif;

if (!defined('GPS_PLUGIN_VERSION')) :
	define('GPS_PLUGIN_VERSION', '1.0.0');
endif;

if (!defined('GPS_PLUGIN_PATH')) :
	define('GPS_PLUGIN_PATH', plugin_dir_path(__FILE__));
endif;

if (!defined('GPS_PLUGIN_URI')) :
	define('GPS_PLUGIN_URI', plugin_dir_url(__FILE__));
endif;

require_once GPS_PLUGIN_PATH . 'public/init.php';
