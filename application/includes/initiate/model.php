<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!class_exists('Base_Model')) {
	abstract class Models {}
} else {
	abstract class Models extends Base_Model {}
}

class Model extends Models {
	public $initiate = false;
	public $table = null;
	protected $_ci;
	protected $db;

	public function __construct() {
		$this->_ci =& get_instance();
		if (!isset($this->db)) {
			$this->_ci->load->database();
			$this->db =& $this->_ci->db;
		}
		$this->db->save_queries = false;
		$this->db->start_cache();
	}

	public function __call($method, $args) {
		$result = false;
		$own_method = array('get_all', 'get_all_array', 'get_one', 'get_one_array', 'count_all', 'count_results', 'is_exists');
		if (!$this->initiate) {
			$this->db
				->select($this->select)
				->from($this->table);
		}
		if (in_array($method, $own_method)) {
			// $function = '__' . $method;
			// $call = array($this, $function);
			$this->db->stop_cache();
			$result = true;
			$query = $this->db->get();
		} else {
			$this->initate = true;
			return call_user_func_array(array($this->db, $method), $args);
		}

		if ($result) {
			switch ($method) {
				case 'get_all':
					$return = $query->result();
					break;
				case 'get_all_array':
					$return = $query->result_array();
					break;
				case 'get_one':
					$return = $query->row();
					break;
				case 'get_row_array':
					$return = $query->row_array();
					break;
				case 'count_all':
					$return = $query->count_all();
					break;
				case 'count_results':
					$return = $query->count_all_results();
					break;
				case 'is_exists':
					$return = false;
					if ($query->num_rows()>0) {
						$return = true;
					}
					break;
			}
			$this->db->flush_cache();
			$this->db->close();
			return $return;
		}
		// $this->db->flush_cache();
		// $this->db->close();
		// return $return;
	}
}

/* End of file MY_Model.php */
/* Location: ./application/core/MY_Model.php */