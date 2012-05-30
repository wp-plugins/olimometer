<?php
// Olimometer Skin Loading functions

// If not defined here, this variable must point to skins.xml and be defined in the parent PHP file.
//$olimometer_skin_xml_file = "skins.xml";
	global $olimometer_skincount;
	global $olimometer_skindata;
	global $olimometer_skinstate;
	
// Loads the XML from the skins.xml file:

	if (!($olimometer_skinfp=@fopen($olimometer_skin_xml_file, "r"))) {
	        die ("Couldn't open XML.");
	}
	$olimometer_skincount=0;
	$olimometer_skindata=array();
	$olimometer_skinstate='';
	if (!($olimometer_skinxml_parser = xml_parser_create())) {
	        die("Couldn't create parser.");
	}

	xml_set_element_handler($olimometer_skinxml_parser,"olimometer_skin_startElementHandler","olimometer_skin_endElementHandler");
	xml_set_character_data_handler( $olimometer_skinxml_parser, "olimometer_skin_characterDataHandler");

	while( $olimometer_skinxmldata = fread($olimometer_skinfp, 4096)){
		if(!xml_parse($olimometer_skinxml_parser, $olimometer_skinxmldata, feof($olimometer_skinfp))) {
			break;
		}
	}
	xml_parser_free($olimometer_skinxml_parser);

// Function to print a list of all skin details
function olimometer_list_skins()
{
	global $olimometer_skincount;
	global $olimometer_skindata;
	global $olimometer_skinstate;
	
	echo "Listing skins... ".$olimometer_skincount." in total:<br><br>";
	for ($i=0;$i<$olimometer_skincount; $i++) {
		echo "skin number: ".$i."<br>";
		echo $olimometer_skindata[$i]["skin_name"]."<br>";
		echo $olimometer_skindata[$i]["skin_folder"]."<br>";
		echo $olimometer_skindata[$i]["bulb_file"]."<br>";
		echo $olimometer_skindata[$i]["bar_file"]."<br>";
		echo $olimometer_skindata[$i]["top_file"]."<br>";
		echo $olimometer_skindata[$i]["bar_colour"]."<br>";
		echo $olimometer_skindata[$i]["bar_xpos"]."<br>";
		echo $olimometer_skindata[$i]["bar_width"]."<br>";
		echo $olimometer_skindata[$i]["bar_top"]."<hr>";		
	}

}


// Function to return an array of all skin names
function olimometer_get_skin_names()
{
	global $olimometer_skincount;
	global $olimometer_skindata;
	global $olimometer_skinstate;
	
	$olimometer_skin_names = array();
	
	for ($i=0;$i<$olimometer_skincount; $i++) {
		$olimometer_skin_names[$i] = $olimometer_skindata[$i]["skin_name"];
	}
	return $olimometer_skin_names;
}

// Function to return a single skin's data as an array of named items
function olimometer_get_skin($olimometer_skin_number)
{
	global $olimometer_skincount;
	global $olimometer_skindata;
	global $olimometer_skinstate;
	
	$olimometer_skin = array();
	
	$olimometer_skin["skin_name"]=$olimometer_skindata[$olimometer_skin_number]["skin_name"];
	$olimometer_skin["skin_folder"]=$olimometer_skindata[$olimometer_skin_number]["skin_folder"];
	$olimometer_skin["bulb_file"]=$olimometer_skindata[$olimometer_skin_number]["bulb_file"];
	$olimometer_skin["bar_file"]=$olimometer_skindata[$olimometer_skin_number]["bar_file"];
	$olimometer_skin["top_file"]=$olimometer_skindata[$olimometer_skin_number]["top_file"];
	$olimometer_skin["bar_colour"]=$olimometer_skindata[$olimometer_skin_number]["bar_colour"];
	$olimometer_skin["bar_xpos"]=$olimometer_skindata[$olimometer_skin_number]["bar_xpos"];
	$olimometer_skin["bar_width"]=$olimometer_skindata[$olimometer_skin_number]["bar_width"];	
	$olimometer_skin["bar_top"]=$olimometer_skindata[$olimometer_skin_number]["bar_top"];
    $olimometer_skin["text_xpos"]=$olimometer_skindata[$olimometer_skin_number]["text_xpos"];	
	
	return $olimometer_skin;
}




// These three functions are for handling the XML Parser
function olimometer_skin_startElementHandler ($parser,$name,$attrib) {
	global $olimometer_skincount;
	global $olimometer_skindata;
	global $olimometer_skinstate;
	$olimometer_skinstate = $name;
}

function olimometer_skin_endElementHandler ($parser,$name) {
	global $olimometer_skincount;
	global $olimometer_skindata;
	global $olimometer_skinstate;
	$olimometer_skinstate='';

	if($name=="SKIN") {
		$olimometer_skincount++;
	}
}

function olimometer_skin_characterDataHandler ($parser, $data) {
	global $olimometer_skincount;
	global $olimometer_skindata;
	global $olimometer_skinstate;
	
	if (!$olimometer_skinstate) {return;}
	if ($olimometer_skinstate=="SKIN_NAME") { $olimometer_skindata[$olimometer_skincount]["skin_name"] = $data;}
	if ($olimometer_skinstate=="SKIN_FOLDER") { $olimometer_skindata[$olimometer_skincount]["skin_folder"] = $data;}
	if ($olimometer_skinstate=="BULB_FILE") { $olimometer_skindata[$olimometer_skincount]["bulb_file"] = $data;}
	if ($olimometer_skinstate=="BAR_FILE") { $olimometer_skindata[$olimometer_skincount]["bar_file"] = $data;}
	if ($olimometer_skinstate=="TOP_FILE") { $olimometer_skindata[$olimometer_skincount]["top_file"] = $data;}
	if ($olimometer_skinstate=="BAR_COLOUR") { $olimometer_skindata[$olimometer_skincount]["bar_colour"] = $data;}
	if ($olimometer_skinstate=="BAR_XPOS") { $olimometer_skindata[$olimometer_skincount]["bar_xpos"] = $data;}
	if ($olimometer_skinstate=="BAR_WIDTH") { $olimometer_skindata[$olimometer_skincount]["bar_width"] = $data;}
	if ($olimometer_skinstate=="BAR_TOP") { $olimometer_skindata[$olimometer_skincount]["bar_top"] = $data;}
	if ($olimometer_skinstate=="TEXT_XPOS") { $olimometer_skindata[$olimometer_skincount]["text_xpos"] = $data;}	
}




//olimometer_list_skins();

/*
//Test olimometer_get_skin_names
$olimometer_skin_names = array();
$olimometer_skin_names = olimometer_get_skin_names();

foreach ($olimometer_skin_names as $olimometer_skin_name) {
	echo $olimometer_skin_name . "<br>";
}
*/

/*
//Test olimometer_get_skin
$olimometer_skin = array();
$olimometer_skin = olimometer_get_skin(1);

echo $olimometer_skin["skin_name"]."<br>";
echo $olimometer_skin["skin_folder"]."<br>";
echo $olimometer_skin["bulb_file"]."<br>";
echo $olimometer_skin["bar_file"]."<br>";
echo $olimometer_skin["top_file"]."<br>";
echo $olimometer_skin["bar_colour"]."<br>";
echo $olimometer_skin["bar_xpos"]."<br>";
echo $olimometer_skin["bar_width"]."<hr>";
*/

?>