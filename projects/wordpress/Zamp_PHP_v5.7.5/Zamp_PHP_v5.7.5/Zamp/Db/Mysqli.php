<?php
/**
 *    This file is part of the Zamp PHP Framework.
 *    (c) Mathan kumar R <phpmathan@gmail.com>
 *
 *    For the full copyright and license information, please view the LICENSE
 *    file that was distributed with this source code.
 *
 * @package Zamp_Db_Abstract
 */

/**
 *    Mysqli Database class
 *
 * @abstract
 * @category Zamp
 * @package Zamp_Db_Abstract
 * @subpackage Zamp_Db_Mysqli
 * @author Mathan kumar R <phpmathan@gmail.com>
 * @version SVN: $Id: Mysqli.php 381 2018-12-25 13:09:18Z phpmathan $
 */
class Zamp_Db_Mysqli extends Zamp_Db_Abstract {
	/**
	 *    Construct function
	 *
	 * @access public
	 * @param array $db_info
	 * @return void
	 */
	public function __construct($db_info) {
		Zamp::$core->isExtensionLoaded('mysqli');

		if(isset($db_info['query_callback']))
			$this->queryCallback = $db_info['query_callback'];

		$this->serverHash = md5($db_info['host_name'].':'.$db_info['host_port'].':'.$db_info['username'].':'.$db_info['password']);
		
		$this->dbName = $db_info['dbname'];

		$db_info['connection_cache_file'] = 'zamp_db_'.$this->serverHash.'_'.$this->dbName;
		$db_info['connection_cache_file'] = $db_info['tmp_folder'].$db_info['connection_cache_file'].'.php';
		
		@include_once $db_info['connection_cache_file'];

		if(isset($dbCache))
			$this->_db_cache = $dbCache;
		
		if(isset($dbCache['thread_id']))
			$db_info['thread_id'] = $dbCache['thread_id'];
		
		if(isset($dbCache['tables']))
			$db_info['write_required'] = false;
		else
			$db_info['write_required'] = true;
		
		$this->db_info = $db_info;
	}

	/**
	 *    Connect to the Database
	 *
	 * @access protected
	 * @param $boolReturn bool
	 */
	protected function connectDB($boolReturn = false) {
		$runBasicQuery = true;
		
		$this->connection = mysqli_init();
		
		if(isset($this->db_info['ssl_settings'])) {
			$this->connection->ssl_set(
				$this->db_info['ssl_settings']['key'],
				$this->db_info['ssl_settings']['cert'],
				$this->db_info['ssl_settings']['ca'],
				$this->db_info['ssl_settings']['capath'],
				$this->db_info['ssl_settings']['cipher']
			);
        }
		
        if($this->db_info['persistent']) {
            @$this->connection->real_connect('p:'.$this->db_info['host_name'], $this->db_info['username'], $this->db_info['password'], $this->db_info['dbname'], $this->db_info['host_port']);
			
			if(!$this->connection->connect_errno) {
				if($this->db_info['thread_id'] == $this->connection->thread_id)
					$runBasicQuery = false;
				else {
					$this->_db_cache['thread_id'] = $this->connection->thread_id;
					$this->db_info['write_required'] = true;
				}
			}
        }
        else {
            @$this->connection->real_connect($this->db_info['host_name'], $this->db_info['username'], $this->db_info['password'], $this->db_info['dbname'], $this->db_info['host_port']);
        }
		
		if($this->connection->connect_errno) {
			if($boolReturn === true)
				return false;
			
			$dbConfig = $this->db_info;
			$dbConfig['password'] = '******';
			
			throw new Zamp_Db_Exception("Database Connection Error!<br/><br/><font color='red'>".$this->connection->connect_error."</font><br/><br/>Database Configuration:<br/>".var_export($dbConfig, true), 18);
		}
		elseif($runBasicQuery) {
			$this->setDbCharset();
			$this->_db_cache['tables'] = $this->getDbTables();
		}
		
		if($runBasicQuery && $this->db_info['write_required']) {
			file_put_contents($this->db_info['connection_cache_file'], "<?php\n\n\$dbCache = ".var_export($this->_db_cache, true).";\n\n", LOCK_EX);
            Zamp_General::invalidate($this->db_info['connection_cache_file']);
        }
		
		return true;
	}

	/**
	 *    Close the Database Connection
	 *
	 * @access public
	 * @return bool
	 */
	protected function closeDB() {
		if($this->checkStatus())
			return $this->connection->close();
		
		return false;
	}

	/**
	 *    set the Database character set
	 *
	 * @access public
	 * @param string $char_set
	 * @param string $dbcollation
	 * @return bool
	 */
	public function setDbCharset($char_set = null, $dbcollation = null) {
		if(!$char_set)
			$char_set = $this->db_info['char_set'];
		
		if(!$dbcollation)
			$dbcollation = $this->db_info['dbcollation'];
		
		if($char_set && $dbcollation) {
			$this->runQuery("SET NAMES '$char_set' COLLATE '$dbcollation';");

			return $this->connection->set_charset($char_set);
		}
		
		if($char_set)
			return $this->connection->set_charset($char_set);
	}

	/**
	 *    checking the query or connection status
	 *
	 * @access protected
	 * @param string $query
	 * @return bool
	 */
	protected function checkStatus($query = '') {
		if($query)
			return (object) $query === $query;
		else
			return (object) $this->connection === $this->connection;
	}

	/**
	 *    get table fields informations
	 *
	 * @access protected
	 * @param string $tableName
	 * @return array
	 */
	protected function getTableFieldsInfo($tableName) {
		if(!$this->checkStatus())
			$this->connectDB();
		
		$sql = "SELECT * FROM $tableName LIMIT 1";
		
		$result = $this->runQuery($sql);
		$fields = $this->fetch($result, 'fields');
		
		$out = [];
		
		foreach($fields as $v) {
			$flags = $this->getFieldFlags($v->flags);
			
			$out[$v->name] = $flags;
		}
		
		return $out;
	}

	/**
	 *    get the flags for the given flag value
	 *
	 * @access protected
	 * @param integer $flag
	 * @return array
	 */
	protected function getFieldFlags($flag) {
		$flags = [
			MYSQLI_NOT_NULL_FLAG => 'NOT_NULL_FLAG',
			MYSQLI_PRI_KEY_FLAG => 'PRI_KEY_FLAG',
			MYSQLI_UNIQUE_KEY_FLAG => 'UNIQUE_KEY_FLAG',
			MYSQLI_MULTIPLE_KEY_FLAG => 'MULTIPLE_KEY_FLAG',
			MYSQLI_BLOB_FLAG => 'BLOB_FLAG',
			MYSQLI_UNSIGNED_FLAG => 'UNSIGNED_FLAG',
			MYSQLI_ZEROFILL_FLAG => 'ZEROFILL_FLAG',
			MYSQLI_AUTO_INCREMENT_FLAG => 'AUTO_INCREMENT_FLAG',
			MYSQLI_TIMESTAMP_FLAG => 'TIMESTAMP_FLAG',
			MYSQLI_SET_FLAG => 'SET_FLAG',
			MYSQLI_NUM_FLAG => 'NUM_FLAG',
			MYSQLI_PART_KEY_FLAG => 'PART_KEY_FLAG',
			MYSQLI_GROUP_FLAG => 'GROUP_FLAG',
			MYSQLI_ENUM_FLAG => 'ENUM_FLAG',
			MYSQLI_BINARY_FLAG => 'BINARY_FLAG',
			65536 => 'UNIQUE_FLAG',
		];
		
		$result = [];
		
		foreach($flags as $constant => $value) {
			if(($flag & $constant) > 0)
				$result[$value] = true;
		}
		
		return $result;
	}
	
	/**
	 *    database query fetch methods
	 *
	 * @access public
	 * @param object $result
	 * @param string $type
	 * @return mixed
	 */
	public function fetch($result, $type = '') {
		switch($type) {
			case 'name':
				return $result->fetch_assoc();
			case 'number':
				return $result->fetch_row();
			case 'object':
				return $result->fetch_object();
			case 'field':
				return $result->fetch_field();
			case 'fields':
				return $result->fetch_fields();
			case 'mixed':
			default:
				return $result->fetch_array();
		}
	}

	/**
	 *    database query method
	 *
	 * @access public
	 * @param string $sql
	 * @param string|array $params
	 * @return object
	 */
	public function runQuery($sql, $params = '') {
		if(!$this->connection)
			$this->connectDB();
		
		if($params)
			$sql = $this->prepareSql($sql, $params);
		
		$Zampf = Zamp::$core;
		
		$Zampf->_timer->startTimer('dbQuery');
		
		$result = @$this->connection->query($sql);
		
		if(!$result) {
			if($this->getLastErrorCode() == 2006 && $this->connectDB(true))
				return $this->runQuery($sql);
			
			$error = wordwrap($this->getLastErrorMessage(), 100, '<br/>');
			$error .= '<br/><br/>';
			$error .= '<font color="red">SQL</font> : <font color="blue">'.$sql.'</font>';
			
			throw new Zamp_Db_Exception($error, 20);
		}
		
		$query_time = $Zampf->_timer->getProcessedTime('dbQuery', 5);
		
		$Zampf->_timer->resetTimer("dbQuery");
		
		$this->_runned_sql_queries[] = [
			"query" => $sql,
			"time_taken" => $query_time
		];
		
		if($this->queryCallback) {
			$queryCallback = $this->queryCallback;
			$queryCallback($sql, $query_time, $this->queryId);
		}
		
		$this->queryId = null;
		
		return $result;
	}

	/**
	 *    get the last error message
	 *
	 * @abstract
	 * @access public
	 * @return string
	 */
	public function getLastErrorMessage() {
		return $this->connection->error;
	}

	/**
	 *    get the last error code
	 *
	 * @abstract
	 * @access public
	 * @return integer
	 */
	public function getLastErrorCode() {
		return $this->connection->errno;
	}

	/**
	 *    free query result
	 *
	 * @access protected
	 * @param object $result
	 */
	protected function freeResult($result) {
		$result->free();
	}

	/**
	 *    This method used to get the number rows affected in last insert, delete, update
	 *
	 * @access public
	 * @return integer
	 */
	public function getAffectedRows() {
		return $this->connection->affected_rows;
	}

	/**
	 *    method returns last insert id
	 *
	 * @access public
	 * @return integer
	 */
	public function getLastInsertId() {
		return $this->connection->insert_id;
	}

	/**
	 *    Block SQL Injection Attact
	 *
	 * @access public
	 * @param array|string $data
	 * @param array $exceptions list of array keys to jump. used for description like fields [optional]
	 * @return array|string
	 */
	public function blockSqlInjection($data, $exceptions = []) {
		$this->checkDbConnection();
		
		if((array) $data === $data) {
			$new_data = [];
			foreach($data as $key => $value) {
				if((array) $value === $value)
					$new_data[$this->connection->real_escape_string($key)] = $this->blockSqlInjection($value, $exceptions);
				elseif(in_array($key, $exceptions))
					$new_data[$key] = $value;
				else
					$new_data[$this->connection->real_escape_string($key)] = $this->connection->real_escape_string(trim($value));
			}

			return $new_data;
		}
		elseif((string) $data === $data)
			return $this->connection->real_escape_string(trim($data));
		else
			return trim($data);
	}
}
/** End of File **/
