<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

$init_files = array('defines', 'utilities', 'assign', 'routing');
foreach ($init_files as $file) { @require_once dirname(__FILE__).DIRECTORY_SEPARATOR."{$file}".EXT; }

$root_files = array('config');
$core_files = array('functions', 'themes', 'auth', 'format', 'controller', 'rest', 'model');
$base_files = array('private', 'public', 'api');

if (isset($my_config) && count($my_config)>0) {
	foreach ($my_config as $c => $i) {
		$assign_to_config[$c] = $i;
	}
}