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
	define ( 'SYSTEM_PATH', '../../cores/3.0.1' );

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
	define ( 'APP_DEBUG', true );

	// END OF EDITING FILE
	// The name of THIS file
	define ( 'SELF', pathinfo ( __FILE__, PATHINFO_BASENAME ) );

	// Load system
	include 'system.php';