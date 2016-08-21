<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_key extends Model {

	public function __construct() {
		parent::__construct();
		$this->load->database();
	}

	public function key_exists($key) {
		return $this->db->where(config_item('rest_key_column'), $key)->count_all_results(config_item('rest_keys_table')) > 0;
	}

}

/* End of file m_key.php */
/* Location: ./application/modules/api/models/m_key.php */