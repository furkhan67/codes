<?php
/**
 *    This file is part of the Zamp PHP Framework.
 *    (c) Mathan kumar R <phpmathan@gmail.com>
 *
 *    For the full copyright and license information, please view the LICENSE
 *    file that was distributed with this source code.
 *
 * @package Zamp_Cache_Abstract
 */

/**
 *    Redis Cache class
 *
 * @category Zamp
 * @package Zamp_Cache_Abstract
 * @subpackage Zamp_Cache_Redis
 * @author Mathan kumar R <phpmathan@gmail.com>
 * @version SVN: $Id: Redis.php 367 2016-02-22 09:48:41Z phpmathan $
 */
class Zamp_Cache_Redis extends Zamp_Cache_Abstract {
	/**
	 * @access public
	 * @var object $_redisCache
	 */
	public $_redisCache;
	
	/**
	 *    construct method
	 *
	 * @access public
	 * @param array $cache_config
	 */
	public function __construct($cache_config) {
		if(!extension_loaded('Redis'))
			throw new Zamp_Cache_Exception('Could not Initialize Redis Cache', 26);
		
		if(empty($cache_config['host']))
			$cache_config['cache_config'] = '127.0.0.1';
		
		if(empty($cache_config['port']))
			$cache_config['port'] = '6379';
		
		if(!isset($cache_config['timeout']))
			$cache_config['timeout'] = 0;
		
		$this->_redisCache = new \Redis();
		
		if(!$this->_redisCache->connect($cache_config['host'], $cache_config['port'], $cache_config['timeout']))
			throw new Zamp_Cache_Exception('Redis connection failed.');
		
		if(isset($cache_config['password']) && !$this->_redisCache->auth($cache_config['password']))
			throw new Zamp_Cache_Exception('Redis authentication failed.');
		
		if(isset($cache_config['database']))
			$this->_redisCache->select((int) $cache_config['database']);
		
		parent::__construct($cache_config);
	}

	/**
	 *    fetch the cache data for given identifier. If cache not set the returns boolean false.
	 *
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function fetch($key) {
		list($parent) = $this->getKeyValue($key);
		
		$data = $this->_redisCache->get($parent);
		
		if(($data = @unserialize($data)) === false)
			$data = [];
		
		return $this->getCache($key, $data);
	}

	/**
	 *    store the data into the Cache
	 *
	 * @access public
	 * @param string $key identifier name
	 * @param mixed $data
	 * @param integer $ttl Cache life time. If not given it taken from the application configuration. [default: 3600]
	 * @return bool|string
	 */
	public function store($key, $data, $ttl = null) {
		$ttl = (int) (isset($ttl) ?$ttl :$this->_cache_config['duration']);

		list($key, $data) = $this->getKeyValue($key, $data);

		return $this->_redisCache->setEx($key, $ttl, serialize($data));
	}

	/**
	 *    delete the Cache for given identifier
	 *
	 * @access protected
	 * @since 1.4.7
	 * @param string $key
	 * @return bool|string
	 */
	protected function deleteCache($key) {
		return $this->_redisCache->del($key);
	}

	/**
	 *    clear all the Cache data
	 *
	 * @access public
	 * @return bool|string
	 */
	public function clear() {
		return $this->_redisCache->flushDB();
	}
}
/** End of File **/