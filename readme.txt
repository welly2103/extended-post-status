=== Extended Post Status ===
Contributors: welly2103
Tags: status, post, publishing, extended, statuses
Requires at least: 4.9.8
Tested up to: 5.0
Requires PHP: 7.2
Stable tag: 5.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add new post status types.

== Description ==

This plugin provides the option to add new statuses to the backend and define system relevant settings. You can add / edit statuses just as categories or tags. All statuses are available for posts and pages.

You will find a new menu item located in the posts admin menu.

== Installation ==

1. Upload to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Use the new menu item `Statuses` in posts menu

== Screenshots ==

1. The status overview page
2. Quick edit with custom statuses
3. The status options in the classic post editor
4. The status options in gutenberg editor

== Frequently Asked Questions ==

= How does this plugin work? =

As there is no core hook to add new items to the status dropdown in posts and in the quick edit view, js is required to achieve this. It is definently the most dirty way, but WP Core does not provide other ways to do it.

= What happens when I delete a status or deactivate the plugin? =

Be careful, posts without a valid status will be hidden! Just change the status of posts to a system status (e.g. publish or draft) with custom statuses before you delete a status or deactivate the plugin.
Your posts will not be deleted, but you need to know your old status slugs, once you have deleted a status or deactivated the plugin to get your posts back in the backend.

There is an open track ticket fo this circumstance:
https://core.trac.wordpress.org/ticket/12706

== Changelog ==

= 1.0.1 =
* [Added] Page status support
* [Fixed] Show in admin list all bug

= 1.0.0 =
* [Added] Initial version