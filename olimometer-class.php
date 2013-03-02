<?php
// Class to define an Olimometer object


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
    
    private $olimometer_default_link = "http://www.olivershingler.co.uk/oliblog/olimometer/";
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
        
        if($query_results['olimometer_link'] == "" || $query_results['olimometer_link'] == null) {
            $this->olimometer_link = $this->olimometer_default_link;
        }
        else {
            $this->olimometer_link = $query_results['olimometer_link'];
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
                                                                'olimometer_stayclassypid' => $this->olimometer_stayclassypid
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
                                'olimometer_stayclassypid' => $this->olimometer_stayclassypid
                        ), 
                        array( 'olimometer_id' => $this->olimometer_id )
                    );
/*
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
                                'olimometer_overlay_y' => $this->olimometer_overlay_y
                        ), 
                        array( 'olimometer_id' => $this->olimometer_id )
                    );
*/
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

        // If StayClassy integration is configured, get the current balance and save it
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
    
    
        $olimometer_font = "LiberationSans-Regular.ttf";

        $image_location = plugins_url('olimometer/thermometer.php', dirname(__FILE__) );
        
        
        $the_olimometer_text = "<a href='".$this->olimometer_link."' target=_blank><img src='".$image_location."?olimometer_id=".$this->olimometer_id."' border=0";
        if(strlen($css_class) > 0) {
            $the_olimometer_text = $the_olimometer_text." class='".$css_class."'";
        }
        $the_olimometer_text = $the_olimometer_text." alt='Olimometer 2.45'></a>";
        
        return $the_olimometer_text;
        //return null;
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
            return urldecode($httpParsedResponseAr["L_AMT0"]);
        }
        else  {
            return false;
        }
    }

    
}
?>