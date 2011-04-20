=== Olimometer ===
Contributors: oshingler
Donate link: http://www.olivershingler.co.uk/oliblog/olimometer
Tags: fundraising, thermometer, fundraising thermometer, olimometer, sponsor, sponsorship, charity, donation, donate
Requires at least: 3.1
Tested up to: 3.1

A dynamic fundraising thermometer with customisable height, currency and background colour or transparency.

== Description ==

A dynamic fundraising thermometer with customisable height, currency and background colour or transparency.

The background of the thermometer image (the part with the text labels on) can be customised to fit in with your site's design. The height (or length?!?) of the thermometer can be customised, and the scale of the mercury bar will automatically asjust accordingly.

Also includes a widget for use on sidebars.

This plugin requires that the GD libraries are available within PHP.

If you wish the thermometer to have a transparent background then I suggest you still choose a background colour close to the actual background on which the thermometer will appear. This will ensure that the image blends in perfectly.

If you have *any* problems installing or using this plugin, or if it doesn't quite meet your needs then please let me know. Visit http://www.olivershingler.co.uk/oliblog/olimometer and leave a comment, or via Twitter @OliverShingler

== Installation ==

Either install using the automatic WordPress plugin installation method, or do it manually:

1. Upload all files to to the `/wp-content/plugins/olimometer` directory (create it if it doesn't exist) within your Wordpress Installation
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the settings to your personal requirements
4. Place `<?php show_olimometer();?>` in your templates or [show_olimometer] in a post.

== Frequently Asked Questions ==

= The thermometer image won't show (or shows up as a red cross) =

You may not have the GD libraries installed as part of PHP, you'll need to speak to your web server administrator

= I've found a bug / have a suggestion =

You can contact me via Twitter @OliverShingler or you can leave a comment on the plugin's official page http://www.olivershingler.co.uk/oliblog/olimometer
I can't make any promises but I will do my best.

== Screenshots ==

1. The Olimometer in a sidebar widget
2. The configuration interface (as of v1.0)

== Changelog ==

= 1.0 =
* The initial release

= 1.1 =
* Thermometer now includes an optional transparent background

= 1.2 =
* Tidied up the variables within the code
* Resolved overlapping text issue by making the progress value optional
* Progress value is now displayed under the thermometer image if required
* Optional text field can be displayed alongside progress value
* Font size can be adjusted
* Width of the image can be adjusted to cater for larger fonts
* Tidied up the lower border of the thermometer image
* Amended screenshot 1 to reflect the new changes

= 1.21 =
* Fixed default currency, now defaults to GBP

= 1.22 =
* It is now possible to add a suffix (for example a % symbol) at the end of your values.
* Currency symbol is now optional and defaults to nothing

== Upgrade Notice ==

= 1.0 =
The initial release

= 1.1 =
Thermometer now includes an optional transparent background

= 1.2 =
Fixed the overlapping text bug where the progress value would overlap with the target and starting values. Font size is also adjustable now and I've tidied up the thermometer image's border too.

= 1.21 = 
Fixed default currency, now defaults to GBP - not a critical update at all if you already have it installed as this only affects new installations.

= 1.22 =
I noticed that some people were using the Olimometer to track a percentage, so this version allows you to add a suffix (for example a % symbol) at the end of your values. Currencies are now also optional as a consequence.