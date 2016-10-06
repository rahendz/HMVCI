<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

$init_files = array('defines', 'unit_test', 'assign', 'routing');
foreach ($init_files as $file) { @require_once dirname(__FILE__)."/{$file}.php"; }

$root_files = array ( 'config' );
$core_files = array ( 'functions', 'themes', 'auth', 'format', 'controller', 'rest', 'model' );
$base_files = array ( 'private', 'public', 'api' );

if (isset($my_config) && count($my_config)>0) {
	foreach ($my_config as $c => $i) {
		$assign_to_config[$c] = $i;
	}
}

// @require_once VENDORPATH . 'autoload.php';