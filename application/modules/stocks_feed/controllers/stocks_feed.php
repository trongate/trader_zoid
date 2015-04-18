<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Stocks_feed extends MX_Controller {

function get_start_of_day_prices($stock_symbol) {
	//get the IDs records which represent the start of a new trading day
	$current_day = "";
	$query = $this->get('date_added');
	foreach($query->result() as $row) {
		$last_trade = $row->last_trade;
		$this_day = substr($last_trade, 0, 6);

			if ($current_day!=$this_day) {
				$day_starts[] = $row->id; //add this to the array
				$current_day = $this_day;	
			}
	}

	if (!isset($day_starts)) {
		echo "There is not enough data to make this thing work.  D'oh!";
		die();
	}

	return $day_starts;
}

function test($stock_symbol) {
	$day_starts = $this->get_start_of_day_prices($stock_symbol);
	foreach ($day_starts as $key => $value) {
		echo $value."<br>";
	}

	echo "<h1>Now let us get the end of day ID's</h1>";
	$day_ends = $this->get_end_of_day_prices($stock_symbol);
	foreach ($day_ends as $key => $value) {
		echo $value."<br>";
	}
}

function get_prev_price_id($id) {
	//get the ID belonging to price just before a known price
	$query = $this->get_where($id);
	foreach($query->result() as $row) {
		$stock_symbol = $row->stock_symbol;
		$date_added = $row->date_added;
	}

	$this->load->model('mdl_stocks_feed');
	$prev_price_id = $this->mdl_stocks_feed->get_prev_price_id($stock_symbol, $date_added);
	return $prev_price_id;
}

function get_end_of_day_prices($stock_symbol) {
	//get the IDs records which represent the end of a new day
	//start by getting all of the day starts
	$day_starts = $this->get_start_of_day_prices($stock_symbol);
	foreach ($day_starts as $key => $value) {
		$end_of_day_prices[] = $this->get_prev_price_id($value); 
	}

	if (!isset($end_of_day_prices)) {
		echo "There is not enough data to make this thing work.  D'oh!";
		die();
	}

	return $end_of_day_prices;
}

function show_json($stock_symbol) {
	$mysql_query = "select * from stocks_feed where stock_symbol='$stock_symbol' order by date_added";
	$data['query'] = $this->_custom_query($mysql_query);
	$this->load->view('show_json');
}

function _has_new_trade_taken_place($data) {
	//has a new trade been executed for this stock?
	//accepts $data['stock_symbol'] and $data['last_trade']
	$stock_symbol = $data['stock_symbol'];
	$last_trade = $data['last_trade'];

	$mysql_query = "select * from stocks_feed where stock_symbol='$stock_symbol' and last_trade='$last_trade'";
	$query = $this->_custom_query($mysql_query);
	$num_rows = $query->num_rows();
	if ($num_rows>0) {
		return FALSE; //no new trades, therefore do NOT update database
	} else {
		return TRUE;
	}
}

function get_prices_for_candle($id, $chart_type) {
	//return low, open, close, high for candlestick chart
	//chart type can be 'daily', 'monthly' or 'weekly'

	$query = $this->get_where($id);
	foreach($query->result() as $row) {
		$stock_symbol = $row->stock_symbol;
		$end_time = $row->date_added;
		$price = $row->price;
	}

	if ($chart_type=="daily") {
		//get the previous ID

		//the previous ID happened around 20 minutes ago
		$twenty_minutes = 60*20;
		$nowtime = time();
		$start_time = $nowtime-$twenty_minutes;
	}

	$data['opening_price'] = $price-1; //the price of the previous entry
	$data['closing_price'] = $price;
	$data['lowest_price'] = $this->get_lowest_price_between_two_times($stock_symbol, $start_time, $end_time);
	$data['highest_price'] = $this->get_highest_price_between_two_times($stock_symbol, $start_time, $end_time);
	return $data;
}

function get_lowest_price_between_two_times($stock_symbol, $start_time, $end_time) {
	$this->load->model('mdl_stocks_feed');
	$price = $this->mdl_stocks_feed->get_lowest_price_between_two_times($stock_symbol, $start_time, $end_time);
	return $price;
}

function get_highest_price_between_two_times($stock_symbol, $start_time, $end_time) {
	$this->load->model('mdl_stocks_feed');
	$price = $this->mdl_stocks_feed->get_highest_price_between_two_times($stock_symbol, $start_time, $end_time);
	return $price;
}





function view_chart($stock_symbol) {
	//let's fetch the data for this stock
	$data['stock_symbol'] = $stock_symbol;
	$chart_type = $this->uri->segment(4);

	if (($chart_type!="weekly") && ($chart_type!="monthly")) {
		$chart_type = "daily"; //let's default to the daily chart type
	}

	if ($chart_type=="daily") {
		//get the NEWEST day start
		$start_of_day_prices = $this->get_start_of_day_prices($stock_symbol);
		foreach ($start_of_day_prices as $key => $value) {
			$day_start_id = $value;
		}

		//now figure out what the date_added value is for the newest day
		$query = $this->get_where($day_start_id);
		foreach($query->result() as $row) {
			$date_added = $row->date_added;
		}
		$additional_sql = " and date_added>=$date_added ";
		$headline = "Daily Chart for ".$stock_symbol;
	}

	if ($chart_type=="weekly") {
		//get the last seven days
		$seven_days = 86400*7;
		$nowtime = time();
		$seven_days_back = $nowtime-$seven_days;
		$additional_sql = " and date_added>$seven_days_back ";
		$headline = "Weekly Chart for ".$stock_symbol;
	}

	if ($chart_type=="monthly") {
		$additional_sql = ""; //just deal with all numbers from the start
		$headline = "Monthly Chart for ".$stock_symbol;
	}

	$data['headline'] = $headline;
	$mysql_query = "select * from stocks_feed where stock_symbol='$stock_symbol' ".$additional_sql." order by date_added";
	$data['query'] = $this->_custom_query($mysql_query);
	$this->load->view('stock_chart_'.$chart_type, $data);
}

function get($order_by) {
$this->load->model('mdl_stocks_feed');
$query = $this->mdl_stocks_feed->get($order_by);
return $query;
}

function get_with_limit($limit, $offset, $order_by) {
$this->load->model('mdl_stocks_feed');
$query = $this->mdl_stocks_feed->get_with_limit($limit, $offset, $order_by);
return $query;
}

function get_where($id) {
$this->load->model('mdl_stocks_feed');
$query = $this->mdl_stocks_feed->get_where($id);
return $query;
}

function get_where_custom($col, $value) {
$this->load->model('mdl_stocks_feed');
$query = $this->mdl_stocks_feed->get_where_custom($col, $value);
return $query;
}

function _insert($data) {
$this->load->model('mdl_stocks_feed');
$this->mdl_stocks_feed->_insert($data);
}

function _update($id, $data) {
$this->load->model('mdl_stocks_feed');
$this->mdl_stocks_feed->_update($id, $data);
}

function _delete($id) {
$this->load->model('mdl_stocks_feed');
$this->mdl_stocks_feed->_delete($id);
}

function count_where($column, $value) {
$this->load->model('mdl_stocks_feed');
$count = $this->mdl_stocks_feed->count_where($column, $value);
return $count;
}

function get_max() {
$this->load->model('mdl_stocks_feed');
$max_id = $this->mdl_stocks_feed->get_max();
return $max_id;
}

function _custom_query($mysql_query) {
$this->load->model('mdl_stocks_feed');
$query = $this->mdl_stocks_feed->_custom_query($mysql_query);
return $query;
}


function run() {
	$this->load->module('stock_reader');
	$this->load->view('run');
}

function info() {
	$this->load->module('stock_reader');
	$data['stocks'] = $this->stock_reader->get_stocks();
	$this->load->view('info', $data);	
}


}