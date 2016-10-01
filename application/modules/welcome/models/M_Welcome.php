<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

class M_Welcome extends Model {
	public $table = null;
	
	public function __construct() {
		parent::__construct();
	}

	function test() {
		$this->table = 'users';
		return $this->get_all();
	}

	function test_2(){
		$this->table = 'keys';
		return $this->get_all();
	}

	function sign_validation(){
		$model = new Model;
		$model->rules = array (
			array (
				'field' => 'username',
				'label' => 'Username',
				'rules' => 'required'
				),
			array (
				'field' => 'password',
				'label' => 'Password',
				'rules' => 'required'
				)
			);
		$model->format = array (
			'required' => 'jinguk! %s ki kudu diisi ndes'
			);
		// var_dump($model->validate());
		if ( $model->validate() ) {
			echo 'true';
		}

		else {
			var_dump($model->errors);
		}
	}
}