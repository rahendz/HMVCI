<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

@date_default_timezone_set ( 'Asia/Jakarta' );
@set_time_limit ( 3600 );
@ini_set ( 'memory_limit', '1024M' );

defined ( 'EXT' ) OR define ( 'EXT', pathinfo ( __FILE__, PATHINFO_EXTENSION ) );

define ( 'MODULES_LOCATIONS', APPPATH .'modules/' );

define ( 'REST_API', FALSE );
define ( 'REST_API_REALM', 'REST API' );
define ( 'REST_API_USERNAME', 'admin' );
define ( 'REST_API_PASSWORD', '1234' );

$core_files = array ( 'functions', 'themes', 'format', ( ! REST_API ? 'controller' : 'rest' ) );
$base_files = array ( 'private', 'public' );

@require_once dirname ( __FILE__ ) .'/unit_test'. EXT;
@require_once dirname ( __FILE__ ) .'/config'. EXT;

if ( isset ( $my_config ) AND count ( $my_config ) > 0 )
	foreach ( $my_config as $c => $i ) $assign_to_config[$c] = $i;

$modules_locations = current ( $assign_to_config['modules_locations'] );