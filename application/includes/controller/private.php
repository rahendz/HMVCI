<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

abstract class Private_Controller extends Base_Controller {

	protected $theme = 'admin';
	protected $theme_part = NULL;
	protected $data_theme = array();
	protected $content_theme = NULL;
	protected $enqueue_style = array();
	protected $enqueue_script = array();

	public function __construct() {
		parent::__construct();
		// $this->user_priviledge();
	}

	protected function render_theme ( $vars = array(), $return = FALSE ) {
		$this->load->theme_config['backend'] = $this->theme;
		$this->load->enqueue_style = $this->enqueue_style;
		$this->load->enqueue_script = $this->enqueue_script;
		$this->load->views_data = $this->data_theme;
		$this->load->views_file = $this->content_theme;
		$this->load->theme_part = $this->theme_part;
		return $this->load->theme ( $vars, $return );
	}

	protected function views ( $path, $vars = array(), $return = FALSE ) {
		return $this->load->view ( $path, $vars, $return );
	}

	protected function user_priviledge() {
		$settings =& $this->model('m_settings');
		if ( $this->router->fetch_class() !== 'inheritance' AND ! $settings->is_priviledge_approved() ) {
			show_error ( 'You don\'t have permission to access this page. Please contact your Administrator for detail information.' );
		}
	}
}