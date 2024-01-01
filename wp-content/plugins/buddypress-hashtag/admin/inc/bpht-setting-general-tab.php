<?php
/**
 *
 * This file is called for general settings section at admin settings.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( is_multisite() && is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
	$bpht_general_settings = get_site_option( 'bpht_general_settings' );
} else {
	$bpht_general_settings = get_option( 'bpht_general_settings' );
}

$an_enabled = bpht_alpha_numeric_hashtags_enabled();
if ( $an_enabled ) {
	$lengths_display_class = 'display:none';
} else {
	$lengths_display_class = '';
}

?>
<div class="wbcom-tab-content">
	<div class="wbcom-wrapper-admin">
		<div class="wbcom-admin-title-section wbcom-flex">
			<h3 class="wbcom-welcome-title"><?php esc_html_e( 'General Setting', 'buddypress-hashtags' ); ?></h3>
			<a href="<?php echo esc_url( 'https://wbcomdesigns.com/docs/buddypress-hashtags/getting-started-with-buddypress-hashtags/' ); ?>" class="wbcom-docslink" target="_blank"><?php esc_html_e( 'Documentation', 'buddypress-hashtags' ); ?></a>
		</div>
		<div class="wbcom-admin-option-wrap wbcom-admin-option-wrap-view">	
			<form method="post" action="options.php">
				<?php
				settings_fields( 'bpht_general_settings_section' );
				do_settings_sections( 'bpht_general_settings_section' );
				?>
				<div class="form-table">
					<div class="wbcom-settings-section-wrap">
						<div class="wbcom-settings-section-options-heading">
							<label for="blogname"><?php esc_html_e( 'Allow Unicode Characters', 'buddypress-hashtags' ); ?></label>
							<p class="description" id="tagline-description"><?php esc_html_e( 'Unicode encompasses a vast set of characters, including letters, numbers, symbols, and emojis from different languages and scripts.', 'buddypress-hashtags' ); ?></p>
							<p class="wb-warning"><?php esc_html_e( 'Note: The setting currently enabled does not allow for minimum and maximum hashtag length restrictions to be applied.', 'buddypress-hashtags' ); ?></p>
						</div>
						<div class="wbcom-settings-section-options">
							<label class="wb-switch">
								<input class="allow_non_an_ht" name="bpht_general_settings[allow_non_an_ht]" type="checkbox" value="yes" <?php ( isset( $bpht_general_settings['allow_non_an_ht'] ) ) ? checked( $bpht_general_settings['allow_non_an_ht'], 'yes' ) : ''; ?>>
								<div class="wb-slider wb-round"></div>
							</label>
						</div>
					</div>
					<div class="wbcom-settings-section-wrap bpht-lengths-row" style="<?php echo esc_attr( $lengths_display_class ); ?>" >
						<div class="wbcom-settings-section-options-heading">
							<label for="blogname"><?php esc_html_e( 'Minimum hashtag length', 'buddypress-hashtags' ); ?></label>
							<p class="description" id="tagline-description"><?php esc_html_e( 'A hashtag should be at least three to four characters for effectiveness and recognition.', 'buddypress-hashtags' ); ?></p>
						</div>
						<div class="wbcom-settings-section-options">
							<input name='bpht_general_settings[min_length]' type='number' min='3' class="regular-text" value='<?php echo ( isset( $bpht_general_settings['min_length'] ) && $bpht_general_settings['min_length'] ) ? esc_attr( $bpht_general_settings['min_length'] ) : '3'; ?>' placeholder="<?php esc_html_e( 'set minimum hashtag length', 'buddypress-hashtags' ); ?>" />
						</div>
					</div>
					<div class="wbcom-settings-section-wrap bpht-lengths-row" style="<?php echo esc_attr( $lengths_display_class ); ?>">
						<div class="wbcom-settings-section-options-heading">
							<label for="blogname"><?php esc_html_e( 'Maximum hashtag length', 'buddypress-hashtags' ); ?></label>
							<p class="description" id="tagline-description"><?php esc_html_e( 'When using hashtags, whether or not to limit their length depends on your goals.', 'buddypress-hashtags' ); ?></p>
						</div>
						<div class="wbcom-settings-section-options">							
							<input name='bpht_general_settings[max_length]' type='number' min='5' class="regular-text" value='<?php echo ( isset( $bpht_general_settings['max_length'] ) && $bpht_general_settings['max_length'] ) ? esc_attr( $bpht_general_settings['max_length'] ) : '16'; ?>' placeholder="<?php esc_html_e( 'set maximum hashtag length', 'buddypress-hashtags' ); ?>" />
						</div>
					</div>
					<?php if ( class_exists( 'Buddypress' ) ) { ?>
					<div class="wbcom-settings-section-wrap">
						<div class="wbcom-settings-section-options-heading">
							<label for="blogname"><?php esc_html_e( 'Delete Hashtag Index Table', 'buddypress-hashtags' ); ?></label>
							<p class="description" id="tagline-description"><?php esc_html_e( 'We store hashtag counts in a separate table. To reset the index table, use this option. It removes the hashtag counts without deleting activities.', 'buddypress-hashtags' ); ?></p>
						</div>
						<div class="wbcom-settings-section-options">
							<a href="javascript:void(0)" class="bpht-clear-bp-hashtags button button-primary wb-button-error"><?php esc_html_e( 'Reset Hashtag Index', 'buddypress-hashtags' ); ?></a>
						</div>
					</div>
					<?php } ?>
					<!-- <?php // if ( class_exists( 'bbPress' ) ) { ?>
					<div class="wbcom-settings-section-wrap">
						<div class="wbcom-settings-section-options-heading">
							<label for="blogname"><?php // esc_html_e( 'Clear bbpress widgets hashtags', 'buddypress-hashtags' ); ?></label>
							<p class="description" id="tagline-description"><?php // esc_html_e( 'This will only clear old hashtags from bbpress forum widget.', 'buddypress-hashtags' ); ?>
						</div>
						<div class="wbcom-settings-section-options">
							<a href="javascript:void(0)" class="bpht-clear-bbpress-hashtags button button-primary"><?php // esc_html_e( 'Clear', 'buddypress-hashtags' ); ?></a>
						</div>
					</div>
					<?php // } ?>
					<div class="wbcom-settings-section-wrap">
						<div class="wbcom-settings-section-options-heading">
							<label for="blogname"><?php // esc_html_e( 'Clear post widgets hashtags', 'buddypress-hashtags' ); ?></label>
							<p class="description" id="tagline-description"><?php // esc_html_e( 'This will only clear old hashtags from wp post hashtags widget.', 'buddypress-hashtags' ); ?></p>
						</div>
						<div class="wbcom-settings-section-options">
							<a href="javascript:void(0)" class="bpht-clear-post-hashtags button button-primary"><?php // esc_html_e( 'Clear', 'buddypress-hashtags' ); ?></a>
						</div>
					</div>
					<div class="wbcom-settings-section-wrap">
						<div class="wbcom-settings-section-options-heading">
							<label for="blogname"><?php // esc_html_e( 'Clear page widgets hashtags', 'buddypress-hashtags' ); ?></label>
							<p class="description" id="tagline-description"><?php // esc_html_e( 'This will only clear old hashtags from wp page hashtags widget.', 'buddypress-hashtags' ); ?></p>
						</div>
						<div class="wbcom-settings-section-options">
							<a href="javascript:void(0)" class="bpht-clear-page-hashtags button button-primary"><?php // esc_html_e( 'Clear', 'buddypress-hashtags' ); ?></a>
						</div>
					</div> -->
					<?php if ( class_exists( 'bbPress' ) ) { ?>
					<div class="wbcom-settings-section-wrap">
						<div class="wbcom-settings-section-options-heading">
							<label for="blogname"><?php esc_html_e( 'Disable Hashtag on bbPress?', 'buddypress-hashtags' ); ?></label>
							<p class="description" id="tagline-description"><?php esc_html_e( 'Turn off the hashtags link on topics and replies.', 'buddypress-hashtags' ); ?></p>
						</div>
						<div class="wbcom-settings-section-options">
							<label class="wb-switch">
								<input class="disable_on_bbpress" name="bpht_general_settings[disable_on_bbpress]" type="checkbox" value="yes" <?php ( isset( $bpht_general_settings['disable_on_bbpress'] ) ) ? checked( $bpht_general_settings['disable_on_bbpress'], 'yes' ) : ''; ?>>
								<div class="wb-slider wb-round"></div>
							</label>
						</div>
					</div>
					<?php } ?>
					<div class="wbcom-settings-section-wrap">
						<div class="wbcom-settings-section-options-heading">
							<label for="blogname"><?php esc_html_e( 'Disable Hashtag on Blog Posts?', 'buddypress-hashtags' ); ?></label>
							<p class="description" id="tagline-description"><?php esc_html_e( 'Turn off the hashtags link on posts and pages.', 'buddypress-hashtags' ); ?></p>
						</div>
						<div class="wbcom-settings-section-options">
							<label class="wb-switch">
								<input class="disable_on_blog_posts" name="bpht_general_settings[disable_on_blog_posts]" type="checkbox" value="yes" <?php ( isset( $bpht_general_settings['disable_on_blog_posts'] ) ) ? checked( $bpht_general_settings['disable_on_blog_posts'], 'yes' ) : ''; ?>>
								<div class="wb-slider wb-round"></div>
							</label>
						</div>
					</div>
					<?php if ( class_exists( 'Buddypress' ) ) { ?>
					<div class="wbcom-settings-section-wrap">
						<div class="wbcom-settings-section-options-heading">
							<label for="blogname"><?php esc_html_e( 'Disable Follow Hashtags', 'buddypress-hashtags' ); ?></label>
							<p class="description"><?php _e( 'When you follow a hashtag, you will see activities containing that hashtag in your feed, even if they are from accounts you don\'t follow. This feature can help you discover new content and engage with people who share your interests. You can allow your members to follow hashtags from <a href="' . esc_url( get_site_url() . '/members/me/settings/hashtags/' ) . '">https://domain.com/members/me/settings/hashtags/</a> that interest them.', 'buddypress-hashtags' ); ?></p>
						</div>
						<div class="wbcom-settings-section-options">
							<label class="wb-switch">
								<input class="disable-follow-hashtag" name="bpht_general_settings[disable_follow_hashtag]" type="checkbox" value="yes" <?php ( isset( $bpht_general_settings['disable_follow_hashtag'] ) ) ? checked( $bpht_general_settings['disable_follow_hashtag'], 'yes' ) : ''; ?>>
								<div class="wb-slider wb-round"></div>
							</label>
						</div>
					</div>
					<?php } ?>				
				</div>
				<?php submit_button(); ?>
			</form>	
		</div>
	</div>
</div>
