<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

abstract class Public_Controller extends Base_Controller {
	// protected $theme_var = array('config'=>array('frontend'=>'default'));
	// protected $theme;

	public function __construct() {
		parent::__construct();
	}

	public function render_theme($vars = array(), $return = false) {
		$this->theme_var['config']['frontend'] = $this->theme;
		parent::render_theme($vars, $return);
	}
}