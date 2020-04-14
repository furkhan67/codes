<?php
/**
 *    This file is part of the Zamp PHP Framework.
 *    (c) Mathan kumar R <phpmathan@gmail.com>
 *
 *    For the full copyright and license information, please view the LICENSE
 *    file that was distributed with this source code.
 *
 * @package Zamp_View_Php
 */

/**
 *    Php view class
 *
 * @category Zamp_View
 * @package Zamp_View_Php
 * @author Mathan kumar R <phpmathan@gmail.com>
 * @version SVN: $Id: Php.php 381 2018-12-25 13:09:18Z phpmathan $
 */
class Zamp_View_Php extends Zamp_View_Abstract {
	/**
	 *    store assigned view data
	 *
	 * @access protected
	 * @var array $_viewData
	 */
	protected $_viewData = [];

	/**
	 *    Loading Php Template Engine
	 *
	 * @access public
	 * @param bool $reload load new instance. If you changed your view settings at runtime then reload the view
	 * @return object
	 */
	public static function loadTemplateEngine($reload = false) {
		$Zamp_View_Php = Zamp::getInstance('Zamp_View_Php', [], $reload);
		
		if(!$Zamp_View_Php->_view_template_extension)
			$Zamp_View_Php->_view_template_extension = '.php';
		
		$Zamp_View_Php->setDefaultViewData($Zamp_View_Php);
		
		return $Zamp_View_Php;
	}

	/**
	 *    __get magic method
	 *
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function __get($key) {
		return $this->_viewData[$key];
	}

	/**
	 *    __set magic method
	 *
	 * @access public
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function __set($key, $value = null) {
		$this->assign($key, $value);
	}

	/**
	 *    __isset magic method
	 *
	 * @access public
	 * @param string $key
	 * @return bool
	 */
	public function __isset($key) {
		return isset($this->_viewData[$key]);
	}

	/**
	 *    __unset magic method
	 *
	 * @access public
	 * @param string $key
	 */
	public function __unset($key) {
		unset($this->_viewData[$key]);
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
			$this->_viewData = array_merge($this->_viewData, $spec);

			return;
		}

		$this->_viewData[$spec] = $value;
	}

	/**
	 *    clear all assigned smarty view variables
	 *
	 * @access public
	 */
	public function clearVars() {
		$this->_viewData = [];
	}

	/**
	 *    display main layout
	 *
	 * @access public
	 * @return void
	 */
	public function display($filename) {
		foreach($this->_viewData as $k => $v)
			$$k = $v;
		
		if(strpos($filename, 'file:') !== false)
			$filename = substr($filename, 5);
		else
			$filename = $this->templateDir.'/'.$filename;
		
		$Zampf = Zamp::$core;
		
		include $filename;
	}
}
/** End of File **/
