<?php

// Turn off all error reporting
error_reporting(0);


//user defined variables
$image_height = $_GET['height'];
$thermometer_bg_colour = $_GET['bg'];
$total_value = $_GET['total'];
$progress_value = $_GET['progress'];
$currency = $_GET['currency'];
$text_colour = $_GET['text_colour'];
$transparent = $_GET['transparent'];
$show_progress = $_GET['show_progress'];
$progress_label = $_GET['progress_label'];
$font_height = $_GET['font_height'];
$thermometer_width = $_GET['width'];
$suffix = $_GET['suffix'];
$therm_skin = $_GET['skin'];

//hard coded variables
//$therm_bulb_file = 'therm_bulb.jpg';
//$therm_bar_file = 'therm_bar_empty.jpg';
//$therm_top_file = 'therm_top.jpg';
//$therm_skin = 0;
$font_name = 'LiberationSans-Regular.ttf';
$olimometer_skin_xml_file = "skins.xml";


//Import skin from xml file
include('skins.php');
$therm_skin_data = array();
$therm_skin_data = olimometer_get_skin($therm_skin);
$therm_skin_folder = "skins/".$therm_skin_data["skin_folder"]."/";
$therm_bulb_file = $therm_skin_folder.$therm_skin_data["bulb_file"];
$therm_bar_file = $therm_skin_folder.$therm_skin_data["bar_file"];
$therm_top_file = $therm_skin_folder.$therm_skin_data["top_file"];
$therm_bar_merc_colour = $therm_skin_data["bar_colour"];
$therm_bar_merc_xpos = $therm_skin_data["bar_xpos"];
$therm_bar_merc_width = $therm_skin_data["bar_width"];
$therm_bar_merc_top = $therm_skin_data["bar_top"];
$therm_text_xpos = $therm_skin_data["text_xpos"];

//if the progress value and label are being shown, leave space at the bottom of the thermometer for the label.
if($show_progress == 1) {
   $thermometer_height = $image_height - ($font_height*2);
}
else {
   $thermometer_height = $image_height;
}

//create new image
header('Content-type: image/jpeg');
$new_image = imagecreatetruecolor($thermometer_width, $image_height);

//fill the background of the image with the specified colour
$fill_color_array = rgb2array($thermometer_bg_colour);
$background_colour = imagecolorallocate($new_image, $fill_color_array[0], $fill_color_array[1], $fill_color_array[2]);
imagefill($new_image, 0, 0,  $background_colour);


//read bulb image file and place at the bottom of new file
//$therm_bulb = imagecreatefromjpeg($therm_bulb_file);
$therm_bulb = imagecreatefrompng($therm_bulb_file);
list($therm_bulb_width, $therm_bulb_height) = getimagesize($therm_bulb_file);
$bulb_ypos = $thermometer_height - $therm_bulb_height;
imagecopyresampled($new_image, $therm_bulb, 0, $bulb_ypos, 0, 0, $therm_bulb_width, $therm_bulb_height, $therm_bulb_width, $therm_bulb_height);

//read bar thermometer image
//$therm_bar = imagecreatefromjpeg($therm_bar_file);
$therm_bar = imagecreatefrompng($therm_bar_file);
list($therm_bar_width, $therm_bar_height) = getimagesize($therm_bar_file);

//fill the new image with empty thermometer to the full height
for ($therm_bar_ypos = $bulb_ypos - $therm_bar_height; $therm_bar_ypos >= $therm_bar_merc_top; $therm_bar_ypos = $therm_bar_ypos - $therm_bar_height) {
imagecopyresampled($new_image, $therm_bar, 0, $therm_bar_ypos, 0, 0, $therm_bar_width, $therm_bar_height, $therm_bar_width, $therm_bar_height);
}


//fill in the gap
//imagecopyresampled($new_image, $therm_bar, 0, $therm_bar_merc_top, 0, 0, $therm_bar_width, $therm_bar_height, $therm_bar_width, $therm_bar_height);


//put the top on the thermometer
//$therm_top = imagecreatefromjpeg($therm_top_file);
$therm_top = imagecreatefrompng($therm_top_file);
list($therm_top_width, $therm_top_height) = getimagesize($therm_top_file);
imagecopyresampled($new_image, $therm_top, 0, 0, 0, 0, $therm_top_width, $therm_top_height, $therm_top_width, $therm_top_height);




//work out length of mercury bar
$total_bar_length = $thermometer_height - $therm_bulb_height - $therm_bar_merc_top;

//work out how many pixels of that bar need to be coloured in
$filled_bar_length = ceil(($total_bar_length / $total_value) * $progress_value);

//work out the ypos of the top of the mercury bar
$top_of_bar = $thermometer_height - ($filled_bar_length + $therm_bulb_height);

//draw mercury line
//$mercury_colour_rgb = imagecolorat($therm_bulb, 27, 3); // Old method of getting colour.. use new skin specified value insead
$mercury_colour_array = rgb2array($therm_bar_merc_colour);
$mercury_colour_rgb = imagecolorallocate($new_image, $mercury_colour_array[0], $mercury_colour_array[1], $mercury_colour_array[2]);
imagefilledrectangle($new_image, $therm_bar_merc_xpos, $bulb_ypos, $therm_bar_merc_xpos+$therm_bar_merc_width, $top_of_bar, $mercury_colour_rgb);

//write labels
$text_color_array = rgb2array($text_colour);
$text_colour_rgb = imagecolorallocate($new_image, $text_color_array[0], $text_color_array[1], $text_color_array[2]);
if ($currency == 128)
	$currency_symbol = "&#8364;";
else if ($currency == 'x')
        $currency_symbol = "";
else
	$currency_symbol = chr($currency);

if ($suffix != 'x') {
        $suffix_symbol = chr($suffix);
}



imagettftext($new_image, $font_height, 0, $therm_text_xpos, $font_height*1.2, $text_colour_rgb, $font_name, $currency_symbol.$total_value.$suffix_symbol);
imagettftext($new_image, $font_height, 0, $therm_text_xpos, $bulb_ypos+10, $text_colour_rgb, $font_name, $currency_symbol.'0'.$suffix_symbol);

//No longer showing progress value next to mercury bar as of v1.2
//imagettftext($new_image, $font_height, 0, 60, $top_of_bar, $text_colour_rgb, $font_name, $currency_symbol.$progress_value);


//If the Progress label is needed, show it!
if($show_progress == 1) {
   imagettftext($new_image, $font_height, 0, 0, $image_height-(ceil($font_height/2)), $text_colour_rgb, $font_name, $progress_label.' '.$currency_symbol.$progress_value.$suffix_symbol);
}

//Set transparancy if required using supplied background colour as mask
if ($transparent == 1) {
	imagecolortransparent($new_image, $background_colour);
}


//output the image to the browser
imagepng($new_image, NULL);

//function to convert hex colour string to rgb array
function rgb2array($rgb) {
    return array(
        base_convert(substr($rgb, 0, 2), 16, 10),
        base_convert(substr($rgb, 2, 2), 16, 10),
        base_convert(substr($rgb, 4, 2), 16, 10),
    );
}

?> 
