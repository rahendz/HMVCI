<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Telo extends Public_Controller {

	public function index()
	{
		echo 'iki telo joss';
		$this->load->theme();
	}

	public function jinguk()
	{
		echo 'iki jinguk';
		$this->load->theme();
	}
}

/* End of file telo.php */
/* Location: ./application/modules/telo/controllers/telo.php */