<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );
// echo 'init loaded';

@date_default_timezone_set ( 'Asia/Jakarta' );
@set_time_limit ( 3600 );
@ini_set ( 'memory_limit', '1024M' );

defined ( 'EXT' ) OR define ( 'EXT', '.' . pathinfo ( __FILE__, PATHINFO_EXTENSION ) );

$root_files = array ( 'config' );
$core_files = array ( 'functions', 'themes', 'controller', 'auth' );
$base_files = array ( 'private', 'public' );

@require_once dirname ( __FILE__ ) .'/defines'. EXT;
@require_once dirname ( __FILE__ ) .'/unit_test'. EXT;
@require_once dirname ( __FILE__ ) .'/configurations'. EXT;
@require_once dirname ( __FILE__ ) .'/routing'. EXT;

if ( isset ( $my_config ) AND count ( $my_config ) > 0 ) {
	foreach ( $my_config as $c => $i ) {
		$assign_to_config[$c] = $i;
	}
}

// @require_once VENDORPATH . 'autoload.php';