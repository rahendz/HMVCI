<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

abstract class Public_Controller extends MY_Controller {
	protected $data_theme = array();
	protected $content_theme = NULL;
	protected $enqueue_style = array();
	protected $enqueue_script = array();

	public function __construct() {
		parent::__construct();
		// $this = parent::$this;
	}

	protected function render_theme ( $vars = array(), $return = FALSE ) {
		$this->load->enqueue_style = $this->enqueue_style;
		$this->load->enqueue_script = $this->enqueue_script;
		$this->load->views_data = $this->data_theme;
		$this->load->views_file = $this->content_theme;
		return $this->load->theme ( $vars, $return );
	}
}