=== Generate Post Thumbnails ===
Contributors: marynixie
Donate link: http://wordpress.shaldybina.com/donate
Tags: thumbnails, thumbnail
Requires at least: 2.9
Tested up to: 3.1
Stable tag: 0.8

Tool for mass generation of Wordpress posts thumbnails using the post images.

== Description ==

This plugin generates Featured Image for all posts at once.

It is based on WordPres 2.9+ feature - Post Thumbnails. This plugin takes the first image (by default) from post body and assignes it as a post Featured Image. It helps if your theme supports post thumbnails feature and you want to assign Featured Image for all your existing posts.

Plugin performs mass manipulation on posts Featured Image property and uploads files if they are stored externally. Don't forget to backup your files and database before using this plugin. 

It displays log of success/fails. There is also a feature to remove all Featured Images from all posts - this only changes post setting, no actual files are removed.

Related Links:

* <a href="http://wordpress.shaldybina.com/plugins/generate-post-thumbnails/" title="Generate Post Thumbnails Plugin for WordPress">Plugin Homepage</a>

== Installation ==

1. Extract zip in the /wp-content/plugins/ directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Open the plugin management page, which is located under Tools -> Generate Thumbnails. If you get plugin warning, that means either your theme does not support Wordpress Post Thumbnails feature or your Wordpress version is lower than 2.9. See related links for more information.
1. Set overwrite parameter, if you want existing post thumbnails to be overwritten by generated thumbnails.
1. Set the number of the post image, that you want to be used as your post thumbnail.
1. Click on Generate Thumbnails and wait until process is finished.

== Frequently Asked Questions ==

= What image will be used as post thumbnail? =

You can specify image number that will be used as post thumbnail. By default it takes the first image in the post body. If this image was uploaded on server plugin assigns it as a post thumbnail. If the image is externally hosted, plugin will upload it on server, attach to post and assign as thumbnail.

If there is no image for specified image number in the post, then no thumbnail will be stored for this post. If *Overwrite* parameter is checked, then if the post already has thumbnail old thumbnail will be removed. 

== Screenshots ==

1. Plugin management page

== Changelog ==

= 0.8 =
* Tested with WP 3.1
* Added url decoding
* Added live log 
* Removing thumbnails feature

= 0.7 =
* Fixed the bug when external images contain query parameters

= 0.6 =
* Uploading of externally hosted images tries several methods for different configurations
* Relative paths support fixed

= 0.5 =
* Added support of externally hosted images

= 0.4.1 =
* Released plugin initial version

== Upgrade Notice ==

= 0.8 =
* Working with WP 3.1, new features - live log and removing thumbnails

= 0.7 =
* Fixed the bug when external images contain query parameters

= 0.6 =
This version uses different methods to upload externally hosted images for different configurations and supports relative paths to images

= 0.5 =
This version supports externally hosted images

= 0.4.1 =
The first released version

