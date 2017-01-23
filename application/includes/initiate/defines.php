<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

defined('EXT') || define('EXT', '.'.pathinfo(__FILE__, PATHINFO_EXTENSION));
defined('INITPATH') || define('INITPATH', INCPATH.basename(__DIR__).'/');

define('DEFAULT_TIMEZONE', 'Asia/Jakarta');
define('TIME_LIMIT', 3600);
define('LIMIT_SIZE', '1024M');

// Switch 'true' if you wanna use REST Api
define ( 'REST_API', false );

if (!defined('PASSWORD_BCRYPT')) {
	define('PASSWORD_BCRYPT', 1);
	define('PASSWORD_DEFAULT', PASSWORD_BCRYPT);
	define('PASSWORD_BCRYPT_DEFAULT_COST', 10);
}