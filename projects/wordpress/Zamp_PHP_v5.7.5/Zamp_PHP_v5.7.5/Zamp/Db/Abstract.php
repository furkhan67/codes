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
 *    Database Abstraction class
 *
 * @abstract
 * @category Zamp
 * @package Zamp_Db_Abstract
 * @author Mathan kumar R <phpmathan@gmail.com>
 * @version SVN: $Id: Abstract.php 379 2018-05-20 04:19:14Z phpmathan $
 */
abstract class Zamp_Db_Abstract {
	/**
	 *    Server connection hash
	 *
	 * @access public
	 * @var string $serverHash
	 */
	public $serverHash = '';
	/**
	 *    database name for easy access in joins
	 *
	 * @access public
	 * @var string $serverHash
	 */
	public $dbName = '';
	/**
	 *    Database connection variable
	 *
	 * @access protected
	 * @var object $connection
	 */
	public $connection;
	/**
	 *    by default transaction level assigned to 0, means no transaction
	 *
	 * @access protected
	 * @var integer $_transaction_level
	 */
	protected $_transaction_level = 0;
	/**
	 *    database cache
	 *
	 * @access protected
	 * @var array $_db_cache
	 */
	protected $_db_cache = [];
	/**
	 *    Store executed sql queries.
	 *
	 * @access public
	 * @var array $_runned_sql_queries
	 */
	public $_runned_sql_queries = [];
	/**
	 *    Callback function to call when query executed
	 *
	 * @access public
	 * @var string $queryCallback
	 */
	public $queryCallback = '';
	/**
	 *    Query ID which is send as 3rd parameter to queryCallback
	 *
	 * @access public
	 * @var string|integer $queryId
	 */
	public $queryId = null;
	/**
	 *    catch running sql queries at runtime.
	 *
	 * @access public
	 * @var array $_sql_runtime_queries
	 */
	public $_sql_runtime_queries = [];

	/**
	 *    Call function for display error message with parameters, when non defined function calls
	 *
	 * @access public
	 * @param string @fun_name
	 * @param array $args
	 */
	public function __call($fun_name, $args) {
		$errorDialog = "Undefined function <font color='blue'>$fun_name</font> called with the following arguments";
		$errorDialog .= '<pre>'.var_export($args, true).'</pre>';
		throw new Zamp_Db_Exception($errorDialog, 8);
	}

	/**
	 *    __call magic method
	 *
	 * @access public
	 * @param string $name
	 */
	public function __get($name) {
		if(isset($this->_db_cache['tables'][$name]))
			return $this->_db_cache['tables'][$name];
		
		if(!$this->checkStatus()) {
			$this->connectDB();

			if(isset($this->_db_cache['tables'][$name]))
				return $this->_db_cache['tables'][$name];
		}

		throw new Zamp_Db_Exception('Table name <font color="red">'.preg_replace('/^t_/', '', $name, 1).'</font> not found in database <font color="red">`'.$this->db_info['dbname'].'`</font>!', 19);
	}

	/**
	 *    check whether database connected, if not connect the database
	 *
	 * @access protected
	 */
	protected function checkDbConnection() {
		if(!$this->checkStatus())
			$this->connectDB();
	}

	/**
	 *    method execute given/parsed sql queries.
	 *
	 * @access public
	 * @param string $query your mysql query
	 * @param array $params select statement binding params
	 * @param bool $runTimeCache [default:true]
	 * @return mixed
	 */
	public function execute($query, $params = '', $runTimeCache = true) {
		$result = $runTimeCache ?$this->isQueryCached($query) :false;
		
		if($result !== false)
			return $result;
		
		return $this->runQuery($query, $params);
	}

	/**
	 *    Get all the field information for given query.
	 *
	 * @access public
	 * @param object $result
	 * @param string $sql This is used to analyse which query gets some error and for intelligent cache.
	 * @param string $index_field (optional)
	 * @param bool $runTimeCache [default:true]
	 * @param mixed $default (optional)
	 * @return mixed
	 */
	public function returnQuery($result, $sql, $index_field = '', $runTimeCache = true, $default = null) {
		$info = $runTimeCache ?$this->isQueryCached($sql) :false;
		
		if($info !== false)
			return $info;
		
		if(!$this->checkStatus($result)) {
			$error = (!$result) ?"No <font color='#3eadd2'>execute</font> resource found in returnQuery()" :wordwrap($this->getLastErrorMessage(), 100, '<br/>');
			$error .= '<br/><br/>';
			$error .= '<font color="red">SQL</font> : <font color="blue">'.$sql.'</font>';
			
			throw new Zamp_Db_Exception($error, 21);
		}
		
		$info = [];
		
		$row = $this->fetch($result, 'name');
		
		if($row) {
			if($index_field || isset($row[$index_field])) {
				if(isset($default)) {
					$info[$row[$index_field]] = $default;
					
					while($row = $this->fetch($result, 'name'))
						$info[$row[$index_field]] = $default;
				}
				else {
					$info[$row[$index_field]] = $row;
					
					while($row = $this->fetch($result, 'name'))
						$info[$row[$index_field]] = $row;
				}
			}
			else {
				$i = 0;
				
				$info[$i++] = $row;
				
				while($row = $this->fetch($result, 'name'))
					$info[$i++] = $row;
			}
		}
		
		$this->freeResult($result);
		
		if($runTimeCache)
			$this->setQueryCache($sql, $info);
		
		return $info;
	}
	
	protected function _getNumberData($type, $table, $field, $cond = '') {
		$sql = "SELECT $type($field) as result FROM $table $cond;";
		$result = $this->isQueryCached($sql);
		
		if($result !== false)
			return $result;
		
		$query = $this->execute($sql);
		
		if($row = $this->fetch($query, 'name'))
			$result = $row["result"];
		
		$this->freeResult($query);
		
		$this->setQueryCache($sql, $result);
		
		return $result;
	}

	/**
	 *    Get the minimum value for given table field
	 *
	 * @access public
	 * @param string $table Table Name
	 * @param string $field Field Name
	 * @param string $cond condition (optional)
	 * @return integer
	 */
	public function getMinimum($table, $field, $cond = '') {
		return $this->_getNumberData('MIN', $table, $field, $cond);
	}

	/**
	 *    Get the maximum value for given table field
	 *
	 * @access public
	 * @param string $table Table Name
	 * @param string $field Field Name
	 * @param string $cond condition (optional)
	 * @return integer
	 */
	public function getMaximum($table, $field, $cond = '') {
		return $this->_getNumberData('MAX', $table, $field, $cond);
	}

	/**
	 *    Count total number of records for given query
	 *
	 * @access public
	 * @param string $table Table Name
	 * @param string $field Field Name (optional)
	 * @param string $cond condition (optional)
	 * @return integer
	 */
	public function getCount($table, $field = '', $cond = '') {
		$field = ($field) ?$field :'*';
		
		return $this->_getNumberData('COUNT', $table, $field, $cond);
	}

	/**
	 *    Get Table fields for given table name.
	 *
	 * @access public
	 * @param string $table
	 * @return array
	 */
	public function getTableFields($table) {
		if(isset($this->_db_cache['fields'][$table]))
			return $this->_db_cache['fields'][$table];
		
		$fields = $this->getTableFieldsInfo($table);
		$this->_db_cache['fields'][$table] = $fields;
		
		file_put_contents($this->db_info['connection_cache_file'], "<?php\n\n\$dbCache = ".var_export($this->_db_cache, true).";\n\n", LOCK_EX);
		
        Zamp_General::invalidate($this->db_info['connection_cache_file']);
        
		return $fields;
	}

	/**
	 *    Get List of Tables which is present in given database or
	 *    check a table if exists
	 *
	 * @access public
	 * @param string $tableExist table name to check
	 * @return array|bool
	 */
	public function getDbTables($tableExist = '') {
		if(isset($this->_db_cache['tables']))
			return $tableExist ?isset($this->_db_cache['tables'][$tableExist]) :$this->_db_cache['tables'];
		
		$sql = 'SHOW TABLES FROM '.$this->db_info['dbname'].';';
		$result = $this->execute($sql);
		
		while($row = $this->fetch($result, 'number')) {
			$temp = "t_".preg_replace('/^'.$this->db_info['table_prefix'].'/', '', $row[0], 1, $total);
			
			if($this->db_info['table_prefix'] && $total == 0)
				continue;
			
			$this->_db_cache['tables'][$temp] = $row[0];
		}
		
		return $tableExist ?isset($this->_db_cache['tables'][$tableExist]) :$this->_db_cache['tables'];
	}

	/**
	 *    prepare sql queries
	 *
	 * @access public
	 * @param string $sql
	 * @param string|array $params
	 * @return string
	 */
	public function prepareSql($sql, $params = '') {
		if(!$params)
			return $sql;
		
		$params = $this->blockSqlInjection($params);
		
		if((array) $params === $params) {
			$placeHolder = array_fill(0, count($params), '/\?/');

			return preg_replace($placeHolder, $params, $sql, 1);
		}
		
		return str_replace('?', $params, $sql);
	}

	/**
	 *    Check the given field exists or not for given table.
	 *
	 * @access public
	 * @param string $table_name In which table you want check
	 * @param string $field_name In which field you want to Check against the given table.
	 * @return bool
	 */
	public function checkTableField($table_name, $field_name) {
		$fields = $this->getTableFields($table_name);
		
		return isset($fields[$field_name]) ?true :false;
	}

	/**
	 *    Insert Record to the given table.
	 *
	 * @access public
	 * @param array $data
	 * @param string $table
	 * @param string $identifier
	 * @return string If successfull then return 'ok' otherwise returns the error message
	 */
	public function insertRecord($data, $table, $identifier = "") {
		if((array) $data !== $data)
			throw new Zamp_Db_Exception("Input must be Array in insertRecord function!", 21);
		
		$fields = $this->getTableFields($table);
		
		$fieldNames = $fieldValues = [];
		
		if($identifier)
			$identifier = preg_quote($identifier, "/");
		
		foreach($data as $key => $value) {
			$key = trim($key);
			
			if($identifier) {
				if(!preg_match("/^(.*?)".$identifier."$/", $key, $match))
					continue;
				
				$key = $match[1];
			}
			
			if(isset($fields[$key])) {
				$fieldNames[] = $key;
				$fieldValues[] = $this->_detectDataType($value, $fields[$key]);
			}
			else
				throw new Zamp_Db_Exception("Field (`$key`) not found in Table (`$table`)", 21);
		}
		
		if(!$fieldNames)
			return false;
		
		$fieldNames = "`".implode("`, `", $fieldNames)."`";
		$fieldValues = implode(", ", $fieldValues);
		
		$sql = "INSERT INTO `$table` ($fieldNames) VALUES ($fieldValues);";
		
		return ($this->runQuery($sql)) ?"ok" :$this->getLastErrorMessage();
	}

	/**
	 *    Update the Record to the given table
	 *
	 * @access public
	 * @param array $data
	 * @param string $table
	 * @param string $condition Enter the update condition.
	 * @param string $identifier
	 * @return string If successfull then return 'ok' otherwise returns the error message
	 */
	public function updateRecord($data, $table, $condition, $identifier = "") {
		if((array) $data !== $data)
			throw new Zamp_Db_Exception("Input must be Array in updateRecord function!", 21);
		
		$fields = $this->getTableFields($table);
		
		$updates = "";
		
		if($identifier)
			$identifier = preg_quote($identifier, "/");
		
		foreach($data as $key => $value) {
			$key = trim($key);
			
			if($identifier) {
				if(!preg_match("/^(.*?)".$identifier."$/", $key, $match))
					continue;
				
				$key = $match[1];
			}
			
			if(isset($fields[$key]))
				$updates .= "`$key` = ".$this->_detectDataType($value, $fields[$key]).", ";
			else
				throw new Zamp_Db_Exception("Field (`$key`) not found in Table (`$table`)", 21);
		}
		
		if(!$updates)
			return false;
		
		$updates = rtrim($updates, " ,");
		
		$sql = "UPDATE `$table` SET $updates $condition;";
		
		return ($this->runQuery($sql)) ?"ok" :$this->getLastErrorMessage();
	}

	/**
	 *    detect datatype like NULL values
	 *
	 * @access private
	 * @ignore
	 * @since v1.4.8
	 * @return mixed
	 */
	private function _detectDataType($value, $fieldInfo) {
		if(isset($fieldInfo['BINARY_FLAG']))
			return empty($value) ?"''" :'0x'.bin2hex($value);
		elseif(strtoupper($value) == 'NULL')
			return $value;
		else
			return "'".$this->blockSqlInjection($value)."'";
	}

	/**
	 *    This method used to Optimize the mysql table
	 *
	 * @access public
	 * @return bool
	 */
	public function optimizeTable() {
		$tables = func_get_args();
		
		if(!$tables)
			return false;
		
		$tables = '`'.implode('`, `', $tables).'`';
		
		return ($this->execute("OPTIMIZE TABLE $tables;")) ?true :false;
	}

	/**
	 *    Begin transaction
	 *
	 * @access public
	 * @return boolean
	 */
	public function beginWork() {
		if($this->_transaction_level == 0) {
			if(!$this->execute('BEGIN WORK;'))
				return false;
		}

		$this->_transaction_level++;

		return true;
	}

	/**
	 *    Commit transaction
	 *
	 * @access public
	 * @return boolean
	 */
	public function commit() {
		if($this->_transaction_level) {
			$this->_transaction_level--;

			if($this->_transaction_level == 0) {
				if(!$this->execute('COMMIT;'))
					return false;
			}
		}

		return true;
	}

	/**
	 *    Rollback transaction
	 *
	 * @access public
	 * @return boolean
	 */
	public function rollback() {
		if($this->_transaction_level) {
			$this->_transaction_level = 0;

			if(!$this->execute('ROLLBACK;'))
				return false;
		}

		return true;
	}

	/**
	 *    Set query ID
	 *
	 * @access public
	 * @since v4.8.3
	 * @return object
	 */
	public function queryId($queryId) {
		$this->queryId = $queryId;

		return $this;
	}

	/**
	 *    Method used to set the runned sql queries to array to avoid repeat the same query
	 *
	 * @access protected
	 * @param string $sql_query
	 * @param mixed $result
	 * @return void
	 */
	protected function setQueryCache($sql_query, $result) {
		$this->_sql_runtime_queries[md5($sql_query)] = $result;
	}

	/**
	 *    Method checks and return given query result if it is already executed
	 *
	 * @access protected
	 * @param string $sql_query
	 * @return bool|mixed
	 */
	protected function isQueryCached($sql_query) {
		$hash = md5($sql_query);
		
		if(isset($this->_sql_runtime_queries[$hash]))
			return $this->_sql_runtime_queries[$hash];
		
		return false;
	}

	/**
	 *    Connect to the Database
	 *
	 * @abstract
	 * @access protected
	 */
	abstract protected function connectDB();

	/**
	 *    Close the Database Connection
	 *
	 * @abstract
	 * @access public
	 * @return bool
	 */
	abstract protected function closeDB();

	/**
	 *    checking the query or connection status
	 *
	 * @abstract
	 * @access protected
	 * @param string $query
	 * @return bool
	 */
	abstract protected function checkStatus($query = '');

	/**
	 *    database query fetch methods
	 *
	 * @abstract
	 * @access public
	 * @param resource|object $result
	 * @param string $type
	 * @return mixed
	 */
	abstract public function fetch($result, $type = '');

	/**
	 *    database query method
	 *
	 * @abstract
	 * @access public
	 * @param string $sql
	 * @param string|array $params
	 * @return resource|object
	 */
	abstract public function runQuery($sql, $params);

	/**
	 *    get the last error message
	 *
	 * @abstract
	 * @access public
	 * @return string
	 */
	abstract public function getLastErrorMessage();

	/**
	 *    get the last error code
	 *
	 * @abstract
	 * @access public
	 * @return integer
	 */
	abstract public function getLastErrorCode();

	/**
	 *    free query result
	 *
	 * @abstract
	 * @access protected
	 * @param resource|object $result
	 */
	abstract protected function freeResult($result);

	/**
	 *    This method used to get the number rows affected in last insert, delete, update
	 *
	 * @abstract
	 * @access public
	 * @return integer
	 */
	abstract public function getAffectedRows();

	/**
	 *    method returns last insert id
	 *
	 * @abstract
	 * @access public
	 * @return integer
	 */
	abstract public function getLastInsertId();

	/**
	 *    Block SQL Injection Attact
	 *
	 * @abstract
	 * @access public
	 * @param array|string $data
	 * @param array $exceptions list of array keys to jump. used for description like fields [optional]
	 * @return array|string
	 */
	abstract public function blockSqlInjection($data, $exceptions = []);

	/**
	 *    get table fields informations
	 *
	 * @access protected
	 * @param string $tableName
	 * @return array
	 */
	abstract protected function getTableFieldsInfo($tableName);
}
/** End of File **/
