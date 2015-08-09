<?php
// Class to define an Olimometer object


class Olimometer
{
	function __construct() {}
    /*function Olimometer()
    {
        // Constructor
    }*/
    
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
    public $olimometer_skin_slug = "oli-default";
	public $olimometer_use_paypal = 0;
	public $olimometer_paypal_username;
	public $olimometer_paypal_password;
	public $olimometer_paypal_signature;
    public $olimometer_paypal_extra_value = 0.00;
    public $olimometer_number_format = 0;
    public $olimometer_link = "";
    public $olimometer_overlay = 0;
    public $olimometer_overlay_image = "";
    public $olimometer_overlay_x = 0;
    public $olimometer_overlay_y = 0;
    public $olimometer_stayclassypid = 0;
    public $olimometer_stayclassyeid = 0;
    public $olimometer_display_dp;
    public $olimometer_display_thousands;
    public $olimometer_display_decimal;
    public $olimometer_link_disable = 0;
    
    private $olimometer_default_link = "";
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
        $this->olimometer_skin_slug = $query_results['olimometer_skin_slug'];
        $this->olimometer_use_paypal = $query_results['olimometer_use_paypal'];
        $this->olimometer_paypal_username = $query_results['olimometer_paypal_username'];	
        $this->olimometer_paypal_password = $query_results['olimometer_paypal_password'];
        $this->olimometer_paypal_signature = $query_results['olimometer_paypal_signature'];
        $this->olimometer_paypal_extra_value = $query_results['olimometer_paypal_extra_value'];
        $this->olimometer_number_format = $query_results['olimometer_number_format'];
        $this->olimometer_overlay = $query_results['olimometer_overlay'];
        $this->olimometer_overlay_image = $query_results['olimometer_overlay_image'];
        $this->olimometer_overlay_x = $query_results['olimometer_overlay_x'];
        $this->olimometer_overlay_y = $query_results['olimometer_overlay_y'];
        $this->olimometer_stayclassypid = $query_results['olimometer_stayclassypid'];
        $this->olimometer_stayclassyeid = $query_results['olimometer_stayclassyeid'];
        $this->olimometer_link_disable = $query_results['olimometer_link_disable'];
        
        if($query_results['olimometer_link'] == "" || $query_results['olimometer_link'] == null) {
            $this->olimometer_link = $this->olimometer_default_link;
        }
        else {
            $this->olimometer_link = $query_results['olimometer_link'];
        }

        if($this->olimometer_number_format == null) {
            $this->olimometer_number_format = 0;
        }

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
        
        // Sanitize data
        if($this->olimometer_paypal_extra_value == '') {
            $olimometer_paypal_extra_value = 0.00;
        }
        else {
            $olimometer_paypal_extra_value = $this->olimometer_paypal_extra_value;
        }
        
        if($this->olimometer_progress_value == '') {
            $olimometer_progress_value = 0.00;
        }
        else {
            $olimometer_progress_value = $this->olimometer_progress_value;
        }

        // Set the default overlay co-ordinates to 0,0
        if($this->olimometer_overlay_x == "" || $this->olimometer_overlay_x == null) {
            $this->olimometer_overlay_x = 0;
        }

        if($this->olimometer_overlay_y == "" || $this->olimometer_overlay_y == null) {
            $this->olimometer_overlay_y = 0;
        }

        // If the overlay image url box is null or empty, disable the overlay
        if($this->olimometer_overlay_image == "" || $this->olimometer_overlay_image == null) {
            $this->olimometer_overlay = 0;
        }
            
        // Is this an existing olimometer or a new one to be saved?
        if($this->olimometer_id == -1)
        {
            // This is a new one
            $rows_affected = $wpdb->insert( $table_name, array( 'olimometer_description' => $this->olimometer_description,
                                                                'olimometer_progress_value' => $olimometer_progress_value,
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
                                                                'olimometer_skin_slug' => $this->olimometer_skin_slug,
                                                                'olimometer_use_paypal' => $this->olimometer_use_paypal,
                                                                'olimometer_paypal_username' => $this->olimometer_paypal_username,	
                                                                'olimometer_paypal_password' => $this->olimometer_paypal_password,
                                                                'olimometer_paypal_signature' => $this->olimometer_paypal_signature,
                                                                'olimometer_paypal_extra_value' => $olimometer_paypal_extra_value,
                                                                'olimometer_number_format' => $this->olimometer_number_format,
                                                                'olimometer_link' => $this->olimometer_link,
                                                                'olimometer_overlay' => $this->olimometer_overlay,
                                                                'olimometer_overlay_image' => $this->olimometer_overlay_image,
                                                                'olimometer_overlay_x' => $this->olimometer_overlay_x,
                                                                'olimometer_overlay_y' => $this->olimometer_overlay_y,
                                                                'olimometer_stayclassypid' => $this->olimometer_stayclassypid,
                                                                'olimometer_stayclassyeid' => $this->olimometer_stayclassyeid,
                                                                'olimometer_link_disable' => $this->olimometer_link_disable
                                                                 ) );
            
            // Find out the olimometer_id of the record just created and save it to the object.
            $this->olimometer_id = $wpdb->insert_id;
        }
        else
        {
            // This is an existing one
            $wpdb->update($table_name, 
                        array(  'olimometer_description' => $this->olimometer_description,
                                'olimometer_progress_value' => $olimometer_progress_value,
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
                                'olimometer_skin_slug' => $this->olimometer_skin_slug,
                                'olimometer_use_paypal' => $this->olimometer_use_paypal,
                                'olimometer_paypal_username' => $this->olimometer_paypal_username,	
                                'olimometer_paypal_password' => $this->olimometer_paypal_password,
                                'olimometer_paypal_signature' => $this->olimometer_paypal_signature,
                                'olimometer_paypal_extra_value' => $olimometer_paypal_extra_value,
                                'olimometer_number_format' => $this->olimometer_number_format,
                                'olimometer_link' => $this->olimometer_link,
                                'olimometer_overlay' => $this->olimometer_overlay,
                                'olimometer_overlay_image' => $this->olimometer_overlay_image,
                                'olimometer_overlay_x' => $this->olimometer_overlay_x,
                                'olimometer_overlay_y' => $this->olimometer_overlay_y,
                                'olimometer_stayclassypid' => $this->olimometer_stayclassypid,
                                'olimometer_stayclassyeid' => $this->olimometer_stayclassyeid,
                                'olimometer_link_disable' => $this->olimometer_link_disable
                        ), 
                        array( 'olimometer_id' => $this->olimometer_id )
                    );
        }
      
    }
    
    // Returns the olimometer display code
    function show($css_class = '')
    {
        // Make sure the progress value is updated in case it is pulled in from PayPal etc...
        $this->update_progress();
    
        // And format...
        $olimometer_font = "LiberationSans-Regular.ttf";
        $image_location = plugins_url('olimometer/thermometer.php', dirname(__FILE__) );   
        if($this->olimometer_link_disable == 0) {
            $the_olimometer_text = "<a href='".$this->olimometer_link."' target=_blank>";
        } 
        $the_olimometer_text = $the_olimometer_text."<img src='".$image_location."?olimometer_id=".$this->olimometer_id."' border=0";
        if(strlen($css_class) > 0) {
            $the_olimometer_text = $the_olimometer_text." class='".$css_class."'";
        }
        $the_olimometer_text = $the_olimometer_text." alt='Olimometer 2.52'>";
        if($this->olimometer_link_disable == 0) {
            $the_olimometer_text = $the_olimometer_text."</a>";
        } 
        
        
        return $the_olimometer_text;
    }


    // Makes sure that the progress value is updated in case it is frmo a dynamic source such as PayPal.
    // Should be called before a value is displayed
    function update_progress() {
        // If PayPal integration is configured, get the current balance and save it
        if($this->olimometer_use_paypal == 1) {
            $olimometer_paypal_balance = $this->get_paypal_balance();
            if($olimometer_paypal_balance == false) {
                // If PayPal link is broken, set balance to 0
                $olimometer_paypal_balance = 0;
            }
            else {
                    if($this->olimometer_progress_value == ($olimometer_paypal_balance + $this->olimometer_paypal_extra_value) ) {
                        // PayPal balance hasn't changed since we last checked so don't do anything
                    }
                    else {
                        // It has changed, so save it
                        $this->olimometer_progress_value = $olimometer_paypal_balance + $this->olimometer_paypal_extra_value;
                        $this->save();
                    }
            }
        }

        // If StayClassy PROJECT ID integration is configured, get the current balance and save it
        if($this->olimometer_use_paypal == 2) {
            $olimometer_stayclassy_balance = $this->getStayClassy($this->olimometer_stayclassypid);
            if($olimometer_stayclassy_balance == false) {
                // If PayPal link is broken, set balance to 0
                $olimometer_stayclassy_balance = 0;
            }
            else {
                    if($this->olimometer_progress_value == ($olimometer_stayclassy_balance) ) {
                        // PayPal balance hasn't changed since we last checked so don't do anything
                    }
                    else {
                        // It has changed, so save it
                        $this->olimometer_progress_value = $olimometer_stayclassy_balance;
                        $this->save();
                    }
            }
        }

        // If StayClassy EVENT ID integration is configured, get the current balance and save it
        if($this->olimometer_use_paypal == 3) {
            $olimometer_stayclassy_balance = $this->getStayClassyEvent($this->olimometer_stayclassyeid);
            if($olimometer_stayclassy_balance == false) {
                // If PayPal link is broken, set balance to 0
                $olimometer_stayclassy_balance = 0;
            }
            else {
                    if($this->olimometer_progress_value == ($olimometer_stayclassy_balance) ) {
                        // PayPal balance hasn't changed since we last checked so don't do anything
                    }
                    else {
                        // It has changed, so save it
                        $this->olimometer_progress_value = $olimometer_stayclassy_balance;
                        $this->save();
                    }
            }
        }
    }
    
    // Gets the total_raised value from StayClassy.org for a given PID.
    function getStayClassy($PID) {
        $json = file_get_contents("http://www.stayclassy.org/api/project-info?pid=$PID");

        $jsonIterator = new RecursiveIteratorIterator( 
        new RecursiveArrayIterator(json_decode($json, TRUE)), 
        RecursiveIteratorIterator::SELF_FIRST); 
    
        $return_value = 0;

            foreach ($jsonIterator as $key => $val) { 
                if(is_array($val)) { 
                    //$return_text = $return_text . "$key:\n"; 
                } else {
                    if($key == "total_raised") {
                        $return_value = $val;
                    } 
                    //$return_text = $return_text . "$key => $val\n"; 
                } 
            } 

        return $return_value;
    }
    
        // Gets the total_raised value from StayClassy.org for a given EID.
    function getStayClassyEvent($EID) {
        $json = file_get_contents("http://www.stayclassy.org/api/event-info?eid=$EID");

        $jsonIterator = new RecursiveIteratorIterator( 
        new RecursiveArrayIterator(json_decode($json, TRUE)), 
        RecursiveIteratorIterator::SELF_FIRST); 
    
        $return_value = 0;

            foreach ($jsonIterator as $key => $val) { 
                if(is_array($val)) { 
                    //$return_text = $return_text . "$key:\n"; 
                } else {
                    if($key == "total_raised") {
                        $return_value = $val;
                    } 
                    //$return_text = $return_text . "$key => $val\n"; 
                } 
            } 

        return $return_value;
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
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
    
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
            return urldecode($httpParsedResponseAr["L_AMT0"]);
        }
        else  {
            return false;
        }
    }


    // Returns a formatted progress value string
    function get_display_progress() {
        $this->set_display_format();
        $display_string = number_format($this->olimometer_progress_value,$this->olimometer_display_dp,$this->olimometer_display_decimal,$this->olimometer_display_thousands);
        return $this->wrap_prefix_and_suffix($display_string);
    }

    // Returns a formatted total to raise string
    function get_display_total() {
        $this->set_display_format();
        $display_string = number_format($this->olimometer_total_value,$this->olimometer_display_dp,$this->olimometer_display_decimal,$this->olimometer_display_thousands);
        return $this->wrap_prefix_and_suffix($display_string);
    }

    // Returns a formatted '0' value string
    function get_display_zero() {
        $this->set_display_format();
        $display_string = number_format(0,$this->olimometer_display_dp,$this->olimometer_display_decimal,$this->olimometer_display_thousands);
        return $this->wrap_prefix_and_suffix($display_string);       
    }

    // How much do we need to raise to meet the target?
    function get_display_remaining() {
        $this->set_display_format();
        $togo = $this->olimometer_total_value - $this->olimometer_progress_value;

        $display_string = number_format($togo,$this->olimometer_display_dp,$this->olimometer_display_decimal,$this->olimometer_display_thousands);
        return $this->wrap_prefix_and_suffix($display_string);       
    }

    function set_display_format() {
        // Load display array values
        // No dp, no thousands
        $olimometer_display_array[0][0] = 0;
        $olimometer_display_array[0][1] = '';
        $olimometer_display_array[0][2] = '.';
        // no dp, thousands marker
        $olimometer_display_array[1][0] = 0;
        $olimometer_display_array[1][1] = ',';
        $olimometer_display_array[1][2] = '.';
        // 2dp, no thousands
        $olimometer_display_array[2][0] = 2;
        $olimometer_display_array[2][1] = '';
        $olimometer_display_array[2][2] = '.';
        // 2dp, thousands marker
        $olimometer_display_array[3][0] = 2;
        $olimometer_display_array[3][1] = ',';
        $olimometer_display_array[3][2] = '.';
        // no dp, period thousands marker
        $olimometer_display_array[4][0] = 0;
        $olimometer_display_array[4][1] = '.';
        $olimometer_display_array[4][2] = ',';
        // 2dp (comma), period thousands marker
        $olimometer_display_array[5][0] = 2;
        $olimometer_display_array[5][1] = '.';
        $olimometer_display_array[5][2] = ',';
        // no dp, space thousands marker
        $olimometer_display_array[6][0] = 0;
        $olimometer_display_array[6][1] = ' ';
        $olimometer_display_array[6][2] = '.';
        // 2dp (comma), space thousands marker
        $olimometer_display_array[7][0] = 2;
        $olimometer_display_array[7][1] = ' ';
        $olimometer_display_array[7][2] = ',';
        // 2dp (period), space thousands marker
        $olimometer_display_array[8][0] = 2;
        $olimometer_display_array[8][1] = '.';
        $olimometer_display_array[8][2] = ' ';

        $this->olimometer_display_dp = $olimometer_display_array[$this->olimometer_number_format][0];
        $this->olimometer_display_thousands = $olimometer_display_array[$this->olimometer_number_format][1];
        $this->olimometer_display_decimal = $olimometer_display_array[$this->olimometer_number_format][2];
    }   


    // Returns a string wrapped with the required prefix and suffix
    function wrap_prefix_and_suffix($formatted_value) {
        // Figure out prefix and suffix values
        switch($this->olimometer_currency) {
            case 128:
                $currency_symbol = "&#8364;";
                //$currency_symbol = "a";
                break;
            case '':
                $currency_symbol = "";
                break;
            case 'x':
                $currency_symbol = "";
                break;
            case '10000':
                $currency_symbol = "kr ";
                break;
		    case '10001':
                $currency_symbol = "CHF ";
                break;		
            default:
                $currency_symbol = "&#$this->olimometer_currency;";
        }
    
        switch($this->olimometer_suffix) {
            case 128:
                $suffix_symbol = "&#8364;";
                break;
            case '':
                $suffix_symbol = "";
                break;
            case 'x':
                $suffix_symbol = "";
                break;
            case '10000':
                $suffix_symbol = " kr";
                break;
		    case '10001':
                $suffix_symbol = " CHF";
                break;				
            default:
                $suffix_symbol = "&#$this->olimometer_suffix;";
        }
        return $currency_symbol.$formatted_value.$suffix_symbol;
    }
    
}
?>