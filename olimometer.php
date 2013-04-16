<?php
/*
Plugin Name: Olimometer
Plugin URI: http://www.olivershingler.co.uk/oliblog/olimometer/
Description: A dynamic fundraising thermometer with PayPal integration, customisable height, currency, background colour, transparency and skins.
Author: Oliver Shingler
Author URI: http://www.olivershingler.co.uk
Version: 2.45
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

/* Create the capabilities for this plugin
    - Make sure the administrator can always access this */
$olimometer_capability_dashboard = "olimometer_dashboard_widget";
$role = get_role( 'administrator' );
$role->add_cap( $olimometer_capability_dashboard );

require_once("olimometer-class.php");


/* Make sure we know where the Olimometer skins are stored */
update_option("olimometer_skins_location",WP_PLUGIN_DIR."/".plugin_basename(dirname(__FILE__))."/");
update_option("olimometer_skins_custom_location",WP_CONTENT_DIR."/uploads/olimometer/");


/* Create new Olimometer*/
if (isset($_REQUEST['olimometer_create']) && isset($_REQUEST['olimometer_description'])) {
    $new_olimometer = new Olimometer();
    $new_olimometer->olimometer_description = $_REQUEST['olimometer_description'];
    $new_olimometer->save();
    update_olimometer_last($new_olimometer->olimometer_id);
}

/* Delete an Olimometer*/
if (isset($_REQUEST['olimometer_delete'])) {
    if($_REQUEST['olimometer_id'] == 1)
    {
        // This is Olimometer #1... Can't delete it!
    }
    else
    {
        $dead_olimometer = new Olimometer();
        
        $dead_olimometer->load($_REQUEST['olimometer_id']);
        $dead_olimometer->delete();
        update_olimometer_last(1);
    }
}

/* Load an Olimometer */
if (isset($_REQUEST['olimometer_load'])) {
    // Which one?
    update_olimometer_last($_REQUEST['olimometer_id']);
}


/* Global Options Save*/
if (isset($_REQUEST['olimometer_global_submit'])) {
    
    // What was the old role?
    $old_olimometer_dashboard_role = get_option('olimometer_dashboard_role','administrator');
    
    // Need to save the dashboard role
    $new_olimometer_dashboard_role = $_REQUEST['olimometer_dashboard_role'];
    update_option("olimometer_dashboard_role",$new_olimometer_dashboard_role);
    

    
    if($old_olimometer_dashboard_role == $new_olimometer_dashboard_role) {
        // No change.. do nothing
    }
    else {
        // Right, so we need to remove the old role's permissions as long as this isn't the administrator
        if($old_olimometer_dashboard_role == 'administrator') {
            // Don't do it.... we don't want to lock them out
        }
        else {
            // Bye bye capability for the old role
            $role = get_role( $old_olimometer_dashboard_role );
            $role->remove_cap( $olimometer_capability_dashboard );
        }
        // Now, we add the capability for the new role
        $role = get_role( $new_olimometer_dashboard_role );
        $role->add_cap( $olimometer_capability_dashboard );
    }
    
}

/* Main Settings save */
if (isset($_REQUEST['olimometer_submit']) && isset($_REQUEST['olimometer_total_value'])) {
   
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
	//$an_olimometer->olimometer_skin = $_REQUEST['olimometer_skin'];
    $an_olimometer->olimometer_skin_slug = $_REQUEST['olimometer_skin_slug'];
	$an_olimometer->olimometer_use_paypal = $_REQUEST['olimometer_use_paypal'];
	$an_olimometer->olimometer_paypal_username = $_REQUEST['olimometer_paypal_username'];
	$an_olimometer->olimometer_paypal_password = $_REQUEST['olimometer_paypal_password'];
	$an_olimometer->olimometer_paypal_signature = $_REQUEST['olimometer_paypal_signature'];
    $an_olimometer->olimometer_paypal_extra_value = $_REQUEST['olimometer_paypal_extra_value'];
    $an_olimometer->olimometer_number_format = $_REQUEST['olimometer_number_format'];
    $an_olimometer->olimometer_link = $_REQUEST['olimometer_link'];
    //echo "Overlay = xxx".$_REQUEST['olimometer_overlay']."xxx";
    $an_olimometer->olimometer_overlay = $_REQUEST['olimometer_overlay'];
    $an_olimometer->olimometer_overlay_image = $_REQUEST['upload_image'];
    $an_olimometer->olimometer_overlay_x = $_REQUEST['olimometer_overlay_x'];
    $an_olimometer->olimometer_overlay_y = $_REQUEST['olimometer_overlay_y'];
    $an_olimometer->olimometer_stayclassypid = $_REQUEST['olimometer_stayclassypid'];
    
    // Save it
    $an_olimometer->save();

}

/* Dashboard Widget save */
if (isset($_REQUEST['olimometer_dw_submit']) && isset($_REQUEST['olimometer_total_value'])) {

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

function olimometer_admin_scripts() {
    //echo "loading admin scripts";
    wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
    wp_register_script('my-upload', WP_PLUGIN_URL.'/olimometer/my-script.js', array('jquery','media-upload','thickbox'));
    wp_enqueue_script('my-upload');
    }
 
function olimometer_admin_styles() {
    //echo "loading admin styles";
    wp_enqueue_style('thickbox');
    }

if (isset($_GET['page']) && $_GET['page'] == 'olimometer_manage') {
        // Add hooks to load image upload scripts
        add_action('admin_print_scripts', 'olimometer_admin_scripts');
        add_action('admin_print_styles', 'olimometer_admin_styles');
        }

function olimometer_manage_page() {
    echo '<div class="wrap">';


?>
<script type="text/javascript" src="<?php echo plugins_url(); ?>/olimometer/jscolor/jscolor.js"></script>

<script language="javascript">

    /*
    function olimometer_progress_disable() {
    document.olimometer_form1.olimometer_progress_value.readOnly = true;
    document.olimometer_form1.olimometer_paypal_username.readOnly = false;
    document.olimometer_form1.olimometer_paypal_password.readOnly = false;
    document.olimometer_form1.olimometer_paypal_signature.readOnly = false;
    document.olimometer_form1.olimometer_paypal_extra_value.readOnly = false;
    }

    function olimometer_progress_enable() {
    document.olimometer_form1.olimometer_progress_value.readOnly = false;
    document.olimometer_form1.olimometer_paypal_username.readOnly = true;
    document.olimometer_form1.olimometer_paypal_password.readOnly = true;
    document.olimometer_form1.olimometer_paypal_signature.readOnly = true;
    document.olimometer_form1.olimometer_paypal_extra_value.readOnly = true;
    }*/

    function olimometer_progress($progress_type) {
        // 0 = Manual
        // 1 = PayPal
        // 2 = StayClassy
        if ($progress_type == 0) {
            // Enable manual
            olimometer_disable_manual(false);
            // Disable PayPal
            olimometer_disable_paypal(true);
            // Disable StayClassy
            olimometer_disable_stayclassy(true);
        }
        if ($progress_type == 1) {
            // Enable PayPal
            olimometer_disable_paypal(false);
            // Disable Manual
            olimometer_disable_manual(true);
            // Disable StayClassy
            olimometer_disable_stayclassy(true);
        }
        if ($progress_type == 2) {
            // Enable StayClassy
            olimometer_disable_stayclassy(false);
            // Disable PayPal
            olimometer_disable_paypal(true);
            // Disable Manual
            olimometer_disable_manual(true);
        }
    }

    function olimometer_disable_manual($tof) {
        document.olimometer_form1.olimometer_progress_value.readOnly = $tof;
    }

    function olimometer_disable_paypal($tof) {
        document.olimometer_form1.olimometer_paypal_username.readOnly = $tof;
        document.olimometer_form1.olimometer_paypal_password.readOnly = $tof;
        document.olimometer_form1.olimometer_paypal_signature.readOnly = $tof;
        document.olimometer_form1.olimometer_paypal_extra_value.readOnly = $tof;
    }

    function olimometer_disable_stayclassy($tof) {
        document.olimometer_form1.olimometer_stayclassypid.readOnly = $tof;
    }

    function olimometer_overlay_disable() {
        document.olimometer_form1.upload_image.readOnly = true;
        document.olimometer_form1.olimometer_overlay_x.readOnly = true;
        document.olimometer_form1.olimometer_overlay_y.readOnly = true;
        document.olimometer_form1.upload_image_button.disabled = true;
    }

    function olimometer_overlay_enable() {
        document.olimometer_form1.upload_image.readOnly = false;
        document.olimometer_form1.olimometer_overlay_x.readOnly = false;
        document.olimometer_form1.olimometer_overlay_y.readOnly = false;
        document.olimometer_form1.upload_image_button.disabled = false;
    }

</script>

<?php
    // Load the olimometer values:
    // If we are being asked to load a particular Olimometer's settings
    if (isset($_REQUEST['olimometer_load'])) {
        // Which one?
        update_olimometer_last($_REQUEST['olimometer_id']);
        $current_olimometer_id = $_REQUEST['olimometer_id'];
        
    }
    else {
        if(get_olimometer_last() == 0)
        {
            $current_olimometer_id = 1;
        }
        else {
            $current_olimometer_id = get_olimometer_last();
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
<div id="olimometer_global_wrapper">
    <div class="alignleft" style="padding-right:12px;margin-right:12px;border-right:1px dashed grey;">
    <h3><?php echo $current_olimometer->olimometer_description; ?> Options</h3>
    <?php

    
    
    // Now start the main options page
	echo '<p>';
    echo '<a href="#olimometer_details">Olimometer Details</a><br />';
    echo '<a href="#progressvalues">Progress Values</a><br />';
	echo '<a href="#appearance">Appearance and Layout</a><br />';
	echo '<a href="#diagnostics">Diagnostics</a><br />';
	echo '<a href="#OtherInformation">Other Information</a></p>';

    ?>
    </div>
    <div id="olimometer_global_options">
        <!-- Global Options -->
        <h3>Global Options</h3>
        <form method="post" id="olimometer_global_options" name="olimometer_global_options">
        <table>
           	<tr class="form-field form-required">
                <td valign="center" align="left">Dashboard Widget Role:</td>
                <td><select name="olimometer_dashboard_role" id="olimometer_dashboard_role" aria-required="true" >
                <?php
                // What's the current saved role for this option? administrator is default
                $olimometer_dashboard_role = get_option("olimometer_dashboard_role", "administrator");
                
                // Display a drop-down list of all available roles, with the current saved one selected
                wp_dropdown_roles($selected = $olimometer_dashboard_role);
                ?>
                </select>
                
                </td>
            </tr>
            
        <tr>
           <td colspan=2><span class="description">Administrators will always have access to both the settings and dashboard widget.</span> </td>
        </tr>
        <tr>
           <td colspan=2>
        <input type="submit" class="button-primary" name="olimometer_global_submit" value="Save Global Options" />    
            </td>
        </tr>
            
        
        </table>
        
        </form>
        
    </div>
</div> <!-- olimometer_global_wrapper -->

<a name="olimometer_details"></a>

<div id="olimometer_details_wrapper" style="clear:both;">
<hr />

    <form method="post" id="olimometer_form1" name="olimometer_form1">
    <input type="hidden" id="olimometer_id" name="olimometer_id" value="<?php echo $current_olimometer_id; ?>">

    <div class="alignleft" style="clear:both;margin-right:10px;">
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
        <div><h3>Preview</h3>
            <?php echo show_olimometer($current_olimometer_id); ?>
        </div>
        
    </div><!-- olimometer_details_wrapper -->        
        
    <div id="restofform" style="clear:both;">
    <?php
	echo '<hr /><a name="progressvalues"></a>';
	echo '<h3>Progress Values</h3>';
	
	?>
	<table class="form-table">
		<tr class="form-required">
			<th scope="row" valign="top"><label for="name">Manual or Automatic Progress Tracking?</label></th>
			<td><input name="olimometer_use_paypal" id="olimometer_use_paypal" type="radio" value="0"<?php
if($current_olimometer->olimometer_use_paypal == 0) {
	echo " checked";
}

?> onClick="olimometer_progress(0);"> Manual<br />
			    <input name="olimometer_use_paypal" id="olimometer_use_paypal" type="radio" value="1"<?php
if($current_olimometer->olimometer_use_paypal == 1) {
	echo " checked";
}

?> onClick="olimometer_progress(1);"> PayPal<br />
			    <input name="olimometer_use_paypal" id="olimometer_use_paypal" type="radio" value="2"<?php
if($current_olimometer->olimometer_use_paypal == 2) {
	echo " checked";
}

?> onClick="olimometer_progress(2);"> StayClassy

            <p><span class="description">Do you want to update the progress (current amount raised) manually or automatically by linking to a PayPal or StayClassy account?</span></p></td>

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
        
        <tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name">Number Format</label></th>
			<td>
                <select name="olimometer_number_format">
			<option value="0" <?php if($current_olimometer->olimometer_number_format==0) { echo "SELECTED"; } ?>>1000</option>
			<option value="1" <?php if($current_olimometer->olimometer_number_format==1) { echo "SELECTED"; } ?>>1,000</option>
			<option value="2" <?php if($current_olimometer->olimometer_number_format==2) { echo "SELECTED"; } ?>>1000.00</option>
			<option value="3" <?php if($current_olimometer->olimometer_number_format==3) { echo "SELECTED"; } ?>>1,000.00</option>
            <option value="4" <?php if($current_olimometer->olimometer_number_format==4) { echo "SELECTED"; } ?>>1.000</option>
            <option value="5" <?php if($current_olimometer->olimometer_number_format==5) { echo "SELECTED"; } ?>>1.000,00</option>			
            <option value="6" <?php if($current_olimometer->olimometer_number_format==6) { echo "SELECTED"; } ?>>1 000</option>
            <option value="7" <?php if($current_olimometer->olimometer_number_format==7) { echo "SELECTED"; } ?>>1 000,00</option>
            <option value="8" <?php if($current_olimometer->olimometer_number_format==8) { echo "SELECTED"; } ?>>1 000.00</option>                    
            </select>
            <p><span class="description">Please choose a display format for your values.</span></p></td>
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
        
       <tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name">Offline Donations</label></th>
			<td><input name="olimometer_paypal_extra_value" id="olimometer_paypal_extra_value" type="text" value="<?php 
				echo $current_olimometer->olimometer_paypal_extra_value;
			
			?>" size="40" aria-required="true" />
            <p><span class="description">How much has been raised offline? This amount will be added to the PayPal total.</span></p></td>
		</tr>
		
        <tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name">StayClassy PID</label></th>
			<td><input name="olimometer_stayclassypid" id="olimometer_stayclassypid" type="text" value="<?php 
				echo $current_olimometer->olimometer_stayclassypid;
			
			?>" size="40" aria-required="true" />
            <p><span class="description">Please enter your unique StayClassy.org project ID for which you would like to track the total.</span></p></td>
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
            <option value="10000" <?php if($current_olimometer->olimometer_currency=="10000") { echo "SELECTED"; } ?>>kr</option>
 			<option value="10001" <?php if($current_olimometer->olimometer_currency=="10001") { echo "SELECTED"; } ?>>CHF</option> 
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
            <option value="10000" <?php if($current_olimometer->olimometer_suffix=="10000") { echo "SELECTED"; } ?>>kr</option>
 			<option value="10001" <?php if($current_olimometer->olimometer_suffix=="10001") { echo "SELECTED"; } ?>>CHF</option> 
			<option value="" <?php if($current_olimometer->olimometer_suffix=="") { echo "SELECTED"; } ?>>No Suffix</option>
			</select>
			</td>
		</tr>
		
		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name">Thermometer Skin</label></th>
			<td><select name="olimometer_skin_slug" id="olimometer_skin_slug" aria-required="true" >

<?php
// Import list of Olimometer skins from XML file
include_once('skins.php');	

$olimometer_skins = new Olimometer_Skins();
$olimometer_skins->olimometer_skins_location = get_option("olimometer_skins_location");
$olimometer_skins->olimometer_skins_custom_location = get_option("olimometer_skins_custom_location");
$olimometer_skins->load();

$olimometer_skin_names = array();
$olimometer_skin_names = $olimometer_skins->get_skin_names();

// Loop around each skin name and display in a drop-down list
foreach ($olimometer_skin_names as $olimometer_skin_name) {
	echo "<option value='".$olimometer_skin_name["skin_slug"]."'";
	if($current_olimometer->olimometer_skin_slug == $olimometer_skin_name["skin_slug"]) {
		echo " selected";
	}
	echo ">".$olimometer_skin_name["skin_name"]."</option>";
}



?>

			</select>
            <p><span class="description">Choose a skin for the thermometer. A skin changes the look and design of the thermometer.</span></p></td>
		</tr>

		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name">Thermometer Height/Width</label></th>
			<td><input name="olimometer_thermometer_height" id="olimometer_thermometer_height" type="text" value="<?php 
				echo $current_olimometer->olimometer_thermometer_height;
			?>" size="40" aria-required="true" />
            <p><span class="description">The height (or width if using a horizontal skin) of the thermometer in pixels. Default = 200</span></p></td>
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
        
        <tr class="form-field">
			<th scope="row" valign="top"><label for="name">Olimometer Hyperlink URL</label></th>
			<td><input name="olimometer_link" id="olimometer_link" type="text" value="<?php 
				echo $current_olimometer->olimometer_link;
			?>" size="40" aria-required="false" />
            <p><span class="description">(Optional) The URL users are directed to when clicking on an Olimometer image.</span></p></td>
		</tr>


        <!-- Overlay Image Begin -->

        <tr class="form-required">
			<th scope="row" valign="top"><label for="name">Would you like to overlay an image on to the Olimometer?</label></th>
			<td><input name="olimometer_overlay" id="olimometer_overlay" type="radio" value="0"<?php
if($current_olimometer->olimometer_overlay == 0) {
	echo " checked";
}

?> onClick="olimometer_overlay_disable();"> No<br />
			    <input name="olimometer_overlay" id="olimometer_overlay" type="radio" value="1"<?php
if($current_olimometer->olimometer_overlay == 1) {
	echo " checked";
}

?> onClick="olimometer_overlay_enable();"> Yes

            <p><span class="description">The overlay image will be placed over the top of the Olimometer. You will need to specify x and y co-ordinates, in pixels, corresponding to where you would like to position the overlay in respect to the top-left corner of the Olimometer. This feature only works on vertical Olimometers.</span></p></td>

		</tr>

        <tr class="form-field">
            <th scope="row" valign="top"><label for="name">Overlay Image</label></th>
            <td><label for="upload_image"><input id="upload_image" type="text" size="36" name="upload_image" value="<?php 
				echo $current_olimometer->olimometer_overlay_image;
			?>" /><input id="upload_image_button" type="button" value="Upload Image" /><br />Enter a URL or upload an image for the overlay. NOTE: Only .PNG files are supported. Once uploaded, click on the 'Insert Into Post' button to auto-complete this field with your chosen image.</label></td>
        </tr>
        
        <tr class="form-field">
			<th scope="row" valign="top"><label for="name">Overlay X Co-ordinate</label></th>
			<td><input name="olimometer_overlay_x" id="olimometer_overlay_x" type="text" value="<?php 
				echo $current_olimometer->olimometer_overlay_x;
			?>" size="40" aria-required="false" />
            <p><span class="description"></span></p></td>
		</tr>
        <tr class="form-field">
			<th scope="row" valign="top"><label for="name">Overlay Y Co-ordinate</label></th>
			<td><input name="olimometer_overlay_y" id="olimometer_overlay_y" type="text" value="<?php 
				echo $current_olimometer->olimometer_overlay_y;
			?>" size="40" aria-required="false" />
            <p><span class="description"></span></p></td>
		</tr>

        
	</table>	
	<p class="submit"><input type="submit" class="button-primary" name="olimometer_submit" value="Save Changes" /></p>
	<?php
	echo '</form>';
    
    ?>

<a name="diagnostics"></a>
<script type="text/javascript"><!--
google_ad_client = "ca-pub-9213372745182820";
/* Olimometer - Leaderboard */
google_ad_slot = "8984536418    ";
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
            <li>- The 'ProgPress' skins are based on the progress meters of the <a href="http://wordpress.org/extend/plugins/progpress/" target="_blank">ProgPress</a> plugin for Wordpress</li>
            </ul>
			
            
	        </small>
            </div><!-- restofform -->
            </div><!-- Wrap -->

<script language="javascript">

if(document.olimometer_form1.olimometer_use_paypal[0].checked)
{
olimometer_progress(0);
}
if(document.olimometer_form1.olimometer_use_paypal[1].checked)
{
olimometer_progress(1);
}
if(document.olimometer_form1.olimometer_use_paypal[2].checked)
{
olimometer_progress(2);
}

if(document.olimometer_form1.olimometer_overlay[0].checked)
{
olimometer_overlay_disable();
}
else
{
olimometer_overlay_enable();
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
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'header' => '', 'footer' => '', 'img_css' => '', 'div_css' => '', 'olimometer_donate_address' => '', 'olimometer_donate_currency' => '', 'olimometer_donate_locale' => '' ) );
    $title = $instance['title'];
    $olimometer_id = $instance['olimometer_id'];
    $header = $instance['header'];
    $footer = $instance['footer'];
    $img_css = $instance['img_css'];
    $div_css = $instance['div_css'];
    $olimometer_donate_address = $instance['olimometer_donate_address'];
    $olimometer_donate_currency = $instance['olimometer_donate_currency'];
    $olimometer_donate_locale = $instance['olimometer_donate_locale'];

    if($olimometer_donate_locale == '') {
        // Set default locale
        $olimometer_donate_locale = 'en_US';
    }


    $currency_codes = array('AUD' => 'Australian Dollars (A $)',
						   	'CAD' => 'Canadian Dollars (C $)',
						   	'EUR' => 'Euros (&euro;)',
						   	'GBP' => 'Pounds Sterling (&pound;)',
						   	'JPY' => 'Yen (&yen;)',
						   	'USD' => 'U.S. Dollars ($)',
						   	'NZD' => 'New Zealand Dollar ($)',
						   	'CHF' => 'Swiss Franc',
						   	'HKD' => 'Hong Kong Dollar ($)',
						   	'SGD' => 'Singapore Dollar ($)',
						   	'SEK' => 'Swedish Krona',
						   	'DKK' => 'Danish Krone',
						   	'PLN' => 'Polish Zloty',
						   	'NOK' => 'Norwegian Krone',
						   	'HUF' => 'Hungarian Forint',
						   	'CZK' => 'Czech Koruna',
						   	'ILS' => 'Israeli Shekel',
						   	'MXN' => 'Mexican Peso',
						   	'BRL' => 'Brazilian Real',
						   	'TWD' => 'Taiwan New Dollar',
						   	'PHP' => 'Philippine Peso',
						   	'TRY' => 'Turkish Lira',
						   	'THB' => 'Thai Baht');

    $localized_buttons = array('en_AU' => 'Australia - Australian English',
								   'de_DE/AT' => 'Austria - German',
								   'nl_NL/BE' => 'Belgium - Dutch',
								   'fr_XC' => 'Canada - French',
								   'zh_XC' => 'China - Simplified Chinese',
								   'fr_FR/FR' => 'France - French',
								   'de_DE/DE' => 'Germany - German',
								   'it_IT/IT' => 'Italy - Italian',
								   'ja_JP/JP' => 'Japan - Japanese',
								   'es_XC' => 'Mexico - Spanish',
								   'nl_NL/NL' => 'Netherlands - Dutch',
								   'pl_PL/PL' => 'Poland - Polish',
								   'es_ES/ES' => 'Spain - Spanish',
								   'de_DE/CH' => 'Switzerland - German',
								   'fr_FR/CH' => 'Switzerland - French',
								   'en_US' => 'United States - U.S. English');


?>
  <p><label for="<?php echo $this->get_field_id('olimometer_id'); ?>">Olimometer: <?php
                echo olimometer_list(esc_attr($olimometer_id),$this->get_field_id('olimometer_id'),$this->get_field_name('olimometer_id'));
                ?>
    </label></p>
  <p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p>
  <p><label for="<?php echo $this->get_field_id('header'); ?>">Header: <textarea class="widefat" rows=4 id="<?php echo $this->get_field_id('header'); ?>" name="<?php echo $this->get_field_name('header'); ?>"><?php echo esc_attr($header); ?></textarea></label></p>
  <p><label for="<?php echo $this->get_field_id('footer'); ?>">Footer: <textarea class="widefat" rows=4 id="<?php echo $this->get_field_id('footer'); ?>" name="<?php echo $this->get_field_name('footer'); ?>"><?php echo esc_attr($footer); ?></textarea></label></p>
  <p><label for="<?php echo $this->get_field_id('img_css'); ?>">CSS class(es) for image: <input class="widefat" id="<?php echo $this->get_field_id('img_css'); ?>" name="<?php echo $this->get_field_name('img_css'); ?>" type="text" value="<?php echo esc_attr($img_css); ?>" /></label></p>
  <p><label for="<?php echo $this->get_field_id('div_css'); ?>">CSS class(es) for widget: <input class="widefat" id="<?php echo $this->get_field_id('div_css'); ?>" name="<?php echo $this->get_field_name('div_css'); ?>" type="text" value="<?php echo esc_attr($div_css); ?>" /></label></p>
  <p><label for="<?php echo $this->get_field_id('olimometer_donate_address'); ?>">If you'd like a PayPal donate button, enter your account email address here: <input class="widefat" id="<?php echo $this->get_field_id('olimometer_donate_address'); ?>" name="<?php echo $this->get_field_name('olimometer_donate_address'); ?>" type="text" value="<?php echo esc_attr($olimometer_donate_address); ?>" /></label></p>
  <p><label for="<?php echo $this->get_field_id('olimometer_donate_currency'); ?>">PayPal donation currency:<select name="<?php echo $this->get_field_name('olimometer_donate_currency'); ?>" id="<?php echo $this->get_field_id('olimometer_donate_currency'); ?>">
    <?php 
		foreach ( $currency_codes as $key => $code ) {
	        echo '<option value="'.$key.'"';
			if (esc_attr($olimometer_donate_currency) == $key) {
                echo ' selected="selected"';
            }
			echo '>'.$code.'</option>';
		} ?></select></label></p>
  <p><label for="<?php echo $this->get_field_id('olimometer_donate_locale'); ?>">PayPal donation locale:<select name="<?php echo $this->get_field_name('olimometer_donate_locale'); ?>" id="<?php echo $this->get_field_id('olimometer_donate_locale'); ?>">
    <?php 
		foreach ( $localized_buttons as $key => $code ) {
	        echo '<option value="'.$key.'"';
			if (esc_attr($olimometer_donate_locale) == $key) {
                echo ' selected="selected"';
            }
			echo '>'.$code.'</option>';
		} ?></select></label></p>


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
    $instance['olimometer_donate_address'] = $new_instance['olimometer_donate_address'];
    $instance['olimometer_donate_currency'] = $new_instance['olimometer_donate_currency'];
    $instance['olimometer_donate_locale'] = $new_instance['olimometer_donate_locale'];
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
    $olimometer_donate_address = $instance['olimometer_donate_address'];
    $olimometer_donate_currency = $instance['olimometer_donate_currency'];
    $olimometer_donate_locale = $instance['olimometer_donate_locale'];
    
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

    if($olimometer_donate_address == "") {
        // It's empty, so don't display a paypal button
    }
    else {
        ?>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post"><div class="paypal-payments"><input type="hidden" name="cmd" value="_donations" /><input type="hidden" name="business" value="<?php echo $olimometer_donate_address; ?>" /><input type="hidden" name="currency_code" value="<?php echo $olimometer_donate_currency; ?>" /><input type="image" style="padding: 5px 0;" id="ppbutton" src="https://www.paypal.com/<?php echo $olimometer_donate_locale; ?>/i/btn/btn_donate_LG.gif" name="submit" alt="PayPal - The safer, easier way to pay online." /><img alt="" id="ppbutton" style="padding: 5px 0;" src="https://www.paypal.com/<?php echo $olimometer_donate_locale; ?>/i/scr/pixel.gif" width="1" height="1" /></div></form>
        <?php
    }

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
    
    if(strlen(get_olimometer_last()) > 0)
    {
        $current_olimometer_id = get_olimometer_last();
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

&nbsp;&nbsp;<a href='options-general.php?page=olimometer_manage'>Settings</a>
	<?php
	echo '</p></form></div>'; 

} 

function olimometer_add_dashboard_widgets() {
    if ( current_user_can( "olimometer_dashboard_widget" ) ) {
	    wp_add_dashboard_widget('olimometer_dashboard_widget', 'Olimometer', 'olimometer_dashboard_widget_function');	
    }
} 

add_action('wp_dashboard_setup', 'olimometer_add_dashboard_widgets' );











/************************
Database Functions
************************/
global $olimometer_db_version;
$olimometer_db_version = "2.41";

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
  olimometer_skin_slug VARCHAR(255),
  olimometer_use_paypal tinyint,
  olimometer_paypal_extra_value DOUBLE,
  olimometer_paypal_username VARCHAR(255),
  olimometer_paypal_password VARCHAR(255),
  olimometer_paypal_signature VARCHAR(255),
  olimometer_number_format tinyint,
  olimometer_link VARCHAR(255),
  olimometer_overlay tinyint,
  olimometer_overlay_image VARCHAR(255),
  olimometer_overlay_x int,
  olimometer_overlay_y int,
  olimometer_stayclassypid int,
  UNIQUE KEY olimometer_id (olimometer_id)
    );";

   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($sql);
 
   update_option("olimometer_db_version", $olimometer_db_version);
   
    // Now, create the first olimometer object if one doesn't exist:
    $olimometer_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name;" ) );
    if($olimometer_count == 0)
    {
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
        // If currently installed database version is less than current version required for this plugin, then we need to upgrade
        $required_db_version = 2.41;
        $installed_db_version = get_option("olimometer_db_version");
        if($installed_db_version < $required_db_version) {
            olimometer_install();
            // Now we need to do one-off upgrades for specific versions:
            if($installed_db_version < 2.20) {
                // Skin upgrade, we need to work out which skins were used in the database and write the slug names in
                global $wpdb;
                $table_name = $wpdb->prefix . "olimometer_olimometers";
                $search_results = $wpdb->get_results( 
                    "
                    SELECT *
                    FROM $table_name
                    "
                );
                
                // Loop around results:
                foreach ( $search_results as $search_result ) 
                {
                    // What is the id of the olimometer?
                    $search_olimometer_id = $search_result->olimometer_id;
                    
                    // What was the skin id used?
                    $search_skin = $search_result->olimometer_skin;
                    
                    // Check the skin id and create a slug accordingly
                    $new_slug = "";
                    switch ($search_skin) {
                        case 0:
                            $new_slug = "oli-default";
                            break;
                        case 1:
                            $new_slug = "oli-rounded";
                            break;
                        case 2:
                            $new_slug = "oli-bold-chunky";
                            break;
                        case 3:
                            $new_slug = "oli-watermaster";
                            break;
                        case 4:
                            $new_slug = "oli-ourprogress-blue";
                            break;                            
                        case 5:
                            $new_slug = "oli-ourprogress-green";
                            break;
                        case 6:
                            $new_slug = "oli-ourprogress-red";
                            break;                           
                    }
                    
                    // Now insert that in to the database:
                    $wpdb->update($table_name, 
                        array(  'olimometer_skin_slug' => $new_slug 
                        ), 
                        array( 'olimometer_id' => $search_olimometer_id )
                    );
                }
      
                
            }
        }
        
    }
    else
    {
    
        // No it hasn't, so we need to import the old variables and save as the first olimometer
        
        // Create the database
        olimometer_install();
        
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


// Saves the olimometer_last value for this user
function update_olimometer_last($last_olimometer_id) {
    // What is this user's user_id?
    require_once (ABSPATH . WPINC . '/pluggable.php');
    global $current_user;
    get_currentuserinfo();
    
    update_option('olimometer_last_' . $current_user->user_login, $last_olimometer_id);
}

// Returns the olimometer_last value for this user
function get_olimometer_last() {
    // What is this user's user_id?
    require_once (ABSPATH . WPINC . '/pluggable.php');
    global $current_user;
    get_currentuserinfo();
    
    $olimometer_last = get_option('olimometer_last_' . $current_user->user_login);
    return $olimometer_last;
}



	


?>