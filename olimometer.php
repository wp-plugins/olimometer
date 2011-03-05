<?php
/*
Plugin Name: Olimometer
Plugin URI: http://www.olivershingler.co.uk/oliblog/olimometer/
Description: Allows WordPress to display a thermometer to measure progress such as fundraising.
Author: Oliver Shingler
Author URI: http://www.olivershingler.co.uk
Version: 1.0
*/

/*
/--------------------------------------------------------------------\
|                                                                    |
| License: GPL                                                       |
|                                                                    |
| Copyright (C) 2011, Oliver Shingler				     |
| http://www.olivershingler.co.uk/oliblog/olimometer                                   	     |
| All rights reserved.                                               |
|                                                                    |
| This program is free software; you can redistribute it and/or      |
| modify it under the terms of the GNU General Public License        |
| as published by the Free Software Foundation; either version 2     |
| of the License, or (at your option) any later version.             |
|                                                                    |
| This program is distributed in the hope that it will be useful,    |
| but WITHOUT ANY WARRANTY; without even the implied warranty of     |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the      |
| GNU General Public License for more details.                       |
|                                                                    |
| You should have received a copy of the GNU General Public License  |
| along with this program; if not, write to the                      |
| Free Software Foundation, Inc.                                     |
| 51 Franklin Street, Fifth Floor                                    |
| Boston, MA  02110-1301, USA                                        |   
|                                                                    |
\--------------------------------------------------------------------/
*/

add_action('admin_menu', 'olimometer_add_pages');
add_filter('plugin_action_links', 'olimometer_action', -10, 2);

add_shortcode('show_olimometer','show_olimometer');



if ($_REQUEST['olimometer_submit'] && isset($_REQUEST['olimometer_total_value'])) {

	$uploadpath = wp_upload_dir();
	$myFile = $uploadpath['basedir']."/olimometer_settings.txt";
	$fh = fopen($myFile, 'w') or die("can't open file");

	$olimometer_progress_value = ereg_replace("[^0-9]", "", floor($_REQUEST['olimometer_progress_value']));
	$olimometer_total_value = ereg_replace("[^0-9]", "", floor($_REQUEST['olimometer_total_value']));


	$stringData = $olimometer_progress_value."\n";
	fwrite($fh, $stringData);
	$stringData = $olimometer_total_value."\n";
	fwrite($fh, $stringData);
	$stringData = $_REQUEST['olimometer_currency']."\n";
	fwrite($fh, $stringData);
	$stringData = $_REQUEST['olimometer_thermometer_bg_colour']."\n";
	fwrite($fh, $stringData);
	$stringData = $_REQUEST['olimometer_text_colour']."\n";
	fwrite($fh, $stringData);
	$stringData = $_REQUEST['olimometer_thermometer_height']."\n";
	fwrite($fh, $stringData);
	$stringData = $_REQUEST['olimometer_thermometer_class']."\n";
	fwrite($fh, $stringData);
	$stringData = $_REQUEST['olimometer_widget_title']."\n";
	fwrite($fh, $stringData);
	fclose($fh);

	update_option("olimometer_progress_value", $olimometer_progress_value);
	update_option("olimometer_total_value", $olimometer_total_value);
	update_option("olimometer_currency", $_REQUEST['olimometer_currency']);
	update_option("olimometer_thermometer_bg_colour", $_REQUEST['olimometer_thermometer_bg_colour']);
	update_option("olimometer_text_colour", $_REQUEST['olimometer_text_colour']);
	update_option("olimometer_thermometer_height", $_REQUEST['olimometer_thermometer_height']);
	update_option("olimometer_thermometer_class", $_REQUEST['olimometer_thermometer_class']);
	update_option("olimometer_widget_title", $_REQUEST['olimometer_widget_title']);






}



function olimometer_action($links, $file) {
	// adds the link to the settings page in the plugin list page
	if ($file == plugin_basename(dirname(__FILE__).'/olimometer.php'))
	$links[] = "<a href='options-general.php?page=olimometer_manage'>" . __('Settings', 'Olimometer') . "</a>";
	return $links;
}



function olimometer_add_pages() {
    //add_management_page('Olimometer', 'Olimometer', 8, 'olimometer_manage', 'olimometer_manage_page');
	add_submenu_page('options-general.php','Olimometer Settings', 'Olimometer', 'manage_options', 'olimometer_manage', 'olimometer_manage_page');
}

function olimometer_manage_page() {
    echo '<div class="wrap">';
	echo '<h2>Olimometer</h2>';
	echo '<form method="post">';
	
	?>
	<table class="form-table">
		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name">Current Amount</label></th>
			<td><input name="olimometer_progress_value" id="olimometer_progress_value" type="text" value="<?php 
				if(get_option("olimometer_progress_value")) {echo get_option("olimometer_progress_value");} else {echo "0";}
			
			?>" size="40" aria-required="true" />
            <p>How much money have you raised to date?</p></td>
		</tr>
		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name">Target Amount</label></th>
			<td><input name="olimometer_total_value" id="olimometer_total_value" type="text" value="<?php 
				if(get_option("olimometer_total_value")) {echo get_option("olimometer_total_value");} else {echo "100";}
			?>" size="40" aria-required="true" />
            <p>Input the total amount of money you would like to raise.</p></td>
		</tr>

		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name">Currency</label></th>
			<td><input name="olimometer_currency" id="olimometer_currency" type="text" value="<?php 
				if(get_option("olimometer_currency")) {echo get_option("olimometer_currency");} else {echo "156";}
			?>" size="40" aria-required="true" />
            <p>Decimal ASCII Value of the currency (for LiberationSans-Regular.ttf)</p>

		<p>Common currency values include:<br/>
		&pound = 163<br/>
		$ = 36<br/>
		&#8364; = 128</p>
		</td>
		</tr>

		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name">Thermometer Height</label></th>
			<td><input name="olimometer_thermometer_height" id="olimometer_thermometer_height" type="text" value="<?php 
				if(get_option("olimometer_thermometer_height")) {echo get_option("olimometer_thermometer_height");} else {echo "200";}
			?>" size="40" aria-required="true" />
            <p>The height of the thermometer in pixels</p></td>
		</tr>

		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name">Background Colour</label></th>
			<td><input name="olimometer_thermometer_bg_colour" id="olimometer_thermometer_bg_colour" type="text" value="<?php 
				if(get_option("olimometer_thermometer_bg_colour")) {echo get_option("olimometer_thermometer_bg_colour");} else {echo "FFFFFF";}
			?>" size="40" aria-required="true" />
            <p>Hex value for background colour of thermometer image (FFFFFF = white, 000000 = black)</p></td>
		</tr>

		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name">Text Colour</label></th>
			<td><input name="olimometer_text_colour" id="olimometer_text_colour" type="text" value="<?php 
				if(get_option("olimometer_text_colour")) {echo get_option("olimometer_text_colour");} else {echo "000000";}
			?>" size="40" aria-required="true" />
            <p>Hex value for the text colour within the image (FFFFFF = white, 000000 = black)</p></td>
		</tr>

		<tr class="form-field">
			<th scope="row" valign="top"><label for="name">Image Class</label></th>
			<td><input name="olimometer_thermometer_class" id="olimometer_thermometer_class" type="text" value="<?php 
				if(get_option("olimometer_thermometer_class")) {echo get_option("olimometer_thermometer_class");} else {echo "";}
			?>" size="40" aria-required="false" />
            <p>(Optional) The name of a CSS Class for the thermometer image</p></td>
		</tr>

		<tr class="form-field">
			<th scope="row" valign="top"><label for="name">Widget Title</label></th>
			<td><input name="olimometer_widget_title" id="olimometer_widget_title" type="text" value="<?php 
				if(get_option("olimometer_widget_title")) {echo get_option("olimometer_widget_title");} else {echo "";}
			?>" size="40" aria-required="false" />
            <p>(Optional) The title to appear on the Olimometer sidebar widget</p></td>
		</tr>

        
        
	</table>	
	<p class="submit"><input type="submit" class="button" name="olimometer_submit" value="Update" /></p>
	<?php
	echo '<input id="old" type="hidden" value="'.get_option("olimometer_progress").'">';
	echo '</form>';
	
	echo "	<small><p><strong>Installation</strong></p>
			<p>You can display the fundraising thermometer by placing the code <em>&lt;?php show_olimometer();?&gt;</em> anywhere in your theme or [show_olimometer] in a post.</p>

			<p><strong>Want to say thank you?</strong></p>
		  	<p>You can visit the my site for more information or to make a donation: <a href='http://www.olivershingler.co.uk/oliblog/olimometer'>http://www.olivershingler.co.uk/oliblog/olimometer</a>.</p>
<p>
<table><tr><td style='background-color:white;'>
<form action='https://www.paypal.com/cgi-bin/webscr' method='post' border=0>
<input type='hidden' name='cmd' value='_s-xclick'>
<input type='hidden' name='hosted_button_id' value='QLFWHB9SEJAYY'>
<input type='image' src='https://www.paypal.com/en_US/GB/i/btn/btn_donateCC_LG.gif' border='0' name='submit' alt='PayPal - The safer, easier way to pay online.'  style='border:0px solid #FF0000;' >
<img alt='' border='0' src='https://www.paypal.com/en_GB/i/scr/pixel.gif' width='1' height='1'>
</form>
</td></tr></table>
</p>
			<p><strong>Credits</strong></p>
			<p>Special thanks to <a href='http://thisismyurl.com/downloads/wordpress/plugins/fundraising-thermometer-for-wp/'>Christopher Ross</a>, author of the Our Progress Wordpress plugin from which the Olimometer has adapted the Wordpress plugin framework.</p>
			<p>Images are adapted from the PHP Fundraising Thermometer Generator by Sairam Suresh at <a href='www.entropyfarm.org'>www.entropyfarm.org</a></p>
			<p>TrueType Font is from the <a href='https://fedorahosted.org/liberation-fonts/'>Liberation Fonts</a> collection.</p>";
	
	echo '</small></div>';
}


function show_olimometer() {
	if(strlen(get_option("olimometer_total_value"))>1) {$total_value = get_option("olimometer_total_value");} else {$total_value= "100";}
	if(strlen(get_option("olimometer_progress_value"))>1) {$progress_value = get_option("olimometer_progress_value");} else {$progress_value = 0;}
	if(strlen(get_option("olimometer_currency"))>1) {$currency = get_option("olimometer_currency");} else {$currency = 156;}
	if(strlen(get_option("olimometer_thermometer_bg_colour"))>1) {$thermometer_bg_colour = get_option("olimometer_thermometer_bg_colour");} else {$thermometer_bg_colour= "FFFFFF";}
	if(strlen(get_option("olimometer_text_colour"))>1) {$text_colour = get_option("olimometer_text_colour");} else {$text_colour= "000000";}
	if(strlen(get_option("olimometer_thermometer_height"))>1) {$thermometer_height = get_option("olimometer_thermometer_height");} else {$thermometer_height = "200";}
	if(strlen(get_option("olimometer_thermometer_class"))>1) {$thermometer_class = get_option("olimometer_thermometer_class");} else {$thermometer_class = "";}
	echo "<div class='".$thermometer_class."'>\n";
	$image_location = plugins_url('olimometer/thermometer.php', dirname(__FILE__) );
	echo "<img src='".$image_location."?total_value=".$total_value."&progress_value=".$progress_value."&currency=".$currency."&thermometer_bg_colour=".$thermometer_bg_colour."&text_colour=".$text_colour."&thermometer_height=".$thermometer_height."'>";
	echo "</div>\n";
}




function my_money_format($format, $num) {
		if (function_exists('money_format')) {
			 return (money_format($format,$num));
		} else {
			return "$" . number_format($num, 2);
		}
     
    }

function widget_cr_olimometer() {

	if(strlen(get_option("olimometer_widget_title"))>1) {$widget_title = get_option("olimometer_widget_title");} else {$widget_title = "";}
?>
  <h2 class="widgettitle"><? echo $widget_title; ?></h2>
<div style="display: inline-block;">
  <?php show_olimometer(); ?>
</div>

<?php
}
function cr_olimometer_init()
{
  register_sidebar_widget(__('Olimometer'), 'widget_cr_olimometer');
}
add_action("plugins_loaded", "cr_olimometer_init");


?>