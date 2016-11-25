<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

abstract class Api_Controller extends Rest_Controller {

	public function __construct() {
		parent::__construct();
	}

}