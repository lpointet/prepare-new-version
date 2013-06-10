=== Advanced Page Manager ===
Contributors: Lionel Pointet
Tags: duplication, copy, admin, cms, content management
Requires at least: 3.6.0
Tested up to: 3.6.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily create some copies and duplicates of one content from any types

== Description ==

> This plugin is in beta stage.

== Installation ==

> This plugin is in beta stage.

WP Post Duplication doesn't require specific action to be installed. Just follow the regular process :

1. Upload `wp-post-duplication` to the `/wp-content/plugins/` directory

2. Activate the plugin through the *Plugins* menu in WordPress

== Frequently Asked Questions ==

> This plugin is in beta stage.

= This plugin is beta. What does that mean? =
WP Post Duplication is fresh out of the box. We have tested it but it is young and for sure have bugs. We are going to work hard to have it clean shortly.

= Do the duplicated posts appear on the front side of my site? =
No, they won't appear on front since they are meant to be 'draft' versions of contents. Only copies will appear since they are real copies and create a real post.

= Is the plugin compatible with all the post types, even the ones I created? =
Yes. The plugin manages all built-in and custom post types.

== Screenshots ==

> This plugin is in beta stage.

== Changelog ==

> This plugin is in beta stage.

== Hooks ==

> This plugin is in beta stage.

= wppd_duplicata_status_args =
Filter that can be used to alter arguments sent to 'register_post_status'.
Default arguments are:
 - label => Duplicate
 - public => false
 - exclude_from_search => true
 - show_in_admin_all_list => false
 - label_count =>
    - singular => Duplicate <span class="count">(%s)</span>
    - plural => Duplicates <span class="count">(%s)</span>

= wppd_erase_content_destination =
Filter that can be used to alter $destination array before 'wp_insert_post' get called when a copy, duplication or replacement is triggered
This filter sends 2 other parameters: the $source object (WP_Post instance) and the $copy boolean

= wppd_filtered_metas =
Filter that can be used to alter meta names that must not be treated while a post is copied, duplicated or replaced
The default metas is an array with these values:
 - _wppd_duplicata => true

= wppd_erase_content =
Action triggered at the end of a copy, duplication or replacement.
This action sends 4 parameters:
 - the $source object (WP_Post instance)
 - the $destination array (represents post data)
 - the $copy boolean
 - the destination's post ID

= wppd_action_url_redirect =
Filter that can be used to alter redirect URL after a copy, duplication or replacement.
The default value is the admin edit page for the destination post (the one that has been saved)
This filter sends 1 other parameter: the destination post ID

= wppd_{column_name}_column_value =
Filter that can be used to alter the value displayed on the posts list, in the '{column_name}' column.
This plugin comes with only one custom column for now, 'duplicata', so {column_name} will always be 'duplicata', and the default value will be the count of duplicates for the post.
This filter sends 1 other parameter: the post ID