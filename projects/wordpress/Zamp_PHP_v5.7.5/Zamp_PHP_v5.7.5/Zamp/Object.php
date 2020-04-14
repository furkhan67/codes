<?php
/**
 *    This file is part of the Zamp PHP Framework.
 *    (c) Mathan kumar R <phpmathan@gmail.com>
 *
 *    For the full copyright and license information, please view the LICENSE
 *    file that was distributed with this source code.
 *
 * @package Zamp_Object
 */

/**
 *    Abstraction class for controller and model. All your controller and model must extend this Zamp_Object
 *
 * @category Zamp
 * @package Zamp_Object
 * @abstract
 * @author Mathan kumar R <phpmathan@gmail.com>
 * @version SVN: $Id: Object.php 381 2018-12-25 13:09:18Z phpmathan $
 */
abstract class Zamp_Object {
	/**
	 *    contruct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {}
	
	/**
	 *    __get magic function to get Zamp_SystemCore properties
	 *
	 * @access public
	 * @param string $name
	 */
	public function __get($name) {
		$Zampf = Zamp::$core;
		
		if(isset($Zampf->_runTimeProperties[$name])) {
			$property = $Zampf->_runTimeProperties[$name];
			
			if((object) $property === $property)
				$this->$name = $property;
			
			return $property;
		}
		
		if(isset($this->_currentApplicationName) && !isset($Zampf->_restricted_properties[$name])) {
			if(isset($Zampf->_config['database_info'][$name])) {
				if((object) $Zampf->$name === $Zampf->$name)
					return $this->$name = $Zampf->$name;
				
				return $Zampf->$name;
			}
			
			$cacheKey = $this->_currentApplicationName.':'.$name;
			$fileName = isFileExists($cacheKey, true);
			
			if($fileName)
				$isFileExists = true;
			else {
				$isFileExists = false;
				
				$cName = strstr($name, 'Controller', true);
				
				if($cName) {
					$cFile = 'controller/'.$cName.'Controller.php';
					$exceptionCode = 1;
				}
				else {
					$cName = $name;
					$cFile = 'model/'.$cName.'.php';
					$exceptionCode = 2;
				}
				
				$fileName = APP_PATH.$this->_currentApplicationName.'/'.ZAMP_CORE_MODULE.$cFile;
				
				if(isFileExists($fileName, $cacheKey))
					$isFileExists = true;
				else
					$fileName = APP_PATH.$this->_currentApplicationName.'/'.$cName.'/'.$cFile;
			}
			
			if($isFileExists || isFileExists($fileName, $cacheKey, true)) {
				require_once $fileName;
				
				if(strpos($name, $this->_currentApplicationName.'_') === 0)
					$temp = str_replace($this->_currentApplicationName.'_', '', $name);
				else
					$temp = $name;
				
				$name = ucfirst($this->_currentApplicationName).'_'.$temp;
				if(!class_exists($name, false))
					$name = $temp;
				
				$Zampf->_loadedApps[$this->_currentApplicationName."/$temp"] = $name;
				
				$appInstance = Zamp::getInstance($name);
				$appInstance->_currentApplicationName = $this->_currentApplicationName;
				Zamp_SystemCore::handleContructorHook($appInstance);
				
				$Zampf->$name = $appInstance;
			}
			else
				throw new Zamp_Exception("<font color='red'>{$this->_currentApplicationName}</font> Application file <font color='blue'>$fileName</font> not found!", $exceptionCode);
		}
		
		if((object) $Zampf->$name === $Zampf->$name)
			return $this->$name = $Zampf->$name;
		
		return $Zampf->$name;
	}

	/**
	 *    __call magic function to get Zamp_SystemCore methods
	 *
	 * @access public
	 * @param string $fun_name
	 * @param mixed $args
	 */
	public function __call($fun_name, $args) {
		if($args)
			return call_user_func_array([
				Zamp::$core,
				$fun_name
			], $args);
		
		return Zamp::$core->$fun_name();
	}
}
/** End of File **/
