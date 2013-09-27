=== Prepare New Version ===
Contributors: GLOBALIS media systems
Tags: copy, duplication, clone, editing, replacement, workflow, update, anticipate, post, page, version
Requires at least: 3.5
Tested up to: 3.6.1
Stable tag: 0.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Create the next version of your post while preserving its already online version.

== Description ==

Sometimes happiness is about small things. What about being able to prepare a new version of your content while its original is still online?

Anticipating and preparing the next version of an article which is already online can quickly become a tedious and risky task. Indeed, most publishers will create a brand new post, fill it in, publish it once it's ready and finally disable the original article.


= Basic features: =
* creation of a new version of an article in 1 click;
* direct access to other pending versions of an article;
* update of an article to its new version in 1 click.

= Advanced features: =
* duplication of an article into a brand new independent copy;
* hookable ;-)
* works with any post types, custom or not
* makes coffee (we’re still working on that)


== Installation ==


You can install Prepare New Version using the built in WordPress plugin installer. It’s easy, 2 seconds.

If you prefer download Prepare New Version manually :

1. Upload prepare-new-version (URL) to the /wp-content/plugins/ directory.
1. Activate the plugin through the Plugins menu in WordPress.
1. A new “Pending versions” box is now available when creating/editing a post/page.
1. That's it. You're ready to go!


== Frequently Asked Questions ==

= Which languages are currently supported? =
As of now, Prepare New Version is available in English and in French. If you wish to, you can translate the interface in your own language in the [standard WordPress way] http://codex.wordpress.org/Translating_WordPress

= Do the duplicated posts appear on the front side of my site? =
No, they won't appear on front since they are meant to be 'draft' versions of contents.
Only copies will appear since they are real copies and create a real post.

= Is the plugin compatible with all the post types, even the ones I created? =
Yes. The plugin manages all built-in and custom post types.

= GLOBALIS what ? =
[GLOBALIS](http://www.globalis-ms.com/) is a web IT consulting company based in Paris, and a pioneer of the PHP and LAMP platform. Since 1997, we have been designing, making and maintaining Internet, intranet or mobile software. We have been working with open source CMS since 2000 and have regularly been using WordPress since 2007.

= Why should I use Prepare New Versions? =
Prepare New Version makes your life easier by letting you focus on your primary activity: Writing. You don’t need to handle permalinks, content replacement, or things that would usually be tedious and maybe not without risk. With Prepare New Versions, all of these are being taken care of!

= How do I create a new version of a post? =
New versions are created from the post editing screen. It can be either an existing post or a pending version of one.

= When I duplicate a post, what’s the status of the newly created copy? =
New posts which are copied from existing posts are automatically created with a Draft status. They are independent of the original post and can be published whenever you like.

= How can I quickly see how many pending versions have been created for each post? =
When you activate the Prepare New Versions plugin, a new column “Pending versions” appears in last position, in the Posts list. You can then access these versions by editing the original post and scrolling to the “Pending versions” box.

= When I prepare a new version, the permalink differs from the original post’s. What’s going to happen when I update the original post? =
When you click the “Update original” button, the permalink is not going to be altered, and will remain the same. This means it won’t change anything for your blog visitors, except for the updated content.

= How can I remove pending versions I don’t use anymore? =
When you update an original post, previous pending versions are not deleted and will remain available. However, you have 2 ways to remove them:
* go to the pending version editing screen and click “Move to trash” in the “Publish” box:
* go the the Posts list and click the “Pending versions” link.You will now access a list of all pending versions and will be able to remove one or several at a time.

== Screenshots ==

1. **New handy box in editing view** - When creating/editing a post or a page, users get access to a new box to prepare a new version or create a copy of the document with only **one click** !
2. And when you're ready to update the current online article, it will just take you one more click. Easy as a pie!
3. Create a new post as usual. When you’re done,  you can of course publish it (A) or directly create a new version (B), or even create a copy (C). If you directly create a new version or a copy, the post is saved as a draft.
4. After you prepared a new version, the “Pending versions” block now looks like the picture below (D). To access the pending versions list, you first need to edit the original post, or one of its pending versions.
5. Once you clicked on a pending version of a post, you can edit it as a normal one. Inside the “Publish” block, hit the save button (E) to save the pending version you’re working on, or the preview button (F). To delete the pending version, click on the link “Move to trash” (H). If you want to replace the current version of the original post with the pending version click on the button “Update original” (G). You can also directly move to another pending version by clicking the link (I), create another new version (J) or create a complete new post (K) based on the current pending version.
6. **Preparing a new version** - A copy of the article is created. "[New version]" is automatically prepended to its title and a link to the original article is available at the top of the editing form. The created post is completely independent of the original. It is in a Draft status and "[Copy]" is automatically prepended to its title.
7. In the Posts list, a new column “Pending version” appears in last position.

== Changelog ==

= 0.9 =
initial public version released

== About ==

= Hooks =

**pnv_duplicata_status_args**
Filter that can be used to alter arguments sent to 'register_post_status'.
Default arguments are:

* label => Duplicate

* public => false

* exclude_from_search => true

* show_in_admin_all_list => false

* label_count =>

* * singular => Duplicate <span class="count">(%s)</span>

* * plural => Duplicates <span class="count">(%s)</span>

**pnv_erase_content_destination**
Filter that can be used to alter $destination array before 'wp_insert_post' get called when a copy, duplication or replacement is triggered
This filter sends 3 other parameters: the $destination post array, the $source object (WP_Post instance) and the $action string

**pnv_filtered_metas**
Filter that can be used to alter meta names that must not be treated while a post is copied, duplicated or replaced
The default metas is an array with these values:

* _pnv_duplicata => true

**pnv_erase_content**
Action triggered at the end of a copy, duplication or replacement.
This action sends 4 parameters:

* the $source object (WP_Post instance)

* the $destination array (represents post data)

* the $copy boolean

* the destination's post ID

**pnv_action_url_redirect**
Filter that can be used to alter redirect URL after a copy, duplication or replacement.
The default value is the admin edit page for the destination post (the one that has been saved)
This filter sends 1 other parameter: the destination post ID
pnv_{column_name}_column_value
Filter that can be used to alter the value displayed on the posts list, in the '{column_name}' column.
This plugin comes with only one custom column for now, 'duplicata', so {column_name} will always be 'duplicata', and the default value will be the count of duplicates for the post.
This filter sends 1 other parameter: the post ID


= Thank’s =

The original version of this plugin has been developed by Lionel POINTET (https://github.com/lpointet) who keeps following the project carefully.

A big thank-you to [Groupe Moniteur](http://www.groupemoniteur.fr/) for which a great part of this development was intended and which accepted enthusiastically to do a completely open source plugin from it. Thank you to [Uncategorized Creations](http://uncategorized-creations.com/) people, for their regular advice and their perseverance in making WordPress a leading CMS.

= GLOBALIS =

[GLOBALIS](http://www.globalis-ms.com/) is a web IT consulting company based in Paris, and a pioneer of the PHP and LAMP platform. Since 1997, we have been designing, making and maintaining Internet, intranet or mobile software. We have been working with open source CMS since 2000 and have regularly been using WordPress since 2007.

