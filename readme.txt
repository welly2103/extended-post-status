=== Extended Post Status ===
Contributors: welly2103
Tags: status, post, publishing, extended, statuses, page, post type
Requires at least: 4.9
Tested up to: 5.8
Requires PHP: 7.2
Stable tag: 5.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add new statuses to all post types (posts, pages, products, ...). You can handle post types with custom statuses and control the visibility of post types with your custom statuses.

== Description ==

This plugin provides the option to add new statuses to the backend and define the system relevant status settings. You can add/edit statuses just as categories or tags. All statuses are available for all your post types.

You will find a new menu item located in the settings admin menu.

For the sake of understanding, this plugin minimally changes messages, translations and workflow in Gutenberg Editor!

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

There is an open trac ticket fo this circumstance:
https://core.trac.wordpress.org/ticket/12706

= What happens when I delete the plugin? =

Since all custom statuses would no longer be available, deleting the plugin will reset all posts, pages etc. with an custom status to the draft status.
This step cannot be undone by reinstalling the plugin!
However, the plugin can be deactivated without any problems (see "What happens when I delete a status or deactivate the plugin?").

= What happens when I delete a status or deactivate the plugin? =

Be careful, posts without a valid status will be hidden! Just change the status of your post with a custom status to a system status (e.g. publish or draft) before you delete a custom status or deactivate the plugin.
Your posts will never be deleted, but you need to know your old status slugs once you have deleted a status or deactivated the plugin to get your posts back.

= Why is a hidden status still visible in dropdowns? =

The "Hide in admin drop downs" option only hides the status if the current post doesn't have the status. As long as your post has the hidden status, the drop down will still show it.

= Why is my button no longer called publish in Gutenberg? =

To avoid misunderstandings in handling with own custom statuses, the button has been named more generally and is now just called Save.
Likewise, the message that a post has been published has been renamed. This now only indicates that a post has been saved. The indicator whether a post is published or not should always be the status and not a message or button label.
Furthermore, the publishing sidebar of Gutenberg has been removed.

== Changelog ==

= 1.0.19 =
* [Fixed] Update handling of the javascript for disabling the publishing sidebar in Gutenberg

= 1.0.18 =
* [Added] The publish button in Gutenberg and Classic Editor no longer contains publish but only save to prevent confusion
* [Added] Remove the "two click" publishing sidebar in Gutenberg
* [Added] Override post published message and replace it with general post saved message in Gutenberg

= 1.0.17 =
* [Fixed] Status which were created by other plugins are no longer overwritten (Thanks to @ikancijan)
See: https://wordpress.org/support/topic/fix-for-custom-statuses-by-other-plugins/
* [Fixed] Add custom status meta box to gutenberg on all custom post types and not only for posts and pages
See: https://wordpress.org/support/topic/public-not-working-for-listings/
* [Fixed] Allow users with editing capabilities to see/preview posts with a non public status
See: https://wordpress.org/support/topic/permalinks-further-issue/

= 1.0.16 =
* [Fixed] If you delete the plugin, all posts, pages etc. with custom status will be reset to draft status.

= 1.0.15 =
* [Fixed] PHP undefined indexes
* [Fixed] Set default status to draft, if no status is selected.

= 1.0.14 =
* [Fixed] Respect future status
* [Fixed] Show planned (status future) posts in admin posts overview

= 1.0.13 =
* [Fixed] Trashing posts inside the posts editor is not possible

= 1.0.12 =
* [Fixed] PHP 7.4 access of non existing array object bug

= 1.0.11 =
* [Fixed] Enable bulk editing of custom statuses.

= 1.0.10 =
* [Added] Hide status in admin drop downs
* [Fixed] Selected custom statuses will auto select other custom statuses in admin drop downs.

= 1.0.9 =
* [Fixed] PHP error on accessing a non-object on admin menu page

= 1.0.8 =
* [Added] Settings submenu item "Extended Post Status"
* [Fixed] Quickedit custom status doesn't show up
* [Fixed] Non public posts won't show up in admin "All" list
* [Removed] Posts submenu item 'Status'

= 1.0.7 =
* [Fixed] Label of settings doesn't work
* [Fixed] PHP errors, wrong class declaration

= 1.0.6 =
* [Fixed] PHP errors, wrong class declaration

= 1.0.5 =
* [Fixed] New posts were saved as drafts instead of published
* [Fixed] Translation errors

= 1.0.4 =
* [Added] Settings section in "Settings > Wrtiting"

= 1.0.3 =
* [Added] Support for all post types

= 1.0.2 =
* [Added] Page and post count in status overview
* [Fixed] Bug that removes all status settings when quickediting a status
* [Fixed] Set slug length to max 20 chars because of posts status db field length

= 1.0.1 =
* [Added] Page status support
* [Fixed] Bug in displaying posts/pages in 'all' list

= 1.0.0 =
* [Added] Initial version
