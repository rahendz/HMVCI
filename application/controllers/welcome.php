<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends Public_Controller {

	public function __construct() {
		parent::__construct();
	}

	public function index()
	{
		$this->enqueue_style = array (
			'style' => array ( 'style.css', array(), '1.0.0' )
			);

		$this->enqueue_script = array (
			'meneh' => array ( 'meneh.js',array(),'3.2.0', TRUE )
			);

		$this->content_theme = 'welcome_message';
		return $this->render_theme();
	}

	public function testdb() {
		$this->load->database();
		echo_r($this->db->list_tables());
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */