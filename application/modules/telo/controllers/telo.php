<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Telo extends Public_Controller {

	public function index()
	{
		return $this->render_theme();
	}

	public function jinguk()
	{
		echo 'iki jinguk';
	}
}

/* End of file telo.php */
/* Location: ./application/modules/telo/controllers/telo.php */