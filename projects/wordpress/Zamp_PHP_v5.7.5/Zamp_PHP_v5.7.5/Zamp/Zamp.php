<?php
/**
 *    This file is part of the Zamp PHP Framework.
 *    (c) Mathan kumar R <phpmathan@gmail.com>
 *
 *    For the full copyright and license information, please view the LICENSE
 *    file that was distributed with this source code.
 *
 * @package Zamp
 */

/**
 *    set your application path
 */
if(!defined('APP_PATH'))
	exit('Define the constant <b>APP_PATH</b> in bootstrap.php file!<br/>');

/**
 *    set your path of Zamp Framework folder (Project path)
 */
if(!defined('_PROJECT_PATH_'))
	exit('Define the constant <b>_PROJECT_PATH_</b> in bootstrap.php file!<br/>');

/**
 *    set your Logs folder
 */
if(!defined('_APP_LOG_FOLDER_'))
	define('_APP_LOG_FOLDER_', _PROJECT_PATH_.'app_logs/');

/**
 *    Zamp PHP version
 */
define('ZAMP_VERSION', '5.7.5');

if(phpversion() < '7.2.0')
	exit('PHP 7.2.0 or higher version required. You are using php '.phpversion());

/**
 *    Zamp supports modular design, set your base or core module path from your application folder WITH "/" AT END.
 *    If you don't want to enable the modular design set empty value (ie., '')
 */
if(!defined('ZAMP_CORE_MODULE'))
	define('ZAMP_CORE_MODULE', '');

/**
 *    Set Zamp PHP Version information in HTTP Response Header
 */
if(!defined('SET_ZAMP_HEADER'))
	define('SET_ZAMP_HEADER', true);

/**
 *    If you are testing you application in live environment, enable this option.
 */
if(!defined('ZAMP_LIVE_TEST_MODE'))
	define('ZAMP_LIVE_TEST_MODE', false);

/**
 *    If you enabled ZAMP_LIVE_TEST_MODE then enter the access key here.
 *
 *  In live test mode you can access your application by adding your access key at the end.
 *
 *    Example : "http://example.com/?zamp_live_test_mode_access_key=your-test-mode-access-key"
 */
if(!defined('ZAMP_LIVE_TEST_MODE_ACCESS_KEY'))
	define('ZAMP_LIVE_TEST_MODE_ACCESS_KEY', 'your-test-mode-access-key');

require_once _PROJECT_PATH_.'Zamp/SystemCore.php';
require_once _PROJECT_PATH_.'Zamp/Timer.php';
require_once _PROJECT_PATH_.'Zamp/Request.php';
require_once _PROJECT_PATH_.'Zamp/ErrorHandler.php';
require_once _PROJECT_PATH_.'Zamp/Session.php';
require_once _PROJECT_PATH_.'Zamp/General.php';
require_once _PROJECT_PATH_.'Zamp/Cache/Abstract.php';
require_once _PROJECT_PATH_.'Zamp/View/Abstract.php';

/**
 *    Loading and creating instance of the class. Zamp is Singleton pattern design
 *
 * @category Zamp
 * @package Zamp
 * @author Mathan kumar R <phpmathan@gmail.com>
 * @version SVN: $Id: Zamp.php 382 2019-01-07 14:04:15Z phpmathan $
 */
final class Zamp {
	/**
	 * @access public
	 */
	public static $instances = [];
	/**
	 * @access public
	 */
	public static $core;
	/**
	 * @access public
	 */
	public static $autoloaderSkipList = [
		'Smarty' => 1,
	];
	/**
	 * @access public
	 */
	private static $_vendors;
	/**
	 *    Map your class to different module
	 *
	 *    example:
	 *
	 *    Zamp::$classMapping = array_merge(Zamp::$classMapping, [
	 *        'PaymentsController' => 'user/Shop', // application/module
	 *        'user/Myorder' => 'admin/Myorder',
	 *        'Payment' => 'Shop', // if application not given then current application used
	 *    ]);
	 *
	 * @access public
	 */
	public static $classMapping = [];
	/**
	 *    Register your `redirect` compatible callback functions
	 *    which will be called with exit parameter.
	 *    
	 *    example:
	 *    
	 *    Zamp::$cleanExitCallbacks['callbackIdentifier'] = function($param) {
	 *        
	 *    };
	 *    
	 *    NOTE: If you callback return `false` then following registered callbacks not called.
	 * 
	 * @access public
	 */
	public static $cleanExitCallbacks = [];
	/**
	 * @access public
	 */
	public static $headerRedirectCallback = false;

	/**
	 * @ignore
	 */
	final private function __construct() {}

	/**
	 *    set vendors folder paths
	 *
	 * @access public
	 * @static
	 * @since v4.2.8
	 * @param string|array $path
	 * @param string $prefix
	 * @return void
	 */
	public static function setVendorPaths($path, $prefix = '*') {
		if(!isset(self::$_vendors[$prefix]))
			self::$_vendors[$prefix] = [];
		
		if((array) $path === $path)
			self::$_vendors[$prefix] = array_merge(self::$_vendors[$prefix], $path);
		else
			self::$_vendors[$prefix][] = $path;
	}

	/**
	 *    get vendors path settings
	 *
	 * @access public
	 * @static
	 * @since v4.2.8
	 * @return array
	 */
	public static function getVendorsPath() {
		return self::$_vendors;
	}

	/**
	 *    return the file path for the given class
	 *
	 * @access public
	 * @static
	 * @since v4.2.8
	 * @param string $className
	 * @param string $cacheKey
	 * @return bool|string
	 */
	public static function getVendorPath($className, $cacheKey = '') {
		if(!preg_match("/[_\\\]/", $className))
			return false;
		
		foreach(self::$_vendors as $prefix => $paths) {
			if($prefix == '*')
				continue;
			
			$len = strlen($prefix);
			
			if(substr($className, 0, $len) != $prefix)
				continue;
			
			$file = substr($className, $len);
			$file = preg_replace("/[_\\\]/", '/', $file).'.php';
			
			foreach($paths as $path) {
				$filename = $path.$file;
				$filename = isFileExists($filename, $cacheKey, true);
				
				if($filename)
					return $filename;
			}
		}
		
		if(empty(self::$_vendors['*']))
			return false;
		
		$file = preg_replace("/[_\\\]/", '/', $className).'.php';
		
		foreach(self::$_vendors['*'] as $path) {
			$filename = $path.$file;
			$filename = isFileExists($filename, $cacheKey, true);
			
			if($filename)
				return $filename;
		}
		
		return false;
	}

	/**
	 *    method return the instance of given class name.
	 *
	 *    Example use :<pre>
	 *        Zamp::getInstance('Zamp_Mailer');
	 *        Zamp::getInstance( 'MyClassName', array($arg1, $arg2...) );
	 *    </pre>
	 *
	 * @access public
	 * @param string $_className
	 * @param array $_args give parameter in array format for class construct method
	 * @param bool $reload force to get new instance
	 * @static
	 * @return object
	 */
	public static function getInstance($_className, $_args = [], $reload = false) {
		if(!$reload && isset(self::$instances[$_className]))
			return self::$instances[$_className];
		
		if(!class_exists($_className, false)) {
			if(stripos($_className, 'Zamp') === 0)
				require_once _PROJECT_PATH_.str_replace('_', '/', $_className).'.php';
			else {
				$fileFullPath = Zamp::isAvailable($_className, '', false, true);
				require_once $fileFullPath;
				
				$temp = $_className;
				$_className = ucfirst(_APPLICATION_NAME_).'_'.$temp;
				
				if(!class_exists($_className, false))
					$_className = $temp;
			}
		}
		
		$_reflection = new ReflectionClass($_className);
		
		if($_reflection->getConstructor())
			$instance = $_reflection->newInstanceArgs($_args);
		else
			$instance = $_reflection->newInstance();
		
		if($_className == 'Zamp_SystemCore')
			self::$core = $instance;
		else
			self::$instances[$_className] = $instance;
		
		return $instance;
	}

	/**
	 *    check the class file available or not
	 *
	 * @access public
	 * @static
	 * @since v1.2.10
	 * @param string $className
	 * @param string $appsName
	 * @param bool $includeFile [default:true]
	 * @param bool $showException [default:false]
	 * @return mixed
	 */
	public static function isAvailable($className, $appsName = '', $includeFile = true, $showException = false) {
		$cacheKey = ($appsName ?$appsName.':' :'').$className;
		
		if($filename = isFileExists($cacheKey, true)) {
			if($includeFile)
				require_once $filename;
			
			return $filename;
		}
		
		$isFileExists = false;
		
		if(
			($filename = self::isInMappedClass($className, $appsName, $cacheKey))
				||
			($filename = self::getVendorPath($className, $cacheKey))
		)
			$isFileExists = true;
		else {
			$cName = strstr($className, 'Controller', true);
			
			if($cName) {
				$cFile = 'controller/'.$cName.'Controller.php';
				$exceptionCode = 3;
			}
			else {
				$cName = $className;
				$cFile = 'model/'.$cName.'.php';
				$exceptionCode = 4;
			}
			
			if($appsName) {
				$appFolder = APP_PATH.$appsName.'/'.ZAMP_CORE_MODULE;
				$appFolder2 = APP_PATH.$appsName.'/'.$cName.'/';
			}
			else {
				$appFolder = _APPLICATION_CORE_;
				$appFolder2 = _APPLICATION_.$cName.'/';
			}
			
			$filename = $appFolder.$cFile;
			
			if(!($isFileExists = isFileExists($filename, $cacheKey, true)))
				$filename = $appFolder2.$cFile;
		}
		
		if($isFileExists || isFileExists($filename, $cacheKey, true)) {
			if($includeFile)
				require_once $filename;
			
			return $filename;
		}
		elseif(!$showException)
			return false;
		else
			throw new Zamp_Exception("File <font color='blue'>$filename</font> Not Found!", $exceptionCode);
	}

	/**
	 *    Check the classname is in classMapping array.
	 *
	 * @access public
	 * @static
	 * @since v4.3.1
	 * @param string $className
	 * @param string $appsName
	 * @param string $cacheKey
	 * @return bool|string
	 */
	public static function isInMappedClass($className, $appsName = '', $cacheKey = '') {
		$appFolder = false;
		
		if($appsName) {
			$key = $appsName.'/'.$className;
			
			if(isset(self::$classMapping[$key]))
				$appFolder = APP_PATH.$appsName.'/'.self::$classMapping[$key].'/';
		}
		
		if(!$appFolder && isset(self::$classMapping[$className]))
			$appFolder = _APPLICATION_.self::$classMapping[$className].'/';
		
		if(!$appFolder)
			return $appFolder;
		
		$cName = strstr($className, 'Controller', true);
		
		if($cName)
			$cFile = 'controller/'.$cName.'Controller.php';
		else {
			$cName = $className;
			$cFile = 'model/'.$cName.'.php';
		}
		
		$filename = $appFolder.$cFile;
		
		return isFileExists($filename, $cacheKey, true);
	}

	/**
	 *    preventing cloning of the class
	 *
	 * @access public
	 * @ignore
	 */
	final public function __clone() {
		trigger_error('Cannot clone instance of Singleton pattern', E_USER_ERROR);
	}
}

/**
 *    autoloading the class
 *
 * @access public
 * @param string $className
 */
function zampAutoloader($className) {
	foreach(Zamp::$autoloaderSkipList as $classNameStartsWith => $status) {
		if($status && strpos($className, $classNameStartsWith) === 0)
			return;
	}
	
	if($filepath = Zamp::isAvailable($className, '', false, true)) {
		if(strpos($className, 'Zamp_') === 0)
			require_once $filepath;
		else
			require_once $filepath;
	}
}

// registering Zamp autoloader function
spl_autoload_register('zampAutoloader');

// setting vendor paths
Zamp::setVendorPaths(_PROJECT_PATH_.'Zamp/', 'Zamp_');

/**
 *    get configuration array or value
 *
 * @access public
 * @since v1.4.8
 * @param string $key $key can be like a/b/c [optional]
 * @param array $configs [optional]
 * @return mixed
 */
function getConf($key = '', $configs = null) {
	if(!isset($configs))
		$configs = Zamp::$core->_config;
	
	if(!$key)
		return $configs;
	
	return Zamp_General::getMultiArrayValue(explode('/', $key), $configs);
}

function getConfFile($conf_to_check, $confFileName, $appsName = '') {
	$cacheKey = ($appsName ?$appsName.':' :'').$confFileName.':'.$conf_to_check;
	$confFile = isFileExists($cacheKey, true);
	
	if(!$confFile) {
		if($appsName) {
			$appFolder = APP_PATH.$appsName.'/'.ZAMP_CORE_MODULE;
			$appFolder2 = APP_PATH.$appsName.'/'.$confFileName.'/';
		}
		else {
			$appFolder = _APPLICATION_CORE_;
			$appFolder2 = _APPLICATION_.$confFileName.'/';
		}
		
		$confFile = $appFolder.'config/'.$confFileName.'.noauto.php';
		$confFile = isFileExists($confFile, $cacheKey);
		
		if(!$confFile) {
			$confFile = $appFolder2.'config/'.$confFileName.'.noauto.php';
			$confFile = isFileExists($confFile, $cacheKey, true);
			
			if(!$confFile)
				throw new Zamp_Exception("Configuration file (<font color=blue>$confFile</font>) not found!", 10);
		}
	}
	
	return $confFile;
}

/**
 *    set or modify configurations
 *
 * @access public
 * @since v1.5.0
 * @param string $key $key can be like a/b/c [optional]
 * @param mixed $value
 * @param bool $merge [default:true]
 * @param array $configs [optional]
 * @return void|array if custom config then updated config returned
 */
function setConf($key = '', $value, $merge = true, $customConfig = null) {
	$key = $key ?explode('/', $key) :[];
	
	if(!isset($customConfig))
		$configs = Zamp::$core->_config;
	else
		$configs = $customConfig;
	
	if($merge) {
		$configs = Zamp_General::arrayMergeRecursiveDistinct($configs,
            Zamp_General::setMultiArrayValue($key, $value)
        );
	}
	elseif($key) {
		if(!isset($key[1]))
			$configs[$key[0]] = $value;
		else
            $configs = Zamp_General::setMultiArrayValue($key, $value, $configs);
	}
	else
		$configs = $value;
	
	if(!isset($customConfig))
		Zamp::$core->_config = $configs;
	else
		return $configs;
}

/**
 *    set or modify session data
 *
 * @access public
 * @since v1.5.4
 * @param string $key $key can be like a/b/c [optional]
 * @param mixed $value
 * @param bool $unsetBeforeSet
 * @return void
 */
function setSession($key, $value, $unsetBeforeSet = false) {
	$key = explode('/', $key);
	
	if($unsetBeforeSet)
		Zamp_General::unsetMultiArrayValue($key, $_SESSION);
	
	$_SESSION = Zamp_General::arrayMergeRecursiveDistinct($_SESSION,
        Zamp_General::setMultiArrayValue($key, $value)
    );
}

/**
 *    get session data
 *
 * @access public
 * @since v1.5.4
 * @param string $key $key can be like a/b/c [optional]
 * @return mixed
 */
function getSession($key = '') {
	if(!$key)
		return $_SESSION;
	
	$key = explode('/', $key);
	
	return Zamp_General::getMultiArrayValue($key, $_SESSION);
}

/**
 *    delete session data
 *
 * @access public
 * @since v1.5.4
 * @param string $key $key can be like a/b/c
 * @return void
 */
function deleteSession($key = '') {
	if(!$key) {
		unset($_SESSION);
		session_destroy();
		
		return;
	}
	
	$key = explode('/', $key);
	
	Zamp_General::unsetMultiArrayValue($key, $_SESSION);
}

/**
 *    check and load configuration which are not loaded by default due to ".noauto" restriction
 *
 * @access public
 * @since v1.5.2
 * @param string $conf_to_check conf to check like a/b/c
 * @param string $confFileName filename without .noauto.php
 * @param string $appsName
 * @return bool
 */
function checkAndLoadConfiguration($conf_to_check, $confFileName, $appsName = '') {
	if(!getConf($conf_to_check)) {
		$confFile = getConfFile($conf_to_check, $confFileName, $appsName);
		
		require_once $confFile;
        
		setConf(null, $config);
		
		return true;
	}
	else
		return false;
}

/**
 *    Redirecting to URL, Controller/Action, Model/Method etc..
 *    <pre>
 *    example 1: redirect('http://google.com'); -> this will redirect to google.com web page
 *    example 2: redirect('MemberController/register'); -> this will call register method in MemberController class
 *    example 3: redirect('Member/getUserInfo', array(5)); is equal to $this->Member->getUserInfo(5);
 *    example 4: redirect('admin/MailServer/getServers'); -> this will call getServers method in MailServer class under admin application
 *    example 5: redirect('#APPLICATION_URL#Member/home'); this will redirect to http://applicationurl.com/Member/home
 *    example 6: redirect('#ROOT_URL#');
 *    example 7: redirect('#CURRENT_URL#?page=2');
 *    example 8: redirect('myfunction', array(param1, param2,..)); -> myfunction(param1, param2, ....);
 *    example 9: redirect(['MyClass', 'method'], [1, 2]) -> this will call static method MyClass::method(1, 2);
 *    example 10: redirect([$myClassObj, 'method']) -> this will call $myClassObj->method();
 *    example 11: $anonymousFunction = function($p1, $p2) {};redirect($anonymousFunction, [1, 2]); -> this will call Closer function with given parameters
 *    </pre>
 *
 * @access public
 * @since v1.5.1
 * @param string $page
 * @param array $params [optional]
 * @return mixed
 */
function redirect($page, $params = '') {
	if(gettype($page) !== 'string') {
		if(is_callable($page, true)) {
			if($params && (array) $params === $params)
				return call_user_func_array($page, $params);
			else
				return call_user_func($page);
		}
		
		throw new Exception("First parameter is not valid in redirect() function call.");
	}
	elseif(preg_match('@(^(http(s)?\:\/\/)|(#[A-Z_]+#))@', $page)) {
		$page = applyURLTemplates($page);
		$page = str_replace([
			"\r",
			"\n"
		], '', $page);
		
		if(Zamp::$headerRedirectCallback !== false && is_callable(Zamp::$headerRedirectCallback, true)) {
			$func = Zamp::$headerRedirectCallback;
			$func($page);
		}
		
		if(!headers_sent())
			header("Location: $page");
		else
			echo "<script type='text/javascript'>window.top.location.href='$page';</script>";
		
		cleanExit();
	}
	else {
		$page = explode('/', trim($page, '/ '));
		$weight = count($page);
		
		if($weight > 3)
			throw new Zamp_Exception("Invalid Page/Function/Method (<font color='red'>$page</font>) parsed in redirect() function!", 22);
		
		if($weight == 3)
			$instance = Zamp::$core->getApplication(array_shift($page), array_shift($page));
		elseif($weight == 2) {
			$instance = Zamp::getInstance(array_shift($page));
			Zamp_SystemCore::handleContructorHook($instance);
		}
		
		if($weight > 1)
			$functionName = [
				$instance,
				array_shift($page)
			];
		else
			$functionName = array_shift($page);
		
		if($params && (array) $params === $params)
			return call_user_func_array($functionName, $params);
		else
			return call_user_func($functionName);
	}
}

/**
 *    replacement for `exit` command
 *
 * @access public
 * @since v4.5.8
 * @param mixed $param
 * @return void
 */
function cleanExit($param = null) {
	if(!Zamp::$cleanExitCallbacks)
		exit($param);
	
	foreach(Zamp::$cleanExitCallbacks as $callback) {
		if(redirect($callback, [$param]) === false)
			exit($param);
	}
	
	exit($param);
}

/**
 *    apply common URL templates
 *
 * @access public
 * @since v3.3.1
 * @param string $url
 * @param array $urlTemplates more url templates
 * @return string
 */
function applyURLTemplates($url, $urlTemplates = []) {
	$Zampf = Zamp::$core;
	
	$urls = [
		'#ROOT_URL#' => $Zampf->_root_url,
		'#APPLICATION_URL#' => $Zampf->_application_url,
		'#CURRENT_URL#' => $Zampf->_current_url,
	];
	
	if($urlTemplates)
		$urls = Zamp_General::arrayMergeRecursiveDistinct($urls, $urlTemplates);
	
	return str_replace(array_keys($urls), $urls, $url);
}

/**
 *    Function used to check the file existence and store the result in file cache
 *
 * @access public
 * @since 4.2.0
 * @param string $input1 file to check or cacheKey
 * @param string $input2 cacheKey or Is $input1 is cacheKey
 * @param bool $forceCheck
 * @param bool $noCatcheSave Don't save cache if file not found
 * @return string|bool if file present returns file path, else boolean false returned
 */
function isFileExists($input1, $input2 = '', $forceCheck = false, $noCatcheSave = false) {
	if($input2 === true) {
		$file = false;
		$cacheKey = $input1;
	}
	else {
		$file = str_replace('//', '/', $input1);
		$cacheKey = $input2;
	}
	
	if(!$cacheKey)
		$cacheKey = $file;
	
	if(!$cacheKey)
		return false;
	
	if(!isset($GLOBALS['ZampFilePresenceCache'])) {
		$GLOBALS['ZampFilePresenceCache'] = [];
		
		$cacheFile = _PROJECT_PATH_.'data/tmp/cache/zamp_file_presence_cache.php';
		
		if(file_exists($cacheFile)) {
			$GLOBALS['ZampFilePresenceCache'] = include $cacheFile;
			
			if((array) $GLOBALS['ZampFilePresenceCache'] !== $GLOBALS['ZampFilePresenceCache'])
				$GLOBALS['ZampFilePresenceCache'] = [];
		}
	}
	
	if(!$forceCheck && isset($GLOBALS['ZampFilePresenceCache'][$cacheKey])) {
		if($GLOBALS['ZampFilePresenceCache'][$cacheKey] && $cacheKey == $file)
			return $file;
		else
			return $GLOBALS['ZampFilePresenceCache'][$cacheKey];
	}
	
	if(!$file)
		return false;
	elseif(file_exists($file))
		$GLOBALS['ZampFilePresenceCache'][$cacheKey] = ($cacheKey == $file) ?true :$file;
	else {
		$file = false;
		$GLOBALS['ZampFilePresenceCache'][$cacheKey] = $file;
	}
	
	if($noCatcheSave && !$file)
		return $file;
	elseif(!isset($cacheFile))
		$cacheFile = _PROJECT_PATH_.'data/tmp/cache/zamp_file_presence_cache.php';
	
	file_put_contents($cacheFile, "<?php\n// ".date('Y-m-d H:i:s')."\nreturn ".var_export($GLOBALS['ZampFilePresenceCache'], true).";\n", LOCK_EX);
    
    Zamp_General::invalidate($cacheFile);
	
	return $file;
}

// Get environment values defined in `.env.php` file under _PROJECT_PATH_
function env($key) {
    static $env;
	
	if($env === null) {
		$file = _PROJECT_PATH_.'.env.php';
		$env = isFileExists($file, 'env_values') ?require_once $file :[];
	}
	
	return Zamp_General::getMultiArrayValue(explode('.', $key), $env);
}
/** End of File **/
