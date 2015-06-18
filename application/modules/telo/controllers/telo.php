<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Telo extends Public_Controller {

	public function index()
	{
		echo 'iki telo joss';
		// echo $this->load->current_controller;
		return $this->render_theme();
	}

	public function jinguk()
	{
		echo 'iki jinguk';
		return $this->render_theme();
	}
}

/* End of file telo.php */
/* Location: ./application/modules/telo/controllers/telo.php */