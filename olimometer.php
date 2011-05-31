<?php
/*
Plugin Name: Olimometer
Plugin URI: http://www.olivershingler.co.uk/oliblog/olimometer/
Description: A dynamic fundraising thermometer with customisable height, currency, background colour, transparency and skins. Integrates with PayPal to retrieve an account balance as the current progress value.
Author: Oliver Shingler
Author URI: http://www.olivershingler.co.uk
Version: 1.40
*/


/*  Copyright 2011 Oliver Shingler (email : oliver@shingler.co.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


add_action('admin_menu', 'olimometer_add_pages');
add_filter('plugin_action_links', 'olimometer_action', -10, 2);

add_shortcode('show_olimometer','show_olimometer');


/* Main Settings save */
if ($_REQUEST['olimometer_submit'] && isset($_REQUEST['olimometer_total_value'])) {

	$olimometer_progress_value = ereg_replace("[^0-9]", "", floor($_REQUEST['olimometer_progress_value']));
	$olimometer_total_value = ereg_replace("[^0-9]", "", floor($_REQUEST['olimometer_total_value']));

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
	update_option("olimometer_use_paypal", $_REQUEST['olimometer_use_paypal']);
	update_option("olimometer_paypal_username", $_REQUEST['olimometer_paypal_username']);	
	update_option("olimometer_paypal_password", $_REQUEST['olimometer_paypal_password']);
	update_option("olimometer_paypal_signature", $_REQUEST['olimometer_paypal_signature']);
	

}

/* Dashboard Widget save */
if ($_REQUEST['olimometer_dw_submit'] && isset($_REQUEST['olimometer_total_value'])) {

	$olimometer_progress_value = ereg_replace("[^0-9]", "", floor($_REQUEST['olimometer_progress_value']));
	$olimometer_total_value = ereg_replace("[^0-9]", "", floor($_REQUEST['olimometer_total_value']));

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
	add_submenu_page('options-general.php','Olimometer Settings', 'Olimometer', 'manage_options', 'olimometer_manage', 'olimometer_manage_page');
}

function olimometer_manage_page() {
    echo '<div class="wrap">';

?>
<script language="javascript">
function olimometer_progress_disable()
{
document.olimometer_form1.olimometer_progress_value.readOnly=true;
document.olimometer_form1.olimometer_paypal_username.readOnly=false;
document.olimometer_form1.olimometer_paypal_password.readOnly=false;
document.olimometer_form1.olimometer_paypal_signature.readOnly=false;
}

function olimometer_progress_enable()
{
document.olimometer_form1.olimometer_progress_value.readOnly=false;
document.olimometer_form1.olimometer_paypal_username.readOnly=true;
document.olimometer_form1.olimometer_paypal_password.readOnly=true;
document.olimometer_form1.olimometer_paypal_signature.readOnly=true;
}

</script>

<?
	echo '<h2>Olimometer</h2>';
	echo '<a href="#progressvalues">Progress Values</a><br />';
	echo '<a href="#appearance">Appearance and Layout</a><br />';
	echo '<a href="#diagnostics">Diagnostics</a><br />';
	echo '<a href="#OtherInformation">Other Information</a><br />';

	echo '<form method="post" id="olimometer_form1" name="olimometer_form1">';
	echo '<hr /><a name="progressvalues"></a>';
	echo '<h3>Progress Values</h3>';
	
	?>
	<table class="form-table">
		<tr class="form-required">
			<th scope="row" valign="top"><label for="name">Manual or PayPal Link?</label></th>
			<td><input name="olimometer_use_paypal" id="olimometer_use_paypal" type="radio" value="0"<?php
if(get_option("olimometer_use_paypal") == 0) {
	echo " checked";
}

?> onClick="olimometer_progress_enable();"> Manual<br />
			    <input name="olimometer_use_paypal" id="olimometer_use_paypal" type="radio" value="1"<?php
if(get_option("olimometer_use_paypal") == 1) {
	echo " checked";
}

?> onClick="olimometer_progress_disable();"> PayPal

            <p>Do you want to update the progress (current amount raised) manually or automatically by linking to a PayPal account?</p></td>

		</tr>

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
            <p>Input the total amount you would like to raise.</p></td>
		</tr>

		<tr class="form-field">
			<th scope="row" valign="top"><label for="name">PayPal API Username</label></th>
			<td><input name="olimometer_paypal_username" id="olimometer_paypal_username" type="text" value="<?php 
				if(get_option("olimometer_paypal_username")) {echo get_option("olimometer_paypal_username");}
			?>" size="40" /></td>
		</tr>

		<tr class="form-field">
			<th scope="row" valign="top"><label for="name">PayPal API Password</label></th>
			<td><input name="olimometer_paypal_password" id="olimometer_paypal_password" type="text" value="<?php 
				if(get_option("olimometer_paypal_password")) {echo get_option("olimometer_paypal_password");}
			?>" size="40" /></td>
		</tr>

		<tr class="form-field">
			<th scope="row" valign="top"><label for="name">PayPal API Signature</label></th>
			<td><input name="olimometer_paypal_signature" id="olimometer_paypal_signature" type="text" value="<?php 
				if(get_option("olimometer_paypal_signature")) {echo get_option("olimometer_paypal_signature");}
			?>" size="40" />
<p>To get your PayPal API credentials log in to your PayPal account. Under My Account, choose Profile then My Selling Preferences. Under the Selling Online section, choose the update link next to API Access, and finally choose Option 2 (Request API credentials).</p>
</td>
		</tr>
		


	</table>
<p class="submit"><input type="submit" class="button" name="olimometer_submit" value="Update" /></p>	
<?
	echo '<hr /><a name="appearance"></a>';
	echo '<h3>Appearance and Layout</h3>';
?>

	<table class="form-table">
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

	echo '<hr /><a name="diagnostics"></a>';
	echo '<h3>Diagnostics</h3>';	
	echo 'GD Extension: ';
	if (extension_loaded('gd') && function_exists('gd_info')) {
		echo "Installed";
	}
	else {
		echo "<font color=red><b>NOT DETECTED</b></font>";
	}
	echo '<br />';
	echo 'PayPal Integration: ';
	
	if(olimometer_get_paypal_balance() == FALSE) {
		echo "<font color=red><b>NOT WORKING</b></font>";
	}
	else {
		echo "OK. Current Balance = " . olimometer_get_paypal_balance();
	}
	echo '<br />';
	echo '<hr /><a name="OtherInformation"></a>';
	echo '<h3>Other Information</h3>';	

	
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
			<p>The 'original' theme images are adapted from the PHP Fundraising Thermometer Generator by Sairam Suresh at <a href='http://www.entropyfarm.org'>www.entropyfarm.org</a></p>
			<p>TrueType Font is from the <a href='https://fedorahosted.org/liberation-fonts/'>Liberation Fonts</a> collection.</p>";
	
	echo '</small></div>';


?>
<script language="javascript">

if(document.olimometer_form1.olimometer_use_paypal[0].checked)
{
olimometer_progress_enable();
}
else
{
olimometer_progress_disable();
}

</script>
<?

}


function show_olimometer() {
	// If PayPal integration is configured, get the current balance and save it
	if(get_option("olimometer_use_paypal") == 1) {
		update_option("olimometer_progress_value", olimometer_get_paypal_balance());
	}

	// Get the rest of the saved values.
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
	$the_olimometer_text = $the_olimometer_text." alt='Olimometer 1.40'>";
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





// The following function is for PayPal balance retrieval
function Olimometer_PPHttpPost($methodName_, $nvpStr_) {
	$olimometer_pp_environment = 'live';

	$API_UserName = urlencode(get_option("olimometer_paypal_username"));
	$API_Password = urlencode(get_option("olimometer_paypal_password"));
	$API_Signature = urlencode(get_option("olimometer_paypal_signature"));
	$API_Endpoint = "https://api-3t.paypal.com/nvp";
	if("sandbox" === $olimometer_pp_environment || "beta-sandbox" === $olimometer_pp_environment) {
		$API_Endpoint = "https://api-3t.$olimometer_pp_environment.paypal.com/nvp";
	}
	$version = urlencode('51.0');

	// setting the curl parameters.
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);

	// turning off the server and peer verification(TrustManager Concept).
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);

	// NVPRequest for submitting to server
	$nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";

	// setting the nvpreq as POST FIELD to curl
	curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

	// getting response from server
	$httpResponse = curl_exec($ch);

	if(!$httpResponse) {
		exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
	}

	// Extract the RefundTransaction response details
	$httpResponseAr = explode("&", $httpResponse);

	$httpParsedResponseAr = array();
	foreach ($httpResponseAr as $i => $value) {
		$tmpAr = explode("=", $value);
		if(sizeof($tmpAr) > 1) {
			$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
		}
	}

	if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
		exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
	}

	return $httpParsedResponseAr;
}

function olimometer_get_paypal_balance()
{
	$nvpStr="";

	$httpParsedResponseAr = olimometer_PPHttpPost('GetBalance', $nvpStr);

	if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
		return urldecode($httpParsedResponseAr[L_AMT0]);
	}
	else  {
		return false;
	}
}
?>