=== Olimometer ===
Contributors: oshingler
Donate link: http://www.olivershingler.co.uk/oliblog/olimometer
Tags: charities, charity, counter, donate, donate goals, donate meter, donation, donations, fund, fundraise, fundraising, fundraising goal, fundraising thermometer, goal, olimometer, paypal, progress, sidebar, sponsor, sponsorship, thermometer, widget
Requires at least: 3.1
Tested up to: 3.3.2
Stable tag: trunk

A fully customisable fundraising thermometer with PayPal integration, custom skins and support for multiple thermometers throughout your site.

== Description ==

A fully customisable fundraising thermometer with PayPal integration and custom skin support.

Multiple thermometers can be configured and displayed separately on different pages or posts. Each thermometer supports individual customisation, targets, currencies and progress tracking.

Thermometers can be placed in sidebar widgets with custom headers, footers and CSS classes.

Choose from a number of pre-installed thermometer skins, or create your own and share it.

This plugin requires that the GD libraries are available within PHP.

If you wish the thermometer to have a transparent background then I suggest you still choose a background colour close to the actual background on which the thermometer will appear. This will ensure that the image blends in perfectly.

If you have *any* problems installing or using this plugin, or if it doesn't quite meet your needs then please let me know. Visit http://www.olivershingler.co.uk/oliblog/olimometer and leave a comment, or via Twitter @OliverShingler
 

== Installation ==


Either install using the automatic WordPress plugin installation method, or do it manually:


1. Upload all files to to the `/wp-content/plugins/olimometer` directory (create it if it doesn't exist) within your Wordpress Installation
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the settings to your personal requirements
4. Place `<?php echo show_olimometer(olimometer_id,olimometer_css);?>` in your templates or [olimometer id=olimometer_id css_class=olimometer_css] in a post, where olimometer_id = the Olimometer's id found on the settings page and olimometer_css = an optional string containing the name of the CSS class to apply to the image.


== Frequently Asked Questions ==


= The thermometer image won't show (or shows up as a red cross) =

You may not have the GD libraries installed as part of PHP, you'll need to speak to your web server administrator

= Can I apply my own CSS to the Olimometer image? =

Yes! When you call the Olimometer, simply pass in the name of the CSS class you wish to use as a parameter.
For example, to use a class called align_right, use the following code:

`<?php echo show_olimometer(1,'align_right');?>` in your templates or [olimometer id=1 css_class="align_right"] in a post replacing 1 with the actual id of your Olimometer.

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

1. The Olimometer in a sidebar widget with default skin
2. The Red and Rounded skin in a widget with a header
3. Olimometer settings page


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
* The show_olimometer shortcode now operates correctly fixing a bug which prevented the thermometer image from being displayed inline with the contents of a post or page.

= 1.33 =
* Disabled error logging on thermometer.php to prevent buildup of large error_log file due to direct access from crawlers without correct parameters
* Replaced HTTP_GET_VARS with _GET which is what I should have done in the first place. Fixes a problem which prevents the Olimometer from displaying on a default PHP installation.
* Automatically detects for the presence of the PHP GD extension upon which this plugin is dependent and displays a message on the admin page.

= 1.40 =
* PayPal integration: Progress value can optionally be updated by retrieving the balance from a PayPal account.
* Tidied up administration page.
* It can now cope if the progress value exceeds the target value.

= 1.41 =
* Made the PayPal integration code a bit more efficient. Fewer lookups to the PayPal API and reduced unnecessary database writes.

= 1.42 =
* Minor bug fix affecting the widget
* Added Watermaster skin courtesy of http://www.fscinternational.com
* Widget now supports custom header and footer text or HTML

= 1.43 =
* Fixed bug in Widget header and footer when using HTML. Characters now escape correctly.

= 1.50 =
* Support for decimal places in displayed values. Olimometer now no longer rounds to the nearest whole number.
* Prefix and suffix values can be selected from a drop-down list.

= 1.51 =
* Minor bug fix for PHP running on IIS
* Re-jigged the widget layout to conform to latest Wordpress standards. This removes the annoying dot in the widget that some people have noticed.
* Widget settings are per widget now and no longer stored amongst the Olimometer settings
* The CSS class for the Olimometer is now set via shortcode or function parameters. See the FAQ for details.
* These changes are all in preparation of a rework to allow multiple Olimometers on a blog

= 2.00 =
* Multiple Olimometers now supported with independent progress tracking.
* Added extra currency and suffix symbols (Yen, Cent, Pence, Lira, Pesetas, Degree)
* Automatic width calculation of the thermometer image - no more manual guesswork
* Target values can now be enabled and disabled
* Shortcode is now [olimometer] to reduce confusion
* Colour picker installed to allow you to easily choose font and background colours
* Preview your Olimometers on the settings page
* Added 'Our Progress' styled thermometer skins in blue, red and green.

= 2.02 =
* Quick bug fix to prevent data loss when upgrading.

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
The show_olimometer shortcode now operates correctly fixing a bug which prevented the thermometer image from being displayed inline with the contents of a post or page.

= 1.33 =
Disabled error logging on thermometer.php to prevent buildup of large error_log file due to direct access from crawlers without correct parameters.
Fixes a problem which prevents the Olimometer from displaying on a default PHP installation.
Automatically detects for the presence of the PHP GD extension upon which this plugin is dependent and displays a message on the admin page.

= 1.40 =
PayPal integration: Progress value can optionally be updated by retrieving the balance from a PayPal account. Tidied up administration page and finally it can now cope if the progress value exceeds the target value.

= 1.41 =
Made the PayPal integration code a bit more efficient. Fewer lookups to the PayPal API and reduced unnecessary database writes. This should speed up web page loading times when using the PayPal option as your progress value.

= 1.42 =
* Minor bug fix affecting the widget
* Added Watermaster skin courtesy of http://www.fscinternational.com
* Widget now supports custom header and footer text or HTML

= 1.43 =
* Fixed bug in Widget header and footer when using HTML. Characters now escape correctly.

= 1.50 =
* Support for decimal places in displayed values. Olimometer now no longer rounds to the nearest whole number.
* Prefix and suffix values can be selected from a drop-down list.

= 1.51 =
* Minor bug fix for PHP running on IIS
* Re-jigged the widget layout to conform to latest Wordpress standards. This removes the annoying dot in the widget that some people have noticed.
* Widget settings are per widget now and no longer stored amongst the Olimometer settings
* The CSS class for the Olimometer is now set via shortcode or function parameters. See the FAQ for details.
* These changes are all in preparation of a rework to allow multiple Olimometers on a blog

= 2.00 =
* Multiple Olimometers now supported with independent progress tracking.
* Added extra currency and suffix symbols (Yen, Cent, Pence, Lira, Pesetas, Degree)
* Automatic width calculation of the thermometer image - no more manual guesswork
* Target values can now be enabled and disabled
* Shortcode is now [olimometer] to reduce confusion
* Colour picker installed to allow you to easily choose font and background colours
* Preview your Olimometers on the settings page
* Added 'Our Progress' styled thermometer skins in blue, red and green.

= 2.02 =
* Multiple Olimometers now supported with independent progress tracking.
* Added extra currency and suffix symbols (Yen, Cent, Pence, Lira, Pesetas, Degree).
* Automatic width calculation of the thermometer image - no more manual guesswork.
* Target values can now be enabled and disabled.
* Shortcode is now [olimometer] to reduce confusion.
* Colour picker installed to allow you to easily choose font and background colours.
* Preview your Olimometers on the settings page.
* Added 'Our Progress' styled thermometer skins in blue, red and green.