<?php
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
 *     development / (bool) true
 *     testing
 *     production / (bool) false
 *
 * NOTE: If you change these, also change the error_reporting() code
 *
 */
	define('APP_DEBUG', true);

// END OF EDITING FILE
@include('../autoloader.php');

if(!defined('BASEPATH')){
	header ( 'HTTP/1.1 500 Internal Server Error.', TRUE, 50 );
	echo 'System just not responding, check the requirement.';
	exit(1);
}