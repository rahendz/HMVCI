<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

defined('EXT') || define('EXT', '.'.pathinfo(__FILE__, PATHINFO_EXTENSION));
defined('INITPATH') || define('INITPATH', INCPATH.basename(__DIR__).'/');

// Switch 'true' if you wanna use REST Api
define ( 'REST_API', false );