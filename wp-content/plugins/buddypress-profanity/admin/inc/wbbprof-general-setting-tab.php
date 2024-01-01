<?php
/**
 * This file is used for rendering and saving plugin general settings.
 *
 * @link       http://www.wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    Buddypress_Profanity
 * @subpackage Buddypress_Profanity/inc
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wbbprof_settings  = bp_get_option( 'wbbprof_settings' );
$content_to_filter = content_to_filter_array();
$rendering_symbols = word_rendering_symbols();
?>
<div class="wbcom-tab-content">
	<div class="wbcom-wrapper-admin">
		<div class="wbcom-admin-title-section">
			<h3><?php esc_html_e( 'General Profanity', 'buddypress-profanity' ); ?></h3>
		</div>
		<div class="wbcom-admin-option-wrap wbcom-admin-option-wrap-view">
			<form method="post" action="admin.php?action=update_network_options">
					<?php
					settings_fields( 'buddypress_profanity_general' );
					do_settings_sections( 'buddypress_profanity_general' );
					?>
					<div class="form-table buddypress-profanity-admin-table">
						<div class="wbcom-settings-section-wrap">
							<div class="wbcom-settings-section-options-heading">
								<label for="blogname"><?php esc_html_e( 'Keywords to remove', 'buddypress-profanity' ); ?></label>
								<p class="description" id="tagline-description">
									<?php esc_html_e( 'Enter keywords here which you want to remove from community.', 'buddypress-profanity' ); ?>
								</p>
								<p>
									<a href="javascript:void(0)" class="button" id="wbbprof_to_reset"><?php esc_html_e( 'Reset to default', 'buddypress-profanity' ); ?></a>
								</p>
							</div>
							<div class="wbcom-selectize-control-wrap wbcom-settings-section-options">
								<input name='wbbprof_settings[keywords]' type='text' class="regular-text wbbprof-keywords-text" value='<?php echo isset( $wbbprof_settings['keywords'] ) ? esc_attr( $wbbprof_settings['keywords'] ) : ''; ?>' placeholder="<?php esc_html_e( 'Keywords to remove', 'buddypress-profanity' ); ?>" />
							</div>
						</div>
					</div>
					<div class="form-table">
						<div class="wbcom-settings-section-wrap">
							<div class="wbcom-settings-section-options-heading">
								<label for="blogname"><?php esc_html_e( 'Content to be fitered', 'buddypress-profanity' ); ?></label>
							</div>
							<div class="wbcom-settings-section-options">
								<legend class="screen-reader-text"><span><?php esc_html_e( 'Content to be fitered', 'buddypress-profanity' ); ?></span></legend>
								<ul class="wbcom-settings-member-retraction wbcom-settings-section-options-flex">
									<?php
									foreach ( $content_to_filter as $key => $value ) {
										if ( isset( $wbbprof_settings['filter_contents'] ) && in_array( $key, $wbbprof_settings['filter_contents'] ) ) { //phpcs:ignore
											$checked = 'checked';
										} else {
											$checked = '';
										}
										?>
										<li>
											<label class="wb-switch">
												<input name="wbbprof_settings[filter_contents][]" value="<?php echo esc_attr( $key );?>" type="checkbox" . <?php echo esc_attr( $checked );?>>
												<div class="wb-slider wb-round"></div>
											</label>
											<label class="wbbprof-span-text wbbprof-chkbox-txt" for="bp_create_post_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></label>
										</li>
										<?php
									}
									?>
								</ul>	
							</div>
						</div>
					</div>
					<div class="form-table">
						<div class="wbcom-settings-section-wrap">
							<div class="wbcom-settings-section-options-heading">
								<label for="blogname"><?php esc_html_e( 'Word rendering', 'buddypress-profanity' ); ?></label>						
							</div>
							<div class="wbcom-settings-section-options">
							<fieldset>
								<legend class="screen-reader-text"><span><?php esc_html_e( 'Word rendering', 'buddypress-profanity' ); ?></span></legend>
								<label>
									<input name="wbbprof_settings[word_render]" value="first" type="radio" <?php ( isset( $wbbprof_settings['word_render'] ) ) ? checked( $wbbprof_settings['word_render'], 'first' ) : ''; ?>>
									<span class="wbbprof-span-text"><?php esc_html_e( 'First letter retainded', 'buddypress-profanity' ); ?></span>
									<code>[blog => b***]</code>
								</label>
								<br>
								<label>
									<input name="wbbprof_settings[word_render]" value="last" type="radio" <?php ( isset( $wbbprof_settings['word_render'] ) ) ? checked( $wbbprof_settings['word_render'], 'last' ) : ''; ?>>
									<span class="wbbprof-span-text"><?php esc_html_e( 'Last letter retained', 'buddypress-profanity' ); ?></span>
									<code>[blog => ***g]</code>
								</label>
								<br>
								<label>
									<?php $first_last = ( isset( $wbbprof_settings['word_render'] ) && 'fisrt_last' == $wbbprof_settings['word_render'] ) ? 'fisrt_last' : 'first_last'; ?>
									<input name="wbbprof_settings[word_render]" value="first_last" type="radio" <?php ( isset( $wbbprof_settings['word_render'] ) ) ? checked( $wbbprof_settings['word_render'], $first_last ) : ''; ?>>
									<span class="wbbprof-span-text"><?php esc_html_e( 'First and Last letter retained', 'buddypress-profanity' ); ?></span>
									<code>[blog => b**g]</code>
								</label>
								<br>
								<label>
									<input name="wbbprof_settings[word_render]" value="all" type="radio" <?php ( isset( $wbbprof_settings['word_render'] ) ) ? checked( $wbbprof_settings['word_render'], 'all' ) : ''; ?>>
									<span class="wbbprof-span-text"><?php esc_html_e( 'All letter removed', 'buddypress-profanity' ); ?></span>
									<code>[blog => ****]</code>
								</label>
								<br>
							</fieldset>
							</div>
						</div>
					</div>
					<div class="form-table">
						<div class="wbcom-settings-section-wrap">
							<div class="wbcom-settings-section-options-heading">
								<label for="blogname"><?php esc_html_e( 'Filter character', 'buddypress-profanity' ); ?></label>								
							</div>
							<div class="wbcom-settings-section-options">
							<select name="wbbprof_settings[character]">
								<?php
								foreach ( $rendering_symbols as $key => $value ) {

									if ( isset( $wbbprof_settings['character'] ) && $wbbprof_settings['character'] == $key ) {
										$selected = 'selected';
									} else {
										$selected = '';
									}
									echo "<option value='" . esc_attr( $key ) . "' " . esc_attr( $selected ) . '>' . esc_html( $value ) . '</option>';
								}
								?>
							</select>
							</div>
						</div>
					</div>
					<div class="form-table">
						<div class="wbcom-settings-section-wrap">
							<div class="wbcom-settings-section-options-heading">
								<label for="blogname"><?php esc_html_e( 'Case matching', 'buddypress-profanity' ); ?></label>
								<p class="description" id="tagline-description"><?php esc_html_e( 'Note: Case Insensitive matching type is better as it captures more words!', 'buddypress-profanity' ); ?></p>
							</div>
							<div class="wbcom-settings-section-options">
								<fieldset>
									<legend class="screen-reader-text"><span><?php esc_html_e( 'Case matching', 'buddypress-profanity' ); ?></span></legend>
									<label>
										<input name="wbbprof_settings[case]" value="case" type="radio" <?php ( isset( $wbbprof_settings['case'] ) ) ? checked( $wbbprof_settings['case'], 'case' ) : ''; ?>>
										<span class="wbbprof-span-text"><?php esc_html_e( 'Case Sensitive', 'buddypress-profanity' ); ?></span>
									</label>
									<br>
									<label>
										<input name="wbbprof_settings[case]" value="incase" type="radio" <?php ( isset( $wbbprof_settings['case'] ) ) ? checked( $wbbprof_settings['case'], 'incase' ) : ''; ?>>
										<span class="wbbprof-span-text"><?php esc_html_e( 'Case Insensitive (recommended)', 'buddypress-profanity' ); ?></span>
									</label>
									<br>
								</fieldset>
							</div>
						</div>
					</div>
					<div class="form-table">
						<div class="wbcom-settings-section-wrap">
							<div class="wbcom-settings-section-options-heading">
								<label for="blogname"><?php esc_html_e( 'Strict filtering', 'buddypress-profanity' ); ?></label>
								<p class="description" id="tagline-description"><?php esc_html_e( 'Note: When strict filtering is ON, embedded keywords are filtered.', 'buddypress-profanity' ); ?></p>
							</div>
							<div class="wbcom-settings-section-options">
								<fieldset>
									<legend class="screen-reader-text"><span><?php esc_html_e( 'Strict filtering', 'buddypress-profanity' ); ?></span></legend>
									<label>
										<input name="wbbprof_settings[strict_filter]" value="on" type="radio" <?php ( isset( $wbbprof_settings['strict_filter'] ) ) ? checked( $wbbprof_settings['strict_filter'], 'on' ) : ''; ?>>
										<span class="wbbprof-case-span-text"><?php esc_html_e( 'Strict Filtering OFF', 'buddypress-profanity' ); ?></span>					
										<code>[eg. ass becomes passable]</code>
									</label>
									<br>
									<label>
										<input name="wbbprof_settings[strict_filter]" value="off" type="radio" <?php ( isset( $wbbprof_settings['strict_filter'] ) ) ? checked( $wbbprof_settings['strict_filter'], 'off' ) : ''; ?>>
										<span class="wbbprof-case-span-text"><?php esc_html_e( 'Strict Filtering ON (recommended)', 'buddypress-profanity' ); ?></span>
										<code>[eg. ass becomes p***able]</code>
									</label>
									<br>
								</fieldset>
							</div>
						</div>
					</div>
					<?php submit_button(); ?>
				</div>
			</form>
	</div>
</div>