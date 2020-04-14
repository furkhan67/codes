<?php
/**
 *    This file is part of the Zamp PHP Framework.
 *    (c) Mathan kumar R <phpmathan@gmail.com>
 *
 *    For the full copyright and license information, please view the LICENSE
 *    file that was distributed with this source code.
 *
 * @package Zamp_General
 */

/**
 *    This package contains frequently used general functions.
 *
 * @category Zamp
 * @package Zamp_General
 * @author Mathan kumar R <phpmathan@gmail.com>
 * @version SVN: $Id: General.php 381 2018-12-25 13:09:18Z phpmathan $
 */
class Zamp_General {
	/**
	 *    Function used to find the value in multidimentional array
	 *
	 * @access public
	 * @static
	 * @param string $needle
	 * @param array $haystack
	 * @param bool $strict [default:false]
	 * @param array $path
	 * @return bool|array
	 */
	public static function arraySearch($needle, $haystack, $strict = false, $path = []) {
		if((array) $haystack !== $haystack)
			return false;
		
		foreach($haystack as $key => $val) {
			if((array) $val === $val && $subPath = Zamp_General::arraySearch($needle, $val, $strict, $path)) {
				$path = array_merge($path, [$key], $subPath);

				return $path;
			}
			elseif((!$strict && $val == $needle) || ($strict && $val === $needle)) {
				$path[] = $key;

				return $path;
			}
		}

		return false;
	}

	/**
	 *    Function used to find the key in multidimentional array
	 *
	 * @access public
	 * @since v1.7.3
	 * @static
	 * @param string $needle
	 * @param array $haystack
	 * @return bool|array
	 */
	public static function arrayKeyExists($needle, $haystack) {
		if((array) $haystack !== $haystack)
			return false;
		
		foreach($haystack as $key => $value) {
			if($needle === $key)
				return [$key];
			
			if((array) $value === $value) {
				$temp = Zamp_General::arrayKeyExists($needle, $value);
				
				if($temp !== false) {
					return array_merge([$key], $temp);
				}
			}
		}
		
		return false;
	}

	/**
	 *    Method Used to get unique array using serialize.
	 *
	 * @access public
	 * @static
	 * @param array $myArray
	 * @return array
	 */
	public static function arrayUnique($myArray) {
		if((array) $myArray !== $myArray)
			return $myArray;

		foreach($myArray as &$myvalue)
			$myvalue = serialize($myvalue);
		
		$myArray = array_unique($myArray);

		foreach($myArray as &$myvalue)
			$myvalue = unserialize($myvalue);
		
		return $myArray;
	}

	/**
	 *    Function used to convert the array to an object.
	 *
	 * @access public
	 * @static
	 * @param array $data
	 * @return object
	 */
	public static function array2Object($data) {
		if((array) $data !== $data)
			return $data;

		$object = new stdClass();

		if($data) {
			foreach($data as $name => $value) {
				if($name) {
					$isNumericArray = false;
					
					if((array) $value === $value) {
						foreach($value as $k => $v) {
							if(is_numeric($k)) {
								$isNumericArray = true;
								break;
							}
						}
					}

					if($isNumericArray)
						$object->$name = $value;
					else
						$object->$name = Zamp_General::array2Object($value);
				}
			}
		}

		return $object;
	}

	/**
	 *    Function used to convert the object to an array.
	 *
	 * @access public
	 * @static
	 * @param object $data
	 * @return array
	 */
	public static function object2Array($data) {
		if((object) $data !== $data && (array) $data !== $data)
			return $data;
		
		if((object) $data === $data)
			$data = get_object_vars($data);

		return array_map([
			'Zamp_General',
			'object2Array'
		], $data);
	}

	/**
	 *    method used to generate/set multi dimentional array.
	 *
	 * @access public
	 * @static
	 * @since 1.4.7
	 * @param array $array
	 * @param mixed $value
     * @param array $source
	 * @return mixed
	 */
	public static function setMultiArrayValue($array, $value = [], $source = []) {
		if(!$array || (array) $array !== $array)
			return $value;
		
		$temp =& $source;
		
		foreach($array as $item) {
			if(!isset($temp[$item]) || (array) $temp[$item] !== $temp[$item])
                $temp[$item] = [];
            
			$temp =& $temp[$item];
		}
        
        $temp = $value;
		
		return $source;
	}

	/**
	 *    method used to get the value from the multi dimentional array
	 *
	 * @access public
	 * @static
	 * @since 4.0.2
	 * @param array $keys
	 * @param array $array
     * @param mixed $defaultValue
	 * @return mixed
	 */
	public static function getMultiArrayValue($keys, $array, $defaultValue = null) {
		if(!$keys || (array) $keys !== $keys)
			return $array;
		
		foreach($keys as $key) {
			if(!isset($array[$key]))
				return $defaultValue;
			
			$array = $array[$key];
		}
		
		return $array;
	}

	/**
	 *    method used to unset the value from the multi dimentional array
	 *
	 * @access public
	 * @static
	 * @since 4.0.2
	 * @param array $keys
	 * @param array $reference
	 * @return bool is value unset ?
	 */
	public static function unsetMultiArrayValue($keys, &$reference) {
		if(!$keys || (array) $keys !== $keys)
			return false;
		
		$size = count($keys);
		
		$i = 1;
		
		foreach($keys as $key) {
			if(!isset($reference[$key]))
				return false;
			elseif($size == $i) {
				unset($reference[$key]);
				
				return true;
			}
			
			$reference =& $reference[$key];
			
			$i++;
		}
	}

	/**
	 *    Merges 2 arrays recursively, replacing
	 *    entries with string keys with values from latter arrays.
	 *    If the entry or the next value to be assigned is an array, then it
	 *    automagically treats both arguments as an array.
	 *    Numeric entries are appended, not replaced, but only if they are
	 *    unique
	 *
	 *    calling: result = arrayMergeRecursiveDistinct(a1, a2)
	 *
	 *    Taken from http://php.net/manual/en/function.array-merge-recursive.php
	 *    Zamp Thank to mark.roduner@gmail.com
	 *
	 * @access public
	 * @static
	 * @since v1.4.8
	 */
	public static function arrayMergeRecursiveDistinct($base, $arrays) {
		$arrays = [$arrays];
		
		if((array) $base !== $base)
			$base = $base ?[$base] :[];
		
		foreach($arrays as $append) {
			if((array) $append !== $append)
				$append = [$append];
			
			foreach($append as $key => $value) {
				if(!isset($base[$key]) && !array_key_exists($key, $base) && !((integer) $key === $key || is_numeric($key))) {
					$base[$key] = $value;
					continue;
				}
				
				if((array) $value === $value || (isset($base[$key]) && (array) $base[$key] === $base[$key]))
					$base[$key] = Zamp_General::arrayMergeRecursiveDistinct((isset($base[$key]) ?$base[$key] :[]), $value);
				elseif((integer) $key === $key || is_numeric($key)) {
					if(!in_array($value, $base))
						$base[] = $value;
				}
				else
					$base[$key] = $value;
			}
		}
		
		return $base;
	}
    
	/**
	 *    Bytes conversion
	 *
	 * @access public
	 * @static
	 * @param integer $size
	 * @param integer $decimal [default: 2]
	 * @return string
	 */
	public static function byteCalculate($size, $decimal = 2) {
		if(is_numeric($size)) {
			$position = 0;
			$units = [
				' Bytes',
				' KB',
				' MB',
				' GB',
				' TB',
				' PB',
				' EB',
				' ZB',
				' YB'
			];

			while($size >= 1024 && ($size / 1024) >= 1) {
				$size /= 1024;
				$position++;
			}

			return round($size, $decimal).$units[$position];
		}
		else
			return '0 Bytes';
	}

	/**
	 *    set server header Status
	 *
	 * @access public
	 * @since 1.2.8
	 * @static
	 * @param integer $code
	 * @param string $text [optional]
	 * @return bool
	 */
	public static function setHeader($code = 200, $text = '') {
		if(headers_sent())
			return false;
		
		$statusCode = [
			100	=> 'Continue',
			101	=> 'Switching Protocols',
			102	=> 'Processing',
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			207	=> 'Multi-Status',
			208 => 'Already Reported',
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			307 => 'Temporary Redirect',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			426 => 'Upgrade Required',
			428 => 'Precondition Required',
			429 => 'Too Many Requests',
			431 => 'Request Header Fields Too Large',
			451 => 'Unavailable For Legal Reasons',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported',
			510 => 'Not Extended',
			511 => 'Network Authentication Required',
			598 => 'Network read timeout error',
			599 => 'Network connect timeout error'
		];
		
		if($code == '' || !is_numeric($code))
			throw new Exception('Status codes must be numeric');
		
		if($text == '' && isset($statusCode[$code]))
			$text = $statusCode[$code];
		
		if($text == '')
			throw new Exception('No status text available.  Please check your status code number or supply your own message text.');
		
		$server_protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ?$_SERVER['SERVER_PROTOCOL'] :false;
		
		if(substr(php_sapi_name(), 0, 3) == 'cgi')
			header("Status: $code $text", true);
		elseif($server_protocol == 'HTTP/1.1' || $server_protocol == 'HTTP/1.0')
			header($server_protocol." $code $text", true, $code);
		else
			header("HTTP/1.1 $code $text", true, $code);
		
		return true;
	}

	/**
	 *    Check, if the connection is via SSL
	 *
	 * @access public
	 * @since 4.2.3
	 * @static
	 * @return bool
	 */
	public static function isSslConnection() {
		if(
			(isset($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] == '443'))
				||
			(isset($_SERVER['HTTP_X_FORWARDED_PORT']) && ($_SERVER['HTTP_X_FORWARDED_PORT'] == '443'))
				||
			(isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == '1' || strtolower($_SERVER['HTTPS']) == 'on'))
				||
			(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https')
		)
			return true;
		else
			return false;
	}
	
	/**
	 *    Shorten an multidimensional array into a single dimensional array concatenating all keys with separator.
	 *
	 *    @example array('country' => array(0 => array('name' => 'Bangladesh', 'capital' => 'Dhaka')))
	 *          to array('country.0.name' => 'Bangladesh', 'country.0.capital' => 'Dhaka')
	 *
	 * @access public
	 * @since 5.5.20
	 * @static
	 * @param array $inputArray, arrays to be marged into a single dimensional array
	 * @param string $path, Default Initial path
	 * @param string $separator, array key path separator
	 * @return array, single dimensional array with key and value pair
	 */
	public static function arrayShort(array $inputArray, $path = null, $separator = '.') {
		$data = [];
		
		if($path !== null)
			$path .= $separator;
		
		foreach($inputArray as $key => &$value) {
			if((array) $value !== $value)
				$data[$path.$key] = $value;
			else
				$data = array_merge($data, self::arrayShort($value, $path.$key, $separator));
		}
		
		return $data;
	}
	
	/**
	 *    Unshorten a single dimensional array into multidimensional array.
	 *
	 *     @example array('country.0.name' => 'Bangladesh', 'country.0.capital' => 'Dhaka')
	 *          to array('country' => array(0 => array('name' => 'Bangladesh', 'capital' => 'Dhaka')))
	 *
	 * @access public
	 * @since 5.5.20
	 * @static
	 * @param array $data data to be converted into multidimensional array
	 * @param string $separator key path separator
	 * @return array multi dimensional array
	 */
	public static function arrayUnShort($data, $separator = '.') {
		$result = [];
		
		foreach($data as $key => $value) {
			if(strpos($key, $separator) !== false) {
				$str = explode($separator, $key, 2);
				$result[$str[0]][$str[1]] = $value;
				
				if(strpos($str[1], $separator))
					$result[$str[0]] = self::arrayUnShort($result[$str[0]], $separator);
			}
			else
				$result[$key] = is_array($value)? self::arrayUnShort($value, $separator) : $value;
		}
		
		return $result;
	}
    
    /**
	 *    Clear file cache
	 *
	 * @access public
	 * @since 5.5.28
	 * @static
	 * @param string $file
	 * @param bool $force
	 * @return bool
	 */
    public static function invalidate($file, $force = true) {
        return opcache_invalidate($file, $force);
    }
    
    /**
	 *    URL friendly base64 data
	 *
	 * @access public
	 * @since 5.5.31
	 * @static
	 * @param string $data
	 * @param string $type [encode, decode]
	 * @return string
	 */
    public static function base64_clean($data, $type = 'encode') {
        if($type == 'encode')
            return rtrim(strtr($data, '+/', '-_'), '=');
        else
            return str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT);
    }
    
    /**
	 *    URL friendly base64 encode
	 *
	 * @access public
	 * @since 5.5.31
	 * @static
	 * @param string $data
	 * @return string
	 */
    public static function base64_encode($data) {
        return self::base64_clean(base64_encode($data));
    }
    
    /**
	 *    URL friendly base64 decode
	 *
	 * @access public
	 * @since 5.5.31
	 * @static
	 * @param string $data
     * @param boolean $strict [default: false]
	 * @return string
	 */
    public static function base64_decode($data, $strict = false) {
        return base64_decode(self::base64_clean($data, 'decode'), $strict);
    }
}
/** End of File **/
