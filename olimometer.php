<?php
/*
Plugin Name: Olimometer
Plugin URI: http://www.olivershingler.co.uk/oliblog/olimometer/
Description: Allows WordPress to display a thermometer to measure progress such as fundraising.
Author: Oliver Shingler
Author URI: http://www.olivershingler.co.uk
Version: 1.32
*/

/*
/--------------------------------------------------------------------\
|                                                                    |
| License: GPL                                                       |
|                                                                    |
| Copyright (C) 2011, Oliver Shingler				     |
| http://www.olivershingler.co.uk/oliblog/olimometer                 |
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


/* Main Settings save */
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
	$stringData = $_REQUEST['olimometer_transparent']."\n";
	fwrite($fh, $stringData);
	$stringData = $_REQUEST['olimometer_show_progress']."\n";
	fwrite($fh, $stringData);
	$stringData = $_REQUEST['olimometer_progress_label']."\n";
	fwrite($fh, $stringData);
	$stringData = $_REQUEST['olimometer_font_height']."\n";
	fwrite($fh, $stringData);
	$stringData = $_REQUEST['olimometer_width']."\n";
	fwrite($fh, $stringData);
	$stringData = $_REQUEST['olimometer_suffix']."\n";
	fwrite($fh, $stringData);
	$stringData = $_REQUEST['olimometer_skin']."\n";
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
	update_option("olimometer_transparent", $_REQUEST['olimometer_transparent']);
	update_option("olimometer_show_progress", $_REQUEST['olimometer_show_progress']);
	update_option("olimometer_progress_label", $_REQUEST['olimometer_progress_label']);
	update_option("olimometer_font_height", $_REQUEST['olimometer_font_height']);
	update_option("olimometer_width", $_REQUEST['olimometer_width']);
	update_option("olimometer_suffix", $_REQUEST['olimometer_suffix']);
	update_option("olimometer_skin", $_REQUEST['olimometer_skin']);

}

/* Dashboard Widget save */
if ($_REQUEST['olimometer_dw_submit'] && isset($_REQUEST['olimometer_total_value'])) {

	$uploadpath = wp_upload_dir();
	$myFile = $uploadpath['basedir']."/olimometer_settings.txt";
	$fh = fopen($myFile, 'w') or die("can't open file");

	$olimometer_progress_value = ereg_replace("[^0-9]", "", floor($_REQUEST['olimometer_progress_value']));
	$olimometer_total_value = ereg_replace("[^0-9]", "", floor($_REQUEST['olimometer_total_value']));


	$stringData = $olimometer_progress_value."\n";
	fwrite($fh, $stringData);
	$stringData = $olimometer_total_value."\n";
	fwrite($fh, $stringData);
	$stringData = get_option("olimometer_currency")."\n";
	fwrite($fh, $stringData);
	$stringData = get_option("olimometer_thermometer_bg_colour")."\n";
	fwrite($fh, $stringData);
	$stringData = get_option("olimometer_text_colour")."\n";
	fwrite($fh, $stringData);
	$stringData = get_option("olimometer_thermometer_height")."\n";
	fwrite($fh, $stringData);
	$stringData = get_option("olimometer_thermometer_class")."\n";
	fwrite($fh, $stringData);
	$stringData = get_option("olimometer_widget_title")."\n";
	fwrite($fh, $stringData);
	$stringData = get_option("olimometer_transparent")."\n";
	fwrite($fh, $stringData);
	$stringData = get_option("olimometer_show_progress")."\n";
	fwrite($fh, $stringData);
	$stringData = get_option("olimometer_progress_label")."\n";
	fwrite($fh, $stringData);
	$stringData = get_option("olimometer_font_height")."\n";
	fwrite($fh, $stringData);
	$stringData = get_option("olimometer_width")."\n";
	fwrite($fh, $stringData);
	$stringData = get_option("olimometer_suffix")."\n";
	fwrite($fh, $stringData);
	$stringData = get_option("olimometer_skin")."\n";
	fwrite($fh, $stringData);	

	fclose($fh);

	update_option("olimometer_progress_value", $olimometer_progress_value);
	update_option("olimometer_total_value", $olimometer_total_value);

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
			<th scope="row" valign="top"><label for="name">Current Amount Raised (Progress Value)</label></th>
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


<!--
		<tr class="form-field">
			<th scope="row" valign="top"><label for="name">Currency</label></th>
			<td><input name="olimometer_currency" id="olimometer_currency" type="text" value="<?php 
				if(get_option("olimometer_currency")) {echo get_option("olimometer_currency");}
			?>" size="40" aria-required="true" />
            <p>(Optional) Decimal ASCII Value of the currency (for LiberationSans-Regular.ttf)</p>

		<p>Common currency values include:<br/>
		&pound = 163<br/>
		$ = 36<br/>
		&#8364; = 128</p>
		</td>
		</tr>		
-->

		<tr class="form-field">
			<th scope="row" valign="top"><label for="name">Currency</label></th>
			<td><input name="olimometer_currency" id="olimometer_currency" type="text" value="<?php 
				if(get_option("olimometer_currency")) {echo get_option("olimometer_currency");}
			?>" size="40" aria-required="true" />
            <p>(Optional) Decimal ASCII Value of the currency (for LiberationSans-Regular.ttf)</p>

		<p>Common currency values include:<br/>
		&pound = 163<br/>
		$ = 36<br/>
		&#8364; = 128</p>
		</td>
		</tr>		

		<tr class="form-field">
			<th scope="row" valign="top"><label for="name">Suffix</label></th>
			<td><input name="olimometer_suffix" id="olimometer_suffix" type="text" value="<?php 
				if(get_option("olimometer_suffix")) {echo get_option("olimometer_suffix");}
			?>" size="40" aria-required="true" />
            <p>(Optional) Decimal ASCII Value of the suffix (character to go after the value)</p>

		<p>Common values include:<br/>
                % = 37<br/>
                </p>
		</td>
		</tr>
		
		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name">Thermometer Skin</label></th>
			<td><select name="olimometer_skin" id="olimometer_skin" aria-required="true" >

<?php
// Import list of Olimometer skins from XML file
$olimometer_skin_xml_file = WP_PLUGIN_DIR."/".plugin_basename(dirname(__FILE__).'/skins.xml');
include_once('skins.php');	
$olimometer_current_skin=0;
$olimometer_skin_names = array();
$olimometer_skin_names = olimometer_get_skin_names();

// Loop around each skin name and display in a drop-down list
foreach ($olimometer_skin_names as $olimometer_skin_name) {
	echo "<option value=".$olimometer_current_skin;
	if(get_option("olimometer_skin") == $olimometer_current_skin) {
		echo " selected";
	}
	echo ">".$olimometer_skin_name."</option>";	
	$olimometer_current_skin++;
}



?>

			</select>
            <p>Choose a skin for the thermometer. A skin changes the look and design of the thermometer.</p></td>
		</tr>

		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name">Thermometer Height</label></th>
			<td><input name="olimometer_thermometer_height" id="olimometer_thermometer_height" type="text" value="<?php 
				if(get_option("olimometer_thermometer_height")) {echo get_option("olimometer_thermometer_height");} else {echo "200";}
			?>" size="40" aria-required="true" />
            <p>The height of the thermometer in pixels. Default = 200</p></td>
		</tr>

		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name">Thermometer Width</label></th>
			<td><input name="olimometer_width" id="olimometer_width" type="text" value="<?php 
				if(get_option("olimometer_width")) {echo get_option("olimometer_width");} else {echo "100";}
			?>" size="40" aria-required="true" />
            <p>The width of the thermometer in pixels. Default = 100</p></td>
		</tr>

		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name">Background Colour</label></th>
			<td><input name="olimometer_thermometer_bg_colour" id="olimometer_thermometer_bg_colour" type="text" value="<?php 
				if(get_option("olimometer_thermometer_bg_colour")) {echo get_option("olimometer_thermometer_bg_colour");} else {echo "FFFFFF";}
			?>" size="40" aria-required="true" />
            <p>Hex value for background colour of thermometer image (FFFFFF = white, 000000 = black)</p></td>
		</tr>

		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name">Transparent Background</label></th>
			<td><select name="olimometer_transparent" id="olimometer_transparent" aria-required="true" >
				<option value=0>No</option>
				<option value=1<?php
if(get_option("olimometer_transparent") == 1) {
	echo " selected";
}

?>>Yes</option>
			</select>
            <p>Make the thermometer background transparent? If you select this option to yes then make sure you choose a background colour above that is close to your site's actual background colour. This will help it blend in nicely.</p></td>
		</tr>

		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name">Text Height</label></th>
			<td><input name="olimometer_font_height" id="olimometer_font_height" type="text" value="<?php 
				if(get_option("olimometer_font_height")) {echo get_option("olimometer_font_height");} else {echo "8";}
			
			?>" size="40" aria-required="true" />
            <p>Specify the size of the font in pixels. If you increase this and the text is cut off, you need to increase the thermometer width. Default = 8</p></td>
		</tr>

		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name">Text Colour</label></th>
			<td><input name="olimometer_text_colour" id="olimometer_text_colour" type="text" value="<?php 
				if(get_option("olimometer_text_colour")) {echo get_option("olimometer_text_colour");} else {echo "000000";}
			?>" size="40" aria-required="true" />
            <p>Hex value for the text colour within the image (FFFFFF = white, 000000 = black)</p></td>
		</tr>

		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name">Show Progress Value (Current Amount)</label></th>
			<td><select name="olimometer_show_progress" id="olimometer_show_progress" aria-required="true" >
				<option value=1<?php
if(get_option("olimometer_show_progress") == 1) {
	echo " selected";
}

?>>Yes</option>
				<option value=0<?php
if( (get_option("olimometer_show_progress") == 0) && (strlen(get_option("olimometer_show_progress")) > 0)) {
	echo " selected";
}

?>>No</option>
			</select>
            <p>Do you wish the current amount raised to be displayed on the image? It will be placed underneath the thermometer with an optional text string specified below...</p></td>
		</tr>

		<tr class="form-field">
			<th scope="row" valign="top"><label for="name">Progress Label</label></th>
			<td><input name="olimometer_progress_label" id="olimometer_progress_label" type="text" value="<?php 
				if(get_option("olimometer_progress_label")) {echo get_option("olimometer_progress_label");} else {echo "Raised so far:";}
			?>" size="40" aria-required="false" />
            <p>(Optional) The text string to display before the Progress Value. Default = "Raised so far:"</p></td>
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
			<p>Images are adapted from the PHP Fundraising Thermometer Generator by Sairam Suresh at <a href='http://www.entropyfarm.org'>www.entropyfarm.org</a></p>
			<p>TrueType Font is from the <a href='https://fedorahosted.org/liberation-fonts/'>Liberation Fonts</a> collection.</p>";
	
	echo '</small></div>';
}


function show_olimometer() {
	if(strlen(get_option("olimometer_total_value"))>0) {$total_value = get_option("olimometer_total_value");} else {$total_value= "100";}
	if(strlen(get_option("olimometer_progress_value"))>0) {$progress_value = get_option("olimometer_progress_value");} else {$progress_value = 0;}
	if(strlen(get_option("olimometer_currency"))>1) {$currency = get_option("olimometer_currency");} else {$currency = x;}
	if(strlen(get_option("olimometer_suffix"))>1) {$olimometer_suffix = get_option("olimometer_suffix");} else {$olimometer_suffix = x;}
	if(strlen(get_option("olimometer_thermometer_bg_colour"))>1) {$thermometer_bg_colour = get_option("olimometer_thermometer_bg_colour");} else {$thermometer_bg_colour= "FFFFFF";}
	if(strlen(get_option("olimometer_text_colour"))>1) {$text_colour = get_option("olimometer_text_colour");} else {$text_colour= "000000";}
	if(strlen(get_option("olimometer_thermometer_height"))>1) {$thermometer_height = get_option("olimometer_thermometer_height");} else {$thermometer_height = "200";}
	if(strlen(get_option("olimometer_thermometer_class"))>1) {$thermometer_class = get_option("olimometer_thermometer_class");} else {$thermometer_class = "";}
	if(strlen(get_option("olimometer_transparent"))>0) {$thermometer_transparent = get_option("olimometer_transparent");} else {$thermometer_transparent = "plop";}
	if(strlen(get_option("olimometer_show_progress"))>0) {$olimometer_show_progress = get_option("olimometer_show_progress");} else {$olimometer_show_progress = "1";}
	if(strlen(get_option("olimometer_progress_label"))>1) {$olimometer_progress_label = get_option("olimometer_progress_label");} else {$olimometer_progress_label = "Raised so far:";}
	if(strlen(get_option("olimometer_font_height"))>1) {$olimometer_font_height = get_option("olimometer_font_height");} else {$olimometer_font_height = "8";}
	if(strlen(get_option("olimometer_width"))>1) {$olimometer_width = get_option("olimometer_width");} else {$olimometer_width = "100";}
	if(strlen(get_option("olimometer_skin"))>0) {$olimometer_skin = get_option("olimometer_skin");} else {$olimometer_skin = "0";}

	$image_location = plugins_url('olimometer/thermometer.php', dirname(__FILE__) );
	$the_olimometer_text = "<img src='".$image_location."?total=".$total_value."&progress=".$progress_value."&currency=".$currency."&bg=".$thermometer_bg_colour."&text_colour=".$text_colour."&height=".$thermometer_height."&transparent=".$thermometer_transparent."&show_progress=".$olimometer_show_progress."&progress_label=".$olimometer_progress_label."&font_height=".$olimometer_font_height."&width=".$olimometer_width."&suffix=".$olimometer_suffix."&skin=".$olimometer_skin."'";
	if(strlen(get_option("olimometer_thermometer_class"))>0) {
		$the_olimometer_text = $the_olimometer_text." class='".$thermometer_class."'";
	}
	$the_olimometer_text = $the_olimometer_text." alt='Olimometer 1.32'>";
	return $the_olimometer_text;
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
  <?php echo show_olimometer(); ?>
</div>

<?php
}
function cr_olimometer_init()
{
  register_sidebar_widget(__('Olimometer'), 'widget_cr_olimometer');
}
add_action("plugins_loaded", "cr_olimometer_init");



/* *****
Start of Dashboard Widget section
**** */

function olimometer_dashboard_widget_function() {
    echo '<div class="wrap">';
	echo '<form method="post">';
	
	?>
	<table class="form-table">
		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name">Current Amount Raised (Progress Value)</label></th>
			<td><input name="olimometer_progress_value" id="olimometer_progress_value" type="text" value="<?php 
				if(get_option("olimometer_progress_value")) {echo get_option("olimometer_progress_value");} else {echo "0";}
			
			?>" size="40" aria-required="true" /></td>
		</tr>
		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name">Target Amount</label></th>
			<td><input name="olimometer_total_value" id="olimometer_total_value" type="text" value="<?php 
				if(get_option("olimometer_total_value")) {echo get_option("olimometer_total_value");} else {echo "100";}
			?>" size="40" aria-required="true" /></td>
		</tr>
	</table>	
	<p class="submit"><input type="submit" class="button" name="olimometer_dw_submit" value="Update" />
	<?php
	echo '<input id="old" type="hidden" value="'.get_option("olimometer_progress").'">';
	?>
&nbsp;&nbsp;<a href='options-general.php?page=olimometer_manage'>Settings</a>
	<?php
	echo '</p></form></div>'; 

} 

function olimometer_add_dashboard_widgets() {
	wp_add_dashboard_widget('olimometer_dashboard_widget', 'Olimometer', 'olimometer_dashboard_widget_function');	
} 

add_action('wp_dashboard_setup', 'olimometer_add_dashboard_widgets' );

?>