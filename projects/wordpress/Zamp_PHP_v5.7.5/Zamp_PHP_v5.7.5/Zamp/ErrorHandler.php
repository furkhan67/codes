<?php
/**
 *    This file is part of the Zamp PHP Framework.
 *    (c) Mathan kumar R <phpmathan@gmail.com>
 *
 *    For the full copyright and license information, please view the LICENSE
 *    file that was distributed with this source code.
 *
 * @package Zamp_ErrorHandler
 */

/**
 *    This package handle the Errors and Exceptions in Zamp PHP.
 *
 * @category Zamp
 * @package Zamp_ErrorHandler
 * @author Mathan kumar R <phpmathan@gmail.com>
 * @version SVN: $Id: ErrorHandler.php 381 2018-12-25 13:09:18Z phpmathan $
 */
class Zamp_ErrorHandler {
	/**
	 *    Handling the Errors
	 *
	 * @access public
	 * @param mixed $exception
	 * @return void
	 */
	public function handleError($exception) {
		static $handling = false;
		
		restore_error_handler();
		restore_exception_handler();
		
		if($handling)
			$this->handleRecursiveError($exception);
		else {
			$handling = true;
			$this->displayException($exception);
		}
	}

	/**
	 *    php error handler overwritten by this errorHandler method.
	 *
	 * @static
	 * @access public
	 * @param integer $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param integer $errline
	 * @return void
	 */
	public static function errorHandler($errno, $errstr, $errfile, $errline) {
		if(error_reporting() != 0)
			throw new Zamp_Exception_PhpErrorException($errno, $errstr, $errfile, $errline);
	}

	/**
	 *    Handling the Exceptions
	 *
	 * @static
	 * @access public
	 * @param mixed $exception
	 * @return void
	 */
	public static function exceptionHandler($exception) {
		Zamp::getInstance('Zamp_ErrorHandler')->handleError($exception);
		
		cleanExit();
	}

	/**
	 *    handling recursive errors
	 *
	 * @access protected
	 * @param mixed $exception
	 * @return void
	 */
	protected function handleRecursiveError($exception) {
		echo "<html>
				<head>
					<title>Recursive Error</title>
				</head>
				<body>
					<h1>Recursive Error</h1>
					<pre>".$exception->__toString()."</pre>
				</body>
			</html>";
	}
	
	/**
	 *    returns error html or log file path
	 *
	 * @access public
	 * @param mixed $exception
	 * @param array $errorHandlerConfig [optional]
	 * @return array
	 */
	public static function getErrorInfoHTML($exception, $errorHandlerConfig = null) {
		if(!isset($errorHandlerConfig)) {
			$errorHandlerConfig = [];
			
			if(isset(Zamp::$core->_config['errorHandler']))
				$errorHandlerConfig = Zamp::$core->_config['errorHandler'];
		}
		
		if(!isset($errorHandlerConfig['isDevelopmentPhase']))
			$errorHandlerConfig['isDevelopmentPhase'] = !empty(Zamp::$core->_config['isDevelopmentPhase']);
		
		$errorTimeDiffFromGmt = 0;
		
		if(isset($errorHandlerConfig['error_time_diff_from_gmt']) && is_numeric($errorHandlerConfig['error_time_diff_from_gmt']))
			$errorTimeDiffFromGmt = $errorHandlerConfig['error_time_diff_from_gmt'];
		
		if($errorTimeDiffFromGmt < 1) {
			$timeZoneFormat = '-';
			$tempVal = -1 * $errorTimeDiffFromGmt;
		}
		else {
			$timeZoneFormat = '+';
			$tempVal = $errorTimeDiffFromGmt;
		}
		
		$hrs = floor($tempVal / 3600);
		$mins = floor(($tempVal - ($hrs * 3600)) / 60);
		$sec = $tempVal - ($hrs * 3600) - ($mins * 60);
		
		$timeZoneFormat .= $hrs.':'.$mins.($sec ?':'.$sec :'');
		
		$errorInfo = self::getErrorInfo($exception);
		
		ob_start();
		require_once _PROJECT_PATH_.'Zamp/Exception/template/exception.html.php';
		$error_output = ob_get_clean();
		
		if(!empty($errorHandlerConfig['isDevelopmentPhase'])) {
			return [
				'type' => 'html',
				'result' => $error_output
			];
		}
		
		$save_error_into_folder = (isset($errorHandlerConfig['save_error_into_folder'])) ?rtrim($errorHandlerConfig['save_error_into_folder'], '/') :_PROJECT_PATH_.'application_errors';
		
		if(!is_dir($save_error_into_folder))
			mkdir($save_error_into_folder, 0777, true);
		
		$error_file_name = isset($errorHandlerConfig['error_file_format']) ?$errorHandlerConfig['error_file_format'] :'YmdHis';
		$error_file_name = Zamp::$core->systemTime($error_file_name, $errorTimeDiffFromGmt).'.html';
		$error_full_file_name = realpath($save_error_into_folder).'/'.$error_file_name;
		
		file_put_contents($error_full_file_name, $error_output, LOCK_EX);
		
		if(!empty($errorHandlerConfig['send_email_alert'])) {
			$send_error_email = true;
			unset($errorInfo['traces']);
            $currentErrorHash = md5(implode('@@', $errorInfo));
			
			$errorHashFile = $save_error_into_folder.'/mailed_error_hashes.php';
            $currentTime = Zamp::$core->systemTime(null, 0);
            $notifiedErrors = [];
            
            if(file_exists($errorHashFile))
                $notifiedErrors = include $errorHashFile;
			
            if(!isset($notifiedErrors[$currentErrorHash]))
                $notifiedErrors[$currentErrorHash] = [];
            
            if($notifiedErrors[$currentErrorHash]) {
                $lastError = array_pop($notifiedErrors[$currentErrorHash]);
                $notifiedErrors[$currentErrorHash][] = $lastError;
                
                $minDelay = $currentTime - $lastError['occurredOn'];
                
                if($minDelay < $errorHandlerConfig['interval_for_same_error_realert'])
                    $send_error_email = false;
            }
            
            $notifiedErrors[$currentErrorHash][] = [
                'errorNo' => count($notifiedErrors[$currentErrorHash]) + 1,
                'occurredOn' => $currentTime,
                'errorFile' => $error_file_name,
                'notified' => $send_error_email ?'Yes': 'No',
            ];
            
            file_put_contents($errorHashFile, "<?php\nreturn ".var_export($notifiedErrors, true).";\n", LOCK_EX);
            
            if($send_error_email) {
                $errorHandlerConfig['message'] .= "<br/><br/>Error Hash: {$currentErrorHash}<br/><br/>Events: <pre style='font-size:12px;'>".json_encode($notifiedErrors[$currentErrorHash], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)."</pre>";
                
				if(!isset($errorHandlerConfig['attach']))
					$errorHandlerConfig['attach'] = [];
				
				$errorHandlerConfig['attach'][] = [
					$error_full_file_name,
					$error_file_name
				];
				
				ob_start();
				Zamp::getInstance('Zamp_Mailer')->sendMail($errorHandlerConfig);
				ob_end_clean();
			}
		}
		
		return [
			'type' => 'log',
			'result' => $error_full_file_name
		];
	}
	
	/**
	 *    displaying the exception or error screen
	 *    Sending error alert email if configured
	 *
	 * @access protected
	 * @param mixed $exception
	 * @return void
	 */
	protected function displayException($exception) {
		$result = self::getErrorInfoHTML($exception);
		
		if($result['type'] == 'log')
			Zamp::$core->showErrorPage(500);
		else
			echo $result['result'];
		
		cleanExit();
	}

	/**
	 *    collecting the error information
	 *
	 * @static
	 * @access public
	 * @param mixed $exception
	 * @return array
	 */
	public static function getErrorInfo($exception) {
		if($exception instanceof Zamp_Exception_PhpErrorException) {
			$traces = $exception->getTrace();
			$errorInfo = [];
			
			static $errorTypes = [
				E_ERROR => 'Error',
				E_WARNING => 'Warning',
				E_PARSE => 'Parsing Error',
				E_NOTICE => 'Notice',
				E_CORE_ERROR => 'Core Error',
				E_CORE_WARNING => 'Core Warning',
				E_COMPILE_ERROR => 'Compile Error',
				E_COMPILE_WARNING => 'Compile Warning',
				E_USER_ERROR => 'User Error',
				E_USER_WARNING => 'User Warning',
				E_USER_NOTICE => 'User Notice',
				E_STRICT => 'Runtime Notice'
			];
			
			$errorInfo['code'] = $traces[0]['args'][0];
			
			if(isset($errorTypes[$errorInfo['code']]))
				$errorInfo['text'] = ($errorText = $errorTypes[$errorInfo['code']]) ?$errorText :'Unknown Error';
			else
				$errorInfo['text'] = 'Unknown Error';
			
			$errorInfo['name'] = $traces[0]['args'][1];
		}
		else {
			$errorInfo['code'] = $exception->getCode();
			$errorInfo['text'] = 'Exception';
			$errorInfo['name'] = get_class($exception);
		}
		
		$errorInfo['message'] = $exception->getMessage();
		$errorInfo['traces'] = self::getTraces($exception);
		
		return $errorInfo;
	}

	/**
	 *    getting the source code line to display in the error screen
	 *
	 * @access public
	 * @static
	 * @param string $fileName
	 * @param integer $errorInLine
	 * @return string
	 */
	public static function getSourceCode($fileName, $errorInLine) {
		if(is_readable($fileName)) {
			$content = explode('<br />', highlight_file($fileName, true));
			
			$beginLine = max($errorInLine - 3, 1);
			$endLine = min($errorInLine + 3, count($content));
			
			$lines = [];

			for($i = $beginLine; $i <= $endLine; $i++) {
				$lines[] = '<li'.($i == $errorInLine ?' class="selected"' :'').'>'.$content[$i - 1].'</li>';
			}

			return '<ol start="'.max($errorInLine - 3, 1).'">'.implode("\n", $lines).'</ol>';
		}
	}

	/**
	 *    getting the exception trace path.
	 *
	 * @access protected
	 * @param mixed $exception
	 * @param string $format Available formats are 'html' and 'text'. [default: html]
	 * @return array
	 */
	protected static function getTraces($exception, $format = 'html') {
		$traceData = $exception->getTrace();
		
		if($exception instanceof Zamp_Exception_PhpErrorException)
			array_shift($traceData);
		
		array_unshift($traceData, array(
			'file' => $exception->getFile(),
			'line' => $exception->getLine(),
			'function' => '',
			'class' => null,
			'type' => null,
			'args' => [],
		));
		
		$traces = [];

		if($format == 'html')
			$lineFormat = 'at <strong>%s%s%s</strong>(%s)<br />in <em>%s</em> line %s <a href="#" onclick="toggle(\'%s\'); return false;">...</a><br /><ul class="code" id="%s" style="display: %s">%s</ul>';
		else
			$lineFormat = 'at %s%s%s(%s) in %s line %s';
		
		$count = count($traceData);
		
		for($i = 0; $i < $count; $i++) {
			$line = isset($traceData[$i]['line']) ?$traceData[$i]['line'] :null;
			$file = isset($traceData[$i]['file']) ?$traceData[$i]['file'] :null;
			$args = isset($traceData[$i]['args']) ?$traceData[$i]['args'] :[];
			
			$data = sprintf($lineFormat, (isset($traceData[$i]['class']) ?$traceData[$i]['class'] :''), (isset($traceData[$i]['type']) ?$traceData[$i]['type'] :''), $traceData[$i]['function'], self::formatArgs($args, false, $format), $file, null === $line ?'n/a' :$line, 'trace_'.$i, 'trace_'.$i, $i == 0 ?'none' :'none', self::getSourceCode($file, $line));
			
			if($traceData[$i]['function'] == '')
				$data = preg_replace('~^at (.*?)in ~', 'in ', $data);
			
			$traces[] = $data;
		}
		
		return $traces;
	}

	/**
	 *    Formatting trace arguments.
	 *
	 * @access public
	 * @static
	 * @param array $args
	 * @param bool $single
	 * @param string $format
	 * @return string
	 */
	public static function formatArgs($args, $single = false, $format = 'html') {
		$result = [];
		
		$single and $args = [$args];
		
		foreach($args as $key => $value) {
			if((object) $value === $value)
				$formattedValue = ($format == 'html' ?'<em>object</em>' :'object').sprintf("('%s')", get_class($value));
			elseif((array) $value === $value)
				$formattedValue = ($format == 'html' ?'<em>array</em>' :'array').sprintf("(%s)", self::formatArgs($value));
			elseif((string) $value === $value)
				$formattedValue = ($format == 'html' ?sprintf("'%s'", $value) :"'$value'");
			elseif(null === $value)
				$formattedValue = ($format == 'html' ?'<em>null</em>' :'null');
			else
				$formattedValue = $value;

			$result[] = is_numeric($key) ?$formattedValue :sprintf("'%s' => %s", $key, $formattedValue);
		}
		
		return implode(', ', $result);
	}
}
/** End of File **/
