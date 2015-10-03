<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Site_settings extends MX_Controller
{

function __construct() {
parent::__construct();
}

function get_phase_one_tollerance_percent() {
	//the range that a price needs to be within to be considered close enough to warrant phase two analysis
	$tollerance = 0.00005; //half a percent
	return $tollerance;
}

}