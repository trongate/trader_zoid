<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Chart_analyser extends MX_Controller
{

//A module dedicated to the analysis of charts

function __construct() {
parent::__construct();
}

function test() {
	$stock_symbol = "GOOG";
	$timestamp = 1429390400;
	$keytimes = $this->get_daily_key_times($timestamp);
	foreach ($keytimes as $key => $value) {
		echo "key of $key has value of $value<br>";
	}
}

function get_prev_stock_price_id($array_count, $stock_symbol, $chart_type, $timestamp) {
	//get the ID of the previous price point, given number in data array
	$target_count = $array_count-1;

	$count = 0;
	$keytimes = $this->get_daily_key_times($timestamp);
	foreach ($keytimes as $key => $value) {
		$count++;
			if (($target_count==$count) && ($target_count>0)) {
				$date_added = $value;
				$this->load->module('stocks_feed');
				$id = $this->stocks_feed->get_id_at_time($stock_symbol, $value);
				return $id;
			}
	}

	if (!isset($id)) {
		$id = 0;
		return $id;
	}
}


function get_daily_key_times($timestamp) {
	//return an array of timestamps which represent the points on our daily chart
	$start_of_day = $this->get_start_of_day($timestamp);
	$this->load->module('timedate');

	$one_hour = 3600;
	$hours_ahead = $this->get_hours_ahead_of_new_york();
	$seconds_to_add = $one_hour*$hours_ahead;

	$hour = 9;
	$minute = 30;
	$second = 0;
	$day = date('j', $start_of_day);
	$month = date('n', $start_of_day);
	$year = date('Y', $start_of_day);
	$key_time = $this->timedate->maketime($hour, $minute, $second, $month, $day, $year);
	$key_time = $key_time+$seconds_to_add;
	$key_times['9:30'] = $key_time;

	$hour = 10;
	$minute = 0;
	$key_time = $this->timedate->maketime($hour, $minute, $second, $month, $day, $year);
	$key_time = $key_time+$seconds_to_add;
	$key_times['10:00'] = $key_time;

	$hour = 10;
	$minute = 30;
	$key_time = $this->timedate->maketime($hour, $minute, $second, $month, $day, $year);
	$key_time = $key_time+$seconds_to_add;
	$key_times['10:30'] = $key_time;

	$hour = 11;
	$minute = 0;
	$key_time = $this->timedate->maketime($hour, $minute, $second, $month, $day, $year);
	$key_time = $key_time+$seconds_to_add;
	$key_times['11:00'] = $key_time;

	$hour = 11;
	$minute = 30;
	$key_time = $this->timedate->maketime($hour, $minute, $second, $month, $day, $year);
	$key_time = $key_time+$seconds_to_add;
	$key_times['11:30'] = $key_time;

	$hour = 12;
	$minute = 0;
	$key_time = $this->timedate->maketime($hour, $minute, $second, $month, $day, $year);
	$key_time = $key_time+$seconds_to_add;
	$key_times['12:00'] = $key_time;

	$hour = 13;
	$minute = 0;
	$key_time = $this->timedate->maketime($hour, $minute, $second, $month, $day, $year);
	$key_time = $key_time+$seconds_to_add;
	$key_times['1:00'] = $key_time;

	$hour = 13;
	$minute = 30;
	$key_time = $this->timedate->maketime($hour, $minute, $second, $month, $day, $year);
	$key_time = $key_time+$seconds_to_add;
	$key_times['1:30'] = $key_time;

	$hour = 14;
	$minute = 0;
	$key_time = $this->timedate->maketime($hour, $minute, $second, $month, $day, $year);
	$key_time = $key_time+$seconds_to_add;
	$key_times['2:00'] = $key_time;

	$hour = 14;
	$minute = 30;
	$key_time = $this->timedate->maketime($hour, $minute, $second, $month, $day, $year);
	$key_time = $key_time+$seconds_to_add;
	$key_times['2:30'] = $key_time;

	$hour = 15;
	$minute = 0;
	$key_time = $this->timedate->maketime($hour, $minute, $second, $month, $day, $year);
	$key_time = $key_time+$seconds_to_add;
	$key_times['3:00'] = $key_time;

	$hour = 15;
	$minute = 30;
	$key_time = $this->timedate->maketime($hour, $minute, $second, $month, $day, $year);
	$key_time = $key_time+$seconds_to_add;
	$key_times['3:30'] = $key_time;

	$hour = 16;
	$minute = 0;
	$key_time = $this->timedate->maketime($hour, $minute, $second, $month, $day, $year);
	$key_time = $key_time+$seconds_to_add;
	$key_times['4:00'] = $key_time;

	return $key_times;
}

function get_hours_ahead_of_new_york() {
	$hours_ahead = 6; //how many hours the UK is ahead of New York
	return $hours_ahead;
}

function get_start_of_day($timestamp) {
	//return the start of the day as a timestamp
	//(I'm in the UK and ahead of New York, so I'll count day start as 6am)
	$hour = 6;
	$minute = 0;
	$second = 0;
	$day = date('j', $timestamp);
	$month = date('n', $timestamp);
	$year = date('Y', $timestamp);

	$this->load->module('timedate');
	$start_of_day = $this->timedate->maketime($hour, $minute, $second, $month, $day, $year);
	return $start_of_day;
}

function get_end_of_day($timestamp) {
	//return the start of the day as a timestamp
	//(I'm in the UK and ahead of New York, so I'll count day start as 6am)
	$hour = 23;
	$minute = 59;
	$second = 59;
	$day = date('j', $timestamp);
	$month = date('n', $timestamp);
	$year = date('Y', $timestamp);

	$this->load->module('timedate');
	$end_of_day = $this->timedate->maketime($hour, $minute, $second, $month, $day, $year);
	return $end_of_day;
}

function get_price_at_time($stock_symbol, $timestamp) {
	//attempts to get price at a time.  If no precise time available then
	//...will default back to nearest available time
	$this->load->module('stocks_feed');
	$price = $this->stocks_feed->get_price_at_time($stock_symbol, $timestamp);
	return $price;
}

function get_start_of_day_price($stock_symbol, $timestamp) {
	//returns price at start of day/trading session
	$start_of_day = $this->get_start_of_day($timestamp);
	$start_of_day_price = $this->get_price_at_time($stock_symbol, $start_of_day);
	return $start_of_day_price;
}

function get_end_of_day_price($stock_symbol, $timestamp) {
	//returns price at end of day
	$end_of_day = $this->get_end_of_day($timestamp);
	$end_of_day_price = $this->get_price_at_time($stock_symbol, $end_of_day);
	return $end_of_day_price;
}

function get_price_now($stock_symbol) {
	//return the most recent stock price
	$timestamp = time();
	$price = $this->get_price_at_time($stock_symbol, $timestamp);
	return $price;
}

function get_percent_move_today($stock_symbol) {
	//how much has this stock moved by today?
	$nowtime = time();
	$this->load->module('stocks_feed');
	$last_trade_time = $this->stocks_feed->get_last_trade_time($stock_symbol, $nowtime);
	$start_of_day = $this->get_start_of_day($last_trade_time);

	if ((($last_trade_time>0) && ($start_of_day>0)) && (($last_trade_time>0) && ($start_of_day>0))) {
		//figure out the percent change
		$opening_price = $this->get_start_of_day_price($stock_symbol, $last_trade_time);

		if ($opening_price==0) {
			$opening_price = 1;
		}

		$closing_price = $this->get_price_now($stock_symbol);
		$percent_change = $closing_price/$opening_price;

		if ($percent_change<1) {
			//price has moved DOWN
			$percent_change = -(1-$percent_change)*100;
		} else {
			$percent_change = ($percent_change-1)*100;
		}

	} else {
		//it ain't happening
		$percent_change = "";
	}
	return $percent_change;
}

function get_highest_price_between_two_times($stock_symbol, $start_time, $end_time) {
	//the highest price reached between two points
	$this->load->module('stocks_feed');
	$price = $this->stocks_feed->get_highest_price_between_two_times($stock_symbol, $start_time, $end_time);
	return $price;
}

function get_lowest_price_between_two_times($stock_symbol, $start_time, $end_time) {
	//the lowest price reached between two points
	$this->load->module('stocks_feed');
	$price = $this->stocks_feed->get_lowest_price_between_two_times($stock_symbol, $start_time, $end_time);
	return $price;
}



































function get_start_of_day_prices($stock_symbol) {
	//get the IDs records which represent the start of a new trading day
	$current_day = "";
	$this->load->module('stocks_feed');
	$query = $this->stocks_feed->get('date_added');
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



function get_prices_for_candle($id, $chart_type) {
	//return low, open, close, high for candlestick chart
	//chart type can be 'daily', 'monthly' or 'weekly'
	$this->load->module('stocks_feed');
	$query = $this->stocks_feed->get_where($id);
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





}