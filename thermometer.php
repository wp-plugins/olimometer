<?php

// Turn off all error reporting
error_reporting(0);
 //log_errors(1);

//header('Content-type: image/jpeg');

// Load Wordpress!
// Where is the wordpress wp-load.php file??
// It should be 3 levels below this file
require_once("../../../wp-load.php");

// Load the Olimometer_skins class
require_once("skins.php");

// Load the Olimometer class
require_once("olimometer-class.php");

// What is the id of the olimometer to draw?
$olimometer_id = $_GET['olimometer_id'];


// Load this Olimometer's details
$olimometer_to_display = new Olimometer();
$olimometer_to_display->load($olimometer_id);

// Load all skins
$olimometer_skins = new Olimometer_Skins();
$olimometer_skins->olimometer_skins_location = get_option("olimometer_skins_location");
$olimometer_skins->olimometer_skins_custom_location = get_option("olimometer_skins_custom_location");
$olimometer_skins->load();

// Get the info for the required skin
$olimometer_skin_data = array();
$olimometer_skin_data = $olimometer_skins->get_skin($olimometer_to_display->olimometer_skin_slug);
    
    // Required Skin variables
    //$therm_skin_folder = "skins/".$olimometer_skin_data["skin_slug"]."/";
    $therm_skin_folder = $olimometer_skin_data["skin_location"] . "skins/".$olimometer_skin_data["skin_slug"]."/";
    $therm_bulb_file = $therm_skin_folder.$olimometer_skin_data["bulb_file"];
    $therm_bar_file = $therm_skin_folder.$olimometer_skin_data["bar_file"];
    $therm_top_file = $therm_skin_folder.$olimometer_skin_data["top_file"];
    $therm_bar_merc_colour = $olimometer_skin_data["bar_colour"];
    $therm_bar_merc_xpos = $olimometer_skin_data["bar_pos"];
    $therm_bar_merc_width = $olimometer_skin_data["bar_width"];
    $therm_bar_merc_top = $olimometer_skin_data["bar_end"];
    $therm_text_xpos = $olimometer_skin_data["text_pos"];
	$olimometer_dir = dirname(__FILE__);
    $font_name = $olimometer_dir."/LiberationSans-Regular.ttf";
    $olimometer_skin_xml_file = "skins.xml";
    
    
    // Required Olimometer variables
    $image_height = $olimometer_to_display->olimometer_thermometer_height;
    $thermometer_bg_colour = $olimometer_to_display->olimometer_thermometer_bg_colour;
    $total_value = $olimometer_to_display->olimometer_total_value;
    $progress_value = $olimometer_to_display->olimometer_progress_value;
    $text_colour = $olimometer_to_display->olimometer_text_colour;
    $transparent = $olimometer_to_display->olimometer_transparent;
    $show_target = $olimometer_to_display->olimometer_show_target;
    $show_progress = $olimometer_to_display->olimometer_show_progress;
    $progress_label = $olimometer_to_display->olimometer_progress_label;
    $font_height = $olimometer_to_display->olimometer_font_height;

    // Overlay Variables
    $overlay = $olimometer_to_display->olimometer_overlay;
    $overlay_image = $olimometer_to_display->olimometer_overlay_image;
    $overlay_x = $olimometer_to_display->olimometer_overlay_x;
    $overlay_y = $olimometer_to_display->olimometer_overlay_y;
    

    $display_total_value = $olimometer_to_display->get_display_total();
    $display_progress_value = $olimometer_to_display->get_display_progress();
    $display_zero = $olimometer_to_display->get_display_zero();
 

    // Are we making a horizontal or a vertical thermometer?
    if($olimometer_skin_data["orientation"] == "horizontal")
    {
    
        //read bulb image file and place at the bottom of new file
        //$therm_bulb = imagecreatefromjpeg($therm_bulb_file);
        $therm_bulb = imagecreatefrompng($therm_bulb_file);
        list($therm_bulb_width, $therm_bulb_height) = getimagesize($therm_bulb_file);
        
        //create temporary image
        $temp_new_image = imagecreatetruecolor($image_height,50);
        
        //fill the background of the image with the specified colour
        $fill_color_array = rgb2array($thermometer_bg_colour);
        $background_colour = imagecolorallocate($temp_new_image, $fill_color_array[0], $fill_color_array[1], $fill_color_array[2]);
        

        
        
        //read bar thermometer image
        //$therm_bar = imagecreatefromjpeg($therm_bar_file);
        $therm_bar = imagecreatefrompng($therm_bar_file);
        list($therm_bar_width, $therm_bar_height) = getimagesize($therm_bar_file);

        //put the top on the thermometer
        $therm_top = imagecreatefrompng($therm_top_file);
        list($therm_top_width, $therm_top_height) = getimagesize($therm_top_file);
        
        //work out length of mercury bar
        $total_bar_length = $image_height - $therm_bulb_width - $therm_bar_merc_top;
        
        //work out how many pixels of that bar need to be coloured in
        if ($progress_value >= $total_value) {
            $filled_bar_length = ceil($total_bar_length);
        }
        else {
            $filled_bar_length = ceil(($total_bar_length / $total_value) * $progress_value);
        }
        
        //work out the xpos of the end of the mercury bar
        $top_of_bar = $image_height - ($filled_bar_length + $therm_bulb_width);
        
        //draw mercury line
        $mercury_colour_array = rgb2array($therm_bar_merc_colour);
        $mercury_colour_rgb = imagecolorallocate($temp_new_image, $mercury_colour_array[0], $mercury_colour_array[1], $mercury_colour_array[2]);
        
        
        //write labels
        $text_color_array = rgb2array($text_colour);
        $text_colour_rgb = imagecolorallocate($temp_new_image, $text_color_array[0], $text_color_array[1], $text_color_array[2]);

        
        // What is the width of the top label?
        $new_image_width = $image_height; // default width!
        
        // Are we showing the target labels?
        if($show_target == 1) {
            $the_box = calculateTextBox($olimometer_to_display->get_display_total(), $font_name, $font_height, 0);
            $top_text_width = $the_box["width"]; 
        }
        
        // Are we showing the progress label?
        if($show_progress == 1) {
            // Calculate the width of it
            $the_box2 = calculateTextBox($progress_label.' '.$olimometer_to_display->get_display_progress(), $font_name, $font_height, 0);
            $progress_text_width = $the_box2["width"] + 2;
            // Is this wider than our previously calculated value?
                if($progress_text_width > $image_height) {
                        // Yes, it's bigger, so we need to use this value
                        $new_image_width = $progress_text_width;
                }
        }
        
        
        // Assemble the image:
        // - Create a new image:
        if($show_progress == 1) {
            $thermometer_height = $therm_bulb_height + ($font_height*2);
        }
        else {
            $thermometer_height = $therm_bulb_height;
        }
        if($show_target == 1) {
            $thermometer_height = $thermometer_height + ($font_height*2);
        }
        
        $top_of_therm = 0;
        if($show_target == 1)
        {
            $top_of_therm = $font_height*2;
        }
        $new_image = imagecreatetruecolor($new_image_width, $thermometer_height);
        // - Background Colour
        imagefill($new_image, 0, 0,  $background_colour);
        // - Add the bulb
        imagecopyresampled($new_image, $therm_bulb, 0, $top_of_therm, 0, 0, $therm_bulb_width, $therm_bulb_height, $therm_bulb_width, $therm_bulb_height);
        // - Fill the new image with empty thermometer to the full height
        for ($therm_bar_xpos = $therm_bulb_width; $therm_bar_xpos <= $therm_bulb_width+$total_bar_length; $therm_bar_xpos = $therm_bar_xpos + $therm_bar_width) {
            imagecopyresampled($new_image, $therm_bar, $therm_bar_xpos, $top_of_therm, 0, 0, $therm_bar_width, $therm_bar_height, $therm_bar_width, $therm_bar_height);
        }
        //- Top of the thermometer:
        imagecopyresampled($new_image, $therm_top, $image_height-$therm_top_width, $top_of_therm, 0, 0, $therm_top_width, $therm_top_height, $therm_top_width, $therm_top_height);
        // - Draw mercury line
        imagefilledrectangle($new_image, $therm_bulb_width, $therm_bar_merc_xpos+$top_of_therm, $filled_bar_length+$therm_bulb_width,$therm_bar_merc_xpos+$therm_bar_merc_width+$top_of_therm, $mercury_colour_rgb);
        // - Add value labels to the right of the thermometer
        if($show_target == 1) {
            imagettftext($new_image, $font_height, 0, 0, $font_height+2, $text_colour_rgb, $font_name, $olimometer_to_display->get_display_zero());
            imagettftext($new_image, $font_height, 0, $image_height-$top_text_width, $font_height+2, $text_colour_rgb, $font_name, $olimometer_to_display->get_display_total());
        }
        // - If the Progress label is needed, show it!
        if($show_progress == 1) {
            // It would be nice to center the text... How wide is it?
            if($new_image_width == $progress_text_width)
            {
                $progress_label_x = 0;
            }
            else
            {
                $progress_label_x = ($new_image_width-$progress_text_width)/2;
            }
            imagettftext($new_image, $font_height, 0, $progress_label_x, $therm_bulb_height+$top_of_therm+$font_height+5, $text_colour_rgb, $font_name, $progress_label.' '.$olimometer_to_display->get_display_progress());
        }
        // - Set transparancy if required using supplied background colour as mask
        if ($transparent == 1) {
            imagecolortransparent($new_image, $background_colour);
        }
    }    
    else // vertical one....
    {
        //if the progress value and label are being shown, leave space at the bottom of the thermometer for the label.
        if($show_progress == 1) {
            $thermometer_height = $image_height - ($font_height*2);
        }
        else {
            $thermometer_height = $image_height;
        }
        

        

        //create temporary image
        $temp_new_image = imagecreatetruecolor(50, $image_height);
        
        //fill the background of the image with the specified colour
        $fill_color_array = rgb2array($thermometer_bg_colour);
        $background_colour = imagecolorallocate($temp_new_image, $fill_color_array[0], $fill_color_array[1], $fill_color_array[2]);
        
        //read bulb image file and place at the bottom of new file
        $therm_bulb = imagecreatefrompng($therm_bulb_file);
        list($therm_bulb_width, $therm_bulb_height) = getimagesize($therm_bulb_file);
        $bulb_ypos = $thermometer_height - $therm_bulb_height;
        
        
        //read bar thermometer image
        $therm_bar = imagecreatefrompng($therm_bar_file);
        list($therm_bar_width, $therm_bar_height) = getimagesize($therm_bar_file);
        
        
        //put the top on the thermometer
        $therm_top = imagecreatefrompng($therm_top_file);
        list($therm_top_width, $therm_top_height) = getimagesize($therm_top_file);
        
        
        //work out length of mercury bar
        $total_bar_length = $thermometer_height - $therm_bulb_height - $therm_bar_merc_top;
        
        //work out how many pixels of that bar need to be coloured in
        if ($progress_value > $total_value) {
            $filled_bar_length = ceil($total_bar_length);
        }
        else {
            $filled_bar_length = ceil(($total_bar_length / $total_value) * $progress_value);
        }
        
        //work out the ypos of the top of the mercury bar
        $top_of_bar = $thermometer_height - ($filled_bar_length + $therm_bulb_height);
        
        //draw mercury line
        $mercury_colour_array = rgb2array($therm_bar_merc_colour);
        $mercury_colour_rgb = imagecolorallocate($temp_new_image, $mercury_colour_array[0], $mercury_colour_array[1], $mercury_colour_array[2]);
        
        
        //write labels
        $text_color_array = rgb2array($text_colour);
        $text_colour_rgb = imagecolorallocate($temp_new_image, $text_color_array[0], $text_color_array[1], $text_color_array[2]);
        
        
        // What is the width of the top label?
        $new_image_width = 0; // default width!
        
        // Are we showing the target labels?
        if($show_target == 1) {
            $the_box = calculateTextBox($olimometer_to_display->get_display_total(), $font_name, $font_height, 0);
            $top_text_width = $the_box["width"]; 
            
            // The width of the image file = label width + x_pos of label.
            $new_image_width = $top_text_width + $therm_text_xpos + 2;
        }
        
        // Are we showing the progress label?
        if($show_progress == 1) {
            // Calculate the width of it
        $the_box2 = calculateTextBox($progress_label.' '.$olimometer_to_display->get_display_progress(), $font_name, $font_height, 0);
        $progress_text_width = $the_box2["width"] + 2;
        // Is this wider than our previously calculated value?
        if($progress_text_width > $new_image_width) {
                // Yes, it's bigger, so we need to use this value
                $new_image_width = $progress_text_width;
        }
        }
        
        // What is the width of the thermometer template image?
        // Is it wider than the current estimate for width?
        if ($therm_bulb_width > $new_image_width) {
            // Yup... use this instead
            $new_image_width = $therm_bulb_width;
        }
        

        // Are we using an overlay image?
        if($overlay == 1) {
            // Load the overlay PNG
            $overlay_image_object = imagecreatefrompng($overlay_image);

            // Get the overlay image dimensions
            list($overlay_image_object_width, $overlay_image_object_height) = getimagesize($overlay_image);

            // Do we need to adjust the width of the Olimometer to fit this in?
            if($new_image_width < ($overlay_image_object_width+$overlay_x)) {
                // Yes
                $new_image_width = $overlay_image_object_width+$overlay_x;
            }

            // Do we need to adjust the height of the Olimometer to fit this in?
            if($image_height < ($overlay_image_object_height+$overlay_y)) {
                // Yes
                $image_height = $overlay_image_object_height+$overlay_y;
            }

        }
        
        
        
        // Assemble the image:
        // - Create a new image:
        $new_image = imagecreatetruecolor($new_image_width, $image_height);
        // - Background Colour
        imagefill($new_image, 0, 0,  $background_colour);
        // - Add the bulb
        imagecopyresampled($new_image, $therm_bulb, 0, $bulb_ypos, 0, 0, $therm_bulb_width, $therm_bulb_height, $therm_bulb_width, $therm_bulb_height);
        // - Fill the new image with empty thermometer to the full height
        for ($therm_bar_ypos = $bulb_ypos - $therm_bar_height; $therm_bar_ypos >= $therm_bar_merc_top; $therm_bar_ypos = $therm_bar_ypos - $therm_bar_height) {
        imagecopyresampled($new_image, $therm_bar, 0, $therm_bar_ypos, 0, 0, $therm_bar_width, $therm_bar_height, $therm_bar_width, $therm_bar_height);
        }
        //- Top of the thermometer:
        imagecopyresampled($new_image, $therm_top, 0, 0, 0, 0, $therm_top_width, $therm_top_height, $therm_top_width, $therm_top_height);
        // - Draw mercury line
        imagefilledrectangle($new_image, $therm_bar_merc_xpos, $bulb_ypos, $therm_bar_merc_xpos+$therm_bar_merc_width, $top_of_bar, $mercury_colour_rgb);
        // - Add value labels to the right of the thermometer
        if($show_target == 1) {
            imagettftext($new_image, $font_height, 0, $therm_text_xpos, $font_height*1.2, $text_colour_rgb, $font_name, $olimometer_to_display->get_display_total());
            imagettftext($new_image, $font_height, 0, $therm_text_xpos, $bulb_ypos+10, $text_colour_rgb, $font_name, $olimometer_to_display->get_display_zero());
        }
        // - If the Progress label is needed, show it!
        if($show_progress == 1) {
        imagettftext($new_image, $font_height, 0, 0, $image_height-(ceil($font_height/2)), $text_colour_rgb, $font_name, $progress_label.' '.$olimometer_to_display->get_display_progress());
        }

        // Overlay the overlay if needed!
        if($overlay == 1) {
            imagecopyresampled($new_image, $overlay_image_object, $overlay_x, $overlay_y, 0, 0, $overlay_image_object_width, $overlay_image_object_height, $overlay_image_object_width, $overlay_image_object_height);
        }
        

        // - Set transparancy if required using supplied background colour as mask
        if ($transparent == 1) {
            imagecolortransparent($new_image, $background_colour);
        }
    }    
    
    //output the image to the browser
    header('Content-type: image/png');
    imagepng($new_image, NULL);
    ob_end_flush();




//function to convert hex colour string to rgb array
function rgb2array($rgb) {
    return array(
        base_convert(substr($rgb, 0, 2), 16, 10),
        base_convert(substr($rgb, 2, 2), 16, 10),
        base_convert(substr($rgb, 4, 2), 16, 10),
    );
}

function calculateTextBox($text,$fontFile,$fontSize,$fontAngle) { 
    /************ 
    simple function that calculates the *exact* bounding box (single pixel precision).
     The function returns an associative array with these keys: 
    left, top:  coordinates you will pass to imagettftext 
    width, height: dimension of the image you have to create 
    *************/ 
    $rect = imagettfbbox($fontSize,$fontAngle,$fontFile,$text); 
    $minX = min(array($rect[0],$rect[2],$rect[4],$rect[6])); 
    $maxX = max(array($rect[0],$rect[2],$rect[4],$rect[6])); 
    $minY = min(array($rect[1],$rect[3],$rect[5],$rect[7])); 
    $maxY = max(array($rect[1],$rect[3],$rect[5],$rect[7])); 
    
    return array( 
     "left"   => abs($minX) - 1, 
     "top"    => abs($minY) - 1, 
     "width"  => $maxX - $minX, 
     "height" => $maxY - $minY, 
     "box"    => $rect 
    ); 
}  

?> 
