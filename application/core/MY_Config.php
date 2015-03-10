<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

class MY_Config extends CI_Config {}

function __autoload ( $class ) {
	global $modules_locations;

	$to_loaded = array (
		APPPATH .'models/'. strtolower ( $class ) . EXT,
		$modules_locations .'models/'. strtolower ( $class ) . EXT
		);

	foreach ( $to_loaded as $t ) {
		if ( file_exists ( $t ) ) {
			require_once $t;
		}
	}
}