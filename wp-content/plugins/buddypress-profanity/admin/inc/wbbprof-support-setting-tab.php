<?php
/**
 * Faqs support template file.
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
?>
<div class="wbcom-tab-content">      
	<div class="wbcom-faq-adming-setting">
		<div class="wbcom-admin-title-section">
			<h3><?php esc_html_e( 'Have some questions?', 'buddypress-profanity'  ); ?></h3>
		</div>
		<div class="wbcom-faq-admin-settings-block">
			<div id="wbcom-faq-settings-section" class="wbcom-faq-table">
				<div class="wbcom-faq-section-row">
					<div class="wbcom-faq-admin-row">
						<button class="wbcom-faq-accordion">
							<?php esc_html_e( 'Does This plugin requires BuddyPress?', 'buddypress-profanity' ); ?>
						</button>
						<div class="wbcom-faq-panel">
							<p> 
								<?php esc_html_e( 'Yes, It needs you to have BuddyPress installed and activated.', 'buddypress-profanity' ); ?>
							</p>
						</div>
					</div>
				</div>
				<div class="wbcom-faq-section-row">
					<div class="wbcom-faq-admin-row">
						<button class="wbcom-faq-accordion">
						<?php esc_html_e( 'Does this plugin filter multiple keywords?', 'buddypress-profanity' ); ?>
						</button>
						<div class="wbcom-faq-panel">
							<p> 
							<?php esc_html_e( 'Yes, multiple keywords can be set to filter with the setting [Keywords to remove] provied under general tab.', 'buddypress-profanity' ); ?>  
							</p>
						</div>
					</div>
				</div>
				<div class="wbcom-faq-section-row">
					<div class="wbcom-faq-admin-row">
						<button class="wbcom-faq-accordion">
						<?php esc_html_e( 'How do I specify a character other than defined character to replace out keywords?', 'buddypress-profanity' ); ?>
						</button>
						<div class="wbcom-faq-panel">
							<p>
								<?php esc_html_e( 'This can be achieved with the help of filters provided in the plugin. To replace keywords with cutsom character for eg. @ write below coed in your functions.php file of active theme or wherever you want.', 'buddypress-profanity' ); ?>
							</p>
							<pre>add_filter( 'wbbprof_word_rendering_symbols', 'custom_wbbprof_word_rendering_symbols', 10, 1 );
function custom_wbbprof_word_rendering_symbols($rendering_symbols) {
$rendering_symbols['at_the_rate'] = '[ @] At the rate';
return $rendering_symbols;
}
add_filter('wbbprof_custom_character', 'custom_wbbprof_custom_character', 10, 1);
function custom_wbbprof_custom_character($symbol) {
$symbol = '@';
return $symbol;
}</pre>
							<p><?php esc_html_e( 'After adding this code an option is created under [Filter Character], select the newly added option and save the settings.', 'buddypress-profanity' ); ?></p>
						</div>
					</div>
				</div>
				<div class="wbcom-faq-section-row">
					<div class="wbcom-faq-admin-row">
						<button class="wbcom-faq-accordion">
							<?php esc_html_e( 'Does this change the content in BuddyPress database?', 'buddypress-profanity' ); ?>
						</button>
						<div class="wbcom-faq-panel">
							<p> 
							<?php esc_html_e( 'No, the plugin filters the content to display on screen, buddypress database is unaffected from plugin changes.', 'buddypress-profanity' ); ?>   
							</p>
						</div>
					</div>
				</div>
				<div class="wbcom-faq-section-row">
					<div class="wbcom-faq-admin-row">
						<button class="wbcom-faq-accordion">
							<?php esc_html_e( 'How is Case Matching setting useful?', 'buddypress-profanity' ); ?>
						</button>
						<div class="wbcom-faq-panel">
							<p>
								<?php esc_html_e( 'The [Case Matching] setting provides two option Case Sensitive and Case Insensitive. Case Sensitive filters keywords with strich case matching and is not recommended while Case Insensitive setting capture more words while filtering.', 'buddypress-profanity' ); ?>
							</p>
							<p>
								<?php esc_html_e( 'We recommend users to use Case Insensitive matching.', 'buddypress-profanity' ); ?>
							</p>
						</div>
					</div>
				</div>
				<div class="wbcom-faq-section-row">
					<div class="wbcom-faq-admin-row">
						<button class="wbcom-faq-accordion">
							<?php esc_html_e( 'How is Strict Filtering setting useful?', 'buddypress-profanity' ); ?>
						</button>
						<div class="wbcom-faq-panel">
							<p>
								<?php esc_html_e( 'The [Strict Filtering] with strict mode on does not filter embedded keywords.', 'buddypress-profanity' ); ?>
							</p>
							<p>
								<?php esc_html_e( 'We recommend users to use Strict Filtering ON mode.', 'buddypress-profanity' ); ?>
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
