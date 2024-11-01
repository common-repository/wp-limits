=== Plugin Name ===
Contributors:wpvirtuoso
Tags: memory, memory limit, time limit, max upload filesize, demo import failed, php limits
Tested up to: 5.8
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==
 
The default WordPress limits are sometimes not enough, especially if you have a lot of plugins installed. 

WPL  allows you to increase the wordpress system limits without editing any WordPress files.

increase your 

-Memory Limit

-Max upload FileSize

-Time limit

== Installation ==

1.) Upload 'wp-limits/change-wp-limits.php' to the '/wp-content/plugins/' directory.
2.) Activate the plugin through the 'Plugins' menu in WordPress.
3.) You'll automatically be forwarded to limit options.

== Frequently Asked Questions ==

= Why is this plugin necessary? =

The default WordPress limits are sometimes not enough, especially if you have a lot of plugins installed. This plugin allows you to increase the memory limit without editing any WordPress files.

= What is an appropriate limit to set? =

Most blogs are perfectly happy with a 64Mb limit. The plugin uses 64Mb as a default (if you haven't already set it higher by some other means).

= Why doesn't it work? =

Your host may prevent PHP from increasing its own memory limit. Please speak to your web host about "changing the default php memory limit".

== Changelog ==

= 1.0 =
* Release.