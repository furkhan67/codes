<?php
/**
 *    This file is part of the Zamp PHP Framework.
 *    (c) Mathan kumar R <phpmathan@gmail.com>
 *
 *    For the full copyright and license information, please view the LICENSE
 *    file that was distributed with this source code.
 *
 * @package Zamp_Exception
 */

/**
 *    This package handle the Exceptions in Zamp PHP.
 *
 * @category Zamp
 * @package Zamp_Exception
 * @author Mathan kumar R <phpmathan@gmail.com>
 * @version SVN: $Id: Exception.php 367 2016-02-22 09:48:41Z phpmathan $
 */
class Zamp_Exception extends Exception {
	/**
	 * @access private
	 * @var string $_errorCode
	 */
	private $_errorCode;

	/**
	 *    construct method
	 *
	 * @access public
	 * @param string $errorMessage
	 */
	public function __construct($errorMessage, $code = 0) {
		$this->_errorCode = $code;
		
		$args = func_get_args();
		
		$n = count($args);
		
		$tokens = [];
		
		for($i = 2; $i < $n; ++$i)
			$tokens['{'.$i.'}'] = TPropertyValue::ensureString($args[$i]);
		
		parent::__construct(strtr($errorMessage, $tokens), $code);
	}

	/**
	 *    get the error code of the Exception
	 *
	 * @access public
	 * @return string error code
	 */
	public function getErrorCode() {
		return $this->_errorCode;
	}

	/**
	 *    set the error code of the Exception
	 *
	 * @access public
	 * @param string $code error code
	 */
	public function setErrorCode($code) {
		$this->_errorCode = $code;
	}

	/**
	 *    get the exception error message
	 *
	 * @access public
	 * @return string error message
	 */
	public function getErrorMessage() {
		return $this->getMessage();
	}

	/**
	 *    set the exception error message
	 *
	 * @access protected
	 * @param string error message
	 */
	protected function setErrorMessage($message) {
		$this->message = $message;
	}
}
/** End of File **/