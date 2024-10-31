=== Selected Tags RSS ===
Contributors: MTR Design
Donate link: http://mtr-design.com/
Tags: rss, tags
Requires at least: 3.0
Tested up to: 3.4.2
Stable tag: 1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Enable separate RSS feeds covering different combinations of tags. Each feed will only list entries that match at least one of the feed's tags. 

== Description ==

This plugin represents a RSS feed separator, which enables your readers to create RSS subscriptions that only contain posts with specific tags. 
For example, visitor A could create and subscribe to an RSS feed including the tags *cooking*, *recipes*, and *fine living*. Visitor B, could 
instead prefer to read posts related to the tags *cocktails*, *wine*, and *night life*. Each person will have it's own RSS Feed URL, and each of 
those feeds will only include posts that have one or more of the preselected tags. 

If you have a busy blog with lots of entries in it, you should definitely consider installing Selected Tags RSS, in order to enable your visitors
to subscribe just to the topics they are interested in.

You can configure the location where the the RSS selector would be showing from the plugin settings. If your theme supports widgets you can add and configure a widget from *Appearance > Widgets > Selected Tags RSS*.

The plugin supports shortcodes. Use `[selected-tags-rss-post title="***"]` shortcode to show RSS link in posts and `[selected-tags-rss-search title="***"]` to show the RSS Selector. Title parameter is the optional name of link.

[Plugin homepage](http://mtr-design.com/) 

Features:

* A different Feed URL for each combination of tags
* Simple administration interface

*Take the time to read the user guide.*

== Installation ==

= Automatic = 
1. Automatic Plugin Installation
1. Go to Plugins > Add New > Upload. 
1. Click on the Browse button and select the .zip archive from your local computer.
1. Click Install Now to install the WordPress Plugin. 
1. If this is the first time you've installed a WordPress Plugin, you may need to enter the FTP login credential information. If you've installed a Plugin before, it will still have the login information. This information is available through your web server host.
1. Click Proceed to continue with the installation. The resulting installation screen will list the installation as successful or note any problems during the install.
1. If successful, click Activate Plugin to activate it, or Return to Plugin Installer for further actions. 

= Manual =
1. Download your WordPress Plugin to your desktop. 
1. If downloaded as a .zip archive, extract the Plugin folder to your desktop. 
1. With your FTP program, upload the Plugin folder to the wp-content/plugins folder of your WordPress installation. 
1. Go to Plugins screen and find the newly uploaded Plugin in the list. 
1. Click Activate Plugin to activate it. 

== Usage ==

= Adding the RSS Selector to the WordPress Sidebar =
2. The plugin provides a widget called "Selected Tags RSS". You can add this widget to your sidebar or other widget zone via Appearance > Widgets.

= Adding the RSS Selector to a WordPress Page =
1. Click on the "Selected Tags RSS" link in the Wordpress administration menu to access the plugin settings.
1. Use the "Selected Pages" widget to Activate the RSS Selector for the desired pages by moving them from the left list to the right one.
1. Another option is to edit the page and use the shortcode following shortcode (title is optional): `[selected-tags-rss-search title="Generate RSS feed with these tags"]`

= Adding the RSS Link to WordPress Posts =
3. The plugin provides a global setting called "Show RSS Link in Posts", which is found at the "Selected Tags RSS" settings page in the Wordpress administration menu. Enabling this setting will result in a special link being added to all Posts 
3. Another option is to use the following shortcode in your Post (title is optional): `[selected-tags-rss-post title="Generate RSS feed with these tags"]`
3. Yet another option is to edit your Posts template and call the function *get_selected_tags_RSS()*, as in: `<?php echo get_selected_tags_RSS(); ?>`. 
3. The automatically generated RSS Link text can be edited vie the plugin settings.

== Frequently Asked Questions ==
 
= How to style plugin? =
* You can style the plugin markup by adding the following styles to your css theme file:

> Markup of RSS Selector:
> 
>     .mtr_rss_tag_search {}
>     .mtr_rss_tag_search h3.title {}
>     .mtr_rss_tag_search input.search {}
>     .mtr_rss_tag_search div.tag_cloud {}
>     .mtr_rss_tag_search div.tag_cloud a.select_all_tags {}
>     .mtr_rss_tag_search div.tag_cloud span a {}
>     .mtr_rss_tag_search div.selected_tags {}
>     .mtr_rss_tag_search div.selected_tags a.remove_all_tags {}
>     .mtr_rss_tag_search div.selected_tags span a {}
>     .mtr_rss_tag_search div.rss {}
>     .mtr_rss_tag_search div.rss a {}
>


> Markup of RSS Link:
>
>     .mtr_rss_tag_search {}
>     .mtr_rss_tag_search div.rss {}
>     .mtr_rss_tag_search div.rss a {}

> Markup of RSS sidebar widget:
> 
>     .widget_mtr_selected_tags_rss_search {}
>     .widget_mtr_selected_tags_rss_search .mtr_rss_tag_search {}
>     .widget_mtr_selected_tags_rss_search .mtr_rss_tag_search input.search {}
>     .widget_mtr_selected_tags_rss_search .mtr_rss_tag_search div.tag_cloud {}
>     .widget_mtr_selected_tags_rss_search .mtr_rss_tag_search div.tag_cloud a.select_all_tags {}
>     .widget_mtr_selected_tags_rss_search .mtr_rss_tag_search div.tag_cloud span a {}
>     .widget_mtr_selected_tags_rss_search .mtr_rss_tag_search div.selected_tags {}
>     .widget_mtr_selected_tags_rss_search .mtr_rss_tag_search div.selected_tags a.remove_all_tags {}
>     .widget_mtr_selected_tags_rss_search .mtr_rss_tag_search div.selected_tags span a {}
>     .widget_mtr_selected_tags_rss_search .mtr_rss_tag_search div.rss {}
>     .widget_mtr_selected_tags_rss_search .mtr_rss_tag_search div.rss a {}

== Screenshots ==

No screen shots are available at this time.

== Changelog ==

= 1.2 =
 * Fixed URL handling

= 1.3 = 
 * Fixed ordering

== Upgrade Notice ==

None.
