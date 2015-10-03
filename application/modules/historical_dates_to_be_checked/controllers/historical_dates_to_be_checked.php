<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Historical_dates_to_be_checked extends MX_Controller
{

function __construct() {
parent::__construct();
}

function hi() {
	echo "hello";
}

function populate_table($startpoint=NULL) {
	//populate the historical_dates_to_be_checked table with dates that should be checked

	if (!is_numeric($startpoint)) {
		echo "Add the start point to the URL as a timestamp.  To check from beginning, just add 0.";
		die();
	}

	if ($startpoint<1000) {
		$this->load->module('stocks_feed');
		$startpoint = $this->stocks_feed->get_min_date();
	}

	$this->load->module('stocks_feed');
	$timestamp1 = $startpoint;
	$timestamp2 = time();

	$this->load->module('timedate');
	$days = $this->timedate->get_days_from_range($timestamp1, $timestamp2);
	foreach($days as $day) {
		$this->_attempt_insert($day);
	}

	echo "Finished<br><br>";
	echo anchor('dashboard', "Return To Dashboard");
}

function _attempt_insert($day) {
	$count = $this->count_where('unix_timestamp', $day);
	if ($count<1) {
		$data['unix_timestamp'] = $day;
		$this->_insert($data);
	}
}

function get($order_by) {
$this->load->model('mdl_historical_dates_to_be_checked');
$query = $this->mdl_historical_dates_to_be_checked->get($order_by);
return $query;
}

function get_with_limit($limit, $offset, $order_by) {
$this->load->model('mdl_historical_dates_to_be_checked');
$query = $this->mdl_historical_dates_to_be_checked->get_with_limit($limit, $offset, $order_by);
return $query;
}

function get_where($id) {
$this->load->model('mdl_historical_dates_to_be_checked');
$query = $this->mdl_historical_dates_to_be_checked->get_where($id);
return $query;
}

function get_where_custom($col, $value) {
$this->load->model('mdl_historical_dates_to_be_checked');
$query = $this->mdl_historical_dates_to_be_checked->get_where_custom($col, $value);
return $query;
}

function _insert($data) {
$this->load->model('mdl_historical_dates_to_be_checked');
$this->mdl_historical_dates_to_be_checked->_insert($data);
}

function _update($id, $data) {
$this->load->model('mdl_historical_dates_to_be_checked');
$this->mdl_historical_dates_to_be_checked->_update($id, $data);
}

function _delete($id) {
$this->load->model('mdl_historical_dates_to_be_checked');
$this->mdl_historical_dates_to_be_checked->_delete($id);
}

function count_where($column, $value) {
$this->load->model('mdl_historical_dates_to_be_checked');
$count = $this->mdl_historical_dates_to_be_checked->count_where($column, $value);
return $count;
}

function get_max() {
$this->load->model('mdl_historical_dates_to_be_checked');
$max_id = $this->mdl_historical_dates_to_be_checked->get_max();
return $max_id;
}

function _custom_query($mysql_query) {
$this->load->model('mdl_historical_dates_to_be_checked');
$query = $this->mdl_historical_dates_to_be_checked->_custom_query($mysql_query);
return $query;
}

}