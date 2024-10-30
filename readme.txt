=== Listing Posts Type ===
Contributors: albertochoa
Tags: recent post, post, post type, custom post types, sidebar, widget, template, recent
Requires at least: 3.1
Tested up to: 5.0

== Description ==

*Listing Posts Type* display the Custom Posts Type in the sidebar with a Function or Widget.

It gives you a new template tag called `listing_posts_type()` that you can place anywhere in your sidebar template.

= Localization =

* English - (en_EN)
* Spanish - (es_ES)
* Spanish - (es_MX)

== Installation ==

1. Upload `listing-posts-type` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Add the appropriate code to your template files

== Frequently Asked Questions ==

= How do I add it to my theme? =

You would use this call:

`
<?php if ( function_exists( 'listing_posts_type' ) ) listing_posts_type(); ?>
`

== Changelog ==

= 0.3.1 =
* Code cleanup

= 0.3.0 =
* Tested in WordPress 5.0
* Code cleanup

= 0.2.1 =
* Tested in WordPress 3.5
* Cleaned up the code
* Update plugin data

= 0.2 =
* Tested in WordPress 3.3
* Cleaned up the code

= 0.1 =
* First public release
