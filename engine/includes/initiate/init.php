<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

@date_default_timezone_set ( 'Asia/Jakarta' );
@set_time_limit ( 3600 );
@ini_set ( 'memory_limit', '1024M' );

// @require_once dirname ( __FILE__ ) .'/autoloader.php';
@require_once dirname ( __FILE__ ) .'/defines.php';
@require_once dirname ( __FILE__ ) .'/unit_test.php';
@require_once dirname ( __FILE__ ) .'/configure.php';
@require_once dirname ( __FILE__ ) .'/routing.php';

if ( class_exists ( 'Autoloader' ) ) {
	Autoloader::getInstance()->register();
}

$root_files = array ( 'config' );
$core_files = array ( 'functions', 'themes', 'auth', 'format', 'controller', 'rest' );
$base_files = array ( 'private', 'public', 'api' );

if ( isset ( $my_config ) AND count ( $my_config ) > 0 ) {
	foreach ( $my_config as $c => $i ) {
		$assign_to_config[$c] = $i;
	}
}

// @require_once VENDORPATH . 'autoload.php';