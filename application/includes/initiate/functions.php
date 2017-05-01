<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

if (!function_exists('call')) {
	function &call ( $controller ) {
		switch ($controller) {
			case 'login-form':
				$_ci =& get_instance();
				extract ( $_ci->load->views_data );

				$default_login_theme_name = 'login';
				if ( isset ( $alert ) ) {
					$type = 'info';
					if (isset($alert['class'])) {
						$class = ' '.trim($alert['class']);
					}
					if (isset($alert['type'])) {
						$type = $alert['type'];
					}
					$alert_div = '<div class="alert alert-' .$type. ' alert-thin text-center' .$class. '">' . $alert['message'] . '</div>' . "\n";
				}

				if ( file_exists ( get_template_dir ( $default_login_theme_name . EXT ) ) ) {
					include get_template_dir ( $default_login_theme_name . EXT );
				} else {
					include INCPATH . 'views/login/form' . EXT;
				}
				break;

			default:
				$_ci =& get_instance();
				return $_ci->load->controller($controller);
				break;
		}
	}
}

// Loader Helper
if (!function_exists('library')) {
	function &library ($library) {
		$_ci =& get_instance();
		if (!isset($_ci->$library)) {
			$_ci->load->library($library);
		}
		return $_ci->$library;
	}
}

if (!function_exists('model')) {
	function &model ($model) {
		$_ci =& get_instance();
		if (!isset($_ci->$model)) {
			$_ci->load->model($model);
		}
		return $_ci->$model;
	}
}

if (!function_exists('helper')) {
	function helper ($helper) {
		$_ci =& get_instance();
		$_ci->load->helper($helper);
	}
}

if (!function_exists('view')) {
	function view ($filepath, $vars = array()) {
		$_ci =& get_instance();
		return $_ci->load->view($filepath, $vars);
	}
}

if (!function_exists('get_view' ) ) {
	function get_view ($filepath, $vars = array()) {
		$_ci =& get_instance();
		return $_ci->load->view($filepath, $vars, true);
	}
}

// Path / URL Helper
if (!function_exists('current_path')) {
	function current_path ($type = null, $realpath = false) {
		$_ci =& get_instance();
		$debug = current ( debug_backtrace() );
		$current_path = str_replace ( array ( FCPATH, '\\' ), array ( '', '/' ), $debug['file'] );

		if ('views' == $type && strpos($current_path, $type) !== false) {
			return str_replace('application/', APPPATH, $current_path);
		}
		elseif('views' == $type && strpos($current_path, $type) === false) {
			return '<small>It\'s not a views file!</small>';
		}
		elseif('controllers' == $type) {
			return $_ci->load->current_controller;
		}
		else {
			return $realpath ? realpath($current_path) : $current_path;
		}
	}
}

if (!function_exists('current_url')) {
	function current_url($string_only = false) {
		$_ci =& get_instance();
		$query_string = null;

		if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
			$query_string = "?{$_SERVER['QUERY_STRING']}";
		}

		$url_string = $_ci->uri->uri_string() . $query_string;

		if ($string_only) {
			return $url_string;
		}
		return $_ci->config->site_url($url_string);
	}
}

if (!function_exists('referer_url')) {
	function referer_url() {
		if (isset($_SERVER['HTTP_REFERER'])) {
			return $_SERVER['HTTP_REFERER'];
		}
		return false;
	}
}

if (!function_exists('url')) {
	function url ($path=null, $base=false) {
		$_ci =& get_instance();
		if ($base) {
			return $_ci->config->base_url($path);
		}
		return $_ci->config->site_url($path);
	}
}

if (!function_exists('redirect')) {
	function redirect ($redirect_url_path = '/', $method = 'location', $http_response_code = 302) {
		$_ci =& get_instance();

		$type = 'site';
		if ($redirect_url_path === '/') {
			$type = 'base';
		}
		$url_type = $type.'_url';
		$url_string = $_ci->uri->uri_string();
		$url_path = $_ci->config->site_url($url_string);
		$url_encoded = urlencode($url_path);
		$url_direct = "?redirect={$url_encoded}";

		if (!preg_match('#^https?://#i', $redirect_url_path)) {
			$redirect_url_path = $_ci->config->$url_type($redirect_url_path);
		}

		switch($method) {
			case 'refresh':
				if ($http_response_code===302) {
					$http_response_code = 0;
				}
				header("Refresh:{$http_response_code};url={$redirect_url_path}");
				break;

			case 'meta':
				if ($http_response_code===302) {
					$http_response_code = 0;
				}
				return "<meta http-equiv='refresh' content='{$http_response_code}; url={$redirect_url_path}'>\n";
				break;

			case 'redirect':
				header("Location: {$redirect_url_path}{$url_direct}", true, $http_response_code);
				break;

			default:
				header("Location: {$redirect_url_path}", true, $http_response_code);
				break;
		}
		exit;
	}
}

// Data Input Helper
if (!function_exists('input')) {
	function input ($type=null, $name=null) {
		if (is_null($type)) {
			parse_str($_SERVER['QUERY_STRING'], $parse);
			return is_null(key($parse)) ? false : key($parse);
		}

		$_ci =& get_instance();
		$type = strtolower ( $type );
		$func = array (
		// 	$name 		=> $method
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

		if ($type==='files') {
			$files = current($_FILES);
			if (!is_null($name)) {
				return $files[$name];
			}
			return $files;
		}

		if (!array_key_exists($type, $func)) {
			return false;
		}

		$input_method = $func[$type];
		$input_value = $_ci->input->$input_method($name, true);

		if ($type==='md5') {
			return md5($input_value);
		}
		return $input_value;
	}
}

if (!function_exists('is_input')) {
	function is_input ($type, $name=null, $value=null) {
		$input = input($type, $name);
		if (!$input) {
			return false;
		}
		if (is_null($value)) {
			return true;
		}
		if (is_bool($value)===true) {
			return $input;
		}
		if ($input!==$value) {
			return false;
		} else {
			return true;
		}
	}
}

if (!function_exists('is_post')) {
	function is_post ($name, $value=null) {
		return is_input('post', $name, $value);
	}
}

if (!function_exists('is_get')) {
	function is_get($name, $value=null) {
		return is_input('get', $name, $value);
	}
}

if (!function_exists('is_submit')) {
	function is_submit ($value=null) {
		if (is_get('submit')) {
			return is_get('submit', $value);
		}
		elseif (is_post('submit')) {
			return is_post('submit', $value);
		}
		else {
			return false;
		}
	}
}

// Date Helper
if (!function_exists('date_translate')) {
	function date_translate($date, $from='en', $to='id') {
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
		return (is_array($from) && is_array($to)) ?
			str_replace($from, $to, $date) :
			str_replace($$from, $$to, $date);
	}
}

if (!function_exists('date_range')) {
	function date_range ($date1, $date2, $duration=false) {
		$date1 = strtotime($date1);
		$date2 = strtotime($date2);

		if ($date1==$date2 && $duration) {
			return array();
		}
		elseif ($date1==$date2 && !$duration) {
			return array($date1);
		}

		$first = $date1<$date2 ? $date1 : $date2;
		$last = $date1>$date2 ? $date1 : $date2;

		if (!$duration) {
			$date_range[] = date('Y-m-d', $first);
		}

		while($first!=$last) {
			$first = mktime(0, 0, 0, date("m", $first), date("d", $first)+1, date("Y", $first));
			$date_range[] = date('Y-m-d', $first);
		}

		return $date_range;
	}
}

if (!function_exists('date_duration')) {
	function date_duration ($date1, $date2) {
		return date_range($date1, $date2, true);
	}
}

// Array Helper
if (!function_exists('array_column')) {
	function array_column ($input=null, $columnKey=null, $indexKey=null) {
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
		if (!is_int($params[1]) && !is_float($params[1]) && !is_string($params[1]) && $params[1] !== null && !(is_object($params[1]) && method_exists($params[1], '__toString'))) {
			trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
			return false;
		}
		if (isset($params[2]) && !is_int($params[2]) && !is_float($params[2]) && !is_string($params[2]) && !(is_object($params[2]) && method_exists($params[2], '__toString'))) {
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

if (!function_exists('array_flatten')) {
	function array_flatten($array, $flattened=array()) {
		return call_user_func_array('array_merge', $array);
	}
}

if (!function_exists('recursive_array_search')) {
	function recursive_array_search ($needle, $haystack) {
		foreach ($haystack as $key => $value) {
			if (is_array($value)) {
				$inside = recursive_array_search($needle, $value);
			}
			if ($needle===$value || (isset($inside) && $inside!==false)) {
				return $value;
			}
		}
		return false;
	}
}

if (!function_exists('recursive_array_search_key')) {
	function recursive_array_search_key($needle, $haystack) {
		foreach ($haystack as $key => $value) {
			if (is_array($value)) {
				$inside = recursive_array_search_key($needle, $value);
			}
			if ($needle===$value || (isset($inside) && $inside!==false)) {
				return $key;
			}
		}
		return false;
	}
}

// Benchmark Helper
if (!function_exists('start_mark')) {
	function start_mark ($slug) {
		$_ci =& get_instance();
		$mark_name = $slug .'_start';
		return $_ci->benchmark->mark($mark_name);
	}
}

if (!function_exists('end_mark')) {
	function end_mark ($slug) {
		$_ci =& get_instance();
		$mark_name = $slug .'_end';
		return $_ci->benchmark->mark($mark_name);
	}
}

if (!function_exists('elapse_mark')) {
	function elapse_mark ($slug) {
		$_ci =& get_instance();
		$mark_start = $slug .'_start';
		$mark_end = $slug .'_end';
		$mark_time = str_replace(',', '', $_ci->benchmark->elapsed_time($mark_start, $mark_end));
		$mark_suffix_time = $mark_time<60 ? ' detik' : ($mark_time<3600 ? ' menit' : ($mark_time<86400 ? ' jam' : ' hari'));
		$mark_exact_time = $mark_time<60 ? $mark_time : ($mark_time<3600 ? $mark_time/60 : ($mark_time<86400 ? $mark_time/3600 : $mark_time/86400));
		return floor($mark_exact_time) . $mark_suffix_time;
	}
}

// Pagination Helper
if (!function_exists('pagination')) {
	function pagination($model,$args='page') {
		$_ci =& get_instance();
		$default_var = array(
			'page_name'		=> 'page',
			'uri_segment'	=> 3,
			'per_page'		=> 10,
			'num_links'		=> 3,
			'page_query_string'	=> false
			);
		if (!is_null($args) && $args!==false && !empty($args) && !is_array($args) && !is_int($args)) {
			$default_var['page_name'] = $args;
		} elseif (is_int($args)) {
			$default_var['per_page'] = $args;
		}
		extract($default_var);
		if (is_array($args)) {
			extract($args);
		}
		if (strpos($_ci->uri->uri_string(), $page_name)!==false) {
			$uri_segment = count(explode('/',current(explode($page_name,$_ci->uri->uri_string()))))+1;
		} else {
			$uri_segment = count(explode('/',$_ci->uri->uri_string()))+1;
		}
		if (!isset($_ci->paged)) {
			$_ci->load->library('pagination', null, 'paged');
		}
		if (!isset($_ci->data_paged)) {
			$_ci->load->model($model,'data_paged');
		}
		$offset = $_ci->uri->segment ( $uri_segment ) ? ( $per_page * $_ci->uri->segment ( $uri_segment ) ) - $per_page : 0;
		$url = isset ( $uri_string ) ? $uri_string : $_ci->uri->uri_string();
		if (!is_array($page_name) && strpos($url, $page_name)!==false) {
			list($url, $page) = explode($page_name, $url);
		}
		$_ci->paged->name = $page_name;
		$paging['base_url']	= $_ci->config->site_url($url .'/'. $page_name);
		if ($_ci->input->get('submit', true)=='search' || $page_query_string!==false) {
			$paging['first_url'] = $_ci->config->site_url($url) .'?'. http_build_query($_ci->input->get(null, true));
			$paging['suffix'] = '?'. http_build_query($_ci->input->get(null, true));
		}
		if (!method_exists($_ci->data_paged,'pagination_total_rows')) {
			show_error('Undefined pagination_total_rows');
		}
		$paging['total_rows'] = $_ci->data_paged->pagination_total_rows();
		// show_error($paging['total_rows']);
		$paging['per_page'] = isset($per_page) ? $per_page : 10;
		$paging['num_links'] = isset($num_links) ? $num_links - 1 : 2;
		$paging['uri_segment'] = isset($uri_segment) ? $uri_segment : 3;
		$paging['use_page_numbers'] = isset($use_page_numbers) ? $use_page_numbers : true;
		$paging['page_query_string'] = isset($page_query_string) ? $page_query_string : false;
		$paging['full_tag_open'] = isset($full_tag_open) ? $full_tag_open : '<ul class="pagination">';
		$paging['full_tag_close'] = isset($full_tag_close) ? $full_tag_close : '</ul>';
		$paging['first_link'] = isset($first_link) ? $first_link : 'First';
		$paging['first_tag_open'] = isset($first_tag_open) ? $first_tag_open : '<li>';
		$paging['first_tag_close'] = isset($first_tag_close) ? $first_tag_close : '</li>';
		$paging['last_link'] = isset($last_link) ? $last_link : 'Last';
		$paging['last_tag_open'] = isset($last_tag_open) ? $last_tag_open : '<li>';
		$paging['last_tag_close'] = isset($last_tag_close) ? $last_tag_close : '</li>';
		$paging['next_link'] = isset($next_link) ? $next_link : '&raquo;';
		$paging['next_tag_open'] = isset($next_tag_open) ? $next_tag_open : '<li>';
		$paging['next_tag_close'] = isset($next_tag_close) ? $next_tag_close : '</li>';
		$paging['prev_link'] = isset($prev_link) ? $prev_link : '&laquo;';
		$paging['prev_tag_open'] = isset($prev_tag_open) ? $prev_tag_open : '<li>';
		$paging['prev_tag_close'] = isset($prev_tag_close) ? $prev_tag_close : '</li>';
		$paging['cur_tag_open'] = isset($cur_tag_open) ? $cur_tag_open : '<li class="active"><a>';
		$paging['cur_tag_close'] = isset($cur_tag_close) ? $cur_tag_close : '</a></li>';
		$paging['num_tag_open'] = isset($num_tag_open) ? $num_tag_open : '<li>';
		$paging['num_tag_close'] = isset($num_tag_close) ? $num_tag_close : '</li>';
		if (isset($display_pages)) {
			$paging['display_pages'] = $display_pages;
		}
		$_ci->paged->initialize($paging);
		$current_offset = $paging['use_page_numbers'] !== false ? $offset : $_ci->uri->segment ( $uri_segment );
		$current_num = $paging['use_page_numbers'] !== false ? $offset + 1 : $_ci->uri->segment ( $uri_segment ) + 1;
		$last_per_page = $paging['total_rows'] < ( $current_num * $per_page ) ? $paging['total_rows'] : $current_num * $per_page;
		if (!method_exists($_ci->data_paged,'pagination_data_each')) {
			show_error('Undefined pagination_data_each');
		}
		$find_info = array('{{current}}','{{last}}','{{total}}');
		$replace_info = array($current_num, $last_per_page, $paging['total_rows']);
		if (!isset($info_text_format)) {
			$info_text_format = 'Showing {{current}} to {{last}} of {{total}} records.';
		}
		$info_text = str_replace($find_info, $replace_info, $info_text_format);
		return (object) array(
			'limit' => $per_page,
			'offset' => $current_offset,
			'num' => $current_num,
			'info' => $info_text,
			'links' => $_ci->paged->create_links(),
			'data' => $_ci->data_paged->pagination_data_each($per_page, $current_offset)
			);
	}
}

// Upload Helper
if (!function_exists('upload')) {
	function upload ($name='userfile', $path='./upload/', $types='gif|jpg|png', $size='500') {
		$CI =& get_instance();
		if (!isset($CI->upload)) {
			$CI->load->library('upload');
		}
		if (is_array($path)) {
			foreach ($path as $opt => $val) {
				$cfg[$opt] = $val;
			}
		} else {
			$cfg['upload_path'] = $path;
			$cfg['allowed_types'] = $types;
			$cfg['max_size'] = $size;
		}
		$CI->upload->initialize($cfg);
		if (!is_dir($cfg['upload_path']) && !mkdir($cfg['upload_path'], 0777, true)) {
			return array('error'=>true, 'msg'=>'Ups! Something wrong with directory upload');
		}
		if ($CI->upload->do_upload($name)===false) {
			return array('error'=>true, 'msg'=>$CI->upload->error_msg);
		}
		else {
			return $CI->upload->data();
		}
	}
}

// Database helper
if (!function_exists('initiate_db')) {
	function &initiate_db() {
		$_ci =& get_instance();
		$_ci->load->config('database', false, true);
		$dbparam = !$_ci->config->item('default') ? '' : $_ci->config->item('default');
		unset($_ci->db);
		$object = $_ci->load->database($dbparam, true);
		return $object;
	}
}

if (!function_exists('list_tables')) {
	function list_tables() {
		$_db =& initiate_db();
		$list_tables = $_db->list_tables();
		return $list_tables;
	}
}

if (!function_exists('table_exists')) {
	function table_exists($table) {
		$_db =& initiate_db();
		$is_exists = $_db->table_exists($_db->dbprefix($table));
		return $is_exists;
	}
}

if (!function_exists('list_fields')) {
	function list_fields() {
		$_db =& initiate_db();
		$list_fields = $_db->list_fields();
		return $list_fields;
	}
}

if (!function_exists('field_exists')) {
	function field_exists($field, $table) {
		$_db =& initiate_db();
		$is_exists = $_db->field_exists($field, $_db->dbprefix($table));
		return $is_exists;
	}
}

if (!function_exists('field_data')) {
	function field_data($table=null) {
		$_db =& initiate_db();
		$field_data = $_db->field_data($_db->dbprefix($table));
		return $field_data;
	}
}

if (!function_exists('fields_data')) {
	function fields_data($table,$where=array(),$like=false) {
		if (is_null($table)) {
			return false;
		}
		$_db =& initiate_db();
		$type = ' WHERE ';
		if ($like) {
			$type = ' LIKE ';
		}
		$clause = '';
		if (is_array($where) && count($where)>0) {
			$count = count($where);
			foreach ($where as $field => $value) {
				$value = addslashes($value);
				$clause .= "{$field}='{$value}'";
				if ($count>1) {
					$clause .= ' AND ';
				}
				$count--;
			}
			$clause = $type . $clause;
		} elseif (is_string($where)) {
			$clause = $where;
		}
		$query = $_db->query('SHOW COLUMNS FROM '. $_db->dbprefix($table) . $clause);
		if ($query->num_rows()>0) {
			return $query->result();
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
if (!function_exists('db_tools')) {
	function db_tools ($func=null, $data=null, $param=false) {
		$_ci =& get_instance();
		$_ci->load->dbforge();
		if (is_null($func)) {
			return $_ci->dbforge;
		}
		return $_ci->dbforge->$func($data, $param);
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
if (!function_exists('db_utility')) {
	function db_utility ($func=null, $param=array(), $download=false, $filepath='backup.gz') {
		$_ci =& get_instance();
		$_ci->load->dbutil();

		if (is_null($func)) {
			return $_ci->dbutil;
		}

		if ('backup'!==$func && !$download) {
			return $_ci->dbutil->$func($param);
		}

		$backup =& $_ci->dbutil->$func($param);
		$_ci->load->helper('file');
		write_file(FCPATH.$filepath, $backup);
		$_ci->load->helper('download');
		return force_download($filepath, $backup);
	}
}

// CURL Helper
if (!function_exists('crequest')) {
	function crequest ($type='get',$source, $value=array(), $user_auth=array('admin'=>'1234'), $format='json', $http_auth='basic') {
		if (is_array($user_auth)) {
			$username = key($user_auth);
			$password = current($user_auth);
		}

		switch ( $format ) {
			case 'json': $header = 'Content-Type: application/json'; break;
			case 'xml': $header = 'Content-Type: application/xml'; break;
			case 'html': $header = 'Content-Type: text/html'; break;
			default: $header = 'Content-Type: text/plain'; break;
		}

		$curl_handle = curl_init();
		curl_setopt($curl_handle, CURLOPT_URL, $source);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		// curl_setopt($curl_handle, CURLOPT_HEADER, 0);
		if ($type==='post') {
			curl_setopt($curl_handle, CURLOPT_POST, 1);
		} elseif ($type==='put') {
			curl_setopt($curl_handle, CURLOPT_PUT, 1);
		}
		if (is_array($value) && count($value)>0) {
			curl_setopt($curl_handle, CURLOPT_POSTFIELDS, http_build_query($value));
		}
		curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array($header));

		if (isset($username, $password)) {
			curl_setopt($curl_handle, CURLOPT_USERPWD, "{$username}:{$password}");
			if ($http_auth==='basic') {
				curl_setopt($curl_handle,  CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			} elseif ($http_auth==='digest') {
				curl_setopt($curl_handle,  CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
			}
		}

		$buffer = curl_exec ( $curl_handle );
		curl_close ( $curl_handle );

		return $buffer;
	}
}

if (!function_exists('cget')) {
	function cget($source, $value=array(), $user_auth=array('admin'=>'1234'), $format='json', $http_auth='basic') {
		return crequest('get',$source, $value, $user_auth, $format, $http_auth);
	}
}

if (!function_exists('cpost')) {
	function cpost ($source, $value=array(), $user_auth=array('admin'=>'1234'), $format='json', $http_auth='basic') {
		return crequest('post',$source, $value, $user_auth, $format, $http_auth);
	}
}

if (!function_exists('cput')) {
	function cput ($source, $value=array(), $user_auth=array('admin'=>'1234'), $format='json', $http_auth='basic') {
		return crequest('put',$source, $value, $user_auth, $format, $http_auth);
	}
}

// String Helper
if (!function_exists('slug')) {
	function slug ($name, $sep='-', $lowercase=true, $num=null) {
		$_ci =& get_instance();
		if (!function_exists('increment_string')) {
			$_ci->load->helper('string');
		}
		if (!function_exists('url_title')) {
			$_ci->load->helper('url');
		}
		$title = url_title($name, $sep, $lowercase);
		return increment_string($title, $sep, $num);
	}
}

// Sessions Helper
if (!function_exists('session')) {
	function session($type,$name=array(),$value='') {
		$_ci =& get_instance();
		if (!isset($_ci->session)) {
			$_ci->load->library('session');
		}
		switch ($type) {
			case 'set': return $_ci->session->set_userdata($name,$value); break;
			case 'get': return $_ci->session->userdata($name); break;
			case 'all': return $_ci->session->all_userdata(); break;
			case 'unset': return $_ci->session->unset_userdata($name); break;
			case 'kill': return $_ci->session->sess_destroy(); break;
			case 'flashset': return $_ci->session->set_flashdata($name, $value); break;
			case 'flashget': return $_ci->session->flashdata($name); break;
			case 'flashkeep': return $_ci->session->keep_flashdata($name); break;
		}
	}
}

if (!function_exists('set_session')) {
	function set_session ($name=array(), $value='') {
		return session('set', $name, $value);
	}
}

if (!function_exists('get_session')) {
	function get_session ($name=array()) {
		return session('get', $name);
	}
}

if (!function_exists('all_session')) {
	function all_session() {
		return session('all');
	}
}

if (!function_exists('unset_session')) {
	function unset_session ($name=array()) {
		return session('unset', $name);
	}
}

if (!function_exists('kill_session')) {
	function kill_session() {
		return session('kill');
	}
}

if (!function_exists('set_flashdata')) {
	function set_flashdata ($name=array(), $value='') {
		return session('flashset', $name, $value);
	}
}

if (!function_exists('get_flashdata')) {
	function get_flashdata ($name=array()) {
		return session('flashget', $name);
	}
}

if (!function_exists('keep_flashdata')) {
	function keep_flashdata ($name=array()) {
		return session('flashkeep', $name);
	}
}

if (!function_exists('encrypt_this')) {
	function encrypt_this($string) {
		$key = md5('rahendz',true);
		$data = mcrypt_encrypt(MCRYPT_BLOWFISH, $key, $string, 'ecb');
		return bin2hex($data);
	}
}

if (!function_exists('decrypt_this')) {
	function decrypt_this($string) {
		$key = md5('rahendz',true);
		$data = hex2bin($string);
	    $data = mcrypt_decrypt(MCRYPT_BLOWFISH, $key, $data, 'ecb');
	    return $data;
	}
}

if (!function_exists('base64url_encode')) {
	function base64url_encode($data) {
		return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
	}
}

if (!function_exists('base64url_decode')) {
	function base64url_decode($data) {
		return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
	}
}

if (!function_exists('password_hash')) {
	function password_hash($password, $algo, array $options = array()) {
		if (!function_exists('crypt')) {
			trigger_error("Crypt must be loaded for password_hash to function", E_USER_WARNING);
			return null;
		}
		if (is_null($password) || is_int($password)) {
			$password = (string) $password;
		}
		if (!is_string($password)) {
			trigger_error("password_hash(): Password must be a string", E_USER_WARNING);
			return null;
		}
		if (!is_int($algo)) {
			trigger_error("password_hash() expects parameter 2 to be long, " . gettype($algo) . " given", E_USER_WARNING);
			return null;
		}
		$resultLength = 0;
		switch ($algo) {
			case PASSWORD_BCRYPT:
				$cost = PASSWORD_BCRYPT_DEFAULT_COST;
				if (isset($options['cost'])) {
					$cost = (int) $options['cost'];
					if ($cost < 4 || $cost > 31) {
						trigger_error(sprintf("password_hash(): Invalid bcrypt cost parameter specified: %d", $cost), E_USER_WARNING);
						return null;
					}
				}
				$raw_salt_len = 16;
				$required_salt_len = 22;
				$hash_format = sprintf("$2y$%02d$", $cost);
				$resultLength = 60;
				break;
			default:
				trigger_error(sprintf("password_hash(): Unknown password hashing algorithm: %s", $algo), E_USER_WARNING);
				return null;
		}
		$salt_req_encoding = false;
		if (isset($options['salt'])) {
			switch (gettype($options['salt'])) {
				case 'NULL':
				case 'boolean':
				case 'integer':
				case 'double':
				case 'string':
					$salt = (string) $options['salt'];
					break;
				case 'object':
					if (method_exists($options['salt'], '__tostring')) {
						$salt = (string) $options['salt'];
						break;
					}
				case 'array':
				case 'resource':
				default:
					trigger_error('password_hash(): Non-string salt parameter supplied', E_USER_WARNING);
					return null;
			}
			if (Ppass_compat_strlen($salt) < $required_salt_len) {
				trigger_error(sprintf("password_hash(): Provided salt is too short: %d expecting %d", pass_compat_strlen($salt), $required_salt_len), E_USER_WARNING);
				return null;
			} elseif (0 == preg_match('#^[a-zA-Z0-9./]+$#D', $salt)) {
				$salt_req_encoding = true;
			}
		} else {
			$buffer = '';
			$buffer_valid = false;
			if (function_exists('mcrypt_create_iv') && !defined('PHALANGER')) {
				$buffer = mcrypt_create_iv($raw_salt_len, MCRYPT_DEV_URANDOM);
				if ($buffer) {
					$buffer_valid = true;
				}
			}
			if (!$buffer_valid && function_exists('openssl_random_pseudo_bytes')) {
				$strong = false;
				$buffer = openssl_random_pseudo_bytes($raw_salt_len, $strong);
				if ($buffer && $strong) {
					$buffer_valid = true;
				}
			}
			if (!$buffer_valid && @is_readable('/dev/urandom')) {
				$file = fopen('/dev/urandom', 'r');
				$read = 0;
				$local_buffer = '';
				while ($read < $raw_salt_len) {
					$local_buffer .= fread($file, $raw_salt_len - $read);
					$read = pass_compat_strlen($local_buffer);
				}
				fclose($file);
				if ($read >= $raw_salt_len) {
					$buffer_valid = true;
				}
				$buffer = str_pad($buffer, $raw_salt_len, "\0") ^ str_pad($local_buffer, $raw_salt_len, "\0");
			}
			if (!$buffer_valid || pass_compat_strlen($buffer) < $raw_salt_len) {
				$buffer_length = pass_compat_strlen($buffer);
				for ($i = 0; $i < $raw_salt_len; $i++) {
					if ($i < $buffer_length) {
						$buffer[$i] = $buffer[$i] ^ chr(mt_rand(0, 255));
					} else {
						$buffer .= chr(mt_rand(0, 255));
					}
				}
			}
			$salt = $buffer;
			$salt_req_encoding = true;
		}
		if ($salt_req_encoding) {
			$base64_digits = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
			$bcrypt64_digits = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
			$base64_string = base64_encode($salt);
			$salt = strtr(rtrim($base64_string, '='), $base64_digits, $bcrypt64_digits);
		}
		$salt = pass_compat_substr($salt, 0, $required_salt_len);
		$hash = $hash_format . $salt;
		$ret = crypt($password, $hash);
		if (!is_string($ret) || pass_compat_strlen($ret) != $resultLength) {
			return false;
		}
		return $ret;
	}
}

if (!function_exists('password_get_info')) {
	function password_get_info($hash) {
		$return = array(
			'algo' => 0,
			'algoName' => 'unknown',
			'options' => array()
			);
		if (pass_compat_substr($hash, 0, 4) == '$2y$' && pass_compat_strlen($hash) == 60) {
			$return['algo'] = PASSWORD_BCRYPT;
			$return['algoName'] = 'bcrypt';
			list($cost) = sscanf($hash, "$2y$%d$");
			$return['options']['cost'] = $cost;
		}
		return $return;
	}
}

if (!function_exists('password_needs_rehash')) {
	function password_needs_rehash($hash, $algo, array $options = array()) {
		$info = password_get_info($hash);
		if ($info['algo'] !== (int) $algo) {
			return true;
		}
		switch ($algo) {
			case PASSWORD_BCRYPT:
				$cost = isset($options['cost']) ? (int) $options['cost'] : PASSWORD_BCRYPT_DEFAULT_COST;
				if ($cost !== $info['options']['cost']) {
					return true;
				}
				break;
		}
		return false;
	}
}

if (!function_exists('password_verify')) {
	function password_verify($password, $hash) {
		if (!function_exists('crypt')) {
			trigger_error("Crypt must be loaded for password_verify to function", E_USER_WARNING);
			return false;
		}
		$ret = crypt($password, $hash);
		if (!is_string($ret) || pass_compat_strlen($ret) != pass_compat_strlen($hash) || pass_compat_strlen($ret) <= 13) {
			return false;
		}
		$status = 0;
		for ($i = 0; $i < pass_compat_strlen($ret); $i++) {
			$status |= (ord($ret[$i]) ^ ord($hash[$i]));
		}
		return $status === 0;
	}
}

if (!function_exists('pass_compat_strlen')) {
	function pass_compat_strlen($binary_string) {
		if (function_exists('mb_strlen')) {
			return mb_strlen($binary_string, '8bit');
		}
		return strlen($binary_string);
	}
}

if (!function_exists('pass_compat_substr')) {
	function pass_compat_substr($binary_string, $start, $length) {
		if (function_exists('mb_substr')) {
			return mb_substr($binary_string, $start, $length, '8bit');
		}
		return substr($binary_string, $start, $length);
	}
}

if (!function_exists('pass_compat_check')) {
	function pass_compat_check() {
		static $pass = null;
		if (is_null($pass)) {
			if (function_exists('crypt')) {
				$hash = '$2y$04$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG';
				$test = crypt("password", $hash);
				$pass = $test == $hash;
			} else {
				$pass = false;
			}
		}
		return $pass;
	}
}