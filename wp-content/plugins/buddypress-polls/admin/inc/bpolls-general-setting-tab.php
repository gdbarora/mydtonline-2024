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
if ( class_exists( 'Buddypress' ) ) {
	if ( is_multisite() && is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
		$bpolls_settings = get_site_option( 'bpolls_settings' );
	} else {
		$bpolls_settings = get_site_option( 'bpolls_settings' );
	}
	if ( ! isset( $bpolls_settings['limit_poll_activity'] ) ) {
		$bpolls_settings['limit_poll_activity'] = 'no';
	}

	global $wp_roles;
	?>
	<div class="wbcom-tab-content">
	<div class="wbcom-wrapper-admin">
	<div class="wbcom-admin-title-section"><h3 style="margin: 0 0 5px"><?php esc_html_e( 'Activity Polls Setting', 'buddypress-polls' ); ?></h3>
	<p class="description"><?php esc_html_e( 'Activity polls are a BuddyPress and BuddyBoss Platform specific feature that allows members to create polls as activities. These polls are visible on their newsfeed, and can be added from the "Whats new" area.', 'buddypress-polls' ); ?></p>
	</div>
	<div class="wbcom-admin-option-wrap wbcom-admin-option-wrap-view">
	<form method="post" action="admin.php?action=update_network_options">
		<div class="wbcom-admin-option-wrap-bp-poll">
		<input name='bpolls_settings[hidden]' type='hidden' value=""/>
		<?php
		settings_fields( 'buddypress_polls_general' );
		do_settings_sections( 'buddypress_polls_general' );
		?>
		<div class="form-table polls-general-options">
			<div class="wbcom-settings-section-wrap">
				<div class="wbcom-settings-section-options-heading">
					<label for="blogname"><?php esc_html_e( 'Turn off the Add Poll feature for BuddyPress', 'buddypress-polls' ); ?></label>
					<p class="description" id="tagline-description"><?php esc_html_e( 'Enable this setting to hide the add poll icon on the Whats new activity section.', 'buddypress-polls' ); ?>
					</p>
				</div>
				<div class="wbcom-settings-section-options">
					<label class="wb-switch">
						<input name='bpolls_settings[hide_poll_icon]' type='checkbox' value='yes' <?php ( isset( $bpolls_settings['hide_poll_icon'] ) ) ? checked( $bpolls_settings['hide_poll_icon'], 'yes' ) : ''; ?>/>
						<div class="wb-slider wb-round"></div>
					</label>
				</div>
			</div>
			<div class="wbcom-settings-section-wrap">
				<div class="wbcom-settings-section-options-heading">
					<label for="blogname"><?php esc_html_e( 'Multi-select Polls', 'buddypress-polls' ); ?></label>
					<p class="description" id="tagline-description"><?php esc_html_e( 'In the polls, users can vote for multiple options, together or separately.', 'buddypress-polls' ); ?>
					</p>
				</div>
				<div class="wbcom-settings-section-options">
					<label class="wb-switch">
						<input name='bpolls_settings[multiselect]' type='checkbox' value='yes' <?php ( isset( $bpolls_settings['multiselect'] ) ) ? checked( $bpolls_settings['multiselect'], 'yes' ) : ''; ?>/>
						<div class="wb-slider wb-round"></div>
					</label>
				</div>
			</div>
			<div class="wbcom-settings-section-wrap">
				<div class="wbcom-settings-section-options-heading">
					<label for="blogname"><?php esc_html_e( 'Allow members to add poll options', 'buddypress-polls' ); ?></label>
					<p class="description" id="tagline-description"><?php esc_html_e( 'Members can contribute their options to the poll that already exists.', 'buddypress-polls' ); ?>
					</p>
				</div>
				<div class="wbcom-settings-section-options">
					<label class="wb-switch">
						<input name='bpolls_settings[user_additional_option]' type='checkbox' value='yes' <?php ( isset( $bpolls_settings['user_additional_option'] ) ) ? checked( $bpolls_settings['user_additional_option'], 'yes' ) : ''; ?>/>
						<div class="wb-slider wb-round"></div>
					</label>												
				</div>		
			</div>

			<div class="wbcom-settings-section-wrap">
				<div class="wbcom-settings-section-options-heading">
					<label for="blogname"><?php esc_html_e( 'Hide Poll results', 'buddypress-polls' ); ?></label>
					<p class="description" id="tagline-description"><?php esc_html_e( 'Users can only see the poll results after voting.', 'buddypress-polls' ); ?>
					</p>
				</div>
				<div class="wbcom-settings-section-options">
				<label class="wb-switch">
					<input name='bpolls_settings[hide_results]' type='checkbox' value='yes' <?php ( isset( $bpolls_settings['hide_results'] ) ) ? checked( $bpolls_settings['hide_results'], 'yes' ) : ''; ?>/>
					<div class="wb-slider wb-round"></div>
				</label>			
				</div>
				</div>

				<div class="wbcom-settings-section-wrap">
				<div class="wbcom-settings-section-options-heading">
					<label for="blogname"><?php esc_html_e( 'Poll closing date & time', 'buddypress-polls' ); ?></label>
					<p class="description" id="tagline-description"><?php esc_html_e( 'Users can now set the closing date and time for polls.', 'buddypress-polls' ); ?>
				</p>
				</div>
				<div class="wbcom-settings-section-options">
				<label class="wb-switch">
					<input name='bpolls_settings[close_date]' type='checkbox' value='yes' <?php ( isset( $bpolls_settings['close_date'] ) ) ? checked( $bpolls_settings['close_date'], 'yes' ) : ''; ?>/>
					<div class="wb-slider wb-round"></div>
				</label>			
				</div>		
			</div>

			<div class="wbcom-settings-section-wrap">
				<div class="wbcom-settings-section-options-heading">
					<label for="blogname"><?php esc_html_e( 'Enable image attachment', 'buddypress-polls' ); ?></label>
					<p class="description" id="tagline-description"><?php esc_html_e( 'Users are now able to attach an image when creating a poll.', 'buddypress-polls' ); ?>
				</p>
				</div>
				<div class="wbcom-settings-section-options">
				<label class="wb-switch">
					<input name='bpolls_settings[enable_image]' type='checkbox' value='yes' <?php ( isset( $bpolls_settings['enable_image'] ) ) ? checked( $bpolls_settings['enable_image'], 'yes' ) : ''; ?>/>
					<div class="wb-slider wb-round"></div>
				</label>			
				</div>
			</div>

			<div class="wbcom-settings-section-wrap">
				<div class="wbcom-settings-section-options-heading">
					<label for="options_limit"><?php esc_html_e( 'Poll Options Limit', 'buddypress-polls' ); ?></label>
					<p class="description" id="tagline-description"><?php esc_html_e( 'Please indicate the maximum number of options for the poll.', 'buddypress-polls' ); ?></p>
				</div>
				<div class="wbcom-settings-section-options">
				<input id="options_limit" name='bpolls_settings[options_limit]' type='number' value='<?php echo ( isset( $bpolls_settings['options_limit'] ) ) ? esc_attr( $bpolls_settings['options_limit'] ) : '5'; ?>'/>			
				</div>
			</div>

			<div class="wbcom-settings-section-wrap">
				<div class="wbcom-settings-section-options-heading">
					<label for="poll_options_result"><?php esc_html_e( 'Display Poll Result %', 'buddypress-polls' ); ?></label>
					<p class="description" id="tagline-description"><?php esc_html_e( 'After the poll, you can see the results in percentage format.', 'buddypress-polls' ); ?></p>
				</div>
				<div class="wbcom-settings-section-options">
				<label class="wb-switch">
					<input id="poll_options_result" name='bpolls_settings[poll_options_result]' type='checkbox' value='yes' <?php ( isset( $bpolls_settings['poll_options_result'] ) ) ? checked( $bpolls_settings['poll_options_result'], 'yes' ) : ''; ?>/>
					<div class="wb-slider wb-round"></div>
				</label>			
				</div>
			</div>

			<div class="wbcom-settings-section-wrap">
				<div class="wbcom-settings-section-options-heading">
					<label for="poll_revoting"><?php esc_html_e( 'Enable Revoting', 'buddypress-polls' ); ?></label>
					<p class="description" id="tagline-description"><?php esc_html_e( 'Allow members to modify their vote after submitting it.', 'buddypress-polls' ); ?></p>
				</div>
				<div class="wbcom-settings-section-options">
				<label class="wb-switch">
					<input id="poll_revoting" name='bpolls_settings[poll_revoting]' type='checkbox' value='yes' <?php ( isset( $bpolls_settings['poll_revoting'] ) ) ? checked( $bpolls_settings['poll_revoting'], 'yes' ) : ''; ?>/>
					<div class="wb-slider wb-round"></div>
				</label>			
				</div>
			</div>

			<div class="wbcom-settings-section-wrap">
				<div class="wbcom-settings-section-options-heading">
					<label for="poll_list_voters"><?php esc_html_e( 'Display Voters list', 'buddypress-polls' ); ?></label>
					<p class="description" id="tagline-description"><?php esc_html_e( 'The feature is enabled to allow members to view the list of voters.', 'buddypress-polls' ); ?></p>
				</div>
				<div class="wbcom-settings-section-options">
				<label class="wb-switch">
					<input id="poll_list_voters" name='bpolls_settings[poll_list_voters]' type='checkbox' value='yes' <?php ( isset( $bpolls_settings['poll_list_voters'] ) ) ? checked( $bpolls_settings['poll_list_voters'], 'yes' ) : ''; ?>/>
					<div class="wb-slider wb-round"></div>
				</label>			
				</div>
			</div>

			<div class="wbcom-settings-section-wrap" id="poll_limit_voters_options" <?php echo ! isset( $bpolls_settings['poll_list_voters'] )  ? 'style="display:none"' : '';?>>
				<div class="wbcom-settings-section-options-heading">
					<label for="poll_limit_voters"><?php esc_html_e( 'How many voters will be visible?', 'buddypress-polls' ); ?></label>
					<p class="description" id="tagline-description"><?php esc_html_e( 'The maximum number of voters to be displayed on the poll choices after votes are cast.', 'buddypress-polls' ); ?></p>
				</div>
				<div class="wbcom-settings-section-options">
				<input id="poll_limit_voters" name='bpolls_settings[poll_limit_voters]' type='number' value='<?php echo ( isset( $bpolls_settings['poll_limit_voters'] ) ) ? esc_attr( $bpolls_settings['poll_limit_voters'] ) : '3'; ?>'/>
				</div>
			</div>

			<div class="wbcom-settings-section-wrap">
				<div class="wbcom-settings-section-options-heading">
					<label for="blogname"><?php esc_html_e( 'Color Scheme', 'buddypress-polls' ); ?></label>
					<p class="description" id="tagline-description"><?php esc_html_e( 'Please specify the color scheme for the progress bar in the poll choices and the submit button.', 'buddypress-polls' ); ?></p>
				</div>
				<div class="wbcom-settings-section-options">
				<label for>
					<input id="polls_background_color" name='bpolls_settings[polls_background_color]' type='text' value='<?php echo isset( $bpolls_settings['polls_background_color'] ) ? esc_attr( $bpolls_settings['polls_background_color'] ) : '#4caf50'; ?>' />
				</label>			
				</div>
			</div>

			<div class="wbcom-settings-section-wrap">
				<div class="wbcom-settings-section-options-heading">
					<label for="blogname"><?php esc_html_e( 'Enable after-poll message', 'buddypress-polls' ); ?></label>
					<p class="description" id="tagline-description"><?php esc_html_e( 'Users can now include a personalized thank you message in their poll.', 'buddypress-polls' ); ?></p>
				</div>
				<div class="wbcom-settings-section-options">
				<label class="wb-switch">
					<input name='bpolls_settings[enable_thank_you_message]' type='checkbox' value='yes' <?php ( isset( $bpolls_settings['enable_thank_you_message'] ) ) ? checked( $bpolls_settings['enable_thank_you_message'], 'yes' ) : ''; ?>/>
					<div class="wb-slider wb-round"></div>
				</label>			
				</div>
			</div>

			<div class="wbcom-settings-section-wrap">
				<div class="wbcom-settings-section-options-heading">
					<label><?php esc_html_e( 'Add Poll Restrictions', 'buddypress-polls' ); ?></label>
					<p class="description">
						<?php esc_html_e( 'Enable specific user roles or member types to create new polls.', 'buddypress-polls' ); ?>
					</p>
				</div>
				<div class="wbcom-settings-section-options">
					<label>
					<input name='bpolls_settings[limit_poll_activity]' type='radio' value='no' <?php ( isset( $bpolls_settings['limit_poll_activity'] ) ) ? checked( $bpolls_settings['limit_poll_activity'], 'no' ) : ''; ?>/>&nbsp; <?php esc_html_e( 'No Limit', 'buddypress-polls' ); ?>
					</label>
					<label>
					<input name='bpolls_settings[limit_poll_activity]' type='radio' value='user_role' <?php ( isset( $bpolls_settings['limit_poll_activity'] ) ) ? checked( $bpolls_settings['limit_poll_activity'], 'user_role' ) : ''; ?>/>&nbsp; <?php esc_html_e( 'Limit by User Role', 'buddypress-polls' ); ?>
					</label>
					<label>
					<input name='bpolls_settings[limit_poll_activity]' type='radio' value='member_type' <?php ( isset( $bpolls_settings['limit_poll_activity'] ) ) ? checked( $bpolls_settings['limit_poll_activity'], 'member_type' ) : ''; ?>/>&nbsp; <?php esc_html_e( 'Limit by Member Type', 'buddypress-polls' ); ?>
					</label>				
				</div>
			</div>

			<div class="wbcom-settings-section-wrap" id="bpolls_user_role" 
			<?php
			if ( isset( $bpolls_settings['limit_poll_activity'] ) && 'user_role' !== $bpolls_settings['limit_poll_activity'] ) :
				?>
				style="display:none" <?php endif; ?>>
				<div class="wbcom-settings-section-options-heading">
					<label><?php esc_html_e( 'Select User Roles', 'buddypress-polls' ); ?></label>
					<p class="description">
						<?php esc_html_e( 'Select user role which are allowed to publish poll type activities.', 'buddypress-polls' ); ?>
					</p>
				</div>
				<div class="wbcom-settings-section-options">
					<select class="polls-multi-selectize" name="bpolls_settings[poll_user_role][]" multiple>
						<?php
						$roles = $wp_roles->get_names();
						foreach ( $roles as $role => $rname ) {
							$selected = ( ! empty( $bpolls_settings['poll_user_role'] ) && in_array( $role, $bpolls_settings['poll_user_role'], true ) ) ? 'selected' : '';
							?>
						<option value="<?php echo esc_attr( $role ); ?>" <?php echo esc_attr( $selected ); ?>><?php echo esc_attr( $rname ); ?></option>
						<?php } ?>
					</select>				
				</div>		
			</div>
			<?php
			$types = bp_get_member_types( array(), 'objects' );
			if ( $types ) {
				?>
			<div class="wbcom-settings-section-wrap" id="bpolls_member_type" 
				<?php
				if ( isset( $bpolls_settings['limit_poll_activity'] ) && 'member_type' !== $bpolls_settings['limit_poll_activity'] ) :
					?>
				style="display:none" <?php endif; ?>>
				<div class="wbcom-settings-section-options-heading">
					<label><?php esc_html_e( 'Select Member Type', 'buddypress-polls' ); ?></label>
					<p class="description">
						<?php esc_html_e( 'Select member type which are allowed to publish poll type activities.', 'buddypress-polls' ); ?>
					</p>
				</div>
				<div class="wbcom-settings-section-options">
						<select class="polls-multi-selectize" name="bpolls_settings[poll_member_type][]" multiple>

						<?php
						foreach ( $types as $typ ) {
							$selected = ( ! empty( $bpolls_settings['poll_member_type'] ) && in_array( $typ->name, $bpolls_settings['poll_member_type'], true ) ) ? 'selected' : '';
							?>
							<option value="<?php echo esc_attr( $typ->name ); ?>" <?php echo esc_attr( $selected ); ?>><?php echo esc_html( $typ->labels['singular_name'] ); ?></option>
						<?php } ?>
						</select>
					</div>
				</div>		
				<?php } ?>
		</div>
	</div>
		<?php submit_button(); ?>
	</form>
	</div>
	</div>
	</div>
<?php
}
?>