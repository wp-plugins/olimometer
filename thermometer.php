<?php

//user defined variables (will be passed in eventually)
$image_height = $HTTP_GET_VARS['height'];
$thermometer_bg_colour = $HTTP_GET_VARS['bg'];
$total_value = $HTTP_GET_VARS['total'];
$progress_value = $HTTP_GET_VARS['progress'];
$currency = $HTTP_GET_VARS['currency'];
$text_colour = $HTTP_GET_VARS['text_colour'];
$transparent = $HTTP_GET_VARS['transparent'];
$show_progress = $HTTP_GET_VARS['show_progress'];
$progress_label = $HTTP_GET_VARS['progress_label'];
$font_height = $HTTP_GET_VARS['font_height'];
$thermometer_width = $HTTP_GET_VARS['width'];
$suffix = $HTTP_GET_VARS['suffix'];

//hard coded variables
$therm_bulb_file = 'therm_bulb.jpg';
$therm_bar_file = 'therm_bar_empty.jpg';
$therm_top_file = 'therm_top.jpg';
$font_name = 'LiberationSans-Regular.ttf';

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
$therm_bulb = imagecreatefromjpeg($therm_bulb_file);
list($therm_bulb_width, $therm_bulb_height) = getimagesize($therm_bulb_file);
$bulb_ypos = $thermometer_height - $therm_bulb_height;
imagecopyresampled($new_image, $therm_bulb, 0, $bulb_ypos, 0, 0, $therm_bulb_width, $therm_bulb_height, $therm_bulb_width, $therm_bulb_height);

//read bar thermometer image
$therm_bar = imagecreatefromjpeg($therm_bar_file);
list($therm_bar_width, $therm_bar_height) = getimagesize($therm_bar_file);

//fill the new image with empty thermometer to the full height
for ($therm_bar_ypos = $bulb_ypos - $therm_bar_height; $therm_bar_ypos >= -4; $therm_bar_ypos = $therm_bar_ypos - $therm_bar_height) {
imagecopyresampled($new_image, $therm_bar, 0, $therm_bar_ypos, 0, 0, $therm_bar_width, $therm_bar_height, $therm_bar_width, $therm_bar_height);
}

//put the top on the thermometer
$therm_top = imagecreatefromjpeg($therm_top_file);
list($therm_top_width, $therm_top_height) = getimagesize($therm_top_file);
imagecopyresampled($new_image, $therm_top, 0, 0, 0, 0, $therm_top_width, $therm_top_height, $therm_top_width, $therm_top_height);

//work out length of mercury bar
$total_bar_length = $thermometer_height - $therm_bulb_height - 7;

//work out how many pixels of that bar need to be coloured in
$filled_bar_length = ceil(($total_bar_length / $total_value) * $progress_value);

//work out the ypos of the top of the mercury bar
$top_of_bar = $thermometer_height - ($filled_bar_length + $therm_bulb_height);

//draw mercury line
$mercury_colour_rgb = imagecolorat($therm_bulb, 27, 3);
imagefilledrectangle($new_image, 26, $bulb_ypos, 30, $top_of_bar, $mercury_colour_rgb);

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



imagettftext($new_image, $font_height, 0, 60, $font_height*1.2, $text_colour_rgb, $font_name, $currency_symbol.$total_value.$suffix_symbol);
imagettftext($new_image, $font_height, 0, 60, $bulb_ypos+10, $text_colour_rgb, $font_name, $currency_symbol.'0'.$suffix_symbol);

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
