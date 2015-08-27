<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

/*
|--------------------------------------------------------------------------
| Modular Location
|--------------------------------------------------------------------------
|
| Where system will looking for modular mvc
|
|	Default: APPPATH . 'modules/'
|
*/
$modules_directory = str_replace ( APPPATH, '../', MODULESPATH );
$my_config['modules_locations'] = array ( MODULESPATH => $modules_directory );
$my_config['app_name'] = 'HMVCI';