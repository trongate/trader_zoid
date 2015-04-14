<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Stock_reader extends MX_Controller {

function get_data() {
$url="http://finance.google.com/finance/info?client=ig&q=NYSE:";

$nasdaq_stocks[] = "AAPL";
$nasdaq_stocks[] = "ADBE";
$nasdaq_stocks[] = "AIG";
$nasdaq_stocks[] = "AMZN";
$nasdaq_stocks[] = "NYSE:BA";
$nasdaq_stocks[] = "BAC";
$nasdaq_stocks[] = "NYSE:CAT";
$nasdaq_stocks[] = "CVX";
$nasdaq_stocks[] = "NYSE:DIS";
$nasdaq_stocks[] = "F";
$nasdaq_stocks[] = "FB";
$nasdaq_stocks[] = "GE";
$nasdaq_stocks[] = "GOOG";
$nasdaq_stocks[] = "HD";
$nasdaq_stocks[] = "IBM";
$nasdaq_stocks[] = "JPM";
$nasdaq_stocks[] = "KO";
$nasdaq_stocks[] = "LMT";
$nasdaq_stocks[] = "LNKD";
$nasdaq_stocks[] = "MRK";
$nasdaq_stocks[] = "MCD";
$nasdaq_stocks[] = "MSFT";
$nasdaq_stocks[] = "NFLX";
$nasdaq_stocks[] = "NKE";
$nasdaq_stocks[] = "PFE";
$nasdaq_stocks[] = "PG";
$nasdaq_stocks[] = "SBUX";
$nasdaq_stocks[] = "SPLS";
$nasdaq_stocks[] = "TWTR";
$nasdaq_stocks[] = "V";
$nasdaq_stocks[] = "VIA";
$nasdaq_stocks[] = "WMT";
$nasdaq_stocks[] = "YHOO";
$nasdaq_stocks[] = "XOM";

$count = 0;
foreach ($nasdaq_stocks as $key => $value) {
	$count++;
	if ($count>1) {
		$url.=",".$value;
	} else {
		$url.=$value;
	}
}

echo $url; //die();

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
	$this->stocks_feed->_insert($data);
	echo "<br>INSERTED<hr>";
}

}

}