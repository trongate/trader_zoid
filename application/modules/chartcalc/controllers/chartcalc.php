<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Chartcalc extends MX_Controller
{

function __construct() {
parent::__construct();
}

function test() {
	$this->load->module('gimme_the_price');
	$this->load->module('stocks_feed');
	$root_date = $this->stocks_feed->get_min_date();
	$checkpoints_for_day = $this->gimme_the_price->get_checkpoints_for_day($root_date);
	
	foreach($checkpoints_for_day as $unix_timestamp) {
		echo $unix_timestamp."<br>";
	}
}

//Start recording real data

//Get seven days worth of data recorded

//PHASE TWO: figure out how to compare blueprints for day

function testCOOL() {
	$stock_symbol = "SBUX";
	$start_time1 = 1429094112;
	$end_time1 = 1429105454;

	$data['stock_symbol'] = $stock_symbol;
	$data['start_time1'] = $start_time1;
	$data['end_time1'] = $end_time1;

	//$start_time2 = $this->calc_start_time2($data);
	//$end_time2 = time();
	$start_time2 = $end_time1;
	$end_time2 = $this->get_dummy_end_time2($stock_symbol);
	


	//count values for range 1
	$query1 = $this->get_vales($stock_symbol, $start_time1, $end_time1);
	$num_rows1 = $query1->num_rows();

	//count values for range 2
	$query2 = $this->get_vales($stock_symbol, $start_time2, $end_time2);
	$num_rows2 = $query2->num_rows();

	$limit = $num_rows1;

	if ($num_rows2<$num_rows1) {
		$limit = $num_rows2;
	}

	$query1 = $this->get_vales($stock_symbol, $start_time1, $end_time1, $limit);
	$query2 = $this->get_vales($stock_symbol, $start_time2, $end_time2, $limit);

	$num_rows1 = $query1->num_rows();
	$num_rows2 = $query2->num_rows();
	
	//let's all this data to the chartcalc table
	$this->_clear_table();

	unset($data);
	foreach($query1->result() as $row) {
		$data['chart_historic'] = $row->price;
		$this->_insert($data);
	}


	//now update with the second price
	unset($data);
	$update_id = 0;
	foreach($query2->result() as $row) {
		$data['chart_today'] = $row->price;
		$update_id++;
		$this->_update($update_id, $data);
	}

	redirect('chartcalc/sample');	

}

function _clear_table() {
	$mysql_query = "TRUNCATE TABLE chartcalc";
	$query = $this->_custom_query($mysql_query);
}

function get_dummy_end_time2($stock_symbol) {
	$mysql_query = "select * from stocks_feed where stock_symbol='".$stock_symbol."' order by date_added";
	$query = $this->_custom_query($mysql_query);
	foreach($query->result() as $row) {
		$date_added = $row->date_added;
	}
	return $date_added;
}

function get_vales($stock_symbol, $start_time, $end_time, $limit=NULL) {
	$mysql_query = "select * from stocks_feed where stock_symbol='".$stock_symbol."' and date_added>=$start_time and date_added<=$end_time order by date_added";

	if (is_numeric($limit)) {
		$mysql_query.= " limit 0, $limit";
	}



	$query = $this->_custom_query($mysql_query);
	return $query;
}

function calc_start_time2($data) {
	//data = start_time1 and endtime1 and stock_symbol
	$start_time1 = $data['start_time1'];
	$end_time1 = $data['end_time1'];
	$stock_symbol = $data['stock_symbol'];

	$timegap = $end_time1-$start_time1;

	$end_time2 = time();
	$start_time2 = $end_time2-$timegap;
	return $start_time2;
}

function get_current_price_range_tollerance() {
	$tollerance = 2; //the current % of flexibility for deviation at current prices
	return $tollerance;
}

function get_opening_price_old() {
	$opening_price = 44;
	return $opening_price;
}

function get_opening_price_new() {
	$opening_price = 44;
	return $opening_price;
}

function _calc_chart_diff_score() {
	$opening_price_old = $this->get_opening_price_old();
	$opening_price_new = $this->get_opening_price_new();
	$current_price_tollerance = $this->get_current_price_range_tollerance();

	//loops through the table and record the % diff for both entries
	$query = $this->get('id');

	$total_diff = 0;

	foreach($query->result() as $row) {
		$chart_historic = $row->chart_historic;
		$chart_today = $row->chart_today;
		$percent_change_one = $this->_calc_percent_change($opening_price_old, $chart_historic);
		$percent_change_two = $this->_calc_percent_change($opening_price_new, $chart_today);
		$price_change_diff = $percent_change_two-$percent_change_one;
		$price_change_diff = abs($price_change_diff);
		$total_diff = $total_diff+$price_change_diff;
	}

	$total_diff = abs($total_diff);
	return $total_diff;
}

function fix() {
	//add dummy deviations
	$opening_price = $this->get_opening_price_old();

	$query = $this->get('id');
	foreach($query->result() as $row) {
		$chart_historic = $row->chart_historic;
		$chart_today = $row->chart_today;

		$percent_change_one = $this->_calc_percent_change($opening_price, $chart_historic);
		$percent_change_two = $this->_calc_percent_change($opening_price, $chart_today);

		echo $percent_change_one." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;".$percent_change_two."<br>";
	}

	$chart_diff_score = $this->_calc_chart_diff_score();
	echo "<h1>Chart Diff Score: ".$chart_diff_score."</h1>";

}

function _calc_percent_change($opening_price, $current_price) {
	$percent_change = (($current_price/$opening_price)*100)-100;
	return $percent_change;
}

function sample() {
$query = $this->get('id');
$num_rows = $query->num_rows();
$data_block = "";
$count = 0;

//build data block
foreach($query->result() as $row) {
$count++;
$value_1 = $row->chart_historic;
$value_2 = $row->chart_today;
$data_block.= "[".$count.",  ".$value_1.", ".$value_2."]";
if ($num_rows>$count) {
$data_block.=",
";
} else {
$data_block.="
";
}
}
	$data['chart_diff_score'] = $this->_calc_chart_diff_score();
	$data['data_block'] = $data_block;
	$this->load->view('sample', $data);
}

function get($order_by) {
$this->load->model('mdl_chartcalc');
$query = $this->mdl_chartcalc->get($order_by);
return $query;
}

function get_with_limit($limit, $offset, $order_by) {
$this->load->model('mdl_chartcalc');
$query = $this->mdl_chartcalc->get_with_limit($limit, $offset, $order_by);
return $query;
}

function get_where($id) {
$this->load->model('mdl_chartcalc');
$query = $this->mdl_chartcalc->get_where($id);
return $query;
}

function get_where_custom($col, $value) {
$this->load->model('mdl_chartcalc');
$query = $this->mdl_chartcalc->get_where_custom($col, $value);
return $query;
}

function _insert($data) {
$this->load->model('mdl_chartcalc');
$this->mdl_chartcalc->_insert($data);
}

function _update($id, $data) {
$this->load->model('mdl_chartcalc');
$this->mdl_chartcalc->_update($id, $data);
}

function _delete($id) {
$this->load->model('mdl_chartcalc');
$this->mdl_chartcalc->_delete($id);
}

function count_where($column, $value) {
$this->load->model('mdl_chartcalc');
$count = $this->mdl_chartcalc->count_where($column, $value);
return $count;
}

function get_max() {
$this->load->model('mdl_chartcalc');
$max_id = $this->mdl_chartcalc->get_max();
return $max_id;
}

function _custom_query($mysql_query) {
$this->load->model('mdl_chartcalc');
$query = $this->mdl_chartcalc->_custom_query($mysql_query);
return $query;
}

}