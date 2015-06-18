<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

abstract class Private_Controller extends MY_Controller {

	protected $theme_name = 'default';
	protected $theme_part = null;
	protected $data_theme = array();
	protected $content_theme = null;
	protected $enqueue_style = array();
	protected $enqueue_script = array();

	public function __construct() {
		parent::__construct();
	}

	protected function render_theme ( $vars = array(), $return = FALSE ) {
		$this->load->theme_config['backend'] = $this->theme_name;
		$this->load->enqueue_style = $this->enqueue_style;
		$this->load->enqueue_script = $this->enqueue_script;
		$this->load->views_data = $this->data_theme;
		$this->load->views_file = $this->content_theme;
		return $this->load->theme ( $vars, $return );
	}

	protected function views ( $path, $vars = array(), $return = FALSE ) {
		return $this->load->view ( $path, $vars, $return );
	}
}