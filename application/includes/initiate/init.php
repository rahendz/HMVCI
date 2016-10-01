<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

@require_once dirname ( __FILE__ ) .'/defines.php';
@require_once dirname ( __FILE__ ) .'/unit_test.php';
@require_once dirname ( __FILE__ ) .'/assign.php';
@require_once dirname ( __FILE__ ) .'/routing.php';

$root_files = array ( 'config' );
$core_files = array ( 'functions', 'themes', 'auth', 'format', 'controller', 'rest', 'model' );
$base_files = array ( 'private', 'public', 'api' );

if ( isset ( $my_config ) AND count ( $my_config ) > 0 ) {
	foreach ( $my_config as $c => $i ) {
		$assign_to_config[$c] = $i;
	}
}

// @require_once VENDORPATH . 'autoload.php';