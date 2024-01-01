<?php
/**
 * This file is used for rendering and saving plugin general settings.
 *
 * @package    Buddypress_Polls
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( is_multisite() && is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
	$bpolls_settings = get_site_option( 'wbpolls_settings' );
} else {
	$bpolls_settings = get_site_option( 'wbpolls_settings' );
}
global $wp_roles;
?>
<div class="wbcom-tab-content">
	<div class="wbcom-admin-title-section">
		<h3 style="margin: 0 0 5px"><?php esc_html_e( 'Poll Setting', 'buddypress-polls' ); ?></h3>
		<p class="description"><?php esc_html_e( 'This feature lets you create polls as a post type that works independently of BuddyPress Activity Polls. You can easily list and use the polls as a shortcode on any content section.', 'buddypress-polls' ); ?></p>
	</div>
	<div class="wbcom-admin-option-wrap wbcom-admin-option-wrap-view">		
		<form method="post" action="admin.php?action=update_network_options">
			<div class="wbcom-wrapper-admin">				
				<div class="wbcom-admin-option-wrap-bp-poll">
					<input name='wbpolls_settings[hidden]' type='hidden' value="" />
					<?php
					settings_fields( 'buddypress_wbpolls' );
						do_settings_sections( 'buddypress_wbpolls' );
					?>
					<div class="form-table polls-general-options">

						<div class="wbcom-settings-section-wrap">
							<div class="wbcom-settings-section-options-heading">
								<label for="blogname"><?php esc_html_e( 'Poll Page Mapping', 'buddypress-polls' ); ?></label>
							</div>
							<div class="wbcom-settings-section-options">
								<div class="wbcom-settings-section-options-heading-poll">
									<span><?php esc_html_e( 'Create Poll Page', 'buddypress-polls' ); ?></span>
								</div>
								
								<select name="wbpolls_settings[create_poll_page]">
									<option value=""><?php esc_html_e( '- None -', 'buddypress-polls' )?></option>
									<?php
									$pages = get_pages();
									foreach ( $pages as $page ) {
										?>
									<option value="<?php echo esc_attr( $page->ID ); ?>" <?php if(isset($bpolls_settings['create_poll_page']) && $bpolls_settings['create_poll_page'] == $page->ID){ echo "selected"; } ?>><?php echo esc_attr( $page->post_title ); ?></option>
									<?php } ?>
								</select>
								
								<?php if ( isset($bpolls_settings['create_poll_page']) && get_post( $bpolls_settings['create_poll_page'] ) ) : ?>

									<a href="<?php echo esc_url( get_permalink( $bpolls_settings['create_poll_page'] ) ); ?>" class="button-secondary" target="_bp">
										<?php esc_html_e( 'View', 'buddypress-polls' ); ?> <span class="dashicons dashicons-external" aria-hidden="true"></span>
										<span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'buddypress-polls' ); ?></span>
									</a>

								<?php endif; ?>

								<div class="wbcom-settings-section-options-heading-poll">
									<span><?php esc_html_e( 'Poll Dashboard Page', 'buddypress-polls' ); ?></span>
								</div>
								<select name="wbpolls_settings[poll_dashboard_page]">
									<option value=""><?php esc_html_e( '- None -', 'buddypress-polls' )?></option>
									<?php
									$pages = get_pages();
									foreach ( $pages as $dpage ) {										
										?>
									<option value="<?php echo esc_attr( $dpage->ID ); ?>" <?php if(isset($bpolls_settings['poll_dashboard_page']) && $bpolls_settings['poll_dashboard_page'] == $dpage->ID){ echo "selected"; } ?>><?php echo esc_attr( $dpage->post_title ); ?></option>
									<?php } ?>
								</select>
								
								<?php if ( isset($bpolls_settings['poll_dashboard_page']) && get_post( $bpolls_settings['poll_dashboard_page'] ) ) : ?>

									<a href="<?php echo esc_url( get_permalink( $bpolls_settings['poll_dashboard_page'] ) ); ?>" class="button-secondary" target="_bp">
										<?php esc_html_e( 'View', 'buddypress-polls' ); ?> <span class="dashicons dashicons-external" aria-hidden="true"></span>
										<span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'buddypress-polls' ); ?></span>
									</a>

								<?php endif; ?>
							</div>
						</div>		
						
						<div class="wbcom-settings-section-wrap" id="bpolls_user_role" >
							<div class="wbcom-settings-section-options-heading">
								<label><?php esc_html_e( 'Who can create a poll?', 'buddypress-polls' ); ?></label>
								<p class="description">
									<?php esc_html_e( 'Select the user roles that can create polls.', 'buddypress-polls' ); ?>
								</p>
							</div>
							<div class="wbcom-settings-section-options">
								<select class="polls-multi-selectize" name="wbpolls_settings[wppolls_create_poll][]" multiple>
									<?php
									$roles = $wp_roles->get_names();
									foreach ( $roles as $role => $rname ) {
										$selected = ( ! empty( $bpolls_settings['wppolls_create_poll'] ) && in_array( $role, $bpolls_settings['wppolls_create_poll'], true ) ) ? 'selected' : '';
										?>
									<option value="<?php echo esc_attr( $role ); ?>" <?php echo esc_attr( $selected ); ?>><?php echo esc_attr( $rname ); ?></option>
									<?php } ?>
								</select>				
							</div>		
						</div>						
						
						<div class="wbcom-settings-section-wrap">
							<div class="wbcom-settings-section-options-heading">
								<label
									for="blogname"><?php esc_html_e( 'Allow users to create poll options.', 'buddypress-polls' ); ?></label>
								<p class="description" id="tagline-description">
									<?php esc_html_e( 'This option will allow the users to create poll options. (Valid only for Text Poll type)', 'buddypress-polls' ); ?>
								</p>
							</div>
							<div class="wbcom-settings-section-options">
								<label class="wb-switch">
									<input name='wbpolls_settings[wbpolls_user_add_extra_op]' type='checkbox' value='yes'
										<?php ( isset( $bpolls_settings['wbpolls_user_add_extra_op'] ) ) ? checked( $bpolls_settings['wbpolls_user_add_extra_op'], 'yes' ) : ''; ?> />
									<div class="wb-slider wb-round"></div>
								</label>
							</div>
						</div>

						<div class="wbcom-settings-section-wrap">
							<div class="wbcom-settings-section-options-heading">
								<label
									for="blogname"><?php esc_html_e( 'Status of submitted poll', 'buddypress-polls' ); ?></label>
								<p class="description" id="tagline-description">
									<?php esc_html_e( 'Set the status of the newly submitted poll.', 'buddypress-polls' ); ?>
								</p>
							</div>
							<div class="wbcom-settings-section-options">
								<select name="wbpolls_settings[wbpolls_submit_status]">
									<option value="pending" 
									<?php
									if ( isset($bpolls_settings['wbpolls_submit_status']) && $bpolls_settings['wbpolls_submit_status'] == 'pending' ) {
										echo 'selected'; }
									?>
									><?php esc_html_e( 'Pending Review', 'buddypress-polls' ); ?></option>
									<option value="publish" 
									<?php
									if ( isset($bpolls_settings['wbpolls_submit_status']) && $bpolls_settings['wbpolls_submit_status'] == 'publish' ) {
										echo 'selected'; }
									?>
									><?php esc_html_e( 'Publish', 'buddypress-polls' ); ?></option>
								</select>
							</div>
						</div>

						<div class="wbcom-settings-section-wrap">
							<div class="wbcom-settings-section-options-heading">
								<label
									for="blogname"><?php esc_html_e( 'Show/Hide Poll Results', 'buddypress-polls' ); ?></label>
								<p class="description" id="tagline-description">
									<?php esc_html_e( 'Users can only see the poll results after voting.', 'buddypress-polls' ); ?>
								</p>
							</div>
							<div class="wbcom-settings-section-options">
								<label class="wb-switch">
									<input name='wbpolls_settings[wppolls_show_result]' type='checkbox' value='yes'
										<?php ( isset( $bpolls_settings['wppolls_show_result'] ) ) ? checked( $bpolls_settings['wppolls_show_result'], 'yes' ) : ''; ?> />
									<div class="wb-slider wb-round"></div>
								</label>
							</div>
						</div>

						<div class="wbcom-settings-section-wrap">
							<div class="wbcom-settings-section-options-heading">
								<label
									for="blogname"><?php esc_html_e( 'Enable Poll Comment', 'buddypress-polls' ); ?></label>
								<p class="description" id="tagline-description">
									<?php esc_html_e( 'To enable the comment feature on the polls, please select this option.', 'buddypress-polls' ); ?>
								</p>
							</div>
							<div class="wbcom-settings-section-options">
								<label class="wb-switch">
									<input name='wbpolls_settings[wppolls_show_comment]' type='checkbox' value='yes'
										<?php ( isset( $bpolls_settings['wppolls_show_comment'] ) ) ? checked( $bpolls_settings['wppolls_show_comment'], 'yes' ) : ''; ?> />
									<div class="wb-slider wb-round"></div>
								</label>
							</div>
						</div>
						
						<div class="wbcom-settings-section-wrap">
							<div class="wbcom-settings-section-options-heading">
								<label for="blogname"><?php esc_html_e( 'Who can vote?', 'buddypress-polls' ); ?></label>
								<p class="description" id="tagline-description">
									<?php esc_html_e( 'Select the user roles who can cast votes on polls.', 'buddypress-polls' ); ?>
								</p>
							</div>
							<div class="wbcom-settings-section-options">
								<select class="polls-multi-selectize" name="wbpolls_settings[wppolls_who_can_vote][]" multiple>
									<?php
									$roles = $wp_roles->get_names();
									$roles['guest'] = esc_html__( 'Guest', 'buddypress-polls' );									
									foreach ( $roles as $role => $rname ) {
										$selected = ( ! empty( $bpolls_settings['wppolls_who_can_vote'] ) && in_array( $role, $bpolls_settings['wppolls_who_can_vote'], true ) ) ? 'selected' : '';
										?>
									<option value="<?php echo esc_attr( $role ); ?>" <?php echo esc_attr( $selected ); ?>><?php echo esc_attr( $rname ); ?></option>
									<?php } ?>
								</select>				
							</div>
						</div>
						
					</div>
				</div>
			</div>

			<div class="wbcom-wrapper-admin">
				<div class="wbcom-admin-option-wrap-bp-poll">
						<div class="wbcom-settings-section-wrap">
							<div class="wbcom-settings-section-options-heading">
								<label for="blogname"><?php esc_html_e( 'Color Scheme', 'buddypress-polls' ); ?></label>
								<p class="description" id="tagline-description">
									<?php esc_html_e( 'Please specify the color scheme for the progress bar in the poll choices and the submit button.', 'buddypress-polls' ); ?>
								</p>
							</div>
							<div class="wbcom-settings-section-options">
								<label for>
									<input id="polls_background_color" name='wbpolls_settings[wbpolls_background_color]'
										type='text'
										value='<?php echo isset( $bpolls_settings['wbpolls_background_color'] ) ? esc_attr( $bpolls_settings['wbpolls_background_color'] ) : '#4caf50'; ?>' />
								</label>
							</div>
						</div>
				</div>
			</div>
				<?php submit_button(); ?>
		</form>
	</div>
</div>
