<?php
/**
 *    This file is part of the Zamp PHP Framework.
 *    (c) Mathan kumar R <phpmathan@gmail.com>
 *
 *    For the full copyright and license information, please view the LICENSE
 *    file that was distributed with this source code.
 *
 * @package Zamp_View_Smarty
 */

/**
 *    Smarty view class
 *
 * @category Zamp_View
 * @package Zamp_View_Smarty
 * @author Mathan kumar R <phpmathan@gmail.com>
 * @version SVN: $Id: Smarty.php 381 2018-12-25 13:09:18Z phpmathan $
 */
class Zamp_View_Smarty extends Zamp_View_Abstract {
	/**
	 * @access public
	 * @var object $_engine
	 */
	public $_engine;

	/**
	 *    load smarty template engine
	 *
	 * @access public
	 * @param bool $reload load new instance. If you changed your view settings at runtime then reload the view
	 * @return object
	 */
	public static function loadTemplateEngine($reload = false) {
		$Zamp_View_Smarty = Zamp::getInstance('Zamp_View_Smarty', [], $reload);
		
		if(!$Zamp_View_Smarty->_view_template_extension)
			$Zamp_View_Smarty->_view_template_extension = '.tpl';
		
		require_once _PROJECT_PATH_."Zamp/3rdparty/smarty/Smarty.class.php";
		
		$compileDir = _PROJECT_PATH_.'data/tmp/templates_c/'._APPLICATION_NAME_.'/'.Zamp::$core->_config['viewTemplate']['template_name'];
		
		$Zamp_View_Smarty->_engine = Zamp::getInstance('Smarty');
		$Zamp_View_Smarty->_engine->setTemplateDir($Zamp_View_Smarty->templateDir);
		$Zamp_View_Smarty->_engine->setCompileDir($compileDir);
		$Zamp_View_Smarty->_engine->compile_check = Zamp::$core->_config['isDevelopmentPhase'] ?true :false;
		$Zamp_View_Smarty->_engine->force_compile = Zamp::$core->_config['isDevelopmentPhase'] ?true :false;

		$Zamp_View_Smarty->setDefaultViewData($Zamp_View_Smarty->_engine);

		return $Zamp_View_Smarty;
	}

	/**
	 *    set smarty configurations
	 *
	 * @access public
	 * @since 1.4.2
	 * @param array|string $key
	 * @param mixed $value
	 */
	public function setConfig($key, $value = null) {
		if((array) $key === $key) {
			foreach($key as $k => $v)
				$this->_engine->$k = $v;
			
			return;
		}
		
		$this->_engine->$key = $value;
	}

	/**
	 *    __set magic method
	 *
	 * @access public
	 * @param string $key
	 * @param mixed $val
	 */
	public function __set($key, $val) {
		$this->assign($key, $val);
	}

	/**
	 *    __call magic method
	 *
	 * @access public
	 * @param string $fun_name
	 * @param array $args
	 */
	public function __call($fun_name, $args) {
		call_user_func_array([
			$this->_engine,
			$fun_name
		], $args);
	}

	/**
	 *    __get magic method
	 *
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function __get($key) {
		return $this->_engine->getTemplateVars($key);
	}

	/**
	 *    __isset magic method
	 *
	 * @access public
	 * @param string $key
	 * @return bool
	 */
	public function __isset($key) {
		return $this->_engine->getTemplateVars($key) !== null;
	}

	/**
	 *    __unset magic method
	 *
	 * @access public
	 * @param string $key
	 */
	public function __unset($key) {
		$this->_engine->clearAssign($key);
	}

	/**
	 *    smarty assign method overwritten
	 *
	 * @access public
	 * @param string $spec
	 * @param mixed $value
	 * @return void
	 */
	public function assign($spec, $value = null) {
		if((array) $spec === $spec) {
			$this->_engine->assign($spec);

			return;
		}
		$this->_engine->assign($spec, $value);
	}

	/**
	 *    clear all assigned smarty view variables
	 *
	 * @access public
	 */
	public function clearVars() {
		$this->_engine->clearAllAssign();
	}

	public function display($display_filename) {
		$compileDir = $this->_engine->getCompileDir();
		
		if(!is_dir($compileDir)) {
			if(!@mkdir($compileDir, 0777, true))
				throw new Zamp_Exception("Create Smarty Complie Directory <font color=blue>$compileDir</font> manually and make it writable!", 24);
		}
		
		$this->_engine->display($display_filename);
	}
}
/** End of File **/
