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
 *    Cache abstract class
 *
 * @abstract
 * @category Zamp
 * @package Zamp_Cache_Abstract
 * @author Mathan kumar R <phpmathan@gmail.com>
 * @version SVN: $Id: Abstract.php 379 2018-05-20 04:19:14Z phpmathan $
 */
abstract class Zamp_Cache_Abstract {
	protected $cache_config;
	/**
	 *    construct method
	 *
	 * @access public
	 * @param array $cache_config
	 */
	protected function __construct($cache_config) {
		if($cache_config)
			$this->_cache_config = $cache_config;
		else
			$this->_cache_config = [];
		
		if(!isset($this->_cache_config['prefix']))
			$this->_cache_config['prefix'] = '';
		
		if(empty($this->_cache_config['duration']))
			$this->_cache_config['duration'] = 3600;
	}

	/**
	 *    method return update cache key and value
	 *
	 * @access protected
	 * @since 1.4.7
	 * @param string $key
	 * @param mixed $value
	 * @return array
	 */
	protected function getKeyValue($key, $value = null) {
		$cacheNamespace = explode('/', trim($key, '/ '));
		
		$info[0] = $this->_cache_config['prefix'].array_shift($cacheNamespace);
		
		if($value !== null) {
			$info[1] = Zamp_General::setMultiArrayValue($cacheNamespace, $value);
			
			$previousData = $this->fetch($info[0]);
			
			if(isset($info[1]) && (array) $info[1] === $info[1] && (array) $previousData === $previousData)
				$info[1] = Zamp_General::arrayMergeRecursiveDistinct($previousData, $info[1]);
		}
		
		return $info;
	}

	/**
	 *    delete the cache
	 *
	 * @access public
	 * @param string $key
	 * @return bool|string
	 */
	public function delete($key) {
		$cacheNamespace = explode('/', trim($key, '/ '));
		$cacheName = array_shift($cacheNamespace);
		
		$parent = $this->_cache_config['prefix'].$cacheName;
		
		if(!isset($cacheNamespace[0]))
			return $this->deleteCache($parent);
		
		$data = $this->fetch($parent);
		
		Zamp_General::unsetMultiArrayValue($cacheNamespace, $data);
		
		$this->deleteCache($parent);

		return $this->store($cacheName, $data);
	}

	/**
	 *    get the cache with namespace condition
	 *
	 * @access protected
	 * @since 1.4.7
	 * @param string $key
	 * @param mixed $data
	 * @return mixed
	 */
	protected function getCache($key, $data) {
		$key = explode('/', trim($key, '/ '));
		array_shift($key);
		
		foreach($key as $k) {
			if(isset($data[$k]))
				$data = $data[$k];
			else {
				$data = false;
				break;
			}
		}
		
		return $data;
	}

	/**
	 *    fetch the cache data for given identifier. If cache not set the returns boolean false.
	 *
	 * @abstract
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	abstract public function fetch($key);

	/**
	 *    store the data into the Cache
	 *
	 * @abstract
	 * @access public
	 * @param string $key identifier name
	 * @param mixed $data
	 * @param integer $ttl Cache life time. If not given it taken from the application configuration. [default: 3600]
	 * @return bool|string
	 */
	abstract public function store($key, $data, $ttl = null);

	/**
	 *    delete the Cache for given identifier
	 *
	 * @abstract
	 * @access protected
	 * @param string $key
	 * @return bool|string
	 */
	abstract protected function deleteCache($key);

	/**
	 *    clear all the Cache data
	 *
	 * @abstract
	 * @access public
	 * @return bool|string
	 */
	abstract public function clear();
}
/** End of File **/
