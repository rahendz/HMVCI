<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

defined ( 'EXT' ) OR define ( 'EXT', '.' . pathinfo ( __FILE__, PATHINFO_EXTENSION ) );

define ( 'INITPATH', dirname(__FILE__) .'\\' );
define ( 'INITVIEWS', INITPATH . '/views/' );

define ( 'REST_API', false );
define ( 'REST_API_REALM', 'REST API' );
define ( 'REST_API_USERNAME', 'admin' );
define ( 'REST_API_PASSWORD', '1234' );
define ( 'REST_API_KEYS', true );
// set api base url `false` to use this system as a rest server
define ( 'REST_API_BASE_URL', false );

// define ( 'MODULES_LOCATIONS', APPPATH .'modules/' );
// define ( 'APPDIR', trim ( str_replace ( trim ( FCPATH, '/' ) . '\\', '', trim ( APPPATH, '\\' ) ), '/' ) . '/' );

// define ( 'VENDORPATH', APPPATH . 'core/vendor/' );