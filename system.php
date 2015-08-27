<?php

// ERROR REPORTING
if ( defined ( 'APP_DEBUG' ) ) {
	switch ( APP_DEBUG ) {
		case true:
			error_reporting ( E_ALL );
			break;

		case false:
			error_reporting ( 0 );
			break;

		default:
			exit ( 'The application state is not set correctly.' );
	}
}

// SYSTEM PATH
$system_path = SYSTEM_PATH;

// APP PATH
$application_folder = 'application';

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
		exit ( "Your system folder path does not appear to be set correctly. Please open the following file and correct this: " . pathinfo ( __FILE__, PATHINFO_BASENAME ) );
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
	define ( 'EXT', '.php' );

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