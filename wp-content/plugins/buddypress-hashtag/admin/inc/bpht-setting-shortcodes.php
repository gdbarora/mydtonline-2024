<?php
/**
 *
 * This file is called for rendering shortcodes.
 */

?>
<div class="wbcom-tab-content">      
<div class="wbcom-faq-adming-setting">
	<div class="wbcom-admin-title-section">
		<h3><?php esc_html_e( 'Available shortcodes', 'buddypress-hashtags' ); ?></h3>
	</div>
	<div class="wbcom-faq-admin-settings-block">
		<div id="wbcom-faq-settings-section" class="wbcom-faq-table">
			<div class="wbcom-faq-section-row">
				<div class="wbcom-faq-admin-row">
					<button class="wbcom-faq-accordion">
						<?php esc_html_e( 'BuddyPress Hashtags', 'buddypress-hashtags' ); ?>
					</button>
					<div class="wbcom-faq-panel">
					<p>
						<code>[bpht_bp_hashtags]</code>
					</p>
					<p>
						<?php esc_html_e( 'For eg.', 'buddypress-hashtags' ); ?><code><?php esc_html_e( '[bpht_bp_hashtags displaystyle="cloud" sortby="name" sortorder="asc" limit="12"]', 'buddypress-hashtags' ); ?></code>
					</p>
					<p><?php esc_html_e( 'Values accepted by parameters', 'buddypress-hashtags' ); ?></p>
					<p><?php esc_html_e( '{displaystyle} - cloud/list', 'buddypress-hashtags' ); ?></p>
					<p><?php esc_html_e( '{sortby} - name/size', 'buddypress-hashtags' ); ?></p>
					<p><?php esc_html_e( '{sortorder} - asc/desc', 'buddypress-hashtags' ); ?></p>
					<p><?php esc_html_e( '{limit} - any numeric value', 'buddypress-hashtags' ); ?></p>
					</div>
				</div>
			</div>
			<div class="wbcom-faq-section-row">
				<div class="wbcom-faq-admin-row">
					<button class="wbcom-faq-accordion">
						<?php esc_html_e( 'bbPress Hashtags', 'buddypress-hashtags' ); ?>
					</button>
					<div class="wbcom-faq-panel">
					<p>
						<code>[bpht_bbpress_hashtags]</code>
					</p>
					<p>
						<?php esc_html_e( 'For eg.', 'buddypress-hashtags' ); ?><code><?php esc_html_e( '[bpht_bbpress_hashtags displaystyle="cloud" sortby="name" sortorder="asc" limit="12"]', 'buddypress-hashtags' ); ?></code>
					</p>
					<p><?php esc_html_e( 'Values accepted by parameters', 'buddypress-hashtags' ); ?></p>
					<p><?php esc_html_e( '{displaystyle} - cloud/list', 'buddypress-hashtags' ); ?></p>
					<p><?php esc_html_e( '{sortby} - name/size', 'buddypress-hashtags' ); ?></p>
					<p><?php esc_html_e( '{sortorder} - asc/desc', 'buddypress-hashtags' ); ?></p>
					<p><?php esc_html_e( '{limit} - any numeric value', 'buddypress-hashtags' ); ?></p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
