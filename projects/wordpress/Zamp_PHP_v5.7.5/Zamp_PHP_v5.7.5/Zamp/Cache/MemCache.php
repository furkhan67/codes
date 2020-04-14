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
 *    MemCache Cache class
 *
 * @category Zamp
 * @package Zamp_Cache_Abstract
 * @subpackage Zamp_Cache_MemCache
 * @author Mathan kumar R <phpmathan@gmail.com>
 * @version SVN: $Id: MemCache.php 376 2017-11-24 09:17:20Z phpmathan $
 */
class Zamp_Cache_MemCache extends Zamp_Cache_Abstract {
	/**
	 * @access public
	 * @var object $_memCache
	 */
	public $_memCache;

	/**
	 *    construct method
	 *
	 * @access public
	 * @param array $cache_config
	 */
	public function __construct($cache_config) {
		if(!class_exists('MemCache', false))
			throw new Zamp_Cache_Exception('Could not Initialize MemCache', 26);
		
		if(!isset($cache_config['servers']) || (array) $cache_config['servers'] !== $cache_config['servers'])
			throw new Zamp_Cache_Exception('Enter MemCache Server');
		
		$this->_memCache = Zamp::getInstance('MemCache');
		
		$servers_added = 0;
		
		foreach($cache_config['servers'] as $server) {
			$server = explode(':', $server);
			
			if(isset($server[1])) {
				$this->addServer($server[0], $server[1]);
				$servers_added++;
			}
		}
		
		if($servers_added == 0)
			throw new Zamp_Cache_Exception('Enter atleast one MemCache Server');
		elseif(!$this->_memCache->getversion())
			throw new Zamp_Cache_Exception('Is your MemCache server running ?');
		
		parent::__construct($cache_config);
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
		$ttl = isset($ttl) ?$ttl :$this->_cache_config['duration'];
		$ttl = (int) $ttl;
		
		list($key, $data) = $this->getKeyValue($key, $data);

		return $this->_memCache->set($key, $data, 0, $ttl);
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
		
		return $this->getCache($key, $this->_memCache->get($parent));
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
		return $this->_memCache->delete($key);
	}

	/**
	 *    add MemCache server
	 *
	 * @access public
	 * @param string $host
	 * @param integer $port [default: 11211]
	 * @param integer $weight [default: 10]
	 */
	public function addServer($host, $port = 11211, $weight = 10) {
		$this->_memCache->addServer($host, $port, true, $weight);
	}

	/**
	 *    clear all the Cache data
	 *
	 * @access public
	 * @return bool|string
	 */
	public function clear() {
		return $this->_memCache->flush();
	}
}
/** End of File **/