<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Stock_reader extends MX_Controller {

function get_data() {
$url="http://finance.google.com/finance/info?client=ig&q=NASDAQ:GOOG,AAPL";
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