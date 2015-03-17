<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

class M_Welcome {
	function test() {
		$model = new Model;
		$model->table = 'ikd_fakultas';
		$model->get();
		return $model->last_query();
	}
	function test_2(){
		$model = new Model;
		$model->table = 'ikd_semester';
		$model->get();
		return $model->last_query();
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