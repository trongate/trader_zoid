<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Auto_comments extends MX_Controller
{

function __construct() {
parent::__construct();
}

function view() {
	$this->load->module('timedate');
	$query = $this->get('id desc');
	foreach($query->result() as $row) {

		if ($row->comment=="Phase one analysis started.") {
			$row_colour = "yellow";
		} else {
			$row_colour = "white";
		}

		echo "<div style='margin: 12px; background-color: ".$row_colour.";'>";
		$nice_date = $this->timedate->get_nice_date($row->date_created, 'full');
		echo $nice_date."<br>";
		echo nl2br($row->comment);
		echo "<hr>";
		echo "</div>";
	}
}

function _insert_comment($comment) {
	$data['date_created'] = time();
	$data['comment'] = $comment;
	$this->_insert($data);
}

function get($order_by) {
$this->load->model('mdl_auto_comments');
$query = $this->mdl_auto_comments->get($order_by);
return $query;
}

function get_with_limit($limit, $offset, $order_by) {
$this->load->model('mdl_auto_comments');
$query = $this->mdl_auto_comments->get_with_limit($limit, $offset, $order_by);
return $query;
}

function get_where($id) {
$this->load->model('mdl_auto_comments');
$query = $this->mdl_auto_comments->get_where($id);
return $query;
}

function get_where_custom($col, $value) {
$this->load->model('mdl_auto_comments');
$query = $this->mdl_auto_comments->get_where_custom($col, $value);
return $query;
}

function _insert($data) {
$this->load->model('mdl_auto_comments');
$this->mdl_auto_comments->_insert($data);
}

function _update($id, $data) {
$this->load->model('mdl_auto_comments');
$this->mdl_auto_comments->_update($id, $data);
}

function _delete($id) {
$this->load->model('mdl_auto_comments');
$this->mdl_auto_comments->_delete($id);
}

function count_where($column, $value) {
$this->load->model('mdl_auto_comments');
$count = $this->mdl_auto_comments->count_where($column, $value);
return $count;
}

function get_max() {
$this->load->model('mdl_auto_comments');
$max_id = $this->mdl_auto_comments->get_max();
return $max_id;
}

function _custom_query($mysql_query) {
$this->load->model('mdl_auto_comments');
$query = $this->mdl_auto_comments->_custom_query($mysql_query);
return $query;
}

}