<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Site_settings extends MX_Controller
{

function __construct() {
parent::__construct();
}

function get_nowtime() {
	$nowtime = 1429290732;
	return $nowtime;
}

function get_phase_one_tolerance_percent() {
	//the range that a price needs to be within to be considered close enough to warrant phase two analysis
	$tolerance = 0.5; // (note 100 = 100 percent, 50 = 50% etc)
	$tolerance = $tolerance+0.00000001;
	return $tolerance;
}

function get_checkpoints_interval() {
	//the time gap between each checkpoint for analysis phase two
	$gap = 300; //five minutes
	//$gap = 3600; //one hour
	return $gap;
}

}