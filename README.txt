=== Geolocation2 ===
Contributors: frsh, mdawaffe, automattic, jhanggi
Tags: geolocation, maps, geotag
Requires at least: 2.9.2
Tested up to: 3.2
Stable tag: 0.2.0

Display a map showing your post, tags, or category's geo location.  Geotag your post from the Edit Post screen or with any geo-enabled WordPress mobile application.

== Description ==

The Geolocation plugin allows you to geotag your posts using the Edit Post page or with any geo-enabled WordPress mobile application such as WordPress for iPhone, WordPress for Android, or WordPress for BlackBerry.

Visitors see a map of the address either before, after, or at a custom location within the post. You can also geotag tags and categories.

There is a widget that you can set to display all your geotagged posts, tags, or categories. Within a tag or category, the widget display all posts in that tag/category.

Forked from Chris Boyd's Geolocation plugin http://plugins.svn.wordpress.org/geolocation/

== Installation ==

1. Upload the `geolocation` directory to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Modify the display settings as needed on the Settings > Geolocation page.

For geotagging categories and tags, make sure you have installed the Taxonomy Metadata plugin
http://wordpress.org/extend/plugins/taxonomy-metadata/
or another plugin that implements get_term_meta(), update_term_meta(), etc.

== Theme Usage ==

The easiest way to use this plugin is to add the Geolocation widget.

You can also include a map of all posts by including the following in your theme:
<?php geolocation_map_all_posts($options); ?> 
	
If you have a taxonomy metadata plugin installed (i.e. it defines get_term_meta()) you can use the following to include a map of all the geotagged tags or categories:
geolocation_map_all_tags($options); 
geolocation_map_all_categories($options);

Default options:
$options = array(
	'width' => '400px',
	'height' => '400px',
	'zoom' => 6
)
== Screenshots ==

1. screenshot-1.png
2. screenshot-2.png

== Changelog ==

= 0.2.0 =
* Supports multiple maps (from multiple posts) on a single page
* Display the map always, as opposed to on hover
* Refactoring of code into multiple files
* Geotagging tags and categories (assuming you have a taxonomy metadata plugin)
* Widget that can display all geotagged posts, tags, and categories

= 0.1.1 =
* Added ability to turn geolocation on and off for individual posts.
* Admin Panel no longer shows up when editing a page.
* Removed display of latitude and longitude on mouse hover.
* Map link color now defaults to your theme.
* Clicking map link now (properly) does nothing.

= 0.1 =
* Initial release.
