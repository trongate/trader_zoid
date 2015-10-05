<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Analysis_phase_two extends MX_Controller
{

function __construct() {
parent::__construct();
}

function test2() {
	$this->load->module('stocks_feed');
	$mysql_query = "select * from stocks_feed where stock_symbol='MSFT' order by id";
	$query = $this->stocks_feed->_custom_query($mysql_query);
	foreach($query->result() as $row) {
		
		echo $row->date_added."<br>";
		echo "DATE IS : ";
		echo Modules::run('timedate/get_nice_date', $row->date_added, 'full')."<br>";
		echo $row->price."<br>";
		echo "<hr>";
		
	}
	$stock_symbol = "MSFT";
	$unix_timestamp = 1429644253;
	$nice_date = Modules::run('timedate/get_nice_date', $unix_timestamp, 'cool')."<br>";
	echo "<h1>Gimme The Price on $unix_timestamp | $nice_date </h1>";
	$this->load->module('gimme_the_price');
	$price = $this->gimme_the_price->get_price($stock_symbol, $unix_timestamp);
	echo $price;	
}

function test() {
	//works with dummy stocks feed table
	$this->load->module('stocks_feed');
	$query = $this->stocks_feed->get('id');
	foreach($query->result() as $row) {
		echo $row->date_added."<br>";
		echo Modules::run('timedate/get_nice_date', $row->date_added, 'full')."<br>";
		echo $row->price."<br>";
		echo "<hr>";
	}
	$stock_symbol = "MSFT";
	$unix_timestamp = 1441993497;
	echo "<h1>Gimme The Price</h1>";
	$this->load->module('gimme_the_price');
	$price = $this->gimme_the_price->get_price($stock_symbol, $unix_timestamp);
	echo $price;
}

function test3() {
	$unix_timestamp = 1430388324;
	$stock_symbol = 'MSFT';
	$this->load->module('gimme_the_price');
	$price = $this->gimme_the_price->get_price($stock_symbol, $unix_timestamp);
	echo "for $unix_timestamp the price is $price";
}

function test4() {
	$unix_timestamp = $this->uri->segment(3);
	$stock_symbol = 'MSFT';
	$this->load->module('gimme_the_price');
	$price = $this->gimme_the_price->get_price($stock_symbol, $unix_timestamp);
	echo "for $unix_timestamp the price is $price";
}

function go() {

	$this->load->module('site_settings');
	$nowtime = $this->site_settings->get_nowtime();
	$this->load->module('gimme_the_price');

	//clear phase two results
	$this->load->module('phase_two_results');
	$this->phase_two_results->_clear_table();

	//repopulate today's checkpoints for the target stock

	//enter today's prices into chartcalc table

	//get an array of all of the checkpoints
	$checkpoints_for_today = $this->get_checkpoints_for_day($nowtime);



	$stock_symbol = "MSFT";
	foreach ($checkpoints_for_today as $key => $value) {

		$unix_timestamp = $value;
		$value = $this->timedate->get_nice_date($unix_timestamp, 'full');
		echo $value." ( ".$unix_timestamp.")<br>";
		$price = $this->gimme_the_price->get_price($stock_symbol, $unix_timestamp);
		echo "<h3>$price</h3>";
	}
	die();

	$stock_symbol = "MSFT";
	$unix_timestamp = $nowtime;
	$this->_analyse_stock($stock_symbol, $unix_timestamp, $checkpoints_for_today);


	echo "finished";
	//fetch result from phase_one_results table

	//get checkpoints for that time

	//get price for that time

}

function _analyse_stock($stock_symbol, $unix_timestamp, $checkpoints_for_today) {

	//clear today's checkpoints
	$this->load->module('chartcalc');
	$this->chartcalc->_clear_table();

	//populate table for today prices
	$this->load->module('chart_today');
	$this->load->module('gimme_the_price');
	foreach ($checkpoints_for_today as $unix_timestamp) {
		# code...
		$data['chart_today'] = $this->gimme_the_price->get_price($stock_symbol, $unix_timestamp);
		$this->chartcalc->_insert($data);
	}
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

	//how many seconds are we from the opening bell
	$seconds_from_opening_bell = $this->timedate->get_seconds_from_opening_bell($unix_timestamp);

	$end_of_day = $start_of_day+$seconds_from_opening_bell;
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