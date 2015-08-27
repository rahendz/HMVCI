<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// echo 'class modules/telo loaded<br/>';
class Telo extends Public_Controller {

	public function index()
	{
		echo_r(list_tables());
		return $this->render_theme();
	}

	public function jinguk()
	{
		echo 'iki jinguk';
	}
}

/* End of file telo.php */
/* Location: ./application/modules/telo/controllers/telo.php */