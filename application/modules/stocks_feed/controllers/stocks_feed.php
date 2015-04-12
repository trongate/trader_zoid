<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Stocks_feed extends MX_Controller { 

function test() {
	echo "here we go";
	$data['stock_symbol'] = "zzz";
	$data['price'] = 88.88;
	$data['last_trade'] = "bla";
	$data['date_added'] = 123456789;
	$this->_insert($data);
	echo "done";
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


}