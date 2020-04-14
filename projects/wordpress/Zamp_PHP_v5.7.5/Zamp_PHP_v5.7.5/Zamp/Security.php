<?php
/**
 *    This file is part of the Zamp PHP Framework.
 *    (c) Mathan kumar R <phpmathan@gmail.com>
 *
 *    For the full copyright and license information, please view the LICENSE
 *    file that was distributed with this source code.
 *
 * @package Zamp_Security
 */

/**
 *    Zamp PHP Framework Security class
 *
 * @category Zamp
 * @package Zamp_Security
 * @author Mathan kumar R <phpmathan@gmail.com>
 * @version SVN: $Id: Security.php 377 2018-03-09 06:17:39Z phpmathan $
 */
class Zamp_Security {
	/**
	 *    Random Security code.
	 *
	 * @access public
	 * @var string $secret_key
	 */
	public $secret_key = "kjfbg98g5itubegkjdfbgdg98dsapfpo43htwe08tgsdgioasgdg";

	/**
	 *    construct method
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		if(isset(Zamp::$core->_config['encryption_secret_key']))
			$this->secret_key = Zamp::$core->_config['encryption_secret_key'];
	}

	/**
	 *    Encode the given string.
	 *
	 * @access public
	 * @param string $str String to encrypt.
	 * @param string $key password or key for this encryption (optional)
	 * @param string $identifier identifier to use (optional)
	 * @param string $cipher cipher method (optional) 
	 * @return string
	 */
	public function encode($str, $key = '', $identifier = '$', $cipher = 'bf-cbc') {
		Zamp::$core->isExtensionLoaded('openssl');
		
		if(!$key)
			$key = $this->secret_key;
		
		$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
		$str = openssl_encrypt($str, $cipher, $key, 0, $iv);

		return Zamp_General::base64_encode($str.$identifier.base64_encode($iv));
	}

	/**
	 *    Decode the given encoded string.
	 *
	 * @access public
	 * @param string $crypt_arr Encrypted Text
	 * @param string $key password or key for this decryption (optional)
	 * @param string $identifier identifier to use (optional)
	 * @param string $cipher cipher method (optional) 
	 * @return string|null
	 */
	public function decode($crypt_arr, $key = '', $identifier = '$', $cipher = 'bf-cbc') {
		Zamp::$core->isExtensionLoaded('openssl');
		
		if(!$key)
			$key = $this->secret_key;
		
		$crypt_arr = Zamp_General::base64_decode($crypt_arr);
		$crypt_arr = explode($identifier, $crypt_arr);
		
		if(!isset($crypt_arr[1]))
			return;
		
		$data = openssl_decrypt($crypt_arr[0], $cipher, $key, 0, base64_decode($crypt_arr[1]));

		return ($data !== false) ?$data :null;
	}

	/**
	 *    Generating Random Password
	 *
	 * @access public
	 * @static
	 * @param integer $length [default: 9]
	 * @return string
	 */
	public static function generatePassword($length = 9) {
		$vowels = 'aeuy';
		$consonants = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
		//$consonants = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890~!@#$%^&*()';
		$password = '';
		$alt = time() % 2;
		for($i = 0; $i < $length; $i++) {
			if($alt == 1) {
				$password .= $consonants[(rand() % strlen($consonants))];
				$alt = 0;
			}
			else {
				$password .= $vowels[(rand() % strlen($vowels))];
				$alt = 1;
			}
		}

		return $password;
	}

	/**
	 *    Convert String into ASCII Code.
	 *
	 * @access public
	 * @static
	 * @param string $str String to convert into ASCII
	 * @return string
	 */
	public static function string2Ascii($str) {
		$asc = [
			'a' => '\141',
			'b' => '\142',
			'c' => '\143',
			'd' => '\144',
			'e' => '\145',
			'f' => '\146',
			'g' => '\147',
			'h' => '\150',
			'i' => '\151',
			'j' => '\152',
			'k' => '\153',
			'l' => '\154',
			'm' => '\155',
			'n' => '\156',
			'o' => '\157',
			'p' => '\160',
			'q' => '\161',
			'r' => '\162',
			's' => '\163',
			't' => '\164',
			'u' => '\165',
			'v' => '\166',
			'w' => '\167',
			'x' => '\170',
			'y' => '\171',
			'z' => '\172',
			'A' => '\101',
			'B' => '\102',
			'C' => '\103',
			'D' => '\104',
			'E' => '\105',
			'F' => '\106',
			'G' => '\107',
			'H' => '\110',
			'I' => '\111',
			'J' => '\112',
			'K' => '\113',
			'L' => '\114',
			'M' => '\115',
			'N' => '\116',
			'O' => '\117',
			'P' => '\120',
			'Q' => '\121',
			'R' => '\122',
			'S' => '\123',
			'T' => '\124',
			'U' => '\125',
			'V' => '\126',
			'W' => '\127',
			'X' => '\130',
			'Y' => '\131',
			'Z' => '\132',
			'~' => '\176',
			'!' => '\41',
			'@' => '\100',
			'#' => '\43',
			'$' => '\44',
			'%' => '\45',
			'^' => '\136',
			'&' => '\46',
			'*' => '\52',
			'(' => '\50',
			')' => '\51',
			'-' => '\55',
			'_' => '\137',
			'=' => '\75',
			'+' => '\53',
			'|' => '\174',
			'\\' => '\134',
			'[' => '\133',
			']' => '\135',
			'{' => '\173',
			'}' => '\175',
			':' => '\72',
			';' => '\73',
			'"' => '\42',
			"'" => '\47',
			'<' => '\74',
			'>' => '\76',
			',' => '\54',
			'.' => '\56',
			'?' => '\77',
			'/' => '\57',
			'0' => '\60',
			'1' => '\61',
			'2' => '\62',
			'3' => '\63',
			'4' => '\64',
			'5' => '\65',
			'6' => '\66',
			'7' => '\67',
			'8' => '\70',
			'9' => '\71',
		];
		
		$len = strlen($str);

		$result = "";

		for($i = 0; $i < $len; $i++) {
			$a = substr($str, $i, 1);
			if($asc["$a"])
				$result .= $asc["$a"];
			else
				$result .= $a;
		}

		return $result;
	}

	/**
	 *    Convert ASCII Code into String.
	 *
	 * @access public
	 * @static
	 * @param string $str
	 * @return string
	 */
	public static function ascii2String($str) {
		$uni = [
			'141' => 'a',
			'142' => 'b',
			'143' => 'c',
			'144' => 'd',
			'145' => 'e',
			'146' => 'f',
			'147' => 'g',
			'150' => 'h',
			'151' => 'i',
			'152' => 'j',
			'153' => 'k',
			'154' => 'l',
			'155' => 'm',
			'156' => 'n',
			'157' => 'o',
			'160' => 'p',
			'161' => 'q',
			'162' => 'r',
			'163' => 's',
			'164' => 't',
			'165' => 'u',
			'166' => 'v',
			'167' => 'w',
			'170' => 'x',
			'171' => 'y',
			'172' => 'z',
			'101' => 'A',
			'102' => 'B',
			'103' => 'C',
			'104' => 'D',
			'105' => 'E',
			'106' => 'F',
			'107' => 'G',
			'110' => 'H',
			'111' => 'I',
			'112' => 'J',
			'113' => 'K',
			'114' => 'L',
			'115' => 'M',
			'116' => 'N',
			'117' => 'O',
			'120' => 'P',
			'121' => 'Q',
			'122' => 'R',
			'123' => 'S',
			'124' => 'T',
			'125' => 'U',
			'126' => 'V',
			'127' => 'W',
			'130' => 'X',
			'131' => 'Y',
			'132' => 'Z',
			'176' => '~',
			'41' => '!',
			'100' => '@',
			'43' => '#',
			'44' => '$',
			'45' => '%',
			'136' => '^',
			'46' => '&',
			'52' => '*',
			'50' => '(',
			'51' => ')',
			'55' => '-',
			'137' => '_',
			'75' => '=',
			'53' => '+',
			'174' => '|',
			'134' => '\\',
			'133' => '[',
			'135' => ']',
			'173' => '{',
			'175' => '}',
			'72' => ':',
			'73' => ';',
			'42' => '"',
			'47' => "'",
			'74' => '<',
			'76' => '>',
			'54' => ',',
			'56' => '.',
			'77' => '?',
			'57' => '/',
			'60' => '0',
			'61' => '1',
			'62' => '2',
			'63' => '3',
			'64' => '4',
			'65' => '5',
			'66' => '6',
			'67' => '7',
			'70' => '8',
			'71' => '9',
		];
		
		$new = preg_replace('/\\\\/si', ',', $str);
		$new = explode(',', $new);

		$len = count($new);

		for($i = 0; $i < $len; $i++) {
			$a = $new[$i];

			if(strpos($a, ' ') !== false) {
				$mathan = true;
				$kumar = strlen($a);
				$a1 = substr($a, 2, 1);
				$a2 = substr($a, 3, 1);

				if($a1 == ' ')
					$kumar = $kumar - 2;
				if($a2 == ' ')
					$kumar = $kumar - 3;

				$a = trim($a);
			}

			if($uni["$a"])
				$result .= $uni["$a"];
			else
				$result .= $a;

			if($mathan) {
				for($h = 0; $h < $kumar; $h++)
					$result .= ' ';

				$kumar = false;
				$mathan = false;
			}
		}

		return $result;
	}
	
	/**
	 *    Remove Invisible Characters
	 *
	 * This prevents sandwiching null characters
	 * between ascii characters, like Java\0script.
	 *
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	public static function removeInvisibleCharacters($str, $urlEncoded = true) {
		$nonDisplayables = array();
		
		// every control character except newline (dec 10),
		// carriage return (dec 13) and horizontal tab (dec 09)
		if($urlEncoded) {
			$nonDisplayables[] = '/%0[0-8bcef]/';	// url encoded 00-08, 11, 12, 14, 15
			$nonDisplayables[] = '/%1[0-9a-f]/';	// url encoded 16-31
		}
		
		$nonDisplayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127
		
		do {
			$str = preg_replace($nonDisplayables, '', $str, -1, $count);
		}
		while($count);
		
		return $str;
	}
	
	/**
	 *    Sanitize Filename
	 *
	 * @param	string	$str		Input file name
	 * @param 	bool	$relative_path	Whether to preserve paths
	 * @return	string
	 */
	public static function sanitizeFilename($str, $relativePath = false) {
		$bad = [
			'../', '<!--', '-->', '<', '>',
            "'", '"', '&', '$', '#',
            '{', '}', '[', ']', '=',
            ';', '?', '%20', '%22',
            '%3c',		// <
            '%253c',	// <
            '%3e',		// >
            '%0e',		// >
            '%28',		// (
            '%29',		// )
            '%2528',	// (
            '%26',		// &
            '%24',		// $
            '%3f',		// ?
            '%3b',		// ;
            '%3d'		// =
		];
		
		if(!$relativePath) {
			$bad[] = './';
			$bad[] = '/';
		}
		
		$str = self::removeInvisibleCharacters($str, false);
		
		do {
			$old = $str;
			$str = str_replace($bad, '', $str);
		}
		while($old !== $str);
		
		return stripslashes($str);
	}
	
	/**
	 *    Strip Image Tags
	 *
	 * @param	string	$str
	 * @return	string
	 */
	public function stripImageTags($str) {
		return preg_replace(
			[
				'#<img[\s/]+.*?src\s*=\s*(["\'])([^\\1]+?)\\1.*?\>#i',
				'#<img[\s/]+.*?src\s*=\s*?(([^\s"\'=<>`]+)).*?\>#i'
			],
			'\\2',
			$str
		);
	}
}
/** End of File **/
