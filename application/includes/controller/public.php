<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

abstract class Public_Controller extends Base_Controller {

	// protected $theme = 'default';
	// protected $theme_part = null;
	// protected $data_theme = array();
	// protected $content_theme = null;
	// protected $enqueue_style = array();
	// protected $enqueue_script = array();

	public function __construct() {
		parent::__construct();
	}
}