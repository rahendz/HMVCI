<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

abstract class Public_Controller extends Base_Controller {
	protected $theme_var = array('config'=>array('frontend'=>'default'));

	public function __construct() {
		parent::__construct();
	}
}