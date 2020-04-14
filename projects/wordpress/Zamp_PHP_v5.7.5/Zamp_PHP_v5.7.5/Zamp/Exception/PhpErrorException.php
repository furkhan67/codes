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
 *    php error exception class
 *
 * @category Zamp
 * @package Zamp_Exception
 * @subpackage Zamp_Exception_PhpErrorException
 * @author Mathan kumar R <phpmathan@gmail.com>
 * @version SVN: $Id: PhpErrorException.php 367 2016-02-22 09:48:41Z phpmathan $
 */
class Zamp_Exception_PhpErrorException extends Zamp_Exception {
	/**
	 *    construct method
	 *
	 * @access public
	 * @param integer $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param integer $errline
	 */
	public function __construct($errno, $errstr, $errfile, $errline) {
		static $errorTypes = [
			E_ERROR => "Error",
			E_WARNING => "Warning",
			E_PARSE => "Parsing Error",
			E_NOTICE => "Notice",
			E_CORE_ERROR => "Core Error",
			E_CORE_WARNING => "Core Warning",
			E_COMPILE_ERROR => "Compile Error",
			E_COMPILE_WARNING => "Compile Warning",
			E_USER_ERROR => "User Error",
			E_USER_WARNING => "User Warning",
			E_USER_NOTICE => "User Notice",
			E_STRICT => "Runtime Notice"
		];
		
		$errorType = isset($errorTypes[$errno]) ?$errorTypes[$errno] :'Unknown Error';
		parent::__construct("[$errorType] $errstr (@line $errline in file $errfile).", $errno);
	}
}
/** End of File **/