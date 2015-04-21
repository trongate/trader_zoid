<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Price_movements_daily extends MX_Controller
{

function __construct() {
parent::__construct();
}

function _record_price_movements() {

	$mysql_query = "delete from price_movements_daily";
	$this->_custom_query($mysql_query);

	$this->load->module('chart_analyser');
	$stocks = Modules::run('stock_reader/get_stocks');
	foreach($stocks as $stock) {
		$data['stock_symbol'] = $stock;
		$data['percentage_change'] = $this->chart_analyser->get_percent_move_today($stock);
		$this->_insert($data);
	}
}

function get_day_type() {
	$query = $this->get('id');
	$total_rows = $query->num_rows();

	$up_stocks = 0;
	$down_stocks = 0;

	foreach($query->result() as $row) {
		$percentage_change = $row->percentage_change;
		if ($percentage_change>0) {
			$up_stocks = $up_stocks+1;
		}

		if ($percentage_change<0) {
			$down_stocks = $down_stocks+1;
		}
	}

	$target_to_reach = $total_rows*0.7; //70% of stocks must do this!
	if ($up_stocks>=$target_to_reach) {
		$day_type = "up";
	}

	if ($down_stocks>=$target_to_reach) {
		$day_type = "down";
	}

	if (!isset($day_type)) {
		$day_type = "sideways";
	}
	return $day_type;
}

function test() {
	$this->_record_price_movements();
}

function get($order_by) {
$this->load->model('mdl_price_movements_daily');
$query = $this->mdl_price_movements_daily->get($order_by);
return $query;
}

function get_with_limit($limit, $offset, $order_by) {
$this->load->model('mdl_price_movements_daily');
$query = $this->mdl_price_movements_daily->get_with_limit($limit, $offset, $order_by);
return $query;
}

function get_where($id) {
$this->load->model('mdl_price_movements_daily');
$query = $this->mdl_price_movements_daily->get_where($id);
return $query;
}

function get_where_custom($col, $value) {
$this->load->model('mdl_price_movements_daily');
$query = $this->mdl_price_movements_daily->get_where_custom($col, $value);
return $query;
}

function _insert($data) {
$this->load->model('mdl_price_movements_daily');
$this->mdl_price_movements_daily->_insert($data);
}

function _update($id, $data) {
$this->load->model('mdl_price_movements_daily');
$this->mdl_price_movements_daily->_update($id, $data);
}

function _delete($id) {
$this->load->model('mdl_price_movements_daily');
$this->mdl_price_movements_daily->_delete($id);
}

function count_where($column, $value) {
$this->load->model('mdl_price_movements_daily');
$count = $this->mdl_price_movements_daily->count_where($column, $value);
return $count;
}

function get_max() {
$this->load->model('mdl_price_movements_daily');
$max_id = $this->mdl_price_movements_daily->get_max();
return $max_id;
}

function _custom_query($mysql_query) {
$this->load->model('mdl_price_movements_daily');
$query = $this->mdl_price_movements_daily->_custom_query($mysql_query);
return $query;
}

}