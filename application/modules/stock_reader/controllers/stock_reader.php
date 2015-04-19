<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Stock_reader extends MX_Controller {

function get_data() {
$url="http://finance.google.com/finance/info?client=ig&q=";

$stocks = $this->get_stocks();

$count = 0;
foreach ($stocks as $key => $value) {
	$count++;
	if ($count>1) {
		$url.=",".$value;
	} else {
		$url.=$value;
	}
}

//echo $url; die();

$info= file_get_contents($url);
$info = str_replace('// ', '', $info);
$stock_data = json_decode($info, true);

$this->load->module('stocks_feed');

foreach ($stock_data as $key => $value) {
	$count = $key+1;
	echo "COUNT: ".$count."<br>";
	echo "Stock Symbol: ".$value['t'];
	echo "<br>";

	echo "Price: ".$value['l'];
	echo "<br>";

	echo "Last Trade Time: ".$value['lt'];
	echo "<br>";

	$data['stock_symbol'] = $value['t'];
	$data['price'] = $value['l'];
	$data['last_trade'] = $value['lt'];
	$data['date_added'] = time();

	$got_new_data = $this->stocks_feed->_has_new_trade_taken_place($data);
	if ($got_new_data==TRUE) {
		$this->stocks_feed->_insert($data);
		echo "<br>INSERTED<hr>";
	} else {
		echo "<br>NO NEW DATA<hr>";
	}
}

}

function get_stocks() {
	//return an array of the stocks that we are watching
	$stocks[] = "AAPL";
	$stocks[] = "ADBE";
	$stocks[] = "AIG";
	$stocks[] = "AMZN";
	$stocks[] = "NYSE:BA";
	$stocks[] = "BAC";
	$stocks[] = "NYSE:CAT";
	$stocks[] = "CVX";
	$stocks[] = "NYSE:DIS";
	$stocks[] = "F";
	$stocks[] = "FB";
	$stocks[] = "GE";
	$stocks[] = "GOOG";
	$stocks[] = "HD";
	$stocks[] = "IBM";
	$stocks[] = "JPM";
	$stocks[] = "KO";
	$stocks[] = "NYSE:LMT";
	$stocks[] = "LNKD";
	$stocks[] = "MRK";
	$stocks[] = "MCD";
	$stocks[] = "MSFT";
	$stocks[] = "NFLX";
	$stocks[] = "NKE";
	$stocks[] = "PFE";
	$stocks[] = "PG";
	$stocks[] = "SBUX";
	$stocks[] = "SPLS";
	$stocks[] = "TWTR";
	$stocks[] = "V";
	$stocks[] = "VIA";
	$stocks[] = "WMT";
	$stocks[] = "YHOO";
	$stocks[] = "XOM";
	return $stocks;
}



}