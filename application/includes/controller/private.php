<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

abstract class Private_Controller extends Base_Controller {

	// protected $theme_var = array('config'=>array('backend'=>'default'));

	public function __construct() {
		parent::__construct();
		// $this->user_priviledge();
	}

	public function __render_theme($vars = array(), $return = false) {
		$this->theme_var['config']['backend'] = $this->theme;
		parent::__render_theme($vars, $return);
	}

	protected function __user_priviledge() {
		$settings =& $this->model('m_settings');
		if ( $this->router->fetch_class() !== 'inheritance' AND ! $settings->is_priviledge_approved() ) {
			show_error ( 'You don\'t have permission to access this page. Please contact your Administrator for detail information.' );
		}
	}
}