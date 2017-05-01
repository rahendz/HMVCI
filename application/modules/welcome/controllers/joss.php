<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Joss extends CI_Controller {

	public function index()
	{
		// __r($this->load->get_component('router'));
		__r(current ( debug_backtrace() ));
	}

}

/* End of file joss.php */
/* Location: ./application/modules/welcome/controllers/joss.php */