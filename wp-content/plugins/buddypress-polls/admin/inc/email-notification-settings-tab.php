<?php
/**
 * Notification Tab Content.
 *
 * @link       https:// wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Member_Blog_Pro
 * @subpackage Buddypress_Member_Blog_Pro/admin/inc
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $wp_roles;
$user_roles            = $wp_roles->get_names();
$bpmb_pronotify_option = get_option( 'wbpolls_notification_setting_options' );

?>
<div class="wbcom-tab-content woo-document-pro">
	<div class="wbcom-wrapper-admin">
		<div class="wbcom-admin-title-section">
			<h3 style="margin: 0 0 5px"><?php esc_html_e( 'Notifications Setting Options', 'buddypress-polls' ); ?></h3>
		<p class="description"><?php esc_html_e( 'This feature is available only for Polls as Posts, and is not applicable to BuddyBoss or BuddyPress Activity Polls.', 'buddypress-polls' ); ?></p>
		</div>
		<div class="wbcom-admin-option-wrap wbcom-admin-option-wrap-view wbcom-notification-setting-options">
			<form method="post" action="options.php">
				<?php
					settings_fields( 'wbpolls_notification_setting_options' );
					do_settings_sections( 'wbpolls_notification_setting_options' );
				?>
				<div class="form-table bpmbp-notification-options-accordion">
					<div class="wbcom-settings-section-wrap wbcom-bpmbp-accordion">
						<div class="wbcom-settings-section-options-heading">
							<a href="#" class="bpmbp-accordion">	
								<label for="wcap-pro-tab">
									<?php esc_html_e( 'Admin notification settings', 'buddypress-polls' ); ?>
								</label>
							</a>
						</div>
						<div class="bpmbp-admin-notification-options-content">
						<div class="form-table">
							<div class="wbcom-settings-section-wrap">
								<div class="wbcom-settings-section-options-heading">
									<label for="wcap-pro-tab">
										<?php esc_html_e( 'Subject', 'buddypress-polls' ); ?>							
									</label>
								</div>
								<div class="wbcom-settings-section-options">
									<input name='wbpolls_notification_setting_options[admin][notification_subject]' id="bp-member-blog-pro-notification-subject" type='text' value='<?php echo ( isset( $bpmb_pronotify_option['admin']['notification_subject'] ) ) ? esc_attr( $bpmb_pronotify_option['admin']['notification_subject'] ) : ''; ?>' placeholder="<?php esc_html_e( 'Add notification subject', 'buddypress-polls' ); ?>" />
									<p class="description"><?php esc_html_e( 'Enter the subject line for notification subject.', 'buddypress-polls' ); ?></p>
								</div>
							</div>
						</div>
						<div class="form-table">
							<div class="wbcom-settings-section-wrap">
								<div class="wbcom-settings-section-options-heading">
									<label for="wcap-pro-tab">
									<?php esc_html_e( 'Notification content', 'buddypress-polls' ); ?>							
									</label>
								</div>
								<div class="wbcom-settings-section-options">
									<?php
									wp_editor(
										isset( $bpmb_pronotify_option['admin']['notification_content'] ) ? $bpmb_pronotify_option['admin']['notification_content'] : '',
										'bpmbp-admin-notification-content',
										array(
											'media_buttons' => false,
											'textarea_name' => 'wbpolls_notification_setting_options[admin][notification_content]',
										)
									);
									?>
								</div>
								<p class="description"><?php esc_html_e( 'Text to admin for approve the blog.Personalize with HTML and {tag} markers.', 'buddypress-polls' ); ?></p>
								<code>
									{site_name} - <?php esc_html_e( 'Site title', 'buddypress-polls' ); ?> </br>
									{poll_name} - <?php esc_html_e( 'Published post title', 'buddypress-polls' ); ?> </br>
									{site_admin} - <?php esc_html_e( 'Selected admin first name', 'buddypress-polls' ); ?>
								</code>
							</div>
						</div>
					</div>
				</div>
				<div class="form-table bpmbp-notification-options-accordion">
					<div class="wbcom-settings-section-wrap wbcom-bpmbp-accordion">
						<div class="wbcom-settings-section-options-heading">
							<a href="#" class="bpmbp-accordion">
								<label for="wcap-pro-tab">
									<?php esc_html_e( 'Member notification settings', 'buddypress-polls' ); ?>							
								</label>
							</a>
						</div>
						<div class="bpmbp-member-notification-options-content bpmbp-notification-options-content">
							<div class="form-table">
								<div class="wbcom-settings-section-wrap">
									<div class="wbcom-settings-section-options-heading">
										<label for="wcap-pro-tab">
											<?php esc_html_e( 'Subject', 'buddypress-polls' ); ?>							
										</label>
									</div>
									<div class="wbcom-settings-section-options">
										<input name='wbpolls_notification_setting_options[member][notification_subject]' id="bp-member-blog-pro-notification-subject" type='text' value='<?php echo ( isset( $bpmb_pronotify_option['member']['notification_subject'] ) ) ? esc_attr( $bpmb_pronotify_option['member']['notification_subject'] ) : ''; ?>' placeholder="<?php esc_html_e( 'Add notification subject', 'buddypress-polls' ); ?>" />
										<p class="description"><?php esc_html_e( 'Enter the subject line for notification subject.', 'buddypress-polls' ); ?></p>
									</div>
								</div>
							</div>
							<div class="form-table">
								<div class="wbcom-settings-section-wrap">
									<div class="wbcom-settings-section-options-heading">
										<label for="wcap-pro-tab">
										<?php esc_html_e( 'Notification content', 'buddypress-polls' ); ?>							
										</label>
									</div>
									<div class="wbcom-settings-section-options">
										<?php
										wp_editor(
											isset( $bpmb_pronotify_option['member']['notification_content'] ) ? $bpmb_pronotify_option['member']['notification_content'] : '',
											'bpmbp-member-notification-content',
											array(
												'media_buttons' => false,
												'textarea_name' => 'wbpolls_notification_setting_options[member][notification_content]',
											)
										);
										?>
										<p class="description"><?php esc_html_e( 'Text to member to notify them, the blog is approved. Personalize with HTML and {tag} markers.', 'buddypress-polls' ); ?></p>
										<code>
											{site_name} - <?php esc_html_e( 'Site title', 'buddypress-polls' ); ?> </br>
											{poll_name} - <?php esc_html_e( 'Published post title', 'buddypress-polls' ); ?> </br>
											{publisher_name} - <?php esc_html_e( 'Post autohr first name', 'buddypress-polls' ); ?>
										</code>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php submit_button(); ?>
			</form>
		</div>
	</div>


