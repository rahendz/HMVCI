<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

class M_Welcome extends Model {
	public $table = null;

	public function __construct() {
		parent::__construct();
	}

	function test() {
		$this->table = 'users';
		return $this->get_one();
	}

	function test_again() {
		$this->db->from('userlog');
		$get = $this->db->get();
		return $get->result();
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

	function pagination_total_rows() {
		$this->table = 'products';
		if (is_get('search')) {
			$this->where('merk', is_get('search',true));
		}
		return $this->count_all_results();
	}

	function pagination_data_each($limit,$offset) {
		$this->table = 'products';
		if (is_get('search')) {
			$this->where('merk', is_get('search',true));
		}
		$this->limit($limit,$offset);
		return $this->get_all();
	}
}