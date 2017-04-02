<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends API_Controller {

	public function index_get(){
		$this->response(array('type'=>'public', 'auth'=>$this->get('auth')));
	}

	public function index_get_private() {
		$this->response(array('type'=>'private'));
	}

}

/* End of file test.php */
/* Location: ./application/modules/api/controllers/test.php */