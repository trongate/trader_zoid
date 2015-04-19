<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Stocks_feed extends MX_Controller {


function view_chart($stock_symbol) {
	$stock_symbol = str_replace("NYSE:", "", $stock_symbol);
	//echo $stock_symbol; die();
	$nowtime = time();
	$last_trade_time = $this->get_last_trade_time($stock_symbol, $nowtime);
	$this->load->module('chart_analyser');
	$keytimes = $this->chart_analyser->get_daily_key_times($last_trade_time);
	$num_entries = count($keytimes);
	$this->load->module('chart_analyser');

	
	//let's get a list of prices for the stock at this keytime
	$data_string = ""; //let's start building a data string for candlestick chart
	$count = 0;
	foreach ($keytimes as $key => $value) {
		$count++;

		$prev_stock_price_id = $this->chart_analyser->get_prev_stock_price_id($count, $stock_symbol, 'daily', $last_trade_time); 



		//get the closing price at this time
		$closing_price = $this->get_price_at_time($stock_symbol, $value);

				if ($count==1) {
					$opening_price = $closing_price;
					$highest_price = $closing_price;
					$lowest_price = $closing_price;
				} else {

					$start_time = $this->get_date_added($prev_stock_price_id);
					$end_time = $value;

					$opening_price = $this->get_price_for_id($prev_stock_price_id);
					$highest_price = $this->get_highest_price_between_two_times($stock_symbol, $start_time, $end_time);
					$lowest_price = $this->get_lowest_price_between_two_times($stock_symbol, $start_time, $end_time);
				}


				if ($count==$num_entries) {
					$additional_code = ""; //no comma on the last entry!
				} else {
					$additional_code = ",";
				}
		
		
		$data_string.= "['".$key."', $lowest_price, $opening_price, $closing_price, $highest_price]".$additional_code."
		";
	}

	$data['data_string'] = $data_string;
	$data['stock_symbol'] = $stock_symbol;
	$data['latest_price'] = $this->chart_analyser->get_price_now($stock_symbol);
	$data['headline'] = "Daily Chart For $stock_symbol (latest price: $".$data['latest_price'].")";
	$percent_move = $this->chart_analyser->get_percent_move_today($stock_symbol);

	if ($percent_move>0) {
		$color = "green";
		$info = "higher";
	}

	if ($percent_move<1) {
		$color = "red";
		$info = "lower";
	}

	if (!isset($color)) {
		$color = "black";
		$info = " (unchanged)";
	}

	$percent_move = number_format($percent_move, 2);
	$data['percent_change'] = "<h2 style='color: ".$color."'>".$percent_move."% ".$info."</h2>";
	$this->load->view('stock_chart_daily', $data);
}


function get_price_for_id($id) {
	$query = $this->get_where($id);
	foreach($query->result() as $row) {
		$price = $row->price;
	}
	return $price;	
}

function get_price_at_time($stock_symbol, $timestamp) {
	$stock_symbol = str_replace(' ', '', $stock_symbol);
	$id = $this->get_id_at_time($stock_symbol, $timestamp);
	$query = $this->get_where($id);
	foreach($query->result() as $row) {
		$price = $row->price;
	}

	if (!isset($price)) {
		$price = "";
	}
	return $price;
}

function get_id_at_time($stock_symbol, $timestamp) {
	//get the id of a record that should be used for a stock at a timestamp
	$this->load->model('mdl_stocks_feed');
	$id = $this->mdl_stocks_feed->get_id_at_time($stock_symbol, $timestamp);
	return $id;
}

function get_date_added($id) {
	$query = $this->get_where($id);
	foreach($query->result() as $row) {
		$date_added = $row->date_added;
	}
	return $date_added;	
}

function get_last_trade_time($stock_symbol, $timestamp) {
	//get a timestamp for when the last trade happened
	$id = $this->get_id_at_time($stock_symbol, $timestamp);
	$query = $this->get_where($id);
	foreach($query->result() as $row) {
		$date_added = $row->date_added;
	}
	if (!isset($date_added)) {
		$date_added = $timestamp;
	}
	return $date_added;
}

function get_highest_price_between_two_times($stock_symbol, $start_time, $end_time) {
	//the highest price reached between two points
	$this->load->model('mdl_stocks_feed');
	$price = $this->mdl_stocks_feed->get_highest_price_between_two_times($stock_symbol, $start_time, $end_time);
	return $price;
}

function get_lowest_price_between_two_times($stock_symbol, $start_time, $end_time) {
	//the lowest price reached between two points
	$this->load->model('mdl_stocks_feed');
	$price = $this->mdl_stocks_feed->get_lowest_price_between_two_times($stock_symbol, $start_time, $end_time);
	return $price;
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