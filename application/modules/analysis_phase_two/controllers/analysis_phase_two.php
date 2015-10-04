<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Analysis_phase_two extends MX_Controller
{

function __construct() {
parent::__construct();
}

function go() {

	$nowtime = time();

	//get an array of all of the checkpoints
	$checkpoints = $this->get_checkpoints_for_day($nowtime);

	$this->load->module('timedate');

	$nice_date = $this->timedate->get_nice_date($nowtime, 'full');
	echo "<h2>Now is $nice_date</h2>";


	$count = 0;
	foreach ($checkpoints as $key => $value) {
		$count++;
		$nice_date = $this->timedate->get_nice_date($value, 'full');
		echo $nice_date."<br>";
		echo "key of $key has value of $value<br><hr>";
	}

	echo $count;

}

function get_checkpoints_interval() {
	//the time gap between each price check
	$this->load->module('site_settings');
	$interval = $this->site_settings->get_checkpoints_interval();
	return $interval;
}

function get_checkpoints_for_day($unix_timestamp) {
	
	$this->load->module('timedate');
	$start_of_day = $this->timedate->get_start_of_day_as_timestamp($unix_timestamp);

	//get the date numer
	$date_number = date('j', $start_of_day);
	$interval = $this->get_checkpoints_interval();

	$checkpoints[] = $start_of_day;

	$end_of_day = $start_of_day+86400;
	for ($i=$start_of_day; $i < $end_of_day; $i++) { 
		$i = $i+$interval;
		$checkpoints[] = $i;
	}

	return $checkpoints;
}

function testYEAH() {
	//return all of the checkpoints that need to be tested for a certain day
	$unix_timestamp = time();
	$checkpoints = $this->get_checkpoints_for_day($unix_timestamp);
	echo $checkpoints;

	foreach ($checkpoints as $key => $value) {
		echo $value."<br>";
	}

	echo "num check points is ";
	$num_checkpoints = $this->get_num_required_checkpoints();
	echo $num_checkpoints;
}




function get($order_by) {
$this->load->model('mdl_analysis_phase_two');
$query = $this->mdl_analysis_phase_two->get($order_by);
return $query;
}

function get_with_limit($limit, $offset, $order_by) {
$this->load->model('mdl_analysis_phase_two');
$query = $this->mdl_analysis_phase_two->get_with_limit($limit, $offset, $order_by);
return $query;
}

function get_where($id) {
$this->load->model('mdl_analysis_phase_two');
$query = $this->mdl_analysis_phase_two->get_where($id);
return $query;
}

function get_where_custom($col, $value) {
$this->load->model('mdl_analysis_phase_two');
$query = $this->mdl_analysis_phase_two->get_where_custom($col, $value);
return $query;
}

function _insert($data) {
$this->load->model('mdl_analysis_phase_two');
$this->mdl_analysis_phase_two->_insert($data);
}

function _update($id, $data) {
$this->load->model('mdl_analysis_phase_two');
$this->mdl_analysis_phase_two->_update($id, $data);
}

function _delete($id) {
$this->load->model('mdl_analysis_phase_two');
$this->mdl_analysis_phase_two->_delete($id);
}

function count_where($column, $value) {
$this->load->model('mdl_analysis_phase_two');
$count = $this->mdl_analysis_phase_two->count_where($column, $value);
return $count;
}

function get_max() {
$this->load->model('mdl_analysis_phase_two');
$max_id = $this->mdl_analysis_phase_two->get_max();
return $max_id;
}

function _custom_query($mysql_query) {
$this->load->model('mdl_analysis_phase_two');
$query = $this->mdl_analysis_phase_two->_custom_query($mysql_query);
return $query;
}

}