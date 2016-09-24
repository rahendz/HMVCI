<?php

/*
 *---------------------------------------------------------------
 * SYSTEM FOLDER NAME
 *---------------------------------------------------------------
 *
 * This variable must contain the name of your "system" folder.
 * Include the path if the folder is not in the same  directory
 * as this file.
 *
 */
	define('SYSTEM_PATH', 'system');

/*
 *---------------------------------------------------------------
 * APPLICATION STATE
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     testing
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code
 *
 */
	define('APP_DEBUG', true);

// END OF EDITING FILE
// Load system
@include('load.php');

// Returning header error notice while basepath constant where not defined
if(!defined('BASEPATH')){
	header ( 'HTTP/1.1 500 Internal Server Error.', TRUE, 50 );
	echo 'System just not responding, check the requirement.';
	exit(1);
}