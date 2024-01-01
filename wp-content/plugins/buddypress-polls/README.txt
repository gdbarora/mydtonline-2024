=== Wbcom Designs – BuddyPress Polls ===
Contributors: wbcomdesigns
Donate link: https://wbcomdesigns.com
Tags: comments, spam, polls, buddypress polls
Requires at least: 3.0.1
Tested up to: 6.4.1
Stable tag: 4.3.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

With the BuddyPress Polls plugin, you and your community can easily create polls within your posts. These polls can be conveniently added to the main activity stream, as well as to user profiles and groups.

== Description ==

With the BuddyPress Polls plugin, you and your community can create polls within posts. These polls can be added to the main activity stream, user profiles, and groups.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Download the zip file and extract it.
2. Upload `buddypress-polls` directory to the `/wp-content/plugins/` directory
3. Activate the plugin through the \'Plugins\' menu.
4. Enjoy
If you need additional help, you can contact us for [Custom Development](https://wbcomdesigns.com/hire-us/).

== Frequently Asked Questions ==

= Does This plugin requires BuddyPress? =

Yes, needs you to have BuddyPress installed and activated.

= What to expect when installing and activating BuddyPress Polls? =

Once you activate the plugin, a poll icon will appear in the post box on the activity stream, user profiles, and groups. This feature enables you to create polls and ask questions for others to vote on. With the BuddyPress Polls plugin, both you and your community can easily create and place polls in the main activity stream, user profiles, and group pages.

== Changelog ==
= 4.3.6 =
* Updated: Admin label, description, and frontend percent poll UI
* Updated: (#348) Backend poll answers spacing
* Fix: Email site name issue
* Fix: (#348) Backend additional fields setting not working
* Fix: FAQ UI and notification_subject
* Fix: Poll notification subject site name fixes
* Fix: Show hide option and updated label
* Fix: Frontend poll list space

= 4.3.5 =
* Fix: BP v12
* Fix: License issue

= 4.3.4 =
* Fix: PHPCS fixes

= 4.3.3 =
* Added: Hookable position after poll submit

= 4.3.2 =
* Added: BuddyPress search compatibility
* Added: (#334) Poll Share via Embed
* Added: (#337) Allow Comment Option Setting For Single Poll
* Added: (#333) Plugin CSS to the archive page
* Added: (#333) Archive and Single Page Template
* Managed: (#342) Poll search results UI
* Managed: (#334) Embed poll input UI
* Managed: (#334) Embed poll UI
* Managed: (#333) Archive poll listing UI and fixes
* Updated: (#333) Sidebar name
* Fix: (#342) Poll activity not listed in BuddyBoss search
* Fix: (#184) Creator unable to add option
* Fix: (#334) Add poll option via share embedded poll
* Fix: (#333) Warning
* Fix: (#332) String changes and allow comments

= 4.3.1 =
* Fix: (#330) grunt and UI fixes
* Fix: Fixed #330 - Issue with guest user
* Fix: Fixed #328 - Show Result After Expires hide when Never Expire Enabled
* Fix: Fixed #327 - conflict with stories plugin

= 4.3.0 =
* New Feature: A new type of poll has been added as a custom post type.
* New Feature: Added poll options: text, video, images, audio, and HTML. Engage your audience like never before.
* New Feature: A poll dashboard has been created to manage new poll posts, except for BP Poll Activities.

= 4.2.8 =
* Fix: Set only inactive when the license key deactivates
* Fix: Update the license activation file and set the response to transient
* Fix: Call plugin js and css when the Elementor widget is used on any page

= 4.2.7 =
* Enhancement - Display notice if license key not activated, License key expired
* Enhancement - Added - bp business profile plugin compatibility
* Fix: Fixed #210 - poll icon is visible even on restriction

= 4.2.6 =
* Fix: (#194) Fixed Poll label text
* Fix: Fixed Plugin redirect issue when multi plugin activate the same time
* Fix: (#195) Fixed poll embed activity does not work with forums

= 4.2.5 =
* Fix: (#193)Fixed PHP warning errors

= 4.2.4 =
* Fix: (#187)Fixed editor role is unable to view entire media library

= 4.2.3 =
* Fix: Fixed UI of sortable handle spacing
* Fix: (#181)Fixed users can not see poll results before voting

= 4.2.1 =
* Fix: (#179)Fixed setting reset when we deactivate the plugin

= 4.2.0 =
* Fix: (#174) Poll UI fixed with shortcode
* Fix: (#177)Fixed fatal error with bp member blog pro
* Fix: Conflict with member blog plugin
* Fix: Admin UI tab option managed
* Fix: Remove view more extension button
* Fix: Updated admin UI

= 4.1.3 =
* Fix: #173 - View all votes issue

= 4.1.2 =
* Fix: Allow poll author to update the poll description with bb.
= 4.1.1 =
* Fix: Edit fixes for BB reverted

= 4.1.0 =
* New Feature: Allowed members to add new poll options.
* New Feature: Allow members to chose if they want to display poll results while creating new polls.
* Fix: Allow poll author to update the poll description with bb.

= 4.0.0 =
* New Feature : Added Shortcode to display Poll activity at any page or CPT
* New Feature : #150 - Added Polls Quick Tag for bbPress Topics editor
* Fixed - (#150) Managed polls shortcode UI in forum
* New Feature #150 - Enqueue script and style in shortcode and register global
* Fixed - (#133)Fixed unable to see the result of poll inside the hidden groups
* Fixed - Reduced setInterval timeout with bb platform
* Fixed - (#148) Added RTL support

= 3.9.5 =
* Fixed - #129 - Polls Closing Date time then hide checkbox or radio button
* Fixed - #129 - Poll closing date & time setting
* Fixed - #132 - Fixed php notice with private group poll activity
* Fixed - managed backend welcome page
* Fixed - Embed polls activity data in rest api activity endpoint

= 3.9.4 =
* Fixed - Activity poll media uploader popup fixed
* Fixed - Managed 'Add a poll' language transferable and fixes

= 3.9.3 =
* Fixed - (#124) Update poll icon and fixes
* Fixed - BB Plattfom related script only work when buddyboss platform
* Enhancement - Added poll tooltip with bb platform
* Enhancement - #124 - BuddyBoss activity post form icon

= 3.9.2 =
* Fixed - Managed RTL Fixes

= 3.9.1 =
* Enhancement - (#123) Set color scheme for progress bar
* Enhancement - (#123) Updated polls results UI

= 3.9.0 =
* (#118) Update UI with kleo, Olympus
* Removed <p> tag from Highest Voted Poll data
* Fixed #118 - Hide reults enable then hide other user votes for unvoted
* (#118) Update dialog message
* Fixed - PHP warning indefined variable $title in admin side and $poll
* Fixed #118 - Close one div
* Fixed #118 - Fixed disable Polls Results issue after submit poll
* Fixed - Disable poll results then only Hide percentage and Disabled
* Fixed #118 - Add Revoting option
* Fixed (#118) Managed dialog box UI

= 3.8.1 =
* Fixed - (#119)Fixed enabling users to edit other users activities

= 3.8.0 =
* Fixed - user role restriction issue with buddyboss
* Fixed - (#117) Fixed backend dropdown UI issue
* Fixed - (#114) Hide poll icon for restricted members
* Fixed - Fixed - Delete checkin and quote activity when user click on Polls Icon
* Enhancement - Manage poll icon with rtmedia
* Enhancement - Manage inline poll icon with buddyboss
* Fixed - (##111) Fixed result do not show when all the options are numbers

= 3.7.2 =
* Fixed - (#75) Update UI with kleo theme.
* Fixed - (#85) Managed UI with youzify theme.
* Fixed - (#106) Fixed poll visibility with BB Plateform.
* Fixed - Fixed - Plugin activation issues.

= 3.7.1 =
* Fixed - Update datetimepicker spacing with reign, buddyx

= 3.7.0 =
* Fixed - Fixed UI issue with Aardvark, Gwangi themes
* Fixed - beehive theme specific UI managed

= 3.6.0 =
* Fixed - #100 - poll % result issue with numbers
* Fixed - fatal error when open customizer in admin side

= 3.5.0 =
* Fixed - thrive theme with icon align issue fixed
* New Feature - Enable thank you message input when user create polls.
* Fixed - Display Polls Activity in admin side

= 3.4.0 =
* Fixed - (#98) Fixed translation issue
* Fixed - Update plugin backend UI

= 3.2.0 =
* Fixed - #88 - poll and buddyform conflict
* Fixed - Hide activate option when open another option
* Fixed - added Post from anywhere shortcode check to load js and css file
* Fixed - #109 - Compatibility issue with BP Polls
* Fixed - (#70) Fixed translation issue

= 3.1.0 =
* Enhancement: Pass activity poll action to ignore sticky post query
* Enhancement: Remove has_activity function to check poll activity

= 3.0.3 =
* Enhancement: Style improvement with BuddyBoss theme

= 3.0.2 =
* Enhancement: Style improvement with BuddyBoss theme

= 3.0.1 =
* Fixed - License issue
* Fixed - Admin can see all the poll results

= 3.0.0 =
* Fixed - Dashboard will display list of all recent poll results
* Fixed - Truncate the title on poll graph widget if it is too big
* Fixed - define exist condition for followin places
* Fixed #46 - Translation issues
* Added - Polls Activities user role and member type support
* Fixed #47 - uncheckable after you have submitted ed your answer

= 2.9.1 =
* Fixed - Activity Graph Widget Issue
* Fixed - German Translation
* Fixed - Poll save option issue when disable all option after save changes

= 2.9.0 =
* Fixed - Poll icon update in rtmedia container with default theme
* Fixed - Remove Polls Text with icon
* Fixed #64 - Admin will see all the result irrespective he has voted on any poll activity or not.
* Fixed - No polls voting in user then not need to display any pols in Polls Graph dropdown
* Fixed #58 - widget withspecific poll result
* Fixed #62 - poll graph widget
* Fixed #63 - Change name of the widget
* Fixed #59 - Poll issue with BuddyBoss
* Fixed #56 - Fixed notices and warnings on widget
* Fixed #55 - Create widget to make a dynamic poll system

= 2.7.0 =
 * Fix: (#49) Fixed dashicon conflict with bb
 * Fix: (#48) Fixed datetimepicker UI issue

= 2.6.0 =
* Fix: Call Admin CSS and JS file in buddypress polls setting page
* Fix: Load front end js and css on plugin pages

= 2.5.0 =
* Fix: Compatibility with BuddyPres Check-ins and BuddyPress Quotes
* Fix: Buddypres Poll UI Fixes with BoddyBoss Theme
* Fix: Admin notice for buddypress activation

= 2.4.0 =
* Fix: (#28) Fixed 'groups_activity_new_update_action' hook
* Enhancement: Added German translation Files contributed by Thorsten Wollenhoefer

= 2.3.1 =
* Fix: Blank poll results for fresh installation.

= 2.3.0 =
* Enhancement: bp 4.3.0 compatibility.
* Enhancement: Youzer compatibility.
* Fix: added fontawesome 4.7.0.

= 2.2.0 =
* Enhancement: Added option to attach image with poll posting.
* Fix : PHP erros and warning fixes.

= 2.1.0 =
* Enhancement: Added wp dashboard widget to list sitewide polls statistics.
* Enhancement: Added wp dashboard widget to list polls graphical results.
* Enhancement: Dedicated support for Kleo and Boss Theme

= 2.0.1 =
* Fix: Auto Updater Fix.

= 2.0.0 =
* Enhancement: Plugin backend settings ui enhancement.
* Enhancement: Compatibility with BP 4.1.0

= 1.0.6 =
* Fix : BP Nouveau compatibility.
* Fix : Poll graph widget duplicate options fix.
* Enhancement: Better Compatibility with Default WordPress Themes
* Enhancement: Dedicated support with Reign Theme
* Enhancement: Better Compatibility with rtMedia
* Enhancement: Added French translation files – credits to Jean Pierre Michaud

= 1.0.5 =
* Fix : BP nouveau activity post issue.

= 1.0.4 =
* Fix : Resolved translation issue.

= 1.0.3 =
* Fix : Poll options sortable issue with nouveau template packs.

= 1.0.2 =
* Enhancement : Added plugin license code.

= 1.0.1 =
* Enhancement : Added support for multisite

= 1.0.0 =
* first version.
