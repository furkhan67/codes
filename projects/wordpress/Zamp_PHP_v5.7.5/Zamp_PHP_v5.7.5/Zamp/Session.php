<?php
/**
 *    This file is part of the Zamp PHP Framework.
 *    (c) Mathan kumar R <phpmathan@gmail.com>
 *
 *    For the full copyright and license information, please view the LICENSE
 *    file that was distributed with this source code.
 *
 * @package Zamp_Session
 */

/**
 *    Zamp PHP Framework Session class
 *
 *    CREATE TABLE IF NOT EXISTS `sessions` (
 *      `sess_id` char(40) NOT NULL,
 *      `sess_data` mediumtext NOT NULL,
 *      `sess_time` int(15) NOT NULL,
 *      PRIMARY KEY (`sess_id`)
 *    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 *
 * @category Zamp
 * @package Zamp_Session
 * @since v1.3.0
 * @author Mathan kumar R <phpmathan@gmail.com>
 * @version SVN: $Id: Session.php 377 2018-03-09 06:17:39Z phpmathan $
 */
class Zamp_Session {
	/**
	 *    Session database name
	 *
	 * @access private
	 * @var $_session_db
	 */
	private $_session_db;
	/**
	 *    session table name
	 *
	 * @access private
	 * @var $_session_table
	 */
	private $_session_table;
	/**
	 *    session configuration property
	 *
	 * @access private
	 * @var $_session_config
	 */
	private $_session_config = [];

	/**
	 *    Construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct($sessionConf = []) {
		$cookieDefaults = session_get_cookie_params();
		
		$this->_session_config = array_merge([
			'session_name' => 'ZampPHP',
			'auto_start' => false,
			'session_handler' => 'file',
			'session_cookie_lifetime' => $cookieDefaults['lifetime'],
			'session_cookie_path' => $cookieDefaults['path'],
			'session_cookie_domain' => $cookieDefaults['domain'],
			'session_cookie_secure' => $cookieDefaults['secure'],
			'session_cookie_httponly' => isset($cookieDefaults['httponly']) ?$cookieDefaults['httponly'] :false,
			'session_save_path' => _PROJECT_PATH_.'data/tmp/sessions',
		], $sessionConf);
		
		if(
			!isset($this->_session_config['session_handler'])
				||
			($this->_session_config['session_handler'] === 'file')
		) {
			$sessionSavePathFailed = false;

			$sessionSavePath = realpath($this->_session_config['session_save_path']);
			if(!is_dir($sessionSavePath)) {
				if(!mkdir($sessionSavePath, 0777, true))
					$sessionSavePathFailed = true;
			}
			elseif(!is_writable($sessionSavePath))
				$sessionSavePathFailed = true;

			if(!$sessionSavePathFailed)
				session_save_path($this->_session_config['session_save_path']);
			else
				throw new Zamp_Exception('Session Save Path Folder <font color=blue>'.$sessionSavePath.'</font> can not be accessible!');
		}

		ini_set('session.use_trans_sid', 0);
		
		// < 7.1.0
		ini_set('session.hash_function', 1);
		ini_set('session.hash_bits_per_character', 4);
		
		// >= 7.1.0
		ini_set('session.sid_length', 40);
		ini_set('session.sid_bits_per_character', 4);
		
		session_name($this->_session_config['session_name']);

		if(isset($this->_session_config['session_id'])) {
			if(ini_get('session.use_cookies'))
				ini_set('session.use_cookies', 0);

			session_id($this->_session_config['session_id']);
		}
		else {
			ini_set('session.use_strict_mode', 1);
			ini_set('session.use_cookies', 1);
			ini_set('session.use_only_cookies', 1);
		}

		session_set_cookie_params(
			$this->_session_config['session_cookie_lifetime'],
			$this->_session_config['session_cookie_path'],
			$this->_session_config['session_cookie_domain'],
			$this->_session_config['session_cookie_secure'],
			$this->_session_config['session_cookie_httponly']
		);

		if(isset($this->_session_config['session_cache_limiter']))
			session_cache_limiter($this->_session_config['session_cache_limiter']);

		if(isset($this->_session_config['session_cache_expire']))
			session_cache_expire($this->_session_config['session_cache_expire']);

		if($this->_session_config['session_handler'] === 'mysql')
			$this->_sessionHandlingUsingMySQL();
		elseif(
			!empty($this->_session_config['session_handler'])
				&&
			($this->_session_config['session_handler'] !== 'file')
				&&
			($this->_session_config['session_handler'] !== 'php')
		) {
			$this->_session_config = redirect($this->_session_config['session_handler'], [
				$this->_session_config
			]);
		}

		register_shutdown_function('session_write_close');

		Zamp::$cleanExitCallbacks['session_write_close'] = function($param) {
			session_write_close();
		};

		session_start();

		$this->_session_config['_started_session_id'] = session_id();

		Zamp::$core->_config['session_settings'] = $this->_session_config;
	}

	/**
	 *    checking session storage system
	 *
	 * @access private
	 * @return void
	 */
	private function _set_new_session_id($id) {
		$this->_session_config['_started_session_id'] = $id;
		Zamp::$core->_config['session_settings']['_started_session_id'] = $id;
	}

	/**
	 *    checking session storage system
	 *
	 * @access private
	 * @return void
	 */
	private function _sessionHandlingUsingMySQL() {
		$db = $this->_session_config['db_instance_name'];
		
		$dbSettings = getConf('database_info/'.$db);
		
		if(((array) $dbSettings === $dbSettings) && isset($dbSettings['driver']))
			$this->_session_db = Zamp::$core->$db;
		else
			throw new Zamp_Exception('Session Database instance name <font color=red>'.$db.'</font> not found in $config[\'database_info\']');
		
		$this->_session_config = array_merge([
			'db_table' => 'sessions',
			'db_id_col' => 'sess_id',
			'db_data_col' => 'sess_data',
			'db_time_col' => 'sess_time',
			'db_lock_timeout' => 300,
		], $this->_session_config);
		
		$session_table = 't_'.$this->_session_config['db_table'];
		$this->_session_table = $this->_session_db->$session_table;
		
		$this->_session_config['_is_locked'] = false;
		
		session_set_save_handler(
			[$this, 'dbSessionOpen'],
			[$this, 'dbSessionClose'],
			[$this, 'dbSessionRead'],
			[$this, 'dbSessionWrite'],
			[$this, 'dbSessionDestroy'],
			[$this, 'dbSessionGC']
		);
	}

	/**
	 *    database session open
	 *
	 * @access public
	 * @return bool
	 */
	public function dbSessionOpen($path, $name) {
		return true;
	}

	/**
	 *    database session close
	 *
	 * @access public
	 * @return bool
	 */
	public function dbSessionClose() {
		$this->_releaseLock();
		
		return true;
	}

	/**
	 *    database session reading
	 *
	 * @access public
	 * @return mixed
	 */
	public function dbSessionRead($id) {
		$id = $this->_session_db->blockSqlInjection($id);
		
		if(!$this->_getLock($id))
			return '';
		
		$sql = "SELECT {$this->_session_config['db_data_col']}
    			FROM {$this->_session_table}
    			WHERE {$this->_session_config['db_id_col']} = '$id'";

		$query = $this->_session_db->queryId('_zamp_session_handler')->execute($sql, '', false);
		$result = $this->_session_db->returnQuery($query, $sql, '', false);

		return (isset($result[0])) ?$result[0][$this->_session_config['db_data_col']] :'';
	}

	/**
	 *    database session writing
	 *
	 * @access public
	 * @return bool
	 */
	public function dbSessionWrite($id, $data) {
		if(!$this->_session_config['_is_locked'])
			return false;
		
		$id = $this->_session_db->blockSqlInjection($id);
		
		if($id !== $this->_session_config['_started_session_id']) {
			if(!$this->_releaseLock() || !$this->_getLock($id))
				return false;
			
			$this->_set_new_session_id($id);
		}
		
		$data = $this->_session_db->blockSqlInjection($data);

		$sql = "REPLACE INTO {$this->_session_table} (`{$this->_session_config['db_id_col']}`, `{$this->_session_config['db_data_col']}`, `{$this->_session_config['db_time_col']}`) VALUES ('$id', '$data', '".Zamp::$core->systemTime()."')";

		$this->_session_db->queryId('_zamp_session_handler')->execute($sql, '', false);

		return true;
	}

	/**
	 *    database session deleting
	 *
	 * @access public
	 * @return bool
	 */
	public function dbSessionDestroy($id) {
		if(!$this->_session_config['_is_locked'])
			return false;
		
		$id = $this->_session_db->blockSqlInjection($id);

		$sql = "DELETE
    			FROM {$this->_session_table}
    			WHERE {$this->_session_config['db_id_col']}='$id'";

		$this->_session_db->queryId('_zamp_session_handler')->execute($sql, '', false);
		
		return $this->_releaseLock();
	}

	/**
	 *    database session cleaning old sessions
	 *
	 * @access public
	 * @return bool
	 */
	public function dbSessionGC($lifetime) {
		$time = Zamp::$core->systemTime();
		$sql = "DELETE
    			FROM {$this->_session_table}
    			WHERE {$this->_session_config['db_time_col']} + $lifetime < $time";

		$this->_session_db->queryId('_zamp_session_handler')->execute($sql, '', false);

		return true;
	}
	
	/**
	 *    get lock
	 *
	 * @access private
	 * @return bool
	 */
	private function _getLock($id) {
		if($this->_session_config['_is_locked'])
			return true;
		
		$sql = "SELECT GET_LOCK('$id', {$this->_session_config['db_lock_timeout']}) AS zamp_session_lock";
		
		$query = $this->_session_db->queryId('_zamp_session_handler')->execute($sql, '', false);
		$result = $this->_session_db->returnQuery($query, $sql, '', false);
		
		return ($this->_session_config['_is_locked'] = !empty($result[0]['zamp_session_lock']));
	}
	
	/**
	 *    release lock
	 *
	 * @access private
	 * @return bool
	 */
	private function _releaseLock() {
		if(!$this->_session_config['_is_locked'])
			return true;
		
		$sql = "SELECT RELEASE_LOCK('{$this->_session_config['_started_session_id']}') AS zamp_session_lock";
		
		$query = $this->_session_db->queryId('_zamp_session_handler')->execute($sql, '', false);
		$result = $this->_session_db->returnQuery($query, $sql, '', false);
		
		return !($this->_session_config['_is_locked'] = empty($result[0]['zamp_session_lock']));
	}
	
	/**
	 *    generate session id
	 * 	
	 * @access public
	 * @since v5.5.23
	 * @return string
	 */
	public static function generateSessionId() {
		if(version_compare(PHP_VERSION, '7.1.0', '>=')) {
			ini_set('session.sid_length', 40);
			ini_set('session.sid_bits_per_character', 4);
			
			$id = session_create_id();
		}
		else {
			$id = openssl_random_pseudo_bytes(20);
			$id = bin2hex($id);
		}
		
		return $id;
	}
	
	/**
	 *    is valid session id
	 * 	
	 * @access public
	 * @since v5.5.23
	 * @return bool
	 */
	public static function isValidSessionId($id) {
		if(empty($id) || !($id = trim($id)))
			return false;
		
		return preg_match('/^[a-f0-9]{40}$/', $id);
	}
}
/** End of File **/
