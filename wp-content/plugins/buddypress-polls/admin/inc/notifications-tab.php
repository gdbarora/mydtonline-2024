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
	$bpolls_settings = get_site_option( 'wbpolls_notification_settings' );
} else {
	$bpolls_settings = get_site_option( 'wbpolls_notification_settings' );
}

?>
<div class="wbcom-tab-content">
	<div class="wbcom-admin-title-section">
		<h3 style="margin: 0 0 5px"><?php esc_html_e( 'Notifications Settings', 'buddypress-polls' ); ?></h3>
		<p class="description"><?php esc_html_e( 'This feature is available only for Polls as Posts, and is not applicable to BuddyBoss or BuddyPress Activity Polls.', 'buddypress-polls' ); ?></p>
	</div>
	<div class="wbcom-admin-option-wrap wbcom-admin-option-wrap-view">
		<form method="post" action="admin.php?action=update_network_options">
			<div class="wbcom-wrapper-admin">				
				<div class="wbcom-admin-option-wrap-bp-poll">
					<input name='wbpolls_notification_settings[hidden]' type='hidden' value="" />
					<?php
					settings_fields( 'buddypress_wbpolls' );
						do_settings_sections( 'buddypress_wbpolls' );
					?>
					<div class="form-table polls-general-options">
						<div class="wbcom-settings-section-wrap">
							<div class="wbcom-settings-section-options-heading">
								<label
									for="blogname"><?php esc_html_e( 'Enable Notifications', 'buddypress-polls' ); ?></label>
								<p class="description" id="tagline-description">
									<?php esc_html_e( 'Allow admin users and other users ( who create a poll ) to get notifications.', 'buddypress-polls' ); ?>
								</p>
							</div>
							<div class="wbcom-settings-section-options">
								<label class="wb-switch">
									<input name='wbpolls_notification_settings[wppolls_enable_notification]' type='checkbox' value='yes'
										<?php ( isset( $bpolls_settings['wppolls_enable_notification'] ) ) ? checked( $bpolls_settings['wppolls_enable_notification'], 'yes' ) : ''; ?> />
									<div class="wb-slider wb-round"></div>
								</label>
							</div>
						</div>

						<div class="wbcom-settings-section-wrap">
							<div class="wbcom-settings-section-options-heading">
								<label
									for="blogname"><?php esc_html_e( 'Admin Notifications', 'buddypress-polls' ); ?></label>
								<p class="description" id="tagline-description">
									<?php esc_html_e( 'The admin will get a notification for review the poll created by a user. This option will work if "Enable user publishing" is disable.', 'buddypress-polls' ); ?>
								</p>
							</div>
							<div class="wbcom-settings-section-options">
								<label class="wb-switch">
									<input name='wbpolls_notification_settings[wppolls_admin_notification]' type='checkbox' value='yes'
										<?php ( isset( $bpolls_settings['wppolls_admin_notification'] ) ) ? checked( $bpolls_settings['wppolls_admin_notification'], 'yes' ) : ''; ?> />
									<div class="wb-slider wb-round"></div>
								</label>
							</div>
						</div>
                        <div class="wbcom-settings-section-wrap">
							<div class="wbcom-settings-section-options-heading">
								<label
									for="blogname"><?php esc_html_e( 'Member Notification', 'buddypress-polls' ); ?></label>
								<p class="description" id="tagline-description">
									<?php esc_html_e( 'The members will get a notification when admin approved their poll.', 'buddypress-polls' ); ?>
								</p>
							</div>
							<div class="wbcom-settings-section-options">
								<label class="wb-switch">
									<input name='wbpolls_notification_settings[wppolls_member_notification]' type='checkbox' value='yes'
										<?php ( isset( $bpolls_settings['wppolls_member_notification'] ) ) ? checked( $bpolls_settings['wppolls_member_notification'], 'yes' ) : ''; ?> />
									<div class="wb-slider wb-round"></div>
								</label>
							</div>
						</div>	
                        <div class="wbcom-settings-section-wrap">
							<div class="wbcom-settings-section-options-heading">
								<label for="blogname"><?php esc_html_e( 'Select admin user(s)', 'buddypress-polls' ); ?></label>
								<p class="description" id="tagline-description">
									<?php esc_html_e( 'Select an admin to whom you want to get approval notifications.', 'buddypress-polls' ); ?>
								</p>
							</div>
							<?php 
							$administrators = get_users(array(
								'role' => 'administrator',
							)); ?>
							<div class="wbcom-settings-section-options">
								<select class="polls-multi-selectize" name="wbpolls_notification_settings[wppolls_admin_user][]" multiple>
									<?php
									foreach ( $administrators as $rname ) {
										$selected = ( ! empty( $bpolls_settings['wppolls_admin_user'] ) && in_array( $rname->ID, $bpolls_settings['wppolls_admin_user']) ) ? 'selected' : '';
										?>
									<option value="<?php echo esc_attr( $rname->ID ); ?>" <?php echo esc_attr( $selected ); ?>><?php echo esc_attr( $rname->user_login ); ?></option>
									<?php } ?>
								</select>				
							</div>
						</div>					
					</div>
				</div>
			</div>
            <?php submit_button(); ?>
		</form>
	</div>
</div>
