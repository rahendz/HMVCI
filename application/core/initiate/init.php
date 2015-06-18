<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

define ( 'INIT', TRUE );
defined ( 'EXT' ) OR define ( 'EXT', pathinfo ( __FILE__, PATHINFO_EXTENSION ) );

@date_default_timezone_set ( 'Asia/Jakarta' );
@set_time_limit ( 3600 );
@ini_set ( 'memory_limit', '1024M' );

@require_once dirname ( __FILE__ ). '/defines' .EXT;

$core_files = array ( 'functions', 'themes' );

if ( ! REST_API ) {
	$core_files[] = 'controller';
} else {
	$core_files[] = 'format';
	$core_files[] = 'rest';
}

$base_files = array ( 'private', 'public' );

@require_once dirname ( __FILE__ ). '/unit_test'.EXT;
@require_once dirname ( __FILE__ ). '/config'.EXT;

if ( isset ( $my_config ) AND count ( $my_config ) > 0 ) {
	foreach ( $my_config as $c => $i ) {
		$assign_to_config[$c] = $i;
	}
}

$modules_locations = MODULES_LOCATIONS;

@require_once VENDORPATH. 'autoload' .EXT;