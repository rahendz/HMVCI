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
	public $result = false;
	protected $_ci;
	protected $db;

	public function __construct() {
		$this->_ci =& get_instance();
		if (!isset($this->db)) {
			$this->_ci->load->database();
			$this->db =& $this->_ci->db;
			unset($this->_ci->db);
		}
		$this->db->save_queries = false;
		$this->__recall('start_cache');
	}

	public function __call ($method, $args) {
		// $result = false;
		$own_method = array('get_all', 'get_all_array', 'get_one', 'get_one_array', 'count_all', 'count_results', 'is_exists');
		if (!$this->initiate) {
			$this->initiate = true;
			if ($method==='select') {
				// $this->db->select($args);
				$this->__recall('select',$args);
			}
			// $this->db->from($this->table);
			$this->__recall('from',array($this->table));
		}
		if (in_array($method, $own_method)) {
			// $this->db->stop_cache();
			$this->__recall('stop_cache');
			$this->result = true;
			$this->initiate = false;
			// $query = $this->db->get();
			$query = $this->__recall('get');
		} elseif (!in_array($method, $own_method) && $method!=='select') {
			// $return = call_user_func_array(array($this->db, $method), $args);
			$return = $this->__recall($method, $args);
		}

		if ($this->result!==false) {
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
					// $return = $this->db->count_all();
					$return = $this->__recall('count_all');
					break;
				case 'count_results':
					// $return = $query->count_all_results();
					$return = $this->__recall('count_all_results');
					break;
				case 'is_exists':
					$return = new stdClass();
					$return->status = false;
					if ($query->num_rows()>0) {
						$return->status = true;
						$return->get_all = $query->result();
						$return->get_one = $query->row();
					}
					break;
			}
			// $this->db->flush_cache();
			$this->__recall('flush_cache');
		}

		if (isset($return)) {
			return $return;
		}
	}

	private function __recall ($method, $args=array()) {
		return call_user_func_array(array($this->db, $method), $args);
	}
}

/* End of file MY_Model.php */
/* Location: ./application/core/MY_Model.php */