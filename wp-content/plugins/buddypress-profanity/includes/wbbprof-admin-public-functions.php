<?php
/**
 * This file is used for defining functions for use in admin and public.
 *
 * @link       http://www.wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    Buddypress_Profanity
 * @subpackage Buddypress_Profanity/includes
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *
 * Çontent to filter array (admin end use).
 */
function content_to_filter_array() {
	$content_filter        = array(
		'status_updates'    => __( 'Status Updates', 'buddypress-profanity' ),
		'activity_comments' => __( 'Activity Comments', 'buddypress-profanity' ),
		'messages'          => __( 'Messages', 'buddypress-profanity' ),
		'bbpress_title'     => __( 'bbPress Forums, Topics and Replies Title', 'buddypress-profanity' ),
		'bbpress_content'   => __( 'bbPress Forums, Topics and Replies Content', 'buddypress-profanity' ),
	);
	return $content_filter = apply_filters( 'wbbprof_content_to_filter_array', $content_filter );
}

/**
 *
 * Filter character symbol array (admin end use).
 */
function word_rendering_symbols() {
	$rendering_symbols        = array(
		'asterisk'    => __( '[*] Asterisk', 'buddypress-profanity' ),
		'dollar'      => __( '[$] Dollar', 'buddypress-profanity' ),
		'question'    => __( '[?] Question', 'buddypress-profanity' ),
		'exclamation' => __( '[!] Exclamation', 'buddypress-profanity' ),
		'hyphen'      => __( '[-] Hyphen', 'buddypress-profanity' ),
		'hash'        => __( '[#] Hash', 'buddypress-profanity' ),
		'tilde'       => __( '[~] Tilde', 'buddypress-profanity' ),
		'blank'       => __( '[ ] Blank', 'buddypress-profanity' ),
	);
	return $rendering_symbols = apply_filters( 'wbbprof_word_rendering_symbols', $rendering_symbols );
}
