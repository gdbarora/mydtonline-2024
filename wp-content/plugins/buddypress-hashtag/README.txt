=== Wbcom Designs - BuddyPress Hashtags ===
Contributors: wbcomdesigns
Donate link: https://wbcomdesigns.com
Tags: comments, spam, hashtags, buddypress hashtags
Requires at least: 3.0.1
Tested up to: 6.3.0
Stable tag: 2.9.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The plugin gives the ability to use hashtags on any buddypress,bbpress and wordpress posts and pages.

== Description ==

BuddyPress Hashtags gives the ability to use hashtags on any buddypress,bbpress and wordpress posts and pages. It comes with searchable hashtags either from widgets provided by the plugin or using buddypress search.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Download the zip file and extract it.
2. Upload `buddypress-hashtags` directory to the `/wp-content/plugins/` directory
3. Activate the plugin through the \'Plugins\' menu.
4. Enjoy
If you need additional help you can contact us for [Custom Development](https://wbcomdesigns.com/hire-us/).

== Frequently Asked Questions ==

= Does This plugin require BuddyPress? =

Yes, It needs you to have BuddyPress installed and activated.

== Changelog ==
= 2.9.8 =
* Fix: (#185)Fixed activities are not loading
* Fix: (#184) Fixed activity slug issue with bp-12 beta-3
* Fix: (#183) Added enable/disable follow hashtag setting
* Fix: (#183) Update backend setting labels and description
* Fix: Delete license key on plugin deactivation

= 2.9.7 =
* Fix: (#179) Single activity does not open when clicking on the activity hashtag in BuddyBoss.

= 2.9.6 =
* Fix: PHPCS fixes

= 2.9.5 =
* Fix: (#62) Issue with php 8.2

= 2.9.4 =
* Fix: (#173) activity issues in more than one hashtag.
* Fix: (#168) issue in adding follow hashtags.
* Fix: (#171) automatically followed hashtag.
* Fix: (#167) followed hashtags activity issue.
* Fix: Check the Hashtag item with the user id before inserting
* Fix: #169 - User Profile allowed Hashtag Activities issue ( in case of no followed hashtags)
* Fix: (#166) - admin bar submenu missing
* Fix: (#165) - Issue with BuddyBoss edit (count increases with every edit).


= 2.9.3 =
*Fix: #161 - Hashtag deletion issue

= 2.9.2 =
* Fix: Fixed managed hashtag log UI
* Fix: (#158)Fixed managed hashtag font sizes
* Fix: (#160)Fixed page hashtag not working
* Fix: (#159)Fixed bbPress hashtag searching is not working

= 2.9.1 =
* Fix: (#148)Fixed max hashtag lenght not working with allow non alphanumeric hashtag option
* Fix: (#149)Fixed plugin conflict with Simple Custom CSS and JS PRO
* Fix: (#150)Fixed no video preview if video title has hashtag
* Fix: (#152)Fixed error on posting activity

= 2.9.0 =
* Fix: updated admin wrapper
* Fix: PHPCS fixes
* Fix: (#145) Added RTL support
* Fix: (#143) - forum hashtag is not working with BB Plateform

= 2.8.0 =
* Fix: (#134) - Admin wrapper displaying if bbpress activated and BuddyPress
* Fix: (#135) - Disable bbpress hashtag setting displaying if bbPress
* Fix: (#138) changed bbPress widget title string
* Fix: Fixed Old Url ?s= to ?activity_search=

= 2.7.0 =
* Fix: Fixed redirect issue on bulk plugin activation
* Fix: Removed install plugin button from wrapper
* Fix: #124 Hashtags Widget UI while no hashtags are available
* Fix: backend UI updated

= 2.6.0 =
* Fix: (#120) Load newest button issue

= 2.5.4 =
* Fix: Create new hashtag counting table with post id wise
* Fix: hashtag Counting issue with Post, Page and bbPress.
* Fix: text domain errors
* Enhancement: #114 - Display Hashtag count option in widget

= 2.5.3 =
* Fix: hashtag tag not added in Gutenberg page
* Fix: #110 - Notice is displaying when we switch to Olympus Theme
* Fix: string changes

= 2.5.2 =
* Fix: Remove Friends component word when buddypress plugin not activate
* New Feature: Added #97 Added new Hooks
* Fix: #106 - Duplicate Query issue in activity page
* New Feature: Added #94 sort by date
* Fix: #99 Plugin activation issue

= 2.5.1 =
* Fixed #95 -Update Last Count when update hashtag table count

= 2.5.0 =
* New Feature: hashtags logs with delete option

= 2.4.0 =
* Fixed #79 - When delete comment then hashtag didn't decrease
* Fixed #82 - hashtag only for bbpress
* Added video link wrapper
* Updated Backend UI

= 2.3.0 =
Fixed : replace wp_kses to bp_activity_filter_kses function
Fixed : hashtag comment search issue
Fixed : #78 - Wordpress database error 

= 2.2.0 =
Fixed : Set hashtag link in buddyboss platform topic reply
Fixed: #46 - BBplatform issues: Hashtags added from backend
Fixed: #62 - Unable to search hashtag shortcode
Fixed: #65 - Community hashtags : Sort by name
Added: #63 - Most used Hashtags with Font variation cloud display

= 2.1.1 =
* Fix: #59 - Fixed License issue.
* Fix: #58 - Fixed insertion of invalid links inside the customizer.

= 2.1.0 =
* Fix: #55 - Subscribers are unable to post multiple hashtags.
* Fix: #47 - Hashtag for single page with bb platfrom
* Fix: #48 - Hashtag for single post with bb platfrom

= 2.0.1 =
* Fix: Fixed p Tag added in HashTag Widget with buddyboss platform plugin

= 2.0.0 =
* Fix: Added Setting Link in BuddyPress Hashtag plugin
* Fix: Fixed alphanumeric hashtag with BuddyBoss platform 
* Fix: Fixed Hashtag issue with BuddyBoss platform plugin
* Fix: Limited call of admin CSS and js file on BuddyPress hashtag setting page

= 1.7.0 =
* Fix: Fixed bbPress Sort order issue with Alpabatic order

= 1.6.0 =
* Enhancement: Added support for blog comments
* Fix: Compatibility with BP v5.1.2
* Fix: Fixed Issue hashtag white space remove after publish post or page
* Enhancement: Add Option Disable HashTag link on bbPress and Blog Posts
* Fix: Fixed #26 Hashtag do not show when Allow non alphanumeric hashtag is enabled
* Fix: fixed #26 for gutenberg editor issue with paragraph block

= 1.5.0 =
* Fix: Display #link search if activity page is set as homepage
* Fix: Links will be published based on activity slug

= 1.4.1 =
* Fix: (#24) Translation issue.
* Enhancement: Added Germen Translation file contributed by Thorsten Wollenhoefer

= 1.4.0 =
* Enhancement: added support for wordpress posts and pages to include hashtags.
* Enhancement: provided admin option to clear older hashtags for buddypress,bbpress,wp posts and pages.
* Enhancement: added support to post hashtags in multiple languages.

= 1.3.0 =
* Enhancement: added buddypress and bbpress shortcodes to list hashtags.

= 1.2.1 =
* Fix: added font awesome 4.7.0.

= 1.2.0 =
* Enhancement: 4.3.0 compatibility.

= 1.1.1 =
* Enhancement: Links for new posted activities with Youzer

= 1.1.0 =
* Enhancement: Separate wordpress table for buddypress and bbpress hashtags.
* Fix : Added admin description.

= 1.0.0 =
* first version.
