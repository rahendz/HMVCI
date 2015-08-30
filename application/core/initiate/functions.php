<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

if ( ! function_exists ( '__status_header' ) ) :
	function __status_header ( $code = 200, $text = '' ) {
		$stati = array(
			200	=> 'OK',
			201	=> 'Created',
			202	=> 'Accepted',
			203	=> 'Non-Authoritative Information',
			204	=> 'No Content',
			205	=> 'Reset Content',
			206	=> 'Partial Content',

			300	=> 'Multiple Choices',
			301	=> 'Moved Permanently',
			302	=> 'Found',
			304	=> 'Not Modified',
			305	=> 'Use Proxy',
			307	=> 'Temporary Redirect',

			400	=> 'Bad Request',
			401	=> 'Unauthorized',
			403	=> 'Forbidden',
			404	=> 'Not Found',
			405	=> 'Method Not Allowed',
			406	=> 'Not Acceptable',
			407	=> 'Proxy Authentication Required',
			408	=> 'Request Timeout',
			409	=> 'Conflict',
			410	=> 'Gone',
			411	=> 'Length Required',
			412	=> 'Precondition Failed',
			413	=> 'Request Entity Too Large',
			414	=> 'Request-URI Too Long',
			415	=> 'Unsupported Media Type',
			416	=> 'Requested Range Not Satisfiable',
			417	=> 'Expectation Failed',

			500	=> 'Internal Server Error',
			501	=> 'Not Implemented',
			502	=> 'Bad Gateway',
			503	=> 'Service Unavailable',
			504	=> 'Gateway Timeout',
			505	=> 'HTTP Version Not Supported'
		);

		if ( $code == '' OR ! is_numeric ( $code ) )
		{
			__die ( 'Status codes must be numeric', 500 );
		}

		if ( isset ( $stati[$code] ) AND $text == '' )
		{
			$text = $stati[$code];
		}

		if ( $text == '' )
		{
			__die ( 'No status text available.  Please check your status code number or supply your own message text.', 500 );
		}

		$server_protocol = ( isset ( $_SERVER['SERVER_PROTOCOL'] ) ) ? $_SERVER['SERVER_PROTOCOL'] : false;

		if ( substr ( php_sapi_name(), 0, 3 ) == 'cgi' )
		{
			header ( "Status: {$code} {$text}", true );
		}
		elseif ( $server_protocol == 'HTTP/1.1' OR $server_protocol == 'HTTP/1.0' )
		{
			header ( $server_protocol . " {$code} {$text}", true, $code );
		}
		else
		{
			header ( "HTTP/1.1 {$code} {$text}", true, $code );
		}
	}
endif;

if ( ! function_exists ( '__die' ) ) :
	function __die ( $message, $code = 500, $heading = 'An Error Was Encountered' ) {
		__status_header ( $code );
		include_once FCPATH . 'application/errors/error_general' . EXT; exit;
	}
endif;

if ( ! function_exists ( 'load_controller' ) ) :
	function &load_controller ( $controller ) {
		// $_ci =& get_instance();
		$name = false;

		$controllers_file = APPPATH . 'controllers/' . $controller . EXT;
		if ( file_exists ( $controllers_file ) )
		{
			$name = $controller;
			if ( class_exists ( $name ) === false )
			{
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

		if ( $name === false )
		{
			__die ( 'Unable to locate the specified class: ' . $controllers_file, 404 );
		}
		// ob_start();
		$_controllers = new $name();
		// $buffer = ob_get_contents();
		// @ob_end_clean();
		return $_controllers;
	}
endif;

if ( ! function_exists ( 'load_library' ) ) :
	function &load_library ( $library ) {
		$_ci =& get_instance();
		$subname = $basename = false;
		$modules_locations = config_item ( 'modules_locations' );

		$sub_library_file = APPPATH . 'libraries/' . ucfirst ( $library ) . EXT;

		if ( ! file_exists ( $sub_library_file ) AND ! empty ( $_ci->router->module ) ) {
			$sub_library_file = $modules_locations[0] . $_ci->router->module . '/libraries/' . ucfirst ( $library ) . EXT;
		}

		$base_library_file = BASEPATH . 'libraries/' . ucfirst ( $library ) . EXT;

		$name = 'CI_' . ucfirst ( $library );

		if ( file_exists ( $base_library_file ) AND ! class_exists ( $name ) ) {
			include_once $base_library_file;
		}

		if ( file_exists ( $sub_library_file ) AND ! class_exists ( $name ) ) {
			include_once $sub_library_file;
		}

		$_library = new $name();
		return $_library;
	}
endif;

if ( ! function_exists ( 'load_model' ) ) {
	function &load_model ( $model ) {
		$_ci =& get_instance();
		if ( ! isset ( $_ci->$model ) ) {
			$_ci->load->model ( $model );
		}
		return $_ci->$model;
	}
}

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

if ( ! function_exists ( 'redirect' ) ) :
	function redirect ( $uri = '/', $method = 'location', $http_response_code = 302 ) {
		$_ci =& get_instance();
		$type = $uri === '/' ? 'base_url' : 'site_url';
		$redirect_to = '?redirect=' . urlencode ( $_ci->config->site_url ( $_ci->uri->uri_string() ) );
		if ( ! preg_match ( '#^https?://#i', $uri ) ) $uri = $_ci->config->$type($uri);
		switch ( $method ) :
			case 'refresh' : header ( "Refresh:0;url=" . $uri ); break;
			case 'meta' : return '<meta http-equiv="refresh" content="' . $http_response_code . '; url=' . $uri . '">' . "\n"; break;
			case 'redirect' : header ( "Location: " . $uri . $redirect_to, true, $http_response_code ); break;
			default : header ( "Location: " . $uri, true, $http_response_code ); break;
		endswitch; exit;
	}
endif;

if ( ! function_exists ( 'get_input' ) ) :
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
endif;

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

if ( ! function_exists ( 'load_views' ) ) :
	function load_views ( $view_path, $vars = array() ) {
		$_ci =& get_instance();
		return $_ci->load->view ( $view_path, $vars );
	}
endif;

if ( ! function_exists ( 'get_views' ) ) :
	function get_views ( $view_path, $vars = array() ) {
		$_ci =& get_instance();
		return $_ci->load->view ( $view_path, $vars, true );
	}
endif;

if ( ! function_exists ( 'is_input' ) ) :
	function is_input ( $type, $name = null, $value = null ) {
		if ( ! is_null ( $value ) ) {
			return get_input ( $type, $name ) !== $value ? false : true;
		}
		return ! get_input ( $type, $name ) ? false : true;
	}
endif;

if ( ! function_exists ( 'is_post' ) AND ! function_exists ( 'is_get' ) ) {
	function is_post ( $name, $value = null ) {
		return is_input ( 'post', $name, $value );
	}
	function is_get ( $name, $value = null ) {
		return is_input ( 'get', $name, $value );
	}
}

if ( ! function_exists ( 'current_url_string' ) ) :
	function current_url_string() {
		$_ci =& get_instance();
		$query_string = ( isset ( $_SERVER['QUERY_STRING'] ) AND ! empty ( $_SERVER['QUERY_STRING'] ) ) ?
			'?' . $_SERVER['QUERY_STRING'] : null;
		return $_ci->uri->uri_string() . $query_string;
	}
endif;

if ( ! function_exists ( 'site_url' ) ) :
	function site_url ( $path = null ) {
		$_ci =& get_instance();
		if ( is_null ( $path ) ) $url = null;
		elseif ( ! $path ) $url = current_url_string();
		else $url = $path;
		return $_ci->config->site_url ( $url );
	}
endif;

if ( ! function_exists ( 'base_url' ) ) :
	function base_url ( $path = null ) {
		$_ci =& get_instance();
		return $_ci->config->base_url ( $path );
	}
endif;

if ( ! function_exists ( 'date_translate' ) ) :
	function date_translate ( $date, $from = 'en', $to = 'id' ) {
		$en = array (
			'January', 'February', 'March', 'May', 'June',
			'July', 'August', 'October', 'December', 'Sunday',
			'Monday', 'Tuesday', 'Wednesday', 'Thursday',
			'Friday', 'Saturday' );
		$id = array (
			'Januari', 'Februari', 'Maret', 'Mei', 'Juni',
			'Juli', 'Agustus', 'Oktober', 'Desember', 'Minggu',
			'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat',
			'Sabtu' );
		return ( is_array ( $from ) AND is_array ( $to ) ) ?
			str_replace ( $from, $to, $date ) :
			str_replace ( $$from, $$to, $date );
	}
endif;

if  ( ! function_exists ( 'array_column' ) ) :
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
endif;

if ( ! function_exists ( 'array_flatten' ) ) :
	function array_flatten ( $array, $flattened = array() ) {
		return call_user_func_array ( 'array_merge', $array );
	}
endif;

if ( ! function_exists ( 'recursive_array_search' ) ) :
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
endif;

if ( ! function_exists ( 'recursive_array_search_key' ) ) :
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
endif;

if ( ! function_exists ( 'benchmark_start' ) ) :
	function benchmark_start ( $slug ) {
		$_ci =& get_instance();
		$mark_name = $slug . '_start';
		return $_ci->benchmark->mark ( $mark_name );
	}
endif;

if ( ! function_exists ( 'benchmark_end' ) ) :
	function benchmark_end ( $slug ) {
		$_ci =& get_instance();
		$mark_name = $slug . '_end';
		return $_ci->benchmark->mark ( $mark_name );
	}
endif;

if ( ! function_exists ( 'benchmark_time' ) ) :
	function benchmark_time ( $slug ) {
		$_ci =& get_instance();
		$mark_start = $slug . '_start';
		$mark_end = $slug . '_end';
		$mark_time = str_replace ( ',', '', $_ci->benchmark->elapsed_time ( $mark_start, $mark_end ) );
		$mark_suffix_time = $mark_time < 60 ? ' detik' : ( $mark_time < 3600 ? ' menit' : ( $mark_time < 86400 ? ' jam' : ' hari' ) );
		$mark_exact_time = $mark_time < 60 ? $mark_time : ( $mark_time < 3600 ? $mark_time / 60 : ( $mark_time < 86400 ? $mark_time / 3600 : $mark_time / 86400 ) );
		return floor ( $mark_exact_time ) . $mark_suffix_time;
	}
endif;

if ( ! function_exists ( 'date_range' ) ) :
	function date_range ( $date1, $date2, $duration = false ) {
		$date1 = strtotime ( $date1 );
		$date2 = strtotime ( $date2 );
		if ( $date1 == $date2 AND $duration ) return array();
		if ( $date1 == $date2 AND ! $duration ) return array ( $date1 );
		$first = $date1 < $date2 ? $date1 : $date2;
		$last = $date1 > $date2 ? $date1 : $date2;
		if ( ! $duration ) $date_range[] = date ( 'Y-m-d', $first );
		while ( $first != $last ) :
			$first = mktime ( 0, 0, 0, date ( "m", $first ), date ( "d", $first ) + 1, date ( "Y", $first ) );
			$date_range[] = date ( 'Y-m-d', $first );
		endwhile;
		return $date_range;
	}
endif;

if ( ! function_exists ( 'date_duration' ) ) :
	function date_duration ( $date1, $date2 ) {
		return date_range ( $date1, $date2, true );
	}
endif;

if ( ! function_exists ( 'pagination' ) ) :
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
endif;

if ( ! function_exists ( 'do_upload' ) ) :
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
endif;

if ( ! function_exists ( 'initiate_db' ) ) :
	function &initiate_db() {
		$_ci =& get_instance();
		$_ci->load->config ( 'database', false, true );
		$dbparam = ! config_item ( 'default' ) ? '' : config_item ( 'default' );
		unset ( $_ci->db );
		$db = $_ci->load->database ( $dbparam, true );
		return $db;
	}
endif;

if ( ! function_exists ( 'list_tables' ) ) :
	function list_tables() {
		$_db =& initiate_db();
		return $_db->list_tables();
	}
endif;

if ( ! function_exists ( 'table_exists' ) ) :
	function table_exists ( $table ) {
		$_db =& initiate_db();
		return $_db->table_exists ( $_db->dbprefix ( $table ) );
	}
endif;

if ( ! function_exists ( 'list_fields' ) ) :
	function list_fields() {
		$_db =& initiate_db();
		return $_db->list_fields();
	}
endif;

if ( ! function_exists ( 'field_exists' ) ) :
	function field_exists ( $field, $table ) {
		$_db =& initiate_db();
		return $_db->field_exists ( $field, $_db->dbprefix ( $table ) );
	}
endif;

if ( ! function_exists ( 'field_data' ) ) :
	function field_data ( $table = null ) {
		$_db =& initiate_db();
		return $_db->field_data ( $_db->dbprefix ( $table ) );
	}
endif;

if ( ! function_exists ( 'fields_data' ) ) :
	function fields_data ( $table = null ) {
		$_db =& initiate_db();
		$query = $_db->query ( 'SHOW COLUMNS FROM ' . $_db->dbprefix ( $table ) );
		if ( $query->num_rows() > 0 ) {
			// while ( $row = $query->result() ) {
			echo_r ( $query->result() );
			// }
		}
	}
endif;

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
if ( ! function_exists ( 'db_tools' ) ) :
	function db_tools ( $func, $data, $param = false ) {
		$_ci =& get_instance();
		$_ci->load->dbforge();
		return $_ci->dbforge->$func ( $data, $param );
	}
endif;

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
if ( ! function_exists ( 'db_utility' ) ) :
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
endif;

if ( ! function_exists ( 'get_remote' ) ) :
	function get_remote ( $source, $format = 'json' ) {
		$server = config_item ( 'api_baseurl' ) ? config_item ( 'api_baseurl' ) .'/' : null;

		if ( config_item ( 'api_logins' ) ) {
			$username = key ( config_item ( 'api_logins' ) );
			$password = current ( config_item ( 'api_logins' ) );
		}

		$curl_handle = curl_init();
		curl_setopt ( $curl_handle, CURLOPT_URL, $server . $source );
		curl_setopt ( $curl_handle, CURLOPT_RETURNTRANSFER, 1 );

		if ( isset ( $username ) AND isset ( $password ) ) {
			curl_setopt ( $curl_handle, CURLOPT_USERPWD, $username . ':' . $password );
			curl_setopt ( $curl_handle,  CURLOPT_HTTPAUTH, CURLAUTH_DIGEST );
		}

		$buffer = curl_exec ( $curl_handle );
		curl_close ( $curl_handle );

		header('Content-Type: application/json');
		return $buffer;
	}
endif;

if ( ! function_exists ( 'post_remote' ) ) :
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

		header('Content-Type: application/json');
		return $buffer;
	}
endif;

if ( ! function_exists ( 'get_template_dir' ) ) {
	function get_template_dir ( $path = null ) {
		$_ci =& get_instance();
		return $_ci->load->theme_dir . $path;
	}
}

if ( ! function_exists ( 'call_login_form' ) ) {
	function call_login_form ( $alert = null ) {
		$_ci =& get_instance();
		extract ( $_ci->load->views_data );

		$default_login = 'login';

		if ( isset ( $alert_msg ) ) {
			$alert = '<div class="alert alert-warning alert-thin text-center">' . $alert_msg . '</div>';
		} elseif ( is_get ( 'logged_out', 'true' ) ) {
			$alert = '<div class="alert alert-warning alert-thin text-center">Anda berhasil keluar.</div>';
		}

		if ( file_exists ( get_template_dir ( $default_login . EXT ) ) ) {
			include get_template_dir ( $default_login . EXT );
		} else {
			include INIT_VIEWS . 'login_form' . EXT;
		}
	}
}

if ( ! function_exists ( 'is_updated' ) ) {
	function is_updated() {
		if ( is_get ( 'status_updated', 'true' ) OR is_get ( 'status_added', 'true' ) OR is_get ( 'status_trashed', 'true' ) OR is_get ( 'status_restored', 'true' ) ) {
			return true;
		} elseif ( is_get ( 'status_updated', 'false' ) OR is_get ( 'status_added' ,'false' ) OR is_get ( 'status_trashed', 'false' ) OR is_get ( 'status_restored', 'false' ) ) {
			return false;
		} else {
			return null;
		}
	}
}

if ( ! function_exists ( 'get_option' ) ) {
	function get_option ( $name ) {
		$_ci =& get_instance();
		$_ci->load->model ( 'm_settings', 'settings' );
		return $_ci->settings->get_option ( $name );
	}
}

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