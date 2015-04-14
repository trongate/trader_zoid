<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Stocks_feed extends MX_Controller { 

function _has_new_trade_taken_placeCOOL($data) {
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

function _has_new_trade_taken_place($data) {
return TRUE;
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