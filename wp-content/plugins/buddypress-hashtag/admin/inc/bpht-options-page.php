<?php
/**
 *
 * This template file is used for fetching desired options page file at admin settings end.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$admin_tabs = filter_input( INPUT_GET, 'tab' ) ? filter_input( INPUT_GET, 'tab' ) : 'welcome';
if ( isset( $_GET['tab'] ) ) { //phpcs:ignore
	$bpht_tab = sanitize_text_field( $admin_tabs );
} else {
	$bpht_tab = 'welcome';
}

bpht_include_admin_setting_tabs( $bpht_tab );

/**
 * Include setting template.
 *
 * @param string $bpht_tab
 */
function bpht_include_admin_setting_tabs( $bpht_tab ) {
	switch ( $bpht_tab ) {
		case 'welcome':
			include 'bpht-welcome-page.php';
			break;
		case 'general':
			include 'bpht-setting-general-tab.php';
			break;
		case 'hashtag-logs':
			include 'bpht-hashtag-delete-tab.php';
			break;
		case 'shortcodes':
			include 'bpht-setting-shortcodes.php';
			break;
		default:
			include 'bpht-welcome-page.php';
			break;
	}
}
