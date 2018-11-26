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

This plugin provides the option to add new statuses to the backend and define
system relevant settings. You can add / edit statuses just as categories or
tags.

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

As there is no core hook to add new items to the status dropdown in posts and
in the quick edit view, js is required to achieve this. It is definently the
most dirty way, but WP Core does not provide other ways to do it.

There is an open track ticket fo this circumstance:
https://core.trac.wordpress.org/ticket/12706

== Changelog ==

= 1.0.0 =
* [Added] Initial version