<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Stock_alerts extends MX_Controller
{

function __construct() {
parent::__construct();
}

function get_target_percent_movement($day_type) {
	if ($day_type=="up") {
		$target_percent = -1.2; //on an up day stock must be less than this
	} else {
		$target_percent = 1.5; //on a down day stock must be greater than this
	}
	return $target_percent;
}

function _attempt_alert() {

	$is_alert_due = $this->_is_alert_due();
	if ($is_alert_due==TRUE) {
		//we need to create at least one alert!

		//find out what kind of day we are having
		$this->load->module('price_movements_daily');
    	$day_type = $this->price_movements_daily->get_day_type();
    	$target_percent_movement = $this->get_target_percent_movement($day_type);
		
    	//Get any stocks that have moved in the opposite direction from the majority by >x%?
    	if ($day_type=="up") {
    		$trade_advice = "go long";
    		$additional_sql = "where percentage_change<=$target_percent_movement"; //up day
    	} else {
    		$trade_advice = "go short";
    		$additional_sql = "where percentage_change>=$target_percent_movement"; //down day
    	}
	
    	$mysql_query = "select * from price_movements_daily ".$additional_sql;

    	$query = $this->price_movements_daily->_custom_query($mysql_query);
    	foreach($query->result() as $row) {
    		$stock_symbol = $row->stock_symbol;
    		$sent_alert = $this->sent_alert_today($stock_symbol);

    		if ($sent_alert==FALSE) {
    			$message = $stock_symbol.": ".$trade_advice;
    			
                $this->load->module('site_settings');
                $nowtime = $this->site_settings->get_nowtime();

				$date_created = date('l jS \of F Y', $nowtime);
				$sent = 1;
    			$this->_create_alert($stock_symbol, $message, $date_created, $sent);
    		}
    	}
	}
	
}

function _create_alert($stock_symbol, $message, $date_created, $sent) {
	//insert into database
	$data['stock_symbol'] = $stock_symbol;
	$data['message'] = $message;
	$data['date_created'] = $date_created;
	$data['sent'] = $sent;
	$this->_insert($data);


	//Now send an SMS (text) message, emails etc
	$target_mobnum = "07956030868";
	$this->load->module('sms');
	$from_name = "Davcon";
	$this->sms->fire_text($target_mobnum, $message, $from_name);


}

function _is_alert_due() {
	//have all of the conditions been met for sending an alert?
	$this->load->module('site_settings');
    $nowtime = $this->site_settings->get_nowtime();

	//TEST 1: Is this a weekday?
	$this_day = date('l', $nowtime);
	if (($this_day=="Saturday") || ($this_day=="Sunday")) {
		$alert_due = FALSE;
		return $alert_due;
	}

	//TEST 2: Is the time beween 4:30 and 5:30pm (London time)
    $day = date('j', $nowtime);
    $month = date('n', $nowtime);
    $year = date('Y', $nowtime);

    $hour=16;
    $minute=30;
    $second=0;
    $time_one = mktime($hour, $minute, $second, $month, $day, $year);
  
    $hour=17;
    $minute=30;
    $second=0;
    $time_two = mktime($hour, $minute, $second, $month, $day, $year);

    if (($nowtime<$time_one) || ($nowtime>$time_two)) {
    	$alert_due = FALSE;
		return $alert_due;
    }

    //TEST 3: Is this an overall positive or negative day?
    $this->load->module('price_movements_daily');
    $day_type = $this->price_movements_daily->get_day_type();
    $target_percent_movement = $this->get_target_percent_movement($day_type);

    if ($day_type=="sideways") {
    	$alert_due = FALSE;
		return $alert_due;
    }

    //TEST 4: Have any stocks moved in the opposite direction from the majority by x%?
    if ($day_type=="up") {
    	$additional_sql = "where percentage_change<=$target_percent_movement"; //up day
    } else {
    	$additional_sql = "where percentage_change>=$target_percent_movement"; //down day
    }

    $mysql_query = "select * from price_movements_daily ".$additional_sql;
    $query = $this->price_movements_daily->_custom_query($mysql_query);
    $num_rows = $query->num_rows();
    if ($num_rows<1) {
     	$alert_due = FALSE; //no anomaly stocks 
		return $alert_due;   	
    }

    $alerts_required = 0;
	//TEST 5: have we already sent an alert today for the anomaly stocks?
    foreach($query->result() as $row) {
    	$stock_symbol = $row->stock_symbol;
    	$sent_alert = $this->sent_alert_today($stock_symbol);

    		if ($sent_alert==FALSE) {
    			$alerts_required++;
    		}
    }

    if ($alerts_required<1) {
    	$alert_due = FALSE; //no alerts are required
		return $alert_due;
    }

    //If we have made it this far then there must be an alert due
    $alert_due = TRUE;
    return $alert_due;
}

function sent_alert_today($stock_symbol) {
	//have we sent an alert today for this stock?
	$this->load->module('site_settings');
    $nowtime = $this->site_settings->get_nowtime();
    
	$the_date = date('l jS \of F Y', $nowtime);
	$mysql_query = "select * from Stock_alerts where stock_symbol='$stock_symbol' and date_created='$the_date'";
	$query = $this->_custom_query($mysql_query);
	$num_rows = $query->num_rows();
	if ($num_rows>0) { //we have already sent an alert today!
		$sent_alert = TRUE;
	} else {
		$sent_alert = FALSE;
	}
	return $sent_alert;
}

function get($order_by) {
$this->load->model('mdl_stock_alerts');
$query = $this->mdl_stock_alerts->get($order_by);
return $query;
}

function get_with_limit($limit, $offset, $order_by) {
$this->load->model('mdl_stock_alerts');
$query = $this->mdl_stock_alerts->get_with_limit($limit, $offset, $order_by);
return $query;
}

function get_where($id) {
$this->load->model('mdl_stock_alerts');
$query = $this->mdl_stock_alerts->get_where($id);
return $query;
}

function get_where_custom($col, $value) {
$this->load->model('mdl_stock_alerts');
$query = $this->mdl_stock_alerts->get_where_custom($col, $value);
return $query;
}

function _insert($data) {
$this->load->model('mdl_stock_alerts');
$this->mdl_stock_alerts->_insert($data);
}

function _update($id, $data) {
$this->load->model('mdl_stock_alerts');
$this->mdl_stock_alerts->_update($id, $data);
}

function _delete($id) {
$this->load->model('mdl_stock_alerts');
$this->mdl_stock_alerts->_delete($id);
}

function count_where($column, $value) {
$this->load->model('mdl_stock_alerts');
$count = $this->mdl_stock_alerts->count_where($column, $value);
return $count;
}

function get_max() {
$this->load->model('mdl_stock_alerts');
$max_id = $this->mdl_stock_alerts->get_max();
return $max_id;
}

function _custom_query($mysql_query) {
$this->load->model('mdl_stock_alerts');
$query = $this->mdl_stock_alerts->_custom_query($mysql_query);
return $query;
}

}