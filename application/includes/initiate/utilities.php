<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );
// echo 'unit test loaded';

if (!function_exists('__status_header')) {
	function __status_header ($code=200, $text='') {
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

		if ($code=='' || !is_numeric($code)) {
			__die('Status codes must be numeric', 500);
		}

		if (isset($stati[$code]) && $text=='') {
			$text = $stati[$code];
		}

		if ($text=='') {
			__die('No status text available.  Please check your status code number or supply your own message text.', 500);
		}

		$server_protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : false;

		if (substr(php_sapi_name(), 0, 3 )=='cgi') {
			header("Status: {$code} {$text}", true);
		}
		elseif ($server_protocol=='HTTP/1.1' || $server_protocol=='HTTP/1.0') {
			header($server_protocol . " {$code} {$text}", true, $code);
		}
		else {
			header("HTTP/1.1 {$code} {$text}", true, $code);
		}
	}
}

if (!function_exists('__die')) {
	function __die ($message, $code=500, $heading='An Error Was Encountered', $path_error='errors', $file_error='error_general') {
		__status_header($code);
		@include_once APPPATH.$path_error.'/'.$file_error.EXT;
		exit(1);
	}
}

if (!function_exists('__is_true')) {
	function __is_true ($test) {
		return (is_bool($test) && $test===true) ? true : false;
	}
}

if (!function_exists('__is_false')) {
	function __is_false ($test) {
		return (is_bool($test) && $test===false) ? true : false;
	}
}

if (!function_exists('__e')) {
	function __e($string, $exit=false) {
		echo $string;
		if ($exit) {
			exit;
		}
	}
}

if (!function_exists('__ej')) {
	function __ej ($data, $exit=false) {
		header ( 'Content-Type: application/json' );
		if ($exit) {
			exit($data);
		}
		echo $data;
	}
}

if (!function_exists('__j')) {
	function __j ($data, $exit=false) {
		$return = json_encode($data);
		header ( 'Content-Type: application/json' );
		if ($exit) {
			exit($return);
		}
		echo $return;
	}
}

if (!function_exists('__x')) {
	function __x($data,$exit=false) {
		echo '<pre>';
		var_dump($data);
		echo '</pre>';
		if ($exit) {
			exit;
		}
	}
}

if (!function_exists('__r')) {
	function __r($data,$exit=false) {
		echo '<pre>';
		print_r($data);
		echo '</pre>';
		if ($exit) {
			exit;
		}
	}
}

if (!function_exists('__is_version')) {
	function __is_version ($version, $operator = '=') {
		switch($operator) {
			default: case '=': return CI_VERSION == $version ? true : false; break;
			case '<': return CI_VERSION < $version ? true : false; 	break;
			case '>': return CI_VERSION > $version ? true : false; break;
			case '<=': return CI_VERSION <= $version ? true : false; break;
			case '>=': return CI_VERSION >= $version ? true : false; break;
		}
	}
}