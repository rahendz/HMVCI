<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Basic extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/basic
	 *	- or -
	 * 		http://example.com/index.php/basic/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/basic/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->view('basic_message');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */