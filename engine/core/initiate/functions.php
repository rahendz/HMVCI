<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

// DEPRECATED
if ( ! function_exists ( 'load_controller' ) ) {
	function &load_controller ( $controller ) {
		// $_ci =& get_instance();
		$name = false;

		if ( file_exists ( $controllers_file = APPPATH . 'controllers/' . $controller . EXT ) ) {
			if ( class_exists ( $controller ) === false ) {
				$name = $controller;
				require_once $controllers_file;
			}
		}

		if ( $modules_locations = config_item ( 'modules_locations' ) ) {
			if ( strpos ( $controller, '/' ) !== false )
				list ( $module, $controller ) = explode ( '/', $controller );
			$controllers_file = current ( $modules_locations ) . ( isset ( $module ) ? $module : $controller ) . '/controllers/'. $controller . EXT;
			// $controllers_file = key ( $_ci->config->item ( 'modules_locations' ) ) . 'controllers/' . $controller . EXT;
			if ( file_exists ( $controllers_file ) )
			{
				$name = $controller;
				if ( class_exists ( $name ) === false )
				{
					include_once $controllers_file;
				}
			}
		}

		if ( $name === false ) {
			__die ( 'Unable to locate the specified class: ' . $controllers_file, 404 );
		}

		$_controllers = new $name();
		return $_controllers;
	}
}

if ( ! function_exists ( 'on_input' ) ) :
	function on_input ( $name, $type = 'get' ) {
		if ( $type === 'get' AND isset ( $_GET[$name] ) ) {
			return $name;
		} elseif ( $type === 'post' AND isset ( $_POST[$name] ) ) {
			return $name;
		} elseif ( $type === 'both' AND ( isset ( $_GET[$name] ) OR isset ( $_POST[$name] ) ) ) {
			return $name;
		} else {
			return false;
		}
	}
endif;

// Basic Helper
if ( ! function_exists ( 'ci_version' ) ) {
	function ci_version ( $operator = null, $version = null ) {
		if ( is_null ( $version ) ) {
			return CI_VERSION;
		}

		switch ( $operator ) {
			default:
			case '<':
				return CI_VERSION < $version ? true : false;
				break;

			case '>':
				return CI_VERSION > $version ? true : false;
				break;

			case '<=':
				return CI_VERSION <= $version ? true : false;
				break;

			case '>=':
				return CI_VERSION >= $version ? true : false;
				break;

			case '=':
				return CI_VERSION == $version ? true : false;
				break;
		}
	}
}

// Loader Helper
if ( ! function_exists ( '_library' ) ) {
	function &_library ( $library ) {
		$_ci =& get_instance();
		if ( ! isset ( $_ci->$library ) ) {
			$_ci->load->library ( $library );
		}
		return $_ci->$library;
	}
}

if ( ! function_exists ( '_model' ) ) {
	function &_model ( $model ) {
		$_ci =& get_instance();
		if ( ! isset ( $_ci->$model ) ) {
			$_ci->load->model ( $model );
		}
		return $_ci->$model;
	}
}

if ( ! function_exists ( '_view_load' ) ) {
	function _view_load ( $view_path, $vars = array() ) {
		$_ci =& get_instance();
		return $_ci->load->view ( $view_path, $vars );
	}
}

if ( ! function_exists ( '_view_get' ) ) {
	function _view_get ( $view_path, $vars = array() ) {
		$_ci =& get_instance();
		return $_ci->load->view ( $view_path, $vars, true );
	}
}

// Path Helper
if ( ! function_exists ( 'get_current_path' ) ) {
	function get_current_path ( $type = null, $realpath = false ) {
		$_ci =& get_instance();
		$debug = current ( debug_backtrace() );
		$current_path = str_replace ( array ( FCPATH, '\\' ), array ( '', '/' ), $debug['file'] );

		if ( 'views' == $type AND strpos ( $current_path, $type ) !== false ) {
			return $current_path;
		}

		elseif ( 'views' == $type AND strpos ( $current_path, $type ) === false ) {
			return '<small>It\'s not a views file!</small>';
		}

		elseif ( 'controllers' == $type ) {
			return $_ci->load->current_controller;
		}

		else {
			return $realpath ? realpath ( $current_path ) : $current_path;
		}
	}
}

// URL Helper
if ( ! function_exists ( 'redirect' ) ) {
	function redirect ( $uri = '/', $method = 'location', $http_response_code = 302 ) {
		$_ci =& get_instance();
		$type = $uri === '/' ? 'base_url' : 'site_url';
		$redirect_to = '?redirect=' . urlencode ( $_ci->config->site_url ( $_ci->uri->uri_string() ) );

		if ( ! preg_match ( '#^https?://#i', $uri ) ) {
			$uri = $_ci->config->$type ( $uri );
		}

		switch ( $method ) {
			case 'refresh' :
				header ( "Refresh:0;url=" . $uri );
				break;

			case 'meta' :
				return '<meta http-equiv="refresh" content="' . $http_response_code . '; url=' . $uri . '">' . "\n";
				break;

			case 'redirect' :
				header ( "Location: " . $uri . $redirect_to, true, $http_response_code );
				break;

			default :
				header ( "Location: " . $uri, true, $http_response_code );
				break;
		}
		exit;
	}
}

if ( ! function_exists ( 'current_url_string' ) ) {
	function current_url_string() {
		$_ci =& get_instance();
		$query_string = ( isset ( $_SERVER['QUERY_STRING'] ) AND ! empty ( $_SERVER['QUERY_STRING'] ) ) ?
			'?' . $_SERVER['QUERY_STRING'] : null;
		return $_ci->uri->uri_string() . $query_string;
	}
}

if ( ! function_exists ( 'site_url' ) ) {
	function site_url ( $path = null ) {
		$_ci =& get_instance();
		if ( is_null ( $path ) ) {
			$url = null;
		} elseif ( ! $path ) {
			$url = current_url_string();
		} else {
			$url = $path;
		}
		return $_ci->config->site_url ( $url );
	}
}

if ( ! function_exists ( 'base_url' ) ) {
	function base_url ( $path = null ) {
		$_ci =& get_instance();
		return $_ci->config->base_url ( $path );
	}
}

// Data Input Helper
if ( ! function_exists ( 'get_input' ) ) {
	function get_input ( $type = null, $name = null ) {
		if ( is_null ( $type ) ) {
			parse_str ( $_SERVER['QUERY_STRING'], $parse );
			return is_null ( key ( $parse ) ) ? false : key ( $parse );
		}
		$_ci =& get_instance();
		$type = strtolower ( $type );
		$func = array (
			'post'		=> 'post',
			'get'		=> 'get',
			'both'		=> 'get_post',
			'cookie'	=> 'cookie',
			'server'	=> 'server',
			'ip'		=> 'ip_address',
			'valid_ip'	=> 'valid_ip',
			'agent'		=> 'user_agent',
			'md5'		=> 'post'
			);

		if ( $type === 'files' ) {
			$files = current ( $_FILES );

			if ( ! is_null ( $name ) ) {
				return $files[$name];
			}

			return $files;
		}

		if ( ! array_key_exists ( $type, $func ) ) {
			return false;
		}

		$input_method = $func[$type];

		if ( $type == 'md5' ) {
			return md5 ( $_ci->input->$input_method ( $name, true ) );
		}
		return $_ci->input->$input_method ( $name, true );
	}
}

if ( ! function_exists ( 'is_input' ) ) {
	function is_input ( $type, $name = null, $value = null ) {
		if ( is_null ( $name ) ) {
			return get_input ( $type );
		}
		$input = get_input ( $type, $name );
		if ( ! $input OR $input !== $value ) {
			return false;
		}
		if ( is_null ( $value ) ) {
			return $input;
		}
		return true;
	}
}

if ( ! function_exists ( 'is_post' ) ) {
	function is_post ( $name, $value = null ) {
		return is_input ( 'post', $name, $value );
	}
}

if ( ! function_exists ( 'is_get' ) ) {
	function is_get ( $name, $value = null ) {
		return is_input ( 'get', $name, $value );
	}
}

// Date Helper
if ( ! function_exists ( 'date_translate' ) ) {
	function date_translate ( $date, $from = 'en', $to = 'id' ) {
		$en = array (
			'January', 'February', 'March', 'May', 'June',
			'July', 'August', 'October', 'December', 'Sunday',
			'Monday', 'Tuesday', 'Wednesday', 'Thursday',
			'Friday', 'Saturday', 'Aug', 'Oct', 'Dec', 'Sun',
			'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' );
		$id = array (
			'Januari', 'Februari', 'Maret', 'Mei', 'Juni',
			'Juli', 'Agustus', 'Oktober', 'Desember', 'Minggu',
			'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat',
			'Sabtu', 'Agt', 'Okt', 'Des', 'Min', 'Sen', 'Sel',
			'Rab', 'Kam', 'Jum', 'Sab' );
		return ( is_array ( $from ) AND is_array ( $to ) ) ?
			str_replace ( $from, $to, $date ) :
			str_replace ( $$from, $$to, $date );
	}
}

if ( ! function_exists ( 'date_range' ) ) {
	function date_range ( $date1, $date2, $duration = false ) {
		$date1 = strtotime ( $date1 );
		$date2 = strtotime ( $date2 );

		if ( $date1 == $date2 AND $duration ) {
			return array();
		}
		elseif ( $date1 == $date2 AND ! $duration ) {
			return array ( $date1 );
		}

		$first = $date1 < $date2 ? $date1 : $date2;
		$last = $date1 > $date2 ? $date1 : $date2;

		if ( ! $duration ) {
			$date_range[] = date ( 'Y-m-d', $first );
		}

		while ( $first != $last ) {
			$first = mktime ( 0, 0, 0, date ( "m", $first ), date ( "d", $first ) + 1, date ( "Y", $first ) );
			$date_range[] = date ( 'Y-m-d', $first );
		}

		return $date_range;
	}
}

if ( ! function_exists ( 'date_duration' ) ) {
	function date_duration ( $date1, $date2 ) {
		return date_range ( $date1, $date2, true );
	}
}

// Array Helper
if  ( ! function_exists ( 'array_column' ) ) {
	function array_column ( $input = null, $columnKey = null, $indexKey = null ) {
		$argc = func_num_args();
		$params = func_get_args();
		if ($argc < 2) {
			trigger_error("array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
			return null;
		}
		if (!is_array($params[0])) {
			trigger_error('array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given', E_USER_WARNING);
			return null;
		}
		if (!is_int($params[1])
			&& !is_float($params[1])
			&& !is_string($params[1])
			&& $params[1] !== null
			&& !(is_object($params[1]) && method_exists($params[1], '__toString'))
		) {
			trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
			return false;
		}
		if (isset($params[2])
			&& !is_int($params[2])
			&& !is_float($params[2])
			&& !is_string($params[2])
			&& !(is_object($params[2]) && method_exists($params[2], '__toString'))
		) {
			trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
			return false;
		}
		$paramsInput = $params[0];
		$paramsColumnKey = ($params[1] !== null) ? (string) $params[1] : null;
		$paramsIndexKey = null;
		if (isset($params[2])) {
			if (is_float($params[2]) || is_int($params[2])) {
				$paramsIndexKey = (int) $params[2];
			} else {
				$paramsIndexKey = (string) $params[2];
			}
		}
		$resultArray = array();
		foreach ($paramsInput as $row) {
			$key = $value = null;
			$keySet = $valueSet = false;
			if ($paramsIndexKey !== null && array_key_exists($paramsIndexKey, $row)) {
				$keySet = true;
				$key = (string) $row[$paramsIndexKey];
			}
			if ($paramsColumnKey === null) {
				$valueSet = true;
				$value = $row;
			} elseif (is_array($row) && array_key_exists($paramsColumnKey, $row)) {
				$valueSet = true;
				$value = $row[$paramsColumnKey];
			}
			if ($valueSet) {
				if ($keySet) {
					$resultArray[$key] = $value;
				} else {
					$resultArray[] = $value;
				}
			}
		}
		return $resultArray;
	}
}

if ( ! function_exists ( 'array_flatten' ) ) {
	function array_flatten ( $array, $flattened = array() ) {
		return call_user_func_array ( 'array_merge', $array );
	}
}

if ( ! function_exists ( 'recursive_array_search' ) ) {
	function recursive_array_search ( $needle, $haystack ) {
		foreach ( $haystack as $key => $value ) {
			if ( is_array ( $value ) ) {
				$inside = recursive_array_search ( $needle, $value );
			}

			if ( $needle === $value OR ( isset ( $inside ) AND $inside !== false ) ) {
				return $value;
			}
		}

		return false;
	}
}

if ( ! function_exists ( 'recursive_array_search_key' ) ) {
	function recursive_array_search_key ( $needle, $haystack ) {
		foreach ( $haystack as $key => $value ) {
			if ( is_array ( $value ) ) {
				$inside = recursive_array_search_key ( $needle, $value );
			}

			if ( $needle === $value OR ( isset ( $inside ) AND $inside !== false ) ) {
				return $key;
			}
		}

		return false;
	}
}

// Benchmark Helper
if ( ! function_exists ( 'benchmark_start' ) ) {
	function benchmark_start ( $slug ) {
		$_ci =& get_instance();
		$mark_name = $slug . '_start';
		return $_ci->benchmark->mark ( $mark_name );
	}
}

if ( ! function_exists ( 'benchmark_end' ) ) {
	function benchmark_end ( $slug ) {
		$_ci =& get_instance();
		$mark_name = $slug . '_end';
		return $_ci->benchmark->mark ( $mark_name );
	}
}

if ( ! function_exists ( 'benchmark_time' ) ) {
	function benchmark_time ( $slug ) {
		$_ci =& get_instance();
		$mark_start = $slug . '_start';
		$mark_end = $slug . '_end';
		$mark_time = str_replace ( ',', '', $_ci->benchmark->elapsed_time ( $mark_start, $mark_end ) );
		$mark_suffix_time = $mark_time < 60 ? ' detik' : ( $mark_time < 3600 ? ' menit' : ( $mark_time < 86400 ? ' jam' : ' hari' ) );
		$mark_exact_time = $mark_time < 60 ? $mark_time : ( $mark_time < 3600 ? $mark_time / 60 : ( $mark_time < 86400 ? $mark_time / 3600 : $mark_time / 86400 ) );
		return floor ( $mark_exact_time ) . $mark_suffix_time;
	}
}

// Pagination Helper
if ( ! function_exists ( 'pagination' ) ) {
	function pagination ( $page_name = 'page', $uri_segment = 3, $per_page = 10, $num_links = 3, $total_rows = 100, $page_query_string = false ) {
		$_ci =& get_instance();
		if ( is_array ( $page_name ) ) extract ( $page_name );
		if ( ! isset ( $_ci->paged ) ) $_ci->load->library ( 'pagination', null, 'paged' );
		$offset = $_ci->uri->segment ( $uri_segment ) ? ( $per_page * $_ci->uri->segment ( $uri_segment ) ) - $per_page : 0;
		$url = isset ( $uri_string ) ? $uri_string : $_ci->uri->uri_string();
		if ( ! is_array ( $page_name ) AND strpos ( $url, $page_name ) !== false ) {
			list ( $url, $page ) = explode ( $page_name, $url );
		}
		$paging['base_url']	= $_ci->config->site_url (  $url . '/' . $page_name );
		if ( $_ci->input->get ( 'submit', true ) == 'search' OR $page_query_string !== false ) :
			$paging['first_url'] = $_ci->config->site_url (  $url ) .'?'. http_build_query ( $_ci->input->get ( null, true ) );
			$paging['suffix'] = '?'. http_build_query ( $_ci->input->get ( null, true ) );
		endif;
		$paging['total_rows'] = isset ( $total_rows ) ? $total_rows : 100;
		$paging['per_page'] = isset ( $per_page ) ? $per_page : 10;
		$paging['num_links'] = isset ( $num_links ) ? $num_links - 1 : 2;
		$paging['uri_segment'] = isset ( $uri_segment ) ? $uri_segment : 3;
		$paging['use_page_numbers'] = isset ( $use_page_numbers ) ? $use_page_numbers : false;
		$paging['page_query_string'] = isset ( $page_query_string ) ? $page_query_string : false;
		$paging['full_tag_open'] = isset ( $full_tag_open ) ? $full_tag_open : '<ul class="pagination">';
		$paging['full_tag_close'] = isset ( $full_tag_close ) ? $full_tag_close : '</ul>';
		$paging['first_link'] = isset ( $first_link ) ? $first_link : 'First';
		$paging['first_tag_open'] = isset ( $first_tag_open ) ? $first_tag_open : '<li>';
		$paging['first_tag_close'] = isset ( $first_tag_close ) ? $first_tag_close : '</li>';
		$paging['last_link'] = isset ( $last_link ) ? $last_link : 'Last';
		$paging['last_tag_open'] = isset ( $last_tag_open ) ? $last_tag_open : '<li>';
		$paging['last_tag_close'] = isset ( $last_tag_close ) ? $last_tag_close : '</li>';
		$paging['next_link'] = isset ( $next_link ) ? $next_link : '&raquo;';
		$paging['next_tag_open'] = isset ( $next_tag_open ) ? $next_tag_open : '<li>';
		$paging['next_tag_close'] = isset ( $next_tag_close ) ? $next_tag_close : '</li>';
		$paging['prev_link'] = isset ( $prev_link ) ? $prev_link : '&laquo;';
		$paging['prev_tag_open'] = isset ( $prev_tag_open ) ? $prev_tag_open : '<li>';
		$paging['prev_tag_close'] = isset ( $prev_tag_close ) ? $prev_tag_close : '</li>';
		$paging['cur_tag_open'] = isset ( $cur_tag_open ) ? $cur_tag_open : '<li class="active"><a>';
		$paging['cur_tag_close'] = isset ( $cur_tag_close ) ? $cur_tag_close : '</a></li>';
		$paging['num_tag_open'] = isset ( $num_tag_open ) ? $num_tag_open : '<li>';
		$paging['num_tag_close'] = isset ( $num_tag_close ) ? $num_tag_close : '</li>';
		if ( isset ( $display_pages ) ) $paging['display_pages'] = $display_pages;
		$_ci->paged->initialize ( $paging );
		$current_offset = $paging['use_page_numbers'] !== false ? $offset : $_ci->uri->segment ( $uri_segment );
		$current_num = $paging['use_page_numbers'] !== false ? $offset + 1 : $_ci->uri->segment ( $uri_segment ) + 1;
		$last_per_page = $total_rows < ( $current_num * $per_page ) ? $total_rows : $current_num * $per_page;
		return ( object ) array (
			'limit' => $per_page,
			'offset' => $current_offset,
			'num' => $current_num,
			'info' => 'Showing ' .$current_num. ' to ' .$last_per_page. ' of ' .$total_rows. ' Records',
			'links' => $_ci->paged->create_links()
			);
	}
}

// Upload Helper
if ( ! function_exists ( 'do_upload' ) ) {
	function do_upload ( $name = 'userfile', $path = './upload/', $types = 'gif|jpg|png', $size = '500' ) {
		$CI =& get_instance();

		if ( ! isset ( $CI->upload ) ) {
			$CI->load->library ( 'upload' );
		}

		if ( is_array ( $path ) ) {
			foreach ( $path as $opt => $val ) {
				$cfg[$opt] = $val;
			}
		} else {
			$cfg['upload_path'] = $path;
			$cfg['allowed_types'] = $types;
			$cfg['max_size'] = $size;
		}

		$CI->upload->initialize($cfg);

		if ( ! is_dir ( $cfg['upload_path'] ) AND ! mkdir ( $cfg['upload_path'], 0777, true ) ) {
			return array ( 'error' => true, 'msg' => 'Ups! Something wrong with directory upload' );
		}

		if ( $CI->upload->do_upload ( $name ) === false ) {
			return array ( 'error' => true, 'msg' => $CI->upload->error_msg );
		}
		else {
			return $CI->upload->data();
		}
	}
}

// Database helper
if ( ! function_exists ( 'initiate_db' ) ) {
	function &initiate_db() {
		$_ci =& get_instance();
		$_ci->load->config ( 'database', false, true );
		$dbparam = ! config_item ( 'default' ) ? '' : config_item ( 'default' );
		unset ( $_ci->db );
		$db = $_ci->load->database ( $dbparam, true );
		return $db;
	}
}

if ( ! function_exists ( 'list_tables' ) ) {
	function list_tables() {
		$_db =& initiate_db();
		return $_db->list_tables();
	}
}

if ( ! function_exists ( 'table_exists' ) ) {
	function table_exists ( $table ) {
		$_db =& initiate_db();
		return $_db->table_exists ( $_db->dbprefix ( $table ) );
	}
}

if ( ! function_exists ( 'list_fields' ) ) {
	function list_fields() {
		$_db =& initiate_db();
		return $_db->list_fields();
	}
}

if ( ! function_exists ( 'field_exists' ) ) {
	function field_exists ( $field, $table ) {
		$_db =& initiate_db();
		return $_db->field_exists ( $field, $_db->dbprefix ( $table ) );
	}
}

if ( ! function_exists ( 'field_data' ) ) {
	function field_data ( $table = null ) {
		$_db =& initiate_db();
		return $_db->field_data ( $_db->dbprefix ( $table ) );
	}
}

if ( ! function_exists ( 'fields_data' ) ) {
	function fields_data ( $table = null ) {
		$_db =& initiate_db();
		$query = $_db->query ( 'SHOW COLUMNS FROM ' . $_db->dbprefix ( $table ) );
		if ( $query->num_rows() > 0 ) {
			// while ( $row = $query->result() ) {
			echo_r ( $query->result() );
			// }
		}
	}
}

/*
	First Param 		Second Param 						Third Parameter
	-----------------	------------------------------		----------------------
	create_database 	Database name 						-
	drop_database 		Database name 						-
	add_field 			Field preference (array/string)		-
	add_key				Field name 							Primary Key (true/false)
	create_table		Table name 							IF NOT EXISTS (true/false)
	drop_table			Table name 							-
	rename_table		Old table name 						New table name
	add_column 			Table name 							Field preference
	drop_column			Table name 							Column Name
	modify_column		Table name 							Field preference

	The Field Preference
	$pref = array (
		'field_name' => array (
			'name'				=> New name for modifying column only
			'type'				=> INT/VARCHAR/TEXT etc.
			'constraint'		=> Based-type size
			'unsigned'			=> true/false
			'auto_increment'	=> true/false
			'null'				=> true/false
			'default'			=> ''
			)
		);

	Third parameter are used for the backup function to handle a force download, default is false
	Fourth parameter are used for the backup filename, default is backup.gz
*/
if ( ! function_exists ( 'db_tools' ) ) {
	function db_tools ( $func, $data, $param = false ) {
		$_ci =& get_instance();
		$_ci->load->dbforge();
		return $_ci->dbforge->$func ( $data, $param );
	}
}

/*
	First Param 		Second Param
	-----------------	------------------------------
	list_databases 		No need second parameter
	database_exists 	Database name
	optimize_table 		Table name
	repair_table		Table name
	optimize_database	No need second parameter
	csv_from_result		Query result
	xml_from_result		Query result
	backup 				Backup Preference

	The Backup Preference
	$pref = array (
		'tables'      => array('table1', 'table2'), 	// Array of tables to backup.
		'ignore'      => array(),           			// List of tables to omit from the backup
		'format'      => 'txt',             			// gzip, zip, txt
		'filename'    => 'mybackup.sql',    			// File name - NEEDED ONLY WITH ZIP FILES
		'add_drop'    => true,              			// Whether to add DROP TABLE statements to backup file
		'add_insert'  => true,              			// Whether to add INSERT data to backup file
		'newline'     => "\n"               			// Newline character used in backup file
		);

	Third parameter are used for the backup function to handle a force download, default is false
	Fourth parameter are used for the backup filename, default is backup.gz
*/
if ( ! function_exists ( 'db_utility' ) ) {
	function db_utility ( $func, $param = array(), $download = false, $filepath = 'backup.gz' ) {
		$_ci =& get_instance();
		$_ci->load->dbutil();

		if ( 'backup' !== $func AND ! $download ) {
			return $this->dbutil->$func ( $param );
		}

		$backup =& $_ci->dbutil->$func ( $param );

		$_ci->load->helper ( 'file' );
		write_file ( FCPATH . $filepath, $backup );

		$_ci->load->helper ( 'download' );
		return force_download ( $filepath, $backup );
	}
}

// CURL Helper
if ( ! function_exists ( 'get_remote' ) ) {
	function get_remote ( $source, $value = array(), $format = 'json' ) {
		$server = config_item ( 'api_baseurl' ) ? config_item ( 'api_baseurl' ) .'/' : null;

		if ( config_item ( 'api_logins' ) ) {
			$username = key ( config_item ( 'api_logins' ) );
			$password = current ( config_item ( 'api_logins' ) );
		}

		if ( ! isset ( $value['format'] ) ) {
			$value['format'] = $format;
		}

		$query = count ( $value ) > 0 ? '?' . http_build_query ( $value ) : null;

		$curl_handle = curl_init();
		curl_setopt ( $curl_handle, CURLOPT_URL, $server . $source . $query );
		curl_setopt ( $curl_handle, CURLOPT_RETURNTRANSFER, 1 );

		if ( isset ( $username ) AND isset ( $password ) ) {
			curl_setopt ( $curl_handle, CURLOPT_USERPWD, $username . ':' . $password );
			curl_setopt ( $curl_handle,  CURLOPT_HTTPAUTH, CURLAUTH_DIGEST );
		}

		$buffer = curl_exec ( $curl_handle );
		curl_close ( $curl_handle );

		if ( ! $buffer ) {
			$key_status = config_item ( 'rest_status_field_name' ) ? config_item ( 'rest_status_field_name' ) : 'status';
			$key_message = config_item ( 'rest_message_field_name' ) ? config_item ( 'rest_message_field_name' ) : 'error';
			$method = 'to_' . $value['format'];
			$format = new Format ( array ( $key_status => false, $key_message => 'Error on communicating to server' ) );
			$buffer = $format->$method();
		}

		switch ( $value['format'] ) {
			case 'json':
				header('Content-Type: application/json');
				break;

			case 'xml':
				header('Content-Type: application/xml');
				break;

			case 'html':
				header('Content-Type: text/html');
				break;

			default:
				header('Content-Type: text/plain');
				break;
		}
		return $buffer;
	}
}

if ( ! function_exists ( 'post_remote' ) ) {
	function post_remote ( $source, $value = array(), $format = 'json' ) {
		$server = config_item ( 'api_baseurl' ) ? config_item ( 'api_baseurl' ) .'/' : null;

		if ( config_item ( 'api_logins' ) ) {
			$username = key ( config_item ( 'api_logins' ) );
			$password = current ( config_item ( 'api_logins' ) );
		}

		$curl_handle = curl_init();
		curl_setopt ( $curl_handle, CURLOPT_URL, $server . $source );
		curl_setopt ( $curl_handle, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $curl_handle, CURLOPT_POST, 1 );
		curl_setopt ( $curl_handle, CURLOPT_POSTFIELDS, $value );

		if ( isset ( $username ) AND isset ( $password ) ) {
			curl_setopt ( $curl_handle, CURLOPT_USERPWD, $username . ':' . $password );
			curl_setopt ( $curl_handle,  CURLOPT_HTTPAUTH, CURLAUTH_DIGEST );
		}

		$buffer = curl_exec ( $curl_handle );
		curl_close ( $curl_handle );

		if ( ! $buffer ) {
			$key_status = config_item ( 'rest_status_field_name' ) ? config_item ( 'rest_status_field_name' ) : 'status';
			$key_message = config_item ( 'rest_message_field_name' ) ? config_item ( 'rest_message_field_name' ) : 'error';
			$method = 'to_' . $value['format'];
			$format = new Format ( array ( $key_status => false, $key_message => 'Error on communicating to server' ) );
			$buffer = $format->$method();
		}

		switch ( $value['format'] ) {
			case 'json':
				header('Content-Type: application/json');
				break;

			case 'xml':
				header('Content-Type: application/xml');
				break;

			case 'html':
				header('Content-Type: text/html');
				break;

			default:
				header('Content-Type: text/plain');
				break;
		}
		return $buffer;
	}
}

if ( ! function_exists ( 'put_remote' ) ) {
	function put_remote ( $source, $value = array(), $format = 'json' ) {
		$server = config_item ( 'api_baseurl' ) ? config_item ( 'api_baseurl' ) .'/' : null;

		if ( config_item ( 'api_logins' ) ) {
			$username = key ( config_item ( 'api_logins' ) );
			$password = current ( config_item ( 'api_logins' ) );
		}

		$curl_handle = curl_init();

		curl_setopt ( $curl_handle, CURLOPT_URL, $server . $source );
		curl_setopt ( $curl_handle, CURLOPT_CUSTOMREQUEST, "PUT" );
		curl_setopt ( $curl_handle, CURLOPT_HEADER, 0 );
		curl_setopt ( $curl_handle, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $curl_handle, CURLOPT_HTTPHEADER, array ( 'Content-Type: application/json' ) );
		curl_setopt ( $curl_handle, CURLOPT_POSTFIELDS, http_build_query ( $value ) );

		$buffer = curl_exec ( $curl_handle );
		curl_close ( $curl_handle );

		if ( ! $buffer ) {
			$key_status = config_item ( 'rest_status_field_name' ) ? config_item ( 'rest_status_field_name' ) : 'status';
			$key_message = config_item ( 'rest_message_field_name' ) ? config_item ( 'rest_message_field_name' ) : 'error';
			$method = 'to_' . $value['format'];
			$format = new Format ( array ( $key_status => false, $key_message => 'Error on communicating to server' ) );
			$buffer = $format->$method();
		}

		switch ( $value['format'] ) {
			case 'json':
				header('Content-Type: application/json');
				break;

			case 'xml':
				header('Content-Type: application/xml');
				break;

			case 'html':
				header('Content-Type: text/html');
				break;

			default:
				header('Content-Type: text/plain');
				break;
		}
		return $buffer;
	}
}

// String Helper
if ( ! function_exists ( 'unique_slug' ) ) {
	function unique_slug ( $name, $sep = '-', $num = null ) {
		$_ci =& get_instance();

		if ( ! function_exists ( 'increment_string' ) ) {
			$_ci->load->helper('string');
		}

		return increment_string ( $name, $sep, $num );
	}
}

if ( ! function_exists ( 'create_slug' ) ) {
	function create_slug ( $title, $sep = '-', $lowercase = true ) {
		$_ci =& get_instance();

		if ( ! function_exists ( 'url_title' ) ) {
			$_ci->load->helper('url');
		}

		return url_title ( $title, $sep, $lowercase );
	}
}

// Still unclassified
if ( ! function_exists ( 'get_option' ) ) {
	function get_option ( $name ) {
		$settings =& _model ( 'm_settings' );
		return $settings->get_option ( $name );
	}
}