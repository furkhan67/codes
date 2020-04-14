<?php
/**
// Set the required php modules for your Application
$config['required_modules'] = [
	'mysqli',
	'gd',
	'curl',
	'mcrypt',
	'session',
	'json',
	'date'
];
*/

/**
 *    set the database information. database connected when you parse your first query.
 *    you can add more number of database connections and it can be accessed by the database identifier.
 *    for example, if the identifier is `db` then this database can be accessed as `$Zamp_SystemCore_Object->db`
 *
 *    Zamp stores table information and threat id to the cache file which is stored in `tmp_folder`
 *
 *    NOTE: Mysqli persistent connection supported from php >= 5.3.0
 *
 *    If you need the database connection link identifier then you can use `$Zamp_SystemCore_Object->db->connection`
 */
$config['database_info'] = [
	'db' => [
		'driver'		=> 'mysqli', // currently Zamp supports only mysqli driver
		'persistent'	=> false,
		'tmp_folder'	=> _PROJECT_PATH_.'data/tmp/cache/', // put slash at the end
		'host_name' 	=> 'localhost',
		'host_port' 	=> 3306, // Mysql server default port is 3306
		'username' 		=> 'database_username',
		'password' 		=> 'database_password',
		'dbname' 		=> 'database_name',
		'table_prefix' 	=> '', // [optional]
		'char_set' 		=> 'utf8', // [optional] If you dont want to set char_set and dbcollation remove it from this array
		'dbcollation' 	=> '', // [optional]
		'query_callback'=> '', // [optional] callback to call for every query. queryCallback(string $sql, float $timeTaken, string|integer $queryId);
		
		// ssl settings for mysqli driver [optional]
		/*'ssl_settings' => [
			'key' => null,
			'cert' => null,
			'ca' => null,
			'capath' => null,
			'cipher' => null,
		],*/
	],
];

// Session Handler settings
/**
 *    When session started, then started session id will be set in this config, in key as `_started_session_id`
 *
 *    Available options
 *
 *    session_handler - set any of the following session handler, or your own session handling method/function.
 *                      If any of the following session handler not used, then Zamp treat as your own session
 *                      handler defined, and it called with `session_settings` config array, and your own handler
 *                      MUST return updated `session_settings` array.
 *    
 *    NOTE: If `session_handler` NOT SET or set to `null` then Zamp use `file` based session handling by default
 *    
 *       php - use php default session handling which is mentioned in php.ini file
 *       file - use file based session handling
 *       mysql - use mySql database for session handling
 *    
 *    session_name - name of the session
 *    session_id - session id. If you set session_id session.use_cookies will be false [default: php settings]
 *    auto_start - by default auto_start set to `false`. if you want to start session automatically put `true`
 *    no_auto_start_for - If you dont want to start session automatically for array of controlller/action
 *                        example: ['Api', 'Soap/getResponse']; // `controller` or `controller/action`
 *    
 *    session_cookie_lifetime - session cookie lifetime [default: php settings]
 *    session_cookie_path - session cookie path [default: php settings]
 *    session_cookie_domain - session cookie domain [default: php settings]
 *    session_cookie_secure - session cookie secure [default: php settings]
 *    session_cookie_httponly - session cookie httponly [default: php settings]
 *    session_cache_limiter - session cache limit [default: php settings]
 *    session_cache_expire - session cache expire [default: php settings]
 *    session_save_path - session save path [default: _PROJECT_PATH_.'data/tmp/sessions']
 *    db_instance_name - if you set session_handler as mysql then enter the db instance name which you set in $config['database_info']
 *                       example: If you set $config['database_info']['db'] then db_instance_name is 'db'
 *                       If you use same database for your Application and session then enter only once in $config['database_info']
 *    
 *    db_table - session table name [default: sessions]
 *    db_id_col - name of session id column [default: sess_id]
 *    db_data_col - name of session data column [default: sess_data]
 *    db_time_col - name of session time column [default: sess_time]
 *    db_lock_timeout - session lock wait timeout in seconds [default: 300]
 */
$config['session_settings'] = [
	'auto_start' => false,
	'session_name' => 'ZampPHP',
];

// specify your template settings.
$config['viewTemplate'] = [
	'template_name' => 'default', // name of your template folder
	'template_engine' => 'Php', // Zamp supported template engines are Smarty, Php
];

/**
 * 	Zamp supported cache engines are File, APCu, XCache, MemCache, NoCache, Redis
 * 	
 * 	NOTE: If cache settings not set, then `NoCache` will be initiated by default to Zamp::$core->_cache
 * 	
 * // File storage engine.
 * $config['cache'] = [
 * 		'engine' => 'File', // [required]
 * 		'duration' => 3600, // [optional]
 * 		'path' => '', // [optional] by default Zamp takes ./data/tmp/cache/ folder.
 * 		'prefix' => '' // [optional]
 * ];
 *
 * // APCu, XCache
 * $config['cache'] = [
 * 		'engine' => 'XCache', // [required] available engines are APCu, XCache
 * 		'duration' => 3600, // [optional]
 * 		'prefix' => '' // [optional]
 * ];
 *
 * // Memcache
 * $config['cache'] = [
 * 		'engine' => 'MemCache', // [required]
 * 		'duration' => 3600, // [optional]
 * 		'prefix' => '', // [optional]
 * 		'servers' => [
 * 			'127.0.0.1:11211' // localhost, default port 11211
 * 		]
 * ];
 * 
 * // Redis
 * $config['cache'] = [
 *		'engine' => 'Redis', // [required]
 *		'duration' => 3600, // [optional]
 *		'prefix' => '', // [optional]
 *		'host' => '127.0.0.1',
 *		'port' => '6379',
 *		'password' => null,
 *		'database' => null,
 *		'timeout' => null,
 * ];
 */

/**
 *    Zamp has builtin debugger to debug your applications and it needs FirePHP 0.5.0
 *
 *    Download FirePHP 0.5.0 from http://www.firephp.org/HQ/FinalRelease.htm
 *    After installation dont forgot to enable FirePHP to debug.
 *
 *    NOTE: if $config['isDevelopmentPhase'] set to `false` then
 *    debugging gets deactivated and the following settings wont affect
 *
 *    The following debugging settings are removed from configuration array when bootstrap completed
 */
$config['_Zamp_Debugging'] = [
	'isActivated' => 0, // to activate debugger set to `1`, to deactivate set to `0`
	'allowed_ips' => '*', // comma separated ips or `*` to allow all
	
	'project_debugging' => [
		// this is under experiment
		'codeflow_debugging' => [
			'isActivated' => 0,
			'debug_folder' => _PROJECT_PATH_.'data/tmp/debugged_files/',
			'print_codeflow' => 0,
			'method_counting' => 0,
		],
		'included_config' => 0,
		'framework_constants' => 0,
		'print_sql_queries' => 1,
	],
	
	'environment_debugging' => [
		'_GET' => 0,
		'_POST' => 0,
		'_REQUEST' => 0,
		'_COOKIE' => 0,
		'_FILES' => 0,
		'_SERVER' => 0,
		'_ENV' => 0,
	],
	
	'php_debugging' => [
		'version' => 0,
		'get_include_path' => 0,
		'sys_get_temp_dir' => 0,
		'php_ini_loaded_file' => 0,
		'php_sapi_name' => 0,
		'php_uname' => 0,
		'error_reporting' => 0,
		'loaded_extensions' => 0,
		'php_ini_scanned_files' => 0,
		'config_options' => 0,
	],
];

// Encryption secret key string. Zamp prefer you to set some random characters as secret key.
$config['encryption_secret_key'] = '';

// Is this development phase. if set to true then error/debug messages will be displayed.
$config['isDevelopmentPhase'] = true;

// set your aplication time difference in seconds from GMT time
$config['app_time_diff_from_gmt'] = 0;

// Set your default controller name
$config['defaultController'] = 'Index';

// Set your default action name
$config['defaultAction'] = 'index';

/**
 *    Callback function to call when 404 error occured in NON development phase
 *    (ie., config `isDevelopmentPhase` set to false)
 *
 *    Callback called with type of error like `controller_not_found`, `method_not_found`, `view_not_found`
 *    ex:    yourcallback($errorType)
 *
 *    refer redirect() function in Zamp/Zamp.php for callback functions
 */
$config['routing404Callback'] = '';

/** End of File **/