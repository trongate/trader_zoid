<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sms extends MX_Controller 
{
    function __construct() 
    {
        parent::__construct();
        $username = $this->get_username();
        $password = $this->get_password();
        $this->my_url = self::$URL."?username=$username&password=$password&option=xml";
    }
    


    private static $URL = 'http://www.textmarketer.biz/gateway/';
    private $my_url;
    private $error;
    private $numberOfCreditsRemaining;
    private $creditsUsed;
    private $transaction_id;


    function get_username() {
        $username = "342nd";
        return $username;
    }
    
    
    function get_password() {
        $password = "bbn8s";
        return $password;
    }
    
    
    function get_target_mobile_numbers() {
        $target_mobile[] = "07956030868"; //DC
        return $target_mobile;
    }
    

    function test() {
       echo "hello"; 
       die();
       $target_mobile_number = $this->get_target_mobile_number();
       $message = "This is a test message from the new system.  I hope it reaches you (DC)";
       $from_name = "FVL System";
       $this->fire_text($target_mobile_number, $message, $from_name);
       echo "message was sent";
    }
    
    
    function _send_text_message($data) {
        //THIS IS THE ONE THAT ACTUALLY GETS USED ON THE WEBSITE!
        
        if (!isset($data['firstname'])) {
            $data['firstname'] = "";
        }
        
        
        if (!isset($data['lastname'])) {
            $data['lastname'] = "";
        }
        
        
        if (!isset($data['message_type'])) {
            $data['message_type'] = "";
        }
         
        
        if (!isset($data['telnum'])) {
            $data['telnum'] = "";
        }
        
        
        if (!isset($data['message'])) {
            $data['message'] = "";
        }
        
        
        $firstname = $data['firstname'];
        $lastname = $data['lastname'];
        $message_type = $data['message_type'];
        $telnum = $data['telnum'];
        $message = $data['message'];
        $name = $firstname." ".$lastname;
        
        //start building the message
        $the_message = $message_type.": ";
        $the_message.=$name." ";
        $the_message.=$telnum. " :: ";
        $the_message.=$message;
        
        $this->load->helper('text');
        $the_message = character_limiter($the_message, 140);
        
        $the_message = str_replace('&#8230;', '...', $the_message);
        
        $from_name = "FVL System";
        
        $target_mobile_numbers = $this->get_target_mobile_numbers();
        
            foreach($target_mobile_numbers as $target_mobile_number) {
                $is_number_good = $this->is_mobile_good($target_mobile_number);
                
                if ($is_number_good==TRUE) {
                    $this->fire_text($target_mobile_number, $the_message, $from_name);
                }
            }
     
    }
    
    
    
    function is_mobile_good($number) {
        //get the first character
        $str = substr($number, 0, 2);
        
        if (($str=="07") OR ($str=="09")) {
            return TRUE;
        } else {
            return FALSE;
        }
        
    }
    
    

    

    // Sends an SMS to the gateway, the message length must be between 1 and 640 characters long.
    public function send($number, $message, $originator) {
        $this->error = null;

        $query_string = "&number=$number&message=".urlencode($message).'&orig='.urlencode($originator);
        $fp = fopen($this->my_url.$query_string, 'r');
        $response = fread($fp, 1024);
        return $this->processResponse($response);
    }

    // Returns an array of error messages
    public function getError() {
        $arr = each($this->error);
        return $arr['value'];
    }
    
    // the total of credits you have left in your account
    public function getCreditsRemaining() {
        return $this->numberOfCreditsRemaining;
    }
    
    // This is the unique code that represents your send, you need this code to match up delivery reports
    public function getTransactionID() {
        return $this->transaction_id;
    }

    // how many credits were used for the send, a message that uses more than 160 characters will use more credits. 1 CR = 160 characters
    public function getCreditsUsed() {
        return $this->creditsUsed;
    }
    
    //////// PRIVATE FUNCTIONS
    private function processResponse($r) {
        $xml=simplexml_load_string($r);
        if($xml['status'] == "failed") {
            foreach($xml->reason as $index => $reason) 
                $this->error[] = $reason; /// parse the errors into an array
            return false;
        } else {
            $this->transaction_id = $xml['id'];
            $this->numberOfCreditsRemaining = $xml->credits;
            $this->creditsUsed = $xml->credits_used;
            return true;
        }
    }
    
    
    
    
    
function fire_text($target_mobile_number, $message, $from_name) {    
    if($this->send($target_mobile_number, $message, $from_name)){
        //echo "I have ".$sms->getCreditsRemaining()." left and I used ".$sms->getCreditsUsed()." credits";
        //REPORT credits left
        
        $credits_left = $this->getCreditsRemaining();
        $this->_update_table($credits_left);
        
    } else {
            //REPORT ERRORS
           // while($error = $this->getError()) {
           // echo $error;
           // }
    }
}
    



function _update_table($credits_remaining) {
    $data['date_updated'] = time();
    $data['credits_remaining'] = $credits_remaining;
    $id = 1;
    $this->_update($id, $data);
}







function get($order_by){
    $this->load->model('mdl_sms');
    $query = $this->mdl_sms->get($order_by);
    return $query;
}

function get_with_limit($limit, $offset, $order_by) {
    $this->load->model('mdl_sms');
    $query = $this->mdl_sms->get_with_limit($limit, $offset, $order_by);
    return $query;
}

function get_where($id){
    $this->load->model('mdl_sms');
    $query = $this->mdl_sms->get_where($id);
    return $query;
}

function get_where_custom($col, $value) {
    $this->load->model('mdl_sms');
    $query = $this->mdl_sms->get_where_custom($col, $value);
    return $query;
}

function _insert($data){
    $this->load->model('mdl_sms');
    $this->mdl_sms->_insert($data);
}

function _update($id, $data){
    $this->load->model('mdl_sms');
    $this->mdl_sms->_update($id, $data);
}

function _delete($id){
    $this->load->model('mdl_sms');
    $this->mdl_sms->_delete($id);
}

function count_where($column, $value) {
    $this->load->model('mdl_sms');
    $count = $this->mdl_sms->count_where($column, $value);
    return $count;
}

function get_max() {
    $this->load->model('mdl_sms');
    $max_id = $this->mdl_sms->get_max();
    return $max_id;
}

function _custom_query($mysql_query) {
    $this->load->model('mdl_sms');
    $query = $this->mdl_sms->_custom_query($mysql_query);
    return $query;
}


function count_all() {
    $this->load->model('mdl_sms');
    $count = $this->mdl_sms->count_all();
    return $count;
}



function show_credits() {
    $data['credits_remaining'] = $this->get_credits_remaining();
    $data['username'] = $this->get_username();
    $data['password'] = $this->get_password();
    $data['headline'] = "SMS Credits";
    $data['sub_headline'] = "A summary of your SMS status";
    $data['view_file'] = "show_credits";
    $this->load->module('template');
    $this->template->admin($data);
}

function get_credits_remaining() {
    $id = 1;
    $query = $this->get_where($id);
    foreach($query->result() as $row) {
        $credits_remaining = $row->credits_remaining;
    }
    
    if (!isset($credits_remaining)) {
        $credits_remaining = "unknown";
    }
    
    return $credits_remaining;    
}


function credits() {
    $id = 1;
    $query = $this->get_where($id);
    foreach($query->result() as $row) {
        $credits_remaining = $row->credits_remaining;
    }
    
    if (!isset($credits_remaining)) {
        $credits_remaining = "unknown";
    }
    
    echo "Credits Remaining: ".$credits_remaining;
    
}



    
 
}       
    
    
  