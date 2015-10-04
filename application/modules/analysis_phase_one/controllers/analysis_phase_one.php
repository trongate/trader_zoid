<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Analysis_phase_one extends MX_Controller
{

/*

This module gets the price of a stock RIGHT NOW (relative to open)

THEN LOOPS THROUGH ALL CANDIDATES AND...
{
		and finds the price of the same stock at different dates, at the same time.
		
		If date is within tollerance range then gets added to array
		
		finished array is then insert into phase_one_results
		
		IF NOT matching dates then stock is deleted from candidates pool 
}
*/

function __construct() {
parent::__construct();
}

function test2() {
	//test opening bell timestamps
}

function test3() {
	//are 4 prices within target range?

	$current_opening_price = 100;
	$current_stock_price = 200;

	$historic_opening_price = 50;
	$historic_stock_price = 40;

	$tollerance_percent = $this->get_tollerance();

	$are_prices_within_target_range = $this->are_prices_within_target_range($current_opening_price, $current_stock_price, $historic_opening_price, $historic_stock_price, $tollerance_percent);
	if ($are_prices_within_target_range==TRUE) {
		echo "TRUE";
	} else {
		echo "FALSE";
	}
}

function test() {
	//calculate the percent change between two prices
	$price_one = 100;
	$price_two = 150;
	$percent_change = (($price_two/$price_one)-1)*100;
	$percent_change = abs($percent_change);
	echo $percent_change;
}

function get_tollerance() {
	$this->load->module("site_settings");
	$tollerance_percent = $this->site_settings->get_phase_one_tollerance_percent();
	return $tollerance_percent;
}






function go() {

	$comment = "Phase one analysis started.";
	$this->load->module('auto_comments');
	$this->auto_comments->_insert_comment($comment);

	$tollerance_percent = $this->get_tollerance();

	//get all of the stocks to be checked
	$this->load->module('candidates');
	$query_stocks = $this->candidates->get_active_stocks();
	foreach($query_stocks->result() as $row_stocks) {
		$stocks_to_be_checked[] = $row_stocks->stock_symbol;
	}

	//figure out how far we are past opening time
	$this->load->module('timedate');
	$nowtime = time();
	$seconds_from_opening_bell = $this->timedate->get_seconds_from_opening_bell($nowtime);

	//get all of the historical dates to be checked
	$this->load->module('historical_dates_to_be_checked');
	$query_dates = $this->historical_dates_to_be_checked->get('unix_timestamp');
	foreach($query_dates->result() as $row_dates) {
		$unix_timestamp = $row_dates->unix_timestamp;
		$this->_check_date_for_stocks($unix_timestamp, $stocks_to_be_checked, $seconds_from_opening_bell, $tollerance_percent);
	}
}

function _check_date_for_stocks($unix_timestamp, $stocks_to_be_checked, $seconds_from_opening_bell, $tollerance_percent) {
	//check this date for ALL of the stocks that need to be checked

	echo "*** START OF CHECKING A DATE ***<br><br>";

	$this->load->module('auto_comments');

	$this->load->module('timedate');
	$this->load->module('gimme_the_price');

	$opening_bell_time = $this->timedate->get_opening_bell_time_as_timestamp($unix_timestamp);

	$positive_results = 0;
		foreach($stocks_to_be_checked as $stock_symbol) {

			$comment = "Checking $stock_symbol.";	
			$this->auto_comments->_insert_comment($comment);

			if (!isset($current_stock_price)) {
				//get the current stock price and the price at today's opening bell
				$nowtime = time();
				$current_stock_price = $this->gimme_the_price->get_price($stock_symbol, $nowtime);
				$opening_bell_today = $this->timedate->get_opening_bell_time_as_timestamp($nowtime);
				$current_opening_price = $this->gimme_the_price->get_price($stock_symbol, $opening_bell_today);
			}

			$opening_bell_historic = $this->timedate->get_opening_bell_time_as_timestamp($unix_timestamp);
			$historic_opening_price = $this->gimme_the_price->get_price($stock_symbol, $opening_bell_historic);
			$historic_target_time = $opening_bell_historic+$seconds_from_opening_bell;
			$historic_stock_price = $this->gimme_the_price->get_price($stock_symbol, $historic_target_time);

			if (($historic_opening_price!=$historic_stock_price)) {

				echo "TIMESTAMP OPENING BELL: ".$opening_bell_today."<br>";
				echo "TIMESTAMP NOWTIME: ".$nowtime."<br>";
				echo "TIMESTAMP OPENING BELL: ".$opening_bell_historic."<br>";
				echo "TIMESTAMP AT NOWTIME: ".$historic_target_time."<br>";
	
				echo "<b>Opening Bell Price: </b>".$current_opening_price."<br>";
				echo "<b>Current Price: </b>".$current_stock_price."<br>";
				$historic_date = $this->timedate->get_nice_date($unix_timestamp, 'cool');
				echo "<br><b>Historic Date: </b>".$historic_date."<br>";
				echo "<b>Historic Opening Bell Price: </b>".$historic_opening_price."<br>";
				echo "<b>Historic Stock Price At This Time: </b>".$historic_stock_price."<br>";

				$are_prices_within_target_range = $this->are_prices_within_target_range($current_opening_price, $current_stock_price, $historic_opening_price, $historic_stock_price, $tollerance_percent);
				if ($are_prices_within_target_range==TRUE) {
					//add this to the database for deeper analysis
					$positive_results++;
					echo "<h1 style='color: green;'>THIS IS POTENTIALLY A MATCHING CHART</h1>";
				} else {
					echo "<h1 style='color: red;'>NO MATCHING CHART</h1>";
				}
			} else {
				echo "No data for this date.";
			}
		}

		if ($positive_results<1) {
			//score this stock off of the candidates list
			$this->load->module('candidates');
		}

	echo "<br>END OF CHECKING A DATE<br><hr>";
}




function are_prices_within_target_range($current_opening_price, $current_stock_price, $historic_opening_price, $historic_stock_price, $tollerance_percent) {
	//return TRUE (if good) or FALSE to ignore

	if (($current_opening_price==0) || ($current_stock_price==0) || ($historic_opening_price==0) || ($historic_stock_price==0)) {
		return FALSE; //no point in wasting time if figures are duff
	}

	
	$percent_change1 = (($current_stock_price/$current_opening_price)-1)*100;

	echo "<hr>";

	echo "current opening bell price is $current_opening_price<br>";
	echo "current stock price is $current_stock_price<br>";
	echo "percent change today is $percent_change1";

	echo "<hr>";


	if ($historic_opening_price==0) {
		$percent_change2 = 0;
	} else {
		$percent_change2 = (($historic_stock_price/$historic_opening_price)-1)*100;
	}


	echo "<hr>";

	echo "historic opening bell price is $historic_opening_price<br>";
	echo "historic stock price is $historic_stock_price<br>";
	echo "percent change historic is $percent_change2";

	echo "<hr>";
	

	$price_percent_difference = $percent_change2-$percent_change1;
	$price_percent_difference = abs($price_percent_difference);




	echo "<br>The difference between $percent_change2 and $percent_change1 is $price_percent_difference<br>";
	echo "We are aiming for difference to be within $tollerance_percent, so result is<br>";

	if ($price_percent_difference<=$tollerance_percent) {
		return TRUE;
	} else {
		return FALSE;
	}

}



function get($order_by) {
$this->load->model('mdl_analysis_phase_one');
$query = $this->mdl_analysis_phase_one->get($order_by);
return $query;
}

function get_with_limit($limit, $offset, $order_by) {
$this->load->model('mdl_analysis_phase_one');
$query = $this->mdl_analysis_phase_one->get_with_limit($limit, $offset, $order_by);
return $query;
}

function get_where($id) {
$this->load->model('mdl_analysis_phase_one');
$query = $this->mdl_analysis_phase_one->get_where($id);
return $query;
}

function get_where_custom($col, $value) {
$this->load->model('mdl_analysis_phase_one');
$query = $this->mdl_analysis_phase_one->get_where_custom($col, $value);
return $query;
}

function _insert($data) {
$this->load->model('mdl_analysis_phase_one');
$this->mdl_analysis_phase_one->_insert($data);
}

function _update($id, $data) {
$this->load->model('mdl_analysis_phase_one');
$this->mdl_analysis_phase_one->_update($id, $data);
}

function _delete($id) {
$this->load->model('mdl_analysis_phase_one');
$this->mdl_analysis_phase_one->_delete($id);
}

function count_where($column, $value) {
$this->load->model('mdl_analysis_phase_one');
$count = $this->mdl_analysis_phase_one->count_where($column, $value);
return $count;
}

function get_max() {
$this->load->model('mdl_analysis_phase_one');
$max_id = $this->mdl_analysis_phase_one->get_max();
return $max_id;
}

function _custom_query($mysql_query) {
$this->load->model('mdl_analysis_phase_one');
$query = $this->mdl_analysis_phase_one->_custom_query($mysql_query);
return $query;
}

}