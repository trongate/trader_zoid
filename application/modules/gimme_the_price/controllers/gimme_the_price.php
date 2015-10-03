<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Gimme_the_price extends MX_Controller
{

function __construct() {
parent::__construct();
}

function get_percent_to_gamble() {
	$percent = 40;
	return $percent;
}

function get_win_back_multiplier() {
	//the amount you get back if you win
	$win_back_multiplier = 1.7;
	return $win_back_multiplier;
}

function get_lose_multiplier() {
	$lose_multiplier = 0.15;
	return $lose_multiplier;
}

function generate_trade_result() {
    $characters = 'WWL';
    $length = 1;
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

function simulate() {
	$kitty = 40;

	echo "<h1>Start Kitty: ".$kitty."</h1>";

	$trade_count = 0;
	for ($i=0; $i < 1000; $i++) { 
		$trade_count++;
		$result = $this->generate_trade_result(); //returns W or L
		$kitty = $this->_calc_new_kitty_amount($kitty, $result);


		if ($result=="L") {
			$result_desc = "<span style='color: red;'>LOSS</span>";
		} else {
			$result_desc = "<span style='color: green;'>WIN</span>";
		}

		$kitty_desc = number_format($kitty, 2);
		echo "<h2>Trade: ".$trade_count." | Result Was ".$result_desc." | Kitty: &pound;$kitty_desc</h2>";	

		if ($kitty<10) {
			echo "<div style='top: 20px; left: 500px; position: fixed;'><h1 style='font-size: 7em; color: red;'>BUST<br>Trade Count: ".$trade_count."</h1></div>"; die();
		}

		if ($kitty>=1000000) {
			echo "<div style='top: 20px; left: 500px; position: fixed;'><h1 style='font-size: 5em; color: green;'>YOU WIN<br>CONGRATULATIONS!!! <br>Trade Count: ".$trade_count."</h1></div>"; die();
		}
	}
}

function _calc_new_kitty_amount($kitty, $result) {

	echo "before bet, kitty was $kitty ";

	$percent_to_gamble = $this->get_percent_to_gamble();
	$gamble_amount = ($percent_to_gamble/100)*$kitty;
	$kitty = $kitty-$gamble_amount;

	$win_back_multiplier = $this->get_win_back_multiplier();

	if ($result=="W") {
		$new_kitty_amount = ($gamble_amount*$win_back_multiplier)+$kitty;
	} else {
		$new_kitty_amount = $kitty;
		$lose_multiplier = $this->get_lose_multiplier();
		$money_back = $gamble_amount*$lose_multiplier;

		$new_kitty_amount = $kitty+$money_back;
	}

	return $new_kitty_amount;
}

function test() {
	echo "hello";
}

function get_num_required_checkpoints() {
	//how many checkpoints do we require from the start time?
	$day_depth = $this->get_day_depth();
	$interval_gap = $this->get_checkpoints_interval();
	$num_checkpoints = $day_depth/$interval_gap;
	return $num_checkpoints;
}

function get_day_depth() {
	//how deep into the day are we going to test up to?
	$depth = 10800; //three hours
	return $depth;
}

function get_checkpoints_interval() {
	//the time gap between each price check
	$interval = 300;
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

//THE FUNCITONS BELOW ARE SUPER COOL: USE THEM!
function test_nice_too() {

	$this->load->module('stocks_feed');
	$timestamp1 = $this->stocks_feed->get_min_date();
	$timestamp2 = time();

	$this->load->module('timedate');
	$days = $this->timedate->get_days_from_range($timestamp1, $timestamp2);
	echo $days;
}

function testYEAH() {
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

function test_nice() {
	$nowtime = time();
	$stock_symbol = "AIG";
	$last_price = $this->get_price($stock_symbol, $nowtime);
	echo "The last price for $stock_symbol was $last_price";

	$this->load->module('timedate');
	$start_of_day = $this->timedate->get_start_of_day_as_timestamp($nowtime);

	echo "<br>Now is $nowtime<br>";
	echo "Dayst is $start_of_day";
}


function get_price($stock_symbol, $unix_timestamp) {

	$this->load->module('stocks_feed');

	$mysql_query = "select max(id) as target_id from stocks_feed where stock_symbol='$stock_symbol' and date_added<$unix_timestamp";
	$query = $this->stocks_feed->_custom_query($mysql_query);
	foreach($query->result() as $row) {
		$target_id = $row->target_id;
	}

	if (!isset($target_id)) {
		$price = 0;
		return $price;
	}

	$query = $this->stocks_feed->get_where($target_id);
	foreach($query->result() as $row) {
		$price = $row->price;
	}

	return $price;
}

}