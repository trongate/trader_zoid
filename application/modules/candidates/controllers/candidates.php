<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Candidates extends MX_Controller
{

function __construct() {
parent::__construct();
}

function get_active_stocks() {
	$query = $this->get_where_custom('active', 1);
	return $query;
}

function restart() {
	$mysql_query = "delete from candidates";
	$this->_custom_query($mysql_query);

	$this->load->module('stock_reader');
	$stocks_list = $this->stock_reader->get_stocks();
	foreach ($stocks_list as $key => $value) {
		$data['stock_symbol'] = $value;
		$data['active'] = 1;
		$this->_insert($data);
	}

	echo "Successfully repopulated.<br>";
	echo anchor('dashboard', "Return To Dashboard");
}

function view_current_candidates() {
	$data['query'] = $this->get_where_custom('active', 1);
	$data['query2'] = $this->get_where_custom('active', 0);
	$this->load->view('current_candidates', $data);
}

function get($order_by) {
$this->load->model('mdl_candidates');
$query = $this->mdl_candidates->get($order_by);
return $query;
}

function get_with_limit($limit, $offset, $order_by) {
$this->load->model('mdl_candidates');
$query = $this->mdl_candidates->get_with_limit($limit, $offset, $order_by);
return $query;
}

function get_where($id) {
$this->load->model('mdl_candidates');
$query = $this->mdl_candidates->get_where($id);
return $query;
}

function get_where_custom($col, $value) {
$this->load->model('mdl_candidates');
$query = $this->mdl_candidates->get_where_custom($col, $value);
return $query;
}

function _insert($data) {
$this->load->model('mdl_candidates');
$this->mdl_candidates->_insert($data);
}

function _update($id, $data) {
$this->load->model('mdl_candidates');
$this->mdl_candidates->_update($id, $data);
}

function _delete($id) {
$this->load->model('mdl_candidates');
$this->mdl_candidates->_delete($id);
}

function count_where($column, $value) {
$this->load->model('mdl_candidates');
$count = $this->mdl_candidates->count_where($column, $value);
return $count;
}

function get_max() {
$this->load->model('mdl_candidates');
$max_id = $this->mdl_candidates->get_max();
return $max_id;
}

function _custom_query($mysql_query) {
$this->load->model('mdl_candidates');
$query = $this->mdl_candidates->_custom_query($mysql_query);
return $query;
}

}