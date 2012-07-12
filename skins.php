<?php
// Olimometer Skin Loading functions

class Olimometer_Skins
{
    function Olimometer_Skins()
    {
        // Constructor
        $this->olimometer_skincount=0;
	    $this->olimometer_skindata=array();
	    $this->olimometer_skinstate='';
    }


    // Properties
    public $olimometer_skincount;
    public $olimometer_skindata;
	public $olimometer_skinstate;
    public $olimometer_skins_location; // THIS MUST BE SET BEFORE LOADING!
    public $olimometer_skins_custom_location; // THIS MUST BE SET BEFORE LOADING!
        
    function load()
    {
        // Load built-in skins
        //$skin_xml_file = $this->olimometer_skins_location . "skins.xml";
        $this->load_from_file($this->olimometer_skins_location);
        
        // Load custom skins.
        $skin_xml_file = $this->olimometer_skins_custom_location . "skins.xml";
        // Does the custom skin file exist?
        if(file_exists($skin_xml_file))
        {
            $this->load_from_file($this->olimometer_skins_custom_location);
        }
        
    }
    
    function load_from_file($skinfilelocation)
    {
        // Load the skins from the provided xml file
        $start_skin = $this->olimometer_skincount;
        
        $skin_xml_file = $skinfilelocation . "skins.xml";
        if (!($olimometer_skinfp=@fopen($skin_xml_file, "r"))) {
                die ("Couldn't open XML.");
        }
    
        
        if (!($olimometer_skinxml_parser = xml_parser_create())) {
                die("Couldn't create parser.");
        }
        
        xml_set_object($olimometer_skinxml_parser, $this);
    
        xml_set_element_handler($olimometer_skinxml_parser,"olimometer_skin_startElementHandler","olimometer_skin_endElementHandler");
        xml_set_character_data_handler( $olimometer_skinxml_parser, "olimometer_skin_characterDataHandler");
    
        while( $olimometer_skinxmldata = fread($olimometer_skinfp, 4096)){
            if(!xml_parse($olimometer_skinxml_parser, $olimometer_skinxmldata, feof($olimometer_skinfp))) {
                break;
            }
        }
        xml_parser_free($olimometer_skinxml_parser);
        $finish_skin = $this->olimometer_skincount;
        
        // Loop around these new skins and add the correct location:
        for($i = $start_skin; $i < $finish_skin; $i++) {
            $this->olimometer_skindata[$i]["skin_location"] = $skinfilelocation;
        }
    
    }
    
    // Function to print a list of all skin details
/*    function olimometer_list_skins()
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
 */
 
    
    // Function to return an array of all skin names
    function get_skin_names()
    {
        
        $olimometer_skin_names = array();
        
        for ($i=0;$i<$this->olimometer_skincount; $i++) {
            $olimometer_skin_names[$i]["skin_name"] = $this->olimometer_skindata[$i]["skin_name"];
            $olimometer_skin_names[$i]["skin_slug"] = $this->olimometer_skindata[$i]["skin_slug"];
        }
        return $olimometer_skin_names;
    }
    
    
    // Function to return a single skin's data as an array of named items
    function get_skin($olimometer_skin_slug)
    {
        
        $olimometer_skin = array();
        if ( ($olimometer_skin_slug == NULL) || ($olimometer_skin_slug == '')) {
            // Set the slug to the default one because one hasn't been found.
            // stops it from breaking old installations when upgraded
            $olimometer_skin_slug = "oli-default";
        }
        
        // Loop around skins until slug matches:
        for ($i=0;$i<$this->olimometer_skincount; $i++) {
            //$olimometer_skin_names[$i] = $olimometer_skindata[$i]["skin_name"];
            if($this->olimometer_skindata[$i]["skin_slug"] == $olimometer_skin_slug) {
                // We have a match, so pull in the details:
                    $olimometer_skin["skin_name"]=$this->olimometer_skindata[$i]["skin_name"];
                    $olimometer_skin["skin_slug"]=$this->olimometer_skindata[$i]["skin_slug"];
                    $olimometer_skin["bulb_file"]=$this->olimometer_skindata[$i]["bulb_file"];
                    $olimometer_skin["bar_file"]=$this->olimometer_skindata[$i]["bar_file"];
                    $olimometer_skin["top_file"]=$this->olimometer_skindata[$i]["top_file"];
                    $olimometer_skin["bar_colour"]=$this->olimometer_skindata[$i]["bar_colour"];
                    $olimometer_skin["bar_pos"]=$this->olimometer_skindata[$i]["bar_pos"];
                    $olimometer_skin["bar_width"]=$this->olimometer_skindata[$i]["bar_width"];	
                    $olimometer_skin["bar_end"]=$this->olimometer_skindata[$i]["bar_end"];
                    $olimometer_skin["text_pos"]=$this->olimometer_skindata[$i]["text_pos"];	
                    $olimometer_skin["orientation"]=$this->olimometer_skindata[$i]["orientation"];
                    $olimometer_skin["skin_location"]=$this->olimometer_skindata[$i]["skin_location"];
            }
        }
        
        
        return $olimometer_skin;
    }
    
    
    
    
    // These three functions are for handling the XML Parser
    function olimometer_skin_startElementHandler ($parser,$name,$attrib) {
        $this->olimometer_skinstate = $name;
    }
    
    function olimometer_skin_endElementHandler ($parser,$name) {
        $this->olimometer_skinstate='';
    
        if($name=="SKIN") {
            $this->olimometer_skincount++;
        }
    }
    
    function olimometer_skin_characterDataHandler ($parser, $data) {
        if (!$this->olimometer_skinstate) {return;}
        if ($this->olimometer_skinstate=="SKIN_NAME") { $this->olimometer_skindata[$this->olimometer_skincount]["skin_name"] = $data;}
        if ($this->olimometer_skinstate=="SKIN_SLUG") { $this->olimometer_skindata[$this->olimometer_skincount]["skin_slug"] = $data;}
        if ($this->olimometer_skinstate=="BULB_FILE") { $this->olimometer_skindata[$this->olimometer_skincount]["bulb_file"] = $data;}
        if ($this->olimometer_skinstate=="BAR_FILE") { $this->olimometer_skindata[$this->olimometer_skincount]["bar_file"] = $data;}
        if ($this->olimometer_skinstate=="TOP_FILE") { $this->olimometer_skindata[$this->olimometer_skincount]["top_file"] = $data;}
        if ($this->olimometer_skinstate=="BAR_COLOUR") { $this->olimometer_skindata[$this->olimometer_skincount]["bar_colour"] = $data;}
        if ($this->olimometer_skinstate=="BAR_POS") { $this->olimometer_skindata[$this->olimometer_skincount]["bar_pos"] = $data;}
        if ($this->olimometer_skinstate=="BAR_WIDTH") { $this->olimometer_skindata[$this->olimometer_skincount]["bar_width"] = $data;}
        if ($this->olimometer_skinstate=="BAR_END") { $this->olimometer_skindata[$this->olimometer_skincount]["bar_end"] = $data;}
        if ($this->olimometer_skinstate=="TEXT_POS") { $this->olimometer_skindata[$this->olimometer_skincount]["text_pos"] = $data;}	
        if ($this->olimometer_skinstate=="ORIENTATION") { $this->olimometer_skindata[$this->olimometer_skincount]["orientation"] = $data;}
    }
    

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