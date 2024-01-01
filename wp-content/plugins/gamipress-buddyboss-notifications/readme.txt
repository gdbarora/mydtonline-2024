=== GamiPress - BuddyBoss Notifications ===
Contributors: gamipress, tsunoa, rubengc, eneribs
Tags: gamipress, gamification, point, achievement, rank, badge, award, reward, credit, engagement, ajax
Requires at least: 4.4
Tested up to: 6.2
Stable tag: 1.0.7
License: GNU AGPLv3
License URI: http://www.gnu.org/licenses/agpl-3.0.html

Instantly notify of achievements, steps and/or points awards completion to your BuddyBoss members.

== Description ==

BuddyBoss Notifications notifies to your community members about new achievements, steps, points awards, points deductions, ranks and/or rank requirements completion using the BuddyBoss Notifications component.

You are able configure the notifications patterns with dynamic tags to show, for example, the member name to make the notifications more personalized.

In addition, the add-on includes general settings and setting by type to configure or disabled notifications in the way you wish (disable all ranks notifications, personalize notifications of a specific type, etc).

Note: This add-on DOES NOT requires the GamiPress - Notifications add-on. This add-on is completely independent and can work without the GamiPress - Notifications add-on.

= Features =

* Notify to your BuddyBoss members about new earnings using the BuddyBoss Notifications component.
* General settings to configure or disable notifications to all elements.
* Settings by type to configure or disable notifications for a specific type.
* Includes several tags to personalize the notification content, like the title, content, image or url of the element notified or even user tags to display the user information.

== Installation ==

= From WordPress backend =

1. Navigate to Plugins -> Add new.
2. Click the button "Upload Plugin" next to "Add plugins" title.
3. Upload the downloaded zip file and activate it.

= Direct upload =

1. Upload the downloaded zip file into your `wp-content/plugins/` folder.
2. Unzip the uploaded zip file.
3. Navigate to Plugins menu on your WordPress admin area.
4. Activate this plugin.

== Frequently Asked Questions ==

== Changelog ==

= 1.0.7 =

* **Bug Fixes**
* Fixed compatibility to display the avatar in BuddyBoss App Notifications.

= 1.0.6 =

* **Improvements**
* Improved the SQL query to get the latest user earning for a user.

= 1.0.5 =

* **Improvements**
* Improved the way to get the latest user earning for a user.

= 1.0.4 =

* **Improvements**
* Prevent PHP warnings when rendering notifications.

= 1.0.3 =

* **Improvements**
* Added new checks to try to get the points awarded or deducted directly from the user earning when parsing the {points} tag.

= 1.0.2 =

* **New Features**
* Added support to BuddyBoss theme notifications avatar feature.

= 1.0.1 =

* **Improvements**
* Prevent PHP warnings while parsing notification tags.

= 1.0.0 =

* Initial release.
