<?php
// ERROR REPORTING
if ( defined ( 'APP_DEBUG' ) ) {
	switch ( APP_DEBUG ) {
		case true:
			error_reporting ( E_ALL );
			ini_set ( 'display_errors', 1 );
			$environment = 'development';
			break;

		case false:
			error_reporting ( 0 );
			ini_set ( 'display_errors', 0 );
			$environment = 'production';
			break;

		case 'testing':
			ini_set ( 'display_errors', 0 );
			$environment = 'testing';
			if ( version_compare ( PHP_VERSION, '5.3', '>=' ) ) {
				error_reporting ( E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED );
			} else {
				error_reporting ( E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE );
			}
			break;

		default:
			header ( 'HTTP/1.1 503 Service Unavailable.', TRUE, 503 );
			echo 'The application state is not set correctly.';
			exit(1); // EXIT_ERROR
	}
} else {
	header ( 'HTTP/1.1 503 Service Unavailable.', TRUE, 503 );
	echo 'The application debug is not set correctly.';
	exit(1); // EXIT_ERROR
}

// for v3.x
define ( 'ENVIRONMENT', isset ( $_SERVER['CI_ENV'] ) ? $_SERVER['CI_ENV'] : $environment );

// SYSTEM PATH
$system_path = SYSTEM_PATH;

// APP PATH
$application_folder = 'application';

// VIEW PATH for v3.x
$view_folder = '';

// MODULES PATH
$modules_folder = 'modules';



// --------------------------------------------------------------------
// END OF USER CONFIGURABLE SETTINGS.  DO NOT EDIT BELOW THIS LINE
// --------------------------------------------------------------------

/*
 * ---------------------------------------------------------------
 *  Resolve the system path for increased reliability
 * ---------------------------------------------------------------
 */

	// Set the current directory correctly for CLI requests
	if ( defined ( 'STDIN' ) ) {
		chdir ( dirname ( __FILE__ ) );
	}

	if ( realpath ( $system_path ) !== FALSE ){
		$system_path = realpath ( $system_path ) . '/';
	}

	// ensure there's a trailing slash
	$system_path = rtrim ( $system_path, '/' ) . '/';

	// Is the system path correct?
	if ( ! is_dir ( $system_path ) ) {
		exit ( 'Your system folder path does not appear to be set correctly. Please open the following file and correct this: ' . pathinfo ( __FILE__, PATHINFO_BASENAME ) );
	}

/*
 * -------------------------------------------------------------------
 *  Now that we know the path, set the main path constants
 * -------------------------------------------------------------------
 */

	// The name of THIS file
	define ( 'SELF', pathinfo ( __FILE__, PATHINFO_BASENAME ) );

	// The PHP file extension
	// this global constant is deprecated.
	define ( 'EXT', '.' .pathinfo ( __FILE__, PATHINFO_EXTENSION ) );

	// Path to the system folder
	define ( 'BASEPATH', str_replace ( "\\", "/", $system_path ) );

	// Path to the front controller (this file)
	define ( 'FCPATH', str_replace ( SELF, '', __FILE__ ) );

	// Name of the "system folder"
	define ( 'SYSDIR', trim ( strrchr ( trim ( BASEPATH, '/' ), '/' ), '/' ) );


	// The path to the "application" folder
	if ( is_dir ( $application_folder ) ) {
		define ( 'APPPATH', $application_folder . '/' );
	} else {
		if ( ! is_dir ( BASEPATH . $application_folder . '/' ) ) {
			exit ( "Your application folder path does not appear to be set correctly. Please open the following file and correct this: " . SELF );
		}

		define ( 'APPPATH', BASEPATH . $application_folder . '/' );
	}

	// VIEW PATH for v3.x
	// The path to the "views" folder
	if ( ! is_dir($view_folder))
	{
		if ( ! empty($view_folder) && is_dir(APPPATH.$view_folder.DIRECTORY_SEPARATOR))
		{
			$view_folder = APPPATH.$view_folder;
		}
		elseif ( ! is_dir(APPPATH.'views'.DIRECTORY_SEPARATOR))
		{
			header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
			echo 'Your view folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF;
			exit(3); // EXIT_CONFIG
		}
		else
		{
			$view_folder = APPPATH.'views';
		}
	}

	if (($_temp = realpath($view_folder)) !== FALSE)
	{
		$view_folder = $_temp.DIRECTORY_SEPARATOR;
	}
	else
	{
		$view_folder = rtrim($view_folder, '/\\').DIRECTORY_SEPARATOR;
	}

	define('VIEWPATH', $view_folder);


	// The path to the "application" folder
	if ( is_dir ( $modules_folder ) ) {
		define ( 'MODULESPATH', $modules_folder . '/' );
	} elseif ( is_dir ( APPPATH . $modules_folder . '/' ) ) {
		define ( 'MODULESPATH', APPPATH . $modules_folder . '/' );
	} else {
		exit ( "Your modules folder path does not appear to be set correctly. Please open the following file and correct this: " . SELF );
	}

/*
 * --------------------------------------------------------------------
 * LOAD THE BOOTSTRAP FILE
 * --------------------------------------------------------------------
 *
 * And away we go...
 *
 */
require_once APPPATH . 'core/initiate/init.php';
require_once BASEPATH . 'core/CodeIgniter.php';

/* End of file index.php */
/* Location: ./index.php */