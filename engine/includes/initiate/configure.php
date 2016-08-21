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


// RESTful SERVER API Configuration

# HTTP protocol
$my_config['force_https'] = false;

# REST Format ( 'array', 'csv', 'json', 'html', 'php', 'serialized', 'xml' )
$my_config['rest_default_format'] = 'json';

# REST Status Field Name
$my_config['rest_status_field_name'] = 'status';

# REST Message Field Name
$my_config['rest_message_field_name'] = 'error';

# Enable Emulate Request
$my_config['enable_emulate_request'] = true;

# REST Realm (window popup title)
$my_config['rest_realm'] = REST_API_REALM;

# REST Login ( false, 'basic', 'digest', 'session' )
$my_config['rest_auth'] = false;

# REST Login Source ( '', 'ldap', 'library' )
# Note: If 'rest_auth' is set to 'session' then change 'auth_source' to the name of the session variable
$my_config['auth_source'] = '';

# REST Login Class and Function
$my_config['auth_library_class'] = '';
$my_config['auth_library_function'] = '';

# Override auth types for specific class/method
// $my_config['auth_override_class_method']['deals']['view'] = 'none';
// $my_config['auth_override_class_method']['deals']['insert'] = 'digest';
// $my_config['auth_override_class_method']['accounts']['user'] = 'basic';
// $my_config['auth_override_class_method']['dashboard']['*'] = 'basic';


// ---Uncomment list line for the wildard unit test
// $my_config['auth_override_class_method']['wildcard_test_cases']['*'] = 'basic';

# Override auth types for specfic 'class/method/HTTP method'
// ---Uncomment list line for the wildard unit test
// $my_config['auth_override_class_method_http']['wildcard_test_cases']['*']['options'] = 'basic';

# REST Login Usernames
$my_config['rest_valid_logins'] = array ( REST_API_USERNAME => REST_API_PASSWORD );

# Global IP Whitelisting
$my_config['rest_ip_whitelist_enabled'] = false;

# REST IP Whitelist
$my_config['rest_ip_whitelist'] = '';

# Global IP Blacklisting
$my_config['rest_ip_blacklist_enabled'] = false;

# REST IP Blacklist
$my_config['rest_ip_blacklist'] = '';

# REST Database Group
$my_config['rest_database_group'] = 'default';

# REST API Keys Table Name
$my_config['rest_keys_table'] = 'keys';

# REST Enable Keys
/*

Default table schema:
CREATE TABLE `keys` (
   `id` INT(11) NOT NULL AUTO_INCREMENT,
   `key` VARCHAR(40) NOT NULL,
   `level` INT(2) NOT NULL,
   `ignore_limits` TINYINT(1) NOT NULL DEFAULT '0',
   `is_private_key` TINYINT(1)  NOT NULL DEFAULT '0',
   `ip_addresses` TEXT NULL DEFAULT NULL,
  `date_created` INT(11) NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

*/
$my_config['rest_enable_keys'] = REST_API_KEYS;

# REST Table Key Column Name
$my_config['rest_key_column'] = 'key';

# REST API Limits method ( 'API_KEY', 'METHOD_NAME', 'ROUTED_URL' )
$my_config['rest_limits_method'] = 'API_KEY';

# REST Key Length
$my_config['rest_key_length'] = 40;

# REST API Key Variable
$my_config['rest_key_name'] = 'secret';

# REST Enable Logging
/*
|
| Default table schema:
|   CREATE TABLE `logs` (
|       `id` INT(11) NOT NULL AUTO_INCREMENT,
|       `uri` VARCHAR(255) NOT NULL,
|       `method` VARCHAR(6) NOT NULL,
|       `params` TEXT DEFAULT NULL,
|       `api_key` VARCHAR(40) NOT NULL,
|       `ip_address` VARCHAR(45) NOT NULL,
|       `time` INT(11) NOT NULL,
|       `rtime` FLOAT DEFAULT NULL,
|       `authorized` VARCHAR(1) NOT NULL,
|       `response_code` smallint(3) DEFAULT '0',
|       PRIMARY KEY (`id`)
|   ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
|
*/
$my_config['rest_enable_logging'] = FALSE;

# REST API Logs Table Name
$my_config['rest_logs_table'] = 'logs';

# REST Method Access Control
/*
|
| Default table schema:
|   CREATE TABLE `access` (
|       `id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
|       `key` VARCHAR(40) NOT NULL DEFAULT '',
|       `controller` VARCHAR(50) NOT NULL DEFAULT '',
|       `date_created` DATETIME DEFAULT NULL,
|       `date_modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
|       PRIMARY KEY (`id`)
|    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
|
*/
$my_config['rest_enable_access'] = false;

# REST API Access Table Name
$my_config['rest_access_table'] = 'access';

# REST API Param Log Format
$my_config['rest_logs_json_params'] = false;

# REST Enable Limits
/*
|
| Default table schema:
|   CREATE TABLE `limits` (
|       `id` INT(11) NOT NULL AUTO_INCREMENT,
|       `uri` VARCHAR(255) NOT NULL,
|       `count` INT(10) NOT NULL,
|       `hour_started` INT(11) NOT NULL,
|       `api_key` VARCHAR(40) NOT NULL,
|       PRIMARY KEY (`id`)
|   ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
|
| To specify the limits within the controller's __construct() method, add per-method
| limits with:
|
|       $this->method['METHOD_NAME']['limit'] = [NUM_REQUESTS_PER_HOUR];
|
| See application/controllers/api/example.php for examples
*/
$my_config['rest_enable_limits'] = false;

# REST API Limits Table Name
$my_config['rest_limits_table'] = 'limits';

# REST Ignore HTTP Accept
$my_config['rest_ignore_http_accept'] = false;

# REST AJAX Only
$my_config['rest_ajax_only'] = false;

# REST Language File
$my_config['rest_language'] = 'english';


// RESTful CLIENT API Configuration

# REST API Base Url
$my_config['api_base_url'] = REST_API_BASE_URL;

# REST API Logins
$my_config['api_logins'] = array ( REST_API_USERNAME => REST_API_PASSWORD );