<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model {

	// private $db;
	public $table = NULL;
	public $native = FALSE;
	public $return_id = FALSE;
	public $select = NULL;
	public $select_max = NULL;
	public $select_min = NULL;
	public $select_avg = NULL;
	public $select_sum = NULL;
	public $distinct = FALSE;
	public $set = array();
	public $join = array();
	public $where = array();
	public $or_where = array();
	public $where_in = array();
	public $or_where_in = array();
	public $where_not_in = array();
	public $or_where_not_in = array();
	public $like = array();
	public $or_like = array();
	public $not_like = array();
	public $or_not_like = array();
	public $having = array();
	public $or_having = array();
	public $limit = NULL;
	public $offset = NULL;
	public $group_by = NULL;
	public $order_by = NULL;
	public $string = NULL;
	public $values = array();
	public $key = NULL;
	public $rules = array();
	public $errors = FALSE;
	public $format = array();
	public $exists = array();
	public $platform;
	public $version;
	public $conn_id;

	public function __construct() {
		parent::__construct();
		isset ( $this->db ) OR $this->load->database();
		$this->platform = $this->db->platform();
		$this->version = $this->db->version();
		$this->conn_id = $this->db->conn_id;
	}

	public function __destruct() {
		$this->db->save_queries = FALSE;
	}

	public function prefix ( $table_name ) {
		return $this->db->dbprefix ( $table_name );
	}

	private function initiate_query ( $method ) {
		// Select Query
		if ( in_array ( $method, array ( 'get' ) ) ) {
			if ( ! is_null ( $this->select ) ) {
				$this->db->select ( $this->select );
			}
			elseif ( ! is_null ( $this->select_max ) ) {
				$this->db->select_max ( $this->select_max );
			}
			elseif ( ! is_null ( $this->select_min ) ) {
				$this->db->select_min ( $this->select_min );
			}
			elseif ( ! is_null ( $this->select_avg ) ) {
				$this->db->select_avg ( $this->select_avg );
			}
			elseif ( ! is_null ( $this->select_sum ) ) {
				$this->db->select_sum ( $this->select_sum );
			}
			elseif ( $this->distinct !== FALSE ) {
				$this->db->distinct();
			}
		}

		// From Query
		if ( in_array ( $method, array ( 'get', 'count', 'delete', 'empty_table', 'truncate' ) ) ) {
			$this->db->from ( $this->db->dbprefix ( $this->table ) );
		}

		// Join Query
		if ( in_array ( $method, array ( 'get' ) ) ) {
			if ( isset ( $this->join[0] ) ) {
				if ( is_array ( $this->join[0] ) ) {
					foreach ( $this->join as $j ) {
						$this->db->join ( $j[0], $j[1], ( isset ( $j[2] ) ? $j[2] : NULL ) );
					}
				} else {
					$this->db->join ( $this->join[0], $this->join[1], ( isset ( $this->join[2] ) ? $this->join[2] : NULL ) );
				}
			}
		}

		// Set Query
		if ( in_array ( $method, array ( 'insert', 'update', 'replace' ) ) ) {
			if ( ! is_null ( $this->set ) AND count ( $this->set ) > 0 ) {
				$this->db->set ( $this->set );
			}
		}

		// Where Query
		if ( in_array ( $method, array ( 'get', 'count', 'update', 'delete' ) ) ) {
			if ( ! is_null ( $this->where ) AND count ( $this->where ) > 0 ) {
				$this->db->where ( $this->where );
			}

			if ( ! is_null ( $this->or_where ) AND count ( $this->or_where ) > 0 ) {
				$this->db->or_where ( $this->or_where );
			}

			if ( ! is_null ( $this->where_in ) AND count ( $this->where_in ) > 0 ) {
				$this->db->where_in ( $this->where_in );
			}

			if ( ! is_null ( $this->or_where_in ) AND count ( $this->or_where_in ) > 0 ) {
				$this->db->or_where_in ( $this->or_where_in );
			}

			if ( ! is_null ( $this->where_not_in ) AND count ( $this->where_not_in ) > 0 ) {
				$this->db->where_not_in ( $this->where_not_in );
			}

			if ( ! is_null ( $this->or_where_not_in ) AND count ( $this->or_where_not_in ) > 0 ) {
				$this->db->or_where_not_in ( $this->or_where_not_in );
			}

			if ( ! is_null ( $this->like ) AND count ( $this->like ) > 0 ) {
				$this->db->like ( $this->like );
			}

			if ( ! is_null ( $this->or_like ) AND count ( $this->or_like ) > 0 ) {
				$this->db->or_like ( $this->or_like );
			}

			if ( ! is_null ( $this->not_like ) AND count ( $this->not_like ) > 0 ) {
				$this->db->not_like ( $this->not_like );
			}

			if ( ! is_null ( $this->or_not_like ) AND count ( $this->or_not_like ) > 0 ) {
				$this->db->or_not_like ( $this->or_not_like );
			}
		}

		// Having Query
		if ( in_array ( $method, array ( 'get', 'count' ) ) ) {
			if ( ! is_null ( $this->having ) ) {
				$this->db->having ( $this->having );
			}

			if ( ! is_null ( $this->or_having ) ) {
				$this->db->or_having ( $this->or_having );
			}
		}

		// Group By Query
		if ( in_array ( $method, array ( 'get', 'count' ) ) ) {
			if ( ! is_null ( $this->group_by ) ) {
				$this->db->group_by ( $this->group_by );
			}
		}

		// Order By Query
		if ( in_array ( $method, array ( 'get', 'count' ) ) ) {
			if ( is_array ( $this->order_by ) ) {
				$this->db->order_by ( $this->order_by[0], $this->order_by[1] );
			} elseif ( ! is_null ( $this->order_by ) ) {
				$this->db->order_by ( $this->order_by );
			}
		}

		// Limit Query
		if ( in_array ( $method, array ( 'get', 'count' ) ) ) {
			if ( ! is_null ( $this->limit ) ) {
				$this->db->limit ( $this->limit, $this->offset );
			}
		}
	}

	public function set_query ( $query = array() ) {
		foreach ( $query as $var => $value ) {
			$this->$var = $value;
		}
	}

	public function last_query() {
		return $this->db->last_query();
	}

	public function get() {
		$this->initiate_query ( __FUNCTION__ );
		return $this->db->get();
	}

	public function get_one ( $object = TRUE ) {
		if ( $object ) {
			return $this->get()->row();
		} else {
			return $this->get()->row_array();
		}
	}

	public function get_all ( $object = TRUE ) {
		if ( $object ) {
			return $this->get()->result();
		} else {
			return $this->get()->result_array();
		}
	}

	public function count() {
		$this->initiate_query ( __FUNCTION__ );
		return $this->db->count_all_results();
	}

	public function count_all() {
		return $this->db->count_all ( $this->db->dbprefix ( $this->table ) );
	}

	public function insert() {
		$return = ! $this->return_id ? 'affected_rows' : 'insert_id';
		$this->initiate_query ( __FUNCTION__ );
		$this->db->insert ( $this->db->dbprefix ( $this->table ), $this->values );
		return $this->db->$return();
	}

	public function insert_batch() {
		if ( FALSE !== $this->native ) {
			$insert_id = array();
			foreach ( $this->values as $val ) {
				$this->values = $val;
				$insert_id[] = $this->insert();
			}
			return $insert_id;
		}
		$this->db->trans_start();
		$this->db->insert_batch ( $this->db->dbprefix ( $this->table ), $this->values );
		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	public function insert_string() {
		return $this->db->insert_string ( $this->db->dbprefix ( $this->table ), $this->values );
	}

	public function update() {
		$this->initiate_query ( __FUNCTION__ );
		$this->db->update ( $this->db->dbprefix ( $this->table ), $this->values );
		return $this->db->affected_rows();
	}

	public function update_batch() {
		$this->db->trans_start();
		$this->db->update_batch ( $this->db->dbprefix ( $this->table ), $this->values, $this->key );
		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	public function update_string() {
		return $this->db->update_string ( $this->db->dbprefix ( $this->table ), $this->values, $this->where );
	}

	public function replace() {
		$this->initiate_query ( __FUNCTION__ );
		$this->db->replace ( $this->db->dbprefix ( $this->table ), $this->values );
		return $this->db->affected_rows();
	}

	public function delete() {
		$this->initiate_query ( __FUNCTION__ );
		return $this->db->delete ( $this->db->dbprefix ( $this->table ) );
	}

	public function empty_table() {
		$this->initiate_query ( __FUNCTION__ );
		return $this->db->empty_table ( $this->db->dbprefix ( $this->table ) );
	}

	public function truncate() {
		$this->initiate_query ( __FUNCTION__ );
		return $this->db->truncate();
	}

	public function cache ( $trigger ) {
		$cache = $trigger.'_cache';
		$this->db->$cache();
	}

	public function flush() {
		$this->db->flush_cache();
	}

	public function query() {
		if ( ! is_null ( $this->string ) ) {
			return $this->db->query ( $this->string );
		}
	}

	public function simple_query() {
		if ( ! is_null ( $this->string ) ) {
			return $this->db->simple_query ( $this->string );
		}
	}

	public function transaction ( $state, $param = FALSE ) {
		$method = 'trans_'.$state;
		return $this->db->$method($param);
	}

	public function call() {
		return call_user_func_array ( array ( &$this->db, 'call_function' ), func_get_args() );
	}

	public function dbcache ( $state, $param = array() ) {
		$method = 'cache_'.$state;
		if ( count ( $param ) > 0 ) {
			return call_user_func_array ( array ( &$this->db, $method ), $param );
		}
		return $this->db->$method();
	}

	public function validate() {
		if ( ! isset ( $this->validation ) ) {
			$this->load->library ( 'form_validation', $this->rules, 'validation' );
		}

		$this->validation->set_message ( $this->format );

		if ( $this->validation->run() == FALSE AND $this->validation->error_string() !== '' ) {
			$this->errors['string'] = $this->validation->error_string();
			foreach ( $this->rules as $r ) {
				$this->errors[$r['field']] = $this->validation->error($r['field']);
			}
			return FALSE;
		}
		return TRUE;
	}

	public function reset_query() {
		// $this->table = NULL;
		$this->native = FALSE;
		$this->return_id = FALSE;
		$this->select = NULL;
		$this->select_max = NULL;
		$this->select_min = NULL;
		$this->select_avg = NULL;
		$this->select_sum = NULL;
		$this->distinct = FALSE;
		$this->set = array();
		$this->join = array();
		$this->where = array();
		$this->or_where = array();
		$this->where_in = array();
		$this->or_where_in = array();
		$this->where_not_in = array();
		$this->or_where_not_in = array();
		$this->like = array();
		$this->or_like = array();
		$this->not_like = array();
		$this->or_not_like = array();
		$this->having = array();
		$this->or_having = array();
		$this->limit = NULL;
		$this->offset = NULL;
		$this->group_by = NULL;
		$this->order_by = NULL;
		$this->string = NULL;
		$this->values = array();
		$this->key = NULL;
		$this->rules = array();
		$this->errors = FALSE;
		$this->format = array();
		$this->exists = array();
		$this->db->flush_cache();
	}

}

class Model extends MY_Model {
	public function __construct() {
		parent::__construct();
	}
}

/* End of file MY_Model.php */
/* Location: ./application/core/MY_Model.php */