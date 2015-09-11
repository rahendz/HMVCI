<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model {

	// private $db;
	public $table = null;
	public $native = false;
	public $return_id = false;
	public $select = null;
	public $select_max = null;
	public $select_min = null;
	public $select_avg = null;
	public $select_sum = null;
	public $distinct = false;
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
	public $limit = null;
	public $offset = null;
	public $group_by = null;
	public $order_by = null;
	public $string = null;
	public $values = array();
	public $key = null;
	public $rules = array();
	public $errors = false;
	public $format = array();
	public $exists = array();
	public $platform;
	public $version;
	public $conn_id;

	public function __construct() {
		parent::__construct();
		isset ( $this->db ) OR $this->load->database();
		$this->db->cache_off();
		$this->db->save_queries = false;
		$this->platform = $this->db->platform();
		$this->version = $this->db->version();
		$this->conn_id = $this->db->conn_id;
	}

	public function __destruct() {
		$this->db->close();
	}

	public function prefix() {
		return $this->db->dbprefix ( $this->table );
	}

	private function initiate_query ( $method ) {
		if ( ! $this->table AND ! $this->db->table_exists ( $this->prefix() ) ) {
			show_error ( 'Ups! Table not selected' );
		}
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
			elseif ( $this->distinct !== false ) {
				$this->db->distinct();
			}
		}

		// From Query
		if ( in_array ( $method, array ( 'get', 'count', 'delete', 'empty_table', 'truncate' ) ) ) {
			$this->db->from ( $this->prefix() );
		}

		// Join Query
		if ( in_array ( $method, array ( 'get' ) ) ) {
			if ( isset ( $this->join[0] ) ) {
				if ( is_array ( $this->join[0] ) ) {
					foreach ( $this->join as $j ) {
						$this->db->join ( $j[0], $j[1], ( isset ( $j[2] ) ? $j[2] : null ) );
					}
				} else {
					$this->db->join ( $this->join[0], $this->join[1], ( isset ( $this->join[2] ) ? $this->join[2] : null ) );
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
			$result = $this->get()->row();
		} else {
			$result = $this->get()->row_array();
		}
		$this->reset_query();
		return $result;
	}

	public function get_all ( $object = TRUE ) {
		if ( $object ) {
			$result = $this->get()->result();
		} else {
			$result = $this->get()->result_array();
		}
		$this->reset_query();
		return $result;
	}

	public function count() {
		$this->initiate_query ( __FUNCTION__ );
		$result = $this->db->count_all_results();
		$this->reset_query();
		return $result;
	}

	public function count_all() {
		$result = $this->db->count_all ( $this->prefix() );
		$this->reset_query();
		return $result;
	}

	public function insert() {
		$return = ! $this->return_id ? 'affected_rows' : 'insert_id';
		$this->initiate_query ( __FUNCTION__ );
		$this->db->insert ( $this->prefix(), $this->values );
		$result = $this->db->$return();
		$this->reset_query();
		return $result;
	}

	public function insert_batch() {
		if ( false !== $this->native ) {
			$insert_id = array();
			$loop = $this->values;
			foreach ( $loop as $val ) {
				$this->values = $val;
				$insert_id[] = $this->insert();
			}
			$this->reset_query();
			return $insert_id;
		}
		$this->db->trans_start();
		$this->db->insert_batch ( $this->prefix(), $this->values );
		$this->db->trans_complete();
		$result = $this->db->trans_status();
		$this->reset_query();
		return $result;
	}

	public function insert_string() {
		$result = $this->db->insert_string ( $this->prefix(), $this->values );
		$this->reset_query();
		return $result;
	}

	public function update() {
		$this->initiate_query ( __FUNCTION__ );
		$this->db->update ( $this->prefix(), $this->values );
		$result = $this->db->affected_rows();
		$this->reset_query();
		return $result;
	}

	public function update_batch() {
		if ( false !== $this->native ) {
			$affected_rows = array();
			$loop = $this->values;
			foreach ( $loop as $val ) {
				$this->values = $val;
				$affected_rows[] = $this->update();
			}
			$this->reset_query();
			return $affected_rows;
		}
		$this->db->trans_start();
		$this->db->update_batch ( $this->prefix(), $this->values, $this->key );
		$this->db->trans_complete();
		$result = $this->db->trans_status();
		$this->reset_query();
		return $result;
	}

	public function update_string() {
		$result = $this->db->update_string ( $this->prefix(), $this->values, $this->where );
		$this->reset_query();
		return $result;
	}

	public function replace() {
		$this->initiate_query ( __FUNCTION__ );
		$this->db->replace ( $this->prefix(), $this->values );
		$result = $this->db->affected_rows();
		$this->reset_query();
		return $result;
	}

	public function delete() {
		$this->initiate_query ( __FUNCTION__ );
		$result = $this->db->delete ( $this->prefix() );
		$this->reset_query();
		return $result;
	}

	public function empty_table() {
		$this->initiate_query ( __FUNCTION__ );
		$result = $this->db->empty_table ( $this->prefix() );
		$this->reset_query();
		return $result;
	}

	public function truncate() {
		$this->initiate_query ( __FUNCTION__ );
		$result = $this->db->truncate();
		$this->reset_query();
		return $result;
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
			$result = $this->db->query ( $this->string );
			$this->reset_query();
			return $result;
		}
	}

	public function simple_query() {
		if ( ! is_null ( $this->string ) ) {
			$result = $this->db->simple_query ( $this->string );
			$this->reset_query();
			return $result;
		}
	}

	public function transaction ( $state, $param = false ) {
		$method = 'trans_'.$state;
		return $this->db->$method($param);
	}

	public function call() {
		return call_user_func_array ( array (
			&$this->db, 'call_function'
			), func_get_args() );
	}

	public function dbcache ( $state, $param = array() ) {
		$method = 'cache_'.$state;
		if ( count ( $param ) > 0 ) {
			return call_user_func_array ( array ( &$this->db, $method ), $param );
		}
		return $this->db->$method();
	}

	public function validate() {
		$obj = 'validation';
		if ( ! isset ( $this->validation ) ) {
			if ( ! isset ( $this->form_validation ) ) {
				$this->load->library ( 'form_validation', $this->rules, $obj );
			} else {
				$obj = 'form_validation';
			}
		}

		$this->$obj->set_message ( $this->format );

		if ( $this->$obj->run() == false AND $this->$obj->error_string() !== '' ) {
			$this->errors['string'] = $this->$obj->error_string();
			foreach ( $this->rules as $r ) {
				$this->errors[$r['field']] = $this->$obj->error($r['field']);
			}
			return false;
		}
		return TRUE;
	}

	public function list_fields() {
		$lists = $this->db->list_fields ( $this->prefix() );
		$this->reset_query();
		return $lists;
	}

	public function data_fields() {
		$result = null;
		$query = $this->db->query ( 'SHOW COLUMNS FROM ' . $this->prefix() );
		if ( $query->num_rows() > 0 ) {
			$result = array_map ( function ( $f ) {
				$return['name'] = $f->Field;
				$return['type'] = current(explode('(', $f->Type));
				$return['length'] = end(explode('(',rtrim($f->Type,')')));
				$return['null'] = $f->Null === 'NO' ? false : true;
				$return['key'] = $f->Key === 'PRI' ? 'Primary' : ( $f->Key === 'UNI' ? 'Unique' : null );
				$return['default'] = $f->Default;
				$return['ai'] = $f->Extra === 'auto_increment' ? true : false;
				return $return;
				}, $query->result() );
		}
		$this->reset_query();
		return $result;
	}

	public function reset_query() {
		// $this->table = null;
		$this->native = false;
		$this->return_id = false;
		$this->select = null;
		$this->select_max = null;
		$this->select_min = null;
		$this->select_avg = null;
		$this->select_sum = null;
		$this->distinct = false;
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
		$this->limit = null;
		$this->offset = null;
		$this->group_by = null;
		$this->order_by = null;
		$this->string = null;
		$this->values = array();
		$this->key = null;
		$this->rules = array();
		$this->errors = false;
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