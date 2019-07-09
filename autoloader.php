<?php
// ERROR REPORTING
if (defined('APP_DEBUG')) {
	switch (APP_DEBUG) {
		case true: case 'development':
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
			$environment = 'development';
			break;

		case false: case 'production':
			error_reporting (0);
			ini_set('display_errors', 0);
			$environment = 'production';
			break;

		case 'testing':
			if (version_compare(PHP_VERSION, '5.3', '>=')) {
				error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
			} else {
				error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
			}
			ini_set('display_errors', 0);
			$environment = 'testing';
			break;

		default:
			header('HTTP/1.1 503 Service Unavailable.', true, 503);
			echo 'The application state is not set correctly.';
			exit(1); // EXIT_ERROR
	}
}
else {
	header('HTTP/1.1 503 Service Unavailable.', true, 503);
	echo 'The application debug is not set correctly.';
	exit(1); // EXIT_ERROR
}

// for v3.x
define('ENVIRONMENT', isset($_SERVER['CI_ENV'])?$_SERVER['CI_ENV']:$environment);

// --------------------------------------------------------------------
// END OF USER CONFIGURABLE SETTINGS.  DO NOT EDIT BELOW THIS LINE
// --------------------------------------------------------------------

if (!is_file(dirname(__FILE__).DIRECTORY_SEPARATOR.'paths.'.pathinfo(__FILE__,PATHINFO_EXTENSION))) {
	header('HTTP/1.1 500 Internal Server Error.', true, 500);
	(APP_DEBUG == false) OR exit('Your paths does not loaded or missing. Please open the following file and correct this or make sure that file is there: paths.php');
}
@require 'paths.php';


/*
 * ---------------------------------------------------------------
 *  Resolve the system path for increased reliability
 * ---------------------------------------------------------------
 */
	extract($_SERVER);
	if (!isset($document_root)) {
		$document_root = realpath(dirname(__FILE__).$root_folder).DIRECTORY_SEPARATOR;
	}

	// Set the current directory correctly for CLI requests
	if (defined('STDIN')) {
		chdir(dirname(__FILE__));
	}

	if (($_temp = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.$system_path)) !== FALSE){
		$system_path = $_temp.DIRECTORY_SEPARATOR;
	}
	else {
		// ensure there's a trailing slash
		$system_path = strtr(
			rtrim($system_path, '/\\'),
			'/\\',
			DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
		).DIRECTORY_SEPARATOR;
	}
	// Is the system path correct?
	if (!is_dir($system_path)) {
		header('HTTP/1.1 500 Internal Server Error.', true, 500);
		exit('Your system folder path does not appear to be set correctly. Please open the following file and correct this: paths.php');
	}

/*
 * -------------------------------------------------------------------
 *  Now that we know the path, set the main path constants
 * -------------------------------------------------------------------
 */

	// The name of THIS file
	define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

	// The PHP file extension
	// this global constant is deprecated.
	define('EXT', '.'.pathinfo(__FILE__, PATHINFO_EXTENSION));

	// Path to the system folder
	define('BASEPATH', $system_path);

	// Path to the front controller (this file)
	define('FCPATH', dirname(__FILE__).DIRECTORY_SEPARATOR);

	// Name of the "system folder"
	define('SYSDIR', trim(strrchr(trim(BASEPATH, DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR));

	// Path to the root folder, usually the public
	define('ROOTPATH', rtrim(str_replace('\\', DIRECTORY_SEPARATOR, $document_root),DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR);

	// The path to the "application" folder
	if (is_dir($application_folder)) {
		define('APPPATH', $application_folder.DIRECTORY_SEPARATOR);
		} elseif (!is_dir(BASEPATH.$application_folder.DIRECTORY_SEPARATOR)) {
			header('HTTP/1.1 500 Internal Server Error.', true, 500);
			exit("Your application folder path does not appear to be set correctly. Please open the following file and correct this: paths.php");
		} else {
			define('APPPATH', BASEPATH.$application_folder.DIRECTORY_SEPARATOR);
	}

	// VIEW PATH for v3.x
	// The path to the "views" folder
	if (!is_dir($view_folder)) {
		if (!empty($view_folder) && is_dir(APPPATH.$view_folder.DIRECTORY_SEPARATOR)) {
			$view_folder = APPPATH.$view_folder;
			}
			elseif (!is_dir(APPPATH.'views'.DIRECTORY_SEPARATOR)) {
				header('HTTP/1.1 500 Internal Server Error.', true, 500);
				echo 'Your view folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF;
				exit(3); // EXIT_CONFIG
			} else {
				$view_folder = APPPATH.'views';
		}
	}

	if (($_temp=realpath($view_folder))!==FALSE) {
		$view_folder = $_temp.DIRECTORY_SEPARATOR;
		}
		else {
			$view_folder = rtrim($view_folder, '/\\').DIRECTORY_SEPARATOR;
	}

	define('VIEWPATH', $view_folder);


	// The path to the "modules" folder
	if (is_dir($modules_folder)) {
		define('MODULESPATH', $modules_folder.'/');
		}
		elseif(is_dir(APPPATH.$modules_folder.'/')) {
			define('MODULESPATH', APPPATH.$modules_folder.'/');
		}
		else {
			header('HTTP/1.1 500 Internal Server Error.', true, 500);
			exit("Your modules folder path does not appear to be set correctly. Please open the following file and correct this: paths.php");
	}

	// The path to the "public" folder
	if(is_dir(realpath('.'.DIRECTORY_SEPARATOR).$public_folder)) {
		define('PUBLICPATH', FCPATH.((!isset($public_folder)||empty($public_folder))?'public':$public_folder).DIRECTORY_SEPARATOR);
		}
		else {
			header('HTTP/1.1 500 Internal Server Error.', true, 500);
			exit("Ups! Missing public directory.");
	}

// The path to the "application" folder
	if(is_dir($includes_folder)) {
		define('INCPATH', FCPATH.$includes_folder.'/');
		}
		elseif(is_dir(APPPATH.$includes_folder)) {
			define('INCPATH', APPPATH.$includes_folder.'/');
		}
		else {
			header('HTTP/1.1 500 Internal Server Error.', true, 500);
			exit("Includes folder are missing. Please open the following file and correct this: paths.php");
	}

/*
 * --------------------------------------------------------------------
 * LOAD THE BOOTSTRAP FILE
 * --------------------------------------------------------------------
 *
 * And away we go...
 *
 */
require_once INCPATH.'initiate'.DIRECTORY_SEPARATOR.'init'.EXT;
require_once BASEPATH.'core'.DIRECTORY_SEPARATOR.'CodeIgniter'.EXT;

/* End of file index.php */
/* Location: ./index.php */