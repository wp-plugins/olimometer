=== Olimometer ===
Contributors: oshingler
Donate link: http://www.olivershingler.co.uk/oliblog/olimometer
Tags: charities, charity, counter, donate, donate goals, donate meter, donation, donations, fund, fundraise, fundraising, fundraising goal, fundraising thermometer, goal, olimometer, paypal, progress, sidebar, sponsor, sponsorship, thermometer, widget
Requires at least: 3.1
Tested up to: 3.1.2
Stable tag: trunk

A dynamic fundraising thermometer with customisable height, currency, background colour, transparency and skins.

== Description ==

A dynamic fundraising thermometer with customisable height, currency, background colour, transparency and skinnable images.

The background of the thermometer image (the part with the text labels on) can be customised to fit in with your site's design. The height (or length?!?) of the thermometer can be customised, and the scale of the mercury bar will automatically adjust accordingly. The thermometer image can be customised using one of the preset skins, or by creating your own. See the FAQ for guidelines on creating your own skins. Don't forget to share your skins with me so I can include them in a future release. Also includes a widget for use on sidebars and a dashboard widget for quick updating of the progress and target values directly from the admin dashboard.

This plugin requires that the GD libraries are available within PHP.

If you wish the thermometer to have a transparent background then I suggest you still choose a background colour close to the actual background on which the thermometer will appear. This will ensure that the image blends in perfectly.

If you have *any* problems installing or using this plugin, or if it doesn't quite meet your needs then please let me know. Visit http://www.olivershingler.co.uk/oliblog/olimometer and leave a comment, or via Twitter @OliverShingler
 

== Installation ==


Either install using the automatic WordPress plugin installation method, or do it manually:


1. Upload all files to to the `/wp-content/plugins/olimometer` directory (create it if it doesn't exist) within your Wordpress Installation
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the settings to your personal requirements
4. Place `<?php echo show_olimometer();?>` in your templates or [show_olimometer] in a post.


== Frequently Asked Questions ==


= The thermometer image won't show (or shows up as a red cross) =

You may not have the GD libraries installed as part of PHP, you'll need to speak to your web server administrator


= I've found a bug / have a suggestion =

You can contact me via Twitter @OliverShingler or you can leave a comment on the plugin's official page http://www.olivershingler.co.uk/oliblog/olimometer. I can't make any promises but I will do my best.

= How do I change the thermometer image? =
See: How do I create my own skin?

= How do I create my own skin? =
Within the plugin folder you'll find a file called skins.xml. This file contains the skin defintions for all available Olimometer skins. The easiest way to create your own skin is to copy and paste an existing one changing the values as appropriate. You need to create an entire 'skin' object within the XML structure. Each sub-object within the skin object is explained below:

skin_name:	The name of the skin as displayed in the drop-down list on the settings page

skin_folder:	The name of a subfolder within the plugin's skin folder in which you will store the thermometer images associated with this skin

bulb_file:	The name of the PNG image file of the thermometer 'bulb' (bottom image)

bar_file:	The name of the PNG image file of the thermometer 'bar'. This should be empty of mercury.

top_file:	The name of the PNG image file of the top of the thermometer.

bar_colour:	A six character RGB hex value (e.g. white = ffffff) colour of the mercury bar

bar_xpos:	The number of pixels from the left of the image at which the mercury bar will be drawn.

bar_width:	The width of the mercury bar in pixels.

bar_top:	The number of pixels from the top of the image where the mercury bar will stop at 100%

text_xpos:	The number of pixels from the left of the image at which the text values are placed.


Once you've created your XML object, you'll then need to create a folder (as specified in skin_folder above) in wp-content/plugins/olimometer/skins/<yourfoldername>.
Then create yourself three new files, naming them according to that specified in the XML file. One image should contain the bottom of the thermometer (the bulb), the second should be the thermometer's bar which should be drawn empty of mercury and the third is the top of the thermometer.

For best results follow these tips:

* Each image file should be of equal width.

* The background of each image should be an identical solid colour to enable seamless placement and transparency.

* Use a droplet tool or other suitable colour identifier to identify a suitable bar_colour value using the mercury colour in your bulb_file image.

* Make sure the top_file image includes a copy of the bar_file underneath it, but cleaned of any marks or bars - see the built in skins for an example. This is to avoid a gap being left between the top image and the bar image at certain image heights.

* Share your skins with me - contact me using the Wordpress forums, tweet me @OliverShingler, or leave a comment on the Olimometer page: http://www.olivershingler.co.uk/oliblog/olimometer

* Keep a backup of skins.xml and your image files. When you update the Olimometer it will overwrite any skins you have created, this is why it is important to share them with me if possible to be included in a future release.





== Screenshots ==

1. The Olimometer in a sidebar widget - default skin
2. The configuration interface (as of v1.0)
3. Skin: Red and Rounded
4. The Dashboard Widget
5. Bold and Chunky

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

= 1.23 =
* Fixes a bug which caused progress values (total amount raised) of 9 or less to be displayed as 0.

= 1.30 =
* Added a dashboard widget on the admin interface. You can now quickly update your progress or target values on the dashboard directly.
* The thermometer image can now be customised using skins. See FAQ for current skinning guidelines.

= 1.31 =
* Fixed overlapping image bug in the skins. Thermometer top images are now larger to compensate.

= 1.32 =
* The show_olimometer shortcode now operates correctly fixing a bug which prevented the thermometer image from being displayed inline with the contents of a post of page. IMPORTANT: If you have used the show_olimometer() function within your templates, this must now be updated to "echo show_olimometer()" (note the extra echo) or the image will not be displayed. This is because I didn't read the Wordpress documentation on how to create a plugin properly when I first wrote the plugin


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

= 1.23 =
Fixes a bug which caused progress values (total amount raised) of 9 or less to be displayed as 0.

= 1.30 =
* Added a dashboard widget on the admin interface. You can now quickly update your progress or target values on the dashboard directly.
* SKINS! The thermometer image can now be customised using skins. See FAQ for current skinning guidelines.

= 1.31 =
Fixed overlapping image bug in the skins.

= 1.32 =
The show_olimometer shortcode now operates correctly fixing a bug which prevented the thermometer image from being displayed inline with the contents of a post of page.