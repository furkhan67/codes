<?php
/**
 *    This file is part of the Zamp PHP Framework.
 *    (c) Mathan kumar R <phpmathan@gmail.com>
 *
 *    For the full copyright and license information, please view the LICENSE
 *    file that was distributed with this source code.
 *
 * @package Zamp_SystemCore
 */

/**
 *    Zamp PHP Framework core file class
 *
 * @category Zamp
 * @package Zamp_SystemCore
 * @author Mathan kumar R <phpmathan@gmail.com>
 * @version SVN: $Id: SystemCore.php 381 2018-12-25 13:09:18Z phpmathan $
 */
class Zamp_SystemCore {
	/**
	 *    Run time properties
	 *
	 * @access public
	 * @var array $_runTimeProperties
	 */
	public $_runTimeProperties = [];
	/**
	 *    Query String parameters
	 *
	 * @access public
	 * @var array $_params
	 */
	public $_params = [];
	/**
	 *    Application configurations
	 *
	 * @access public
	 * @var array $_config
	 */
	public $_config = [];
	/**
	 *    Loaded Plugins
	 *
	 * @access public
	 * @var array $_plugins_loaded
	 */
	public $_plugins_loaded = [];
	/**
	 *    Loaded Applications
	 *
	 * @access public
	 * @var array $_loadedApps
	 */
	public $_loadedApps = [];
	/**
	 *    By default routing is enabled
	 *
	 * @access public
	 * @var bool $_noRouting
	 */
	public $_noRouting = false;
	/**
	 *    checking initialize
	 *
	 * @access private
	 * @var bool $_initialized
	 */
	private $_initialized = false;
	/**
	 *    restricted properties to avoid problem when loading cross application and
	 *    setting property to framework object
	 *
	 *    array declared as `key` => `value` because to use isset() instead in_array() to increase performance
	 *
	 * @access public
	 * @var array $_restricted_properties
	 */
	public $_restricted_properties = [
		'_runTimeProperties' => 1,
		'_params' => 1,
		'_config' => 1,
		'_cache' => 1,
		'_plugins_loaded' => 1,
		'_loadedApps' => 1,
		'_noRouting' => 1,
		'_initialized' => 1,
		'_view' => 1,
		'_restricted_properties' => 1,
		'_timer' => 1,
		'_request' => 1,
		'_root_url' => 1,
		'_current_url' => 1,
		'_application_url' => 1,
	];

	/**
	 *    Construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct($bootstrapInformation) {
		$this->_timer = Zamp::$instances['Zamp_Timer'] = new Zamp_Timer();
		$this->_timer->startTimer('Total_Runtime');
		$this->_timer->startTimer('Zamp_SystemCore_Construct');
		
		$configurations = $this->_bootStrap($bootstrapInformation);
		
		$this->_loadConfigurations($configurations);
		
		if(empty($this->_params['controller']))
			$this->setControllerAction($this->_config['defaultController']);
		
		if(empty($this->_params['action']))
			$this->setControllerAction(null, $this->_config['defaultAction']);
		
		$this->_timer->stopTimer('Zamp_SystemCore_Construct');
	}

	/**
	 *    Application Bootstraping
	 *
	 * @access private
	 * @param array $bootstrapInformation
	 * @return array
	 */
	private function _bootStrap($bootstrapInformation) {
		if((array) $bootstrapInformation['applications'] !== $bootstrapInformation['applications'])
			cleanExit('Applications not defined in bootstrap.php');
		
		$this->_timer->startTimer('Zamp_Request_Construct');
		$this->_request = Zamp::$instances['Zamp_Request'] = new Zamp_Request();
		$this->_timer->stopTimer('Zamp_Request_Construct');
		
		$this->_setPath();
		
		$query_string = $this->_request->server('REQUEST_URI');
		$query_string = preg_replace('!'._PROJECT_RELATIVE_PATH_.'!', '', $query_string, 1);
		
		$this->updateRoutingValues($this->checkRouterAndBuild($query_string, $bootstrapInformation['router']));
		
		$config_file = APP_PATH.'configuration.php';
		
		if(isFileExists($config_file))
			require_once $config_file;
		
		$matchedApplication = '';
		
		if($this->getController()) {
			if(isset($bootstrapInformation['applications'][$this->getController()])) {
				$matchedApplication = $this->getController();
				
				self::setApplication($bootstrapInformation['applications'][$this->getController()].'/');
				
				$config_file = _APPLICATION_CORE_.'configuration.php';
				
				if(isFileExists($config_file))
					require_once $config_file;
				
				if($this->getAction()) {
					$this->setControllerAction(str_replace(' ', '', $this->_params['action']));
					
					if(isset($this->_params['query'][0])) {
						$this->setControllerAction(null, str_replace(' ', '', $this->_params['query'][0]));
						
						if(count($this->_params['query']) > 1)
							array_shift($this->_params['query']);
						else
							$this->_params['query'] = [];
					}
					else {
						$this->setControllerAction(null, '');
						
						if((array) $this->_params['query'] === $this->_params['query']) {
							foreach($this->_params['query'] as $temp => $temp2) {
								if(is_numeric($temp))
									unset($this->_params['query'][$temp]);
							}
						}
					}
				}
				else {
					$this->setControllerAction('', '');
					
					if((array) $this->_params['query'] === $this->_params['query']) {
						foreach($this->_params['query'] as $temp => $temp2) {
							if(is_numeric($temp))
								unset($this->_params['query'][$temp]);
						}
					}
				}
			}
			else {
				$this->setControllerAction(str_replace(' ', '', $this->_params['controller']));
				
				if(isset($bootstrapInformation['applications']['*'])) {
					$config_file = APP_PATH.$bootstrapInformation['applications']['*'].'/'.ZAMP_CORE_MODULE.'configuration.php';
					
					if(isFileExists($config_file))
						require_once $config_file;
				}
			}
		}
		elseif(isset($bootstrapInformation['applications']['*'])) {
			$config_file = APP_PATH.$bootstrapInformation['applications']['*'].'/'.ZAMP_CORE_MODULE.'configuration.php';
			
			if(isFileExists($config_file))
				require_once $config_file;
		}
		
		if(!$matchedApplication) {
			$matchedApplication = '*';
			$applicationFolder = $bootstrapInformation['applications']['*'].(($bootstrapInformation['applications']['*']) ?'/' :'');
			self::setApplication($applicationFolder);
		}
		
		$this->_params['matched_application'] = $matchedApplication;
		
		$appConfig = _APPLICATION_CORE_.'config';
		if(is_dir($appConfig)) {
			$configFiles = [];
			
			$noAuto = glob("$appConfig/*.noauto.php");
			$all = glob("$appConfig/*.php");
			
			if($all && $noAuto)
				$configFiles = array_diff($all, $noAuto);
			elseif($all && !$noAuto)
				$configFiles = $all;
			
			foreach($configFiles as $configFile)
				require_once $configFile;
		}
		
		if(empty($config['viewTemplate'])) {
			$config['viewTemplate'] = [
				'template_name' => 'default',
				'template_engine' => 'Smarty',
			];
		}
		elseif(empty($config['viewTemplate']['template_name']))
			$config['viewTemplate']['template_name'] = 'default';
		
		$config['isDevelopmentPhase'] = isset($config['isDevelopmentPhase']) ?(bool) $config['isDevelopmentPhase'] :true;
		$this->initErrorHandlers();
		
		if((array) $config === $config) {
			$config = array_merge($config, $bootstrapInformation);

			return $config;
		}
		else
			throw new Zamp_Exception("Configuration File <font color='red'>$config_file</font> not found or Invalid!", 10);
	}

	/**
	 *    initialize the autoloading objects
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		if($this->_initialized !== false)
			return;
		
		if(!$this->isExtensionLoaded())
			return;
		
		$this->_initialized = true;
		
		$this->_root_url = $this->getRootURL();
		$this->_current_url = $this->getRootURL(true).'/'.$this->_params['requested_url'];
		$this->_application_url = $this->_root_url;
		
		if(_APPLICATION_NAME_ && $this->_params['matched_application'] != '*')
			$this->_application_url = $this->_root_url.$this->_params['matched_application'].'/';
		
		$this->checkLiveTestMode();
		
		try {
            $this->_doProcess();
        }
        catch(Error $e) {
            Zamp_ErrorHandler::exceptionHandler($e);
        }
		
		$this->_timer->stopTimer('Total_Runtime');
	}
	
    /**
	 *    do application process
	 *
	 * @access private
	 * @return void
	 */
    private function _doProcess() {
        $this->_timer->startTimer('application_processing_time');
		
		if(isFileExists(_APPLICATION_CORE_.'Processing.php')) {
			require_once _APPLICATION_CORE_.'Processing.php';
			
			$this->_startSession();
			
			$className = ucfirst(_APPLICATION_NAME_).'_Processing';
			
			if(!class_exists($className, false)) {
				$className = 'Processing';
				if(!class_exists($className, false))
					$className = false;
			}
			
			if($className) {
				$ProcessingClass = Zamp::getInstance($className);
				
				if(is_callable([
					$ProcessingClass,
					'preProcessing'
				], true))
					$ProcessingClass->preProcessing();
				
				$this->startRouting();
				
				if(is_callable([
					$ProcessingClass,
					'postProcessing'
				], true))
					$ProcessingClass->postProcessing();
			}
			else
				$this->startRouting();
		}
		else {
			$this->_startSession();
			$this->startRouting();
		}
		
		$this->_timer->stopTimer('application_processing_time');
    }
	
	/**
	 *    Start cache when called first time
	 * 	
	 * @access private
	 * @return object
	 */
	private function _startCache() {
		$cache_engine = empty($this->_config['cache']['engine']) ?'NoCache' :$this->_config['cache']['engine'];
		$cache_engine = 'Zamp_Cache_'.$cache_engine;
		
		$cache_config = empty($this->_config['cache']) ?[] :$this->_config['cache'];
		
		return $this->_cache = Zamp::$instances[$cache_engine] = new $cache_engine($cache_config);
	}
	
	/**
	 *    State session before main routing
	 *
	 * @access private
	 * @return void
	 */
	private function _startSession() {
		if(empty($this->_config['session_settings']['auto_start']))
			return;
		
		if(!empty($this->_config['session_settings']['no_auto_start_for'])) {
			$controller = strtolower($this->getController());
			$action = strtolower($this->getAction());
			
			foreach($this->_config['session_settings']['no_auto_start_for'] as $check) {
				$check = strtolower($check);
				
				if("$controller/$action" == $check || $controller == $check)
					return;
			}
		}
		
		Zamp::$instances['Zamp_Session'] = new Zamp_Session($this->_config['session_settings']);
	}

	/**
	 *    loading view object
	 *
	 * @access public
	 * @param bool $reload You can reload the view object whenever you need for your updated configurations at runtime [default:false]
	 * @return object
	 */
	public function loadZampView($reload = false) {
		$template_engine = $this->_config['viewTemplate']['template_engine'];
		
		if($template_engine == 'Smarty' || $template_engine == 'Php') {
			require_once _PROJECT_PATH_."Zamp/View/$template_engine.php";
			
			$engine = "Zamp_View_$template_engine";
			return $this->_view = $engine::loadTemplateEngine($reload);
		}
		else
			throw new Zamp_Exception("You've choosed `<font color=red>$template_engine</font>` as template engine. But Zamp supported template engines are <font color=blue>Smarty</font>, <font color=blue>Php</font>", 11);
	}

	/**
	 *    initialize error handlers
	 *
	 * @access public
	 * @return void
	 */
	public function initErrorHandlers() {
		set_error_handler([
			'Zamp_ErrorHandler',
			'errorHandler'
		], error_reporting());
		
		set_exception_handler([
			'Zamp_ErrorHandler',
			'exceptionHandler'
		]);
	}

	/**
	 *    checking if Zamp Live Test Mode enabled not not
	 *
	 * @access public
	 * @since v1.3.3
	 * @return void
	 */
	public function checkLiveTestMode() {
		if(!ZAMP_LIVE_TEST_MODE)
			return;
		
		$block_test_mode_access = true;
		
		if($accessKey = $this->_request->get('zamp_live_test_mode_access_key')) {
			$host = preg_replace('/^www\./i', '', $this->_request->server('HTTP_HOST'));
			
			$cookieInfo = session_get_cookie_params();
			
			if($accessKey == ZAMP_LIVE_TEST_MODE_ACCESS_KEY) {
				setcookie('zamp_live_test_mode_key', ZAMP_LIVE_TEST_MODE_ACCESS_KEY, time() + 86400, '/', '.'.$host, $cookieInfo['secure'], $cookieInfo['httponly']);
				
				$block_test_mode_access = false;
			}
			elseif($accessKey == 'stop') {
				setcookie('zamp_live_test_mode_key', '', time() - 86400, '/', '.'.$host, $cookieInfo['secure'], $cookieInfo['httponly']);
				$block_test_mode_access = true;
			}
		}
		
		$liveTestModeKey = $this->_request->cookie('zamp_live_test_mode_key');
		
		if(
			(
				empty($liveTestModeKey)
					||
				($liveTestModeKey != ZAMP_LIVE_TEST_MODE_ACCESS_KEY)
			)
				&&
			$block_test_mode_access
		) {
			Zamp_General::setHeader(503);
			header('Retry-After: 3600');
			
			if(SET_ZAMP_HEADER)
				header("X-Powered-By: Zamp PHP/".ZAMP_VERSION);
			
			if(isset($this->_view)) {
				$errorFile = 'zamp_live_test_mode'.$this->_view->_view_template_extension;
				if(file_exists($errorFilePath = _APPLICATION_CORE_.'view/'.$this->_config['viewTemplate']['template_name'].'/'.$errorFile) or file_exists($errorFilePath = _APPLICATION_CORE_.'view/default/'.$errorFile)) {
					$errorFilePath = 'file:'.preg_replace('@(\\\|/{2,})@', '/', realpath($errorFilePath));
					$this->_view->display($errorFilePath);
					cleanExit();
				}
			}
			
			$fileName = _PROJECT_PATH_."Zamp/Exception/template/zamp_live_test_mode.html.php";
			require_once $fileName;
			cleanExit();
		}
	}

	/**
	 *    check and apply router configuration then build cotroller, action, query string etc..
	 *
	 * @access public
	 * @since 2.9.2
	 * @param string $requested_url
	 * @param array $routerConfig [optional]
	 * @return array
	 */
	public function checkRouterAndBuild($requested_url, $routerConfig = []) {
		if($routerConfig)
			$route = $routerConfig;
		elseif(isset($this->_config['router']))
			$route = $this->_config['router'];
		else
			$route = [];
		
		$updated = [];
		
		$updated['requested_url'] = $query_string = $requested_url;
		
		if((array) $route === $route) {
			foreach($route as $condition => $value) {
				if(preg_match($condition, $requested_url, $matches)) {
					$updated['router_condition'] = $condition;
					$updated['router_value'] = $value;
					
					if(preg_match_all("/#(\d+)#/", $value, $subMatches)) {
						foreach($subMatches[1] as $v)
							$value = str_replace("#$v#", $matches[$v], $value);
					}
					
					$updated['routed_url'] = $query_string = $value;
					break;
				}
			}
		}
		
		$temp = explode("?", $query_string);
		$query_string = explode('/', $temp[0], 3);
		$updated['controller'] = $query_string[0];
		$updated['action'] = (isset($query_string[1])) ?$query_string[1] :'';
		
		if(isset($temp[1]))
			parse_str($temp[1], $GetQueryString);
		
		if(isset($query_string[2])) {
			$temp2 = explode('/', $query_string[2]);
			
			foreach($temp2 as $temp3) {
				if($temp3)
					$GetQueryString[] = $temp3;
			}
		}
		
		if(isset($GetQueryString)) {
			ksort($GetQueryString, SORT_STRING);
			$updated['query'] = $GetQueryString;
			
			$requested_url = [];
			
			if(!empty($updated['controller']))
				$requested_url[] = $updated['controller'];
			
			if(!empty($updated['action']))
				$requested_url[] = $updated['action'];
			
			$requested_url[] = $this->_request->getQueryUrl(true, '&', $GetQueryString);
			
			$requested_url = implode('/', $requested_url);
			
			if(!isset($GetQueryString[0]))
				$requested_url = preg_replace('~/\?~', '?', $requested_url, 1);
			
			$updated['requested_url'] = $requested_url;
		}
		else
			$updated['query'] = [];
		
		if(defined('_APPLICATION_NAME_'))
			$updated['controller'] = $updated['controller'];
		
		return $updated;
	}

	/**
	 *    update or apply routing values
	 *
	 * @access public
	 * @since 2.9.2
	 * @param string $routingValues
	 * @param bool $updateCurrentUrl
	 * @return void
	 */
	public function updateRoutingValues($routingValues, $updateCurrentUrl = true) {
		$this->_params = $routingValues;
		
		$_GET = $routingValues['query'];
		
		if($updateCurrentUrl && defined('_APPLICATION_NAME_')) {
			$this->_current_url = $this->getRootURL(true).'/'.$routingValues['requested_url'];
			
			if(isset($this->_view))
				$this->_view->assign('current_url', $this->_current_url);
		}
	}

	/**
	 *    Call function for display error message with parameters, when non defined function calls
	 *
	 * @access public
	 * @param string $fun_name
	 * @param mixed $args
	 */
	public function __call($fun_name, $args) {
		$errorDialog = "Undefined function <font color='blue'>$fun_name</font> called with the following arguments";
		$errorDialog .= '<pre>'.var_export($args, true).'</pre>';
		throw new Zamp_Exception($errorDialog, 8);
	}

	/**
	 *    Set run time properties
	 *
	 * @access public
	 * @since v1.2.9
	 * @param string $name
	 * @param mixed $value
	 */
	public function setProperty($name, $value) {
		if(!isset($this->_restricted_properties[$name]))
			$this->_runTimeProperties[$name] = $value;
		else
			throw new Zamp_Exception("Property <font color='red'>$name</font> can not be set due to restricted properties.", 12);
	}

	/**
	 *    __get magic function to load the model
	 *
	 * @access public
	 * @param string $name
	 * @return object
	 */
	public function __get($name) {
		if($name == '_cache')
			return $this->_startCache();
		
		if($name == '_view')
			return $this->loadZampView();
		
		if(isset($this->_config['database_info'][$name])) {
			$dbConfig = $this->_config['database_info'][$name];
			$dbEngine = strtolower($dbConfig['driver']);
			
			if($dbEngine == 'mysqli')
				$this->$name = new Zamp_Db_Mysqli($dbConfig);
			else
				throw new Zamp_Exception("Database Engine <font color='red'>{$dbConfig['driver']}</font> Not Supported!");
			
			$this->_restricted_properties[$name] = 1;
			
			return $this->$name;
		}
		
		$this->$name = Zamp::getInstance($name);
		self::handleContructorHook($this->$name);
		
		return $this->$name;
	}

	/**
	 *    method define _PROJECT_RELATIVE_PATH_ constant
	 *
	 * @access private
	 * @return void
	 */
	private function _setPath() {
		if(!defined('_PROJECT_RELATIVE_PATH_')) {
			$docRoot = (isset($_SERVER['PHP_DOCUMENT_ROOT'])) ?$_SERVER['PHP_DOCUMENT_ROOT'] :$_SERVER['DOCUMENT_ROOT'];
			$folder = str_replace(realpath($docRoot), '/', realpath(_PROJECT_PATH_)).'/';
			$folder = preg_replace('@(\\\|/{2,}|\/\\\\)@', '/', $folder);
			
			define('_PROJECT_RELATIVE_PATH_', $folder);
		}
	}

	/**
	 *    load plugins. If the class found then instance of the class returned otherwise bool true returned
	 *    example1: load S3 class in amazon folder use the following
	 *    $s3 = $this->loadPlugin('amazon/S3'); // instance of S3 class returned
	 *
	 *    example2: to load your function file simply use
	 *    $this->loadPlugin('myfunctions/functions'); // it loads myfunctions/functions.php file and return true
	 *
	 * @access public
	 * @since v1.3.7
	 * @param string $plugin_class
	 * @param array $_args if class found then these parameters parsed to contruct method
	 * @param string $pluginFolder
	 * @param bool $reload force to get new instance
	 * @return object/bool
	 */
	public function loadPlugin($plugin_class, $_args = [], $pluginFolder = '', $reload = false) {
		if(!$pluginFolder)
			$pluginFolder = _PROJECT_PATH_.'plugins/';
		else
			$pluginFolder = rtrim($pluginFolder, '/ ').'/';
		
		$plugin_file = $pluginFolder.$plugin_class.'.php';
		
		if(!$reload && isset($this->_plugins_loaded[$plugin_file]))
			return $this->_plugins_loaded[$plugin_file];
		
		if(isFileExists($plugin_file)) {
			require_once $plugin_file;
			
			$plugin_class = explode('/', $plugin_class);
			$plugin_class = array_pop($plugin_class);
			
			if(class_exists($plugin_class, false))
				$this->_plugins_loaded[$plugin_file] = Zamp::getInstance($plugin_class, $_args, $reload);
			else
				$this->_plugins_loaded[$plugin_file] = true;
			
			return $this->_plugins_loaded[$plugin_file];
		}
		else
			throw new Zamp_Exception("Plugin File <font color='red'>$plugin_file</font> not found!", 14);
	}

	/**
	 *    Define Application folder
	 *
	 * @static
	 * @access public
	 * @return void
	 */
	public static function setApplication($applicationFolder) {
		if(!defined('_APPLICATION_'))
			define('_APPLICATION_', APP_PATH.$applicationFolder);
		
		if(!defined('_APPLICATION_CORE_'))
			define('_APPLICATION_CORE_', _APPLICATION_.ZAMP_CORE_MODULE);
		
		if(!defined('_APPLICATION_NAME_'))
			define('_APPLICATION_NAME_', rtrim($applicationFolder, '/'));
	}

	/**
	 *    get application name of given object
	 *
	 * @access public
	 * @since v1.6.1
	 * @param object $object
	 * @return string
	 */
	public function getAppName($object) {
		if(isset($object->_currentApplicationName))
			return $object->_currentApplicationName;
		else
			return _APPLICATION_NAME_;
	}

	/**
	 *    get application instance. method used to get the instance of application model or controller
	 *
	 * @access public
	 * @since v1.2.5
	 * @param string $applicationName Application Name
	 * @param string $modelOrControllerName Model or Controller Name
	 * @param array $_args parameters for the given model or controller in array format
	 * @return instance
	 */
	public function getApplication($applicationName, $modelOrControllerName, $_args = []) {
		if($className = $this->_loadedApps["$applicationName/$modelOrControllerName"]) {
			$applicationInstance = Zamp::getInstance($className);
			$applicationInstance->_currentApplicationName = $applicationName;
			self::handleContructorHook($applicationInstance);
			
			return $applicationInstance;
		}
		elseif(!in_array($modelOrControllerName, get_declared_classes())) {
			$cacheKey = $applicationName.':'.$modelOrControllerName;
			$fileName = isFileExists($cacheKey, true);
			
			if($fileName)
				$isFileExists = true;
			else {
				$isFileExists = false;
				
				$cName = strstr($modelOrControllerName, 'Controller', true);
				
				if($cName) {
					$cFile = 'controller/'.$cName.'Controller.php';
					$exceptionCode = 1;
				}
				else {
					$cName = $modelOrControllerName;
					$cFile = 'model/'.$cName.'.php';
					$exceptionCode = 2;
				}
				
				$fileName = APP_PATH.$applicationName.'/'.ZAMP_CORE_MODULE.$cFile;
				
				if(isFileExists($fileName, $cacheKey))
					$isFileExists = true;
				else
					$fileName = APP_PATH.$applicationName.'/'.$cName.'/'.$cFile;
			}
			
			if($isFileExists || isFileExists($fileName, $cacheKey, true)) {
				require_once $fileName;
				
				if(strpos($modelOrControllerName, $applicationName.'_') === 0)
					$appInstanceData[0] = str_replace($applicationName.'_', '', $modelOrControllerName);
				else
					$appInstanceData[0] = $modelOrControllerName;
				
				$appInstanceData[0] = ucfirst($applicationName).'_'.$modelOrControllerName;
				if(!class_exists($appInstanceData[0], false))
					$appInstanceData[0] = $modelOrControllerName;
				
				$this->_loadedApps["$applicationName/$modelOrControllerName"] = $appInstanceData[0];
				
				$appInstanceData[1] = $_args;
				
				$applicationInstance = call_user_func_array([
					'Zamp',
					'getInstance'
				], $appInstanceData);
				
				$applicationInstance->_currentApplicationName = $applicationName;
				self::handleContructorHook($applicationInstance);
				
				return $applicationInstance;
			}
			else
				throw new Zamp_Exception("<font color='red'>$applicationName</font> Application file <font color='blue'>$fileName</font> not found!", $exceptionCode);
		}
		else {
			if(stripos($modelOrControllerName, $applicationName.'_') !== 0) {
				$modelOrControllerName = ucfirst($applicationName).'_'.$modelOrControllerName;

				return $this->getApplication($applicationName, $modelOrControllerName, $_args);
			}
			
			$realClass = preg_replace("/^".$applicationName."_/i", "", $modelOrControllerName);
			throw new Zamp_Exception("Class <font color='blue'>$realClass</font> and <font color='blue'>$modelOrControllerName</font> already declared!", 15);
		}
	}

	/**
	 *    returns the benchmark
	 *
	 * @access public
	 * @return array
	 */
	public function getBenchmark() {
		return $this->_timer->timer;
	}

	/**
	 *    Checking system Requirements to install/run the Application.
	 *
	 * @access public
	 * @param array|string $check_module
	 * @return bool
	 */
	public function isExtensionLoaded($check_module = '') {
		if($this->_config['isDevelopmentPhase'] !== true)
			return true;
		
		if((array) $check_module !== $check_module) {
			if($check_module) {
				$check_module = [$check_module];
				$total_modules = 1;
			}
			elseif(!isset($this->_config['required_modules']))
				return true;
			elseif(!($check_module = $this->_config['required_modules']) || (array) $check_module !== $check_module)
				return true;
			else
				$total_modules = count($check_module);
		}
		else
			$total_modules = count($check_module);
		
		$loaded = $not_loaded = $_loaded = [];
		foreach($check_module as $key => $value) {
			if((string) $value === $value) {
				$value = strtolower($value);
				if(extension_loaded($value)) {
					$loaded[$value] = true;
					$_loaded[$value] = "<font color='green'><b>PASSED</b></font>";
				}
				else {
					$not_loaded[] = $value;
					$_loaded[$value] = "<font color='red'><b>FAILED</b></font>";
				}
			}
		}
		
		if($total_modules == count($loaded))
			return true;
		else {
			if(function_exists('dl')) {
				$prefix = (PHP_SHLIB_SUFFIX === 'dll') ?'php_' :'';
				foreach($not_loaded as $value) {
					$value = strtolower($value);
					if(@dl($prefix.$value.PHP_SHLIB_SUFFIX)) {
						$loaded[$value] = true;
						$_loaded[$value] = "<font color='green'><b>PASSED</b></font>";
					}
				}
				
				if($total_modules == count($loaded))
					return true;
				else
					$display_error = true;
			}
			else
				$display_error = true;
			
			if($display_error) {
				$errorDialog = "<font color='red' size='4'>System Requirements Failed!</font>
								<br/>
								The following failed modules must be enabled.
								<table cellpadding='3'>";
				foreach($_loaded as $key => $value)
					$errorDialog .= "<tr><td width='50'>$key</td><td>=></td><td>$value</td></tr>";
				$errorDialog .= "
								</table>";
				throw new Zamp_Exception($errorDialog, 16);
			}
		}
	}

	/**
	 *    Load the Configurations globally.
	 *
	 * @access private
	 */
	private function _loadConfigurations($configurations) {
		if((array) $configurations !== $configurations)
			throw new Zamp_Exception('Configurations must be in Array format!', 17);
		
		$configurations = array_merge([
			'session_settings' => [
				'session_name' => 'ZampPHP',
			],
			'encryption_secret_key' => 'bkjGF&AT*&@RFG(A*DA)FGAUIGjivx98^89',
			'isDevelopmentPhase' => false,
			'app_time_diff_from_gmt' => 0,
			'defaultController' => 'Index',
			'defaultAction' => 'index',
		], $configurations);
		
		foreach($configurations as $key => $value)
			$this->_config[$key] = $value;
	}
	
	/**
	 *    Get server root url based on header information.
	 *
	 * @access public
	 * @param bool $dont_use_project_path
	 * @return string
	 */
	public function getRootURL($dont_use_project_path = false) {
		$host = $this->_request->server('HTTP_HOST') ?:'';
		
		if(!$server_port = strstr($host, ':'))
			$server_port = '';
		elseif($server_port == ':80' || $server_port == ':443')
			$server_port = '';
		
		$protocol = (Zamp_General::isSslConnection()) ?'https://' :'http://';
		
		if($server_port)
			$url = $protocol.str_replace($server_port, '', $host).$server_port;
		else
			$url = $protocol.$host;
		
		if($dont_use_project_path)
			return $url;
		
		return $url._PROJECT_RELATIVE_PATH_;
	}

	/**
	 *    Returns GMT Time
	 *
	 * @access public
	 * @param string $format date function compatible formats
	 * @param integer $timeDiff
	 * @return integer|string
	 */
	public function systemTime($format = null, $timeDiff = null) {
		$now = DateTime::createFromFormat('U.u', number_format(microtime(true), 6, '.', ''));
		$timeDiff = (int) (isset($timeDiff) ?$timeDiff :$this->_config['app_time_diff_from_gmt']);
		$now->modify($timeDiff.' seconds');
		
		if(!isset($format))
			return (int) $now->format('U');
		
		elseif($format === 'micro-seconds')
			return (int) $now->format('Uu');
		
		elseif($format === 'mili-seconds')
			return (int) substr($now->format('Uu'), 0, -3);
		
		else
			return $now->format($format);
	}

	/**
	 *    Method used for to record the system errors
	 *
	 * @access public
	 * @param string $log_for This is name of the create log file.
	 * @param string $str Error message to record in the log.
	 * @return void
	 */
	public function recordSystemLog($log_for, $str) {
		$str = strip_tags($str);
		
		if(!is_dir(_APP_LOG_FOLDER_))
			mkdir(_APP_LOG_FOLDER_, 0775, true);
		
		$str = $this->systemTime("d/m/Y h:i:s A")."\n".$str."\n\n";
		file_put_contents(_APP_LOG_FOLDER_.$log_for.'_log.txt', $str, FILE_APPEND | LOCK_EX);
	}

	/**
	 *    Set Apache Restriction for logs, error logs folder
	 *
	 * @access public
	 * @since v1.3.4
	 * @param string $data
	 * @return void
	 */
	public function setApacheRestriction($data) {
		$this->Zampf_Apache_Restriction = $data;
	}

	/**
	 *    change/update controller and action at runtime
	 *
	 * @access public
	 * @since v1.4.1
	 * @param string $controller
	 * @param string $action
	 * @return void
	 */
	public function setControllerAction($controller = null, $action = null) {
		if($controller !== null)
			$this->_params['controller'] = $controller;
		
		if($action !== null)
			$this->_params['action'] = $action;
	}

	/**
	 *    get the Controller name
	 *
	 * @access public
	 * @since v1.4.1
	 * @return string
	 */
	public function getController() {
		return $this->_params['controller'];
	}

	/**
	 *    get the Action name
	 *
	 * @access public
	 * @since v1.4.1
	 * @return string
	 */
	public function getAction() {
		return $this->_params['action'];
	}

	/**
	 *    stop main routing
	 *
	 * @access public
	 * @since v1.4.1
	 * @return void
	 */
	public function stopRouting() {
		$this->_noRouting = true;
	}

	/**
	 *    Main Application routing
	 *
	 * @access public
	 */
	final public function startRouting() {
		if($this->_noRouting !== false)
			return;
		
		$controllerName = $this->getController();
		$controllerFullName = $controllerName.'Controller';
		
		$controller_file = Zamp::isAvailable($controllerFullName);
		
		if(!$controller_file) {
			if($this->_config['isDevelopmentPhase'])
				throw new Zamp_Exception("<font color='blue'>$controllerFullName</font> File Not Found!", 3);
			else {
				if(!empty($this->_config['routing404Callback']))
					redirect($this->_config['routing404Callback'], ['controller_not_found']);
				
				$this->showErrorPage(404);
			}
		}
		
		$temp = $controllerFullName;
		$controllerFullName = ucfirst(_APPLICATION_NAME_).'_'.$temp;
		if(!class_exists($controllerFullName, false))
			$controllerFullName = $temp;
		
		if(class_exists($controllerFullName, false)) {
			$controllerInstance = Zamp::getInstance($controllerFullName);
			self::handleContructorHook($controllerInstance);
			
			$controllerMethod = $this->getAction();
			$controllerMethodLowerCase = strtolower($controllerMethod);
			
			if($controllerMethod[0] == '_')
				$isMethodCallOk = 'internal-method';
			else
				$isMethodCallOk = method_exists($controllerInstance, $controllerMethod);
			
			if($isMethodCallOk === true) {
				$blockedMethods = [];
				
				if(isset($controllerInstance->hooks) && !empty($controllerInstance->hooks['routing']['blocked_methods']))
					$blockedMethods = $controllerInstance->hooks['routing']['blocked_methods'];
				
				if(
					$blockedMethods
						&&
					(array) $blockedMethods === $blockedMethods
						&&
					in_array($controllerMethodLowerCase, array_map('strtolower', $blockedMethods))
				)
					$isMethodCallOk = 'blocked';
			}
			
			if($isMethodCallOk === true) {
				$controllerInstance->$controllerMethod();
				
				if(isset($this->_view))
					$viewFile = $controllerName.'/'.$controllerMethodLowerCase.$this->_view->_view_template_extension;
			}
			elseif(isset($controllerInstance->hooks) && !empty($controllerInstance->hooks['routing']['method_error_handler'])) {
				$reporterMethod = $controllerInstance->hooks['routing']['method_error_handler'];
				
				if(!method_exists($controllerInstance, $reporterMethod)) {
					if($this->_config['isDevelopmentPhase'])
						throw new Zamp_Exception("Method access error reporting failed. Method name `$reporterMethod` not found in <font color='blue'>$controllerName</font>");
					else
						$this->showErrorPage(500);
				}
				
				if(!$isMethodCallOk)
					$isMethodCallOk = 'undefined';
				
				$controllerInstance->$reporterMethod($controllerMethod, $isMethodCallOk);
			}
			elseif($this->_config['isDevelopmentPhase']) {
				if($isMethodCallOk == 'internal-method')
					throw new Zamp_Exception("Method begins with `_` are considered as internal use only, and can not be accessed via routing.", 27);
				elseif($isMethodCallOk == 'blocked')
					throw new Zamp_Exception("<font color='blue'>".$controllerName."</font> Controller method (<font color='blue'>".$controllerMethod."</font>) is in `blocked_methods` list.", 28);
				else
					throw new Zamp_Exception("<font color='blue'>".$controllerName."</font> Controller method (<font color='blue'>".$controllerMethod."</font>) not Found!", 7);
			}
			else {
				if(!empty($this->_config['routing404Callback']))
					redirect($this->_config['routing404Callback'], ['method_not_found']);
				
				$this->showErrorPage(404);
			}
		}
		
		if(isset($this->_view) && isset($this->_view->viewFile))
			$viewFile = $this->_view->viewFile;
		
		if(!empty($viewFile)) {
			$cacheKey = $controllerName.':'.$controllerMethodLowerCase.':'.$viewFile;
			$templateFile = isFileExists($cacheKey, true);
			
			if(!$templateFile) {
				if($viewFile[0] == '.')
					$templateFile = isFileExists($templateFile, $cacheKey);
				else {
					$viewFileDir = strstr($controller_file, 'controller/'.$controllerName, true).'view/';
					
					$viewDir1 = $viewFileDir.$this->_config['viewTemplate']['template_name'].'/';
					$templateFile = realpath($viewDir1.$viewFile);
					$templateFile = isFileExists($templateFile, $cacheKey);
					
					if(!$templateFile) {
						$viewDir2 = $viewFileDir.'default/';
						$templateFile = realpath($viewDir2.$viewFile);
						$templateFile = isFileExists($templateFile, $cacheKey, true);
					}
				}
				
				if(!$templateFile) {
					if($this->_config['isDevelopmentPhase']) {
						$errorDialog = "Create your Template File <font color='blue'>$viewFile</font> under any of the following folder.<ul style='list-style:disc;'><li>$viewDir1</li><li>$viewDir2</li></ul>";
						
						throw new Zamp_Exception($errorDialog, 9);
					}
					else {
						if(!empty($this->_config['routing404Callback']))
							redirect($this->_config['routing404Callback'], ['view_not_found']);
						
						$this->showErrorPage(404);
					}
				}
			}
			
			if($this->_view instanceof Zamp_View_Smarty)
				$this->_view->viewFile = 'file:'.$templateFile;
			else
				$this->_view->viewFile = $templateFile;
		}
	}

	/**
	 *    contruct method hook handler
	 *
	 * @access public
	 * @static
	 * @since v1.8.9
	 * @param object $controllerInstance
	 * @return bool is hook method called?
	 */
	public static function handleContructorHook(&$controllerInstance) {
		if(empty($controllerInstance->hooks))
			return false;
		
		if(empty($controllerInstance->hooks['__construct']))
			return false;
		
		$hookInfo = $controllerInstance->hooks['__construct'];
		
		if(!empty($hookInfo['__isExecuted']))
			return false;
		
		if(empty($hookInfo['method']))
			throw new Zamp_Exception("Class <font color='blue'>".get_class($controllerInstance)."</font> Constructor Hook method is required!");
		
		if(!method_exists($controllerInstance, $hookInfo['method']))
			throw new Zamp_Exception("Class <font color='blue'>".get_class($controllerInstance)."</font> Constructor Hook method <font color='red'>".$hookInfo['method']."</font> is not callable!");
		
		$controllerInstance->hooks['__construct']['__isExecuted'] = true;
		
		if($hookInfo['isStatic'])
			$function = get_class($controllerInstance).'::'.$hookInfo['method'];
		else
			$function = [
				$controllerInstance,
				$hookInfo['method']
			];
		
		if(!empty($hookInfo['args']) && (array) $hookInfo['args'] === $hookInfo['args'])
			call_user_func_array($function, $hookInfo['args']);
		else {
			if($hookInfo['isStatic'])
				$function();
			else {
				$methodName = $hookInfo['method'];
				$controllerInstance->$methodName();
			}
		}
		
		return true;
	}

	/**
	 *    Display Error Page
	 *
	 * @access public
	 * @since 1.2.8
	 */
	public function showErrorPage($errorCode, $errorFilePath = '') {
		$errorCode = (is_numeric($errorCode)) ?$errorCode :500;
		Zamp_General::setHeader($errorCode);
		
		if(isset($this->_view)) {
			if(!$errorFilePath) {
				$errorFile = "error_$errorCode".$this->_view->_view_template_extension;
				
				$errorFilePath = _APPLICATION_CORE_.'view/'.$this->_config['viewTemplate']['template_name'].'/'.$errorFile;
				$errorFilePath = isFileExists($errorFilePath);
				
				if(!$errorFilePath)
					$errorFilePath = isFileExists(_APPLICATION_CORE_.'view/default/'.$errorFile);
			}
			
			if($errorFilePath) {
				$errorFilePath = 'file:'.preg_replace('@(\\\|/{2,})@', '/', realpath($errorFilePath));
				$this->_view->display($errorFilePath);
				cleanExit();
			}
		}
		
		$fileName = _PROJECT_PATH_."Zamp/Exception/template/error$errorCode.html.php";
		require_once $fileName;
		cleanExit();
	}
}
/** End of File **/
