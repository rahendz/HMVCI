<?php if ( ! defined ( 'INIT' ) ) exit ( 'No direct script access allowed' );

define ( 'INITPATH', dirname(__FILE__) );
define ( 'INITVIEWS', INITPATH . '/views/' );

define ( 'REST_API', FALSE );
define ( 'REST_API_REALM', 'REST API' );
define ( 'REST_API_USERNAME', 'admin' );
define ( 'REST_API_PASSWORD', '1234' );

define ( 'MODULES_LOCATIONS', APPPATH .'modules/' );

define ( 'VENDORPATH', APPPATH . 'core/vendor/' );