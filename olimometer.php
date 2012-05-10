<?php
/*
Plugin Name: Olimometer
Plugin URI: http://www.olivershingler.co.uk/oliblog/olimometer/
Description: A dynamic fundraising thermometer with PayPal integration, customisable height, currency, background colour, transparency and skins.
Author: Oliver Shingler
Author URI: http://www.olivershingler.co.uk
Version: 2.00
*/


/*  Copyright 2012 Oliver Shingler (email : oliver@shingler.co.uk)

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

add_shortcode('show_olimometer','call_show_olimometer'); // Legacy
add_shortcode('olimometer','call_show_olimometer');

register_activation_hook(__FILE__,'olimometer_install');
add_action('plugins_loaded', 'update_check');



/* Create new Olimometer*/
if ($_REQUEST['olimometer_create'] && isset($_REQUEST['olimometer_description'])) {
    $new_olimometer = new Olimometer();
    $new_olimometer->olimometer_description = $_REQUEST['olimometer_description'];
    $new_olimometer->save();
    update_option("olimometer_last", $new_olimometer->olimometer_id);
}

/* Delete an Olimometer*/
if ($_REQUEST['olimometer_delete']) {
    if($_REQUEST['olimometer_id'] == 1)
    {
        // This is Olimometer #1... Can't delete it!
    }
    else
    {
        $dead_olimometer = new Olimometer();
        
        $dead_olimometer->load($_REQUEST['olimometer_id']);
        $dead_olimometer->delete();
        update_option("olimometer_last", 1);
    }
}

/* Load an Olimometer */
if ($_REQUEST['olimometer_load']) {
    // Which one?
    update_option("olimometer_last", $_REQUEST['olimometer_id']);
}

/* Main Settings save */
if ($_REQUEST['olimometer_submit'] && isset($_REQUEST['olimometer_total_value'])) {
   
    // Which olimometer do they wish to save?
    $current_olimometer_id = $_REQUEST['olimometer_id'];
    
    // Create a new object with that value
    $an_olimometer = new Olimometer();
    $an_olimometer->olimometer_id = $current_olimometer_id;
    
    // Get values from form and dump in to the object
    $an_olimometer->olimometer_description = $_REQUEST['olimometer_description'];
    $an_olimometer->olimometer_progress_value = $_REQUEST['olimometer_progress_value'];
	$an_olimometer->olimometer_total_value = $_REQUEST['olimometer_total_value'];
	$an_olimometer->olimometer_currency = $_REQUEST['olimometer_currency'];
	$an_olimometer->olimometer_thermometer_bg_colour = $_REQUEST['olimometer_thermometer_bg_colour'];
	$an_olimometer->olimometer_text_colour = $_REQUEST['olimometer_text_colour'];
	$an_olimometer->olimometer_thermometer_height = $_REQUEST['olimometer_thermometer_height'];
	$an_olimometer->olimometer_transparent = $_REQUEST['olimometer_transparent'];
	$an_olimometer->olimometer_show_target = $_REQUEST['olimometer_show_target'];
    $an_olimometer->olimometer_show_progress = $_REQUEST['olimometer_show_progress'];
	$an_olimometer->olimometer_progress_label = $_REQUEST['olimometer_progress_label'];
	$an_olimometer->olimometer_font_height = $_REQUEST['olimometer_font_height'];
	$an_olimometer->olimometer_suffix = $_REQUEST['olimometer_suffix'];
	$an_olimometer->olimometer_skin = $_REQUEST['olimometer_skin'];
	$an_olimometer->olimometer_use_paypal = $_REQUEST['olimometer_use_paypal'];
	$an_olimometer->olimometer_paypal_username = $_REQUEST['olimometer_paypal_username'];
	$an_olimometer->olimometer_paypal_password = $_REQUEST['olimometer_paypal_password'];
	$an_olimometer->olimometer_paypal_signature = $_REQUEST['olimometer_paypal_signature'];
    
    // Save it
    $an_olimometer->save();

}

/* Dashboard Widget save */
if ($_REQUEST['olimometer_dw_submit'] && isset($_REQUEST['olimometer_total_value'])) {

    // Which olimometer do they wish to save?
    $current_olimometer_id = $_REQUEST['olimometer_id'];
    
    // Create a new object with that value
    $an_olimometer = new Olimometer();
    
    // Load the values for the chosen olimometer
    $an_olimometer->load($current_olimometer_id);
    
    // Get values from form and dump in to the object
    $an_olimometer->olimometer_progress_value = $_REQUEST['olimometer_progress_value'];
	$an_olimometer->olimometer_total_value = $_REQUEST['olimometer_total_value'];

    // Save it
    $an_olimometer->save();

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
<script type="text/javascript" src="<?php echo plugins_url(); ?>/olimometer/jscolor/jscolor.js"></script>

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

<?php
    // Load the olimometer values:
    //$current_olimometer_id = 1; // Hard coded for the moment
    // If we are being asked to load a particular Olimometer's settings
    if ($_REQUEST['olimometer_load']) {
        // Which one?
        update_option("olimometer_last", $_REQUEST['olimometer_id']);
        $current_olimometer_id = $_REQUEST['olimometer_id'];
        
    }
    else {
        if(strlen(get_option("olimometer_last")) > 0)
        {
            $current_olimometer_id = get_option("olimometer_last");
        }
        else {
            $current_olimometer_id = 1;
        }
    }
    
    
    
    $current_olimometer = new Olimometer();
    $current_olimometer->load($current_olimometer_id);
    
    
	echo '<div class="icon32" id="icon-options-general"><br></div><h2>Olimometer - '.$current_olimometer->olimometer_description.'</h2>';
    



    
    
    ?>
    <table>
        <form method="post" id="olimometer_selection_form" name="olimometer_selection_form">
    		<tr class="form-field form-required">
            
			<td ><label for="name">Please choose an Olimometer:</label></td>
			<td>
                <?php
                echo olimometer_list($current_olimometer_id,"olimometer_id","olimometer_id");
                ?>
             </td>

            <td><input type="submit" class="button-primary" name="olimometer_load" value="Load" /></td>
            </tr>
        </form>
        

        
        <form method="post" id="olimometer_create_form" name="olimometer_create_form">
    		<tr class="form-field form-required">
            
			<td ><label for="name">Create a new Olimometer:</label></td>
			<td><input type="text" maxlength="30" name="olimometer_description"></input>
             </td>

            <td><input type="submit" class="button-primary" name="olimometer_create" value="Create" /></td>
            </tr>
        </form>
        
        
    </table>
    
    
    <hr />
    <?php

    
    
    // Now start the main options page
	echo '<p><a href="#progressvalues">Progress Values</a><br />';
	echo '<a href="#appearance">Appearance and Layout</a><br />';
	echo '<a href="#diagnostics">Diagnostics</a><br />';
	echo '<a href="#OtherInformation">Other Information</a></p>';

	echo '<form method="post" id="olimometer_form1" name="olimometer_form1">';
    echo '<input type="hidden" id="olimometer_id" name="olimometer_id" value="'.$current_olimometer_id.'">';
    echo '<hr />';
    ?>
    <a name="olimometer_details"></a>
    <div class="alignleft" style="margin-right:10px;">
    <h3>Olimometer Details</h3>
    
    	<table class="form-table">
            <tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name">Olimometer Name:</label></th>
			<td><input name="olimometer_description" id="olimometer_description" type="text" maxlength="30" value="<?php 
				echo $current_olimometer->olimometer_description;
			?>" size="20" aria-required="true" />
            <p><span class="description">What would you like to call this Olimometer?</span></p></td>
		</tr>
        </table>
        
        <p>
            You can insert this Olimometer in to your posts and pages by using the following shortcode:<br />
            <i><b>[olimometer id=<?php echo $current_olimometer_id; ?>]</b></i>
        </p>
        
        <p class="submit"><input type="submit" class="button-primary" name="olimometer_submit" value="Save Changes" /><input type="submit" class="button-primary" name="olimometer_delete" value="Delete this Olimometer" /></p>
        
        </div><!-- Olimometer Details -->
        <div>
           <h3>Preview</h3>
            <?php echo show_olimometer($current_olimometer_id); ?>
        </div>
    <div id="restofform" style="clear:both;">
    <?php
	echo '<hr /><a name="progressvalues"></a>';
	echo '<h3>Progress Values</h3>';
	
	?>
	<table class="form-table">
		<tr class="form-required">
			<th scope="row" valign="top"><label for="name">Manual or PayPal Link?</label></th>
			<td><input name="olimometer_use_paypal" id="olimometer_use_paypal" type="radio" value="0"<?php
if($current_olimometer->olimometer_use_paypal == 0) {
	echo " checked";
}

?> onClick="olimometer_progress_enable();"> Manual<br />
			    <input name="olimometer_use_paypal" id="olimometer_use_paypal" type="radio" value="1"<?php
if($current_olimometer->olimometer_use_paypal == 1) {
	echo " checked";
}

?> onClick="olimometer_progress_disable();"> PayPal

            <p><span class="description">Do you want to update the progress (current amount raised) manually or automatically by linking to a PayPal account?</span></p></td>

		</tr>

		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name">Current Amount Raised (Progress Value)</label></th>
			<td><input name="olimometer_progress_value" id="olimometer_progress_value" type="text" value="<?php 
				echo $current_olimometer->olimometer_progress_value;
			
			?>" size="40" aria-required="true" />
            <p><span class="description">How much money have you raised to date?</span></p></td>
		</tr>
		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name">Target Amount</label></th>
			<td><input name="olimometer_total_value" id="olimometer_total_value" type="text" value="<?php 
				echo $current_olimometer->olimometer_total_value;
			?>" size="40" aria-required="true" />
            <p><span class="description">Input the total amount you would like to raise.</span></p></td>
		</tr>

		<tr class="form-field">
			<th scope="row" valign="top"><label for="name">PayPal API Username</label></th>
			<td><input name="olimometer_paypal_username" id="olimometer_paypal_username" type="text" value="<?php 
				echo $current_olimometer->olimometer_paypal_username;
			?>" size="40" /></td>
		</tr>

		<tr class="form-field">
			<th scope="row" valign="top"><label for="name">PayPal API Password</label></th>
			<td><input name="olimometer_paypal_password" id="olimometer_paypal_password" type="text" value="<?php 
				echo $current_olimometer->olimometer_paypal_password;
			?>" size="40" /></td>
		</tr>

		<tr class="form-field">
			<th scope="row" valign="top"><label for="name">PayPal API Signature</label></th>
			<td><input name="olimometer_paypal_signature" id="olimometer_paypal_signature" type="text" value="<?php 
				echo $current_olimometer->olimometer_paypal_signature;
			?>" size="40" />
<p><span class="description">To get your PayPal API credentials log in to your PayPal account. Under My Account, choose Profile then My Selling Preferences. Under the Selling Online section, choose the update link next to API Access, and finally choose Option 2 (Request API credentials).</span></p>
</td>
		</tr>
		


	</table>
<p class="submit"><input type="submit" class="button-primary" name="olimometer_submit" value="Save Changes" /></p>	

<a name="appearance"></a>
<script type="text/javascript"><!--
google_ad_client = "ca-pub-9213372745182820";
/* Olimometer - Leaderboard */
google_ad_slot = "8984536418";
google_ad_width = 728;
google_ad_height = 90;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
<hr />
<?php

	echo '<h3>Appearance and Layout</h3>';
?>

	<table class="form-table">
		<tr class="form-field">
			<th scope="row" valign="top"><label for="name">Prefix</label></th>
			<td>
			<select name="olimometer_currency">
			<option value="163" <?php if($current_olimometer->olimometer_currency=="163") { echo "SELECTED"; } ?>>&pound;</option>
			<option value="36" <?php if($current_olimometer->olimometer_currency=="36") { echo "SELECTED"; } ?>>$</option>
			<option value="8364" <?php if($current_olimometer->olimometer_currency=="8364") { echo "SELECTED"; } ?>>&#8364;</option>
			<option value="37" <?php if($current_olimometer->olimometer_currency=="37") { echo "SELECTED"; } ?>>%</option>
            <option value="165" <?php if($current_olimometer->olimometer_currency=="165") { echo "SELECTED"; } ?>>&yen;</option>
            <option value="162" <?php if($current_olimometer->olimometer_currency=="162") { echo "SELECTED"; } ?>>&#162;</option>
            <option value="112" <?php if($current_olimometer->olimometer_currency=="112") { echo "SELECTED"; } ?>>&#112;</option>
            <option value="8359" <?php if($current_olimometer->olimometer_currency=="8359") { echo "SELECTED"; } ?>>&#8359;</option>                
            <option value="8356" <?php if($current_olimometer->olimometer_currency=="8356") { echo "SELECTED"; } ?>>&#8356;</option> 
            <option value="176" <?php if($current_olimometer->olimometer_currency=="176") { echo "SELECTED"; } ?>>&#176;</option> 
			<option value="" <?php if($current_olimometer->olimometer_currency=="") { echo "SELECTED"; } ?>>No Prefix</option>
			</select>
			</td>
		</tr>		

		<tr class="form-field">
			<th scope="row" valign="top"><label for="name">Suffix</label></th>
			<td>
			<select name="olimometer_suffix">
			<option value="163" <?php if($current_olimometer->olimometer_suffix=="163") { echo "SELECTED"; } ?>>&pound;</option>
			<option value="36" <?php if($current_olimometer->olimometer_suffix=="36") { echo "SELECTED"; } ?>>$</option>
			<option value="8364" <?php if($current_olimometer->olimometer_suffix=="8364") { echo "SELECTED"; } ?>>&#8364;</option>
			<option value="37" <?php if($current_olimometer->olimometer_suffix=="37") { echo "SELECTED"; } ?>>%</option>
            <option value="165" <?php if($current_olimometer->olimometer_suffix=="165") { echo "SELECTED"; } ?>>&yen;</option>
            <option value="162" <?php if($current_olimometer->olimometer_suffix=="162") { echo "SELECTED"; } ?>>&#162;</option>
            <option value="112" <?php if($current_olimometer->olimometer_suffix=="112") { echo "SELECTED"; } ?>>&#112;</option>
            <option value="8359" <?php if($current_olimometer->olimometer_suffix=="8359") { echo "SELECTED"; } ?>>&#8359;</option>                
            <option value="8356" <?php if($current_olimometer->olimometer_suffix=="8356") { echo "SELECTED"; } ?>>&#8356;</option> 
            <option value="176" <?php if($current_olimometer->olimometer_suffix=="176") { echo "SELECTED"; } ?>>&#176;</option> 
			<option value="" <?php if($current_olimometer->olimometer_suffix=="") { echo "SELECTED"; } ?>>No Suffix</option>
			</select>
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
	if($current_olimometer->olimometer_skin == $olimometer_current_skin) {
		echo " selected";
	}
	echo ">".$olimometer_skin_name."</option>";	
	$olimometer_current_skin++;
}



?>

			</select>
            <p><span class="description">Choose a skin for the thermometer. A skin changes the look and design of the thermometer.</span></p></td>
		</tr>

		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name">Thermometer Height</label></th>
			<td><input name="olimometer_thermometer_height" id="olimometer_thermometer_height" type="text" value="<?php 
				echo $current_olimometer->olimometer_thermometer_height;
			?>" size="40" aria-required="true" />
            <p><span class="description">The height of the thermometer in pixels. Default = 200</span></p></td>
		</tr>

		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name">Background Colour</label></th>
			<td><input name="olimometer_thermometer_bg_colour" id="olimometer_thermometer_bg_colour" type="text" value="<?php 
				echo $current_olimometer->olimometer_thermometer_bg_colour;
			?>" size="40" aria-required="true" class="color" />
            <p><span class="description">Hex value for background colour of thermometer image (FFFFFF = white, 000000 = black)</span></p></td>
		</tr>

		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name">Transparent Background</label></th>
			<td><select name="olimometer_transparent" id="olimometer_transparent" aria-required="true" >
				<option value=0>No</option>
				<option value=1<?php
if($current_olimometer->olimometer_transparent == 1) {
	echo " selected";
}

?>>Yes</option>
			</select>
            <p><span class="description">Make the thermometer background transparent? If you select this option to yes then make sure you choose a background colour above that is close to your site's actual background colour. This will help it blend in nicely.</span></p></td>
		</tr>

		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name">Text Height</label></th>
			<td><input name="olimometer_font_height" id="olimometer_font_height" type="text" value="<?php 
				echo $current_olimometer->olimometer_font_height;
			
			?>" size="40" aria-required="true" />
            <p><span class="description">Specify the size of the font in pixels. Default = 8</span></p></td>
		</tr>

		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name">Text Colour</label></th>
			<td><input name="olimometer_text_colour" id="olimometer_text_colour" type="text" value="<?php 
				echo $current_olimometer->olimometer_text_colour;
			?>" size="40" aria-required="true" class="color" />
            <p><span class="description">Hex value for the text colour within the image (FFFFFF = white, 000000 = black)</span></p></td>
		</tr>
        
        
        <tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name">Show Target Value</label></th>
			<td><select name="olimometer_show_target" id="olimometer_show_target" aria-required="true" >
				<option value=1<?php
if($current_olimometer->olimometer_show_target == 1) {
	echo " selected";
}

?>>Yes</option>
				<option value=0<?php
if( ($current_olimometer->olimometer_show_target == 0) ) {
	echo " selected";
}

?>>No</option>
			</select>
            <p><span class="description">Do you wish the target amount raised to be displayed on the image?</span></p></td>
		</tr>

		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name">Show Progress Value (Current Amount)</label></th>
			<td><select name="olimometer_show_progress" id="olimometer_show_progress" aria-required="true" >
				<option value=1<?php
if($current_olimometer->olimometer_show_progress == 1) {
	echo " selected";
}

?>>Yes</option>
				<option value=0<?php
if( ($current_olimometer->olimometer_show_progress == 0)) {
	echo " selected";
}

?>>No</option>
			</select>
            <p><span class="description">Do you wish the current amount raised to be displayed on the image? It will be placed underneath the thermometer with an optional text string specified below...</span></p></td>
		</tr>

		<tr class="form-field">
			<th scope="row" valign="top"><label for="name">Progress Label</label></th>
			<td><input name="olimometer_progress_label" id="olimometer_progress_label" type="text" value="<?php 
				echo $current_olimometer->olimometer_progress_label;
			?>" size="40" aria-required="false" />
            <p><span class="description">(Optional) The text string to display before the Progress Value. Default = "Raised so far:"</span></p></td>
		</tr>
        
        
	</table>	
	<p class="submit"><input type="submit" class="button-primary" name="olimometer_submit" value="Save Changes" /></p>
	<?php
	//echo '<input id="old" type="hidden" value="'.get_option("olimometer_progress").'">';
	echo '</form>';
    
    ?>

<a name="diagnostics"></a>
<script type="text/javascript"><!--
google_ad_client = "ca-pub-9213372745182820";
/* Olimometer - Leaderboard */
google_ad_slot = "8984536418";
google_ad_width = 728;
google_ad_height = 90;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>  
<hr />
    
    <?php
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
	
	if($current_olimometer->get_paypal_balance() == FALSE) {
		echo "<font color=red><b>NOT WORKING</b></font>";
	}
	else {
		echo "OK. Current Balance = " . $current_olimometer->get_paypal_balance();
	}
	echo '<br />';
	echo '<hr /><a name="OtherInformation"></a>';
	echo '<h3>Other Information</h3>';	

	?>
	        <small><p><strong>Installation</strong></p>
            <p>For information on customising the Olimometer, creating skins, or for general documentation, please visit the FAQ: <a href='http://wordpress.org/extend/plugins/olimometer/faq/' target=_blank>http://wordpress.org/extend/plugins/olimometer/faq/</a></p>
            <p>You cannot delete the first Olimometer.</p>

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
			<p><strong>Credits</strong>
			<ul>
            <li>- The 'original' theme images are adapted from the PHP Fundraising Thermometer Generator by Sairam Suresh at <a href='http://www.entropyfarm.org'>www.entropyfarm.org</a></li>
            <li>- Colour Picker code courtesy of <a href="http://jscolor.com/" target="_blank">http://jscolor.com/</a>.</li>    
            <li>- TrueType Font is from the <a href='https://fedorahosted.org/liberation-fonts/'>Liberation Fonts</a> collection.</li>
			<li>- Watermaster skin courtesy of <a href='http://www.fscinternational.com'>www.fscinternational.com</a></li>
            <li>- The 'Our Progress' skins are based on the thermometer in the <a href="http://wordpress.org/extend/plugins/fundraising-thermometer-plugin-for-wordpress/" target="_blank">Our Progress</a> Wordpress plugin.</li>
            </ul>
			
            
	        </small>
            </div><!-- restofform -->
            </div><!-- Wrap -->

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
<?php

}


// Looks for shortcode parameters and calls show_olimometer with those parameters
// Defaults to olimometer_id of 1
function call_show_olimometer($atts) {
    extract( shortcode_atts( array(
		'css_class' => '',
        'id' => '1',
	), $atts ) );

	return show_olimometer($id,$css_class);
}


// Displays the olimometer img.
// Parameters:
//      $css_class = a string of css classes to be applied to the img
//      $olimometer_id = int
//
function show_olimometer($olimometer_id,$css_class = '') {
    // Load the olimometer
    $current_olimometer = new Olimometer();
    $current_olimometer->load($olimometer_id);
    return $current_olimometer->show($css_class);
}




function my_money_format($format, $num) {
		if (function_exists('money_format')) {
			 return (money_format($format,$num));
		} else {
			return "$" . number_format($num, 2);
		}
     
    }


/***************
Olimometer Sidebar Widget
****************/

class OlimometerWidget extends WP_Widget
{
  function OlimometerWidget()
  {
    $widget_ops = array('classname' => 'OlimometerWidget', 'description' => 'Displays the Olimometer in a sidebar widget' );
    $this->WP_Widget('OlimometerWidget', 'Olimometer', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'header' => '', 'footer' => '', 'img_css' => '', 'div_css' => '' ) );
    $title = $instance['title'];
    $olimometer_id = $instance['olimometer_id'];
    $header = $instance['header'];
    $footer = $instance['footer'];
    $img_css = $instance['img_css'];
    $div_css = $instance['div_css'];
?>
  <p><label for="<?php echo $this->get_field_id('olimometer_id'); ?>">Olimometer: <?php
                echo olimometer_list(attribute_escape($olimometer_id),$this->get_field_id('olimometer_id'),$this->get_field_name('olimometer_id'));
                ?>
    </label></p>
  <p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
  <p><label for="<?php echo $this->get_field_id('header'); ?>">Header: <textarea class="widefat" rows=4 id="<?php echo $this->get_field_id('header'); ?>" name="<?php echo $this->get_field_name('header'); ?>"><?php echo attribute_escape($header); ?></textarea></label></p>
  <p><label for="<?php echo $this->get_field_id('footer'); ?>">Footer: <textarea class="widefat" rows=4 id="<?php echo $this->get_field_id('footer'); ?>" name="<?php echo $this->get_field_name('footer'); ?>"><?php echo attribute_escape($footer); ?></textarea></label></p>
  <p><label for="<?php echo $this->get_field_id('img_css'); ?>">CSS class(es) for image: <input class="widefat" id="<?php echo $this->get_field_id('img_css'); ?>" name="<?php echo $this->get_field_name('img_css'); ?>" type="text" value="<?php echo attribute_escape($img_css); ?>" /></label></p>
  <p><label for="<?php echo $this->get_field_id('div_css'); ?>">CSS class(es) for widget: <input class="widefat" id="<?php echo $this->get_field_id('div_css'); ?>" name="<?php echo $this->get_field_name('div_css'); ?>" type="text" value="<?php echo attribute_escape($div_css); ?>" /></label></p>
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    $instance['header'] = $new_instance['header'];
    $instance['footer'] = $new_instance['footer'];
    $instance['img_css'] = $new_instance['img_css'];
    $instance['div_css'] = $new_instance['div_css'];
    $instance['olimometer_id'] = $new_instance['olimometer_id'];
    return $instance;
  }
 
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
 
    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
    $olimometer_id = $instance['olimometer_id'];
    $header = $instance['header'];
    $footer = $instance['footer'];
    $img_css = $instance['img_css'];
    $div_css = $instance['div_css'];
    
    if($olimometer_id > 0)
    {
        // All is good
    }
    else
    {
        // Set a default olimometer_id
        $olimometer_id = 1;
    }
       
 
    if (!empty($title))
      echo $before_title . $title . $after_title;
 
    // WIDGET CODE GOES HERE
    echo "<div id='olimometer_widget'";
    if(strlen($div_css) > 0) { 
        echo " class='$div_css'";
    }
    echo ">";
    echo $header;
    echo show_olimometer($olimometer_id,$img_css);
    echo $footer;
	echo "</div><!-- olimometer_widget div -->";    
    
    echo $after_widget;
  }
 
}
add_action( 'widgets_init', create_function('', 'return register_widget("OlimometerWidget");') );


/* *****
Start of Dashboard Widget section
**** */

function olimometer_dashboard_widget_function() {
    echo '<div class="wrap">';
    
    if(strlen(get_option("olimometer_last")) > 0)
    {
        $current_olimometer_id = get_option("olimometer_last");
    }
    else {
        $current_olimometer_id = 1;
    }
    
    // Load the olimometer
    $dash_olimometer = new Olimometer();
    $dash_olimometer->load($current_olimometer_id);
	
	?>
    
    <table>
        <form method="post" id="olimometer_selection_form" name="olimometer_selection_form">
    		<tr class="form-field form-required">
            
			<td ><label for="name">Please choose an Olimometer:</label></td>
			<td>
                <?php
                echo olimometer_list($current_olimometer_id,"olimometer_id","olimometer_id");
                ?>
             </td>

            <td><input type="submit" class="button-primary" name="olimometer_load" value="Load" /></td>
            </tr>
        </form>
    </table>
    
	<table class="form-table">
        <form method="post">
        <input type="hidden" id="olimometer_id" name="olimometer_id" value="<?php echo $current_olimometer_id ?>">
		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name">Current Amount Raised (Progress Value)</label></th>
			<td><input name="olimometer_progress_value" id="olimometer_progress_value" type="text" value="<?php 
				echo $dash_olimometer->olimometer_progress_value;
			
			?>" size="40" aria-required="true" /></td>
		</tr>
		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name">Target Amount</label></th>
			<td><input name="olimometer_total_value" id="olimometer_total_value" type="text" value="<?php 
				echo $dash_olimometer->olimometer_total_value;
			?>" size="40" aria-required="true" /></td>
		</tr>
	</table>	
<p>
<script type="text/javascript"><!--
google_ad_client = "ca-pub-9213372745182820";
/* Olimometer Admin Widget */
google_ad_slot = "8636922583";
google_ad_width = 234;
google_ad_height = 60;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
</p>
	<p><input type="submit" class="button-primary" name="olimometer_dw_submit" value="Update" />
	<?php
	//echo '<input id="old" type="hidden" value="'.get_option("olimometer_progress").'">';
	?>

        
        
&nbsp;&nbsp;<a href='options-general.php?page=olimometer_manage'>Settings</a>
	<?php
	echo '</p></form></div>'; 

} 

function olimometer_add_dashboard_widgets() {
	wp_add_dashboard_widget('olimometer_dashboard_widget', 'Olimometer', 'olimometer_dashboard_widget_function');	
} 

add_action('wp_dashboard_setup', 'olimometer_add_dashboard_widgets' );








class Olimometer
{
    function Olimometer()
    {
        // Constructor
    }
    
    // Properties
    public $olimometer_id = -1;
    public $olimometer_description = "Olimometer";
    public $olimometer_progress_value = 0;
	public $olimometer_total_value = 100;
	public $olimometer_currency = "163";
	public $olimometer_thermometer_bg_colour = "FFFFFF";
	public $olimometer_text_colour = "000000";
	public $olimometer_thermometer_height = 200;
	public $olimometer_transparent = 0;
	public $olimometer_show_target = 1;
    public $olimometer_show_progress = 1;
	public $olimometer_progress_label = "Raised so far:";
	public $olimometer_font_height = 8;
	public $olimometer_suffix = "";
	public $olimometer_skin = 0;
	public $olimometer_use_paypal = 0;
	public $olimometer_paypal_username;
	public $olimometer_paypal_password;
	public $olimometer_paypal_signature;
    
    private $olimometer_table_name = "olimometer_olimometers";
    
    // Loads database values based on supplied id
    function load($olimometer_id)
    {     
        global $wpdb;
        $table_name = $wpdb->prefix . $this->olimometer_table_name;
        $query_results = $wpdb->get_row("SELECT * FROM $table_name WHERE olimometer_id = $olimometer_id", ARRAY_A);
        
        $this->olimometer_id = $olimometer_id;
        $this->olimometer_description = $query_results['olimometer_description'];
        $this->olimometer_progress_value = $query_results['olimometer_progress_value'];
        $this->olimometer_total_value = $query_results['olimometer_total_value'];
        $this->olimometer_currency = $query_results['olimometer_currency'];
        $this->olimometer_thermometer_bg_colour = $query_results['olimometer_thermometer_bg_colour'];
        $this->olimometer_text_colour = $query_results['olimometer_text_colour'];
        $this->olimometer_thermometer_height = $query_results['olimometer_thermometer_height'];
        $this->olimometer_transparent = $query_results['olimometer_transparent'];
        $this->olimometer_show_target = $query_results['olimometer_show_target'];
        $this->olimometer_show_progress = $query_results['olimometer_show_progress'];
        $this->olimometer_progress_label = $query_results['olimometer_progress_label'];
        $this->olimometer_font_height = $query_results['olimometer_font_height'];
        $this->olimometer_suffix = $query_results['olimometer_suffix'];
        $this->olimometer_skin = $query_results['olimometer_skin'];
        $this->olimometer_use_paypal = $query_results['olimometer_use_paypal'];
        $this->olimometer_paypal_username = $query_results['olimometer_paypal_username'];	
        $this->olimometer_paypal_password = $query_results['olimometer_paypal_password'];
        $this->olimometer_paypal_signature = $query_results['olimometer_paypal_signature'];

    }
    
    // Delete the olimometer from the database
    function delete()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->olimometer_table_name;
        $wpdb->query( "DELETE FROM $table_name
                       WHERE olimometer_id = $this->olimometer_id
                      "
                    );

    }
    
    // Saves the olimometer to the database
    function save()
    { 
        global $wpdb;
        $table_name = $wpdb->prefix . $this->olimometer_table_name;
            
        // Is this an existing olimometer or a new one to be saved?
        if($this->olimometer_id == -1)
        {
            // This is a new one
            $rows_affected = $wpdb->insert( $table_name, array( 'olimometer_description' => $this->olimometer_description,
                                                                'olimometer_progress_value' => $this->olimometer_progress_value,
                                                                'olimometer_total_value' => $this->olimometer_total_value,
                                                                'olimometer_currency' => $this->olimometer_currency,
                                                                'olimometer_thermometer_bg_colour' => $this->olimometer_thermometer_bg_colour,
                                                                'olimometer_text_colour' => $this->olimometer_text_colour,
                                                                'olimometer_thermometer_height' => $this->olimometer_thermometer_height,
                                                                'olimometer_transparent' => $this->olimometer_transparent,
                                                                'olimometer_show_target' => $this->olimometer_show_target,
                                                                'olimometer_show_progress' => $this->olimometer_show_progress,
                                                                'olimometer_progress_label' => $this->olimometer_progress_label,
                                                                'olimometer_font_height' => $this->olimometer_font_height,
                                                                'olimometer_suffix' => $this->olimometer_suffix,
                                                                'olimometer_skin' => $this->olimometer_skin,
                                                                'olimometer_use_paypal' => $this->olimometer_use_paypal,
                                                                'olimometer_paypal_username' => $this->olimometer_paypal_username,	
                                                                'olimometer_paypal_password' => $this->olimometer_paypal_password,
                                                                'olimometer_paypal_signature' => $this->olimometer_paypal_signature ) );
            
            // Find out the olimometer_id of the record just created and save it to the object.
            $this->olimometer_id = $wpdb->insert_id;
        }
        else
        {
            // This is an existing one
            $wpdb->update($table_name, 
                        array(  'olimometer_description' => $this->olimometer_description,
                                'olimometer_progress_value' => $this->olimometer_progress_value,
                                'olimometer_total_value' => $this->olimometer_total_value,
                                'olimometer_currency' => $this->olimometer_currency,
                                'olimometer_thermometer_bg_colour' => $this->olimometer_thermometer_bg_colour,
                                'olimometer_text_colour' => $this->olimometer_text_colour,
                                'olimometer_thermometer_height' => $this->olimometer_thermometer_height,
                                'olimometer_transparent' => $this->olimometer_transparent,
                                'olimometer_show_target' => $this->olimometer_show_target,
                                'olimometer_show_progress' => $this->olimometer_show_progress,
                                'olimometer_progress_label' => $this->olimometer_progress_label,
                                'olimometer_font_height' => $this->olimometer_font_height,
                                'olimometer_suffix' => $this->olimometer_suffix,
                                'olimometer_skin' => $this->olimometer_skin,
                                'olimometer_use_paypal' => $this->olimometer_use_paypal,
                                'olimometer_paypal_username' => $this->olimometer_paypal_username,	
                                'olimometer_paypal_password' => $this->olimometer_paypal_password,
                                'olimometer_paypal_signature' => $this->olimometer_paypal_signature 
                        ), 
                        array( 'olimometer_id' => $this->olimometer_id )
                    );
        }
      
    }
    
    // Returns the olimometer display code
    function show($css_class = '')
    {
        // If PayPal integration is configured, get the current balance and save it
        if($this->olimometer_use_paypal == 1) {
            $olimometer_paypal_balance = $this->get_paypal_balance();
            if($olimometer_paypal_balance == false) {
                // If PayPal link is broken, set balance to 0
                $olimometer_paypal_balance = 0;
            }
            else {
                if($this->olimometer_progress_value == $olimometer_paypal_balance) {
                    // PayPal balance hasn't changed since we last checked so don't do anything
                }
                else {
                    // It has changed, so save it
                    $this->olimometer_progress_value = $olimometer_paypal_balance;
                    $this->save();
                }
            }
        }
    
    
        $olimometer_font = "LiberationSans-Regular.ttf";

        $image_location = plugins_url('olimometer/thermometer.php', dirname(__FILE__) );
        $the_olimometer_text = "<img src='".$image_location."?total=".$this->olimometer_total_value."&progress=".$this->olimometer_progress_value."&currency=".$this->olimometer_currency."&bg=".$this->olimometer_thermometer_bg_colour."&text_colour=".$this->olimometer_text_colour."&height=".$this->olimometer_thermometer_height."&transparent=".$this->olimometer_transparent."&show_progress=".$this->olimometer_show_progress."&show_target=".$this->olimometer_show_target."&progress_label=".$this->olimometer_progress_label."&font_height=".$this->olimometer_font_height."&suffix=".$this->olimometer_suffix."&skin=".$this->olimometer_skin."&font=".$olimometer_font."'";
        if(strlen($css_class) > 0) {
            $the_olimometer_text = $the_olimometer_text." class='".$css_class."'";
        }
        $the_olimometer_text = $the_olimometer_text." alt='Olimometer 2.00'>";
        return $the_olimometer_text;
    }
    
    
    
    // The following function is for PayPal balance retrieval
    function PPHttpPost($methodName_, $nvpStr_) {
        $olimometer_pp_environment = 'live';
    
        $API_UserName = urlencode($this->olimometer_paypal_username);
        $API_Password = urlencode($this->olimometer_paypal_password);
        $API_Signature = urlencode($this->olimometer_paypal_signature);
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
    
    function get_paypal_balance()
    {
        $nvpStr="";
    
        $httpParsedResponseAr = $this->PPHttpPost('GetBalance', $nvpStr);
    
        if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
            return urldecode($httpParsedResponseAr[L_AMT0]);
        }
        else  {
            return false;
        }
    }

    
}



/************************
Database Functions
************************/
global $olimometer_db_version;
$olimometer_db_version = "2.00";

function olimometer_install() {
   global $wpdb;
   global $olimometer_db_version;

   $table_name = $wpdb->prefix . "olimometer_olimometers";
   
   // Create the table....
   $sql = "CREATE TABLE $table_name (
  olimometer_id mediumint(9) NOT NULL AUTO_INCREMENT,
  olimometer_description VARCHAR(255),
  olimometer_progress_value DOUBLE,
  olimometer_total_value DOUBLE,
  olimometer_currency VARCHAR(255),
  olimometer_thermometer_bg_colour VARCHAR(255),
  olimometer_text_colour VARCHAR(255),
  olimometer_thermometer_height mediumint(9),
  olimometer_transparent tinyint,
  olimometer_show_target tinyint,
  olimometer_show_progress tinyint,
  olimometer_progress_label VARCHAR(255),
  olimometer_font_height smallint,
  olimometer_suffix VARCHAR(255),
  olimometer_skin smallint,
  olimometer_use_paypal tinyint,
  olimometer_paypal_username VARCHAR(255),
  olimometer_paypal_password VARCHAR(255),
  olimometer_paypal_signature VARCHAR(255),
  UNIQUE KEY olimometer_id (olimometer_id)
    );";

   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($sql);
 
   add_option("olimometer_db_version", $olimometer_db_version);
   
    // Now, create the first olimometer object if one doesn't exist:
    $olimometer_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name;" ) );
    if($olimometer_count >0) {
        // More 
    }
    else {
   
        $first_olimometer = new Olimometer();
        $first_olimometer->save();
    }
   
}



// Check for updates
function update_check() {
    // Has this plugin been updated to v2.00 (database version)?
    if( (strlen(get_option("olimometer_updated_to_two")) > 0) && (strlen(get_option("olimometer_db_version")) > 0))
    {
        // Yes it has!
    }
    else
    {
        // No it hasn't, so we need to import the old variables and save as the first olimometer
        $old_olimometer = new Olimometer();
        $old_olimometer->olimometer_id = 1;
        
        if(strlen(get_option("olimometer_progress_value")) > 0) {$old_olimometer->olimometer_progress_value = get_option("olimometer_progress_value");}
        if(strlen(get_option("olimometer_total_value") ) > 0 ) { $old_olimometer->olimometer_total_value = get_option("olimometer_total_value"); }
        if(strlen(get_option("olimometer_currency")) > 0) {$old_olimometer->olimometer_currency = get_option("olimometer_currency");}
        if(strlen(get_option("olimometer_thermometer_bg_colour")) > 1) {$old_olimometer->olimometer_thermometer_bg_colour = get_option("olimometer_thermometer_bg_colour");}
        if(strlen(get_option("olimometer_text_colour")) > 1) {$old_olimometer->olimometer_text_colour = get_option("olimometer_text_colour");}
        if(strlen(get_option("olimometer_thermometer_height")) > 1) {$old_olimometer->olimometer_thermometer_height = get_option("olimometer_thermometer_height");}
        if(strlen(get_option("olimometer_transparent")) > 0) {$old_olimometer->olimometer_transparent = get_option("olimometer_transparent");}
        if(strlen(get_option("olimometer_show_target")) > 0) {$old_olimometer->olimometer_show_target = get_option("olimometer_show_target");}
        if(strlen(get_option("olimometer_show_progress")) > 0) {$old_olimometer->olimometer_show_progress = get_option("olimometer_show_progress");}
        if(strlen(get_option("olimometer_progress_label")) > 1) {$old_olimometer->olimometer_progress_label = get_option("olimometer_progress_label");}
        if(strlen(get_option("olimometer_font_height")) > 1) {$old_olimometer->olimometer_font_height = get_option("olimometer_font_height");}
        if(strlen(get_option("olimometer_suffix")) > 0) {$old_olimometer->olimometer_suffix = get_option("olimometer_suffix");}
        if(strlen(get_option("olimometer_skin")) > 0) {$old_olimometer->olimometer_skin = get_option("olimometer_skin");}
        if(strlen(get_option("olimometer_use_paypal")) > 0) {$old_olimometer->olimometer_use_paypal = get_option("olimometer_use_paypal");}
        if(strlen(get_option("olimometer_paypal_username")) > 0) {$old_olimometer->olimometer_paypal_username = get_option("olimometer_paypal_username");}
        if(strlen(get_option("olimometer_paypal_password")) > 0) {$old_olimometer->olimometer_paypal_password = get_option("olimometer_paypal_password");}
        if(strlen(get_option("olimometer_paypal_signature")) > 0) {$old_olimometer->olimometer_paypal_signature = get_option("olimometer_paypal_signature");}
        
        $old_olimometer->save();
        
        // Mark the old olimometer as upgraded
        update_option("olimometer_updated_to_two", 1);
    }
    
}


function olimometer_list($selected_olimometer,$form_id,$form_name)
{
    $current_olimometer_id = $selected_olimometer;
    
    // Create a form of olimometers from the database
    global $wpdb;
    $table_name = $wpdb->prefix . "olimometer_olimometers";
    $search_results = $wpdb->get_results( 
        "
        SELECT olimometer_id, olimometer_description
        FROM $table_name
        "
    );
    
    $return_string = "<select name='". $form_name . "' id='". $form_id ."' aria-required='true' >";
    
    foreach ( $search_results as $search_result ) 
    {
        $return_string = $return_string . "<option value='".$search_result->olimometer_id."'";
        if($current_olimometer_id == $search_result->olimometer_id)
        {
            $return_string = $return_string . " SELECTED";
        }
        $return_string = $return_string . ">".$search_result->olimometer_description."</option>";
    }
               
    $return_string = $return_string . "</select>";
    return $return_string;
}

?>