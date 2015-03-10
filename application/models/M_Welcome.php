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
}